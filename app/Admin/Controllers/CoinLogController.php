<?php

namespace App\Admin\Controllers;

use App\Models\CoinLog;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CoinLogController extends Controller
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
        $grid = new Grid(new CoinLog);

        $grid->id('ID');
        $grid->paid_usd('paid_usd');
        $grid->obtained_coins('obtained_coins');
        $grid->user_id('user_id');
        $grid->method('method');
        $grid->donor_id('donor_id');
        $grid->donor_type('donor_type');
        $grid->status('status');
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
        $show = new Show(CoinLog::findOrFail($id));

        $show->id('ID');
        $show->paid_usd('paid_usd');
        $show->obtained_coins('obtained_coins');
        $show->user_id('user_id');
        $show->method('method');
        $show->donor_id('donor_id');
        $show->donor_type('donor_type');
        $show->status('status');
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
        $form = new Form(new CoinLog);

        $form->display('ID');
        $form->text('paid_usd', 'paid_usd');
        $form->text('obtained_coins', 'obtained_coins');
        $form->text('user_id', 'user_id');
        $form->text('method', 'method');
        $form->text('donor_id', 'donor_id');
        $form->text('donor_type', 'donor_type');
        $form->text('status', 'status');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
