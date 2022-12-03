<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix ('auth')->group (function (){
    Route::post ('register',[\App\Http\Controllers\Api\V1\Auth\RegisterController::class,'register']);
    Route::post ('login',[\App\Http\Controllers\Api\V1\Auth\LoginController::class,'login']);
    Route::post ('send-code',[\App\Http\Controllers\Api\V1\Auth\LoginController::class,'sendPhoneCode']);
});

Route::middleware('auth:sanctum')->group (function (){
    Route::get ('my-data',[\App\Http\Controllers\Api\V1\UserController::class,'my_data']);
    Route::get ('get_myfavorite',[\App\Http\Controllers\Api\V1\UserController::class,'get_myfavorite']);
    Route::prefix ('users')->group (function (){
        Route::get ('/{id}',[\App\Http\Controllers\Api\V1\UserController::class,'show']);
    });
    Route::prefix ('profile')->group (function (){
        Route::get ('get/{id}',[\App\Http\Controllers\Api\V1\ProfileController::class,'show']);
        Route::post ('update',[\App\Http\Controllers\Api\V1\ProfileController::class,'update']);
    });
    Route::prefix ('relations')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\UserController::class,'userFriend']);
        Route::post ('follow',[\App\Http\Controllers\Api\V1\FollowController::class,'follow']);
        Route::post ('un-follow',[\App\Http\Controllers\Api\V1\FollowController::class,'unFollow']);
    });
    Route::prefix ('rooms')->group (function (){

        Route::post('enter_room',[\App\Http\Controllers\Api\V1\RoomController::class,'enter_room']);
        Route::post('quit_room',[\App\Http\Controllers\Api\V1\RoomController::class,'quit_room']);
        Route::post('getRoomUsers',[\App\Http\Controllers\Api\V1\RoomController::class,'getRoomUsers']);
        Route::post('microphone_status',[\App\Http\Controllers\Api\V1\RoomController::class,'microphone_status']);
        Route::post('up_microphone',[\App\Http\Controllers\Api\V1\RoomController::class,'up_microphone']);
        Route::post('leave_microphone',[\App\Http\Controllers\Api\V1\RoomController::class,'go_microphone']);
        Route::post('lock_microphone_place',[\App\Http\Controllers\Api\V1\RoomController::class,'shut_microphone']);
        Route::post('unlock_microphone_place',[\App\Http\Controllers\Api\V1\RoomController::class,'open_microphone']);
        Route::post('mute_microphone_place',[\App\Http\Controllers\Api\V1\RoomController::class,'is_sound']);
        Route::post('unmute_microphone_place',[\App\Http\Controllers\Api\V1\RoomController::class,'remove_sound']);





        Route::get ('/',[\App\Http\Controllers\Api\V1\RoomController::class,'index']);
        Route::get ('/{id}',[\App\Http\Controllers\Api\V1\RoomController::class,'show']);
        Route::post ('/create',[\App\Http\Controllers\Api\V1\RoomController::class,'store']);
        Route::post ('/{id}/edit',[\App\Http\Controllers\Api\V1\RoomController::class,'update']);

    });
    Route::prefix ('mall')->group (function (){
        Route::get ('wares',[\App\Http\Controllers\Api\V1\MallController::class,'index']);
    });
    Route::prefix ('gifts')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\GiftController::class,'index']);
    });

    Route::prefix ('countries')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\HomeController::class,'allCountries']);
        Route::get ('/{id}',[\App\Http\Controllers\Api\V1\HomeController::class,'getCountry']);
    });

    Route::prefix ('room_category')->group (function (){
        Route::get ('classes',[\App\Http\Controllers\Api\V1\HomeController::class,'allClasses']);
        Route::get ('types',[\App\Http\Controllers\Api\V1\HomeController::class,'getTypes']);
        Route::get ('types_by_class/{id}',[\App\Http\Controllers\Api\V1\HomeController::class,'getClassChildren']);
    });

    Route::prefix ('backgrounds')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\HomeController::class,'allBackgrounds']);
    });

});
