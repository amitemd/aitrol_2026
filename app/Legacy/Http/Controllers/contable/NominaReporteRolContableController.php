<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Ct_Nomina;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;

class NominaReporteRolContableController extends Controller
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

    /*****************************************************/
    /********INDEX BUSCADOR ROLES CONTABILIDAD***********/
    /****************************************************/

    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $empresas = Empresa::all();

        return view('contable.reporte_roles_contable.index', ['empresas' => $empresas]);
    }

    /***********************************************************************/
    /**********BUSCADOR ROLES DE PAGO POR EMPRESA CONTABILIDAD**************/
    /***********************************************************************/
    public function buscador_roles_contable(Request $request)
    {
        $id_empresa = $request['id_empresa'];
        $id_anio    = $request['year'];
        $id_mes     = $request['mes'];

        $rol_det_consulta = DB::table('ct_rol_pagos as rp')
            ->where('rp.id_empresa', $id_empresa)
            ->where('rp.anio', $id_anio)
            ->where('rp.mes', $id_mes)
            ->where('rp.estado', '1')
            ->join('ct_detalle_rol as drol', 'drol.id_rol', 'rp.id')
            ->join('users as u', 'rp.id_user', 'u.id')
            ->orderby('u.apellido1')
            ->select(
                'rp.id_empresa as idempresa',
                'rp.anio as anio',
                'rp.mes as mes',
                'rp.id_user as usuario',
                'rp.id_nomina as id_nomina',
                'rp.estado as estado_rol',
                'rp.id as id_rol',
                'drol.sueldo_mensual as sueldo',
                'drol.base_iess as base_iess',
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
                'drol.fondo_reserva_acumulado as fondo_reserva_acum',
                'drol.decimo_tercero_acumulado as decimo_tercero_acum',
                'drol.decimo_cuarto_acumulado as decimo_cuarto_acum',
                'drol.otros_egresos as otro_egres',
                'drol.dias_laborados as dias_laborados'
            )->get();

        return view('contable.reporte_roles_contable.resultado_busqueda_rolescont', ['rol_det_consulta' => $rol_det_consulta]);
    }

    /*****************************************************/
    /*************REPORTE ROLES CONTABILIDAD**************/
    /****************************************************/
    public function reporte_datos_rol_pago_contable(Request $request)
    {
        $id_empresa = $request['id_empresa'];
        $id_anio    = $request['year'];
        $id_mes     = $request['mes'];

        $mes_rol = null;

        $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();

        if ($id_mes == '1') {
            $mes_rol = 'ENERO';
        } elseif ($id_mes == '2') {
            $mes_rol = 'FEBRERO';
        } elseif ($id_mes == '3') {
            $mes_rol = 'MARZO';
        } elseif ($id_mes == '4') {
            $mes_rol = 'ABRIL';
        } elseif ($id_mes == '5') {
            $mes_rol = 'MAYO';
        } elseif ($id_mes == '6') {
            $mes_rol = 'JUNIO';
        } elseif ($id_mes == '7') {
            $mes_rol = 'JULIO';
        } elseif ($id_mes == '8') {
            $mes_rol = 'AGOSTO';
        } elseif ($id_mes == '9') {
            $mes_rol = 'SEPTIEMBRE';
        } elseif ($id_mes == '10') {
            $mes_rol = 'OCTUBRE';
        } elseif ($id_mes == '11') {
            $mes_rol = 'NOVIEMBRE';
        } elseif ($id_mes == '12') {
            $mes_rol = 'DICIEMBRE';
        }

        $rol_det_consulta = $rol_det_consulta = DB::table('ct_rol_pagos as rp')
            ->where('rp.id_empresa', $id_empresa)
            ->where('rp.anio', $id_anio)
            ->where('rp.mes', $id_mes)
            ->where('rp.estado', '1')
            ->join('ct_detalle_rol as drol', 'drol.id_rol', 'rp.id')
            ->join('users as u', 'rp.id_user', 'u.id')
            ->join('ct_nomina as nom', 'nom.id', 'rp.id_nomina')
            ->leftjoin('ct_sucursales as scrsal', 'scrsal.id', 'nom.id_sucursal')
            ->orderby('u.apellido1')
            ->select(
                'rp.id_empresa as idempresa',
                'rp.anio as anio',
                'rp.mes as mes',
                'rp.id_user as usuario',
                'rp.id_nomina as id_nomina',
                'rp.estado as estado_rol',
                'rp.id as id_rol',
                'drol.sueldo_mensual as sueldo',
                'drol.base_iess as base_iess',
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
                'drol.total_quota_quirog as total_cuot_quir',
                'drol.total_quota_hipot as total_cuot_hipot',
                'drol.fond_reserv_cobrar as fond_reserv_cobr',
                'drol.fondo_reserva_acumulado as fondo_reserva_acum',
                'drol.decimo_tercero_acumulado as decimo_tercero_acum',
                'drol.decimo_cuarto_acumulado as decimo_cuarto_acum',
                'drol.parqueo',
                'drol.extension_conyuge',
                'drol.otros_egresos as otro_egres',
                'nom.id_sucursal as id_sucursal',
                'scrsal.nombre_sucursal as nombre_sucursal',
                'rp.pasante',
                'rp.jornada',
                'drol.valor_vacacion',
                'nom.fecha_ingreso as fecha_ingreso',
                'drol.dias_laborados'
            )->get();
        /*if (Auth::user()->id == '0922729587') {
        dd($rol_det_consulta);
        }*/

        //$fecha_d = date('Y/m/d');
        Excel::create('Reporte Rol Pago Empleados Contable' . ' : ' . $mes_rol . ' DEL ' . $id_anio, function ($excel) use ($rol_det_consulta, $empresa, $id_anio, $mes_rol) {
            $excel->sheet('Datos Rol Pago', function ($sheet) use ($rol_det_consulta, $empresa, $id_anio, $mes_rol) {
                $fecha_d = date('Y/m/d');
                $i       = 5;

                $sum_salario           = 0;
                $sum_cantid_horas_50   = 0;
                $sum_cantid_horas_100  = 0;
                $sum_horas_50          = 0;
                $sum_horas_100         = 0;
                $sum_bono              = 0;
                $sum_bonoimp           = 0;
                $sum_alimentacion      = 0;
                $sum_transporte        = 0;
                $sum_vacaciones_pagadas = 0;
                $sum_fondo_reserva     = 0;
                $sum_decimo_tercero    = 0;
                $sum_decimo_cuarto     = 0;
                $sum_total_ingreso     = 0;
                $sum_iess              = 0;
                $sum_baseiess          = 0;
                $sum_seg_priv          = 0;
                $sum_imp_renta         = 0;
                $sum_multa             = 0;
                $sum_examlab           = 0;
                $sum_prestamoempresa   = 0;
                $sum_anticipo_quincena = 0;
                $sum_total_egreso      = 0;
                $sum_total_hip         = 0;
                $sum_total_quir        = 0;
                $sum_neto_recibido     = 0;
                $sum_otro_anticipo     = 0;
                $sum_otro_egreso       = 0;
                $sum_saldo_prestamo    = 0;
                $sum_parqueo           = 0;
                $sum_ext_conyuge           = 0;
                $sum_acum_fondo_reserva    = 0;
                $sum_acum_decimo_tercero  = 0;
                $sum_acum_decimo_cuarto   = 0;
                $sum_valor_vacaciones = 0;


                $sheet->mergeCells('A1:AG1');
                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {$mes_letra = "ENERO";}
                if ($mes == 02) {$mes_letra = "FEBRERO";}
                if ($mes == 03) {$mes_letra = "MARZO";}
                if ($mes == 04) {$mes_letra = "ABRIL";}
                if ($mes == 05) {$mes_letra = "MAYO";}
                if ($mes == 06) {$mes_letra = "JUNIO";}
                if ($mes == 07) {$mes_letra = "JULIO";}
                if ($mes == '08') {$mes_letra = "AGOSTO";}
                if ($mes == '09') {$mes_letra = "SEPTIEMBRE";}
                if ($mes == '10') {$mes_letra = "OCTUBRE";}
                if ($mes == '11') {$mes_letra = "NOVIEMBRE";}
                if ($mes == '12') {$mes_letra = "DICIEMBRE";}
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1', function ($cell) use ($fecha2, $id_anio, $mes_rol) {
                    // manipulate the cel
                    $cell->setValue('DETALLE ROL PAGOS EMPLEADOS' . ' : ' . $mes_rol . ' DEL ' . $id_anio);
                    $cell->setFontSize('15');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells('A1', function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('15');
                });
                $sheet->mergeCells('A2:AG2');
                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    if (!is_null($empresa)) {
                        $cell->setValue($empresa->nombrecomercial);
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->cells('A2', function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                });
                $sheet->mergeCells('A3:AG3');
                $sheet->cell('A3', function ($cell) use ($empresa) {
                    // manipulate the cel
                    if (!is_null($empresa)) {
                        $cell->setValue($empresa->id);
                    }
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->cells('A3', function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                });
                $sheet->cells('A1:K3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES DE EMPLEADOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA DE INGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DIAS LABORADOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUCURSAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SUELDO MENSUAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE IESS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BONO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ALIMENTACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MOVILIZACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR FONDO RESERVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR DECIMO IV');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL INGRESOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('9.45% IESS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO PRIVADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('O4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMPUESTO A LA RENTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('P4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MULTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('Q4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMO EMPRESA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('R4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALDO EMPRESA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('S4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTRO EGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('T4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANTICIPO QUINCENA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('U4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS ANTICIPOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                //Prestamos Hipotecarios
                $sheet->cell('V4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMOS HIPOTECARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //Prestamos Quirografarios

                $sheet->cell('W4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESTAMOS QUIROGRAFARIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PARQUEO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EXTENSIONES DE CONYUGUE ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('Z4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL EGRESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AA4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NETO RECIBIDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AB4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PASANTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                
                $sheet->cell('AC4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('JORNADA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AD4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VALOR VACACIONES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('AE4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ACUMULADO FONDO DE RESERVA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
               

                $sheet->cell('AF4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ACUMULADO DECIMO TERCERO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ACUMULADO DECIMO CUARTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AH4', function ($cell) {
                    $cell->setValue('VACACIONES PAGADAS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
		
                $sheet->cells('A4:AH4', function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                // DETALLES
                $sheet->setColumnFormat(array(
                    'E'  => '0.00', 'F'  => '0.00', 'G'  => '0.00','H' => '0.00',
                    'I'  => '0.00', 'J'  => '0.00', 'K'  => '0.00', 'L'  => '0.00', 'M'  => '0.0000', 
                    'N'  => '0.00', 'O'  => '0.00', 'P'  => '0.00', 'Q'  => '0.00','R' => '0.00',
                    'S'  => '0.00', 'T'  => '0.00', 'U'  => '0.00','V' => '0.00',
                    'W'  => '0.00', 'X'  => '0.00', 'Y'  => '0.00',
                    'Z'  => '0.00', 'AA'  => '0.00', 'AD' => '0.00',
                    'AE' => '0.00', 'AF' => '0.00', 'AG'  => '0.00',
                ));

                foreach ($rol_det_consulta as $value) {
                    $txtcolor = '#000000';

                    $empresa    = null;
                    $usuario    = null;
                    $dat_nomina = null;

                    if ($value->id_nomina != null) {
                        $dat_nomina = Ct_Nomina::find($value->id_nomina);
                    }

                    if ($value->usuario != null) {
                        $usuario         = User::find($value->usuario);
                        $nombre_paciente = "";

                        $nombre_paciente = $nombre_paciente . $usuario->apellido1 . " ";
                        if ($usuario->apellido2 != '(N/A)') {
                            $nombre_paciente = $nombre_paciente . $usuario->apellido2 . " ";
                        }

                        $nombre_paciente = $nombre_paciente . $usuario->nombre1 . " ";

                        if ($usuario->nombre2 != '(N/A)') {
                            $nombre_paciente = $nombre_paciente . $usuario->nombre2 . " ";
                        }
                    }
                    $pasante = "";
                    if(($value->pasante == '1')){
                        $pasante = "PASANTE";
                    }
                    $jornada = "";
                    if($value->jornada == '1'){
                       $jornada = "MEDIA JORNADA";
                    }
                    if($value->jornada == '0'){
                        $jornada = "JORNADA COMPLETA";
                     }
                    if($value->valor_vacacion == null){
                        $value->valor_vacacion = 0;
                    }
                    $sheet->cell('A' . $i, function ($cell) use ($nombre_paciente, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($nombre_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $nomina=Ct_Nomina::find($value->id_nomina);
                    $sheet->cell('B'.$i, function($cell) use ($nomina, $txtcolor){
                        if (!is_null($nomina->fecha_ant)){
                            $cell->setValue($nomina->fecha_ant);
                        }
                        else{
                            $cell->setValue($nomina->fecha_ingreso);
                        }
                        
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    }); 
                    
                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->dias_laborados);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->nombre_sucursal);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });    
                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->sueldo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->base_iess);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->bonificacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->alimentacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->transporte);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->fondo_reserva);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->decimo_cuarto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('L' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->total_ingreso);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('M' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->porcentaje_iess);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->seguro_privado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('O' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->impuesto_renta);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('P' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->multa);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('Q' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $total = ($value->prestamo_empleado + $value->exam_laboratorio);
                        $cell->setValue($total);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('R' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $total = ($value->saldo_inicial);
                        $cell->setValue($total);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('S' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $total = ($value->otro_egres);
                        $cell->setValue($total);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('T' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->anticipo_quincena);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('U' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->otro_anticipo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('V' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->total_cuot_hipot);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);

                    });
                    $sheet->cell('W' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->total_cuot_quir);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('X' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->parqueo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('Y' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->extension_conyuge);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('Z' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->total_egreso);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);

                    });
                    $sheet->cell('AA' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->neto_recibido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AB' . $i, function ($cell) use ($value, $txtcolor,$pasante) {
                        // manipulate the cel
                        $cell->setValue($pasante);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AC' . $i, function ($cell) use ($value, $txtcolor,$jornada) {
                        // manipulate the cel
                        $cell->setValue($jornada);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AD' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->valor_vacacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
		            $sheet->cell('AE' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->fondo_reserva_acum);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
		            $sheet->cell('AF' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->decimo_tercero_acum);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AG' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->decimo_cuarto_acum);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AH' . $i, function ($cell) use ($value, $txtcolor) {
                        $cell->setValue($value->vacaciones);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
		            
                    $sum_salario = $sum_salario + $value->sueldo;
                    /*$sum_cantid_horas_50 = $sum_cantid_horas_50+$value->cantidad_horas_50;
                    $sum_cantid_horas_100 = $sum_cantid_horas_100+$value->cantidad_horas_100;*/
                    /* $sum_horas_50 = $sum_horas_50+$value->valor_horas_50;*/
                    /* $sum_horas_100 = $sum_horas_100+$value->valor_horas_100;*/
                    $sum_bono = $sum_bono + $value->bonificacion;
                    /*$sum_bonoimp = $sum_bonoimp+$value->bono_imputable;*/
                    $sum_baseiess       = $sum_baseiess + $value->base_iess;
                    $sum_alimentacion   = $sum_alimentacion + $value->alimentacion;
                    $sum_transporte     = $sum_transporte + $value->transporte;
                    $sum_vacaciones_pagadas += $value->vacaciones;
                    $sum_fondo_reserva  = $sum_fondo_reserva + $value->fondo_reserva;
                    $sum_decimo_tercero = $sum_decimo_tercero + $value->decimo_tercero;
                    $sum_decimo_cuarto  = $sum_decimo_cuarto + $value->decimo_cuarto;
                    $sum_total_ingreso  = $sum_total_ingreso + $value->total_ingreso;
                    $sum_iess           = $sum_iess + $value->porcentaje_iess;
                    $sum_seg_priv       = $sum_seg_priv + $value->seguro_privado;
                    $sum_imp_renta      = $sum_imp_renta + $value->impuesto_renta;
                    $sum_multa          = $sum_multa + $value->multa;
                    /*$sum_examlab =   $sum_examlab+$value->exam_laboratorio;*/
                    $sum_prestamoempresa   = $sum_prestamoempresa + ($value->prestamo_empleado + $value->exam_laboratorio);
                    $sum_anticipo_quincena = $sum_anticipo_quincena + $value->anticipo_quincena;
                    $sum_total_egreso      = $sum_total_egreso + $value->total_egreso;
                    $sum_neto_recibido     = $sum_neto_recibido + $value->neto_recibido;
                    $sum_total_hip         = $sum_total_hip + $value->total_cuot_hipot;
                    $sum_total_quir        = $sum_total_quir + $value->total_cuot_quir;
                    $sum_otro_egreso       = $sum_otro_egreso + $value->otro_egres;
                    $sum_otro_anticipo     = $sum_otro_anticipo + $value->otro_anticipo;
                    $sum_saldo_prestamo    = $sum_saldo_prestamo + $value->saldo_inicial;
                    $sum_parqueo           = $sum_parqueo + $value->parqueo;
                    $sum_ext_conyuge           = $sum_ext_conyuge + $value->extension_conyuge;
                    $sum_acum_fondo_reserva    = $sum_acum_fondo_reserva + $value->fondo_reserva_acum;
                    $sum_acum_decimo_tercero   = $sum_acum_decimo_tercero + $value->decimo_tercero_acum;
                    $sum_acum_decimo_cuarto    = $sum_acum_decimo_cuarto + $value->decimo_cuarto_acum;
                    $sum_valor_vacaciones  = $sum_valor_vacaciones + $value->valor_vacacion;


                    $i = $i + 1;
                }

                $txtcolor = '#000000';
                $sheet->cell('A' . $i, function ($cell) use ($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('TOTALES');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('A' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('B'.$i, function($cell) use($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('B'.$i, function($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
            
                });
                $sheet->cell('C'.$i, function($cell) use($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('C'.$i, function($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
            
                });
                $sheet->cell('D'.$i, function($cell) use($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('D'.$i, function($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
            
                });
                $sheet->cell('E' . $i, function ($cell) use ($sum_salario, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_salario);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('E' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('F' . $i, function ($cell) use ($sum_baseiess, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_baseiess);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('F' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('G' . $i, function ($cell) use ($sum_bono, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_bono);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('G' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('H' . $i, function ($cell) use ($sum_alimentacion, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_alimentacion);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('H' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('I' . $i, function ($cell) use ($sum_transporte, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_transporte);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('I' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('J' . $i, function ($cell) use ($sum_fondo_reserva, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_fondo_reserva);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('J' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('K' . $i, function ($cell) use ($sum_decimo_cuarto, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_decimo_cuarto);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('K' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('L' . $i, function ($cell) use ($sum_total_ingreso, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_total_ingreso);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('L' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('M' . $i, function ($cell) use ($sum_iess, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_iess);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('M' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('N' . $i, function ($cell) use ($sum_seg_priv, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_seg_priv);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('N' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('O' . $i, function ($cell) use ($sum_imp_renta, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_imp_renta);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('O' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('P' . $i, function ($cell) use ($sum_multa, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_multa);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('P' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('Q' . $i, function ($cell) use ($sum_prestamoempresa, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_prestamoempresa);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('Q' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('R' . $i, function ($cell) use ($sum_saldo_prestamo, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_saldo_prestamo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('R' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('S' . $i, function ($cell) use ($sum_otro_egreso, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_otro_egreso);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('S' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('T' . $i, function ($cell) use ($sum_anticipo_quincena, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_anticipo_quincena);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('T' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->cell('U' . $i, function ($cell) use ($sum_otro_anticipo, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_otro_anticipo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('U' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                //Suma Total Hipotecario
                $sheet->cell('V' . $i, function ($cell) use ($sum_total_hip, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_total_hip);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('V' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                //Suma Total Quirografario
                $sheet->cell('W' . $i, function ($cell) use ($sum_total_quir, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_total_quir);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('W' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('X' . $i, function ($cell) use ($sum_parqueo, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_parqueo);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('X' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('Y' . $i, function ($cell) use ($sum_ext_conyuge, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_ext_conyuge);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('Y' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('Z' . $i, function ($cell) use ($sum_total_egreso, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_total_egreso);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('Z' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('AA' . $i, function ($cell) use ($sum_neto_recibido, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_neto_recibido);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AA' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('AB'.$i, function($cell) use($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AB'.$i, function($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('AC'.$i, function($cell) use($txtcolor) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setAlignment('left');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AC'.$i, function($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000'); 
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                
                $sheet->cell('AD' . $i, function ($cell) use ($sum_valor_vacaciones, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_valor_vacaciones);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AD' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
		        $sheet->cell('AE' . $i, function ($cell) use ($sum_acum_fondo_reserva, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_acum_fondo_reserva);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AE' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('AF' . $i, function ($cell) use ($sum_acum_decimo_tercero, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_acum_decimo_tercero);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AF' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('AG' . $i, function ($cell) use ($sum_acum_decimo_cuarto, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_acum_decimo_cuarto);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AG' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });
                $sheet->cell('AH' . $i, function ($cell) use ($sum_vacaciones_pagadas, $txtcolor) {
                    $cell->setValue($sum_vacaciones_pagadas);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                /*$sheet->cell('AC' . $i, function ($cell) use ($sum_acum_decimo_cuarto, $txtcolor) {
                    // manipulate the cel
                    $cell->setValue($sum_acum_decimo_cuarto);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                });
                $sheet->cells('AC' . $i, function ($cells) {
                    $cells->setBackground('#D6D4D4');
                    $cells->setFontColor('#000000');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });*/
           
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(28)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(20)->setAutosize(false);

        })->export('xlsx');

    }

}
