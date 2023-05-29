<?php

namespace App\Http\Resources\Api\V1;

use App\Helpers\Common;
use App\Http\Resources\CountryResource;
use App\Models\Agency;
use App\Models\AgencyJoinRequest;
use App\Models\Country;
use App\Models\Family;
use App\Models\FamilyUser;
use App\Models\Pack;
use App\Models\Room;
use App\Models\Ware;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {  

        $data = [
            'id'=>@$this->user->id,
            'uuid'=>@$this->user->uuid,
            'name'=>@$this->user->name?:'',
            'profile'=> [ 
                'image' => @$this->user->profile->avatar,
                'age' => Carbon::parse (@$this->user->profile->birthday)->age,
                'gender'=>$this->user->gender == 1 ? __ ('male') : __ ('female'),
            ],
            'frame'=>Common::getUserDress($this->user->id,$this->user->dress_1,4,'img2')?:Common::getUserDress($this->user->id,$this->user->dress_1,4,'img1'),
            'frame_id'=>@$this->user->dress_1,
            'vip'=> [
                'level' => @$this->user->UserVip->level,
            ],
            'level'=> [
                'receiver_img' => @$this->user->getImageReceiverOrSender('receiver_id',1)->img,
                'sender_img' => @$this->user->getImageReceiverOrSender('sender_id',2)->img, 
            ],
            'has_color_name'=>Common::hasInPack (@$this->user->id,18),
            'group_message' => $this->text,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
        ];

        return $data;
    }

}