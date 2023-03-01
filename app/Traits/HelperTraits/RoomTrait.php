<?php


namespace App\Traits\HelperTraits;


use App\Http\Resources\Api\V1\UserResource;
use App\Models\Mic;
use App\Models\Pk;
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
        $uid = DB::table('rooms')->where('room_visitor', 'like', '%' . $user_id . '%')->value('uid');
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
        $room = Room::query ()->where ('uid',$uid)->first ();
        $pk = Pk::query ()->where ('room_id',$room->id)->where ('status',1)->first ();
        if ($pk){
            $pk->mics = $microphone;
            $pk->save ();
        }
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



    public static function check_gap_hand($uid,$type = 1,$group = 'roomowner_id'){
        if ($type == 1) {
            $time = 'today';
        } elseif ($type == 2) {
            $time = 'week';
        } elseif ($type == 3) {
            $time = 'month';
        }
        $where['roomowner_id']= $uid;
        $exp = DB::table('gift_logs')->where($where)->whereTime('created_at', $time)->sum('giftPrice') ? : 0;

        $exp2 = (array)DB::table('gift_logs')->whereTime('created_at', $time)->selectRaw("sum(giftPrice) as exp")->groupByRaw($group)
            ->having("exp", ">",$exp)->orderBy("exp", "asc")->first ();
        $exp2 = $exp2 ? $exp2['exp'] : '';
        if(!$exp2){
            $data['gap'] = 0;
        }else{
            $cha = ($exp2 - $exp);
            $gap=self::room_hot($cha);
            $data['gap'] = $gap;
        }
        $data['exp'] = self::room_hot($exp);
        return $data;
    }

    public static function micSortHand($user_id = null ,$uid = null){
        $data=DB::table('mics','a')
            ->where(['a.roomowner_id'=>$uid])
            ->selectRaw('a.id,a.user_id,a.type')
            ->orderBy('a.id','asc')
            ->get();

        $i=$j=$sort=$shiyin_sort=0;
        foreach ($data as $k => &$v) {
            if($v->type==1){
                $i++;
                if($v->user_id == $user_id) $sort=$i;
            }elseif($v->type==2){
                $j++;
                if($v->user_id == $user_id) $shiyin_sort=$j;
            }

        }
        unset($v);
        $arr['sort']=$sort;
        $arr['num']=$i;

        $arr['audio_sort']=$shiyin_sort;
        $arr['audio_num']=$j;
        return $arr;
    }


    public static function roomDataFormat($data = array())
    {
        if (!$data) {
            return [];
        }
        foreach ($data as $k => &$v) {
            if (isset($v['openid'])) {
                $v['openid'] = !empty($v['openid']) ?: '';
            }
            $v['room_name'] = urldecode($v['room_name']);
            if (isset($v['hot'])) {
                $v['hot'] = self::room_hot($v['hot']);
            }
            if (isset($v['microphone'])) {
                $mic_arr = explode(',', $v['microphone']);
                $zc = array_pop($mic_arr);
                $v['host'] = $zc > 1000 ? self::getUserField($zc, 'nickname') : '';
            }
        }
        return $data;
    }


    //Get user information
    public static function getUserField($user_id=null,$field=null){
        if(!$user_id || !$field)    return '';
        $user = User::query ()->find ($user_id);
        if(in_array ($field,['avatar','gender','birthday','province','city','country'])){
            return @$user->profile->{$field};
        }
        $value=@$user->{$field};
        return $value ? : '';
    }



    //Get nickname color according to vip level
    public static function getNickColorByVip($level=0){
        $color = '#ffffff';
        if($level < 3){
            $color='#ffffff';
        }elseif($level>=3 && $level<7){
            $color='#93ffa5';
        }elseif($level>=7 && $level<11){
            $color='#8ce1fe';
        }elseif($level>=11 && $level<15){
            $color='#ffc6e1';
        }elseif($level>=15 && $level<18){
            $color='#e09dff';
        }elseif($level>=18 && $level<=20){
            $color='#fff585';
        }else{
            $color = '#000000';
        }
        return $color;
    }




    //Increase the accumulative value after opening the room mode. Added in the fourth phase
    public static function add_play_num($uid,$user_id,$price){
        $where['uid']=$uid;
        $where['user_id']=$user_id;
        $data=DB::table('play_num_logs')->where($where)->first();
        if(!$data){
            $info['uid']=$uid;
            $info['user_id']=$user_id;
            $info['price']=$price;
            DB::table('play_num_logs')->insertGetId($info);
        }else{
            DB::table('play_num_logs')->where($where)->increment('price',$price);
        }

    }






    public static function unlock_wares($user_id, $cp_id = null)
    {
        if ($cp_id) {
            $level = self::getCpLevel($cp_id);
            $get_type = 8;
        } else {
            $get_type = 1;
            $level = self::getLevel($user_id, 3);
        }
        $type = [4, 5, 6, 7, 8];
        foreach ($type as $k => &$v) {
            $where['get_type'] = $get_type;
            $where['enable'] = 1;
            $where['level'] = ['elt', $level];
            $where['type'] = $v;
            $wares = DB::table('wares')->where($where)->selectRaw('id,type,expire')->orderByRaw("id desc")->limit(1)->first();
            if (!$wares) {
                continue;
            }
            $where_pack['user_id'] = $user_id;
            $where_pack['get_type'] = $get_type;
            $where_pack['type'] = $v;
            $target_id = DB::table('pack')->where($where_pack)->value('target_id');
            if ($wares->id == $target_id) {
                continue;
            }
            if (!$target_id) {
                $arr['user_id'] = $user_id;
                $arr['get_type'] = $get_type;
                $arr['type'] = $v;
                $arr['target_id'] = $wares->id;
                $arr['addtime'] = time();
                $arr['expire'] = $wares['expire'] ? time() + $wares['expire'] * 86400 : 0;
                $res = Db::table('pack')->insert($arr);
            } else {
                $res = DB::table('pack')->where($where_pack)->update(['target_id' => $wares['id']]);
            }
            if ($res && in_array($v, [4, 5, 6, 7]) && $get_type == 1) {
                $dress = 'dress_' . $v;
                DB::table('users')->where(['id' => $user_id])->update([$dress => $wares['id']]);
            }
        }
    }


    public static function update_user_total($user_id = null, $type = null, $coins = null)
    {
        if (!$user_id || !$type || !$coins) {
            return false;
        }
        $data = DB::table('user_totals')->where(['user_id' => $user_id])->first();
        if ($type == 1) {
            DB::table('user_totals')->where(['user_id' => $user_id])->increment('room', $coins);
        } elseif ($type == 2) {
            DB::table('user_totals')->where(['user_id' => $user_id])->increment('send', $coins);
            $vip_level = self::getLevel_two($user_id, 3);
            $res = DB::table('user_totals')->where(['user_id' => $user_id])->update(['vip_level' => $vip_level]);
            if ($res) {
                self::add_user_official($user_id, 1, $vip_level);
            }
        } elseif ($type == 3) {
            DB::table('user_totals')->where(['user_id' => $user_id])->increment('gain', $coins);
        } elseif ($type == 4) {
            $cp_level = self::getUserMaxCpLevel($user_id, 'level');
            $res = DB::table('user_total')->where(['user_id' => $user_id])->update(['cp_level' => $cp_level]);
            if ($res) {
                self::add_user_official($user_id, 8, $cp_level);
            }
        } else {
            return false;
        }
    }

    public static function getLevel_two($user_id = null, $type = null, $is_img = null)
    {
        if (!$user_id || !$type) {
            return 0;
        }
        $gold_num = DB::table('user_totals')->where('user_id', $user_id)->value('send');
        $star_num = DB::table('user_totals')->where('user_id', $user_id)->value('gain');
        if ($type == 1) {
            $coins = $star_num;
        } elseif ($type == 2 || $type == 3) {
            $coins = $gold_num;
        } else {
            return 0;
        }
        if (!$coins && $is_img == 'img') {
            return '';
        }
        if (!$coins) {
            return 0;
        }
        $level = DB::table('vips')->where(['type' => $type])->where('mizuan', '<=', $coins)->orderBy('id', 'desc')->limit(1)->value('level');

        if ($is_img == 'img') {
            if ($level) {

                $img = DB::table('vips')->where(['level' => $level, 'type' => $type])->value('img');
                return $img;
            } else {
                return '';
            }
        } else {
            return $level;
        }
    }
    public static function getUserMaxCpLevel($user_id = null, $field = null)
    {
        if (!$user_id || !$field) {
            return 0;
        }
        $where['user_id|fromUid'] = $user_id;
        $where['status'] = 1;
        $cp = DB::table('cps')->where($where)->orderByRaw('exp desc')->limit(1)->first();
        if (!$cp) {
            return 0;
        }
        $exp = $cp->exp;
        $where_vip['type'] = 5;
        $where_vip['exp'] = ['elt', $exp];
        $level = DB::table('vips')->where($where_vip)->orderByRaw('id desc')->limit(1)->value('level');
        if ($field == 'level') {
            return $level;
        } else {
            return $cp[$field] ?: 0;
        }
    }

    public static function add_user_official($user_id, $get_type, $level)
    {
        if ($get_type == 1) {
            $where['type'] = 3;
            $class = "VIP";
        } elseif ($get_type == 8) {
            $where['type'] = 5;
            $class = "Guardian CP";
        } else {
            return false;
        }
        $where['level'] = $level;
        $where['enable'] = 1;
        $auth = DB::table('vip_auth')->where($where)->value('name');
        if (!$auth) {
            return false;
        }
        $content = "Congratulations," . $class . 'level reached' . $level . 'Level up, successfully unlocked' . $auth . 'privilege~';
        self::addOfficialMessage('', $user_id, $content);
    }

    protected static function addOfficialMessage($title = null , $user_id, $content)
    {
        $title = $title ?: 'system notification';
        $info['title'] = $title;
        $info['user_id'] = $user_id;
        $info['content'] = $content;
        $info['created_at'] = date('Y-m-d H:i:s', time());
        $res = DB::table('official_messages')->insertGetId($info);
        return $res;
    }

    public static function fin_task($user_id,$task_id){
        $task=Db::table('tasks')->where(['id'=>$task_id,'enable'=>1])->first();
        if(!$task)  return 0;
        $user_task=Db::table('user_tasks')->where(['user_id'=>$user_id])->first();
        if($task->type == 1 && !substr_count($user_task->not_fin_1,$task_id))   return 0;
        $field='fin_'.$task->type;
        $str=$user_task->{$field};
        $num=substr_count($str,$task_id);
        if($num == $task->num)    return 0;
        $str_arr=explode(',', $str);
        $str_arr[]=$task_id;
        $info[$field]=trim(implode(',', $str_arr),',');
        Db::table('user_tasks')->where(['user_id'=>$user_id])->update($info);
        return 1;
    }


    public static function can_kick($user_id){
        $vip_level      = self ::getLevel ( $user_id , 3 );
        $vip_auth = DB ::table ( 'vip_auth' ) -> where ( ['type' => 3 , 'enable' => 1] )->whereIn ('name',['ممنوع الطرد','not kicked']) ;
        $vip_auth = $vip_auth-> get ();
        $can_kick = true;
        foreach ($vip_auth as $k => &$v) {
            if ($vip_level >= $v->level){
                $can_kick = false;
            }
        }
        return $can_kick;
    }

    public static function increaseRoomSession($owner_id,$num){
        Room::query ()->where ('uid',$owner_id)->increment ('session',$num);
    }
}
