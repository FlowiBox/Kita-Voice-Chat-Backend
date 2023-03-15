<?php
namespace App\Traits;

use App\Models\Follow;
use App\Models\Room;

Trait FollowTrait{
    public function followers_ids(){
        return Follow::query ()->where ('followed_user_id',$this->id)->orderByDesc('created_at')->pluck ('user_id');
    }

    public function followeds_ids(){
        return Follow::query ()->where ('user_id',$this->id)->orderByDesc('created_at')->pluck ('followed_user_id');
    }

    public function rooms_uids(){
        return Room::query ()->where ('room_status',1)->where ('is_afk',1)->pluck ('uid');
    }


    public function followers(){
        return self::query ()->whereIn('id',$this->followers_ids ())->get ()->sortByDesc('follow_date');
    }
    public function followeds(){
        return self::query ()->whereIn('id',$this->followeds_ids ())->get ()->sortByDesc('followed_date');
    }

    public function friends(){
        return self::query ()->whereIn('id',$this->followeds_ids ())->whereIn('id',$this->followers_ids ())->get ()->sortByDesc(['follow_date','followed_date']);
    }

    public function friends_ids(){
        return self::query ()->whereIn('id',$this->followeds_ids ())->whereIn('id',$this->followers_ids ())->pluck ('id');
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

    public function onRoomFolloweds(){
        return self::query ()->whereIn('id',$this->followeds_ids ())->whereIn('id',$this->rooms_uids ())->get ();
    }

    public function my_followers(){
        return self::query ()->whereIn('id',$this->followers_ids ());
    }

}
