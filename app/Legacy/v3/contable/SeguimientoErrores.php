<?php

namespace Sis_medico\v3\contable;

/**
 * Guarda el estado de errores corregidos sin modificar laravel.log.
 */
class SeguimientoErrores
{
    public static function clasificar(array $errores, $archivo)
    {
        $registros = self::leer($archivo);
        $pendientes = [];
        $corregidos = [];
        $firmasActuales = [];

        foreach ($errores as $error) {
            if (empty($error['firma'])) {
                $pendientes[] = $error;
                continue;
            }

            $firma = $error['firma'];
            $firmasActuales[$firma] = true;
            $registro = isset($registros[$firma]) ? $registros[$firma] : null;

            if ($registro && !self::ocurrioDespues($error['fecha'], $registro['corregido_en'])) {
                $corregidos[] = array_merge($error, self::datosCorreccion($registro));
            } else {
                $error['reabierto'] = $registro !== null;
                $pendientes[] = $error;
            }
        }

        foreach ($registros as $firma => $registro) {
            if (!isset($firmasActuales[$firma])) {
                $corregidos[] = self::errorHistorico($registro);
            }
        }

        usort($corregidos, function ($a, $b) {
            return strcmp($b['corregido_en'], $a['corregido_en']);
        });

        return [
            'pendientes' => $pendientes,
            'corregidos' => array_slice($corregidos, 0, 50),
        ];
    }

    public static function marcar($archivo, array $error, $usuarioId, $usuario, $nota = '')
    {
        $directorio = dirname($archivo);
        if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
            throw new \RuntimeException('No fue posible crear el directorio de seguimiento.');
        }

        $manejador = fopen($archivo, 'c+');
        if ($manejador === false) {
            throw new \RuntimeException('No fue posible abrir el seguimiento de errores.');
        }

        try {
            if (!flock($manejador, LOCK_EX)) {
                throw new \RuntimeException('No fue posible bloquear el seguimiento de errores.');
            }

            rewind($manejador);
            $contenido = stream_get_contents($manejador);
            $registros = self::decodificar($contenido);
            $firma = $error['firma'];
            $registros[$firma] = [
                'firma' => $firma,
                'mensaje' => isset($error['mensaje']) ? $error['mensaje'] : 'Error sin descripcion',
                'origen' => isset($error['origen']) ? $error['origen'] : null,
                'linea' => isset($error['linea']) ? $error['linea'] : null,
                'categoria' => isset($error['categoria']) ? $error['categoria'] : 'Aplicacion',
                'nivel' => isset($error['nivel']) ? $error['nivel'] : 'ERROR',
                'ultima_aparicion' => isset($error['fecha']) ? $error['fecha'] : null,
                'corregido_en' => date('Y-m-d H:i:s'),
                'usuario_id' => $usuarioId,
                'usuario' => self::limitar($usuario, 120),
                'nota' => self::limitar($nota, 500),
            ];

            $json = json_encode($registros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false) {
                throw new \RuntimeException('No fue posible codificar el seguimiento de errores.');
            }

            rewind($manejador);
            ftruncate($manejador, 0);
            if (fwrite($manejador, $json) === false) {
                throw new \RuntimeException('No fue posible guardar el seguimiento de errores.');
            }
            fflush($manejador);
            flock($manejador, LOCK_UN);
        } finally {
            fclose($manejador);
        }
    }

    public static function leer($archivo)
    {
        if (!is_file($archivo) || !is_readable($archivo)) {
            return [];
        }

        $manejador = fopen($archivo, 'rb');
        if ($manejador === false) {
            return [];
        }

        $contenido = '';
        if (flock($manejador, LOCK_SH)) {
            $contenido = stream_get_contents($manejador);
            flock($manejador, LOCK_UN);
        }
        fclose($manejador);

        return self::decodificar($contenido);
    }

    private static function decodificar($contenido)
    {
        if (!is_string($contenido) || trim($contenido) === '') {
            return [];
        }

        $registros = json_decode($contenido, true);
        return is_array($registros) ? $registros : [];
    }

    private static function ocurrioDespues($fechaError, $fechaCorreccion)
    {
        $error = strtotime((string) $fechaError);
        $correccion = strtotime((string) $fechaCorreccion);

        return $error !== false && $correccion !== false && $error > $correccion;
    }

    private static function datosCorreccion(array $registro)
    {
        return [
            'corregido_en' => isset($registro['corregido_en']) ? $registro['corregido_en'] : '',
            'usuario_id' => isset($registro['usuario_id']) ? $registro['usuario_id'] : null,
            'usuario' => isset($registro['usuario']) ? $registro['usuario'] : 'No identificado',
            'nota' => isset($registro['nota']) ? $registro['nota'] : '',
        ];
    }

    private static function errorHistorico(array $registro)
    {
        return array_merge([
            'firma' => isset($registro['firma']) ? $registro['firma'] : '',
            'fecha' => isset($registro['ultima_aparicion']) ? $registro['ultima_aparicion'] : null,
            'nivel' => isset($registro['nivel']) ? $registro['nivel'] : 'ERROR',
            'mensaje' => isset($registro['mensaje']) ? $registro['mensaje'] : 'Error sin descripcion',
            'origen' => isset($registro['origen']) ? $registro['origen'] : null,
            'linea' => isset($registro['linea']) ? $registro['linea'] : null,
            'categoria' => isset($registro['categoria']) ? $registro['categoria'] : 'Aplicacion',
            'ocurrencias' => 0,
        ], self::datosCorreccion($registro));
    }

    private static function limitar($texto, $limite)
    {
        $texto = trim((string) $texto);
        if (function_exists('mb_substr')) {
            return mb_substr($texto, 0, $limite, 'UTF-8');
        }

        return substr($texto, 0, $limite);
    }
}
