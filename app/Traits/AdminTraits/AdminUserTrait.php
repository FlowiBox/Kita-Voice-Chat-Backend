<?php


namespace App\Traits\AdminTraits;


trait AdminUserTrait
{

    public function getAdminUsers(){
        $userModel = config('admin.database.users_model');
        $model = new $userModel;

        return $model->all();
    }

}
