<?php

namespace App\Helpers;

use App\Enums\Constant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class CommonHelper
{

    /**
     * @param $bytes
     * @return string
     */
    public static function byteConvert($bytes): string
    {
        if ($bytes == 0)
            return "0.00 B";

        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $pow = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $pow), 2) . $units[$pow];
    }

    /**
     * @param int|string $size
     * @return float - bytes
     */
    public static function parseSize($size): float
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }

        return round($size);
    }

    /**
     * @return string
     */
    public static function getLanguage(): string
    {
        return app()->getLocale() ?? 'vi';
    }

    /**
     * @param string|null $path
     * @param string $typePath
     * @return string
     */
    public static function getUrlFile(?string $path, string $typePath = Constant::PATH_LIBRARY): string
    {
        $path = trim($path);

        if (Str::contains($path, 'https://') || Str::contains($path, 'http://')) {
            return $path;
        }

        return Storage::url($typePath . '/' . $path);
    }

    /**
     * @param string $directory
     * @return void
     */
    public static function autoload(string $directory): void
    {
        $helpers = File::glob($directory . '/*.php');
        foreach ($helpers as $helper) {
            File::requireOnce($helper);
        }
    }

    public static function makeCurlRequest($url, $method, $data, array $headers = []): bool|string
    {
        try {
            $ch = curl_init();

            if ($method == 'GET' && !empty($data)) {
                $url .= '?' . http_build_query($data);
            } elseif ($method == 'POST') {
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $data,
                ]);
            }

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new Exception(curl_error($ch));
            }

            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $statusCode === Constant::SUCCESS_CODE ? $response : false;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
    public static function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
}
