<?php


return [
    'shopee' => [
        'merchant_id' => env('MERCHANT_EXT_ID'),
        'store_id' => env('STORE_EXT_ID'),
        'secret' => env('SECRET'),
        'client_id' => env('CLIENT_ID'),
        'return_url_app' => env('RETURN_URL_APP'),
        'return_url_web' => env('RETURN_URL_WEB')
    ]
];
