<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Admin;
use App\Models\Country;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use function Doctrine\Common\Cache\Psr6\get;

class AdminUserController extends \Encore\Admin\Controllers\UserController
{


    protected $model;



    public function __construct ()
    {
        $userModel = Admin::class;
        $this->model = new $userModel;
    }

    public function destroy ( $id )
    {
        $user = $this->model->find($id);
        if ($user){
            if ($user->isRole('admin') || $user->isRole('developer')){
                return response ()->json (['error'=>'','message'=>__('admin cant be deleted')]);
            }
        }

        return parent ::destroy ($id);

    }



}
