<?php

namespace App\Modules\SupportIntegrations\Infrastructure\Persistence;

use App\Modules\SupportIntegrations\Application\Contracts\AuditTrailRepository;
use App\Modules\SupportIntegrations\Application\Data\AuditFilters;
use App\Modules\SupportIntegrations\Application\Data\AuditTrailPage;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

final class LaravelAuditTrailRepository implements AuditTrailRepository
{
    public function read(AuditFilters $filters, int $activityPage, int $technicalPage, int $perPage): AuditTrailPage
    {
        $warnings = [];
        $activitiesToday = 0;
        $technicalToday = 0;
        $usersToday = 0;

        try {
            if (Schema::hasTable('log_usuario')) {
                $activityQuery = $this->activityQuery($filters);
                $activitiesToday = (clone $activityQuery)
                    ->where('log.created_at', '>=', now()->startOfDay())
                    ->count('log.id');
                $activities = $activityQuery
                    ->orderByDesc('log.id')
                    ->paginate($perPage, ['*'], 'activity_page', $activityPage)
                    ->through(fn (object $record): object => $this->sanitizeActivity($record));
            } else {
                $warnings[] = 'La tabla log_usuario no existe en la base activa.';
                $activities = $this->emptyPaginator('activity_page', $activityPage, $perPage);
            }
        } catch (Throwable $exception) {
            report($exception);
            $warnings[] = 'No fue posible consultar log_usuario.';
            $activities = $this->emptyPaginator('activity_page', $activityPage, $perPage);
        }

        try {
            if (Schema::hasTable('new_db_auditorias')) {
                $technicalQuery = $this->technicalQuery($filters);
                $technicalToday = (clone $technicalQuery)
                    ->where('audit.created_at_auditoria', '>=', now()->startOfDay())
                    ->count('audit.id_auditoria');
                $usersToday = (clone $technicalQuery)
                    ->where('audit.created_at_auditoria', '>=', now()->startOfDay())
                    ->distinct()
                    ->count('audit.id_usuario_auditoria');
                $technicalEvents = $technicalQuery
                    ->orderByDesc('audit.id_auditoria')
                    ->paginate($perPage, ['*'], 'technical_page', $technicalPage)
                    ->through(fn (object $record): object => $this->sanitizeTechnicalEvent($record));
            } else {
                $warnings[] = 'La tabla new_db_auditorias no existe en la base activa.';
                $technicalEvents = $this->emptyPaginator('technical_page', $technicalPage, $perPage);
            }
        } catch (Throwable $exception) {
            report($exception);
            $warnings[] = 'No fue posible consultar new_db_auditorias.';
            $technicalEvents = $this->emptyPaginator('technical_page', $technicalPage, $perPage);
        }

        return new AuditTrailPage(
            activities: $activities,
            technicalEvents: $technicalEvents,
            summary: [
                'activities_today' => $activitiesToday,
                'technical_today' => $technicalToday,
                'users_today' => $usersToday,
            ],
            warnings: $warnings,
        );
    }

    private function activityQuery(AuditFilters $filters): Builder
    {
        $query = DB::table('log_usuario as log')
            ->leftJoin('users as audit_user', 'audit_user.id', '=', 'log.id_usuario')
            ->select([
                'log.id',
                'log.id_usuario',
                'log.ip_usuario',
                'log.descripcion',
                'log.dato_ant1',
                'log.dato1',
                'log.dato_ant2',
                'log.dato2',
                'log.dato_ant3',
                'log.dato3',
                'log.dato_ant4',
                'log.dato4',
                'log.created_at',
                'audit_user.nombre1',
                'audit_user.apellido1',
                'audit_user.email',
            ]);

        $this->applyCommonFilters($query, $filters, 'log.created_at', 'log.id_usuario');

        if ($filters->search !== null) {
            $pattern = '%'.$this->escapeLike($filters->search).'%';
            $query->where(function (Builder $searchQuery) use ($pattern): void {
                $searchQuery
                    ->where('log.descripcion', 'like', $pattern)
                    ->orWhere('log.dato_ant1', 'like', $pattern)
                    ->orWhere('log.dato1', 'like', $pattern)
                    ->orWhere('log.dato_ant2', 'like', $pattern)
                    ->orWhere('log.dato2', 'like', $pattern)
                    ->orWhere('log.dato_ant3', 'like', $pattern)
                    ->orWhere('log.dato3', 'like', $pattern)
                    ->orWhere('log.dato_ant4', 'like', $pattern)
                    ->orWhere('log.dato4', 'like', $pattern)
                    ->orWhere('audit_user.nombre1', 'like', $pattern)
                    ->orWhere('audit_user.apellido1', 'like', $pattern)
                    ->orWhere('audit_user.email', 'like', $pattern);
            });
        }

        return $query;
    }

