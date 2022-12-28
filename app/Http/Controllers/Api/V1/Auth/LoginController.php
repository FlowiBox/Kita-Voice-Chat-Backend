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
                return $this->loginWithGoogle ($fields);
            case 'facebook':
                $fields = ['name'=>$request->name,'email' => $request->email,'facebook_id' => $request->facebook_id];
                return $this->loginWithFacebook ($fields);
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
            $this->logoutAsConfiguration($user);
            $token = $user->createToken('api_token')->plainTextToken;
            $user->auth_token=$token;
            if (!$this->canLogin($user)){
                return Common::apiResponse (false,'you are blocked',[],401);
            }
            return Common::apiResponse (true,'logged in successfully',new UserResource($user),200);
        }else{
            return Common::apiResponse (false,'credentials does\'t match',[],401);
        }
    }

    protected function loginWithPhonePassword($fields){

        if (Auth::attempt($fields)){
            $user = \auth()->user();
            $this->logoutAsConfiguration($user);
            $token = $user->createToken('api_token')->plainTextToken;
            $user->auth_token=$token;
            if (!$this->canLogin($user)){
                return Common::apiResponse (false,'you are blocked',[],401);
            }
            return Common::apiResponse (true,'logged in successfully',new UserResource($user),200);
        }else{
            return Common::apiResponse (false,'credentials does\'t match',[],401);
        }
    }

    protected function loginWithGoogle($data){
        $user = User::query ()->whereNotNull ('google_id')->where ('google_id',$data['google_id'])->first ();
        if ($user){
            $this->logoutAsConfiguration($user);
            $token = $user->createToken('api_token')->plainTextToken;
            $user->auth_token=$token;
            if (!$this->canLogin($user)){
                return Common::apiResponse (false,'you are blocked',[],401);
            }
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

    protected function loginWithFacebook($data){
        $user = User::query ()->whereNotNull ('facebook_id')->where ('facebook_id',$data['facebook_id'])->first ();
        if ($user){
            $this->logoutAsConfiguration($user);
            $token = $user->createToken('api_token')->plainTextToken;
            $user->auth_token=$token;

            if (!$this->canLogin($user)){dd ($this->canLogin($user));
                return Common::apiResponse (false,'you are blocked',[],401);
            }

            return Common::apiResponse (true,'logged in successfully',new UserResource($user),200);
        }else{
            if (User::query ()->whereNotNull ('email')->where ('email',$data['email'])->exists ()){
                return Common::apiResponse (false,'email already taken',[],401);
            }else{
                $user = User::query ()->create (
                    [
                        'name'=>$data['name'],
                        'email'=>$data['email'],
                        'facebook_id'=>$data['facebook_id'],
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
        $code = rand (111111,999999);
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
            if (!$this->canLogin($user)){
                return Common::apiResponse (false,'you are blocked',[],401);
            }
            return Common::apiResponse (true,'',new UserResource($user),200);
        }
        return Common::apiResponse (false,'invalid code',null,422);
    }

    public function logout(Request $request){
        $request->user ()->tokens()->delete();
        return Common::apiResponse (1,'logged out');
    }


    protected function userWithToken($user){
        $this->logoutAsConfiguration($user);
        $token = $user->createToken('api_token')->plainTextToken;
        $user->auth_token=$token;
        return $user;
    }

    public function logoutAsConfiguration($user){
        if (Common::getConf ('login_from_only_one_device')){
            $user->tokens()->delete();
        }
    }


    public function canLogin($user){
        if ($user->status == 1){
            return true;
        }
        return false;
    }


}
