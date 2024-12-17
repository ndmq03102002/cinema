<?php

namespace App\Http\Traits;

use Exception;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

class ThumbnailTrait
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
     * @var UploadsManager
     */
    protected $uploadManager;

    /**
     * ThumbnailTrait constructor.
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
        $this->uploadManager = new UploadsManagerTrait();
    }

    /**
     * @param string $imagePath
     * @return ThumbnailTrait
     * @author NamPx
     */
    public function setImage($imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * @return string $imagePath
     * @author NamPx
     */
    public function getImage()
    {
        return $this->imagePath;
    }

    /**
     * @param double $rate
     * @return ThumbnailTrait
     * @author NamPx
     */
    public function setRate($rate)
    {
        $this->thumbRate = $rate;

        return $this;
    }

    /**
     * @return double $thumbRate
     * @author NamPx
     */
    public function getRate()
    {
        return $this->thumbRate;
    }

    /**
     * @param $width
     * @param null $height
     * @return ThumbnailTrait
     * @author NamPx
     */
    public function setSize($width, $height = null)
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
    public function getSize()
    {
        return [$this->thumbWidth, $this->thumbHeight];
    }

    /**
     * @param string $destinationPath
     * @return ThumbnailTrait
     * @author NamPx
     */
    public function setDestinationPath($destinationPath)
    {
        $this->destinationPath = $destinationPath;

        return $this;
    }

    /**
     * @return string $destinationPath
     * @author NamPx
     */
    public function getDestinationPath()
    {
        return $this->destinationPath;
    }

    /**
     * @param integer $xCoord
     * @param integer $yCoord
     * @return ThumbnailTrait
     * @author NamPx
     */
    public function setCoordinates($xCoordination, $yCoordination)
    {
        $this->xCoordinate = $xCoordination;
        $this->yCoordinate = $yCoordination;

        return $this;
    }

    /**
     * @return array
     * @author NamPx
     */
    public function getCoordinates()
    {
        return [$this->xCoordinate, $this->yCoordinate];
    }

    /**
     * @param string $position
     * @return ThumbnailTrait
     * @author NamPx
     */
    public function setFitPosition($position)
    {
        $this->fitPosition = $position;

        return $this;
    }

    /**
     * @return string $fitPosition
     * @author NamPx
     */
    public function getFitPosition()
    {
        return $this->fitPosition;
    }

    /**
     * @param string $fileName
     * @return ThumbnailTrait
     * @author NamPx
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string $fileName
     * @author NamPx
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $type
     * @return mixed
     * @author NamPx
     * @throws Exception
     */
    public function save($type = 'fit')
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
