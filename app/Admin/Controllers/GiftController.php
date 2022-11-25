<?php

namespace App\Admin\Controllers;

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
        $grid->hot('hot');
        $grid->is_play('is_play');
        $grid->price('price');
        $grid->img('img');
        $grid->show_img('show_img');
        $grid->show_img2('show_img2');
        $grid->sort('sort');
        $grid->enable('enable');
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

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
        $form->text('type', 'type');
        $form->text('vip_level', 'vip_level');
        $form->text('hot', 'hot');
        $form->text('is_play', 'is_play');
        $form->text('price', 'price');
        $form->text('img', 'img');
        $form->text('show_img', 'show_img');
        $form->text('show_img2', 'show_img2');
        $form->text('sort', 'sort');
        $form->text('enable', 'enable');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
