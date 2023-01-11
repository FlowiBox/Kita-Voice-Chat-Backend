<?php

namespace App\Admin\Actions;

use App\Models\Admin;
use App\Models\Charge;
use App\Models\User;
use Encore\Admin\Actions\Action;
use Encore\Admin\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChargeAction extends Action
{
    public $name = 'Charge';

    protected $selector = '.charge_action';

    public function handle(Request $request)
    {
        if ($request->user_type == 'app'){
            $user = User::query ()->find ($request->user_id);
            if (!$user){
                return $this->response()->error('user not found')->refresh();
            }
        }elseif ($request->user_type == 'dash'){
            $user = Admin::query ()->find ($request->user_id);
            if (!$user){
                return $this->response()->error('user not found')->refresh();
            }
        }else{
            return $this->response()->error('system need to know what type of user you want add balance to')->refresh();
        }

        if ($request->amount < 10){
            return $this->response()->error('amount must be more than 10')->refresh();
        }

        DB::beginTransaction ();
        try {
            $charge = new Charge();
            $charge->charger_id = Auth::id ();
            $charge->charger_type = 'dash';
            $charge->user_id = $request->user_id;
            $charge->user_type = $request->user_type;
            $charge->amount = $request->amount;
            $charge->amount_type = 1;
            $charge->balance_before = $user->di;
            $user->di += $request->amount;
            $charge->save ();
            $user->save ();
            DB::commit ();
            return $this->response()->success('success')->refresh();
        }catch (\Exception $exception){
            DB::rollBack ();
            return $this->response()->error($exception->getMessage ())->refresh();
        }

    }

    public function form()
    {
        $this->hidden('charger_id', 'charger id')->value (Auth::id ());
        $this->hidden('charger_type', 'charger_type')->value ('dash');
        $this->text('user_id', __('user id'));
        $this->select('user_type', __('user type'))->options (['app'=>__ ('app'),'dash'=>__ ('dash')])->default ('app');
        $this->text('amount', __('amount'));
        $this->hidden('amount_type', 'amount_type')->value (1);
    }

    public function html()
    {
        return <<<HTML

    <li><a href="javascript:void(0);" class="charge_action "><i class="fa fa-dollar text-red"></i> add balance</a></li>

HTML;
    }
}
