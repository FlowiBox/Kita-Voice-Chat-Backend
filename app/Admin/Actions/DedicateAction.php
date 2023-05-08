<?php

namespace App\Admin\Actions;

use App\Helpers\Common;
use App\Models\OVip;
use App\Models\Pack;
use App\Models\User;
use App\Models\UserVip;
use App\Models\Ware;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DedicateAction extends RowAction
{
    public $name = 'Dedicate';

    public function handle(Model $model, Request $request)
    {
        $user = User::query ()->where ('uuid',$request->user_uuid)->first ();
        if (!$user){
            return $this->response()->error('المستخدم غير موجود')->refresh();
        }
        if($model instanceof Ware){
            $ware = $model;
            $pack = Pack::query ()->where ('user_id',$user->id)->where ('target_id',$ware->id)->first ();
            if($pack){
                if ($pack->expire == 0) return $this->response()->error ('العنصر موجود لدى المستخدم و غير قابل للانتهاء')->refresh();
                if ($pack->expire > now ()->timestamp) {
                    if ($ware->expire != 0){
                        DB::beginTransaction ();
                        try {
                            $pack->expire += ($ware->expire * 86400);
                            $pack->save ();
                            DB::commit ();
                            Common::sendOfficialMessage ($user->id,__('congratulations'),__('لقد حصلت على اهداء'));
                            return $this->response()->success ('تم بنجاح');
                        }catch (\Exception $exception){
                            DB::rollBack ();
                            return $this->response()->error ('خطا غير متوقع');
                        }

                    }else{
                        return $this->response()->error ('العنصر موجود لدى المستخدم');
                    }
                }else{
                    $pack->delete ();
                }
            }
            DB::beginTransaction ();
            try {
                $arr['user_id']=$user->id;
                $arr['type']=$ware->type;
                $arr['get_type']=$ware->get_type;
                $arr['target_id']=$ware->id;
                $arr['num']=1;//$qty;
                $arr['expire']= $ware->expire ? time()+($ware->expire * 86400) : 0;
                $arr['is_read']=1;
                Pack::query ()->create ($arr);
                DB::commit ();
                return $this->response()->success('تم بنجاح');
            }catch (\Exception $exception){
                DB::rollBack ();
                return $this->response()->error('خطا غير متوقع');
            }
        }elseif ($model instanceof OVip){
            $vip = $model;
            DB::beginTransaction ();
            try {
                UserVip::query ()->create (
                    [
                        'type'=>1,
                        'sender_id'=>0,
                        'user_id'=>$user->id,
                        'vip_id'=>$vip->id,
                        'level'=>$vip->level,
                        'expire'=>$request->days?:1,
                        'qty'=>1,
                        'price'=>0,
                        'total'=>0
                    ]
                );
                Common::handelVip ($vip,$user);
                DB::commit ();
                Common::sendOfficialMessage ($user->id,__('congratulations'),__('you obtained new vip level as a gift'));
                return $this->response()->success ('تم بنجاح');
            }catch (\Exception $exception){
                DB::rollBack ();
                return $this->response()->error('خطا.')->refresh();
            }

        }else{
            return $this->response()->error('خطا.')->refresh();
        }
    }

    public function form()
    {
        $this->integer('days', 'days');
        $this->integer('user_uuid', 'user uuid');
    }
}
