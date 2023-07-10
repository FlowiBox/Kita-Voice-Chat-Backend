<?php

namespace App\Http\Middleware;

use App\Helpers\Common;
use App\Models\Ban;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class GeneralBanMiddleware
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

        //Todo remove it in 12-7-2023
        if(!$user->device_token && $user instanceof User){
            $user->enableSaving = false;
            $user->device_token = $request->header ('device');
            $user->save ();
        }

        $now = now();
        $is_user_ban = Ban::query ()
            ->where ('uid',$user->uuid)
            ->where ('user_type',0)
            ->whereRaw("created_at + INTERVAL duration HOUR > '$now'")
            ->where ('type','all')
            ->exists ();
        $ip_ban = Ban::query ()->where ('ip',$request->ip())
            ->whereRaw("created_at + INTERVAL duration HOUR > '$now'")
            ->exists ();
        $device_ban = Ban::query ()
            ->whereNotNull ('device_number')
            ->where ('device_number',$request->header ('device'))
            ->whereRaw("created_at + INTERVAL duration HOUR > '$now'")
            ->exists ();
        if ($is_user_ban || $ip_ban || $device_ban){
            return Common::apiResponse (0,'ban reason',null,501);
        }
        return $next($request);
    }
}
