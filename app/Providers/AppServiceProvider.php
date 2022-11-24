<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\Api\V1\UserObserver;
use App\Repositories\Room\RoomRepo;
use App\Repositories\Room\RoomRepoInterface;
use App\Repositories\User\UserRepo;
use App\Repositories\User\UserRepoInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind (RoomRepoInterface::class,RoomRepo::class);
        $this->app->bind (UserRepoInterface::class,UserRepo::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe (UserObserver::class);
    }
}
