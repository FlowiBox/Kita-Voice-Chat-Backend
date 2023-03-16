<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
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
            'name'=>$this->name,
            'notice'=>$this->notice,
            'status'=>$this->status,
            'phone'=>$this->phone,
            'url'=>$this->url,
            'img'=>$this->img,
            'contents'=>$this->contents,
            'owner'=>new UserResource($this->owner),
        ];
    }
}
