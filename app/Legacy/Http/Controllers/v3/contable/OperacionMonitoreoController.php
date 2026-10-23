<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\AnalizadorLog;
use Sis_medico\v3\contable\EvaluadorSeguridad;
use Sis_medico\v3\contable\EvaluadorMantenimiento;
use Sis_medico\v3\contable\SeguimientoErrores;

class OperacionMonitoreoController extends Controller
{
    /**
     * Panel de salud y actividad operativa de la version v3.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (!Auth::check() || (int) Auth::user()->id_tipo_usuario !== 1) {
            abort(403, 'No tiene permisos para consultar el monitoreo operativo.');
        }

        $inicioBase = microtime(true);
        $baseDisponible = false;
        $latenciaBase = null;
        $errorBase = null;

        try {
            DB::connection()->getPdo();
            $baseDisponible = true;
            $latenciaBase = round((microtime(true) - $inicioBase) * 1000, 2);
        } catch (\Exception $e) {
            $errorBase = 'No fue posible establecer conexion con MySQL';
            $this->registrarError('v3.monitoreo.mysql_no_disponible', $e);
        }

        $auditoria = $this->resumenAuditoria($baseDisponible);
        $log = $this->resumenLog();
        $almacenamiento = $this->resumenAlmacenamiento();
        $seguridad = $this->resumenSeguridad();
        $mantenimiento = $this->resumenMantenimiento($log);
        $minimoDisco = (float) config('monitoreo_v3.minimum_free_disk_percent', 15);

        $verificaciones = [
            [
                'nombre' => 'Conexion MySQL',
                'correcto' => $baseDisponible,
                'detalle' => $baseDisponible ? 'Disponible en ' . $latenciaBase . ' ms' : 'No disponible: ' . $errorBase,
                'icono' => 'fas fa-database',
            ],
            [
                'nombre' => 'Registro de eventos',
                'correcto' => $log['directorio_escribible'],
                'detalle' => $log['directorio_escribible'] ? 'Directorio de logs disponible' : 'El directorio de logs no existe o no permite escritura',
                'icono' => 'fas fa-file-alt',
            ],
            [
                'nombre' => 'Tamano del log',
                'correcto' => $log['tamano_controlado'],
                'detalle' => $log['tamano'] . ' de ' . $log['maximo'] . ' permitidos',
                'icono' => 'fas fa-weight-hanging',
            ],
            [
                'nombre' => 'Auditoria funcional',
                'correcto' => $auditoria['disponible'],
                'detalle' => $auditoria['disponible'] ? 'Tabla new_db_auditorias disponible' : $auditoria['error'],
                'icono' => 'fas fa-user-shield',
            ],
            [
                'nombre' => 'Modo de depuracion',
                'correcto' => !config('app.debug'),
                'detalle' => config('app.debug') ? 'APP_DEBUG esta activo; debe desactivarse en produccion' : 'Desactivado correctamente',
                'icono' => 'fas fa-bug',
            ],
            [
                'nombre' => 'Espacio en disco',
                'correcto' => $almacenamiento['porcentaje_libre'] === null || $almacenamiento['porcentaje_libre'] >= $minimoDisco,
                'detalle' => $almacenamiento['detalle'],
                'icono' => 'fas fa-hdd',
            ],
        ];

        $correctas = 0;
        foreach ($verificaciones as $verificacion) {
            if ($verificacion['correcto']) {
                $correctas++;
            }
        }

        $data = [
            'controlador' => 'operacion-monitoreo',
            'carpeta' => 'contable',
            'submodulo_' => 'operacion-monitoreo',
            'title' => 'Operacion y monitoreo | Sistema v3',
            'vista' => 'v3.contable.monitoreo.view_operacion',
            'obseraudi' => 'Consulto el panel de operacion y monitoreo del sistema v3',
            'fechaRevision' => date('d/m/Y H:i:s'),
            'entorno' => config('app.env'),
            'versionPhp' => PHP_VERSION,
            'versionLaravel' => app()->version(),
            'latenciaBase' => $latenciaBase,
            'verificaciones' => $verificaciones,
            'verificacionesCorrectas' => $correctas,
            'totalVerificaciones' => count($verificaciones),
            'porcentajeSalud' => round(($correctas / count($verificaciones)) * 100),
            'auditoriaOperacion' => $auditoria,
            'resumenLog' => $log,
            'seguridadOperacion' => $seguridad,
            'mantenimientoOperacion' => $mantenimiento,
            'umbralesOperacion' => [
                'peticion_lenta_ms' => (int) config('monitoreo_v3.slow_request_ms', 2000),
                'consulta_lenta_ms' => (int) config('monitoreo_v3.slow_query_ms', 1000),
                'dias_auditoria' => max(1, (int) config('monitoreo_v3.audit_window_days', 7)),
                'disco_minimo' => $minimoDisco,
                'log_maximo_mb' => (float) config('monitoreo_v3.maximum_log_size_mb', 100),
            ],
        ];

        $data['contenido'] = view($data['vista'], $data);
        $newData = array_merge(config('data', []), $data);
        config(['data' => $newData]);

        return view('v3.layouts.view_child');
    }

    public function marcarErrorCorregido(Request $request, $firma)
    {
        if (!Auth::check() || (int) Auth::user()->id_tipo_usuario !== 1) {
            abort(403, 'No tiene permisos para actualizar el seguimiento de errores.');
        }

        if (!preg_match('/^[a-f0-9]{40}$/', $firma)) {
            abort(404);
        }

        $nota = trim((string) $request->input('nota', ''));
        if (strlen($nota) > 500) {
            return redirect()->route('v3.operacion-monitoreo')
                ->with('operacion_error', 'La nota no puede superar 500 caracteres.');
        }

        $log = $this->resumenLog();
        $errorSeleccionado = null;
        foreach ($log['errores_detalle'] as $error) {
            if (isset($error['firma']) && hash_equals($error['firma'], $firma)) {
                $errorSeleccionado = $error;
                break;
            }
        }

        if ($errorSeleccionado === null) {
            return redirect()->route('v3.operacion-monitoreo')
                ->with('operacion_error', 'El error ya fue corregido o ya no aparece en la muestra reciente.');
        }

        $usuario = trim((string) Auth::user()->nombre1 . ' ' . (string) Auth::user()->apellido1);
        if ($usuario === '') {
            $usuario = 'Usuario ' . Auth::user()->id;
        }

        try {
            SeguimientoErrores::marcar(
                $this->archivoSeguimientoErrores(),
                $errorSeleccionado,
                Auth::user()->id,
                $usuario,
                $nota
            );
        } catch (\Exception $e) {
            $this->registrarError('v3.monitoreo.seguimiento_no_disponible', $e);
            return redirect()->route('v3.operacion-monitoreo')
                ->with('operacion_error', 'No fue posible guardar el estado corregido; revise permisos de storage/app/v3.');
        }

        return redirect()->route('v3.operacion-monitoreo')
            ->with('operacion_exito', 'El error fue marcado como corregido. Si vuelve a ocurrir, se reabrira automaticamente.');
    }

    private function resumenAuditoria($baseDisponible)
    {
        $resumen = [
            'disponible' => false,
            'error' => 'No fue posible consultar la auditoria',
            'hoy' => 0,
            'ultimos_siete_dias' => 0,
            'usuarios_hoy' => 0,
            'consultas_lentas' => 0,
            'ultima_actividad' => null,
            'modulos' => [],
            'actividades' => [],
        ];

        if (!$baseDisponible) {
            return $resumen;
        }

        try {
            if (!DB::getSchemaBuilder()->hasTable('new_db_auditorias')) {
                $resumen['error'] = 'La tabla new_db_auditorias no existe';
                return $resumen;
            }

            $empresa = session('id_empresa');
            $hoy = date('Y-m-d 00:00:00');
            $diasAuditoria = max(1, (int) config('monitoreo_v3.audit_window_days', 7));
            $inicioVentana = date('Y-m-d 00:00:00', strtotime('-' . ($diasAuditoria - 1) . ' days'));
            $limiteConsulta = (int) config('monitoreo_v3.slow_query_ms', 1000);
            $consulta = DB::table('new_db_auditorias')
                ->where('id_empresa_auditoria', $empresa);

            $resumen['disponible'] = true;
            $resumen['error'] = '';
            $resumen['hoy'] = (clone $consulta)->where('created_at_auditoria', '>=', $hoy)->count();
            $resumen['ultimos_siete_dias'] = (clone $consulta)->where('created_at_auditoria', '>=', $inicioVentana)->count();
            $resumen['usuarios_hoy'] = (clone $consulta)->where('created_at_auditoria', '>=', $hoy)
                ->distinct()->count('id_usuario_auditoria');
            $resumen['consultas_lentas'] = (clone $consulta)->where('created_at_auditoria', '>=', $hoy)
                ->where('tiempo_consulta_auditoria', '>=', $limiteConsulta)->count();
            $resumen['ultima_actividad'] = (clone $consulta)->max('created_at_auditoria');

            $resumen['modulos'] = (clone $consulta)
                ->where('created_at_auditoria', '>=', $inicioVentana)
                ->select('controlador_auditoria', DB::raw('COUNT(*) as total'))
                ->groupBy('controlador_auditoria')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();

            $resumen['actividades'] = DB::table('new_db_auditorias as a')
                ->leftJoin('users as u', 'a.id_usuario_auditoria', '=', 'u.id')
                ->where('a.id_empresa_auditoria', $empresa)
                ->orderBy('a.id_auditoria', 'desc')
                ->limit(8)
                ->get([
                    'a.controlador_auditoria',
                    'a.submodulo_auditoria',
                    'a.observacion_auditoria',
                    'a.tiempo_consulta_auditoria',
                    'a.created_at_auditoria',
                    'u.nombre1',
                    'u.apellido1',
                ]);
        } catch (\Exception $e) {
            $resumen['disponible'] = false;
            $resumen['error'] = 'No fue posible consultar la informacion de auditoria';
            $this->registrarError('v3.monitoreo.auditoria_no_disponible', $e);
        }

        return $resumen;
    }

    private function resumenLog()
    {
        $directorio = storage_path('logs');
        $archivo = $directorio . DIRECTORY_SEPARATOR . 'laravel.log';
        $resumen = [
            'directorio_escribible' => is_dir($directorio) && is_writable($directorio),
            'archivo_existe' => is_file($archivo),
            'tamano' => '0 B',
            'tamano_bytes' => 0,
            'maximo' => config('monitoreo_v3.maximum_log_size_mb', 100) . ' MB',
            'tamano_controlado' => true,
            'ultima_modificacion' => null,
            'errores_recientes' => 0,
            'errores_unicos' => 0,
            'errores_pendientes' => 0,
            'errores_corregidos' => 0,
            'errores_detalle' => [],
            'errores_corregidos_detalle' => [],
            'advertencias_recientes' => 0,
        ];

        if (!$resumen['archivo_existe'] || !is_readable($archivo)) {
            return $resumen;
        }

        $tamano = filesize($archivo);
        $resumen['tamano'] = $this->formatearBytes($tamano);
        $resumen['tamano_bytes'] = $tamano;
        $resumen['tamano_controlado'] = $tamano <= ((float) config('monitoreo_v3.maximum_log_size_mb', 100) * 1048576);
        $resumen['ultima_modificacion'] = date('d/m/Y H:i:s', filemtime($archivo));
        $bytes = min($tamano, max(1024, (int) config('monitoreo_v3.log_tail_bytes', 1048576)));
        $contenido = '';
        $manejador = fopen($archivo, 'rb');
        if ($manejador !== false) {
            if ($bytes > 0) {
                fseek($manejador, -$bytes, SEEK_END);
                $contenido = fread($manejador, $bytes);
            }
            fclose($manejador);
        }

        $analisis = AnalizadorLog::resumir($contenido, 20);
        $clasificacion = SeguimientoErrores::clasificar($analisis['errores'], $this->archivoSeguimientoErrores());
        $resumen['errores_recientes'] = $analisis['total'];
        $resumen['errores_unicos'] = $analisis['unicos'];
        $resumen['errores_pendientes'] = count($clasificacion['pendientes']);
        $resumen['errores_corregidos'] = count($clasificacion['corregidos']);
        $resumen['errores_detalle'] = $clasificacion['pendientes'];
        $resumen['errores_corregidos_detalle'] = $clasificacion['corregidos'];
        $resumen['advertencias_recientes'] = preg_match_all('/\.WARNING:/', $contenido, $coincidencias);

        return $resumen;
    }

    private function archivoSeguimientoErrores()
    {
        return storage_path('app/v3/errores_corregidos.json');
    }

    private function resumenAlmacenamiento()
    {
        $total = @disk_total_space(base_path());
        $libre = @disk_free_space(base_path());
        if ($total === false || $libre === false || $total == 0) {
            return ['porcentaje_libre' => null, 'detalle' => 'No fue posible calcular el espacio disponible'];
        }

        $porcentaje = round(($libre / $total) * 100, 1);
        return [
            'porcentaje_libre' => $porcentaje,
            'detalle' => $this->formatearBytes($libre) . ' libres (' . $porcentaje . '%)',
        ];
    }

    private function resumenSeguridad()
    {
        $archivos = [
            'rutas' => base_path('routes/v3/contable/web.php'),
            'csrf' => app_path('Http/Middleware/VerifyCsrfToken.php'),
            'kernel' => app_path('Http/Kernel.php'),
            'controlador' => __FILE__,
            'console_kernel' => app_path('Console/Kernel.php'),
        ];
        $contenido = [];

        foreach ($archivos as $nombre => $archivo) {
            $contenido[$nombre] = is_readable($archivo) ? (string) file_get_contents($archivo) : '';
        }

        return EvaluadorSeguridad::evaluar([
            'rutas' => $contenido['rutas'],
            'csrf' => $contenido['csrf'],
            'kernel' => $contenido['kernel'],
            'controlador' => $contenido['controlador'],
            'console_kernel' => $contenido['console_kernel'],
            'entorno' => config('app.env'),
            'debug' => config('app.debug'),
            'app_key' => config('app.key'),
            'session_http_only' => config('session.http_only'),
            'session_secure' => config('session.secure'),
        ]);
    }

    private function resumenMantenimiento(array $log)
    {
        $marcador = trim((string) config('monitoreo_v3.backup_marker_path', ''));
        if ($marcador !== '' && !preg_match('/^(?:[A-Za-z]:[\\\\\/]|\/)/', $marcador)) {
            $marcador = base_path($marcador);
        }

        $horasRespaldo = null;
        if ($marcador !== '' && is_file($marcador) && is_readable($marcador)) {
            $modificacion = filemtime($marcador);
            if ($modificacion !== false) {
                $horasRespaldo = round(max(0, time() - $modificacion) / 3600, 1);
            }
        }

        $consoleKernel = app_path('Console/Kernel.php');
        $contenidoScheduler = is_readable($consoleKernel) ? (string) file_get_contents($consoleKernel) : '';

        return EvaluadorMantenimiento::evaluar([
            'entorno' => config('app.env'),
            'storage_escribible' => is_dir(storage_path()) && is_writable(storage_path()),
            'logs_escribibles' => $log['directorio_escribible'],
            'canal_log' => config('app.log', 'single'),
            'nivel_log' => config('app.log_level', 'debug'),
            'log_controlado' => $log['tamano_controlado'],
            'tamano_log' => $log['tamano'],
            'maximo_log' => $log['maximo'],
            'config_cache' => is_file(base_path('bootstrap/cache/config.php')),
            'respaldo_configurado' => $marcador !== '',
            'respaldo_horas' => $horasRespaldo,
            'maximo_respaldo_horas' => max(1, (int) config('monitoreo_v3.maximum_backup_age_hours', 24)),
            'scheduler_definido' => strpos($contenidoScheduler, '$schedule->') !== false,
        ]);
    }

    private function formatearBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    private function registrarError($mensaje, \Exception $e)
    {
        try {
            Log::error($mensaje, ['tipo' => get_class($e)]);
        } catch (\Exception $errorLog) {
            // El panel debe seguir disponible aunque falle el archivo de logs.
        }
    }
}
