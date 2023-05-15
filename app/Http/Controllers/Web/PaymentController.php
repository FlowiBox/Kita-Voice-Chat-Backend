<?php

namespace App\Http\Controllers\Web;

use App\Classes\PaymentGateways\Stripe;
use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\CoinLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    public function success(Request $request){

        if ($request->p_method == 'strip'){

            $coinLog = CoinLog::query ()->where ('trx',$request->trx)->where ('status',0)->where ('method','strip')->first ();
            if (!$coinLog){
                return Common::apiResponse (0,'fail',null,400);
            }
            $user = User::query ()->find ($coinLog->user_id);

            if (!$coinLog) return Common::apiResponse (0,'cannot find transaction',null,404);
            $session_id = $coinLog->pid;
            if (Stripe::status ($session_id)->payment_status == "paid"){
                if (!$user){
                    return Common::apiResponse (0,'paid but cant found user',null,404);
                }
                $user->increment ('di',$coinLog->obtained_coins);
                $coinLog->status = 1;
                $coinLog->save();
                Common::sendOfficialMessage (@$user->id,__('congratulations'),__('your recharge success'));
                return Common::apiResponse (1,'successfully paid',null,200);
            }
            return Common::apiResponse (0,'fail',null,400);
        }
        return Common::apiResponse (0,'fail',null,400);
    }

    public function fail(){
        return Common::apiResponse (0,'fail',null,400);
    }
}
