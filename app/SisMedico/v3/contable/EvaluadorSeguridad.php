<?php

namespace Sis_medico\v3\contable;

/**
 * Evaluacion informativa y de solo lectura para el entorno v3.
 */
class EvaluadorSeguridad
{
    public static function evaluar(array $contexto)
    {
        $rutas = isset($contexto['rutas']) ? $contexto['rutas'] : '';
        $csrf = isset($contexto['csrf']) ? $contexto['csrf'] : '';
        $kernel = isset($contexto['kernel']) ? $contexto['kernel'] : '';
        $controlador = isset($contexto['controlador']) ? $contexto['controlador'] : '';
        $consoleKernel = isset($contexto['console_kernel']) ? $contexto['console_kernel'] : '';
        $entorno = isset($contexto['entorno']) ? strtolower((string) $contexto['entorno']) : '';
        $produccion = in_array($entorno, ['production', 'produccion', 'prod'], true);
        $controles = [];

        $grupo = strpos($rutas, "['middleware' => ['operacion.v3', 'general']");
        $rutaPanel = strpos($rutas, "Route::get('v3/operacion-monitoreo'");
        $protegido = $grupo !== false && $rutaPanel !== false && $rutaPanel > $grupo;
        $controles[] = self::control(
            'Acceso al diagnostico',
            $protegido ? 'correcto' : 'critico',
            $protegido ? 'La ruta utiliza operacion.v3 y general.' : 'No se confirmo el middleware del panel.',
            $protegido ? 'Conservar ambos middleware.' : 'Proteger la ruta antes de publicar el diagnostico.',
            'routes/v3/contable/web.php',
            'Alta'
        );

        $autorizacion = strpos($controlador, 'Auth::check()') !== false && strpos($controlador, 'id_tipo_usuario') !== false;
        $controles[] = self::control(
            'Autorizacion administrativa',
            $autorizacion ? 'correcto' : 'critico',
            $autorizacion ? 'El controlador rechaza usuarios no administradores.' : 'No se confirmo una autorizacion dentro del controlador.',
            $autorizacion ? 'Mantener la comprobacion y evolucionar luego a policies.' : 'Agregar autorizacion explicita sin depender solo del menu.',
            'OperacionMonitoreoController.php',
            'Alta'
        );

        $csrfActivo = strpos($kernel, 'VerifyCsrfToken::class') !== false;
        $v3Excluido = preg_match('/["\'][^"\']*v3[^"\']*["\']/', $csrf) === 1;
        $controles[] = self::control(
            'Proteccion CSRF',
            $csrfActivo && !$v3Excluido ? 'correcto' : 'critico',
            $csrfActivo && !$v3Excluido ? 'CSRF esta activo y no se detectaron exclusiones v3.' : 'CSRF no esta activo o existe una exclusion que menciona v3.',
            'No excluir formularios v3; usar token CSRF en cada POST.',
            'Http/Kernel.php y VerifyCsrfToken.php',
            'Alta'
        );

        $debug = !empty($contexto['debug']);
        $controles[] = self::control(
            'Modo de depuracion',
            $debug ? ($produccion ? 'critico' : 'revisar') : 'correcto',
            $debug ? 'APP_DEBUG esta activo en ' . ($entorno ?: 'el entorno actual') . '.' : 'APP_DEBUG esta desactivado.',
            $debug ? 'Desactivarlo en produccion desde el archivo .env del servidor.' : 'Conservarlo desactivado en produccion.',
            'Configuracion efectiva del servidor',
            $produccion ? 'Alta' : 'Media'
        );

        $clave = isset($contexto['app_key']) ? trim((string) $contexto['app_key']) : '';
        $controles[] = self::control(
            'Clave de aplicacion',
            $clave !== '' ? 'correcto' : 'critico',
            $clave !== '' ? 'APP_KEY esta configurada.' : 'APP_KEY no esta disponible en el entorno.',
            $clave !== '' ? 'No exponerla ni versionarla.' : 'Configurar APP_KEY unicamente en el .env del servidor.',
            'Configuracion efectiva del servidor',
            'Alta'
        );

        $httpOnly = !empty($contexto['session_http_only']);
        $controles[] = self::control(
            'Cookie HttpOnly',
            $httpOnly ? 'correcto' : 'revisar',
            $httpOnly ? 'JavaScript no puede leer la cookie de sesion.' : 'La cookie de sesion no tiene HttpOnly.',
            $httpOnly ? 'Conservar SESSION_HTTP_ONLY habilitado.' : 'Habilitar HttpOnly en la configuracion de sesion.',
            'config/session.php',
            'Alta'
        );

        $secure = !empty($contexto['session_secure']);
        $estadoCookie = $secure ? 'correcto' : ($produccion ? 'critico' : 'revisar');
        $controles[] = self::control(
            'Cookie solo por HTTPS',
            $estadoCookie,
            $secure ? 'La cookie requiere HTTPS.' : 'SESSION_SECURE_COOKIE esta desactivado.',
            $secure ? 'Conservar HTTPS en todo el sitio.' : 'Activarlo en el .env de produccion cuando HTTPS este confirmado.',
            'Configuracion efectiva de session.secure',
            $produccion ? 'Alta' : 'Media'
        );

        $antesDelGrupo = $grupo !== false ? substr($rutas, 0, $grupo) : $rutas;
        $rutasPublicas = preg_match_all('/Route::(?:get|post|match|any)\s*\(/', $antesDelGrupo, $coincidencias);
        $controles[] = self::control(
            'Inventario de rutas publicas',
            $rutasPublicas > 0 ? 'revisar' : 'correcto',
            $rutasPublicas > 0 ? $rutasPublicas . ' rutas aparecen antes del grupo protegido.' : 'No se detectaron rutas antes del grupo protegido.',
            $rutasPublicas > 0 ? 'Confirmar una por una si son publicas por diseno y aplicar limite de solicitudes donde corresponda.' : 'Mantener las rutas funcionales dentro del grupo protegido.',
            'routes/v3/contable/web.php',
            'Alta'
        );

        $rutasMixtas = preg_match_all('/Route::match\s*\(\s*\[[^\]]*["\']get["\'][^\]]*["\']post["\']/i', $rutas, $coincidencias);
        $controles[] = self::control(
            'Separacion GET y POST',
            $rutasMixtas > 0 ? 'revisar' : 'correcto',
            $rutasMixtas > 0 ? $rutasMixtas . ' rutas aceptan GET y POST en el mismo punto de entrada.' : 'No se detectaron rutas mixtas.',
            $rutasMixtas > 0 ? 'Migrar gradualmente escrituras a POST y dejar GET solo para consultas, con pruebas previas.' : 'Conservar la separacion por metodo HTTP.',
            'routes/v3/contable/web.php',
            'Media'
        );

        $cargaArchivos = strpos($rutas, 'subirArchivo') !== false;
        $controles[] = self::control(
            'Carga de archivos',
            $cargaArchivos ? 'revisar' : 'correcto',
            $cargaArchivos ? 'v3 contiene al menos una ruta de carga de archivos.' : 'No se detectaron rutas de carga en el archivo principal.',
            $cargaArchivos ? 'Verificar MIME real, extension permitida, tamano, nombre generado y ruta final antes de modificarla.' : 'Aplicar validacion estricta si se agrega una carga.',
            'Ruta v3/utilidades/subirArchivo',
            'Alta'
        );

        $tlsDesactivado = stripos($consoleKernel, '--no-check-certificate') !== false;
        $shellConcatenado = strpos($consoleKernel, "schedule->exec('wget ' .") !== false;
        $schedulerSeguro = !$tlsDesactivado && !$shellConcatenado;
        $controles[] = self::control(
            'Descargas del programador',
            $schedulerSeguro ? 'correcto' : 'revisar',
            $schedulerSeguro ? 'No se detecto desactivacion de TLS ni URL concatenada en wget.' : 'El scheduler desactiva la validacion TLS o concatena una URL en un comando shell.',
            $schedulerSeguro ? 'Conservar validacion TLS.' : 'Reemplazar gradualmente por un comando dedicado, validar la URL contra una lista permitida y retirar --no-check-certificate despues de probar certificados.',
            'app/Console/Kernel.php',
            'Alta'
        );

        $puntos = 0;
        foreach ($controles as $control) {
            $puntos += $control['estado'] === 'correcto' ? 100 : ($control['estado'] === 'revisar' ? 50 : 0);
        }

        return [
            'porcentaje' => count($controles) > 0 ? (int) round($puntos / count($controles)) : 0,
            'correctos' => self::contar($controles, 'correcto'),
            'revisar' => self::contar($controles, 'revisar'),
            'criticos' => self::contar($controles, 'critico'),
            'controles' => $controles,
        ];
    }

    private static function control($nombre, $estado, $detalle, $recomendacion, $evidencia, $prioridad)
    {
        return compact('nombre', 'estado', 'detalle', 'recomendacion', 'evidencia', 'prioridad');
    }

    private static function contar(array $controles, $estado)
    {
        $total = 0;
        foreach ($controles as $control) {
            if ($control['estado'] === $estado) {
                $total++;
            }
        }

        return $total;
    }
}
