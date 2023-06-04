<?php

namespace App\Admin\Controllers\AgencyControllers;

use App\Helpers\Common;
use App\Models\User;
use App\Models\UserTarget;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserTargetController extends AdminController
{
    use HasResourceActions;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserTarget);
        $grid->model ()->ofAgency()->where('agency_obtain','>',0);
        $grid->id('ID');
        $grid->column('user_id',__('user id'))->modal ('user info',function ($model){
            return Common::getUserShow ($model->user_id);
        });
        $grid->column('agency_id',__ ('agency id'))->modal ('agency info',function ($model){
            return Common::getAgencyShow ($model->agency_id);
        });
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
        $grid->column('user_obtain',__ ('user obtain'));
        $grid->column('agency_obtain',__ ('agency obtain'));
        $grid->disableActions ();
        $grid->disableCreateButton ();
        return $grid;
    }


}
