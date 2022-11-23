<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request){
        $user = $request->user ();
        $data = new UserResource($user);
        return Common::apiResponse (true,'',$data,200);
    }

    public function userFriend(Request $request){
        $user = $request->user ();
        switch ($request->type){
            case '1': // they follow me
                return Common::apiResponse (true,'',UserResource::collection ($user->followers()),200);
            case '2': // I follow them
                return Common::apiResponse (true,'',UserResource::collection ($user->followeds()),200);
            case '3': // friends [i follow them & they follow me]
                return Common::apiResponse (true,'',UserResource::collection ($user->friends()),200);
            default: // friends [i follow them & they follow me]
                return Common::apiResponse (false,'please select type',null,200);
        }
    }

}
