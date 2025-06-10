<?php

namespace Modules\Product\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AppendIncludeRelationships
{
    public function handle(Request $request, Closure $next, string ...$includes): mixed
    {
        $query = $request->query();

        $query['include'] = implode(',', $includes);

        $request->query->replace($query);

        return $next($request);
    }
}
