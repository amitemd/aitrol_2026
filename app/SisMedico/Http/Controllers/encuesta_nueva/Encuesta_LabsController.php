<?php

namespace Sis_medico\Http\Controllers\encuesta_nueva;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use laravel\laravel;
use Carbon\Carbon;
use Sis_medico\Agenda;
use Sis_medico\Preguntas_Labs;
use Sis_medico\Encuesta_Labs;
use Sis_medico\Encuesta_Complementolabs;
use Sis_medico\Http\Controllers\SinLoginController;


class Encuesta_LabsController extends Controller
{

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }


    public static function sumarProducto($matrices, $dividendo)
    {


        if (isset($matrices) and $dividendo != 0) {
            $result = 0;
            $matricesCount = count($matrices);
            $matrixSize = count($matrices[0]);

            for ($i = 0; $i < $matrixSize; $i++) {
                $product = 1;
                for ($j = 0; $j < $matricesCount; $j++) {
                    $product *= $matrices[$j][$i];
                }
                $result += $product;
            }

            $res = ($result * 10) / $dividendo;

            return  $res;
        }
    }






    public function array_resultado($desde, $hasta)
    {

        $query_cinco_ultimos_pacientes = Encuesta_Labs::join("paciente", "paciente.id", "encuesta_labs.id_paciente")
            ->where("encuesta_labs.tipo_master", 2)
            ->select("paciente.nombre1", "paciente.nombre2", "paciente.apellido1", "paciente.apellido2", "encuesta_labs.*")
            ->whereBetween('encuesta_labs.created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->get();

        $array = []; //603
        $pro = 0;

        foreach ($query_cinco_ultimos_pacientes as $val) {
            $complemento = Encuesta_Complementolabs::where("id_encuesta_labs", $val->id)->where("id_pregunta_labs", "<>", 14)->get();

            foreach ($complemento as $i) {

                $valor = intval($i->valor);
                $nombre = $i->pregunta->nombre;

                if (!isset($array[$nombre])) {
                    $array[$nombre] = [
                        '10' => 0,
                        '8' => 0,
                        '5' => 0,
                        '2' => 0,
                        '0' => 0,
                        "total_preguntas" => 0,
                        "ponderacion" => 0,
                    ];
                }



                $contador  = $array[$nombre][$valor] + 1;
                $contadorT = $array[$nombre]["total_preguntas"] + 1;
                $array[$nombre][$valor] = $contador;
                $array[$nombre]["total_preguntas"] = $contadorT;
            }

            foreach ($array as $ty => $ite) {

                $matriz1 = [[$ite['10'], $ite['8'], $ite['5'], $ite['2'], $ite['0']], [10, 8, 5, 2, 0]];
                $sumarProducto = $this->sumarProducto($matriz1, $ite['total_preguntas']);
                $array[$ty]['ponderacion'] = number_format((float)$sumarProducto, 2, '.', '');
            }

           
        }

        foreach ($array as $promedio) {
            $pro += $promedio['ponderacion'];
        }
        $resF = $pro / 6;
        
        return [$array, $resF];
    }
    public function index(Request $request)
    {
        $desde = date("Y-m-01");
        $hasta = date("Y-m-t", strtotime($desde . " +1 month"));
        $res = $this->array_resultado($desde, $hasta);
        $array_resultados = $res[0];
        $promedio = $res[1];
        if ($array_resultados === null) {
            $array_resultados = [];
        }
        return view('encuesta_nueva/index', compact('array_resultados', 'promedio', 'desde', 'hasta'));
    }

    public function buscador(Request $request)
    {

        $desde = $request->desde;
        $hasta = $request->hasta;
        if ($request->excel == 0) {
        $res = $this->array_resultado($desde, $hasta);

        $array_resultados = $res[0];
        $promedio = $res[1];
        if ($array_resultados === null) {
            $array_resultados = [];
        }
        return view('encuesta_nueva/index', compact('array_resultados', 'promedio', 'desde', 'hasta'));
        } elseif ($request->excel == 1) {
            SinLoginController::excel_nuevo_formato($desde, $hasta);
        }
    }
}
