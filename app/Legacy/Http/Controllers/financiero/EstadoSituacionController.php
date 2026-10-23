<?php

namespace Sis_medico\Http\Controllers\financiero;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Empresa;
use Sis_medico\EstadoResultado;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;

class EstadoSituacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $ingresos   = array();

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = $request['fecha_desde'];
            $fecha_hasta = $request['fecha_hasta'];
        } else {
            $fecha_desde = date('Y-m-d');
            $fecha_hasta = date('Y-m-d');
        }
        $activos    = array();
        $pasivos    = array();
        $patrimonio = array();
        $totpyg     = "";
        return view('fin_estado_situacion/index', ['fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta,
            'empresa'                                                => $empresa, 'activos'         => $activos, 'pasivos' => $pasivos, 'patrimonio' => $patrimonio, 'totpyg' => $totpyg]);
    }

    public function show(Request $request)
    {

        $id_empresa      = $request->session()->get('id_empresa');
        $empresa         = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $cuentas_detalle = "";

        if (!isset($request['imprimir'])) {
            if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
                $fecha_desde = $request['fecha_desde'];
                $fecha_hasta = $request['fecha_hasta'];
            } else {
                $fecha_desde = date('Y');
                $fecha_hasta = date('Y');
            }
            if (isset($request['cuentas_detalle'])) {
                $cuentas_detalle = 1;
            }
            $mostrar_detalles = "";
            if (isset($request['mostrar_detalles'])) {
                $mostrar_detalles = $request['mostrar_detalles'];
            }
            $balance = array();
            for ($i = $fecha_desde; $i <= $fecha_hasta; $i++) {
                $activos[$i]    = $this->detalle($i, $i, 'A', $id_empresa, $cuentas_detalle);
                $pasivos[$i]    = $this->detalle($i, $i, 'P', $id_empresa, $cuentas_detalle);
                $patrimonio[$i] = $this->detalle($i, $i, 'PA', $id_empresa, $cuentas_detalle);
                $totpyg[$i]     = EstadoResultado::utilidad_gravable($i, $i, '2');

            }
            $periodo_desde = $this->fechaTexto($fecha_desde);
            $periodo_hasta = $this->fechaTexto($fecha_hasta);
            // dd($request);
            return view('fin_estado_situacion/index', ['fecha_desde' => $fecha_desde, 'fecha_hasta'        => $fecha_hasta, 'empresa'         => $empresa,
                'activos'                                                => $activos, 'pasivos'                => $pasivos, 'patrimonio'          => $patrimonio, 'totpyg' => $totpyg, 'cuentas_detalle' => $cuentas_detalle,
                'mostrar_detalles'                                       => $mostrar_detalles, 'periodo_desde' => $periodo_desde, 'periodo_hasta' => $periodo_hasta]);
        }
    }

    public function detalle($desde, $hasta, $tipo, $id_empresa, $cuentas_detalle = "")
    {
        $balance = array();
        if ($tipo == 'A') {
            $condicion = '1';
        } elseif ($tipo == 'P') {
            $condicion = '2';
        } else {
            $condicion = '3';
        }
        if ($cuentas_detalle == "") {
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->whereRaw('character_length(id) <= 8')
                ->where('id', 'like', "$condicion%")
                ->select('id', 'nombre', 'naturaleza')
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $plans = Plan_Cuentas::where('estado', '<>', 0)
                ->where('id', 'like', "$condicion%")
            //->orwhere('id', 'like', "$condicion")
                ->select('id', 'nombre', 'naturaleza')
                ->orderBy('id', 'asc')
                ->get();
        }

        $i = 0;
        foreach ($plans as $plan) {
            $data = array();
            if ($plan->id != "") {
                $saldo2  = 0;
                $asiento = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                    ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                    ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                // ->where('ct_asientos_detalle.id_plan_cuenta', 'like', $plan->id.'%')
                    ->where('c.id_empresa', $id_empresa)
                    ->where('ct_asientos_detalle.estado', '<>', 0);
                if ($plan->naturaleza != 0) {
                    $asiento  = $asiento->where('p.naturaleza', '<>', '0');
                    $asiento2 = Ct_Asientos_Detalle::where('id_plan_cuenta', 'like', $plan->id . '%')
                        ->join('ct_asientos_cabecera as c', 'c.id', 'ct_asientos_detalle.id_asiento_cabecera')
                        ->join('plan_cuentas as p', 'ct_asientos_detalle.id_plan_cuenta', 'p.id')
                    // ->where('ct_asientos_detalle.id_plan_cuenta', 'like', $plan->id.'%')
                        ->where('c.id_empresa', $id_empresa)
                        ->where('ct_asientos_detalle.estado', 1)
                        ->where('p.naturaleza', '0')
                        ->whereBetween('fecha', ["$desde-01-01 00:00:00", "$hasta-12-31 23:59:59"])
                        ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                        ->get();
                    foreach ($asiento2 as $row) {
                        $saldo2 = $row->saldo;
                    }
                }
                $asiento = $asiento->whereBetween('fecha', ["$desde-01-01 00:00:00", "$hasta-12-31 23:59:59"])
                    ->select(DB::raw('ifnull(SUM(debe-haber),0) as saldo'))
                    ->get();
                $saldo = 0;
                foreach ($asiento as $row) {
                    $saldo = $row->saldo - $saldo2;
                }
                $data['cuenta'] = $plan->id;
                $data['nombre'] = strtoupper($plan->nombre);
                $data['saldo']  = $saldo;
                $balance[]      = $data;
            }

        }
        return $balance;
    }

    public function fechaTexto($fecha)
    {
        $fecha     = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia       = date('l', strtotime($fecha));
        $mes       = date('F', strtotime($fecha));
        $anio      = date('Y', strtotime($fecha));
        $dias_EN   = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $dias_ES   = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
        $meses_ES  = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $meses_EN  = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
        // return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
        return $numeroDia . " de " . $nombreMes . " de " . $anio;
    }

}
