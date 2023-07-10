<?php

namespace App\Http\Controllers;

use App\Models\LiveTime;
use App\Models\Pack;
use App\Models\User;
use App\Models\UserDay;
use App\Models\Vip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index(Request $request) {
        $today = LiveTime::where ('uid',$request->id)->whereDate('created_atq',today ())->sum('hours');
        $today_1 = LiveTime::where ('uid',$request->id)->whereDate('created_at', Carbon::yesterday())->sum('hours');
        $today_2 = LiveTime::where ('uid',$request->id)->whereDate('created_at',Carbon::today()->subDays(2))->sum('hours');
        $today_3 = LiveTime::where ('uid',$request->id)->whereDate('created_at',Carbon::today()->subDays(3))->sum('hours');
        $today_4 = LiveTime::where ('uid',$request->id)->whereDate('created_at',Carbon::today()->subDays(4))->sum('hours');
        $today_5 = LiveTime::where ('uid',$request->id)->whereDate('created_at',Carbon::today()->subDays(5))->sum('hours');
        $today_6 = LiveTime::where ('uid',$request->id)->whereDate('created_at',Carbon::today()->subDays(6))->sum('hours');
        dd($today, $today_1, $today_2, $today_3, $today_4, $today_5, $today_6);
    }
}
