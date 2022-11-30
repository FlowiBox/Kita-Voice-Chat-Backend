<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CacheHelper;
use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\EditRoomRequest;
use App\Http\Resources\Api\V1\RoomResource;
use App\Models\Room;
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
        return Common::apiResponse (true,'',RoomResource::collection (CacheHelper::get ('rooms',$this->repo->all ($request))),200);
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
    public function store(CreateRoomRequest $request)
    {
        try {
            $this->repo->create (array_merge($request->validated (),['uid'=>$request->user ()->id]));
            return Common::apiResponse (true,'created',null,200);
        }catch (\Exception $exception){
            return Common::apiResponse (false,$exception->getMessage (),null,$exception->getCode ());
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
        return Common::apiResponse (true,'',new RoomResource($this->repo->find ($id)),200);
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
            if ($room->uid != $request->user ()->id && !in_array ($request->user ()->id,explode (',',$room->room_admin))){
                return Common::apiResponse (false,'not allowed',null,401);
            }
            if ($request->room_name){
                $room->room_name = $request->room_name;
            }
            if ($request->room_mics){
                $room->microphone = $request->room_mics;
            }
            if ($request->hasFile ('room_cover')){
                $room->room_cover = Common::upload ('rooms',$request->file ('room_cover'));
            }
            if ($request->hasFile ('room_background')){
                $room->room_cover = Common::upload ('rooms',$request->file ('room_background'));
            }
            $this->repo->save ($room);
            return Common::apiResponse (true,'updated',new RoomResource($room),200);
        }catch (\Exception $exception){
            return Common::apiResponse (false,$exception->getMessage (),null,500);
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




    //进入房间
    public function enter_room(Request $request)
    {

        $room_pass = $request['room_pass'];
        $owner_id       = $request['owner_id'];
        $user_id   = $request->user ()->id;

        if($owner_id == $user_id){
            $res=DB::table('users')->where('id',$user_id)->value('is_idcard');
            if(!$res)  return Common::apiResponse(false,__('Please complete real-name authentication first'));
        }
        $black_list=Common::getUserBlackList($owner_id);
        if(in_array($user_id, $black_list)) return Common::apiResponse(false,__('You have been blocked by the other party'));
        $room_info = (array)DB::table('rooms','rooms')
            ->join('users as users','rooms.uid','=','users.id','left')
            ->join('room_categories as room_categories','rooms.room_type','=','room_categories.id','left')
            ->where('rooms.uid',$request['owner_id'])
            ->select(['rooms.numid','rooms.uid','rooms.room_status','rooms.room_name',
                        'rooms.room_cover','room_categories.name','rooms.room_cover','rooms.room_intro',
                        'rooms.room_pass','rooms.room_type','rooms.hot','rooms.room_background','rooms.room_admin',
                        'rooms.room_speak','rooms.room_sound','rooms.microphone','rooms.room_judge','rooms.is_afk',
                        'users.nickname','rooms.room_visitor','rooms.play_num','rooms.free_mic','rooms.room_welcome'])
            ->first ();

        if(!$room_info) return Common::apiResponse (false,__('No room yet, please create first'));



        //exit the original room
        $room_id=Common::userNowRoom($user_id);
        if($room_id && $room_id != $owner_id){
            Common::quit_hand($room_id,$user_id);
        }

        if($room_info['room_status'] == 3) return Common::apiResponse(false,__('The room has been banned, please contact customer service'));
        //enter your room
        if($owner_id== $user_id){
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
                if($sjc < 180 && $arr[0] == $user_id ){
                    Common::apiResponse(false,__('No entry for '). $arr[1] .__(' minutes after being kicked out of the room'));
                }

                if($sjc >= 180){
                    unset($is_black[$k]);
                }
            }
            $roomBlack = implode(",", $is_black);
            DB::table('rooms')->where('uid',$owner_id)->update(['room_black'=>trim($roomBlack,',')]);
        }


        //Total value of all gifts received      stopped here
        $room_info['giftPrice'] = DB::table('gift_logs')->where('receiver_id',$owner_id)->sum('giftPrice');


        if($room_info['room_pass'] &&  $owner_id != $user_id){
            if(!$room_pass) return Common::apiResponse(false,__('The room is locked, please enter the password'));
            if($room_info['room_pass'] != $room_pass )  return Common::apiResponse(false,__('Password is incorrect, please re-enter'));
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
                $room_info['uid_black'] = 2;   //homeowners ban typing
            }else{
                $room_info['uid_black'] = 1;   //Homeowner does not ban typing
            }
        }



        $room_info['owner_avatar'] = @User::query ()->find($owner_id)->profile->avatar;

        $mykeep = DB::table('users')->where('id',$user_id)->value('mykeep');
        $mykeep_arr=explode(",", $mykeep);
        //1 has been collected 2 has not been collected
        $room_info['is_mykeep'] = in_array($owner_id, $mykeep_arr)  ? 1 : 2; // stopped here

        //room number added
        if($room_info['user_type'] != 1){
            $roomVisitor=$room_info['room_visitor'];
            $visitor_arr=explode(',',$roomVisitor);
            if(!in_array($user_id, $visitor_arr))   array_unshift($visitor_arr,$user_id);
            $visitor=trim(implode(",", $visitor_arr),",");
            DB::table('rooms')->where('uid',$owner_id)->update(['room_visitor'=>$visitor]);
            $room_info['room_visitor']=$visitor;
        }

        $room_info['room_pass'] = !empty($room_info['room_pass']) ? : '';
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

//        //Are you a master? 0=No 1=Yes
//        $skill_apply_count = Db::table('skill_apply')->where(array('user_id'=>$user_id,'status'=>1))->count();
//        $room_info['is_god'] = ($skill_apply_count > 0) ? 1 : 0;
//        // Task - watch the live broadcast for 5 minutes
//        fin_task($user_id,6);
//        // Task - watch 3 live streams
//        fin_task($user_id,9);



        return Common::apiResponse (true,'',$room_info);
    }



}
