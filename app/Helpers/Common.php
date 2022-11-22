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

    public static function upload($folder,$file){
        $file->store('/',$folder);
        $fileName = $file->hashName();
        return $folder.DIRECTORY_SEPARATOR.$fileName;
    }
}
