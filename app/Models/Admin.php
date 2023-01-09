<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Administrator
{
    public function agency(){
        return $this->hasOne (Agency::class,'owner_id');
    }


}
