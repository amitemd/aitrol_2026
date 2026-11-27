<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ExigirHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('seguridad.exigir_https') || $request->isSecure()) {
            return $next($request);
        }

        if ($request->is('api/*') || ! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return response('Usa HTTPS para acceder a Aitrol.', 403);
        }

        $url = config('app.url');
        if (parse_url($url, PHP_URL_SCHEME) !== 'https' || ! parse_url($url, PHP_URL_HOST)) {
            return response('Configura la URL HTTPS de Aitrol.', 503);
        }

        return redirect()->away(rtrim($url, '/').'/'.ltrim($request->getPathInfo(), '/'), 308);
    }
}
