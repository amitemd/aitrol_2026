<?php

namespace Sis_medico\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Response;
use Sis_medico\Empresa;
use Sis_medico\InvDetMovimientos;
use Sis_medico\MenuUsuario;

class MenuController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
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


    public function menu_usuario(Request $request)
    {
        Db::beginTransaction();
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $id_empresa = Session::get('id_empresa');
            $tipo = $request->tipo;
            $foto = asset('/avatars/avatar.jpg');

            $menu = MenuUsuario::where('id_usuario', $idusuario)->where('id_empresa', $id_empresa)->where('url', $request->url)->first();

            if (is_null($menu)) {
                $arr_crea = [
                    'id_usuario'        => $idusuario,
                    'id_empresa'        => $id_empresa,
                    'titulo'            => $request->titulo,
                    'url'               => $request->url,
                    'imagen'            => $foto,
                    'descripcion'       => $request->descripcion,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ];
                $id_usuario_menu = MenuUsuario::insertGetId($arr_crea);
            } else {
                $arr_up = [
                    'estado'            => 1,
                    'titulo'            => $request->titulo,
                    'url'               => $request->url,
                    'imagen'            => $foto,
                    'id_usuariomod'     => $idusuario,
                    'ip_modificacion'   => $ip_cliente,
                ];

                $menu->update($arr_up);
                $id_usuario_menu = $menu->id;
            }

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'id' => $id_usuario_menu];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }

    public function eliminar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        Db::beginTransaction();
        try {
            $menu_usuario  = MenuUsuario::find($request->id);
            if (!is_null($menu_usuario)) {
                $arr_up = [
                    'estado'            => 0,
                    'id_usuariomod'     => $idusuario,
                    'ip_modificacion'   => $ip_cliente,
                ];
                $menu_usuario->update($arr_up);
            }
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Eliminado con Exito'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => 'Error al guardar', 'exp' => $e->getMessage()];
        }
    }

    public function buscadorTarget(Request $request)
    {
        // dd($request->all());
        $titulo = MenuUsuario::where('titulo', 'like', "%{$request->buscador}%")->where('id_usuario', Auth::user()->id)->where('id_empresa', Session::get("id_empresa"))->where("estado", 1);
        $descripcion = MenuUsuario::where('descripcion', 'like', "%{$request->buscador}%")->where('id_usuario', Auth::user()->id)->where('id_empresa', Session::get("id_empresa"))->where("estado", 1);
        $buscador = $titulo->union($descripcion)->get();
        // dd($buscador);
        $targets = "";

        if($request->tipo == 2){
            foreach($buscador as $value){
              $targets .= "<a class='container-clip enlaces_directos' href='{$value->url}'>{$value->descripcion} </a>";
            }

            return ["targets" => $targets];

        }
        
        foreach ($buscador as $value) {
            $targets .= "
                    <div class=''>
                    <section class='page-contain'>
                    <div class='data-card contendor-card'>
                        <h3 class='titulo'> {$value->titulo}</h3>
                        <h4>{$value->descripcion}</h4>
                        <span class='link-text' style='display: flex;justify-content: space-evenly;'>
                            <a class='enlace' href='{$value->url}'>Ir <i class='fa fa-long-arrow-right' aria-hidden='true'></i></a>
                            <button onclick='eliminarTarget({$value->id})' class='enlace-delete' >
                                <i class='fa fa-trash-o' aria-hidden='true'></i>
                            </button>
                        </span>
                    </div>
                    </section>
                </div>";
        }

        return ["targets" => $targets];
    }

    public function index_caducados(Request $request)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);

        $stock = DB::select("SELECT * FROM producto p WHERE p.cantidad < p.minimo");

        /*
        |--------------------------------------------------------------------------
        | Filtro por botón
        |--------------------------------------------------------------------------
        | valores permitidos:
        | todos
        | vigentes
        | caducados
        | usar_primero
        | usar_segundo
        | usar_tercero
        | por_vencer
        */
        $filtro_estado = $request->get('estado', 'todos');
        $busqueda = trim($request->get('buscar', ''));

        $prioridadCase = "
            CASE
                WHEN iis.fecha_vence < CURDATE() THEN 'CADUCADO'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 4 MONTH) THEN 'CADUCA EN 4 MESES'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 8 MONTH) THEN 'CADUCA EN 8 MESES'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 12 MONTH) THEN 'CADUCA EN 12 MESES'
                ELSE 'VIGENTE'
            END
        ";

        $colorCase = "
            CASE
                WHEN iis.fecha_vence < CURDATE() THEN 'caducado'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 4 MONTH) THEN 'usar_primero'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 8 MONTH) THEN 'usar_segundo'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 12 MONTH) THEN 'usar_tercero'
                ELSE 'vigente'
            END
        ";

        /*
        |--------------------------------------------------------------------------
        | Query base
        |--------------------------------------------------------------------------
        */
        $baseQuery = function () use ($id_empresa) {
            return DB::table('inv_inventario_serie as iis')
                ->join('inv_inventario as ii', 'iis.id_inv_inventario', '=', 'ii.id')
                ->join('producto as p', 'ii.id_producto', '=', 'p.id')
                ->join('inv_cab_movimientos as icm', 'iis.id_cab_movimientos', '=', 'icm.id')
                ->join('tipo as t', 'p.tipo_producto', '=', 't.id')
                ->leftJoin('pedido as pe', 'icm.id_pedido', '=', 'pe.id')
                ->leftJoin('proveedor as pr', 'pr.id', '=', 'pe.id_proveedor')
                ->leftJoin('bodega as b', 'b.id', '=', 'iis.id_bodega')
                ->join('marca as m', 'p.id_marca', '=', 'm.id')
                ->where('ii.id_empresa', $id_empresa)
                ->where('iis.existencia', '>=', 1)
                ->where('iis.estado', 1)
                ->where('ii.estado', 1)
                ->where('p.estado', 1);
        };

        /*
        |--------------------------------------------------------------------------
        | Resumen para tarjetas y gráfico
        |--------------------------------------------------------------------------
        */
        $resumenDb = $baseQuery()
            ->selectRaw("$prioridadCase AS prioridad_uso")
            ->selectRaw("COUNT(*) AS total")
            ->groupBy(DB::raw($prioridadCase))
            ->pluck('total', 'prioridad_uso')
            ->toArray();

        $resumen = [
            'VIGENTE' => (int)($resumenDb['VIGENTE'] ?? 0),
            'CADUCADO' => (int)($resumenDb['CADUCADO'] ?? 0),
            'CADUCA EN 4 MESES' => (int)($resumenDb['CADUCA EN 4 MESES'] ?? 0),
            'CADUCA EN 8 MESES' => (int)($resumenDb['CADUCA EN 8 MESES'] ?? 0),
            'CADUCA EN 12 MESES' => (int)($resumenDb['CADUCA EN 12 MESES'] ?? 0),
        ];

        $total_registros = array_sum($resumen);

        $porcentaje = function ($cantidad) use ($total_registros) {
            if ($total_registros <= 0) {
                return 0;
            }

            return round(($cantidad * 100) / $total_registros, 1);
        };

        $cards = [
            [
                'key' => 'vigentes',
                'label' => 'Vigentes',
                'valor' => $resumen['VIGENTE'],
                'porcentaje' => $porcentaje($resumen['VIGENTE']),
                'class' => 'vigente',
                'icon' => 'fa-check-circle',
            ],
            [
                'key' => 'caducados',
                'label' => 'Caducados',
                'valor' => $resumen['CADUCADO'],
                'porcentaje' => $porcentaje($resumen['CADUCADO']),
                'class' => 'caducado',
                'icon' => 'fa-calendar-times-o',
            ],
            [
                'key' => 'usar_primero',
                'label' => 'Caduca en 4 meses',
                'valor' => $resumen['CADUCA EN 4 MESES'],
                'porcentaje' => $porcentaje($resumen['CADUCA EN 4 MESES']),
                'class' => 'usar_primero',
                'icon' => 'fa-arrow-up',
            ],
            [
                'key' => 'usar_segundo',
                'label' => 'Caduca en 8 meses',
                'valor' => $resumen['CADUCA EN 8 MESES'],
                'porcentaje' => $porcentaje($resumen['CADUCA EN 8 MESES']),
                'class' => 'usar_segundo',
                'icon' => 'fa-angle-double-right',
            ],
            [
                'key' => 'usar_tercero',
                'label' => 'Caduca en 12 meses',
                'valor' => $resumen['CADUCA EN 12 MESES'],
                'porcentaje' => $porcentaje($resumen['CADUCA EN 12 MESES']),
                'class' => 'usar_tercero',
                'icon' => 'fa-angle-double-right',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Listado principal filtrado
        |--------------------------------------------------------------------------
        */
        $query = $baseQuery()
            ->select([
                'p.codigo',
                'p.nombre',
                DB::raw('IFNULL(icm.id_pedido, icm.observacion) AS pedido_detalle'),
                'iis.serie',
                'b.nombre as bodega',
                'pr.razonsocial as proveedor',
                'm.nombre as marca',
                'iis.existencia',
                'iis.fecha_vence',
                'ii.costo_promedio',
                't.nombre as tipo',
            ])
            ->selectRaw("$prioridadCase AS prioridad_uso")
            ->selectRaw("$colorCase AS color_class");

        /*
        |--------------------------------------------------------------------------
        | Filtro por estado
        |--------------------------------------------------------------------------
        */
        if ($filtro_estado !== 'todos') {
            if ($filtro_estado === 'vigentes') {
                $query->whereRaw("($prioridadCase) = ?", ['VIGENTE']);
            }

            if ($filtro_estado === 'caducados') {
                $query->whereRaw("($prioridadCase) = ?", ['CADUCADO']);
            }

            if ($filtro_estado === 'usar_primero') {
                $query->whereRaw("($prioridadCase) = ?", ['CADUCA EN 4 MESES']);
            }

            if ($filtro_estado === 'usar_segundo') {
                $query->whereRaw("($prioridadCase) = ?", ['CADUCA EN 8 MESES']);
            }

            if ($filtro_estado === 'usar_tercero') {
                $query->whereRaw("($prioridadCase) = ?", ['CADUCA EN 12 MESES']);
            }

            if ($filtro_estado === 'por_vencer') {
                $query->where('iis.fecha_vence', '>=', DB::raw('CURDATE()'))
                    ->where('iis.fecha_vence', '<=', DB::raw('DATE_ADD(CURDATE(), INTERVAL 30 DAY)'));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Buscador general por cualquier campo
        |--------------------------------------------------------------------------
        */
        if ($busqueda !== '') {
            $query->where(function ($q) use ($busqueda, $prioridadCase) {
                $like = '%' . $busqueda . '%';

                $q->where('p.codigo', 'LIKE', $like)
                    ->orWhere('p.nombre', 'LIKE', $like)
                    ->orWhere('iis.serie', 'LIKE', $like)
                    ->orWhereRaw("IFNULL(icm.id_pedido, icm.observacion) LIKE ?", [$like])
                    ->orWhere('b.nombre', 'LIKE', $like)
                    ->orWhere('pr.razonsocial', 'LIKE', $like)
                    ->orWhere('m.nombre', 'LIKE', $like)
                    ->orWhere('t.nombre', 'LIKE', $like)
                    ->orWhere('iis.existencia', 'LIKE', $like)
                    ->orWhere('iis.fecha_vence', 'LIKE', $like)
                    ->orWhereRaw("($prioridadCase) LIKE ?", [$like]);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Total de cantidad según filtro/búsqueda actual
        |--------------------------------------------------------------------------
        | Se calcula antes de paginar para que el total represente todos los
        | registros filtrados, no solo los 15 visibles en la página actual.
        */
        $total_cantidad_filtrada = (clone $query)->sum('iis.existencia');

        $caducado = $query
            ->orderByRaw("
                CASE
                    WHEN ($prioridadCase) = 'CADUCADO' THEN 1
                    WHEN ($prioridadCase) = 'CADUCA EN 4 MESES' THEN 2
                    WHEN ($prioridadCase) = 'CADUCA EN 8 MESES' THEN 3
                    WHEN ($prioridadCase) = 'CADUCA EN 12 MESES' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('iis.fecha_vence', 'asc')
            ->orderBy('p.nombre', 'asc')
            ->paginate(15)
            ->appends($request->query());

        $inventario = DB::select("
            SELECT *
            FROM inv_inventario i
            JOIN producto p ON i.id_producto = p.id
            WHERE i.existencia < i.existencia_min
            GROUP BY id_producto
        ");

        $fecha_hoy = date('Y-m-d');

        return view('caducados', [
            'caducado' => $caducado,
            'inventario' => $inventario,
            'fecha_hoy' => $fecha_hoy,
            'stock' => $stock,
            'empresa' => $empresa,
            'cards' => $cards,
            'resumen' => $resumen,
            'total_registros' => $total_registros,
            'filtro_estado' => $filtro_estado,
            'busqueda' => $busqueda,
            'total_cantidad_filtrada' => $total_cantidad_filtrada,
        ]);
    }


    public function autocomplete_caducados(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $termino = trim($request->get('term', ''));

        if ($termino === '') {
            return response()->json([]);
        }

        $prioridadCase = "
            CASE
                WHEN iis.fecha_vence < CURDATE() THEN 'CADUCADO'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 4 MONTH) THEN 'CADUCA EN 4 MESES'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 8 MONTH) THEN 'CADUCA EN 8 MESES'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 12 MONTH) THEN 'CADUCA EN 12 MESES'
                ELSE 'VIGENTE'
            END
        ";

        $like = '%' . $termino . '%';

        $registros = DB::table('inv_inventario_serie as iis')
            ->join('inv_inventario as ii', 'iis.id_inv_inventario', '=', 'ii.id')
            ->join('producto as p', 'ii.id_producto', '=', 'p.id')
            ->join('inv_cab_movimientos as icm', 'iis.id_cab_movimientos', '=', 'icm.id')
            ->join('tipo as t', 'p.tipo_producto', '=', 't.id')
            ->leftJoin('pedido as pe', 'icm.id_pedido', '=', 'pe.id')
            ->leftJoin('proveedor as pr', 'pr.id', '=', 'pe.id_proveedor')
            ->leftJoin('bodega as b', 'b.id', '=', 'iis.id_bodega')
            ->join('marca as m', 'p.id_marca', '=', 'm.id')
            ->where('ii.id_empresa', $id_empresa)
            ->where('iis.existencia', '>=', 1)
            ->where('iis.estado', 1)
            ->where('ii.estado', 1)
            ->where('p.estado', 1)
            ->where(function ($q) use ($like, $prioridadCase) {
                $q->where('p.codigo', 'LIKE', $like)
                    ->orWhere('p.nombre', 'LIKE', $like)
                    ->orWhere('iis.serie', 'LIKE', $like)
                    ->orWhereRaw("IFNULL(icm.id_pedido, icm.observacion) LIKE ?", [$like])
                    ->orWhere('b.nombre', 'LIKE', $like)
                    ->orWhere('pr.razonsocial', 'LIKE', $like)
                    ->orWhere('m.nombre', 'LIKE', $like)
                    ->orWhere('t.nombre', 'LIKE', $like)
                    ->orWhere('iis.fecha_vence', 'LIKE', $like)
                    ->orWhereRaw("($prioridadCase) LIKE ?", [$like]);
            })
            ->select([
                'p.codigo',
                'p.nombre',
                'iis.serie',
                DB::raw('IFNULL(icm.id_pedido, icm.observacion) AS pedido_detalle'),
                'b.nombre as bodega',
                'pr.razonsocial as proveedor',
                'm.nombre as marca',
                't.nombre as tipo',
                'iis.fecha_vence',
                DB::raw("$prioridadCase AS prioridad_uso"),
            ])
            ->limit(30)
            ->get();

        $sugerencias = [];

        foreach ($registros as $item) {
            $campos = [
                $item->codigo,
                $item->nombre,
                $item->serie,
                $item->pedido_detalle,
                $item->bodega,
                $item->proveedor,
                $item->marca,
                $item->tipo,
                $item->fecha_vence,
                $item->prioridad_uso,
            ];

            foreach ($campos as $campo) {
                if ($campo !== null && stripos((string)$campo, $termino) !== false) {
                    $sugerencias[] = [
                        'label' => (string)$campo,
                        'value' => (string)$campo,
                    ];
                }
            }
        }

        $sugerencias = collect($sugerencias)
            ->unique('value')
            ->values()
            ->take(15);

        return response()->json($sugerencias);
    }


    public function estadisticas_caducados_producto(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $codigos = $request->get('codigos', []);

        if (!is_array($codigos)) {
            $codigos = explode(',', $codigos);
        }

        $codigos = array_values(array_unique(array_filter(array_map(function ($codigo) {
            return trim($codigo);
        }, $codigos))));

        if (count($codigos) == 0) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No se recibieron códigos para consultar.',
                'productos' => [],
                'resumen' => [
                    'total_productos' => 0,
                    'stock_total' => 0,
                    'proximo_vencer' => 0,
                    'caducados' => 0,
                    'porcentaje_proximo' => 0,
                    'porcentaje_caducados' => 0,
                ],
                'estados' => [],
            ]);
        }

        $prioridadCase = "
            CASE
                WHEN iis.fecha_vence < CURDATE() THEN 'CADUCADO'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 4 MONTH) THEN 'CADUCA EN 4 MESES'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 8 MONTH) THEN 'CADUCA EN 8 MESES'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 12 MONTH) THEN 'CADUCA EN 12 MESES'
                ELSE 'VIGENTE'
            END
        ";

        $colorCase = "
            CASE
                WHEN iis.fecha_vence < CURDATE() THEN 'caducado'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 4 MONTH) THEN 'usar_primero'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 8 MONTH) THEN 'usar_segundo'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 12 MONTH) THEN 'usar_tercero'
                ELSE 'vigente'
            END
        ";

        $registros = DB::table('inv_inventario_serie as iis')
            ->join('inv_inventario as ii', 'iis.id_inv_inventario', '=', 'ii.id')
            ->join('producto as p', 'ii.id_producto', '=', 'p.id')
            ->join('inv_cab_movimientos as icm', 'iis.id_cab_movimientos', '=', 'icm.id')
            ->join('tipo as t', 'p.tipo_producto', '=', 't.id')
            ->leftJoin('pedido as pe', 'icm.id_pedido', '=', 'pe.id')
            ->leftJoin('proveedor as pr', 'pr.id', '=', 'pe.id_proveedor')
            ->leftJoin('bodega as b', 'b.id', '=', 'iis.id_bodega')
            ->join('marca as m', 'p.id_marca', '=', 'm.id')
            ->where('ii.id_empresa', $id_empresa)
            ->whereIn('p.codigo', $codigos)
            ->where('iis.existencia', '>=', 1)
            ->where('iis.estado', 1)
            ->where('ii.estado', 1)
            ->where('p.estado', 1)
            ->select([
                'p.codigo',
                'p.nombre',
                'iis.existencia',
                'iis.fecha_vence',
                'b.nombre as bodega',
                'pr.razonsocial as proveedor',
                'm.nombre as marca',
                't.nombre as tipo',
            ])
            ->selectRaw("$prioridadCase AS prioridad_uso")
            ->selectRaw("$colorCase AS color_class")
            ->orderBy('p.codigo', 'asc')
            ->orderBy('iis.fecha_vence', 'asc')
            ->get();

        if ($registros->count() == 0) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No se encontraron registros activos para los códigos seleccionados.',
                'productos' => [],
                'resumen' => [
                    'total_productos' => 0,
                    'stock_total' => 0,
                    'proximo_vencer' => 0,
                    'caducados' => 0,
                    'porcentaje_proximo' => 0,
                    'porcentaje_caducados' => 0,
                ],
                'estados' => [],
            ]);
        }

        $ordenEstado = [
            'caducado' => 1,
            'usar_primero' => 2,
            'usar_segundo' => 3,
            'usar_tercero' => 4,
            'vigente' => 5,
        ];

        $estadoTexto = [
            'vigente' => 'VIGENTE',
            'caducado' => 'CADUCADO',
            'usar_primero' => 'CADUCA EN 4 MESES',
            'usar_segundo' => 'CADUCA EN 8 MESES',
            'usar_tercero' => 'CADUCA EN 12 MESES',
        ];

        $stockTotal = (int)$registros->sum('existencia');

        $proximoVencer = (int)$registros->filter(function ($item) {
            return in_array($item->color_class, ['usar_primero', 'usar_segundo', 'usar_tercero']);
        })->sum('existencia');

        $caducados = (int)$registros->filter(function ($item) {
            return $item->color_class == 'caducado';
        })->sum('existencia');

        $productos = [];
        $hoy = \Carbon\Carbon::now()->startOfDay();

        foreach ($registros->groupBy('codigo') as $codigo => $items) {
            $stock = (int)$items->sum('existencia');

            $peor = $items->sortBy(function ($item) use ($ordenEstado) {
                return isset($ordenEstado[$item->color_class]) ? $ordenEstado[$item->color_class] : 9;
            })->first();

            /*
            |--------------------------------------------------------------------------
            | CORRECCIÓN RESUMEN MODAL
            |--------------------------------------------------------------------------
            | Antes el resumen por producto tomaba el peor estado del código y pintaba
            | todo el stock como CADUCADO. Si el código tiene 103 caducados, 52 en
            | 8 meses, 1 en 12 meses y 4 vigentes, el stock total es 160, pero el
            | estado NO debe mostrarse como si los 160 estuvieran caducados.
            |
            | Ahora se envía un desglose por estado para que la vista muestre cada
            | cantidad correctamente en la columna Estado.
            */
            $estadosProducto = [
                'vigente' => 0,
                'caducado' => 0,
                'usar_primero' => 0,
                'usar_segundo' => 0,
                'usar_tercero' => 0,
            ];

            foreach ($items as $itemEstado) {
                if (isset($estadosProducto[$itemEstado->color_class])) {
                    $estadosProducto[$itemEstado->color_class] += (int)$itemEstado->existencia;
                }
            }

            $estadosDetalle = [];
            foreach ($estadosProducto as $keyEstado => $cantidadEstado) {
                if ($cantidadEstado > 0) {
                    $estadosDetalle[] = [
                        'key' => $keyEstado,
                        'label' => isset($estadoTexto[$keyEstado]) ? $estadoTexto[$keyEstado] : strtoupper($keyEstado),
                        'cantidad' => $cantidadEstado,
                    ];
                }
            }

            $cantidadEstadosConStock = count($estadosDetalle);
            $estadoResumen = $cantidadEstadosConStock > 1
                ? 'MIXTO'
                : (isset($estadoTexto[$peor->color_class]) ? $estadoTexto[$peor->color_class] : $peor->prioridad_uso);

            $estadoResumenClass = $cantidadEstadosConStock > 1 ? 'mixto' : $peor->color_class;

            $fechaProxima = $items->filter(function ($itemFecha) use ($hoy) {
                try {
                    return \Carbon\Carbon::parse($itemFecha->fecha_vence)->startOfDay()->greaterThanOrEqualTo($hoy);
                } catch (\Exception $e) {
                    return false;
                }
            })->sortBy('fecha_vence')->first();

            $fechaMinima = $items->sortBy('fecha_vence')->first();
            $fechaMostrar = $fechaProxima ? $fechaProxima->fecha_vence : ($fechaMinima ? $fechaMinima->fecha_vence : null);

            $bodegasProducto = $items->groupBy(function ($itemBodega) {
                return trim((string)($itemBodega->bodega ?: 'Sin bodega'));
            })->map(function ($itemsBodega, $nombreBodega) {
                return [
                    'bodega' => $nombreBodega,
                    'stock' => (int)collect($itemsBodega)->sum('existencia'),
                ];
            })->sortByDesc('stock')->values()->all();

            $bodegasTexto = collect($bodegasProducto)->pluck('bodega')->implode(', ');

            $productos[] = [
                'codigo' => $codigo,
                'nombre' => $peor->nombre,
                'stock' => $stock,
                'fecha_vencimiento' => $fechaMostrar,
                'estado' => $estadoResumen,
                'estado_class' => $estadoResumenClass,
                'estado_prioridad' => isset($estadoTexto[$peor->color_class]) ? $estadoTexto[$peor->color_class] : $peor->prioridad_uso,
                'estado_prioridad_class' => $peor->color_class,
                'estados_detalle' => $estadosDetalle,
                'stock_por_estado' => $estadosProducto,
                'bodega' => $peor->bodega,
                'bodegas' => $bodegasProducto,
                'bodegas_texto' => $bodegasTexto,
                'proveedor' => $peor->proveedor,
                'marca' => $peor->marca,
                'tipo' => $peor->tipo,
            ];
        }

        usort($productos, function ($a, $b) use ($ordenEstado) {
            $classA = isset($a['estado_prioridad_class']) ? $a['estado_prioridad_class'] : $a['estado_class'];
            $classB = isset($b['estado_prioridad_class']) ? $b['estado_prioridad_class'] : $b['estado_class'];

            $oa = isset($ordenEstado[$classA]) ? $ordenEstado[$classA] : 9;
            $ob = isset($ordenEstado[$classB]) ? $ordenEstado[$classB] : 9;

            if ($oa == $ob) {
                return strcmp($a['codigo'], $b['codigo']);
            }

            return $oa - $ob;
        });

        $estados = [
            'vigente' => 0,
            'caducado' => 0,
            'usar_primero' => 0,
            'usar_segundo' => 0,
            'usar_tercero' => 0,
        ];

        foreach ($registros as $item) {
            if (isset($estados[$item->color_class])) {
                $estados[$item->color_class] += (int)$item->existencia;
            }
        }

        $bodegas = $registros->groupBy(function ($itemBodega) {
            return trim((string)($itemBodega->bodega ?: 'Sin bodega'));
        })->map(function ($itemsBodega, $nombreBodega) {
            return [
                'bodega' => $nombreBodega,
                'stock' => (int)collect($itemsBodega)->sum('existencia'),
            ];
        })->sortByDesc('stock')->values()->all();

        $porcentajeProximo = $stockTotal > 0 ? round(($proximoVencer * 100) / $stockTotal, 1) : 0;
        $porcentajeCaducados = $stockTotal > 0 ? round(($caducados * 100) / $stockTotal, 1) : 0;

        return response()->json([
            'ok' => true,
            'productos' => $productos,
            'resumen' => [
                'total_productos' => count($productos),
                'stock_total' => $stockTotal,
                'proximo_vencer' => $proximoVencer,
                'caducados' => $caducados,
                'porcentaje_proximo' => $porcentajeProximo,
                'porcentaje_caducados' => $porcentajeCaducados,
            ],
            'estados' => $estados,
            'bodegas' => $bodegas,
        ]);
    }


    public function stock_minimo(Request $request)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_empresa = Session::get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $fecha_hoy = date('Y-m-d');
        $search = trim($request->get('search'));

        $registros = DB::table('inv_inventario_serie as iis')
            ->join('producto as p', 'iis.id_producto', '=', 'p.id')
            ->where('iis.existencia', '>=', 0)
            ->where('iis.estado', 1)
            ->where('p.estado', '1')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('p.codigo', 'LIKE', '%' . $search . '%')
                    ->orWhere('p.nombre', 'LIKE', '%' . $search . '%')
                    ->orWhere('p.descripcion', 'LIKE', '%' . $search . '%');
                });
            })
            ->select(
                'iis.id_producto',
                'p.codigo',
                'p.nombre',
                'p.descripcion',
                'p.minimo as stock_minimo',
                DB::raw('SUM(iis.existencia) as cantidad_total')
            )
            ->groupBy('iis.id_producto', 'p.codigo', 'p.nombre', 'p.descripcion', 'p.minimo')
            ->havingRaw('SUM(iis.existencia) < p.minimo')
            ->orderBy('p.nombre', 'asc')
            ->paginate(20);

        return view('stock_minimo', [
            'registros' => $registros,
            'fecha_hoy' => $fecha_hoy,
            'empresa' => $empresa,
            'search' => $search
        ]);
    }

    public function exportar_caducados_excel(Request $request)
    {
        $id_empresa = Session::get('id_empresa');

        /*
        |--------------------------------------------------------------------------
        | Mantiene los mismos filtros de la vista principal
        |--------------------------------------------------------------------------
        */
        $filtro_estado = $request->get('estado', 'todos');
        $busqueda = trim($request->get('buscar', $request->get('search', '')));

        $prioridadCase = "
            CASE
                WHEN iis.fecha_vence < CURDATE() THEN 'CADUCADO'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 4 MONTH) THEN 'CADUCA EN 4 MESES'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 8 MONTH) THEN 'CADUCA EN 8 MESES'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 12 MONTH) THEN 'CADUCA EN 12 MESES'
                ELSE 'VIGENTE'
            END
        ";

        $colorCase = "
            CASE
                WHEN iis.fecha_vence < CURDATE() THEN 'caducado'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 4 MONTH) THEN 'usar_primero'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 8 MONTH) THEN 'usar_segundo'
                WHEN iis.fecha_vence <= DATE_ADD(CURDATE(), INTERVAL 12 MONTH) THEN 'usar_tercero'
                ELSE 'vigente'
            END
        ";

        $caducado = DB::table('inv_inventario_serie as iis')
            ->join('inv_inventario as ii', 'iis.id_inv_inventario', '=', 'ii.id')
            ->join('producto as p', 'ii.id_producto', '=', 'p.id')
            ->join('inv_cab_movimientos as icm', 'iis.id_cab_movimientos', '=', 'icm.id')
            ->join('tipo as t', 'p.tipo_producto', '=', 't.id')
            ->leftJoin('pedido as pe', 'icm.id_pedido', '=', 'pe.id')
            ->leftJoin('proveedor as pr', 'pr.id', '=', 'pe.id_proveedor')
            ->leftJoin('bodega as b', 'b.id', '=', 'iis.id_bodega')
            ->join('marca as m', 'p.id_marca', '=', 'm.id')
            ->select([
                'p.codigo',
                'p.nombre',
                DB::raw('IFNULL(icm.id_pedido, icm.observacion) AS pedido_detalle'),
                'iis.serie',
                'b.nombre as bodega',
                'pr.razonsocial as proveedor',
                'm.nombre as marca',
                'iis.existencia',
                'iis.fecha_vence',
                'ii.costo_promedio',
                't.nombre as tipo'
            ])
            ->selectRaw("$prioridadCase AS prioridad_uso")
            ->selectRaw("$colorCase AS color_class")
            ->where('ii.id_empresa', $id_empresa)
            ->where('iis.existencia', '>=', 1)
            ->where('iis.estado', 1)
            ->where('ii.estado', 1)
            ->where('p.estado', 1);

        if ($filtro_estado !== 'todos') {
            if ($filtro_estado === 'vigentes') {
                $caducado->whereRaw("($prioridadCase) = ?", ['VIGENTE']);
            }

            if ($filtro_estado === 'caducados') {
                $caducado->whereRaw("($prioridadCase) = ?", ['CADUCADO']);
            }

            if ($filtro_estado === 'usar_primero') {
                $caducado->whereRaw("($prioridadCase) = ?", ['CADUCA EN 4 MESES']);
            }

            if ($filtro_estado === 'usar_segundo') {
                $caducado->whereRaw("($prioridadCase) = ?", ['CADUCA EN 8 MESES']);
            }

            if ($filtro_estado === 'usar_tercero') {
                $caducado->whereRaw("($prioridadCase) = ?", ['CADUCA EN 12 MESES']);
            }

            if ($filtro_estado === 'por_vencer') {
                $caducado->where('iis.fecha_vence', '>=', DB::raw('CURDATE()'))
                    ->where('iis.fecha_vence', '<=', DB::raw('DATE_ADD(CURDATE(), INTERVAL 30 DAY)'));
            }
        }

        if ($busqueda !== '') {
            $caducado->where(function ($q) use ($busqueda, $prioridadCase) {
                $like = '%' . $busqueda . '%';

                $q->where('p.codigo', 'LIKE', $like)
                    ->orWhere('p.nombre', 'LIKE', $like)
                    ->orWhere('iis.serie', 'LIKE', $like)
                    ->orWhereRaw("IFNULL(icm.id_pedido, icm.observacion) LIKE ?", [$like])
                    ->orWhere('b.nombre', 'LIKE', $like)
                    ->orWhere('pr.razonsocial', 'LIKE', $like)
                    ->orWhere('m.nombre', 'LIKE', $like)
                    ->orWhere('t.nombre', 'LIKE', $like)
                    ->orWhere('iis.existencia', 'LIKE', $like)
                    ->orWhere('iis.fecha_vence', 'LIKE', $like)
                    ->orWhereRaw("($prioridadCase) LIKE ?", [$like]);
            });
        }

        $caducado = $caducado
            ->orderByRaw("
                CASE
                    WHEN ($prioridadCase) = 'CADUCADO' THEN 1
                    WHEN ($prioridadCase) = 'CADUCA EN 4 MESES' THEN 2
                    WHEN ($prioridadCase) = 'CADUCA EN 8 MESES' THEN 3
                    WHEN ($prioridadCase) = 'CADUCA EN 12 MESES' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('iis.fecha_vence', 'asc')
            ->orderBy('p.nombre', 'asc')
            ->get();

        $search = $busqueda;
        $fecha = date('Ymd_His');
        $nombreArchivo = 'reporte_productos_caducados_' . $fecha . '.xls';

        $headers = array(
            "Content-Type" => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$nombreArchivo",
            "Pragma" => "no-cache",
            "Expires" => "0"
        );

        return Response::make(
            view('caducados_excel', compact('caducado', 'search'))->render(),
            200,
            $headers
        );
    }

    public function exportar_stock_minimo_excel(Request $request)
    {
        $id_empresa = Session::get('id_empresa');
        $search = trim($request->get('search'));

        $registros = DB::table('inv_inventario_serie as iis')
            ->join('producto as p', 'iis.id_producto', '=', 'p.id')
            ->where('iis.id_empresa', $id_empresa)
            ->where('iis.existencia', '>=', 0)
            ->where('iis.estado', 1)
            ->where('p.estado', '1')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('p.codigo', 'LIKE', '%' . $search . '%')
                    ->orWhere('p.nombre', 'LIKE', '%' . $search . '%')
                    ->orWhere('p.descripcion', 'LIKE', '%' . $search . '%');
                });
            })
            ->select(
                'iis.id_producto',
                'p.codigo',
                'p.nombre',
                'p.descripcion',
                'p.minimo as stock_minimo',
                DB::raw('SUM(iis.existencia) as cantidad_total')
            )
            ->groupBy('iis.id_producto', 'p.codigo', 'p.nombre', 'p.descripcion', 'p.minimo')
            ->havingRaw('SUM(iis.existencia) < p.minimo')
            ->orderBy('p.nombre', 'asc')
            ->get();

        $fecha = date('Ymd_His');
        $nombreArchivo = 'reporte_stock_minimo_' . $fecha . '.xls';

        $headers = array(
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $nombreArchivo,
            'Pragma' => 'no-cache',
            'Expires' => '0'
        );

        return Response::make(
            view('stock_minimo_excel', compact('registros', 'search'))->render(),
            200,
            $headers
        );
    }
}
