<?php

namespace Sis_medico\Http\Controllers\contable;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_Devolucion_Productos;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\LogAsiento;
use Sis_medico\Log_usuario;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Utilidades;
use Sis_medico\Venta_Kardex;

/**
 * Flujo independiente de nota de credito de cliente con retorno fisico.
 *
 * No comparte el guardado de las notas anteriores. La fuente de los productos
 * es el egreso de inventario asociado a la venta, no los datos enviados por el
 * navegador.
 */
class NotaCreditoClienteInventarioController extends Controller
{
    const TIPO_NOTA_INVENTARIO = 3;
    const PREFIJO_MOVIMIENTO = 'DEVOLUCION N/C INVENTARIO #';
    const PREFIJO_ASIENTO_COSTO = 'REVERSO COSTO N/C INVENTARIO #';

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Request $request)
    {
        if ($this->sinPermiso()) {
            return response()->view('errors.404');
        }

        $idEmpresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($idEmpresa);
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $idEmpresa)
            ->orderBy('codigo_sucursal')
            ->get();
        $porIva = Utilidades::porcentajeIva();

        return view('contable.nota_credito_cliente.create_inventario', compact(
            'empresa',
            'sucursales',
            'porIva'
        ));
    }

    public function facturas(Request $request)
    {
        if ($this->sinPermiso()) {
            return response()->json(array(), 403);
        }
        $idEmpresa = $request->session()->get('id_empresa');
        $search = trim((string) $request->get('search', ''));

        $query = Ct_ventas::join('venta_kardex as vk', 'ct_ventas.id', '=', 'vk.id_venta')
            ->join('inv_cab_movimientos as cm', 'vk.id_cab_moviento', '=', 'cm.id')
            ->where('ct_ventas.id_empresa', $idEmpresa)
            ->where('ct_ventas.estado', '!=', 0)
            ->where('ct_ventas.valor_contable', '>', 0)
            ->where('vk.estado', 1)
            ->where('cm.estado', 1);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ct_ventas.nro_comprobante', 'like', '%' . $search . '%')
                    ->orWhere('ct_ventas.nombre_cliente', 'like', '%' . $search . '%')
                    ->orWhere('ct_ventas.id_cliente', 'like', '%' . $search . '%');
            });
        }

        $facturas = $query->groupBy('ct_ventas.id')
            ->orderBy('ct_ventas.fecha', 'desc')
            ->limit(30)
            ->get([
                'ct_ventas.id',
                DB::raw('CONCAT(ct_ventas.nro_comprobante, " | ", ct_ventas.nombre_cliente) as text'),
            ]);

        return response()->json($facturas);
    }

    public function detalleFactura($id, Request $request)
    {
        if ($this->sinPermiso()) {
            return response()->json(['status' => 'error', 'msj' => 'No tiene permisos para esta operacion.'], 403);
        }
        try {
            $venta = $this->ventaValida($id, $request->session()->get('id_empresa'));
            $lineas = $this->lineasRetornables($venta, false);
            if (count($lineas) === 0) {
                return response()->json([
                    'status' => 'error',
                    'msj' => 'La factura no tiene productos pendientes que hayan salido del inventario.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'factura' => [
                    'id' => $venta->id,
                    'numero' => $venta->nro_comprobante,
                    'cliente' => $venta->nombre_cliente,
                    'identificacion' => $venta->id_cliente,
                    'fecha' => $venta->fecha,
                    'total' => round((float) $venta->total_final, 2),
                    'saldo' => round((float) $venta->valor_contable, 2),
                ],
                'lineas' => $lineas,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'msj' => $e->getMessage()], 422);
        }
    }

    public function store(Request $request)
    {
        if ($this->sinPermiso()) {
            return response()->json(['status' => 'error', 'msj' => 'No tiene permisos para esta operacion.'], 403);
        }

        $idEmpresa = $request->session()->get('id_empresa');
        $idUsuario = Auth::user()->id;
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';

        DB::beginTransaction();
        try {
            $this->validarCabecera($request);
            $venta = $this->ventaValida($request->get('factura'), $idEmpresa, true);
            $disponibles = $this->lineasRetornables($venta, true);
            $seleccion = $this->validarSeleccion($request->get('items', array()), $disponibles);
            $totales = $this->calcularTotales($seleccion);

            if ($totales['total'] <= 0) {
                throw new Exception('Seleccione al menos un producto y una cantidad mayor a cero.');
            }
            if ($totales['total'] > round((float) $venta->valor_contable, 2) + 0.01) {
                throw new Exception('El total de la nota de credito supera el saldo contable de la factura.');
            }

            $sucursal = Ct_Sucursales::where('id', $request->get('sucursal'))
                ->where('id_empresa', $idEmpresa)
                ->where('estado', 1)
                ->first();
            $caja = Ct_Caja::where('id', $request->get('punto_emision'))
                ->where('id_sucursal', $sucursal->id)
                ->where('id_empresa', $idEmpresa)
                ->where('estado', 1)
                ->first();
            if (is_null($sucursal) || is_null($caja)) {
                throw new Exception('La sucursal o el punto de emision no son validos.');
            }

            $secuencia = $this->siguienteSecuencia(
                $idEmpresa,
                $sucursal->codigo_sucursal,
                $caja->codigo_caja
            );
            $comprobante = $sucursal->codigo_sucursal . '-' . $caja->codigo_caja . '-' . $secuencia;
            $fecha = $request->get('fecha_hoy');
            $concepto = trim((string) $request->get('concepto'));

            $idAsiento = $this->crearAsientoComercial(
                $idEmpresa,
                $fecha,
                $concepto,
                $comprobante,
                $sucursal->codigo_sucursal,
                $caja->codigo_caja,
                $totales,
                $idUsuario,
                $ip
            );

            $idCredito = Ct_Nota_Credito_Clientes::insertGetId([
                'id_empresa' => $idEmpresa,
                'id_cliente' => $venta->id_cliente,
                'check_sri' => $request->has('check_archivo_sri') ? 1 : 0,
                'id_asiento_cabecera' => $idAsiento,
                'sucursal' => $sucursal->codigo_sucursal,
                'punto_emision' => $caja->codigo_caja,
                'numero_factura' => $venta->nro_comprobante,
                'id_factura' => $venta->id,
                'nro_comprobante' => $comprobante,
                'secuencia' => $secuencia,
                'fecha' => $fecha,
                'tipo' => 'CLI-NC',
                'concepto' => $concepto,
                'subtotal' => $totales['subtotal'],
                'descuento' => $totales['descuento'],
                'impuesto' => $totales['impuesto'],
                'sub_sin_imp' => $totales['subtotal'],
                'subtotal0' => $totales['subtotal0'],
                'tar_iva_12' => $totales['impuesto'],
                'subtotal12' => $totales['subtotal12'],
                'total_credito' => $totales['total'],
                'total_deudas' => $totales['total'],
                'total_abonos' => $totales['total'],
                'total_nuevo_saldo' => $totales['total'],
                'observacion' => trim((string) $request->get('observaciones')),
                'electronica' => $request->has('electronica') ? 1 : 0,
                'doc_electronico' => 0,
                'estado' => 1,
                'tipo_nota' => self::TIPO_NOTA_INVENTARIO,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
                'id_usuariocrea' => $idUsuario,
                'id_usuariomod' => $idUsuario,
            ]);

            $this->guardarDetallesNota($idCredito, $venta, $seleccion, $idUsuario, $ip);
            $resultadoInventario = $this->crearIngresosInventario(
                $idCredito,
                $venta,
                $seleccion,
                $fecha,
                $idEmpresa,
                $idUsuario,
                $ip
            );
            $idAsientoCosto = $this->crearAsientoReversoCosto(
                $idCredito,
                $venta,
                $resultadoInventario['costo'],
                $fecha,
                $idEmpresa,
                $idUsuario,
                $ip
            );

            $venta->valor_contable = round((float) $venta->valor_contable - $totales['total'], 2);
            $venta->id_usuariomod = $idUsuario;
            $venta->ip_modificacion = $ip;
            $venta->save();

            Log_usuario::create([
                'id_usuario' => $idUsuario,
                'ip_usuario' => $ip,
                'descripcion' => 'NOTA CREDITO CLIENTE CON DEVOLUCION INVENTARIO',
                'dato_ant1' => $venta->id,
                'dato1' => $idCredito,
                'dato_ant2' => implode(',', $resultadoInventario['movimientos']),
                'dato2' => $idAsientoCosto,
            ]);

            DB::commit();

            $sri = null;
            if ($request->has('electronica')) {
                try {
                    $sri = NotaCreditoClienteController::newGetSriParcial($idCredito);
                } catch (Exception $e) {
                    $sri = array('status' => 'error', 'msj' => $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'msj' => 'Nota de credito y devolucion de inventario guardadas correctamente.',
                'id' => $idCredito,
                'id_asiento' => $idAsiento,
                'id_asiento_costo' => $idAsientoCosto,
                'movimientos' => $resultadoInventario['movimientos'],
                'numero' => $comprobante,
                'sri' => $sri,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'msj' => $e->getMessage(),
                'linea' => $e->getLine(),
            ], 422);
        }
    }

    /**
     * Revierte el movimiento y el asiento de costo al anular una nota tipo 3.
     * Se invoca desde el controlador historico para conservar una sola opcion
     * de anulacion en el listado.
     */
    public function prepararAnulacion(Ct_Nota_Credito_Clientes $nota, $observacion)
    {
        try {
            $movimientos = InvCabMovimientos::where('id_empresa', $nota->id_empresa)
                ->where('estado', 1)
                ->where('observacion', self::PREFIJO_MOVIMIENTO . $nota->id)
                ->get();
            if ($movimientos->isEmpty()) {
                throw new Exception('No se encontro el ingreso de inventario asociado a la nota de credito.');
            }

            foreach ($movimientos as $movimiento) {
                InvCabMovimientos::anularTransaccionBodega($movimiento->id);
                $movimiento->estado = 0;
                $movimiento->save();
            }

            $asientoCosto = Ct_Asientos_Cabecera::where('id_empresa', $nota->id_empresa)
                ->where('estado', 1)
                ->where('observacion', self::PREFIJO_ASIENTO_COSTO . $nota->id)
                ->first();
            if (is_null($asientoCosto)) {
                throw new Exception('No se encontro el asiento de reverso de costo asociado a la nota de credito.');
            }
            $anulado = LogAsiento::anularAsiento(
                $asientoCosto->id,
                'CLI-NC-INV',
                'ANULACION ' . self::PREFIJO_ASIENTO_COSTO . $nota->id . ': ' . $observacion
            );
            if ($anulado['status'] === 'error') {
                throw new Exception($anulado['msj']);
            }
            $asientoCosto->estado = 0;
            $asientoCosto->save();

            $detalles = Ct_Devolucion_Productos::where('id_nota_credito', $nota->id)
                ->where('estado', 1)
                ->get();
            foreach ($detalles as $detalle) {
                if (preg_match('/\[DV:(\d+)\]/', (string) $detalle->descripcion, $coincidencia)) {
                    $detalleVenta = Ct_detalle_venta::where('id', $coincidencia[1])->lockForUpdate()->first();
                    if (!is_null($detalleVenta)) {
                        $detalleVenta->valor_contable = (float) $detalleVenta->valor_contable + (float) $detalle->total;
                        $detalleVenta->save();
                    }
                }
            }

            return array('status' => 'success');
        } catch (Exception $e) {
            return array('status' => 'error', 'msj' => $e->getMessage());
        }
    }

    private function ventaValida($id, $idEmpresa, $bloquear = false)
    {
        $query = Ct_ventas::where('id', $id)
            ->where('id_empresa', $idEmpresa)
            ->where('estado', '!=', 0);
        if ($bloquear) {
            $query->lockForUpdate();
        }
        $venta = $query->first();
        if (is_null($venta)) {
            throw new Exception('No se encontro una factura valida para la empresa actual.');
        }
        if ((float) $venta->valor_contable <= 0) {
            throw new Exception('La factura no tiene saldo contable disponible.');
        }
        return $venta;
    }

    private function lineasRetornables(Ct_ventas $venta, $bloquear)
    {
        $cabeceras = Venta_Kardex::where('id_venta', $venta->id)
            ->where('estado', 1)
            ->pluck('id_cab_moviento')
            ->unique()
            ->values()
            ->all();
        if (count($cabeceras) === 0) {
            return array();
        }

        $query = InvDetMovimientos::whereIn('id_inv_cab_movimientos', $cabeceras)
            ->where('estado', 1)
            ->orderBy('id');
        if ($bloquear) {
            $query->lockForUpdate();
        }
        $salidas = $query->get();

        $porProducto = array();
        foreach ($salidas as $salida) {
            if (is_null($salida->cabecera) || (string) $salida->cabecera->id_empresa !== (string) $venta->id_empresa) {
                continue;
            }
            $tipo = $salida->cabecera->documento_bodega->tipo_movimiento->tipo;
            if (in_array($tipo, array('I', 'R', 'G'))) {
                continue;
            }
            $devuelto = $this->cantidadDevuelta($salida->id, $venta->id_empresa, $bloquear);
            $disponible = max(0, (float) $salida->cantidad - $devuelto);
            if (!isset($porProducto[$salida->id_producto])) {
                $porProducto[$salida->id_producto] = array();
            }
            $porProducto[$salida->id_producto][] = array(
                'movimiento' => $salida,
                'devuelto' => $devuelto,
                'disponible' => $disponible,
            );
        }

        $detallesVenta = Ct_detalle_venta::where('id_ct_ventas', $venta->id)->orderBy('id')->get();
        $detallesPorProducto = array();
        foreach ($detallesVenta as $detalleVenta) {
            $idProductoInventario = $this->resolverProductoInventario(
                $detalleVenta,
                $venta,
                $porProducto,
                count($detallesVenta)
            );
            if (is_null($idProductoInventario)) {
                continue;
            }
            if (!isset($detallesPorProducto[$idProductoInventario])) {
                $detallesPorProducto[$idProductoInventario] = array();
            }
            $detallesPorProducto[$idProductoInventario][] = $detalleVenta;
        }

        $resultado = array();
        foreach ($detallesPorProducto as $idProducto => $detalles) {
            if (!isset($porProducto[$idProducto])) {
                continue;
            }
            $indiceSalida = 0;
            $usadoSalida = 0;
            foreach ($detalles as $detalleVenta) {
                $pendienteFactura = (float) $detalleVenta->cantidad;
                while ($pendienteFactura > 0.000001 && isset($porProducto[$idProducto][$indiceSalida])) {
                    $infoSalida = $porProducto[$idProducto][$indiceSalida];
                    $salida = $infoSalida['movimiento'];
                    $restaSalidaOriginal = (float) $salida->cantidad - $usadoSalida;
                    $segmento = min($pendienteFactura, $restaSalidaOriginal);

                    $devueltoEnSegmento = min($segmento, $infoSalida['devuelto']);
                    $porProducto[$idProducto][$indiceSalida]['devuelto'] -= $devueltoEnSegmento;
                    $disponibleSegmento = $segmento - $devueltoEnSegmento;

                    if ($disponibleSegmento > 0.000001) {
                        $inventario = $salida->inventario;
                        $resultado[] = array(
                            'key' => $detalleVenta->id . ':' . $salida->id,
                            'detalle_venta_id' => $detalleVenta->id,
                            'detalle_movimiento_id' => $salida->id,
                            'cab_movimiento_id' => $salida->id_inv_cab_movimientos,
                            'codigo' => $detalleVenta->id_ct_productos,
                            'nombre' => $detalleVenta->nombre,
                            'producto_inventario' => isset($salida->producto->nombre) ? $salida->producto->nombre : $detalleVenta->nombre,
                            'bodega' => !is_null($inventario) && !is_null($inventario->bodega) ? $inventario->bodega->nombre : '',
                            'serie' => $salida->serie,
                            'lote' => $salida->lote,
                            'cantidad_salida' => round($segmento, 4),
                            'cantidad_disponible' => round($disponibleSegmento, 4),
                            'precio' => round($this->precioUnitario($detalleVenta), 6),
                            'descuento_unitario' => round($this->descuentoUnitario($detalleVenta), 6),
                            'iva_porcentaje' => $detalleVenta->check_iva == 1 ? (float) $detalleVenta->porcentaje : 0,
                            'costo_unitario' => round((float) $salida->valor_unitario, 6),
                        );
                    }

                    $pendienteFactura -= $segmento;
                    $usadoSalida += $segmento;
                    if ($usadoSalida >= (float) $salida->cantidad - 0.000001) {
                        $indiceSalida++;
                        $usadoSalida = 0;
                    }
                }
            }
        }

        return $resultado;
    }

    /**
     * Resuelve el producto fisico sin confiar en un unico tipo de enlace.
     * Todos los candidatos recibidos aqui ya pertenecen al egreso asociado a
     * la venta por venta_kardex.
     */
    private function resolverProductoInventario(Ct_detalle_venta $detalleVenta, Ct_ventas $venta, array $porProducto, $cantidadDetallesVenta)
    {
        // 1. Enlace mas preciso: la serie elegida al crear la factura.
        if (!is_null($detalleVenta->id_inv_serie) && $detalleVenta->id_inv_serie !== '') {
            $inventarioSerie = InvInventarioSerie::where('id', $detalleVenta->id_inv_serie)
                ->where('id_empresa', $venta->id_empresa)
                ->first();
            if (!is_null($inventarioSerie) && isset($porProducto[$inventarioSerie->id_producto])) {
                return $inventarioSerie->id_producto;
            }
        }

        // 2. Enlace configurado entre producto contable e insumo.
        $productoContable = Ct_productos::where('codigo', $detalleVenta->id_ct_productos)
            ->where('id_empresa', $venta->id_empresa)
            ->first();
        if (is_null($productoContable)) {
            $productoContable = Ct_productos::where('codigo', $detalleVenta->id_ct_productos)->first();
        }
        if (!is_null($productoContable)) {
            $enlace = Ct_productos_insumos::where('id_producto', $productoContable->id)->first();
            if (!is_null($enlace) && !is_null($enlace->id_insumo) && isset($porProducto[$enlace->id_insumo])) {
                return $enlace->id_insumo;
            }
        }

        // 3. El codigo contable coincide con el codigo del producto fisico.
        $codigoFactura = strtoupper(trim((string) $detalleVenta->id_ct_productos));
        foreach ($porProducto as $idProducto => $salidas) {
            foreach ($salidas as $salida) {
                $producto = $salida['movimiento']->producto;
                if (!is_null($producto) && strtoupper(trim((string) $producto->codigo)) === $codigoFactura) {
                    return $idProducto;
                }
            }
        }

        // 4. Facturas antiguas guardaron lote/serie en la descripcion.
        $textoDetalle = strtoupper(
            (string) $detalleVenta->detalle . ' '
                . (string) $detalleVenta->descripcion_producto . ' '
                . (string) $detalleVenta->adicional
        );
        $coincidencias = array();
        foreach ($porProducto as $idProducto => $salidas) {
            foreach ($salidas as $salida) {
                $movimiento = $salida['movimiento'];
                $serie = strtoupper(trim((string) $movimiento->serie));
                $lote = strtoupper(trim((string) $movimiento->lote));
                if (($serie !== '' && strpos($textoDetalle, $serie) !== false)
                    || ($lote !== '' && strpos($textoDetalle, $lote) !== false)
                ) {
                    $coincidencias[$idProducto] = true;
                }
            }
        }
        if (count($coincidencias) === 1) {
            $ids = array_keys($coincidencias);
            return $ids[0];
        }

        // 5. Correspondencia inequivoca: una linea facturada y un solo
        // producto fisico distinto dentro del egreso de esa misma venta.
        if ((int) $cantidadDetallesVenta === 1 && count($porProducto) === 1) {
            $ids = array_keys($porProducto);
            return $ids[0];
        }

        return null;
    }

    private function cantidadDevuelta($idDetalleSalida, $idEmpresa, $bloquear)
    {
        $query = InvDetMovimientos::join('inv_cab_movimientos as retorno_cab', 'inv_det_movimientos.id_inv_cab_movimientos', '=', 'retorno_cab.id')
            ->where('inv_det_movimientos.id_detalle_origen', $idDetalleSalida)
            ->where('inv_det_movimientos.estado', 1)
            ->where('retorno_cab.estado', 1)
            ->where('retorno_cab.id_empresa', $idEmpresa)
            ->where('retorno_cab.observacion', 'like', self::PREFIJO_MOVIMIENTO . '%');
        // El detalle de salida ya fue bloqueado antes de llegar aqui. No se usa
        // FOR UPDATE sobre SUM(), porque no es portable entre versiones MySQL.
        return (float) $query->sum('inv_det_movimientos.cantidad');
    }

    private function validarSeleccion($items, array $disponibles)
    {
        if (!is_array($items)) {
            throw new Exception('La seleccion de productos no es valida.');
        }
        $mapa = array();
        foreach ($disponibles as $linea) {
            $mapa[$linea['key']] = $linea;
        }

        $seleccion = array();
        $claves = array();
        foreach ($items as $item) {
            if (!isset($item['seleccionado']) || (string) $item['seleccionado'] !== '1') {
                continue;
            }
            $key = isset($item['key']) ? (string) $item['key'] : '';
            if ($key === '' || !isset($mapa[$key]) || isset($claves[$key])) {
                throw new Exception('Uno de los productos seleccionados no pertenece a la salida de la factura.');
            }
            $cantidad = isset($item['cantidad']) ? (float) $item['cantidad'] : 0;
            if ($cantidad <= 0 || $cantidad > (float) $mapa[$key]['cantidad_disponible'] + 0.000001) {
                throw new Exception('La cantidad a devolver de ' . $mapa[$key]['nombre'] . ' no es valida.');
            }
            $linea = $mapa[$key];
            $linea['cantidad'] = $cantidad;
            $seleccion[] = $linea;
            $claves[$key] = true;
        }
        if (count($seleccion) === 0) {
            throw new Exception('Seleccione al menos un producto para devolver.');
        }
        return $seleccion;
    }

    private function calcularTotales(array &$seleccion)
    {
        $totales = array(
            'subtotal0' => 0,
            'subtotal12' => 0,
            'subtotal' => 0,
            'descuento' => 0,
            'impuesto' => 0,
            'total' => 0,
        );
        foreach ($seleccion as $indice => $linea) {
            $bruto = $linea['precio'] * $linea['cantidad'];
            $descuento = min($bruto, $linea['descuento_unitario'] * $linea['cantidad']);
            $base = max(0, $bruto - $descuento);
            $impuesto = $base * $linea['iva_porcentaje'];
            if ($linea['iva_porcentaje'] > 0) {
                $totales['subtotal12'] += $bruto;
            } else {
                $totales['subtotal0'] += $bruto;
            }
            $totales['descuento'] += $descuento;
            $totales['impuesto'] += $impuesto;
            $seleccion[$indice]['bruto'] = round($bruto, 2);
            $seleccion[$indice]['descuento'] = round($descuento, 2);
            $seleccion[$indice]['impuesto'] = round($impuesto, 2);
            $seleccion[$indice]['total'] = round($base + $impuesto, 2);
        }
        $totales['subtotal0'] = round($totales['subtotal0'], 2);
        $totales['subtotal12'] = round($totales['subtotal12'], 2);
        $totales['subtotal'] = round($totales['subtotal0'] + $totales['subtotal12'], 2);
        $totales['descuento'] = round($totales['descuento'], 2);
        $totales['impuesto'] = round($totales['impuesto'], 2);
        $totales['total'] = round($totales['subtotal'] - $totales['descuento'] + $totales['impuesto'], 2);
        return $totales;
    }

    private function crearAsientoComercial($idEmpresa, $fecha, $concepto, $comprobante, $sucursal, $caja, array $totales, $idUsuario, $ip)
    {
        $idAsiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion' => $concepto,
            'fecha_asiento' => $fecha,
            'sucursal' => $sucursal,
            'punto_emision' => $caja,
            'fact_numero' => $comprobante,
            'valor' => $totales['total'],
            'id_empresa' => $idEmpresa,
            'estado' => 1,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'id_usuariocrea' => $idUsuario,
            'id_usuariomod' => $idUsuario,
        ]);

        $cuentaCxC = Plan_Cuentas::find(NotaCreditoClienteController::confgClientesComerciales());
        $cuentaVenta = Plan_Cuentas::find(\Sis_medico\LogConfig::busqueda('4.1.07.01'));
        if (is_null($cuentaCxC) || is_null($cuentaVenta)) {
            throw new Exception('No estan configuradas las cuentas de clientes o devolucion en ventas.');
        }
        $this->crearDetalleAsiento($idAsiento, $cuentaCxC, $fecha, 0, $totales['total'], $idUsuario, $ip);
        $this->crearDetalleAsiento($idAsiento, $cuentaVenta, $fecha, $totales['subtotal'] - $totales['descuento'], 0, $idUsuario, $ip);

        if ($totales['impuesto'] > 0) {
            $cuentaIva = Plan_Cuentas::find(\Sis_medico\LogConfig::busqueda('2.01.07.01.01'));
            if (is_null($cuentaIva)) {
                throw new Exception('No esta configurada la cuenta contable de IVA para la nota de credito.');
            }
            $this->crearDetalleAsiento($idAsiento, $cuentaIva, $fecha, $totales['impuesto'], 0, $idUsuario, $ip);
        }

        return $idAsiento;
    }

    private function guardarDetallesNota($idCredito, Ct_ventas $venta, array $seleccion, $idUsuario, $ip)
    {
        foreach ($seleccion as $linea) {
            Ct_Devolucion_Productos::create([
                'id_nota_credito' => $idCredito,
                'descripcion' => '[DV:' . $linea['detalle_venta_id'] . '] Retorno de inventario, movimiento ' . $linea['detalle_movimiento_id'],
                'id_factura' => $venta->id,
                'codigo' => $linea['codigo'],
                'cantidad' => $linea['cantidad'],
                'precio' => $linea['precio'],
                'valor_iva' => $linea['impuesto'],
                'iva' => $linea['iva_porcentaje'] > 0 ? 1 : 0,
                'descuento' => $linea['descuento'],
                'total' => $linea['total'],
                'estado' => 1,
                'nombre' => $linea['nombre'],
                'id_usuariocrea' => $idUsuario,
                'id_usuariomod' => $idUsuario,
                'ip_creacion' => $ip,
                'ip_modificacion' => $ip,
            ]);

            $detalleVenta = Ct_detalle_venta::where('id', $linea['detalle_venta_id'])->lockForUpdate()->first();
            if (!is_null($detalleVenta)) {
                $detalleVenta->valor_contable = max(0, (float) $detalleVenta->valor_contable - $linea['total']);
                $detalleVenta->id_usuariomod = $idUsuario;
                $detalleVenta->ip_modificacion = $ip;
                $detalleVenta->save();
            }
        }
    }

    private function crearIngresosInventario($idCredito, Ct_ventas $venta, array $seleccion, $fecha, $idEmpresa, $idUsuario, $ip)
    {
        $porCabecera = array();
        foreach ($seleccion as $linea) {
            if (!isset($porCabecera[$linea['cab_movimiento_id']])) {
                $porCabecera[$linea['cab_movimiento_id']] = array();
            }
            $porCabecera[$linea['cab_movimiento_id']][] = $linea;
        }

        $movimientos = array();
        $costo = 0;
        foreach ($porCabecera as $idCabeceraSalida => $lineas) {
            $cabeceraSalida = InvCabMovimientos::where('id', $idCabeceraSalida)
                ->where('id_empresa', $idEmpresa)
                ->where('estado', 1)
                ->lockForUpdate()
                ->first();
            if (is_null($cabeceraSalida)) {
                throw new Exception('El egreso original de la factura ya no esta disponible.');
            }

            $bodega = $cabeceraSalida->id_bodega_origen;
            $documento = InvDocumentosBodegas::where('abreviatura_documento', 'ING')
                ->where(function ($q) use ($idEmpresa) {
                    $q->where('id_empresa', $idEmpresa)->orWhereNull('id_empresa');
                })
                ->first();
            if (is_null($documento)) {
                throw new Exception('No esta configurado el documento de ingreso ING para devolver mercaderia.');
            }
            $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                ->where('id_bodega', $bodega)
                ->lockForUpdate()
                ->first();
            if (is_null($transaccion)) {
                throw new Exception('No existe una transaccion de ingreso configurada para la bodega de la venta.');
            }
            // Se incrementa la transaccion exacta ya bloqueada. El helper historico
            // busca el primer documento ING global y puede tomar el de otra empresa.
            $secuencia = max(1, (int) $transaccion->secuencia + 1);
            $transaccion->secuencia = $secuencia;
            $transaccion->save();

            $cabecera = new InvCabMovimientos;
            $cabecera->id_documento_bodega = $documento->id;
            $cabecera->id_transaccion_bodega = $transaccion->id;
            $cabecera->id_bodega_origen = $bodega;
            $cabecera->id_bodega_destino = $bodega;
            $cabecera->numero_documento = str_pad($secuencia, 9, '0', STR_PAD_LEFT);
            $cabecera->observacion = self::PREFIJO_MOVIMIENTO . $idCredito;
            $cabecera->fecha = $fecha;
            $cabecera->num_doc_cont = $venta->nro_comprobante;
            $cabecera->id_docum_origen = $idCabeceraSalida;
            $cabecera->id_empresa = $idEmpresa;
            $cabecera->estado = 1;
            $cabecera->ip_creacion = $ip;
            $cabecera->ip_modificacion = $ip;
            $cabecera->id_usuariocrea = $idUsuario;
            $cabecera->id_usuariomod = $idUsuario;
            $cabecera->save();

            $subtotalCabecera = 0;
            foreach ($lineas as $linea) {
                $salida = InvDetMovimientos::where('id', $linea['detalle_movimiento_id'])
                    ->where('id_inv_cab_movimientos', $idCabeceraSalida)
                    ->where('estado', 1)
                    ->lockForUpdate()
                    ->first();
                if (is_null($salida)) {
                    throw new Exception('Uno de los productos ya no pertenece al egreso de la factura.');
                }
                $proporcionUso = (float) $salida->cantidad > 0
                    ? (float) $salida->cant_uso / (float) $salida->cantidad
                    : 0;
                $subtotal = round((float) $salida->valor_unitario * $linea['cantidad'], 6);
                $detalle = new InvDetMovimientos;
                $detalle->id_inv_cab_movimientos = $cabecera->id;
                $detalle->id_producto = $salida->id_producto;
                $detalle->id_inv_inventario = $salida->id_inv_inventario;
                $detalle->cantidad = $linea['cantidad'];
                $detalle->cant_uso = $proporcionUso * $linea['cantidad'];
                $detalle->serie = $salida->serie;
                $detalle->lote = $salida->lote;
                $detalle->fecha_vence = $salida->fecha_vence;
                $detalle->valor_unitario = $salida->valor_unitario;
                $detalle->subtotal = $subtotal;
                $detalle->descuento = 0;
                $detalle->iva = 0;
                $detalle->total = $subtotal;
                $detalle->motivo = self::PREFIJO_MOVIMIENTO . $idCredito;
                $detalle->id_detalle_origen = $salida->id;
                $detalle->id_pedido = $salida->id_pedido;
                $detalle->estado = 1;
                $detalle->ip_creacion = $ip;
                $detalle->ip_modificacion = $ip;
                $detalle->id_usuariocrea = $idUsuario;
                $detalle->id_usuariomod = $idUsuario;
                $detalle->save();
                $subtotalCabecera += $subtotal;
                $costo += $subtotal;
            }

            $cabecera->subtotal = round($subtotalCabecera, 6);
            $cabecera->subtotal_0 = round($subtotalCabecera, 6);
            $cabecera->descuento = 0;
            $cabecera->iva = 0;
            $cabecera->total = round($subtotalCabecera, 6);
            $cabecera->save();

            InvKardex::setKardex($cabecera->id);
            $movimientos[] = $cabecera->id;
        }

        return array('movimientos' => $movimientos, 'costo' => round($costo, 2));
    }

    private function crearAsientoReversoCosto($idCredito, Ct_ventas $venta, $costo, $fecha, $idEmpresa, $idUsuario, $ip)
    {
        if ($costo < 0) {
            throw new Exception('El costo de los productos devueltos no puede ser negativo.');
        }
        $cuentaCosto = Ct_Configuraciones::where('id_empresa', $idEmpresa)
            ->where('tipo', 'COSTO_DEBE_PROCEDIMIENTO')
            ->first();
        $cuentaInventario = Ct_Configuraciones::where('id_empresa', $idEmpresa)
            ->where('tipo', 'COSTO_HABER_PROCEDIMIENTO')
            ->first();
        if (is_null($cuentaCosto) || is_null($cuentaInventario)) {
            throw new Exception('Configure las cuentas COSTO_DEBE_PROCEDIMIENTO y COSTO_HABER_PROCEDIMIENTO.');
        }
        $planCosto = Plan_Cuentas::find($cuentaCosto->id_plan);
        $planInventario = Plan_Cuentas::find($cuentaInventario->id_plan);
        if (is_null($planCosto) || is_null($planInventario)) {
            throw new Exception('Las cuentas de costo e inventario configuradas no existen en el plan de cuentas.');
        }

        $idAsiento = Ct_Asientos_Cabecera::insertGetId([
            'observacion' => self::PREFIJO_ASIENTO_COSTO . $idCredito,
            'fecha_asiento' => $fecha,
            'fact_numero' => $venta->nro_comprobante,
            'valor' => $costo,
            'id_empresa' => $idEmpresa,
            'estado' => 1,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'id_usuariocrea' => $idUsuario,
            'id_usuariomod' => $idUsuario,
        ]);

        // Reverso del costo de venta: inventario al Debe y costo de venta al Haber.
        $this->crearDetalleAsiento($idAsiento, $planInventario, $fecha, $costo, 0, $idUsuario, $ip);
        $this->crearDetalleAsiento($idAsiento, $planCosto, $fecha, 0, $costo, $idUsuario, $ip);

        return $idAsiento;
    }

    private function crearDetalleAsiento($idAsiento, Plan_Cuentas $cuenta, $fecha, $debe, $haber, $idUsuario, $ip)
    {
        Ct_Asientos_Detalle::create([
            'id_asiento_cabecera' => $idAsiento,
            'id_plan_cuenta' => $cuenta->id,
            'descripcion' => $cuenta->nombre,
            'fecha' => $fecha,
            'debe' => round($debe, 2),
            'haber' => round($haber, 2),
            'estado' => 1,
            'id_usuariocrea' => $idUsuario,
            'id_usuariomod' => $idUsuario,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
        ]);
    }

    private function validarCabecera(Request $request)
    {
        if (!$request->get('factura')) {
            throw new Exception('Seleccione una factura.');
        }
        if (!$request->get('sucursal') || !$request->get('punto_emision')) {
            throw new Exception('Seleccione la sucursal y el punto de emision.');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->get('fecha_hoy'))) {
            throw new Exception('La fecha de la nota no es valida.');
        }
        if (trim((string) $request->get('concepto')) === '') {
            throw new Exception('Ingrese el concepto de la nota de credito.');
        }
    }

    private function siguienteSecuencia($idEmpresa, $sucursal, $caja)
    {
        $ultima = Ct_Nota_Credito_Clientes::where('id_empresa', $idEmpresa)
            ->where('sucursal', $sucursal)
            ->where('punto_emision', $caja)
            ->orderBy('secuencia', 'desc')
            ->lockForUpdate()
            ->first();
        $numero = is_null($ultima) ? 1 : ((int) $ultima->secuencia + 1);
        return str_pad($numero, 9, '0', STR_PAD_LEFT);
    }

    private function precioUnitario(Ct_detalle_venta $detalle)
    {
        if ((float) $detalle->cantidad > 0 && (float) $detalle->extendido > 0) {
            return (float) $detalle->extendido / (float) $detalle->cantidad;
        }
        return (float) $detalle->precio;
    }

    private function descuentoUnitario(Ct_detalle_venta $detalle)
    {
        return (float) $detalle->cantidad > 0
            ? (float) $detalle->descuento / (float) $detalle->cantidad
            : 0;
    }

    private function sinPermiso()
    {
        return !in_array(Auth::user()->id_tipo_usuario, array(1, 4, 5, 20, 22));
    }
}
