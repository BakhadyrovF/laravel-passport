<?php

namespace App\Http\Middleware;

use App\Models\Client;
use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;

class IsValidToken
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
        $user = auth('api')->user();
        if ($request->hasCookie('refresh_token') && is_null($user)) {
            app(AuthService::class)->refreshTokens($request);
        }

        return $next($request);
    }
}
