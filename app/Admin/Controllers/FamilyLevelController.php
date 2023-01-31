<?php

namespace App\Admin\Controllers;

use App\Models\FamilyLevel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class FamilyLevelController extends Controller
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
//        return $content
//            ->header(trans('admin.detail'))
//            ->description(trans('admin.description'))
//            ->body($this->detail($id));
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
        $grid = new Grid(new FamilyLevel);

        $grid->id('ID');
        $grid->column('name',__ ('name'));
        $grid->column('img',__ ('img'))->image ('',30);
        $grid->column('exp',__ ('exp'));
        $grid->column('members',__ ('members'));
        $grid->column('admins',__ ('admins'));
//        $grid->type('type');
//        $grid->created_at(trans('admin.created_at'));
//        $grid->updated_at(trans('admin.updated_at'));

        $grid->actions (function ($actions){
            $actions->disableView();
        });
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
        $show = new Show(FamilyLevel::findOrFail($id));

//        $show->id('ID');
//        $show->name('name');
//        $show->img('img');
//        $show->exp('exp');
//        $show->type('type');
//        $show->created_at(trans('admin.created_at'));
//        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new FamilyLevel);

        $form->display('ID');
        $form->text('name', __('name'));
        $form->image('img', __('img'));
        $form->number('exp', __('exp'));
        $form->number('members', __('members'));
        $form->number('admins', __('admins'));
//        $form->text('type', 'type');
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
