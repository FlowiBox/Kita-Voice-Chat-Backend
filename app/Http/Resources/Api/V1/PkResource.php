<?php

namespace App\Http\Resources\Api\V1;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $t1 = User::query ()->whereIn ('id',explode (',',$this->team_1))->get ();
        $t2 = User::query ()->whereIn ('id',explode (',',$this->team_2))->get ();
        return [
            'id'=>$this->id,
            'room_id'=>$this->room_id,
            'start_at'=>$this->start_at,
            'end_at'=>$this->end_at,
            'team1'=>UserResource::collection ($t1),
            'team2'=>UserResource::collection ($t2),
            'team1_score'=>$this->t1_score,
            'team2_score'=>$this->t2_score,
        ];
    }
}
