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

class DeleteUserVipAction extends RowAction
{
    public $name = 'Delete';

    public function handle(Model $model, Request $request)
    {
        try{
            DB::beginTransaction ();
            $model->delete ();
            $wares = Ware::query ()->where ('get_type',1)->where ('level',$model->level)->pluck ('id')->toArray ();
            Pack::query ()->whereIn ('target_id',$wares)->where ('user_id',$model->user_id)->delete ();
            $user = User::query ()->find ($model->user_id);
            if ($user){
                if ($user->vip == $model->id){
                    $uvip = UserVip::query ()->where ('user_id',$user->id)->where ('id','!=',$model->id)->orderByDesc ('level')->first ();
                    if ($uvip){
                        $user->vip = $uvip->id;
                        $user->save ();
                    }
                }
            }
            DB::commit ();
            return $this->response()->success ('تم بنجاح')->refresh ();
        }catch (\Exception $exception){
            DB::rollBack ();
            return $this->response()->error($exception->getMessage ())->refresh();
        }
    }

    public function dialog()
    {
        $this->confirm('هل متاكد من انك تريد حذف هذا العنصر ؟','',[]);
    }
}
