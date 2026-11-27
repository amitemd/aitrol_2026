<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

final class VerificarEmpresa
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();
        if (! $usuario?->empresa()->exists()) {
            if ($request->is('api/*') || $request->expectsJson()) {
                abort(403, 'La cuenta debe tener una empresa activa.');
            }

            return to_route('dashboard');
        }

        $token = $usuario->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            abort_unless((int) $token->id_empresa === (int) $usuario->id_empresa, 403, 'El token no corresponde a la empresa activa.');
        }

        return $next($request);
    }
}
