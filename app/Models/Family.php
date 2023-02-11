<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $guarded = ['id'];


    public function owner(){
        return $this->belongsTo (User::class);
    }

    public function getMembersNumAttribute(){
        $fu = FamilyUser::query ()->where ('family_id',$this->id)->where ('status',1)->where ('user_type',0)->count ();
        return $fu;
    }

    public function getAdminsNumAttribute(){
        $fu = FamilyUser::query ()->where ('family_id',$this->id)->where ('status',1)->where ('user_type',1)->count ();
        return $fu;
    }

    public function getLevelAttribute(){
        $giftLogs = GiftLog::query ()->where (function ($q){
            $q->where ('receiver_family_id',$this->id)->orWhere('sender_family_id',$this->id);
        })->sum ('giftPrice');
        $cur_level = FamilyLevel::query ()->where ('exp','<=',$giftLogs)->orderByDesc ('exp')->first ();
        $next_level = FamilyLevel::query ()->where ('exp','>',$giftLogs)->orderBy ('exp')->first ();
        $min_exp = @$cur_level->exp?:0;
        $over = $giftLogs - $min_exp;
        $diff = @$next_level->exp - @$cur_level->exp;
        $lev = [
            'level_exp'=>@$cur_level->exp?:0,
            'level_name'=>@$cur_level->name?:'',
            'level_img'=>@$cur_level->img?:'',
            'family_exp'=>$giftLogs,
            'over_current_level_exp'=>$over,
            'next_exp'=>@$next_level->exp,
            'next_name'=>@$next_level->name,
            'next_img'=>@$next_level->img,
            'per'=> $over/$diff
        ];

        return $lev;
    }

    public function getLevelMaxMembersNumAttribute(){
        $giftLogs = GiftLog::query ()->where ('receiver_family_id',$this->id)->sum ('giftPrice');
        $level = FamilyLevel::query ()->where ('exp','<=',$giftLogs)->orderByDesc ('exp')->first ();
        if ($level){
            return $level->members;
        }
        return null;
    }

    public function getLevelMaxAdminsNumAttribute(){
        $giftLogs = GiftLog::query ()->where ('receiver_family_id',$this->id)->sum ('giftPrice');
        $level = FamilyLevel::query ()->where ('exp','<=',$giftLogs)->orderByDesc ('exp')->first ();
        if ($level){
            return $level->admins;
        }
        return null;
    }

    public function getNumAttribute($value){
        if ($this->level_max_members_num){
            return $this->level_max_members_num;
        }
        return $value;
    }

    public function getNumAdminsAttribute(){
        if ($this->level_max_admins_num){
            return $this->level_max_admins_num;
        }
        return 2;
    }

}
