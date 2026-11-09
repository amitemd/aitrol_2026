<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Empresa;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Hc_protocolo_training;
use Sis_medico\Medicina;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Procedimiento;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\hc_receta_detalle;

class ClinicalHistoryIessReportExportService
{
    public function export(Request $request)
    {

        //return $request->all();
        $desde = $request->fecha;
        $hasta = $request->fecha_hasta;

        $fecha_d = Date('Y-m-d');
        

        $historiaclinica_pro = DB::table('historiaclinica as hc')->join('paciente as p', 'p.id', 'hc.id_paciente')->join('hc_procedimientos as hp', 'hp.id_hc', 'hc.hcid')->join('agenda as a', 'a.id', 'hc.id_agenda')->join('users as d1', 'd1.id', 'hc.id_doctor1')->leftjoin('procedimiento_completo as pp', 'pp.id', 'hp.id_procedimiento_completo')->leftjoin('grupo_procedimiento as gp', 'gp.id', 'pp.id_grupo_procedimiento')->leftjoin('hc_protocolo as pro', 'pro.id_hc_procedimientos', 'hp.id')->leftjoin('hc_receta as rta', 'rta.id_hc', 'hc.hcid')->leftjoin('users as d2', 'd2.id', 'hc.id_doctor2')->leftjoin('users as d3', 'd3.id', 'hc.id_doctor3')->join('seguros as seg', 'seg.id', 'hc.id_seguro')->leftjoin('pentax as px', 'px.hcid', 'hc.hcid')->select('hc.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'p.telefono1', 'p.telefono2', 'p.sexo', 'p.observacion', 'a.est_amb_hos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'a.proc_consul', 'gp.nombre as gpnombre', 'pp.nombre_general as ppnombregeneral', 'pro.motivo as promotivo', 'pro.hallazgos as prohallazgo', 'pro.conclusion as proconclusion', 'rta.id as receta', 'd3.apellido1 as d3apellido1', 'd3.nombre1 as d3nombre1', 'd2.apellido1 as d2apellido1', 'd2.nombre1 as d2nombre1', 'seg.nombre as seguro', 'rta.prescripcion', 'rta.rp', 'hp.id_doctor_examinador', 'hp.id_seguro as id_seguro_procedimiento', 'px.estado_pentax', 'pro.id as protocolo', 'a.id_empresa','hp.id as hc_pro')->where('hp.id_seguro', '2')->where('a.estado', '!=', '0')->where('a.proc_consul', '1')->where('a.id_doctor1','<>','4444444444')->whereBetween('a.fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])->where('hp.estado', '1')->get();

        $historiaclinica_con = DB::table('historiaclinica as hc')->join('paciente as p', 'p.id', 'hc.id_paciente')->join('hc_procedimientos as hp', 'hp.id_hc', 'hc.hcid')->join('agenda as a', 'a.id', 'hc.id_agenda')->join('users as d1', 'd1.id', 'hc.id_doctor1')->leftjoin('procedimiento_completo as pp', 'pp.id', 'hp.id_procedimiento_completo')->leftjoin('grupo_procedimiento as gp', 'gp.id', 'pp.id_grupo_procedimiento')->leftjoin('hc_protocolo as pro', 'pro.id_hc_procedimientos', 'hp.id')->leftjoin('hc_evolucion as evo', 'evo.hc_id_procedimiento', 'hp.id')->leftjoin('hc_child_pugh as chi', 'chi.id_hc_evolucion', 'evo.id')->leftjoin('hc_receta as rta', 'rta.id_hc', 'hc.hcid')->leftjoin('users as d2', 'd2.id', 'hc.id_doctor2')->leftjoin('users as d3', 'd3.id', 'hc.id_doctor3')->join('seguros as seg', 'seg.id', 'hc.id_seguro')->leftjoin('pentax as px', 'px.hcid', 'hc.hcid')->select('hc.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'p.telefono1', 'p.telefono2', 'p.sexo', 'p.observacion', 'a.est_amb_hos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'a.proc_consul', 'gp.nombre as gpnombre', 'pp.nombre_general as ppnombregeneral', 'pro.motivo as promotivo', 'pro.hallazgos as prohallazgo', 'evo.motivo as evomotivo', 'chi.examen_fisico', 'pro.conclusion as proconclusion', 'rta.id as receta', 'd3.apellido1 as d3apellido1', 'd3.nombre1 as d3nombre1', 'd2.apellido1 as d2apellido1', 'd2.nombre1 as d2nombre1', 'seg.nombre as seguro', 'rta.prescripcion', 'rta.rp', 'hp.id_doctor_examinador', 'hp.id_seguro as id_seguro_procedimiento', 'px.estado_pentax', 'a.id_empresa', 'a.espid', 'a.estado_cita')->where('hc.id_seguro', '2')->where('a.estado', '!=', '0')->where('a.proc_consul', '=', '0')->whereBetween('a.fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])->get();

  
        Excel::create('Historiaclinica-' . $fecha_d, function ($excel) use ($historiaclinica_pro, $historiaclinica_con, $fecha_d) {

            $excel->sheet('Historiaclinica', function ($sheet) use ($historiaclinica_pro, $historiaclinica_con, $fecha_d) {

                //$sheet->getDefaultColumnDimension('R')->setAutosize(false);
                //$sheet->getDefaultColumnDimension('R')->setWidth(5);
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');;
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA NACIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('SEXO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACION SI/NO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('I1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ALERGIA SI/NO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('J1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ALERGIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HOSPITALIZADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO PRINCIPAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO ASISTENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO CONSULTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MOTIVO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('R1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HALLAZGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('S1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DIAGNOSTICO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                //DIRECCIONES Y TELEFONOS
                $sheet->cell('T1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('U1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TEMPERATURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('V1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('W1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTATURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('X1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMC');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Y1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMC VAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Z1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GCT');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AA1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PESO IDEAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AB1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PERIMETRO ABDOMINAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AC1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RP');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AD1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AE1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTE 1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AF1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTE 2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AG1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AH1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AI1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DR.TRAINING');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = 2;
                foreach ($historiaclinica_pro as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->apellido2 != "(N/A)") {
                            $vapellido = $value->apellido1 . ' ' . $value->apellido2;
                        } else {
                            $vapellido = $value->apellido1;
                        }

                        $cell->setValue($vapellido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        if ($value->nombre2 != "(N/A)") {
                            $vnombre = $value->nombre1 . ' ' . $value->nombre2;
                        } else {
                            $vnombre = $value->nombre1;
                        }

                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->fecha_nacimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->sexo == '2') {
                            $cell->setValue('FEMENINO');

                        } else {
                            $cell->setValue('MASCULINO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->telefono1 . '/' . $value->telefono2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->observacion != null) {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue($value->observacion);

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $alergiasxpac = null;
                    $txt_ale      = '';
                    $alergiasxpac = Paciente_Alergia::where('id_paciente', $value->id_paciente)->get();
                    if (!is_null($alergiasxpac)) {

                        foreach ($alergiasxpac as $ale_pac) {
                            $txt_ale = $txt_ale . ' ' . $ale_pac->principio_activo->nombre;
                        }

                    }

                    $sheet->cell('I' . $i, function ($cell) use ($value, $txt_ale) {
                        // manipulate the cel

                        if ($txt_ale != '') {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value, $txt_ale) {
                        // manipulate the cel

                        $cell->setValue($txt_ale);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue(substr($value->created_at, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->est_amb_hos == '1') {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->id_empresa == '1307189140001') {
                            $cell->setValue('ROBLES CARLOS');
                        } else {
                            if ($value->id_doctor_examinador == null) {
                                $cell->setValue($value->d1apellido1 . ' ' . $value->d1nombre1);

                            } else {
                                $doctor = User::find($value->id_doctor_examinador);
                                $cell->setValue($doctor->apellido1 . ' ' . $doctor->nombre1);

                            }

                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->id_empresa != '1307189140001') {
                            $cell->setValue(' ');
                        } else {

                            if ($value->id_doctor_examinador == null) {
                                if ($value->id_doctor1 == '1307189140') {
                                    $cell->setValue(' ');
                                } else {
                                    $cell->setValue($value->d1apellido1 . ' ' . $value->d1nombre1);
                                }
                                $cell->setValue($value->d1apellido1 . ' ' . $value->d1nombre1);

                            } else {
                                if ($value->id_doctor_examinador == '1307189140') {
                                    $cell->setValue(' ');
                                } else {
                                    $doctor = User::find($value->id_doctor_examinador);
                                    $cell->setValue($doctor->apellido1 . ' ' . $doctor->nombre1);
                                }

                            }

                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $tipo = 'PROCEDIMIENTO';
                        if ($value->proc_consul == '1') {
                            $tipo = $value->gpnombre;
                        } else {
                            $tipo = 'CONSULTA';
                        }
                        if ($tipo == '') {
                            $tipo = 'PROCEDIMIENTO';
                        }

                        $cell->setValue($tipo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $pnombre = 'NO INGRESADO';
                        if ($value->proc_consul == '1') {
                            $pnombre = $value->ppnombregeneral;
                        } else {
                            $pnombre = 'CONSULTA';
                        }
                        if ($pnombre == '') {
                            //$pnombre = 'NO INGRESADO';
                            $proc_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $value->hc_pro)->get();
                            $fl_pf      = 1;
                            foreach ($proc_final as $pf) {
                                if ($fl_pf) {
                                    $pnombre = Procedimiento::where('id', $pf->id_procedimiento)->first()->nombre;
                                    $fl_pf   = 0;
                                } else {
                                    $pnombre = $pnombre . ' + ' . Procedimiento::where('id', $pf->id_procedimiento)->first()->nombre;
                                }
                            }
                        }
                        $cell->setValue($pnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('Q' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $motivo = '';
                        if ($value->proc_consul == '1') {
                            $motivo = $value->promotivo;
                        } else {
                            //$motivo = $value->evomotivo;
                        }
                        $cell->setValue($motivo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('R' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $hallazgo = '';
                        if ($value->proc_consul == '1') {
                            $hallazgo = $value->prohallazgo;
                            $hallazgo = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($hallazgo)), ENT_QUOTES, "UTF-8");
                        } else {
                            //$hallazgo = $value->examen_fisico;
                        }
                        $cell->setValue($hallazgo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('S' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $conclusion = '';
                        if ($value->proc_consul == '1') {
                            $conclusion = $value->proconclusion;
                            $conclusion = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($conclusion)), ENT_QUOTES, "UTF-8");
                        } else {
                            $conclusion = '';
                        }
                        $cell->setValue($conclusion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('T' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->presion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('U' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->temperatura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('V' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->peso);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('W' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->altura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('X' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $gct   = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        $texto = "";
                        if ($imc < 16) {
                            $texto = "Desnutrición";
                        } else if ($imc < 18) {
                            $texto = "Bajo de Peso";
                        } else if ($imc < 25) {
                            $texto = "Normal";
                        } else if ($imc < 27) {
                            $texto = "Sobrepeso";
                        } else if ($imc < 30) {
                            $texto = "Obesidad Tipo 1";
                        } else if ($imc < 40) {
                            $texto = "Obesidad Clinica";
                        } else {
                            $texto = "Obesidad Mordida";
                        }
                        $cell->setValue($texto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('Y' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $cell->setValue($imc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('Z' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }

                        if ($imc == 0) {
                            $gct = 0;
                        } else {
                            $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        }
                        $cell->setValue($gct);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('AA' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        $cell->setValue($peso_ideal);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('AB' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->perimetro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $receta  = '';
                    $receta2 = '';

                    if ($value->receta != null) {

                        $detalles = hc_receta_detalle::where('id_hc_receta', $value->receta)->get();

                        foreach ($detalles as $value2) {

                            $genericos = Medicina::where('id', $value2->id_medicina)->first()->genericos;

                            $receta  = $receta . ' - ' . $value2->medicina->nombre . ' (';
                            $receta2 = $receta2 . ' - ' . $value2->medicina->nombre . ': ' . $value2->dosis;
                            foreach ($genericos as $gen) {

                                $receta = $receta . $gen->generico->nombre . ' ';
                            }
                            $receta = $receta . ' ) ' . $value2->cantidad;

                        }

                    }

                    $sheet->cell('AC' . $i, function ($cell) use ($value, $receta) {
                        $rece_1 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($value->rp)), ENT_QUOTES, "UTF-8");
                        $cell->setValue($receta . ' ' . $rece_1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('AD' . $i, function ($cell) use ($value, $receta2) {
                        $rece_2 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($value->prescripcion)), ENT_QUOTES, "UTF-8");
                        $cell->setValue($receta2 . ' ' . $rece_2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('AE' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->d2apellido1 . ' ' . $value->d2nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('AF' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->d3apellido1 . ' ' . $value->d3nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //CONVENIO
                    $sheet->cell('AG' . $i, function ($cell) use ($value) {
                        $empresa  = Empresa::find($value->id_empresa);
                        $tempresa = '';
                        if (!is_null($empresa)) {
                            $tempresa = '/' . $empresa->nombre_corto;
                        }
                        if ($value->id_seguro_procedimiento == null) {
                            $cell->setValue($value->seguro . $tempresa);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $seguro_hc_procedimiento = Seguro::find($value->id_seguro_procedimiento);
                            $cell->setValue($seguro_hc_procedimiento->nombre . $tempresa);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }

                    });

                    $sheet->cell('AH' . $i, function ($cell) use ($value) {

                        $estado = '';
                        if ($value->estado_pentax == '0') {
                            $estado = 'EN ESPERA';
                        } elseif ($value->estado_pentax == '1') {
                            $estado = 'PREPARACION';
                        }elseif ($value->estado_pentax == '-1') {
                            $estado = 'PRE ADMISION';
                        }
                         elseif ($value->estado_pentax == '2') {
                            $estado = 'EN PROCEDIMIENTO';
                        } elseif ($value->estado_pentax == '3') {
                            $estado = 'RECUPERACION';
                        } elseif ($value->estado_pentax == '4') {
                            $estado = 'ALTA';
                        } elseif ($value->estado_pentax == '5') {
                            $estado = 'SUSPENDIDO';
                        } else {
                            if ($value->proc_consul == '4') {
                                $estado = 'ALTA';
                            }
                        }

                        if($estado==''){
                            if($value->proc_consul=='4'){
                                $estado = 'ALTA';        
                            }        
                        }

                        if($value->estado_pentax < '4'){
                            if($value->prohallazgo !=null){
                                $estado = 'ALTA';        
                            }
                            if($value->proconclusion !=null){
                                $estado = 'ALTA';        
                            } 
                            $cie10 = Hc_Cie10::where('hcid',$value->hcid)->first();
                            if(!is_null($cie10)){
                                $estado = 'ALTA';      
                            }


                        }


                        $cell->setValue($estado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $tr_arr = ['I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
                    if ($value->protocolo != null) {
                        $trainin_pro = Hc_protocolo_training::where('id_hc_protocolo', $value->protocolo)->get();
                        $tr_i        = 0;
                        foreach ($trainin_pro as $tr) {
                            $sheet->cell('A' . $tr_arr[$tr_i] . $i, function ($cell) use ($tr, $tr_i) {

                                $cell->setValue($tr->doctor->apellido1 . ' ' . $tr->doctor->nombre1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                            $tr_i++;
                        }
                    }

                    $i = $i + 1;

                }

                foreach ($historiaclinica_con as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->apellido2 != "(N/A)") {
                            $vapellido = $value->apellido1 . ' ' . $value->apellido2;
                        } else {
                            $vapellido = $value->apellido1;
                        }

                        $cell->setValue($vapellido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        if ($value->nombre2 != "(N/A)") {
                            $vnombre = $value->nombre1 . ' ' . $value->nombre2;
                        } else {
                            $vnombre = $value->nombre1;
                        }

                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->fecha_nacimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->sexo == '2') {
                            $cell->setValue('FEMENINO');

                        } else {
                            $cell->setValue('MASCULINO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->telefono1 . '/' . $value->telefono2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->observacion != null) {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue($value->observacion);

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $alergiasxpac = null;
                    $txt_ale      = '';
                    $alergiasxpac = Paciente_Alergia::where('id_paciente', $value->id_paciente)->get();
                    if (!is_null($alergiasxpac)) {

                        foreach ($alergiasxpac as $ale_pac) {
                            $txt_ale = $txt_ale . ' ' . $ale_pac->principio_activo->nombre;
                        }

                    }

                    $sheet->cell('I' . $i, function ($cell) use ($value, $txt_ale) {
                        // manipulate the cel

                        if ($txt_ale != '') {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value, $txt_ale) {
                        // manipulate the cel

                        $cell->setValue($txt_ale);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue(substr($value->created_at, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->est_amb_hos == '1') {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->espid == '8') {
                            if ($value->id_doctor_examinador == null) {
                                $cell->setValue($value->d1apellido1 . ' ' . $value->d1nombre1);

                            } else {
                                $doctor = User::find($value->id_doctor_examinador);
                                $cell->setValue($doctor->apellido1 . ' ' . $doctor->nombre1);

                            }
                        } else {

                            if ($value->id_empresa == '1307189140001') {
                                $cell->setValue('ROBLES CARLOS');
                            } else {
                                if ($value->id_doctor_examinador == null) {
                                    $cell->setValue($value->d1apellido1 . ' ' . $value->d1nombre1);

                                } else {
                                    $doctor = User::find($value->id_doctor_examinador);
                                    $cell->setValue($doctor->apellido1 . ' ' . $doctor->nombre1);

                                }

                            }

                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(' ');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $tipo = 'PROCEDIMIENTO';
                        if ($value->proc_consul == '1') {
                            $tipo = $value->gpnombre;
                        } else {
                            $tipo = 'CONSULTA';
                        }
                        if ($tipo == '') {
                            $tipo = 'PROCEDIMIENTO';
                        }

                        $cell->setValue($tipo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $pnombre = 'NO INGRESADO';
                        if ($value->proc_consul == '1') {
                            $pnombre = $value->ppnombregeneral;
                        } else {
                            if ($value->espid == '8') {
                                $pnombre = 'CONSULTA CARDIOLÓGICA';
                            } else {
                                $pnombre = 'CONSULTA';
                            }

                        }
                        if ($pnombre == '') {
                            $pnombre = 'NO INGRESADO';
                        }
                        $cell->setValue($pnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('Q' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $motivo = '';
                        if ($value->proc_consul == '1') {
                            $motivo = $value->promotivo;
                        } else {
                            $motivo = $value->evomotivo;
                        }
                        $cell->setValue($motivo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('R' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $hallazgo = '';
                        if ($value->proc_consul == '1') {
                            $hallazgo = $value->prohallazgo;
                            $hallazgo = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($hallazgo)), ENT_QUOTES, "UTF-8");
                        } else {
                            $hallazgo = $value->examen_fisico;
                        }
                        $cell->setValue($hallazgo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('S' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $conclusion = '';
                        if ($value->proc_consul == '1') {
                            $conclusion = $value->proconclusion;
                            $conclusion = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($conclusion)), ENT_QUOTES, "UTF-8");
                        } else {
                            $conclusion = '';
                        }
                        $cell->setValue($conclusion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('T' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->presion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('U' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->temperatura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('V' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->peso);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('W' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->altura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('X' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $gct   = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        $texto = "";
                        if ($imc < 16) {
                            $texto = "Desnutrición";
                        } else if ($imc < 18) {
                            $texto = "Bajo de Peso";
                        } else if ($imc < 25) {
                            $texto = "Normal";
                        } else if ($imc < 27) {
                            $texto = "Sobrepeso";
                        } else if ($imc < 30) {
                            $texto = "Obesidad Tipo 1";
                        } else if ($imc < 40) {
                            $texto = "Obesidad Clinica";
                        } else {
                            $texto = "Obesidad Mordida";
                        }
                        $cell->setValue($texto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('Y' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $cell->setValue($imc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('Z' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }

                        if ($imc == 0) {
                            $gct = 0;
                        } else {
                            $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        }
                        $cell->setValue($gct);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('AA' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        $cell->setValue($peso_ideal);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('AB' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->perimetro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $receta  = '';
                    $receta2 = '';

                    if ($value->receta != null) {

                        $detalles = hc_receta_detalle::where('id_hc_receta', $value->receta)->get();

                        foreach ($detalles as $value2) {

                            $genericos = Medicina::where('id', $value2->id_medicina)->first()->genericos;

                            $receta  = $receta . ' - ' . $value2->medicina->nombre . ' (';
                            $receta2 = $receta2 . ' - ' . $value2->medicina->nombre . ': ' . $value2->dosis;
                            foreach ($genericos as $gen) {

                                $receta = $receta . $gen->generico->nombre . ' ';
                            }
                            $receta = $receta . ' ) ' . $value2->cantidad;

                        }

                    }

                    $sheet->cell('AC' . $i, function ($cell) use ($value, $receta) {
                        $rece_1 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($value->rp)), ENT_QUOTES, "UTF-8");
                        $cell->setValue($receta . ' ' . $rece_1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('AD' . $i, function ($cell) use ($value, $receta2) {
                        $rece_2 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($value->prescripcion)), ENT_QUOTES, "UTF-8");
                        $cell->setValue($receta2 . ' ' . $rece_2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('AE' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->d2apellido1 . ' ' . $value->d2nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $sheet->cell('AF' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->d3apellido1 . ' ' . $value->d3nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    //convenio
                    $sheet->cell('AG' . $i, function ($cell) use ($value) {
                        $empresa  = Empresa::find($value->id_empresa);
                        $tempresa = '';
                        if (!is_null($empresa)) {
                            $tempresa = '/' . $empresa->nombre_corto;
                        }
                        if ($value->id_seguro_procedimiento == null) {
                            $cell->setValue($value->seguro . $tempresa);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $seguro_hc_procedimiento = Seguro::find($value->id_seguro_procedimiento);
                            $cell->setValue($seguro_hc_procedimiento->nombre . $tempresa);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }

                    });

                    $sheet->cell('AH' . $i, function ($cell) use ($value) {

                        $estado = '';
                        if ($value->estado_cita == '4') {
                            $estado = 'ATENDIDO';
                        } elseif ($value->estado_cita == '3') {
                            $estado = 'SUSPENDIDO';
                        } elseif ($value->estado_cita == '-1') {
                            $estado = 'NO ASISTE';
                        } else {
                            if ($value->proc_consul == '4') {
                                $estado = 'ATENDIDO';
                            }
                        }

                        $cell->setValue($estado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });

                    $i = $i + 1;

                }

            });
            //$excel->getActiveSheet()->setAutosize(false);
            //$excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(5);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(15)->setAutosize(false);
            //dd($excel->getActiveSheet());
        })->export('xlsx');
    }

}
