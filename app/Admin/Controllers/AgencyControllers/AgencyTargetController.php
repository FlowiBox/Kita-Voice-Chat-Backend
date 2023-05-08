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

class AgencyTargetController extends AdminController
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
        $grid->model ()->ofAgency();
        $grid->model ()->selectRaw ('add_month,add_year,SUM(agency_obtain) as tar')->groupBy ('add_month','add_year');
        $grid->column('add_month',__ ('month'));
        $grid->column('add_year',__ ('year'));
        $grid->column('tar',__ ('tar'));

        $grid->disableActions ();
        $grid->disableCreateButton ();
        return $grid;
    }


}
