<?php

namespace App\Admin\Controllers;

use App\Models\StoreLog;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class StoreLogController extends Controller
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
        $grid = new Grid(new StoreLog);

        $grid->id('ID');
        $grid->user_id('user_id');
        $grid->get_nums('get_nums');
        $grid->get_type('get_type');
        $grid->now_nums('now_nums');
        $grid->adduser('adduser');
        $grid->symbol('symbol');
        $grid->types('types');
        $grid->union_id('union_id');
        $grid->family_id('family_id');
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
        $show = new Show(StoreLog::findOrFail($id));

        $show->id('ID');
        $show->user_id('user_id');
        $show->get_nums('get_nums');
        $show->get_type('get_type');
        $show->now_nums('now_nums');
        $show->adduser('adduser');
        $show->symbol('symbol');
        $show->types('types');
        $show->union_id('union_id');
        $show->family_id('family_id');
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
        $form = new Form(new StoreLog);

        $form->display('ID');
        $form->text('user_id', 'user_id');
        $form->text('get_nums', 'get_nums');
        $form->text('get_type', 'get_type');
        $form->text('now_nums', 'now_nums');
        $form->text('adduser', 'adduser');
        $form->text('symbol', 'symbol');
        $form->text('types', 'types');
        $form->text('union_id', 'union_id');
        $form->text('family_id', 'family_id');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
