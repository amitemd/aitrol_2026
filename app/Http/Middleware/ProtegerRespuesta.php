<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ProtegerRespuesta
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(18));
        $request->attributes->set('csp_nonce', $nonce);
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'same-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), payment=(), usb=()');
        $response->headers->set('Cache-Control', 'private, no-store');

        if ($request->isSecure() && config('seguridad.exigir_https')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        if (str_starts_with((string) $response->headers->get('Content-Type'), 'text/html')) {
            $response->headers->set('Content-Security-Policy', implode('; ', [
                "default-src 'self'",
                "script-src 'nonce-{$nonce}' 'strict-dynamic' 'self' https://cdn.jsdelivr.net https://code.jquery.com https://unpkg.com",
                "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://unpkg.com",
                "img-src 'self' data: blob: https://tile.openstreetmap.org https://unpkg.com",
                "font-src 'self' data: https://cdn.jsdelivr.net",
                "connect-src 'self'",
                "frame-src 'self'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                "base-uri 'none'",
                "object-src 'none'",
            ]));
        }

        return $response;
    }
}
