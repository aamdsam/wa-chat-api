<?php

namespace App\Http\Middleware;

use Closure;

class SetHeader
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
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Accept', 'application/json')
            ->header('Content-Type', 'application/json');
    }
}
