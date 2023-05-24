<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Room;
use App\Helpers\Common;

class RequestBackgroundImage extends Model
{
    protected $table = 'request_background_images';

    public static function boot()
    {
        parent::boot();

        static::updating(function($model)
        {
            if($model->status == 1){
                $room = Room::where('uid',$model->owner_room_id)->first();
                $data = [
                    "messageContent"=>[
                        "message"=>"changeBackground",
                        "imgbackground"=>$model->img?:"",
                        "roomIntro"=>$room->room_intro?:"",
                        "roomImg"=>$room->room_cover?:"",
                        "room_type"=>@$room->myType->name?:"",
                        "room_name"=>@$room->room_name?:""
                    ]
                ];
                $json = json_encode ($data);
                $res = Common::sendToZego ('SendCustomCommand',$room->id,$model->owner_room_id,$json);
            }
        });
    }
}
