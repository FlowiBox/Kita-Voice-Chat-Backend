<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Country;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CountryController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(trans('admin.index'))
            ->description(trans('admin.description'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Country);

        $grid->id('ID');
        $grid->name(trans('name'));
        $grid->e_name(trans('english name'));
        $grid->phone_code(trans('phone code'));
        $grid->language(trans ('language'));
        $grid->column ('flag',trans ('flag'))->image ('',30);
        $grid->iso(trans('iso'));
        $grid->iso3(trans('iso3'));
        $grid->continent_name(trans('continent name'));
        $grid->e_continent_name(trans('english continent name'));
        $grid->column ('status',trans ('status'))->switch (Common::getSwitchStates ());


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
        $show = new Show(Country::findOrFail($id));

        $show->id('ID');
        $show->name(trans('name'));
        $show->e_name(trans('english name'));
        $show->phone_code(trans('phone code'));
        $show->language(trans('language'));
        $show->iso(trans('iso'));
        $show->iso3(trans('iso3'));
        $show->continent_name(trans('continent name'));
        $show->e_continent_name(trans('english continent name'));


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Country);

        $form->display('ID');
        $form->text('name', trans('name'));
        $form->text('e_name', trans('english name'));
        $form->text('phone_code', trans('phone code'));
        $form->text('language', trans('language'));
        $form->image ('flag',trans ('flag'));
        $form->text('iso', trans('iso'));
        $form->text('iso3', trans('iso3'));
        $form->text('continent_name', trans('continent name'));
        $form->text('e_continent_name', trans('english continent name'));
        $form->switch ('status',trans ('status'))->states (Common::getSwitchStates ());


        return $form;
    }
}
