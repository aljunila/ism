<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthApiMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Reuse session-based login; return JSON 401 instead of redirect.
        if (session()->get('username')) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}
