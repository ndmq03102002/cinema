<?php

namespace App\Facades;

use App\Services\Media\MediaService;
use Illuminate\Support\Facades\Facade;

class MediaServiceFacade extends Facade
{


    protected static function getFacadeAccessor(): string
    {
        return MediaService::class;
    }
}
