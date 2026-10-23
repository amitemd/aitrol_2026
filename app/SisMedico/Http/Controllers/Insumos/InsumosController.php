<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Sis_medico\Http\Controllers\Controller;
use Session;
use Sis_medico\InvInventarioSerie;
use Sis_medico\ControlStockCabecera;
use Sis_medico\ControlStockDetalle;
use Sis_medico\User;
use Sis_medico\Agenda;
use Sis_medico\Bodega;
use Sis_medico\Ct_productos;
use Sis_medico\hc_procedimientos;
use Sis_medico\Equipo_Historia;
use Sis_medico\Empresa;
use Sis_medico\Producto;
use Sis_medico\Movimiento_Paciente;
use Sis_medico\OrdenCompra;
use Sis_medico\OrdenCompraDetalle;
use Sis_medico\Presentaciones;
use Sis_medico\Producto_Lotes;
use Sis_medico\Concentracion;

class InsumosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22, 7)) == false) {
            return true;
        }
    }

    public function edit($id)
    {
        $orden = DB::table('orden_compra')
            ->where('id', $id)
            ->first();

        if (!$orden) {
            return redirect()->route('ordenes_compra.index')
                ->with('error', 'La orden de compra no existe.');
        }

        $proveedor = DB::table('proveedor')
            ->where('id', $orden->proveedor)
            ->first();

        $detalle = DB::table('orden_compra_detalle as d')
            ->leftJoin('ct_productos as p', 'p.id', '=', 'd.producto')
            ->where('d.orden_compra_id', $id)
            ->orderBy('d.orden', 'asc')
            ->select(
                'd.*',
                DB::raw("
                CASE
                    WHEN p.id IS NOT NULL THEN CONCAT(IFNULL(p.codigo, ''), ' - ', IFNULL(p.nombre, ''))
                    ELSE d.codigo
                END as producto_texto
            ")
            )
            ->get();

        $orden->proveedor_data = $proveedor;
        $orden->detalle = $detalle;

        return view('insumos.orden_venta.orden_venta', ['orden' => $orden]);
    }

    public function pdf($id)
    {
        $orden = DB::table('orden_compra')
            ->where('id', $id)
            ->first();

        if (!$orden) {
            return redirect()->route('ordenes_compra.index')
                ->with('error', 'La orden de compra no existe.');
        }

        $proveedor = DB::table('proveedor')
            ->where('id', $orden->proveedor)
            ->first();

        $detalle = DB::table('orden_compra_detalle as d')
            ->leftJoin('ct_productos as p', 'p.id', '=', 'd.producto')
            ->where('d.orden_compra_id', $id)
            ->orderBy('d.orden', 'asc')
            ->select(
                'd.*',
                'p.codigo as codigo_real',
                'p.nombre as nombre_producto'
            )
            ->get();

        $logoPath = null;
        if (!empty(session('id_empresa'))) {
            $empresa = DB::table('empresa')->where('id', session('id_empresa'))->first();

            if ($empresa && !empty($empresa->logo_form)) {
                $rutaLogo = storage_path('app/logo/' . $empresa->logo_form);
                if (file_exists($rutaLogo)) {
                    $logoPath = $rutaLogo;
                }
            }
        }


        $lineas = [];
        foreach ($detalle as $item) {
            $lineas[] = [
                'codigo' => $item->codigo_real ?? $item->codigo ?? '',
                'descripcion' => $item->descripcion ?? ($item->nombre_producto ?? ''),
                'cantidad' => $item->cantidad ?? 0,
                'diameter_fr' => $item->diameter_fr ?? '',
                'precio_unitario' => $item->precio_unitario ?? 0,
                'total' => $item->total ?? 0,
                'observacion' => $item->observacion ?? '',
            ];
        }


        $filasDeseadas = 11;
        $faltantes = max(0, $filasDeseadas - count($lineas));

        $html = view('insumos/orden_venta/pdf_orden', [
            'orden' => $orden,
            'proveedor' => $proveedor,
            'lineas' => $lineas,
            'faltantes' => $faltantes,
            'logoPath' => $logoPath,
            'empresa' => $empresa,
        ])->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html)->setPaper('a4');

        return $pdf->stream('orden_compra_' . $orden->numero . '.pdf');
    }

    public function buscarProveedor(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $proveedor  = [];
        if ($request['search'] != null) {
            $proveedor = Proveedor::where('nombrecomercial', 'LIKE', "%{$request['search']}%")->select('id as id', 'nombrecomercial as text', 'direccion as direccion')->where('estado', 1)->take(4)->get();
        }

        return response()->json($proveedor);
    }

    public function update(Request $request, $id)
    {

        //dd($request->all());

        DB::beginTransaction();

        try {

            DB::table('orden_compra')
                ->where('id', $id)
                ->update([
                    'fecha' => $request->fecha,
                    'proveedor' => $request->proveedor_id,
                    'entrega_a' => $request->entrega_a,
                    'observaciones' => null,
                    'total_cantidades' => $this->cleanNumber($request->total_cantidades ?? 0),
                    'subtotal' => $this->cleanNumber($request->total_subtotal ?? 0),
                    "id_usuariomod" => Auth::user()->id,
                ]);

            DB::table('orden_compra_detalle')
                ->where('orden_compra_id', $id)
                ->delete();

            foreach ($request->items ?? [] as $index => $item) {
                if (empty($item['product_id'])) {
                    continue;
                }

                DB::table('orden_compra_detalle')->insert([
                    'orden_compra_id' => $id,
                    'producto' => $item['product_id'] ?? null,
                    'codigo' => $item['product_id'] ?? null,
                    'descripcion' => $item['descripcion'] ?? null,
                    'cantidad' => $this->cleanNumber($item['cantidad'] ?? 0),
                    'diameter_fr' => $item['diameter_fr'] ?? null,
                    'precio_unitario' => $this->cleanNumber($item['precio'] ?? 0),
                    'total' => $this->cleanNumber($item['total'] ?? 0),
                    'observacion' => $item['observacion'] ?? null,
                    'orden' => $index + 1,
                    "id_usuariomod" => Auth::user()->id,

                ]);
            }

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Orden actualizada con éxito.',
                'id' => $id
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function cleanNumber($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (float) str_replace(['$', ','], '', $value);
    }

    public function index_orden_compra()
    {


        $ordenes = OrdenCompra::orderBy('id', 'desc')
            ->paginate(20);
            



        return view('insumos/orden_venta/index_orden_compra', ['ordenes' => $ordenes]);
    }


    public function orden_compra()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }


        return view('insumos/orden_venta/orden_venta');
    }


    public function buscarProductos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $producto = [];

        if (!is_null($request["search"])) {

            $producto = Ct_productos::where('estado_tabla', 1)
                ->where('id_empresa', $id_empresa)
                ->where('codigo', 'LIKE', 'EG%')
                ->where('codigo', 'NOT LIKE', 'EG ER%')
                ->where(function ($q) use ($request) {
                    $q->where('codigo', "LIKE", "%{$request['search']}%")
                        ->orWhere('nombre', "LIKE", "%{$request['search']}%");
                })
                ->select('id', DB::raw('CONCAT(codigo," | ",nombre) as text'))
                ->take(20)
                ->get();
        }

        return response()->json($producto);
    }

    public function store(Request $request)
    {

    //dd($request->all());

        DB::beginTransaction();

        try {

            $ultimoNumero = DB::table('orden_compra')
                ->where('id_empresa', session('id_empresa'))
                ->lockForUpdate()
                ->max('numero');

            $siguienteNumero = str_pad(((int)$ultimoNumero) + 1, 10, '0', STR_PAD_LEFT);

            $idOrdenCompra = DB::table('orden_compra')->insertGetId([
                'id_empresa' => session('id_empresa'),
                'numero' => $siguienteNumero,
                'fecha' => $request->fecha,
                'proveedor' => $request->proveedor_id,
                'entrega_a' => $request->entrega_a,
                'observaciones' => null,
                'total_cantidades' => $request->total_cantidades ?? 0,
                'subtotal' => $request->total_subtotal ?? 0,
                'estado' => 1,
                'id_usuariocrea' => Auth::user()->id,
                'id_usuariomod' => Auth::user()->id,
            ]);

            foreach ($request->items ?? [] as $index => $item) {
                if (empty($item['product_id'])) {
                    continue;
                }

                DB::table('orden_compra_detalle')->insert([
                    'orden_compra_id' => $idOrdenCompra,
                    'producto' => $item['product_id'] ?? null,
                    'codigo' => $item['product_id'] ?? null,
                    'descripcion' => $item['descripcion'] ?? null,
                    'cantidad' => $item['cantidad'] ?? 0,
                    'diameter_fr' => $item['diameter_fr'] ?? null,
                    'precio_unitario' => $item['precio'] ?? 0,
                    'total' => $this->cleanNumber($item['total'] ?? 0),
                    'observacion' => $item['observacion'] ?? null,
                    'orden' => $index + 1,
                    'id_usuariocrea' => Auth::user()->id,
                    'id_usuariomod' => Auth::user()->id,
                ]);
            }

           

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Orden guardada con éxito.',
                'id' => $idOrdenCompra,
                'numero' => $siguienteNumero
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function descargo_insumos($id_agenda)
    {
        // dd("EPA");
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $agenda = Agenda::find($id_agenda);
        $bodegas = Bodega::all();

        $procedimientos = hc_procedimientos::join('historiaclinica as hc', 'hc.hcid', '=', 'hc_procedimientos.id_hc')
            ->where('hc.id_agenda', $id_agenda)->get();

        if ($procedimientos->count() > 0) {
            $hcid = $procedimientos->first()->hcid;
        } else {
            $hcid = "";
        }
        $equipos = Equipo_Historia::where('hcid', $hcid)->get(); 

        return view('enfermeria/insumos', ['procedimientos' => $procedimientos, 'agenda' => $agenda, 'equipos' => $equipos, 'hcid' => $hcid, 'bodegas'=>$bodegas]);
    }

    /* REPORTE DE EGRESO DE PACIENTES */

    public function egresoprocedimiento(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha_desde = date('Y-m-d', strtotime($request['fecha_desde']));
            $fecha_hasta = date('Y-m-d', strtotime($request['fecha_hasta']));
        } else {
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
        }

        $id_empresa     = Session::get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $id_producto = 0;
        if (isset($request['id_producto'])) {
            $id_producto = $request['id_producto'];
        }
        $id_bodega = 0;
        if (isset($request['id_bodega'])) {
            $id_bodega = $request['id_bodega'];
        }
        // dd($fecha_desde);
        $productos  = Producto::orderby('id', 'desc')->get();
        $bodegas    = Bodega::all();
        return view('insumos/reporte/egreso_procedimiento/index', [
            'productos' => $productos, 'empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'id_producto' => $id_producto, 
            'bodegas'=>$bodegas, 'id_bodega' => $id_bodega
        ]);
    }

    public function show_egresoprocedimiento(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa     = Session::get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $rfecha_desde           = $request['fecha_desde'];
            $request['fecha_desde'] = str_replace('/', '-', $request['fecha_desde']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_desde'])->timestamp;
            $fecha_desde            = date('Y-m-d', $timestamp);

            $rfecha_hasta           = $request['fecha_hasta'];
            $request['fecha_hasta'] = str_replace('/', '-', $request['fecha_hasta']);
            $timestamp              = \Carbon\Carbon::parse($request['fecha_hasta'])->timestamp;
            $fecha_hasta            = date('Y-m-d', $timestamp);
        } else {
            $fecha_desde = date('Y/m/d');
            $fecha_hasta = date('Y/m/d');
        }
        $movimiento = Movimiento_Paciente::where('created_at','>=' , $fecha_desde . " 00:00:00")
                                            ->where('created_at','<=' , $fecha_hasta . " 23:59:59")
                                            ->get();
        $equipo = Equipo_Historia::where('created_at','>=' , $fecha_desde . " 00:00:00")
                                            ->where('created_at','<=' , $fecha_hasta . " 23:59:59")
                                            ->get();
        return view('insumos/reporte/egreso_procedimiento/show', ['empresa' => $empresa, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'movimiento' => $movimiento, 
            'detalles' => "", "equipo"=>$equipo
        ]);
                                    
    }

     
    public static function pdfReporte(Request $request)
    {


        $meses = [
            1  => 'Enero',
            2  => 'Febrero',
            3  => 'Marzo',
            4  => 'Abril',
            5  => 'Mayo',
            6  => 'Junio',
            7  => 'Julio',
            8  => 'Agosto',
            9  => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];



        $fechaDesde = $request->fecha_d . ' 00:00:00';
        $fechaHasta = $request->fecha_h . ' 23:59:59';

        $fechaCarbon = Carbon::parse($request->fecha_d);



        $movimientos = ControlStockDetalle::join(
            "control_stock_cabecera",
            "control_stock_detalle.cabecera_id",
            "=",
            "control_stock_cabecera.id"
        )
            ->leftJoin(
                'producto_lotes',
                'control_stock_detalle.producto_lote_id',
                '=',
                'producto_lotes.id'
            )
            ->join(
                "producto",
                "producto_lotes.producto_id",
                "=",
                "producto.id"
            )
            ->leftJoin(
                'presentaciones',
                'producto_lotes.presentaciones_id',
                '=',
                'presentaciones.id'
            )
            ->leftJoin(
                'concentracion',
                'producto_lotes.concentracion_id',
                '=',
                'concentracion.id'
            )
            ->whereIn("control_stock_cabecera.tipo", [1, 2])
            ->whereBetween("control_stock_cabecera.fecha", [$fechaDesde, $fechaHasta])
            ->select(
                'control_stock_cabecera.tipo',
                'control_stock_cabecera.responsable_id',
                'control_stock_cabecera.fecha',
                'presentaciones.id as idPresentacion',
                'concentracion.id as idConcentracion',
                'control_stock_detalle.*',
                'control_stock_detalle.lote as lote',
                'producto.id as producto_id',
                'producto.nombre as producto',
                'producto_lotes.fecha_caducidad'
            )

            ->orderBy('producto.nombre', 'asc')
            ->orderBy('control_stock_cabecera.fecha', 'asc')
            ->get();

        $stockAgrupado = [];

        foreach ([1, 2] as $tipo) {

            $stockAgrupado[$tipo] = $movimientos
                ->where('tipo', $tipo)
                ->groupBy(function ($item) {
                    return implode('|', [
                        $item->producto_id,
                        $item->lote,
                        $item->idPresentacion,
                        $item->idConcentracion,
                        $item->fecha_caducidad,
                    ]);
                })
                ->map(function ($items) {

                    $primero = $items->first();

                    $dias = [];
                    $responsablesPorDia = [];

                    foreach ($items as $item) {
                        $dia = (int) date('d', strtotime($item->fecha));

                        $dias[$dia] = $item->stock;

                        $responsablesPorDia[$dia] = $item->responsable_id;
                    }

                    $primero->dias = $dias;
                    $primero->responsablesPorDia = $responsablesPorDia;

                    return $primero;
                })
                ->values();
        }

        $array = [];

        $array[0] = $stockAgrupado[1] ?? collect();
        $array[1] = $stockAgrupado[2] ?? collect();

        $responsable = User::find($request->responsable);
        $presentaciones = Presentaciones::where("estado", 1)->get();
        $concentracion = Concentracion::where("estado", 1)->get();

        $data = [
            "servicio"                   => $request->servicio,
            "opc"                        => $request->opc,
            "responsable"                => $responsable,
            "mesNombre"                  => $meses[$fechaCarbon->month],
            "anio"                       => $fechaCarbon->year,
            "diasMes"                    => $fechaCarbon->daysInMonth,
            "stock"                      => $array,
            "presentaciones"             => $presentaciones,
            "concentracion"              => $concentracion,
        ];

        $view = \View::make('insumos/carro_paro/pdf_reporte', $data)->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('legal', 'landscape');
        $pdf->setOptions([
            'dpi' => 120,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'chroot' => base_path('/')
        ]);

        return $pdf->stream('reporte_coche_paro.pdf');
    }

    public function excelReporte(Request $request)
    {
        $meses = [
            1  => 'Enero',
            2  => 'Febrero',
            3  => 'Marzo',
            4  => 'Abril',
            5  => 'Mayo',
            6  => 'Junio',
            7  => 'Julio',
            8  => 'Agosto',
            9  => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        $fechaDesde = $request->fecha_d . ' 00:00:00';
        $fechaHasta = $request->fecha_h . ' 23:59:59';

        $responsable = User::find($request->responsable);

        $presentaciones = Presentaciones::where('estado', 1)->get()->keyBy('id');
        $concentracion = Concentracion::where('estado', 1)->get()->keyBy('id');

        $fechaCarbon = Carbon::parse($request->fecha_d);
        $mesNombre = $meses[$fechaCarbon->month];
        $anio = $fechaCarbon->year;
        $diasMes = $fechaCarbon->daysInMonth;

        $nombreResponsable = '';

        if ($responsable) {
            $nombreResponsable = trim(
                (isset($responsable->nombre1) ? $responsable->nombre1 : '') . ' ' .
                    (isset($responsable->nombre2) ? $responsable->nombre2 : '') . ' ' .
                    (isset($responsable->apellido1) ? $responsable->apellido1 : '') . ' ' .
                    (isset($responsable->apellido2) ? $responsable->apellido2 : '')
            );

            if ($nombreResponsable == '' && isset($responsable->name)) {
                $nombreResponsable = $responsable->name;
            }
        }

        $nombreServicio = '';

        if (isset($request->servicio) && $request->servicio == 1) {
            $nombreServicio = 'GASTROENTEROLOGÍA';
        } else {
            $nombreServicio = isset($request->servicio) ? $request->servicio : '';
        }


        $movimientos = ControlStockDetalle::join(
            "control_stock_cabecera",
            "control_stock_detalle.cabecera_id",
            "=",
            "control_stock_cabecera.id"
        )
            ->leftJoin(
                'producto_lotes',
                'control_stock_detalle.producto_lote_id',
                '=',
                'producto_lotes.id'
            )
            ->join(
                "producto",
                "producto_lotes.producto_id",
                "=",
                "producto.id"
            )
            ->leftJoin(
                'presentaciones',
                'producto_lotes.presentaciones_id',
                '=',
                'presentaciones.id'
            )
            ->leftJoin(
                'concentracion',
                'producto_lotes.concentracion_id',
                '=',
                'concentracion.id'
            )
            ->whereIn("control_stock_cabecera.tipo", [1, 2])
            ->whereBetween("control_stock_cabecera.fecha", [$fechaDesde, $fechaHasta])
            ->select(
                'control_stock_cabecera.tipo',
                'control_stock_cabecera.responsable_id',
                'control_stock_cabecera.fecha',

                'presentaciones.id as idPresentacion',
                'concentracion.id as idConcentracion',

                'control_stock_detalle.*',
                'control_stock_detalle.lote as lote',

                'producto.id as producto_id',
                'producto.nombre as producto',

                'producto_lotes.fecha_caducidad'
            )
            ->orderBy('producto.nombre', 'asc')
            ->orderBy('control_stock_detalle.lote', 'asc')
            ->orderBy('control_stock_cabecera.fecha', 'asc')
            ->get();

        $responsablesIds = $movimientos
            ->pluck('responsable_id')
            ->filter()
            ->unique()
            ->values();

        $firmasUsuarios = \Sis_medico\Firma_Usuario::whereIn('id_usuario', $responsablesIds)
            ->get()
            ->keyBy('id_usuario');

        $stockAgrupado = [];
        $indexMovimientos = [];
        $indexFirmas = [];
        $diasConRegistro = [];

        foreach ([1, 2] as $tipo) {

            $stockAgrupado[$tipo] = $movimientos
                ->where('tipo', $tipo)
                ->groupBy(function ($item) {

                    return implode('|', [
                        isset($item->producto_id) ? $item->producto_id : '',
                        isset($item->lote) ? $item->lote : '',
                        isset($item->idPresentacion) ? $item->idPresentacion : '',
                        isset($item->idConcentracion) ? $item->idConcentracion : '',
                        isset($item->fecha_caducidad) ? $item->fecha_caducidad : '',
                    ]);
                })
                ->map(function ($items) use (
                    &$indexMovimientos,
                    &$indexFirmas,
                    &$diasConRegistro,
                    $firmasUsuarios,
                    $tipo
                ) {

                    $primero = $items->first();

                    $claveFila = implode('|', [
                        isset($primero->producto_id) ? $primero->producto_id : '',
                        isset($primero->lote) ? $primero->lote : '',
                        isset($primero->idPresentacion) ? $primero->idPresentacion : '',
                        isset($primero->idConcentracion) ? $primero->idConcentracion : '',
                        isset($primero->fecha_caducidad) ? $primero->fecha_caducidad : '',
                    ]);

                    $dias = [];
                    $responsablesPorDia = [];

                    foreach ($items as $item) {

                        if (empty($item->fecha)) {
                            continue;
                        }

                        $dia = (int) Carbon::parse($item->fecha)->day;

                        $dias[$dia] = $item->stock;

                        $responsablesPorDia[$dia] = $item->responsable_id;

                        $diasConRegistro[$tipo][$dia] = true;

                        if (!isset($indexMovimientos[$tipo])) {
                            $indexMovimientos[$tipo] = [];
                        }

                        if (!isset($indexMovimientos[$tipo][$claveFila])) {
                            $indexMovimientos[$tipo][$claveFila] = [];
                        }

                        $indexMovimientos[$tipo][$claveFila][$dia] = $item->stock;

                        if (
                            !empty($item->responsable_id) &&
                            isset($firmasUsuarios[$item->responsable_id]) &&
                            !empty($firmasUsuarios[$item->responsable_id]->nombre) &&
                            !isset($indexFirmas[$tipo][$dia])
                        ) {
                            $indexFirmas[$tipo][$dia] = storage_path(
                                'app/avatars/' . $firmasUsuarios[$item->responsable_id]->nombre
                            );
                        }
                    }

                    $primero->clave_fila = $claveFila;
                    $primero->dias = $dias;
                    $primero->responsablesPorDia = $responsablesPorDia;

                    return $primero;
                })
                ->values();
        }

        $array = [];
        $array[0] = isset($stockAgrupado[1]) ? $stockAgrupado[1] : collect();
        $array[1] = isset($stockAgrupado[2]) ? $stockAgrupado[2] : collect();
        Excel::create('reporte_coche_paro', function ($excel) use (
            $array,
            $presentaciones,
            $concentracion,
            $diasMes,
            $nombreResponsable,
            $nombreServicio,
            $mesNombre,
            $anio,
            $indexMovimientos,
            $indexFirmas,
            $diasConRegistro
        ) {

            $excel->sheet('Insumos', function ($sheet) use (
                $array,
                $presentaciones,
                $diasMes,
                $nombreResponsable,
                $nombreServicio,
                $mesNombre,
                $anio,
                $indexMovimientos,
                $indexFirmas,
                $diasConRegistro
            ) {
                $stockInsumos = isset($array[1]) ? $array[1] : collect();

                $this->generarHojaCocheParo(
                    $sheet,
                    'CONTROL DE INSUMOS DE COCHE DE PARO',
                    $stockInsumos,
                    2,
                    false,
                    $presentaciones,
                    null,
                    $diasMes,
                    $nombreResponsable,
                    $nombreServicio,
                    $mesNombre,
                    $anio,
                    $indexMovimientos,
                    $indexFirmas,
                    $diasConRegistro
                );
            });

            $excel->sheet('Medicamentos', function ($sheet) use (
                $array,
                $presentaciones,
                $concentracion,
                $diasMes,
                $nombreResponsable,
                $nombreServicio,
                $mesNombre,
                $anio,
                $indexMovimientos,
                $indexFirmas,
                $diasConRegistro
            ) {
                $stockMedicamentos = isset($array[0]) ? $array[0] : collect();

                $this->generarHojaCocheParo(
                    $sheet,
                    'CONTROL DE MEDICAMENTOS DE COCHE DE PARO',
                    $stockMedicamentos,
                    1,
                    true,
                    $presentaciones,
                    $concentracion,
                    $diasMes,
                    $nombreResponsable,
                    $nombreServicio,
                    $mesNombre,
                    $anio,
                    $indexMovimientos,
                    $indexFirmas,
                    $diasConRegistro
                );
            });
        })->export('xlsx');
    }

     private function generarHojaCocheParo(
        $sheet,
        $titulo,
        $stock,
        $tipoCabecera,
        $llevaConcentracion,
        $presentaciones,
        $concentraciones,
        $diasMes,
        $nombreResponsable,
        $nombreServicio,
        $mesNombre,
        $anio,
        $indexMovimientos,
        $indexFirmas,
        $diasConRegistro
    ) {


        $columnasFijas = $llevaConcentracion ? 7 : 6;
        $totalColumnas = $columnasFijas + $diasMes;
        $ultimaColumna = $this->numeroALetraColumna($totalColumnas);
        $sheet->getStyle('C:C')
            ->getNumberFormat()
            ->setFormatCode('@');
        $fila = 1;



        $sheet->mergeCells("A{$fila}:{$ultimaColumna}{$fila}");
        $sheet->row($fila, [$titulo]);

        $sheet->cells("A{$fila}:{$ultimaColumna}{$fila}", function ($cells) {
            $cells->setAlignment('center');
            $cells->setValignment('center');
            $cells->setFontWeight('bold');
            $cells->setFontSize(16);
            $cells->setBackground('#D9EAF7');
        });

        $fila++;

        $sheet->mergeCells("A{$fila}:{$ultimaColumna}{$fila}");
        $sheet->row($fila, ['Reporte mensual de control y verificación']);

        $sheet->cells("A{$fila}:{$ultimaColumna}{$fila}", function ($cells) {
            $cells->setAlignment('center');
            $cells->setFontSize(11);
        });

        $fila += 2;


        $sheet->row($fila, [
            'Responsable',
            $nombreResponsable,
            'Servicio',
            $nombreServicio,
            'Periodo',
            $mesNombre . ' ' . $anio
        ]);

        $sheet->cells("A{$fila}:F{$fila}", function ($cells) {
            $cells->setBorder('solid', 'solid', 'solid', 'solid');
        });

        foreach (['A', 'C', 'E'] as $col) {
            $sheet->cells($col . $fila, function ($cells) {
                $cells->setFontWeight('bold');
                $cells->setBackground('#E2E8F0');
            });
        }

        $fila += 2;

        $sheet->row($fila, ['', 'Caducado', '', 'Próximo a caducar (7 días)']);

        $sheet->cells("A{$fila}", function ($cells) {
            $cells->setBackground('#FDE2E2');
            $cells->setBorder('solid', 'solid', 'solid', 'solid');
        });

        $sheet->cells("C{$fila}", function ($cells) {
            $cells->setBackground('#FEF3C7');
            $cells->setBorder('solid', 'solid', 'solid', 'solid');
        });

        $fila += 2;


        if ($llevaConcentracion) {
            $cabecera = [
                '#',
                'Insumo',
                'Lote',
                'Present.',
                'Concen.',
                'Cant.',
                'Caduca'
            ];
        } else {
            $cabecera = [
                '#',
                'Insumo',
                'Lote',
                'Present.',
                'Cant.',
                'Caduca'
            ];
        }

        for ($i = 1; $i <= $diasMes; $i++) {
            $cabecera[] = $i;
        }

        $sheet->row($fila, $cabecera);

        $sheet->cells("A{$fila}:{$ultimaColumna}{$fila}", function ($cells) {
            $cells->setBackground('#1E293B');
            $cells->setFontColor('#FFFFFF');
            $cells->setFontWeight('bold');
            $cells->setAlignment('center');
            $cells->setValignment('center');
            $cells->setBorder('solid', 'solid', 'solid', 'solid');
        });

        $filaDataInicio = $fila + 1;
        $fila++;


        if (!empty($stock) && count($stock) > 0) {

            foreach ($stock as $index => $val) {

                $fechaVence = isset($val->fecha_caducidad) ? $val->fecha_caducidad : '';

                $presentacionTexto = $this->buscarPresentacionExcel(
                    $presentaciones,
                    isset($val->idPresentacion) ? $val->idPresentacion : null
                );

                $loteTexto = isset($val->lote) ? $val->lote : '';

                $cantidad = '';

                if (isset($val->stock_disponible)) {
                    $cantidad = $val->stock_disponible;
                } elseif (isset($val->stock)) {
                    $cantidad = $val->stock;
                }

                $claveFila = isset($val->clave_fila) ? $val->clave_fila : implode('|', [
                    isset($val->producto_id) ? $val->producto_id : '',
                    isset($val->lote) ? $val->lote : '',
                    isset($val->idPresentacion) ? $val->idPresentacion : '',
                    isset($val->idConcentracion) ? $val->idConcentracion : '',
                    isset($val->fecha_caducidad) ? $val->fecha_caducidad : '',
                ]);

                if ($llevaConcentracion) {

                    $idConcentracion = null;

                    if (isset($val->idConcentracion)) {
                        $idConcentracion = $val->idConcentracion;
                    } elseif (isset($val->idConcetracion)) {
                        $idConcentracion = $val->idConcetracion;
                    }

                    $concentracionTexto = $this->buscarConcentracionExcel(
                        $concentraciones,
                        $idConcentracion
                    );

                    $row = [
                        $index + 1,
                        isset($val->producto) ? $val->producto : '',
                        $loteTexto,
                        $presentacionTexto,
                        $concentracionTexto,
                        $cantidad,
                        $fechaVence
                    ];
                } else {

                    $row = [
                        $index + 1,
                        isset($val->producto) ? $val->producto : '',
                        $loteTexto,
                        $presentacionTexto,
                        $cantidad,
                        $fechaVence
                    ];
                }


                for ($i = 1; $i <= $diasMes; $i++) {

                    if (isset($val->dias) && isset($val->dias[$i])) {
                        $row[] = $val->dias[$i];
                    } elseif (
                        isset($indexMovimientos[$tipoCabecera]) &&
                        isset($indexMovimientos[$tipoCabecera][$claveFila]) &&
                        isset($indexMovimientos[$tipoCabecera][$claveFila][$i])
                    ) {
                        $row[] = $indexMovimientos[$tipoCabecera][$claveFila][$i];
                    } else {
                        $row[] = '';
                    }
                }

                $sheet->row($fila, $row);

                $sheet->cells("A{$fila}:{$ultimaColumna}{$fila}", function ($cells) {
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                    $cells->setValignment('center');
                });

                if (!empty($fechaVence)) {

                    $hoy = date('Y-m-d');
                    $unaSemana = date('Y-m-d', strtotime('+7 days'));

                    if ($fechaVence < $hoy) {
                        $sheet->cells("A{$fila}:{$ultimaColumna}{$fila}", function ($cells) {
                            $cells->setBackground('#FDE2E2');
                        });
                    } elseif ($fechaVence <= $unaSemana) {
                        $sheet->cells("A{$fila}:{$ultimaColumna}{$fila}", function ($cells) {
                            $cells->setBackground('#FEF3C7');
                        });
                    }
                }

                $fila++;
            }
        } else {

            $sheet->mergeCells("A{$fila}:{$ultimaColumna}{$fila}");
            $sheet->row($fila, ['No existen registros para mostrar.']);

            $sheet->cells("A{$fila}:{$ultimaColumna}{$fila}", function ($cells) {
                $cells->setAlignment('center');
                $cells->setFontWeight('bold');
                $cells->setBorder('solid', 'solid', 'solid', 'solid');
            });

            $fila++;
        }


        $labelFirma = $columnasFijas;

        $firmaRow = array_fill(0, $labelFirma - 1, '');
        $firmaRow[] = 'Firma Responsable';

        $firmasParaInsertar = [];

        for ($i = 1; $i <= $diasMes; $i++) {

            $rutaFirma = '';

            if (
                isset($indexFirmas[$tipoCabecera]) &&
                isset($indexFirmas[$tipoCabecera][$i])
            ) {
                $rutaFirma = $indexFirmas[$tipoCabecera][$i];
            }

            $firmaRow[] = '';

            if (!empty($rutaFirma) && file_exists($rutaFirma)) {
                $firmasParaInsertar[$i] = $rutaFirma;
            }
        }

        $sheet->row($fila, $firmaRow);
        $sheet->setHeight($fila, 45);

        foreach ($firmasParaInsertar as $dia => $rutaFirma) {

            $columnaFirma = $this->numeroALetraColumna($labelFirma + $dia);

            $objDrawing = new \PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Firma');
            $objDrawing->setDescription('Firma');

            $objDrawing->setPath($rutaFirma);
            $objDrawing->setHeight(40);
            $objDrawing->setCoordinates($columnaFirma . $fila);
            $objDrawing->setOffsetX(5);
            $objDrawing->setOffsetY(5);
            $objDrawing->setWorksheet($sheet);
        }

        $rangoFirmaLabel = "A{$fila}:" . $this->numeroALetraColumna($labelFirma) . $fila;

        $sheet->cells($rangoFirmaLabel, function ($cells) {
            $cells->setAlignment('center');
            $cells->setFontWeight('bold');
            $cells->setBackground('#F8FAFC');
            $cells->setBorder('solid', 'solid', 'solid', 'solid');
        });

        if ($labelFirma < $totalColumnas) {

            $colInicioFirmas = $this->numeroALetraColumna($labelFirma + 1);

            $sheet->cells("{$colInicioFirmas}{$fila}:{$ultimaColumna}{$fila}", function ($cells) {
                $cells->setBorder('solid', 'solid', 'solid', 'solid');
                $cells->setAlignment('center');
            });
        }

        $fila += 2;


        $sheet->mergeCells("A{$fila}:{$ultimaColumna}{$fila}");
        $sheet->row($fila, ['Generado el ' . date('d/m/Y H:i')]);

        $sheet->cells("A{$fila}:{$ultimaColumna}{$fila}", function ($cells) {
            $cells->setAlignment('right');
        });

        if ($llevaConcentracion) {
            $anchos = [
                'A' => 6,
                'B' => 35,
                'C' => 18,
                'D' => 15,
                'E' => 15,
                'F' => 12,
                'G' => 15
            ];
        } else {
            $anchos = [
                'A' => 6,
                'B' => 35,
                'C' => 18,
                'D' => 15,
                'E' => 12,
                'F' => 15
            ];
        }

        foreach ($anchos as $col => $ancho) {
            $sheet->setWidth($col, $ancho);
        }

        for ($i = $columnasFijas + 1; $i <= $totalColumnas; $i++) {
            $col = $this->numeroALetraColumna($i);
            $sheet->setWidth($col, 5);
        }


        if ($filaDataInicio <= $fila) {

            $colDiasInicio = $this->numeroALetraColumna($columnasFijas + 1);

            $sheet->cells("{$colDiasInicio}" . ($filaDataInicio - 1) . ":{$ultimaColumna}{$fila}", function ($cells) {
                $cells->setAlignment('center');
            });
        }
    }

     private function buscarPresentacionExcel($presentaciones, $idPresentacion)
    {
        if (!$idPresentacion) {
            return '';
        }

        return isset($presentaciones[$idPresentacion])
            ? (isset($presentaciones[$idPresentacion]->unidad) ? $presentaciones[$idPresentacion]->unidad : '')
            : '';
    }


    private function buscarConcentracionExcel($concentraciones, $idConcentracion)
    {
        if (!$idConcentracion || !$concentraciones) {
            return '';
        }

        return isset($concentraciones[$idConcentracion])
            ? (isset($concentraciones[$idConcentracion]->nombre) ? $concentraciones[$idConcentracion]->nombre : '')
            : '';
    }


    private function numeroALetraColumna($numero)
    {
        $letra = '';

        while ($numero > 0) {
            $modulo = ($numero - 1) % 26;
            $letra = chr(65 + $modulo) . $letra;
            $numero = intval(($numero - $modulo) / 26);
        }

        return $letra;
    }


     public function index_carro_paro(Request $request)
    {



        if ($request->vista) {

            return self::insumosBuscadorParo($request);
        }


        if ($request->pdf) {

            return self::pdfReporte($request);
        }

        if ($request->excel) {

            return self::excelReporte($request);
        }


        $usuarios = User::where("id_tipo_usuario", 6)->get();

        $query = ControlStockCabecera::query();

        if (!empty($request['fecha_d'])) {
            $query->whereDate('fecha', '>=', $request->fecha_d);
        }

        if (!empty($request['fecha_h'])) {
            $query->whereDate('fecha', '<=', $request->fecha_h);
        }

        if (!empty($request['responsable'])) {
            $query->where('responsable_id', $request->responsable);
        }

        if (!empty($request['opc'])) {
            $query->where('tipo', $request->opc);
        }

        if (!empty($request['servicio'])) {
            $query->where('servicio', $request->servicio);
        }

        $insumos = $query->orderBy('fecha', 'desc')
            ->where("estado", 1)
            ->paginate(15)
            ->appends($request->except('page'));


        return view("insumos/carro_paro/index", [
            'usuarios' => $usuarios,
            'insumos' => $insumos,

        ]);
    }

     public function carro_paro(Request $request)
    {


        //verificacion de registro
        $registro = ControlStockCabecera::find($request->id);
        $usuarios = User::where("id_tipo_usuario", 6)->get();


        $productos = self::obtenerStockPorTipo([2, 3, 8]);
        $productosAlertas = [
            'vencidos' => [],
            'criticos' => [],
            'proximos' => []
        ];
        $hoy = Carbon::now();
        foreach ($productos as $p) {

            if (empty($p->fecha_caducidad) || $p->fecha_caducidad == '0000-00-00') continue;

            $fecha = Carbon::parse($p->fecha_caducidad);

            if ($fecha->lt($hoy)) {
                $productosAlertas['vencidos'][] = $p;
            } elseif ($fecha->between($hoy, $hoy->copy()->addWeek())) {
                $productosAlertas['criticos'][] = $p;
            } elseif ($fecha->between($hoy, $hoy->copy()->addWeeks(3))) {
                $productosAlertas['proximos'][] = $p;
            }
        }
        return view("insumos/carro_paro/create", ['productosAlertas' => $productosAlertas, 'usuarios' => $usuarios, 'registro' => $registro]);
    }
    public function insumos_cargarTipo(Request $request)
    {
        try {
            $esFechaAnterior = !empty($request->fechaAnterior);
            $fechaRegistro   = $esFechaAnterior ? $request->fechaAnterior : $request->fecha;

            $tipo = [];
            if ($request->tipo == 1) {
                $tipo = [2];
            } elseif ($request->tipo == 2) {
                $tipo = [3, 8, 0];
            }
            $presentaciones = Presentaciones::where("estado", 1)->get();
            $concentracion  = Concentracion::where("estado", 1)->get();

            $stock = collect();
            $idControlStockCabecera = null;


            if ($request->editarId) {
                $idControlStockCabecera = ControlStockCabecera::findOrFail($request->editarId);

                if ($idControlStockCabecera->tipo !== (int) $request->tipo) {
                    $verificar = ControlStockCabecera::whereBetween('fecha', [
                        $fechaRegistro . ' 00:00:00',
                        $fechaRegistro . ' 23:59:59'
                    ])
                        ->where('tipo', $request->tipo)
                        ->where('estado', 1)
                        ->first();
                    if ($verificar) {
                        return response()->json([
                            'status'   => true,
                            'redirect' => url('carro/paro?id=' . $verificar->id),
                        ]);
                    }
                    return response()->json([
                        'status'   => true,
                        'redirect' => url('carro/paro') . '?tipo=' . $request->tipo,
                    ]);
                }


                $idCabecera = (int) $idControlStockCabecera->id;

                $stock = ControlStockDetalle::join('producto_lotes as pl', 'control_stock_detalle.producto_lote_id', '=', 'pl.id')
                    ->join(DB::raw("
                        (
                            SELECT 
                                iis.id_producto,
                                iis.lote,
                                MIN(iis.serie) as serie,
                                MIN(iis.fecha_vence) as fecha_vence
                            FROM inv_inventario_serie iis
                            INNER JOIN control_stock_detalle csd2
                                ON csd2.lote = iis.lote
                            INNER JOIN producto_lotes pl2
                                ON csd2.producto_lote_id = pl2.id
                            INNER JOIN bodega
                                ON iis.id_bodega = bodega.id
                                AND pl2.producto_id = iis.id_producto
                            WHERE bodega.nombre like '%COCHE DE PARO%'
                            AND csd2.cabecera_id = " . $idCabecera . "
                            AND csd2.estado = 1
                            GROUP BY iis.id_producto, iis.lote
                        ) as inv_inventario_serie
                    "), function ($join) {
                        $join->on('pl.producto_id', '=', 'inv_inventario_serie.id_producto')
                            ->on('control_stock_detalle.lote', '=', 'inv_inventario_serie.lote');
                    })
                    ->join('control_stock_cabecera', 'control_stock_detalle.cabecera_id', '=', 'control_stock_cabecera.id')
                    ->join('producto as p', 'pl.producto_id', '=', 'p.id')
                    ->leftJoin('presentaciones', 'pl.presentaciones_id', '=', 'presentaciones.id')
                    ->leftJoin('concentracion', 'pl.concentracion_id', '=', 'concentracion.id')
                    ->select(
                        'control_stock_cabecera.fecha as fecha_control',
                        'control_stock_cabecera.created_at',
                        'control_stock_detalle.id',
                        'control_stock_detalle.stock',
                        'control_stock_detalle.lote',
                        'inv_inventario_serie.serie',
                        'control_stock_detalle.producto_lote_id as idProductoLotes',
                        'p.id as producto_id',
                        'p.nombre as producto',
                        'p.tipo',
                        'inv_inventario_serie.fecha_vence',
                        'presentaciones.id as idPresentacion',
                        'concentracion.id as idConcetracion',
                        'control_stock_detalle.referencia_cantidad',
                        'pl.fecha_caducidad',
                        'pl.observacion'
                    )
                    ->where('control_stock_detalle.cabecera_id', $idCabecera)
                    ->where('control_stock_detalle.estado', 1)
                    ->orderByRaw('pl.fecha_caducidad IS NULL ASC')
                    ->orderBy('pl.fecha_caducidad', 'asc')
                    ->orderBy('p.nombre', 'asc')
                    ->get();
            } else {

                $verificar = ControlStockCabecera::whereBetween('fecha', [
                    $fechaRegistro . ' 00:00:00',
                    $fechaRegistro . ' 23:59:59'
                ])
                    ->where('tipo', $request->tipo)
                    ->where('estado', 1)
                    ->first();

                if ($verificar) {
                    return response()->json([
                        'status'   => true,
                        'redirect' => url('carro/paro?id=' . $verificar->id),
                    ]);
                }

                $stock = InvInventarioSerie::join('producto as p', 'inv_inventario_serie.id_producto', '=', 'p.id')
                    ->join('tipo', 'p.tipo_producto', '=', 'tipo.id')
                    ->leftJoin(DB::raw("
                        (
                            SELECT producto_id, MAX(id) AS id_producto_lote
                            FROM producto_lotes
                            WHERE estado = 1
                            GROUP BY producto_id
                        ) as pl_max
                    "), function ($join) {
                        $join->on('p.id', '=', 'pl_max.producto_id');
                    })
                    ->leftJoin('producto_lotes', 'pl_max.id_producto_lote', '=', 'producto_lotes.id')
                    ->leftJoin('presentaciones', 'producto_lotes.presentaciones_id', '=', 'presentaciones.id')
                    ->leftJoin('concentracion', 'producto_lotes.concentracion_id', '=', 'concentracion.id')
                    ->join("bodega","inv_inventario_serie.id_bodega","bodega.id")
                    ->select(
                        'p.id as producto_id',
                        'p.tipo',
                        'p.nombre as producto',
                        'inv_inventario_serie.lote',
                        'inv_inventario_serie.serie',
                        'presentaciones.id as idPresentacion',
                        'concentracion.id as idConcetracion',
                        'producto_lotes.cantidad',
                        'producto_lotes.fecha_caducidad',
                        'inv_inventario_serie.fecha_vence',
                        'producto_lotes.id as idProductoLotes',
                        'producto_lotes.observacion',
                        'inv_inventario_serie.existencia as stock_disponible'
                    )
                    ->where('bodega.nombre','like','%COCHE DE PARO%')
                    ->whereIn('p.tipo', $tipo)
                    ->where('inv_inventario_serie.estado', 1)
                    ->where('inv_inventario_serie.existencia', '>', 0)
                    ->orderByRaw('producto_lotes.fecha_caducidad IS NULL, producto_lotes.fecha_caducidad ASC')
                    ->orderBy('p.nombre', 'asc')
                    ->get();
            }

            $now       = Carbon::now();
            $dateToday = date("Ymd");
            $esEdicion = !empty($request->editarId);

            if ($esFechaAnterior) {
                $fechaId   = date('Ymd', strtotime($request->fechaAnterior));
                $diaHeader = date('d',   strtotime($request->fechaAnterior));
            } elseif ($esEdicion && $stock->isNotEmpty()) {
                $fechaId   = date('Ymd', strtotime($stock[0]->fecha_control));
                $diaHeader = date('d',   strtotime($stock[0]->fecha_control));
            } else {
                $fechaId   = Carbon::create($now->year, $now->month, $now->day)->format('Ymd');
                $diaHeader = $now->day;
            }
            $inputReadonly = ($dateToday === $fechaId || $esFechaAnterior) ? '' : 'readonly';


            $meses = [
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre'
            ];

            $mes  = $meses[date('n')];
            $anio = date('Y');
            $html = '';
            $hoy       = date('Y-m-d');
            $unaSemana = date('Y-m-d', strtotime('+7 days'));

            if ($request->tipo == 2) {
                $html = '
            <div class="table-responsive" style="height: 100vh; overflow:auto;">    
                <table class="table table-bordered table-striped table-coche-paro" style="min-width: 1600px;">
                    <thead class="table-light">     
                        <tr>
                            <th colspan="8" class="text-center">' . $mes . ' ' . $anio . '</th>
                        </tr>
                        <tr>
                            <th style="width:2.2%">ITEM</th>
                            <th style="width:17.5%">INSUMO</th>
                            <th style="width:8.8%">PRESENTACIÓN</th>
                            <th style="width:19%">LOTE</th>
                            <th style="width:19%">SERIE</th>
                            <th style="width:4.3%">CANTIDAD</th>
                            <th style="width:8.8%">FECHA CADUCIDAD</th>
                            <th style="width:13.4%">OBSERVACION</th>
                            <th style="width:7%" class="dia-col text-center">Día: ' . $diaHeader . '</th>
                        </tr>
                    </thead>
                    <tbody id="body">';

                foreach ($stock as $cont => $value) {
                    $style = '';
                        if ($value->fecha_caducidad < $hoy) {
                            $style = 'style="background-color:#f8d7da;"';
                        } elseif ($value->fecha_caducidad <= $unaSemana) {
                            $style = 'style="background-color:#fff3cd;"';
                    }

                    $valorDia = '';
                    if ($esEdicion) {
                        $fechaRegistroDetalle = !empty($value->fecha_control)
                            ? Carbon::parse($value->fecha_control)->format('Ymd')
                            : null;
                        if ($fechaRegistroDetalle === $fechaId) {
                            $valorDia = $value->stock;
                        }
                    } else {
                        $valorDia = $value->stock_disponible;
                    }

                    $html .= '
                    <input type="hidden" name="idProductoLotes[]" value="' . $value->idProductoLotes . '" />
                    <tr ' . $style . '>
                        <td class="text-center">' . ($cont + 1) . '</td>
                        <td>
                            <input type="hidden" name="lote[]" value="' . $value->lote . '" />
                            <input type="hidden" name="idProducto[]" value="' . $value->producto_id . '">
                            <span class="producto-nombre">' . $value->producto . '</span>
                        </td>
                        <td>
                            <select name="presentacion[]" class="form-control select2">
                                <option value="">--Seleccione--</option>';

                    foreach ($presentaciones as $val) {
                        $selected     = ($val->id == $value->idPresentacion) ? 'selected' : '';
                        $textoMostrar = ($val->nombre == null) ? $val->unidad : $val->nombre;
                        $html .= '<option ' . $selected . ' value="' . $val->id . '">' . $textoMostrar . '</option>';
                    }

                    $html .= '
                            </select>
                        </td>
                        <td><input type="text" readonly name="lote_m[]" value="' . $value->lote . '" class="form-control"></td>
                        <td><input type="text" readonly name="serie[]" value="' . $value->serie . '" class="form-control"></td>
                        <td><input type="number" readonly name="cantidad[]" value="' . ($esEdicion ? $value->referencia_cantidad : $value->stock_disponible) . '" class="form-control" min="0"></td>
                        <td><input type="date" name="fecha_caducidad[]" value="' . $value->fecha_caducidad . '" class="form-control"></td>
                        <td><textarea name="observacion[]" class="form-control" rows="1">' . $value->observacion . '</textarea></td>
                        <td class="text-center">
                            <input 
                                type="number"
                                onchange="validarMinimo(' . $fechaId . ',' . $value->producto_id . ',\'' . $value->lote . '\',this)"
                                value="' . $valorDia . '"
                                ' . $inputReadonly . '
                                class="form-control text-center p-0 ' . $fechaId . '"
                                style="width:80px; height:30px;"
                                name="contador[' . $cont . '][]" />
                        </td>
                    </tr>';
                }

                $html .= '</tbody></table></div>';
            }

            if ($request->tipo == 1) {
                $html = '
            <div class="table-responsive" style="height: 100vh; overflow:auto;">    
                <table class="table table-bordered table-striped table-coche-paro" style="min-width: 1600px;">
                    <thead class="table-light">
                        <tr>
                            <th colspan="9" class="text-center">' . $mes . ' ' . $anio . '</th>
                        </tr>
                        <tr>
                            <th style="width:2.2%">ITEM</th>
                            <th style="width:17%">INSUMO</th>
                            <th style="width:15%">LOTE</th>
                            <th style="width:15%">SERIE</th>
                            <th style="width:9%">PRESENTACIÓN</th>
                            <th style="width:9%">CONCENTRACIÓN</th>
                            <th style="width:4%">CANTIDAD</th>
                            <th style="width:12.8%">FECHA CADUCIDAD</th>
                            <th style="width:6%" class="dia-col text-center">Día: ' . $diaHeader . '</th>
                        </tr>
                    </thead>
                    <tbody id="body">';

                foreach ($stock as $cont => $value) {
                    $style = '';
                    
                    if(!empty($value->fecha_caducidad)){
                        if ($value->fecha_caducidad < $hoy) {
                            $style = 'style="background-color:#f8d7da;"';
                        } elseif ($value->fecha_caducidad <= $unaSemana) {
                            $style = 'style="background-color:#fff3cd;"';
                        }
                    }

                    $valorDia = '';
                    if ($esEdicion) {

                        $fechaRegistroDetalle = !empty($value->fecha_control)
                            ? Carbon::parse($value->fecha_control)->format('Ymd')
                            : null;
                        if ($fechaRegistroDetalle === $fechaId) {
                            $valorDia = $value->stock;
                        }
                    } else {
                        $valorDia = $value->stock_disponible;
                    }

                    $html .= '
                    <input type="hidden" name="idProductoLotes[]" value="' . $value->idProductoLotes . '" />
                    <tr ' . $style . '>
                        <td class="text-center">' . ($cont + 1) . '</td>
                        <td>
                            <input type="hidden" name="idProducto[]" value="' . $value->producto_id . '">
                            <input type="hidden" name="lote[]" value="' . $value->lote . '" />
                            <span class="producto-nombre">' . $value->producto . '</span>
                        </td>
                        <td><input type="text" readonly name="lote_m[]" value="' . $value->lote . '" class="form-control"></td>
                        <td><input type="text" readonly name="serie[]" value="' . $value->serie . '" class="form-control"></td>
                        <td>
                            <select name="presentacion[]" class="form-control select2">
                                <option value="">--Seleccione--</option>';

                    foreach ($presentaciones as $val) {
                        $selected = ($val->id == $value->idPresentacion) ? 'selected' : '';
                        $html .= '<option ' . $selected . ' value="' . $val->id . '">' . $val->unidad . '</option>';
                    }

                    $html .= '
                            </select>
                        </td>
                        <td>
                            <select name="concentracion[]" class="form-control select2">
                                <option value="">--Seleccione--</option>';

                    foreach ($concentracion as $val) {
                        $selected = ($val->id == $value->idConcetracion) ? 'selected' : '';
                        $html .= '<option ' . $selected . ' value="' . $val->id . '">' . $val->nombre . '</option>';
                    }

                    $html .= '
                            </select>
                        </td>
                        <td><input type="number" readonly name="cantidad[]" value="' . ($esEdicion ? $value->referencia_cantidad : $value->stock_disponible) . '" class="form-control" min="0"></td>
                        <td><input type="date" name="fecha_caducidad[]" value="' . $value->fecha_caducidad . '" class="form-control"></td>
                        <td class="text-center">
                            <input 
                                type="number"
                                onchange="validarMinimo(' . $fechaId . ',' . $value->producto_id . ',\'' . $value->lote . '\',this)"
                                value="' . $valorDia . '"
                                ' . $inputReadonly . '
                                class="form-control text-center p-0 ' . $fechaId . '"
                                style="width:80px; height:30px;"
                                name="contador[' . $cont . '][]" />
                        </td>
                    </tr>';
                }

                $html .= '</tbody></table></div>';
            }

            return response()->json([
                'status' => true,
                'html'   => $html,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage(),
                'line'   => $e->getLine(),
            ]);
        }
    }

      public function insumos_guardarForm(Request $request)
    {

        try {
            parse_str($request->form, $data);
            $cabeceraId = $data["editarId"] ?? null;

            if (!$cabeceraId) {
                $existe = ControlStockCabecera::whereBetween('fecha', [
                    $data["fecha"] . ' 00:00:00',
                    $data["fecha"] . ' 23:59:59'
                ])->where("tipo", $data["opc"])
                    ->where("estado", 1)->exists();

                if ($existe) {
                    return response()->json([
                        'status' => false,
                        'error' => "Ya existe un registro para el día de hoy.",
                        'line' => "",
                    ]);
                }
            }
            $id = null;
            DB::transaction(function () use ($data, $cabeceraId, &$id) {

                $fechaSeleccionada = !empty($data["fecha"]) ? Carbon::parse($data["fecha"]) : Carbon::now();
                $dateDay = (int)$fechaSeleccionada->format("d");



                if (!empty($cabeceraId)) {
                    $t = ControlStockCabecera::where('id', $cabeceraId)->firstOrFail();
                    $t->fecha = $data["fecha"] ?? null;
                    $t->servicio = $data["servicio"] ?? null;
                    $t->responsable_id = $data["responsable"] ?? null;
                    $t->tipo = $data["opc"] ?? null;
                    $t->estado = 1;
                    $t->id_usuariomod = Auth::user()->id;
                    $t->save();

                    $id = $cabeceraId;

                    // eliminar detalles anteriores para volver a crearlos
                    ControlStockDetalle::where('cabecera_id', $cabeceraId)->delete();
                } else {

                    $id = ControlStockCabecera::insertGetId([
                        'fecha'          => $data["fecha"] ?? null,
                        'servicio'       => $data["servicio"] ?? null,
                        'responsable_id' => $data["responsable"] ?? null,
                        'tipo'           => $data["opc"] ?? null,
                        'estado'         => 1,
                        'id_usuariocrea'     => Auth::user()->id,
                        'id_usuariomod'      => Auth::user()->id,
                    ]);
                }

                foreach ($data["idProducto"] as $r => $value) {
                    $productoLoteId = $data["idProductoLotes"][$r] ?? null;
                    $stock = (int)($data['contador'][$r][0] ?? 0);

                    if (empty($productoLoteId)) {
                        $productoLoteId = Producto_Lotes::insertGetId([
                            'producto_id'        => $data["idProducto"][$r] ?? null,
                            'presentaciones_id'  => !empty($data['presentacion'][$r]) ? $data['presentacion'][$r] : null,
                            'concentracion_id'   => !empty($data['concentracion'][$r]) ? $data['concentracion'][$r] : null,
                            'cantidad'           => $stock,
                            'fecha_caducidad'    => $data['fecha_caducidad'][$r] ?? null,
                            'observacion'        => $data['observacion'][$r] ?? null,
                            'tipo'               => $data["opc"] ?? null,
                            'id_usuariocrea'     => Auth::user()->id,
                            'id_usuariomod'      => Auth::user()->id,
                            'estado'             => 1,
                        ]);
                    } else {
                        $p = Producto_Lotes::where('id', $productoLoteId)->first();

                        if ($p) {
                            $p->producto_id = $data["idProducto"][$r] ?? null;
                            $p->presentaciones_id = !empty($data['presentacion'][$r]) ? $data['presentacion'][$r] : null;
                            $p->concentracion_id = !empty($data['concentracion'][$r]) ? $data['concentracion'][$r] : null;
                            $p->cantidad = $stock;
                            $p->fecha_caducidad = $data['fecha_caducidad'][$r] ?? null;
                            $p->observacion = $data['observacion'][$r] ?? null;
                            $p->tipo = $data["opc"] ?? null;
                            $p->estado = 1;
                            $p->id_usuariomod = Auth::user()->id;
                            $p->save();
                        }
                    }
                    ControlStockDetalle::insert([
                        'cabecera_id'          => $id,
                        'producto_lote_id'     => $productoLoteId,
                        'stock'                => $stock,
                        'lote'                 => $data["lote"][$r] ?? null,
                        'referencia_cantidad'  => $data["cantidad"][$r] ?? 0, //viene de inv_inventario serie
                        'estado'               => 1,
                        'id_usuariocrea'        => Auth::user()->id,
                        'id_usuariomod'         => Auth::user()->id,
                    ]);
                }
            });
            return response()->json([
                'status' => true,
                'message' => ($cabeceraId ? 'Editado ' : 'Guardado ') . 'Correctamente',
                'id' => $id,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }
    

     public function insumos_cambiarEstado(Request $request)
    {

        try {

            $idCabecera = ControlStockCabecera::find($request->id);
            $idCabecera->estado = 0;
            $idCabecera->save();
            return response()->json([
                'status' => true,
                'message' => 'Anulado correctamente',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }

     public static function obtenerStockPorTipo(array $tipo)
    {
        return InvInventarioSerie::join('producto as p', 'inv_inventario_serie.id_producto', '=', 'p.id')
            ->join('tipo', 'p.tipo_producto', '=', 'tipo.id')
            ->leftJoin(DB::raw("
            (
                SELECT producto_id, MAX(id) AS id_producto_lote
                FROM producto_lotes
                WHERE estado = 1
                GROUP BY producto_id
            ) as pl_max
        "), function ($join) {
                $join->on('p.id', '=', 'pl_max.producto_id');
            })
            ->leftJoin('producto_lotes', 'pl_max.id_producto_lote', '=', 'producto_lotes.id')
            ->leftJoin('presentaciones', 'producto_lotes.presentaciones_id', '=', 'presentaciones.id')
            ->leftJoin('concentracion', 'producto_lotes.concentracion_id', '=', 'concentracion.id')
            ->join("bodega","inv_inventario_serie.id_bodega","bodega.id")
            ->select(
                'p.id as producto_id',
                'p.tipo',
                'p.nombre as producto',
                'inv_inventario_serie.lote',
                'presentaciones.id as idPresentacion',
                'concentracion.id as idConcetracion',
                'producto_lotes.cantidad',
                'producto_lotes.fecha_caducidad',
                'inv_inventario_serie.fecha_vence',
                'producto_lotes.id as idProductoLotes',
                'producto_lotes.observacion',
                'inv_inventario_serie.existencia as stock_disponible'
            )
            ->where('bodega.nombre', 'like', '%COCHE DE PARO%')
            ->whereIn('p.tipo', $tipo)
            ->where('inv_inventario_serie.estado', 1)
            ->where('inv_inventario_serie.existencia', '>', 0)
            ->orderBy('producto_lotes.fecha_caducidad', 'asc')
            ->orderBy('p.nombre', 'asc')
            ->get();
    }

      public static function insumosBuscadorParo(Request $request)
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        if ($request->vista) {
            $array[] = self::obtenerStockPorTipo([2]);
            $array[] = self::obtenerStockPorTipo([3, 8, 0]);
            $responsable = User::find($request->responsable);
            $insumos = ControlStockDetalle::whereBetween('created_at', [
                $request->fecha_d . ' 00:00:00',
                $request->fecha_h . ' 23:59:59'
            ])->get();
            $presentaciones = Presentaciones::where("estado", 1)->get();
            $concentracion = Concentracion::where("estado", 1)->get();
            $fecha = $meses[Carbon::parse($request->fecha_d)->month];
            $anio = Carbon::parse($request->fecha_d)->year;
            return view("insumos/carro_paro/view_coche_paro", ["insumos" => $insumos, 'servicio' => $request->servicio, 'opc' => $request->opc, 'responsable' => $responsable, 'mesNombre' => $fecha, 'anio' => $anio, "stock" => $array, 'presentaciones' => $presentaciones, 'concentracion' => $concentracion]);
        }

        $insumos = [];
        return view("insumos/carro_paro/view_coche_paro", ["insumos" => $insumos]);
    }


     public function consultar_uso(Request $request)
    {
        try {
            $query = ControlStockCabecera::whereBetween('created_at', [
                $request->fecha_desde . ' 00:00:00' ?? date('Y-m-d') . ' 00:00:00',
                $request->fecha_hasta . ' 23:59:59' ?? date('Y-m-d') . ' 23:59:59'
            ])->get();
            $result = [];
            foreach ($query as $val) {
                if (!$val->detalle) {
                    continue;
                }

                foreach ($val->detalle as $value) {

                    if (!$value->producto_lote || !$value->producto_lote->producto || !$val->usuario) {
                        continue;
                    }

                    $referenciaCantidad = (float) $value->referencia_cantidad;
                    $stockActual = (float) $value->stock;
                    $cantidadUsada = $referenciaCantidad - $stockActual;

                    if ($cantidadUsada <= 0) {
                        continue;
                    }
                    $result[] = [
                        'id_producto' => $value->producto_lote->producto->id,
                        'nombre'      => $value->producto_lote->producto->nombre,
                        'fecha'       => $val->fecha,
                        'responsable' => trim(
                            ($val->usuario->nombre1 ?? '') . ' ' .
                                ($val->usuario->apellido1 ?? '') . ' ' .
                                ($val->usuario->apellido2 ?? '')
                        ),
                        'referencia_cantidad' => $value->referencia_cantidad,
                        'stock'               => $value->stock,
                        'cantidad_usada'      =>  $value->referencia_cantidad - (float)$value->stock,
                        'tipo'                => (int)$val->tipo == 1 ? 'Medicamento' : 'Insumo',
                    ];
                }
            }


            return response()->json([

                'status' => true,
                'html' => $result,
                'message' => '',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }

    public function validar_stock(Request $request)
    {

        try {


            $consultar = InvInventarioSerie::join("bodega","inv_inventario_serie.id_bodega","bodega.id")
                ->where("inv_inventario_serie.id_producto", $request->id_producto)
                ->where('bodega.nombre', 'like', '%COCHE DE PARO%')
                ->where("inv_inventario_serie.estado", 1)
                ->where("inv_inventario_serie.lote", $request->lote)
                ->first(['inv_inventario_serie.existencia']);
            if ((int)$consultar->existencia < (int)$request->cantidad) {
                return response()->json([
                    'status' => false,
                    'error' => 'Cantidad superior al stock',
                    'line' => '',
                ]);
            }
            return response()->json([
                'status' => true,
                'error' => '',
                'line' => '',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }

    public function registro_anterior(Request $request)
    {
        try {
            $registro = ControlStockCabecera::whereBetween('fecha', [
                $request->fecha . ' 00:00:00',
                $request->fecha . ' 23:59:59'
            ])->where("tipo", $request->tipo)
                ->where("estado", 1)
                ->first();
            if ($registro) {
                return response()->json([
                    'status'   => true,
                    'redirect' => url('carro/paro?id=' . $registro->id),
                ]);
            }

            return response()->json([
                'status' => true,
                'redirect' => url('carro/paro') . '?tipo=' . $request->tipo . '&fecha=' . $request->fecha,

            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }
}
