<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\HomeCarousel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class HomeCarouselController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'carousel';
    public $hiddenColumns = [

    ];


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new HomeCarousel);

        $grid->id('ID');
        $grid->column('img',trans ('img'))->image ('',30);
        $grid->column('contents',trans ('contents'));
        $grid->column('url',trans ('url'))->url ();
        $grid->column('enable',trans ('enable'))->switch (Common::getSwitchStates ());
        $grid->column('sort',trans ('sort'));
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
        $show = new Show(HomeCarousel::findOrFail($id));

//        $show->id('ID');
//        $show->img('img');
//        $show->contents('contents');
//        $show->url('url');
//        $show->enable('enable');
//        $show->sort('sort');
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
        $form = new Form(new HomeCarousel);

        $form->display('ID');
        $form->image('img', trans('img'));
        $form->text('contents', trans('contents'));
        $form->url('url', trans('url'));
        $form->switch('enable', trans('enable'))->states (Common::getSwitchStates ());
        $form->number('sort', trans('sort'));

        return $form;
    }
}
