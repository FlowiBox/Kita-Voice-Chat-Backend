<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProfileController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    public function __construct ()
    {
        $this->title = __('Profile');
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
        $grid->column('avatar', __('Avatar'))->image ('',30);
        $grid->column('gender', __('Gender'))->using (
        [
            1=>trans('male'),
            2=>trans('female')
        ]
        );
        $grid->column('country', __('Country'));
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
        $show->field('avatar', __('Avatar'))->image ('',30);
        $show->field('gender', __('Gender'))->using (
            [
                1=>trans('male'),
                2=>trans('female')
            ]
        );
        $show->field('country', __('Country'));


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

        $form->display('id', __('Id'));
        $form->image('avatar', __('Avatar'));
        $form->display('gender', __('Gender'));
        $form->display('country', __('Country'));



        return $form;
    }
}
