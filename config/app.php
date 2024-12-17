<?php

use Illuminate\Support\Facades\Facade;

return [

    'name' => env('APP_NAME', 'Laravel'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool)env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    'timezone' => 'Asia/Ho_Chi_Minh',

    'time_otp' => 3,

    'locale' => 'vi',

    'fallback_locale' => 'vi',

    'faker_locale' => 'en_US',

    'key' => env('APP_KEY'),

    'tc_api_key' => env('TC_API_KEY') ,

    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    'providers' => [
        App\Providers\FCM\FcmServiceProvider::class,

        App\Providers\Contact\ContactServiceProvider::class,

        App\Providers\Notification\NotificationServiceProvider::class,

        App\Providers\Rating\RatingServiceProvider::class,

        App\Providers\Payment\PaymentServiceProvider::class,

        App\Providers\Info\InfoServiceProvider::class,

        App\Providers\Ticket\TicketServiceProvider::class,

        App\Providers\Trip\TripServiceProvider::class,

        App\Providers\Promotion\PromotionServiceProvider::class,

        App\Providers\Website\WebsiteServiceProvider::class,


        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
//        Jenssegers\Mongodb\MongodbServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,

        Intervention\Image\ImageServiceProvider::class

    ],

    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
        'LRedis' => 'Illuminate\Support\Facades\Redis',
        'Image' => Intervention\Image\Facades\Image::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,

    ])->toArray(),

];
