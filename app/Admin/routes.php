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



    //resources
    $router->resource('users', 'UserController');
    $router->resource('vips', 'VipController');
    $router->resource('rooms', 'RoomController');
    $router->resource('blacks', 'BlackListController');
    $router->resource('codes', 'CodeController');
    $router->resource ('gifts','GiftController');
    $router->resource ('wares','WareController');
    $router->resource ('coupons','CouponController');


    //--------------------
    $router->get('/', 'HomeController@index')->name('home');
    $router->get('/dev', 'HomeController@devindex')->name('dev-home');


});
