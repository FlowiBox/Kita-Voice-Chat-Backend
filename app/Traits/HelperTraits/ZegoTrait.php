<?php


namespace App\Traits\HelperTraits;


use App\Helpers\Common;
use Illuminate\Support\Facades\Http;

Trait ZegoTrait
{

    public static  $serverSecret = 'd161c1daca18e1fa29ca74de431d5981';
    public static  $appId = 1381228;

    public static function getSignatureNonce(){
        return bin2hex(random_bytes(8));
    }

    public static function GenerateSignature()
    {
        $str = static::$appId.static::getSignatureNonce ().static::$serverSecret.time();
        $signature = md5($str);
        return $signature;
    }

    public static function sendToZego($Action,$RoomId,$FromUserId,$MessageContent,$IsTest = 'false'){
        $url = 'https://rtc-api.zego.im';
        $AppId = self::$appId;
        $SignatureNonce = self::getSignatureNonce ();
        $Timestamp = time ();
        $str = $AppId.$SignatureNonce.static::$serverSecret.$Timestamp;
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
        $res = Http::withHeaders ($headers)->acceptJson ()->get ($url,$params)->json ();
        return $res;
    }

    public static function sendToZego_2($Action,$RoomId,$UserId,$UserName,$MessageContent,$IsTest = 'false'){
        $url = 'https://rtc-api.zego.im';
        $AppId = self::$appId;
        $SignatureNonce = self::getSignatureNonce ();
        $Timestamp = time ();
        $str = $AppId.$SignatureNonce.static::$serverSecret.$Timestamp;
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
        $res = Http::withHeaders ($headers)->acceptJson ()->get ($url,$params)->json ();
        return $res;
    }

    public static function sendToZego_3($Action,$RoomId,$UserId,$IsTest = 'false'){
        $url = 'https://rtc-api.zego.im';
        $AppId = self::$appId;
        $SignatureNonce = self::getSignatureNonce ();
        $Timestamp = time ();
        $str = $AppId.$SignatureNonce.static::$serverSecret.$Timestamp;
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
        $res = Http::withHeaders ($headers)->acceptJson ()->get ($url,$params)->json ();
        return $res;
    }

    public static function sendToZego_4($Action,$RoomId,$UserId,$IsTest = 'false'){
        $url = 'https://rtc-api.zego.im';
        $AppId = self::$appId;
        $SignatureNonce = self::getSignatureNonce ();
        $Timestamp = time ();
        $str = $AppId.$SignatureNonce.static::$serverSecret.$Timestamp;
        $signature = md5($str);
        $SignatureVersion = '2.0';
        $params = [
            'Action'=>$Action,
            'RoomId'=>$RoomId,
            'FromUserId'=>$fromUserId,
            'ToUserId[]'=>$toUserId,
            'AppId'=>$AppId,
            'SignatureNonce'=>$SignatureNonce,
            'Timestamp'=>$Timestamp,
            'Signature'=>$signature,
            'SignatureVersion'=>$SignatureVersion,
            'IsTest'=>$IsTest
        ];
        $headers = [

        ];
        $res = Http::withHeaders ($headers)->acceptJson ()->get ($url,$params)->json ();
        return $res;
    }
}
https://rtc-api.zego.im/?Action=SendCustomCommand&AppId=1381228&Timestamp=1672910944&Signature=c9ea8a5e1d5d75281e5904c40efc755b&SignatureVersion=2.0&SignatureNonce=e03330b2b68e7505&IsTest=no&RoomId=156&FromUserId=246&ToUserId[]=221&MessageContent={ 'messageContent': 'showEmojie',
'id':   8    }
