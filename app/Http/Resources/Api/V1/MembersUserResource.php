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

class MembersUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       if(!@$this->id){
           return ;
       }

        $data = [
            'id'=>@$this->id,
            'name'=>@$this->name?:'',
            'profile'=>new ProfileResource(@$this->profile),
            'level'=>Common::level_center (@$this->id),
            'frame_id'=>@$this->dress_1,
            'frame'=>Common::getUserDress($this->id,$this->dress_1,4,'img2')?:Common::getUserDress($this->id,$this->dress_1,4,'img1'),
        ];

        return $data;
    }

    public function handelStatics($request){
        $user = $request->user();
        $frame = 0;
        

        return [
            'frame' => $frame,
        ];
    }
}
