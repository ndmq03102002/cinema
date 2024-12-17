<?php

namespace App\Providers\Promotion;
use App\Models\Promotion;
use App\Repositories\RepositoryAbstract;

use App\Repositories\Promotion\PromotionInterface;
use App\Repositories\Promotion\PromotionRepository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class PromotionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PromotionInterface::class, function () {
            return new PromotionRepository(new Container(), new Request(), new Promotion());
        });
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
