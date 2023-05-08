<?php


namespace App\Traits\HelperTraits;


use App\Helpers\Common;
use Illuminate\Support\Facades\Http;

Trait ZegoTrait
{

    public static  $serverSecret;
    public static  $appId;

    public static function getSignatureNonce(){
        return bin2hex(random_bytes(8));
    }

    public static function GenerateSignature()
    {
        $str = self::getConf ('zego_app_id').static::getSignatureNonce ().self::getConf('zego_server_secret').time();
        $signature = md5($str);
        return $signature;
    }

    public static function sendToZego($Action,$RoomId,$FromUserId,$MessageContent,$IsTest = 'false'){
        $url = 'https://rtc-api.zego.im';
        $AppId = self::getConf ('zego_app_id');
        $SignatureNonce = self::getSignatureNonce ();
        $Timestamp = time ();
        $str = $AppId.$SignatureNonce.self::getConf('zego_server_secret').$Timestamp;
        $signature = md5($str);
        $SignatureVersion = '2.0';
        $params = [
            'Action'=>$Action,
            'RoomId'=>$RoomId,
            'FromUserId'=>$FromUserId,
            'MessageContent'=>$MessageContent,
            'AppId'=>$AppId,
            'SignatureNonce'=>$SignatureNonce,
            'Timestamp'=>$Timestamp,
            'Signature'=>$signature,
            'SignatureVersion'=>$SignatureVersion,
            'IsTest'=>$IsTest
        ];
        $headers = [

        ];
        try {
            Http::withHeaders ($headers)->acceptJson ()->timeout (20)->get ($url,$params)->json ();
        }catch (\Exception $exception){

        }

        return ;
    }

    public static function sendToZego_2($Action,$RoomId,$UserId,$UserName,$MessageContent,$IsTest = 'false'){
        $url = 'https://rtc-api.zego.im';
        $AppId = self::getConf ('zego_app_id');
        $SignatureNonce = self::getSignatureNonce ();
        $Timestamp = time ();
        $str = $AppId.$SignatureNonce.self::getConf('zego_server_secret').$Timestamp;
        $signature = md5($str);
        $SignatureVersion = '2.0';
        $params = [
            'Action'=>$Action,
            'RoomId'=>$RoomId,
            'UserId'=>$UserId,
            'UserName'=>$UserName,
            'MessageCategory'=>1,
            'MessageContent'=>$MessageContent,
            'AppId'=>$AppId,
            'SignatureNonce'=>$SignatureNonce,
            'Timestamp'=>$Timestamp,
            'Signature'=>$signature,
            'SignatureVersion'=>$SignatureVersion,
            'IsTest'=>$IsTest
        ];
        $headers = [

        ];
        try {
            Http::withHeaders ($headers)->acceptJson ()->timeout (10)->get ($url,$params)->json ();
        }catch (\Exception $exception){

        }
        return ;
    }

    public static function sendToZego_3($Action,$RoomId,$UserId,$IsTest = 'false'){
        $url = 'https://rtc-api.zego.im';
        $AppId = self::getConf ('zego_app_id');
        $SignatureNonce = self::getSignatureNonce ();
        $Timestamp = time ();
        $str = $AppId.$SignatureNonce.self::getConf('zego_server_secret').$Timestamp;
        $signature = md5($str);
        $SignatureVersion = '2.0';
        $params = [
            'Action'=>$Action,
            'RoomId'=>$RoomId,
            'UserId[]'=>$UserId,
            'AppId'=>$AppId,
            'SignatureNonce'=>$SignatureNonce,
            'Timestamp'=>$Timestamp,
            'Signature'=>$signature,
            'SignatureVersion'=>$SignatureVersion,
            'IsTest'=>$IsTest
        ];
        $headers = [

        ];
        try {
            $res = Http::withHeaders ($headers)->acceptJson ()->timeout (10)->get ($url,$params)->json ();
        }catch (\Exception $exception){

        }

        return $res;
    }

    public static function sendToZego_4($Action,$RoomId,$fromUserId,$toUserId,$MessageContent,$IsTest = 'false'){
        $url = 'https://rtc-api.zego.im';
        $AppId = self::getConf ('zego_app_id');
        $SignatureNonce = self::getSignatureNonce ();
        $Timestamp = time ();
        $str = $AppId.$SignatureNonce.self::getConf('zego_server_secret').$Timestamp;
        $signature = md5($str);
        $SignatureVersion = '2.0';
        $params = [
            'Action'=>$Action,
            'RoomId'=>$RoomId,
            'FromUserId'=>$fromUserId,
            'ToUserId[]'=>$toUserId,
            'MessageContent'=>$MessageContent,
            'AppId'=>$AppId,
            'SignatureNonce'=>$SignatureNonce,
            'Timestamp'=>$Timestamp,
            'Signature'=>$signature,
            'SignatureVersion'=>$SignatureVersion,
            'IsTest'=>$IsTest
        ];
        $headers = [

        ];
        try {
            $res = Http::withHeaders ($headers)->acceptJson ()->timeout (10)->get ($url,$params)->json ();
        }catch (\Exception $exception){

        }

        return $res;
    }
}
