<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftLogController extends Controller
{
    public  function push_gifts(){
        $data=DB::table('gift_logs')->where('is_play',2)->limit(1)->orderByDesc("id")->select(['id','uid','giftId','giftName','user_id','fromUid','giftNum'])->find();
        if(!$data)  $this->ApiReturn(0,'暂无可播放播报');
        $info['uid']=$data['uid'];
        $info['user_name']=DB::name('users')->where('id',$data['user_id'])->value('nickname');
        $info['from_name']=DB::name('users')->where('id',$data['fromUid'])->value('nickname');
        $info['num']=$data['giftNum'];
        $info['gift_name']=$data['giftName'];
        $img=DB::name('gifts')->where('id',$data['giftId'])->value('img');
        $info['img']=$this->auth->setFilePath($img);
        $arr['type']='gift';
        $arr['data']=$info;
        $arr_json=json_encode($arr,JSON_UNESCAPED_UNICODE);
        $res1=$this->android_push('','',$arr);
        $res2=$this->ios_push('',$arr);

        DB::name('gift_logs')->where('id',$data['id'])->update(['is_play'=>1]);
        $this->ApiReturn(1,'推送成功'.$data['id']);
        // if($res1 || $res2 ){
        //     DB::name('gift_logs')->where('id',$data['id'])->update(['is_play'=>1]);
        //     $this->ApiReturn(1,'推送成功'.$data['id']);
        // }else{
        //     $this->ApiReturn(0,'推送失败');
        // }
    }



    //Execute send gift
    protected function sendGifts($id,$uid,$num,$name,$price,$user_id,$fromUid,$is_play){
        $info['giftId']=$id;
        $info['uid']=$uid;
        $info['giftNum']=$num;
        $info['giftName']=$name;
        $info['giftPrice']=$price * $num;
        $info['user_id']=$user_id;
        $info['fromUid']=$fromUid;
        $info['is_play']=$is_play ? 2 : 1;
        $info['type']=2;
        $info['created_at']=$info['updated_at']=date('Y-m-d H:i:s',time());


        //Calculate the share
        $income=$this->calculate($uid,$fromUid,$info['giftPrice']);
        $info['platform_obtain']=$income['platform'];   //platform
        $info['fromUid_obtain']=$income['fromUid'];     //recipient
        $info['uid_obtain']=$income['uid']+$income['uid_yj'];//homeowner
        //get guild
        $union=Db::table('user_union')->where(['users_id' => $fromUid,'check_status'=>1])->first ();
        if ($union){
            $info['union_id']= $union['union_id'];//guild
        }
        $res=DB::table('gift_logs')->insertGetId($info);
        if($res){
            if($income['uid'] > 0) {
                //Increase guild income and records
                $union=Db::table('user_union')->where(['users_id' => $uid,'check_status'=>1])->first();
                if ($union){
                    userUnionStoreInc($uid,$income['uid'],31,'r_mibi');
                }else{
                    userStoreInc($uid,$income['uid'],31,'r_mibi');  //Room running water
                }
            }
            if($income['uid_yj'] > 0) {
                //Increase guild income and records
                $union=Db::name('user_union')->where(['users_id' => $uid,'check_status'=>1])->find();
                if ($union){
                    userUnionStoreInc($uid,$income['uid_yj'],32,'r_mibi');
                }else{
                    userStoreInc($uid,$income['uid_yj'],32,'r_mibi');    //Subordinate share of homeowners, commission
                }
            }
            if($income['fromUid'] > 0)  {
                //Increase guild income and records
                $union=Db::name('user_union')->where(['users_id' => $fromUid,'check_status'=>1])->find();
                if ($union){
                    userUnionStoreInc($fromUid,$income['fromUid'],21,'mibi');
                }else{
                    userStoreInc($fromUid,$income['fromUid'],21,'mibi');//Receive a gift
                }

            }
            if($income['platform'] > 0)  userStoreInc(0,$income['platform'],99,'mibi');  //platform income
            // userStoreDec($data['user_id'],$total_price,13,'mizuan');      //Send a Gift
            //increase room heat
            $this->addRoomHot($uid,$info['giftPrice']);
            //The total amount received by the user
            $this->update_user_total($fromUid,3,$info['giftPrice']);
            return 1;
        }else{
            return 0;
        }
    }

    //Calculate the income of all parties
    protected function calculate($uid,$fromUid,$total){
        $room_user=DB::table('users')->select(['id','is_sign','scale','is_leader'])->where('id',$uid)->first();
        $room_scale = Common::getConfig('platform_share');
        $room_scale = $room_scale ? $room_scale : 30;//Platform share
        if(!$room_user->is_sign){//non-contract homeowner
            $data['uid']=0;//Room running water
            $data['fromUid']=$total * ((100 - $room_scale)/100) ;//recipient
            $data['platform']=$total * ($room_scale/100) ;//Platform flow
            $data['uid_yj']=0;//homeowner
        }else{
            //Room running water
            $stream=$total*$room_user->scale/100;
            //platform
            $platform=$total*($room_scale-$room_user->scale)/100;
            if($room_user->is_leader){
                $scale=DB::table('leader')->where('uid',$uid)->where('user_id',$fromUid)->where('status',2)->value('scale') ? : 100;
            }else{
                $scale = 100;
            }
            //recipient
            $room_scale_sign = (100 - $room_scale)/100;
            $get_gift=$total * $room_scale_sign * $scale /100;
            $uid_yj = $total * $room_scale_sign * (100 - $scale)/100;

            $data['uid']=$stream;//Room running water
            $data['fromUid']=$get_gift;//recipient
            $data['platform']=$platform;//Platform flow
            $data['uid_yj']=$uid_yj;//homeowner
        }
        $data=array_map(function($val){
            return round($val*0.1,2);
        }, $data);
        return $data;
    }
}
