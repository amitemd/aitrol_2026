<?php

namespace Sis_medico\v3\contable;

/**
 * Controles preventivos que no modifican archivos ni servicios.
 */
class EvaluadorMantenimiento
{
    public static function evaluar(array $contexto)
    {
        $entorno = strtolower((string) self::valor($contexto, 'entorno', ''));
        $produccion = in_array($entorno, ['production', 'produccion', 'prod'], true);
        $controles = [];

        $controles[] = self::control(
            'Escritura de almacenamiento',
            self::valor($contexto, 'storage_escribible', false) ? 'correcto' : 'critico',
            self::valor($contexto, 'storage_escribible', false) ? 'storage permite escritura al proceso PHP.' : 'storage no existe o no permite escritura.',
            'Verificar propietario y permisos del usuario PHP sin usar permisos globales 777.',
            'Diaria',
            'Alta'
        );

        $controles[] = self::control(
            'Directorio de logs',
            self::valor($contexto, 'logs_escribibles', false) ? 'correcto' : 'critico',
            self::valor($contexto, 'logs_escribibles', false) ? 'El registro de eventos esta disponible.' : 'Laravel no puede escribir en storage/logs.',
            'Restaurar escritura antes de investigar incidentes para no perder evidencia.',
            'Diaria',
            'Alta'
        );

        $canalLog = strtolower((string) self::valor($contexto, 'canal_log', 'single'));
        $controles[] = self::control(
            'Rotacion del log',
            $canalLog === 'daily' ? 'correcto' : 'revisar',
            $canalLog === 'daily' ? 'APP_LOG utiliza archivos diarios.' : 'APP_LOG utiliza el canal ' . ($canalLog ?: 'no identificado') . '.',
            $canalLog === 'daily' ? 'Conservar una retencion acorde al servidor.' : 'Evaluar APP_LOG=daily en produccion y definir retencion externa antes de eliminar archivos.',
            'Semanal',
            'Media'
        );

        $nivelLog = strtolower((string) self::valor($contexto, 'nivel_log', 'debug'));
        $nivelAdecuado = !$produccion || !in_array($nivelLog, ['debug', 'info'], true);
        $controles[] = self::control(
            'Nivel de registro',
            $nivelAdecuado ? 'correcto' : 'revisar',
            'Nivel efectivo: ' . ($nivelLog ?: 'no identificado') . '.',
            $nivelAdecuado ? 'Conservar detalle suficiente sin registrar datos sensibles.' : 'Evaluar APP_LOG_LEVEL=warning o error en produccion despues de confirmar la observabilidad.',
            'Mensual',
            'Media'
        );

        $logControlado = (bool) self::valor($contexto, 'log_controlado', true);
        $controles[] = self::control(
            'Crecimiento del log',
            $logControlado ? 'correcto' : 'critico',
            (string) self::valor($contexto, 'tamano_log', 'No disponible') . ' de ' . (string) self::valor($contexto, 'maximo_log', 'limite no disponible') . '.',
            $logControlado ? 'Revisar la tendencia y mantener rotacion.' : 'Respaldar la evidencia, activar rotacion y liberar espacio con un procedimiento autorizado.',
            'Diaria',
            'Alta'
        );

        $cacheConfiguracion = (bool) self::valor($contexto, 'config_cache', false);
        $estadoCache = !$produccion || $cacheConfiguracion ? 'correcto' : 'revisar';
        $controles[] = self::control(
            'Cache de configuracion',
            $estadoCache,
            $cacheConfiguracion ? 'La configuracion esta compilada.' : 'No se detecto bootstrap/cache/config.php.',
            $cacheConfiguracion ? 'Reconstruirla despues de cada cambio autorizado del .env.' : 'En produccion ejecutar config:cache durante el despliegue, despues de validar el .env.',
            'Por despliegue',
            'Media'
        );

        $respaldoConfigurado = (bool) self::valor($contexto, 'respaldo_configurado', false);
        $respaldoHoras = self::valor($contexto, 'respaldo_horas', null);
        $maximoRespaldo = (int) self::valor($contexto, 'maximo_respaldo_horas', 24);
        $respaldoReciente = $respaldoConfigurado && $respaldoHoras !== null && $respaldoHoras <= $maximoRespaldo;
        $estadoRespaldo = !$respaldoConfigurado ? 'revisar' : ($respaldoReciente ? 'correcto' : 'critico');
        $detalleRespaldo = !$respaldoConfigurado
            ? 'No se configuro una evidencia verificable de ultimo respaldo.'
            : ($respaldoHoras === null ? 'El marcador de respaldo no existe.' : 'Ultima evidencia hace ' . $respaldoHoras . ' horas.');
        $controles[] = self::control(
            'Respaldo verificable',
            $estadoRespaldo,
            $detalleRespaldo,
            'El proceso de respaldo debe actualizar un marcador solo despues de validar que el archivo puede restaurarse.',
            'Diaria',
            'Alta'
        );

        $schedulerDefinido = (bool) self::valor($contexto, 'scheduler_definido', false);
        $controles[] = self::control(
            'Tareas programadas',
            $schedulerDefinido ? 'revisar' : 'critico',
            $schedulerDefinido ? 'Existen tareas en Console/Kernel; el panel no puede confirmar el cron del sistema operativo.' : 'No se detectaron tareas programadas.',
            $schedulerDefinido ? 'Verificar en el servidor que schedule:run se ejecute cada minuto y conservar evidencia de la ultima ejecucion.' : 'Definir el responsable y mecanismo de tareas antes de depender de procesos automaticos.',
            'Diaria',
            'Alta'
        );

        $controles[] = self::control(
            'Plataforma heredada controlada',
            'revisar',
            'El sistema permanece fijado en PHP 7.1.33 y Laravel 5.4.',
            'Mantener aislamiento, respaldos y pruebas; planificar modernizacion por modulos sin actualizar directamente en produccion.',
            'Trimestral',
            'Alta'
        );

        return self::resumen($controles);
    }

    private static function control($nombre, $estado, $detalle, $recomendacion, $frecuencia, $prioridad)
    {
        return compact('nombre', 'estado', 'detalle', 'recomendacion', 'frecuencia', 'prioridad');
    }

    private static function resumen(array $controles)
    {
        $puntos = 0;
        $totales = ['correcto' => 0, 'revisar' => 0, 'critico' => 0];
        foreach ($controles as $control) {
            $totales[$control['estado']]++;
            $puntos += $control['estado'] === 'correcto' ? 100 : ($control['estado'] === 'revisar' ? 50 : 0);
        }

        return [
            'porcentaje' => count($controles) > 0 ? (int) round($puntos / count($controles)) : 0,
            'correctos' => $totales['correcto'],
            'revisar' => $totales['revisar'],
            'criticos' => $totales['critico'],
            'controles' => $controles,
        ];
    }

    private static function valor(array $contexto, $clave, $predeterminado)
    {
        return array_key_exists($clave, $contexto) ? $contexto[$clave] : $predeterminado;
    }
}
