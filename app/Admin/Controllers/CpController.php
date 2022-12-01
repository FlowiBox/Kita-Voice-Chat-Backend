<?php

namespace App\Admin\Controllers;

use App\Models\Cp;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CpController extends Controller
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
        $grid = new Grid(new Cp);

        $grid->id('ID');
        $grid->uid('uid');
        $grid->wares_id('wares_id');
        $grid->num('num');
        $grid->user_id('user_id');
        $grid->fromUid('fromUid');
        $grid->status('status');
        $grid->exp('exp');
        $grid->addtime('addtime');
        $grid->agreetime('agreetime');
        $grid->refusetime('refusetime');
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
        $show = new Show(Cp::findOrFail($id));

        $show->id('ID');
        $show->uid('uid');
        $show->wares_id('wares_id');
        $show->num('num');
        $show->user_id('user_id');
        $show->fromUid('fromUid');
        $show->status('status');
        $show->exp('exp');
        $show->addtime('addtime');
        $show->agreetime('agreetime');
        $show->refusetime('refusetime');
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
        $form = new Form(new Cp);

        $form->display('ID');
        $form->text('uid', 'uid');
        $form->text('wares_id', 'wares_id');
        $form->text('num', 'num');
        $form->text('user_id', 'user_id');
        $form->text('fromUid', 'fromUid');
        $form->text('status', 'status');
        $form->text('exp', 'exp');
        $form->text('addtime', 'addtime');
        $form->text('agreetime', 'agreetime');
        $form->text('refusetime', 'refusetime');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
