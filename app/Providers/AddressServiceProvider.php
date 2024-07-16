<?php

namespace App\Providers;

use App\Repositories\Interfaces\AddressRepository;
use App\Repositories\AddressRepositoryImpl;
use Illuminate\Support\ServiceProvider;

class AddressServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AddressRepository::class, AddressRepositoryImpl::class);
    }
}
