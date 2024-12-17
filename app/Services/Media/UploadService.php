<?php

namespace App\Services\Media;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class UploadService {

    /**
     * @param string $path
     * @return int
     */
    public function fileSize(string $path): int
    {
        return Storage::size($path);
    }

    /**
     * @param string $path
     * @return string|null
     */
    public function fileMimeType(string $path): ?string
    {
        return Storage::mimeType($path);
    }

    /**
     * @param string $path
     * @return string
     */
    public function fileModified(string $path): string
    {
        return Carbon::createFromTimestamp(Storage::lastModified($path));
    }

    /**
     * @param string $url
     * @return string
     */
    public function getRealPath(string $url): string
    {
        switch (config('filesystems.default')) {
            case 'local':
            case 'public':
                return Storage::path($url);
            default:
                return Storage::url($url);
        }
    }
}
