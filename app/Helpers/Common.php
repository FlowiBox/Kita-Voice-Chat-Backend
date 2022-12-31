<?php
namespace App\Helpers;
use App\Models\Config;
use App\Models\Follow;
use App\Models\GiftLog;
use App\Models\Room;
use App\Models\Vip;
use App\Traits\HelperTraits\AdminTrait;
use App\Traits\HelperTraits\AttributesTrait;
use App\Traits\HelperTraits\CalcsTrait;
use App\Traits\HelperTraits\MoneyTrait;
use App\Traits\HelperTraits\RoomTrait;
use Illuminate\Support\Facades\DB;

class Common{

    use CalcsTrait , AdminTrait , MoneyTrait ,RoomTrait , AttributesTrait;

    public static function apiResponse(bool $success,$message,$data = null,$statusCode = null,$paginates = null){

        if ($success == false && $statusCode == null){
            $statusCode = 422;
        }

        if ($success == true && $statusCode == null){
            $statusCode = 200;
        }

        return response ()->json (
            [
                'success'   => $success,

                'message'   => __ ($message),

                'data'      => $data == [] || $data == null ? null : $data,

                'extra_data'=> [
                    'storage_base_url'=>self::getConf ('storage_base_url') ?:asset ('storage')
                ],

                'paginates' =>$paginates
            ],
            $statusCode
        );
    }

    public static function getConf($key){
        if ($conf = Config::query ()->where('name',$key)->first ()){
            return $conf->value;
        }
        return null;
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

    // هل اتابعه

    public static function IsFollow($user_id = null,$followed_user_id = null){
        if(!$user_id || !$followed_user_id) return 0;
        if($user_id == $followed_user_id)   return 1;
        $id=Follow::query ()->where(['user_id'=>$user_id,'followed_user_id'=>$followed_user_id,'status'=>1])->value('id');
        return $id ? 1 : 0;
    }



    public static function getConfig($name = null){
        if (!$name) {
            return '';
        }
        $val = DB::table('configs')->where('name', $name)->value('value');
        return $val;
    }



    public static function gmOrderDataFormat($data, $type = 1)
    {
        if (!$data) {
            return [];
        }
        $f_yj_ratio = self::getConfig('f_yj_ratio');
        foreach ($data as $k => &$v) {
//            $skill = $redisMod->getRedisData('skill', 'getSkillDetails', 18000, $v['skill_id']);
//            $v['skill_img'] = isset($skill['image']) ? $skill['image'] :$this->auth->setFilePath($this->getConfig('logo'));
//            $v['skill_name'] = isset($skill['name']) ? $skill['name'] :'暂无';
            if (in_array($type, [1, 3])) {
                $v->user_name = self::getUserField($v->master_id, 'nickname');
                $v->avatar = self::getUserField($v->master_id, 'avatar');
            } elseif ($type == 2) {
                $v->user_name = self::getUserField($v->user_id, 'nickname');
                $v->avatar = self::getUserField($v->user_id, 'avatar');
            }
            if ($v->status == 1) {
                $sysj = $v->addtime + 1200 - time();
                $v->sysj = $sysj > 0 ? $sysj : 0;
            }
            if ($type == 3) {
                $v->real_price = round($v->num * $v->price * $f_yj_ratio, 2);
            }
            $v->type = $type;
            $v->status_text = self::getGmOrdersText($v->status, $type);
            $v->start_time = $v->start_time ? date('Y.m.d H:i', $v->start_time) : '';
            $v->refusetime = $v->refusetime ? date('Y.m.d H:i', $v->refusetime) : '';
            $v->finishtime = $v->finishtime ? date('Y.m.d H:i', $v->finishtime) : '';
            $v->paytime = $v->paytime ? date('Y.m.d H:i', $v->paytime) : '';
            $v->addtime = $v->addtime ? date('Y.m.d H:i', $v->addtime) : '';
        }
        return $data;
    }


    //تصنيف حالة ترتيب اللعبة
//type 1 users 2 master
    public static function getGmOrdersText($val = null,$type = 1){
        $user=[
            1 => 'to be paid',
            2 => 'Pending orders',
            3 => 'to be served',
            31 => 'The other side applies for immediate service',
            4 => 'in progress',
            5 => 'Completed',
            6 => 'Cancelled',
            7 => 'Rejected',

            81 => 'refund application',
            82 => 'Refund successful',
            83 => 'Refund failed',
            84 => 'Appealing',
        ];

        $master=[
            1 => 'to be paid',
            2 => 'Pending orders',
            3 => 'to be served',
            31 => 'Applied for immediate service',
            4 => 'in progress',
            5 => 'Completed',
            6 => 'The other party has canceled',
            7 => 'Rejected',

            81 => 'refund application',
            82 => 'Agree to refund',
            83 => 'Refused to refund',
            84 => 'The other party is appealing',
        ];
        if($type == 1){
            return $val ? $user[$val] : $user;
        }elseif(in_array($type, [2,3])){
            return $val ? $master[$val] : $master;
        }else{
            return '';
        }
    }



}
