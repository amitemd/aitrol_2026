<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Response;
use Sis_medico\Contable;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Masivo;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\LogAsiento;
use Sis_medico\Numeros_Letras;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Proveedor;
use Sis_medico\User;
use Sis_medico\Validate_Decimals;
use Sis_medico\Ct_Detalle_Comprobante_Egreso;
use Sis_medico\Ct_Comprobante_Egreso;
use Sis_medico\Ct_Detalle_Cruce;
use Sis_medico\Ct_Debito_Bancario_Detalle;
use Sis_medico\Ct_Retenciones;
use Sis_medico\Ct_Credito_Acreedores;
use Sis_medico\Ct_Detalle_Debito_Acreedores;
use Sis_medico\Ct_Cruce_Cuentas;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Mail;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Sucursales;
use Sis_medico\LogConfig;
use Sis_medico\Plan_Cuentas_Empresa;

class Egreso_MasivoController extends Controller
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
        $id_empresa           = $request->session()->get('id_empresa');
        $empresa              = Empresa::where('id', $id_empresa)->first();
        $comprobante_egreso_m = Ct_Comprobante_Egreso_Masivo::where('id_empresa', $id_empresa)->orderBy('id', 'DESC')->get();
        //     dd($comprobante_egreso_m);
        return view('contable/comp_egreso_masivo/index', ['empresa' => $empresa, 'comprobante_egreso_m' => $comprobante_egreso_m]);
    }
    public function create(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa    = $request->session()->get('id_empresa');
        $divisas       = Ct_divisas::where('estado', '1')->get();
        $banco         = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $tipo_pago     = Ct_Tipo_Pago::where('estado', '1')->get();
        $formas_pago   = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $tipos   = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $empresa = Empresa::where('id', $id_empresa)->first();
        $compras = Ct_compras::where('estado', '!=', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->orderBy('f_autorizacion', 'desc')->take(100)->get();

        if ($id_empresa == '0993069299001') {
            $compras = Ct_compras::where('estado', '!=', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->whereIn('proveedor', ['0992704152001', '0991330887001', '0905723466001', '0603804527001', '0993094072001', '0993265802001', '1201918453001'])->orderBy('f_autorizacion', 'desc')->take(100)->get();
        }

        if ($id_empresa == '0992704152001' || $id_empresa == '1314490929001' || $id_empresa == '1307189140001' || $id_empresa == '0993170887001') {
            return view('contable/comp_egreso_masivo/create', ['divisas' => $divisas, 'tipos_pagos' => $tipo_pago, 'tipos' => $tipos, 'bancos' => $banco, 'user_vendedor' => $user_vendedor, 'empresa' => $empresa, 'compras' => $compras, 'formas_pago' => $formas_pago]);
        } else {
            return view('contable/comp_egreso_masivo/create2', ['divisas' => $divisas, 'tipos_pagos' => $tipo_pago, 'tipos' => $tipos, 'bancos' => $banco, 'user_vendedor' => $user_vendedor, 'empresa' => $empresa, 'compras' => $compras, 'formas_pago' => $formas_pago]);
        }
    }
    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa            = $request->session()->get('id_empresa');
        $empresa               = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $divisas               = Ct_divisas::where('estado', '1')->get();
        $formas_pago           = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $banco                 = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $comprobante_egreso_m  = Ct_Comprobante_Egreso_Masivo::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $detalle_comprobante_m = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_comprobante', $comprobante_egreso_m->id)->get();
        $sucursal = "";
        $asientoCabecera = Ct_Asientos_Cabecera::find($comprobante_egreso_m->id_asiento_cabecera);
        if (!is_null($asientoCabecera)) {
            $sucursal = $asientoCabecera->sucursal;
        }
        return view('contable/comp_egreso_masivo/edit', ['divisas' => $divisas, 'formas_pago' => $formas_pago, 'empresa' => $empresa, 'banco' => $banco, 'detalle_egreso_m' => $detalle_comprobante_m, 'comprobante_egreso_m' => $comprobante_egreso_m, 'sucursal' => $sucursal]);
    }
    public function store2(Request $request)
    {
        if (Auth::user()->id == '0951561075') {
            //dd($request['contador']);
        }
        $contador_ctv      = DB::table('ct_comprobante_egreso_masivo')->get()->count();
        $numero_factura    = 0;
        $superavit         = (int) $request['superavit'];
        $secuencia_factura = (int) $request['asiento'];
        $secuencia         = 0;
        $id_proveedor      = $request['id_proveedor'];
        $id_empresa        = $request->session()->get('id_empresa');
        $fechahoy          = $request['fecha_hoy'];
        $ip_cliente        = $_SERVER["REMOTE_ADDR"];
        $idusuario         = Auth::user()->id;
        $contador_ctv      = DB::table('ct_comprobante_egreso_masivo')->where('id_empresa', $id_empresa)->get()->count();
        $numero_factura    = 0;
        $banco             = $request['banco'];
        //dd($request->abono0);
        $objeto_validar = new Validate_Decimals();

        if ($contador_ctv == 0) {

            $num            = '1';
            $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
        } else {

            $max_id = DB::table('ct_comprobante_egreso_masivo')->where('id_empresa', $id_empresa)->latest()->first();
            $max_id = intval($max_id->secuencia);
            if (strlen($max_id) < 10) {
                $nu             = $max_id + 1;
                $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }
        }

        $input_cabecera = [
            'observacion'     => $request['aaa'],
            'fecha_asiento'   => $request['fecha_hoy'],
            'fact_numero'     => $numero_factura,
            'valor'           => $request['valor_cheque'],
            'id_empresa'      => $id_empresa,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
        if (($banco) != null) {
            $nuevo_saldof      = $objeto_validar->set_round($request['valor_cheque']);
            $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();

            $desc_cuenta = Plan_Cuentas::where('id', $consulta_db_cajab->cuenta_mayor)->first();
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $consulta_db_cajab->cuenta_mayor,
                'descripcion'         => $consulta_db_cajab->nombre,
                'fecha'               => $request['fecha_hoy'],
                'haber'               => $nuevo_saldof,
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);
        }

        $input_comprobante = [
            'descripcion'         => $request['aaa'],
            'estado'              => '1',
            'beneficiario'        => $request['nombre_proveedor'],
            'fecha_cheque'        => $request['fecha_cheque'],
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'check'               => $request['verificar_cheque'],
            'id_secuencia'        => $request['numero'],
            'id_pago'             => $request['formas_pago'],
            'id_caja_banco'       => $request['banco'],
            'no_cheque'           => $request['numero_cheque'],
            'fecha_comprobante'   => $request['fecha_hoy'],
            'secuencia'           => $numero_factura,
            'girado_a'            => $request['giradoa'],
            'id_empresa'          => $id_empresa,
            'valor_pago'          => $request['valor_cheque'],
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
        ];

        $id_comprobante = Ct_Comprobante_Egreso_Masivo::insertGetId($input_comprobante);
        //dd($request->all());
        $primerarray = [];
        for ($i = 0; $i <= $request['contador']; $i++) {

            if (!is_null($request['abono' . $i])) {

                if (($request['abono' . $i]) > 0) {
                    //$nuevo_saldof = $objeto_validar->set_round($request['abono' . $i]);
                    $consulta_proveedor = Proveedor::where('id', $request['proveedor' . $i])->first();
                    $nuevo_saldof       = $objeto_validar->set_round($request['abono' . $i]);
                    $c                  = $consulta_proveedor->id_cuentas;
                    if ($c == null) {
                        $c = "2.01.03.01.01";
                    }
                    $cuent_descrip   = Plan_Cuentas::find($c);
                    if (Auth::user()->id == "0957258056") {
                        //dd($request['id_actualiza' . $i]);
                    }
                    $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->first();
                    Ct_Detalle_Comprobante_Egreso_Masivo::create([
                        'id_comprobante'  => $id_comprobante,
                        'observacion'     => $request['aaa'],
                        'id_compra'       => $consulta_compra->id,
                        'id_secuencia'    => $request['numero' . $i],
                        'id_proveedor'    => $request['proveedor' . $i],
                        'saldo_base'      => $request['saldo' . $i],
                        'abono'           => $request['abono' . $i],
                        'estado'          => '1',
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ]);
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuent_descrip->id,
                        'descripcion'         => $cuent_descrip->nombre,
                        'fecha'               => $fechahoy,
                        'debe'                => $nuevo_saldof,
                        'haber'               => '0',
                        'estado'              => '1',
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                    ]);
                }
                //dd($request['abono'.$i]);

            }
        }

        $consulta_compra = null;
        $input_actualiza = null;

        for ($i = 0; $i <= $request['contador']; $i++) {
            if (!is_null($request['abono' . $i]) && $request['abono' . $i] > 0) {
                $nuevo_saldo = 0;
                //actualizar valor contable de cada tabla
                $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->first();
                //dd($consulta_compra);
                if ($consulta_compra != null || $consulta_compra != '[]') {
                    if ($request['abono' . $i] > 0) {
                        if ($request['abono' . $i] > ($consulta_compra->valor_contable)) {
                            $nuevo_saldo = $request['abono' . $i] - $consulta_compra->valor_contable;
                        } else {
                            $nuevo_saldo = $consulta_compra->valor_contable - $request['abono' . $i];
                        }

                        $nuevo_saldof    = $objeto_validar->set_round($nuevo_saldo);
                        $input_actualiza = null;
                        if ($nuevo_saldof != 0) {
                            $input_actualiza = [
                                'estado'          => '2', //poner otro estado para que no salga en las consultas
                                'valor_contable'  => $nuevo_saldof,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                            ];
                        } else {
                            $input_actualiza = [
                                'estado'          => '3', //poner otro estado para que no salga en las consultas
                                'valor_contable'  => $nuevo_saldof,
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                            ];
                        }
                        $consulta_compra->update($input_actualiza);
                    }
                }
            }
        }
        return $id_comprobante;
    }
    public function store(Request $request)
    {
        if (Auth::user()->id == "0957258056") {
            //  dd("store 1", $request->all());
        }
        DB::beginTransaction();
        try {
            $contador_ctv      = DB::table('ct_comprobante_egreso_masivo')->get()->count();
            $numero_factura    = 0;
            $superavit         = (int) $request['superavit'];
            $secuencia_factura = (int) $request['asiento'];
            $secuencia         = 0;
            $id_proveedor      = $request['id_proveedor'];
            $id_empresa        = $request->session()->get('id_empresa');
            $fechahoy          = $request['fecha_hoy'];
            $ip_cliente        = $_SERVER["REMOTE_ADDR"];
            $idusuario         = Auth::user()->id;
            $contador_ctv      = DB::table('ct_comprobante_egreso_masivo')->where('id_empresa', $id_empresa)->get()->count();
            $numero_factura    = 0;
            $banco             = $request['banco'];
            $objeto_validar = new Validate_Decimals();
            if ($contador_ctv == 0) {
                $num            = '1';
                $numero_factura = str_pad($num, 9, "0", STR_PAD_LEFT);
            } else {
                $max_id = DB::table('ct_comprobante_egreso_masivo')
                    ->where('id_empresa', $id_empresa)
                    ->latest()
                    ->first();
                $max_id = intval($max_id->secuencia);
                if (strlen($max_id) < 10) {
                    $nu             = $max_id + 1;
                    $numero_factura = str_pad($nu, 10, "0", STR_PAD_LEFT);
                }
            }
            $input_cabecera = [
                'observacion'     => $request['aaa'],
                'fecha_asiento'   => $request['fecha_hoy'],
                'fact_numero'     => $numero_factura,
                'valor'           => $request['valor_cheque'],
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            echo '<pre>';
            print_r($input_cabecera);
            DB::rollBack();
            exit;
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            if (($banco) != null) {
                $nuevo_saldof      = $objeto_validar->set_round($request['valor_cheque']);
                $consulta_db_cajab = Ct_Caja_Banco::where('id', $banco)->first();
                $desc_cuenta = Plan_Cuentas::where('id', $consulta_db_cajab->cuenta_mayor)->first();
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $consulta_db_cajab->cuenta_mayor,
                    'descripcion'         => $consulta_db_cajab->nombre,
                    'fecha'               => $request['fecha_hoy'],
                    'haber'               => $nuevo_saldof,
                    'debe'                => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            $input_comprobante = [
                'descripcion'         => $request['aaa'],
                'estado'              => '1',
                'beneficiario'        => $request['nombre_proveedor'],
                'fecha_cheque'        => $request['fecha_cheque'],
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'check'               => $request['verificar_cheque'],
                'id_secuencia'        => $request['numero'],
                'id_pago'             => $request['formas_pago'],
                'id_caja_banco'       => $request['banco'],
                'no_cheque'           => $request['numero_cheque'],
                'fecha_comprobante'   => $request['fecha_hoy'],
                'secuencia'           => $numero_factura,
                'girado_a'            => $request['giradoa'],
                'id_empresa'          => $id_empresa,
                'valor_pago'          => $request['valor_cheque'],
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];
            $id_comprobante = Ct_Comprobante_Egreso_Masivo::insertGetId($input_comprobante);
            //dd($request->all());
            $primerarray = [];
            for ($i = 0; $i <= $request['contador']; $i++) {
                if (!is_null($request['abono' . $i])) {
                    if (($request['abono' . $i]) > 0) {
                        $nuevo_saldof       = $objeto_validar->set_round($request['abono' . $i]);
                        $consulta_proveedor = Proveedor::where('id', $request['proveedor' . $i])->first();
                        if (!is_null($consulta_proveedor)) {
                            $segundoarray = [$consulta_proveedor->id_cuentas, $request['abono' . $i]];
                            $key          = array_search($consulta_proveedor->id_cuentas, array_column($primerarray, '0'));
                            //dd($segundoarray);
                            if ($key !== false) {
                                $valor                = $primerarray[$key][1];
                                $valor                = $valor + $request['abono' . $i];
                                $primerarray[$key][0] = $consulta_proveedor->id_cuentas;
                                $primerarray[$key][1] = $valor;
                            } else {
                                array_push($primerarray, $segundoarray);
                            }
                        }
                        $cuenta_provloc = Ct_Configuraciones::obtener_cuenta('EGRESOMASIV_PROV_LOC');
                        //$desc_cuenta = Plan_Cuentas::where('id', '2.01.03.01.01')->first();
                        Ct_Asientos_Detalle::create([
                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $cuenta_provloc->cuenta_guardar,
                            'descripcion'         => $cuenta_provloc->nombre_mostrar,
                            'fecha'               => $request['fecha_hoy'],
                            'debe'                => $nuevo_saldof,
                            'haber'               => '0',
                            'estado'              => '1',
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,
                        ]);
                        $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->first();
                        Ct_Detalle_Comprobante_Egreso_Masivo::create([
                            'id_comprobante'  => $id_comprobante,
                            'observacion'     => $request['aaa'],
                            'id_compra'       => $consulta_compra->id,
                            'id_secuencia'    => $request['numero' . $i],
                            'id_proveedor'    => $request['proveedor' . $i],
                            'saldo_base'      => $request['saldo' . $i],
                            'abono'           => $request['abono' . $i],
                            'estado'          => '1',
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                            'id_usuariomod'   => $idusuario,
                        ]);
                    }
                    //dd($request['abono'.$i]);
                }
            }
            //dd($primerarray);
            for ($file = 0; $file < count($primerarray); $file++) {
                $cuent_descrip = Plan_Cuentas::where('id', $primerarray[$file][0])->first();
                //dd($cuent_descrip);
                $cuentapadre = $cuent_descrip;
                $cuenta      = $primerarray[$file][0];
                //$debe =   $arr_primario[$file][1];
                $debe = number_format($primerarray[$file][1], 2, '.', '');
                Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuent_descrip->id,
                    'descripcion'         => $cuent_descrip->nombre,
                    'fecha'               => $fechahoy,
                    'debe'                => $objeto_validar->set_round($debe),
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            $consulta_compra = null;
            $input_actualiza = null;
            for ($i = 0; $i <= $request['contador']; $i++) {
                if (!is_null($request['abono' . $i]) && $request['abono' . $i] > 0) {
                    $nuevo_saldo = 0;
                    //actualizar valor contable de cada tabla
                    $consulta_compra = Ct_compras::where('id', $request['id_actualiza' . $i])->first();
                    //dd($consulta_compra);
                    if ($consulta_compra != null || $consulta_compra != '[]') {
                        if ($request['abono' . $i] > 0) {
                            if ($request['abono' . $i] > ($consulta_compra->valor_contable)) {
                                $nuevo_saldo = $request['abono' . $i] - $consulta_compra->valor_contable;
                            } else {
                                $nuevo_saldo = $consulta_compra->valor_contable - $request['abono' . $i];
                            }
                            $nuevo_saldof    = $objeto_validar->set_round($nuevo_saldo);
                            $input_actualiza = null;
                            if ($nuevo_saldof != 0) {
                                $input_actualiza = [
                                    'estado'          => '2', //poner otro estado para que no salga en las consultas
                                    'valor_contable'  => $nuevo_saldof,
                                    'ip_modificacion' => $ip_cliente,
                                    'id_usuariomod'   => $idusuario,
                                ];
                            } else {
                                $input_actualiza = [
                                    'estado'          => '3', //poner otro estado para que no salga en las consultas
                                    'valor_contable'  => $nuevo_saldof,
                                    'ip_modificacion' => $ip_cliente,
                                    'id_usuariomod'   => $idusuario,
                                ];
                            }
                            $consulta_compra->update($input_actualiza);
                        }
                    }
                }
            }
            return $id_comprobante;
        } catch (Exception $e) {
            DB::rollBack();
            $arrayError = [
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ];
            echo '<pre>';
            print_r($arrayError);
            exit;
        }
    }
    public function buscar(Request $request)
    {
        //dd('entra');
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        if ($request->excel == 0) {
            $id_empresa  = $request->session()->get('id_empresa');
            $constraints = [
                'id'                  => $request['id'],
                'girado_a'            => $request['girado_a'],
                'descripcion'         => $request['descripcion'],
                'no_cheque'           => $request['cheque'],
                'fecha_cheque'        => $request['fecha'],
                'id_asiento_cabecera' => $request['id_asiento_cabecera'],

            ];

            $comprobante_egreso_m = $this->doSearchingQuery($constraints, $id_empresa);
            $empresa              = Empresa::where('id', $id_empresa)->first();
            return view('contable/comp_egreso_masivo/index', ['comprobante_egreso_m' => $comprobante_egreso_m, 'searchingVals' => $constraints, 'empresa' => $empresa]);
        }
        if ($request->excel == 1) {
            self::excel_comprobante_masivo($request);
        }
    }
    public function excel_comprobante_masivo(Request $request)
    {
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $query = Ct_Comprobante_Egreso_Masivo::query();
        if ($request->has('id') && $request->id !== null) {
            $query->where('id', $request->id);
        }
        if ($request->has('girado_a') && $request->girado_a !== null) {
            $query->where('girado_a', $request->girado_a);
        }
        if ($request->has('descripcion') && $request->descripcion !== null) {
            $query->where('descripcion', 'like', '%' . $request->descripcion . '%');
        }
        if ($request->has('fecha') && $request->fecha !== null) {
            $query->where('fecha_cheque', $request->fecha);
        }
        if ($request->has('cheque') && $request->cheque !== null) {
            $query->where('nro_cheque', $request->cheque);
        }
        if ($request->has('id_asiento_cabecera') && $request->id_asiento_cabecera !== null) {
            $query->where('id_asiento_cabecera', $request->id_asiento_cabecera);
        }
        $comprobante_egreso_m = $query->orderby('id', 'desc')->get();

        $titulos = array("Id", "Concepto", "Secuencia", "Nro. Asiento", "Fecha de Comprobante", "Fecha Cheque", "Nro_Cheque", "Girado a	", "Valor", "Creado por", "Estado");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        Excel::create('Comprobante de Egreso Masivo', function ($excel) use ($titulos, $posicion, $comprobante_egreso_m) {
            $excel->sheet('Comprobante de Egreso Masivo', function ($sheet) use ($titulos, $posicion, $comprobante_egreso_m) {
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Comprobante de Egreso Masivo');
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
                foreach ($comprobante_egreso_m as $detalle) {

                    $valor = round($detalle->valor_pago, 2);
                    $total += $detalle->valor_pago;
                    $valor = round($detalle->valor_pago, 2);
                    $total_formateado = round($total, 2);

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

                    $datos_excel = [$detalle->id, $detalle->descripcion, $detalle->secuencia, $detalle->id_asiento_cabecera, $detalle->fecha_comprobante, $detalle->fecha_cheque, $detalle->no_cheque, $detalle->girado_a, $valor, $Creado_por, $estado];

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            if ($i == 8) {
                                $cell->setAlignment('right');
                            } else {
                                $cell->setAlignment('center');
                            }
                        });
                    }
                    $comienzo++;
                }

                $sheet->cell('H' . $comienzo, function ($cell) {
                    $cell->setValue('VALOR TOTAL');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(18);
                    $cell->setAlignment('center');
                });

                if (isset($total_formateado)) {
                    $sheet->cell('I' . $comienzo, function ($cell) use ($total_formateado) {
                        $cell->setValue($total_formateado);
                        $cell->setAlignment('right');
                        $cell->setFontWeight('bold');
                    });
                }
                //dd($total_formateado);
                $sheet->setWidth('A', 10);
                $sheet->setWidth('B', 100);
                $sheet->setWidth('C', 15);
                $sheet->setWidth('D', 15);
                $sheet->setWidth('E', 20);
                $sheet->setWidth('F', 15);
                $sheet->setWidth('G', 15);
                $sheet->setWidth('H', 25);
                $sheet->setWidth('I', 15);
                $sheet->setWidth('J', 25);
                $sheet->setWidth('k', 15);
            });
        })->export('xlsx');
    }
    /*************************************************
     ******************CONSULTA QUERY******************
    /*************************************************/
    private function doSearchingQuery($constraints, $id_empresa)
    {

        $query  = Ct_Comprobante_Egreso_Masivo::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->where('id_empresa', $id_empresa)->orderBy('id', 'desc')->paginate(10);
    }
    public function pdf_egreso($id, Request $request)
    {

        //dd($asiento_detalle);
        $id_empresa                 = $request->session()->get('id_empresa');
        $comprobante_egreso_m       = Ct_Comprobante_Egreso_Masivo::where('id_empresa', $id_empresa)->where('id', $id)->first();
        $comprobante_egreso_detalle = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_comprobante', $id)->get();
        $total_abono                = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_comprobante', $id)->sum('abono');

        //dd($total_abono);

        $empresa          = Empresa::where('id', $comprobante_egreso_m->id_empresa)->first();
        $letras           = new Numeros_Letras();
        $total_str        = $letras->convertir(number_format($comprobante_egreso_m->valor_pago, 2, '.', ''), "DOLARES", "CTVS");
        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $comprobante_egreso_m->id_asiento_cabecera)->first();
        $compras          = Ct_compras::where('secuencia_f', $comprobante_egreso_m->id_secuencia)->first();
        $asiento_detalle  = Ct_Asientos_Detalle::where('estado', '1')->where('id_asiento_cabecera', $asiento_cabecera->id)->get();
        $vistaurl         = "contable.comp_egreso_masivo.pdf_egreso_masivo";
        $view             = \View::make($vistaurl, compact('comprobante_egreso_m', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle', 'comprobante_egreso_detalle'))->render();
        $pdf              = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('storage/app')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Comprobante de Egreso Masivo' . '.pdf');
    }
    public function reporte_compegresoma(Request $request, $id, $tipo)
    {
        $id_empresa = $request->session()->get('id_empresa');
        //dd($request->all());
        //$fecha_proc = date('d/m/Y');
        $empresa   = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();

        $reporte_datos = [];
        //$rol_form_pag = [];
        //dd($tipo);
        //dd($reporte_datos);
        if ($tipo == '1') {
            $reporte_datos = DB::table('ct_comprobante_egreso_masivo as ctcm')
                ->join('ct_detalle_comprobante_egreso_masivo as ctem', 'ctem.id_comprobante', 'ctcm.id')
                ->join('ct_tipo_pago as ctp', 'ctcm.id_pago', 'ctp.id')
                ->join('proveedor as p', 'ctem.id_proveedor', 'p.id')
                ->join('ct_configuracion_bancos as ctcb', 'ctcb.id', 'p.id_configuracion')
                ->groupBy('ctem.id_proveedor')
                ->where('ctcm.id', $id)
                ->select(DB::raw('SUM(ctem.abono) as suma_abono'), 'ctcm.*', 'ctem.id_proveedor', 'ctp.nombre as ntipoc', 'ctcm.*', 'ctcb.submotivo_pago')
                ->get();

            //dd($reporte_datos);
        }
        //dd($reporte_datos);
        Excel::create('ORDEN COMPROBANTE MASIVO', function ($excel) use ($empresa, $reporte_datos) {
            $excel->sheet('Comprobante Masivo', function ($sheet) use ($empresa, $reporte_datos) {

                //$fecha_d = date('Y/m/d');
                $i = 3;
                $j = 0;

                $sum_valor = 0;
                $cont_empl = 0;

                $sheet->mergeCells('A1:K1');

                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue('ORDEN COMPROBANTE MASIVO');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K1', function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('15');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Forma Pag/Cob');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Banco');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tip.Cta/Che');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Num.Cta/Che');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Valor');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Identificacion');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Tip.Doc.');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NUC');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Beneficiario');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Telefono');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Referencia');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A2:K2', function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                });
                // DETALLES
                $sheet->setColumnFormat(array(
                    'E' => '0.00',
                ));

                foreach ($reporte_datos as $value) {
                    //dd($value);
                    $txtcolor = '#000000';

                    $id_proveedor = Proveedor::find($value->id_proveedor);

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        $tipo_pago = '';
                        // CU:CUENTA,EF:EFECTIVO,CH:CHEQUE

                        if (is_null($value->id_pago)) {
                            $tipo_pago = "NO TIENE";
                        } else {
                            if ($value->tipo == '1') {
                                $tipo_pago = 'CU';
                            } elseif ($value->tipo == '2') {
                                $tipo_pago = 'EF';
                            } elseif ($value->tipo == '2') {
                                $tipo_pago = 'CH';
                            }
                        }

                        // manipulate the cel
                        $cell->setValue($tipo_pago);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->submotivo_pago);
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // dd($value->nombre);
                        $tip_cuent = '';

                        // Tipos de Cuenta
                        //10: AHORRO 00:CORRIENTE
                        // manipulate the cel
                        if ($id_proveedor->tipo_cuenta == 1) {

                            $cell->setValue('10');
                        } else {
                            $cell->setValue('00');
                        }
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($id_proveedor->cuenta);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->suma_abono);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(' ' . $id_proveedor->identificacion);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        //C: CEDULA, R:RUC, P:PASAPORTE, X:NINGUNO
                        if (strlen($id_proveedor->identificacion) == 10) {
                            $cell->setValue('C');
                        } elseif (strlen($id_proveedor->identificacion) > 10) {
                            $cell->setValue('R');
                        } else {
                            $cell->setValue('P');
                        }
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel

                        $cell->setValue(' ' . $id_proveedor->identificacion);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($id_proveedor->beneficiario);
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($id_proveedor, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($id_proveedor->telefono1);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->submotivo_pago == "30") {
                            $cell->setValue('PR');
                        } else {
                            $cell->setValue('RU');
                        }
                        $cell->setAlignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sum_valor = $sum_valor + $value->suma_abono;

                    $i         = $i + 1;
                    $cont_empl = $cont_empl + 1;
                }

                $j        = $i + 1;
                $k        = $j + 1;
                $l        = $k + 1;
                $txtcolor = '#000000';

                //Subtotales
                $sheet->cell('A' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('SUBTOTALES');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('D' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('E' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('F' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //Termina Sub Total
                //Total
                $sheet->cell('A' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('TOTAL');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('B' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('C' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('FORMA');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('C' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('D' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('CANT.');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });

                $sheet->cells('D' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('E' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('E' . $j, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('F' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $j, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //Fin TOtal
                //USD
                $sheet->cell('A' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('USD');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('CU');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('D' . $k, function ($cell) use ($cont_empl, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($cont_empl);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('E' . $k, function ($cell) use ($sum_valor, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_valor);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('F' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $k, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                //FIN USD
                //TOTAL GENERAL
                $sheet->cell('A' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('B' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('TOTAL GENERAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('C' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('DOLARES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('C' . $l, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('D' . $l, function ($cell) use ($cont_empl, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($cont_empl);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('D' . $l, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('E' . $l, function ($cell) use ($sum_valor, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_valor);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('E' . $l, function ($cells) {
                    $cells->setBackground('#4933FF');
                    $cells->setFontColor('#ffffff');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('F' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('G' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('H' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('I' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('J' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cell('K' . $l, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });

                //FIN TOTAL GENERAL

            });
        })->export('xlsx');
    }
    public function anular($id, Request $request)
    {
        DB::beginTransaction();
        try {
            //if (!is_null($id)) {
            $comp_ingreso = Ct_Comprobante_Egreso_Masivo::where('id', $id)->where('estado', '1')->first();
            $ip_cliente   = $_SERVER["REMOTE_ADDR"];
            $concepto     = $request['concepto'];
            $id_empresa   = $request->session()->get('id_empresa');
            $cab_asiento = Ct_Asientos_Cabecera::find($comp_ingreso->id_asiento_cabecera);
            //dd($cab_asiento);
            // $verifica_cierre = LogAsiento::verificar_cierre_anio($cab_asiento->fecha_asiento);
            // if ($verifica_cierre['status'] == 'error') {
            //     return $verifica_cierre;
            // }

            $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($cab_asiento['fecha_asiento']));
            if ($verifica_cierre->status == 'error') {
                return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
            }
        

            $idusuario = Auth::user()->id;
            if (!is_null($comp_ingreso)) {

                // ahora actualizo el valor y le sumo lo que ya le había restado
                //dd($comp_ingreso->detalle);  219.12
                if (!is_null($comp_ingreso->detalles)) {
                    foreach ($comp_ingreso->detalles as $value) {
                        $consulta_venta = Ct_compras::where('id', $value->id_compra)->where('estado', '>', '0')->where('id_empresa', $id_empresa)->first();

                        if (!is_null($consulta_venta)) {
                            $valor = $consulta_venta->valor_contable;
                            $suma = ($value->abono) + $valor;
                            $input_actualiza = [
                                'valor_contable'  => $suma,
                                'estado'          => '1',
                                'ip_modificacion' => $ip_cliente,
                                'id_usuariomod'   => $idusuario,
                            ];
                            $consulta_venta->update($input_actualiza);
                        }
                    }
                }
                $input = [
                    'estado'          => '0',
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                ];
                $comp_ingreso->update($input);
                $asiento = Ct_Asientos_Cabecera::findorfail($comp_ingreso->id_asiento_cabecera);
                $asiento->estado = 1;
                $asiento->id_usuariomod = $idusuario;
                $asiento->save();
                $detalles   = $asiento->detalles;
                $id_asiento = Ct_Asientos_Cabecera::insertGetId([
                    'observacion'     => strtoupper($concepto),
                    'fecha_asiento'   => $asiento->fecha_asiento,
                    'id_empresa'      => $id_empresa,
                    'fact_numero'     => $comp_ingreso->secuencia,
                    'valor'           => $asiento->valor,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'sucursal'         => $asiento->sucursal,
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
                LogAsiento::anulacion("EG-M", $id_asiento, $asiento->id);
                // Log_Contable::create([
                //     'tipo'           => 'EM',
                //     'valor_ant'      => $asiento->valor,
                //     'valor'          => $asiento->valor,
                //     'id_usuariocrea' => $idusuario,
                //     'id_usuariomod'  => $idusuario,
                //     'observacion'    => $asiento->concepto,
                //     'id_ant'         => $asiento->id,
                //     'id_referencia'  => $id_asiento,
                // ]);

                //return redirect()->route('acreedores_cegreso');
            }
            // } else {
            //     return 'error';
            // }
            DB::commit();
            return ["status" => "success", "msj" => "Eliminado con exito"];
        } catch (\Exception $e) {
            DB::rollback();
            return ["status" => "error", "msj" => "ocurrio un error", "exp" => $e->getMessage()];
        }
    }
    public function repararEgresoMasivo($id_empresa)
    {
        $compras = Ct_Compras::where('id_empresa', $id_empresa)->orderBy('id', 'Desc')->get();

        foreach ($compras as $c) {
            $id = $c->id;
            //egresos
            $v_egreso = 0; //---
            $egreso = Ct_Detalle_Comprobante_Egreso::where('id_compra', $id)->get();
            foreach ($egreso as $x) {
                if ($x->comp_egreso->estado == 1) {
                    $v_egreso += $x->abono;
                }
            }

            //cruce
            $v_cruce = 0; //---
            //dd($id);
            if ($id != "") {
                $cruce = Ct_Detalle_Cruce::where('id_factura', $id)->get();
                foreach ($cruce as $x) {
                    if ($x->cabecera->estado == 1) {
                        $v_cruce += $x->total;
                    }
                }
            }


            //bancario
            $v_bancario = 0; //---
            if ($id != "") {
                $debito = Ct_Debito_Bancario_Detalle::where('id_compra', $id)->get();
                foreach ($debito as $x) {
                    if ($x->cabecera->estado == 1) {
                        $v_bancario += $x->abono;
                    }
                }
            }
            //retenciones
            $v_retencion = 0; //---
            if ($id != "") {
                $retenciones = Ct_Retenciones::where('id_compra', $id)->where('estado', '1')->first();
                if (!is_null($retenciones)) {
                    $v_retencion = $retenciones->valor_fuente + $retenciones->valor_iva;
                }
            }

            $v_credito = 0; //---
            if ($id != "") {
                $credito = Ct_Credito_Acreedores::where('id_compra', $id)->where('estado', '1')->get();
                if (!is_null($credito)) {
                    foreach ($credito as $x) {
                        $v_credito += $x->valor_contable;
                    }
                }
            }

            $v_debito = 0; //---
            if ($id != "") {
                $debito = Ct_Detalle_Debito_Acreedores::where('id_factura', $id)->where('estado', '1')->get();
                foreach ($debito as $x) {
                    if ($x->cabecera->estado == 1) {
                        $v_debito += $x->total;
                    }
                }
            }

            $v_cruce_cuentas = 0; //---
            $cruce = Ct_Cruce_Cuentas::where('id_factura', $id)->where('estado', '1')->get();
            if (!is_null($cruce)) {
                foreach ($cruce as $x) {
                    $v_cruce += $x->total;
                }
            }

            $v_egreso_masivo = 0; //---
            if ($id != "") {
                $egreso = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_compra', $id)->get();
                foreach ($egreso as $x) {
                    if ($x->comp_egreso->estado == 1) {
                        $v_egreso_masivo += $x->abono;
                    }
                }
            }
            //dd("aqi",$v_cruce);
            $v_total = $v_egreso + $v_cruce + $v_bancario + $v_retencion + $v_credito + $v_debito + $v_cruce_cuentas + $v_egreso_masivo;
            if ($c->total_final >= $v_total) {
                $saldo =  $c->total_final - $v_total;
                echo "<p>Compra:{$c->id} [{$c->total_final}] -> retencion:{$v_retencion} -> cruce:{$v_cruce} -> bancario:{$v_bancario} -> credito:{$v_credito} -> debitoo: {$v_debito} -> cruce cuentas:{$v_cruce_cuentas} -> egreso_masivo {$v_egreso_masivo} -> total:{$v_total} -> saldo:{$saldo}<br> </p>";
            }
        }
    }

    // public function envio_correo_pro(Request $request)
    // {
    //     try{
    //     $correoEnvio = '';

    //     $comprobante_egreso_m  = Ct_Comprobante_Egreso_Masivo::where('id', $request['id'])->first();
    //     $detalle_comprobante_m = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_comprobante', $comprobante_egreso_m->id)->get();
    //     for ($i = 0; $i < count($detalle_comprobante_m); $i++) {
    //         $empresa = Empresa::where('id', $comprobante_egreso_m->id_empresa)->first();
    //         $correo  = Proveedor::where('id', $detalle_comprobante_m[$i]->id_proveedor)->first();
    //         $array   = ['nombre' => $correo->nombrecomercial, 'abono' => $detalle_comprobante_m[$i]->abono, 'logo' => $empresa->nombrecomercial, 'fecha' => $detalle_comprobante_m[$i]->created_at, 'num_factura' => $detalle_comprobante_m[$i]->compras->numero];
    //         if (!is_null($correo->email)) {
    //             $correoEnvio = $correo->email;
    //         } else {
    //             $correoEnvio = $correo->email2;
    //         }

    //         Mail::send('contable.comp_egreso_masivo.emision_varios', $array, function ($msj) use ($correoEnvio) {
    //             $msj->subject('Detalle Proovedor');
    //                 $msj->to($correoEnvio);
    //                 $msj->from('no-reply@mdconsgroup.com');
    //                 $msj->bcc('torbi10@hotmail.com', 'llozano@accountingsolutions.com.ec', 'fortiz@mdconsgroup.com', 'walarcon95@hotmail.com');
    //         });


    //     }
    //         return ['status'=>true,'msj'=>'Enviando Correctamente'];
    //     }catch(\Exception $e){

    //         return ['status'=>false,'msj'=>'Enviando Correctamente'];
    //     }
    // }


    public function envio_correo_pro(Request $request)
    {
        try {
            $correoEnvio = '';
            $id_empresa = $request->session()->get('id_empresa');
            $empresa_nombre = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
            $comprobante_egreso_m  = Ct_Comprobante_Egreso_Masivo::leftjoin('ct_tipo_pago as tp','ct_comprobante_egreso_masivo.id_pago','tp.id')->where('ct_comprobante_egreso_masivo.id', $request['id'])->first();
            $detalle_comprobante_m = Ct_Detalle_Comprobante_Egreso_Masivo::where('id_comprobante', $comprobante_egreso_m->id)->get();
            for ($i = 0; $i < count($detalle_comprobante_m); $i++) {
                $empresa = Empresa::where('id', $comprobante_egreso_m->id_empresa)->first();
                $correo  = Proveedor::where('id', $detalle_comprobante_m[$i]->id_proveedor)->first();
                $array   = [
                    'abono' => $detalle_comprobante_m[$i]->abono, 'fecha' => $detalle_comprobante_m[$i]->created_at, 'concepto' => $detalle_comprobante_m[$i]->observacion,
                    'secuencia' => $detalle_comprobante_m[$i]->id_secuencia, 'saldo' => $detalle_comprobante_m[$i]->saldo_base,
                    'formaPago'=>$comprobante_egreso_m->nombre,
                ];
                if (!is_null($correo->email)) {
                    $correoEnvio = $correo->email;
                } else {
                    $correoEnvio = $correo->email2;
                }
                Mail::send('contable.comp_egreso_masivo.emision_varios', $array, function ($msj) use ($empresa_nombre, $correoEnvio) {
                    $msj->subject('Notificación de pago: ' . $empresa_nombre->razonsocial);
                    $msj->to($correoEnvio);
                    $msj->from('no-reply@mdconsgroup.com', "Notificación de Pago - $empresa_nombre->razonsocial");
                    $msj->bcc('torbi10@hotmail.com','fortiz@mdconsgroup.com','alexo8ec@hotmail.com','notificaciones.gruporobles2023@gmail.com');
                });
            }
            return ['status' => true, 'msj' => 'Correo enviando a la cola de correos'];
        } catch (\Exception $e) {
            return ['status' => false, 'msj' => $e->getMessage()];
        }
    }
    public function buscadorDetalles(Request $request)
    {
        $table = "";
        $id_empresa = Session::get('id_empresa');
        $busq_acreedor  = null;
        $busq_factura   = null;
        $busq_ruc       = null;
        if (!is_null($request->buscador)) {
            $nombre_prov = [];
            $proveedor = Proveedor::where('razonsocial', 'like', "%{$request->buscador}%")->get();
            foreach ($proveedor as $prov) {
                array_push($nombre_prov, $prov->id);
            }

            $busq_acreedor = Ct_compras::where('estado', '!=', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->whereIn('proveedor', $nombre_prov);
            $busq_ruc = Ct_compras::where('estado', '!=', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->where('proveedor', 'like', "%{$request->buscador}%");
            $busq_factura = Ct_compras::where('estado', '!=', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->where('numero', 'like', "%{$request->buscador}%");
            $compras = $busq_ruc->union($busq_factura)->union($busq_acreedor)->orderBy('f_autorizacion', 'desc')->get();
        }
        $tipo1 = 'COM-FA';
        foreach ($compras as $value) {
            $tipo1 = 'COM-FACT';
            if ($value->tipo == 1) {
                $tipo1 = 'COM-FA';
            }
            $a_href = url('contable/compras/editar') . "/" . $value->id;
            $table .= "
            <tr id='trlinea_$value->id'>
                <td style='text-align: center;'>
                    <a class='label label-info url-compras' target='_blank' href='{$a_href}'>{$value->id}</a>
                    <input type='hidden' name='' value='{$value->id}'>
                </td>
                <td> <input class='form-control' id='proveedor_{$value->id}' value='{$value->proveedorf->razonsocial}'></td>
                <td> <input class='form-control' id='fecha_{$value->id}' type='text' value='{$value->f_caducidad}' readonly> </td>
                <td> <input class='form-control' type='text' id='tipo_{$value->id}' value='{$tipo1}' readonly> </td>
                <td> <input class='form-control' id='factura_{$value->id}' type='text' value='Fact #: {$value->numero} - Prov: {$value->proveedor} {$value->observacion}' readonly> </td>
                <td> <input class='form-control' style='background-color: #c9ffe5;' type='text' value='$' readonly> </td>
                <td> <input class='form-control' style='background-color: #c9ffe5; width: 100%' type='text' id='saldo_{$value->id}' value='{$value->valor_contable}' disabled> </td>
                <td style='text-align: center;'><button style='padding: 1px 20px;border-radius: 16px;' type='button' onclick='agregarFila({$value->id})' class='btn btn-primary'>Agregar</button> </td>
            </tr>";
        }

        if ($table != "") {
            return ["status" => "success", "table" => $table];
        } else {
            return ["status" => "error", "table" => $table];
        }
    }
    public function newCreate2(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();
        //dd($min_date);
        $id_empresa    = $request->session()->get('id_empresa');
        $divisas       = Ct_divisas::where('estado', '1')->get();
        $banco         = DB::table('ct_caja_banco')->where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $tipo_pago     = Ct_Tipo_Pago::where('estado', '1')->get();
        $formas_pago   = DB::table('ct_tipo_pago')->where('estado', '1')->get();
        $user_vendedor = User::where('id_tipo_usuario', 17)
            ->where('estado', 1)->get();
        $sucursales = Ct_Sucursales::where('estado', 1)
            ->where('id_empresa', $id_empresa)
            ->get();
        $tipos   = Ct_Tipo_Tarjeta::where('estado', '1')->get();
        $empresa = Empresa::where('id', $id_empresa)->first();
        $compras = Ct_compras::where('estado', '!=', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->orderBy('f_autorizacion', 'desc')->take(400)->get();
        return view('contable/comp_egreso_masivo/new_create2', ['min_date' => $min_date, 'divisas' => $divisas, 'tipos_pagos' => $tipo_pago, 'tipos' => $tipos, 'bancos' => $banco, 'user_vendedor' => $user_vendedor, 'empresa' => $empresa, 'compras' => $compras, 'formas_pago' => $formas_pago, 'sucursales' => $sucursales]);
    }
    public function newStore2(Request $request)
    {
        $id_empresa        = $request->session()->get('id_empresa');
        $fechahoy          = $request['fecha_hoy'];
        $ip_cliente        = $_SERVER["REMOTE_ADDR"];
        $idusuario         = Auth::user()->id;
        DB::beginTransaction();

        try {
            $banco             = $request['banco'];
            if (is_null($banco)) {
                DB::rollback();
                return ["status" => "error", "msj" => "Debe Seleccionar el banco", "exp" => ""];
            }

            if ($request["formas_pago"] == 2) {
                if (is_null($request["numero_cheque"]) or trim($request["numero_cheque"]) == "") {
                    DB::rollback();
                    return ["status" => "error", "msj" => "Selecciono forma de pago cheque, debe llenar el campo de <b>Cheque No</b>", "exp" => ""];
                }
            }

            $numero_factura    = LogAsiento::getSecuencia(8);
            $input_cabecera = [
                'observacion'     => $request['concepto'],
                'fecha_asiento'   => $request['fecha_hoy'],
                'fact_numero'     => $numero_factura,
                'valor'           => $request['valor_cheque'],
                'id_empresa'      => $id_empresa,
                'estado'          => '1',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
            $consulta_db_cajab = Ct_Caja_Banco::find($banco);

            if (is_null($consulta_db_cajab->cuenta_mayor)) {
                DB::rollback();
                return ["status" => "error", "msj" => "No tiene configurada la cuenta del banco, configure y luego continue", "exp" => ""];
            }

            $plan_empresa = Plan_Cuentas_Empresa::where('id_empresa', $id_empresa)->where('id_plan', $consulta_db_cajab->cuenta_mayor)->first();

            if (is_null($plan_empresa)) {
                DB::rollback();
                return ["status" => "error", "msj" => "No tiene la cuenta del banco creada en la empresa"];
            }
            Ct_Asientos_Detalle::create([
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'id_plan_cuenta'      => $plan_empresa->id_plan,
                'descripcion'         => $plan_empresa->nombre,
                'fecha'               => $request['fecha_hoy'],
                'haber'               => $request['abono_total'],
                'debe'                => '0',
                'estado'              => '1',
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ]);


            $input_comprobante = [
                'descripcion'         => $request['concepto'],
                'estado'              => '1',
                'beneficiario'        => $request['nombre_proveedor'],
                'fecha_cheque'        => $request['fecha_cheque'],
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'check'               => isset($request['cheque_entregado']) ? 1 : 0,
                'id_secuencia'        => $request['numero'],
                'id_pago'             => $request['formas_pago'],
                'id_caja_banco'       => $request['banco'],
                'no_cheque'           => $request['numero_cheque'],
                'fecha_comprobante'   => $request['fecha_hoy'],
                'secuencia'           => $numero_factura,
                'girado_a'            => $request['giradoa'],
                'id_empresa'          => $id_empresa,
                'valor_pago'          => $request['valor_cheque'],
                'fecha_entregado'     => isset($request['cheque_entregado']) ? date("Y-m-d") : null,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
            ];

            $id_comprobante = Ct_Comprobante_Egreso_Masivo::insertGetId($input_comprobante);
            for ($i = 0; $i < count($request['id_compra']); $i++) {
                $compras = Ct_Compras::find($request['id_compra'][$i]);

                if ($request["abono"][$i] > $compras->valor_contable) {
                    DB::rollback();
                    return ["status" => "error", "msj" => "El abono para la factura <b>ID: {$compras->id} | {$compras->numero}</b>, es mayor al Saldo ({$compras->valor_contable})", "exp" => ""];
                }

                if (!isset($compras->proveedorf)) {
                    DB::rollback();
                    return ["status" => "error", "msj" => "No tiene proveedor la factura <b>ID: {$compras->id} | {$compras->numero}</b>", "exp" => ""];
                }
                $cuenta_proveedor = '';
                $nombre_cuenta_proveedor = '';
                $plan_empresa = Plan_Cuentas_Empresa::where('id_plan', $compras->proveedorf->id_cuentas)->where('id_empresa', $id_empresa)->first();

                if (is_null($plan_empresa)) {
                    $id_plan_config = LogConfig::busqueda('2.01.03.01.01');
                    $cuenta_prov = Plan_Cuentas_Empresa::where('id_plan', $id_plan_config)->where('id_empresa', $id_empresa)->first();
                    if (!is_null($cuenta_prov)) {
                        $cuenta_proveedor = $cuenta_prov->id_plan;
                        $nombre_cuenta_proveedor = $cuenta_prov->nombre;
                    }
                } else {
                    $cuenta_proveedor = $plan_empresa->id_plan;
                    $nombre_cuenta_proveedor = $plan_empresa->nombre;
                }

                if ($cuenta_proveedor == '') {
                    DB::rollback();
                    return ["status" => "error", "msj" => "No tiene configurada la cuenta del proveedor <b>ID: {$compras->id} | {$compras->numero}</b>", "exp" => ""];
                }

                Ct_Detalle_Comprobante_Egreso_Masivo::create([
                    'id_comprobante'  => $id_comprobante,
                    'observacion'     => $request['concepto'],
                    'id_compra'       => $compras->id,
                    'id_secuencia'    => $compras->numero,
                    'id_proveedor'    => $compras->proveedorf->id,
                    'saldo_base'      => $compras->valor_contable,
                    'abono'           => $request['abono'][$i],
                    'estado'          => '1',
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuenta_proveedor,
                    'descripcion'         => $nombre_cuenta_proveedor,
                    'fecha'               => $fechahoy,
                    'debe'                => $request["abono"][$i],
                    'haber'               => '0',
                    'estado'              => '1',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);


                $nuevo_saldof = $compras->valor_contable - $request["abono"][$i];

                if ($nuevo_saldof != 0) {
                    $compras->estado          = '2'; //poner otro estado para que no salga en las consulta;
                } else {
                    $compras->estado          = '3'; //poner otro estado para que no salga en las consulta;
                }

                $compras->valor_contable  = $nuevo_saldof;
                $compras->ip_modificacion = $ip_cliente;
                $compras->id_usuariomod   = $idusuario;
                $compras->save();
            }
            DB::commit();
            $datos = ["id_asiento" => $id_asiento_cabecera, "numero" => $numero_factura, "id_comprobante" => $id_comprobante];
            return ["status" => "success", "msj" => "Guardado Correctamente", "datos" => $datos];
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e);
            return ['status' => "error", "msj" => "Ocurrio un error", "exp" => $e->getMessage(), "line" => $e->getLine()];
        }
    }
    public function buscadorDetalles_solicitud(Request $request)
    {
        $table = "";
        $id_empresa = Session::get('id_empresa');
        $busq_acreedor  = null;
        $busq_factura   = null;
        $busq_ruc       = null;
        if (!is_null($request->buscador)) {
            $nombre_prov = [];
            $proveedor = Proveedor::where('razonsocial', 'like', "%{$request->buscador}%")->get();
            foreach ($proveedor as $prov) {
                array_push($nombre_prov, $prov->id);
            }

            $busq_acreedor = Ct_compras::where('estado', '!=', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->whereIn('proveedor', $nombre_prov);
            $busq_ruc = Ct_compras::where('estado', '!=', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->where('proveedor', 'like', "%{$request->buscador}%");
            $busq_factura = Ct_compras::where('estado', '!=', '0')->where('valor_contable', '>', '0')->where('id_empresa', $id_empresa)->where('numero', 'like', "%{$request->buscador}%");
            $compras = $busq_ruc->union($busq_factura)->union($busq_acreedor)->orderBy('f_autorizacion', 'desc')->get();
        }
        $tipo1 = 'COM-FA';
        foreach ($compras as $value) {
            $retenciones = '';
            $retencion  = Ct_Retenciones::where('estado', 1)->where('id_compra', $value->id)->first();
            if (!is_null($retencion)) {
                $retenciones = "Si";
            } else {
                $retenciones = "No";
            }
            $tipo1 = 'COM-FACT';
            if ($value->tipo == 1) {
                $tipo1 = 'COM-FA';
            }
            $a_href = url('contable/compras/editar') . "/" . $value->id;
            $table .= "
            <tr>
                <td> <input class='form-control' id='fecha_{$value->id}' type='text' value='{$value->f_caducidad}' readonly> </td>
                <td> <input class='form-control' type='text' id='tipo_{$value->id}' value='{$tipo1}' readonly> </td>
                <td> <input class='form-control' onmouseover='bigImg(this)' onmouseout='normalImg(this)' id='proveedor_{$value->id}' value='{$value->proveedorf->razonsocial}'></td>
                <td> <input class='form-control' id='numero_{$value->id}' type='text' value='{$value->numero}' readonly> </td>
                <td> <input class='form-control' onmouseover='bigImg(this)' onmouseout='normalImg(this)' id='factura_{$value->id}' type='text' value='Fact #: {$value->numero} - Prov: {$value->proveedor} {$value->observacion}' readonly=''> </td>
                <td> <input class='form-control' id='retencion_{$value->id}' type='text' value='{$retenciones}' readonly> </td>
                <td> <input class='form-control' style='background-color: #c9ffe5;' type='text' value='$' readonly> </td>
                <td> <input class='form-control' style='background-color: #c9ffe5; width: 100%' type='text' id='saldo_{$value->id}' value='{$value->valor_contable}' disabled> </td>
                <td style='text-align: center;'><button style='padding: 1px 20px;border-radius: 16px;' type='button' onclick='agregarFila({$value->id})' class='btn btn-primary'>Agregar</button> </td>
            </tr>";
        }
        if ($table != "") {
            return ["status" => "success", "table" => $table];
        } else {
            return ["status" => "error", "table" => $table];
        }
    }
}
