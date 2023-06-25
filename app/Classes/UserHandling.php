<?php

namespace App\Classes;

use App\Models\User;
use App\Models\UserSallary;
use Carbon\Carbon;

class UserHandling
{

    public function kickUserFromAgency(User &$user)
    {
        // set diamond to zero
        $user->monthly_diamond_received = 0;

        // set user salary this month to zero
        $values = [
            'user_id'        => $user->id,
            'month'          => Carbon::now()->month,
            'year'           => Carbon::now()->year,
            'user_agency_id' => 0,
            'sallary'        => 0,
            'cut_amount'     => 0
        ];
        UserSallary::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'month'   => Carbon::now()->month,
                'year'    => Carbon::now()->year
            ],
            $values
        );
    }
}
