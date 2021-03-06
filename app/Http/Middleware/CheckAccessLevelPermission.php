<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Facades\Helpers\AccessLevelPermissionHelper as ALP;

class CheckAccessLevelPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @always use this middleware with \App\Http\Middleware\Authenticate
     * @usage middleware('alp:access_level@255,read,write')
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $filters = ['access_level', 'read', 'write'];
        $guards = $guards ?: [];
        foreach ($guards as $guard) {
            $keyLevel = explode('@', $guard);
            if (COUNT($keyLevel) === 2 && in_array($keyLevel[0], $filters)) {
                if (ALP::accessLevelGranted((int) $keyLevel[1]) === false)
                    abort(403, __('Forbidden'));
            } else if (in_array($guard, $filters)) {
                $fn = 'can'.ucfirst($guard);
                if (ALP::$fn() === false)
                    abort(403, __('Forbidden'));
            }
        }
        return $next($request);
    }
}
