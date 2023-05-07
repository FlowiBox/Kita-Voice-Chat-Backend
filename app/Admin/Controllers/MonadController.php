<?php

namespace App\Admin\Controllers;

use App\Models\Monad;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MonadController extends Controller
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
        $grid = new Grid(new Monad);

        $grid->id('ID');
        $grid->uid('uid');
        $grid->skill_id('skill_id');
        $grid->service_time('service_time');
        $grid->remark('remark');
        $grid->addtime('addtime');
        $grid->endtime('endtime');
        $grid->status('status');
        $grid->adduser('adduser');
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
        $show = new Show(Monad::findOrFail($id));

        $show->id('ID');
        $show->uid('uid');
        $show->skill_id('skill_id');
        $show->service_time('service_time');
        $show->remark('remark');
        $show->addtime('addtime');
        $show->endtime('endtime');
        $show->status('status');
        $show->adduser('adduser');
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
        $form = new Form(new Monad);

        $form->display('ID');
        $form->text('uid', 'uid');
        $form->text('skill_id', 'skill_id');
        $form->text('service_time', 'service_time');
        $form->text('remark', 'remark');
        $form->text('addtime', 'addtime');
        $form->text('endtime', 'endtime');
        $form->text('status', 'status');
        $form->text('adduser', 'adduser');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
