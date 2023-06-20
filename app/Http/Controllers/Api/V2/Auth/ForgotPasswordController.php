<?php

namespace App\Http\Controllers\Api\V2\Auth;

use App\Helpers\Common;
use App\Helpers\FirebaseValidate;
use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class ForgotPasswordController extends Controller
{
    public function reset(Request $request){
        if (!$request->phone || !$request->password || !$request->credential) return Common::apiResponse(0, 'missing params');
        try {
            FirebaseValidate::validateIdToken($request['credential']);
        } catch (FailedToVerifyToken $e) {
            return Common::apiResponse(0, 'invalid credential', null, 422);
        } catch (\Exception $e) {
            // Error occurred while verifying the authentication token
            return Common::apiResponse(0, 'Un expected error', null, 422);
        }
        $user = User::query ()->where ('phone',$request->phone)->first ();
        $user->password = $request->password;
        $user->save();
        return Common::apiResponse (1,'reset successful',null);
    }
}
