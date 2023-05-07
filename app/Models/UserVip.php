<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVip extends Model
{
    protected $table = 'users_vips';
    protected $guarded = ['id'];

    public function OVip()
    {
        return $this->hasOne(OVip::class, 'vip_id','id');
    }
    
}
