<?php

namespace App\Http\Controllers\Api\V1;

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
}
