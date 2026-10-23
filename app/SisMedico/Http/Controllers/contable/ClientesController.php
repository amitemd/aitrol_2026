<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;
use Sis_medico\Ct_Clientes;
use Sis_medico\Pais;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Empresa;
use Sis_medico\Ct_ventas;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Rubros_Cliente;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;

class ClientesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth     = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }
    public function saldos_iniciales_cliente(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        return view('contable/saldos_iniciales_clientes/index', ['empresa' => $empresa]);
    }
    public function opciones_cliente(Request $r)
    {
        if ($r->opcion == '') {
            return view('contable/clientes/view_opciones');
        } elseif ($r->opcion == 'importarXls') {
            ini_set('max_execution_time', '300');
            $datosImportar = [];
            try {
                $path = $r->file($r->tipo_archivo)->getRealPath();
                $datos = Excel::load($path)->get();
                if (!empty($datos)) {
                    if ($r->tipo == 'cliente') {
                        $i = $r->desde;
                        for ($i; $i < $r->hasta; $i++) {
                            $tipo = '';
                            $cliente = Ct_Clientes::where('identificacion', $datos[$i]->codigo)->first();
                            if (is_numeric($datos[$i]->codigo)) {
                                if (strlen($datos[$i]->codigo) == 10) {
                                    $tipo = '5';
                                } else {
                                    $tipo = '4';
                                }
                            }
                            if ($cliente == '') {
                                if ($datos[$i]->codigo != '') {
                                    $datoExcel = [
                                        'identificacion' => $datos[$i]->codigo,
                                        'nombre' => $datos[$i]->nombre,
                                        'nombre_representante' => $datos[$i]->nombre,
                                        'cedula_representante' => $datos[$i]->ruc,
                                        'tipo' => $tipo,
                                        'telefono1_representante' => $datos[$i]->telefono1,
                                        'telefono2_representante' => $datos[$i]->telefono2,
                                        'direccion_representante' => $datos[$i]->direccion,
                                        'id_usuariocrea' => '0921605895',
                                        'id_usuariomod' => '0921605895',
                                        'ip_creacion' => '::1',
                                        'ip_modificacion' => '::1',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ];
                                    array_push($datosImportar, $datoExcel);
                                    Ct_Clientes::insert($datoExcel);
                                }
                            }
                        }
                    } elseif ($r->tipo == 'proveedor') {
                        $j = $r->desde;
                        for ($j; $j < $r->hasta; $j++) {
                            $tipo = '';
                            $proveedor = Proveedor::where('id', $datos[$j]->codigo)->first();

                            if (is_numeric($datos[$j]->codigo)) {
                                if (strlen($datos[$j]->codigo) == 10) {
                                    $tipo = '5';
                                } else {
                                    $tipo = '4';
                                }
                            }
                            if ($proveedor == '') {
                                if ($datos[$j]->codigo != '') {
                                    $datoExcel = [
                                        'id' => $datos[$j]->codigo,
                                        'razonsocial' => $datos[$j]->nombre,
                                        'nombrecomercial' => $datos[$j]->nombre,
                                        'tipo' => $tipo,
                                        'telefono1' => $datos[$j]->telefono1,
                                        'telefono2' => $datos[$j]->telefono2,
                                        'direccion' => $datos[$j]->direccion,
                                        'id_usuariocrea' => '0921605895',
                                        'id_usuariomod' => '0921605895',
                                        'ip_creacion' => '::1',
                                        'ip_modificacion' => '::1',
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ];
                                    array_push($datosImportar, $datoExcel);
                                    Proveedor::insert($datoExcel);
                                }
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                $datosImportar = [
                    'msj' => 'Un producto no tiene codigo asigando en su archivo',
                    'error' => $e->getMessage(),
                    'linea' => $e->getLine(),
                ];
            }
            return json_encode($datosImportar);
        }
    }
    public function saldos_iniciales_cliente2(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $clientes = Ct_Clientes::where('estado', '!=', null)->orderby('identificacion', 'desc')->get();
        $ventas = Ct_ventas::where('estado', 2)->where('id_empresa', $id_empresa)->paginate(10);
        return view('contable/saldos_iniciales_clientes/index1', ['empresa' => $empresa, 'clientes' => $clientes, 'ventas' => $ventas]);
    }

    public function search_cliente(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $constraints = [
            'id'         => $request['id'],
            'id_cliente' => $request['id_cliente'],
            'concepto' => $request['concepto'],
            'numero' => $request['num_factura'],
            'fecha' => $request['fecha'],

        ];
        $clientes = Ct_Clientes::where('estado', '!=', null)->orderby('identificacion', 'desc')->get();
        $ventas = $this->doSearchingQuery2($constraints, $id_empresa);
        return view('contable/saldos_iniciales_clientes/index1', ['ventas' => $ventas, 'searchingVals' => $constraints, 'empresa' => $empresa, 'clientes' => $clientes]);
    }
    private function doSearchingQuery2($constraints, $id_empresa)
    {

        $query          = Ct_ventas::query();
        $fields         = array_keys($constraints);

        $index          = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->where('estado', '2')->where('id_empresa', $id_empresa)->paginate(5);
    }


    /************************************************
     **************BUSCA RUBRO CLIENTE****************
    /************************************************/
    public function autocomplete_rub_cliente(Request $request)
    {

        $codigo = $request['term'];
        $data      = array();
        $rubros = DB::table('ct_rubros_cliente')->where('nombre', 'like', '%' . $codigo . '%')->get();
        foreach ($rubros as $prov) {
            $data[] = array('value' => $prov->nombre, 'codigo' => $prov->codigo);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados'];
        }
    }

    /************************************************
     ***********SALDO INICIAL CLIENTES STORE***********
    /************************************************/
    public function guardar_saldo_inicial_cliente(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');

        $contador_ctv = DB::table('ct_asientos_cabecera')->get()->count();

        $numero_factura = 0;

        if ($contador_ctv == 0) {

            //return 'No Retorno nada';
            $num = '1';
            $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
        } else {

            //Obtener Ultimo Registro de la Tabla ct_compras
            $max_id = DB::table('ct_asientos_cabecera')->max('id');

            if (($max_id >= 1) && ($max_id < 10)) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }

            if (($max_id >= 10) && ($max_id < 100)) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }

            if (($max_id >= 100) && ($max_id < 1000)) {
                $nu = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }

            if ($max_id == 1000) {
                $numero_factura = $max_id;
            }
        }
        $numeroconcadenado = '001-002-' . $numero_factura;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $valr = 0;
        $valor2 = 0;
        $idusuario  = Auth::user()->id;
        $cabeceraa = [
            'observacion'                   => 'SALDOS INICIALES CLIENTES: ' . $request['concepto'] . ' A: ' . $request['id_cliente'],
            'fecha_asiento'                 => $request['fecha_hoy'],
            'fact_numero'                   => $numero_factura,
            'valor'                         => $request['total'],
            'id_empresa'                    => $id_empresa,
            'estado'                        => '1',
            'ip_creacion'                   => $ip_cliente,
            'ip_modificacion'               => $ip_cliente,
            'id_usuariocrea'                => $idusuario,
            'id_usuariomod'                 => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($cabeceraa);
        if (!is_null($request['contador'])) {
            $primerarray = array();
            for ($i = 0; $i < $request['contador']; $i++) {
                if ($request['visibilidad' . $i] == 1) {
                    $valr += $request['valor' . $i];
                    $consulta_rubro = Ct_Rubros_Cliente::where('nombre', $request['rubro' . $i])->first();
                    if ($consulta_rubro != '[]' || $consulta_rubro != null) {
                        $segundoarray = [$consulta_rubro->haber, $request['valor' . $i]];
                        $key = array_search($consulta_rubro->haber, array_column($primerarray, '0'));

                        if ($key !== false) {
                            $valor2 =  $primerarray[$key][1];
                            $valor2 = $valor2 + $request['valor' . $i];
                            $primerarray[$key][0] = $consulta_rubro->haber;
                            $primerarray[$key][1] = $valor2;
                        } else {
                            array_push($primerarray, $segundoarray);
                        }
                    }
                }
            }

            for ($file = 0; $file < count($primerarray); $file++) {
                $cuent_descrip = Plan_Cuentas::where('id', $primerarray[$file][0])->first();
                $cuenta = $primerarray[$file][0];
                $debe =  number_format($primerarray[$file][1], 2, '.', '');
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                    'id_plan_cuenta'                => $cuenta,
                    'descripcion'                   => $cuent_descrip->nombre,
                    'fecha'                         => $request['fecha_hoy'],
                    'debe'                          => $debe,
                    'haber'                         => '0',
                    'estado'                        => '1',
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,
                ]);
            }
            $cuenta_cxccliente_com = \Sis_medico\Ct_Configuraciones::obtener_cuenta('CLIENTES_CXCCLIENTE_COMERCIAL');
            $plan_cuentas2 = Plan_Cuentas::where('id', '1.01.02.05.01')->first();
            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => $cuenta_cxccliente_com->cuenta_guardar,
                'descripcion'                   => $cuenta_cxccliente_com->nombre_mostrar,
                'fecha'                         => $request['fecha_hoy'],
                'haber'                         => $valr,
                'debe'                          => '0',
                'estado'                        => '1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            ]);
            $input = [

                'tipo'                          => $request['tipo'],
                'estado'                        => '2',
                'id_asiento'                    => $id_asiento_cabecera,
                'fecha'                         => $request['fecha_hoy'],
                'numero'                        => $numero_factura,
                'nro_comprobante'               => $numeroconcadenado,
                'id_cliente'                    => $request['id_cliente'],
                'divisas'                       => '1',
                'id_recaudador'                 => $idusuario,
                'id_empresa'                    => $id_empresa,
                'ci_vendedor'                   => $idusuario,
                'total_final'                   => $request['total'],
                'valor_contable'                => $request['total'],
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,

            ];
            $id_venta = Ct_ventas::insertGetId($input);
            return [$id_asiento_cabecera, $id_venta, $numero_factura];
        } else {
            return 'error vacios';
        }
        return 'ok';
    }

    public function anular_saldo($id, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $venta_estado = Ct_ventas::where('id', $id)->where('estado', '<>', 0)->first();
        if (!empty($venta_estado)) {
            $act_estado = [
                'valor_contable'                => 0,
                'estado'                        => '-1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            ];


            $fechahoy = Date('Y-m-d H:i:s');
            Ct_ventas::where('id', $id)->update($act_estado);
            //Necesito llenar los datos de la factura pero al revès para que cumplan los datos y quiten las cuentas en el haber
            $compras = Ct_ventas::where('id', $id)->first();
            $id_empresa = $request->session()->get('id_empresa');

            $cabecera = Ct_Asientos_Cabecera::where('id', $compras->id_asiento)->first();
            $actualiza = [
                'estado' => '1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
            ];
            $cabecera->update($actualiza);
            $detalles = $cabecera->detalles;
            $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                'observacion'     => 'ANULACIÓN ' . $cabecera->observacion,
                'fecha_asiento'   => $cabecera->fecha_asiento,
                'id_empresa'      => $id_empresa,
                'fact_numero'     => $cabecera->secuencia,
                'valor'           => $cabecera->valor,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
            foreach ($detalles as $value) {
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento,
                    'id_plan_cuenta'      => $value->id_plan_cuenta,
                    'debe'                => $value->haber,
                    'haber'               => $value->debe,
                    'descripcion'         => $value->descripcion,
                    'fecha'               => $cabecera->fecha_asiento,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ]);
            }
            return redirect()->route('saldosinicialesclientes.index2');
        }
    }



    /************************************************
     ****************LISTADO CLIENTES*****************
    /************************************************/
    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());
        $clientes = Ct_Clientes::where('estado', '!=', null);
        if ($request->buscar_correo != null) {

            $clientes = $clientes->where('email_representante', 'LIKE', '%' . $request->buscar_correo . '%');
        }
        if ($request->buscar_identificacion != null) {
            $clientes = $clientes->where('identificacion', $request->buscar_identificacion);
        }
        if ($request->buscar_nombre != null) {
            $clientes = $clientes->where('nombre', 'LIKE', '%' . $request->buscar_nombre . '%');
        }
        $clientes = $clientes->orderby('identificacion', 'DESC')->paginate(20);


        return view('contable.clientes.index', ['clientes' => $clientes, 'searchingVals' => $request]);
    }

    /*************************************************
     ******************CREAR CLIENTE*******************
    /*************************************************/
    public function crear()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $pais = pais::where('estado', '1')->get();
        return view('contable.clientes.create', ['pais' => $pais]);
    }


    /*************************************************
     **************BUSCAR CLIENTES*********************
    /*************************************************/
    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $constraints = [
            'identificacion' => $request['buscar_identificacion'],
            'nombre'      => $request['buscar_nombre'],
            'email_representante'        => $request['email_representante'],
        ];

        $clientes = $this->doSearchingQuery($constraints);

        return view('contable.clientes.index', ['request' => $request, 'clientes' => $clientes, 'searchingVals' => $constraints]);
    }

    /*************************************************
     ******************CONSULTA QUERY******************
    /*************************************************/

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Clientes::query();

        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {

            if ($constraint != null) {
                //dd($constraint);
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }


        return $query->paginate(5);
    }

    public function store(Request $request)
    {

        $reglas = [

            'nombre'                  => 'required',
            'identificacion'          => 'required|max:50|unique:ct_clientes',
        ];

        $mensajes = [
            'nombre.required'                  => 'Ingrese un Nombre',
            'identificacion.required'          => 'Agregue el número Identificación.',
            'identificacion.unique'            => 'El número de identificación ya existe.',
        ];


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $this->validate($request, $reglas, $mensajes);
        date_default_timezone_set('America/Guayaquil');

        Ct_Clientes::create([


            'nombre'                  => strtoupper($request['nombre']),
            'tipo'                    => $request['tipo_identificacion'],
            'identificacion'          => $request['identificacion'],
            'clase'                   => $request['clase'],
            'nombre_representante'    => $request['nombre_representante'],
            'cedula_representante'    => $request['cedula_representante'],
            'ciudad_representante'    => $request['ciudad_representante'],
            'direccion_representante' => $request['direccion_representante'],
            'telefono1_representante' => $request['telefono1'],
            'telefono2_representante' => $request['telefono2'],
            'email_representante'     => $request['correo'],
            'pais'                    => $request['pais'],
            'estado'                  => '1',
            'comentarios'             => $request['comentario'],
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,


        ]);

        return redirect()->intended('/contable/clientes');
    }

    public function editar($id)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $cliente = Ct_Clientes::where('identificacion', $id)->first();
        $pais = pais::where('estado', '1')->get();

        // Redirect to user list if updating user wasn't existed
        if (is_null($cliente)) {
            return redirect()->intended('/contable/clientes');
        }

        return view('contable/clientes/edit', ['cliente' => $cliente, 'pais' => $pais]);
    }


    public function update(Request $request, $id)
    {

        $clientes  = Ct_Clientes::where('identificacion', $id)->first();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');


        $client = Ct_Clientes::where('identificacion', $request['identificacion'])->first();

        if (!is_null($client)) {

            $input = [

                'nombre'                  => strtoupper($request['nombre']),
                'tipo'                    => $request['tipo_identificacion'],
                'clase'                   => $request['clase'],
                'nombre_representante'    => $request['nombre_representante'],
                'cedula_representante'    => $request['cedula_representante'],
                'ciudad_representante'    => $request['ciudad_representante'],
                'direccion_representante' => $request['direccion_representante'],
                'telefono1_representante' => $request['telefono1'],
                'telefono2_representante' => $request['telefono2'],
                'email_representante'     => $request['correo'],
                'pais'                    => $request['pais'],
                'estado'                  => $request['estado'],
                'comentarios'             => $request['comentario'],
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
            ];
        } else {

            $input = [

                'nombre'                  => strtoupper($request['nombre']),
                'tipo'                    => $request['tipo_identificacion'],
                'identificacion'          => $request['identificacion'],
                'clase'                   => $request['clase'],
                'nombre_representante'    => $request['nombre_representante'],
                'cedula_representante'    => $request['cedula_representante'],
                'ciudad_representante'    => $request['ciudad_representante'],
                'direccion_representante' => $request['direccion_representante'],
                'telefono1_representante' => $request['telefono1'],
                'telefono2_representante' => $request['telefono2'],
                'email_representante'     => $request['correo'],
                'pais'                    => $request['pais'],
                'estado'                  => $request['estado'],
                'comentarios'             => $request['comentario'],
                'id_usuariocrea'          => $idusuario,
                'id_usuariomod'           => $idusuario,
                'ip_creacion'             => $ip_cliente,
                'ip_modificacion'         => $ip_cliente,
            ];
        }

        Ct_Clientes::where('identificacion', $id)->update($input);

        return redirect()->intended('/contable/clientes');
    }
    public static function reload_ty(Request $request)
    {
        $typ = "Erp";
        if ($request->static == 1) {
            $typ = "es";
            return response()->json('ok');
        } else {
            $typ = "esx";
            return \response()->json('gift');
        }
    }
    public function onvideocall(Request $request)
    {
        $user = User::find($request->id);
        $price = $user->price;
        $hour = $request->hour;
        $total = $price * $hour;
        $total = number_format(round($request->hour, 2), '2', '.', '');
        return response()->json(['price' => $total, 'hour' => $hour, 'user' => $user]);
    }
}
