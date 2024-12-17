<?php

namespace App\Providers\Ticket;
use App\Models\Ticket;
use App\Repositories\RepositoryAbstract;

use App\Repositories\Ticket\TicketInterface;
use App\Repositories\Ticket\TicketRepository;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Container\Container;

class TicketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TicketInterface::class, function () {
            return new TicketRepository(new Container(), new Request(), new Ticket());
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
