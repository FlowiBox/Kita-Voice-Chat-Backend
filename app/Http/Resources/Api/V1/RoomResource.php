<?php

namespace App\Http\Resources\Api\V1;

use App\Helpers\Common;
use App\Models\User;
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
        $data = [
            'id'=>$this->id,
            'owner_id'=>$this->uid,
            'room_id'=>$this->numid,
            'name'=>$this->room_name,
            'visitors_count'=>$this->visitors ()->count (),
            'cover'=>$this->room_cover,
            'class'=>$this->myClass,
            'type'=>$this->myType,
            'is_hot'=>$this->hot,
            'is_popular'=>$this->is_popular,
            'room_status'=>$this->room_status,
            'room_intro'=>$this->room_intro,
            'is_recommended'=>$this->is_recommended,
            'lang'=>$this->lang,
            'country'=>$this->country
        ];
        if ($request['show']){
            $data['room_users'] = Common::get_room_users (@$this->owner ()->id,$request->user ()->id);
            $data['background'] = $this->room_background;
            $data['mics'] = explode (',',$this->microphone);
            $data['is_mics_free']=$this->free_mic;
            $data['owner'] = $this->owner ();
            $data['admins'] = $this->admins ();
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
