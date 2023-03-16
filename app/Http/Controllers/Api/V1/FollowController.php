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
        $f = Follow::query ()->where (
            [
                'user_id'=>$request->user ()->id,
                'followed_user_id'=>$request->user_id
            ]
        )->exists ();
        if (!$f){
            Follow::query ()->create (

                [
                    'user_id'=>$request->user ()->id,
                    'followed_user_id'=>$request->user_id,
                    'status'=>1
                ]
            );
            Common::handelFirebase ($request,'follow');
        }


//        $id = (integer)$request->user_id ;
//        $followers_count = @Common::fireBaseDatabase ("$id/followers",'','get')['count']?:0;
//        $path = "$id/followers";
//        $obj = [
//            'count'=>$followers_count + 1,
//        ];
//        Common::fireBaseDatabase ($path,$obj);
//
//        $id = $request->user ()->id ;
//        $followings_count = @Common::fireBaseDatabase ("$id/followings",'','get')['count']?:0;
//        $path = "$id/followings";
//        $obj = [
//            'count'=>$followings_count + 1,
//        ];
//        Common::fireBaseDatabase ($path,$obj);
//
//        if (in_array ($request->user_id,$request->user ()->followers_ids()->toArray())){
//            $id = (integer)$request->user_id ;
//            $friends_count = @Common::fireBaseDatabase ("$id/friends",'','get')['count']?:0;
//            $path = "$id/friends";
//            $obj = [
//                'count'=>$friends_count + 1,
//            ];
//            Common::fireBaseDatabase ($path,$obj);
//
//            $id = $request->user ()->id ;
//            $friends_count = @Common::fireBaseDatabase ("$id/friends",'','get')['count']?:0;
//            $path = "$id/friends";
//            $obj = [
//                'count'=>$friends_count + 1,
//            ];
//            Common::fireBaseDatabase ($path,$obj);
//        }

        return Common::apiResponse (true,'follow done',null,201);
    }
    public function unFollow(Request $request){
        Follow::query ()->where ('user_id',$request->user ()->id)->where ('followed_user_id',$request->user_id)->delete ();
        return Common::apiResponse (true,'unFollow done',null,201);
    }
}
