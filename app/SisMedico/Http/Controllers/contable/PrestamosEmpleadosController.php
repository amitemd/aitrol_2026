<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Tipo_Rol;
use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Rh_Prestamos;
use Sis_medico\Ct_Nomina;
use Sis_medico\Empresa;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Numeros_Letras;
use Sis_medico\User;
use Sis_medico\Ct_Rol_Pagos;
use Excel;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Prestamos_Utilidades;
use Sis_medico\Ct_Rh_Saldos_Iniciales;

use Sis_medico\LogAsiento;
use Sis_medico\Nota_Debito;
use Sis_medico\Nota_Debito_Detalle;
use Sis_medico\Ct_Cierre_Cabecera;

class PrestamosEmpleadosController extends Controller
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


    /************************************************
     *********PRESTAMOS A EMPLEADOS INDEX*************
    /************************************************/
    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        //$principales = Ct_Rh_Prestamos::where('estado', '1')->where('id_empresa', $id_empresa)->orderby('id', 'desc')->get();
        //dd($principales);
        $principales = Ct_Rh_Prestamos::where('ct_rh_prestamos.estado', '1')
            ->where('id_empresa', $id_empresa)
            ->join('users as u', 'u.id', 'ct_rh_prestamos.id_empl')
            ->select('u.*', 'ct_rh_prestamos.*')
            ->orderby('u.apellido1', 'asc')->get();
        //$empresas = Empresa::all();
        //dd($principales);

        return view('contable.rh_prestamos_empleados.index', ['registros' => $principales, 'empresa' => $empresa]);
    }


    /*************************************************
     **********CREAR PRESTAMOS EMPLEADOS***************
    /*************************************************/
    public function crear(Request $request, $id_nomina, $cedula)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $nombre_completo = '';
        $inf_usuario = User::where('id', $cedula)->first();

        if (!is_null($inf_usuario)) {
            $nombre_completo = $inf_usuario->nombre1 . " " . $inf_usuario->nombre2 . " " . $inf_usuario->apellido1 . " " . $inf_usuario->apellido2;
        }

        $id_i = $cedula;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empl_nomina = Ct_Nomina::findorfail($id_nomina);

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $lista_banco = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->whereNotNull('cuenta_mayor')->get();

        /*$cargos = DB::table('ct_nomina')
            ->join('users', 'users.id', '=', 'ct_nomina.id_user')
            ->where('ct_nomina.estado',1)
            ->select('ct_nomina.*')
            ->first();*/
        $min_date = Ct_Cierre_Cabecera::ultima_fecha();

        return view('contable.rh_prestamos_empleados.modal_prestamos', ['min_date'=>$min_date,'cargos' => $empl_nomina->cargo, 'id_i' => $id_i, 'nombre_completo' => $nombre_completo, 'tipo_pago_rol' => $tipo_pago_rol, 'lista_banco' => $lista_banco, 'bancos' => $bancos, 'empl_nomina' => $empl_nomina, 'sueldo_neto' => $empl_nomina->sueldo_neto]);
    }

    /*************************************************
     ***************GUARDA PRESTAMO EMPLEADO***********    
    /*************************************************/

    public function store_prestamos_empl(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $contador_ctpres = DB::table('ct_rh_prestamos')->where('id_empresa', $id_empresa)->get()->count();
        $numero_prestamo = 0;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $datos = $request['id_empl'];
        $val_prestamo = $request['monto_prestamo'];
        $fech_creacion = $request['fecha_creacion'];

        $anio_1mes_cobro = $request['anio_pmes_cobro'];
        $mes_inicio_cobro = $request['pmes_cobro'];

        $anio_fin_cobro = $request['anio_fcobro'];
        $mes_fin_cobro = $request['mes_fcobro'];

        //$mes = $request['mes'];
        $cuotas = $request['cuotas'];
        $val_cuotas = $request['valor_cuotas'];
        $cuent_sal = $request['cuenta_saliente'];

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        //Obtenemos el Mes de Inicio
        $txt_mes_inicio = '';
        if ($mes_inicio_cobro == '12') {
            $txt_mes_inicio = 'DICIEMBRE';
        } elseif ($mes_inicio_cobro == '11') {
            $txt_mes_inicio = 'NOVIEMBRE';
        } elseif ($mes_inicio_cobro == '10') {
            $txt_mes_inicio = 'OCTUBRE';
        } elseif ($mes_inicio_cobro == '9') {
            $txt_mes_inicio = 'SEPTIEMBRE';
        } elseif ($mes_inicio_cobro == '8') {
            $txt_mes_inicio = 'AGOSTO';
        } elseif ($mes_inicio_cobro == '7') {
            $txt_mes_inicio = 'JULIO';
        } elseif ($mes_inicio_cobro == '6') {
            $txt_mes_inicio = 'JUNIO';
        } elseif ($mes_inicio_cobro == '5') {
            $txt_mes_inicio = 'MAYO';
        } elseif ($mes_inicio_cobro == '4') {
            $txt_mes_inicio = 'ABRIL';
        } elseif ($mes_inicio_cobro == '3') {
            $txt_mes_inicio = 'MARZO';
        } elseif ($mes_inicio_cobro == '2') {
            $txt_mes_inicio = 'FEBRERO';
        } elseif ($mes_inicio_cobro == '1') {
            $txt_mes_inicio = 'ENERO';
        }

        //Obtenemos el Mes Fin
        $txt_mes_fin = '';
        if ($mes_fin_cobro == '12') {
            $txt_mes_fin = 'DICIEMBRE';
        } elseif ($mes_fin_cobro == '11') {
            $txt_mes_fin = 'NOVIEMBRE';
        } elseif ($mes_fin_cobro == '10') {
            $txt_mes_fin = 'OCTUBRE';
        } elseif ($mes_fin_cobro == '9') {
            $txt_mes_fin = 'SEPTIEMBRE';
        } elseif ($mes_fin_cobro == '8') {
            $txt_mes_fin = 'AGOSTO';
        } elseif ($mes_fin_cobro == '7') {
            $txt_mes_fin = 'JULIO';
        } elseif ($mes_fin_cobro == '6') {
            $txt_mes_fin = 'JUNIO';
        } elseif ($mes_fin_cobro == '5') {
            $txt_mes_fin = 'MAYO';
        } elseif ($mes_fin_cobro == '4') {
            $txt_mes_fin = 'ABRIL';
        } elseif ($mes_fin_cobro == '3') {
            $txt_mes_fin = 'MARZO';
        } elseif ($mes_fin_cobro == '2') {
            $txt_mes_fin = 'FEBRERO';
        } elseif ($mes_fin_cobro == '1') {
            $txt_mes_fin = 'ENERO';
        }

        if ($contador_ctpres == 0) {
            $num = '1';
            $numero_prestamo = str_pad($num, 9, "0", STR_PAD_LEFT);
        } else {

            $max_id = DB::table('ct_rh_prestamos')->where('id_empresa', $id_empresa)->latest()->first();
            $max_id = intval($max_id->secuencia);
            if (strlen($max_id) < 10) {
                $nu = $max_id + 1;
                $numero_prestamo = str_pad($nu, 10, "0", STR_PAD_LEFT);
            }
        }

        /*$verifica_cierre = LogAsiento::verificar_cierre_anio($request->fecha_creacion);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }*/
        // Verificar el cierre
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($fech_creacion));
        //dd($verifica_cierre);
        if ($verifica_cierre->status == 'error') {
            return ['respuesta' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }

        DB::beginTransaction();
        try {

            $text  = 'Prestamo a Empresa' . ':' . ' ' . 'Año Inicio Cobro' . ':' . $anio_1mes_cobro . ' ' . 'Mes Inicio Cobro' . ':' . $txt_mes_inicio . ' ' . 'Año Fin Cobro' . ':' . $anio_fin_cobro . ' ' . 'Mes Fin Cobro' . ':' . $txt_mes_fin . ' ' . 'Valor' . ':' . $val_prestamo;

            /************************************
             *****Inserta Ct_Asientos_Cabecera****
        /************************************/
            $input_cabecera = [
                'observacion'     => 'PRESTAMO EMPLEADO:' . $numero_prestamo . ' POR LA CANTIDAD DE ' . $request['monto_prestamo'],
                'fecha_asiento'   => $fech_creacion,
                'fact_numero'     => $numero_prestamo,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $val_prestamo,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $input_prestamo = [

                'id_empl' => $request['id_empl'],
                'nombres' => $request['nombres'],
                'id_empresa' => $request['id_empresa'],
                'monto_prestamo' => $request['monto_prestamo'],
                'fecha_creacion' => $request['fecha_creacion'],
                'tipo_rol' => $request['tipo_rol'],
                'num_cuotas' => $request['cuotas'],
                'valor_cuota' => $request['valor_cuotas'],
                'mes_inicio_cobro' => $request['pmes_cobro'],
                'anio_inicio_cobro' => $request['anio_pmes_cobro'],
                'mes_fin_cobro' => $request['mes_fcobro'],
                'anio_fin_cobro' => $request['anio_fcobro'],
                'id_tipo_pago' => $request['tipo_pago'],
                'concepto' => $request['concepto'],
                'num_cuenta_benef' => $request['numero_cuenta'],
                'banco_beneficiario' => $request['banco'],
                'cuenta_saliente' => $request['cuenta_saliente'],
                'num_cheque' => $request['numero_cheque'],
                'fecha_cheque' => $request['fecha_cheque'],
                'id_asiento_cabecera' => $id_asiento_cabecera,
                'secuencia'       => $numero_prestamo,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'saldo_total'   => $request['monto_prestamo'],

            ];


            /************************************
             *****Inserta Ct_Asientos_Detalle*****
        /************************************/

            if ($val_prestamo > 0) {

                $plan_cuentas = Plan_Cuentas::where('id', $cuent_sal)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                    'id_plan_cuenta'                => $cuent_sal,
                    'descripcion'                   => $plan_cuentas->nombre,
                    'fecha'                         => $fech_creacion,
                    'debe'                          => '0',
                    'haber'                         => $val_prestamo,
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,

                ]);
            }

            if ($val_prestamo > 0) {

                //1.01.02.03.02 PRESTAMOS_EMPLEADOS
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.03')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera'           => $id_asiento_cabecera,
                    //'id_plan_cuenta'                => '1.01.02.06.03',
                    'id_plan_cuenta'                => $plan_cuentas->cuenta_guardar,
                    'descripcion'                   => $plan_cuentas->nombre_mostrar,
                    'fecha'                         => $fech_creacion,
                    'debe'                          => $val_prestamo,
                    'haber'                         => '0',
                    'id_usuariocrea'                => $idusuario,
                    'id_usuariomod'                 => $idusuario,
                    'ip_creacion'                   => $ip_cliente,
                    'ip_modificacion'               => $ip_cliente,

                ]);

                $caja_banco = Ct_Caja_Banco::where('id_empresa', $id_empresa)->where('cuenta_mayor', $request['cuenta_saliente'])->where('estado', 1)->first();

                if ($request['tipo_pago'] == '1') { //DEBITO
                    $nota_debito         = [
                        'concepto'        => $request['concepto'],
                        'fecha'           => $request['fecha_creacion'],
                        'valor'           => $request['monto_prestamo'],
                        'empresa'         => $id_empresa,
                        'tipo'            => "BAN-ND",
                        'id_asiento'      => $id_asiento_cabecera,
                        'id_banco'        => $caja_banco->id,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'modulo'          => 'PRESTAMOS EMPLEADOS',
                    ];
                    $id_nota = Nota_Debito::insertGetId($nota_debito);

                    $nota_deb_detalle = [
                        'id_nota_debito'  => $id_nota,
                        'codigo'          => $plan_cuentas->cuenta_guardar,
                        'cuenta'          => $plan_cuentas->nombre_mostrar,
                        'debe'            => $request['monto_prestamo'],
                        'haber'           => number_format(0, 2),
                        'valor_base'      => $request['monto_prestamo'], //number_format($valor['debe'], 2),
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                    ];
                    Nota_Debito_Detalle::create($nota_deb_detalle);

                    $input_prestamo['id_documento'] = $id_nota;
                    $input_prestamo['tipo_documento'] = 'BAN-ND';
                    // $prestamo->update([
                    //     'id_documento' => $id_nota,
                    //     'tipo_documento' => 'BAN-ND'
                    // ]);
                } else { //COMPROBANTE DE EGRESO
                    $numero_factura = LogAsiento::getSecuencia(2);

                    $input_comprobante = [
                        'descripcion'           => $request['concepto'],
                        'estado'                => '1',
                        'id_asiento_cabecera'   => $id_asiento_cabecera,
                        'id_secuencia'          => 'null',
                        'nota'                  => $request['concepto'],
                        'fecha_comprobante'     => $request['fecha_creacion'],
                        'beneficiario'          => $request['nombres'],
                        'check'                 => 0,
                        'girado'                => $request['nombres'],
                        'id_caja_banco'         => $caja_banco->id,
                        'nro_cheque'            => $request['numero_cheque'],
                        'valor'                 => $request['monto_prestamo'],
                        'fecha_cheque'          => $request['fecha_cheque'],
                        'secuencia'             => $numero_factura,
                        'id_empresa'            => $id_empresa,
                        'id_usuariocrea'        => $idusuario,
                        'id_usuariomod'         => $idusuario,
                        'ip_creacion'           => $ip_cliente,
                        'ip_modificacion'       => $ip_cliente,
                        'modulo'                => 'PRESTAMOS EMPLEADOS',
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);
                    Ct_Detalle_Comprobante_Egreso_Varios::create([
                        'id_comprobante_varios'          => $id_comprobante,
                        'codigo'                         => $plan_cuentas->cuenta_guardar,
                        'cuenta'                         => $plan_cuentas->nombre_mostrar,
                        'descripcion'                    => $request['concepto'],
                        'debe'                           => $request['monto_prestamo'],
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $idusuario,
                        'id_usuariomod'                  => $idusuario,
                    ]);
                    $input_prestamo['id_documento'] = $id_comprobante;
                    $input_prestamo['tipo_documento'] = 'EGV';
                    // $prestamo->update([
                    //     'id_documento' => $id_comprobante,
                    //     'tipo_documento' => 'EGV'
                    // ]);
                }
            }
            // dd($input_prestamo);

            $id_prestamo = Ct_Rh_Prestamos::insertGetId($input_prestamo);

            DB::commit();
            //$inventario = Inventario2::build_process('C', $id_compras, $id_empresa);
            //dd($inventario);
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'titulos' => 'Exito', 'id_prestamo' => $id_prestamo];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function masivo_egresos()
    {
        $empresa = Empresa::where('prioridad', 1)->where('estado', 1)->first();
        $prestamos = Ct_Rh_Prestamos::where('estado', '1')->where('id_empresa', $empresa->id)->wherenull('id_documento')->get();

        foreach ($prestamos as $prestamo) {
            $caja_banco = Ct_Caja_Banco::where('id_empresa', $prestamo->id_empresa)->where('cuenta_mayor', $prestamo->cuenta_saliente)->where('estado', 1)->first();
            if (!is_null($caja_banco)) {
                $asiento = Ct_Asientos_Cabecera::where('id_empresa', $prestamo->id_empresa)->where('id', $prestamo->id_asiento_cabecera)->first();
                if (!is_null($asiento)) {
                    # code...
                    if ($prestamo->id_tipo_pago == '1') { //DEBITO
                        $nota_debito         = [
                            'concepto'        => $prestamo->concepto,
                            'fecha'           => $prestamo->fecha_creacion,
                            'valor'           => $prestamo->monto_prestamo,
                            'empresa'         => $prestamo->id_empresa,
                            'tipo'            => "BAN-ND",
                            'id_asiento'      => $prestamo->id_asiento_cabecera,
                            'id_banco'        => $caja_banco->id,
                            'ip_creacion'     => "MASIVO PRESTAMOS",
                            'ip_modificacion' => $prestamo->ip_cliente,
                            'id_usuariocrea'  => $prestamo->id_usuariocrea,
                            'id_usuariomod'   => $prestamo->id_usuariocrea,
                            'modulo'          => 'PRESTAMOS EMPLEADOS',
                        ];
                        $id_nota = Nota_Debito::insertGetId($nota_debito);
                        if (count($asiento->detalles) > 0) {
                            foreach ($asiento->detalles as $detalle) {
                                if ($detalle->debe > 0) {
                                    $nota_deb_detalle = [
                                        'id_nota_debito'  => $id_nota,
                                        'codigo'          => $detalle->id_plan_cuenta,
                                        'cuenta'          => $detalle->descripcion,
                                        'debe'            => $prestamo->monto_prestamo,
                                        'haber'           => number_format(0, 2),
                                        'valor_base'      => $prestamo->monto_prestamo, //number_format($valor['debe'], 2),
                                        'ip_creacion'     => "MASIVO PRESTAMOS",
                                        'ip_modificacion' => $prestamo->ip_cliente,
                                        'id_usuariocrea'  => $prestamo->id_usuariocrea,
                                        'id_usuariomod'   => $prestamo->id_usuariocrea,
                                    ];
                                    Nota_Debito_Detalle::create($nota_deb_detalle);
                                }
                            }
                        }

                        $prestamo->update([
                            'id_documento' => $id_nota,
                            'tipo_documento' => 'BAN-ND'
                        ]);
                    } else { //COMPROBANTE DE EGRESO
                        $numero_factura = LogAsiento::getSecuencia(2);

                        $input_comprobante = [
                            'descripcion'     => $prestamo->concepto,
                            'estado'          => '1',
                            'id_asiento_cabecera' => $prestamo->id_asiento_cabecera,
                            'id_secuencia'    => 'null',
                            'nota'            => $prestamo->concepto,
                            'fecha_comprobante' => $prestamo->fecha_creacion,
                            'beneficiario'    => $prestamo->nombres,
                            'check'           => 0,
                            'girado'          => $prestamo->nombres,
                            'id_caja_banco'   => $caja_banco->id,
                            'nro_cheque'      => $prestamo->num_cheque,
                            'valor'           => $prestamo->monto_prestamo,
                            'fecha_cheque'    => $prestamo->fecha_cheque,
                            'secuencia'       => $numero_factura,
                            'id_empresa'      => $prestamo->id_empresa,
                            'id_usuariocrea'  => $prestamo->id_usuariocrea,
                            'id_usuariomod'   => $prestamo->id_usuariocrea,
                            'ip_creacion'     => "MASIVO PRESTAMOS",
                            'ip_modificacion' => $prestamo->ip_cliente,
                            'modulo'          => 'PRESTAMOS EMPLEADOS',
                        ];
                        $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);
                        if (count($asiento->detalles) > 0) {
                            # code...

                            foreach ($asiento->detalles as $detalle) {

                                if ($detalle->debe > 0) {
                                    Ct_Detalle_Comprobante_Egreso_Varios::create([
                                        'id_comprobante_varios'          => $id_comprobante,
                                        'codigo'                         => $detalle->id_plan_cuenta,
                                        'cuenta'                         => $detalle->descripcion,
                                        'descripcion'                    => $prestamo->concepto,
                                        'debe'                           => $prestamo->monto_prestamo,
                                        'id_secuencia'                   => $numero_factura,
                                        'estado'                         => '1',
                                        'ip_creacion'                    => "MASIVO PRESTAMOS",
                                        'ip_modificacion'                => $prestamo->ip_cliente,
                                        'id_usuariocrea'                 => $prestamo->id_usuario,
                                        'id_usuariomod'                  => $prestamo->id_usuario,
                                    ]);
                                }
                            }
                        }
                        $prestamo->update([
                            'id_documento' => $id_comprobante,
                            'tipo_documento' => 'EGV'
                        ]);
                    }
                }
            }
        }

        return "ok gracias amigo";
    }

    public function pdfprestamos($id_prestamo, Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');

        $prest_empleado = Ct_Rh_Prestamos::where('estado', '1')
            ->where('id_empresa', $id_empresa)
            ->where('id', $id_prestamo)->first();

        $empresa = Empresa::where('estado', '1')
            ->where('id', $prest_empleado->id_empresa)->first();

        $letras = new Numeros_Letras();

        //CONVIERTE EL VALOR EN LETRAS
        $total_str = $letras->convertir(number_format($prest_empleado->valor_cuota, 2, '.', ''), "DOLARES", "CTVS");

        $asiento_cabecera = Ct_Asientos_Cabecera::where('id', $prest_empleado->id_asiento_cabecera)->first();

        $asiento_detalle = Ct_Asientos_Detalle::where('estado', '1')
            ->where('id_asiento_cabecera', $asiento_cabecera->id)->get();

        $caj_banc = Ct_Caja_Banco::where('estado', '1')
            ->where('cuenta_mayor', $prest_empleado->cuenta_saliente)
            ->first();

        $view = \View::make('contable.rh_prestamos_empleados.pdf_prestamos', compact('prest_empleado', 'empresa', 'total_str', 'asiento_cabecera', 'asiento_detalle', 'caj_banc'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Prestamos Comprobante' . $id_prestamo . '.pdf');
    }


    /*************************************************
     ************BUSCAR PRESTAMO EMPLEADO**************
    /*************************************************/

    public function search_prestamo(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $constraints = [
            'id_empl'    => $request['identificacion'],
            'estado'     => 1,
            'id_empresa' => $id_empresa,
            'nombres'    => $request['nombre'],

        ];
        $registros = $this->doSearchingQuery($constraints);
        //$empresas = Empresa::all();

        return view('contable.rh_prestamos_empleados.index', ['request' => $request, 'empresa' => $empresa, 'registros' => $registros, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Rh_Prestamos::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                //$query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                $query = $query->where('estado', '1')->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(10);
    }

    //Reporte Prestamo
    public function reporte_prestamo(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        //dd($id_empresa);
        $reporte_prestamo = DB::table('ct_rh_prestamos')
            ->join('ct_nomina', 'ct_rh_prestamos.id_empl', '=', 'ct_nomina.id_user')
            ->join('empresa', 'ct_rh_prestamos.id_empresa', 'empresa.id')
            ->where('ct_rh_prestamos.monto_prestamo', '!=', 0.00)
            ->select('ct_rh_prestamos.*', 'empresa.nombrecomercial')
            ->distinct()
            ->paginate(5);
        $empresa_buscar = Empresa::all();
        $bancos = Ct_Bancos::all();

        return view('contable.rh_prestamos_empleados.reporte_prestamos', ['empresa' => $empresa, 'reporte_prestamo' => $reporte_prestamo, 'empresa_buscar' => $empresa_buscar, 'bancos' => $bancos]);
    }
    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();
        $fecha     = $request['fechaini'];
        $fechafin  = $request['fecha'];
        $constraints = [
            'id_empl'                   => $request['cedula'],
            'id_empresa'                => $request['nombre_empresa'],
            'concepto'                  => $request['concepto'],
            'banco_beneficiario'        => $request['banco'],
        ];
        $reporte_prestamo = $this->doSearchingQueru($constraints, $fecha, $fechafin);
        $empresa_buscar = Empresa::all();
        $bancos = Ct_Bancos::all();
        $prestamos = Ct_Rh_Prestamos::where('estado', 1)->paginate(5);
        return view('contable.rh_prestamos_empleados.reporte_prestamos', ['empresa' => $empresa, 'request' => $request, 'reporte_prestamo' => $reporte_prestamo, 'prestamos' => $prestamos, 'empresa_buscar' => $empresa_buscar, 'searchingVals' => $constraints, 'bancos' => $bancos]);
    }
    private function doSearchingQueru($constraints, $fecha, $fechafin)
    {
        $query  = Ct_Rh_Prestamos::query();
        $fields = array_keys($constraints);
        $index = 0;

        if ($fecha == null) {
            $fecha = date('2010-01-01');
        }
        if ($fechafin == null) {
            $fechafin = date('Y-m-d');
        }
        //dd($fecha);
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where('ct_rh_prestamos.estado', '1')->where($fields[$index], 'like', '%' . $constraint . '%')
                    ->join('empresa', 'ct_rh_prestamos.id_empresa', 'empresa.id')
                    ->whereBetween('ct_rh_prestamos.fecha_creacion', [$fecha, $fechafin])
                    ->select('ct_rh_prestamos.*', 'empresa.nombrecomercial');
            }

            $index++;
        }

        return $query->paginate(5);
    }

    public function prestamos_visualizar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_auth = Auth::user()->id;
        $usuario = User::find($id_auth);
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();

        $prestamos = Ct_Rh_Prestamos::where('id_empl', $usuario->id)->get();
        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl', $usuario->id)->get();

        //dd($saldos);


        return view('contable.rh_prestamos_visualizar.index', ['id_auth' => $id_auth, 'usuario' => $usuario, 'empresa' => $empresa, 'prestamos' => $prestamos, 'saldos' => $saldos]);
    }

    public function modal_prestamos(Request $request, $id_prestamo)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $fecha_hoy = date('Y-m-d');
        $prestamo = Ct_Rh_Prestamos::find($id_prestamo);

        //$rol = Ct_Rol_Pagos::where('id_user',$prestamo->id_empl)->whereBetween('fecha_elaboracion', [$prestamo->fecha_creacion, $fecha_hoy])->join('ct_detalle_rol as detrol','detrol.id_rol','ct_rol_pagos.id')->select('ct_rol_pagos.id as idrol','detrol.*')->get();   
        //$pres_utili = Ct_Prestamos_Utilidades::where('id_usuario', $prestamo->id_empl)->where('pres_sal','1')->where('estado','1')->whereNotNull('id_asiento')->first();     
        //dd($rol);

        $detalle = $prestamo->detalles;

        return view('contable.rh_prestamos_visualizar.modal_ver_prestamos', ['empresa' => $empresa, 'prestamo' => $prestamo, 'detalle' => $detalle]);
    }

    public function prestamos_saldos(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();
        $fecha = date('Y/m/d');

        return view('contable.rh_prestamos_visualizar.reporte_saldo_prestamo', ['empresa' => $empresa, 'fecha' => $fecha]);
    }

    public function excel_reporte_prestamo(Request $request)
    {
        // asd
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();

        $fecha_hasta = $request['fecha_hasta'];
        //dd($fecha_hasta);
        if (is_null($fecha_hasta)) {
            $fecha_hasta = date('Y/m/d');
        }
        $prestamos = Ct_Rh_Prestamos::where('id_empresa', $id_empresa)->where('estado', '1')->where('prest_cobrad', '0')->get();
        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empresa', $id_empresa)->where('estado', '1')->get();

        Excel::create('Reporte Prestamos', function ($excel) use ($prestamos, $empresa, $fecha_hasta, $saldos) {
            $excel->sheet('Reporte', function ($sheet) use ($prestamos, $empresa, $fecha_hasta, $saldos) {
                $sheet->mergeCells('A1:D1');
                $sheet->cell('A1', function ($cell) use ($fecha_hasta) {
                    $cell->setValue('SALDOS  PRESTAMOS EMPLEADOS AL' . ' ' . $fecha_hasta);
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#EC4420');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A2:D2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    $cell->setValue('EMPRESA:' . ' ' . $empresa->id . '-' . $empresa->nombrecomercial);
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A3', function ($cell) {
                    $cell->setValue('EMPLEADO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#EC4420');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B3', function ($cell) {
                    $cell->setValue('FECHA INGRESO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#EC4420');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C3', function ($cell) use ($fecha_hasta) {
                    $cell->setValue('SALDO' . ' ' . $fecha_hasta);
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#EC4420');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function ($cell) {
                    $cell->setValue('CUOTA MENSUAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontColor('#EC4420');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i = 4;
                $totalprestamo = 0;
                // dd($prestamos);
                foreach ($prestamos as $value) {
                    $validar_usuario = Ct_Nomina::where('id_user', $value->id_empl)->where('estado', '1')->first();
                    if (!is_null($validar_usuario)) {
                        $anio = date('Y', strtotime($fecha_hasta));
                        $mes  = date('m', strtotime($fecha_hasta));
                        // dd($anio);
                        $rol = Ct_Rol_Pagos::where('id_user', $value->id_empl)
                            //->whereBetween('ct_rol_pagos.fecha_elaboracion', [$fecha . ' 00:00', $fecha_hasta . ' 23:59'])
                            ->join('ct_detalle_rol as detrol', 'detrol.id_rol', 'ct_rol_pagos.id')
                            ->select('ct_rol_pagos.id as idrol', 'detrol.*')->get();
                        //dd($value->monto_prestamo);
                        $total = $value->monto_prestamo;
                        $totalprestamo += $value->saldo_total;
                        //dd($total);

                        foreach ($rol as $r) {
                            $total = $total - $r->prestamos_empleado;
                        }
                        //($total);
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->nombres);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nomina = Ct_Nomina::where('id_user', $value->id_empl)->first();
                        $sheet->cell('B' . $i, function ($cell) use ($nomina) {
                            if (!is_null($nomina)) {
                                if (!is_null($nomina->fecha_ant)) {
                                    $cell->setValue($nomina->fecha_ant);
                                } else {
                                    $cell->setValue($nomina->fecha_ingreso);
                                }
                            } else {
                                $cell->setValue('');
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->saldo_total);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->valor_cuota);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $i++;
                        //$totalPrestamo = $totalPrestamo + $total;
                    }
                }
                $i++;
                $totalsaldo = 0;
                foreach ($saldos as $s) {
                    $validar_usuario = Ct_Nomina::where('id_user', $s->id_empl)->where('estado', '1')->first();
                    if (!is_null($validar_usuario)) {
                        $totalsaldo += $s->saldo_res;
                        $sheet->cell('A' . $i, function ($cell) use ($s) {
                            $cell->setValue($s->nombres);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nomina = Ct_Nomina::where('id_user', $s->id_empl)->first();
                        $sheet->cell('B' . $i, function ($cell) use ($nomina) {
                            if (!is_null($nomina)) {
                                if (!is_null($nomina->fecha_ant)) {
                                    $cell->setValue($nomina->fecha_ant);
                                } else {
                                    $cell->setValue($nomina->fecha_ingreso);
                                }
                            } else {
                                $cell->setValue('');
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->cell('C' . $i, function ($cell) use ($s) {
                            $cell->setValue($s->saldo_res);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->cell('D' . $i, function ($cell) use ($s) {
                            $cell->setValue($s->valor_cuota);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $i++;
                    }
                }
                $tot = $totalprestamo + $totalsaldo;
                $sheet->cell('A' . $i, function ($cell) {
                    $cell->setValue('TOTALES');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B' . $i, function ($cell) use ($tot) {
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C' . $i, function ($cell) use ($tot) {
                    $cell->setValue($tot);
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->getStyle('C' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                $sheet->cell('D' . $i, function ($cell) {
                    $cell->setValue('');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });

            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(35)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(18)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(18)->setAutosize(false);
        })->export('xlsx');
    }

    public function index_cruce(Request $request)
    {
        dd("Error");

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();

        $anio = date('Y');
        $mes = date('m');
        $fecha_hoy = date('Y-m-d');

        $empl_nomina = Ct_Nomina::where('estado', '1')
            ->where('id_empresa', $id_empresa)
            ->orderby('id', 'asc')->get();

        $prestamos = Ct_Rh_Prestamos::where('ct_rh_prestamos.id_empresa', $id_empresa)
            ->where('ct_rh_prestamos.estado', '1')
            ->join('ct_nomina as n', 'n.id_user', 'ct_rh_prestamos.id_empl')
            ->select('ct_rh_prestamos.*', 'n.cargo', 'n.area', 'n.sueldo_neto', 'n.id as id_nomina', 'n.fecha_ingreso')->get();
        //dd($prestamos);
        $saldos = Ct_Rh_Saldos_Iniciales::where('ct_rh_saldos_iniciales.id_empresa', $id_empresa)
            ->where('ct_rh_saldos_iniciales.estado', '1')
            ->join('ct_nomina as n', 'n.id_user', 'ct_rh_saldos_iniciales.id_empl')
            ->select('ct_rh_saldos_iniciales.*', 'n.cargo', 'n.area', 'n.sueldo_neto', 'n.id as id_nomina', 'n.fecha_ingreso')->get();

        return view('contable.rh_prestamo_utilidades.index', ['empl_nomina' => $empl_nomina, 'empresa' => $empresa, 'prestamos' => $prestamos, 'anio' => $anio, 'mes' => $mes, 'saldos' => $saldos, 'fecha_hoy' => $fecha_hoy]);
    }

    public function modal_utilidades($id)
    {

        return view('contable.rh_prestamo_utilidades.modal_utilidades', ['id' => $id]);
    }

    public function modal_utilidades_saldos($id)
    {

        return view('contable.rh_prestamo_utilidades.modal_utilidades_saldos', ['id' => $id]);
    }

    public function guardar_mod(Request $request)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $id_empresa   = $request->session()->get('id_empresa');
        $id_prestamo  = $request['id_prestamo'];
        $prestamo = Ct_Rh_Prestamos::find($id_prestamo);
        $anio = $request['anio'];
        $mes = $request['mes'];
        $monto_utilidad = $request['val_liq'];
        $val_total = $prestamo->saldo_total - $monto_utilidad;

        //dd($request->all());

        $arr = [
            'anio'            => $anio,
            'mes'             => $mes,
            'id_usuario'      => $prestamo->id_empl,
            'id_empresa'      => $id_empresa,
            'total'           => $request['val_liq'],
            'fecha_creacion'  => date('Y-m-d'),
            'pres_sal'        => '1', //prestamo
            'monto_pres_sal'  => $prestamo->monto_prestamo,
            'valor_total'     => $val_total,
            'id_prestamo'     => $prestamo->id,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        Ct_Prestamos_Utilidades::create($arr);

        $msj =  "ok";

        return ['msj' => $msj];
    }

    public function guardar_mod_saldos(Request $request)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $id_empresa   = $request->session()->get('id_empresa');
        $id_saldo  = $request['id_saldo'];
        $saldo = Ct_Rh_Saldos_Iniciales::find($id_saldo);
        $anio = $request['anio'];
        $mes = $request['mes'];
        $monto_utilidad_s = $request['val_liq_sal'];
        $v_total = $saldo->saldo_res - $monto_utilidad_s;
        // dd($id_saldo);
        //dd($request->all());

        $arr = [
            'anio'            => $anio,
            'mes'             => $mes,
            'id_usuario'      => $saldo->id_empl,
            'id_empresa'      => $id_empresa,
            'total'           => $monto_utilidad_s,
            'fecha_creacion'  => date('Y-m-d'),
            'pres_sal'        => '2', //saldos
            'monto_pres_sal'  => $saldo->saldo_inicial,
            'valor_total'     => $v_total,
            'id_saldo'        => $saldo->id,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        Ct_Prestamos_Utilidades::create($arr);

        $msj =  "ok";

        return ['msj' => $msj];
    }

    public function asientos_guardar(Request $request)
    {

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $fecha = $request['fecha_creacion'];
        //dd($fecha);
        $id_empresa     = $request->session()->get('id_empresa');
        $anio           = $request['year'];
        $mes            = $request['mes'];
        $prestamo_utilidad = Ct_Prestamos_Utilidades::where('estado', '1')->where('anio', $anio)->where('mes', $mes)->where('id_empresa', $id_empresa);
        $suma_utilidad = $prestamo_utilidad->sum('total');
        //dd($suma_utilidad);
        $txt_mes = '';
        if ($mes == '12') {
            $txt_mes = 'DICIEMBRE';
        } elseif ($mes == '11') {
            $txt_mes = 'NOVIEMBRE';
        } elseif ($mes == '10') {
            $txt_mes = 'OCTUBRE';
        } elseif ($mes == '9') {
            $txt_mes = 'SEPTIEMBRE';
        } elseif ($mes == '8') {
            $txt_mes = 'AGOSTO';
        } elseif ($mes == '7') {
            $txt_mes = 'JULIO';
        } elseif ($mes == '6') {
            $txt_mes = 'JUNIO';
        } elseif ($mes == '5') {
            $txt_mes = 'MAYO';
        } elseif ($mes == '4') {
            $txt_mes = 'ABRIL';
        } elseif ($mes == '3') {
            $txt_mes = 'MARZO';
        } elseif ($mes == '2') {
            $txt_mes = 'FEBRERO';
        } elseif ($mes == '1') {
            $txt_mes = 'ENERO';
        }

        $text = 'Cruce Utilidades vs Prestamos' . ':' . ' ' . 'Id_Empresa' . ':' . $id_empresa . ' ' . 'Año' . ':' . $anio . ' ' . 'Mes' . ':' . $txt_mes;

        $input_cabecera = [
            'fecha_asiento'   => $fecha,
            'id_empresa'      => $id_empresa,
            'observacion'     => $text,
            'valor'           => $suma_utilidad, //suma de las utilidades
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];

        $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

        if ($suma_utilidad > 0) {

            //2.01.04.05 PARTICIPACION TRABAJADORES POR PAGAR DEL EJERCICIO
            //PRESTAMOS_EMPLEADOS_PARTIC_TRAB_PAGAR
            //$plan_cuentas= Plan_Cuentas::where('id','2.01.07.05')->first();
            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS_PARTIC_TRAB_PAGAR');


            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera'           => $id_asiento_cabecera,
                //'id_plan_cuenta'                => '2.01.07.05',
                'id_plan_cuenta'                => $plan_cuentas->cuenta_guardar,
                'descripcion'                   => $plan_cuentas->nombre_mostrar,
                'fecha'                         => $fecha,
                'debe'                          => $suma_utilidad,
                'haber'                         => '0',
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,

            ]);
        }

        if ($suma_utilidad > 0) {
            //$plan_cuentas= Plan_Cuentas::where('id','1.01.02.06.03')->first();
            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');

            Ct_Asientos_Detalle::create([

                'id_asiento_cabecera'           => $id_asiento_cabecera,
                //'id_plan_cuenta'                => '1.01.02.06.03',
                'id_plan_cuenta'                => $plan_cuentas->cuenta_guardar,
                'descripcion'                   => $plan_cuentas->nombre_mostrar,
                'fecha'                         => $fecha,
                'debe'                          => '0',
                'haber'                         => $suma_utilidad,
                'id_usuariocrea'                => $idusuario,
                'id_usuariomod'                 => $idusuario,
                'ip_creacion'                   => $ip_cliente,
                'ip_modificacion'               => $ip_cliente,

            ]);
        }

        $prest_utilidad = $prestamo_utilidad->get();
        foreach ($prest_utilidad as $value) {
            $arr_prest_utili = [
                'id_asiento'        => $id_asiento_cabecera,
                'id_usuariomod'     => $idusuario,
            ];

            $value->update($arr_prest_utili);
        }


        $p_u = $prestamo_utilidad->where('pres_sal', '1')->get();

        foreach ($p_u as $val) {
            $prest = Ct_Rh_Prestamos::where('estado', '1')->where('id_empl', $val->id_usuario)->first();
            if (!is_null($prest)) {
                $resta  = $prest->saldo_total - $val->total;

                if ($resta <= '0,00') {
                    $arr_prestamo = [
                        'prest_cobrad'      => '1',
                        'estado'            => '0',
                        'id_usuariomod'     => $idusuario,
                    ];

                    $prest->update($arr_prestamo);
                } else {
                    $arr_pres2 = [
                        'saldo_total' => $resta,
                        'id_usuariomod' => $idusuario,
                    ];

                    $prest->update($arr_pres2);
                }
            }
        }

        $p_utili_s = Ct_Prestamos_Utilidades::where('estado', '1')->where('anio', $anio)->where('mes', $mes)->where('pres_sal', '2')->where('id_empresa', $id_empresa)->get();

        foreach ($p_utili_s as $ps) {
            $sald = Ct_Rh_Saldos_Iniciales::where('estado', '1')->where('id_empl', $ps->id_usuario)->first();
            if (!is_null($sald)) {
                $resta_saldo = $sald->saldo_res - $ps->total;

                if ($resta_saldo <= '0,00') {
                    $arr_saldo = [
                        'saldo_cobrad'      => '1',
                        'estado'            => '0',
                        'id_usuariomod'     => $idusuario,
                    ];

                    $sald->update($arr_saldo);
                } else {
                    $arr_sald2 = [
                        'saldo_res' => $resta_saldo,
                        'id_usuariomod' => $idusuario,
                    ];

                    $sald->update($arr_sald2);
                }
            }
        }




        //return "ok";
        return redirect()->route('prestamos_empleados.index_cruce');
    }

    public function index_saldos(Request $request)
    {
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', 1)->first();

        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empresa', $id_empresa)->where('estado', '1')->get();

        return view('contable.rh_saldos_iniciales.index', ['empresa' => $empresa, 'saldos' => $saldos]);
    }

    public function modal_saldos(Request $request, $id_saldo)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $saldo = Ct_Rh_Saldos_Iniciales::find($id_saldo);
        $detalle = $saldo->detalles;
        $fecha_hoy = date('Y-m-d');
        //$rol = Ct_Rol_Pagos::where('id_user',$saldo->id_empl)->whereBetween('fecha_elaboracion', [$saldo->fecha_creacion, $fecha_hoy])->join('ct_detalle_rol as detrol','detrol.id_rol','ct_rol_pagos.id')->select('ct_rol_pagos.id as idrol','detrol.*')->get(); 
        //$pres_utili = Ct_Prestamos_Utilidades::where('id_usuario', $saldo->id_empl)->where('pres_sal','1')->where('estado','1')->whereNotNull('id_asiento')->first();     
        //dd($saldo);


        return view('contable.rh_saldos_iniciales.modal_ver_saldos', ['id_empresa' => $id_empresa, 'saldo' => $saldo, 'detalle' => $detalle]);
    }

    public function buscar_saldos(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $constraints = [
            'id_empl'    => $request['identificacion'],
            'estado'     => 1,
            'id_empresa' => $id_empresa,
            'nombres'    => $request['nombre'],

        ];
        $saldos = $this->doSearchingQuery2($constraints);
        //$empresas = Empresa::all();

        return view('contable.rh_saldos_iniciales.index', ['request' => $request, 'empresa' => $empresa, 'saldos' => $saldos, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery2($constraints)
    {

        $query  = Ct_Rh_Saldos_Iniciales::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                //$query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                $query = $query->where('estado', '1')->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(10);
    }

    public function pdf_cruce($mes, $anio, Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('estado', '1')->where('id', $id_empresa)->first();

        $empl_nomina = Ct_Nomina::where('estado', '1')
            ->where('id_empresa', $id_empresa)
            ->orderby('id', 'asc')->get();

        $prestamos = Ct_Rh_Prestamos::where('ct_rh_prestamos.id_empresa', $id_empresa)
            ->where('ct_rh_prestamos.estado', '1')
            ->join('ct_nomina as n', 'n.id_user', 'ct_rh_prestamos.id_empl')
            ->select('ct_rh_prestamos.*', 'n.cargo', 'n.area', 'n.sueldo_neto', 'n.id as id_nomina', 'n.fecha_ingreso')->get();
        //dd($prestamos);
        $saldos = Ct_Rh_Saldos_Iniciales::where('ct_rh_saldos_iniciales.id_empresa', $id_empresa)
            ->where('ct_rh_saldos_iniciales.estado', '1')
            ->join('ct_nomina as n', 'n.id_user', 'ct_rh_saldos_iniciales.id_empl')
            ->select('ct_rh_saldos_iniciales.*', 'n.cargo', 'n.area', 'n.sueldo_neto', 'n.id as id_nomina', 'n.fecha_ingreso')->get();

        /*$prest_util = Ct_Prestamos_Utilidades::where('estado','1')
        ->where('anio',$anio)
        ->where('mes',$mes)
        ->where('id_empresa',$id_empresa)->get();*/

        $view = \View::make('contable.rh_prestamo_utilidades.pdf_cruce', compact('empresa', 'anio', 'mes', 'empl_nomina', 'prestamos', 'saldos'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('pdf_cruce.pdf');
    }

    public function actualizar_saldo_prestamo(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $idusuario    = Auth::user()->id;
        $prestamos = Ct_Rh_Prestamos::where('estado', '1')->get();
        $saldos = Ct_Rh_Saldos_Iniciales::where('estado', '1')->get();
        //dd($saldos, $prestamos);
        $fecha_hoy = date('Y-m-d');
        foreach ($prestamos as $prestamo) {
            $rol = Ct_Rol_Pagos::where('id_user', $prestamo->id_empl)
                ->whereBetween('fecha_elaboracion', [$prestamo->fecha_creacion, $fecha_hoy])
                ->join('ct_detalle_rol as detrol', 'detrol.id_rol', 'ct_rol_pagos.id')
                ->select('ct_rol_pagos.id as idrol', 'detrol.*')->get();

            $total = $prestamo->monto_prestamo;
            foreach ($rol as $r) {
                $total = $total - $r->prestamos_empleado;
            }

            $up_pres = [
                'saldo_total'    => $total,
                'id_usuariomod'  => $idusuario,
            ];

            $prestamo->update($up_pres);
        }

        foreach ($saldos as $saldo) {
            $rol = Ct_Rol_Pagos::where('id_user', $saldo->id_empl)
                ->whereBetween('fecha_elaboracion', [$saldo->fecha_creacion, $fecha_hoy])
                ->join('ct_detalle_rol as detrol', 'detrol.id_rol', 'ct_rol_pagos.id')
                ->select('ct_rol_pagos.id as idrol', 'detrol.*')
                ->get();

            $tot = $saldo->saldo_inicial;

            foreach ($rol as $value) {
                $tot = $tot - $value->saldo_inicial_prestamo;
            }

            $up_sal = [
                'saldo_res'   => $tot,
                'id_usuariomod' => $idusuario,
            ];

            $saldo->update($up_sal);
        }

        return "osk";
    }
}
