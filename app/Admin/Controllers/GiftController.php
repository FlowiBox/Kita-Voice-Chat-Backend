<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Gift;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GiftController extends Controller
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
        $grid = new Grid(new Gift);

        $grid->id('ID');
        $grid->name('name');
        $grid->e_name('e_name');
        $grid->type('type');
        $grid->vip_level('vip_level');
        $grid->column('hot',trans ('hot'));
        $grid->column('is_play',trans ('is_play'))->switch (Common::getSwitchStates ());
        $grid->price('price');
        $grid->column('img',trans ('image'))->image ('','30');
        $grid->column('show_img',trans ('show_img'))->image ('','30');
        $grid->column('show_img2',trans ('show_img2'))->image ('','30');
        $grid->sort('sort');
        $grid->column('enable',trans ('enable'))->switch (Common::getSwitchStates ());


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
        $show = new Show(Gift::findOrFail($id));

        $show->id('ID');
        $show->name('name');
        $show->e_name('e_name');
        $show->type('type');
        $show->vip_level('vip_level');
        $show->hot('hot');
        $show->is_play('is_play');
        $show->price('price');
        $show->img('img');
        $show->show_img('show_img');
        $show->show_img2('show_img2');
        $show->sort('sort');
        $show->enable('enable');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {


        $form = new Form(new Gift);

        $form->display('ID');
        $form->text('name', 'name');
        $form->text('e_name', 'e_name');
        $form->select('type', 'type')->options ([1=>__ ('normal'),2=>__ ('hot'),3=>__ ('other')]);
        $form->number('vip_level', 'vip_level')->min (0)->placeholder (__ ('less than 256'));
        $form->number('hot', 'hot')->min (0);
        $form->switch('is_play', __ ('is_play'))->states (Common::getSwitchStates ());
        $form->currency('price', 'price')->symbol ('💎');
        $form->file('img', 'img');
        $form->file('show_img', 'show_img');
        $form->file('show_img2', 'show_img2');
        $form->number('sort', 'sort');
        $form->switch('enable', 'enable')->states (Common::getSwitchStates ());


        return $form;
    }
}
