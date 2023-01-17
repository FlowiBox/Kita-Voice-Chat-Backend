<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Agency;
use App\Models\AgencyJoinRequest;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AgencyJoinRequestController extends Controller
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
        $grid = new Grid(new AgencyJoinRequest);
        $grid->model ()->orderByDesc ('id');
        $grid->filter (function (Grid\Filter $filter){
            $filter->expand();
            $filter->column(1/2, function ($filter) {
                $filter->equal('status',__('status'))->select([0=>'pending',1=>'accepted',2=>'denied']);

            });
        });

        $grid->id('ID');
        $grid->column('user_id',__('user id'))->modal ('user info',function ($model){
            if ($model->user_id){
                return Common::getUserShow ($model->user_id);
            }
            return null;
        });
        $grid->column('agency_id',__ ('agency id'))->modal ('agency info',function ($model){
            if ($model->agency_id){
                return Common::getAgencyShow ($model->agency_id);
            }
            return null;
        });
        $grid->column('status',__('status'))->select (
            [
                0=>__('pending'),
                1=>__ ('accepted'),
                2=>__ ('denied')
            ]
        );
        $grid->column('change_status_admin_id',__('change status admin id'))->modal ('admin info',function ($model){
            if ($model->change_status_admin_id){
                return Common::getAdminShow ($model->change_status_admin_id);
            }
            return null;
        });
        $grid->column('updated_at',trans('time'))->diffForHumans ();

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
        $show = new Show(AgencyJoinRequest::findOrFail($id));

        $show->id('ID');
        $show->user_id('user_id');
        $show->agency_id('agency_id');
        $show->status('status');
        $show->change_status_admin_id('change_status_admin_id');
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
        $form = new Form(new AgencyJoinRequest);

        $form->display('ID');
        $form->text('user_id', 'user_id');
        $form->text('agency_id', 'agency_id');
        $form->text('status', 'status');
        $form->text('change_status_admin_id', 'change_status_admin_id');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
