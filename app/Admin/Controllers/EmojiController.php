<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Emoji;
use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class EmojiController extends MainController
{
    use HasResourceActions;
    public $permission_name = 'emoji';
    public $hiddenColumns = [

    ];


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
        $show = new Show(Emoji::findOrFail($id));

        $show->id('ID');
        $show->pid('pid');
        $show->name('name');
        $show->emoji('emoji');
        $show->t_length('t_length');
        $show->enable('enable');
        $show->sort('sort');
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
