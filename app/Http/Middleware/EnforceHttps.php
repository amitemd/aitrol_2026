<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.force_https') || $request->isSecure()) {
            return $next($request);
        }

        if ($request->isMethodSafe()) {
            return redirect()->secure($request->getRequestUri(), 308);
        }

        return response('HTTPS is required for this request.', 400);

    }
}
