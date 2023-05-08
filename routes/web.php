<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view ('welcome');
});

Route::prefix ('payment')->group (function (){
    Route::get ('payment-success',[\App\Http\Controllers\Web\PaymentController::class,'success']);
    Route::get ('payment-fail',[\App\Http\Controllers\Web\PaymentController::class,'fail']);
});

Route::get('/page/{name}', function ($name) {
    $page =  \App\Models\Page::query ()->where ('name',$name)->firstOrFail ();
    return $page->content;
});
