<?php

namespace App\Admin\Controllers;

use App\Models\Coin;
use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CoinController extends MainController
{
    use HasResourceActions;

    public $permission_name = 'coins';
    public $hiddenColumns = [

    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Coin);

        $grid->id('ID');
        $grid->usd('usd');
        $grid->coin('coin');
//        $grid->first_charge_coin('first_charge_coin');
//        $grid->status('status');
//        $grid->discount_code('discount_code');
//        $grid->discount_code_expire_in('discount_code_expire_in');
//        $grid->extra_value('extra_value');
//        $grid->extra_value_end_in('extra_value_end_in');
//        $grid->created_at(trans('admin.created_at'));
//        $grid->updated_at(trans('admin.updated_at'));
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
        $show = new Show(Coin::findOrFail($id));

        $show->id('ID');
        $show->usd('usd');
        $show->coin('coin');
//        $show->first_charge_coin('first_charge_coin');
//        $show->status('status');
//        $show->discount_code('discount_code');
//        $show->discount_code_expire_in('discount_code_expire_in');
//        $show->extra_value('extra_value');
//        $show->extra_value_end_in('extra_value_end_in');
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
        $form = new Form(new Coin);

        $form->display('ID');
        $form->text('usd', 'usd');
        $form->text('coin', 'coin');
//        $form->text('first_charge_coin', 'first_charge_coin');
//        $form->text('status', 'status');
//        $form->text('discount_code', 'discount_code');
//        $form->text('discount_code_expire_in', 'discount_code_expire_in');
//        $form->text('extra_value', 'extra_value');
//        $form->text('extra_value_end_in', 'extra_value_end_in');
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
