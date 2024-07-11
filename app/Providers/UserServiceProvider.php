<?php

namespace App\Providers;

use App\Repositories\Interfaces\UserRepository;
use App\Repositories\UserRepositoryImpl;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepository::class, UserRepositoryImpl::class);
    }
}
