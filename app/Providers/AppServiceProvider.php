<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Bill;
use App\Models\Room;
use App\Models\Seat;
use App\Repositories\admin\Room\RoomInterface;
use App\Repositories\admin\Room\RoomRepository;
use App\Repositories\admin\Seat\SeatInterface;
use App\Repositories\admin\Seat\SeatRepository;
use App\Repositories\user\Account\AccountInterface;
use App\Repositories\user\Account\AccountRepository;
use App\Repositories\user\Bill\BillInterface;
use App\Repositories\user\Bill\BillRepository;
use App\Repositories\user\Room\RoomRepository as RoomRepositoryUser;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AccountInterface::class, function () {
            return new AccountRepository(new Container(), new Request(), new Account());
        });

        $this->app->bind(BillInterface::class, function () {
            return new BillRepository(new Container(), new Request(), new Bill());
        });

        $this->app->bind(RoomInterface::class, function () {
            return new RoomRepository(new Container(), new Request(), new Room());
        });


        $this->app->bind(SeatInterface::class, function () {
            return new SeatRepository(new Container(), new Request(), new Seat());
        });

        $this->app->bind(SeatInterface::class, function () {
            return new SeatRepository(new Container(), new Request(), new Seat());
        });
        $this->app->bind(\App\Repositories\user\Room\RoomInterface::class, function() {
            return new RoomRepository(new Container(), new Request(), new Seat());
        });
    }



    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }
}
