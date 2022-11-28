<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    protected $guarded = ['id'];

    public function owner(){
        return $this->belongsTo (User::class,'uid','id');
    }
}
