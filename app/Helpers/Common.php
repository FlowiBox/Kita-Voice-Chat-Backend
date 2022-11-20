<?php
namespace App\Helpers;
class Common{
    public static function apiResponse(bool $success,string $message,$data,$statusCode = 200){
        return response ()->json (
            [
                'success'   => $success,

                'message'   => __ ($message),

                'data'      => $data == [] || $data == null ? null : $data
            ],
            $statusCode
        );
    }
}
