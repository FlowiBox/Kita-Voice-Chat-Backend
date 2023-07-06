<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\User\FamilyUserResource as FamilyUserResourceAlias;
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
//        $owner = User::where('id', $this->user_id)->with(['profile:id,user_id,avatar as image'])->select(['id', 'name'])->first();

//        $me = new UserResource($request->user ());

//        $mems = FamilyUser::query ()->where ('family_id',@$this->id)->where ('status',1)->pluck ('user_id');

        [$owner, $admins, $members] = $this->getFamilyUsers();
        return [
            'id'=>@$this->id,
            'name'=>@$this->name?:'',
            'introduce'=>@$this->introduce?:'',
            'image'=>@$this->image?:'',
            'notice'=>@$this->notice?:'',
            'max_num_of_members'=>@$this->num?:0,
            'max_num_of_admins'=>@$this->num_admins?:0,
            'rank'=>@(integer)$this->rank?:0,
            'owner'=>new FamilyUserResourceAlias($owner),
            //            'me'=>$me,
            'am_i_member'=>FamilyUser::query ()->where ('user_id',$request->user ()->id)->where ('family_id',$this->id)->where ('status',1)->exists (),
            'am_i_owner'=>(@$this->user_id == $request->user ()->id) ?true:false,
            'am_i_admin'=>$request->user ()->is_family_admin ?true:false,
            //            'members'=> ShortFamilyUserResource::collection (User::query ()->whereIn ('id',$mems)->where ('id','!=',$this->user_id)->get ()),
            'members'=> FamilyUserResourceAlias::collection($members),
            'num_of_requests'=>FamilyUser::query ()->where ('family_id',$this->id)->where ('status',0)->count (),
            'num_of_members'=>$this->members_count,
            'level'=>@$this->level?:'',
            'today_rank'=>$this->today_rank,
            'week_rank'=>$this->week_rank,
            'month_rank'=>$this->month_rank,
            //            'admins' => @$this->admins ? MiniUserResource::collection($this->admins) :[],
            'admins' => FamilyUserResourceAlias::collection($admins)
        ];
    }

    public function getFamilyUsers()
    {
        $familyUsers = $this->users;

        $owner = collect([$this->user_id]);
        $admins = $familyUsers->where('user_type', 1)->pluck('user_id')->unique();
        $members = $familyUsers->where('user_type', 0)->pluck('user_id')->unique();

        $allUsers = $admins->concat($members)->concat($owner);

        $allUsers = User::query()->whereIn('id', $allUsers)
                        ->withoutAppends()
                        ->select(['id', 'name'])
                        ->with(['profile:id,user_id,avatar as image'])
                        ->get();

        $admins = $allUsers->whereIn('id', $admins)->values();
        $members = $allUsers->whereIn('id', $members)->values();
        $owner = $allUsers->whereIn('id', $owner)->first();

        return [$owner, $admins, $members];
    }
}
