<?php
namespace App\Admin\Controllers;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Widgets\Table;

class ReportController extends AdminController{

    protected function grid(){
        $grid = new Grid(new User());

        $grid->column ('name',__ ('name'));

    }

}
