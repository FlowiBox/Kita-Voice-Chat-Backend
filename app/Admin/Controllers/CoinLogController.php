<?php

namespace App\Admin\Controllers;

use App\Models\CoinLog;
use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CoinLogController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'coin-logs';
    public $hiddenColumns = [

    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CoinLog);

        $grid->id('ID');
        $grid->paid_usd(__('paid usd'));
        $grid->obtained_coins(__('obtained coins'));
        $grid->user_id(__('user id'));
        $grid->method(__('method'));
        $grid->donor_id(__('donor id'));
        $grid->donor_type(__('donor type'));
        $grid->status(__('status'));

        $this->extendGrid ($grid);
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

//        $show->id('ID');
//        $show->paid_usd('paid_usd');
//        $show->obtained_coins('obtained_coins');
//        $show->user_id('user_id');
//        $show->method('method');
//        $show->donor_id('donor_id');
//        $show->donor_type('donor_type');
//        $show->status('status');
//        $show->created_at(trans('admin.created_at'));
//        $show->updated_at(trans('admin.updated_at'));
        $this->extendShow ($show);
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
//
//        $form->display('ID');
//        $form->text('paid_usd', __('paid_usd'));
//        $form->text('obtained_coins', __('obtained_coins'));
//        $form->text('user_id', 'user_id');
//        $form->text('method', 'method');
//        $form->text('donor_id', 'donor_id');
//        $form->text('donor_type', 'donor_type');
//        $form->text('status', 'status');
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
