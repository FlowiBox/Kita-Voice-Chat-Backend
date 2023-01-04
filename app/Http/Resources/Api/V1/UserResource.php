<?php

namespace App\Http\Resources\Api\V1;

use App\Helpers\Common;
use App\Models\Ware;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $data = [
            'id'=>$this->id,
            'name'=>$this->name?:'',
            'email'=>$this->email?:"",
            'phone'=>$this->phone?:'',
            'number_of_fans'=>$this->numberOfFans(),
            'number_of_followings'=>$this->numberOfFollowings(),
            'number_of_friends'=>$this->numberOfFriends(),
            'profile_visitors'=>$this->profileVisits()->count(),
            'is_follow'=>(bool)Common::IsFollow (@$request->user ()->id,$this->id),
            'is_in_live'=>$this->is_in_live(),
            'now_room'=>[
                'is_in_room'=>$this->now_room_uid != 0,
                'uid'=>$this->now_room_uid,
                'is_mine'=>$this->id == $this->now_room_uid
            ],
            'profile'=>new ProfileResource($this->profile),
            'level'=>Common::level_center ($this->id),
            'diamonds'=>$this->di?:0,
            'vip'=>Common::vip_center ($this->id),
            'income'=>Common::user_income ($this->id),
            'my_store'=>$this->my_store,
            'lang'=>$this->lang,
            'country'=>$this->country?:'',
            'frame'=>Common::getUserDress($this->id,$this->dress_1,4,'img1')?:Common::getUserDress($this->id,$this->dress_1,4,'img2'),
            'bubble'=>Common::getUserDress($this->id,$this->dress_2,5,'img1')?:Common::getUserDress($this->id,$this->dress_1,4,'img2'),
            'intro'=>Common::getUserDress($this->id,$this->dress_3,6,'img2')?:Common::getUserDress($this->id,$this->dress_1,4,'img1'),
            'mic_halo'=>Common::getUserDress($this->id,$this->dress_4,7,'img1')?:Common::getUserDress($this->id,$this->dress_1,4,'img2'),
            'can_kicked_of_room'=>Common::can_kick ($this->id),
            'bio'=>$this->bio?:''
        ];

//        $additional = [
//            'star_level'=>Common::getLevel ($this->id,1),
//            'gold_level'=>Common::getLevel ($this->id,2),
//            'vip_level'=>Common::getLevel ($this->id,3),
//            'hz_level'=>Common::getHzLevel ($this->id,3),
//            'is_follow'=>Common::IsFollow (@$request->user ()->id,$this->id),
//            'star_img'=>Common::getLevel ($this->id,1,true),
//            'gold_img'=>Common::getLevel ($this->id,2,true),
//            'vip_img'=>Common::getLevel ($this->id,3,true),
//            'images'=>[
//                $this->img_1,
//                $this->img_2,
//                $this->img_3,
//            ],
//            'room_info'=>Common::getRoomInfo ($this->id),
//            'user_gifts'=>Common::getUserGifts ($this->id)
//        ];


        if ($this->auth_token){
            $data['auth_token'] = $this->auth_token;
        }
        if (@$this->is_mic == '0' || @$this->is_mic == '1'){
            $data['is_mic'] = $this->is_mic;
        }
        return $data;
    }
}
