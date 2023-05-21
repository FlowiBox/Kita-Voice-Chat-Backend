<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class GameController extends Controller
{
    public function playerInfo(Request $request){
        $uid = PersonalAccessToken::findToken ($request->token)->tokenable->id;
        $user = \App\Models\User::query ()->find ($uid);
        if (!$user){
            $res = [
                'error_code'=>1,
                'error_message'=>'Cannot get user info',
                'data'=>''
            ];
        }else{
            $data = [
                'name'=>$user->name,
                'uid'=>$user->id,
                'avatar'=>asset ("storage/$user->avatar"),
                'coins'=>(integer)$user->di
            ];
            $res = [
                'error_code'=>0,
                'error_message'=>null,
                'data'=>$data
            ];
        }

        return $res;
    }

    public function updatePlayerCoins(Request $request){
        $payload = $request->payload['payload'];
        try {
            foreach ($payload as $item){
                $user = User::query ()->find($item['uid']);
                if ($item['up'] == 1){
                    $user->increment ('di',$item['amount']);
                }else{
                    $user->decrement ('di',$item['amount']);
                }
            }
            $round = $request->round_number;

            $res = [
                'error_code'=>0,
                'error_message'=>null,
            ];
        }catch (\Exception $exception){
            $res = [
                'error_code'=>1,
                'error_message'=>"Cannot update coins due to ".$exception->getMessage (),
            ];
        }

        return $res;

    }
}
