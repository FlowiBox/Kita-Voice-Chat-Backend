<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * لائحة الطلبات
     * @param int          keywords             Order status: 1 pending payment 2 pending order 3 pending service 4 in progress 8 refund/complaint
     * @param int          type                 1 as user 2 as master
     * @param int          page                 分页
     */
    public function go_order_list(Request $request){
        $user_id=$request->user ()->id;
        $keywords = $request->keywords;
        $type = $request->type;
        $page = $request->page;
        if(!in_array($type,[1,2])) return Common::apiResponse(0, 'Parameter error',null,422);
        if($type == 1){
            $field='user_id';
        }elseif($type == 2){
            if($keywords == 1) return Common::apiResponse(1,'');  //That's right
            $field='master_id';
            $where['status']=['notlike',1];
        }
        if($keywords)   $where['status']=['like',$keywords."%"];
        $where[$field]=$user_id;
        $data=DB::table('gm_orders')->where($where)
            ->selectRaw('reason,images,out_refund_no')
            ->orderByRaw('addtime desc')
            ->forPage($page,10)
            ->get();
        $data=Common::gmOrderDataFormat($data,$type);
        return Common::apiResponse(1,'',$data);
    }

    /**
     * game order
     * @param int          skill_apply_id       user skill id
     * @param int          start_time           service time, timestamp
     * @param int          num                  Serving Quantity
     * @param string       remarks              Remark
     * @param int          coupon_id            coupon id
     */
    public function go_add_order(Request $request){
        $user_id=$request->user ()->id;
        $id=$request->skill_apply_id ?:0;
        $start_time=$request->start_time?:0;
        $num=$request->num?:0;
        $remarks=$request->remarks?:'';
        $coupon_id=$request->coupon_id?:0;
        $t=time();
        if(!$start_time || $start_time < $t) return Common::apiResponse (0,'The service time cannot be empty or less than the current time',null,422);

        if($num  <= 0 )	return Common::apiResponse (0,'Serving Quantity Wrong',null,400);

        $apply_data=Db::table('skill_apply')->where(['id'=>$id,'status'=>1])->selectRaw('id,skill_id,user_id,gm_price_id,is_open')->first();
        if(!$apply_data)	return Common::apiResponse(0,'Great master skill information is wrong');
        if($apply_data['is_open'] != 1)	return Common::apiResponse(0,'master is currently not accepting orders');
        if($apply_data['user_id'] == $user_id)	return Common::apiResponse(0,'Can\'t place an order on myself');
        $price_data=Db::table('gm_price')->where(['id'=>$apply_data['gm_price_id']])->selectRaw('id,price,unit')->first();
        if(!$price_data)	return Common::apiResponse(0,'wrong price');

        $total_price = $price_data['price'] * $num;
        //10% commission if not in the family, 20% commission if in the family
        $ratio=Common::getFeeRatio($apply_data['user_id']);
        $fee = round($total_price * $ratio, 2);
        $real_price = round($total_price * (1-$ratio) , 2);

        //coupon
        $coupon_price=0;

        if($coupon_id){
            $coupon=Db::table('user_coupons')->where(['user_id'=>$user_id,'id'=>$coupon_id,'status'=>1])->first();
            if(!$coupon)    return Common::apiResponse(0,'There is no such coupon yet or it has been used');
            $wares=Db::table('wares')->where(['id'=>$coupon['wares_id'],'enable'=>1])->first();
            if(!$wares) return Common::apiResponse(0,'There is no such item or it has been taken off the shelf');
            $coupon_price= $total_price - $wares['price'];
            $coupon_price= ($coupon_price > 0 ) ? $coupon_price : 0;
        }
        $arr=[
            'skill_apply_id'=>$id,
            'order_no'      =>Common::getOrderNo('GM'),
            'user_id'		=>$user_id,
            'master_id'		=>$apply_data['user_id'],
            'skill_id'		=>$apply_data['skill_id'],
            'start_time'	=>$start_time,
            'num'			=>$num,
            'remarks'		=>$remarks,
            'price'			=>$price_data['price'],
            'unit'			=>$price_data['unit'],
            'total_price'	=>$coupon_id ? $coupon_price : $total_price,
            'fee'			=>$fee,
            'real_price'	=>$real_price,
            'addtime'		=>$t,
            'coupon_id'     =>$coupon_id,
            'coupon_price'  =>$coupon_id ? $wares['price'] : 0,
            'f_user_id'     =>Common::getFeeRatio($apply_data['user_id'],2),//patriarch id
            'union_id'      =>Common::getUnionFeeRatio($apply_data['user_id'],2),//guild id
        ];

        DB::beginTransaction();
        try {
            $res=Db::table('gm_orders')->insertGetId($arr);
            $note='The order is placed successfully, please pay as soon as possible';
            if($coupon_id){
                //Paid
                if($arr['total_price'] == 0){
                    //Deduct user whale coins and records
                    $arr_up['status']=2;
                    $arr_up['pay_type']=3;
                    $arr_up['paytime']=time();
                    $res2=DB::table('gm_orders')->where(['id'=>$res])->update($arr_up);
                    // Task - first order
                    Common::fin_task($user_id,5);
                }
                $note='successfully ordered';
                // Task - first order
                Common::fin_task($user_id,5);
                //Coupon used
                Db::table('user_coupons')->where(['id'=>$coupon_id])->update(['status'=>2]);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return Common::apiResponse(0,'Failed to place an order, please refresh and try again',null,400);
        }
        return Common::apiResponse(1,$note,['order_id'=>$res]);
    }

}
