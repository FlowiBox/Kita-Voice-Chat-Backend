<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomCategory extends Model
{
    protected $table = 'room_categories';

    public function typeRooms(){
        return $this->hasMany (Room::class,'room_type')->select ('id','numid','room_name','room_cover','room_intro');
    }

    public function classRooms(){
        return $this->hasMany (Room::class,'room_class')->select ('id','numid','room_name','room_cover','room_intro');
    }

    public function children(){
        return $this->hasMany (RoomCategory::class,'parent_id');
    }

    public function parent(){
        return $this->belongsTo (RoomCategory::class,'parent_id');
    }
}
