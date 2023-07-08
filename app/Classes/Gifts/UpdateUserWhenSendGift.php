<?php

namespace App\Classes\Gifts;

use App\Models\User;
use App\Models\Vip;

class UpdateUserWhenSendGift
{

    public function update(int $totalCoins, User $receivedUser)
    {
        $receivedUser->enableSaving = false;
        //update monthly diamond for received user
        $receivedUser->monthly_diamond_received += $totalCoins;
        $receivedUser->total_diamond_received   += $totalCoins;
        // update levels
        $receivedUser->received_level = $this->getLevel(1, $receivedUser->total_diamond_received) != null ? $this->getLevel(1, $receivedUser->total_diamond_received)->level: 0;
        //
        $receivedUser->save();
        $receivedUser->enableSaving = true;

        return $receivedUser;

    }

    public function getLevel(int $type, int $totalCoins)
    {
        return Vip::query()->where(['type' => $type])->where('exp', '<=', $totalCoins)->orderByDesc('exp')->limit(1)->first();
    }


    /*
     * 1 for receiver
     * 2, 3 for sender or vip
     */

    public function send(int $totalCoins, User $senderUser)
    {
        $senderUser->enableSaving         = false;
        $senderUser->monthly_diamond_send += $totalCoins;
        $senderUser->total_diamond_send   += $totalCoins;
        $senderUser->sender_level         = $this->getLevel(2, $senderUser->total_diamond_send) != null ? $this->getLevel(2, $senderUser->total_diamond_send)->level: 0;
        $senderUser->save();
        $senderUser->enableSaving = true;

        return $senderUser;

    }
}
