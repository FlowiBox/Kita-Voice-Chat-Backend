<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
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


}
