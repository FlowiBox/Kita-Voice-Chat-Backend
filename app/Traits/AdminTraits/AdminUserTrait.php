<?php


namespace App\Traits\AdminTraits;


use App\Models\Admin;
use Encore\Admin\Auth\Database\Administrator;

trait AdminUserTrait
{

    public function getAgencies(){
        $model = new Admin();

        return $model->whereHas('roles',function ($q){
            $q->where('slug','agency');
        })->get();
    }

}
