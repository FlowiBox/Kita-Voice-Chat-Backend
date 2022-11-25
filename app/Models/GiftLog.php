<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftLog extends Model
{
    protected $table = 'gift_logs';

    public function gift(){
        return $this->belongsTo (Gift::class,'giftId','id');
    }
}
