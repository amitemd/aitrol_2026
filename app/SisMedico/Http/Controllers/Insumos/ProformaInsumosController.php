<?php

namespace Sis_medico\Http\Controllers\Insumos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Sis_medico\Bodega;
use DateTime;
use Illuminate\Auth\Recaller;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Ats;
use Sis_medico\Ct_Cliente_Retencion;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Cruce_Cuentas_Clientes;
use Sis_medico\Ct_Cruce_Valores_Cliente;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Credito_Clientes;
use Sis_medico\Ct_Detalle_Cruce_Clientes;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\InvCabMovimientos;
use Sis_medico\InvInventarioSerie;
use Sis_medico\Log_Contable;
use Sis_medico\Log_Venta_Costo;
use Sis_medico\LogAsiento;
use Sis_medico\LogConfig;
use Sis_medico\Permiso_Compra;
use Sis_medico\Producto;
use Sis_medico\PrecioProducto;
use Sis_medico\Procesado_Excel_Proforma_Cab;
use Sis_medico\Procesado_Excel_Proforma_Det;
use Sis_medico\Proforma_Detalle;
use Sis_medico\Proforma_Venta_Cab;
use Sis_medico\Proforma_Venta_Det;
use Sis_medico\Proforma_Ventas;
use Sis_medico\SecuenciaProforma;
use Sis_medico\Transito_Proforma;
use Sis_medico\Union_Bodega;
use Sis_medico\User;
use Sis_medico\Venta_Kardex;
use Sis_medico\Utilidades;
use PHPExcel_Worksheet_Drawing;
use PHPExcel_Style_Border;

class ProformaInsumosController extends Controller
{
   private $porIva = '';
   public function __construct()
   {
      $this->middleware('auth');
      $this->middleware(function (Request $request, \Closure $next): mixed {
         $this->porIva = Utilidades::porcentajeIva();

         if ($this->porIva === null) {
            return response()->view('errors.204', [
               'mensajes' => 'No se ha configurado el porcentaje del iva',
            ]);
         }

         return $next($request);
      });
   }

   public function index(Request $request)
   {

      //dd($request->all());

      $fecha = Proforma_Venta_Cab::whereBetween('created_at', [$request['fecha_desde'] . ' 00:00:00', $request['fecha_hasta'] . ' 23:59:59'])
         ->groupBy('id_cliente', 'descripcion', 'id_usuariocrea', 'id_usuariomod', 'tipo', 'estado')
         ->select('id_cliente', 'descripcion', 'id_usuariocrea', 'id_usuariomod', 'tipo', 'estado', DB::raw('COUNT(*) as cantidad'))
         ->get();


      ProformaInsumosController::validarFecha();

      $venta = DB::table('proforma_ventas_cab')
         ->join('users', 'proforma_ventas_cab.id_usuariocrea', '=', 'users.id')
         ->groupBy('id_usuariocrea')
         ->select('proforma_ventas_cab.id_usuariocrea', 'users.nombre1', 'users.nombre2', 'users.apellido2', 'users.apellido1')
         ->get();

      $aprueba = DB::table('proforma_ventas_cab')
         ->join('users', 'proforma_ventas_cab.id_usuariomod', '=', 'users.id')
         ->groupBy('id_usuariomod')
         ->select('proforma_ventas_cab.id_usuariomod', 'users.nombre1', 'users.nombre2', 'users.apellido2', 'users.apellido1')
         ->get();
      //dd($aprueba);


      // dd($request->all());
      $proforma_ventas_cab = ProformaInsumosController::busqueda($request->all());
      $valores = ProformaInsumosController::valores($request->all());

      $permiso_aprobado = Permiso_Compra::where('id_usuario', Auth::user()->id)->where('id_empresa', Session::get('id_empresa'))->where('permiso', '1')->first();
      $empresa = Empresa::find(Session::get('id_empresa'));

      return view("insumos/proforma/index", ["proformas" => $proforma_ventas_cab, "valores" => $valores, "permiso_aprobado" => $permiso_aprobado, "empresa" => $empresa, "nombre" => $venta, "aprueba" => $aprueba, "fecha" => $fecha, "data" => $request->all()]);
   }

   public static function busqueda($data)
   {
      $id_empresa = Session::get('id_empresa');
      $proforma_ventas_cab = Proforma_Venta_Cab::where('id_empresa', $id_empresa);
      if (Auth::user()->id_tipo_usuario == '17') {
         $proforma_ventas_cab = $proforma_ventas_cab->where('id_usuariocrea', Auth::user()->id);
      }

      if (count($data) > 0) {
         if (isset($data["descripcion"]) and !is_null($data["descripcion"])) {
            $proforma_ventas_cab = $proforma_ventas_cab->where('descripcion', "like", "%" . trim($data["descripcion"]) . "%");
         }
         if (isset($data['cliente']) and !is_null($data["cliente"])) {
            $cliente = Ct_Clientes::where('nombre', "like", "%" . trim($data["cliente"]) . "%")->orWhere('identificacion', "like", "%" . trim($data["cliente"]) . "%")->pluck('identificacion');
            $proforma_ventas_cab = $proforma_ventas_cab->whereIn('id_cliente', $cliente);
         }

         if (isset($data['id_usuariocrea']) && !is_null($data['id_usuariocrea'])) {
            $proforma_ventas_cab  = $proforma_ventas_cab->where('id_usuariocrea', $data['id_usuariocrea']);
         }

         if (isset($data['id_usuariomod']) && !is_null($data['id_usuariomod'])) {
            $proforma_ventas_cab  = $proforma_ventas_cab->where('id_usuariomod', $data['id_usuariomod']);
         }




         if (isset($data['estado']) && !is_null($data['estado'])) {
            if ($data['estado'] == 0) {
               $proforma_ventas_cab = $proforma_ventas_cab->where('estado', 0); // Anuladas
            } elseif ($data['estado'] == 1) {
               $proforma_ventas_cab = $proforma_ventas_cab->where('estado', 1); // Aprobadas
            } elseif ($data['estado'] == 2) {
               $proforma_ventas_cab = $proforma_ventas_cab->where('estado', 2); // Editables
            } elseif ($data['estado'] == 3) {
               $proforma_ventas_cab = $proforma_ventas_cab->where('estado', 3); // Caducadas
            }
         }

         if (isset($data['tipo']) && !is_null($data['tipo'])) {
            if ($data['tipo'] == 1) {
               $proforma_ventas_cab = $proforma_ventas_cab->where('tipo', 1); // proforma privada
            } else if ($data['tipo'] == 3) {
               $proforma_ventas_cab = $proforma_ventas_cab->where('tipo', 3); // Otra condici�n para tipo 3
            }
         }


         if (isset($data['fecha_desde']) && !is_null($data['fecha_desde']) && (isset($data['fecha_hasta']) && !is_null($data['fecha_hasta']))) {
            $proforma_ventas_cab  = $proforma_ventas_cab->whereBetween('created_at', [$data['fecha_desde'] . ' 00:00:00', $data['fecha_hasta'] . ' 23:59:59']);
         } else {
            if (isset($data['fecha_desde']) && !is_null($data['fecha_desde'])) {
               $proforma_ventas_cab  = $proforma_ventas_cab->where('created_at', '>=', $data['fecha_desde'] . ' 00:00:00');
            }
         }
      }


      return $proforma_ventas_cab->orderBy('id', 'Desc')->paginate(15);
   }

   public static function valores($data)
   {
      $id_empresa = Session::get('id_empresa');
      $proforma_ventas_cab = Proforma_Venta_Cab::where('id_empresa', $id_empresa)->where('estado', 1)->where('id_usuariocrea', Auth::user()->id)->get();
      $total = 0;
      foreach ($proforma_ventas_cab as $value) {
         foreach ($value->detalles as $val) {
            $total += $val->base0 + $val->base12;
         }
      }
      return ["total_proforma" => $total, "cantidad_proforma" => count($proforma_ventas_cab)];
   }


   public function store(Request $request)
   {
      $id_empresa = Session::get('id_empresa');
      $id_usuario = Auth::user()->id;
      $ip_usuario = $_SERVER['REMOTE_ADDR'];

      DB::beginTransaction();
      //ultimo secuencia y auementamos

      $tipo = 1;
      if (isset($request->type)) {
         $tipo = $request->type;
      }

      $secuencia_proforma = SecuenciaProforma::where('id_empresa', $id_empresa)->where("tipo", $tipo)->where('estado', 1)->first();
      $numeroConCeros = str_pad(1, 10, "0", STR_PAD_LEFT);

      if (!is_null($secuencia_proforma)) {
         $numeroConCeros = str_pad(intval($secuencia_proforma->secuencia) + 1, 10, "0", STR_PAD_LEFT);
         $secuencia_proforma->secuencia = $numeroConCeros;
         $secuencia_proforma->id_usuariomod   = $id_usuario;
         $secuencia_proforma->ip_modificacion = $ip_usuario;
         $secuencia_proforma->save();
      } else {
         $dataSecuencia = [
            "secuencia" => $numeroConCeros,
            "tipo" => $tipo,
            "estado" => 1,
            "id_empresa" => $id_empresa,
            "id_usuariocrea" => $id_usuario,
            "id_usuariomod" => $id_usuario,
            "ip_creacion" => $ip_usuario,
            "ip_modificacion" => $ip_usuario
         ];
         SecuenciaProforma::insert($dataSecuencia);
      }


      // $ultimo_secuencia = Proforma_Venta_Cab::latest()->first();

      // $cont = 0;

      // if (empty($ultimo_secuencia->secuencia)) {
      //    $cont = 1;
      // } else {
      //    $cont = $ultimo_secuencia->secuencia + 1;
      // }
      // $numeroConCeros = str_pad($cont, 10, "0", STR_PAD_LEFT);

      try {
         $input = [
            'descripcion'     => "",
            'id_empresa'      => $id_empresa,
            'estado'          => 2,
            'tipo'            => $tipo,
            'secuencia'       => $numeroConCeros,
            'id_usuariocrea'  => $id_usuario,
            'id_usuariomod'   => $id_usuario,
            'ip_creacion'     => $ip_usuario,
            'ip_modificacion' => $ip_usuario
         ];

         $id_proforma = Proforma_Venta_Cab::insertGetId($input);
         DB::commit();
         return ["status" => "success", "msj" => "Guardado con exito", "id_proforma" => $id_proforma];
      } catch (\Exception $e) {
         DB::rollBack();
         return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
      }
   }

   public function editar($id)
   {
      $id_empresa = Session::get('id_empresa');
      $proforma = Proforma_Venta_Cab::find($id);

      if ($proforma->estado == 1) {
         if ($proforma->tipo == 4) {
            return ProformaInsumosController::observarProformaSinKardex($proforma->id);
         } else {
            return redirect()->route('insumos.proformaInsumos.observarProforma', ["id_proforma" => $proforma->id]);
         }
      }
      if ($proforma->tipo == 4) {
         return $this->proformaSinInventarioEdit($proforma->id);
      }

      $bodegas = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->get();
      $detalle = ProformaInsumosController::details($proforma->id);

      $permiso_aprobado = Permiso_Compra::where('id_usuario', Auth::user()->id)->where('id_empresa', Session::get('id_empresa'))->where('permiso', '1')->first();

      if (Auth::user()->id_tipo_usuario == '17' and $proforma->pre_aprobada == 1) {
         return redirect()->route('insumos.proformaInsumos.observarProforma', ["id_proforma" => $proforma->id]);
      }

      return view("insumos/proforma/edit", ["proforma" => $proforma, "bodegas" => $bodegas, "detalle" => $detalle, "permiso_aprobado" => $permiso_aprobado]);
   }

   public function buscadorClientes(Request $request)
   {
      $clientes  = [];
      if ($request['search'] != null) {
         $clientes1 = Ct_Clientes::where('nombre', 'LIKE', "%{$request['search']}%")->where('estado', 1)->select('identificacion as id', DB::raw("CONCAT(identificacion , ' - ', nombre) as text"), 'email_representante as correo');
         $clientes2 = Ct_Clientes::where('identificacion', 'LIKE', "%{$request['search']}%")->where('estado', 1)->select('identificacion as id', DB::raw("CONCAT(identificacion , ' - ', nombre) as text"), 'email_representante as correo');
         $clientes = $clientes1->union($clientes2)->take(10)->get();
      }
      return response()->json($clientes);
   }

