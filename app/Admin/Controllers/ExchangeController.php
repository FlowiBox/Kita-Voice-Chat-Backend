<?php

namespace App\Admin\Controllers;

use App\Models\Exchange;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ExchangeController extends MainController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Exchange';

    public $permission_name = 'exchange';
    public $hiddenColumns = [

    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Exchange());

        $grid->column('id', __('ID'))->sortable();
        $grid->column('diamonds', __('diamonds'));
        $grid->column('value', __('value'));
        $grid->column('type', __('type'));
        $this->extendGrid ($grid);
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Exchange::findOrFail($id));

//        $show->field('id', __('ID'));
//        $show->field('created_at', __('Created at'));
//        $show->field('updated_at', __('Updated at'));
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
        $form = new Form(new Exchange());

        $form->display('id', __('ID'));
        $form->number('diamonds', __('diamonds'));
        $form->number('value', __('value'));
        $form->select('type', __('type'))->options (
            [
                0=>__('coin'),
                1=>__('silver'),
            ]
        );

        return $form;
    }
}
