<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\GiftResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftLogResource extends JsonResource
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
            'num'=>$this->t,
            'gift'=>new GiftResource($this->gift),
        ];
    }
}
