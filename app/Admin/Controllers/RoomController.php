<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Room;
use App\Http\Controllers\Controller;
use App\Models\RoomCategory;
use App\Models\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;

use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class RoomController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'rooms';
    public $hiddenColumns = [

    ];


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Room);

        $grid->filter (function (Grid\Filter $filter){
            $filter->expand ();
            $filter->column(1/2, function ($filter) {
                $filter->equal('uid',__ ('owner_id'));
            });
        });

        $grid->id('ID');
        $grid->column('numid',trans ('custom id'));
        $grid->column('uid',trans ('room owner'))->display (function ($uid){
            $user = User::query ()->find ($uid);
            if($user){
                return "<a href='users/$uid'>".$user->name != '' ? $user->name : trans('no name')."</a>";
            }
            return "N/A";
        });


        $grid->column('room_status')->switch (Common::getSwitchStates ());
        $grid->column('top_room')->switch (Common::getSwitchStates ());
        $grid->column('room_name',trans ('room name'));
        $grid->column('room_cover',trans ('room cover'))->image ('',30);
        $grid->column('room_intro',trans ('room_intro'));
        $grid->column('microphone',trans ('microphone'));
        $grid->column('room_visitor',trans ('room_visitor'));
        $grid->column('is_afk',trans ('owner in'))->switch (Common::getSwitchStates ());
//        $grid->column('free_mic',trans ('is mic free'))->switch (Common::getSwitchStates ());

        $grid->actions (function ($action){
            $action->disableView ();
        });
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
        $show = new Show(Room::findOrFail($id));

//        $show->id('ID');
//        $show->numid('numid');
//        $show->uid('uid');
//        $show->room_status('room_status');
//        $show->room_name('room_name');
//        $show->room_cover('room_cover');
//        $show->room_intro('room_intro');
//        $show->room_pass('room_pass');
//        $show->room_class('room_class');
//        $show->room_type('room_type');
//        $show->room_welcome('room_welcome');
//        $show->room_admin('room_admin');
//        $show->room_visitor('room_visitor');
//        $show->room_speak('room_speak');
//        $show->room_sound('room_sound');
//        $show->room_black('room_black');
//        $show->week_star('week_star');
//        $show->ranking('ranking');
//        $show->is_popular('is_popular');
//        $show->secret_chat('secret_chat');
//        $show->is_top('is_top');
//        $show->sort('sort');
//        $show->room_background('room_background');
//        $show->microphone('microphone');
//        $show->super_uid('super_uid');
//        $show->is_afk('is_afk');
//        $show->hot('hot');
//        $show->room_judge('room_judge');
//        $show->is_prohibit_sound('is_prohibit_sound');
//        $show->openid('openid');
//        $show->commission_proportion('commission_proportion');
//        $show->fresh_time('fresh_time');
//        $show->start_hour('start_hour');
//        $show->end_hour('end_hour');
//        $show->is_recommended('is_recommended');
//        $show->play_num('play_num');
//        $show->free_mic('free_mic');
//        $show->created_at(trans('admin.created_at'));
//        $show->updated_at(trans('admin.updated_at'));
        $this->extendShow ($show);
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
        $form->text('numid', __('numid'));
//        $form->select ('uid',trans ('owner'))->options (function (){
//            $users = User::query ()->whereDoesntHave ('rooms')->get ();
//            $arr = [];
//            foreach ($users as $user){
//                $arr[$user->id]=($user->nickname?:$user->name).'[id='.$user->id.']';
//            }
//            if (count ($users) < 1){
//                $arr[0]='no users dose not have room found';
//            }
//            return $arr;
//        });
        $form->switch('room_status', __('room_status'))->options (Common::getSwitchStates ());
        $form->switch('top_room', __('top_room'))->options (Common::getSwitchStates ());
        $form->text('room_name', trans('room name'));
        $form->image('room_cover', trans('room cover'));
        $form->text('room_intro', trans('room intro'));
        $form->text('room_pass', trans('room pass'));
        $form->hidden('is_afk', trans('owner in'));
        $form->select ('room_class')->options (function (){
            $options = [];
            $cats = RoomCategory::query ()->where ('enable',1)->where ('parent_id',0)->get ();
            foreach ($cats as $cat){
                $options[$cat->id] = $cat->name;
            }
            return $options;
        });
        $form->select('room_type', trans('room type'))->options (function (){
            $options = [];
            $cats = RoomCategory::query ()->where ('enable',1)->where ('parent_id',$this->room_class)->get ();
            foreach ($cats as $cat){
                $options[$cat->id] = $cat->name;
            }
            return $options;
        });
        $form->text('room_welcome', trans('room welcome'));


        return $form;
    }
}
