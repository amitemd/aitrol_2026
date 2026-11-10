<?php

namespace App\Support\Errors;

use App\Exceptions\UserFacingException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\ViewException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class SupportIncidentRenderer
{
    private const INCIDENT_ATTRIBUTE = '_aitrol_support_incident_id';

    private const LOGGED_ATTRIBUTE = '_aitrol_support_incident_logged';

    /**
     * @return array<string, bool|int|string|null>
     */
    public function context(Throwable $exception): array
    {
        $request = app()->bound('request') ? app(Request::class) : null;

        return $this->buildContext($exception, $request);
    }

    public function render(Throwable $exception, Request $request): ?Response
    {
        if ($exception instanceof AuthenticationException || $exception instanceof ValidationException) {
            return null;
        }

        $status = $this->status($exception);
        $incidentId = $this->incidentId($request);
        $presentation = $this->presentation($exception, $status);

        $this->logRenderedIncident($exception, $request, $status);

        $payload = [
            'message' => $presentation['message'],
            'incident_id' => $incidentId,
            'status' => $status,
        ];

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json($payload, $status);
        }

        return response()->view('errors.support', [
            ...$presentation,
            'incidentId' => $incidentId,
            'occurredAt' => now()->format('d/m/Y H:i:s'),
            'requestPath' => '/'.ltrim($request->path(), '/'),
            'status' => $status,
        ], $status);
    }

    private function status(Throwable $exception): int
    {
        if ($exception instanceof UserFacingException) {
            return min(599, max(400, $exception->status));
        }

        if ($this->isActiveHospitalConfigurationError($exception)) {
            return 409;
        }

        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        return 500;
    }

    /**
     * @return array{title: string, message: string, action: string, icon: string}
     */
    private function presentation(Throwable $exception, int $status): array
    {
        if ($exception instanceof UserFacingException) {
            return [
                'title' => $exception->title,
                'message' => $exception->publicMessage,
                'action' => $exception->recommendedAction,
                'icon' => 'configuration',
            ];
        }

        if ($this->isActiveHospitalConfigurationError($exception)) {
            return [
                'title' => __('new_system.support_errors.active_hospital_title'),
                'message' => __('new_system.support_errors.active_hospital_message'),
                'action' => __('new_system.support_errors.active_hospital_action'),
                'icon' => 'configuration',
            ];
        }

        if ($exception instanceof QueryException) {
            return [
                'title' => __('new_system.support_errors.database_title'),
                'message' => __('new_system.support_errors.database_message'),
                'action' => __('new_system.support_errors.database_action'),
                'icon' => 'database',
            ];
        }

        if ($exception instanceof ViewException) {
            return [
                'title' => __('new_system.support_errors.view_title'),
                'message' => __('new_system.support_errors.view_message'),
                'action' => __('new_system.support_errors.view_action'),
                'icon' => 'screen',
            ];
        }

        $key = match ($status) {
            400 => 'bad_request',
            403 => 'forbidden',
            404 => 'not_found',
            405 => 'method_not_allowed',
            419 => 'session_expired',
            429 => 'too_many_requests',
            503 => 'unavailable',
            default => 'unexpected',
        };

        return [
            'title' => __("new_system.support_errors.{$key}_title"),
            'message' => __("new_system.support_errors.{$key}_message"),
            'action' => __("new_system.support_errors.{$key}_action"),
            'icon' => $status === 404 ? 'search' : ($status === 419 ? 'clock' : 'warning'),
        ];
    }

    private function incidentId(?Request $request): string
    {
        if ($request?->attributes->has(self::INCIDENT_ATTRIBUTE)) {
            return (string) $request->attributes->get(self::INCIDENT_ATTRIBUTE);
        }

        $incidentId = 'INC-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        $request?->attributes->set(self::INCIDENT_ATTRIBUTE, $incidentId);

        return $incidentId;
    }

    private function isActiveHospitalConfigurationError(Throwable $exception): bool
    {
        return $exception instanceof \InvalidArgumentException
            && Str::contains(Str::lower($exception->getMessage()), ['hospital activo', 'active hospital']);
    }

    /**
     * @return array<string, bool|int|string|null>
     */
    private function buildContext(Throwable $exception, ?Request $request): array
    {
        return [
            'incident_id' => $this->incidentId($request),
            'exception_class' => $exception::class,
            'exception_file' => $exception->getFile(),
            'exception_line' => $exception->getLine(),
            'http_method' => $request?->method(),
            'request_path' => $request ? '/'.ltrim($request->path(), '/') : null,
            'route_name' => $this->safeContextValue(fn () => $request?->route()?->getName()),
            'company_id' => $this->safeContextValue(
                fn () => $request?->hasSession() ? $request->session()->get('id_empresa') : null,
            ),
            'request_user_id' => $this->safeContextValue(fn () => $request?->user()?->getAuthIdentifier()),
            'request_ip' => $request?->ip(),
        ];
    }

    private function safeContextValue(callable $resolver): bool|int|string|null
    {
        try {
            $value = $resolver();
        } catch (Throwable) {
            return null;
        }

        return is_bool($value) || is_int($value) || is_string($value) ? $value : null;
    }

    private function logRenderedIncident(Throwable $exception, Request $request, int $status): void
    {
        if ((bool) $request->attributes->get(self::LOGGED_ATTRIBUTE, false)) {
            return;
        }

        $request->attributes->set(self::LOGGED_ATTRIBUTE, true);

        try {
            Log::log($status >= 500 ? 'error' : ($status === 404 ? 'notice' : 'warning'), 'Support incident rendered.', [
                ...$this->buildContext($exception, $request),
                'http_status' => $status,
                'exception_message' => $exception->getMessage(),
            ]);
        } catch (Throwable) {
        }
    }
}
