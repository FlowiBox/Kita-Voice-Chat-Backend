<?php

namespace App\Admin\Actions;

use App\Models\Admin;
use App\Models\Agency;
use App\Models\Charge;
use App\Models\CoinLog;
use App\Models\SalaryTrx;
use App\Models\User;
use Encore\Admin\Actions\Action;
use Encore\Admin\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalaryAction extends Action
{
    public $name;
    public $options = [];
    public $id ;
    public $type;

    protected $selector = '.salary_action';

    public function __construct ($id=0,$type='user')
    {
        $this->id = $id;
        $this->type = $type;
        $this->name = __ ('cashing');
        $this->options = ['agency'=>__('agency'),'agency_users'=>__('agency users')];
        parent::__construct ();
    }

    public function handle(Request $request)
    {
        $amount = \request ('amount');
        try {
            DB::beginTransaction ();
            if (\request ('id') && \request ('type') == 'agency'){
                $agency = Agency::query ()->find (\request ('id'));
                if ($agency){
                    $prev = $agency->old_usd;
                    if ($amount){
                        $agency->old_usd -= $amount;
                    }else{
                        $agency->old_usd = 0;
                    }
                    $agency->save ();
                    $m = $amount?:$agency->old_usd ;
                    if ($m > 0){
                        SalaryTrx::query ()->create (
                            [
                                'type'=>1,
                                'oid'=>$agency->id,
                                'amount'=>$amount?:$agency->old_usd,
                                't_no'=>rand (11111111,99999999),
                                'note'=>'paid via admin',
                                'before_pay'=>$prev,
                                'after_pay'=>$prev - $m,
                                'payer_id'=>auth ()->id (),
                                'payer_type'=>0
                            ]
                        );
                    }
                }
            }
            elseif (\request ('id') && \request ('type') == 'user'){
                $user = User::query ()->find (\request ('id'));
                if ($user){
                    $prev = $user->old_usd;
                    $m = $amount?:$user->old_usd ;
                    if ($amount){
                        $user->old_usd -= $amount;
                    }else{
                        $user->old_usd = 0;
                    }
                    $user->save ();
                    if ($m > 0){
                        SalaryTrx::query ()->create (
                            [
                                'type'=>0,
                                'oid'=>$user->id,
                                'amount'=>$amount?:$user->old_usd,
                                't_no'=>rand (11111111,99999999),
                                'note'=>'paid via admin',
                                'before_pay'=>$prev,
                                'after_pay'=>$prev - $m,
                                'payer_id'=>auth ()->id (),
                                'payer_type'=>0
                            ]
                        );
                    }
                }
            }
            elseif (\request ('id') && \request ('type') == 'agency_users'){
                $agency = Agency::query ()->find (\request ('id'));
                if ($agency){
                    $prev = $agency->old_usd;
                    $m = $agency->old_usd ;
                    $agency->old_usd = 0;
                    if ($m > 0){
                        SalaryTrx::query ()->create (
                            [
                                'type'=>0,
                                'oid'=>$agency->id,
                                'amount'=>$agency->old_usd,
                                't_no'=>rand (11111111,99999999),
                                'note'=>'paid via admin',
                                'before_pay'=>$prev,
                                'after_pay'=>$prev - $m,
                                'payer_id'=>auth ()->id (),
                                'payer_type'=>0
                            ]
                        );
                    }
                    $users = $agency->users;
                    foreach ($users as $user){
                        $m = $user->old_usd ;
                        $user->old_usd = 0;
                        if ($m > 0){
                            SalaryTrx::query ()->create (
                                [
                                    'type'=>0,
                                    'oid'=>$user->id,
                                    'amount'=>$user->old_usd,
                                    't_no'=>rand (11111111,99999999),
                                    'note'=>'paid via admin for agency , contact your agent for your salary',
                                    'before_pay'=>$prev,
                                    'after_pay'=>$prev - $m,
                                    'payer_id'=>auth ()->id (),
                                    'payer_type'=>0
                                ]
                            );
                        }
                        $user->save();
                    }
                    $agency->save ();
                }
            }
            DB::commit ();
        }
        catch (\Exception $exception){
            DB::rollBack ();
            return $this->response()->error(__ ('un known error'))->refresh();
        }

        return $this->response()->success('success')->refresh();

    }

    public function form()
    {
        $this->hidden('id', __('id'))->attribute ('id','vid');
        if ($this->type == 'user'){
            $this->hidden ('type','type')->value ('user');
        }
        else{
            $this->select('type', __('type'))->options ($this->options);
        }
        $this->text('amount', __('amount'))->help (__ ('let it empty to pay total'));
    }

    public function html()
    {
        return '<a href="javascript:void(0);" onclick="pu('.$this->id.')" class="btn btn-sm btn-danger salary_action ">pay</a>
<script>
function pu(val) {

  $("#vid").val(val)
}
</script>
';
    }

}
