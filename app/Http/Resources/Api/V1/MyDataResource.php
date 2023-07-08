<?php

namespace App\Http\Resources\Api\V1;

use App\Helpers\Common;
use App\Models\Agency;
use App\Models\AgencyJoinRequest;
use App\Models\Family;
use App\Models\Pack;
use App\Models\Room;
use Illuminate\Http\Resources\Json\JsonResource;

class MyDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        Pack::query ()
            ->where ('expire','!=',0)
            ->where ('expire','<',time ())->delete ();


        $agency_joined = $this->agency;
        if ($agency_joined != null) {
            $owner = $agency_joined->app_owner_id == $this->id ? new \stdClass() : new MiniUserResource($agency_joined->owner);
            if ($this->agency != null) {
                $agency_joined = [
                    'id'=>$this->agency->id,
                    'name'=>$this->agency->name,
                    'status'=>$this->agency->status,
                    'owner'=>$owner,
                ];
            } else {
                $agency_joined = null;
            }
        }
        $pass_status = false;
        $now_room = Room::query ()->where ('uid',$this->now_room_uid)->first ();
        if ($now_room){
            if($now_room->room_pass){
                $pass_status = true;
            }
        }

        $f = null;
        $family = Family::query ()->where ('id',@$this->family_id)->first ();
        if ($family){
            $f = [
                'owner_id'      => $family->user_id,
                'family_name'   =>$family->name,
                'max_num'       =>$family->num,
                'img'           =>$family->image,
                'members_num'   =>$family->members_count,
                'level'         =>$family->level
            ];
        }

        $packs = Pack::query()
            ->where ('user_id',$this->id)
            ->where (function ($q){
                $q->where('expire',0)->orWhere('expire','>=',now ()->timestamp);
            })
            ->pluck('type')
            ->toArray();
        $previliges = [];
        $previliges['no_kick']            = in_array(9, $packs);
        $previliges['intro_animation']    = in_array(11, $packs);
        $previliges['vip_gifts']          = in_array(14, $packs);
        $previliges['no_pan']             = in_array(15, $packs);
        $previliges['anonymous_man']      = in_array(17, $packs);
        $previliges['colored_name']       = in_array(18, $packs);


//        $previliges = [
//            'no_kick'=>Common::pack_get (9,$this->id),
//            'intro_animation'=>Common::pack_get (11,$this->id),
//            'vip_gifts'=>Common::pack_get (14,$this->id),
//            'no_pan'=>Common::pack_get (15,$this->id),
//            'anonymous_man'=>Common::pack_get (17,$this->id),
//            'colored_name'=>Common::pack_get (18,$this->id),
//        ];

        $data = [
            'id'=>@$this->id, // both
            'uuid'=>@$this->uuid, // both
            'chat_id'=>@$this->chat_id?:"", // both
            'notification_id'=>@$this->notification_id?:"", // both
            'is_gold'=>@$this->is_gold_id, // both
            'name'=>@$this->name?:'', // both
            'nick_name'=>@$this->nick_name, // both
            'email'=>@$this->email?:"", // both
            'phone'=>@$this->phone?:'',// both
            'number_of_fans'=>$this->numberOfFans(), // both
            'number_of_followings'=>$this->numberOfFollowings(), // both
            'number_of_friends'=>$this->numberOfFriends(), // both
            'profile_visitors'=>$this->profileVisits()->count(), // both
            'is_first'=>@(bool)$this->is_points_first, // my data
            'now_room'=>[
                'is_in_room'=>@$this->now_room_uid != 0,
                'uid'=>@(integer)$this->now_room_uid,
                'is_mine'=>@$this->id == $this->now_room_uid,
                'password_status'=>$pass_status
            ],
            'agency'=>@$agency_joined, // both
            'is_agency_request'=>(bool)AgencyJoinRequest::where('user_id',@$this->id)->where ('status','!=',2)->count (), // my
            'family_id'=>@$this->family_id, // both
            'family_data'=>$f, // refactor
            'profile'=>new ProfileResource(@$this->profile), // both
            'level'=>Common::level_center (@$this->id), // both
            'diamonds'=>@$this->monthly_diamond_received?:0, // both
            'usd'=>@$this->salary, // my
            'vip'=>@Common::ovip_center ($this->id), // both
            'my_store'=> [
                'id'=>$this->id,
                'coins'=>$this->di,
                'diamonds'=>$this->coins,
                'silver_coins'=>$this->gold,
                'usd' => (double)$this->sallary ,
            ], // my
            'lang'=>@$this->lang, // both
            'country'=>!Common::hasInPack ($this->id,13,true)?($this->country?:''):'', // both
            'frame'=>Common::getUserDress($this->id,$this->dress_1,4,'img2')?:Common::getUserDress($this->id,$this->dress_1,4,'img1'), // both
            'bubble'=>Common::getUserDress($this->id,$this->dress_2,5,'img2')?:Common::getUserDress($this->id,$this->dress_2,5,'img1'), // both
            'intro'=>Common::getUserDress($this->id,$this->dress_3,6,'img2')?:Common::getUserDress($this->id,$this->dress_3,6,'img1'), // both
            'frame_id'=>@$this->dress_1, // both
            'bubble_id'=>@$this->dress_2, // both
            'intro_id'=>@$this->dress_3, // both
            'bio'=>@$this->bio?:'', // both
            'facebook_bind'=>@$this->facebook_id?true:false, // my
            'google_bind'=>@$this->google_id?true:false, // my
            'phone_bind'=>@$this->phone?true:false, // my
            'has_room'=>$this->hasRoom(), // my
            'is_agent'=>$this->is_agent, // both
            'my_agency'=>$this->ownAgency()->select('id','name','notice','status','phone','url','img','contents')->first(), // both
            'prev'=>$previliges, // my
            'online_time'=>!Common::hasInPack ($this->id,20,true)?($this->online_time?date("Y-m-d H:i:s", $this->online_time):''):'', // both - calculated when get my data only
            'has_color_name'=>Common::hasInPack ($this->id,18), // both
            'anonymous'=>Common::hasInPack ($this->id,17,true), // both
            'country_hidden'=>Common::hasInPack ($this->id,13,true), // both
            'last_active_hidden'=>Common::hasInPack ($this->id,20,true), // both
            'visit_hidden'=>Common::hasInPack ($this->id,19,true), // both
            'room_hidden'=>Common::hasInPack ($this->id,16,true), // both
        ];
        $data['auth_token'] = $this->auth_token;
        if (@$this->is_mic == '0' || @$this->is_mic == '1'){
            $data['is_mic'] = $this->is_mic;
        }
        if ($this->pivot){
            $data['visit_time']=$this->pivot->updated_at;
        }
        return $data;
    }
}
