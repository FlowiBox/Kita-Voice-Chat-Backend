<?php

namespace App\Observers\Api\V1;

use App\Models\Agency;
use App\Models\AgencyJoinRequest;
use App\Models\LiveTime;
use App\Models\Target;
use App\Models\User;
use App\Models\UserTarget;
use Carbon\Carbon;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $user->profile ()->create (
            [
                'gender'=>1
            ]
        );
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */


    public function updated(User $user)
    {
        if (!$user->agency_id){
            AgencyJoinRequest::query ()->where ('user_id',$user->id)->delete ();
        }
        if ($user->agency_id){
            $agency = Agency::query ()->find ($user->agency_id);
            if (!$agency){
                AgencyJoinRequest::query ()->where ('user_id',$user->id)->delete ();
            }
        }
        if ($user->is_host == 1){

            $target = Target::query ()->where ('diamonds','<=',$user->coins)->orderBy ('diamonds','desc')->first ();
            if ($target){
                $hours = 0;
                $days = 0;
                $times = LiveTime::query ()->where ('uid',$user->id)
                    ->whereYear ('created_at','=',Carbon::now ()->year)
                    ->whereMonth ('created_at','=',Carbon::now ()->month)
                    ->selectRaw('uid, count(hours) as hnum, count(days) as dnum')
                    ->groupBy ('uid')
                    ->first ()
                ;
                if ($times){
                    $hours = $times->hnum;
                    $days = $times->days;
                }
                UserTarget::query ()->updateOrCreate (
                    [
                        'user_id'=>$user->id,
                        'add_month'=>Carbon::now ()->month,
                        'add_year'=>Carbon::now ()->year
                    ],
                    [
                        'user_id'=>$user->id,
                        'add_month'=>Carbon::now ()->month,
                        'add_year'=>Carbon::now ()->year,
                        'agency_id'=>$user->agency_id,
                        'family_id'=>$user->family_id,
                        'target_id'=>$target->id,
                        'target_diamonds'=>$target->diamonds,
                        'target_usd'=>$target->usd,
                        'target_hours'=>$target->hours,
                        'target_days'=>$target->days,
                        'target_agency_share'=>$target->agency_share,
                        'user_diamonds'=>$user->coins,
                        'user_hours'=>$hours,
                        'user_days'=>$days
                    ]
                );
            }else{
                UserTarget::query ()->where (['user_id'=>$user->id,'add_month'=>Carbon::now ()->month,'add_year'=>Carbon::now ()->year])->delete ();
            }
        }

    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $user->profile ()->delete ();
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }

    /**
     * @param User $user
     * @return void
     */
    public function creating(User $user){

    }

    /**
     * @param User $user
     * @return void
     */
    public function updating (User $user){
        if ($user->agency_id){
            $user->is_host = 1;
        }
        if (!$user->agency_id){
            $user->is_host = 0;
        }
        if ($user->status == 0){
            $user->tokens()->delete ();
        }
    }

    /**
     * @param User $user
     * @return void
     */
    public function saving(User $user){

    }
}
