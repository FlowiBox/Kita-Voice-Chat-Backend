<?php


use App\Classes\Facades\Agency;
use Encore\Admin\Facades\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group(
    [
        'prefix'        => config('admin.route.prefix'),
        'namespace'     => config('admin.route.namespace'),
        'middleware'    => [
            'web',
            'admin',
            'multiLanguage',
        ],
        'as'            => config('admin.route.prefix') . '.',
    ],
    function (Router $router) {


    $router->resource ('auth/users','AdminUserController');
    //resources
    $router->resource('users', 'UserController',[
        'names'=>[
            'index'=>'users',
            'show'=>'users.show'
        ]
    ]);
    $router->resource('profiles', 'ProfileController');
    $router->resource('vips', 'VipController');
    $router->resource('rooms', 'RoomController',[
        'names'=>[
            'index'=>'rooms'
        ]
    ]);
    $router->resource('blacks', 'BlackListController');
    $router->resource('codes', 'CodeController');
    $router->resource ('gifts','GiftController',[
        'names'=>[
            'index'=>'gifts'
        ]
    ]);
    $router->resource ('wares','WareController',[
        'names'=>[
            'index'=>'wares'
        ]
    ]);
    $router->resource ('coupons','CouponController');
    $router->resource ('configs','ConfigController');
    $router->resource ('categories','RoomCategoryController');
    $router->resource ('countries','CountryController');
    $router->resource ('backgrounds','BackgroundController');
    $router->resource ('official_msgs','OfficialMessageController');
    $router->resource ('emojis','EmojiController');
    $router->resource ('home_carousels','HomeCarouselController');
    $router->resource ('vip_prev','VipAuthController');
    $router->resource ('agencies','AgencyController');
    $router->resource ('families','FamilyController');
    $router->resource ('targets','TargetController');
    $router->resource ('charges','ChargeController',[
        'names'=>[
            'store'=>'charges.new'
        ]
    ]);
    $router->resource ('charge_values','ChargeValueController');
    $router->resource ('userTarget','UserTargetController',[
        'names'=>[
            'index'=>'user_targets'
        ]
    ]);


    //--------------------
    $router->get('/', 'HomeController@infoBox')->name('home');
    $router->get('/dev', 'HomeController@devindex')->name('dev-home');
    $router->get('/agency_home', 'HomeController@agencyInfoBox')->name('agency.home');

    $router->resource ('agency_join_requests','AgencyJoinRequestController');
    $router->resource ('family_levels','FamilyLevelController');
    $router->resource ('silver','SilverController');
    $router->resource ('coins','CoinController');
    $router->resource ('ovip','OVipController');
    $router->resource ('vip_privilege','VipPrivilegeController');
    $router->resource ('tickets','TicketController');
    $router->resource ('pages','PageController');


}
);













