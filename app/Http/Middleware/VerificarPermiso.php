<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class VerificarPermiso
{
    public function handle(Request $request, Closure $next, string $opcion, string $accion): Response
    {
        Gate::authorize('permiso', [$opcion, $accion]);

        return $next($request);
    }
}