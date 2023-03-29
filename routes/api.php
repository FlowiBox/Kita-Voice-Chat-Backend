<?php

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
    Route::post ('forget_password',[\App\Http\Controllers\Api\V1\Auth\ForgotPasswordController::class,'reset']);

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
        Route::post ('is_user_friend',[\App\Http\Controllers\Api\V1\HomeController::class,'check_if_friend']);
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
        Route::post('mute_microphone',[\App\Http\Controllers\Api\V1\RoomController::class,'mute_microphone']);
        Route::post('unmute_microphone',[\App\Http\Controllers\Api\V1\RoomController::class,'unmute_microphone']);
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

        Route::post ('remove_pass',[\App\Http\Controllers\Api\V1\RoomController::class,'removeRoomPass']);


        Route::get ('/',[\App\Http\Controllers\Api\V1\RoomController::class,'index']);
        Route::get ('/{id}',[\App\Http\Controllers\Api\V1\RoomController::class,'show']);
        Route::post ('/create',[\App\Http\Controllers\Api\V1\RoomController::class,'store']);
        Route::post ('/{id}/edit',[\App\Http\Controllers\Api\V1\RoomController::class,'update']);

        Route::post ('createPK',[\App\Http\Controllers\Api\V1\RoomController::class,'createPK']);
        Route::post ('closePK',[\App\Http\Controllers\Api\V1\RoomController::class,'closePK']);
        Route::post ('showPK',[\App\Http\Controllers\Api\V1\RoomController::class,'showPK']);



        Route::post ('admins',[\App\Http\Controllers\Api\V1\RoomController::class,'getAdmins']);
        Route::post ('firstOfRoom',[\App\Http\Controllers\Api\V1\RoomController::class,'firstOfRoom']);

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
        Route::post ('takeOff',[\App\Http\Controllers\Api\V1\UserController::class,'takeOff']);
        Route::get ('my_store',[\App\Http\Controllers\Api\V1\UserController::class,'my_store']);
        Route::get ('my_income',[\App\Http\Controllers\Api\V1\UserController::class,'my_income']);
        Route::post ('go_order_list',[\App\Http\Controllers\Api\V1\OrderController::class,'go_order_list']);
        Route::get ('get_user_union',[\App\Http\Controllers\Api\V1\UnionController::class,'getUserUnion']);
        Route::get ('agency_join_requests',[\App\Http\Controllers\Api\V1\AgencyController::class,'joinRequests']);
        Route::post ('getTimes',[\App\Http\Controllers\Api\V1\HomeController::class,'getTimes']);
    });

    Route::post ('send_pack',[\App\Http\Controllers\Api\V1\UserController::class,'sendPack']);

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
    Route::post ('hidePk',[\App\Http\Controllers\Api\V1\HomeController::class,'hidePk']);


    Route::prefix ('account')->group (function (){
        Route::post ('bind',[\App\Http\Controllers\Api\V1\UserController::class,'joinAccount']);
        Route::get ('delete',[\App\Http\Controllers\Api\V1\UserController::class,'delete']);
        Route::post ('change_phone',[\App\Http\Controllers\Api\V1\UserController::class,'changePhone']);
        Route::post ('reset_password',[\App\Http\Controllers\Api\V1\Auth\ResetPasswordController::class,'reset']);
    });

    Route::prefix ('agencies')->group (function (){
        Route::post ('join_request',[\App\Http\Controllers\Api\V1\AgencyController::class,'joinRequest']);
    });


    Route::prefix ('families')->group (function (){
        Route::get ('all',[\App\Http\Controllers\Api\V1\FamilyController::class,'index']);
        Route::get ('show/{id}',[\App\Http\Controllers\Api\V1\FamilyController::class,'show']);
        Route::post('create',[\App\Http\Controllers\Api\V1\FamilyController::class,'store']);
        Route::post ('ranking',[\App\Http\Controllers\Api\V1\FamilyController::class,'ranking']);
        Route::post ('edit/{id}',[\App\Http\Controllers\Api\V1\FamilyController::class,'update']);
        Route::post ('join',[\App\Http\Controllers\Api\V1\FamilyController::class,'join']);
        Route::get ('delete/{id}',[\App\Http\Controllers\Api\V1\FamilyController::class,'destroy']);
        Route::post ('remove_user',[\App\Http\Controllers\Api\V1\FamilyController::class,'removeUser']);
        Route::post ('req_list',[\App\Http\Controllers\Api\V1\FamilyController::class,'req_list']);
        Route::post ('take_action',[\App\Http\Controllers\Api\V1\FamilyController::class,'accdie']);
        Route::post ('change_user_type',[\App\Http\Controllers\Api\V1\FamilyController::class,'changeReqType']);
        Route::post ('getMembersList',[\App\Http\Controllers\Api\V1\FamilyController::class,'getMembersList']);
        Route::post ('getFamilyRooms',[\App\Http\Controllers\Api\V1\FamilyController::class,'getFamilyRooms']);
        Route::post ('exitFamily',[\App\Http\Controllers\Api\V1\FamilyController::class,'exitFamily']);
    });

    Route::post ('charge_to',[\App\Http\Controllers\Api\V1\UserController::class,'chargeTo']);
    Route::post ('charge_history',[\App\Http\Controllers\Api\V1\UserController::class,'charge_history']);
    Route::post ('chargePage',[\App\Http\Controllers\Api\V1\UserController::class,'chargePage']);

    Route::prefix ('black_list')->group (function (){
        Route::get ('/',[\App\Http\Controllers\Api\V1\BlackListController::class,'index']);
        Route::post ('/add',[\App\Http\Controllers\Api\V1\BlackListController::class,'add']);
        Route::post ('/remove',[\App\Http\Controllers\Api\V1\BlackListController::class,'remove']);
    });

    Route::prefix ('silver')->group (function (){
        Route::get ('/value',[\App\Http\Controllers\Api\V1\MallController::class,'silver_value']);
        Route::get ('/history',[\App\Http\Controllers\Api\V1\MallController::class,'silver_history']);
        Route::post ('/buy',[\App\Http\Controllers\Api\V1\MallController::class,'buySilverCoins']);
    });

    Route::prefix ('coins')->group (function (){
        Route::get ('/list',[\App\Http\Controllers\Api\V1\MallController::class,'coinList']);
        Route::post ('/buyCoins',[\App\Http\Controllers\Api\V1\MallController::class,'buyCoins']);
    });

    Route::prefix ('vips')->group (function (){
        Route::get ('/list',[\App\Http\Controllers\Api\V1\MallController::class,'vipList']);
        Route::post ('/buyVip',[\App\Http\Controllers\Api\V1\MallController::class,'buyVip']);
    });
    Route::prefix ('exchange')->group (function (){
        Route::get ('/list',[\App\Http\Controllers\Api\V1\HomeController::class,'exchangeList']);
        Route::post ('/make',[\App\Http\Controllers\Api\V1\HomeController::class,'exchangeSave']);
    });
    Route::post ('send_to_zego',[\App\Http\Controllers\Api\V1\HomeController::class,'sendToZego']);
    Route::post ('get_room_mode',[\App\Http\Controllers\Api\V1\RoomController::class,'roomMode']);
    Route::post ('change_room_mode',[\App\Http\Controllers\Api\V1\RoomController::class,'changeMode']);

    Route::get ('trxs',[\App\Http\Controllers\Api\V1\HomeController::class,'trxLog']);

    Route::prefix ('box')->group (function (){
        Route::get ('list',[\App\Http\Controllers\Api\V1\BoxController::class,'index']);
        Route::post ('send',[\App\Http\Controllers\Api\V1\BoxController::class,'send']);
        Route::post ('pickup',[\App\Http\Controllers\Api\V1\BoxController::class,'pick']);
    });

    Route::get ('my_gifts',[\App\Http\Controllers\Api\V1\GiftLogController::class,'giftLogsList']);
});

Route::prefix ('tickets')->group (function (){
    Route::post ('open',[\App\Http\Controllers\Api\V1\HomeController::class,'openTicket']);
});
