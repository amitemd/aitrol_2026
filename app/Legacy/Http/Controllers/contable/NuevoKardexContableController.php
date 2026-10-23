<?php

namespace Sis_medico\Http\Controllers\contable;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Sis_medico\Bodega;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvDetMovimientos;
use Sis_medico\InvDocumentosBodegas;
use Sis_medico\InvInventario;
use Sis_medico\InvInventarioSerie;
use Sis_medico\InvKardex;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\KardexUsoLabs;
use Sis_medico\Movimiento;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Producto;
use Sis_medico\Union_Bodega;
use Sis_medico\Venta_Kardex;

class NuevoKardexContableController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth');
   }


   public static function verificarKardex(Request $request)
   {
      $empresa = Empresa::find(Session::get('id_empresa'));

      $id_bodega = $request->id_bodega;
      $cantidad = $request->cantidad;
      $cod_producto_conta = $request->id_producto;
      $id_inv_serie = $request->inv_serie;

      if ($empresa->inventario == 0) {
         return ['status' => "success", "msj" => "No tiene habilitado el inventario"];
      }

      if ($empresa->egreso_kardex == 0) {
         return ['status' => "success", "msj" => "No tiene habilitado el egreso de kardex"];
      }

      $producto_contab =   Ct_productos::where('codigo', $cod_producto_conta)->where('id_empresa', $empresa->id)->first();
      if ($producto_contab->tipo_producto == 0) {
         return ['status' => "success", "msj" => "Producto es un servicio"];
      }
      if (is_null($producto_contab)) {
         return ['status' => "error", "msj" => "No se encontro el producto: {$cod_producto_conta}", "exp" => ""];
      }

      $union_bodega = Union_Bodega::where('id_bodega_cont', $id_bodega)->where('id_empresa', $empresa->id)->first();
      if (is_null($union_bodega)) {
         return ['status' => "error", "msj" => "No se encontro ligue de bodega", "exp" => ""];
      }



      $union_producto = Ct_productos_insumos::where('id_producto', $producto_contab->id)->first();
      if (is_null($union_producto)) {
         return ['status' => "error", "msj" => "El producto no se encuentra ligado con los productos de compras: <br>Cod: {$producto_contab->nombre}", "exp" => ""];
      }

      $inv_inventario = [];
      // DD($request->all());
      if (!is_null($id_inv_serie) and trim($id_inv_serie) != "") {
         $inv_inventario = InvInventarioSerie::where('id', $id_inv_serie)->where('id_bodega', $union_bodega->id_bodega_compra)->get();
         // dd($request->all(), $union_bodega,  $inv_inventario );

      } else {
         $inv_inventario = InvInventarioSerie::where('id_producto', $union_producto->id_insumo)->where('id_bodega', $union_bodega->id_bodega_compra)
            ->where('estado', 1)
            ->where('existencia', '>', 0)->groupBy('id_producto', 'id_bodega')->select(DB::raw('SUM(existencia) as existencia'), 'id_bodega', 'id_producto', 'id_empresa')
            ->where('id_empresa', Session::get('id_empresa'))->get();
         // dd($inv_inventario);
      }


      $existencia = 0;
      foreach ($inv_inventario as $inventario) {

         if ($inventario->id_empresa != $empresa->id) {
            return ["status" => "error", "msj" => "Mal registrada el inventario por la empresa"];
         }

         if ($inventario->existencia >= $cantidad) {
            return ["status" => "success", "msj" => "Si tiene existencia: {$inventario->existencia}"];
         } else {
            $existencia += $inventario->existencia;
         }
      }

      return ["status" => "error", "msj" => "Cantidades insuficientes en la bodega, solicite traslado de bodegas<br><b>Cantidad Solicitada: <b>{$cantidad} <br> <b>Cantidad en bodega: <b>{$existencia} "];
   }


   public static function generarKardex($id, $request)
   {
      $id_empresa = Session::get('id_empresa');
      $empresa = Empresa::find(Session::get('id_empresa'));
      if ($empresa->inventario == 0) {
         return ["status" => "success", "msj" => "La empresa no tiene inventario"];
      }

      $total_asiento = 0;
      try {

         $venta = Ct_ventas::find($id);

         $cab_movimiento = InvCabMovimientos::where('id_empresa', $id_empresa)->latest()->first();
         $id_usuario = Auth::user()->id;
         $numero = "000000001";
         $ip_cliente = $_SERVER['REMOTE_ADDR'];
         if (!is_null($cab_movimiento)) {
            $numero = str_pad(intval($cab_movimiento->numero_documento) + 1, 9, "0", STR_PAD_LEFT);
         }
         $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'EGV')->where('id_empresa', $id_empresa)->first();
         if (is_null($inv_documento_bod)) {
            return ['status' => 'error', 'msj' => "No tiene configurado documento de bodegas"];
         }

         $inv_transacciones_bodegas = InvTransaccionesBodegas::insertGetId([
            'id_documento_bodega'   => $inv_documento_bod->id,
            // 'id_bodega'             => $id_bodega,//cambiar por bodefa de empresa prioridad_labs
            'secuencia'             => 100,
            'id_empresa'            => $id_empresa,
            'id_usuariocrea'        => $id_usuario,
            'id_usuariomod'         => $id_usuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente
         ]);

         // $inv_trans_bodega = InvTransaccionesBodegas::where('id_documento_bodega', 18)->first();
         $data_cab_movimiento = [
            'id_documento_bodega'       => $inv_documento_bod->id,
            'id_transaccion_bodega'     => $inv_transacciones_bodegas,
            'id_bodega_origen'          => 1,
            // 'id_bodega_destino'=> null, 
            'numero_documento'          => $numero,
            // 'num_doc_ext'=> '', 
            // 'num_doc_cont'=> '', 
            'observacion'               => "EGRESO POR FACTURA VENTA: {$venta->id}, Numero: {$venta->numero}",
            'fecha'                     => date("Y-m-d", strtotime($venta->fecha)),
            'descuento'                 => 0,
            'subtotal'                  => 0,
            'subtotal_0'                => 0,
            'iva'                       => 0,
            'total'                     => 0,
            'estado'                    => 1,
            // 'id_movimiento_estado'=> '', 
            // 'id_pedido'=> '', 
            // 'id_agenda'=> '', 
            // 'id_docum_origen'=> '', 
            // 'id_hc_procedimientos'=> '', 
            // 'id_asiento'=> '', 
            'id_empresa'                => $id_empresa,
            'id_usuariocrea'            => $id_usuario,
            'id_usuariomod'             => $id_usuario,
            'ip_creacion'               => $ip_cliente,
            'ip_modificacion'           => $ip_cliente,
         ];

         $id_cab_movimiento = InvCabMovimientos::insertGetId($data_cab_movimiento);

         $data_asiento["observacion"] = "{$venta->concepto} / {$venta->nro_comprobante}";

         foreach ($venta->detalles as $value) {
            $aux_cantidad = $value->cantidad;
            $aux_precio = $value->extendido;
            $aux_bodega = $value->bodega;
            $aux_descuento = $value->descuento;
            $aux_cod_producto = $value->id_ct_productos;
            if ($value->check_iva == 1) {
               $aux_precio = ($value->extendido) + ($value->extendido * $value->porcentaje);
            }
            $aux_precio_unitario = $aux_precio / $aux_cantidad;
            // dd($request->all());

            foreach ($request->all() as $key => $arreglo_valor) {
               $request->request->remove($key);
            }

            //#FF0000
            $request->request->add(['id_bodega' => $aux_bodega, 'cantidad' => $aux_cantidad, 'id_producto' => $aux_cod_producto, "inv_serie" => $value->id_inv_serie]);
            $verificar_kardex = NuevoKardexContableController::verificarKardex($request);
            // dd($request->all());
            if ($verificar_kardex["status"] == "error") {
               return $verificar_kardex;
            }

            $producto_contab =   Ct_productos::where('codigo', $aux_cod_producto)->where('id_empresa', $empresa->id)->first();
            $union_producto = Ct_productos_insumos::where('id_producto', $producto_contab->id)->first();
            $union_bodega = Union_Bodega::where('id_bodega_cont', $aux_bodega)->where('id_empresa', $empresa->id)->first();

            //#FF0000
            $inv_inventario_busq = null;
            if (!is_null($value->id_inv_serie) and trim($value->id_inv_serie) != "") {
               $inv_inventario_busq = InvInventarioSerie::where('id', $value->id_inv_serie)->get();
            } else {
               $inv_inventario_busq = InvInventarioSerie::where('id_producto', $union_producto->id_insumo)->where('id_bodega', $union_bodega->id_bodega_compra)
                  ->where('id_empresa', Session::get('id_empresa'))->where('existencia', '>', 0)->get();
            }

            $id_bodega = $union_bodega->id_bodega_compra;
            $residuo_resta = 0;
            foreach ($inv_inventario_busq as $val_inventario) {
               // $inventario_existencias = $val_inventario->existencia;
               if ((float)$val_inventario->existencia >= $aux_cantidad) {
                  $residuo_resta    = $aux_cantidad;
                  $aux_cantidad     = 0;
               } else {
                  $residuo_resta = $val_inventario->existencia;
                  $aux_cantidad = $aux_cantidad - $val_inventario->existencia;
               }



               /*******************************MUEVE KARDEX *********************/
               // if(!is_null($value->id_inv_serie) and trim($value->id_inv_serie) != ""){
               $inv_inventario = InvInventario::find($val_inventario->id_inv_inventario);
               // }else{
               //    $inv_inventario = InvInventario::where('id_producto', $union_producto->id_insumo)->where('id_bodega', $id_bodega)->where('id_empresa', $id_empresa)->latest()->first();

               // }
               $data_inv_inventario = [
                  'id_producto' => $val_inventario->id_producto,
                  'id_bodega' => $id_bodega,
                  'tipo' => 'F',
                  'existencia' => intval($inv_inventario->existencia) - ($residuo_resta),
                  'existencia_uso' => intval($inv_inventario->existencia_uso) - ($residuo_resta),
                  'existencia_min' => $inv_inventario->existencia_min,
                  'existencia_max' => $inv_inventario->existencia_max,
                  'comprometido'  => intval($inv_inventario->comprometido) + (($residuo_resta)),
                  'comprometido_uso'  => intval($inv_inventario->comprometido_uso) + ($residuo_resta),
                  // 'costo_promedio'    => $,
                  // 'ubicacion'=> ,
                  'estado' => 1,
                  'id_empresa' => $id_empresa,
                  'id_usuariocrea' => $id_usuario,
                  'id_usuariomod' => $id_usuario,
                  'ip_creacion' => $ip_cliente,
                  'ip_modificacion' => $ip_cliente,
               ];

               $inv_inventario_update = InvInventario::where('id', $inv_inventario->id)->update($data_inv_inventario);
               // $id_inv_inventario = InvInventario::insertGetId($data_inv_inventario);

               $movimiento = Movimiento::where('serie', "%{$val_inventario->serie}%")->where('estado', 1)->first();

               $data_inv_det_movimiento = [
                  'id_inv_cab_movimientos'        => $id_cab_movimiento,
                  'id_producto'                   => $val_inventario->id_producto,
                  'serie'                         => $val_inventario->serie,
                  'fecha_vence'                   => date('Y-m-d', strtotime($val_inventario->fecha_vence)),
                  'lote'                          => $val_inventario->lote,
                  'id_inv_inventario'             => $inv_inventario->id,
                  'cantidad'                      => $residuo_resta,
                  'cant_uso'                      => $residuo_resta,
                  'valor_unitario'                => ($residuo_resta) * $aux_precio_unitario, //$planilla_detalle_labs->precio,
                  'subtotal'                      => ($residuo_resta) * $aux_precio_unitario, //$planilla_detalle_labs->precio,
                  'descuento'                     => !is_null($aux_descuento) ? $aux_descuento : 0,
                  'iva'                           => 0,
                  'total'                         => $residuo_resta * $aux_precio_unitario,
                  'estado'                        => 1,
                  'kardex'                        => 1,
                  'motivo'                        => "EGRESO FACT: {$venta->id} ",
                  // 'id_detalle_origen'=> '',
                  'id_pedido'                     => !is_null($movimiento) ? $movimiento->id_pedido : null,
                  // 'id_detalle_pedido'=> '',
                  // 'id_procedimiento'=> '',
                  // 'id_movimiento_paciente'=> '',
                  'id_usuariocrea'                => $id_usuario,
                  'id_usuariomod'                 => $id_usuario,
                  'ip_creacion'                   => $ip_cliente,
                  'ip_modificacion'               => $ip_cliente,
               ];
               $inv_det_movimiento = InvDetMovimientos::insertGetId($data_inv_det_movimiento);

               //#FF0000
               // if(!is_null($value->id_inv_serie)){
               $inv_serie = InvInventarioSerie::find($val_inventario->id);
               // }else{
               //    $inv_serie = InvInventarioSerie::where('id_empresa', $id_empresa)->where('serie', $val_inventario->serie)
               //    ->where('id_bodega', $union_bodega->id_bodega_compra)->first();
               // }

               if (!is_null($inv_serie)) {
                  if ($inv_serie->existencia > 0) {
                     $inv_serie->existencia = $inv_serie->existencia - ($residuo_resta);
                     $inv_serie->existencia_uso = $inv_serie->existencia_uso - ($residuo_resta);
                     $inv_serie->id_usuariomod = $id_usuario;
                     $inv_serie->ip_modificacion = $ip_cliente;
                     $inv_serie->save();
                  }
               }

               $data_inv_kardex = [
                  'id_inv_inventario' => $inv_inventario->id,
                  'id_bodega' => $union_bodega->id_bodega_compra,
                  'id_producto' => $val_inventario->id_producto,
                  'tipo' => 'E',
                  'descripcion' => 'EGP-FACT',
                  // 'referencia' => "EGRESO FACTURA: {$venta->numero}", se cambio xq no teniamos el numero al guardar la fc
                  'referencia' => "EGRESO FACTURA: {$venta->id}",
                  'fecha' => date('Y-m-d'),
                  'cantidad' => $residuo_resta,
                  'cant_uso' => $inv_serie->existencia,
                  // 'valor_unitario' => $aux_precio_unitario,
                  'valor_unitario' => $inv_inventario->costo_promedio,

                  // 'iva' => '', 
                  'total' => ($residuo_resta) * ($aux_precio_unitario),
                  'exist_cant' => $inv_inventario->existencia,
                  'exist_uso' => $inv_inventario->existencia_uso,
                  // 'exist_valor_unitario' => '', 
                  // 'exist_total' => '', 
                  'estado' => 1,
                  'id_documento_bodega' => $inv_documento_bod->id,
                  'id_inv_det_movimientos' => $inv_det_movimiento,
                  'id_empresa' => $id_empresa,
                  'id_usuariocrea' => $id_usuario,
                  'id_usuariomod' => $id_usuario,
                  'ip_creacion' => $ip_cliente,
                  'ip_modificacion' => $ip_cliente,
                  // 'dar_baja' => '', 
               ];
               $id_inv_kardex = InvKardex::insertGetId($data_inv_kardex);
               /*******************************FIN MUEVE KARDEX *********************/
               $total_asiento += $residuo_resta * $inv_inventario->costo_promedio;

               if ($aux_cantidad == 0) {
                  break;
               }
            }
         }

         $data_asiento["total"] = $total_asiento;
         $genera_asiento = NuevoKardexContableController::generaAsientoCosto($data_asiento);
         if ($genera_asiento["status"] == "error") {
            return $genera_asiento;
         }

         Venta_Kardex::create([
            'id_venta'           => $venta->id,
            'id_cab_moviento'    => $id_cab_movimiento,
            'id_usuariocrea' => $id_usuario,
            'id_usuariomod' => $id_usuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
         ]);

         return ["status" => "success", "msj" => "Se creo el kardex", "asiento_costo" => $genera_asiento["id_asiento"]];
      } catch (\Exception $e) {
         return ["status" => "error", "msj" => "No se pudo cargar", "exp" => $e->getMessage(), "e" => "Linea: {$e->getLine()}", "nuevo_kardex" => $e];
      }
   }


   public static function generaAsientoCosto($data)
   {
      //dd($data);
      try {


         $debe = Ct_Configuraciones::where('id_empresa', Session::get('id_empresa'))->where('tipo', 'COSTO_DEBE_PROCEDIMIENTO')->first();

         if (is_null($debe)) {
            return ["status" => "error", "msj" => "No tiene confugarado la cuenta debe (COSTO_DEBE_PROCEDIMIENTO)"];
         }

         $haber = Ct_Configuraciones::where('id_empresa', Session::get('id_empresa'))->where('tipo', 'COSTO_HABER_PROCEDIMIENTO')->first();

         if (is_null($haber)) {
            return ["status" => "error", "msj" => "No tiene confugarado la cuenta haber (COSTO_HABER_PROCEDIMIENTO)"];
         }

         $ip_cliente = $_SERVER["REMOTE_ADDR"];
         $id_usuario = Auth::user()->id;
         //cabecera
         $cab_asiento                  = new Ct_Asientos_Cabecera;
         $cab_asiento->observacion     = "COSTO VENTA: {$data['observacion']}";
         $cab_asiento->fecha_asiento   = date('Y-m-d');
         $cab_asiento->valor           = $data["total"];
         $cab_asiento->estado          = 1;
         $cab_asiento->id_empresa      = Session::get('id_empresa');
         $cab_asiento->modulo          = 'COSTO_PROCEDIMIENTO';
         $cab_asiento->ip_creacion     = $ip_cliente;
         $cab_asiento->ip_modificacion = $ip_cliente;
         $cab_asiento->id_usuariocrea  = $id_usuario;
         $cab_asiento->id_usuariomod   = $id_usuario;
         $cab_asiento->save();

         //detalle de debe
         $plan                             = Plan_Cuentas::where('id', $debe->id_plan)->first();
         $det_asiento                      = new Ct_Asientos_Detalle;
         $det_asiento->id_asiento_cabecera = $cab_asiento->id;
         $det_asiento->fecha               = date('Y-m-d');
         $det_asiento->id_plan_cuenta      = $debe->id_plan;
         $det_asiento->descripcion         = $plan->nombre;
         $det_asiento->debe                = $data["total"];
         $det_asiento->haber               = 0;
         $det_asiento->estado              = 1;
         $det_asiento->ip_creacion         = $ip_cliente;
         $det_asiento->ip_modificacion     = $ip_cliente;
         $det_asiento->id_usuariocrea      = $id_usuario;
         $det_asiento->id_usuariomod       = $id_usuario;
         $det_asiento->save();

         //detalle de haber
         $plan                             = Plan_Cuentas::where('id', $haber->id_plan)->first();
         $det_asiento                      = new Ct_Asientos_Detalle;
         $det_asiento->id_asiento_cabecera = $cab_asiento->id;
         $det_asiento->fecha               = date('Y-m-d');
         $det_asiento->id_plan_cuenta      = $haber->id_plan;
         $det_asiento->descripcion         = $plan->nombre;
         $det_asiento->haber               = $data["total"];
         $det_asiento->debe                = 0;
         $det_asiento->estado              = 1;
         $det_asiento->ip_creacion         = $ip_cliente;
         $det_asiento->ip_modificacion     = $ip_cliente;
         $det_asiento->id_usuariocrea      = $id_usuario;
         $det_asiento->id_usuariomod       = $id_usuario;
         $det_asiento->save();

         if (isset($data["id_cab_movimiento"])) {
            $inv_cab_movimiento  = InvCabMovimientos::find($data["id_cab_movimiento"]);
            $inv_cab_movimiento->id_asiento = $cab_asiento->id;
            $inv_cab_movimiento->save();
         }


         return ["status" => "success", "msj" => "Generado con exito", "id_asiento" => $cab_asiento->id];
      } catch (\Exception $e) {
         return ["status" => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage(), "e" => "NuevoKardexContr linea : {$e->getLine()}"];
      }
   }



   public static function newgenerarKardex($idPlanilla)
   {
      // DB::beginTransaction();
      try {
         $data_asiento["total"] = 0;

         $detalle_unico = "";
         $idUsuario = Auth::user()->id;
         $id_empresa = Session::get('id_empresa');
         $cab_movimiento = InvCabMovimientos::where('id_empresa', $id_empresa)->latest()->first();
         $total_asiento = 0;
         $ventas_cabecera = Ct_ventas::find($idPlanilla);

         $id_usuario = Auth::user()->id;
         $numero = "000000001";
         $ip_cliente = $_SERVER['REMOTE_ADDR'];
         if (!is_null($cab_movimiento)) {
            $numero = str_pad(intval($cab_movimiento->numero_documento) + 1, 9, "0", STR_PAD_LEFT);
         }
         $idEmpresa = Session::get('id_empresa');
         $inv_documento_bod = InvDocumentosBodegas::where('abreviatura_documento', 'EGV')->where('id_empresa', $idEmpresa)->first();

         $data_asiento["observacion"] = "{$ventas_cabecera->concepto} / {$ventas_cabecera->nro_comprobante}";
         if (is_null($inv_documento_bod)) {
            return ['status' => false, "msj" => 'No tiene configurado el documento de egreso'];
         }

         $genera_asiento = [
            "generate" => false,
            "status" => "error",
            "id_asiento" => null
         ];

         if (count($ventas_cabecera->detalles) > 0) {
            foreach ($ventas_cabecera->detalles as $row) {
               $ct_productos = Ct_productos::where('codigo', $row->id_ct_productos)->first();
               if (is_null($ct_productos)) {
                  return ['status' => "error", "msj" => 'Se encontro el producto con el codigo ' . $row->codigo . " | " . $row->nombre];
               }
               if ($ct_productos->tipo_producto == 1) {
                  // if ($ct_productos->tipo_producto == 1 && is_null($row->bodega)) {
                  //    dd($row);
                  // }
                  $ct_producto_insumos = Ct_productos_insumos::where('id_producto', $ct_productos->id)->first();

                  if (is_null($ct_producto_insumos)) {
                     return ['status' => "error", "msj" => 'No existe un ligue con un producto de insumos para el producto con el codigo ' . $row->codigo . " | " . $row->nombre];
                  } else if (is_null($ct_producto_insumos->id_insumo)) {
                     return ['status' => "error", "msj" => 'No existe un ligue con un producto de insumos para el producto con el codigo ' . $row->codigo . " | " . $row->nombre];
                  }

                  $union_bodega = Union_Bodega::where('id_bodega_cont', $row->bodega)->first();

                  if (is_null($union_bodega)) {
                     return ['status' => "error", "msj" => 'No existe un ligue con una bodega configure y vuelva a intentarlo'];
                  }

                  $producto = Producto::find($ct_producto_insumos->id_insumo);
                  $bodega_compra = Bodega::find($union_bodega->id_bodega_compra);

                  if (is_null($row->id_inv_serie) or $row->id_inv_serie == "") {
                     $inv_serie = InvInventarioSerie::where('id_empresa', $idEmpresa)
                        ->where('estado', '1')
                        ->where('id_producto', $producto->id)
                        ->where('id_bodega', $bodega_compra->id)
                        ->where('existencia', '>=', $row->cantidad)
                        ->first();
                  } else {
                     $inv_serie = InvInventarioSerie::find($row->id_inv_serie);
                  }


                  // $total_asiento +=  $row->precio;


                  if (is_null($inv_serie)) {
                     return ['status' => "error", "msj" => 'No hay suficiente stock para el producto con el codigo ' . $row->codigo . " | " . $row->nombre];
                  }

                  if (isset($producto->id)) {
                     $cantidad_uso = $row->cantidad * $producto->usos;
                  } else {
                     $cantidad_uso = 0;
                  }
                  $datos_factura = ["id_cliente" => $ventas_cabecera->id_cliente, "id" => $ventas_cabecera->id];

                  $data_asiento["total"] += ($inv_serie->inventario->costo_promedio * $row->cantidad);

                  $detalle_unico = "FV: {$ventas_cabecera->id}-{$ventas_cabecera->id_cliente}-{$ventas_cabecera->id_empresa}";
                  if (!is_null($inv_serie)) {
                     $kardex = NuevoKardexContableController::documentoEgresoCliente($inv_serie, $row->cantidad, $cantidad_uso, $bodega_compra->id, $row->id, $datos_factura, $detalle_unico);
                     if ($kardex["status"] == "error") {
                        return $kardex;
                     }

                     Venta_Kardex::create([
                        'id_venta'           => $ventas_cabecera->id,
                        'id_cab_moviento'    => $kardex['cab_mov_inv'],
                        'id_usuariocrea'     => $id_usuario,
                        'id_usuariomod'      => $id_usuario,
                        'ip_creacion'        => $ip_cliente,
                        'ip_modificacion'    => $ip_cliente,
                     ]);
                     InvCabMovimientos::calcularTotalCabMovimiento($kardex['cab_mov_inv']);
                     $data_asiento["id_cab_movimiento"] = $kardex['cab_mov_inv'];
                  }
               }
            }

            if ($data_asiento["total"] > 0) {
               $data['observacion'] = "{$ventas_cabecera->concepto} / {$ventas_cabecera->nro_comprobante}";
               $genera_asiento = NuevoKardexContableController::generaAsientoCosto($data_asiento);

               if ($genera_asiento["status"] == "error") {
                  return $genera_asiento;
               }
               $genera_asiento["generate"] = true;
            }




            // if ($genera_asiento["status"] == "error") {
            //    return $genera_asiento;
            // }
         }
         // DB::commit();
         return ["status" => "success", "msj" => "Se creo el kardex", "asiento_costo" => $genera_asiento["id_asiento"], "generate"=>$genera_asiento["generate"]];

         // return ['status' => 'status', "msj" => 'Guardado con exito'];
      } catch (\Exception $e) {
         // DB::rollBack();
         return ['status' => 'error', "msj" => $e->getMessage(), 'linea' => $e->getLine()];
      }
   }


   public static function documentoEgresoCliente($inv_serie, $cantidad, $cantidad_uso, $id_bodega_param = 2, $id_detalle_pla, $datos_factura, $detalle_unico = "")
   {
      try {
         $id_bodega = $id_bodega_param;
         $ip_cliente = $_SERVER['REMOTE_ADDR'];
         $idusuario = Auth::user()->id;
         $id_empresa = Session::get('id_empresa');
         $documento_bodega_abr = 'EGV';
         $cab_mov_inv = DB::table('inv_cab_movimientos as c')
            ->join('inv_documentos_bodegas as d', 'c.id_documento_bodega', '=', 'd.id')
            ->where('d.abreviatura_documento', $documento_bodega_abr)
            ->where('c.id_bodega_origen', $id_bodega)
            ->where('c.id_empresa', $id_empresa)
            ->where('c.estado', '!=', 0)
            ->where('c.data_humanlabs', $detalle_unico)
            ->select('c.*')
            ->first();
         if (!is_null($inv_serie->inventario)) {
            $valor_precio = $inv_serie->inventario->costo_promedio;
         } else {
            $movimiento = Movimiento::where('serie', $inv_serie->serie)->orderBy('created_at', 'Desc')->first();
            $valor_precio = $movimiento->precio;
         }
         if (!isset($cab_mov_inv->id)) {
            # creo la cabecera del traslado #
            $documento = InvDocumentosBodegas::where('abreviatura_documento', $documento_bodega_abr)->first();
            $secuencia = InvDocumentosBodegas::getSecueciaTipoDocum($id_bodega, $documento_bodega_abr);
            if ($secuencia != 0) {
               $transaccion = InvTransaccionesBodegas::where('id_documento_bodega', $documento->id)
                  ->where('id_bodega', $id_bodega)
                  ->first();
               $cab_mov_inv                        = new InvCabMovimientos;
               $cab_mov_inv->id_documento_bodega   = $documento->id;
               $cab_mov_inv->id_transaccion_bodega = $transaccion->id;
               $cab_mov_inv->id_bodega_origen      = $id_bodega;
               $cab_mov_inv->numero_documento      = str_pad($secuencia, 9, "0", STR_PAD_LEFT);
               $cab_mov_inv->observacion           = "EGRESO FACT CLIENTE: {$datos_factura['id_cliente']}, ID VENTA: {$datos_factura['id']}";
               $cab_mov_inv->fecha                 = date('Y-m-d');
               $cab_mov_inv->id_empresa            = $id_empresa;
               $cab_mov_inv->ip_creacion           = $ip_cliente;
               $cab_mov_inv->ip_modificacion       = $ip_cliente;
               $cab_mov_inv->id_usuariocrea        = $idusuario;
               $cab_mov_inv->id_usuariomod         = $idusuario;
               $cab_mov_inv->data_humanlabs        = $detalle_unico;
               $cab_mov_inv->save();
            }
         }
         # I V A
         $iva = 0;

         # TRAIGO EL DETALLE DEL INGRESO DEL ITEM
         $inicial = InvDetMovimientos::where('serie', $inv_serie->serie)
            ->where('estado', 1)
            ->orderBy('id', 'asc')
            ->first();
         # DETALLES
         $det_mov_inv                         = new InvDetMovimientos;
         $det_mov_inv->id_inv_cab_movimientos = $cab_mov_inv->id;
         // $det_mov_inv->id_producto            = $inv_serie->inventario->producto->id;
         $det_mov_inv->id_producto            = $inv_serie->id_producto;
         $det_mov_inv->id_inv_inventario      = $inv_serie->id_inv_inventario;
         // $det_mov_inv->id_procedimiento       = $id_hc_procedimientos;
         $det_mov_inv->cantidad               = $cantidad;
         $det_mov_inv->cant_uso               = $cantidad_uso;
         $det_mov_inv->serie                  = $inv_serie->serie;
         $det_mov_inv->lote                   = $inv_serie->lote;
         $det_mov_inv->fecha_vence            = $inv_serie->fecha_vence;
         $det_mov_inv->valor_unitario         = $valor_precio;
         // $det_mov_inv->subtotal               = $valor_precio;
         $det_mov_inv->subtotal               = ($valor_precio * $cantidad);
         $det_mov_inv->descuento              = 0;
         $det_mov_inv->iva                    = $iva;
         // $det_mov_inv->total                  = $inv_serie->inventario->costo_promedio + $iva;
         $det_mov_inv->total                  = ($valor_precio * $cantidad) + $iva;
         $det_mov_inv->motivo                 = $cab_mov_inv->observacion;
         if (isset($inicial->cabecera)) {
            $det_mov_inv->id_pedido = $inicial->cabecera->id_pedido;
         }
         // $det_mov_inv->id_movimiento_paciente = $id_movimiento_paciente;
         $det_mov_inv->ip_creacion            = $ip_cliente;
         $det_mov_inv->ip_modificacion        = $ip_cliente;
         $det_mov_inv->id_usuariocrea         = $idusuario;
         $det_mov_inv->id_usuariomod          = $idusuario;
         $det_mov_inv->save();

         $serie = $inv_serie->serie;
         $id_inv_det_mov = $det_mov_inv->id;
         //   $regreso = KardexUsoLabs::descontar_kardex_nuevo($serie, $id_bodega, $id_inv_det_mov, $id_detalle_pla);

         //   InvCabMovimientos::calcularTotalCabMovimiento($cab_mov_inv->id);
         //   if ($regreso ==  0 ) {
         $kardex = InvKardex::setKardex2($cab_mov_inv->id);
         if (!$kardex["status"]) {
            return ["status" => 'error', "msj" => "Error al registrar el kardex", "exp" => $kardex["exp"], "mod" => "setKardex2"];
         }
         //   }

         return ["status" => 'success', "cab_mov_inv" => $cab_mov_inv->id];
      } catch (\Exception $e) {
         // return $e->getMessage();
         return ["status" => 'error', "msj" => "Error al registrar el kardex", "exp" => $e->getMessage(), "mod" => "KardexUsoLabs"];
      }
   }
}
