<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftLogController extends Controller
{

    //push notification
    public  function push_gifts(){
        $data=DB::table('gift_logs')->where('is_play',2)->limit(1)->orderByDesc("id")->select(['id','uid','giftId','giftName','user_id','fromUid','giftNum'])->find();
        if(!$data)  $this->ApiReturn(0,'لا يوجد حاليا أي بث متاح',null,404);
        $info['uid']=$data['uid'];
        $info['user_name']=DB::table('users')->where('id',$data['user_id'])->value('nickname');
        $info['from_name']=DB::table('users')->where('id',$data['fromUid'])->value('nickname');
        $info['num']=$data['giftNum'];
        $info['gift_name']=$data['giftName'];
        $img=DB::table('gifts')->where('id',$data['giftId'])->value('img');
        $info['img']=$this->auth->setFilePath($img);
        $arr['type']='gift';
        $arr['data']=$info;
        $arr_json=json_encode($arr,JSON_UNESCAPED_UNICODE);
        $res1=$this->android_push('','',$arr);
        $res2=$this->ios_push('',$arr);

        DB::table('gift_logs')->where('id',$data['id'])->update(['is_play'=>1]);
        $this->ApiReturn(1,'推送成功'.$data['id']);
        // if($res1 || $res2 ){
        //     DB::name('gift_logs')->where('id',$data['id'])->update(['is_play'=>1]);
        //     $this->ApiReturn(1,'推送成功'.$data['id']);
        // }else{
        //     $this->ApiReturn(0,'推送失败');
        // }
    }

    public function gift_queue_six(Request $request)
    {
        $data=$request;
        $data['user_id'] = $request->user ()->id;
        if(!$data['id'] || !$data['owner_id'] || !$data['user_id'] || !$data['toUid'] || !$data['num'] )
            return Common::apiResponse(0,'Missing parameters',$data->all ());
        if($data['num'] < 1)    return Common::apiResponse(0,'The number of gifts cannot be less than 1',null,422);
        $gift=DB::table('gifts')->select(['id','name','type','price','vip_level','is_play','img','show_img','show_img2'])->where(['id'=>$data['id']])->where('enable',1)->first();

        $user=DB::table('users')->select(['id','di','name'])->where(['id'=>$data['user_id']])->first();

        if(!$gift) return Common::apiResponse(0,'Gift does not exist or has been removed',null,404);
        $room=DB::table('rooms')->where(['uid'=>$data['owner_id']])->selectRaw('id,uid,room_visitor,play_num,hot')->first();

        if(!$room)  return Common::apiResponse(0,'room does not exist',null,404);
        $vis_arr=explode(",",$room->room_visitor);
        $vis_arr[]=$data['owner_id'];

        if(!in_array($data['user_id'],$vis_arr))    return Common::apiResponse(0,'you are not in this room',null,403);

        //Determine whether to send
        $vip_level=Common::getLevel($data['user_id'],3);
        if($vip_level < $gift->vip_level)   return Common::apiResponse(0,'vip '.$gift->vip_level.' to send this gift');
        $to_arr=explode(',', $data['toUid']);
        foreach ($to_arr as $k1 => &$v1) {
            if(!in_array($v1,$vis_arr))    return Common::apiResponse(0,'User is not in this room',null,403);
//            if($data['user_id'] == $v1)    return Common::apiResponse(0,'Can\'t give yourself a gift');
        }
        unset($v1);


        //Number of backpacks
        $pack_gift_num=DB::table('packs')->where(['type'=>2,'user_id'=>$data['user_id'],'target_id'=>$data['id']])->value('num') ? : 0;
        //Total sent quantity
        $send_num=$data['num'] * count($to_arr);

        if($pack_gift_num > 0){
            if($pack_gift_num <= $send_num){
                //Calculate required diamonds
                $shengyu_num=$send_num - $pack_gift_num;
                $sum_gift_price=$shengyu_num * $gift->price;
                if($user->di < $sum_gift_price)   return Common::apiResponse(0,'Insufficient balance, please go to recharge!',null,407);
            }
        }else{
            $total_price=$gift->price * $send_num;
            if($user->di < $total_price)   return Common::apiResponse(0,'Insufficient balance, please go to recharge!',null,407);
        }

        DB::beginTransaction();
        try{

            if($pack_gift_num > 0){
                if($pack_gift_num > $send_num){
                    //Subtract $send_num from the number of gifts in the backpack, without diamonds
                    Common::userPackStoreDec($data['user_id'],2,$data['id'],$send_num);
                    $shenngyu_price=0;
                }else{
                    //Calculate required diamonds
                    $shengyu_num=$send_num - $pack_gift_num;
                    $sum_gift_price=$shengyu_num * $gift->price;
                    if($user->di < $sum_gift_price)    return Common::apiResponse(0,'Insufficient balance, please go to recharge!',null,407);
                    //Delete all the gifts in the backpack, deduct the difference diamonds
                    Common::userPackStoreDec($data['user_id'],2,$data['id'],$pack_gift_num);
                    $shenngyu_price=$sum_gift_price;
                }
            }else{
                $total_price=$gift->price * $send_num;
                if($user->di < $total_price)    return Common::apiResponse(0,'Insufficient balance, please go to recharge!',null,407);
                $shenngyu_price=$total_price;
            }

            $i=0;
            $res=$push=[];
            foreach ($to_arr as $k => &$v) {
                $i++;
                $this->sendGifts($data['id'],$data['owner_id'],$data['num'],$gift->name,$gift->price,$data['user_id'],$v,0);
                $level= Common::getLevel($v,3);
                $res_arr['nick_color'] = Common::getNickColorByVip($level);
                $res_arr['is_first'] = 0;
                $user=User::find($v);
                $res_arr['userId']=$v;
                $res_arr['nickname']=@$user->nickname;
                $res_arr['image']=@$user->profile->avatar;


                //numerical play
                if($room->play_num == 1){
                    $price = $data['num'] * $gift->price;
                    Common::add_play_num($data['owner_id'],$v,$price);
                }
                //broadcast
                if($gift->is_play == 1){
                    $info['uid']=$data['owner_id'];
                    $info['user_name']=Common::getUserField($data['user_id'],'nickname');
                    $info['to_name']=Common::getUserField($v,'nickname');
                    $info['num']=$data['num'];
                    $info['gift_name']=$gift->name;
                    $info['img']=$gift->img;
                    $push[]=$info;
                }

                $res[]=$res_arr;
            }
            unset($v);
            if($shenngyu_price > 0)     Common::userStoreDec($data['user_id'],$shenngyu_price,13,'di');      //Send a Gift
            if($i == count($to_arr)){
                //Unlock vip, cp level items, and dress up the latest items
                Common::unlock_wares($data['user_id']);
                $total_mizuan= $send_num * $gift->price;
                //Homeowner's total turnover
                Common::update_user_total($data['uid'],1,$total_mizuan);
                //The total amount issued by the user
                Common::update_user_total($data['user_id'],2,$total_mizuan);
                // Task - give others gifts 3 times
                Common::fin_task($data['user_id'],7);
            }
            //commit transaction
            DB::commit();
        } catch (\Exception $e) {dd ($e);
            //rollback transaction
            DB::rollback();
            return Common::apiResponse(0,'Gift delivery failed',null,400);
        }
        if(is_array($to_arr) && count ($to_arr) > 0){
            $to_id = $to_arr[0];
            $to = 'room';
        }else{
            $to_id = $data['toUid'];
            $to = @User::query ()->find ($data['toUid'])->name?:'empty name';
        }

        Common::sendToZego_2 ('SendBroadcastMessage',$room->id,$user->id,$user->name,($user->name?:' empty name'.' send gift ') . $gift->price . ' to ' . $to );
        $return_arr['users']=$res;
        $return_arr['push']=$push;
        if ($request->to_zego == 1){
            $ro = Room::query ()->find ($room->id);
            $d = [
                "messageContent"=>[
                    "message"=>"showGifts",
                    "showGift"=>$gift->show_img?:$gift->show_img2,
                    'giftImg'=>$gift->img,
                    'send_id' => (integer)$data['user_id'],
                    'receiver_id'=>(integer)$to_id,
                    'isExpensive'=>($gift->price >= 2000)?true:false,
                    'num_gift'=>$send_num,
                    "plural"=>(is_array($to_arr) && count ($to_arr) > 1)?true:false,
                    'gift_price'=>$ro->hot
                ]
            ];
            $json = json_encode ($d);
            $res = Common::sendToZego ('SendCustomCommand',$room->id,$user->id,$json);
            if ($gift->price >= 2000){
                $rooms = Room::where('room_status',1)->where(function ($q){
                    $q->where('is_afk',1)->orWhere('room_visitor','!=','');
                })->get();
                $d2 = [
                    "messageContent"=>[
                        "message"=>"showBanner",
                        'send_id'=>(integer)$data['user_id'],
                        'receiver_id'=>(integer)$to_id,
                        'owner_id'=>(integer)$data['owner_id'],
                        'is_password'=>$room->room_pass?true:false,
                        "giftImg"=>$gift->img
                    ]
                ];
                $json2 = json_encode ($d2);
                foreach ($rooms as $r){
                    $res = Common::sendToZego ('SendCustomCommand',$r->id,$user->id,$json2);
                }
            }else{
                $res = Common::sendToZego ('SendCustomCommand',$room->id,$user->id,$json);
            }
        }
        return Common::apiResponse(1,'Gift sent successfully',$return_arr);
    }

    //Execute send gift
    protected function sendGifts($id,$uid,$num,$name,$price,$user_id,$toUid,$is_play){
        $info['giftId']=$id;
        $info['roomowner_id']=$uid;
        $info['giftNum']=$num;
        $info['giftName']=$name;
        $info['giftPrice']=$price * $num;
        $info['sender_id']=$user_id;
        $info['receiver_id']=$toUid;
        $info['is_play']=$is_play ? 2 : 1;
        $info['type']=2;
        $info['created_at']=$info['updated_at']=date('Y-m-d H:i:s',time());


        //Calculate the share
        $income = $this->calculate($uid,$toUid,$info['giftPrice']);
        $info['platform_obtain']=$income['platform'];   //platform
        $info['receiver_obtain']=$income['toUid'];     //recipient
        $info['roomowner_obtain']=$income['uid']+$income['uid_yj'];//homeowner
        //get guild
        $union=DB::table('user_unions')->where(['user_id' => $toUid,'check_status'=>1])->first ();
        if ($union){
            $info['union_id']= $union->union_id;//guild
        }
        $receiver_family=DB::table('family_user')->where(['user_id' => $toUid,'status'=>1])->first ();
        if ($receiver_family){
            $info['receiver_family_id']= $receiver_family->family_id;//family
        }
        $sender_family=DB::table('family_user')->where(['user_id' => $toUid,'status'=>1])->first ();
        if ($sender_family){
            $info['sender_family_id']= $sender_family->family_id;//family
        }
        $toUser = User::query ()->find ($toUid);
        $agency = Agency::query ()->find($toUser->agency_id);
        if ($agency){
            $info['agency_id']= $agency->id;//family
        }
        $res=DB::table('gift_logs')->insertGetId($info);
        if($res){
            if($income['uid'] > 0) {
                //Increase guild income and records
                $union=DB::table('user_unions')->where(['user_id' => $uid,'check_status'=>1])->first();
                if ($union){
                    Common::userUnionStoreInc($uid,$income['uid'],31,'room_coins');
                }else{
                    Common::userStoreInc($uid,$income['uid'],31,'room_coins');  //Room running water
                }
            }
            if($income['uid_yj'] > 0) {
                //Increase guild income and records
                $union=Db::table('user_unions')->where(['user_id' => $uid,'check_status'=>1])->first();
                if ($union){
                    Common::userUnionStoreInc($uid,$income['uid_yj'],32,'room_coins');
                }else{
                    Common::userStoreInc($uid,$income['uid_yj'],32,'room_coins');    //Subordinate share of homeowners, commission
                }
            }
            if($income['toUid'] > 0)  {
                //Increase guild income and records
                $union=Db::table('user_unions')->where(['user_id' => $toUid,'check_status'=>1])->first();
                if ($union){
                    Common::userUnionStoreInc($toUid,$income['toUid'],21,'coins');
                }else{
                    Common::userStoreInc($toUid,$income['toUid'],21,'coins');//Receive a gift
                }
            }
            if($income['platform'] > 0)  Common::userStoreInc(0,$income['platform'],99,'coins');  //platform income
            //increase room heat
            $this->addRoomHot($uid,$info['giftPrice']);
            //The total amount received by the user
            Common::update_user_total($toUid,3,$info['giftPrice']);

            return 1;
        }
        else{
            return 0;
        }
    }

    //Calculate the income of all parties
    public function calculate($uid,$toUid,$total){
        $room_user=DB::table('users')->select(['id','is_sign','scale','is_leader'])->where('id',$uid)->first();
        if (!$room_user){
            throw new \Exception('room owner not found');
        }
        $room_scale = Common::getConfig('platform_share');
        $room_scale = $room_scale ? $room_scale : 0;//Platform share
        if(!$room_user->is_sign){//non-contract homeowner
            $data['uid']=0;//Room running water
            $data['toUid']=$total * ((100 - $room_scale)/100) ;//recipient
            $data['platform']=$total * ($room_scale/100) ;//Platform flow
            $data['uid_yj']=0;//homeowner
        }else{
            //Room running water
            $stream = $total * $room_user->scale/100;
            //platform
            $platform = $total * ($room_scale-$room_user->scale)/100;
            if($room_user->is_leader){
                $scale=DB::table('leaders')->where('uid',$uid)->where('user_id',$toUid)->where('status',2)->value('scale') ? : 100;
            }else{
                $scale = 100;
            }
            //recipient
            $room_scale_sign = (100 - $room_scale)/100;
            $get_gift=$total * ($room_scale_sign * $scale /100);
            $uid_yj = $total * ($room_scale_sign * (100 - $scale)/100);

            $data['uid']=$stream;//Room running water
            $data['toUid']=$get_gift;//recipient
            $data['platform']=$platform;//Platform flow
            $data['uid_yj']=$uid_yj;//homeowner
        }
        $data=array_map(function($val){
//            $gvic = Common::getConf ('gift_value_in_coins')?:0.1;
            $gvic = 1;
            return round($val*$gvic,2);
        }, $data);
        return $data;
    }

    //increase room heat
    protected function addRoomHot($uid,$hot){
        DB::table('rooms')->where('uid',$uid)->increment('hot',$hot);
    }
}
