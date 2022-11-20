<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request){
        switch ($request['type']){
            case 'email_pass':
                $fields = ['email'=>$request['email'],'password'=>$request['password']];
                return $this->loginWithEmailPassword ($fields);
        }
    }

    protected function loginWithEmailPassword($fields){
        if (Auth::attempt($fields)){
            $user = \auth()->user();
            $user->tokens()->delete();
            $token = $user->createToken('api_token')->plainTextToken;
            $user->auth_token=$token;
            return Common::apiResponse (false,'logged in successfully',new UserResource($user),401);
        }else{
            return Common::apiResponse (false,'credentials does\'t match',[],401);
        }
    }
}
