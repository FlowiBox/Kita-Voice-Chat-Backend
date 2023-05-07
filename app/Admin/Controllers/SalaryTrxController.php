<?php

namespace App\Admin\Controllers;

use App\Models\SalaryTrx;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SalaryTrxController extends Controller
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
        $grid = new Grid(new SalaryTrx);

        $grid->id('ID');
        $grid->type('type');
        $grid->oid('oid');
        $grid->amount('amount');
        $grid->t_no('t_no');
        $grid->note('note');
        $grid->before_pay('before_pay');
        $grid->after_pay('after_pay');
        $grid->payer_id('payer_id');
        $grid->payer_type('payer_type');
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
        $show = new Show(SalaryTrx::findOrFail($id));

        $show->id('ID');
        $show->type('type');
        $show->oid('oid');
        $show->amount('amount');
        $show->t_no('t_no');
        $show->note('note');
        $show->before_pay('before_pay');
        $show->after_pay('after_pay');
        $show->payer_id('payer_id');
        $show->payer_type('payer_type');
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
        $form = new Form(new SalaryTrx);

        $form->display('ID');
        $form->text('type', 'type');
        $form->text('oid', 'oid');
        $form->text('amount', 'amount');
        $form->text('t_no', 't_no');
        $form->text('note', 'note');
        $form->text('before_pay', 'before_pay');
        $form->text('after_pay', 'after_pay');
        $form->text('payer_id', 'payer_id');
        $form->text('payer_type', 'payer_type');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
