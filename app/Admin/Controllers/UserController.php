<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\ChargeAction;
use App\Helpers\Common;
use App\Models\Agency;
use App\Models\Charge;
use App\Models\Country;
use App\Models\User;
use App\Traits\AdminTraits\AdminControllersTrait;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\App;


class UserController extends MainController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    public $permission_name = 'users';
    public $hiddenColumns = [
        'is_host',
        'status'
    ];

    public function __construct ()
    {
        $this->title ='Users';
    }

    public function index ( Content $content )
    {
        if (!Admin::user()->can('*')){
            Permission::check('browse-users');
        }

        return $content
            ->title(__($this->title))
            ->row(function($row) {
                $row->column(10, $this->grid());
                $row->column(2, view('admin.grid.users.actions'));
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
        $grid->model ()->ofAgency();
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
        $grid->column('gold', __('silver coins'));
        $grid->column('coins', __('diamonds'));
//        $grid->column('is_host', __('is host'))->switch (Common::getSwitchStates ());
        $grid->column('status', __('block status'))->switch (Common::getSwitchStates2 () );
        $grid->column ('agency_id',__ ('agency id'))->modal ('agency info',function ($model){
            if ($model->agency_id){
                return Common::getAgencyShow ($model->agency_id);
            }
            return null;
        });






        $this->extendGrid ($grid);


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
        $show->field ('avatar',__('avatar'))->image ('',200);
        $show->field('name', __('Name'));
        $show->field('nickname', __('NickName'));
        $show->field('flag', __('country'))->image ('',50);
        $show->field('email', __('Email'));
        $show->field('di', __('coins'));
        $show->field('gold', __('silver coins'));
        $show->field('coins', __('diamonds'));
        $show->field('is_host', __('is host'))->using (
            [
                0=>__ ('not host'),
                1=>__('host')
            ]
        );

        $this->extendShow ($show);


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
        $form->number ('di',__('coins'));
        $form->number ('gold',__('silver coins'));
        $form->email('email', __('Email'));
        $form->password('password', __('Password'));

//        $form->switch ('is_host',__('is host'))->options (Common::getSwitchStates ());
        $form->switch ('status',__('block status'))->options (Common::getSwitchStates2 ());

        return $form;
    }




}
