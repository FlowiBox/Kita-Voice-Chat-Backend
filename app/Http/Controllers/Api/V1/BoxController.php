<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BoxResource;
use App\Http\Resources\Api\V1\BoxUseResource;
use App\Models\Box;
use App\Models\BoxUse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoxController extends Controller
{
    public function index(){
        $normal = Box::query ()->where ('type',0)->orderByDesc ('id')->get ();
        $super = Box::query ()->where ('type',1)->orderByDesc ('id')->get ();
        $data = [
            'normal'=>BoxResource::collection($normal),
            'super'=>BoxResource::collection($super)
        ];
        return Common::apiResponse (1,'',$data,200);
    }

    public function send(Request $request){
        $box = Box::query ()->find ($request->box_id);
        $user = $request->user ();
        if (!$box) return Common::apiResponse (0,'not found',null,404);
        if ($request->label && $box->type == 1 && $box->has_label == 1){
            $label = $request->label;
        }
        if ($user->di < $box->coins){
            return Common::apiResponse (0,'low balance',null,407);
        }
        try {
            DB::beginTransaction ();
            $boxU = BoxUse::query ()->create (
                [
                    ''
                ]
            );
            $user->decrement ('di',$box->coins);
            DB::commit ();
            return Common::apiResponse (1,'',new BoxUseResource($boxU),200);
        }catch (\Exception $exception){
            DB::rollBack ();
            return Common::apiResponse (0,'fail',null,400);
        }
    }
}
