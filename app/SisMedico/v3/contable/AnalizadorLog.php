<?php

namespace Sis_medico\v3\contable;

/**
 * Resume una muestra del log de Laravel sin exponer trazas ni datos sensibles.
 */
class AnalizadorLog
{
    /**
     * @param string $contenido
     * @param int    $limite
     *
     * @return array
     */
    public static function resumir($contenido, $limite = 20)
    {
        $resultado = [
            'total' => 0,
            'unicos' => 0,
            'errores' => [],
        ];

        if (!is_string($contenido) || trim($contenido) === '') {
            return $resultado;
        }

        $patron = '/^\[([^\]\r\n]+)\]\s+([A-Za-z0-9_-]+)\.(ERROR|CRITICAL|ALERT|EMERGENCY):\s*(.*?)(?=^\[[^\]\r\n]+\]\s+[A-Za-z0-9_-]+\.[A-Z]+:|\z)/ms';
        preg_match_all($patron, $contenido, $coincidencias, PREG_SET_ORDER);

        $resultado['total'] = count($coincidencias);
        $agrupados = [];

        foreach (array_reverse($coincidencias) as $coincidencia) {
            $bloque = isset($coincidencia[4]) ? $coincidencia[4] : '';
            $mensaje = self::extraerMensaje($bloque);
            $origen = self::extraerOrigen($bloque);
            $nivel = strtoupper($coincidencia[3]);
            $firma = self::firma($nivel, $mensaje, $origen);
            $diagnostico = self::diagnosticar($mensaje);

            if (!isset($agrupados[$firma])) {
                $agrupados[$firma] = [
                    'firma' => $firma,
                    'fecha' => self::limpiarTexto($coincidencia[1], 40),
                    'entorno' => self::limpiarTexto($coincidencia[2], 30),
                    'nivel' => $nivel,
                    'mensaje' => $mensaje,
                    'origen' => $origen['archivo'],
                    'linea' => $origen['linea'],
                    'ocurrencias' => 0,
                    'categoria' => $diagnostico['categoria'],
                    'causa_probable' => $diagnostico['causa_probable'],
                    'sugerencia' => $diagnostico['sugerencia'],
                    'prioridad' => $diagnostico['prioridad'],
                ];
            }

            $agrupados[$firma]['ocurrencias']++;
        }

        $resultado['unicos'] = count($agrupados);
        $resultado['errores'] = array_slice(array_values($agrupados), 0, max(1, (int) $limite));

        return $resultado;
    }

    private static function extraerMensaje($bloque)
    {
        $lineas = preg_split('/\r\n|\r|\n/', trim($bloque));
        $mensaje = isset($lineas[0]) ? trim($lineas[0]) : 'Error sin descripcion';

        // Laravel agrega el contexto JSON al final de la primera linea.
        $mensaje = preg_replace('/\s+\{"(?:exception|user|context)"\s*:.*$/i', '', $mensaje);
        $mensaje = self::protegerDatos($mensaje);

        return self::limpiarTexto($mensaje !== '' ? $mensaje : 'Error sin descripcion', 500);
    }

    private static function extraerOrigen($bloque)
    {
        $origen = ['archivo' => null, 'linea' => null];
        $patrones = [
            '/\bin\s+((?:[A-Za-z]:)?[\\\\\/][^\r\n:]+\.php):(\d+)/i',
            '/((?:[A-Za-z]:)?[\\\\\/][^\r\n:]+\.php)(?::|\()(\d+)\)?/i',
        ];

        foreach ($patrones as $patron) {
            if (preg_match($patron, $bloque, $coincidencia)) {
                $origen['archivo'] = self::rutaSegura($coincidencia[1]);
                $origen['linea'] = (int) $coincidencia[2];
                break;
            }
        }

        return $origen;
    }

    private static function rutaSegura($ruta)
    {
        $ruta = str_replace('\\', '/', trim($ruta));
        foreach (['/app/', '/routes/', '/resources/', '/vendor/'] as $segmento) {
            $posicion = stripos($ruta, $segmento);
            if ($posicion !== false) {
                return ltrim(substr($ruta, $posicion), '/');
            }
        }

        return basename($ruta);
    }

    private static function protegerDatos($texto)
    {
        $texto = preg_replace('/\b(Bearer)\s+[A-Za-z0-9._~+\/-]+=*/i', '$1 [PROTEGIDO]', $texto);
        $texto = preg_replace(
            '/(["\']?(?:password|passwd|token|secret|authorization|api[_-]?key)["\']?\s*[:=]\s*["\']?)([^"\',\s}\]]+)/i',
            '$1[PROTEGIDO]',
            $texto
        );

        return $texto;
    }

