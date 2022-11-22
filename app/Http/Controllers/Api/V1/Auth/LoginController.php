<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request){
        switch ($request['type']){
            case 'email_pass':
                $fields = ['email'=>$request['email'],'password'=>$request['password']];
                return $this->loginWithEmailPassword ($fields);
            case 'phone_pass':
                $fields = ['phone'=>$request['phone'],'password'=>$request['password']];
                return $this->loginWithPhonePassword ($fields);
            case 'google':
                $data = ['name'=>$request->name,'email' => $request->email,'google_is' => $request->google_id];

        }
    }

    protected function loginWithEmailPassword($fields){
        if (Auth::attempt($fields)){
            $user = \auth()->user();
            $user->tokens()->delete();
            $token = $user->createToken('api_token')->plainTextToken;
            $user->auth_token=$token;
            return Common::apiResponse (false,'logged in successfully',new UserResource($user),200);
        }else{
            return Common::apiResponse (false,'credentials does\'t match',[],401);
        }
    }

    protected function loginWithPhonePassword($fields){
        if (Auth::attempt($fields)){
            $user = \auth()->user();
            $user->tokens()->delete();
            $token = $user->createToken('api_token')->plainTextToken;
            $user->auth_token=$token;
            return Common::apiResponse (false,'logged in successfully',new UserResource($user),200);
        }else{
            return Common::apiResponse (false,'credentials does\'t match',[],401);
        }
    }

    public function loginWithGoogle($data){
        $user = User::query ()->find ($data['google_id']);
        if ($user){
            $user->tokens()->delete();
            $token = $user->createToken('api_token')->plainTextToken;
            $user->auth_token=$token;
            return Common::apiResponse (false,'logged in successfully',new UserResource($user),200);
        }else{
            if (User::query ()->whereNotNull ('email')->where ('email',$data['email'])->exists ()){
                return Common::apiResponse (false,'email already taken',[],401);
            }else{
                $user = User::query ()->create (
                    [
                        'name'=>$data['name'],
                        'email'=>$data['email'],
                        'google_id'=>$data['google_id']
                    ]
                );
                $token = $user->createToken('api_token')->plainTextToken;
                $user->auth_token=$token;
                return Common::apiResponse (false,'logged in successfully',new UserResource($user),200);
            }
        }
    }
}
