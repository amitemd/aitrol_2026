<?php

namespace Sis_medico\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Bodega;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Http\Controllers\plantilla_labs\PlantillaControlLabsController;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvContableCab;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\Log_Api_Humanlabs;
use Sis_medico\Movimiento;
use Sis_medico\Planilla_Cabecera_Labs;
use Sis_medico\Planilla_Detalle_Labs;
use Sis_medico\Producto;
use Session;
use Sis_medico\Http\Controllers\Insumos\TransitoController;
use Sis_medico\KardexUsoLabs;

class ApiHumanlabs extends Controller
{
    public function index(Request $r)
    {
        
        // return ['status' => true, 'msj' => $detalle_unico];
        if ($r->opcion == 'bajarinsumos') {
            DB::beginTransaction();
            try {
                $total_derivados = 0;
                $total_asiento = 0;
                //if (count($r->bodegas) > 0) {
                    /*foreach ($r->bodegas as $row) {
                        /*return [
                        'code' => 500,
                        'status' => false,
                        'msj' => $row['id_bodega_gye']
                    ];*/
                    /*$bodega = Bodega::find($row['id_bodega_gye']);
                        if ($bodega == '') {
                            return ["status" => false, "msj" => "No existe bodega", 'data' => $r->input()];
                        }
                    }*/
                //}
                //$id_bodega = $bodega->id;
                if ($r->planilla_cabecera_labs['aprobado'] == 1) {
                    return ["status" => false, "msj" => "Ya se encuentra aprobada la plantilla"];
                }
                $planilla = $r->planilla_cabecera_labs;

                unset($planilla['id'], $planilla['id_orden'], $planilla["detalles"]);
                // return ['status' => false, 'msj' => $planilla];

                $IdPlanilla = Planilla_Cabecera_Labs::insertGetId($planilla);
                $planillaCabecera = Planilla_Cabecera_Labs::find($IdPlanilla);
                // $verificarStock = PlantillaControlLabsController::verificarStock($IdPlanilla);
                // if ($verificarStock['status'] == false) {
                //     return ['status' => false, "msj" => $verificarStock['problem']];
                // }
                $countNoExiste = 0;
                $dProducto = [];
                $count = 0;
                $arrayProductos = [];
                /*  if (count($r->examenDev) > 0) {
                        for($h=0;$h<count($r->examenDev);$h)
                        {
                            $total_derivados+=$r->examenDev[$h]->
                        }
                          
            } */
            
                for ($q = 0; $q < count($r->planilla_detalle); $q++) {
                    $producto = Producto::where('codigo', $r->planilla_detalle[$q]['codigo'])->first();
                    $costo = InvInventario::where('id_producto', $producto->id)->where('id_bodega', $r->planilla_detalle[$q]['id_bodega'])
                    ->where('estado', 1)->where('existencia', '>', 0)->where('id_empresa', '0993075000001')->first();
                    
                    $serie = InvInventarioSerie::where('serie', $r->planilla_detalle[$q]['serie'])->first();
                    if(is_null($serie)){
                        $serie = InvInventarioSerie::where("id_producto",$producto->id)->where("id_bodega",$r->planilla_detalle[$q]['id_bodega'])
                        ->where("estado",1)->where("existencia",'>',0)->where("id_empresa","0993075000001")
                        ->orderBy('fecha_vence','DESC')
                        ->first();
                    }
                    $arrayDetallePlanilla = [
                        "id_producto" => $producto->id,
                        "codigo" => $producto->codigo,
                        "id_planilla_cabecera" => $IdPlanilla,
                        "check" => 1,
                        "precio" => $costo->costo_promedio,
                        "estado" => 1,
                        "id_usuariocrea" => 'FACELECTRO',
                        "id_usuariomod" => 'FACELECTRO',
                        "ip_creacion" => $_SERVER['SERVER_ADDR'],
                        "ip_modificacion" => $_SERVER['SERVER_ADDR'],
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" =>  date('Y-m-d H:i:s'),
                        "cantidad" => $r->planilla_detalle[$q]['cantidad'],
                        "serie" =>  $r->planilla_detalle[$q]['serie'],
                        "lote" => $serie->lote,
                        "fecha_vencimiento" => $serie->fecha_vence,
                        "observacion" => null,
                        "tipo_plantilla" => null,
                        "id_platilla" => null,
                        "id_examen_derivado" => $r->planilla_detalle[$q]['id_examen_derivado'],
                        //"id_examen_derivado" => $IdPlanilla,
                        "id_bodega" => $r->planilla_detalle[$q]['id_bodega'],
                        "id_examen" => 0,
                    ];
                    Planilla_Detalle_Labs::insert($arrayDetallePlanilla);
                }
                // for ($i = 0; $i < count($r->productos); $i++) {
                //     $producto = Producto::where('codigo', $r->productos[$i]['codigo'])->first();
                //     if ($producto == '') {
                //         $arrayP = [
                //             'codigo' => $r->productos[$i]['codigo'],
                //             'nombre' => $r->productos[$i]['nombre']
                //         ];
                //         array_push($arrayProductos, $arrayP);
                //         $countNoExiste++;
                //     } else {
                //         $countBodegas = 0;

                //         // $invSerie = PlantillaControlLabsController::verificarStock($IdPlanilla);
                //         // $invSerie = InvInventario::where('id_producto', $producto->id)
                //         //     ->where('id_bodega', $r['bodegas'][$i])
                //         //     ->first();
                //         // if ($invSerie == '') {
                //         //     $prod = '';
                //         //     for ($q = 0; $q < count($r->productos); $q++) {
                //         //         $arrayP = [
                //         //             'codigo' => $r->productos[$q]['codigo'],
                //         //             'nombre' => $r->productos[$q]['nombre']
                //         //         ];
                //         //         array_push($arrayProductos, $arrayP);
                //         //     }
                //         //     if (count($arrayProductos) > 0) {
                //         //         for ($f = 0; $f < count($arrayProductos); $f++) {
                //         //             $prod .= '<br/>Código: ' . $arrayProductos[$f]['codigo'] . ' -  Nombre:' . $arrayProductos[$f]['nombre'] . '<br/>';
                //         //         }
                //         //     }
                //         //     $result = array('code' => 500, 'status' => false, 'data' => '', 'msj' => 'Error: El producto ' . $prod . ' no tiene existencia...');
                //         //     DB::rollBack();
                //         //     return $result;
                //         // }
                //         return ['status' => false, 'msj' => $r->all()];
                //         $invSerie = InvInventario::where('id_producto', $producto->id)->where('id_bodega', $r['bodegas'][$i])
                //             ->where('estado', 1)->where('existencia', '>', 0)->where('id_empresa', '0993075000001')->first();

                //         $serie = InvInventarioSerie::where('serie', $r->planilla_detalle['serie'][$i])->first();

                //         return [$serie, $invSerie];
                //         $arrayDetallePlanilla = [
                //             "id_producto" => $producto->id,
                //             "codigo" => $producto->codigo,
                //             "id_planilla_cabecera" => $IdPlanilla,
                //             "check" => 1,
                //             "precio" => $invSerie->costo_promedio,
                //             "estado" => 1,
                //             "id_usuariocrea" => 'FACELECTRO',
                //             "id_usuariomod" => 'FACELECTRO',
                //             "ip_creacion" => $_SERVER['SERVER_ADDR'],
                //             "ip_modificacion" => $_SERVER['SERVER_ADDR'],
                //             "created_at" => date('Y-m-d H:i:s'),
                //             "updated_at" =>  date('Y-m-d H:i:s'),
                //             "cantidad" => $r->datos['cantidad'][$i],
                //             "serie" => $serie->serie,
                //             "lote" => $serie->lote,
                //             "fecha_vencimiento" => $serie->fecha_vence,
                //             "observacion" => null,
                //             "tipo_plantilla" => null,
                //             "id_platilla" => null,
                //             "id_examen_derivado" => $r->pl_cab_derivado['id'],
                //             //"id_examen_derivado" => $IdPlanilla,
                //             "id_bodega" => $r['bodegas'][$i],
                //             "id_examen" => 0,
                //         ];
                //         Planilla_Detalle_Labs::insert($arrayDetallePlanilla);
                //         //$countNoExiste++;
                //     }
                // }
                if ($countNoExiste > 0) {
                    $prod = '';
                    if (count($arrayProductos) > 0) {
                        for ($i = 0; $i < count($arrayProductos); $i++) {
                            $prod .= '<br/>' . $arrayProductos[$i]['codigo'] . ' - ' . $arrayProductos[$i]['nombre'] . '<br/>';
                        }
                    }
                    $result = array('code' => 500, 'status' => false, 'data' => '', 'msj' => 'Error: El producto ' . $prod . ' no existe...');
                    DB::rollBack();
                    return $result;
                }
                if (isset($r->pl_cab_derivado['detalles'])) {
                    foreach ($r->pl_cab_derivado['detalles'] as $row)
                        $total_asiento += $row['precio'];
                }
                // $result = $this->bajarinsumos($r, $IdPlanilla, $total_asiento);
                
                $result = $this->newbajarinsumos($r, $IdPlanilla, $total_asiento);
                DB::commit();
                return $result;
            } catch (Exception $e) {
                DB::rollBack();
                $result = array('code' => 500, 'status' => false, 'data' => $r['pl_cab_derivado']['detalles'], 'msj' => 'Error:' . $e->getMessage(), 'linea' => $e->getLine());
                //$result = array('code' => 500, 'status' => false, 'data' => '', 'msj' => 'Error: El producto no existe...');
                return $result;
            }
        } elseif ($r->opcion == 'verificarStock') {
            $data_val["id_producto"] = $r->id_producto;
            $data_val["cantidad"] = $r->cantidad;
            $data_val["id_bodega"] = $r->id_bodega;
            return  ApiHumanlabs::validarStock($data_val);
        }
    }
    public function bajarinsumos_(Request $r, $idPlanilla, $total_derivado = 0)
    {
        DB::beginTransaction();
        try {
            $idUsuario = 'FACELECTRO';
            $reproceso = $this->reprocesar_serie($idPlanilla);
            /*echo '<pre>';
            print_r($r->input());
            exit;*/
            $cab_movimiento = InvCabMovimientos::where('id_empresa', $r->id_empresa)->latest()->first();
            $total_asiento = 0;
            $bodega = Bodega::where("principal_labs", 1)->first();
            $planilla_cabecera_labs = Planilla_Cabecera_Labs::where('id', $idPlanilla)->first();

            if ($planilla_cabecera_labs->aprobado == 1) {
                DB::rollBack();
                return ["status" => false, "msj" => "Ya se encuentra aprobada la plantilla"];
            }
            if (isset($planilla_cabecera_labs->detalles)) {
                $detalle = $planilla_cabecera_labs->detalles->sum('precio');
                $total_asiento += $detalle;
            }
            $id_usuario = $r->id_usuariocrea;
            $numero = "000000001";
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            if (!is_null($cab_movimiento)) {
                $numero = str_pad(intval($cab_movimiento->numero_documento) + 1, 9, "0", STR_PAD_LEFT);
            }
            $idEmpresa = '0993075000001';
            $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'EPL')->where('id_empresa', $idEmpresa)->first();
            $secuenciaBod = 1;
            if (!is_null($inv_documento_bod)) {
                $secuenciaBod = intval($inv_documento_bod->secuencia);
            }
            $inv_transacciones_bodegas = InvTransaccionesBodegas::insertGetId([
                'id_documento_bodega'   => $inv_documento_bod->id,
                'secuencia'             => $secuenciaBod,
                'id_empresa'            => $idEmpresa,
                'id_usuariocrea'        => $idUsuario,
                'id_usuariomod'         => $idUsuario,
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente
            ]);
            $arrayMovCab = [
                'id_documento_bodega'       => $inv_documento_bod->id,
                'id_transaccion_bodega'     => $inv_transacciones_bodegas,
                'id_bodega_origen'          => 1,
                'numero_documento'          => $numero,
                'observacion'               => "EGRESO LAB PACIENTE: {$r->examen_orden['id_paciente']}, Orden: {$r->examen_orden['id']}",
                'fecha'                     => date("Y-m-d"),
                'descuento'                 => 0,
                'subtotal'                  => 0,
                'subtotal_0'                => 0,
                'iva'                       => 0,
                'total'                     => 0,
                'estado'                    => 1,
                'id_empresa'                => $idEmpresa,
                'id_usuariocrea'            => $idUsuario,
                'id_usuariomod'             => $idUsuario,
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
            ];
            $id_cab_movimiento = InvCabMovimientos::insertGetId($arrayMovCab);
            if (count($planilla_cabecera_labs->detalles) > 0) {
                foreach ($planilla_cabecera_labs->detalles as $row) {
                    if ($row->check == 1) { // && $row->id_examen_derivado == null
                        $costo_promedio = $row->precio;
                        $exist_nueva = $row->cantidad;
                        // $inv_inventario = InvInventario::getInventario($row->id_producto, $row->id_bodega, "C");
                        $inv_inventario = InvInventario::where('id_producto', $row->id_producto)->where('id_bodega', $row->id_bodega)->latest()->first();
                        if (!is_null($inv_inventario)) {
                            $exist_nueva = $inv_inventario->existencia + $row->cantidad;
                            if (!is_null($inv_inventario->costo_promedio)) {
                                $cantidad_ant       = $exist_nueva;
                                $precio_ant         = $inv_inventario->costo_promedio;
                                $suma_ant           = $inv_inventario->existencia * $precio_ant;
                                $costo_promedio     = ($suma_ant + $row->costo_total) / $exist_nueva;
                            }
                        } else {
                            DB::rollBack();
                            return array('code' => 200, 'status' => false, 'data' => '', 'msj' => 'No existe producto en la bodega');
                        }
                        $total_asiento +=  $row->precio;
                        $data_inv_inventario = [
                            'id_producto' => $row->id_producto,
                            'id_bodega' => $row->id_bodega,
                            'tipo' => 'C',
                            'existencia' => intval($inv_inventario->existencia) - intval($row->cantidad),
                            'existencia_uso' => intval($inv_inventario->existencia_uso) - intval($row->cantidad),
                            'existencia_min' => $inv_inventario->existencia_min,
                            'existencia_max' => $inv_inventario->existencia_max,
                            'comprometido'  => intval($inv_inventario->comprometido) + intval($row->cantidad),
                            'comprometido_uso'  => intval($inv_inventario->comprometido_uso) + intval($row->cantidad),
                            'costo_promedio'    => $costo_promedio,
                            'estado' => 1,
                            'id_empresa' => $idEmpresa,
                            'id_usuariocrea' => $idUsuario,
                            'id_usuariomod' => $idUsuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                        ];
                        $id_inv_inventario = InvInventario::insertGetId($data_inv_inventario);
                        $movimiento = Movimiento::where('serie', $row->serie)->where('estado', 1)->first();
                        $data_inv_det_movimiento = [
                            'id_inv_cab_movimientos'        => $id_cab_movimiento,
                            'id_producto'                   => $row->id_producto,
                            'serie'                         => $row->serie,
                            'fecha_vence'                   => date('Y-m-d', strtotime($row->fecha_vencimiento)),
                            'lote'                          => $row->lote,
                            'id_inv_inventario'             => $id_inv_inventario,
                            'cantidad'                      => $row->cantidad,
                            'cant_uso'                      => $row->cantidad,
                            'valor_unitario'                => $row->precio,
                            'subtotal'                      => $row->precio,
                            'descuento'                     => !is_null($movimiento) ? $movimiento->descuento : 0,
                            'iva'                           => 0,
                            'total'                         => $row->precio,
                            'estado'                        => 1,
                            'kardex'                        => 1,
                            'motivo'                        => "EGRESO LAB PACIENTE: {$r->examen_orden['id_paciente']} | Orden: {$r->examen_orden['id']} | {$r->bodegas[0]['nombre']}",
                            'id_pedido'                     => !is_null($movimiento) ? $movimiento->id_pedido : null,
                            'id_usuariocrea'                => $idUsuario,
                            'id_usuariomod'                 => $idUsuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ];
                        $inv_det_movimiento = InvDetMovimientos::insertGetId($data_inv_det_movimiento);
                        $inv_serie = InvInventarioSerie::where('id_empresa', $idEmpresa)
                            ->where('serie', $row->serie)
                            ->where('id_bodega', $row->id_bodega)
                            ->first();
                        $regreso = KardexUsoLabs::descontar_kardex_nuevo($inv_serie->serie, $row->id_bodega, $inv_det_movimiento, $row->id);
                        // return $regreso;
                        // if($regreso["status"] == false){
                        //     return $regreso;
                        // }
                        // return array('code' => 500, 'status' => false, 'data' => $regreso, 'msj' => 'Error:' . $regreso);
                        InvCabMovimientos::calcularTotalCabMovimiento($id_cab_movimiento);
                        if ($regreso == 0) {
                            if (!is_null($inv_serie)) {
                                if ($inv_serie->existencia > 0) {
                                    $inv_serie->existencia = $inv_serie->existencia - $row->cantidad;
                                    $inv_serie->existencia_uso = $inv_serie->existencia_uso -  $row->cantidad;
                                    $inv_serie->id_usuariomod = $r->id_usuariocrea;
                                    $inv_serie->ip_modificacion = $ip_cliente;
                                    $inv_serie->save();
                                }
                            }
                            $data_inv_kardex = [
                                'id_inv_inventario' => $id_inv_inventario,
                                'id_bodega' => $row->id_bodega,
                                'id_producto' => $row->id_producto,
                                'tipo' => 'E',
                                'descripcion' => 'EGR-',
                                'referencia' => "EGRESO LAB PACIENTE: {$r->examen_orden['id_paciente']} | Orden: {$r->examen_orden['id']} | {$r->bodegas[0]['nombre']}",
                                'fecha' => date('Y-m-d'),
                                'cantidad' => $row->cantidad,
                                'cant_uso' => $row->cantidad,
                                'valor_unitario' => $row->precio,
                                'total' => $row->precio,
                                'exist_cant' => $inv_inventario->existencia,
                                'exist_uso' => $inv_inventario->existencia_uso,
                                'estado' => 1,
                                'id_documento_bodega' => $inv_documento_bod->id,
                                'id_inv_det_movimientos' => $inv_det_movimiento,
                                'id_empresa' => $idEmpresa,
                                'id_usuariocrea' => $idUsuario,
                                'id_usuariomod' => $idUsuario,
                                'ip_creacion' => $ip_cliente,
                                'ip_modificacion' => $ip_cliente
                            ];
                            $id_inv_kardex = InvKardex::insertGetId($data_inv_kardex);
                        }
                    }
                }
            }
            $id_asiento_cab = null;
            $arrayDetalleAsiento = [];
            $inv_contable_cab = InvContableCab::where('id_documento_bodega', $inv_documento_bod->id)->where('id_empresa', $idEmpresa)->first();

            if (!is_null($inv_contable_cab)) {
                $input_cabecera = [
                    'fecha_asiento'   => $r->examen_orden['fecha_orden'],
                    'fact_numero'     => $numero,
                    'id_empresa'      => $idEmpresa,
                    'observacion'     => "EXAMEN LAB PACIENTE: {$r->examen_orden['id_paciente']} ",
                    'valor'           => $total_asiento + $total_derivado,
                    'id_usuariocrea'  => $idUsuario,
                    'id_usuariomod'   => $idUsuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $id_asiento_cab = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                foreach ($inv_contable_cab->detalles as $det_labs) {
                    $arrayAsientoDetale = [
                        'id_asiento_cabecera' => $id_asiento_cab,
                        'id_plan_cuenta'      => $det_labs->cuenta,
                        'descripcion'         => '',
                        'fecha'               => $r->examen_orden['fecha_orden'],
                        'debe'                => $det_labs->tipo == 'D' ? $total_asiento + $total_derivado  : 0,
                        'haber'               => $det_labs->tipo == 'H' ? $total_asiento + $total_derivado : 0,
                        'estado'              => '1',
                        'id_usuariocrea'      => $idUsuario,
                        'id_usuariomod'       => $idUsuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ];
                    Ct_Asientos_Detalle::create($arrayAsientoDetale);
                    array_push($arrayDetalleAsiento, $arrayAsientoDetale);
                }
            }
            if (!is_null($planilla_cabecera_labs)) {
                $planilla_cabecera_labs->aprobado = 1;
                $planilla_cabecera_labs->observacion = $r->observacion_planilla;
                $planilla_cabecera_labs->id_usuariomod = $id_usuario;
                $planilla_cabecera_labs->id_asiento_cabecera = $id_asiento_cab;
                $planilla_cabecera_labs->save();
            }
            DB::commit();
            //DB::rollBack();
            return ['status' => true, "msj" => 'Guardado con exito', 'asiento' => $id_asiento_cab];
        } catch (Exception $e) {
            DB::rollBack();
            return array('code' => 500, 'status' => false, 'data' => '', 'msj' => 'Error:' . $e->getMessage(), 'linea' => $e->getLine());
        }
    }
    public function reprocesar_serie($id)
    {
        $idEmpresa = '0993075000001';
        $planilla = Planilla_Cabecera_Labs::findOrFail($id);
        if ($planilla->aprobado == 1) {
            return response()->json([
                'estado'    => 'error',
                'respuesta' => 'planilla ya aprobada',
                'valores'   => array()
            ]);
        }
        $controlador = false;
        $faltantes = array();
        $detalles = Planilla_Detalle_Labs::where('id_planilla_cabecera', $id)->where('check', 1)->where('estado', 1)->get();
        // dd($planilla, $detalles,$id);
        $usados = [];
        foreach ($detalles as $value) {
            $serie = InvInventarioSerie::where('serie', $value->serie)
                ->where('id_empresa', $idEmpresa)
                ->where('id_bodega', $value->id_bodega)
                ->where('estado', 1)
                ->first();
            if (!is_null($serie)) {
                if ($serie->producto->codigo_siempre == 1) {
                    $nueva_serie = null;
                    /*********************CAMBIOS PARA LAS CANTIDADES EN PRODUCTOS******************************/
                    if (isset($usados[$serie->producto->id][$value->serie])) {
                        $restante = $serie->existencia - ($usados[$value->producto->id][$value->serie] + $value->cantidad);
                        if ($restante < 0) {
                            $inv_series = InvInventarioSerie::where('id_producto', $value->producto->id)
                                ->where('id_empresa', $idEmpresa)
                                // ->where('id', '<>', $serie->id)
                                ->where('id_bodega', $value->id_bodega)
                                ->where('existencia', '>=', $value->cantidad)
                                ->get();
                            foreach ($inv_series as $inv_serie) {
                                if (isset($usados[$value->producto->id][$inv_serie->serie])) {
                                    $resta = $inv_serie->existencia - ($usados[$value->producto->id][$inv_serie->serie] + $value->cantidad);
                                    if ($resta >= 0) {
                                        $usados[$value->producto->id][$inv_serie->serie] += $value->cantidad;
                                        $nueva_serie = $inv_serie;
                                    }
                                } else {
                                    $usados[$value->producto->id][$inv_serie->serie] = $value->cantidad;
                                    $nueva_serie = $inv_serie;
                                }
                            }
                            $controlador = true;
                        } else {
                            $usados[$value->producto->id][$value->serie] += $value->cantidad;
                            $nueva_serie = $serie;
                        }
                    } else {
                        if ($serie->existencia >= $value->cantidad) {
                            $nueva_serie = $serie;
                            $usados[$value->producto->id][$value->serie] = $value->cantidad;
                        } else {
                            $nueva_serie = InvInventarioSerie::where('id_producto', $value->producto->id)
                                ->where('id_empresa', $idEmpresa)
                                // ->where('id', '<>', $serie->id)  
                                ->where('id_bodega', $value->id_bodega)
                                ->where('existencia', '>=', $value->cantidad)
                                ->first();
                            if (!is_null($nueva_serie)) {
                                $usados[$serie->producto->id][$nueva_serie->serie] = $value->cantidad;
                            }
                            $controlador = true;
                        }
                    }

                    if ($controlador) {
                        if (!is_null($nueva_serie)) {
                            DB::beginTransaction();
                            // $nuevo_pedido = Movimiento::where('movimiento.serie', $nueva_serie->serie)
                            //     ->join('pedido', 'movimiento.id_pedido', '=', 'pedido.id')
                            //     ->where('pedido.id_empresa', Session::get('id_empresa'))
                            //     ->select('movimiento.*')
                            //     ->first();
                            try {
                                Planilla_Detalle_Labs::where('id', $value->id)
                                    ->update(['serie' => $nueva_serie->serie, 'ip_modificacion' => 'REPROCESO APROBADO', 'observacion' => 'REPROCESO APROBADO']);
                                DB::commit();
                            } catch (\Exception $e) {
                                DB::rollBack();

                                return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
                            }
                        } else {
                            $dd = array();
                            $dd['serie'] = $value->serie;
                            $dd['error'] = "No exiten productos en la Bodega";
                            $dd['producto'] = $value->codigo;
                            array_push($faltantes, $dd);
                        }
                    }
                }
            } else {
                $dd = array();
                $dd['serie'] = $value->serie;
                $dd['error'] = "No existe la serie";
                $dd['producto'] = $value->codigo;
                array_push($faltantes, $dd);
            }
        }
        if ($faltantes == array()) {
            return response()->json([
                'estado'    => 'okay',
                'respuesta' => 'Faltan Datos',
                'valores'   => $faltantes
            ]);
        } else {
            return response()->json([
                'estado'    => 'error',
                'respuesta' => 'Faltan Datos',
                'valores'   => $faltantes
            ]);
        }
    }
    public function getBodegas(Request $request)
    {
        if ($request->header('appid') === null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return response()->json(['status' => 'error', 'msj' => 'Creedenciales Invalidad']);
        }
        $getBodegas = [
            'status' => "",
            "msj"   => "",
            "detalles" => []
        ];
        $bodegas = Bodega::where('estado', 1)->where('id_empresa', '0993075000001')->get();

        if (count($bodegas) == 0) {
            $getBodegas["status"] = "error";
            $getBodegas["msj"] = "No hay bodegas registradas";
            return response()->json($getBodegas);
        }


        foreach ($bodegas as $bodega) {
            array_push($getBodegas["detalles"], ['id' => $bodega->id, 'nombre' => $bodega->nombre]);
        }
        $getBodegas["status"] = "success";
        $getBodegas["msj"] = "Si tiene bodegas";
        return response()->json($getBodegas);
    }
    public function getProductos(Request $request)
    {
        if ($request->header('appid') === null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return response()->json(['status' => 'error', 'msj' => 'Creedenciales Invalidad']);
        }
        $getProductos = [
            'status' => "",
            "msj"   => "",
            "detalles" => []
        ];

        $getProductos["detalles"]['serie'] = "";
        $id_producto = null;
        $inv_serie = InvInventarioSerie::where('serie', $request->nombreProducto)->where('id_empresa', '0993075000001')->first();

        if (!is_null($inv_serie)) {
            $getProductos["detalles"]['serie'] = $inv_serie->serie;
            $id_producto = $inv_serie->id_producto;
        }

        if (!is_null($id_producto)) {
            $productos = Producto::find($id_producto);
        } else {
            $productos = Producto::where('nombre', 'like', "%$request->nombreProducto%")
                ->orWhere('codigo', 'like', "%$request->nombreProducto%")
                ->orWhere('descripcion', 'like', "%$request->nombreProducto%")
                ->orWhere('id', $request->nombreProducto)
                ->get();
        }



        if (count($productos) == 0) {
            $getProductos["status"] = "error";
            $getProductos["msj"] = "No hay bodegas registradas";
            return response()->json($getProductos);
        }


        foreach ($productos as $producto) {
            array_push($getProductos["detalles"], ['id' => $producto->id, 'nombre' => $producto->nombre, 'codigo' => $producto->codigo]);
        }
        $getProductos["status"] = "success";
        $getProductos["msj"] = "Si tiene bodegas";
        return response()->json($getProductos);
    }

