<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request){
        $user = User::query ()->create(
            $request->validated ()
        );
        $token = $user->createToken('api_token')->plainTextToken;
        $user->auth_token=$token;
        return Common::apiResponse (true,'registered successfully',new UserResource($user),200);
    }


}
