<?php

namespace App\Http\Resources\Api\V1;

use App\Models\FamilyUser;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyRankResource extends JsonResource
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

        return [
            'id'=>@$this->id,
            'name'=>@$this->name?:'',
            'introduce'=>@$this->introduce?:'',
            'image'=>@$this->image?:'',
            //'notice'=>@$this->notice?:'',
            //'max_num_of_members'=>@$this->num?:0,
            //'max_num_of_admins'=>@$this->num_admins?:0,
            'rank'=>@(integer)$this->rank?:0,
            //'am_i_member'=>FamilyUser::query ()->where ('user_id',$request->user ()->id)->where ('family_id',$this->id)->where ('status',1)->exists (),
            //'am_i_owner'=>(@$this->user_id == $request->user ()->id) ?true:false,
            //'am_i_admin'=>$request->user ()->is_family_admin ?true:false,
            //'num_of_requests'=>FamilyUser::query ()->where ('family_id',$this->id)->where ('status',0)->count (),
            //'num_of_members'=>$this->members_count,
            //'today_rank'=>$this->today_rank,
            //'week_rank'=>$this->week_rank,
            //'month_rank'=>$this->month_rank,
        ];
    }
}
