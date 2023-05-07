<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\GiftResource;
use App\Models\Gift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftController extends Controller
{
    public function index(Request $request){
        $user = $request->user ();
        $gifts = Gift::query ()->where ('enable',1);
        if ($request->type){
            $gifts = $gifts->where ('type',$request->type);
        }
        $gifts = $gifts->orderBy ('sort')->get ();
        return Common::apiResponse (true,'',GiftResource::collection ($gifts),200);
    }


    //gift list
    public function gift_list()
    {
        $user_id=$this->user_id;
        $RedisCache=new RedisCache;
        //Gift
        $gifts=$RedisCache->getRedisData('room','gift_list',60);
        //宝石
        $baoshi=$RedisCache->getRedisData('room','baoshi_list');

        //我的
        //宝石
        $my_baoshi=Db::name('pack')->where(['a.type'=>1,'a.user_id'=>$user_id,'b.enable'=>1])
            ->alias('a')
            ->join('wares b','a.target_id = b.id')
            ->field('a.id,a.num,a.target_id,b.get_type,a.expire,b.name,b.price,b.img1,b.img2,b.show_img,b.type')
            ->select();

        //爆音卡
        $my_baoyin=Db::name('pack')->where(['a.type'=>3,'a.user_id'=>$user_id,'a.target_id'=>6,'b.enable'=>1])
            ->alias('a')
            ->join('wares b','a.target_id = b.id')
            ->field('a.id,a.num,a.target_id,b.get_type,a.expire,b.name,b.price,b.img1,b.img2,b.show_img,b.type')
            ->select();
        $data=array_merge($my_baoshi,$my_baoyin);
        foreach ($data as $k2 => &$v2) {
            //四期
            $v2['price_004']= $v2['get_type'] == 4 ? $v2['price'] : get_wares_allway($v2['get_type']);
            //四期前
            $v2['id']=$v2['target_id'];
            $v2['price']= "x".$v2['num'];
            $v2['img']=$this->auth->setFilePath($v2['img1']);
            $v2['show_img']=$this->auth->setFilePath($v2['show_img']);
            $v2['show_img2']=$this->auth->setFilePath($v2['img2']);
            $v2['wares_type']=$v2['type'];
            $v2['type']=$v2['show_img2'] ? 2 : 1;
        }
        unset($v2);
        //我的礼物
        $my_gift=Db::name('pack')->where(['a.type'=>2,'a.user_id'=>$user_id])->alias('a')
            ->join('gifts b','a.target_id = b.id')
            ->field('a.id,a.num,a.target_id,a.get_type,b.name,b.price,b.img,b.show_img,b.show_img2,b.type')
            ->order("price asc")
            ->select();
        foreach ($my_gift as $k3 => &$v3){
            //四期
            $v3['price_004']= $v3['price'];
            //四期前
            $v3['id']=$v3['target_id'];
            $v3['wares_type'] = 2;
            $v3['price']="x".$v3['num'];
            $v3['img']=$this->auth->setFilePath($v3['img']);
            $v3['show_img']=$this->auth->setFilePath($v3['show_img']);
            $v3['show_img2']=$this->auth->setFilePath($v3['show_img2']);
        }
        unset($v3);
        $res_arr=array_merge($data,$my_gift);
        $n=0;
        foreach ($res_arr as $k => &$va) {
            $va['is_check']=$n ? 0 : 1;
            $n++;
            $va['e_name']='';
        }
        unset($va);
        $mizuan=DB::name('users')->where('id',$user_id)->value('mizuan');
        $arr['gifts']=$gifts;
        $arr['baoshi']=$baoshi;
        $arr['my_wares']=$res_arr;
        $arr['mizuan']=$mizuan;
        $this->ApiReturn(1,'获取成功',$arr);
    }


    protected function gift_list1(){
        $data = DB::table('gifts')->where(['hot'=>['elt',900],'enable'=>1])
            ->orderByRaw('sort desc, price asc')
            ->select(['id','name','price','img','type','show_img','show_img2'])->get();
        $i=0;
        foreach ($data as $key => &$v) {
            $v->is_check = $i ? 0 : 1;
            $i++;
            //Phase four
            $v->price_004=$v->price;
            //Four periods ago
            $v->price=$v->price.'diamond';
            $v->wares_type=2;
            $v->e_name='';
        }
        return $data;
    }

    //宝石数据
    protected function baoshi_list(){
        $data=Db::name('wares')->where(['type'=>1,'enable'=>1])->field('id,name,price,img1,show_img,img2,get_type,type')->select();
        $j=0;
        foreach ($data as $k1 => &$v1) {
            $v1['is_check']= $j ? 0 : 1;
            $j++;
            //四期
            $v1['price_004']= $v1['get_type'] == 4 ? $v1['price'] : get_wares_allway($v1['get_type']);
            //四期前
            $v1['price']= $v1['get_type'] == 4 ? $v1['price'].'钻石' : get_wares_allway($v1['get_type']);
            $v1['img']=$this->auth->setFilePath($v1['img1']);
            $v1['show_img']=$this->auth->setFilePath($v1['show_img']);
            $v1['show_img2']=$this->auth->setFilePath($v1['img2']);
            $v1['type']= $v1['show_img2'] ? 2 : 1;
            $v1['wares_type']=1;
            $v1['e_name']='';
        }
        return $data;
    }












}
