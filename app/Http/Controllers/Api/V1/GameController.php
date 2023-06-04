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


    public function updatePlayerCoins000()
    {
        $payload = request('payload');
        $errors = [];
        foreach ($payload as $i =>  $item) {
            $uid = @PersonalAccessToken::findToken ($item['token'])->tokenable->id;
            if (!$uid){
                array_push ($errors ,[
                    'error_code'=>1,
                    'token'=>$item['token'],
                    'error_message'=>'please inter valid token',
                ]) ;
            }
            $user = \App\Models\User::query ()->find ($uid);
            if (!$user) {
                array_push ($errors , [
                    'error_code' => 1 ,
                    'user_id'=>$uid,
                    'error_message' => 'Cannot find user',
                ]);
                continue;
            }

            if ($item['up'] == 1) {
                $user->increment('di', $item['amount']);
                Game::query ()
                    ->where ('game_id',$item['game_id'])
                    ->where ('uid',$item['uid'])
                    ->update (
                        [
                            'amount'=>$item['amount'] * 1
                        ]
                    );
            } else {
                if (($user->di - $item['amount']) > 0){
                    $user->decrement('di', $item['amount']);
                }else{
                    array_push ($errors , [
                        'error_code'=>0,
                        'user_id'=>$user->id,
                        'error_message'=>'low balance',
                    ]);
                }
                Game::query ()
                    ->where ('game_id',$item['game_id'])
                    ->where ('uid',$item['uid'])
                    ->update (
                        [
                            'amount'=> 0 - $item['amount']
                        ]
                    );
            }

        }
        if ($errors){
            return $errors;
        }
        return [
            'error_code'=>0,
            'error_message'=>null,
        ];
    }


    public function updatePlayerCoins()
    {
        $payload = request('payload');
        $users = User::whereIn('id', array_column($payload, 'uid'))->get();

        foreach ($payload as $i => $item) {
            $user = $users->where('id', $item['uid'])->first(); // change "id" to the col name that you place player  uid. (The player number)
            if (!$user) continue;
            if ($item['up'] == 0 && $user->di < $item['amount']) continue;
            $method = $item['up'] ? "increment" : "decrement";
            $user->{$method}('di', $item['amount']);

            try {
                $gameQuery = Game::where('game_id', $item['game_id'])->where('uid', $item['uid']);
                if ($gameRecord = $gameQuery->first()) {
                    $gameRecord->$method('amount', $item['amount']);
                } else {
                    $gameQuery->create([
                                           'game_id' => $item['game_id'],
                                           'uid' => $item['uid'],
                                           'amount' => $item['up'] ? $item['amount'] : -$item['amount'],
                                       ]);
                }
            } catch (\Exception $ex) {
                // Update it if you need.
                \Log::critical('Invalid Game Query', [
                    'exception' => $ex,
                ]);
            }

        }

        return [
            'errorCode' => 0,
        ];
    }
}
