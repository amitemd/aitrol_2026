<?php

namespace Sis_medico\Http\Controllers\limpieza_desinfeccion;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Limpieza;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\Sala;
use Sis_medico\User;
use Sis_medico\Empresa;

class LimpiezaDesinfeccionController extends Controller
{

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1)) == false) {
            return true;
        }
    }

    public function index($id_sala, $id_paciente, $id_pentax)
    {

        $limpieza = Limpieza::where('estado', '1')->where('id_sala', $id_sala)->where('id_paciente', $id_paciente)->where('id_pentax', $id_pentax)->get();
        $sala     = Sala::find($id_sala);
        $paciente = null;
        foreach ($limpieza as $value) {
            $paciente = Pentax::where('pentax.id', $value->id_pentax)->join('agenda as a', 'pentax.id_agenda', 'a.id')->select('a.fechaini')->first();

        }
        //dd($paciente);
        return view('limpieza_desinfeccion/index', ['limpieza' => $limpieza, 'id_sala' => $id_sala, 'sala' => $sala, 'id_paciente' => $id_paciente, 'paciente' => $paciente]);
    }

    public function index_paciente(Request $request, $id)
    {
        $sala = Sala::find($id);

        $fecha = $request['fecha'];
        $fecha_2 = $request['hasta'];

      


        $pacientes = Agenda::where("agenda.estado_cita", "4")
            ->where('agenda.proc_consul', '1')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_2 . ' 23:59'])
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('pentax as px', 'agenda.id', 'px.id_agenda')
            ->where('px.id_sala', $id)
            ->where('px.estado_pentax','!=','5')
            ->select('agenda.*', 'px.id as id_pentax', 'px.id_agenda as agenda_pentax', 'px.id_sala as pentax_sala')->get();

        //dd($pacientes);
        //$pacientes = $pacientes->select('agenda.*','px.id as id_pentax','px.id_agenda as agenda_pentax','px.id_sala as pentax_sala')->paginate(30);

        //dd($pacientes);
        $p_limpieza = Limpieza::where('id_sala', $id)
            ->whereBetween('created_at', [$fecha . ' 00:00', $fecha_2 . ' 23:59'])
            ->whereNull('id_pentax')
            ->get();
        //dd($p_limpieza);

        return view('limpieza_desinfeccion/index_paciente', ['pacientes' => $pacientes, 'id' => $id, 'sala' => $sala, 'fecha_2' => $fecha_2, 'p_limpieza' => $p_limpieza,'fecha'=>$fecha]);
    }

    public function salas()
    {

        $sala  = Sala::where('id_hospital', 2)
        ->where('estado', 1)
        ->where('proc_consul_sala', 1)
        ->get();
        $fecha = date('Y-m-d');

        return view('limpieza_desinfeccion/salas', ['sala' => $sala, 'fecha' => '0']);
    }

    public function crear($id_paciente, $id_pentax, $id_sala)
    {

        $paciente       = Paciente::find($id_paciente);
        $anestesiologos = User::where('id_tipo_usuario', '9')->where('estado', '1')->get();
        //dd($paciente);

        return view('limpieza_desinfeccion/crear', ['id_paciente' => $id_paciente, 'id_pentax' => $id_pentax, 'paciente' => $paciente, 'id_sala' => $id_sala, 'anestesiologos' => $anestesiologos]);
    }

    public function crear2($id_sala)
    {

        $anestesiologos = User::where('id_tipo_usuario', '9')->where('estado', '1')->get();

        return view('limpieza_desinfeccion/crear2', ['anestesiologos' => $anestesiologos, 'id_sala' => $id_sala]);
    }
    public function guardar(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        try{
           $arr        = [
                'id_paciente'        => $request['id_paciente'],
                'id_sala'            => $request['id_sala'],
                'id_pentax'          => $request['id_pentax'],
                'tipo_desinfecion'   => $request['tipo_desinfeccion'],
                'nom_deter_desinfec' => $request['nom_detergente'],
                'nom_toallitas'      => $request['nom_toallas'],
                'anestesiologia'     => $request['anestesiologia'],
                'responsable_anest'  => $request['responsable_anest'],
                'responsable'        => $request['responsable'],
                'en_camilla'         => $request['camilla'],
                'en_velador'         => $request['velador'],
                'en_monitor'         => $request['monitor'],
                'en_soporte'         => $request['sop_monitor'],
                'en_otros'           => $request['otros'],
                'observacion'        => $request['observacion'],
                'id_sala'            => $request['id_sala'],
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,
                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'hora_registro'      => $request['hora_registro'],
            ];

            Limpieza::create($arr); 
        DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito', 'id_sala' => $request['id_sala']];
        }catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage()];
        }

        
    }

    public function editar($id)
    {

        $limpieza       = Limpieza::find($id);
        $anestesiologos = User::where('id_tipo_usuario', '9')->where('estado', '1')->get();
        return view('limpieza_desinfeccion/editar', ['limpieza' => $limpieza, 'anestesiologos' => $anestesiologos]);
    }

    public function update(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        try{
            $id         = $request['id_limpieza'];
            $limpieza   = Limpieza::find($id);

            $arr = [
                'id_paciente'        => $request['id_paciente'],
                'id_sala'            => $request['id_sala'],
                'id_pentax'          => $request['id_pentax'],
                'tipo_desinfecion'   => $request['tipo_desinfeccion'],
                'nom_deter_desinfec' => $request['nom_detergente'],
                'nom_toallitas'      => $request['nom_toallas'],
                'anestesiologia'     => $request['anestesiologia'],
                'responsable'        => $request['responsable'],
                'responsable_anest'  => $request['responsable_anest'],
                'en_camilla'         => $request['camilla'],
                'en_velador'         => $request['velador'],
                'en_monitor'         => $request['monitor'],
                'en_soporte'         => $request['sop_monitor'],
                'en_otros'           => $request['otros'],
                'observacion'        => $request['observacion'],
                'id_sala'            => $request['id_sala'],
                'id_usuariomod'      => $idusuario,
                'ip_modificacion'    => $ip_cliente,
            ];

            $limpieza->update($arr);

            DB::commit();
            return ['respuesta' => 'success', 'msj' => 'Guardado con Exito'];
        }catch (\Exception $e) {
            DB::rollBack();
            return ['respuesta' => 'error', 'msj' => $e->getMessage()];
        }
        

        return "ok";
    }

    public function eliminar($id)
    {

        $limpieza = Limpieza::find($id);

        $arr = [
            'estado' => '0',
        ];

        $limpieza->update($arr);

        return "ok";
    }

    public function imprimir_excel($id, $tipo, Request $request)
    {   
        
        //dd($request->all());

        $fecha = $request['fecha'];
        $fecha_2 = $request['hasta'];
        //dd($request->all());
        //dd($fecha, $id, $tipo);

        // if ($fecha == 0) {
        //     $fecha_2 = date('Y-m-d');
        //     $fecha   = $fecha_2;
        // } else {
        //     $fecha_2 = $fecha;
        // }

        // dd($fecha, $fecha_2);

        //dd($id, $tipo);

        $pacientes = Agenda::where("agenda.estado_cita", "4")
            ->where('agenda.proc_consul', '1')
            ->where('agenda.estado','1')
            ->whereBetween('agenda.fechaini', [$fecha . ' 00:00:00', $fecha_2 . ' 23:59:59'])
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('pentax as px', 'agenda.id', 'px.id_agenda')
            ->where('px.id_sala', $id)
            ->where('px.estado_pentax','!=','5')
            ->select('agenda.*', 'px.id as id_pentax', 'px.id_agenda as agenda_pentax', 'px.id_sala as pentax_sala')
            ->get();

        
            
            
        //dd($pacientes);
        $sala = Sala::where('id', $id)->first();
        //dd($sala);
        $p_limpieza = Limpieza::where('id_sala', $id)
            ->whereBetween('created_at', [$fecha . ' 00:00', $fecha_2 . ' 23:59'])
            ->whereNull('id_pentax')
            ->get();

            $id_empresa = $request->session()->get('id_empresa');
            $empresa        = Empresa::where('id', $id_empresa)->first();
            //dd($pacientes, $p_limpieza);
        Excel::create('REGISTRO LIMPIEZA Y DESINFECCION DEL AMBIENTE HOSPITALARIO Vers. 0.1', function ($excel) use ($pacientes, $p_limpieza, $sala, $tipo, $fecha ,$fecha_2, $empresa) {
            $excel->sheet('LIMPIEZA Y DESINFECCION', function ($sheet) use ($pacientes, $p_limpieza, $sala, $tipo, $fecha ,$fecha_2, $empresa) {

                $sheet->mergeCells('A1:P1');
                if ($tipo == 1) {
                    $sheet->cell('A1', function ($cell) use($empresa) {
                        // manipulate the cel
                        $cell->setValue($empresa->nombrecomercial);
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBackground('#31A5D3');
                    });
                }

                if ($tipo == 2) {
                    $sheet->cell('A1', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('CARLOS ROBLES MEDRANDA');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBackground('#31A5D3');
                    });
                }

                $sheet->mergeCells('A2:P2');
                $sheet->cell('A2', function ($cell) use ($sala) {
                    // manipulate the cel
                    $cell->setValue('REGISTRO DE LIMPIEZA Y DESINFECCION DEL AMBIENTE HOSPITALARIO ' . ' - ' . $sala->nombre_sala);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#31A5D3');
                });

                $sheet->mergeCells('A3:P3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AREA  O  SERVICIO:');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#31A5D3');
                });

                $sheet->mergeCells('Q1:Q3');
                $sheet->cell('Q1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Vers. 0.1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#31A5D3');
                });

                $sheet->mergeCells('A4:A6');
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nº');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('B4:B6');
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('C4:C6');
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('D4:D6');
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('E4:F4');
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO DE DESINFECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('E5:E6');
                $sheet->cell('E5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONCURRENTE ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('F5:F6');
                $sheet->cell('F5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TERMINAL ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('G4:G6');
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE DEL DETERGENTE/DESINFECTANTE UTILIZADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });

                $sheet->mergeCells('H4:H6');
                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE DE TOALLITAS DESINFECTANTES UTILIZADAS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });

                $sheet->mergeCells('I4:I5');
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANESTESIOLOGIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });

                $sheet->cell('I6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MÁQUINA DE ANESTESIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('J4:J6');
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RESPONSABLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });

                $sheet->mergeCells('K4:O5');
                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ENFERMERIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });

                $sheet->cell('K6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CAMILLA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->cell('L6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('VELADORES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->cell('M6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MONITORES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->cell('N6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SOPORTE DE MONITORES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->cell('O6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS EQUIPOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('P4:P6');
                $sheet->cell('P4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RESPONSABLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });

                $sheet->mergeCells('Q4:Q6');
                $sheet->cell('Q4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACIONES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i = 7; $x = 1;
                foreach ($p_limpieza as $value) {
                    if ($value->nom_limp == 1) {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(substr($value->created_at, 0, 10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(substr($value->created_at, 11, 15));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        if ($value->nom_limp == 1) {
                            $sheet->cell('D' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue('Registro Inicial');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($value->tipo_desinfecion == 1) {
                            $sheet->cell('E' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } else {
                            $sheet->cell('E' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }
                        if ($value->tipo_desinfecion == 2) {
                            $sheet->cell('F' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } else {
                            $sheet->cell('F' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_deter_desinfec);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_toallitas);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        if ($value->anestesiologia == 1) {
                            $sheet->cell('I' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->anestesiologia == 2) {
                            $sheet->cell('I' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->anestesiologia == 3) {
                            $sheet->cell('I' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->responsable);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        if ($value->en_camilla == 1) {
                            $sheet->cell('K' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_camilla == 2) {
                            $sheet->cell('K' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_camilla == 3) {
                            $sheet->cell('K' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($value->en_velador == 1) {
                            $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_velador == 2) {
                            $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_velador == 3) {
                            $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($value->en_monitor == 1) {
                            $sheet->cell('M' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_monitor == 2) {
                            $sheet->cell('M' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_monitor == 3) {
                            $sheet->cell('M' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($value->en_soporte == 1) {
                            $sheet->cell('N' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_soporte == 2) {
                            $sheet->cell('N' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_soporte == 3) {
                            $sheet->cell('N' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($value->en_otros == 1) {
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_otros == 2) {
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_otros == 3) {
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->responsable);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->observacion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                    }
                    $i++; $x++;
                }

                $cont=0;$por = 0;
                foreach ($pacientes as $value) {
                    $limpieza = Limpieza::where('id_paciente', $value->id_paciente)->where('id_pentax', $value->id_pentax)->where('estado', '1')->whereBetween('created_at', [$fecha . ' 00:00:00', $fecha_2 . ' 23:59:59'])->orderBy('hora_registro','desc')->get();

                    

                    foreach ($limpieza as $limp) {
                      if(!empty($limp)){
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(substr($value->fechaini, 0, 10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        
                        if (is_null($limp->hora_registro)) {
                            $sheet->cell('C' . $i, function ($cell) use ($limp) {
                                // manipulate the cel
                               
                                $cell->setValue(date('H:i', strtotime($limp->created_at)));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }else{
                            $sheet->cell('C' . $i, function ($cell) use ($limp) {
                                // manipulate the cel
                                $cell->setValue(substr($limp->hora_registro,0,5));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                        }
                        
                        $sheet->cell('D' . $i, function ($cell) use ($limp) {
                            // manipulate the cel
                            $cell->setValue($limp->paciente->nombre1 . ' ' . $limp->paciente->apellido1 . ' ' . $limp->paciente->apellido2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        if ($limp->tipo_desinfecion == 1) {
                            $sheet->cell('E' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } else {
                            $sheet->cell('E' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }
                        if ($limp->tipo_desinfecion == 2) {
                            $sheet->cell('F' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } else {
                            $sheet->cell('F' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        $sheet->cell('G' . $i, function ($cell) use ($limp) {
                            // manipulate the cel
                            $cell->setValue($limp->nom_deter_desinfec);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('H' . $i, function ($cell) use ($limp) {
                            // manipulate the cel
                            $cell->setValue($limp->nom_toallitas);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        if ($limp->anestesiologia == 1) {
                            $sheet->cell('I' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->anestesiologia == 2) {
                            $sheet->cell('I' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->anestesiologia == 3) {
                            $sheet->cell('I' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }
                        if (!is_null($limp->responsable_anest)) {
                            $sheet->cell('J' . $i, function ($cell) use ($limp) {
                                // manipulate the cel
                                $cell->setValue($limp->user->nombre1 . ' ' . $limp->user->apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }else{
                            $sheet->cell('J' . $i, function ($cell) use ($limp) {
                                // manipulate the cel
                                $cell->setValue('');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }
                        

                        if ($limp->en_camilla == 1) {
                            $sheet->cell('K' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->en_camilla == 2) {
                            $sheet->cell('K' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->en_camilla == 3) {
                            $sheet->cell('K' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($limp->en_velador == 1) {
                            $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->en_velador == 2) {
                            $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->en_velador == 3) {
                            $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($limp->en_monitor == 1) {
                            $sheet->cell('M' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->en_monitor == 2) {
                            $sheet->cell('M' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->en_monitor == 3) {
                            $sheet->cell('M' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($limp->en_soporte == 1) {
                            $sheet->cell('N' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->en_soporte == 2) {
                            $sheet->cell('N' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->en_soporte == 3) {
                            $sheet->cell('N' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($limp->en_otros == 1) {
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->en_otros == 2) {
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($limp->en_otros == 3) {
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        $sheet->cell('P' . $i, function ($cell) use ($limp) {
                            // manipulate the cel
                            $cell->setValue($limp->responsable);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('Q' . $i, function ($cell) use ($limp) {
                            // manipulate the cel
                            $cell->setValue($limp->observacion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                    }
                    //dd($limpieza);

                    $i++; $x++;

                }
                
                }
                $i++; $x++;

                foreach ($p_limpieza as $value) {
                    if ($value->nom_limp == 2) {
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('B' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(substr($value->created_at, 0, 10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(substr($value->created_at, 11, 15));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                        if ($value->nom_limp == 2) {
                            $sheet->cell('D' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue('Registro final');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($value->tipo_desinfecion == 1) {
                            $sheet->cell('E' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } else {
                            $sheet->cell('E' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }
                        if ($value->tipo_desinfecion == 2) {
                            $sheet->cell('F' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } else {
                            $sheet->cell('F' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_deter_desinfec);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->nom_toallitas);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        if ($value->anestesiologia == 1) {
                            $sheet->cell('I' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->anestesiologia == 2) {
                            $sheet->cell('I' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->anestesiologia == 3) {
                            $sheet->cell('I' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->responsable);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        if ($value->en_camilla == 1) {
                            $sheet->cell('K' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_camilla == 2) {
                            $sheet->cell('K' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_camilla == 3) {
                            $sheet->cell('K' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($value->en_velador == 1) {
                            $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_velador == 2) {
                            $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_velador == 3) {
                            $sheet->cell('L' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($value->en_monitor == 1) {
                            $sheet->cell('M' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_monitor == 2) {
                            $sheet->cell('M' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_monitor == 3) {
                            $sheet->cell('M' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($value->en_soporte == 1) {
                            $sheet->cell('N' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_soporte == 2) {
                            $sheet->cell('N' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_soporte == 3) {
                            $sheet->cell('N' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        if ($value->en_otros == 1) {
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('X');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_otros == 2) {
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        } elseif ($value->en_otros == 3) {
                            $sheet->cell('O' . $i, function ($cell) {
                                // manipulate the cel
                                $cell->setValue('XXX');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');

                            });
                        }

                        $sheet->cell('P' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->responsable);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });

                        $sheet->cell('Q' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue($value->observacion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        });
                    }
                    $i++; $x++;

                }
                $i++; $x++;

                $sheet->mergeCells('A' . $i . ':Q' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Nota: Colocar una " X " al tipo de actividad que se realice.');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $i++;

                $sheet->mergeCells('A' . $i . ':E' . $i);
                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LIMPIEZA MARQUE UNA ¨X¨');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('F' . $i . ':G' . $i);
                $sheet->cell('F' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESINFECCIÓN MARQUE UNA ¨XX¨');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('H' . $i . ':J' . $i);
                $sheet->cell('H' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LIMPIEZA Y DESINFECCIÓN MARQUE UNA ¨XXX¨');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('K' . $i . ':Q' . $i);
                $sheet->cell('K' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $i++;

            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(4)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(17)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(12)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(14)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
            $excel->getActiveSheet()->getStyle('G4')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('H4')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('I6')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('N6')->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle('O6')->getAlignment()->setWrapText(true);

        })->export('xlsx');

    }

    public function paciente_nombre(Request $request)
    {

        $nombre_pac   = $request['term'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre_pac);
        //$seteo          = "%";
        $seteo = '%';

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
            //$seteo = $value . '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' LIMIT 50";

        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $data[] = array('value' => $nombre->completo, 'id' => $nombre->id, 'nombres' => $nombre->completo);
        }

        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No se encontraron resultados', 'id' => ''];
        }

    }

    public function paciente_nombre2(Request $request)
    {
        $nombre_pac   = $request['paciente'];
        $data         = null;
        $nuevo_nombre = explode(' ', $nombre_pac);
        //$seteo          = "%";
        $seteo = '%';

        foreach ($nuevo_nombre as $value) {
            $seteo = $seteo . $value . '%';
            //$seteo = '%'.$seteo . $value. '%';
        }

        $query = "SELECT CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) as completo, id
                  FROM paciente
                  WHERE CONCAT_WS(' ', nombre1, nombre2, apellido1, apellido2) like '" . $seteo . "' ";

        $nombres = DB::select($query);

        foreach ($nombres as $nombre) {
            $data[] = array('value' => $nombre->completo, 'id' => $nombre->id, 'nombres' => $nombre->completo);
        }

        if (count($data) > 0) {
            return $data[0];
        } else {
            return '0';
        }

    }

    public function guardar2(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $arr        = [
            'nom_limp'           => $request['nom_limp'],
            'id_sala'            => $request['id_sala'],
            'tipo_desinfecion'   => $request['tipo_desinfeccion'],
            'nom_deter_desinfec' => $request['nom_detergente'],
            'nom_toallitas'      => $request['nom_toallas'],
            'anestesiologia'     => $request['anestesiologia'],
            'responsable_anest'  => $request['responsable_anest'],
            'responsable'        => $request['responsable'],
            'en_camilla'         => $request['camilla'],
            'en_velador'         => $request['velador'],
            'en_monitor'         => $request['monitor'],
            'en_soporte'         => $request['sop_monitor'],
            'en_otros'           => $request['otros'],
            'observacion'        => $request['observacion'],
            'id_sala'            => $request['id_sala'],
            'id_usuariocrea'     => $idusuario,
            'id_usuariomod'      => $idusuario,
            'ip_creacion'        => $ip_cliente,
            'ip_modificacion'    => $ip_cliente,
        ];

        Limpieza::create($arr);

        return ['estado' => 'ok', 'id_sala' => $request['id_sala']];

    }

}
