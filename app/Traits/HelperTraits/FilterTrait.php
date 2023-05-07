<?php


namespace App\Traits\HelperTraits;


use App\Models\Agency;
use App\Models\Family;

trait FilterTrait
{

    public static function by_agency_filter (){
        $ops = [0=>'no agency'];
        $agencies = Agency::query ()->where ('status',1)->get ();
        foreach ($agencies as $agency){
            $ops[$agency->id]=$agency->name;
        }
        return $ops;
    }

    public static function by_family_filter (){
        $ops = [0=>'no family'];
        $families = Family::query ()->where ('status',1)->get ();
        foreach ($families as $family){
            $ops[$family->id]=$family->name;
        }
        return $ops;
    }


}
