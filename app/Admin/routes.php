<?php


use Encore\Admin\Facades\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->resource ('auth/users','AdminUserController');


    //resources
    $router->resource('users', 'UserController');
    $router->resource('profiles', 'ProfileController');
    $router->resource('vips', 'VipController');
    $router->resource('rooms', 'RoomController');
    $router->resource('blacks', 'BlackListController');
    $router->resource('codes', 'CodeController');
    $router->resource ('gifts','GiftController');
    $router->resource ('wares','WareController');
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


    //--------------------
    $router->get('/', 'HomeController@infoBox')->name('home');
    $router->get('/dev', 'HomeController@devindex')->name('dev-home');


});



