<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = 'seller')
    {
        if (Auth::guard('seller')->check()) {
            return $next($request);
        }

        if(Auth::guard($guard)->user() && Auth::guard($guard)->user()->status == 0){
            $user = Auth::guard($guard)->user();

            $user->logout($user);

            $notify[]=['error','Your account is banned by the super admin'];
            return redirect()->route('seller.login')->withNotify($notify);
        }

        return redirect()->route('seller.login');
    }
}
