<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function my_data(Request $request){
        $user = $request->user ();
        $data = new UserResource($user);
        return Common::apiResponse (true,'',$data,200);
    }

    public function show(Request $request,$id){
        $user = User::query ()->find ($id);
        if (!$user) return Common::apiResponse (false,'not found',null,404);
        $data = new UserResource($user);
        return Common::apiResponse (true,'',$data,200);
    }

    public function userFriend(Request $request){
        $user = $request->user ();
        switch ($request->type){
            case '1': // they follow me
                return Common::apiResponse (true,'',UserResource::collection ($user->followers()),200);
            case '2': // I follow them
                return Common::apiResponse (true,'',UserResource::collection ($user->followeds()),200);
            case '3': // friends [i follow them & they follow me]
                return Common::apiResponse (true,'',UserResource::collection ($user->friends()),200);
            default: // friends [i follow them & they follow me]
                return Common::apiResponse (false,'please select type',null,200);
        }
    }

    //my favorite room
    public function get_myfavorite(Request $request)
    {
        $user = $request->user ();
        $list = $user->value('mykeep');
        $mykeep = explode(',', $list);
        $room = DB::table('rooms','t1')
            ->join('users','t1.uid','=','users.id','left')
            ->select(['t1.numid','t1.uid','t1.room_name','t1.room_cover','t1.hot','t1.is_afk','users.nickname'])
            ->whereIn('t1.uid',$mykeep)
            ->get();
        $room=Common::roomDataFormat($room);
        $ar1=$ar2=[];
        foreach ($room as $key => &$v) {
            if($v['is_afk'] == 1){
                $ar1[]=$v;
            }else{
                $ar2[]=$v;
            }
        }
        unset($v);
        $arr['on']  = $ar1;
        $arr['off'] = $ar2;
        return common::apiResponse(1,'',$arr);
    }


    //my backpack  type

//    '1' => 'gem',
//    '2' => 'gifts - not used',
//    '3' => 'coupons',
//    '4' => 'avatar frames',
//    '5' => 'bubble boxes',
//    '6' => 'entry effects',
//    '7' => 'mic on the aperture',
//    '8' => 'badges',


