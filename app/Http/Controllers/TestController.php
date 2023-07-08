<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use App\Models\User;
use App\Models\UserDay;
use App\Models\Vip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index() {
        $user = User::find(1890);
        $packs = Pack::query()
            ->where ('user_id',$user->id)
            ->where (function ($q){
                $q->where('expire',0)->orWhere('expire','>=',now ()->timestamp);
            })->get();
        $no_kick=false;
        $intro_animation=false;
        $vip_gifts=false;
        $no_pan=false;
        $anonymous_man=false;
        $colored_name=false;
        foreach ($packs as $pack) {
            $no_kick            = !$no_kick ? $pack->type == 9 : $no_kick;
            $intro_animation    = !$intro_animation ? $pack->type == 11 : $intro_animation;
            $vip_gifts          = !$vip_gifts ? $pack->type == 14 : $vip_gifts;
            $no_pan             = !$no_pan ? $pack->type == 15 : $no_pan;
            $anonymous_man      = !$anonymous_man ? $pack->type == 17 : $anonymous_man;
            $colored_name       = !$colored_name ? $pack->type == 18 : $colored_name;
        }
        dd($no_kick, $intro_animation, $vip_gifts, $no_pan, $anonymous_man, $colored_name);
    }
}
