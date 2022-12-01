<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function my_data(Request $request){
        $user = $request->user ();
        $data = new UserResource($user);
        return Common::apiResponse (true,'',$data,200);
    }

    public function show(Request $request,$id){
        $user = User::query ()->find ($id);
        if (!$user) return Common::apiResponse (false,'not found',null,404);
        $data = new UserResource($user);
        return Common::apiResponse (true,'',$data,200);
    }

    public function userFriend(Request $request){
        $user = $request->user ();
        switch ($request->type){
            case '1': // they follow me
                return Common::apiResponse (true,'',UserResource::collection ($user->followers()),200);
            case '2': // I follow them
                return Common::apiResponse (true,'',UserResource::collection ($user->followeds()),200);
            case '3': // friends [i follow them & they follow me]
                return Common::apiResponse (true,'',UserResource::collection ($user->friends()),200);
            default: // friends [i follow them & they follow me]
                return Common::apiResponse (false,'please select type',null,200);
        }
    }

    //my favorite room
    public function get_myfavorite(Request $request)
    {
        $user = $request->user ();
        $list = $user->value('mykeep');
        $mykeep = explode(',', $list);
        $room = DB::table('rooms','t1')
            ->join('users','t1.uid','=','users.id','left')
            ->select(['t1.numid','t1.uid','t1.room_name','t1.room_cover','t1.hot','t1.is_afk','users.nickname'])
            ->whereIn('t1.uid',$mykeep)
            ->get();
        $room=Common::roomDataFormat($room);
        $ar1=$ar2=[];
        foreach ($room as $key => &$v) {
            if($v['is_afk'] == 1){
                $ar1[]=$v;
            }else{
                $ar2[]=$v;
            }
        }
        unset($v);
        $arr['on']  = $ar1;
        $arr['off'] = $ar2;
        return common::apiResponse(1,'',$arr);
    }

}
