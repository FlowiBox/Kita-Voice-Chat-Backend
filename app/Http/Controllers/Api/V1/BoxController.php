<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BoxResource;
use App\Http\Resources\Api\V1\BoxUseResource;
use App\Models\Box;
use App\Models\BoxUse;
use App\Models\Room;
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
                    'users_num'=>$box->users,
                    'not_used_num'=>$box->users,
                    'type'=>$box->type,
                    'label'=>$label,
                    'image'=>$box->image,
                ]
            );
            $user->decrement ('di',$box->coins);
            DB::commit ();
            return Common::apiResponse (1,'',new BoxUseResource($boxU),200);
        }catch (\Exception $exception){
            DB::rollBack ();
            dd ($exception);
            return Common::apiResponse (0,'fail',null,400);
        }
    }

    public function pick(Request $request){

    }
}
