<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class Currency
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    public function handle($request, Closure $next)
    {
        $sessioncurrency = session()->get('syscurrency', 1);
        if (empty($sessioncurrency)) {
        session()->put('syscurrency', 1);
        }
        return $next($request);
    }



}
