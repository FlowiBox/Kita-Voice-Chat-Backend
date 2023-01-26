<?php

namespace App\Http\Resources\Api\V1;

use App\Helpers\Common;
use App\Models\Agency;
use App\Models\AgencyJoinRequest;
use App\Models\FamilyUser;
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

        $reqs_count = AgencyJoinRequest::query ()->where ('user_id',$this->id)->where ('status','!=',2)->count ();

        $agency_joined = null;
        if($this->agency_id){
            $agency_joined = Agency::query ()->find ($this->agency_id);
            $agency_joined = new AgencyResource($agency_joined);
            $agency_joined->am_i_owner = false;
            if ($agency_joined->owner_id == $this->id){
                $agency_joined->am_i_owner = true;
            }
        }

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
            'agency'=>$agency_joined,
            'is_agency_request'=>($reqs_count >= 1)?true:false,
            'is_family_admin'=>$this->is_family_admin,
            'profile'=>new ProfileResource($this->profile),
            'level'=>Common::level_center ($this->id),
            'diamonds'=>$this->coins?:0,
            'vip'=>Common::vip_center ($this->id),
            'income'=>Common::user_income ($this->id),
            'my_store'=>$this->my_store,
            'lang'=>$this->lang,
            'country'=>$this->country?:'',
            'frame'=>Common::getUserDress($this->id,$this->dress_1,4,'img2')?:Common::getUserDress($this->id,$this->dress_1,4,'img1'),
            'bubble'=>Common::getUserDress($this->id,$this->dress_2,5,'img2')?:Common::getUserDress($this->id,$this->dress_2,5,'img1'),
            'intro'=>Common::getUserDress($this->id,$this->dress_3,6,'img2')?:Common::getUserDress($this->id,$this->dress_3,6,'img1'),
            'mic_halo'=>Common::getUserDress($this->id,$this->dress_4,7,'img1')?:Common::getUserDress($this->id,$this->dress_4,7,'img2'),
            'frame_id'=>$this->dress_1,
            'bubble_id'=>$this->dress_2,
            'intro_id'=>$this->dress_3,
            'mic_halo_id'=>$this->dress_4,
            'can_kicked_of_room'=>Common::can_kick ($this->id),
            'bio'=>$this->bio?:'',
            'facebook_bind'=>$this->facebook_id?true:false,
            'google_bind'=>$this->google_id?true:false,
            'phone_bind'=>$this->phone?true:false,
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
