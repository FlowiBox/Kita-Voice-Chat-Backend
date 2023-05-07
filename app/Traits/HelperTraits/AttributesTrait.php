<?php


namespace App\Traits\HelperTraits;


use App\Models\Pack;
use App\Models\Ware;

trait AttributesTrait
{
    public static function getUserDress($user_id,$dress,$type,$item = 'img1'){
        $dr = '';
        if (!$dress){
            return $dr;
        }
        $pack = Pack::query ()
            ->where ('user_id',$user_id)
            ->where ('target_id',$dress)
            ->where ('type',$type)
            ->where (function ($q){
                $q->where('expire',0)->orWhere('expire','>=',now ()->timestamp);
            })
            ->exists ();
        if ($pack){
            $ware = Ware::query ()
                ->where ('id',$dress)
                ->where ('enable',1)
                ->where ('type',$type)
                ->first ();
            if ($ware){
                $dr = $ware->{$item};
            }
        }

        return $dr?:'';
    }

    public static function pack_get($key,$user_id){
        return Pack::query ()
            ->where ('user_id',$user_id)
            ->where ('type',$key)
            ->where (function ($q){
                $q->where('expire',0)->orWhere('expire','>=',now ()->timestamp);
            })
            ->exists ();
    }
}
