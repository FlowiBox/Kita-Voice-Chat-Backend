<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserTarget extends Model
{
    protected $table = 'user_target';

    protected $guarded = ['id'];

    public function scopeOfAgency($q){
        $user = Auth::user ();
        if (Auth::user ()->isRole('agency')){
            $q->whereNotNull('agency_id')->where('agency_id', '=', @$user->agency_id);
        }

    }
}
