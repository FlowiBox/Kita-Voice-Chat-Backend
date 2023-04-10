<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
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
            'title'=>$this->title,
            'description'=>$this->description,
            'url'=>$this->url,
            'shares_num'=>$this->shares_num?:0,
            'comments_num'=>$this->comments_num?:0,
            'likes_num'=>$this->likes_num?:0,
            'views_num'=>$this->views_num?:0,
            'author'=>new UserResource($this->author),
        ];
    }
}
