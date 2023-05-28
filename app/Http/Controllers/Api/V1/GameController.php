<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class GameController extends Controller
{
    public function playerInfo(Request $request){
        $uid = @PersonalAccessToken::findToken ($request->token)->tokenable->id;
        if (!$uid){
            $res = [
                'error_code'=>1,
                'error_message'=>'please inter valid token',
                'data'=>''
            ];
        }
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
            Game::query ()
                ->create (
                    [
                        'game_id'=>$request['game_id'],
                        'uid'=>$uid,
                        'lang'=>$request['lang'],
                        'sign'=>$request['sign']
                    ]
                );
            $res = [
                'error_code'=>0,
                'error_message'=>null,
                'data'=>$data
            ];
        }


        return $res;
    }

    public function updatePlayerCoins_(Request $request){
        $payload = $request->payload;
        try {
            foreach ($payload as $item){
                $user = User::query ()->find($item['uid']);
                if ($item['up'] == 1){
                    $user->increment ('di',$item['amount']);
                    $am = 0 + $item['amount'];
                }else{
                    $user->decrement ('di',$item['amount']);
                    $am = 0 - $item['amount'];
                }
                Game::query ()
                    ->where ('game_id',$item['game_id'])
                    ->where ('uid',$item['uid'])
                    ->update (
                        [
                            'round'=>$request->round_number,
                            'amount'=>$am
                        ]
                    );

            }

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


    public function updatePlayerCoins()
    {
        $payload = request('payload');

        foreach ($payload as $i =>  $item) {
            $user = User::query ()->where('id', $item['uid'])->first();

            if ($item['up'] == 1) {
                $user->increment('di', $item['amount']);
            } else {
                $user->decrement('di', $item['amount']);
            }
        }

        return [
            'error_code'=>0,
            'error_message'=>null,
        ];
    }
}
