<?php

namespace Sis_medico\Services;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Procedimiento;

class ClinicalHc4ReviewService
{
    public function formulario_procs(Request $request)
    {

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $procedimientos = $request->procedimientos;
        $procedimientos = array_unique($procedimientos);

        if (count($procedimientos) != 0) {
            $pentax = Pentax::where('id_agenda', $request->id)->first();
            if (!is_null($pentax)) {
                $pentax_procs = PentaxProc::where('id_pentax', $pentax->id)->get();
                $name_procs   = '';
                foreach ($pentax_procs as $ad) {
                    $name_procs = Procedimiento::find($ad->id_procedimiento)->observacion . ' ' . $name_procs;
                    $ad->delete();
                }
                $name_procs2 = '';
                foreach ($procedimientos as $value) {
                    $name_procs2      = Procedimiento::find($value)->observacion . ' ' . $name_procs2;
                    $input_pentax_pro = [
                        'id_pentax'        => $pentax->id,
                        'id_procedimiento' => $value,
                        'id_usuariocrea'   => $idusuario,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariomod'    => $idusuario,
                        'ip_creacion'      => $ip_cliente,
                    ];
                    PentaxProc::create($input_pentax_pro);

                }
                $input_log = [
                    'id_pentax'       => $pentax->id,
                    'tipo_cambio'     => 'REVISA AGENDA',
                    'descripcion'     => 'CAMBIO PROCEDIMIENTOS',
                    'estado_pentax'   => $pentax->estado_pentax,
                    'id_seguro'       => $pentax->id_seguro,
                    'id_subseguro'    => $pentax->id_subseguro,
                    'procedimientos'  => $name_procs2,
                    'id_doctor1'      => $pentax->id_doctor1,
                    'id_doctor2'      => $pentax->id_doctor2,
                    'id_doctor3'      => $pentax->id_doctor3,
                    'id_sala'         => $pentax->id_sala,

                    'observacion'     => 'CAMBIA PROCEDIMIENTOS',
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                ];

                Pentax_log::create($input_log);
            } else {
                $agenda     = Agenda::find($request->id);
                $agproc     = AgendaProcedimiento::where('id_agenda', $request->id)->get();
                $name_procs = Procedimiento::find($agenda->id_procedimiento)->observacion;
                foreach ($agproc as $ad) {
                    $name_procs = $name_procs . ' ' . Procedimiento::find($ad->id_procedimiento)->observacion;
                    $ad->delete();
                }
                $input_proc = [
                    'id_procedimiento' => $procedimientos[0],
                    'id_usuariomod'    => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                ];
                $agenda->update($input_proc);

                $name_procs2 = '';
                foreach ($procedimientos as $value) {
                    $name_procs2 = Procedimiento::find($value)->observacion . ' ' . $name_procs2;
                    if ($procedimientos[0] != $value) {
                        AgendaProcedimiento::create([
                            'id_agenda'        => $request->id,
                            'id_procedimiento' => $value,
                            'ip_creacion'      => $ip_cliente,
                            'ip_modificacion'  => $ip_cliente,
                            'id_usuariocrea'   => $idusuario,
                            'id_usuariomod'    => $idusuario,
                        ]);
                    }
                }
                Log_agenda::create([
                    'id_agenda'       => $request->id,
                    'descripcion'     => 'REVISA AGENDA',
                    'descripcion2'    => 'ACTUALIZA PROCEDIMIENTOS',
                    'observaciones'   => 'REVISA AGENDA: ' . $name_procs . '=>' . $name_procs2,
                    'id_usuariomod'   => $idusuario,
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion'     => $ip_cliente,
                ]);
            }

        }

        return $procedimientos;

    }


    public function carga_revision($id_agenda, $val)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $id_agenda = substr($id_agenda, 8);
        $txt       = '';
        if ($val == '0') {
            $txt = 'NO';
        }if ($val == '1') {
            $txt = 'SI';
        }
        $agenda = Agenda::find($id_agenda);
        if (!is_null($agenda)) {
            $agenda->update(['revisado' => $val]);
            Log_agenda::create([
                'id_agenda'       => $agenda->id,
                'descripcion'     => 'REVISA AGENDA',
                'descripcion2'    => $txt,
                'observaciones'   => 'REVISA AGENDA: ' . $txt,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ]);
        }

