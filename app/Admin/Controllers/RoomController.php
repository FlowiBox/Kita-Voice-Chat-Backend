<?php

namespace App\Admin\Controllers;

use App\Models\Room;
use App\Http\Controllers\Controller;
use App\Models\User;
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
        $grid->column('numid',trans ('custom id'));
        $grid->column('uid',trans ('room owner'))->display (function ($uid){
            $user = User::query ()->find ($uid);
            if($user){
                return "<a href='users/$uid'>".$user->name != '' ? $user->name : trans('no name')."</a>";
            }
            return "N/A";
        });


        $grid->column('room_status')->using (
            [
                1=>trans ('normal'),
                2=>trans ('closed'),
                3=>trans ('baned'),
                4=>trans ('canceled')
            ]
        );
        $grid->column('room_name');
        $grid->column('room_cover');
        $grid->column('room_intro');
        $grid->microphone('microphone');
        $grid->free_mic('free_mic');


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

        $form->select('room_status', 'room_status')->options (
            [
                1=>trans ('normal'),
                2=>trans ('closed'),
                3=>trans ('baned'),
                4=>trans ('canceled')
            ]
        );
        $form->text('room_name', 'room_name');
        $form->image('room_cover', 'room_cover');
        $form->text('room_intro', 'room_intro');
        $form->text('room_pass', 'room_pass');

        $form->text('room_type', 'room_type');
        $form->text('room_welcome', 'room_welcome');


        $form->text('is_recommended', 'is_recommended');

        $form->text('free_mic', 'free_mic');


        return $form;
    }
}
