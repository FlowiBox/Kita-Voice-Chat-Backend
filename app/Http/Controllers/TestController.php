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
        $user = User::first();
        $packs = Pack::query()
            ->where ('user_id',$user->id)
            ->where (function ($q){
                $q->where('expire',0)->orWhere('expire','>=',now ()->timestamp);
            })->get();

        dd($packs);
    }
}
