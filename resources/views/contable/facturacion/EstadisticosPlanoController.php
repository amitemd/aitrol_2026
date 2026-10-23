<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Equipo_Historia;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_movimiento;
use Sis_medico\Movimiento;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\Producto;
use Sis_medico\Seguro;
use Sis_medico\Tipo;
use Sis_medico\User;

class EstadisticosPlanoController extends Controller
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
        if (in_array($rolUsuario, array(1, 6, 7, 9, 11, 20)) == false) {
            return true;
        }
    }


    public function index(Request $request)
    {
        //dd($request);
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd("dasda");
        //dd($consulta);
        //seguro IESS Y MSP IEES: 2 Y MSP: 5
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        //dd($fechaini);
        if(is_null($fechaini)){
            $fechaini= date('Y-m-d',strtotime($fechaini."- 1 month"));
        }
        if(is_null($fechafin)){
            $fechafin= date('Y-m-d');
        }
        $archivoplano = Archivo_Plano_Cabecera::where('estado', '1');
        $procedimiento = Procedimiento::all();
        $id_procedimiento = $request['id_procedimiento'];
        $archivoplano = DB::table('archivo_plano_cabecera as ap_cabecera')->join('hc_procedimientos as hc', 'ap_cabecera.id_hc_procedimimentos', 'hc.id')->join('archivo_plano_detalle as ap_detalle', 'ap_detalle.id_ap_cabecera', 'ap_cabecera.id')->join('hc_procedimiento_final as hc_final', 'hc_final.id_hc_procedimientos', 'hc.id')->join('procedimiento as p', 'p.id', 'hc_final.id_procedimiento')->where('ap_cabecera.estado', '1')->where('ap_detalle.estado', '1');
        //dd($archivoplano);
        if ($fechaini != null) {
            $archivoplano = $archivoplano->whereDate('ap_cabecera.fecha_ing', '<=', $fechafin);
        } else {
            $archivoplano = $archivoplano->wherebetween('ap_cabecera.fecha_ing', [$fechaini . ' 00:00:00', $fechafin . ' 23:59:59']);
        }
        if ($id_procedimiento != null) {
            $archivoplano = $archivoplano->where('hc_final.id_procedimiento', $id_procedimiento);
        }
        //dd("ssdas");
        $archivoplano = $archivoplano->groupBy('hc_final.id_procedimiento')->select(DB::raw("SUM(ap_detalle.total) as total"), 'p.nombre', DB::raw("SUM(ap_detalle.cantidad) as cantidad"));
        $archivoplano = $archivoplano->get();
        //dd($archivoplano);


        return view('archivo_plano/estadisticos/index', ['archivo_plano' => $archivoplano, 'fechaini' => $fechaini, 'fechafin' => $fechafin, 'procedimiento' => $procedimiento, 'id_procedimiento' => $id_procedimiento]);
    }
    public function orden(Request $request){
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd("dasda");
        //dd($consulta);
        //seguro IESS Y MSP IEES: 2 Y MSP: 5
        $fechaini = $request['fechaini'];
        $fechafin = $request['fechafin'];
        $seguro= $request['seguro'];
        $tipo_pagos=DB::table('ct_orden_venta as ct_orden')->join('ct_orden_venta_pago as pago','pago.id_orden','ct_orden.id')->join('ct_tipo_pago as forma','forma.id','pago.tipo')->groupBy('pago.tipo');
        //dd($fechaini);
        if(is_null($fechaini)){
            $fechaini= date('Y-m-d',strtotime($fechaini."- 1 month"));
        }
        if(is_null($fechafin)){
            $fechafin= date('Y-m-d');
        }
        $venorden = DB::table('ct_orden_venta as ct_orden')->join('ct_orden_venta_detalle as detalles','detalles.id_orden','ct_orden.id')->join('seguros as s','s.id','ct_orden.id_seguro');
        if(!is_null($request->seguro)){
            $venorden= $venorden->whereIn('ct_orden.id_seguro',$seguro);
            $tipo_pagos= $tipo_pagos->whereIn('ct_orden.id_seguro',$seguro);
        }
        $id_seguro= $request->seguro;
        $venorden= $venorden->whereBetween('ct_orden.fecha_emision',[$fechaini,$fechafin])->groupBy('ct_orden.id_seguro')->select(DB::raw('SUM(detalles.total) as total'),'s.nombre')->get();
        $tipo_pagos= $tipo_pagos->whereBetween('ct_orden.fecha_emision',[$fechaini,$fechafin])->groupBy('pago.tipo')->select(DB::raw('SUM(pago.valor) as valor'),'forma.nombre as nombre_pagos')->get();
        $seguros= Seguro::all();
        
        //dd($venorden,$tipo_pagos);
        return view('contable/facturacion/estadisticos', ['venorden' => $venorden,'tipo_pagos'=>$tipo_pagos, 'fechaini' => $fechaini, 'fechafin' => $fechafin,'id_seguro'=>$id_seguro, 'seguros' => $seguros]);
    }
}
