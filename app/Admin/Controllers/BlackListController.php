<?php

namespace App\Admin\Controllers;

use App\Models\BlackList;
use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class BlackListController extends MainController
{
    use HasResourceActions;

    public $permission_name = 'black-list';
    public $hiddenColumns = [

    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BlackList);

        $grid->id('ID');
        $grid->column('user_id',trans ('user id'));
        $grid->column('from_uid',trans ('from uid'));
        $grid->column('status',trans ('status'));
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
        $show = new Show(BlackList::findOrFail($id));

        $show->id('ID');
        $show->user_id('user_id');
        $show->from_uid('from_uid');
        $show->status('status');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));
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
        $form = new Form(new BlackList);

        $form->display('ID');
        $form->text('user_id', 'user_id');
        $form->text('from_uid', 'from_uid');
        $form->text('status', 'status');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
