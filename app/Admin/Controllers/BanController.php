<?php

namespace App\Admin\Controllers;

use App\Models\Ban;
use App\Http\Controllers\Controller;
use App\Models\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Request;

class BanController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(trans('admin.index'))
            ->description(trans('admin.description'))
            ->row(function($row) {
                $row->column(10, $this->grid());
                $row->column(2, view('admin.grid.users.ban'));
            });
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->body($this->form());
    }



    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Ban);

        $grid->id('ID');
        $grid->uid(__('uid'));
//        $grid->user_type(__('user_type'));
        $grid->duration(__('duration'));
//        $grid->type(__('type'));
        $grid->ip(__('ip'));
        $grid->device_number(__('device_number'));
        $grid->staff_id(__('staff_id'));
        $grid->created_at(trans('admin.created_at'));

        $grid->disableExport ();
        $grid->disableRowSelector ();
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
//        $show = new Show(Ban::findOrFail($id));
//
//        $show->id('ID');
//        $show->uid('uid');
//        $show->user_type('user_type');
//        $show->duration('duration');
//        $show->type('type');
//        $show->ip('ip');
//        $show->device_number('device_number');
//        $show->staff_id('staff_id');
//        $show->created_at(trans('admin.created_at'));
//        $show->updated_at(trans('admin.updated_at'));
//
//        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
//        $form = new Form(new Ban);
//
//        $form->display('ID');
//        $form->text('uid', __('uid'));
////        $form->text('user_type', __('user_type'));
//        $form->number('duration', __('duration(hours)'));
////        $form->text('type', __('type'));
//        $form->switch('ban_ip', 'ip_ban')->states ([0=>'off',1=>'on']);
//        $form->switch('device_ban', __('device_ban'))->states ([0=>'off',1=>'on']);
//        $form->display(trans('admin.created_at'));
//
//
//        return $form;
    }
}
