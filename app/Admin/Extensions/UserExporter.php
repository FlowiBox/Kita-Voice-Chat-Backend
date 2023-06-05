<?php
namespace App\Admin\Extensions;
use Encore\Admin\Grid\Exporters\ExcelExporter;

class UserExporter extends ExcelExporter
{
    protected $fileName = 'users.xlsx';

    protected $columns = [
        'id' => 'ID',
        'name' => 'title',
        'uuid' => 'uuid',
    ];
}
