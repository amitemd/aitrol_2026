<?php

namespace Sis_medico\Http\Controllers\guia;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\AfActivo;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Ct_productos;
use Sis_medico\Trasportista;
use Sis_medico\Ct_Guia_Remision_Detalle;
use Sis_medico\Ct_Guia_Remision_Cabecera;
use Sis_medico\PrecioProducto;
use Sis_medico\LogConfig;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_detalle_venta;
use Sis_medico\Ct_transportista;
use Sis_medico\Proveedor;
use Sis_medico\Ct_ventas;

class GuiaRemisionController extends Controller
{
    private $controlador = 'guiaremision';
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function buscador(Request $req)
    {
        return Ct_Guia_Remision_Cabecera::getBusqueda($req);
    }
    public function created(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $sucursales  = Ct_Sucursales::where('estado', 1)->where('id_empresa', $id_empresa)->get();
        $productos = Ct_productos::where('id_empresa', $id_empresa)->get();
        $proveedor = Proveedor::where('estado', 1)->get();
        $trasportista = Trasportista::all();
        return view('guia.created', ['sucursales' => $sucursales, 'productos' => $productos, 'proveedor' => $proveedor, 'trasportista' => $trasportista]);
    }

    public function autocomplete_nombre(Request $request)
    {
        $campo      = strtoupper($request['term']);
        $id_empresa  = $request->session()->get('id_empresa');
        $productos = Ct_productos::where('id_empresa', $id_empresa)
            ->where(function ($q) use ($campo) {
                $q->orWhere('nombre', 'like', '%' . $campo . '%');
                $q->orWhere('codigo', 'like', '%' . $campo . '%');
            })
            ->get();
        foreach ($productos as $id => $prod) {
            $valid_tags[] = ['id' => $prod->id, 'nombre' => $prod->nombre, 'codigo' => $prod->codigo];
        }
        return response()->json($valid_tags);
    }
    public function cargar_productos(Request $request)
    {
        $campo      = strtoupper($request['term']);
        $id_empresa  = $request->session()->get('id_empresa');
        $productos = Ct_productos::select('id', 'codigo', 'nombre', 'tipo_objeto')
            ->where('id_empresa', $id_empresa)
            ->where(function ($q) use ($campo) {
                $q->orWhere('nombre', 'like', '%' . $campo . '%');
                $q->orWhere('codigo', 'like', '%' . $campo . '%');
            });


        $activos = AfActivo::select('id', 'codigo', 'nombre', 'tipo_objeto')
            ->where('empresa', $id_empresa)
            ->where('estado', 1)
            ->where(function ($q) use ($campo) {
                $q->orWhere('nombre', 'like', '%' . $campo . '%');
                $q->orWhere('codigo', 'like', '%' . $campo . '%');
            })
            ->union($productos)
            ->get();
        $valid_tags = [];
        foreach ($activos as $product) {
            $valid_tags[] = ['id' => $product->id, 'nombre' => $product->nombre, 'codigo' => $product->codigo, 'tipo' => $product->tipo_objeto];
        }
        return response()->json($valid_tags);
    }
    public function ci_nombres(Request $request)
    {
        $name_ced = $request['name_ced'];
        $trasportista = Trasportista::query();
        if (strcmp($name_ced, 'ci') == 0) {
            $trasportista = $trasportista->select('id', "cedula")->get();
            return (['type' => 'ci', 'data' => $trasportista]);
        } else {
            $trasportista = $trasportista->selectRaw("CONCAT(nombres,' ',apellidos) as full_name , id")->get();
            return (['type' => 'name', 'data' => $trasportista]);
        }
    }

