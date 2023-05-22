<?php

namespace App\Admin\Controllers;

use App\Models\Game;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class GameController extends Controller
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
        $grid = new Grid(new Game);

        $grid->id('ID');
        $grid->name('name');
        $grid->game_id('game_id');
        $grid->lang('lang');
        $grid->sign('sign');
        $grid->player_1('player_1');
        $grid->player_2('player_2');
        $grid->player1_amount('player1_amount');
        $grid->player2_amount('player2_amount');
        $grid->amount('amount');
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
        $show = new Show(Game::findOrFail($id));

        $show->id('ID');
        $show->name('name');
        $show->game_id('game_id');
        $show->lang('lang');
        $show->sign('sign');
        $show->player_1('player_1');
        $show->player_2('player_2');
        $show->player1_amount('player1_amount');
        $show->player2_amount('player2_amount');
        $show->amount('amount');
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
        $form = new Form(new Game);

        $form->display('ID');
        $form->text('name', 'name');
        $form->text('game_id', 'game_id');
        $form->text('lang', 'lang');
        $form->text('sign', 'sign');
        $form->text('player_1', 'player_1');
        $form->text('player_2', 'player_2');
        $form->text('player1_amount', 'player1_amount');
        $form->text('player2_amount', 'player2_amount');
        $form->text('amount', 'amount');
        $form->display(trans('admin.created_at'));
        $form->display(trans('admin.updated_at'));

        return $form;
    }
}
