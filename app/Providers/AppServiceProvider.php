<?php

namespace App\Providers;

use App\Classes\UserHandling;
use App\Models\Agency;
use App\Models\AgencyJoinRequest;
use App\Models\Family;
use App\Models\FamilyUser;
use App\Models\Pk;
use App\Models\Room;
use App\Models\User;
use App\Observers\Api\V1\AgencyJoinRequestObserver;
use App\Observers\Api\V1\AgencyObserver;
use App\Observers\Api\V1\FamilyObserver;
use App\Observers\Api\V1\FamilyUserObserver;
use App\Observers\Api\V1\PKObserver;
use App\Observers\Api\V1\RoomObserver;
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
        $this->app->storagePath();
        $this->app->bind (RoomRepoInterface::class,RoomRepo::class);
        $this->app->bind (UserRepoInterface::class,UserRepo::class);
        $this->app->bind ('UserHandling',function($app){
            return new UserHandling();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe (UserObserver::class);
        Room::observe(RoomObserver::class);
        Family::observe (FamilyObserver::class);
        FamilyUser::observe (FamilyUserObserver::class);
        Pk::observe (PKObserver::class);
        Agency::observe (AgencyObserver::class);
        AgencyJoinRequest::observe (AgencyJoinRequestObserver::class);
    }
}
