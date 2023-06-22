<?php

namespace App\Observers\Api\V1;

use App\Helpers\Common;
use App\Models\AgencyJoinRequest;
use App\Models\GiftLog;
use App\Models\User;
use Carbon\Carbon;

class AgencyJoinRequestObserver
{
    /**
     * Handle the AgencyJoinRequest "created" event.
     *
     * @param  \App\Models\AgencyJoinRequest  $agencyJoinRequest
     * @return void
     */
    public function created(AgencyJoinRequest $agencyJoinRequest)
    {
        //
    }

    /**
     * Handle the AgencyJoinRequest "updated" event.
     *
     * @param  \App\Models\AgencyJoinRequest  $agencyJoinRequest
     * @return void
     */
    public function updated(AgencyJoinRequest $agencyJoinRequest)
    {
        //
    }

    /**
     * Handle the AgencyJoinRequest "deleted" event.
     *
     * @param  \App\Models\AgencyJoinRequest  $agencyJoinRequest
     * @return void
     */
    public function deleted(AgencyJoinRequest $agencyJoinRequest)
    {
        //
    }

    /**
     * Handle the AgencyJoinRequest "restored" event.
     *
     * @param  \App\Models\AgencyJoinRequest  $agencyJoinRequest
     * @return void
     */
    public function restored(AgencyJoinRequest $agencyJoinRequest)
    {
        //
    }

    /**
     * Handle the AgencyJoinRequest "force deleted" event.
     *
     * @param  \App\Models\AgencyJoinRequest  $agencyJoinRequest
     * @return void
     */
    public function forceDeleted(AgencyJoinRequest $agencyJoinRequest)
    {
        //
    }

    public function updating(AgencyJoinRequest $agencyJoinRequest){

        if ($agencyJoinRequest->status == 1){
            $user = User::query ()->find ($agencyJoinRequest->user_id);
            $agid = $user->agency_id;
            if ($user){
                $user->agency_id = $agencyJoinRequest->agency_id;
                $user->save ();
                if ($agid == '' || $agid == null || $agid == 0){

                    $user->coins = 0;
                    $user->save ();
                }
                Common::sendOfficialMessage ($user->id,'congratulations','you are accepted in agency');
            }
        }
    }
}
