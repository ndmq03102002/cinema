<?php

namespace App\Providers\Info;
use App\Repositories\RepositoryAbstract;

use App\Repositories\Info\InfoInterface;
use App\Repositories\Info\InfoRepository;
use Illuminate\Support\ServiceProvider;

class InfoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InfoInterface::class, InfoRepository::class);
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
