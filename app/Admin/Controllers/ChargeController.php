<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Charge;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ChargeController extends MainController
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
            ->header(trans('admin.index'))
            ->description(trans('admin.description'))
            ->row(function($row) {
                $row->column(10, $this->grid());
                $row->column(2, view('admin.grid.users.actions'));
            });
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
        $grid = new Grid(new Charge);
        $grid->model ()->orderByDesc ('id');
        $grid->id('ID');
        $grid->column('charger_id',__('charger id'))->modal ('Charger info',function ($model){
            if ($model->charger_id){
                return Common::getAdminShow($model->charger_id);
            }
            return null;
        });
//        $grid->column('charger_type',__ ('charger type'));
        $grid->column('user_id',__('user id'))->modal ('User info',function ($model){
            if ($model->user_id){
                if ($model->user_type == 'app'){
                    return Common::getUserShow($model->user_id);
                }else{
                    return Common::getAdminShow($model->user_id);
                }

            }
            return null;
        });
        $grid->column('user_type',__ ('user type'))->using (
            [
                'app'=>__ ('app'),
                'dash'=>__ ('agency')
            ]
        );
        $grid->amount(__('amount'));
//        $grid->amount_type('amount_type');
        $grid->column('created_at',trans('admin.created_at'))->diffForHumans ();
//        $grid->updated_at(trans('admin.updated_at'));
        $grid->disableActions ();
        $grid->disableCreateButton ();
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
        $show = new Show(Charge::findOrFail($id));

//        $show->id('ID');
//        $show->charger_id('charger_id');
//        $show->charger_type('charger_type');
//        $show->user_id('user_id');
//        $show->user_type('user_type');
//        $show->amount('amount');
//        $show->amount_type('amount_type');
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
        $form = new Form(new Charge);

//        $form->display('ID');
//        $form->text('charger_id', 'charger_id');
//        $form->text('charger_type', 'charger_type');
//        $form->text('user_id', 'user_id');
//        $form->text('user_type', 'user_type');
//        $form->text('amount', 'amount');
//        $form->text('amount_type', 'amount_type');
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
