<?php

namespace App\Providers\Payment;
use App\Repositories\RepositoryAbstract;

use App\Repositories\Payment\PaymentInterface;
use App\Repositories\Payment\PaymentRepository;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PaymentInterface::class, PaymentRepository::class);
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
