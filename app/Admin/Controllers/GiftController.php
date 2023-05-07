<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Gift;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GiftController extends MainController
{
    use HasResourceActions;

    public $permission_name = 'gift';
    public $hiddenColumns = [

    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Gift);

        $grid->id('ID');
        $grid->name(__('name'));
        $grid->e_name(__('e_name'));
        $grid->type(__('type'));
        $grid->vip_level(__('vip_level'));
        $grid->column('hot',trans ('hot'));
        $grid->column('is_play',trans ('is_play'))->switch (Common::getSwitchStates ());
        $grid->price('price');
        $grid->column('img',trans ('image'))->image ('','30');
        $grid->column('show_img',trans ('show_img'))->image ('','30');
        $grid->column('show_img2',trans ('show_img2'))->image ('','30');
        $grid->sort('sort');
        $grid->column('enable',trans ('enable'))->switch (Common::getSwitchStates ());
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
        $show = new Show(Gift::findOrFail($id));

//        $show->id('ID');
//        $show->name(__('name'));
//        $show->e_name(__('e_name'));
//        $show->type(__('type'));
//        $show->vip_level(__('vip_level'));
//        $show->hot(__('hot'));
//        $show->is_play('is_play');
//        $show->price(__('price'));
//        $show->img(__('img'));
//        $show->show_img(('show_img'));
//        $show->show_img2('show_img2');
//        $show->sort('sort');
//        $show->enable('enable');

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


        $form = new Form(new Gift);

        $form->display('ID');
        $form->text('name', __('name'));
        $form->text('e_name', __('e_name'));
        $form->select('type', __('type'))->options (
            [
                1=>__ ('normal'),
                2=>__ ('hot'),
                3=>__ ('country')
            ]
        );
        $form->number('vip_level', __('vip_level'))->min (0)->placeholder (__ ('less than 256'));
//        $form->number('hot', 'hot')->min (0);
//        $form->switch('is_play', __ ('is_play'))->states (Common::getSwitchStates ());
        $form->currency('price', __('price'))->symbol ('💎');
        $form->file('img', __('img'));
        $form->file('show_img', __('show_img'));
        $form->file('show_img2', __('show_img2'));
        $form->number('sort', __('sort'));
        $form->switch('enable', __('enable'))->states (Common::getSwitchStates ());


        return $form;
    }
}
