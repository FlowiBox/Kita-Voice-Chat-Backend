<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CacheHelper;
use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\EditRoomRequest;
use App\Http\Resources\Api\V1\RoomResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Background;
use App\Models\LiveTime;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\RoomView;
use App\Models\User;
use App\Repositories\Room\RoomRepo;
use App\Repositories\Room\RoomRepoInterface;
use App\Traits\HelperTraits\RoomTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{


    protected $repo;

    public function __construct (RoomRepoInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $result = $this->repo->all ($request);
        return Common::apiResponse (true,'',RoomResource::collection ($result),200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request['show']=true;
        $request['numid']=rand (111111,999999);
        $request['room_name']=rand (111111,999999);
        try {
            $room = Room::query ()->where('uid',$request->user ()->id)->first ();
            if ($room){
                return Common::apiResponse (true,'you are already have a room',new RoomResource($room),200);
            }
           $room = $this->repo->create (array_merge($request->all (),['uid'=>$request->user ()->id]));
            if ($request->hasFile ('room_cover')){
                $room->room_cover = Common::upload ('rooms',$request->file ('room_cover'));
                $room->save();
            }
            return Common::apiResponse (true,'created',new RoomResource($room),200);
        }catch (\Exception $exception){
            return Common::apiResponse (false,'failed',null,400);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request,$id)
    {
        $request['show'] = true;
        $room = Room::find($id);
        if (!$room){
           return Common::apiResponse(0,'not found',null,404);
        }
        return Common::apiResponse (true,'',new RoomResource($room),200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EditRoomRequest $request, $id)
    {



        try {
            $room = $this->repo->find ($id);
            if(!$room){
                return Common::apiResponse (false,'not found',null,404);
            }
            if ($room->uid != $request->user ()->id && !in_array ($request->user ()->id,explode (',',$room->room_admin))){
                return Common::apiResponse (false,'not allowed',null,403);
            }
            if ($request->room_name){
                $room->room_name = $request->room_name;
            }

            if ($request->hasFile ('room_cover')){
                $room->room_cover = Common::upload ('rooms',$request->file ('room_cover'));
            }

            if ($request->free_mic){
                $room->free_mic = $request->free_mic;
            }

            if ($request->room_intro){
                $room->room_intro = $request->room_intro;
            }

            if ($request->room_pass){
                $room->room_pass = $request->room_pass;
            }

            if ($request->room_type){
                if (!RoomCategory::query ()->where ('id',$request->room_type)->where ('enable',1)->exists ()) return Common::apiResponse (0,'type not found',null,404);
                $room->room_type = $request->room_type;
            }

            if ($request->room_class){
                if (!RoomCategory::query ()->where ('id',$request->room_class)->where ('enable',1)->exists ()) return Common::apiResponse (0,'class not found',null,404);
                $room->room_type = $request->room_type;
            }

            if ($request->room_background){
                if (!Background::query ()->where ('id',$request->room_background)->where ('enable',1)->exists ()){
                    return Common::apiResponse (0,'background not found',null,404);
                }
                $room->room_background = $request->room_background;

            }


//            $this->repo->save ($room);
            $room->save ();
            $request['owner_id'] = $room->uid;

            $data = [
                "messageContent"=>[
                    "message"=>"changeBackground",
                    "imgbackground"=>$room->room_background,
                    "roomIntro"=>$room->room_intro?:"",
                    "roomImg"=>$room->room_cover?:""
                ]
            ];
            $json = json_encode ($data);
            $res = Common::sendToZego ('SendCustomCommand',$room->id,$request->user ()->id,$json);
            $request->is_update = true;
            return $this->enter_room ($request);

        }catch (\Exception $exception){
            return Common::apiResponse (false,'failed',null,400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


//------------------------------------------------------------------ops----------------------------------------------------------------

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
            return Common::apiResponse (1,'have a room',null,201);
        }
        return Common::apiResponse(0,'does not have a room',null,404);
    }



    //enter the room
    public function enter_room(Request $request)
    {
        $room_pass = $request['room_pass'];
        $owner_id  = $request['owner_id'];
        $user_id   = $request->user ()->id;

//        if($owner_id == $user_id){
//            $res=DB::table('users')->where('id',$user_id)->value('is_idcard');
//            if(!$res)  return Common::apiResponse(false,'Please complete real-name authentication first');
//        }
        $black_list=Common::getUserBlackList($owner_id);
        if(in_array($user_id, $black_list)) return Common::apiResponse(false,__('You have been blocked by the other party'));
        $room_info = (array)DB::table('rooms','rooms')
            ->join('users as users','rooms.uid','=','users.id','left')
            ->join('room_categories as room_categories','rooms.room_type','=','room_categories.id','left')
            ->where('rooms.uid',$request['owner_id'])
            ->select(['rooms.id as id','rooms.numid as room_id_num','rooms.uid as owner_id','rooms.room_status','rooms.room_name',
                        'rooms.room_cover','room_categories.name','rooms.room_cover','rooms.room_intro',
                        'rooms.room_pass','rooms.room_type','rooms.hot','rooms.room_background','rooms.room_admin',
                        'rooms.room_speak','rooms.room_sound','rooms.microphone','rooms.room_judge','rooms.is_afk',
                        'users.nickname','rooms.room_visitor','rooms.play_num','rooms.free_mic','rooms.room_welcome'])
            ->first ();

        if(!$room_info) return Common::apiResponse (false,'No room yet, please create first',null,404);



        //exit the original room
        $room_id=Common::userNowRoom($user_id);
        if($room_id && $room_id != $owner_id){
            Common::quit_hand($room_id,$user_id);
        }

        if($room_info['room_status'] == 3) return Common::apiResponse(false,'The room has been banned, please contact customer service',null,408);
        //enter your room
        if($owner_id == $user_id){
            $res_afk=DB::table('rooms')->where('uid',$owner_id)->update(['is_afk'=>1]);
            if($res_afk)    $room_info['is_afk']=1;
        }
        //kicked out of the room
        $roomBlack = DB::table('rooms')->where('uid',$owner_id)->value('room_black');

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

//        $room_ban = DB::table('rooms')->where('uid',$owner_id)->value('room_speak');
//        $arr_room_ban = explode (',',$room_ban);

//        if (in_array ($user_id,$arr_room_ban)){
//            return Common::apiResponse (false,'you are in room ban list');
//        }


        //Total value of all gifts received      stopped here
        $room_info['giftPrice'] = DB::table('gift_logs')->where('receiver_id',$owner_id)->sum('giftPrice');


        if($room_info['room_pass'] &&  $owner_id != $user_id){
            if(!$room_pass) return Common::apiResponse(false,__('The room is locked, please enter the password'),null,409);
            if($room_info['room_pass'] != $room_pass )  return Common::apiResponse(false,__('Password is incorrect, please re-enter'),null,410);
        }
        //General users
        $room_info['user_type'] = 5; //General users
        $roomAdmin = explode(',', $room_info['room_admin']);
        for ($i=0; $i < count($roomAdmin); $i++) {
            if($roomAdmin[$i] == $user_id){
                $room_info['user_type'] = 2;//administrator
            }
        }

        $roomJudge = explode(',', $room_info['room_judge']);
        for ($i=0; $i < count($roomJudge); $i++) {
            if($roomJudge[$i] == $user_id){
                $room_info['user_type'] = 4;//judges
            }
        }
        $is_sound = explode(',', $room_info['room_sound']);
        for ($i=0; $i < count($is_sound); $i++) {
            if($is_sound[$i] == $user_id){
                $room_info['is_sound'] = 2;    //User Mute
            }else{
                $room_info['is_sound'] = 1;    //User speak
            }
        }
        if($user_id == $owner_id){
            $room_info['user_type'] = 1;       //homeowner
        }

        $uid_sound = explode(',', $room_info['room_sound']);
        for ($i=0; $i < count($uid_sound); $i++) {
            if($uid_sound[$i] == $owner_id){
                $room_info['owner_sound'] = 2;   //Homeowner Mute
            }else{
                $room_info['owner_sound'] = 1;   //The homeowner speak
            }
        }

        $uid_black = explode(',', $room_info['room_speak']);

        for ($i=0; $i < count($uid_black); $i++) {
            if($uid_black[$i] == $owner_id){
                $room_info['uid_black'] = 2;   //homeowners ban writing
            }else{
                $room_info['uid_black'] = 1;   //Homeowner does not ban writing
            }
        }



        $room_info['owner_avatar'] = @User::query ()->find($owner_id)->profile->avatar;

        $mykeep = DB::table('users')->where('id',$user_id)->value('mykeep');
        $mykeep_arr=explode(",", $mykeep);
        //1 has been collected 2 has not been collected
        $room_info['is_favorite'] = in_array($owner_id, $mykeep_arr)  ? 1 : 2; // stopped here

        //room number added
        if($room_info['user_type'] != 1){
            $roomVisitor=$room_info['room_visitor'];
            $visitor_arr=explode(',',$roomVisitor);
            if(!in_array($user_id, $visitor_arr))   array_unshift($visitor_arr,$user_id);
            $visitor=trim(implode(",", $visitor_arr),",");
            DB::table('rooms')->where('uid',$owner_id)->update(['room_visitor'=>$visitor]);
            $room_info['room_visitor']=$visitor;
        }

        $room_info['room_visitors_count'] = count(explode (',',$room_info['room_visitor']));

        $room_info['room_pass'] = !empty($room_info['room_pass']) ? $room_info['room_pass']: '';
        //background
        $back = DB::table('backgrounds')->where('enable',1)->where(['id'=>$room_info['room_background']])->value('img');
        if(!$back){
            $mr_back=(array)DB::table('backgrounds')->where('enable',1)->orderBy('id', 'asc')->limit(1)->first();
            $back=@$mr_back['img'];
            $room_info['room_background']=@$mr_back['id'];
        }
        $room_info['back_img']=  $back;
        $room_info['room_name']=  urldecode($room_info['room_name']);

        //homeowner information
        $user=(array)DB::table('users')->select('id','dress_1','dress_4')->find($owner_id);
        if (!$user){
            return Common::apiResponse (false,'room owner may be deleted',null,404);
        }
        $txk=DB::table('wares')->where(['id'=>$user['dress_1']])->value('img1');
        $room_info['txk']=$txk;
        $room_info['mic_color']=DB::table('wares')->where(['id'=>$user['dress_4']])->value('color') ? : '#ffffff';
        //difference from the previous one
        $res_gap = Common::check_gap_hand($owner_id);
        $room_info['gap'] = $res_gap['gap'];
        $room_info['exp'] = $res_gap['exp'];
        $room_info['hot'] = Common::room_hot($room_info['hot']);

        //Is it in the mic position
        $mic_arr=explode(",",$room_info['microphone']);
        $room_info['phase']= !in_array($user_id, $mic_arr) ? 0 : array_search($user_id,$mic_arr)+1 ;

        //sorting number
        $micSortHand = Common::micSortHand($user_id,$owner_id);
        $room_info['sort'] = $micSortHand['sort'];
        $room_info['num']  = $micSortHand['num'];
        $room_info['audio_sort'] = $micSortHand['audio_sort'];
        $room_info['audio_num']  = $micSortHand['audio_num'];

        //Dispatch has started time
        $room_info['strto_time'] = 0;
        $wap = "status = '1' and uid = '$owner_id' and endtime is null";
        $monadsInfo = (array)Db::table('monads')->selectRaw('id,addtime')->whereRaw($wap)->first();
        if (!empty($monadsInfo) && !empty($monadsInfo['addtime'])){
            $room_info['strto_time'] = (time() - $monadsInfo['addtime']);
        }

        foreach ($room_info as $k => &$v){
            if (!$v){
                if (in_array ($k,['is_afk','gap','exp','sort','num','audio_sort','audio_num','strto_time'])){
                    $v = 0;
                }else{
                    $v = '';
                }
            }
        }

        $type = RoomCategory::find($room_info['room_type']);
        if (!$type){
            $room_info['room_type'] = '';
        }else{
            $room_info['room_type'] = $type->name;
        }

        $bg = Background::find($room_info['room_background']);
        if (!$bg){
            $room_info['room_background'] = '';
        }else{
            $room_info['room_background'] = $bg->img;
        }


        $user = $request->user ();
        $user->now_room_uid = $room_info['owner_id'];
        $user->save();
        $timer_id = 0;
        if($owner_id == $user_id){
            $timer = LiveTime::query ()->create (
                [
                    'uid'=>$owner_id,
                    'start_time'=>time ()
                ]
            );
            $timer_id = $timer->id;
        }

        $d = [
            "messageContent"=>[
                "message"=>"userEntro",
                "entroImg"=>Common::getUserDress($user->id,$user->dress_3,6,'img2')?:Common::getUserDress($user->id,$user->dress_3,6,'img1'),
                'userName'=>$user->name?:$user->nickname,
                'userImge'=>$user->avatar
            ]
        ];
        $json = json_encode ($d);
        if (!$request->is_update){
            Common::sendToZego ('SendCustomCommand',$room_info['id'],$user->id,$json);
            Common::sendToZego_2 ('SendBroadcastMessage',$room_info['id'],$user->id,$user->name,$user->name.' inter room');
        }
        $room_info['timer_id'] = $timer_id;
        $room_info['password_status']=$room_info['room_pass']==""?false:true;

        return Common::apiResponse (true,'',$room_info);
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
        if ($request->owner_id == $user_id){
            $this->calcTime ($request->timer_id);
        }
        return Common::apiResponse(true,'exited',['visitor_ids_list'=>$visitor_ids_list]);
    }

    public function calcTime($timer_id){
        $timer = LiveTime::query ()->find ($timer_id);
        if ($timer){
            $hours = floor((time ()-$timer->start_time)/(60*60));
            $timer->end_time = time ();
            $timer->hours = $hours;
            if ($hours >= 1){
                $timer->days = 1;
            }
            $timer->save ();
        }
    }


    //getRoomUsers
    public function getRoomUsers(Request $request){
        $uid = $request->owner_id;
        $roomAdmin=Room::query ()->where(['uid'=>$uid])->value('room_admin');
        $roomAdmin=explode(',',$roomAdmin);
        $admins=User::where('id','in', $roomAdmin)->get();
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
        $visitor = [];
        foreach($visitors as $k=>$v){
            $visitor[$k]['id'] = @$v->id;
            $visitor[$k]['nickname'] = @$v->nickname;
            $visitor[$k]['avatar'] = @$v->profile->avatar;
            $visitor[$k]['country'] = @$v->profile->country;
            $visitor[$k]['is_admin'] = 0;
        }
        $res['room_id']=$uid;
        $res['admin']= UserResource::collection ($admins);//$admin;
        $res['visitors']=UserResource::collection ($visitors);//$visitor;
        return Common::apiResponse(1,'',$res);
    }


    // mic sequence list
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
        $room=(array)DB::table('rooms')->where(['uid'=>$data['owner_id']])->whereIn('room_status',['neq',4])->selectRaw('id,room_visitor,room_admin,microphone,free_mic')->first();
        if(!$room)  return Common::apiResponse(0,__('room does not exist'));
        $vis_arr= !$room['room_visitor'] ? [] : explode(",", $room['room_visitor']);
        if(!in_array($user_id, $vis_arr) && $data['uid'] != $user_id)   return Common::apiResponse(0,__('The user is not in this room'),null,403);

        $position = $data['position'];//Wheat sequence 0-8
        if($position <0 || $position >9) return Common::apiResponse(0,__('position error'),null,422);

        $mic_arr=explode(',', $room['microphone']);
        if($mic_arr[$position] == -1)   return Common::apiResponse(0,__('This slot has been locked'),null,408);
        if($mic_arr[$position] != 0)   return Common::apiResponse(0,__('There is a user on the mic'),null,405);


        //How to play free mic
        $adm_id=$request->user ()->id;
        if($room['free_mic'] == 0 && $adm_id != $data['owner_id']){
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
                $va['cp_xssm']= $va['cp_level'] >=7 ? $cp_xssm : '';
            }else{
                $va['cp_xssm']='';
            }
            $i++;
        }
        $mic_arr[$position]=$user_id;
        $mic=implode(',', $mic_arr);
        $res = DB::table('rooms')->where('uid',$data['owner_id'])->update(['microphone'=>$mic]);

        $user=(array)DB::table('users')->selectRaw('id,nickname')->find($user_id);
        $user['avatar']=@User::query ()->find ($user_id)->profile->avatar;
        $user_level=Common::getLevel($user_id,3);
        $user['nick_color']=Common::getNickColorByVip($user_level);
        $res_arr['cp']=$cp_arr;
        $res_arr['user']=$user;
        if($res){
            //Remove mic sequence
            Common::delMicHand($user_id);
            return Common::apiResponse(1,__('Success on the mic'),$res_arr);
        }else{
            return Common::apiResponse(0,__('Failed to mic'),null,400);
        }
    }

    //leave mic
    public function go_microphone(Request $request){
        $data = $request;
        $result=Common::go_microphone_hand($data['owner_id'],$data['user_id']);
        if($result){
            return Common::apiResponse(1,__('Success'));
        }else{
            return Common::apiResponse(0,__('Failed'),null,400);
        }
    }


    //lock mic place
    public function shut_microphone(Request $request)
    {
        $data = $request;
        $position = $data['position'];
        if($position <0 || $position >9) return Common::apiResponse(0,__('position error'),null,422);
        $admins = Room::query ()->where ('uid',$data['owner_id'])->first ()->value ('room_admin');
        $admins = explode (',',$admins);
        if($request->user ()->id != $data['owner_id'] || !in_array ($request->user ()->id,$admins) ) {
            return Common::apiResponse(0,__('you dont have permission'),null,408);
        }
        $microphone = DB::table('rooms')->where('uid',$data['owner_id'])->value('microphone');
        $microphone = explode(',', $microphone);
        $microphone[$position] = -1;
        $microphone = implode(',', $microphone);
        $res = DB::table('rooms')->where('uid',$data['owner_id'])->update(['microphone'=>$microphone]);
        if($res){
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
        if($position <0 || $position >9)  return Common::apiResponse(0,__('position error'));
        $admins = Room::query ()->where ('uid',$data['owner_id'])->first ()->value ('room_admin');
        $admins = explode (',',$admins);
        if($request->user ()->id != $data['owner_id'] || !in_array ($request->user ()->id,$admins) ) {
            return Common::apiResponse(0,__('you dont have permission'),null,408);
        }
        $microphone = DB::table('rooms')->where('uid',$data['owner_id'])->value('microphone');
        $microphone = explode(',', $microphone);
        $microphone[$position] = 0;
        $microphone = implode(',', $microphone);
        $res = DB::table('rooms')->where('uid',$data['owner_id'])->update(['microphone'=>$microphone]);
        if($res){
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
        $admins = Room::query ()->where ('uid',$uid)->first ()->value ('room_admin');
        $admins = explode (',',$admins);
        if($request->user ()->id != $uid || !in_array ($request->user ()->id,$admins) ) {
            return Common::apiResponse(0,__('you dont have permission'),null,408);
        }
        $sound = DB::table('rooms')->where('uid',$uid)->value('room_sound');
        $sound_arr=explode(',', $sound);
        if(in_array($user_id , $sound_arr)) return Common::apiResponse (0,__ ('The user is already muted, please do not repeat the settings'),null,444);

        array_push($sound_arr,$user_id);
        $str=implode(',', $sound_arr);
        $res = DB::table('rooms')->where('uid',$uid)->update(['room_sound'=>$str]);
        if($res){
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
        $admins = Room::query ()->where ('uid',$uid)->first ()->value ('room_admin');
        $admins = explode (',',$admins);
        if($request->user ()->id != $uid || !in_array ($request->user ()->id,$admins) ) {
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
            return Common::apiResponse(1,__('Successfully unmuted'));
        }else{
            return Common::apiResponse(0,__('Unmute failed'),null,400);
        }
    }

    //kick out of the room
    public function out_room(Request $request){
        //https://rtc-api.zego.im/?
        //Action=KickoutUser&
        //AppId=1381228&
        //Timestamp=1672673866&
        //Signature=a5e129db978cdfbd0485c7e3a2db1664&
        //SignatureVersion=2.0&
        //SignatureNonce=119a4f216892a749&
        //IsTest=no&
        //RoomId=156&
        //UserId[]=2484dxcx
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
                'MessageContent'=>[
                    'message'=>'kickout',
                    'duration'=>$duration
                ]
            ];
            $json = json_encode ($mc);
//            Common::sendToZego_3 ('KickoutUser',$room_id,$black_id);
            Common::sendToZego_4 ('SendCustomCommand',$room_id,$uid,$black_id,$json);
            return Common::apiResponse(1,'success');
        }else{
            return Common::apiResponse(0,'fail',null,400);
        }
    }

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
    public function is_admin(Request $request){
        $uid=$request->owner_id;
        $admin_id=$request->user_id;
        if ($request->user ()->id != $uid){
            return Common::apiResponse(0,'not allowed',null,403);
        }
        if(!$uid || !$admin_id) return Common::apiResponse(0,'invalid data',null,422);
        if($uid == $admin_id)    return Common::apiResponse(0,'invalid data',null,422);
        $roomVisitor=DB::table('rooms')->where('uid',$uid)->value('room_visitor');
        $vis_arr= !$roomVisitor ? [] : explode(",", $roomVisitor);
        if(!in_array($admin_id, $vis_arr))   return Common::apiResponse(0,'This user is not in this room',null,404);


        $roomAdmin=DB::table('rooms')->where('uid',$uid)->value('room_admin');
        $adm_arr= !$roomAdmin ? [] : explode(",", $roomAdmin);
        if(in_array($admin_id, $adm_arr))   return Common::apiResponse(0,'This user is already an administrator, please do not repeat the settings',null,444);
        if(count($adm_arr) >= 15)    return Common::apiResponse(0,'room manager is full',null,403);

        $adm_arr=array_merge($adm_arr,[$admin_id]);
        $str=implode(",",$adm_arr);

        $res=DB::table('rooms')->where(['uid'=>$uid])->update(['room_admin'=>$str]);
        if($res){
            return Common::apiResponse(1,'Set administrator successfully');
        }else{
            return Common::apiResponse(0,'Failed to set administrator',null,400);
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
        $roomAdmin=DB::table('rooms')->where('uid',$uid)->value('room_admin');
        $adm_arr= !$roomAdmin ? [] : explode(",", $roomAdmin);
        if(!in_array($admin_id, $adm_arr))   return Common::apiResponse(0,'This user is not an administrator of this room',null,404);
        $key=array_search($admin_id,$adm_arr);
        unset($adm_arr[$key]);
        $str=implode(",", $adm_arr);
        $res=DB::table('rooms')->where(['uid'=>$uid])->update(['room_admin'=>$str]);
        if($res){
            return Common::apiResponse(1,'Cancel administrator successfully');
        }else{
            return Common::apiResponse(0,'Failed to cancel administrator',null,400);
        }
    }

    //add ban
    public function is_black(Request $request){
        $uid=$request->owner_id;
        $user_id=$request->user_id;
        if(!$uid || !$user_id) return Common::apiResponse(0,'invalid data',null,422);
        if($uid == $user_id)    return Common::apiResponse(0,'Illegal operation',null,403);
//        if ($request->user ()->id != $uid){
//            return Common::apiResponse(0,'not allowed');
//        }
        $roomVisitor=DB::table('rooms')->where('uid',$uid)->value('room_visitor');
        $vis_arr= !$roomVisitor ? [] : explode(",", $roomVisitor);
        if(!in_array($user_id, $vis_arr))   return Common::apiResponse(0,'This user is not in this room',null,404);


        $roomSpeak=DB::table('rooms')->where('uid',$uid)->value('room_speak');
        $spe_arr= !$roomSpeak ? [] : explode(",", $roomSpeak);
        foreach ($spe_arr as $k => &$v) {
            $arr=explode("#",$v);
            if($arr[0] == $user_id) return Common::apiResponse(0,'This user is already on the ban list',null,444);
        }
        $shic=time() + 180;
        $jinyan=$user_id."#".$shic;
        $spe_arr=array_merge($spe_arr,[$jinyan]);
        $str=implode(",", $spe_arr);
        $res=DB::table('rooms')->where(['uid'=>$uid])->update(['room_speak'=>$str]);
        if($res){
            return Common::apiResponse(1,'Succeeded adding writing ban for 3 minutes');
        }else{
            return Common::apiResponse(0,'Failed to add writing ban',null,400);
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





}
