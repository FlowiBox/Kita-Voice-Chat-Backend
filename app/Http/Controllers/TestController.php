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
        foreach (\App\Models\User::all() as $user) {

            $duplicates = DB::table('users')
                ->select('device_token', DB::raw('COUNT(*) as `device_token`'))
                ->groupBy('device_token', 'location')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            $nulled_users = DB::table('users')->where('device_token', null)->get();

            dd($duplicates, $nulled_users);

        }
    }
}
