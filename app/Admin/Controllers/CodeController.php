<?php

namespace App\Admin\Controllers;

use App\Models\Code;
use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CodeController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'charge';
    public $hiddenColumns = [

    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Code);

        $grid->id('ID');
        $grid->column('phone',trans ('phone'));
        $grid->column('code',trans ('code'));
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
        $show = new Show(Code::findOrFail($id));

        $show->id('ID');
        $show->phone(__('phone'));
        $show->code(__('code'));

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
        $form = new Form(new Code);

        $form->display('ID');
        $form->text('phone', trans('phone'));
        $form->text('code', trans('code'));

        return $form;
    }
}
