<?php

namespace App\Admin\Controllers;

use App\Models\Vip;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VipController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Vips';

    public function __construct ()
    {
        $this->title = __('Vips');
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Vip());
        $grid->quickSearch ();
        $grid->column('id', __('Id'));
        $grid->column('type', __('Type'))->select (
            [
                1=>__ ('broadcaster level'),
                2=>__ ('honor level'),
                3=>__ ('vip'),
            ]
        );
        $grid->column('level', __('Level'));
        $grid->column('exp', __('Exp'));
        $grid->column('di', __('Diamonds'));
        $grid->column('co', __('Coins'));
        $grid->column('img', __('Image'))->image ('','50','50');
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
        $show = new Show(Vip::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', __('Type'))->number ();
        $show->field('level', __('Level'))->number ();
        $show->field('exp', __('Exp'))->number ();
        $show->field('di', __('Diamonds'))->number ();
        $show->field('co', __('Coins'))->number ();
        $show->field('img', __('Image'))->image ();
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Vip());

        $form->select('type', __('Type'))->options (
            [
                1=>__ ('broadcaster level'),
                2=>__ ('honor level'),
                3=>__ ('vip'),
            ]
        );
        $form->number('level', __('Level'));
        $form->number('exp', __('Exp'))->help (__('sender: 1 coin = 10 exp -- receiver: 1 coin = 1 exp'));
        $form->number('di', __('Diamonds'));
        $form->number('co', __('Coins'));
        $form->image ('img','Image');

        return $form;
    }
}
