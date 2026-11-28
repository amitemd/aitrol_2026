<?php

namespace App\Services;

use Closure;
use DOMDocument;
use Generator;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use XMLReader;
use ZipArchive;

final class LectorExcel
{
    /**
     * @param  Closure(array<string, string>): array<string, string>  $reconocerEncabezados
     * @return array{total: int, filas: Generator<int, array<string, string>>, fechasDesde1904: bool}
     */
    public function leer(string $ruta, Closure $reconocerEncabezados, string $errorEncabezados, bool $omitirVacias = false): array
    {
        $archivo = new ZipArchive;
        if ($archivo->open($ruta, ZipArchive::RDONLY) !== true) {
            throw new RuntimeException('El archivo debe ser un libro Excel .xlsx válido.');
        }

        $compartidos = [];
        if ($archivo->locateName('xl/sharedStrings.xml') !== false) {
            foreach ($this->elementos($archivo, 'xl/sharedStrings.xml', 'si', 'sst', 40_000_000) as $texto) {
                $compartidos[] = $this->texto($texto);
            }
        }

        $fechasDesde1904 = false;
        foreach ($this->elementos($archivo, 'xl/workbook.xml', 'workbookPr', 'workbook', 1_000_000) as $propiedades) {
            $fechasDesde1904 = in_array((string) $propiedades['date1904'], ['1', 'true'], true);
        }

        for ($indice = 0; $indice < $archivo->numFiles; $indice++) {
            $hoja = $archivo->getNameIndex($indice);
            if (! preg_match('#^xl/worksheets/[^/]+\.xml$#D', $hoja)) {
                continue;
            }
            $encabezado = null;
            $columnas = [];
            $total = 0;
            foreach ($this->elementos($archivo, $hoja, 'row', 'worksheet') as $posicion => $fila) {
                if ($encabezado !== null) {
                    if (! $omitirVacias || $this->tieneDatos($this->celdas($fila, $compartidos, $columnas))) {
                        $total++;
                    }

                    continue;
                }
                if ($posicion >= 30) {
                    break;
                }
                $encabezados = array_map(
                    fn (string $celda): string => trim(preg_replace('/[^a-z0-9]+/', '_', Str::lower(Str::ascii($celda))), '_'),
                    $this->celdas($fila, $compartidos),
                );
                $columnas = $reconocerEncabezados($encabezados);
                if ($columnas !== []) {
                    $encabezado = $posicion;
                }
            }
            if ($encabezado !== null) {
                return [
                    'total' => $total,
                    'filas' => $this->filas($archivo, $hoja, $encabezado, $columnas, $compartidos, $omitirVacias),
                    'fechasDesde1904' => $fechasDesde1904,
                ];
            }
        }

        throw new RuntimeException($errorEncabezados);
    }

    /** @return Generator<int, SimpleXMLElement> */
    private function elementos(ZipArchive $archivo, string $entrada, string $elemento, string $raiz, int $limite = 160_000_000): Generator
    {
        $estado = $archivo->statName($entrada);
        if ($estado === false || $estado['size'] > $limite || ! ($flujo = $archivo->getStream($entrada))) {
            throw new RuntimeException('El libro Excel tiene una sección ilegible o supera el tamaño de procesamiento.');
        }

        $erroresAnteriores = libxml_use_internal_errors(true);
        libxml_clear_errors();
        try {
            $opciones = LIBXML_NONET | (defined('LIBXML_NO_XXE') ? constant('LIBXML_NO_XXE') : 0);
            $lector = XMLReader::fromStream($flujo, null, $opciones);
            $lector->setParserProperty(XMLReader::LOADDTD, false);
            $lector->setParserProperty(XMLReader::SUBST_ENTITIES, false);
            $encontrada = false;
            while ($lector->read()) {
                if ($lector->nodeType === XMLReader::DOC_TYPE) {
                    throw new RuntimeException('El libro Excel contiene una estructura XML no compatible.');
                }
                if ($lector->nodeType !== XMLReader::ELEMENT) {
                    continue;
                }
                if ($lector->depth === 0) {
                    $encontrada = $lector->localName === $raiz;
                }
                if ($encontrada && $lector->localName === $elemento) {
                    $documento = new DOMDocument;
                    $nodo = $lector->expand($documento);
                    if ($nodo === false) {
                        throw new RuntimeException('El libro Excel contiene una sección XML dañada.');
                    }
                    $documento->appendChild($nodo);
                    yield simplexml_import_dom($nodo);
                }
            }
            if (! $encontrada || libxml_get_last_error() !== false) {
                throw new RuntimeException('El libro Excel contiene una sección XML dañada.');
            }
        } finally {
            if (isset($lector)) {
                $lector->close();
            }
            fclose($flujo);
            libxml_clear_errors();
            libxml_use_internal_errors($erroresAnteriores);
        }
    }

    /** @return array<string, string> */
    private function celdas(SimpleXMLElement $fila, array $compartidos, ?array $columnas = null): array
    {
        $valores = [];
        foreach ($fila->c as $celda) {
            $columna = rtrim((string) $celda['r'], '0123456789');
            if ($columnas !== null && ! isset($columnas[$columna])) {
                continue;
            }
            $valor = (string) $celda->v;
            $valores[$columnas[$columna] ?? $columna] = trim(match ((string) $celda['t']) {
                's' => $compartidos[$valor] ?? '',
                'inlineStr' => $this->texto($celda->is),
                'e' => '',
                default => $valor,
            });
        }

        return $valores;
    }

    private function texto(SimpleXMLElement $nodo): string
    {
        $texto = (string) $nodo->t;
        foreach ($nodo->r as $parte) {
            $texto .= (string) $parte->t;
        }

        return $texto;
    }

    /** @return array<string, string> */
    public static function mapearColumnas(array $encabezados, array $aliasPorCampo): array
    {
        $columnas = [];
        foreach ($aliasPorCampo as $campo => $alias) {
            foreach ($encabezados as $columna => $clave) {
                if (in_array($clave, $alias, true)) {
                    $columnas[$columna] = $campo;
                    break;
                }
            }
        }

        return $columnas;
    }

    /** @return Generator<int, array<string, string>> */
    private function filas(ZipArchive $archivo, string $hoja, int $encabezado, array $columnas, array $compartidos, bool $omitirVacias): Generator
    {
        foreach ($this->elementos($archivo, $hoja, 'row', 'worksheet') as $posicion => $fila) {
            if ($posicion > $encabezado) {
                $datos = $this->celdas($fila, $compartidos, $columnas);
                if (! $omitirVacias || $this->tieneDatos($datos)) {
                    yield $datos;
                }
            }
        }
    }

    private function tieneDatos(array $fila): bool
    {
        return array_filter($fila, fn (string $valor): bool => $valor !== '') !== [];
    }
}
