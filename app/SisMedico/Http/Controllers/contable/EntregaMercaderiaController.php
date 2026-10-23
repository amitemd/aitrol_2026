<?php

namespace Sis_medico\Http\Controllers\contable;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Bodega;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Guia_Remision_Cabecera;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\EntregaMercaderia;
use Sis_medico\EntregaMercaderiaDetalle;
use Sis_medico\EntregaMercaderiaTrazabilidad;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\PrecioProducto;
use Sis_medico\Services\EntregaMercaderiaService;
use Sis_medico\Union_Bodega;
use Sis_medico\v3\contable\Transportistas;

class EntregaMercaderiaController extends Controller
{
    private $inventario;

    public function __construct(EntregaMercaderiaService $inventario)
    {
        $this->middleware('auth');
        $this->inventario = $inventario;
    }

    public function index(Request $request)
    {
        $empresa = $request->session()->get('id_empresa');
        $query = EntregaMercaderia::with(['cliente', 'guia', 'venta'])
            ->where('id_empresa', $empresa)->orderBy('id', 'desc');
        if ($request->has('buscar') && trim((string) $request->input('buscar')) !== '') {
            $buscar = trim($request->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('numero', 'like', '%' . $buscar . '%')
                    ->orWhere('id_cliente', 'like', '%' . $buscar . '%')
                    ->orWhereHas('cliente', function ($cliente) use ($buscar) {
                        $cliente->where('nombre', 'like', '%' . $buscar . '%');
                    });
            });
        }
        if ($request->has('estado') && trim((string) $request->input('estado')) !== '') {
            $query->where('estado', $request->estado);
        }
        return view('contable.entregas_mercaderia.index', ['entregas' => $query->paginate(20)]);
    }

    public function create(Request $request)
    {
        $empresa = $request->session()->get('id_empresa');
        $clienteSeleccionado = null;
        $bodegasSeleccionadas = collect();
        $productosSeleccionados = collect();
        $seriesSeleccionadas = collect();

        if ($request->old('id_cliente')) {
            $clienteSeleccionado = Ct_Clientes::where('identificacion', $request->old('id_cliente'))
                ->where('estado', 1)->first();
        }
        $bodegasAnteriores = array_filter((array) $request->old('bodega', []));
        if (count($bodegasAnteriores)) {
            $bodegasSeleccionadas = Bodega::whereIn('id', $bodegasAnteriores)
                ->where('estado', 1)->get()->keyBy('id');
        }
        $productosAnteriores = array_filter((array) $request->old('producto', []));
        if (count($productosAnteriores)) {
            $productosSeleccionados = Ct_productos::where('id_empresa', $empresa)
                ->where('estado_tabla', 1)->where('tipo_producto', '<>', 0)
                ->whereIn('id', $productosAnteriores)->get()->keyBy('id');
        }
        $seriesAnteriores = array_filter((array) $request->old('serie', []));
        if (count($seriesAnteriores)) {
            $seriesSeleccionadas = InvInventarioSerie::where('id_empresa', $empresa)
                ->whereIn('id', $seriesAnteriores)->get()->keyBy('id');
        }

        return view('contable.entregas_mercaderia.create', [
            'idempotencyKey' => $request->old('idempotency_key', str_random(40)),
            'clienteSeleccionado' => $clienteSeleccionado,
            'bodegasSeleccionadas' => $bodegasSeleccionadas,
            'productosSeleccionados' => $productosSeleccionados,
            'seriesSeleccionadas' => $seriesSeleccionadas,
            'sucursales' => Ct_Sucursales::where('id_empresa', $empresa)->where('estado', 1)->get(),
            'cajas' => Ct_Caja::where('estado', 1)->get(),
            'transportistas' => Transportistas::where('estado', 1)->orderBy('razon_social')->get(),
        ]);
    }

    public function buscarDestinatarios(Request $request)
    {
        $termino = trim((string) $request->input('q', ''));
        $pagina = max(1, (int) $request->input('page', 1));
        $consulta = Ct_Clientes::where('estado', 1)
            ->when($termino !== '', function ($query) use ($termino) {
                $query->where(function ($filtro) use ($termino) {
                    $filtro->where('identificacion', 'like', '%' . $termino . '%')
                        ->orWhere('nombre', 'like', '%' . $termino . '%');
                });
            })
            ->orderBy('nombre')
            ->select('identificacion', 'nombre');

        return $this->respuestaSelect2($consulta, $pagina, function ($cliente) {
            return [
                'id' => $cliente->identificacion,
                'text' => $cliente->identificacion . ' · ' . $cliente->nombre,
            ];
        });
    }

    public function buscarBodegas(Request $request)
    {
        $termino = trim((string) $request->input('q', ''));
        $pagina = max(1, (int) $request->input('page', 1));
        $empresa = $request->session()->get('id_empresa');
        $idProducto = $request->input('id_producto');
        $producto = Ct_productos::where('id', $idProducto)
            ->where('id_empresa', $empresa)
            ->where('estado_tabla', 1)
            ->first();
        $idsInsumos = DB::table('ct_productos_insumos')->where('id_producto', $idProducto)
            ->pluck('id_insumo')->filter()->unique()->values()->all();
        if (!$producto || !count($idsInsumos)) {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }
        $consulta = DB::table('bodega as bodega_inventario')
            ->join('inv_inventario_serie as serie', 'serie.id_bodega', '=', 'bodega_inventario.id')
            ->join('inv_inventario as inventario', 'inventario.id', '=', 'serie.id_inv_inventario')
            ->where(function ($query) {
                $query->where('bodega_inventario.estado', 1)
                    ->orWhereNull('bodega_inventario.estado');
            })
            ->where(function ($query) {
                $query->where('serie.estado', 1)
                    ->orWhereNull('serie.estado');
            })
            ->where(function ($query) use ($empresa) {
                $query->where('serie.id_empresa', $empresa)
                    ->orWhereNull('serie.id_empresa');
            })
            ->where(function ($query) use ($empresa) {
                $query->where('inventario.id_empresa', $empresa)
                    ->orWhereNull('inventario.id_empresa');
            })
            ->whereNull('serie.deleted_at')
            ->whereNull('inventario.deleted_at')
            ->whereIn('serie.id_producto', $idsInsumos)
            ->where('serie.existencia', '>', 0);

        if ($termino !== '') {
            $consulta->where('bodega_inventario.nombre', 'like', '%' . $termino . '%');
        }

        $consulta->groupBy('bodega_inventario.id', 'bodega_inventario.nombre')
            ->orderBy('bodega_inventario.nombre')
            ->select('bodega_inventario.id', 'bodega_inventario.nombre', DB::raw('SUM(serie.existencia) as existencia'));

        return $this->respuestaSelect2($consulta, $pagina, function ($bodega) {
            return [
                'id' => $bodega->id,
                'text' => $bodega->nombre . ' · Stock total: ' . ((float) $bodega->existencia),
                'existencia' => (float) $bodega->existencia,
            ];
        });
    }

    public function buscarProductos(Request $request)
    {
        $termino = trim((string) $request->input('q', ''));
        $pagina = max(1, (int) $request->input('page', 1));
        $empresa = $request->session()->get('id_empresa');
        $consulta = Ct_productos::where('id_empresa', $empresa)
            ->where('estado_tabla', 1)
            ->where('tipo_producto', '<>', 0)
            ->when($termino !== '', function ($query) use ($termino) {
                $query->where(function ($filtro) use ($termino) {
                    $filtro->where('codigo', 'like', '%' . $termino . '%')
                        ->orWhere('nombre', 'like', '%' . $termino . '%');
                });
            })
            ->orderBy('nombre')
            ->select('id', 'codigo', 'nombre', 'valor_total_paq');

        return $this->respuestaSelect2($consulta, $pagina, function ($producto) {
            $precio = PrecioProducto::where('codigo_producto', $producto->codigo)
                ->where('estado', 1)
                ->orderByRaw('CASE WHEN nivel = 1 THEN 0 ELSE 1 END')
                ->orderBy('nivel')
                ->orderBy('id')
                ->first();
            $precioVenta = $precio ? (float) $precio->precio : (float) $producto->valor_total_paq;

            return [
                'id' => $producto->id,
                'precio_venta' => $precioVenta,
                'text' => $producto->codigo . ' · ' . $producto->nombre,
            ];
        });
    }

    public function buscarSeries(Request $request)
    {
        $empresa = $request->session()->get('id_empresa');
        $pagina = max(1, (int) $request->input('page', 1));
        $limite = 20;
        $token = trim((string) $request->input('token', ''));
        $productoContable = Ct_productos::where('id', $request->input('id_producto'))
            ->where('id_empresa', $empresa)->where('estado_tabla', 1)->first();
        $bodega = Bodega::where('id', $request->input('id_bodega_inventario'))
            ->where('estado', 1)->first();
        $idsInsumos = DB::table('ct_productos_insumos')
            ->where('id_producto', $request->input('id_producto'))
            ->pluck('id_insumo')->filter()->unique()->values()->all();
        if (!$productoContable || !$bodega || !count($idsInsumos)) {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        $consulta = DB::table('inv_inventario_serie as serie')
            ->join('inv_inventario as inventario', 'inventario.id', '=', 'serie.id_inv_inventario')
            ->whereIn('serie.id_producto', $idsInsumos)
            ->where('serie.id_bodega', $bodega->id)
            ->where(function ($query) use ($empresa) {
                $query->where('serie.id_empresa', $empresa)
                    ->orWhereNull('serie.id_empresa');
            })
            ->where(function ($query) {
                $query->where('serie.estado', 1)->orWhereNull('serie.estado');
            })
            ->whereNull('serie.deleted_at')
            ->where('serie.existencia', '>', 0)
            ->where(function ($query) use ($empresa) {
                $query->where('inventario.id_empresa', $empresa)
                    ->orWhereNull('inventario.id_empresa');
            })
            ->where('inventario.id_bodega', $bodega->id)
            ->whereNull('inventario.deleted_at')
            ->orderBy('serie.fecha_vence')
            ->orderBy('serie.id')
            ->select(
                'serie.id',
                'serie.serie',
                'serie.lote',
                'serie.fecha_vence',
                'serie.existencia'
            );

        $registros = $consulta->skip(($pagina - 1) * $limite)->take($limite + 1)->get();
        $hayMas = $registros->count() > $limite;
        $idsSeries = $registros->pluck('id')->all();
        $consultaReservas = DB::table('ct_entregas_mercaderia_reservas')
            ->whereIn('id_inv_inventario_serie', $idsSeries)
            ->where('expires_at', '>', date('Y-m-d H:i:s'));
        if ($token !== '') {
            $consultaReservas->where('token', '<>', $token);
        }
        $reservas = $consultaReservas
            ->groupBy('id_inv_inventario_serie')
            ->select('id_inv_inventario_serie', DB::raw('SUM(cantidad) as cantidad'))
            ->get()->keyBy('id_inv_inventario_serie');
        $resultados = [];
        foreach ($registros->take($limite) as $serie) {
            $reserva = $reservas->get($serie->id);
            $disponible = max(0, (float) $serie->existencia - ($reserva ? (float) $reserva->cantidad : 0));
            if ($disponible <= 0) {
                continue;
            }
            $resultados[] = [
                'id' => $serie->id,
                'text' => 'Serie: ' . ($serie->serie ?: 'N/A') . ' · Lote: ' . ($serie->lote ?: 'N/A') . ' · Vence: ' . ($serie->fecha_vence ?: 'N/A') . ' · Disponible: ' . $disponible,
                'serie' => $serie->serie,
                'lote' => $serie->lote,
                'fecha_vence' => $serie->fecha_vence,
                'existencia' => $disponible,
            ];
        }

        return response()->json(['results' => $resultados, 'pagination' => ['more' => $hayMas]]);
    }

    public function reservarSerie(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|max:64',
            'id_bodega_inventario' => 'required|integer',
            'id_producto' => 'required|integer',
            'id_inv_inventario_serie' => 'required|integer',
            'cantidad' => 'required|integer|min:1',
        ]);

        $empresa = $request->session()->get('id_empresa');
        DB::beginTransaction();
        try {
            DB::table('ct_entregas_mercaderia_reservas')->where('expires_at', '<=', date('Y-m-d H:i:s'))->delete();
            $bodega = Bodega::where('id', $request->id_bodega_inventario)
                ->where('estado', 1)->firstOrFail();
            $productoContable = Ct_productos::where('id', $request->id_producto)
                ->where('id_empresa', $empresa)->where('estado_tabla', 1)->firstOrFail();
            $idsInsumos = DB::table('ct_productos_insumos')->where('id_producto', $request->id_producto)
                ->pluck('id_insumo')->filter()->unique()->values()->all();
            if (!count($idsInsumos)) {
                throw new Exception('El producto no está ligado al inventario de compras.');
            }
            $serie = InvInventarioSerie::where('id', $request->id_inv_inventario_serie)
                ->whereIn('id_producto', $idsInsumos)
                ->where('id_bodega', $bodega->id)
                ->where(function ($query) use ($empresa) {
                    $query->where('id_empresa', $empresa)->orWhereNull('id_empresa');
                })->where(function ($query) {
                    $query->where('estado', 1)->orWhereNull('estado');
                })
                ->lockForUpdate()->firstOrFail();
            $inventario = InvInventario::where('id', $serie->id_inv_inventario)
                ->where('id_producto', $serie->id_producto)
                ->where('id_bodega', $bodega->id)
                ->where(function ($query) use ($empresa) {
                    $query->where('id_empresa', $empresa)->orWhereNull('id_empresa');
                })
                ->lockForUpdate()->firstOrFail();
            $reservadoPorOtros = (float) DB::table('ct_entregas_mercaderia_reservas')
                ->where('id_inv_inventario_serie', $serie->id)
                ->where('token', '<>', $request->token)
                ->where('expires_at', '>', date('Y-m-d H:i:s'))->sum('cantidad');
            $disponible = min((int) $serie->existencia, (int) $inventario->existencia) - $reservadoPorOtros;
            if ((float) $request->cantidad > $disponible) {
                throw new Exception('La cantidad solicitada ya no está disponible. Disponible actual: ' . $disponible . '.');
            }

            $reserva = DB::table('ct_entregas_mercaderia_reservas')
                ->where('token', $request->token)
                ->where('id_inv_inventario_serie', $serie->id)->lockForUpdate()->first();
            $unionBodega = $this->unionBodegaInventario($bodega->id, $empresa);
            $datos = [
                'id_empresa' => $empresa,
                'id_bodega_contable' => $unionBodega ? $unionBodega->id_bodega_cont : 0,
                'id_bodega_inventario' => $bodega->id,
                'id_producto' => $request->id_producto,
                'cantidad' => $request->cantidad,
                'expires_at' => date('Y-m-d H:i:s', time() + 900),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($reserva) {
                DB::table('ct_entregas_mercaderia_reservas')->where('id', $reserva->id)->update($datos);
            } else {
                $datos['token'] = $request->token;
                $datos['id_inv_inventario_serie'] = $serie->id;
                $datos['created_at'] = date('Y-m-d H:i:s');
                DB::table('ct_entregas_mercaderia_reservas')->insert($datos);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'disponible' => $disponible,
                'reservado' => (float) $request->cantidad,
                'expires_at' => $datos['expires_at'],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    public function liberarSerie(Request $request)
    {
        DB::table('ct_entregas_mercaderia_reservas')
            ->where('id_empresa', $request->session()->get('id_empresa'))
            ->where('token', $request->input('token'))
            ->where('id_inv_inventario_serie', $request->input('id_inv_inventario_serie'))
            ->delete();
        return response()->json(['status' => 'success']);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'idempotency_key' => 'required|max:64',
            'id_cliente' => 'required',
            'bodega' => 'required|array|min:1',
            'bodega.*' => 'required|integer',
            'id_sucursal' => 'required|integer',
            'id_caja' => 'required|integer',
            'id_transportista' => 'required',
            'fecha_entrega' => 'required|date',
            'fecha_fin_traslado' => 'required|date',
            'placa' => 'required|max:20',
            'direccion_partida' => 'required|max:255',
            'direccion_destino' => 'required|max:255',
            'motivo_traslado' => 'required|max:255',
            'producto' => 'required|array|min:1',
            'producto.*' => 'required',
            'serie' => 'required|array|min:1',
            'serie.*' => 'required|integer|distinct',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|integer|min:1',
            'precio_venta' => 'required|array|min:1',
            'precio_venta.*' => 'required|numeric|min:0',
        ]);

        $empresa = $request->session()->get('id_empresa');
        $usuario = Auth::user()->id;
        $ip = $request->ip();
        DB::beginTransaction();
        try {
            $cantidadLineas = count((array) $request->producto);
            foreach (['bodega', 'serie', 'cantidad', 'precio_venta'] as $campoLinea) {
                if (count((array) $request->input($campoLinea, [])) !== $cantidadLineas) {
                    throw new Exception('La información de productos, bodegas, series y cantidades está incompleta.');
                }
            }
            $entregaExistente = EntregaMercaderia::where('id_empresa', $empresa)
                ->where('idempotency_key', $request->idempotency_key)->lockForUpdate()->first();
            if ($entregaExistente) {
                DB::commit();
                return redirect()->route('entregas_mercaderia.show', $entregaExistente->id)
                    ->with('success', 'La entrega ya había sido procesada. No se generó un movimiento duplicado.');
            }
            if (strtotime($request->fecha_fin_traslado) < strtotime($request->fecha_entrega)) {
                throw new Exception('La fecha fin del traslado no puede ser anterior a la fecha de entrega.');
            }
            $cliente = Ct_Clientes::where('identificacion', $request->id_cliente)
                ->where('estado', 1)->firstOrFail();
            $transportista = Transportistas::where('id', $request->id_transportista)
                ->where('estado', 1)->firstOrFail();
            $sucursal = Ct_Sucursales::where('id', $request->id_sucursal)
                ->where('id_empresa', $empresa)->where('estado', 1)->firstOrFail();
            $caja = Ct_Caja::where('id', $request->id_caja)
                ->where('id_sucursal', $sucursal->id)->where('estado', 1)->firstOrFail();
            $reservas = DB::table('ct_entregas_mercaderia_reservas')
                ->where('id_empresa', $empresa)
                ->where('token', $request->idempotency_key)
                ->where('expires_at', '>', date('Y-m-d H:i:s'))->get()->keyBy('id_inv_inventario_serie');

            foreach ($request->serie as $i => $idSerie) {
                $reserva = $reservas->get($idSerie);
                if (
                    !$reserva || (int) $reserva->id_bodega_inventario !== (int) $request->bodega[$i]
                    || (float) $reserva->cantidad < (float) $request->cantidad[$i]
                ) {
                    throw new Exception('La reserva de una serie o lote venció o cambió. Vuelva a seleccionarla antes de confirmar.');
                }
            }

            $ultimo = EntregaMercaderia::where('id_empresa', $empresa)->lockForUpdate()->latest()->first();
            $numero = 'ENT-' . date('Y') . '-' . str_pad($ultimo ? ((int) preg_replace('/\D/', '', substr($ultimo->numero, -9)) + 1) : 1, 9, '0', STR_PAD_LEFT);
            $unionPrimeraBodega = $this->unionBodegaInventario($request->bodega[0], $empresa);
            $entrega = EntregaMercaderia::create([
                'numero' => $numero,
                'idempotency_key' => $request->idempotency_key,
                'id_empresa' => $empresa,
                'id_cliente' => $cliente->identificacion,
                'id_bodega_contable' => $unionPrimeraBodega ? $unionPrimeraBodega->id_bodega_cont : 0,
                'id_bodega_inventario' => $request->bodega[0],
                'fecha_entrega' => $request->fecha_entrega,
                'fecha_fin_traslado' => $request->fecha_fin_traslado,
                'transportista_identificacion' => $transportista->ci_ruc,
                'transportista_nombre' => $transportista->razon_social,
                'transportista_email' => $transportista->email,
                'placa' => strtoupper($request->placa ?: $transportista->placa),
                'direccion_partida' => $request->direccion_partida,
                'direccion_destino' => $request->direccion_destino,
                'ruta' => $request->ruta,
                'motivo_traslado' => $request->motivo_traslado,
                'observacion' => $request->observacion,
                'estado' => 'ENTREGADA',
                'id_usuariocrea' => $usuario,
                'id_usuariomod' => $usuario,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ]);

            $lineas = [];
            foreach ($request->producto as $i => $idProducto) {
                $producto = Ct_productos::where('id', $idProducto)->where('id_empresa', $empresa)->firstOrFail();
                $bodega = Bodega::where('id', $request->bodega[$i])->where('estado', 1)->firstOrFail();
                $unionBodega = $this->unionBodegaInventario($bodega->id, $empresa);
                $cantidad = (int) $request->cantidad[$i];
                $detalleEntrega = EntregaMercaderiaDetalle::create([
                    'id_entrega' => $entrega->id,
                    'id_producto' => $producto->id,
                    'id_bodega_contable' => $unionBodega ? $unionBodega->id_bodega_cont : null,
                    'id_bodega_inventario' => $bodega->id,
                    'id_inv_inventario_serie' => $request->serie[$i],
                    'codigo_producto' => $producto->codigo,
                    'nombre_producto' => $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio_venta' => (float) $request->precio_venta[$i],
                    'descripcion' => isset($request->descripcion[$i]) ? $request->descripcion[$i] : null,
                    'id_usuariocrea' => $usuario,
                    'id_usuariomod' => $usuario,
                    'ip_creacion' => $ip,
                    'ip_modificacion' => $ip,
                ]);
                $lineas[] = [
                    'id_detalle' => $detalleEntrega->id,
                    'id_producto' => $producto->id,
                    'id_bodega_contable' => $unionBodega ? $unionBodega->id_bodega_cont : null,
                    'id_bodega_inventario' => $bodega->id,
                    'id_serie' => $request->serie[$i],
                    'cantidad' => $cantidad,
                ];
            }

            $movimiento = $this->inventario->descargarInventario($entrega, $lineas, $usuario, $ip);
            $idGuia = $this->crearGuia($entrega, $cliente, $transportista, $sucursal, $caja, $usuario, $ip);
            $entrega->id_guia_remision = $idGuia;
            $entrega->save();

            $this->traza($entrega, 'ENTREGA_CREADA', 'ENTREGA', $entrega->id, 'Entrega registrada y confirmada.', $usuario, $ip);
            $this->traza($entrega, 'INVENTARIO_DESCARGADO', 'INV_CAB_MOVIMIENTOS', $movimiento['movimiento'], 'Costo descargado: ' . $movimiento['costo'], $usuario, $ip);
            $this->traza($entrega, 'ASIENTO_COSTO_CREADO', 'CT_ASIENTOS_CABECERA', $movimiento['asiento'], 'Asiento del costo de la entrega.', $usuario, $ip);
            $this->traza($entrega, 'GUIA_CREADA', 'GUIA_REMISION', $idGuia, 'Guía en cola de autorización SRI.', $usuario, $ip);
            DB::table('ct_entregas_mercaderia_reservas')
                ->where('id_empresa', $empresa)->where('token', $request->idempotency_key)->delete();
            DB::commit();

            return redirect()->route('entregas_mercaderia.show', $entrega->id)
                ->with('success', 'Entrega, egreso, asiento de costo y guía generados correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            $entregaExistente = EntregaMercaderia::where('id_empresa', $empresa)
                ->where('idempotency_key', $request->idempotency_key)->first();
            if ($entregaExistente) {
                return redirect()->route('entregas_mercaderia.show', $entregaExistente->id)
                    ->with('success', 'La entrega ya había sido procesada. No se generó un movimiento duplicado.');
            }
            return redirect()->back()->withInput()->withErrors(['entrega' => $e->getMessage()]);
        }
    }

    public function show(Request $request, $id)
    {
        $entrega = EntregaMercaderia::with(['cliente', 'detalles.bodegaInventario', 'guia', 'venta', 'trazabilidad'])
            ->where('id_empresa', $request->session()->get('id_empresa'))->findOrFail($id);
        return view('contable.entregas_mercaderia.show', compact('entrega'));
    }

    public function anular(Request $request, $id)
    {
        $this->validate($request, [
            'motivo_anulacion' => 'required|min:5|max:500',
        ]);

        $empresa = $request->session()->get('id_empresa');
        $usuario = Auth::user()->id;
        $ip = $request->ip();
        $motivo = trim($request->input('motivo_anulacion'));

        DB::beginTransaction();
        try {
            $entrega = EntregaMercaderia::where('id_empresa', $empresa)
                ->where('id', $id)->lockForUpdate()->firstOrFail();

            if ($entrega->estado === 'ANULADA') {
                throw new Exception('La entrega ya se encuentra anulada.');
            }
            if ($entrega->id_venta || $entrega->estado === 'FACTURADA') {
                throw new Exception('La entrega tiene una factura asociada. Anule primero el documento comercial antes de revertir el inventario.');
            }

            $reversion = $this->inventario->revertirInventario($entrega, $usuario, $ip, $motivo);
            $estadoGuiaAnterior = null;
            if ($entrega->id_guia_remision) {
                $guia = Ct_Guia_Remision_Cabecera::where('id', $entrega->id_guia_remision)
                    ->where('id_empresa', $empresa)->lockForUpdate()->first();
                if ($guia) {
                    $estadoGuiaAnterior = $guia->estado;
                    $guia->estado = -2;
                    $guia->id_usuariomod = $usuario;
                    $guia->ip_modificacion = $ip;
                    $guia->save();
                }
            }

            $entrega->estado = 'ANULADA';
            $entrega->id_asiento_reversion = $reversion['asiento_reversion'];
            $entrega->fecha_anulacion = date('Y-m-d H:i:s');
            $entrega->motivo_anulacion = $motivo;
            $entrega->id_usuario_anulacion = $usuario;
            $entrega->id_usuariomod = $usuario;
            $entrega->ip_modificacion = $ip;
            $entrega->save();

            $this->traza($entrega, 'INVENTARIO_REVERTIDO', 'INV_CAB_MOVIMIENTOS', $reversion['movimiento'], 'Existencias restauradas en inventario y series.', $usuario, $ip);
            if ($reversion['asiento_reversion']) {
                $this->traza($entrega, 'ASIENTO_COSTO_REVERTIDO', 'CT_ASIENTOS_CABECERA', $reversion['asiento_reversion'], 'Asiento inverso del costo original #' . $entrega->id_asiento_costo . '.', $usuario, $ip);
            }
            if ($entrega->id_guia_remision) {
                $this->traza($entrega, 'GUIA_ANULADA', 'GUIA_REMISION', $entrega->id_guia_remision, 'Estado anterior de la guía: ' . $estadoGuiaAnterior . '.', $usuario, $ip);
            }
            $this->traza($entrega, 'ENTREGA_ANULADA', 'ENTREGA', $entrega->id, $motivo, $usuario, $ip);

            DB::commit();
            return redirect()->route('entregas_mercaderia.show', $entrega->id)
                ->with('success', 'Entrega anulada. El inventario y el asiento de costo fueron revertidos correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['entrega' => $e->getMessage()]);
        }
    }

    public function facturar(Request $request, $id)
    {
        $entrega = EntregaMercaderia::where('id_empresa', $request->session()->get('id_empresa'))->findOrFail($id);
        if ($entrega->id_venta || $entrega->estado === 'FACTURADA') {
            return redirect()->route('entregas_mercaderia.show', $id)->withErrors(['entrega' => 'Esta entrega ya tiene factura.']);
        }
        if ($entrega->estado === 'ANULADA') {
            return redirect()->route('entregas_mercaderia.show', $id)->withErrors(['entrega' => 'Una entrega anulada no puede facturarse.']);
        }
        if (!$entrega->id_movimiento_inventario || !$entrega->id_guia_remision) {
            return redirect()->route('entregas_mercaderia.show', $id)->withErrors(['entrega' => 'La entrega no completó inventario y guía.']);
        }
        return redirect()->route('ventas_crear', ['entrega_mercaderia' => $entrega->id]);
    }

    private function crearGuia($entrega, $cliente, $transportista, $sucursal, $caja, $usuario, $ip)
    {
        $idGuia = DB::table('ct_cabecera_remision')->insertGetId([
            'id_empresa' => $entrega->id_empresa,
            'establecimiento' => $sucursal->codigo_sucursal,
            'punto_emision' => $caja->codigo_caja,
            'num_secuencial' => 0,
            'fecha_emision_documento' => $entrega->fecha_entrega,
            'direccion_partida' => $entrega->direccion_partida,
            'ci_ruc_trasnportista' => $transportista->ci_ruc,
            'email_transportista' => $transportista->email,
            'placa' => $entrega->placa,
            'fecha_ini' => $entrega->fecha_entrega,
            'fecha_fin' => $entrega->fecha_fin_traslado,
            'ci_destinatario' => $cliente->identificacion,
            'direccion_destinatario' => $entrega->direccion_destino,
            'motivo_traslado_destinatario' => $entrega->motivo_traslado,
            'ruta' => $entrega->ruta,
            'razon_social_destinatario' => $cliente->nombre,
            'email_traslado_destinatario' => $cliente->email_representante,
            'codigo_est_destino' => $sucursal->codigo_sucursal,
            'estado' => 0,
            'datos_adicionales' => json_encode(['entrega_mercaderia' => $entrega->numero]),
            'id_inv_cab_movimiento' => $entrega->id_movimiento_inventario,
            'id_usuariocrea' => $usuario,
            'id_usuariomod' => $usuario,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        foreach ($entrega->detalles as $detalle) {
            $datosSerie = [];
            if ($detalle->serie) {
                $datosSerie[] = 'Serie: ' . $detalle->serie;
            }
            if ($detalle->lote) {
                $datosSerie[] = 'Lote: ' . $detalle->lote;
            }
            if ($detalle->fecha_vence) {
                $datosSerie[] = 'Vence: ' . $detalle->fecha_vence;
            }
            if ($detalle->descripcion) {
                $datosSerie[] = $detalle->descripcion;
            }
            DB::table('ct_remision_detalle')->insert([
                'id_cabecera_remision' => $idGuia,
                'id_producto' => $detalle->id_producto,
                'cantidad' => $detalle->cantidad,
                'cod_principal' => $detalle->codigo_producto,
                'cod_adicional' => $detalle->codigo_producto,
                'observacion' => implode(' | ', $datosSerie),
                'descripcion' => $detalle->nombre_producto,
                'detalle3' => $entrega->numero,
                'id_usuariocrea' => $usuario,
                'id_usuariomod' => $usuario,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return $idGuia;
    }

    private function traza($entrega, $evento, $tipo, $documento, $detalle, $usuario, $ip)
    {
        EntregaMercaderiaTrazabilidad::create([
            'id_entrega' => $entrega->id,
            'evento' => $evento,
            'documento_tipo' => $tipo,
            'documento_id' => $documento,
            'detalle' => $detalle,
            'id_usuario' => $usuario,
            'ip' => $ip,
        ]);
    }

    private function respuestaSelect2($consulta, $pagina, $transformar)
    {
        $limite = 20;
        $registros = $consulta->skip(($pagina - 1) * $limite)->take($limite + 1)->get();
        $hayMas = $registros->count() > $limite;
        $resultados = [];

        foreach ($registros->take($limite) as $registro) {
            $resultados[] = $transformar($registro);
        }

        return response()->json([
            'results' => $resultados,
            'pagination' => ['more' => $hayMas],
        ]);
    }

    private function unionBodegaInventario($idBodegaInventario, $idEmpresa)
    {
        return Union_Bodega::join('bodega as bodega_inventario', 'bodega_inventario.id', '=', 'union_bodega.id_bodega_compra')
            ->join('ct_bodegas as bodega_contable', 'bodega_contable.id', '=', 'union_bodega.id_bodega_cont')
            ->where('union_bodega.id_bodega_compra', $idBodegaInventario)
            ->where('union_bodega.id_empresa', $idEmpresa)
            ->orderByRaw('CASE WHEN UPPER(TRIM(bodega_contable.nombre)) = UPPER(TRIM(bodega_inventario.nombre)) THEN 0 ELSE 1 END')
            ->select('union_bodega.*')
            ->first();
    }
}
