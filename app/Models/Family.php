<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['rank'];



    public function owner(){
        return $this->belongsTo (User::class,'user_id');
    }

    public function getMembersNumAttribute(){
        $fu = FamilyUser::query ()->where ('family_id',$this->id)->where ('status',1)->where ('user_type',0)->count ();
        return $fu;
    }

    public function getMembersCountAttribute(){
        $fu = FamilyUser::query ()->where ('family_id',$this->id)->where ('status',1)->count ();
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
            'level_exp'=>@(integer)$cur_level->exp?:0,
            'level_name'=>@$cur_level->name?:'',
            'level_img'=>@$cur_level->img?:'',
            'family_exp'=>(integer)$giftLogs,
            'over_current_level_exp'=>(integer)$over,
            'next_exp'=>@(integer)$next_level->exp,
            'next_name'=>@$next_level->name,
            'next_img'=>@$next_level->img,
            'per'=> (double)($over/$diff),
            'rem'=>(integer)($diff-$over)
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

    public function getRankAttribute0(){
        $gl = GiftLog::query ()->where (function ($q){
            $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
        })->sum('giftPrice');
        return $gl;
    }

    public function getRankAttribute(){
        $time = \request ('time');
        if($time == 'today'){
//            $gl = GiftLog::query ()->whereRaw('CAST(created_at AS DATE) = CAST(NOW() AS DATE)')->where (function ($q) use ($time){
//                $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
//            })->sum('giftPrice');
            $gl = $this->today_rank;
        }elseif ($time == 'week'){
//            $gl = GiftLog::query ()->whereRaw('WEEK(CAST(created_at AS DATE)) = WEEK(CAST(NOW() AS DATE))')->where (function ($q) use ($time){
//                $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
//            })->sum('giftPrice');
            $gl = $this->week_rank;
        }elseif ($time == 'month'){
//            $gl = GiftLog::query ()->whereRaw('MONTH(CAST(created_at AS DATE)) = MONTH(CAST(NOW() AS DATE))')->where (function ($q) use ($time){
//                $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
//            })->sum('giftPrice');
            $gl = $this->month_rank;
        }else{
            $gl = GiftLog::query ()->where (function ($q) use ($time){
                $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
            })->sum('giftPrice');
        }
        return $gl;
    }


    public function getTodayRankAttribute($val){
        $gl = GiftLog::query ()->whereRaw('CAST(created_at AS DATE) = CAST(NOW() AS DATE)')->where (function ($q){
            $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
        })->sum('giftPrice');
        if ($val != $gl){
            $this->attributes['today_rank']=$gl;
            $this->save ();
        }
        return $gl;
    }

    public function getWeekRankAttribute($val){
        $gl = GiftLog::query ()->whereRaw('WEEK(CAST(created_at AS DATE)) = WEEK(CAST(NOW() AS DATE))')->where (function ($q){
            $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
        })->sum('giftPrice');
        if ($val != $gl){
            $this->attributes['week_rank']=$gl;
            $this->save ();
        }
        return $gl;
    }

    public function getMonthRankAttribute($val){
        $gl = GiftLog::query ()->whereRaw('MONTH(CAST(created_at AS DATE)) = MONTH(CAST(NOW() AS DATE))')->where (function ($q){
            $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
        })->sum('giftPrice');
        if ($val != $gl){
            $this->attributes['month_rank']=$gl;
            $this->save ();
        }
        return $gl;
    }


    public function setTodayRankAttribute(){
        $gl = GiftLog::query ()->whereRaw('CAST(created_at AS DATE) = CAST(NOW() AS DATE)')->where (function ($q){
            $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
        })->sum('giftPrice');
        $this->attributes['today_rank']=$gl;
    }

    public function setWeekRankAttribute(){
        $gl = GiftLog::query ()->whereRaw('WEEK(CAST(created_at AS DATE)) = WEEK(CAST(NOW() AS DATE))')->where (function ($q){
            $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
        })->sum('giftPrice');
        $this->attributes['week_rank']=$gl;
    }

    public function setMonthRankAttribute(){
        $gl = GiftLog::query ()->whereRaw('MONTH(CAST(created_at AS DATE)) = MONTH(CAST(NOW() AS DATE))')->where (function ($q){
            $q->where('sender_family_id',$this->id)->orWhere('receiver_family_id',$this->id);
        })->sum('giftPrice');
        $this->attributes['month_rank']=$gl;
    }


    public function admins()
    {
        return $this->hasManyThrough(User::class, FamilyUser::class, 'family_id', 'id', 'id', 'user_id');
    }




}
