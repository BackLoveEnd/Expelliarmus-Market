<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\User\Enums\RolesEnum;

class GuestOrUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth(RolesEnum::MANAGER->toString())->check()) {
            return response()->json(['message' => 'Not allowed. Should be customer account.'], 403);
        }

        return $next($request);
    }
}
