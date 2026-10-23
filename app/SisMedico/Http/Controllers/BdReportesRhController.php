<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Ordenes_Ventas;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Encuesta_Labs;
use Sis_medico\Examen_Orden;
use Sis_medico\GrupoPregunta;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Labs_Factura_Agrupada_Orden;
use Sis_medico\Paciente;
use Sis_medico\Preguntas_Labs;
use Sis_medico\Vacaciones;

class BdReportesRhController extends Controller
{
    public function consulta_vacaciones(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $fecha_desde = $request->fecha_inicio;
        $fecha_hasta = $request->fecha_fin;
        $empresa = Empresa::where('prioridad',1)->where('estado',1)->first();
        $vacaciones = Vacaciones::where('estado', 1)->where('id_empresa', $empresa->id);
    
        if (!is_null($fecha_desde)) {
            $vacaciones = $vacaciones->whereBetween('inicio', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
            //$vacaciones = $vacaciones->where('inicio', '>=', $fecha_desde . ' 00:00:00');
        }else{
            $vacaciones = $vacaciones->where('fin', '<=', $fecha_hasta);
        }
        // if (!is_null($fecha_hasta)) {
        //     $vacaciones = $vacaciones->where('fin', '<=', $fecha_hasta . ' 23:59:59');
        // }

        $vacaciones = $vacaciones->get();

        $arr_vacaciones = [];
        foreach ($vacaciones as $value) {
            $data['id'] = $value->id;
            $data['id_usuario'] = $value->id_usuario;
            $data['nombre_usuario'] = isset($value->nombre_usuario) ? $value->nombre_usuario->apellido1.' '.$value->nombre_usuario->apellido2.' '.$value->nombre_usuario->nombre1.' '.$value->nombre_usuario->nombre2 : '';
            $data['fecha_inicio'] = $value->inicio;
            $data['fecha_fin'] = $value->fin;
            $data['id_contingente'] = $value->id_contingente;
            $data['nombre_contingente'] = isset($value->nombre_contingente) ? $value->nombre_contingente->apellido1.' '.$value->nombre_contingente->nombre1 : '';
            array_push($arr_vacaciones, $data);
        }

        return $arr_vacaciones;

    }
    
}
