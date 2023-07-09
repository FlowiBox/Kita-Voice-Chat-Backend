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

        $duplicated = User::whereIn('device_token', function ( $query ) {
            $query->select('device_token')->from('users')->groupBy('device_token')->havingRaw('count(*) > 1');
        })->get();

        $nulled_users = DB::table('users')->whereNull('device_token')->count();
        $un_nulled_users = DB::table('users')->whereNotNull('device_token')->count();

        dd($duplicated, $nulled_users, $un_nulled_users);

    }
}
