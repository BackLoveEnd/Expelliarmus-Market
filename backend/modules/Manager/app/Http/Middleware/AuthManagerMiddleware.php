<?php

namespace Modules\Manager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthManagerMiddleware
{
    public function handle(Request $request, Closure $next, ?string $onlyFor = null)
    {
        if (! auth('manager')->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($onlyFor === 'super-manager' && ! $request->user('manager')?->isSuperManager()) {
            return response()->json(['message' => 'Access denied. Only for main manager.'], 403);
        }

        Auth::shouldUse('manager');

        return $next($request);
    }
}
