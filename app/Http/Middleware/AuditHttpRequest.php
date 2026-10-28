<?php

namespace App\Http\Middleware;

use App\Modules\SupportIntegrations\Application\Contracts\RequestArchitectureInspector;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class AuditHttpRequest
{
    public function __construct(private RequestArchitectureInspector $architecture) {}

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = hrtime(true);
        $correlationId = (string) Str::uuid();
        $architecture = $this->architecture->inspect($request->route()?->getActionName());

        Context::add([
            'correlation_id' => $correlationId,
            'request_trace' => [
                'method' => $request->method(),
                'path' => $request->path(),
                'route' => $request->route()?->getName(),
                'controller' => $request->route()?->getActionName(),
                'user_id' => $request->user()?->getAuthIdentifier(),
                'company_id' => $request->session()->get('new_system.company_id')
                    ?? $request->session()->get('id_empresa'),
                'architecture' => $architecture,
            ],
        ]);

        try {
            $response = $next($request);
            $this->record(
                $request,
                $correlationId,
                $architecture,
                $response->getStatusCode(),
                round((hrtime(true) - $startedAt) / 1_000_000, 2),
            );

            return $response;
        } catch (Throwable $exception) {
            $this->record(
                $request,
                $correlationId,
                $architecture,
                500,
                round((hrtime(true) - $startedAt) / 1_000_000, 2),
                $exception::class,
            );

            throw $exception;
        } finally {
            Context::forget(['correlation_id', 'request_trace']);
        }
    }

    /**
     * @param  array{controller: ?string, method: ?string, components: list<array{type: string, name: string, implementation: ?string}>}  $architecture
     */
    private function record(
        Request $request,
        string $correlationId,
        array $architecture,
        int $status,
        float $duration,
        ?string $exception = null,
    ): void {
        $context = [
            'correlation_id' => $correlationId,
            'method' => $request->method(),
            'path' => $request->path(),
            'route' => $request->route()?->getName(),
            'controller' => $request->route()?->getActionName(),
            'status' => $status,
            'user_id' => $request->user()?->getAuthIdentifier(),
            'company_id' => $request->session()->get('new_system.company_id')
                ?? $request->session()->get('id_empresa'),
            'ip' => $request->ip(),
            'duration_ms' => $duration,
            'architecture' => $architecture,
            'exception' => $exception,
        ];

        try {
            if ($exception !== null || $status >= 500) {
                Log::error('Solicitud HTTP fallida.', $context);

                return;
            }

            if ($status >= 400 || $duration >= (float) config('observability.slow_request_warning_ms', 500)) {
                Log::warning('Solicitud HTTP atendida con advertencia.', $context);

                return;
            }

            Log::info('Solicitud HTTP atendida.', $context);
        } catch (Throwable) {
        }
    }
}
