<?php

namespace App\Observers\Api\V1;

use App\Models\Family;
use App\Models\FamilyUser;
use App\Models\User;

class FamilyUserObserver
{
    /**
     * Handle the FamilyUser "created" event.
     *
     * @param  \App\Models\FamilyUser  $familyUser
     * @return void
     */
    public function created(FamilyUser $familyUser)
    {
        if ($familyUser->status == 1){
            User::query ()->where ('id',$familyUser->user_id)->update (['family_id'=>$familyUser->family_id]);
        }
    }

    /**
     * Handle the FamilyUser "updated" event.
     *
     * @param  \App\Models\FamilyUser  $familyUser
     * @return void
     */
    public function updated(FamilyUser $familyUser)
    {
        if ($familyUser->status == 1){
            User::query ()->where ('id',$familyUser->user_id)->update (['family_id'=>$familyUser->family_id]);
        }
    }

    /**
     * Handle the FamilyUser "deleted" event.
     *
     * @param  \App\Models\FamilyUser  $familyUser
     * @return void
     */
    public function deleted(FamilyUser $familyUser)
    {
        $family = Family::query ()->where ('id',$familyUser->family_id)->first ();
        if ($family){
            User::query ()->where ('id',$familyUser->user_id)->where ('id','!=',$family->user_id)->update (['family_id'=>0]);
        }else{
            User::query ()->where ('id',$familyUser->user_id)->update (['family_id'=>0]);
        }

    }

    public function deleting(FamilyUser $familyUser){

    }

    /**
     * Handle the FamilyUser "restored" event.
     *
     * @param  \App\Models\FamilyUser  $familyUser
     * @return void
     */
    public function restored(FamilyUser $familyUser)
    {
        //
    }

    /**
     * Handle the FamilyUser "force deleted" event.
     *
     * @param  \App\Models\FamilyUser  $familyUser
     * @return void
     */
    public function forceDeleted(FamilyUser $familyUser)
    {
        //
    }
}
