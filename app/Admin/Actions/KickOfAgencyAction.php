<?php

namespace App\Admin\Actions;

use App\Helpers\Common;
use App\Models\AgencyJoinRequest;
use App\Models\FamilyUser;
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

class KickOfAgencyAction extends RowAction
{
    public $name = 'طرد من الوكالة';

    public function handle(Model $model, Request $request)
    {

        $model->agency_id = 0;

        AgencyJoinRequest::query ()->where ('user_id',$model->id)->delete ();

        $model->save ();

        return $this->response()->success ('تم بنجاح');
    }

    public function dialog()
    {
        $this->confirm('هل متاكد من انك تريد طرده من الوكالة ؟','',[]);
    }
}
