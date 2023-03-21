<?php

namespace App\Admin\Controllers;

use App\Models\UserBoxGift;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserBoxGiftController extends Controller
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
        $grid = new Grid(new UserBoxGift);

        $grid->id('ID');
        $grid->box_uses_id('box_uses_id');
        $grid->user_id('user_id');
        $grid->coins('coins');
        $grid->room_uid('room_uid');
        $grid->room_id('room_id');
        $grid->type('type');
        $grid->box_uses_owner_id('box_uses_owner_id');
        $grid->image('image');
        $grid->label('label');
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
        $show = new Show(UserBoxGift::findOrFail($id));

        $show->id('ID');
        $show->box_uses_id('box_uses_id');
        $show->user_id('user_id');
        $show->coins('coins');
        $show->room_uid('room_uid');
        $show->room_id('room_id');
        $show->type('type');
        $show->box_uses_owner_id('box_uses_owner_id');
        $show->image('image');
        $show->label('label');
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
        $form = new Form(new UserBoxGift);

        $form->display('ID');
        $form->text('box_uses_id', 'box_uses_id');
        $form->text('user_id', 'user_id');
        $form->text('coins', 'coins');
        $form->text('room_uid', 'room_uid');
        $form->text('room_id', 'room_id');
        $form->text('type', 'type');
        $form->text('box_uses_owner_id', 'box_uses_owner_id');
        $form->text('image', 'image');
        $form->text('label', 'label');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
