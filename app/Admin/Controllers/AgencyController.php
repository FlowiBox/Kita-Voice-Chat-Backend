<?php

namespace App\Admin\Controllers;

use App\Models\Agency;
use App\Http\Controllers\Controller;
use App\Traits\AdminTraits\AdminUserTrait;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class AgencyController extends Controller
{
    use HasResourceActions,AdminUserTrait;

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

        $ops = [];
        foreach ($this->getAdminUsers () as $user){
            $ops[$user->id]=$user->name;
        }



        $grid = new Grid(new Agency);

//        $user = auth ()->user ();
//        if (!$user->isRole('admin') && !$user->isRole('developer')){
//            $grid->model()->ofOwner($user->id);
//        }

        $grid->id('ID');
        $grid->column('owner_id',trans ('owner id'))->select ($ops);
        $grid->column('name',trans ('name'));
        $grid->column('notice',trans ('notice'));
        $grid->column('status',trans ('status'))->select (
            [
                0=>trans ('disabled'),
                1=>trans ('enabled'),
            ]
        );
        $grid->column('phone',trans ('phone'));
        $grid->column('img',trans ('img'))->image ('',30);

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
        $show = new Show(Agency::findOrFail($id));

        $show->id('ID');
        $show->owner_id('owner_id');
        $show->name('name');
        $show->notice('notice');
        $show->status('status');
        $show->phone('phone');
        $show->url('url');
        $show->img('img');
        $show->contents('contents');
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

        $ops = [];
        foreach ($this->getAdminUsers () as $user){
            $ops[$user->id]=$user->name;
        }

        $form = new Form(new Agency);

        $form->display('ID');
        $form->select('owner_id', 'owner_id')->options ($ops);
        $form->text('name', 'name');
        $form->text('notice', 'notice');
        $form->text('status', 'status');
        $form->text('phone', 'phone');
        $form->text('url', 'url');
        $form->text('img', 'img');
        $form->text('contents', 'contents');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
