<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\ChargeAction;
use App\Helpers\Common;
use App\Models\Agency;
use App\Models\Charge;
use App\Models\Country;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Doctrine\Common\Cache\Psr6\get;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    public function __construct ()
    {
        $this->title ='Users';
    }

    public function index ( Content $content )
    {
        return $content
            ->title(__($this->title))
            ->row(function($row) {
                $row->column(10, $this->grid());
                $row->column(2, view('admin.grid.users.actions'));
//                $row->column(2, new ChargeAction());
            });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());
        $grid->quickSearch ();
        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('nickname', __('NickName'));
        $grid->column('email', __('Email'));
//        $grid->column('charge', __('Charge'))->modal('charge',function ($user){
//            $form = (new Form(new Charge))->setAction (route ('admin.charges.new'));
//            $form->hidden('charger_id', 'charger id')->value (Auth::id ());
//            $form->hidden('charger_type', 'charger_type')->value ('dashboard');
//            $form->hidden('user_id', 'user id')->value ($user->id);
//            $form->hidden('user_type', 'user type')->value ('app');
//            $form->number('amount', 'amount');
//            $form->hidden('amount_type', 'amount_type')->value (1);
//
//            return $form;
//        });
//        $grid->column('isOnline', __('isOnline'));
        $grid->column('di', __('coins'));
        $grid->column('gold', __('game coins'));
        $grid->column('coins', __('diamonds'));
        $grid->column('is_host', __('is host'))->bool ();
        $grid->column('status', __('is blocked'))->switch (Common::getSwitchStates2 () );
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());

        $form->text('name', __('Name'));
        $form->select ('country_id',trans ('country'))->options (function (){
            $ops = [];
            $countries = Country::all ();
            foreach ($countries as $country){
                $ops[$country->id] = App::isLocale('en') ? $country->e_name : $country->name;
            }
            return $ops;
        });
        $form->select ('agency_id',__('agency'))->options(function (){
            $ops = [0=>'no agency'];
            $agencies = Agency::query ()->where ('status',1)->get ();
            foreach ($agencies as $agency){
                $ops[$agency->id]=$agency->name;
            }
            return $ops;
        });
//        $form->currency ('di',__('Diamonds'));
        $form->email('email', __('Email'));
        $form->password('password', __('Password'));

        $form->switch ('status',__('is blocked'))->options (Common::getSwitchStates2 ());

        return $form;
    }


}
