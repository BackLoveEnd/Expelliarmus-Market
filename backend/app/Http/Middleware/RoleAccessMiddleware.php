<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleAccessMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user('web') ?: $request->user('manager');

        if ($user->getRoleNames()->first() !== $role) {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        return $next($request);
    }
}
