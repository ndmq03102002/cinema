<?php

namespace App\Providers\FCM;

use App\Repositories\FCM\FcmInterface;
use App\Repositories\FCM\FcmRepository;
use Illuminate\Support\ServiceProvider;

class FcmServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FcmInterface::class, FcmRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
