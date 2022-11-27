<?php


namespace App\Traits\HelperTraits;


use App\Http\Resources\Api\V1\UserResource;
use App\Models\Mic;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait RoomTrait
{

    public static function get_room_users($owner_id,$user_id){
        $room  =DB::table('rooms')->where(['uid'=>$owner_id])->select('id','microphone','room_visitor')->first();
        if(!$room)   return __('room does not exist');
//        if($owner_id == $user_id)    return __('No operation authority for homeowners');

        $mic_arr=$room->microphone ? explode(',', $room->microphone) : [];
        foreach ($mic_arr as $k => &$v) {
            if($v == 0 || $v == -1 || $v == $owner_id)   unset($mic_arr[$k]);
        }
        $vis_arr=$room->room_visitor ? explode(',', $room->room_visitor) : [];
        if($user_id && !in_array($user_id,$vis_arr))    return __('User is not in this room');
        $sea_user=array();
        $mic_user=User::query ()->whereIn('id',$mic_arr)->with ('profile')->get ();
        foreach ($mic_user as $k => &$v){
            $v->is_mic=1;
            if($user_id == $v->id)  $sea_user[]=$v;
        }
        unset($v);


        //Arranging mic or ordering personnel
        $pm_arr=$paimai=$shiyin=[];
        $paimai_data=Mic::where('roomowner_id',$owner_id)->select('type','created_at','user_id','roomowner_id')->get ();
        $i=$j=0;
        foreach ($paimai_data as $k => &$v2) {
            $v2->id = $v2->user->id;
            $v2->is_mic=0;
            $v2->name = @$v2->user->name;
            $v2->avatar = @$v2->user->profile->avatar;

            if($v2->type==1){
                $i++;
                $v2->sort=$i;
                $paimai[]=$v2;
            }elseif($v2->type==2){
                $j++;
                $v2->sort=$j;
                $shiyin[]=$v2;
            }

            $pm_arr[]=$v2->user_id;
            if($user_id == $v2->user_id) $sea_user[]=$v2;
            unset($v2->user);
            unset($v2->user_id);
            unset($v2->roomowner_id);
        }
        unset($v2);


        //

        //people in the room
        $vis_arr=array_diff($vis_arr,$mic_arr);
        $vis_arr=array_diff($vis_arr,$pm_arr);
        $room_user=User::query ()->whereIn('id',$vis_arr)->get ();
        foreach ($room_user as $k1 => &$v1){
            $v1->is_mic=0;
            if($user_id == $v1->id) $sea_user[]=$v1;
        }

        unset($v1);



        $data['mic_users']= UserResource::collection ($mic_user) ;
        $data['auction']= $paimai;
        $data['audio']=  $shiyin ;
        $data['room_users']= UserResource::collection ($room_user);
        $data['sea_users']= UserResource::collection ($sea_user);


        return $data;
    }

}
