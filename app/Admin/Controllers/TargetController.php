<?php

namespace App\Admin\Controllers;

use App\Models\Target;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TargetController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'target';
    public $hiddenColumns = [

    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Target);

        $grid->id('ID');
        $grid->level(__('target no'));
        $grid->diamonds(__('diamonds'));
        $grid->usd(__('usd'));
//        $grid->coin('coin');
//        $grid->gold('gold');
//        $grid->minuts('minuts');
        $grid->hours(__('hours'));
        $grid->days(__('days'));
//        $grid->img('img');
        $grid->agency_share(__('agency share').'(%)');
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
        $show = new Show(Target::findOrFail($id));

        $show->id('ID');
        $show->level('target no');
        $show->diamonds('diamonds');
//        $show->minuts('minuts');
        $show->hours('hours');
        $show->days('days');
//        $show->img('img');
//        $show->usd('usd');
//        $show->coin('coin');
//        $show->gold('gold');
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
        $form = new Form(new Target);

        $form->display('ID');
        $form->number('level', __('target no'));
        $form->number('diamonds', __('diamonds'));
        $form->decimal('usd', __('usd'));
//        $form->text('coin', 'coin');
//        $form->text('gold', 'gold');
//        $form->text('minuts', 'minuts');
        $form->number('hours', __('hours'));
        $form->number('days', __('days'));
//        $form->text('img', 'img');
        $form->decimal('agency_share', __('agency share').'(%)');


        return $form;
    }
}
