<?php

namespace App\Observers\Api\V1;

use App\Models\PK;

class PKObserver
{
    /**
     * Handle the PK "created" event.
     *
     * @param  \App\Models\PK  $pK
     * @return void
     */
    public function created(PK $pK)
    {
        //
    }

    /**
     * Handle the PK "updated" event.
     *
     * @param  \App\Models\PK  $pK
     * @return void
     */
    public function updated(PK $pK)
    {
        //
    }

    public function updating(PK $pK)
    {
        $mics = $pK->mics;
        $m = explode (',',$mics);
        $mic_1 = isset($m[1])?$m[1]:0;
        $mic_2 = isset($m[2])?$m[2]:0;
        $mic_3 = isset($m[3])?$m[3]:0;
        $mic_4 = isset($m[4])?$m[4]:0;
        $mic_5 = isset($m[5])?$m[5]:0;
        $mic_6 = isset($m[6])?$m[6]:0;
        $mic_7 = isset($m[7])?$m[7]:0;
        $mic_8 = isset($m[8])?$m[8]:0;
        $team_1 = [$mic_1,$mic_2,$mic_5,$mic_6];
        $team_2 = [$mic_3,$mic_4,$mic_7,$mic_8];
        $t1 = implode (',',$team_1);
        $t2 = implode (',',$team_2);
        $pK->team_1 = $t1;
        $pK->team_2 = $t2;
    }

    /**
     * Handle the PK "deleted" event.
     *
     * @param  \App\Models\PK  $pK
     * @return void
     */
    public function deleted(PK $pK)
    {
        //
    }



    /**
     * Handle the PK "restored" event.
     *
     * @param  \App\Models\PK  $pK
     * @return void
     */
    public function restored(PK $pK)
    {
        //
    }

    /**
     * Handle the PK "force deleted" event.
     *
     * @param  \App\Models\PK  $pK
     * @return void
     */
    public function forceDeleted(PK $pK)
    {
        //
    }
}
