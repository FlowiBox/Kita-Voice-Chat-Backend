<?php

namespace App\Admin\Controllers;

use App\Admin\Customization\Dashboard\CustomDashboard;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Dashboard')
            ->description('Description...')
            ->row(CustomDashboard::title())
            ->row(function (Row $row) {
                $row->column(3, function (Column $column) {
                    $labels = ["- 20", "20:30", "30:40", "40:50", "50:60", "+ 60"];
                    $numbers = [12, 19, 3, 5, 2, 3];
                    $data = [
                        'labels'=>$labels,
                        'numbers'=>$numbers
                    ];
                    $column->append(new Box('user age', view('admin.components.users-age-chart',['data'=>$data])));
                });
                $row->column(3, function (Column $column) {
                    $labels = ["male", "female"];
                    $numbers = [12, 19];
                    $data = [
                        'labels'=>$labels,
                        'numbers'=>$numbers
                    ];
                    $column->append(new Box('user gender', view('admin.components.users-gender-chart',['data'=>$data])));
                });
            });
    }

    public function devIndex(Content $content)
    {
        return $content
            ->title('Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }
}
