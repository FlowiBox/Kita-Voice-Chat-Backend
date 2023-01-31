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


    public function update ( $id )
    {
//        dd ($this->form ());
//        if ($this->form ()->input ('password'))
        return $this->form()->update($id);
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
        $grid->filter (function (Grid\Filter $filter){
//            $filter->expand ();
            $filter->column(1/2, function ($filter) {
                $filter->equal('is_host',__('is host'))->select([0=>'normal',1=>'host']);
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('agency_id',__('agency'))->select(Common::by_agency_filter ());
                $filter->equal('family_id',__('Family'))->select(Common::by_family_filter ());
            });
        });
        $grid->column('id', __('Id'));
        $grid->column ('uuid','uuid')->editable ();
        $grid->column ('is_gold_id',__ ('Gold id'))->switch (Common::getSwitchStates ());
        $grid->column('name', __('Name'));
        $grid->column('nickname', __('NickName'));
        $grid->column('email', __('Email'));
        $grid->column('phone', __('Phone'));
        $grid->column('di', __('coins'));
        $grid->column('gold', __('silver coins'));
        $grid->column('coins', __('diamonds'));
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
        $show->field('uuid', __('uuid'));
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

        $form->display('id', __('id'));
        $form->text('uuid', __('uuid'));
        $form->switch ('is_gold_id',__('Gold id'))->states (Common::getSwitchStates ());
        $form->text('name', __('Name'));
        $form->select ('country_id',trans ('country'))->options (function (){
            $ops = [0=>'no country'];
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
        $form->email('email', __('Email'))->attribute ('onfocus',"this.removeAttribute('readonly');")->attribute ('readonly');
        $form->password('password', __('Password'))->attribute ('onfocus',"this.removeAttribute('readonly');")->attribute ('readonly');
        $form->text('phone', __('phone'));
        $form->text('facebook_id', __('facebook id'));
        $form->text('google_id', __('google id'));
//        $form->switch ('is_host',__('is host'))->options (Common::getSwitchStates ());
        $form->switch ('status',__('block status'))->options (Common::getSwitchStates2 ());

//        $form->select ('dre')

        return $form;
    }




}
