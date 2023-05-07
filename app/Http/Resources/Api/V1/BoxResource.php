<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class BoxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $is_label = false;
        if ($this->type == 1 && $this->has_label == 1){
            $is_label = true;
        }
        return [
            'id'=>$this->id,
            'type'=>$this->type == 1 ? 'super':'normal',
            'coins'=>$this->coins,
            'users_num'=>$this->users,
            'image'=>$this->image,
            'is_label'=>$is_label
        ];
    }
}
