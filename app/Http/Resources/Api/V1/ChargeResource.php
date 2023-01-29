<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->charger_type == 'app'){
            $sender = User::find($this->charger_id);
            $s_type = 'user';
        }else{
            $sender = Admin::find($this->charger_id);
            $s_type = 'management';
        }
        if ($this->user_type == 'app'){
            $receiver =  User::find($this->user_id);
            $r_type = 'user';
        }else{
            $receiver =  Admin::find($this->user_id);
            $r_type = 'management';
        }
        $sender_data = [
            'id'=>$sender->id,
            'name'=>$sender->name?:"",
            'img'=>$sender->img?:"",
            'type'=>$s_type
        ];
        $receiver_data = [
            'id'=>$receiver->id,
            'name'=>$receiver->name?:"",
            'img'=>$receiver->img?:"",
            'type'=>$r_type
        ];
        return [
            'id'=>$this->id,
            'sender'=>$sender_data,
            'receiver'=>$receiver_data,
            'value'=>$this->amount,
            'time'=>$this->created_at
        ];
    }
}
