<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgencyJoinRequest extends Model
{
    protected $table = 'agency_join_requests';

    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo (User::class);
    }

    public function agency(){
        return $this->belongsTo (Agency::class);
    }
}
