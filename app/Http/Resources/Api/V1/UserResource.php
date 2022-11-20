<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->auth_token){
            return [
                'name'=>$this->name,
                'email'=>$this->email,
                'phone'=>$this->phone,
                'profile'=>new ProfileResource($this->profile),
                'auth_token'=>$this->auth_token
            ];
        }
        return [
            'name'=>$this->name,
            'email'=>$this->email,
            'phone'=>$this->phone,
            'google_id'=>$this->google_id,
            'facebook_id'=>$this->facebook_id,
            'profile'=>new ProfileResource($this->profile),
        ];
    }
}
