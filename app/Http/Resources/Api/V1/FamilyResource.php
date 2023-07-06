<?php

namespace App\Http\Resources\Api\V1;

use App\Models\FamilyUser;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        if (!$this->resource){
//            return null;
//        }
        $user = User::find($this->user_id);
        if ($user){
            $owner = new UserResource($user);
        }else{
            $owner = new \stdClass();
        }

//        $me = new UserResource($request->user ());

//        $mems = FamilyUser::query ()->where ('family_id',@$this->id)->where ('status',1)->pluck ('user_id');

        [$admins, $members] = $this->getFamilyUsers();
        return [
            'id'=>@$this->id,
            'name'=>@$this->name?:'',
            'introduce'=>@$this->introduce?:'',
            'image'=>@$this->image?:'',
            'notice'=>@$this->notice?:'',
            'max_num_of_members'=>@$this->num?:0,
            'max_num_of_admins'=>@$this->num_admins?:0,
            'rank'=>@(integer)$this->rank?:0,
            'owner'=>$owner,
//            'me'=>$me,
            'am_i_member'=>FamilyUser::query ()->where ('user_id',$request->user ()->id)->where ('family_id',$this->id)->where ('status',1)->exists (),
            'am_i_owner'=>(@$this->user_id == $request->user ()->id) ?true:false,
            'am_i_admin'=>$request->user ()->is_family_admin ?true:false,
//            'members'=> ShortFamilyUserResource::collection (User::query ()->whereIn ('id',$mems)->where ('id','!=',$this->user_id)->get ()),
            'members'=> $members,
            'num_of_requests'=>FamilyUser::query ()->where ('family_id',$this->id)->where ('status',0)->count (),
            'num_of_members'=>$this->members_count,
            'level'=>@$this->level?:'',
            'today_rank'=>$this->today_rank,
            'week_rank'=>$this->week_rank,
            'month_rank'=>$this->month_rank,
//            'admins' => @$this->admins ? MiniUserResource::collection($this->admins) :[],
            'admins' => $admins
        ];
    }

    public function getFamilyUsers()
    {
        $familyUsers = $this->users;
        $admins = $familyUsers->where('user_type', 2)->pluck('user_id')->toArray();
        $members = $familyUsers->where('user_type','!=' ,2)->pluck('user_id')->toArray();

        $admins = array_unique($admins);
        $members = array_unique($members);

        $allUsers = array_merge($admins, $members);

        $allUsers = User::query()->withoutAppends()->whereIn('id', $allUsers)->with('profile')->select(['id', 'name'])->get();

        $admins = collect($admins);
        $members = collect($members);

        $admins = $admins->map(function ($id) use($allUsers){
            return $allUsers->where('id', $id);
        });

        $members = $members->map(function ($id) use($allUsers){
            return $allUsers->where('id', $id);
        });
        return [$admins, $members];

    }
}
