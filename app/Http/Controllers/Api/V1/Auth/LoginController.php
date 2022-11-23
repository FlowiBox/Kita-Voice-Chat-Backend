<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Code;
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
                $fields = ['name'=>$request->name,'email' => $request->email,'google_id' => $request->google_id];
                return $this->loginWithPhoneCode ($fields);
            case 'phone_code':
                $fields = ['phone' => $request->phone,'code'=>$request->code];
                return $this->loginWithPhoneCode ($fields);
            default :
                return Common::apiResponse (false,'invalid login method',null,422);
        }
    }

    protected function loginWithEmailPassword($fields){
        if (Auth::attempt($fields)){
            $user = \auth()->user();
            $user->tokens()->delete();
            $token = $user->createToken('api_token')->plainTextToken;
            $user->auth_token=$token;
            return Common::apiResponse (true,'logged in successfully',new UserResource($user),200);
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

    protected function loginWithGoogle($data){
        $user = User::query ()->find ($data['google_id']);
        if ($user){
            $user->tokens()->delete();
            $token = $user->createToken('api_token')->plainTextToken;
            $user->auth_token=$token;
            return Common::apiResponse (true,'logged in successfully',new UserResource($user),200);
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
                return Common::apiResponse (true,'logged in successfully',new UserResource($user),200);
            }
        }
    }

    protected function sendPhoneCode(Request $request){
        if (!$request->phone){
            return Common::apiResponse (false,'require phone',null,422);
        }
        $code = rand (1111,9999);
        Code::query ()->create (
            [
                'phone'=>$request->phone,
                'code'=>$code
            ]
        );
        return Common::apiResponse (true,'code is sent to your phone',null,200);
    }

    protected function loginWithPhoneCode($fields){
        if (Code::query ()->whereNotNull (['phone','code'])->where ('phone',$fields['phone'])->where ('code',$fields['code'])->exists ()){
            $user = User::query ()->updateOrCreate (
                [
                    'phone'=>$fields['phone']
                ],
                [
                    'phone'=>$fields['phone'],
                ]
            );
            $user = $this->userWithToken ($user);
            Code::query ()->where ('phone',$fields['phone'])->where ('code',$fields['code'])->delete ();
            return Common::apiResponse (true,'',new UserResource($user),200);
        }
        return Common::apiResponse (false,'invalid code',null,422);
    }


    protected function userWithToken($user){
        $user->tokens()->delete();
        $token = $user->createToken('api_token')->plainTextToken;
        $user->auth_token=$token;
        return $user;
    }


}
