<?php

namespace Sis_medico\Services;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Archivo_historico;
use Sis_medico\Documento;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento;
use Sis_medico\User;

class ClinicalInsuranceDocumentReportExportService extends ClinicalDocumentReportQuery
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

        $agendas_iess = DB::table('agenda')->join('historiaclinica as hc', 'agenda.id', 'hc.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'hc.id_seguro')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'hc.hcid', 'paciente.referido', 'paciente.parentesco')->orderby('agenda.fechaini', 'desc')->where('proc_consul', '1')->where('seguros.id', 2);

        //dd($agenda_iess);

        $agendas_msp = DB::table('agenda')->join('historiaclinica as hc', 'agenda.id', 'hc.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'hc.id_seguro')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'hc.hcid', 'paciente.referido', 'paciente.parentesco')->orderby('agenda.fechaini', 'desc')->where('proc_consul', '1')->where('seguros.id', 5);

        $agendas_isspol = DB::table('agenda')->join('historiaclinica as hc', 'agenda.id', 'hc.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'hc.id_seguro')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'hc.hcid', 'paciente.referido', 'paciente.parentesco')->orderby('agenda.fechaini', 'desc')->where('proc_consul', '1')->where('seguros.id', 6);

        $agendas_issfa = DB::table('agenda')->join('historiaclinica as hc', 'agenda.id', 'hc.id_agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')->join('seguros', 'seguros.id', '=', 'hc.id_seguro')->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'hc.hcid', 'paciente.referido', 'paciente.parentesco')->orderby('agenda.fechaini', 'desc')->where('proc_consul', '1')->where('seguros.id', 3);

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

        $fecha_d = date('Y/m/d');

        $documentos_iess   = $this->carga_documentos_rpt('2')->get();
        $documentos_msp    = $this->carga_documentos_rpt('5')->get();
        $documentos_isspol = $this->carga_documentos_rpt('6')->get();
        $documentos_issfa  = $this->carga_documentos_rpt('3')->get();

        $cant_doc = 0;

        Excel::create('Documentos-' . $fecha_d, function ($excel) use ($agendas_iess, $agendas_msp, $agendas_isspol, $agendas_issfa, $fecha, $documentos_iess, $documentos_msp, $documentos_isspol, $documentos_issfa, $cant_doc) {

            $excel->sheet('IESS', function ($sheet) use ($agendas_iess, $agendas_msp, $agendas_isspol, $agendas_issfa, $fecha, $documentos_iess, $documentos_msp, $documentos_isspol, $documentos_issfa, $cant_doc) {
                $cant_doc = $documentos_iess->count();
                $letras   = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A2:' . $letras[$cant_doc - 1] . '2');

                $sheet->mergeCells('A3:' . $letras[$cant_doc - 1] . '3');

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
                    $cell->setValue('PROCEDIMIENTOS DOCUMENTOS IESS' . ' - ' . $fecha2);

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
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PARENTESCO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $nletra = 0;
                foreach ($documentos_iess as $documento) {
                    if (($documento->id != 40) && ($documento->id != 33) && ($documento->id != 41)) {
                        $sheet->cell($letras[$nletra] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nletra++;
                    }
                }
                $sheet->cell($letras[$nletra] . '4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ADELANTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $nletra++;
                $sheet->cell($letras[$nletra] . '4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CADUCA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                foreach ($agendas_iess as $value) {
                    $txtcolor = '#FFFFFF';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

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

                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor, $txtpproc) {
                        // manipulate the cel
                        $cell->setValue($txtpproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $txt = $value->senombre;
                        if ($value->consultorio) {
                            $txt = $txt . '-CONSULTORIO';
                        }
                        $cell->setValue($txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->referido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->parentesco);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $nletra = 0;
                    foreach ($documentos_iess as $documento) {
                        if (($documento->id != 40) && ($documento->id != 33) && ($documento->id != 41)) {
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

                    $sheet->cell($letras[$nletra] . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $txt = '';
                        if ($value->adelantado) {
                            $txt = 'ADELANTADO';
                        }
                        $cell->setValue($txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $nletra++;
                    $sheet->cell($letras[$nletra] . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue($value->fecha_val);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;
                }
            });

            $excel->sheet('MSP', function ($sheet) use ($agendas_iess, $agendas_msp, $agendas_isspol, $agendas_issfa, $fecha, $documentos_iess, $documentos_msp, $documentos_isspol, $documentos_issfa, $cant_doc) {
                $cant_doc = $documentos_msp->count();
                $letras   = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A2:' . $letras[$cant_doc - 1] . '2');

                $sheet->mergeCells('A3:' . $letras[$cant_doc - 1] . '3');

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
                    $cell->setValue('PROCEDIMIENTOS DOCUMENTOS POR SEGURO' . ' - ' . $fecha2);

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
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PARENTESCO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $nletra = 0;
                foreach ($documentos_msp as $documento) {
                    if (($documento->id != 40) && ($documento->id != 33) && ($documento->id != 41)) {
                        $sheet->cell($letras[$nletra] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nletra++;
                    }
                }
                $sheet->cell($letras[$nletra] . '4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ADELANTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $nletra++;
                $sheet->cell($letras[$nletra] . '4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CADUCA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                foreach ($agendas_msp as $value) {
                    $txtcolor = '#FFFFFF';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

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

                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor, $txtpproc) {
                        // manipulate the cel
                        $cell->setValue($txtpproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $txt = $value->senombre;
                        if ($value->consultorio) {
                            $txt = $txt . '-CONSULTORIO';
                        }
                        $cell->setValue($txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->referido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->parentesco);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $nletra = 0;
                    foreach ($documentos_msp as $documento) {
                        if (($documento->id != 40) && ($documento->id != 33) && ($documento->id != 41)) {
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
                    $sheet->cell($letras[$nletra] . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $txt = '';
                        if ($value->adelantado) {
                            $txt = 'ADELANTADO';
                        }
                        $cell->setValue($txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $nletra++;
                    $sheet->cell($letras[$nletra] . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue($value->fecha_val);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;
                }
            });

            $excel->sheet('ISSPOL', function ($sheet) use ($agendas_iess, $agendas_msp, $agendas_isspol, $agendas_issfa, $fecha, $documentos_iess, $documentos_msp, $documentos_isspol, $documentos_issfa, $cant_doc) {
                $cant_doc = $documentos_isspol->count();
                $letras   = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A2:' . $letras[$cant_doc - 1] . '2');

                $sheet->mergeCells('A3:' . $letras[$cant_doc - 1] . '3');

                $mes = substr($fecha_d, 5, 2);if ($mes == 01) {
                    $mes_letra
                    = "ENERO";}if ($mes == 02) {$mes_letra = "FEBRERO";}if
                ($mes == 03) {$mes_letra = "MARZO";}if ($mes == 04) {$mes_letra = "ABRIL";}if ($mes == 05) {
                    $mes_letra
                    = "MAYO";}if ($mes == 06) {$mes_letra = "JUNIO";}if
                ($mes == 07) {$mes_letra = "JULIO";}if ($mes == '08') {$mes_letra = "AGOSTO";}if ($mes == '09') {
                    $mes_letra
                    = "SEPTIEMBRE";}if ($mes == '10') {
                    $mes_letra
                    = "OCTUBRE";}if ($mes == '11') {
                    $mes_letra
                    = "NOVIEMBRE";}if ($mes == '12') {
                    $mes_letra
                    = "DICIEMBRE";}$fecha2 = 'FECHA: ' . substr($fecha_d, 8,
                    2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS DOCUMENTOS ISSPOL' . ' - ' . $fecha2);

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
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PARENTESCO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $nletra = 0;
                foreach ($documentos_isspol as $documento) {
                    if (($documento->id != 40) && ($documento->id != 33) && ($documento->id != 41)) {
                        $sheet->cell($letras[$nletra] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nletra++;
                    }
                }
                $sheet->cell($letras[$nletra] . '4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CADUCA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                foreach ($agendas_isspol as $value) {
                    $txtcolor = '#FFFFFF';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

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

                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor, $txtpproc) {
                        // manipulate the cel
                        $cell->setValue($txtpproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $txt = $value->senombre;
                        if ($value->consultorio) {
                            $txt = $txt . '-CONSULTORIO';
                        }
                        $cell->setValue($txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->referido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->parentesco);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $nletra = 0;
                    foreach ($documentos_isspol as $documento) {
                        if (($documento->id != 40) && ($documento->id != 33) && ($documento->id != 41)) {
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
                    $sheet->cell($letras[$nletra] . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue($value->fecha_val);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;
                }
            });

            $excel->sheet('ISSFA', function ($sheet) use ($agendas_iess, $agendas_msp, $agendas_isspol, $agendas_issfa, $fecha, $documentos_iess, $documentos_msp, $documentos_isspol, $documentos_issfa, $cant_doc) {
                $cant_doc = $documentos_issfa->count();
                $letras   = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];

                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A2:' . $letras[$cant_doc - 1] . '2');

                $sheet->mergeCells('A3:' . $letras[$cant_doc - 1] . '3');

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
                    $cell->setValue('PROCEDIMIENTOS DOCUMENTOS ISSFA' . ' - ' . $fecha2);

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
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PARENTESCO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $nletra = 0;
                foreach ($documentos_issfa as $documento) {
                    if (($documento->id != 40) && ($documento->id != 41)) {
                        $sheet->cell($letras[$nletra] . '4', function ($cell) use ($documento) {
                            // manipulate the cel
                            $cell->setValue($documento->nombre_corto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $nletra++;
                    }
                }
                $sheet->cell($letras[$nletra] . '4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CADUCA ORDEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                foreach ($agendas_issfa as $value) {
                    $txtcolor = '#FFFFFF';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;
                        }
                    }

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

                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor, $txtpproc) {
                        // manipulate the cel
                        $cell->setValue($txtpproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $txt = $value->senombre;
                        if ($value->consultorio) {
                            $txt = $txt . '-CONSULTORIO';
                        }
                        $cell->setValue($txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->referido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->parentesco);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //$cell->setFontColor($txtcolor);
                    });

                    $nletra = 0;
                    foreach ($documentos_issfa as $documento) {
                        if (($documento->id != 40) && ($documento->id != 41)) {
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
                    $sheet->cell($letras[$nletra] . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue($value->fecha_val);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;
                }
            });

        })->export('xlsx');
    }

}
