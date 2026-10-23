<?php

namespace Sis_medico\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Empresa;
use Sis_medico\Examen_Orden;
use Sis_medico\GrupoPregunta;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Master_encuesta;
use Sis_medico\Paciente;
use Sis_medico\Pregunta;

class BdRobotWtController extends Controller
{
    public function menu_respuestas(Request $request)
    {
        $id_paciente = $request['id_paciente'];
        $fecha_nac = $request['fecha_nacimiento'];
        $opcion = $request['opcion'];
        $data = "";
        if (!is_null($opcion)) {
            $verificar = BdRobotWtController::verificar_paciente($id_paciente, $fecha_nac);
            if ($verificar['status'] == 'success') {

                if ($opcion == 1) {
                    $data = BdRobotWtController::getReceta($id_paciente);
                }

                if ($opcion == 2) {
                    $data = BdRobotWtController::getLaboratorio($id_paciente);
                }

                if ($opcion == 3) {
                    $data = BdRobotWtController::getInforme($id_paciente);
                }
            }
        }
        return $data;
    }

    public static function verificar_paciente($id_paciente, $fecha_nac)
    {
        $paciente = Paciente::find($id_paciente);
        if (is_null($paciente)) {
            return ['status' => 'error', 'msj' => 'Paciente no existe'];
        }
        if ($paciente->fecha_nacimiento != $fecha_nac) {
            return ['status' => 'error', 'msj' => 'Fecha de nacimiento incorrecta'];
        }

        return ['status' => 'success', 'msj' => 'Paciente ok'];
    }

    public static function getReceta($id_paciente)
    {
        $receta = hc_receta::join('historiaclinica as h', 'hc_receta.id_hc', 'h.hcid')
            ->where('h.id_paciente', $id_paciente)
            ->select('hc_receta.*')
            ->latest()
            ->first();
        if (is_null($receta)) {
            return ['status' => 'error', 'msj' => 'Receta no existe'];
        } else {
            return ['status' => 'success', 'msj' => 'Receta ok', 'url' => url('bd/download/receta') . '/' . $receta->id . '/2'];
        }
    }

    public static function getLaboratorio($id_paciente)
    {
        $examen_orden = Examen_Orden::where('examen_orden.pago_online', '0')
            ->where('examen_orden.id_paciente', $id_paciente)
            ->where('examen_orden.estado', '1')
            ->where('examen_orden.realizado', '1')
            ->select('examen_orden.*')
            ->latest()
            ->first();

        if (is_null($examen_orden)) {
            return ['status' => 'error', 'msj' => 'Resultado no existe'];
        } else {
            return ['status' => 'success', 'msj' => 'Resultado ok', 'url' => url('bd/download/resultados/externos') . '/' . $examen_orden->id];
        }
    }

    public static function getInforme($id_paciente)
    {
        $procedimientos = Historiaclinica::where('historiaclinica.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
            ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->where('hc_p.estado', '1')
            // ->select('hc_p.id_doctor_examinador', 'a.fechaini', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'historiaclinica.hcid as id_hc', 'a.id as id_agenda', 'hc_p.cuadro_clinico_bp as cuadro_clinico', 'hc_p.diagnosticos_bp as diagnosticos', 'hc_proto.tipo_procedimiento')
            ->select('hc_proto.*')
            ->latest()
            ->get();

        dd($procedimientos);
    }

    public function confirmar_agenda(Request $request)
    {
        $id_agenda = $request['id_agenda'];
    }

    public function preAdmision_guardar(Request $request)
    {
    }

    public function preguntas_encuesta_app(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        
        $id = $request['id_master'];
        $grupopregunta = GrupoPregunta::all();
        $contador      = 0;
        $encuesta = Master_encuesta::find($id);
        $tiempos = explode('+', $encuesta->tiempos);

        foreach ($grupopregunta as $value) {
            $variable = $value->id;
            $dato = Pregunta::where('id_grupopregunta', '=', $variable)
                ->where('pregunta.estado', '=', '1')
                ->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')
                ->where('pe.id_masterencuesta', $id)
                ->select('pregunta.*')
                ->get();

            if ($dato->isNotEmpty()) {
                $arr_p = array();
                foreach ($dato as $d) {
                    $valores = $d->pregunta_valor;
                    $arr_valores = array();
                    foreach ($valores as $val) {
                        $arr_val = [
                            "nombre" => $val->nombre_opcion->nombre,
                            "valor" => $val->valor,
                        ];
                        array_push($arr_valores, $arr_val);
                    }
                    //pregunta de tiempo
                    if ($d->id == 4) {
                        foreach ($tiempos as $tp) {
                            $arr_val = [
                                "nombre" => $tp,
                                "valor" => $tp,
                            ];
                            array_push($arr_valores, $arr_val);
                        }
                    }
                    $arr = [
                        "id" => $d->id,
                        "pregunta" => $d->nombre,
                        "id_grupopregunta" => $d->id_grupopregunta,
                        "valores" =>  $arr_valores
                    ];
                    array_push($arr_p, $arr);
                }
                $arreglo[$contador] = $arr_p;
                $contador           = $contador + 1;
            }
        }

        return $arreglo;
    }

    public function encuesta_ieced_guardar(Request $request)
    {
        if ($request->header('appid') == null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return json_encode(["respuesta" => 'CREDENCIALES INVALIDAS', 'status' => 'error']);
        }
        $encuesta = SinLoginController::encuestas_nuevo_guardar($request);
        return $encuesta;
    }
}