    public function getProductosUnico(Request $request)
    {
        if ($request->header('appid') === null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return response()->json(['status' => 'error', 'msj' => 'Creedenciales Invalidad']);
        }
        $getProductos = [
            'status' => "",
            "msj"   => "",
            "serie" => "",
            "id"    => "",
            "nombre" => "",
            "codigo" => "",
        ];

        // $getProductos['serie'] = "";
        $id_producto = null;
        if (!is_null($request->id_bodega)) {
            $invserie = InvInventarioSerie::where('serie', $request->nombreProducto)->where('id_bodega', $request->id_bodega)->where('id_empresa', '0993075000001')
                ->where('existencia', '>', 0)->first();
            if (!is_null($invserie)) {
                $getProductos["serie"] = $invserie->serie;
                $id_producto = $invserie->id_producto;
            }
        }
        $productos = null;
        if (!is_null($id_producto)) {
            $productos = Producto::where('id', $id_producto)->first();
        } else {
            $productos = Producto::where('codigo', $request->nombreProducto)->first();
            if (!is_null($productos)) {
                $invserie = InvInventarioSerie::where('id_producto', $productos->id)->where('id_bodega', $request->id_bodega)->where('id_empresa', '0993075000001')
                    ->where('existencia', '>', 0)->first();
                if (!is_null($invserie)) {
                    $getProductos["serie"] = $invserie->serie;
                }
            }
        }

        if (is_null($productos)) {
            $getProductos["status"] = "error";
            $getProductos["msj"] = is_null($productos) ?  "No existe el producto" :  "No tiene existencias";
            return response()->json($getProductos);
        }


        $getProductos['id'] = $productos->id;
        $getProductos['nombre'] = $productos->nombre;
        $getProductos['codigo'] = $productos->codigo;
        $getProductos["status"] = "success";
        $getProductos["msj"] = "Si tiene producto";
        return response()->json($getProductos);
    }


