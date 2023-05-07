<?php

namespace App\Admin\Controllers\AgencyControllers;

use App\Admin\Actions\ChargeAction;
use App\Admin\Controllers\MainController;
use App\Helpers\Common;
use App\Models\Agency;
use App\Models\Charge;
use App\Models\Country;
use App\Models\User;
use App\Models\Ware;
use App\Traits\AdminTraits\AdminControllersTrait;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\App;


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
        $grid->filter (function (Grid\Filter $filter){
            $filter->expand ();
            $filter->column(1/2, function ($filter) {
                $filter->equal('uuid',__ ('uuid'));
            });

        });
        $grid->column('id', __('Id'));
        $grid->column ('uuid','uuid');

        $grid->column('name', __('Name'));
        $grid->column('nickname', __('NickName'));
        $grid->column('email', __('Email'));
        $grid->column('phone', __('Phone'));

        $grid->column ('agency_id',__ ('agency id'))->modal ('agency info',function ($model){
            if ($model->agency_id){
                return Common::getAgencyShow ($model->agency_id);
            }
            return null;
        });

        $grid->disableActions ();
        $grid->disableCreateButton ();

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





        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */





}
