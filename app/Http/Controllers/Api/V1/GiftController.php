<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\GiftResource;
use App\Models\Gift;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    public function index(Request $request){
        $user = $request->user ();
        $gifts = Gift::query ()->where ('enable',1)->where ('vip_level','<=',Common::getLevel ($user->id,3)?:0);
        if ($request->type){
            $gifts = $gifts->where ('type',$request->type);
        }
        $gifts = $gifts->orderBy ('sort')->get ();
        return Common::apiResponse (true,'',GiftResource::collection ($gifts),200);
    }












}
