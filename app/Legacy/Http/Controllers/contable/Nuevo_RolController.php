<?php


namespace Sis_medico\Http\Controllers\contable;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Ct_Nomina;
use Sis_medico\Ct_Tipo_Rol;
use Sis_medico\Ct_Rh_Saldos_Iniciales_Detalle;
use Sis_medico\Ct_Rh_Saldos_Iniciales;
use Sis_medico\Ct_Rh_Valores;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Ct_Detalle_Rol;
use Sis_medico\Ct_Rh_Prestamos;
use Sis_medico\Ct_Rh_Otros_Anticipos;
use Sis_medico\Ct_Rh_Prestamos_Detalle;
use Sis_medico\Ct_Rh_Valor_Anticipos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Ct_Rh_Cuotas_Quirografario;
use Sis_medico\Ct_Rh_Cuotas_Hipotecarios;
use Sis_medico\Nota_Debito;
use Sis_medico\Nota_Debito_Detalle;
use Sis_medico\Ct_Rh_Tipo_Pago;
use Sis_medico\Ct_Rol_Proceso;
use Sis_medico\LogAsiento;
use Sis_medico\Ct_Comprobante_Egreso_Varios;
use Sis_medico\Ct_Detalle_Comprobante_Egreso_Varios;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_Rol_Forma_Pago;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Empresa;
use Sis_medico\Ct_Rh_Detalle_Horas_Extras;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_Rh_Detalle_Prestamos_Subidos;
//Nueva Forma de Pago
use Sis_medico\User;
use Sis_medico\RolAsiento;
use Sis_medico\RolAsientoCuentas;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Rh_Acumulados;
use PHPExcel_Worksheet_Drawing;
use Excel;
use Sis_medico\Ct_Detalle_Pago;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\Log_usuario;

use Sis_medico\RolAsientoRubros;
use Sis_medico\Ct_Rh_Sucursal_Periodo;
use Sis_medico\Ct_Rh_Acumulados_Detalle;
use Illuminate\Support\Facades\Response;
use Sis_medico\CtRhRolDetalle;
use Sis_medico\CtLogRolNomina;
use Sis_medico\RolAsientoPagoRubros;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Rh_Fondo_Reserva_Anterior;

class Nuevo_RolController extends Controller
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

    public function index(Request $request, $id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $anio = $request->year;
        $mes  = $request->mes;
        if ($anio == null) {
            $anio = date('Y');
        }
        if ($mes == null) {
            $mes = 0;
        }

        $empleado = Ct_Nomina::find($id);

        $roles = $empleado->roles->where('estado', 1)->where('anio', $anio)->sortByDesc('fecha_elaboracion');

        if ($mes > 0) {
            $roles = $roles->where('mes', $mes);
        }

        return view('contable/nuevo_rol_pago/index', ['empleado' => $empleado, 'roles' => $roles, 'anio' => $anio, 'mes' => $mes]);
    }

    public function calcular_sueldo_neto($jornada, $horas_dias, $dias_laborados, $sueldo_mensual)
    {
        if ($jornada != '0') {
            $horas_mes = $horas_dias * $dias_laborados;
            $sueldo_mensual = $sueldo_mensual * $horas_mes / 240;
        } else {
            $sueldo_mensual = $sueldo_mensual / 30;
            $sueldo_mensual = $sueldo_mensual * $dias_laborados;
        }
        $sueldo_mensual = round($sueldo_mensual, 2);

        return $sueldo_mensual;
    }

    public function crear($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $dias_laborados = 30;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $nomina = Ct_Nomina::findorfail($id);
        $horas_dias = "";
        $horas_mes = "";
        $procent_iess = "IESS 9.45%";
        if ($nomina->pasante == 1) {
            $procent_iess = "IESS 17.6%";
        }
        if ($nomina->jornada != '0') {
            $dias_laborados = $nomina->dias_laborables;
            $horas_dias = $nomina->cantidad_horas;
            $horas_mes = $nomina->horas_mes;
        }

        $fechaActual = new Datetime();
        $fecha = new DateTime($nomina->fecha_ingreso);
        //anio actual y de ct_nomina
        $anio_actual = $fechaActual->format('Y');
        $anio_ingreso = $fecha->format('Y');
        //mes actual y de Ct_Nomina
        $mesNomina = $fecha->format('m');
        $mesActual = $fechaActual->format('m');
        //dia laborados
        $diasA = $fecha->format('d');
        $dias = 30;
        if ($mesNomina == $mesActual && $anio_actual == $anio_ingreso) {
            $dias_laborados = $dias - intval($diasA) + 1;
        }

        //if($nomina->id == '507'){ dd($mesNomina , $mesActual , $anio_actual , $anio_ingreso, $diasA); }
        /*$val_aport_pers = Ct_Rh_Valores::where('id_empresa', $registro->id_empresa)
        ->where('tipo', 1)->where('id', $registro->aporte_personal)->first();*/

        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 4)->first(); //dd($val_fond_reserv);
        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 3)->first();
        $anio = date('Y');
        $mes  = date('m');
        $fecha = date('Y-m-d H:i:s');
        $rol = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->where('ct_rol_pagos.anio', $anio)
            ->where('ct_rol_pagos.mes', $mes)
            ->where('ct_rol_pagos.id_nomina', $nomina->id)
            ->first();
        $empresa = Empresa::find($nomina->id_empresa);
        $parqueo = $nomina->parqueo;
        $pasante = $nomina->pasante;
        $jornada = $nomina->jornada;
        $cantidad_horas = $nomina->cantidad_horas;
        $sueldo_pasante = $nomina->sueldo_pasante;

        if (is_null($rol)) {

            $id_rol = Ct_Rol_Pagos::insertGetId([
                'id_nomina'         => $nomina->id,
                'id_user'           => $nomina->id_user,
                'id_empresa'        => $nomina->id_empresa,
                'anio'              => $anio,
                'mes'               => $mes,
                'id_tipo_rol'       => '1',
                'pasante'           => $pasante,
                //'neto_recibido'     => ,
                'fecha_elaboracion' => $fecha,
                'estado'            => '1',
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'jornada'           => $jornada,
                'cantidad_horas'    => $cantidad_horas,
                'porcentaje_aportacion'     => $procent_iess,
            ]);

            //PRESTAMOS
            $prestamos = Ct_Rh_Prestamos::where('id_empl', $nomina->id_user)
                ->where('id_empresa', $nomina->id_empresa)
                ->where('estado', '1')
                ->where('prest_cobrad', '0')
                //->where('mes_inicio_cobro','<=',$mes)
                //->where('anio_inicio_cobro','<=',$anio)
                ->where(function ($query) use ($anio, $mes) {
                    $query->where('anio_inicio_cobro', '<', $anio) //2021 < 2022
                        ->orwhere(function ($query2) use ($anio, $mes) {
                            $query2->where('anio_inicio_cobro', '=', $anio) //2022 = 2022
                                ->Where('mes_inicio_cobro', '<=', $mes); // ene <= ene   
                        });
                })
                ->get();

            $total_cuota = 0;
            foreach ($prestamos as $prestamo) {
                //$total_cuota += $prestamo->valor_cuota;
                $cuotas = $prestamo->detalles()->where('estado', 1)->where('estado_pago', 1)->count(); //dd($cuotas);
                $xanio   = $prestamo->anio_inicio_cobro;
                $xmes    = $prestamo->mes_inicio_cobro;
                $xmes    = $xmes + $cuotas;
                if ($xmes > 12) {
                    $xmes = 1;
                    $xanio++;
                }
                $cuotas++;
                $saldo_prestamo = $prestamo->saldo_total;
                $cuota = $prestamo->valor_cuota;
                if ($cuota > $saldo_prestamo) {
                    //CUOTA ES 100 Y EL SALDO ES 50
                    $cuota = $saldo_prestamo;
                    $total_cuota += $cuota;
                } else {
                    $total_cuota += $prestamo->valor_cuota;
                }
                if ($cuotas <= $prestamo->num_cuotas) {
                    Ct_Rh_Prestamos_Detalle::create([
                        'id_ct_rh_prestamos' => $prestamo->id,
                        'anio'               => $anio,
                        'mes'                => $mes,
                        'fecha'              => date('Y-m-d H:i:s'),
                        'cuota'              => $cuotas,
                        'valor_cuota'        => $cuota,
                        'id_ct_rol_pagos'    => $id_rol,
                        'estado'             => '1',
                        'estado_pago'        => '1',
                        'fecha_pago'         => date('Y-m-d H:i:s'),
                        'id_usuariocrea'     => $idusuario,
                        'id_usuariomod'      => $idusuario,
                        'ip_creacion'            => $ip_cliente,
                        'ip_modificacion'    => $ip_cliente,
                    ]);
                    $saldo_prestamo -= $cuota;
                    $prestamo->update([
                        'saldo_total' => $saldo_prestamo,
                    ]);
                }
                if ($cuotas >= $prestamo->num_cuotas) {
                    $prestamo->update([
                        'prest_cobrad' => '1',
                    ]);
                }
                if ($saldo_prestamo <= 0) {
                    $prestamo->update([
                        'prest_cobrad' => '1',
                    ]);
                }
            }
            //SALDOS
            $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl', $nomina->id_user)
                ->where('id_empresa', $nomina->id_empresa)
                ->where('estado', '1')
                ->where('saldo_cobrad', '0')
                ->where(function ($query) use ($anio, $mes) {
                    $query->where('anio_inicio_cobro', '<', $anio) //2021 < 2022
                        ->orwhere(function ($query2) use ($anio, $mes) {
                            $query2->where('anio_inicio_cobro', '=', $anio) //2022 = 2022
                                ->Where('mes_inicio_cobro', '<=', $mes); // ene <= ene
                        });
                })
                ->get(); //dd($saldos);  

            $total_cuota_ini = 0;
            foreach ($saldos as $saldo) {

                $cuotas = $saldo->detalles()->where('estado', 1)->where('estado_pago', 1)->count(); //dd($cuotas);
                $anio   = $saldo->anio_inicio_cobro;
                $mes    = $saldo->mes_inicio_cobro;
                $mes    = $mes + $cuotas;
                if ($mes > 12) {
                    $mes = 1;
                    $anio++;
                }
                $cuotas++;
                $saldo_prestamo = $saldo->saldo_res;
                $cuota = $saldo->valor_cuota;
                if ($cuota > $saldo_prestamo) {
                    //CUOTA ES 100 Y EL SALDO ES 50
                    $cuota = $saldo_prestamo;
                    $total_cuota_ini += $cuota;
                } else {
                    $total_cuota_ini += $saldo->valor_cuota;
                }
                //dd($cuotas, $saldo->num_cuotas);
                if ($cuotas < $saldo->num_cuotas) {
                    Ct_Rh_Saldos_Iniciales_Detalle::create([
                        'id_ct_rh_saldos_iniciales' => $saldo->id,
                        'anio'               => $anio,
                        'mes'                => $mes,
                        'fecha'              => date('Y-m-d H:i:s'),
                        'cuota'              => $cuotas,
                        'valor_cuota'        => $cuota,
                        'id_ct_rol_pagos'    => $id_rol,
                        'estado'             => '1',
                        'estado_pago'        => '1',
                        'fecha_pago'         => date('Y-m-d H:i:s'),
                        'id_usuariocrea'     => $idusuario,
                        'id_usuariomod'      => $idusuario,
                        'ip_creacion'            => $ip_cliente,
                        'ip_modificacion'    => $ip_cliente,
                    ]);

                    $saldo_prestamo -= $cuota;
                    $saldo->update([
                        'saldo_res' => $saldo_prestamo,
                    ]);
                }
                if ($cuotas >= $saldo->num_cuotas) {
                    $saldo->update([
                        'saldo_cobrad' => '1',
                    ]);
                }
                if ($saldo_prestamo <= 0) {
                    $saldo->update([
                        'saldo_cobrad' => '1',
                    ]);
                }
            }

            //OTROS ANTICIPOS
            $anticipos = Ct_Rh_Otros_Anticipos::where('id_empl', $nomina->id_user)
                ->where('id_empresa', $nomina->id_empresa)
                ->where('mes_cobro_anticipo', $mes)
                ->where('anio_cobro_anticipo', $anio)
                ->where('estado', '1')
                ->get();
            $total_otros_anticipos = 0;
            foreach ($anticipos as $anticipo) {
                $total_otros_anticipos += $anticipo->monto_anticipo;
                $anticipo->update(['id_ct_rol' => $id_rol]);
            }

            //1:VERIFICAR ESTA CARGADA LA VARIABLE NOMINA Y VALOR DEL FONDO DE RESERVA;
            $sobre_tiempo_50 = 0;
            $sobre_tiempo_100 = 0;
            $cantidad_horas50 = 0;
            $cantidad_horas100 = 0;
            $bonificacion = 0;
            $transporte = 0;
            $exam_laboratorio = 0;
            $fondo_reserva = 0;
            $decimo_tercero = 0;
            $decimo_cuarto = 0;
            $decimo_tercero_acumulado = 0;
            $decimo_cuarto_acumulado = 0;
            $multa = 0;
            $impuesto_renta = 0;
            $total_quota_quirog = 0;
            $total_quota_hipot = 0;
            $fond_reserv_cobrar = 0;
            $fondo_reserva_acumulado = 0;
            $sueldo_mensual     = $nomina->sueldo_neto;
            $impuesto_renta     = $nomina->impuesto_renta;
            $bono_imputable     = $nomina->bono_imputable;
            $alimentacion       = $nomina->alimentacion;
            $vacaciones         = 0;
            $fecha_ingreso      = $nomina->fecha_ingreso;
            if ($nomina->fecha_ant != null) {
                $fecha_ingreso = $nomina->fecha_ant;
            }
            $bonificacion       = $nomina->bono;
            $tipo_fondo_reserva = $nomina->pago_fondo_reserva;
            $tipo_decimo_cuarto      = $nomina->decimo_cuarto;
            $tipo_decimo_tercero     = $nomina->decimo_tercero;
            $aporte_personal         = $nomina->aportepersonal->valor;
            $seguro_privado          = $nomina->seguro_privado;
            $anticipo_quincena       = $nomina->val_anticip_quince;
            $transporte              = $nomina->movilizacion;

            $a15 = Ct_Rh_Valor_Anticipos::where('id_user', $nomina->id_user)->where('id_empresa', $nomina->id_empresa)->where('anio', $anio)->where('mes', $mes)->where('estado', '1')->first();
            if (!is_null($a15)) {
                $anticipo_quincena = $a15->valor_anticipo;
            }
            $pct_fondo_reserva = 0;
            if (!is_null($val_fond_reserv)) {
                $pct_fondo_reserva   = $val_fond_reserv->valor;
            }

            $valor_sueldo_basico = 0;
            if (!is_null($val_sal_basico)) {
                $valor_sueldo_basico = $val_sal_basico->valor;
            }

            //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
            /*if($jornada != '0'){
                $horas_mes = $horas_dias * $dias_laborados;
                $sueldo_mensual = $sueldo_mensual * $horas_mes / 240;
            }else{
                $sueldo_mensual = $sueldo_mensual / 30;
                $sueldo_mensual = $sueldo_mensual * $dias_laborados;
                
            }            
            $sueldo_mensual = round($sueldo_mensual,2);*/
            $sueldo_mensual = $this->calcular_sueldo_neto($jornada, $horas_dias, $dias_laborados, $sueldo_mensual);

            //3:BASE_IESS = SUELDO MES + VALOR_EXTRAS50 + VALOR_EXTRAS100 + BONO_IMPUTABLE
            $base_iess = $sueldo_mensual + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;

            //////////////////////////////////////////////
            $sueldo_nomina     = $nomina->sueldo_neto;
            $base_nomina       = $sueldo_nomina + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;

            //4:FONDO DE RESERVA
            /*$fecha         = date("Y-m-d");
            $fec           = new DateTime($fecha);
            $fec2          = new DateTime($fecha_ingreso);
            $diff          = $fec->diff($fec2); //dd($diff);
            $intervalMeses = $diff->format("%m");
            $intervalAnos  = $diff->format("%y") * 12; //dd($intervalMeses,$intervalAnos);
            $añosAhora    = $diff->format("%y"); //dd($añosAhora);
            $intervalDias  = $diff->format("%d");
            $diaActual     = $fec->format("d");
            $meses_totales = $intervalMeses + $intervalAnos; //dd($meses_totales);
            if ($añosAhora > 0) {
                if ($tipo_fondo_reserva == 2) { //mensualiza
                    $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100);
                } 
                ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (1)///////////////////////////////////////
                else{
                    $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100);
                    $fondo_reserva_acumulado = round($fondo_reserva_acumulado,2);
                }
                //////////////////////////////////////////////////////////////////////////////////////////////////
            }*/
            $anioActual = date('Y');
            $mesActual = date('m');
            $ultimoDia = date('Y-m-t', strtotime($anioActual . '-' . $mesActual));
            $fecha         = $ultimoDia;
            //$fecha         = date("Y-m-d");
            $fec           = new DateTime($fecha);
            $vt_dia        = date("d", strtotime($fecha_ingreso));
            $fec2          = new DateTime($fecha_ingreso);
            $diff          = $fec->diff($fec2); //dd($diff);
            $intervalMeses = $diff->format("%m");
            $intervalAnos  = $diff->format("%y") * 12; //dd($intervalMeses,$intervalAnos);
            $añosAhora     = $diff->format("%y"); //dd($añosAhora);
            $intervalDias  = $diff->format("%d");
            $diaActual     = $fec->format("d");
            $meses_totales = $intervalMeses + $intervalAnos; //dd($meses_totales);
            $fr_anterior = Ct_Rh_Fondo_Reserva_Anterior::where('id_nomina',$id)->where('estado',1)->first();
            if(!is_null($fr_anterior)){
                $meses_totales += $fr_anterior->total_meses_anterior;
                $añosAhora += $fr_anterior->total_anios_anterior;
            }
            //dd($meses_totales);

            /*if($id == 531){
                //dd($añosAhora,$tipo_fondo_reserva,$meses_totales);

                dd($fecha);

            }*/
            //dd($añosAhora,$tipo_fondo_reserva,$meses_totales);
            $arr_fr = $this->calculo_fondo_reserva($añosAhora, $nomina, $meses_totales, $dias_laborados, $base_iess, $base_nomina, $jornada, $vt_dia, $val_fond_reserv);


            $fondo_reserva = $arr_fr['fondo_reserva'];
            $fondo_reserva_acumulado = $arr_fr['fondo_reserva_acumulado'];

            /*if ($añosAhora > 0) {
                if ($tipo_fondo_reserva == 2) { //mensualiza
                    if ($meses_totales == '12') {
                        $vt_dia = $dias_laborados - $vt_dia + 1; //30-19 SE LE SUMO UN DIA POR EL TEMA DE MARIA JOSE REVISAR CON IVANNA

                        $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                        ////////////////////////////
                        if ($jornada == 0 && $dias_laborados != 30) {
                            $fondo_reserva = $base_nomina * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                        }
                    } else {
                        $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100);
                    }
                }
                ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (2)///////////////////////////////////////
                else {
                    $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100);
                    $fondo_reserva_acumulado = round($fondo_reserva_acumulado, 2);
                }
                //////////////////////////////////////////////////////////////////////////////////////////////////////
            }
            $fondo_reserva = round($fondo_reserva, 2);*/

            //5:DECIMO TERCER
            /*if($tipo_decimo_tercero == 2){
                $decimo_tercero = $base_iess / 12;    
            }else{
                $decimo_tercero_acumulado =  $base_iess / 12;  
            }
            $decimo_tercero = round($decimo_tercero,2);
            $decimo_tercero_acumulado = round($decimo_tercero_acumulado,2);*/

            $dter = $this->calculo_decimo_tercer($tipo_decimo_tercero, $base_iess, $pasante);

            $decimo_tercero = $dter['decimo_tercero'];
            $decimo_tercero_acumulado = $dter['decimo_tercero_acumulado'];

            //6:DECIMO CUARTO
            /*if($tipo_decimo_cuarto == 2){
                $decimo_cuarto = $valor_sueldo_basico / 12;    
            }else{
                $decimo_cuarto_acumulado = $valor_sueldo_basico / 12; 
            }
            $decimo_cuarto = round($decimo_cuarto,2);
            $decimo_cuarto_acumulado = round($decimo_cuarto_acumulado,2);*/

            //$dcua = $this->calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada);
            $dias_nomina        = $nomina->dias_laborables;
            $dcua = $this->new_calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada, $dias_nomina);

            $decimo_cuarto = $dcua['decimo_cuarto'];
            $decimo_cuarto_acumulado = $dcua['decimo_cuarto_acumulado'];

            $valor_vacacion = $this->calculo_vacaciones($base_iess, $fecha_ingreso, $pasante);

            $valor_base_iess = 0;
            if ($nomina->extension_conyuge == 1) {
                $ct_rh_valores = Ct_Rh_Valores::where("id_empresa", $nomina->id_empresa)->where('tipo', 5)->first();
                $valor_base_iess = ($base_iess * $ct_rh_valores->valor) / 100;
                $valor_base_iess = round($valor_base_iess, 2);
            }
            //7:PORCENTAJE IESS
            $porcentaje_iess = $base_iess * $aporte_personal / 100;
            $porcentaje_iess = round($porcentaje_iess, 2);

            $total_ingresos = $base_iess + $bonificacion + $fondo_reserva + $decimo_tercero + $decimo_cuarto + $alimentacion + $transporte + $vacaciones;
            $total_ingresos = round($total_ingresos, 2);

            $total_egresos  = $porcentaje_iess + $multa + $anticipo_quincena + $total_cuota_ini + $total_otros_anticipos + $total_cuota + $seguro_privado + $impuesto_renta + $total_quota_quirog + $total_quota_hipot + $parqueo + $valor_base_iess;
            $total_egresos = round($total_egresos, 2);

            $neto_recibir = $total_ingresos - $total_egresos;

            //Guardado en la Tabla Ct_Detalle_Rol

            Ct_Detalle_Rol::create([
                'id_rol'                      => $id_rol,
                'dias_laborados'              => $dias_laborados,
                'base_iess'                   => $base_iess,
                'sueldo_mensual'              => $sueldo_mensual,
                'cantidad_horas50'            => $cantidad_horas50,
                'sobre_tiempo50'              => $sobre_tiempo_50,
                'cantidad_horas100'           => $cantidad_horas100,
                'sobre_tiempo100'             => $sobre_tiempo_100,
                'bonificacion'                => $bonificacion,
                'alimentacion'                => $alimentacion,
                'transporte'                  => $transporte,
                'vacaciones'                  => $vacaciones,
                'bono_imputable'              => $bono_imputable,
                'exam_laboratorio'            => $exam_laboratorio,
                'fondo_reserva'               => $fondo_reserva,
                'decimo_tercero'              => $decimo_tercero,
                'decimo_cuarto'               => $decimo_cuarto,
                'porcentaje_iess'             => $porcentaje_iess,
                'seguro_privado'              => $seguro_privado,
                'impuesto_renta'              => $impuesto_renta,
                'multa'                       => $multa,
                'fond_reserv_cobrar'          => $fond_reserv_cobrar,
                'otros_egresos'               => 0,
                'prestamos_empleado'          => $total_cuota,
                'saldo_inicial_prestamo'      => $total_cuota_ini,
                'anticipo_quincena'           => $anticipo_quincena,
                //'observacion_bono'          => $request['observacion_bono'],
                //'observacion_alimentacion'  => $request['observacion_alimentacion'],
                //'observ_seg_privado'        => $request['observacion_seg_priv'],
                //'observ_imp_renta'          => $request['observacion_imp_rent'],
                'otro_anticipo'               => $total_otros_anticipos,
                //'observacion_multa'         => $request['observ_multa'],
                //'observacion_fondo_cobrar'  => $request['obs_fond_cob_trab'],
                //'observacion_prestamo'      => $request['concepto_prestamo'],
                //'observacion_saldo_inicial' => $request['obser_saldo_inicial'],
                //'observacion_anticip_quinc' => $request['concepto_quincena'],
                //'observacion_otro_anticip'  => $request['concep_otros_anticipos'],
                //'observacion_transporte'    => $request['observacion_transporte'],
                //'observacion_bonoimp'       => $request['observacion_bonoimp'],
                //'observ_examlaboratorio'    => $request['observ_examlaboratorio'],
                //'observacion_otro_egreso'   => $request['obs_otros_egres_trab'],
                'total_quota_quirog'          => $total_quota_quirog,
                'total_quota_hipot'           => $total_quota_hipot,
                'total_ingresos'              => $total_ingresos,
                'total_egresos'               => $total_egresos,
                'neto_recibido'               => $neto_recibir,

                'id_usuariocrea'            => $idusuario,
                'id_usuariocrea'            => $idusuario,
                'id_usuariomod'             => $idusuario,
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
                'parqueo'                   => $parqueo,
                'fondo_reserva_acumulado'   => $fondo_reserva_acumulado, ////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (1)
                'decimo_cuarto_acumulado'   => $decimo_cuarto_acumulado,
                'decimo_tercero_acumulado'  => $decimo_tercero_acumulado,
                'extension_conyuge'         => $valor_base_iess,
                'valor_vacacion'            => $valor_vacacion,
                'sueldo_pasante'            => $sueldo_pasante,
            ]);

            $rol = Ct_Rol_Pagos::find($id_rol);
        }

        $ct_tipo_rol = Ct_Tipo_Rol::all();

        $detalle_rol = $rol->detalle->first();

        $prestamos_rol = Ct_Rh_Prestamos_Detalle::where('id_ct_rol_pagos', $rol->id)->get();

        $saldos_ini = Ct_Rh_Saldos_Iniciales_Detalle::where('id_ct_rol_pagos', $rol->id)->get();

        $otros_anticipos = Ct_Rh_Otros_Anticipos::where('id_ct_rol', $rol->id)->get();

        $cuotas_quiro = Ct_Rh_Cuotas_Quirografario::where('id_rol', $rol->id)->get();

        $cuotas_hipo = Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $rol->id)->get();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $lista_banco = Ct_Bancos::all();

        $forma_pago = $rol->formas_de_pago->where('estado', '1');

        $rubros_ingresos = [];
        $rubros_egresos  = [];

        if($rol->id_empresa == '1391927177001'){
            $rubros_ingresos = $rol->rubros_detalles->where('estado', '1')->where('ingreso','0');
            $rubros_egresos  = $rol->rubros_detalles->where('estado', '1')->where('ingreso','1');
        }

        //dd($detalle_rol);
        return view('contable.rol_pago.editnew', ['rol' => $rol, 'nomina' => $nomina, 'val_fond_reserv' => $val_fond_reserv, 'val_sal_basico' => $val_sal_basico, 'form_pago' => null, 'ct_tipo_rol' => $ct_tipo_rol, 'detalle_rol' => $detalle_rol, 'forma_pago' => $forma_pago, 'prestamos_rol' => $prestamos_rol, 'saldos_ini' => $saldos_ini, 'otros_anticipos' => $otros_anticipos, 'cuotas_hipo' => $cuotas_hipo, 'cuotas_quiro' => $cuotas_quiro, 'tipo_pago_rol' => $tipo_pago_rol, 'lista_banco' => $lista_banco, 'empresa' => $empresa, 'rubros_ingresos' => $rubros_ingresos, 'rubros_egresos' => $rubros_egresos]);
    }

    public function calculo_decimo_tercer($tipo_decimo_tercero, $base_iess, $pasante)
    {

        $decimo_tercero = 0;
        $decimo_tercero_acumulado = 0;
        if ($tipo_decimo_tercero == 2) {
            $decimo_tercero = $base_iess / 12;
        } else {
            $decimo_tercero_acumulado =  $base_iess / 12;
        }

        if ($pasante) {
            $decimo_tercero = 0;
            $decimo_tercero_acumulado = 0;
        }
        $decimo_tercero = round($decimo_tercero, 2);
        $decimo_tercero_acumulado = round($decimo_tercero_acumulado, 2);

        return [
            'decimo_tercero' => $decimo_tercero,
            'decimo_tercero_acumulado' =>  $decimo_tercero_acumulado,
        ];
    }

    public function calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada)
    {

        $decimo_cuarto = 0;
        $decimo_cuarto_acumulado = 0;
        if ($tipo_decimo_cuarto == 2) {
            $decimo_cuarto = $valor_sueldo_basico / 12;
        } else {
            $decimo_cuarto_acumulado = $valor_sueldo_basico / 12;
        }
        if ($pasante) {
            $decimo_cuarto = 0;
            $decimo_cuarto_acumulado = 0;
        }

        $decimo_cuarto = $decimo_cuarto * $dias_laborados / 30;
        $decimo_cuarto_acumulado = $decimo_cuarto_acumulado * $dias_laborados / 30;

        if ($jornada != 0) {
            $decimo_cuarto = $decimo_cuarto / 2;
            $decimo_cuarto_acumulado = $decimo_cuarto_acumulado / 2;
        }

        $decimo_cuarto = round($decimo_cuarto, 4);
        $decimo_cuarto_acumulado = round($decimo_cuarto_acumulado, 4);

        return [
            'decimo_cuarto' => $decimo_cuarto,
            'decimo_cuarto_acumulado' =>  $decimo_cuarto_acumulado,
        ];
    }
    public function new_calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada, $dias_nomina)
    {

        $decimo_cuarto = 0;
        $decimo_cuarto_acumulado = 0;
        if ($tipo_decimo_cuarto == 2) {
            $decimo_cuarto = $valor_sueldo_basico / 12;
        } else {
            $decimo_cuarto_acumulado = $valor_sueldo_basico / 12;
        }
        if ($pasante) {
            $decimo_cuarto = 0;
            $decimo_cuarto_acumulado = 0;
        }

        $decimo_cuarto = $decimo_cuarto * $dias_laborados / 30;
        $decimo_cuarto_acumulado = $decimo_cuarto_acumulado * $dias_laborados / 30;

        if ($jornada != 0) {
            //$decimo_cuarto = $decimo_cuarto / 2;
            //$decimo_cuarto_acumulado = $decimo_cuarto_acumulado / 2;
            $decimo_cuarto = $decimo_cuarto * $dias_laborados / $dias_nomina;
            $decimo_cuarto_acumulado = $decimo_cuarto_acumulado * $dias_laborados / $dias_nomina;
        }

        $decimo_cuarto = round($decimo_cuarto, 2);
        $decimo_cuarto_acumulado = round($decimo_cuarto_acumulado, 2);

        return [
            'decimo_cuarto' => $decimo_cuarto,
            'decimo_cuarto_acumulado' =>  $decimo_cuarto_acumulado,
        ];
    }

    public function calculo_vacaciones($base_iess, $fecha_ingreso, $pasante)
    {

        $valor_vacaciones = 0;

        if ($pasante == 0) {

            $fecha         = date("Y-m-d");

            $ultimoDia = date('Y-m-t', strtotime(date("Y") . '-' . date("m")));
            if (Auth::user()->id == '0922290697') {
                //$ultimoDia   = '2025-07-31';
            }

            $fec           = new DateTime($ultimoDia);
            $fec2          = new DateTime($fecha_ingreso);
            $diff          = $fec->diff($fec2);
            $intervalMeses = $diff->format("%m");
            $intervalAnos  = $diff->format("%y");
            $intervalDias  = $diff->format("%d");
            if (Auth::user()->id == '0922290697') {
                //dd($ultimoDia,$fecha_ingreso,$intervalMeses,$intervalAnos,$intervalDias);
            }
            $dias_adicional = 0;


            if ($intervalAnos >= 5) {
                //$dias_adicional = $intervalAnos - 5;
                $dias_adicional = $intervalAnos - 4; // LA PELEA CON EMILY SE CALCULA EL INTERVALO DE AÑOS MAS 1 Y MENOS 5
                //if (Auth::user()->id == '0922290697') {

                if ($intervalMeses > 0 /*and $dias_adicional == 0*/) { /// LO QUITAMOS POR EL CASO DE ANDREA IGUAL VA A TOCAR REVISAR
                    //$dias_adicional++;
                }
                // }
            }
            //REVISAR LOS DIAS
            if ($dias_adicional >= 15) {
                $dias_adicional = 15;
            }

            $valor_vacaciones = $base_iess / 24;

            $valor_adicional = $valor_vacaciones * $dias_adicional / 15;
            if (Auth::user()->id == '0922290697') {
                //dd($fecha_ingreso,$intervalAnos, $dias_adicional, $base_iess, $valor_vacaciones, $valor_adicional, $intervalMeses, $dias_adicional);
            }


            $valor_vacaciones = $valor_vacaciones + $valor_adicional;

            $valor_vacaciones = round($valor_vacaciones, 2);
        }

        return $valor_vacaciones;
    }

    public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $decimo_tercero_acumulado = 0;
        $decimo_cuarto_acumulado = 0;

        $id_rol = $request->id_rol;
        $id_detalle_rol = $request->id_detalle_rol;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $detalle_rol = Ct_Detalle_Rol::find($id_detalle_rol);
        $nomina = $rol->ct_nomina;
        $nom_funcion = 'update';
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $procent_iess = "IESS 9.45%";
        if ($rol->pasante == 1) {
            $procent_iess = "IESS 17.6%";
        }
        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 4)->first();
        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 3)->first();

        $total_cuota = 0;$total_cuota_ini = 0; $total_otros_anticipos = 0; 
        $total_quota_quirog = 0; $total_quota_hipot = 0; 

        $total_cuota = $rol->prestamo_detalle->sum('valor_cuota'); 
        $total_cuota_ini = $rol->saldo_detalle->sum('valor_cuota');  
        $total_otros_anticipos = $rol->otros_anticipos->sum('monto_anticipo'); 
        $total_quota_hipot = $rol->cuotas_hipotecarios->sum('valor_cuota');
        $total_quota_quirog = $rol->cuotas_quirografario->sum('valor_cuota');


        //1:VERIFICAR ESTA CARGADA LA VARIABLE NOMINA Y VALOR DEL FONDO DE RESERVA;
        $dias_laborados = $request->dias_laborados;$sobre_tiempo_50 = 0;$sobre_tiempo_100 = 0;$cantidad_horas50 = $request->cant_horas_50;
        $cantidad_horas100 = $request->cant_horas_100;$bonificacion = $request->valor_bono;
        $transporte = $request->transporte;
        $vacaciones = $request->vacaciones;
        $exam_laboratorio = $request->exam_laboratorio;$fondo_reserva = 0;$decimo_tercero = 0;$decimo_cuarto = 0;$fondo_reserva_acumulado = 0;
        $multa = $request->valor_multa;$impuesto_renta = $request->impuesto_renta;
        $sueldo_mensual     = $nomina->sueldo_neto;

        $bono_imputable     = $request->bono_imputable;
        $alimentacion       = $request->alimentacion;
        $fecha_ingreso      = $nomina->fecha_ingreso;
        if ($nomina->fecha_ant != null) {
            $fecha_ingreso = $nomina->fecha_ant;
        }
        $tipo_fondo_reserva = $nomina->pago_fondo_reserva;
        $tipo_decimo_cuarto      = $nomina->decimo_cuarto;
        $tipo_decimo_tercero     = $nomina->decimo_tercero;
        $aporte_personal         = $nomina->aportepersonal->valor;
        $seguro_privado          = $request->seguro_privado;
        $anticipo_quincena       = $request->anticipo_quincena;
        $parqueo                 = $request->parqueo;
        $jornada                 = $rol->jornada;
        $pct_fondo_reserva = 0; $valor_sueldo_basico = 0;
        if(!is_null($val_fond_reserv)){
            $pct_fondo_reserva   = $val_fond_reserv->valor;
        }
        if (!is_null($val_sal_basico)) {
            $valor_sueldo_basico = $val_sal_basico->valor;
        }

        $fond_reserv_cobrar  = $request->fond_res_cobrar_trab;
        $otros_egresos = $request->otros_egresos_trab;

        //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
        /*$sueldo_mensual = $sueldo_mensual / 30;

        $sueldo_mensual = $sueldo_mensual * $dias_laborados;
        //dd($sueldo_mensual);
        $sueldo_mensual = round($sueldo_mensual,2);*/
        //CALCULO SOBRETIEMPO 50
        $horas_dias = 0;
        $horas_mes = 0;

        if ($nomina->jornada != '0') {
            // $dias_laborados = $nomina->dias_laborables;
            $horas_dias = $rol->cantidad_horas;
            //$horas_mes = $rol->horas_mes;
        }

        $sueldo_mensual = $this->calcular_sueldo_neto($jornada, $horas_dias, $dias_laborados, $sueldo_mensual);
        //$sobre_tiempo_50 = ((($sueldo_mensual/30)/8) * 1.50) * $cantidad_horas50;
        $sobre_tiempo_50 = ((($nomina->sueldo_neto / 30) / 8) * 1.50) * $cantidad_horas50;
        $sobre_tiempo_50 = round($sobre_tiempo_50, 2);
        //CALCULO SOBRETIEMPO 100
        //$sobre_tiempo_100 = ((($sueldo_mensual/30)/8) * 2.00) * $cantidad_horas100;
        $sobre_tiempo_100 = ((($nomina->sueldo_neto / 30) / 8) * 2.00) * $cantidad_horas100;
        $sobre_tiempo_100 = round($sobre_tiempo_100, 2);

        //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
        /*$sueldo_mensual = $sueldo_mensual / 30;
        $sueldo_mensual = $sueldo_mensual * $dias_laborados;
        $sueldo_mensual = round($sueldo_mensual,2);*/

        //3:BASE_IESS = SUELDO MES + VALOR_EXTRAS50 + VALOR_EXTRAS100 + BONO_IMPUTABLE
        $base_iess = $sueldo_mensual + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;
        //////////////////////////////////////////////
        $sueldo_nomina     = $nomina->sueldo_neto;
        $base_nomina       = $sueldo_nomina + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;

        //4:FONDO DE RESERVA
        //SI HOY ES ELM ES SIGUIENTE DEBO SETEAR LA FECHA AL ULTIMO DIA DEL MES ANTERIOR
        //$fecha         = date("Y-m-d");
        $anioActual = $rol->anio;
        $mesActual = $rol->mes;
        $ultimoDia = date('Y-m-t', strtotime($anioActual . '-' . $mesActual));
        $fecha      = $ultimoDia;

        //$fecha   = date("Y-m-d", strtotime($detalle_rol->created_at));
        $fec           = new DateTime($fecha);
        $vt_dia        = date("d", strtotime($fecha_ingreso));
        $fec2          = new DateTime($fecha_ingreso);
        $diff          = $fec->diff($fec2);
        $intervalMeses = $diff->format("%m");
        $intervalAnos  = $diff->format("%y") * 12; //dd($intervalMeses,$intervalAnos);
        $añosAhora     = $diff->format("%y"); //dd($añosAhora);
        $intervalDias  = $diff->format("%d");
        $diaActual     = $fec->format("d");
        $meses_totales = $intervalMeses + $intervalAnos; //dd($meses_totales);
        $fr_anterior = Ct_Rh_Fondo_Reserva_Anterior::where('id_nomina',$nomina->id)->where('estado',1)->first();
        if(!is_null($fr_anterior)){
            $meses_totales += $fr_anterior->total_meses_anterior;
            $añosAhora += $fr_anterior->total_anios_anterior;
        }

        $arr_fr = $this->calculo_fondo_reserva($añosAhora, $nomina, $meses_totales, $dias_laborados, $base_iess, $base_nomina, $jornada, $vt_dia, $val_fond_reserv);


        $fondo_reserva = $arr_fr['fondo_reserva'];
        $fondo_reserva_acumulado = $arr_fr['fondo_reserva_acumulado'];

        /*if ($añosAhora > 0) {
            if ($tipo_fondo_reserva == 2) { //mensualiza
                if ($meses_totales == '12') {
                    $vt_dia = $dias_laborados - $vt_dia + 1; //30-19
                    $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                    ////////////////////////////
                    if ($jornada == 0 && $dias_laborados != 30) {
                        $fondo_reserva = $base_nomina * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                    }
                } else {
                    $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100);
                }
            }
            ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (2)///////////////////////////////////////
            else {
                $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100);
                $fondo_reserva_acumulado = round($fondo_reserva_acumulado, 2);
            }
            //////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            /*if($intervalMeses == '11' && $tipo_fondo_reserva == 2){
                $vt_dia = $dias_laborados - $vt_dia; 
                $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                //dd($fondo_reserva,$pct_fondo_reserva);
            }
            ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (2)///////////////////////////////////////
            else{
                if($intervalMeses == '11'){
                    $vt_dia = $dias_laborados - $vt_dia; 
                    $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                    $fondo_reserva_acumulado = round($fondo_reserva_acumulado,2);
                }    
            }
            ////////////////////////////////////////////////////////////////////////////////////////////////////// 
        }
        $fondo_reserva = round($fondo_reserva, 2);*/

        //5:DECIMO TERCER
        /*if($tipo_decimo_tercero == 2){
            $decimo_tercero = $base_iess / 12;    
        }else{
            $decimo_tercero_acumulado =  $base_iess / 12;  
        }
        $decimo_tercero_acumulado = round($decimo_tercero_acumulado,2);
        $decimo_tercero = round($decimo_tercero,2);*/

        //6:DECIMO CUARTO
        /*if($tipo_decimo_cuarto == 2){
            $decimo_cuarto = $valor_sueldo_basico / 12;    
        }
        else{
            $decimo_cuarto_acumulado = $valor_sueldo_basico / 12; 
        }
        $decimo_cuarto_acumulado = round($decimo_cuarto_acumulado,2);
        $decimo_cuarto = round($decimo_cuarto,2);*/

        //7:PORCENTAJE IESS
        $porcentaje_iess = $base_iess * $aporte_personal / 100;
        $porcentaje_iess = round($porcentaje_iess, 2);

        $pasante = $rol->pasante;
        $dter = $this->calculo_decimo_tercer($tipo_decimo_tercero, $base_iess, $pasante);

        $decimo_tercero = $dter['decimo_tercero'];
        $decimo_tercero_acumulado = $dter['decimo_tercero_acumulado'];

        $jornada = $rol->jornada;
        $dias_nomina        = $nomina->dias_laborables;
        //$dcua = $this->calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada);
        $dcua = $this->new_calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada, $dias_nomina);

        $decimo_cuarto = $dcua['decimo_cuarto'];
        $decimo_cuarto_acumulado = $dcua['decimo_cuarto_acumulado'];

        $total_ingresos = $base_iess + $bonificacion + $fondo_reserva + $decimo_tercero + $decimo_cuarto + $alimentacion + $transporte + $vacaciones;
        $total_ingresos = round($total_ingresos, 2);
        $valor_base_iess = 0;
        if ($nomina->extension_conyuge == 1) {
            $ct_rh_valores = Ct_Rh_Valores::where("id_empresa", $nomina->id_empresa)->where('tipo', 5)->first();
            $valor_base_iess = ($base_iess * $ct_rh_valores->valor) / 100;
            $valor_base_iess = round($valor_base_iess, 2);
        }
        $total_egresos  = $porcentaje_iess + $multa + $anticipo_quincena + $total_cuota_ini + $total_otros_anticipos + $total_cuota + $seguro_privado + $impuesto_renta + $total_quota_quirog + $total_quota_hipot + $otros_egresos + $exam_laboratorio + $parqueo + $fond_reserv_cobrar + $valor_base_iess;
        $total_egresos = round($total_egresos, 2);

        $neto_recibir = $total_ingresos - $total_egresos;


        $valor_vacacion = $this->calculo_vacaciones($base_iess, $fecha_ingreso, $pasante);

        $arr_update_det = [
            'dias_laborados'              => $dias_laborados,
            'base_iess'                   => $base_iess,
            'sueldo_mensual'              => $sueldo_mensual,
            'sueldo_pasante'              => $request['sueldo_pasante'],
            'cantidad_horas50'            => $cantidad_horas50,
            'sobre_tiempo50'              => $sobre_tiempo_50,
            'cantidad_horas100'           => $cantidad_horas100,
            'sobre_tiempo100'             => $sobre_tiempo_100,
            'bonificacion'                => $bonificacion,
            'alimentacion'                => $alimentacion,
            'transporte'                  => $transporte,
            'vacaciones'                  => $vacaciones,
            'bono_imputable'              => $bono_imputable,
            'exam_laboratorio'            => $exam_laboratorio,
            'fondo_reserva'               => $fondo_reserva,
            'decimo_tercero'              => $decimo_tercero,
            'decimo_cuarto'               => $decimo_cuarto,
            'porcentaje_iess'             => $porcentaje_iess,
            'seguro_privado'              => $seguro_privado,
            'impuesto_renta'              => $impuesto_renta,
            'multa'                       => $multa,
            'fond_reserv_cobrar'          => $fond_reserv_cobrar,
            'otros_egresos'               => $otros_egresos,
            'prestamos_empleado'          => $total_cuota,
            'saldo_inicial_prestamo'      => $total_cuota_ini,
            'anticipo_quincena'           => $anticipo_quincena,
            'observacion_bono'          => $request['observacion_bono'],
            'observacion_alimentacion'  => $request['observacion_alimentacion'],
            'observ_seg_privado'        => $request['observacion_seg_priv'],
            'observ_imp_renta'          => $request['observacion_imp_rent'],
            'otro_anticipo'               => $total_otros_anticipos,
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
            'total_quota_quirog'          => $total_quota_quirog,
            'total_quota_hipot'           => $total_quota_hipot,
            'total_ingresos'              => $total_ingresos,
            'total_egresos'               => $total_egresos,
            'neto_recibido'               => $neto_recibir,
            'id_usuariomod'               => $idusuario,
            'ip_modificacion'             => $ip_cliente,
            'parqueo'                     => $parqueo,
            'fondo_reserva_acumulado'     => $fondo_reserva_acumulado, ////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (2)
            'decimo_cuarto_acumulado'     => $decimo_cuarto_acumulado,
            'decimo_tercero_acumulado'    => $decimo_tercero_acumulado,
            'extension_conyuge'           => $valor_base_iess,
            'valor_vacacion'              => $valor_vacacion,

        ];

        $detalle_rol->update($arr_update_det);
        CtLogRolNomina::guarda_log_update_rol($arr_update_det, $id_sucursal, $id_empresa, $nom_funcion, $id_rol);
        return "ok";
    }

    public function update_observaciones(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_rol = $request->id_rol;
        $id_detalle_rol = $request->id_detalle_rol;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $nomina = $rol->ct_nomina;
        $nom_funcion = 'update_observaciones';
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $detalle_rol = Ct_Detalle_Rol::find($id_detalle_rol);


        $observacion_bono = $request->observacion_bono;
        $observacion_bonoimp = $request->observacion_bonoimp;
        $observacion_alimentacion = $request->observacion_alimentacion;
        $observacion_transporte = $request->observacion_transporte;
        $observacion_vacaciones = $request->observacion_vacaciones;
        $observacion_seg_priv = $request->observacion_seg_priv;
        $observacion_imp_rent = $request->observacion_imp_rent;
        $observ_multa = $request->observ_multa;
        $obs_fond_cob_trab = $request->obs_fond_cob_trab;
        $obs_otros_egres_trab = $request->obs_otros_egres_trab;
        $observ_examlaboratorio = $request->observ_examlaboratorio;
        $obser_saldo_inicial = $request->obser_saldo_inicial;
        $concepto_quincena = $request->concepto_quincena;
        $concepto_prestamo = $request->concepto_prestamo;
        $concep_otros_anticipos = $request->concep_otros_anticipos;
        $observacion_horas_extras_50 = $request->observacion_horas_extras_50;
        $observacion_horas_extras_100 = $request->observacion_horas_extras_100;

        $arr_obs = [
            'observacion_bono'          => $observacion_bono,
            'observacion_alimentacion'  => $observacion_alimentacion,
            'observ_seg_privado'        => $observacion_seg_priv,
            'observ_imp_renta'          => $observacion_imp_rent,
            'observacion_multa'         => $observ_multa,
            'observacion_fondo_cobrar'  => $obs_fond_cob_trab,
            'observacion_prestamo'      => $concepto_prestamo,
            'observacion_saldo_inicial' => $obser_saldo_inicial,
            'observacion_anticip_quinc' => $concepto_quincena,
            'observacion_otro_anticip'  => $concep_otros_anticipos,
            'observacion_transporte'    => $observacion_transporte,
            'observacion_vacaciones'    => $observacion_vacaciones,
            'observacion_bonoimp'       => $observacion_bonoimp,
            'observ_examlaboratorio'    => $observ_examlaboratorio,
            'observacion_otro_egreso'   => $obs_otros_egres_trab,
            'id_usuariomod'             => $idusuario,
            'ip_modificacion'           => $ip_cliente,
            'observacion_horas_extras_50' => $observacion_horas_extras_50,
            'observacion_horas_extras_100' => $observacion_horas_extras_100,
        ];
        $detalle_rol->update($arr_obs);
        CtLogRolNomina::guarda_log_update_observacion_rol($arr_obs, $id_sucursal, $id_empresa, $nom_funcion, $id_rol);
        return "ok";
    }

    public function eliminar_prestammo_rol($id)
    {

        $detalle_prestamo_rol = Ct_Rh_Prestamos_Detalle::find($id);

        $prestamo_rol = $detalle_prestamo_rol->prestamos;
        //dd($prestamo_rol);
        $id_rol = $detalle_prestamo_rol->id_ct_rol_pagos;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'eliminar_prestammo_rol';
        $prestamo_rol->update([
            'saldo_total' => $prestamo_rol->saldo_total + $detalle_prestamo_rol->valor_cuota,
            'prest_cobrad' => 0
        ]);
        CtLogRolNomina::guarda_log_elimina_prestamo_rol('ELIMINA PRESTAMO DE ROL PAGO', $id_sucursal, $id_empresa, $nom_funcion, $rol->id);
        $detalle_prestamo_rol->delete();
    }

    public function recargar_prestammo_rol($id_nomina, $id_rol)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nomina = Ct_Nomina::find($id_nomina);
        $rol = Ct_Rol_Pagos::find($id_rol);
        $mes = $rol->mes;
        $anio = $rol->anio;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'recargar_prestammo_rol';
        //PRESTAMOS
        $prestamos = Ct_Rh_Prestamos::where('id_empl', $nomina->id_user)
            ->where('id_empresa', $nomina->id_empresa)
            ->where('estado', '1')
            //->where('mes_inicio_cobro','<=',$mes)
            //->where('anio_inicio_cobro','<=',$anio)
            ->where(function ($query) use ($anio, $mes) {
                $query->where('anio_inicio_cobro', '<', $anio) //2021 < 2022
                    ->orwhere(function ($query2) use ($anio, $mes) {
                        $query2->where('anio_inicio_cobro', '=', $anio) //2022 = 2022
                            ->Where('mes_inicio_cobro', '<=', $mes); // ene <= ene   
                    });
            })
            ->where('prest_cobrad', '0')
            ->get(); // dd($prestamos->toSql(),$mes,$anio);  

        $total_cuota = 0;
        foreach ($prestamos as $prestamo) {
            //$total_cuota += $prestamo->valor_cuota;
            $cuotas = $prestamo->detalles()->where('estado', 1)->where('estado_pago', 1)->count(); //dd($cuotas);
            $xanio   = $prestamo->anio_inicio_cobro;
            $xmes    = $prestamo->mes_inicio_cobro;
            $xmes    = $xmes + $cuotas;
            if ($xmes > 12) {
                $xmes = 1;
                $xanio++;
            }
            $cuotas++;
            $saldo_prestamo = $prestamo->saldo_total;
            $cuota = $prestamo->valor_cuota;
            if ($cuota > $saldo_prestamo) {
                //CUOTA ES 100 Y EL SALDO ES 50
                $cuota = $saldo_prestamo;
                $total_cuota += $cuota;
            } else {
                $total_cuota += $prestamo->valor_cuota;
            }
            if ($cuotas <= $prestamo->num_cuotas) {
                //Ct_Rh_Prestamos_Detalle::create([
                $arr_det_prest = [

                    'id_ct_rh_prestamos' => $prestamo->id,
                    'anio'               => $anio,
                    'mes'                => $mes,
                    'fecha'              => date('Y-m-d H:i:s'),
                    'cuota'              => $cuotas,
                    'valor_cuota'        => $cuota,
                    'id_ct_rol_pagos'    => $id_rol,
                    'estado'             => '1',
                    'estado_pago'        => '1',
                    'fecha_pago'         => date('Y-m-d H:i:s'),
                    'id_usuariocrea'     => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,
                ];
                Ct_Rh_Prestamos_Detalle::create($arr_det_prest);
                CtLogRolNomina::guarda_log_agrega_prestamo_rol($arr_det_prest, $id_sucursal, $id_empresa, $nom_funcion, $id_rol);
                $saldo_prestamo -= $cuota;
                $prestamo->update([
                    'saldo_total' => $saldo_prestamo,
                ]);
            }
            /*if ($cuotas >= $prestamo->num_cuotas) {
                $prestamo->update([
                    'prest_cobrad' => '1',
                ]);
            }*/
            if ($saldo_prestamo <= 0) {
                $prestamo->update([
                    'prest_cobrad' => '1',
                ]);
            }
        }
    }

    public function eliminar_saldo_rol($id)
    {

        $detalle_saldo_rol = Ct_Rh_Saldos_Iniciales_Detalle::find($id);
        $id_rol = $detalle_saldo_rol->id_ct_rol_pagos;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'eliminar_saldo_rol';
        $saldo_rol = $detalle_saldo_rol->saldos;
        $saldo_rol->update([
            'saldo_res' => $saldo_rol->saldo_res + $detalle_saldo_rol->valor_cuota,
            'saldo_cobrad' => 0
        ]);
        CtLogRolNomina::guarda_log_elimina_saldo_inicial_rol('ELIMINA SALDO INICIAL DE ROL PAGO', $id_sucursal, $id_empresa, $nom_funcion, $rol->id);
        $detalle_saldo_rol->delete();
    }

    public function recargar_saldo_rol($id_nomina, $id_rol)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nomina = Ct_Nomina::find($id_nomina);
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'recargar_saldo_rol';
        $rol = Ct_Rol_Pagos::find($id_rol);
        $mes = $rol->mes;
        $anio = $rol->anio;

        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl', $nomina->id_user)
            ->where('id_empresa', $nomina->id_empresa)
            ->where('estado', '1')
            ->where('saldo_cobrad', '0')
            ->where(function ($query) use ($anio, $mes) {
                $query->where('anio_inicio_cobro', '<', $anio) //2021 < 2022
                    ->orwhere(function ($query2) use ($anio, $mes) {
                        $query2->where('anio_inicio_cobro', '=', $anio) //2022 = 2022
                            ->Where('mes_inicio_cobro', '<=', $mes); // ene <= ene
                    });
            })
            ->get(); //dd($saldos);  

        $total_cuota_ini = 0;
        foreach ($saldos as $saldo) {
            //$total_cuota_ini += $saldo->valor_cuota;
            $cuotas = $saldo->detalles()->where('estado', 1)->where('estado_pago', 1)->count(); //dd($cuotas);
            $xanio   = $saldo->anio_inicio_cobro;
            $xmes    = $saldo->mes_inicio_cobro;
            $xmes    = $xmes + $cuotas;
            if ($mes > 12) {
                $xmes = 1;
                $xanio++;
            }
            $cuotas++;
            $saldo_prestamo = $saldo->saldo_res;
            $cuota = $saldo->valor_cuota;
            if ($cuota > $saldo_prestamo) {
                //CUOTA ES 100 Y EL SALDO ES 50
                $cuota = $saldo_prestamo;
                $total_cuota_ini += $cuota;
            } else {
                $total_cuota_ini += $saldo->valor_cuota;
            }
            if ($cuotas <= $saldo->num_cuotas) {
                $arr_saldo_ini = [

                    'id_ct_rh_saldos_iniciales' => $saldo->id,
                    'anio'               => $anio,
                    'mes'                => $mes,
                    'fecha'              => date('Y-m-d H:i:s'),
                    'cuota'              => $cuotas,
                    'valor_cuota'        => $cuota,
                    'id_ct_rol_pagos'    => $id_rol,
                    'estado'             => '1',
                    'estado_pago'        => '1',
                    'fecha_pago'         => date('Y-m-d H:i:s'),
                    'id_usuariocrea'     => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_creacion'            => $ip_cliente,
                    'ip_modificacion'    => $ip_cliente,
                ];
                Ct_Rh_Saldos_Iniciales_Detalle::create($arr_saldo_ini);
                CtLogRolNomina::guarda_log_agrega_saldo_inicial_rol($arr_saldo_ini, $id_sucursal, $id_empresa, $nom_funcion, $id_rol);
                $saldo_prestamo -= $cuota;
                $saldo->update([
                    'saldo_res' => $saldo_prestamo,
                ]);
            }
            /*if ($cuotas >= $saldo->num_cuotas) {
                $saldo->update([
                    'saldo_cobrad' => '1',
                ]);
            }*/
            if ($saldo_prestamo <= 0) {
                $saldo->update([
                    'saldo_cobrad' => '1',
                ]);
            }
        }
    }

    public function eliminar_anticipo_rol($id)
    {

        $anticipo_rol = Ct_Rh_Otros_Anticipos::find($id);
        $id_rol = $anticipo_rol->id_ct_rol;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'eliminar_anticipo_rol';
        $anticipo_rol->update([
            'id_ct_rol' => null
        ]);
        CtLogRolNomina::guarda_log_elimina_otro_anticipo_rol('ELIMINA ANTICIPO EN ROL PAGO', $id_sucursal, $id_empresa, $nom_funcion, $rol->id);
    }

    public function recargar_anticipo_rol($id_nomina, $id_rol)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nomina = Ct_Nomina::find($id_nomina);
        $rol = Ct_Rol_Pagos::find($id_rol);
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'recargar_anticipo_rol';
        //OTROS ANTICIPOS
        $anticipos = Ct_Rh_Otros_Anticipos::where('id_empl', $nomina->id_user)
            ->where('id_empresa', $nomina->id_empresa)
            ->where('mes_cobro_anticipo', $rol->mes)
            ->where('anio_cobro_anticipo', $rol->anio)
            ->where('estado', '1')
            ->whereNull('id_ct_rol')
            ->get();
        $total_otros_anticipos = 0;
        foreach ($anticipos as $anticipo) {
            $total_otros_anticipos += $anticipo->monto_anticipo;
            $anticipo->update(['id_ct_rol' => $id_rol]);
        }
        CtLogRolNomina::guarda_log_agrega_otro_anticipo_rol('AGREGA ANTICIPO EN ROL PAGO', $id_sucursal, $id_empresa, $nom_funcion, $rol->id);
    }

    public function cargar_cuota_quirografario($id_rol, Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'cargar_cuota_quirografario';
        $arr_quiro = [

            'id_rol'          => $id_rol,
            'valor_cuota'     => $request->cuota_quiro,
            'detalle_cuota'   => $request->detalle_quiro,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ];
        CtLogRolNomina::guarda_log_agrega_prestamo_quirografario_rol($arr_quiro, $id_sucursal, $id_empresa, $nom_funcion, $id_rol);
        Ct_Rh_Cuotas_Quirografario::insert($arr_quiro);
    }

    public function cargar_cuota_hipotecario($id_rol, Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'cargar_cuota_hipotecario';
        $arr_quiro = [

            'id_rol'          => $id_rol,
            'valor_cuota'     => $request->cuota_hipo,
            'detalle_cuota'   => $request->detalle_hipo,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,

        ];
        CtLogRolNomina::guarda_log_agrega_prestamo_hipotecario_rol($arr_quiro, $id_sucursal, $id_empresa, $nom_funcion, $id_rol);
        Ct_Rh_Cuotas_Hipotecarios::insert($arr_quiro);
    }

    public function eliminar_cuota_quiro($id)
    {

        $cuota_quiro = Ct_Rh_Cuotas_Quirografario::find($id);
        $id_rol = $cuota_quiro->id_rol;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'eliminar_cuota_quiro';
        CtLogRolNomina::guarda_log_elimina_prestamo_quirografario_rol('ELIMINA PRESTAMO QUIROGRAFARIO EN ROL PAGO', $id_sucursal, $id_empresa, $nom_funcion, $rol->id);
        $cuota_quiro->delete();
    }

    public function eliminar_cuota_hipo($id)
    {

        $cuota_hipo = Ct_Rh_Cuotas_Hipotecarios::find($id);
        $id_rol = $cuota_hipo->id_rol;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'eliminar_cuota_hipo';
        CtLogRolNomina::guarda_log_elimina_prestamo_hipotecario_rol('ELIMINA PRESTAMO HIPOTECARIO EN ROL PAGO', $id_sucursal, $id_empresa, $nom_funcion, $rol->id);
        $cuota_hipo->delete();
    }

    public function forma_pago_store(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_rol = $request->id_rol;
        $rol = Ct_Rol_Pagos::find($id_rol);
        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'forma_pago_store';
        $arr_form_pago = [
            'id_rol_pago'     => $request->id_rol,
            'id_tipo_pago'    => $request->tipo_pago,
            'fecha'           => date('Y-m-d H:i:s'),
            'banco'           => $request->banco,
            'numero_cuenta'   => $request->numero_cuenta,
            'num_cheque'      => $request->num_cheque,
            'estado'          => 1,
            'valor'           => $request->valor_forma_pago,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        CtLogRolNomina::guarda_log_agrega_forma_pago_rol($arr_form_pago, $id_sucursal, $id_empresa, $nom_funcion, $id_rol);
        Ct_Rol_Forma_Pago::create($arr_form_pago);
    }

    public function editar_nuevo_rol($id_rol)
    {

        $rol = Ct_Rol_Pagos::find($id_rol);

        $nomina = $rol->ct_nomina;
        $nom_funcion = 'editar_nuevo_rol';
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $dias = null;
        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 4)->first(); //dd($val_fond_reserv);
        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 3)->first();

        $ct_tipo_rol = Ct_Tipo_Rol::all();

        $detalle_rol = $rol->detalle->first();

        $prestamos_rol = Ct_Rh_Prestamos_Detalle::where('id_ct_rol_pagos', $rol->id)->where('estado', '1')->get();

        $saldos_ini = Ct_Rh_Saldos_Iniciales_Detalle::where('id_ct_rol_pagos', $rol->id)->where('estado', '1')->get();

        $otros_anticipos = Ct_Rh_Otros_Anticipos::where('id_ct_rol', $rol->id)->where('estado', '1')->get();

        $cuotas_quiro = Ct_Rh_Cuotas_Quirografario::where('id_rol', $rol->id)->where('estado', '1')->get();

        $cuotas_hipo = Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $rol->id)->where('estado', '1')->get();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        $lista_banco = Ct_Bancos::all();

        $forma_pago = $rol->formas_de_pago->where('estado', '1');
        CtLogRolNomina::guardarLog_visualiza_rol_pago($rol->id, $nom_funcion, $id_empresa, $id_sucursal, 'VISUALIZA ROL DE PAGO');
        $rubros_ingresos = [];
        $rubros_egresos  = [];

        if($rol->id_empresa == '1391927177001'){
            
            $rubros_ingresos = $rol->rubros_detalles->where('estado', '1')->where('ingreso_egreso','0');
            $rubros_egresos  = $rol->rubros_detalles->where('estado', '1')->where('ingreso_egreso','1');
        }

        return view('contable.rol_pago.editnew', ['rol' => $rol, 'nomina' => $nomina, 'val_fond_reserv' => $val_fond_reserv, 'val_sal_basico' => $val_sal_basico, 'form_pago' => null, 'ct_tipo_rol' => $ct_tipo_rol, 'detalle_rol' => $detalle_rol, 'forma_pago' => $forma_pago, 'prestamos_rol' => $prestamos_rol, 'saldos_ini' => $saldos_ini, 'otros_anticipos' => $otros_anticipos, 'cuotas_hipo' => $cuotas_hipo, 'cuotas_quiro' => $cuotas_quiro, 'tipo_pago_rol' => $tipo_pago_rol, 'lista_banco' => $lista_banco, 'rubros_ingresos' => $rubros_ingresos, 'rubros_egresos' => $rubros_egresos]);
    }


    public function eliminar_rol($id)
    {

        //Obtenemos la fecha de Hoy
        $fecha_actual = Date('Y-m-d H:i:s');
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $rol = Ct_Rol_Pagos::find($id);
        $nom_funcion = 'eliminar_rol';
        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;

        $rol->update($act_estado = [
            'estado' => '0',
        ]);


        $detalle_prestamo_rol = Ct_Rh_Prestamos_Detalle::where('id_ct_rol_pagos', $id)->get();

        foreach ($detalle_prestamo_rol as $det_prestamo) {
            $prestamo_rol = $det_prestamo->prestamos;
            $prestamo_rol->update([
                'saldo_total' => $prestamo_rol->saldo_total + $det_prestamo->valor_cuota,
                'prest_cobrad' => 0
            ]);
            $det_prestamo->delete();
        }


        $detalle_saldo_rol = Ct_Rh_Saldos_Iniciales_Detalle::where('id_ct_rol_pagos', $id)->get();

        foreach ($detalle_saldo_rol as $det_saldo) {
            $saldo_rol = $det_saldo->saldos;
            $saldo_rol->update([
                'saldo_res' => $saldo_rol->saldo_res + $det_saldo->valor_cuota,
                'saldo_cobrad' => 0
            ]);
            $det_saldo->delete();
        }

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "ELIMINO ROL DE PAGO",
            'dato_ant1'   => $rol->id_user,
        ]);
        CtLogRolNomina::guardarLog_elimina_rol_pago($rol->id, $nom_funcion, $id_empresa, $id_sucursal, 'ELIMINA ROL DE PAGO');
        return "ok";
    }

    public function masivo_prestamo_saldo()
    {

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;


        //$prestamos = Ct_Rh_Prestamos::where('anio_inicio_cobro', '2021')->whereNull('leido')->get();

        $prestamos = Ct_Rh_Prestamos::where('anio_inicio_cobro', '2021')
            ->where('leido', '1')
            ->where('estado', '1')
            ->where('prest_cobrad', '1')
            ->get();

        //$saldos = Ct_Rh_Saldos_Iniciales::where('anio_inicio_cobro', '2021')->whereNull('leido')->get();

        $saldos = Ct_Rh_Saldos_Iniciales::where('anio_inicio_cobro', '2021')
            ->where('leido', '1')
            ->where('estado', '1')
            ->where('saldo_cobrad', '1')
            ->get();

        foreach ($prestamos as $p) {

            $xmes = $p->mes_inicio_cobro;
            $cont = 0;
            $tot_cuota = 0;
            while ($xmes <= 9) {
                $cont++;
                $tot_cuota += $p->valor_cuota;

                $rol = Ct_Rol_Pagos::where('mes', $xmes)->where('anio', '2021')->where('id_user', $p->id_empl)->first();
                if (!is_null($rol)) {
                    Ct_Rh_Prestamos_Detalle::create([
                        'id_ct_rh_prestamos'    => $p->id,
                        'anio'                  => 2021,
                        'mes'                   => $xmes,
                        'fecha'                 => date('Y-m-d H:i:s'),
                        'valor_cuota'           => $p->valor_cuota,
                        'id_ct_rol_pagos'       => $rol->id,
                        'cuota'                 => $cont,
                        'estado'                => '1',
                        'estado_pago'           => '1',
                        'fecha_pago'            => date('Y-m-d H:i:s'),
                        'id_usuariocrea'        => $idusuario,
                        'id_usuariomod'         => $idusuario,
                        'ip_creacion'           => $ip_cliente,
                        'ip_modificacion'       => $ip_cliente,
                    ]);
                }

                if ($tot_cuota >= $p->monto_prestamo) {
                    // code...
                    break;
                }

                $xmes++;
            }

            $arr_prestamo = [
                'leido'             => 2,
                'id_usuariomod'     => $idusuario,
                'ip_modificacion'   => $ip_cliente,
                'saldo_sistema'     => $p->monto_prestamo - $tot_cuota,
            ];

            $p->update($arr_prestamo);
        }

        foreach ($saldos as $s) {

            $mes_saldo = $s->mes_inicio_cobro;
            $aux = 0;
            $total_cuota = 0;
            while ($mes_saldo <= 9) {
                $aux++;
                $total_cuota += $s->valor_cuota;
                $rol = Ct_Rol_Pagos::where('mes', $mes_saldo)->where('anio', '2021')->where('id_user', $s->id_empl)->first();
                if (!is_null($rol)) {
                    Ct_Rh_Saldos_Iniciales_Detalle::create([
                        'id_ct_rh_saldos_iniciales' => $s->id,
                        'anio'               => 2021,
                        'mes'                => $mes_saldo,
                        'fecha'              => date('Y-m-d H:i:s'),
                        'cuota'              => $aux++,
                        'valor_cuota'        => $s->valor_cuota,
                        'id_ct_rol_pagos'    => $rol->id,
                        'estado'             => '1',
                        'estado_pago'        => '1',
                        'fecha_pago'         => date('Y-m-d H:i:s'),
                        'id_usuariocrea'     => $idusuario,
                        'id_usuariomod'      => $idusuario,
                        'ip_crea'            => $ip_cliente,
                        'ip_modificacion'    => $ip_cliente,
                    ]);
                }


                if ($total_cuota >= $s->saldo_inicial) {
                    // code...
                    break;
                }

                $mes_saldo++;
            }

            $arr_saldo = [
                'leido'             => 2,
                'id_usuariomod'     => $idusuario,
                'ip_modificacion'   => $ip_cliente,
                'saldo_sistema'     => $s->saldo_inicial - $total_cuota,
            ];

            $s->update($arr_saldo);
        }
    }

    public function masivo_anticipos()
    {

        $anticipos = Ct_Rh_Otros_Anticipos::where('estado', '1')->get();
        foreach ($anticipos as $anticipo) {
            $rol = Ct_Rol_Pagos::where('mes', $anticipo->mes_cobro_anticipo)->where('anio', '2021')->where('id_user', $anticipo->id_empl)->first();
            if (!is_null($rol)) {
                $anticipo->update([
                    'id_ct_rol' => $rol->id,
                ]);
            }
        }
    }

    public function index_prestamos_saldos($id_user)
    {

        $user = User::find($id_user);

        $prestamos = Ct_Rh_Prestamos::where('id_empl', $id_user)->get();

        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl', $id_user)->get();


        return view('contable/nuevo_prestamo_saldos/index', ['prestamos' => $prestamos, 'saldos' => $saldos, 'user' => $user]);
    }

    public function masivo_search(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $empleado   = $request->empleado;

        if ($id_empresa == null) {
            $empresa    = Empresa::where('prioridad', '1')->first();
            $id_empresa = $empresa->id;
        }
        if ($anio == null) {
            $anio = date('Y');
        }
        if ($mes == null) {
            $mes = date('m');
        }

        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')->where('ct_rol_pagos.anio', $anio)->where('ct_rol_pagos.mes', $mes)->where('ct_rol_pagos.id_empresa', $id_empresa)->join('ct_nomina as nom', 'nom.id', 'ct_rol_pagos.id_nomina')->select('ct_rol_pagos.*', 'nom.nombres');

        if ($empleado != null) {
            $roles = $roles->where('nom.nombres', 'like', '%' . $empleado . '%');
        }

        $roles = $roles->get();

        //dd($roles);

        //$empresas = Empresa::where('estado', '1')->get();
        $id_empresa       = $request->session()->get('id_empresa');
        $empresas          = Empresa::where('id', $id_empresa)->get();

        $procesos = Ct_Rol_Proceso::where('id_empresa', $id_empresa)->where('anio', $anio)->where('mes', $mes)->get();
        $sueldo_basico = Ct_Rh_Valores::where('id_empresa', $id_empresa)->where('estado', '1')->where('tipo', '3')->first();
        return view('contable.nuevo_rol_pago.masivo_rol', ['sueldo_basico' => $sueldo_basico, 'roles' => $roles, 'anio' => $anio, 'mes' => $mes, 'id_empresa' => $id_empresa, 'empresas' => $empresas, 'empleado' => $empleado, 'err' => null, 'procesos' => $procesos]);
    }

    public function reporte_por_anio(Request $request)
    {
        $reporte = DB::table('ct_rol_pagos as p')
            ->join('ct_detalle_rol as d', 'd.id_rol', 'p.id')
            ->join('ct_nomina as n', 'n.id', 'p.id_nomina')
            ->join('empresa as e', 'e.id', 'p.id_empresa')
            ->join('users as u', 'u.id', 'n.id_user')
            ->where('p.estado', 1)
            ->where('p.anio', $request['anio_reporte'])
            ->where('e.id', $request['id_empresa_reporte'])
            ->select(
                "p.id_empresa",
                "e.nombrecomercial",
                "p.anio",
                "p.mes",
                "p.id_user",
                "d.base_iess",
                "d.sueldo_mensual",
                "d.sobre_tiempo50",
                "d.sobre_tiempo100",
                "d.bonificacion",
                "d.alimentacion",
                "d.transporte",
                "d.vacaciones",
                "d.bono_imputable",
                "d.exam_laboratorio",
                "d.fondo_reserva",
                "d.decimo_tercero",
                "d.decimo_cuarto",
                "d.seguro_privado",
                "d.impuesto_renta",
                "d.multa",
                "d.fond_reserv_cobrar",
                "d.otros_egresos",
                "d.prestamos_empleado",
                "d.saldo_inicial_prestamo",
                "d.anticipo_quincena",
                "d.total_ingresos",
                "d.total_egresos",
                "d.neto_recibido",
                DB::raw('CONCAT_WS(" ", u.apellido1, u.apellido2, u.nombre1, u.nombre2) as nombres')
            )->orderBy('u.apellido1', 'asc')->orderBy('u.apellido2', 'asc')->orderBy('p.mes', 'asc')->get();
        if (Auth::user()->id == '0951561075') {
            //dd($reporte);
        }
        $titulos = array("Cédula", "Nombres", "Nombre Comercial", "Año", "Mes", "Base Iess", "Sueldo Mensual", "Sobre Tiempo50", "Sobre Tiempo100", "Bonificación", "Alimentación", "Transporte", "Vacaciones", "Bono Imputable", "Examen Laboratorio", "Fondo Reserva", "Decimo Tercero", "Decimo Cuarto", "Seguro Privado", "Impuesto a la Renta", "Multa", "Fondo de Reserva Cobrar", "Otros Egresos", "Prestamos Empleado", "Saldo Incial Prestamo", "Anticipo Quincena", "Total Ingresos", "Total Egresos", "Neto Recibido");
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF", "AG", "AH");
        Excel::create('REPORTE ROLES DE PAGOS ', function ($excel) use ($titulos, $posicion, $reporte) {
            $excel->sheet('REPORTE POR ROLES', function ($sheet) use ($titulos, $posicion, $reporte) {
                $sheet->mergeCells('A1:AB1');
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('REPORTE DE ROLES DE PAGOS POR AÑO');
                    $cell->setFontWeight('bold');
                    $cell->setBackground('#DAF7A6');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo = 2;
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#DAF7A6');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                foreach ($reporte as $val) {
                    $datos_excel = array();
                    array_push(
                        $datos_excel,
                        $val->id_user,
                        $val->nombres,
                        $val->nombrecomercial,
                        $val->anio,
                        $val->mes,
                        $val->base_iess,
                        $val->sueldo_mensual,
                        $val->sobre_tiempo50,
                        $val->sobre_tiempo100,
                        $val->bonificacion,
                        $val->alimentacion,
                        $val->transporte,
                        $val->vacaciones,
                        $val->bono_imputable,
                        $val->exam_laboratorio,
                        $val->fondo_reserva,
                        $val->decimo_tercero,
                        $val->decimo_cuarto,
                        $val->seguro_privado,
                        $val->impuesto_renta,
                        $val->multa,
                        $val->fond_reserv_cobrar,
                        $val->otros_egresos,
                        $val->prestamos_empleado,
                        $val->saldo_inicial_prestamo,
                        $val->anticipo_quincena,
                        $val->total_ingresos,
                        $val->total_egresos,
                        $val->neto_recibido
                    );
                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $sheet->getStyle('G' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('H' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('I' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('J' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('K' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('L' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('M' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('N' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('O' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('P' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('Q' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('R' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('S' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('T' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('U' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('V' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('W' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('X' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('Y' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('Z' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('AA' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('AB' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $sheet->getStyle('AC' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    $comienzo++;
                }
                $sheet->setWidth(array(
                    'A' => 15,
                    'B' => 50,
                    'C' => 18,
                    'D' => 10,
                    'E' => 10,
                    'F' => 10,
                    'G' => 15,
                    'H' => 15,
                    'I' => 15,
                    'J' => 15,
                    'K' => 15,
                    'L' => 15,
                    'M' => 15,
                    'N' => 15,
                    'O' => 15,
                    'P' => 15,
                    'Q' => 15,
                    'R' => 15,

                    'S' => 15,
                    'T' => 15,
                    'U' => 15,

                    'V' => 15,
                    'W' => 15,
                    'X' => 15,

                    'Y' => 15,
                    'Z' => 15,
                    'AA' => 15,

                    'AB' => 15,
                    'AC' => 15,

                ));
            });
            $excel->getActiveSheet()->getStyle('F1:F' . $excel->getActiveSheet()->getHighestRow())
                ->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('D1:D' . $excel->getActiveSheet()->getHighestRow())
                ->getAlignment()->setWrapText(true);
        })->export('xlsx');
    }
    public function masivo_genera_roles(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $fecha      = date('Y-m-d H:i:s');
        $decimo_tercero_acumulado = 0;
        $decimo_cuarto_acumulado = 0;
        $dias_laborados = 30;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $nominas = Ct_Nomina::where('estado', '1')->where('id_empresa', $id_empresa)->get();
        $err = null;

        /*if($anio < date('Y')){
            $err = "El año seleccionado es menor al actual";
        }else{
            if($anio == date('Y')){
                if($mes < date('m')){
                    $err = "El mes seleccionado es menor al actual";
                }
            }
        }*/

        if ($err == null) {
            $procesadas = 0; $no_procesadas = 0;

            $id_proceso = Ct_Rol_Proceso::insertGetId([
                'id_empresa'    => $id_empresa,
                'anio'          => $anio,
                'mes'           => $mes,
                'tipo_proceso'  => 'ROL_POR_EMPRESA',
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ]);

            foreach ($nominas as $nomina) {

                $fechaActual = new Datetime();
                $fecha = new DateTime($nomina->fecha_ingreso);
                //anio actual y de ct_nomina
                $anio_actual = $fechaActual->format('Y');
                $anio_ingreso = $fecha->format('Y');
                //mes actual y de Ct_Nomina
                $mesNomina = $fecha->format('m');
                $mesActual = $fechaActual->format('m');
                //dia laborados
                $diasA = $fecha->format('d');
                $dias = 30;
                $dias_laborados = 30;
                $id_sucursal =  $nomina->id_sucursal;
                $id_empresa = $nomina->id_empresa;
                $nom_funcion = 'masivo_genera_roles';
                $procent_iess = "IESS 9.45%";
                if ($nomina->pasante == 1) {
                    $procent_iess = "IESS 17.6%";
                }
                //if($nomina->id == '507'){ dd($mesNomina , $mesActual , $anio_actual , $anio_ingreso); }
                if ($mesNomina == $mesActual && $anio_actual == $anio_ingreso) {
                    $dias_laborados = $dias - intval($diasA) + 1;
                }

                $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)->where('tipo', 4)->first(); //dd($val_fond_reserv);
                //NO SIEMPRE EXISTE VALIDAR
                $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)->where('tipo', 3)->first();
                $rol = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
                    ->where('ct_rol_pagos.anio', $anio)
                    ->where('ct_rol_pagos.mes', $mes)
                    ->where('ct_rol_pagos.id_nomina', $nomina->id)
                    ->first();
                if ($nomina->aportepersonal == null) {
                    $no_procesadas++;
                } else {

                    if (is_null($rol)) {

                        $procesadas++;
                        $id_rol = Ct_Rol_Pagos::insertGetId([
                            'id_nomina'         => $nomina->id,
                            'id_user'           => $nomina->id_user,
                            'id_empresa'        => $nomina->id_empresa,
                            'anio'              => $anio,
                            'mes'               => $mes,
                            'id_tipo_rol'       => '1',
                            'fecha_elaboracion' => $fecha,
                            'estado'            => '1',
                            'id_usuariocrea'    => $idusuario,
                            'id_usuariomod'     => $idusuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'certificado'       => 0,
                            'id_proceso'        => $id_proceso,
                            'pasante'           => $nomina->pasante,
                            'jornada'           => $nomina->jornada,
                            'cantidad_horas'    => $nomina->cantidad_horas,
                            'porcentaje_aportacion'     => $procent_iess,
                        ]);

                        //PRESTAMOS
                        $prestamos = Ct_Rh_Prestamos::where('id_empl', $nomina->id_user)
                            ->where('id_empresa', $nomina->id_empresa)
                            ->where('estado', '1')
                            ->where('prest_cobrad', '0')
                            //->where('anio_inicio_cobro','<=',$anio)
                            //->where('mes_inicio_cobro','<=',$mes)
                            ->where(function ($query) use ($anio, $mes) {
                                $query->where('anio_inicio_cobro', '<', $anio) //2021 < 2022
                                    ->orwhere(function ($query2) use ($anio, $mes) {
                                        $query2->where('anio_inicio_cobro', '=', $anio) //2022 = 2022
                                            ->Where('mes_inicio_cobro', '<=', $mes); // ene <= ene   
                                    });
                            })
                            ->get();

                        $total_cuota = 0;
                        foreach ($prestamos as $prestamo) {
                            //$total_cuota += $prestamo->valor_cuota;
                            $cuotas = $prestamo->detalles()->where('estado', 1)->where('estado_pago', 1)->count(); //dd($cuotas);
                            $xanio   = $prestamo->anio_inicio_cobro;
                            $xmes    = $prestamo->mes_inicio_cobro;
                            $xmes    = $xmes + $cuotas;
                            if ($xmes > 12) {
                                $xmes = 1;
                                $xanio++;
                            }
                            $cuotas++;
                            $saldo_prestamo = $prestamo->saldo_total;
                            $cuota = $prestamo->valor_cuota;
                            if ($cuota > $saldo_prestamo) {
                                //CUOTA ES 100 Y EL SALDO ES 50
                                $cuota = $saldo_prestamo;
                                $total_cuota += $cuota;
                            } else {
                                $total_cuota += $prestamo->valor_cuota;
                            }
                            if ($cuotas <= $prestamo->num_cuotas) {
                                Ct_Rh_Prestamos_Detalle::create([
                                    'id_ct_rh_prestamos' => $prestamo->id,
                                    //'anio'               => $xanio,
                                    //'mes'                => $xmes,
                                    'anio'               => $anio,
                                    'mes'                => $mes,
                                    'fecha'              => date('Y-m-d H:i:s'),
                                    'cuota'              => $cuotas,
                                    'valor_cuota'        => $cuota,
                                    'id_ct_rol_pagos'    => $id_rol,
                                    'estado'             => '1',
                                    'estado_pago'        => '1',
                                    'fecha_pago'         => date('Y-m-d H:i:s'),
                                    'id_usuariocrea'     => $idusuario,
                                    'id_usuariomod'      => $idusuario,
                                    'ip_creacion'            => $ip_cliente,
                                    'ip_modificacion'    => $ip_cliente,
                                ]);
                                $saldo_prestamo -= $cuota;
                                $prestamo->update([
                                    'saldo_total' => $saldo_prestamo,
                                ]);
                            }
                            if ($cuotas >= $prestamo->num_cuotas) {
                                $prestamo->update([
                                    'prest_cobrad' => '1',
                                ]);
                            }
                            if ($saldo_prestamo <= 0) {
                                $prestamo->update([
                                    'prest_cobrad' => '1',
                                ]);
                            }
                        }
                        //SALDOS
                        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl', $nomina->id_user)
                            ->where('id_empresa', $nomina->id_empresa)
                            ->where('estado', '1')
                            ->where('saldo_cobrad', '0')
                            //->where('anio_inicio_cobro','<=',$anio)
                            //->where('mes_inicio_cobro','<=',$mes)
                            ->where(function ($query) use ($anio, $mes) {
                                $query->where('anio_inicio_cobro', '<', $anio) //2021 < 2022
                                    ->orwhere(function ($query2) use ($anio, $mes) {
                                        $query2->where('anio_inicio_cobro', '=', $anio) //2022 = 2022
                                            ->orWhere('mes_inicio_cobro', '<=', $mes); // ene <= ene   
                                    });
                            })
                            ->get(); //dd($saldos);  

                        $total_cuota_ini = 0;
                        foreach ($saldos as $saldo) {
                            //$total_cuota_ini += $saldo->valor_cuota;
                            $cuotas = $saldo->detalles()->where('estado', 1)->where('estado_pago', 1)->count(); //dd($cuotas);
                            $xanio   = $saldo->anio_inicio_cobro;
                            $xmes    = $saldo->mes_inicio_cobro;
                            $xmes    = $xmes + $cuotas;
                            if ($xmes > 12) {
                                $xmes = 1;
                                $xanio++;
                            }
                            $cuotas++;
                            $saldo_prestamo = $saldo->saldo_res;
                            $cuota = $saldo->valor_cuota;
                            if ($cuota > $saldo_prestamo) {
                                //CUOTA ES 100 Y EL SALDO ES 50
                                $cuota = $saldo_prestamo;
                                $total_cuota_ini += $cuota;
                            } else {
                                $total_cuota_ini += $saldo->valor_cuota;
                            }
                            if ($cuotas < $saldo->num_cuotas) {
                                Ct_Rh_Saldos_Iniciales_Detalle::create([
                                    'id_ct_rh_saldos_iniciales' => $saldo->id,
                                    //'anio'               => $xanio,
                                    //'mes'                => $xmes,
                                    'anio'               => $anio,
                                    'mes'                => $mes,
                                    'fecha'              => date('Y-m-d H:i:s'),
                                    'cuota'              => $cuotas,
                                    'valor_cuota'        => $cuota,
                                    'id_ct_rol_pagos'    => $id_rol,
                                    'estado'             => '1',
                                    'estado_pago'        => '1',
                                    'fecha_pago'         => date('Y-m-d H:i:s'),
                                    'id_usuariocrea'     => $idusuario,
                                    'id_usuariomod'      => $idusuario,
                                    'ip_creacion'            => $ip_cliente,
                                    'ip_modificacion'    => $ip_cliente,
                                ]);

                                $saldo_prestamo -= $cuota;
                                $saldo->update([
                                    'saldo_res' => $saldo_prestamo,
                                ]);
                            }
                            if ($cuotas >= $saldo->num_cuotas) {
                                $saldo->update([
                                    'saldo_cobrad' => '1',
                                ]);
                            }
                            if ($saldo_prestamo <= 0) {
                                $saldo->update([
                                    'saldo_cobrad' => '1',
                                ]);
                            }
                        }

                        //OTROS ANTICIPOS
                        $anticipos = Ct_Rh_Otros_Anticipos::where('id_empl', $nomina->id_user)
                            ->where('id_empresa', $nomina->id_empresa)
                            ->where('mes_cobro_anticipo', $mes)
                            ->where('anio_cobro_anticipo', $anio)
                            ->where('estado', '1')
                            ->get();
                        $total_otros_anticipos = 0;
                        foreach ($anticipos as $anticipo) {
                            $total_otros_anticipos += $anticipo->monto_anticipo;
                            $anticipo->update(['id_ct_rol' => $id_rol]);
                        }

                        //1:VERIFICAR ESTA CARGADA LA VARIABLE NOMINA Y VALOR DEL FONDO DE RESERVA;
                        $sobre_tiempo_50 = 0;
                        $sobre_tiempo_100 = 0;
                        $cantidad_horas50 = 0;
                        $cantidad_horas100 = 0;
                        $bonificacion = 0;
                        $transporte =  $nomina->movilizacion;
                        $exam_laboratorio = 0;
                        $fondo_reserva = 0;
                        $decimo_tercero = 0;
                        $decimo_cuarto = 0;
                        $multa = 0;
                        $impuesto_renta = 0;
                        $total_quota_quirog = 0;
                        $total_quota_hipot = 0;
                        $fond_reserv_cobrar = 0;
                        $fondo_reserva_acumulado = 0;
                        $sueldo_mensual     = $nomina->sueldo_neto;
                        $impuesto_renta     = $nomina->impuesto_renta;
                        $bono_imputable     = $nomina->bono_imputable;
                        $alimentacion       = $nomina->alimentacion;
                        $vacaciones         = 0;
                        $fecha_ingreso      = $nomina->fecha_ingreso;
                        $jornada            = $nomina->jornada;
                        if ($nomina->fecha_ant !== null) {
                            $fecha_ingreso = $nomina->fecha_ant;
                        }
                        $tipo_fondo_reserva = $nomina->pago_fondo_reserva;
                        $tipo_decimo_cuarto      = $nomina->decimo_cuarto;
                        $tipo_decimo_tercero     = $nomina->decimo_tercero;
                        $aporte_personal         = $nomina->aportepersonal->valor;
                        $seguro_privado          = $nomina->seguro_privado;
                        $anticipo_quincena       = $nomina->val_anticip_quince;
                        $parqueo                 = $nomina->parqueo;
                        $bonificacion            = $nomina->bono;

                        $a15 = Ct_Rh_Valor_Anticipos::where('id_user', $nomina->id_user)->where('id_empresa', $nomina->id_empresa)->where('anio', $anio)->where('mes', $mes)->where('estado', '1')->first();
                        if (!is_null($a15)) {
                            $anticipo_quincena = $a15->valor_anticipo;
                        }
                        $pct_fondo_reserva = 0; $valor_sueldo_basico = 0;
                        if (!is_null($val_fond_reserv)) {
                            $pct_fondo_reserva   = $val_fond_reserv->valor;
                        }
                        if (!is_null($val_sal_basico)) {
                            $valor_sueldo_basico = $val_sal_basico->valor;
                        }

                        //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
                        //$sueldo_mensual = $sueldo_mensual / 30; //// ANTERIOR 
                        //$sueldo_mensual = $sueldo_mensual * $dias_laborados; //// Anterior
                        //dd($sueldo_mensual, $new_dias_lab);
                        /*$sueldo_mensual = $sueldo_mensual / 30;
                        $sueldo_mensual = $sueldo_mensual * $dias_laborados;
                        $sueldo_mensual = round($sueldo_mensual,2);*/

                        $horas_dias = "";
                        $horas_mes = "";
                        if ($nomina->jornada != '0') {
                            $dias_laborados = $nomina->dias_laborables;
                            $horas_dias = $nomina->cantidad_horas;
                            $horas_mes = $nomina->horas_mes;
                        }

                        $sueldo_mensual = $this->calcular_sueldo_neto($jornada, $horas_dias, $dias_laborados, $sueldo_mensual);

                        //3:BASE_IESS = SUELDO MES + VALOR_EXTRAS50 + VALOR_EXTRAS100 + BONO_IMPUTABLE
                        $base_iess = $sueldo_mensual + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;
                        //////////////////////////////////////////////
                        $sueldo_nomina     = $nomina->sueldo_neto;
                        $base_nomina       = $sueldo_nomina + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;

                        //4:FONDO DE RESERVA
                        /*$fecha         = date("Y-m-d");
                        $fec           = new DateTime($fecha);
                        $fec2          = new DateTime($fecha_ingreso);
                        $diff          = $fec->diff($fec2); //dd($diff);
                        $intervalMeses = $diff->format("%m");
                        $intervalAnos  = $diff->format("%y") * 12; //dd($intervalMeses,$intervalAnos);
                        $añosAhora    = $diff->format("%y"); //dd($añosAhora);
                        $intervalDias  = $diff->format("%d");
                        $diaActual     = $fec->format("d");
                        $meses_totales = $intervalMeses + $intervalAnos; //dd($meses_totales);
                        if ($añosAhora > 0) {
                            if ($tipo_fondo_reserva == 2) { //mensualiza
                                $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100);
                            } 
                            ////////////////////////ROL DE PAGO ACUMULADO PARA ASIENTOS (3)////////////////
                            else{
                                $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100);
                                $fondo_reserva_acumulado = round($fondo_reserva_acumulado,2);    
                            }
                            ///////////////////////////////////////////////////////////////////////////////
                        }*/
                        $anioActual = date('Y');
                        $mesActual = date('m');
                        $ultimoDia = date('Y-m-t', strtotime($anioActual . '-' . $mesActual));
                        $fecha      = $ultimoDia;
                        //$fecha         = date("Y-m-d");
                        $fec           = new DateTime($fecha);
                        $vt_dia        = date("d", strtotime($fecha_ingreso));
                        $fec2          = new DateTime($fecha_ingreso);
                        $diff          = $fec->diff($fec2); //dd($diff);
                        $intervalMeses = $diff->format("%m");
                        $intervalAnos  = $diff->format("%y") * 12; //dd($intervalMeses,$intervalAnos);
                        $añosAhora     = $diff->format("%y"); //dd($añosAhora);
                        $intervalDias  = $diff->format("%d");
                        $diaActual     = $fec->format("d");
                        $meses_totales = $intervalMeses + $intervalAnos; //dd($meses_totales);
                        $fr_anterior = Ct_Rh_Fondo_Reserva_Anterior::where('id_nomina',$nomina->id)->where('estado',1)->first();
                        if(!is_null($fr_anterior)){
                            $meses_totales += $fr_anterior->total_meses_anterior;
                            $añosAhora += $fr_anterior->total_anios_anterior;
                        }
                        
                        $arr_fr = $this->calculo_fondo_reserva($añosAhora, $nomina, $meses_totales, $dias_laborados, $base_iess, $base_nomina, $jornada, $vt_dia, $val_fond_reserv);


                        $fondo_reserva = $arr_fr['fondo_reserva'];
                        $fondo_reserva_acumulado = $arr_fr['fondo_reserva_acumulado'];
                        /*if ($añosAhora > 0) {
                            if ($tipo_fondo_reserva == 2) { //mensualiza
                                if ($meses_totales == '12') {
                                    $vt_dia = $dias_laborados - $vt_dia + 1; //30-19 SE LE SUMO UN DIA POR EL TEMA DE MARIA JOSE REVISAR CON IVANNA

                                    $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                                    ////////////////////////////
                                    if ($jornada == 0 && $dias_laborados != 30) {
                                        $fondo_reserva = $base_nomina * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                                    }
                                } else {
                                    $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100);
                                }
                            }
                            ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (2)///////////////////////////////////////
                            else {
                                $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100);
                                $fondo_reserva_acumulado = round($fondo_reserva_acumulado, 2);
                            }
                            //////////////////////////////////////////////////////////////////////////////////////////////////////
                        }
                        $fondo_reserva = round($fondo_reserva, 2);*/

                        //5:DECIMO TERCER
                        /*if($tipo_decimo_tercero == 2){
                            $decimo_tercero = $base_iess / 12;    
                        }else{
                            $decimo_tercero_acumulado =  $base_iess / 12;  
                        }
                        $decimo_tercero_acumulado = round($decimo_tercero_acumulado,2);
                        $decimo_tercero = round($decimo_tercero,2);

                        //6:DECIMO CUARTO
                        if($tipo_decimo_cuarto == 2){
                            $decimo_cuarto = $valor_sueldo_basico / 12;    
                        }else{
                              $decimo_cuarto_acumulado = $valor_sueldo_basico / 12; 
                        }
                        $decimo_cuarto_acumulado = round($decimo_cuarto_acumulado,2);
                        $decimo_cuarto = round($decimo_cuarto,2);*/
                        $pasante = $nomina->pasante;
                        //$jornada = $nomina->jornada;

                        $dter = $this->calculo_decimo_tercer($tipo_decimo_tercero, $base_iess, $pasante);

                        $decimo_tercero = $dter['decimo_tercero'];
                        $decimo_tercero_acumulado = $dter['decimo_tercero_acumulado'];
                        $dias_nomina        = $nomina->dias_laborables;
                        //$dcua = $this->calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada);
                        $dcua = $this->new_calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada, $dias_nomina);
                        $decimo_cuarto = $dcua['decimo_cuarto'];
                        $decimo_cuarto_acumulado = $dcua['decimo_cuarto_acumulado'];

                        $valor_vacacion = $this->calculo_vacaciones($base_iess, $fecha_ingreso, $pasante);

                        $valor_base_iess = 0;
                        if ($nomina->extension_conyuge == 1) {
                            $ct_rh_valores = Ct_Rh_Valores::where("id_empresa", $nomina->id_empresa)->where('tipo', 5)->first();
                            $valor_base_iess = ($base_iess * $ct_rh_valores->valor) / 100;
                            $valor_base_iess = round($valor_base_iess, 2);
                        }
                        //7:PORCENTAJE IESS
                        $porcentaje_iess = $base_iess * $aporte_personal / 100;
                        $porcentaje_iess = round($porcentaje_iess, 2);

                        $total_ingresos = $base_iess + $bonificacion + $fondo_reserva + $decimo_tercero + $decimo_cuarto + $alimentacion + $transporte + $vacaciones;
                        $total_ingresos = round($total_ingresos, 2);


                        $total_egresos  = $porcentaje_iess + $multa + $anticipo_quincena + $total_cuota_ini + $total_otros_anticipos + $total_cuota + $seguro_privado + $impuesto_renta + $total_quota_quirog + $total_quota_hipot + $parqueo + $valor_base_iess;
                        $total_egresos = round($total_egresos, 2);

                        $neto_recibir = $total_ingresos - $total_egresos;



                        //Guardado en la Tabla Ct_Detalle_Rol
                        $arr_det_nom = [
                            'id_rol'                      => $id_rol,
                            'dias_laborados'              => $dias_laborados,
                            'base_iess'                   => $base_iess,
                            'sueldo_mensual'              => $sueldo_mensual,
                            'cantidad_horas50'            => $cantidad_horas50,
                            'sobre_tiempo50'              => $sobre_tiempo_50,
                            'cantidad_horas100'           => $cantidad_horas100,
                            'sobre_tiempo100'             => $sobre_tiempo_100,
                            'bonificacion'                => $bonificacion,
                            'alimentacion'                => $alimentacion,
                            'transporte'                  => $transporte,
                            'vacaciones'                  => $vacaciones,
                            'bono_imputable'              => $bono_imputable,
                            'exam_laboratorio'            => $exam_laboratorio,
                            'fondo_reserva'               => $fondo_reserva,
                            'decimo_tercero'              => $decimo_tercero,
                            'decimo_cuarto'               => $decimo_cuarto,
                            'porcentaje_iess'             => $porcentaje_iess,
                            'seguro_privado'              => $seguro_privado,
                            'impuesto_renta'              => $impuesto_renta,
                            'multa'                       => $multa,
                            'fond_reserv_cobrar'          => $fond_reserv_cobrar,
                            'otros_egresos'               => 0,
                            'prestamos_empleado'          => $total_cuota,
                            'saldo_inicial_prestamo'      => $total_cuota_ini,
                            'anticipo_quincena'           => $anticipo_quincena,
                            'otro_anticipo'               => $total_otros_anticipos,
                            'total_quota_quirog'          => $total_quota_quirog,
                            'total_quota_hipot'           => $total_quota_hipot,
                            'total_ingresos'              => $total_ingresos,
                            'total_egresos'               => $total_egresos,
                            'neto_recibido'               => $neto_recibir,
                            'id_usuariocrea'            => $idusuario,
                            'id_usuariocrea'            => $idusuario,
                            'id_usuariomod'             => $idusuario,
                            'ip_creacion'               => $ip_cliente,
                            'ip_modificacion'           => $ip_cliente,
                            'parqueo'                   => $parqueo,
                            'fondo_reserva_acumulado'   => $fondo_reserva_acumulado, ////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (3)
                            'decimo_cuarto_acumulado'   => $decimo_cuarto_acumulado,
                            'decimo_tercero_acumulado'  => $decimo_tercero_acumulado,
                            'extension_conyuge'         => $valor_base_iess,
                            'valor_vacacion'            => $valor_vacacion,
                            'sueldo_pasante'            => $nomina->sueldo_pasante,
                        ];
                        Ct_Detalle_Rol::create($arr_det_nom);
                        CtLogRolNomina::guarda_log_masivo_genera_roles($arr_det_nom, $id_sucursal, $id_empresa, $nom_funcion);
                        $rol = Ct_Rol_Pagos::find($id_rol);
                    } else {
                        $no_procesadas++;
                    }
                }
            }

            $proceso = Ct_Rol_Proceso::find($id_proceso);

            $proceso->update([
                'procesados' => $procesadas,
                'no_procesados' => $no_procesadas,
            ]);
        } else {
            $err = "No se puede procesar Roles anteriores al mes actual";
        }

        return "ok";
    }

    public function excel_prestamos_saldos($id_user)
    {

        $user = User::find($id_user);
        $prestamos = Ct_Rh_Prestamos::where('id_empl', $id_user)->get();
        $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl', $id_user)->get();

        $titulos = array("FECHA CREACION", "CONCEPTO", "NUMERO CUOTA", "VALOR CUOTA", "MES/AÑO INICIO COBRO", "MES/AÑO FIN COBRO", "MONTO PRESTAMO", "VALOR A PAGAR", "ESTADO");

        $titulos2 = array("FECHA CREACION", "CONCEPTO", "NUMERO CUOTA", "VALOR CUOTA", "MES/AÑO INICIO COBRO", "MES/AÑO FIN COBRO", "SALDO INICIAL", "VALOR A PAGAR", "ESTADO");


        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I");

        Excel::create('Reporte_' . $user->apellido1 . ' ' . $user->nombre1, function ($excel) use ($titulos, $posicion, $prestamos, $saldos, $user, $titulos2) {
            $excel->sheet('Reporte_' . $user->apellido1 . ' ' . $user->nombre1, function ($sheet) use ($titulos, $posicion, $prestamos, $saldos, $user, $titulos2) {

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMOS - SALDOS DETALLE');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('right');
                });

                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('B2', function ($cell) use ($user) {
                    // manipulate the cel
                    $cell->setValue($user->id);
                    $cell->setAlignment('right');
                });

                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });


                $sheet->mergeCells('D2:E2');
                $sheet->cell('D2', function ($cell) use ($user) {
                    // manipulate the cel
                    $cell->setValue($user->apellido1 . ' ' . $user->apellido2 . ' ' . $user->nombre1 . ' ' . $user->nombre2);
                    $cell->setAlignment('right');
                });

                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMOS');
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });


                $comienzo = 5; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL 

                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                /*****FIN DE TITULOS DEL EXCEL***********/

                $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                $titulo_detallep = array("AÑO", "MES", "CUOTA", "VALOR CUOTA");
                $acum_prestamos = 0;
                foreach ($prestamos as $prestamo) {

                    $detalle_prestamo = Ct_Rh_Prestamos_Detalle::where('id_ct_rh_prestamos', $prestamo->id)->get();
                    $datos_excel = array();

                    if ($prestamo->estado) {
                        $acum_prestamos += $prestamo->saldo_total;
                        if ($prestamo->prest_cobrad) {
                            $estado = 'PAGADO';
                        } else {
                            $estado = 'Activo';
                        }
                    } else {
                        $estado = 'Inactivo';
                    }


                    $ms = intval($prestamo->mes_inicio_cobro) - 1;
                    $m = intval($prestamo->mes_fin_cobro) - 1;

                    array_push($datos_excel, $prestamo->fecha_creacion, $prestamo->concepto, $prestamo->num_cuotas, $prestamo->valor_cuota, $meses[$ms] . '-' . $prestamo->anio_inicio_cobro, $meses[$m] . '-' . $prestamo->anio_fin_cobro, $prestamo->monto_prestamo, $prestamo->saldo_total, $estado);

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->getStyle('D' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->getStyle('G' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->getStyle('H' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    }

                    $comienzo++;

                    if ($detalle_prestamo->count() > 0) {
                        for ($i = 0; $i < count($titulo_detallep); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulo_detallep, $i) {
                                $cell->setValue($titulo_detallep[$i]);
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        $comienzo++;

                        $tot_det_prest = 0;
                        foreach ($detalle_prestamo as $det_prest) {

                            $datos_detalle = array();
                            $mesd = intval($det_prest->mes) - 1;
                            $tot_det_prest += $det_prest->valor_cuota;

                            array_push($datos_detalle, $det_prest->anio, $meses[$mesd], $det_prest->cuota, $det_prest->valor_cuota);

                            for ($i = 0; $i < count($datos_detalle); $i++) {
                                $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_detalle, $i) {
                                    $cell->setValue($datos_detalle[$i]);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                                $sheet->getStyle('D' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                            }
                            $comienzo++;
                        }
                        $sheet->cell('C' . $comienzo, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TOTAL');
                            $cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                        });

                        $sheet->cell('D' . $comienzo, function ($cell) use ($tot_det_prest) {
                            // manipulate the cel
                            $cell->setValue($tot_det_prest);
                            $cell->setAlignment('center');
                        });
                        $sheet->getStyle('D' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                        $comienzo++;
                    }
                }

                $sheet->cell('G' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL PRESTAMOS');
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H' . $comienzo, function ($cell) use ($acum_prestamos) {
                    // manipulate the cel
                    $cell->setValue('$ ' . $acum_prestamos);
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });

                $comienzo++;

                $sheet->cell('A' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALDOS INICIALES');
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $comienzo++;


                for ($i = 0; $i < count($titulos2); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos2, $i) {
                        $cell->setValue($titulos2[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#CCE1A7');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;

                $acum_saldo = 0;
                foreach ($saldos as $saldo) {

                    $detalle_saldos = Ct_Rh_Saldos_Iniciales_Detalle::where('id_ct_rh_saldos_iniciales', $saldo->id)->get();
                    $datos_saldo = array();

                    if ($saldo->estado) {
                        $acum_saldo += $saldo->saldo_res;
                        if ($saldo->saldo_cobrad) {
                            $estado = 'PAGADO';
                        } else {
                            $estado = 'Activo';
                        }
                    } else {
                        $estado = 'Inactivo';
                    }


                    $msi = intval($saldo->mes_inicio_cobro) - 1;
                    $mi = intval($saldo->mes_fin_cobro) - 1;


                    array_push($datos_saldo, $saldo->fecha_creacion, $saldo->observacion, $saldo->num_cuotas, $saldo->valor_cuota, $meses[$msi] . '-' . $saldo->anio_inicio_cobro, $meses[$mi] . '-' . $saldo->anio_fin_cobro, $saldo->saldo_inicial, $saldo->saldo_res, $estado);



                    for ($i = 0; $i < count($datos_saldo); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_saldo, $i) {
                            $cell->setValue($datos_saldo[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                        $sheet->getStyle('D' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->getStyle('G' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                        $sheet->getStyle('H' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    }
                    $comienzo++;
                    if ($detalle_saldos->count() > 0) {
                        for ($i = 0; $i < count($titulo_detallep); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulo_detallep, $i) {
                                $cell->setValue($titulo_detallep[$i]);
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        $comienzo++;

                        $tot_det_saldo = 0;
                        foreach ($detalle_saldos as $det_saldo) {

                            $datos_detalle_s = array();
                            $tot_det_saldo += $det_saldo->valor_cuota;
                            $mss = intval($det_saldo->mes) - 1;

                            array_push($datos_detalle_s, $det_saldo->anio, $meses[$mss], $det_saldo->cuota, $det_saldo->valor_cuota);

                            for ($i = 0; $i < count($datos_detalle_s); $i++) {
                                $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_detalle_s, $i) {
                                    $cell->setValue($datos_detalle_s[$i]);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                                $sheet->getStyle('D' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                            }
                            $comienzo++;
                        }
                        $sheet->cell('C' . $comienzo, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TOTAL');
                            $cell->setAlignment('center');
                            $cell->setFontWeight('bold');
                        });

                        $sheet->cell('D' . $comienzo, function ($cell) use ($tot_det_saldo) {
                            // manipulate the cel
                            $cell->setValue($tot_det_saldo);
                            $cell->setAlignment('center');
                        });
                        $sheet->getStyle('D' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                    }
                    $comienzo++;
                }
                $comienzo++;
                $sheet->cell('G' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL SALDOS');
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H' . $comienzo, function ($cell) use ($acum_saldo) {
                    // manipulate the cel
                    $cell->setValue($acum_saldo);
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $sheet->getStyle('H' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
                $total_pago = $acum_saldo + $acum_prestamos;

                $comienzo++;
                $sheet->cell('G' . $comienzo, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL A PAGAR');
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H' . $comienzo, function ($cell) use ($total_pago) {
                    // manipulate the cel
                    $cell->setValue($total_pago);
                    $cell->setAlignment('center');
                    $cell->setAlignment('right');
                    $cell->setFontWeight('bold');
                });
                $sheet->getStyle('H' . $comienzo)->getNumberFormat()->setFormatCode('$ 0.00');
            });
        })->export('xlsx');
    }

    public function masivo_certificar($id, $cert)
    {

        $rol = Ct_Rol_Pagos::find($id);

        $rol->update([
            'certificado' => $cert,
        ]);

        return "ok";
    }

    public function masivo_certificar_mes(Request $request)
    {

        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;

        $roles = Ct_Rol_Pagos::where('id_empresa', $id_empresa)->where('anio', $anio)->where('mes', $mes)->where('estado', '1')->where('certificado', '0')->get();

        foreach ($roles as $rol) {
            $rol->update([
                'certificado' => '1',
            ]);
        }

        return "ok";
    }

    public function masivos_horario_extra(Request $request)
    {
        //dd($request->all());
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $idusuario    = Auth::user()->id;
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];

        $proceso = Ct_Rol_Proceso::where('id_empresa', $id_empresa)->where('anio', $anio)->where('mes', $mes)->where('tipo_proceso', 'ROL_POR_EMPRESA')->first();

        if (is_null($proceso)) {
            return ['tipo' => 'err', 'mensaje' => 'Debe generar los roles primero'];
        }

        $asiento_rol = RolAsiento::where('id_empresa', $id_empresa)->where('anio', $anio)->where('mes', $mes)->where('estado', '1')->first();
        if (!is_null($asiento_rol)) {
            if (!is_null($asiento_rol->id_asiento)) {
                return ['tipo' => 'err', 'mensaje' => 'Ya existe un asiento contable para estos roles, no se puede modificar'];
            }
        }

        $fecha_actual    = Date('Y-m-d H:i:s');
        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = "he_" . $id_empresa . '_' . $anio . '_' . $mes . '_' . date('YmdHis') . "." . $extension;

        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));
        $rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;
        $prestamo = null;
        $id_proceso = Ct_Rol_Proceso::insertGetId([
            'id_empresa'    => $id_empresa,
            'anio'          => $anio,
            'mes'           => $mes,
            'tipo_proceso'  => 'HORAS_EXTRAS_POR_EMPRESA',
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ]);
        $procesadas=0;$no_procesadas=0;

        if ($r1) {
            Excel::filter('chunk')
                ->formatDates(true, 'Y-m-d')
                ->load($rutadelaimagen)
                ->chunk(250, function ($reader) use ($idusuario, $anio, $mes, $id_empresa, $fecha_actual, $ip_cliente, $nombre_original, $id_proceso, $procesadas, $no_procesadas) {

                    foreach ($reader as $book) {
                        $detalle_resultado = "";
                        //dd($book);
                        if (!is_null($book)) {

                            $continuar = true;

                            if (is_null($book->cedula)) {


                                /*$arr_det_log =[
                                    'id_empleado'           => $book->cedula,
                                    'nombre'                => $book->colaborador,
                                    'sueldo'                => $book->sueldo,
                                    'valor_horas50'         => $book->valor_horas50,
                                    'valor_horas100'        => $book->valor_horas100,
                                    'num_horas50'           => $book->num_horas50,
                                    'num_horas100'          => $book->num_horas100,
                                    'total50'               => $book->total50,
                                    'total100'              => $book->total100,
                                    'total'                 => $book->total,
                                    'id_usuariocrea'        => $idusuario,
                                    'id_usuariomod'         => $idusuario,
                                    'ip_creacion'           => $ip_cliente,
                                    'ip_modificacion'       => $ip_cliente,
                                    'id_proceso'            => $id_proceso,
                                    'detalle_resultado'     => 'REGISTRO SIN NUMERO DE CEDULA'
                                   
                                ];*/

                                //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                $detalle_resultado = 'REGISTRO SIN NUMERO DE CEDULA';
                                $no_procesadas++;$continuar=false;    
                            }

                            if ($continuar) {
                                $nomina = Ct_Nomina::where('id_user', $book->cedula)->where('id_empresa', $id_empresa)->first();
                                if (is_null($nomina)) {

                                    /*$arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'EMPLEADO NO EXISTE EN LA NOMINA'
                                    ];*/

                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'EMPLEADO NO EXISTE EN LA NOMINA';
                                    $no_procesadas++;$continuar=false;

                                }
                            }
                            //dd("h",$book);
                            if ($continuar) {

                                $rol = Ct_Rol_Pagos::where('id_user', $book->cedula)->where('mes', $mes)->where('anio', $anio)->where('estado', '1')->where('id_empresa', $id_empresa)->first();

                                if (is_null($rol)) {
                                    /*$arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'EMPLEADO SIN ROL GENERADO'
                                    ];*/

                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'EMPLEADO SIN ROL GENERADO';
                                    $no_procesadas++;$continuar=false;
                                }
                            }

                            if ($continuar) {

                                if (is_null($book->num_horas50)) {
                                    /*$arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'EMPLEADO SIN VALOR DE HORAS EXTRAS AL 50%'
                                    ];*/

                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'EMPLEADO SIN VALOR DE HORAS EXTRAS AL 50%';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if ($continuar) {

                                if (!is_numeric($book->num_horas50)) {
                                    /*$arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'ERROR EN FORMATO DEL VALOR DE HORAS EXTRAS AL 50%'
                                    ];*/

                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DEL VALOR DE HORAS EXTRAS AL 50%';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if ($continuar) {

                                if (is_null($book->num_horas100)) {
                                    /*$arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'EMPLEADO SIN VALOR DE HORAS EXTRAS AL 100%'
                                    ];*/

                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'EMPLEADO SIN VALOR DE HORAS EXTRAS AL 100%';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if ($continuar) {

                                if (!is_numeric($book->num_horas100)) {
                                    /*$arr_det_log =[
                                        'id_empleado'           => $book->cedula,
                                        'nombre'                => $book->colaborador,
                                        'sueldo'                => $book->sueldo,
                                        'valor_horas50'         => $book->valor_horas50,
                                        'valor_horas100'        => $book->valor_horas100,
                                        'num_horas50'           => $book->num_horas50,
                                        'num_horas100'          => $book->num_horas100,
                                        'total50'               => $book->total50,
                                        'total100'              => $book->total100,
                                        'total'                 => $book->total,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'id_proceso'            => $id_proceso,
                                        'detalle_resultado'     => 'ERROR EN FORMATO DEL VALOR DE HORAS EXTRAS AL 100%'
                                    ];*/

                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DEL VALOR DE HORAS EXTRAS AL 100%';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if ($continuar) {

                                if (!is_numeric($book->bono)) {
                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DEL BONO';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            // if($continuar){
                            //     if(is_null($book->obs_B)){
                            //         $detalle_resultado = 'ERROR LA OBSERVACION DEL BONO ESTA VACIO';
                            //         $no_procesadas++;$continuar=false;    
                            //     }
                            // }

                            if ($continuar) {

                                if (!is_numeric($book->imp_renta)) {
                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DEL IMPUESTO A LA RENTA';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            // if($continuar){
                            //     if(is_null($book->obs_IR)){
                            //         $detalle_resultado = 'ERROR LA OBSERVACION DEL IMPUESTO A LA RENTA ESTA VACIA';
                            //         $no_procesadas++;$continuar=false;    
                            //     }
                            // }

                            if ($continuar) {

                                if (!is_numeric($book->movilizacion)) {
                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DEL MOVILIZACION';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            // if($continuar){
                            //     if(is_null($book->obs_mov)){
                            //         $detalle_resultado = 'ERROR LA OBSERVACION DE LA MOVILIZACION ESTA VACIA';
                            //         $no_procesadas++;$continuar=false;    
                            //     }
                            // }

                            if ($continuar) {

                                if (!is_numeric($book->bono_iess)) {
                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DEL BONO IMPUTABLE';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            // if($continuar){
                            //     if(is_null($book->obs_Bi)){
                            //         $detalle_resultado = 'ERROR LA OBSERVACION DEL BONO IESS ESTA VACIA';
                            //         $no_procesadas++;$continuar=false;    
                            //     }
                            // }

                            if ($continuar) {

                                if (!is_numeric($book->alimentacion)) {
                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DEL ALIMENTACION';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            // if($continuar){
                            //     if(is_null($book->obs_alim)){
                            //         $detalle_resultado = 'ERROR LA OBSERVACION DE LA ALIMENTACION ESTA VACIA';
                            //         $no_procesadas++;$continuar=false;    
                            //     }
                            // }

                            if ($continuar) {

                                if (!is_numeric($book->parqueo)) {
                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DEL PARQUEO';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            // if($continuar){
                            //     if(is_null($book->obs_parq)){
                            //         $detalle_resultado = 'ERROR LA OBSERVACION DEL PARQUEO ESTA VACIA';
                            //         $no_procesadas++;$continuar=false;    
                            //     }
                            // }

                            if ($continuar) {

                                if (!is_numeric($book->seguro)) {
                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DEL SEGURO';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            // if($continuar){
                            //     if(is_null($book->obs_seg)){
                            //         $detalle_resultado = 'ERROR LA OBSERVACION DEL SEGURO ESTA VACIA';
                            //         $no_procesadas++;$continuar=false;    
                            //     }
                            // }

                            if ($continuar) {

                                if (!is_numeric($book->multa)) {
                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DEL MULTA';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            // if($continuar){
                            //     if(is_null($book->obs_multa)){
                            //         $detalle_resultado = 'ERROR LA OBSERVACION DE LA MULTA ESTA VACIA';
                            //         $no_procesadas++;$continuar=false;    
                            //     }
                            // }

                            if ($continuar) {

                                if (!is_numeric($book->otros_egresos)) {
                                    //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                    $detalle_resultado = 'ERROR EN FORMATO DE OTROS EGRESOS';
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            // if($continuar){
                            //     if(is_null($book->obs_OE)){
                            //         $detalle_resultado = 'ERROR LA OBSERVACION DE OTROS EGRESOS ESTA VACIA';
                            //         $no_procesadas++;$continuar=false;    
                            //     }
                            // }

                            $bono                 = 0;
                            $renta                = 0;
                            $movilizacion         = 0;
                            $bono_iess            = 0;
                            $alimentacion         = 0;
                            $parqueo              = 0;
                            $seguro               = 0;
                            $multa                = 0;
                            $otros_egresos        = 0;

                            if ($book->imp_renta != null) {
                                $renta          = $book->imp_renta;
                            }
                            if ($book->bono != null) {
                                $bono           = $book->bono;
                            }

                            if ($book->movilizacion != null) {
                                $movilizacion   = $book->movilizacion;
                            }
                            if ($book->bono_iess != null) {
                                $bono_iess      = $book->bono_iess;
                            }
                            if ($book->alimentacion != null) {
                                $alimentacion   = $book->alimentacion;
                            }
                            if ($book->parqueo != null) {
                                $parqueo        = $book->parqueo;
                            }
                            if ($book->seguro != null) {
                                $seguro         = $book->seguro;
                            }
                            if ($book->multa != null) {
                                $multa          = $book->multa;
                            }
                            if ($book->otros_egresos != null) {
                                $otros_egresos = $book->otros_egresos;
                            }

                            if ($continuar) {
                                $rol = Ct_Rol_Pagos::where('id_user', $book->cedula)->where('mes', $mes)->where('anio', $anio)->where('estado', '1')->where('id_empresa', $id_empresa)->first();

                                if (!is_null($rol)) {
                                    $nomina = $rol->ct_nomina;
                                    $id_sucursal =  $nomina->id_sucursal;
                                    $id_empresa = $nomina->id_empresa;
                                    $nom_funcion = 'masivos_horario_extra';
                                    $detalle_rol = $rol->detalle->first();
                                    if (!is_null($detalle_rol)) {

                                        $arr_det = [
                                            'cantidad_horas50'  => $book->num_horas50,
                                            'cantidad_horas100' => $book->num_horas100,
                                            /////////////////////////////////////////
                                            'bonificacion'     => $bono,
                                            'impuesto_renta'   => $renta,
                                            'transporte'       => $movilizacion,
                                            'bono_imputable'   => $bono_iess,
                                            'alimentacion'     => $alimentacion,
                                            'parqueo'          => $parqueo,
                                            'seguro_privado'   => $seguro,
                                            'multa'            => $multa,
                                            'otros_egresos'    => $otros_egresos,
                                            'observacion_bono' => $book->obs_b,
                                            'observacion_alimentacion' => $book->obs_alim,
                                            'observ_seg_privado' => $book->obs_seg,
                                            'observ_imp_renta' => $book->obs_ir,
                                            'observacion_multa' => $book->obs_multa,
                                            'observacion_otro_egreso' => $book->obs_oe,
                                            'observacion_transporte' => $book->obs_mov,
                                            'observacion_bonoimp' => $book->obs_bi,
                                            'observacion_parqueo' => $book->obs_parq,
                                            ////////////////////////////////////////
                                        ];

                                        $detalle_rol->update($arr_det);
                                        CtLogRolNomina::guarda_log_masivo_horas_extras($arr_det, $id_sucursal, $id_empresa, $nom_funcion, $rol);
                                        $this->recalcular_db($rol->id);

                                        /*$arr_det_log =[
                                            'id_empleado'           => $book->cedula,
                                            'nombre'                => $book->colaborador,
                                            'sueldo'                => $book->sueldo,
                                            'valor_horas50'         => $book->valor_horas50,
                                            'valor_horas100'        => $book->valor_horas100,
                                            'num_horas50'           => $book->num_horas50,
                                            'num_horas100'          => $book->num_horas100,
                                            'total50'               => $book->total50,
                                            'total100'              => $book->total100,
                                            'total'                 => $book->total,
                                            'id_usuariocrea'        => $idusuario,
                                            'id_usuariomod'         => $idusuario,
                                            'ip_creacion'           => $ip_cliente,
                                            'ip_modificacion'       => $ip_cliente,
                                            'id_proceso'            => $id_proceso,
                                            'detalle_resultado'     => 'PROCESADO OK'
                                        ];*/

                                        //$detalle_log =Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                                        $detalle_resultado = 'PROCESADO OK';
                                        $procesadas++;
                                    }
                                }
                            }

                            $arr_det_log = [
                                'id_empleado'           => $book->cedula,
                                'nombre'                => $book->colaborador,
                                'sueldo'                => $book->sueldo,
                                'valor_horas50'         => $book->valor_horas50,
                                'valor_horas100'        => $book->valor_horas100,
                                'num_horas50'           => $book->num_horas50,
                                'num_horas100'          => $book->num_horas100,
                                'total50'               => $book->total50,
                                'total100'              => $book->total100,
                                'total'                 => $book->total,
                                'id_usuariocrea'        => $idusuario,
                                'id_usuariomod'         => $idusuario,
                                'ip_creacion'           => $ip_cliente,
                                'ip_modificacion'       => $ip_cliente,
                                'id_proceso'            => $id_proceso,
                                'detalle_resultado'     => $detalle_resultado,
                                ///////////NUEVOS RUBROS///////////////////////
                                'bono'                  => $bono,
                                'renta'                 => $renta,
                                'movilizacion'          => $movilizacion,
                                'bono_iess'             => $bono_iess,
                                'alimentacion'          => $alimentacion,
                                'parqueo'               => $parqueo,
                                'seguro'                => $seguro,
                                'multa'                 => $multa,
                                'otros_egresos'         => $otros_egresos,
                                'obs_bono'              => $book->obs_b,
                                'obs_bono_iess'         => $book->obs_bi,
                                'obs_alimentacion'      => $book->obs_alim,
                                'obs_seguro'            => $book->obs_seg,
                                'obs_imp_renta'         => $book->obs_ir,
                                'obs_multa'             => $book->obs_multa,
                                'obs_otros_egresos'     => $book->obs_oe,
                                'obs_parqueo'           => $book->obs_parq,
                                ///////////////////////////////////////////////
                            ];

                            $detalle_log = Ct_Rh_Detalle_Horas_Extras::insertGetId($arr_det_log);
                        }
                    }

                    $proceso = Ct_Rol_Proceso::find($id_proceso);

                    $proceso->update([
                        'procesados'    =>  $procesadas,
                        'no_procesados'  =>  $no_procesadas,
                    ]);
                });
        }

        $proceso = Ct_Rol_Proceso::find($id_proceso);

        if (is_null($proceso->procesados)) {
            $proceso->update([
                'observacion' => 'Archivo sin registros válidos'
            ]);
            return ['tipo' => 'err', 'mensaje' => 'Archivo sin registros'];
        }
        return ['tipo' => 'ok', 'mensaje' => 'Procesados: ' . $proceso->procesados . ' No Procesados: ' . $proceso->no_procesados];
    }

    public function recalcular_db($id_rol)
    {

        $pct_fondo_reserva = 0;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $rol         = Ct_Rol_Pagos::find($id_rol);
        $detalle_rol = $rol->detalle->first();

        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'recalcular_db';
        $procent_iess = "IESS 9.45%";
        if ($rol->pasante == 1) {
            $procent_iess = "IESS 17.6%";
        }
        $val_fond_reserv = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 4)->first();
        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $nomina->id_empresa)
            ->where('tipo', 3)->first();

        $total_cuota = 0;
        $total_cuota_ini = 0;
        $total_otros_anticipos = 0;
        $total_quota_quirog = 0;
        $total_quota_hipot = 0;

        $sobre_tiempo_50 = 0;
        $sobre_tiempo_100 = 0;
        $fondo_reserva = 0;
        $decimo_tercero = 0;
        $decimo_cuarto = 0;
        $fondo_reserva_acumulado = 0;
        $decimo_tercero_acumulado = 0;
        $decimo_cuarto_acumulado = 0;

        $total_cuota = $rol->prestamo_detalle->sum('valor_cuota');
        $total_cuota_ini = $rol->saldo_detalle->sum('valor_cuota');
        $total_otros_anticipos = $rol->otros_anticipos->sum('monto_anticipo');
        $total_quota_hipot = $rol->cuotas_hipotecarios->sum('valor_cuota');
        $total_quota_quirog = $rol->cuotas_quirografario->sum('valor_cuota');

        $ingresos_adicional = $rol->rubros_detalles->where('ingreso_egreso', '0')->where('estado','1')->sum('valor');
        $egresos_adicional = $rol->rubros_detalles->where('ingreso_egreso', '1')->where('estado','1')->sum('valor');

        //1:VERIFICAR ESTA CARGADA LA VARIABLE NOMINA Y VALOR DEL FONDO DE RESERVA;
        //dd($detalle_rol);
        $dias_laborados    = $detalle_rol->dias_laborados;
        $cantidad_horas50  = $detalle_rol->cantidad_horas50;
        $cantidad_horas100 = $detalle_rol->cantidad_horas100;
        $bonificacion      = $detalle_rol->bonificacion;
        $transporte        = $detalle_rol->transporte;
        $vacaciones        = $detalle_rol->vacaciones;
        $exam_laboratorio  = $detalle_rol->exam_laboratorio;

        $multa             = $detalle_rol->multa;
        $impuesto_renta    = $detalle_rol->impuesto_renta;
        $sueldo_mensual    = $nomina->sueldo_neto;
        $dias_nomina        = $nomina->dias_laborables;

        $bono_imputable     = $detalle_rol->bono_imputable;
        $alimentacion       = $detalle_rol->alimentacion;
        $fecha_ingreso      = $nomina->fecha_ingreso;
        if ($nomina->fecha_ant != null) {
            $fecha_ingreso = $nomina->fecha_ant;
        }
        $tipo_fondo_reserva = $nomina->pago_fondo_reserva;
        $tipo_decimo_cuarto      = $nomina->decimo_cuarto;
        $tipo_decimo_tercero     = $nomina->decimo_tercero;
        $aporte_personal         = $nomina->aportepersonal->valor;
        $seguro_privado          = $detalle_rol->seguro_privado;
        $anticipo_quincena       = $detalle_rol->anticipo_quincena;
        $pct_fondo_reserva   = $val_fond_reserv->valor != null ? $val_fond_reserv->valor : 0;
        $valor_sueldo_basico = $val_sal_basico->valor;
        $fond_reserv_cobrar  = $detalle_rol->fond_reserv_cobrar;
        $otros_egresos       = $detalle_rol->otros_egresos;
        $parqueo             = $detalle_rol->parqueo;
        $jornada             = $rol->jornada;

        //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
        /*$sueldo_mensual = $sueldo_mensual / 30;

        $sueldo_mensual = $sueldo_mensual * $dias_laborados;
        //dd($sueldo_mensual);
        $sueldo_mensual = round($sueldo_mensual,2);*/
        $horas_dias = 0;
        $horas_mes = 0;
        if ($nomina->jornada != '0') {
            //$dias_laborados = $nomina->dias_laborables;
            $horas_dias = $rol->cantidad_horas;
            // $horas_mes = $rol->horas_mes;
        }

        $sueldo_mensual = $this->calcular_sueldo_neto($jornada, $horas_dias, $dias_laborados, $sueldo_mensual);
        //CALCULO SOBRETIEMPO 50
        //$sobre_tiempo_50 = ((($sueldo_mensual/30)/8) * 1.50) * $cantidad_horas50;
        $sobre_tiempo_50 = ((($nomina->sueldo_neto / 30) / 8) * 1.50) * $cantidad_horas50;
        if ($nomina->jornada != '0') {
            $sobre_tiempo_50 = ((($sueldo_mensual / 30) / 8) * 1.50) * $cantidad_horas50;
        }
        $sobre_tiempo_50 = round($sobre_tiempo_50, 2);
        //CALCULO SOBRETIEMPO 100
        //$sobre_tiempo_100 = ((($sueldo_mensual/30)/8) * 2.00) * $cantidad_horas100;
        $sobre_tiempo_100 = ((($nomina->sueldo_neto / 30) / 8) * 2.00) * $cantidad_horas100;
        if ($nomina->jornada != '0') {
            $sobre_tiempo_100 = ((($sueldo_mensual / 30) / 8) * 2.00) * $cantidad_horas50;
        }
        $sobre_tiempo_100 = round($sobre_tiempo_100, 2);

        //2:SUELDO_MENSUAL = SUELDO_NOMINA / 30 * DIAS_LABORADOS
        /*$sueldo_mensual = $sueldo_mensual / 30;
        $sueldo_mensual = $sueldo_mensual * $dias_laborados;
        $sueldo_mensual = round($sueldo_mensual,2);*/

        //3:BASE_IESS = SUELDO MES + VALOR_EXTRAS50 + VALOR_EXTRAS100 + BONO_IMPUTABLE
        $base_iess = $sueldo_mensual + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;

        //////////////////////////////////////////////
        $sueldo_nomina     = $nomina->sueldo_neto;
        $base_nomina       = $sueldo_nomina + $sobre_tiempo_50 + $sobre_tiempo_100 + $bono_imputable;

        //4:FONDO DE RESERVA
        //SI HOY ES ELM ES SIGUIENTE DEBO SETEAR LA FECHA AL ULTIMO DIA DEL MES ANTERIOR
        //$fecha         = date("Y-m-d");
        $anioActual = $rol->anio;
        $mesActual = $rol->mes;
        $ultimoDia = date('Y-m-t', strtotime($anioActual . '-' . $mesActual));
        $fecha      = $ultimoDia;
        //$fecha   = date("Y-m-d", strtotime($detalle_rol->created_at)); //dd($vt_nf_fecha);
        $fec           = new DateTime($fecha);
        $vt_dia        = date("d", strtotime($fecha_ingreso));
        $fec2          = new DateTime($fecha_ingreso);
        $diff          = $fec->diff($fec2); //dd($diff);
        $intervalMeses = $diff->format("%m");
        $intervalAnos  = $diff->format("%y") * 12; //dd($intervalMeses,$intervalAnos);
        $añosAhora     = $diff->format("%y"); //dd($añosAhora);
        $intervalDias  = $diff->format("%d");
        $diaActual     = $fec->format("d");
        $meses_totales = $intervalMeses + $intervalAnos; //dd($meses_totales);

        if ($id_rol == '1785') {
            //dd($añosAhora, $meses_totales, $fecha, $vt_dia, $dias_laborados, $base_iess, $pct_fondo_reserva, $diff, $fecha_ingreso);
        }
        $fr_anterior = Ct_Rh_Fondo_Reserva_Anterior::where('id_nomina',$nomina->id)->where('estado',1)->first();
        if(!is_null($fr_anterior)){
            $meses_totales += $fr_anterior->total_meses_anterior;
            $añosAhora += $fr_anterior->total_anios_anterior;
        }

        $arr_fr = $this->calculo_fondo_reserva($añosAhora, $nomina, $meses_totales, $dias_laborados, $base_iess, $base_nomina, $jornada, $vt_dia, $val_fond_reserv);


        $fondo_reserva = $arr_fr['fondo_reserva'];
        $fondo_reserva_acumulado = $arr_fr['fondo_reserva_acumulado'];

        $decimo_tercero_acumulado = 0;
        $decimo_cuarto_acumulado = 0;
        //5:DECIMO TERCER
        /*if($tipo_decimo_tercero == 2){
            $decimo_tercero = $base_iess / 12;    
        }
        else{
            $decimo_tercero_acumulado =  $base_iess / 12;  
        }
        $decimo_tercero_acumulado = round($decimo_tercero_acumulado,2);
        $decimo_tercero = round($decimo_tercero,2);*/

        //6:DECIMO CUARTO
        /*if($tipo_decimo_cuarto == 2){
            $decimo_cuarto = $valor_sueldo_basico / 12;    
        }else{
              $decimo_cuarto_acumulado = $valor_sueldo_basico / 12; 
        }
        $decimo_cuarto_acumulado = round($decimo_cuarto_acumulado,2);
        $decimo_cuarto = round($decimo_cuarto,2);*/

        //7:PORCENTAJE IESS
        $porcentaje_iess = $base_iess * $aporte_personal / 100;
        $porcentaje_iess = round($porcentaje_iess, 2);
        $valor_base_iess = 0;
        if ($nomina->extension_conyuge == 1) {
            $ct_rh_valores = Ct_Rh_Valores::where("id_empresa", $nomina->id_empresa)->where('tipo', 5)->first();
            $valor_base_iess = ($base_iess * $ct_rh_valores->valor) / 100;
            $valor_base_iess = round($valor_base_iess, 2);
        }

        if ($rol->id_user == '0913954913' and $rol->id == '35') {
            $valor_base_iess  = $detalle_rol->extension_conyuge;
        }

        if ($rol->id_user == '0924300650' and $rol->id == '2323') {
            $fondo_reserva  = $detalle_rol->fondo_reserva;
        }
        if ($rol->id_user == '0919747105' and $rol->id == '537') {
            $fondo_reserva  = $detalle_rol->fondo_reserva;
        }
        if ($rol->id_user == '0803498526' and $rol->id == '2887') {
            $fondo_reserva  = $detalle_rol->fondo_reserva;
        }
        if ($rol->id_user == '0923325724' and $rol->id == '2903') {
            $fondo_reserva  = $detalle_rol->fondo_reserva;
        }
        if ($rol->id_user == '0930511878' and $rol->id == '663') {
            $fondo_reserva  = $detalle_rol->fondo_reserva;
        }
        
        if ($rol->id_user == '0953192580' and $rol->id == '60') {
            $valor_base_iess  = $detalle_rol->extension_conyuge;
        }

        if ($rol->id_user == '0913686176' and $rol->id == '2240') {
            $valor_base_iess  = $detalle_rol->extension_conyuge;
        }

        $pasante       = $rol->pasante;
        $jornada       = $rol->jornada;

        $dter = $this->calculo_decimo_tercer($tipo_decimo_tercero, $base_iess, $pasante);

        $decimo_tercero = $dter['decimo_tercero'];
        $decimo_tercero_acumulado = $dter['decimo_tercero_acumulado'];

        //$dcua = $this->calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada);
        $dcua = $this->new_calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada, $dias_nomina);

        $decimo_cuarto = $dcua['decimo_cuarto'];
        $decimo_cuarto_acumulado = $dcua['decimo_cuarto_acumulado'];



        $total_ingresos = $base_iess + $bonificacion + $fondo_reserva + $decimo_tercero + $decimo_cuarto + $alimentacion + $transporte + $vacaciones + $ingresos_adicional;

        $total_ingresos = round($total_ingresos, 2);
        $total_egresos  = $porcentaje_iess + $multa + $anticipo_quincena + $total_cuota_ini + $total_otros_anticipos + $total_cuota + $seguro_privado + $impuesto_renta + $total_quota_quirog + $total_quota_hipot + $otros_egresos + $exam_laboratorio + $parqueo + $fond_reserv_cobrar + $valor_base_iess + $egresos_adicional;
        $total_egresos = round($total_egresos, 2);



        $valor_vacacion = $this->calculo_vacaciones($base_iess, $fecha_ingreso, $pasante);

        $neto_recibir = $total_ingresos - $total_egresos;

        $arr_new_db = [
            'dias_laborados'              => $dias_laborados,
            'base_iess'                   => $base_iess,
            'sueldo_mensual'              => $sueldo_mensual,
            'cantidad_horas50'            => $cantidad_horas50,
            'sobre_tiempo50'              => $sobre_tiempo_50,
            'cantidad_horas100'           => $cantidad_horas100,
            'sobre_tiempo100'             => $sobre_tiempo_100,
            'bonificacion'                => $bonificacion,
            'alimentacion'                => $alimentacion,
            'transporte'                  => $transporte,
            'vacaciones'                  => $vacaciones,
            'bono_imputable'              => $bono_imputable,
            'exam_laboratorio'            => $exam_laboratorio,
            'fondo_reserva'               => $fondo_reserva,
            'decimo_tercero'              => $decimo_tercero,
            'decimo_cuarto'               => $decimo_cuarto,
            'porcentaje_iess'             => $porcentaje_iess,
            'seguro_privado'              => $seguro_privado,
            'impuesto_renta'              => $impuesto_renta,
            'multa'                       => $multa,
            'fond_reserv_cobrar'          => $fond_reserv_cobrar,
            'otros_egresos'               => $otros_egresos,
            'prestamos_empleado'          => $total_cuota,
            'saldo_inicial_prestamo'      => $total_cuota_ini,
            'anticipo_quincena'           => $anticipo_quincena,
            //'observacion_bono'          => $request['observacion_bono'],
            //'observacion_alimentacion'  => $request['observacion_alimentacion'],
            //'observ_seg_privado'        => $request['observacion_seg_priv'],
            //'observ_imp_renta'          => $request['observacion_imp_rent'],
            'otro_anticipo'               => $total_otros_anticipos,
            //'observacion_multa'         => $request['observ_multa'],
            //'observacion_fondo_cobrar'  => $request['obs_fond_cob_trab'],
            //'observacion_prestamo'      => $request['concepto_prestamo'],
            //'observacion_saldo_inicial' => $request['obser_saldo_inicial'],
            //'observacion_anticip_quinc' => $request['concepto_quincena'],
            //'observacion_otro_anticip'  => $request['concep_otros_anticipos'],
            //'observacion_transporte'    => $request['observacion_transporte'],
            //'observacion_bonoimp'       => $request['observacion_bonoimp'],
            //'observ_examlaboratorio'    => $request['observ_examlaboratorio'],
            //'observacion_otro_egreso'   => $request['obs_otros_egres_trab'],
            'total_quota_quirog'          => $total_quota_quirog,
            'total_quota_hipot'           => $total_quota_hipot,
            'total_ingresos'              => $total_ingresos,
            'total_egresos'               => $total_egresos,
            'neto_recibido'               => $neto_recibir,
            'id_usuariomod'               => $idusuario,
            'ip_modificacion'             => $ip_cliente,
            'parqueo'                     => $parqueo,
            'fondo_reserva_acumulado'     => $fondo_reserva_acumulado, ////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (4) 
            'decimo_cuarto_acumulado'     => $decimo_cuarto_acumulado,
            'decimo_tercero_acumulado'    => $decimo_tercero_acumulado,
            'extension_conyuge'           => $valor_base_iess,
            'valor_vacacion'              => $valor_vacacion,

        ];

        $detalle_rol->update($arr_new_db);
        CtLogRolNomina::guarda_log_recalcular_db_rol($arr_new_db, $id_sucursal, $id_empresa, $nom_funcion, $rol->id);

        return "ok";
    }

    public function detalle_he_rol($id_proceso)
    {

        $detalles = Ct_Rh_Detalle_Horas_Extras::where('id_proceso', $id_proceso)->get();

        return view('contable.nuevo_rol_pago.detalle_proceso', ['detalles' => $detalles]);
    }

    public function detalle_iess_rol($id_proceso)
    {

        $detalles = Ct_Rh_Detalle_Prestamos_Subidos::where('id_proceso', $id_proceso)->get();
        //dd($detalles);
        return view('contable.nuevo_rol_pago.detalle_proceso_iess', ['detalles' => $detalles]);
    }

    public function masivos_prestamos(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $prestamo   = $request->prestamos;
        if ($prestamo == '1') {
            $tipo_pres  = 'QUIRO';
        } else {
            $tipo_pres  = 'HIPOT';
        }

        //dd($prestamo);
        $idusuario    = Auth::user()->id;
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];

        $proceso = Ct_Rol_Proceso::where('id_empresa', $id_empresa)->where('anio', $anio)->where('mes', $mes)->where('tipo_proceso', 'ROL_POR_EMPRESA')->first();

        if (is_null($proceso)) {
            return ['tipo' => 'err', 'mensaje' => 'Debe generar los roles primero'];
        }

        $asiento_rol = RolAsiento::where('id_empresa', $id_empresa)->where('anio', $anio)->where('mes', $mes)->where('estado', '1')->first();
        if (!is_null($asiento_rol)) {
            if (!is_null($asiento_rol->id_asiento)) {
                return ['tipo' => 'err', 'mensaje' => 'Ya existe un asiento contable para estos roles, no se puede modificar'];
            }
        }

        $fecha_actual    = Date('Y-m-d H:i:s');
        $nombre_original = $request['archivo']->getClientOriginalName();
        $extension       = $request['archivo']->getClientOriginalExtension();
        $nuevo_nombre    = "hp_" . $tipo_pres . '_' . $id_empresa . '_' . $anio . '_' . $mes . '_' . date('YmdHis') . "." . $extension;
        //dd($nuevo_nombre);

        $r1 = Storage::disk('public')->put($nuevo_nombre, \File::get($request['archivo']));
        $rutadelaimagen = base_path() . '/storage/app/avatars/' . $nuevo_nombre;

        $id_proceso = Ct_Rol_Proceso::insertGetId([
            'id_empresa'    => $id_empresa,
            'anio'          => $anio,
            'mes'           => $mes,
            'tipo_proceso'  => 'PRESTAMOS_' . $tipo_pres . '_POR_EMPRESA',
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ]);
        $procesadas=0;$no_procesadas=0;

        if ($r1) {
            Excel::filter('chunk')
                ->formatDates(true, 'Y-m-d')
                ->load($rutadelaimagen)
                ->chunk(250, function ($reader) use ($idusuario, $anio, $mes, $prestamo, $id_empresa, $fecha_actual, $ip_cliente, $nombre_original, $id_proceso, $procesadas, $no_procesadas, $tipo_pres) {
                    //dd($reader);
                    $cant = 0;
                    foreach ($reader as $book) {

                        if (!is_null($book)) {

                            $continuar = true;

                            if (is_null($book->cedula)) {
                                $arr_det_log = [

                                    'id_proceso'            => $id_proceso,
                                    'numero'                => $book->no,
                                    'cedula'                => $book->cedula,
                                    'nombres'               => $book->nombres,
                                    'detalle'               => $book->detalle,
                                    'nut'                   => $book->nut,
                                    'valor'                 => $book->valor,
                                    'id_usuariocrea'        => $idusuario,
                                    'id_usuariomod'         => $idusuario,
                                    'ip_creacion'           => $ip_cliente,
                                    'ip_modificacion'       => $ip_cliente,
                                    'detalle_resultado'     => 'REGISTRO SIN NUMERO DE CEDULA'
                                ];

                                $detalle_log = Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                $no_procesadas++;$continuar=false;    
                            }

                            if ($continuar) {
                                $nomina = Ct_Nomina::where('id_user', $book->cedula)->where('id_empresa', $id_empresa)->first();
                                if (is_null($nomina)) {

                                    $arr_det_log = [

                                        'id_proceso'            => $id_proceso,
                                        'numero'                => $book->no,
                                        'cedula'                => $book->cedula,
                                        'nombres'               => $book->nombres,
                                        'detalle'               => $book->detalle,
                                        'nut'                   => $book->nut,
                                        'valor'                 => $book->valor,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'detalle_resultado'     => 'EMPLEADO NO EXISTE EN LA NOMINA'
                                    ];

                                    $detalle_log = Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;

                                }
                            }

                            if ($continuar) {

                                $rol = Ct_Rol_Pagos::where('id_user', $book->cedula)->where('mes', $mes)->where('anio', $anio)->where('estado', '1')->where('id_empresa', $id_empresa)->first();

                                if (is_null($rol)) {
                                    $arr_det_log = [

                                        'id_proceso'            => $id_proceso,
                                        'numero'                => $book->no,
                                        'cedula'                => $book->cedula,
                                        'nombres'               => $book->nombres,
                                        'detalle'               => $book->detalle,
                                        'nut'                   => $book->nut,
                                        'valor'                 => $book->valor,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'detalle_resultado'     => 'EMPLEADO SIN ROL GENERADO'
                                    ];

                                    $detalle_log = Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;
                                }
                            }

                            if ($continuar) {

                                if (!is_numeric($book->valor)) {
                                    $arr_det_log = [

                                        'id_proceso'            => $id_proceso,
                                        'numero'                => $book->no,
                                        'cedula'                => $book->cedula,
                                        'nombres'               => $book->nombres,
                                        'detalle'               => $book->detalle,
                                        'nut'                   => $book->nut,
                                        'valor'                 => $book->valor,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'detalle_resultado'     => 'ERROR EN FORMATO DE VALOR DEL PRESTAMO'
                                    ];

                                    $detalle_log = Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if ($continuar) {

                                if (is_null($book->valor)) {
                                    $arr_det_log = [

                                        'id_proceso'            => $id_proceso,
                                        'numero'                => $book->no,
                                        'cedula'                => $book->cedula,
                                        'nombres'               => $book->nombres,
                                        'detalle'               => $book->detalle,
                                        'nut'                   => $book->nut,
                                        'valor'                 => $book->valor,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'detalle_resultado'     => 'VALOR DEL PRESTAMO VACIO'
                                    ];

                                    $detalle_log = Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                    $no_procesadas++;$continuar=false;    
                                }
                            }

                            if ($continuar) {

                                $rol = Ct_Rol_Pagos::where('id_user', $book->cedula)->where('mes', $mes)->where('anio', $anio)->where('estado', '1')->where('id_empresa', $id_empresa)->first();


                                if (!is_null($rol)) {
                                    $nomina = $rol->ct_nomina;
                                    $id_sucursal =  $nomina->id_sucursal;
                                    $id_empresa = $nomina->id_empresa;
                                    $nom_funcion = 'masivos_prestamos';
                                    if ($prestamo == 1) {

                                        $arr_qui = [
                                            'id_rol'            => $rol->id,
                                            'valor_cuota'       => $book->valor,
                                            'detalle_cuota'     => $book->detalle,
                                            'id_usuariocrea'    => $idusuario,
                                            'id_usuariomod'     => $idusuario,
                                            'ip_creacion'       => $ip_cliente,
                                            'ip_modificacion'   => $ip_cliente,
                                        ];

                                        Ct_Rh_Cuotas_Quirografario::create($arr_qui);
                                        CtLogRolNomina::guarda_log_masivo_prestamos_quiro($arr_qui, $id_sucursal, $id_empresa, $nom_funcion, $tipo_pres, $rol);
                                    } else {
                                        $arr_hip = [
                                            'id_rol'            => $rol->id,
                                            'valor_cuota'       => $book->valor,
                                            'detalle_cuota'     => $book->detalle,
                                            'id_usuariocrea'    => $idusuario,
                                            'id_usuariomod'     => $idusuario,
                                            'ip_creacion'       => $ip_cliente,
                                            'ip_modificacion'   => $ip_cliente,
                                        ];

                                        Ct_Rh_Cuotas_Hipotecarios::create($arr_hip);
                                        CtLogRolNomina::guarda_log_masivo_prestamos_hipo($arr_hip, $id_sucursal, $id_empresa, $nom_funcion, $tipo_pres, $rol);
                                    }

                                    $this->recalcular_db($rol->id);

                                    $arr_det_log = [

                                        'id_proceso'            => $id_proceso,
                                        'numero'                => $book->no,
                                        'cedula'                => $book->cedula,
                                        'nombres'               => $book->nombres,
                                        'detalle'               => $book->detalle,
                                        'nut'                   => $book->nut,
                                        'valor'                 => $book->valor,
                                        'id_usuariocrea'        => $idusuario,
                                        'id_usuariomod'         => $idusuario,
                                        'ip_creacion'           => $ip_cliente,
                                        'ip_modificacion'       => $ip_cliente,
                                        'detalle_resultado'     => 'PROCESADO'
                                    ];

                                    $detalle_log = Ct_Rh_Detalle_Prestamos_Subidos::insertGetId($arr_det_log);
                                    $procesadas++;
                                }
                            }
                        }


                        $cant++;
                    }
                    $proceso = Ct_Rol_Proceso::find($id_proceso);
                    $proceso->update([
                        'procesados'    =>  $procesadas,
                        'no_procesados'  =>  $no_procesadas,
                    ]);
                });
        }

        $proceso = Ct_Rol_Proceso::find($id_proceso);

        if (is_null($proceso->procesados)) {
            //dd($proceso);
            $proceso->update([
                'observacion' => 'Archivo sin registros válidos'
            ]);
            return ['tipo' => 'err', 'mensaje' => 'Archivo sin registros'];
        }
        //dd("fin");
        return ['tipo' => 'ok', 'mensaje' => 'Procesados: ' . $proceso->procesados . ' No Procesados: ' . $proceso->no_procesados];
    }

    public function he_valida_ejecutado(Request $request)
    {

        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $prestamo   = $request->prestamos;

        $proceso = Ct_Rol_Proceso::where('id_empresa', $id_empresa)->where('anio', $anio)->where('mes', $mes)->where('tipo_proceso', 'HORAS_EXTRAS_POR_EMPRESA')->first();

        if (is_null($proceso)) {
            return ['estado' => 'ok'];
        }

        return ['estado' => 'wrn', 'mensaje' => 'Ya existe un proceso ingresado, Desea Continuar'];
    }

    public function p_valida_ejecutado(Request $request)
    {

        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;
        $prestamo   = $request->prestamos;

        if ($prestamo == '1') { //QUIROGRAFARIO
            $proceso = Ct_Rol_Proceso::where('id_empresa', $id_empresa)->where('anio', $anio)->where('mes', $mes)->where('tipo_proceso', 'PRESTAMOS_QUIRO_POR_EMPRESA')->first();
        } else { //HIPOTECARIO
            $proceso = Ct_Rol_Proceso::where('id_empresa', $id_empresa)->where('anio', $anio)->where('mes', $mes)->where('tipo_proceso', 'PRESTAMOS_HIPOT_POR_EMPRESA')->first();
        }


        if (is_null($proceso)) {
            return ['estado' => 'ok'];
        }

        return ['estado' => 'wrn', 'mensaje' => 'Ya existe un proceso ingresado, Desea Continuar'];
    }

    public function asientos_por_generar($anio, $mes, Request $request)
    {

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $id_empresa     = $request->session()->get('id_empresa');
        $anio           = $anio;
        $mes            = $mes;
        $txt_mes        = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];

        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->where('ct_rol_pagos.anio', $anio)
            ->where('ct_rol_pagos.mes', $mes)
            ->where('ct_rol_pagos.id_empresa', $id_empresa)
            ->get();

        if (empty($roles)) {
            return ['msj' => "error", 'mensaje' => "No existe roles de pago en el Año : {$anio} Mes : {$txt_mes}, verifique la fecha de la creación del asiento"];
        }

        $rol_asiento    = RolAsiento::where('id_empresa', $id_empresa)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('estado', '1')
            ->first();

        if (!is_null($rol_asiento)) {

            return ['msj' => "ok", 'id_rol_asiento' => $rol_asiento->id];
        } else {


            $input_rol_asiento = [

                'id_empresa'      => $id_empresa,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'id_empresa'      => $id_empresa,
                'anio'            => $anio,
                'mes'             => $mes,
                'estado'          =>  1,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_rol_asiento = RolAsiento::insertGetId($input_rol_asiento);

            $rol_acum = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
                ->where('ct_rol_pagos.anio', $anio)
                ->where('ct_rol_pagos.mes', $mes)
                ->where('ct_rol_pagos.id_empresa', $id_empresa)
                ->join('ct_detalle_rol as drol', 'drol.id_rol', 'ct_rol_pagos.id')
                ->select(
                    'drol.id',
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
                    DB::raw("SUM(drol.parqueo) as parqueo"),
                    DB::raw("SUM(drol.fondo_reserva_acumulado) as fondo_reserva_acumulado"),
                    DB::raw("SUM(drol.extension_conyuge) as extension_conyuge")
                )->first();

            /* PRESTAMOS A EMPLEADOS MAS SALDO INICIAL ACTIVO */
            $val_prestamo = $rol_acum->total_prestamos;
            $val_prestamo += $rol_acum->total_sald_inicial;
            $extension_conyuge = $rol_acum->extension_conyuge;
            //mio
            if ($extension_conyuge > 0) {
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('EXTENSION_DE_CONYUGE');
                //dd($plan_cuentas);
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'EXTENSION DE CONYUGE',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $extension_conyuge,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS2',
                ]);
            }
            if (floatval($val_prestamo) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.03')->first(); 1.01.02.03.02
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'PRESTAMOS A EMPLEADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_prestamo,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* ANTICIPO SUELDOS EMPLEADOS ACTIV */
            $anticipo_sueldo = $rol_acum->total_anticipos;
            if (floatval($anticipo_sueldo) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.04')->first();1.01.02.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('OTROS_ANTICIPOS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ANTICIPOS QUINCENA',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $anticipo_sueldo,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* OTROS ANTICIPO */
            $otros_anticipo = $rol_acum->total_otro_anticipo;
            if (floatval($otros_anticipo) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.04')->first();1.01.02.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('OTROS_ANTICIPOS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'OTROS ANTICIPOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $otros_anticipo,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    //'doc_pendiente'     => 'E/V',  
                ]);
            }

            /* IMPUESTO A LA RENTA POR PAGAR PASIVO */
            $val_imp_renta = $rol_acum->total_imp_renta;
            if (floatval($val_imp_renta) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.11')->first();2.01.04.01.10
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_IMPUESTO_RENTA_PAGAR');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => '302 RET FTE RELACION DEPENDENCIA',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_imp_renta,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* APORTE INDIVIDUAL 9.45% PASIVO */
            $aporte_individual = $rol_acum->total_iess;
            if (floatval($aporte_individual) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.01')->first();2.01.04.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_APORTE_INDIVIDUAL_9.4');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'APORTE INDIVIDUAL 9.45 %',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $aporte_individual,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS2',
                    'editable'          => '2',
                ]);
            }

            /* CON EL IESS PRESTAMOS QUIROGRAFARIO PASIVO */
            $val_prest_quirografario = $rol_acum->total_quot_quirog;
            if (floatval($val_prest_quirografario) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.05')->first();2.01.04.03.05
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_PRESTAMOS_QUIROGRAFARIOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'PRESTAMOS QUIROGRAFARIOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_prest_quirografario,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS2',
                ]);
            }

            /* CON EL IESS PRESTAMOS HIPOTECARIOS PASIVO */
            $val_prest_hipotecario = $rol_acum->total_quot_hipot;
            if (floatval($val_prest_hipotecario) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.06')->first();2.01.04.03.06
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_PRESTAMOS_HIPOTECARIOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'PRESTAMOS HIPOTECARIOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_prest_hipotecario,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS2',
                ]);
            }

            /**********NETO A RECIBIR (CUENTA DESTINO)*******/
            $net_recibir = $rol_acum->total_neto_recibido;
            if (floatval($net_recibir) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.04.04.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_SUELDOS_SALARIOS_PAGAR');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'SUELDOS SALARIOS PAGAR',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $net_recibir,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'editable'          => '2',
                ]);
            }

            /* MULTAS EMPLEADOS Y FUNCIONARIO INGRESO */
            $val_multa = $rol_acum->total_multa;
            if (floatval($val_multa) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.01')->first();4.1.04.01.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_MULTAS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'MULTAS EMPLEADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_multa,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* PARQUEO */
            $val_parqueo = $rol_acum->parqueo;
            //$val_otro_egreso = $val_otro_egreso + $total_sum->parqueo;
            if (floatval($val_parqueo) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.04')->first();4.1.04.01.02
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_OTROS_INGRESOS_PARQUEO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'OTROS INGRESOS PARQUEOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_parqueo,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* MODIFICADA OTROS INGRESOS */
            $val_otro_egreso = $rol_acum->otro_egres;
            if (floatval($val_otro_egreso) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.04')->first();4.1.04.01.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_OTROS_INGRESOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'OTROS INGRESOS (INTERESES)',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_otro_egreso,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* PAGO DE SUELDOS Y SALARIOS HORAS EXTRAS AL 50 Y AL 100 BONO IMPUTABLE SUELDO GASTO */
            $sueldo_salario = $rol_acum->total_sueldo;
            if (floatval($sueldo_salario) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.01')->first(); //ROLPAGO_SUELDO 5.2.02.01.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROLPAGO_SUELDO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'SUELDOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $sueldo_salario,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS',
                ]);
            }

            /*Suma de Horas extras*/
            $sobre_tiempo_50  = $rol_acum->total_horas_50;
            $sobre_tiempo_100 = $rol_acum->total_horas_100;
            $total_sobre_tiempo = $sobre_tiempo_50 + $sobre_tiempo_100;
            if (floatval($total_sobre_tiempo) > 0) {
                //$plan_cuentas       = Plan_Cuentas::where('id', '5.2.02.01.02')->first();
                //5.2.02.01.02  ROL_PAGOS_HORAS_EXTRAS
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_HORAS_EXTRAS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'HORAS EXTRAS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $total_sobre_tiempo,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS',
                ]);
            }

            /* FONDO RESERVA MENSUALES GASTO */
            $val_fond_reserv = $rol_acum->total_fond_reserva;
            if (floatval($val_fond_reserv) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.02.03')->first();5.2.02.02.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'FONDOS DE RESERVA MENSUALES',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_fond_reserv,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* VACACIONES PAGADAS EN EL ROL */
            $val_vacaciones = $rol_acum->total_vacaciones;
            if (floatval($val_vacaciones) > 0) {
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_VACACIONES_(2)');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'VACACIONES PAGADAS EN ROL',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($val_vacaciones, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* FONDO RESERVA ACUMULADOS GASTO */
            $val_fond_reserv_acum = $rol_acum->fondo_reserva_acumulado;
            if (floatval($val_fond_reserv_acum) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.02.03')->first();5.2.02.02.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'FONDOS DE RESERVA ACUMULADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_fond_reserv_acum,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS'
                ]);
            }

            /* ALIMENTACION GASTO */
            $val_alimentacion = $rol_acum->total_alimentacion;
            if (floatval($val_alimentacion) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.01')->first();5.2.02.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_ALIMENTACION');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ALIMENTACION',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_alimentacion,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* BONIFICACION ESPECIAL GASTO */
            $val_bonif = $rol_acum->total_bono;
            if (floatval($val_bonif) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.03')->first();
                // 5.2.02.03.03   ROL_PAGOS_BONIFICACION_ESPECIAL 5.2.02.03.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_BONIFICACION_ESPECIAL');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'BONIFICACION ESPECIAL',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_bonif,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* BONO IMPUTABLE GASTO */
            $val_bono_imput = $rol_acum->total_bonoimp;
            if (floatval($val_bono_imput) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.04')->first();5.2.02.03.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_BONIFICACION_IMPUTABLE');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'BONIFICACION INPUTABLE',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_bono_imput,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS',
                ]);
            }

            /* TRANSPORTE Movilizacion y Transporte */
            $val_transporte = $rol_acum->total_transporte;
            if (floatval($val_transporte) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.06')->first();5.2.02.03.06
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_MOVILIZACION_TRANSPORTE');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'MOVILIZACIONES Y TRANSPORTE',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_transporte,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* FONDO RESERVA ACUMULADOS GASTO */
            $val_fond_reserv_acum = $rol_acum->fondo_reserva_acumulado;
            if (floatval($val_fond_reserv_acum) > 0) {
                //2.01.04.03.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA_SALE');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'FONDOS DE RESERVA ACUMULADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_fond_reserv_acum,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS2',
                ]);
            }

            /* DECIMO TERCERO GASTO */
            $val_decim_terc = $rol_acum->total_decimo_tercero;
            if (floatval($val_decim_terc) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.04')->first();5.2.02.01.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOTERCER_SUELDO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'DECIMO TERCER SUELDO',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_decim_terc,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* DECIMO CUARTO SUELDO GASTO */
            $val_decim_cuart = $rol_acum->total_decimo_cuarto;
            if (floatval($val_decim_cuart) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.05')->first();5.2.02.01.05
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOCUARTO_SUELDO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'DECIMO CUARTO SUELDO',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => $val_decim_cuart,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /*  SEGURO PRIVADO DEBEN REALIZAR OTRO ANTICIPO SE BLOQUEA CAMPO EN EL EDIT  */
            /*  FONDO RESERVA COBRAR REALIZAR OTRO ANTICIPO SE BLOQUEA CAMPO EN EL EDIT  */
            /*  EXAMEN DE LABORATORIO REALIZAR OTRO ANTICIPO SE BLOQUEA CAMPO EN EL EDIT */

            /*  MODIFICADO CORRECTO SEGURO ASISTENCIA MEDICA CXC SEGURO CUENTA X COBRAR SALUD GASTO */
            $val_seg_privado = $rol_acum->total_seguro_privado;
            //dd("123", floatval($val_seg_privado));

            if (floatval($val_seg_privado) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.05')->first();5.2.02.01.05
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_CUENTAS_POR_COBRAR_SEGURO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ROL_PAGOS_CUENTAS_POR_COBRAR_SEGURO',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => $val_seg_privado,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }
            /*if ($val_seg_privado > 0) {

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
            }*/

            /* FONDO DE RESERVA COBRAR TRABAJADORES MODIFICADO INGRESO */

            /*$val_res_cob = $total_sum->fond_reser_cob;

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
            }*/

            /* EXAMENES DE LABORATORIO */
            /*$val_exa_lab = $total_sum->total_exlaboratorio;

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
            }*/

            /*$plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DEDUCIBLES_DEBE');
            if(!is_null($plan_cuentas)){
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ROL_PAGOS_DEDUCIBLES_DEBE',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'editable'          => '1',
                ]);
            }*/

            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DEDUCIBLES_HABER');
            if (!is_null($plan_cuentas)) {
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ROL_PAGOS_DEDUCIBLES_HABER',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'editable'          => '2',
                ]);
            }

            return ['msj' => "ok", 'id_rol_asiento' => $id_rol_asiento];
        }
    }

    public function detalle_asientos_rol($id)
    {

        ///COLOCAR EN UN ARCHIVO DE CONFIGURACION DE NOMINA CUANDO ESTES REEELIGHT 2da vez que revisas vago
        /*$aporte_patronal = 11.15;
        $secap           = 1;*/
        $aporte_patronal   = 11.1501167220229;
        $aporte_patronal   = round($aporte_patronal, 2);
        $secap           = 1.00008572163328;
        $secap          = round($secap, 2);

        $cuentas_rol = RolAsiento::find($id);
        $idusuario      = Auth::user()->id;
        if (!is_null($cuentas_rol->id_sucursal)) {
            $id_sucursal = $cuentas_rol->id_sucursal;
        }


        $cuentas = $cuentas_rol->cuentas;
        $cuentas_iess = [];
        $total_aporte = 0;
        $valor_aporte = 0;
        $valor_secap = 0;
        $cuentas_iess2 = [];
        if ($cuentas_rol->id_asiento != null) {
            $cuentas_iess = $cuentas_rol->cuentas->where('doc_pendiente', 'IESS');
            $total_aporte = $cuentas_rol->cuentas->where('doc_pendiente', 'IESS')->sum('debe');
            /*$valor_aporte = $total_aporte * $aporte_patronal /100;
            $valor_aporte = round($valor_aporte,2);
            $valor_secap = $total_aporte * $secap /100;
            $valor_secap = round($valor_secap,2);*/
            $cuentas_iess2 = $cuentas_rol->cuentas->where('doc_pendiente', 'IESS2');
        }
        $lista_banco   = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $cuentas_rol->id_empresa)->get();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        /*$roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            //->join("ct_detalle_rol as ct_r","ct_rol_pagos.id","ct_r.id_rol")
            ->where('ct_rol_pagos.anio', $cuentas_rol->anio)
            ->where('ct_rol_pagos.mes', $cuentas_rol->mes)
            ->where('ct_rol_pagos.id_empresa', $cuentas_rol->id_empresa)
            ->get();*/

        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->join("ct_detalle_rol as ct_r", "ct_rol_pagos.id", "ct_r.id_rol")
            ->join('ct_nomina as nom', 'nom.id', 'ct_rol_pagos.id_nomina')
            ->where('ct_rol_pagos.mes', $cuentas_rol->mes)
            ->where('ct_rol_pagos.id_empresa', $cuentas_rol->id_empresa)
            ->select('nom.id_sucursal as id_sucursal', 'ct_r.decimo_tercero_acumulado', 'ct_r.decimo_cuarto_acumulado', 'ct_r.sueldo_mensual', 'ct_rol_pagos.*', 'ct_r.sueldo_mensual', 'ct_r.porcentaje_iess', 'ct_r.base_iess', 'ct_r.valor_vacacion');
        //->get();
        if (!is_null($id_sucursal)) {
            $roles = $roles->where('id_sucursal', $id_sucursal);
        }
        $roles = $roles->get();

        
        $dtercero = 0; $dcuarto = 0;$smensual = 0; $sueldo_pasante = 0;$vacaciones = 0;
        foreach ($roles as $rol) {
            $dtercero += $rol->decimo_tercero_acumulado;
            $dcuarto  += $rol->decimo_cuarto_acumulado;
            $vacaciones += $rol->valor_vacacion;
            //$smensual += $rol->sueldo_mensual; 
            $smensual += $rol->base_iess;
            $pct_apte = $rol->porcentaje_iess / $rol->base_iess;
            $pct_apte = round($pct_apte * 100, 2);
            if ($pct_apte > 17) {
                $sueldo_pasante += $rol->sueldo_mensual;
            }
        }

        $total_aporte = $total_aporte - $sueldo_pasante;
        $valor_aporte = $total_aporte * $aporte_patronal / 100;
        $valor_aporte = round($valor_aporte, 2);
        $valor_secap = $total_aporte * $secap / 100;
        $valor_secap = round($valor_secap, 2);
        ////////QUE PASA CON LOS QUE TIENEN MAS DE 15 DIAS 
        //$vacaciones = $smensual / 24 ;
        //$vacaciones  = round($vacaciones, 2);  
        $arr_beneficios = $this->beneficios($dtercero, $dcuarto, $vacaciones);
        //dd($arr_beneficios);


        return view('contable.nuevo_rol_pago.cuentas', ['cuentas_rol' => $cuentas_rol, 'cuentas' => $cuentas, 'lista_banco' => $lista_banco, 'bancos' => $bancos, 'tipo_pago_rol' => $tipo_pago_rol, 'roles' => $roles, 'cuentas_iess' => $cuentas_iess, 'aporte_patronal' => $aporte_patronal, 'secap' => $secap, 'valor_aporte' => $valor_aporte, 'valor_secap' => $valor_secap, 'total_aporte' => $total_aporte, 'cuentas_iess2' => $cuentas_iess2, 'arr_beneficios' => $arr_beneficios, 'sueldo_pasante' => $sueldo_pasante]);
    }

    private function beneficios($dtercero, $dcuarto, $vacaciones)
    {
        $arr_beneficios = [];
        $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOTERCER_SUELDO');
        if (!is_null($plan_cuentas)) {
            array_push($arr_beneficios, ['id_cuenta' => $plan_cuentas->cuenta_guardar, 'nombre' => 'DECIMO TERCER SUELDO', 'debe' => $dtercero, 'haber' => 0]);
            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOCUARTO_SUELDO');
            array_push($arr_beneficios, ['id_cuenta' => $plan_cuentas->cuenta_guardar, 'nombre' => 'DECIMO CUARTO SUELDO', 'debe' => $dcuarto, 'haber' => 0]);
            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_VACACIONES_(5)');
            array_push($arr_beneficios, ['id_cuenta' => $plan_cuentas->cuenta_guardar, 'nombre' => 'VACACIONES', 'debe' => $vacaciones, 'haber' => 0]);
            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOTERCER_SUELDO_(2)');
            array_push($arr_beneficios, ['id_cuenta' => $plan_cuentas->cuenta_guardar, 'nombre' => 'DECIMO TERCER SUELDO', 'debe' => 0, 'haber' => $dtercero]);
            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOCUARTO_SUELDO_(2)');
            array_push($arr_beneficios, ['id_cuenta' => $plan_cuentas->cuenta_guardar, 'nombre' => 'DECIMO CUARTO SUELDO', 'debe' => 0, 'haber' => $dcuarto]);
            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_VACACIONES_(2)');
            array_push($arr_beneficios, ['id_cuenta' => $plan_cuentas->cuenta_guardar, 'nombre' => 'VACACIONES', 'debe' => 0, 'haber' => $vacaciones]);
        }

        return $arr_beneficios;
    }

    public function generar_asientos($id, Request $request)
    {

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;

        //$fecha_actual = date('Y-m-d H:i:s');
        $fecha_actual = $request->fecha_asiento1;
        $id_empresa   = $request->session()->get('id_empresa');
        $cuentas_rol  = RolAsiento::find($id);
        $sucursal     = $cuentas_rol->obtiene_sucursal;
        //dd($sucursal);

        $verifica_cierre = LogAsiento::verificar_cierre_anio($fecha_actual);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }


        if ($cuentas_rol->id_asiento == null) {
            //dd($cuentas_rol);
            $txt_mes  = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
            $text     = 'Total Roles de Pago' . ':' . ' ' . 'Id_Empresa' . ':' . $id_empresa . ' ' . 'Año' . ':' . $cuentas_rol->anio . ' ' . 'Mes' . ':' . $txt_mes[$cuentas_rol->mes];

            $cuentas = $cuentas_rol->cuentas();
            $debe    = $cuentas_rol->cuentas()->sum('debe');
            $haber   = $cuentas_rol->cuentas()->sum('haber');

            if ($debe != $haber) {
                return ['respuesta' => "error", 'msj' => "No se puede generar, se encuentra descuadrado"];
            }

            $input_cabecera = [
                'fecha_asiento'   => $fecha_actual,
                'id_empresa'      => $id_empresa,
                'observacion'     => $text,
                'valor'           => $debe,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'ASIENTO DE ROLES',
                'sucursal'        => $sucursal->codigo_sucursal,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $input_rol_asiento = [
                'id_asiento'      => $id_asiento_cabecera,
                'fecha_asiento'   => $fecha_actual,
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
            $cuentas_rol->update($input_rol_asiento);

            $cuentas = $cuentas_rol->cuentas;

            foreach ($cuentas as $value) {
                if ($value->debe > 0 or $value->haber > 0) {
                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $value->id_plan_cuentas,
                        'descripcion'         => $value->descripcion,
                        'fecha'               => $fecha_actual,
                        'debe'                => $value->debe,
                        'haber'               => $value->haber,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }
            }

            return ['respuesta' => "success", 'id_rol_asiento' => $cuentas_rol->id];
        } else {

            return ['respuesta' => "error", 'msj' => "Ya se encuentran Generados los Asientos"];
        }
    }

    public function pago_de_roles(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->tipo_pago);

        $roles = $request->roles;

        if (!is_null($roles)) {
            if (count($roles) < 1) {
                return ['respuesta' => 'error', 'msj' => 'SELECCIONE AL MENOS UN ROL PARA GENERAR', 'titulos' => 'Error'];
            }
        }

        $caja_banco = Ct_Caja_Banco::where('id_empresa', $id_empresa)->where('cuenta_mayor', $request->cuenta_saliente)->where('estado', 1)->first();
        if (is_null($caja_banco)) {
            return ['respuesta' => 'error', 'msj' => 'CAJA BANCO SIN PLAN DE CUENTAS', 'titulos' => 'Error'];
        }

        $cuentas_rol  = RolAsiento::find($request->rol_asientos);
        $sucursal     = $cuentas_rol->obtiene_sucursal;

        $verifica_cierre = LogAsiento::verificar_cierre_anio($request->fecha_creacion);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }

        DB::beginTransaction();
        try {

            $sum_pagar = 0;
            foreach ($roles as $rol) {

                $ct_rol  = Ct_Rol_Pagos::find($rol);
                $detalle = $ct_rol->detalle->first();
                if (!is_null($ct_rol)) {
                    if ($ct_rol->id_asiento_pago == null) {
                        if (!is_null($detalle)) {
                            $sum_pagar   += round($detalle->neto_recibido, 4);
                        }
                    }
                }
            }
            $sum_pagar = round($sum_pagar, 2);

            //dd($sum_pagar);
            $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            $ms = intval($cuentas_rol->mes) - 1;
            //$concepto  = 'PAGOS DE SUELDOS DEL PERIODO AÑO: '.$cuentas_rol->anio.' Mes: '.$meses[$ms].' Por la Cantidad de:' . $sum_pagar;
            $concepto  = $request->concepto1;
            //$concepto2 = 'SUELDOS Año: ' . $cuentas_rol->anio . ' Mes: ' . $meses[$ms] . 'Por la Cantidad de :' . $sum_pagar;
            $concepto2 = $request->beneficiario1;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request->fecha_creacion,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'PAGO ROLES EMPLEADOS',
                'sucursal'        => $sucursal->codigo_sucursal,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);


            foreach ($roles as $rol) {
                $ct_rol  = Ct_Rol_Pagos::find($rol);

                if ($ct_rol->id_asiento_pago == null) {

                    $arr_valor = [
                        'id_tipo_pago'          => $request->tipo_pago,
                        'numero_cuenta'         => $request->numero_cuenta,
                        'banco'                 => $request->banco,
                        'cuenta_saliente'       => $request->cuenta_saliente,
                        'num_cheque'            => $request->numero_cheque,
                        'fecha_cheque'          => $request->fecha_cheque,
                        'id_asiento_pago'       => $id_asiento_cabecera,
                    ];

                    $ct_rol->update($arr_valor);
                }
            }

            if ($sum_pagar > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.04.04.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_SUELDOS_SALARIOS_PAGAR');

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request->fecha_creacion,
                    'debe'                => $sum_pagar,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                $plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->cuenta_saliente)->orwhere('plan', $request->cuenta_saliente)->first();
                $plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request->cuenta_saliente,
                    'descripcion'         => $plan_cuentas2->nombre,
                    'fecha'               => $request->fecha_creacion,
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                if ($ct_tipo_pago->tipo == 'ACREDITACION') { //DEBITO

                    $nota_debito         = [
                        'concepto'        => $concepto,
                        'fecha'           => $request->fecha_creacion,
                        'valor'           => $sum_pagar,
                        'empresa'         => $id_empresa,
                        'tipo'            => "BAN-ND",
                        'id_asiento'      => $id_asiento_cabecera,
                        'id_banco'        => $caja_banco->id,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                        'modulo'          => 'PAGO DE SUELDOS',
                    ];
                    //$id_nota = 0;
                    $id_nota = Nota_Debito::insertGetId($nota_debito);

                    $nota_deb_detalle = [
                        'id_nota_debito'  => $id_nota,
                        'codigo'          => $plan_cuentas->cuenta_guardar,
                        'cuenta'          => $plan_cuentas->nombre_mostrar,
                        'debe'            => $sum_pagar,
                        'haber'           => number_format(0, 2),
                        'valor_base'      => $sum_pagar, //number_format($valor['debe'], 2),
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                    ];
                    Nota_Debito_Detalle::create($nota_deb_detalle);
                } else { //COMPROBANTE DE EGRESO
                    $numero_factura = LogAsiento::getSecuencia(2);

                    $input_comprobante = [
                        'descripcion'     => $concepto,
                        'estado'          => '1',
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_secuencia'    => 'null',
                        'nota'            => $concepto,
                        'fecha_comprobante' => $request->fecha_creacion,
                        'beneficiario'    => $concepto2,
                        'check'           => 0,
                        'girado'          => $concepto2,
                        'id_caja_banco'   => $caja_banco->id,
                        'nro_cheque'      => $request->numero_cheque,
                        'valor'           => $sum_pagar,
                        'fecha_cheque'    => $request->fecha_cheque,
                        'secuencia'       => $numero_factura,
                        'id_empresa'      => $id_empresa,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'modulo'          => 'PAGO DE SUELDOS',
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                    Ct_Detalle_Comprobante_Egreso_Varios::create([
                        'id_comprobante_varios'          => $id_comprobante,
                        'codigo'                         => $plan_cuentas->cuenta_guardar,
                        'cuenta'                         => $plan_cuentas->nombre_mostrar,
                        'descripcion'                    => $concepto,
                        'debe'                           => $sum_pagar,
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $id_usuario,
                        'id_usuariomod'                  => $id_usuario,
                    ]);
                }
            }


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function aportes_patronales(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        /*if($id_usuario == '0922290697')
        {
            dd($request->all());
        }*/
        

        $rol_asientos_aporte  = $request->rol_asientos_aporte;
        $total_aporte         = round($request->total_aporte, 2);
        $p_patronal           = round($request->p_patronal, 2);
        $p_secap              = round($request->p_secap, 2);
        $aporte_patronal      = round($request->aporte_patronal, 2);
        $aporte_secap         = round($request->aporte_secap, 2);
        $fecha_asiento_aporte = $request->fecha_asiento_aporte;

        $cuentas_rol  = RolAsiento::find($rol_asientos_aporte);
        $sucursal     = $cuentas_rol->obtiene_sucursal;

        $verifica_cierre = LogAsiento::verificar_cierre_anio($fecha_asiento_aporte);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }


        if ($total_aporte <= 0) {
            return ['respuesta' => 'error', 'msj' => 'SIN VALOR A APORTAR', 'titulos' => 'Error'];
        }

        if ($p_patronal <= 0) {
            return ['respuesta' => 'error', 'msj' => 'CONFIGURE EL PORCENTAJE DE APORTACION PATRONAL', 'titulos' => 'Error'];
        }

        if ($p_secap <= 0) {
            return ['respuesta' => 'error', 'msj' => 'CONFIGURE EL PORCENTAJE DE APORTACION SECAP', 'titulos' => 'Error'];
        }

        if (is_null($cuentas_rol)) {
            return ['respuesta' => 'error', 'msj' => 'PERDIODO NO DEFINIDO', 'titulos' => 'Error'];
        }

        $sum_pagar = $aporte_patronal + $aporte_secap;



        DB::beginTransaction();
        try {

            $rubro_apatronal = RolAsientoRubros::where('id_rol_asiento', $rol_asientos_aporte)->where('descripcion', 'APORTE PATRONAL')->first();
            if (!is_null($rubro_apatronal)) {

                $arr_patronal = [
                    'id_rol_asiento'        => $rol_asientos_aporte,
                    'descripcion'           => 'APORTE PATRONAL',
                    'valor'                => $aporte_patronal,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,

                ];
                //dd($arr_patronal,$nuevo_patronal);

                $rubro_apatronal->update($arr_patronal);
            } else {
                $arr_patronal = [
                    'id_rol_asiento'        => $rol_asientos_aporte,
                    'descripcion'           => 'APORTE PATRONAL',
                    'valor'                => $aporte_patronal,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,

                ];
                //dd($arr_patronal,$nuevo_patronal);

                RolAsientoRubros::create($arr_patronal);
            }

            $rubro_asecap = RolAsientoRubros::where('id_rol_asiento', $rol_asientos_aporte)->where('descripcion', 'APORTE SECAP')->first();
            if (!is_null($rubro_asecap)) {
                $arr_secap = [
                    'id_rol_asiento'        => $rol_asientos_aporte,
                    'descripcion'           => 'APORTE SECAP',
                    'valor'                => $aporte_secap,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,

                ];

                $rubro_asecap->update($arr_secap);
            } else {

                $arr_secap = [
                    'id_rol_asiento'        => $rol_asientos_aporte,
                    'descripcion'           => 'APORTE SECAP',
                    'valor'                => $aporte_secap,
                    'id_usuariocrea'        => $id_usuario,
                    'id_usuariomod'         => $id_usuario,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,

                ];

                RolAsientoRubros::create($arr_secap);
            }


            $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            $ms = intval($cuentas_rol->mes) - 1;
            $concepto  = 'APORTES PATRONALES Y DEL SECAP: ' . $cuentas_rol->anio . ' Mes: ' . $meses[$ms] . ' Por la Cantidad de:' . $sum_pagar;
            $concepto2 = 'PATRONAL Y SECAP Año: ' . $cuentas_rol->anio . ' Mes: ' . $meses[$ms] . 'Por la Cantidad de :' . $sum_pagar;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $fecha_asiento_aporte,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'APORTE PATRONAL Y SECAP',
                'sucursal'        => $sucursal->codigo_sucursal,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $cuentas_rol->update([
                'id_asiento_aporte'    => $id_asiento_cabecera,
                'fecha_asiento_aporte' => $fecha_asiento_aporte,
                'aporte_patronal'      => $aporte_patronal,
                'aporte_secap'         => $aporte_secap,
                'total_aporte'         => $total_aporte,
                'p_patronal'           => $p_patronal,
                'p_secap'              => $p_secap,

            ]);

            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(2)');
            if (is_null($plan_cuentas)) {
                return ['respuesta' => 'error', 'msj' => 'cuenta APORTE_PATRONAL_11.15%_(2) no se encuentra configurada', 'titulos' => 'Error'];
            }

            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(5)');
            if (is_null($plan_cuentas)) {
                return ['respuesta' => 'error', 'msj' => 'cuenta APORTE_PATRONAL_11.15%_(5) no se encuentra configurada', 'titulos' => 'Error'];
            }

            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(2)');
            if (is_null($plan_cuentas)) {
                return ['respuesta' => 'error', 'msj' => 'cuenta APORTE_SECAP_1%_(2) no se encuentra configurada', 'titulos' => 'Error'];
            }

            $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(5)');
            if (is_null($plan_cuentas)) {
                return ['respuesta' => 'error', 'msj' => 'cuenta APORTE_SECAP_1%_(5) no se encuentra configurada', 'titulos' => 'Error'];
            }

            if ($aporte_patronal > 0) {


                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(2)');

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_asiento_aporte,
                    'debe'                => 0,
                    'haber'               => $aporte_patronal,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(5)');

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_asiento_aporte,
                    'debe'                => $aporte_patronal,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            if ($aporte_secap > 0) {



                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(2)');

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_asiento_aporte,
                    'debe'                => 0,
                    'haber'               => $aporte_secap,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(5)');

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $fecha_asiento_aporte,
                    'debe'                => $aporte_secap,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function pago_aportes_patronales(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_usuario = Auth::user()->id;
        $idusuario  = $id_usuario;
        $id_empresa = $request->session()->get('id_empresa');

        $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->id_tipo_pago_planilla);


        $caja_banco = Ct_Caja_Banco::where('id_empresa', $id_empresa)->where('cuenta_mayor', $request->id_cuenta_planilla)->where('estado', 1)->first();
        if (is_null($caja_banco)) {
            return ['respuesta' => 'error', 'msj' => 'CAJA BANCO SIN PLAN DE CUENTAS', 'titulos' => 'Error'];
        }

        $cuentas_rol  = RolAsiento::find($request->rol_planillas);
        $sucursal     = $cuentas_rol->obtiene_sucursal;

        if ($cuentas_rol->id_asiento_planilla != null) {
            return ['respuesta' => 'error', 'msj' => 'YA SE GENERO EL ASIENTO PARA EL PAGO DE LA PLANILLA', 'titulos' => 'Error'];
        }

        $cuentas_iess2 = $cuentas_rol->cuentas->where('doc_pendiente', 'IESS2');

        $sum_pagar = $request->total_planilla;
        if ($sum_pagar <= 0) {
            return ['respuesta' => 'error', 'msj' => 'SIN VALOR A PAGAR', 'titulos' => 'Error'];
        }

        DB::beginTransaction();
        try {

            //dd($sum_pagar);
            $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            $ms = intval($cuentas_rol->mes) - 1;
            //$concepto  = 'PAGOS DE PLANILLAS DEL PERIODO AÑO: '.$cuentas_rol->anio.' Mes: '.$meses[$ms].' Por la Cantidad de:' . $sum_pagar;
            $concepto = $request->concepto2;
            //$concepto2 = 'PLANILLAS Año: ' . $cuentas_rol->anio . ' Mes: ' . $meses[$ms] . 'Por la Cantidad de :' . $sum_pagar;
            $concepto2 = $request->beneficiario2;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request->fecha_planilla,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'PAGO DE APORTE PATRONAL Y SECAP',
                'sucursal'        => $sucursal->codigo_sucursal,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $arr_valor = [
                'id_tipo_pago_planilla'    => $request->id_tipo_pago_planilla,
                'numero_cuenta_planilla'   => $request->numero_cuenta_planilla,
                'id_banco_planilla'        => $request->id_banco_planilla,
                'id_cuenta_planilla'       => $request->id_cuenta_planilla,
                'numero_cheque_planilla'   => $request->numero_cheque_planilla,
                'fecha_cheque_planilla'    => $request->fecha_cheque_planilla,
                'id_asiento_planilla'      => $id_asiento_cabecera,
                'fecha_planilla'           => $request->fecha_planilla,
            ];

            $cuentas_rol->update($arr_valor);

            foreach ($cuentas_iess2 as $cuentas) {

                if ($cuentas->haber > 0) {

                    Ct_Asientos_Detalle::create([

                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_plan_cuenta'      => $cuentas->id_plan_cuentas,
                        'descripcion'         => $cuentas->descripcion,
                        'fecha'               => $request->fecha_planilla,
                        'debe'                => $cuentas->haber,
                        'haber'               => 0,
                        'id_usuariocrea'      => $idusuario,
                        'id_usuariomod'       => $idusuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,

                    ]);
                }
            }

            if ($cuentas_rol->aporte_patronal > 0) {
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(2)');

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request->fecha_planilla,
                    'debe'                => $cuentas_rol->aporte_patronal,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }


            if ($cuentas_rol->aporte_secap > 0) {
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(2)');

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request->fecha_planilla,
                    'debe'                => $cuentas_rol->aporte_secap,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            $plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->id_cuenta_planilla)->orwhere('plan', $request->id_cuenta_planilla)->first();
            $plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);

            if ($sum_pagar > 0) {

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request->id_cuenta_planilla,
                    'descripcion'         => $plan_cuentas2->nombre,
                    'fecha'               => $request->fecha_planilla,
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            if ($ct_tipo_pago->tipo == 'ACREDITACION') { //DEBITO

                $nota_debito         = [
                    'concepto'        => $concepto,
                    'fecha'           => $request->fecha_planilla,
                    'valor'           => $sum_pagar,
                    'empresa'         => $id_empresa,
                    'tipo'            => "BAN-ND",
                    'id_asiento'      => $id_asiento_cabecera,
                    'id_banco'        => $caja_banco->id,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'modulo'          => 'PAGO DE PLANILLAS',
                    //'sucursal'        => $sucursal->codigo_sucursal,
                ];
                //$id_nota = 0;
                $id_nota = Nota_Debito::insertGetId($nota_debito);

                $nota_deb_detalle = [
                    'id_nota_debito'  => $id_nota,
                    'codigo'          => $request->id_cuenta_planilla,
                    'cuenta'          => $plan_cuentas2->nombre,
                    'debe'            => $sum_pagar,
                    'haber'           => number_format(0, 2),
                    'valor_base'      => $sum_pagar, //number_format($valor['debe'], 2),
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ];
                Nota_Debito_Detalle::create($nota_deb_detalle);
            } else { //COMPROBANTE DE EGRESO
                $numero_factura = LogAsiento::getSecuencia(2);

                $input_comprobante = [
                    'descripcion'     => $concepto,
                    'estado'          => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_secuencia'    => 'null',
                    'nota'            => $concepto,
                    'fecha_comprobante' => $request->fecha_planilla,
                    'beneficiario'    => $concepto2,
                    'check'           => 0,
                    'girado'          => $concepto2,
                    'id_caja_banco'   => $caja_banco->id,
                    'nro_cheque'      => $request->numero_cheque_planilla,
                    'valor'           => $sum_pagar,
                    'fecha_cheque'    => $request->fecha_cheque_planilla,
                    'secuencia'       => $numero_factura,
                    'id_empresa'      => $id_empresa,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'modulo'          => 'PAGO DE PLANILLAS',
                    //'sucursal'        => $sucursal->codigo_sucursal,

                ];
                $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                Ct_Detalle_Comprobante_Egreso_Varios::create([
                    'id_comprobante_varios'          => $id_comprobante,
                    'codigo'                         => $request->id_cuenta_planilla,
                    'cuenta'                         => $plan_cuentas2->nombre,
                    'descripcion'                    => $concepto,
                    'debe'                           => $sum_pagar,
                    'id_secuencia'                   => $numero_factura,
                    'estado'                         => '1',
                    'ip_creacion'                    => $ip_cliente,
                    'ip_modificacion'                => $ip_cliente,
                    'id_usuariocrea'                 => $id_usuario,
                    'id_usuariomod'                  => $id_usuario,
                ]);
            }


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function asientos_beneficios_sociales(Request $request)
    {

        DB::beginTransaction();
        try {

            $ip_cliente     = $_SERVER["REMOTE_ADDR"];
            $idusuario      = Auth::user()->id;
            $fecha_actual = $request->fecha_beneficio;
            $id_empresa   = $request->session()->get('id_empresa');
            $cuentas_rol  = RolAsiento::find($request->rol_planillas);
            $sucursal     = $cuentas_rol->obtiene_sucursal;
            $id_sucursal  = $sucursal->id;

            $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
                ->join("ct_detalle_rol as ct_r", "ct_rol_pagos.id", "ct_r.id_rol")
                ->join('ct_nomina as nom', 'nom.id', 'ct_rol_pagos.id_nomina')
                ->join('ct_sucursales as scrsal', 'scrsal.id', 'nom.id_sucursal')
                ->where('ct_rol_pagos.anio', $cuentas_rol->anio)
                ->where('ct_rol_pagos.mes', $cuentas_rol->mes)
                ->where('ct_rol_pagos.id_empresa', $cuentas_rol->id_empresa)
                //->select("ct_r.*")
                ->select('nom.id_sucursal as id_sucursal', 'ct_r.decimo_tercero_acumulado', 'ct_r.decimo_cuarto_acumulado', 'ct_r.sueldo_mensual', 'ct_rol_pagos.*', 'ct_r.sueldo_mensual', 'ct_r.porcentaje_iess', 'ct_r.base_iess', 'ct_r.valor_vacacion');
            if (!is_null($id_sucursal)) {
                $roles = $roles->where('id_sucursal', $id_sucursal);
            }
            $roles = $roles->get();
            //dd($roles);


            if ($cuentas_rol->id_asiento_beneficios == null) {
                //dd($cuentas_rol);
                $txt_mes  = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
                $text     = 'Total Beneficios' . ':' . ' ' . 'Id_Empresa' . ':' . $id_empresa . ' ' . 'Año' . ':' . $cuentas_rol->anio . ' ' . 'Mes' . ':' . $cuentas_rol->mes;
                $dtercero = 0; $dcuarto = 0;$smensual = 0; $vacaciones = 0;
                foreach ($roles as $rol) {
                    $dtercero += $rol->decimo_tercero_acumulado;
                    $dcuarto  += $rol->decimo_cuarto_acumulado;
                    $vacaciones += $rol->valor_vacacion;
                    //$smensual += $rol->sueldo_mensual; 
                    $smensual += $rol->base_iess;
                }
                ////////QUE PASA CON LOS QUE TIENEN MAS DE 15 DIAS 
                /*$vacaciones = $smensual / 24 ;
                $vacaiones  = round($vacaciones, 2); */
                $arr_beneficios = $this->beneficios($dtercero, $dcuarto, $vacaciones);
                $debe = $dtercero + $dcuarto + $vacaciones;
                $input_cabecera = [
                    'fecha_asiento'   => $fecha_actual,
                    'id_empresa'      => $id_empresa,
                    'observacion'     => $text,
                    'valor'           => $debe,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'modulo'          => 'PAGO DE BENEFICIOS SOCIALES DECIMO 3er Y 4to',
                    'sucursal'        => $sucursal->codigo_sucursal,
                ];
                $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);
                $input_rol_asiento = [
                    'id_asiento_beneficios'      => $id_asiento_cabecera,
                    'fecha_beneficios'   => $fecha_actual,
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                ];
                $cuentas_rol->update($input_rol_asiento);
                $cuentas = $cuentas_rol->cuentas; //dd($arr_beneficios);
                foreach ($arr_beneficios as $value) {
                    if (Auth::user()->id == '0928572205') {
                        // dd($value['debe']);
                    }
                    if (($value['debe'] + $value['haber']) > 0) {
                        Ct_Asientos_Detalle::create([

                            'id_asiento_cabecera' => $id_asiento_cabecera,
                            'id_plan_cuenta'      => $value['id_cuenta'],
                            'descripcion'         => $value['nombre'],
                            'fecha'               => $fecha_actual,
                            'debe'                => $value['debe'],
                            'haber'               => $value['haber'],
                            'id_usuariocrea'      => $idusuario,
                            'id_usuariomod'       => $idusuario,
                            'ip_creacion'         => $ip_cliente,
                            'ip_modificacion'     => $ip_cliente,

                        ]);
                    }
                }
                DB::commit();
                //return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
                return ['msj' => "success", 'id_rol_asiento' => $cuentas_rol->id];
            } else {
                return ['msj' => "error", 'mensaje' => "Ya se encuentran Generados los Asientos"];
            }
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function ct_rh_acumulados($anio, $sucursal, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        if (is_null($empresa)) {
            return "No encuentra empresa";
        }

        $nominas = Ct_Nomina::where('id_empresa', $empresa->id)->where('id_sucursal', $sucursal)->get();
        foreach ($nominas as $nomina) {

            $acumulado = Ct_Rh_Acumulados::where('id_empresa', $empresa->id)
                ->where('anio', $anio)
                ->where('tipo', 'TERCER')
                ->where('estado', 1)
                ->where('id_nomina', $nomina->id)
                ->where('id_sucursal', $sucursal)
                ->first();

            if (is_null($acumulado)) {
                Ct_Rh_Acumulados::create([
                    'id_empresa'        => $nomina->id_empresa,
                    'id_nomina'         => $nomina->id,
                    'anio'              => $anio,
                    'tipo'              => 'TERCER',
                    'inicio'            => ($anio - 1) . '12',
                    'fin'               => $anio . '11',
                    'valor'             => '0',
                    'estado'            => 1,
                    'id_sucursal'       => $sucursal,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }
        }

        $this->ct_rh_acumulados_update($anio, $sucursal, $request);
    }

    public function ct_rh_acumulados_update($anio, $sucursal, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        if (is_null($empresa)) {
            return "No encuentra empresa";
        }

        $acumulados = Ct_Rh_Acumulados::where('estado', 1)->where('anio', $anio)->where('tipo', 'TERCER')->where('id_sucursal', $sucursal)->get();

        foreach ($acumulados as $acumulado) {

            $anio_ini = substr($acumulado->inicio, 0, 4);
            $mes_ini = substr($acumulado->inicio, 4, 2);
            $anio_fin = substr($acumulado->fin, 0, 4);
            $mes_fin = substr($acumulado->fin, 4, 2);

            // dd($anio_ini,$mes_inicio,$anio_fin,$mes_fin);
            $rol1 = DB::table('ct_rol_pagos as r')
                ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
                ->where('r.id_empresa', $empresa->id)
                ->where('r.anio', $anio_ini)
                ->where('r.mes', $mes_ini)
                ->where('r.estado', 1)
                ->where('r.id_nomina', $acumulado->id_nomina)
                ->select('r.id as rid', 'd.*', 'd.decimo_tercero_acumulado')
                ->first();


            $roles = DB::table('ct_rol_pagos as r')
                ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
                ->where('r.id_empresa', $empresa->id)
                ->where('r.anio', $anio_fin)
                ->where('r.mes', '<=', $mes_fin)
                ->where('r.estado', 1)
                ->where('r.id_nomina', $acumulado->id_nomina)
                ->select('r.id as rid', 'd.*', 'd.decimo_tercero_acumulado')
                ->get();


            $dlaborados = 0;
            $d_tercer_acumulado = 0;
            if (!is_null($rol1)) {


                $detalle    =   Ct_Detalle_Rol::find($rol1->id);
                $dlaborados += $detalle->dias_laborados;
                $d_tercer_acumulado  =  $rol1->decimo_tercero_acumulado;
            }



            foreach ($roles as $rol) {

                $detalle    =   Ct_Detalle_Rol::find($rol->id);
                $dlaborados += $detalle->dias_laborados;

                $d_tercer_acumulado  +=  $rol->decimo_tercero_acumulado;;
            }

            if ($acumulado->dias_subrogacion != null) {
                $dlaborados += $acumulado->dias_subrogacion;
            }
            if ($acumulado->valor_subrogacion != null) {
                $d_tercer_acumulado += $acumulado->valor_subrogacion;
            }


            $acumulado->update([
                'valor' => $d_tercer_acumulado,
                'dias_laborados' => $dlaborados,
            ]);
        }


        return "listo";
    }
    public function recalcular_acumulados_decimos($id)
    {
        $acumulado = Ct_Rh_Acumulados::find($id);
        $empresa   =  Empresa::find($acumulado->id_empresa);

        $anio_ini = substr($acumulado->inicio, 0, 4);
        $mes_ini = substr($acumulado->inicio, 4, 2);
        $anio_fin = substr($acumulado->fin, 0, 4);
        $mes_fin = substr($acumulado->fin, 4, 2);

        // dd($anio_ini,$mes_inicio,$anio_fin,$mes_fin);
        $rol1 = DB::table('ct_rol_pagos as r')
            ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
            ->where('r.id_empresa', $empresa->id)
            ->where('r.anio', $anio_ini)
            ->where('r.mes', $mes_ini)
            ->where('r.estado', 1)
            ->where('r.id_nomina', $acumulado->id_nomina)
            ->select('r.id as rid', 'd.*', 'd.decimo_tercero_acumulado')
            ->first();


        $roles = DB::table('ct_rol_pagos as r')
            ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
            ->where('r.id_empresa', $empresa->id)
            ->where('r.anio', $anio_fin)
            ->where('r.mes', '<=', $mes_fin)
            ->where('r.estado', 1)
            ->where('r.id_nomina', $acumulado->id_nomina)
            ->select('r.id as rid', 'd.*', 'd.decimo_tercero_acumulado')
            ->get();


        $dlaborados = 0;
        $d_tercer_acumulado = 0;
        if (!is_null($rol1)) {


            $detalle    =   Ct_Detalle_Rol::find($rol1->id);
            $dlaborados += $detalle->dias_laborados;
            $d_tercer_acumulado  =  $rol1->decimo_tercero_acumulado;
        }



        foreach ($roles as $rol) {

            $detalle    =   Ct_Detalle_Rol::find($rol->id);
            $dlaborados += $detalle->dias_laborados;

            $d_tercer_acumulado  +=  $rol->decimo_tercero_acumulado;;
        }

        if ($acumulado->dias_subrogacion != null) {
            $dlaborados += $acumulado->dias_subrogacion;
        }
        if ($acumulado->valor_subrogacion != null) {
            $d_tercer_acumulado += $acumulado->valor_subrogacion;
        }


        $acumulado->update([
            'valor' => $d_tercer_acumulado,
            'dias_laborados' => $dlaborados,
        ]);



        return "listo";
    }

    public function acumula_decimo_tercer(Request $request)
    {

        $anio = $request->anio;
        $empleado = $request->empleado;
        $id_sucursal = $request->sucursal;

        if ($request['excel'] == 0) {

            if ($anio == null) {
                $anio = date('Y');
            }
            $id_empresa     = $request->session()->get('id_empresa');


            $DecimosTeceros = Ct_Rh_Acumulados::where('ct_rh_acumulados.tipo', 'TERCER')
                ->where('ct_rh_acumulados.estado', 1)
                ->where('ct_rh_acumulados.anio', $anio)
                ->where('ct_rh_acumulados.id_empresa', $id_empresa)
                ->join('ct_nomina as n', 'n.id', 'ct_rh_acumulados.id_nomina')
                ->join('users as us', 'us.id', 'n.id_user');;

            if ($empleado != null) {

                $DecimosTeceros = $DecimosTeceros->where('n.nombres', 'like', '%' . $empleado . '%');
            }
            if ($id_sucursal != null) {

                $DecimosTeceros = $DecimosTeceros->where('ct_rh_acumulados.id_sucursal', $id_sucursal);
            }



            $DecimosTeceros = $DecimosTeceros->select('ct_rh_acumulados.*', 'n.id_user', 'n.nombres', 'us.nombre1', 'us.nombre2', 'us.apellido1', 'us.apellido2')->where('ct_rh_acumulados.valor', '<>', '0')->get();
            $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->where('uso_nomina', '1')->get();
            if (count($sucursales) == 1) {
                $id_sucursal = $sucursales->first()->id;
            }
            $lista_banco   = Ct_Bancos::all();

            $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
            $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();
            $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->tipo_pago);

            $min_date = Ct_Cierre_Cabecera::ultima_fecha();

            return view('contable.nuevo_rol_pago.decimo_tercer', ['min_date' => $min_date, 'DecimosTeceros' => $DecimosTeceros, 'sucursales' => $sucursales, 'id_sucursal' => $id_sucursal, 'anio' => $anio, 'empleado' => $empleado, 'lista_banco' => $lista_banco, 'bancos' => $bancos, 'tipo_pago_rol' => $tipo_pago_rol]);
        } else {
            $titulos = array("N°", "CEDULA",  "APELLIDOS", "NOMBRES", "OCUPACION", "GENERO", "DIAS TRABAJADOS", "VALOR DECIMO", "FIRMA O HUELLA");
            //Posiciones en el excel
            $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
            if ($anio == null) {
                $anio = 2023;
            }
            $id_empresa     = $request->session()->get('id_empresa');
            $empresa = Empresa::where('id', $id_empresa)->first();
            $DecimosTeceros = Ct_Rh_Acumulados::where('ct_rh_acumulados.tipo', 'TERCER')
                ->where('ct_rh_acumulados.estado', 1)
                ->where('ct_rh_acumulados.anio', $anio)
                ->where('ct_rh_acumulados.id_empresa', $id_empresa)
                ->join('ct_nomina as n', 'n.id', 'ct_rh_acumulados.id_nomina')
                ->join('users as us', 'us.id', 'n.id_user')
                ->orderBy('us.apellido1', 'asc');
            if ($empleado != null) {
                $DecimosTeceros = $DecimosTeceros->where('n.nombres', 'like', '%' . $empleado . '%');
            }

            $DecimosTeceros = $DecimosTeceros->select('ct_rh_acumulados.*', 'n.id_user', 'n.nombres', 'n.sexo', 'n.cargo', 'us.nombre1', 'us.nombre2', 'us.apellido1', 'us.apellido2')->where('ct_rh_acumulados.valor', '<>', '0')->get();

            Excel::create('DECIMO TERCER SUELDO', function ($excel) use ($titulos, $posicion, $DecimosTeceros, $request, $anio, $empresa) {
                $excel->sheet('DECIMO TERCER SUELDO', function ($sheet) use ($titulos, $posicion, $DecimosTeceros, $request, $anio, $empresa) {
                    $sheet->cells('A1:C2', function ($cell) use ($empresa, $sheet) {
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                        $objDrawing->setCoordinates('B1');
                        $objDrawing->setHeight(145);
                        $objDrawing->setWidth(180);
                        $cell->setAlignment('center');
                        $objDrawing->setWorksheet($sheet);
                    });
                    $sheet->mergeCells('D1:I1');
                    $sheet->cell('D1', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('INFORME INDIVIDUAL SOBRE EL PAGO DE DÉCIMO TERCERA REMUNERACIÓN');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('D2:I2');
                    $sheet->cell('D2', function ($cell) use ($empresa, $sheet) {
                        // manipulate the cel
                        $cell->setValue($empresa->razonsocial);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('D3:I3');
                    $sheet->cell('D3', function ($cell) use ($empresa, $sheet) {
                        // manipulate the cel
                        $cell->setValue('R.U.C:' . $empresa->id);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo = 4; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL
                    /****************TITULOS DEL EXCEL*********************/
                    //crear los titulos en el excel
                    for ($i = 0; $i < count($titulos); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                            $cell->setValue($titulos[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#92CFEF');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    /*****FIN DE TITULOS DEL EXCEL***********/
                    $contador1 = 1;

                    $total_valores = 0;
                    foreach ($DecimosTeceros as $value) {
                        //dd($days);
                        $datos_excel = array();
                        $valor_decimo = round($value->valor, 2);
                        array_push($datos_excel, $contador1, $value->id_user, $value->apellido1 . ' ' . $value->apellido2,  $value->nombre1  . ' ' . $value->nombre2, $value->ct_nomina->cargo, $value->sexo, $value->dias_laborados, $valor_decimo, '');

                        $total_valores += $valor_decimo;

                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                        $contador1++;
                    }




                    $sheet->cell('G' . $comienzo, function ($cell) {
                        $cell->setValue('TOTAL:');
                        $cell->setAlignment('right');
                    });

                    $sheet->cell('H' . $comienzo, function ($cell) use ($total_valores) {
                        $cell->setValue($total_valores);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setAlignment('center');
                    });

                });
                $excel->getActiveSheet()->getStyle('C4:C4')->getAlignment()->setWrapText(true);
            })->export('xlsx');
        }
        //dd($r
    }

    public function detalle_decimo_tercer($id_nomina, $inicio, $fin, Request $request)
    {

        $anio_ini = substr($inicio, 0, 4);
        $mes_ini = substr($inicio, 4, 2);
        $anio_fin = substr($fin, 0, 4);
        $mes_fin = substr($fin, 4, 2);

        $id_empresa     = $request->session()->get('id_empresa');

        $rol1 = DB::table('ct_rol_pagos as r')
            ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
            ->where('r.id_empresa', $id_empresa)
            ->where('r.anio', $anio_ini)
            ->where('r.mes', $mes_ini)
            ->where('r.estado', 1)
            ->where('r.id_nomina', $id_nomina)
            ->select('r.id as idrol', 'r.anio', 'r.mes', 'd.*')
            ->first();

        $roles = DB::table('ct_rol_pagos as r')
            ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
            ->where('r.id_empresa', $id_empresa)
            ->where('r.anio', $anio_fin)
            ->where('r.mes', '<=', $mes_fin)
            ->where('r.estado', 1)
            ->where('r.id_nomina', $id_nomina)
            ->select('r.id as idrol', 'r.anio', 'r.mes', 'd.*')
            ->get();

        $nomina = Ct_Nomina::find($id_nomina);

        return view('contable.nuevo_rol_pago.detalle_decimo_tercer', ['rol1' => $rol1, 'roles' => $roles, 'nomina' => $nomina]);
    }

    public function ct_rh_acumulados_cuarto($anio, $sucursal, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa     = $request->session()->get('id_empresa');

        $empresa = Empresa::find($id_empresa);
        if (is_null($empresa)) {
            return "No encuentra empresa";
        }

        //$nominas = Ct_Nomina::where('id_empresa', $empresa->id)->where('estado','1')->get();
        $nominas = Ct_Nomina::where('id_empresa', $empresa->id)->where('id_sucursal', $sucursal)/*->where('estado', '1')*/->get();
        //dd($nominas);
        foreach ($nominas as $nomina) {

            $acumulado = Ct_Rh_Acumulados::where('id_empresa', $empresa->id)
                ->where('anio', $anio)
                ->where('tipo', 'CUARTO')
                ->where('estado', 1)
                ->where('id_nomina', $nomina->id)
                ->where('id_sucursal', $sucursal)
                ->first();
            //dd($acumulado);
            $periodo =  Ct_Rh_Sucursal_Periodo::where('id_empresa', $empresa->id)->where('id_sucursal', $sucursal)->first();


            if (is_null($acumulado)) {
                Ct_Rh_Acumulados::create([
                    'id_empresa'        => $nomina->id_empresa,
                    'id_nomina'         => $nomina->id,
                    'anio'              => $anio,
                    'tipo'              => 'CUARTO',
                    //'inicio'            => ( $anio - 1 ) . '3',
                    'inicio'            => ($anio - 1) . $periodo->inicio_decimo_cuarto,
                    //'fin'               => $anio . '2',
                    'fin'               => $anio . $periodo->fin_decimo_cuarto,
                    'valor'             => '0',
                    'estado'            => 1,
                    'id_sucursal'       => $sucursal,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }
        }

        $this->ct_rh_acumulados_update_cuarto($anio, $sucursal, $request);

        return "listo";
    }

    public function ct_rh_acumulados_update_cuarto($anio, $sucursal, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        if (is_null($empresa)) {
            return "No encuentra empresa";
        }

        $acumulados = Ct_Rh_Acumulados::where('estado', 1)->where('anio', $anio)->where('tipo', 'CUARTO')->where('id_sucursal', $sucursal)->get();


        foreach ($acumulados as $acumulado) {

            $anio_ini = substr($acumulado->inicio, 0, 4);
            $mes_ini = substr($acumulado->inicio, 4, 2);
            $anio_fin = substr($acumulado->fin, 0, 4);
            $mes_fin = substr($acumulado->fin, 4, 2);

            $roles1 = DB::table('ct_rol_pagos as r')
                ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
                ->join('ct_nomina as nom', 'nom.id', 'r.id_nomina')
                ->where('r.id_empresa', $empresa->id)
                ->where('r.anio', $anio_ini) //2022
                ->where('r.mes', '>=', $mes_ini) //3
                ->where('r.estado', 1)
                ->where('r.id_nomina', $acumulado->id_nomina)
                ->select('r.id as rid', 'd.*', 'nom.decimo_cuarto', 'r.pasante', 'r.jornada')
                ->get();

            $roles2 = DB::table('ct_rol_pagos as r')
                ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
                ->join('ct_nomina as nom', 'nom.id', 'r.id_nomina')
                ->where('r.id_empresa', $empresa->id)
                ->where('r.anio', $anio_fin) //2023
                ->where('r.mes', '<=', $mes_fin) //2
                ->where('r.estado', 1)
                ->where('r.id_nomina', $acumulado->id_nomina)
                ->select('r.id as rid', 'd.*', 'nom.decimo_cuarto', 'r.pasante', 'r.jornada')
                ->get();

            $valor_sueldo_basico = 0;

            $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $empresa->id)
                ->where('tipo', 3)->first();

            $d_cuarto_final = 0;

            $dlaborados = 0;
            if (!is_null($val_sal_basico)) {
                $valor_sueldo_basico =  $val_sal_basico->valor;
            }

            foreach ($roles1 as $rol) {


                $tipo_decimo_cuarto = $rol->decimo_cuarto;

                $pasante = $rol->pasante;
                $jornada = $rol->jornada;
                $dias_laborados = $rol->dias_laborados;

                $dcua = $this->calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada);
                $decimo_cuarto_acumulado = $dcua['decimo_cuarto_acumulado'];
                $d_cuarto_final  +=  $decimo_cuarto_acumulado;
                $detalle    =   Ct_Detalle_Rol::find($rol->id);
                if ($pasante == 0) {
                    $dlaborados += $detalle->dias_laborados;
                }
                $detalle->update(['dcuarto_acumulado_final' => $decimo_cuarto_acumulado]);
            }

            foreach ($roles2 as $rol) {

                $tipo_decimo_cuarto = $rol->decimo_cuarto;
                $pasante = $rol->pasante;
                $jornada = $rol->jornada;
                $dias_laborados = $rol->dias_laborados;

                $dcua = $this->calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada);
                $decimo_cuarto_acumulado = $dcua['decimo_cuarto_acumulado'];
                $d_cuarto_final  +=  $decimo_cuarto_acumulado;
                $detalle    =   Ct_Detalle_Rol::find($rol->id);
                if ($pasante == 0) {
                    $dlaborados += $detalle->dias_laborados;
                }
                $detalle->update(['dcuarto_acumulado_final' => $decimo_cuarto_acumulado]);
            }
            if ($acumulado->dias_subrogacion != null) {
                $dlaborados += $acumulado->dias_subrogacion;
            }
            if ($acumulado->valor_subrogacion != null) {
                $d_cuarto_final += $acumulado->valor_subrogacion;
            }

            $acumulado->update([
                'valor' => round($d_cuarto_final, 2),
                'dias_laborados' => $dlaborados,
                'sueldo_basico' => $valor_sueldo_basico,
            ]);
        }


        return "listo";
    }


    public function acumula_decimo_cuarto(Request $request)
    {

        $anio = $request->anio;
        $empleado = $request->empleado;
        $id_sucursal = $request->sucursal;

        if ($request['excel'] == 0) {

            if ($anio == null) {
                $anio = date('Y');
            }
            $id_empresa     = $request->session()->get('id_empresa');

            //DB::enableQueryLog();
            $DecimosCuarto = Ct_Rh_Acumulados::where('ct_rh_acumulados.tipo', 'CUARTO')
                ->join('ct_nomina as n', 'n.id', 'ct_rh_acumulados.id_nomina')
                //->join('ct_rol_pagos as p','p.id_nomina','n.id')
                //->join('ct_detalle_rol as d','d.id_rol','p.id')
                ->where('ct_rh_acumulados.estado', 1)
                ->where('ct_rh_acumulados.anio', $anio)
                ->where('ct_rh_acumulados.id_empresa', $id_empresa)
                ->join('users as us', 'us.id', 'n.id_user');

            if ($empleado != null) {

                $DecimosCuarto = $DecimosCuarto->where('n.nombres', 'like', '%' . $empleado . '%');
            }

            if ($id_sucursal != null) {

                $DecimosCuarto = $DecimosCuarto->where('ct_rh_acumulados.id_sucursal', $id_sucursal);
            }


            $DecimosCuarto = $DecimosCuarto->select('ct_rh_acumulados.id as id', 'ct_rh_acumulados.*', 'n.id_user', 'n.nombres', 'us.nombre1', 'us.nombre2', 'us.apellido1', 'us.apellido2')->where('ct_rh_acumulados.valor', '<>', '0')->get();
            $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->where('uso_nomina', '1')->get();

            if (count($sucursales) == 1) {
                $id_sucursal = $sucursales->first()->id;
            }



            $lista_banco   = Ct_Bancos::all();

            $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
            $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();
            $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->tipo_pago);
            $min_date = Ct_Cierre_Cabecera::ultima_fecha();

            return view('contable.nuevo_rol_pago.decimo_cuarto', ['min_date' => $min_date, 'lista_banco' => $lista_banco, 'bancos' => $bancos, 'tipo_pago_rol' => $tipo_pago_rol, 'DecimosCuarto' => $DecimosCuarto, 'anio' => $anio, 'empleado' => $empleado, 'sucursales' => $sucursales, 'id_sucursal' => $id_sucursal]);
        } else {
            $titulos = array("N°", "CEDULA",  "APELLIDOS", "NOMBRES", "OCUPACION", "GENERO", "DIAS TRABAJADOS", "RETENCION", "VALOR DECIMO", "FIRMA O HUELLA");
            //Posiciones en el excel
            $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
            if ($anio == null) {
                $anio = 2023;
            }
            $id_empresa     = $request->session()->get('id_empresa');
            $empresa = Empresa::where('id', $id_empresa)->first();
            $DecimosCuarto = Ct_Rh_Acumulados::where('ct_rh_acumulados.tipo', 'CUARTO')
                ->where('ct_rh_acumulados.estado', 1)
                ->where('ct_rh_acumulados.anio', $anio)
                ->where('ct_rh_acumulados.id_empresa', $id_empresa)
                ->join('ct_nomina as n', 'n.id', 'ct_rh_acumulados.id_nomina')
                ->join('users as us', 'us.id', 'n.id_user')
                ->orderBy('us.apellido1', 'asc');
            if ($empleado != null) {
                $DecimosCuarto = $DecimosCuarto->where('n.nombres', 'like', '%' . $empleado . '%');
            }

            $DecimosCuarto = $DecimosCuarto->select('ct_rh_acumulados.*', 'n.id_user', 'n.nombres', 'n.sexo', 'n.cargo', 'us.nombre1', 'us.nombre2', 'us.apellido1', 'us.apellido2')->where('ct_rh_acumulados.valor', '<>', '0')->get();

            Excel::create('DECIMO CUARTO SUELDO', function ($excel) use ($titulos, $posicion, $DecimosCuarto, $request, $anio, $empresa) {
                $excel->sheet('DECIMO CUARTO SUELDO', function ($sheet) use ($titulos, $posicion, $DecimosCuarto, $request, $anio, $empresa) {
                    $sheet->cells('A1:C2', function ($cell) use ($empresa, $sheet) {
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                        $objDrawing->setCoordinates('B1');
                        $objDrawing->setHeight(150);
                        $objDrawing->setWidth(200);
                        $cell->setAlignment('center');
                        $objDrawing->setWorksheet($sheet);
                    });
                    $sheet->mergeCells('D1:J1');
                    $sheet->cell('D1', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('INFORME INDIVIDUAL SOBRE EL PAGO DE DÉCIMOCUARTA REMUNERACIÓN');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('D2:J2');
                    $sheet->cell('D2', function ($cell) use ($empresa, $sheet) {
                        // manipulate the cel
                        $cell->setValue($empresa->razonsocial);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('D3:J3');
                    $sheet->cell('D3', function ($cell) use ($empresa, $sheet) {
                        // manipulate the cel
                        $cell->setValue('R.U.C:' . $empresa->id);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo = 4; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL
                    /****************TITULOS DEL EXCEL*********************/
                    //crear los titulos en el excel
                    for ($i = 0; $i < count($titulos); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                            $cell->setValue($titulos[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#92CFEF');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    /*****FIN DE TITULOS DEL EXCEL***********/
                    $contador1 = 1;
                    foreach ($DecimosCuarto as $value) {
                        //dd($days);
                        $datos_excel = array();
                        array_push($datos_excel, $contador1, $value->id_user, $value->apellido1 . ' ' . $value->apellido2,  $value->nombre1  . ' ' . $value->nombre2, '', $value->sexo, $value->dias_laborados, '', $value->valor, '');
                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                        $contador1++;
                    }
                });
                $excel->getActiveSheet()->getStyle('C4:C4')->getAlignment()->setWrapText(true);
            })->export('xlsx');
        }
        //dd($r



    }

    public function detalle_decimo_cuarto($id_nomina, $inicio, $fin, Request $request)
    {

        $anio_ini = substr($inicio, 0, 4);
        $mes_ini = substr($inicio, 4, 2);
        $anio_fin = substr($fin, 0, 4);
        $mes_fin = substr($fin, 4, 2);

        $id_empresa     = $request->session()->get('id_empresa');


        $roles1 = DB::table('ct_rol_pagos as r')
            ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
            ->where('r.id_empresa', $id_empresa)
            ->where('r.anio', $anio_ini) //2022
            ->where('r.mes', '>=', $mes_ini) //3
            ->where('r.estado', 1)
            ->where('r.id_nomina', $id_nomina)
            ->select('r.id as rid', 'd.*')
            ->get();

        $roles2 = DB::table('ct_rol_pagos as r')
            ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
            ->where('r.id_empresa', $id_empresa)
            ->where('r.anio', $anio_fin) //2023
            ->where('r.mes', '<=', $mes_fin) //2
            ->where('r.estado', 1)
            ->where('r.id_nomina', $id_nomina)
            ->select('r.id as rid', 'd.*')
            ->get();


        $nomina = Ct_Nomina::find($id_nomina);

        return view('contable.nuevo_rol_pago.detalle_decimo_cuarto', ['roles1' => $roles1, 'roles2' => $roles2, 'nomina' => $nomina]);
    }



    /*  public function update_cuarto($anio)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $empresa = Empresa::where('prioridad',1)->first();
        if(is_null($empresa)){
            return "No encuentra empresa";
        }

        $acumulados = Ct_Rh_Acumulados::where('estado',1)->where('anio',$anio)->where('tipo','CUARTO')->get();


        foreach($acumulados as $acumulado){

            $anio_ini= substr($acumulado->inicio, 0,4);
            $mes_ini= substr($acumulado->inicio, 4,2);
            $anio_fin= substr($acumulado->fin, 0,4);
            $mes_fin= substr($acumulado->fin, 4,2);
           
        
    
            $roles2 = DB::table('ct_rol_pagos as r')
                ->join('ct_detalle_rol as d','d.id_rol','r.id')
                ->where('r.id_empresa', $empresa->id)
                ->where('r.anio',$anio_fin) //2023
                ->where('r.mes','<=',$mes_fin) //2
                ->where('r.estado',1)
                ->where('r.id_nomina', $acumulado->id_nomina)
                ->select('r.id as rid','d.*') 
                ->get();

            $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $empresa->id)
                ->where('tipo', 3)->first();

            $sbasico=0;
            

                
            foreach( $roles2 as $rol){
                $sbasico  +=  round($val_sal_basico->valor / 12, 4);  
                $detalle    =   Ct_Detalle_Rol::find($rol->id);
                $detalle->update(['decimo_cuarto_acumulado' => round($val_sal_basico->valor / 12, 4)]);
                
            }
              
            $acumulado->update([
                'valor' => $sbasico,
            ]);

        }
        
        return "listo";

            
    }*/

    public function calcular_otros_anticipos($id_rol)
    {

        $rol = Ct_Rol_Pagos::find($id_rol);

        $id_empleado = $rol->id_user;

        $otros_anticipos = Ct_Rh_Otros_Anticipos::where('id_empl', $id_empleado)->where('id_empresa', $rol->id_empresa)->whereNull('id_ct_rol')->where('estado', '1')->get();

        return view('contable.nuevo_rol_pago.listar_otros_anticipos', ['otros_anticipos' => $otros_anticipos, 'rol' => $rol]);
    }

    public function aplicar_otros_anticipos($id_anticipo, $id_rol)
    {

        $anticipo = Ct_Rh_Otros_Anticipos::find($id_anticipo);
        $rol = Ct_Rol_Pagos::find($id_rol);
        $nomina = $rol->ct_nomina;
        $id_sucursal =  $nomina->id_sucursal;
        $id_empresa = $nomina->id_empresa;
        $nom_funcion = 'aplicar_otros_anticipos';
        $anticipo->update(['id_ct_rol' => $id_rol]);
        CtLogRolNomina::guarda_log_agrega_otro_anticipo_rol('AGREGA ANTICIPO EN ROL PAGO DESDE EL LISTADO', $id_sucursal, $id_empresa, $nom_funcion, $rol->id);
        $this->recalcular_db($id_rol);

        return "ok";
    }

    public function decimo_cuarto($anio, $mes, Request $request)
    {

        $id_empresa     = $request->session()->get('id_empresa');

        if ($id_empresa == null) {
            return "sin empresa";
        }

        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $id_empresa)
            ->where('tipo', 3)->first();

        if ($val_sal_basico == null) {
            return "sin salario basico";
        }

        $valor_sueldo_basico = $val_sal_basico->valor;

        $roles = Ct_Rol_Pagos::where('id_empresa', $id_empresa)
            ->where('estado', 1)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->get();

        //$decimo_cuarto_acumulado = $valor_sueldo_basico / 12; 

        foreach ($roles as $rol) {

            $tipo_decimo_cuarto      = $rol->ct_nomina->decimo_cuarto;
            $tipo_decimo_tercero     = $rol->ct_nomina->decimo_tercero;
            $detalle                 = $rol->detalle->first();

            if (!is_null($detalle)) {
                $base_iess = $detalle->base_iess;
                $pasante   = $rol->pasante;
                $dias_laborados = $detalle->dias_laborados;
                $jornada   = $rol->jornada;
                $fecha_ingreso      = $rol->ct_nomina->fecha_ingreso;
                if ($rol->ct_nomina->fecha_ant != null) {
                    $fecha_ingreso = $rol->ct_nomina->fecha_ant;
                }


                $dter = $this->calculo_decimo_tercer($tipo_decimo_tercero, $base_iess, $pasante);

                $decimo_tercero = $dter['decimo_tercero'];
                $decimo_tercero_acumulado = $dter['decimo_tercero_acumulado'];

                $dcua = $this->calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada);

                $decimo_cuarto = $dcua['decimo_cuarto'];
                $decimo_cuarto_acumulado = $dcua['decimo_cuarto_acumulado'];

                $valor_vacacion = $this->calculo_vacaciones($base_iess, $fecha_ingreso, $pasante);

                $array = [
                    'decimo_cuarto_acumulado' => $decimo_cuarto_acumulado,
                    'decimo_tercero_acumulado' => $decimo_tercero_acumulado,
                    'valor_vacacion' => $valor_vacacion,
                ];
                $detalle->update($array);
            }
        }

        return "ok";
    }

    public function vacaciones($anio, $mes, Request $request)
    {

        $id_empresa     = $request->session()->get('id_empresa');

        if ($id_empresa == null) {
            return "sin empresa";
        }

        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $id_empresa)
            ->where('tipo', 3)->first();

        if ($val_sal_basico == null) {
            return "sin salario basico";
        }

        $valor_sueldo_basico = $val_sal_basico->valor;

        $roles = Ct_Rol_Pagos::where('id_empresa', $id_empresa)
            ->where('estado', 1)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->get();

        //$decimo_cuarto_acumulado = $valor_sueldo_basico / 12; 

        foreach ($roles as $rol) {

            $tipo_decimo_cuarto      = $rol->ct_nomina->decimo_cuarto;
            $tipo_decimo_tercero     = $rol->ct_nomina->decimo_tercero;
            $detalle                 = $rol->detalle->first();

            if (!is_null($detalle)) {
                $base_iess = $detalle->base_iess;
                $pasante   = $rol->pasante;
                $dias_laborados = $detalle->dias_laborados;
                $jornada   = $rol->jornada;
                $fecha_ingreso      = $rol->ct_nomina->fecha_ingreso;
                if ($rol->ct_nomina->fecha_ant != null) {
                    $fecha_ingreso = $rol->ct_nomina->fecha_ant;
                }


                $dter = $this->calculo_decimo_tercer($tipo_decimo_tercero, $base_iess, $pasante);

                $decimo_tercero = $dter['decimo_tercero'];
                $decimo_tercero_acumulado = $dter['decimo_tercero_acumulado'];

                $dcua = $this->calculo_decimo_cuarto($tipo_decimo_cuarto, $valor_sueldo_basico, $pasante, $dias_laborados, $jornada);

                $decimo_cuarto = $dcua['decimo_cuarto'];
                $decimo_cuarto_acumulado = $dcua['decimo_cuarto_acumulado'];

                $valor_vacacion = $this->calculo_vacaciones($base_iess, $fecha_ingreso, $pasante);

                $array = [
                    'decimo_cuarto_acumulado' => $decimo_cuarto_acumulado,
                    'decimo_tercero_acumulado' => $decimo_tercero_acumulado,
                    'valor_vacacion' => $valor_vacacion,
                ];
                $detalle->update($array);
            }
        }

        return "ok";
    }

    public function detalle_beneficios(Request  $request)
    {
        //dd($request->all());

        $id_rol_asiento = $request->id;
        $rol_asiento    = RolAsiento::find($id_rol_asiento);
        $id_sucursal    = $rol_asiento->id_sucursal;
        //dd($rol_asiento);

        $roles = Ct_Rol_Pagos::join('ct_nomina as nom', 'nom.id', 'ct_rol_pagos.id_nomina')
            ->join('ct_sucursales as scrsal', 'scrsal.id', 'nom.id_sucursal')
            ->where('ct_rol_pagos.anio', $rol_asiento->anio)->where('ct_rol_pagos.mes', $rol_asiento->mes)
            ->where('ct_rol_pagos.id_empresa', $rol_asiento->id_empresa)->where('ct_rol_pagos.estado', 1)
            ->select('ct_rol_pagos.*', 'nom.id_sucursal');

        if (!is_null($id_sucursal)) {
            $roles = $roles->where('id_sucursal', $id_sucursal);
        }
        $roles = $roles->get();

        //dd($roles);

        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $rol_asiento->id_empresa)->where('tipo', 3)->first();

        return view('contable.nuevo_rol_pago.detalle_beneficios', ['roles' => $roles, 'val_sal_basico' => $val_sal_basico]);
    }

    public function detalle_beneficios_rrhh(Request  $request)
    {
        //dd($request->all());

        $anio = $request->anio;
        $mes  = $request->mes;
        $id_empresa = $request->session()->get('id_empresa');
        $id_sucursal = $request->sucursal;

        $roles = Ct_Rol_Pagos::join('ct_nomina as nom', 'nom.id', 'ct_rol_pagos.id_nomina')
            ->join('ct_sucursales as scrsal', 'scrsal.id', 'nom.id_sucursal')
            ->where('ct_rol_pagos.anio', $anio)->where('ct_rol_pagos.mes', $mes)
            ->where('ct_rol_pagos.id_empresa', $id_empresa)->where('ct_rol_pagos.estado', 1)
            ->select('ct_rol_pagos.*', 'nom.id_sucursal');

        if (!is_null($id_sucursal)) {
            $roles = $roles->where('id_sucursal', $id_sucursal);
        }

        $roles = $roles->get();

        $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $id_empresa)->where('tipo', 3)->first();

        return view('contable.nuevo_rol_pago.detalle_beneficios', ['roles' => $roles, 'val_sal_basico' => $val_sal_basico]);
    }

    public function actualizar_pasante(Request $request)
    {

        $id = $request->id;
        $valor = $request->value;
        $pasante = 0;
        if ($valor == 'on' or $valor == '1') {
            $pasante = 1;
        }
        //dd($pasante, $request->all());
        $rol = Ct_Rol_Pagos::find($id);
        $rol->update(['pasante' => $pasante]);

        return "ok";
    }

    public function actualizar_jornada(Request $request)
    {

        $id = $request->id;
        $valor = $request->value;


        $rol = Ct_Rol_Pagos::find($id);
        $rol->update(['jornada' => $valor]);

        return "ok";
    }
    public function buscador_index_sucursal(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->first();

        $roles_pago = Ct_Rol_Pagos::where('estado', '1')->orderby('id', 'asc')->paginate(5);

        $empresas = Empresa::all();
        $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->where('uso_nomina', '1')->get();
        //dd($sucursales);

        $cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'plan_cuentas.id', 'pe.id_plan')->where('pe.id_empresa', $id_empresa)->where('pe.estado', '2')->get();

        return view('contable/rol_pago_sucursal/buscar_roles_sucursal_index', ['roles_pago' => $roles_pago, 'empresas' => $empresas, 'id_empresa' => $id_empresa, 'cuentas' => $cuentas, 'empresa' => $empresa, 'sucursales' => $sucursales]);
    }

    public function buscador_roles_sucursal(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        // $id_empresa       = $request->session()->get('id_empresa');
        $sucursal      = $request['sucursal'];
        //$empresa          = $id_empresa;
        $id_anio          = $request['year'];
        $id_mes           = $request['mes'];
        //dd($id_empresa, $id_anio, $id_mes,$id_sucursal);
        $rol_det_consulta = DB::table('ct_rol_pagos as rp')
            ->where('rp.id_empresa', $id_empresa)
            ->where('rp.anio', $id_anio)
            ->where('rp.mes', $id_mes)
            ->where('rp.estado', '1')
            ->join('ct_detalle_rol as drol', 'drol.id_rol', 'rp.id')
            ->join('users as u', 'rp.id_user', 'u.id')
            ->join('ct_nomina as nom', 'nom.id', 'rp.id_nomina')
            ->join('ct_sucursales as scrsal', 'scrsal.id', 'nom.id_sucursal')
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
                'drol.base_iess',
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
                'nom.id_sucursal as id_sucursal',
                'scrsal.nombre_sucursal as nombre_sucursal',
                'u.apellido1'
            )
            ->orderby('u.apellido1', 'asc');
        if (!is_null($sucursal)) {
            $rol_det_consulta = $rol_det_consulta->where('id_sucursal', $sucursal);
        }

        $rol_det_consulta = $rol_det_consulta->get();
        //dd($rol_det_consulta);

        return view('contable.rol_pago_sucursal.resultado_busqueda_roles_sucursal', ['rol_det_consulta' => $rol_det_consulta]);
    }

    public function existe_rol_pago_sucursal(Request $request)
    {
        $id_empresa       = $request->session()->get('id_empresa');
        $id_anio          = $request['year'];
        $id_mes           = $request['mes'];
        $sucursal      = $request['sucursal'];
        $rol_det_consulta = DB::table('ct_rol_pagos')
            ->where('ct_rol_pagos.id_empresa', $id_empresa)
            ->where('ct_rol_pagos.anio', $id_anio)
            ->where('ct_rol_pagos.mes', $id_mes)
            ->join('ct_nomina as nom', 'nom.id', 'ct_rol_pagos.id_nomina')
            ->join('ct_sucursales as scrsal', 'scrsal.id', 'nom.id_sucursal')
            ->where('ct_rol_pagos.estado', '1')->where('nom.id_sucursal', $sucursal)->first();
        if (is_null($rol_det_consulta)) {
            $msj = "no";
            return ['msj' => $msj];
        } else {
            $msj = "si";
            return ['msj' => $msj];
        }
    }

    public function imprimir_new_rol_pago_sucursal($sucursal, $mes, $anio, Request $request)
    {
        //dd($sucursal);
        $id_anio          = $anio;
        $id_mes           = $mes;
        $sucursal         = $sucursal;
        $id_empresa       = $request->session()->get('id_empresa');
        $empresa          = Empresa::where('id', $id_empresa)->first();
        $rol_det_consulta = DB::table('ct_rol_pagos as rp')
            ->where('rp.id_empresa', $id_empresa)
            ->where('rp.anio', $id_anio)
            ->where('rp.mes', $id_mes)
            ->where('rp.estado', '1')
            ->join('ct_detalle_rol as drol', 'drol.id_rol', 'rp.id')
            ->join('users as u', 'rp.id_user', 'u.id')
            ->join('ct_nomina as nom', 'rp.id_nomina', 'nom.id')
            ->join('ct_sucursales as scrsal', 'scrsal.id', 'nom.id_sucursal')
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
                'nom.cargo',
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
                'nom.area',
                'nom.id_sucursal as id_sucursal',
                'scrsal.nombre_sucursal as nombre_sucursal',
                'drol.observacion_anticip_quinc'

            );
        if (!is_null($sucursal)) {
            $rol_det_consulta = $rol_det_consulta->where('id_sucursal', $sucursal);
        }

        $rol_det_consulta = $rol_det_consulta->get();

        $lista_cuota_quirog = DB::table('ct_rh_cuotas_hipotecarios')->get();
        $lista_cuota_hip    = DB::table('ct_rh_cuotas_quirografario')->get();
        //dd($rol_det_consulta);

        $vistaurl = "contable.rol_pago_sucursal.pdf_rol_pago_sucursal";
        $view     = \View::make($vistaurl, compact('rol_det_consulta', 'empresa', 'lista_cuota_quirog', 'lista_cuota_hip'))->render();
        $pdf      = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Comprobante Rol Pago.pdf');
    }
    public function asientos_por_generar_sucursal($sucursal, $anio, $mes, Request $request)
    {

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $id_empresa     = $request->session()->get('id_empresa');
        $anio           = $anio;
        $mes            = $mes;
        $sucursal       = $sucursal;
        $txt_mes        = ['', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
        $roles = [];
        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->where('ct_rol_pagos.anio', $anio)
            ->where('ct_rol_pagos.mes', $mes)
            ->where('ct_rol_pagos.id_empresa', $id_empresa)
            ->get();
        //dd($roles,$anio,$mes,$id_empresa);

        if (count($roles) == 0) {
            //return ['msj' => "error", 'mensaje' => "No existe roles de pago en el Año : {$anio} Mes : {$txt_mes}, verifique la fecha de la creación del asiento"];
        }

        $rol_asiento    = RolAsiento::where('id_empresa', $id_empresa)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->where('id_sucursal', $sucursal)
            ->where('estado', '1')
            ->first();
        //dd($rol_asiento);
        if (!is_null($rol_asiento)) {

            return ['msj' => "ok", 'id_rol_asiento' => $rol_asiento->id];
        } else {


            $input_rol_asiento = [

                'id_empresa'      => $id_empresa,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                //'id_empresa'      => $id_empresa,
                'anio'            => $anio,
                'mes'             => $mes,
                'id_sucursal'     => $sucursal,
                'estado'          =>  1,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_rol_asiento = RolAsiento::insertGetId($input_rol_asiento);

            $rol_acum = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
                ->where('ct_rol_pagos.anio', $anio)
                ->where('ct_rol_pagos.mes', $mes)
                ->where('ct_rol_pagos.id_empresa', $id_empresa)
                ->join('ct_detalle_rol as drol', 'drol.id_rol', 'ct_rol_pagos.id')
                ->join('ct_nomina as nom', 'nom.id', 'ct_rol_pagos.id_nomina')
                ->join('ct_sucursales as scrsal', 'scrsal.id', 'nom.id_sucursal')
                ->where('nom.id_sucursal', $sucursal)
                ->select(
                    'drol.id',
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
                    DB::raw("SUM(drol.parqueo) as parqueo"),
                    DB::raw("SUM(drol.fondo_reserva_acumulado) as fondo_reserva_acumulado"),
                    DB::raw("SUM(drol.extension_conyuge) as extension_conyuge")
                )->first();

            /* PRESTAMOS A EMPLEADOS MAS SALDO INICIAL ACTIVO */
            $val_prestamo = $rol_acum->total_prestamos;
            $val_prestamo += $rol_acum->total_sald_inicial;
            $extension_conyuge = $rol_acum->extension_conyuge;
            //mio
            if ($extension_conyuge > 0) {
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('EXTENSION_DE_CONYUGE');
                //dd($plan_cuentas);
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'EXTENSION DE CONYUGE',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($extension_conyuge, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS2',
                ]);
            }
            if (floatval($val_prestamo) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.03')->first(); 1.01.02.03.02
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'PRESTAMOS A EMPLEADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($val_prestamo, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* ANTICIPO SUELDOS EMPLEADOS ACTIV */
            $anticipo_sueldo = $rol_acum->total_anticipos;
            if (floatval($anticipo_sueldo) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.04')->first();1.01.02.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('OTROS_ANTICIPOS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ANTICIPOS QUINCENA',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($anticipo_sueldo, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* OTROS ANTICIPO */
            $otros_anticipo = $rol_acum->total_otro_anticipo;
            if (floatval($otros_anticipo) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '1.01.02.06.04')->first();1.01.02.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('OTROS_ANTICIPOS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'OTROS ANTICIPOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($otros_anticipo, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    //'doc_pendiente'     => 'E/V',  
                ]);
            }

            /* IMPUESTO A LA RENTA POR PAGAR PASIVO */
            $val_imp_renta = $rol_acum->total_imp_renta;
            if (floatval($val_imp_renta) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.01.11')->first();2.01.04.01.10
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_IMPUESTO_RENTA_PAGAR');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => '302 RET FTE RELACION DEPENDENCIA',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($val_imp_renta, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* APORTE INDIVIDUAL 9.45% PASIVO */
            $aporte_individual = $rol_acum->total_iess;
            if (floatval($aporte_individual) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.01')->first();2.01.04.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_APORTE_INDIVIDUAL_9.4');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'APORTE INDIVIDUAL 9.45 %',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($aporte_individual, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS2',
                    //'editable'          => '2',
                ]);
            }

            /* CON EL IESS PRESTAMOS QUIROGRAFARIO PASIVO */
            $val_prest_quirografario = $rol_acum->total_quot_quirog;
            if (floatval($val_prest_quirografario) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.05')->first();2.01.04.03.05
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_PRESTAMOS_QUIROGRAFARIOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'PRESTAMOS QUIROGRAFARIOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($val_prest_quirografario, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS2',
                ]);
            }

            /* CON EL IESS PRESTAMOS HIPOTECARIOS PASIVO */
            $val_prest_hipotecario = $rol_acum->total_quot_hipot;
            if (floatval($val_prest_hipotecario) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '2.01.07.03.06')->first();2.01.04.03.06
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_PRESTAMOS_HIPOTECARIOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'PRESTAMOS HIPOTECARIOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($val_prest_hipotecario, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS2',
                ]);
            }

            /**********NETO A RECIBIR (CUENTA DESTINO)*******/
            $net_recibir = $rol_acum->total_neto_recibido;
            if (floatval($net_recibir) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.04.04.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_SUELDOS_SALARIOS_PAGAR');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'SUELDOS SALARIOS PAGAR',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($net_recibir, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* MULTAS EMPLEADOS Y FUNCIONARIO INGRESO */
            $val_multa = $rol_acum->total_multa;
            if (floatval($val_multa) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.01')->first();4.1.04.01.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_MULTAS_EMPLEADOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'MULTAS EMPLEADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($val_multa, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* PARQUEO */
            $val_parqueo = $rol_acum->parqueo;
            //$val_otro_egreso = $val_otro_egreso + $total_sum->parqueo;
            if (floatval($val_parqueo) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.04')->first();4.1.04.01.02
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_OTROS_INGRESOS_PARQUEO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'OTROS INGRESOS PARQUEOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($val_parqueo, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* MODIFICADA OTROS INGRESOS */
            $val_otro_egreso = $rol_acum->otro_egres;
            if (floatval($val_otro_egreso) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '4.1.05.04')->first();4.1.04.01.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_OTROS_INGRESOS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'OTROS INGRESOS (INTERESES)',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($val_otro_egreso, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* PAGO DE SUELDOS Y SALARIOS HORAS EXTRAS AL 50 Y AL 100 BONO IMPUTABLE SUELDO GASTO */
            $sueldo_salario = $rol_acum->total_sueldo;
            if (floatval($sueldo_salario) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.01')->first(); //ROLPAGO_SUELDO 5.2.02.01.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROLPAGO_SUELDO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'SUELDOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($sueldo_salario, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS',
                ]);
            }

            /*Suma de Horas extras*/
            $sobre_tiempo_50  = $rol_acum->total_horas_50;
            $sobre_tiempo_100 = $rol_acum->total_horas_100;
            $total_sobre_tiempo = $sobre_tiempo_50 + $sobre_tiempo_100;
            if (floatval($total_sobre_tiempo) > 0) {
                //$plan_cuentas       = Plan_Cuentas::where('id', '5.2.02.01.02')->first();
                //5.2.02.01.02  ROL_PAGOS_HORAS_EXTRAS
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_HORAS_EXTRAS');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'HORAS EXTRAS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($total_sobre_tiempo, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS',
                ]);
            }

            /* FONDO RESERVA MENSUALES GASTO */
            $val_fond_reserv = $rol_acum->total_fond_reserva;
            if (floatval($val_fond_reserv) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.02.03')->first();5.2.02.02.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'FONDOS DE RESERVA MENSUALES',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($val_fond_reserv, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* VACACIONES PAGADAS EN EL ROL */
            $val_vacaciones = $rol_acum->total_vacaciones;
            if (floatval($val_vacaciones) > 0) {
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_VACACIONES_(2)');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'VACACIONES PAGADAS EN ROL',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($val_vacaciones, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* FONDO RESERVA ACUMULADOS GASTO */
            $val_fond_reserv_acum = $rol_acum->fondo_reserva_acumulado;
            if (floatval($val_fond_reserv_acum) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.02.03')->first();5.2.02.02.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'FONDOS DE RESERVA ACUMULADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($val_fond_reserv_acum, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS'
                ]);
            }

            /* ALIMENTACION GASTO */
            $val_alimentacion = $rol_acum->total_alimentacion;
            if (floatval($val_alimentacion) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.01')->first();5.2.02.03.01
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_ALIMENTACION');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ALIMENTACION',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($val_alimentacion, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* BONIFICACION ESPECIAL GASTO */
            $val_bonif = $rol_acum->total_bono;
            if (floatval($val_bonif) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.03')->first();
                // 5.2.02.03.03   ROL_PAGOS_BONIFICACION_ESPECIAL 5.2.02.03.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_BONIFICACION_ESPECIAL');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'BONIFICACION ESPECIAL',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($val_bonif, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* BONO IMPUTABLE GASTO */
            $val_bono_imput = $rol_acum->total_bonoimp;
            if (floatval($val_bono_imput) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.04')->first();5.2.02.03.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_BONIFICACION_IMPUTABLE');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'BONIFICACION INPUTABLE',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($val_bono_imput, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS',
                ]);
            }

            /* TRANSPORTE Movilizacion y Transporte */
            $val_transporte = $rol_acum->total_transporte;
            if (floatval($val_transporte) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.03.06')->first();5.2.02.03.06
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_MOVILIZACION_TRANSPORTE');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'MOVILIZACIONES Y TRANSPORTE',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($val_transporte, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* FONDO RESERVA ACUMULADOS GASTO */
            $val_fond_reserv_acum = $rol_acum->fondo_reserva_acumulado;
            if (floatval($val_fond_reserv_acum) > 0) {
                //2.01.04.03.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_FONDOS_RESERVA_SALE');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'FONDOS DE RESERVA ACUMULADOS',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($val_fond_reserv_acum, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'doc_pendiente'     => 'IESS2',
                ]);
            }

            /* DECIMO TERCERO GASTO */
            $val_decim_terc = $rol_acum->total_decimo_tercero;
            if (floatval($val_decim_terc) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.04')->first();5.2.02.01.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOTERCER_SUELDO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'DECIMO TERCER SUELDO',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($val_decim_terc, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /* DECIMO CUARTO SUELDO GASTO */
            $val_decim_cuart = $rol_acum->total_decimo_cuarto;
            if (floatval($val_decim_cuart) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.05')->first();5.2.02.01.05
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOCUARTO_SUELDO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'DECIMO CUARTO SUELDO',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => round($val_decim_cuart, 2),
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /*  SEGURO PRIVADO DEBEN REALIZAR OTRO ANTICIPO SE BLOQUEA CAMPO EN EL EDIT  */
            /*  FONDO RESERVA COBRAR REALIZAR OTRO ANTICIPO SE BLOQUEA CAMPO EN EL EDIT  */
            /*  EXAMEN DE LABORATORIO REALIZAR OTRO ANTICIPO SE BLOQUEA CAMPO EN EL EDIT */

            /*  MODIFICADO CORRECTO SEGURO ASISTENCIA MEDICA CXC SEGURO CUENTA X COBRAR SALUD GASTO */
            $val_seg_privado = $rol_acum->total_seguro_privado;
            //dd("123", floatval($val_seg_privado));

            if (floatval($val_seg_privado) > 0) {
                //$plan_cuentas = Plan_Cuentas::where('id', '5.2.02.01.05')->first();5.2.02.01.05
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_CUENTAS_POR_COBRAR_SEGURO');
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ROL_PAGOS_CUENTAS_POR_COBRAR_SEGURO',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => round($val_seg_privado, 2),
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }

            /*$plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DEDUCIBLES_HABER');
            if (!is_null($plan_cuentas)) {
                RolAsientoCuentas::create([
                    'id_rol_asiento'    => $id_rol_asiento,
                    'item'              => 'ROL PAGOS DEDUCIBLES HABER',
                    'id_plan_cuentas'   => $plan_cuentas->cuenta_guardar,
                    'descripcion'       => $plan_cuentas->nombre_mostrar,
                    'debe'              => 0,
                    'haber'             => 0,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                    'editable'          => '2',
                ]);
            }*/


            return ['msj' => "ok", 'id_rol_asiento' => $id_rol_asiento];
        }
    }
    public function detalle_asientos_rol_sucursal($id)
    {

        ///COLOCAR EN UN ARCHIVO DE CONFIGURACION DE NOMINA CUANDO ESTES REEELIGHT 2da vez que revisas vago
        /*$aporte_patronal = 11.15;
        $secap           = 1;*/
        $aporte_patronal = 11.1501167220229;
        $aporte_patronal = round($aporte_patronal, 2);
        $secap           = 1.00008572163328;
        $secap = round($secap, 2);

        $cuentas_rol = RolAsiento::find($id);
        $sucursal = $cuentas_rol->obtiene_sucursal;
        $id_sucursal = $sucursal->id;


        $cuentas = $cuentas_rol->cuentas;
        $cuentas_iess = [];$total_aporte = 0;$valor_aporte = 0; $valor_secap = 0; $cuentas_iess2 = [];
        if ($cuentas_rol->id_asiento != null) {
            $cuentas_iess = $cuentas_rol->cuentas->where('doc_pendiente', 'IESS');
            $total_aporte = $cuentas_rol->cuentas->where('doc_pendiente', 'IESS')->sum('debe');
            /*$valor_aporte = $total_aporte * $aporte_patronal /100;
            $valor_aporte = round($valor_aporte,2);
            $valor_secap = $total_aporte * $secap /100;
            $valor_secap = round($valor_secap,2);*/
            $cuentas_iess2 = $cuentas_rol->cuentas->where('doc_pendiente', 'IESS2');
        }
        $lista_banco   = Ct_Bancos::all();

        $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $cuentas_rol->id_empresa)->get();

        $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();

        /*$roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            //->join("ct_detalle_rol as ct_r","ct_rol_pagos.id","ct_r.id_rol")
            ->where('ct_rol_pagos.anio', $cuentas_rol->anio)
            ->where('ct_rol_pagos.mes', $cuentas_rol->mes)
            ->where('ct_rol_pagos.id_empresa', $cuentas_rol->id_empresa)
            ->get();*/

        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->join("ct_detalle_rol as ct_r", "ct_rol_pagos.id", "ct_r.id_rol")
            ->where('ct_rol_pagos.anio', $cuentas_rol->anio)
            ->where('ct_rol_pagos.mes', $cuentas_rol->mes)
            ->where('ct_rol_pagos.id_empresa', $cuentas_rol->id_empresa)
            ->join('ct_nomina as nom', 'ct_rol_pagos.id_nomina', 'nom.id')
            ->join('ct_sucursales as scrsal', 'scrsal.id', 'nom.id_sucursal')
            ->select('ct_r.decimo_tercero_acumulado', 'ct_r.decimo_cuarto_acumulado', 'nom.id_sucursal as id_sucursal', 'ct_r.sueldo_mensual', 'ct_rol_pagos.*', 'ct_r.sueldo_mensual', 'ct_r.porcentaje_iess', 'ct_r.base_iess', 'ct_r.valor_vacacion');
        //->get();
        if (!is_null($id_sucursal)) {
            $roles = $roles->where('id_sucursal', $id_sucursal);
        }

        $roles = $roles->get();
        
        $dtercero = 0; $dcuarto = 0;$smensual = 0; $sueldo_pasante = 0;$vacaciones = 0;
        foreach ($roles as $rol) {
            $dtercero += $rol->decimo_tercero_acumulado;
            $dcuarto  += $rol->decimo_cuarto_acumulado;
            $vacaciones += $rol->valor_vacacion;
            //$smensual += $rol->sueldo_mensual; 
            $smensual += $rol->base_iess;
            $pct_apte = $rol->porcentaje_iess / $rol->base_iess;
            $pct_apte = round($pct_apte * 100, 2);
            if ($pct_apte > 17) {
                $sueldo_pasante += $rol->sueldo_mensual;
            }
        }

        $total_aporte = $total_aporte - $sueldo_pasante;
        $total_aporte = round($total_aporte, 2);
        $valor_aporte = $total_aporte * $aporte_patronal / 100;
        $valor_aporte = round($valor_aporte, 2);
        //dd($total_aporte);
        //$valor_aporte = round($valor_aporte,2);
        $valor_secap = $total_aporte * $secap / 100;
        $valor_secap = round($valor_secap, 2);
        //$valor_secap = round($valor_secap,2);
        ////////QUE PASA CON LOS QUE TIENEN MAS DE 15 DIAS 
        //$vacaciones = $smensual / 24 ;
        //$vacaciones  = round($vacaciones, 2);  



        $arr_beneficios = $this->beneficios($dtercero, $dcuarto, $vacaciones);
        $plan_cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')
            ->where('pe.id_empresa', $cuentas_rol->id_empresa)
            ->where('plan_cuentas.estado', '=', 2)
            ->select('plan_cuentas.*')
            ->get();

        $pago_rubros = RolAsientoPagoRubros::where('id_rol_asiento', $cuentas_rol->id)->where('estado', '1')->get();
        
        return view('contable.nuevo_rol_pago.cuentas', ['sucursal' => $sucursal, 'cuentas_rol' => $cuentas_rol, 'cuentas' => $cuentas, 'lista_banco' => $lista_banco, 'bancos' => $bancos, 'tipo_pago_rol' => $tipo_pago_rol, 'roles' => $roles, 'cuentas_iess' => $cuentas_iess, 'aporte_patronal' => $aporte_patronal, 'secap' => $secap, 'valor_aporte' => $valor_aporte, 'valor_secap' => $valor_secap, 'total_aporte' => $total_aporte, 'cuentas_iess2' => $cuentas_iess2, 'arr_beneficios' => $arr_beneficios, 'sueldo_pasante' => $sueldo_pasante, 'plan_cuentas' => $plan_cuentas, 'pago_rubros' => $pago_rubros]);
    }


    public function actualiza_detalle_cuentas(Request $request)
    {
        //dd($request->all());
        $id_detalle = $request->id;
        $valor = $request->value;
        $edit = $request->edit;
        $debe = 0; $haber = 0;

        $rol_detalle = RolAsientoCuentas::find($id_detalle);
        if (!is_null($rol_detalle)) {
            if ($rol_detalle->editable == '1') {
                $rol_detalle->update(['debe' => $valor]);
            }
            if ($rol_detalle->editable == '2') {
                $rol_detalle->update(['haber' => $valor]);
            }
            $rol_asiento = $rol_detalle->rol_asiento;
            $debe = $rol_asiento->cuentas->sum('debe');
            $haber = $rol_asiento->cuentas->sum('haber');
        }

        return ['debe' => $debe, 'haber' => $haber];
    }
    public function pago_decimo_cuarto(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');

        $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->tipo_pago);

        $DecimosCuarto = $request->DecimosCuarto;
        //dd($request->DecimosCuarto);
        if (!is_null($DecimosCuarto)) {
            if (count($DecimosCuarto) < 1) {
                return ['respuesta' => 'error', 'msj' => 'SELECCIONE AL MENOS UN EMPLEADO PARA GENERAR', 'titulos' => 'Error'];
            }
        }

        $caja_banco = Ct_Caja_Banco::where('id_empresa', $id_empresa)->where('cuenta_mayor', $request->cuenta_saliente)->where('estado', 1)->first();
        if (is_null($caja_banco)) {
            return ['respuesta' => 'error', 'msj' => 'CAJA BANCO SIN PLAN DE CUENTAS', 'titulos' => 'Error'];
        }

        /*$verifica_cierre = LogAsiento::verificar_cierre_anio($request->fecha_creacion);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }*/
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha_creacion']));
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }

        DB::beginTransaction();
        try {
            $sum_pagar = 0;
            $valor_aplicado = 0;
            $valor_por_cruzar = 0;
            $valor_disponible = 0;
            foreach ($DecimosCuarto as $value) {

                $v_decimo_c  = Ct_Rh_Acumulados::find($value);

                if (!is_null($v_decimo_c)) {
                    $valor_por_cruzar = $request->valor_pago[$value];
                    $valor_por_cruzar =  floatval($valor_por_cruzar);
                    $valor_disponible = $v_decimo_c->valor - $v_decimo_c->valor_pago;
                    //dd($valor_por_cruzar, $valor_disponible);
                    $valor_por_cruzar = round($valor_por_cruzar, 2);
                    $valor_disponible = round($valor_disponible, 2);
                    if ($valor_por_cruzar > $valor_disponible) {
                        return ['respuesta' => 'error', 'msj' => 'EL VALOR A PAGAR ES MAYOR AL SALDO DISPONIBLE ' . $valor_por_cruzar . ' EL valor disponible es' . $valor_disponible, 'titulos' => 'ERROR!'];
                    } else {
                        $valor_aplicado = $valor_por_cruzar;
                        $sum_pagar   += $valor_aplicado;
                    }
                }
            }
            //dd($sum_pagar);

            //dd($sum_pagar);
            //$meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            //$ms = intval($DecimosCuarto->mes) - 1;
            //$concepto  = 'PAGOS DE SUELDOS DEL PERIODO AÑO: '.$cuentas_rol->anio.' Mes: '.$meses[$ms].' Por la Cantidad de:' . $sum_pagar;
            $concepto  = $request->concepto1;
            $concepto2 = $request->concepto2;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request->fecha_creacion,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'PAGO DECIMO CUARTO EMPLEADOS',
                //'sucursal'        => '',
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);


            if ($sum_pagar > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.07.04.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('DECIMO CUARTO SUELDO');

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request->fecha_creacion,
                    'debe'                => $sum_pagar,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                $plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->cuenta_saliente)->orwhere('plan', $request->cuenta_saliente)->first();
                $plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request->cuenta_saliente,
                    'descripcion'         => $plan_cuentas2->nombre,
                    'fecha'               => $request->fecha_creacion,
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                if ($ct_tipo_pago->tipo == 'ACREDITACION') { //DEBITO

                    $nota_debito         = [
                        'concepto'        => $concepto,
                        'fecha'           => $request->fecha_creacion,
                        'valor'           => $sum_pagar,
                        'empresa'         => $id_empresa,
                        'tipo'            => "BAN-ND",
                        'id_asiento'      => $id_asiento_cabecera,
                        'id_banco'        => $caja_banco->id,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                        'modulo'          => 'PAGO DECIMO CUARTO EMPLEADOS',
                    ];
                    //$id_nota = 0;
                    $id_nota = Nota_Debito::insertGetId($nota_debito);

                    $nota_deb_detalle = [
                        'id_nota_debito'  => $id_nota,
                        'codigo'          => $plan_cuentas->cuenta_guardar,
                        'cuenta'          => $plan_cuentas->nombre_mostrar,
                        'debe'            => $sum_pagar,
                        'haber'           => number_format(0, 2),
                        'valor_base'      => $sum_pagar, //number_format($valor['debe'], 2),
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                    ];
                    Nota_Debito_Detalle::create($nota_deb_detalle);
                } else { //COMPROBANTE DE EGRESO
                    $numero_factura = LogAsiento::getSecuencia(2);

                    $input_comprobante = [
                        'descripcion'     => $concepto,
                        'estado'          => '1',
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_secuencia'    => 'null',
                        'nota'            => $concepto,
                        'fecha_comprobante' => $request->fecha_creacion,
                        'beneficiario'    => $concepto2,
                        'check'           => 0,
                        'girado'          => $concepto2,
                        'id_caja_banco'   => $caja_banco->id,
                        'nro_cheque'      => $request->numero_cheque,
                        'valor'           => $sum_pagar,
                        'fecha_cheque'    => $request->fecha_cheque,
                        'secuencia'       => $numero_factura,
                        'id_empresa'      => $id_empresa,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'modulo'          => 'PAGO DECIMO CUARTO EMPLEADOS',
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                    Ct_Detalle_Comprobante_Egreso_Varios::create([
                        'id_comprobante_varios'          => $id_comprobante,
                        'codigo'                         => $plan_cuentas->cuenta_guardar,
                        'cuenta'                         => $plan_cuentas->nombre_mostrar,
                        'descripcion'                    => $concepto,
                        'debe'                           => $sum_pagar,
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $id_usuario,
                        'id_usuariomod'                  => $id_usuario,
                    ]);
                }
                $valor_cancelado = 0;
                foreach ($DecimosCuarto as $value) {
                    //dd($value);
                    $v_decimo_c  = Ct_Rh_Acumulados::find($value);
                    //dd($v_decimo_c);
                    if (!is_null($v_decimo_c)) {
                        $valor_aplicado = $request->valor_pago[$value];
                        $valor_aplicado = round($valor_aplicado, 2);
                        //dd($valor_aplicado);
                        //$valor_aplicado =  floatval($valor_aplicado);
                        $valor_cancelado = $v_decimo_c->valor_pago + $valor_aplicado;
                        $valor_cancelado = round($valor_cancelado, 2);
                        $sum_pagar   += $valor_aplicado;
                        //dd($valor_aplicado,$valor_cancelado, $v_decimo_t);


                        if ($ct_tipo_pago->tipo == 'ACREDITACION') { //debito
                            Ct_Rh_Acumulados_Detalle::create([
                                'id_ct_rh_acumulados'        => $value,
                                'valor'                      => $valor_aplicado,
                                'tipo'                       => 'PAGO A EMPLEADO',
                                'estado'                     => 1,

                                'id_asiento_pago'        => $id_asiento_cabecera,
                                'id_comprobante'         => $id_nota,
                                'tipo_comprobante'       => 'ACREDITACION',
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                            ]);
                        } else {
                            Ct_Rh_Acumulados_Detalle::create([
                                'id_ct_rh_acumulados'        => $value,
                                'valor'                      => $valor_aplicado,
                                'tipo'                       => 'PAGO A EMPLEADO',
                                'estado'                     => 1,

                                'id_asiento_pago'        => $id_asiento_cabecera,
                                'id_comprobante'         => $id_comprobante,
                                'tipo_comprobante'       => 'COMPROBANTE DE EGRESO',
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                            ]);
                        }


                        $arr_valor = [
                            'valor_pago'       => $valor_cancelado,
                        ];
                        $v_decimo_c->update($arr_valor);
                    }
                }
            }


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function imprimir_pago_decimo_cuarto($id_nomina)
    {

        $decimo = Ct_Rh_Acumulados::where('id_nomina', $id_nomina)->first();
        $sucursal = $decimo->id_sucursal;

        $registro = Ct_Nomina::where('id', $decimo->id_nomina)->first();
        $empresa  = Empresa::where('id', $registro->id_empresa)->first();
        $usuario  = User::where('id', $registro->id_user)->first();

        $periodo =  Ct_Rh_Sucursal_Periodo::where('id_empresa', $empresa->id)->where('id_sucursal', $sucursal)->first();

        $vistaurl = "contable.nuevo_rol_pago.pdf_pago_decimo_cuarto";

        $view     = \View::make($vistaurl, compact('decimo', 'registro', 'empresa', 'usuario', 'periodo'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Comprobante Pago Decimo Cuarto-' . $id_nomina . '.pdf');
    }

    public function estadistico_horas_extras(Request $request)
    {

        $id_sucursal = $request->id_sucursal;
        $nombre_empleado = $request->nombre_empleado;
        $anio_inicio = $request->anio_inicio;
        $anio_fin = $request->anio_fin;
        $mes_inicio = $request->mes_inicio;
        $mes_fin = $request->mes_fin;
        $id_empresa = $request->session()->get('id_empresa');

        if ($anio_fin == null) {
            $anio_fin = date("Y");
        }
        if ($anio_inicio == null) {
            $anio_inicio = date("Y");
        }

        if ($mes_fin == null) {
            $mes_fin = date("m");
        }
        if ($mes_inicio == null) {
            $mes_inicio = '1';
        }

        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->join('ct_nomina as n', 'n.id', 'ct_rol_pagos.id_nomina')
            ->join('ct_detalle_rol as d', 'd.id_rol', 'ct_rol_pagos.id')
            ->join('users as u', 'u.id', 'n.id_user')
            ->where('ct_rol_pagos.anio', '>=', $anio_inicio)
            ->where('ct_rol_pagos.anio', '<=', $anio_fin)
            ->where('ct_rol_pagos.mes', '>=', $mes_inicio)
            ->where('ct_rol_pagos.mes', '<=', $mes_fin)
            ->where('ct_rol_pagos.id_empresa', $id_empresa);

        if ($nombre_empleado != null) {
            $roles = $roles->where('n.nombres', 'like', '%' . $nombre_empleado . '%');
        }

        if ($id_sucursal != null) {
            $roles = $roles->where('n.id_sucursal', $id_sucursal);
        }

        $roles = $roles->select('u.nombre1', 'u.nombre2', 'u.apellido1', 'u.apellido2', 'n.nombres', 'ct_rol_pagos.anio', 'ct_rol_pagos.mes', 'n.id_sucursal', 'd.*')->get();

        $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->where('uso_nomina', '1')->get();

        return view('contable.nuevo_rol_pago.estadistico_he', ['roles' => $roles, 'sucursales' => $sucursales, 'request' => $request, 'anio_inicio' => $anio_inicio, 'anio_fin' => $anio_fin, 'mes_inicio' => $mes_inicio, 'mes_fin' => $mes_fin]);
    }

    public function excel_plantilla_nomina(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        // dd($request->all());        
        $id_empresa = $request->id_empresa;
        $anio       = $request->anio;
        $mes        = $request->mes;

        if ($id_empresa == null) {
            $empresa    = Empresa::where('prioridad', '1')->first();
            $id_empresa = $empresa->id;
        }
        if ($anio == null) {
            $anio = date('Y');
        }
        if ($mes == null) {
            $mes = date('m');
        }

        $roles = Ct_Rol_Pagos::where('ct_rol_pagos.estado', '1')
            ->where('ct_rol_pagos.anio', $anio)
            ->where('ct_rol_pagos.mes', $mes)
            ->where('ct_rol_pagos.id_empresa', $id_empresa)
            ->join('ct_nomina as nom', 'nom.id', 'ct_rol_pagos.id_nomina')
            ->select('ct_rol_pagos.*', 'nom.nombres', 'nom.sueldo_neto', 'nom.bono', 'nom.bono_imputable', 'nom.movilizacion', 'nom.alimentacion', 'nom.seguro_privado', 'nom.impuesto_renta', 'nom.parqueo', 'nom.jornada', 'nom.dias_laborables', 'nom.cantidad_horas', 'nom.horas_mes')
            // ->toSql();
            ->get();
        //dd($roles);
        Excel::create('Plantilla_Roles_' . $mes . '_' . $anio, function ($excel) use ($roles) {

            $excel->sheet('Nomina Empleados', function ($sheet) use ($roles) {
                $abec = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB");


                $i = 1;

                $arrTitulos = ["CEDULA", "COLABORADOR",    "SUELDO", "VALOR-HRA 50", "VALOR_HORA100", "num_horas50", "num_horas100", "total50", "total100", "TOTAL_H_E", "Bono", "obs_B", "bono_iess", "obs_Bi", "movilizacion", "obs_mov", "alimentacion", "obs_alim", "seguro", "obs_seg", "imp_renta", "obs_IR", "multa", "obs_multa", "otros_egresos", "obs_OE", "parqueo", "obs_parq"];

                $comienzo = 1;

                for ($i = 0; $i < count($arrTitulos); $i++) {
                    $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrTitulos, $i) {
                        // manipulate the cel
                        $cell->setValue($arrTitulos[$i]);
                        $cell->setBackground('#D1E9F2');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                foreach ($roles as $value) {
                    $arrValue = [];
                    $bono = $value->bono;
                    if ($bono == null) {
                        $bono = '0';
                    }
                    $bono_imputable = $value->bono_imputable;
                    if ($bono_imputable == null) {
                        $bono_imputable = '0';
                    }
                    $movilizacion = $value->movilizacion;
                    if ($movilizacion == null) {
                        $movilizacion = '0';
                    }
                    $alimentacion = $value->alimentacion;
                    if ($alimentacion == null) {
                        $alimentacion = '0';
                    }
                    $seguro_privado = $value->seguro_privado;
                    if ($seguro_privado == null) {
                        $seguro_privado = '0';
                    }
                    $impuesto_renta = $value->impuesto_renta;
                    if ($impuesto_renta == null) {
                        $impuesto_renta = '0';
                    }
                    $parqueo = $value->parqueo;
                    if ($parqueo == null) {
                        $parqueo = '0';
                    }

                    $sueldo_mensual = $value->sueldo_neto;
                    if ($value->jornada != '0') {
                        $dias_laborados = $value->dias_laborables;
                        $horas_dias = $value->cantidad_horas;
                        $horas_mes = $value->horas_mes;
                        $sueldo_mensual = $this->calcular_sueldo_neto($value->jornada, $horas_dias, $dias_laborados, $sueldo_mensual);
                    }

                    $nombre_empleado = $value->usuario->apellido1 . ' ' . $value->usuario->apellido2 . ' ' . $value->usuario->nombre1 . ' ' . $value->usuario->nombre2;
                    $arrValue = [$value->id_user, $nombre_empleado, $sueldo_mensual, "0", "0", "0", "0", "0", "0", "0", $bono, "", $bono_imputable, "", $movilizacion, "", $alimentacion, "", $seguro_privado, "", $impuesto_renta, "", "0", "", "0", "", $parqueo, ""];
                    for ($i = 0; $i < count($arrValue); $i++) {
                        $sheet->cell("{$abec[$i]}{$comienzo}", function ($cell) use ($arrValue, $i) {
                            // manipulate the cel
                            $cell->setValue($arrValue[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                }
            });
        })->export('xlsx');
    }
    public function ct_rh_acumulados_vacaciones($anio, $sucursal, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_empresa     = $request->session()->get('id_empresa');

        $empresa = Empresa::find($id_empresa);
        if (is_null($empresa)) {
            return "No encuentra empresa";
        }

        //$nominas = Ct_Nomina::where('id_empresa', $empresa->id)->where('estado','1')->get();
        $nominas = Ct_Nomina::where('id_empresa', $empresa->id)->where('id_sucursal', $sucursal)->where('estado', '1')->get();
        //dd($nominas);
        foreach ($nominas as $nomina) {

            $acumulado = Ct_Rh_Acumulados::where('id_empresa', $empresa->id)
                ->where('anio', $anio)
                ->where('tipo', 'VACACIONES')
                ->where('estado', 1)
                ->where('id_nomina', $nomina->id)
                ->where('id_sucursal', $sucursal)
                ->first();
            //dd($acumulado);

            if (is_null($acumulado)) {
                Ct_Rh_Acumulados::create([
                    'id_empresa'        => $nomina->id_empresa,
                    'id_nomina'         => $nomina->id,
                    'anio'              => $anio,
                    'tipo'              => 'VACACIONES',
                    //'inicio'            => ( $anio - 1 ) . '3',
                    'inicio'            => $anio . '1',
                    //'fin'               => $anio . '2',
                    'fin'               => $anio . '12',
                    'valor'             => '0',
                    'estado'            => 1,
                    'id_sucursal'       => $sucursal,
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario,
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ]);
            }
        }

        $this->ct_rh_acumulados_update_vacaciones($anio, $sucursal, $request);

        return "listo";
    }

    public function ct_rh_acumulados_update_vacaciones($anio, $sucursal, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::find($id_empresa);
        if (is_null($empresa)) {
            return "No encuentra empresa";
        }

        $acumulados = Ct_Rh_Acumulados::where('estado', 1)->where('anio', $anio)->where('tipo', 'VACACIONES')->where('id_sucursal', $sucursal)->get();
        //dd($acumulados);

        foreach ($acumulados as $acumulado) {

            $anio_ini = substr($acumulado->inicio, 0, 4);
            $mes_ini = substr($acumulado->inicio, 4, 2);
            $anio_fin = substr($acumulado->fin, 0, 4);
            $mes_fin = substr($acumulado->fin, 4, 2);

            $roles1 = DB::table('ct_rol_pagos as r')
                ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
                ->join('ct_nomina as nom', 'nom.id', 'r.id_nomina')
                ->where('r.id_empresa', $empresa->id)
                ->where('r.anio', $anio_ini) //2022
                ->where('r.mes', '>=', $mes_ini) //3
                ->where('r.estado', 1)
                ->where('r.id_nomina', $acumulado->id_nomina)
                ->select('r.id as rid', 'd.*', 'd.valor_vacacion', 'r.pasante', 'r.jornada')
                ->get();


            $valor_sueldo_basico = 0;

            $val_sal_basico = Ct_Rh_Valores::where('id_empresa', $empresa->id)
                ->where('tipo', 3)->first();

            $v_vacacion_final = 0;
            $dlaborados = 0;
            if (!is_null($val_sal_basico)) {
                $valor_sueldo_basico =  $val_sal_basico->valor;
            }

            foreach ($roles1 as $rol) {

                $valor_vacacion = $rol->valor_vacacion;
                $detalle    =   Ct_Detalle_Rol::find($rol->id);
                $dlaborados += $detalle->dias_laborados;

                $v_vacacion_final  +=  $valor_vacacion;
            }


            $acumulado->update([
                'valor' => $v_vacacion_final,
                'sueldo_basico' => $valor_sueldo_basico,
                'dias_laborados' => $dlaborados,
            ]);
        }


        return "listo";
    }
    public function acumula_valor_vacacion(Request $request)
    {

        $anio = $request->anio;
        $empleado = $request->empleado;
        $id_sucursal = $request->sucursal;

        if ($request['excel'] == 0) {

            if ($anio == null) {
                $anio = date('Y');
            }
            $id_empresa     = $request->session()->get('id_empresa');


            $ValorVacaciones = Ct_Rh_Acumulados::where('ct_rh_acumulados.tipo', 'VACACIONES')
                ->join('ct_nomina as n', 'n.id', 'ct_rh_acumulados.id_nomina')
                ->join('users as u', 'u.id', 'n.id_user')
                //->join('ct_rol_pagos as p','p.id_nomina','n.id')
                //->join('ct_detalle_rol as d','d.id_rol','p.id')
                ->where('ct_rh_acumulados.estado', 1)
                ->where('ct_rh_acumulados.anio', $anio)
                ->where('ct_rh_acumulados.id_empresa', $id_empresa)
                ->join('users as us', 'us.id', 'n.id_user');

            if ($empleado != null) {

                //ValorVacaciones = $ValorVacaciones->where('n.nombres','like','%'.$empleado.'%'); 
                $ValorVacaciones = $ValorVacaciones->where(function ($jq1) use ($empleado) {
                    $jq1->orwhereraw('CONCAT(u.nombre1," ",u.nombre2," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $empleado . '%'])
                        ->orwhereraw('CONCAT(u.nombre1," ",u.apellido1," ",u.apellido2) LIKE ?', ['%' . $empleado . '%']);
                });
            }

            if ($id_sucursal != null) {

                $ValorVacaciones = $ValorVacaciones->where('ct_rh_acumulados.id_sucursal', $id_sucursal);
            }


            $ValorVacaciones = $ValorVacaciones->select('ct_rh_acumulados.id as id', 'ct_rh_acumulados.*', 'n.id_user', 'n.nombres', 'us.nombre1', 'us.nombre2', 'us.apellido1', 'us.apellido2')->where('ct_rh_acumulados.valor', '<>', '0')->get();
            $sucursales = Ct_Sucursales::where('estado', '1')->where('id_empresa', $id_empresa)->where('uso_nomina', '1')->get();

            if (count($sucursales) == 1) {
                $id_sucursal = $sucursales->first()->id;
            }



            $lista_banco   = Ct_Bancos::all();

            $bancos = Ct_Caja_Banco::where('estado', '1')->where('id_empresa', $id_empresa)->get();
            $tipo_pago_rol = Ct_Rh_Tipo_Pago::all();
            $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->tipo_pago);


            return view('contable.nuevo_rol_pago.vacaciones.listado_valor_vacaciones', ['lista_banco' => $lista_banco, 'bancos' => $bancos, 'tipo_pago_rol' => $tipo_pago_rol, 'ValorVacaciones' => $ValorVacaciones, 'anio' => $anio, 'empleado' => $empleado, 'sucursales' => $sucursales, 'id_sucursal' => $id_sucursal]);
        } else {
            $titulos = array("N°", "CEDULA", "NOMBRES Y APELLIDOS", "OCUPACION", "GENERO", "DIAS TRABAJADOS", "RETENCION", "VALOR DECIMO", "FIRMA O HUELLA");
            //Posiciones en el excel
            $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
            if ($anio == null) {
                $anio = 2023;
            }
            $id_empresa     = $request->session()->get('id_empresa');
            $empresa = Empresa::where('id', $id_empresa)->first();
            $ValorVacaciones = Ct_Rh_Acumulados::where('ct_rh_acumulados.tipo', 'VACACIONES')
                ->where('ct_rh_acumulados.estado', 1)
                ->where('ct_rh_acumulados.anio', $anio)
                ->where('ct_rh_acumulados.id_empresa', $id_empresa)
                ->join('ct_nomina as n', 'n.id', 'ct_rh_acumulados.id_nomina')
                ->join('users as us', 'us.id', 'n.id_user');

            if ($empleado != null) {
                $ValorVacaciones = $ValorVacaciones->where('n.nombres', 'like', '%' . $empleado . '%');
            }

            $ValorVacaciones = $ValorVacaciones->select('ct_rh_acumulados.*', 'n.id_user', 'n.nombres', 'n.sexo', 'n.cargo', 'us.nombre1', 'us.nombre2', 'us.apellido1', 'us.apellido2')->where('ct_rh_acumulados.valor', '<>', '0')->get();

            //dd($ValorVacaciones);

            Excel::create('VALOR VACACIONES', function ($excel) use ($titulos, $posicion, $ValorVacaciones, $request, $anio, $empresa) {
                $excel->sheet('VALOR VACACIONES', function ($sheet) use ($titulos, $posicion, $ValorVacaciones, $request, $anio, $empresa) {
                    $sheet->cells('A1:C2', function ($cell) use ($empresa, $sheet) {
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                        $objDrawing->setCoordinates('B1');
                        $objDrawing->setHeight(150);
                        $objDrawing->setWidth(200);
                        $cell->setAlignment('center');
                        $objDrawing->setWorksheet($sheet);
                    });
                    $sheet->mergeCells('D1:I1');
                    $sheet->cell('D1', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('INFORME INDIVIDUAL SOBRE EL PAGO DE REMUNERACIÓN VACACIONES');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('D2:I2');
                    $sheet->cell('D2', function ($cell) use ($empresa, $sheet) {
                        // manipulate the cel
                        $cell->setValue($empresa->razonsocial);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->mergeCells('D3:I3');
                    $sheet->cell('D3', function ($cell) use ($empresa, $sheet) {
                        // manipulate the cel
                        $cell->setValue('R.U.C:' . $empresa->id);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $comienzo = 4; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL
                    /****************TITULOS DEL EXCEL*********************/
                    //crear los titulos en el excel
                    for ($i = 0; $i < count($titulos); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                            $cell->setValue($titulos[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#92CFEF');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    /*****FIN DE TITULOS DEL EXCEL***********/
                    $contador1 = 1;
                    foreach ($ValorVacaciones as $value) {
                        //dd($days);
                        $datos_excel = array();
                        array_push($datos_excel, $contador1, $value->id_user, $value->nombres, '', $value->sexo, $value->dias_laborados, '', $value->valor, '');
                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                        $contador1++;
                    }
                });
                $excel->getActiveSheet()->getStyle('C4:C4')->getAlignment()->setWrapText(true);
            })->export('xlsx');
        }
        //dd($r


    }

    public function detalle_valor_vacacion($id_nomina, $inicio, $fin, Request $request)
    {

        $anio_ini = substr($inicio, 0, 4);
        $mes_ini = substr($inicio, 4, 2);
        $anio_fin = substr($fin, 0, 4);
        $mes_fin = substr($fin, 4, 2);

        $id_empresa     = $request->session()->get('id_empresa');


        $roles1 = DB::table('ct_rol_pagos as r')
            ->join('ct_detalle_rol as d', 'd.id_rol', 'r.id')
            ->where('r.id_empresa', $id_empresa)
            ->where('r.anio', $anio_ini) //2022
            ->where('r.mes', '>=', $mes_ini) //3
            ->where('r.estado', 1)
            ->where('r.id_nomina', $id_nomina)
            ->select('r.id as rid', 'd.*')
            ->get();



        $nomina = Ct_Nomina::find($id_nomina);

        return view('contable.nuevo_rol_pago.vacaciones.detalle_vacaciones', ['roles1' => $roles1, 'nomina' => $nomina]);
    }

    public function pago_valor_vacacion(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($id_empresa);
        $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->tipo_pago);

        $ValorVacaciones = $request->ValorVacaciones;
        //$valores_pagos  = $request->valor_pago;

        //dd($request->all());
        if (!is_null($ValorVacaciones)) {
            if (count($ValorVacaciones) < 1) {
                return ['respuesta' => 'error', 'msj' => 'SELECCIONE AL MENOS UN EMPLEADO PARA GENERAR', 'titulos' => 'Error'];
            }
        }

        $caja_banco = Ct_Caja_Banco::where('id_empresa', $id_empresa)->where('cuenta_mayor', $request->cuenta_saliente)->where('estado', 1)->first();

        if (is_null($caja_banco)) {
            return ['respuesta' => 'error', 'msj' => 'CAJA BANCO SIN PLAN DE CUENTAS', 'titulos' => 'Error'];
        }

        $verifica_cierre = LogAsiento::verificar_cierre_anio($request->fecha_creacion);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }

        //dd($ValorVacaciones);

        DB::beginTransaction();
        try {
            $sum_pagar = 0;
            $valor_aplicado = 0;
            $valor_por_cruzar = 0;
            $valor_disponible = 0;
            //$id_comprobante = null;
            //$id_nota = null;
            //dd($ValorVacaciones);
            //dd($request->all());
            foreach ($ValorVacaciones as $value) {
                //dd($value);
                $v_vacacion  = Ct_Rh_Acumulados::find($value);

                //dd($v_vacacion);
                if (!is_null($v_vacacion)) {
                    $valor_por_cruzar = $request->valor_pago[$value];
                    //dd($request->valor_pago[$value]);
                    $valor_por_cruzar =  floatval($valor_por_cruzar);
                    $valor_disponible = $v_vacacion->valor - $v_vacacion->valor_pago;
                    //dd($v_vacacion->valor, $valor_por_cruzar,$valor_disponible);
                    if ($valor_por_cruzar > $valor_disponible) {
                        return ['respuesta' => 'error', 'msj' => 'EL VALOR A PAGAR ES MAYOR AL SALDO DISPONIBLE', 'titulos' => 'ERROR!'];
                    } else {
                        $valor_aplicado = $valor_por_cruzar;
                        $sum_pagar   += $valor_aplicado;
                    }

                    /*$arr_valor = [
                        'valor_pago'       => $valor_aplicado,
                    ];
                    $v_vacacion->update($arr_valor);

                    Ct_Rh_Acumulados_Detalle::create([
                        'id_ct_rh_acumulados'        => $value,
                        'valor'                      => $valor_aplicado,
                        'tipo'                       => 'PAGO A EMPLEADO',
                        'estado'            => 1,

                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ]);*/
                }
            }
            //dd($sum_pagar);

            //dd($sum_pagar);
            //$meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            //$ms = intval($DecimosCuarto->mes) - 1;
            //$concepto  = 'PAGOS DE SUELDOS DEL PERIODO AÑO: '.$cuentas_rol->anio.' Mes: '.$meses[$ms].' Por la Cantidad de:' . $sum_pagar;
            $concepto  = $request->concepto1;
            $concepto2 = 'Valores Correspondientes al Pago de Vacaciones  Por la Cantidad de:' . $sum_pagar;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request->fecha_creacion,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'PAGO VALOR VACACIONES EMPLEADOS',
                //'sucursal'        => '',
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            if ($sum_pagar > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.07.04.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_VACACIONES_(2)');

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request->fecha_creacion,
                    'debe'                => $sum_pagar,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                $plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->cuenta_saliente)->orwhere('plan', $request->cuenta_saliente)->first();
                $plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request->cuenta_saliente,
                    'descripcion'         => $plan_cuentas2->nombre,
                    'fecha'               => $request->fecha_creacion,
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                if ($ct_tipo_pago->tipo == 'ACREDITACION') { //DEBITO

                    $nota_debito         = [
                        'concepto'        => $concepto,
                        'fecha'           => $request->fecha_creacion,
                        'valor'           => $sum_pagar,
                        'empresa'         => $id_empresa,
                        'tipo'            => "BAN-ND",
                        'id_asiento'      => $id_asiento_cabecera,
                        'id_banco'        => $caja_banco->id,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                        'modulo'          => 'PAGO VALOR VACACIONES EMPLEADOS',
                    ];
                    //$id_nota = 0;
                    $id_nota = Nota_Debito::insertGetId($nota_debito);

                    $nota_deb_detalle = [
                        'id_nota_debito'  => $id_nota,
                        'codigo'          => $plan_cuentas->cuenta_guardar,
                        'cuenta'          => $plan_cuentas->nombre_mostrar,
                        'debe'            => $sum_pagar,
                        'haber'           => number_format(0, 2),
                        'valor_base'      => $sum_pagar, //number_format($valor['debe'], 2),
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                    ];
                    Nota_Debito_Detalle::create($nota_deb_detalle);
                } else { //COMPROBANTE DE EGRESO
                    $numero_factura = LogAsiento::getSecuencia(2);

                    $input_comprobante = [
                        'descripcion'     => $concepto,
                        'estado'          => '1',
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_secuencia'    => 'null',
                        'nota'            => $concepto,
                        'fecha_comprobante' => $request->fecha_creacion,
                        'beneficiario'    => $concepto2,
                        'check'           => 0,
                        'girado'          => $concepto2,
                        'id_caja_banco'   => $caja_banco->id,
                        'nro_cheque'      => $request->numero_cheque,
                        'valor'           => $sum_pagar,
                        'fecha_cheque'    => $request->fecha_cheque,
                        'secuencia'       => $numero_factura,
                        'id_empresa'      => $id_empresa,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'modulo'          => 'PAGO VACACIONES EMPLEADOS',
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                    Ct_Detalle_Comprobante_Egreso_Varios::create([
                        'id_comprobante_varios'          => $id_comprobante,
                        'codigo'                         => $plan_cuentas->cuenta_guardar,
                        'cuenta'                         => $plan_cuentas->nombre_mostrar,
                        'descripcion'                    => $concepto,
                        'debe'                           => $sum_pagar,
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $id_usuario,
                        'id_usuariomod'                  => $id_usuario,
                    ]);
                }


                $valor_cancelado = 0;
                foreach ($ValorVacaciones as $value) {
                    //dd($value);
                    $v_vacacion  = Ct_Rh_Acumulados::find($value);
                    //dd($v_vacacion);
                    if (!is_null($v_vacacion)) {
                        $valor_aplicado = $request->valor_pago[$value];
                        $valor_cancelado = $v_vacacion->valor_pago + $valor_aplicado;
                        $sum_pagar   += $valor_aplicado;
                        if ($ct_tipo_pago->tipo == 'ACREDITACION') { //debito
                            Ct_Rh_Acumulados_Detalle::create([
                                'id_ct_rh_acumulados'        => $value,
                                'valor'                      => $valor_aplicado,
                                'tipo'                       => 'PAGO A EMPLEADO',
                                'estado'                     => 1,
                                'id_asiento_pago'        => $id_asiento_cabecera,
                                'id_comprobante'         => $id_nota,
                                'tipo_comprobante'       => 'ACREDITACION',
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                            ]);
                        } else {
                            Ct_Rh_Acumulados_Detalle::create([
                                'id_ct_rh_acumulados'        => $value,
                                'valor'                      => $valor_aplicado,
                                'tipo'                       => 'PAGO A EMPLEADO',
                                'estado'                     => 1,
                                'id_asiento_pago'        => $id_asiento_cabecera,
                                'id_comprobante'         => $id_comprobante,
                                'tipo_comprobante'       => 'COMPROBANTE DE EGRESO',
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                            ]);
                        }
                        $arr_valor = [
                            'valor_pago'       => $valor_cancelado,
                        ];
                        $v_vacacion->update($arr_valor);
                    }
                }
            }
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function detalle_pago_vacacion($id_ct_rh_acumulado, Request $request)
    {

        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        //dd($ct_rh_acumulado);
        $nomina = Ct_Nomina::find($ct_rh_acumulado->id_nomina);
        $detalles = Ct_Rh_Acumulados_Detalle::where('id_ct_rh_acumulados', $id_ct_rh_acumulado)->where('estado', '1')->get();
        //dd($detalles);

        return view('contable.nuevo_rol_pago.vacaciones.detalle_pago_vacaciones', ['nomina' => $nomina, 'detalles' => $detalles]);
    }

    public function detalle_prestamos_empleado_vac($id_ct_rh_acumulado)
    {

        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        $valor_por_cruzar = $ct_rh_acumulado->valor - $ct_rh_acumulado->valor_pago;
        //dd($valor_por_cruzar);
        if ($valor_por_cruzar == 0) {
            return "
            <center>
            <div class='col-md-12'>
                <br> <br> <br> <br> <br> <br> <br>
                <spam class='color_rojo'> EL EMPLEADO NO TIENE DISPONIBLE VALORES PARA CRUZAR </spam>
            </div>
            </center>
            ";
        }
        $nomina = Ct_Nomina::find($ct_rh_acumulado->id_nomina);
        $prestamos = Ct_Rh_Prestamos::where('id_empl', $nomina->id_user)->where('estado', '1')->where('prest_cobrad', '0')->where('id_empresa', $nomina->id_empresa)->get();
        //dd($prestamos);
        if (count($prestamos) == 0) {
            return "
            <center>
            <div class='col-md-12'>
                <br> <br> <br> <br> <br> <br> <br>
                <spam class='color_rojo'> EL EMPLEADO NO TIENE PRESTAMOS </spam>
            </div>
            </center>
            ";
        }
        return view('contable.nuevo_rol_pago.vacaciones.listado_prestamos_empleado', ['prestamos' => $prestamos, 'nomina' => $nomina, 'ct_rh_acumulado' => $ct_rh_acumulado]);
    }

    public function cruce_valor_prestamos_vacaciones(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($id_empresa);
        $id_ct_rh_acumulado = $request->id_ct_rh_acum;
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        $sucursal     = $ct_rh_acumulado->obtiene_sucursal;

        //dd($ct_rh_acumulado);    
        $Prestamos = $request->Prestamos;
        //$valores_pagos  = $request->valor_pago;
        //dd($Prestamos);
        //dd($request->DecimosCuarto);
        if (!is_null($Prestamos)) {
            if (count($Prestamos) < 1) {
                return ['respuesta' => 'error', 'msj' => 'SELECCIONE AL MENOS UN PRESTAMO PARA GENERAR', 'titulos' => 'Error'];
            }
        }

        $verifica_cierre = LogAsiento::verificar_cierre_anio($request["fecha_creacion" . $id_ct_rh_acumulado]);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }

        //dd($ValorVacaciones);

        DB::beginTransaction();
        try {
            $sum_pagar = 0;
            $valor_aplicado = 0;
            //dd($ValorVacaciones);
            //dd($request->all());

            $anio_cruce = date("Y");
            $mes_cruce = date("m");

            $valor_prest_cruzar = 0;
            $valor_disponible = 0;
            $valor_prest_cruzar = $ct_rh_acumulado->valor - $ct_rh_acumulado->valor_pago;

            foreach ($Prestamos as $value) {
                //dd($value);
                //$v_vacacion  = Ct_Rh_Acumulados::find($value);
                $prestamo = Ct_Rh_Prestamos::find($value);
                //dd($prestamo);

                if (!is_null($prestamo)) {
                    $valor_aplicado = $request->valor_pago_prest[$value];
                    $sum_pagar   += $valor_aplicado;
                    if ($sum_pagar > $valor_prest_cruzar) {

                        return ['respuesta' => 'error', 'msj' => 'EL VALOR A CRUZAR ES MAYOR AL SALDO DISPONIBLE', 'titulos' => 'ERROR!'];
                    }
                }
            }
            //dd($sum_pagar);

            $concepto  = 'CRUCE DE PRESTAMOS CON VALOR VACACIONES';
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request["fecha_creacion" . $id_ct_rh_acumulado],
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'CRUCE VALOR VACACIONES EMPLEADOS',
                'sucursal'        => $sucursal->codigo_sucursal,

            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            if ($sum_pagar > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.04.04.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_VACACIONES_(2)');
                //dd($plan_cuentas->nombre_mostrar);

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request["fecha_creacion" . $id_ct_rh_acumulado],
                    'debe'                => $sum_pagar,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                //$plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->cuenta_saliente)->orwhere('plan', $request->cuenta_saliente)->first();
                //$plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);
                $plan_cuentas2 = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');
                //dd($plan_cuentas2->cuenta_saliente);
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas2->cuenta_guardar,
                    'descripcion'         => $plan_cuentas2->nombre_mostrar,
                    'fecha'               => $request["fecha_creacion" . $id_ct_rh_acumulado],
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            $valor_cancelado = 0;
            foreach ($Prestamos as $value) {
                //dd($value);
                $prestamo = Ct_Rh_Prestamos::find($value);
                //dd($v_vacacion);

                if (!is_null($prestamo)) {
                    $valor_aplicado = $request->valor_pago_prest[$value];
                    $valor_cancelado = $ct_rh_acumulado->valor_pago + $valor_aplicado;
                    $sum_pagar   += $valor_aplicado;

                    $cuotas = $prestamo->detalles()->where('estado', 1)->where('estado_pago', 1)->count(); //dd($cuotas);
                    $xanio   = $prestamo->anio_inicio_cobro;
                    $xmes    = $prestamo->mes_inicio_cobro;
                    $xmes    = $xmes + $cuotas;
                    if ($xmes > 12) {
                        $xmes = 1;
                        $xanio++;
                    }
                    $cuotas++;

                    $input_det_acum = [
                        'id_ct_rh_acumulados'        => $id_ct_rh_acumulado,
                        'valor'                      => $valor_aplicado,
                        'tipo'                       => 'CRUCE PRESTAMOS',
                        'estado'                     => 1,
                        'tipo_comprobante'           => $prestamo->concepto,

                        'id_asiento_pago'        => $id_asiento_cabecera,

                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ];

                    $id_det_acum = Ct_Rh_Acumulados_Detalle::insertGetId($input_det_acum);

                    Ct_Rh_Prestamos_Detalle::create([
                        'id_ct_rh_prestamos'        => $value,
                        'anio'                       => $anio_cruce,
                        'mes'                        => $mes_cruce,
                        'fecha'                      => date('Y-m-d H:i:s'),
                        'cuota'                      => $cuotas,
                        'valor_cuota'                => $valor_aplicado,
                        'estado'                     => 1,
                        'estado_pago'                => 1,
                        'fecha_pago'                 => date('Y-m-d H:i:s'),
                        'observacion'                => 'Pago desde el modulo cruce de vacaciones',
                        'id_ct_rh_acum_det'          => $id_det_acum,

                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ]);

                    $arr_valor = [
                        'saldo_total'       => $prestamo->saldo_total - $valor_aplicado,
                    ];
                    $prestamo->update($arr_valor);


                    $arr_valor1 = [
                        'valor_pago'       => $valor_cancelado,
                    ];
                    $ct_rh_acumulado->update($arr_valor1);
                }
            }




            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function imprimir_pago_vacacion($id_ct_rh_acumulado)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;

        $ct_rh_acum = Ct_Rh_Acumulados::findorfail($id_ct_rh_acumulado);

        if (($rolUsuario != 1) && ($rolUsuario != 20) && ($rolUsuario != 21) && ($rolUsuario != 22)) {
            if ($ct_rh_acum->id_user != Auth::user()->id) {
                return response()->view('errors.404');
            }
        }

        /*$lista_cuota_quirog = Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $id_rolpag)->get();
        $lista_cuota_hip    = Ct_Rh_Cuotas_Quirografario::where('id_rol', $id_rolpag)->get();*/

        $registro = Ct_Nomina::where('id', $ct_rh_acum->id_nomina)->first();
        $empresa  = Empresa::where('id', $registro->id_empresa)->first();
        $usuario  = User::where('id', $registro->id_user)->first();

        /* $rol_forma_pago = Ct_Rol_Forma_Pago::where('id_rol_pago', $rol_pago->id)->first();*/

        $detalles_acum = Ct_Rh_Acumulados_Detalle::where('id_ct_rh_acumulados', $ct_rh_acum->id)->where('estado', 1)->get();

        /*$ct_for_pag = Ct_Rol_Forma_Pago::where('id_rol_pago', $rol_pago->id)->get(); */
        $vistaurl = "contable.nuevo_rol_pago.vacaciones.pdf_pago_vacaciones";

        $view     = \View::make($vistaurl, compact('ct_rh_acum', 'registro', 'empresa', 'usuario', 'detalles_acum'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Comprobante Rol Pago-' . $id_ct_rh_acumulado . '.pdf');
    }

    public function pago_decimo_tercer(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($id_empresa);
        $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->tipo_pago);
        //dd($request->all());
        $DecimosTeceros = $request->DecimosTeceros;
        //$valores_pagos  = $request->valor_pago;

        //dd($request->DecimosCuarto);
        if (!is_null($DecimosTeceros)) {
            if (count($DecimosTeceros) < 1) {
                return ['respuesta' => 'error', 'msj' => 'SELECCIONE AL MENOS UN EMPLEADO PARA GENERAR', 'titulos' => 'Error'];
            }
        }

        $caja_banco = Ct_Caja_Banco::where('id_empresa', $id_empresa)->where('cuenta_mayor', $request->cuenta_saliente)->where('estado', 1)->first();
        //dd($request->cuenta_saliente, $caja_banco);
        if (is_null($caja_banco)) {
            return ['respuesta' => 'error', 'msj' => 'CAJA BANCO SIN PLAN DE CUENTAS', 'titulos' => 'Error'];
        }

        /*$verifica_cierre = LogAsiento::verificar_cierre_anio($request->fecha_creacion);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }*/

        //dd($ValorVacaciones);
        $verifica_cierre = json_decode(Ct_Cierre_Cabecera::verificar_fecha($request['fecha_creacion']));
        if ($verifica_cierre->status == 'error') {
            return ['status' => $verifica_cierre->status, 'msj' => $verifica_cierre->message];
        }

        DB::beginTransaction();
        try {

            $sum_pagar = 0;
            $valor_aplicado = 0;
            $valor_por_cruzar = 0;
            $valor_disponible = 0;
            //dd($ValorVacaciones);
            //dd($request->all());
            foreach ($DecimosTeceros as $value) {
                //dd($value);
                $v_decimo_t  = Ct_Rh_Acumulados::find($value);

                //dd($v_decimo_t);
                if (!is_null($v_decimo_t)) {
                    $valor_por_cruzar = $request->valor_pago[$value];
                    $valor_por_cruzar =  floatval($valor_por_cruzar);
                    $valor_disponible = $v_decimo_t->valor - $v_decimo_t->valor_pago;
                    //dd($valor_por_cruzar, $valor_disponible);
                    $valor_por_cruzar = round($valor_por_cruzar, 2);
                    $valor_disponible = round($valor_disponible, 2);
                    if ($valor_por_cruzar > $valor_disponible) {
                        return ['respuesta' => 'error', 'msj' => 'EL VALOR A PAGAR ES MAYOR AL SALDO DISPONIBLE ' . $valor_por_cruzar . ' EL valor disponible es' . $valor_disponible, 'titulos' => 'ERROR!'];
                    } else {
                        $valor_aplicado = $valor_por_cruzar;
                        $sum_pagar   += $valor_aplicado;
                    }

                    /*$arr_valor = [
                        'valor_pago'       => $valor_aplicado,
                    ];
                    $v_vacacion->update($arr_valor);

                    Ct_Rh_Acumulados_Detalle::create([
                        'id_ct_rh_acumulados'        => $value,
                        'valor'                      => $valor_aplicado,
                        'tipo'                       => 'PAGO A EMPLEADO',
                        'estado'            => 1,

                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ]);*/
                }
            }
            //dd($sum_pagar);

            //dd($sum_pagar);
            //$meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            //$ms = intval($DecimosCuarto->mes) - 1;
            //$concepto  = 'PAGOS DE SUELDOS DEL PERIODO AÑO: '.$cuentas_rol->anio.' Mes: '.$meses[$ms].' Por la Cantidad de:' . $sum_pagar;
            $concepto  = $request->concepto1;
            //$concepto2 = 'Decimo Tercer Sueldo  Por la Cantidad de:' . $sum_pagar;
            $concepto2 = $request->concepto2;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request->fecha_creacion,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'PAGO VALOR DECIMO TERCER EMPLEADOS',
                //'sucursal'        => '',
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            if ($sum_pagar > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.07.04.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOTERCER_SUELDO');
                //dd($plan_cuentas);

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request->fecha_creacion,
                    'debe'                => $sum_pagar,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                $plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->cuenta_saliente)->orwhere('plan', $request->cuenta_saliente)->first();
                $plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $request->cuenta_saliente,
                    'descripcion'         => $plan_cuentas2->nombre,
                    'fecha'               => $request->fecha_creacion,
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                if ($ct_tipo_pago->tipo == 'ACREDITACION') { //DEBITO

                    $nota_debito         = [
                        'concepto'        => $concepto,
                        'fecha'           => $request->fecha_creacion,
                        'valor'           => $sum_pagar,
                        'empresa'         => $id_empresa,
                        'tipo'            => "BAN-ND",
                        'id_asiento'      => $id_asiento_cabecera,
                        'id_banco'        => $caja_banco->id,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                        'modulo'          => 'PAGO VALOR VACACIONES EMPLEADOS',
                    ];
                    //$id_nota = 0;
                    $id_nota = Nota_Debito::insertGetId($nota_debito);

                    $nota_deb_detalle = [
                        'id_nota_debito'  => $id_nota,
                        'codigo'          => $plan_cuentas->cuenta_guardar,
                        'cuenta'          => $plan_cuentas->nombre_mostrar,
                        'debe'            => $sum_pagar,
                        'haber'           => number_format(0, 2),
                        'valor_base'      => $sum_pagar, //number_format($valor['debe'], 2),
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                    ];
                    Nota_Debito_Detalle::create($nota_deb_detalle);
                } else { //COMPROBANTE DE EGRESO
                    $numero_factura = LogAsiento::getSecuencia(2);

                    $input_comprobante = [
                        'descripcion'     => $concepto,
                        'estado'          => '1',
                        'id_asiento_cabecera' => $id_asiento_cabecera,
                        'id_secuencia'    => 'null',
                        'nota'            => $concepto,
                        'fecha_comprobante' => $request->fecha_creacion,
                        'beneficiario'    => $concepto2,
                        'check'           => 0,
                        'girado'          => $concepto2,
                        'id_caja_banco'   => $caja_banco->id,
                        'nro_cheque'      => $request->numero_cheque,
                        'valor'           => $sum_pagar,
                        'fecha_cheque'    => $request->fecha_cheque,
                        'secuencia'       => $numero_factura,
                        'id_empresa'      => $id_empresa,
                        'id_usuariocrea'  => $id_usuario,
                        'id_usuariomod'   => $id_usuario,
                        'ip_creacion'     => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'modulo'          => 'PAGO DECIMO TERCER EMPLEADOS',
                    ];
                    $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                    Ct_Detalle_Comprobante_Egreso_Varios::create([
                        'id_comprobante_varios'          => $id_comprobante,
                        'codigo'                         => $plan_cuentas->cuenta_guardar,
                        'cuenta'                         => $plan_cuentas->nombre_mostrar,
                        'descripcion'                    => $concepto,
                        'debe'                           => $sum_pagar,
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $id_usuario,
                        'id_usuariomod'                  => $id_usuario,
                    ]);
                }


                $valor_cancelado = 0;
                foreach ($DecimosTeceros as $value) {
                    //dd($value);
                    $v_decimo_t  = Ct_Rh_Acumulados::find($value);
                    //dd($v_decimo_t);
                    if (!is_null($v_decimo_t)) {
                        $valor_aplicado = $request->valor_pago[$value];
                        $valor_aplicado = round($valor_aplicado, 2);
                        //dd($valor_aplicado);
                        //$valor_aplicado =  floatval($valor_aplicado);
                        $valor_cancelado = $v_decimo_t->valor_pago + $valor_aplicado;
                        $valor_cancelado = round($valor_cancelado, 2);
                        $sum_pagar   += $valor_aplicado;
                        //dd($valor_aplicado,$valor_cancelado, $v_decimo_t);


                        if ($ct_tipo_pago->tipo == 'ACREDITACION') { //debito
                            Ct_Rh_Acumulados_Detalle::create([
                                'id_ct_rh_acumulados'        => $value,
                                'valor'                      => $valor_aplicado,
                                'tipo'                       => 'PAGO A EMPLEADO',
                                'estado'                     => 1,

                                'id_asiento_pago'        => $id_asiento_cabecera,
                                'id_comprobante'         => $id_nota,
                                'tipo_comprobante'       => 'ACREDITACION',
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                            ]);
                        } else {
                            Ct_Rh_Acumulados_Detalle::create([
                                'id_ct_rh_acumulados'        => $value,
                                'valor'                      => $valor_aplicado,
                                'tipo'                       => 'PAGO A EMPLEADO',
                                'estado'                     => 1,

                                'id_asiento_pago'        => $id_asiento_cabecera,
                                'id_comprobante'         => $id_comprobante,
                                'tipo_comprobante'       => 'COMPROBANTE DE EGRESO',
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                            ]);
                        }


                        $arr_valor = [
                            'valor_pago'       => $valor_cancelado,
                        ];
                        $v_decimo_t->update($arr_valor);
                    }
                }
            }
            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'line' => $e->getLine(), 'titulos' => 'Error'];
        }
    }

    public function detalle_pago_decimo_tercer($id_ct_rh_acumulado, Request $request)
    {
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        //dd($ct_rh_acumulado);
        $nomina = Ct_Nomina::find($ct_rh_acumulado->id_nomina);
        $detalles = Ct_Rh_Acumulados_Detalle::where('id_ct_rh_acumulados', $id_ct_rh_acumulado)->where('estado', 1)->get();
        //dd($detalles);

        return view('contable.nuevo_rol_pago.decimo_tercer.detalle_pago_decimo_tercer', ['nomina' => $nomina, 'detalles' => $detalles]);
    }
    public function detalle_prestamos_empleado_decimo_tercer($id_ct_rh_acumulado)
    {

        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        //dd($ct_rh_acumulado);
        $valor_por_cruzar = $ct_rh_acumulado->valor - $ct_rh_acumulado->valor_pago;
        //dd($valor_por_cruzar);
        if ($valor_por_cruzar == 0) {
            return "
            <center>
            <div class='col-md-12'>
                <br> <br> <br> <br> <br> <br> <br>
                <spam class='color_rojo'> EL EMPLEADO NO TIENE DISPONIBLE VALORES PARA CRUZAR </spam>
            </div>
            </center>
            ";
        }
        $nomina = Ct_Nomina::find($ct_rh_acumulado->id_nomina);
        $prestamos = Ct_Rh_Prestamos::where('id_empl', $nomina->id_user)->where('estado', '1')->where('prest_cobrad', '0')->where('id_empresa', $nomina->id_empresa)->get();
        //dd($prestamos);
        if (count($prestamos) == 0) {
            return "
            <center>
            <div class='col-md-12'>
                <br> <br> <br> <br> <br> <br> <br>
                <spam class='color_rojo'> EL EMPLEADO NO TIENE PRESTAMOS </spam>
            </div>
            </center>
            ";
        }
        return view('contable.nuevo_rol_pago.decimo_tercer.listado_prestamos_empleado_dtercer', ['prestamos' => $prestamos, 'nomina' => $nomina, 'ct_rh_acumulado' => $ct_rh_acumulado]);
    }
    public function cruce_valor_prestamos_decimo_tercer(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($id_empresa);
        $id_ct_rh_acumulado = $request->id_ct_rh_acum;
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        $sucursal     = $ct_rh_acumulado->obtiene_sucursal;

        //dd($ct_rh_acumulado);    
        $Prestamos = $request->Prestamos;
        //$valores_pagos  = $request->valor_pago;
        //dd($Prestamos);
        //dd($request->DecimosCuarto);
        if (!is_null($Prestamos)) {
            if (count($Prestamos) < 1) {
                return ['respuesta' => 'error', 'msj' => 'SELECCIONE AL MENOS UN PRESTAMO PARA GENERAR', 'titulos' => 'Error'];
            }
        }

        $verifica_cierre = LogAsiento::verificar_cierre_anio($request["fecha_creacion" . $id_ct_rh_acumulado]);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }

        //dd($ValorVacaciones);

        DB::beginTransaction();
        try {
            $sum_pagar = 0;
            $valor_aplicado = 0;
            //dd($ValorVacaciones);
            //dd($request->all());

            $anio_cruce = date("Y");
            $mes_cruce = date("m");

            $valor_prest_cruzar = 0;
            $valor_disponible = 0;
            $valor_prest_cruzar = $ct_rh_acumulado->valor - $ct_rh_acumulado->valor_pago;

            foreach ($Prestamos as $value) {
                //dd($value);
                //$v_vacacion  = Ct_Rh_Acumulados::find($value);
                $prestamo = Ct_Rh_Prestamos::find($value);
                //dd($prestamo);

                if (!is_null($prestamo)) {
                    $valor_aplicado = $request->valor_pago_prest[$value];
                    $sum_pagar   += $valor_aplicado;
                    if ($sum_pagar > $valor_prest_cruzar) {

                        return ['respuesta' => 'error', 'msj' => 'EL VALOR A CRUZAR ES MAYOR AL SALDO DISPONIBLE', 'titulos' => 'ERROR!'];
                    }
                }
            }
            //dd($sum_pagar);

            $concepto  = 'CRUCE DE PRESTAMOS CON DECIMO TERCERO';
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request["fecha_creacion" . $id_ct_rh_acumulado],
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'CRUCE DECIMO TERCERO EMPLEADOS',
                'sucursal'        => $sucursal->codigo_sucursal,

            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            if ($sum_pagar > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.04.04.04
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOTERCER_SUELDO_(2)');
                //dd($plan_cuentas->nombre_mostrar);

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request["fecha_creacion" . $id_ct_rh_acumulado],
                    'debe'                => $sum_pagar,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                //$plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->cuenta_saliente)->orwhere('plan', $request->cuenta_saliente)->first();
                //$plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);
                $plan_cuentas2 = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');
                //dd($plan_cuentas2->cuenta_saliente);
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas2->cuenta_guardar,
                    'descripcion'         => $plan_cuentas2->nombre_mostrar,
                    'fecha'               => $request["fecha_creacion" . $id_ct_rh_acumulado],
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            $valor_cancelado = 0;
            foreach ($Prestamos as $value) {
                //dd($value);
                $prestamo = Ct_Rh_Prestamos::find($value);
                //dd($v_vacacion);

                if (!is_null($prestamo)) {
                    $valor_aplicado = $request->valor_pago_prest[$value];
                    $valor_cancelado = $ct_rh_acumulado->valor_pago + $valor_aplicado;
                    $sum_pagar   += $valor_aplicado;

                    $cuotas = $prestamo->detalles()->where('estado', 1)->where('estado_pago', 1)->count(); //dd($cuotas);
                    $xanio   = $prestamo->anio_inicio_cobro;
                    $xmes    = $prestamo->mes_inicio_cobro;
                    $xmes    = $xmes + $cuotas;
                    if ($xmes > 12) {
                        $xmes = 1;
                        $xanio++;
                    }
                    $cuotas++;

                    $input_det_acum = [
                        'id_ct_rh_acumulados'        => $id_ct_rh_acumulado,
                        'valor'                      => $valor_aplicado,
                        'tipo'                       => 'CRUCE PRESTAMOS',
                        'estado'                     => 1,
                        'tipo_comprobante'           => $prestamo->concepto,

                        'id_asiento_pago'        => $id_asiento_cabecera,

                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ];

                    $id_det_acum = Ct_Rh_Acumulados_Detalle::insertGetId($input_det_acum);

                    Ct_Rh_Prestamos_Detalle::create([
                        'id_ct_rh_prestamos'        => $value,
                        'anio'                       => $anio_cruce,
                        'mes'                        => $mes_cruce,
                        'fecha'                      => date('Y-m-d H:i:s'),
                        'cuota'                      => $cuotas,
                        'valor_cuota'                => $valor_aplicado,
                        'estado'                     => 1,
                        'estado_pago'                => 1,
                        'fecha_pago'                 => date('Y-m-d H:i:s'),
                        'observacion'                => 'Pago desde el modulo cruce de decimo tercero',
                        'id_ct_rh_acum_det'          => $id_det_acum,

                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ]);

                    $arr_valor = [
                        'saldo_total'       => $prestamo->saldo_total - $valor_aplicado,
                    ];
                    $prestamo->update($arr_valor);


                    $arr_valor1 = [
                        'valor_pago'       => $valor_cancelado,
                    ];
                    $ct_rh_acumulado->update($arr_valor1);
                }
            }




            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }
    public function imprimir_pago_decimo_tecer($id_ct_rh_acumulado)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;

        $ct_rh_acum = Ct_Rh_Acumulados::findorfail($id_ct_rh_acumulado);

        if (($rolUsuario != 1) && ($rolUsuario != 20) && ($rolUsuario != 21) && ($rolUsuario != 22)) {
            if ($ct_rh_acum->id_user != Auth::user()->id) {
                return response()->view('errors.404');
            }
        }

        /*$lista_cuota_quirog = Ct_Rh_Cuotas_Hipotecarios::where('id_rol', $id_rolpag)->get();
        $lista_cuota_hip    = Ct_Rh_Cuotas_Quirografario::where('id_rol', $id_rolpag)->get();*/

        $registro = Ct_Nomina::where('id', $ct_rh_acum->id_nomina)->first();
        $empresa  = Empresa::where('id', $registro->id_empresa)->first();
        $usuario  = User::where('id', $registro->id_user)->first();

        /* $rol_forma_pago = Ct_Rol_Forma_Pago::where('id_rol_pago', $rol_pago->id)->first();*/

        $detalles_acum = Ct_Rh_Acumulados_Detalle::where('id_ct_rh_acumulados', $ct_rh_acum->id)->where('estado', 1)->get();

        /*$ct_for_pag = Ct_Rol_Forma_Pago::where('id_rol_pago', $rol_pago->id)->get(); */
        $vistaurl = "contable.nuevo_rol_pago.vacaciones.pdf_pago_vacaciones";

        $view     = \View::make($vistaurl, compact('ct_rh_acum', 'registro', 'empresa', 'usuario', 'detalles_acum'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('Comprobante Rol Pago-' . $id_ct_rh_acumulado . '.pdf');
    }
    public function index_pago_manual_decimo_tercer($id_ct_rh_acumulado, Request $request)
    {
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        //dd($ct_rh_acumulado);
        $nomina = Ct_Nomina::find($ct_rh_acumulado->id_nomina);
        //$detalles = Ct_Rh_Acumulados_Detalle::where('id_ct_rh_acumulados', $id_ct_rh_acumulado)->where('estado', '1')->get();
        //dd($detalles);

        return view('contable.nuevo_rol_pago.decimo_tercer.pago_manual_decimo_tercer', ['nomina' => $nomina, 'ct_rh_acumulado' => $ct_rh_acumulado]);
    }
    public function pago_manual_decimo_tercer(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($id_empresa);
        $id_ct_rh_acumulado = $request->id_ct_rh_acum;
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        $sucursal     = $ct_rh_acumulado->obtiene_sucursal;


        DB::beginTransaction();
        try {
            $valor_aplicado = 0;
            //dd($ValorVacaciones);
            //dd($request->all());

            $valor_prest_cruzar = 0;
            $valor_disponible = 0;
            $valor_cancelado = 0;
            $valor_prest_cruzar = $ct_rh_acumulado->valor - $ct_rh_acumulado->valor_pago;
            $valor_prest_cruzar = round($valor_prest_cruzar, 2);

            $valor_aplicado = floatval($request->valor_pago_manual);
            $valor_cancelado = $ct_rh_acumulado->valor_pago + $valor_aplicado;
            $valor_cancelado = round($valor_cancelado, 2);


            if ($valor_aplicado > $valor_prest_cruzar) {

                return ['respuesta' => 'error', 'msj' => 'EL VALOR A CRUZAR ES MAYOR AL SALDO DISPONIBLE', 'titulos' => 'ERROR!'];
            }
            if($id_usuario == '0922290697')
            {
                //dd($valor_aplicado,$valor_prest_cruzar);
            }


            $input_det_acum = [
                'id_ct_rh_acumulados'        => $id_ct_rh_acumulado,
                'valor'                      => $valor_aplicado,
                'tipo'                       => 'PAGO MANUAL',
                'estado'                     => 1,
                'tipo_comprobante'           => $request['observacion_pago'],

                //'id_asiento_pago'        => $id_asiento_cabecera,

                'id_usuariocrea'    => $id_usuario,
                'id_usuariomod'     => $id_usuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            $id_det_acum = Ct_Rh_Acumulados_Detalle::insertGetId($input_det_acum);


            $arr_valor = [
                'valor_pago'       => $valor_cancelado,
            ];
            $ct_rh_acumulado->update($arr_valor);


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }
    public function recalcular_decimo_tercer($tipo, $anio)
    {
        $d_tercer = Ct_Rh_Acumulados::where('tipo', $tipo)->where('anio', $anio)->get();
        foreach ($d_tercer as $value) {
            $detalles = $value->obtiene_detalles->where('estado', '1');
            $acum = 0;
            foreach ($detalles as $det) {
                $acum += $det->valor;
            }
            $arr_valor_act = [
                'valor_pago'       => $acum,
            ];
            $value->update($arr_valor_act);
        }
        return "oki";
    }

    public function index_pago_manual_decimo_cuarto($id_ct_rh_acumulado, Request $request)
    {
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        //dd($ct_rh_acumulado);
        $nomina = Ct_Nomina::find($ct_rh_acumulado->id_nomina);
        //$detalles = Ct_Rh_Acumulados_Detalle::where('id_ct_rh_acumulados', $id_ct_rh_acumulado)->where('estado', '1')->get();
        //dd($detalles);

        return view('contable.nuevo_rol_pago.decimo_cuarto.pago_manual_decimo_cuarto', ['nomina' => $nomina, 'ct_rh_acumulado' => $ct_rh_acumulado]);
    }
    public function pago_manual_decimo_cuarto(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($id_empresa);
        $id_ct_rh_acumulado = $request->id_ct_rh_acum;
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        $sucursal     = $ct_rh_acumulado->obtiene_sucursal;


        DB::beginTransaction();
        try {
            $valor_aplicado = 0;
            //dd($ValorVacaciones);
            //dd($request->all());

            $valor_prest_cruzar = 0;
            $valor_disponible = 0;
            $valor_cancelado = 0;
            $valor_prest_cruzar = $ct_rh_acumulado->valor - $ct_rh_acumulado->valor_pago;


            $valor_aplicado = $request->valor_pago_manual;
            $valor_cancelado = $ct_rh_acumulado->valor_pago + $valor_aplicado;

            if ($valor_aplicado > $valor_prest_cruzar) {

                return ['respuesta' => 'error', 'msj' => 'EL VALOR A CRUZAR ES MAYOR AL SALDO DISPONIBLE', 'titulos' => 'ERROR!'];
            }

            $input_det_acum = [
                'id_ct_rh_acumulados'        => $id_ct_rh_acumulado,
                'valor'                      => $valor_aplicado,
                'tipo'                       => 'PAGO MANUAL',
                'estado'                     => 1,
                'tipo_comprobante'           => $request['observacion_pago'],

                //'id_asiento_pago'        => $id_asiento_cabecera,

                'id_usuariocrea'    => $id_usuario,
                'id_usuariomod'     => $id_usuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
            ];

            $id_det_acum = Ct_Rh_Acumulados_Detalle::insertGetId($input_det_acum);


            $arr_valor = [
                'valor_pago'       => $valor_cancelado,
            ];
            $ct_rh_acumulado->update($arr_valor);


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }
    public function detalle_pago_decimo_cuarto($id_ct_rh_acumulado, Request $request)
    {
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        //dd($ct_rh_acumulado);
        $nomina = Ct_Nomina::find($ct_rh_acumulado->id_nomina);
        $detalles = Ct_Rh_Acumulados_Detalle::where('id_ct_rh_acumulados', $id_ct_rh_acumulado)->where('estado', 1)->get();
        //dd($detalles);

        return view('contable.nuevo_rol_pago.decimo_cuarto.detalle_pago_decimo_cuarto', ['nomina' => $nomina, 'detalles' => $detalles]);
    }
    public function detalle_prestamos_empleado_decimo_cuarto($id_ct_rh_acumulado)
    {
        //dd("aqui");
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        //dd($ct_rh_acumulado);
        $valor_por_cruzar = $ct_rh_acumulado->valor - $ct_rh_acumulado->valor_pago;
        //dd($valor_por_cruzar);
        if ($valor_por_cruzar == 0) {
            return "
            <center>
            <div class='col-md-12'>
                <br> <br> <br> <br> <br> <br> <br>
                <spam class='color_rojo'> EL EMPLEADO NO TIENE DISPONIBLE VALORES PARA CRUZAR </spam>
            </div>
            </center>
            ";
        }
        $nomina = Ct_Nomina::find($ct_rh_acumulado->id_nomina);
        $prestamos = Ct_Rh_Prestamos::where('id_empl', $nomina->id_user)->where('estado', '1')->where('prest_cobrad', '0')->where('id_empresa', $nomina->id_empresa)->get();
        //dd($prestamos);
        if (count($prestamos) == 0) {
            return "
            <center>
            <div class='col-md-12'>
                <br> <br> <br> <br> <br> <br> <br>
                <spam class='color_rojo'> EL EMPLEADO NO TIENE PRESTAMOS </spam>
            </div>
            </center>
            ";
        }
        return view('contable.nuevo_rol_pago.decimo_cuarto.listado_prestamos_empleado_cuarto', ['prestamos' => $prestamos, 'nomina' => $nomina, 'ct_rh_acumulado' => $ct_rh_acumulado]);
    }
    public function cruce_valor_prestamos_decimo_cuarto(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($id_empresa);
        $id_ct_rh_acumulado = $request->id_ct_rh_acum;
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        $sucursal     = $ct_rh_acumulado->obtiene_sucursal;

        //dd($ct_rh_acumulado);    
        $Prestamos = $request->Prestamos;
        //$valores_pagos  = $request->valor_pago;
        //dd($Prestamos);
        //dd($request->DecimosCuarto);
        if (!is_null($Prestamos)) {
            if (count($Prestamos) < 1) {
                return ['respuesta' => 'error', 'msj' => 'SELECCIONE AL MENOS UN PRESTAMO PARA GENERAR', 'titulos' => 'Error'];
            }
        }

        $verifica_cierre = LogAsiento::verificar_cierre_anio($request["fecha_creacion" . $id_ct_rh_acumulado]);
        if ($verifica_cierre['status'] == 'error') {
            return ['respuesta' => $verifica_cierre['status'], 'msj' => $verifica_cierre['msj']];
        }

        //dd($ValorVacaciones);

        DB::beginTransaction();
        try {
            $sum_pagar = 0;
            $valor_aplicado = 0;
            //dd($ValorVacaciones);
            //dd($request->all());

            $anio_cruce = date("Y");
            $mes_cruce = date("m");

            $valor_prest_cruzar = 0;
            $valor_disponible = 0;
            $valor_prest_cruzar = $ct_rh_acumulado->valor - $ct_rh_acumulado->valor_pago;

            foreach ($Prestamos as $value) {
                //dd($value);
                //$v_vacacion  = Ct_Rh_Acumulados::find($value);
                $prestamo = Ct_Rh_Prestamos::find($value);
                //dd($prestamo);

                if (!is_null($prestamo)) {
                    $valor_aplicado = $request->valor_pago_prest[$value];
                    $sum_pagar   += $valor_aplicado;
                    if ($sum_pagar > $valor_prest_cruzar) {

                        return ['respuesta' => 'error', 'msj' => 'EL VALOR A CRUZAR ES MAYOR AL SALDO DISPONIBLE', 'titulos' => 'ERROR!'];
                    }
                }
            }
            //dd($sum_pagar);
            $empleado = $ct_rh_acumulado->ct_nomina->user->apellido1 . ' ' . $ct_rh_acumulado->ct_nomina->user->apellido2 . ' ' . $ct_rh_acumulado->ct_nomina->user->nombre1 . ' ' . $ct_rh_acumulado->ct_nomina->user->nombre2;
            $concepto  = 'CRUCE DE PRESTAMOS CON DECIMO CUARTO AL EMPLEADO:  ' . $empleado;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request["fecha_creacion" . $id_ct_rh_acumulado],
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'CRUCE DECIMO CUARTO EMPLEADOS',
                'sucursal'        => $sucursal->codigo_sucursal,

            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            if ($sum_pagar > 0) {

                //$plan_cuentas = Plan_Cuentas::where('id', $cuenta_destino)->first();2.01.04.04.03
                $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('ROL_PAGOS_DECIMOCUARTO_SUELDO_(2)');
                //dd($plan_cuentas->nombre_mostrar);

                Ct_Asientos_Detalle::create([ //////DEBE

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
                    'descripcion'         => $plan_cuentas->nombre_mostrar,
                    'fecha'               => $request["fecha_creacion" . $id_ct_rh_acumulado],
                    'debe'                => $sum_pagar,
                    'haber'               => 0,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);

                //$plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->cuenta_saliente)->orwhere('plan', $request->cuenta_saliente)->first();
                //$plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);
                $plan_cuentas2 = \Sis_medico\Ct_Configuraciones::obtener_cuenta('PRESTAMOS_EMPLEADOS');
                //dd($plan_cuentas2->cuenta_saliente);
                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $plan_cuentas2->cuenta_guardar,
                    'descripcion'         => $plan_cuentas2->nombre_mostrar,
                    'fecha'               => $request["fecha_creacion" . $id_ct_rh_acumulado],
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }

            $valor_cancelado = 0;
            foreach ($Prestamos as $value) {
                //dd($value);
                $prestamo = Ct_Rh_Prestamos::find($value);
                //dd($v_vacacion);

                if (!is_null($prestamo)) {
                    $valor_aplicado = $request->valor_pago_prest[$value];
                    $valor_cancelado = $ct_rh_acumulado->valor_pago + $valor_aplicado;
                    $sum_pagar   += $valor_aplicado;

                    $cuotas = $prestamo->detalles()->where('estado', 1)->where('estado_pago', 1)->count(); //dd($cuotas);
                    $xanio   = $prestamo->anio_inicio_cobro;
                    $xmes    = $prestamo->mes_inicio_cobro;
                    $xmes    = $xmes + $cuotas;
                    if ($xmes > 12) {
                        $xmes = 1;
                        $xanio++;
                    }
                    $cuotas++;

                    $input_det_acum = [
                        'id_ct_rh_acumulados'        => $id_ct_rh_acumulado,
                        'valor'                      => $valor_aplicado,
                        'tipo'                       => 'CRUCE PRESTAMOS',
                        'estado'                     => 1,
                        'tipo_comprobante'           => $prestamo->concepto,

                        'id_asiento_pago'        => $id_asiento_cabecera,

                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ];

                    $id_det_acum = Ct_Rh_Acumulados_Detalle::insertGetId($input_det_acum);

                    Ct_Rh_Prestamos_Detalle::create([
                        'id_ct_rh_prestamos'        => $value,
                        'anio'                       => $anio_cruce,
                        'mes'                        => $mes_cruce,
                        'fecha'                      => date('Y-m-d H:i:s'),
                        'cuota'                      => $cuotas,
                        'valor_cuota'                => $valor_aplicado,
                        'estado'                     => 1,
                        'estado_pago'                => 1,
                        'fecha_pago'                 => date('Y-m-d H:i:s'),
                        'observacion'                => 'Pago desde el modulo cruce de decimo cuarto',
                        'id_ct_rh_acum_det'          => $id_det_acum,

                        'id_usuariocrea'    => $id_usuario,
                        'id_usuariomod'     => $id_usuario,
                        'ip_creacion'       => $ip_cliente,
                        'ip_modificacion'   => $ip_cliente,
                    ]);


                    $arr_valor = [
                        'saldo_total'       => $prestamo->saldo_total - $valor_aplicado,
                    ];
                    $prestamo->update($arr_valor);

                    if ($prestamo->saldo_total <= 0) {
                        $prestamo->update([
                            'prest_cobrad' => '1',
                        ]);
                    }


                    $arr_valor = [
                        'valor_pago'       => $valor_cancelado,
                    ];
                    $ct_rh_acumulado->update($arr_valor);
                }
            }




            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function subida_iess_horas_extras(Request $request)
    {

        $anio       = $request->year;
        $mes        = $request->mes;
        $id_empresa = $request->id_empresa;
        $id_sucursal = $request->sucursal;

        $roles = Ct_Rol_Pagos::join('ct_nomina as n', 'n.id', 'ct_rol_pagos.id_nomina')
            ->join('users as u', 'ct_rol_pagos.id_user', 'u.id')
            ->where('ct_rol_pagos.id_empresa', $id_empresa)
            ->where('ct_rol_pagos.anio', $anio)
            ->where('ct_rol_pagos.mes', $mes)
            ->where('n.id_sucursal', $id_sucursal)
            ->orderby('u.apellido1')
            ->select('ct_rol_pagos.id_empresa', 'ct_rol_pagos.anio', 'ct_rol_pagos.mes', 'n.id_user', 'ct_rol_pagos.id')
            ->get();

        $contenido = '';

        $isuc = '0001';

        if ($id_empresa == '0993075000001') {

            if ($id_sucursal == '23') {
                $isuc = '0003';
            }
            if ($id_sucursal == '24') {
                $isuc = '0004';
            }
            if ($id_sucursal == '25') {
                $isuc = '0006';
            }
            if ($id_sucursal == '30') {
                $isuc = '0008';
            }
        }

        foreach ($roles as $rol) {

            $total = 0;
            $detalle = Ct_Detalle_Rol::where('id_rol', $rol->id)->where('estado', '1')->first();
            if (!is_null($detalle)) {
                if ($detalle->sobre_tiempo50 > 0 or $detalle->sobre_tiempo100 > 0) {
                    $sobre_tiempo50 = $detalle->sobre_tiempo50;
                    $sobre_tiempo100 = $detalle->sobre_tiempo100;
                    $total = $sobre_tiempo50 + $sobre_tiempo100;




                    $contenido = $contenido . $rol->id_empresa . ";";
                    $contenido = $contenido . $isuc . ";";
                    $contenido = $contenido . $rol->anio . ";";
                    $contenido = $contenido . $rol->mes . ";";
                    $contenido = $contenido . "INS;";
                    $contenido = $contenido . $rol->id_user . ";";
                    $contenido = $contenido . $total . ";O\n";
                }
            }
        }

        // Nombre del archivo
        $nombreArchivo = 'archivo_generado.txt';

        // Crear la respuesta con el contenido del archivo y el encabezado adecuado
        $response = Response::make($contenido);
        $response->header('Content-Type', 'text/plain');
        $response->header('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');


        return $response;
    }

    public function total_excel_prestamos_saldos(Request $request)
    {
        $id_empresa     = $request->session()->get('id_empresa');
        $empleados = Ct_Nomina::where('id_empresa', $id_empresa)->get();

        $titulos = array("FECHA CREACION", "CONCEPTO", "NUMERO CUOTA", "VALOR CUOTA", "MES/AÑO INICIO COBRO", "MES/AÑO FIN COBRO", "MONTO PRESTAMO", "VALOR A PAGAR", "ESTADO");
        $titulos2 = array("FECHA CREACION", "CONCEPTO", "NUMERO CUOTA", "VALOR CUOTA", "MES/AÑO INICIO COBRO", "MES/AÑO FIN COBRO", "SALDO INICIAL", "VALOR A PAGAR", "ESTADO");
        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I");

        Excel::create('Reporte_', function ($excel) use ($titulos, $posicion, $empleados, $titulos2, $id_empresa) {
            $excel->sheet('Reporte_', function ($sheet) use ($titulos, $posicion, $empleados, $titulos2, $id_empresa) {
                $i = 0;
                foreach ($empleados as $empleado) {

                    $user = User::find($empleado->id_user);
                    $prestamos = Ct_Rh_Prestamos::where('id_empl', $empleado->id_user)->where('id_empresa', $id_empresa)->get();
                    $saldos = Ct_Rh_Saldos_Iniciales::where('id_empl', $empleado->id_user)->where('id_empresa', $id_empresa)->get();
                    $i++;

                    if (count($prestamos) > 0) {
                        $sheet->cell('A' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('PRESTAMOS - SALDOS DETALLE');
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('right');
                        });
                        $i++;

                        $sheet->cell('A' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CEDULA');
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                        });


                        $sheet->cell('B' . $i, function ($cell) use ($user) {
                            // manipulate the cel
                            $cell->setValue($user->id);
                            $cell->setAlignment('right');
                        });

                        $sheet->cell('C' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('NOMBRES');
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                        });


                        $sheet->mergeCells('D' . $i . ':E' . $i);
                        $sheet->cell('D' . $i, function ($cell) use ($user) {
                            // manipulate the cel
                            $cell->setValue($user->apellido1 . ' ' . $user->apellido2 . ' ' . $user->nombre1 . ' ' . $user->nombre2);
                            $cell->setAlignment('right');
                        });
                        $i++;

                        $sheet->cell('A' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('PRESTAMOS');
                            $cell->setAlignment('center');
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                        });

                        $i++;
                        /****************TITULOS DEL EXCEL*********************/
                        //crear los titulos en el excel
                        for ($j = 0; $j < count($titulos); $j++) {
                            $sheet->cell('' . $posicion[$j] . '' . $i, function ($cell) use ($titulos, $j) {
                                $cell->setValue($titulos[$j]);
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#CCE1A7');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        $i++;
                        /*****FIN DE TITULOS DEL EXCEL***********/

                        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                        $titulo_detallep = array("AÑO", "MES", "CUOTA", "VALOR CUOTA");
                        $acum_prestamos = 0;
                        foreach ($prestamos as $prestamo) {

                            $detalle_prestamo = Ct_Rh_Prestamos_Detalle::where('id_ct_rh_prestamos', $prestamo->id)->get();
                            $datos_excel = array();

                            if ($prestamo->estado) {
                                $acum_prestamos += $prestamo->saldo_total;
                                if ($prestamo->prest_cobrad) {
                                    $estado = 'PAGADO';
                                } else {
                                    $estado = 'Activo';
                                }
                            } else {
                                $estado = 'Inactivo';
                            }


                            $ms = intval($prestamo->mes_inicio_cobro) - 1;
                            $m = intval($prestamo->mes_fin_cobro) - 1;

                            array_push($datos_excel, $prestamo->fecha_creacion, $prestamo->concepto, $prestamo->num_cuotas, $prestamo->valor_cuota, $meses[$ms] . '-' . $prestamo->anio_inicio_cobro, $meses[$m] . '-' . $prestamo->anio_fin_cobro, $prestamo->monto_prestamo, $prestamo->saldo_total, $estado);

                            for ($j = 0; $j < count($datos_excel); $j++) {
                                $sheet->cell('' . $posicion[$j] . '' . $i, function ($cell) use ($datos_excel, $j) {
                                    $cell->setValue($datos_excel[$j]);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                                $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            }

                            $i++;

                            if ($detalle_prestamo->count() > 0) {
                                for ($j = 0; $j < count($titulo_detallep); $j++) {
                                    $sheet->cell('' . $posicion[$j] . '' . $i, function ($cell) use ($titulo_detallep, $j) {
                                        $cell->setValue($titulo_detallep[$j]);
                                        $cell->setFontWeight('bold');
                                        $cell->setAlignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                }
                                $i++;

                                $tot_det_prest = 0;
                                foreach ($detalle_prestamo as $det_prest) {

                                    $datos_detalle = array();
                                    $mesd = intval($det_prest->mes) - 1;
                                    $tot_det_prest += $det_prest->valor_cuota;

                                    array_push($datos_detalle, $det_prest->anio, $meses[$mesd], $det_prest->cuota, $det_prest->valor_cuota);

                                    for ($j = 0; $j < count($datos_detalle); $j++) {
                                        $sheet->cell('' . $posicion[$j] . '' . $i, function ($cell) use ($datos_detalle, $j) {
                                            $cell->setValue($datos_detalle[$j]);
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                            $cell->setAlignment('center');
                                        });
                                        $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                    }
                                    $i++;
                                }
                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('TOTAL');
                                    $cell->setAlignment('center');
                                    $cell->setFontWeight('bold');
                                });

                                $sheet->cell('D' . $i, function ($cell) use ($tot_det_prest) {
                                    // manipulate the cel
                                    $cell->setValue($tot_det_prest);
                                    $cell->setAlignment('center');
                                });
                                $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                $i++;
                            }
                        }

                        $sheet->cell('G' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TOTAL PRESTAMOS');
                            $cell->setAlignment('center');
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('H' . $i, function ($cell) use ($acum_prestamos) {
                            // manipulate the cel
                            $cell->setValue('$ ' . $acum_prestamos);
                            $cell->setAlignment('center');
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                        });

                        $i++;
                    }

                    if (count($saldos) > 0) {
                        $sheet->cell('A' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('SALDOS INICIALES');
                            $cell->setAlignment('center');
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                        });
                        $i++;


                        for ($j = 0; $j < count($titulos2); $j++) {
                            $sheet->cell('' . $posicion[$j] . '' . $i, function ($cell) use ($titulos2, $j) {
                                $cell->setValue($titulos2[$j]);
                                $cell->setFontWeight('bold');
                                $cell->setBackground('#CCE1A7');
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        $i++;

                        $acum_saldo = 0;
                        foreach ($saldos as $saldo) {

                            $detalle_saldos = Ct_Rh_Saldos_Iniciales_Detalle::where('id_ct_rh_saldos_iniciales', $saldo->id)->get();
                            $datos_saldo = array();

                            if ($saldo->estado) {
                                $acum_saldo += $saldo->saldo_res;
                                if ($saldo->saldo_cobrad) {
                                    $estado = 'PAGADO';
                                } else {
                                    $estado = 'Activo';
                                }
                            } else {
                                $estado = 'Inactivo';
                            }


                            $msi = intval($saldo->mes_inicio_cobro) - 1;
                            $mi = intval($saldo->mes_fin_cobro) - 1;


                            array_push($datos_saldo, $saldo->fecha_creacion, $saldo->observacion, $saldo->num_cuotas, $saldo->valor_cuota, $meses[$msi] . '-' . $saldo->anio_inicio_cobro, $meses[$mi] . '-' . $saldo->anio_fin_cobro, $saldo->saldo_inicial, $saldo->saldo_res, $estado);



                            for ($j = 0; $j < count($datos_saldo); $j++) {
                                $sheet->cell('' . $posicion[$j] . '' . $i, function ($cell) use ($datos_saldo, $j) {
                                    $cell->setValue($datos_saldo[$j]);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                                $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                $sheet->getStyle('G' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            }
                            $i++;
                            if ($detalle_saldos->count() > 0) {
                                for ($j = 0; $j < count($titulo_detallep); $j++) {
                                    $sheet->cell('' . $posicion[$j] . '' . $i, function ($cell) use ($titulo_detallep, $j) {
                                        $cell->setValue($titulo_detallep[$j]);
                                        $cell->setFontWeight('bold');
                                        $cell->setAlignment('center');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    });
                                }
                                $i++;

                                $tot_det_saldo = 0;
                                foreach ($detalle_saldos as $det_saldo) {

                                    $datos_detalle_s = array();
                                    $tot_det_saldo += $det_saldo->valor_cuota;
                                    $mss = intval($det_saldo->mes) - 1;

                                    array_push($datos_detalle_s, $det_saldo->anio, $meses[$mss], $det_saldo->cuota, $det_saldo->valor_cuota);

                                    for ($j = 0; $j < count($datos_detalle_s); $j++) {
                                        $sheet->cell('' . $posicion[$j] . '' . $i, function ($cell) use ($datos_detalle_s, $j) {
                                            $cell->setValue($datos_detalle_s[$j]);
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                            $cell->setAlignment('center');
                                        });
                                        $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                                    }
                                    $i++;
                                }
                                $sheet->cell('C' . $i, function ($cell) {
                                    // manipulate the cel
                                    $cell->setValue('TOTAL');
                                    $cell->setAlignment('center');
                                    $cell->setFontWeight('bold');
                                });

                                $sheet->cell('D' . $i, function ($cell) use ($tot_det_saldo) {
                                    // manipulate the cel
                                    $cell->setValue($tot_det_saldo);
                                    $cell->setAlignment('center');
                                });
                                $sheet->getStyle('D' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                            }
                            $i++;
                        }
                        $i++;
                        $sheet->cell('G' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TOTAL SALDOS');
                            $cell->setAlignment('center');
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('H' . $i, function ($cell) use ($acum_saldo) {
                            // manipulate the cel
                            $cell->setValue($acum_saldo);
                            $cell->setAlignment('center');
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                        });
                        $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                        $total_pago = $acum_saldo + $acum_prestamos;

                        $i++;
                        $sheet->cell('G' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('TOTAL A PAGAR');
                            $cell->setAlignment('center');
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                        });
                        $sheet->cell('H' . $i, function ($cell) use ($total_pago) {
                            // manipulate the cel
                            $cell->setValue($total_pago);
                            $cell->setAlignment('center');
                            $cell->setAlignment('right');
                            $cell->setFontWeight('bold');
                        });
                        $sheet->getStyle('H' . $i)->getNumberFormat()->setFormatCode('$ 0.00');
                    }
                }
            });
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(90)->setAutosize(false);
        })->export('xlsx');
    }
    public function ingreso_subrogacion_dcuarto($id_ct_rh_acumulado, Request $request)
    {
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        //dd($ct_rh_acumulado);
        $nomina = Ct_Nomina::find($ct_rh_acumulado->id_nomina);
        //$detalles = Ct_Rh_Acumulados_Detalle::where('id_ct_rh_acumulados', $id_ct_rh_acumulado)->where('estado', '1')->get();
        //dd($detalles);

        return view('contable.nuevo_rol_pago.decimo_cuarto.ingreso_subrogacion_decimo_cuarto', ['nomina' => $nomina, 'ct_rh_acumulado' => $ct_rh_acumulado]);
    }
    public function guarda_subrogacion_empleado(Request $request)
    {
        //dd($request->all());
        $valor_por_cruzar = 0;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($id_empresa);
        $id_ct_rh_acumulado = $request->id_ct_rh_acum;
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        $sucursal     = $ct_rh_acumulado->obtiene_sucursal;

        $valor_a_pagar = 0;
        $dias_subrogacion = 0;
        $valor_subrogacion = 0;

        DB::beginTransaction();
        try {

            //dd($ValorVacaciones);
            //dd($request->all());
            $valor_por_cruzar = $ct_rh_acumulado->valor;
            $valor_subrogacion = $request['valor_subrogacion'];
            $valor_subrogacion =  floatval($valor_subrogacion);
            $dias_laborados = $ct_rh_acumulado->dias_laborados;
            $dias_subrogacion =  $request['dias_subrogacion'];
            $dias_subrogacion =  intval($dias_subrogacion);
            //dd($dias_laborados, $dias_subrogacion);
            $total_dias_laborados = $dias_laborados + $dias_subrogacion;


            $valor_a_pagar = $valor_por_cruzar + $valor_subrogacion;

            $arr_valor = [
                'valor'                  => round($valor_a_pagar, 2),
                'dias_laborados'         => $total_dias_laborados,
                'valor_subrogacion'     => $request['valor_subrogacion'],
                'dias_subrogacion'      => $request['dias_subrogacion'],
                'observacion_subrogacion' => $request['observacion_pago'],

                'id_usuariomod'     => $id_usuario,
                'ip_modificacion'   => $ip_cliente,
            ];
            //dd($arr_valor);
            $ct_rh_acumulado->update($arr_valor);


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function ingreso_subrogacion_dtecero($id_ct_rh_acumulado, Request $request)
    {
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        //dd($ct_rh_acumulado);
        $nomina = Ct_Nomina::find($ct_rh_acumulado->id_nomina);
        //$detalles = Ct_Rh_Acumulados_Detalle::where('id_ct_rh_acumulados', $id_ct_rh_acumulado)->where('estado', '1')->get();
        //dd($detalles);

        return view('contable.nuevo_rol_pago.decimo_tercer.ingreso_subrogacion_decimo_tercero', ['nomina' => $nomina, 'ct_rh_acumulado' => $ct_rh_acumulado]);
    }
    public function guarda_subrogacion_empleado_dtecero(Request $request)
    {
        //dd($request->all());
        $valor_por_cruzar = 0;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        //dd($id_empresa);
        $id_ct_rh_acumulado = $request->id_ct_rh_acum;
        $ct_rh_acumulado = Ct_Rh_Acumulados::find($id_ct_rh_acumulado);
        $sucursal     = $ct_rh_acumulado->obtiene_sucursal;

        $valor_a_pagar = 0;
        $dias_subrogacion = 0;
        $valor_subrogacion = 0;

        DB::beginTransaction();
        try {

            //dd($ValorVacaciones);
            //dd($request->all());
            $valor_por_cruzar = $ct_rh_acumulado->valor;
            $valor_subrogacion = $request['valor_subrogacion'];
            $valor_subrogacion =  floatval($valor_subrogacion);
            $dias_laborados = $ct_rh_acumulado->dias_laborados;
            $dias_subrogacion =  $request['dias_subrogacion'];
            $dias_subrogacion =  intval($dias_subrogacion);
            //dd($dias_laborados, $dias_subrogacion);
            $total_dias_laborados = $dias_laborados + $dias_subrogacion;


            $valor_a_pagar = $valor_por_cruzar + $valor_subrogacion;

            $arr_valor = [
                'valor'                  => round($valor_a_pagar, 2),
                'dias_laborados'         => $total_dias_laborados,
                'valor_subrogacion'     => $request['valor_subrogacion'],
                'dias_subrogacion'      => $request['dias_subrogacion'],
                'observacion_subrogacion' => $request['observacion_pago'],

                'id_usuariomod'     => $id_usuario,
                'ip_modificacion'   => $ip_cliente,
            ];
            //dd($arr_valor);
            $ct_rh_acumulado->update($arr_valor);


            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error'];
        }
    }

    public function carga_detalle_rol($id){

        CtRhRolDetalle::crea_detalle_rol($id);


    }

    public function actualiza_detalle(Request $request){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;

        //dd($request->all());
        $id = $request->id;
        $valor = $request->valor;
        $observacion = $request->observacion;

        $detalle = CtRhRolDetalle::find($id);

        if(!is_null($detalle)){

            $detalle->update([
                'valor' => $valor,
                'observacion' => $observacion,
                'id_usuariomod'     => $id_usuario,
                'ip_modificacion'   => $ip_cliente,
            ]);

            $this->recalcular_db($detalle->id_rol);

            $detalle_rol = Ct_Detalle_Rol::where('id_rol',$detalle->id_rol)->where('estado','1')->first();

            if(!is_null($detalle_rol)){

                return ['msn' => 'ok', 'total_ingresos' => $detalle_rol->total_ingresos, 'total_egresos' => $detalle_rol->total_egresos, 'neto_recibido' => $detalle_rol->neto_recibido];

            } else{

                return ['msn' => 'error', 'total_ingresos' => 0, 'total_egresos' => 0, 'neto_recibido' => 0];

            }   

            
        }

        return ['msn' => 'error', 'total_ingresos' => 0, 'total_egresos' => 0, 'neto_recibido' => 0];

    }
    public function calculo_fondo_reserva($añosAhora,$nomina,$meses_totales,$dias_laborados,$base_iess,$base_nomina,$jornada,$vt_dia,$val_fond_reserv){
        $fondo_reserva_acumulado = 0;
        $fondo_reserva = 0;
        $tipo_fondo_reserva = $nomina->pago_fondo_reserva;
        $dias_nomina        = $nomina->dias_laborables;

        $pct_fondo_reserva   = $val_fond_reserv->valor != null ? $val_fond_reserv->valor : 0;

        if ($añosAhora > 0) {
            
            if ($tipo_fondo_reserva == 2) { //mensualiza

                if ($meses_totales == '12') {
                    $vt_dia = $dias_laborados - $vt_dia + 1; //30-19 SE LE SUMO UN DIA POR EL TEMA DE MARIA JOSE REVISAR CON IVANNA
                    $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                    ////////////////////////////
                    if ($jornada != 0) {
                        $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / $dias_nomina);
                    }
                    if ($jornada == 0 && $dias_laborados != 30) {
                        $fondo_reserva = $base_nomina * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                    }
                } else {
                    
                    $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100);
                    $fondo_reserva = round($fondo_reserva, 2);
                }
            }
            ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (2)///////////////////////////////////////
            else {
                $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100);
                $fondo_reserva_acumulado = round($fondo_reserva_acumulado, 2);
            }
            //////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            /*if($intervalMeses == '11' && $tipo_fondo_reserva == 2){
                $vt_dia = $dias_laborados - $vt_dia; 
                $fondo_reserva = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                //dd($fondo_reserva,$pct_fondo_reserva);
                //dd($fondo_reserva);
            }
            ///////////////////FONDO DE RESERVA ACUMULADO PARA ASIENTOS (4)///////////////////////////////////////
            else{
                if($intervalMeses == '11'){
                    $vt_dia = $dias_laborados - $vt_dia; 
                    $fondo_reserva_acumulado = $base_iess * ($pct_fondo_reserva / 100) * ($vt_dia / 30);
                    $fondo_reserva_acumulado = round($fondo_reserva_acumulado,2);
                }    
            }*/
            ////////////////////////////////////////////////////////////////////////////////////////////////////// 

        }
        $fondo_reserva = round($fondo_reserva, 2);
        return ['fondo_reserva' => $fondo_reserva, 'fondo_reserva_acumulado' => $fondo_reserva_acumulado];
    }
    public function index_log_nomina($id_rol)
    {

        $log_nomina = CtLogRolNomina::where('id_rol', $id_rol)->orderBy('id', 'desc')->get();

        return view('contable.nomina.index_log_nomina', ['log_nomina' => $log_nomina]);
    }
    public function actualiza_2_decimales_rol($id)
    {
        $rol_asiento = RolAsiento::find($id);
        $ct_rol = Ct_Rol_Pagos::where('mes', $rol_asiento->mes)->where('anio', $rol_asiento->anio)->where('estado', 1)->get();
        if (!is_null($ct_rol)) {
            foreach ($ct_rol as $value) {
                $detalles = $value->detalle;
                foreach ($detalles as $detalle) {
                    $arr_det = [
                        'porcentaje_iess'           => round($detalle->porcentaje_iess, 2),
                        'neto_recibido'             => round($detalle->neto_recibido, 2),
                        'total_egresos'              => round($detalle->total_egresos, 2),
                        'decimo_tercero_acumulado'  => round($detalle->decimo_tercero_acumulado, 2),
                        'decimo_cuarto_acumulado'   => round($detalle->decimo_cuarto_acumulado, 2),
                        'decimo_tercero'            => round($detalle->decimo_tercero, 2),
                        'decimo_cuarto'             => round($detalle->decimo_cuarto, 2),
                        'valor_vacacion'            => round($detalle->valor_vacacion, 2),
                        'dcuarto_acumulado_final'   => round($detalle->dcuarto_acumulado_final, 2),
                    ];
                    $detalle->update($arr_det);
                }
                $this->recalcular_db($value->id);
            }
        }
        return redirect(route('nuevo_rol.detalle_asientos_rol_sucursal', ['id' => $id]));
    }
    public function actualiza_2_decimales_rol_anio_mes($anio, $mes)
    {

        $ct_rol = Ct_Rol_Pagos::where('mes', $mes)->where('anio', $anio)->where('estado', 1)->get();
        if (!is_null($ct_rol)) {
            foreach ($ct_rol as $value) {
                $detalles = $value->detalle;
                foreach ($detalles as $detalle) {
                    $arr_det = [
                        'porcentaje_iess'           => round($detalle->porcentaje_iess, 2),
                        'neto_recibido'             => round($detalle->neto_recibido, 2),
                        'total_egresos'              => round($detalle->total_egresos, 2),
                        'decimo_tercero_acumulado'  => round($detalle->decimo_tercero_acumulado, 2),
                        'decimo_cuarto_acumulado'   => round($detalle->decimo_cuarto_acumulado, 2),
                        'decimo_tercero'            => round($detalle->decimo_tercero, 2),
                        'decimo_cuarto'             => round($detalle->decimo_cuarto, 2),
                        'valor_vacacion'            => round($detalle->valor_vacacion, 2),
                        'dcuarto_acumulado_final'   => round($detalle->dcuarto_acumulado_final, 2),
                    ];
                    $detalle->update($arr_det);
                }
                $this->recalcular_db($value->id);
            }
        }
        return "oki";
    }
    public function cargar_cuentas_pago_rubros($id)
    {
        $cuentas_rol = RolAsiento::find($id);
        $cuentas_iess2 = [];
        $rubros = [];
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        if ($cuentas_rol->id_asiento != null) {
            $cuentas_iess2 = $cuentas_rol->cuentas->where('doc_pendiente', 'IESS2');
            $rubros = $cuentas_rol->rubros;

            if (count($cuentas_iess2) > 0) {
                foreach ($cuentas_iess2 as $value) {
                    $rol_asiento_pago = RolAsientoPagoRubros::where('id_rol_asiento', $cuentas_rol->id)->where('id_plan_cuentas', $value->id_plan_cuentas)->where('estado', 1)->first();
                    if (is_null($rol_asiento_pago)) {
                        $arr_pago_rubros1 = [
                            'id_rol_asiento'    => $cuentas_rol->id,
                            'id_plan_cuentas'   => $value->id_plan_cuentas,
                            'item'              => $value->item,
                            'descripcion'       => $value->descripcion,
                            'debe'              => $value->haber,
                            'haber'             => 0,
                            'id_usuariocrea'    => $id_usuario,
                            'id_usuariomod'     => $id_usuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'estado'            => 1,

                        ];
                        RolAsientoPagoRubros::create($arr_pago_rubros1);
                    }
                }
            }

            if (count($rubros) > 0) {

                foreach ($rubros as $value2) {
                    //dd($value2);

                    if ($value2->descripcion == 'APORTE PATRONAL') {
                        $cuenta_patronal = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(2)');
                        $rol_asiento_pago = RolAsientoPagoRubros::where('id_rol_asiento', $cuentas_rol->id)->where('id_plan_cuentas', $cuenta_patronal->cuenta_guardar)->where('estado', 1)->first();
                        if (is_null($rol_asiento_pago)) {
                            $arr_pago_rubros2 = [
                                'id_rol_asiento'    => $cuentas_rol->id,
                                'id_plan_cuentas'   => $cuenta_patronal->cuenta_guardar,
                                'item'              => $cuenta_patronal->nombre_mostrar,
                                'descripcion'       => $value2->descripcion,
                                'debe'              => $value2->valor,
                                'haber'             => 0,
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                                'estado'            => 1,

                            ];
                            //dd($arr_pago_rubros2);
                            RolAsientoPagoRubros::create($arr_pago_rubros2);
                        }
                    }
                    if ($value2->descripcion == 'APORTE SECAP') {
                        $cuenta_secap = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(2)');
                        $rol_asiento_pago = RolAsientoPagoRubros::where('id_rol_asiento', $cuentas_rol->id)->where('id_plan_cuentas', $cuenta_secap->cuenta_guardar)->where('estado', 1)->first();
                        if (is_null($rol_asiento_pago)) {
                            $arr_pago_rubros2 = [
                                'id_rol_asiento'    => $cuentas_rol->id,
                                'id_plan_cuentas'   => $cuenta_secap->cuenta_guardar,
                                'item'              => $cuenta_secap->nombre_mostrar,
                                'descripcion'       => $value2->descripcion,
                                'debe'              => $value2->valor,
                                'haber'             => 0,
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                                'estado'            => 1,

                            ];
                            RolAsientoPagoRubros::create($arr_pago_rubros2);
                        }
                    }
                }
            }
        }
        return "oki";
        //dd($rubros,$cuenta_patronal,$cuenta_secap);
        //dd($cuentas_iess2);
    }

    public function cargar_pagos(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $id_usuario = Auth::user()->id;
        $idusuario  = $id_usuario;
        $id_empresa = $request->session()->get('id_empresa');

        $cuenta_pago = Ct_Configuraciones::obtener_cuenta('CAJA_GENERAL_PAGOS_NOMINA');

        //$ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->id_tipo_pago_planilla);


        /*$caja_banco = Ct_Caja_Banco::where('id_empresa', $id_empresa)->where('cuenta_mayor', $request->id_cuenta_planilla)->where('estado', 1)->first();
        if (is_null($caja_banco)) {
            return ['respuesta' => 'error', 'msj' => 'CAJA BANCO SIN PLAN DE CUENTAS', 'titulos' => 'Error'];
        }*/

        $cuentas_rol  = RolAsiento::find($request->rol_planillas);
        $sucursal     = $cuentas_rol->obtiene_sucursal;
        //dd($cuentas_rol,$request->rol_planillas);
        if ($cuentas_rol->id_asiento_planilla != null) {
            return ['respuesta' => 'error', 'msj' => 'YA SE GENERO EL ASIENTO PARA EL PAGO DE LA PLANILLA', 'titulos' => 'Error'];
        }

        $cuentas_iess2 = $cuentas_rol->cuentas->where('doc_pendiente', 'IESS2');

        $sum_pagar = $request->total_planilla;
        if ($sum_pagar <= 0) {
            return ['respuesta' => 'error', 'msj' => 'SIN VALOR A PAGAR', 'titulos' => 'Error'];
        }

        DB::beginTransaction();
        try {

            //dd($sum_pagar);
            $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            $ms = intval($cuentas_rol->mes) - 1;
            //$concepto  = 'PAGOS DE PLANILLAS DEL PERIODO AÑO: '.$cuentas_rol->anio.' Mes: '.$meses[$ms].' Por la Cantidad de:' . $sum_pagar;
            $concepto = $request->concepto2;
            //$concepto2 = 'PLANILLAS Año: ' . $cuentas_rol->anio . ' Mes: ' . $meses[$ms] . 'Por la Cantidad de :' . $sum_pagar;
            $concepto2 = $request->beneficiario2;
            /*$input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $request->fecha_planilla,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'PAGO DE APORTE PATRONAL Y SECAP',
                'sucursal'        => $sucursal->codigo_sucursal,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);*/
            /*$arr_valor = [
                'id_tipo_pago_planilla'    => $request->id_tipo_pago_planilla,
                'numero_cuenta_planilla'   => $request->numero_cuenta_planilla,
                'id_banco_planilla'        => $request->id_banco_planilla,
                'id_cuenta_planilla'       => $request->id_cuenta_planilla,
                'numero_cheque_planilla'   => $request->numero_cheque_planilla,
                'fecha_cheque_planilla'    => $request->fecha_cheque_planilla,
                'id_asiento_planilla'      => null,
                'fecha_planilla'           => $request->fecha_planilla,
                'beneficiario_planilla'    => $request->beneficiario2,
            ];

            $cuentas_rol->update($arr_valor);*/

            if (count($cuentas_iess2) > 0) {
                foreach ($cuentas_iess2 as $value) {
                    $rol_asiento_pago = RolAsientoPagoRubros::where('id_rol_asiento', $cuentas_rol->id)->where('id_plan_cuentas', $value->id_plan_cuentas)->where('estado', 1)->first();
                    if (is_null($rol_asiento_pago)) {
                        //dd($cuentas_rol->id,'cuenta cuentas_iess2: '.$value->id_plan_cuentas);
                        $arr_pago_rubros1 = [
                            'id_rol_asiento'    => $cuentas_rol->id,
                            'id_plan_cuentas'   => $value->id_plan_cuentas,
                            'item'              => $value->item,
                            'descripcion'       => $value->descripcion,
                            'debe'              => round($value->haber, 2),
                            'haber'             => 0,
                            'id_usuariocrea'    => $id_usuario,
                            'id_usuariomod'     => $id_usuario,
                            'ip_creacion'       => $ip_cliente,
                            'ip_modificacion'   => $ip_cliente,
                            'estado'            => 1,

                        ];
                        RolAsientoPagoRubros::create($arr_pago_rubros1);
                    }
                }
            }
            $cuentas_iess2 = $cuentas_rol->cuentas->where('doc_pendiente', 'IESS2');
            $rubros = $cuentas_rol->rubros;
            if (count($rubros) > 0) {
                foreach ($rubros as $value2) {
                    if ($value2->descripcion == 'APORTE PATRONAL') {
                        $cuenta_patronal = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(2)');
                        $rol_asiento_pago = RolAsientoPagoRubros::where('id_rol_asiento', $cuentas_rol->id)->where('id_plan_cuentas', $cuenta_patronal->cuenta_guardar)->where('estado', 1)->first();
                        if (is_null($rol_asiento_pago)) {
                            $arr_pago_rubros2 = [
                                'id_rol_asiento'    => $cuentas_rol->id,
                                'id_plan_cuentas'   => $cuenta_patronal->cuenta_guardar,
                                'item'              => $cuenta_patronal->nombre_mostrar,
                                'descripcion'       => $value2->descripcion,
                                'debe'              => round($value2->valor, 2),
                                'haber'             => 0,
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                                'estado'            => 1,
                            ];
                            RolAsientoPagoRubros::create($arr_pago_rubros2);
                        }
                    }
                    if ($value2->descripcion == 'APORTE SECAP') {
                        $cuenta_secap = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(2)');
                        $rol_asiento_pago = RolAsientoPagoRubros::where('id_rol_asiento', $cuentas_rol->id)->where('id_plan_cuentas', $cuenta_secap->cuenta_guardar)->where('estado', 1)->first();
                        if (is_null($rol_asiento_pago)) {
                            $arr_pago_rubros2 = [
                                'id_rol_asiento'    => $cuentas_rol->id,
                                'id_plan_cuentas'   => $cuenta_secap->cuenta_guardar,
                                'item'              => $cuenta_secap->nombre_mostrar,
                                'descripcion'       => $value2->descripcion,
                                'debe'              => round($value2->valor, 2),
                                'haber'             => 0,
                                'id_usuariocrea'    => $id_usuario,
                                'id_usuariomod'     => $id_usuario,
                                'ip_creacion'       => $ip_cliente,
                                'ip_modificacion'   => $ip_cliente,
                                'estado'            => 1,
                            ];
                            RolAsientoPagoRubros::create($arr_pago_rubros2);
                        }
                    }
                }
            }



            //$plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $request->id_cuenta_planilla)->orwhere('plan', $request->id_cuenta_planilla)->first();
            $plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $cuenta_pago->cuenta_guardar)->orwhere('plan', $cuenta_pago->cuenta_guardar)->first();
            //dd($plan_cuentas2,$cuenta_pago);
            //$plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);
            $rol_asiento_pago = RolAsientoPagoRubros::where('id_rol_asiento', $cuentas_rol->id)->where('id_plan_cuentas', $plan_cuentas2->id)->where('estado', 1)->first();
            if (is_null($rol_asiento_pago)) {
                if ($sum_pagar > 0) {
                    RolAsientoPagoRubros::create([
                        'id_rol_asiento'      => $cuentas_rol->id,
                        'id_plan_cuentas'     => $plan_cuentas2->id_plan,
                        'item'                => $plan_cuentas2->descripcion,
                        'descripcion'         => $plan_cuentas2->nombre,
                        'debe'                => '0',
                        'haber'               => round($sum_pagar, 2),
                        'estado'              => 1,
                        'id_usuariocrea'      => $id_usuario,
                        'id_usuariomod'       => $id_usuario,
                        'ip_creacion'         => $ip_cliente,
                        'ip_modificacion'     => $ip_cliente,
                        'cuenta_haber'        => '1',
                    ]);
                }
            }

            /*if ($ct_tipo_pago->tipo == 'ACREDITACION') { //DEBITO
                $nota_debito         = [
                    'concepto'        => $concepto,
                    'fecha'           => $request->fecha_planilla,
                    'valor'           => $sum_pagar,
                    'empresa'         => $id_empresa,
                    'tipo'            => "BAN-ND",
                    'id_asiento'      => $id_asiento_cabecera,
                    'id_banco'        => $caja_banco->id,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'modulo'          => 'PAGO DE PLANILLAS',
                    //'sucursal'        => $sucursal->codigo_sucursal,
                ];
                //$id_nota = 0;
                $id_nota = Nota_Debito::insertGetId($nota_debito);

                $nota_deb_detalle = [
                    'id_nota_debito'  => $id_nota,
                    'codigo'          => $request->id_cuenta_planilla,
                    'cuenta'          => $plan_cuentas2->nombre,
                    'debe'            => $sum_pagar,
                    'haber'           => number_format(0, 2),
                    'valor_base'      => $sum_pagar, //number_format($valor['debe'], 2),
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                ];
                Nota_Debito_Detalle::create($nota_deb_detalle);
            } else { //COMPROBANTE DE EGRESO
                $numero_factura = LogAsiento::getSecuencia(2);

                $input_comprobante = [
                    'descripcion'     => $concepto,
                    'estado'          => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_secuencia'    => 'null',
                    'nota'            => $concepto,
                    'fecha_comprobante' => $request->fecha_planilla,
                    'beneficiario'    => $concepto2,
                    'check'           => 0,
                    'girado'          => $concepto2,
                    'id_caja_banco'   => $caja_banco->id,
                    'nro_cheque'      => $request->numero_cheque_planilla,
                    'valor'           => $sum_pagar,
                    'fecha_cheque'    => $request->fecha_cheque_planilla,
                    'secuencia'       => $numero_factura,
                    'id_empresa'      => $id_empresa,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'modulo'          => 'PAGO DE PLANILLAS',
                    //'sucursal'        => $sucursal->codigo_sucursal,

                ];
                $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                Ct_Detalle_Comprobante_Egreso_Varios::create([
                    'id_comprobante_varios'          => $id_comprobante,
                    'codigo'                         => $request->id_cuenta_planilla,
                    'cuenta'                         => $plan_cuentas2->nombre,
                    'descripcion'                    => $concepto,
                    'debe'                           => $sum_pagar,
                    'id_secuencia'                   => $numero_factura,
                    'estado'                         => '1',
                    'ip_creacion'                    => $ip_cliente,
                    'ip_modificacion'                => $ip_cliente,
                    'id_usuariocrea'                 => $id_usuario,
                    'id_usuariomod'                  => $id_usuario,
                ]);
            }*/

            $pago_rubros = RolAsientoPagoRubros::where('id_rol_asiento', $cuentas_rol->id)->where('estado', '1')->get();

            DB::commit();
            return response()->json(['respuesta' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito', 'data' => null]);
        } catch (Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error', 'line' => $e->getLine()];
        }
    }

    public function update_valor_detalle(Request $request)
    {
        $id = $request->id;
        $debe = $request->debe;
        $haber = $request->haber;
        $rubro    = RolAsientoPagoRubros::find($id);
        if (!is_null($rubro)) {
            $rubro->update([
                'debe'   => round($debe, 2),
                'haber'   => round($haber, 2),
            ]);

            $data = "";

            return ['status' => 'success', 'msj' => 'Valores actualizados con exito', 'data' => null];
        } else {
            return ['status' => 'error', 'msj' => 'Rubro pago no encontrado'];
        }
    }

    public function anular($id_rubro)
    {
        $rubro = RolAsientoPagoRubros::find($id_rubro);
        $arr = [
            'estado' => '0',
        ];
        $rubro->update($arr);
        $pago_rubros = RolAsientoPagoRubros::where('id_rol_asiento', $rubro->id_rol_asiento)->where('estado', '1')->get();

        return ['status' => 'success', 'msj' => 'ok', 'data' => null];
    }

    public function agregarRubro(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_usuario = Auth::user()->id;
        $id_rol = $request->id_rol_aiento;
        $id_cuenta = $request->id_cuenta;
        $cuenta = Plan_Cuentas::find($id_cuenta);
        $rol_asiento_pago = RolAsientoPagoRubros::where('id_rol_asiento', $id_rol)->where('id_plan_cuentas', $id_cuenta)->where('estado', 1)->first();
        if (is_null($rol_asiento_pago)) {
            $arr_rubro = [
                'id_rol_asiento' => $id_rol,
                'id_plan_cuentas' => $id_cuenta,
                'item' => $cuenta->descripcion,
                'descripcion' => $cuenta->descripcion,
                'debe' => '0',
                'haber' => '0',
                'estado' => '1',
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $id_usuario,
                'id_usuariomod' => $id_usuario,
            ];
            RolAsientoPagoRubros::create($arr_rubro);

            return ['status' => 'success', 'msj' => 'ok', 'data' => null];
        } else {
            return ['status' => 'error', 'msj' => 'Cuenta ya agregada'];
        }
    }

    public function guardar_pago_rubros(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //dd($request->all());
        $id_usuario = Auth::user()->id;
        $idusuario  = $id_usuario;
        $id_rol = $request->rol_planillas;
        $rol_asiento = RolAsiento::find($id_rol);
        $id_empresa = $rol_asiento->id_empresa;
        $ct_tipo_pago = Ct_Rh_Tipo_Pago::find($request->id_tipo_pago_planilla);
        if (is_null($ct_tipo_pago)) {
            return ['status' => 'error', 'msj' => 'SELECCIONE LA FORMA DE PAGO', 'titulos' => 'Error'];
        }
        //dd($ct_tipo_pago);
        $caja_banco = Ct_Caja_Banco::where('id_empresa', $id_empresa)->where('cuenta_mayor', $request->id_cuenta_planilla)->where('estado', 1)->first();
        //dd($caja_banco, $request->all());
        if (is_null($caja_banco)) {
            return ['status' => 'error', 'msj' => 'CAJA BANCO SIN PLAN DE CUENTAS', 'titulos' => 'Error'];
        }

        $sucursal     = $rol_asiento->obtiene_sucursal;

        if ($rol_asiento->id_asiento_planilla != null) {
            return ['status' => 'error', 'msj' => 'YA SE GENERO EL ASIENTO PARA EL PAGO DE LA PLANILLA', 'titulos' => 'Error'];
        }

        $rol_haber = RolAsientoPagoRubros::where('id_rol_asiento', $id_rol)->where('estado', '1')->where('cuenta_haber', '1')->first();
        if (is_null($rol_haber)) {
            return ['status' => 'error', 'msj' => 'NO SE CREO LA CUENTA HABER', 'titulos' => 'Error'];
        }

        $debe = 0;
        $haber = 0;
        $cuentas = RolAsientoPagoRubros::where('id_rol_asiento', $id_rol)->where('estado', '1')->get();
        

        foreach ($cuentas as $value) {
            $debe += round($value->debe, 2);
            $haber += round($value->haber, 2);
        }
        //dd(floatval($debe),floatval($haber));
        if (abs(floatval($debe) - floatval($haber)) > 0.0001) {
            return ['status' => 'error', 'msj' => 'Los valores del debe y el haber no estan cuadrados. Debe: ' . $debe . '; Haber: ' . $haber, 'titulos' => 'Error'];
        }


        $sum_pagar = $debe;
        if ($sum_pagar <= 0) {
            return ['status' => 'error', 'msj' => 'SIN VALOR A PAGAR', 'titulos' => 'Error'];
        }

        /*if($rol_asiento->id_tipo_pago_planilla != $request->id_tipo_pago_planilla){
            return ['status' => 'error', 'msj' => 'No se puede cambiar el documento', 'titulos' => 'Error'];
        }*/
        //dd($request->all());

        //dd($arr_cta_haber);

        //dd($rol_asiento);

        DB::beginTransaction();
        try {

            $arr_valor = [
                'id_tipo_pago_planilla'    => $request->id_tipo_pago_planilla,
                'numero_cuenta_planilla'   => $request->numero_cuenta_planilla,
                'id_banco_planilla'        => $request->id_banco_planilla,
                'id_cuenta_planilla'       => $request->id_cuenta_planilla,
                'numero_cheque_planilla'   => $request->numero_cheque_planilla,
                'fecha_cheque_planilla'    => $request->fecha_cheque_planilla,
                'fecha_planilla'           => $request->fecha_planilla,
                'beneficiario_planilla'    => $request->beneficiario2,
            ];

            $rol_asiento->update($arr_valor);

            $arr_cta_haber = [
                'id_plan_cuentas'   => $caja_banco->cuenta_mayor,
                'item'              => $caja_banco->nombre,
                'descripcion'       => $caja_banco->nombre,
            ];

            $rol_haber->update($arr_cta_haber);
            $cuentas = RolAsientoPagoRubros::where('id_rol_asiento', $id_rol)->where('estado', '1')->get();

            //dd($sum_pagar);
            $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            $ms = intval($rol_asiento->mes) - 1;
            //$concepto  = 'PAGOS DE PLANILLAS DEL PERIODO AÑO: '.$cuentas_rol->anio.' Mes: '.$meses[$ms].' Por la Cantidad de:' . $sum_pagar;
            $concepto = $request->concepto2;
            //$concepto2 = 'PLANILLAS Año: ' . $cuentas_rol->anio . ' Mes: ' . $meses[$ms] . 'Por la Cantidad de :' . $sum_pagar;
            $concepto2 = $request->beneficiario2;
            $input_cabecera = [
                'observacion'     => $concepto,
                'fecha_asiento'   => $rol_asiento->fecha_planilla,
                'id_empresa'      => $id_empresa,
                'valor'           => $sum_pagar,
                'id_usuariocrea'  => $id_usuario,
                'id_usuariomod'   => $id_usuario,
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'modulo'          => 'PAGO DE APORTE PATRONAL Y SECAP',
                'sucursal'        => $sucursal->codigo_sucursal,
            ];

            $id_asiento_cabecera = Ct_Asientos_Cabecera::insertGetId($input_cabecera);

            $rol_asiento->update([
                'id_asiento_planilla'  => $id_asiento_cabecera,
            ]);

            /*$arr_valor = [
                'id_tipo_pago_planilla'    => $rol_asiento->id_tipo_pago_planilla,
                'numero_cuenta_planilla'   => $rol_asiento->numero_cuenta_planilla,
                'id_banco_planilla'        => $rol_asiento->id_banco_planilla,
                'id_cuenta_planilla'       => $rol_asiento->id_cuenta_planilla,
                'numero_cheque_planilla'   => $rol_asiento->numero_cheque_planilla,
                'fecha_cheque_planilla'    => $rol_asiento->fecha_cheque_planilla,
                'id_asiento_planilla'      => $id_asiento_cabecera,
                'fecha_planilla'           => $rol_asiento->fecha_planilla,
            ];

            $rol_asiento->update($arr_valor);*/


            foreach ($cuentas as $cuenta) {
                $detalle_asiento = Ct_Asientos_Detalle::create([
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $cuenta->id_plan_cuentas,
                    'descripcion'         => $cuenta->descripcion,
                    'fecha'               => $rol_asiento->fecha_planilla,
                    'debe'                => $cuenta->debe,
                    'haber'               => $cuenta->haber,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                ]);
            }
            // if ($rol_asiento->aporte_patronal > 0) {
            //     $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_PATRONAL_11.15%_(2)');

            //     Ct_Asientos_Detalle::create([ //////DEBE

            //         'id_asiento_cabecera' => $id_asiento_cabecera,
            //         'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
            //         'descripcion'         => $plan_cuentas->nombre_mostrar,
            //         'fecha'               => $rol_asiento->fecha_planilla,
            //         'debe'                => $rol_asiento->aporte_patronal,
            //         'haber'               => 0,
            //         'id_usuariocrea'      => $id_usuario,
            //         'id_usuariomod'       => $id_usuario,
            //         'ip_creacion'         => $ip_cliente,
            //         'ip_modificacion'     => $ip_cliente,

            //     ]);
            // }


            // if ($rol_asiento->aporte_secap > 0) {
            //     $plan_cuentas = \Sis_medico\Ct_Configuraciones::obtener_cuenta('APORTE_SECAP_1%_(2)');

            //     Ct_Asientos_Detalle::create([ //////DEBE

            //         'id_asiento_cabecera' => $id_asiento_cabecera,
            //         'id_plan_cuenta'      => $plan_cuentas->cuenta_guardar,
            //         'descripcion'         => $plan_cuentas->nombre_mostrar,
            //         'fecha'               => $rol_asiento->fecha_planilla,
            //         'debe'                => $rol_asiento->aporte_secap,
            //         'haber'               => 0,
            //         'id_usuariocrea'      => $id_usuario,
            //         'id_usuariomod'       => $id_usuario,
            //         'ip_creacion'         => $ip_cliente,
            //         'ip_modificacion'     => $ip_cliente,

            //     ]);
            // }

            $plan_cuentas2 = Plan_Cuentas_Empresa::where('id_plan', $rol_asiento->id_cuenta_planilla)->orwhere('plan', $rol_asiento->id_cuenta_planilla)->first();
            // $plan_cuentas2 = Plan_Cuentas::find(is_null($plan_cuentas2->id_plan) ? $plan_cuentas2->plan : $plan_cuentas2->id_plan);

            /*if ($sum_pagar > 0) {

                Ct_Asientos_Detalle::create([

                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_plan_cuenta'      => $rol_asiento->id_cuenta_planilla,
                    'descripcion'         => $plan_cuentas2->nombre,
                    'fecha'               => $rol_asiento->fecha_planilla,
                    'debe'                => '0',
                    'haber'               => $sum_pagar,
                    'id_usuariocrea'      => $id_usuario,
                    'id_usuariomod'       => $id_usuario,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,

                ]);
            }*/

            if ($ct_tipo_pago->tipo == 'ACREDITACION') { //DEBITO

                $nota_debito         = [
                    'concepto'        => $concepto,
                    'fecha'           => $rol_asiento->fecha_planilla,
                    'valor'           => $sum_pagar,
                    'empresa'         => $id_empresa,
                    'tipo'            => "BAN-ND",
                    'id_asiento'      => $id_asiento_cabecera,
                    'id_banco'        => $caja_banco->id,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'modulo'          => 'PAGO DE PLANILLAS',
                    //'sucursal'        => $sucursal->codigo_sucursal,
                ];
                //$id_nota = 0;
                $id_nota = Nota_Debito::insertGetId($nota_debito);

                foreach ($cuentas as $cuenta) {
                    //if($cuenta->cuenta_haber != 1){
                    /*$nota_deb_detalle = [
                            'id_nota_debito'  => $id_nota,
                            'codigo'          => $rol_asiento->id_cuenta_planilla,
                            'cuenta'          => $plan_cuentas2->nombre,
                            'debe'            => $sum_pagar,
                            'haber'           => number_format(0, 2),
                            'valor_base'      => $sum_pagar, //number_format($valor['debe'], 2),
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $id_usuario,
                            'id_usuariomod'   => $id_usuario,
                        ];*/
                    $valor_base = 0;
                    if ($cuenta->haber != 0) {
                        $valor_base = $cuenta->haber;
                    }
                    if ($cuenta->debe != 0) {
                        $valor_base = $cuenta->debe;
                    }
                    $valor_base =
                        $nota_deb_detalle = [
                            'id_nota_debito'  => $id_nota,
                            'codigo'          => $cuenta->id_plan_cuentas,
                            'cuenta'          => $cuenta->descripcion,
                            'debe'            => $cuenta->debe,
                            'haber'           => $cuenta->haber,
                            'valor_base'      => $valor_base, //number_format($valor['debe'], 2),
                            'ip_creacion'     => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea'  => $id_usuario,
                            'id_usuariomod'   => $id_usuario,
                        ];
                    Nota_Debito_Detalle::create($nota_deb_detalle);
                    //}    
                }
            } else { //COMPROBANTE DE EGRESO
                $numero_factura = LogAsiento::getSecuencia(2);

                $input_comprobante = [
                    'descripcion'     => $concepto,
                    'estado'          => '1',
                    'id_asiento_cabecera' => $id_asiento_cabecera,
                    'id_secuencia'    => 'null',
                    'nota'            => $concepto,
                    'fecha_comprobante' => $rol_asiento->fecha_planilla,
                    'beneficiario'    => $concepto2,
                    'check'           => 0,
                    'girado'          => $concepto2,
                    'id_caja_banco'   => $caja_banco->id,
                    'nro_cheque'      => $rol_asiento->numero_cheque_planilla,
                    'valor'           => $sum_pagar,
                    'fecha_cheque'    => $rol_asiento->fecha_cheque_planilla,
                    'secuencia'       => $numero_factura,
                    'id_empresa'      => $id_empresa,
                    'id_usuariocrea'  => $id_usuario,
                    'id_usuariomod'   => $id_usuario,
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'modulo'          => 'PAGO DE PLANILLAS',
                    //'sucursal'        => $sucursal->codigo_sucursal,

                ];
                $id_comprobante = Ct_Comprobante_Egreso_Varios::insertGetId($input_comprobante);

                foreach ($cuentas as $cuenta) {
                    //if($cuenta->cuenta_haber != 1){
                    /*Ct_Detalle_Comprobante_Egreso_Varios::create([
                            'id_comprobante_varios'          => $id_comprobante,
                            'codigo'                         => $rol_asiento->id_cuenta_planilla,
                            'cuenta'                         => $plan_cuentas2->nombre,
                            'descripcion'                    => $concepto,
                            'debe'                           => $sum_pagar,
                            'id_secuencia'                   => $numero_factura,
                            'estado'                         => '1',
                            'ip_creacion'                    => $ip_cliente,
                            'ip_modificacion'                => $ip_cliente,
                            'id_usuariocrea'                 => $id_usuario,
                            'id_usuariomod'                  => $id_usuario,
                        ]);*/

                    Ct_Detalle_Comprobante_Egreso_Varios::create([
                        'id_comprobante_varios'          => $id_comprobante,
                        'codigo'                         => $cuenta->id_plan_cuentas,
                        'cuenta'                         => $cuenta->descripcion,
                        'descripcion'                    => $concepto,
                        'debe'                => $cuenta->debe,
                        'haber'               => $cuenta->haber,
                        'id_secuencia'                   => $numero_factura,
                        'estado'                         => '1',
                        'ip_creacion'                    => $ip_cliente,
                        'ip_modificacion'                => $ip_cliente,
                        'id_usuariocrea'                 => $id_usuario,
                        'id_usuariomod'                  => $id_usuario,
                    ]);
                    //}    

                }
            }

            $data = "";
            $pago_rubros = RolAsientoPagoRubros::where('id_rol_asiento', $id_rol)->where('estado', '1')->get();
            foreach ($pago_rubros as $value) {
                $data .= '<tr>
                    <td>' . $value->id_plan_cuentas . '-' . $value->descripcion . '</td>
                    <td style="text-align: right;">' . number_format($value->debe, 2, '.', ' ') . '</td>
                    <td style="text-align: right;">' . number_format($value->haber, 2, '.', ' ') . '</td>
                    <td>
                        
                    </td>
                </tr>';
            }
            DB::commit();
            return ['status' => 'success', 'msj' => 'Guardado Exitosamente', 'titulos' => 'Exito', 'data' => $data];
        } catch (Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'msj' => $e->getMessage(), 'titulos' => 'Error', 'line' => $e->getLine()];
        }
    }

    public function index_cuentas_rubros($id_rol)
    {

        $rol_asiento = RolAsiento::find($id_rol);

        $plan_cuentas = Plan_Cuentas::join('plan_cuentas_empresa as pe', 'pe.id_plan', 'plan_cuentas.id')
            ->where('pe.id_empresa', $rol_asiento->id_empresa)
            ->where('plan_cuentas.estado', '=', 2)
            ->select('plan_cuentas.*')
            ->get();

        $pago_rubros = RolAsientoPagoRubros::where('id_rol_asiento', $rol_asiento->id)->where('estado', '1')->get();

        return view('contable.nuevo_rol_pago.ctas_rubros', ['pago_rubros' => $pago_rubros, 'plan_cuentas' => $plan_cuentas, 'cuentas_rol' => $rol_asiento]);
    }


    public function acumula_decimo_tercer_excel(Request $request)
    {

        $anio = $request->anio;
        $empleado = $request->empleado;
        $id_sucursal = $request->sucursal;

        $titulos = array("N°", "CEDULA",  "APELLIDOS", "NOMBRES", "OCUPACION", "GENERO", "DIAS TRABAJADOS", "VALOR DECIMO", "FIRMA O HUELLA");
        //Posiciones en el excel
        $posicion = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        if ($anio == null) {
            $anio = 2023;
        }
        $id_empresa     = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        $DecimosTeceros = Ct_Rh_Acumulados::where('ct_rh_acumulados.tipo', 'TERCER')
            ->where('ct_rh_acumulados.estado', 1)
            ->where('ct_rh_acumulados.anio', $anio)
            ->where('ct_rh_acumulados.id_empresa', $id_empresa)
            ->join('ct_nomina as n', function ($join) {
                $join->on('n.id', '=', 'ct_rh_acumulados.id_nomina')
                    ->where('n.estado', '=', 1);
            })
            ->join('users as us', 'us.id', '=', 'n.id_user')
            ->orderBy('us.apellido1', 'asc');

        if ($empleado != null) {
            $DecimosTeceros = $DecimosTeceros->where('n.nombres', 'like', '%' . $empleado . '%');
        }

        $DecimosTeceros = $DecimosTeceros->select('ct_rh_acumulados.*', 'n.id_user', 'n.nombres', 'n.sexo', 'n.cargo', 'us.nombre1', 'us.nombre2', 'us.apellido1', 'us.apellido2')->where('ct_rh_acumulados.valor', '<>', '0')->get();

        Excel::create('DECIMO TERCER SUELDO', function ($excel) use ($titulos, $posicion, $DecimosTeceros, $request, $anio, $empresa) {
            $excel->sheet('DECIMO TERCER SUELDO', function ($sheet) use ($titulos, $posicion, $DecimosTeceros, $request, $anio, $empresa) {
                $sheet->cells('A1:C2', function ($cell) use ($empresa, $sheet) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('B1');
                    $objDrawing->setHeight(145);
                    $objDrawing->setWidth(180);
                    $cell->setAlignment('center');
                    $objDrawing->setWorksheet($sheet);
                });
                $sheet->mergeCells('D1:I1');
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INFORME INDIVIDUAL SOBRE EL PAGO DE DÉCIMO TERCERA REMUNERACIÓN');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('D2:I2');
                $sheet->cell('D2', function ($cell) use ($empresa, $sheet) {
                    // manipulate the cel
                    $cell->setValue($empresa->razonsocial);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('D3:I3');
                $sheet->cell('D3', function ($cell) use ($empresa, $sheet) {
                    // manipulate the cel
                    $cell->setValue('R.U.C:' . $empresa->id);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $comienzo = 4; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL
                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos); $i++) {
                    $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                        $cell->setValue($titulos[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#92CFEF');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                /*****FIN DE TITULOS DEL EXCEL***********/
                $contador1 = 1;

                $total_valores = 0;
                foreach ($DecimosTeceros as $value) {
                    //dd($days);
                    $datos_excel = array();
                    $valor_decimo = round($value->valor, 2);
                    array_push($datos_excel, $contador1, $value->id_user, $value->apellido1 . ' ' . $value->apellido2,  $value->nombre1  . ' ' . $value->nombre2, $value->ct_nomina->cargo, $value->sexo, $value->dias_laborados, $valor_decimo, '');

                    $total_valores += $valor_decimo;

                    for ($i = 0; $i < count($datos_excel); $i++) {
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                            $cell->setValue($datos_excel[$i]);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setAlignment('center');
                        });
                    }
                    $comienzo++;
                    $contador1++;
                }
                $sheet->cell('G' . $comienzo, function ($cell) {
                    $cell->setValue('TOTAL:');
                    $cell->setAlignment('right');
                });

                $sheet->cell('H' . $comienzo, function ($cell) use ($total_valores) {
                    $cell->setValue($total_valores);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setAlignment('center');
                });
            });
            $excel->getActiveSheet()->getStyle('C4:C4')->getAlignment()->setWrapText(true);
        })->export('xlsx');
    }
    public function index_fondo_reserva_anterior($id_nomina){
        $nomina = Ct_Nomina::find($id_nomina);
        $fr_anterior = Ct_Rh_Fondo_Reserva_Anterior::where('id_nomina',$id_nomina)->where('estado',1)->first();
        //dd($fr_anterior);

        return view('contable.nomina.fondo_reserva_anterior',['nomina'=>$nomina, 'fr_anterior'=>$fr_anterior]);
    }
    public function store_fr_anterior(Request $request){
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        //dd($request->all());
        $id_usuario = Auth::user()->id;
        $id_nomina = $request->id_nomina;

        $fecha_ing = $request->fecha_ingreso_anterior;
        $fecha_egr = $request->fecha_egreso_anterior;
        $fecha_inicio = new DateTime($fecha_ing);
        $fecha_fin = new DateTime($fecha_egr);

        // Calcular la diferencia entre las fechas
        $diferencia = $fecha_inicio->diff($fecha_fin);

        // Obtener el total de meses (considerando los años y los meses)
        $total_meses = ($diferencia->y * 12) + $diferencia->m;
        $aniosTotales = $diferencia->y;
        $fr_anterior = Ct_Rh_Fondo_Reserva_Anterior::where('id_nomina',$id_nomina)->where('estado',1)->first();
        if(!is_null($fr_anterior)){
            $arr_up_fr_ant =[
                'id_nomina'                 => $id_nomina,
                'fecha_ingreso_anterior'    => $fecha_ing,
                'fecha_egreso_anterior'     => $fecha_egr,
                'estado'                    => '1',
                'total_meses_anterior'      => $total_meses,
                'total_anios_anterior'      => $aniosTotales,
                'id_usuariomod'             => $id_usuario, 
                'ip_modificacion'           => $ip_cliente,
           ];
           //dd($arr_fr_ant);
           $fr_anterior->update($arr_up_fr_ant);
        }else{
            $arr_fr_ant =[
                'id_nomina'                 => $id_nomina,
                'fecha_ingreso_anterior'    => $fecha_ing,
                'fecha_egreso_anterior'     => $fecha_egr,
                'estado'                    => '1',
                'total_meses_anterior'      => $total_meses,
                'total_anios_anterior'      => $aniosTotales,
                'id_usuariocrea'            => $id_usuario,
                'id_usuariomod'             => $id_usuario, 
                'ip_creacion'               => $ip_cliente,
                'ip_modificacion'           => $ip_cliente,
           ];
           //dd($arr_fr_ant);
           Ct_Rh_Fondo_Reserva_Anterior::create($arr_fr_ant);
        }

       return json_encode("ok");
    }
}
