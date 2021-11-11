<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsActivated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user()->activation_token)
            return response('account not activated',401);
        return $next($request);
    }
}
