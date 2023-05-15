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

class BanUser extends Action
{
    public $name;

    protected $selector = '.ban_user_action';

    public function handle(Request $request)
    {

        $user = User::query ()->where ('uuid',$request->uid)->first ();
        if (!$user){
            return $this->response()->error(__('user not found'))->refresh();
        }
        if (in_array ('ip',$request->type)){
            $ips = $user->ips;
            foreach ($ips as $ip){
                Ban::query ()->create (
                    [
                        'uid'=>$request->uid,
                        'duration'=>$request->duration,
                        'ip'=>$ip->ip,
                        'type'=>'all',
                        'user_type'=>0,
                        'staff_id'=>Auth::id ()
                    ]
                );
            }
        }
        if (in_array ('device',$request->type)){
            Ban::query ()->create (
                [
                    'uid'=>$request->uid,
                    'duration'=>$request->duration,
                    'device_number'=>$user->device_token,
                    'type'=>'all',
                    'user_type'=>0,
                    'staff_id'=>Auth::id ()
                ]
            );
        }
        if (in_array ('normal',$request->type)){
            Ban::query ()->create (
                [
                    'uid'=>$request->uid,
                    'duration'=>$request->duration,
                    'type'=>'all',
                    'user_type'=>0,
                    'staff_id'=>Auth::id ()
                ]
            );
        }

        return $this->response()->success('success')->refresh();

    }

    public function form()
    {
        $this->integer('uid', __('uuid'));
        $this->integer('duration', __('duration(hours) : empty if forever'));
        $this->checkbox('type', __('type'))->options ([
            'normal' => __('normal'),
            'ip' => __('ip'),
            'device' => __('device'),
        ]);
    }

    public function html()
    {
        return <<<HTML

    <li><a href="javascript:void(0);" class="ban_user_action "><i class="fa fa-dollar text-red"></i> Ban</a></li>

HTML;
    }
}
