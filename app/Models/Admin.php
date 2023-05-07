<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Administrator
{
    protected $appends = ['agency_id'];
    public function agency(){
        return $this->hasOne (Agency::class,'owner_id');
    }

    public function getAgencyIdAttribute(){
        return @$this->agency->id;
    }

    public function getImgAttribute(){
        return $this->attributes['avatar'];
    }


}
