<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


{
    if (!function_exists('makeCurlRequest')) {

        function makeCurlRequest($url, $method, $data, array $headers = [])
        {
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

            curl_close($ch);

            return json_decode($response);
        }
    }
}

if (!function_exists('getSecretKey')) {

    /**
     * @return string
     */
    function getSecretKey(): string
    {
        return md5(date("Y-m-d") . "7519d6d392f9c07c6ca3126a48b39039");
    }
}

if (!function_exists('generateOrderCode')) {

    /**
     * @param $ticket_id
     * @return string
     */
    function generateOrderCode($ticket_id): string
    {
        return 'TCB'.date('Ymd') . $ticket_id . mt_rand(10000000, 99999999);
    }
}

if (!function_exists('generateRequestId')) {

    /**
     * @param $ticket_id
     * @return string
     */
    function generateRequestId($ticket_id): string
    {
        return 'TCB' . $ticket_id . date('Ymd') . uniqid();
    }
}

if (!function_exists('convertStringToDateTime')) {
    function convertStringToDateTime($string): string
    {
        $dateTime = strtotime($string);
        return date('Y-m-d', $dateTime);
    }
}
