<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProtectApi
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
        if ($request->input('key') !== config('app.protect_api_key')) {
            return response(['message' => 'Key is not valid!'], 401);
        }

        return $next($request);
    }
}
