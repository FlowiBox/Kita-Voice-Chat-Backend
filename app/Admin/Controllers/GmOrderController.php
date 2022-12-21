<?php

namespace App\Admin\Controllers;

use App\Models\GmOrder;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GmOrderController extends Controller
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
        $grid = new Grid(new GmOrder);

        $grid->id('ID');
        $grid->skill_apply_id('skill_apply_id');
        $grid->order_no('order_no');
        $grid->user_id('user_id');
        $grid->master_id('master_id');
        $grid->status('status');
        $grid->skill_id('skill_id');
        $grid->start_time('start_time');
        $grid->num('num');
        $grid->remarks('remarks');
        $grid->price('price');
        $grid->unit('unit');
        $grid->total_price('total_price');
        $grid->fee('fee');
        $grid->real_price('real_price');
        $grid->refund('refund');
        $grid->pay_type('pay_type');
        $grid->is_first('is_first');
        $grid->is_discuss('is_discuss');
        $grid->is_notify('is_notify');
        $grid->cancel('cancel');
        $grid->coupon_id('coupon_id');
        $grid->coupon_price('coupon_price');
        $grid->reason('reason');
        $grid->images('images');
        $grid->f_user_id('f_user_id');
        $grid->out_refund_no('out_refund_no');
        $grid->addtime('addtime');
        $grid->paytime('paytime');
        $grid->refusetime('refusetime');
        $grid->finishtime('finishtime');
        $grid->union_id('union_id');
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
        $show = new Show(GmOrder::findOrFail($id));

        $show->id('ID');
        $show->skill_apply_id('skill_apply_id');
        $show->order_no('order_no');
        $show->user_id('user_id');
        $show->master_id('master_id');
        $show->status('status');
        $show->skill_id('skill_id');
        $show->start_time('start_time');
        $show->num('num');
        $show->remarks('remarks');
        $show->price('price');
        $show->unit('unit');
        $show->total_price('total_price');
        $show->fee('fee');
        $show->real_price('real_price');
        $show->refund('refund');
        $show->pay_type('pay_type');
        $show->is_first('is_first');
        $show->is_discuss('is_discuss');
        $show->is_notify('is_notify');
        $show->cancel('cancel');
        $show->coupon_id('coupon_id');
        $show->coupon_price('coupon_price');
        $show->reason('reason');
        $show->images('images');
        $show->f_user_id('f_user_id');
        $show->out_refund_no('out_refund_no');
        $show->addtime('addtime');
        $show->paytime('paytime');
        $show->refusetime('refusetime');
        $show->finishtime('finishtime');
        $show->union_id('union_id');
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
        $form = new Form(new GmOrder);

        $form->display('ID');
        $form->text('skill_apply_id', 'skill_apply_id');
        $form->text('order_no', 'order_no');
        $form->text('user_id', 'user_id');
        $form->text('master_id', 'master_id');
        $form->text('status', 'status');
        $form->text('skill_id', 'skill_id');
        $form->text('start_time', 'start_time');
        $form->text('num', 'num');
        $form->text('remarks', 'remarks');
        $form->text('price', 'price');
        $form->text('unit', 'unit');
        $form->text('total_price', 'total_price');
        $form->text('fee', 'fee');
        $form->text('real_price', 'real_price');
        $form->text('refund', 'refund');
        $form->text('pay_type', 'pay_type');
        $form->text('is_first', 'is_first');
        $form->text('is_discuss', 'is_discuss');
        $form->text('is_notify', 'is_notify');
        $form->text('cancel', 'cancel');
        $form->text('coupon_id', 'coupon_id');
        $form->text('coupon_price', 'coupon_price');
        $form->text('reason', 'reason');
        $form->text('images', 'images');
        $form->text('f_user_id', 'f_user_id');
        $form->text('out_refund_no', 'out_refund_no');
        $form->text('addtime', 'addtime');
        $form->text('paytime', 'paytime');
        $form->text('refusetime', 'refusetime');
        $form->text('finishtime', 'finishtime');
        $form->text('union_id', 'union_id');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
