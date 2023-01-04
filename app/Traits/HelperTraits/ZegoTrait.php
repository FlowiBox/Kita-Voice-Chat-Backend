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

    public static function sendToZego($Action,$RoomId,$FromUserId,$MessageContent,$IsTest = 'no'){
        $url = 'https://rtc-api.zego.im';
        $AppId = self::$appId;
        $SignatureNonce = self::getSignatureNonce ();
        $Timestamp = time ();
        $str = $AppId.$SignatureNonce.static::$serverSecret.$Timestamp;
        $signature = md5($str);
        $SignatureVersion = 2.0;
        $q = "?Action=$Action&RoomId=$RoomId&FromUserId=$FromUserId&MessageContent=$MessageContent&AppId=$AppId&SignatureNonce=$SignatureNonce&Timestamp=$Timestamp&Signature=$signature&SignatureVersion=$SignatureVersion&IsTest=$IsTest";
        $res = Http::withHeaders (
            [
                'Host'=>'<calculated when request is sent>'
            ]
        )->get ($url.$q);
        return $res;
    }

}
