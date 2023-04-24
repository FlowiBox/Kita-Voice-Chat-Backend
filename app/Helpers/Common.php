<?php
namespace App\Helpers;
use App\Http\Resources\CountryResource;
use App\Models\Config;
use App\Models\Country;
use App\Models\Follow;
use App\Models\GiftLog;
use App\Models\OfficialMessage;
use App\Models\Pack;
use App\Models\PackLog;
use App\Models\Room;
use App\Models\User;
use App\Models\UserVip;
use App\Models\Vip;
use App\Models\Ware;
use App\Traits\HelperTraits\AdminTrait;
use App\Traits\HelperTraits\AttributesTrait;
use App\Traits\HelperTraits\CalcsTrait;
use App\Traits\HelperTraits\FilterTrait;
use App\Traits\HelperTraits\InfoTrait;
use App\Traits\HelperTraits\MoneyTrait;
use App\Traits\HelperTraits\RoomTrait;
use App\Traits\HelperTraits\ZegoTrait;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Twilio\Rest\Client as TwilioClint;

class Common{

    use CalcsTrait , AdminTrait , MoneyTrait ,RoomTrait , AttributesTrait,ZegoTrait ,InfoTrait, FilterTrait;

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

                'data'      => $data,

                'extra_data'=> [
                    'storage_base_url'=>self::getConf ('storage_base_url') ?:asset ('storage'),
                    'countries'=>CountryResource::collection (Country::query ()->where ('status',1)->get ())
                ],

