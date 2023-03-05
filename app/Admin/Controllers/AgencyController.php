<?php

namespace App\Admin\Controllers;

use App\Helpers\Common;
use App\Models\Agency;
use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\Room;
use App\Models\User;
use App\Models\UserTarget;
use App\Models\Ware;
use App\Traits\AdminTraits\AdminUserTrait;
use Encore\Admin\Admin;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\InfoBox;
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
            ->title(__("agency details"))
            ->row(function ($row) use ($id){
                $agency = Agency::find($id);
                $row->column(3, new InfoBox(__('Users'), 'users', 'aqua', '?type=users', User::query ()->where ('agency_id',$id)->count ()));
                $row->column(3, new InfoBox(__('Balance'), 'dollar', 'green', '?type=balance_details', $agency->old_usd + $agency->target_usd - $agency->target_token_usd));
                $row->column(3, new InfoBox(__('Targets'), 'gift', 'yellow', '?type=target', UserTarget::query ()->where ('agency_id',$id)->selectRaw ('agency_id,add_month,add_year,ROUND(SUM(agency_obtain), 2) as tot')
                    ->groupByRaw ('agency_id,add_month,add_year')->get ()->count ()));
//                $row->column(3, new InfoBox(__('Store'), 'shopping-cart', 'red', route ('admin.wares'), Ware::query ()->count ()));
            })
            ->row(function($row) use ($id){

                if (request ('type') == 'users'){
                    $row->column(12,__ ('Users'));
                    $row->column(12, $this->usersGrid($id));
                }elseif(request ('type') == 'target'){
                    $row->column(12,__ ('target'));
                    $row->column(12, $this->targetGrid($id));
                }elseif (request ('type') == 'balance_details'){
                    $row->column(12,__ ('balance details'));
                    $row->column(12, $this->balance_details($id));
                }else{
                    $row->column(12,__ ('target'));
                    $row->column(12, $this->targetGrid($id));
                }

            })

            ;


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

    protected function balance_details($id){
        $grid = new Grid(new Agency);

        $grid->model ()->where ('id',$id);

        $grid->id('ID');
        $grid->column('owner_id',trans ('owner id'))->modal ('owner info',function ($model){
            return Common::getAdminShow ($model->owner_id);
        });
        $grid->column('name',trans ('name'));
        $grid->column('old_usd',trans ('old'));
        $grid->column('target_usd',trans ('target'));
        $grid->column('target_token_usd',trans ('token'));
        $grid->column('img',trans ('img'))->image ('',30);
        $grid->disableActions ();
        $grid->disableCreateButton ();
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
            });
            $filter->column(1/2, function ($filter) {
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




        $grid->disableActions ();
        $grid->disableCreateButton ();


        return $grid;
    }

    public function targetGrid($id){
        $grid = new Grid(new UserTarget);
        $grid->filter (function (Grid\Filter $filter){
            $filter->expand ();
            $filter->column(1/2, function ($filter) {
                $filter->equal('add_month',__ ('month'));
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('add_year',__('year'));
            });
        });
        $grid->model ()->where ('agency_id',$id)
            ->selectRaw ('agency_id,add_month as m,add_year as y,ROUND(SUM(agency_obtain), 4) as tot')
            ->groupByRaw ('agency_id,m,y')
        ;
        $grid->column('agency_id',__ ('agency id'))->modal ('agency info',function ($model){
            return Common::getAgencyShow ($model->agency_id);
        });
        $grid->column('m',__ ('month'));
        $grid->column('y',__ ('year'));
        $grid->column('tot',__('agency obtain'));
        $grid->disableActions ();
        $grid->disableCreateButton ();

        return $grid;
    }

}
