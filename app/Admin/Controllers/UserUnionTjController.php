<?php

namespace App\Admin\Controllers;

use App\Models\UserUnionTj;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserUnionTjController extends Controller
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
        $grid = new Grid(new UserUnionTj);

        $grid->id('ID');
        $grid->union_id('union_id');
        $grid->users_id('users_id');
        $grid->real_price('real_price');
        $grid->add_time('add_time');
        $grid->add_time_month('add_time_month');
        $grid->lw_price('lw_price');
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
        $show = new Show(UserUnionTj::findOrFail($id));

        $show->id('ID');
        $show->union_id('union_id');
        $show->users_id('users_id');
        $show->real_price('real_price');
        $show->add_time('add_time');
        $show->add_time_month('add_time_month');
        $show->lw_price('lw_price');
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
        $form = new Form(new UserUnionTj);

        $form->display('ID');
        $form->text('union_id', 'union_id');
        $form->text('users_id', 'users_id');
        $form->text('real_price', 'real_price');
        $form->text('add_time', 'add_time');
        $form->text('add_time_month', 'add_time_month');
        $form->text('lw_price', 'lw_price');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
