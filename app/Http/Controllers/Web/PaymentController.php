<?php

namespace App\Http\Controllers\Web;

use App\Classes\PaymentGateways\Stripe;
use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\CoinLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    public function success(Request $request){
        if ($request->p_method == 'stripe'){
            $coinLog = CoinLog::query ()->where ('trx',$request->trx)->where ('status',0)->where ('method','strip')->first ();
            if (!$coinLog) return Common::apiResponse (0,'cannot find transaction',null,404);
            $session_id = $coinLog->pid;
            dd (Stripe::status ($session_id));
        }


    }

    public function fail(){
        return Common::apiResponse (0,'fail',null,400);
    }
}
