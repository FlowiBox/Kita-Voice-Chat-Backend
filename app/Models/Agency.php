<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{

    public function users(){
        return $this->hasMany (User::class);
    }

    public function scopeOfOwner($query, $owner_id)
    {
        return $query->where('owner_id', $owner_id);
    }

    public function owner(){
        return $this->belongsTo (User::class,'app_owner_id','id');
    }

    public function getUrlAttribute($val){
        if (!$val) {
            return "";
        }
        return $val;
    }
    public function getContentsAttribute($val){
        if (!$val) {
            return "";
        }
        return $val;
    }
}
