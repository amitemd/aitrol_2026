<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Seguro;
use Sis_medico\Tipo_Seguro;
use Sis_medico\Empresa;
use Sis_medico\Codigo_Dependencia;
use Sis_medico\Codigo_Derivacion;
use Sis_medico\Agenda;
use Sis_medico\Historiaclinica;
use Sis_medico\Paciente;
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Archivo_Plano_Detalle;
use Sis_medico\Ap_Tipo_Examen;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Convenio;
use Sis_medico\Examen_Orden;
use Sis_medico\ApProcedimiento;
use Sis_medico\ApPlantilla;
use Sis_medico\Ap_Agrupado;
use Sis_medico\Ap_Tipo_Seg;
use Sis_medico\ApPlantillaItem;
use Sis_medico\ApProcedimientoNivel;
use Excel;
use Exception;
use PHPExcel_Style_NumberFormat;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Examen_Nivel;
use Sis_medico\Ap_Interconsulta_Espe;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Ap_Procedimiento;
use Sis_medico\Planilla;
use Sis_medico\Planilla_Cabecera;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\Medicina_tarifario;
use Sis_medico\hc_receta;
use Sis_medico\LogAuditoriaPublica;
use Carbon\Carbon;

class ApNuevoPlanillaController extends Controller
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
        if (in_array($rolUsuario, array(1, 4, 5, 11, 22)) == false) {
            return true;
        }
    }

    public function cargarInsumosPlanillaModal($cabecera)
    {
        $a_plano = Archivo_Plano_Cabecera::find($cabecera);
        $id_hc_procedimiento = null;
        if (!is_null($a_plano)) {
            $id_hc_procedimiento = $a_plano->id_hc_procedimimentos;
        }

        $fecha_hasta = date('Y/m/d');
        $fecha = strtotime('-3 month', strtotime($fecha_hasta));
        $fecha = date('Y/m/d', $fecha);


        /*$insumos = Planilla_Cabecera::join('agenda as a','a.id','planilla_cabecera.id_agenda')
        ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
        ->join('hc_evolucion as hc_e', 'hc_e.hcid', 'h.hcid')
        ->join('hc_procedimientos as hp','hp.id','planilla_cabecera.id_hc_procedimiento')
        ->join('planilla_detalle as pd','pd.id_planilla_cabecera','planilla_cabecera.id')
        ->where('pd.check',1)->where('pd.estado',1)->whereIn('ap.tipo',['I','IV','IVC'])
        ->join('producto as p','p.codigo','pd.codigo')
        ->leftjoin('seguros as s', 's.id', 'hp.id_seguro')
        ->leftjoin('empresa as emp', 'emp.id', 'hp.id_empresa')
        ->leftjoin('users as u', 'u.id', 'hp.id_doctor_examinador')
        ->leftjoin('especialidad as espe', 'espe.id', 'a.espid')
        ->leftjoin('ct_productos_insumos as cpi','cpi.id_insumo','p.id')
        ->leftjoin('ct_productos as cp','cp.id','cpi.id_producto')
        ->leftjoin('ap_procedimiento as ap','ap.id_producto','cp.id')->where('planilla_cabecera.id_hc_procedimiento',$id_hc_procedimiento)
        ->select(DB::raw('SUM(pd.cantidad) as cant'),'a.fechaini', 'hp.id_seguro', 'hp.id_empresa', 'hp.id_doctor_examinador', 's.nombre as nombre_seguro', 'emp.nombre_corto', 
        'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'h.hcid', 'h.id_seguro as seguro_historia', 'a.id_empresa as empresa_agenda', 
        'a.id as id_agenda', 'espe.nombre as nombre_espe','ap.tipo as tipo_procedimiento','ap.descripcion as descripcion_procedimiento',
        'ap.valor as valor_procedimiento','ap.codigo as codigo_procedimiento','ap.id as id_procedimiento')
        ->groupBy('pd.serie')
        ->get();*/

        $insumos = Planilla::where('estado', '1')
            ->where('aprobado', '1')
            ->where('id_hc_procedimiento', $id_hc_procedimiento)
            ->whereBetween('fecha', [$fecha, $fecha_hasta])
            ->get();
        //->toSql();
        return view('archivo_plano/planilla/planilla_insumos', ['insumos' => $insumos, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'a_plano' => $a_plano]);
    }

    public function cargarInsumosPlanillaBuscar($cabecera, Request $request)
    {
        $a_plano = Archivo_Plano_Cabecera::find($cabecera);
        $id_hc_procedimiento = null;
        if (!is_null($a_plano)) {
            $id_hc_procedimiento = $a_plano->id_hc_procedimimentos;
        }

        $fecha_hasta = $request->fecha_hasta;
        $fecha = $request->fecha;

        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y/m/d');
        }

        if ($fecha == null) {
            $fecha = date('Y/m/d');
        }


        $insumos = Planilla::where('estado', '1')
            ->where('aprobado', '1')
            ->where('id_hc_procedimiento', $id_hc_procedimiento)
            ->whereBetween('fecha', [$fecha, $fecha_hasta])
            ->get();

        return view('archivo_plano/planilla/planilla_insumos_div', ['insumos' => $insumos, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'a_plano' => $a_plano]);
    }

    public function cargarMedicinasPlanilla($cabecera, Request $request)
    {
        $a_plano = Archivo_Plano_Cabecera::find($cabecera);
        $id_hc_procedimiento = null;
        if (!is_null($a_plano)) {
            $id_hc_procedimiento = $a_plano->id_hc_procedimimentos;
        }

        $fecha_hasta = $request->fecha_hasta;
        $fecha = $request->fecha;

        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y/m/d');
        }

        if ($fecha == null) {
            $fecha = strtotime('-3 month', strtotime($fecha_hasta));
            $fecha = date('Y/m/d', $fecha);
        }

        $medicinas = Historiaclinica::join('hc_receta as r', 'r.id_hc', 'historiaclinica.hcid')
            ->join('seguros as s', 's.id', 'historiaclinica.id_seguro')
            ->where('historiaclinica.id_paciente', $a_plano->id_paciente)
            ->where('s.tipo', 0)
            ->whereBetween('r.created_at', [$fecha, $fecha_hasta])
            ->select('r.*')
            ->get();


        /*$medicinas = Planilla_Cabecera::join('agenda as a','a.id','planilla_cabecera.id_agenda')
        ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
        ->join('hc_evolucion as hc_e', 'hc_e.hcid', 'h.hcid')
        ->join('hc_procedimientos as hp','hp.id','planilla_cabecera.id_hc_procedimiento')
        ->join('planilla_detalle as pd','pd.id_planilla_cabecera','planilla_cabecera.id')
        ->where('pd.check',1)->where('pd.estado',1)->whereIn('ap.tipo',['M'])
        ->join('producto as p','p.codigo','pd.codigo')
        ->leftjoin('seguros as s', 's.id', 'hp.id_seguro')
        ->leftjoin('empresa as emp', 'emp.id', 'hp.id_empresa')
        ->leftjoin('users as u', 'u.id', 'hp.id_doctor_examinador')
        ->leftjoin('especialidad as espe', 'espe.id', 'a.espid')
        ->leftjoin('ct_productos_insumos as cpi','cpi.id_insumo','p.id')
        ->leftjoin('ct_productos as cp','cp.id','cpi.id_producto')
        ->leftjoin('ap_procedimiento as ap','ap.id_producto','cp.id')->where('planilla_cabecera.id_hc_procedimiento',$id_hc_procedimiento)
        ->select(DB::raw('SUM(pd.cantidad) as cant'),'a.fechaini', 'hp.id_seguro', 'hp.id_empresa', 'hp.id_doctor_examinador', 's.nombre as nombre_seguro', 'emp.nombre_corto', 
        'u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'h.hcid', 'h.id_seguro as seguro_historia', 'a.id_empresa as empresa_agenda', 
        'a.id as id_agenda', 'espe.nombre as nombre_espe','ap.tipo as tipo_procedimiento','ap.descripcion as descripcion_procedimiento',
        'ap.valor as valor_procedimiento','ap.codigo as codigo_procedimiento','ap.id as id_procedimiento')
        ->groupBy('pd.serie')
        ->get();*/
        //->toSql();
        //dd($medicinas);
        return view('archivo_plano/planilla/planilla_medicinas', ['medicinas' => $medicinas, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'a_plano' => $a_plano]);
    }

    public function cargarMedicinasPlanilla_div($cabecera, Request $request)
    {
        $a_plano = Archivo_Plano_Cabecera::find($cabecera);
        $id_hc_procedimiento = null;
        if (!is_null($a_plano)) {
            $id_hc_procedimiento = $a_plano->id_hc_procedimimentos;
        }

        $fecha_hasta = $request->fecha_hasta;
        $fecha = $request->fecha;

        if ($fecha_hasta == null) {
            $fecha_hasta = date('Y/m/d');
        }

        if ($fecha == null) {
            $fecha = strtotime('-3 month', strtotime($fecha_hasta));
            $fecha = date('Y/m/d', $fecha);
        }

        $medicinas = Historiaclinica::join('hc_receta as r', 'r.id_hc', 'historiaclinica.hcid')
            ->join('seguros as s', 's.id', 'historiaclinica.id_seguro')
            ->where('historiaclinica.id_paciente', $a_plano->id_paciente)
            ->where('s.tipo', 0)
            ->whereBetween('r.created_at', [$fecha, $fecha_hasta])
            ->select('r.*')
            ->get();


        return view('archivo_plano/planilla/planilla_medicinas_div', ['medicinas' => $medicinas, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'a_plano' => $a_plano]);
    }

    public function agregarInsumo(Request $request)
    {
        try {
            $cabecera = $request->cabecera;
            $id_planilla = $request->id_planilla;

            $rolUsuario = Auth::user()->id_tipo_usuario;

            $id_usuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $a_plano = Archivo_Plano_Cabecera::find($cabecera);

            $fecha = date("Y-m-d", strtotime($a_plano->fecha_ing));

            $planilla = Planilla::find($id_planilla);
            //dd($planilla);

            if (!is_null($a_plano) and !is_null($planilla)) {
                $cont = 0;
                $detalles = $planilla->detalles; //dd($detalles);
                foreach ($detalles as $detalle) { //dd($detalle->producto);

                    if ($detalle->estado == '1' and $detalle->check == '1') {
                        $cont++;

                        // La clasificación válida para archivo plano está en
                        // ap_tipo_examen (IMM). Algunos insumos MSP, por ejemplo
                        // los IDE, no tienen activa la marca inventario ap_insumo.
                        if (!is_null($detalle->producto)) {
                            if ($cont == 10) {
                                /* if($id_usuario == '0922290697'){
                                    dd($detalle,$detalle->producto);
                                }*/
                                //dd($detalle, $detalle->producto);
                            }

                            //precio pedido 
                            $valor_unitario = 0;
                            $cantidad = 0;
                            $id_producto = $detalle->producto->id;
                            $serie = $detalle->serie;
                            $bodega = $detalle->id_bodega;
                            $valor_unitario10p = 0;
                            $redo = '4';

                            $ins_pro = Ct_productos_insumos::where('id_insumo', $id_producto)->first(); //dd($ins_pro);
                            if (!is_null($ins_pro)) {
                                $ap_proced = ApProcedimiento::where('id_producto', $ins_pro->id_producto)->first();
                                if (!is_null($ap_proced)) {
                                    $es_insumo = Ap_Tipo_Examen::where('tipo', $ap_proced->tipo)
                                        ->where('tipo_ex', 'IMM')
                                        ->where('estado', '1')
                                        ->exists();
                                    if ($es_insumo) {

                                        $ya_importado = Archivo_Plano_Detalle::where('id_ap_cabecera', $a_plano->id)
                                            ->where('id_producto', $ins_pro->id_producto)
                                            ->where('serie', $serie)
                                            ->where('estado', '1')
                                            ->exists();
                                        if ($ya_importado) {
                                            continue;
                                        }

                                        //$detalle_mos = InvDetMovimientos::where('serie', $serie)->where('id_producto',$id_producto)->where('motivo','INGRESO PEDIDO')->first();
                                        $detalle_mos = InvDetMovimientos::join('inv_cab_movimientos as icm', 'icm.id', 'inv_det_movimientos.id_inv_cab_movimientos')
                                            ->join('inv_documentos_bodegas as idb', 'idb.id', 'icm.id_documento_bodega')
                                            ->where('inv_det_movimientos.serie', $serie)
                                            ->where('inv_det_movimientos.id_producto', $id_producto)
                                            ->where('idb.id_inv_tipo_movimiento', 1)
                                            ->select('inv_det_movimientos.*')
                                            ->first();

                                        if (!is_null($detalle_mos)) {
                                            $valor_unitario = $detalle_mos->valor_unitario;
                                            //dd($valor_unitario);
                                            ////// PARA CRM SI TIENE EL CAMPO VALOR_CRM AGARRA DE AHY CASO CONTRARIO INCREMENTA 10%
                                            if ($a_plano->id_empresa == '1307189140001') {
                                                if ($ap_proced->valor_crm != null || $ap_proced->valor_crm != 0) {
                                                    $valor_unitario = $ap_proced->valor_crm / (1 + $ap_proced->porcentaje10);
                                                } else {
                                                    $valor_unitario = $detalle_mos->valor_unitario * (1 + $ap_proced->porcentaje10);
                                                }
                                            }
                                            //dd($a_plano,$valor_unitario);

                                            ////// 
                                            $valor_unitario10p = $valor_unitario * (1 + $ap_proced->porcentaje10);
                                            $cantidad = $detalle->cantidad;
                                            $vinsumo = $cantidad * $valor_unitario10p;

                                            //$valor_unitario = $vinsumo / 1.10;
                                            // $valor_unitario = $valor_unitario10p / (1 + $ap_proced->porcentaje10);
                                            $valor_unitario = round($valor_unitario, 4);
                                            $subtotal = $valor_unitario * $cantidad;

                                            $p_iva = $ap_proced->iva;
                                            if ($fecha >= '2024-04-01') {

                                                $p_iva = $ap_proced->iva_nuevo;
                                            }

                                            $iva_unitario = $valor_unitario * $p_iva;
                                            $p10_unitario = $valor_unitario * $ap_proced->porcentaje10;

                                            //$iva_unitario = $this->redondea_menos_4_dec($iva_unitario);
                                            //$p10_unitario = $this->redondea_menos_4_dec($p10_unitario);

                                            if ($a_plano->id_seguro == '5') {
                                                $iva_unitario = round($iva_unitario, 4);
                                                $p10_unitario = round($p10_unitario, 4);
                                            } else {
                                                $iva_unitario = $this->redondea_menos_4_dec($iva_unitario);
                                                $p10_unitario = $this->redondea_menos_4_dec($p10_unitario);
                                            }



                                            //$iva = $subtotal * $ap_proced->iva; 
                                            $iva = $iva_unitario * $cantidad;

                                            //$porcentaje10 = round($subtotal,2,PHP_ROUND_HALF_UP) * $ap_proced->porcentaje10;
                                            $porcentaje10 = $p10_unitario * $cantidad;

                                            $total = $subtotal + $iva + $porcentaje10;

                                            //$a_clasificador = $this->obtener_clasificador($ap_proced->tipo);

                                            //$clasificador = $a_clasificador['clasificador'];

                                            $porcent_clasif = null;$clasificador = null;
                                            $val_clasif = 0;
                                            if ($a_plano->id_seguro == '5') {
                                                $redo = '2';
                                                //$val_unit = round($val_unit,2);
                                                //$iva_unitario = round($iva_unitario,2);
                                                //$p10_unitario = round($p10_unitario,2);
                                                $total = round($valor_unitario + $iva_unitario + $p10_unitario, 2);
                                                $total = $total * $cantidad;
                                                $porcent_clasif = Ap_Tipo_Examen::where('tipo', $ap_proced->tipo)->where('estado', '1')->first();
                                                if (!is_null($porcent_clasif)) {
                                                    $val_clasif = $subtotal * $porcent_clasif->porcentaje_modif_msp;
                                                    $clasificador = $porcent_clasif->clasificado;
                                                }
                                            }
                                            //$total_sol = $total;
                                            $total_sol = $total + $val_clasif;


                                            $arr_procedimiento = [
                                                'id_ap_cabecera' => $a_plano->id,
                                                'fecha' => $a_plano->fecha_ing,
                                                'id_nivel' => $a_plano->id_nivel,
                                                'tipo' => $ap_proced->tipo,
                                                'codigo' => $ap_proced->codigo,
                                                'descripcion' => $ap_proced->descripcion,
                                                'cantidad' => $cantidad,
                                                'valor' => round(($valor_unitario10p), $redo),
                                                'subtotal' => round(($subtotal), $redo),
                                                'valor_unitario' => $valor_unitario,
                                                'porcent_10' => $ap_proced->porcentaje10,
                                                'porcentaje10' => $porcentaje10,
                                                'porcentaje_iva' => $p_iva,
                                                'iva' => $iva,
                                                'total' => round(($total), $redo),
                                                'porcentaje_honorario' => 100,
                                                'estado' => 1,
                                                'id_usuariocrea' => $id_usuario,
                                                'id_usuariomod' => $id_usuario,
                                                'ip_creacion' => $ip_cliente,
                                                'ip_modificacion' => $ip_cliente,
                                                'serie' => $serie,
                                                'id_producto' => $ins_pro->id_producto,
                                                'id_pedido' => $detalle_mos->id_pedido,
                                                'iva_unitario' => $iva_unitario,
                                                'p10_unitario' => $p10_unitario,
                                                //msp
                                                'clasif_porcentaje_msp' => '100',
                                                'valor_porcent_clasifi'  => round(($val_clasif), $redo),
                                                'total_solicitado_usd'  => round(($total_sol), $redo),
                                                'clasificador' => $clasificador,
                                                'iva_unitario'           => $iva_unitario,
                                                'p10_unitario'           => $p10_unitario,

                                            ];
                                            //dd($arr_procedimiento);
                                            Archivo_Plano_Detalle::create($arr_procedimiento);
                                        }
                                        LogAuditoriaPublica::guardarLog_agrega_boton_insumo($cabecera, 'INGRESO INSUMOS');
                                    }
                                }
                            }
                        }
                        if ($cont == 13) {
                            //dd("xxx",$detalle, $detalle->producto);
                        }
                    }
                    
                }
                return ['status' => 'success', 'msj' => 'success'];
            } else {
                return ['status' => 'error', 'msj' => 'Cabecera archivo plano no encontrada'];
            }
        } catch (Exception $ex) {
            return ['status' => 'error', 'msj' => $ex->getMessage(), 'linea' => $ex->getLine()];
        }
    }

    public function agregarMedicina(Request $request)
    {

        //dd($request); 
        try {
            $cabecera = $request->cabecera;
            $id_receta = $request->id_receta;

            $id_usuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $a_plano = Archivo_Plano_Cabecera::find($cabecera);
            if (!is_null($a_plano)) {

                $receta = hc_receta::find($id_receta);
                $detalles = $receta->detalles;

                foreach ($detalles as $detalle) {
                    $medicina_tarifario = Medicina_tarifario::where('id_medicina', $detalle->id_medicina)->where('estado', 1)->first();
                    if (!is_null($medicina_tarifario)) {
                        $ap_procedimiento = Ap_Procedimiento::find($medicina_tarifario->id_ap_procedimiento);

                        if (!is_null($ap_procedimiento)) {

                            $subtotal = $ap_procedimiento->valor * $detalle->cantidad;
                            $iva = 0;
                            if ($ap_procedimiento->iva == 1) {
                                $iva = $subtotal * 0.12;
                            }
                            $total = $subtotal + $iva;
                            $arr_procedimiento = [
                                'id_ap_cabecera' => $a_plano->id,
                                'fecha' => $a_plano->fecha_ing,
                                'tipo' => $ap_procedimiento->tipo,
                                'codigo' => $ap_procedimiento->codigo,
                                'descripcion' => $ap_procedimiento->descripcion,
                                'cantidad' => $detalle->cantidad,
                                'valor' => $ap_procedimiento->valor,
                                'subtotal' => $subtotal,
                                'valor_unitario' => $ap_procedimiento->valor,
                                'iva' => 0,
                                'total' => $total,
                                'estado' => 1,
                                'id_usuariocrea' => $id_usuario,
                                'id_usuariomod' => $id_usuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_nivel' => $a_plano->id_nivel,
                                'porcentaje_honorario' => '100',
                            ];
                            Archivo_Plano_Detalle::create($arr_procedimiento);
                        }
                        LogAuditoriaPublica::guardarLog_agrega_boton_medicina($cabecera, 'INGRESO MEDICINA');
                    }
                }
            } else {
                return ['status' => 'error', 'msj' => 'Cabecera archivo plano no encontrada'];
            }

            return ['status' => 'success', 'msj' => 'Ejecutado correctamente'];
        } catch (Exception $ex) {
            return ['status' => 'error', 'msj' => $ex->getMessage(), 'linea' => $ex->getLine()];
        }
    }

    public function add_all_insumos($cabecera)
    {
        try {
            DB::beginTransaction();
            $id_usuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $a_plano = Archivo_Plano_Cabecera::find($cabecera);
            $id_hc_procedimiento = null;
            $error = "";
            if (!is_null($a_plano)) {
                $id_hc_procedimiento = $a_plano->id_hc_procedimimentos;
            }
            $insumos = Planilla_Cabecera::join('agenda as a', 'a.id', 'planilla_cabecera.id_agenda')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->join('hc_evolucion as hc_e', 'hc_e.hcid', 'h.hcid')
                ->join('hc_procedimientos as hp', 'hp.id', 'planilla_cabecera.id_hc_procedimiento')
                ->join('planilla_detalle as pd', 'pd.id_planilla_cabecera', 'planilla_cabecera.id')
                ->where('pd.check', 1)->where('pd.estado', 1)->whereIn('ap.tipo', ['I', 'IV', 'IVC'])
                ->join('producto as p', 'p.codigo', 'pd.codigo')
                ->leftjoin('seguros as s', 's.id', 'hp.id_seguro')
                ->leftjoin('empresa as emp', 'emp.id', 'hp.id_empresa')
                ->leftjoin('users as u', 'u.id', 'hp.id_doctor_examinador')
                ->leftjoin('especialidad as espe', 'espe.id', 'a.espid')
                ->leftjoin('ct_productos_insumos as cpi', 'cpi.id_insumo', 'p.id')
                ->leftjoin('ct_productos as cp', 'cp.id', 'cpi.id_producto')
                ->leftjoin('ap_procedimiento as ap', 'ap.id_producto', 'cp.id')->where('planilla_cabecera.id_hc_procedimiento', $id_hc_procedimiento)
                ->select(
                    DB::raw('SUM(pd.cantidad) as cant'),
                    'a.fechaini',
                    'hp.id_seguro',
                    'hp.id_empresa',
                    'hp.id_doctor_examinador',
                    's.nombre as nombre_seguro',
                    'emp.nombre_corto',
                    'u.nombre1',
                    'u.nombre2',
                    'u.apellido1',
                    'u.apellido2',
                    'h.hcid',
                    'h.id_seguro as seguro_historia',
                    'a.id_empresa as empresa_agenda',
                    'a.id as id_agenda',
                    'espe.nombre as nombre_espe',
                    'ap.tipo as tipo_procedimiento',
                    'ap.descripcion as descripcion_procedimiento',
                    'ap.valor as valor_procedimiento',
                    'ap.codigo as codigo_procedimiento',
                    'ap.id as id_procedimiento'
                )
                ->groupBy('pd.serie')
                ->get();
            foreach ($insumos as $value) {
                if (!is_null($a_plano)) {
                    $ap_procedimiento = Ap_Procedimiento::find($value->id_procedimiento);
                    if (!is_null($ap_procedimiento)) {
                        $valor_unitario = $ap_procedimiento->valor / 1.10;
                        $subtotal = $valor_unitario * $value->cant;
                        $iva = $subtotal * $ap_procedimiento->iva;
                        $porcentaje10 = round($subtotal, 2, PHP_ROUND_HALF_UP) * $ap_procedimiento->porcentaje10;
                        $total = $subtotal + $iva + $porcentaje10;
                        $arr_procedimiento = [
                            'id_ap_cabecera' => $a_plano->id,
                            'fecha' => $a_plano->fecha_ing,
                            'tipo' => $ap_procedimiento->tipo,
                            'codigo' => $ap_procedimiento->codigo,
                            'descripcion' => $ap_procedimiento->descripcion,
                            'cantidad' => $value->cant,
                            'valor' => $ap_procedimiento->valor,
                            'subtotal' => round($subtotal, 2, PHP_ROUND_HALF_UP),
                            'valor_unitario' => round($valor_unitario, 2, PHP_ROUND_HALF_UP),
                            'porcent_10' => $ap_procedimiento->porcentaje10,
                            'porcentaje10' => $porcentaje10,
                            'porcentaje_iva' => $ap_procedimiento->iva,
                            'iva' => $iva,
                            'total' => $total,
                            'porcentaje_honorario' => 100,
                            'estado' => 1,
                            'id_usuariocrea' => $id_usuario,
                            'id_usuariomod' => $id_usuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ];
                        Archivo_Plano_Detalle::create($arr_procedimiento);
                    } else {
                        $error = $error . 'Procedimiento no encontrado: ' . $value->id_procedimiento . '<br>';
                    }
                } else {
                    return ['status' => 'error', 'msj' => 'Cabecera archivo plano no encontrada'];
                }
            }
            if ($error != "") {
                DB::rollback();
                return ['status' => 'error', 'msj' => $error];
            } else {
                DB::commit();
                return ['status' => 'success', 'msj' => 'Ejecutado correctamente'];
            }
        } catch (Exception $ex) {
            DB::rollback();
            return ['status' => 'error', 'msj' => $ex->getMessage(), 'linea' => $ex->getLine()];
        }
    }

    public function agregaMasivoInsumosLaboratorio($cabecera)
    {
        $archivo_plano = Archivo_Plano_Cabecera::find($cabecera);
        if (is_null($archivo_plano)) {
            return response()->json(['estado' => 'error', 'mensaje' => 'Planilla no encontrada.'], 404);
        }
        if ((int) $archivo_plano->cierre === 1) {
            return response()->json(['estado' => 'error', 'mensaje' => 'La liquidación está cerrada.'], 422);
        }

        DB::beginTransaction();
        try {
            $antes = Archivo_Plano_Detalle::where('id_ap_cabecera', $archivo_plano->id)
                ->where('estado', '1')
                ->count();

            $fecha_limite = Carbon::parse($archivo_plano->fecha_ing)->endOfDay();
            $planilla = Planilla::where('id_hc_procedimiento', $archivo_plano->id_hc_procedimimentos)
                ->where('estado', '1')
                ->where('aprobado', '1')
                ->where('fecha', '<=', $fecha_limite)
                ->orderBy('fecha', 'desc')
                ->first();

            if (!is_null($planilla)) {
                $request = Request::create('', 'POST', [
                    'cabecera' => $archivo_plano->id,
                    'id_planilla' => $planilla->id,
                ]);
                $resultado_insumos = $this->agregarInsumo($request);
                if (is_array($resultado_insumos) && isset($resultado_insumos['status']) && $resultado_insumos['status'] === 'error') {
                    throw new Exception($resultado_insumos['msj']);
                }
            }

            $inicio_mes = Carbon::parse($archivo_plano->fecha_ing)->startOfMonth()->startOfDay();
            $fin_mes = Carbon::parse($archivo_plano->fecha_ing)->endOfMonth()->endOfDay();
            $orden = Examen_Orden::where('id_paciente', $archivo_plano->id_paciente)
                ->where('id_seguro', $archivo_plano->id_seguro)
                ->where('estado', '1')
                ->where('realizado', '1')
                ->whereBetween('fecha_orden', [$inicio_mes, $fin_mes])
                ->orderBy('fecha_orden', 'asc')
                ->first();

            if (!is_null($orden)) {
                $laboratorio_importado = Archivo_Plano_Detalle::where('id_ap_cabecera', $archivo_plano->id)
                    ->where('id_orden_labs', $orden->id)
                    ->where('estado', '1')
                    ->exists();
                if (!$laboratorio_importado) {
                    (new Ap_PlanillaController())->ingresa_ordenes_labs($orden->id, $archivo_plano->id);
                }
            }

            $despues = Archivo_Plano_Detalle::where('id_ap_cabecera', $archivo_plano->id)
                ->where('estado', '1')
                ->count();
            $agregados = max(0, $despues - $antes);

            DB::commit();
            return response()->json([
                'estado' => 'ok',
                'agregados' => $agregados,
                'mensaje' => $agregados > 0
                    ? 'Se agregaron '.$agregados.' registros.'
                    : 'No había registros nuevos de insumos o laboratorio para agregar.',
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json([
                'estado' => 'error',
                'mensaje' => $ex->getMessage(),
            ], 500);
        }
    }

    public function add_all_medicinas($cabecera)
    {
        try {
            DB::beginTransaction();
            $id_usuario = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $a_plano = Archivo_Plano_Cabecera::find($cabecera);
            $id_hc_procedimiento = null;
            $error = "";
            if (!is_null($a_plano)) {
                $id_hc_procedimiento = $a_plano->id_hc_procedimimentos;
            }
            $medicinas = Planilla_Cabecera::join('agenda as a', 'a.id', 'planilla_cabecera.id_agenda')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->join('hc_evolucion as hc_e', 'hc_e.hcid', 'h.hcid')
                ->join('hc_procedimientos as hp', 'hp.id', 'planilla_cabecera.id_hc_procedimiento')
                ->join('planilla_detalle as pd', 'pd.id_planilla_cabecera', 'planilla_cabecera.id')
                ->where('pd.check', 1)->where('pd.estado', 1)->whereIn('ap.tipo', ['M'])
                ->join('producto as p', 'p.codigo', 'pd.codigo')
                ->leftjoin('seguros as s', 's.id', 'hp.id_seguro')
                ->leftjoin('empresa as emp', 'emp.id', 'hp.id_empresa')
                ->leftjoin('users as u', 'u.id', 'hp.id_doctor_examinador')
                ->leftjoin('especialidad as espe', 'espe.id', 'a.espid')
                ->leftjoin('ct_productos_insumos as cpi', 'cpi.id_insumo', 'p.id')
                ->leftjoin('ct_productos as cp', 'cp.id', 'cpi.id_producto')
                ->leftjoin('ap_procedimiento as ap', 'ap.id_producto', 'cp.id')->where('planilla_cabecera.id_hc_procedimiento', $id_hc_procedimiento)
                ->select(
                    DB::raw('SUM(pd.cantidad) as cant'),
                    'a.fechaini',
                    'hp.id_seguro',
                    'hp.id_empresa',
                    'hp.id_doctor_examinador',
                    's.nombre as nombre_seguro',
                    'emp.nombre_corto',
                    'u.nombre1',
                    'u.nombre2',
                    'u.apellido1',
                    'u.apellido2',
                    'h.hcid',
                    'h.id_seguro as seguro_historia',
                    'a.id_empresa as empresa_agenda',
                    'a.id as id_agenda',
                    'espe.nombre as nombre_espe',
                    'ap.tipo as tipo_procedimiento',
                    'ap.descripcion as descripcion_procedimiento',
                    'ap.valor as valor_procedimiento',
                    'ap.codigo as codigo_procedimiento',
                    'ap.id as id_procedimiento'
                )
                ->groupBy('pd.serie')
                ->get();
            foreach ($medicinas as $value) {
                if (!is_null($a_plano)) {
                    $ap_procedimiento = Ap_Procedimiento::find($value->id_procedimiento);
                    if (!is_null($ap_procedimiento)) {
                        $subtotal = $ap_procedimiento->valor * $value->cant;
                        $iva = 0;
                        if ($ap_procedimiento->iva == 1) {
                            $iva = $subtotal * 0.12;
                        }
                        $total = $subtotal + $iva;
                        $arr_procedimiento = [
                            'id_ap_cabecera' => $a_plano->id,
                            'fecha' => $a_plano->fecha_ing,
                            'tipo' => $ap_procedimiento->tipo,
                            'codigo' => $ap_procedimiento->codigo,
                            'descripcion' => $ap_procedimiento->descripcion,
                            'cantidad' => $value->cant,
                            'valor' => $ap_procedimiento->valor,
                            'subtotal' => $subtotal,
                            'valor_unitario' => $ap_procedimiento->valor,
                            'iva' => 0,
                            'total' => $total,
                            'estado' => 1,
                            'id_usuariocrea' => $id_usuario,
                            'id_usuariomod' => $id_usuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ];
                        Archivo_Plano_Detalle::create($arr_procedimiento);
                    } else {
                        $error = $error . 'Procedimiento no encontrado: ' . $value->id_procedimiento . '<br>';
                    }
                } else {
                    return ['status' => 'error', 'msj' => 'Cabecera archivo plano no encontrada'];
                }
            }

            if ($error != "") {
                DB::rollback();
                return ['status' => 'error', 'msj' => $error];
            } else {
                DB::commit();
                return ['status' => 'success', 'msj' => 'Ejecutado correctamente'];
            }
        } catch (Exception $ex) {
            DB::rollback();
            return ['status' => 'error', 'msj' => $ex->getMessage(), 'linea' => $ex->getLine()];
        }
    }

    public function cargar_plantilla()
    {

        Excel::filter('chunk')->load('carga_plantilla.xlsx')->chunk(500, function ($reader) {
            $creado = 0;
            $valor_ap = 0;
            $porcentaje_10 = 0;
            $contador = 0;
            foreach ($reader as $book) {
                $contador++;
                $id_plantilla = $book->id_planilla;
                $id_procedimiento = $book->id_procedimiento;
                $cantidad = $book->cantidad;
                $codigo_insumos = $book->codigo;
                $valor =    $book->valor;
                $porcentaje =  $book->porcentaje;

                $plantilla        = ApPlantilla::find($id_plantilla); //dd($plantilla);
                if (!is_null($plantilla)) {

                    $ap_procedimiento = ApProcedimiento::find($id_procedimiento);
                    //dd($ap_procedimiento);
                    if (!is_null($ap_procedimiento)) {
                        $item_plantilla = ApPlantillaItem::where('cod_plantilla', $plantilla->codigo)->where('procedimiento', $ap_procedimiento->id)->where('id_procedimiento', $ap_procedimiento->codigo)->first();
                        if (is_null($item_plantilla)) {
                            $input = [
                                'cod_plantilla'       => $plantilla->codigo,
                                'tipo_item'           => $ap_procedimiento->tipo,
                                'id_procedimiento'    => $ap_procedimiento->codigo, ///EN GYE en ambos cambos guarda el id y este es para el codigo de ap
                                'cantidad'            => $cantidad,
                                'orden'               => '0',
                                'honorario'           => '100', //Revisar si es 100, 50 o N 
                                'separado'            => 'NO',
                                'procedimiento'       => $ap_procedimiento->id,
                                'estado'              => '1',
                                'id_usuariocrea'  => '0922290697',
                                'id_usuariomod'   => '0922290697',
                                'ip_creacion'     => 'CREA_102023',
                                'ip_modificacion' => '::1',
                            ];
                            //dd($input);
                            ApPlantillaItem::create($input);
                            $creado++;
                        }

                        if ($porcentaje == '0') {
                            if ($ap_procedimiento->valor != $valor) {
                                $input_ap = [
                                    'valor' => $valor,
                                    'id_usuariomod'   => '0922290697',
                                    'ip_creacion'     => 'MOD_04102023',
                                    'ip_modificacion' => '::1',
                                    'codigo_insumos'  => $codigo_insumos,
                                ];
                                $ap_procedimiento->update($input_ap);
                            }
                        } else {
                            $porcentaje_10 = $valor * $porcentaje;
                            $valor_ap = $valor + $porcentaje_10;
                            $imput_ap = [
                                'valor'             => $valor_ap,
                                'porcentaje10'      => $porcentaje,
                                'iva'               => '0.12',
                                'id_usuariomod'     => '0922290697',
                                'ip_creacion'       => 'MOD_04102023',
                                'ip_modificacion'   => '::1',
                                'codigo_insumos'    => $codigo_insumos,
                            ];
                            $ap_procedimiento->update($imput_ap);
                        }
                    }
                }
            }
        });

        return "oki walter 02102023";
    }

    public function detalle_receta($id)
    {

        $receta   = hc_receta::find($id);
        $detalles = $receta->detalles;
        $agenda = $receta->historia->agenda;

        return view('archivo_plano/planilla/receta_detalle', ['detalles' => $detalles, 'receta_id' => $receta->id, 'agenda' => $agenda]);
    }
    private function redondea_menos_4_dec($numero)
    {

        if ($this->contarDecimales($numero) > 4) {
            $numero = floor($numero * 10000) / 10000;
        }

        return $numero;
    }

    private function contarDecimales($numero)
    {
        $partes = explode('.', strval($numero));
        if (count($partes) < 2) {
            return 0; // Si no hay punto decimal, no hay decimales
        } else {
            return strlen($partes[1]); // La longitud de la parte despu�s del punto decimal es el n�mero de decimales
        }
    }
}
