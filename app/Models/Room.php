<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['lang','country'];
//    protected $attributes = ['room_background'];

    protected $casts = [

    ];


    public function getRoomBackgroundAttribute($val){
        return @Background::query ()->where ('id',$val)->first ()->img;
    }

    public function owner(){
        return $this->belongsTo (User::class,'uid','id');
    }

    public function getLangAttribute(){
        return @$this->owner->country->language;
    }

    public function getCountryAttribute(){
        $country = @$this->owner->country;
        return $country;

    }


    public function myClass(){
        return $this->belongsTo (RoomCategory::class,'room_class')->select ('name','img');
    }

    public function myType(){
        return $this->belongsTo (RoomCategory::class,'room_type')->select ('name','img');
    }

    public function gifts(){
        return $this->hasMany (GiftLog::class,'roomowner_id','uid');
    }
}
