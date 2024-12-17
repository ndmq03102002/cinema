<?php

namespace App\Http\Traits;

use App\Models\MerchandiseImages;
use App\Models\MessageImages;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Maestroerror\HeicToJpg;
use Intervention\Image\Imagick;

trait FileUploadTrait
{
    /**
     * store path
     * @param object $req
     * @param string $type
     * @return string $path
     * @author Nampx
     */
    public function getFilePath(object $req, string $type)
    {
        $isImage = $this->checkValidate($req);
        if ($isImage) {
            // lấy filename
            $imageName = Str::random(16) . '.' . $req->getClientOriginalExtension();
            // lấy đường dẫn
            $filePath = $type;
            // size ảnh
            $imgSize = [800,600];

            $thumbnailTrait = new ThumbnailTrait();

            $file = $thumbnailTrait
                ->setImage($req)
                ->setSize($imgSize[0], $imgSize[1])
                ->setDestinationPath($type)
                ->setFileName($imageName)
                ->save();

            return $type . '/' . $imageName;
        }

        return false;
    }

    /**
     * validate image
     * @param object $req
     * @return boolean $isImage
     * @author Nampx
     */
    public function checkValidate(object $req)
    {
//        $rules = array('jpeg','jpg','png','gif', 'svg');
//        $typeOfFile = $req->extension();

        $sizeOfFile = number_format($req->getSize() / 10485760,2);

        return $sizeOfFile<10;
//        return (in_array($typeOfFile, $rules) && $sizeOfFile<10);
    }

    /**
     * del img
     * @param string $type
     * @author Nampx
     */
    public function deleteFile($deleteImageId, string $type)
    {
        switch ($type) {
            case 'merchandises':
                $model = new MerchandiseImages();
                break;
            case 'message':
                $model = new MessageImages();
                break;
            default:
                Log::debug('Thể loại ảnh xóa: ' . $type);

                return false;
        }

        foreach ($deleteImageId as $key => $delId) {
            if (is_object($delId)) {
                $delId = $delId->id;
            }
            $file = $model->where('id', $delId)->first();
            if (isset($file)) {
                Storage::delete('public/' . $file->image_data);
                $deleteFile = $file->delete();

                Log::debug('Xóa file: ' . $key . ' ' . $deleteFile);
            }
        }
    }
}
