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

});
