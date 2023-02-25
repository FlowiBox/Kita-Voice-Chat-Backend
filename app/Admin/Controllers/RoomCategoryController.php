<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\RoomCategory;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RoomCategoryController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'categories';
    public $hiddenColumns = [

    ];

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RoomCategory);

        $grid->id('ID');
        $grid->name(trans('name'));
        $grid->column('img',trans ('img'))->image ('',30);
        $grid->column('enable',trans ('enable'))->switch (Common::getSwitchStates ());
        $this->extendGrid ($grid);

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
        $show = new Show(RoomCategory::findOrFail($id));

//        $show->id('ID');
//        $show->parent_id('parent_id');
//        $show->name('name');
//        $show->img('img');
//        $show->enable('enable');
//        $show->created_at(trans('admin.created_at'));
//        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new RoomCategory);

        $form->display('ID');
        $form->select ('parent_id',trans ('parent'))->options (function (){
            $options = [0=>trans ('root')];
            $cats = RoomCategory::query ()->where ('id','!=',$this->id)->where ('enable',1)->where ('parent_id',0)->get ();
            foreach ($cats as $cat){
                $options[$cat->id] = $cat->name;
            }
            return $options;
        });
        $form->text('name', trans('name'));
        $form->image('img', trans('img'));
        $form->switch('enable', trans('enable'))->states (Common::getSwitchStates ());

        return $form;
    }
}
