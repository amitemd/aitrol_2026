<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class VickyCoreOutbox
{
    private const FLUJOS = [
        'sales', 'purchasing', 'finance', 'payments', 'inventory',
        'accounting', 'tax', 'secretarial', 'nursing', 'medical',
    ];

    private const OPERACIONES = ['status', 'list', 'summarize', 'lookup', 'catalog', 'read'];

    public static function directorioPrivado(): string
    {
        $padre = dirname(base_path());

        return ! app()->environment('testing') && PHP_OS_FAMILY === 'Windows' && strcasecmp(basename($padre), 'htdocs') === 0
            ? dirname($padre).DIRECTORY_SEPARATOR.'vicky-private'
            : storage_path('app/private');
    }

    public function guardar(array $evento): void
    {
        $directorio = $this->directorioPendiente();
        File::ensureDirectoryExists($directorio, 0700);
        $nombre = sprintf('%013d-%s.json', (int) (microtime(true) * 1000), $evento['requestId']);
        File::replace($directorio.DIRECTORY_SEPARATOR.$nombre, json_encode($evento, JSON_THROW_ON_ERROR), 0600);
    }

    /** @param callable(array): bool $enviar */
    public function despachar(callable $enviar): void
    {
        $directorio = $this->directorioPendiente();
        $bloqueo = @fopen($directorio.DIRECTORY_SEPARATOR.'despacho.lock', 'c');
        if ($bloqueo === false) {
            return;
        }
        try {
            if (! flock($bloqueo, LOCK_EX | LOCK_NB)) {
                return;
            }
            $pendientes = glob($directorio.DIRECTORY_SEPARATOR.'*.json') ?: [];
            $intentos = 0;
            foreach ($pendientes as $ruta) {
                if ($intentos === 3) {
                    break;
                }
                $evento = $this->eventoGuardado($ruta);
                if ($evento === null) {
                    continue;
                }
                $intentos++;
                if (! $enviar($evento)) {
                    break;
                }
                File::delete($ruta);
            }
        } finally {
            fclose($bloqueo);
        }
    }

    private function directorioPendiente(): string
    {
        return self::directorioPrivado().DIRECTORY_SEPARATOR.'aitrol_2026-outbox';
    }

    private function eventoGuardado(string $ruta): ?array
    {
        if (is_link($ruta)) {
            return null;
        }
        $contenido = @file_get_contents($ruta, false, null, 0, 2049);
        $evento = is_string($contenido) && strlen($contenido) <= 2048
            ? json_decode($contenido, true, depth: 3) : null;
        $requeridos = ['requestId', 'tenantKey', 'status', 'durationMs'];
        if (! is_array($evento) || array_diff($requeridos, array_keys($evento)) !== []
            || array_diff(array_keys($evento), [...$requeridos, 'workflow', 'operation']) !== []
            || ! is_string($evento['requestId']) || ! Str::isUuid($evento['requestId'])
            || ! is_string($evento['tenantKey']) || ! preg_match('/\A[a-f0-9]{64}\z/', $evento['tenantKey'])
            || ! in_array($evento['status'], ['completed', 'failed', 'rejected'], true)
            || ! is_int($evento['durationMs']) || $evento['durationMs'] < 0 || $evento['durationMs'] > 120_000) {
            return null;
        }
        if (count($evento) !== 4 && (! in_array($evento['workflow'] ?? null, self::FLUJOS, true)
            || ! in_array($evento['operation'] ?? null, self::OPERACIONES, true)
            || $evento['status'] === 'rejected')) {
            return null;
        }

        return $evento;
    }
}
