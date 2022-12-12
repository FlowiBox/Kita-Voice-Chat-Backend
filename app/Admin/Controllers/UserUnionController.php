<?php

namespace App\Admin\Controllers;

use App\Models\UserUnion;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserUnionController extends Controller
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
        $grid = new Grid(new UserUnion);

        $grid->id('ID');
        $grid->union_id('union_id');
        $grid->user_id('user_id');
        $grid->total_price('total_price');
        $grid->settlement_price('settlement_price');
        $grid->check_time('check_time');
        $grid->check_content('check_content');
        $grid->check_uid('check_uid');
        $grid->check_status('check_status');
        $grid->di('di');
        $grid->coins('coins');
        $grid->room_coins('room_coins');
        $grid->flowers('flowers');
        $grid->flowers_value('flowers_value');
        $grid->gold('gold');
        $grid->unsettled_price('unsettled_price');
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
        $show = new Show(UserUnion::findOrFail($id));

        $show->id('ID');
        $show->union_id('union_id');
        $show->user_id('user_id');
        $show->total_price('total_price');
        $show->settlement_price('settlement_price');
        $show->check_time('check_time');
        $show->check_content('check_content');
        $show->check_uid('check_uid');
        $show->check_status('check_status');
        $show->di('di');
        $show->coins('coins');
        $show->room_coins('room_coins');
        $show->flowers('flowers');
        $show->flowers_value('flowers_value');
        $show->gold('gold');
        $show->unsettled_price('unsettled_price');
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
        $form = new Form(new UserUnion);

        $form->display('ID');
        $form->text('union_id', 'union_id');
        $form->text('user_id', 'user_id');
        $form->text('total_price', 'total_price');
        $form->text('settlement_price', 'settlement_price');
        $form->text('check_time', 'check_time');
        $form->text('check_content', 'check_content');
        $form->text('check_uid', 'check_uid');
        $form->text('check_status', 'check_status');
        $form->text('di', 'di');
        $form->text('coins', 'coins');
        $form->text('room_coins', 'room_coins');
        $form->text('flowers', 'flowers');
        $form->text('flowers_value', 'flowers_value');
        $form->text('gold', 'gold');
        $form->text('unsettled_price', 'unsettled_price');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
