<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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

    public function updatePlayerCoins(){

    }
}
