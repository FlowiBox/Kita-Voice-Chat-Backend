<?php


namespace App\Traits\HelperTraits;


use App\Http\Resources\Api\V1\UserResource;
use App\Models\Mic;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait RoomTrait
{

    public static function get_room_users($owner_id,$user_id){
        $room  =DB::table('rooms')->where(['uid'=>$owner_id])->select('id','microphone','room_visitor')->first();
        if(!$room)   return __('room does not exist');
//        if($owner_id == $user_id)    return __('No operation authority for homeowners');

        $mic_arr=$room->microphone ? explode(',', $room->microphone) : [];
        foreach ($mic_arr as $k => &$v) {
            if($v == 0 || $v == -1 || $v == $owner_id)   unset($mic_arr[$k]);
        }
        $vis_arr=$room->room_visitor ? explode(',', $room->room_visitor) : [];
        if($user_id && !in_array($user_id,$vis_arr))    return __('User is not in this room');
        $sea_user=array();
        $mic_user=User::query ()->whereIn('id',$mic_arr)->with ('profile')->get ();
        foreach ($mic_user as $k => &$v){
            $v->is_mic=1;
            if($user_id == $v->id)  $sea_user[]=$v;
        }
        unset($v);


        //Arranging mic or ordering personnel
        $pm_arr=$paimai=$shiyin=[];
        $paimai_data=Mic::where('roomowner_id',$owner_id)->select('type','created_at','user_id','roomowner_id')->get ();
        $i=$j=0;
        foreach ($paimai_data as $k => &$v2) {
            $v2->id = $v2->user->id;
            $v2->is_mic=0;
            $v2->name = @$v2->user->name;
            $v2->avatar = @$v2->user->profile->avatar;

            if($v2->type==1){
                $i++;
                $v2->sort=$i;
                $paimai[]=$v2;
            }elseif($v2->type==2){
                $j++;
                $v2->sort=$j;
                $shiyin[]=$v2;
            }

            $pm_arr[]=$v2->user_id;
            if($user_id == $v2->user_id) $sea_user[]=$v2;
            unset($v2->user);
            unset($v2->user_id);
            unset($v2->roomowner_id);
        }
        unset($v2);


        //

        //people in the room
        $vis_arr=array_diff($vis_arr,$mic_arr);
        $vis_arr=array_diff($vis_arr,$pm_arr);
        $room_user=User::query ()->whereIn('id',$vis_arr)->get ();
        foreach ($room_user as $k1 => &$v1){
            $v1->is_mic=0;
            if($user_id == $v1->id) $sea_user[]=$v1;
        }

        unset($v1);



        $data['mic_users']= UserResource::collection ($mic_user) ;
//        $data['auction']= $paimai;
//        $data['audio']=  $shiyin ;
        $data['room_users']= UserResource::collection ($room_user);
//        $data['sea_users']= UserResource::collection ($sea_user);


        return $data;
    }


    //Get blacklist list
    public static function getUserBlackList($user_id = null) {
        if (!$user_id) return [];
        $ids = DB::table('black_lists')->where('user_id', $user_id)->where('status', 1)->pluck('from_uid')->toArray ();
        return $ids;
    }


    public static function userNowRoom($user_id = null)
    {
        if (!$user_id) {
            return false;
        }
        $is_afk = DB::table('rooms')->where('uid', $user_id)->value('is_afk');
        if ($is_afk) {
            return $user_id;
        }
        $uid = DB::table('rooms')->where('roomVisitor', 'like', '%' . $user_id . '%')->value('uid');
        return $uid ?: 0;
    }

    public static function userNowRooms($user_id = null)
    {
        if (!$user_id) {
            return false;
        }
        $is_afk = Room::query ()->where('uid', $user_id)->value('is_afk');
        if ($is_afk) {
            return $user_id;
        }
        $uid = Room::query ()->where('uid',$user_id)->value('uid');
        return $uid ?: 0;
    }

    public static function getRoomInfo($user_id = null){
        $room_id = self::userNowRooms ($user_id);
        if ($room_id) {
            $roomInfo = Room::query ()->select(['uid', 'room_name', 'hot','room_cover'])->where('uid', $room_id)->first ();
            $roomInfo['hot'] = self::room_hot($roomInfo['hot']);
            $roomInfo['room_name'] = urldecode($roomInfo['room_name']);
        } else {
            $roomInfo =(object)[];
        }
        return $roomInfo;
    }



    //exit room - perform action
    public static function quit_hand($uid,$user_id){
        $Visitor=DB::table('rooms')->where(['uid'=>$uid])->value('room_visitor');
        $room_visitor=explode(',', $Visitor);
        //homeowner exits room
        if($uid == $user_id){
            DB::table('rooms')->where('uid',$uid)->update(['is_afk'=>0]);
            // return $Visitor;
        }
        if( $uid != $user_id && !in_array($user_id, $room_visitor)){
            return $Visitor;
        }
        foreach ($room_visitor as $k => &$v) {
            if($user_id == $v){
                unset($room_visitor[$k]);
            }
        }
        $new_visitor=trim(implode(',', $room_visitor),',');
        DB::table('rooms')->where('uid',$uid)->update(['room_visitor'=>$new_visitor]);
        //mic
        self::go_microphone_hand($uid,$user_id);
        //Remove mic
        self::delMicHand($user_id);
//        self::updrycheck($user_id);
        return $new_visitor;
    }


    //Down the wheat - execute the operation
    public static function go_microphone_hand($uid,$user_id){
        $microphone = DB::table('rooms')->where('uid',$uid)->value('microphone');
        $microphone = explode(',', $microphone);
        if(!$microphone || !in_array($user_id, $microphone)){
            return 0;
        }
        for ($i=0; $i < count($microphone); $i++) {
            if($microphone[$i] == $user_id){
                $position = $i;
            }
        }
        $microphone[$position] = 0;
        $microphone = implode(',', $microphone);
        $result = DB::table('rooms')->where('uid',$uid)->update(['microphone'=>$microphone]);

        //clear timer
        Db::table('time_logs')->where(['uid'=>$uid,'user_id'=>$user_id])->delete();

        return $result;
    }

    //Remove mic discharge operation
    public static function delMicHand($user_id){
        $res=DB::table('mics')->where(['user_id'=>$user_id])->delete();
        return $res;
    }

    //In the online state, modify the user status
    public static function updrycheck($ry_uid)
    {
        import('RongCloud/RongCloud', VENDOR_PATH);
        $AppKey = self::getConf('ry_app_key');
        $AppSecret = self::getConf('ry_app_secret');
        $RongSDK = new \RongCloud\RongCloud($AppKey, $AppSecret);
        $user = [
            'id' => $ry_uid,
        ];
        $res = $RongSDK->getUser()->Onlinestatus()->check($user);
        $status = 0;
        if($res['code'] == 200){
            //Query whether the user exists
            $info = Db::table('users')->where(['id'=>$ry_uid])->first ();
            if ($info && $res['status'] == 1 && $res['status'] != $info['isOnline']){
                Db::table('users')->where(array('id'=>$ry_uid))->update(['isOnline'=>$res['status'],'online_time'=>time()]);
            }
            $status =  Db::table('users')->where(['id'=>$ry_uid])->value('isOnline');
        }

        return $status;
    }

}
