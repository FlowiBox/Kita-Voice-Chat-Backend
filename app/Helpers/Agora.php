<?php


namespace App\Helpers;


use App\Classes\AgoraDynamicKey\RtcTokenBuilder;
use App\Classes\AgoraDynamicKey\RtmTokenBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Agora
{
    public static function RTCToken($uid = 0,$channelName = 'default',$role = 2)
    {

        try {
            $appID = Common::getConf ('agora_rtc_app_id');//config('app.agora_app_id');
            $appCertificate = Common::getConf ('agora_rtc_app_certificate');//config('app.agora_app_certificate');
            $expireTimeInSeconds = 3600;
            $currentTimestamp = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp();
            $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
            return RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);

        }catch (\Throwable $exception){
            return null;
        }
    }


    public static function RTMToken($uid = 0,$role = 1){
        try {
            $appID = Common::getConf ('agora_rtm_app_id');//config('app.agora_rtm_app_id');
            $appCertificate = Common::getConf ('agora_rtm_app_certificate');//config('app.agora_rtm_app_certificate');
            $expireTimeInSeconds = 3600;
            $currentTimestamp = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp();
            $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
            return RtmTokenBuilder::buildToken($appID, $appCertificate, "$uid", $role, $privilegeExpiredTs);

        }catch (\Exception $exception){
           return null;
        }

    }

}
