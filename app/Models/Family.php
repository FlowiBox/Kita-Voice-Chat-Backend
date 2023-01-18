<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $guarded = ['id'];

    public function owner(){
        return $this->belongsTo (User::class);
    }
}