//Obtaining method 1 vip level automatic acquisition 2 activities 3 treasure box 4 purchase 5 = background addition

    public function my_pack(Request $request){
        $user_id = $request->user ()->id;
        $type = $request->type;
        if(!in_array($type,[1,2,3,4,5,6,7]))    return Common::apiResponse (0,'type not found');
        $where['a.user_id']=$user_id;
        $where['a.type']=$type;
        if($type == 2){
            $data=DB::table('packs','a')->join('gifts as b','a.target_id','=','b.id')
                ->where($where)
                ->selectRaw("a.*,b.name,b.show_img,b.price")
                ->get();
        }else{
            $data=DB::table('packs','a')->join('wares as b','a.target_id','=','b.id')
                ->where($where)
                ->selectRaw("a.*,b.name,b.show_img,b.title,b.color")
                ->get();
        }
        if(in_array($type,[4,5,6,7])){
            $dress_id=Db::table('users')->where(['id'=>$user_id])->value("dress_".$type);
        }
        foreach ($data as $k => &$v) {
            $v->is_dress=0;
            if(in_array($type,[4,5,6,7])){
                $v->title= empty($v->expire) ? "permanent" : date('Y-m-d H:i:s',$v->expire)."expire";
                $v->is_dress= $dress_id == $v->target_id  ? 1 : 0;
                $v->color= $v->color ? : '';
            }elseif($type == 2){
                $v->title = "have".$v->num."value".$v->num*$v->price."diamond";
                $v->color = '';
            }else{
                $v->title="have".$v->num."indivual ".$v->title;
                $v->color= $v->color ? : '';
            }

            $types = [
                '1' => 'gem',
                '2' => 'gifts',
                '3' => 'coupons',
                '4' => 'avatar frames',
                '5' => 'bubble boxes',
                '6' => 'entry effects',
                '7' => 'mic on the aperture',
                '8' => 'badges',
            ];
            $get_types = [
                '1'=>'vip level automatic acquisition',
                '2'=>'activities',
                '3'=>'treasure box',
                '4'=>'purchase',
                '5'=>'background addition'
            ];
            $v->type = __($types[$v->type]);
            $v->get_type = __($get_types[$v->get_type]);

            //status changed to read
            if ($v->is_read == 1){
                Db::table('pack')->where(array('id'=>$v['id']))->update(array('is_read'=>0));
            }

        }

        return Common::apiResponse(1,'',$data);
    }




    //leaderboard
    public function ranking(Request $request) {
        $class = $request->class  ? : 1; //1 star sharp 2 gold sharp
        $type = $request->type ? : 1; //1 day list 2 week list March list
        if (!in_array($class, [1, 2]) || !in_array($type, [1, 2, 3])) return Common::apiResponse (0,'Parameter error');
        $is_home=$request->is_home;
        $limit = $is_home ? 3 : 30;
        $arr=$this->rankingHand($class,$type,$request->user (),$limit);
        return Common::apiResponse(1,'',$arr);
    }
    public function rankingHand($class,$type,$user,$limit=30) {
        $user_id = $user->id;
        if (!in_array($class, [1, 2]) || !in_array($type, [1, 2, 3])) return Common::apiResponse (0,'Parameter error');

        if ($class == 1) {
            $keywords = 'receiver_id';
        } elseif ($class = 2) {
            $keywords = 'sender_id';
        }
        if ($type == 1) {
            $query = DB::table('gift_logs')->whereDay('created_at', Carbon::now ()->day);
        } elseif ($type == 2) {
            $query = DB::table('gift_logs')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()] );
        } elseif ($type == 3) {
            $query = DB::table('gift_logs')->whereMonth('created_at', Carbon::now()->month);
        }
        $data=$query->selectRaw("sum(giftPrice) as exp ,". $keywords)->groupBy($keywords)->orderByRaw("exp desc")->limit($limit)->get();
        $i=$l=0;
        foreach ($data as $k => & $v) {
            $i++;
            $users = User::query ()->find($v->{$keywords});
            $v->user_id = $v->{$keywords};
            $v->exp = ceil($v->exp);
            $v->avatar = @$users->profile->avatar?:'';
            $v->nickname = $users->nickname?:'';
            $v->sex = @$users->profile->gender == 1?trans ('male'):trans ('female');
            $v->stars_img = Common::getLevel($v->{$keywords}, 1 ,'img');
            $v->gold_img = Common::getLevel($v->{$keywords}, 2 ,'img');
            $v->vip_img = Common::getLevel($v->{$keywords}, 3 ,'img');
            if ($v->{$keywords} == $user_id) $l = $i;
            unset($v->{$keywords});
        }
        unset($v);
        //empty data
        $kong['exp']=0;
        $kong['user_id']=0;
        $kong['sex']=0;
        $kong['avatar']='';
        $kong['nickname']='';
        $kong['stars_img']='';
        $kong['gold_img']='';
        $kong['vip_img']='';
        $data[0] = isset($data[0]) ? $data[0] : $kong;
        $data[1] = isset($data[1]) ? $data[1] : $kong;
        $data[2] = isset($data[2]) ? $data[2] : $kong;
        if($limit == 3) return $data;
        //user
        $user->sort = $l ? (string)$l : '99+';
        $user->stars_img = Common::getLevel($user->id, 1 ,'img');
        $user->gold_img = Common::getLevel($user->id, 2 ,'img');
        $user->vip_img = Common::getLevel($user->id, 3 ,'img');
        if ($type == 1) {
            $q = DB::table('gift_logs')->whereDay('created_at', Carbon::now ()->day);
        } elseif ($type == 2) {
            $q = DB::table('gift_logs')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()] );
        } elseif ($type == 3) {
            $q = DB::table('gift_logs')->whereMonth('created_at', Carbon::now()->month);
        }
        $exp=$q->where($keywords,$user_id)->sum('giftPrice');
        $user->exp = ceil($exp);
        $user->avatar = @$user->profile->avatar;
        $user->sex = @$user->profile->gender == 1 ? 'male' : 'female';
        $arr['user'][0] = $user->only('id','exp','nickname','avatar','sex','sort','stars_img','gold_img','vip_img');

        $arr['top'] = array_slice($data->toArray (), 0, 3);
        $arr['other'] = array_slice($data->toArray (), 3);
        return $arr;
    }


    public function vip_center(Request $request){
        $user = $request->user();
        $vipCenter = Common::vip_center ($user->id,$request->level);
        return Common::apiResponse (1,'',$vipCenter);
    }

    public function level_center(Request $request){
        $user = $request->user();
        $levelCenter = Common::level_center ($user->id);
        return Common::apiResponse (1,'',$levelCenter);
    }

    // محفظتي
    public function my_store(Request $request){
        $user = $request->user();
        $myStore = Common::my_store ($user->id);
        return Common::apiResponse (1,'',$myStore);
    }

    public function my_income(Request $request){
        $data = Common::user_income ($request->user ()->id);
        return Common::apiResponse (1,'',$data);
    }

}
