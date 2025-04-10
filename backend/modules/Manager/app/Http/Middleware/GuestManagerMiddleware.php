<?php

namespace Modules\Manager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestManagerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('manager')->check()) {
            return response()->json(['message' => 'Already authenticated'], 403);
        }

        return $next($request);
    }
}
