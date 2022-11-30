<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\CacheHelper;
use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\EditRoomRequest;
use App\Http\Resources\Api\V1\RoomResource;
use App\Models\Room;
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
        $room_info = DB::table('rooms','rooms')
            ->join('users as users','rooms.uid','=','users.id','left')
            ->join('room_categories as room_categories','rooms.room_type','=','room_categories.id','left')
            ->where('rooms.uid',$request['owner_id'])
            ->select(['rooms.numid','rooms.uid','rooms.room_status','rooms.room_name',
                        'rooms.room_cover','room_categories.name','rooms.room_cover','rooms.room_intro',
                        'rooms.room_pass','rooms.room_type','rooms.hot','rooms.room_background','rooms.room_admin',
                        'rooms.room_speak','rooms.room_sound','rooms.microphone','rooms.room_judge','rooms.is_afk',
                        'users.nickname','rooms.room_visitor','rooms.play_num','rooms.free_mic'])
            ->first ();

        if(!$room_info) return Common::apiResponse (false,__('No room yet, please create first'));

        dd ($room_info);

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
        $room_info['giftPrice'] = DB::name('gift_logs')->where('uid',$data['uid'])->sum('giftPrice');


        if($room_info['room_pass'] &&  $uid != $user_id){
            if(!$room_pass) $this->ApiReturn(4,'房间已经上锁，请输入密码',$data['uid']);
            if($room_info['room_pass'] != $room_pass )  $this->ApiReturn(4,'密码不正确，请重新输入');
        }
        //一般用户
        $room_info['user_type'] = 5;
        $roomAdmin = explode(',', $room_info['roomAdmin']);
        for ($i=0; $i < count($roomAdmin); $i++) {
            if($roomAdmin[$i] == $user_id){
                $room_info['user_type'] = 2;//管理员
            }
        }

        $roomJudge = explode(',', $room_info['roomJudge']);
        for ($i=0; $i < count($roomJudge); $i++) {
            if($roomJudge[$i] == $user_id){
                $room_info['user_type'] = 4;//评委
            }
        }
        $is_sound = explode(',', $room_info['roomSound']);
        for ($i=0; $i < count($is_sound); $i++) {
            if($is_sound[$i] == $user_id){
                $room_info['is_sound'] = 2;    //用户禁声
            }else{
                $room_info['is_sound'] = 1;    //用户不禁声
            }
        }
        if($user_id == $data['uid']){
            $room_info['user_type'] = 1;       //房主
        }

        $uid_sound = explode(',', $room_info['roomSound']);
        for ($i=0; $i < count($uid_sound); $i++) {
            if($uid_sound[$i] == $data['uid']){
                $room_info['uid_sound'] = 2;   //房主禁声
            }else{
                $room_info['uid_sound'] = 1;   //房主不禁声
            }
        }

        $uid_black = explode(',', $room_info['roomSpeak']);
        for ($i=0; $i < count($uid_black); $i++) {
            if($uid_black[$i] == $data['uid']){
                $room_info['uid_black'] = 2;   //房主禁止打字
            }else{
                $room_info['uid_black'] = 1;   //房主不禁止打字
            }
        }



        $room_info['headimgurl'] = $this->auth->setFilePath($room_info['headimgurl']);
        $room_info['room_cover'] = $this->auth->setFilePath($room_info['room_cover']);
        $mykeep = DB::name('users')->where('id',$user_id)->value('mykeep');
        $mykeep_arr=explode(",", $mykeep);
        //1已收藏2未收藏
        $room_info['is_mykeep'] = in_array($data['uid'], $mykeep_arr)  ? 1 : 2;

        //房间人数添加
        if($room_info['user_type'] != 1){
            $roomVisitor=$room_info['roomVisitor'];
            $visitor_arr=explode(',',$roomVisitor);
            if(!in_array($user_id, $visitor_arr))   array_unshift($visitor_arr,$user_id);
            $visitor=trim(implode(",", $visitor_arr),",");
            DB::name('rooms')->where('uid',$data['uid'])->update(['roomVisitor'=>$visitor]);
            $room_info['roomVisitor']=$visitor;
        }

        $room_info['room_pass'] = !empty($room_info['room_pass']) ? : '';
        //背景
        $back = DB::name('backgrounds')->where('enable',1)->where(['id'=>$room_info['room_background']])->value('img');
        if(!$back){
            $mr_back=DB::name('backgrounds')->where('enable',1)->order('id', 'asc')->limit(1)->select();
            $back=$mr_back[0]['img'];
            $room_info['room_background']=$mr_back[0]['id'];
        }
        $room_info['back_img']=  $this->auth->setFilePath($back);
        $room_info['room_welcome']=  $this->getConfig('room_welcome');
        $room_info['room_name']=  urldecode($room_info['room_name']);

        //房主信息
        $user=DB::name('users')->field('id,dress_4,dress_7')->find($uid);
        $txk=DB::name('wares')->where(['id'=>$user['dress_4']])->value('img1');
        $room_info['txk']=$this->auth->setFilePath($txk);
        $room_info['mic_color']=DB::name('wares')->where(['id'=>$user['dress_7']])->value('color') ? : '#ffffff';
        //距上一名差额
        $ThreePhase = new \app\api\controller\ThreePhase;
        $res_gap = $ThreePhase->check_gap_hand($uid);
        $room_info['gap'] = $res_gap['gap'];
        $room_info['exp'] = $res_gap['exp'];
        $room_info['hot'] = room_hot($room_info['hot']);

        //是否在麦位
        $mic_arr=explode(",",$room_info['microphone']);
        $room_info['phase']= !in_array($user_id, $mic_arr) ? 0 : array_search($user_id,$mic_arr)+1 ;

        //排麦序号
        $waitSortHand = $this->waitSortHand($user_id,$uid);
        $room_info['sort'] = $waitSortHand['sort'];
        $room_info['num']  = $waitSortHand['num'];
        $room_info['shiyin_sort'] = $waitSortHand['shiyin_sort'];
        $room_info['shiyin_num']  = $waitSortHand['shiyin_num'];

        //派单已经开始时间
        $room_info['strto_time'] = 0;
        $wap = "status = '1' and uid = '$uid' and endtime is null";
        $monadsInfo = Db::name('monads')->field('id,addtime')->where($wap)->find();
        if (!empty($monadsInfo) && !empty($monadsInfo['addtime'])){
            $room_info['strto_time'] = (time() - $monadsInfo['addtime']);
        }

        //是否大神 0=否 1=是
        $skill_apply_count = Db::name('skill_apply')->where(array('user_id'=>$user_id,'status'=>1))->count();
        $room_info['is_god'] = ($skill_apply_count > 0) ? 1 : 0;
        // 任务--观看直播5分钟
        fin_task($user_id,6);
        // 任务--观看3次直播
        fin_task($user_id,9);
        $this->ApiReturn(1,'进入成功',[$room_info]);
    }



}
