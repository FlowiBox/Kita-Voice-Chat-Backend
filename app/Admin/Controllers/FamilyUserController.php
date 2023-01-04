<?php

namespace App\Admin\Controllers;

use App\Models\FamilyUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class FamilyUserController extends Controller
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
        $grid = new Grid(new FamilyUser);

        $grid->id('ID');
        $grid->user_id('user_id');
        $grid->family_id('family_id');
        $grid->user_type('user_type');
        $grid->status('status');
        $grid->type('type');
        $grid->ope_user_id('ope_user_id');
        $grid->ope_time('ope_time');
        $grid->closeswitch('closeswitch');
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
        $show = new Show(FamilyUser::findOrFail($id));

        $show->id('ID');
        $show->user_id('user_id');
        $show->family_id('family_id');
        $show->user_type('user_type');
        $show->status('status');
        $show->type('type');
        $show->ope_user_id('ope_user_id');
        $show->ope_time('ope_time');
        $show->closeswitch('closeswitch');
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
        $form = new Form(new FamilyUser);

        $form->display('ID');
        $form->text('user_id', 'user_id');
        $form->text('family_id', 'family_id');
        $form->text('user_type', 'user_type');
        $form->text('status', 'status');
        $form->text('type', 'type');
        $form->text('ope_user_id', 'ope_user_id');
        $form->text('ope_time', 'ope_time');
        $form->text('closeswitch', 'closeswitch');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
