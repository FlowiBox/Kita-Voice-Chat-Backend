<?php

namespace App\Admin\Controllers;

use App\Models\Country;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\App;
use function Doctrine\Common\Cache\Psr6\get;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    public function __construct ()
    {
        $this->title = __('User');
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());
        $grid->quickSearch ();
        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('isOnline', __('isOnline'));
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());

        $form->text('name', __('Name'));
        $form->select ('country_id',trans ('country'))->options (function (){
            $ops = [];
            $countries = Country::all ();
            foreach ($countries as $country){
                $ops[$country->id] = App::isLocale('en') ? $country->e_name : $country->name;
            }
            return $ops;
        });
        $form->email('email', __('Email'));
        $form->password('password', __('Password'));


        return $form;
    }
}
