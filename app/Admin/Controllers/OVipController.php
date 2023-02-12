<?php

namespace App\Admin\Controllers;

use App\Models\OVip;
use App\Http\Controllers\Controller;
use App\Models\VipPrivilege;
use App\Selectables\Privileges;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OVipController extends Controller
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
//        $arr = [];
//        $privs = VipPrivilege::all ();
//        foreach ($privs as $priv){
//            $arr[$priv->id]=$priv->name;
//        }
        $grid = new Grid(new OVip);

        $grid->id('ID');
        $grid->column('level',__ ('level'));
        $grid->column('name',__ ('name'));
        $grid->column('img',__ ('img'))->image ('',30);
        $grid->column('price',__ ('price'));
        $grid->column('expire',__ ('expire'));
//        $grid->created_at(trans('admin.created_at'));
//        $grid->updated_at(trans('admin.updated_at'));

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
        $show = new Show(OVip::findOrFail($id));

        $show->id('ID');
        $show->level('level');
        $show->name('name');
        $show->img('img');
        $show->price('price');
        $show->privileges('privileges');
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
//        $arr = [];
//        $privs = VipPrivilege::all ();
//        foreach ($privs as $priv){
//            $arr[$priv->id]=$priv->name;
//        }

        $form = new Form(new OVip);

        $form->display('ID');
        $form->number('level', __('level'));
        $form->text('name', __('name'));
        $form->file('img', __('img'));
        $form->currency('price', __('price'));
        $form->number('expire', __('expire'));
        $form->belongsToMany('privilegs', Privileges::class, __('privileges'));
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));
        return $form;
    }
}
