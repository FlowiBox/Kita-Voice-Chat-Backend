<?php

namespace App\Admin\Controllers;

use App\Models\GiftLog;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GiftLogController extends Controller
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
        $grid = new Grid(new GiftLog);

        $grid->id('ID');
        $grid->type('type');
        $grid->giftId('giftId');
        $grid->roomowner_id('roomowner_id');
        $grid->giftName('giftName');
        $grid->giftNum('giftNum');
        $grid->giftPrice('giftPrice');
        $grid->sender_id('sender_id');
        $grid->receiver_id('receiver_id');
        $grid->is_play('is_play');
        $grid->platform_obtain('platform_obtain');
        $grid->receiver_obtain('receiver_obtain');
        $grid->roomowner_obtain('roomowner_obtain');
        $grid->union_id('union_id');
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
        $show = new Show(GiftLog::findOrFail($id));

        $show->id('ID');
        $show->type('type');
        $show->giftId('giftId');
        $show->roomowner_id('roomowner_id');
        $show->giftName('giftName');
        $show->giftNum('giftNum');
        $show->giftPrice('giftPrice');
        $show->sender_id('sender_id');
        $show->receiver_id('receiver_id');
        $show->is_play('is_play');
        $show->platform_obtain('platform_obtain');
        $show->receiver_obtain('receiver_obtain');
        $show->roomowner_obtain('roomowner_obtain');
        $show->union_id('union_id');
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
        $form = new Form(new GiftLog);

        $form->display('ID');
        $form->text('type', 'type');
        $form->text('giftId', 'giftId');
        $form->text('roomowner_id', 'roomowner_id');
        $form->text('giftName', 'giftName');
        $form->text('giftNum', 'giftNum');
        $form->text('giftPrice', 'giftPrice');
        $form->text('sender_id', 'sender_id');
        $form->text('receiver_id', 'receiver_id');
        $form->text('is_play', 'is_play');
        $form->text('platform_obtain', 'platform_obtain');
        $form->text('receiver_obtain', 'receiver_obtain');
        $form->text('roomowner_obtain', 'roomowner_obtain');
        $form->text('union_id', 'union_id');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
