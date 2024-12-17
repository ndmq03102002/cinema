<?php

namespace App\Providers\Website;
use App\Repositories\RepositoryAbstract;

use App\Repositories\Website\WebsiteInterface;
use App\Repositories\Website\WebsiteRepository;
use Illuminate\Support\ServiceProvider;

class WebsiteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WebsiteInterface::class, WebsiteRepository::class);
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
