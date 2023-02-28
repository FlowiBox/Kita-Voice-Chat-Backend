<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftLog extends Model
{
    protected $table = 'gift_logs';

    public function gift(){
        return $this->belongsTo (Gift::class,'giftId','id');
    }

    public function sender(){
        return $this->belongsTo (User::class,'sender_id');
    }

    public function receiver(){
        return $this->belongsTo (User::class,'receiver_id');
    }
}
