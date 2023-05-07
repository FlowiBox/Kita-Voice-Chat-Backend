<?php

namespace App\Observers\Api\V1;

use App\Models\Family;
use App\Models\FamilyUser;
use App\Models\GiftLog;
use App\Models\User;

class FamilyObserver
{
    /**
     * Handle the Family "created" event.
     *
     * @param  \App\Models\Family  $family
     * @return void
     */
    public function created(Family $family)
    {

    }

    /**
     * Handle the Family "updated" event.
     *
     * @param  \App\Models\Family  $family
     * @return void
     */
    public function updated(Family $family)
    {
        //
    }

    /**
     * Handle the Family "deleted" event.
     *
     * @param  \App\Models\Family  $family
     * @return void
     */
    public function deleted(Family $family)
    {
        User::query ()->where ('family_id',$family->id)->update (['family_id'=>0]);
        FamilyUser::query ()->where ('family_id',$family->id)->delete ();
    }

    /**
     * Handle the Family "restored" event.
     *
     * @param  \App\Models\Family  $family
     * @return void
     */
    public function restored(Family $family)
    {
        //
    }

    /**
     * Handle the Family "force deleted" event.
     *
     * @param  \App\Models\Family  $family
     * @return void
     */
    public function forceDeleted(Family $family)
    {
        //
    }
}
