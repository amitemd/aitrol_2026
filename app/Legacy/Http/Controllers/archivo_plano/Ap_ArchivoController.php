<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Carbon\Carbon;
use Cookie;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel_Worksheet_Drawing;
use Response;
use Sis_medico\Ap_Agrupado;
use Sis_medico\Ap_Tipo_Seg;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Seguro;
use Sis_medico\Agenda;
use Sis_medico\Reporte_Consultas_Iess;
use Sis_medico\Examen_Orden;
use Sis_medico\Orden;
use Sis_medico\Archivo_Plano_Detalle;
use Sis_medico\Tipo_Seguro;
use Sis_medico\Ap_Orden_Venta;
use Sis_medico\Ct_Ven_Orden;
use Sis_medico\Ct_Ven_Orden_Detalle;
use Sis_medico\Ct_Clientes;
use Sis_medico\ApProcedimiento;

class Ap_ArchivoController extends Controller
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
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 11, 22)) == false) {
            return true;
        }
    }

    public function genera_ap(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd("here");
        $sumatoria = [];$agrupados = [];

        $mes_plano    = $request->mes_plano;
        $tipo_seg     = $request->id_tipo_seguro;
        $cob_compar   = $request->id_cobertura_comp;
        $seg          = $request->seguro;
        $empresa      = $request->id_empresa;
        $arr_base_0   = [];
        $arr_base_iva = [];
        $arr_v_iva    = [];
        $arr_amd_10   = [];
        //dd($t_seg);

        $grupos = DB::table('tipo_seguro')->select('tipo_principal')->groupby('tipo_principal')->orderBy('tipo_principal','asc')->get();
        //dd($grupos);
        if($mes_plano != null){
            foreach($grupos as $grupo){
                //dd($grupo);
               
                    
                $archivo_plano[$grupo->tipo_principal] = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                    ->where('archivo_plano_cabecera.id_seguro', $seg)
                    ->where('archivo_plano_cabecera.id_empresa', $empresa)
                    ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
                    ->where('archivo_plano_cabecera.estado', '1')
                    ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
                    //->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                    //->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                    ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
                    ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                    ->where('tseg.tipo_principal',$grupo->tipo_principal)
                    ->where('apd.estado', '1')
                    ->select( 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tseg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'archivo_plano_cabecera.id as arplaca', 'apd.subtotal','archivo_plano_cabecera.id_cobertura_comp','apd.valor_porcent_clasifi', 'apd.porcent_10')
                    ->orderby('archivo_plano_cabecera.id_paciente')
                    ->orderby('archivo_plano_cabecera.fecha_ing')
                    ->orderby('tipo_ex.orden_plano')
                    ->get();

                $archivo_plano[$grupo->tipo_principal.'-ISSFA'] = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                    ->where('archivo_plano_cabecera.id_seguro', $seg)
                    ->where('archivo_plano_cabecera.id_empresa', $empresa)
                    ->where('archivo_plano_cabecera.id_cobertura_comp', '3')
                    ->where('archivo_plano_cabecera.estado', '1')
                    ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
                    //->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                    //->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                    ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
                    ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                    ->where('tseg.tipo_principal',$grupo->tipo_principal)
                    ->where('apd.estado', '1')
                    ->select( 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tseg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'archivo_plano_cabecera.id as arplaca', 'apd.subtotal','archivo_plano_cabecera.id_cobertura_comp','apd.valor_porcent_clasifi', 'apd.porcent_10')
                    ->orderby('archivo_plano_cabecera.id_paciente')
                    ->orderby('archivo_plano_cabecera.fecha_ing')
                    ->orderby('tipo_ex.orden_plano')
                    ->get();

                    $archivo_plano[$grupo->tipo_principal.'-ISSPOL'] = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                    ->where('archivo_plano_cabecera.id_seguro', $seg)
                    ->where('archivo_plano_cabecera.id_empresa', $empresa)
                    ->where('archivo_plano_cabecera.id_cobertura_comp', '6')
                    ->where('archivo_plano_cabecera.estado', '1')
                    ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
                    //->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                    //->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                    ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
                    ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                    ->where('tseg.tipo_principal',$grupo->tipo_principal)
                    ->where('apd.estado', '1')
                    ->select( 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tseg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'archivo_plano_cabecera.id as arplaca', 'apd.subtotal','archivo_plano_cabecera.id_cobertura_comp','apd.valor_porcent_clasifi', 'apd.porcent_10')
                    ->orderby('archivo_plano_cabecera.id_paciente')
                    ->orderby('archivo_plano_cabecera.fecha_ing')
                    ->orderby('tipo_ex.orden_plano')
                    ->get();
                    
            }
            //dd($archivo_plano);
            $sumatoria = [];$cobertura = null;
            foreach($archivo_plano as $key => $ap_tipo){
                $sumpxq = 0;$sumiva_ap = 0;$base0 = 0;$base12 = 0;$admin = 0;
                foreach($ap_tipo as $cabecera){
                    $pxq = $cabecera->cantidad * $cabecera->valor;
                    $iva_ap = $cabecera->valor_unitario * $cabecera->porcentaje_iva * $cabecera->cantidad;
                    if($seg == '5'){
                        $iva_ap = round($iva_ap,2);
                    }
                    $sumiva_ap += $iva_ap;
                    $sumpxq += $pxq;
                    if($cabecera->porcentaje_iva == 0){
                        if($seg == '5'){
                            $base0 += $cabecera->subtotal + $cabecera->valor_porcent_clasifi;
                        }else{
                            $base0 += $cabecera->subtotal;
                        }    
                    }else{
                        $base12 += $cabecera->subtotal;
                    }
                    if($seg == '6' || $seg == '3'){
                        $adm = $cabecera->subtotal * $cabecera->porcent_10;
                        $admin += $adm;    
                    }else{
                        $admin += $cabecera->porcentaje10;
                    }
                    //$admin += $cabecera->porcentaje10;
                    $cobertura = $cabecera->id_cobertura_comp;       
                }
                $sumpxq = round($sumpxq,2); 
                $base0 = round($base0,2); 
                $base12 = round($base12,2); 
                if($seg != '6' and $seg != '3'){
                    $admin = round($admin,2); 
                }
                if($cobertura == null){
                    $cobertura = 0;
                }
                //$sumiva_ap = round($sumiva_ap,2);
                $sumatoria[$key] = [
                    'mes_plano' => $mes_plano,
                    'pxq' => $sumpxq,
                    'iva' => $sumiva_ap,
                    'base0' => $base0,
                    'base12' => $base12,
                    'admin' => $admin,
                    'cobertura' => $cobertura,
                ];
                //dd($key, $sumpxq, $sumiva_ap, $base0, $base12, $admin);
                
            }
            //dd($sumatoria);
            $agrupados = Ap_Agrupado::where('mes_plano',$mes_plano)->where('empresa',$empresa)->where('seguro',$seg)->get();
            //dd($agrupados);

        }    
        $cant_pac = [];$base_0 = [];$base_iva = [];$base_iva = [];$v_iva = [];$amd_10 = [];
        /*$cant_pac = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->select('tseg.id', 'tseg.nombre', 'tseg.tipo')
            ->selectRaw('count(*) as cantidad')
            ->groupBy('tseg.id', 'tseg.nombre', 'tseg.tipo')
            ->orderBy('tseg.id', 'tseg.nombre', 'tseg.tipo')->get();
        //dd($cant_pac);    
        //dd($cant_pac->where('tseg.id', '1')->get(),$cant_pac->where('tseg.id', '2')->get());
        $base_0 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.porcentaje_iva', '=', '0')
            ->where('apd.estado', '1')
            ->select('tseg.id', 'tseg.nombre', 'tseg.tipo')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(apd.subtotal) as valor')
            ->groupBy('tseg.id', 'tseg.nombre', 'tseg.tipo')
            ->orderBy('tseg.id', 'tseg.nombre', 'tseg.tipo')->get();
        //dd($base_0);

        foreach ($base_0 as $value) {
            $arr_base_0[$value->id] = $value->valor;
        }
        //dd($arr_base_0);

        $base_iva = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.porcentaje_iva', '!=', '0')
            ->where('apd.estado', '1')
            ->select('tseg.id', 'tseg.nombre', 'tseg.tipo')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(apd.subtotal) as valor')
            ->groupBy('tseg.id', 'tseg.nombre', 'tseg.tipo')
            ->orderBy('tseg.id', 'tseg.nombre', 'tseg.tipo')->get();
        //dd($base_0);

        foreach ($base_iva as $value) {
            $arr_base_iva[$value->id] = $value->valor;
        }

        $v_iva = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.porcentaje_iva', '!=', '0')
            ->where('apd.estado', '1')
            ->select('tseg.id', 'tseg.nombre', 'tseg.tipo')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(apd.iva) as valor')
            ->groupBy('tseg.id', 'tseg.nombre', 'tseg.tipo')
            ->orderBy('tseg.id', 'tseg.nombre', 'tseg.tipo')->get();
        //dd($base_0);

        foreach ($v_iva as $value) {
            $arr_v_iva[$value->id] = $value->valor;
        }

        $amd_10 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->where('apd.porcent_10', '!=', '0')
            ->select('tseg.id', 'tseg.nombre', 'tseg.tipo')
            ->selectRaw('count(*) as cantidad')
            ->selectRaw('sum(apd.porcentaje10) as valor')
            ->groupBy('tseg.id', 'tseg.nombre', 'tseg.tipo')
            ->orderBy('tseg.id', 'tseg.nombre', 'tseg.tipo')->get();
        //dd($base_0);

        foreach ($amd_10 as $value) {
            $arr_amd_10[$value->id] = $value->valor;
        }*/

        $tipo_seguros = Ap_Tipo_Seg::where('estado', '1')->get();

        $empresas         = Empresa::where('estado', '1')->where('prioridad',1)->get();
        $seguro           = Seguro::where('tipo', '0')->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();
        $mes_plan         = Archivo_Plano_Cabecera::select('mes_plano')->orderBy('mes_plano')->groupBy('mes_plano')->where('estado', '1')->get();


        return view('archivo_plano/archivo/archivo_plano', ['tipo_seguros' => $tipo_seguros, 'mes_plano' => $mes_plano, 'empresas' => $empresas, 'seguros_publicos' => $seguros_publicos, 'seguro' => $seguro, 'seg' => $seg, 'empresa' => $empresa, 'mes_plan' => $mes_plan, 'arr_base_0' => $arr_base_0, 'arr_base_iva' => $arr_base_iva, 'arr_v_iva' => $arr_v_iva, 'arr_amd_10' => $arr_amd_10, 'cant_pac' => $cant_pac, 'base_0' => $base_0, 'base_iva' => $base_iva, 'v_iva' => $v_iva, 'amd_10' => $amd_10, 'tipo_seg' => $tipo_seg, 'cob_compar' => $cob_compar, 'sumatoria' => $sumatoria, 'agrupados' => $agrupados]);
    }

    public function genera_ap_anterior(Request $request)
    {

        $mes_plano  = $request->mes_plano;
        $seg        = $request->seguro;
        $tipo_seg   = $request->id_tipo_seguro;
        $cob_compar = $request->id_cobertura_comp;
        $empresa    = $request->id_empresa;

        /*$archivo_plano = Archivo_Plano_Cabecera::where('mes_plano', $mes_plano)
        ->where('id_seguro',$seg)
        ->where('id_cobertura_comp',$cob_compar)
        ->where('id_empresa',$empresa)
        ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
        ->join('ap_tipo_seg as apts','apts.codigo','tseg.tipo')
        ->where('apts.id',$tipo_seg)
        ->join('archivo_plano_detalle as apd','apd.id_ap_cabecera','archivo_plano_cabecera.id')
        ->select('archivo_plano_cabecera.id_paciente','archivo_plano_cabecera.id_usuario','archivo_plano_cabecera.fecha_ing','apd.descripcion','apd.tipo','apd.codigo','apd.cantidad','apd.valor','apd.porcentaje10','archivo_plano_cabecera.parentesco','archivo_plano_cabecera.presuntivo_def','apd.iva','archivo_plano_cabecera.cie10','tseg.tipo as tiposeg','apd.porcentaje_iva','archivo_plano_cabecera.id_hc')
        ->get();*/

        $archivo_plano_1 = [];
        $archivo_plano_2 = [];
        $archivo_plano_3 = [];
        $archivo_plano_4 = [];
        $archivo_plano_5 = [];
        $archivo_plano   = [];

        if ($tipo_seg == '1') {

            //Activo
            $archivo_plano_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            //->join('ap_tipo_seg as apts','apts.codigo','tseg.tipo')
            //->where('apts.id',$tipo_seg)
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->where('apd.estado', '1')
                ->select('archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres')
                ->get();

            //Activo Conyugue
            $archivo_plano_2 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '2')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            //->join('ap_tipo_seg as apts','apts.codigo','tseg.tipo')
            //->where('apts.id',$tipo_seg)
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->where('apd.estado', '1')
                ->select('archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres')
                ->get();

            //HIJO DE 0 - 1 AÑO
            $archivo_plano_3 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '3')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            //->join('ap_tipo_seg as apts','apts.codigo','tseg.tipo')
            //->where('apts.id',$tipo_seg)
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->where('apd.estado', '1')
                ->select('archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres')
                ->get();

            //HIJO DE 2 - 6 AÑOS
            $archivo_plano_4 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '4')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            //->join('ap_tipo_seg as apts','apts.codigo','tseg.tipo')
            //->where('apts.id',$tipo_seg)
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->where('apd.estado', '1')
                ->select('archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres')
                ->get();

            //HIJO DE 7 - 17 AÑOS
            $archivo_plano_5 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '5')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            //->join('ap_tipo_seg as apts','apts.codigo','tseg.tipo')
            //->where('apts.id',$tipo_seg)
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->where('apd.estado', '1')
                ->select('archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres')
                ->get();

        } else {
            $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', $tipo_seg)
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            //->join('ap_tipo_seg as apts','apts.codigo','tseg.tipo')
            //->where('apts.id',$tipo_seg)
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->where('apd.estado', '1')
                ->select('archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres')
                ->get();
        }

        //dd($request->all(),$archivo_plano);
        //$tipo_seguros = Db::table('tipo_seguro as ts')->join('ap_tipo_seg as apts','apts.codigo','ts.tipo')->select('apts.descripcion','apts.codigo','ts.tipo','ts.id')->orderBy('apts.descripcion')->get();
        $tipo_seguros = Ap_Tipo_Seg::where('estado', '1')->get();

        $empresas         = Empresa::where('estado', '1')->where('prioridad',1)->get();
        $seguro           = Seguro::where('tipo', '0')->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();
        $mes_plan         = Archivo_Plano_Cabecera::select('mes_plano')->orderBy('mes_plano')->groupBy('mes_plano')->where('estado', '1')->get();

        //dd($mes_plan);
        return view('archivo_plano/archivo/archivo_plano', ['tipo_seguros' => $tipo_seguros, 'mes_plano' => $mes_plano, 'empresas' => $empresas, 'seguros_publicos' => $seguros_publicos, 'seguro' => $seguro, 'archivo_plano' => $archivo_plano, 'mes_plano' => $mes_plano, 'seg' => $seg, 'tipo_seg' => $tipo_seg, 'cob_compar' => $cob_compar, 'empresa' => $empresa, 'mes_plan' => $mes_plan, 'archivo_plano_1' => $archivo_plano_1, 'archivo_plano_2' => $archivo_plano_2, 'archivo_plano_3' => $archivo_plano_3, 'archivo_plano_4' => $archivo_plano_4, 'archivo_plano_5' => $archivo_plano_5]);

    }

    public function genera_plan_consolidad(Request $request)
    {

        $cedula     = $request['cedula'];
        $nombres    = $request['paciente'];
        $mes_plano  = $request['mes_plano'];
        $id_seguro  = $request['id_seguro'];
        $id_empresa = $request['id_empresa'];

        $inf_empresa = Empresa::where('id', $id_empresa)
            ->where('estado', '1')
            ->first();

        $archivo_plano = Archivo_Plano_Cabecera::whereNotNull('fecha_ing')
            ->where('estado', '1');

        //Valida que ingrese la cedula
        if ($cedula != null) {
            $paciente = Paciente::find($cedula);
            if (!is_null($paciente)) {
                $archivo_plano = $archivo_plano->where('id_paciente', $paciente->id);

            }
        }

        //Valida que ingrese el mes de Plano
        if ($mes_plano != null) {

            $archivo_plano = $archivo_plano->where('mes_plano', $mes_plano);

        }

        //Valida que ingrese el seguro
        if ($id_seguro != null) {

            $archivo_plano = $archivo_plano->where('id_seguro', $id_seguro);
        }

        //Valida que ingrese la empresa
        if ($id_empresa != null) {
            $archivo_plano = $archivo_plano->where('id_empresa', $id_empresa);
        }

        $archivo_plano = $archivo_plano->orderby('fecha_ing');

        $archivo_plano_first = $archivo_plano->get()->first();

        $archivo_plano_last = $archivo_plano->get()->last();

        $archivo_plano_reg = $archivo_plano->get();

        //dd($archivo_plano_reg);

        $txt_texto  = null;
        $txt_texto1 = null;
        $txt_cie10  = null;
        $contador   = 0;

        foreach ($archivo_plano_reg as $value) {

            $contador = $contador + 1;

            if ($contador == 1) {

                $txt_texto1 = $value->nom_procedimiento;

            } else {

                $txt_texto1 = $txt_texto1 . ' - ' . $value->nom_procedimiento;

            }

            $cie10 = Cie_10_3::find($value->cie10);

            if (is_null($cie10)) {

                $cie10 = Cie_10_4::find($value->cie10);

                if (!is_null($cie10)) {

                    if ($contador == 1) {

                        $txt_cie10 = '(' . $value->cie10 . ')' . trim($cie10->descripcion);

                    } else {

                        $txt_cie10 = $txt_cie10 . ' - ' . '(' . $value->cie10 . ')' . trim($cie10->descripcion);

                    }

                }

            } else {

                if ($contador == 1) {

                    $txt_cie10 = '(' . $value->cie10 . ')' . trim($cie10->descripcion);

                } else {

                    $txt_cie10 = $txt_cie10 . ' - ' . '(' . $value->cie10 . ')' . trim($cie10->descripcion);
                }

            }

        }

        Excel::create('Formato Planilla Consolidado', function ($excel) use ($archivo_plano_first, $archivo_plano_last, $archivo_plano_reg, $id_seguro, $txt_texto1, $txt_cie10, $inf_empresa) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($archivo_plano_first, $archivo_plano_last, $archivo_plano_reg, $id_seguro, $txt_texto1, $txt_cie10, $inf_empresa) {

                //$sheet->mergeCells('A1:P1');
                $sheet->mergeCells('A2:D2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE DEL PRESTADOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->mergeCells('E2:P2');
                $sheet->cell('E2', function ($cell) use ($inf_empresa) {
                    $cell->setValue($inf_empresa->razonsocial);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A3:D3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->mergeCells('E3:P3');
                $sheet->cell('E3', function ($cell) use ($id_seguro) {

                    if ($id_seguro == 2) {
                        $cell->setValue('IESS');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    } elseif ($id_seguro == 5) {
                        $cell->setValue('MSP');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }

                });
                $sheet->mergeCells('A4:D4');
                $sheet->cell('A4', function ($cell) {

                    $cell->setValue('NOMBRE DEL PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->mergeCells('E4:P4');
                $sheet->cell('E4', function ($cell) use ($archivo_plano_first) {

                    $cell->setValue($archivo_plano_first->paciente->apellido1 . ' ' . $archivo_plano_first->paciente->apellido2 . ' ' . $archivo_plano_first->paciente->nombre1 . ' ' . $archivo_plano_first->paciente->nombre2);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A5:D5');
                $sheet->cell('A5', function ($cell) {

                    $cell->setValue('CÉDULA DE IDENTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->mergeCells('E5:P5');
                $sheet->cell('E5', function ($cell) use ($archivo_plano_first) {

                    $cell->setValue($archivo_plano_first->id_paciente);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A6:D6');
                $sheet->cell('A6', function ($cell) {

                    $cell->setValue('HISTORIA CLÍNICA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $hc = substr($archivo_plano_first->id_paciente, 5, 10);
                $sheet->mergeCells('E6:P6');
                $sheet->cell('E6', function ($cell) use ($hc) {

                    $cell->setValue($hc);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A7:D7');
                $sheet->cell('A7', function ($cell) {

                    $cell->setValue('FECHA DE INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $fecha_ing     = substr($archivo_plano_first->fecha_ing, 0, 10);
                $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                $sheet->mergeCells('E7:P7');
                $sheet->cell('E7', function ($cell) use ($fecha_ing_inv) {

                    $cell->setValue($fecha_ing_inv);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A8:D8');
                $sheet->cell('A8', function ($cell) {

                    $cell->setValue('FECHA DE EGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $fecha_alt     = substr($archivo_plano_last->fecha_alt, 0, 10);
                $fecha_alt_inv = date("d/m/Y", strtotime($fecha_alt));

                $sheet->mergeCells('E8:P8');
                $sheet->cell('E8', function ($cell) use ($fecha_alt_inv) {

                    $cell->setValue($fecha_alt_inv);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A9:D9');
                $sheet->cell('A9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->mergeCells('E9:P9');
                $sheet->cell('E9', function ($cell) use ($txt_texto1) {
                    // manipulate the cel
                    $cell->setValue($txt_texto1);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $sheet->mergeCells('A10:D10');
                $sheet->cell('A10', function ($cell) {

                    $cell->setValue('DIAGNÓSTICO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('E10:P10');
                $sheet->cell('E10', function ($cell) use ($txt_cie10) {

                    $cell->setValue($txt_cie10);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A11:P11');
                $sheet->cell('A11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PLANILLA DE CARGOS DEL PROVEEDOR (CONSULTA EXTERNA,HOSPITALIZACIÓN Y EMERGENCIA)');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('A12:P12');
                $sheet->cell('A12', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HONORARIOS MEDICOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('A13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE-10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('D13:J13');
                $sheet->cell('D13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M13', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N13', function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O13', function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P13', function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $i = 14; $total_honor = 0;

                $sheet->setColumnFormat(array(
                    'N' => '$ 0.00',
                    'O' => '$ 0.00',
                    'P' => '$ 0.00',

                ));

                foreach ($archivo_plano_reg as $value1) {

                    $total         = 0;
                    $honor_medicos = Db::table('archivo_plano_detalle as apd')
                        ->where('id_ap_cabecera', $value1->id)
                        ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                        ->orderby('apd.porcentaje_honorario', 'desc')
                        ->orderby('apt.secuencia', 'asc')
                        ->where('apt.tipo_ex', 'HME')
                        ->where('apd.estado', '1')
                        ->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')
                        ->get();

                    foreach ($honor_medicos as $value) {
                        $total += $value->total;
                        $fecha_honor     = substr($value->fecha, 0, 10);
                        $fecha_honor_inv = date("d/m/Y", strtotime($fecha_honor));

                        $sheet->cell('A' . $i, function ($cell) use ($fecha_honor_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_honor_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value1) {
                            // manipulate the cel
                            //$cell->setValue($value->codigo.' '.$value->tipo);
                            $cell->setValue($value1->cie10);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('D' . $i . ':J' . $i);
                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                        $sheet->cell('L' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(round($value->subtotal, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(round($value->porcentaje10, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(round($value->iva, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(round($value->total, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;

                    }

                    $total_honor = $total_honor + $total;

                }

                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('HONORARIOS MEDICOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($total_honor) {
                    // anipulate the cel
                    $cell->setValue(round($total_honor, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICINAS VALOR AL ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE-10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('D' . $i . ':J' . $i);
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = $i + 1; $total_medic_reg = 0;

                foreach ($archivo_plano_reg as $value1) {

                    $totalmed  = 0;
                    $medicinas = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value1->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'FAR')->where('apd.estado', '1')->get();

                    foreach ($medicinas as $medicina) {

                        $totalmed += $medicina->total;
                        $fecha_med     = substr($medicina->fecha, 0, 10);
                        $fecha_med_inv = date("d/m/Y", strtotime($fecha_med));

                        $sheet->cell('A' . $i, function ($cell) use ($fecha_med_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_med_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value1) {
                            // manipulate the cel
                            $cell->setValue($value1->cie10);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('C' . $i, function ($cell) use ($medicina) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('D' . $i . ':J' . $i);
                        $sheet->cell('D' . $i, function ($cell) use ($medicina) {
                            // manipulate the cel
                            $cell->setValue($medicina->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('K' . $i, function ($cell) use ($medicina) {
                            // manipulate the cel
                            $cell->setValue($medicina->cantidad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                        $sheet->cell('L' . $i, function ($cell) use ($medicina) {
                            // manipulate the cel
                            $cell->setValue($medicina->valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                        $sheet->cell('M' . $i, function ($cell) use ($medicina) {
                            // manipulate the cel
                            $cell->setValue(round($medicina->subtotal, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($medicina) {
                            // manipulate the cel
                            $cell->setValue(round($medicina->porcentaje10, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('O' . $i, function ($cell) use ($medicina) {
                            // manipulate the cel
                            $cell->setValue(round($medicina->iva, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('P' . $i, function ($cell) use ($medicina) {
                            // manipulate the cel
                            $cell->setValue(round($medicina->total, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $i++;
                    }

                    $total_medic_reg = $total_medic_reg + $totalmed;

                }

                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('MEDICINAS VALOR AL ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($total_medic_reg) {
                    // anipulate the cel
                    $cell->setValue(round($total_medic_reg, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INSUMOS - VALOR AL ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE-10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('D' . $i . ':J' . $i);
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = $i + 1; $total_ins_reg = 0;

                foreach ($archivo_plano_reg as $value1) {

                    $totalins = 0;
                    $insumos  = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value1->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'apd.valor_unitario', 'apd.porcentaje_iva')->where('apt.tipo_ex', 'IMM')->where('apd.estado', '1')->get();

                    foreach ($insumos as $insumo) {
                        $totalins += (round($insumo->subtotal, 2) + round($insumo->porcentaje10, 2) + ($insumo->subtotal * $insumo->porcentaje_iva));
                        $fecha_ins     = substr($insumo->fecha, 0, 10);
                        $fecha_ins_inv = date("d/m/Y", strtotime($fecha_ins));
                        $sheet->cell('A' . $i, function ($cell) use ($fecha_ins_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ins_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value1) {
                            // manipulate the cel
                            $cell->setValue($value1->cie10);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($insumo) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('D' . $i . ':J' . $i);
                        $sheet->cell('D' . $i, function ($cell) use ($insumo) {
                            // manipulate the cel
                            $cell->setValue($insumo->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('K' . $i, function ($cell) use ($insumo) {
                            // manipulate the cel
                            $cell->setValue($insumo->cantidad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                        $sheet->cell('L' . $i, function ($cell) use ($insumo) {
                            // manipulate the cel
                            $cell->setValue($insumo->valor_unitario);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                        $sheet->cell('M' . $i, function ($cell) use ($insumo) {
                            // manipulate the cel
                            $cell->setValue(round($insumo->subtotal, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($insumo) {
                            // manipulate the cel
                            $cell->setValue(round($insumo->porcentaje10, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('O' . $i, function ($cell) use ($insumo) {
                            // manipulate the cel
                            //$cell->setValue(round($insumo->iva,2));
                            $xviva = $insumo->subtotal * $insumo->porcentaje_iva;
                            $cell->setValue($xviva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('P' . $i, function ($cell) use ($insumo) {
                            // manipulate the cel
                            //$cell->setValue(round($insumo->total,2));
                            $xvtotal = round($insumo->subtotal, 2) + round($insumo->porcentaje10, 2) + ($insumo->subtotal * $insumo->porcentaje_iva);
                            $cell->setValue($xvtotal);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }

                    $total_ins_reg = $total_ins_reg + $totalins;
                }

                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('INSUMOS - VALOR AL ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($total_ins_reg) {
                    // anipulate the cel
                    $cell->setValue(round($total_ins_reg, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE-10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('D' . $i . ':J' . $i);
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = $i + 1; $total_lab_reg = 0;

                foreach ($archivo_plano_reg as $value1) {

                    $totallab    = 0;
                    $laboratorio = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value1->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'LAB')->where('apd.estado', '1')->get();

                    foreach ($laboratorio as $lab) {

                        $totallab += $lab->total;
                        $fecha_lab     = substr($lab->fecha, 0, 10);
                        $fecha_lab_inv = date("d/m/Y", strtotime($fecha_lab));

                        $sheet->cell('A' . $i, function ($cell) use ($fecha_lab_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_lab_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value1) {
                            // manipulate the cel
                            $cell->setValue($value1->cie10);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('C' . $i, function ($cell) use ($lab) {
                            // manipulate the cel
                            $cell->setValue($lab->codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('D' . $i . ':J' . $i);
                        $sheet->cell('D' . $i, function ($cell) use ($lab) {
                            // manipulate the cel
                            $cell->setValue($lab->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('K' . $i, function ($cell) use ($lab) {
                            // manipulate the cel
                            $cell->setValue($lab->cantidad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                        $sheet->cell('L' . $i, function ($cell) use ($lab) {
                            // manipulate the cel
                            $cell->setValue($lab->valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');

                        $sheet->cell('M' . $i, function ($cell) use ($lab) {
                            // manipulate the cel
                            $cell->setValue(round($lab->subtotal, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($lab) {
                            // manipulate the cel
                            $cell->setValue(round($lab->porcentaje10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('O' . $i, function ($cell) use ($lab) {
                            // manipulate the cel
                            $cell->setValue(round($lab->iva, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('P' . $i, function ($cell) use ($lab) {
                            // manipulate the cel
                            $cell->setValue(round($lab->total, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;

                    }

                    $total_lab_reg = $total_lab_reg + $totallab;

                }
                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($total_lab_reg) {
                    // anipulate the cel
                    $cell->setValue(round($total_lab_reg, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMAGEN(*)');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE-10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('D' . $i . ':J' . $i);
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = $i + 1; $total_ima_reg = 0;

                foreach ($archivo_plano_reg as $value1) {

                    $totalima = 0;

                    $imagen = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value1->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'IMA')->where('apd.estado', '1')->get();

                    foreach ($imagen as $ima) {
                        $totalima += $ima->total;
                        $fecha_ima     = substr($ima->fecha, 0, 10);
                        $fecha_ima_inv = date("d/m/Y", strtotime($fecha_ima));

                        $sheet->cell('A' . $i, function ($cell) use ($fecha_ima_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ima_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value1) {
                            // manipulate the cel
                            $cell->setValue($value1->cie10);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('C' . $i, function ($cell) use ($ima) {
                            // manipulate the cel
                            $cell->setValue($ima->codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('D' . $i . ':J' . $i);
                        $sheet->cell('D' . $i, function ($cell) use ($ima) {
                            // manipulate the cel
                            $cell->setValue($ima->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('K' . $i, function ($cell) use ($ima) {
                            // manipulate the cel
                            $cell->setValue($ima->cantidad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($ima) {
                            // manipulate the cel
                            $cell->setValue($ima->valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($ima) {
                            // manipulate the cel
                            $cell->setValue(round($ima->subtotal, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($ima) {
                            // manipulate the cel
                            $cell->setValue(round($ima->porcentaje10, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('O' . $i, function ($cell) use ($ima) {
                            // manipulate the cel
                            $cell->setValue(round($ima->iva, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('P' . $i, function ($cell) use ($ima) {
                            // manipulate the cel
                            $cell->setValue(round($ima->total, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;

                    }

                    $total_ima_reg = $total_ima_reg + $totalima;

                }

                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IMAGEN(*)');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($total_ima_reg) {
                    // anipulate the cel
                    $cell->setValue(round($total_ima_reg, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SERVICIOS INSTITUCIONALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE-10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('D' . $i . ':J' . $i);
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i = $i + 1; $total_serv_reg = 0;

                foreach ($archivo_plano_reg as $value1) {

                    $totalserv     = 0;
                    $servicios_ins = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value1->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'HOSP/QUIR')->where('apd.estado', '1')->get();

                    foreach ($servicios_ins as $servicio) {
                        $totalserv += $servicio->total;
                        $fecha_serv     = substr($servicio->fecha, 0, 10);
                        $fecha_serv_inv = date("d/m/Y", strtotime($fecha_serv));

                        $sheet->cell('A' . $i, function ($cell) use ($fecha_serv_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_serv_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value1) {
                            // manipulate the cel
                            $cell->setValue($value1->cie10);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('C' . $i, function ($cell) use ($servicio) {
                            // manipulate the cel
                            $cell->setValue($servicio->codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('D' . $i . ':J' . $i);
                        $sheet->cell('D' . $i, function ($cell) use ($servicio) {
                            // manipulate the cel
                            $cell->setValue($servicio->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('K' . $i, function ($cell) use ($servicio) {
                            // manipulate the cel
                            $cell->setValue($servicio->cantidad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($servicio) {
                            // manipulate the cel
                            $cell->setValue($servicio->valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($servicio) {
                            // manipulate the cel
                            $cell->setValue(round($servicio->subtotal, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($servicio) {
                            // manipulate the cel
                            $cell->setValue(round($servicio->porcentaje10, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('O' . $i, function ($cell) use ($servicio) {
                            // manipulate the cel
                            $cell->setValue(round($servicio->iva, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('P' . $i, function ($cell) use ($servicio) {
                            // manipulate the cel
                            $cell->setValue(round($servicio->total, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;

                    }

                    $total_serv_reg = $total_serv_reg + $totalserv;

                }

                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('SERVICIOS INSTITUCIONALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($total_serv_reg) {
                    // anipulate the cel
                    $cell->setValue(round($total_serv_reg, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':P' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EQUIPOS ESPECIALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = $i + 1;
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIE-10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('C' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÓDIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('D' . $i . ':J' . $i);
                $sheet->cell('D' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('IVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i = $i + 1; $total_equip_reg = 0;

                foreach ($archivo_plano_reg as $value1) {

                    $totalequip = 0;
                    $equipos    = Db::table('archivo_plano_detalle as apd')->where('id_ap_cabecera', $value1->id)->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')->select('apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total')->where('apt.tipo_ex', 'PRO/ESP')->where('apd.estado', '1')->get();

                    foreach ($equipos as $equip) {
                        $totalequip += $equip->total;
                        $fecha_equi     = substr($equip->fecha, 0, 10);
                        $fecha_equi_inv = date("d/m/Y", strtotime($fecha_equi));
                        $sheet->cell('A' . $i, function ($cell) use ($fecha_equi_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_equi_inv);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value1) {
                            // manipulate the cel
                            $cell->setValue($value1->cie10);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($equip) {
                            // manipulate the cel
                            $cell->setValue($equip->codigo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('D' . $i . ':J' . $i);
                        $sheet->cell('D' . $i, function ($cell) use ($equip) {
                            // manipulate the cel
                            $cell->setValue($equip->descripcion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('K' . $i, function ($cell) use ($equip) {
                            // manipulate the cel
                            $cell->setValue($equip->cantidad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($equip) {
                            // manipulate the cel
                            $cell->setValue($equip->valor);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($equip) {
                            // manipulate the cel
                            $cell->setValue(round($equip->subtotal, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($equip) {
                            // manipulate the cel
                            $cell->setValue(round($equip->porcentaje10, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('O' . $i, function ($cell) use ($equip) {
                            // manipulate the cel
                            $cell->setValue(round($equip->iva, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('P' . $i, function ($cell) use ($equip) {
                            // manipulate the cel
                            $cell->setValue(round($equip->total, 2));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }

                    $total_equip_reg = $total_equip_reg + $totalequip;

                }

                $i = $i;
                $sheet->mergeCells('A' . $i . ':J' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('K' . $i . ':O' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('EQUIPOS ESPECIALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P' . $i, function ($cell) use ($total_equip_reg) {
                    // anipulate the cel
                    $cell->setValue(round($total_equip_reg, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $i++;
                $sheet->mergeCells('A' . $i . ':O' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // anipulate the cel
                    $cell->setValue('TOTAL LIQUIDACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $total_liq = $total_honor + $total_medic_reg + $total_ins_reg + $total_lab_reg + $total_ima_reg + $total_serv_reg + $total_equip_reg;
                $sheet->cell('P' . $i, function ($cell) use ($total_liq) {
                    // anipulate the cel
                    $cell->setValue(round($total_liq, 2));
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(22)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(11)->setAutosize(false);

        })->export('xlsx');

    }

    public function genera_ap_excel(Request $request)
    {
        

        //ini_set('memory_limit', '1024M');
        //dd("sistemas");
        $mes_plano = $request['mes_plano'];

        $seguro     = $request->seguro;
        $tipo_seg   = $request->id_tipo_seguro;
        $cob_compar = $request['id_cobertura_comp'];
        $empresa    = $request->id_empresa;

        $archivo_plano_1 = [];
        $archivo_plano_2 = [];
        $archivo_plano_3 = [];
        $archivo_plano_4 = [];
        $archivo_plano_5 = [];
        $archivo_plano   = [];

        $texto_apellido_1 = null;
        $texto_apellido_2 = null;
        $texto_final      = null;
        $texto_fin        = null;
        $texto_nombre_1   = null;
        $texto_nombre_2   = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        $cantidad_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '1')
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->where('apd.estado', '1')
            ->groupby('pac.id')
            ->orderby('pac.id', 'asc')
            ->select('pac.id')
            ->get()->count();

        //Activo Conyugue
        $cantidad_2 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '2')
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->where('apd.estado', '1')
            ->groupby('pac.id')
            ->orderby('pac.id', 'asc')
            ->select('pac.id')
            ->get()->count();

        //HIJO DE 0 - 1 AÑO
        $cantidad_3 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '3')
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->where('apd.estado', '1')
            ->groupby('pac.id')
            ->orderby('pac.id', 'asc')
            ->select('pac.id')
            ->get()->count();

        //HIJO DE 2 - 6 AÑOS
        $cantidad_4 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '4')
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->where('apd.estado', '1')
            ->groupby('pac.id')
            ->orderby('pac.id', 'asc')
            ->select('pac.id')
            ->get()->count();

        //HIJO DE 7 - 17 AÑOS
        $cantidad_5 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '5')
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->where('apd.estado', '1')
            ->groupby('pac.id')
            ->orderby('pac.id', 'asc')
            ->select('pac.id')
            ->get()->count();

        $cantidad_6 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->where('apd.estado', '1')
            ->groupby('pac.id')
            ->orderby('pac.id', 'asc')
            ->select('pac.id')
            ->get()->count();

        $cantidad_7 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '7')
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->where('apd.estado', '1')
            ->groupby('pac.id')
            ->orderby('pac.id', 'asc')
            ->select('pac.id')
            ->get()->count();

        $cantidad_8 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
            ->where('archivo_plano_cabecera.id_seguro', $seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->where('apd.estado', '1')
            ->groupby('pac.id')
            ->orderby('pac.id', 'asc')
            ->select('pac.id')
            ->get()->count();

        if ($tipo_seg == '1') {

            //Activo
            $archivo_plano_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where(function ($query) {
                    $query->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                        ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '2')
                        ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '3')
                        ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '4')
                        ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '5');
                })
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();

            //Activo Conyugue
            /*$archivo_plano_2 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro','2')
            ->where('archivo_plano_cabecera.id_seguro',$seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp',$cob_compar)
            ->where('archivo_plano_cabecera.id_empresa',$empresa)
            ->where('archivo_plano_cabecera.estado','1')
            ->join('paciente as pac','pac.id','archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd','apd.id_ap_cabecera','archivo_plano_cabecera.id')
            ->where('apd.estado','1')
            //->orderby('pac.apellido1', 'asc')
            ->select('pac.*','archivo_plano_cabecera.id_paciente','archivo_plano_cabecera.id_usuario','archivo_plano_cabecera.fecha_ing','apd.descripcion','apd.tipo','apd.codigo','apd.cantidad','apd.valor','apd.porcentaje10','archivo_plano_cabecera.parentesco','archivo_plano_cabecera.presuntivo_def','apd.iva','archivo_plano_cabecera.cie10','tseg.tipo as tiposeg','apd.porcentaje_iva','archivo_plano_cabecera.id_hc','archivo_plano_cabecera.id_tipo_seguro','archivo_plano_cabecera.nombres')
            ->get();*/
            $archivo_plano_2 = [];

            //HIJO DE 0 - 1 AÑO
            /*$archivo_plano_3 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro','3')
            ->where('archivo_plano_cabecera.id_seguro',$seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp',$cob_compar)
            ->where('archivo_plano_cabecera.id_empresa',$empresa)
            ->where('archivo_plano_cabecera.estado','1')
            ->join('paciente as pac','pac.id','archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd','apd.id_ap_cabecera','archivo_plano_cabecera.id')
            ->where('apd.estado','1')
            //->orderby('pac.apellido1', 'asc')
            ->select('pac.*','archivo_plano_cabecera.id_paciente','archivo_plano_cabecera.id_usuario','archivo_plano_cabecera.fecha_ing','apd.descripcion','apd.tipo','apd.codigo','apd.cantidad','apd.valor','apd.porcentaje10','archivo_plano_cabecera.parentesco','archivo_plano_cabecera.presuntivo_def','apd.iva','archivo_plano_cabecera.cie10','tseg.tipo as tiposeg','apd.porcentaje_iva','archivo_plano_cabecera.id_hc','archivo_plano_cabecera.id_tipo_seguro','archivo_plano_cabecera.nombres')
            ->get();*/
            $archivo_plano_3 = [];

            //HIJO DE 2 - 6 AÑOS
            /*$archivo_plano_4 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro','4')
            ->where('archivo_plano_cabecera.id_seguro',$seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp',$cob_compar)
            ->where('archivo_plano_cabecera.id_empresa',$empresa)
            ->where('archivo_plano_cabecera.estado','1')
            ->join('paciente as pac','pac.id','archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd','apd.id_ap_cabecera','archivo_plano_cabecera.id')
            ->where('apd.estado','1')
            //->orderby('pac.apellido1', 'asc')
            ->select('pac.*','archivo_plano_cabecera.id_paciente','archivo_plano_cabecera.id_usuario','archivo_plano_cabecera.fecha_ing','apd.descripcion','apd.tipo','apd.codigo','apd.cantidad','apd.valor','apd.porcentaje10','archivo_plano_cabecera.parentesco','archivo_plano_cabecera.presuntivo_def','apd.iva','archivo_plano_cabecera.cie10','tseg.tipo as tiposeg','apd.porcentaje_iva','archivo_plano_cabecera.id_hc','archivo_plano_cabecera.id_tipo_seguro','archivo_plano_cabecera.nombres')
            ->get();*/
            $archivo_plano_4 = [];

            //HIJO DE 7 - 17 AÑOS
            /*$archivo_plano_5 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro','5')
            ->where('archivo_plano_cabecera.id_seguro',$seguro)
            ->where('archivo_plano_cabecera.id_cobertura_comp',$cob_compar)
            ->where('archivo_plano_cabecera.id_empresa',$empresa)
            ->where('archivo_plano_cabecera.estado','1')
            ->join('paciente as pac','pac.id','archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd','apd.id_ap_cabecera','archivo_plano_cabecera.id')
            ->where('apd.estado','1')
            //->orderby('pac.apellido1', 'asc')
            ->select('pac.*','archivo_plano_cabecera.id_paciente','archivo_plano_cabecera.id_usuario','archivo_plano_cabecera.fecha_ing','apd.descripcion','apd.tipo','apd.codigo','apd.cantidad','apd.valor','apd.porcentaje10','archivo_plano_cabecera.parentesco','archivo_plano_cabecera.presuntivo_def','apd.iva','archivo_plano_cabecera.cie10','tseg.tipo as tiposeg','apd.porcentaje_iva','archivo_plano_cabecera.id_hc','archivo_plano_cabecera.id_tipo_seguro','archivo_plano_cabecera.nombres')
            ->get();*/
            $archivo_plano_5 = [];

        } else {

            $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', $tipo_seg)
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'archivo_plano_cabecera.id as arplaca')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();

                //dd($archivo_plano);

        }

        Excel::create('Archivo Plano ', function ($excel) use ($archivo_plano, $archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $archivo_plano_4, $archivo_plano_5, $tipo_seg, $cantidad_1, $cantidad_2, $cantidad_3, $cantidad_4, $cantidad_5, $cantidad_6, $cantidad_7, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2, $cantidad_8) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($archivo_plano, $archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $archivo_plano_4, $archivo_plano_5, $tipo_seg, $cantidad_1, $cantidad_2, $cantidad_3, $cantidad_4, $cantidad_5, $cantidad_6, $cantidad_7, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2, $cantidad_8) {

                $i = 1; $x = 0; $id_temporal = 0;

                //jubilado
                if ($tipo_seg == '6') {

                    $x = $cantidad_1 + $cantidad_2 + $cantidad_3 + $cantidad_4 + $cantidad_5;

                    //dd($x, $cantidad_1 , $cantidad_2 , $cantidad_3 , $cantidad_4 , $cantidad_5);
                }

                //MONTEPIO
                if ($tipo_seg == '8') {

                    $x = $cantidad_1 + $cantidad_2 + $cantidad_3 + $cantidad_4 + $cantidad_5 + $cantidad_6;
                    //dd($x);
                }
                //JUB CAMPESINO
                if ($tipo_seg == '7') {

                    $x = $cantidad_1 + $cantidad_2 + $cantidad_3 + $cantidad_4 + $cantidad_5 + $cantidad_6 + $cantidad_8;
                    //dd($x);

                }
                //CAMPESINO
                if ($tipo_seg == '9') {

                    $x = 0;
                }

                if (!is_null($archivo_plano_1)) {

                    foreach ($archivo_plano_1 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$caract_remplazar = array("¨","$","%","&","/","(",")","#","@", "|", "!","/","+","-","}", "{", ">", "< ", ";", ",", ":",
                            ".","*",">","<","[","]","?","¿","º","~");*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_2)) {
                    foreach ($archivo_plano_2 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            //$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                            //$cell->setValue($value->descripcion);

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->nombres);
                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_3)) {

                    foreach ($archivo_plano_3 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_4)) {

                    foreach ($archivo_plano_4 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                            //$cell->setValue($value->descripcion);

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);
                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);
                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;
                    //$i=$i+1;

                }

                if (!is_null($archivo_plano_5)) {

                    foreach ($archivo_plano_5 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->descripcion);
                            //$cell->setValue($value->descripcion);

                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);
                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);
                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano)) {

                    foreach ($archivo_plano as $value) {
                        //dd($x, $value, $value->id_paciente, $id_temporal);
                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->descripcion);
                            //$cell->setValue($value->descripcion);

                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);
                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);
                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,4));

                            //$cell->setValue($value->iva));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }

                }

                if (count($archivo_plano_1) == 0 && count($archivo_plano) == 0) {

                    $sheet->mergeCells('A3:D3');
                    $sheet->cell('A3', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('NO SE ENCONTRARON REGISTROS');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');

                    });

                }

            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(11)->setAutosize(false);
        })->export('xlsx');
    }

    public function genera_ap_excel_092022(Request $request)
    {
        //ini_set('memory_limit', '1024M');
        //dd("sistemas");
        $mes_plano = $request['mes_plano'];
        $seguro     = $request->seguro;
        $tipo_seg   = $request->id_tipo_seguro;
        //$cob_compar = $request['id_cobertura_comp'];
        $empresa    = $request->id_empresa;

        $archivo_plano_1 = [];
        $archivo_plano_2 = [];
        $archivo_plano_3 = [];
        $archivo_plano_4 = [];
        $archivo_plano_5 = [];
        $archivo_plano   = [];

        $texto_apellido_1 = null;
        $texto_apellido_2 = null;
        $texto_final      = null;
        $texto_fin        = null;
        $texto_nombre_1   = null;
        $texto_nombre_2   = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        //ACTIVOS
        $cob_compar = null;
        $archivo_plano_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                //->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont = $archivo_plano_1->count(); 
        
        $cob_compar = null;//JM
        $archivo_plano_2 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereNotIn('archivo_plano_cabecera.id_tipo_seguro',['6','8'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_2 = $archivo_plano_2->count(); 

        $cob_compar = null;//JC
        $archivo_plano_3 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereNotIn('archivo_plano_cabecera.id_tipo_seguro',['7','9'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_3 = $archivo_plano_3->count(); 

        $cob_compar = 3;//ISFFA
        $archivo_plano_4 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereNotIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5','6','8'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_issfa = $archivo_plano_4->count();

        $cob_compar = 6;//ISSPOL
        $archivo_plano_5 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereNotIn('archivo_plano_cabecera.id_tipo_seguro',['1','2','3','4','5','6','8'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();

        $cont_isspol = $archivo_plano_5->count();

        Excel::create('Archivo Plano ', function ($excel) use ($archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $archivo_plano_4, $archivo_plano_5, $cont, $cont_2, $cont_3, $cont_issfa, $cont_isspol, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $archivo_plano_4, $archivo_plano_5, $cont,$cont_2, $cont_3, $cont_issfa, $cont_isspol, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {

                $i = 1; $x = 0; $id_temporal = 0;

                if (!is_null($archivo_plano_1)) {

                    foreach ($archivo_plano_1 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$caract_remplazar = array("¨","$","%","&","/","(",")","#","@", "|", "!","/","+","-","}", "{", ">", "< ", ";", ",", ":",
                            ".","*",">","<","[","]","?","¿","º","~");*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_2)) {
                    foreach ($archivo_plano_2 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            //$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                            //$cell->setValue($value->descripcion);

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->nombres);
                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_3)) {

                    foreach ($archivo_plano_3 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_4)) {

                    foreach ($archivo_plano_4 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_5)) {

                    foreach ($archivo_plano_5 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }
            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(11)->setAutosize(false);
        })->export('xlsx');
    }

    public function genera_ap_excel_campesino_092022(Request $request)
    {
        //ini_set('memory_limit', '1024M');
        //dd("sistemas");
        $mes_plano = $request['mes_plano'];
        $seguro     = $request->seguro;
        $tipo_seg   = $request->id_tipo_seguro;
        //$cob_compar = $request['id_cobertura_comp'];
        $empresa    = $request->id_empresa;

        $archivo_plano_1 = [];
        $archivo_plano_2 = [];
        $archivo_plano_3 = [];
        $archivo_plano_4 = [];
        $archivo_plano_5 = [];
        $archivo_plano   = [];

        $texto_apellido_1 = null;
        $texto_apellido_2 = null;
        $texto_final      = null;
        $texto_fin        = null;
        $texto_nombre_1   = null;
        $texto_nombre_2   = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        //ACTIVOS
        $cob_compar = null;
        $archivo_plano_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereIn('archivo_plano_cabecera.id_tipo_seguro',['7','9'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont = $archivo_plano_1->count(); 
        
        $cob_compar = 3;//ISFFA
        $archivo_plano_2 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereNotIn('archivo_plano_cabecera.id_tipo_seguro',['7','9'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_issfa = $archivo_plano_2->count(); 

        $cob_compar = 6;//ISSPOL
        $archivo_plano_3 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->whereNotIn('archivo_plano_cabecera.id_tipo_seguro',['7','9'])
                ->where('archivo_plano_cabecera.id_seguro', $seguro)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                ->where('apd.estado', '1')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name') , 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario')
                ->orderby('archivo_plano_cabecera.id_tipo_seguro')
                ->orderby('full_name')
                ->orderby('archivo_plano_cabecera.fecha_ing')
                ->orderby('tipo_ex.orden_plano')
                ->get();
        $cont_isspol = $archivo_plano_3->count();


        Excel::create('Archivo Plano ', function ($excel) use ($archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $cont, $cont_issfa, $cont_isspol, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($archivo_plano_1, $archivo_plano_2, $archivo_plano_3, $cont, $cont_issfa, $cont_isspol, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {

                $i = 1; $x = 0; $id_temporal = 0;

                if (!is_null($archivo_plano_1)) {

                    foreach ($archivo_plano_1 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$caract_remplazar = array("¨","$","%","&","/","(",")","#","@", "|", "!","/","+","-","}", "{", ">", "< ", ";", ",", ":",
                            ".","*",">","<","[","]","?","¿","º","~");*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_2)) {
                    foreach ($archivo_plano_2 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/

                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            //$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }
                            //$cell->setValue($value->descripcion);

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->nombres);
                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);

                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

                if (!is_null($archivo_plano_3)) {

                    foreach ($archivo_plano_3 as $value) {

                        if ($value->id_paciente != $id_temporal) {
                            $id_temporal = $value->id_paciente;
                            $x++;
                        }

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('515');
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value, $x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $fecha_ing     = substr($value->fecha_ing, 0, 10);
                        $fecha_ing_inv = date("d/m/Y", strtotime($fecha_ing));

                        $sheet->cell('C' . $i, function ($cell) use ($fecha_ing_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_ing_inv);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->tiposeg);
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2, $texto_apellido_1, $texto_apellido_2, $texto_final, $texto_fin, $texto_nombre_1, $texto_nombre_2) {
                            // manipulate the cel
                            //$cell->setValue($value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2);
                            /*$cell->setValue($value->apellido1.' '.$value->apellido2.' '.$value->nombre1.' '.$value->nombre2);*/
                            if ($value->apellido1 != 'N/A' && $value->apellido1 != '(N/A)') {
                                $texto_apellido_1 = $value->apellido1;
                            }

                            if ($value->apellido2 != 'N/A' && $value->apellido2 != '(N/A)') {
                                $texto_apellido_2 = $value->apellido2;
                            }

                            if ($value->nombre1 != 'N/A' && $value->nombre1 != '(N/A)') {
                                $texto_nombre_1 = $value->nombre1;
                            }

                            if ($value->nombre2 != 'N/A' && $value->nombre2 != '(N/A)') {
                                $texto_nombre_2 = $value->nombre2;
                            }

                            $texto_final = $texto_apellido_1 . ' ' . $texto_apellido_2 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;

                            if($value->apellido2 == '(N/A)' || $value->apellido2 == ('N/A') || $value->apellido2 == "."){
                                $texto_final = $texto_apellido_1 . ' ' . $texto_nombre_1 . ' ' . $texto_nombre_2;
                            }

                            /*$texto_final = $texto_apellido_1.' '.$texto_apellido_2.' '. $value->nombre1.' '.$value->nombre2;*/

                            $texto_fin = str_replace($caract_remplazar_1, "", $texto_final);
                            $texto_fin = str_replace($caract_remplazar_2, "N", $texto_fin);
                            $cell->setValue($texto_fin);

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->paciente->sexo == 1) {
                                $cell->setValue('M');
                            } elseif ($value->paciente->sexo == 2) {
                                $cell->setValue('F');
                            }

                        });

                        $fecha_nac     = substr($value->paciente->fecha_nacimiento, 0, 10);
                        $fecha_nac_inv = date("d/m/Y", strtotime($fecha_nac));

                        $sheet->cell('H' . $i, function ($cell) use ($fecha_nac_inv) {
                            // manipulate the cel
                            $cell->setValue($fecha_nac_inv);

                        });
                        if ($value->paciente->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad = Carbon::createFromDate(substr($value->paciente->fecha_nacimiento, 0, 4), substr($value->paciente->fecha_nacimiento, 5, 2), substr($value->paciente->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('I' . $i, function ($cell) use ($value, $edad) {
                            // manipulate the cel
                            $cell->setValue($edad);

                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'P' || $value->tipo == 'AN' || $value->tipo == 'TA') {
                                $cell->setValue('HME');

                            } elseif ($value->tipo == 'M') {
                                $cell->setValue('FAR');

                            } elseif ($value->tipo == 'S') {
                                $cell->setValue('HOSP/QUIR');

                            } elseif ($value->tipo == 'IM') {
                                $cell->setValue('IMA');

                            } elseif ($value->tipo == 'EX') {
                                $cell->setValue('LAB');

                            } elseif ($value->tipo == 'I' || $value->tipo == 'IV' || $value->tipo == 'IF' || $value->tipo == 'IVC') {
                                $cell->setValue('IMM');

                            } elseif ($value->tipo == 'PA') {
                                $cell->setValue('PAQUE');

                            } elseif ($value->tipo == 'EQ') {
                                $cell->setValue('PRO/ESP');

                            }

                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                $cell->setValue(' ');
                            } else {
                                $cell->setValue($value->codigo);
                            }

                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel

                            //$cell->setValue($value->descripcion);
                            //$descrip = str_replace($caract_remplazar,"",$value->descripcion);
                            $descrip = str_replace($caract_remplazar_1, "", $value->descripcion);
                            $descrip = str_replace($caract_remplazar_2, "N", $descrip);

                            if ($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M' || $value->tipo == 'IVC') {
                                //$cell->setValue($value->descripcion);
                                $cell->setValue($descrip);

                            } else {

                                $cell->setValue('');

                            }

                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cie10);

                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->cantidad);

                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            //$cell->setValue(round($value->valor+$value->porcentaje10,2));
                            $cell->setValue(round($value->valor, 2));

                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value) {
                            // manipulate the cel

                            if ($value->parentesco == 'TITULAR') {
                                $cell->setValue('T');

                            } elseif ($value->parentesco == 'CONYUGE') {
                                $cell->setValue('C');

                            } elseif ($value->parentesco == 'HIJO/HIJA') {
                                $cell->setValue('H');

                            } elseif ($value->parentesco == 'PARIENTE') {
                                $cell->setValue('X');

                            }

                            //$cell->setValue($value->parentesco);

                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_usuario);
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value, $caract_remplazar_1, $caract_remplazar_2) {
                            // manipulate the cel
                            //$cell->setValue($value->usuario->apellido1.' '.$value->usuario->apellido2.' '.$value->usuario->nombre1.' '.$value->usuario->nombre2);
                            //$cell->setValue($value->nombres);

                            //$descrip_nombre = str_replace($caract_remplazar,"",$value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_1, "", $value->nombres);
                            $descrip_nombre = str_replace($caract_remplazar_2, "N", $descrip_nombre);

                            $cell->setValue($descrip_nombre);

                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('CVAF');

                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('X' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('1');

                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('D');

                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('0');

                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('');

                        });
                        $sheet->cell('AD' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->porcentaje_iva);

                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            //$cell->setValue(round($value->iva,2));
                            //$nuevo_iva = $value->cantidad*$value->valor_unitario*$value->porcentaje_iva;
                            $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
                            $cell->setValue(round($nuevo_iva, 4));

                        });
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue('F');

                        });
                        $i++;
                    }
                    $i = $i;

                }

            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("T")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("U")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("V")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("W")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("X")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Y")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Z")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AA")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AB")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AE")->setWidth(11)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AF")->setWidth(11)->setAutosize(false);
        })->export('xlsx');
    }

    public function genera_planillas(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ruta = Cookie::queue('planillas', 'generar', '1000');

        $cedula = $request->cedula; //esta llegando en null la cedula ??

        $nombres = $request->nombres;

        $paciente  = null;
        $proc      = null;
        $proc2     = null;
        $consultas = null;
        if ($cedula != null) {
            $paciente = Paciente::find($cedula);

            if (!is_null($paciente)) {

                $proc = historiaclinica::where('historiaclinica.id_paciente', $paciente->id)
                    ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'historiaclinica.hcid')
                    ->join('hc_procedimiento_final as hc_pro_fin', 'hc_pro_fin.id_hc_procedimientos', 'hc_pro.id')
                    ->join('procedimiento as pro', 'pro.id', 'hc_pro_fin.id_procedimiento')
                    ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
                    ->join('seguros as s', 's.id', 'hc_pro.id_seguro')
                    ->leftjoin('empresa as emp', 'emp.id', 'hc_pro.id_empresa')
                    ->whereNotNull('pro.id_grupo_procedimiento')
                    ->where('a.estado', '1')
                    ->orderBy('hc_pro_fin.created_at', 'desc')
                    ->select('historiaclinica.hcid', 'hc_pro_fin.id_procedimiento', 'hc_pro.id_seguro', 'pro.nombre', 'a.fechaini', 'hc_pro.id_empresa', 's.nombre as nombre_seguro', 'emp.nombrecomercial', 'hc_pro.id as id_hc_proced', 'emp.nombre_corto')->get();

                $consultas = historiaclinica::where('historiaclinica.id_paciente', $paciente->id)
                    ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'historiaclinica.hcid')
                    ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
                    ->join('seguros as s', 's.id', 'hc_pro.id_seguro')
                    ->leftjoin('empresa as emp', 'emp.id', 'hc_pro.id_empresa')
                    ->orderBy('a.fechaini', 'desc')
                    ->where('a.estado', '1')
                    ->where('a.proc_consul',0)
                    ->select('historiaclinica.hcid', 'hc_pro.id_seguro', 'a.fechaini', 'hc_pro.id_empresa', 's.nombre as nombre_seguro', 'emp.nombrecomercial', 'hc_pro.id as id_hc_proced', 'emp.nombre_corto', 'a.espid', 'a.id_empresa as a_idempresa')->get();

                $proc2 = historiaclinica::where('historiaclinica.id_paciente', $paciente->id)
                    ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'historiaclinica.hcid')
                    ->join('hc_procedimiento_final as hc_pro_fin', 'hc_pro_fin.id_hc_procedimientos', 'hc_pro.id')
                    ->join('procedimiento as pro', 'pro.id', 'hc_pro_fin.id_procedimiento')
                    ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
                    ->join('seguros as s', 's.id', 'historiaclinica.id_seguro')
                    ->join('empresa as emp', 'emp.id', 'a.id_empresa')
                    ->whereNotNull('pro.id_grupo_procedimiento')
                    ->where('a.estado', '4')
                    ->orderBy('hc_pro_fin.created_at', 'desc')
                    ->select('historiaclinica.hcid', 'hc_pro_fin.id_procedimiento', 'hc_pro.id_seguro', 'pro.nombre', 'a.fechaini', 'hc_pro.id_empresa', 's.nombre as nombre_seguro', 'emp.nombrecomercial', 'hc_pro.id as id_hc_proced', 'emp.nombre_corto')->get();
                //dd($proc2);

                //dd($consultas);

            }
        }

        //dd($proc);
        return view('archivo_plano/generar/generar_planillas', ['cedula' => $cedula, 'nombres' => $nombres, 'proc' => $proc, 'paciente' => $paciente, 'consultas' => $consultas, 'proc2' => $proc2]);
    }

    public function paciente_nombre(Request $request)
    {

        $nombre_pac   = $request['term'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre_pac);
        //$seteo          = "%";
        $seteo = '%';

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
            //$seteo = $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' LIMIT 50";

        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $data[] = array('value' => $nombre->completo, 'id' => $nombre->id, 'nombres' => $nombre->completo);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }

    public function paciente_nombre2(Request $request)
    {
        $nombre_pac   = $request['paciente'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre_pac);
        //$seteo          = "%";
        $seteo = '%';

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
            //$seteo = '%'.$seteo . $value. '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' ";

        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $data[] = array('value' => $nombre->completo, 'id' => $nombre->id, 'nombres' => $nombre->completo);
        }

        if (count($data) > 0) {
            return $data[0];
        } else {
            return '0';
        }

    }

    public function planillas_generadas(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ruta = Cookie::queue('planillas', 'generadas', '1000');

        $cedula        = $request['cedula'];
        $nombres       = $request['nombres'];
        $mes_plano     = $request['mes_plano'];
        $paciente      = null;
        $archivo_plano = null;
        $id_seguro     = $request['id_seguro'];
        $id_empresa    = $request['id_empresa'];
        $seguro           = Seguro::where('tipo', '0')->get();

        $empresas = Empresa::where('estado', '1')->where('prioridad',1)->get();

        $archivo_plano = Archivo_Plano_Cabecera::whereNotNull('fecha_ing')
            ->where('estado', '1');

        if ($cedula != null) {
            $archivo_plano = $archivo_plano->where('id_paciente', $cedula);
        }

        if ($cedula != null) {
            $paciente = Paciente::find($cedula);
            if (!is_null($paciente)) {
                //$archivo_plano = $archivo_plano->where('id_paciente',$paciente->id);
                $nombres = $paciente->nombre1 . ' ' . $paciente->nombre2 . ' ' . $paciente->apellido1 . ' ' . $paciente->apellido2;
            }
        }

        if ($mes_plano != null) {

            $archivo_plano = $archivo_plano->where('mes_plano', $mes_plano);

        }

        if ($id_seguro != null) {

            $archivo_plano = $archivo_plano->where('id_seguro', $id_seguro);

        }

        if ($id_empresa != null) {

            $archivo_plano = $archivo_plano->where('id_empresa', $id_empresa);

        }

        $archivo_plano = $archivo_plano->paginate(100);
        
        /*

        if($id_seguro!=null && $mes_plano!=null && $cedula!=null && $id_empresa!=null){

        $paciente=Paciente::find($cedula);

        if(!is_null($paciente)){

        $archivo_plano = Archivo_Plano_Cabecera::whereNotNull('fecha_ing')
        ->where('id_paciente',$paciente->id)
        ->where('mes_plano',$mes_plano)
        ->where('id_seguro',$id_seguro)
        ->where('id_empresa',$id_empresa)
        ->where('estado','1')->get();

        }
        }else if($id_seguro!=null && $cedula!=null && $id_empresa!=null){

        $paciente=Paciente::find($cedula);

        if(!is_null($paciente)){

        $archivo_plano = Archivo_Plano_Cabecera::whereNotNull('fecha_ing')
        ->where('id_paciente',$paciente->id)
        ->where('id_seguro',$id_seguro)
        ->where('id_empresa',$id_empresa)
        ->where('estado','1')->get();

        }

        }*/

        return view('archivo_plano/generar/planillas_generadas', ['cedula' => $cedula, 'nombres' => $nombres, 'archivo_plano' => $archivo_plano, 'paciente' => $paciente, 'mes_plano' => $mes_plano, 'id_seguro' => $id_seguro, 'empresas' => $empresas, 'id_empresa' => $id_empresa, 'seguro' => $seguro]);
    }

    public function reportes(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $mes_plano    = $request->mes_plano;
        $seg          = $request->seguro;
        $tipo_seg     = $request->id_tipo_seguro;
        $empresa      = $request->id_empresa;
        $tipo_reporte = $request->tipo_reporte;
        //dd($tipo_reporte);
        $archivo_plano = null;
        if ($tipo_reporte == '1') {
            $archivo_plano = Archivo_Plano_Cabecera::where('mes_plano', $mes_plano)
                ->where('id_seguro', $seg)
                ->where('id_empresa', $empresa)
                ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                ->join('ap_tipo_seg as apts', 'apts.codigo', 'tseg.tipo')
                ->where('apts.id', $tipo_seg)
                ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
                ->where('apt.tipo_ex', 'HME')
                ->select('archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'tseg.tipo as tiposeg', 'archivo_plano_cabecera.nom_procedimiento', 'tseg.nombre as nom_tseg', 'apd.clasif_porcentaje_msp', 'apd.total_solicitado_usd')->get();
        }

        //dd($request->all(),$archivo_plano);
        //Se Cambio al Tipo Seguro
        //$tipo_seguros = Ap_Tipo_Seg::where('estado','1')->get();
        //$tipo_seguros = Tipo_Seguro::where('estado','1')->get();
        //dd($tipo_seguros);

        $empresas         = Empresa::where('estado', '1')->where('prioridad',1)->get();
        $seguro           = Seguro::where('tipo', '0')->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();
        $mes_plan         = Archivo_Plano_Cabecera::select('mes_plano')->orderBy('mes_plano')->groupBy('mes_plano')->where('estado', '1')->get();
        //dd($mes_plan);
        return view('archivo_plano/reportes/reportes', ['empresas' => $empresas, 'seguros_publicos' => $seguros_publicos, 'seguro' => $seguro, 'archivo_plano' => $archivo_plano, 'mes_plano' => $mes_plano, 'seg' => $seg, 'tipo_seg' => $tipo_seg, 'empresa' => $empresa, 'mes_plan' => $mes_plan]);

    }

    public function reportes_excel(Request $request)
    {
        $mes_plano = $request->mes_plano;
        //dd($mes_plano);
        $seg          = $request->seguro;
        $tipo_seg     = $request->id_tipo_seguro;
        $empresa      = $request->id_empresa;
        $tipo_reporte = $request->tipo_reporte;
        //dd($tipo_reporte);

        $archivo_plano = Archivo_Plano_Cabecera::where('mes_plano', $mes_plano)
            ->where('id_seguro', $seg)
            ->where('id_empresa', $empresa)
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('ap_tipo_seg as apts', 'apts.codigo', 'tseg.tipo')
            ->where('apts.id', $tipo_seg)
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->where('apt.tipo_ex', 'HME')
            ->select('archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'tseg.tipo as tiposeg', 'archivo_plano_cabecera.nom_procedimiento', 'tseg.nombre as nom_tseg', 'apd.clasif_porcentaje_msp', 'apd.total_solicitado_usd')->get();

        Excel::create('Reporte Honorarios Anestesiologo', function ($excel) use ($archivo_plano) {
            $excel->sheet(date('Y-m-d'), function ($sheet) use ($archivo_plano) {

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE BENEFICIARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESCRIPCION HONORARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('H1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO HNR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('I1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $i = 2;
                foreach ($archivo_plano as $value) {
                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nom_tseg);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fecha_ing, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nom_procedimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->codigo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->clasif_porcentaje_msp);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->total_solicitado_usd);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $i++;
                }

            });
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(60)->setAutosize(false);
        })->export('xlsx');

    }

    public function reporte_agrupado(Request $request)
    {
        //dd("system");
        //ini_set('memory_limit', '5000M');
        $mes_plano = $request->mes_plano;
        $seg       = $request->seguro;
        //dd($mes_plano);
        $tipo_seg     = $request->id_tipo_seguro;
        $empresa      = $request->id_empresa;
        $tipo_reporte = $request->tipo_reporte;

        $archivo_plano_activo        = [];
        $archivo_plano_conyugue      = [];
        $archivo_plano_0_1           = [];
        $archivo_plano_2_6           = [];
        $archivo_plano_7_17          = [];
        $archivo_plano_jubilado      = [];
        $archivo_plano_jub_campesino = [];
        $archivo_plano_montepio      = [];
        $archivo_plano_ssc           = [];
        $archivo_plano               = [];


        if ($tipo_seg == '10') {

            

            //ACTIVO
            $archivo_plano_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();
            //dd($archivo_plano_activo);  
              
            //Activo Conyugue
            $archivo_plano_conyugue = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '2')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();

            //HIJO DE 0 - 1 AÑO
            $archivo_plano_0_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '3')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();

            //HIJO DE 2 - 6 AÑOS
            $archivo_plano_2_6 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '4')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();

            //dd($archivo_plano_2_6);

            //HIJO DE 7 - 17 AÑOS
            $archivo_plano_7_17 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '5')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();

            //JUBILADO
            $archivo_plano_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();

            //JUBILADO CAMPESINO
            $archivo_plano_jub_campesino = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '7')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();

            //MONTEPIO
            $archivo_plano_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();

            //SSC
            $archivo_plano_ssc = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '9')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();

        } else {
            //Para todos los Tipos de Seguro
            $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', $tipo_seg)
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            //->orderby('pac.apellido1', 'asc')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();

        }

        Excel::create('ReporteAgrupadoIESS', function ($excel) use ($archivo_plano, $archivo_plano_activo, $archivo_plano_conyugue, $archivo_plano_0_1, $archivo_plano_2_6, $archivo_plano_7_17, $archivo_plano_jubilado, $archivo_plano_jub_campesino, $archivo_plano_montepio, $archivo_plano_ssc) {
            $excel->sheet('ReporteAgrupadoIESS', function ($sheet) use ($archivo_plano, $archivo_plano_activo, $archivo_plano_conyugue, $archivo_plano_0_1, $archivo_plano_2_6, $archivo_plano_7_17, $archivo_plano_jubilado, $archivo_plano_jub_campesino, $archivo_plano_montepio, $archivo_plano_ssc) {

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHAING');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHAEGR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO_SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBREBENEF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('USUARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA_CREACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('H1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $sheet->cell('I1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE 0');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $sheet->cell('J1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE 12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $sheet->cell('K1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IVA 12%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $sheet->cell('L1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GAST.ADM 10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $sheet->cell('M1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                //Muestra Todos los tipos de Seguro
                //ACTIVO
                $i = 2; $base = 0;
                if (!is_null($archivo_plano_activo)) {
                    //dd("entro");

                    foreach ($archivo_plano_activo as $value) {

                        $base_0        = 0;
                        $sum           = 0;
                        $sum_bas_0     = 0;
                        $sum_bas_iva   = 0;
                        $sum_v_iva     = 0;
                        $sum_gasto_amd = 0;
                        $total_m_iva   = 0;

                        $detalles_activo = $value->detalles->where('estado', 1);
                        //dd($detalles_activo->count());
                        

                        foreach ($detalles_activo as $detalle) {

                            //Si porcentaje_iva == 0  BASE_0
                            if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                                $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;
                                if($value->id_seguro=='5'){
                                    $sum_bas_0 = $sum_bas_0 + $detalle->valor_porcent_clasifi;
                                }

                            }

                            //Si porcentaje_iva == 0.12  BASE_IVA
                            if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                                $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                            }

                            //Suma v_iva
                            if ($detalle->estado == 1) {

                                //$sum_v_iva = $sum_v_iva+$detalle->iva;
                                //$sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);
                                //Suma v_iva
                                $sum_v_iva = $sum_v_iva + ($detalle->valor_unitario * $detalle->cantidad * $detalle->porcentaje_iva);
                            }

                            //Porcentaje_10  GAST_ADM_10
                            if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                                $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                            }

                        }
                        //dd($value);

                        

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->id_tipo_seguro == '1') {
                                $txt_cobertura = '';
                                if ($value->id_cobertura_comp != null) {
                                    $txt_cobertura = $value->cob_compartida->nombre;
                                }
                                $cell->setValue('ACTIVO ' . $txt_cobertura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);

                            if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            }
                            
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if (!is_null($value->id_usuariocrea)) {
                                $cell->setValue($value->usuario_crear->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //$base+=$value->detalles->subtotal;
                        $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $i++;
                    }

                }

                //ACTIVO (CONYUGE)
                $i = $i; $base = 0;
                if (!is_null($archivo_plano_conyugue)) {

                    foreach ($archivo_plano_conyugue as $value) {

                        $detalles_conyugue = $value->detalles;

                        $base_0        = 0;
                        $sum           = 0;
                        $sum_bas_0     = 0;
                        $sum_bas_iva   = 0;
                        $sum_v_iva     = 0;
                        $sum_gasto_amd = 0;
                        $total_m_iva   = 0;

                        foreach ($detalles_conyugue as $detalle) {

                            if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                                $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;


                            }

                            if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                                $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                            }

                            if ($detalle->estado == 1) {

                                //$sum_v_iva = $sum_v_iva+$detalle->iva;
                                //$sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);
                                $sum_v_iva = $sum_v_iva + ($detalle->valor_unitario * $detalle->cantidad * $detalle->porcentaje_iva);
                            }

                            if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                                $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                            }

                        }



                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->id_tipo_seguro == '2') {
                                $txt_cobertura = null;
                                if ($value->id_cobertura_comp != null) {
                                    $txt_cobertura = $value->cob_compartida->nombre;
                                }
                                $cell->setValue('ACTIVO (CONYUGE) ' . $txt_cobertura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if (!is_null($value->id_usuariocrea)) {
                                $cell->setValue($value->usuario_crear->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //$base+=$value->detalles->subtotal;
                        $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $i++;
                    }

                }

                //HIJO DE 0 - 1 AÑO
                $i = $i; $base = 0;
                if (!is_null($archivo_plano_0_1)) {

                    foreach ($archivo_plano_0_1 as $value) {

                        $detalles_hijo_0_1 = $value->detalles;

                        $base_0        = 0;
                        $sum           = 0;
                        $sum_bas_0     = 0;
                        $sum_bas_iva   = 0;
                        $sum_v_iva     = 0;
                        $sum_gasto_amd = 0;
                        $total_m_iva   = 0;

                        foreach ($detalles_hijo_0_1 as $detalle) {

                            if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                                $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;

                            }

                            if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                                $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                            }

                            if ($detalle->estado == 1) {

                                //$sum_v_iva = $sum_v_iva+$detalle->iva;
                                //$sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);
                                $sum_v_iva = $sum_v_iva + ($detalle->valor_unitario * $detalle->cantidad * $detalle->porcentaje_iva);
                            }

                            if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                                $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                            }

                        }

                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->id_tipo_seguro == '3') {
                                $txt_cobertura = null;
                                if ($value->id_cobertura_comp != null) {
                                    $txt_cobertura = $value->cob_compartida->nombre;
                                }
                                $cell->setValue('HIJO DE 0 - 1 AÑO ' . $txt_cobertura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            
                            $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            
                            if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if (!is_null($value->id_usuariocrea)) {
                                $cell->setValue($value->usuario_crear->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //$base+=$value->detalles->subtotal;
                        $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $i++;
                    }

                }

                //HIJO DE 2 - 6 AÑOS
                $i = $i; $base = 0;
                if (!is_null($archivo_plano_2_6)) {

                    foreach ($archivo_plano_2_6 as $value) {

                        $detalles_hijo_2_6 = $value->detalles;

                        $base_0        = 0;
                        $sum           = 0;
                        $sum_bas_0     = 0;
                        $sum_bas_iva   = 0;
                        $sum_v_iva     = 0;
                        $sum_gasto_amd = 0;
                        $total_m_iva   = 0;

                        foreach ($detalles_hijo_2_6 as $detalle) {

                            if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                                $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;

                            }

                            if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                                $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                            }

                            if ($detalle->estado == 1) {

                                //$sum_v_iva = $sum_v_iva+$detalle->iva;
                                $sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);
                            }

                            if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                                $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                            }

                        }

                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->id_tipo_seguro == '4') {
                                $txt_cobertura = null;
                                if ($value->id_cobertura_comp != null) {
                                    $txt_cobertura = $value->cob_compartida->nombre;
                                }
                                $cell->setValue('HIJO DE 2 - 6 AÑOS ' . $txt_cobertura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if (!is_null($value->id_usuariocrea)) {
                                $cell->setValue($value->usuario_crear->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //$base+=$value->detalles->subtotal;
                        $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $i++;
                    }

                }

                //HIJO DE 7 - 17 AÑOS
                $i = $i; $base = 0;
                if (!is_null($archivo_plano_7_17)) {

                    foreach ($archivo_plano_7_17 as $value) {

                        $detalles_hijo_7_17 = $value->detalles;

                        $base_0        = 0;
                        $sum           = 0;
                        $sum_bas_0     = 0;
                        $sum_bas_iva   = 0;
                        $sum_v_iva     = 0;
                        $sum_gasto_amd = 0;
                        $total_m_iva   = 0;

                        foreach ($detalles_hijo_7_17 as $detalle) {

                            if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                                $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;

                            }

                            if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                                $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                            }

                            if ($detalle->estado == 1) {

                                //$sum_v_iva = $sum_v_iva+$detalle->iva;
                                //$sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);
                                $sum_v_iva = $sum_v_iva + ($detalle->valor_unitario * $detalle->cantidad * $detalle->porcentaje_iva);
                            }

                            if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                                $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                            }

                        }

                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->id_tipo_seguro == '5') {
                                $txt_cobertura = null;
                                if ($value->id_cobertura_comp != null) {
                                    $txt_cobertura = $value->cob_compartida->nombre;
                                }
                                $cell->setValue('HIJO DE 7 - 17 AÑOS ' . $txt_cobertura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if (!is_null($value->id_usuariocrea)) {
                                $cell->setValue($value->usuario_crear->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //$base+=$value->detalles->subtotal;
                        $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $i++;
                    }

                }

                //JUBILADO
                $i = $i; $base = 0;
                if (!is_null($archivo_plano_jubilado)) {

                    foreach ($archivo_plano_jubilado as $value) {

                        $detalles_jubilado = $value->detalles;

                        $base_0        = 0;
                        $sum           = 0;
                        $sum_bas_0     = 0;
                        $sum_bas_iva   = 0;
                        $sum_v_iva     = 0;
                        $sum_gasto_amd = 0;
                        $total_m_iva   = 0;

                        foreach ($detalles_jubilado as $detalle) {

                            if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                                $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;

                            }

                            if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                                $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                            }

                            if ($detalle->estado == 1) {

                                //$sum_v_iva = $sum_v_iva+$detalle->iva;
                                //$sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);
                                $sum_v_iva = $sum_v_iva + ($detalle->valor_unitario * $detalle->cantidad * $detalle->porcentaje_iva);
                            }

                            if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                                $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                            }

                        }

                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->id_tipo_seguro == '6') {
                                $txt_cobertura = null;
                                if ($value->id_cobertura_comp != null) {
                                    $txt_cobertura = $value->cob_compartida->nombre;
                                }
                                $cell->setValue('JUBILADO ' . $txt_cobertura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if (!is_null($value->id_usuariocrea)) {
                                $cell->setValue($value->usuario_crear->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //$base+=$value->detalles->subtotal;
                        $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $i++;
                    }

                }

                //JUBILADO CAMPESINO
                $i = $i; $base = 0;
                if (!is_null($archivo_plano_jub_campesino)) {

                    foreach ($archivo_plano_jub_campesino as $value) {

                        $detalles_jub_campes = $value->detalles;

                        $base_0        = 0;
                        $sum           = 0;
                        $sum_bas_0     = 0;
                        $sum_bas_iva   = 0;
                        $sum_v_iva     = 0;
                        $sum_gasto_amd = 0;
                        $total_m_iva   = 0;

                        foreach ($detalles_jub_campes as $detalle) {

                            if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                                $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;

                            }

                            if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                                $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                            }

                            if ($detalle->estado == 1) {

                                //$sum_v_iva = $sum_v_iva+$detalle->iva;
                                //$sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);
                                $sum_v_iva = $sum_v_iva + ($detalle->valor_unitario * $detalle->cantidad * $detalle->porcentaje_iva);
                            }

                            if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                                $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                            }

                        }

                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->id_tipo_seguro == '7') {
                                $txt_cobertura = null;
                                if ($value->id_cobertura_comp != null) {
                                    $txt_cobertura = $value->cob_compartida->nombre;
                                }
                                $cell->setValue('JUBILADO CAMPESINO ' . $txt_cobertura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            }

                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if (!is_null($value->id_usuariocrea)) {
                                $cell->setValue($value->usuario_crear->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //$base+=$value->detalles->subtotal;
                        $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $i++;
                    }

                }

                //MONTEPIO
                $i = $i; $base = 0;
                if (!is_null($archivo_plano_montepio)) {

                    foreach ($archivo_plano_montepio as $value) {

                        $detalles_montepio = $value->detalles;

                        $base_0        = 0;
                        $sum           = 0;
                        $sum_bas_0     = 0;
                        $sum_bas_iva   = 0;
                        $sum_v_iva     = 0;
                        $sum_gasto_amd = 0;
                        $total_m_iva   = 0;

                        foreach ($detalles_montepio as $detalle) {

                            if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                                $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;

                            }

                            if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                                $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                            }

                            if ($detalle->estado == 1) {

                                //$sum_v_iva = $sum_v_iva+$detalle->iva;
                                $sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);

                            }

                            if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                                $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                            }

                        }

                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->id_tipo_seguro == '8') {
                                $txt_cobertura = null;
                                if ($value->id_cobertura_comp != null) {
                                    $txt_cobertura = $value->cob_compartida->nombre;
                                }
                                $cell->setValue('MONTEPIO ' . $txt_cobertura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if (!is_null($value->id_usuariocrea)) {
                                $cell->setValue($value->usuario_crear->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //$base+=$value->detalles->subtotal;
                        $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $i++;
                    }

                }

                //SSC
                $i = $i; $base = 0;
                if (!is_null($archivo_plano_ssc)) {

                    foreach ($archivo_plano_ssc as $value) {

                        $detalles_ssc = $value->detalles;

                        $base_0        = 0;
                        $sum           = 0;
                        $sum_bas_0     = 0;
                        $sum_bas_iva   = 0;
                        $sum_v_iva     = 0;
                        $sum_gasto_amd = 0;
                        $total_m_iva   = 0;

                        foreach ($detalles_ssc as $detalle) {

                            if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                                $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;

                            }

                            if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                                $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                            }

                            if ($detalle->estado == 1) {

                                //$sum_v_iva = $sum_v_iva+$detalle->iva;
                                //$sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);
                                $sum_v_iva = $sum_v_iva + ($detalle->valor_unitario * $detalle->cantidad * $detalle->porcentaje_iva);

                            }

                            if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                                $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                            }

                        }

                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->id_tipo_seguro == '9') {
                                $txt_cobertura = null;
                                if ($value->id_cobertura_comp != null) {
                                    $txt_cobertura = $value->cob_compartida->nombre;
                                }
                                $cell->setValue('SSC ' . $txt_cobertura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });

                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if (!is_null($value->id_usuariocrea)) {
                                $cell->setValue($value->usuario_crear->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }

                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_procedimiento);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //$base+=$value->detalles->subtotal;
                        $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                        $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $i++;
                    }

                }

                //Consulta de Archivo Plano Por Diferente Tipo de Seguro
                $i = 2; $base = 0;
                foreach ($archivo_plano as $value) {

                    $detalles = $value->detalles;

                    $base_0        = 0;
                    $sum           = 0;
                    $sum_bas_0     = 0;
                    $sum_bas_iva   = 0;
                    $sum_v_iva     = 0;
                    $sum_gasto_amd = 0;
                    $total_m_iva   = 0;

                    foreach ($detalles as $detalle) {

                        if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                            $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;

                        }

                        if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                            $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                        }

                        if ($detalle->estado == 1) {

                            //$sum_v_iva = $sum_v_iva+$detalle->iva;
                            //$sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);
                            $sum_v_iva = $sum_v_iva + ($detalle->valor_unitario * $detalle->cantidad * $detalle->porcentaje_iva);

                        }

                        if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                            $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                        }

                    }

                    $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $txt_cobertura = null;
                        if ($value->id_cobertura_comp != null) {
                            $txt_cobertura = $value->cob_compartida->nombre;
                        }

                        if ($value->id_tipo_seguro == '1') {
                            $cell->setValue('ACTIVO ' . $txt_cobertura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        } elseif ($value->id_tipo_seguro == '2') {
                            $cell->setValue('ACTIVO (CONYUGE) ' . $txt_cobertura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        } elseif ($value->id_tipo_seguro == '3') {
                            $cell->setValue('HIJO DE 0 - 1 AÑO ' . $txt_cobertura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        } elseif ($value->id_tipo_seguro == '4') {
                            $cell->setValue('HIJO DE 2 - 6 AÑOS ' . $txt_cobertura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        } elseif ($value->id_tipo_seguro == '5') {
                            $cell->setValue('HIJO DE 7 - 17 AÑOS ' . $txt_cobertura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        } elseif ($value->id_tipo_seguro == '6') {
                            $cell->setValue('JUBILADO ' . $txt_cobertura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        } elseif ($value->id_tipo_seguro == '7') {
                            $cell->setValue('JUBILADO CAMPESINO ' . $txt_cobertura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        } elseif ($value->id_tipo_seguro == '8') {
                            $cell->setValue('MONTEPIO ' . $txt_cobertura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        } elseif ($value->id_tipo_seguro == '9') {
                            $cell->setValue('SSC ' . $txt_cobertura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        }

                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                        if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                            }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if (!is_null($value->id_usuariocrea)) {
                            $cell->setValue($value->usuario_crear->apellido1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }

                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nom_procedimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //$base+=$value->detalles->subtotal;
                    $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                    $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_0);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                    $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_iva);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                    $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_v_iva);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                    $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                        // manipulate the cel
                        $cell->setValue($sum_gasto_amd);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                    $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $i++;
                }

            });
        })->export('xlsx');

    }

    /*public function reporte_cuenta_iess(Request $request){

    Excel::create('ReportePIIESSDetallado', function ($excel) use($archivo_plano){
    $excel->sheet('ReportePIIESSDetallado', function ($sheet) use($archivo_plano){

    $sheet->cell('A', function ($cell) {
    // manipulate the cel
    $cell->setValue('TIPOSEG');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');

    });
    $sheet->cell('B', function ($cell) {
    // manipulate the cel
    $cell->setValue('TIPO_SEGURO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');

    });
    $sheet->cell('C', function ($cell) {
    // manipulate the cel
    $cell->setValue('N_EXP');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');

    });
    $sheet->cell('D', function ($cell) {
    // manipulate the cel
    $cell->setValue('BASE_0');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');

    });
    $sheet->cell('E', function ($cell) {
    // manipulate the cel
    $cell->setValue('BASE_IVA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');

    });
    $sheet->cell('F', function ($cell) {
    // manipulate the cel
    $cell->setValue('V_IVA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');

    });
    $sheet->cell('G', function ($cell) {
    // manipulate the cel
    $cell->setValue('GAST_AMD_10');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');

    });

    $sheet->cell('H', function ($cell) {
    // manipulate the cel
    $cell->setValue('TOTAL_M_IVA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');

    });

    })->export('xlsx');
    }

    }*/

    public function reporte_consolidado_iess(Request $request)
    {

        //dd("kaka");
        //ini_set('memory_limit', '10024M');
        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $tipo_seg  = $request['id_tipo_seguro'];
        $empresa   = $request['id_empresa'];

        $sub_cadena_1 = '';
        $sub_cadena_2 = '';

        $cantidad_1_activo          = 0;
        $cantidad_2_activo_conyugue = 0;
        $cantidad_0_1               = 0;
        $cantidad_2_6               = 0;
        $cantidad_7_17              = 0;
        $cantidad_jubilado          = 0;
        $cantidad_montepio          = 0;

        $archivo_plano_activo         = [];
        $archivo_plan_activo_conyugue = [];
        $archivo_plano_0_1            = [];
        $archivo_plano_2_6            = [];
        $archivo_plano_7_17           = [];
        $archivo_plano_jubilado       = [];
        $archivo_plano_jub_campesino  = [];
        $archivo_plano_montepio       = [];

        $texto_nombre = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        if (!is_null($mes_plano)) {
            $sub_cadena_1 = substr($mes_plano, 0, 2);
        }

        if (!is_null($mes_plano)) {
            $sub_cadena_2 = substr($mes_plano, 2, 6);
        }

        $inf_empresa = Empresa::where('id', $empresa)->where('estado', '1')->first();

        //REPORTE CONSOLIDADO IEESS GENERAL
        //ACTIVO  SG
        $archivo_plano_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where(function ($query) {
                $query->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '2')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '3')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '4')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '5');
            })
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO JU
        $archivo_plano_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO CAMPESINO JC
        $archivo_plano_jub_campesino = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '7')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //MONTEPIO MO
        $archivo_plano_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        Excel::create('ReporteConsolidadoIESS', function ($excel) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $cantidad_1_activo, $cantidad_2_activo_conyugue, $cantidad_0_1, $cantidad_2_6, $cantidad_7_17, $cantidad_jubilado, $cantidad_montepio, $archivo_plano_activo, $archivo_plano_jubilado, $archivo_plano_jub_campesino, $archivo_plano_montepio, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre) {
            $excel->sheet('ReporteConsolidadoIESS', function ($sheet) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $cantidad_1_activo, $cantidad_2_activo_conyugue, $cantidad_0_1, $cantidad_2_6, $cantidad_7_17, $cantidad_jubilado, $cantidad_montepio, $archivo_plano_activo, $archivo_plano_jubilado, $archivo_plano_jub_campesino, $archivo_plano_montepio, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre) {

                $sheet->setColumnFormat(array(
                    'I' => '$### ### ### ##0.00',
                    'K' => '$### ### ### ##0.00',
                    'M' => '$### ### ### ##0.00',
                    'N' => '$### ### ### ##0.00',
                    'O' => '$### ### ### ##0.00',
                ));

                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(base_path() . '/storage/app/logo/iess_logo.png');
                $objDrawing->setCoordinates('N2');
                $objDrawing->setHeight(70);
                $objDrawing->setWorksheet($sheet);

                $sheet->mergeCells('A2:N2');
                $sheet->cell('A2', function ($cell) use ($inf_empresa) {
                    $cell->setValue('INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                });

                $sheet->mergeCells('A4:N4');
                $sheet->cell('A4', function ($cell) use ($inf_empresa) {
                    $cell->setValue('COORDINACIÓN PROVINCIAL DE PRESTACIONES DEL SEGURO DE SALUD MANABI'); 
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                });

                $sheet->mergeCells('A6:P6');
                $sheet->cell('A6', function ($cell) use ($inf_empresa) {
                    $cell->setValue('PLANILLA CONSOLIDADO DE PRESTACION DE SERVICIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A7:B7');
                $sheet->cell('A7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre prestador:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C7:P7');
                $sheet->cell('C7', function ($cell) use ($inf_empresa) {
                    $cell->setValue($inf_empresa->razonsocial);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A8:B8');
                $sheet->cell('A8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Teléfono:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C8:P8');
                $sheet->cell('C8', function ($cell) use ($inf_empresa) {
                    $cell->setValue('(04)2 109 180');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A9:B9');
                $sheet->cell('A9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Correo::');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C9:P9');
                $sheet->cell('C9', function ($cell) use ($inf_empresa) {
                    $cell->setValue('gerenciageneral@ieced.ec');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A10:B10');
                $sheet->cell('A10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Servicio:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C10:P10');
                $sheet->cell('C10', function ($cell) use ($inf_empresa) {
                    $cell->setValue('AMBULATORIO');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A11:B11');
                $sheet->cell('A11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Mes:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C11:I11');
                $sheet->cell('C11', function ($cell) use ($sub_cadena_1) {

                    $txt_mes = '';

                    if ($sub_cadena_1 == '01') {
                        $txt_mes = 'ENERO';
                    } elseif ($sub_cadena_1 == '02') {
                        $txt_mes = 'FEBRERO';
                    } elseif ($sub_cadena_1 == '03') {
                        $txt_mes = 'MARZO';
                    } elseif ($sub_cadena_1 == '04') {
                        $txt_mes = 'ABRIL';
                    } elseif ($sub_cadena_1 == '05') {
                        $txt_mes = 'MAYO';
                    } elseif ($sub_cadena_1 == '06') {
                        $txt_mes = 'JUNIO';
                    } elseif ($sub_cadena_1 == '07') {
                        $txt_mes = 'JULIO';
                    } elseif ($sub_cadena_1 == '08') {
                        $txt_mes = 'AGOSTO';
                    } elseif ($sub_cadena_1 == '09') {
                        $txt_mes = 'SEPTIEMBRE';
                    } elseif ($sub_cadena_1 == '10') {
                        $txt_mes = 'OCTUBRE';
                    } elseif ($sub_cadena_1 == '11') {
                        $txt_mes = 'NOVIEMBRE';
                    } elseif ($sub_cadena_1 == '12') {
                        $txt_mes = 'DICIEMBRE';
                    }

                    $cell->setValue($txt_mes);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $sheet->mergeCells('J11:P11');
                $sheet->cell('J11', function ($cell) use ($sub_cadena_2) {
                    // manipulate the cel
                    $cell->setValue('Año:' . ($sub_cadena_2));
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*$sheet->mergeCells('k11:P11');
                $sheet->cell('K11', function($cell) use($sub_cadena_2){
                $cell->setValue($sub_cadena_2);
                $cell->setAlignment('left');
                $cell->setFontSize('10');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });*/

                $sheet->cell('A12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('No');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B12:G12');
                $sheet->cell('B12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('NOMBRE DE PACIENTE/USUARIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('TIPO DE SEGURO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I12:J12');
                $sheet->cell('I12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE0');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K12:L12');
                $sheet->cell('K12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE12');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('VALOR IVA 12%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('GASTO ADM');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('O12:P12');
                $sheet->cell('O12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i           = 13;
                $base        = 0;
                $id_temporal = 0;
                $x           = 1;
                $sub_total   = 0;
                $iva         = 0;
                $total       = 0;

                $total_activo     = 0;
                $iva_activo       = 0;
                $sub_total_activo = 0;

                $total_conyugue     = 0;
                $iva_conyugue       = 0;
                $sub_total_conyugue = 0;

                $total_0_1     = 0;
                $iva_0_1       = 0;
                $sub_total_0_1 = 0;

                $total_2_6     = 0;
                $iva_2_6       = 0;
                $sub_total_2_6 = 0;

                $total_7     = 0;
                $iva_7       = 0;
                $sub_total_7 = 0;

                $total_jub     = 0;
                $iva_jub       = 0;
                $sub_total_jub = 0;

                $total_jub_camp     = 0;
                $iva_jub_camp       = 0;
                $sub_total_jub_camp = 0;

                $total_montepio     = 0;
                $iva_montepio       = 0;
                $sub_total_montepio = 0;

                $total_general     = 0;
                $sub_total_general = 0;
                $iva_general       = 0;

                $acum_bas_0 = 0; $acum_bas_iva = 0; $acum_v_iva = 0; $acum_gasto_amd = 0;
                $value      = null;

                //MUESTRA ACTIVO  SG
                if (!is_null($archivo_plano_activo)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$total_activo = 0;
                    //$iva_activo = 0;
                    $id_temporal = 0;
                   
                    foreach ($archivo_plano_activo as $value) {
                        

                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {
                                

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                /*if($x=='99'){
                                dd($i,$nombre_anterior);
                                }*/

                                $total_activo += $total_m_iva;
                                $iva_activo += $sum_v_iva;
                                $sub_total_activo = $total_activo - $iva_activo;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //$acum_v_iva += $value->iva;
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //Escribo en excel
                    //No
                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //NOMBRE DE PACIENTE/USUARIO
                    $sheet->mergeCells('B' . $i . ':G' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                        // manipulate the cel
                        $cell->setValue($nombre_anterior);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //TIPO DE SEGURO
                    $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                        // manipulate the cel

                        $cell->setValue($tipo_seguro_ant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE0
                    $sheet->mergeCells('I' . $i . ':J' . $i);
                    $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_0);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE12
                    $sheet->mergeCells('K' . $i . ':L' . $i);
                    $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //VALOR IVA 12%
                    $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_v_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //GASTO ADM
                    $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                        // manipulate the cel
                        $cell->setValue($sum_gasto_amd);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //SUBTOTAL
                    $sheet->mergeCells('O' . $i . ':P' . $i);
                    $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //dd($nombre_anterior,$i,$x);

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $x++;
                    $i++;
                    //dd($x,$i);
                }

                //JUBILADO
                if (!is_null($archivo_plano_jubilado)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_jubilado as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                $total_jub += $total_m_iva;
                                $iva_jub += $sum_v_iva;
                                $sub_total_jub = $total_jub - $iva_jub;
                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;
                            //dd($value,$id_temporal);

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$x++;
                    //$i++;
                }

                //MONTEPIO
                if (!is_null($archivo_plano_montepio)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_montepio as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_montepio += $total_m_iva;
                                $iva_montepio += $sum_v_iva;
                                $sub_total_montepio = $total_montepio - $iva_montepio;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;
                            //dd($value,$id_temporal);

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel
                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $x++;
                        $i++;

                    }

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$x++;
                    //$i++;
                }

                //JUBILADO CAMPESINO
                if (!is_null($archivo_plano_jub_campesino)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_jub_campesino as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    //$cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    //$cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_jub_camp += $total_m_iva;
                                $iva_jub_camp += $sum_v_iva;
                                $sub_total_jub_camp = $total_jub_camp - $iva_jub_camp;
                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;
                            //dd($value,$id_temporal);

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$x++;
                    //$i++;

                }

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                //dd($acum_bas_0 ,$acum_bas_iva ,$acum_v_iva ,$acum_gasto_amd);
                $subtotal_final = $acum_bas_0 + $acum_bas_iva + $acum_gasto_amd;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $subtotal_final) {
                    // manipulate the cel
                    $cell->setValue($subtotal_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('IVA:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $acum_v_iva) {
                    // manipulate the cel
                    $cell->setValue($acum_v_iva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $total_final = $subtotal_final + $acum_v_iva;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $total_final) {
                    // manipulate the cel
                    $cell->setValue($total_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $i = $i + 7;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('t', '', 'thin', '');

                });
                $i++;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('FIRMA / SELLO PRESTADOR');
                    $cell->setAlignment('center');
                    $cell->setBorder('t', '', '', '');

                });

                //$excel->getActiveSheet()->getColumnDimension("A")->setWidth(10)->setAutosize(false);
                //$excel->getActiveSheet()->getColumnDimension("B")->setWidth(10)->setAutosize(false);

                /*
            $sheet->mergeCells('G'.$i.':J'.$i);
            $sheet->cell('G'.$i, function ($cell) use($value){
            // manipulate the cel
            $cell->setValue('TOTAL');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');

            });
            $sheet->mergeCells('G'.$i.':J'.$i);
            $sheet->cell('G'.$i, function ($cell) use($value){
            // manipulate the cel
            $cell->setValue('FIRMA / SELLO PRESTADOR');
            $cell->setBorder('thin', 'thin', 'thin', 'thin');

            });*/

            });
        })->export('xlsx');

    }

    public function reporte_consolidado_campesino(Request $request)
    {

        $mes_plano    = $request['mes_plano'];
        $seg          = $request['seguro'];
        $tipo_seg     = $request['id_tipo_seguro'];
        $empresa      = $request['id_empresa'];
        $sub_cadena_1 = '';
        $sub_cadena_2 = '';

        $texto_nombre = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        if (!is_null($mes_plano)) {
            $sub_cadena_1 = substr($mes_plano, 0, 2);
        }

        if (!is_null($mes_plano)) {
            $sub_cadena_2 = substr($mes_plano, 2, 6);
        }

        $inf_empresa = Empresa::where('id', $empresa)->where('estado', '1')->first();

        $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '9')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        Excel::create('ReporteConsolidadoCampesino', function ($excel) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $archivo_plano, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre) {
            $excel->sheet('ReporteConsolidadoCampesino', function ($sheet) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $archivo_plano, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre) {

                $sheet->setColumnFormat(array(
                    'I' => '$### ### ### ##0.00',
                    'K' => '$### ### ### ##0.00',
                    'M' => '$### ### ### ##0.00',
                    'N' => '$### ### ### ##0.00',
                    'O' => '$### ### ### ##0.00',
                ));

                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(base_path() . '/storage/app/logo/iess_logo.png');
                $objDrawing->setCoordinates('N2');
                $objDrawing->setHeight(70);
                $objDrawing->setWorksheet($sheet);

                $sheet->mergeCells('A2:N2');
                $sheet->cell('A2', function ($cell) use ($inf_empresa) {
                    $cell->setValue('INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                });

                $sheet->mergeCells('A4:N4');
                $sheet->cell('A4', function ($cell) use ($inf_empresa) {
                    $cell->setValue('COORDINACIÓN PROVINCIAL DE PRESTACIONES DEL SEGURO DE SALUD GUAYAS');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                });

                $sheet->mergeCells('A6:P6');
                $sheet->cell('A6', function ($cell) use ($inf_empresa) {
                    $cell->setValue('PLANILLA CONSOLIDADO DE PRESTACION DE SERVICIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A7:B7');
                $sheet->cell('A7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre prestador:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C7:P7');
                $sheet->cell('C7', function ($cell) use ($inf_empresa) {
                    $cell->setValue($inf_empresa->razonsocial);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A8:B8');
                $sheet->cell('A8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Teléfono:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C8:P8');
                $sheet->cell('C8', function ($cell) use ($inf_empresa) {
                    $cell->setValue('(04)2 109 180');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A9:B9');
                $sheet->cell('A9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Correo::');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C9:P9');
                $sheet->cell('C9', function ($cell) use ($inf_empresa) {
                    $cell->setValue('gerenciageneral@ieced.ec');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A10:B10');
                $sheet->cell('A10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Servicio:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C10:P10');
                $sheet->cell('C10', function ($cell) use ($inf_empresa) {
                    $cell->setValue('AMBULATORIO');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A11:B11');
                $sheet->cell('A11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Mes:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C11:I11');
                $sheet->cell('C11', function ($cell) use ($sub_cadena_1) {

                    $txt_mes = '';

                    if ($sub_cadena_1 == '01') {
                        $txt_mes = 'ENERO';
                    } elseif ($sub_cadena_1 == '02') {
                        $txt_mes = 'FEBRERO';
                    } elseif ($sub_cadena_1 == '03') {
                        $txt_mes = 'MARZO';
                    } elseif ($sub_cadena_1 == '04') {
                        $txt_mes = 'ABRIL';
                    } elseif ($sub_cadena_1 == '05') {
                        $txt_mes = 'MAYO';
                    } elseif ($sub_cadena_1 == '06') {
                        $txt_mes = 'JUNIO';
                    } elseif ($sub_cadena_1 == '07') {
                        $txt_mes = 'JULIO';
                    } elseif ($sub_cadena_1 == '08') {
                        $txt_mes = 'AGOSTO';
                    } elseif ($sub_cadena_1 == '09') {
                        $txt_mes = 'SEPTIEMBRE';
                    } elseif ($sub_cadena_1 == '10') {
                        $txt_mes = 'OCTUBRE';
                    } elseif ($sub_cadena_1 == '11') {
                        $txt_mes = 'NOVIEMBRE';
                    } elseif ($sub_cadena_1 == '12') {
                        $txt_mes = 'DICIEMBRE';
                    }

                    $cell->setValue($txt_mes);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $sheet->mergeCells('J11:P11');
                $sheet->cell('J11', function ($cell) use ($sub_cadena_2) {
                    // manipulate the cel
                    $cell->setValue('Año:' . ($sub_cadena_2));
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*$sheet->mergeCells('k11:P11');
                $sheet->cell('K11', function($cell) use($sub_cadena_2){
                $cell->setValue($sub_cadena_2);
                $cell->setAlignment('left');
                $cell->setFontSize('10');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });*/

                $sheet->cell('A12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('No');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B12:G12');
                $sheet->cell('B12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('NOMBRE DE PACIENTE/USUARIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('TIPO DE SEGURO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I12:J12');
                $sheet->cell('I12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE0');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K12:L12');
                $sheet->cell('K12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE12');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('VALOR IVA 12%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('GASTO ADM');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('O12:P12');
                $sheet->cell('O12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $acum_bas_0 = 0; $acum_bas_iva = 0; $acum_v_iva = 0; $acum_gasto_amd = 0; $i = 13; $x = 1; $total_jub_camp = 0; $iva_jub_camp = 0;

                if (!is_null($archivo_plano)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    //$cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    //$cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //dd($sum_v_iva);
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total_jub_camp += $total_m_iva;
                                $iva_jub_camp += $sum_v_iva;
                                $sub_total_jub_camp = $total_jub_camp - $iva_jub_camp;
                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;
                            //dd($value,$id_temporal);

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;

                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);

                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            //$cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    //Fin de Escritura del Excel
                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    //$x++;
                    //$i++;

                }

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                //dd($acum_bas_0 ,$acum_bas_iva ,$acum_v_iva ,$acum_gasto_amd);
                $subtotal_final = $acum_bas_0 + $acum_bas_iva + $acum_gasto_amd;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $subtotal_final) {
                    // manipulate the cel
                    $cell->setValue($subtotal_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('IVA:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $acum_v_iva) {
                    // manipulate the cel
                    $cell->setValue($acum_v_iva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $total_final = $subtotal_final + $acum_v_iva;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $total_final) {
                    // manipulate the cel
                    $cell->setValue($total_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $i = $i + 7;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('t', '', 'thin', '');

                });
                $i++;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('FIRMA / SELLO PRESTADOR');
                    $cell->setAlignment('center');
                    $cell->setBorder('t', '', '', '');

                });

            });
        })->export('xlsx');

    }

    public function reporte_cobertura_issfa(Request $request)
    {

        $mes_plano         = $request['mes_plano'];
        $seg               = $request['seguro'];
        $tipo_seg          = $request['id_tipo_seguro'];
        $empresa           = $request['id_empresa'];
        $cobert_compartida = $request['id_cobertura_comp'];
        $sub_cadena_1      = '';
        $sub_cadena_2      = '';

        $archivo_plano_activo        = [];
        $archivo_plano_jubilado      = [];
        $archivo_plano_jub_campesino = [];
        $archivo_plano_montepio      = [];

        $texto_nombre = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        if (!is_null($mes_plano)) {
            $sub_cadena_1 = substr($mes_plano, 0, 2);
        }

        if (!is_null($mes_plano)) {
            $sub_cadena_2 = substr($mes_plano, 2, 6);
        }

        $inf_empresa = Empresa::where('id', $empresa)->where('estado', '1')->first();

        //REPORTE CONSOLIDADO ISSFA
        $archivo_plano_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where(function ($query) {
                $query->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '2')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '3')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '4')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '5');
            })
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '3')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO JU
        $archivo_plano_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '3')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO CAMPESINO JC
        $archivo_plano_jub_campesino = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '7')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '3')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //MONTEPIO MO
        $archivo_plano_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '3')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        Excel::create('Reporte Consolidado. ISSFA', function ($excel) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $mes_plano, $archivo_plano_activo, $archivo_plano_jubilado, $archivo_plano_jub_campesino, $archivo_plano_montepio, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre) {
            $excel->sheet('Reporte Consolidado. ISSFA', function ($sheet) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $mes_plano, $archivo_plano_activo, $archivo_plano_jubilado, $archivo_plano_jub_campesino, $archivo_plano_montepio, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre) {

                $sheet->setColumnFormat(array(
                    'I' => '$### ### ### ##0.00',
                    'K' => '$### ### ### ##0.00',
                    'M' => '$### ### ### ##0.00',
                    'N' => '$### ### ### ##0.00',
                    'O' => '$### ### ### ##0.00',
                ));

                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(base_path() . '/storage/app/logo/iess_logo.png');
                $objDrawing->setCoordinates('N2');
                $objDrawing->setHeight(70);
                $objDrawing->setWorksheet($sheet);

                $sheet->mergeCells('A2:N2');
                $sheet->cell('A2', function ($cell) use ($inf_empresa) {
                    $cell->setValue('INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                });

                $sheet->mergeCells('A4:N4');
                $sheet->cell('A4', function ($cell) use ($inf_empresa) {
                    $cell->setValue('COORDINACIÓN PROVINCIAL DE PRESTACIONES DEL SEGURO DE SALUD GUAYAS');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                });

                $sheet->mergeCells('A6:P6');
                $sheet->cell('A6', function ($cell) use ($inf_empresa) {
                    $cell->setValue('PLANILLA CONSOLIDADO DE PRESTACION DE SERVICIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A7:B7');
                $sheet->cell('A7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre prestador:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C7:P7');
                $sheet->cell('C7', function ($cell) use ($inf_empresa) {
                    $cell->setValue($inf_empresa->razonsocial);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A8:B8');
                $sheet->cell('A8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Teléfono:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C8:P8');
                $sheet->cell('C8', function ($cell) use ($inf_empresa) {
                    $cell->setValue('(04)2 109 180');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A9:B9');
                $sheet->cell('A9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Correo::');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C9:P9');
                $sheet->cell('C9', function ($cell) use ($inf_empresa) {
                    $cell->setValue('gerenciageneral@ieced.ec');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A10:B10');
                $sheet->cell('A10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Servicio:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C10:P10');
                $sheet->cell('C10', function ($cell) use ($inf_empresa) {
                    $cell->setValue('AMBULATORIO');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A11:B11');
                $sheet->cell('A11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Mes:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C11:I11');
                $sheet->cell('C11', function ($cell) use ($sub_cadena_1) {

                    $txt_mes = '';

                    if ($sub_cadena_1 == '01') {
                        $txt_mes = 'ENERO';
                    } elseif ($sub_cadena_1 == '02') {
                        $txt_mes = 'FEBRERO';
                    } elseif ($sub_cadena_1 == '03') {
                        $txt_mes = 'MARZO';
                    } elseif ($sub_cadena_1 == '04') {
                        $txt_mes = 'ABRIL';
                    } elseif ($sub_cadena_1 == '05') {
                        $txt_mes = 'MAYO';
                    } elseif ($sub_cadena_1 == '06') {
                        $txt_mes = 'JUNIO';
                    } elseif ($sub_cadena_1 == '07') {
                        $txt_mes = 'JULIO';
                    } elseif ($sub_cadena_1 == '08') {
                        $txt_mes = 'AGOSTO';
                    } elseif ($sub_cadena_1 == '09') {
                        $txt_mes = 'SEPTIEMBRE';
                    } elseif ($sub_cadena_1 == '10') {
                        $txt_mes = 'OCTUBRE';
                    } elseif ($sub_cadena_1 == '11') {
                        $txt_mes = 'NOVIEMBRE';
                    } elseif ($sub_cadena_1 == '12') {
                        $txt_mes = 'DICIEMBRE';
                    }

                    $cell->setValue($txt_mes);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $sheet->mergeCells('J11:P11');
                $sheet->cell('J11', function ($cell) use ($sub_cadena_2) {
                    // manipulate the cel
                    $cell->setValue('Año:' . ($sub_cadena_2));
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*$sheet->mergeCells('k11:P11');
                $sheet->cell('K11', function($cell) use($sub_cadena_2){
                $cell->setValue($sub_cadena_2);
                $cell->setAlignment('left');
                $cell->setFontSize('10');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });*/

                $sheet->cell('A12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('No');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B12:G12');
                $sheet->cell('B12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('NOMBRE DE PACIENTE/USUARIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('TIPO DE SEGURO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I12:J12');
                $sheet->cell('I12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE0');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K12:L12');
                $sheet->cell('K12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE12');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('VALOR IVA 12%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('GASTO ADM');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('O12:P12');
                $sheet->cell('O12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i          = 13; $base          = 0; $id_temporal          = 0; $x          = 1; $sub_total          = 0; $iva          = 0; $total          = 0;
                $acum_bas_0 = 0; $acum_bas_iva = 0; $acum_v_iva = 0; $acum_gasto_amd = 0;
                $value      = null;

                //TODOS LOS ACTIVOS
                if (!is_null($archivo_plano_activo)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_activo as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //$total+=$total_m_iva;
                                //$iva+=$sum_v_iva;
                                //$sub_total = $total-$iva;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    if ($nombre_anterior != '') {

                        //Escribo en excel
                        //No
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $base_0          = 0;
                        $sum             = 0;
                        $sum_bas_0       = 0;
                        $sum_bas_iva     = 0;
                        $sum_v_iva       = 0;
                        $sum_gasto_amd   = 0;
                        $total_m_iva     = 0;
                        $nombre_anterior = '';
                        $tipo_seguro_ant = '';
                        $x++;
                        $i++;
                    }

                }
                //JUBILADO
                if (!is_null($archivo_plano_jubilado)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;
                    $id_temporal     = 0;

                    foreach ($archivo_plano_jubilado as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total += $total_m_iva;
                                $iva += $sum_v_iva;
                                $sub_total = $total - $iva;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/

                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';

                    /*$sheet->mergeCells('A'.$i.':L'.$i);
                $sheet->cell('A'.$i, function ($cell){

                $cell->setValue('');
                $cell->setBorder('thin', '', 'thin', 'thin');
                });

                $sheet->mergeCells('M'.$i.':N'.$i);
                $sheet->cell('M'.$i, function ($cell){

                $cell->setValue('SUBTOTAL');
                $cell->setFontWeight('bold');
                $cell->setAlignment('right');
                $cell->setBorder('', '', 'thin', '');
                });

                $sheet->getStyle('P'.$i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->mergeCells('O'.$i.':P'.$i);
                $sheet->cell('O'.$i, function($cell) use($sub_total){

                $valor_subtotal = number_format($sub_total, 2);
                $cell->setValue($valor_subtotal);
                $cell->setAlignment('right');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i=$i+1;
                $sheet->mergeCells('A'.$i.':L'.$i);
                $sheet->cell('A'.$i, function ($cell){

                $cell->setValue('');
                $cell->setBorder('thin', '', 'thin', 'thin');
                });
                $sheet->mergeCells('M'.$i.':N'.$i);
                $sheet->cell('M'.$i, function ($cell){

                $cell->setValue('IVA');
                $cell->setFontWeight('bold');
                $cell->setAlignment('right');
                $cell->setBorder('', '', 'thin', '');
                });

                $sheet->getStyle('P'.$i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->mergeCells('O'.$i.':P'.$i);
                $sheet->cell('O'.$i, function($cell) use($iva){

                $valor_iva = number_format($iva, 2);
                $cell->setValue($valor_iva);
                $cell->setAlignment('right');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i=$i+1;
                $sheet->mergeCells('A'.$i.':L'.$i);
                $sheet->cell('A'.$i, function ($cell){

                $cell->setValue('');
                $cell->setBorder('thin', '', 'thin', 'thin');
                });

                $sheet->mergeCells('M'.$i.':N'.$i);
                $sheet->cell('M'.$i, function ($cell){

                $cell->setValue('TOTAL');
                $cell->setFontWeight('bold');
                $cell->setAlignment('right');
                $cell->setBorder('', '', 'thin', '');
                });

                $sheet->getStyle('P'.$i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->mergeCells('O'.$i.':P'.$i);
                $sheet->cell('O'.$i, function($cell) use($total){

                $valor_total = number_format($total, 2);
                $cell->setValue($valor_total);
                $cell->setAlignment('right');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i=$i+8;
                $sheet->mergeCells('H'.$i.':K'.$i);
                $sheet->cell('H'.$i, function ($cell){

                $cell->setValue('');
                $cell->setBorder('', '', 'thin', '');
                });
                $i=$i+1;
                $sheet->mergeCells('H'.$i.':K'.$i);
                $sheet->cell('H'.$i, function ($cell){

                $cell->setValue('FIRMA / SELLO PRESTADOR');
                $cell->setAlignment('center');
                $cell->setBorder('', '', '', '');
                });*/
                }
                //JUBILADO CAMPESINO
                if (!is_null($archivo_plano_jub_campesino)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_jub_campesino as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total += $total_m_iva;
                                $iva += $sum_v_iva;
                                $sub_total = $total - $iva;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/

                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';

                    /*$sheet->mergeCells('A'.$i.':L'.$i);
                $sheet->cell('A'.$i, function ($cell){

                $cell->setValue('');
                $cell->setBorder('thin', '', 'thin', 'thin');
                });

                $sheet->mergeCells('M'.$i.':N'.$i);
                $sheet->cell('M'.$i, function ($cell){

                $cell->setValue('SUBTOTAL');
                $cell->setFontWeight('bold');
                $cell->setAlignment('right');
                $cell->setBorder('', '', 'thin', '');
                });

                $sheet->getStyle('P'.$i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->mergeCells('O'.$i.':P'.$i);
                $sheet->cell('O'.$i, function($cell) use($sub_total){

                $valor_subtotal = number_format($sub_total, 2);
                $cell->setValue($valor_subtotal);
                $cell->setAlignment('right');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i=$i+1;
                $sheet->mergeCells('A'.$i.':L'.$i);
                $sheet->cell('A'.$i, function ($cell){

                $cell->setValue('');
                $cell->setBorder('thin', '', 'thin', 'thin');
                });
                $sheet->mergeCells('M'.$i.':N'.$i);
                $sheet->cell('M'.$i, function ($cell){

                $cell->setValue('IVA');
                $cell->setFontWeight('bold');
                $cell->setAlignment('right');
                $cell->setBorder('', '', 'thin', '');
                });

                $sheet->getStyle('P'.$i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->mergeCells('O'.$i.':P'.$i);
                $sheet->cell('O'.$i, function($cell) use($iva){

                $valor_iva = number_format($iva, 2);
                $cell->setValue($valor_iva);
                $cell->setAlignment('right');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i=$i+1;
                $sheet->mergeCells('A'.$i.':L'.$i);
                $sheet->cell('A'.$i, function ($cell){

                $cell->setValue('');
                $cell->setBorder('thin', '', 'thin', 'thin');
                });

                $sheet->mergeCells('M'.$i.':N'.$i);
                $sheet->cell('M'.$i, function ($cell){

                $cell->setValue('TOTAL');
                $cell->setFontWeight('bold');
                $cell->setAlignment('right');
                $cell->setBorder('', '', 'thin', '');
                });

                $sheet->getStyle('P'.$i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->mergeCells('O'.$i.':P'.$i);
                $sheet->cell('O'.$i, function($cell) use($total){

                $valor_total = number_format($total, 2);
                $cell->setValue($valor_total);
                $cell->setAlignment('right');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i=$i+8;
                $sheet->mergeCells('H'.$i.':K'.$i);
                $sheet->cell('H'.$i, function ($cell){

                $cell->setValue('');
                $cell->setBorder('', '', 'thin', '');
                });
                $i=$i+1;
                $sheet->mergeCells('H'.$i.':K'.$i);
                $sheet->cell('H'.$i, function ($cell){

                $cell->setValue('FIRMA / SELLO PRESTADOR');
                $cell->setAlignment('center');
                $cell->setBorder('', '', '', '');
                });*/
                }
                //MONTEPIO
                if (!is_null($archivo_plano_montepio)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_montepio as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total += $total_m_iva;
                                $iva += $sum_v_iva;
                                $sub_total = $total - $iva;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';

                }

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                //dd($acum_bas_0 ,$acum_bas_iva ,$acum_v_iva ,$acum_gasto_amd);
                $subtotal_final = $acum_bas_0 + $acum_bas_iva + $acum_gasto_amd;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $subtotal_final) {
                    // manipulate the cel
                    $cell->setValue($subtotal_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('IVA:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $acum_v_iva) {
                    // manipulate the cel
                    $cell->setValue($acum_v_iva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $total_final = $subtotal_final + $acum_v_iva;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $total_final) {
                    // manipulate the cel
                    $cell->setValue($total_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $i = $i + 7;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('t', '', 'thin', '');

                });
                $i++;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('FIRMA / SELLO PRESTADOR');
                    $cell->setAlignment('center');
                    $cell->setBorder('t', '', '', '');

                });

            });
        })->export('xlsx');

    }

    public function reporte_cobertura_isspol(Request $request)
    {

        $mes_plano         = $request['mes_plano'];
        $seg               = $request['seguro'];
        $tipo_seg          = $request['id_tipo_seguro'];
        $empresa           = $request['id_empresa'];
        $cobert_compartida = $request['id_cobertura_comp'];
        $sub_cadena_1      = '';
        $sub_cadena_2      = '';

        $archivo_plano_activo        = [];
        $archivo_plano_jubilado      = [];
        $archivo_plano_jub_campesino = [];
        $archivo_plano_montepio      = [];

        $texto_nombre = null;

        $caract_remplazar_1 = array("¨", "$", "%", "&", "/", "(", ")", "#", "@", "|", "!", "/", "+", "-", "}", "{", ">", "< ", ";", ",", ":",
            ".", "*", ">", "<", "[", "]", "?", "¿", "º", "~");

        $caract_remplazar_2 = array("Ñ", "ñ");

        if (!is_null($mes_plano)) {
            $sub_cadena_1 = substr($mes_plano, 0, 2);
        }

        if (!is_null($mes_plano)) {
            $sub_cadena_2 = substr($mes_plano, 2, 6);
        }

        $inf_empresa = Empresa::where('id', $empresa)->where('estado', '1')->first();

        $archivo_plano_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where(function ($query) {
                $query->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '2')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '3')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '4')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '5');
            })
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '6')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total','apd.valor_unitario')
            ->orderby('full_name')
            ->get();
        //dd($archivo_plano_activo);    

        //JUBILADO JU
        $archivo_plano_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '6')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total','apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO CAMPESINO JC
        $archivo_plano_jub_campesino = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '7')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '6')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total','apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //MONTEPIO MO
        $archivo_plano_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '6')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total','apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        Excel::create('Reporte Consolidado. ISSPOL', function ($excel) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $archivo_plano_activo, $archivo_plano_jubilado, $archivo_plano_jub_campesino, $archivo_plano_montepio, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre) {
            $excel->sheet('Reporte Consolidado. ISSPOL', function ($sheet) use ($inf_empresa, $sub_cadena_1, $sub_cadena_2, $archivo_plano_activo, $archivo_plano_jubilado, $archivo_plano_jub_campesino, $archivo_plano_montepio, $caract_remplazar_1, $caract_remplazar_2, $texto_nombre) {

                $sheet->setColumnFormat(array(
                    'I' => '$### ### ### ##0.00',
                    'K' => '$### ### ### ##0.00',
                    'M' => '$### ### ### ##0.00',
                    'N' => '$### ### ### ##0.00',
                    'O' => '$### ### ### ##0.00',
                ));

                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(base_path() . '/storage/app/logo/iess_logo.png');
                $objDrawing->setCoordinates('N2');
                $objDrawing->setHeight(70);
                $objDrawing->setWorksheet($sheet);

                $sheet->mergeCells('A2:N2');
                $sheet->cell('A2', function ($cell) use ($inf_empresa) {
                    $cell->setValue('INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('14');
                });

                $sheet->mergeCells('A4:N4');
                $sheet->cell('A4', function ($cell) use ($inf_empresa) {
                    $cell->setValue('COORDINACIÓN PROVINCIAL DE PRESTACIONES DEL SEGURO DE SALUD GUAYAS');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                });

                $sheet->mergeCells('A6:P6');
                $sheet->cell('A6', function ($cell) use ($inf_empresa) {
                    $cell->setValue('PLANILLA CONSOLIDADO DE PRESTACION DE SERVICIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A7:B7');
                $sheet->cell('A7', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombre prestador:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C7:P7');
                $sheet->cell('C7', function ($cell) use ($inf_empresa) {
                    $cell->setValue($inf_empresa->razonsocial);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A8:B8');
                $sheet->cell('A8', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Teléfono:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C8:P8');
                $sheet->cell('C8', function ($cell) use ($inf_empresa) {
                    $cell->setValue('(04)2 109 180');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A9:B9');
                $sheet->cell('A9', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Correo::');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C9:P9');
                $sheet->cell('C9', function ($cell) use ($inf_empresa) {
                    $cell->setValue('gerenciageneral@ieced.ec');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A10:B10');
                $sheet->cell('A10', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Servicio:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C10:P10');
                $sheet->cell('C10', function ($cell) use ($inf_empresa) {
                    $cell->setValue('AMBULATORIO');
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('A11:B11');
                $sheet->cell('A11', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Mes:');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('C11:I11');
                $sheet->cell('C11', function ($cell) use ($sub_cadena_1) {

                    $txt_mes = '';

                    if ($sub_cadena_1 == '01') {
                        $txt_mes = 'ENERO';
                    } elseif ($sub_cadena_1 == '02') {
                        $txt_mes = 'FEBRERO';
                    } elseif ($sub_cadena_1 == '03') {
                        $txt_mes = 'MARZO';
                    } elseif ($sub_cadena_1 == '04') {
                        $txt_mes = 'ABRIL';
                    } elseif ($sub_cadena_1 == '05') {
                        $txt_mes = 'MAYO';
                    } elseif ($sub_cadena_1 == '06') {
                        $txt_mes = 'JUNIO';
                    } elseif ($sub_cadena_1 == '07') {
                        $txt_mes = 'JULIO';
                    } elseif ($sub_cadena_1 == '08') {
                        $txt_mes = 'AGOSTO';
                    } elseif ($sub_cadena_1 == '09') {
                        $txt_mes = 'SEPTIEMBRE';
                    } elseif ($sub_cadena_1 == '10') {
                        $txt_mes = 'OCTUBRE';
                    } elseif ($sub_cadena_1 == '11') {
                        $txt_mes = 'NOVIEMBRE';
                    } elseif ($sub_cadena_1 == '12') {
                        $txt_mes = 'DICIEMBRE';
                    }

                    $cell->setValue($txt_mes);
                    $cell->setAlignment('left');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $sheet->mergeCells('J11:P11');
                $sheet->cell('J11', function ($cell) use ($sub_cadena_2) {
                    // manipulate the cel
                    $cell->setValue('Año:' . ($sub_cadena_2));
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*$sheet->mergeCells('k11:P11');
                $sheet->cell('K11', function($cell) use($sub_cadena_2){
                $cell->setValue($sub_cadena_2);
                $cell->setAlignment('left');
                $cell->setFontSize('10');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });*/

                $sheet->cell('A12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('No');
                    $cell->setAlignment('left');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('B12:G12');
                $sheet->cell('B12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('NOMBRE DE PACIENTE/USUARIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('TIPO DE SEGURO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I12:J12');
                $sheet->cell('I12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE0');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K12:L12');
                $sheet->cell('K12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('BASE12');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('VALOR IVA 12%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('GASTO ADM');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('O12:P12');
                $sheet->cell('O12', function ($cell) use ($inf_empresa) {
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('10');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i          = 13; $base          = 0; $id_temporal          = 0; $x          = 1; $sub_total          = 0; $iva          = 0; $total          = 0;
                $acum_bas_0 = 0; $acum_bas_iva = 0; $acum_v_iva = 0; $acum_gasto_amd = 0;
                $value      = null;

                if (!is_null($archivo_plano_activo)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;
                    $vh_conta = 0;

                    foreach ($archivo_plano_activo as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total += $total_m_iva;
                                $iva += $sum_v_iva;
                                $sub_total = $total - $iva;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }

                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);  

                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);
                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //dd($sum_bas_iva,$sum_v_iva);

                    //Escribo en excel
                    //No
                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x.'+');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //NOMBRE DE PACIENTE/USUARIO
                    $sheet->mergeCells('B' . $i . ':G' . $i);
                    $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                        // manipulate the cel
                        $cell->setValue($nombre_anterior);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    //TIPO DE SEGURO
                    $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                        // manipulate the cel

                        $cell->setValue($tipo_seguro_ant);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //BASE0
                    $sheet->mergeCells('I' . $i . ':J' . $i);
                    $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_0);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //BASE12
                    $sheet->mergeCells('K' . $i . ':L' . $i);
                    $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_bas_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //VALOR IVA 12%
                    $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                        // manipulate the cel
                        $cell->setValue($sum_v_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //GASTO ADM
                    $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                        // manipulate the cel
                        $cell->setValue($sum_gasto_amd);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //SUBTOTAL
                    $sheet->mergeCells('O' . $i . ':P' . $i);
                    $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                        // manipulate the cel
                        $cell->setValue($total_m_iva);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $x++;
                    $i++;

                }

                if (!is_null($archivo_plano_jubilado)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_jubilado as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total += $total_m_iva;
                                $iva += $sum_v_iva;
                                $sub_total = $total - $iva;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';

                }

                if (!is_null($archivo_plano_jub_campesino)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_jub_campesino as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total += $total_m_iva;
                                $iva += $sum_v_iva;
                                $sub_total = $total - $iva;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }

                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';

                }

                if (!is_null($archivo_plano_montepio)) {

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';
                    $id_temporal     = 0;

                    foreach ($archivo_plano_montepio as $value) {
                        if ($value->id_paciente != $id_temporal) {

                            if ($id_temporal != 0) {

                                //Escribo en excel
                                //No
                                $sheet->cell('A' . $i, function ($cell) use ($x) {
                                    // manipulate the cel
                                    $cell->setValue($x);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //NOMBRE DE PACIENTE/USUARIO
                                $sheet->mergeCells('B' . $i . ':G' . $i);
                                $sheet->cell('B' . $i, function ($cell) use ($value, $nombre_anterior) {
                                    // manipulate the cel
                                    $cell->setValue($nombre_anterior);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                //TIPO DE SEGURO
                                $sheet->cell('H' . $i, function ($cell) use ($value, $tipo_seguro_ant) {
                                    // manipulate the cel

                                    $cell->setValue($tipo_seguro_ant);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE0
                                $sheet->mergeCells('I' . $i . ':J' . $i);
                                $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_0);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //BASE12
                                $sheet->mergeCells('K' . $i . ':L' . $i);
                                $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_bas_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //VALOR IVA 12%
                                $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                                    // manipulate the cel
                                    $cell->setValue($sum_v_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //GASTO ADM
                                $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                                    // manipulate the cel
                                    $cell->setValue($sum_gasto_amd);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });
                                //SUBTOTAL
                                $sheet->mergeCells('O' . $i . ':P' . $i);
                                $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                                    // manipulate the cel
                                    $cell->setValue($total_m_iva);
                                    $cell->setAlignment('right');
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                                });

                                $total += $total_m_iva;
                                $iva += $sum_v_iva;
                                $sub_total = $total - $iva;

                                //Fin de Escritura del Excel
                                $base_0          = 0;
                                $sum             = 0;
                                $sum_bas_0       = 0;
                                $sum_bas_iva     = 0;
                                $sum_v_iva       = 0;
                                $sum_gasto_amd   = 0;
                                $total_m_iva     = 0;
                                $nombre_anterior = '';
                                $tipo_seguro_ant = '';
                                $x++;
                                $i++;

                            }

                            $id_temporal = $value->id_paciente;

                        }

                        //Si porcentaje_iva == 0  BASE_0
                        if ($value->porcentaje_iva == 0) {
                            $sum_bas_0 = $sum_bas_0 + $value->subtotal;
                            $acum_bas_0 += $value->subtotal;
                        }
                        //Si porcentaje_iva == 0.12  BASE_IVA
                        if ($value->porcentaje_iva == 0.12) {
                            $sum_bas_iva = $sum_bas_iva + $value->subtotal;
                            $acum_bas_iva += $value->subtotal;
                        }
                        //Suma v_iva
                        //$sum_v_iva = $sum_v_iva+$value->iva;
                        //$acum_v_iva += $value->iva;
                        $sum_v_iva = $sum_v_iva + ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        $acum_v_iva += ($value->valor_unitario * $value->cantidad * $value->porcentaje_iva);
                        //Porcentaje_10  GAST_ADM_10
                        $sum_gasto_amd = $sum_gasto_amd + $value->porcentaje10;
                        $acum_gasto_amd += $value->porcentaje10;

                        //TOTAL_M_IVA
                        $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                        /*$nombre_anterior = $value->paciente->apellido1.' '.$value->paciente->apellido2.' '.$value->paciente->nombre1.' '.$value->paciente->nombre2;*/
                        $nombre_anterior = '';
                        if ($value->paciente->apellido1 != 'N/A' && $value->paciente->apellido1 != '(N/A)') {
                            $nombre_anterior = $value->paciente->apellido1;
                        }
                        if ($value->paciente->apellido2 != 'N/A' && $value->paciente->apellido2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->apellido2;
                        }
                        if ($value->paciente->nombre1 != 'N/A' && $value->paciente->nombre1 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre1;
                        }

                        if ($value->paciente->nombre2 != 'N/A' && $value->paciente->nombre2 != '(N/A)') {
                            $nombre_anterior = $nombre_anterior . ' ' . $value->paciente->nombre2;
                        }

                        $nombre_anterior = str_replace($caract_remplazar_1, "", $nombre_anterior);
                        $nombre_anterior = str_replace($caract_remplazar_2, "N", $nombre_anterior);

                        $tipo_seguro_ant = $value->tiposeg;

                    }
                    //Escribo en excel
                    //No
                    if ($nombre_anterior != '') {

                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //NOMBRE DE PACIENTE/USUARIO
                        $sheet->mergeCells('B' . $i . ':G' . $i);
                        $sheet->cell('B' . $i, function ($cell) use ($nombre_anterior) {
                            // manipulate the cel
                            $cell->setValue($nombre_anterior);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        //TIPO DE SEGURO
                        $sheet->cell('H' . $i, function ($cell) use ($tipo_seguro_ant) {
                            // manipulate the cel

                            $cell->setValue($tipo_seguro_ant);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //BASE0
                        $sheet->mergeCells('I' . $i . ':J' . $i);
                        $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_0);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //BASE12
                        $sheet->mergeCells('K' . $i . ':L' . $i);
                        $sheet->cell('K' . $i, function ($cell) use ($sum_bas_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_bas_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //VALOR IVA 12%
                        $sheet->cell('M' . $i, function ($cell) use ($sum_v_iva) {
                            // manipulate the cel
                            $cell->setValue($sum_v_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //GASTO ADM
                        $sheet->cell('N' . $i, function ($cell) use ($sum_gasto_amd) {
                            // manipulate the cel
                            $cell->setValue($sum_gasto_amd);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        //SUBTOTAL
                        $sheet->mergeCells('O' . $i . ':P' . $i);
                        $sheet->cell('O' . $i, function ($cell) use ($total_m_iva) {
                            // manipulate the cel
                            $cell->setValue($total_m_iva);
                            $cell->setAlignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $x++;
                        $i++;
                    }

                    $base_0          = 0;
                    $sum             = 0;
                    $sum_bas_0       = 0;
                    $sum_bas_iva     = 0;
                    $sum_v_iva       = 0;
                    $sum_gasto_amd   = 0;
                    $total_m_iva     = 0;
                    $nombre_anterior = '';
                    $tipo_seguro_ant = '';

                }

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTAL:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                //dd($acum_bas_0 ,$acum_bas_iva ,$acum_v_iva ,$acum_gasto_amd);
                $subtotal_final = $acum_bas_0 + $acum_bas_iva + $acum_gasto_amd;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $subtotal_final) {
                    // manipulate the cel
                    $cell->setValue($subtotal_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('IVA:');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $acum_v_iva) {
                    // manipulate the cel
                    $cell->setValue($acum_v_iva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $i++;

                $sheet->mergeCells('A' . $i . ':N' . $i);
                $sheet->cell('A' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });
                $total_final = $subtotal_final + $acum_v_iva;
                $sheet->mergeCells('O' . $i . ':P' . $i);
                $sheet->cell('O' . $i, function ($cell) use ($value, $total_final) {
                    // manipulate the cel
                    $cell->setValue($total_final);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                });

                $i = $i + 7;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('t', '', 'thin', '');

                });
                $i++;
                $sheet->mergeCells('G' . $i . ':J' . $i);
                $sheet->cell('G' . $i, function ($cell) use ($value) {
                    // manipulate the cel
                    $cell->setValue('FIRMA / SELLO PRESTADOR');
                    $cell->setAlignment('center');
                    $cell->setBorder('t', '', '', '');

                });

            });
        })->export('xlsx');

    }

    public function guardar_agrupado(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $arr = [
            'id_tipo_seg'     => $request['tipo_seguro'],
            'mes_plano'       => $request['mes_plano'],
            'empresa'         => $request['empresa'],
            'seguro'          => $request['seguro'],
            'n_exp'           => $request['cantidad'],
            'base_0'          => $request['base_0'],
            'base_iva'        => $request['base_iva'],
            'v_iva'           => $request['v_iva'],
            'gast_amd10'      => $request['amd_10'],
            'total_iva'       => $request['total_iva'],
            'valor_cobrado'   => $request['total_iva'],
            'cod_proceso'     => $request['codigo'],
            'tipo'            => $request['tipo'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        Ap_Agrupado::create($arr);

        return "ok";
    }

    public function guardar_objetar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $arr = [
            'id_tipo_seg'     => $request['tipo_seguro'],
            'mes_plano'       => $request['mes_plano'],
            'empresa'         => $request['empresa'],
            'seguro'          => $request['seguro'],
            'n_exp'           => $request['cantidad'],
            'base_0'          => $request['base_0'],
            'base_iva'        => $request['base_iva'],
            'v_iva'           => $request['v_iva'],
            'gast_amd10'      => $request['amd_10'],
            'total_iva'       => $request['valor_cobrado'],
            'valor_cobrado'   => $request['valor_cobrado'],
            'cod_proceso'     => $request['codigo'],
            'tipo'            => $request['tipo'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        Ap_Agrupado::create($arr);

        return "ok";
    }

    public function codigo_proceso(Request $request, $id)
    {

        return view('archivo_plano/archivo/proceso', ['id' => $id]);
    }

    public function codigo_proceso2(Request $request, $id)
    {

        return view('archivo_plano/archivo/proceso2', ['id' => $id]);
    }

    //Actualizar valores de Iva
    public function modifica_valor_iva_total()
    {

        $recor_cabec = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', '06-2020')
            ->where('archivo_plano_cabecera.id_seguro', '2')
            ->where('archivo_plano_cabecera.id_empresa', '0992704152001')
            ->where('archivo_plano_cabecera.estado', '1')
            ->get();

        //Recorre Cabecera
        foreach ($recor_cabec as $value) {

            $detalles = $value->detalles->where('estado', '1');

            foreach ($detalles as $detalle) {

                if (($detalle->porcentaje_iva > 0) && ($detalle->iva > 0)) {

                    $k_valor   = $detalle->valor;
                    $val_unit  = $k_valor / (1 + $detalle->porcentaje10);
                    $subtotal  = $detalle->cantidad * $val_unit;
                    $valor10   = $subtotal * $detalle->porcentaje10;
                    $valor_iva = $subtotal * $detalle->iva;
                    $total     = $subtotal + $valor10 + $valor_iva;
                    $total_sol = $total;

                    $detalle->update(['iva' => $valor_iva,
                        'total'                 => round(($total), 2),
                        'total_solicitado_usd'  => round(($total_sol), 2),
                    ]);

                }

            }

        }

    }

    //Verifica Excel Reporte Agrupado
    public function verifica_reporte_agrupado(Request $request)
    {

        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $empresa   = $request['empresa'];
        $tipo_seg  = $request['tipo_seg'];

        $archivo_plano_activo        = [];
        $archivo_plano_conyugue      = [];
        $archivo_plano_0_1           = [];
        $archivo_plano_2_6           = [];
        $archivo_plano_7_17          = [];
        $archivo_plano_jubilado      = [];
        $archivo_plano_jub_campesino = [];
        $archivo_plano_montepio      = [];
        $archivo_plano_ssc           = [];
        $archivo_plano               = [];

        if ($tipo_seg == '10') {

            //Activo
            $archivo_plano_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('archivo_plano_detalle as det', 'det.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->where('det.estado', '1')
                ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp', 'det.total')
                ->orderby('full_name')
                ->get();

            //return $archivo_plano_activo;

            //Activo Conyugue
            $archivo_plano_conyugue = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '2')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('archivo_plano_detalle as det', 'det.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->where('det.estado', '1')
                ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp', 'det.total')
                ->orderby('full_name')
                ->get();

            //HIJO DE 0 - 1 AÑO
            $archivo_plano_0_1 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '3')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('archivo_plano_detalle as det', 'det.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->where('det.estado', '1')
                ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp', 'det.total')
                ->orderby('full_name')
                ->get();

            //HIJO DE 2 - 6 AÑOS
            $archivo_plano_2_6 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '4')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('archivo_plano_detalle as det', 'det.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->where('det.estado', '1')
                ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp', 'det.total')
                ->orderby('full_name')
                ->get();

            //HIJO DE 7 - 17 AÑOS
            $archivo_plano_7_17 = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '5')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('archivo_plano_detalle as det', 'det.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->where('det.estado', '1')
                ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp', 'det.total')
                ->orderby('full_name')
                ->get();

            //JUBILADO
            $archivo_plano_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('archivo_plano_detalle as det', 'det.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->where('det.estado', '1')
                ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp', 'det.total')
                ->orderby('full_name')
                ->get();

            //JUBILADO CAMPESINO
            $archivo_plano_jub_campesino = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '7')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('archivo_plano_detalle as det', 'det.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->where('det.estado', '1')
                ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp', 'det.total')
                ->orderby('full_name')
                ->get();

            //MONTEPIO
            $archivo_plano_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('archivo_plano_detalle as det', 'det.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->where('det.estado', '1')
                ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp', 'det.total')
                ->orderby('full_name')
                ->get();

            //SSC
            $archivo_plano_ssc = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', '9')
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('archivo_plano_detalle as det', 'det.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->where('det.estado', '1')
                ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp', 'det.total')
                ->orderby('full_name')
                ->get();

        } else {
            //Para todos los Tipos de Seguro
            $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', $tipo_seg)
                ->where('archivo_plano_cabecera.id_seguro', $seg)
                ->where('archivo_plano_cabecera.id_empresa', $empresa)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('archivo_plano_detalle as det', 'det.id_ap_cabecera', 'archivo_plano_cabecera.id')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->where('det.estado', '1')
                ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp', 'det.total')
                ->orderby('full_name')
                ->get();

        }

        if ((count($archivo_plano_activo) > 0) || (count($archivo_plano_conyugue) > 0) || (count($archivo_plano_0_1) > 0) || (count($archivo_plano_2_6) > 0) || (count($archivo_plano_7_17) > 0) || (count($archivo_plano_jubilado) > 0) || (count($archivo_plano_jub_campesino) > 0) || (count($archivo_plano_montepio) > 0) || (count($archivo_plano_ssc) > 0) || (count($archivo_plano) > 0)) {

            return "existe";

        } else {

            return "no_existe";

        }

    }

    //Verifica Excel Reporte Consolidado General
    public function verifica_consolidado_general(Request $request)
    {

        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $empresa   = $request['empresa'];

        $archivo_plano_activo        = [];
        $archivo_plano_jubilado      = [];
        $archivo_plano_jub_campesino = [];
        $archivo_plano_montepio      = [];

        //ACTIVO  SG;ACTIVO (CONYUGE);HIJO DE 0 - 1 AÑO;HIJO DE 2 - 6 AÑOS;HIJO DE 7 - 17 AÑOS
        $archivo_plano_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where(function ($query) {
                $query->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '2')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '3')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '4')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '5');
            })
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO JU
        $archivo_plano_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO CAMPESINO JC
        $archivo_plano_jub_campesino = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '7')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //MONTEPIO MO
        $archivo_plano_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        if ((count($archivo_plano_activo) > 0) || (count($archivo_plano_jubilado) > 0) || (count($archivo_plano_jub_campesino) > 0) || (count($archivo_plano_montepio) > 0)) {

            return "existe";

        } else {

            return "no_existe";
        }

    }

    //Verifica Excel Reporte Consolidado Campesino
    public function verifica_consolidado_campesino(Request $request)
    {

        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $empresa   = $request['empresa'];

        $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '9')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        if (count($archivo_plano) > 0) {

            return "existe";

        } else {

            return "no_existe";

        }

    }

    //Verifica Excel Reporte Consolidado ISSFA
    public function verifica_consolidado_issfa(Request $request)
    {

        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $empresa   = $request['empresa'];

        //ACTIVO  SG;ACTIVO (CONYUGE);HIJO DE 0 - 1 AÑO;HIJO DE 2 - 6 AÑOS;HIJO DE 7 - 17 AÑOS
        $archivo_plano_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where(function ($query) {
                $query->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '2')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '3')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '4')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '5');
            })
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '3')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO JU
        $archivo_plano_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '3')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //JUBILADO CAMPESINO JC
        $archivo_plano_jub_campesino = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '7')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '3')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        //MONTEPIO MO
        $archivo_plano_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '3')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total', 'apd.valor_unitario')
            ->orderby('full_name')
            ->get();

        if ((count($archivo_plano_activo) > 0) || (count($archivo_plano_jubilado) > 0) || (count($archivo_plano_jub_campesino) > 0) || (count($archivo_plano_montepio) > 0)) {

            return "existe";

        } else {

            return "no_existe";

        }
    }

    //Verifica Excel Reporte Consolidado ISSPOL
    public function verifica_consolidado_isspol(Request $request)
    {

        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $empresa   = $request['empresa'];

        //ACTIVO  SG;ACTIVO (CONYUGE);HIJO DE 0 - 1 AÑO;HIJO DE 2 - 6 AÑOS;HIJO DE 7 - 17 AÑOS
        $archivo_plano_activo = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where(function ($query) {
                $query->where('archivo_plano_cabecera.id_tipo_seguro', '1')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '2')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '3')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '4')
                    ->orwhere('archivo_plano_cabecera.id_tipo_seguro', '5');
            })
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '6')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total')
            ->orderby('full_name')
            ->get();

        //JUBILADO JU
        $archivo_plano_jubilado = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '6')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '6')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total')
            ->orderby('full_name')
            ->get();

        //JUBILADO CAMPESINO JC
        $archivo_plano_jub_campesino = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '7')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '6')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total')
            ->orderby('full_name')
            ->get();

        //MONTEPIO MO
        $archivo_plano_montepio = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_tipo_seguro', '8')
            ->where('archivo_plano_cabecera.id_seguro', $seg)
            ->where('archivo_plano_cabecera.id_cobertura_comp', '6')
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->orderby('pac.apellido1', 'asc')
            ->select(DB::raw('CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.id_empresa', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tiposeg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.subtotal', 'apd.total')
            ->orderby('full_name')
            ->get();

        if ((count($archivo_plano_activo) > 0) || (count($archivo_plano_jubilado) > 0) || (count($archivo_plano_jub_campesino) > 0) || (count($archivo_plano_montepio) > 0)) {

            return "existe";

        } else {

            return "no_existe";

        }
    }

    //Verifica Excel Reporte Seguro Privado
    public function verifica_seguro_privado(Request $request)
    {

        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $empresa   = $request['empresa'];

        $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
            ->where('archivo_plano_cabecera.id_empresa', $empresa)
            ->where('archivo_plano_cabecera.estado', '1')
            ->whereNotNull('id_seguro_priv')
            ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
            ->where('apd.estado', '1')
            ->get();

        if (count($archivo_plano) > 0) {

            return "existe";

        } else {

            return "no_existe";

        }
    }

    //Verifica Excel Reporte Horarrios Cirujano
    public function verifica_honorario_cirujano(Request $request)
    {

        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $empresa   = $request['empresa'];

        //ACTIVO  SG;ACTIVO (CONYUGE);HIJO DE 0 - 1 AÑO;HIJO DE 2 - 6 AÑOS;HIJO DE 7 - 17 AÑOS
        $honor_medicos_activos = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where(function ($query) {
                $query->where('ap.id_tipo_seguro', '1')
                    ->orwhere('ap.id_tipo_seguro', '2')
                    ->orwhere('ap.id_tipo_seguro', '3')
                    ->orwhere('ap.id_tipo_seguro', '4')
                    ->orwhere('ap.id_tipo_seguro', '5');
            })
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_empresa', $empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'apd.clasif_porcentaje_msp', 'ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //JUBILADO JU
        $honor_medicos_jubilado = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '6')
            ->where('ap.id_empresa', $empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //JUBILADO CAMPESINO JC
        $honor_medicos_jub_campesino = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '7')
            ->where('ap.id_empresa', $empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //MONTEPIO MO
        $honor_medicos_montepio = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '8')
            ->where('ap.id_empresa', $empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //SSC
        $honor_medicos_ssc = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '9')
            ->where('ap.id_empresa', $empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        if ((count($honor_medicos_activos) > 0) || (count($honor_medicos_jubilado) > 0) || (count($honor_medicos_jub_campesino) > 0) || (count($honor_medicos_montepio) > 0) || (count($honor_medicos_ssc) > 0)) {

            return "existe";

        } else {

            return "no_existe";

        }

    }

    //Verifica Excel Reporte Horarrios Anestesiologo
    public function verifica_honorario_anestesiologo(Request $request)
    {

        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $empresa   = $request['empresa'];
        $tipo_seg  = $request['tipo_seg'];

        $archivo_plano = Archivo_Plano_Cabecera::where('mes_plano', $mes_plano)
            ->where('id_seguro', $seg)
            ->where('id_empresa', $empresa)
            ->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
            ->join('ap_tipo_seg as apts', 'apts.codigo', 'tseg.tipo')
            ->where('apts.id', $tipo_seg)
            ->join('archivo_plano_detalle as apd', 'apd.id_ap_cabecera', 'archivo_plano_cabecera.id')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->where('apt.tipo_ex', 'HME')
            ->select('archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'tseg.tipo as tiposeg', 'archivo_plano_cabecera.nom_procedimiento', 'tseg.nombre as nom_tseg', 'apd.clasif_porcentaje_msp', 'apd.total_solicitado_usd')->get();

        if (count($archivo_plano) > 0) {

            return "existe";

        } else {

            return "no_existe";

        }

    }

    //Verifica Excel Reporte Horarrios Cirujano
    public function verifica_biopsias(Request $request)
    {

        $mes_plano = $request['mes_plano'];
        $seg       = $request['seguro'];
        $empresa   = $request['empresa'];

        //ACTIVO  SG;ACTIVO (CONYUGE);HIJO DE 0 - 1 AÑO;HIJO DE 2 - 6 AÑOS;HIJO DE 7 - 17 AÑOS
        $honor_medicos_activos = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where(function ($query) {
                $query->where('ap.id_tipo_seguro', '1')
                    ->orwhere('ap.id_tipo_seguro', '2')
                    ->orwhere('ap.id_tipo_seguro', '3')
                    ->orwhere('ap.id_tipo_seguro', '4')
                    ->orwhere('ap.id_tipo_seguro', '5');
            })
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_empresa', $empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'apd.clasif_porcentaje_msp', 'ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //JUBILADO JU
        $honor_medicos_jubilado = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '6')
            ->where('ap.id_empresa', $empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //JUBILADO CAMPESINO JC
        $honor_medicos_jub_campesino = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '7')
            ->where('ap.id_empresa', $empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //MONTEPIO MO
        $honor_medicos_montepio = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '8')
            ->where('ap.id_empresa', $empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        //SSC
        $honor_medicos_ssc = Db::table('archivo_plano_detalle as apd')
            ->join('ap_tipo_examen as apt', 'apt.tipo', 'apd.tipo')
            ->join('archivo_plano_cabecera as ap', 'ap.id', 'apd.id_ap_cabecera')
            ->join('paciente as p', 'p.id', 'ap.id_paciente')
            ->join('tipo_seguro as ts', 'ts.id', 'ap.id_tipo_seguro')
            ->where('mes_plano', $mes_plano)
            ->where('apt.tipo_ex', 'HME')
            ->where('ap.estado', '1')
            ->where('apd.estado', '1')
            ->where('ap.id_seguro', $seg)
            ->where('ap.id_tipo_seguro', '9')
            ->where('ap.id_empresa', $empresa)
            ->select(DB::raw('CONCAT(p.apellido1, p.apellido2, p.nombre1, p.nombre2) AS full_name'), 'p.*', 'apd.tipo', 'apt.tipo_ex', 'apd.fecha', 'apd.descripcion', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.subtotal', 'apd.porcentaje10', 'apd.iva', 'apd.total', 'ts.nombre as tsnombre', 'ap.id_paciente', 'p.apellido1', 'p.apellido2', 'p.nombre1', 'p.nombre2', 'ap.fecha_ing', 'ap.nom_procedimiento', 'apd.porcentaje_honorario', 'ap.id_nivel')
            ->orderby('full_name')
            ->orderby('ap.fecha_ing', 'asc')
            ->get();

        if ((count($honor_medicos_activos) > 0) || (count($honor_medicos_jubilado) > 0) || (count($honor_medicos_jub_campesino) > 0) || (count($honor_medicos_montepio) > 0) || (count($honor_medicos_ssc) > 0)) {

            return "existe";

        } else {

            return "no_existe";

        }

    }

    public function consulta_seguro_iess(){

        $reportes = Reporte_Consultas_Iess::all();

        foreach($reportes as $reporte)
        {
            $reporte->delete();

        }

        $consultas_iess = Agenda::where('agenda.estado','1')
                            ->select('agenda.*')
                            ->where('agenda.fechaini','>','2021-05-01 00:00:00')
                            ->where('agenda.proc_consul','0')
                            ->join('historiaclinica as h','h.id_agenda','agenda.id')
                            ->leftjoin('reporte_consultas_iess as r','r.id_agenda_consulta','agenda.id')
                            ->whereNull('r.id_agenda_consulta')
                            ->where('h.id_seguro','2')
                            ->orderBy('agenda.fechaini','asc')
                            ->get();

        //dd($consultas_iess->count());

        foreach($consultas_iess as $consulta){
            //dd($consulta);

            $reporte = Reporte_Consultas_Iess::where('id_agenda_consulta',$consulta->id)->first();
            if(is_null($reporte)){
                Reporte_Consultas_Iess::create([
                    'anio'                  => date('Y',strtotime($consulta->fechaini)),
                    'mes'                   => date('m',strtotime($consulta->fechaini)),
                    'id_agenda_consulta'    => $consulta->id,
                    'espid_consulta' => $consulta->espid,
                ]);    
            }

        }  

        //LABORATORIO
        //$reportes = Reporte_Consultas_Iess::whereNull('id_orden_labs')->get();
        $reportes = Reporte_Consultas_Iess::all();

        foreach( $reportes as $reporte ){
            $agenda = Agenda::find($reporte->id_agenda_consulta);
            $fecha_agenda = date('Y-m-d',strtotime($agenda->fechaini));
            $doctor = $agenda->historia_clinica->id_doctor1;
            //dd($doctor);
            
            $orden_labs = Examen_Orden::where('id_paciente',$agenda->id_paciente)
                ->whereBetween('fecha_orden', array($fecha_agenda.'  0:00:00', $fecha_agenda.' 23:59:59'))
                ->where('id_doctor_ieced',$doctor)
                ->where('estado',1)
                ->orderBy('fecha_orden','asc')
                ->first();
            if(!is_null($orden_labs)){
                $lab_dup = Reporte_Consultas_Iess::where('id_orden_labs',$orden_labs->id)->first();
                if(is_null($lab_dup)){
                    $reporte->update([
                        'id_orden_labs' => $orden_labs->id,
                    ]);
                }    
            }    
                
        }

        //ORDENES_IMAGENES
        $reportes = Reporte_Consultas_Iess::whereNull('id_orden_imagenes')
            ->get();

        foreach($reportes as $reporte){
            $agenda = Agenda::find($reporte->id_agenda_consulta);
            $fecha_agenda = date('Y-m-d',strtotime($agenda->fechaini));
            $orden_imagen = Orden::where('id_paciente',$agenda->id_paciente)
                ->where('tipo_procedimiento',2)
                ->whereBetween('fecha_orden', array($fecha_agenda.'  0:00:00', $fecha_agenda.' 23:59:59'))
                ->where('id_doctor',$doctor)
                ->where('estado',1)
                ->orderBy('fecha_orden','asc')
                ->first();
            if(!is_null($orden_imagen)){
                $ima_dup = Reporte_Consultas_Iess::where('id_orden_imagenes',$orden_imagen->id)->first();
                if(is_null($ima_dup)){
                    $reporte->update([
                        'id_orden_imagenes' => $orden_imagen->id,
                    ]);
                }    
            }     
            

        
        }

        //INTERCONSULTAS
        $reportes = Reporte_Consultas_Iess::whereNull('id_agenda_otra_esp')
            ->get();

        foreach($reportes as $reporte){
            $agenda = Agenda::find($reporte->id_agenda_consulta);
            $interconsulta = Agenda::where('id_paciente',$agenda->id_paciente)
                ->where('proc_consul',0)
                ->where('fechaini','>',$agenda->fechaini)
                ->where('estado',1)
                ->where('espid','<>',$agenda->espid)
                ->orderBy('fechaini','asc')
                ->first();
            if(!is_null($interconsulta)){
                $inter_dup = Reporte_Consultas_Iess::where('id_agenda_otra_esp',$interconsulta->id)->first();
                if(is_null($inter_dup)){
                    $reporte->update([
                        'id_agenda_otra_esp' => $interconsulta->id,
                        'espid_inter'        => $interconsulta->espid
                    ]);
                }    
            }     
            

        
        }


        dd("fin");

        


    }

    public function masivo_corregir_subtotal(){

        $detalles = Archivo_Plano_Detalle::where('archivo_plano_detalle.codigo','419')->join('archivo_plano_cabecera as ap','ap.id','archivo_plano_detalle.id_ap_cabecera')->where('ap.mes_plano','012022')->select('archivo_plano_detalle.*')->get();//dd($detalles);
        foreach($detalles as $detalle){
            $subtotal = $detalle->cantidad * $detalle->valor;
            $detalle->update(['subtotal' => $subtotal, 'ip_creacion' => 'CUADRE02']);
        }
        $detalles = Archivo_Plano_Detalle::where('archivo_plano_detalle.codigo','282')->join('archivo_plano_cabecera as ap','ap.id','archivo_plano_detalle.id_ap_cabecera')->where('ap.mes_plano','012022')->select('archivo_plano_detalle.*')->get();
        foreach($detalles as $detalle){
            $subtotal = $detalle->cantidad * $detalle->valor;
            $detalle->update(['subtotal' => $subtotal, 'ip_creacion' => 'CUADRE02']);
        }
        return "fin";

    }

    public function plano_contable_ingresar($anio_mes, $tipo, $seg, $cobertura, $empresa,Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $cob_compar = $cobertura;

        if($cob_compar == 0){
            $cob_compar = null;
        }

        $agrupado = Ap_Agrupado::where('mes_plano',$anio_mes)->where('cobertura',$cob_compar)->where('empresa',$empresa)->where('seguro',$seg)->where('id_tipo_seg',$tipo)->first();
        
        if(is_null($agrupado)){  
            /*
            if($tipo=='MSP'){
                $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $anio_mes)
                    ->where('archivo_plano_cabecera.id_seguro', $seg)
                    ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                    ->where('archivo_plano_cabecera.id_empresa', $empresa)
                    ->where('archivo_plano_cabecera.estado', '1')
                    ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
                    ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
                    ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                    ->where('apd.estado', '1')
                    ->select( 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tseg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'archivo_plano_cabecera.id as arplaca', 'apd.subtotal','apd.total_solicitado_usd')
                    ->orderby('archivo_plano_cabecera.id_paciente')
                    ->orderby('archivo_plano_cabecera.fecha_ing')
                    ->orderby('tipo_ex.orden_plano')
                    ->get();//dd($archivo_plano);
            }else{
                $archivo_plano = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $anio_mes)
                    ->where('archivo_plano_cabecera.id_seguro', $seg)
                    ->where('archivo_plano_cabecera.id_cobertura_comp', $cob_compar)
                    ->where('archivo_plano_cabecera.id_empresa', $empresa)
                    ->where('archivo_plano_cabecera.estado', '1')
                    ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
                    ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
                    ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                    ->where('tseg.tipo_principal',$tipo)
                    ->where('apd.estado', '1')
                    ->select( 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tseg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'archivo_plano_cabecera.id as arplaca', 'apd.subtotal')
                    ->orderby('archivo_plano_cabecera.id_paciente')
                    ->orderby('archivo_plano_cabecera.fecha_ing')
                    ->orderby('tipo_ex.orden_plano')
                    ->get();//dd($archivo_plano);    
            }*/

            $sumpxq = 0;$sumiva_ap = 0;$base0 = 0;$base12 = 0;$admin = 0;$total = 0;
            /*foreach($archivo_plano as $cabecera){
                if($tipo=='MSP'){
                    $iva_ap = $cabecera->iva;
                    $iva_ap = round($iva_ap,2);
                    $sumiva_ap += $iva_ap;
                    if($cabecera->porcentaje_iva == 0){
                        $base0 += $cabecera->subtotal;
                    }else{
                        $base12 += $cabecera->subtotal;
                    }
                    $admin += $cabecera->porcentaje10;
                    $total += $cabecera->total_solicitado_usd;    
                }else{
                    $pxq = $cabecera->cantidad * $cabecera->valor;
                    $iva_ap = $cabecera->valor_unitario * $cabecera->porcentaje_iva * $cabecera->cantidad;
                    $sumiva_ap += $iva_ap;
                    $sumpxq += $pxq;
                    if($cabecera->porcentaje_iva == 0){
                        $base0 += $cabecera->subtotal;
                    }else{
                        $base12 += $cabecera->subtotal;
                    }
                    $admin += $cabecera->porcentaje10;    
                }    
                         
            } */

            $base0      = $request->base0;
            $base12     = $request->base12;
            $admin      = $request->admin;
            $sumiva_ap  = $request->iva;
            
            if($tipo=='MSP'){
                $msp = $total - $admin - $base0 - $base12 - $sumiva_ap;
                $valor_cobrado = $total;
            }else{
                $msp = 0;
                $valor_cobrado = $base0 + $base12 + $admin + $sumiva_ap;    
            }
            
           
            $arr = [
                'id_tipo_seg'     => $tipo,
                'mes_plano'       => $anio_mes,
                'empresa'         => $empresa,
                'seguro'          => $seg,
                'cobertura'       => $cob_compar,
                'msp'             => $msp,
                'base_0'          => $base0,
                'base_iva'        => $base12,
                'gast_amd10'      => $admin,
                'total_iva'       => $sumiva_ap,
                'valor_cobrado'   => $valor_cobrado,
                'estado_pago'     => '0',  
 
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                
            ];

            $id = Ap_Agrupado::insertGetId($arr);

        }else{

            $id = $agrupado->id;
        }

        $agrupado = Ap_Agrupado::find($id);  

        return view('archivo_plano/archivo/plano_contable', ['agrupado' => $agrupado ]);

    }
    public function plano_contable_editar( $id )
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;


        $agrupado = Ap_Agrupado::find($id); 


        return view('archivo_plano/archivo/plano_contable', ['agrupado' => $agrupado ]);

    }

    public function plano_contable_eliminar( $id )
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;


        $agrupado = Ap_Agrupado::find($id);  

        $agrupado->delete();

        return "ok";

    }


    public function guardar_agrupado_vt(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id = $request->id;
        $base_12_pref =0;
        $base_0_pref =0;
        $total_base_12 = 0;
        $agrupado = Ap_Agrupado::find($id);
        
        $base_0          = $agrupado->base_0;
        $base_12         = $agrupado->base_iva;
        $gast_amd10      = $agrupado->gast_amd10;
        $total_iva       = $agrupado->total_iva;
        $valor_cobrado   = $agrupado->valor_cobrado;
        $valor_facturado = $request['valor_facturado'.$id];
        //$valor_aceptado  = $request['valor_aceptado'.$id];
        $estado_pago     = $request['estado'.$id];
        
        $valor_objetado  =  $valor_cobrado - $request['valor_facturado'.$id];
        $valor_iva_cobrar = $request['valor_iva_cobrar'.$id];
        //$facturado_0     = $request['facturado_0'.$id];
        //$facturado_12    = $request['facturado_12'.$id];
        //$iva_facturado   = $facturado_12*0.12;
        //$valor_facturado = $facturado_12+$facturado_0+$iva_facturado;
        //$valor_objetado  = $valor_cobrado-$;
        //$objetado_0      = ($base_0+$gast_amd10)-$facturado_0;
        //$objetado_12     = $base_12-$facturado_12;
        //$iva_objetado    = $objetado_12*0.12;
        
        $porcentaje_glosa =0;
        if (($valor_cobrado>0)){
            $porcentaje_glosa = $valor_objetado/$valor_cobrado;
            $porcentaje_glosa = $porcentaje_glosa*100;
 
         }
    
        $base_12_pref =  $valor_iva_cobrar * 100 / 12;
        $total_base_12 = $base_12_pref + $valor_iva_cobrar;
        //dd($valor_iva_cobrar, $base_12_pref, $total_base_12);
        $base_0_pref = $valor_facturado - $total_base_12;

        $arr = [

            'cod_proceso'          => $request['cod_proceso'.$id],
            'valor_aceptado'       => $request['valor_aceptado'.$id],
            //'facturado_0'          => $request['facturado_0'.$id],
            //'facturado_12'         => $request['facturado_12'.$id],
            //'iva_facturado'        => $facturado_12*0.12,
            'valor_facturado'      => $valor_facturado,
            'valor_objetado'       => $valor_objetado,
            //'objetado_0'           => $objetado_0,
            //'objetado_12'          => $base_12-$facturado_12,
            //'iva_objetado'         => $objetado_12*0.12,
            'valor_iva_cobrar'     => $valor_iva_cobrar,
            'porcentaje_glosa'     => $porcentaje_glosa, 
            //'valor_levantar'       => $request['valor_levantar'.$id],
            //'valor_aceptado'       => $request['valor_aceptado'.$id],
            'estado_pago'          => $request['estado'.$id],
            'id_usuariomod'        => $idusuario,
            'ip_modificacion'      => $ip_cliente,
            'base_12_pref'         => $base_12_pref,
            'base_0_pref'          => $base_0_pref,
        ];

        $agrupado->update($arr);  

    
        

        return "ok";
    }

    public function total_agrupado(Request $request)
    {
       
        $mes_plano  = $request->mes_plano;
        $seguro     = $request->seguro;

        if($mes_plano == null){
            $mes_plano = date('mY');
        }

        if($seguro == null){
            $seguro = '2';        
        }    

        $agrupados = Ap_Agrupado::where('mes_plano',$mes_plano)->where('seguro',$seguro)->get();

        $seguros = Seguro::where('inactivo','1')->where('tipo','0')->get();

        return view('archivo_plano/archivo/index_agrupado',['agrupados' => $agrupados, 'mes_plano' => $mes_plano, 'seguro' => $seguro, 'seguros' => $seguros]);


    }    

    public function total_agrupado_crear(){

        $empresas = Empresa::where('estado',1)->where('admision',1)->get();
        $seguros  = Seguro::where('inactivo',1)->where('tipo',0)->get();
        $tipos    = Tipo_Seguro::where('estado',1)->select('tipo_principal')->groupby('tipo_principal')->orderby('tipo_principal')->get();

        return view('archivo_plano/archivo/crear_agrupado',['empresas' => $empresas, 'seguros' => $seguros, 'tipos' => $tipos ]); 

    }

    public function total_agrupado_store(Request $request){
        //ACTIVO-ISSFA
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        $mes_plano     = $request->mes_plano;
        $empresa       = $request->empresa;
        $seguro        = $request->seguro;
        $id_tipo_seg   = $request->id_tipo_seg;
        $base_0        = $request->base_0;
        $base_iva      = $request->base_iva;
        $gast_amd_10   = $request->gast_amd_10;
        $total_iva     = $request->total_iva;
        $valor_cobrado = $request->valor_cobrado;
        $estado_pago   = $request->estado;

        $arr = [
            
            'id_tipo_seg'     => $id_tipo_seg,
            'mes_plano'       => $mes_plano,
            'empresa'         => $empresa,
            'seguro'          => $seguro,
            'cobertura'       => null,
            'msp'             => 0,
            'base_0'          => $base_0,
            'base_iva'        => $base_iva,
            'gast_amd10'      => $gast_amd_10,
            'total_iva'       => $total_iva,
            'valor_cobrado'   => $valor_cobrado,
            'estado_pago'     => $estado_pago,

            'cod_proceso'     => $request['cod_proceso'],
            'valor_facturado' => $request['valor_facturado'],
            'valor_objetado'  => $request['valor_objetado'],
            'porcentaje_glosa'=> $request['porcentaje_glosa'],
            'valor_levantar'  => $request['valor_levantar'],
            'valor_aceptado'  => $request['valor_aceptado'],

            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        $id = Ap_Agrupado::insertGetId($arr);

        return "ok";

    }    

    public function total_agrupado_editar($id){

        $agrupado = Ap_Agrupado::find($id);

        $empresas = Empresa::where('estado',1)->where('admision',1)->get();
        $seguros  = Seguro::where('inactivo',1)->where('tipo',0)->get();
        $tipos    = Tipo_Seguro::where('estado',1)->select('tipo_principal')->groupby('tipo_principal')->orderby('tipo_principal')->get();

        return view('archivo_plano/archivo/editar_agrupado',['empresas' => $empresas, 'seguros' => $seguros, 'tipos' => $tipos, 'agrupado' => $agrupado ]); 

    }

    public function total_agrupado_update(Request $request){

        
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id = $request->id;

        $agrupado =  Ap_Agrupado::find($id);
        
        $mes_plano     = $request['mes_plano'.$id];
        $empresa       = $request['empresa'.$id];
        $seguro        = $request['seguro'.$id];
        $id_tipo_seg   = $request['id_tipo_seg'.$id];
        $base_0        = $request['base_0'.$id];
        $base_iva      = $request['base_iva'.$id];
        $gast_amd_10   = $request['gast_amd_10'.$id];
        $total_iva     = $request['total_iva'.$id];
        $valor_cobrado = $request['valor_cobrado'.$id];
        $estado_pago   = $request['estado'.$id];

        $arr = [
            
            'id_tipo_seg'     => $id_tipo_seg,
            'mes_plano'       => $mes_plano,
            'empresa'         => $empresa,
            'seguro'          => $seguro,
            'cobertura'       => null,
            'msp'             => 0,
            'base_0'          => $base_0,
            'base_iva'        => $base_iva,
            'gast_amd10'      => $gast_amd_10,
            'total_iva'       => $total_iva,
            'valor_cobrado'   => $valor_cobrado,
            'estado_pago'     => $estado_pago,

            'cod_proceso'     => $request['cod_proceso'.$id],
            'valor_facturado' => $request['valor_facturado'.$id],
            'valor_objetado'  => $request['valor_objetado'.$id],
            'porcentaje_glosa'=> $request['porcentaje_glosa'.$id],
            'valor_levantar'  => $request['valor_levantar'.$id],
            'valor_aceptado'  => $request['valor_aceptado'.$id],


            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        
        $agrupado->update($arr);
        
        return "ok";

    }  
    public function resumen_planillas(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd("here");
        $sumatoria = [];$agrupados = [];

        $mes_plano    = $request->mes_plano;
        $tipo_seg     = $request->id_tipo_seguro;
        $cob_compar   = $request->id_cobertura_comp;
        $seg          = $request->seguro;
        $empresa      = $request->id_empresa;
        $arr_base_0   = [];
        $arr_base_iva = [];
        $arr_v_iva    = [];
        $arr_amd_10   = [];

        $grupos = DB::table('tipo_seguro')->select('tipo_principal')->groupby('tipo_principal')->orderBy('tipo_principal','asc')->get();
        //dd($grupos);
        if($mes_plano != null){
            foreach($grupos as $grupo){
                //dd($grupo);
                $archivo_plano[$grupo->tipo_principal] = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                    ->where('archivo_plano_cabecera.id_seguro', $seg)
                    ->whereNull('archivo_plano_cabecera.id_cobertura_comp')
                    ->where('archivo_plano_cabecera.id_empresa', $empresa) 
                    ->where('archivo_plano_cabecera.estado', '1')
                    ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
                    ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
                    ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                    ->where('tseg.tipo_principal',$grupo->tipo_principal)
                    ->where('apd.estado', '1')
                    ->select( 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tseg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'archivo_plano_cabecera.id as arplaca', 'apd.subtotal')
                    ->orderby('archivo_plano_cabecera.id_paciente')
                    ->orderby('archivo_plano_cabecera.fecha_ing')
                    ->orderby('tipo_ex.orden_plano')
                    ->get();
            }
            
            $sumatoria = [];
            foreach($archivo_plano as $key => $ap_tipo){
                $sumpxq = 0;$sumiva_ap = 0;$base0 = 0;$base12 = 0;$admin = 0;
                foreach($ap_tipo as $cabecera){
                    $pxq = $cabecera->cantidad * $cabecera->valor;
                    $iva_ap = $cabecera->valor_unitario * $cabecera->porcentaje_iva * $cabecera->cantidad;
                    $sumiva_ap += $iva_ap;
                    $sumpxq += $pxq;
                    if($cabecera->porcentaje_iva == 0){
                        $base0 += $cabecera->subtotal;
                    }else{
                        $base12 += $cabecera->subtotal;
                    }
                    $admin += $cabecera->porcentaje10;
                        
                       
                }
                $sumpxq = round($sumpxq,2); 
                $base0 = round($base0,2); 
                $base12 = round($base12,2); 
                $admin = round($admin,2); 
              
                $sumatoria[$key] = [
                    'mes_plano' => $mes_plano,
                    'pxq' => $sumpxq,
                    'iva' => $sumiva_ap,
                    'base0' => $base0,
                    'base12' => $base12,
                    'admin' => $admin
                ];
               
                
            }
           
            $agrupados = Ap_Agrupado::where('mes_plano',$mes_plano)->where('empresa',$empresa)->where('seguro',$seg)->get();
 

        }    
        $cant_pac = [];$base_0 = [];$base_iva = [];$base_iva = [];$v_iva = [];$amd_10 = [];
    
        $sumatoria_issfa = [];


        if($mes_plano != null){
            foreach($grupos as $grupo_issfa){
                //dd($grupo);
                $archivo_plano_issfa[$grupo_issfa->tipo_principal] = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                    ->where('archivo_plano_cabecera.id_seguro', $seg)
                    ->where('archivo_plano_cabecera.id_empresa', $empresa)
                    ->where('archivo_plano_cabecera.estado', '1')
                    ->where('archivo_plano_cabecera.id_cobertura_comp', '3')
                    ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
                    //->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                    //->join('tipo_seguro as tseg', 'tseg.id', 'archivo_plano_cabecera.id_tipo_seguro')
                    ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
                    ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                    ->where('tseg.tipo_principal',$grupo_issfa->tipo_principal)
                    ->where('apd.estado', '1')
                    ->select( 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tseg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'archivo_plano_cabecera.id as arplaca', 'apd.subtotal')
                    ->orderby('archivo_plano_cabecera.id_paciente')
                    ->orderby('archivo_plano_cabecera.fecha_ing')
                    ->orderby('tipo_ex.orden_plano')
                    ->get();
            }
            //dd($archivo_plano_issfa);
            $sumatoria_issfa = [];
            foreach($archivo_plano_issfa as $key => $ap_tipo){
                $sumpxq = 0;$sumiva_ap = 0;$base0 = 0;$base12 = 0;$admin = 0;
                foreach($ap_tipo as $cabecera){
                    $pxq = $cabecera->cantidad * $cabecera->valor;
                    $iva_ap = $cabecera->valor_unitario * $cabecera->porcentaje_iva * $cabecera->cantidad;
                    $sumiva_ap += $iva_ap;
                    $sumpxq += $pxq;
                    if($cabecera->porcentaje_iva == 0){
                        $base0 += $cabecera->subtotal;
                    }else{
                        $base12 += $cabecera->subtotal;
                    }
                    $admin += $cabecera->porcentaje10;
                        
                       
                }
                $sumpxq = round($sumpxq,2); 
                $base0 = round($base0,2); 
                $base12 = round($base12,2); 
                $admin = round($admin,2); 
   
                $sumatoria_issfa[$key] = [
                    'mes_plano' => $mes_plano,
                    'pxq' => $sumpxq,
                    'iva' => $sumiva_ap,
                    'base0' => $base0,
                    'base12' => $base12,
                    'admin' => $admin
                ];
           
                
            }

        }    
    

        $sumatoria_isspol = [];


        if($mes_plano != null){
            foreach($grupos as $grupo_isspol){
                //dd($grupo);
                $archivo_plano_isspol[$grupo_isspol->tipo_principal] = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                    ->where('archivo_plano_cabecera.id_seguro', $seg)
                    ->where('archivo_plano_cabecera.id_empresa', $empresa)
                    ->where('archivo_plano_cabecera.estado', '1')
                    ->where('archivo_plano_cabecera.id_cobertura_comp', '6')
                    ->join('tipo_seguro as tseg','tseg.id','archivo_plano_cabecera.id_tipo_seguro')
                    ->join('archivo_plano_detalle as apd', 'archivo_plano_cabecera.id', 'apd.id_ap_cabecera')
                    ->join('ap_tipo_examen as tipo_ex', 'tipo_ex.tipo', 'apd.tipo')
                    ->where('tseg.tipo_principal',$grupo_isspol->tipo_principal)
                    ->where('apd.estado', '1')
                    ->select( 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.id_usuario', 'archivo_plano_cabecera.fecha_ing', 'apd.descripcion', 'apd.tipo', 'apd.codigo', 'apd.cantidad', 'apd.valor', 'apd.porcentaje10', 'archivo_plano_cabecera.parentesco', 'archivo_plano_cabecera.presuntivo_def', 'apd.iva', 'archivo_plano_cabecera.cie10', 'tseg.tipo as tseg', 'apd.porcentaje_iva', 'archivo_plano_cabecera.id_hc', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.nombres', 'apd.valor_unitario', 'archivo_plano_cabecera.id as arplaca', 'apd.subtotal')
                    ->orderby('archivo_plano_cabecera.id_paciente')
                    ->orderby('archivo_plano_cabecera.fecha_ing')
                    ->orderby('tipo_ex.orden_plano')
                    ->get();
            }
            //dd($archivo_plano);
            $sumatoria_isspol = [];
            foreach($archivo_plano_isspol as $key => $ap_tipo){
                $sumpxq = 0;$sumiva_ap = 0;$base0 = 0;$base12 = 0;$admin = 0;
                foreach($ap_tipo as $cabecera){
                    $pxq = $cabecera->cantidad * $cabecera->valor;
                    $iva_ap = $cabecera->valor_unitario * $cabecera->porcentaje_iva * $cabecera->cantidad;
                    $sumiva_ap += $iva_ap;
                    $sumpxq += $pxq;
                    if($cabecera->porcentaje_iva == 0){
                        $base0 += $cabecera->subtotal;
                    }else{
                        $base12 += $cabecera->subtotal;
                    }
                    $admin += $cabecera->porcentaje10;
                        
                       
                }
                $sumpxq = round($sumpxq,2); 
                $base0 = round($base0,2); 
                $base12 = round($base12,2); 
                $admin = round($admin,2); 
                //$sumiva_ap = round($sumiva_ap,2);
                $sumatoria_isspol[$key] = [
                    'mes_plano' => $mes_plano,
                    'pxq' => $sumpxq,
                    'iva' => $sumiva_ap,
                    'base0' => $base0,
                    'base12' => $base12,
                    'admin' => $admin
                ];
                //dd($key, $sumpxq, $sumiva_ap, $base0, $base12, $admin);
                
            }
            //dd($sumatoria);
           

        }    
      

        $tipo_seguros = Ap_Tipo_Seg::where('estado', '1')->get();

        $empresas         = Empresa::where('estado', '1')->get();
        $seguro           = Seguro::where('tipo', '0')->get();
        $seguros_publicos = Seguro::where('inactivo', '1')->where('tipo', '0')->orderBy('nombre')->get();
        $mes_plan         = Archivo_Plano_Cabecera::select('mes_plano')->orderBy('mes_plano')->groupBy('mes_plano')->where('estado', '1')->get();


        return view('archivo_plano/estadisticos/resumen', ['tipo_seguros' => $tipo_seguros, 'mes_plano' => $mes_plano, 'empresas' => $empresas, 'seguros_publicos' => $seguros_publicos, 'seguro' => $seguro, 'seg' => $seg, 'empresa' => $empresa, 'mes_plan' => $mes_plan, 'arr_base_0' => $arr_base_0, 'arr_base_iva' => $arr_base_iva, 'arr_v_iva' => $arr_v_iva, 'arr_amd_10' => $arr_amd_10, 'cant_pac' => $cant_pac, 'base_0' => $base_0, 'base_iva' => $base_iva, 'v_iva' => $v_iva, 'amd_10' => $amd_10, 'tipo_seg' => $tipo_seg, 'cob_compar' => $cob_compar, 'sumatoria' => $sumatoria, 'agrupados' => $agrupados, 'sumatoria_issfa'=> $sumatoria_issfa, 'sumatoria_isspol' => $sumatoria_isspol]);
    }

    public function resumen_agrupado(Request $request)
    {
        //ini_set('memory_limit', '5000M');
        $mes_plano      = $request->mes_plano;
        $id_seguro      = $request->id_seguro;
        $tipo           = $request->tipo;
        $id_empresa     = $request->id_empresa;
        $id_cobertura_comp     = $request->id_cobertura_comp; 

        $archivo_plano_activo        = [];
        $archivo_plano_conyugue      = [];
        $archivo_plano_0_1           = [];
        $archivo_plano_2_6           = [];
        $archivo_plano_7_17          = [];
        $archivo_plano_jubilado      = [];
        $archivo_plano_jub_campesino = [];
        $archivo_plano_montepio      = [];
        $archivo_plano_ssc           = [];
        $archivo_plano               = [];

        $tipos_seguro = DB::table('tipo_seguro')->where('estado',1)->where('tipo_principal',$tipo)->get();
        foreach($tipos_seguro as $tipov){
            //ACTIVO
            $archivo_plano_activo[$tipov->id] = Archivo_Plano_Cabecera::where('archivo_plano_cabecera.mes_plano', $mes_plano)
                ->where('archivo_plano_cabecera.id_tipo_seguro', $tipov->id)
                ->where('archivo_plano_cabecera.id_seguro', $id_seguro)
                ->where('archivo_plano_cabecera.id_empresa', $id_empresa)
                ->where('archivo_plano_cabecera.id_cobertura_comp', $id_cobertura_comp)
                ->where('archivo_plano_cabecera.estado', '1')
                ->join('paciente as pac', 'pac.id', 'archivo_plano_cabecera.id_paciente')
                ->select(DB::raw('CASE WHEN (pac.apellido2 = "(N/A)" OR pac.apellido2 = "N/A" OR pac.apellido2 = ".") THEN CONCAT(pac.apellido1," ",pac.nombre1," ",pac.nombre2) ELSE CONCAT(pac.apellido1," ",pac.apellido2," ",pac.nombre1," ",pac.nombre2) END AS full_name'), 'pac.*', 'archivo_plano_cabecera.id_paciente', 'archivo_plano_cabecera.fecha_ing', 'archivo_plano_cabecera.nom_procedimiento', 'archivo_plano_cabecera.created_at'
                    , 'archivo_plano_cabecera.fecha_alt', 'archivo_plano_cabecera.id', 'archivo_plano_cabecera.id_tipo_seguro', 'archivo_plano_cabecera.id_usuariocrea', 'archivo_plano_cabecera.id_cobertura_comp','archivo_plano_cabecera.id_seguro')
                ->orderby('full_name')
                ->get();
        }
        
        //dd($archivo_plano_activo);   

        Excel::create('ReporteAgrupadoIESS', function ($excel) use ($archivo_plano, $archivo_plano_activo) {
            $excel->sheet('ReporteAgrupadoIESS', function ($sheet) use ($archivo_plano, $archivo_plano_activo) {
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHAING');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHAEGR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO_SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBREBENEF');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('USUARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA_CREACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });
                $sheet->cell('H1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $sheet->cell('I1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE 0');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $sheet->cell('J1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE 12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $sheet->cell('K1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IVA 12%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $sheet->cell('L1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GAST.ADM 10%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                $sheet->cell('M1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');

                });

                //Muestra Todos los tipos de Seguro
                //ACTIVO
                $i = 2; $base = 0;
                if (!is_null($archivo_plano_activo)) {
                    //dd("entro");

                    foreach ($archivo_plano_activo as $planos) {
                        foreach ( $planos as $value ){

                            $base_0        = 0;
                            $sum           = 0;
                            $sum_bas_0     = 0;
                            $sum_bas_iva   = 0;
                            $sum_v_iva     = 0;
                            $sum_gasto_amd = 0;
                            $total_m_iva   = 0;

                            $detalles_activo = $value->detalles->where('estado', 1);
                            //dd($detalles_activo->count());
                            

                            foreach ($detalles_activo as $detalle) {

                                //Si porcentaje_iva == 0  BASE_0
                                if (($detalle->porcentaje_iva == 0) && ($detalle->estado == 1)) {

                                    $sum_bas_0 = $sum_bas_0 + $detalle->subtotal;
                                    if($value->id_seguro=='5'){
                                        $sum_bas_0 = $sum_bas_0 + $detalle->valor_porcent_clasifi;
                                    }

                                }

                                //Si porcentaje_iva == 0.12  BASE_IVA
                                if (($detalle->porcentaje_iva == 0.12) && ($detalle->estado == 1)) {

                                    $sum_bas_iva = $sum_bas_iva + $detalle->subtotal;

                                }

                                //Suma v_iva
                                if ($detalle->estado == 1) {

                                    //$sum_v_iva = $sum_v_iva+$detalle->iva;
                                    //$sum_v_iva += round(($detalle->subtotal * $detalle->porcentaje_iva), 4);
                                    //Suma v_iva
                                    $sum_v_iva = $sum_v_iva + ($detalle->valor_unitario * $detalle->cantidad * $detalle->porcentaje_iva);
                                }

                                //Porcentaje_10  GAST_ADM_10
                                if (($detalle->porcent_10 == 0.1) && ($detalle->estado == 1)) {

                                    $sum_gasto_amd = $sum_gasto_amd + $detalle->porcentaje10;

                                }

                            }
                            //dd($value);

                            

                            //TOTAL_M_IVA
                            $total_m_iva = $sum_bas_0 + $sum_bas_iva + $sum_v_iva + $sum_gasto_amd;

                            $sheet->cell('A' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue(date('d/m/Y', strtotime($value->fecha_ing)));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });

                            $sheet->cell('B' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue(date('d/m/Y', strtotime($value->fecha_alt)));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });

                            $sheet->cell('C' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                               
                                    $txt_cobertura = '';
                                    if ($value->id_cobertura_comp != null) {
                                        $txt_cobertura = $value->cob_compartida->nombre;
                                    }
                                    $cell->setValue( $value->tiposeguro->nombre.' '. $txt_cobertura);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                

                            });

                            $sheet->cell('D' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->id_paciente);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                            $sheet->cell('E' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->apellido2 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);

                                if($value->paciente->apellido2 == '(N/A)' || $value->paciente->apellido2 == 'N/A' || $value->paciente->apellido2 == '.'){
                                    $cell->setValue($value->paciente->apellido1 . ' ' . $value->paciente->nombre1 . ' ' . $value->paciente->nombre2);
                                }
                                
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                            $sheet->cell('F' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                if (!is_null($value->id_usuariocrea)) {
                                    $cell->setValue($value->usuario_crear->apellido1);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                }

                            });
                            $sheet->cell('G' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue(date('d/m/Y', strtotime($value->created_at)));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                            $sheet->cell('H' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue($value->nom_procedimiento);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                            //$base+=$value->detalles->subtotal;
                            $sheet->getStyle('I' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                            $sheet->cell('I' . $i, function ($cell) use ($sum_bas_0) {
                                // manipulate the cel
                                $cell->setValue($sum_bas_0);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });

                            $sheet->getStyle('J' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                            $sheet->cell('J' . $i, function ($cell) use ($sum_bas_iva) {
                                // manipulate the cel
                                $cell->setValue($sum_bas_iva);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });

                            $sheet->getStyle('K' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                            $sheet->cell('K' . $i, function ($cell) use ($sum_v_iva) {
                                // manipulate the cel
                                $cell->setValue($sum_v_iva);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });

                            $sheet->getStyle('L' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                            $sheet->cell('L' . $i, function ($cell) use ($sum_gasto_amd) {
                                // manipulate the cel
                                $cell->setValue($sum_gasto_amd);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });

                            $sheet->getStyle('M' . $i)->getNumberFormat()->setFormatCode('"$"* # ##0.00');
                            $sheet->cell('M' . $i, function ($cell) use ($total_m_iva) {
                                // manipulate the cel
                                $cell->setValue($total_m_iva);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                            $i++;
                        }    
                    }

                }

            });
        })->export('xlsx');

    }

    public function plano_contable_agrupado_editar( $id )
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;


        $agrupado = Ap_Agrupado::find($id);  

        return view('archivo_plano/contable/plano_agrupado_contable', ['agrupado' => $agrupado ]);

    }

    public function plano_contable_agrupado_eliminar( $id )
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;


        $agrupado = Ap_Agrupado::find($id);  

        $agrupado->delete();

        return "ok";

    }


    public function guardar_agrupado_contable(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id = $request->id;
        $agrupado = Ap_Agrupado::find($id);


        $arr = [
            'cod_proceso'       => $request['cod_proceso'.$id],
            'valor_facturado'   => $request['valor_facturado'.$id],
            'facturado_0'       => $request['facturado_0'.$id],
            'facturado_12'      => $request['facturado_12'.$id],
            'valor_objetado'    => $request['valor_objetado'.$id],
            'objetado_0'        => $request['objetado_0'.$id],
            'objetado_12'       => $request['objetado_12'.$id],
            'porcentaje_glosa'  => $request['porcentaje_glosa'.$id],
            'valor_levantar'    => $request['valor_levantar'.$id],
            'valor_aceptado'    => $request['valor_aceptado'.$id],
            'estado_pago'       => $request['estado'.$id],
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $agrupado->update($arr);

        return "ok";
    }
  
    public function total_agrupado_contable(Request $request)
    {
        $empresa   = $request->empresa;
        $mes_plano = $request->mes_plano;
        $seguro    = $request->seguro;

        if($mes_plano == null){
            $mes_plano = date('mY');
        }

        if($seguro == null){
            $seguro = '2';        
        }    

        $agrupados  = Ap_Agrupado::where('mes_plano',$mes_plano)->where('seguro',$seguro) -> where('empresa', $empresa)->get();

        $seguros   = Seguro::where('inactivo','1')->where('tipo','0')->get();

        $empresa  = Empresa::where('estado',1)->where('prioridad',1)->get();
              

        return view('archivo_plano/contable/index_contable', ['agrupados'=> $agrupados, 'mes_plano' => $mes_plano, 'seguro' => $seguro ,'seguros' => $seguros,  'empresa' => $empresa]);
       
       //return view('archivo_plano/contable/index_contable',['agrupado' => $agrupado, 'empresa' =>$empresa,'mes_plano'=> $anio_mes, 'seguro'=>$seg, 'id_tipo_seg'=>$tipo ]);

    
        }
        

    public function total_agrupado_contable_crear(){

        $empresas = Empresa::where('admision', '1')->get();
        $seguro  = Seguro::where('inactivo',1)->where('tipo',0)->get();
        $tipos    = Tipo_Seguro::where('estado',1)->select('tipo_principal')->groupby('tipo_principal')->orderby('tipo_principal')->get();
         

        return view('archivo_plano/contable/crear_contable',['empresas' => $empresas, 'seguro' => $seguro, 'tipos' => $tipos ]); 

    }

    public function total_agrupado_contable_store(Request $request){
        
        
        // dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
                
        $mes_plano        = $request->mes_plano;
        $empresa          = $request->empresa;
        $seguro           = $request->seguro;
        $id_tipo_seg      = $request->id_tipo_seg;
        $base_0           = $request->base_0;
        $base_iva         = $request->base_iva;
        $gast_amd10       = $request->gast_amd10;
        $total_iva        = $request->total_iva;
        $estado_pago      = $request->estado_pago;
        $valor_facturado  = $request->valor_facturado;
        $valor_cobrado    = $base_0+$base_iva+$total_iva+$gast_amd10;
        $valor_objetado   = $valor_cobrado-$valor_facturado;
        $porcentaje_glosa = 0;
        if (($valor_cobrado>0)){
           $porcentaje_glosa = $valor_objetado/$valor_cobrado;
           $porcentaje_glosa = $porcentaje_glosa*100;

        }
                
        //$facturado_0 = $request->facturado_0;
        //$facturado_12 =$request->facturado_12;
        //$objetado_0 = $request->objetado_0;
        //$objetado_12= $request->objetado_12;
        $ap_agrupado = Ap_Agrupado:: where('mes_plano', $request->mes_plano)-> where('empresa', $request->empresa)->where('seguro', $request->seguro)->where('id_tipo_seg', $request->id_tipo_seg)->first();
        
        //dd($request->all());
        if (!is_null($ap_agrupado)){
            return  ['msj'=> 'ya existe mes plano con '];
        }

        $arr = [
            
            'id_tipo_seg'     => $id_tipo_seg,
            'mes_plano'       => $request['mes_plano'],
            'empresa'         => $empresa,
            'seguro'          => $seguro,
            'cobertura'       => null,
            'msp'             => 0,
            'base_0'          => $base_0,
            'base_iva'        => $base_iva,
            'gast_amd10'      => $gast_amd10,
            'total_iva'       => $total_iva,
            'valor_cobrado'   => $valor_cobrado,
            'estado_pago'     => $estado_pago,

            'cod_proceso'     => $request['cod_proceso'],
            'valor_facturado' => $request['valor_facturado'],
            
            'valor_objetado'  => $valor_cobrado-$valor_facturado,
          
            'porcentaje_glosa'=> $request['porcentaje_glosa'], 
            
            'valor_levantar'  => $request['valor_levantar'], 
            'valor_aceptado'  => $request['valor_aceptado'],

            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        
        //$cod_proceso = $request['cod_proceso']; 
        //dd($cod_proceso);
        /*$pre_ordenes = Ap_Agrupado::where('cod_proceso', $request->cod_proceso)->first();
        if ($cod_proceso == $pre_ordenes->cod_proceso){
            return ['msj' => 'ya existe el No. de Tramite, ingrese uno distinto por favor'];
        }*/
        

        $id = Ap_Agrupado::insertGetId($arr);
    
        return  ['msj'=> 'Guardado correctamente'];

    }    

    public function total_agrupado_contable_editar($id){

        $agrupado = Ap_Agrupado::find($id);

        $empresas = Empresa::where('estado',1)->where('admision',1)->get();
        $seguros  = Seguro::where('inactivo',1)->where('tipo',0)->get();
        $tipos    = Tipo_Seguro::where('estado',1)->select('tipo_principal')->groupby('tipo_principal')->orderby('tipo_principal')->get();
       
        return view('archivo_plano/contable/editar_contable',['empresas' => $empresas, 'seguros' => $seguros, 'tipos' => $tipos, 'agrupado' => $agrupado ]); 

    }

    public function total_agrupado_contable_update(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id = $request->id;

        $agrupado =  Ap_Agrupado::find($id);
        
        $mes_plano       = $request['mes_plano'.$id];
        $empresa         = $request['empresa'.$id];
        $seguro          = $request['seguro'.$id];
        $id_tipo_seg     = $request['id_tipo_seg'.$id];
        $base_0          = $request['base_0'.$id];
        $base_iva        = $request['base_iva'.$id];
        $gast_amd_10     = $request['gast_amd_10'.$id];
        $total_iva       = $request['total_iva'.$id];
        $valor_cobrado   = $request['valor_cobrado'.$id];
        $estado_pago     = $request['estado'.$id];
        $gast_amd10      = $request['gast_amd10'.$id];
        $valor_facturado = $request['valor_facturado'.$id];
        //$facturado_0 = $request['facturado_0'.$id];
       // $facturado_12 =$request['facturado_12'.$id];
       // $objetado_0 = $request['objetado_0'.$id];
        //$objetado_12= $request['objetado_12'.$id];

        $valor_cobrado    = $base_0+$base_iva+$total_iva+$gast_amd10;
        $valor_objetado   = $valor_cobrado-$valor_facturado;
        $porcentaje_glosa = 0;
        if (($valor_cobrado>0)){
           $porcentaje_glosa = $valor_objetado/$valor_cobrado;
           $porcentaje_glosa = $porcentaje_glosa*100;

        }
       
        $arr = [
            
            'id_tipo_seg'     => $id_tipo_seg,
            'mes_plano'       => $mes_plano,
            'empresa'         => $empresa,
            'seguro'          => $seguro,
            'cobertura'       => null,
            'msp'             => 0,
            'base_0'          => $base_0,
            'base_iva'        => $base_iva,
            'gast_amd10'      => $gast_amd10,
            'total_iva'       => $total_iva,
            'valor_cobrado'   => $valor_cobrado,
            'estado_pago'     => $estado_pago,

            'cod_proceso'     => $request['cod_proceso'.$id],
            'valor_facturado' => $request['valor_facturado'.$id],
            //'facturado_0'      => $request['facturado_0'.$id],
            //'facturado_12'      => $request['facturado_12'.$id],
            'valor_objetado'  => $valor_cobrado.$id - $valor_facturado.$id,
            //'objetado_0'      => $valor_cobrado.$id - $valor_facturado.$id,
            //'objetado_12'      => $request['objetado_12'.$id],
            'porcentaje_glosa'=> $valor_objetado.$id/$valor_cobrado.$id*100, 
            'valor_levantar'  => $request['valor_levantar'.$id],
            'valor_aceptado'  => $request['valor_aceptado'.$id],

            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        $agrupado->update($arr);

        return "ok";

    }  

    public function ingresar_prefactura($id){

        //dd($id); chid-b0
        $vt_array = explode("-",$id);
        $id_ap = substr($vt_array[0],2); 
        $base = $vt_array[1];
        //dd($id,$id_ap,$base);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id; 
        $agrupado    = Ap_Agrupado::find($id_ap);
        //dd($agrupado);    
        //$prefactura = Ap_Orden_Venta::where('id_ap_agrupado',$id)->first();   
        $prefactura = Ap_Orden_Venta::where('id_ap_agrupado',$id_ap)->where('concepto',$base)->first();
         
        if(is_null($prefactura)){
            //CREAR EN NUEVA TABLA
          $empresa      = $agrupado->empresa;  
          $mes_anio     = $agrupado->mes_plano;
          $id_tipo_seg  = $agrupado->id_tipo_seg;
          $seguro       = $agrupado->seguro;
          $cobertura    = $agrupado->cobertura;
          $concepto     = $base;

          $seguros = Seguro::find($seguro);
          $ruc_cliente= $seguros->ruc_cliente;
          $valor = 0;
          $valor_iva = 12;
          if($concepto == "B0"){
            $valor = $agrupado->base_0_pref;
            $valor_iva = 0;
          }
          if($concepto == 'B12'){
            $valor = $agrupado->base_12_pref;
            $valor_iva = $agrupado->valor_iva_cobrar;
          }
          

          $arr = [
            'empresa'         => $empresa,
            'mes_anio'        => $mes_anio,
            'id_tipo_seg'     => $id_tipo_seg,
            'id_ap_agrupado'  => $id_ap,
            'seguro'          => $seguro,
            'cobertura'       => $cobertura,
            'concepto'        => $concepto,
            'valor'           => $valor,
            'valor_iva'       => $valor_iva,
            'ruc'             => $ruc_cliente,

            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

         ];

         $id_ap_orden_venta = Ap_Orden_Venta::insertGetId($arr); 

        }  
      
        //$prefactura -> update($arr);
        return "ok";
        //return view('archivo_plano/contable/prefactura_ap_orden_venta',['agrupado' => $agrupado]);
    }

    public function detalle_prefactura(Request $request){

       
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $empresa          = $request->empresa;
        $mes_plano        = $request->mes_plano;
        $seguro           = $request->seguro;
        $id_tipo_seg      = $request->id_tipo_seg;
 
        //dd($request->all());
 
        $det_prefacturas  = Ap_Orden_Venta:: where('empresa',$request->empresa)-> where('seguro', $request->seguro)-> where('mes_anio', $request->mes_plano)-> whereNull('id_orden_venta')->get();

        //dd($det_prefacturas);
        return view('archivo_plano/contable/prefactura_ap_orden_venta', ['det_prefacturas'=> $det_prefacturas,'empresa'=>$empresa, 'mes_anio' => $mes_plano, 'seguro' => $seguro ]);

    }

    public function enviar_prefactura(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        
        DB::beginTransaction();
        try {
        $empresa    = $request->empresa;
        $seguro     = $request->seguro;
        $mes_plano  = $request->mes_plano;

       
        $seguros = Seguro::find($request->seguro);
        $ruc_cliente= $seguros->ruc_cliente;

        $descripcion= $seguros->descripcion;
        //dd($ruc_cliente,$descripcion);


        //dd($det_prefactura);
          
        $det_prefactura = Ap_Orden_Venta::where('ap_orden_venta.empresa',$request->empresa)-> where('ap_orden_venta.seguro', $request->seguro)-> where('ap_orden_venta.mes_anio', $request->mes_plano)
                     ->whereNull('ap_orden_venta.id_orden_venta')
                     ->select('ap_orden_venta.id as id_apven','ap_orden_venta.concepto','ap_orden_venta.valor','ap_orden_venta.valor_iva','ap_orden_venta.id_ap_agrupado', 'ap_orden_venta.mes_anio', 'ap_orden_venta.id_tipo_seg')
                     ->get();
    
        //dd($det_prefactura);            
        $acum_base_0   = 0;
        $acum_base_12 =0;
        $acum_iva=0;

        $vt_contador = 0;$vt_contador2 = 0;
        foreach($det_prefactura as $value){
            //$acum_base0   += $value->base_0;
            //$acum_base12  +=$value->base_iva;
            $valor_0  = 0;
            $valor_12 = 0;
            $valor_iva = 0;
            $ntramite = $value->agrupado->cod_proceso;
                        
            $arr_mes = ['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];
            $nmes = substr($value->mes_anio,0,2);
            $nmes = intval($nmes);
            $anio = substr($value->mes_anio,2,4);
            $tmes = $arr_mes[$nmes];
            $mes_anio = $tmes.' '.$anio;
            $txt_tipo_abv = $value->id_tipo_seg;
            $txt_tipo_abv = substr($txt_tipo_abv,0,3);

            if($value->concepto == 'B0'){
                if($vt_contador == 0){
                    $arr_detalle_fact = [];
                    array_push($arr_detalle_fact, 'SERVICIOS MEDICOS');
                    array_push($arr_detalle_fact, 'POR ATENCION BAJO CONVENIO');
                    array_push($arr_detalle_fact, 'AMBULATORIOS '.$value->id_tipo_seg);
                    array_push($arr_detalle_fact, 'TRAMITES MES DE  '.$mes_anio.' '.$txt_tipo_abv.' '.$ntramite);

                    $vt_contador ++;

                }else{

                    array_push($arr_detalle_fact, 'AMBULATORIOS '.$value->id_tipo_seg);
                    array_push($arr_detalle_fact, 'TRAMITES MES DE  '.$mes_anio.' '.$txt_tipo_abv.' '.$ntramite);
                
                }
            } 
            if($value->concepto == 'B12'){
                if($vt_contador2 == 0){
                    $arr_detalle_fact2 = [];
                    array_push($arr_detalle_fact2, 'SERVICIOS MEDICOS');
                    array_push($arr_detalle_fact2, 'POR ATENCION BAJO CONVENIO');
                    array_push($arr_detalle_fact2, 'AMBULATORIOS '.$value->id_tipo_seg);
                    array_push($arr_detalle_fact2, 'TRAMITES MES DE  '.$mes_anio.' '.$txt_tipo_abv.' '.$ntramite);

                    $vt_contador2 ++;

                }else{

                    array_push($arr_detalle_fact2, 'AMBULATORIOS '.$value->id_tipo_seg);
                    array_push($arr_detalle_fact2, 'TRAMITES MES DE  '.$mes_anio.' '.$txt_tipo_abv.' '.$ntramite);
                
                }
            }    
            if($value->concepto == 'B0'){
                
                $valor_0 = $value->valor;

            }
            if($value->concepto == 'B12'){
                
                $valor_12 = $value->valor;
                $valor_iva = $value->valor_iva; 
            }
            $acum_base_0   += $valor_0;
            $acum_base_12   += $valor_12;
            $acum_iva     += $valor_iva;

        }
        
        if($det_prefactura->count()>0){
            $subtotal_total = $acum_base_0 + $acum_base_12;
            $iva_total = $acum_iva;
            //dd($iva_total,$subtotal_total);
            $arr= [

               'sucursal'       => 0,
               'punto_emision'  => 0,
               'numero'         => 0,

               'id_cliente'       => $ruc_cliente,

               'nombre_cliente'   => $descripcion,
               'id_empresa'       => $empresa,
               'fecha'            => date('Y-m-d'),
         
               'divisas'           => 1,
               'subtotal_0'        => $acum_base_0,
               'subtotal_12'       => $acum_base_12,
               'subtotal_total'    => $subtotal_total,
               
               'iva_total'         => $iva_total,
               'total_final'       => ($subtotal_total)+($iva_total),
            
               'descuento'         => 0,
               'base_imponible'    => 0,
               'impuesto'          => 0,
               'estado'            => 1,
                       
               'id_usuariocrea'    => $idusuario,
               'id_usuariomod'     => $idusuario,
               'ip_creacion'       => $ip_cliente,
               'ip_modificacion'   => $ip_cliente,

            ];
             
           $id_orden_venta = Ct_Ven_Orden::insertGetId($arr); 

            if($acum_base_0 > 0){
               
                
                Ct_Ven_Orden_Detalle::create([
                    "id_ct_ven_orden"   => $id_orden_venta,
                    "id_ct_productos"    => "BASE_0",
                    "nombre"            => "BASE 0",
                    "cantidad"          => "1",
                    "precio"            => $acum_base_0,
                    "descuento_porcentaje"=> 0,
                    "descuento"         => 0,
                    "estado"            => 1,
                    "copago"            => 0,
                    "check_iva"         => 0,
                    "detalle"           => json_encode($arr_detalle_fact),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
    
                    
                     ]);
            }
            if($acum_base_12 > 0){
                
                Ct_Ven_Orden_Detalle::create([
                    "id_ct_ven_orden"   => $id_orden_venta,
                    "id_ct_productos"    => "BASE_12",
                    "nombre"            => "BASE 12",
                    "cantidad"          => "1",
                    "precio"            => $acum_base_12,
                    "descuento_porcentaje"=> 0,
                    "descuento"         => 0,
                    "estado"            => 1,
                    "copago"            => 0,
                    "check_iva"         => 1,
                    "detalle"           => json_encode($arr_detalle_fact2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,


                ]);
            }           

            //dd($id_ap_orden_venta);
            //dd($arr);
            //dd($det_prefactura);


            foreach ($det_prefactura as $value) { 
                $ap_orden = Ap_Orden_Venta::find($value->id_apven);
                
                $arr= [
                    'id_orden_venta' => $id_orden_venta,
                ]; 
                //dd($arr, $value, $ap_orden);

                $ap_orden->update($arr);
            }
            DB::commit();
            return ["status"=>"success", "msj"=>"Guardado Correctamente"];  
        }
        DB::rollBack();
        return ["status"=>"error", "msj"=>"Sin Registro"];
        } catch (\Exception $e) {

            DB::rollBack();
            return ["status"=>"error", "msj"=> "Ocurrio un error", "exp"=>$e->getMessage(),'linea' => $e->getLine()];
        }
                      
    }

    public function prefactura_delete( $id ) {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
    
            $det_prefactura = Ap_Orden_Venta::find($id);  
    
            $det_prefactura->delete();
    
            return "ok";
    }



    public function subida_plantilla_msp(Request $request)
    {
        try {

            $textoSearch = $request->auditoria;
            $leerCsv = function ($campo, array $requeridas) use ($request) {

                $archivo = $request->file($campo);
               
                
                if (is_null($request->auditoria)) {
                    throw new \RuntimeException(
                        "El campo auditoria esta vacio."
                    );
                }

                if (!$archivo || !$archivo->isValid()) {
                    throw new \RuntimeException(
                        "El archivo {$campo} no es válido."
                    );
                }

                $recurso = fopen($archivo->getRealPath(), 'r');

                if ($recurso === false) {
                    throw new \RuntimeException(
                        "No se pudo abrir {$campo}."
                    );
                }

                try {

                    $columnas = fgetcsv($recurso, 0, ',');

                    if ($columnas === false) {
                        throw new \RuntimeException(
                            "El archivo {$campo} está vacío."
                        );
                    }
                    $columnas = array_map(function ($valor) {

                        return trim(
                            preg_replace(
                                '/^\xEF\xBB\xBF/',
                                '',
                                trim($valor)
                            )
                        );
                    }, $columnas);


                    if (
                        in_array('', $columnas, true) ||
                        count(array_unique($columnas)) !== count($columnas)
                    ) {
                        throw new \RuntimeException(
                            "El archivo {$campo} tiene encabezados vacíos o duplicados."
                        );
                    }
                    $faltantes = array_diff(
                        $requeridas,
                        $columnas
                    );

                    if ($faltantes) {

                        throw new \RuntimeException(
                            "Faltan columnas en {$campo}: " .
                                implode(', ', $faltantes)
                        );
                    }


                    $registros = [];
                    $numeroFila = 1;


                    while (
                        ($fila = fgetcsv($recurso, 0, ',')) !== false
                    ) {

                        $numeroFila++;


                        if (
                            count($fila) === 1 &&
                            trim((string) $fila[0]) === ''
                        ) {
                            continue;
                        }

                        if (
                            count($fila) !== count($columnas)
                        ) {

                            throw new \RuntimeException(
                                "Cantidad de columnas incorrecta en {$campo}, fila {$numeroFila}."
                            );
                        }


                        $registro = array_combine(
                            $columnas,
                            $fila
                        );
                        foreach ($requeridas as $columna) {

                            $registro[$columna] = trim(
                                (string) $registro[$columna]
                            );

                            if ($registro[$columna] === '') {

                                throw new \RuntimeException(
                                    "El campo {$columna} está vacío en {$campo}, fila {$numeroFila}."
                                );
                            }
                        }


                        $registros[] = $registro;
                    }


                    return $registros;
                } finally {

                    fclose($recurso);
                }
            };

            $normalizarRegistro = function (
                array $registro,
                array $camposDecimales = []
            ) {

                foreach ($registro as $campo => $valor) {

                    if (is_string($valor)) {

                        $valor = trim($valor);

                        if (
                            strtolower($valor) === 'null'
                        ) {
                            $registro[$campo] = null;
                        } else {
                            $registro[$campo] = $valor;
                        }
                    }
                }


                foreach ($camposDecimales as $campo) {

                    if (
                        !array_key_exists($campo, $registro)
                    ) {
                        continue;
                    }


                    if (
                        $registro[$campo] === null ||
                        $registro[$campo] === ''
                    ) {
                        continue;
                    }


                    $valor = trim(
                        (string) $registro[$campo]
                    );

                    $valor = str_replace(
                        ',',
                        '.',
                        $valor
                    );


                    if (!is_numeric($valor)) {

                        throw new \RuntimeException(
                            "El campo {$campo} tiene un valor numérico inválido: {$registro[$campo]}"
                        );
                    }


                    $registro[$campo] = $valor;
                }


                $camposFecha = [
                    'created_at',
                    'updated_at'
                ];


                foreach ($camposFecha as $campo) {

                    if (
                        !isset($registro[$campo]) ||
                        $registro[$campo] === ''
                    ) {
                        continue;
                    }


                    $fecha = \DateTime::createFromFormat(
                        'j/n/Y H:i:s',
                        $registro[$campo]
                    );


                    if ($fecha !== false) {

                        $registro[$campo] = $fecha->format(
                            'Y-m-d H:i:s'
                        );
                    }
                }


                return $registro;
            };


            $cabeceras = $leerCsv(
                'archivo_c',
                [
                    'codigo'
                ]
            );


            $detalles = $leerCsv(
                'archivo_d',
                [
                    'cod_plantilla',
                    'id_procedimiento'
                ]
            );


            $procedimientos = $leerCsv(
                'archivo_ap',
                [
                    'id'
                ]
            );


            if (!$cabeceras) {

                throw new \RuntimeException(
                    'El archivo de cabecera no contiene registros.'
                );
            }


            if (!$detalles) {

                throw new \RuntimeException(
                    'El archivo de detalles no contiene registros.'
                );
            }


            if (!$procedimientos) {

                throw new \RuntimeException(
                    'El archivo de procedimientos no contiene registros.'
                );
            }
            $codigosPlantilla = [];


            foreach ($cabeceras as $cabecera) {

                $codigo = trim(
                    $cabecera['codigo']
                );


                if (
                    isset($codigosPlantilla[$codigo])
                ) {

                    throw new \RuntimeException(
                        "Código de plantilla duplicado: {$codigo}"
                    );
                }


                $codigosPlantilla[$codigo] = true;
            }
            foreach ($detalles as $detalle) {

                $codigoPlantilla = trim(
                    $detalle['cod_plantilla']
                );


                if (
                    !isset(
                        $codigosPlantilla[$codigoPlantilla]
                    )
                ) {

                    throw new \RuntimeException(
                        "No existe la cabecera para la plantilla {$codigoPlantilla}"
                    );
                }
            }
            $procedimientosOriginales = [];


            foreach ($procedimientos as $procedimiento) {

                $idOriginal = trim(
                    $procedimiento['id']
                );


                if (
                    isset(
                        $procedimientosOriginales[$idOriginal]
                    )
                ) {

                    throw new \RuntimeException(
                        "ID de procedimiento duplicado en archivo_ap: {$idOriginal}"
                    );
                }


                $procedimientosOriginales[$idOriginal] = true;
            }
            foreach ($detalles as $detalle) {

                $idOriginal = trim(
                    $detalle['id_procedimiento']
                );


                if (
                    !isset(
                        $procedimientosOriginales[$idOriginal]
                    )
                ) {

                    throw new \RuntimeException(
                        "El procedimiento {$idOriginal} usado en archivo_d no existe en archivo_ap."
                    );
                }
            }

            DB::transaction(function () use (
                $cabeceras,
                $detalles,
                $procedimientos,
                $normalizarRegistro,
                $textoSearch
            ) {

                $codigosUsados = [];


                $codigosExistentes = DB::table(
                    'ap_procedimiento'
                )
                    ->whereNotNull('codigo')
                    ->pluck('codigo');


                foreach ($codigosExistentes as $codigo) {

                    $codigosUsados[(string) $codigo] = true;
                }


                $generarCodigo = function () use (
                    &$codigosUsados
                ) {

                    for (
                        $intento = 0;
                        $intento < 1000;
                        $intento++
                    ) {

                        $codigo = (string) random_int(
                            100000,
                            999999
                        );


                        if (
                            isset(
                                $codigosUsados[$codigo]
                            )
                        ) {
                            continue;
                        }


                        $existe = DB::table(
                            'ap_procedimiento'
                        )
                            ->where(
                                'codigo',
                                $codigo
                            )
                            ->exists();


                        if (!$existe) {

                            $codigosUsados[$codigo] = true;

                            return $codigo;
                        }
                    }


                    throw new \RuntimeException(
                        'No se pudo generar un código único de 6 dígitos.'
                    );
                };


                $procedimientosMap = [];


                foreach (
                    $procedimientos as $datosProcedimiento
                ) {

                    $idOriginal = trim(
                        $datosProcedimiento['id']
                    );


                    if (
                        isset(
                            $procedimientosMap[$idOriginal]
                        )
                    ) {

                        throw new \RuntimeException(
                            "ID de procedimiento duplicado: {$idOriginal}"
                        );
                    }
                    unset(
                        $datosProcedimiento['id']
                    );

                    $datosProcedimiento =
                        $normalizarRegistro(
                            $datosProcedimiento,
                            [
                                'cantidad',
                                'valor',
                                'porcentaje_clasificado',
                                'porcentaje10',
                                'iva',
                                'valor_crm',
                                'valor_anterior',
                                'iva_nuevo'
                            ]
                        );
                    $datosProcedimiento['codigo'] =
                        $generarCodigo();

                    $datosProcedimiento['ip_creacion'] =
                        $textoSearch;

                    $datosProcedimiento['ip_modificacion'] =
                        $textoSearch;
                    $nuevoId = DB::table(
                        'ap_procedimiento'
                    )
                        ->insertGetId(
                            $datosProcedimiento
                        );

                    $procedimientosMap[$idOriginal] =
                        $nuevoId;
                }



                foreach (
                    $cabeceras as $datosCabecera
                ) {

                    unset(
                        $datosCabecera['id']
                    );


                    $datosCabecera =
                        $normalizarRegistro(
                            $datosCabecera,
                            []
                        );


                    $datosCabecera['ip_creacion'] =
                        $textoSearch;

                    $datosCabecera['ip_modificacion'] =
                        $textoSearch;


                    DB::table(
                        'ap_plantilla'
                    )
                        ->insert(
                            $datosCabecera
                        );
                }

                foreach (
                    $detalles as $datosDetalle
                ) {

                    $idOriginal = trim(
                        $datosDetalle['id_procedimiento']
                    );


                    if (
                        !isset(
                            $procedimientosMap[$idOriginal]
                        )
                    ) {

                        throw new \RuntimeException(
                            "No se encontró el procedimiento {$idOriginal} en archivo_ap."
                        );
                    }

                    $nuevoIdProcedimiento =
                        $procedimientosMap[$idOriginal];

                    unset(
                        $datosDetalle['id']
                    );

                    $datosDetalle =
                        $normalizarRegistro(
                            $datosDetalle,
                            [
                                'cantidad',
                                'orden',
                                'porcentaje_modificador'
                            ]
                        );

                    $datosDetalle['id_procedimiento'] =
                        $nuevoIdProcedimiento;

                    $datosDetalle['procedimiento'] =
                        $nuevoIdProcedimiento;

                    $datosDetalle['ip_creacion'] =
                        $textoSearch;

                    $datosDetalle['ip_modificacion'] =
                        $textoSearch;


                    DB::table(
                        'ap_plantilla_items'
                    )
                        ->insert(
                            $datosDetalle
                        );
                }
            });


            return response()->json([
                'status'  => true,
                'mensaje' => 'Procesado correctamente'
            ]);
        } catch (\Throwable $error) {

            return response()->json([
                'status'  => false,
                'mensaje' => $error->getMessage(),
                'line'    => $error->getLine()
            ]);
        }
    }
}