   public function buscador(Request $request)
   {
      $id_empresa = Session::get('id_empresa');
      $lote    = InvInventarioSerie::where('lote', 'like', "%{$request->buscador}%")->where('id_empresa', $id_empresa)->where('id_bodega', $request->bodega)->where('existencia', '>', 0);
      $serie   = InvInventarioSerie::where('serie', 'like', "%{$request->buscador}%")->where('id_empresa', $id_empresa)->where('id_bodega', $request->bodega)->where('existencia', '>', 0);

      $producto1   = InvInventarioSerie::where('inv_inventario_serie.id_empresa', $id_empresa)->where('inv_inventario_serie.id_bodega', $request->bodega)
         ->join('producto as p', 'inv_inventario_serie.id_producto', 'p.id')
         ->where('p.codigo', 'like', "%{$request->buscador}%")
         ->where('inv_inventario_serie.existencia', '>', 0)
         ->select("inv_inventario_serie.*");

      $producto2  = InvInventarioSerie::where('inv_inventario_serie.id_empresa', $id_empresa)->where('inv_inventario_serie.id_bodega', $request->bodega)
         ->join('producto as p', 'inv_inventario_serie.id_producto', 'p.id')
         ->where('p.nombre', 'like', "%{$request->buscador}%")
         ->where('inv_inventario_serie.existencia', '>', 0)
         ->select("inv_inventario_serie.*");

      $global = $lote->union($serie)->union($producto1)->union($producto2)->get();

      return ProformaInsumosController::table($global);
   }

   public static function table($data)
   {
      $tr = "";
      if (count($data) == 0) {
         return ["status" => "error", "msj" => "No se encontraron resultados"];
      }

      try {

         foreach ($data as $value) {
            $config = Ct_Configuraciones::find(3);
            $valor_iva = 0;
            if ($value->producto->iva == 1) {
               $valor_iva = $config->iva;
            }
            $tr .= "<tr>
                     <td>{$value->producto->codigo}</td>
                     <td>{$value->producto->nombre}</td>
                     <td>{$value->lote}</td>
                     <td>{$value->bodega->nombre}</td>
                     <td>{$value->fecha_vence}</td>
                     <td>{$value->inventario->costo_promedio}</td>
                     <td> <input type='hidden' id='existencia_{$value->id}' value='{$value->existencia}'> {$value->existencia}</td>
                     <td> <input type='text' class='form-control borde' id='espec_tecnica_{$value->id}' value=''> </td>
                     <td> <input type='text' class='form-control borde' id='espec_tecnica_ofertada_{$value->id}' value=''> </td>
                     <td> <input type='number' class='form-control borde' id='cantidad_{$value->id}' value='0'> </td>
                     <td> <input type='text' class='form-control borde' id='precio_{$value->id}' value='0'> </td>
                     <td> <input type='text' class='form-control borde' id='descuento_{$value->id}' value='0'> </td>
                     <td> 
                        <input type='hidden' id='iva_{$value->id}' value='{$valor_iva}'>
                        <input type='checkbox'" . ($valor_iva > 0 ? 'checked' : '') . " disabled >
                     </td>
                     <td><p id='total_{$value->id}'>0.00</p></td>
                     <td> <button class='btn btn-primary' onclick='agregarDetalle({$value->id})'>Agregar</button> </td>
                  </tr>";
         }
         $table = "<table class='table'>
                  <thead>
                     <tr style='background-color: #63cdda; color: white;'>
                        <th colspan='15' style='text-align: center;'>Informacion</th>
                     </tr>
                     <tr>
                        <th>Cod. Producto</th>
                        <th style='width:10%;'>Nombre</th>
                        <th>Lote</th>
                        <th>Bodega</th>
                        <th>Fecha Vence</th>
                        <th>Precio Referencia</th>
                        <th>Existencia</th>
                        <th>Espec. Tecnica</th>
                        <th>Espec. Tecnica Ofertada</th>
                        <th style='width: 8%;'>Cantidad</th>
                        <th style='width: 8%;'>Precio</th>
                        <th style='width: 8%;'>Descuento</th>
                        <th>Iva</th>
                        <th style='width: 8%;'>Total</th>
                        <th>Accion</th>
                     </tr>
                  </thead>
                  <tbody>
                     {$tr}
                  </tbody>
               </table>";

         return ["status" => "success", "msj" => "Exito", "table" => $table];
      } catch (\Exception $e) {
         return ["status" => "error", "msj" => "Ocurrio un error en la busqueda", "exp" => $e->getMessage()];
      }
   }

   public function updateDetails(Request $request)
   {
      $id_usuario = Auth::user()->id;
      $ip_usuario = $_SERVER['REMOTE_ADDR'];

      if ($request["accion"] == 0) {
         DB::beginTransaction();
         try {
            $proforma_detalle = Proforma_Venta_Det::find($request["id_detalle"]);
            $proforma_detalle->estado = 0;
            $proforma_detalle->id_usuariomod = $id_usuario;
            $proforma_detalle->ip_modificacion = $ip_usuario;
            $proforma_detalle->save();
            DB::commit();
            $table = ProformaInsumosController::details($proforma_detalle->id_proforma);
            return ["status" => "success", "msj" => "Eliminado con exito", "table" => $table];
         } catch (\Exception $e) {
            DB::rollBack();
            return ["status" => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage()];
         }
      }

      $validar = ProformaInsumosController::validarStock($request->all());

      if ($validar["status"] == "error") {
         return $validar;
      }


      DB::beginTransaction();
      try {
         $serie = InvInventarioSerie::find($request['id_inv_serie']);

         if (is_null($serie)) {
            DB::rollBack();
            return ["status" => "error", "msj" => "No se encontro este producto"];
         }

         $subtotal0 = 0;
         $subtotal12 = 0;

         $base0 = 0;
         $base12 = 0;
         $config = Ct_Configuraciones::find(3);
         // dd($serie->producto);
         if ($serie->producto->iva == 1) {
            $calcIva = (($request['precio'] * $request["cantidad"]) - $request['descuento']) * $config->iva;
            $subtotal12 = (($request['precio'] * $request["cantidad"]));
            $base12 = (($request['precio'] * $request["cantidad"])) - $request['descuento'];
            $total = $base12 + $calcIva;
         } else {
            $subtotal0 = (($request['precio'] * $request["cantidad"]));
            $base0 = (($request['precio'] * $request["cantidad"])) - $request['descuento'];
            $total = $base0;
         }

         $data_detalle = [
            "id_bodega"                       => $serie->id_bodega,
            "serie"                           => $serie->serie,
            "lote"                            => $serie->lote,
            "cantidad"                        => $request["cantidad"],
            "estado"                          => 1,
            "id_proforma"                     => $request->id,
            "id_usuariocrea"                  => $id_usuario,
            "id_usuariomod"                   => $id_usuario,
            "ip_creacion"                     => $ip_usuario,
            "ip_modificacion"                 => $ip_usuario,
            "id_inv_serie"                    => $serie->id,
            "precio"                          => $request["precio"],
            "descuento"                       => $request["descuento"],
            "iva"                             => $serie->producto->iva,
            "subtotal0"                       => $subtotal0,
            "base0"                           => $base0,
            "subtotal12"                      => $subtotal12,
            "base12"                          => $base12,
            "total"                           => $total,
            "porct_iva"                       => $config->iva,
            "especificacion_tecnica"          => $request->especificacion_tecnica,
            "especificacion_tecnica_ofertada" => $request->especificacion_tecnica_ofertada,
            "nombre_generico"                 => $serie->producto->nombre,
            "codigo"                          => $serie->producto->codigo,
            "registro_sanitario"              => $serie->producto->registro_sanitario
         ];

         Proforma_Venta_Det::create($data_detalle);

         DB::commit();
         $table = ProformaInsumosController::details($request->id);
         return ["status" => "success", "msj" => "Guardado con exito", "table" => $table["table"]];
      } catch (\Exception $e) {
         DB::rollback();
         return ["status" => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage()];
      }
   }

   public static function details($id_proforma)
   {
      $detalles = Proforma_Venta_Det::where('id_proforma', $id_proforma)->where('estado', 1)->get();
      $tr = "";
      foreach ($detalles as $detalle) {
         $tr .= "<tr id='fila_{$detalle->id}'>";

         if ($detalle->proforma_venta_cab->tipo == 3) {
            $tr .= "<td class='text-left'>
                     <input type='text' name='codigo' class='form-control' onchange='handleDetail(this, {$detalle->id})' value='{$detalle->codigo}'/>
                  </td>";
         } else {
            $tr .= "<td class='text-left'>{$detalle->inv_serie->producto->codigo}</td>";
         }


         if ($detalle->proforma_venta_cab->tipo != 3) {

            $tr .= "<td class='text-left'>{$detalle->inv_serie->producto->nombre}</td>";
         } else {
            $tr .= "<td class='text-left'>
                        <input type='text' class='form-control' name='nombre_generico' onchange='handleDetail(this, {$detalle->id})' value='{$detalle->nombre_generico}' />
                     </td>";
         }
         $tr .= "<td class='text-left'>{$detalle->lote}</td>
                  <td class='text-left'>{$detalle->inv_serie->bodega->nombre}</td>";
         if ($detalle->proforma_venta_cab->tipo == 3) {
            $tr .= "<td class='text-left'>";
            if ($detalle->mostrar == 1) {
               $tr .= "<input onchange='handleDetail(this, {$detalle->id})' type='text' class='form-control' name='registro_sanitario' value='{$detalle->registro_sanitario}'>";
            }
            $tr .= "</td>";

            $tr .= "<td class='text-left'>";
            if ($detalle->proforma_venta_cab->mostrar == 1) {
               $tr .= "<input onchange='editFila({$detalle->id}, {$detalle->id_inv_serie})' type='text' class='form-control' id='espec_tecnica_{$detalle->id}_{$detalle->id_inv_serie}' value='{$detalle->especificacion_tecnica}'>";
            }
            $tr .= "</td>";

            $tr .= "<td class='text-left'>";
            if ($detalle->proforma_venta_cab->mostrar == 1) {
               $tr .= "<input onchange='editFila({$detalle->id}, {$detalle->id_inv_serie})' type='text' class='form-control' id='espec_tecnica_ofertada_{$detalle->id}_{$detalle->id_inv_serie}' value='{$detalle->especificacion_tecnica_ofertada}'>";
            }
            $tr .= "</td>";
         }
         $tr .= "
                  <td>
                     <p>{$detalle->cantidad}</p>
                     <input type='hidden' class='form-control' name='cantidad_{$detalle->id}_{$detalle->id_inv_serie}' value='{$detalle->cantidad}'>
                  </td>";

         $tr .= "<td>
                     <input onchange='editFila({$detalle->id}, {$detalle->id_inv_serie})' type='text' class='form-control' id='precio_{$detalle->id}_{$detalle->id_inv_serie}' value='{$detalle->precio}'>
                  </td>
                  <td>
                     <input onchange='editFila({$detalle->id}, {$detalle->id_inv_serie})' type='text' class='form-control' id='descuento_{$detalle->id}_{$detalle->id_inv_serie}' value='{$detalle->descuento}'>
                  </td>
                  <td> <input type='checkbox'" . ($detalle->iva == 1 ? 'checked' : '') . " disabled > </td>
                  <td> <p>{$detalle->total}</p> </td>
                  <td style='display:flex; gap:3px; flex-wrap:wrap;'> 
                     
                     <button class='btn-general btn-eliminar' onclick='eliminarDetalle({$detalle->id})'>
                        <i class='fa fa-trash' aria-hidden='true'></i>
                     </button> 
                  </td>
               </tr>";
      }
      return ["table" => $tr];
   }

   public static function validarStock($data)
   {

      $serie = InvInventarioSerie::find($data["id_inv_serie"]);

      if (is_null($serie)) {
         return ["status" => "error", "msj" => "No se encontraron datos"];
      }

      if ($data['cantidad'] > $serie->existencia) {
         return ["status" => "error", "msj" => "La cantidad no debe ser mayor a la existencia:<br> <b>Existencias: {$serie->existencia}</b>"];
      }

      $producto_insumo = Ct_productos_insumos::where('id_insumo', $serie->id_producto)->first();

      if (is_null($producto_insumo)) {
         return ["status" => "error", "msj" => "Este producto no se encuentra ligado con Contabilidad, primero liguelo"];
      }

      $ct_producto = Ct_productos::where('id', $producto_insumo->id_producto)->where('id_empresa', Session::get('id_empresa'))->first();

      if (is_null($ct_producto)) {
         return ["status" => "error", "msj" => "No se encontro el producto contable en la empresa"];
      }

      $ligue_bodega = Union_Bodega::where('id_bodega_compra', $serie->id_bodega)->first();

      if (is_null($ligue_bodega)) {
         return ["status" => "error", "msj" => "No esta ligado con la bodega de contabilidad"];
      }

      return ["status" => "success", "msj" => "Puede continuar"];
   }

