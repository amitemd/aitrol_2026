<?php

namespace Sis_medico\Http\Middleware;

use Closure;

class RequireJsonApiResponse
{
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
