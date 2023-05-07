<?php

namespace App\Observers\Api\V1;

use App\Helpers\Common;
use App\Models\Agency;
use App\Models\AgencyJoinRequest;
use App\Models\GiftLog;
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
    }


    public function saved(User $user)
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
        AgencyJoinRequest::query ()->where ('user_id',$user->id)->delete ();
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
        $user->uuid = (string)rand (1000000,9999999);
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

        if (!$user->uuid){
            $user->uuid = (string)rand (1000000,9999999);
        }

        if ($user->is_host == 1){
            $month_received = GiftLog::query ()
                ->where ('receiver_id',$user->id)
                ->whereYear ('created_at',Carbon::now ()->year)
                ->whereMonth ('created_at',Carbon::now ()->month)
                ->sum ('receiver_obtain');
            $target = Target::query ()->where ('diamonds','<=',$month_received)->orderBy ('diamonds','desc')->first ();
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
                    $days = $times->dnum;
                }
                $per = 0.50;
                if ($target->hours <= $hours){
                    $per += 0.20;
                }
                if ($target->days <= $days){
                    $per += 0.30;
                }
                $t = $target->usd * $per;
                $ap = $target->agency_share/100;
                $user->target_usd = $t;
                $tar = UserTarget::query ()
                    ->where ('user_id',$user->id)
                    ->where ('add_month',Carbon::now ()->month)
                    ->where ('add_year',Carbon::now ()->year)
                    ->where ('target_id',$target->id)
                    ->exists ();
                if (!$tar){
                    Common::sendOfficialMessage ($user->id,__ ('congratulations'),__ ('you achieve new target'));
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
                        'user_diamonds'=>$month_received,
                        'user_hours'=>$hours,
                        'user_days'=>$days,
                        'user_obtain'=>$t,
                        'agency_obtain'=>$t * $ap
                    ]
                );
            }
        }

    }

    public function saving(User $user){
        if ($user->agency_id){
            $user->is_host = 1;
        }
        if (!$user->agency_id){
            $user->is_host = 0;
        }
        if ($user->status == 0){
            $user->tokens()->delete ();
        }

        if (!$user->uuid){
            $user->uuid = (string)rand (1000000,9999999);
        }

        if ($user->is_host == 1){
            $month_received = GiftLog::query ()
                ->where ('receiver_id',$user->id)
                ->whereYear ('created_at',Carbon::now ()->year)
                ->whereMonth ('created_at',Carbon::now ()->month)
                ->sum ('receiver_obtain');
            $target = Target::query ()->where ('diamonds','<=',$month_received)->orderBy ('diamonds','desc')->first ();
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
                    $days = $times->dnum;
                }
                $per = 0.50;
                if ($target->hours <= $hours){
                    $per += 0.20;
                }
                if ($target->days <= $days){
                    $per += 0.30;
                }
                $t = $target->usd * $per;
                $ap = $target->agency_share/100;
                $user->target_usd = $t;
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
                        'user_diamonds'=>$month_received,
                        'user_hours'=>$hours,
                        'user_days'=>$days,
                        'user_obtain'=>$t,
                        'agency_obtain'=>$t * $ap
                    ]
                );


            }
        }
    }

    /**
     * @param User $user
     * @return void
     */

}