    private static function diagnosticar($mensaje)
    {
        $reglas = [
            [
                'patron' => '/SQLSTATE\[42S02\]|Base table or view not found|Table .* doesn.t exist/i',
                'categoria' => 'Base de datos',
                'causa' => 'La tabla consultada no existe en el esquema activo o su nombre no coincide.',
                'sugerencia' => 'Confirmar la base y el nombre de tabla en el servidor; revisar la migracion pendiente y ejecutarla primero en pruebas y con respaldo.',
                'prioridad' => 'Alta',
            ],
            [
                'patron' => '/SQLSTATE\[23000\]|Integrity constraint violation|Duplicate entry|foreign key constraint/i',
                'categoria' => 'Integridad de datos',
                'causa' => 'Una clave unica o relacion de base de datos rechazo la operacion.',
                'sugerencia' => 'Identificar el dato duplicado o la referencia ausente, validar antes de guardar y conservar la operacion dentro de una transaccion.',
                'prioridad' => 'Alta',
            ],
            [
                'patron' => '/SQLSTATE\[HY000\].*(2002|2006)|Connection refused|server has gone away|No connection could be made/i',
                'categoria' => 'Conexion MySQL',
                'causa' => 'MySQL no respondio o la conexion se interrumpio.',
                'sugerencia' => 'Verificar servicio MySQL, host, puerto y credenciales del .env del servidor; despues revisar tiempos de espera y consultas extensas.',
                'prioridad' => 'Alta',
            ],
            [
                'patron' => '/Deadlock found|Lock wait timeout exceeded/i',
                'categoria' => 'Concurrencia MySQL',
                'causa' => 'Dos operaciones compiten por los mismos registros o mantienen bloqueos demasiado tiempo.',
                'sugerencia' => 'Acortar la transaccion, mantener el mismo orden de actualizacion, revisar indices y considerar reintento controlado para deadlocks.',
                'prioridad' => 'Alta',
            ],
            [
                'patron' => '/Undefined (index|offset)|Undefined array key/i',
                'categoria' => 'Datos de entrada',
                'causa' => 'El codigo intenta leer una posicion que no siempre llega en la solicitud o consulta.',
                'sugerencia' => 'Validar la entrada y comprobar isset o array_key_exists antes de usarla; definir un valor predeterminado cuando sea valido.',
                'prioridad' => 'Media',
            ],
            [
                'patron' => '/Trying to get property .*non-object|Attempt to read property .*null|Call to a member function .* on null/i',
                'categoria' => 'Resultado nulo',
                'causa' => 'Una consulta o relacion devolvio null y el codigo la utilizo como objeto.',
                'sugerencia' => 'Comprobar el resultado antes de acceder; usar firstOrFail/findOrFail solo cuando corresponda y mostrar una respuesta controlada.',
                'prioridad' => 'Media',
            ],
            [
                'patron' => '/Class .* not found|Target .* is not instantiable/i',
                'categoria' => 'Clase o namespace',
                'causa' => 'La clase no se encuentra, su namespace no coincide o el autoload del servidor esta desactualizado.',
                'sugerencia' => 'Revisar namespace, use y mayusculas del archivo; en el servidor regenerar autoload solo dentro del procedimiento de despliegue aprobado.',
                'prioridad' => 'Alta',
            ],
            [
                'patron' => '/Call to undefined (method|function)/i',
                'categoria' => 'Metodo inexistente',
                'causa' => 'El codigo llama un metodo ausente o incompatible con la version instalada.',
                'sugerencia' => 'Confirmar la clase real y la API disponible en Laravel 5.4/PHP 7.1.33; corregir primero con una prueba que reproduzca el caso.',
                'prioridad' => 'Alta',
            ],
            [
                'patron' => '/Permission denied|failed to open stream|is not writable/i',
                'categoria' => 'Archivos y permisos',
                'causa' => 'El proceso PHP no puede leer o escribir el archivo o directorio indicado.',
                'sugerencia' => 'Verificar existencia, propietario y permisos de storage/bootstrap/cache en el servidor sin aplicar permisos globales inseguros.',
                'prioridad' => 'Alta',
            ],
            [
                'patron' => '/TokenMismatchException|CSRF token mismatch/i',
                'categoria' => 'Sesion y CSRF',
                'causa' => 'El formulario no envio un token valido o la sesion expiro.',
                'sugerencia' => 'Confirmar @csrf en el formulario, dominio/HTTPS de la cookie y expiracion de sesion; no excluir la ruta de CSRF.',
                'prioridad' => 'Alta',
            ],
            [
                'patron' => '/Allowed memory size .* exhausted|Maximum execution time .* exceeded/i',
                'categoria' => 'Rendimiento',
                'causa' => 'La operacion procesa mas datos o tarda mas de lo permitido.',
                'sugerencia' => 'Medir la consulta, seleccionar solo columnas necesarias y procesar por lotes/paginacion; aumentar limites solo despues de optimizar.',
                'prioridad' => 'Media',
            ],
        ];

        foreach ($reglas as $regla) {
            if (preg_match($regla['patron'], $mensaje)) {
                return [
                    'categoria' => $regla['categoria'],
                    'causa_probable' => $regla['causa'],
                    'sugerencia' => $regla['sugerencia'],
                    'prioridad' => $regla['prioridad'],
                ];
            }
        }

        return [
            'categoria' => 'Aplicacion',
            'causa_probable' => 'El mensaje no coincide con una causa comun identificable de forma segura.',
            'sugerencia' => 'Reproducir el caso en pruebas, revisar la traza completa en el servidor desde la primera excepcion y corregir con una prueba de regresion.',
            'prioridad' => 'Por evaluar',
        ];
    }

    private static function firma($nivel, $mensaje, array $origen)
    {
        $normalizado = strtolower($mensaje);
        $normalizado = preg_replace('/[0-9a-f]{8}-[0-9a-f-]{27,}/i', '#uuid#', $normalizado);
        $normalizado = preg_replace('/\b\d+\b/', '#', $normalizado);
        $normalizado = preg_replace('/\s+/', ' ', $normalizado);

        return sha1($nivel . '|' . $normalizado . '|' . (string) $origen['archivo']);
    }

    private static function limpiarTexto($texto, $limite)
    {
        $texto = trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', (string) $texto));
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($texto, 'UTF-8') > $limite ? mb_substr($texto, 0, $limite - 3, 'UTF-8') . '...' : $texto;
        }

        return strlen($texto) > $limite ? substr($texto, 0, $limite - 3) . '...' : $texto;
    }
}
