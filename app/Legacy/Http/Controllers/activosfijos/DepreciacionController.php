<?php

namespace Sis_medico\Http\Controllers\activosfijos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Session;
use Sis_medico\AfActivo;
use Sis_medico\AfDepreciacionCabecera;
use Sis_medico\AfDepreciacionDetalle;
use Sis_medico\AfTipo;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\User;
use DateTime;
use Sis_medico\LogAsiento;
use Excel;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Sucursales;

class DepreciacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20)) == false) {
            return true;
        }
    }
    public function buscar(Request $request)
    {
        //0dd("buscar");
        if ($this->rol()) {
            return response()->view('errors.404');
        }


        if (!is_null($request['fecha']) && !is_null($request['fecha'])) {
            $fecha = $request['fecha'];
        } else {
            $fecha = date('d/m/Y');
        }
        if (!is_null($request['tipo']) && !is_null($request['tipo'])) {
            $tipo = $request['tipo'];
        } else {
            $tipo = null;
        }

        $fecha_desde = $request['desde'];
        $fecha_hasta = $request['hasta'];
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        //$generados  = $this->getGenerados($fecha, $id_empresa);
        $activos = $this->getActivos($tipo, $id_empresa, $fecha_desde, $fecha_hasta);

        $tipos   = AfTipo::where('estado', '!=', 0)->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        return view('activosfijos/documentos/depreciacion/index', ['empresa' => $empresa, 'fecha' => $fecha, 'tipos' => $tipos, 'activos' => $activos, 'tipo' => $tipo, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'min_date' => $min_date]);
    }

    public function index(Request $request)
    {
        //dd("index");
        //dd("holis");
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (!is_null($request['fecha']) && !is_null($request['fecha'])) {
            $fecha = $request['fecha'];
        } else {
            $fecha = date('d/m/Y');
        }
        if (!is_null($request['tipo']) && !is_null($request['tipo'])) {
            $tipo = $request['tipo'];
        } else {
            $tipo = null;
        }

        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        //$generados  = $this->getGenerados($fecha, $id_empresa);
        //dd($generados);
        $activos = $this->getActivos($tipo, $id_empresa, $fecha_desde, $fecha_hasta);
        $tipos   = AfTipo::where('estado', '!=', 0)->get();

        // $afdepreciados = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', 1)
        //     ->where('id_empresa', $id_empresa)
        //     ->join('af_depreciacion_detalle as dep_det', 'dep_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')
        //     ->join('af_activo as af', 'af.id', 'dep_det.activo_id')
        //     ->select('af.*')
        //     ->get();

        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        return view('activosfijos/documentos/depreciacion/index', ['empresa' => $empresa, 'fecha' => $fecha, 'tipos' => $tipos, 'activos' => $activos, 'tipo' => $tipo, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'min_date' => $min_date]);
    }

    public function getGenerados($fecha, $id_empresa)
    {
        $fecha     = str_replace('/', '-', $fecha);
        $timestamp = \Carbon\Carbon::parse($fecha)->timestamp;
        $fecha     = date('Y-m-d', $timestamp);

        $datos = '[]';
        if ($fecha != null) {
            $datos = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', 1)
                ->where('id_empresa', $id_empresa)
                ->join('af_depreciacion_detalle as dep_det', 'dep_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')
                ->join('af_activo as af', 'af.id', 'dep_det.activo_id')
                ->select('af.*')
                ->get();
        }
        // dd($facturas);
        return $datos;
    }

    public function getActivos($tipo, $id_empresa, $fecha_desde, $fecha_hasta)
    {
        $activo = AfActivo::where('estado', 1)->where('empresa', $id_empresa)->wherebetween('fecha_compra', [$fecha_desde . ' 00:00', $fecha_hasta . ' 23:59'])->where('costo','>','0');
        if ($tipo != null) {
            $activo = $activo->where('tipo_id', $tipo);
        }
        $activo = $activo->get();

        return $activo;
    }

    public function __store___(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        if (!is_null($request['filperiodo'])) {
            $periodo = $request['filperiodo'];
        } else {
            $periodo = date('Y-m');
        }
        $request['periodo'] = $request['filperiodo'];

        $this->guardarDepreciacion($request);


        //    return redirect()->route('afDepreciacion.index', [$request]);

    }

    public function store_depreciacion(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = Session::get('id_empresa');
        //dd($request->all());
        $verifica_cierre = Ct_Cierre_Cabecera::verificar_fecha($request->fecha_asiento);
        $verifica_cierre = json_decode($verifica_cierre);

        if ($verifica_cierre->status == 'error') {
            return ['respuesta' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }
        DB::BeginTransaction();
        try {
            $sucursal = Ct_Sucursales::where('id_empresa', $id_empresa)->where('estado',1)->first();

            $anio = date('Y', strtotime($request->fecha_asiento));
            $mes = date('m', strtotime($request->fecha_asiento));
            $input = [
                'fecha'           => $request->fecha_asiento,
                'id_empresa'      => $id_empresa,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'anio'            => $anio,
                'mes'             => $mes,
                'observacion'     => $request->det_asiento,
                'sucursal'        => !is_null($sucursal) ? $sucursal->codigo : null
            ];
            $id_cabecera = AfDepreciacionCabecera::insertGetId($input);
            $days = $request->days;
            $i = 0;

            foreach ($request->id_activo as $value) {
                $activo = AfActivo::where('estado', 1)->where('id', $value)->first();

                $valida = AfDepreciacionCabecera::join('af_depreciacion_detalle as add', 'add.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')
                    ->where('af_depreciacion_cabecera.anio', $anio)
                    ->where('af_depreciacion_cabecera.mes', $mes)
                    ->where('af_depreciacion_cabecera.estado', 1)
                    //->where('af_depreciacion_cabecera.estado', 1)
                    ->where('add.activo_id', $value)
                    ->first();

                if (!is_null($valida)) {
                    DB::rollBack();
                    return ['respuesta' => 'error', 'msj' => 'Ya existe un Asiento de Depreciacion para este mes'];
                }



                if (($activo->costo != null and $activo->costo != 0) and ($activo->tasa != null and $activo->tasa != 0)) {

                    if ($days[$i] >= 30) {
                        $days[$i] = 30;
                    }

                    $valordepreciacion = ((($activo->costo * ($activo->tasa / 100)) / 360) * $days[$i]);

                    if (isset($activo->ultima_depreciacion()->totaldepreciado)) {
                        $totaldepreciado = $activo->ultima_depreciacion()->totaldepreciado;
                    } else {
                        $totaldepreciado = 0;
                    }

                    //dd($valordepreciacion);

                    // if ($valordepreciacion == 0) {
                    //     DB::rollBack();
                    //     return ['respuesta' => 'error', 'msj' => 'Valor a depreciar es $0.00 por favor verificar las fechas'];
                    // }

                    $totaldepreciado += $valordepreciacion;
                    $saldo = $activo->costo - $totaldepreciado;

                    AfDepreciacionDetalle::create([
                        'depreciacion_cabecera_id' => $id_cabecera,
                        'activo_id'                => $activo->id,
                        'costo'                    => $activo->costo,
                        'porcentaje'               => $activo->tasa,
                        'vidautil'                 => $activo->vida_util,
                        'valordepreciacion'        => round($valordepreciacion, 2),
                        'totaldepreciado'          => round($totaldepreciado, 2),
                        'saldo'                    => $saldo,
                        'estado'                   => '1',
                        'id_usuariocrea'           => $idusuario,
                        'id_usuariomod'            => $idusuario,
                        'ip_creacion'              => $idusuario,
                        'ip_modificacion'          => $idusuario,
                    ]);
                }

                $i++;
            }

            $this->asientoDepreciacion($id_cabecera, $request->fecha_asiento, $request->det_asiento);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
        // return; 
    }

    public function asientoDepreciacion($id_cab_depreciacion, $fecha_asiento, $det_asiento)
    {
        $cabecera_dep   = AfDepreciacionCabecera::where('id', $id_cab_depreciacion)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa = Session::get('id_empresa');
        $cabecera   = [
            'observacion'     => $det_asiento,
            //'observacion'     => 'DEPRECIACIÓN ACUMULADA DE ACTIVOS FIJOS A FECHA : ' . date('d/m/Y'),
            'fecha_asiento'   => $fecha_asiento,
            // 'fact_numero'       => $request['serie_factura'] . $request['secuencia'],
            'valor'           => 0,
            'estado'          => '2',
            'id_empresa'      => $id_empresa,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabecera);
        if (!is_null($cabecera_dep)) {
            $dep_cab = [
                'id_asiento'    => $id_asiento_cabecera,
            ];
            $cabecera_dep->update($dep_cab);
        }
        $detalles = AfDepreciacionDetalle::where('depreciacion_cabecera_id', $id_cab_depreciacion)
            ->join('af_activo as a', 'af_depreciacion_detalle.activo_id', 'a.id')
            ->join('af_tipo as t', 'a.tipo_id', 't.id')
            ->select('af_depreciacion_detalle.depreciacion_cabecera_id', 't.cuantadepreciacion', 't.cuentagastos', 't.id as id_tipo', DB::raw('SUM(valordepreciacion) as valordepreciacion'))
            ->groupBy('t.id')
            ->get();
        //dd($detalles);
        $acumvalor = 0;
        //dd($detalles);
        foreach ($detalles as $value) {
            $cuenta = Plan_Cuentas::where('id', $value->cuantadepreciacion)->first();
            if (!is_null($cuenta)) {
                $acumvalor += $value->valordepreciacion;
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $value->cuantadepreciacion,
                    'descripcion'         => $cuenta->nombre, //aqui puse el nombre de la cuenta del acreedor
                    'fecha'               => date('Y-m-d H:i:s'),
                    'debe'                => '0',
                    'haber'               => $value->valordepreciacion,
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $idusuario,
                    'ip_modificacion'     => $idusuario,
                ]);
            }
        }

        foreach ($detalles as $value) {

            $cuenta = Plan_Cuentas::where('id', $value->cuentagastos)->first();
            if (!is_null($cuenta)) {
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $value->cuentagastos,
                    'descripcion'         => $cuenta->nombre, //aqui puse el nombre de la cuenta del acreedor
                    'fecha'               => date('Y-m-d H:i:s'),
                    'debe'                => $value->valordepreciacion,
                    'haber'               => 0,
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $idusuario,
                    'ip_modificacion'     => $idusuario,
                ]);
            }
        }

        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $id_asiento_cabecera)->update(['valor' => $acumvalor]);
    }
    public function pdf_depreciacionesmen(Request $request)
    {
        //dd($request->all());
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::find($id_empresa);
        $desde = $request['desde'];
        $hasta = $request['hasta'];
        $tipo  = $request['tipo'];


        $activos = AfActivo::where('estado', '1')->where('empresa', $empresa->id);

        if (!is_null($tipo)) {
            $activos = $activos->where('tipo_id', $tipo);
        }

        if ($desde != null || $hasta != null) {
            $activos = $activos->whereBetween('fecha_compra', [$desde . ' 00:00', $hasta . ' 23:59']);
        }

        $activos = $activos->get();

        $view = \View::make('activosfijos.documentos.depreciacion.pdf_depreciacionesmen', compact('empresa', 'desde', 'tipo', 'activos', 'tipo', 'hasta'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->setPaper('a4', 'landscape');
        return $pdf->stream('Depreciacion_acumulada.pdf');
    }

    public function saldo_inicial_dep($nombre)
    {
        DB::beginTransaction();
        try {
            Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(800, function ($reader) {
                $arr = array();
                $id_cabecera = AfDepreciacionCabecera::insertGetId([
                    'anio'  => '2023',
                    'mes'   => '12',
                    'estado' => '1',
                    'fecha' => '2023-12-01 00:00:00',
                    'observacion' => 'Saldo Inicial depreciación diciembre 2023',
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                    'ip_creacion' => $_SERVER["REMOTE_ADDR"],
                    'ip_modificacion' => $_SERVER["REMOTE_ADDR"],
                ]);
                foreach ($reader as $book) {
                    $activo = AfActivo::where('estado', 1)->where('nombre', 'like', '%' . $book->nombre . '%')->first();
                    if (!is_null($activo)) {
                        AfDepreciacionDetalle::create([
                            'depreciacion_cabecera_id' => $id_cabecera,
                            'activo_id'                => $activo->id,
                            'costo'                    => $activo->costo,
                            'porcentaje'               => $activo->tasa,
                            'vidautil'                 => $activo->vida_util,
                            'valordepreciacion'        => $book->valordepreciacion,
                            'totaldepreciado'          => $book->total_depreciado,
                            'saldo'                    => $book->saldo,
                            'id_usuariocrea'           => Auth::user()->id,
                            'id_usuariomod'            => Auth::user()->id,
                            'ip_creacion'              => $_SERVER["REMOTE_ADDR"],
                            'ip_modificacion'          => $_SERVER["REMOTE_ADDR"],
                        ]);
                    }
                }
            });
            DB::commit();
            return "guardado";
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function dep_acumulada($nombre)
    {
        DB::beginTransaction();
        try {
            Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(800, function ($reader) {
                foreach ($reader as $book) {
                    // $activo = AfActivo::where('estado', 1)->where('nombre', 'like', '%' . $book->nombre . '%')->where('empresa','0993069299001')->first();
                    $activo = AfActivo::find($book->id_activo);
                    if (!is_null($activo)) {
                        $activo->update([
                            'depreciacion_acum' => $book->total_depreciado,
                            'ip_creacion' => 'mod_activo saldo inicial'
                        ]);
                    }
                }
            });
            DB::commit();
            return "guardado";
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }

    public function index_depreciaciones(Request $request)
    {
        $mes = $request['mes'];
        $anio = $request['anio'];
        $observacion = $request['observacion'];

        $id_empresa = Session::get('id_empresa');
        $depreciaciones = AfDepreciacionCabecera::where('estado', 1)->where('id_empresa', $id_empresa)->whereNotNull('id_asiento');
        if (!is_null($mes)) {
            $depreciaciones = $depreciaciones->where('mes', $mes);
        }
        if (!is_null($anio)) {
            $depreciaciones = $depreciaciones->where('anio', $anio);
        }
        if (!is_null($observacion)) {
            $depreciaciones = $depreciaciones->where('observacion', 'like', '%' . $observacion . '%');
        }
        $depreciaciones = $depreciaciones->get();

        return view('activosfijos.documentos.depreciacion.index_depreciaciones', ['depreciaciones' => $depreciaciones, 'anio' => $anio, 'mes' => $mes, 'observacion' => $observacion]);
    }

    public function ver_depreciacion($id)
    {
        $detalles = AfDepreciacionDetalle::where('depreciacion_cabecera_id', $id)->get();
        return view('activosfijos.documentos.depreciacion.modal_depreciacion', ['detalles' => $detalles]);
    }

    public function masivo_dep_acumulada($nombre)
    {
        DB::beginTransaction();
        try {
            Excel::filter('chunk')->load($nombre . '.xlsx')->chunk(600, function ($reader) {
                foreach ($reader as $book) {
                    $activo = AfActivo::find($book->id_activo);
                    if (!is_null($activo)) {
                        $activo->update([
                            'depreciacion_acum' => $book->total_depreciado,
                            'ip_creacion' => 'saldo 2021 dep_acumulada'
                        ]);
                    }
                }
            });
            DB::commit();
            return ['msj' => 'ok gracias amigo'];
        } catch (\Exception $e) {
            DB::rollBack();
            echo '<pre>';
            print_r(['error' => $e->getMessage(), 'linea' => $e->getLine()]);
        }
    }

    public function excel_masivo_depreciacion(Request $request)
    {
        $fecha_desde = $request->fecha_hasta;
        $fecha_hasta = $request->fecha_hasta;
        $titulos = array("ID", "CODIGO", "NOMBRE", "FECHA COMPRA", "COSTO", "SALDO ANT. DEPRECIACION", "DEP. ACUMULADA", "SALDO ACTUAL");
        $titulos2 = array("AÑO", "MES", "FECHA", "MONTO");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U");

        Excel::create('Depreciacion', function ($excel) use ($titulos, $posicion, $titulos2) {
            $excel->sheet('Listado Activos', function ($sheet) use ($titulos, $posicion, $titulos2) {
                $activos = AfActivo::where('estado', '1')->where('empresa', Session::get('id_empresa'))->get();
                $sheet->mergeCells('A1:D1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('LISTADO GENERAL DE ACTIVOS FIJOS CON DEPRECIACIONES');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $comienzo = 5;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                foreach ($activos as $activo) {
                    $depreciacion = AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', '1')
                        ->join('af_depreciacion_detalle as depre_det', 'depre_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')
                        ->select('depre_det.*', 'af_depreciacion_cabecera.*')
                        ->where('depre_det.activo_id', $activo->id)
                        ->get();
                    $tot_activo = 0;

                    if (!is_null($depreciacion)) {
                        foreach ($depreciacion as $dep) {
                            $tot_activo += $dep->valordepreciacion;
                        }

                        $tot_activo = $tot_activo + $activo->depreciacion_acum;
                    }

                    $new_saldo = $activo->costo - $tot_activo;


                    if ($new_saldo != 0) {
                        $saldo = $new_saldo;
                    } else {
                        $saldo = $activo->costo - $activo->depreciacion_acum;
                    }

                    $datos_excel = array();
                    array_push($datos_excel, $activo->id, $activo->codigo, $activo->nombre, substr($activo->fecha_compra, 0, 10), $activo->costo, $activo->depreciacion_acum, $tot_activo, $saldo);

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                        });
                    }

                    $comienzo++;

                    $comienzo = $comienzo + 1;

                    $tot_depreciado = 0;
                    foreach ($depreciacion as $dep) {
                        $tot_depreciado += $dep->valordepreciacion;
                        $datos_excel1 = array();
                        array_push($datos_excel1, substr($dep->fecha, 0, 4), date("m", strtotime($dep->fecha)), substr($dep->fecha, 0, 10), $dep->valordepreciacion);
                        for ($i = 0; $i < count($datos_excel1); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel1, $i) {
                                $cell->setValue($datos_excel1[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                    $sheet->mergeCells('A' . $comienzo . ':C' . $comienzo);
                    $sheet->cell('A' . $comienzo, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('TOTAL DEPRECIADO POR ACTIVO');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $comienzo, function ($cell) use ($tot_depreciado) {
                        // manipulate the cel
                        $cell->setValue($tot_depreciado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $comienzo++;
                    $comienzo = $comienzo + 1;
                }
            });
        })->export('xlsx');
    }

    public function anular_depreciacion($id)
    {
        DB::beginTransaction();
        try {

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $depreciacion = AfDepreciacionCabecera::find($id);

            $fecha_dep = date("Y-m-d", strtotime($depreciacion->fecha));
            $verifica_cierre = Ct_Cierre_Cabecera::verificar_fecha($fecha_dep);
            $verifica_cierre = json_decode($verifica_cierre);

            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }

            $depreciacion->update([
                'estado' => 0,
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente
            ]);

            $asientoc = Ct_Asientos_Cabecera::find($depreciacion->id_asiento);

            $cabecera   = [
                'sucursal'        => $asientoc->sucursal,
                'punto_emision'   => $asientoc->punto_emision,
                'observacion'     => 'ANULACION ' . $depreciacion->observacion,
                'fecha_asiento'   => $asientoc->fecha_asiento,
                'fact_numero'     => $asientoc->fact_numero,
                'valor'           => $asientoc->valor,
                'estado'          => '1',
                'id_empresa'      => $asientoc->id_empresa,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabecera);

            $detasiento = Ct_Asientos_Detalle::where('id_asiento_cabecera', $asientoc->id)->orderBy('id', 'desc')->get();
            foreach ($detasiento as $row) {
                $asiento_detalle = [
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $row->id_plan_cuenta,
                    'descripcion'         => $row->descripcion, //aqui puse el nombre de la cuenta del acreedor
                    'fecha'               => $row->fecha,
                    'debe'                => $row->haber,
                    'haber'               => $row->debe,
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $idusuario,
                    'ip_modificacion'     => $idusuario,
                ];
                Ct_Asientos_Detalle::insert($asiento_detalle);
            }
            LogAsiento::anulacion("AF-DEP", $id_asiento_cabecera, $asientoc->id);

            DB::commit();
            return ['status' => 'success', 'msj' => 'Depreciacion anulada'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => 'error', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
}
