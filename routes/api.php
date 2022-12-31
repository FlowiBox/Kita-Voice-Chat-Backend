<?php

use App\Http\Controllers\Api\V1\UserController;
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
    Route::post ('auth/logout',[\App\Http\Controllers\Api\V1\UserController::class,'logout']);
    Route::get ('my-data',[\App\Http\Controllers\Api\V1\UserController::class,'my_data']);
    Route::get ('get_myfavorite',[\App\Http\Controllers\Api\V1\UserController::class,'get_myfavorite']);
    Route::prefix ('users')->group (function (){
        Route::get ('/{id}',[\App\Http\Controllers\Api\V1\UserController::class,'show']);
    });
    Route::prefix ('profile')->group (function (){
        Route::get ('get/{id}',[\App\Http\Controllers\Api\V1\ProfileController::class,'show']);
        Route::post ('update',[\App\Http\Controllers\Api\V1\ProfileController::class,'update']);
        Route::get ('visitors',[\App\Http\Controllers\Api\V1\ProfileController::class,'myProfileVisitorsList']);
    });
    Route::prefix ('relations')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\UserController::class,'userFriend']);
        Route::post ('follow',[\App\Http\Controllers\Api\V1\FollowController::class,'follow']);
        Route::post ('un-follow',[\App\Http\Controllers\Api\V1\FollowController::class,'unFollow']);
    });
    Route::prefix ('unions')->group (function (){
        Route::post ('add_union',[\App\Http\Controllers\Api\V1\UnionController::class,'addUnion']);
        Route::post ('add_user_to_union',[\App\Http\Controllers\Api\V1\UnionController::class,'addUserUnion']);
        Route::post ('union_target',[\App\Http\Controllers\Api\V1\UnionController::class,'unionTj']);
    });

    Route::prefix ('rooms')->group (function (){

//        Route::post ('get_other_user',[\App\Http\Controllers\Api\V1\RoomController::class,'get_other_user']);

        Route::post('enter_room',[\App\Http\Controllers\Api\V1\RoomController::class,'enter_room']);
        Route::post('quit_room',[\App\Http\Controllers\Api\V1\RoomController::class,'quit_room']);
        Route::post('kick_out_of_room',[\App\Http\Controllers\Api\V1\RoomController::class,'out_room']);
        Route::post('getRoomUsers',[\App\Http\Controllers\Api\V1\RoomController::class,'getRoomUsers']);
        Route::post('microphone_status',[\App\Http\Controllers\Api\V1\RoomController::class,'microphone_status']);
        Route::post('up_microphone',[\App\Http\Controllers\Api\V1\RoomController::class,'up_microphone']);
        Route::post('leave_microphone',[\App\Http\Controllers\Api\V1\RoomController::class,'go_microphone']);
        Route::post('lock_microphone_place',[\App\Http\Controllers\Api\V1\RoomController::class,'shut_microphone']);
        Route::post('unlock_microphone_place',[\App\Http\Controllers\Api\V1\RoomController::class,'open_microphone']);
        Route::post('mute_microphone_place',[\App\Http\Controllers\Api\V1\RoomController::class,'is_sound']);
        Route::post('unmute_microphone_place',[\App\Http\Controllers\Api\V1\RoomController::class,'remove_sound']);
        Route::post('get_room_by_owner_id',[\App\Http\Controllers\Api\V1\RoomController::class,'get_room_by_owner_id']);
        Route::post('add_admin_to_room',[\App\Http\Controllers\Api\V1\RoomController::class,'is_admin']);
        Route::post('remove_admin',[\App\Http\Controllers\Api\V1\RoomController::class,'remove_admin']);
        Route::post('ban_user_from_writing',[\App\Http\Controllers\Api\V1\RoomController::class,'is_black']);
        Route::post('room_background_list',[\App\Http\Controllers\Api\V1\RoomController::class,'room_background']);
        Route::post('check_if_has_pass',[\App\Http\Controllers\Api\V1\RoomController::class,'is_pass']);
        Route::get('check_if_i_have_room',[\App\Http\Controllers\Api\V1\RoomController::class,'amIHaveRoom']);




        Route::get ('/',[\App\Http\Controllers\Api\V1\RoomController::class,'index']);
        Route::get ('/{id}',[\App\Http\Controllers\Api\V1\RoomController::class,'show']);
        Route::post ('/create',[\App\Http\Controllers\Api\V1\RoomController::class,'store']);
        Route::post ('/{id}/edit',[\App\Http\Controllers\Api\V1\RoomController::class,'update']);

    });
    Route::prefix ('mall')->group (function (){
        Route::get ('wares',[\App\Http\Controllers\Api\V1\MallController::class,'index']);
        Route::post ('buy',[\App\Http\Controllers\Api\V1\UserController::class,'buyWare']);
    });
    Route::prefix ('gifts')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\GiftController::class,'index']);
        Route::post ('/send',[\App\Http\Controllers\Api\V1\GiftLogController::class,'gift_queue_six']);
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


    Route::prefix ('user_info')->group (function (){
        Route::get ('my_pack',[\App\Http\Controllers\Api\V1\UserController::class,'my_pack']);
        Route::post ('use_pack_item',[\App\Http\Controllers\Api\V1\UserController::class,'usePackItem']);
        Route::get ('my_store',[\App\Http\Controllers\Api\V1\UserController::class,'my_store']);
        Route::get ('my_income',[\App\Http\Controllers\Api\V1\UserController::class,'my_income']);
        Route::post ('go_order_list',[\App\Http\Controllers\Api\V1\OrderController::class,'go_order_list']);
        Route::get ('get_user_union',[\App\Http\Controllers\Api\V1\UnionController::class,'getUserUnion']);
    });

    Route::prefix ('community')->group (function (){
        Route::get ('official_messages',[\App\Http\Controllers\Api\V1\CommunityController::class,'official_messages']);
    });

    Route::prefix ('emojis')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\EmojiController::class,'index']);
        Route::get ('/{id}',[\App\Http\Controllers\Api\V1\EmojiController::class,'show']);
    });

    Route::prefix ('agora')->group (function (){
        Route::post ('/generate_token',[\App\Http\Controllers\Api\V1\HomeController::class,'generateAgoraToken']);
    });

    //tops
    Route::prefix ('ranking')->group (function (){
        Route::post ('/',[\App\Http\Controllers\Api\V1\UserController::class,'ranking']);
    });

    //search
    Route::prefix ('merge_search')->group (function (){
        Route::post ('/',[\App\Http\Controllers\Api\V1\CommunityController::class,'merge_search']);
    });


    Route::prefix ('home_carousels')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\HomeCarouselController::class,'index']);
    });


    Route::prefix ('vip_center')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\UserController::class,'vip_center']);
    });

    Route::prefix ('level_center')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\UserController::class,'level_center']);
    });



    Route::prefix ('search')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\CommunityController::class,'merge_search']);
        Route::get ('/history',[\App\Http\Controllers\Api\V1\CommunityController::class,'searchList']);
        Route::get ('/clean_search_history',[\App\Http\Controllers\Api\V1\CommunityController::class,'cleanSearchList']);
    });

    Route::get ('vip_count',[\App\Http\Controllers\Api\V1\HomeController::class,'countVips']);




});
