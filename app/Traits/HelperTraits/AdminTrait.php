<?php
namespace App\Traits\HelperTraits;

trait AdminTrait{
    public static function getSwitchStates(){
        return [
            'on'=>['value'=>1,'text'=>'yes','color'=>'success'],
            'off'=>['value'=>0,'text'=>'no','color'=>'danger'],
        ];
    }

    public static function getSwitchStates2(){
        return [
            'on'=>['value'=>1,'text'=>'pass','color'=>'success'],
            'off'=>['value'=>0,'text'=>'stop','color'=>'danger'],
        ];
    }
}
