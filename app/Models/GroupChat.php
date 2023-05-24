<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChat extends Model
{
    protected $table = 'group_chat';

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
