<?php

namespace App\Admin\Actions;

use App\Helpers\Common;
use App\Models\Admin;
use App\Models\Agency;
use App\Models\Ban;
use App\Models\Charge;
use App\Models\CoinLog;
use App\Models\User;
use Encore\Admin\Actions\Action;
use Encore\Admin\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RemoveBanUser extends Action
{
    public $name;

    protected $selector = '.remove_ban_user_action';

    public function handle(Request $request)
    {

        $user = User::query ()->where ('uuid',$request->uid)->first ();
        if (!$user){
            return $this->response()->error(__('user not found'))->refresh();
        }
        Ban::query ()->where ('uid',$request->uid)->delete ();
        return $this->response()->success('success')->refresh();

    }

    public function form()
    {
        $this->integer('uid', __('uuid'));
    }

    public function html()
    {
        return <<<HTML

    <li><a href="javascript:void(0);" class="remove_ban_user_action "><i class="fa fa-dollar text-red"></i> Remove Ban</a></li>

HTML;
    }
}
