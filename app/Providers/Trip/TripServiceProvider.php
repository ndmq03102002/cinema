<?php

namespace App\Providers\Trip;
use App\Repositories\RepositoryAbstract;

use App\Repositories\Trip\TripInterface;
use App\Repositories\Trip\TripRepository;
use Illuminate\Support\ServiceProvider;

class TripServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TripInterface::class, TripRepository::class);
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
