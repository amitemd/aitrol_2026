<?php

namespace Sis_medico\Http\Controllers\contable;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_Kardex;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_productos_equipos;
use Sis_medico\Ct_productos_insumos;
use Sis_medico\Ct_productos_procedimientos;
use Sis_medico\Ct_productos_paquete;
use Sis_medico\Equipo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Marca;
use Sis_medico\Plan_Cuentas;
use Sis_medico\PrecioProducto;
use Sis_medico\Producto;
use Sis_medico\Proveedor;
use Sis_medico\Ct_Acreedores;
use Sis_medico\Empresa;
use Sis_medico\Seguro;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Orden_Venta_Detalle_Paquete;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Producto_Tarifario_Paquete;
use Sis_medico\Ct_Productos_Tarifario;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\Agenda_Permiso;
//Quitar
use Sis_medico\Archivo_Plano_Cabecera;
use Sis_medico\Ct_Saldos_Producto;
use Svg\Tag\Rect;

class Productos_ServiciosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $productos = Ct_productos::where('id_empresa', $id_empresa)->paginate(10);

        return view('contable/productos/index', ['productos' => $productos, 'empresa' => $empresa]);
    }

    public function crear(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $cuentas           = plan_cuentas::where('estado', '2')->get();
        $proveedor         = Proveedor::all();
        $marca             = marca::where('estado', '1')->get();
        $productos_insumos = producto::where('estado', '1')->get();
        $equipo            = Equipo::where('estado', '1')->get();
        $impuestos         = DB::table('plan_cuentas')->where('estado', '2')->get();
        $seguros           = Seguro::where('inactivo', '1')->get();
        //dd($impuestos);
        //Query de Productos Tarifario (Nueva Funcionalidad)
        //$seguros   = Ct_Productos_Tarifario::groupby('id_producto')->get();
        //$seg       = Ct_Productos_Tarifario::orderby('id_seguro')->orderby('nivel')->groupBy(DB::raw('id_seguro, nivel'))->get();
        //$productos1 = Ct_productos::where('estado_tabla', '1')->paginate(20);
        $idusuario  = Auth::user()->id;
        $agenda_permiso = Agenda_Permiso::where('id_usuario', $idusuario)->where('ip_creacion', 'like', '%COMERCIAL%')->first();
        if (!is_null($agenda_permiso)) {
            return view('comercial/producto_tarifario/crear_producto', ['seguros' => $seguros, 'empresa' => $empresa]);
        } else {
            return view('contable/productos/create', ['seguros' => $seguros, 'cuentas' => $cuentas, 'proveedor' => $proveedor, 'marca' => $marca, 'productos_insumos' => $productos_insumos, 'impuestos' => $impuestos, 'equipo' => $equipo, 'empresa' => $empresa]);
        }
    }

    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $verificar  = $request->verificar;
        $tipo  =     $request->tipo;
        //dd($tipo);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $constraints = [
            'codigo' => $request['codigo'],
            'nombre' => $request['nombre'],
            'estado_tabla' => 1,
        ];
        //dd($constraints);
        $productos = $this->doSearchingQuery($constraints, $id_empresa, $verificar, $tipo);

        //dd($productos);

        return view('contable/productos/index', ['productos' => $productos, 'searchingVals' => $constraints, 'empresa' => $empresa, 'verificar' => $verificar ,'tipo'=> $tipo]);
    }

    private function doSearchingQuery($constraints, $id_empresa, $verificar, $tipo = "")
    {
        $query  = Ct_productos::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }
            $index++;
        }
        
        if($tipo !== null){
            $query = $query->where("tipo_producto",$tipo);
        }

        if ($verificar == "2") {
            $query = $query->leftJoin('ct_productos_insumos', 'ct_productos.id', '=', 'ct_productos_insumos.id_producto');
            $query = $query->whereNull('ct_productos_insumos.id_producto');
        } elseif ($verificar == "1") {
            $query = $query->join('ct_productos_insumos', 'ct_productos.id', '=', 'ct_productos_insumos.id_producto');
            //$query = $query->whereNotNull('ct_productos_insumos.id_producto');
        }
        return $query->where('id_empresa', $id_empresa)->paginate(10);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $reglas = [
                'codigo' => 'required|max:50|unique:ct_productos',
            ];
            $id_empresa = $request->session()->get('id_empresa');
            $mensajes = [
                'codigo.unique'   => 'El Código ya se encuentra registrado.',
                'codigo.required' => 'Ingrese el Codigo del Producto.',
            ];
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $this->validateInput($request, $reglas, $mensajes);
            date_default_timezone_set('America/Guayaquil');
            $p_id = Ct_productos::insertGetId([
                'codigo'                     => $request['codigo'],
                'nombre'                     => strtoupper($request['nombre']),
                'codigo_barra'               => $request['cod_barra'],
                'descripcion'                => $request['descripcion'],
                'id_empresa'                 => $id_empresa,
                'clase'                      => $request['clase'],
                'grupo'                      => $request['grupo'],
                'proveedor'                  => $request['proveedor'],
                'cta_gastos'                 => $request['cta_gastos'],
                'cta_ventas'                 => $request['cta_ventas'],
                'cta_costos'                 => $request['cta_costos'],
                'cta_devolucion'             => $request['cta_devolucion'],
                'reg_serie'                  => $request['reg_serie'],
                'mod_precio'                 => $request['mod_precio'],
                'mod_desc'                   => $request['mod_desc'],
                'iva'                        => $request['iva'] != null ? $request['iva'] : 0,
                'promedio'                   => $request['promedio'],
                'reposicion'                 => $request['reposicion'],
                'lista'                      => $request['lista'],
                'ultima_compra'              => $request['ultima_compra'],
                'descuento'                  => $request['descuento'],
                'financiero'                 => $request['financiero'],
                'marca'                      => $request['marca'],
                'modelo'                     => $request['modelo'],
                'stock_minimo'               => $request['stock_minimo'],
                'fecha_expiracion'           => $request['fecha_expiracion'],
                'impuesto_iva_compras'       => $request['impuesto_iva_compras'],
                'impuesto_iva_ventas'        => $request['impuesto_iva_ventas'],
                'impuesto_servicio'          => $request['impuesto_servicio'],
                'impuesto_ice'               => $request['impuesto_ice'],
                'clasificacion_impuesto_ice' => $request['clasificacion_impuesto_ice'],
                'ip_creacion'                => $ip_cliente,
                'ip_modificacion'            => $ip_cliente,
                'id_usuariocrea'             => $idusuario,
                'id_usuariomod'              => $idusuario,
                'tipo_producto'              => $request['tipo_producto']
            ]);
            if (isset($request["codigo_producto"])) {
                if (count($request["codigo_producto"]) > 0) {
                    foreach ($request->codigo_producto as $item => $v) {
                        if ($request->id_insumo[$item] != null && $request->id_insumo[$item] != '') {
                            $data2 = array(
                                'id_usuariocrea'  => $idusuario,
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                                'codigo_producto' => $request['codigo'],
                                'id_insumo'       => $request->id_insumo[$item],
                                'id_producto'     => $p_id,
                            );
                            Ct_productos_insumos::insert($data2);
                        }
                    }
                }
            }
            if (isset($request["insumos_producto"])) {
                if (count($request["insumos_producto"]) > 0) {
                    foreach ($request->insumos_producto as $item => $v) {
                        $data2 = array(
                            'id_usuariocrea'  => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                            'codigo_producto' => $request->insumos_producto[$item],
                            'id_equipo'       => $request->id_equipo[$item],
                            'id_producto'     => $p_id,
                        );
                        Ct_productos_equipos::insert($data2);
                    }
                }
            }
            if (isset($request["insumos_producto"])) {
                if (count($request["proce_id"]) > 0) {
                    foreach ($request->proce_id as $item => $v) {
                        $data2 = array(
                            'id_usuariocrea'   => $idusuario,
                            'ip_creacion'      => $ip_cliente,
                            'ip_modificacion'  => $ip_cliente,
                            'id_usuariomod'    => $idusuario,
                            'precio'           => $request->precio_procedimiento[$item],
                            'id_seguro'        => $request->seguro_procedimiento[$item],
                            'nombre'           => $request->proce_id[$item],
                            'id_procedimiento' => $request->id_procedimiento[$item],
                            'id_producto'      => $p_id, //si va
                        );
                        Ct_productos_procedimientos::insert($data2);
                    }
                }
            }
            /*if (count($request->paque_id) > 0) {
            foreach ($request->paque_id as $item => $v) {
                $data2 = array(
                    'id_usuariocrea'   => $idusuario,
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                    'nombre'           => $request->paque_id[$item],
                    'id_paquete'       => $request->id_paquete[$item],
                    'id_producto'      => $p_id, //si va
                    'cantidad'         => $request->paque_cant[$item],
                    'precio'           => $request->precio_paq[$item],
                );
                Ct_productos_paquete::insert($data2);
            }
        }*/
            //Ingreso de Detalle Paquete
            $cont_det_paquete = $request['contador_paquetes'];
            $porc_iva = null;
            for ($i = 0; $i < $cont_det_paquete; $i++) {
                $visib_det_paquete = $request['visibilidad_paquete' . $i];
                if ($visib_det_paquete == 1) {
                    if ($request['iva_prod' . $i] == 1) {
                        $porc_iva = 0.12;
                    }
                    $input_det_paq = [
                        'id_usuariocrea'   => $idusuario,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'nombre'           => $request['paque_id' . $i],
                        'id_paquete'       => $request['id_paquete' . $i],
                        'id_producto'      => $p_id, //Obtenemos el Id del Producto
                        'cantidad'         => $request['paque_cant' . $i],
                        'precio'           => $request['precio_paq' . $i],
                        'iva'              => $porc_iva,
                    ];
                    Ct_productos_paquete::insert($input_det_paq);
                }
            }
            //Nueva Funcionalidad  AS
            //Guardado de Precio Producto
            /*PrecioProducto::create([
            'codigo_producto' => $request['codigo'],
            'nivel'           => $request['nivel'],
            'precio'          => $request['precio'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]); */
            //Funcion Guarda Precio Producto 
            $arr_total = [];
            for ($i = 0; $i < count($request->input("precio")); $i++) {
                if ($request->input("precio")[$i] != "" && $request->input("nivel")[$i] != "") {
                    $arr = [
                        'nivel'  => $request->input("nivel")[$i],
                        'precio' => $request->input("precio")[$i],
                    ];
                    array_push($arr_total, $arr);
                }
            }
            $cont_ = 0;
            foreach ($arr_total as $valor) {
                $precio = [
                    'codigo_producto' => $request['codigo'],
                    'nivel'           => $valor['nivel'],
                    'precio'          => $valor['precio'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ];
                if ($cont_ == 0) {
                    $arrayPrecios = [
                        'precio1' => $valor['precio'],
                        'valor_total_paq' => $valor['precio']
                    ];
                    Ct_productos::where('id', $p_id)->update($arrayPrecios);
                }
                $cont_++;
                PrecioProducto::create($precio);
            }
            DB::commit();
            //return redirect()->intended('/contable/productos_servicios');
            //return redirect()->intended('/contable/productos_servicios/editar/{$p_id}');
            return redirect()->route('productos_servicios_editar', ['id' => $p_id]);
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage() . ' Linea: ' . $e->getLine();
        }
    }
    private function validateInput($request)
    {
        $this->validate($request, []);
    }
    public function editar(Request $request, $codigo)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa    = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();

        $productos = ct_productos::where('id', $codigo)->first();
        if ($productos == null) {
            return redirect()->intended('/contable/productos_servicios');
        }
        $cuentas   = plan_cuentas::where('estado', '2')->get();
        $proveedor = Proveedor::all();
        $marca     = marca::where('estado', '1')->get();
        $impuestos = DB::table('plan_cuentas')->where('estado', '2')->get();

        //Anterior
        $precios   = PrecioProducto::where('codigo_producto', $productos->codigo)->get();
        //$precios   = PrecioProducto::where('codigo_producto', $productos->codigo)->first();

        //dd($productos->codigo);
        $insumo = Ct_productos_insumos::where('id_producto', $codigo)->get();
        //dd($insumo);
        $equipo         = Ct_productos_equipos::where('id_producto', $codigo)->get();
        $procedimientos = Ct_productos_procedimientos::where('id_producto', $codigo)->get();
        $paquetes = Ct_productos_paquete::where('id_producto', $codigo)->get();
        $seguros = Seguro::where('inactivo', '1')->get();
        //dd($procedimientos);
        // Redirect to user list if updating user wasn't existed
        //Obtenemos Informacion del Producto en la Tabla ct_producto_tarifario
        /*$seg = Ct_Productos_Tarifario:: orderby('id_seguro')
                                       ->orderby('nivel')
                                       ->groupBy(DB::raw('id_seguro, nivel'))
                                       ->get();*/
        $seg = Ct_Productos_Tarifario::where('id_producto', $codigo)
            ->where('estado', '1')
            ->get();
        //dd($seg);
        //dd($seg);
        //dd($seg);
        $productos_tarifario = Ct_productos::where('id', $codigo)
            ->where('estado_tabla', '1')->first();
        //dd($productos_tarifario);
        $id_usuario = Auth::user()->id;
        return view('contable/productos/edit', ['seguros' => $seguros, 'productos' => $productos, 'cuentas' => $cuentas, 'proveedor' => $proveedor, 'marca' => $marca, 'impuestos' => $impuestos, 'precios' => $precios, 'insumo' => $insumo, 'equipo' => $equipo, 'procedimientos' => $procedimientos, 'paquetes' => $paquetes, 'productos_tarifario' => $productos_tarifario, 'seg' => $seg, 'empresa' => $empresa]);
    }

    public function update(Request $request, $id)
    {
        $productos  = Ct_productos::where('id', $id)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id_empresa = $request->session()->get('id_empresa');
        $prod = Ct_productos::where('codigo', $request['codigo'])->first();
        if (!is_null($prod)) {
            $input = [
                //'codigo'                        => $request['codigo'],
                'nombre'                     => strtoupper($request['nombre']),
                'codigo_barra'               => $request['cod_barra'],
                'descripcion'                => $request['descripcion'],
                'id_empresa'                 => $id_empresa,
                'clase'                      => $request['clase'],
                'grupo'                      => $request['grupo'],
                'proveedor'                  => $request['proveedor'],
                'cta_gastos'                 => $request['cta_gastos'],
                'cta_ventas'                 => $request['cta_ventas'],
                'cta_costos'                 => $request['cta_costos'],
                'cta_devolucion'             => $request['cta_devolucion'],
                'reg_serie'                  => isset($request['reg_serie']),
                'mod_precio'                 => isset($request['mod_precio']),
                'mod_desc'                   => isset($request['mod_desc']),
                'iva'                        => isset($request['iva']),
                'promedio'                   => $request['promedio'],
                'reposicion'                 => $request['reposicion'],
                'lista'                      => $request['lista'],
                'ultima_compra'              => $request['ultima_compra'],
                'descuento'                  => $request['descuento'],
                'financiero'                 => $request['financiero'],
                'marca'                      => $request['marca'],
                'modelo'                     => $request['modelo'],
                'stock_minimo'               => $request['stock_minimo'],
                'fecha_expiracion'           => $request['fecha_expiracion'],
                'impuesto_iva_compras'       => $request['impuesto_iva_compras'],
                'impuesto_iva_ventas'        => $request['impuesto_iva_ventas'],
                'impuesto_servicio'          => $request['impuesto_servicio'],
                'impuesto_ice'               => $request['impuesto_ice'],
                'clasificacion_impuesto_ice' => $request['clasificacion_impuesto_ice'],
                'estado_tabla'               => isset($request['estado']),
                'ip_creacion'                => $ip_cliente,
                'ip_modificacion'            => $ip_cliente,
                'id_usuariocrea'             => $idusuario,
                'id_usuariomod'              => $idusuario,
                'tipo_producto' => $request['tipo_producto'],
            ];
        } else {

            $input = [
                'codigo'                     => $request['codigo'],
                'nombre'                     => strtoupper($request['nombre']),
                'codigo_barra'               => $request['cod_barra'],
                'descripcion'                => $request['descripcion'],
                'clase'                      => $request['clase'],
                'grupo'                      => $request['grupo'],
                'proveedor'                  => $request['proveedor'],
                'cta_gastos'                 => $request['cta_gastos'],
                'cta_ventas'                 => $request['cta_ventas'],
                'cta_costos'                 => $request['cta_costos'],
                'cta_devolucion'             => $request['cta_devolucion'],
                'reg_serie'                  => isset($request['reg_serie']),
                'mod_precio'                 => isset($request['mod_precio']),
                'mod_desc'                   => isset($request['mod_desc']),
                'iva'                        => isset($request['iva']),
                'promedio'                   => $request['promedio'],
                'reposicion'                 => $request['reposicion'],
                'lista'                      => $request['lista'],
                'ultima_compra'              => $request['ultima_compra'],
                'descuento'                  => $request['descuento'],
                'financiero'                 => $request['financiero'],
                'marca'                      => $request['marca'],
                'modelo'                     => $request['modelo'],
                'stock_minimo'               => $request['stock_minimo'],
                'fecha_expiracion'           => $request['fecha_expiracion'],
                'impuesto_iva_compras'       => $request['impuesto_iva_compras'],
                'impuesto_iva_ventas'        => $request['impuesto_iva_ventas'],
                'impuesto_servicio'          => $request['impuesto_servicio'],
                'impuesto_ice'               => $request['impuesto_ice'],
                'clasificacion_impuesto_ice' => $request['clasificacion_impuesto_ice'],
                'estado_tabla'               => isset($request['estado']),
                'ip_creacion'                => $ip_cliente,
                'ip_modificacion'            => $ip_cliente,
                'id_usuariocrea'             => $idusuario,
                'id_usuariomod'              => $idusuario,
                'tipo_producto' => $request['tipo_producto'],
            ];
        }
        $producto = Ct_productos::where('id', $id)->update($input);
        if ($request->has("codigo_producto")) {
            $borrar = Ct_productos_insumos::where('id_producto', $id);
            if (!is_null($borrar)) {
                $borrar->delete();
            }
            if ('codigo_producto' != $borrar) {
                if (count($request->codigo_producto) > 0) {
                    foreach ($request->codigo_producto as $item => $v) {
                        //dd($request);
                        $data2 = array(
                            'id_usuariocrea'  => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                            'codigo_producto' => $v,
                            'id_insumo'       => $request->id_insumo[$item],
                            'id_producto'     => $id,
                        );
                        Ct_productos_insumos::insert($data2);
                    }
                }
            }
        }

        if ($request->has("insumos_producto")) {
            $eliminar = Ct_productos_equipos::where('id_producto', $id);
            //dd($eliminar);
            if (!is_null($eliminar)) {
                $eliminar->delete();
            }
            if ('insumos_producto' != $eliminar) {
                if (count($request->insumos_producto) > 0) {
                    foreach ($request->insumos_producto as $item => $v) {
                        // dd($request);
                        $data2 = array(
                            'id_usuariocrea'  => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                            'codigo_producto' => $request->insumos_producto[$item],
                            'id_equipo'       => $request->id_equipo[$item],
                            'id_producto'     => $id,

                        );
                        //dd($data2);
                        Ct_productos_equipos::insert($data2);
                    }
                }
            }
        }

        if ($request->has("proce_id")) {
            $variable = Ct_productos_procedimientos::where('id_producto', $id);
            //dd($eliminar);
            if (!is_null($variable)) {
                $variable->delete();
            }
            if ('proce_id' != $variable) {
                if (count($request->proce_id) > 0) {
                    foreach ($request->proce_id as $item => $v) {
                        // dd($request);
                        $data2 = array(
                            'id_usuariocrea'   => $idusuario,
                            'ip_creacion'      => $ip_cliente,
                            'ip_modificacion'  => $ip_cliente,
                            'id_usuariomod'    => $idusuario,
                            'precio'           => $request->precio_procedimiento[$item],
                            'nombre'           => $request->proce_id[$item],
                            'id_seguro'        => $request->seguro_procedimiento[$item],
                            'id_procedimiento' => $request->id_procedimiento[$item],
                            'id_producto'      => $id, //si va

                        );
                        //dd($data2);

                        Ct_productos_procedimientos::insert($data2);
                    }
                }
            }
        }

        /*if ($request->has("paque_id")) {
            $variable = Ct_productos_paquete::where('id_producto', $id);*/

        /*if (!is_null($variable)) {
                $variable->delete();
            }*/
        /*if ('paque_id' != $variable) {
                if (count($request->paque_id) > 0) {
                    foreach ($request->paque_id as $item => $v) {
                        // dd($request);
                        $data2 = array(
                            'id_usuariocrea'   => $idusuario,
                            'ip_creacion'      => $ip_cliente,
                            'ip_modificacion'  => $ip_cliente,
                            'id_usuariomod'    => $idusuario,
                            'nombre'           => $request->paque_id[$item],
                            'id_paquete'       => $request->id_paquete[$item],
                            'id_producto'      => $id, //si va
                            'cantidad'         => $request->paque_cant[$item],
                            'precio'           => $request->precio_paq[$item],
                        );
                        //dd($data2);

                        //Ct_productos_paquete::insert($data2);
                        $variable->update($data2); 
                    }
                }
            }*/
        //}

        //Ingreso de Detalle Paquete
        //$cont_det_paquete = $request['contador_paquetes'];

        /*for($i = 0; $i < $cont_det_paquete; $i++){

            $visib_det_paquete = $request['visibilidad_paquete'. $i];

            if($visib_det_paquete == 1){

                $pr_paq = Ct_productos_paquete::where('id',$request['id_prod_paq'. $i]);

                if(!is_null($pr_paq)){    
                
                    $input_det_paq = [

                        'id_usuariocrea'   => $idusuario,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'nombre'           => $request['paque_id'.$i],
                        'id_paquete'       => $request['id_paquete'.$i],
                        'id_producto'      => $id, //Obtenemos el Id del Producto
                        'cantidad'         => $request['paque_cant'.$i],
                        'precio'           => $request['precio_paq'.$i],
                    ];
                    $pr_paq->update($input_det_paq);
                }else{
                    $input_det_paq = [
                        'id_usuariocrea'   => $idusuario,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'nombre'           => $request['paque_id'.$i],
                        'id_paquete'       => $request['id_paquete'.$i],
                        'id_producto'      => $id, //Obtenemos el Id del Producto
                        'cantidad'         => $request['paque_cant'.$i],
                        'precio'           => $request['precio_paq'.$i],
                    ];
                    Ct_productos_paquete::insert($input_det_paq);
                }
            }
        }*/
        //Guardado en la Tabla Precio_Producto
        /*$pre_prod = PrecioProducto::where('codigo_producto', $request['codigo']);
       
        $input_prec_prod = [
            'codigo_producto' => $request['codigo'],
            'nivel'           => $request['nivel'],
            'precio'          => $request['precio'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $pre_prod->update($input_prec_prod);*/
        //Guardado Precio Producto
        $arr_nuevo = [];

        if ($request->has("precio")) {
            for ($i = 0; $i < count($request->input("precio")); $i++) {
                if ($request->input("precio")[$i] != "" && $request->input("nivel")[$i] != "") {
                    $arr = [
                        'nivel'  => $request->input("nivel")[$i],
                        'precio' => $request->input("precio")[$i],
                        'id'     => $request->input("id")[$i],
                        'estado' => $request->input("estado")[$i],

                    ];

                    array_push($arr_nuevo, $arr);
                }
            }
        }
        foreach ($arr_nuevo as $valor) {
            PrecioProducto::updateOrCreate(
                ['id' => $valor['id']],
                [
                    'codigo_producto' => $request['codigo'],
                    'nivel'           => $valor['nivel'],
                    'precio'          => $valor['precio'],
                    'estado'          => isset($valor['estado']),
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]
            );
        }

        //return redirect()->intended('/contable/productos_servicios');
        return redirect()->route('productos_servicios_editar', ['id' => $id]);
    }

    public function buscar_insumo(Request $request)
    {
        $nombre    = $request['term'];
        $data      = array();
        $productos = DB::table('equipo')->where('nombre', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->id, 'value' => $product->nombre);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function buscar_producto(Request $request)
    {

        $nombre    = $request['term'];
        $data      = array();
        $productos = DB::table('producto')->where('nombre', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value1' => $product->id, 'value' => $product->nombre);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function buscar_procedimientos(Request $request)
    {

        $nombre    = $request['term'];
        $data      = array();
        $productos = DB::table('procedimiento')->where('nombre', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value1' => $product->id, 'value' => $product->nombre);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function buscar_paquete(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $posic_paq = $request['posicion_paq'];
        $nombre    = $request['term'];
        $data      = array();
        $productos = DB::table('ct_productos')->where('id_empresa', $id_empresa)->where('nombre', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value1' => $product->id, 'value' => $product->nombre, 'codig_product' =>  $product->codigo, 'pos_paq' =>  $posic_paq);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function buscar_precio(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $nombre = $request['nombre'];
        $data      = array();
        $precio    = 0;
        $producto = Ct_productos::where('nombre', $nombre)->where('id_empresa', $id_empresa)->get()->first();
        if (!is_null($producto)) {
            $precio   = $producto->valor_total_paq;
            if ($precio == null || $precio == 0) {
                $precio_producto = PrecioProducto::where('codigo_producto', $producto->codigo)->orderBy('nivel', 'asc')->get()->first();
                if ($precio_producto != null) {
                    $precio = $precio_producto->precio;
                } else {
                    $precio = 0;
                }
            }
        }
        $data[] = array('precio' => $precio);
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function agregar_id(Request $request)
    {
        $nombre       = $request['nombre'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ',nombre)
                  as completo, id
                  FROM producto
                  WHERE CONCAT_WS(' ',nombre) like '" . $seteo . "'";
        $nombres = DB::select($query);
        foreach ($nombres as $nombre) {
            $data[] = array(
                'value' => $nombre->completo,
                'id'    => $nombre->id,
            );
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $data;
    }

    public function agregar_id_equipo(Request $request)
    {
        $nombre       = $request['nombre'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ',nombre)
                  as completo, id
                  FROM equipo
                  WHERE CONCAT_WS(' ',nombre) like '" . $seteo . "'";
        $nombres = DB::select($query);
        foreach ($nombres as $nombre) {
            $data[] = array(
                'value' => $nombre->completo,
                'id'    => $nombre->id,
            );
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $data;
    }

    public function agregar_id_procedimiento(Request $request)
    {
        $nombre       = $request['nombre'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ',nombre)
                  as completo, id
                  FROM procedimiento
                  WHERE CONCAT_WS(' ',nombre) like '" . $seteo . "'";
        $nombres = DB::select($query);
        foreach ($nombres as $nombre) {
            $data[] = array(
                'value' => $nombre->completo,
                'id'    => $nombre->id,
            );
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $data;
    }

    public function agregar_id_paquete(Request $request)
    {
        $nombre       = $request['nombre'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre);
        $seteo        = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
        }
        $query = "SELECT CONCAT_WS(' ',nombre)
                  as completo, id, iva
                  FROM ct_productos
                  WHERE CONCAT_WS(' ',nombre) like '" . $seteo . "'";
        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $data[] = array(
                'value'     => $nombre->completo,
                'id'        => $nombre->id,
                'iva'       => $nombre->iva,
                //'codigo'    => $nombre->codigo,
            );
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

        return $data;
    }


    //Crear Tarifario Producto
    public function crear_tarifario($codigo)
    {
        /*$seguros = Seguro::where('seguros.inactivo', '1')
                           ->where('seguros.promo_seguro', '<>','1')
                           ->orderBy('seguros.nombre','asc')
                           ->get();*/

        $seguros = Seguro::where('seguros.inactivo', '1')
            ->where('seguros.promo_seguro', '<>', '1')
            ->orderBy('seguros.nombre', 'asc')
            ->get();

        /*$seguros = Seguro::where('seguros.inactivo', '1')
                           ->orderBy('seguros.nombre','asc')->get();*/

        //dd($seguros);

        $productos = Ct_productos::where('estado_tabla', '1')->get();

        return view('contable/productos/create_tarifario', ['seguros' => $seguros, 'productos' => $productos, 'codigo' => $codigo]);
    }


    public function edit_producto_tarifario($id_producto, $id_seguro, $id_nivel)
    {


        $prod_tari = Ct_Productos_Tarifario::where('id_producto', $id_producto)
            ->where('id_seguro', $id_seguro)
            ->where('nivel', $id_nivel)
            ->where('ct_producto_tarifario.estado', '1')
            ->join('ct_productos as prod', 'prod.id', 'ct_producto_tarifario.id_producto')
            ->select('prod.nombre', 'ct_producto_tarifario.id_producto', 'prod.codigo', 'prod.id as id_producto', 'ct_producto_tarifario.id_seguro', 'ct_producto_tarifario.precio_producto', 'ct_producto_tarifario.nivel')->first();

        //dd($prod_tari);

        //$seguros = Seguro::where('id',$id_seguro)->where('inactivo', '1')->orderBy('nombre')->get();
        $seguros   = Seguro::where('inactivo', '1')->orderBy('nombre')->get();

        return view('contable/productos/edit_tarifario', ['seguros' => $seguros, 'prod_tari' => $prod_tari, 'id_producto' => $id_producto]);
    }


    public function edit_producto_tarifario_paquete($id_prod_tar_paq, $id_prod, $id_seguro, $id_nivel)
    {

        $prod_tari_paq = Ct_Producto_Tarifario_Paquete::where('ct_producto_tarifario_paquete.id', $id_prod_tar_paq)
            ->where('ct_producto_tarifario_paquete.id_seguro', $id_seguro)
            ->where('ct_producto_tarifario_paquete.id_nivel', $id_nivel)
            ->where('ct_producto_tarifario_paquete.estado', '1')
            ->select('ct_producto_tarifario_paquete.id_producto', 'ct_producto_tarifario_paquete.id_seguro', 'ct_producto_tarifario_paquete.precio', 'ct_producto_tarifario_paquete.id_nivel', 'ct_producto_tarifario_paquete.id_paquete')->first();

        return view('contable/productos/modal_editar', ['prod_tari_paq' => $prod_tari_paq, 'id_prod_tar_paq' => $id_prod_tar_paq, 'id_prod' => $id_prod]);
    }

    public function edit_tarifario_uno($id_producto)
    {

        $prod_tari = Ct_Productos_Tarifario::where('id_producto', $id_producto)
            ->where('estado', '1')
            ->join('ct_productos as prod', 'prod.id', 'ct_producto_tarifario.id_producto')
            ->select('prod.nombre', 'ct_producto_tarifario.id_producto', 'prod.codigo', 'prod.id as id_producto', 'ct_producto_tarifario.id_seguro', 'ct_producto_tarifario.precio_producto', 'ct_producto_tarifario.nivel')->get();

        $seguros = Seguro::where('inactivo', '1')->orderBy('nombre')->get();

        return view('contable/productos/edit_tarifario_uno', ['seguros' => $seguros, 'prod_tari' => $prod_tari, 'id_producto' => $id_producto]);
    }


    public function crea_producto_tarifario_paquete($id_prod_paq, $id_producto, $id_paquete)
    {




        $prod_tar_paq = Ct_Producto_Tarifario_Paquete::where('id_producto', $id_paquete)
            ->where('estado', '1')
            ->join('ct_productos as prod', 'prod.id', 'ct_producto_tarifario_paquete.id_producto')
            ->select('prod.nombre', 'ct_producto_tarifario_paquete.id_producto', 'prod.codigo', 'ct_producto_tarifario_paquete.id_seguro', 'ct_producto_tarifario_paquete.precio', 'ct_producto_tarifario_paquete.id_nivel', 'ct_producto_tarifario_paquete.id as id_prod_tar_paq')
            ->get();

        /*if(!is_null($prod_tar_paq){

              return view('contable/productos/modal_prod_tarif_paquet', ['id_prod_paq' => $id_prod_paq,'id_producto' => $id_producto,'id_paquete' => $id_paquete,'prod_tar_paq' => $prod_tar_paq]);

            }else{

              $product_tarifario = Ct_Productos_Tarifario::where('estado', '1')
                                                           where('estado', '1')
                                                           ->get();

            }*/


        return view('contable/productos/modal_prod_tarif_paquet', ['id_prod_paq' => $id_prod_paq, 'id_producto' => $id_producto, 'id_paquete' => $id_paquete, 'prod_tar_paq' => $prod_tar_paq]);
    }

    public function  store_producto_tarifario_paquete(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        $input = [

            'id_producto_tarifario'  => $ip_cliente,
            'id_paquete'             => $ip_cliente,
            //'estado'                 => '1';
            'ip_creacion'            => $ip_cliente,
            'ip_modificacion'        => $ip_cliente,
            'id_usuariocrea'         => $id_usuario,
            'id_usuariomod'          => $id_usuario

        ];

        Ct_Producto_Tarifario_Paquete::create($input);
    }

    public function modal_crear_tarifario($id_prod_paq, $id_producto, $id_paquete, $cantidad)
    {

        $seguros = Seguro::where('seguros.inactivo', '1')
            ->where('promo_seguro', '<>', 1)
            ->orderBy('seguros.nombre')
            ->get();

        //$productos = Ct_productos::where('estado_tabla', '1')->get();

        /*return view('contable/productos/modal_producto_tarifario',['seguros' => $seguros,'productos' => $productos,'id_producto' => $id_producto,'id_paquete' => $id_paquete]);*/

        return view('contable/productos/modal_producto_tarifario', ['id_prod_paq' => $id_prod_paq, 'seguros' => $seguros, 'id_producto' => $id_producto, 'id_paquete' => $id_paquete, 'cantidad' => $cantidad]);
    }


    //Anula Producto Tarifario
    public function  anula_producto_tarifario(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $act_estado = [

            'estado'  => '0',
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ];


        $act_prod = Ct_Productos_Tarifario::where('id', $request['id_prod_tar'])
            ->update($act_estado);
    }


    //Anula Producto Tarifario Paquete
    public function  anula_producto_tarifario_paquete(Request $request)
    {


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $act_estado = [

            'estado'  => '0',
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ];

        $act_prod_tar = Ct_Producto_Tarifario_Paquete::where('id', $request['id_pr_paq'])
            ->update($act_estado);
    }


    public function update_producto_tarifario_paquete(Request $request, $id_prod_tar_paq)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $prod_tari_paq = Ct_Producto_Tarifario_Paquete::where('id', $id_prod_tar_paq)
            ->where('estado', '1')
            ->first();
        $arr = [
            'precio'          => $request['precio'],
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        $prod_tari_paq->update($arr);

        return "ok";
    }

    public function recarga_prod_tarifario($id_prod, $id_paq)
    {


        $prod_tar_paq = Ct_Producto_Tarifario_Paquete::where('id_producto', $id_prod)
            ->where('id_paquete', $id_paq)
            ->where('estado', '1')
            ->select(
                'ct_producto_tarifario_paquete.id_producto',
                'ct_producto_tarifario_paquete.id_seguro',
                'ct_producto_tarifario_paquete.precio',
                'ct_producto_tarifario_paquete.id_nivel',
                'ct_producto_tarifario_paquete.id as id_prod_tar_paq',
                'ct_producto_tarifario_paquete.cantidad as cantidad'
            )
            ->get();



        return view('contable/productos/index_tarif_paq', ['prod_tar_paq' => $prod_tar_paq, 'id_prod' => $id_prod]);
    }

    /*public function (){

        $prod_tar_paq = Ct_Producto_Tarifario_Paquete::where('id_producto', $id_paquete)
                                                       ->where('estado', '1')
                                                       ->join('ct_productos as prod', 'prod.id', 'ct_producto_tarifario_paquete.id_producto')
                                                       ->select('prod.nombre', 'ct_producto_tarifario_paquete.id_producto', 'prod.codigo','ct_producto_tarifario_paquete.id_seguro','ct_producto_tarifario_paquete.precio','ct_producto_tarifario_paquete.id_nivel','ct_producto_tarifario_paquete.id as id_prod_tar_paq')
                                                       ->get();


    }*/

    public function modal_crear_paquete()
    {

        return view('contable/productos/modal_prod_paquet');
    }

    public function cargar_producto_paquete($id)
    {

        //return $id;

        //$c = [];
        //$x = 0;

        $prod_paq = Ct_productos_paquete::where('id_producto', $id)
            ->where('estado', '1')
            ->join('ct_productos as prod', 'prod.id', 'ct_productos_paquete.id_producto')
            ->select(
                'ct_productos_paquete.cantidad as cantidad',
                'ct_productos_paquete.id_producto as id_producto',
                'ct_productos_paquete.id_paquete as id_paquete',
                'ct_productos_paquete.precio as precio',
                'ct_productos_paquete.nombre as nombre',
                'ct_productos_paquete.id as id_prod_paq'
            )
            ->get();


        if (!is_null($prod_paq)) {

            return view('contable/productos/index_prod_paq', ['prod_paq' => $prod_paq, 'id_prod' => $id]);
        }

        /*if (!is_null($prod_paq)) {

            foreach ($prod_paq as $value) {

                $c[$x] = ['id' => $value->id,'cantid' => $value->cantidad, 'paquete' => $value->nombre, 'pvp' => $value->precio,'id_producto' => $value->id_producto,'id_paquete' => $value->id_paquete];

                $x++;
            }

            return $c;

        } else {
            return "no";
        }*/
    }


    public function buscar_producto_nombre(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $nombre    = $request['term'];
        $data      = array();
        $productos = DB::table('ct_productos')->where('id_empresa', $id_empresa)->where('nombre', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value1' => $product->id, 'value' => $product->nombre, 'codig_product' =>  $product->codigo, 'iva_prod' =>  $product->iva);
        }
        if (count($data) > 0) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }

    public function store_produc_paquete(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $cant = $request['cant'];
        $id_prod = $request['id_prod'];
        $id_prod_paq = $request['id_prod_paq'];
        $nomb_prod = $request['nomb_prod'];
        $prec = $request['prec'];
        $iva_prod = $request['iva_prod'];

        //Verifica si ya existe el Paquete Ingresado para el Producto
        $exist_paq_prod = Ct_productos_paquete::where('ct_productos_paquete.estado', '1')
            ->where('ct_productos_paquete.id_producto', $id_prod)
            ->where('ct_productos_paquete.id_paquete', $id_prod_paq)
            ->first();

        if (!is_null($exist_paq_prod)) {

            $msj = "ok";
            return ['msj' => $msj];
        } else {

            $input_prod_paq = [

                'id_usuariocrea'   => $idusuario,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,
                'nombre'           => $nomb_prod,
                'id_paquete'       => $id_prod_paq,
                'cantidad'         => $cant,
                'id_producto'      => $id_prod,
                'cantidad'         => $cant,
                'precio'           => $prec,
                'iva'              => $iva_prod,

            ];

            $id = Ct_productos_paquete::insertGetId($input_prod_paq);
        }



        /*$cant = $request['paque_cant'];
        $id_prod = $request['id_prod_pr'];
        $id_prod_paq = $request['id_paquete'];
        $nomb_prod = $request['paque_id'];
        $prec = $request['precio_paq'];*/



        //return redirect()->route('productos_servicios_editar', ['id' =>$id]);

        //$msj =  "ok";

        //return ['msj' => $msj];

        //$count = Hc4_Biopsias::where('hc_id_procedimiento', $request['hc_id_procedimiento'])->get()->count();

        /*$prod_paq = Ct_productos_paquete::find($id);

        return ['id' => $id, 'cantidad' => $prod_paq->cantidad, 'nomb_paq' => $prod_paq->nombre, 'precio_pvp' => $precio];*/

        //return redirect()->route('productos_servicios_editar', ['id' => $p_id]);

    }


    //Guarda Precio del Producto
    public function store_precio_produc(Request $request)
    {


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $cod_prod = $request['cod_prod'];
        $nivel_prod = $request['nivel_pre'];
        $prec_prod = $request['prec_prod'];

        //Verifica si ya existe el Producto con el mismo Nivel y Precio
        $exist_nivel_precio = PrecioProducto::where('precio_producto.estado', '1')
            ->where('precio_producto.codigo_producto', $cod_prod)
            ->where('precio_producto.nivel', $nivel_prod)
            ->first();



        if (!is_null($exist_nivel_precio)) {

            $msj = "ok";
            return ['msj' => $msj];
        } else {

            $input_precio_prod = [

                'codigo_producto' => $cod_prod,
                'nivel'  => $nivel_prod,
                'precio'   => $prec_prod,
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,

            ];

            $id = PrecioProducto::insertGetId($input_precio_prod);
        }
    }


    //Anula Producto Paquete
    public function  anula_producto_paquete(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $act_estado = [

            'estado'  => '0',
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ];

        $act_prod_paq = Ct_productos_paquete::where('id', $request['id_pr_paq'])
            ->update($act_estado);
    }

    //Anula Precio Producto
    public function  anula_producto_precio(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        //dd($request['id_pr_prod']);

        $act_estado = [

            'estado'  => '0',
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ];

        $act_prod_precio = PrecioProducto::where('id', $request['id_pr_prod'])->update($act_estado);
    }



    //Carga Precio Porducto
    public function cargar_precio_producto($cod_product)
    {

        $prod_precio = PrecioProducto::where('codigo_producto', $cod_product)
            ->select(
                'precio_producto.nivel as nivel_precio',
                'precio_producto.precio as precio_producto',
                'precio_producto.id as id_prec_prod'
            )->where('estado', '1')->get();



        if (!is_null($prod_precio)) {

            return view('contable/productos/index_prod_precio', ['prod_precio' => $prod_precio]);
        }
    }

    //Recarga Tabla ct_orden_venta_detalle_paquete
    public function recarga_orden_detalle_paquete($id_vent)
    {

        //Consultamos la Tabla CT_VENTA
        $fact_venta = Ct_ventas::findorfail($id_vent);

        if ($fact_venta != null) {

            $orden = Ct_Orden_Venta::find($fact_venta->orden_venta);
            $detalles = $orden->detalles;
        }

        return view('contable/facturacion/index_orden_detalle_paq', ['detalles' => $detalles, 'orden' => $orden]);
    }

    //Editar Orden Detalle Paquete
    public function edit_orden_detalle_paquete($id_ord)
    {

        $orde_det_paq = Ct_Orden_Venta_Detalle_Paquete::where('id', $id_ord)
            ->where('estado', '1')
            ->first();

        return view('contable/facturacion/modal_editar_det_paquete', ['orde_det_paq' => $orde_det_paq]);
    }

    //Actualiza Orden Detalle Paquete
    public function update_ord_detalle_paquete(Request $request, $id_ord)
    {


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $upd_det_paq = Ct_Orden_Venta_Detalle_Paquete::where('id', $id_ord)
            ->where('estado', '1')
            ->first();
        if (!is_null($upd_det_paq->iva)) {

            $val_iva = ($request['cantidad'] * $request['precio']) * $upd_det_paq->iva;
        }

        $arr = [
            'cantidad'        => $request['cantidad'],
            'precio'          => $request['precio'],
            'valor_iva'       => $val_iva,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        $upd_det_paq->update($arr);

        return "ok";
    }

    //Actualiza Valor Total de Paquete con Seguro Particular
    public function update_val_total_paquete_part($id_prod)
    {

        return view('contable/productos/modal_upd_val_part', ['id_producto' => $id_prod]);
    }


    //Guarda Valor Total de Paquete con Seguro Particular
    public function store_total_valor_paquete(Request $request)
    {

        //return view('contable/productos/modal_upd_val_part', ['id_producto' => $id_prod]);

        Ct_productos::where('id', $request['id_prod'])->update(['valor_total_paq' => $request['precio']]);


        return "ok";
    }
    public function saldos_iniciales(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $saldos = Ct_Saldos_Producto::where('estado', '1')->where('id_empresa', $id_empresa)->paginate(10);
        $productos = Ct_productos::where('id_empresa', $id_empresa)->get();
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $id_producto = $request['id_producto'];
        if ($request['id_producto'] != null) {
            $constraints = [
                'id_producto' => $request['id_producto'],
                'descripcion' => $request['descripcion'],
            ];
            $saldos = $this->doSearchingQuerys($constraints, $id_empresa);
        }
        return view('contable/productos/saldos_iniciales', ['saldos' => $saldos, 'id_producto' => $id_producto, 'descripcion' => $request['descripcion'], 'empresa' => $empresa, 'productos' => $productos]);
    }
    private function doSearchingQuerys($constraints, $id_empresa)
    {
        $query  = Ct_Saldos_Producto::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {

                if ($fields[$index] == "id_producto") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }

            $index++;
        }

        return $query->where('id_empresa', $id_empresa)->paginate(10);
    }
    public function create_saldos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $productos = Ct_productos::where('id_empresa', $id_empresa)->get();
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        return view('contable/productos/create_saldos', ['empresa' => $empresa, 'productos' => $productos]);
    }
    public function store_iniciales(Request $request)
    {
        if (!is_null($request->id_producto)) {
            $saldos = Ct_Saldos_Producto::where('id_producto', $request->id_producto)->first();
            if (is_null($saldos)) {
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $idusuario  = Auth::user()->id;
                $id_empresa = $request->session()->get('id_empresa');
                $kardex = Ct_Kardex::insertGetId([
                    'fecha'                 => $request->fecha,
                    'id_movimiento'         => '0',
                    'movimiento'            => '0',
                    'tipo'                  => 'INVENTARIO',
                    'numero'                => '0001-0001',
                    'cantidad'              => $request->cantidad,
                    'valor_unitario'        => $request->costo,
                    'total'                 => $request->total,
                    'id_empresa'            => $id_empresa,
                    'producto_id'           => $request->id_producto,
                    'bodega_id'             => '1',
                    'saldo_cantidad'        => '0',
                    'saldo_valor_unitario'  => '0',
                    'saldo_total'           => '0',
                    'ip_creacion'           => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                ]);
                Ct_Saldos_Producto::create([
                    'fecha' => $request->fecha,
                    'id_producto' => $request->id_producto,
                    'descripcion' => $request->descripcion,
                    'id_empresa'            => $id_empresa,
                    'cantidad' => $request->cantidad,
                    'costo' => $request->costo,
                    'total' => $request->total,
                    'nota' => $request->nota,
                    'id_kardex' => $kardex,
                    'estado' => '1',
                    'ip_creacion'           => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                ]);
            } else {
                return redirect()->route('productos.saldos_iniciales')->withErrors([' Error: Ya existe el producto con su saldo inicial', 'Ya existe el producto con su saldo inicial']);
            }
        }
        return redirect()->route('productos.saldos_iniciales');
    }
    public function update_saldos($id, Request $request)
    {
        if (!is_null($request->id_producto)) {
            $saldos = Ct_Saldos_Producto::find($id);
            if (!is_null($saldos)) {
                $fnde = Ct_Kardex::find($saldos->id_kardex);
                $ip_cliente = $_SERVER["REMOTE_ADDR"];
                $idusuario  = Auth::user()->id;
                $id_empresa = $request->session()->get('id_empresa');
                $kardex = [
                    'fecha'                 => $request->fecha,
                    'id_movimiento'         => '0',
                    'movimiento'            => '0',
                    'tipo'                  => 'INVENTARIO',
                    'numero'                => '0001-0001',
                    'cantidad'              => $request->cantidad,
                    'valor_unitario'        => $request->costo,
                    'total'                 => $request->total,
                    'id_empresa'            => $id_empresa,
                    'producto_id'           => $request->id_producto,
                    'bodega_id'             => '1',
                    'saldo_cantidad'        => '0',
                    'saldo_valor_unitario'  => '0',
                    'saldo_total'           => '0',
                    'ip_creacion'           => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                ];
                $fnde->update($kardex);
                $arrays = [
                    'fecha' => $request->fecha,
                    'id_producto' => $request->id_producto,
                    'descripcion' => $request->descripcion,
                    'cantidad' => $request->cantidad,
                    'costo' => $request->costo,
                    'total' => $request->total,
                    'nota' => $request->nota,
                    'estado' => '1',
                    'ip_creacion'           => $ip_cliente,
                    'id_usuariocrea'        => $idusuario,
                ];
                $saldos->update($arrays);
            } else {
                return redirect()->back()->withErrors(['msg', 'No existe']);
            }
        }
        return redirect()->route('productos.saldos_iniciales');
    }
    public function edit_saldos($id, Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $productos = Ct_productos::where('id_empresa', $id_empresa)->get();
        $saldos = Ct_Saldos_Producto::find($id);
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        return view('contable/productos/edit_saldos', ['empresa' => $empresa, 'productos' => $productos, 'saldos' => $saldos]);
    }


    public function productoNoLigados()
    {
        $id_empresa = Session::get("id_empresa");
        $ct_productos = Ct_productos::where("id_empresa", $id_empresa)->where("estado_tabla", 1)->get();

        Excel::create('Productos', function ($excel) use ($ct_productos) {
            $excel->sheet('Productos', function ($sheet) use ($ct_productos) {

                $title["data"] = ["Cod. Contable", "Nomb. Contable", "Cod. Compras", "Nomb. Compras"];
                $title["background-color"] = "#000000";
                $title["color"] = "#FCFCFC";
                // $title["comienzo"] ++;
                $comienzo = 2;

                excelCreate::details($sheet, $title);
                foreach ($ct_productos as $ct_producto) {
                    $producto_insumo = Ct_productos_insumos::where('id_producto', $ct_producto->id)->get();
                    // $comienzo++;
                    if (count($producto_insumo) > 0) {
                        foreach ($producto_insumo  as $insumos) {
                            $data_insumos["data"] = [$ct_producto->codigo, $ct_producto->nombre, isset($insumos->producto_compra) ? $insumos->producto_compra->codigo : 'P',  isset($insumos->producto_compra) ? $insumos->producto_compra->nombre : 'P'];
                            $data_insumos["comienzo"] = $comienzo;
                            excelCreate::details($sheet, $data_insumos);
                            $comienzo++;
                        }
                    } else {
                        $data_no_ligado["data"] = [$ct_producto->codigo, $ct_producto->nombre, "No esta ligado con compras", "No esta ligado con compras"];
                        $data_no_ligado["background-color"] = "#D25A5A";
                        $data_no_ligado["color"] = "#FCFCFC";
                        $data_no_ligado["comienzo"] = $comienzo;
                        excelCreate::details($sheet, $data_no_ligado);
                        $comienzo++;
                    }
                }
            });
        })->export('xlsx');
    }
}