   public function updateCabecera(Request $request)
   {
      // 1 cliente, 2 Descripcion 3) fecha exp
      // dd($request->all());
      $ip_usuario = $_SERVER['REMOTE_ADDR'];
      $id_usuario = Auth::user()->id;
      $proforma = Proforma_Venta_Cab::find($request->id_proforma);

      if (is_null($proforma)) {
         return ["status" => "error", "msj" => " No se encontro la proforma"];
      }

      DB::beginTransaction();
      try {
         if ($request->tipo == 1) {
            $proforma->id_cliente = $request->valor;
         } else if ($request->tipo == 2) {
            $proforma->descripcion = $request->valor;
         } else if ($request->tipo == 3) {
            $proforma->fecha_exp = $request->valor;
         } else if ($request->tipo == 4) {
            $proforma->dias_plazo = $request->valor;
         } else if ($request->tipo == 5) {
            $proforma->correo_cliente = $request->valor;
         }
         $proforma->id_usuariomod = $id_usuario;
         $proforma->ip_modificacion = $ip_usuario;
         $proforma->save();
         DB::commit();
         return ["status" => "success", "msj" => "Guardado con exito"];
      } catch (\Exception $e) {
         DB::rollBack();
         return ["status" => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage()];
      }
   }

   public function aprobarProforma(Request $request)
   {
      $proforma = Proforma_Venta_Cab::find($request->id_proforma);

      if ($proforma->tipo == 4) {
         return ProformaInsumosController::aprobarProformaSinKardex($proforma->id);
      }

      if (is_null($proforma)) {
         return ["status" => "error", "msj" => "No se encontro la proforma"];
      }

      if (is_null($proforma->id_cliente)) {
         return ["status" => "error", "msj" => "Agregar un cliente"];
      }

      if (is_null($proforma->descripcion) or trim($proforma->descripcion) == '') {
         return ["status" => "error", "msj" => "Agregar un descripcion"];
      }

      if (is_null($proforma->fecha_exp)) {
         return ["status" => "error", "msj" => "Agrege la fecha de caducidad de la proforma"];
      }

      $detalles = Proforma_Venta_Det::where('id_proforma', $proforma->id)->where('estado', 1)->first();
      if (is_null($detalles)) {
         return ["status" => "error", "msj" => "Agregar un producto"];
      }



      DB::beginTransaction();

      try {
         $id_usuario = Auth::user()->id;
         $ip_modificacion = $_SERVER["REMOTE_ADDR"];
         $proforma->estado = 1;
         $proforma->id_usuariomod = $id_usuario;
         $proforma->ip_modificacion = $ip_modificacion;
         $proforma->save();
         if ($proforma->tipo == 2) {
            $transito_proforma = Transito_Proforma::where('id_proforma', $proforma->id)->where('estado', 1)->first();
            if (!is_null($transito_proforma)) {
               $detalles = Proforma_Venta_Det::where('id_proforma', $proforma->id)->where('estado', 1)->groupBy('id_proforma')
                  ->select(DB::raw('SUM(total) as total'), 'id_proforma')->first();

               $cab = [
                  "observacion" => "NOTA ENTREGA A CLIENTE: {$proforma->cliente->nombre}",
                  "fecha_asiento" => date("Y-m-d"),
                  "valor" => $detalles->total,
                  "estado" => 1,
                  "id_empresa" => Session::get('id_empresa'),
                  "id_usuariocrea" => $id_usuario,
                  "id_usuariomod" => $id_usuario,
                  "ip_creacion" => $ip_modificacion,
                  "ip_modificacion" => $ip_modificacion,
                  "modulo" => "PROFORMAS NOTA ENTREGA",
               ];
               $id_asiento = Ct_Asientos_Cabecera::insertGetId($cab);

               $ct_debe = Ct_Configuraciones::where('tipo', 'LIKE', '%NOTA_ENTREGA_TRANSITO_DEBE%')->where('id_empresa', Session::get('id_empresa'))->first();
               if (is_null($ct_debe) or is_null($ct_debe->id_plan)) {
                  DB::rollback();
                  return ["status" => "error", "msj" => "No se encontro la cuenta del Debe: <br> <b>NOTA_ENTREGA_TRANSITO_DEBE</b>"];
               }

               $ct_haber = Ct_Configuraciones::where('tipo', 'LIKE', '%NOTA_ENTREGA_TRANSITO_HABER%')->where('id_empresa', Session::get('id_empresa'))->first();
               if (is_null($ct_haber) or is_null($ct_haber->id_plan)) {
                  DB::rollback();
                  return ["status" => "error", "msj" => "No se encontro la cuenta del Haber: <br> <b>NOTA_ENTREGA_TRANSITO_HABER</b>"];
               }

               $detalle = [
                  "id_plan_cuenta" => $ct_debe->id_plan,
                  "descripcion" => $ct_debe->cuenta->nombre,
                  "fecha" => date("Y-m-d"),
                  "debe" => $detalles->total,
                  "haber" => 0,
                  "estado" => 1,
                  "id_usuariocrea" => $id_usuario,
                  "id_usuariomod" => $id_usuario,
                  "ip_creacion" => $ip_modificacion,
                  "ip_modificacion" => $ip_modificacion,
                  "id_asiento_cabecera" => $id_asiento

               ];

               Ct_Asientos_Detalle::create($detalle);

               $detalle["id_plan_cuenta"] = $ct_haber->id_plan;
               $detalle["descripcion"] = $ct_haber->cuenta->nombre;
               $detalle["fecha"] = date("Y-m-d");
               $detalle["debe"] = 0;
               $detalle["haber"] = $detalles->total;
               Ct_Asientos_Detalle::create($detalle);

               $transito_proforma->id_usuariomod = $id_usuario;
               $transito_proforma->ip_modificacion = $ip_modificacion;
               $transito_proforma->id_asiento = $id_asiento;
               $transito_proforma->save();

               DB::commit();
               return ["status" => "success", "msj" => "Aprobado con exito "];
            }
         }

         DB::commit();
         return ["status" => "success", "msj" => "Aprobado con exito"];
      } catch (\Exception $e) {

         DB::rollback();
         return ["status" => "error", "msj" => "Error al aprobar", "exp" => $e->getMessage()];
      }
   }

   public function observarProforma($id_proforma)
   {
      $proforma = Proforma_Venta_Cab::find($id_proforma);
      if ($proforma->tipo == 4) {
         return redirect()->route('insumos.proformaInsumos.observarProformaSinKardex', ["id" => $id_proforma]);
      }
      $detalles = Proforma_Venta_Det::where('estado', 1)->where('id_proforma', $proforma->id)->get();
      return view("insumos/proforma/view_proforma", ["proforma" => $proforma, "detalles" => $detalles, 'id_proforma' => $id_proforma]);
   }

