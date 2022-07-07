<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('Authorization');

        clock("custom auth has key: $key");

        if (!$request->session()->get($key)) {
            return abort(403);
        }

        // if(!Auth::check()) {
        //     //return abort(403); //$next($request);
        //     clock('if', !Auth::check()); //$next($request);
        // }
        return $next($request);
    }
}
