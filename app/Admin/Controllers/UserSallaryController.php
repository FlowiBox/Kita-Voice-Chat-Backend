<?php

namespace App\Admin\Controllers;

use App\Models\UserSallary;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserSallaryController extends Controller
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
        $grid = new Grid(new UserSallary);

        $grid->id('ID');
        $grid->user_id('user_id');
        $grid->sallary('sallary');
        $grid->cut_amount('cut_amount');
        $grid->month('month');
        $grid->year('year');
        $grid->is_paid('is_paid');
        $grid->user_agency_id('user_agency_id');
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
        $show = new Show(UserSallary::findOrFail($id));

        $show->id('ID');
        $show->user_id('user_id');
        $show->sallary('sallary');
        $show->cut_amount('cut_amount');
        $show->month('month');
        $show->year('year');
        $show->is_paid('is_paid');
        $show->user_agency_id('user_agency_id');
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
        $form = new Form(new UserSallary);

        $form->display('ID');
        $form->text('user_id', 'user_id');
        $form->text('sallary', 'sallary');
        $form->text('cut_amount', 'cut_amount');
        $form->text('month', 'month');
        $form->text('year', 'year');
        $form->text('is_paid', 'is_paid');
        $form->text('user_agency_id', 'user_agency_id');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
