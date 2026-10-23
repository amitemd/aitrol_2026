<?php

namespace Sis_medico\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerificarSaludV3 extends Command
{
    protected $signature = 'v3:health-check {--json : Devuelve el resultado en formato JSON}';

    protected $description = 'Verifica dependencias operativas de v3 sin modificar datos';

    public function handle()
    {
        $resultados = [];
        $baseDisponible = false;
        $inicio = microtime(true);

        try {
            DB::connection()->getPdo();
            $baseDisponible = true;
            $resultados[] = $this->resultado('mysql', true, 'Disponible en ' . round((microtime(true) - $inicio) * 1000, 2) . ' ms');
        } catch (\Exception $e) {
            $resultados[] = $this->resultado('mysql', false, 'No disponible');
        }

        $auditoriaDisponible = false;
        if ($baseDisponible) {
            try {
                $auditoriaDisponible = DB::getSchemaBuilder()->hasTable('new_db_auditorias');
            } catch (\Exception $e) {
                $auditoriaDisponible = false;
            }
        }
        $resultados[] = $this->resultado('auditoria', $auditoriaDisponible, $auditoriaDisponible ? 'Tabla disponible' : 'Tabla no disponible');

        $directorioLogs = storage_path('logs');
        $logsDisponibles = is_dir($directorioLogs) && is_writable($directorioLogs);
        $resultados[] = $this->resultado('logs', $logsDisponibles, $logsDisponibles ? 'Directorio escribible' : 'Directorio no escribible');

        $debugSeguro = !config('app.debug');
        $resultados[] = $this->resultado('debug', $debugSeguro, $debugSeguro ? 'Desactivado' : 'Activo');

        $total = @disk_total_space(base_path());
        $libre = @disk_free_space(base_path());
        $minimoDisco = (float) config('monitoreo_v3.minimum_free_disk_percent', 15);
        $porcentajeLibre = ($total !== false && $libre !== false && $total > 0) ? round(($libre / $total) * 100, 1) : null;
        $discoCorrecto = $porcentajeLibre === null || $porcentajeLibre >= $minimoDisco;
        $resultados[] = $this->resultado('disco', $discoCorrecto, $porcentajeLibre === null ? 'No calculable' : $porcentajeLibre . '% libre');

        $archivoLog = $directorioLogs . DIRECTORY_SEPARATOR . 'laravel.log';
        $maximoLogMb = (float) config('monitoreo_v3.maximum_log_size_mb', 100);
        $tamanoLogMb = is_file($archivoLog) ? round(filesize($archivoLog) / 1048576, 2) : 0;
        $logControlado = $tamanoLogMb <= $maximoLogMb;
        $resultados[] = $this->resultado('tamano_log', $logControlado, $tamanoLogMb . ' MB de ' . $maximoLogMb . ' MB');

        $correctas = 0;
        foreach ($resultados as $resultado) {
            if ($resultado['correcto']) {
                $correctas++;
            }
        }

        $saludable = $correctas === count($resultados);
        $salida = [
            'estado' => $saludable ? 'saludable' : 'atencion',
            'fecha' => date('c'),
            'correctas' => $correctas,
            'total' => count($resultados),
            'verificaciones' => $resultados,
        ];

        if ($this->option('json')) {
            $this->line(json_encode($salida));
        } else {
            $filas = [];
            foreach ($resultados as $resultado) {
                $filas[] = [
                    $resultado['componente'],
                    $resultado['correcto'] ? 'OK' : 'ATENCION',
                    $resultado['detalle'],
                ];
            }
            $this->table(['Componente', 'Estado', 'Detalle'], $filas);
            $this->line('Resultado: ' . strtoupper($salida['estado']) . ' (' . $correctas . '/' . count($resultados) . ')');
        }

        return $saludable ? 0 : 1;
    }

    private function resultado($componente, $correcto, $detalle)
    {
        return [
            'componente' => $componente,
            'correcto' => (bool) $correcto,
            'detalle' => $detalle,
        ];
    }
}
