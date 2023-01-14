<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\User;
use App\Models\UserTarget;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserTargetController extends Controller
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
        $grid = new Grid(new UserTarget);
        $grid->model ()->ofAgency();
        $grid->id('ID');
        $grid->column('user_id',__('user id'))->modal ('user info',function ($model){
            return Common::getUserShow ($model->user_id);
        });
        $grid->column('agency_id',__ ('agency id'))->modal ('agency info',function ($model){
            return Common::getAgencyShow ($model->agency_id);
        });
//        $grid->union_id('union_id');
//        $grid->family_id('family_id');
        $grid->column('target_id',__ ('target id'));
        $grid->column('target_diamonds',__ ('target diamonds'));
        $grid->column('add_month',__ ('month'));
        $grid->column('add_year',__ ('year'));
        $grid->column('target_usd',__('usd').' '.__ ('deserved'));
        $grid->column('target_hours',__ ('target hours'));
        $grid->column('target_days',__ ('target days'));
        $grid->column('target_agency_share',__ ('agency share').'(%)');
        $grid->column('user_diamonds',__ ('user diamonds'));
        $grid->column('user_hours',__ ('user hours'));
        $grid->column('user_days',__ ('user days'));
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
        $show = new Show(UserTarget::findOrFail($id));

        $show->id('ID');
        $show->user_id('user_id');
        $show->agency_id('agency_id');
        $show->union_id('union_id');
        $show->family_id('family_id');
        $show->target_id('target_id');
        $show->target_diamonds('target_diamonds');
        $show->add_month('add_month');
        $show->add_year('add_year');
        $show->target_usd('target_usd');
        $show->target_hours('target_hours');
        $show->target_days('target_days');
        $show->target_agency_share('target_agency_share');
        $show->user_diamonds('user_diamonds');
        $show->user_hours('user_hours');
        $show->user_days('user_days');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new UserTarget);

        $form->display('ID');
        $form->text('user_id', 'user_id');
        $form->text('agency_id', 'agency_id');
        $form->text('union_id', 'union_id');
        $form->text('family_id', 'family_id');
        $form->text('target_id', 'target_id');
        $form->text('target_diamonds', 'target_diamonds');
        $form->text('add_month', 'add_month');
        $form->text('add_year', 'add_year');
        $form->text('target_usd', 'target_usd');
        $form->text('target_hours', 'target_hours');
        $form->text('target_days', 'target_days');
        $form->text('target_agency_share', 'target_agency_share');
        $form->text('user_diamonds', 'user_diamonds');
        $form->text('user_hours', 'user_hours');
        $form->text('user_days', 'user_days');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
