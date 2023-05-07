<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\DedicateAction;
use App\Helpers\Common;
use App\Models\Ware;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class DedicateWareController extends MainController
{
    use HasResourceActions;
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Ware);

        $grid->id('ID');
        $grid->column('get_type',__ ('get_type'))->select (
            [
                1=>trans ('vip level automatic acquisition'),
//               2=>trans ('activity'),
//               3=>trans ('treasure box'),
                4=>trans ('purchase'),
//               5=>trans ('background modification'),
                6=>trans ('limited time purchase'),
//               7=>trans ('treasure box point exchange'),
//               8=>trans ('cp level unlock'),
            ]
        );
        $grid->column('type',__ ('type'))->select (
            [
                1=>trans ('Gemstone'),
                3=>trans ('Card Scroll'),
                4=>trans ('Avatar Frame'),
                5=>trans ('Bubble Frame'),
                6=>trans ('Entering Special Effects'),
                7=>trans ('Microphone Aperture'),
                8=>trans ('Badge'),
                9=>trans ('NoKick'),
                10=>trans ('Icon'),
                11=>trans ('intro animation'),
                12=>trans ('wapel'),
                13=>trans ('hide country and last login'),
                14=>trans ('vip gifts'),
                15=>trans ('no pan'),
                16=>trans ('hidden room'),
                17=>trans ('anonymous man'),
                18=>trans ('colored name'),
                19=>trans ('profile visitors hide in'),
            ]
        );
        $grid->column('name',__ ('name'))->editable ();
        $grid->title(__('title'));
        $grid->column('price',__ ('price'))->currency ();
        $grid->level(__('level'));
        $grid->column('show_img',__ ('show_img'))->image ('',30);
        $grid->column('color',__('color'));
        $grid->expire(__('expire'));
        $grid->column('enable',__ ('enable'))->switch (Common::getSwitchStates ());
        $grid->sort('sort',__ ('sort'));
        $grid->actions (function ($actions){
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            $actions->add(new DedicateAction());
        });
        return $grid;
    }

}
