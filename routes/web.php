<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

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

Route::get('/update-users', function () {

    $users = \App\Models\User::all();
    foreach ($users as $user){
        $user->enableSaving = false;
        $month_diamond_receiver = DB::table('gift_logs')
            ->where('receiver_id', $user->id)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
                           ->sum('giftPrice');

        $month_diamond_send = DB::table('gift_logs')
                                    ->where('sender_id', $user->id)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->whereMonth('created_at', Carbon::now()->month)
                                    ->sum('giftPrice');

        $class = new \App\Classes\Gifts\UpdateUserWhenSendGift();

        $user->monthly_diamond_received = intval($month_diamond_receiver) ?? 0;
        $user->monthly_diamond_send = intval($month_diamond_send) ?? 0;
//        $user->received_level = $class->getLevel(1, $month_diamond_receiver??0)->level ?? 0;
//        $user->sender_level = $class->getLevel(2, $month_diamond_send??0)->level ?? 0;
        $user->save();

    }
    return 'done';
});

Route::get('/t2', function () {
    return gethostname();
});

Route::get('/update', function () {
    DB::table('users')->where('agency_id', '!=', 0)->orWhere('agency_id', '!=', null)
        ->update(['monthly_diamond_received' => 0]);

    return 'Done';
});



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

Route::get('/clear', function() {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');

    return "Cleared!";

 });

 Route::get('test', function(){
    $disk = \Storage::disk('gcs');
$disk->put('hello.txt','hello text');
 });
