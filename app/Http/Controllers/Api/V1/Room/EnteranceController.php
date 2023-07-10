<?php

namespace App\Http\Controllers\Api\V1\Room;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BoxUseResource;
use App\Http\Resources\Api\V1\MiniUserResource;
use App\Http\Resources\Api\V1\PkResource;
use App\Http\Resources\Api\V2\EnterRoomCollection;
use App\Jobs\EnterRoomZigoRequest;
use App\Models\Background;
use App\Models\BoxUse;
use App\Models\EnteredRoom;
use App\Models\Family;
use App\Models\GiftLog;
use App\Models\LiveTime;
use App\Models\Pk;
use App\Models\RequestBackgroundImage;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnteranceController extends Controller
{
    public function enter_room(Request $request)
    {

        $room_pass = $request['room_pass'];
        $owner_id  = $request['owner_id'];


        if ($request->type == 'random'){
            $owner_id = Room::query ()
                ->where('room_status',1)
                ->where('uid','!=',null)
                ->where (function ($q){
                    $q->where ('count_room_socket','!=',0) ->orWhere('is_afk',1);
                })
                ->pluck ('uid')
                ->random ();
        }

        $user   = $request->user();
        $user_id = $user->id;

        // if owner id not path throw error
        if (!$owner_id) return Common::apiResponse (0,'not found',null,404);

        //check if this user in black-list
        $black_list=Common::getUserBlackList($owner_id);
        if(in_array($user_id, $black_list)) return Common::apiResponse(false,__('You have been blocked by the other party'),null,423);


        // get room by owner_id
        $room = Room::query()->where('uid', $owner_id)
            ->with(['owner', 'roomCategory', 'family'])
            ->first();

        $roomBlack = $room->room_black;
        if(!empty($roomBlack)){
            $is_black = explode(',', $roomBlack);
            foreach ($is_black as $k => &$v) {
                $arr=explode("#",$v);
                $sjc= time() - $arr[1];
                $rt = $arr[2] - $sjc;
                $h = floor ($rt/3600);
                $r = $rt%3600;
                $m = floor($r/60);
                $s = $r%60;
                if($sjc < $arr[2] && $arr[0] == $user_id ){
                    return Common::apiResponse(false,__('No entry for '). $arr[2]/60 .__(' minutes after being kicked out of the room'),['remaining_time'=>"$h:$m:$s"],200);
                }

                if($sjc >= $arr[2]){
                    unset($is_black[$k]);
                }
            }
            $roomBlack = implode(",", $is_black);
            DB::table('rooms')->where('uid',$owner_id)->update(['room_black'=>trim($roomBlack,',')]);
        }

        if(!$room) return Common::apiResponse (false,'No room yet, please create first',null,404);

        if($room->room_pass &&  $owner_id != $user_id && !$request->ignorePassword){
            if(!$room_pass) return Common::apiResponse(false,__('The room is locked, please enter the password'),null,409);
            if($room->room_pass != $room_pass )  return Common::apiResponse(false,__('Password is incorrect, please re-enter'),null,410);
        }


        if (!$request->is_update){
            if ($request->sendToZego != 'no') {
                dispatch(new EnterRoomZigoRequest($user, $room->id, $request->have_vip));
            }
        }
//        $this->getRoomTwoLastPk($room->id);
        $room_info = (new EnterRoomCollection($room, $user_id));
        $room_info['room_rule'] = Common::getConfig('room_rule');

        $this->enterTheRoomCreateOrUpdate($user_id, $owner_id, $room->id);


        $this->updateRoomVisitor($user_id, $owner_id, $room);
        //send to zego
        $user->enableSaving = false;
        $user->now_room_uid = (integer)$owner_id;
        $user->save();

        return Common::apiResponse(true, '', $room_info);
    }


    private function updateRoomVisitor($user_id, $owner_id, Room $room)
    {
        if($user_id == $owner_id) return;

        $visitors = explode(',', $room->room_visitor);
        if ($visitors[0] == '') $visitors = [];
        if(!in_array($user_id, $visitors)) {
            $visitors[] = $user_id;
            $visitors = array_unique($visitors);
            $visitors=trim(implode(",", $visitors),",");
            $room->room_visitor = $visitors;
            $room->save();
        }
    }

    //exit the room
    public function quit_room(Request $request){
        if(!$request->owner_id)   Common::apiResponse(false,__('missing owner_id'),null,422);
        $user_id=$request->user ()->id;
        $res=Common::quit_hand($request->owner_id,$user_id);
        $visitor_ids_list = explode (',',$res);
        $user = $request->user ();
        $user->now_room_uid = 0;
        $user->save();
        $this->calcTime ($user_id);
        return Common::apiResponse(true,'exited',['visitor_ids_list'=>$visitor_ids_list]);
    }

    public function out_room(Request $request){
        $uid = $request->owner_id ? : 0;
        $black_id = $request->user_id ? : 0;
        $duration = $request->minutes ? : 5;
        if(!$uid || !$black_id) return Common::apiResponse (0,'invalid data',null,422);
        if (!Common::can_kick ($black_id)) return Common::apiResponse (0,'cant kick this user',null,403);
        $black_list = @DB::table('rooms')->where('uid',$uid)->first ()->room_black;
        $room_id = @DB::table('rooms')->where('uid',$uid)->first ()->id;
        if($black_list == null){
            $black_list = $black_id.'#'.time().'#'.($duration * 60);
        }else{
            $list = explode(',', $black_list);
            $exists = false;
            foreach ($list as &$item) {
                $black = explode ('#',$item);
                if ($black[0] == $black_id){
                    $item = $black_id.'#'.time().'#'.($duration * 60);
                    $exists = true;
                }
            }
            if (!$exists){
                array_push ($list,$black_id.'#'.time().'#'.($duration * 60));
            }

            $black_list = implode (',',$list);

        }
        $result = DB::table('rooms')->where('uid',$uid)->update(['room_black'=>$black_list]);

        if($result){
            //exit the room
            Common::quit_hand($uid,$black_id);
            $user = User::find($black_id);
            if ($user){
                $user->now_room_uid = 0;
                $user->save();
            }
            $mc = [
                'messageContent'=>[
                    'message'=>'kickout',
                    'duration'=>$duration
                ]
            ];
            $json = json_encode ($mc);
            $b = User::find($black_id);
            $n = 'nan';
            if ($b){
                $n = $b->name?:'nan';
            }
            Common::sendToZego_4 ('SendCustomCommand',$room_id,$uid,$black_id,$json);
            $this->calcTime($black_id);
            Common::sendToZego_2 ('SendBroadcastMessage',$room_id,$uid,'room'," تم طرد $n" );
            return Common::apiResponse(1,'success');
        }else{
            return Common::apiResponse(0,'fail',null,400);
        }
    }

    private function enterTheRoomCreateOrUpdate($user_id, $owner_id, $room_id)
    {
        EnteredRoom::query ()->updateOrCreate (
            [
                'uid'=>$user_id,
                'ruid'=>$owner_id,
                'rid'=>$room_id
            ],
            [
                'entered_at'=>now ()
            ]
        );
    }

    public function calcTime($uid){
        $timer = LiveTime::query ()->where ('uid',$uid)->where('end_time',null)->first ();
        if ($timer){
            $hours = round((time () - $timer->start_time)/(60*60),2);
            $timer->end_time = time ();
            $timer->hours = $hours;
            $d = LiveTime::query ()->where ('uid',$uid)->whereDate ('created_at',today ())->where ('days','>=',1)->exists ();
            if (!$d){
                if ($hours >= 1){
                    $timer->days = 1;
                }
            }

            $timer->save ();
        }
    }
}
