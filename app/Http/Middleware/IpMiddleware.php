<?php

namespace App\Http\Middleware;

use App\Helpers\Common;
use App\Models\Ban;
use App\Models\Ip;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user ();
        Ip::query ()->updateOrCreate (
            [
                'ip'=>$request->ip (),
                'uid'=>@$user->id,
            ],
            [
                'ip'=>$request->ip (),
                'uid'=>@$user->id,
                'user_type'=>0
            ]
        );
        return $next($request);
    }
}
