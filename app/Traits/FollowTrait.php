<?php
namespace App\Traits;

use App\Models\Follow;

Trait FollowTrait{
    public function followers_ids(){
        return Follow::query ()->where ('followed_user_id',$this->id)->pluck ('user_id');
    }

    public function followeds_ids(){
        return Follow::query ()->where ('user_id',$this->id)->pluck ('followed_user_id');
    }

    public function followers(){
        return self::query ()->whereIn('id',$this->followers_ids ())->get ();
    }
    public function followeds(){
        return self::query ()->whereIn('id',$this->followeds_ids ())->get ();
    }

    public function friends(){
        return self::query ()->whereIn('id',$this->followeds_ids ())->whereIn('id',$this->followers_ids ())->get ();
    }

    public function numberOfFans(){
        return self::query ()->whereIn('id',$this->followers_ids ())->count();
    }

    public function numberOfFollowings(){
        return self::query ()->whereIn('id',$this->followeds_ids ())->count ();
    }

    public function numberOfFriends(){
        return self::query ()->whereIn('id',$this->followeds_ids ())->whereIn('id',$this->followers_ids ())->count ();
    }
}
