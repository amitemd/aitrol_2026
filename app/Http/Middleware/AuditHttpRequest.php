<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AuditHttpRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = hrtime(true);
        $response = $next($request);

        try {
            Log::info('Solicitud HTTP atendida.', [
                'method' => $request->method(),
                'path' => $request->path(),
                'route' => $request->route()?->getName(),
                'controller' => $request->route()?->getActionName(),
                'status' => $response->getStatusCode(),
                'user_id' => $request->user()?->getAuthIdentifier(),
                'company_id' => $request->session()->get('new_system.company_id')
                    ?? $request->session()->get('id_empresa'),
                'ip' => $request->ip(),
                'duration_ms' => round((hrtime(true) - $startedAt) / 1_000_000, 2),
            ]);
        } catch (Throwable) {
        }

        return $response;
    }
}
