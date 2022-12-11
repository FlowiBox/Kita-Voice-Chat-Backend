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
            return Common::apiResponse (false,'cant follow your self',null,200);
        }
        if (!User::query ()->find ($request->user_id)){
            return Common::apiResponse (false,'this user not found',null,200);
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
        return Common::apiResponse (true,'follow done',null,200);
    }
    public function unFollow(Request $request){
        Follow::query ()->where ('user_id',$request->user ()->id)->where ('followed_user_id',$request->user_id)->delete ();
        return Common::apiResponse (true,'unFollow done',null,200);
    }
}
