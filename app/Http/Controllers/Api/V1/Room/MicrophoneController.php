<?php

namespace App\Http\Controllers\Api\V1\Room;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\LiveTime;
use App\Models\Pk;
use App\Models\Room;
use App\Models\User;
use App\Models\UserDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MicrophoneController extends Controller
{

    public function microphone_status(Request $request){
        $uid = $request->owner_id;
        if(!$uid)   return Common::apiResponse(0,__('missing owner_id'),null,422);
        $room=(array)DB::table('rooms')->selectRaw("uid,microphone,is_prohibit_sound,room_sound,play_num")->where('uid',$uid)->first ();
        if(!$room)    return Common::apiResponse(0,__('room not found'),null,404);
        $microphone = explode(',', $room['microphone']);
        $is_prohibit_sound = explode(',', $room['is_prohibit_sound']);
        $roomSound_arr=explode(",", $room['room_sound']);
        $mic=[];

        foreach ($microphone as $k => &$v) {
            $ar=[];
//            $ar['remainTime'] = 0;
            foreach ($is_prohibit_sound as $ke => &$va) {
                if($k == $ke){
                    $ar['can_lock']  =   $va  ? 2 : 1;
                }
            }

            if($v == 0){
                $ar['status'] = 1;
            }elseif($v == -1){
                $ar['status'] = 3;
            }else{
                $ar['status'] = 2;
                $user=(array)DB::table('users')->selectRaw("id,nickname,dress_1,dress_4")->find($v);
                $ar['user_id']=$v;
                $ar['avatar']=@User::query ()->find ($v)->profile->avatar;
                $ar['nickname']=$user['nickname'];
                $ar['gender']=@User::query ()->find ($v)->profile->gender;
                if($user['dress_1']){
                    $txk=DB::table('wares')->where(['id'=>$user['dress_4']])->value('img1');
                    $ar['txk']=$txk;
                }else{
                    $ar['txk']='';
                }
                if($user['dress_4']){
                    $ar['mic_color']=DB::table('wares')->where(['id'=>$user['dress_4']])->value('color') ? : '#ffffff';
                }else{
                    $ar['mic_color']='#ffffff';
                }

                //numerical play
                $ar['is_play']=$room['play_num'];
                if($room['play_num']){
                    $ar['price'] = DB::table('play_num_logs')->where(['uid'=>$uid,'user_id'=>$v])->value('price') ? : 0;
                }else{
                    $ar['price'] = 0;
                }
                $ar['is_master']= $uid == $v ? 1 : 0;

                //countdown time
                $info = (array)Db::table('time_logs')->selectRaw('created_at,time')->where(array('uid'=>$uid,'muid'=>$v))->orderByRaw('id desc')->limit(1)->first();
                if (!empty($info) && $info['time'] && $info['created_at']){
                    $endTime = ($info['time'] + $info['created_at']);
                    $remainTime = ($endTime - time());
                    $ar['remainTime'] = $remainTime <= 0 ? 0 : (string)$remainTime;
                    if ($ar['remainTime'] <= 0){
                        Db::table('time_log')->where(array('uid'=>$uid,'muid'=>$v))->delete();
                    }
                    //if ($v == '1100001'){
                    //}
                    //删除计时时间
                    // if ($ar['remainTime'] == 0){
                    //    //Db::name('time_log')->where(array('uid'=>$uid,'muid'=>$uid))->delete();
                    // }
                }
            }
            $ar['is_muted'] = in_array($v,$roomSound_arr) ? 2 : 1;
            $mic[]=$ar;
        }
        $wait_user_id=DB::table('mics')->where(['roomowner_id'=>$uid,'type'=>1])->orderBy('id','asc')->limit(1)->value('user_id');
        $arr['user_id'] = !$wait_user_id ? '' : $wait_user_id;
        $arr['microphone']=$mic;
        return Common::apiResponse(1,'',$arr);
    }


    // on the mic
    public function up_microphone(Request $request){
        $data = $request;
        $user_id= $request->user_id;
        $phase=$request->phase;
        if(!$data['owner_id'] || !$user_id) return Common::apiResponse(0,__('Missing data'),null,422);
        $room=(array)DB::table('rooms')->where(['uid'=>$data['owner_id']])->selectRaw('id,room_visitor,room_admin,microphone,free_mic,mode')->first();
        if(!$room)  return Common::apiResponse(0,__('room does not exist'));
        $vis_arr= !$room['room_visitor'] ? [] : explode(",", $room['room_visitor']);
        if(!in_array($user_id, $vis_arr) && $data['owner_id'] != $user_id)   return Common::apiResponse(0,__('The user is not in this room'),null,403);

        $position = $data['position'];//mic sequence 0-8
        if ($room['mode'] != '1'){
            if($position <0 || $position >9) return Common::apiResponse(0,__('position error'),null,422);
        }else{
            if($position <0 || $position >17) return Common::apiResponse(0,__('position error'),null,422);
        }
        $mic_arr=explode(',', $room['microphone']);
        //if(@$mic_arr[$position] == -1)   return Common::apiResponse(0,__('This slot has been locked'),null,408);
        //if(@$mic_arr[$position] != 0)   return Common::apiResponse(0,__('There is a user on the mic'),null,405);


        //How to play free mic
        $adm_id=$request->user ()->id;
        if($room['free_mic'] == 1 && $adm_id != $data['owner_id']){
            $adm_arr= $room['room_admin'] ? explode(",", $room['room_admin']) : [$data['owner_id']];
            if(!in_array($adm_id, $vis_arr))    return Common::apiResponse(0,__('Please enter this room first'),null,403);
            if(!in_array($adm_id, $adm_arr))    return Common::apiResponse(0,__('You do not have this permission yet'),null,408);
        }


        //If it is on the mic, skip to the top mic, and the original mic is empty
        if(in_array($user_id, $mic_arr)){
            $key=array_search($user_id,$mic_arr);
            $mic_arr[$key]=0;
        }

        $arr=$mic_arr;


        if($phase < 4)  $arr[]=$data['owner_id'];
        $cp_arr=[];
        foreach ($arr as $k => &$v) {
            if($v == -1 || $v == 0) continue;
            $cp_id=Common::check_first_cp($user_id,$v,1);
            if($cp_id){
                $level=Common::getLevel($v,3);
                $ar['cp_level']=Common::getCpLevel($cp_id);
                $ar['nick_color'] = Common::getNickColorByVip($level);
                $ar['id']=$v;
                $ar['nickname']=DB::table('users')->where(['id'=>$v])->value('nickname');
                $ar['exp']=DB::table('cp')->where(['id'=>$cp_id])->value('exp');
                $img=@User::query ()->find ($v)->profile->avatar;
                $ar['img']=$img;
                $cp_arr[]=$ar;
            }
        }
        if($cp_arr){
            array_multisort(array_column($cp_arr,'exp'),SORT_DESC,$cp_arr);
        }
        $cp_xssm=Common::getConf('cp_xssm');
        $i=0;
        foreach ($cp_arr as $k => &$va) {
            if(!$i){
                $va['cp_xssm']= $va['cp_level'] >= 7 ? $cp_xssm : '';
            }else{
                $va['cp_xssm']='';
            }
            $i++;
        }
        if (@$mic_arr[$position] || @$mic_arr[$position] == false){
            $mic_arr[$position]=$user_id;
        }
        $mic=implode(',', $mic_arr);
        $res = DB::table('rooms')->where('uid',$data['owner_id'])->update(['microphone'=>$mic]);
        $room = Room::query ()->where ('uid',$data['owner_id'])->first ();
        $pk = Pk::query ()->where ('room_id',$room->id)->where ('status',1)->first ();
        if ($pk){
            $pk->mics = $mic;
            $pk->save ();
        }

        $user=(array)DB::table('users')->selectRaw('id,nickname')->find($user_id);
        $u = User::query ()->find ($user_id);
        $user['avatar']=@$u->profile->avatar;
        $user_level=Common::getLevel($user_id,3);
        $user['nick_color']=Common::getNickColorByVip($user_level);
        $res_arr['cp']=$cp_arr;
        $res_arr['user']=$user;

        if($res){

            //Remove mic sequence
            Common::delMicHand($user_id);

            $user_day = UserDay::where('user_id', $user_id)->whereDate('created_at', today())->first();
            if (!$user_day) {
                UserDay::create([
                    'user_id'        => $user_id,
                ]);
            }
            LiveTime::query ()->where ('uid',$user_id)->where('end_time',null)->whereDate ('created_at','!=',today ())->delete ();
            $t = LiveTime::query ()->where ('uid',$user_id)
                ->where('end_time',null)
                ->whereDate ('created_at',today ())
                ->orderByDesc ('id')
                ->first ();
            if($t){
                LiveTime::query ()->where ('uid',$user_id)->where('end_time',null)->where ('id','!=',$t->id)->delete ();
            }

            if(!$t){
                LiveTime::query ()->create (
                    [
                        'uid'=>$user_id,
                        'start_time'=>time ()
                    ]
                );
            }

            $ms = [
                'messageContent'=>[
                    'message'=>'upMic',
                    'userId'=>$user_id,
                    'position'=>$position,
                    'userName'=>@$u->name
                ]
            ];
            $json = json_encode ($ms);
            Common::sendToZego ('SendCustomCommand',$room->id,$user_id,$json);
            return Common::apiResponse(1,__('Success on the mic'),$res_arr);
        }else{
            return Common::apiResponse(0,__('Failed to mic'),null,400);
        }
    }

    //leave mic
    public function go_microphone(Request $request){
        $data = $request;
        $this->calcTime($data['user_id']);
        $result=Common::go_microphone_hand($data['owner_id'],$data['user_id']);
        $room = Room::query ()->where ('uid',$data['owner_id'])->first ();
        if (!$room) return Common::apiResponse (0,'room not found',null,404);
        if($result){
            $this->calcTime($data['user_id']);
            return Common::apiResponse(1,__('Success'));
        }else{
            return Common::apiResponse(0,__('Failed'),null,400);
        }
    }


    //mute mic place
    public function mute_microphone(Request $request)
    {
        $data = $request;
        $position = $data['position'];
        $room = Room::query ()->where ('uid',$data['owner_id'])->first ();
        if (@$room->mode != '1'){
            if($position <0 || $position >9) return Common::apiResponse(0,__('position error'),null,422);
        }else{
            if($position <0 || $position >17) return Common::apiResponse(0,__('position error'),null,422);
        }
        $admins = Room::query ()->where ('uid',$data['owner_id'])->value ('room_admin');
        $admins = explode (',',$admins);
        if($request->user ()->id != $data['owner_id'] && !in_array ($request->user ()->id,$admins) ) {
            return Common::apiResponse(0,__('you dont have permission'),null,408);
        }

        $microphone = DB::table('rooms')->where('uid',$data['owner_id'])->value('microphone');
        $microphone = explode(',', $microphone);
        if (@$microphone[$position]){
            $microphone[$position] = -2;
        }
        $microphone = implode(',', $microphone);
        $res = DB::table('rooms')->where('uid',$data['owner_id'])->update(['microphone'=>$microphone]);
        if(true){
            $ms = [
                'messageContent'=>[
                    'message'=>'muteMic',
                    'userId'=>$request->user ()->id,
                    'position'=>$data['position']
                ]
            ];
            $json = json_encode ($ms);
            Common::sendToZego ('SendCustomCommand',$room->id,$request->user ()->id,$json);
            return Common::apiResponse(1,__('Successfully locked the microphone position'));
        }else{
            return Common::apiResponse(0,__('Failed to lock microphone'),null,400);
        }
    }

    //unmute mic place
    public function unmute_microphone(Request $request)
    {
        $data = $request;
        $position = $data['position'];
        $room = Room::query ()->where ('uid',$data['owner_id'])->first ();
        if (@$room->mode != '1'){
            if($position <0 || $position >9) return Common::apiResponse(0,__('position error'),null,422);
        }else{
            if($position <0 || $position >17) return Common::apiResponse(0,__('position error'),null,422);
        }
        $admins = Room::query ()->where ('uid',$data['owner_id'])->value ('room_admin');
        $admins = explode (',',$admins);
        if($request->user ()->id != $data['owner_id'] && !in_array ($request->user ()->id,$admins) ) {
            return Common::apiResponse(0,__('you dont have permission'),null,408);
        }
        $microphone = DB::table('rooms')->where('uid',$data['owner_id'])->value('microphone');
        $microphone = explode(',', $microphone);
        if (@$microphone[$position]){
            $microphone[$position] = 0;
        }
        $microphone = implode(',', $microphone);
        $res = DB::table('rooms')->where('uid',$data['owner_id'])->update(['microphone'=>$microphone]);
        if(true){
            $room = Room::query ()->where ('uid',$data['owner_id'])->first ();
            $ms = [
                'messageContent'=>[
                    'message'=>'unmuteMic',
                    'userId'=>$request->user ()->id,
                    'position'=>$data['position']
                ]
            ];
            $json = json_encode ($ms);
            Common::sendToZego ('SendCustomCommand',$room->id,$request->user ()->id,$json);
            return Common::apiResponse(1,__('Successfully unlocked the microphone'));
        }else{
            return Common::apiResponse(0,__('Failed to unlock microphone'),null,400);
        }
    }

    //lock mic place
    public function shut_microphone(Request $request)
    {
        $data = $request;
        $position = $data['position'];
        $room = Room::query ()->where ('uid',$data['owner_id'])->first ();
        if (@$room->mode != '1'){
            if($position <0 || $position >9) return Common::apiResponse(0,__('position error'),null,422);
        }else{
            if($position <0 || $position >17) return Common::apiResponse(0,__('position error'),null,422);
        }
        $admins = Room::query ()->where ('uid',$data['owner_id'])->value ('room_admin');
        $admins = explode (',',$admins);
        if($request->user ()->id != $data['owner_id'] && !in_array ($request->user ()->id,$admins) ) {
            return Common::apiResponse(0,__('you dont have permission'),null,408);
        }

        $microphone = DB::table('rooms')->where('uid',$data['owner_id'])->value('microphone');
        $microphone = explode(',', $microphone);
        if (@$microphone[$position] == false){
            $microphone[$position] = -1;
        }
        $microphone = implode(',', $microphone);
        $res = DB::table('rooms')->where('uid',$data['owner_id'])->update(['microphone'=>$microphone]);
        if($res){
            $ms = [
                'messageContent'=>[
                    'message'=>'lockMic',
                    'userId'=>$request->user ()->id,
                    'position'=>$data['position']
                ]
            ];
            $json = json_encode ($ms);
            Common::sendToZego ('SendCustomCommand',$room->id,$request->user ()->id,$json);
            return Common::apiResponse(1,__('Successfully locked the microphone position'));
        }else{
            return Common::apiResponse(0,__('Failed to lock microphone'),null,400);
        }
    }


    //open mic place
    public function open_microphone(Request $request)
    {
        $data = $request;
        $position = $data['position'];
        $room = Room::query ()->where ('uid',$data['owner_id'])->first ();
        if (@$room->mode != '1'){
            if($position <0 || $position >9) return Common::apiResponse(0,__('position error'),null,422);
        }else{
            if($position <0 || $position >17) return Common::apiResponse(0,__('position error'),null,422);
        }
        $admins = Room::query ()->where ('uid',$data['owner_id'])->value ('room_admin');
        $admins = explode (',',$admins);
        if($request->user ()->id != $data['owner_id'] && !in_array ($request->user ()->id,$admins) ) {
            return Common::apiResponse(0,__('you dont have permission'),null,408);
        }
        $microphone = DB::table('rooms')->where('uid',$data['owner_id'])->value('microphone');
        $microphone = explode(',', $microphone);
        if (@$microphone[$position]){
            $microphone[$position] = 0;
        }
        $microphone = implode(',', $microphone);
        $res = DB::table('rooms')->where('uid',$data['owner_id'])->update(['microphone'=>$microphone]);
        if(true){
            $room = Room::query ()->where ('uid',$data['owner_id'])->first ();
            $ms = [
                'messageContent'=>[
                    'message'=>'unLockMic',
                    'userId'=>$request->user ()->id,
                    'position'=>$data['position']
                ]
            ];
            $json = json_encode ($ms);
            Common::sendToZego ('SendCustomCommand',$room->id,$request->user ()->id,$json);
            return Common::apiResponse(1,__('Successfully unlocked the microphone'));
        }else{
            return Common::apiResponse(0,__('Failed to unlock microphone'),null,400);
        }
    }


    //Turn off user microphone
    public function is_sound(Request $request){
        $user_id = $request->user_id ? : 0;
        $uid = $request->owner_id ? : 0;
        if(!$uid || !$user_id)  return Common::apiResponse (0,__ ('require user_id and owner_id'),null,422);
        $admins = Room::query ()->where ('uid',$uid)->value ('room_admin');
        $admins = explode (',',$admins);
        if($request->user ()->id != $uid && !in_array ($request->user ()->id,$admins) ) {
            return Common::apiResponse(0,__('you dont have permission'),null,408);
        }
        $sound = DB::table('rooms')->where('uid',$uid)->value('room_sound');
        $sound_arr=explode(',', $sound);
        if(in_array($user_id , $sound_arr)) return Common::apiResponse (0,__ ('The user is already muted, please do not repeat the settings'),null,444);

        array_push($sound_arr,$user_id);
        $str=implode(',', $sound_arr);
        $res = DB::table('rooms')->where('uid',$uid)->update(['room_sound'=>$str]);
        if($res){
            $room = Room::query ()->where ('uid',$uid)->first ();
            $ms = [
                'messageContent'=>[
                    'message'=>'muteMic',
                    'userId'=>$user_id,
                ]
            ];
            $json = json_encode ($ms);
            Common::sendToZego ('SendCustomCommand',$room->id,$user_id,$json);
            return Common::apiResponse(1,__('Successfully muted'));
        }else{
            return Common::apiResponse(0,__('Failed to mute'),null,400);
        }
    }

    //Open user voice microphone
    public function remove_sound(Request $request){
        $user_id = $request->user_id ? : 0;
        $uid = $request->owner_id ? : 0;
        if(!$uid || !$user_id)  return Common::apiResponse (0,__ ('require user_id and owner_id'),null,422);
        $admins = Room::query ()->where ('uid',$uid)->value ('room_admin');
        $admins = explode (',',$admins);
        if($request->user ()->id != $uid && !in_array ($request->user ()->id,$admins) ) {
            return Common::apiResponse(0,__('you dont have permission'));
        }
        $sound = DB::table('rooms')->where('uid',$uid)->value('room_sound');
        $sound_arr=explode(',', $sound);
        if(!in_array($user_id , $sound_arr))  return Common::apiResponse(0,__('The user is no longer in the ban list, please do not repeat the settings'),null,444);
        $key = array_search($user_id,$sound_arr);
        unset($sound_arr[$key]);
        $sound = implode(',', $sound_arr);
        $res = DB::table('rooms')->where('uid',$uid)->update(['room_sound'=>$sound]);
        if($res){
            $room = Room::query ()->where ('uid',$uid)->first ();
            $ms = [
                'messageContent'=>[
                    'message'=>'UnMuteMic',
                    'userId'=>$user_id,
                ]
            ];
            $json = json_encode ($ms);
            Common::sendToZego ('SendCustomCommand',$room->id,$user_id,$json);
            return Common::apiResponse(1,__('Successfully unmuted'));
        }else{
            return Common::apiResponse(0,__('Unmute failed'),null,400);
        }
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
            dd($user_day, LiveTime::where ('uid',$uid)->whereDate('created_at',today ())->get());
            if (LiveTime::where ('uid',$uid)->whereDate('created_at',today ())->sum('hours') >= 0.1 && $user_day->day == 0) {
                $user_day->day = 1;
                $user_day->save();
            }

//            $d = LiveTime::query ()->where ('uid',$uid)->whereDate ('created_at',today ())->where ('days','>=',1)->exists ();
//            if (!$d){
//                if ($hours >= 1){
//                    $timer->days = 1;
//                }
//            }
        }
    }

}
