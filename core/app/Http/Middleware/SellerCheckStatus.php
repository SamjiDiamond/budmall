<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class SellerCheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('seller')->check()) {
            $user = seller();
            if ($user->status  && $user->ev  && $user->sv  && $user->tv) {
                return $next($request);
            } else {
                return redirect()->route('seller.authorization');
            }
        }

        abort(403);
    }
}