    public function ci_nombres_destinatario(Request $request)
    {
        $nombres = trim($request['term']);
        $id_empresa  = $request->session()->get('id_empresa');

        $ventas = Ct_ventas::join('ct_clientes as cl', 'ct_ventas.id_cliente', 'cl.identificacion')
            ->where('ct_ventas.nro_comprobante', 'like', '%' . $nombres . '%')
            ->where('ct_ventas.estado', '<>', 0)
            ->where('ct_ventas.nro_autorizacion', '<>', null)
            ->where('ct_ventas.id_empresa', $id_empresa)
            ->limit(20)
            ->get([
                'ct_ventas.nro_comprobante', 'ct_ventas.id', 'ct_ventas.nro_autorizacion', 'cl.*'
            ]);
        $data      = array();
        foreach ($ventas as $val) {

            $data[] = array(
                'value' => $val->nro_comprobante, 'id' => $val->id, 'autorizacion' => $val->nro_autorizacion, 'id_cliente' => $val->cedula_representante,
                'email' => $val->email_representante, 'direccion_representante' => $val->direccion_representante, 'nombre' => $val->nombre
            );
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function calPreProducto($id_producto)
    {
        $producto = Ct_productos::find($id_producto);
        $precio   = $producto->valor_total_paq;
        $iva = 0.0;
        $ProductoIva = 0.0;
        $total = 0.0;
        if ($precio == null || $precio == 0) {
            $precio_producto = PrecioProducto::where('codigo_producto', $producto->codigo)->orderBy('nivel', 'asc')->get()->first();
            if (!is_null($precio_producto)) {
                $precio = $precio_producto->precio;
            } else {
                $precio = 0;
            }
        }
        if ($producto->iva == 1) {
            $id_plan_config = LogConfig::busqueda("4.1.01.02");
            $ct_config = Ct_Configuraciones::where('id_plan', $id_plan_config)->where('estado', '1')->first();
            $iva = $ct_config->iva;
            $ProductoIva =  $precio * $iva; //precion con iva
            $total = $ProductoIva + $precio; //total
        }
        return ['precio' => $precio, 'productoIva' => $ProductoIva, 'total' => $total];
    }

    public function guardar(Request $request)
    {

        $id_auth = Auth::user()->id;
        $id_empresa  = $request->session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $sucursales  = Ct_Sucursales::where('id', $request['sucursal'])->first();
        $transportistas = Trasportista::find($request['nombre_ruc']);
        $ct_caja = Ct_Caja::where('id_sucursal', $request['sucursal'])->where('id', $request['punto_emision'])->first();
        try {
            DB::beginTransaction();
            $guia_cabecera = new Ct_Guia_Remision_Cabecera;
            $guia_cabecera->establecimiento = $sucursales->codigo_sucursal;
            $guia_cabecera->num_secuencial = '0';
            $guia_cabecera->punto_emision = $ct_caja->codigo_caja;
            $guia_cabecera->datos_adicionales = $request['datos_adicionales'];
            $guia_cabecera->ci_ruc_trasnportista = $transportistas->ci_ruc;
            $guia_cabecera->email_transportista = $request['email_transportista'];
            $guia_cabecera->direccion_partida = $request['direccion_partida'];
            $guia_cabecera->placa = $request['placa'];
            $guia_cabecera->ruta = $request['ruta'];
            $guia_cabecera->fecha_ini = $request['f_inicio'];
            $guia_cabecera->fecha_fin = $request['f_fin'];
            $guia_cabecera->fecha_emision_documento = date('Y-m-d H:i:s');
            $guia_cabecera->ci_destinatario = $request['cedula_destinatario'];
            $guia_cabecera->direccion_destinatario = $request['direccion_destino'];
            $guia_cabecera->tipo_documento_destinatario = $request['tipo_doc'];
            $guia_cabecera->fecha_autorizacion_destinatario = $request['fecha_autoriza'];
            $guia_cabecera->email_traslado_destinatario = $request['email_destina'];
            $guia_cabecera->codigo_est_destino = $request['codigo_esta_destino'];
            $guia_cabecera->num_doc_destino = $request['num_documento'];
            $guia_cabecera->num_autorizacion_sustento = $request['num_documento'];
            $guia_cabecera->motivo_traslado_destinatario = $request['motivo_trasla'];
            $guia_cabecera->razon_social_destinatario = $request['razon_social'];
            $guia_cabecera->num_autorizacion_sustento =  $request['num_autorizacion_sustento'];
            $guia_cabecera->id_usuariocrea = $id_auth;
            $guia_cabecera->id_usuariomod = $id_auth;
            $guia_cabecera->ip_creacion = $ip_cliente;
            $guia_cabecera->ip_modificacion = $ip_cliente;
            $guia_cabecera->id_empresa = $id_empresa;
            $guia_cabecera->estado = -1;
            $guia_cabecera->save();
            for ($i = 0; $i < count($request['producto']); $i++) {
                $detalle = new Ct_Guia_Remision_Detalle;
                $detalle->id_cabecera_remision = $guia_cabecera->id;
                $dato = explode('|', $request['producto'][$i]);
                if (is_array($dato))
                    $detalle->id_producto = $dato[0];
                else
                    $detalle->id_producto = $request['producto'][$i];
                $detalle->cantidad = $request['cantidad'][$i];
                $detalle->cod_principal = $request['cod_principal'][$i];
                $detalle->cod_adicional = $request['cod_adicional'][$i];
                $detalle->observacion = $request['observacion'][$i];
                $detalle->descripcion = $request['descripcion'][$i];
                $detalle->detalle3 = $request['detalle3'][$i];
                $detalle->id_usuariocrea = $id_auth;
                $detalle->id_usuariomod = $id_auth;
                $detalle->ip_creacion = $ip_cliente;
                $detalle->ip_modificacion = $ip_cliente;
                if (is_array($dato))
                    $detalle->tipo_objeto = $dato[1];
                else
                    $detalle->tipo_objeto = 'P';
                $detalle->save();
            }
            DB::commit();
            return ['respuesta' => 'success', 'titulos' => 'Guardado Correctamente', 'msj' => ['perfecto']];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'titulos' => 'Error Guardado', 'msj' => $e->getMessage(), 'linea' => $e->getLine()];
        }
    }
    public function index(Request $request)
    {
        config(['data' => []]);
        if ($request->submodulo == '') {
            $data['controlador'] = $this->controlador;
            config(['data' => $data]);
            return view('guia.index');
        } elseif ($request->submodulo == 'getguiasjs') {
            $data = Ct_Guia_Remision_Cabecera::leftjoin('transportistas as  t', 'ct_cabecera_remision.ci_ruc_trasnportista', '=', 't.ci_ruc')
                ->where('ct_cabecera_remision.id_empresa', session('id_empresa'))->orderBy('ct_cabecera_remision.id', 'desc')->get([
                    'ct_cabecera_remision.id',
                    'ct_cabecera_remision.created_at',
                    't.razon_social',
                    't.nombres',
                    't.apellidos',
                    'direccion_partida',
                    'ct_cabecera_remision.placa',
                    'ct_cabecera_remision.razon_social_destinatario',
                    'direccion_destinatario',
                    'codigo_est_destino',
                    'num_doc_destino',
                    'ct_cabecera_remision.estado',
                    'clave_acceso',
                    'establecimiento',
                    'punto_emision',
                    'num_secuencial',
                    'fecha_emision_documento',
                    'datos_adicionales'
                ]);
            $results = array(
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            );
            echo json_encode($results);
        } elseif ($request->submodulo == 'datoFactura') {
            $dac = explode('-', $request->numFac);
            $ventaCabecera = Ct_ventas::where('id_empresa', session('id_empresa'))
                ->where('sucursal', $dac[0])
                ->where('punto_emision', $dac[1])
                ->where('numero', $dac[2])
                ->first(['id']);
            $ventaDetalle = Ct_detalle_venta::join('ct_productos as p', 'ct_detalle_venta.id_ct_productos', '=', 'p.codigo')

                ->where('id_ct_ventas', $ventaCabecera->id)->get([
                    'ct_detalle_venta.nombre',
                    'ct_detalle_venta.cantidad',
                    'p.id_ct_productos'
                ]);
            return $ventaDetalle;
        }
    }
    public function update(Request $request)
    {
        $id_empresa  = $request->session()->get('id_empresa');
        $id = Ct_Guia_Remision_Cabecera::find($request['id']);
        $detalle = Ct_Guia_Remision_Detalle::where('id_cabecera_remision', $id->id)->get();
        $trasportista = Trasportista::all();
        $sucursales  = Ct_Sucursales::where('estado', 1)->where('id_empresa', $id_empresa)->get();
        $productos = Ct_productos::where('id_empresa', $id_empresa)->get();
        $proveedor = Proveedor::where('estado', 1)->get();
        return view('guia.updated', ['proveedor' => $proveedor, 'id' => $id, 'sucursales' => $sucursales, 'productos' => $productos, 'detalle' => $detalle, 'trasportista' => $trasportista]);
    }
    public function save_update(Request $request)
    {
        $id_auth = Auth::user()->id;
        $guia_cabecera = Ct_Guia_Remision_Cabecera::find($request['id']);
        $detalle = Ct_Guia_Remision_Detalle::where('id_cabecera_remision', $guia_cabecera->id)->delete();
        $id_empresa  = $request->session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $transportistas = Trasportista::find($request['nombre_ruc']);
        $id_sucursal = '';
        $sucursales  = Ct_Sucursales::where('id',  $id_sucursal)->first();
        if (!$sucursales) {
            $id_sucursal = $request['sucursal'];
        } else {
            $id_sucursal = $sucursales;
        }
        try {
            $guia_cabecera->establecimiento = $id_sucursal;
            $guia_cabecera->punto_emision = $request['punto_emision'];
            $guia_cabecera->datos_adicionales = $request['datos_adicionales'];
            $guia_cabecera->ci_ruc_trasnportista = $transportistas->ci_ruc;
            $guia_cabecera->email_transportista = $request['email_transportista'];
            $guia_cabecera->direccion_partida = $request['direccion_partida'];
            $guia_cabecera->placa = $request['placa'];
            $guia_cabecera->fecha_ini = $request['f_inicio'];
            $guia_cabecera->fecha_fin = $request['f_fin'];
            $guia_cabecera->fecha_emision_documento = date('Y-m-d H:i:s');
            $guia_cabecera->ruta = $request['ruta'];
            $guia_cabecera->ci_destinatario = $request['cedula_destinatario'];
            $guia_cabecera->direccion_destinatario = $request['direccion_destino'];
            $guia_cabecera->tipo_documento_destinatario = $request['tipo_doc'];
            $guia_cabecera->fecha_autorizacion_destinatario = $request['fecha_autoriza'];
            $guia_cabecera->email_traslado_destinatario = $request['email_destina'];
            $guia_cabecera->codigo_est_destino = $request['codigo_esta_destino'];
            $guia_cabecera->num_doc_destino = $request['num_documento'];
            $guia_cabecera->motivo_traslado_destinatario = $request['motivo_trasla'];
            $guia_cabecera->id_usuariocrea = $id_auth;
            $guia_cabecera->id_usuariomod = $id_auth;
            $guia_cabecera->ip_creacion = $ip_cliente;
            $guia_cabecera->ip_modificacion = $ip_cliente;
            $guia_cabecera->id_empresa = $id_empresa;
            $guia_cabecera->estado = -1;
            $guia_cabecera->save();
            for ($i = 0; $i < count($request['producto']); $i++) {
                $detalle = new Ct_Guia_Remision_Detalle;
                $detalle->id_cabecera_remision = $guia_cabecera->id;
                $detalle->id_producto = $request['producto'][$i];
                $detalle->cantidad = $request['cantidad'][$i];
                $detalle->cod_principal = $request['cod_principal'][$i];
                $detalle->cod_adicional = $request['cod_adicional'][$i];
                $detalle->observacion = $request['observacion'][$i];
                $detalle->descripcion = $request['descripcion'][$i];
                $detalle->detalle3 = $request['detalle3'][$i];
                $detalle->id_usuariocrea = $id_auth;
                $detalle->id_usuariomod = $id_auth;
                $detalle->ip_creacion = $ip_cliente;
                $detalle->ip_modificacion = $ip_cliente;
                $detalle->save();
            }
            return ['respuesta' => 'success', 'titulos' => 'Editado Correctamente', 'msj' => ['perfecto']];
        } catch (\Throwable $th) {
            return ['respuesta' => 'error', 'titulos' => 'Error en el Editado', 'msj' => $th];
        }
    }
    public function agregar_opcion(Request $request)
    {

        $cabecera = Trasportista::where('ci_ruc', $request['id'])->first();
        if(!is_null($cabecera)){
            return ['status' => 'success','razon_social' => $cabecera->razon_social, 'id' => $cabecera->id];
        }
        else{
            return ['status' => 'error','msj' => 'No se encontro el transportista'];
        }
    }
    public function codigo(Request $request)
    {
        $dato = explode('|', $request->id_producto);
        if ($dato[1] == 'P') {
            $producto = Ct_productos::find($dato[0]);
            if (!is_null($producto)) {
                return ['producto' => $producto];
            } else {
                return ['producto' => []];
            }
        } else {
            $producto = AfActivo::find($dato[0]);
            if (!is_null($producto)) {
                return ['producto' => $producto];
            } else {
                return ['producto' => []];
            }
        }
    }
    public function destinatario(Request $request)
    {
        $ct_proovedor = Proveedor::where('id', $request['id_proveedor'])->first();
        if (!is_null($ct_proovedor)) {
            return ['data' => $ct_proovedor];
        } else {
            return ['data' => []];
        }
    }
    public function transportista_datos(Request $request)
    {
        $transportistas = [];
        $nombreBuscar = '%' . $request['term'] . '%';
        $ct_transportista = Trasportista::where('ci_ruc', 'like', $nombreBuscar)->orWhere(function ($query) use ($request) {
            $query->where('razon_social', 'like', '%' . $request['term'] . '%');
        })
            ->get();
        foreach ($ct_transportista as $key => $value) {
            $transportistas[] = ['id' => $value->id, 'nombreappe' => $value->razon_social, 'data' => $value];
        }
        return response()->json($transportistas);
    }
    public function destinatario_datos(Request $request)
    {
        $destinatario = [];
        $nombreBuscar = '%' . $request['term'] . '%';
        $ct_destinatario = Ct_Clientes::where('identificacion', 'like', $nombreBuscar)->orWhere(function ($query) use ($request) {
            $query->where('nombre', 'like', '%' . $request['term'] . '%');
        })
            ->get();
        foreach ($ct_destinatario as $key => $value) {
            $destinatario[] = ['id' => $value->id, 'nombreappe' => $value->nombre, 'data' => $value];
        }
        return response()->json($destinatario);
    }
    public function crear_transportista()
    {
        return view('guia.modal');
    }
    public function validar_campos(Request $request)
    {
        return;
        $validar = new ApiFacturacionController;
        $ct_transportista = Trasportista::query();
        if ($request['e'] == 'cedula') {
            if (!empty($request['value'])) {
                $validar = $validar->llamarValidarCedula($request['value']);
                if (!$validar) {
                    $ct_transportista = $ct_transportista->where('ci_ruc', $request['value'])->first();
                    if (!empty($ct_transportista)) {
                        return (['data' => 'Cédula duplicado', 'status' => true]);
                    }
                } else {
                    return (['data' => 'Cédula erronea', 'status' => true]);
                }
            }
        } elseif ($request['e'] == 'email') {
            if (!empty($request['value'])) {
                $ct_transportista = $ct_transportista->where('email', $request['value'])->first();
                if (!empty($ct_transportista)) {
                    return (['data' => 'Correo duplicado', 'status' => true]);
                }
            }
        } elseif ($request['e'] == 'telefono') {
            if (!empty($request['value'])) {
                $ct_transportista = $ct_transportista->where('telefono1', $request['value'])->first();
                if (!empty($ct_transportista)) {
                    return (['data' => 'Telefono duplicado', 'status' => true]);
                }
            }
        } elseif ($request['e'] == 'placa') {
            if (!empty($request['value'])) {
                $ct_transportista = $ct_transportista->where('placa', $request['value'])->first();
                if (!empty($ct_transportista)) {
                    return (['data' => 'Placa duplicado', 'status' => true]);
                }
            }
        }
    }
    public function validar_cedula(Request $request)
    {

        /*$validar = new ApiFacturacionController;
        $validar = $validar->llamarValidarCedula($request['id']);
        if (!$validar) {
            return (['data' => 'Cédula erronea', 'status' => true]);
        } else {
            return (['data' => '', 'status' => false]);
        }*/
        return (['data' => '', 'status' => false]);
    }
    public function save_transportista(Request $request)
    {
        $array = array();
        $id_auth = Auth::user()->id;
        parse_str($request['form'], $array);
        $val = 0;
        if ($request['check'] == true) {
            $val = 1;
        }
        try {
            $guia_cabecera = new Trasportista;
            $guia_cabecera->ci_ruc = $array['cedula_ruc'];
            $guia_cabecera->razon_social = $array['razon_social'];
            $guia_cabecera->email = $array['email'];
            $guia_cabecera->telefono1 = $array['telefono'];
            $guia_cabecera->placa = $array['placaM'];
            $guia_cabecera->direccion = $array['direccion'];
            $guia_cabecera->tipo_documento = $array['tipo_documento'];
            $guia_cabecera->id_usuariocrea = $id_auth;
            $guia_cabecera->id_usuariomod = $id_auth;
            $guia_cabecera->rise = $val;
            $guia_cabecera->save();
            return ['msj' => 'Guardado Correctamente', 'data' => $guia_cabecera, 'status' => false];
        } catch (\Throwable $th) {
            return ['msj' => 'Ocurrio un error', 'data' => [], 'status' => true];
        }
    }
    public function llenar_campos(Request $request)
    {
        $transportista = Trasportista::where('id', $request['id'])->first();
        return ['data' => $transportista];
    }
    public function llenar_campos_destinatario(Request $request)
    {
        $transportista = Trasportista::where('id', $request['id'])->first();
        return ['data' => $transportista];
    }
    public function send_information(Request $request)
    {
        try {
            $guia_cabecera = Ct_Guia_Remision_Cabecera::find($request['id']);
            $guia_cabecera->estado = 0;
            $guia_cabecera->save();
            return ['err' => false];
        } catch (\Throwable $th) {
            return ['err' => true];
        }
    }
    public function llenar_productos(Request $request)
    {
        $num = explode('-', $request['producto']);
        //dd($num);
        $query = DB::table('ct_ventas as v')
            ->join('ct_detalle_venta as d', 'v.id', 'd.id_ct_ventas')
            ->leftjoin('ct_productos as p', 'd.id_ct_productos', 'p.codigo')
            ->where('v.sucursal', $num[0])
            ->where('v.punto_emision', $num[1])
            //->where('v.nro_comprobante', 'like', '%' . (int)$num[2] . '%')
            ->where('v.nro_comprobante', $num[0] . '-' . $num[1] . '-' . str_pad($num[2], 9, 0, STR_PAD_LEFT))
            ->where('v.id_empresa', session('id_empresa'))
            ->get([
                'd.id_ct_productos',
                'p.id',
                'd.nombre',
                'v.created_at',
                'p.tipo_objeto as tipo'
            ]);

        return ['query' => $query];
    }
}
