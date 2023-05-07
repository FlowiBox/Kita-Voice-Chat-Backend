<?php

namespace App\Admin\Controllers;

use App\Models\Silver;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class SilverController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'gold-coins';
    public $hiddenColumns = [

    ];


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Silver);

        $grid->id('ID');
        $grid->column('coin',__ ('coin'));
        $grid->column('silver',__ ('silver'));
        $grid->column('sort',__ ('sort'));
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
        $show = new Show(Silver::findOrFail($id));

//        $show->id('ID');
//        $show->coin('coin');
//        $show->silver('silver');
//        $show->sort('sort');
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
        $form = new Form(new Silver);

        $form->display('ID');
        $form->number('coin', __('coin'));
        $form->number('silver', __('silver'));
        $form->number('sort', __('sort'));


        return $form;
    }
}
