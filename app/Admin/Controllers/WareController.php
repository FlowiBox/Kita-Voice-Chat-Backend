<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Ware;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class WareController extends Controller
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
        $grid = new Grid(new Ware);

        $grid->id('ID');
        $grid->column('get_type')->select (
            [
                1=>trans ('vip level automatic acquisition'),
//               2=>trans ('activity'),
//               3=>trans ('treasure box'),
                4=>trans ('purchase'),
//               5=>trans ('background modification'),
                6=>trans ('limited time purchase'),
//               7=>trans ('treasure box point exchange'),
//               8=>trans ('cp level unlock'),
            ]
        );
        $grid->column('type')->select (
            [
                1=>trans ('Gemstone'),
                2=>trans ('Card Scroll'),
                3=>trans ('Avatar Frame'),
                4=>trans ('Bubble Frame'),
                5=>trans ('Entering Special Effects'),
                6=>trans ('Microphone Aperture'),
                7=>trans ('Badge'),
            ]
        );
        $grid->column('name')->editable ();
        $grid->title('title');
        $grid->column('price')->currency ();
        $grid->score('score');
        $grid->level('level');
        $grid->column('show_img')->image ('',30);
        $grid->column('color');
        $grid->expire('expire');
        $grid->column('enable')->switch (Common::getSwitchStates ());
        $grid->sort('sort');

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
        $show = new Show(Ware::findOrFail($id));

        $show->id('ID');
        $show->get_type('get_type');
        $show->type('type');
        $show->name('name');
        $show->title('title');
        $show->price('price');
        $show->score('score');
        $show->level('level');
        $show->show_img('show_img');
        $show->img1('img1');
        $show->img2('img2');
        $show->img3('img3');
        $show->color('color');
        $show->expire('expire');
        $show->enable('enable');
        $show->sort('sort');
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
        $form = new Form(new Ware());

        $form->display('ID');
        $form->select('get_type', trans('get_type'))->options (
           [
               1=>trans ('vip level automatic acquisition'),
//               2=>trans ('activity'),
//               3=>trans ('treasure box'),
               4=>trans ('purchase'),
//               5=>trans ('background modification'),
               6=>trans ('limited time purchase'),
//               7=>trans ('treasure box point exchange'),
//               8=>trans ('cp level unlock'),
           ]
        );
        $form->select('type', trans('type'))->options (
            [
                1=>trans ('Gemstone'),
                2=>trans ('Card Scroll'),
                3=>trans ('Avatar Frame'),
                4=>trans ('Bubble Frame'),
                5=>trans ('Entering Special Effects'),
                6=>trans ('Microphone Aperture'),
                7=>trans ('Badge'),
            ]
        )->rules ('required');
//        ->rules (function ($form){
//            if (!$id = $form->model()->id) {
//                return 'required';
//            }
//        });
        $form->text('name', trans('name'));
        $form->text('title', trans('title'));
        $form->currency('price', trans('price'))->symbol ('💰');
        $form->number('score', trans('score'));
        $form->number('level', trans('level'));
        $form->image('show_img', trans('img'));
        $form->image('img1', trans('img'));
        $form->file('img2', trans('svg'));
        $form->file('img3', trans('video'));
        $form->color('color', trans('color'));
        $form->number('expire', trans('expire(in days)'))->placeholder (trans ('0 if permanent'));
        $form->switch('enable', trans('enable'))->states (Common::getSwitchStates ());
        $form->number('sort', 'sort');


        return $form;
    }
}
