<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\BlackList;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlackListController extends Controller
{
    public function index(Request $request){
        $user = $request->user ();
        return Common::apiResponse (1,'',UserResource::collection ($this->getList ($user->id)),200);
    }

    public function remove(Request $request){
        if (!$request->user_id) return Common::apiResponse (0,'missing params',null,422);
        $me = $request->user ();
        BlackList::query ()->where('user_id', $me->id)->where ('from_uid',$request->user_id)->delete ();
        return Common::apiResponse (1,'done',UserResource::collection ($this->getList ($me->id)),200);
    }

    public function add(Request $request){
        if (!$request->user_id) return Common::apiResponse (0,'missing params',null,422);
        $me = $request->user ();
        DB::beginTransaction ();
        try {
            BlackList::query ()->create (
                [
                    'user_id'=>$me->id,
                    'from_uid'=>$request->user_id
                ]
            );
            Follow::query ()->where ('user_id',$me->id)->where ('followed_user_id',$request->user_id)->delete ();
            Follow::query ()->where ('user_id',$request->user_id)->where ('followed_user_id',$me->id)->delete ();
            DB::commit ();
            return Common::apiResponse (1,'done',UserResource::collection ($this->getList ($me->id)),200);

        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,'fail',null,400);
        }
           }

    public function getList($user_id){
        $black_list = Common::getUserBlackList ($user_id);
        $blacks = User::query ()->whereIn ('id',$black_list)->get ();
        return UserResource::collection ($blacks);
    }
}
