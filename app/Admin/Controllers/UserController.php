<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\ChargeAction;
use App\Admin\Actions\DeletePackAction;
use App\Admin\Actions\DeleteUserVipAction;
use App\Admin\Actions\EditPackExpireAction;
use App\Admin\Actions\KickOfAgencyAction;
use App\Admin\Actions\KickOfFamilyAction;
use App\Admin\Forms\ProfileForm;
use App\Helpers\Common;
use App\Models\Agency;
use App\Models\Charge;
use App\Models\Country;
use App\Models\Pack;
use App\Models\User;
use App\Models\UserTarget;
use App\Models\UserVip;
use App\Models\Ware;
use App\Traits\AdminTraits\AdminControllersTrait;
use Carbon\Carbon;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\App;


class UserController extends MainController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title;

    public $permission_name = 'users';
    public $hiddenColumns = [
        'is_host',
        'status',
        'is_gold_id',
        'id',
        'email',
        'phone',
        'di',
        'gold',
        'coins',
        'actions'
    ];

    public function __construct ()
    {
        $this->title ='Users';
    }

    public function index0 ( Content $content )
    {
        if (!Admin::user()->can('*')){
            Permission::check('browse-users');
        }

        $forms = [
            'one'=>ProfileForm::class,
            'tow'=>ProfileForm::class,
        ];

        return $content
            ->title(__($this->title))
            ->body(Tab::forms($forms));
    }

    public function index ( Content $content )
    {
        if (!Admin::user()->can('*')){
            Permission::check('browse-users');
        }

        return $content
            ->title(__($this->title))
            ->row(function($row) {
                $row->column(12, $this->grid());
            });
    }

    public function show ( $id , Content $content )
    {
        return $content->row(
            function ($row) use ($id){
                $user = User::find($id);
                $row->column(3, new InfoBox(__('Balance'), 'dollar', 'green', '?type=balance_details', $user->old_usd + $user->target_usd - $user->target_token_usd));
            }
        )->row ("<h3>".__('pack')."</h3>")->row (function ($row) use ($id){
            $row->column(12, $this->packList($id));
        })
            ->row ("<h3>".__('vips')."</h3>")->row (function ($row) use ($id){
                $row->column(12, $this->vipList($id));
            })
        ;
    }


    public function update ( $id )
    {
        return $this->form()->update($id);
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new User());
        $grid->model ()->ofAgency();
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

//        $grid->column ('is_gold_id',__ ('use Gold id'))->switch (Common::getSwitchStates ());
        $grid->column('name', __('Name'));
        $grid->column('nickname', __('NickName'));
//        $grid->column('email', __('Email'));
        $grid->column('phone', __('Phone'));