   public function excelproforma(Request $request)
   {
      $proforma = Proforma_Venta_Cab::find($request['id_proforma']);
      $detalles = Proforma_Venta_Det::where('estado', 1)->where('id_proforma', $proforma->id)->get();

      $titulos = array("Cod.PRODUCTO", "NOMBRE", "LOTE", "BODEGA", "CANTIDAD", "PRECIO", "DESCUENTO", "IVA", "TOTAL");
      $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


      Excel::create('Proforma', function ($excel) use ($titulos, $posicion, $detalles, $proforma) {
         $excel->sheet('proforma', function ($sheet) use ($titulos, $posicion, $detalles, $proforma) {
            $sheet->mergeCells('C1:D1');
            $sheet->cell('C1', function ($cell) {
               // manipulate the cel
               $cell->setValue('PROFORMA');
               $cell->setFontWeight('bold');
               $cell->setFontSize(18);
               $cell->setAlignment('center');
            });
            if ($proforma->empresa->logo != null) {
               $sheet->mergeCells('A1:B3');
               $objDrawing = new PHPExcel_Worksheet_Drawing;
               if (file_exists(base_path() . '/storage/app/logo/logo1391707460001.png')) {
                  $objDrawing->setPath(base_path() . '/storage/app/logo/logo1391707460001.png');
                  $objDrawing->setCoordinates('A1');
                  $objDrawing->setHeight(320);
                  $objDrawing->setWidth(200);
                  $objDrawing->setWorksheet($sheet);
               }
            }

            // Datos empresa/cliente (ajusta los campos a tu modelo)
            $empresaNombre = ($proforma->empresa->nombrecomercial ?? $proforma->empresa->razonsocial ?? config('app.name'));
            $empresaDir    = trim(($proforma->empresa->direccion ?? '') . ' ' . ($proforma->empresa->ciudad->nombre ?? ''));

            $cliente       = $proforma->cliente;
            $shipName      = $cliente->nombre;
            $shipAddress   = $cliente->direccion;
            $shipCity      = ($cliente->ciudad->nombre ?? $cliente->ciudad ?? '');
            $shipPhone     = ($cliente->telefono1 ?? $cliente->telefono ?? '');

            $fechaDoc = $proforma->created_at->format('Y-m-d') ?? date('Y-m-d');
            $invoice  = $proforma->numero ?? $proforma->secuencia ?? $proforma->id;

            $sheet->cell('A5', function ($c) {
               $c->setValue('FROM:');
               $c->setFontWeight('bold');
            });
            $sheet->mergeCells('A6:E6');
            $sheet->cell('A6', function ($c) use ($empresaNombre) {
               $c->setValue(strtoupper($empresaNombre));
               $c->setFontWeight('bold');
            });
            $sheet->mergeCells('A7:E7');
            $sheet->cell('A7', function ($c) use ($empresaDir) {
               $c->setValue($empresaDir);
            });
            $descripcion = (string)($proforma->descripcion ?? '');
            $sheet->cell('A9', function ($c) {
               $c->setValue('DESCRIPCIÓN:');
               $c->setFontWeight('bold');
            });
            $sheet->mergeCells('B9:E9');
            $sheet->cell('B9', function ($c) use ($descripcion) {
               $c->setValue($descripcion);
               $c->setAlignment('left');
            });

            // --- Bloques fusionados
            $sheet->mergeCells('A11:E15');
            $sheet->mergeCells('F11:I15');

            // Valores seguros
            $shipName    = trim((string)($proforma->cliente->nombre ?? ''));
            $shipAddress = trim((string)($proforma->cliente->direccion_representante ?? ''));
            $shipCity    = trim((string)($proforma->cliente->ciudad_representante ?? $proforma->cliente->ciudad_representante ?? ''));
            $shipPhone   = trim((string)($proforma->cliente->telefono1_representante ?? $proforma->cliente->telefono1_representante ?? ''));

            $fechaDoc = $proforma->fecha
               ?: ($proforma->created_at ? $proforma->created_at->format('Y-m-d') : date('Y-m-d'));
            $invoice  = $proforma->numero ?? $proforma->secuencia ?? $proforma->id;

            // SHIP TO (todo en B9)
            $shipToText = "SHIP TO:\n"
               . ($shipName    !== '' ? $shipName    : 'Name') . "\n"
               . ($shipAddress !== '' ? $shipAddress : 'Address') . "\n"
               . ($shipCity    !== '' ? $shipCity    : 'City') . "\n"
               . ($shipPhone   !== '' ? $shipPhone   : 'Phone number');

            $sheet->cell('A11', function ($c) use ($shipToText) {
               $c->setValue($shipToText);
               $c->setValignment('top');
               $c->setBorder('thin', 'thin', 'thin', 'thin');
            });

            // DATE / INVOICE (todo en I9)
            $rightText = "DATE : {$fechaDoc}\nINVOICE : {$invoice}";
            $sheet->cell('F11', function ($c) use ($rightText) {
               $c->setValue($rightText);
               $c->setValignment('top');
               $c->setBorder('thin', 'thin', 'thin', 'thin');
            });



            // Envolver texto y alinear arriba en ambos bloques
            $sheet->getStyle('A11:F15')->getAlignment()
               ->setWrapText(true)
               ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);

            $sheet->getStyle('F11:I15')->getAlignment()
               ->setWrapText(true)
               ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);




            /*$sheet->mergeCells('A4:A4');
            $sheet->cell('A4', function ($cell) {
               // manipulate the cel
               $cell->setValue('CLIENTE');
               $cell->setFontWeight('bold');
               $cell->setAlignment('center');
               $cell->setBackground('#92CFEF');
               $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->mergeCells('A5:B5');
            $sheet->cell('A5', function ($cell) use ($proforma) {
               // manipulate the cel
               $cell->setValue($proforma->cliente->nombre);
               $cell->setFontWeight('bold');
               $cell->setAlignment('center');
            });


            $sheet->mergeCells('A7:A7');
            $sheet->cell('A7', function ($cell) {
               // manipulate the cel
               $cell->setValue('DESCRIPCION');
               $cell->setFontWeight('bold');
               $cell->setAlignment('center');
               $cell->setBackground('#92CFEF');
               $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });

            $sheet->mergeCells('A8:A8');
            $sheet->cell('A8', function ($cell) use ($proforma) {
               // manipulate the cel
               $cell->setValue($proforma->descripcion);
               $cell->setFontWeight('bold');
               $cell->setAlignment('center');
            });*/

            $sheet->mergeCells('A17:I17');
            $sheet->cell('A17', function ($cell) {
               // manipulate the cel
               $cell->setValue('DETALLE');
               $cell->setFontWeight('bold');
               $cell->setBackground('#92CFEF');
               $cell->setAlignment('center');
               $cell->setBorder('thin', 'thin', 'thin', 'thin');
            });


            $comienzo = 18;
            for ($i = 0; $i < count($titulos); $i++) {
               $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                  $cell->setValue($titulos[$i]);
                  $cell->setFontWeight('bold');
                  $cell->setAlignment('center');
                  $cell->setBorder('thin', 'thin', 'thin', 'thin');
               });
            }

            $comienzo++;

            $comienzo = 19;
            foreach ($detalles as $soporte) {
               $soporte->iva = $soporte->cantidad * $soporte->precio - $soporte->descuento;

               $datos_excel = [];
               array_push($datos_excel, $soporte->inv_serie->producto->codigo, $soporte->inv_serie->producto->nombre, $soporte->lote, $soporte->inv_serie->bodega->nombre, $soporte->cantidad, $soporte->precio, $soporte->descuento, $soporte->iva * 0.12, $soporte->total);


               for ($i = 0; $i < count($datos_excel); $i++) {
                  $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                     $cell->setValue($datos_excel[$i]);
                     $cell->setBorder('thin', 'thin', 'thin', 'thin');

                     $cell->setAlignment('center');
                  });
               }
               $comienzo++;
            }
         });
      })->export('xlsx');
   }



   public function excelproforma1()
   {


      $titulos = array("CODIGO", "NOMBRE", "LOTE", "BODEGA", "SERIE", "CANTIDAD", "PRECIO", "DESCUENTO");
      $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");


      Excel::create('Proforma', function ($excel) use ($titulos, $posicion) {
         $excel->sheet('proforma', function ($sheet) use ($titulos, $posicion) {


            $comienzo = 1;
            for ($i = 0; $i < count($titulos); $i++) {
               $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                  $cell->setValue($titulos[$i]);
                  $cell->setFontWeight('bold');
                  $cell->setAlignment('center');
                  $cell->setBackground('#92CFEF');
                  $cell->setBorder('thin', 'thin', 'thin', 'thin');
               });
            }
         });
      })->export('xlsx');
   }



   public function delete(Request $request)
   {
      $proforma = Proforma_Venta_Cab::find($request->id_proforma);
      // if ($proforma->estado == 1) {
      //    return ["status" => "error", "msj" => "La proforma esta aprobada no se puede eliminar"];
      // }

      if (!is_null($proforma->id_venta)) {
         return ["status" => "error", "msj" => "La proforma ya esta tiene una factura de venta asociada no se puede eliminar"];
      }

      if ($proforma->estado == 0) {
         return ["status" => "error", "msj" => "La proforma ha sido eliminada anteriormente"];
      }

      DB::beginTransaction();

      try {
         $id_usuario = Auth::user()->id;
         $ip_usuario = $_SERVER['REMOTE_ADDR'];

         if ($proforma->tipo == 2 and $proforma->estado == 1) {
            $transito_proforma = Transito_Proforma::where('id_proforma', $proforma->id)->where('estado', 1)->first();
            if (!is_null($transito_proforma)) {
               $anulacion = LogAsiento::anularAsiento($transito_proforma->id_asiento, "NE-P", "ANULACION DE LA NOTA DE ENTREGA:{$proforma->secuencia}");
               if ($anulacion["status"] == "error") {
                  return $anulacion;
               }
               $transito_proforma->estado = 0;
               $transito_proforma->id_usuariomod = $id_usuario;
               $transito_proforma->ip_modificacion = $ip_usuario;
               $transito_proforma->save();
            }
         }
         $proforma->estado          = 0;
         $proforma->id_usuariomod   = $id_usuario;
         $proforma->ip_modificacion = $ip_usuario;
         $proforma->save();

         DB::commit();
         return ['status' => "success", "msj" => "Eliminado con exito"];
      } catch (\Exception $e) {
         DB::rollBack();
         return ["status" => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage()];
      }
   }

   public function seleccionarProforma(Request $request)
   {
      $proforma = Proforma_Venta_Cab::find($request->id_proforma);
      $detalles = Proforma_Venta_Det::where('estado', 1)->where('id_proforma', $proforma->id)->get();

      $data = [
         "id_cliente"      => $proforma->id_cliente,
         "nombre_cliente"  => $proforma->cliente->nombre,
         "descripcion"     => $proforma->descripcion,
         "dias_plazo"      => $proforma->dias_plazo,
         "detalles"        => [],
      ];
      try {
         foreach ($detalles as $value) {
            $detalle = [];

            $datos_validar = [
               "id_inv_serie"    => $value->id_inv_serie,
               "cantidad"        => $value->cantidad,
            ];


            $validarSeries = ProformaInsumosController::validarStock($datos_validar);

            if ($validarSeries["status"] == "error") {
               DB::rollBack();
               return ["status" => "error", "msj" => "{$validarSeries['msj']} <br> Producto: {$value->inv_serie->producto->codigo} | {$value->inv_serie->producto->nombre}"];
            }

            $producto_insumo = Ct_productos_insumos::where('id_insumo', $value->inv_serie->id_producto)->first();
            // dd($value->inv_serie);

            $union_bodega = Union_Bodega::where('id_bodega_compra', $value->inv_serie->id_bodega)->where('id_empresa', Session::get('id_empresa'))->first();

            $ct_producto = Ct_productos::find($producto_insumo->id_producto);
            $detalle = [
               "id_producto"  => $ct_producto->id,
               "codigo"       => $ct_producto->codigo,
               "nombre"       => $ct_producto->nombre,
               "lote"         => $value->lote,
               "fecha_vence"  => date("d/m/Y", strtotime($value->inv_serie->fecha_vence)),
               "cantidad"     => $value->cantidad,
               "precio"       => $value->precio,
               "descuento"    => $value->descuento,
               "total"        => $value->total,
               "id_inv_serie" => $value->id_inv_serie,
               "bodega"       => $union_bodega->id_bodega_cont
            ];

            array_push($data["detalles"], $detalle);
         }

         return ["status" => "success", "data" => $data];
      } catch (\Exception $e) {
         return ["status" => "error", "msj" => "No se cargo la profoma", "exp" => $e->getMessage()];
      }
   }

   public static function validarFecha()
   {
      if (Auth::user()->id_tipo_usuario == 1) {
         $cab = Proforma_Venta_Cab::where('estado', '<>', 0)->whereNull('id_venta')->where("id_empresa", Session::get("id_empresa"))->where('tipo', 1)->get();
      } else {
         $cab = Proforma_Venta_Cab::where('estado', '<>', 0)->whereNull('id_venta')->where('id_usuariocrea', Auth::user()->id)->where('tipo', 1)->where("id_empresa", Session::get("id_empresa"))->get();
      }
      foreach ($cab as $value) {
         if (!is_null($value->fecha_exp)) {
            if (strtotime($value->fecha_exp) < strtotime(date("Y-m-d"))) {
               // dd("Exp: " . $value->fecha_exp, "Actual: " . date("Y-m-d"), "fecha caducidad mayor");
               $value->estado = 3;
               $value->save();
            }
         }
      }
   }

   public function pdf_proforma($id)
   {
      // return view('insumos.proforma.pdf_proforma');
      $porIva = $this->porIva;
      $proforma = Proforma_Venta_Cab::find($id);
      $vistaurl = "insumos.proforma.pdf_proforma";
      $view = \View::make($vistaurl, compact('proforma', 'porIva'))->render();
      $pdf = \App::make('dompdf.wrapper');
      $pdf->loadHTML($view);
      $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true,  'chroot' => base_path('/')]);
      $pdf->setPaper('A4', 'portrait');
      return $pdf->stream('Proforma de Venta-' . $id . '.pdf');
   }

   public function editarTransito($id_transito)
   {
      $transito_proforma = Transito_Proforma::where('id_transito', $id_transito)->where('id_empresa', Session::get('id_empresa'))->where('estado', 1)->first();
      if (is_null($transito_proforma)) {
         $proforma = ProformaInsumosController::creaProformaTransito($id_transito);
         $id_proforma = $proforma["id_proforma"];
      } else {
         $id_proforma = $transito_proforma->id_proforma;
      }

      $id_empresa = Session::get('id_empresa');
      $proforma = Proforma_Venta_Cab::find($id_proforma);
      $bodegas = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->get();
      $detalle = ProformaInsumosController::details($proforma->id);

      $permiso_aprobado = Permiso_Compra::where('id_usuario', Auth::user()->id)->where('id_empresa', Session::get('id_empresa'))->where('permiso', '1')->first();


      return view("insumos/proforma/edit", ["proforma" => $proforma, "bodegas" => $bodegas, "detalle" => $detalle, "permiso_aprobado" => $permiso_aprobado]);
   }


   public static function creaProformaTransito($id_transito)
   {
      $id_empresa = Session::get('id_empresa');
      $id_usuario = Auth::user()->id;
      $ip_usuario = $_SERVER['REMOTE_ADDR'];

      DB::beginTransaction();

      try {
         $cab_movimiento  = InvCabMovimientos::find($id_transito);
         $id_cliente = null;

         if (isset($cab_movimiento->bodega_destino)) {
            if (!is_null($cab_movimiento->bodega_destino->id_cliente)) {
               $clientes = Ct_Clientes::where('identificacion', $cab_movimiento->bodega_destino->id_cliente)->first();
               $id_cliente = $clientes->identificacion;
            }
         }
         $secuencia = ProformaInsumosController::secuenciaNotaEntrega();

         $input = [
            'descripcion'     => strtoupper("TRASLADO: {$cab_movimiento->observacion} - ({$cab_movimiento->numero_documento})"),
            'id_empresa'      => $id_empresa,
            'estado'          => 2,
            'id_cliente'      => $id_cliente,
            'id_usuariocrea'  => $id_usuario,
            'id_usuariomod'   => $id_usuario,
            'ip_creacion'     => $ip_usuario,
            'ip_modificacion' => $ip_usuario,
            'tipo'            => 2,
            'secuencia'       => $secuencia,
         ];

         $id_proforma = Proforma_Venta_Cab::insertGetId($input);

         foreach ($cab_movimiento->detalles as $value) {
            $inv_serie = InvInventarioSerie::where('serie', $value->serie)->where('id_bodega', $cab_movimiento->id_bodega_destino)->where('id_empresa', $cab_movimiento->id_empresa)
               ->where('estado', 1)->where('lote', $value->lote)->first();
            $config = Ct_Configuraciones::find(3);
            $valor_iva = 0;
            if ($value->producto->iva == 1) {
               $valor_iva = $config->iva;
            }
            $data  = [
               "id_bodega"       => $cab_movimiento->id_bodega_destino,
               "serie"           => $inv_serie->serie,
               "lote"            => $inv_serie->lote,
               "cantidad"        => $value->cantidad,
               "estado"          => "1",
               "id_proforma"     => $id_proforma,
               "id_usuariocrea"  => $id_usuario,
               "id_usuariomod"   => $id_usuario,
               "ip_creacion"     => $ip_usuario,
               "ip_modificacion" => $ip_usuario,
               "id_inv_serie"    => $inv_serie->id,
               "precio"          => 0,
               "descuento"       => 0,
               "total"           => 0,
               "base0"           => 0,
               "base12"          => 0,
               "subtotal12"      => 0,
               "subtotal0"       => 0,
               "iva"             => $value->producto->iva,
               "porct_iva"       => $valor_iva
            ];

            Proforma_Venta_Det::insert($data);
         }

         Transito_Proforma::create([
            'id_transito'     => $id_transito,
            'id_proforma'     => $id_proforma,
            'id_empresa'      => $id_empresa,
            'estado'          => 1,
            'id_usuariocrea'  => $id_usuario,
            'id_usuariomod'   => $id_usuario,
            'ip_creacion'     => $ip_usuario,
            'ip_modificacion' => $ip_usuario
         ]);

         DB::commit();
         return ["status" => "success", "msj" => "Guardado con exito", "id_proforma" => $id_proforma];
      } catch (\Exception $e) {
         DB::rollBack();
         return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
      }
   }

   public static function secuenciaNotaEntrega()
   {
      $id_empresa = Session::get('id_empresa');
      $secuencia = Proforma_Venta_Cab::where('id_empresa', $id_empresa)->where('tipo', 2)->count();
      $secuencia = str_pad((intval($secuencia) + 1), 9, "0", STR_PAD_LEFT);
      return $secuencia;
   }

   public function editarFila(Request $request)
   {
      DB::beginTransaction();
      try {
         $id_usuario = Auth::user()->id;
         $ip_usuario = $_SERVER['REMOTE_ADDR'];
         $proforma_det = Proforma_Venta_Det::find($request->id_detalle);

         $proforma = $proforma_det->proforma_venta_cab;
         if ($proforma->estado == 1) {
            DB::rollBack();
            return ["status" => "error", "msj" => "No se puede editar, ya fue aprobada"];
         }

         $subtotal0 = 0;
         $subtotal12 = 0;
         $base0 = 0;
         $base12 = 0;
         $config = Ct_Configuraciones::find(3);
         if ($proforma_det->inv_serie->producto->iva == 1) {

            $calcIva = ((floatval($request['precio']) * $proforma_det->cantidad) - $request['descuento']) * $config->iva;
            $subtotal12 = ((floatval($request['precio']) * $proforma_det->cantidad));
            $base12 = ((floatval($request['precio']) * $proforma_det->cantidad)) - $request['descuento'];
            $total = $base12 + $calcIva;

            // dd($request['precio'], $proforma_det->cantidad,  $request['descuento'], $config->iva);
            // dd($calcIva, $subtotal12, $base12, $total);
         } else {
            $subtotal0 = ((floatval($request['precio']) * $proforma_det->cantidad));
            $base0 = ((floatval($request['precio']) * $proforma_det->cantidad)) - $request['descuento'];
            $total = $base0;
         }



         $proforma_det->id_usuariomod                   = $id_usuario;
         $proforma_det->ip_modificacion                 = $ip_usuario;
         $proforma_det->precio                          = $request["precio"];
         $proforma_det->descuento                       = $request["descuento"];
         $proforma_det->iva                             = $proforma_det->inv_serie->producto->iva;
         $proforma_det->subtotal0                       = $subtotal0;
         $proforma_det->base0                           = $base0;
         $proforma_det->subtotal12                      = $subtotal12;
         $proforma_det->base12                          = $base12;
         $proforma_det->total                           = $total;
         $proforma_det->porct_iva                       = $config->iva;
         $proforma_det->especificacion_tecnica_ofertada = $request["espec_tecnica_ofertada"];
         $proforma_det->especificacion_tecnica          = $request["espec_tecnica"];

         $proforma_det->save();
         DB::commit();
         $table = ProformaInsumosController::details($proforma_det->id_proforma);
         return ["status" => "success", "msj" => "Guardado con exito", "table" => $table["table"]];
      } catch (\Exception $e) {
         DB::rollBack();
         return ["status" => "error", "msj" => "Error al editar", "linea" => $e->getLine(),  "exp" => $e->getMessage()];
      }
   }

   public function modalTransito()
   {
      return view('insumos/proforma/modal_proforma');
   }

   public function subirExcel(Request $request)
   {
      $subir_excel = ProformaInsumosController::procesarExcel($request); //Procesamos el excel que subimos

      if ($subir_excel["status"] == "error") {
         return $subir_excel;
      }

      try {

         $id_empresa = Session::get('id_empresa');
         $id_procesado = $subir_excel["id_proforma"];
         $procesado = Procesado_Excel_Proforma_Cab::find($id_procesado);
         $cab = [
            "descripcion"     => $procesado->observacion,
            "id_empresa"      => $id_empresa,
            "estado"          => 2,
            "id_usuariocrea"  => Auth::user()->id,
            "id_usuariomod"   => Auth::user()->id,
            "ip_creacion"     => $_SERVER['REMOTE_ADDR'],
            "ip_modificacion" => $_SERVER['REMOTE_ADDR'],
            "excel"           => $procesado->nombre_excel,
            "tipo"            => 1,
         ];
         $id_proforma = Proforma_Venta_Cab::insertGetId($cab);


         foreach ($procesado->detalles as $detalle) {
            $bodega = Bodega::where('nombre', 'LIKE', "%" . trim($detalle->bodega) . "%")->where('id_empresa', $id_empresa)->first();
            if (is_null($bodega)) {
               DB::rollBack();
               return ["status" => "error", "msj" => "La bodega no existe"];
            }

            $producto = Producto::where('codigo', 'LIKE', "%" . trim($detalle->codigo) . "%")->first();
            if (is_null($producto)) {
               DB::rollBack();
               return ["status" => "error", "msj" => "El producto no existe: <br> <b>{$detalle->codigo} - {$detalle->nombre}</b> "];
            }

            $cod_serie =  str_replace("-", "", $detalle->serie);

            $serie = InvInventarioSerie::where('id_empresa', $id_empresa)->where('id_producto', $producto->id)->where('id_bodega', $bodega->id)
               ->where('existencia', '>=', $detalle->cantidad)->where('serie', $cod_serie)->first();
            if (is_null($serie)) {
               $serie = InvInventarioSerie::where('id_empresa', $id_empresa)->where('id_producto', $producto->id)->where('id_bodega', $bodega->id)
                  ->where('existencia', '>=', $detalle->cantidad)->first();
               if (is_null($serie)) {
                  $serie = InvInventarioSerie::where('id_empresa', $id_empresa)->where('serie', $cod_serie)->where('id_bodega', $bodega->id)
                     ->where('existencia', '>=', $detalle->cantidad)->first();
                  if (is_null($serie)) {
                     DB::rollBack();
                     return ["status" => "error", "msj" => "La serie no existe o la cantidad no es suficiente:  <br> <b>{$detalle->codigo} - {$detalle->nombre}</b>"];
                  }
               }
            }


            $config = Ct_Configuraciones::find(3);
            $subtotal0 = 0;
            $subtotal12 = 0;

            $base0 = 0;
            $base12 = 0;
            if ($serie->producto->iva == 1) {
               $calcIva = (($detalle->precio * $detalle->cantidad) - $detalle->descuento) * $config->iva;
               $subtotal12 = (($detalle->precio * $detalle->cantidad));
               $base12 = (($detalle->precio * $detalle->cantidad)) - $detalle->descuento;
               $total = $base12 + $calcIva;
            } else {
               $subtotal0 = (($detalle->precio * $detalle->cantidad));
               $base0 = (($detalle->precio * $detalle->cantidad)) - $detalle->descuento;
               $total = $base0;
            }

            $data_detalle = [
               "id_bodega"       => $bodega->id,
               "serie"           => $serie->serie,
               "lote"            => $serie->lote,
               "cantidad"        => $detalle->cantidad,
               "estado"          => 1,
               "id_proforma"     => $id_proforma,
               "id_usuariocrea"  => Auth::user()->id,
               "id_usuariomod"   => Auth::user()->id,
               "ip_creacion"     => $_SERVER['REMOTE_ADDR'],
               "ip_modificacion" => $_SERVER['REMOTE_ADDR'],
               "id_inv_serie"    => $serie->id,
               "precio"          => $detalle->precio,
               "descuento"       => $detalle->descuento,
               "total"           => $total,
               "base0"           => $base0,
               "base12"          => $base12,
               "subtotal12"      => $subtotal12,
               "subtotal0"       => $subtotal0,
               "iva"             => $serie->producto->iva,
               "porct_iva"       => $config->iva,
            ];

            Proforma_Venta_Det::insert($data_detalle);
         }

         DB::commit();
         return ["status" => "success", "msj" => "Guardado con exito"];
      } catch (\Exception $e) {
         DB::rollBack();
         return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
      }


      // return redirect()->intended('/insumos/carga/inicial/index');
   }


   public static function procesarExcel($request)
   {
      DB::beginTransaction();
      try {
         $anio = date('Y');
         $mes = date('m');
         $observacion = $request->observacion;
         $id_empresa = Session::get('id_empresa');
         $nombre_original = $request['archivo']->getClientOriginalName();
         $extension       = $request['archivo']->getClientOriginalExtension();

         $nuevo_nombre    =   "proforma_" . $id_empresa . '_' . $anio . '_' . $mes . '_' . date('YmdHis') . "." . $extension;
         $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));

         $ruta_excel = base_path() . '/storage/app/avatars/' . $nuevo_nombre;
         $cabecera = [
            'nombre_excel'          => $ruta_excel,
            "id_empresa"            => $id_empresa,
            "id_usuariocrea"        => Auth::user()->id,
            "id_usuariomod"         => Auth::user()->id,
            "ip_creacion"           => $_SERVER["REMOTE_ADDR"],
            "ip_modificacion"       => $_SERVER["REMOTE_ADDR"],
            "observacion"           => $observacion,
         ];

         $id_proforma = Procesado_Excel_Proforma_Cab::insertGetId($cabecera);

         if ($r1) {
            Excel::filter('chunk')->formatDates(true, 'Y-m-d')->load($ruta_excel)->chunk(3000, function ($reader) use ($id_proforma) {
               foreach ($reader as $book) {
                  if (!is_null($book)) {
                     $detalle = [
                        "id_procesado_excel"    => $id_proforma,
                        "codigo"                => $book->codigo,
                        "serie"                 => $book->serie,
                        "bodega"                => $book->bodega,
                        "lote"                  => $book->lote,
                        "nombre"                => $book->nombre,
                        "cantidad"              => $book->cantidad,
                        "precio"                => $book->precio,
                        "descuento"             => $book->descuento,
                        "id_usuariocrea"        => Auth::user()->id,
                        "id_usuariomod"         => Auth::user()->id,
                        "ip_creacion"           => $_SERVER["REMOTE_ADDR"],
                        "ip_modificacion"       => $_SERVER["REMOTE_ADDR"],
                     ];
                     Procesado_Excel_Proforma_Det::create($detalle);
                  }
               }
            });
         }
         DB::commit();
         return ['status' => 'success', "msj" => 'Procesado con exito', 'id_proforma' => $id_proforma];
      } catch (\Exception $e) {
         DB::rollback();
         return ['status' => 'error', "msj" => 'Ocurrio un error...', "exp" => $e->getMessage()];
      }
   }
   public function updatePublica(Request $request)
   {
      DB::beginTransaction();
      try {


         $id_proforma = $request->id_proforma;
         $proforma = Proforma_Venta_Cab::find($id_proforma);
         $sqlUpdate = "UPDATE proforma_ventas_cab SET {$request->column} = '{$request->value}'  WHERE id = $id_proforma";

         $query = DB::update($sqlUpdate);
         DB::commit();
         return ["status" => "success", "msj" => "Guardado con exito"];
      } catch (\Exception $e) {
         DB::rollback();
         return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
      }
   }

   public function updatePublicaDetails(Request $request)
   {
      DB::beginTransaction();
      try {
         $sqlUpdate = "UPDATE proforma_venta_det SET {$request->column} = '{$request->value}'  WHERE id = $request->id";
         $query = DB::update($sqlUpdate);
         DB::commit();

         if ($request->column == "precio" or $request->column == "descuento" or $request->cantidad) {
            $recalcular = ProformaInsumosController::editarFilaSinInventario($request->id);
            return $recalcular;
         }

         return ["status" => "success", "msj" => "Guardado con exito"];
      } catch (\Exception $e) {
         DB::rollback();
         return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
      }
   }

   public function preaprobar(Request $request)
   {
      try {
         $proforma = Proforma_Venta_Cab::find($request->id_proforma);
         $proforma->pre_aprobada = 1;
         $proforma->save();

         DB::commit();
         return ["status" => "success", "msj" => "Pre aprobado con exito"];
      } catch (\Exception $e) {
         DB::rollback();
         return ["status" => "error", "msj" => "Error al guardar", "exp" => $e->getMessage()];
      }
   }

   public function proformaSinInventario(Request $request)
   {
      DB::beginTransaction();
      $id_empresa = Session::get('id_empresa');
      $id_usuario = Auth::user()->id;
      $ip_usuario = $_SERVER['REMOTE_ADDR'];
      try {
         $tipo = 4;
         // $secuencia_proforma = SecuenciaProforma::where('id_empresa', $id_empresa)->where("tipo", $tipo)->where('estado', 1)->first();
         $secuencia_proforma = SecuenciaProforma::where('id_empresa', $id_empresa)->where("tipo", $tipo)->where('estado', 1)->first();
         $numeroConCeros = str_pad(1, 10, "0", STR_PAD_LEFT);
         if (!is_null($secuencia_proforma)) {
            $numeroConCeros = str_pad(intval($secuencia_proforma->secuencia) + 1, 10, "0", STR_PAD_LEFT);
            $secuencia_proforma->secuencia = $numeroConCeros;
            $secuencia_proforma->id_usuariomod   = $id_usuario;
            $secuencia_proforma->ip_modificacion = $ip_usuario;
            $secuencia_proforma->save();
         } else {
            $dataSecuencia = [
               "secuencia" => $numeroConCeros,
               "tipo" => $tipo,
               "estado" => 1,
               "id_empresa" => $id_empresa,
               "id_usuariocrea" => $id_usuario,
               "id_usuariomod" => $id_usuario,
               "ip_creacion" => $ip_usuario,
               "ip_modificacion" => $ip_usuario
            ];
            SecuenciaProforma::insert($dataSecuencia);
         }
         // $numeroConCeros = str_pad(1, 10, "0", STR_PAD_LEFT);
         $input = [
            'descripcion'     => "",
            'id_empresa'      => $id_empresa,
            'estado'          => 2,
            'tipo'            => $tipo,
            'secuencia'       => $numeroConCeros,
            'id_usuariocrea'  => $id_usuario,
            'id_usuariomod'   => $id_usuario,
            'ip_creacion'     => $ip_usuario,
            'ip_modificacion' => $ip_usuario,
            'id_responsable'  => $id_usuario
         ];
         $id = Proforma_Venta_Cab::insertGetId($input);
         // dd($numeroConCeros);

         DB::commit();

         return redirect()->route('insumos.proformaInsumos.sin_inventario.edit', ["id" => $id]);
         // $proforma = Proforma_Venta_Cab::find($id);
         // if ($proforma->estado == 1) {
         //    return redirect()->route('insumos.proformaInsumos.observarProforma', ["id_proforma" => $proforma->id]);
         // }

         // $bodegas = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->get();
         // $detalle = ProformaInsumosController::details($proforma->id);

         // $permiso_aprobado = Permiso_Compra::where('id_usuario', Auth::user()->id)->where('id_empresa', Session::get('id_empresa'))->where('permiso', '1')->first();

         // // if (Auth::user()->id_tipo_usuario == '17' and $proforma->pre_aprobada == 1) {
         // //    return redirect()->route('insumos.proformaInsumos.observarProforma', ["id_proforma" => $proforma->id]);
         // // }

         // return view("insumos.proforma.proformaSinKardex", ["proforma" => $proforma, "bodegas" => $bodegas, "detalle" => $detalle, "permiso_aprobado" => $permiso_aprobado]);
      } catch (\Exception $e) {
         DB::rollback();
         return redirect()->route('insumos.proformaInsumos.index');
      }
   }

   public function proformaSinInventarioEdit($id)
   {
      $id_empresa = Session::get('id_empresa');
      $proforma = Proforma_Venta_Cab::find($id);
      if ($proforma->estado == 1) {
         return redirect()->route('insumos.proformaInsumos.observarProformaSinKardex', ["id_proforma" => $proforma->id]);
      } else if (Auth::user()->id_tipo_usuario == '17' and $proforma->pre_aprobada == 1) {
         return redirect()->route('insumos.proformaInsumos.observarProformaSinKardex', ["id_proforma" => $proforma->id]);
      }

      $bodegas = Bodega::where('id_empresa', $id_empresa)->where('estado', 1)->get();
      $detalle = ProformaInsumosController::detailsSinInventario($proforma->id);
      $permiso_aprobado = Permiso_Compra::where('id_usuario', Auth::user()->id)->where('id_empresa', Session::get('id_empresa'))->where('permiso', '1')->first();
      // dd($detalle);
      // if (Auth::user()->id_tipo_usuario == '17' and $proforma->pre_aprobada == 1) {
      //    return redirect()->route('insumos.proformaInsumos.observarProforma', ["id_proforma" => $proforma->id]);
      // }

      return view("insumos.proforma.sin_kardex.proformaSinKardex", ["proforma" => $proforma, "bodegas" => $bodegas, "detalle" => $detalle, "permiso_aprobado" => $permiso_aprobado]);
   }


   public function proformaSinInventarioBuscador(Request $request)
   {
      // dd($request->all());
      $productoCodigo = Producto::where('codigo', 'like', "%{$request->buscador}%");
      $productoNombre = Producto::where('nombre', 'like', "%{$request->buscador}%");
      $productos = $productoCodigo->union($productoNombre)->groupBy('id')->orderBy('id', 'desc')->get();

      // return response()->json($productos);
      return ProformaInsumosController::tableSinInventario($productos);
   }


   public static function tableSinInventario($data)
   {
      $tr = "";
      if (count($data) == 0) {
         return ["status" => "error", "msj" => "No se encontraron resultados"];
      }

      try {

         foreach ($data as $value) {
            $config = Ct_Configuraciones::find(3);
            $valor_iva = 0;
            if ($value->iva == 1) {
               $valor_iva = $config->iva;
            }
            $tr .= "<tr>
                     <td>{$value->codigo}</td>
                     <td>{$value->nombre}</td>
                     <td class='ocultar'> <input type='text' class='form-control borde' id='espec_tecnica_{$value->id}' value=''> </td>
                     <td class='ocultar'> <input type='text' class='form-control borde' id='espec_tecnica_ofertada_{$value->id}' value=''> </td>
                     <td> <input type='number' class='form-control borde' id='cantidad_{$value->id}' value='0'> </td>
                     <td> <input type='text'class='form-control borde' id='presentacion_{$value->id}'></td>
                     <td> <input type='text' class='form-control borde' id='precio_{$value->id}' value='0'> </td>
                     <td> <input type='text' class='form-control borde' id='descuento_{$value->id}' value='0'> </td>
                     <td>
                        <input type='hidden' id='iva_{$value->id}' value='{$valor_iva}'>
                        <input type='checkbox'" . ($valor_iva > 0 ? 'checked' : '') . " disabled >
                     </td>
                     <td><p id='total_{$value->id}'>0.00</p></td>
                     <td> <button class='btn btn-primary text-center' onclick='agregarDetalle({$value->id})'>Agregar</button> </td>
                  </tr>";
         }
         $table = "<table class='table'>
                     <thead>
                        <tr style='background-color: #63cdda; color: white;'>
                           <th colspan='15' style='text-align: center;'>Informacion</th>
                        </tr>
                        <tr>
                           <th style='text-align:center'>Cod. Producto</th>
                           <th style='width:10%;text-align:center'>Nombre</th>
                           <th style='width:10%;text-align:center' class='ocultar'>Espec. Tecnica</th>
                           <th style='width:10%;text-align:center' class='ocultar'>Espec. Tecnica Ofertada</th>
                           <th style='width: 8%;text-align:center'>Cantidad</th>
                           <th style='width: 5%;text-align:center'>Presentación</th>
                           <th style='width: 8%;text-align:center'>Precio</th>
                           <th style='width: 8%;text-align:center'>Descuento</th>
                           <th style='text-align:center'>Iva</th>
                           <th style='width: 8%;text-align:center'>Total</th>
                           <th class='text-center'>Accion</th>
                        </tr>
                     </thead>
                     <tbody>
                        {$tr}
                     </tbody>
                  </table>";

         return ["status" => "success", "msj" => "Exito", "table" => $table];
      } catch (\Exception $e) {
         return ["status" => "error", "msj" => "Ocurrio un error en la busqueda", "exp" => $e->getMessage()];
      }
   }


   public function updateDetailsSinInventario(Request $request)
   {
      $id_usuario = Auth::user()->id;
      $ip_usuario = $_SERVER['REMOTE_ADDR'];

      if ($request["accion"] == 0) {
         DB::beginTransaction();
         try {
            $proforma_detalle = Proforma_Venta_Det::find($request["id_detalle"]);
            $proforma_detalle->estado = 0;
            $proforma_detalle->id_usuariomod = $id_usuario;
            $proforma_detalle->ip_modificacion = $ip_usuario;
            $proforma_detalle->save();
            DB::commit();
            $table = ProformaInsumosController::detailsSinInventario($proforma_detalle->id_proforma);
            return ["status" => "success", "msj" => "Eliminado con exito", "table" => $table];
         } catch (\Exception $e) {
            DB::rollBack();
            return ["status" => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage()];
         }
      }

      // $validar = ProformaInsumosController::validarStock($request->all());

      // if ($validar["status"] == "error") {
      //    return $validar;
      // }

      DB::beginTransaction();
      try {
         // $serie = InvInventarioSerie::find($request['id_inv_serie']);
         $producto = Producto::find($request['id_producto']);

         // if (is_null($serie)) {
         //    DB::rollBack();
         //    return ["status" => "error", "msj" => "No se encontro este producto"];
         // }

         $subtotal0 = 0;
         $subtotal12 = 0;

         $base0 = 0;
         $base12 = 0;
         $config = Ct_Configuraciones::find(3);
         // dd($serie->producto);
         if ($producto->iva == 1) {
            $calcIva = (($request['precio'] * $request["cantidad"]) - $request['descuento']) * $config->iva;
            $subtotal12 = (($request['precio'] * $request["cantidad"]));
            $base12 = (($request['precio'] * $request["cantidad"])) - $request['descuento'];
            $total = $base12 + $calcIva;
         } else {
            $subtotal0 = (($request['precio'] * $request["cantidad"]));
            $base0 = (($request['precio'] * $request["cantidad"])) - $request['descuento'];
            $total = $base0;
         }

         // dd($serie->producto->nombre);

         $data_detalle = [
            // "id_bodega"                       => $serie->id_bodega,
            // "serie"                           => $serie->serie,
            // "lote"                            => $serie->lote,
            "cantidad"                        => $request["cantidad"],
            "estado"                          => 1,
            "id_proforma"                     => $request->id,
            "id_usuariocrea"                  => $id_usuario,
            "id_usuariomod"                   => $id_usuario,
            "ip_creacion"                     => $ip_usuario,
            "ip_modificacion"                 => $ip_usuario,
            // "id_inv_serie"                    => $serie->id,
            "precio"                          => $request["precio"],
            "descuento"                       => $request["descuento"],
            "iva"                             => $producto->iva,
            "subtotal0"                       => $subtotal0,
            "base0"                           => $base0,
            "subtotal12"                      => $subtotal12,
            "base12"                          => $base12,
            "total"                           => $total,
            "porct_iva"                       => $config->iva,
            "especificacion_tecnica"          => $request->especificacion_tecnica,
            "especificacion_tecnica_ofertada" => $request->especificacion_tecnica_ofertada,
            "nombre_generico"                 => $producto->nombre,
            "codigo"                          => $producto->codigo,
            "registro_sanitario"              => $producto->registro_sanitario,
            "id_producto"                     => $producto->id,
            'presentacion'                    => $request["presentacion"]
         ];

         Proforma_Venta_Det::create($data_detalle);

         DB::commit();
         $table = ProformaInsumosController::detailsSinInventario($request->id);
         return ["status" => "success", "msj" => "Guardado con exito", "table" => $table["table"]];
         // return ["status" => "success", "msj" => "Guardado con exito"];
      } catch (\Exception $e) {
         DB::rollback();
         return ["status" => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage()];
      }
   }

   public static function detailsSinInventario($id_proforma)
   {
      $detalles = Proforma_Venta_Det::where('id_proforma', $id_proforma)->where('estado', 1)->get();
      $tr = "";
      foreach ($detalles as $detalle) {
         $tr .= "<tr id='fila_{$detalle->id}'>";

         $tr .= "<td class='text-left'>
                     <input type='text' name='codigo' class='form-control' onchange='handleDetail(this, {$detalle->id})' value='{$detalle->codigo}'/>
                  </td>";

         $tr .= "<td class='text-left'>
                     <input type='text' class='form-control' name='nombre_generico' onchange='handleDetail(this, {$detalle->id})' value='{$detalle->nombre_generico}' />
                  </td>";

         // $tr .= "<td class='text-left'>{$detalle->lote}</td>
         //          <td class='text-left'>{$detalle->inv_serie->bodega->nombre}</td>";

         $tr .= "
                     <td class='text-left'>
                        <input onchange='handleDetail(this, {$detalle->id})' type='text' class='form-control' name='registro_sanitario' value='{$detalle->registro_sanitario}'>
                     </td>";

         if ($detalle->proforma_venta_cab->mostrar == 1) {

            $tr .= "     <td class='text-left'>
                        <input onchange='handleDetail(this, {$detalle->id})' type='text' class='form-control' name='especificacion_tecnica' id='espec_tecnica_{$detalle->id}_{$detalle->id_inv_serie}' value='{$detalle->especificacion_tecnica}'>
                     </td>
                     <td class='text-left'>
                        <input onchange='handleDetail(this, {$detalle->id})' type='text' class='form-control' name='especificacion_tecnica_ofertada' id='espec_tecnica_ofertada_{$detalle->id}_{$detalle->id_inv_serie}' value='{$detalle->especificacion_tecnica_ofertada}'>
                     </td>";
         }
         $tr .= "<td>
                     <p>{$detalle->cantidad}</p>
                     <input type='hidden' class='form-control' name='cantidad_{$detalle->id}_{$detalle->id_inv_serie}' value='{$detalle->cantidad}'>
                  </td>";

         $tr .= "
                   <td><input onchange='handleDetail(this, {$detalle->id})' type='text'class='form-control borde' value='{$detalle->presentacion}' name='presentacion' id='presentacion_{$detalle->id}'></td>
                  <td>
                     <input onchange='handleDetail(this, {$detalle->id})' name='precio' type='text' class='form-control' id='precio_{$detalle->id}_{$detalle->id_inv_serie}' value='{$detalle->precio}'>
                  </td>
                  <td>
                     <input onchange='handleDetail(this, {$detalle->id})' name='descuento' type='text' class='form-control' id='descuento_{$detalle->id}_{$detalle->id_inv_serie}' value='{$detalle->descuento}'>
                  </td>
                  <td> <input type='checkbox'" . ($detalle->iva == 1 ? 'checked' : '') . " disabled > </td>
                  <td> <p>{$detalle->total}</p> </td>
                  <td style='display:flex; gap:3px; flex-wrap:wrap;'> 
                     
                     <button class='btn-general btn-eliminar' onclick='eliminarDetalle({$detalle->id})'>
                        <i class='fa fa-trash' aria-hidden='true'></i>
                     </button> 
                  </td>
               </tr>";
      }
      return ["table" => $tr];
   }

   public function editarFilaSinInventario($id_detalle)
   {
      DB::beginTransaction();
      try {
         $id_usuario = Auth::user()->id;
         $ip_usuario = $_SERVER['REMOTE_ADDR'];
         $proforma_det = Proforma_Venta_Det::find($id_detalle);

         $proforma = $proforma_det->proforma_venta_cab;
         if ($proforma->estado == 1) {
            DB::rollBack();
            return ["status" => "error", "msj" => "No se puede editar, ya fue aprobada"];
         }

         $subtotal0 = 0;
         $subtotal12 = 0;
         $base0 = 0;
         $base12 = 0;
         $config = Ct_Configuraciones::find(3);
         if ($proforma_det->producto->iva == 1) {
            $calcIva = ((floatval($proforma_det->precio) * $proforma_det->cantidad) - $proforma_det->descuento) * $config->iva;
            $subtotal12 = ((floatval($proforma_det->precio) * $proforma_det->cantidad));
            $base12 = ((floatval($proforma_det->precio) * $proforma_det->cantidad)) - $proforma_det->descuento;
            $total = $base12 + $calcIva;
         } else {
            $subtotal0 = ((floatval($proforma_det->precio) * $proforma_det->cantidad));
            $base0 = ((floatval($proforma_det->precio) * $proforma_det->cantidad)) - $proforma_det->descuento;
            $total = $base0;
         }
         $proforma_det->id_usuariomod     = $id_usuario;
         $proforma_det->ip_modificacion   = $ip_usuario;
         $proforma_det->precio            = $proforma_det->precio;
         $proforma_det->descuento         = $proforma_det->descuento;
         $proforma_det->iva               = $proforma_det->producto->iva;
         $proforma_det->subtotal0         = $subtotal0;
         $proforma_det->base0             = $base0;
         $proforma_det->subtotal12        = $subtotal12;
         $proforma_det->base12            = $base12;
         $proforma_det->total             = $total;
         $proforma_det->porct_iva         = $config->iva;

         $proforma_det->save();
         DB::commit();
         $table = ProformaInsumosController::detailsSinInventario($proforma_det->id_proforma);
         return ["status" => "success", "msj" => "Guardado con exito", "table" => $table["table"]];
      } catch (\Exception $e) {
         DB::rollBack();
         return ["status" => "error", "msj" => "Error al editar", "linea" => $e->getLine(),  "exp" => $e->getMessage()];
      }
   }

   public static function aprobarProformaSinKardex($id_proforma)
   {
      $proforma = Proforma_Venta_Cab::find($id_proforma);

      if (is_null($proforma)) {
         return ["status" => "error", "msj" => "No se encontro la proforma"];
      }

      if (is_null($proforma->id_cliente)) {
         return ["status" => "error", "msj" => "Agregar un cliente"];
      }

      if (is_null($proforma->descripcion) or trim($proforma->descripcion) == '') {
         return ["status" => "error", "msj" => "Agregar un descripcion"];
      }

      if (is_null($proforma->fecha_exp)) {
         return ["status" => "error", "msj" => "Agrege la fecha de caducidad de la proforma"];
      }

      $detalles = Proforma_Venta_Det::where('id_proforma', $proforma->id)->where('estado', 1)->first();
      if (is_null($detalles)) {
         return ["status" => "error", "msj" => "Agregar un producto"];
      }

      $detalles = Proforma_Venta_Det::where('id_proforma', $proforma->id)->where('estado', 1)->get();
      DB::beginTransaction();
      try {
         $id_usuario = Auth::user()->id;
         $ip_modificacion = $_SERVER["REMOTE_ADDR"];
         $proforma->estado = 1;
         $proforma->id_usuariomod = $id_usuario;
         $proforma->ip_modificacion = $ip_modificacion;
         $proforma->save();
         DB::commit();
         return ["status" => "success", "msj" => "Aprobado con exito"];
      } catch (\Exception $e) {

         DB::rollback();
         return ["status" => "error", "msj" => "Error al aprobar", "exp" => $e->getMessage()];
      }
   }

   public function pdfProformaSinKardex($id)
   {
      $proforma = Proforma_Venta_Cab::find($id);
      if ($proforma->estado != 1) {
         return redirect()->route('insumos.proformaInsumos.index');
      }
      $vistaurl = "insumos.proforma.sin_kardex.pdf_proforma";
      $view = \View::make($vistaurl, compact('proforma'))->render();
      $pdf = \App::make('dompdf.wrapper');
      $pdf->loadHTML($view);
      $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true,  'chroot' => base_path('/')]);
      $pdf->setPaper('A4', 'portrait');
      return $pdf->stream('Proforma de Venta-' . $id . '.pdf');
   }

   public function observarProformaSinKardex($id_proforma)
   {
      $proforma = Proforma_Venta_Cab::find($id_proforma);
      $detalles = Proforma_Venta_Det::where('estado', 1)->where('id_proforma', $proforma->id)->get();
      return view("insumos/proforma/sin_kardex/view_proforma", ["proforma" => $proforma, "detalles" => $detalles, 'id_proforma' => $id_proforma]);
   }


   public function buscadorResponsable(Request $request)
   {

      $users  = [];
      if ($request['search'] != null) {
         $users1 = User::where(function ($jq1) use ($request) {
            $jq1->orwhereraw('CONCAT(users.nombre1," ",users.nombre2," ",users.apellido1," ",users.apellido2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.apellido1," ",users.apellido2," ",users.nombre1," ",users.nombre2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.nombre1," ",users.apellido1," ",users.apellido2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.nombre2," ",users.apellido1," ",users.apellido2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.apellido1," ",users.nombre1," ",users.nombre2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.apellido2," ",users.nombre1," ",users.nombre2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.nombre2," ",users.apellido2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.apellido2," ",users.nombre2) LIKE ?', ['%' . $request['search'] . '%']);
         })
            ->where('estado', 1)
            ->select('id as id', DB::raw("CONCAT(id , ' - ', nombre1, ' ', apellido1) as text"));
         $users2 = User::where(function ($jq1) use ($request) {
            $jq1->orwhereraw('CONCAT(users.nombre1," ",users.nombre2," ",users.apellido1," ",users.apellido2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.apellido1," ",users.apellido2," ",users.nombre1," ",users.nombre2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.nombre1," ",users.apellido1," ",users.apellido2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.nombre2," ",users.apellido1," ",users.apellido2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.apellido1," ",users.nombre1," ",users.nombre2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.apellido2," ",users.nombre1," ",users.nombre2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.nombre2," ",users.apellido2) LIKE ?', ['%' . $request['search'] . '%'])
               ->orwhereraw('CONCAT(users.apellido2," ",users.nombre2) LIKE ?', ['%' . $request['search'] . '%']);
         })
            ->where('estado', 1)
            ->select('id as id', DB::raw("CONCAT(id , ' - ', nombre1, ' ', apellido1) as text"));
         $users = $users1->union($users2)->take(10)->get();
      }
      return response()->json($users);
   }


   public function asientoVentaInsumos(Request $request)
   {

      $consulta = ProformaInsumosController::buscadorAsientoVentaCosto($request->all());
      return view("insumos/asiento_costo_venta/index", ["consulta" => $consulta, "parametro" => $request->all()]);
   }
   public static function buscadorAsientoVentaCosto($parametro)
   {
      $consulta = Venta_Kardex::orderBy('venta_kardex.id', 'desc');
      if (isset($parametro["id_venta"]) and !is_null($parametro["id_venta"])) {
         $consulta = $consulta->where("id_venta", $parametro["id_venta"]);
      }

      if (isset($parametro["asiento_venta"]) and !is_null($parametro["asiento_venta"])) {

         $venta = Ct_ventas::where("id_asiento", $parametro["asiento_venta"])->first();

         if (!is_null($venta)) {
            $consulta = $consulta->where("id_venta", $venta->id);
         } else {
            return [];
         }
      }

      if (isset($parametro["asiento_costo"]) and !is_null($parametro["asiento_costo"])) {

         $movimiento = InvCabMovimientos::where("id_asiento", $parametro["asiento_costo"])->first();

         if (!is_null($movimiento)) {
            $consulta = $consulta->where("id_cab_moviento", $movimiento->id);
         } else {
            $log = Log_Venta_Costo::where("id_asiento", $parametro["asiento_costo"])->first();
            if (!is_null($log)) {
               $consulta = $consulta->where("id_venta", $log->id_venta);
            } else {
               return [];
            }
         }
      }

      if (isset($parametro["nro_factura"]) and !is_null($parametro["nro_factura"])) {


         $consulta = $consulta->join('ct_ventas as ve', 've.id', 'venta_kardex.id_venta')->where("ve.nro_comprobante", 'like', "%{$parametro['nro_factura']}%")->select('venta_kardex.*');
      }

      if (isset($parametro["asiento_costo_anulado"]) and !is_null($parametro["asiento_costo_anulado"])) {

         $anulado = Log_Contable::where("id_referencia", $parametro["asiento_costo_anulado"])->first();

         if (!is_null($anulado)) {
            $log = Log_Venta_Costo::where("id_asiento", $anulado->id_ant)->first();
            if (!is_null($log)) {
               $consulta = $consulta->where("id_venta", $log->id_venta);
            } else {
               $movimiento = InvCabMovimientos::where("id_asiento", $anulado->id_ant)->first();
               if (!is_null($movimiento)) {
                  $consulta = $consulta->where("id_cab_moviento", $movimiento->id);
               } else {
                  return [];
               }
            }
         } else {
            return [];
         }
      }

      if (isset($parametro["estado_factura"]) and !is_null($parametro["estado_factura"])) {

         if ($parametro["estado_factura"] == 0) {
            $consulta = $consulta->join('ct_ventas as v', 'v.id', 'venta_kardex.id_venta')->where("v.estado", 0)->select('venta_kardex.*');
         } else if ($parametro["estado_factura"] == 1) {
            $consulta = $consulta->join('ct_ventas as v', 'v.id', 'venta_kardex.id_venta')->where("v.estado", '>', '0')->select('venta_kardex.*');
         }
      }


      if (isset($parametro["nc"]) and !is_null($parametro["nc"])) {
         $consulta = $consulta->join('ct_nota_credito_clientes as nc', function ($q) use ($parametro) {
            if ($parametro["nc"] == 1) {
               $q->on('nc.id_factura', 'venta_kardex.id_venta');
            } else if ($parametro["nc"] == 0) {
               $q->on('nc.id_factura', '!=', 'venta_kardex.id_venta');
            }
         });
         // if ($parametro["nc"] == 1) {
         //    $consulta = $consulta->join('ct_nota_credito_clientes as nc', 'nc.id_factura', 'venta_kardex.id_venta')->select('venta_kardex.*');
         // }else if ($parametro["nc"] == 1) {
         //    $consulta = $consulta->join('ct_nota_credito_clientes as nc', 'nc.id_factura', '!=', 'venta_kardex.id_venta')->select('venta_kardex.*');
         // }
      }

      if (isset($parametro["fecha_desde"]) and !is_null($parametro["fecha_desde"])) {
         $consulta = $consulta->join('ct_ventas as vfd', 'vfd.id', 'venta_kardex.id_venta')->where("vfd.fecha", ">=", $parametro["fecha_desde"])->select('venta_kardex.*');
      }
      if (isset($parametro["fecha_hasta"]) and !is_null($parametro["fecha_hasta"])) {
         $consulta = $consulta->join('ct_ventas as vfh', 'vfh.id', 'venta_kardex.id_venta')->where("vfh.fecha", "<=", $parametro["fecha_hasta"])->select('venta_kardex.*');
      }

      return $consulta->groupBy('id_cab_moviento')->paginate(15);
   }


   public function vincularAsientoVentaInsumos(Request $request)
   {
      $venta_kardex = Venta_Kardex::orderBy('id', 'desc')->groupBy('id_cab_moviento')->get();
      $actualizados = [];
      foreach ($venta_kardex as $vk) {
         $log = null;
         $log = Log_Venta_Costo::where("id_venta", $vk->id_venta)->first();

         if (is_null($log)) {
            $mov = InvCabMovimientos::find($vk->id_cab_moviento);

            if (is_null($mov->id_asiento) or $mov->id_asiento == "") {
               if (isset($vk->venta)) {
                  $asiento_costo = Ct_Asientos_Cabecera::where("observacion", "like", "%COSTO VENTA: {$vk->venta->concepto} / {$vk->venta->nro_comprobante}%")->where('modulo', 'COSTO_PROCEDIMIENTO')->first();
                  // dd($asiento_costo);
                  if (!is_null($asiento_costo)) {
                     Log_Venta_Costo::create([
                        "id_venta"        => $vk->id_venta,
                        "id_asiento"      => $asiento_costo->id,
                        "id_usuariocrea"  => Auth::user()->id,
                        "id_usuariomod"   => Auth::user()->id,
                        "estado"          => 1,
                        "ip_creacion"     => "MAS_UNION",
                        "ip_modificacion"     => "MAS_UNION"

                     ]);
                     // dd($vk);
                  }
               }
            } else {
               Log_Venta_Costo::create([
                  "id_venta"        => $vk->id_venta,
                  "id_asiento"      => $mov->id_asiento,
                  "id_usuariocrea"  => Auth::user()->id,
                  "id_usuariomod"   => Auth::user()->id,
                  "estado"          => 1,
                  "ip_creacion"     => "MAS_UNION",
                  "ip_modificacion"     => "MAS_UNION"

               ]);

               array_push($actualizados, $vk->id_venta);
            }
         }

         InvCabMovimientos::calcularTotalCabMovimiento($vk->id_cab_moviento);
      }

      dd("ok_gracias amigo");
   }

   public function buscarAsientoVenta()
   {
      $id_empresa = Session::get("id_empresa");
      $fac_venta = Ct_ventas::where("id_empresa", Session::get("id_empresa"))->get();
      $id_plan = LogConfig::busqueda('1.01.02.05.01');

      $arrObservacion = [];
      $arrValores = [];
      $granTotal = 0;
      $granGastos = 0;
      $granResta = 0;
      foreach ($fac_venta as $venta) {
         $asiento_det = Ct_Asientos_Detalle::where("id_asiento_cabecera", $venta->id_asiento)->where('id_plan_cuenta', $id_plan)->first();
         if (is_null($asiento_det)) {
            array_push($arrObservacion, [
               "id_venta" => $venta->id,
               "nro" => $venta->nro_comprobante,
               "total" => $venta->total_final,
               "asiento" => $venta->id_asiento,
               "observacion" => "No se encontro el plan de cuenta -> Cuentas por cobrar clientes comerciales <-",
               "type" => 0,
               "color" => "red"
            ]);
         } else {
            if ($asiento_det->debe > $venta->total_final or $asiento_det->debe < $venta->total_final) {
               array_push($arrObservacion, [
                  "id_venta" => $venta->id,
                  "nro" => $venta->nro_comprobante,
                  "total" => $venta->total_final,
                  "asiento" => $venta->id_asiento,
                  "observacion" => "El valor de la fac es mayor o menor al la cuenta del asiento",
                  "type" => 1,
                  "color" => "gray"
               ]);
            }
         }

         if ($venta->estado == 0) {
            $log = Log_Contable::where("id_ant", $venta->id_asiento)->get();
            if (count($log) == 0) {
               array_push($arrObservacion, [
                  "id_venta" => $venta->id,
                  "nro" => $venta->nro_comprobante,
                  "total" => $venta->total_final,
                  "asiento" => $venta->id_asiento,
                  "observacion" => "No se encontro el asiento anulado de la factura",
                  "type" => 2,
                  "color" => "yellow"
               ]);

               //$asiento_aprox = Ct_Asientos_Cabecera::whereBetween('fecha_asiento', [date("Y-m-d H:i:s", strtotime()) . ' 00:00:00', $r->hasta . ' 23:59:59'])->get();


            }
            if (count($log) > 1) {
               array_push($arrObservacion, [
                  "id_venta" => $venta->id,
                  "nro" => $venta->nro_comprobante,
                  "total" => $venta->total_final,
                  "asiento" => $venta->id_asiento,
                  "observacion" => "Tiene varios asientos anulados",
                  "type" => 3,
                  "color" => "blue"
               ]);
            }
         }

         $total_final = $venta->total_final;
         $saldo_contable = $venta->valor_contable;
         $calculado = 0;

         $det_ingreso = Ct_Detalle_Comprobante_Ingreso::where("id_factura", $venta->id)->join('ct_comprobante_ingreso as ci', 'ci.id', 'ct_detalle_comprobante_ingreso.id_comprobante')->where("id_empresa", $id_empresa)->where("ci.estado", 1)->select("ct_detalle_comprobante_ingreso.*")->get();
         if (count($det_ingreso) > 0) {
            foreach ($det_ingreso as $det) {
               $calculado += $det->total;
            }
         }

         $retenciones = Ct_Cliente_Retencion::where("id_factura", $venta->id)->where("estado", "<>", 0)->where("id_empresa", $id_empresa)->get();

         foreach ($retenciones as $ret) {
            $calculado += ($ret->valor_fuente + $ret->valor_iva);
         }


         $cruce_valores = Ct_Detalle_Cruce_Clientes::where("id_factura", $venta->id)->join("ct_cruce_valores_cliente as c", "c.id", "ct_detalle_cruce_clientes.id_comprobante")->where("c.id_empresa", $id_empresa)->where("c.estado", "<>", 0)->select("ct_detalle_cruce_clientes.*")->get();
         foreach ($cruce_valores as $cv) {
            $calculado += $cv->total;
         }



         $cruce_cuentas = Ct_Cruce_Cuentas_Clientes::where("id_factura", $venta->id)->where("estado", "<>", 0)->get();
         foreach ($cruce_cuentas as $cc) {
            $calculado += $cc->total;
         }


         $n_credito1 = Ct_Nota_Credito_Clientes::where("id_factura", $venta->id)->where("estado", "<>", 0)->get();
         foreach ($n_credito1 as $nc) {
            $calculado += $nc->total_credito;
         }

         $n_credito2 = Ct_Detalle_Credito_Clientes::where("ct_detalle_credito_clientes.id_factura", $venta->id)->join("ct_nota_credito_clientes as nc", "nc.id", "ct_detalle_credito_clientes.id_not_cred")->where("nc.estado", "<>", 0)->select("nc.*")->get();
         foreach ($n_credito2 as $nc) {
            $calculado += $nc->total_credito;
         }


         $resta_saldo_contable = floatval($total_final) - floatval($calculado);
         $aux = floatval($resta_saldo_contable) - floatval($saldo_contable);

         if ($venta->estado == 1) {
            $granTotal += $total_final;
            $granGastos += $calculado;
         }


         if (floatval($calculado) != floatval($total_final) and floatval($total_final) != floatval($saldo_contable) and round($aux, 4) != 0) {
            array_push($arrValores, [
               "id_venta" => $venta->id,
               "nro" => $venta->nro_comprobante,
               "fecha" => $venta->fecha,
               "total" => $venta->total_final,
               "asiento" => $venta->id_asiento,
               "observacion" => "El valor calculado es diferente al valor final",
               "total_final" => $total_final,
               "calculado" => $calculado,
               "saldo_contable" => $saldo_contable,
            ]);
         }
      }
      $granResta = $granTotal - $granGastos;

      $query = DB::select("
      select *from (
         select 
         IF((select IFNULL(id,0) from ct_ventas where id_asiento=c.id limit 1)!=0,'VENTA',
         IF((select IFNULL(id,0) from ct_comprobante_ingreso where id_asiento_cabecera=c.id limit 1)!=0,'C-INGRESO',
         IF((select IFNULL(id,0) from ct_cliente_retencion where id_asiento_cabecera=c.id limit 1)!=0,'C-RETENCION',
         IF((select IFNULL(id,0) from ct_cruce_valores_cliente where id_asiento_cabecera=c.id limit 1)!=0,'C-VALORES',
         IF((select IFNULL(id,0) from ct_cheques_post where id_asiento_cabecera=c.id limit 1)!=0,'CHEQUEPOST-C',
         IF((select IFNULL(id,0) from ct_cruce_cuentas_clientes where id_asiento_cabecera=c.id limit 1)!=0,'C-CUENTAS',
         IF((select IFNULL(id,0) from ct_nota_credito_clientes where id_asiento_cabecera=c.id limit 1)!=0,'NC',''
         )))))))
         modulo,c.id id_asiento,c.observacion,c.fecha_asiento,c.valor,d.debe,d.haber,c.id_usuariocrea,c.id_usuariomod,c.created_at,c.updated_at 
         from ct_asientos_cabecera c
         join ct_asientos_detalle d on c.id=d.id_asiento_cabecera
         left join ct_ventas v on c.id=v.id_asiento
         left join log_contable l on c.id=l.id_referencia
         where c.id_empresa='{$id_empresa}'
         and d.id_plan_cuenta ='{$id_plan}'
         and v.nro_comprobante is null
         and l.id_ant is null) as consulta where consulta.modulo = '';");
      $query =  json_decode(json_encode($query), true);


      return view("contable/cuadre_asiento/index", ["consulta" => $arrObservacion, "asientos" => $query, "valores" => $arrValores]);
   }

   public function buscarPosibleAsiento($id)
   {
      $venta = Ct_ventas::find($id);
      $fecha_desde = date("Y-m-d", strtotime($venta->updated_at)) . " 00:00:00";
      $fecha_hasta = date("Y-m-d", strtotime($venta->updated_at)) . " 23:59:59";
      $asiento_aprox = Ct_Asientos_Cabecera::whereBetween('created_at', [date("Y-m-d H:i:s", strtotime($fecha_desde)), date("Y-m-d H:i:s", strtotime($fecha_hasta))])->where("id_empresa", Session::get("id_empresa"))->get();
      return json_encode($asiento_aprox);
   }

   public function guardarAsientoAnulado(Request $request)
   {
      try {
         $asiento_ant = Ct_Asientos_Cabecera::find($request->id_asiento);
         if (is_null($asiento_ant)) {
            return ["status" => "error", "message" => "Falta el asiento anterior"];
         }

         $asiento_elegido = Ct_Asientos_Cabecera::find($request->id_asiento_elegido);
         if (is_null($asiento_elegido)) {
            return ["status" => "error", "message" => "Falta el asiento nuevo o no corresponde el id"];
         }

         Log_Contable::create([
            "id_ant"        => $asiento_ant->id,
            "id_referencia" => $asiento_elegido->id,
            "id_usuariocrea" => Auth::user()->id,
            "id_usuariomod" => Auth::user()->id,
            "tipo"          => "VEN-FA",
            "valor_ant"     => $asiento_ant->valor,
            "valor"         => $asiento_elegido->valor,
         ]);

         return ["status" => "success", "message" => "Guardado con exito"];
      } catch (\Exception $e) {
         return ["status" => "error", "message" => $e->getMessage()];
      }
   }
}
