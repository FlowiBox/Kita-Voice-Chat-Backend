<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class BoxUseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'user'=>new UserResource($this->user),
            'coins'=>$this->coins,
            'end_at'=>$this->end_at,
            'room_uid'=>$this->room_uid,
            'room_id'=>$this->room_id,
            'users_num'=>$this->users_num,
            'not_used_num'=>$this->not_used_num,
            'type'=>$this->type == 1?'super':'normal',
            'label'=>$this->label,
            'image'=>$this->image,
        ];
    }
}
