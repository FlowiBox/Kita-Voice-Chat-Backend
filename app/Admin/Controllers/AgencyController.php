<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Agency;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\AdminTraits\AdminUserTrait;
use Encore\Admin\Admin;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class AgencyController extends MainController
{
    use HasResourceActions,AdminUserTrait;

    public $permission_name = 'agencies';
    public $hiddenColumns = [

    ];



    public function show ( $id , Content $content )
    {
        return $content
            ->title(__($this->title))
            ->row(function($row) use ($id){
                $row->column(12, $this->usersGrid($id));
            });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {





        $grid = new Grid(new Agency);



        $grid->id('ID');
        $grid->column('owner_id',trans ('owner id'))->modal ('owner info',function ($model){
            return Common::getAdminShow ($model->owner_id);
        });
        $grid->column('name',trans ('name'));
        $grid->column('notice',trans ('notice'));
        $grid->column('status',trans ('status'))->switch(Common::getSwitchStates ());
        $grid->column('phone',trans ('phone'));
        $grid->column('img',trans ('img'))->image ('',30);

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
        $show = new Show(Agency::findOrFail($id));

        $show->id('ID');
        $show->field('owner_id',__('owner_id'));
        $show->field('name',__ ('name'));
        $show->field('notice',__ ('notice'));
        $show->field('status',__('status'));
        $show->field('phone',__ ('phone'));
        $show->field('url',__ ('url'));
        $show->field('img',__ ('image'))->image ('',200);
        $show->field('contents',__ ('contents'));

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

        $ops = [];
        foreach ($this->getAgencies () as $user){
            $ops[$user->id]=$user->name;
        }

        $form = new Form(new Agency);

        $form->display('ID');
        $form->select('owner_id', __('owner id'))->options ($ops);
        $form->text('name', __('name'));
        $form->text('notice', __('notice'));
        $form->switch('status', __('status'));
        $form->text('phone', __('phone'));
        $form->url('url', __('url'));
        $form->image('img', __('img'));
        $form->textarea('contents', __('contents'));
        return $form;
    }


    public function usersGrid($id){
        $grid = new Grid(new User());
        $grid->model ()->where ('agency_id',$id);
        $grid->quickSearch ();
        $grid->filter (function (Grid\Filter $filter){
            $filter->expand ();
            $filter->column(1/2, function ($filter) {
                $filter->equal('uuid',__ ('uuid'));
                $filter->equal('is_host',__('is host'))->select([0=>'normal',1=>'host']);
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('agency_id',__('agency'))->select(Common::by_agency_filter ());
                $filter->equal('family_id',__('Family'))->select(Common::by_family_filter ());
            });
        });
        $grid->column('id', __('Id'));
        $grid->column ('uuid',__('uuid'));

        $grid->column ('is_gold_id',__ ('use Gold id'))->switch (Common::getSwitchStates ());
        $grid->column('name', __('Name'));
        $grid->column('nickname', __('NickName'));
        $grid->column('email', __('Email'));
        $grid->column('phone', __('Phone'));
        $grid->column('di', __('coins'));
        $grid->column('gold', __('silver coins'));
        $grid->column('coins', __('diamonds'));
        $grid->column('status', __('block status'))->switch (Common::getSwitchStates2 () );
        $grid->column ('agency_id',__ ('agency id'))->modal ('agency info',function ($model){
            if ($model->agency_id){
                return Common::getAgencyShow ($model->agency_id);
            }
            return null;
        });

        $grid->column ('target',__ ('target'))->expand(function ($model) {

            $targets = $model->targets()->orderBy('created_at','desc')->get()->map(function ($target) {
                $au = $target->target_usd * $target->target_agency_share/100;
                $target->agency_obtain = $au;
                $target->user_obtain = $target->target_usd;
                $target = $target->only(
                    [
                        'id',
                        'add_month',
                        'add_year',
                        'target_usd',
                        'target_hours',
                        'target_days',
                        'target_agency_share',
                        'user_diamonds',
                        'user_hours',
                        'user_days',
                        'user_obtain',
                        'agency_obtain',
                        'updated_at'
                    ]
                );


                return $target;
            });

            return new Table(
                [
                    'ID',
                    __('month'),
                    __('year'),
                    __('usd').' '.__ ('deserved'),
                    __ ('target hours'),
                    __ ('target days'),
                    __ ('agency share').'(%)',
                    __ ('user diamonds'),
                    __ ('user hours'),
                    __ ('user days'),
                    __('user obtain'),
                    __('agency obtain'),
                    __('at time'),

                ]
                , $targets->toArray());
        });




        $this->extendGrid ($grid);


        return $grid;
    }
}
