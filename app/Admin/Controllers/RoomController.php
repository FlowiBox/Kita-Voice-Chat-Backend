<?php

namespace App\Admin\Controllers;

use App\Models\Room;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class RoomController extends Controller
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
        $grid = new Grid(new Room);

        $grid->id('ID');
        $grid->numid('numid');
        $grid->uid('uid');
        $grid->room_status('room_status');
        $grid->room_name('room_name');
        $grid->room_cover('room_cover');
        $grid->room_intro('room_intro');
        $grid->room_pass('room_pass');
        $grid->room_class('room_class');
        $grid->room_type('room_type');
        $grid->room_welcome('room_welcome');
        $grid->room_admin('room_admin');
        $grid->room_visitor('room_visitor');
        $grid->room_speak('room_speak');
        $grid->room_sound('room_sound');
        $grid->room_black('room_black');
        $grid->week_star('week_star');
        $grid->ranking('ranking');
        $grid->is_popular('is_popular');
        $grid->secret_chat('secret_chat');
        $grid->is_top('is_top');
        $grid->sort('sort');
        $grid->room_background('room_background');
        $grid->microphone('microphone');
        $grid->super_uid('super_uid');
        $grid->is_afk('is_afk');
        $grid->hot('hot');
        $grid->room_judge('room_judge');
        $grid->is_prohibit_sound('is_prohibit_sound');
        $grid->openid('openid');
        $grid->commission_proportion('commission_proportion');
        $grid->fresh_time('fresh_time');
        $grid->start_hour('start_hour');
        $grid->end_hour('end_hour');
        $grid->is_recommended('is_recommended');
        $grid->play_num('play_num');
        $grid->free_mic('free_mic');
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
        $show = new Show(Room::findOrFail($id));

        $show->id('ID');
        $show->numid('numid');
        $show->uid('uid');
        $show->room_status('room_status');
        $show->room_name('room_name');
        $show->room_cover('room_cover');
        $show->room_intro('room_intro');
        $show->room_pass('room_pass');
        $show->room_class('room_class');
        $show->room_type('room_type');
        $show->room_welcome('room_welcome');
        $show->room_admin('room_admin');
        $show->room_visitor('room_visitor');
        $show->room_speak('room_speak');
        $show->room_sound('room_sound');
        $show->room_black('room_black');
        $show->week_star('week_star');
        $show->ranking('ranking');
        $show->is_popular('is_popular');
        $show->secret_chat('secret_chat');
        $show->is_top('is_top');
        $show->sort('sort');
        $show->room_background('room_background');
        $show->microphone('microphone');
        $show->super_uid('super_uid');
        $show->is_afk('is_afk');
        $show->hot('hot');
        $show->room_judge('room_judge');
        $show->is_prohibit_sound('is_prohibit_sound');
        $show->openid('openid');
        $show->commission_proportion('commission_proportion');
        $show->fresh_time('fresh_time');
        $show->start_hour('start_hour');
        $show->end_hour('end_hour');
        $show->is_recommended('is_recommended');
        $show->play_num('play_num');
        $show->free_mic('free_mic');
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
        $form = new Form(new Room);

        $form->display('ID');
        $form->text('numid', 'numid');
        $form->text('uid', 'uid');
        $form->text('room_status', 'room_status');
        $form->text('room_name', 'room_name');
        $form->text('room_cover', 'room_cover');
        $form->text('room_intro', 'room_intro');
        $form->text('room_pass', 'room_pass');
        $form->text('room_class', 'room_class');
        $form->text('room_type', 'room_type');
        $form->text('room_welcome', 'room_welcome');
        $form->text('room_admin', 'room_admin');
        $form->text('room_visitor', 'room_visitor');
        $form->text('room_speak', 'room_speak');
        $form->text('room_sound', 'room_sound');
        $form->text('room_black', 'room_black');
        $form->text('week_star', 'week_star');
        $form->text('ranking', 'ranking');
        $form->text('is_popular', 'is_popular');
        $form->text('secret_chat', 'secret_chat');
        $form->text('is_top', 'is_top');
        $form->text('sort', 'sort');
        $form->text('room_background', 'room_background');
        $form->text('microphone', 'microphone');
        $form->text('super_uid', 'super_uid');
        $form->text('is_afk', 'is_afk');
        $form->text('hot', 'hot');
        $form->text('room_judge', 'room_judge');
        $form->text('is_prohibit_sound', 'is_prohibit_sound');
        $form->text('openid', 'openid');
        $form->text('commission_proportion', 'commission_proportion');
        $form->text('fresh_time', 'fresh_time');
        $form->text('start_hour', 'start_hour');
        $form->text('end_hour', 'end_hour');
        $form->text('is_recommended', 'is_recommended');
        $form->text('play_num', 'play_num');
        $form->text('free_mic', 'free_mic');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
