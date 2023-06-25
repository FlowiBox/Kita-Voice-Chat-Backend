<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\Auth\RegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Country;
use App\Models\User;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Firebase\Factory;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        if ($request->type == 'phone_pass') {
            try {
                $credentialPath = public_path('firebase_credentials.json');
                $factory         = (new Factory())->withServiceAccount($credentialPath);
                $auth            = $factory->createAuth();
                $verifiedIdToken = $auth->verifyIdToken($request['credential']);

                // Authentication token is valid
                $uid = $verifiedIdToken->claims()->get('sub');
                unset($request['credential']);
            } catch (FailedToVerifyToken $e) {
                return Common::apiResponse(0, 'invalid credential', null, 422);
            } catch (\Exception $e) {
                // Error occurred while verifying the authentication token
                return Common::apiResponse(0, 'Un expected error', null, 422);
            }
        }
        if (User::query()->where('phone', $request->phone)->exists()) {
            return Common::apiResponse(0, 'already exists', null, 405);
        }
        $user = User::query()->create(
            $request->validated()
        );
        $user = User::find($user->id);

        if (\request('tags') && is_array(\request('tags'))) {
            $user->tags()->attach(\request('tags'));
        }
        $user->is_points_first = 1;
        $user->save();
        if (!$request->country_id) {
            $country          = Country::query()->where('phone_code', '101')->first();
            $user->country_id = @$country->id ?: 0;
            $user->save();
        }
        $token            = $user->createToken('api_token')->plainTextToken;
        $user->auth_token = $token;
        return Common::apiResponse(true, 'registered successfully', new UserResource($user), 200);
    }
}
