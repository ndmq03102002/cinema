<?php

namespace App\Providers\Rating;
use App\Repositories\RepositoryAbstract;

use App\Repositories\Rating\RatingInterface;
use App\Repositories\Rating\RatingRepository;
use Illuminate\Support\ServiceProvider;

class RatingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RatingInterface::class, RatingRepository::class);
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
