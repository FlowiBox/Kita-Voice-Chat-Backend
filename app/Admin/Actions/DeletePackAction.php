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

class DeletePackAction extends RowAction
{
    public $name = 'Delete';

    public function handle(Model $model, Request $request)
    {
        $model->delete ();
        return $this->response()->success ('تم بنجاح');
    }

    public function dialog()
    {
        $this->confirm('هل متاكد من انك تريد حذف هذا العنصر ؟','',[]);
    }
}
