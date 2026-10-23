<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Response;
use Storage;
use Sis_medico\Orden;
use Sis_medico\Orden_Tipo;
use Sis_medico\Orden_Procedimiento;
use Sis_medico\User;
use Carbon\Carbon;


class OrdenesListadoController extends Controller
{

    private $controlador = 'ordeneslistado';
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        //dd($request->all());  
        $data['controlador'] = $this->controlador;
        config(['data' => $data]);
        $fecha =  date('Y-m-d');
        $desde = null;
        $hasta = null;
        $consultaGeneral = DB::table('orden');
        if (is_null($request['desde']) || is_null($request['hasta'])) {
            $consultaGeneral = $consultaGeneral->wherebetween('orden.fecha_orden', [$fecha . ' 00:00:00', $fecha . ' 23:59:59']);
            $desde = $request['desde'];
            $hasta = $request['hasta'];
        }
        $tip = null;
        if (!is_null($request['tipo'])) {
            $consultaGeneral = $consultaGeneral->where('orden.tipo_procedimiento', $request['tipo']);
            $tip = $request['tipo'];
        }
        if (!is_null($request['desde']) || !is_null($request['hasta'])) {
            $consultaGeneral = $consultaGeneral->wherebetween('orden.fecha_orden', [$request['desde'] . ' 00:00:00', $request['hasta'] . ' 23:59:59']);
            $desde = $request['desde'];
            $hasta = $request['hasta'];
        }
        $consultaGeneral = $consultaGeneral->select('orden.id as id', 'orden.id_doctor as id_doctor', 'orden.id_paciente as id_paciente', 'orden.tipo_procedimiento as tipo_procedimiento', 'orden.fecha_orden as fecha_orden', 'orden.id_evolucion as id_evolucion')->orderBy('fecha_orden', 'desc')->get();
        return view('ordenes/index', ['consultaGeneral' => $consultaGeneral, 'tip' => $tip, 'desde' => $desde, 'hasta' => $hasta]);
    }

    public function cantidad()
    {

        $fecha = date('Y-m-d');
        $observaciones = DB::table('orden')->join('orden_tipo as ot', 'orden.id', 'ot.id_orden')->whereBetween('fecha_orden', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->get();
        return $observaciones->count();
    }
}
