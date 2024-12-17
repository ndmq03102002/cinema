<?php

namespace App\Providers\Notification;
use App\Repositories\RepositoryAbstract;

use App\Repositories\Notification\NotificationInterface;
use App\Repositories\Notification\NotificationRepository;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(NotificationInterface::class, NotificationRepository::class);
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
