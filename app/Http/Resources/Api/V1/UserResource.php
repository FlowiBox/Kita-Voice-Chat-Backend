<?php

namespace App\Http\Resources\Api\V1;

use App\Helpers\Common;
use App\Http\Resources\CountryResource;
use App\Models\Agency;
use App\Models\AgencyJoinRequest;
use App\Models\Country;
use App\Models\Family;
use App\Models\FamilyUser;
use App\Models\Pack;
use App\Models\Ware;
use Carbon\Carbon;
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

        Pack::query ()
            ->where ('expire','!=',0)
            ->where ('expire','<',Carbon::now ()->timestamp)->delete ();
        $reqs_count = AgencyJoinRequest::query ()->where ('user_id',@$this->id)->where ('status','!=',2)->count ();

        $agency_joined = null;
        if(@$this->agency_id){
            $agency_joined = Agency::query ()->find (@$this->agency_id);
            $agency_joined = new AgencyResource($agency_joined);
            $agency_joined->am_i_owner = false;
            if ($agency_joined->owner_id == @$this->id){
                $agency_joined->am_i_owner = true;
            }
        }

        $f = new \stdClass();
        $fn = '';
        $family = Family::query ()->where ('id',@$this->family_id)->first ();
        if ($family){
            $f = [
                'name'=>$family->name,
                'max_num'=>$family->num,
                'img'=>$family->image,
                'members_num'=>$family->members_num,
                'level'=>$family->level
            ];
            $fn = $family->name;
        }


        if ($request->user ()){
            $fArr = $request->user ()->friends_ids()->toArray();
        }else{
            $fArr = [];
        }

        $statics = [
            'visitor'=>0,
            'fans'=>0,
            'followers'=>0,
            'income'=>0,
            'frame'=>0,
            'enteirs'=>0,
            'bubble'=>0,
        ];


        $data = [
            'id'=>@$this->id,
            'uuid'=>@$this->uuid,
            'chat_id'=>@$this->chat_id?:"",
            'notification_id'=>@$this->notification_id?:"",
            'is_gold'=>@$this->is_gold_id,
            'name'=>@$this->name?:'',
            'email'=>@$this->email?:"",
            'phone'=>@$this->phone?:'',
            'number_of_fans'=>$this->numberOfFans(),
            'number_of_followings'=>$this->numberOfFollowings(),
            'number_of_friends'=>$this->numberOfFriends(),
            'profile_visitors'=>$this->profileVisits()->count(),
            'is_follow'=>@(bool)Common::IsFollow (@$request->user ()->id,$this->id),
            'is_friend'=>in_array ($this->id,$fArr),
            'is_in_live'=>$this->is_in_live(),
            'is_first'=>@(bool)$this->is_points_first,
            'now_room'=>[
                'is_in_room'=>@$this->now_room_uid != 0,
                'uid'=>@$this->now_room_uid,
                'is_mine'=>@$this->id == $this->now_room_uid
            ],
            'agency'=>@$agency_joined,
            'is_agency_request'=>($reqs_count >= 1)?true:false,
            'is_family_admin'=>@$this->is_family_admin,
            'is_family_member'=>@$this->family_id?true:false,
            'family_id'=>@$this->family_id,
            'is_family_owner'=>@Family::query ()->where ('user_id',$this->id)->exists (),
            'family_name'=>@$fn,
            'family_data'=>@$f,
            'profile'=>new ProfileResource(@$this->profile),
            'level'=>Common::level_center (@$this->id),
            'diamonds'=>@$this->coins?:0,
            'usd'=>@$this->old_usd+$this->target_usd-$this->target_token_usd,
            'vip'=>@Common::ovip_center ($this->id),
            'income'=>@Common::user_income ($this->id),
            'my_store'=>@$this->my_store,
            'lang'=>@$this->lang,
            'country'=>$this->country?:'',
            'frame'=>Common::getUserDress($this->id,$this->dress_1,4,'img2')?:Common::getUserDress($this->id,$this->dress_1,4,'img1'),
            'bubble'=>Common::getUserDress($this->id,$this->dress_2,5,'img2')?:Common::getUserDress($this->id,$this->dress_2,5,'img1'),
            'intro'=>Common::getUserDress($this->id,$this->dress_3,6,'img2')?:Common::getUserDress($this->id,$this->dress_3,6,'img1'),
            'mic_halo'=>Common::getUserDress($this->id,$this->dress_4,7,'img1')?:Common::getUserDress($this->id,$this->dress_4,7,'img2'),
            'frame_id'=>@$this->dress_1,
            'bubble_id'=>@$this->dress_2,
            'intro_id'=>@$this->dress_3,
            'mic_halo_id'=>@$this->dress_4,
            'can_kicked_of_room'=>Common::can_kick (@$this->id),
            'bio'=>@$this->bio?:'',
            'facebook_bind'=>@$this->facebook_id?true:false,
            'google_bind'=>@$this->google_id?true:false,
            'phone_bind'=>@$this->phone?true:false,
            'visit_time'=>'',
            'follow_time'=>$this->getFollowDate($request->get ('pid')),
            'has_room'=>$this->hasRoom(),
            'intro_num'=>$this->intros_count(),
            'frame_num'=>$this->frames_count(),
            'bubble_num'=>$this->bubble_count(),
            'statics'=>$this->statics?:$statics,

        ];


        if ($this->auth_token){
            $data['auth_token'] = $this->auth_token;
        }
        if (@$this->is_mic == '0' || @$this->is_mic == '1'){
            $data['is_mic'] = $this->is_mic;
        }
        if ($this->pivot){
            $data['visit_time']=$this->pivot->updated_at;
        }
        return $data;
    }
}
