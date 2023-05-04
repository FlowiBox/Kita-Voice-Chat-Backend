<?php

namespace App\Admin\Controllers;

use App\Models\VipPrivilege;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class VipPrivilegeController extends MainController
{
    use HasResourceActions;

    public $permission_name = 'vip-privilege';
    public $hiddenColumns = [

    ];
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VipPrivilege);

        $grid->id('ID');
        $grid->column('name',__('name'));
        $grid->column('title',__ ('title'));
        $grid->column('type')->select (
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
        $grid->column('img1',__ ('img 1'))->image ('',30);
//        $grid->img2('img2');
//        $grid->created_at(trans('admin.created_at'));
//        $grid->updated_at(trans('admin.updated_at'));
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
        $show = new Show(VipPrivilege::findOrFail($id));

//        $show->id('ID');
//        $show->name('name');
//        $show->title('title');
//        $show->img1('img1');
//        $show->img2('img2');
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
        $form = new Form(new VipPrivilege);

        $form->display('ID');
        $form->text('name', __('name'));
        $form->text('title', __('title'));
        $form->select('type', trans('type'))->options (
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
        $form->image('img1', __('img1'));
        $form->file('img2', __('img2'));
//        $form->display(trans('admin.created_at'));
//        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
