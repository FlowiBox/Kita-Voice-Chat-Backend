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
            })
            ->pluck('type')
            ->toArray();
        $no_kick            = in_array(9, $packs);
        $intro_animation    = in_array(11, $packs);
        $vip_gifts          = in_array(14, $packs);
        $no_pan             = in_array(15, $packs);
        $anonymous_man      = in_array(17, $packs);
        $colored_name       = in_array(18, $packs);
        dd($no_kick, $intro_animation, $vip_gifts, $no_pan, $anonymous_man, $colored_name);
    }
}
