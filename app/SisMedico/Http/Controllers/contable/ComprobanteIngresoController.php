<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\agenda;
use Sis_medico\AgendaQ;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Comprobante_Ingreso;
use Sis_medico\Ct_Detalle_Pago_Ingreso;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_ventas;
use Sis_medico\Empresa;
use Sis_medico\Contable;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Max_Procedimiento;
use Sis_medico\Numeros_Letras;
use Excel;
use Sis_medico\Plan_Cuentas;
use Sis_medico\User;
use Sis_medico\Ct_Detalle_Cruce;
use Sis_medico\Ct_Cruce_Valores;
use Sis_medico\Validate_Decimals;
use Sis_medico\Ct_Cliente_Retencion;
use Sis_medico\Ct_Detalle_Deposito_Bancario;
use Sis_medico\Log_Contable;
use Sis_medico\Ct_Usuario_Proceso;
use Sis_medico\Ct_Paso_Proceso;
use Sis_medico\LogAsiento;
use Illuminate\Support\Facades\Session;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Ct_Sucursales;

class ComprobanteIngresoController extends Controller
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

    public static function confgCajageneral()
    {
        $id_empresa = Session::get("id_empresa");
        $data = "1.01.01.1.01";
        if ($id_empresa == "1793135579001") {
            $data = "1.01.01.01.01";
        }
        return $data;
    } 


    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa   = $request->session()->get('id_empresa');
        $empresa      = Empresa::where('id', $id_empresa)->first();
        $clientes     = Ct_Clientes::where('estado', '1')->get();
        $comp_ingreso = Ct_Comprobante_Ingreso::where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(20);


        return view('contable/comprobante_ingreso/index', ['comp_ingreso' => $comp_ingreso, 'clientes' => $clientes, 'empresa' => $empresa]);
    }

    public function crear(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $lista_banco   = Ct_Bancos::where('estado', '1')->get();
        $tipo_pago     = Ct_Tipo_Pago::where('estado', '1')->get();
        $clientes      = Ct_Clientes::where('estado', '1')->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $id_empresa = $request->session()->get('id_empresa');
        $tipos      = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        return view('contable/comprobante_ingreso/create', ['sucursales' => $sucursales, 'tipos_pagos' => $tipo_pago, 'tipos' => $tipos, 'bancos' => $lista_banco, 'user_vendedor' => $user_vendedor, 'empresa' => $empresa, 'clientes' => $clientes, 'min_date' => $min_date]);
    }
    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $asientos            = Ct_Asientos_Detalle::all();
        $id_empresa          = $request->session()->get('id_empresa');
        $empresa             = Empresa::find($id_empresa);
        $comprobante_ingreso = Ct_Comprobante_Ingreso::where('id', $id)->where('id_empresa', $id_empresa)->first();
        $detalle_ingreso     = Ct_Detalle_Comprobante_Ingreso::where('id_comprobante', $comprobante_ingreso->id)->get();
        $clientes            = Ct_Clientes::where('estado', '1')->get();
        $tipos               = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $user_vendedor       = User::where('id_tipo_usuario', 17)->where('estado', 1)->get();
        $tipo_pago           = Ct_Tipo_Pago::where('estado', '1')->get();
        $lista_banco         = Ct_Bancos::where('estado', '1')->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $sucursal = "";
        $asientoCabecera = Ct_Asientos_Cabecera::find($comprobante_ingreso->id_asiento_cabecera);
        if (!is_null($asientoCabecera)) {
            $sucursal = $asientoCabecera->sucursal;
        }
        return view('contable/comprobante_ingreso/edit', ['id_empresa' => $id_empresa, 'empresa' => $empresa, 'tipos' => $tipos, 'detalle_ingreso' => $detalle_ingreso, 'comprobante_ingreso' => $comprobante_ingreso, 'tipo_pago' => $tipo_pago, 'lista_banco' => $lista_banco, 'user_vendedor' => $user_vendedor, 'sucursales' => $sucursales, 'sucursal' => $sucursal]);
    }
    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        //dd($request->all());

        if ($request->excel == 0) {
            //dd("sds");
            $constraints = [
                'id'            => $request['id'],
                'id_cliente'    => $request['id_cliente'],
                'secuencia'     => $request['secuencia'],
                'observaciones' => $request['observaciones'],
                'fecha'         => $request['fecha'],
                'id_asiento_cabecera' => $request['id_asiento'],
            ];
            $id_empresa = $request->session()->get('id_empresa');

            //dd($constraints);
            $acreedores = $this->doSearchingQuery($constraints, $id_empresa);
            //        $id_empresa = $request->session()->get('id_empresa');
            $clientes = Ct_Clientes::where('estado', '1')->get();

            $armHtml = '';
            if ($request->id_cliente != null) {
                $clienteBuscar = Ct_Clientes::find($request->id_cliente);
                $armHtml = $clienteBuscar->identificacion . '-' . $clienteBuscar->nombre;
            }
            $empresa  = Empresa::where('id', $id_empresa)->first();
            return view('contable/comprobante_ingreso/index', ['armHtml' => $armHtml, 'comp_ingreso' => $acreedores, 'searchingVals' => $constraints, 'clientes' => $clientes, 'empresa' => $empresa]);
        }
        if ($request->excel == 1) {
            self::excel_comprobante_ingreso($request);
        }
    }

    public function excel_comprobante_ingreso(Request $request)
    {
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $query = Ct_Comprobante_Ingreso::query();
        if ($request->has('id') && $request->id !== null) {
            $query->where('id', $request->id);
        }
        if ($request->has('id_cliente') && $request->id_cliente !== null) {
            $query->where('id_cliente', $request->id_cliente);
        }
        if ($request->has('secuencia') && $request->secuencia !== null) {
            $query->where('secuencia', $request->secuencia);
        }
        if ($request->has('observaciones') && $request->observaciones !== null) {
            $query->where('observaciones', 'like', '%' . $request->observaciones . '%');
        }
        if ($request->has('fecha') && $request->fecha !== null) {
            $query->where('fecha', $request->fecha);
        }
        if ($request->has('id_asiento') && $request->id_asiento !== null) {
            $query->where('id_asiento_cabecera', $request->id_asiento);
        }
        $comp_ingreso = $query->orderby('id', 'desc')->get();

        $titulos = array("Id", "Secuencia", "Id asiento", "Fecha", "Cliente", "Detalle", "Total", "Creado por", "Estado");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        Excel::create('Comprobante de Ingreso', function ($excel) use ($titulos, $posicion, $comp_ingreso) {
            $excel->sheet('Comprobante de Ingreso', function ($sheet) use ($titulos, $posicion, $comp_ingreso) {
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Comprobante de Ingreso');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });
                $comienzo = 4;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBackground('#92CFEF');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                $total = 0;

                foreach ($comp_ingreso as $detalle) {

                    $valor = round($detalle->total_ingreso, 2);
                    $total += $detalle->total_ingreso;
                    $valor = round($detalle->total_ingreso, 2);
                    $total_formateado = round($total, 2);


                    $nombre = '';
                    if ($detalle->cliente != null) {
                        $nombre = $detalle->cliente->nombre;
                    }


                    $estado = '';
                    if ($detalle->estado == '1') {
                        $estado = 'Activo';
                    } elseif ($detalle->estado == '0') {
                        $estado = 'Anulada';
                    }

                    $Creado_por = '';

                    if (isset($detalle->usuario)) {
                        $Creado_por     =  $detalle->usuario->nombre1 . ' ' . $detalle->usuario->nombre2;
                    }

                    $datos_excel = [];

                    $datos_excel = [$detalle->id, $detalle->secuencia, $detalle->id_asiento_cabecera, $detalle->fecha, $nombre, $detalle->observaciones, $valor, $Creado_por, $estado];

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($i == 6) {
                                $cell->setAlignment('right');
                            } else {
                                $cell->setAlignment('center');
                            }
                        });
                    }
                    $comienzo++;
                }

                $comienzo++;
                $sheet->cell('F' . $comienzo, function ($cell) {
                    $cell->setValue('VALOR TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });

                if (isset($total_formateado)) {
                    $sheet->cell('G' . $comienzo, function ($cell) use ($total_formateado) {
                        $cell->setValue($total_formateado);
                        $cell->setAlignment('right');
                        $cell->setFontWeight('bold');
                    });
                }
                //dd($total_formateado);
                $sheet->setWidth('A', 18);
                $sheet->setWidth('B', 15);
                $sheet->setWidth('C', 15);
                $sheet->setWidth('D', 15);
                $sheet->setWidth('E', 80);
                $sheet->setWidth('F', 120);
                $sheet->setWidth('G', 20);
                $sheet->setWidth('H', 23);
                $sheet->setWidth('I', 15);
            });
        })->export('xlsx');
    }

    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query  = Ct_Comprobante_Ingreso::query();
        $fields = array_keys($constraints);
        $query  = $query->where('id_empresa', $id_empresa);
        $index  = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                if ($fields[$index] == "id") {
                    $query = $query->where($fields[$index], $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }

            $index++;
        }
        //dd($query->orderBy('id','desc')->paginate(10));
        return $query->orderBy('id', 'desc')->paginate(20);
    }

    public function store(Request $request)
    {

        $id_empresa     = $request->session()->get('id_empresa');
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $contador_ctv   = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->get()->count();
        $objeto_validar = new Validate_Decimals();
        $numero_factura = 0;
        $superavit      = (int) $request['superavit'];
        $resultado      = json_decode($request['listInvoice']);
        $fechahoy       = $request['fecha'];
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($fechahoy));
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }
        DB::beginTransaction();
        try {
            if (!is_null($request['contador'])) {
                if ($contador_ctv == 0) {

                    //return 'No Retorno nada';
                    $num            = '1';
                    $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
                } else {
                    //Obtener Ultimo Registro de la Tabla ct_compras
                    $max_id = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->latest()->first();
                    //dd($max_id);
                    $secuencia = intval($max_id->secuencia);
                    //dd($max_id->secuencia);
                    if (strlen($secuencia) < 10) {
                        $nu             = $secuencia + 1;
                        $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                    }
                }

                $input_cabecera = [
                    'observacion'     => $request['autollenar'] . ' Comprobante de Ingreso fact:' . $numero_factura . ' POR LA CANTIDAD DE ' . $objeto_validar->set_round($request['valor_total']),
                    'fecha_asiento'   => $request['fecha'],
                    'fact_numero'     => $numero_factura,
                    'valor'           => $objeto_validar->set_round($request['valor_total']),
                    'id_empresa'      => $id_empresa,
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'sucursal'        => $request['sucursal'],
                ];
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                if (!is_null($request['restante'])) {

                    if ($request->restante > 0) {
                        $rest = $request['valor_total'] - $request['restante'];
                        if ($rest < 0) {
                            $rest = $rest * (-1);
                        }
                        //dd($request->all());
                        $desc_cuenta = array();
                        if ($id_empresa == "0992704152001") {
                            $desc_cuenta = Plan_Cuentas::where('id', '2.01.10.01.01')->first();
                        } else if ($id_empresa == "1793135579001") {
                            $desc_cuenta = Plan_Cuentas::where('id', '2.01.07.01.01')->first();
                        } else {
                            $desc_cuenta = Plan_Cuentas::where('id', '2.01.10..01.02')->first();
                        }
                        if (Auth::user()->id == "0951561075") {
                            //   dd($desc_cuenta);
                        }

                        $cuenta_ant_cliente = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPROBANTEINGRESO_ANT_CLIENTE');

                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            //'id_plan_cuenta'      => '2.01.10.01.01',
                            'id_plan_cuenta'      => $cuenta_ant_cliente->cuenta_guardar,
                            'descripcion'         => $cuenta_ant_cliente->nombre_mostrar,
                            'fecha'               => $request['fecha'],
                            'haber'               => $request['restante'],
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                        // $confgcli_comerciales = "1.01.02.05.01";
                        // if($id_empresa=="1793135579001"){
                        //     $confgcli_comerciales = "1.01.02.01.01";
                        // }

                        $cuenta_cxccli_come = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPROBANTEINGRESO_CXCCLI_COME');
                        // $desc_cuenta         = Plan_Cuentas::where('id', $confgcli_comerciales)->first();

                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $cuenta_cxccli_come->cuenta_guardar,
                            'descripcion'         => $cuenta_cxccli_come->nombre_mostrar,
                            'fecha'               => $request['fecha'],
                            'haber'               => $objeto_validar->set_round($rest),
                            'debe'                => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                        $secuencia2 = intval($numero_factura);
                        $numero_factura2 = 0;
                        //dd($max_id->secuencia);
                        if (strlen($secuencia2) < 10) {
                            $nu             = $secuencia2 + 1;
                            $numero_factura2 = str_pad($nu, 10, "0", STR_PAD_LEFT);
                        }
                        $input_comprobante2 = [
                            'observaciones'       => $request['autollenar'],
                            'estado'              => '1',
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'fecha'               => $request['fecha'],
                            'secuencia'           => $numero_factura2,
                            'divisas'             => '1',
                            'tipo'                => '2',
                            'id_empresa'          => $id_empresa,
                            'total_ingreso'       => $objeto_validar->set_round($request['restante']),
                            'deficit_ingreso'     => $objeto_validar->set_round($request['restante']),
                            'id_cliente'          => $request['id_cliente'],
                            'autollenar'          => $request['autollenar'],
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ];
                        $id_comprobante2 = Ct_Comprobante_Ingreso::insertGetId($input_comprobante2);
                    } else {
                        // $confgcli_comerciales = "1.01.02.05.01";
                        // if($id_empresa=="1793135579001"){
                        //     $confgcli_comerciales = "1.01.02.01.01";
                        // }
                        // $desc_cuenta         = Plan_Cuentas::where('id', $confgcli_comerciales)->first();

                        $cuenta_cxccli_come = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPROBANTEINGRESO_CXCCLI_COME');
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $cuenta_cxccli_come->cuenta_guardar,
                            'descripcion'         => $cuenta_cxccli_come->nombre_mostrar,
                            'fecha'               => $request['fecha'],
                            'haber'               => $objeto_validar->set_round($request['valor_total']),
                            'debe'                => '0', 
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                    }
                } else {
                    // $confgcli_comerciales = "1.01.02.05.01";
                    //     if($id_empresa=="1793135579001"){
                    //         $confgcli_comerciales = "1.01.02.01.01";
                    //     }
                    // $desc_cuenta         = Plan_Cuentas::where('id', $confgcli_comerciales)->first();

                    $cuenta_cxccli_come = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPROBANTEINGRESO_CXCCLI_COME');
                    Ct_Asientos_Detalle::create([
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuenta_cxccli_come->cuenta_guardar,
                        'descripcion'         => $cuenta_cxccli_come->nombre_mostrar,
                        'fecha'               => $request['fecha'],
                        'haber'               => $objeto_validar->set_round($request['valor_total']),
                        'debe'                => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }

                //el comprobante de ingreso de clientes
                $input_comprobante = [
                    'observaciones'       => $request['autollenar'] . ' la cantidad de ' . $request['valor_total'],
                    'estado'              => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'fecha'               => $request['fecha'],
                    'sc'                  => $request['secuencia'],
                    'os'                  => $request['os'],
                    'id_vendedor'         => $request['id_vendedor'],
                    'secuencia'           => $numero_factura,
                    'tipo'                => '0',
                    'divisas'             => '1',
                    'id_empresa'          => $id_empresa,
                    'total_ingreso'       => $objeto_validar->set_round($request['valor_total']),
                    'deficit_ingreso'     =>  $objeto_validar->set_round($request['valor_total']),
                    'id_cliente'          => $request['id_cliente'],
                    'autollenar'          => $request['autollenar'],
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ];
                $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
                // se detallan los pagos del comprobante segun el tipo de pago.
                for ($i = 0; $i <= $request['contador']; $i++) {
                    if ($request['visibilidad' . $i] == 1) {
                        if ($request['tipo' . $i] == '4') {
                            Ct_Detalle_Pago_Ingreso::create([
                                'id_comprobante'  => $id_comprobante,
                                'fecha'           => $request['fecha' . $i],
                                'numero'          => $request['numero_a' . $i],
                                'id_tipo_tarjeta' => $request['banco' . $i],
                                'id_tipo'         => $request['tipo' . $i],
                                'total'           => $request['valor' . $i],
                                'cuenta'          => $request['cuenta' . $i],
                                'girador'         => $request['girador' . $i],
                                'estado'          => '1',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                        } else if ($request['tipo' . $i] == '6') {
                            Ct_Detalle_Pago_Ingreso::create([
                                'id_comprobante'  => $id_comprobante,
                                'fecha'           => $request['fecha' . $i],
                                'numero'          => $request['numero_a' . $i],
                                'id_tipo_tarjeta' => $request['banco' . $i],
                                'id_tipo'         => $request['tipo' . $i],
                                'total'           => $request['valor' . $i],
                                'cuenta'          => $request['cuenta' . $i],
                                'girador'         => $request['girador' . $i],
                                'estado'          => '1',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                        } else if ($request['tipo' . $i] == '1') {
                            Ct_Detalle_Pago_Ingreso::create([
                                'id_comprobante'  => $id_comprobante,
                                'fecha'           => $request['fecha' . $i],
                                'numero'          => $request['numero_a' . $i],
                                'id_tipo'         => $request['tipo' . $i],
                                'total'           => $request['valor' . $i],
                                'cuenta'          => $request['cuenta' . $i],
                                'girador'         => $request['girador' . $i],
                                'estado'          => '1',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                        } else {
                            Ct_Detalle_Pago_Ingreso::create([
                                'id_comprobante'  => $id_comprobante,
                                'fecha'           => $request['fecha' . $i],
                                'numero'          => $request['numero_a' . $i],
                                'id_banco'        => $request['banco' . $i],
                                'id_tipo'         => $request['tipo' . $i],
                                'total'           => $request['valor' . $i],
                                'cuenta'          => $request['cuenta' . $i],
                                'girador'         => $request['girador' . $i],
                                'estado'          => '1',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                        }
                    }
                }
                //el contador_a se refiere a los pagos en el cruce.
                $consulta_venta  = null;
                $input_actualiza = null;

                /*************************************
                 ****ACTUALIZO CUANDO ES VENTA TODOS LOS VALORES CONTABLES CON EL ABONO DE COMPROBANTE DE INGRESO***
                /*************************************/
                /*
                for ($i = 0; $i <= $request['contador_a']; $i++) {
                if (!is_null($request['abono_a' . $i])) {
                $nuevo_saldo = 0;
                //actualizar valor contable de cada tabla
                $consulta_venta = Ct_ventas::where('id', $request['id_actualiza' . $i])->where('estado', '<>', '0')->first();
                if ($request['abono_a' . $i] > 0) {
                $nuevo_saldof = $objeto_validar->set_round($request['abono_a' . $i]);
                $desc_cuenta = Plan_Cuentas::where('id', ComprobanteIngresoController::confgCajageneral())->first();
                Ct_Asientos_Detalle::create([
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => ComprobanteIngresoController::confgCajageneral(),
                'descripcion'                   => $desc_cuenta->nombre,
                'fecha'                         => $request['fecha'],
                'debe'                          => $nuevo_saldof,
                'haber'                         => '0',
                'estado'                        => '1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                ]);
                if (!is_null($consulta_venta)) {
                Ct_Detalle_Comprobante_Ingreso::create([
                'id_comprobante'                 => $id_comprobante,
                'fecha'                          => $request['fecha'],
                'observaciones'                  => $request['autollenar'],
                'id_factura'                     => $consulta_venta->id,
                'secuencia_factura'              => $request['numero' . $i],
                'total_factura'                  => $request['saldo_a' . $i],
                'total'                          => $request['abono_a' . $i],
                'estado'                         => '1',
                'ip_creacion'                    => $ip_cliente,
                'ip_modificacion'                => $ip_cliente,
                'id_usuariocrea'                 => $idusuario,
                'id_usuariomod'                  => $idusuario,
                ]);
                if ($request['abono_a' . $i] > ($consulta_venta->valor_contable)) {
                $nuevo_saldo = $request['abono_a' . $i] - $consulta_venta->valor_contable;
                } else {
                $nuevo_saldo = $consulta_venta->valor_contable - $request['abono_a' . $i];
                }
                $nuevo_saldof = $objeto_validar->set_round($nuevo_saldo);
                $input_actualiza = null;
                if ($nuevo_saldof != 0) {
                $input_actualiza = [
                'estado_pago'                   => '2', //poner otro estado para que no salga en las consultas
                'valor_contable'                => $nuevo_saldof,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                ];
                } else {
                $input_actualiza = [
                'estado_pago'                   => '3', //poner otro estado para que no salga en las consultas
                'valor_contable'                => $nuevo_saldof,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                ];
                }
                $consulta_venta->update($input_actualiza);
                }
                }
                }
                }*/
                $add = 0;
                /*************************************
                 ****ACTUALIZO CUANDO ES VENTA TODOS LOS VALORES CONTABLES CON EL ABONO DE COMPROBANTE DE INGRESO***
                /*************************************/
                /*
                foreach($request['id_actualiza'] as $x){
                if(!is_null($x)){
                if($request['abono_a'][$add]>0){
                $nuevo_saldo=0;
                $nuevo_saldof = $objeto_validar->set_round($request['abono_a'][$add]);
                $desc_cuenta = Plan_Cuentas::where('id', ComprobanteIngresoController::confgCajageneral())->first();
                Ct_Asientos_Detalle::create([
                'id_asiento_cabecera'           => $id_asiento_cabecera,
                'id_plan_cuenta'                => ComprobanteIngresoController::confgCajageneral(),
                'descripcion'                   => $desc_cuenta->nombre,
                'fecha'                         => $request['fecha'],
                'debe'                          => $nuevo_saldof,
                'haber'                         => '0',
                'estado'                        => '1',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                ]);
                //ventas
                $ventas= Ct_ventas::find($x);
                if(!is_null($ventas)){
                Ct_Detalle_Comprobante_Ingreso::create([
                'id_comprobante'                 => $id_comprobante,
                'fecha'                          => $request['fecha'],
                'observaciones'                  => $request['autollenar'],
                'id_factura'                     => $ventas->id,
                'secuencia_factura'              => $request['numero'][$add],
                'total_factura'                  => $request['saldo_a'][$add],
                'total'                          => $request['abono_a'][$add],
                'estado'                         => '1',
                'ip_creacion'                    => $ip_cliente,
                'ip_modificacion'                => $ip_cliente,
                'id_usuariocrea'                 => $idusuario,
                'id_usuariomod'                  => $idusuario,
                ]);
                if ($request['abono_a'][$add]> ($ventas->valor_contable)) {
                $nuevo_saldo = $request['abono_a'][$add] - $ventas->valor_contable;
                } else {
                $nuevo_saldo = $ventas->valor_contable - $request['abono_a'][$add];
                }
                if ($nuevo_saldo != 0) {
                $input_actualiza = [
                'estado_pago'                   => '2', //poner otro estado para que no salga en las consultas
                'valor_contable'                => $nuevo_saldo,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                ];
                } else {
                $input_actualiza = [
                'estado_pago'                   => '3', //poner otro estado para que no salga en las consultas
                'valor_contable'                => $nuevo_saldo,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                ];
                }
                $ventas->update($input_actualiza);
                }
                }
                }
                $add++;
                }*/
            }
            foreach ($resultado as $key => $value) {
                //dd($value);
                if (!is_null($value)) {
                    if ($value->abono > 0) {
                        $nuevo_saldo  = 0;
                        $nuevo_saldof = $objeto_validar->set_round($value->abono);

                        //ventas
                        $ventas = Ct_ventas::find($value->id);
                        if (!is_null($ventas)) {
                            Ct_Detalle_Comprobante_Ingreso::create([
                                'id_comprobante'    => $id_comprobante,
                                'fecha'             => $request['fecha'],
                                'observaciones'     => $request['autollenar'],
                                'id_factura'        => $ventas->id,
                                'secuencia_factura' => $value->numero,
                                'total_factura'     => $value->saldo,
                                'total'             => $value->abono,
                                'estado'            => '1',
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                                'id_usuariocrea'    => $idusuario,
                                'id_usuariomod'     => $idusuario,
                            ]);
                            if ($value->abono > ($ventas->valor_contable)) {
                                $nuevo_saldo = $value->abono - $ventas->valor_contable;
                            } else {
                                $nuevo_saldo = $ventas->valor_contable - $value->abono;
                            }
                            if ($nuevo_saldo != 0) {
                                $input_actualiza = [
                                    'estado_pago'     => '2', //poner otro estado para que no salga en las consultas
                                    'valor_contable'  => $nuevo_saldo,
                                    'ip_modificacion' => $ip_cliente,
                                    'id_usuariomod'   => $idusuario,
                                ];
                            } else {
                                $input_actualiza = [
                                    'estado_pago'     => '3', //poner otro estado para que no salga en las consultas
                                    'valor_contable'  => $nuevo_saldo,
                                    'ip_modificacion' => $ip_cliente,
                                    'id_usuariomod'   => $idusuario,
                                ];
                            }
                            $ventas->update($input_actualiza);
                        }
                    }
                }
            }
            //     $confgcaja_general = '1.01.01.1.01';
            //    // $caja_general = ComprobanteIngresoController::confgCajageneral();

            //     if($id_empresa=="1793135579001"){
            //         $confgcaja_general = '1.01.01.01.01';
            //     }

            $cuenta_caja_general = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPROBANTEINGRESO_CAJA_GENERAL');

            $desc_cuenta  = Plan_Cuentas::where('id', $cuenta_caja_general->cuenta_guardar)->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_caja_general->cuenta_guardar,
                'descripcion'         => $cuenta_caja_general->nombre_mostrar,
                'fecha'               => $request['fecha'],
                'debe'                => $request['valor_total'],
                'haber'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            DB::commit();
            //dd($id_asiento_cabecera, Ct_Asientos_Cabecera::find($id_asiento_cabecera), Ct_Asientos_Detalle::where("id_asiento_cabecera", $id_asiento_cabecera)->get());
            //return $id_comprobante;
            return ['status' => 'success', 'msj' => 'Proceso ejecutado con exito', 'id_comprobante' => $id_comprobante];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()];
        }
    }

    public function editar($identificacion)
    {

        return view('contable/acreedores/edit');
    }

    public function update($id, Request $request)
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $input_comprobante = [
            'observaciones'       => $request['autollenar'],
            'estado'              => '1',

            'fecha'               => $request['fecha'],
            'sc'                  => $request['secuencia'],
            'os'                  => $request['os'],
            'id_vendedor'         => $request['id_vendedor'],
            'divisas'             => '1',
            'total_ingreso'       => $request['valor_total'],
            'autollenar'          => $request['autollenar'],
            'id_usuariomod'       => $idusuario,
            'ip_modificacion'     => $ip_cliente,
        ];
        $comp_ingreso = Ct_Comprobante_Ingreso::find($id);

        if (!is_null($comp_ingreso)) {
            if ($comp_ingreso->estado != 0) {
                $id_comprobante = $comp_ingreso->update($input_comprobante);
                $detalles = Ct_Detalle_Pago_Ingreso::where('id_comprobante', $id);
                $detalles->delete();
                $cabecera = Ct_Asientos_Cabecera::find($comp_ingreso->id_asiento_cabecera);
                if (!is_null($cabecera)) {
                    $cabecera->fecha_asiento = $request['fecha'];
                    $cabecera->id_usuariomod = $idusuario;
                    $cabecera->ip_modificacion = $ip_cliente;
                    $cabecera->save();
                    foreach ($cabecera->detalles as $x) {
                        $asiento = Ct_Asientos_Detalle::find($x->id);
                        $asiento->fecha = $request['fecha'];
                        $asiento->id_usuariomod = $idusuario;
                        $asiento->ip_modificacion = $ip_cliente;
                        $asiento->save();
                    }
                }
                // se detallan los pagos del comprobante segun el tipo de pago.
                for ($i = 0; $i <= $request['contador']; $i++) {
                    if ($request['visibilidad' . $i] == 1) {
                        if ($request['tipo' . $i] == '4') {
                            Ct_Detalle_Pago_Ingreso::create([
                                'id_comprobante'  => $id,
                                'fecha'           => $request['fecha' . $i],
                                'numero'          => $request['numero_a' . $i],
                                'id_tipo_tarjeta' => $request['banco' . $i],
                                'id_tipo'         => $request['tipo' . $i],
                                'total'           => $request['valor' . $i],
                                'cuenta'          => $request['cuenta' . $i],
                                'girador'         => $request['girador' . $i],
                                'estado'          => '1',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                        } else if ($request['tipo' . $i] == '6') {
                            Ct_Detalle_Pago_Ingreso::create([
                                'id_comprobante'  => $id,
                                'fecha'           => $request['fecha' . $i],
                                'numero'          => $request['numero_a' . $i],
                                'id_tipo_tarjeta' => $request['banco' . $i],
                                'id_tipo'         => $request['tipo' . $i],
                                'total'           => $request['valor' . $i],
                                'cuenta'          => $request['cuenta' . $i],
                                'girador'         => $request['girador' . $i],
                                'estado'          => '1',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                        } else if ($request['tipo' . $i] == '1') {
                            Ct_Detalle_Pago_Ingreso::create([
                                'id_comprobante'  => $id,
                                'fecha'           => $request['fecha' . $i],
                                'numero'          => $request['numero_a' . $i],
                                'id_tipo'         => $request['tipo' . $i],
                                'total'           => $request['valor' . $i],
                                'cuenta'          => $request['cuenta' . $i],
                                'girador'         => $request['girador' . $i],
                                'estado'          => '1',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                        } else {
                            Ct_Detalle_Pago_Ingreso::create([
                                'id_comprobante'  => $id,
                                'fecha'           => $request['fecha' . $i],
                                'numero'          => $request['numero_a' . $i],
                                'id_banco'        => $request['banco' . $i],
                                'id_tipo'         => $request['tipo' . $i],
                                'total'           => $request['valor' . $i],
                                'cuenta'          => $request['cuenta' . $i],
                                'girador'         => $request['girador' . $i],
                                'estado'          => '1',
                                'ip_creacion'     => $ip_cliente,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariocrea'  => $idusuario,
                                'id_usuariomod'   => $idusuario,
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route("comprobante_ingreso.edit", ['id' => $id]);
    }
    public function cambio_tarjeta(Request $request)
    {
        $opcion = $request['opcion'];
        if (!is_null($opcion)) {
            if ($opcion == 0) {
                $lista_banco = Ct_Bancos::where('estado', '1')->get();
                return $lista_banco;
            } else {
                $id_padre = Ct_Tipo_Tarjeta::where('estado', '1')->get();
                return $id_padre;
            }
        } else {
            return 'no';
        }
    }
    public function nombre_cliente(Request $request)
    {
        $nombre    = $request['term'];
        //dd($nombre);
        $data      = array();
        $productos = DB::table('ct_clientes')->where('nombre', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->nombre, 'id' => $product->identificacion);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function id_cliente(Request $request)
    {
        $nombre    = $request['term'];
        $data      = array();
        $productos = DB::table('ct_clientes')->where('identificacion', 'like', '%' . $nombre . '%')->get();
        foreach ($productos as $product) {
            $data[] = array('value' => $product->identificacion, 'id' => $product->nombre);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }
    }
    public function datos_cliente(Request $request)
    {
        $codigo    = $request['codigo'];
        $data      = null;
        $productos = DB::table('ct_clientes')->where('identificacion', $codigo)->first();
        if (!is_null($productos)) {
            return ['value' => $productos->nombre, 'direccion' => $productos->direccion_representante];
        } else {
            return ['value' => 'no'];
        }
    }
    public function datos_cliente2(Request $request)
    {
        $codigo    = $request['nombre'];
        $data      = null;
        $productos = DB::table('ct_clientes')->where('nombre', $codigo)->first();
        if (!is_null($productos)) {
            return ['value' => $productos->identificacion, 'direccion' => $productos->direccion_representante];
        } else {
            return ['value' => 'no'];
        }
    }
    public function deudas(Request $request)
    {
        $id_proveedor = $request['id_cliente'];
        $id_empresa   = $request->session()->get('id_empresa');
        $data         = 0;
        $tipo         = $request['tipo'];
        $facturas     = '[]';
        $deudas       = null;
        $facturas     = DB::table('ct_clientes as p')
            ->join('ct_ventas as co', 'co.id_cliente', 'p.identificacion')
            ->where('co.id_cliente', $id_proveedor)
            ->where('co.id_empresa', $id_empresa)
            ->where('co.valor_contable', '>', '0')
            ->where('co.estado', '<>', '0')
            ->orderBy('co.fecha', 'desc')
            ->select('co.valor_contable', 'co.numero', 'co.id', 'co.tipo', 'co.concepto', 'co.fecha', 'co.nro_comprobante')
            ->get();
        if ($facturas != '[]') {
            $data = [$facturas[0]->valor_contable, $facturas[0]->numero, $facturas[0]->id, $facturas[0]->fecha, $facturas];
            return $data;
        } else {
            return ['value' => 'no resultados'];
        }
    }
    public function pdf_comprobante(Request $request, $id)
    {
        $comp_ingreso    = Ct_Comprobante_Ingreso::where('id', $id)->first();
        $empresa         = Empresa::where('id', $comp_ingreso->id_empresa)->first();
        $letras          = new Numeros_Letras();
        $detalle_ingreso = Ct_Detalle_Comprobante_Ingreso::where('id_comprobante', $comp_ingreso->id)->first();
        //la variable convertir con la clase Numeros Letras
        $total_str = $letras->convertir(number_format($comp_ingreso->total_ingreso, 2, '.', ''), "DOLARES", "CTVS");
        //dd($factura_contable);
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comp_ingreso->id_asiento_cabecera)->first();
        $asiento_detalle  = null;
        if ($asiento_cabecera != null) {
            $asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        }
        //dd($asiento_detalle);

        if ($comp_ingreso != '[]') {
            $vistaurl = "contable.comprobante_ingreso.pdf_comprobante_ingreso";
            $view     = \View::make($vistaurl, compact('comp_ingreso', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle'))->render();
            $pdf      = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->stream('Comprobante de Ingreso' . $id . '.pdf');
        }

        return 'error';
    }
    public function anular($id, Request $request)
    {
        DB::beginTransaction();
        try {
            if (!is_null($id)) {
                $comp_ingreso = Ct_Comprobante_Ingreso::where('estado', '1')->where('id', $id)->first();
                $detallesa    = Ct_Detalle_Comprobante_Ingreso::where('id_comprobante', $id)->get();
                $ip_cliente   = $_SERVER["REMOTE_ADDR"];
                $id_empresa   = $request->session()->get('id_empresa');
                $idusuario    = Auth::user()->id;
                $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($comp_ingreso->fecha));
                if ($verifica_cierre->status == 'error') {
                    return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
                }
                if (!is_null($comp_ingreso)) {
                    $input = [
                        'estado'          => '0',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod'   => $idusuario,
                    ];
                    $comp_ingreso->update($input);

                    // ahora actualizo el valor y le sumo lo que ya le había restado
                    //dd($comp_ingreso->detalle);
                    foreach ($detallesa as $value) {
                        $consulta_venta = Ct_ventas::where('id', $value->id_factura)->where('id_empresa', $id_empresa)->where('estado', '1')->first();
                        if (!is_null($consulta_venta)) {
                            $valor           = $consulta_venta->valor_contable;
                            $suma            = ($value->total) + $valor;
                            $input_actualiza = [
                                'valor_contable'  => $suma,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                            ];
                            $consulta_venta->update($input_actualiza);
                            $recalculo = Contable::recovery_price($consulta_venta->id, 'V');
                        }
                    }

                    $asiento = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                    if (Auth::user()->id == "0957258056") {
                        //dd($comp_ingreso);
                    }

                    $asiento->estado = 1;
                    $asiento->save();
                    $detalles   = $asiento->detalles;
                    $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                        'observacion'     => 'ANULACIÓN ' . $asiento->observacion,
                        'fecha_asiento'   => $asiento->fecha_asiento,
                        'id_empresa'      => $id_empresa,
                        'fact_numero'     => $comp_ingreso->secuencia,
                        'valor'           => $asiento->valor,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'sucursal'        => $asiento->sucursal,
                    ]);
                    foreach ($detalles as $value) {
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento,
                            'id_plan_cuenta'      => $value->id_plan_cuenta,
                            'debe'                => $value->haber,
                            'haber'               => $value->debe,
                            'descripcion'         => $value->descripcion,
                            'fecha'               => $asiento->fecha_asiento,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                        ]);
                    }
                    LogAsiento::anulacion("CL-CIN", $id_asiento, $asiento->id);

                    DB::commit();
                    return 'ok';
                }
            } else {
                return 'error';
            }
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function superavit(Request $request)
    {
        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $id_empresa     = $request->session()->get('id_empresa');
        $idusuario      = Auth::user()->id;
        $objeto_validar = new Validate_Decimals();

        $fechahoy       = $request['fecha'];
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($fechahoy));
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }
        if (!is_null($request['total_ingresos'])) {
            $numero_factura = 0;
            $contador_ctv   = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->get()->count();
            if ($contador_ctv == 0) {

                //return 'No Retorno nada';
                $num            = '1';
                $numero_factura = str_pad($num, 10, "0", STR_PAD_LEFT);
            } else {
                //Obtener Ultimo Registro de la Tabla ct_compras
                $max_id = DB::table('ct_comprobante_ingreso')->where('id_empresa', $id_empresa)->latest()->first();
                //dd($max_id);
                $secuencia = intval($max_id->secuencia);
                //dd($max_id->secuencia);
                if (strlen($secuencia) < 10) {
                    $nu             = $secuencia + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
            $input_cabecera = [
                'observacion'     => $request['autollenar'],
                'fecha_asiento'   => $request['fecha'],
                'fact_numero'     => $numero_factura,
                'valor'           => $objeto_validar->set_round($request['total_ingresos']),
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'sucursal'        => $request['sucursal'],
                'id_usuariomod'   => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            //cuentas por cobrar clientes
            //$desc_cuenta = Plan_Cuentas::where('id', ComprobanteIngresoController::confgCajageneral())->first();
            $cuenta_caja_general = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPROBANTEINGRESO_CAJA_GENERAL');
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_caja_general->cuenta_guardar,
                'descripcion'         => $cuenta_caja_general->nombre_mostrar,
                'fecha'               => $request['fecha'],
                'debe'               => $objeto_validar->set_round($request['total_ingresos']),
                'haber'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            //cuentas de anticipos clientes
            $desc_cuenta = array();
            if ($id_empresa == "0992704152001") {
                $desc_cuenta = Plan_Cuentas::where('id', '2.01.10.01.01')->first();
            } else {
                $desc_cuenta = Plan_Cuentas::where('id', '2.01.10..01.02')->first();
            }

            $cuenta_ant_cliente = \Sis_medico\Ct_Configuraciones::obtener_cuenta('COMPROBANTEINGRESO_ANT_CLIENTE');
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $cuenta_ant_cliente->cuenta_guardar,
                'descripcion'         => $cuenta_ant_cliente->nombre_mostrar,
                'fecha'               => $request['fecha'],
                'haber'                => $objeto_validar->set_round($request['total_ingresos']),
                'debe'               => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
            $input_comprobante = [
                'observaciones'       => $request['autollenar'] . " anticipo de cliente ",
                'estado'              => '1',
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'fecha'               => $request['fecha'],
                'secuencia'           => $numero_factura,
                'divisas'             => '1',
                'tipo'                => '2',
                'id_empresa'          => $id_empresa,
                'total_ingreso'       => $objeto_validar->set_round($request['total_ingresos']),
                'deficit_ingreso'     => $objeto_validar->set_round($request['total_ingresos']),
                'id_cliente'          => $request['id_cliente'],
                'autollenar'          => $request['autollenar'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Ingreso::insertGetId($input_comprobante);
            for ($i = 0; $i < $request['contador']; $i++) {
                if ($request['visibilidad' . $i] == 1) {
                    if ($request['tipo' . $i] == '4') {
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante'  => $id_comprobante,
                            'fecha'           => $request['fecha' . $i],
                            'numero'          => $request['numero_a' . $i],
                            'id_tipo_tarjeta' => $request['banco' . $i],
                            'id_tipo'         => $request['tipo' . $i],
                            'total'           => $request['valor' . $i],
                            'cuenta'          => $request['cuenta' . $i],
                            'girador'         => $request['girador' . $i],
                            'estado'          => '1',
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                    } else if ($request['tipo' . $i] == '6') {
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante'  => $id_comprobante,
                            'fecha'           => $request['fecha' . $i],
                            'numero'          => $request['numero_a' . $i],
                            'id_tipo_tarjeta' => $request['banco' . $i],
                            'id_tipo'         => $request['tipo' . $i],
                            'total'           => $request['valor' . $i],
                            'cuenta'          => $request['cuenta' . $i],
                            'girador'         => $request['girador' . $i],
                            'estado'          => '1',
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                    } else if ($request['tipo' . $i] == '1') {
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante'  => $id_comprobante,
                            'fecha'           => $request['fecha' . $i],
                            'numero'          => $request['numero_a' . $i],
                            'id_tipo'         => $request['tipo' . $i],
                            'total'           => $request['valor' . $i],
                            'cuenta'          => $request['cuenta' . $i],
                            'girador'         => $request['girador' . $i],
                            'estado'          => '1',
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                    } else {
                        Ct_Detalle_Pago_Ingreso::create([
                            'id_comprobante'  => $id_comprobante,
                            'fecha'           => $request['fecha' . $i],
                            'numero'          => $request['numero_a' . $i],
                            'id_banco'        => $request['banco' . $i],
                            'id_tipo'         => $request['tipo' . $i],
                            'total'           => $request['valor' . $i],
                            'cuenta'          => $request['cuenta' . $i],
                            'girador'         => $request['girador' . $i],
                            'estado'          => '1',
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                    }
                }
            }
            //return $id_comprobante;
            return ['status' => 'success', 'msj' => 'Proceso ejecutado con exito', 'id_comprobante' => $id_comprobante];
        } else {
            return 'error total favor';
        }
    }
    public function verificar(Request $request)
    {
        if (!is_null($request['id_venta'])) {
            switch ($request['verificacion']) {
                case '1':
                    $id_ingresos      = 0;
                    $id_retencion     = 0;
                    $verificar        = 0;
                    $id_cruce         = 0;
                    $ingreso_cabecera = "";
                    $idusuario      = Auth::user()->id;
                    $information = Contable::recovery_by_model('O', 'V', $request['id_venta']);
                    return $information;
                    //}

                    // $ingresos         = Ct_Detalle_Comprobante_Ingreso::where('id_factura', $request['id_venta'])->where('estado', '!=', '0')->get();
                    // if (!is_null($ingresos) && $ingresos != '[]') {

                    //     //dd($ingresos)
                    //     $ingreso_cabecera = Ct_Comprobante_Ingreso::where('id', $ingresos[0]->id_comprobante)->where('estado', '!=', '0')->first();
                    // }

                    // if ($ingreso_cabecera == "" || $ingreso_cabecera == '[]') {
                    //     $verificar++;
                    // } else {
                    //     //dd($ingreso_cabecera);
                    //     $id_ingresos = $ingreso_cabecera->id;
                    // }
                    // $retenciones = Ct_Cliente_Retencion::where('id_factura', $request['id_venta'])->where('estado', '!=', '0')->get();
                    // //dd($retenciones);
                    // if (is_null($retenciones) || $retenciones == '[]') {
                    //     $verificar++;
                    // } else {
                    //     $id_retencion = $retenciones[0]->id;
                    // }
                    // $cruce_detalle = Ct_Detalle_Cruce::where('id_factura', $request['id_venta'])->where('estado', "!=", '0')->get();
                    // if(count($cruce_detalle)>0){
                    //     foreach($cruce_detalle as $c){
                    //         $cruce = Ct_Cruce_Valores::where('id', $c->id_comprobante)->where('estado','1')->first();
                    //         if(!is_null($cruce)){
                    //             $id_cruce = $cruce->id;
                    //         }
                    //     }
                    // }

                    // //dd("retencion {$id_retencion} - verificar{$verificar} - {$id_ingresos}");
                    // return [$verificar, $id_retencion, $id_ingresos, $id_cruce];
                    break;
                case '2':
                    //caso de comprobante de ingreso
                    // $deposito = Ct_Detalle_Pago_Ingreso::where('id_comprobante',$request->id_venta)->first();

                    /* det_deposito->ct_detalle_pago_ingreso[id=>$compro_ingreso->id]->ct_deposito_bancario */

                    $id_empresa   = $request->session()->get('id_empresa');
                    $verificar           = 0;
                    $id_deposito         = 0;
                    $comprobante_ingreso = Ct_Comprobante_Ingreso::where('id', $request['id_venta'])->first();
                    if (!is_null($comprobante_ingreso)) {
                        $secuencia = $comprobante_ingreso->secuencia;
                        $deb       = DB::table('ct_detalle_deposito_bancario as cddb')
                            ->join('ct_detalle_pago_ingreso as dpi', 'cddb.id_ingreso', 'dpi.id')
                            ->join('ct_deposito_bancario as cdb', 'cddb.deposito_bancario_id', 'cdb.id')
                            ->where('dpi.id_comprobante', '=', $comprobante_ingreso->id)
                            //->orWhere('cddb.facturas', '=', $secuencia2)
                            // ->where('cddb.estado', '!=', '0')
                            // ->where('dpi.tipo_c', "<>", 'CIV')
                            ->where('cdb.empresa', $id_empresa)
                            ->select('cddb.deposito_bancario_id', 'dpi.*', 'cdb.estado as estadoDeposito')
                            ->get();

                        // if (!is_null($deb)) {
                        //     return ['respuesta' => 'si', 'id' => $deb->deposito_bancario_id];
                        // } else {
                        //     return ['respuesta' => 'no'];
                        // }

                        if (count($deb) > 0) {
                            $isExiste = 0;
                            $idBancario = 0;
                            foreach ($deb as $row) {
                                if ($row->tipo_c != "CIV" and $row->estadoDeposito == 1) {
                                    $isExiste = 1;
                                    $idBancario = $row->deposito_bancario_id;
                                    break;
                                }
                            }
                            if ($isExiste == 1)
                                return ['respuesta' => 'si', 'id' => $idBancario];
                            else
                                return ['respuesta' => 'no'];
                        } else {
                            return ['respuesta' => 'no'];
                        }
                        /*if (is_null($deb) || $deb == '[]') {
                                $verificar++;
                            } else {
                                $id_deposito = $deb->deposito_bancario_id;
                            }
                            return [$verificar, $id_deposito];*/
                    }

                    return "si";
                    break;
                case '3':
                    $cliente_retencion = Ct_Cliente_Retencion::find($request->id_venta);
                    $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($cliente_retencion['fecha']));
                    if ($verifica_cierre->status == 'error') {
                        return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
                    }
                    if (!is_null($cliente_retencion)) {
                        $information = Contable::recovery_by_model('O', 'V', $cliente_retencion->id_factura);
                        if (isset($information->original)) {
                            if (isset($information->original['ingreso'])) {
                                if (count($information->original['ingreso']) > 0) {
                                    return ['modulo' => 'Comprobante Ingreso', 'id' => $information->original['ingreso'][0], 'respuesta' => 'si'];
                                }
                            } else {
                                return ['respuesta' => 'no'];
                            }
                        } else {
                            return $information;
                        }
                    } else {
                        return ['respuesta' => 'no'];
                    }

                    // $id_ingresos = 0;
                    // $verificar   = 0;
                    // $ingresos    = Ct_Detalle_Comprobante_Ingreso::where('id_factura', $request['id_venta'])->where('estado', '!=', '0')->get();
                    // if (is_null($ingresos) || $ingresos == '[]') {
                    //     $verificar++;
                    // } else {
                    //     $id_ingresos = $ingresos[0]->id_comprobante;
                    // }
                    // return [$verificar, $id_ingresos];
                    // break;

            }
        } else {
            return 'error';
        }
    }
    public function index_proceso(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $id_auth    = Auth::user()->id;
        $usuarios = Ct_Usuario_Proceso::orderBy('id', 'DESC')->groupBy('id_usuario')->paginate(10);
        //dd($usuarios);

        return view('contable.usuario_proceso.index', ['usuarios' => $usuarios, 'empresa' => $empresa]);
    }
    public function crear_proceso(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $usuarios = User::where('id_tipo_usuario', '<>', 2)->get();
        $paso_procesos = Ct_Paso_Proceso::all();

        return view('contable.usuario_proceso.create', ['usuarios' => $usuarios, 'empresa' => $empresa, 'paso_procesos' => $paso_procesos]);
    }
    public function guardar(Request $request)
    {
        $hoy = date("Y-m-d H:i:s");
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $id_empresa = $request->session()->get('id_empresa');
        for ($i = 0; $i < count($request->id_paso); $i++) {

            $user_proc = Ct_Usuario_Proceso::where('id_usuario', $request->id_usuario)->where('id_paso', $request->id_paso[$i])->first();

            if (is_null($user_proc)) {
                $variable = Ct_Usuario_Proceso::create([
                    'id_paso' => $request->id_paso[$i],
                    'id_usuario' => $request['id_usuario'],
                    'id_empresa'  => $id_empresa,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                ]);
            }
        }
        return json_encode("ok");
    }
    public function editar_proceso(Request $request, $id)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        //dd($empresa);
        $paso_procesos = Ct_Paso_Proceso::all();
        $usuarios_procesos = Ct_Usuario_Proceso::where('id', $id)->first();
        $usuarios = User::where('id_tipo_usuario', '<>', 2)->where('id', $usuarios_procesos->id_usuario)->get();
        $paso = Ct_Usuario_Proceso::where('id_usuario', $usuarios_procesos->id_usuario)->get();
        //dd($paso);
        //dd($paso_procesos);
        return view('contable/usuario_proceso/edit', ['paso_procesos' => $paso_procesos, 'usuarios' => $usuarios, 'usuarios_procesos' => $usuarios_procesos, 'paso' => $paso, 'id' => $id, 'empresa' => $empresa]);
    }
    public function actualizar_proceso(Request $request)
    {

        date_default_timezone_set('America/Guayaquil');
        $idusuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        DB::beginTransaction();
        try {
            $usuario = Ct_Usuario_Proceso::where('id_usuario', $request->id_usuario)->delete();
            for ($i = 0; $i < count($request->id_paso); $i++) {
                $variable = Ct_Usuario_Proceso::create([
                    'id_paso' => $request->id_paso[$i],
                    'id_usuario' => $request['id_usuario'],
                    'id_empresa'  => $id_empresa,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,

                ]);
            }
            DB::commit();
            return ['respuesta' => "si", "msj" => "Guardado correcto"];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => "no", "msj" => $e->getMessage()];
        }
        return json_encode('ok');
    }
    public function buscar_nombre_codigo(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $clientes  = [];
        if ($request['search'] != null) {
            $buscador = $request['search'];
            $clientes = Ct_Clientes::where('estado', '1');
            $clientes = $clientes->where(function ($jq1) use ($buscador) {
                $jq1->orwhere('identificacion', "LIKE", "%{$buscador}%")
                    ->orwhere('nombre', 'LIKE', "%{$buscador}%");
            });
            $clientes = $clientes->select('identificacion as id', DB::raw('CONCAT(identificacion," - ",nombre) as text'))->get();
        }
        return response()->json($clientes);
    }



    public function clientessearch(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $cliente  = [];
        if ($request['search'] != null) {
            $cliente = Ct_Clientes::where('nombre', 'LIKE', '%' . $request['search'] . '%')->where('estado', '1')->select('ct_clientes.identificacion as id', 'ct_clientes.nombre as text')->get();
            //dd($cliente);     
        }
        return response()->json($cliente);
    }
}
