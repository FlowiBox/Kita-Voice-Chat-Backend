<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnionController extends Controller
{
    //Get the guilds added by the user
    public function getUserUnion(Request $request){
        $user_id=$request->user ()->id;
        $where['users_id'] = intval($user_id);
        $res_user_union = DB::table('unions')->where('users_id',intval($user_id))->first();
        if ($res_user_union){
            $res_user_union->img = array_filter(explode(',',$res_user_union->img));
            return Common::apiResponse(1,'',$res_user_union);
        }else{
            return Common::apiResponse(0,'No data');
        }
    }


    //Add membership record
    public function addUnion(Request $request){
        ini_set('memory_limit','256M');
        $data = $request;
        if($request->hasFile ('image')){
           $url = Common::upload ('unions',$request->file ('image'));
           $data['url'] = $url;
        }
        if($request->hasFile ('docs')){
            $img = '';
            foreach ($request->file ('docs') as $val){
                $val=Common::upload ('unions',$val);
                $img .= $val.',';
            }
        }
        $arr = [
            'img' => isset($img) ? $img: '',
            'nickname' => $data['nickname'],
            'notice' => $data['notice'],
            'contents' => $data['contents'],
            'phone' => $data['phone'],
            'url' => isset($data['url']) ? $data['url']: '',
            'users_id' => $data['users_id'],
        ];
        if (empty($arr)) {
            return Common::apiResponse(0,'Parameter exception!');
        }
        $res_find = DB::table('unions')->where(['users_id'=> intval($data['users_id'])])->first();
        if ($res_find){
            return Common::apiResponse(0,'Already applied',$res_find);
        }

        $res = DB::table('unions')->insert($arr);
        if ($res){
            return Common::apiResponse(1,'Added successfully',$res);
        }else{
            return Common::apiResponse(0,'add failed');
        }
    }

    //Search guild data by id
    public function getSearchUnion(){
        $data = input();
        if ($data['union_id'] == ''){
            $this->ApiReturn(0,'参数错误',(object)array());
        }
        $where['id'] = intval($data['union_id']);
        $where['status'] = 1;
        $where['check_status'] = 1;
        $res = \db('union')->field('id,url,nickname,notice')->where($where)->find();
        if ($res){
            $res['url'] = $this->auth->setFilePath($res['url']);
            $res_user_union = \db('user_union')->where('union_id',intval($data['union_id']))->count();
            $res['anchor_num'] = isset($res_user_union) ? $res_user_union : 0;
            $this->ApiReturn(1,'公会数据',$res);
        }else{
            $this->ApiReturn(0,'暂无数据',(object)array());
        }
    }

    public function addUserUnion(Request $request){
        $data = $request;
        $arr = [
            'union_id' => intval($data['union_id']),
            'users_id' => intval($data['users_id']),
        ];
        if (empty($arr)) {
            return Common::apiResponse(0,'Parameter exception!');
        }
        $res_find = DB::table('user_union')->selectRaw('id,check_status')->where(['users_id'=> intval($data['users_id'])])->first();
        if ($res_find){
            if ($res_find->check_status == 0 || $res_find->check_status == 1){
                $check_status = ['Reviewing', 'Reviewed', 'Rejected'];
                return Common::apiResponse(1,'You have joined other guilds,'.$check_status[$res_find['check_status']],$res_find);
            }
        }

        $res = DB::table('user_union')->insert($arr);
        if ($res){
            return Common::apiResponse(1,'Added successfully',$res);
        }else{
            return Common::apiResponse(0,'Failed to add, please try again');
        }
    }

    public function getSearchUserUnion(){
        $user_id=$this->user_id;
        if ($user_id == ''){
            $this->ApiReturn(0,'参数错误',(object)array());
        }
        $res_user_union = \db('user_union')->field('union_id,check_status')->where('users_id',intval($user_id))->find();
        if ($res_user_union){
            $where['id'] = intval($res_user_union['union_id']);
            $where['status'] = 1;
            $where['check_status'] = 1;
            $res = \db('union')->field('id,url,nickname,notice')->where($where)->find();
            if ($res) {
                $anchor_num = \db('user_union')->where('union_id',intval($res_user_union['union_id']))->count('id');
                $res['anchor_num'] = $anchor_num;
                $res_user_union = array_merge($res_user_union,$res);
                $res_user_union['url'] = $this->auth->setFilePath($res['url']);
            }
            $this->ApiReturn(1,'我的公会',$res_user_union);
        }else{
            $this->ApiReturn(0,'暂无数据',(object)array());
        }
    }



    public function unionTj(){
        //b_gift_logs b_gm_orders


        $union_list = DB::table('user_unions')->selectRaw('id,users_id,union_id')->where(['check_status' => 1])->get();

        $start_time = date('Y-m-01 00:00:01', strtotime('-1 month'));
        $end_time = date('Y-m-t 23:59:59', strtotime('-1 month'));
        $where_gift['union_id'] = ['neq',0];
        $where_gift['created_at'] = ['between time', [$start_time, $end_time]];
        $gift_list = DB::table('gift_logs')->selectRaw('id,receiver_id,union_id,receiver_obtain')->where($where_gift)->get();
        $where_order['union_id'] = ['neq',0];
        $where_order['status'] = 5;
        $where_order['addtime'] = ['between time', [$start_time, $end_time]];
        $order_list = DB::table('gm_orders')->selectRaw('id,master_id,union_id,real_price')->where($where_order)->get();

        if ($gift_list){
            $gift_arr = [];
            foreach ($gift_list as $key => $info){
                if (!isset($gift_arr[$info->receiver_id])){
                    $gift_arr[$info->receiver_id] = $info->receiver_obtain;
                }else{
                    $gift_arr[$info->receiver_id] += $info->receiver_obtain;
                }
            }
        }

        $order_arr = [];
        if ($order_list){
            foreach ($order_list as $key => $info){
                if (!isset($order_arr[$info['master_id']])){
                    $order_arr[$info->master_id] = $info->real_price;
                }else{
                    $order_arr[$info->master_id] += $info->real_price;
                }
            }
        }

        $union_arr = [];
        if ($union_list){
            foreach ($union_list as $key => $info){
                $union_arr[$key]['union_id'] = $info->union_id;
                $union_arr[$key]['users_id'] = $info->users_id;
                $union_arr[$key]['real_price'] = isset($order_arr[$info->users_id]) ? $order_arr[$info->users_id] : 0;
                $union_arr[$key]['lw_price'] = isset($gift_arr[$info->users_id]) ? $gift_arr[$info->users_id] : 0;
                $union_arr[$key]['add_time'] = date('Y');
                $union_arr[$key]['add_time_month'] = date('n');
            }
        }
        if($union_arr){
            DB::table('user_union_tj')->insert($union_arr);
        }

    }
}
