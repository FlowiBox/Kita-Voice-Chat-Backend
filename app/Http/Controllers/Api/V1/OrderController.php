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
        if(!in_array($type,[1,2])) return Common::apiResponse(0, 'Parameter error');
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

}