        return [$id_agenda, $val];
    }


    public function revision_formulario(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $agenda     = Agenda::find($request->id);
        if (!is_null($agenda)) {
            $agenda->update([
                'observacion_proc' => $request->observacion,
                'diagnostico_proc' => $request->diagnostico,
            ]);
            Log_agenda::create([
                'id_agenda'       => $request->id,
                'descripcion'     => 'ACTUALIZA OBSERVACION/DIAGNOSTICO',
                'descripcion2'    => 'obs: ' . $request->observacion . ' diag:' . $request->diagnostico,
                'observaciones'   => 'obs: ' . $request->observacion . ' diag:' . $request->diagnostico,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ]);

        }
    }


    public function cambiar_supervision($cambio, $id_agenda)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $agenda     = Agenda::find($id_agenda);
        $texto      = 'SUPERVISADO POR EL DR. ROBLES';
        if (!is_null($agenda)) {
            if ($cambio != '1') {
                $cambio = null;
                $texto  = 'YA NO SERA SUPERVISADO POR EL DR. ROBLES';
            }
            $agenda->update([
                'supervisa_robles' => $cambio,
            ]);
            Log_agenda::create([
                'id_agenda'       => $id_agenda,
                'descripcion'     => 'REVISION DE PROCEDIMIENTOS',
                'descripcion2'    => $texto,
                'observaciones'   => '',
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ]);

        }
    }

    public function cambiar_crm($cambio, $id_agenda)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $agenda     = Agenda::find($id_agenda);
        $texto      = 'SOLO REALIZADO POR EL DR. ROBLES';
        if (!is_null($agenda)) {
            if ($cambio != '1') {
                $cambio = null;
                $texto  = 'PUEDE SER REALIZADO POR CUALQUIER DR';
            }
            $agenda->update([
                'solo_robles' => $cambio,
            ]);
            Log_agenda::create([
                'id_agenda'       => $id_agenda,
                'descripcion'     => 'REVISION DE PROCEDIMIENTOS',
                'descripcion2'    => $texto,
                'observaciones'   => '',
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ]);

        }
    }


    public function exportar_revision(Request $request)
    {
        //dd($request->all());
        $procedimientos = Agenda::where('estado', '<>', '0')->where('proc_consul', '1')->whereBetween('fechaini', [$request->desde_inicio . '  0:00:00', $request->fecha_hasta . ' 23:59:59'])->Orderby('fechaini')->get();
        //dd($procedimientos);
        Excel::create('exportar revision', function ($excel) use ($procedimientos) {
            $excel->sheet('exportar revision', function ($sheet) use ($procedimientos) {
                $sheet->cell('B2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha / Hora');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Cedula');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Apellidos');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nombres');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Edad');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO ASIGNADO');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Seguro');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Procedimiento');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Amb/Hosp');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Estado');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Revisado');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Diagnostico');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Observaciones');
                    $cell->setBackground('#ECEFF0');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i = 3;
                foreach ($procedimientos as $procedimiento) {
                    $resto_procs  = AgendaProcedimiento::where('id_agenda', $procedimiento->id)->get();
                    $pentax       = Pentax::where('id_agenda', $procedimiento->id)->first();
                    $pcant        = 0;
                    $pentax_procs = null;
                    if (!is_null($pentax)) {
                        $pentax_procs = PentaxProc::where('id_pentax', $pentax->id)->get();
                        $pcant        = $pentax_procs->count();
                    }

                    $edad = 0;
                    if ($procedimiento->paciente->fecha_nacimiento != null) {
                        $edad = Carbon::createFromDate(substr($procedimiento->paciente->fecha_nacimiento, 0, 4), substr($procedimiento->paciente->fecha_nacimiento, 5, 2), substr($procedimiento->paciente->fecha_nacimiento, 8, 2))->age;
                    }
                    $sheet->cell('B' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->fechaini);
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->id_paciente);
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->paciente->apellido1 . ' ' . $procedimiento->paciente->apellido2);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->paciente->nombre1 . ' ' . $procedimiento->paciente->nombre2);
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($edad) {
                        // manipulate the cel
                        $cell->setValue($edad);
                    });
                    if (!is_null($procedimiento->id_doctor1)) {
                        $sheet->cell('G' . $i, function ($cell) use ($procedimiento) {
                            // manipulate the cel
                            $cell->setValue('Dr. ' . $procedimiento->doctor1->nombre1 . ' ' . $procedimiento->doctor1->apellido1);
                        });
                    }
                    $sheet->cell('H' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->seguro->nombre);
                    });
                    $sheet->cell('I' . $i, function ($cell) use ($procedimiento, $resto_procs, $pentax_procs, $pcant) {
                        // manipulate the cel
                        $texto = $procedimiento->procedimiento->nombre;
                        foreach ($resto_procs as $val) {
                            $texto = $texto . ' ' . Procedimiento::find($val->id_procedimiento)->observacion;
                        }
                        if ($pcant > 0) {
                            $texto = '';
                            foreach ($pentax_procs as $val) {
                                $texto = $texto . ' ' . Procedimiento::find($val->id_procedimiento)->observacion;
                            }
                        }
                        $cell->setValue($texto);
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        if ($procedimiento->est_amb_hos == 0) {
                            $cell->setValue("Ambulatorio");
                        } else {
                            $cell->setValue("Hospitalizado");
                        }
                    });
                    $sheet->cell('K' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        if ($procedimiento->estado_cita == '0') {
                            $cell->setValue("Por Confirmar");
                        } elseif ($procedimiento->estado_cita == '1') {
                            $cell->setValue("Confirmado");
                        } elseif ($procedimiento->estado_cita == '2') {
                            $cell->setValue("Reagendar");
                        } elseif ($procedimiento->estado_cita == '3') {
                            $cell->setValue("Suspendido");
                        } elseif ($procedimiento->estado_cita == '4') {
                            $cell->setValue("Pre-Admisionado");
                        }

                    });
                    $sheet->cell('L' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        if ($procedimiento->revisado == '0') {
                            $cell->setValue("No");
                        } elseif ($procedimiento->revisado == '1') {
                            $cell->setValue("Si");
                        }

                    });
                    $sheet->cell('M' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->diagnostico_proc);
                    });
                    $sheet->cell('N' . $i, function ($cell) use ($procedimiento) {
                        // manipulate the cel
                        $cell->setValue($procedimiento->observacion_proc);
                    });
                    $i++;
                }

            });
        })->export('xlsx');
    }


}