    public function getCostoPromedio(Request $request)
    {
        if ($request->header('appid') === null or $request->header('appid') != 'wOoQ5asvH02IyEJu5U46U4efJgEDjgDVZwHwlNf97UEENJwPi=') {
            return response()->json(['status' => 'error', 'msj' => 'Creedenciales Invalidad']);
        }
        $getProductos = [
            'status' => "",
            "msj"   => "",
            "costo" => 0,
        ];
        $costo_promedio = InvInventario::where('id_producto', $request->id_producto)->where('id_bodega', $request->id_bodega)->first();
        if (is_null($costo_promedio)) {
            $getProductos["status"] = "error";
            $getProductos["msj"] = "No tiene costo";
        }

        if (!is_null($costo_promedio)) {
            $getProductos["status"] = "success";
            $getProductos["costo"] = $costo_promedio->costo_promedio;
        }

        return response()->json($getProductos);
    }


    public function newbajarinsumos(Request $r, $idPlanilla, $total_derivado = 0)
    {
        DB::beginTransaction();
        try {
            $detalle_unico = "";
            $idUsuario = 'FACELECTRO';
            $reproceso = $this->reprocesar_serie($idPlanilla);
            $id_empresa = "0993075000001";
            $cab_movimiento = InvCabMovimientos::where('id_empresa', $id_empresa)->latest()->first();
            $total_asiento = 0;
            $bodega = Bodega::where("principal_labs", 1)->first();
            $planilla_cabecera_labs = Planilla_Cabecera_Labs::where('id', $idPlanilla)->first();

            if ($planilla_cabecera_labs->aprobado == 1) {
                DB::rollBack();
                return ["status" => false, "msj" => "Ya se encuentra aprobada la plantilla"];
            }
            $id_usuario = "FACELECTRO";
            $numero = "000000001";
            $ip_cliente = $_SERVER['REMOTE_ADDR'];
            if (!is_null($cab_movimiento)) {
                $numero = str_pad(intval($cab_movimiento->numero_documento) + 1, 9, "0", STR_PAD_LEFT);
            }
            $idEmpresa = '0993075000001';
            $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'EPL')->where('id_empresa', $idEmpresa)->first();

            if (is_null($inv_documento_bod)) {
                return ['status' => false, "msj" => 'No tiene configurado el documento de egreso'];
            }
            if (count($planilla_cabecera_labs->detalles) > 0) {
                foreach ($planilla_cabecera_labs->detalles as $row) {
                    if ($row->check == 1) {

                        $total_asiento +=  $row->precio;
                        $inv_serie = InvInventarioSerie::where('id_empresa', $idEmpresa)
                            ->where('serie', $row->serie)
                            ->where('id_bodega', $row->id_bodega)
                            ->first();
                        $producto = Producto::find($row->id_producto);
                        if (isset($producto->id)) {
                            $cantidad_uso = $row->cantidad * $producto->usos;
                        } else {
                            $cantidad_uso = 0;
                        }
                        $examen_orden = ["id_paciente" => $r->examen_orden['id_paciente'], "id" => $r->examen_orden['id']];

                        $detalle_unico = "{$r->info_adicional['id_orden']}-{$r->info_adicional['empresa_info']}";
                        if (!is_null($inv_serie)) {
                            $kardex = KardexUsoLabs::documentoEgresoPaciente($inv_serie, null, null, $row->cantidad, $cantidad_uso, "", $row->id_bodega, $row->id, $examen_orden,$detalle_unico );

                            if (!$kardex["status"]) {
                                return $kardex;
                            }
                        }
                    }
                }
            }
            $id_asiento_cab = null;
            $arrayDetalleAsiento = [];
            $inv_contable_cab = InvContableCab::where('id_documento_bodega', $inv_documento_bod->id)->where('id_empresa', $idEmpresa)->first();

            if (!is_null($inv_contable_cab)) {
                $input_cabecera = [
                    'fecha_asiento'   => $r->examen_orden['fecha_orden'],
                    'fact_numero'     => $numero,
                    'id_empresa'      => $idEmpresa,
                    'observacion'     => "EXAMEN LAB PACIENTE: {$r->examen_orden['id_paciente']} ",
                    'valor'           => $total_asiento + $total_derivado,
                    'id_usuariocrea'  => $idUsuario,
                    'id_usuariomod'   => $idUsuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $id_asiento_cab = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

                foreach ($inv_contable_cab->detalles as $det_labs) {
                    $arrayAsientoDetale = [
                        'id_asiento_cabecera' => $id_asiento_cab,
                        'id_plan_cuenta'      => $det_labs->cuenta,
                        'descripcion'         => '',
                        'fecha'               => $r->examen_orden['fecha_orden'],
                        'debe'                => $det_labs->tipo == 'D' ? $total_asiento + $total_derivado  : 0,
                        'haber'               => $det_labs->tipo == 'H' ? $total_asiento + $total_derivado : 0,
                        'estado'              => '1',
                        'id_usuariocrea'      => $idUsuario,
                        'id_usuariomod'       => $idUsuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ];
                    Ct_Asientos_Detalle::create($arrayAsientoDetale);
                    array_push($arrayDetalleAsiento, $arrayAsientoDetale);
                }
            }
            if (!is_null($planilla_cabecera_labs)) {
                $planilla_cabecera_labs->aprobado = 1;
                $planilla_cabecera_labs->observacion = $r->observacion_planilla;
                $planilla_cabecera_labs->id_usuariomod = $id_usuario;
                $planilla_cabecera_labs->id_asiento_cabecera = $id_asiento_cab;
                $planilla_cabecera_labs->save();
            }

            Log_Api_Humanlabs::create([
                "id_planilla"           => $planilla_cabecera_labs->id,
                "id_examen_orden"       => $r->info_adicional['id_orden'],
                "observacion"           => "{$r->info_adicional['ciudad']} | {$r->info_adicional['id_orden']} | {$r->info_adicional['id_paciente']} | {$r->info_adicional['nombre_paciente']}",
                "id_asiento_cabecera"   => $id_asiento_cab,
                "id_usuariocrea"        => "FACELECTRO",
                "id_usuariomod"         => "FACELECTRO",
                "ip_creacion"           => "FACELECTRO",
                "ip_modificacion"       => "FACELECTRO",
            ]);

            DB::commit();
            return ['status' => true, "msj" => 'Guardado con exito', 'asiento' => $id_asiento_cab];
        } catch (Exception $e) {
            DB::rollBack();
            return ['status' => false, 'msj' => $e->getLine()];

            return array('code' => 500, 'status' => false, 'data' => '', 'msj' => 'Error:' . $e->getMessage(), 'linea' => $e->getLine());
        }
    }
    public static function validarStock($data)
    {
        $problemas = "";
        $id_empresa = '0993075000001 ';
        $status = 'success';
        $series_exis = [];
        $series_exis = $data['id_producto'];
        $inv_serie = InvInventarioSerie::where('id_producto', $series_exis)
            ->where('id_bodega', $data["id_bodega"])
            ->where('id_empresa', $id_empresa)
            ->where('existencia', '>=', $data['cantidad'])
            ->first();
        $bodega = Bodega::find($data["id_bodega"]);
        $producto = InvInventarioSerie::find($series_exis);
        if (is_null($inv_serie)) {
            $status = "error";
            $problemas .= "Error: Cantidad insuficiente o No se encontro en el inventario | id producto: {$series_exis} | bodega: {$bodega->nombre}";
        } else if ($inv_serie->existencia < $data['cantidad']) {
            $status = "error";
            $problemas .= "Error: Cantidad insuficiente | serie: {$inv_serie->serie} | bodega: {$bodega->nombre}";
        }
        return ['status' => $status, "problem" => $problemas];
    }
}
