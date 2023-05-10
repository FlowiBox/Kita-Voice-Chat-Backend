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
use App\Models\Room;
use App\Models\Ware;
use Carbon\Carbon;
use http\Client\Curl\User;
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
       if(!@$this->id){
           return ;
       }

        $wapel = Pack::query ()
            ->where ('type',12)
            ->where ('expire','>=',time ())
            ->where ('user_id',$this->id)
            ->where ('use_num','>',0)
            ->first ();

        Pack::query ()
            ->where ('expire','!=',0)
            ->where ('expire','<',time ())->delete ();
        $reqs_count = AgencyJoinRequest::query ()->where ('user_id',@$this->id)->where ('status','!=',2)->count ();

        $agency_joined = null;
        if(@$this->agency_id){
            $agency_joined = Agency::query ()->find (@$this->agency_id);
            if ($agency_joined){
                $agency_joined = new AgencyResource($agency_joined);
                $agency_joined->am_i_owner = false;
                if ($agency_joined->owner_id == @$this->id){
                    $agency_joined->am_i_owner = true;
                }
            }else{
                AgencyJoinRequest::query ()->where ('agency_id',$this->agency_id)->delete();
                $this->agency_id = 0;
                $this->save();
            }
        }
        $pass_status = false;
        $now_room = Room::query ()->where ('uid',$this->now_room_uid)->first ();
        if ($now_room){
            if($now_room->room_pass){
                $pass_status = true;
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
                'members_num'=>$family->members_count,
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

        $previliges = [
            'no_kick'=>Common::pack_get (9,$this->id),
            'intro_animation'=>Common::pack_get (11,$this->id),
            'wapel'=>Common::pack_get (12,$this->id),
            'vip_gifts'=>Common::pack_get (14,$this->id),
            'no_pan'=>Common::pack_get (15,$this->id),
            'anonymous_man'=>Common::pack_get (17,$this->id),
            'colored_name'=>Common::pack_get (18,$this->id),
        ];




        $data = [
            'id'=>@$this->id,
            'uuid'=>@$this->uuid,
            'chat_id'=>@$this->chat_id?:"",
            'notification_id'=>@$this->notification_id?:"",
            'is_gold'=>@$this->is_gold_id,
            'name'=>@$this->name?:'',
            'nick_name'=>@$this->nick_name,
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
                'is_mine'=>@$this->id == $this->now_room_uid,
                'password_status'=>$pass_status
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
            'country'=>!Common::hasInPack ($this->id,13,true)?($this->country?:''):'',
            'frame'=>Common::getUserDress($this->id,$this->dress_1,4,'img2')?:Common::getUserDress($this->id,$this->dress_1,4,'img1'),
            'bubble'=>Common::getUserDress($this->id,$this->dress_2,5,'img2')?:Common::getUserDress($this->id,$this->dress_2,5,'img1'),
            'intro'=>Common::getUserDress($this->id,$this->dress_3,6,'img2')?:Common::getUserDress($this->id,$this->dress_3,6,'img1'),
            'mic_halo'=>Common::getUserDress($this->id,$this->dress_4,7,'img1')?:Common::getUserDress($this->id,$this->dress_4,7,'img2'),
            'frame_id'=>@$this->dress_1,
            'bubble_id'=>@$this->dress_2,
            'intro_id'=>@$this->dress_3,
            'mic_halo_id'=>@$this->dress_4,
            'can_kicked_of_room'=>!Common::hasInPack ($this->id,9),
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
            'statics'=>$this->handelStatics ($request)?:$statics,
            'is_agent'=>$this->is_agent,
//            'my_agency'=>$this->ownAgency()->select('id','name','notice','status','phone','url','img','contents')->first(),
            'prev'=>$previliges,
            'online_time'=>!Common::hasInPack ($this->id,20,true)?($this->online_time?date("Y-m-d H:i:s", $this->online_time):''):'',
            'has_color_name'=>Common::hasInPack ($this->id,18),
            'anonymous'=>Common::hasInPack ($this->id,17,true),
            'country_hidden'=>Common::hasInPack ($this->id,13,true),
            'last_active_hidden'=>Common::hasInPack ($this->id,20,true),
            'visit_hidden'=>Common::hasInPack ($this->id,19,true),
            'room_hidden'=>Common::hasInPack ($this->id,16,true),
            'wapel_num'=>@(integer)$wapel->use_num?:0
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

    public function handelStatics($request){
        $user = $request->user();
        $visitor = 0;
        $fans = 0;
        $friends = 0;
        $income = 0;
        $frame = 0;
        $enteirs = 0;
        $bubble = 0;
        if ($request->visitor != null){
            $visitor = (integer)$user->profileVisits()->count() - (integer)$request->visitor;
        }
        if ($request->fans != null){
            $fans = (integer)$user->numberOfFans() - (integer)$request->fans;
        }
        if ($request->friends != null){
            $friends = (integer)$user->numberOfFriends() - (integer)$request->friends;
        }
        if ($request->income != null){
            $income = (integer)$user->coins - (integer)$request->income;
        }
        if ($request->frame != null){
            $frame = (integer)$user->frames_count() - (integer)$request->frame;
        }
        if ($request->enteirs != null){
            $enteirs = (integer)$user->intros_count() - (integer)$request->enteirs;
        }
        if ($request->bubble != null){
            $bubble = (integer)$user->bubble_count() - (integer)$request->bubble;
        }

        return [
            'visitor' => $visitor,
            'fans' => $fans,
            'friends' => $friends,
            'income' => $income,
            'frame' => $frame,
            'enteirs' => $enteirs,
            'bubble' => $bubble
        ];
    }
}
