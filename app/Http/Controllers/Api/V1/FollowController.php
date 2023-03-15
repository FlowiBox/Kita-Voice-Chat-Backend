<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request){
        if ($request->user ()->id == $request->user_id){
            return Common::apiResponse (false,'cant follow your self',null,403);
        }
        if (!User::query ()->find ($request->user_id)){
            return Common::apiResponse (false,'this user not found',null,404);
        }
        Follow::query ()->updateOrCreate (
            [
                'user_id'=>$request->user ()->id,
                'followed_user_id'=>$request->user_id
            ],
            [
                'user_id'=>$request->user ()->id,
                'followed_user_id'=>$request->user_id,
                'status'=>1
            ]
        );
        $id = $request->user_id ;
        $path = "$id/followers";
        $obj = [
            'id'=>$request->user ()->id,
        ];
        Common::fireBaseDatabase ($path,$obj);

        $id = $request->user ()->id ;
        $path = "$id/followings";
        $obj = [
            'id'=>$request->user_id,
        ];
        Common::fireBaseDatabase ($path,$obj);

        if (in_array ($request->user_id,$request->user ()->followers_ids()->toArray())){
            $id = $request->user_id  ;
            $path = "$id/friends";
            $obj = [
                'id'=>$request->user ()->id,
            ];
            Common::fireBaseDatabase ($path,$obj);

            $id = $request->user ()->id ;
            $path = "$id/friends";
            $obj = [
                'id'=>$request->user_id,
            ];
            Common::fireBaseDatabase ($path,$obj);
        }

        return Common::apiResponse (true,'follow done',null,201);
    }
    public function unFollow(Request $request){
        Follow::query ()->where ('user_id',$request->user ()->id)->where ('followed_user_id',$request->user_id)->delete ();
        return Common::apiResponse (true,'unFollow done',null,201);
    }
}
