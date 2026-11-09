<?php

namespace Sis_medico\Http\Middleware;

use Closure;

class EnforceSecureClinicalTransport
{
    public function handle($request, Closure $next)
    {
        $force = (bool) config('clinical.force_https', false);
        if ($force && !$request->isSecure()) {
            $canonicalHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            $canonicalPort = parse_url((string) config('app.url'), PHP_URL_PORT);
            if (!$canonicalHost) return response('Dominio canónico no configurado.', 503)
                ->header('Cache-Control', 'no-store, private');
            $authority = $canonicalHost . ($canonicalPort && (int) $canonicalPort !== 443 ? ':' . $canonicalPort : '');
            $url = 'https://' . $authority . $request->getRequestUri();
            return redirect()->away($url, $request->isMethod('GET') || $request->isMethod('HEAD') ? 301 : 307);
        }

        $response = $next($request);
        // Manipular la colección de cabeceras de Symfony evita avisos de
        // "headers already sent" en CLI, pruebas y respuestas transmitidas.
        $response->headers->remove('X-Powered-By');
        if ($request->isSecure()) {
            $hstsMaxAge = $force ? max(0, (int) config('clinical.hsts_max_age', 86400)) : 0;
            $hsts = 'max-age=' . $hstsMaxAge;
            if ($hstsMaxAge > 0 && config('clinical.hsts_include_subdomains', false)) {
                $hsts .= '; includeSubDomains';
            }
            if ($hstsMaxAge > 0 && config('clinical.hsts_preload', false)) {
                $hsts .= '; preload';
            }
            $response->headers->set(
                'Strict-Transport-Security',
                $hsts
            );
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            if (!$response->headers->has('Referrer-Policy')) $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            if (!$response->headers->has('X-Frame-Options')) $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
            // La cámara clínica solo puede solicitarse desde el propio Aitrol y
            // sigue sujeta al consentimiento explícito del navegador.
            $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(self), geolocation=(self)');
            $response->headers->set('Content-Security-Policy', "base-uri 'self'; frame-ancestors 'self'; form-action 'self'; object-src 'none'; upgrade-insecure-requests; block-all-mixed-content");
        }
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('Pragma', 'no-cache');
        return $response;
    }
}
