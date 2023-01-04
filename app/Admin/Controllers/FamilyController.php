<?php

namespace App\Admin\Controllers;

use App\Models\Family;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class FamilyController extends Controller
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
            ->body($this->grid());
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
        $grid = new Grid(new Family);

        $grid->id('ID');
        $grid->is_success('is_success');
        $grid->image('image');
        $grid->name('name');
        $grid->introduce('introduce');
        $grid->notice('notice');
        $grid->num('num');
        $grid->user_id('user_id');
        $grid->speakswitch('speakswitch');
        $grid->status('status');
        $grid->update_user_id('update_user_id');
        $grid->suctime('suctime');
        $grid->start_time('start_time');
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

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
        $show->is_success('is_success');
        $show->image('image');
        $show->name('name');
        $show->introduce('introduce');
        $show->notice('notice');
        $show->num('num');
        $show->user_id('user_id');
        $show->speakswitch('speakswitch');
        $show->status('status');
        $show->update_user_id('update_user_id');
        $show->suctime('suctime');
        $show->start_time('start_time');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

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
        $form->text('is_success', 'is_success');
        $form->text('image', 'image');
        $form->text('name', 'name');
        $form->text('introduce', 'introduce');
        $form->text('notice', 'notice');
        $form->text('num', 'num');
        $form->text('user_id', 'user_id');
        $form->text('speakswitch', 'speakswitch');
        $form->text('status', 'status');
        $form->text('update_user_id', 'update_user_id');
        $form->text('suctime', 'suctime');
        $form->text('start_time', 'start_time');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
