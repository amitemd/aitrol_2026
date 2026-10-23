<?php

namespace Sis_medico\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Insumo_Plantilla_Tipo;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ap_Procedimiento;
use Sis_medico\ApProcedimiento;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Globales;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_Termino;
use Sis_medico\Empresa;
use Sis_medico\LogAsiento;
use Sis_medico\LogConfig;
use Sis_medico\Marca;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Producto;
use Sis_medico\Proveedor;
use Sis_medico\Tipo;
use Sis_medico\Validate_Decimals;

class BdProductosController extends Controller
{
    public function opcionesProductos(Request $r)
    {
        if ($r->opcion == '') {
            return 'Api Productos';
        } elseif ($r->opcion == 'saveProducto') {
            $arrayRespuestas = [];
            $datos = $r->input();
            $sucursal = $datos['sucursal'];
            DB::beginTransaction();
            try {
                $diaCreacion = 'Masi_Crear_Prod' . date('Y-m-d');
                $id_usuario = $datos['id_usuario'];
                $id_empresa = $datos['id_empresa'];
                $marca = $datos['marca'] != '' ? $datos['marca'] : $datos['nueva_marca'];
                $marca = Marca::where('nombre', $marca)->first();
                $tipo_producto = Tipo::where('nombre', $datos['tipo_producto'])->where('estado', 1)->first();
                if ($tipo_producto == '') {
                    $tipo_producto = 0;
                } else {
                    $tipo_producto = $tipo_producto->id;
                }
                $tipo_planilla = Insumo_Plantilla_Tipo::where('nombre', $datos['tipo_planilla'])->first();
                if ($tipo_planilla == '') {
                    $tipo_planilla = 0;
                } else {
                    $tipo_planilla = $tipo_planilla->id;
                }
                if ($marca != '') {
                    $id_marca = $marca->id;
                } else {
                    $id_marca = 'No';
                    $arrayMarca = [
                        'nombre' =>  $datos['marca'] != '' ? $datos['marca'] : $datos['nueva_marca'],
                        'descripcion' =>  $datos['marca'] != '' ? $datos['marca'] : $datos['nueva_marca'],
                        'id_usuariocrea' => $id_usuario,
                        'id_usuariomod' => $id_usuario,
                        'ip_creacion' => $diaCreacion,
                        'ip_modificacion' => $diaCreacion,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    $id_marca = Marca::insertGetId($arrayMarca);
                }
                $producto = Producto::where('codigo', $datos['codigo'])->where('estado', 1)->first();
                $ct_producto = Ct_productos::where('codigo', $datos['codigo'])->where('id_empresa', $id_empresa)->first();
                if (is_null($ct_producto)) {
                    $arrayCtProducto = [
                        'codigo' => $datos['codigo'],
                        'nombre' => $datos['nombre'],
                        'tipo' => $tipo_producto,
                        'descripcion' => $datos['descripcion'],
                        'grupo' => $datos['grupo'],
                        'precio1' => $datos['precio'],
                        'valor_total_paq' => $datos['precio'],
                        'estado_tabla' => 1,
                        'id_usuariocrea' => $id_usuario,
                        'id_usuariomod' => $id_usuario,
                        'ip_creacion' => $diaCreacion,
                        'ip_modificacion' => $diaCreacion,
                        'iva' => isset($datos['iva']) ? 1 : 0,
                        'id_empresa' => $id_empresa,
                    ];
                    /*DB::rollback();
                    $arrayCtProducto['t'] = 0;
                    return json_encode($arrayCtProducto);*/
                    $id_producto = Ct_productos::insertGetId($arrayCtProducto);
                    $arrayRespuestas['ct_producto'] = 'Creado';
                    $codigo = date("ymdHis") . rand(100, 999);
                    $tipoAp = $datos['tipo'] == 'EG MED' ? 'M' : ($datos['tipo'] == 'EG DES' ? 'IDE' : ($datos['tipo'] == 'EG INS' ? 'IME' : ($datos['tipo'] == 'EG ER' ? 'IRE' : '')));
                    $ivaInsu = 0;
                    $porcentaje10 = 0;
                    if ($tipoAp == 'EG INS' || $tipoAp == 'EG IDE' || $tipoAp == 'EG IRE' || $datos['tipo'] == 'EG DES') {
                        $ivaInsu = '0.12';
                        $porcentaje10 = '0.10';
                    }
                    $arr_medicamento = [
                        'descripcion' =>  $datos['nombre'],
                        'tipo' => $tipoAp,
                        'valor' =>  $datos['precio'],
                        'estado' => 1,
                        'iva' => $ivaInsu,
                        'porcentaje10' => $porcentaje10,
                        'tipo_i' => 'F',
                        'codigo' => $codigo,
                        'ip_creacion' => '::1',
                        'ip_modificacion' => $diaCreacion,
                        'id_usuariocrea' => $id_usuario,
                        'id_usuariomod' => $id_usuario,
                        'id_producto' => $id_producto,
                    ];
                    ApProcedimiento::create($arr_medicamento);
                    $arrayRespuestas['ap_procedimiento'] = 'Creado';
                    //return json_encode($arr_medicamento);
                } else {
                    $id_productos = $ct_producto->id;
                }
                if (!is_null($producto)) {
                    try {
                        $producto_insumo = Ct_productos_insumos::where('id_insumo', $producto->id)->where('id_producto', $id_productos)->first();
                        if (is_null($producto_insumo)) {
                            $insumo = [
                                'codigo_producto'       => $datos['codigo'],
                                'id_insumo'             => $producto->id,
                                'id_producto'           => $id_productos,
                                'id_usuariocrea'        => $id_usuario,
                                'id_usuariomod'         => $id_usuario,
                                'ip_creacion'           => '::1',
                                'ip_modificacion'       =>  $diaCreacion,
                            ];
                            Ct_productos_insumos::create($insumo);
                            $arrayRespuestas['ct_productos_insumos'] = 'Creado';
                        }
                    } catch (Exception $e) {
                        DB::rollback();
                        $arrayRespuestas['errorCt_Producto_Insumos'] = $e->getMessage();
                        $arrayRespuestas['linea'] = $e->getLine();
                        $arrayRespuestas['status'] = false;
                        $arrayRespuestas['sucursal'] = $sucursal;
                        return json_encode($arrayRespuestas);
                    }
                } else {
                    //array_push($no_vinc, $book->codigo);
                }
                $producto = Producto::where('codigo', $datos['codigo'])->where('estado', 1)->first();
                $producto_exis = Producto::where('codigo', $datos['codigo'])->where('estado', 1)->where('ip_modificacion', '<>', $diaCreacion)->first();
                $ct_producto = Ct_productos::where('codigo', $datos['codigo'])->where('id_empresa', $id_empresa)->first();
                if ($producto == '') {
                    $data = [
                        'nombre' => trim($datos['nombre']),
                        'descripcion' => $datos['descripcion'],
                        'estado' => 1,
                        'medida' => is_null($datos['medida']) ? '' : $datos['medida'],
                        'minimo' => is_null($datos['stock_min']) ? 0 : $datos['stock_min'],
                        'precio_compra' => $datos['precio'],
                        'codigo'            => $datos['codigo'],
                        'codigo_iess'       => $datos['codigo_iess'],
                        'despacho'          => is_null($datos['despacho']) ? 0 : $datos['despacho'],
                        'cantidad'          => $datos['cantidad'],
                        'tipo_producto'     => $tipo_producto,
                        'cantidad_unidad'   => isset($datos['cantidad_unidad']) ? $datos['cantidad_unidad'] : 0,
                        'id_marca'          => $id_marca,
                        'registro_sanitario' => isset($datos['registro_sanitario']) ? $datos['registro_sanitario'] : 0,
                        'usos'              => $datos['uso'],
                        'kardex_uso'        => isset($datos['kardex_uso']) ? $datos['kardex_uso'] : '',
                        'cantidad_usos_kardex' => isset($datos['cantidad_usos_kardex']) ? $datos['cantidad_usos_kardex'] : 0,
                        'codigo_siempre'    => isset($datos['codigo_siempre']) ? $datos['codigo_siempre'] : 0,
                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       =>  $diaCreacion,
                        'ip_modificacion'   =>  $diaCreacion,
                        'iva'               => isset($datos['iva']) ? 1 : 0,
                        'descuento'         => isset($datos['descuento']) ? $datos['descuento'] : 0,
                        'tipo'              => $tipo_planilla,
                        'farmacia'          => isset($datos['farmacia']) ? $datos['farmacia'] : 0,
                    ];
                    /*DB::rollback();
                    $data['t'] = 1;
                    return json_encode($data);*/
                    //dd($producto, $ct_producto, $data);
                    $id_producto = Producto::insertGetId($data);
                    $arrayRespuestas['Producto'] = 'Creado';
                    $id_productos = $id_producto;
                } else {
                    $id_productos = $producto->id;
                }
                if (!is_null($ct_producto)) {
                    $producto_insumo = Ct_productos_insumos::where('id_insumo', $id_productos)->where('id_producto', $ct_producto->id)->first();
                    if (is_null($producto_insumo)) {
                        $insumo = [
                            'codigo_producto'       => $datos['codigo'],
                            'id_insumo'             => $id_productos,
                            'id_producto'           => $ct_producto->id,
                            'id_usuariocrea'        => $id_usuario,
                            'id_usuariomod'         => $id_usuario,
                            'ip_creacion'           => '::1',
                            'ip_modificacion'       =>  $diaCreacion,
                        ];
                        Ct_productos_insumos::create($insumo);
                        $arrayRespuestas['ct_productos_insumos_1'] = 'Creado';
                    }
                }
            } catch (Exception $e) {
                DB::rollback();
                $arrayRespuestas['errorGeneral'] = $e->getMessage();
                $arrayRespuestas['linea'] = $e->getLine();
                $arrayRespuestas['status'] = false;
                $arrayRespuestas['sucursal'] = $sucursal;
                return json_encode($arrayRespuestas);
            }
            $arrayRespuestas['sucursal'] = $sucursal;
            $arrayRespuestas['status'] = true;
            $arrayRespuestas['producto'] = Producto::where('codigo', $datos['codigo'])->first();
            $arrayRespuestas['ct_producto'] = Ct_productos::where('codigo', $datos['codigo'])->where('id_empresa', $id_empresa)->first();
            $arrayRespuestas['ct_productos_insumos'] = Ct_productos_insumos::where('codigo_producto', $datos['codigo'])->first();
            $arrayRespuestas['ap_procedimiento'] = ApProcedimiento::where('id_producto', $id_productos)->first();
            DB::commit();
            return json_encode($arrayRespuestas);
        } elseif ($r->opcion == 'traerTipoProducto') {
            $tipoProducto = Tipo::where('estado', 1)->orderBy('nombre', 'asc')->get(['nombre']);
            $tipoPlanilla = Insumo_Plantilla_Tipo::orderBy('nombre', 'asc')->get(['nombre']);
            $marcas = Marca::where('estado', 1)->orderBy('nombre', 'asc')->get(['nombre']);
            $arrayRespuesta = [
                'tipoProducto' => $tipoProducto,
                'tipoPlanilla' => $tipoPlanilla,
                'marcas' => $marcas,
            ];
            return json_encode($arrayRespuesta);
        } elseif ($r->opcion == 'buscarSecuencia') {
            $secuencia = 1;
            $producto = Producto::where('codigo', 'like', '%' . $r->tipo . '%')->max('codigo');
            if ($producto != '') {
                $secuencia = str_replace($r->tipo, ' ', $producto);
                $secuencia = (int)$secuencia + 1;
                $secuencia = str_pad($secuencia, 5, 0, STR_PAD_LEFT);
            } else {
                $secuencia = str_pad($secuencia, 5, 0, STR_PAD_LEFT);
            }
            return $secuencia;
        } elseif ($r->opcion == 'igualarAP') {
            $productos = Ct_productos::where('codigo', 'LIKE', '%EG MED%')
                ->orWhere('codigo', 'LIKE', '%EG INS%')
                ->orWhere('codigo', 'LIKE', '%EG DES%')
                ->orWhere('codigo', 'LIKE', '%EG ER%')
                ->get();
            $arrayRespuesta = [];
            if (count($productos) > 0) {
                $creado = 'ModificadoNombre' . date('Y-m-d');
                $tipo = '';
                foreach ($productos as $row) {
                    $cadena = $row->codigo;
                    $buscar = "EG MED";
                    $resultado = strpos($cadena, $buscar);
                    if ($resultado !== FALSE) {
                        $tipo = 'M';
                    }

                    $buscar = "EG INS";
                    $resultado = strpos($cadena, $buscar);
                    if ($resultado !== FALSE) {
                        $tipo = 'IME';
                    }

                    $buscar = "EG DES";
                    $resultado = strpos($cadena, $buscar);
                    if ($resultado !== FALSE) {
                        $tipo = 'IDE';
                    }

                    $buscar = "EG ER";
                    $resultado = strpos($cadena, $buscar);
                    if ($resultado !== FALSE) {
                        $tipo = 'IRE';
                    }
                    if ($tipo == 'M') {
                        $arrayAp = [
                            'descripcion' => $row->nombre,
                            'tipo' => $tipo,
                            'ip_creacion' => $creado,
                            'porcentaje10' => 0,
                            'iva' => '0'
                        ];
                    } else {
                        $arrayAp = [
                            'descripcion' => $row->nombre,
                            'tipo' => $tipo,
                            'ip_creacion' => $creado,
                            'porcentaje10' => '0.10',
                            'iva' => '0.12'
                        ];
                    }
                    Ap_Procedimiento::where('id_producto', $row->id)->update($arrayAp);
                    array_push($arrayRespuesta, $arrayAp);
                }
            }
            echo '<pre>';
            print_r($arrayRespuesta);
            exit;
        } else {
            return 'No tiene permisos para estas opciones';
        }
    }

    public function storeCompra(Request $request)
    {
        $fechahoy = $request['f_autorizacion'];
        $ip_cliente = '::1';
        $idusuario  = 'FACELECTRO';
        $empresa = Empresa::where('prioridad', 1)->first();
        $id_empresa = $empresa->id;
        $numero_factura = 0;
        $objeto_validar = new Validate_Decimals();
        try {
            $total_final = $objeto_validar->set_round($request['total_final1']);
            $cod_sucurs = Ct_Sucursales::where('estado', '1')->where('id', $request['sucursal'])->first();
            if (!is_null($cod_sucurs)) {
                $sucursal = $cod_sucurs->codigo_sucursal;
            } else {
                $sucursal = "0";
            }
            $cod_caj = Ct_Caja::where('estado', '1')->where('id', $request['punto_emision'])->first();
            if (!is_null($cod_caj)) {
                $punto_emision = $cod_caj->codigo_caja;
            } else {
                $punto_emision = "0";
            }
            $verificar_proveedor = BdProductosController::verificar_proveedor($request['prov']);
            if ($verificar_proveedor['status'] == 'error') {
                return $verificar_proveedor;
            }
            $total_final = $objeto_validar->set_round($request['total1']);
            $contador_ctv = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)
                ->where('punto_emision', $punto_emision)
                ->get()
                ->count();
            $numero_factura = 0;
            if ($contador_ctv == 0) {
                $num = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {
                $max_id = DB::table('ct_compras')->where('id_empresa', $id_empresa)->where('sucursal', $sucursal)->where('punto_emision', $punto_emision)->latest()->first();
                if ($max_id != '') {
                    $max_id = intval($max_id->secuencia_f);
                    if (strlen($max_id) < 10) {
                        $nu = $max_id + 1;
                        $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                    }
                } else {
                    $nu = 1;
                    $numero_factura = str_pad($nu, 9, "0", STR_PAD_LEFT);
                }
            }
            $numeroconcadenado = $request['serie'] . '-' . $request['secuencia_factura'];
            $comprobacion_compra = Ct_compras::where('numero', $numeroconcadenado)->where('proveedor', $request['proveedor'])
                ->where('tipo_comprobante', $request['tipo_comprobante'])
                ->where('id_empresa', $id_empresa)
                ->where('estado', '!=', '0')
                ->first();
            $subtotalf = $request['base1'];
            if ($comprobacion_compra == '') {
                $input = [
                    'observacion'                   => $request['observacion'],
                    'fecha_asiento'                 => $request['fecha'],
                    'fact_numero'                   => $request['secuencia_factura'],
                    'valor'                         => $total_final,
                    'id_empresa'                    => $id_empresa,
                    'estado'                        => '1',
                    'sucursal'                      => $sucursal,
                    'punto_emision'                 => $punto_emision,
                    'aparece_sri'                   => $request['archivosri'],
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                ];

                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input);
                $consulta_fecha = Ct_Termino::where('id', $request['termino'])->first();
                $modfecha = "";
                if ($consulta_fecha != null) {
                    $nueva_fecha = strtotime("+$consulta_fecha->dias day", strtotime($request['fecha']));
                    $modfecha = date("Y-m-d", $nueva_fecha);
                }
                $t = null;
                if ($request['tipo_comprobante'] == '03') {
                    $t = 0;
                }
                $sucursal = Ct_Sucursales::find($request['sucursal']);
                if (is_null($sucursal)) {
                    return ['status' => 'error', 'msj' => 'no existe sucursal registrada'];
                }
                $caja = Ct_caja::find($request['punto_emision']);
                if (is_null($caja)) {
                    return ['status' => 'error', 'msj' => 'no existe caja registrada'];
                }
                $input = [
                    'doc_electronico' => $t,
                    'tipo' => $request['tipo'],
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha' => $request['fecha'],
                    'numero' => $numeroconcadenado,
                    'archivo_sri' => $request['archivosri'],
                    'proveedor' => $request['proveedor'],
                    'termino' => $request['termino'],
                    'secuencia_f' => $numero_factura,
                    'observacion' => $request['observacion'],
                    'sucursal' => $sucursal->codigo_sucursal,
                    'punto_emision' => $caja->codigo_caja,
                    'valor_contable' => $total_final,
                    'fecha_termino' => $modfecha,
                    'f_caducidad' => $request['f_caducidad'],
                    'tipo_gasto' => $request['tipo_gasto'],
                    'autorizacion' => $request['autorizacion'],
                    'f_autorizacion' => $request['f_autorizacion'],
                    'id_empresa' => $id_empresa,
                    'serie' => $request['serie'],
                    //'serie' => $request['serie'] != '' ? $request['serie'] : $sucursal . '-' . $punto_emision,
                    'secuencia_factura' => $request['secuencia_factura'],
                    'credito_tributario' => $request['credito_tributario'],
                    'tipo_comprobante' => $request['tipo_comprobante'],
                    'subtotal_0' => $request['subtotal_01'],
                    'subtotal_12' => $request['subtotal_121'],
                    'subtotal' => $subtotalf,
                    'descuento' => $request['descuento1'],
                    'iva_total' => $request['tarifa_iva1'],
                    'ice_total' => $request['ice_final1'],
                    'electronica' => $request['tipo_electronica'],
                    'total_final' => $total_final,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'forma_pago' => $request['forma_pago'],
                ];
                $id_compra = Ct_compras::insertGetId($input);
                $arr_total = $request['detalle'];
                /*for ($i = 0; $i < count($request->input("nombre")); $i++) {
                    if ($request->input("nombre")[$i] != "" || $request->input("nombre")[$i] != null) {
                        $arr = [
                            'nombre' => $request->input("nombre")[$i],
                            'cantidad'       => $request->input("cantidad")[$i],
                            'codigo'         => $request->input("codigo")[$i],
                            'precio'         => $request->input("precio")[$i],
                            'descpor'        => $request->input("descpor")[$i],
                            'descuento'      => $request->input("desc")[$i],
                            'precioneto'     => $request->input("precioneto")[$i],
                            'detalle'        => $request->input("descrip_prod")[$i],
                            'iva'            => $request->input("iva")[$i],
                        ];
                        array_push($arr_total, $arr);
                    }
                }*/
                $cuentas_iva = [];
                foreach ($arr_total as $valor) {
                    $detalle = [
                        'id_ct_compras'        => $id_compra,
                        'codigo'               => $valor['codigo'],
                        'nombre'               => $valor['nombre'],
                        'cantidad'             => $valor['cantidad'],
                        'precio'               => $valor['precio'],
                        'descuento_porcentaje' => $valor['descpor'],
                        'estado'               => '1',
                        'descuento'            => $valor['descuento'],
                        'extendido'            => $valor['precioneto'],
                        'detalle'              => $valor['detalle'],
                        'iva'                  => $valor['iva'],
                        'porcentaje'           => $request['ivareal'],
                        'ip_creacion'          => $ip_cliente,
                        'ip_modificacion'      => $ip_cliente,
                        'id_usuariocrea'       => $idusuario,
                        'id_usuariomod'        => $idusuario,
                    ];

                    Ct_detalle_compra::create($detalle);
                    $fc = $valor['cantidad'] * $valor['precio'];
                    $plan_empresa = Plan_Cuentas_Empresa::where('id_plan', "LIKE", "%{$valor['codigo']}%")->orWhere('plan', "LIKE", "%{$valor['codigo']}%")->where('id_empresa', $id_empresa)->first();
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        // 'id_plan_cuenta'                => $valor['codigo'],
                        'id_plan_cuenta'                => $plan_empresa->id_plan,
                        'descripcion'                   => $valor['nombre'],
                        'fecha'                         => $fechahoy,
                        'haber'                         => '0',
                        'debe'                          => $fc,
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }

                $valor_iva = $request['tarifa_iva1'];
                $globales  = Ct_Globales::where('id_modulo', 1)->where('id_empresa', $id_empresa)->first();
                if ($globales == '') {
                    return ['status' => 'error', 'msj' => '¡Error!, Registre cuentas globales en la empresa' . ' ' . $id_empresa];
                }
                //1.01.05.01.02
                if ($valor_iva > 0) {
                    $plan = Plan_Cuentas::find($globales->debe);
                    if ($plan == '') {
                        return ['status' => 'error', 'msj' => '¡Error! Registre Plan de cuentas para la empresa ' . ' ' . $id_empresa];
                    }
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_plan_cuenta'                => $plan->id,
                        'descripcion'                   => $plan->nombre,
                        'fecha'                         => $fechahoy,
                        'haber'                         => '0',
                        'debe'                          => number_format($request['tarifa_iva1'], 2, '.', ''),
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }
                $consulta_proovedor = Proveedor::where('id', $request['proveedor'])->first();
                $cuenta_proveedor = $consulta_proovedor->id_cuentas;
                if ($cuenta_proveedor != 0) {
                    $desc_cuenta = Plan_Cuentas::where('id', $cuenta_proveedor)->first();
                    if ($desc_cuenta == '') {
                        return ['status' => 'error', 'msj' => '¡Error!, falta cuenta 2.01.03.01.01 proveedores'];
                    }
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera'           => $id_asiento_cabecera,
                        'id_plan_cuenta'                => $cuenta_proveedor,
                        'descripcion'                   => $desc_cuenta->nombre,
                        'fecha'                         => $fechahoy,
                        'haber'                         => $total_final,
                        'debe'                          => '0',
                        'estado'                        => '1',
                        'id_usuariocrea'                => $idusuario,
                        'id_usuariomod'                 => $idusuario,
                        'ip_creacion'                   => $ip_cliente,
                        'ip_modificacion'               => $ip_cliente,
                    ]);
                }
                $valor_descuento = number_format($request['descuento1'], 2, '.', '');
                if ($valor_descuento > 0) {
                    $cuenta_des_comp = \Sis_medico\Ct_Configuraciones::obtener_cuenta('FACTCONTABLE_DESC_COMP');
                    if (!is_null($cuenta_des_comp)) {
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera'           => $id_asiento_cabecera,
                            'id_plan_cuenta'                => $cuenta_des_comp->cuenta_guardar,
                            'descripcion'                   => $cuenta_des_comp->nombre_mostrar,
                            'fecha'                         => $fechahoy,
                            'debe'                          => '0',
                            'haber'                         => $valor_descuento,
                            'id_usuariocrea'                => $idusuario,
                            'id_usuariomod'                 => $idusuario,
                            'ip_creacion'                   => $ip_cliente,
                            'ip_modificacion'               => $ip_cliente,
                        ]);
                    } else {
                        return ['status' => 'error', 'msj' => '¡Error!, Falta registar configuracion FACTCONTABLE_DESC_COMP para la empresa ' . $id_empresa];
                    }
                }
                return ['status' => 'success', 'id' => $id_compra, 'id_asiento' => $id_asiento_cabecera];
            } else {
                return ['status' => 'error', 'msj' => '¡Error!, Coincidencia en las Facturas, ingrese otra factura con otros valores '];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => 'Error: ' . $e->getMessage() . ' ' . 'linea' . ' ' . $e->getLine()];
        }
    }

    public function asientos($request, $id_asiento_cabecera, $idusuario, $ip_cliente, $fechahoy, $subtotalf, $total1)
    {
        //dd($request);
        $id_empresa      = Empresa::where('prioridad', 1)->where('estado', 1)->first();

        $globales        = Ct_Globales::where('id_modulo', 1)->where('id_empresa', $id_empresa)->first();
        $valor_descuento = $request['descuento1'];
        $cuentas_iva     = 0;
        $base1           = $request['base1'];
        $total1          = $request['total1'];
        if ($valor_descuento > 0) {
            $base1  = $base1 + $valor_descuento;
            $total1 = $total1 + $valor_descuento;
        }
        $id_plan_confg = LogConfig::busqueda('1.01.03.01.02');

        $cuenta_prod_term = Plan_Cuentas::find($id_plan_confg);

        if ($cuenta_prod_term != '') {
            if ($request['tarifa_iva1'] > 0) {
                $global = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
                if ($global != '') {
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuenta_prod_term->id,
                        'descripcion'         => $cuenta_prod_term->nombre,
                        'fecha'               => $fechahoy,
                        'debe'                => $base1,
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                    $plan_cuentas = Plan_Cuentas::find($globales->debe);
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $plan_cuentas->id,
                        'descripcion'         => $plan_cuentas->nombre,
                        'fecha'               => $fechahoy,
                        'debe'                => $request['tarifa_iva1'],
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                } else {
                    return [
                        'msj' => 'No existe plan de cuenta para la empresa 1: ' . $id_empresa
                    ];
                }
            } else {
                $global = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
                //Aqui cambie
                if ($global != '') {
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuenta_prod_term->id,
                        'descripcion'         => $cuenta_prod_term->nombre,
                        'fecha'               => $fechahoy,
                        'debe'                => $total1,
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                } else {
                    return [
                        'msj' => 'No existe plan de cuenta para la empresa 2: ' . $id_empresa
                    ];
                }
            }
        } else {
            return [
                'msj' => 'No existe plan de cuenta para la empresa 3: ' . $id_empresa
            ];
        }
        if ($valor_descuento > 0) {
            $cuenta_proveedor = $request['proveedor'];
            if ($cuenta_proveedor != null) {
                $consulta_en_proveedor = Proveedor::where('id', $cuenta_proveedor)->first();
                if (($consulta_en_proveedor) != null && $consulta_en_proveedor != '[]') {
                    $desc_cuenta = Plan_Cuentas::where('id', $consulta_en_proveedor->id_cuentas)->first();
                    if ($desc_cuenta == '') {
                        return [
                            'msj' => 'La cuenta del proveedor no esta configurada correctamente para la empresa: ' . $id_empresa
                        ];
                    }
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $desc_cuenta->id,
                        'descripcion'         => $desc_cuenta->nombre,
                        'fecha'               => $fechahoy,
                        'haber'               => $request['total1'],
                        'debe'                => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                } else {
                }
            }
            $global_des = Ct_Globales::where('id_modulo', 21)->where('id_empresa', $id_empresa)->first();
            $id_plan_confg = Ct_Configuraciones::obtener_cuenta("FACTCONTABLE_DESC_COMP");
            $cuenta_desc_ven = Plan_Cuentas::find($id_plan_confg->cuenta_guardar);
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_desc_ven->id,
                'descripcion'         => $cuenta_desc_ven->nombre,
                'fecha'               => $fechahoy,
                'debe'                => '0',
                'haber'               => $valor_descuento,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
        } else {
            $cuenta_proveedor = $request['proveedor'];
            if ($cuenta_proveedor != null) {
                $consulta_en_proveedor = Proveedor::where('id', $cuenta_proveedor)->first();
                if (($consulta_en_proveedor) != null) {
                    $desc_cuenta = Plan_Cuentas::where('id', $consulta_en_proveedor->id_cuentas)->first();
                    if (!is_null($desc_cuenta)) {
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $desc_cuenta->id,
                            'descripcion'         => $desc_cuenta->nombre,
                            'fecha'               => $fechahoy,
                            'haber'               => $request['total1'],
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    } else {
                        $globalh = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
                        $id_plan_confg = LogConfig::busqueda('2.01.03.01.01');
                        $cuenta_locales = Plan_Cuentas::find($id_plan_confg);
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $cuenta_locales->id,
                            'descripcion'         => $cuenta_locales->nombre,
                            'fecha'               => $fechahoy,
                            'haber'               => $request['total1'],
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    }
                } else {
                    $globalh = Ct_Globales::where('id_modulo', 20)->where('id_empresa', $id_empresa)->first();
                    $id_plan_confg = LogConfig::busqueda('2.01.03.01.01');
                    $cuenta_locales = Plan_Cuentas::find($id_plan_confg);
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuenta_locales->id,
                        'descripcion'         => $cuenta_locales->nombre,
                        'fecha'               => $fechahoy,
                        'haber'               => $request['descuento1'],
                        'debe'                => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }
            } else {
            }
        }
    }

    public static function verificar_proveedor($data)
    {
        //return $data['id_proveedor'];
        try {
            $proveedor = Proveedor::where('id', $data['id_proveedor'])->first();
            if (is_null($proveedor)) {
                $id_proveedor = Proveedor::insertGetId([
                    'id'                => $data['id_proveedor'],
                    'razonsocial'       => strtoupper($data['razonsocial']),
                    'nombrecomercial'   => strtoupper($data['nombrecomercial']),
                    'ciudad'            => strtoupper($data['ciudad']),
                    'direccion'         => strtoupper($data['direccion']),
                    'id_tipoproveedor'  => '1',
                    'email'             => $data['email'],
                    'telefono1'         => $data['telefono1'],
                    'telefono2'         => $data['telefono2'],
                    'ip_creacion'       => '::1',
                    'ip_modificacion'   => '::1',
                    'id_usuariocrea'    => 'FACELECTRO',
                    'id_usuariomod'     => 'FACELECTRO',
                    'id_cuentas'        => '2.01.03.01.01',
                    'id_grupo'          => '2.01.03.01',
                ]);

                $proveedor = Proveedor::find($id_proveedor);
            }
            return ["status" => 'success', 'msj' => 'ok', 'data' => $proveedor];
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }
    public static function verificar_productos($data)
    {
        try {
            $empresa = Empresa::where('prioridad', 1)->first();
            $codigo = $data['codigo'];
            $prod = Ct_productos::where('codigo', $codigo)->where('estado_tabla', 1)->first();
            if (is_null($prod)) {

                $p_id = Ct_productos::insertGetId([
                    'codigo'                     => $codigo,
                    'nombre'                     => strtoupper($data['nombre']),
                    'descripcion'                => $data['nombre'],
                    'id_empresa'                 => $empresa->id,
                    'iva'                        => $data['iva'] != null ? $request['iva'] : 0,
                    'id_usuariocrea'             => 'CREAUSER',
                    'id_usuariomod'              => 'CREAUSER',
                    'ip_creacion'                => '::1',
                    'ip_modificacion'            => '::1'
                ]);
                $prod = Ct_productos::find($p_id);
            }
            return $prod;
        } catch (\Exception $e) {
            return ["status" => "error", "msj" => $e->getMessage(), "linea" => $e->getLine(), "archivo" => $e->getFile()];
        }
    }
}
