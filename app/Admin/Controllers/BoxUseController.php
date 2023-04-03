<?php

namespace App\Admin\Controllers;

use App\Models\BoxUse;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class BoxUseController extends MainController
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
        $grid = new Grid(new BoxUse);

        $grid->id('ID');
        $grid->box_id('box_id');
        $grid->user_id('user_id');
        $grid->coins('coins');
        $grid->end_at('end_at');
        $grid->room_uid('room_uid');
        $grid->room_id('room_id');
        $grid->users_num('users_num');
        $grid->type('type');
        $grid->label('label');
        $grid->used_num('used_num');
        $grid->not_used_num('not_used_num');
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
        $show = new Show(BoxUse::findOrFail($id));

        $show->id('ID');
        $show->box_id('box_id');
        $show->user_id('user_id');
        $show->coins('coins');
        $show->end_at('end_at');
        $show->room_uid('room_uid');
        $show->room_id('room_id');
        $show->users_num('users_num');
        $show->type('type');
        $show->label('label');
        $show->used_num('used_num');
        $show->not_used_num('not_used_num');
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
        $form = new Form(new BoxUse);

        $form->display('ID');
        $form->text('box_id', 'box_id');
        $form->text('user_id', 'user_id');
        $form->text('coins', 'coins');
        $form->text('end_at', 'end_at');
        $form->text('room_uid', 'room_uid');
        $form->text('room_id', 'room_id');
        $form->text('users_num', 'users_num');
        $form->text('type', 'type');
        $form->text('label', 'label');
        $form->text('used_num', 'used_num');
        $form->text('not_used_num', 'not_used_num');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
