<?php

namespace App\Services;

use DOMDocument;
use Generator;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use XMLReader;
use ZipArchive;

final class LectorPersonasExcel
{
    private const ENCABEZADOS = [
        'identificacion' => ['identificacion', 'cedula', 'ruc', 'cedula_ruc', 'cedula_o_ruc', 'cedula_pasaporte', 'documento', 'numero_documento', 'numero_de_documento', 'numero_identificacion', 'numero_de_identificacion', 'num_identificacion', 'identificacion_persona', 'ci', 'dni', 'pasaporte', 'cedula_paciente', 'cedula_identidad', 'nro_identificacion'],
        'tipo' => ['tipo_identificacion', 'tipo_de_identificacion', 'tipo_documento', 'tipo_de_documento', 'id_tipo_identificacion'],
        'nombre' => ['nombre', 'nombres', 'nombre_completo', 'nombres_completos', 'nombres_persona', 'nombres_paciente', 'nombre_persona', 'nombre_paciente', 'nombres_y_apellidos', 'apellidos_y_nombres', 'paciente'],
        'apellido' => ['apellido', 'apellidos', 'apellidos_persona', 'apellidos_paciente'],
        'primer_nombre' => ['primer_nombre', 'nombre_1', 'nombre1', 'nombre_uno', 'primer_nombre_paciente'],
        'segundo_nombre' => ['segundo_nombre', 'nombre_2', 'nombre2', 'nombre_dos', 'segundo_nombre_paciente'],
        'primer_apellido' => ['primer_apellido', 'apellido_1', 'apellido1', 'apellido_uno', 'primer_apellido_paciente'],
        'segundo_apellido' => ['segundo_apellido', 'apellido_2', 'apellido2', 'apellido_dos', 'segundo_apellido_paciente'],
        'telefono' => ['telefono', 'telefonos', 'telefono_fijo', 'telf', 'telefono_convencional'],
        'celular' => ['celular', 'movil', 'telefono_celular', 'telefono_movil'],
        'direccion' => ['direccion', 'domicilio', 'direccion_domicilio'],
        'email' => ['email', 'correo', 'correo_electronico', 'e_mail', 'mail'],
        'fecha_nacimiento' => ['fecha_nacimiento', 'fechanacimiento', 'fecha_de_nacimiento', 'f_nacimiento', 'fec_nacimiento'],
        'urbanizacion' => ['urbanizacion'],
        'etapa' => ['etapa'],
        'manzana' => ['manzana'],
        'villa' => ['villa'],
        'referencia' => ['referencia'],
    ];

    /**
     * @return array{total: int, filas: Generator<int, array<string, string>>, fechasDesde1904: bool}
     */
    public function leer(string $ruta): array
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
                    $total++;

                    continue;
                }
                if ($posicion >= 30) {
                    break;
                }
                $columnas = $this->columnas($this->celdas($fila, $compartidos));
                if (in_array('identificacion', $columnas, true) && array_intersect(['nombre', 'primer_nombre'], $columnas) !== []) {
                    $encabezado = $posicion;
                }
            }
            if ($encabezado !== null) {
                return [
                    'total' => $total,
                    'filas' => $this->filas($archivo, $hoja, $encabezado, $columnas, $compartidos),
                    'fechasDesde1904' => $fechasDesde1904,
                ];
            }
        }

        throw new RuntimeException('No se encontró una hoja con las columnas Identificación y Nombres. Revise los encabezados del Excel.');
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
    private function columnas(array $celdas): array
    {
        $columnas = [];
        foreach (self::ENCABEZADOS as $campo => $alias) {
            foreach ($celdas as $columna => $celda) {
                $clave = trim(preg_replace('/[^a-z0-9]+/', '_', Str::lower(Str::ascii($celda))), '_');
                if (in_array($clave, $alias, true)) {
                    $columnas[$columna] = $campo;
                    break;
                }
            }
        }

        return $columnas;
    }

    /** @return Generator<int, array<string, string>> */
    private function filas(ZipArchive $archivo, string $hoja, int $encabezado, array $columnas, array $compartidos): Generator
    {
        foreach ($this->elementos($archivo, $hoja, 'row', 'worksheet') as $posicion => $fila) {
            if ($posicion > $encabezado) {
                yield $this->celdas($fila, $compartidos, $columnas);
            }
        }
    }
}
