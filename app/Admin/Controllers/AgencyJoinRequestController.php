<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Agency;
use App\Models\AgencyJoinRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Encore\Admin\Actions\Response;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class AgencyJoinRequestController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'agency-join-requests';
    public $hiddenColumns = [

    ];






    public function update ( $id )
    {

        if (request ('_edit_inline') == "true"){
            if (request ('status')){
                request ()->request->add(['change_status_admin_id'=>Auth::id ()]);
            }
        }
        return $this->form()->update($id);
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
        $grid->column ('whatsapp',__ ('whatsapp'));
        $grid->column('status',__('status'))->using (
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
        $grid->column('created_at',trans('time'))->diffForHumans ();
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
        $show = new Show(AgencyJoinRequest::findOrFail($id));

//        $show->id('ID');
//        $show->user_id('user_id');
//        $show->agency_id('agency_id');
//        $show->status('status');
//        $show->change_status_admin_id('change_status_admin_id');
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

        $form = new Form(new AgencyJoinRequest);
        $form->display('ID');
        $form->text('user_id', 'user_id');
        $form->text('agency_id', 'agency_id');
        $form->select('status', 'status')->options (
            [
                0=>__('pending'),
                1=>__ ('accepted'),
                2=>__ ('denied')
            ]
        );;
        $form->hidden('change_status_admin_id', 'change_status_admin_id');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));
        $form->saving(function (Form $form) {
            $user = User::query ()->where ('id',$form->model ()->user_id)->first ();
            if ($user->agency_id){
                $error = new MessageBag(
                    [
                        'title'   => 'forbidden',
                        'message' => 'user already in agency',
                    ]
                );
                return back()->with(compact('error'));
            }
        });


        return $form;
    }
}
