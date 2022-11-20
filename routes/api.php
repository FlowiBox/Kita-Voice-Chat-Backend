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
});

Route::middleware('auth:sanctum')->group (function (){
    Route::get ('my-data',[\App\Http\Controllers\Api\V1\UserController::class,'show']);
    Route::prefix ('profile')->group (function (){
        Route::get ('get/{id}',[\App\Http\Controllers\Api\V1\ProfileController::class,'show']);
        Route::post ('update',[\App\Http\Controllers\Api\V1\ProfileController::class,'update']);
    });
});
