<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_productos;
use Sis_medico\PrecioProducto;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Orden_Venta_Detalle;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Empresa;
use Sis_medico\Agenda;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Convenio;
use Sis_medico\EstadoResultado;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Plan_Cuentas;
use Sis_medico\CtCajaCobro;
use Sis_medico\Seguro;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_Orden_Venta_Pago;
use Sis_medico\Membresia;
use Sis_medico\UserMembresia;
use Sis_medico\AppProductoMembresia;
use Sis_medico\SeguroTipos;
use Sis_medico\LogReciboDeCobro;
use Sis_medico\Comisiones_Gastro;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Producto_Comision;
use Sis_medico\Http\Controllers\laboratorio\MembresiasLabsController;
use Sis_medico\Utilidades;

class NuevoReciboCobroController extends Controller
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

    public function crear($id_agenda)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $agenda = Agenda::find($id_agenda);

        $paciente = $agenda->paciente;

        $convenio = Convenio::where('id_seguro')->where('estado', '1')->get()->first();

        $id_nivel = null;
        if (!is_null($convenio)) {
            $id_nivel = $convenio->id_nivel;
        }

        $cliente = Ct_Clientes::find($agenda->id_paciente);

        $tipo_identificacion    = null;
        $telefono   = null;
        $razon_social           = null;
        $email      = null;
        $ciudad                 = null;
        $direccion              = null;
        if (!is_null($cliente)) {
            $tipo_identificacion    = $cliente->tipo;
            $razon_social           = $cliente->nombre;
            $ciudad                 = $cliente->ciudad_representante;
            $direccion              = $cliente->direccion_representante;
            $telefono               = $cliente->telefono1_representante;
            $email                  = $cliente->email_representante;
        } else {

            $cl_nombre = $paciente->apellido1;
            if ($paciente->apellido2 != '(N/A)' && $paciente->apellido2 != 'N/A' && $paciente->apellido2 != '.') {
                $cl_nombre = $cl_nombre . ' ' . $paciente->apellido2;
            }
            $cl_nombre = $cl_nombre . ' ' . $paciente->nombre1;
            if ($paciente->nombre2 != '(N/A)' && $paciente->nombre2 != 'N/A' && $paciente->nombre2 != '.') {
                $cl_nombre = $cl_nombre . ' ' . $paciente->nombre2;
            }


            ct_clientes::create([
                'identificacion'          => $agenda->id_paciente,
                'nombre'                  => $cl_nombre,
                'tipo'                    => '5',
                'clase'                   => 'normal',
                'estado'                  => '1',
                'ciudad_representante'    => $paciente->ciudad,
                'direccion_representante' => $paciente->direccion,
                'telefono1_representante' => $paciente->telefono1,
                'email_representante'     => $paciente->usuario->email,
                'pais'                    => 'Ecuador',
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
            ]);

            $cliente = Ct_Clientes::find($agenda->id_paciente);

            $tipo_identificacion    = $cliente->tipo;
            $razon_social           = $cliente->nombre;
            $ciudad                 = $cliente->ciudad_representante;
            $direccion              = $cliente->direccion_representante;
            $telefono               = $cliente->telefono1_representante;
            $email                  = $cliente->email_representante;
        }

        $id_empresa = $agenda->id_empresa;
        if ($id_empresa == null) {
            $empresa = Empresa::where('prioridad', '1')->get()->first();
            $id_empresa = $empresa->id;
        }

        $arr_orden_venta = [
            'id_agenda'     => $id_agenda,
            'id_empresa'    => $id_empresa,
            'fecha_emision' => date('Y-m-d'),
            'nueva_fecha'   => date('Y-m-d H:i:s'),
            'oda'           => 0,
            'id_seguro'     => $agenda->id_seguro,
            'id_nivel'      => $id_nivel,
            'tipo_identificacion'   => $tipo_identificacion,
            'identificacion'        => $agenda->id_paciente,
            'razon_social'          => $razon_social,
            'ciudad'                => $ciudad,
            'direccion'             => $direccion,
            'telefono'              => $telefono,
            'email'                 => $email,
            //'total FLOAT DEFAULT 0,
            //'iva FLOAT DEFAULT 0,
            //'subtotal_0 FLOAT DEFAULT 0,
            //'subtotal_12 FLOAT DEFAULT 0,
            'id_usuariocrea'        =>  $idusuario,
            'id_usuariomod'         =>  $idusuario,
            'ip_creacion'           =>  $ip_cliente,
            'ip_modificacion'       =>  $ip_cliente,
            'valor_oda'             =>  0,
            'total_sin_tarjeta'     =>  0,
            //'observacion' MEDIUMTEXT DEFAULT NULL,
            //'caja VARCHAR(255) DEFAULT 'NULL',
            'estado'                => -1,
            'numero_oda'            => 0,
            //'descuento FLOAT DEFAULT 0,
            //'total_cobrar_seguro FLOAT DEFAULT 0,
        ];

        $id_orden = Ct_Orden_Venta::insertGetId($arr_orden_venta);

        $arr_log_recibo = [
            'id_ct_orden_venta' => $id_orden,
            'descripcion'       => 'CREADA EL: ' . date('Y-m-d H:i:s'),
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];
        LogReciboDeCobro::create($arr_log_recibo);

        //dd($id_orden);

        return redirect()->route('nuevorecibocobro.editar', ['id' => $id_orden]);
    }

    public function editar($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $orden      = Ct_Orden_Venta::find($id);
        $agenda     = $orden->agenda;
        $empresa    = $orden->empresa;
        $paciente   = $agenda->paciente;
        $cajas      = CtCajaCobro::where('estado', 1)->get();
        $puntoEmision = Ct_Sucursales::join('ct_caja as c', 'ct_sucursales.id', 'c.id_sucursal')
            ->where('c.estado', 1)
            ->where('ct_sucursales.estado', 1)
            ->where('ct_sucursales.id_empresa', session('id_empresa'))
            ->get([
                'ct_sucursales.id as id_sucursal',
                'ct_sucursales.nombre_sucursal',
                'c.id as id_caja',
                'ct_sucursales.codigo_sucursal',
                'c.codigo_caja'
            ]);
        $seguros = Seguro::where('seguros.inactivo', '1')
            ->where('promo_seguro', '<>', 1)
            ->orderBy('nombre', 'asc')->get();
        //    
        $membresia_controller = new MembresiasLabsController;
        $res = $membresia_controller->buscar_membresia_IECED($paciente->id); //dd($res);
        $tipos_seguros = SeguroTipos::where('estado', '>', '1')->get();
        $doctor_referido = Comisiones_Gastro::where("estado", 1)->get();
        return view('contable/nuevo_recibo/editar', ['doctor_referido' => $doctor_referido, 'orden' => $orden, 'agenda' => $agenda, 'empresa' => $empresa, 'seguros' => $seguros, 'paciente' => $paciente, 'cajas' => $cajas, 'res' => $res, 'tipos_seguros' => $tipos_seguros, 'puntoEmision' => $puntoEmision]);
    }

    public function modal_actualiza_cliente($id_cliente, $id_orden)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $cliente = Ct_Clientes::where('identificacion', $id_cliente)->where('estado', '1')->first();
        return view('contable/nuevo_recibo/datos_cliente_rc', ['cliente' => $cliente, 'id_orden' => $id_orden]);
    }



    /*public function detalles($id_orden)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $porIva = Utilidades::porcentajeIva();
        if ($porIva == '') {
            $data['mensajes'] = 'No se ha configurado el porcentaje del iva';
            return view('errors/204', $data);
        }
        
        $orden      = Ct_Orden_Venta::find($id_orden);
        $detalles   = $orden->detalles;

        return view('contable/nuevo_recibo/detalles', ['orden' => $orden, 'detalles' => $detalles, 'porIva' => $porIva]);
    }*/
    public function detalles($id_orden)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $porIva = Utilidades::porcentajeIva();
        if ($porIva == '') {
            $data['mensajes'] = 'No se ha configurado el porcentaje del iva';
            return view('errors/204', $data);
        }

        $orden      = Ct_Orden_Venta::find($id_orden);
        $detalles   = $orden->detalles;
        //$sumaD = Ct_Orden_Venta_Detalle::where("id_orden", $id_orden)->where("cod_prod", "like", "%DEDUC.%")->sum("total") ?? 0;
        //$sumaDf = Ct_Orden_Venta_Detalle::where("id_orden", $id_orden)->where("cod_prod", "like", "%FEE SEGURO PRIVADO%")->sum("total") ?? 0;
        //$sumaT = $sumaD + $sumaDf;
        return view('contable/nuevo_recibo/detalles', ['orden' => $orden, 'detalles' => $detalles, 'porIva' => $porIva]);
    }

    public static function calcular_comision($id_orden_venta)
    {

        try {
            $orden    = Ct_Orden_Venta::find($id_orden_venta);
            $detalles = $orden->detalles;
            foreach ($detalles as $value) { //ct_orden_venta_detalle
                if (is_null($value->check_comision)) {
                    $buscar_producto = Producto_Comision::where('id_ct_producto', $value->id_producto)->where('id_comision_gatro', $orden->doctor_referido)->first();
                    if (!is_null($buscar_producto)) {
                        //particulares
                        $comision = 0;
                        if ($orden->seguro->tipo_seguro->id == 2) { //privado
                            $comision = ($buscar_producto->valor_privado * $buscar_producto->porcentaje_privado) / 100; //porcentaje del particular                           
                        }
                        if ($orden->seguro->tipo_seguro->id == 3) { //particulares
                            $comision = ($buscar_producto->valor * $buscar_producto->porcentaje) / 100;
                        }
                        //guardo en la cabecera de ct_orden_venta
                        $total_comision = $orden->comision_total +  $comision;
                        $orden->comision_total = $total_comision;
                        $orden->save();

                        $value->comision = $comision;
                        $value->check_comision = 1;
                        $value->save();
                    } else {
                        $value->check_comision = 1;
                        $value->comision = 0;
                        $value->save();
                    }
                }
            }
            return ['status' => true, 'msj' => 'Guarado Correctamente'];
        } catch (\Exception $e) {
            dd($e->getMessage());
            return ['status' => false, 'msj' => $e->getMessage()];
        }
    }
    public function guardar_producto(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $p_dcto    = 0;
        $descuento = 0;

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        $id_orden = $request->id;
        $id_producto = $request->producto_nuevo;

        //RUTINA PARA OBTENER EL PRECIO DE LOS PRODUCTOS
        $orden    = Ct_Orden_Venta::find($id_orden);
        $producto = Ct_productos::find($id_producto);
        $precio   = $producto->valor_total_paq;

        if ($precio == null || $precio == 0) {
            $precio_producto = PrecioProducto::where('codigo_producto', $producto->codigo)->orderBy('nivel', 'asc')->get()->first();
            if (!is_null($precio_producto)) {
                $precio = $precio_producto->precio;
            } else {
                $precio = 0;
            }
        }
        $valor_pvp = $precio;
        $valor_promo = 0;
        $vtotal    = $precio;
        if ($orden->id_nivel != null) {
            $tarifario = $producto->tarifarios->where('nivel', $orden->id_nivel)->where('estado', 1)->first();
            if (!is_null($tarifario)) {
                $precio = $tarifario->precio_producto;
                $vtotal = $precio;
                if ($orden->id_seguro != null) {
                    if ($orden->seguro->id_seguro_tipos != null) {
                        if ($orden->seguro->tipo_seguro->estado == '3') {
                            $valor_promo = $precio; //dd($tarifario,$valor_pvp,$valor_promo);
                            $p_dcto    = ($valor_pvp - $valor_promo) / $valor_pvp;
                            $p_dcto    = round($p_dcto, 2);
                            $p_dcto    = $p_dcto * 100;
                            $descuento = ($valor_pvp - $valor_promo);
                            $precio    = $valor_pvp;
                            $vtotal    = $precio - $descuento;
                        }
                    }
                }
            }
        }

        ////////////////////////////////////////////////

        $iva = 0;
        if ($producto->iva == 1) {
            $iva = Ct_Configuraciones::find('3');
            if (!is_null($iva)) {
                $iva = $iva->iva;
            } //TERRIBLE EL TEMA DEL IVA
        }
        //dd($id_orden);

        $valor_iva = $precio * $iva;
        $valor_iva = round($valor_iva, 2);

        $arr_producto = [
            'id_orden' => $id_orden,
            'cod_prod' => $producto->codigo,
            //'descripcion'   => $producto->nombre,
            'nombre_producto'   => $producto->nombre,
            'id_producto'   => $id_producto,
            'precio'        => $precio,
            'cantidad'      => 1,
            'total'         => $vtotal,
            //ident_paquete TINYINT(4) DEFAULT NULL,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            //'tipo_oda' VARCHAR(1) DEFAULT 'NULL',
            'p_oda'           => 0,
            'valor_oda'       => 0,
            'tipo_dcto' => '%',
            'p_dcto'    => $p_dcto,
            'descuento' => $descuento,
            'iva'       => $iva,
            'valor_iva' => $valor_iva,
            //valor_deducible FLOAT DEFAULT 0,
            //valor_oda_hum FLOAT DEFAULT 0,
            //porc_dedu_segu FLOAT DEFAULT NULL,
            //porc_dedu_paci FLOAT DEFAULT NULL,
            //fee FLOAT DEFAULT NULL,
            'cobrar_paciente' => $precio,
            'cobrar_seguro'   => 0,
        ];

        Ct_Orden_Venta_Detalle::create($arr_producto);

        $this->recalcular($id_orden);


        return "ok";
    }


    public function actualizar_producto(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        $id_detalle = $request->id;
        $id_producto = $request->id_producto;
        $cantidad = $request->cantidad;
        $precio   = $request->precio;
        $p_cpac   = $request->p_cpac;
        $deducible = $request->deducible;
        //$p_oda    = $request->p_oda;
        //$cobrar_paciente = $request->cobrar_paciente;
        $p_dcto    = $request->p_dcto;
        //$descuento = $request->descuento;
        $p_oda     = 100 - $p_cpac;

        $detalle  = Ct_Orden_Venta_Detalle::find($id_detalle);
        $producto = Ct_productos::find($id_producto);
        $orden    = $detalle->orden;

        $iva = 0;
        if ($orden->id_seguro != 10) {
            if ($producto->iva == 1) {
                $iva = Ct_Configuraciones::find('3');
                if (!is_null($iva)) {
                    $iva = $iva->iva;
                } //TERRIBLE EL TEMA DEL IVA
            }
        }




        $subtotal  = $cantidad * $precio;
        if ($orden->seguro->id == '4') {
            $subtotal  = $subtotal - $deducible;
        }
        //dd($subtotal, $deducible, $request->all());

        $descuento = $subtotal * $p_dcto / 100;
        $descuento = round($descuento, 2);
        //$subtotal  = $subtotal - $descuento;

        $cobrar_paciente = $subtotal * $p_cpac / 100;
        $cobrar_paciente = round($cobrar_paciente, 2);

        $valor_oda       = $subtotal - $cobrar_paciente;
        $subtotal        = $subtotal - $valor_oda;
        if ($orden->seguro->id != '4') {
            $valor_oda   = $valor_oda - $deducible;
        }


        $valor_iva = ($subtotal - $descuento) * $iva;
        $valor_iva = round($valor_iva, 2);
        $total     = $subtotal - $descuento + $valor_iva;
        //dd($id_orden);

        $arr_producto = [

            'precio'            => $precio,
            'cantidad'          => $cantidad,
            'total'             => $subtotal - $descuento,
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
            'p_oda'             => $p_oda,
            'valor_oda'         => $valor_oda,
            'p_dcto'            => $p_dcto,
            'descuento'         => $descuento,
            'iva'               => $iva,
            'valor_iva'         => $valor_iva,
            'cobrar_paciente'   => $cobrar_paciente,
            'cobrar_seguro'     => $valor_oda,
            'valor_deducible'   => $deducible
        ];

        $detalle->update($arr_producto);

        $this->recalcular($detalle->id_orden);

        return "ok";
    }


    public static function recalcular($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden    = Ct_Orden_Venta::find($id);

        $total_con_descuento = $orden->detalles->sum('total');
        $total_iva           = $orden->detalles->sum('valor_iva');
        $total               = $total_con_descuento + $total_iva;
        $subtotal_0          = $orden->detalles->where('iva', 0)->sum('cobrar_paciente');
        $subtotal_12         = $orden->detalles->where('iva', '>', 0)->sum('cobrar_paciente');
        $oda                 = $orden->detalles->sum('cobrar_seguro');
        $descuento           = $orden->detalles->sum('descuento');

        $arr_cabecera = [
            'total'                 => $total,
            'iva'                   => $total_iva,
            'subtotal_0'            => $subtotal_0,
            'subtotal_12'           => $subtotal_12,
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'valor_oda'             => $oda,
            'total_sin_tarjeta'     => $total_con_descuento,
            'descuento'             => $descuento,
            'total_cobrar_seguro'   => $oda,
        ];

        $orden->update($arr_cabecera);
    }

    public function actualizar_descripcion(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        $id_detalle = $request->id;
        $id_producto = $request->id_producto;
        $descripcion = $request->descripcion;

        $detalle  = Ct_Orden_Venta_Detalle::find($id_detalle);

        $arr_producto = [

            'descripcion'            => $descripcion,
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $detalle->update($arr_producto);


        return "ok";
    }

    public function eliminar_producto(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_detalle = $request->id;


        $detalle  = Ct_Orden_Venta_Detalle::find($id_detalle);

        $id_orden = $detalle->id_orden;

        $detalle->delete();

        $this->recalcular($id_orden);

        return "ok";
    }

    public function actualizar_cabecera(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_orden       = $request->id_orden;
        $empresa        = $request->empresa;
        $id_agenda      = $request->id_agenda;
        $id_doctor      = $request->id_doctor;
        $f_procedimiento = $request->f_procedimiento;
        $f_emision      = $request->f_emision;
        $observacion    = $request->observacion;
        $observacion_admin    = $request->observacion_admin;
        $id_seguro      = $request->id_seguro;
        $id_nivel       = $request->id_nivel;
        $numero_oda     = $request->oda;
        $tipo_identificacion = $request->tipo_identificacion;
        $cedula         = $request->cedula;
        $razon_social   = $request->razon_social;
        $ciudad         = $request->ciudad;
        $direccion      = $request->direccion;
        $telefono       = $request->telefono;
        $email          = $request->email;
        $caja           = $request->caja;
        $idasesor       = $request->idasesor;
        $doctor_referido = $request->doctor_referido;

        $orden          = Ct_Orden_Venta::find($id_orden);

        $fecha_emision  = date('Y-m-d', strtotime($f_emision));
        $nueva_fecha    = date('Y-m-d H:i:s', strtotime($f_emision));
        $f_emision_oda = $request->f_emision_oda;
        $codigo_prestador = $request->codigo_prestador;

        $oda = 0;
        if ($numero_oda != null) {
            $oda = '1';
        }

        $input_cli_crea = [
            'identificacion'          => $cedula,
            'nombre'                  => $razon_social,
            'ciudad_representante'    => $ciudad,
            'direccion_representante' => $direccion,
            'telefono1_representante' => $telefono,
            'email_representante'     => $email,
            'tipo'                    => $tipo_identificacion,
            'clase'                   => '1',
            'cedula_representante'    => $cedula,
            'estado'                  => '1',
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $razon_social,
            'pais'                    => 'Ecuador',

        ];

        $input_cli_mod = [
            'identificacion'          => $cedula,
            'nombre'                  => $razon_social,
            'ciudad_representante'    => $ciudad,
            'direccion_representante' => $direccion,
            'telefono1_representante' => $telefono,
            'email_representante'     => $email,
            'tipo'                    => $tipo_identificacion,
            'clase'                   => '1',
            'cedula_representante'    => $cedula,
            'estado'                  => '1',
            'id_usuariomod'           => $idusuario,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $razon_social,
            'pais'                    => 'Ecuador',

        ];

        $cliente = Ct_Clientes::where('identificacion', $cedula)->first();

        if (!is_null($cliente)) {
            //Ct_Clientes::where('identificacion', $cedula)->update($input_cli_mod);
        } else {
            Ct_Clientes::create($input_cli_crea);
        }

        $arr_orden_venta = [

            'fecha_emision' => $fecha_emision,
            'nueva_fecha'   => $nueva_fecha,
            'oda'           => $oda,
            'id_seguro'     => $id_seguro,
            'id_nivel'      => $id_nivel,
            'tipo_identificacion'   => $tipo_identificacion,
            'identificacion'        => $cedula,
            'razon_social'          => $razon_social,
            'ciudad'                => $ciudad,
            'direccion'             => $direccion,
            'telefono'              => $telefono,
            'email'                 => $email,
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente,
            'observacion'           => $observacion,
            'observacion_admin'     => $observacion_admin,
            'caja'                  => $caja,
            'numero_oda'            => $numero_oda,
            'asesor_venta'          => $idasesor,
            'doctor_referido'       => $doctor_referido,
            'fecha_emision_oda'     => $f_emision_oda,
            'codigo_prestador'      => $codigo_prestador,

        ];

        $orden->update($arr_orden_venta);

        //dd("aguanta");
        $arr_log_recibo = [
            'id_ct_orden_venta' => $orden->id,
            'descripcion'       => 'ACTUALIZADA EL ' . date('Y-m-d H:i:s') . ': ' . $observacion_admin,
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];
        LogReciboDeCobro::create($arr_log_recibo);

        return "ok";
    }

    public function actualizar_cliente(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_orden       = $request->id_orden;
        $tipo_identificacion = $request->tipo_identificacion;
        $cedula         = $request->cedula;
        $razon_social   = $request->razon_social;
        $ciudad         = $request->ciudad;
        $direccion      = $request->direccion;
        $telefono       = $request->telefono;
        $email          = $request->email;

        $orden          = Ct_Orden_Venta::find($id_orden);


        $input_cli_crea = [
            'identificacion'          => $cedula,
            'nombre'                  => $razon_social,
            'ciudad_representante'    => $ciudad,
            'direccion_representante' => $direccion,
            'telefono1_representante' => $telefono,
            'email_representante'     => $email,
            'tipo'                    => $tipo_identificacion,
            'clase'                   => '1',
            'cedula_representante'    => $cedula,
            'estado'                  => '1',
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $razon_social,
            'pais'                    => 'Ecuador',

        ];

        $input_cli_mod = [
            'identificacion'          => $cedula,
            'nombre'                  => $razon_social,
            'ciudad_representante'    => $ciudad,
            'direccion_representante' => $direccion,
            'telefono1_representante' => $telefono,
            'email_representante'     => $email,
            'tipo'                    => $tipo_identificacion,
            'clase'                   => '1',
            'cedula_representante'    => $cedula,
            'estado'                  => '1',
            'id_usuariomod'           => $idusuario,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $razon_social,
            'pais'                    => 'Ecuador',

        ];

        $cliente = Ct_Clientes::where('identificacion', $cedula)->where('estado', '1')->first();

        if (!is_null($cliente)) {
            Ct_Clientes::where('identificacion', $cedula)->update($input_cli_mod);
        } else {
            Ct_Clientes::create($input_cli_crea);
        }

        $arr_orden_venta = [

            'tipo_identificacion'   => $tipo_identificacion,
            'identificacion'        => $cedula,
            'razon_social'          => $razon_social,
            'ciudad'                => $ciudad,
            'direccion'             => $direccion,
            'telefono'              => $telefono,
            'email'                 => $email,
            'id_usuariomod'         => $idusuario,
            'ip_modificacion'       => $ip_cliente,
        ];

        $orden->update($arr_orden_venta);

        return "ok";
    }

    public function formas_pago($id_orden)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }


        $tipo_pago    = Ct_Tipo_Pago::where('estado', 1)->get();
        $lista_banco  = Ct_Bancos::where('estado', 1)->get();
        $tipo_tarjeta = Ct_Tipo_Tarjeta::where('estado', 1)->get();


        $orden      = Ct_Orden_Venta::find($id_orden);
        $detalles   = $orden->pagos;

        return view('contable/nuevo_recibo/formas_pago', ['orden' => $orden, 'detalles' => $detalles, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'tipo_tarjeta' => $tipo_tarjeta]);
    }

    public function guardar_formapago(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        //dd($request->all());
        $id_orden  = $request->id;
        $fp_metodo_nuevo = $request->fp_metodo_nuevo;
        $fp_fecha_nueva  = $request->fp_fecha_nueva;
        $fp_tarjetanueva = $request->fp_tarjetanueva;
        $fp_numero_nuevo = $request->fp_numero_nuevo;
        $fp_banco        = $request->fp_banco;
        $fp_cuenta_nueva = $request->fp_cuenta_nueva;
        $fp_girado_nuevo = $request->fp_girado_nuevo;
        $fp_valor_nuevo  = $request->fp_valor_nuevo;
        $valor_ingreso   = $request->fp_ingresar;

        $arr_forma_pago = [
            'id_orden'  => $id_orden,
            'tipo'      => $fp_metodo_nuevo,
            'banco'     => $fp_banco,
            'cuenta'    => $fp_cuenta_nueva,
            'numero'    => $fp_numero_nuevo,
            'valor'     => $fp_valor_nuevo,
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'tipo_tarjeta'      => $fp_tarjetanueva,
            'posee_fi'          => 0,
            'p_fi'              => 0,
            'fecha'             => $fp_fecha_nueva,
            'valor_ingreso'     => $valor_ingreso,
        ];

        Ct_Orden_Venta_Pago::create($arr_forma_pago);

        return "ok";
    }

    public function eliminar_pago(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_detalle = $request->id;

        $detalle  = Ct_Orden_Venta_Pago::find($id_detalle);

        $id_orden = $detalle->id_orden;

        $detalle->delete();

        return "ok";
    }

    public function validar_valores($id_orden)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $orden      = Ct_Orden_Venta::find($id_orden);

        $pagos      = $orden->pagos;
        $detalles   = $orden->detalles;

        //$total_detalle = $detalles->sum('total');
        $total_detalle = round(floatval($orden->total), 2);
        $total_pago    = round(floatval($pagos->sum('valor')), 2);




        if ($total_detalle != $total_pago) {
            return ['estado' => "Error", 'mensaje' => 'Valor del Recibo no coincide con el valor del pago'];
        }

        if ($detalles->count() == 0) {
            return ['estado' => "Error", 'mensaje' => 'No tiene items ingresados'];
        }

        if ($pagos->count() == 0) {
            return ['estado' => "Error", 'mensaje' => 'No tiene formas de pago ingresadas'];
        }

        return ['estado' => "ok", 'mensaje' => ''];
    }

    public function emitir_recibo($id_orden)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $empresa    = Empresa::where('prioridad', '1')->get()->first();
        $orden      = Ct_Orden_Venta::find($id_orden);

        if (is_null($orden->agenda)) {
            return "error, sin agenda asociada";
        }
        //dd($orden->agenda->id_paciente);
        //dd("aguanta");
        $arr_log_recibo = [
            'id_ct_orden_venta' => $id_orden,
            'descripcion'       => 'EMITIDA EL: ' . date('Y-m-d H:i:s'),
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];
        LogReciboDeCobro::create($arr_log_recibo);

        $arr_orden_venta = [
            'id_usuariomod'     =>  $idusuario,
            'ip_modificacion'   =>  $ip_cliente,
            'estado'            => 1,
        ];
        $orden->update($arr_orden_venta);

        return "ok";
    }

    public function devolver(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $observacion_modal = $request['observacion'];
        $id_orden = $request['id'];

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $empresa    = Empresa::where('prioridad', '1')->get()->first();
        $orden      = Ct_Orden_Venta::find($id_orden);

        $arr_log_recibo = [
            'id_ct_orden_venta' => $id_orden,
            'descripcion'       => 'DEVUELTA EL: ' . date('Y-m-d H:i:s'),
            'observacion'       => $observacion_modal,
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];
        LogReciboDeCobro::create($arr_log_recibo);

        $arr_orden_venta = [
            'id_usuariomod'     =>  $idusuario,
            'ip_modificacion'   =>  $ip_cliente,
            'estado'            => -1,
        ];
        $orden->update($arr_orden_venta);

        return "ok";
    }

    public function crear_deducible($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $detalle    = Ct_Orden_Venta_Detalle::find($id);

        $orden      = $detalle->orden;

        if ($orden->id_seguro == '7') {
            $producto   = Ct_productos::where('nombre', 'like', '%FEE SEGURO PRIVADO%')->get()->first();
            $descripcion = 'FEE SEGURO PRIVADO';
        } else {
            $producto   = Ct_productos::where('nombre', 'like', '%DEDUCIBLE%')->get()->first();
            $descripcion = 'DEDUCIBLE DE ' . $detalle->nombre_producto;
        }

        if (!is_null($producto)) {

            $arr_producto = [
                'id_orden' => $orden->id,
                'cod_prod' => $producto->codigo,
                'descripcion'   => $descripcion,
                'nombre_producto'   => $producto->nombre,
                'id_producto'   => $producto->id,
                'precio'        => $detalle->valor_deducible,
                'cantidad'      => 1,
                'total'         => $detalle->valor_deducible,

                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

                'p_oda'           => 0,
                'valor_oda'       => 0,
                'tipo_dcto' => '%',
                'p_dcto'    => 0,
                'descuento' => 0,
                'iva'       => 0,
                'valor_iva' => 0,

                'cobrar_paciente' => $detalle->valor_deducible,
                'cobrar_seguro'   => 0,
            ];

            Ct_Orden_Venta_Detalle::create($arr_producto);

            $this->recalcular($orden->id);

            return "ok";
        }

        return "error";
    }

    public function crear_deducible_nuevo(Request $r)
    {
        try {
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            $detalle    = Ct_Orden_Venta_Detalle::find($r->id);

            $orden      = $detalle->orden;
            $product = [];
            //$detalleConDeducible = Ct_Orden_Venta_Detalle::where("id_orden", $detalle->id_orden)->where("cod_prod", "like", "%DEDUC%")->first();
            //$detalleConFee = Ct_Orden_Venta_Detalle::where("id_orden", $detalle->id_orden)->where("cod_prod", "like", "%FEE%")->first();
            $sumaD = 0;

            if ($orden->id_seguro == '7') {
                if ($r->deducible !== null /*&& $detalleConDeducible == null*/) {
                    $productoDeducible = Ct_productos::where('nombre', 'like', '%DEDUC%')->where('estado_tabla', '1')->first();
                    $product[] = [
                        "producto" => $productoDeducible,
                        "codigo" => $productoDeducible->nombre,
                        "descripcion" => 'DEDUCIBLE DE ' . $detalle->nombre_producto,
                        "precio" => $r->deducible,
                    ];
                    $sumaD += $r->deducible;
                }
                if ($r->fee !== null /*&& $detalleConFee == null*/) {
                    $productoFee = Ct_productos::where('nombre', 'like', '%FEE SEGURO PRIVADO%')->where('estado_tabla', '1')->first();
                    $product[] = [
                        "producto" => $productoFee,
                        "codigo" => 'FEE SEGURO PRIVADO',
                        "descripcion" => 'FEE SEGURO PRIVADO',
                        "precio" => $r->fee,
                    ];
                    $sumaD += $r->fee;
                }
            }

            if (count($product) > 0) {
                foreach ($product as $value) {
                    if (!is_null($value)) {
                        $arr_producto = [
                            'id_orden' => $orden->id,
                            'cod_prod' => $value["producto"]->codigo,
                            'descripcion'   => $value["descripcion"],
                            'nombre_producto'   => $value["producto"]->nombre,
                            'id_producto'   => $value["producto"]->id,
                            'precio'        => $value["precio"],
                            'cantidad'      => 1,
                            'total'         =>  $value["precio"],
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,

                            'p_oda'           => 0,
                            'valor_oda'       => 0,
                            'tipo_dcto' => '%',
                            'p_dcto'    => 0,
                            'descuento' => 0,
                            'iva'       => 0,
                            'valor_iva' => 0,

                            'cobrar_paciente' => $value["precio"],
                            'cobrar_seguro'   => 0,
                        ];
                        Ct_Orden_Venta_Detalle::create($arr_producto);
                    }
                }

                //$detalle->update(['valor_deducible'=> $sumaD]);
                $this->recalcular($orden->id);
            } else {
                return ["status" => true, "msj" => "repetido"];
            }
            return ["status" => true, "msj" => "ok", "total" => $sumaD];
        } catch (Exception $r) {
            return ["status" => false, "msj" => $r->getMessage(), "line" => $r->getLine()];
        }
    }

    public function lista_aprobacion()
    {

        $ordenes = Ct_Orden_Venta::join('agenda as a', 'a.id', 'ct_orden_venta.id_agenda')
            ->where('ct_orden_venta.estado', '<>', 0)
            ->where('ct_orden_venta.estado_aprobacion', '0')
            ->where('ct_orden_venta.id_seguro', 1)
            ->where('ct_orden_venta.descuento', '>', '0')
            ->where('a.proc_consul', '1')
            ->where('a.fechaini', '>', '2022-07-01 00:00:00')
            ->orderBy('a.fechaini', 'desc')
            ->select('ct_orden_venta.*', 'a.proc_consul', 'a.fechaini')
            ->get(); //dd($ordenes);

        return view('contable/nuevo_recibo/pendientes_aprobacion', ['ordenes' => $ordenes]);
    }

    public function aprobar($id)
    {

        $ordenes = Ct_Orden_Venta::find($id);

        $ordenes->update([
            'estado_aprobacion' => '1',
        ]);

        return "ok";
    }
}
