<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use DateTime;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Detalle_Rol;
use Sis_medico\Ct_Nomina;
use Sis_medico\Ct_Rh_Cuotas_Hipotecarios;
use Sis_medico\Ct_Rh_Cuotas_Quirografario;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Rh_Otros_Anticipos;
use Sis_medico\Ct_Rh_Prestamos;
use Sis_medico\Ct_Rh_Saldos_Iniciales;
use Sis_medico\Ct_Rh_Tipo_Cuenta;
use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Rh_Valores;
use Sis_medico\Ct_Rh_Valor_Anticipos;
use Sis_medico\Ct_Rol_Forma_Pago;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Ct_Tipo_Rol;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
//Nueva Forma de Pago
use Sis_medico\Plan_Cuentas;
use Sis_medico\User;
use Sis_medico\Log_horas_extras;
use Sis_medico\Ct_Rh_Saldos_Iniciales_Detalle;
use Sis_medico\Ct_Rh_Prestamos_Detalle;
use Sis_medico\RolAsiento;
use Sis_medico\CtLogRolNomina;
use Sis_medico\Firma_Usuario;


class RolPagoController extends Controller
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
    public function index($id, $id_empresa)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_nomina = $id;

        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $rol_pag = Ct_Rol_Pagos::where('estado', '1')
            ->where('id_nomina', $id_nomina)
            ->select('*', DB::raw("CONCAT(anio, mes) AS mesanio"))
            ->orderby('mesanio', 'Desc')->paginate(20);
        $empleado = Ct_Nomina::find($id);

        $empresas = Empresa::all();

        $rh_valores = Ct_Rh_Valores::where('id_empresa',$id_empresa)->get();

        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $id_empresa)->where('tipo', 4)->first();
        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $id_empresa)->where('tipo', 3)->first();

        //dd($rh_valores);

        return view('contable/rol_pago/index', ['id_nomina' => $id_nomina, 'rol_pag' => $rol_pag, 'empresas' => $empresas, 'empresa' => $empresa, 'empleado' => $empleado, 'rh_valores' => $rh_valores, 'val_fond_reserv' => $val_fond_reserv, 'val_sal_basico' => $val_sal_basico]);
    }

    public function crear_rol_pago($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $registro = Ct_Nomina::findorfail($id);
        //dd($registro);
        $empresa  = Empresa::where('id', $registro->id_empresa)->first();
        //dd($empresa);
        $usuario  = User::where('id', $registro->id_user)->first();

        $ct_tipo_rol = Ct_Tipo_Rol::all();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $tipo_cuenta = Ct_Rh_Tipo_Cuenta::all();

        $lista_banco = Ct_Bancos::all();

        $prestam_empl = DB::table('ct_egreso_empleado as ep')
            ->where('ep.id_empleado', $registro->id_user)
            ->groupBy('ep.id_empleado')
            ->select(DB::raw("SUM(ep.monto_descontar) as suma_prest"))
            ->first();

        $val_aport_pers = Ct_Rh_Valores::where('id_empresa', $registro->id_empresa)
            ->where('tipo', 1)->where('id', $registro->aporte_personal)->first();

        //dd($registro);

        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $registro->id_empresa)
            ->where('tipo', 4)->first();

        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $registro->id_empresa)
            ->where('tipo', 3)->first();

        $anio = date('Y');
        $mes  = date('m');


        $existe_rol = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->where('ct_rol_pagos.anio', $anio)
            ->where('ct_rol_pagos.mes', $mes)
            ->where('ct_rol_pagos.id_nomina', $id)
            ->first();

        /** Calcula el fondo de reserva*/
        $fecha = date("Y-m-d");
        $fec = new DateTime($fecha);

        $fec2 = new DateTime($registro->fecha_ingreso);
        $diff = $fec->diff($fec2);
        $intervalMeses = $diff->format("%m");
        $intervalAnos = $diff->format("%y") * 12;
        $añosAhora = $diff->format("%y");
        $intervalDias = $diff->format("%d");

        $diaActual = $fec->format("d");
        $meses_totales = $intervalMeses + $intervalAnos;


        $monto_fondo_reserva = 0;

        $pago_fondo = 0;

        if ($añosAhora > 0) {

            if ($registro->pago_fondo_reserva == 2) {

                $pago_fondo = ($registro->sueldo_neto) * ($val_fond_reserv->valor / 100);
            } else if ($registro->pago_fondo_reserva == 1) {

                if ($añosAhora > 0 && $diaActual > 15) {
                    $intervalMeses += 1;
                    $pago_fondo = ($registro->sueldo_neto * $intervalMeses) * ($val_fond_reserv->valor / 100);
                } else {
                    $pago_fondo = ($registro->sueldo_neto * $intervalMeses) * ($val_fond_reserv->valor / 100);
                }
            }
        }


        if (!is_null($existe_rol)) {
            return redirect()->route('rol_pago.editar', ['id' => $existe_rol->id]);
        } else {
            return view('contable/rol_pago/create_rol', ['empresa' => $empresa, 'usuario' => $usuario, 'registro' => $registro, 'ct_tipo_rol' => $ct_tipo_rol, 'val_aport_pers' => $val_aport_pers, 'tipo_pago_rol' => $tipo_pago_rol, 'val_fond_reserv' => $val_fond_reserv, 'val_sal_basico' => $val_sal_basico, 'tipo_cuenta' => $tipo_cuenta, 'lista_banco' => $lista_banco, 'prestam_empl' => $prestam_empl, 'anio' => $anio, 'mes' => $mes, 'monto_fondo_reserva' => $monto_fondo_reserva, 'pago_fondo' => $pago_fondo]);
        }

        //Guia Forma de Pago Factura de Venta
        //$tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        //$tipo_tarjeta = Ct_Tipo_Tarjeta::all();

        /*return view('contable/rol_pago/create', ['empresa' => $empresa,'usuario' => $usuario,'registro' => $registro,'ct_tipo_rol' => $ct_tipo_rol,'val_aport_pers' => $val_aport_pers]);*/
    }

    public function store_rol_pago(Request $request)
    {
        //$variable_hip = $request['contador_hip'];

        //dd($variable_hip);

        //return $variable_hip;

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $fecha_actual = Date('Y-m-d H:i:s');
        $fecha_pag    = Date('Y-m-d');
        $neto_recibir = 0;

        //Calculo Total Ingresos
        //$sueldo_mensual  = $request['sueldo_mensual'];
        $sueldo_mensual = $request['sueldo_recibir'];
        $id_nom         = $request['id_nomina'];

        //Verifica si ya existe un rol de pago registrado con el mismo año y el mismo mes y que este activo, sino esta activo
        //lo deje crear nuevamente

        //$contador_rpag = DB::table('ct_rol_pagos')->get()->count();

        $rol_anio    = $request['year'];
        $rol_mes     = $request['mes'];
        $rol_empresa = $request['id_empresa'];
        $ident_user  = $request['id_user'];

        $existe_rol = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->where('ct_rol_pagos.anio', $rol_anio)
            ->where('ct_rol_pagos.mes', $rol_mes)
            ->where('ct_rol_pagos.id_empresa', $rol_empresa)
            ->where('ct_rol_pagos.id_user', $ident_user)
            ->first();

        if (!is_null($existe_rol)) {

            $msj = "ok";

            return ['msj' => $msj];
        } else {

            if ($sueldo_mensual != null) {

                $total_ingreso = ($sueldo_mensual) + ($request['sobre_tiempo_50']) + ($request['sobre_tiempo_100']) + ($request['valor_bono']) + ($request['bono_imputable']) + ($request['fondo_reserva']) + ($request['decimo_tercero']) + ($request['decimo_cuarto']) + ($request['alimentacion']) + ($request['transporte']) + ($request['vacaciones']);
            }

            //Calculo Total Egresos
            $total_egreso = ($request['iess']) + ($request['valor_multa']) + ($request['prestamo_empleado']) + ($request['exam_laboratorio']) + ($request['saldo_inicial']) + ($request['anticipo_quincena']) + ($request['otro_anticipo']) + ($request['seguro_privado']) + ($request['impuesto_renta']) + ($request['total_val_quot_quir']) + ($request['total_val_quot_hip']) + ($request['fond_res_cobrar_trab']) + ($request['otros_egresos_trab']);

            //Calculo Neto Recibido
            if ($total_ingreso > $total_egreso) {
                $neto_recibir = $total_ingreso - $total_egreso;
            }

            /*Calculo de BaseIess*/
            $base_iess = ($sueldo_mensual) + ($request['sobre_tiempo_50']) + ($request['sobre_tiempo_100']) + ($request['bono_imputable']);

            /********************************************
             ******GUARDADO TABLA ASIENTO CABECERA********
            /********************************************/

            $usuario = User::where('id', $request['id_user'])->first();

            $empresa = Empresa::where('id', $request['id_empresa'])->first();

            //$text  = 'Registrar Rol de Pago, Empleado'.':'. $usuario->nombre1.' '. $usuario->apellido1.'-'.'Empresa'.':'.$empresa->nombrecomercial.'-'.'Año'.':'.$request['year'].'-'.'Mes'.':'.$request['mes'];

            /*$input_cabecera = [

            'fecha_asiento'   => $fecha_actual,
            'id_empresa'      => $request['id_empresa'],
            'observacion'     => $text,
            'valor'           => $neto_recibir,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            ];*/

            //$id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $input_rol_pago = [

                'id_nomina'         => $request['id_nomina'],
                'id_user'           => $request['id_user'],
                'id_empresa'        => $request['id_empresa'],
                //'id_asiento'                    => $id_asiento_cabecera,
                'anio'              => $request['year'],
                'mes'               => $request['mes'],
                'id_tipo_rol'       => $request['tipo_rol'],
                //'id_tipo_pago'                  => $request['tipo_pago'],
                //'num_cheque'                    => $request['num_cheque'],
                'neto_recibido'     => $neto_recibir,
                'fecha_elaboracion' => $fecha_actual,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,

            ];

            $id_rol_pago = Ct_Rol_Pagos::insertGetId($input_rol_pago);

            /*Ct_Rol_Forma_Pago::create([

            'id_rol_pago'     => $id_rol_pago,
            'id_tipo_pago'    => $request['tipo_pago'],
            'id_tipo_cuenta'  => $request['tipo_cuenta'],
            'banco'           => $request['banco'],
            'numero_cuenta'   => $request['numero_cuenta'],
            'num_cheque'      => $request['num_cheque'],
            'valor'           => $neto_recibir,
            'fecha'           => $fecha_pag,
            'id_usuariocrea'  => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            ]);*/

            //Guardado en la Tabla ct_rol_forma_pago
            $variable = $request['contador_pago'];

            for ($i = 0; $i < $variable; $i++) {

                $visibilidad_p = $request['visibilidad_pago' . $i];

                if ($visibilidad_p == 1) {

                    Ct_Rol_Forma_Pago::create([

                        'id_rol_pago'     => $id_rol_pago,
                        'id_tipo_pago'    => $request['id_tip_pago' . $i],
                        //'fecha'           => $request['fecha_pago' . $i],
                        //'tipo_tarjeta'    => $request['tipo_tarjeta' . $i],
                        //'id_tipo_cuenta'  => $request['tipo_cuenta'],
                        'banco'           => $request['id_banco_pago' . $i],
                        'numero_cuenta'   => $request['id_cuenta_pago' . $i],
                        'num_cheque'      => $request['numero_pago' . $i],
                        //'giradoa'         => $request['giradoa' . $i],
                        'valor'           => $request['valor' . $i],
                        //'valor_base'      => $request['valor_base' . $i],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,

                    ]);
                }
            }

            //Guardado en la Tabla Ct_Detalle_Rol
            Ct_Detalle_Rol::create([

                'id_rol'                    => $id_rol_pago,
                'dias_laborados'            => $request['dias_laborados'],
                'sueldo_mensual'            => $request['sueldo_recibir'],
                'cantidad_horas50'          => $request['cant_horas_50'],
                'sobre_tiempo50'            => $request['sobre_tiempo_50'],
                'cantidad_horas100'         => $request['cant_horas_100'],
                'sobre_tiempo100'           => $request['sobre_tiempo_100'],
                'bonificacion'              => $request['valor_bono'],
                'alimentacion'              => $request['alimentacion'],
                'transporte'                => $request['transporte'],
                'vacaciones'                => $request['vacaciones'],
                'bono_imputable'            => $request['bono_imputable'],
                'exam_laboratorio'          => $request['exam_laboratorio'],
                'fondo_reserva'             => $request['fondo_reserva'],
                'decimo_tercero'            => $request['decimo_tercero'],
                'decimo_cuarto'             => $request['decimo_cuarto'],
                'porcentaje_iess'           => $request['iess'],
                'base_iess'                 => $base_iess,
                'seguro_privado'            => $request['seguro_privado'],
                'impuesto_renta'            => $request['impuesto_renta'],
                'multa'                     => $request['valor_multa'],
                'fond_reserv_cobrar'        => $request['fond_res_cobrar_trab'],
                'otros_egresos'             => $request['otros_egresos_trab'],
                'prestamos_empleado'        => $request['prestamo_empleado'],
                'saldo_inicial_prestamo'    => $request['saldo_inicial'],
                'anticipo_quincena'         => $request['anticipo_quincena'],
                'observacion_bono'          => $request['observacion_bono'],
                'observacion_alimentacion'  => $request['observacion_alimentacion'],
                'observ_seg_privado'        => $request['observacion_seg_priv'],
                'observ_imp_renta'          => $request['observacion_imp_rent'],
                'otro_anticipo'             => $request['otro_anticipo'],
                'observacion_multa'         => $request['observ_multa'],
                'observacion_fondo_cobrar'  => $request['obs_fond_cob_trab'],
                'observacion_prestamo'      => $request['concepto_prestamo'],
                'observacion_saldo_inicial' => $request['obser_saldo_inicial'],
                'observacion_anticip_quinc' => $request['concepto_quincena'],
                'observacion_otro_anticip'  => $request['concep_otros_anticipos'],
                'observacion_transporte'    => $request['observacion_transporte'],
                'observacion_vacaciones'    => $request['observacion_vacaciones'],
                'observacion_bonoimp'       => $request['observacion_bonoimp'],
                'observ_examlaboratorio'    => $request['observ_examlaboratorio'],
                'observacion_otro_egreso'   => $request['obs_otros_egres_trab'],
                'total_ingresos'            => $total_ingreso,
                'total_egresos'             => $total_egreso,
                'neto_recibido'             => $neto_recibir,
                'total_quota_quirog'        => $request['total_val_quot_quir'],
                'total_quota_hipot'         => $request['total_val_quot_hip'],
                'id_usuariocrea'            => $idusuario,
                'id_usuariocrea'            => $idusuario,
                'id_usuariomod'             => $idusuario,
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
            ]);

            //INSERCION CUOTA HIPOTECARIO

            $variable_hip = $request['contador_hip'];

            for ($i = 0; $i < $variable_hip; $i++) {

                $visibilidad_hip = $request['visibilidad_hip' . $i];

                if ($visibilidad_hip == 1) {

                    $input_item_hip = [

                        'id_rol'          => $id_rol_pago,
                        'valor_cuota'     => round(($request['valor_cuota_hip' . $i]), 2),
                        'detalle_cuota'   => $request['detalle_cuota_hip' . $i],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,

                    ];

                    Ct_Rh_Cuotas_Hipotecarios::insert($input_item_hip);
                }
            }

            //INSERCION CUOTA QUIROGRAFARIO
            $variable_quir = $request['contador_quiro'];

            for ($i = 0; $i < $variable_quir; $i++) {

                $visibilidad_quir = $request['visibilidad_quiro' . $i];

                if ($visibilidad_quir == 1) {

                    $input_item_quir = [

                        'id_rol'          => $id_rol_pago,
                        'valor_cuota'     => round(($request['valor_cuota_quir' . $i]), 2),
                        'detalle_cuota'   => $request['detalle_cuota_quir' . $i],
                        'id_usuariocrea'  => $idusuario,
                        'id_usuariomod'   => $idusuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,

                    ];

                    Ct_Rh_Cuotas_Quirografario::insert($input_item_quir);
                }
            }

            $prestamo = Ct_Rh_Prestamos::where('id_empl', $request['id_user'])->where('estado', '1')->first();

            if (!is_null($prestamo)) {
                $resta = $prestamo->saldo_total - $request['prestamo_empleado'];
                $arr_prestamo = [
                    'saldo_total'      => $resta, //restante del prestamo
                    'id_usuariomod'    => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                ];

                $prestamo->update($arr_prestamo);

                if ($resta <= '0') {
                    $arr_prest = [
                        'prest_cobrad'      => '1',
                        'estado'            => '0',
                        'id_usuariomod'     => $idusuario,
                        'ip_modificacion'   => $ip_cliente,
                    ];
                    $prestamo->update($arr_prest);
                }
            }

            $saldo_inicial = Ct_Rh_Saldos_Iniciales::where('id_empl', $request['id_user'])->where('estado', '1')->first();

            if (!is_null($saldo_inicial)) {
                $rest_sal = $saldo_inicial->saldo_res - $request['saldo_inicial'];
                $arr_saldo = [
                    'saldo_res'        => $rest_sal,
                    'id_usuariomod'    => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                ];

                $saldo_inicial->update($arr_saldo);

                if ($rest_sal <= '0') {
                    $arr_sal = [
                        'prest_cobrad'      => '1',
                        'estado'            => '0',
                        'id_usuariomod'     => $idusuario,
                        'ip_modificacion'   => $ip_cliente,
                    ];
                    $prestamo->update($arr_sal);
                }
            }

            return ['id_rol_pago' => $id_rol_pago, 'id_nom' => $id_nom];
        }

        //return ['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => $request['in_eg']];

    }

    public function update_rol_pago(Request $request)
    {

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $fecha_actual   = Date('Y-m-d H:i:s');
        $fecha_pag      = Date('Y-m-d');
        $id_rol         = $request['id_rol'];
        $sueldo_mensual = $request['sueldo_recibir'];
        $id_nom         = $request['id_nomina'];

        if ($sueldo_mensual != null) {

            $total_ingreso = ($sueldo_mensual) + ($request['sobre_tiempo_50']) + ($request['sobre_tiempo_100']) + ($request['valor_bono']) + ($request['bono_imputable']) + ($request['fondo_reserva']) + ($request['decimo_tercero']) + ($request['decimo_cuarto']) + ($request['alimentacion']) + ($request['transporte']) + ($request['vacaciones']);
        }

        //Calculo Total Egresos
        $total_egreso = ($request['iess']) + ($request['valor_multa']) + ($request['exam_laboratorio']) + ($request['prestamo_empleado']) + ($request['saldo_inicial']) + ($request['anticipo_quincena']) + ($request['otro_anticipo']) + ($request['seguro_privado']) + ($request['impuesto_renta']) + ($request['total_val_quot_quir']) + ($request['total_val_quot_hip']) + ($request['fond_res_cobrar_trab']) + ($request['otros_egresos_trab']);

        //Calculo Neto Recibido
        $neto_recibir = 0;
        if ($total_ingreso > $total_egreso) {
            $neto_recibir = $total_ingreso - $total_egreso;
        }

        /*Calculo de BaseIess*/
        $base_iess = ($sueldo_mensual) + ($request['sobre_tiempo_50']) + ($request['sobre_tiempo_100']) + ($request['bono_imputable']);

        //Recupera registros a Actualizar
        $r_pago      = Ct_Rol_Pagos::findOrFail($id_rol);
        $r_detalle   = Ct_Detalle_Rol::where('id_rol', $id_rol)->where('estado', '1')->first();
        $r_f_pago    = Ct_Rol_Forma_Pago::where('id_rol_pago', $id_rol)->where('estado', '1')->first();
        $cuot_r_quir = Ct_Rh_Cuotas_Quirografario::where('id_rol', $id_rol)->where('estado', '1')->get();
        $cuot_r_hip  = Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $id_rol)->where('estado', '1')->get();

        //Actualiza Rol de Pago
        $input_rol_pago = [
            //'id_nomina'                   => $request['id_nomina'],
            //'id_user'                     => $request['id_user'],
            //'id_empresa'                  => $request['id_empresa'],
            'anio'            => $request['year'],
            'mes'             => $request['mes'],
            'id_tipo_rol'     => $request['tipo_rol'],
            'neto_recibido'   => $neto_recibir,
            //'fecha_elaboracion'           => $fecha_actual,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ];

        $r_pago->update($input_rol_pago);

        //Actualiza Detalle Rol
        $input_detalle_rol = [

            //'id_rol'                    => $id_rol_pago,
            'dias_laborados'            => $request['dias_laborados'],
            'sueldo_mensual'            => $request['sueldo_recibir'],
            'cantidad_horas50'          => $request['cant_horas_50'],
            'sobre_tiempo50'            => $request['sobre_tiempo_50'],
            'cantidad_horas100'         => $request['cant_horas_100'],
            'sobre_tiempo100'           => $request['sobre_tiempo_100'],
            'bonificacion'              => $request['valor_bono'],
            'alimentacion'              => $request['alimentacion'],
            'transporte'                => $request['transporte'],
            'vacaciones'                => $request['vacaciones'],
            'bono_imputable'            => $request['bono_imputable'],
            'exam_laboratorio'          => $request['exam_laboratorio'],
            'fondo_reserva'             => $request['fondo_reserva'],
            'decimo_tercero'            => $request['decimo_tercero'],
            'decimo_cuarto'             => $request['decimo_cuarto'],
            'porcentaje_iess'           => $request['iess'],
            'base_iess'                 => $base_iess,
            'seguro_privado'            => $request['seguro_privado'],
            'impuesto_renta'            => $request['impuesto_renta'],
            'multa'                     => $request['valor_multa'],
            'fond_reserv_cobrar'        => $request['fond_res_cobrar_trab'],
            'otros_egresos'             => $request['otros_egresos_trab'],
            'prestamos_empleado'        => $request['prestamo_empleado'],
            'saldo_inicial_prestamo'    => $request['saldo_inicial'],
            'anticipo_quincena'         => $request['anticipo_quincena'],
            'observacion_bono'          => $request['observacion_bono'],
            'observacion_alimentacion'  => $request['observacion_alimentacion'],
            'observ_seg_privado'        => $request['observacion_seg_priv'],
            'observ_imp_renta'          => $request['observacion_imp_rent'],
            'otro_anticipo'             => $request['otro_anticipo'],
            'observacion_multa'         => $request['observ_multa'],
            'observacion_fondo_cobrar'  => $request['obs_fond_cob_trab'],
            'observacion_prestamo'      => $request['concepto_prestamo'],
            'observacion_saldo_inicial' => $request['obser_saldo_inicial'],
            'observacion_anticip_quinc' => $request['concepto_quincena'],
            'observacion_otro_anticip'  => $request['concep_otros_anticipos'],
            'observacion_otro_egreso'   => $request['obs_otros_egres_trab'],
            'observacion_transporte'    => $request['observacion_transporte'],
            'observacion_vacaciones'    => $request['observacion_vacaciones'],
            'observacion_bonoimp'       => $request['observacion_bonoimp'],
            'observ_examlaboratorio'    => $request['observ_examlaboratorio'],
            'observacion_otro_egreso'   => $request['obs_otros_egres_trab'],
            'total_ingresos'            => $total_ingreso,
            'total_egresos'             => $total_egreso,
            'neto_recibido'             => $neto_recibir,
            'total_quota_quirog'        => $request['total_val_quot_quir'],
            'total_quota_hipot'         => $request['total_val_quot_hip'],
            'id_usuariocrea'            => $idusuario,
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
            'ip_creacion'               => $ip_cliente,
            'ip_modificacion'           => $ip_cliente,

        ];

        $r_detalle->update($input_detalle_rol);

        //Actualiza Forma de Pago
        /*$input_forma_pago = [

        //'id_rol_pago'             => $id_rol_pago,
        'id_tipo_pago'    => $request['tipo_pago'],
        'id_tipo_cuenta'  => $request['tipo_cuenta'],
        'banco'           => $request['banco'],
        'numero_cuenta'   => $request['numero_cuenta'],
        'num_cheque'      => $request['num_cheque'],
        'valor'           => $neto_recibir,
        'fecha'           => $fecha_pag,
        'id_usuariocrea'  => $idusuario,
        'id_usuariocrea'  => $idusuario,
        'id_usuariomod'   => $idusuario,
        'ip_creacion'     => $ip_cliente,
        'ip_modificacion' => $ip_cliente,

        ];*/

        //ELIMINAMOS LAS FORMAS DE pAGO
        $form_pag_rol = Ct_Rol_Forma_Pago::where('id_rol_pago', $id_rol)->where('estado', '1')->get();

        foreach ($form_pag_rol as $value) {
            $value->delete();
        }

        //Guardado en la Tabla ct_rol_forma_pago
        $variable = $request['contador_pago'];

        for ($i = 0; $i < $variable; $i++) {

            $visibilidad_p = $request['visibilidad_pago' . $i];

            if ($visibilidad_p == 1) {

                Ct_Rol_Forma_Pago::create([

                    'id_rol_pago'     => $id_rol,
                    'id_tipo_pago'    => $request['id_tip_pago' . $i],
                    'banco'           => $request['id_banco_pago' . $i],
                    'numero_cuenta'   => $request['id_cuenta_pago' . $i],
                    'num_cheque'      => $request['numero_pago' . $i],
                    'valor'           => $request['valor' . $i],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                ]);
            }
        }

        //ELIMINAMOS CUOTAS QUIROGRAFARIAS
        foreach ($cuot_r_quir as $value) {
            $value->delete();
        }

        //ELIMINAMOS CUOTAS HIPOTECARIAS
        foreach ($cuot_r_hip as $value) {
            $value->delete();
        }

        //INSERCION CUOTA HIPOTECARIO
        $variable_hip = $request['contador_hip'];

        for ($i = 0; $i < $variable_hip; $i++) {

            $visibilidad_hip = $request['visibilidad_hip' . $i];

            if ($visibilidad_hip == 1) {

                $input_item_hip = [

                    'id_rol'          => $id_rol,
                    'valor_cuota'     => round(($request['valor_cuota_hip' . $i]), 2),
                    'detalle_cuota'   => $request['detalle_cuota_hip' . $i],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,

                ];

                Ct_Rh_Cuotas_Hipotecarios::insert($input_item_hip);
            }
        }

        //INSERCION CUOTA QUIROGRAFARIO
        $variable_quir = $request['contador_quiro'];

        for ($i = 0; $i < $variable_quir; $i++) {

            $visibilidad_quir = $request['visibilidad_quiro' . $i];

            if ($visibilidad_quir == 1) {

                $input_item_quir = [

                    'id_rol'          => $id_rol,
                    'valor_cuota'     => round(($request['valor_cuota_quir' . $i]), 2),
                    'detalle_cuota'   => $request['detalle_cuota_quir' . $i],
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,

                ];

                Ct_Rh_Cuotas_Quirografario::insert($input_item_quir);
            }
        }

        $prestamo = Ct_Rh_Prestamos::where('id_empl', $request['id_user'])->where('estado', '1')->first();

        if (!is_null($prestamo)) {
            $resta = $prestamo->saldo_total - $request['prestamo_empleado'];
            $arr_prestamo = [
                'saldo_total'      => $resta, //restante del prestamo
                'id_usuariomod'    => $idusuario,
                'ip_modificacion'  => $ip_cliente,
            ];

            $prestamo->update($arr_prestamo);

            if ($resta == '0') {
                $arr_prest = [
                    'prest_cobrad'      => '1',
                    'estado'            => '0',
                    'id_usuariomod'     => $idusuario,
                    'ip_modificacion'   => $ip_cliente,
                ];
                $prestamo->update($arr_prest);
            }
        }

        $saldo_inicial = Ct_Rh_Saldos_Iniciales::where('id_empl', $request['id_user'])->where('estado', '1')->first();

        if (!is_null($saldo_inicial)) {
            $rest_sal = $saldo_inicial->saldo_res - $request['saldo_inicial'];
            $arr_saldo = [
                'saldo_res'        => $rest_sal,
                'id_usuariomod'    => $idusuario,
                'ip_modificacion'  => $ip_cliente,
            ];

            $saldo_inicial->update($arr_saldo);

            if ($rest_sal == '0') {
                $arr_sal = [
                    'prest_cobrad'      => '1',
                    'estado'            => '0',
                    'id_usuariomod'     => $idusuario,
                    'ip_modificacion'   => $ip_cliente,
                ];
                $prestamo->update($arr_sal);
            }
        }

        //$r_f_pago->update($input_forma_pago);

        $msj = "ok";

        return ['msj' => $msj, 'id_rol_pago' => $id_rol];

    }

    public function imprimir_rol_pago($id_rolpag)
    {
        $nom_funcion = 'imprimir_rol_pago';
        $rolUsuario = Auth::user()->id_tipo_usuario;

        $rol_pago = Ct_Rol_Pagos::findorfail($id_rolpag);

        if (($rolUsuario != 1) && ($rolUsuario != 20) && ($rolUsuario != 21) && ($rolUsuario != 22)) {
            if ($rol_pago->id_user != Auth::user()->id) {
                return response()->view('errors.404');
            }
        }

        $lista_cuota_quirog = Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $id_rolpag)->get();
        $lista_cuota_hip    = Ct_Rh_Cuotas_Quirografario::where('id_rol', $id_rolpag)->get();

        $registro = Ct_Nomina::where('id', $rol_pago->id_nomina)->first();
        $empresa  = Empresa::where('id', $registro->id_empresa)->first();
        $usuario  = User::where('id', $registro->id_user)->first();
        $id_sucursal =  $registro->id_sucursal;
        $rol_forma_pago = Ct_Rol_Forma_Pago::where('id_rol_pago', $rol_pago->id)->first();

        $detalle_rol = Ct_Detalle_Rol::where('id_rol', $rol_pago->id)->first();

        //$anio_mes = $rol_pago->anio . '-' .  $rol_pago->mes;

        /*$prestam_empl = DB::table('ct_egreso_empleado as ep')
        ->where('ep.id_empleado', $rol_pago->id_user)
        ->groupBy('ep.id_empleado')
        ->select(DB::raw("SUM(ep.monto_descontar) as suma_prest"))
        ->first();*/

        //Nueva Funcionalidad Forma de Pago
        $ct_for_pag = Ct_Rol_Forma_Pago::where('id_rol_pago', $rol_pago->id)->get();

        $firma_responsable = Firma_Usuario::where('responsable_rrhh', '1')->first();
        $firma_recibi = Firma_Usuario::where('id_usuario', $registro->id_user)->first();

        $vistaurl = "contable.rol_pago.pdf_rol_pago";
        $view     = \View::make($vistaurl, compact('rol_pago', 'registro', 'empresa', 'usuario', 'detalle_rol', 'rol_forma_pago', 'lista_cuota_quirog', 'lista_cuota_hip', 'ct_for_pag', 'firma_responsable', 'firma_recibi'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        CtLogRolNomina::guardarLog_descarga_pdf_rol_pago($rol_pago->id,$nom_funcion,$empresa->id,$id_sucursal,'DESCARGA ROL PAGO PDF');
        return $pdf->stream('Comprobante Rol Pago-' . $id_rolpag . '.pdf');
    }

    public function anular($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //Obtenemos la fecha de Hoy
        $fecha_actual = Date('Y-m-d H:i:s');
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $estado_pagos = Ct_Rol_Pagos::where('id', $id)->where('estado', '<>', 0)->first();
        if (!empty($estado_pagos)) {
            $act_estado = [
                'estado' => '0',
            ];

            $rol_pag = Ct_Rol_Pagos::findorfail($id);

            Ct_Rol_Pagos::where('id', $id)->update($act_estado);

            //VERIFICAMOS CT_ASIENTO_CABECERA

            /*$consulta_cabecera_rolpago = Ct_Asientos_Cabecera::where('estado', '1')
            ->where('id',$rol_pag->id_asiento)->first();

            $text  = 'Anulaciòn Pago de Sueldo Empleado';

            $input_cabecera = [

            'fecha_asiento'   => $fecha_actual,
            'id_empresa'      => $rol_pag->id_empresa,
            'observacion'     => $text,
            'valor'           => $rol_pag->neto_recibido,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $consulta_detalle = Ct_Asientos_Detalle::where('id_asiento_cabecera', $consulta_cabecera_rolpago->id)->get();

            if ($consulta_detalle != '[]'){

            foreach ($consulta_detalle as $value) {

            Ct_Asientos_Detalle::create([
            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => $value->id_plan_cuenta,
            'descripcion'         => 'ANULACION ROL DE PAGO',
            'fecha'               => $fecha_actual,
            'haber'               => $value->debe,
            'debe'                => $value->haber,
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,
            ]);
            }

            } */

            //return redirect()->intended('/contable/rol/pago/index');
        }
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $empleado   = Ct_Nomina::find($request['id_nomina']);
        $id_auth    = Auth::user()->id;

        $constraints = [
            'anio'       => $request['year'],
            'mes'        => $request['mes'],
            'id_user'    => $request['identificacion'],
            'estado'     => 1,
            'id_empresa' => $id_empresa,

        ];

        $rol_pag = $this->doSearchingQuery($constraints);

        if ($id_auth == '0922729587') {
            //dd($rol_pag);
        }

        $id_nomina = $request['id_nomina'];

        return view('contable/rol_pago/index', ['request' => $request, 'rol_pag' => $rol_pag, 'id_nomina' => $id_nomina, 'searchingVals' => $constraints, 'empresa' => $empresa, 'empleado' => $empleado]);
    }

    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Rol_Pagos::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }

        return $query->paginate(10);
    }

    public function buscar_cuentas(Request $request)
    {
        $id_empresa = $request->session()->get('id_empresa');
        $cuentas  = [];
        if ($request['search'] != null) {

            $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->where('pe.nombre', 'LIKE', '%' . $request['search'] . '%')->select('pe.id_plan as id', 'pe.nombre as text')->get();
        }

        return response()->json($cuentas);
    }

    /***************************************************
     ********LISTADO ROLES DE PAGO EMPLEADOS*************
    /**************************************************/
    public function buscador_index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $roles_pago = Ct_Rol_Pagos::where('estado', '1')->orderby('id', 'asc')->paginate(5);

        $empresas = Empresa::all();

        $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->get();

        return view('contable/rol_pago/buscador_roles_index', ['roles_pago' => $roles_pago, 'empresas' => $empresas, 'id_empresa' => $id_empresa, 'cuentas' => $cuentas, 'empresa' => $empresa]);
    }

    /***************************************************
     ********BUSCADOR ROLES DE PAGO EMPLEADOS************
    /**************************************************/
    public function buscador_roles(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        // $id_empresa       = $request->session()->get('id_empresa');
        $empresa          = $request['id_empresa'];
        $id_anio          = $request['year'];
        $id_mes           = $request['mes'];
        $id_sucursal      = $request->sucursal;
        
        $rol_det_consulta = DB::table('ct_rol_pagos as rp')
            ->where('rp.id_empresa', $id_empresa)
            ->where('rp.anio', $id_anio)
            ->where('rp.estado', '1')
            ->join('ct_detalle_rol as drol', 'drol.id_rol', 'rp.id')
            ->join('users as u', 'rp.id_user', 'u.id')
            ->join('ct_nomina as n', 'n.id', 'rp.id_nomina')
            ->select(
                'rp.id_empresa as idempresa',
                'rp.anio as anio',
                'rp.mes as mes',
                'rp.id_user as usuario',
                'rp.id_nomina as id_nomina',
                'rp.estado as estado_rol',
                'rp.id as id_rol',
                'drol.sueldo_mensual as sueldo',
                'drol.cantidad_horas50 as cantidad_horas_50',
                'drol.sobre_tiempo50 as valor_horas_50',
                'drol.cantidad_horas100 as cantidad_horas_100',
                'drol.sobre_tiempo100 as valor_horas_100',
                'drol.bonificacion as bonificacion',
                'drol.transporte as transporte',
                'drol.vacaciones as vacaciones',
                'drol.bono_imputable as bono_imputable',
                'drol.exam_laboratorio as exam_laboratorio',
                'drol.alimentacion as alimentacion',
                'drol.parqueo as parqueo',
                'drol.fondo_reserva as fondo_reserva',
                'drol.decimo_tercero as decimo_tercero',
                'drol.decimo_cuarto as decimo_cuarto',
                'drol.porcentaje_iess as porcentaje_iess',
                'drol.multa as multa',
                'drol.prestamos_empleado as prestamo_empleado',
                'drol.saldo_inicial_prestamo as saldo_inicial',
                'drol.anticipo_quincena as anticipo_quincena',
                'drol.otro_anticipo as otro_anticipo',
                'drol.total_ingresos as total_ingreso',
                'drol.total_egresos as total_egreso',
                'drol.neto_recibido as neto_recibido',
                'drol.seguro_privado as seguro_privado',
                'drol.impuesto_renta as impuesto_renta',
                'drol.total_quota_quirog as total_quir',
                'drol.total_quota_hipot as total_hip',
                'drol.fond_reserv_cobrar as fond_reserv_cobr',
                'drol.otros_egresos as otro_egres',
                'drol.dias_laborados as dias_laborados',
                'drol.base_iess as base_iess',
                'drol.extension_conyuge as extension_conyuge',
                'rp.pasante as pasante',
                'rp.jornada as jornada',
                'u.apellido1',
                'n.id_sucursal',
                'drol.fondo_reserva_acumulado as fondo_reserva_acumulado',
                'drol.decimo_tercero_acumulado as decimo_tercero_acumulado',
                'drol.decimo_cuarto_acumulado as decimo_cuarto_acumulado',
                'drol.valor_vacacion as valor_vacacion',
                'n.fecha_ingreso as fecha_ingreso',
                'n.fecha_ant as fecha_anterior'
            );

            if($id_sucursal != null){
                $rol_det_consulta = $rol_det_consulta->where('n.id_sucursal',$id_sucursal);
            }
            if(!is_null($id_mes)){
                $rol_det_consulta = $rol_det_consulta->where('rp.mes', $id_mes);
            }    

            $rol_det_consulta = $rol_det_consulta->orderby('u.apellido1', 'asc')->get();
            //dd($rol_det_consulta);

        return view('contable.rol_pago.resultado_busqueda_roles', ['rol_det_consulta' => $rol_det_consulta]);
    }

    /***************************************************
     ***********GENERAR ASIENTOS DE DIARIO***************
    /**************************************************/
    public function store_asientos_diario(Request $request)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $fecha_actual = Date("{$request->fecha} H:i:s");
        //$id_empresa = $request['id_empresa'];
        $id_empresa     = $request->session()->get('id_empresa');
        $anio           = $request['year'];
        $mes            = $request['mes'];
        $cuenta_destino = $request['id_cuenta_destino'];
        $opcion_store   = 0;

        $fechas = $request->fecha;
        $fecha = explode("-", $fechas);
        $mes1 = intval($fecha[1]);
        $anio1 = intval($fecha[0]);
        //dd($anio);

        $txt_mes = '';
        if ($mes == 12) {
            $txt_mes = 'DICIEMBRE';
        } elseif ($mes == 11) {
            $txt_mes = 'NOVIEMBRE';
        } elseif ($mes == 10) {
            $txt_mes = 'OCTUBRE';
        } elseif ($mes == 9) {
            $txt_mes = 'SEPTIEMBRE';
        } elseif ($mes == 8) {
            $txt_mes = 'AGOSTO';
        } elseif ($mes == 7) {
            $txt_mes = 'JULIO';
        } elseif ($mes == 6) {
            $txt_mes = 'JUNIO';
        } elseif ($mes == 5) {
            $txt_mes = 'MAYO';
        } elseif ($mes == 4) {
            $txt_mes = 'ABRIL';
        } elseif ($mes == 3) {
            $txt_mes = 'MARZO';
        } elseif ($mes == 2) {
            $txt_mes = 'FEBRERO';
        } elseif ($mes == 1) {
            $txt_mes = 'ENERO';
        }

        /*Verifica si Existe Asiento de Diario Registrado por Empresa Año Mes*/
        /*$existe_asiento = Ct_Asientos_Cabecera::where('ct_asientos_cabecera.estado', '1')
            ->where('ct_asientos_cabecera.observacion', 'like', '%' . $id_empresa . '%')
            ->where('ct_asientos_cabecera.observacion', 'like', '%' . $anio . '%')
            ->where('ct_asientos_cabecera.observacion', 'like', '%' . $txt_mes . '%')
            ->select('ct_asientos_cabecera.observacion as observacion')
            ->first();*/

        $existe_asiento = DB::table('rol_asiento')
            ->where('id_empresa', $id_empresa)
            ->where('anio', $anio1)
            ->where('mes', $mes1)
            ->where('estado', '1')
            ->first();
        //dd($existe_asiento);
        //dd("hola");
        if (!is_null($existe_asiento)) {
            $msj = "ok";
            return ['msj' => $msj, 'mensaje' => "Ya existe un Asiento Creado en el Año : {$anio} Mes : {$txt_mes}"];
        } else {
            //dd("{$anio} - {$mes} - {$id_empresa}");
            $total_sum = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
                ->where('ct_rol_pagos.anio', $anio)
                ->where('ct_rol_pagos.mes', $mes)
                ->where('ct_rol_pagos.id_empresa', $id_empresa)
                ->join('ct_detalle_rol as drol', 'drol.id_rol', 'ct_rol_pagos.id')
                ->select(
                    DB::raw("SUM(drol.sueldo_mensual) as total_sueldo"),
                    DB::raw("SUM(drol.sobre_tiempo50) as total_horas_50"),
                    DB::raw("SUM(drol.sobre_tiempo100) as total_horas_100"),
                    DB::raw("SUM(drol.bonificacion) as total_bono"),
                    DB::raw("SUM(drol.alimentacion) as total_alimentacion"),
                    DB::raw("SUM(drol.transporte) as total_transporte"),
                    DB::raw("SUM(drol.vacaciones) as total_vacaciones"),
                    DB::raw("SUM(drol.bono_imputable) as total_bonoimp"),
                    DB::raw("SUM(drol.fondo_reserva) as total_fond_reserva"),
                    DB::raw("SUM(drol.decimo_tercero) as total_decimo_tercero"),
                    DB::raw("SUM(drol.decimo_cuarto) as total_decimo_cuarto"),
                    DB::raw("SUM(drol.porcentaje_iess) as total_iess"),
                    DB::raw("SUM(drol.multa) as total_multa"),
                    DB::raw("SUM(drol.fond_reserv_cobrar) as fond_reser_cob"),
                    DB::raw("SUM(drol.otros_egresos) as otro_egres"),
                    DB::raw("SUM(drol.impuesto_renta) as total_imp_renta"),
                    DB::raw("SUM(drol.seguro_privado) as total_seguro_privado"),
                    DB::raw("SUM(drol.exam_laboratorio) as total_exlaboratorio"),
                    DB::raw("SUM(drol.saldo_inicial_prestamo) as total_sald_inicial"),
                    DB::raw("SUM(drol.prestamos_empleado) as total_prestamos"),
                    DB::raw("SUM(drol.anticipo_quincena) as total_anticipos"),
                    DB::raw("SUM(drol.otro_anticipo) as total_otro_anticipo"),
                    DB::raw("SUM(drol.total_ingresos) as total_ingreso"),
                    DB::raw("SUM(drol.total_egresos) as total_egresos"),
                    DB::raw("SUM(drol.neto_recibido) as total_neto_recibido"),
                    DB::raw("SUM(drol.total_quota_quirog) as total_quot_quirog"),
                    DB::raw("SUM(drol.total_quota_hipot) as total_quot_hipot"),
                    DB::raw("SUM(drol.parqueo) as parqueo")
                )
                ->first();

            // dd($total_sum);
            if (is_null($total_sum->total_ingreso)) {
                return ['msj' => "ok", 'mensaje' => "No existe roles de pago en el Año : {$anio} Mes : {$txt_mes}, verifique la fecha de la creación del asiento"];
            }
            //Texto de Asiento Dario
            $text = 'Total Roles de Pago' . ':' . ' ' . 'Id_Empresa' . ':' . $id_empresa . ' ' . 'Año' . ':' . $request['year'] . ' ' . 'Mes' . ':' . $txt_mes;
            //dd($total_sum->total_ingreso);


            $input_cabecera = [
                'fecha_asiento'   => $fecha_actual,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $total_sum->total_ingreso,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $input_rol_asiento = [
                'id_asiento'      => $id_asiento_cabecera,
                'fecha_asiento'   => $fecha_actual,
                'id_empresa'      => $id_empresa,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'id_empresa'      => $id_empresa,
                'anio'            => $anio1,
                'mes'             => $mes1,
                'estado'          =>  1
            ];
            DB::table('rol_asiento')->insert($input_rol_asiento);

            /* PAGO DE SUELDOS Y SALARIOS HORAS EXTRAS AL 50 Y AL 100 BONO IMPUTABLE SUELDO GASTO */

            $sueldo_salario = $total_sum->total_sueldo;

            //$val_bono_imput = $total_sum->total_bonoimp;
            //$sobre_tiempo_50  = $total_sum->total_horas_50;
            //$sobre_tiempo_100 = $total_sum->total_horas_100;

            if ($sueldo_salario > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.01')->first(); //ROLPAGO_SUELDO

                $cuenta = Ct_Configuraciones::obtener_cuenta('ROLPAGO_SUELDO');


                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuenta->cuenta_guardar,
                    'descripcion'         => $cuenta->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => $sueldo_salario + $total_sum->total_bonoimp,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }



            /*Suma de Horas extras*/

            $sobre_tiempo_50  = $total_sum->total_horas_50;
            $sobre_tiempo_100 = $total_sum->total_horas_100;

            if (($sobre_tiempo_50 > 0) || ($sobre_tiempo_100 > 0)) {

                $total_sobre_tiempo = $sobre_tiempo_50 + $sobre_tiempo_100;
                //$plan_cuentas       = Plan_Cuentas::where('id', '5.2.02.01.02')->first();
                //5.2.02.01.02  ROL_PAGOS_HORAS_EXTRAS
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_HORAS_EXTRAS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '5.2.02.01.02',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => $total_sobre_tiempo,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }


            /* BONIFICACION ESPECIAL GASTO */

            $val_bonif = $total_sum->total_bono;

            if ($val_bonif > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.03')->first();
                // 5.2.02.03.03   ROL_PAGOS_BONIFICACION_ESPECIAL
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_BONIFICACION_ESPECIAL');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '5.2.02.03.03',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => $val_bonif,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* ALIMENTACION GASTO */
            $val_alimentacion = $total_sum->total_alimentacion;

            if ($val_alimentacion > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.01')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_ALIMENTACION');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '5.2.02.03.01',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => $val_alimentacion,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* FONDO RESERVA GASTO */
            $val_fond_reserv = $total_sum->total_fond_reserva;

            if ($val_fond_reserv > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.02.03')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '5.2.02.02.03',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => $val_fond_reserv,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* DECIMO TERCERO GASTO */
            $val_decim_terc = $total_sum->total_decimo_tercero;

            if ($val_decim_terc > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.04')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOTERCER_SUELDO');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '5.2.02.01.04',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => $val_decim_terc,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* DECIMO CUARTO SUELDO GASTO */
            $val_decim_cuart = $total_sum->total_decimo_cuarto;

            if ($val_decim_cuart > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.05')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOCUARTO_SUELDO');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '5.2.02.01.05',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => $val_decim_cuart,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* APORTE INDIVIDUAL 9.45% PASIVO */
            $aporte_individual = $total_sum->total_iess;

            if ($aporte_individual > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.01')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_APORTE_INDIVIDUAL_9.4');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '2.01.07.03.01',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $aporte_individual,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /*  MODIFICADO CORRECTO SEGURO ASISTENCIA MEDICA CXC SEGURO CUENTA X COBRAR SALUD GASTO */
            $val_seg_privado = $total_sum->total_seguro_privado;

            if ($val_seg_privado > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.02')->first();
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.11')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_CUENTAS_POR_COBRAR_SEGURO');


                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.06.11',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $val_seg_privado,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* BONO IMPUTABLE GASTO */
            /*$val_bono_imput = $total_sum->total_bonoimp;

            if ($val_bono_imput > 0) {

            $plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.04')->first();

            Ct_Asientos_Detalle::create([

            'id_asiento_cabecera' => $id_asiento_cabecera,
            'id_plan_cuenta'      => '5.2.02.03.04',
            'descripcion'         => $plan_cuentas->nombre,
            'fecha'               => $fecha_actual,
            'debe'                => $val_bono_imput,
            'haber'               => '0',
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'ip_modificacion'     => $ip_cliente,

            ]);

            }*/

            /* TRANSPORTE Movilizacion y Transporte */

            $val_transporte = $total_sum->total_transporte;

            if ($val_transporte > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.06')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_MOVILIZACION_TRANSPORTE');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '5.2.02.03.06',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => $val_transporte,
                    'haber'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* IMPUESTO A LA RENTA POR PAGAR PASIVO */

            $val_imp_renta = $total_sum->total_imp_renta;

            if ($val_imp_renta > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.11')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_IMPUESTO_RENTA_PAGAR');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '2.01.07.01.11',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $val_imp_renta,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* CON EL IESS PRESTAMOS HIPOTECARIOS PASIVO */

            $val_prest_hipotecario = $total_sum->total_quot_hipot;

            if ($val_prest_hipotecario > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.06')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_PRESTAMOS_HIPOTECARIOS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '2.01.07.03.06',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $val_prest_hipotecario,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* CON EL IESS PRESTAMOS QUIROGRAFARIO PASIVO */

            $val_prest_quirografario = $total_sum->total_quot_quirog;

            if ($val_prest_quirografario > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.05')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_PRESTAMOS_QUIROGRAFARIOS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '2.01.07.03.05',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $val_prest_quirografario,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* MULTAS EMPLEADOS Y FUNCIONARIO INGRESO */
            $val_multa = $total_sum->total_multa;

            if ($val_multa > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.01')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_MULTAS_EMPLEADOS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '4.1.05.01',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $val_multa,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* FONDO DE RESERVA COBRAR TRABAJADORES MODIFICADO INGRESO */

            $val_res_cob = $total_sum->fond_reser_cob;

            if ($val_res_cob > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.02.03')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '5.2.02.02.03',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'haber'                => $val_res_cob,
                    'debe'               => '0',
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* MODIFICADA OTROS INGRESOS */
            $val_otro_egreso = $total_sum->otro_egres;
            $val_otro_egreso = $val_otro_egreso + $total_sum->parqueo;


            if ($val_otro_egreso > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.04')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_OTROS_INGRESOS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '4.1.05.04',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $val_otro_egreso,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* PRESTAMOS A EMPLEADOS MAS SALDO INICIAL ACTIVO */
            $val_prestamo = $total_sum->total_prestamos;
            $val_prestamo += $total_sum->total_sald_inicial;

            if ($val_prestamo > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.03')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.06.03',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $val_prestamo,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* ANTICIPO SUELDOS EMPLEADOS ACTIV */
            $anticipo_sueldo = $total_sum->total_anticipos;

            if ($anticipo_sueldo > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.04')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('OTROS_ANTICIPOS_EMPLEADOS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.06.04',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $anticipo_sueldo,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* OTROS ANTICIPO */
            $otros_anticipo = $total_sum->total_otro_anticipo;

            if ($otros_anticipo > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.04')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('OTROS_ANTICIPOS_EMPLEADOS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.06.04',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $otros_anticipo,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /* EXAMENES DE LABORATORIO */
            $val_exa_lab = $total_sum->total_exlaboratorio;

            if ($val_exa_lab > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.03')->first();
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    //'id_plan_cuenta'      => '1.01.02.06.03',
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $val_exa_lab,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            /***********************************************
             **********NETO A RECIBIR (CUENTA DESTINO)*******
             ******************ACTIVO************************
            /***********************************************/
            $net_recibir = $total_sum->total_neto_recibido;

            if ($net_recibir > 0) {

                $plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuenta_destino,
                    'descripcion'         => $plan_cuentas->nombre,
                    'fecha'               => $fecha_actual,
                    'debe'                => '0',
                    'haber'               => $net_recibir,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                $opcion_store = 1;
            }

            return ['opcion_store' => $opcion_store];
        }
    }

    /***************************************************
     ***************MODAL EDITAR PAGO********************
    /**************************************************/

    public function obtener_edit_pago($id)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $rol_pag = Ct_Rol_Pagos::where('estado', '1')->where('id', $id)->first();

        return view('contable/rol_pago/mod_edit_tipo_pago', ['tipo_pago_rol' => $tipo_pago_rol, 'rol_pag' => $rol_pag]);
    }

    /***************************************************
     *****************EDITAR ROL PAGO********************
    /**************************************************/
    public function editar_rol($id, Request $request)
    {
        // dd($id);

        $id_empresa = $request->session()->get('id_empresa');

        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $tipo_cuenta = Ct_Rh_Tipo_Cuenta::all();

        $lista_banco = Ct_Bancos::all();

        $rol_pag = Ct_Rol_Pagos::where('estado', '1')->where('id', $id)->first();

        $inf_nomina = Ct_Nomina::where('estado', '1')->where('id', $rol_pag->id_nomina)->first();

        // dd($inf_nomina->decimo_cuarto);

        //Obtengo el Valor Configuracion Fondo Reserva
        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $inf_nomina->id_empresa)
            ->where('tipo', 4)->first();

        //Obtengo el Valor Configuracion Aporte Personal
        $val_aport_pers = Ct_Rh_Valores::where('id_empresa', $inf_nomina->id_empresa)
            ->where('tipo', 1)->where('id', $inf_nomina->aporte_personal)->first();

        //Obtengo el Valor Configuracion Salario Basico
        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $inf_nomina->id_empresa)
            ->where('tipo', 3)->first();

        $ct_tipo_rol = Ct_Tipo_Rol::all();

        $deta_rol = Ct_Detalle_Rol::where('estado', '1')->where('id_rol', $rol_pag->id)->first();
        //dd($deta_rol);
        $form_pago = Ct_Rol_Forma_Pago::where('estado', '1')->where('id_rol_pago', $rol_pag->id)->first();

        $xcantidad_quirografario = Ct_Rh_Cuotas_Quirografario::where('id_rol', $id)->get()->count();
        $xcantidad_hipotecario   = Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $id)->get()->count();

        //dd($xcantidad_quirografario, $xcantidad_hipotecario);
        //Obtenemos las Formas de Pago
        /*$forma_pago = DB::table('ct_forma_pago')->where('id_ct_ventas', $id)
        ->where('estado', '1')
        ->get();*/

        //Nueva Funcionalidad Forma de pago
        $forma_pago = Ct_Rol_Forma_Pago::where('estado', '1')->where('id_rol_pago', $rol_pag->id)->get();
        //$tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();

        return view('contable/rol_pago/editar_rol', ['empresa' => $empresa, 'rol_pag' => $rol_pag, 'ct_tipo_rol' => $ct_tipo_rol, 'inf_nomina' => $inf_nomina, 'deta_rol' => $deta_rol, 'val_fond_reserv' => $val_fond_reserv, 'val_aport_pers' => $val_aport_pers, 'val_sal_basico' => $val_sal_basico, 'tipo_pago_rol' => $tipo_pago_rol, 'tipo_cuenta' => $tipo_cuenta, 'lista_banco' => $lista_banco, 'xcantidad_quirografario' => $xcantidad_quirografario, 'xcantidad_hipotecario' => $xcantidad_hipotecario, 'form_pago' => $form_pago, 'forma_pago' => $forma_pago]);
    }

    /**************************************************
     **************CARGA TABLA QUIROGRAFARIO************
    /**************************************************/
    public function listado_cuota_quirografario($id)
    {

        $cuota_quirografario = Ct_Rh_Cuotas_Quirografario::where('id_rol', $id)->where('estado', '1')->get();

        return view('contable/rol_pago/listar_quirografario', ['cuota_quirografario' => $cuota_quirografario]);
    }

    /**************************************************
     **************CARGA TABLA HIPOTECARIO**************
    /**************************************************/

    public function listado_cuota_hipotecario($id)
    {

        $cuota_hipotecario = Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $id)->where('estado', '1')->get();

        return view('contable/rol_pago/listar_hipotecario', ['cuota_hipotecario' => $cuota_hipotecario]);
    }

    /***************************************************
     ***************VERIFICA SI EXISTE ANTICIPO *********
    /**************************************************/

    public function existe_valor(Request $request)
    {

        //$usuario = User::find($request['identificacion']);
        $existe = Ct_Rh_Valor_Anticipos::where('id_user', $request['identificacion'])
            ->where('id_empresa', $request['id_empresa'])
            ->where('anio', $request['anio'])
            ->where('quincena', $request['mes'])
            ->first();
        $dato = ['existe' => $existe];
        return $dato;
    }

    /***************************************************
     ********VERIFICA SI EXISTE PRESTAMO EMPLEADO *******
    /**************************************************/
    public function verifica_existe_prestamos(Request $request)
    {

        $existe_mes  = 0;
        $val_cuot    = 0;
        $sum_cuot    = 0;
        $val_13      = 13;
        $val_12      = 12;
        $num_cuot    = 0;
        $obser_prest = "";

        $val               = 1;
        $empresa_busqueda  = $request['id_empresa'];
        $empleado_busqueda = $request['identificacion'];
        $mes_busqueda      = $request['mes'];
        $anio_busqueda     = $request['anio'];
        $anio_f            = 0;

        $existe_empleado = Ct_Rh_Prestamos::where('id_empl', $empleado_busqueda)
            ->where('id_empresa', $empresa_busqueda)
            ->where('ct_rh_prestamos.anio_inicio_cobro', '=', $anio_busqueda)
            ->where('estado', '1')
            ->get();
        //dd($existe_empleado);

        if ($existe_empleado != null) {

            foreach ($existe_empleado as $prest) {

                $num_cuot = $prest->num_cuotas;
                $mes_ini  = $prest->mes_inicio_cobro;
                $anio_ini = $prest->anio_inicio_cobro;

                $val_cuot    = $prest->valor_cuota;
                $obser_prest = $prest->concepto;

                $sum_mes = ($num_cuot) + ($mes_ini);

                if ($prest->mes_aux != null) {

                    for ($i = $val; $i <= ($prest->mes_aux); $i++) {

                        if ($i == $mes_busqueda) {

                            $existe_mes = 1;
                            $sum_cuot   = $sum_cuot + $val_cuot;

                            if ($mes_busqueda == ($prest->mes_aux)) {

                                $act_est_prest = [
                                    'prest_cobrad' => $val,
                                ];

                                Ct_Rh_Prestamos::where('id', $prest->id)->update($act_est_prest);
                            }

                            /*return ['existe_mes' => $existe_mes, 'val_cuot' => $val_cuot, 'obser_prest' => $obser_prest];*/
                        }
                    }
                }

                if ($sum_mes <= $val_13) {

                    $mes_fin = ($sum_mes) - ($val);
                    $anio_f  = $anio_ini;

                    if ($mes_fin >= 1) {

                        for ($i = $mes_ini; $i <= $mes_fin; $i++) {

                            if ($i == $mes_busqueda) {

                                $existe_mes = 1;
                                $sum_cuot   = $sum_cuot + $val_cuot;

                                /*return ['existe_mes' => $existe_mes, 'val_cuot' => $val_cuot, 'obser_prest' => $obser_prest];*/
                            }
                        }
                    }
                } else if ($sum_mes > $val_13) {

                    $mes_fin = ($sum_mes) - ($val_13);
                    $anio_f  = $anio_ini + 1;

                    for ($i = $mes_ini; $i <= 12; $i++) {

                        if ($i == $mes_busqueda) {

                            $existe_mes = 1;
                            $sum_cuot   = $sum_cuot + $val_cuot;

                            if ($mes_busqueda == $val_12) {

                                $act_anio_ini = [
                                    'anio_inicio_cobro' => $anio_ini + $val,
                                    'mes_aux'           => $mes_fin,
                                ];

                                Ct_Rh_Prestamos::where('id', $prest->id)->update($act_anio_ini);
                            }

                            /*return ['existe_mes' => $existe_mes, 'val_cuot' => $val_cuot, 'obser_prest' => $obser_prest];*/
                        }
                    }
                }
            }

            return ['existe_mes' => $existe_mes, 'val_cuot' => $sum_cuot, 'obser_prest' => $obser_prest];
        }
    }

    /******************************************************************
     ***************VERIFICA EXISTE SALDO INICIAL PRESTAMOs*************
    /*****************************************************************/
    public function verifica_existe_saldo_inicial(Request $request)
    {

        $val_cuot = 0;
        $val_13   = 13;
        $val_12   = 12;
        $num_cuot = 0;

        $val               = 1;
        $empresa_busqueda  = $request['id_empresa'];
        $empleado_busqueda = $request['identificacion'];
        $mes_busqueda      = $request['mes'];
        $anio_busqueda     = $request['anio'];
        $anio_f            = 0;
        $anio_ini          = 0;
        $sum_mes           = 0;

        $existe_empleado = Ct_Rh_Saldos_Iniciales::where('ct_rh_saldos_iniciales.id_empl', $request['identificacion'])
            ->where('ct_rh_saldos_iniciales.id_empresa', $request['id_empresa'])
            ->where('ct_rh_saldos_iniciales.anio_inicio_cobro', '=', $request['anio'])
            ->first();
        if ($existe_empleado != null) {

            $num_cuot = $existe_empleado->num_cuotas;
            $mes_ini  = $existe_empleado->mes_inicio_cobro;
            $anio_ini = $existe_empleado->anio_inicio_cobro;

            $val_cuot    = $existe_empleado->valor_cuota;
            $obser_prest = $existe_empleado->observacion;

            $sum_mes = ($num_cuot) + ($mes_ini);

            if ($existe_empleado->mes_aux != null) {

                for ($i = $val; $i <= ($existe_empleado->mes_aux); $i++) {

                    if ($i == $mes_busqueda) {

                        $existe_mes = 1;
                        $val_cuot   = $val_cuot;

                        if ($mes_busqueda == ($existe_empleado->mes_aux)) {

                            $act_est_saldo = [
                                'saldo_cobrad' => $val,
                            ];

                            Ct_Rh_Saldos_Iniciales::where('id', $existe_empleado->id)->update($act_est_saldo);
                        }

                        return ['existe_mes' => $existe_mes, 'val_cuot' => $val_cuot, 'obser_prest' => $obser_prest];
                    }
                }
            }
        }

        if ($sum_mes <= $val_13) {

            $mes_fin = ($sum_mes) - ($val);
            $anio_f  = $anio_ini;

            if ($mes_fin >= 1) {

                for ($i = $mes_ini; $i <= $mes_fin; $i++) {

                    if ($i == $mes_busqueda) {

                        $existe_mes = 1;
                        $val_cuot   = $val_cuot;

                        return ['existe_mes' => $existe_mes, 'val_cuot' => $val_cuot, 'obser_prest' => $obser_prest];
                    }
                }
            }
        } else if ($sum_mes > $val_13) {

            $mes_fin = ($sum_mes) - ($val_13);
            $anio_f  = $anio_ini + 1;

            for ($i = $mes_ini; $i <= 12; $i++) {

                if ($i == $mes_busqueda) {

                    $existe_mes = 1;
                    $val_cuot   = $val_cuot;

                    if ($mes_busqueda == $val_12) {

                        $act_anio_ini = [
                            'anio_inicio_cobro' => $anio_ini + $val,
                            'mes_aux'           => $mes_fin,
                        ];

                        Ct_Rh_Saldos_Iniciales::where('id', $existe_empleado->id)->update($act_anio_ini);
                    }

                    return ['existe_mes' => $existe_mes, 'val_cuot' => $val_cuot, 'obser_prest' => $obser_prest];
                }
            }
        }
    }

    /******************************************************************
     *********VERIFICA SI EXISTE ANTICIPO 1ERA QUINCENA EMPLEADO********
    /*****************************************************************/
    public function verifica_existe_anticipo(Request $request)
    {

        $existe_anticipo = 0;
        $existe_mes      = 0;
        $mont_anticip    = 0.00;
        $concept_anticip = "";
        //$val_cuot = 0;
        $empresa_busqueda  = $request['id_empresa'];
        $empleado_busqueda = $request['identificacion'];
        $mes_busqueda      = $request['mes'];
        $anio_busqueda     = $request['anio'];

        /*$existe_empleado = Ct_Rh_Anticipos::where('id_empl', $request['identificacion'])
        ->where('id_empresa',$request['id_empresa'])
        ->where('anio_inicio_cobro',$request['anio'])
        ->where('anio_fin_cobro',$request['anio'])
        ->get();*/

        /*if ($existe_empleado != null){

        foreach ($existe_empleado as $anticip) {

        for($i = $anticip->mes_inicio_cobro; $i <= $anticip->mes_fin_cobro; $i++){

        if($i == $mes_busqueda){

        $existe_mes = 1;
        $val_cuot = $anticip->valor_cuota;

        }
        }

        }

        }*/

        /*return ['existe_mes' => $existe_mes, 'val_cuot' => $val_cuot];*/

        $existe_empleado = Ct_Rh_Valor_Anticipos::where('id_user', $request['identificacion'])
            ->where('id_empresa', $request['id_empresa'])
            ->where('mes', $request['mes'])
            ->where('anio', $request['anio'])
            ->where('estado', '1')
            ->first();

        if ($existe_empleado != null) {

            $existe_anticipo = 1;
            $existe_mes      = 1;
            $mont_anticip    = $existe_empleado->valor_anticipo;
            $concept_anticip = 'Anticipo 1era Quincena Empleado';
        } else {
            $existe_anticipo = 0;
            $existe_mes      = 0;
        }

        return ['existe_mes' => $existe_mes, 'existe_anticipo' => $existe_anticipo, 'mont_anticip' => $mont_anticip, 'concept_anticip' => $concept_anticip, 'existe_empleado' => $existe_empleado];
    }

    /****************************************************
     *********VERIFICA SI EXISTE OTRO ANTICIPO EMPLEADO********
    /****************************************************/
    public function verifica_existe_otros_anticipo(Request $request)
    {

        $existe_anticipo = 0;
        $existe_mes      = 0;
        $mont_anticip    = 0.00;
        $concept_anticip = "";
        //$val_cuot = 0;
        $empresa_busqueda  = $request['id_empresa'];
        $empleado_busqueda = $request['identificacion'];
        $mes_busqueda      = $request['mes'];
        $anio_busqueda     = $request['anio'];

        $existe_empleado = Ct_Rh_Otros_Anticipos::where('id_empl', $request['identificacion'])
            ->where('id_empresa', $request['id_empresa'])
            ->where('mes_cobro_anticipo', $request['mes'])
            ->where('anio_cobro_anticipo', $request['anio'])
            ->where('estado', '1')
            ->first();

        $existe = Ct_Rh_Otros_Anticipos::where('id_empl', $request['identificacion'])
            ->where('id_empresa', $request['id_empresa'])
            ->where('mes_cobro_anticipo', $request['mes'])
            ->where('anio_cobro_anticipo', $request['anio'])
            ->where('estado', '1')
            ->get();

        $total_sum = 0;
        $acum_obs = "";
        foreach ($existe as $value) {
            $total_sum += $value->monto_anticipo;
            $acum_obs = $acum_obs . '|' . $value->concepto;
        }

        if ($existe_empleado != null) {

            $existe_anticipo = 1;
            $existe_mes      = 1;
            $mont_anticip    = $existe_empleado->monto_anticipo;
            $concept_anticip = $existe_empleado->concepto;
        } else {
            $existe_anticipo = 0;
            $existe_mes      = 0;
        }

        return ['existe_mes' => $existe_mes, 'existe_anticipo' => $existe_anticipo, 'mont_anticip' => $mont_anticip, 'concept_anticip' => $concept_anticip, 'total_sum' => $total_sum, 'acum_obs' => $acum_obs];
    }

    public function exportar_excel(Request $request)
    {
        //dd("aqui");
        $anio       = $request['year'];
        $mes        = $request['mes'];
        $sucursal  = $request['sucursal'];
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        $mes_rol = ' ';
        if ($mes == '1') {
            $mes_rol = 'ENERO';
        } elseif ($mes == '2') {
            $mes_rol = 'FEBRERO';
        } elseif ($mes == '3') {
            $mes_rol = 'MARZO';
        } elseif ($mes == '4') {
            $mes_rol = 'ABRIL';
        } elseif ($mes == '5') {
            $mes_rol = 'MAYO';
        } elseif ($mes == '6') {
            $mes_rol = 'JUNIO';
        } elseif ($mes == '7') {
            $mes_rol = 'JULIO';
        } elseif ($mes == '8') {
            $mes_rol = 'AGOSTO';
        } elseif ($mes == '9') {
            $mes_rol = 'SEPTIEMBRE';
        } elseif ($mes == '10') {
            $mes_rol = 'OCTUBRE';
        } elseif ($mes == '11') {
            $mes_rol = 'NOVIEMBRE';
        } elseif ($mes == '12') {
            $mes_rol = 'DICIEMBRE';
        }

        $rol_det_consulta = Ct_Rol_Pagos::where('ct_rol_pagos.anio', $anio)
            ->where('ct_rol_pagos.mes', $mes)
            ->where('ct_rol_pagos.id_empresa', $id_empresa)
            ->where('ct_rol_pagos.estado', '1')
            ->join('ct_detalle_rol as drol', 'drol.id_rol', 'ct_rol_pagos.id')
            ->join('users as us', 'ct_rol_pagos.id_user', '=', 'us.id')
            ->join('ct_nomina as nom', 'nom.id', 'ct_rol_pagos.id_nomina')
            ->join('ct_sucursales as scrsal', 'scrsal.id', 'nom.id_sucursal')
            ->where('drol.estado', '1')
            ->orderBy('us.apellido1')
            ->select(
                'us.id',
                'us.nombre1',
                'us.nombre2',
                'us.apellido1',
                'us.apellido2',
                'drol.sueldo_mensual',
                'drol.cantidad_horas50',
                'drol.sobre_tiempo50',
                'drol.cantidad_horas100',
                'drol.sobre_tiempo100',
                'drol.bonificacion',
                'drol.alimentacion',
                'drol.vacaciones',
                'drol.parqueo',
                'drol.bono_imputable',
                'drol.transporte',
                'drol.fondo_reserva',
                'drol.fondo_reserva_acumulado',
                'drol.decimo_tercero',
                'drol.decimo_cuarto',
                'drol.total_ingresos',
                'drol.anticipo_quincena',
                'drol.porcentaje_iess',
                'drol.otro_anticipo',
                'drol.prestamos_empleado',
                'drol.seguro_privado',
                'drol.total_quota_quirog',
                'drol.total_quota_hipot',
                'drol.multa',
                'drol.exam_laboratorio',
                'drol.total_egresos',
                'drol.neto_recibido',
                'drol.saldo_inicial_prestamo',
                'drol.impuesto_renta',
                'drol.dias_laborados as dias_laborados',
                'drol.otros_egresos',
                'nom.id_sucursal as id_sucursal'

            );
            if (!is_null($sucursal)) {
                $rol_det_consulta = $rol_det_consulta->where('id_sucursal', $sucursal);
              }
                $rol_det_consulta = $rol_det_consulta->get();
      
        //dd($rol_det_consulta);

        Excel::create('Reporte Roles de Pago' . ':' . ' ' . 'Mes' . ':' . $mes_rol . ' del ' . $anio, function ($excel) use ($rol_det_consulta, $mes_rol, $empresa, $anio) {
            $excel->sheet('Datos Roles de Pago', function ($sheet) use ($rol_det_consulta, $mes_rol, $empresa, $anio) {

                $sheet->mergeCells('A1:AE1');
                $sheet->cell('A1', function ($cell) use ($mes_rol, $empresa, $anio) {
                    $cell->setValue($empresa->nombrecomercial . ' ROL DE PAGOS MES DE ' . $mes_rol . ' ' . $anio);
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    //$cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                });
                $sheet->cell('A2', function ($cell) {
                    $cell->setValue('CEDULA -');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B2', function ($cell) {
                    $cell->setValue('NOMBRE');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    $cell->setValue('SUELDO MENSUAL');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    $cell->setValue('DIAS LABORADOS');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    $cell->setValue('CANTIDAD HORAS AL 50%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    $cell->setValue('SOBRE TIEMPO 50%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    $cell->setValue('CANTIDAD HORAS AL 100%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    $cell->setValue('SOBRE TIEMPO 100%');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    $cell->setValue('BONO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    $cell->setValue('BONO IMPUTABLE');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    $cell->setValue('ALIMENTACIÓN');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L2', function ($cell) {
                    $cell->setValue('TRANSPORTE');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M2', function ($cell) {
                    $cell->setValue('PARQUEO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N2', function ($cell) {
                    $cell->setValue('FONDO RESERVA');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O2', function ($cell) {
                    $cell->setValue('DECIMO TERCERO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P2', function ($cell) {
                    $cell->setValue('DECIMO CUARTO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q2', function ($cell) {
                    $cell->setValue('TOTAL INGRESOS');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R2', function ($cell) {
                    $cell->setValue('ANTICIPO 1ERA QUINC');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S2', function ($cell) {
                    $cell->setValue('APORTES 9.45% IESS');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T2', function ($cell) {
                    $cell->setValue('OTROS ANTICIPOS');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U2', function ($cell) {
                    $cell->setValue('OTROS EGRESOS');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V2', function ($cell) {
                    $cell->setValue('PREST EMPRESA');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W2', function ($cell) {
                    $cell->setValue('SALDO INICIAL PREST');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X2', function ($cell) {
                    $cell->setValue('SEGURO MÉDICO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y2', function ($cell) {
                    $cell->setValue('IMPUESTO RENTA');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Z2', function ($cell) {
                    $cell->setValue('PRÉSTAMO QUIROG');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA2', function ($cell) {
                    $cell->setValue('PRÉSTAMO HIPOTECARIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AB2', function ($cell) {
                    $cell->setValue('MULTAS ATRASO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AC2', function ($cell) {
                    $cell->setValue('EXAMEN DE LABORATORIO');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AD2', function ($cell) {
                    $cell->setValue('SUMAN DESCUENTOS');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AE2', function ($cell) {
                    $cell->setValue('NETO 2DA QUINCENA');
                    $cell->setAlignment('center');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->setColumnFormat(array(
                    'A'  => '0',
                    'C'  => '$ 0.00',
                    'E'  => '0.00',
                    'F'  => '$ 0.00',
                    'G'  => '0.00',
                    'H'  => '$ 0.00',
                    'I'  => '$ 0.00',
                    'J'  => '$ 0.00',
                    'K'  => '$ 0.00',
                    'L'  => '$ 0.00',
                    'M'  => '$ 0.00',
                    'N'  => '$ 0.00',
                    'O'  => '$ 0.00',
                    'P'  => '$ 0.00',
                    'Q'  => '$ 0.00',
                    'R'  => '$ 0.00',
                    'S'  => '$ 0.00',
                    'T'  => '$ 0.00',
                    'U'  => '$ 0.00',
                    'V'  => '$ 0.00',
                    'W'  => '$ 0.00',
                    'X'  => '$ 0.00',
                    'Y'  => '$ 0.00',
                    'Z'  => '$ 0.00',
                    'AA' => '$ 0.00',
                    'AB' => '$ 0.00',
                    'AC' => '$ 0.00',
                    'AD' => '$ 0.00',
                    'AE' => '$ 0.00'

                ));

                $i                   = 3;
                $total_sueldo        = 0;
                $total_cantidad_50   = 0;
                $total_sobret_50     = 0;
                $total_cantidad_100  = 0;
                $total_sobret_100    = 0;
                $total_bono          = 0;
                $total_alimentacion  = 0;
                $total_transporte    = 0;
                $total_parqueo       = 0;
                $total_bonoimp       = 0;
                $total_fond_reserva  = 0;
                $total_dec_tercero   = 0;
                $total_dec_cuarto    = 0;
                $total_ingr          = 0;
                $total_anticip_1eraq = 0;
                $total_aporte_iess   = 0;
                $total_otros_anticip = 0;
                $total_prest_emp     = 0;
                $total_sald_inicial  = 0;
                $total_seg_medico    = 0;
                $total_imp_renta     = 0;
                $total_prest_quirog  = 0;
                $total_prest_hipotec = 0;
                $total_multas        = 0;
                $total_exlaboratorio = 0;
                $total_sum_desc      = 0;
                $total_net_seg       = 0;
                $total_otros_egresos = 0;
                $t_fondo_reserva = 0;

                foreach ($rol_det_consulta as $value) {
                    
                    $t_fondo_reserva = $value->fondo_reserva + $value->fondo_reserva_acumulado;
                    $total_sueldo += $value->sueldo_mensual;
                    $total_cantidad_50 += $value->cantidad_horas50;
                    $total_sobret_50 += $value->sobre_tiempo50;
                    $total_cantidad_100 += $value->cantidad_horas100;
                    $total_sobret_100 += $value->sobre_tiempo100;
                    $total_bono += $value->bonificacion;
                    $total_alimentacion += $value->alimentacion;
                    $total_transporte += $value->transporte;
                    $total_parqueo += $value->parqueo;
                    $total_bonoimp += $value->bono_imputable;
                    $total_fond_reserva += $t_fondo_reserva;
                    $total_dec_tercero += $value->decimo_tercero;
                    $total_dec_cuarto += $value->decimo_cuarto;
                    $total_ingr += $value->total_ingresos;
                    $total_anticip_1eraq += $value->anticipo_quincena;
                    $total_otros_egresos += $value->otros_egresos;
                    $total_aporte_iess += $value->porcentaje_iess;
                    $total_otros_anticip += $value->otro_anticipo;
                    $total_prest_emp += $value->prestamos_empleado;
                    $total_sald_inicial += $value->saldo_inicial_prestamo;
                    $total_seg_medico += $value->seguro_privado;
                    $total_imp_renta += $value->impuesto_renta;
                    $total_prest_quirog += $value->total_quota_quirog;
                    $total_prest_hipotec += $value->total_quota_hipot;
                    $total_multas += $value->multa;
                    $total_exlaboratorio += $value->exam_laboratorio;
                    $total_sum_desc += $value->total_egresos;
                    $total_net_seg += $value->neto_recibido;

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->apellido1 . " " . $value->apellido2 . " " . $value->nombre1 . " " . $value->nombre2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });
                    //INGRESOS
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->sueldo_mensual);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->dias_laborados);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->cantidad_horas50);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->sobre_tiempo50);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->cantidad_horas100);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->sobre_tiempo100);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->bonificacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->bono_imputable);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->alimentacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->transporte);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->parqueo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($t_fondo_reserva) {
                        $cell->setValue($t_fondo_reserva);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->decimo_tercero);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->decimo_cuarto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('Q' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->total_ingresos);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });

                    //EGRESOS
                    //if ($value->anticipo_quincena != '0') {
                        $sheet->cell('R' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->anticipo_quincena);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('right');
                        });
                    //} else {
                        /*$sheet->cell('R' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->otros_egresos);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('right');
                        });*/
                    //}

                    $sheet->cell('S' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->porcentaje_iess);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('T' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->otro_anticipo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('U' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->otros_egresos);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });

                    $sheet->cell('V' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->prestamos_empleado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });

                    $sheet->cell('W' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->saldo_inicial_prestamo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('X' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->seguro_privado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('Y' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->impuesto_renta);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('Z' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->total_quota_quirog);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('AA' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->total_quota_hipot);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('AB' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->multa);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('AC' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->exam_laboratorio);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('AD' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->total_egresos);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });
                    $sheet->cell('AE' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->neto_recibido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('right');
                    });

                    $i++;
                }

                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTALES');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C' . $i, function ($cell) use ($total_sueldo) {
                    // manipulate the cel
                    $cell->setValue($total_sueldo);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D' . $i, function ($cell) use ($total_cantidad_50) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E' . $i, function ($cell) use ($total_cantidad_50) {
                    // manipulate the cel
                    $cell->setValue($total_cantidad_50);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F' . $i, function ($cell) use ($total_sobret_50) {
                    // manipulate the cel
                    $cell->setValue($total_sobret_50);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G' . $i, function ($cell) use ($total_cantidad_100) {
                    // manipulate the cel
                    $cell->setValue($total_cantidad_100);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H' . $i, function ($cell) use ($total_sobret_100) {
                    // manipulate the cel
                    $cell->setValue($total_sobret_100);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I' . $i, function ($cell) use ($total_bono) {
                    // manipulate the cel
                    $cell->setValue($total_bono);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J' . $i, function ($cell) use ($total_bonoimp) {
                    // manipulate the cel
                    $cell->setValue($total_bonoimp);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K' . $i, function ($cell) use ($total_alimentacion) {
                    // manipulate the cel
                    $cell->setValue($total_alimentacion);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L' . $i, function ($cell) use ($total_transporte) {
                    // manipulate the cel
                    $cell->setValue($total_transporte);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M' . $i, function ($cell) use ($total_parqueo) {
                    // manipulate the cel
                    $cell->setValue($total_parqueo);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('N' . $i, function ($cell) use ($total_fond_reserva) {
                    // manipulate the cel
                    $cell->setValue($total_fond_reserva);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('O' . $i, function ($cell) use ($total_dec_tercero) {
                    // manipulate the cel
                    $cell->setValue($total_dec_tercero);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('P' . $i, function ($cell) use ($total_dec_cuarto) {
                    // manipulate the cel
                    $cell->setValue($total_dec_cuarto);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('Q' . $i, function ($cell) use ($total_ingr) {
                    // manipulate the cel
                    $cell->setValue($total_ingr);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('R' . $i, function ($cell) use ($total_anticip_1eraq) {
                    // manipulate the cel
                    $cell->setValue($total_anticip_1eraq);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('S' . $i, function ($cell) use ($total_aporte_iess) {
                    // manipulate the cel
                    $cell->setValue($total_aporte_iess);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('T' . $i, function ($cell) use ($total_otros_anticip) {
                    // manipulate the cel
                    $cell->setValue($total_otros_anticip);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('U' . $i, function ($cell) use ($total_otros_egresos) {
                    // manipulate the cel
                    $cell->setValue($total_otros_egresos);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('V' . $i, function ($cell) use ($total_prest_emp) {
                    // manipulate the cel
                    $cell->setValue($total_prest_emp);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('W' . $i, function ($cell) use ($total_sald_inicial) {
                    // manipulate the cel
                    $cell->setValue($total_sald_inicial);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('X' . $i, function ($cell) use ($total_seg_medico) {
                    // manipulate the cel
                    $cell->setValue($total_seg_medico);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('Y' . $i, function ($cell) use ($total_imp_renta) {
                    // manipulate the cel
                    $cell->setValue($total_imp_renta);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('Z' . $i, function ($cell) use ($total_prest_quirog) {
                    // manipulate the cel
                    $cell->setValue($total_prest_quirog);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AA' . $i, function ($cell) use ($total_prest_hipotec) {
                    // manipulate the cel
                    $cell->setValue($total_prest_hipotec);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AB' . $i, function ($cell) use ($total_multas) {
                    // manipulate the cel
                    $cell->setValue($total_multas);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AC' . $i, function ($cell) use ($total_exlaboratorio) {
                    // manipulate the cel
                    $cell->setValue($total_exlaboratorio);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AD' . $i, function ($cell) use ($total_sum_desc) {
                    // manipulate the cel
                    $cell->setValue($total_sum_desc);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AE' . $i, function ($cell) use ($total_net_seg) {
                    // manipulate the cel
                    $cell->setValue($total_net_seg);
                    $cell->setAlignment('right');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
            });

            // $excel->getActiveSheet()->getColumnDimension("A")->setWidth(35)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("B")->setWidth(20)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("C")->setWidth(25)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("D")->setWidth(20)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("E")->setWidth(28)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("F")->setWidth(24)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("G")->setWidth(8)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("H")->setWidth(18)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(24)->setAutosize(false);
            // $excel->getActiveSheet()->getColumnDimension("M")->setWidth(23)->setAutosize(false);
        })->export('xlsx');
    }

    public function rol_pago_envio($id)
    {
        $rol     = Ct_Rol_Pagos::find($id);
        $nomina  = $rol->ct_nomina;
        $usuario = $rol->usuario;
        $nom_funcion = 'rol_pago_envio';
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        if (!is_null($nomina->mail_opcional)) {
            $correo = $nomina->mail_opcional;
        } else {
            $correo = $usuario->email;
        }
        $mes = "";
        if ($rol->mes == 1) {
            $mes = 'Enero';
        } elseif ($rol->mes == 2) {
            $mes = 'Febrero';
        } elseif ($rol->mes == 3) {
            $mes = 'Marzo';
        } elseif ($rol->mes == 4) {
            $mes = 'Abril';
        } elseif ($rol->mes == 5) {
            $mes = 'Mayo';
        } elseif ($rol->mes == 6) {
            $mes = 'Junio';
        } elseif ($rol->mes == 7) {
            $mes = 'Julio';
        } elseif ($rol->mes == 8) {
            $mes = 'Agosto';
        } elseif ($rol->mes == 9) {
            $mes = 'Septiembre';
        } elseif ($rol->mes == 10) {
            $mes = 'Octubre';
        } elseif ($rol->mes == 11) {
            $mes = 'Noviembre';
        } elseif ($rol->mes == 12) {
            $mes = 'Diciembre';
        }
        $rol_2 = $this->imprimir_rol_pago($id);

        $asunto = "Rol de Pago de " . $mes . ' del ' . $rol->anio;
        $titulo = "Rol de Pago de " . $mes . ' del ' . $rol->anio . '.pdf';
        Mail::send('mails.rol', ['usuario' => $usuario, 'nomina' => $nomina], function ($msj) use ($correo, $asunto, $rol_2, $titulo) {
            $msj->subject($asunto);
            $msj->from('rol@mdconsgroup.com', 'Sistema de Rol de Pago SIAAM');
            $msj->to($correo);
            $msj->attachData($rol_2, $titulo, [
                'mime' => 'application/pdf',
            ]);
        });
        CtLogRolNomina::guardarLog_envia_mail_rol_pago($rol->id,$nom_funcion,$id_empresa,$id_sucursal,'ENVIA MAIL ROL DE PAGO');
        return 'ok';
    }

    public function imprimir_new_rol_pago($mes, $anio, Request $request)
    {
        // dd($mes);
        $id_anio          = $anio;
        $id_mes           = $mes;
        $id_empresa       = $request->session()->get('id_empresa');
        $empresa          = Empresa::where('id', $id_empresa)->first();
        $rol_det_consulta = DB::table('ct_rol_pagos as rp')
            ->where('rp.id_empresa', $id_empresa)
            ->where('rp.anio', $id_anio)
            ->where('rp.mes', $id_mes)
            ->where('rp.estado', '1')
            ->join('ct_detalle_rol as drol', 'drol.id_rol', 'rp.id')
            ->join('users as u', 'rp.id_user', 'u.id')
            ->join('ct_nomina as ct_n', 'rp.id_nomina', 'ct_n.id')
            ->orderby('u.apellido1')
            ->select(
                'rp.id_empresa as idempresa',
                'rp.anio as anio',
                'rp.mes as mes',
                'rp.id_user as usuario',
                'u.nombre1',
                'u.nombre2',
                'u.apellido1',
                'u.apellido2',
                'rp.id_tipo_rol',
                'ct_n.cargo',
                'rp.id_nomina as id_nomina',
                'rp.estado as estado_rol',
                'rp.id as id_rol',
                'rp.fecha_elaboracion',
                'drol.id_rol as id_rol_pag',
                'drol.observacion_bono',
                'drol.observacion_bonoimp',
                'drol.observ_seg_privado',
                'drol.observacion_alimentacion',
                'drol.observacion_transporte',
                'drol.observacion_vacaciones',
                'drol.sueldo_mensual',
                'drol.observacion_saldo_inicial',
                'drol.cantidad_horas50 as cantidad_horas_50',
                'drol.sobre_tiempo50',
                'drol.cantidad_horas100 as cantidad_horas_100',
                'drol.sobre_tiempo100',
                'drol.bonificacion as bonificacion',
                'drol.transporte as transporte',
                'drol.vacaciones as vacaciones',
                'drol.bono_imputable',
                'drol.exam_laboratorio as exam_laboratorio',
                'drol.alimentacion as alimentacion',
                'drol.fondo_reserva',
                'drol.decimo_tercero as decimo_tercero',
                'drol.decimo_cuarto as decimo_cuarto',
                'drol.porcentaje_iess as porcentaje_iess',
                'drol.multa as multa',
                'drol.prestamos_empleado as prestamo_empleado',
                'drol.saldo_inicial_prestamo',
                'drol.anticipo_quincena as anticipo_quincena',
                'drol.otro_anticipo as otro_anticipo',
                'drol.total_ingresos as total_ingresos',
                'drol.total_egresos',
                'drol.neto_recibido as neto_recibido',
                'drol.seguro_privado as seguro_privado',
                'drol.impuesto_renta as impuesto_renta',
                'drol.total_quota_quirog as total_quir',
                'drol.total_quota_hipot as total_hip',
                'drol.fond_reserv_cobrar',
                'drol.otros_egresos',
                'drol.observ_examlaboratorio',
                'drol.observ_imp_renta',
                'drol.observacion_multa',
                'drol.observacion_fondo_cobrar as observacion_fondo_cobrar',
                'drol.observacion_otro_egreso',
                'drol.observacion_prestamo',
                'drol.observacion_otro_anticip',
                'ct_n.area',
                'drol.observacion_anticip_quinc'

            )->get();

        $lista_cuota_quirog = DB::table('ct_rh_cuotas_hipotecarios')->get();
        $lista_cuota_hip    = DB::table('ct_rh_cuotas_quirografario')->get();
        $firma_responsable = Firma_Usuario::where('responsable_rrhh', '1')->first();
        $firma_recibi = Firma_Usuario::where('id_usuario', $registro->id_user)->first();
        //dd($rol_det_consulta);

        $vistaurl = "contable.rol_pago.pdf_rol_pago2";
        $view     = \View::make($vistaurl, compact('rol_det_consulta', 'empresa', 'lista_cuota_quirog', 'lista_cuota_hip', 'firma_responsable', 'firma_recibi'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Comprobante Rol Pago.pdf');
    }

    public function existe_new_rol_pago(Request $request)
    {
        $id_empresa       = $request->session()->get('id_empresa');
        $id_anio          = $request['year'];
        $id_mes           = $request['mes'];
        $rol_det_consulta = DB::table('ct_rol_pagos')
            ->where('id_empresa', $id_empresa)
            ->where('anio', $id_anio)
            ->where('mes', $id_mes)
            ->where('estado', '1')->first();
        if (is_null($rol_det_consulta)) {
            $msj = "no";
            return ['msj' => $msj];
        } else {
            $msj = "si";
            return ['msj' => $msj];
        }
    }

    public function log_eliminar(Request $request)
    {
        $id_empresa       = $request->session()->get('id_empresa');
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        Log_horas_extras::create([

            'id_usuario'              => $idusuario,
            'nombre_accion'           => $request['detalle'],
            'accion'                  => "eliminado",
            'id_usuariocrea'          => $idusuario,
            'id_usuariomod'           => $idusuario,
            'ip_creacion'             => $ip_cliente,
            'ip_modificacion'         => $ip_cliente,

        ]);
        return json_encode('ok');
    }

    public function verificarRolAsiento($anio, $mes, $sucursal){
       
        //dd($rol_asiento);
        $id_empresa     = session('id_empresa');
        $txt_mes        = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
        $roles = [];
        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->where('ct_rol_pagos.anio', $anio)
            ->where('ct_rol_pagos.mes', $mes)
            ->where('ct_rol_pagos.id_empresa',$id_empresa )
            ->get();
        //dd(count($roles));
        if (count($roles) ==  0) {
            return ['status' => "warning", 'mensaje' => "No existe roles de pago en el Año : {$anio} Mes : {$mes}, verifique la fecha de la creación del asiento"];
        }
        $rol_asiento = RolAsiento::where('anio',$anio)->where('mes',$mes)->where('estado',1)->where('id_sucursal',$sucursal)->first();
        if(!is_null($rol_asiento)){
            return ['status' => 'error', 'msj' => 'Ya existe un asiento creado para esta fecha'];
        }
        else{
            return ['status' => 'success', 'msj' => 'Registro no encontrado'];
        }
    }
}
