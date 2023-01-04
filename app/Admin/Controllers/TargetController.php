<?php

namespace App\Admin\Controllers;

use App\Models\Target;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TargetController extends Controller
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
            ->header(trans('target'))
            ->description(trans('admin.index'))
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
        $form->number('usd', __('usd'));
//        $form->text('coin', 'coin');
//        $form->text('gold', 'gold');
//        $form->text('minuts', 'minuts');
        $form->number('hours', __('hours'));
        $form->number('days', __('days'));
//        $form->text('img', 'img');


        return $form;
    }
}
