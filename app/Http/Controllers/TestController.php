<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use App\Models\User;
use App\Models\UserDay;
use App\Models\Vip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index() {
        foreach (\App\Models\User::all() as $user) {
            $star_num_old = \DB ::table ( 'gift_logs' ) -> where ( 'receiver_id' , $user->id ) -> sum ( 'giftPrice' );
            $gold_num_old = \DB ::table ( 'gift_logs' ) -> where ( 'sender_id' , $user->id ) -> sum ( 'giftPrice' );



            $user->total_diamond_received   = $star_num_old;
            $user->total_diamond_send   = $gold_num_old;


            $user->received_level = \App\Models\Vip::query()->where(['type' => 1])->where('exp', '<=', $star_num_old)->orderByDesc('exp')->limit(1)->first()->level;


            $user->sender_level   = \App\Models\Vip::query()->where(['type' => 1])->where('exp', '<=', $gold_num_old)->orderByDesc('exp')->limit(1)->first()->level;

            $user->save();

        }
    }
}
