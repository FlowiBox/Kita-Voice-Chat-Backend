<?php

namespace App\Admin\Controllers;

use App\Models\OfficialMessage;
use App\Http\Controllers\Controller;
use App\Models\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OfficialMessageController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'official-messages';
    public $hiddenColumns = [

    ];


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OfficialMessage);
        $grid->model ()->where ('type',2);
        $grid->id('ID');
        $grid->title(trans('title'));
        $grid->column('img',trans ('img'))->image ('',30);
        $grid->column('user_id',trans ('user id'));
        $grid->content(trans('content'));
//        $grid->column('type',trans ('type'))->select (
//            [
//                1=>trans('system message'),
//                2=>trans('system announcement')
//            ]
//        );
        $grid->url('url');
        $grid->created_at(trans('admin.created_at'));

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
        $show = new Show(OfficialMessage::findOrFail($id));

//        $show->id('ID');
//        $show->title(trans('title'));
//        $show->img('img');
//        $show->user_id('user_id');
//        $show->content('content');
//        $show->type('type');
//        $show->url('url');
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
        $form = new Form(new OfficialMessage);

        $form->display('ID');
        $form->text('title', trans('title'));
        $form->image('img', trans('img'));
        $form->select('user_id', trans('user'))->options (function (){
            $ops = [0=>trans ('all')];
            foreach (User::all () as $user){
                $ops[$user->id] = $user->id.'--'.($user->nicename?:$user->name);
            }
            return $ops;
        });
        $form->text('content', trans('content'));
        $form->select('type', trans('type'))->options (
            [
                2=>trans('official message')
            ]
        )->default (2);
        $form->text('url', __('url'));

        return $form;
    }
}
