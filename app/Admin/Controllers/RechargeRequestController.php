<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\RechargeRequest;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RechargeRequestController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'recharge-request';
    public $hiddenColumns = [

    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RechargeRequest);

        $grid->id('ID');
        $grid->user_id(__('user_id'));
        $grid->charger_id(__('charger_id'));
        $grid->value_usd(__('value_usd'));
        $grid->column('status',__('status'))->switch (Common::getSwitchStates ());
        $grid->type_value(__('type_value'));
        $grid->type(__('type'));
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
        $show = new Show(RechargeRequest::findOrFail($id));

//        $show->id('ID');
//        $show->user_id('user_id');
//        $show->charger_id('charger_id');
//        $show->value_usd('value_usd');
//        $show->status('status');
//        $show->type_value('type_value');
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
        $form = new Form(new RechargeRequest);

        $form->display('ID');
        $form->text('user_id', __('user_id'));
        $form->text('charger_id', __('charger_id'));
        $form->text('value_usd', __('value_usd'));
        $form->switch('status', __('status'))->states (Common::getSwitchStates ());
        $form->text('type_value', __('type_value'));
        $form->text('type', __('type'));

        return $form;
    }
}
