<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Code;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request){
        if ($request->type == 'phone_pass'){
            if (!Code::query ()->whereNotNull (['phone','code'])->where ('phone',$request->phone)->where ('code',$request->code)->exists ()){
                return Common::apiResponse (0,'code not valid',null,422);
            }
            Code::query ()->where ('phone',$request->phone)->where ('code',$request->code)->delete ();
        }
        if (User::query ()->where ('phone',$request->phone)->exists ()){
            return Common::apiResponse (0,'already exists',null,405);
        }
        $user = User::query ()->create(
            $request->validated()
        );
        if (\request ('tags') && is_array (\request ('tags'))){
            $user->tags()->attach(\request ('tags'));
        }
        $user->is_points_first = 1;
        $user->save ();
        if (!$request->country_id){
            $country = Country::query ()->where('phone_code','101')->first ();
            $user->country_id = @$country->id?:0;
            $user->save();
        }
        $token = $user->createToken('api_token')->plainTextToken;
        $user->auth_token=$token;
        return Common::apiResponse (true,'registered successfully',new UserResource($user),200);
    }
}
