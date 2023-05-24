<?php

namespace App\Http\Resources\Api\V1;

use App\Helpers\Common;
use App\Http\Resources\CountryResource;
use App\Models\BoxUse;
use App\Models\Pk;
use App\Models\User;
use App\Models\Request
use App\Repositories\User\UserRepo;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        Common::setHourHot($this->uid);
        $pk = Pk::query ()->where ('room_id',$this->id)->where ('status',1)->first ();
        $owner = $this->is_afk?1:0;
        $num = $this->visitors ()->count () + $owner;
        $have_luck_box = BoxUse::query ()->where ('room_id',$this->id)->where ('not_used_num','>=',1)->exists ();
        $data = [
            'id'=>$this->id,
            'owner_id'=>$this->uid?:0,
            'room_id'=>$this->numid?:0,
            'name'=>$this->room_name?:'',
            'visitors_count'=>$num,
            'cover'=>$this->room_cover?:'',
            'class'=>$this->myClass?:new \stdClass(),
            'type'=>$this->myType?:new \stdClass(),
            'is_hot'=>$this->hot?:0,
            'is_popular'=>$this->is_popular?:0,
            'room_status'=>$this->room_status,
            'password_status'=>$this->room_pass ?true:false,
            'room_intro'=>$this->room_intro?:'',
            'max_admin' => $this->max_admin?:'',
            'is_recommended'=>$this->is_recommended?:0,
            'lang'=>$this->lang?:'',
            'is_pk'=>$pk?true:false,
            'country'=>$this->country?new CountryResource($this->country):[
                'id'=>0,
                'name'=> '',
                'flag'=>'',
                'lang'=>'',
                'phone_code'=>''
            ],
            'have_luck_box'=>$have_luck_box
        ];
        if ($request['show']){
            $requestBackground = RequestBackgroundImage::where('status',1)->where('owner_room_id',@$this->owner ()->id)->first();
            $data['room_users'] = Common::get_room_users (@$this->owner ()->id,$request->user ()->id);
            $data['background'] = ($requestBackground) ? $requestBackground->img : $this->room_background?:'';
            $data['mics'] = explode (',',$this->microphone)?:[];
            $data['is_mics_free']=$this->free_mic?:0;
            $data['owner'] = $this->owner ();
            $data['admins'] = $this->admins ();
            $data['admins_ids']=$this->admins ()->pluck('id');
//            $data['visitors'] = $this->visitors ();
            $data['speak_ban_list'] = $this->banList ();
            $data['muted_list']=$this->muteList ();
            $data['black_list']=$this->blackList ();
            $data['created_at']=$this->created_at;
        }

        return $data;

    }


    protected function owner(){
        return new UserResource(User::query ()->find ($this->uid));
    }

    protected function admins(){
        $ids = explode (',',$this->room_admin);
        $ids = $this->removeOwner ($ids);
        return UserResource::collection (User::query ()->whereIn ('id',$ids)->get ());
    }


    protected function visitors(){
        $ids = explode (',',$this->room_visitor);
        $ids = $this->removeOwner ($ids);
        return UserResource::collection (User::query ()->whereIn ('id',$ids)->get ());
    }

    protected function blackList(){
        $ids = explode (',',$this->room_black);
        return UserResource::collection (User::query ()->whereIn ('id',$ids)->get ());
    }

    protected function banList(){
        $ids = explode (',',$this->room_speak);
        return UserResource::collection (User::query ()->whereIn ('id',$ids)->get ());
    }

    protected function muteList(){
        $ids = explode (',',$this->room_sound);
        return UserResource::collection (User::query ()->whereIn ('id',$ids)->get ());
    }

    protected function removeOwner($ids){
        if (($key = array_search($this->uid, $ids)) !== false) {
            unset($ids[$key]);
        }
        return $ids;
    }
}