//        $grid->column('di', __('coins'));
//        $grid->column('gold', __('silver coins'));
//        $grid->column('coins', __('diamonds'));
//        $grid->column('status', __('block status'))->switch (Common::getSwitchStates2 () );
        $grid->column ('agency_id',__ ('agency id'))->modal ('agency info',function ($model){
            if ($model->agency_id){
                $a = Agency::query ()->find ($model->agency_id);
                if (!$a){
                    $model->agency_id = 0;
                    $model->save();
                    return null;
                }
                return Common::getAgencyShow (@$model->agency_id);
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




        $this->extendGrid ($grid);

        $grid->actions (function ($actions){
            $actions->add(new KickOfAgencyAction());
            $actions->add(new KickOfFamilyAction());
        });


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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('uuid', __('uuid'));
        $show->field ('avatar',__('avatar'))->image ('',200);
        $show->field('name', __('Name'));
        $show->field('nickname', __('NickName'));
        $show->field('flag', __('country'))->image ('',50);
        $show->field('email', __('Email'));
        $show->field('di', __('coins'));
        $show->field('gold', __('silver coins'));
        $show->field('coins', __('diamonds'));
        $show->field('is_host', __('is host'))->using (
            [
                0=>__ ('not host'),
                1=>__('host')
            ]
        );
        $show->field ('intro',__ ('intro'))->image ();
        $show->field ('frame',__ ('frame'))->image ();

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
        $form = new Form(new User());

        $form->display('id', __('id'));
        $form->text('uuid', __('uuid'));
//        $form->switch ('is_gold_id',__('Gold id'))->states (Common::getSwitchStates ());
        $form->text('name', __('Name'));
        $form->image ('profile.avatar',__ ('image'));
        $form->select ('country_id',trans ('country'))->options (function (){
            $ops = [0=>'no country'];
            $countries = Country::all ();
            foreach ($countries as $country){
                $ops[$country->id] = App::isLocale('en') ? $country->e_name : $country->name;
            }
            return $ops;
        });
        $form->select ('agency_id',__('agency'))->options(function (){
            $ops = [0=>'no agency'];
            $agencies = Agency::query ()->where ('status',1)->get ();
            foreach ($agencies as $agency){
                $ops[$agency->id]=$agency->name;
            }
            return $ops;
        });
//        $form->date('profile.birthday',__ ('birthday'));
        $form->select ('profile.gender',__ ('gender'))->options ([0=>__ ('female'),1=>__ ('male')]);
        $form->number ('di',__('coins'));
        $form->number ('gold',__('silver coins'));
        $form->email('email', __('Email'))->attribute ('onfocus',"this.removeAttribute('readonly');")->attribute ('readonly');
        $form->password('password', __('Password'))->attribute ('onfocus',"this.removeAttribute('readonly');")->attribute ('readonly');
        $form->text('phone', __('phone'));
        $form->text('facebook_id', __('facebook id'));
        $form->text('google_id', __('google id'));
//        $form->switch ('is_host',__('is host'))->options (Common::getSwitchStates ());
        $form->switch ('status',__('block status'))->options (Common::getSwitchStates2 ());
//        $form->html(function (){
//            if (!$this->intro){
//                return __('empty');
//            }
//            return "<img width='50' title='intro img' src='".asset ('storage').'/'.$this->intro."'>";
//        });
//        $form->select ('dress_3',__ ('intro'))->options (function (){
//            $arr = [0=>__ ('empty')];
//            $pack = Pack::query ()->where ('user_id',@$this->id)->where ('type',6)->where (function ($q){
//                $q->where ('expire',0)->orWhere ('expire','>',Carbon::now ()->timestamp);
//            })->get ();
//
//            foreach ($pack as $item){
//                $ware = Ware::find(@$item->target_id);
//                if ($ware){
//                    $arr[$ware->id]=$ware->name;
//                }
//            }
//            return $arr;
//        });
//        $form->html(function (){
//            if (!$this->frame){
//                return __('empty');
//            }
//            return "<img width='50' title='intro img' src='".asset ('storage').'/'.$this->frame."'>";
//        });
//        $form->select ('dress_1',__ ('frame'))->options (function (){
//            $arr = [0=>__ ('empty')];
//            $pack = Pack::query ()->where ('user_id',@$this->id)->where ('type',4)->where (function ($q){
//                $q->where ('expire',0)->orWhere ('expire','>',Carbon::now ()->timestamp);
//            })->get ();
//
//            foreach ($pack as $item){
//                $ware = Ware::find(@$item->target_id);
//                if ($ware){
//                    $arr[$ware->id]=$ware->name;
//                }
//            }
//            return $arr;
//        });

        return $form;
    }



    protected function packList($id){
        Pack::query ()->where ('expire','!=',0)->where ('expire','<',time ())->delete();
        $grid = new Grid(new Pack);
        $grid->model ()->where ('user_id',$id);
        $grid->id('ID');
        $grid->column('user_id',__ ('user id'));
        $grid->column('get_type',__ ('get type'))->using (
            [
                1=>__ ('vip level automatic acquisition'),
                2=>__ ('activities'),
                3=>__ ('treasure box'),
                4=>__ ('purchase'),
                5=>__ ('background addition'),
            ]
        );
        $grid->column('type',__ ('type'))->using (
            [
                1=>__ ('gem'),
                2=>__ ('gift'),
                3=>__ ('card roll'),
                4=>__ ('avatar frame'),
                5=>__ ('bubble frame'),
                6=>__ ('entry special effects'),
                7=>__ ('microphone aperture'),
                8=>__ ('badge'),
            ]
        );
        $grid->column('target_id',__ ('img'))->display (function (){
            $ware = Ware::query ()->where ('id',$this->target_id)->value ('show_img');
            $src = asset ("storage/$ware");
            return "<img width='30' src='$src'>";
        });
        $grid->column('expire',__ ('expire'))->display (function ($row){
            if ($this->expire){
                return Carbon::createFromTimestamp($this->expire)->format('Y-m-d H:i:s');
            }
            return __ ('no time');
        });

        $grid->actions (function ($actions){
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            $actions->add(new DeletePackAction());
            $actions->add(new EditPackExpireAction());
        });

        $grid->disableCreateButton ();
        $grid->disableFilter ();
        $grid->disableRowSelector ();
        $grid->disableExport ();

        return $grid;
    }

    protected function vipList($id){

        UserVip::query ()->where ('expire','!=',0)->where ('expire','<',time ())->delete ();

        $grid = new Grid(new UserVip());
        $grid->model ()->where ('user_id',$id);
        $grid->id('ID');
        $grid->column('user_id',__ ('user id'));
        $grid->column ('level',__ ('level'));
        $grid->column('expire',__ ('expire'))->display (function ($row){
            if ($this->expire){
                return Carbon::createFromTimestamp($this->expire)->format('Y-m-d H:i:s');
            }
            return __ ('no time');
        });

        $grid->actions (function ($actions){
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            $actions->add(new DeleteUserVipAction());
//            $actions->add(new EditPackExpireAction());
        });

        $grid->disableCreateButton ();
        $grid->disableFilter ();
        $grid->disableRowSelector ();
        $grid->disableExport ();

        return $grid;
    }

}
