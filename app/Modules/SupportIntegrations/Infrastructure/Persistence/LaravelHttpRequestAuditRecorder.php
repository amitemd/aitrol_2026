<?php

namespace App\Modules\SupportIntegrations\Infrastructure\Persistence;

use App\Modules\SupportIntegrations\Application\Contracts\HttpRequestAuditRecorder;
use App\Modules\SupportIntegrations\Application\Data\HttpRequestAuditEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;
use Throwable;

final class LaravelHttpRequestAuditRecorder implements HttpRequestAuditRecorder
{
    public function record(HttpRequestAuditEntry $entry): void
    {
        $this->recordFunctionalActivity($entry);
        $this->recordTechnicalActivity($entry);
    }

    private function recordFunctionalActivity(HttpRequestAuditEntry $entry): void
    {
        if ($entry->userId === null || $entry->userId === '') {
            return;
        }

        try {
            DB::table('log_usuario')->insert([
                'id_usuario' => Str::limit($entry->userId, 13, ''),
                'ip_usuario' => Str::limit((string) $entry->ip, 40, ''),
                'descripcion' => Str::limit($this->activityDescription($entry), 255, '…'),
                'dato_ant1' => null,
                'dato1' => Str::limit($entry->path, 255, '…'),
                'dato_ant2' => null,
                'dato2' => Str::limit($this->controllerLabel($entry), 255, '…'),
                'dato_ant3' => null,
                'dato3' => Str::limit($this->resultLabel($entry), 255, '…'),
                'dato_ant4' => null,
                'dato4' => $this->encode([
                    'correlation_id' => $entry->correlationId,
                    'route' => $entry->route,
                    'company_id' => $entry->companyId,
                    'exception' => $entry->exception,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $this->reportPersistenceFailure('log_usuario', $entry, $exception);
        }
    }

    private function recordTechnicalActivity(HttpRequestAuditEntry $entry): void
    {
        try {
            DB::table('new_db_auditorias')->insert([
                'controlador_auditoria' => Str::limit($this->controllerLabel($entry), 255, '…'),
                'modelo_auditoria' => Str::limit($this->componentLabel($entry), 255, '…'),
                'submodulo_auditoria' => Str::limit($this->moduleLabel($entry), 255, '…'),
                'vista_auditoria' => Str::limit($entry->route ?? $entry->path, 255, '…'),
                'query_auditoria' => $entry->httpMethod.' /'.$entry->path,
                'parametros_auditoria' => $this->encode([
                    'correlation_id' => $entry->correlationId,
                    'status' => $entry->status,
                    'route' => $entry->route,
                    'architecture' => $entry->architecture,
                ]),
                'tiempo_consulta_auditoria' => number_format($entry->durationMilliseconds, 2, '.', '').' ms',
                'id_usuario_auditoria' => $entry->userId,
                'id_empresa_auditoria' => $entry->companyId,
                'ip_auditoria' => Str::limit((string) $entry->ip, 255, ''),
                'navegador_auditoria' => $this->browserName($entry->userAgent),
                'sistema_auditoria' => $this->operatingSystemName($entry->userAgent),
                'version_navegador_auditoria' => null,
                'latitud_auditoria' => null,
                'longitud_auditoria' => null,
                'agente_auditoria' => $entry->userAgent,
                'observacion_auditoria' => $this->resultLabel($entry),
                'obj_auditoria' => $this->encode([
                    'exception' => $entry->exception,
                    'spans' => $entry->spans,
                ], 60_000),
                'created_at_auditoria' => now(),
                'updated_at_auditoria' => now(),
            ]);
        } catch (Throwable $exception) {
            $this->reportPersistenceFailure('new_db_auditorias', $entry, $exception);
        }
    }

    private function activityDescription(HttpRequestAuditEntry $entry): string
    {
        $target = $entry->route ?? $entry->path;

        return "ACCESO {$entry->httpMethod}: {$target}";
    }

    private function controllerLabel(HttpRequestAuditEntry $entry): string
    {
        if ($entry->architecture['controller'] === null) {
            return $entry->controller ?? 'Acción sin controlador';
        }

        return $entry->architecture['controller']
            .($entry->architecture['method'] !== null ? '@'.$entry->architecture['method'] : '');
    }

    private function componentLabel(HttpRequestAuditEntry $entry): string
    {
        $components = collect($entry->architecture['components'])
            ->map(function (array $component): string {
                $implementation = $component['implementation'] !== null
                    ? ' → '.$component['implementation']
                    : '';

                return $component['type'].': '.$component['name'].$implementation;
            })
            ->implode(' | ');

        return $components !== '' ? $components : 'Sin dependencias inspeccionables';
    }

    private function moduleLabel(HttpRequestAuditEntry $entry): string
    {
        $route = $entry->route ?? str_replace('/', '.', $entry->path);

        return Str::headline((string) Str::of($route)->before('.'));
    }

    private function resultLabel(HttpRequestAuditEntry $entry): string
    {
        $result = "HTTP {$entry->status} · ".number_format($entry->durationMilliseconds, 2, '.', '').' ms';

        return $entry->exception !== null ? $result.' · '.$entry->exception : $result;
    }

    private function browserName(?string $userAgent): string
    {
        $agent = strtolower((string) $userAgent);

        return match (true) {
            str_contains($agent, 'edg/') => 'Microsoft Edge',
            str_contains($agent, 'firefox/') => 'Mozilla Firefox',
            str_contains($agent, 'chrome/') => 'Google Chrome',
            str_contains($agent, 'safari/') => 'Safari',
            default => 'No identificado',
        };
    }

    private function operatingSystemName(?string $userAgent): string
    {
        $agent = strtolower((string) $userAgent);

        return match (true) {
            str_contains($agent, 'windows') => 'Windows',
            str_contains($agent, 'android') => 'Android',
            str_contains($agent, 'iphone'), str_contains($agent, 'ipad') => 'iOS',
            str_contains($agent, 'mac os') => 'macOS',
            str_contains($agent, 'linux') => 'Linux',
            default => 'No identificado',
        };
    }

    /**
     * @param  array<string, mixed>  $value
     */
    private function encode(array $value, int $maximumBytes = 60_000): string
    {
        try {
            $encoded = json_encode(
                $value,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE,
            );
        } catch (JsonException) {
            return '{"error":"No fue posible serializar la evidencia de auditoría"}';
        }

        if (strlen($encoded) <= $maximumBytes) {
            return $encoded;
        }

        return mb_strcut($encoded, 0, $maximumBytes - strlen('…'), 'UTF-8').'…';
    }

    private function reportPersistenceFailure(
        string $table,
        HttpRequestAuditEntry $entry,
        Throwable $exception,
    ): void {
        if ($this->isMissingAuditTable($table, $exception)) {
            return;
        }

        try {
            Log::warning('No se pudo persistir una entrada de auditoría HTTP.', [
                'audit_table' => $table,
                'correlation_id' => $entry->correlationId,
                'route' => $entry->route,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        } catch (Throwable) {
        }
    }

    private function isMissingAuditTable(string $table, Throwable $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return (str_contains($message, '1146') || str_contains($message, 'no such table'))
            && str_contains($message, strtolower($table));
    }
}
