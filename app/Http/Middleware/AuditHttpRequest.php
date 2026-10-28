<?php

namespace App\Http\Middleware;

use App\Modules\SupportIntegrations\Application\Contracts\HttpRequestAuditRecorder;
use App\Modules\SupportIntegrations\Application\Contracts\RequestArchitectureInspector;
use App\Modules\SupportIntegrations\Application\Contracts\RequestExecutionTracer;
use App\Modules\SupportIntegrations\Application\Data\HttpRequestAuditEntry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class AuditHttpRequest
{
    public function __construct(
        private RequestArchitectureInspector $architecture,
        private RequestExecutionTracer $executionTracer,
        private HttpRequestAuditRecorder $auditRecorder,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = hrtime(true);
        $correlationId = (string) Str::uuid();
        $initialUserId = $request->user()?->getAuthIdentifier();
        $initialCompanyId = $request->session()->get('new_system.company_id')
            ?? $request->session()->get('id_empresa');
        $architecture = $this->architecture->inspect($request->route()?->getActionName());
        $this->executionTracer->start($startedAt);

        Context::add([
            'correlation_id' => $correlationId,
            'request_trace' => [
                'method' => $request->method(),
                'path' => $this->auditedPath($request),
                'route' => $request->route()?->getName(),
                'controller' => $request->route()?->getActionName(),
                'user_id' => $initialUserId,
                'company_id' => $initialCompanyId,
                'architecture' => $architecture,
            ],
        ]);

        try {
            $routeName = $request->route()?->getName() ?? $request->path();
            $response = $this->executionTracer->measure(
                name: strtoupper($request->method()).' '.$routeName,
                type: 'Acción HTTP',
                callback: fn (): Response => $next($request),
                context: [
                    'route' => $routeName,
                    'controller' => $request->route()?->getActionName(),
                ],
            );
            $this->record(
                $request,
                $correlationId,
                $architecture,
                $response->getStatusCode(),
                round((hrtime(true) - $startedAt) / 1_000_000, 2),
                $this->executionTracer->spans(),
                initialUserId: $initialUserId !== null ? (string) $initialUserId : null,
                initialCompanyId: $initialCompanyId !== null ? (string) $initialCompanyId : null,
            );

            return $response;
        } catch (Throwable $exception) {
            $this->record(
                $request,
                $correlationId,
                $architecture,
                500,
                round((hrtime(true) - $startedAt) / 1_000_000, 2),
                $this->executionTracer->spans(),
                $exception::class,
                $initialUserId !== null ? (string) $initialUserId : null,
                $initialCompanyId !== null ? (string) $initialCompanyId : null,
            );

            throw $exception;
        } finally {
            $this->executionTracer->stop();
            Context::forget(['correlation_id', 'request_trace']);
        }
    }

    /**
     * @param  array{controller: ?string, method: ?string, components: list<array{type: string, name: string, implementation: ?string}>}  $architecture
     * @param  list<array<string, mixed>>  $spans
     */
    private function record(
        Request $request,
        string $correlationId,
        array $architecture,
        int $status,
        float $duration,
        array $spans,
        ?string $exception = null,
        ?string $initialUserId = null,
        ?string $initialCompanyId = null,
    ): void {
        $currentUserId = $request->user()?->getAuthIdentifier();
        $currentCompanyId = $request->session()->get('new_system.company_id')
            ?? $request->session()->get('id_empresa');
        $context = [
            'correlation_id' => $correlationId,
            'method' => $request->method(),
            'path' => $this->auditedPath($request),
            'route' => $request->route()?->getName(),
            'controller' => $request->route()?->getActionName(),
            'status' => $status,
            'user_id' => $currentUserId ?? $initialUserId,
            'company_id' => $currentCompanyId ?? $initialCompanyId,
            'ip' => $request->ip(),
            'duration_ms' => $duration,
            'spans' => $spans,
            'architecture' => $architecture,
            'exception' => $exception,
        ];

        $this->persist(
            $request,
            $correlationId,
            $architecture,
            $status,
            $duration,
            $spans,
            $exception,
            $initialUserId,
            $initialCompanyId,
        );

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

    /**
     * @param  array{controller: ?string, method: ?string, components: list<array{type: string, name: string, implementation: ?string}>}  $architecture
     * @param  list<array<string, mixed>>  $spans
     */
    private function persist(
        Request $request,
        string $correlationId,
        array $architecture,
        int $status,
        float $duration,
        array $spans,
        ?string $exception,
        ?string $initialUserId,
        ?string $initialCompanyId,
    ): void {
        try {
            $currentUserId = $request->user()?->getAuthIdentifier();
            $currentCompanyId = $request->session()->get('new_system.company_id')
                ?? $request->session()->get('id_empresa');

            $this->auditRecorder->record(new HttpRequestAuditEntry(
                correlationId: $correlationId,
                httpMethod: strtoupper($request->method()),
                path: $this->auditedPath($request),
                route: $request->route()?->getName(),
                controller: $request->route()?->getActionName(),
                status: $status,
                durationMilliseconds: $duration,
                userId: $currentUserId !== null ? (string) $currentUserId : $initialUserId,
                companyId: $currentCompanyId !== null ? (string) $currentCompanyId : $initialCompanyId,
                ip: $request->ip(),
                userAgent: $request->userAgent(),
                architecture: $architecture,
                spans: $spans,
                exception: $exception,
            ));
        } catch (Throwable) {
        }
    }

    private function auditedPath(Request $request): string
    {
        return $request->route()?->uri() ?? $request->path();
    }
}
