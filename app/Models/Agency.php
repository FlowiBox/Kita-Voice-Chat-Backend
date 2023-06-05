<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{

    public function users(){
        return $this->hasMany (User::class);
    }

    public function scopeOfOwner($query, $owner_id)
    {
        return $query->where('owner_id', $owner_id);
    }

    public function owner(){
        return $this->belongsTo (User::class,'app_owner_id','id');
    }

    public function dashOwner(){
        return $this->belongsTo (Admin::class,'owner_id','id');
    }

    public function getUrlAttribute($val){
        if (!$val) {
            return "";
        }
        return $val;
    }
    public function getContentsAttribute($val){
        if (!$val) {
            return "";
        }
        return $val;
    }

    public function target($month = null,$year = null){
        if (!$month){
            $month = date ('m');
        }
        if (!$year){
            $year = date ('Y');
        }
        return $this->hasMany (AgencySallary::class)->where ('month',$month)->where ('year',$year)->first ();
    }

    public function getSalaryAttribute(){
        $salary = AgencySallary::query ()->where ('agency_id',$this->id)->where ('is_paid',0)->sum (\DB::raw('sallary - cut_amount'));
        $this->attributes['salary'] = $salary;
        return $salary;
    }
}
