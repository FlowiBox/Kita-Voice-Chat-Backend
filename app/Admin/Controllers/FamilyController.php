<?php

namespace App\Admin\Controllers;

use App\Models\Family;
use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class FamilyController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'family';
    public $hiddenColumns = [

    ];


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Family);

        $grid->id('ID');
//        $grid->is_success('is_success');
        $grid->column('image',__('image'))->image ('',30);
        $grid->column('name',__ ('name'));
        $grid->column('introduce',__ ('introduce'));
        $grid->column('notice',__ ('notice'));
        $grid->column('num',__ ('number of people'));
        $grid->column('user_id',__ ('user id'));
        $grid->column('speakswitch',__ ('speak switch'));
        $grid->column('status',__ ('status'));
//        $grid->update_user_id('update_user_id');
//        $grid->suctime('suctime');
//        $grid->start_time('start_time');
//        $grid->created_at(trans('admin.created_at'));
//        $grid->updated_at(trans('admin.updated_at'));
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
        $show = new Show(Family::findOrFail($id));

        $show->id('ID');
//        $show->is_success('is_success');
        $show->image(__('image'));
        $show->name(__('name'));
        $show->introduce(__('introduce'));
        $show->notice(__('notice'));
        $show->num(__('number of people'));
        $show->user_id(__('user id'));
        $show->speakswitch(__('speak switch'));
        $show->status(__('status'));
//        $show->update_user_id('update_user_id');
//        $show->suctime('suctime');
//        $show->start_time('start_time');
//        $show->created_at(trans('admin.created_at'));
//        $show->updated_at(trans('admin.updated_at'));
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
        $form = new Form(new Family);

        $form->display('ID');
        $form->hidden('is_success', 'is_success')->default (1);
        $form->text('image', __('image'));
        $form->text('name', __('name'));
        $form->text('introduce', __('introduce'));
        $form->text('notice', __('notice'));
        $form->text('num', __('number of people'));
        $form->text('user_id', __('user id'));
        $form->text('speakswitch', __('speak switch'));
        $form->text('status', __('status'));
//        $form->text('update_user_id', 'update_user_id');
//        $form->text('suctime', 'suctime');
//        $form->text('start_time', 'start_time');
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
