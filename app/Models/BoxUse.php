<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoxUse extends Model
{
    protected $table = 'box_uses';
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo (User::class,'user_id');
    }
}
