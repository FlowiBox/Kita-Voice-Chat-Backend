<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index() {
        foreach (User::all() as $user) {
            $star_num_old = DB ::table ( 'gift_logs' ) -> where ( 'receiver_id' , $user->id ) -> sum ( 'giftPrice' );
            $gold_num_old = DB ::table ( 'gift_logs' ) -> where ( 'sender_id' , $user->id ) -> sum ( 'giftPrice' );

            $user->total_diamond_received   = $star_num_old;
            $user->total_diamond_send   = $gold_num_old;


            $user->received_level = $this->getLevel(1, $star_num_old)->level;

            $user->sender_level   = $this->getLevel(2, $gold_num_old)->level;


            $user->save();



        }
        dd('dd');
    }

    public function getLevel(int $type, int $totalCoins)
    {
        return Vip::query()->where(['type' => $type])->where('exp', '<=', $totalCoins)->orderByDesc('exp')->limit(1)->first();
    }
}
