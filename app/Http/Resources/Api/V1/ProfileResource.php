<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'image'=>$this->avatar?:'',
            'gender'=>$this->gender == 1 ? __ ('male') : __ ('female'),
            'birthday'=>$this->birthday?Carbon::parse($this->birthday)->format ('Y-m-d'):'',
            'age'=>Carbon::parse ($this->birthday)->age,
            'province'=>$this->province?:'',
            'city'=>$this->city?:'',
            'country'=>$this->country?:''
        ];
    }
}
