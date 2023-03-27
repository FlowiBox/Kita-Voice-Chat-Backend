<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BoxResource;
use App\Http\Resources\Api\V1\BoxUseResource;
use App\Models\Box;
use App\Models\BoxUse;
use App\Models\GiftLog;
use App\Models\Room;
use App\Models\User;
use App\Models\UserBoxGift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoxController extends Controller
{
    public function index(){
        $normal = Box::query ()->where ('type',0)->orderByDesc ('id')->get ();
        $super = Box::query ()->where ('type',1)->orderByDesc ('id')->get ();
        $data = [
            'normal'=>BoxResource::collection($normal),
            'super'=>BoxResource::collection($super)
        ];
        return Common::apiResponse (1,'',$data,200);
    }

    public function send(Request $request){
        if (!$request->box_id || !$request->room_uid) return Common::apiResponse (0,'missing params',null,422);
        $room = Room::query ()->where ('uid',$request->room_uid)->first ();
        if (!$room)  return Common::apiResponse (0,'not found',null,404);
        $box = Box::query ()->find ($request->box_id);
        $user = $request->user ();
        $label = '';
        if (!$box) return Common::apiResponse (0,'not found',null,404);
        if ($request->label && $box->type == 1 && $box->has_label == 1){
            $label = $request->label;
        }
        if ($user->di < $box->coins){
            return Common::apiResponse (0,'low balance',null,407);
        }
        try {
            DB::beginTransaction ();
            $boxU = BoxUse::query ()->create (
                [
                    'box_id'=>$box->id,
                    'user_id'=>$user->id,
                    'coins'=>$box->coins,
                    'end_at'=>now ()->addMinutes ($box->duration)->timestamp,
                    'room_uid'=>$room->uid,
                    'room_id'=>$room->id,
                    'users_num'=>$request->users_num,
                    'not_used_num'=>$box->users,
                    'unused_coins'=>$box->coins,
                    'type'=>$box->type,
                    'label'=>$label,
                    'image'=>$box->image,
                ]
            );
            $user->decrement ('di',$box->coins);
            GiftLog::query ()->create (
                [
                    'type'=>2,
                    'giftId'=>$boxU->id,
                    'roomowner_id'=>$room->uid,
                    'giftName'=>'luck box',
                    'giftNum'=>1,
                    'giftPrice'=>$box->coins,
                    'sender_id'=>$user->id,
                    'receiver_id'=>0,
                    'sender_family_id'=>$user->family_id,
                ]
            );
            DB::commit ();
            return Common::apiResponse (1,'',new BoxUseResource($boxU),200);
        }catch (\Exception $exception){
            DB::rollBack ();
            dd ($exception);
            return Common::apiResponse (0,'fail',null,400);
        }
    }

    public function pick(Request $request){
        $user = $request->user ();
        if(!$request->bid) return Common::apiResponse (0,'missing params',null,422);
        $box_use = BoxUse::query ()->where ('id',$request->bid)
            ->where ('not_used_num','>',0)
            ->where ('unused_coins','>',0)
            ->first ()
        ;
        if(!$box_use) return Common::apiResponse (0,'not found',null,404);
        $already_used = UserBoxGift::query ()->where('user_id',$user->id)->where ('box_uses_id',$box_use->id)->exists ();
        if ($already_used) return Common::apiResponse (0,'used it before',null,403);
        $coins = rand (0,($box_use->unused_coins/$box_use->not_used_num));
        if ($box_use->not_used_num == 1){
            $coins = $box_use->unused_coins;
        }

        try {
            DB::beginTransaction ();
            UserBoxGift::query ()->create (
                [
                    'box_uses_id'=>$box_use->id,
                    'user_id'=>$user->id,
                    'coins'=>$coins,
                    'room_uid'=>$box_use->room_uid,
                    'room_id'=>$box_use->room_id,
                    'type'=>$box_use->type,
                    'box_uses_owner_id'=>$box_use->user_id,
                    'image'=>$box_use->image,
                    'label'=>$box_use->label
                ]
            );
            $box_use->increment ('used_coins',$coins);
            $box_use->increment ('used_num',1);
            $box_use->decrement ('unused_coins',$coins);
            $box_use->decrement ('not_used_num',1);
            $user->increment ('di',$coins);
            DB::commit ();
            return Common::apiResponse (1,'ok',null,201);
        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,'fail',null,400);
        }
    }
}
