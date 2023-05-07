<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Ticket;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TicketController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'ticket';
    public $hiddenColumns = [

    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Ticket);

//        $grid->model ()->where ('status',1);


        $grid->id('ID');
        $grid->user_id('user_id');
        $grid->column('contact_num',__ ('contact'));
        $grid->column('problem',__ ('problem'));
        $grid->column('img',__ ('img'))->image ('',30);
        $grid->column('status',__ ('status'))->switch (Common::getSwitchStates ());
//        $grid->admin_id('admin_id');
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
        $show = new Show(Ticket::findOrFail($id));

        $show->id('ID');
//        $show->user_id('user_id');
        $show->field('contact_num',__ ('contact'));
        $show->field('problem',__ ('problem'));
        $show->field('img',__ ('img'))->image ('',80);
        $show->field('status',__ ('status'))->using ([0=>"closed",1=>"open"]);
//        $show->admin_id('admin_id');
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
        $form = new Form(new Ticket);

        $form->display('ID');
//        $form->text('user_id', 'user_id');
        $form->text('contact_num', __('contact'));
        $form->textarea('problem', __('problem'));
        $form->image('img', __('img'));
        $form->switch('status', __('status'))->states (Common::getSwitchStates ());
//        $form->text('admin_id', 'admin_id');
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
