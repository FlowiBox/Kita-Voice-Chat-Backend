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
        $rem_time = now()->diffInSeconds($this->created_at->addSeconds(30));
        if ($rem_time > 30){
            $rem_time = 0;
        }
        return [
            'id'=>$this->id,
            'user'=> [
                'id'        => $this->user->id,
                'uuid'      => $this->user->uuid,
                'image'     => $this->user->profile->avatar,
                'name'      => $this->user->name
            ],
            'coins'=>$this->coins,
//            'end_at'=>$this->end_at,
//            'room_uid'=>$this->room_uid,
//            'room_id'=>$this->room_id,
//            'users_num'=>$this->users_num,
//            'not_used_num'=>$this->not_used_num,
            'type'=>$this->type == 1?'super':'normal',
//            'label'=>$this->label,
//            'image'=>$this->image,
            'rem_time'=>$this->type == 1 ? $rem_time : 0
        ];
    }
}
