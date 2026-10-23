<?php

namespace Sis_medico\Http\Controllers\servicios;

use Sis_medico\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Historiaclinica;

class InterconexionController extends Controller
{
    public function consultas(Request $request)
    {
        if ($request->header('appid') !== null) {
            if ($request->header('appid') == 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
                $elemento = $request->getContent();
                $data = json_decode($elemento, true);
                $id_paciente =  $data['paciente'];
                $consultas = Historiaclinica::join('hc_procedimientos', 'hc_procedimientos.id_hc', '=', 'historiaclinica.hcid')
                    ->where('historiaclinica.id_paciente', $id_paciente)
                    ->where('hc_procedimientos.id_procedimiento_completo', '40')
                    ->get();
                $procedimientos = Historiaclinica::join('hc_procedimientos', 'hc_procedimientos.id_hc', '=', 'historiaclinica.hcid')
                    ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
                    ->where('historiaclinica.id_paciente', $id_paciente)
                    ->wherenull('hc_procedimientos.id_procedimiento_completo')
                    ->select('historiaclinica.*', 'hc_procedimientos.id as id_hc_procedimiento')
                    ->get();
                foreach ($procedimientos as $procedimiento) {
                    $n_procedimiento = "";
                    $procedimientos_f = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id_hc_procedimiento)->get();
                    foreach ($procedimientos_f as $key =>  $pro_po) {
                        if ($key == 0) {
                            $n_procedimiento .= $pro_po->procedimiento->nombre;
                        } else {
                            $n_procedimiento .= '+' . $pro_po->procedimiento->nombrejajajaja;
                        }
                    }
                    $procedimiento->n_procedimiento = $n_procedimiento;
                }
                $arreglo['consultas'] = $consultas;
                $arreglo['procedimientos'] = $procedimientos;
                return json_encode($arreglo);
            } else {
                return "CREDENCIALES INVALIDAS";
            }
        } else {
            return "CREDENCIALES INVALIDAS";
        }
    }
}
