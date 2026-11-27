<?php

namespace Aitrol\CajaBancos\Infrastructure\Http;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

final class ExtractoCsv
{
    public function leer(UploadedFile $archivo): array
    {
        $flujo = fopen($archivo->getPathname(), 'rb');
        if ($flujo === false) {
            throw ValidationException::withMessages(['archivo' => 'No se pudo leer el extracto.']);
        }

        try {
            $cabecera = fgets($flujo);
            if ($cabecera === false) {
                throw ValidationException::withMessages(['archivo' => 'El archivo está vacío.']);
            }
            $cabecera = preg_replace('/^\xEF\xBB\xBF/', '', trim($cabecera));
            $delimitador = str_contains($cabecera, ';') ? ';' : ',';
            $campos = array_map(fn ($campo) => strtolower(trim($campo)), str_getcsv($cabecera, $delimitador, '"', ''));
            if ($campos !== ['fecha', 'referencia', 'descripcion', 'importe']) {
                throw ValidationException::withMessages(['archivo' => 'La cabecera debe ser fecha,referencia,descripcion,importe, en ese orden.']);
            }
            $lineas = [];
            while (($fila = fgetcsv($flujo, 0, $delimitador, '"', '')) !== false) {
                if ($fila === [null]) continue;
                if (count($fila) !== 4 || count($lineas) >= 1000 || ! mb_check_encoding($fila, 'UTF-8')) {
                    throw ValidationException::withMessages(['archivo' => 'Usa UTF-8, cuatro columnas por fila y un máximo de 1.000 movimientos.']);
                }
                $lineas[] = array_combine($campos, array_map('trim', $fila));
            }

            return $lineas;
        } finally {
            fclose($flujo);
        }
    }
}
