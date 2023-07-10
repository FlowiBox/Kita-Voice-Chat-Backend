<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CacheHelper;
use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BoxUseResource;
use App\Http\Resources\Api\V2\EnterRoomCollection;
use App\Http\Resources\Api\V1\MiniUserResource;
use App\Http\Resources\Api\V1\PkResource;
use App\Http\Resources\Api\V1\RoomResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Jobs\EnterRoomZigoRequest;
use App\Models\Background;
use App\Models\BoxUse;
use App\Models\EnteredRoom;
use App\Models\Family;
use App\Models\GiftLog;
use App\Models\LiveTime;
use App\Models\Pk;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\User;
use App\Models\RequestBackgroundImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{

    public function getAdmins(Request $request){
        if (!$request->owner_id) return Common::apiResponse (0,'missing params',null,422);
        $room = Room::query ()->where ('uid',$request->owner_id)->first ();
        if (!$room) return Common::apiResponse (0,'not found',null,404);
        $room_admin = explode (',',$room->room_admin);
        $admins = User::query ()->whereIn ('id',$room_admin)->get ();
        $ads = UserResource::collection ($admins);
        return Common::apiResponse (1,'',$ads,200);
    }

    //get_room_by_owner_id

    public function get_room_by_owner_id(Request $request){
        $request['show'] = true;
        $room = Room::where('uid',$request->owner_id)->first();
        if (!$room){
            return Common::apiResponse(0,'not found',null,404);
        }
        return Common::apiResponse (true,'',new RoomResource($room),200);
    }

    public function amIHaveRoom(Request $request){
        $room = Room::query ()->where ('uid',$request->user ()->id)->exists ();
        if ($room){
            return Common::apiResponse (1,'have a room',null,200);
        }
        return Common::apiResponse(0,'does not have a room',null,200);
    }




    private function updateRoomVisitors()
    {

    }

    public function enter_room2(Request $request)
    {
        $room_pass = $request['room_pass'];
        $owner_id  = $request['owner_id'];


    }

    public function calcTime($uid){

        // case 1 : up_mic and go_mic in the same day
        $timer = LiveTime::query ()->where ('uid',$uid)->where('end_time',null)->first ();
        if ($timer){
            $hours = round((time () - $timer->start_time)/(60*60),2);
            $timer->end_time = time ();
            $timer->hours = $hours;
            $timer->save ();
            $user_day = UserDay::where('user_id', $uid)->whereDate('created_at', today())->first();
            if (LiveTime::where ('uid',$uid)->whereDate('created_at',today ())->sum('hours') >= 2 && $user_day->days == 0) {
                $user_day->days = 1;
                $user_day->save();
            }
        }



//        $timer = LiveTime::query ()->where ('uid',$uid)->where('end_time',null)->first ();
//        if ($timer){
//            $hours = round((time () - $timer->start_time)/(60*60),2);
//            $timer->end_time = time ();
//            $timer->hours = $hours;
//            $d = LiveTime::query ()->where ('uid',$uid)->whereDate ('created_at',today ())->where ('days','>=',1)->exists ();
//            if (!$d){
//                if ($hours >= 1){
//                    $timer->days = 1;
//                }
//            }
//
//            $timer->save ();
//        }
    }


    //getRoomUsers
    public function getRoomUsers(Request $request){
        $uid = $request->owner_id;
        $roomAdmin=Room::query ()->where(['uid'=>$uid])->value('room_admin');

        $roomAdmin=explode(',',$roomAdmin);
        $admins=User::whereIn('id', $roomAdmin)->get();
        $admins = $admins->filter (function ($q){
            return !Common::hasInPack ($q->id,17,true);
        });
        $admin = [];
        foreach($admins as $k=>$v){
            $admin[$k]['id'] = @$v->id;
            $admin[$k]['nickname'] = @$v->nickname;
            $admin[$k]['avatar'] = @$v->profile->avatar;
            $admin[$k]['country'] = @$v->profile->country;
            $admin[$k]['is_admin'] = 1;
        }

        $roomVisitor=DB::table('rooms')->where(['uid'=>$uid])->value('room_visitor');
        $roomVisitor=explode(',',$roomVisitor);

        $roomVisitor=array_values(array_diff($roomVisitor,$roomAdmin));
        $visitors=User::query ()->whereIn('id', $roomVisitor)->get();
        $visitors = $visitors->filter (function ($q){
            return !Common::hasInPack ($q->id,17,true);
        });
        $visitor = [];
        foreach($visitors as $k=>$v){
            $visitor[$k]['id'] = @$v->id;
            $visitor[$k]['nickname'] = @$v->nickname;
            $visitor[$k]['avatar'] = @$v->profile->avatar;
            $visitor[$k]['country'] = @$v->profile->country;
            $visitor[$k]['is_admin'] = 0;
        }
        $res['room_id']=$uid;
        $res['owner']= new UserResource(User::find($uid));
        $res['admin']= UserResource::collection ($admins);//$admin;
        $res['visitors']=UserResource::collection ($visitors);//$visitor;
        return Common::apiResponse(1,'',$res);
    }


    // mic sequence list


    //kick out of the room


    //make favorite room
    public function room_mykeep(Request $request){
        $data = $request;
        $uid = $data['owner_id'];
        $user_id = $request->user ()->id;
        $mykeep_list = DB::table('users')->where('id',$user_id)->value('mykeep');
        $mykeep_arr=explode(",", $mykeep_list);
        if(in_array($uid, $mykeep_arr)) return Common::apiResponse(0,'Do not repeat favorites',null,444);

        array_unshift($mykeep_arr,$uid);
        $str=trim(implode(",", $mykeep_arr),",");
        $res=DB::table('users')->where('id',$user_id)->update(['mykeep'=>$str]);
        if($res){
            return Common::apiResponse(1,'success');
        }else{
            return Common::apiResponse(0,'failed',null,400);
        }
    }


    //cancel favorite room
    public function remove_mykeep(Request $request){
        $data = $request;
        $uid = $data['owner_id'];
        $user_id = $request->user ()->id;
        $mykeep_list = DB::table('users')->where('id',$user_id)->value('mykeep');
        $mykeep_arr=explode(",", $mykeep_list);
        if(!in_array($uid, $mykeep_arr)) return Common::apiResponse(0,'This room has not been favorited',null,404);
        $key=array_search($uid,$mykeep_arr);
        unset($mykeep_arr[$key]);
        $str=trim(implode(",", $mykeep_arr),",");
        $res=DB::table('users')->where('id',$user_id)->update(['mykeep'=>$str]);
        if($res){
            return Common::apiResponse(1,'success');
        }else{
            return Common::apiResponse(0,'failed',null,400);
        }
    }


    //Whether to set a password
    public function is_pass(Request $request){
        $uid = $request->owner_id ? : 0;
        if(!$uid)   return Common::apiResponse(0,'invalid data');
        $result = DB::table('rooms')->where('uid',$uid)->value('room_pass');
        if($result){
            return Common::apiResponse(1,'The room has a password, please enter the password',['is_password'=>true]);
        }else{
            return Common::apiResponse(1,'room without password',['is_password'=>false]);
        }
    }

    //Get other users in the room
    public function get_other_user(Request $request){
        $data = $request;
        $uid = $data['owner_id'];
        $user_id = $data['user_id'];;
        $my_id = $request->user ()->id;

        $room_info = DB::table('rooms')->where('uid',$uid)->select(['room_admin','room_speak','room_judge','room_sound'])->get()->toArray ();
        $room_info[0] = (array)$room_info[0];
        $room_info[0]['user_type'] = 5;
        $roomAdmin = explode(',', $room_info[0]['room_admin']);
        for ($i=0; $i < count($roomAdmin); $i++) {
            if($roomAdmin[$i] == $user_id){
                $room_info[0]['user_type'] = 2;
            }
        }
        $roomJudge = explode(',', $room_info[0]['room_judge']);
        for ($i=0; $i < count($roomJudge); $i++) {
            if($roomJudge[$i] == $user_id){
                $room_info[0]['user_type'] = 4;
            }
        }
        $room_info[0]['is_speak'] = 1;
        $is_speak = explode(',', $room_info[0]['room_speak']);
        for ($i=0; $i < count($is_speak); $i++) {
            if($is_speak[$i] == $user_id){
                $room_info[0]['is_speak'] = 2;
            }
        }
        // $room_info[0]['is_sound'] = 1;
        // $is_sound = explode(',', $room_info[0]['roomSound']);
        // for ($i=0; $i < count($is_sound); $i++) {
        //     if($is_sound[$i] == $user_id){
        //         $room_info[0]['is_sound'] = 2;
        //     }
        // }

        $is_sound_arr =$room_info[0]['room_sound'] ? explode(',', $room_info[0]['room_sound']) : [];
        $room_info[0]['is_sound'] = in_array($user_id, $is_sound_arr) ? 2 : 1;



        $result = DB::table('users')->where('id',$user_id)->select(['id','nickname'])->get()->toArray ();

        $result[0] = (array)$result[0];

        $is_follows=Common::IsFollow($my_id,$user_id);

        $result[0]['is_follows'] = $is_follows ? 1 : 2;

        $user = User::find($result[0]['id']);

        $result[0]['image'] = @$user->profile->avatar;
        $result[0]['age'] = Common::getBrithdayMsg(@$user->profile->birthday,0)?:0;

        $result[0]['user_type'] = $room_info[0]['user_type'];
        $result[0]['is_speak'] = $room_info[0]['is_speak'];
        $result[0]['is_sound'] = $room_info[0]['is_sound'];


        $star_level=Common::getLevel($user_id,1);
        $gold_level=Common::getLevel($user_id,2);
        $vip_level=Common::getLevel($user_id,3);
        $star_img=DB::table('vips')->where('level',$star_level)->where('type',1)->value('img');
        $gold_img=DB::table('vips')->where('level',$gold_level)->where('type',2)->value('img');
        $vip_img=DB::table('vips')->where('level',$vip_level)->where('type',3)->value('img');
        $result[0]['star_img']=$star_img;
        $result[0]['gold_img']=$gold_img;
        $result[0]['vip_img']=$vip_img;

        $result[0]['is_time'] = 0;
        $info = Db::table('time_logs')->selectRaw('created_at,time')->where(array('uid'=>$uid,'user_id'=>$result[0]['id']))->orderByRaw('id desc')->limit(1)->first ();

        if (!empty($info) && $info['time'] && $info['created_at']){
            $endTime = ($info['time'] + $info['created_at']);
            $remainTime = ($endTime - time());
            $result[0]['is_time'] = $remainTime < 0 ? 0 : 1;
            //delete timer
            if ($remainTime < 0){
                Db::table('time_logs')->where(array('uid'=>$uid,'user_id'=>$result[0]['id']))->delete();
            }
        }






        if($result){
            return Common::apiResponse(1,'success',$result);
        }else{
            return Common::apiResponse(0,'failed',null,400);
        }

    }


    //can you speak
    public function not_speak_status(){
        $uid =  input('uid/d',0);
        $user_id = $this->user_id;
        if(!$uid)   $this->ApiReturn(0,'缺少参数');
        $roomSpeak = DB::name('rooms')->where('uid',$uid)->value('roomSpeak');
        $spe_arr = !$roomSpeak ? [] : explode(',', $roomSpeak);

        $is_speak = 1;
        foreach ($spe_arr as $k => &$v) {
            $arr=explode("#",$v);
            $new_time=$arr[1] + 180;
            if( time() - $new_time   < 0){
                if($arr[0] == $user_id){
                    $is_speak = 0;
                }
            }else{
                unset($spe_arr[$k]);
            }
        }
        $str=trim(implode(",", $spe_arr),",");
        DB::name('rooms')->where(['uid'=>$uid])->update(['roomSpeak'=>$str]);

        if($is_speak){
            $this->ApiReturn(1,'可以发言');
        }else{
            $this->ApiReturn(0,'不能发言');
        }
    }


    //Room background list
    public function room_background(){
        $data=DB::table('backgrounds')->where(['enable'=>1])->selectRaw('id,img')->get();
        return Common::apiResponse (1,'',$data);
    }

    public function room_type(){
        $data=DB::table('room_categories')->where(['pid'=>0,'enable'=>1])->selectRaw("id,name")->get();
        return Common::apiResponse(1,'',$data);
    }


    //set as admin
    public function is_admin(Request $request)
    {
        $roomOwnerId = $request->owner_id;
        $admin_id    = $request->user_id;
        if ($request->user()->id != $roomOwnerId) {
            return Common::apiResponse(0, 'not allowed', null, 403);
        }
        if (!$roomOwnerId || !$admin_id) return Common::apiResponse(0, 'invalid data', null, 422);
        if ($roomOwnerId == $admin_id) return Common::apiResponse(0, 'invalid data', null, 422);
        // search with owner id in room
        $room = Room::query()->where('uid', $roomOwnerId)->first();
        if (!$room) return Common::apiResponse(0, 'Room not exist', null, 422);

        $roomVisitor = $room->room_visitor;
        $vis_arr     = !$roomVisitor ? [] : explode(",", $roomVisitor);
        if (!in_array($admin_id, $vis_arr)) return Common::apiResponse(0, 'This user is not in this room', null, 404);

        $roomAdmin = $room->room_admin;
        $roomMax   = $room->max_admin;
        $adm_arr   = ($roomAdmin == '') ? [] : explode(",", $roomAdmin);
        $adm_arr   = array_unique($adm_arr);

        if (in_array($admin_id, $adm_arr)) return Common::apiResponse(0, 'This user is already an administrator, please do not repeat the settings', null, 444);
        if (count($adm_arr) >= 15) return Common::apiResponse(0, 'room manager is full', null, 403);
        if (count($adm_arr) >= $roomMax) return Common::apiResponse(0, 'room manager is full', null, 403);

        $adm_arr[] = $admin_id;
        $str       = trim(implode(",", trim($adm_arr)));

        //update room admins
        $room->room_admin = $str;
        $res              = $room->save();

        //        $res=DB::table('rooms')->where(['uid'=>$roomOwnerId])->update(['room_admin'=>$str]);
        $rid = $room->id;
        $ms  = [
            'messageContent' => [
                'message' => 'updateAdmins',
                'admins'  => $adm_arr
            ]
        ];
        Common::sendToZego('SendCustomCommand', $rid, $roomOwnerId, json_encode($ms));
        $a = User::query()->select(['id', 'name'])->find($admin_id);
        $ownerName = DB::table('users')->where('id', $roomOwnerId)->value('name') ?? '';
        $n = 'nan';
        if ($a) {
            $n = $a->name ?: 'nan';
        }
        Common::sendToZego_2('SendBroadcastMessage', $rid, $roomOwnerId, $ownerName, " اصبح ادمن $n");
        if ($res) {
            return Common::apiResponse(1, 'Set administrator successfully', $adm_arr, 200);
        } else {
            return Common::apiResponse(0, 'Failed to set administrator', null, 400);
        }
    }

    //cancel manager
    public function remove_admin(Request $request){
        $uid=$request->owner_id;
        $admin_id=$request->user_id;
        if ($request->user ()->id != $uid){
            return Common::apiResponse(0,'not allowed',null,403);
        }
        if(!$uid || !$admin_id)  return Common::apiResponse(0,'invalid data',null,422);
        $roomAdmin = DB::table('rooms')->where('uid',$uid)->value('room_admin');
        $adm_arr= !$roomAdmin ? [] : explode(",", $roomAdmin);
        if(!in_array($admin_id, $adm_arr))   return Common::apiResponse(0,'This user is not an administrator of this room',null,404);
        $key=array_search($admin_id,$adm_arr);
        unset($adm_arr[$key]);
        $str=implode(",", $adm_arr);
        $res=DB::table('rooms')->where(['uid'=>$uid])->update(['room_admin'=>$str]);
        $rid=DB::table('rooms')->where(['uid'=>$uid])->value('id');
        $ms = [
            'messageContent'=>[
                'message'=>'updateAdmins',
                'admins'=>array_values($adm_arr)
            ]
        ];
        $resu = Common::sendToZego ('SendCustomCommand',$rid,$uid,json_encode ($ms));
        if($res){
            return Common::apiResponse(1,'Cancel administrator successfully',$adm_arr,200);
        }else{
            return Common::apiResponse(0,'Failed to cancel administrator',null,400);
        }
    }

    //add ban
    public function is_black(Request $request){
        $uid=$request->owner_id;
        $user_id=$request->user_id;
        if (Common::hasInPack ($user_id,15)){
            return Common::apiResponse(0,'user cannt baned',null,403);
        }
        if(!$uid || !$user_id) return Common::apiResponse(0,'invalid data',null,422);
        if($uid == $user_id)    return Common::apiResponse(0,'Illegal operation',null,403);
//        if ($request->user ()->id != $uid){
//            return Common::apiResponse(0,'not allowed');
//        }
        $roomVisitor=DB::table('rooms')->where('uid',$uid)->value('room_visitor');
        $room = Room::query ()->where('uid',$uid)->first();
        $vis_arr= !$roomVisitor ? [] : explode(",", $roomVisitor);
        if(!in_array($user_id, $vis_arr))   return Common::apiResponse(0,'This user is not in this room',null,404);


        $roomSpeak=DB::table('rooms')->where('uid',$uid)->value('room_speak');
        $spe_arr= !$roomSpeak ? [] : explode(",", $roomSpeak);
        foreach ($spe_arr as $k => &$v) {
            $arr=explode("#",$v);
            if($arr[0] == $user_id) return Common::apiResponse(0,'This user is already on the ban list',null,405);
        }
        $shic=time() + 18000;
        $jinyan=$user_id."#".$shic;
        $spe_arr=array_merge($spe_arr,[$jinyan]);
        $str=implode(",", $spe_arr);
        $res=DB::table('rooms')->where(['uid'=>$uid])->update(['room_speak'=>$str]);
        if($res){
            $ms = [
                'messageContent'=>[
                    'message'=>'banFromWriting',
                    'userId'=>$user_id
                ]
            ];
            Common::sendToZego ('SendCustomCommand',$room->id,$uid,json_encode ($ms));
            return Common::apiResponse(1,'Succeeded adding writing ban for');
        }else{
            return Common::apiResponse(0,'Failed to add writing ban',null,400);
        }
    }

    public function removeBan(Request $request){
        $uid=$request->owner_id;
        $user_id=$request->user_id;

        if(!$uid || !$user_id) return Common::apiResponse(0,'invalid data',null,422);
        if($uid == $user_id)    return Common::apiResponse(0,'Illegal operation',null,403);

        $room = Room::query ()->where('uid',$uid)->first();

        $roomSpeak=DB::table('rooms')->where('uid',$uid)->value('room_speak');
        $spe_arr= !$roomSpeak ? [] : explode(",", $roomSpeak);
        foreach ($spe_arr as $k => &$v) {
            $arr=explode("#",$v);
            if($arr[0] == $user_id) {
                unset($spe_arr[$k]);
            }
        }
        $str=implode(",", $spe_arr);
        $res=DB::table('rooms')->where(['uid'=>$uid])->update(['room_speak'=>$str]);
        if($res){
            $ms = [
                'messageContent'=>[
                    'message'=>'removeBanFromWriting',
                    'userId'=>$user_id
                ]
            ];
            Common::sendToZego ('SendCustomCommand',$room->id,$uid,json_encode ($ms));
            return Common::apiResponse(1,'Succeeded remove writing ban for');
        }else{
            return Common::apiResponse(0,'Failed to remove writing ban',null,400);
        }
    }

    public function removeRoomPass(Request $request){
        $room = Room::query ()->where ('uid',$request->owner_id)->first ();
        if ($room){
            $room->room_pass = '';
            $room->save ();
        }
        return Common::apiResponse(1,'success');
    }


    public function createPK(Request $request){
        if (!$request->owner_id) return Common::apiResponse (0,'missing params',null,422);
        $room = Room::query ()->where ('uid',$request->owner_id)->where ('room_status',1)->first ();
        if (!$room) return Common::apiResponse (0,'not found',null,404);
        if (!$room->is_afk && $room->room_visitor = '') return Common::apiResponse (0,'room closed',null,403);
        $ex = Pk::query ()->where ('room_id',$room->id)->where ('status',1)->exists ();
        if ($ex) return Common::apiResponse (0,'already exists',null,405);
        Pk::query ()->create (
            [
                'room_id'=>$room->id,
                'status'=>1,
                'mics'=>$room->microphone,
//                'prize_value'=>$request->prize_value,
                'start_at'=>Carbon::now (),
                'end_at'=>Carbon::now ()->addMinutes ($request->minutes),
            ]
        );
        $mc = [
            'messageContent'=>[
                'message'=>'startPK',
                'PkTime'=>$request->minutes
            ]
        ];
        $json = json_encode ($mc);
        Common::sendToZego ('SendCustomCommand',$room->id,$request->user ()->id,$json);
        return Common::apiResponse (1,'created',null,201);
    }

    public function closePK(Request $request){
        $rooms = Room::query ()->get();
        if(!empty($request->owner_id)){
            $rooms = $rooms->where ('uid',$request->owner_id);
        }
        $roomsIds = $rooms->pluck('id');
        $pks = Pk::query ()->whereIn('room_id',$roomsIds)->where ('status',1)->get();
        foreach($pks as $pk){
            Pk::query ()->where ('id',$pk->id)->where ('status',1)->update (['status'=>0]);
            if ($pk->t1_score > $pk->t2_score){
                $winner = 1;
            }elseif ($pk->t2_score > $pk->t1_score){
                $winner = 2;
            }else{
                $winner = 0;
            }
            $pk->winner = $winner;
            $pk->save ();

            $mc = [
                'messageContent'=>[
                    'message'=>'closePk',
                    'scoreTeam1'=>$pk->t1_score,
                    'scoreTeam2'=>$pk->t2_score,
                    'percentagepk_team1'=>$pk->t1_per,
                    'percentagepk_team2'=>$pk->t2_per,
                    'winner_Team'=>$winner,
                ]
            ];
            $json = json_encode ($mc);
            Common::sendToZego ('SendCustomCommand',$pk->room_id,$request->user ()->id,$json);
        }
        return Common::apiResponse (1,'closed',null,201);
    }


    public function showPK(Request $request){
        if (!$request->owner_id) return Common::apiResponse (0,'missing params',null,422);
        $room = Room::query ()->where ('uid',$request->owner_id)->first ();
        if (!$room) return Common::apiResponse (0,'not found',null,404);
//        $pk = Pk::query ()->where ('room_id',$room->id)->where ('status',1)->first ();
//        if (!$pk) return Common::apiResponse (0,'not found',null,404);
//        $totalDuration = Carbon::parse($pk->start_at)->diffInSeconds($pk->end_at);
//        $t1p = 0;
//        $t2p = 0;
//        if (($pk->t1_score+$pk->t2_score) > 0){
//            $t1p = $pk->t1_score/($pk->t1_score+$pk->t2_score);
//            $t2p = $pk->t2_score/($pk->t1_score+$pk->t2_score);
//        }
        Pk::query ()->where ('room_id',$room->id)->update (['show_status'=>1]);
        $mc = [
            'messageContent'=>[
                'message'=>'showPK'
            ]
        ];
        $json = json_encode ($mc);
        Common::sendToZego ('SendCustomCommand',$room->id,$request->user ()->id,$json);
        return Common::apiResponse (1,'done',null,201);
    }


    public function firstOfRoom(Request $request){
        $uid = $request->owner_id;
        if (!$uid) return Common::apiResponse (0,'missing param',null,422);
        $gl = GiftLog::query()
            ->selectRaw('sender_id, SUM(giftNum * giftPrice) AS total')
            ->where('roomowner_id', $uid)
            ->groupBy('sender_id')
            ->orderByDesc('total')
            ->first();
        $fUser = User::query ()->find ($gl->sender_id);

        $ms = [
            'messageContent'=>[
                'message'=>'topSendGifts',
                'img'=>'user img',
                'id'=>'user id',
                'name'=>'user name'
            ]
        ];
        return Common::apiResponse (1,'',['user'=>new UserResource($fUser),'total'=>(integer)$gl->total],200);
    }

    public function roomMode(Request $request){
        $room = Room::query ()->where('uid',$request->owner_id)->first ();
        if (!$room) return Common::apiResponse (0,'not found',null,404);
        if ($room->mood == 1){
            $mode = 'party';
        }else{
            $mode = 'topCenter';
        }
        $ms = [
            'messageContent'=>[
                'message'=>'roomMode',
                'mode'=>$mode
            ]
        ];
        $json = json_encode ($ms);
        Common::sendToZego ('SendCustomCommand',$room->id,$request->user ()->id,$json);
        return Common::apiResponse (1,'done',null,201);
    }

    public function changeMode(Request $request){
        if ($request->mode == null || !$request->owner_id) return Common::apiResponse (0,'missing param',null,422);
        $room = Room::query ()->where('uid',$request->owner_id)->first ();
        if (!$room) return Common::apiResponse (0,'not found',null,404);
        $room->mode = $request->mode;
        $room->save ();
        if ($request->mode == '1'){
            $mode = 'party';
        }elseif ($request->mode == '2'){
            $mode = 'seats12';
        }
        else{
            $mode = 'topCenter';
        }
        $ms = [
            'messageContent'=>[
                'message'=>'roomMode',
                'mode'=>$mode
            ]
        ];
        $json = json_encode ($ms);
        Common::sendToZego ('SendCustomCommand',$room->id,$request->user ()->id,$json);
        return Common::apiResponse (1,'done',null,201);
    }

    public function RequestBackgroundImage(Request $request)
    {
        $user = $request->user();
        $costRequestBackGround = Common::getConfig('cost_request_backround');
        if(!$costRequestBackGround)
        {
            return Common::apiResponse (0,'not found params (cost_request_backround) in config dashboard',null,404);
        }
        if($user->di < $costRequestBackGround){
            return Common::apiResponse(0,'Insufficient balance, please go to recharge!',null,407);
        }
        $room = Room::query ()->where ('uid',$request->owner_id)->first ();
        if (!$room) //return Common::apiResponse (0,'not found',null,404);
        if ($request->image == null) return Common::apiResponse (0,'missing param',null,422);
        if ($request->hasFile ('image')){
            $img = $request->file ('image');
            $image = Common::upload ('images',$img);
            $RequestBackgroundImage = new RequestBackgroundImage();
            $RequestBackgroundImage->owner_room_id = $user->id;
            $RequestBackgroundImage->img = $image;
            $RequestBackgroundImage->status = 0;
            $RequestBackgroundImage->save();
            $user->di = ($user->di - $costRequestBackGround);
            $user->save();
            return Common::apiResponse (1,'done',null,201);
        }
        return Common::apiResponse (1,'faild',null,400);
    }



    
}
