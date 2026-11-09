<?php

namespace Sis_medico\Services;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Archivo_historico;
use Sis_medico\Documento;
use Sis_medico\Examen_Orden;
use Sis_medico\Orden;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento;
use Sis_medico\User;

class ClinicalConsolidatedDocumentReportExportService extends ClinicalDocumentReportQuery
{
    public function export(Request $request)
    {
        $proc_consul = $request['proc_consul'];
        $cedula      = $request['cedula'];
        $nombres     = $request['nombres'];
        $fecha       = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $pentax      = $request['pentax'];
        $id_doctor1  = $request['id_doctor1'];
        //$id_seguro   = $request['id_seguro'];

        //dd($request->all());

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->get();
        if ($proc_consul == 'null') {
            $proc_consul = '1';
        }

        $agendas_iess = DB::table('agenda')->join('historiaclinica as hc', 'agenda.id', 'hc.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'hc.id_seguro')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->join('empresa as e', 'e.id', 'agenda.id_empresa')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'hc.hcid', 'paciente.referido', 'paciente.parentesco', 'hc.id_doctor1 as hcdoctor', 'e.nombrecomercial')->orderby('agenda.fechaini', 'desc')->where('proc_consul', '0')->where('seguros.id', 2);
            //dd($agendas_iess);


        $agendas_msp = DB::table('agenda')->join('historiaclinica as hc', 'agenda.id', 'hc.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'hc.id_seguro')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->join('empresa as e', 'e.id', 'agenda.id_empresa')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'hc.hcid', 'paciente.referido', 'paciente.parentesco', 'hc.id_doctor1 as hcdoctor', 'e.nombrecomercial')->orderby('agenda.fechaini', 'desc')->where('proc_consul', '0')->where('seguros.id', 5);

        $agendas_isspol = DB::table('agenda')->join('historiaclinica as hc', 'agenda.id', 'hc.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'hc.id_seguro')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->join('empresa as e', 'e.id', 'agenda.id_empresa')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'hc.hcid', 'paciente.referido', 'paciente.parentesco', 'hc.id_doctor1 as hcdoctor', 'e.nombrecomercial')->orderby('agenda.fechaini', 'desc')->where('proc_consul', '0')->where('seguros.id', 6);

        $agendas_issfa = DB::table('agenda')->join('historiaclinica as hc', 'agenda.id', 'hc.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'hc.id_seguro')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->join('empresa as e', 'e.id', 'agenda.id_empresa')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'hc.hcid', 'paciente.referido', 'paciente.parentesco', 'hc.id_doctor1 as hcdoctor', 'e.nombrecomercial')->orderby('agenda.fechaini', 'desc')->where('proc_consul', '0')->where('seguros.id', 3);

        if ($proc_consul != '2') {
            $agendas_iess   = $agendas_iess->where('agenda.proc_consul', $proc_consul);
            $agendas_msp    = $agendas_msp->where('agenda.proc_consul', $proc_consul);
            $agendas_isspol = $agendas_isspol->where('agenda.proc_consul', $proc_consul);
            $agendas_issfa  = $agendas_issfa->where('agenda.proc_consul', $proc_consul);
        }
        
        if ($proc_consul == '1') {
            if ($pentax == '2') {
                $agendas_iess   = $agendas_iess->where('hospital.id', '2');
                $agendas_msp    = $agendas_msp->where('hospital.id', '2');
                $agendas_isspol = $agendas_isspol->where('hospital.id', '2');
                $agendas_issfa  = $agendas_issfa->where('hospital.id', '2');
            } elseif ($pentax == '0') {
                $agendas_iess   = $agendas_iess->where('hospital.id', '<>', '2');
                $agendas_msp    = $agendas_msp->where('hospital.id', '<>', '2');
                $agendas_isspol = $agendas_isspol->where('hospital.id', '<>', '2');
                $agendas_issfa  = $agendas_issfa->where('hospital.id', '<>', '2');
            }
        }

        if ($fecha != null && $fecha_hasta != null) {
            $agendas_iess   = $agendas_iess->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
            $agendas_msp    = $agendas_msp->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
            $agendas_isspol = $agendas_isspol->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
            $agendas_issfa  = $agendas_issfa->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        //dd($fecha . '--------------' . $fecha_hasta);
//
        if ($cedula != null) {
            $agendas_iess   = $agendas_iess->where('agenda.id_paciente', $cedula);
            $agendas_msp    = $agendas_msp->where('agenda.id_paciente', $cedula);
            $agendas_isspol = $agendas_isspol->where('agenda.id_paciente', $cedula);
            $agendas_issfa  = $agendas_issfa->where('agenda.id_paciente', $cedula);
        }

        if ($id_doctor1 != null) {
            $agendas_iess   = $agendas_iess->where('agenda.id_doctor1', $id_doctor1);
            $agendas_msp    = $agendas_msp->where('agenda.id_doctor1', $id_doctor1);
            $agendas_isspol = $agendas_isspol->where('agenda.id_doctor1', $id_doctor1);
            $agendas_issfa  = $agendas_issfa->where('agenda.id_doctor1', $id_doctor1);
        }

        /*if ($id_seguro != null) {
        $agendas_iess = $agendas_iess->where('hc.id_seguro', $id_seguro);
        }*/

        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $agendas_iess = $agendas_iess->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
                $agendas_msp = $agendas_msp->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
                $agendas_isspol = $agendas_isspol->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
                $agendas_issfa = $agendas_issfa->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });
            } else {

                $agendas_iess   = $agendas_iess->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                $agendas_msp    = $agendas_msp->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                $agendas_isspol = $agendas_isspol->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                $agendas_issfa  = $agendas_issfa->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }
        }

        $agendas_iess   = $agendas_iess->get();
        $agendas_msp    = $agendas_msp->get();
        $agendas_isspol = $agendas_isspol->get();
        $agendas_issfa  = $agendas_issfa->get();

        //dd($agendas_iess);

        $fecha_d = date('Y/m/d');

        $documentos_iess   = $this->carga_documentos_rpt('2')->get();
        $documentos_msp    = $this->carga_documentos_rpt('5')->get();
        $documentos_isspol = $this->carga_documentos_rpt('6')->get();
        $documentos_issfa  = $this->carga_documentos_rpt('3')->get();
        //dd($documentos_issfa);
        $cant_doc = 0;

        Excel::create('Documentos-' . $fecha_d, function ($excel) use ($agendas_iess, $agendas_msp, $agendas_isspol, $agendas_issfa, $fecha, $documentos_iess, $documentos_msp, $documentos_isspol, $documentos_issfa, $cant_doc) {

            $excel->sheet('IESS', function ($sheet) use ($agendas_iess, $agendas_msp, $agendas_isspol, $agendas_issfa, $fecha, $documentos_iess, $documentos_msp, $documentos_isspol, $documentos_issfa, $cant_doc) {
                $cant_doc = $documentos_iess->count();
                $letras   = ['Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF'];

                $letras2 = ['Q', 'R'];

                $letras3 = ['E', 'F', 'G', 'H'];

                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A2:R2');

                //$sheet->mergeCells('A3:' . $letras[ $cant_doc - 1] . '3');

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
                $sheet->cell('A2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('CONSULTA DOCUMENTOS IESS' . ' - ' . $fecha2);

                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:' . $letras[$cant_doc + 1] . '4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                    //$cells->setBackground('#FFFF00');
                });

                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MOTIVO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I3:J3');
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PENTAX');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K3:L3');
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FUNCIONALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('M3:N3');
                $sheet->cell('M3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('O4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('P4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*  $sheet->cell('J3', function ($cell) {
                // manipulate the cel
                $cell->setValue('ORDENES LABS');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                // manipulate the cel
                $cell->setValue('FECHA');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                 */
                $nl = 0;
                foreach ($documentos_iess as $documento) {
                    if ($documento->id == '1' || $documento->id == '60' || $documento->id == '6') {
                        $sheet->cell($letras3[$nl] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nl++;
                    }
                }

                $nletra = 0;
                foreach ($documentos_iess as $documento) {
                    if ($documento->id == '3' || $documento->id == '32') {
                        $sheet->cell($letras2[$nletra] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nletra++;
                    }
                }

                foreach ($agendas_iess as $value) {

                    $txtcolor = '#FFFFFF';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

                    $fecha_agenda = date('Y-m-d', strtotime($value->fechaini));

                    $orden_labs = Examen_Orden::where('id_paciente', $value->id_paciente)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor_ieced', $value->hcdoctor)
                        ->where('estado', 1)
                        ->first();

                    $orden = Orden::where('id_paciente', $value->id_paciente)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->first();

                    $orden_imagen = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 2)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $orden_funcional = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 1)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $orden_endoscopicos = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 0)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 11, 5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        //$cell->setFontColor($txtcolor);
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if (!is_null($value->probservacion)) {
                        $vproc = $value->probservacion;
                    } else {
                        $vproc = 'Consulta';
                    }
                    $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
                    if (!$agprocedimientos->isEmpty()) {
                        foreach ($agprocedimientos as $agendaproc) {
                            $vproc = $vproc . ' + ' . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                        }
                    }

                    if ($value->proc_consul == '0') {
                        $txtpproc = "CONSULTA";
                    } else {

                        $txtpproc = "";
                        if (!is_null($value->pxid)) {
                            $pentaxprocedimientos = PentaxProc::where('id_pentax', $value->pxid)->get();
                            //dd($pentaxprocedimientos);
                            if (!is_null($pentaxprocedimientos)) {
                                $ban = '0';
                                foreach ($pentaxprocedimientos as $proc) {
                                    if ($ban == '0') {
                                        $txtpproc = Procedimiento::find($proc->id_procedimiento)->observacion;
                                        $ban      = '1';
                                    } else {
                                        $txtpproc = $txtpproc . ' + ' . Procedimiento::find($proc->id_procedimiento)->observacion;
                                    }
                                }
                            }
                        } else {
                            $txtpproc = $vproc;
                        }
                    }
                    if (!is_null($orden)) {
                        $sheet->cell('D' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    } else {
                        $sheet->cell('D' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    $nl = 0;
                    foreach ($documentos_iess as $documento) {
                        if ($documento->id == '1' || $documento->id == '60' || $documento->id == '6') {
                            // code...

                            $sheet->cell($letras3[$nl] . $i, function ($cell) use ($documento, $value) {
                                // manipulate the cel
                                $doc_ok = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento->id)->where('estado', '1')->first();

                                if (!is_null($doc_ok)) {
                                    $cell->setValue('X');
                                } else {
                                    if ($documento->rpt_prin == 1) {
                                        $documentos2 = Documento::where('rpt_prin', $documento->id)->get();
                                        $flag_ok2    = false;
                                        foreach ($documentos2 as $documento2) {
                                            if (!$flag_ok2) {
                                                $doc_ok2 = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento2->id)->where('estado', '1')->first();
                                                if (!is_null($doc_ok2)) {
                                                    $flag_ok2 = true;
                                                }
                                            }
                                        }
                                        if ($flag_ok2) {
                                            $cell->setValue('X');
                                        }
                                    }
                                }

                                $cell->setAlignment('center');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $nl++;

                        }
                    }

                    $sheet->cell('H' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('X');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    if (!is_null($orden_endoscopicos)) {

                        $txt_oe        = 'CONSULTA';
                        $cont          = 0;
                        $fecha_ordenoe = '';

                        foreach ($orden_endoscopicos as $oe) {
                            if ($cont < 2) {
                                if ($cont == 0) {
                                    $txt_oe = $oe->nombre_proc;
                                } else {
                                    $txt_oe = $txt_oe . ' + ' . $oe->nombre_proc;
                                }
                                $cont++;
                            }
                            $fecha_ordenoe = substr($oe->fecha_orden, 0, 10);
                        }

                        $sheet->cell('I' . $i, function ($cell) use ($txt_oe) {
                            // manipulate the cel
                            $cell->setValue($txt_oe);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($fecha_ordenoe) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenoe);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('I' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CONSULTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('J' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    if (!is_null($orden_funcional)) {

                        $txt_of        = '';
                        $cont          = 0;
                        $fecha_ordenof = '';

                        foreach ($orden_funcional as $of) {
                            if ($cont < 2) {
                                if ($cont == 0) {$txt_of = $of->nombre_proc;} else { $txt_of = $txt_of . ' + ' . $of->nombre_proc;}
                                $cont++;
                            }
                            $fecha_ordenof = substr($of->fecha_orden, 0, 10);
                        }
                        $sheet->cell('K' . $i, function ($cell) use ($txt_of) {
                            // manipulate the cel
                            $cell->setValue($txt_of);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('L' . $i, function ($cell) use ($fecha_ordenof) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenof);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('K' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('-');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('L' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    if (!is_null($orden_imagen)) {

                        $txt_oi        = '';
                        $cont          = 0;
                        $fecha_ordenoi = '';

                        foreach ($orden_imagen as $oi) {
                            if ($cont < 2) {
                                if ($cont == 0) {$txt_oi = $oi->nombre_proc;} else { $txt_oi = $txt_oi . ' + ' . $oi->nombre_proc;}
                                $cont++;
                            }

                            $fecha_ordenoi = substr($oi->fecha_orden, 0, 10);

                        }

                        if (!is_null($orden_labs)) {
                            $txt_oi = 'LABORATORIO + ' . $txt_oi;
                        }

                        if ($txt_oi == '') {
                            if (!is_null($orden_labs)) {
                                $txt_oi = 'LABORATORIO';
                            }
                        }

                        $sheet->cell('M' . $i, function ($cell) use ($txt_oi) {
                            // manipulate the cel
                            $cell->setValue($txt_oi);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($fecha_ordenoi) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenoi);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('M' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('N' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->dapellido1 . ' ' . $value->dnombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombrecomercial);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    /*if (!is_null($orden_labs)) {

                    $sheet->cell('J' . $i, function ($cell) use ($orden_labs) {
                    // manipulate the cel
                    $cell->setValue($orden_labs->fecha_orden);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setFontColor($txtcolor);
                    });

                    } else {
                    $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setFontColor($txtcolor);
                    });

                    }*/

                    $nletra = 0;
                    foreach ($documentos_iess as $documento) {
                        if ($documento->id == '3' || $documento->id == '32') {
                            // code...

                            $sheet->cell($letras2[$nletra] . $i, function ($cell) use ($documento, $value) {
                                // manipulate the cel
                                $doc_ok = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento->id)->where('estado', '1')->first();

                                if (!is_null($doc_ok)) {
                                    $cell->setValue('X');
                                } else {
                                    if ($documento->rpt_prin == 1) {
                                        $documentos2 = Documento::where('rpt_prin', $documento->id)->get();
                                        $flag_ok2    = false;
                                        foreach ($documentos2 as $documento2) {
                                            if (!$flag_ok2) {
                                                $doc_ok2 = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento2->id)->where('estado', '1')->first();
                                                if (!is_null($doc_ok2)) {
                                                    $flag_ok2 = true;
                                                }
                                            }
                                        }
                                        if ($flag_ok2) {
                                            $cell->setValue('X');
                                        }
                                    }
                                }

                                $cell->setAlignment('center');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $nletra++;

                        }
                    }

                    $i = $i + 1;
                }
            });

            $excel->sheet('MSP', function ($sheet) use ($agendas_iess, $agendas_msp, $agendas_isspol, $agendas_issfa, $fecha, $documentos_iess, $documentos_msp, $documentos_isspol, $documentos_issfa, $cant_doc) {
                $cant_doc = $documentos_msp->count();
                $letras   = ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

                $letras2 = ['Q', 'R'];

                $letras3 = ['E', 'F'];

                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A2:' . $letras[$cant_doc - 1] . '2');

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
                $sheet->cell('A2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('CONSULTA DOCUMENTOS POR SEGURO' . ' - ' . $fecha2);

                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:' . $letras[$cant_doc + 1] . '4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                    //$cells->setBackground('#FFFF00');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CARNET');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MOTIVO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I3:J3');
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PENTAX');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K3:L3');
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FUNCIONALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('M3:N3');
                $sheet->cell('M3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('O4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('P4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*  $sheet->cell('J3', function ($cell) {
                // manipulate the cel
                $cell->setValue('ORDENES LABS');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                // manipulate the cel
                $cell->setValue('FECHA');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                 */
                $nl = 0;
                foreach ($documentos_msp as $documento) {
                    if ($documento->id == '1' || $documento->id == '60') {
                        $sheet->cell($letras3[$nl] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nl++;
                    }
                }

                $nletra = 0;
                foreach ($documentos_msp as $documento) {
                    if ($documento->id == '3' || $documento->id == '32') {
                        $sheet->cell($letras2[$nletra] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nletra++;
                    }
                }

                /*$nletra = 0;
                foreach ($documentos_msp as $documento) {
                $sheet->cell($letras[$nletra] . '4', function ($cell) use ($documento) {
                // manipulate the cel
                $cell->setValue($documento->nombre_corto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $nletra++;
                }*/

                foreach ($agendas_msp as $value) {
                    $txtcolor = '#FFFFFF';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

                    $fecha_agenda = date('Y-m-d', strtotime($value->fechaini));

                    $orden_labs = Examen_Orden::where('id_paciente', $value->id_paciente)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor_ieced', $value->hcdoctor)
                        ->where('estado', 1)
                        ->first();

                    $orden = Orden::where('id_paciente', $value->id_paciente)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->first();

                    $orden_imagen = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 2)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $orden_funcional = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 1)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $orden_endoscopicos = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 0)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 11, 5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        //$cell->setFontColor($txtcolor);
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if (!is_null($value->probservacion)) {
                        $vproc = $value->probservacion;
                    } else {
                        $vproc = 'Consulta';
                    }
                    $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
                    if (!$agprocedimientos->isEmpty()) {
                        foreach ($agprocedimientos as $agendaproc) {
                            $vproc = $vproc . ' + ' . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                        }
                    }

                    if ($value->proc_consul == '0') {
                        $txtpproc = "CONSULTA";
                    } else {

                        $txtpproc = "";
                        if (!is_null($value->pxid)) {
                            $pentaxprocedimientos = PentaxProc::where('id_pentax', $value->pxid)->get();
                            //dd($pentaxprocedimientos);
                            if (!is_null($pentaxprocedimientos)) {
                                $ban = '0';
                                foreach ($pentaxprocedimientos as $proc) {
                                    if ($ban == '0') {
                                        $txtpproc = Procedimiento::find($proc->id_procedimiento)->observacion;
                                        $ban      = '1';
                                    } else {
                                        $txtpproc = $txtpproc . ' + ' . Procedimiento::find($proc->id_procedimiento)->observacion;
                                    }
                                }
                            }
                        } else {
                            $txtpproc = $vproc;
                        }
                    }

                    if (!is_null($orden)) {
                        $sheet->cell('D' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    } else {
                        $sheet->cell('D' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    $nl = 0;
                    foreach ($documentos_msp as $documento) {
                        if ($documento->id == '1' || $documento->id == '60') {
                            // code...

                            $sheet->cell($letras3[$nl] . $i, function ($cell) use ($documento, $value) {
                                // manipulate the cel
                                $doc_ok = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento->id)->where('estado', '1')->first();

                                if (!is_null($doc_ok)) {
                                    $cell->setValue('X');
                                } else {
                                    if ($documento->rpt_prin == 1) {
                                        $documentos2 = Documento::where('rpt_prin', $documento->id)->get();
                                        $flag_ok2    = false;
                                        foreach ($documentos2 as $documento2) {
                                            if (!$flag_ok2) {
                                                $doc_ok2 = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento2->id)->where('estado', '1')->first();
                                                if (!is_null($doc_ok2)) {
                                                    $flag_ok2 = true;
                                                }
                                            }
                                        }
                                        if ($flag_ok2) {
                                            $cell->setValue('X');
                                        }
                                    }
                                }

                                $cell->setAlignment('center');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $nl++;

                        }
                    }

                    $sheet->cell('H' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('x');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        //$cell->setFontColor($txtcolor);
                    });

                    if (!is_null($orden_endoscopicos)) {

                        $txt_oe        = 'CONSULTA';
                        $cont          = 0;
                        $fecha_ordenoe = '';

                        foreach ($orden_endoscopicos as $oe) {
                            if ($cont < 2) {
                                if ($cont == 0) {
                                    $txt_oe = $oe->nombre_proc;
                                } else {
                                    $txt_oe = $txt_oe . ' + ' . $oe->nombre_proc;
                                }
                                $cont++;
                            }
                            $fecha_ordenoe = substr($oe->fecha_orden, 0, 10);
                        }

                        $sheet->cell('I' . $i, function ($cell) use ($txt_oe) {
                            // manipulate the cel
                            $cell->setValue($txt_oe);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($fecha_ordenoe) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenoe);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('I' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CONSULTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('J' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    if (!is_null($orden_funcional)) {

                        $txt_of        = '';
                        $cont          = 0;
                        $fecha_ordenof = '';

                        foreach ($orden_funcional as $of) {
                            if ($cont < 2) {
                                if ($cont == 0) {$txt_of = $of->nombre_proc;} else { $txt_of = $txt_of . ' + ' . $of->nombre_proc;}
                                $cont++;
                            }
                            $fecha_ordenof = substr($of->fecha_orden, 0, 10);
                        }
                        $sheet->cell('K' . $i, function ($cell) use ($txt_of) {
                            // manipulate the cel
                            $cell->setValue($txt_of);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('L' . $i, function ($cell) use ($fecha_ordenof) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenof);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('K' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('-');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('L' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    if (!is_null($orden_imagen)) {

                        $txt_oi        = '';
                        $cont          = 0;
                        $fecha_ordenoi = '';

                        foreach ($orden_imagen as $oi) {
                            if ($cont < 2) {
                                if ($cont == 0) {$txt_oi = $oi->nombre_proc;} else { $txt_oi = $txt_oi . ' + ' . $oi->nombre_proc;}
                                $cont++;
                            }

                            $fecha_ordenoi = substr($oi->fecha_orden, 0, 10);

                        }

                        if (!is_null($orden_labs)) {
                            $txt_oi = 'LABORATORIO + ' . $txt_oi;
                        }

                        if ($txt_oi == '') {
                            if (!is_null($orden_labs)) {
                                $txt_oi = 'LABORATORIO';
                            }
                        }

                        $sheet->cell('M' . $i, function ($cell) use ($txt_oi) {
                            // manipulate the cel
                            $cell->setValue($txt_oi);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($fecha_ordenoi) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenoi);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('M' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('N' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->dapellido1 . ' ' . $value->dnombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombrecomercial);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    /*

                    if (!is_null($orden_labs)) {

                    $sheet->cell('J' . $i, function ($cell) use ($orden_labs) {
                    // manipulate the cel
                    $cell->setValue($orden_labs->fecha_orden);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setFontColor($txtcolor);
                    });

                    } else {
                    $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setFontColor($txtcolor);
                    });

                    }*/

                    $nletra = 0;
                    foreach ($documentos_msp as $documento) {
                        if ($documento->id == '3' || $documento->id == '32') {
                            $sheet->cell($letras[$nletra] . $i, function ($cell) use ($documento, $value) {
                                // manipulate the cel
                                $doc_ok = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento->id)->where('estado', '1')->first();

                                if (!is_null($doc_ok)) {
                                    $cell->setValue('X');
                                } else {
                                    if ($documento->rpt_prin == 1) {
                                        $documentos2 = Documento::where('rpt_prin', $documento->id)->get();
                                        $flag_ok2    = false;
                                        foreach ($documentos2 as $documento2) {
                                            if (!$flag_ok2) {
                                                $doc_ok2 = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento2->id)->where('estado', '1')->first();
                                                if (!is_null($doc_ok2)) {
                                                    $flag_ok2 = true;
                                                }
                                            }
                                        }
                                        if ($flag_ok2) {
                                            $cell->setValue('X');
                                        }
                                    }
                                }

                                $cell->setAlignment('center');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $nletra++;
                        }
                    }

                    $i = $i + 1;
                }
            });

            $excel->sheet('ISSPOL', function ($sheet) use ($agendas_iess, $agendas_msp, $agendas_isspol, $agendas_issfa, $fecha, $documentos_iess, $documentos_msp, $documentos_isspol, $documentos_issfa, $cant_doc) {
                $cant_doc = $documentos_isspol->count();
                $letras   = ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

                $letras2 = ['Q', 'R'];

                $letras3 = ['E', 'F', 'G', 'H'];

                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A2:' . $letras[$cant_doc - 1] . '2');

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
                $sheet->cell('A2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('CONSULTA DOCUMENTOS ISSPOL' . ' - ' . $fecha2);

                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:' . $letras[$cant_doc + 1] . '4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                    //$cells->setBackground('#FFFF00');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CARNET');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MOTIVO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I3:J3');
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PENTAX');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K3:L3');
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FUNCIONALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('M3:N3');
                $sheet->cell('M3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('O4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('P4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*  $sheet->cell('J3', function ($cell) {
                // manipulate the cel
                $cell->setValue('ORDENES LABS');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                // manipulate the cel
                $cell->setValue('FECHA');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                 */
                $nl = 0;
                foreach ($documentos_isspol as $documento) {
                    if ($documento->id == '1' || $documento->id == '60') {
                        $sheet->cell($letras3[$nl] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nl++;
                    }
                }

                $nletra = 0;
                foreach ($documentos_isspol as $documento) {
                    if ($documento->id == '3' || $documento->id == '32') {
                        $sheet->cell($letras2[$nletra] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nletra++;
                    }
                }

                /*

                $nletra = 0;
                foreach ($documentos_isspol as $documento) {
                $sheet->cell($letras[$nletra] . '4', function ($cell) use ($documento) {
                // manipulate the cel
                $cell->setValue($documento->nombre_corto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $nletra++;
                }*/

                foreach ($agendas_isspol as $value) {
                    $txtcolor = '#FFFFFF';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

                    $fecha_agenda = date('Y-m-d', strtotime($value->fechaini));

                    $orden_labs = Examen_Orden::where('id_paciente', $value->id_paciente)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor_ieced', $value->hcdoctor)
                        ->where('estado', 1)
                        ->first();

                    $orden = Orden::where('id_paciente', $value->id_paciente)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->first();

                    $orden_imagen = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 2)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $orden_funcional = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 1)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $orden_endoscopicos = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 0)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 11, 5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        //$cell->setFontColor($txtcolor);
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if (!is_null($value->probservacion)) {
                        $vproc = $value->probservacion;
                    } else {
                        $vproc = 'Consulta';
                    }
                    $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
                    if (!$agprocedimientos->isEmpty()) {
                        foreach ($agprocedimientos as $agendaproc) {
                            $vproc = $vproc . ' + ' . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                        }
                    }

                    if ($value->proc_consul == '0') {
                        $txtpproc = "CONSULTA";
                    } else {

                        $txtpproc = "";
                        if (!is_null($value->pxid)) {
                            $pentaxprocedimientos = PentaxProc::where('id_pentax', $value->pxid)->get();
                            //dd($pentaxprocedimientos);
                            if (!is_null($pentaxprocedimientos)) {
                                $ban = '0';
                                foreach ($pentaxprocedimientos as $proc) {
                                    if ($ban == '0') {
                                        $txtpproc = Procedimiento::find($proc->id_procedimiento)->observacion;
                                        $ban      = '1';
                                    } else {
                                        $txtpproc = $txtpproc . ' + ' . Procedimiento::find($proc->id_procedimiento)->observacion;
                                    }
                                }
                            }
                        } else {
                            $txtpproc = $vproc;
                        }
                    }

                    if (!is_null($orden)) {
                        $sheet->cell('D' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    } else {
                        $sheet->cell('D' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    $nl = 0;
                    foreach ($documentos_isspol as $documento) {
                        if ($documento->id == '1' || $documento->id == '60') {
                            // code...

                            $sheet->cell($letras3[$nl] . $i, function ($cell) use ($documento, $value) {
                                // manipulate the cel
                                $doc_ok = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento->id)->where('estado', '1')->first();

                                if (!is_null($doc_ok)) {
                                    $cell->setValue('X');
                                } else {
                                    if ($documento->rpt_prin == 1) {
                                        $documentos2 = Documento::where('rpt_prin', $documento->id)->get();
                                        $flag_ok2    = false;
                                        foreach ($documentos2 as $documento2) {
                                            if (!$flag_ok2) {
                                                $doc_ok2 = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento2->id)->where('estado', '1')->first();
                                                if (!is_null($doc_ok2)) {
                                                    $flag_ok2 = true;
                                                }
                                            }
                                        }
                                        if ($flag_ok2) {
                                            $cell->setValue('X');
                                        }
                                    }
                                }

                                $cell->setAlignment('center');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $nl++;

                        }
                    }

                    $sheet->cell('G' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('H' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('X');
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    if (!is_null($orden_endoscopicos)) {

                        $txt_oe        = 'CONSULTA';
                        $cont          = 0;
                        $fecha_ordenoe = '';

                        foreach ($orden_endoscopicos as $oe) {
                            if ($cont < 2) {
                                if ($cont == 0) {
                                    $txt_oe = $oe->nombre_proc;
                                } else {
                                    $txt_oe = $txt_oe . ' + ' . $oe->nombre_proc;
                                }
                                $cont++;
                            }
                            $fecha_ordenoe = substr($oe->fecha_orden, 0, 10);
                        }

                        $sheet->cell('I' . $i, function ($cell) use ($txt_oe) {
                            // manipulate the cel
                            $cell->setValue($txt_oe);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($fecha_ordenoe) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenoe);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('I' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CONSULTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('J' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    if (!is_null($orden_funcional)) {

                        $txt_of        = '';
                        $cont          = 0;
                        $fecha_ordenof = '';

                        foreach ($orden_funcional as $of) {
                            if ($cont < 2) {
                                if ($cont == 0) {$txt_of = $of->nombre_proc;} else { $txt_of = $txt_of . ' + ' . $of->nombre_proc;}
                                $cont++;
                            }
                            $fecha_ordenof = substr($of->fecha_orden, 0, 10);
                        }
                        $sheet->cell('K' . $i, function ($cell) use ($txt_of) {
                            // manipulate the cel
                            $cell->setValue($txt_of);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('L' . $i, function ($cell) use ($fecha_ordenof) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenof);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('K' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('-');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('L' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    if (!is_null($orden_imagen)) {

                        $txt_oi        = '';
                        $cont          = 0;
                        $fecha_ordenoi = '';

                        foreach ($orden_imagen as $oi) {
                            if ($cont < 2) {
                                if ($cont == 0) {$txt_oi = $oi->nombre_proc;} else { $txt_oi = $txt_oi . ' + ' . $oi->nombre_proc;}
                                $cont++;
                            }

                            $fecha_ordenoi = substr($oi->fecha_orden, 0, 10);

                        }

                        if (!is_null($orden_labs)) {
                            $txt_oi = 'LABORATORIO + ' . $txt_oi;
                        }

                        if ($txt_oi == '') {
                            if (!is_null($orden_labs)) {
                                $txt_oi = 'LABORATORIO';
                            }
                        }

                        $sheet->cell('M' . $i, function ($cell) use ($txt_oi) {
                            // manipulate the cel
                            $cell->setValue($txt_oi);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($fecha_ordenoi) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenoi);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('M' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('N' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->dapellido1 . ' ' . $value->dnombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombrecomercial);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    /*

                    if (!is_null($orden_labs)) {
                    $sheet->cell('J' . $i, function ($cell) use ($orden_labs) {
                    // manipulate the cel
                    $cell->setValue('LABORATORIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setFontColor($txtcolor);
                    });

                    } else {
                    $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setFontColor($txtcolor);
                    });

                    }*/

                    $nletra = 0;
                    foreach ($documentos_isspol as $documento) {
                        if ($documento->id == '3' || $documento->id == '32') {
                            $sheet->cell($letras[$nletra] . $i, function ($cell) use ($documento, $value) {
                                // manipulate the cel
                                $doc_ok = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento->id)->where('estado', '1')->first();

                                if (!is_null($doc_ok)) {
                                    $cell->setValue('X');
                                } else {
                                    if ($documento->rpt_prin == 1) {
                                        $documentos2 = Documento::where('rpt_prin', $documento->id)->get();
                                        $flag_ok2    = false;
                                        foreach ($documentos2 as $documento2) {
                                            if (!$flag_ok2) {
                                                $doc_ok2 = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento2->id)->where('estado', '1')->first();
                                                if (!is_null($doc_ok2)) {
                                                    $flag_ok2 = true;
                                                }
                                            }
                                        }
                                        if ($flag_ok2) {
                                            $cell->setValue('X');
                                        }
                                    }
                                }

                                $cell->setAlignment('center');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $nletra++;
                        }
                    }

                    $i = $i + 1;
                }
            });

            $excel->sheet('ISSFA', function ($sheet) use ($agendas_iess, $agendas_msp, $agendas_isspol, $agendas_issfa, $fecha, $documentos_iess, $documentos_msp, $documentos_isspol, $documentos_issfa, $cant_doc) {
                $cant_doc = $documentos_issfa->count();
                $letras   = ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

                $letras2 = ['Q', 'R'];

                $letras3 = ['E', 'F', 'G', 'H'];

                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A2:' . $letras[$cant_doc - 1] . '2');

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
                $sheet->cell('A2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('CONSULTA DOCUMENTOS ISSFA' . ' - ' . $fecha2);

                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:' . $letras[$cant_doc + 1] . '4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                    //$cells->setBackground('#FFFF00');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CARNET');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MOTIVO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('I3:J3');
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PENTAX');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('K3:L3');
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FUNCIONALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('M3:N3');
                $sheet->cell('M3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('O4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('P4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                /*  $sheet->cell('J3', function ($cell) {
                // manipulate the cel
                $cell->setValue('ORDENES LABS');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                // manipulate the cel
                $cell->setValue('FECHA');
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                 */
                $nl = 0;
                foreach ($documentos_issfa as $documento) {
                    if ($documento->id == '1' || $documento->id == '60') {
                        $sheet->cell($letras3[$nl] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nl++;
                    }
                }

                $nletra = 0;
                foreach ($documentos_issfa as $documento) {
                    if ($documento->id == '3' || $documento->id == '32') {
                        $sheet->cell($letras2[$nletra] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nletra++;
                    }
                }

                /*
                $nletra = 0;
                foreach ($documentos_issfa as $documento) {
                $sheet->cell($letras[$nletra] . '4', function ($cell) use ($documento) {
                // manipulate the cel
                $cell->setValue($documento->nombre_corto);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $nletra++;
                }*/

                foreach ($agendas_issfa as $value) {
                    $txtcolor = '#FFFFFF';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

                    $fecha_agenda = date('Y-m-d', strtotime($value->fechaini));

                    $orden_labs = Examen_Orden::where('id_paciente', $value->id_paciente)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor_ieced', $value->hcdoctor)
                        ->where('estado', 1)
                        ->first();

                    $orden = Orden::where('id_paciente', $value->id_paciente)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->first();

                    $orden_imagen = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 2)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $orden_funcional = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 1)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $orden_endoscopicos = Orden::where('id_paciente', $value->id_paciente)
                        ->where('orden.tipo_procedimiento', 0)
                        ->whereBetween('fecha_orden', array($fecha_agenda . '  0:00:00', $fecha_agenda . ' 23:59:59'))
                        ->where('id_doctor', $value->hcdoctor)
                        ->where('orden.estado', 1)
                        ->join('orden_tipo as o_tipo', 'o_tipo.id_orden', 'orden.id')
                        ->join('orden_procedimiento as ord_proc', 'ord_proc.id_orden_tipo', 'o_tipo.id')
                        ->join('procedimiento as p', 'p.id', 'ord_proc.id_procedimiento')
                        ->select('orden.*', 'p.nombre as nombre_proc')
                        ->get();

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 11, 5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        //$cell->setFontColor($txtcolor);
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if (!is_null($value->probservacion)) {
                        $vproc = $value->probservacion;
                    } else {
                        $vproc = 'Consulta';
                    }
                    $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
                    if (!$agprocedimientos->isEmpty()) {
                        foreach ($agprocedimientos as $agendaproc) {
                            $vproc = $vproc . ' + ' . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                        }
                    }

                    if ($value->proc_consul == '0') {
                        $txtpproc = "CONSULTA";
                    } else {

                        $txtpproc = "";
                        if (!is_null($value->pxid)) {
                            $pentaxprocedimientos = PentaxProc::where('id_pentax', $value->pxid)->get();
                            //dd($pentaxprocedimientos);
                            if (!is_null($pentaxprocedimientos)) {
                                $ban = '0';
                                foreach ($pentaxprocedimientos as $proc) {
                                    if ($ban == '0') {
                                        $txtpproc = Procedimiento::find($proc->id_procedimiento)->observacion;
                                        $ban      = '1';
                                    } else {
                                        $txtpproc = $txtpproc . ' + ' . Procedimiento::find($proc->id_procedimiento)->observacion;
                                    }
                                }
                            }
                        } else {
                            $txtpproc = $vproc;
                        }
                    }

                    if (!is_null($orden)) {
                        $sheet->cell('D' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    } else {
                        $sheet->cell('D' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setFontWeight('bold');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    $nl = 0;
                    foreach ($documentos_issfa as $documento) {
                        if ($documento->id == '1' || $documento->id == '60') {
                            // code...

                            $sheet->cell($letras3[$nl] . $i, function ($cell) use ($documento, $value) {
                                // manipulate the cel
                                $doc_ok = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento->id)->where('estado', '1')->first();

                                if (!is_null($doc_ok)) {
                                    $cell->setValue('X');
                                } else {
                                    if ($documento->rpt_prin == 1) {
                                        $documentos2 = Documento::where('rpt_prin', $documento->id)->get();
                                        $flag_ok2    = false;
                                        foreach ($documentos2 as $documento2) {
                                            if (!$flag_ok2) {
                                                $doc_ok2 = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento2->id)->where('estado', '1')->first();
                                                if (!is_null($doc_ok2)) {
                                                    $flag_ok2 = true;
                                                }
                                            }
                                        }
                                        if ($flag_ok2) {
                                            $cell->setValue('X');
                                        }
                                    }
                                }

                                $cell->setAlignment('center');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $nl++;

                        }
                    }

                    $sheet->cell('G' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('H' . $i, function ($cell) {
                        // manipulate the cel
                        $cell->setValue('X');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontWeight('bold');
                        //$cell->setFontColor($txtcolor);
                    });

                    if (!is_null($orden_endoscopicos)) {

                        $txt_oe        = 'CONSULTA';
                        $cont          = 0;
                        $fecha_ordenoe = '';

                        foreach ($orden_endoscopicos as $oe) {
                            if ($cont < 2) {
                                if ($cont == 0) {
                                    $txt_oe = $oe->nombre_proc;
                                } else {
                                    $txt_oe = $txt_oe . ' + ' . $oe->nombre_proc;
                                }
                                $cont++;
                            }
                            $fecha_ordenoe = substr($oe->fecha_orden, 0, 10);
                        }

                        $sheet->cell('I' . $i, function ($cell) use ($txt_oe) {
                            // manipulate the cel
                            $cell->setValue($txt_oe);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('J' . $i, function ($cell) use ($fecha_ordenoe) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenoe);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('I' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('CONSULTA');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('J' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    if (!is_null($orden_funcional)) {

                        $txt_of        = '';
                        $cont          = 0;
                        $fecha_ordenof = '';

                        foreach ($orden_funcional as $of) {
                            if ($cont < 2) {
                                if ($cont == 0) {$txt_of = $of->nombre_proc;} else { $txt_of = $txt_of . ' + ' . $of->nombre_proc;}
                                $cont++;
                            }
                            $fecha_ordenof = substr($of->fecha_orden, 0, 10);
                        }
                        $sheet->cell('K' . $i, function ($cell) use ($txt_of) {
                            // manipulate the cel
                            $cell->setValue($txt_of);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('L' . $i, function ($cell) use ($fecha_ordenof) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenof);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('K' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('-');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('L' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    if (!is_null($orden_imagen)) {

                        $txt_oi        = '';
                        $cont          = 0;
                        $fecha_ordenoi = '';

                        foreach ($orden_imagen as $oi) {
                            if ($cont < 2) {
                                if ($cont == 0) {$txt_oi = $oi->nombre_proc;} else { $txt_oi = $txt_oi . ' + ' . $oi->nombre_proc;}
                                $cont++;
                            }

                            $fecha_ordenoi = substr($oi->fecha_orden, 0, 10);

                        }

                        if (!is_null($orden_labs)) {
                            $txt_oi = 'LABORATORIO + ' . $txt_oi;
                        }

                        if ($txt_oi == '') {
                            if (!is_null($orden_labs)) {
                                $txt_oi = 'LABORATORIO';
                            }
                        }

                        $sheet->cell('M' . $i, function ($cell) use ($txt_oi) {
                            // manipulate the cel
                            $cell->setValue($txt_oi);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('N' . $i, function ($cell) use ($fecha_ordenoi) {
                            // manipulate the cel
                            $cell->setValue($fecha_ordenoi);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                    } else {
                        $sheet->cell('M' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });

                        $sheet->cell('N' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            //$cell->setFontColor($txtcolor);
                        });
                    }

                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->dapellido1 . ' ' . $value->dnombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombrecomercial);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    /*if (!is_null($orden_labs)) {

                    $sheet->cell('J' . $i, function ($cell) use ($orden_labs) {
                    // manipulate the cel
                    $cell->setValue($orden_labs->fecha_orden);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setFontColor($txtcolor);
                    });

                    } else {
                    $sheet->cell('J' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //$cell->setFontColor($txtcolor);
                    });

                    }*/

                    $nletra = 0;
                    foreach ($documentos_issfa as $documento) {

                        if ($documento->id == '3' || $documento->id == '32') {

                            $sheet->cell($letras[$nletra] . $i, function ($cell) use ($documento, $value) {
                                // manipulate the cel
                                $doc_ok = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento->id)->where('estado', '1')->first();

                                if (!is_null($doc_ok)) {
                                    $cell->setValue('X');
                                } else {
                                    if ($documento->rpt_prin == 1) {
                                        $documentos2 = Documento::where('rpt_prin', $documento->id)->get();
                                        $flag_ok2    = false;
                                        foreach ($documentos2 as $documento2) {
                                            if (!$flag_ok2) {
                                                $doc_ok2 = Archivo_historico::where('id_historia', $value->hcid)->where('id_documento', $documento2->id)->where('estado', '1')->first();
                                                if (!is_null($doc_ok2)) {
                                                    $flag_ok2 = true;
                                                }
                                            }
                                        }
                                        if ($flag_ok2) {
                                            $cell->setValue('X');
                                        }
                                    }
                                }

                                $cell->setAlignment('center');
                                $cell->setFontWeight('bold');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $nletra++;
                        }
                    }

                    $i = $i + 1;
                }
            });

        })->export('xlsx');

    }

    public function valida_existe($hcid, $id_doc)
    {

        $archivo = Archivo_historico::where('id_documento', $id_doc)->where('id_historia', $hcid)->first();

        if (!is_null($archivo)) {
            return '1';
        }

        return '0';
    }

    /* NO TE OLVIDES DE (*) LA FUNCION DE ARRIBA*/
}