                'paginates' =>$paginates
            ],
            $statusCode
        );
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
            'from' => $collection->firstItem(),
            'to' => $collection->lastItem(),
        ];
    }



    public static function getConf($key){
        if ($conf = Config::query ()->where('name',$key)->first ()){
            return $conf->value;
        }
        return null;
    }

    public static function upload($folder,$file){
//        $file->store('/',$folder);
//        $fileName = $file->hashName();
        $extension = $file->getClientOriginalExtension(); // Get the file extension
        $fileName = Str::random(10).'.'.$extension; // Generate a random filename and append the extension
        $file->storeAs('/public/',$folder.DIRECTORY_SEPARATOR.$fileName); // Store the file with the generated filename
        return $folder.DIRECTORY_SEPARATOR.$fileName;
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


    public static function send_firebase_notification($tokens, $title, $body,$icon = '',$data = [],$action = '', $type = '', $id = '', $notification_type = 'user_notification')
    {

        #API access key from Google API's Console
        if (!defined('API_ACCESS_KEY'))
            define('API_ACCESS_KEY', 'AAAA50BR6kU:APA91bFKjV8CCKrmAPUnTQx1uepRBQ5LoLT258NLo24p1Io8U1RAhYTMrUxMJZQmPKDxmBhm_VkNJaYLoy_vRno0XVQZI60qFuQhKh6rmXhEpFAeJOKjuD_4wVa3Ekr4d5fKLoZciPeo');

        #prep the bundle
        $msg = array(
            "android_channel_id"=>"high_importance_channel",
            'body'=> $body,
            'title'=> $title,
            "sound"=> "default",
            "notification_count"=>1,
            "visibility"=>"PUBLIC",
            "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
        );

        $fields = array(
            'registration_ids'    => $tokens,
//            'data'                => $data,
            "notification" => $msg,
            "priority"=> "high",
            "content_available"=>true,
            "direct_boot_ok"=> true,
            "apns"=>[
                "payload"=>[
                    "aps"=>[
                        "mutable-content"=>1
                    ]
                ],
                "fcm_options"=> [
                    "image"=>"https://foo.bar/pizza-monster.png"
                ]
            ],
        );

        // echo json_encode( $fields );

        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


    public static function handelVip($vip,$user){
        $wares = Ware::query ()->where ('get_type',1)->where ('level',$vip->level)->get ();
        foreach ($wares as $ware){
            Pack::query ()->where ('user_id',$user->id)
                ->where ('expire','<',now ()->timestamp)
                ->where ('expire','!=',0)
                ->delete ();
            $pack =  Pack::query ()
                ->where ('user_id',$user->id)
                ->where ('get_type',1)
                ->where ('target_id',$ware->id)
                ->where (function ($q){
                    $q->where('expire','>=',now ()->timestamp)
                        ->orWhere('expire',0);
                })->first ();

            if($pack){
                if ($pack->expire == 0){
                    throw new \Exception('already exists');
                }else{
                    $pack->expire = $ware->expire ? $pack->expire + ($ware->expire * 86400) : 0;
                    $pack->save ();
                }
            }else{
                Pack::query ()->create (
                    [
                        'user_id'=>$user->id,
                        'get_type'=>$ware->get_type,
                        'type'=>$ware->type,
                        'target_id'=>$ware->id,
                        'num'=>1,
                        'expire'=>$ware->expire ? now ()->addDays ($ware->expire)->timestamp:0
                    ]
                );
            }


        }
        $uvip = UserVip::query ()->where ('user_id',$user->id)->where (function ($q){
            $q->where('expire',0)->orWhere('expire','>',now ()->timestamp);
        })->orderBy ('level','desc')->first ();
        if ($uvip){
            $user->update (['vip'=>$uvip->id]);
        }
    }


    public static function setHourHot($uid){
        $hot = GiftLog::query()->where('roomowner_id', $uid)
            ->where('created_at', '>', now()->subHour())
            ->selectRaw('SUM(giftPrice * giftNum) as total_gift_value')
            ->first();
        DB::table ('rooms')->where ('uid',$uid)->update (['hour_hot'=>(integer)$hot->total_gift_value]);
    }


    public static function sendSMS($phone,$message){
        $account_sid = Common::getConf ('twilio_sid');
        $auth_token = Common::getConf ('twilio_api_key');
        $twilio_number = Common::getConf ('twilio_from');
        $twilio_service = Common::getConf ('twilio_service');
        try {
            $client = new TwilioClint($account_sid, $auth_token);
            if ($twilio_service){
                $arr = [
//                    'from' => $twilio_number,
                    "messagingServiceSid" => $twilio_service,
                    'body' => $message
                ];
            }else{
                $arr = [
                    'from' => $twilio_number,
                    'body' => $message
                ];
            }
            return $client->messages->create(
            // Where to send a text message (your cell phone?)
                $phone,
                $arr
            );
        }catch (\Exception $exception){

        }

    }

    public static function sendOfficialMessage($user_id,$title = '',$content = '',$type = 1){
        OfficialMessage::query ()->create (
            [
                'title'=>$title,
                'user_id'=>$user_id,
                'content'=>$content,
                'img'=>'',
                'type'=>$type
            ]
        );
    }

    public static function fireBaseFactory(){
        return (new Factory)
            ->withServiceAccount(public_path ('firebase_credentials.json'))
            ->withDatabaseUri('https://yay-chat-c2333-default-rtdb.firebaseio.com');
    }

    public static function fireBaseDatabase($path,$obj,$type = 'set'){
        $factory = self::fireBaseFactory ();
        $database = $factory->createDatabase();
        if ($type == 'set'){
            $database->getReference($path) ->set($obj);
        }else{
            return $database->getReference($path)->getSnapshot()->getValue();
        }

    }

    public static function handelFirebase($request,$type = 'follow'){
        $f_add = 0;
        $fr_add = 0;
        $vi_add = 0;
        $id = (integer)$request->user_id ;
        $snap = self::fireBaseDatabase ($id,'','get');
        $followers_count = @(integer)$snap['followers']?:0;
        $followings_count = @(integer)$snap['followings']?:0;
        $friends_count = @(integer)$snap['friends']?:0;
        $visitors_count = @(integer)$snap['visitors']?:0;
        $path = $id;

        if ($type == 'follow'){
            if (in_array ($request->user_id,$request->user ()->followers_ids()->toArray())){
                $fr_add = 1;
            }
            $f_add = 1;
        }elseif($type == 'visit'){
            $vi_add = 1;
        }

        $obj = [
            'followers'=>$followers_count + $f_add,
            'followings'=>$followings_count,
            'friends'=>$friends_count + $fr_add,
            'visitors'=>$visitors_count + $vi_add
        ];

        self::fireBaseDatabase ($path,$obj);





        $f_add = 0;
        $fr_add = 0;
        $vi_add = 0;
        $id = (integer)$request->user()->id ;
        $snap = self::fireBaseDatabase ($id,'','get');
        $followers_count = @(integer)$snap['followers']?:0;
        $followings_count = @(integer)$snap['followings']?:0;
        $friends_count = @(integer)$snap['friends']?:0;
        $visitors_count = @(integer)$snap['visitors']?:0;
        $path = $id;

        if ($type == 'follow'){
            if (in_array ($request->user_id,$request->user ()->followers_ids()->toArray())){
                $fr_add = 1;
            }
            $f_add = 1;
        }
        $obj = [
            'followers'=>$followers_count,
            'followings'=>$followings_count + $f_add,
            'friends'=>$friends_count + $fr_add,
            'visitors'=>$visitors_count + $vi_add
        ];

        self::fireBaseDatabase ($path,$obj);
    }

}
