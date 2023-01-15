<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Code;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function reset(Request $request){
        if (!$request->phone || !$request->password || !$request->vr_code) return Common::apiResponse (0,'missing params');
        $user = $request->user ();
        if ($user->phone != $request->phone) return Common::apiResponse (0,'phone number not register with your account');
        $code = Code::query ()->where ('phone',$request->phone)->where('code',$request->vr_code)->first ();
        if (!$code) return Common::apiResponse (0,'this phone not verified');
        $user->password = $request->password;
        $code->delete ();

        $user->save();
        return Common::apiResponse (1,'reset successful',new UserResource($user));
    }
}
