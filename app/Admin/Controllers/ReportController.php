<?php
namespace App\Admin\Controllers;
use App\Admin\Extensions\AgencyExporter;
use App\Admin\Extensions\UserExporter;
use App\Helpers\Common;
use App\Models\Agency;
use App\Models\SalaryTrx;
use App\Models\User;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends MainController {

    public function index ( Content $content )
    {
        return $content
            ->title(trans('reports'))
            ->description(__(request ('desc')?:'users'))
            ->row(function($row) {
                $row->column(2, view('admin.grid.common.actions'));
                $row->column(10, $this->grid());
            });
    }

    protected function grid(){
        $name = request ('name')?:'users';
        $grid = $name;
        $grid = $this->{$grid}();
        $grid->disableActions ();
        $grid->disableCreateButton ();
        return $grid;
    }

    protected function users_grid(){
        $grid = new Grid(new User());
//        $grid->model ()->where ('target_usd','>',0)
//            ->whereNotIn ('agency_id',['',null,0])
        ;
        $grid->filter (function (Grid\Filter $filter){
            $filter->expand ();
            $filter->column(1/2, function ($filter) {
                $filter->equal('uuid',__ ('uuid'));
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('agency_id',__('agency'))->select(Common::by_agency_filter ());
            });
        });
        $grid->column ('id',__ ('id'));
        $grid->column ('agency',__ ('agency'))->display (function (){return @$this->agency->name;});
        $grid->column ('uuid',__ ('uuid'));
        $grid->column ('name',__ ('name'));
        $grid->column ('old_usd',__ ('old usd'));
        $grid->column ('target_usd',__ ('target usd'));
        $grid->column ('target_token_usd',__ ('target token usd'));
        $grid->column ('due',__ ('due'))->display (function (){
            return $this->old_usd + $this->target_usd - $this->target_token_usd;
        });
        $grid->column ('cashing',__ ('cashing'))->display (function (){
            $options = ['user'=>__('user')];
            return (new \App\Admin\Actions\SalaryAction($this->id,'user'))->render () ;
        });

        $grid->export (function ($export) {
            $export->filename('report');
//            $export->originalValue(['uuid','name','target_usd','target_token_usd']);
            $export->column('uuid', function ($value, $original) {
                return $value;
            });
        });


        return $grid;
    }

    protected function agencies_grid(){
        $grid = new Grid(new Agency());
        $grid->model ()->where ('target_usd','>',0);
        $grid->column ('id',__ ('id'));
        $grid->column ('name',__ ('name'));
        $grid->column ('phone',__ ('phone'));
        $grid->column ('old_usd',__ ('old usd'));
        $grid->column ('target_usd',__ ('target usd'));
        $grid->column ('target_token_usd',__ ('target token usd'));
        $grid->column ('due',__ ('due'))->display (function (){
            return $this->old_usd + $this->target_usd - $this->target_token_usd;
        });
        $grid->column ('users',__ ('users'))->display (function (){
            return '<a href="?name=users&desc='.$this->name.'&aid='.$this->id.'">'.$this->users()->count().'</a>';
        });
        $grid->column ('cashing',__ ('cashing'))->display (function (){
            return (new \App\Admin\Actions\SalaryAction($this->id,'agency'))->render () ;
        });

        return $grid;
    }

    public function cashing000(){
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
                        $user->coins = 0;
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
                        $user->coins = 0;
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

    protected function users(){
        if (request ('update_salary') == 'yes'){
            $users = User::query ()
                ->where ('agency_id','!=',0)
                ->where ('agency_id','!=','')
                ->where ('agency_id','!=',null)
                ->get ();
            foreach ($users as $user){
                $user->salary = 0;
                $user->save();
            }
        }

        $grid = new Grid(new User());
        $grid->model ()
            ->where ('salary','>',0)
            ->where ('agency_id','!=',0)
            ->where ('agency_id','!=','')
            ->where ('agency_id','!=',null)
        ;
        $grid->filter (function (Grid\Filter $filter){
            $filter->expand ();
            $filter->column(1/2, function ($filter) {
                $filter->equal('uuid',__ ('uuid'));
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('agency_id',__('agency'))->select(Common::by_agency_filter ());
            });
        });
        $grid->column ('id',__ ('id'));
        $grid->column ('uuid',__ ('uuid'));
        $grid->column ('name',__ ('name'));
        $grid->column ('target',__ ('target'))->display (function (){return @$this->target()->sallary?:0;});
        $grid->column ('expenses',__ ('expenses'))->display (function (){return @$this->target()->cut_amount?:0;});
        $grid->column ('old',__ ('old'))->display (function (){return $this->old?:0;});
        $grid->column ('total',__ ('salary'))->display (function (){return $this->salary?:0;});
        $grid->column ('agency',__ ('agency'))->display (function (){return @$this->agency->name;});
        $grid->column ('cashing',__ ('cashing'))->display (function (){
            $options = ['user'=>__('user')];
            return (new \App\Admin\Actions\SalaryAction($this->id,'user'))->render () ;
        });
        $grid->exporter(new UserExporter(request ('agency_id'),request ('month'),request ('year')));
        return $grid;
    }

    protected function agencies(){
        $grid = new Grid(new Agency());
        $grid->model ()->where ('salary','>',0);
        $grid->column ('id',__ ('id'));
        $grid->column ('name',__ ('name'));
        $grid->column ('target',__ ('target'))->display (function (){return @$this->target()->sallary?:0;});
        $grid->column ('expenses',__ ('expenses'))->display (function (){return @$this->target()->cut_amount?:0;});
        $grid->column ('old',__ ('old'))->display (function (){return $this->old?:0;});
        $grid->column ('total',__ ('salary'))->display (function (){return $this->salary?:0;});
        $grid->column ('agent',__ ('agent'))->display (function (){return @$this->owner->name?:@$this->dashOwner->name;;});
        $grid->column ('users',__ ('users'))->display (function (){
            return '<a href="?name=users&desc='.$this->name.'&aid='.$this->id.'">'.$this->users()->count().'</a>';
        });
        $grid->column ('cashing',__ ('cashing'))->display (function (){
            return (new \App\Admin\Actions\SalaryAction($this->id,'agency'))->render () ;
        });

        $grid->exporter(new AgencyExporter(request ('month'),request ('year')));

        return $grid;
    }



}
