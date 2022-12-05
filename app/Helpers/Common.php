<?php
namespace App\Helpers;
use App\Models\Config;
use App\Models\Follow;
use App\Models\GiftLog;
use App\Models\Room;
use App\Models\Vip;
use App\Traits\HelperTraits\AdminTrait;
use App\Traits\HelperTraits\CalcsTrait;
use App\Traits\HelperTraits\MoneyTrait;
use App\Traits\HelperTraits\RoomTrait;
use Illuminate\Support\Facades\DB;

class Common{

    use CalcsTrait , AdminTrait , MoneyTrait ,RoomTrait;

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






}
