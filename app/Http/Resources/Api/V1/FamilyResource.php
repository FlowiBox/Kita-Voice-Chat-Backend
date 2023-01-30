<?php

namespace App\Http\Resources\Api\V1;

use App\Models\FamilyUser;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        if (!$this->resource){
//            return null;
//        }
        $owner = new UserResource(User::find(@$this->user_id?:0));
        if (!@$this->user_id){
            $owner = new \stdClass();
        }
        $mems = FamilyUser::query ()->where ('family_id',@$this->id)->where ('status',1)->pluck ('user_id');
        return [
            'id'=>@$this->id,
            'name'=>@$this->name?:'',
            'introduce'=>@$this->introduce?:'',
            'image'=>@$this->image?:'',
            'notice'=>@$this->notice?:'',
            'max_num_of_members'=>@$this->num?:'',
            'rank'=>@$this->rank?:0,
            'owner'=>$owner,
            'am_i_member'=>FamilyUser::query ()->where ('user_id',$request->user ()->id)->where ('family_id',$this->id)->where ('status',1)->exists (),
            'am_i_owner'=>(@$this->user_id == $request->user ()->id) ?true:false,
            'am_i_admin'=>$request->user ()->is_family_admin ?true:false,
            'members'=>UserResource::collection (User::query ()->whereIn ('id',$mems)->where ('id','!=',$this->user_id)->get ()),
            'num_of_requests'=>FamilyUser::query ()->where ('family_id',$this->id)->where ('status',0)->count ()
        ];
    }
}
