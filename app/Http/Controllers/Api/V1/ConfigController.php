<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ConfigValuesRequest;
use App\Models\Config;
use App\Models\Pack;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{

    public function getConfigValues(ConfigValuesRequest $request)
    {
        $configs = Common::getConfFromKey($request['keys']);

        $configs = $configs->flatMap(function($value) {
            return [
                $value->name => $value->value,
            ];
        } );
        if($request['enable-special'] == 1){
            $wapel = Pack::query ()
                         ->where ('type',12)
                         ->where ('expire','>=',time ())
                         ->where ('user_id', Auth::id())
                         ->where ('use_num','>',0)
                            ->select(['id', 'use_num'])
                         ->first ();
            $configs['wapel_num'] =  @(integer)$wapel->use_num ?? 0;
            $configs['user_coins'] =  @(integer)$request->user()->di ?? 0;
        }
        return Common::apiResponse (true,'config returned success',$configs,200);
    }

    public function anonymous_icon() {
        $anonymous_icon = Config::query()->where('name', 'anonymous_icon')->first();
        return Common::apiResponse (true,'anonymous icon',$anonymous_icon,200);
    }
}
