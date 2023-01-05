<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Emoji;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class EmojiController extends Controller
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
        $grid = new Grid(new Emoji);

        $grid->id('ID');
        $grid->column('pid',__ ('pid'));
        $grid->name(__('name'));
        $grid->column('emoji',trans ('emoji'))->image ('',30);
        $grid->t_length(__('t_length'));
        $grid->column('enable',trans ('enable'))->switch (Common::getSwitchStates ());
        $grid->sort(__('sort'));
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
        $show = new Show(Emoji::findOrFail($id));

        $show->id('ID');
        $show->pid('pid');
        $show->name('name');
        $show->emoji('emoji');
        $show->t_length('t_length');
        $show->enable('enable');
        $show->sort('sort');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Emoji);

        $form->display('ID');
        $form->select('pid', __('pid'))->options (function (){
            $ops = [0=>'root'];
            $ps = Emoji::query ()->where ('enable',1)->where ('pid',0)->where ('id','!=',$this->id)->get ();
            foreach ($ps as $p){
                $ops[$p->id] = $p->name;
            }
            return $ops;
        });
        $form->text('name', __('name'));
        $form->file('emoji', __('emoji'));
        $form->number('t_length', __('t_length'));
        $form->switch('enable', __('enable'))->states (Common::getSwitchStates ());
        $form->number('sort', __('sort'));

        return $form;
    }
}
