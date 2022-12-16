<?php

namespace App\Admin\Controllers;

use App\Models\Union;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UnionController extends Controller
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
        $grid = new Grid(new Union);

        $grid->id('ID');
        $grid->img('img');
        $grid->nickname('nickname');
        $grid->notice('notice');
        $grid->contents('contents');
        $grid->phone('phone');
        $grid->url('url');
        $grid->status('status');
        $grid->users_id('users_id');
        $grid->check_time('check_time');
        $grid->check_uid('check_uid');
        $grid->check_status('check_status');
        $grid->admin_id('admin_id');
        $grid->share('share');
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
        $show = new Show(Union::findOrFail($id));

        $show->id('ID');
        $show->img('img');
        $show->nickname('nickname');
        $show->notice('notice');
        $show->contents('contents');
        $show->phone('phone');
        $show->url('url');
        $show->status('status');
        $show->users_id('users_id');
        $show->check_time('check_time');
        $show->check_uid('check_uid');
        $show->check_status('check_status');
        $show->admin_id('admin_id');
        $show->share('share');
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
        $form = new Form(new Union);

        $form->display('ID');
        $form->text('img', 'img');
        $form->text('nickname', 'nickname');
        $form->text('notice', 'notice');
        $form->text('contents', 'contents');
        $form->text('phone', 'phone');
        $form->text('url', 'url');
        $form->text('status', 'status');
        $form->text('users_id', 'users_id');
        $form->text('check_time', 'check_time');
        $form->text('check_uid', 'check_uid');
        $form->text('check_status', 'check_status');
        $form->text('admin_id', 'admin_id');
        $form->text('share', 'share');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
