<?php

namespace App\Http\Resources\Api\V1;

use App\Helpers\Common;
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
            'name'=>$this->name,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'number_of_fans'=>$this->numberOfFans(),
            'number_of_followings'=>$this->numberOfFollowings(),
            'number_of_friends'=>$this->numberOfFriends(),
            'profile'=>new ProfileResource($this->profile),
            'level'=>Common::level_center ($this->id),
            'vip'=>Common::vip_center ($this->id),
            'income'=>Common::user_income ($this->id),
            'my_store'=>$this->my_store
        ];

        $additional = [
            'star_level'=>Common::getLevel ($this->id,1),
            'gold_level'=>Common::getLevel ($this->id,2),
            'vip_level'=>Common::getLevel ($this->id,3),
            'hz_level'=>Common::getHzLevel ($this->id,3),
            'is_follow'=>Common::IsFollow (@$request->user ()->id,$this->id),
            'star_img'=>Common::getLevel ($this->id,1,true),
            'gold_img'=>Common::getLevel ($this->id,2,true),
            'vip_img'=>Common::getLevel ($this->id,3,true),
            'images'=>[
                $this->img_1,
                $this->img_2,
                $this->img_3,
            ],
            'room_info'=>Common::getRoomInfo ($this->id),
            'user_gifts'=>Common::getUserGifts ($this->id)
        ];


        if ($this->auth_token){
            $data['auth_token'] = $this->auth_token;
        }
        if (@$this->is_mic == '0' || @$this->is_mic == '1'){
            $data['is_mic'] = $this->is_mic;
        }
        return $data;
    }
}
