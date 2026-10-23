<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Ct_Clientes;
use Sis_medico\Examen_Orden;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Labs_Factura_Agrupada_Cab;
use Sis_medico\Labs_Factura_Agrupada_Detalle;
use Sis_medico\Labs_Factura_Agrupada_Orden;
use Sis_medico\User;
use Sis_medico\Log_usuario;
use Sis_medico\Examen_Nivel;
use Sis_medico\Http\Controllers\ApiFacturacionController;
use Illuminate\Support\Facades\DB;
use Excel;
use Exception;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Caja;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Paciente;
use Sis_medico\Seguro;

class FacturaAgrupadaController extends Controller
{
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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }

    public function index_factura_agrupada()
    {
        $anio = "";
        $mes = "";
        $factura_cab = Labs_Factura_Agrupada_Cab::where('estado', '1')->get();
        return view('laboratorio/agrupada/index', ['factura_cab' => $factura_cab, 'anio' => $anio, 'mes' => $mes]);
    }

    public function modal_registro()
    {
        $anio = date('Y');
        $mes  = date('m');
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', session('id_empresa'))
            ->get();
        return view('laboratorio/agrupada/modal_registro', ['anio' => $anio, 'mes' => $mes, 'sucursales' => $sucursales]);
    }
    /************** */
    public function modal_edit_cliente($id)
    {
        $cabecera =  Labs_Factura_Agrupada_Cab::find($id);
        $anio = date('Y');
        $mes  = date('m');
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', session('id_empresa'))
            ->get();
        return view('laboratorio/agrupada/modal_editar', ['anio' => $anio, 'mes' => $mes, 'cabecera' => $cabecera, 'sucursales' => $sucursales]);
    }

    /****************** */

    public function guardar_datos_agrupada_cab(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $arr = [
            'anio'              => $request['anio'],
            'mes'               => $request['mes'],
            'tipo_documento'    => $request['documento'],
            'cedula_factura'    => $request['cedula_factura'],
            'nombre_factura'    => $request['nombre_factura'],
            'direccion_factura' => $request['direccion_factura'],
            'ciudad_factura'    => $request['ciudad_factura'],
            'email_factura'     => $request['email_factura'],
            'telefono_factura'  => $request['telefono_factura'],
            'fecha_desde'       => $request['fecha_desde'],
            'fecha_hasta'       => $request['fecha_hasta'],
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'sucursal' => $request['sucursal_factura'] != '' ? $request['sucursal_factura'] : '001',
            'punto_emision' => $request['emision_factura'] != '' ? $request['emision_factura'] : '100'
        ];
        Labs_Factura_Agrupada_Cab::create($arr);
        $input_cli_crea = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',
        ];
        $input_cli_mod = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariomod'           => $idusuario,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',
        ];
        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();
        if (!is_null($cliente)) {
            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli_mod);
        } else {
            Ct_Clientes::create($input_cli_crea);
        }
        return "ok";
    }

    /*cambios: Editar facturas agrupadas*/

    public function editar_datos_agrupada_cab(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;


        // Labs_Factura_Agrupada_Cab::update($arr);
        $affected = DB::table('labs_factura_agrupada_cab')
            ->where('id', $request['id_cambio'])
            ->update([
                'anio'              => $request['anio'],
                'mes'               => $request['mes'],
                'tipo_documento'    => $request['documento'],
                'cedula_factura'    => $request['cedula_factura'],
                'nombre_factura'    => $request['nombre_factura'],
                'direccion_factura' => $request['direccion_factura'],
                'ciudad_factura'    => $request['ciudad_factura'],
                'email_factura'     => $request['email_factura'],
                'telefono_factura'  => $request['telefono_factura'],
                'fecha_desde'       => $request['fecha_desde'],
                'fecha_hasta'       => $request['fecha_hasta'],
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'sucursal' => $request['sucursal_factura'] != '' ? $request['sucursal_factura'] : '001',
                'punto_emision' => $request['emision_factura'] != '' ? $request['emision_factura'] : '100'
            ]);

        $input_cli_mod = [
            'identificacion'          => $request['cedula_factura'],
            'nombre'                  => $request['nombre_factura'],
            'ciudad_representante'    => $request['ciudad_factura'],
            'direccion_representante' => $request['direccion_factura'],
            'telefono1_representante' => $request['telefono_factura'],
            'email_representante'     => $request['email_factura'],
            'tipo'                    => $request['documento'],
            'clase'                   => '1',
            'cedula_representante'    => $request['cedula_factura'],
            'estado'                  => '1',
            'id_usuariomod'           => $idusuario,
            'ip_modificacion'         => $ip_cliente,
            'nombre_representante'    => $request['nombre_factura'],
            'pais'                    => 'Ecuador',

        ];

        $cliente = Ct_Clientes::where('identificacion', $request['cedula_factura'])->where('estado', '1')->first();

        if (!is_null($cliente)) {
            Ct_Clientes::where('identificacion', $request['cedula_factura'])->update($input_cli_mod);
        } else {
            //Ct_Clientes::update($input_cli_crea);
            $affected = DB::table('ct_clientes')
                ->where('identificacion', $request('cedula_factura'))
                ->update([
                    'identificacion'          => $request['cedula_factura'],
                    'nombre'                  => $request['nombre_factura'],
                    'ciudad_representante'    => $request['ciudad_factura'],
                    'direccion_representante' => $request['direccion_factura'],
                    'telefono1_representante' => $request['telefono_factura'],
                    'email_representante'     => $request['email_factura'],
                    'tipo'                    => $request['documento'],
                    'clase'                   => '1',
                    'cedula_representante'    => $request['cedula_factura'],
                    'estado'                  => '1',
                    'id_usuariomod'           => $idusuario,
                    'ip_modificacion'         => $ip_cliente,
                    'nombre_representante'    => $request['nombre_factura'],
                    'pais'                    => 'Ecuador',
                ]);
        }

        return redirect()->route('factura_agrupada.index_factura_agrupada');
    }
    /*Fin de cambios*/



    public function index_privadas($id_cab)
    {
        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);
        //dd($cabecera);
        $ordenes  = Examen_Orden::where('examen_orden.estado', '1')
            ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
            ->whereNull('comprobante')
            ->whereNull('fecha_envio')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where('s.tipo', '!=', '0')
            ->leftJoin('labs_factura_agrupada_orden as f_ord', 'examen_orden.id', '=', 'f_ord.id_examen_orden')
            ->whereNull('f_ord.id')->where('pago_online', '0')
            ->select('examen_orden.*', 'f_ord.id_examen_orden')->get();


        return view('laboratorio/agrupada/index_privada', ['ordenes' => $ordenes, 'id_cab' => $id_cab, 'nombre' => '', 'id_orden' => '', 'cedula' => '']);
    }

    /***********Buscador Indx_privadas*********************/

    public function __index_privadas_buscador__($id_cab, Request $request)
    {
        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);
        $cedula = $request['cedula'];
        $nombre = $request['nombre'];
        $ordenes = array();
        if ($nombre == "") {

            $ordenes  = Examen_Orden::where('examen_orden.estado', '1')
                ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
                ->whereNull('comprobante')
                ->whereNull('fecha_envio')
                ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                ->where('s.tipo', '!=', '0')
                ->where('examen_orden.id_paciente', 'like', '%' . $cedula . '%')
                ->leftJoin('labs_factura_agrupada_orden as f_ord', 'examen_orden.id', '=', 'f_ord.id_examen_orden')
                ->whereNull('f_ord.id')->where('pago_online', '0')
                ->select('examen_orden.*', 'f_ord.id_examen_orden')->get();
        } else if ($cedula == "") {
            // dd('hola desde nombre');
            $ordenes  = Examen_Orden::where('examen_orden.estado', '1')
                ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
                ->whereNull('comprobante')
                ->whereNull('fecha_envio')
                ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
                ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                ->where('s.tipo', '!=', '0')
                ->whereraw('CONCAT_WS (" ",p.apellido1,p.apellido2,p.nombre1,p.nombre2)  LIKE ?', ['%' . $nombre . '%'])
                ->leftJoin('labs_factura_agrupada_orden as f_ord', 'examen_orden.id', '=', 'f_ord.id_examen_orden')
                ->whereNull('f_ord.id')->where('pago_online', '0')
                ->select('examen_orden.*', 'f_ord.id_examen_orden')->get();
        }


        return view('laboratorio/agrupada/index_privada', ['ordenes' => $ordenes, 'id_cab' => $id_cab, 'nombre' => $nombre]);
    }

    public function index_privadas_buscador($id_cab, Request $request)
    {

        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);
        $cedula = $request['cedula'];
        $nombre = $request['nombre'];
        $id_orden = $request['id_orden'];

        $ordenes  = Examen_Orden::where('examen_orden.estado', '1')
            ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
            ->whereNull('comprobante')
            ->whereNull('fecha_envio')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
            ->where('s.tipo', '!=', '0')
            ->leftJoin('labs_factura_agrupada_orden as f_ord', 'examen_orden.id', '=', 'f_ord.id_examen_orden')
            ->whereNull('f_ord.id')
            ->where('pago_online', '0');
        if (!is_null($cedula)) {
            $ordenes = $ordenes->where('examen_orden.id_paciente', 'like', '%' . $cedula . '%');
        }

        if (!is_null($nombre)) {
            //$ordenes = $ordenes->whereraw('CONCAT_WS (" ",p.apellido1,p.apellido2,p.nombre1,p.nombre2)  LIKE ?', ['%' . $nombre . '%']);
            $nombres2 = explode(" ", $nombre);
            $cantidad = count($nombres2);


            if ($cantidad == '2' || $cantidad == '3') {
                $ordenes = $ordenes->where(function ($jq1) use ($nombre) {
                    $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', ['%' . $nombre . '%'])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                        ->orwhereraw('CONCAT(p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.nombre1," ",p.nombre2) LIKE ?', ['%' . $nombre . '%'])
                        ->orwhereraw('CONCAT(p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', ['%' . $nombre . '%'])
                        ->orwhereraw('CONCAT(p.nombre2," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                        ->orwhereraw('CONCAT(p.apellido2," ",p.nombre2) LIKE ?', ['%' . $nombre . '%']);
                });
            } else {

                $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%']);
            }
        }

        if (!is_null($id_orden)) {
            $ordenes = $ordenes->where('examen_orden.id', $id_orden);
        }

        $ordenes = $ordenes->select('examen_orden.*', 'f_ord.id_examen_orden')->get();


        return view('laboratorio/agrupada/index_privada', ['ordenes' => $ordenes, 'id_cab' => $id_cab, 'nombre' => $nombre, 'id_orden' => $id_orden, 'cedula' => $cedula]);
    }


    public function editar_privadas_buscador($id_cab, Request $request)
    {
        $cedula = $request['cedula'];
        $nombre = $request['nombre'];
        $id_orden = $request['id_orden'];

        $detalle = Labs_Factura_Agrupada_Detalle::where('id_agrup_cab', $id_cab)->where('estado', '1')->where('pub_priv', '1')->first();
        $ordenes = null;
        if (!is_null($detalle)) {

            $ordenes = Labs_Factura_Agrupada_Orden::where('labs_factura_agrupada_orden.id_agrup_det', $detalle->id)
                ->join('examen_orden', 'labs_factura_agrupada_orden.id_examen_orden', 'examen_orden.id')
                ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                ->where('labs_factura_agrupada_orden.estado', '1');

            if (!is_null($nombre)) {
                //$ordenes = $ordenes->whereraw('CONCAT_WS (" ",p.apellido1,p.apellido2,p.nombre1,p.nombre2)  LIKE ?', ['%' . $nombre . '%']);

                $nombres2 = explode(" ", $nombre);
                $cantidad = count($nombres2);

                if ($cantidad == '2' || $cantidad == '3') {
                    $ordenes = $ordenes->where(function ($jq1) use ($nombre) {
                        $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.nombre1," ",p.nombre2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.nombre2," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.apellido2," ",p.nombre2) LIKE ?', ['%' . $nombre . '%']);
                    });
                } else {

                    $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%']);
                }
            }

            if (!is_null($cedula)) {
                $ordenes = $ordenes->where('examen_orden.id_paciente', 'like', '%' . $cedula . '%');
            }

            if (!is_null($id_orden)) {
                $ordenes = $ordenes->where('labs_factura_agrupada_orden.id_examen_orden', $id_orden);
            }

            $ordenes = $ordenes->get();
        }
        return view('laboratorio/agrupada/edit_privada', ['ordenes' => $ordenes, 'id_cab' => $id_cab, 'detalle' => $detalle, 'cedula' => $cedula, 'nombre' => $nombre, 'id_orden' => $id_orden]);
    }


    public function editar_publicas_buscador($id_cab, Request $request)
    {
        //En desarrollo
        $cedula = $request['cedula'];
        $nombre = $request['nombre'];
        $id_orden = $request['id_orden'];

        $detalle = Labs_Factura_Agrupada_Detalle::where('id_agrup_cab', $id_cab)->where('estado', '1')->where('pub_priv', '0')->first();
        $ordenes = null;
        if (!is_null($detalle)) {

            $ordenes = Labs_Factura_Agrupada_Orden::where('labs_factura_agrupada_orden.id_agrup_det', $detalle->id)
                ->join('examen_orden', 'labs_factura_agrupada_orden.id_examen_orden', 'examen_orden.id')
                ->join('paciente as p', 'p.id', 'examen_orden.id_paciente')
                ->where('labs_factura_agrupada_orden.estado', '1');

            if (!is_null($cedula)) {
                $ordenes = $ordenes->where('examen_orden.id_paciente', 'like', '%' . $cedula . '%');
            }

            if (!is_null($nombre)) {
                //$ordenes = $ordenes->whereraw('CONCAT_WS (" ",p.apellido1,p.apellido2,p.nombre1,p.nombre2)  LIKE ?', ['%' . $nombre . '%']);
                $nombres2 = explode(" ", $nombre);
                $cantidad = count($nombres2);


                if ($cantidad == '2' || $cantidad == '3') {
                    $ordenes = $ordenes->where(function ($jq1) use ($nombre) {
                        $jq1->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.nombre1," ",p.nombre2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.nombre2," ",p.apellido2) LIKE ?', ['%' . $nombre . '%'])
                            ->orwhereraw('CONCAT(p.apellido2," ",p.nombre2) LIKE ?', ['%' . $nombre . '%']);
                    });
                } else {

                    $ordenes = $ordenes->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%' . $nombre . '%']);
                }
            }

            if (!is_null($id_orden)) {
                $ordenes = $ordenes->where('labs_factura_agrupada_orden.id_examen_orden', $id_orden);
            }

            $ordenes = $ordenes->get();
        }
        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);

        $contador = Examen_Orden::where('examen_orden.estado', '1')
            ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->leftJoin('labs_factura_agrupada_orden as f_ord', 'examen_orden.id', '=', 'f_ord.id_examen_orden')
            ->where('s.tipo', '0')
            ->whereNull('f_ord.id')
            ->select('examen_orden.*')->get();

        $afectadas = count($contador);
        //  dd(count($afectadas));

        return view('laboratorio/agrupada/edit_publica', ['ordenes' => $ordenes, 'id_cab' => $id_cab, 'detalle' => $detalle, 'afectadas' => $afectadas, 'cedula' => $cedula, 'nombre' => $nombre, 'id_orden' => $id_orden]);
    }



    /*****************Fin del buscador*********************/


    public function index_privadas_ajax($id_cab)
    {
        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);
        $ordenes  = Examen_Orden::where('examen_orden.estado', '1')
            ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
            ->whereNull('comprobante')
            ->whereNull('fecha_envio')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where('s.tipo', '!=', '0')
            ->leftJoin('labs_factura_agrupada_orden as f_ord', 'examen_orden.id', '=', 'f_ord.id_examen_orden')
            ->whereNull('f_ord.id')->where('pago_online', '0')->select('examen_orden.*', 'f_ord.id_examen_orden')->get();

        return view('laboratorio/agrupada/index_privada_ajax', ['ordenes' => $ordenes, 'id_cab' => $id_cab]);
    }

    public function guardar_det(Request $request, $id_orden)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $orden = Examen_Orden::find($id_orden);

        $id_cab = $request['id_cab'];
        //dd($id_cab);
        $detalle = Labs_Factura_Agrupada_Detalle::where('id_agrup_cab', $id_cab)->where('pub_priv', '1')->where('estado', '1')->first();

        if (is_null($detalle)) {
            $det_id = Labs_Factura_Agrupada_Detalle::insertGetId([
                'id_agrup_cab'    => $id_cab,
                'pub_priv'        => '1',
                'descripcion'     => 'EXAMENES DE LABORATORIO SEGUROS PRIVADOS Y COLABORADORES',
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            $arr_orden = [
                'id_agrup_det'    => $det_id,
                'id_examen_orden' => $orden->id,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            Labs_Factura_Agrupada_Orden::create($arr_orden);
        } else {
            $arr_det = [
                'id_usuariomod' => $idusuario,

            ];

            $detalle->update($arr_det);

            $arr_orden = [
                'id_agrup_det'    => $detalle->id,
                'id_examen_orden' => $orden->id,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            Labs_Factura_Agrupada_Orden::create($arr_orden);
        }

        return ['estado' => 'ok', 'id_cab' => $id_cab];
    }
    /******************************Agregar todo************************************/

    public function guardar_det_todo(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;


        $id_cab = $request['id_cab'];

        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);
        $detalle = Labs_Factura_Agrupada_Detalle::where('id_agrup_cab', $id_cab)->where('pub_priv', '1')->where('estado', '1')->first();
        $variable = "";
        $mensaje = "incorrecto";
        $ordenes  = Examen_Orden::where('examen_orden.estado', '1')
            ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
            ->whereNull('comprobante')
            ->whereNull('fecha_envio')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where('s.tipo', '!=', '0')
            ->leftJoin('labs_factura_agrupada_orden as f_ord', 'examen_orden.id', '=', 'f_ord.id_examen_orden')
            ->whereNull('f_ord.id')->where('pago_online', '0')
            ->select('examen_orden.*', 'f_ord.id_examen_orden')->get();


        if (is_null($detalle)) {
            $det_id = Labs_Factura_Agrupada_Detalle::insertGetId([
                'id_agrup_cab'    => $id_cab,
                'pub_priv'        => '1',
                'descripcion'     => 'EXAMENES DE LABORATORIO SEGUROS PRIVADOS Y COLABORADORES',
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            foreach ($ordenes as $values) {
                $arr_orden = [
                    'id_agrup_det'    => $det_id,
                    'id_examen_orden' => $values->id,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $variable = Labs_Factura_Agrupada_Orden::create($arr_orden);
            }
        } else {
            $arr_det = [
                'id_usuariomod' => $idusuario,

            ];

            $detalle->update($arr_det);

            foreach ($ordenes as $values) {
                $arr_orden = [
                    'id_agrup_det'    => $detalle->id,
                    'id_examen_orden' => $values->id,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $variable =  Labs_Factura_Agrupada_Orden::create($arr_orden);
            }
        }
        if ($variable == "") {
            $mensaje = "incorrecto";
        } else if (count($variable) > 0) {
            $mensaje = "correcto";
        }
        return $mensaje;
    }

    /***************************Fin de agregar todo*********************************/

    public function editar_privadas($id_cab)
    {

        $detalle = Labs_Factura_Agrupada_Detalle::where('id_agrup_cab', $id_cab)->where('estado', '1')->where('pub_priv', '1')->first();
        $ordenes = null;
        if (!is_null($detalle)) {
            $ordenes = Labs_Factura_Agrupada_Orden::where('id_agrup_det', $detalle->id)->where('estado', '1')->get();
        }
        //dd($ordenes);
        return view('laboratorio/agrupada/edit_privada', ['ordenes' => $ordenes, 'id_cab' => $id_cab, 'detalle' => $detalle, 'cedula' => '', 'nombre' => '', 'id_orden' => '']);
    }

    public function eliminar_orden_privada($id_orden, $id_cab)
    {

        $idusuario = Auth::user()->id;

        $orden = Labs_Factura_Agrupada_Orden::where('id_examen_orden', $id_orden)->delete();

        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);
        $detalle  = $cabecera->detalles;
        $total    = 0;
        foreach ($detalle as $value) {
            $orden = Labs_Factura_Agrupada_Orden::where('id_agrup_det', $value->id)->where('estado', '1')->get();
            //dd($orden);
            $total += $value->total;

            $arr = [
                'total'         => $total,
                'id_usuariomod' => $idusuario,
            ];

            $value->update($arr);
        }

        return "ok";
    }

    public function carga_publicas($id_cab)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $cabecera   = Labs_Factura_Agrupada_Cab::find($id_cab);


        $publicas = Examen_Orden::where('estado', '1')
            ->whereBetween('examen_orden.fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
            // ->whereNull('total_nivel2')
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where('s.tipo', '0')
            ->whereNull('comprobante')
            ->whereNull('fecha_envio')
            ->select('examen_orden.*')->get();


        // foreach ($publicas as $value) {
        //     $detalles     = $value->detalles;
        //     $nivel        = 3;
        //     $total_nivel2 = 0;
        //     foreach ($detalles as $detalle) {
        //         $ex_nivel = Examen_Nivel::where('id_examen', $detalle->id_examen)->where('nivel', $nivel)->first();
        //         if (!is_null($ex_nivel)) {
        //             $valor_nivel2 = round($ex_nivel->valor1 * 0.95, 2);
        //             $total_nivel2 += $valor_nivel2;
        //         }
        //         $detalle->update(['valor_nivel2' => $valor_nivel2]);
        //     }
        //     $value->update(['total_nivel2' => $total_nivel2]);
        // }

        $detalle = Labs_Factura_Agrupada_Detalle::where('id_agrup_cab', $id_cab)->where('pub_priv', '0')->where('estado', '1')->first();

        if (is_null($detalle)) {
            $det_id = Labs_Factura_Agrupada_Detalle::insertGetId([
                'id_agrup_cab'    => $id_cab,
                'pub_priv'        => '0',
                'descripcion'     => 'EXAMENES DE LABORATORIO SEGUROS PUBLICOS', //agregar año y mes
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            foreach ($publicas as $value) {
                $arr_orden = [
                    'id_agrup_det'    => $det_id,
                    'id_examen_orden' => $value->id,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                Labs_Factura_Agrupada_Orden::create($arr_orden);
            }
        } else {
            $arr_det = [
                'id_usuariomod' => $idusuario,

            ];

            $detalle->update($arr_det);
            foreach ($publicas as $value) {
                $ordenes = Labs_Factura_Agrupada_Orden::where('id_examen_orden', $value->id)->first();

                //dd($ordenes);
                if (is_null($ordenes)) {
                    $arr_orden = [
                        'id_agrup_det'    => $detalle->id,
                        'id_examen_orden' => $value->id,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    Labs_Factura_Agrupada_Orden::create($arr_orden);
                }
            }
        }

        return 'ok';
    }

    /*************************Agregar publicas una por una**************************/
    public function carga_publicas_ind($id_cab, $id_orden)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $cabecera   = Labs_Factura_Agrupada_Cab::find($id_cab);
        $filasAfectadas = "";

        //$publicas = Examen_Orden::find($id_orden);

        $publicas = Examen_Orden::where('id', $id_orden)
            ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
            ->first();


        $detalles     = $publicas->detalles;
        $nivel        = 3;
        $total_nivel2 = 0;
        $ordenes_pu = array();
        foreach ($detalles as $detalle) {
            $ex_nivel = Examen_Nivel::where('id_examen', $detalle->id_examen)->where('nivel', $nivel)->first();
            array_push($ordenes_pu, $detalle->id_examen);
            if (!is_null($ex_nivel)) {
                $valor_nivel2 = round($ex_nivel->valor1 * 0.95, 2);
                $total_nivel2 += $valor_nivel2;
            }
            $detalle->update(['valor_nivel2' => $valor_nivel2]);
        }
        $filasAfectadas = $publicas->update(['total_nivel2' => $total_nivel2]);

        $detalle = Labs_Factura_Agrupada_Detalle::where('id_agrup_cab', $id_cab)->where('pub_priv', '0')->where('estado', '1')->first();


        if (is_null($detalle)) {
            $det_id = Labs_Factura_Agrupada_Detalle::insertGetId([
                'id_agrup_cab'    => $id_cab,
                'pub_priv'        => '0',
                'descripcion'     => 'EXAMENES DE LABORATORIO SEGUROS PUBLICOS', //agregar año y mes
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);
            $arr_orden = [
                'id_agrup_det'    => $det_id,
                'id_examen_orden' => $publicas->id,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];
            $filasAfectadas = Labs_Factura_Agrupada_Orden::create($arr_orden);
        } else {
            $arr_det = [
                'id_usuariomod' => $idusuario,
            ];
            $detalle->update($arr_det);
            $ordenes = Labs_Factura_Agrupada_Orden::where('id_examen_orden', $publicas->id)->first();

            //dd($ordenes);
            if (is_null($ordenes)) {
                $arr_orden = [
                    'id_agrup_det'    => $detalle->id,
                    'id_examen_orden' => $publicas->id,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ];
                $filasAfectadas = Labs_Factura_Agrupada_Orden::create($arr_orden);
            }
        }

        return count($filasAfectadas);
    }



    public function editar_publicas($id_cab)
    {

        $detalle = Labs_Factura_Agrupada_Detalle::where('id_agrup_cab', $id_cab)->where('estado', '1')->where('pub_priv', '0')->first();
        $ordenes = null;
        if (!is_null($detalle)) {
            $ordenes = Labs_Factura_Agrupada_Orden::where('id_agrup_det', $detalle->id)->where('estado', '1')->get();
        }

        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);

        $contador = Examen_Orden::where('examen_orden.estado', '1')
            ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->leftJoin('labs_factura_agrupada_orden as f_ord', 'examen_orden.id', '=', 'f_ord.id_examen_orden')
            ->where('s.tipo', '0')
            ->whereNull('f_ord.id')
            ->select('examen_orden.*')->get();

        $afectadas = count($contador);


        return view('laboratorio/agrupada/edit_publica', ['ordenes' => $ordenes, 'id_cab' => $id_cab, 'detalle' => $detalle, 'afectadas' => $afectadas, 'cedula' => '', 'nombre' => '', 'id_orden' => '']);
    }

    public function recalcular_agrupada($id_cab)
    {
        $idusuario  = Auth::user()->id;

        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);
        $detalles = $cabecera->detalles;
        //dd($cabecera);
        $cantidad_priv = 0;
        $total_priv    = 0;
        $cantidad_pub  = 0;
        $total_pub     = 0;

        foreach ($detalles as $detalle) {
            $orden = $detalle->ordenes;
            //dd($orden);
            $total_iess  = 0;
            $cont_iess = 0;
            foreach ($orden as $ord) {
                $examen_orden = Examen_Orden::find($ord->id_examen_orden);
                //dd($examen_orden);
                //dd($ord);
                if ($examen_orden->estado != 1) {
                    $ord->delete();
                } elseif ($examen_orden->fecha_envio != null) {
                    $ord->delete();
                } else {
                    if ($detalle->pub_priv == 1) {
                        $cantidad_priv++;
                        $total_priv = $total_priv + $examen_orden->total_valor;

                        $arr_ord_pv = [
                            'total'           => $examen_orden->total_valor,
                            'id_usuariomod'   => $idusuario,

                        ];
                        $ord->update($arr_ord_pv);
                    }

                    if ($detalle->pub_priv == 0) {
                        $cantidad_pub++;
                        $total_pub = $total_pub + $examen_orden->total_valor;
                        if ($examen_orden->id_seguro == 2) {
                            $cont_iess++;
                            $total_iess = $total_iess + $examen_orden->total_valor;
                        }
                        $arr_ord_pub = [
                            'total'           => $examen_orden->total_valor,
                            'id_usuariomod'   => $idusuario,
                        ];
                        $ord->update($arr_ord_pub);
                    }
                }
            }

            //dd($cont_iess);
            if ($detalle->pub_priv == 1) {

                $arr_det_pv = [
                    'total'           => $total_priv,
                    'id_usuariomod'   => $idusuario,
                ];

                $detalle->update($arr_det_pv);
            }

            $valor_descuento = 0;

            if ($detalle->pub_priv == 0) {
                //dd($total_iess);

                //$valor_descuento = $total_iess * 0.07;
                $valor_descuento = round($valor_descuento, 2);

                $arr_det_pub = [
                    'valor_descuento' => $valor_descuento,
                    'valor_iess'      => $total_iess,
                    'total'           => $total_pub,
                    'id_usuariomod'   => $idusuario,
                ];
                $detalle->update($arr_det_pub);
            }
        }

        $arr_cab = [
            'valor_descuento' => $valor_descuento,
            'total'           => $total_pub + $total_priv,
            'id_usuariomod'   => $idusuario,
        ];

        $cabecera->update($arr_cab);
        //dd($cabecera);

        //dd($cantidad_priv, $total_priv, $cantidad_pub, $total_pub);
        return view('laboratorio/agrupada/modal_recalcular_valores', ['cantidad_priv' => $cantidad_priv, 'cantidad_pub' => $cantidad_pub, 'total_priv' => $total_priv, 'total_pub' => $total_pub, 'detalle' => $detalle, 'id_cab' => $id_cab, 'cabecera' => $cabecera]);
    }

    public function guardar_agrup_sri($id_cab)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $orden = Labs_Factura_Agrupada_Cab::find($id_cab);
        $data['empresa']      = '0993075000001'; // humanlabs
        //$data['empresa']      = '1307189140001';   //robles
        //$data['empresa']      = '32222222222'; //pruebas
        $cliente['cedula']    = $orden->cedula_factura;
        $cliente['tipo']      = $orden->tipo_documento; //eduardo dice q el lo calcula
        $cliente['nombre']    = $orden->nombre_factura;
        $cliente['apellido']  = '';
        $explode              = explode(" ", $orden->nombre_factura);
        if (count($explode) >= 4) {
            $cliente['nombre']    = $explode[0] . ' ' . $explode[1];
            for ($i = 2; $i < count($explode); $i++) {
                $cliente['apellido']  = $cliente['apellido'] . ' ' . $explode[$i];
            }
        }
        if (count($explode) == 3) {
            $cliente['nombre']    = $explode[0];
            $cliente['apellido']  = $explode[1] . ' ' . $explode[2];
        }
        if (count($explode) == 2) {
            $cliente['nombre']    = $explode[0];
            $cliente['apellido']  = $explode[1];
        }
        //dd($cliente);

        $cliente['email']     = $orden->email_factura;
        $cliente['telefono']  = $orden->telefono_factura;
        $direccion['calle']   = $orden->direccion_factura;
        $direccion['ciudad']  = $orden->ciudad_factura;
        $cliente['direccion'] = $direccion;
        $data['cliente']      = $cliente;

        $msn_error = '';
        $flag_error = false;
        if ($cliente['cedula'] == null) {
            $flag_error = true;
            $msn_error = 'Error en cedula';
        }

        if ($cliente['nombre'] == null) {
            $flag_error = true;
            $msn_error = 'Error en Nombre';
        }
        if ($cliente['email'] == null) {
            $flag_error = true;
            $msn_error = 'Error en email';
        }
        if ($cliente['telefono'] == null) {
            $flag_error = true;
            $msn_error = 'Error en telefono';
        }
        if ($direccion['calle'] == null) {
            $flag_error = true;
            $msn_error = 'Error en calle';
        }
        if ($direccion['ciudad'] == null) {
            $flag_error = true;
            $msn_error = 'Error en Ciudad';
        }

        $cant = 0;
        $mes = '';
        $total_descuento = 0;
        foreach ($orden->detalles as $value) {
            //se envian los productos
            $valor = $value->valor * $value->cantidad;
            $subtotal = $valor - $value->valor_descuento;
            if ($value->pub_priv == 1) {
                $producto['sku']       = "LABS-1217"; //ID EXAMEN
            } else {
                $producto['sku']       = "LABS-1216"; //ID EXAMEN
            }
            if ($orden->mes == 1) {
                $mes = 'ENERO';
            } elseif ($orden->mes == 2) {
                $mes = 'FEBRERO';
            } elseif ($orden->mes == 3) {
                $mes = 'MARZO';
            } elseif ($orden->mes == 4) {
                $mes = 'ABRIL';
            } elseif ($orden->mes == 5) {
                $mes = 'MAYO';
            } elseif ($orden->mes == 6) {
                $mes = 'JUNIO';
            } elseif ($orden->mes == 7) {
                $mes = 'JULIO';
            } elseif ($orden->mes == 8) {
                $mes = 'AGOSTO';
            } elseif ($orden->mes == 9) {
                $mes = 'SEPTIEMBRE';
            } elseif ($orden->mes == 10) {
                $mes = 'OCTUBRE';
            } elseif ($orden->mes == 11) {
                $mes = 'NOVIEMBRE';
            } elseif ($orden->mes == 12) {
                $mes = 'DICIEMBRE';
            }
            //dd($mes);
            $descuento = 0;

            if ($value->pub_priv == 0) {
                $descuento =  $value->valor_descuento;
            }
            $total_descuento = $total_descuento + $descuento;
            //$producto['nombre']    = $value->descripcion . ' DEL MES DE ' . $mes . '/' . $orden->anio; // NOMBRE DEL EXAMEN
            $producto['nombre']    = $value->descripcion;
            $producto['cantidad']  = '1';
            $producto['precio']    = $value->total; //DETALLE
            $producto['descuento'] = $descuento;
            $producto['subtotal']  = $value->total - $descuento; //precio-descuento
            $producto['tax']       = "0";
            $producto['total']     = $value->total - $descuento; //SUBTOTAL
            $producto['copago']    = "0";
            $productos[$cant] = $producto;
            $cant++;
        }
        $data['productos'] = $productos;
        /*01  SIN UTILIZACION DEL SISTEMA FINANCIERO
        15  COMPENSACIÓN DE DEUDAS
        16  TARJETA DE DÉBITO
        17  DINERO ELECTRÓNICO
        18  TARJETA PREPAGO
        19  TARJETA DE CRÉDITO
        20  OTROS CON UTILIZACION DEL SISTEMA FINANCIERO
        21  ENDOSO DE TÍTULOS
        */
        $info_adicional['nombre']      = "AGENTES_RETENCION";
        $info_adicional['valor']       = "Resolucion 1";
        $info[0]                       = $info_adicional;

        $info_adicional['nombre']      = "PACIENTE";
        $info_adicional['valor']       = ""; //eduardo dice q null
        $info[1]                       = $info_adicional;

        $info_adicional['nombre']      = "MAIL";
        $info_adicional['valor']       = $orden->email_factura; //EMAIL
        $info[2]                       = $info_adicional;

        $info_adicional['nombre']      = "CIUDAD";
        $info_adicional['valor']       = $orden->ciudad_factura; //EMAIL
        $info[3]                       = $info_adicional;

        $info_adicional['nombre']      = "DIRECCION";
        $info_adicional['valor']       = $orden->direccion_factura; //EMAIL
        $info[4]                       = $info_adicional;

        $info_adicional['nombre']      = "ORDEN";
        $info_adicional['valor']       = '' . $orden->id . ''; //EMAIL
        $info[5]                       = $info_adicional;

        $info_adicional['nombre']      = "SEGURO";
        $info_adicional['valor']       = 'PARTICULAR'; //SEGURO ???
        $info[6]                       = $info_adicional;

        $pago['forma_pago']     = '20';
        $info_adicional['nombre']      = "FORMA_PAGO";
        $valor_con_descuento = $orden->total - $total_descuento;
        $texto = 'PENDIENTE PAGO :' . $valor_con_descuento;
        $info_adicional['valor'] = $texto;
        $info[7]  = $info_adicional;

        $tipos_pago['id_tipo']            = 7; //metodo de pago efectivo, tarjeta, etc
        $tipos_pago['fecha']              = date('Y-m-d H:i:s');
        $tipos_pago['tipo_tarjeta']       = ''; //si es efectivo no se envia
        $tipos_pago['numero_transaccion'] = ''; //si es efectivo no se envia
        $tipos_pago['id_banco']           = ''; //si es efectivo no se envia
        $tipos_pago['cuenta']             = ''; //si es efectivo no se envia
        $tipos_pago['giradoa']            = null; //si es efectivo no se envia
        $tipos_pago['valor']              = $valor_con_descuento; //valor a pagar de total
        $tipos_pago['valor_base']         = $valor_con_descuento; //valor a pagar de base
        $tipos_pago['info_banco'] = '';
        $pagos[1] = $tipos_pago;
        $pago['informacion_adicional'] = $info;
        $pago['dias_plazo']            = '10';
        $data['pago']                  = $pago;
        $data['contable']              = 1; //si se marca 1 crea factura, 0 no crea factura
        $data['laboratorio']           = 1;
        $data['paciente']              = '';
        $data['concepto']              = 'Factura Electronica - ' . $orden->nombre_factura;
        $data['copago']                = 0;
        $data['id_seguro']             = '1'; //si se marca 1 se crea un solo producto por id de laboratorioo, 0 graba detalle de datos
        $data['total_factura']         = $valor_con_descuento;
        $data['formas_pago']           = $pagos;
        $data['seguro'] = '';
        $data['idUsuario'] = $idusuario;
        $establecimiento = Ct_Sucursales::find($orden->sucursal);
        $puntoEmision = Ct_Caja::find($orden->punto_emision);
        if ($puntoEmision->codigo_caja != '002') {
            $data['sucursal'] = $establecimiento->id;
            $data['punto_emision'] = $puntoEmision->id;
            $data['emisor'] = 'MD';
        }
        $data['establecimiento'] = $establecimiento->codigo_sucursal;
        $data['ptoEmision'] = $puntoEmision->codigo_caja;
        $data['externo']      = 1;
        if ($orden->fecha_envio != null) {
            $flag_error = true;
            $msn_error = 'Ya enviado al SRI';
        }
        if ($flag_error) {
            Log_usuario::create([
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "LABORATORIO",
                'dato_ant1'   => $orden->id,
                'dato1'       => $orden->id_paciente,
                'dato_ant2'   => "ERROR AL ENVIAR AL SRI AGRUPADA CONTABILIDAD",
                'dato_ant4'   => $msn_error,
            ]);
            return $msn_error;
        }

        $orden->update([
            'fecha_envio' => date('Y-m-d H:i:s'),
        ]);

        /*foreach ($agrup2 as $value) {
            $o_ori = Examen_Orden::find($value);
            $o_ori->update([
                'fecha_envio' => date('Y-m-d H:i:s'),
            ]);
        }*/

        //dd($data);

        /* ACTIVAR PARA MANDAR A PRODU */
        $envio = ApiFacturacionController::envio_factura($data);
        $envio = json_decode($envio);
        //sin enviar al sri
        //$envio = ApiFacturacionController::crea_factura_noelec($data, '001-001-5000');
        //dd($envio->comprobante);
        $comprobacion = $orden->update([
            'comprobante' => $envio->comprobante,
            'fecha_envio' => date('Y-m-d H:i:s'),
        ]);

        $manage = $envio->status->status . '-' . $envio->status->message . '-' . $envio->status->reason;

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "LABORATORIO",
            'dato_ant1'   => $orden->id,
            'dato1'       => $orden->id_paciente,
            'dato_ant2'   => "ENVIAR AL SRI AGRUPADA CONTABILIDAD",
            'dato_ant4'   => $manage,
        ]);

        //todas las que estan en labs agrupada orden  comparar con examen orden update fecha envio y comprobante
        if ($comprobacion == true || count($comprobacion) == 1) {
            $this->pendientes_labs($orden->mes, $orden->anio);
        }

        return 'ok';
    }

    public function excel_detalle_orden($id_cab)
    {
        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);
        $detalles = $cabecera->detalles;

        Excel::create('DETALLE_ORDENES_AGRUPADA', function ($excel) use ($detalles, $cabecera) {
            $excel->sheet('DETALLE', function ($sheet) use ($detalles, $cabecera) {

                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('N°');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ID ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA ORDEN');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->cell('G3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $x = 1;
                $i = 4;
                $total = 0;
                foreach ($detalles as $detalle) {
                    $ordenes = $detalle->ordenes;

                    //dd($ordenes);

                    foreach ($ordenes as $ord) {
                        //dd($ord);
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                        });

                        $sheet->cell('B' . $i, function ($cell) use ($ord) {
                            // manipulate the cel
                            $cell->setValue($ord->id_examen_orden);
                        });

                        $sheet->cell('C' . $i, function ($cell) use ($ord) {
                            // manipulate the cel
                            $cell->setValue($ord->examen_orden->paciente->apellido1 . ' ' . $ord->examen_orden->paciente->apellido2 . ' ' . $ord->examen_orden->paciente->nombre1 . ' ' . $ord->examen_orden->paciente->nombre2);
                        });

                        $sheet->cell('D' . $i, function ($cell) use ($ord) {
                            // manipulate the cel
                            $cell->setValue($ord->examen_orden->id_paciente);
                        });

                        $sheet->cell('E' . $i, function ($cell) use ($ord) {
                            // manipulate the cel
                            $cell->setValue(substr($ord->examen_orden->fecha_orden, 0, 10));
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('F' . $i, function ($cell) use ($ord) {
                            // manipulate the cel
                            $cell->setValue($ord->examen_orden->seguro->nombre);
                        });

                        $sheet->cell('G' . $i, function ($cell) use ($ord) {
                            // manipulate the cel
                            $cell->setValue($ord->total);
                        });
                        $x++;
                        $i++;
                        $total = $total + $ord->total;
                    }
                    //dd($total);

                    $sheet->cell('F' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setFontWeight('bold');
                        $cell->setValue('Total');
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($total) {
                        // manipulate the cel
                        $cell->setValue($total);
                    });
                }
            });

            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
        })->export('xlsx');
    }

    public function resultados_pendientes_publicas($id_cab)
    {


        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);


        $ordenes = Examen_Orden::where('estado', '1')
            ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->where('s.tipo', '0')->select('examen_orden.*')->get();
        $i = 0;

        $fecha_d = date('Y/m/d');

        Excel::create('Examenes_detalle_publicas-' . $fecha_d, function ($excel) use ($ordenes) {

            $excel->sheet('Examenes_detalle', function ($sheet) use ($ordenes) {
                $fecha_d = date('Y/m/d');
                $i       = 5;

                $sheet->mergeCells('A3:G3');

                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {
                    $mes_letra = "ENERO";
                }
                if ($mes == 02) {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == 03) {
                    $mes_letra = "MARZO";
                }
                if ($mes == 04) {
                    $mes_letra = "ABRIL";
                }
                if ($mes == 05) {
                    $mes_letra = "MAYO";
                }
                if ($mes == 06) {
                    $mes_letra = "JUNIO";
                }
                if ($mes == 07) {
                    $mes_letra = "JULIO";
                }
                if ($mes == '08') {
                    $mes_letra = "AGOSTO";
                }
                if ($mes == '09') {
                    $mes_letra = "SEPTIEMBRE";
                }
                if ($mes == '10') {
                    $mes_letra = "OCTUBRE";
                }
                if ($mes == '11') {
                    $mes_letra = "NOVIEMBRE";
                }
                if ($mes == '12') {
                    $mes_letra = "DICIEMBRE";
                }
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);

                $sheet->cells('A1:G3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A3:G4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LISTADO DE EXAMENES PENDIENTES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NRO. ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOMA DE MUESTRA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });


                $cant = 1;
                $total = 0;
                foreach ($ordenes as $orden) {
                    $detalles   = $orden->detalles;
                    $resultados = $orden->resultados;

                    ///////////
                    $cant_par = 0;
                    foreach ($detalles as $d) {
                        //$parametros = $parametros->where('id_examen', $d->id_examen);
                        if ($d->id_examen == '639') {
                            $xpar = $resultados->where('id_examen', '639')->where('valor', '<>', '0');
                            if ($xpar->count() > 0) {
                                $cant_par = $cant_par + $xpar->count();
                            } else {
                                $cant_par = $cant_par + 10;
                            }
                            //$cant_par++;
                        } else {
                            if ($d->examen->no_resultado == '0') {

                                if (count($d->parametros) == '0') {
                                    $cant_par++;
                                }
                                if ($d->examen->sexo_n_s == '0') {
                                    $parametro_nuevo = $d->parametros->where('sexo', '3');
                                } else {
                                    $parametro_nuevo = $d->parametros->where('sexo', $orden->paciente->sexo);
                                }
                                foreach ($parametro_nuevo as $p) {
                                    $cant_par++;
                                }
                            }
                        }
                    }

                    $certificados = 0;
                    $cantidad     = 0;
                    foreach ($resultados as $r) {
                        $cantidad++;
                        if ($r->certificado == '1') {
                            $certificados++;
                        }
                    }
                    if ($certificados > $cant_par) {
                        $certificados = $cant_par;
                    }
                    if ($certificados != $cant_par) {
                        $sheet->cell('A' . $i, function ($cell) use ($orden) {
                            // manipulate the cel
                            $cell->setValue($orden->id);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $paciente = $orden->paciente;
                        $sheet->cell('B' . $i, function ($cell) use ($paciente) {
                            // manipulate the cel
                            //$cell->setValue(substr($value->created_at,0,10));
                            if ($paciente->apellido2 != "(N/A)") {
                                $vnombre = $paciente->apellido1 . ' ' . $paciente->apellido2;
                            } else {
                                $vnombre = $paciente->apellido1;
                            }

                            if ($paciente->nombre2 != "(N/A)") {
                                $vnombre = $vnombre . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2;
                            } else {
                                $vnombre = $vnombre . ' ' . $paciente->nombre1;
                            }
                            $cell->setValue($vnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($orden) {
                            // manipulate the cel
                            $cell->setValue($orden->id_paciente);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('D' . $i, function ($cell) use ($orden) {
                            $cell->setValue(substr($orden->fecha_orden, 0, 10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $protocolo = $orden->protocolo;
                        $pre_post = '';
                        if (!is_null($protocolo)) {
                            $pre_post = $protocolo->pre_post;
                        }
                        $sheet->cell('E' . $i, function ($cell) use ($pre_post) {
                            $cell->setValue($pre_post);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('F' . $i, function ($cell) use ($orden) {
                            $cell->setValue($orden->toma_muestra);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $i = $i + 1;
                    }
                    /*

                foreach ($detalles as $detalle) {
                $texto_sin_res = '';
                $parametros = Examen_Parametro::where('id_examen',$detalle->id_examen)->get();
                if(is_null($parametros)){
                $texto_sin_res = 'PENDIENTE DE INGRESAR PARAMETROS';
                }
                if($texto_sin_res != ''){

                $sheet->cell('A' . $i, function ($cell) use ($orden, $detalle) {
                // manipulate the cel
                $cell->setValue($orden->id);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $paciente = $orden->paciente;
                $sheet->cell('B' . $i, function ($cell) use ($paciente) {
                // manipulate the cel
                //$cell->setValue(substr($value->created_at,0,10));
                if ($paciente->apellido2 != "(N/A)") {
                $vnombre = $paciente->apellido1 . ' ' . $paciente->apellido2;
                } else {
                $vnombre = $paciente->apellido1;
                }

                if ($paciente->nombre2 != "(N/A)") {
                $vnombre = $vnombre . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2;
                } else {
                $vnombre = $vnombre . ' ' . $paciente->nombre1;
                }
                $cell->setValue($vnombre);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C' . $i, function ($cell) use ($orden) {
                // manipulate the cel
                $cell->setValue($orden->id_paciente);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) use ($orden) {
                $cell->setValue(substr($orden->fecha_orden, 0, 10));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) use ($orden, $detalle) {
                // manipulate the cel
                $cell->setValue($detalle->examen->descripcion);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($texto_sin_res) {
                // manipulate the cel
                $cell->setValue($texto_sin_res);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = $i + 1;

                }

                $cant  = $cant + 1;
                //$total = $total + $value->total_valor;

                }

                if($resultados->count()==0){
                $sheet->cell('A' . $i, function ($cell) use ($orden, $detalle) {
                // manipulate the cel
                $cell->setValue($orden->id);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $paciente = $orden->paciente;
                $sheet->cell('B' . $i, function ($cell) use ($paciente) {
                // manipulate the cel
                //$cell->setValue(substr($value->created_at,0,10));
                if ($paciente->apellido2 != "(N/A)") {
                $vnombre = $paciente->apellido1 . ' ' . $paciente->apellido2;
                } else {
                $vnombre = $paciente->apellido1;
                }

                if ($paciente->nombre2 != "(N/A)") {
                $vnombre = $vnombre . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2;
                } else {
                $vnombre = $vnombre . ' ' . $paciente->nombre1;
                }
                $cell->setValue($vnombre);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C' . $i, function ($cell) use ($orden) {
                // manipulate the cel
                $cell->setValue($orden->id_paciente);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) use ($orden) {
                $cell->setValue(substr($orden->fecha_orden, 0, 10));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) use ($orden, $detalle) {
                // manipulate the cel
                $cell->setValue('SIN RESULTADOS INGRESADOS');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($texto_sin_res) {
                // manipulate the cel
                $cell->setValue('');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = $i + 1;

                }
                foreach($resultados as $resultado){
                if($resultado->certificado=='0'){
                $examen = $resultado->parametro->examen;
                $parametro = $resultado->parametro;
                if($examen->sexo_n_s=='0'){
                $sheet->cell('A' . $i, function ($cell) use ($orden, $detalle) {
                // manipulate the cel
                $cell->setValue($orden->id);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $paciente = $orden->paciente;
                $sheet->cell('B' . $i, function ($cell) use ($paciente) {
                // manipulate the cel
                //$cell->setValue(substr($value->created_at,0,10));
                if ($paciente->apellido2 != "(N/A)") {
                $vnombre = $paciente->apellido1 . ' ' . $paciente->apellido2;
                } else {
                $vnombre = $paciente->apellido1;
                }

                if ($paciente->nombre2 != "(N/A)") {
                $vnombre = $vnombre . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2;
                } else {
                $vnombre = $vnombre . ' ' . $paciente->nombre1;
                }
                $cell->setValue($vnombre);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C' . $i, function ($cell) use ($orden) {
                // manipulate the cel
                $cell->setValue($orden->id_paciente);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) use ($orden) {
                $cell->setValue(substr($orden->fecha_orden, 0, 10));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $parametro = Examen_Parametro::find($resultado->id_parametro);
                $examen    = Examen::find($parametro->id_examen);
                $sheet->cell('E' . $i, function ($cell) use ($examen) {
                // manipulate the cel
                $cell->setValue($examen->nombre);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($parametro) {
                // manipulate the cel
                $cell->setValue($parametro->nombre);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = $i + 1;
                }else{
                $paciente = $resultado->orden->paciente;
                if($parametro->sexo == $paciente->sexo){
                $sheet->cell('A' . $i, function ($cell) use ($orden, $detalle) {
                // manipulate the cel
                $cell->setValue($orden->id);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $paciente = $orden->paciente;
                $sheet->cell('B' . $i, function ($cell) use ($paciente) {
                // manipulate the cel
                //$cell->setValue(substr($value->created_at,0,10));
                if ($paciente->apellido2 != "(N/A)") {
                $vnombre = $paciente->apellido1 . ' ' . $paciente->apellido2;
                } else {
                $vnombre = $paciente->apellido1;
                }

                if ($paciente->nombre2 != "(N/A)") {
                $vnombre = $vnombre . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2;
                } else {
                $vnombre = $vnombre . ' ' . $paciente->nombre1;
                }
                $cell->setValue($vnombre);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C' . $i, function ($cell) use ($orden) {
                // manipulate the cel
                $cell->setValue($orden->id_paciente);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D' . $i, function ($cell) use ($orden) {
                $cell->setValue(substr($orden->fecha_orden, 0, 10));
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $parametro = Examen_Parametro::find($resultado->id_parametro);
                $examen    = Examen::find($parametro->id_examen);
                $sheet->cell('E' . $i, function ($cell) use ($examen) {
                // manipulate the cel
                $cell->setValue($examen->nombre);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F' . $i, function ($cell) use ($parametro) {
                // manipulate the cel
                $cell->setValue($parametro->nombre);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = $i + 1;

                }
                }
                }

                }*/
                }
            });
        })->export('xlsx');
    }
    public function mostrar_pendientes_publicas($id_cab)
    {
        $cabecera = Labs_Factura_Agrupada_Cab::find($id_cab);

        $ordenes = Examen_Orden::where('examen_orden.estado', '1')
            ->whereBetween('fecha_orden', [$cabecera->fecha_desde . ' 00:00:00', $cabecera->fecha_hasta . ' 23:59:59'])
            ->join('seguros as s', 's.id', 'examen_orden.id_seguro')
            ->leftJoin('labs_factura_agrupada_orden as f_ord', 'examen_orden.id', '=', 'f_ord.id_examen_orden')
            ->where('s.tipo', '0')
            ->whereNull('f_ord.id')
            ->select('examen_orden.*')->get();

        return view('laboratorio/agrupada/pendientes', ['ordenes' => $ordenes, 'id_cab' => $id_cab]);
    }

    public function pendientes_labs($mes, $anio)
    {

        $agrupada_orden = DB::table('labs_factura_agrupada_orden as ao')
            ->join('labs_factura_agrupada_detalle as ad', 'ao.id_agrup_det', 'ad.id')
            ->join('labs_factura_agrupada_cab as ac', 'ad.id_agrup_cab', 'ac.id')
            ->join('examen_orden as eo', 'ao.id_examen_orden', 'eo.id')
            ->where('ac.anio', $anio)
            ->where('ac.mes', $mes)
            ->where('ao.estado', '1')
            ->whereNull('eo.comprobante')
            ->whereNull('eo.fecha_envio')
            ->select('ao.id_examen_orden', 'ac.comprobante', 'ac.fecha_envio')
            ->get();

        DB::beginTransaction();

        try {
            $contador = 0;
            $updates = 0;
            $no = 0;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            foreach ($agrupada_orden as $orden) {
                $id_examen = 0;
                $id_examen = $orden->id_examen_orden;


                $envios = [
                    'fecha_envio' => $orden->fecha_envio,
                    'comprobante' =>  $orden->comprobante,
                    'apellido_factura' => 'asd'
                ];
                // dd($orden->comprobante);
                if (!is_null($orden->comprobante)) {
                    $examen_orden = Examen_Orden::where('id', $id_examen)->first();
                    $envios_details = $examen_orden->update($envios);
                    $updates++;
                } else {

                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "Factura agrupada orden",
                        'dato_ant1'   => $orden->id_examen_orden,
                        'dato_ant2'   => "ERROR EN LABS FACTURAS MASIVO " . $mes . "/" . $anio
                    ]);
                    $no++;
                }
            }
            DB::commit();
            // dd('editados: ' . $updates . "- No editados: " . $no . "- Total: " . count($agrupada_orden));
        } catch (\Exception $e) {

            DB::rollBack();
            return $e->getMessage();
        }
    }


    public function agregarPendientes($mes)
    {

        $anio = 2021;

        $agrupada_orden = DB::table('labs_factura_agrupada_orden as ao')
            ->join('labs_factura_agrupada_detalle as ad', 'ao.id_agrup_det', 'ad.id')
            ->join('labs_factura_agrupada_cab as ac', 'ad.id_agrup_cab', 'ac.id')
            ->join('examen_orden as eo', 'ao.id_examen_orden', 'eo.id')
            ->where('ac.anio', $anio)
            ->where('ac.mes', $mes)
            ->where('ao.estado', '1')
            ->whereNull('eo.comprobante')
            ->whereNull('eo.fecha_envio')
            ->select('ao.id_examen_orden', 'ac.comprobante', 'ac.fecha_envio')
            ->get();

        DB::beginTransaction();

        try {
            $contador = 0;
            $updates = 0;
            $no = 0;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            foreach ($agrupada_orden as $orden) {
                $id_examen = 0;
                $id_examen = $orden->id_examen_orden;


                $envios = [
                    'fecha_envio' => $orden->fecha_envio,
                    'comprobante' =>  $orden->comprobante,
                    'apellido_factura' => 'asd'
                ];
                // dd($orden->comprobante);
                if (!is_null($orden->comprobante)) {
                    $examen_orden = Examen_Orden::where('id', $id_examen)->first();
                    $envios_details = $examen_orden->update($envios);
                    $updates++;
                } else {

                    Log_usuario::create([
                        'id_usuario'  => $idusuario,
                        'ip_usuario'  => $ip_cliente,
                        'descripcion' => "Factura agrupada orden",
                        'dato_ant1'   => $orden->id_examen_orden,
                        'dato_ant2'   => "ERROR EN LABS FACTURAS MASIVO " . $mes . "/" . $anio
                    ]);
                    $no++;
                }
            }
            DB::commit();
            dd('editados: ' . $updates . "- No editados: " . $no . "- Total: " . count($agrupada_orden));
        } catch (\Exception $e) {

            DB::rollBack();
            return $e->getMessage();
        }
    }


    public function buscador_agrupada(Request $request)
    {
        $anio = $request['anio'];
        $mes  = $request['mes'];
        $factura_cab =  Labs_Factura_Agrupada_Cab::where('estado', '1');
        if (!is_null($anio)) {
            $factura_cab = $factura_cab->where('anio', $anio);
        }
        if (!is_null($mes)) {
            $factura_cab = $factura_cab->where('mes', $mes);
        }
        if (!is_null($anio) and !is_null($mes)) {
            $factura_cab = $factura_cab->where('anio', $anio)->where('mes', $mes);
        }
        $factura_cab = $factura_cab->get();
        return view('laboratorio/agrupada/index', ['factura_cab' => $factura_cab, 'anio' => $anio, 'mes' => $mes]);
    }

    public function editar_detalle(Request $request, $id_det)
    {

        //dd($request->all(), $id_det);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $detalle_agrupada = Labs_Factura_Agrupada_Detalle::find($id_det);
        $arr_med = [
            'descripcion'       => $request['det' . $id_det],
            'id_usuariomod'     => $idusuario,
            'ip_modificacion'   => $ip_cliente,
        ];

        $detalle_agrupada->update($arr_med);

        return "ok";
    }
}
