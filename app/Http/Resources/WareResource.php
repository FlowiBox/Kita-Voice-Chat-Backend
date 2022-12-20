<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WareResource extends JsonResource
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
            'name'=>$this->name?:'',
            'title'=>$this->title?:'',
            'price'=>$this->price?:0,
            'color'=>$this->color?:'',
            'expire'=>$this->expire == 0 ? 99999999 : $this->expire,
            'image'=>$this->show_img?:''
        ];
    }
}
