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
            User::query()->update(['device_token' => null]);
            dd('ff');

        }
    }
}
