<?php

namespace App\Services;

use Generator;

final readonly class LectorPersonasExcel
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
        'telefono' => ['telefono', 'telefonos', 'telefono_fijo', 'telf', 'telefono_convencional', 'telefono1'],
        'celular' => ['celular', 'movil', 'telefono_celular', 'telefono_movil', 'telefono2'],
        'direccion' => ['direccion', 'domicilio', 'direccion_domicilio'],
        'email' => ['email', 'correo', 'correo_electronico', 'e_mail', 'mail'],
        'fecha_nacimiento' => ['fecha_nacimiento', 'fechanacimiento', 'fecha_de_nacimiento', 'f_nacimiento', 'fec_nacimiento'],
        'urbanizacion' => ['urbanizacion'],
        'etapa' => ['etapa'],
        'manzana' => ['manzana'],
        'villa' => ['villa'],
        'referencia' => ['referencia'],
    ];

    public function __construct(private LectorExcel $excel) {}

    /** @return array{total: int, filas: Generator<int, array<string, string>>, fechasDesde1904: bool} */
    public function leer(string $ruta): array
    {
        return $this->excel->leer($ruta, $this->reconocerEncabezados(...),
            'No se encontró una hoja con las columnas Identificación y Nombres. Revise los encabezados del Excel.');
    }

    private function reconocerEncabezados(array $encabezados): array
    {
        $columnas = LectorExcel::mapearColumnas($encabezados, self::ENCABEZADOS);
        $formatoGastroclinica = ['id', 'nombre1', 'nombre2', 'apellido1', 'apellido2', 'telefono1', 'telefono2', 'telefono3'];
        if (! in_array('identificacion', $columnas, true) && array_diff($formatoGastroclinica, $encabezados) === []) {
            $columnas[array_search('id', $encabezados, true)] = 'identificacion';
        }

        return in_array('identificacion', $columnas, true) && array_intersect(['nombre', 'primer_nombre'], $columnas) !== [] ? $columnas : [];
    }
}
