<?php

namespace App\Modules\SupportIntegrations\Application\Queries;

use App\Modules\SupportIntegrations\Application\Contracts\AuditTrailRepository;
use App\Modules\SupportIntegrations\Application\Contracts\SystemLogRepository;
use App\Modules\SupportIntegrations\Application\Data\AuditFilters;
use App\Modules\SupportIntegrations\Application\Data\SystemLogFilters;
use App\Modules\SupportIntegrations\Application\Data\SystemObservabilityReport;
use DateTimeImmutable;

final readonly class BuildSystemObservabilityReport
{
    public function __construct(
        private SystemLogRepository $logs,
        private AuditTrailRepository $audit,
    ) {}

    public function handle(SystemLogFilters $filters, int $page, int $perPage): SystemObservabilityReport
    {
        $logs = $this->logs->read($filters, $page, $perPage);
        $timeline = $logs->activityMap->flameGraph->timeline;
        $processes = $timeline['processes'];

        if ($processes === []) {
            return new SystemObservabilityReport($logs, []);
        }

        $startDate = substr((string) $timeline['start_at'], 0, 10);
        $endDate = substr((string) $timeline['end_at'], 0, 10);
        $limit = min(1000, max(50, (int) config('observability.audit_correlation_limit', 500)));
        $audit = $this->audit->read(
            new AuditFilters(
                fromDate: $startDate !== '' ? $startDate : null,
                toDate: $endDate !== '' ? $endDate : null,
            ),
            1,
            1,
            $limit,
        );

        return new SystemObservabilityReport(
            $logs,
            $this->correlate(
                $processes,
                $audit->technicalEvents->items(),
                $audit->activities->items(),
                $audit->warnings,
            ),
        );
    }

    /**
     * @param  list<array<string, mixed>>  $processes
     * @param  list<object>  $technicalEvents
     * @param  list<object>  $activities
     * @param  list<string>  $warnings
     * @return array<string, array{technical: list<array<string, mixed>>, activities: list<array<string, mixed>>, warnings: list<string>}>
     */
    private function correlate(array $processes, array $technicalEvents, array $activities, array $warnings): array
    {
        $result = [];

        foreach ($processes as $process) {
            $processTimestamp = (float) ($process['timestamp_ms'] ?? 0.0);
            $duration = max(0.0, (float) ($process['duration_ms'] ?? 0.0));
            $tolerance = max(2000.0, $duration + 1000.0);

            $result[(string) $process['id']] = [
                'technical' => $this->matchingTechnicalEvents($process, $technicalEvents, $processTimestamp, $tolerance),
                'activities' => $this->matchingActivities($process, $activities, $processTimestamp, $tolerance),
                'warnings' => $warnings,
            ];
        }

        return $result;
    }

    /** @param list<object> $events @return list<array<string, mixed>> */
    private function matchingTechnicalEvents(array $process, array $events, float $timestamp, float $tolerance): array
    {
        $controller = strtolower((string) ($process['controller'] ?? ''));
        $controllerBasename = strtolower(class_basename(explode('@', $controller, 2)[0]));

        if ($controller === '' && empty($process['route']) && empty($process['correlation_id'])) {
            return [];
        }

        return collect($events)
            ->filter(function (object $event) use ($process, $timestamp, $tolerance, $controller, $controllerBasename): bool {
                if (! $this->withinWindow($event->created_at_auditoria ?? null, $timestamp, $tolerance)) {
                    return false;
                }

                if (! $this->sameIdentity($process, $event->id_usuario_auditoria ?? null, $event->id_empresa_auditoria ?? null)) {
                    return false;
                }

                $auditController = strtolower((string) ($event->controlador_auditoria ?? ''));

                return $controller === '' || $auditController === ''
                    || str_contains($controller, $auditController)
                    || ($controllerBasename !== '' && str_contains($auditController, $controllerBasename));
            })
            ->take(8)
            ->map(fn (object $event): array => [
                'id' => $event->id_auditoria ?? null,
                'date' => $event->created_at_auditoria ?? null,
                'controller' => $event->controlador_auditoria ?? null,
                'module' => $event->submodulo_auditoria ?? null,
                'model' => $event->modelo_auditoria ?? null,
                'view' => $event->vista_auditoria ?? null,
                'query_time' => $event->tiempo_consulta_auditoria ?? null,
                'observation' => $event->observacion_auditoria ?? null,
                'match' => 'Ventana temporal, identidad y controlador',
            ])
            ->values()
            ->all();
    }

    /** @param list<object> $activities @return list<array<string, mixed>> */
    private function matchingActivities(array $process, array $activities, float $timestamp, float $tolerance): array
    {
        if (($process['user_id'] ?? null) === null) {
            return [];
        }

        return collect($activities)
            ->filter(fn (object $activity): bool => $this->withinWindow($activity->created_at ?? null, $timestamp, $tolerance)
                && $this->sameIdentity($process, $activity->id_usuario ?? null, null))
            ->take(8)
            ->map(fn (object $activity): array => [
                'id' => $activity->id ?? null,
                'date' => $activity->created_at ?? null,
                'description' => $activity->descripcion ?? null,
                'user' => trim((string) (($activity->nombre1 ?? '').' '.($activity->apellido1 ?? ''))),
                'match' => 'Ventana temporal e identidad de usuario',
            ])
            ->values()
            ->all();
    }

    private function sameIdentity(array $process, mixed $userId, mixed $companyId): bool
    {
        $processUser = $process['user_id'] ?? null;
        $processCompany = $process['company_id'] ?? null;

        if ($processUser !== null && $userId !== null && (string) $processUser !== (string) $userId) {
            return false;
        }

        return $processCompany === null || $companyId === null || (string) $processCompany === (string) $companyId;
    }

    private function withinWindow(mixed $date, float $timestamp, float $tolerance): bool
    {
        if (! is_string($date) || $date === '') {
            return false;
        }

        try {
            $eventTimestamp = (float) (new DateTimeImmutable($date))->format('U.u') * 1000;
        } catch (\Exception) {
            return false;
        }

        return abs($eventTimestamp - $timestamp) <= $tolerance;
    }
}
