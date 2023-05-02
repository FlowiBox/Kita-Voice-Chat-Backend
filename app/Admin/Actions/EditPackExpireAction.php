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

class EditPackExpireAction extends RowAction
{
    public $name = 'Edit';


    public function handle(Model $model, Request $request)
    {
        $ex = $request->days * 86400;

        if ($request->type == 0){
            $model->expire += $ex;
        }else{
            $model->expire -= $ex;
        }
        $model->save ();
        return $this->response()->success ('تم بنجاح')->refresh ();
    }

    public function form()
    {
        $this->radio('type',__ ('type'))->options ([0=>'رفع',1=>'خفض']);
        $this->integer('days', 'days');
    }
}
