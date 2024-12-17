<?php

namespace App\Services\Media;

use App\Enums\Constant;
use App\Http\Resources\FileResource;
use App\Repositories\File\FileInterface;
use App\Repositories\Folder\FolderInterface;
use App\Helpers\CommonHelper;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaService
{

    /**
     * @var FileInterface
     */
    protected $fileRepository;

    /**
     * @var FolderInterface
     */
    protected $folderRepository;

    /**
     * @var UploadService
     */
    protected $uploadSevice;


    public function __construct(
        FileInterface   $fileRepository,
        FolderInterface $folderRepository,
        UploadService   $uploadService
    )
    {
        $this->fileRepository = $fileRepository;
        $this->folderRepository = $folderRepository;
        $this->uploadSevice = $uploadService;
    }

    /**
     * @param UploadedFile|null $fileUpload
     * @param bool $isFileLibrary
     * @param int|null $folderId
     * @param string|null $folderSlug
     * @param bool $skipValidation
     * @return array
     */
    public function handleUploadFile(?UploadedFile $fileUpload, bool $isFileLibrary = true, ?int $folderId = 0, ?string $folderSlug = null, bool $skipValidation = false): array
    {
        $request = request();
        if (!$fileUpload) {
            return [
                'error' => true,
                'status' => Constant::BAD_REQUEST_CODE,
                'message' => trans('messages.errors.media.not_exist_file'),
            ];
        }

        $allowedMimeTypes = config('media.allowed_mime_types');

        try {
            $file = $this->fileRepository->getModel();

            $fileExtension = $fileUpload->getClientOriginalExtension();

            if (!$skipValidation) {
                $validator = Validator::make(['uploaded_file' => $fileUpload], [
                    'uploaded_file' => 'required',
                ]);
                if ($validator->fails()) {
                    return [
                        'error' => true,
                        'status' => Constant::BAD_REQUEST_CODE,
                        'message' => trans('messages.errors.' . $validator->getMessageBag()->first()),
                    ];
                }
            }

            $maxSize = $this->maxSizeFile();
            if ($fileUpload->getSize() > (int)$maxSize) {
                return [
                    'error' => true,
                    'message' => trans('messages.errors.media.file_too_big', ['size' => CommonHelper::byteConvert($maxSize)]),
                ];
            }

            if (!$skipValidation && !in_array(strtolower($fileExtension), explode(',', $allowedMimeTypes))) {
                return [
                    'error' => true,
                    'message' => trans('messages.errors.media.can_not_identify_file_type', ['values' => $allowedMimeTypes]),
                ];
            }
            if ($folderId == 0 && !empty($folderSlug) && $isFileLibrary) {
                $folder = $this->folderRepository->getFirstBy(['slug' => $folderSlug]);

                if (!$folder) {
                    $folder = $this->folderRepository->createOrUpdate([
                        'name' => $this->folderRepository->createName($folderSlug, 0),
                        'slug' => $this->folderRepository->createSlug($folderSlug, 0),
                        'parent_id' => 0,
                    ]);
                }

                $folderId = $folder->id;
            }

            $file->name = $this->fileRepository->createName(
                pathinfo($fileUpload->getClientOriginalName(), PATHINFO_FILENAME),
                $folderId
            );

            $prefixPath = $isFileLibrary ? Constant::PATH_LIBRARY : Constant::PATH_UPLOAD;

            if (!$isFileLibrary && !empty($folderSlug)) {
                $prefixPath =  $prefixPath . '/' . $folderSlug;
            }

            $folderPath = $prefixPath . '/' . $this->folderRepository->getFullPath($folderId);

            $fileName = $this->fileRepository->createSlug(
                $file->name,
                $fileExtension,
                Storage::path($folderPath ?: '')
            );

            $filePath = $fileName;
            $filePath = $folderPath . '/' . $filePath;
            $filePath = str_replace('//', '/', $filePath);
            $content = file_get_contents($fileUpload);
            $this->saveFile($filePath, $content);
            $data = $this->fileDetails($filePath);

            if (!$skipValidation && empty($data['mime_type'])) {
                return [
                    'error' => true,
                    'message' => trans('messages.errors.media.can_not_identify_file_type'),
                ];
            }

            if (!$isFileLibrary) {
                return $data;
            }

            $file->url = $data['url'];
            $file->size = $data['size'];
            $file->mime_type = $data['mime_type'];
            $file->folder_id = $folderId;
            $file->user_id = $request->user_id ?? 0;
            $file = $this->fileRepository->createOrUpdate($file);
            return [
                'error' => false,
                'data' => new FileResource($file)
            ];
        } catch (\Exception $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }
    }

    /**
     * @return float
     */
    public function getServerConfigMaxUploadFileSize(): float
    {
        $maxSize = CommonHelper::parseSize(ini_get('post_max_size'));

        $uploadMax = CommonHelper::parseSize(ini_get('upload_max_filesize'));
        if ($uploadMax > 0 && $uploadMax < $maxSize) {
            $maxSize = $uploadMax;
        }

        return $maxSize;
    }

    /**
     * @return mixed
     */
    public function maxSizeFile()
    {
        return config('media.post_max_size');
    }

    /**
     * @param string $path
     * @param string $content
     * @return bool
     */
    public function saveFile(string $path, string $content): bool
    {
        try {
            return Storage::put($this->cleanFolder($path), $content);
        } catch (Exception $e) {
            return Storage::put($this->cleanFolder($path), $content, ['visibility' => 'public']);
        }
    }

    /**
     * @param string $folder
     * @return string
     */
    protected function cleanFolder(string $folder): string
    {
        return DIRECTORY_SEPARATOR . trim(str_replace('..', '', $folder), DIRECTORY_SEPARATOR);
    }

    /**
     * Return an array of file details for a file
     * @param string $path
     * @return array
     */
    public function fileDetails(string $path): array
    {
        return [
            'filename' => basename($path),
            'url' => $this->cleanPathFolder($path),
            'mime_type' => $this->uploadSevice->fileMimeType($path),
            'size' => $this->uploadSevice->fileSize($path),
            'modified' => $this->uploadSevice->fileModified($path),
        ];
    }

    /**
     * @param $path
     * @return string
     */
    public function cleanPathFolder($path): string
    {
        $prefixFolder = [Constant::PATH_LIBRARY . '/', Constant::PATH_UPLOAD . '/'];
        return str_replace($prefixFolder, '', $path);
    }

    /**
     * @param string|null $path
     * @param string $prefixFolder
     * @return bool
     */
    public function deleteFile(?string $path, string $prefixFolder = Constant::PATH_LIBRARY): bool
    {
        $newPath = $prefixFolder . '/' . $path;
        if (!empty($path) && Storage::exists($newPath)) {
            Storage::delete($newPath);
        }

        return false;
    }

}
