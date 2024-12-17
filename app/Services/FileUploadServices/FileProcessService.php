<?php

namespace App\Services\FileUploadServices;

use Exception;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

class FileProcessService
{

    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @var string
     */
    protected $imagePath;

    /**
     * @var float
     */
    protected $thumbRate;

    /**
     * @var int
     */
    protected $thumbWidth;

    /**
     * @var int
     */
    protected $thumbHeight;

    /**
     * @var string
     */
    protected $destinationPath;

    /**
     * @var string
     */
    protected $xCoordinate;

    /**
     * @var string
     */
    protected $yCoordinate;

    /**
     * @var string
     */
    protected $fitPosition;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var FileUploadService
     */
    protected $uploadManager;

    /**
     * FileProcessService constructor.
     * @author NamPx
     */
    public function __construct()
    {
        if (extension_loaded('imagick')) {
            $this->imageManager = new ImageManager([
                'driver' => 'imagick',
            ]);
        } else {
            $this->imageManager = new ImageManager([
                'driver' => 'gd',
            ]);
        }

        $this->thumbRate = 0.75;
        $this->xCoordinate = null;
        $this->yCoordinate = null;
        $this->fitPosition = 'center';
        $this->uploadManager = new FileUploadService();
    }

    /**
     * @param string $imagePath
     * @return FileProcessService
     * @author NamPx
     */
    public function setImage(string $imagePath): FileProcessService
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * @return string $imagePath
     * @author NamPx
     */
    public function getImage(): string
    {
        return $this->imagePath;
    }

    /**
     * @param float $rate
     * @return FileProcessService
     * @author NamPx
     */
    public function setRate(float $rate): FileProcessService
    {
        $this->thumbRate = $rate;

        return $this;
    }

    /**
     * @return float
     * @author NamPx
     */
    public function getRate(): float
    {
        return $this->thumbRate;
    }

    /**
     * @param $width
     * @param null $height
     * @return FileProcessService
     * @author NamPx
     */
    public function setSize($width, $height = null): FileProcessService
    {
        $this->thumbWidth = $width;
        $this->thumbHeight = $height;

        if (empty($height)) {
            $this->thumbHeight = ($this->thumbWidth * $this->thumbRate);
        }

        return $this;
    }

    /**
     * @return array
     * @author NamPx
     */
    public function getSize(): array
    {
        return [$this->thumbWidth, $this->thumbHeight];
    }

    /**
     * @param string $destinationPath
     * @return FileProcessService
     * @author NamPx
     */
    public function setDestinationPath(string $destinationPath): FileProcessService
    {
        $this->destinationPath = $destinationPath;

        return $this;
    }

    /**
     * @return string $destinationPath
     * @author NamPx
     */
    public function getDestinationPath(): string
    {
        return $this->destinationPath;
    }

    /**
     * @param integer $xCoordination
     * @param integer $yCoordination
     * @return FileProcessService
     * @author NamPx
     */
    public function setCoordinates(int $xCoordination,int $yCoordination): FileProcessService
    {
        $this->xCoordinate = $xCoordination;
        $this->yCoordinate = $yCoordination;

        return $this;
    }

    /**
     * @return array
     * @author NamPx
     */
    public function getCoordinates(): array
    {
        return [$this->xCoordinate, $this->yCoordinate];
    }

    /**
     * @param string $position
     * @return FileProcessService
     * @author NamPx
     */
    public function setFitPosition(string $position): FileProcessService
    {
        $this->fitPosition = $position;

        return $this;
    }

    /**
     * @return string $fitPosition
     * @author NamPx
     */
    public function getFitPosition(): string
    {
        return $this->fitPosition;
    }

    /**
     * @param string $fileName
     * @return FileProcessService
     * @author NamPx
     */
    public function setFileName(string $fileName): FileProcessService
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string $fileName
     * @author NamPx
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $type
     * @return mixed
     * @author NamPx
     * @throws Exception
     */
    public function save(string $type = 'fit'): mixed
    {
        $fileName = pathinfo($this->imagePath, PATHINFO_BASENAME);

        if ($this->fileName) {
            $fileName = $this->fileName;
        }

        $destinationPath = sprintf('%s/%s', trim($this->destinationPath, '/'), $fileName);

        $thumbImage = $this->imageManager->make($this->imagePath);

        switch ($type) {
            case 'resize':
                $thumbImage->resize($this->thumbWidth, $this->thumbHeight);
                break;
            case 'crop':
                $thumbImage->crop($this->thumbWidth, $this->thumbHeight, $this->xCoordinate, $this->yCoordinate);
                break;
            case 'fit':
                $thumbImage->fit($this->thumbWidth, $this->thumbHeight, null, $this->fitPosition);
        }

        try {
            $this->uploadManager->saveFile($destinationPath, $thumbImage->stream()->__toString());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }

        return $destinationPath;
    }
}
