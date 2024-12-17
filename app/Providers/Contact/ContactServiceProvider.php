<?php

namespace App\Providers\Contact;
use App\Repositories\RepositoryAbstract;

use App\Repositories\Contact\ContactInterface;
use App\Repositories\Contact\ContactRepository;
use Illuminate\Support\ServiceProvider;

class ContactServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ContactInterface::class, ContactRepository::class);
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
