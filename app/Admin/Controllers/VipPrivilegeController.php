<?php

namespace App\Admin\Controllers;

use App\Models\VipPrivilege;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class VipPrivilegeController extends Controller
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
        $grid = new Grid(new VipPrivilege);

        $grid->id('ID');
        $grid->column('name',__('name'));
        $grid->column('title',__ ('title'));
        $grid->column('type')->select (
            [
                0=>trans ('No Type'),
                1=>trans ('Gemstone'),
                3=>trans ('Card Scroll'),
                4=>trans ('Avatar Frame'),
                5=>trans ('Bubble Frame'),
                6=>trans ('Entering Special Effects'),
                7=>trans ('Microphone Aperture'),
                8=>trans ('Badge'),
                9=>trans ('NoKick'),
                10=>trans ('Icon'),
            ]
        );
        $grid->column('img1',__ ('img 1'))->image ('',30);
//        $grid->img2('img2');
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
        $show = new Show(VipPrivilege::findOrFail($id));

        $show->id('ID');
        $show->name('name');
        $show->title('title');
        $show->img1('img1');
        $show->img2('img2');
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
        $form = new Form(new VipPrivilege);

        $form->display('ID');
        $form->text('name', __('name'));
        $form->text('title', __('title'));
        $form->select('type', trans('type'))->options (
            [
                0=>trans ('No Type'),
                1=>trans ('Gemstone'),
                3=>trans ('Card Scroll'),
                4=>trans ('Avatar Frame'),
                5=>trans ('Bubble Frame'),
                6=>trans ('Entering Special Effects'),
                7=>trans ('Microphone Aperture'),
                8=>trans ('Badge'),
                9=>trans ('NoKick'),
                10=>trans ('Icon'),
            ]
        );
        $form->image('img1', __('img1'));
        $form->file('img2', __('img2'));
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
