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

    public function getLevelAttribute(){
        $giftLogs = GiftLog::query ()->where ('receiver_family_id',$this->id)->sum ('giftPrice');
        $level = FamilyLevel::query ()->where ('exp','<=',$giftLogs)->orderByDesc ('exp')->first ();
        if ($level){
            return $level->name;
        }
        return '';
    }
}
