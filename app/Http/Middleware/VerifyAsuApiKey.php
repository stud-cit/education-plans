<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyAsuApiKey
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
        $apiKey = config('app.protect_asu_api_key');

        $apiKeyIsValid = (!empty($apiKey) && $request->header('x-api-key') === $apiKey);

        abort_if(!$apiKeyIsValid, 403, 'Access denied');

        return $next($request);
    }
}
