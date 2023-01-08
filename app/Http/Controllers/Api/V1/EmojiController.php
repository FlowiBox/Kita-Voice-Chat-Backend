<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Models\Emoji;
use Illuminate\Http\Request;

class EmojiController extends Controller
{
    public function index(Request $request){
        $query = Emoji::query ()->where('enable',1);
        if ($request->pid){
            $query->where ('pid',$request->pid);
        }
        $data = $query->select ('id','pid','name','emoji','t_length','sort')->orderBy ('sort')->get ();
        return Common::apiResponse (1,'',$data);

    }

    public function show(Request $request,$id){
        if (!$request->user_id || !$request->room_id){
            return Common::apiResponse (0,'user_id , room_id are required');
        }
        $data = Emoji::query ()->select ('id','pid','name','emoji','t_length','sort')->find ($id);
        $d = [
            "messageContent"=>[
                "message"=>"showEmojie",
                "id"=>$data->id,
                "emoji"=>$data->emoji,
                "t_length"=>$data->t_length,
                "id_user"=>$request->user_id
            ]
        ];
        $json = json_encode ($d);
        $res = Common::sendToZego ('SendCustomCommand',$request->room_id,$request->user_id,$json);
        return Common::apiResponse (1,'',$data);
    }
}