    private function technicalQuery(AuditFilters $filters): Builder
    {
        $query = DB::table('new_db_auditorias as audit')
            ->leftJoin('users as audit_user', 'audit_user.id', '=', 'audit.id_usuario_auditoria')
            ->select([
                'audit.*',
                'audit_user.nombre1',
                'audit_user.apellido1',
                'audit_user.email',
            ]);

        $this->applyCommonFilters($query, $filters, 'audit.created_at_auditoria', 'audit.id_usuario_auditoria');

        if ($filters->companyId !== null) {
            $query->where('audit.id_empresa_auditoria', $filters->companyId);
        }

        if ($filters->search !== null) {
            $pattern = '%'.$this->escapeLike($filters->search).'%';
            $query->where(function (Builder $searchQuery) use ($pattern): void {
                $searchQuery
                    ->where('audit.controlador_auditoria', 'like', $pattern)
                    ->orWhere('audit.submodulo_auditoria', 'like', $pattern)
                    ->orWhere('audit.modelo_auditoria', 'like', $pattern)
                    ->orWhere('audit.vista_auditoria', 'like', $pattern)
                    ->orWhere('audit.observacion_auditoria', 'like', $pattern)
                    ->orWhere('audit_user.nombre1', 'like', $pattern)
                    ->orWhere('audit_user.apellido1', 'like', $pattern)
                    ->orWhere('audit_user.email', 'like', $pattern);
            });
        }

        return $query;
    }

    private function applyCommonFilters(
        Builder $query,
        AuditFilters $filters,
        string $dateColumn,
        string $userColumn,
    ): void {
        if ($filters->userId !== null) {
            $query->where($userColumn, $filters->userId);
        }

        if ($filters->fromDate !== null) {
            $query->where($dateColumn, '>=', $filters->fromDate.' 00:00:00');
        }

        if ($filters->toDate !== null) {
            $query->where($dateColumn, '<=', $filters->toDate.' 23:59:59');
        }
    }

    private function sanitizeActivity(object $record): object
    {
        foreach (['descripcion', 'dato_ant1', 'dato1', 'dato_ant2', 'dato2', 'dato_ant3', 'dato3', 'dato_ant4', 'dato4'] as $field) {
            $record->{$field} = $this->redact((string) ($record->{$field} ?? ''));
        }

        return $record;
    }

    private function sanitizeTechnicalEvent(object $record): object
    {
        foreach (['query_auditoria', 'parametros_auditoria', 'observacion_auditoria', 'obj_auditoria', 'agente_auditoria'] as $field) {
            $record->{$field} = Str::limit($this->redact((string) ($record->{$field} ?? '')), 12000, '…');
        }

        return $record;
    }

    private function redact(string $value): string
    {
        $patterns = [
            '/\b(Bearer)\s+[A-Za-z0-9._~+\/-]+=*/i' => '$1 [PROTEGIDO]',
            '/(["\']?(?:password|passwd|token|secret|authorization|cookie|api[_-]?key)["\']?\s*[:=]\s*["\']?)([^"\',\s}\]]+)/i' => '$1[PROTEGIDO]',
        ];

        return preg_replace(array_keys($patterns), array_values($patterns), $value) ?? $value;
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\\%_');
    }

    private function emptyPaginator(string $pageName, int $page, int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            [],
            0,
            $perPage,
            $page,
            [
                'path' => route('observability.audit.index'),
                'pageName' => $pageName,
            ],
        );
    }
}
