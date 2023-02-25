<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Agency;
use App\Http\Controllers\Controller;
use App\Traits\AdminTraits\AdminUserTrait;
use Encore\Admin\Admin;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AgencyController extends MainController
{
    use HasResourceActions,AdminUserTrait;

    public $permission_name = 'agencies';
    public $hiddenColumns = [

    ];









    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {





        $grid = new Grid(new Agency);



        $grid->id('ID');
        $grid->column('owner_id',trans ('owner id'))->modal ('owner info',function ($model){
            return Common::getAdminShow ($model->owner_id);
        });
        $grid->column('name',trans ('name'));
        $grid->column('notice',trans ('notice'));
        $grid->column('status',trans ('status'))->switch(Common::getSwitchStates ());
        $grid->column('phone',trans ('phone'));
        $grid->column('img',trans ('img'))->image ('',30);

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
        $show = new Show(Agency::findOrFail($id));

        $show->id('ID');
        $show->field('owner_id',__('owner_id'));
        $show->field('name',__ ('name'));
        $show->field('notice',__ ('notice'));
        $show->field('status',__('status'));
        $show->field('phone',__ ('phone'));
        $show->field('url',__ ('url'));
        $show->field('img',__ ('image'))->image ('',200);
        $show->field('contents',__ ('contents'));

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

        $ops = [];
        foreach ($this->getAgencies () as $user){
            $ops[$user->id]=$user->name;
        }

        $form = new Form(new Agency);

        $form->display('ID');
        $form->select('owner_id', __('owner id'))->options ($ops);
        $form->text('name', __('name'));
        $form->text('notice', __('notice'));
        $form->switch('status', __('status'));
        $form->text('phone', __('phone'));
        $form->url('url', __('url'));
        $form->image('img', __('img'));
        $form->textarea('contents', __('contents'));
        return $form;
    }
}
