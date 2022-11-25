<?php
namespace App\Helpers;
use App\Models\Follow;
use App\Models\GiftLog;
use App\Models\Room;
use App\Models\Vip;
use Illuminate\Support\Facades\DB;

class Common{
    public static function apiResponse(bool $success,$message,$data,$statusCode = 200,$paginates = null){
        return response ()->json (
            [
                'success'   => $success,

                'message'   => __ ($message),

                'data'      => $data == [] || $data == null ? null : $data,

                'paginates' =>$paginates
            ],
            $statusCode
        );
    }

    public static function upload($folder,$file){
        $file->store('/',$folder);
        $fileName = $file->hashName();
        return $folder.DIRECTORY_SEPARATOR.$fileName;
    }

    public static function  getPaginates($collection)
    {
        return [
            'per_page' => $collection->perPage(),
            'path' => $collection->path(),
            'total' => $collection->total(),
            'current_page' => $collection->currentPage(),
            'next_page_url' => $collection->nextPageUrl(),
            'previous_page_url' => $collection->previousPageUrl(),
            'last_page' => $collection->lastPage(),
            'has_more_pages' => $collection->hasMorePages(),
        ];
    }


    public static function paginate($req,$data){
        if ($req->pp){
            return static::getPaginates ($data);
        }
        return null;
    }


    public static function getLevel($user_id = null,$type = null,$is_image = false){
        $star_num = GiftLog::where('receiver_id',$user_id)->sum('giftPrice');
        $gold_num = GiftLog::where('sender_id',$user_id)->sum('giftPrice');

        if($type == 1){
            $total = $star_num;
        }elseif ($type == 2 || $type == 3){
            $total = $gold_num;
        }else{
            $total = 0;
        }

        if (!$total){
            return 0;
        }

        $level = Vip::query ()->where(['type' => $type])->where('di', '<=', $total)->orderByDesc('di')->limit(1)->value('level');

        if ($is_image){
            if ($level > 0) {
                $img = Vip::query ()->where(['level' => $level, 'type' => $type])->value('img');
                return $img;
            } else {
                if($level == '0'){
                    $img = Vip::query ()->where(['level' => $level, 'type' => $type])->value('img');
                    return $img;
                }else{
                    return '';
                }
            }
        }else{
            return $level;
        }

    }

    public static function getHzLevel($user_id, $is_img = false)
    {
        $vip_level = static::getLevel($user_id, 3);
        if(is_numeric($vip_level))
        {
            $level = ceil($vip_level / 2);
        }else{
            $level = $vip_level;
        }

        if ($is_img) {
            $img = Vip::query ()->where(['level' => $level, 'type' => 4])->value('img');
            return $img;
        } else {
            return $level;
        }
    }


    // هل اتابعه
    public static function IsFollow($user_id = null,$followed_user_id = null){
        if(!$user_id || !$followed_user_id) return 0;
        if($user_id == $followed_user_id)   return 1;
        $id=Follow::query ()->where(['user_id'=>$user_id,'followed_user_id'=>$followed_user_id,'status'=>1])->value('id');
        return $id ? 1 : 0;
    }


    protected static function userNowRooms($user_id = null)
    {
        if (!$user_id) {
            return false;
        }
        $is_afk = Room::query ()->where('uid', $user_id)->value('is_afk');
        if ($is_afk) {
            return $user_id;
        }
        $uid = Room::query ()->where('uid',$user_id)->value('uid');
        return $uid ?: 0;
    }

    public static function getRoomInfo($user_id = null){
        $room_id = self::userNowRooms ($user_id);
        if ($room_id) {
            $roomInfo = Room::query ()->select(['uid', 'room_name', 'hot','room_cover'])->where('uid', $room_id)->first ();
            $roomInfo['hot'] = self::room_hot($roomInfo['hot']);
            $roomInfo['room_name'] = urldecode($roomInfo['room_name']);
        } else {
            $roomInfo =(object)[];
        }
        return $roomInfo;
    }


    public static function room_hot($hot = null){
        $hot=(int)$hot;
        if(!$hot)   return 0;
        if($hot <= 9999){
            return $hot;
        }elseif($hot > 9999 && $hot <= 99999999){
            $hot = round($hot/10000 , 1);
            return $hot.'w';
        }elseif($hot > 99999999 ){
            $hot = round($hot/100000000 , 2);
            return $hot.'m';
        }
    }

    public static function getUserGifts ($user_id){

        $gifts = GiftLog::query ()->where ('receiver_id',$user_id)
            ->where ('type','2')
            ->with ('gifts',function ($q){
                $q->select('show_img,price');
            })
            ->groupBy ('giftId')
            ->orderBy ('gifts.price');

        return $gifts;
    }

}
