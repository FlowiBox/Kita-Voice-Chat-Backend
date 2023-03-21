<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Box;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class BoxController extends Controller
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
        $grid = new Grid(new Box);

        $grid->id('ID');
        $grid->column('type',__ ('type'))->using ([0=>__('normal'),1=>__('super')]);
        $grid->column('coins',__ ('coins'));
        $grid->column('users',__ ('users'));
        $grid->column('image',__ ('image'))->image ('',30);
        $grid->column('has_label',__ ('has_label'));
        $grid->column('duration',__ ('duration'));
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
        $show = new Show(Box::findOrFail($id));

//        $show->id('ID');
//        $show->type('type');
//        $show->coins('coins');
//        $show->users('users');
//        $show->image('image');
//        $show->has_label('has_label');
//        $show->duration('duration');
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
        $form = new Form(new Box);

        $form->display('ID');
        $form->select('type', __('type'))->options ([0=>'normal',1=>'super']);
        $form->number('coins', __('coins'));
        $form->number('users', __('users'));
        $form->image('image', __('image'));
        $form->switch('has_label', __('has label'))->states (Common::getSwitchStates ());
        $form->text('default_label', __('default label'));
        $form->number('duration', __('duration'))->help (__ ('in minutes'));

        return $form;
    }
}
