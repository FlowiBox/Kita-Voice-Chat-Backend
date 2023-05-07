<?php

namespace App\Admin\Controllers;

use App\Models\UserTask;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserTaskController extends Controller
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
        $grid = new Grid(new UserTask);

        $grid->id('ID');
        $grid->user_id('user_id');
        $grid->not_fin_1('not_fin_1');
        $grid->fin_1('fin_1');
        $grid->receive_1('receive_1');
        $grid->fin_2('fin_2');
        $grid->receive_2('receive_2');
        $grid->is_open('is_open');
        $grid->addtime('addtime');
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
        $show = new Show(UserTask::findOrFail($id));

        $show->id('ID');
        $show->user_id('user_id');
        $show->not_fin_1('not_fin_1');
        $show->fin_1('fin_1');
        $show->receive_1('receive_1');
        $show->fin_2('fin_2');
        $show->receive_2('receive_2');
        $show->is_open('is_open');
        $show->addtime('addtime');
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
        $form = new Form(new UserTask);

        $form->display('ID');
        $form->text('user_id', 'user_id');
        $form->text('not_fin_1', 'not_fin_1');
        $form->text('fin_1', 'fin_1');
        $form->text('receive_1', 'receive_1');
        $form->text('fin_2', 'fin_2');
        $form->text('receive_2', 'receive_2');
        $form->text('is_open', 'is_open');
        $form->text('addtime', 'addtime');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
