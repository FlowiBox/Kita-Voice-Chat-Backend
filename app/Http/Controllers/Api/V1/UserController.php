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
}
