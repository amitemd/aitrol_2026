<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Permite que Laravel 5.4 reconozca HTTPS cuando el servidor web termina SSL
 * y reenvía la petición al PHP interno mediante X-Forwarded-Proto.
 */
class TrustProxies
{
    public function handle($request, Closure $next)
    {
        $proxies = config('app.trusted_proxies', '*');
        $proxies = $proxies === '*' ? ['0.0.0.0/0', '::/0'] : array_filter(array_map('trim', explode(',', $proxies)));

        if (method_exists($request, 'setTrustedProxies')) {
            $request->setTrustedProxies($proxies, SymfonyRequest::HEADER_X_FORWARDED_ALL);
        }

        if (config('app.force_https', false)) {
            $url = app('url');
            if (method_exists($url, 'forceScheme')) {
                $url->forceScheme('https');
            } elseif (method_exists($url, 'forceSchema')) {
                $url->forceSchema('https');
            }
        }

        return $next($request);
    }
}
