<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\DedicateAction;
use App\Models\OVip;
use App\Http\Controllers\Controller;
use App\Models\VipPrivilege;
use App\Selectables\Privileges;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class DedicateVipController extends MainController
{
    use HasResourceActions;


    protected function grid()
    {
        $grid = new Grid(new OVip);

        $grid->id('ID');
        $grid->column('level',__ ('level'));
        $grid->column('name',__ ('name'));
        $grid->column('img',__ ('img'))->image ('',150);
        $grid->column('price',__ ('price'));
        $grid->column('expire',__ ('expire'));
        $grid->disableCreateButton ();
        $grid->actions (function ($actions){
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            $actions->add(new DedicateAction());
        });
        return $grid;
    }

}
