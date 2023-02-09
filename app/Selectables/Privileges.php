<?php

namespace App\Selectables;

use App\Models\VipPrivilege;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Grid\Selectable;

class Privileges extends Selectable
{

    public $model = VipPrivilege::class;

    public function make()
    {
        $this->column('id');
        $this->column('name');
        $this->column('img1',__('Image'))->image();

        $this->filter(function (Filter $filter) {
            $filter->like('name');
        });
    }
}
