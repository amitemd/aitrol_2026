<?php

namespace Sis_medico\Http\Controllers\limpieza_pentax;

use Excel;
use Response;
use Image;
use PHPExcel_Worksheet_Drawing;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Http\Controllers\servicios_generales\LimpiezaBanosController;
use Sis_medico\Http\Controllers\servicios_generales\MantenimientoHorarioController;
use Sis_medico\Http\Controllers\servicios_generales\LimpiezaAreaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Sis_medico\Agenda;
use Sis_medico\Limpieza;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\Sala;
use Sis_medico\User;
use Sis_medico\LimpiezaPentax;
use Sis_medico\Mantenimientos_Dotacion;
use Sis_medico\Mantenimientos_Insumos_Limpieza;
use Sis_medico\Firma_Servicios_Generales;
use Sis_medico\Examen_Orden;
use Sis_medico\Orden_documento;
use Sis_medico\Empresa;
class LimpiezaPentaxController extends Controller
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
        if (in_array($rolUsuario, array(1, 24)) == false) {
            return true;
        }
    }

    public function index_pentax()
    {


        $sala = Sala::where('id_hospital', 2)
            ->where('estado', 1)
            ->where('proc_consul_sala', 1)
            ->get();
        return view('limpieza_pentax/index', ['sala' => $sala]);
    }

    public function excel_foto(Request $request)
    {

        $id_empresa = $request->session()->get('id_empresa');
        $empresa        = Empresa::where('id', $id_empresa)->first();
        $pentax = LimpiezaPentax::whereBetween('created_at', [$request['fechaAntes'] . ' 00:00:00', $request['fechaDespues'] . ' 23:59:59'])->get();

        Excel::create('REGISTRO LIMPIEZA Y DESINFECCION DEL AMBIENTE HOSPITALARIO Vers. 0.1', function ($excel) use ($pentax,$request,$empresa) {
            $excel->sheet('LIMPIEZA Y DESINFECCION', function ($sheet) use ($pentax,$request,$empresa) {

                $sheet->mergeCells('A1:O1');

                $sheet->cell('A1', function ($cell) use($empresa){
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#31A5D3');
                });




                $sheet->mergeCells('A2:O2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('REGISTRO DE LIMPIEZA Y DESINFECCION PENTAX');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#31A5D3');
                });

                $sheet->mergeCells('A3:O3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALAS');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#31A5D3');
                });

                $sheet->mergeCells('P1:P3');
                $sheet->cell('P1', function ($cell) {
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
                    $cell->setValue('SALA N°');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });


                $sheet->mergeCells('C4:C6');
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('D4:D6');
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('E4:E6');
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('F4:G5');
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO DE DESINFECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('F6:F6');
                $sheet->cell('F6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONCURRENTE ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('G6:G6');
                $sheet->cell('G6', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TERMINAL ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('H4:H6');
                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOTACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });
                $sheet->mergeCells('I4:I6');
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INSUMOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });



                $sheet->mergeCells('J4:M4');
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SERVICIOS GENERALES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });

                $sheet->mergeCells('J5:J6');
                $sheet->cell('J5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PAREDES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('K5:K6');
                $sheet->cell('K5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PISO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('L5:L6');
                $sheet->cell('L5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TECHO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
                $sheet->mergeCells('M5:M6');
                $sheet->cell('M5', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS EQUIPOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });


                $sheet->mergeCells('N4:N6');
                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RESPONSABLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });



                $sheet->mergeCells('O4:O6');
                $sheet->cell('O4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FIRMA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                    $cell->setBackground('#C2C7CA');
                });

                $sheet->mergeCells('P4:P6');
                $sheet->cell('P4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACIONES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });


                $i = 7;
                $x = 1;
                foreach ($pentax as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($x) {
                        // manipulate the cel
                        $cell->setValue($x);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        $sala = Sala::find($value->id_sala);
                        $cell->setValue($sala->nombre_sala);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(substr($value->created_at, 0, 11));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(substr($value->created_at, 11, 18));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {

                        $paciente = Paciente::find($value->id_paciente);
                        // manipulate the cel
                        if(!empty($paciente)){
                        $cell->setValue($paciente->nombre1 . ' ' . $paciente->apellido1);
                        }else{
                            $cell->setValue('');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    if ($value->tipo_desinfeccion == 1) {
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
                    if ($value->tipo_desinfeccion    == 2) {
                        $sheet->cell('G' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } else {
                        $sheet->cell('G' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }




                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $port = explode(',', $value->arregloDotacion);
                        $arr = Mantenimientos_Dotacion::whereIn('id', [$port])->get();
                        $arry = [];
                        foreach ($arr as $key => $value) {
                            $arry[] = $value->nombre;
                        }
                        $cell->setValue(implode(",", $arry));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->cell('I' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $port = explode(',', $value->arregloInsumos);
                        $arr = Mantenimientos_Insumos_Limpieza::whereIn('id', [$port])->get();
                        $arry = [];
                        foreach ($arr as $key => $value) {
                            $arry[] = $value->nombre;
                        }
                        $cell->setValue(implode(",", $arry));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    if ($value->paredes == 1) {
                        $sheet->cell('J' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } elseif ($value->paredes == 2) {
                        $sheet->cell('J' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('XX');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } elseif ($value->paredes == 3) {
                        $sheet->cell('J' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('XXX');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } else {

                        $sheet->cell('J' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    if ($value->piso == 1) {
                        $sheet->cell('K' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } elseif ($value->piso == 2) {
                        $sheet->cell('K' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('XX');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } elseif ($value->piso == 3) {
                        $sheet->cell('K' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('XXX');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } else {

                        $sheet->cell('K' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    if ($value->techo == 1) {
                        $sheet->cell('L' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } elseif ($value->techo == 2) {
                        $sheet->cell('L' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('XX');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } elseif ($value->techo == 3) {
                        $sheet->cell('L' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('XXX');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } else {

                        $sheet->cell('L' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    if ($value->otros_equipos == 1) {
                        $sheet->cell('M' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('X');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } elseif ($value->otros_equipos == 2) {
                        $sheet->cell('M' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('XX');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } elseif ($value->otros_equipos == 3) {
                        $sheet->cell('M' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('XXX');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    } else {

                        $sheet->cell('M' . $i, function ($cell) {
                            // manipulate the cel
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }

                    $sheet->cell('N' . $i, function ($cell) use ($value) {

                        $user = User::find($value->id_usuariocrea);
                        // manipulate the cel
                        $cell->setValue($user->nombre1 . ' ' . $user->apellido1 . ' ' . $user->apellido2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $firma     = Firma_Servicios_Generales::where('id_usuario', $value->id_usuariocrea)->first();

                    $sheet->cell('O' . $i);
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    if(!is_null($firma)){
                      $objDrawing->setPath(base_path() . '/storage/app/avatars/' . $firma->nombre);
                      $objDrawing->setCoordinates('O'.$i);
                      $objDrawing->setWidthAndHeight(130,40);
                      $objDrawing->setWorksheet($sheet);
                    }

                    $sheet->cell('P' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->observaciones);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i++;
                    $x++;
                }

                $sheet->mergeCells('A' . $i . ':P' . $i);
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

                $sheet->mergeCells('F' . $i . ':K' . $i);
                $sheet->cell('F' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DESINFECCIÓN MARQUE UNA ¨XX¨');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });

                $sheet->mergeCells('L' . $i . ':P' . $i);
                $sheet->cell('L' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('LIMPIEZA Y DESINFECCIÓN MARQUE UNA ¨XXX¨');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                    $cell->setAlignment('center');
                });
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(4)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(16)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("M")->setWidth(10)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("N")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("O")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("P")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(25)->setAutosize(false);
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

    public function buscar_sala(Request $request)
    {

        $pacientes = Agenda::where("agenda.estado_cita", "4")
            ->where('agenda.proc_consul', '1')
            ->whereBetween('agenda.fechaini', [$request['fechaa'] . ' 00:00', $request['fechad'] . ' 23:59'])
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('pentax as px', 'agenda.id', 'px.id_agenda')
            ->where('px.id_sala', $request['sala_id'])
            ->select('agenda.*', 'px.id as id_pentax', 'px.id_agenda as agenda_pentax', 'px.id_sala as pentax_sala')->get();


        return view('limpieza_pentax.vista_nueva', ['pacientes' => $pacientes, 'sala_id' => $request['sala_id']]);
    }

    public function created_pentax(Request $request)
    {

        $paciente       = Paciente::find($request['id']);
        $anestesiologos = User::where('id_tipo_usuario', '9')->where('estado', '1')->get();
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        return view('limpieza_pentax.modal_crear', ['productos' => $productos, 'insumos' => $insumos, 'id_pentax' => $request['id_pentax'], 'paciente' => $paciente, 'id_sala' => $request['id_sala'], 'anestesiologos' => $anestesiologos]);
    }

    public function uploading($imagen, $idusuario)
    {

        try {
            $extension       = $imagen->getClientOriginalExtension();
            $nuevo_nombre    = "documento" . date('YmdHis') . $idusuario . "." . $extension;
            $r1              = Storage::disk('public')->put($nuevo_nombre, \File::get($imagen));
            $image           = Image::make(Storage::disk('public')->get($nuevo_nombre));
            $image->resize(1280, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            Storage::disk('public')->put($nuevo_nombre, (string) $image->encode('jpg', 30));
            return ['err' => false, 'data' => $nuevo_nombre];
        } catch (\Exception $e) {
            return ['err' => true, 'data' => ''];
        }
    }

    public function save_pentax(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        try {
            if ($request['imagen_antes'] != '' || $request['imagen_antes'] != null) {
                $pathAntes = $this->uploading($request['imagen_antes'], $idusuario);
                if ($pathAntes['err']) {
                    return ['err' => true, 'msj' => 'Hubo un error en la subida de la foto'];
                }
            }
            $insumos = implode(",", $request['insumos']);
            $productos = implode(",", $request['nom_detergente']);
            $limpiezaPentax = new LimpiezaPentax;
            $limpiezaPentax->id_paciente = $request['id_paciente'];
            $limpiezaPentax->id_pentax = $request['id_pentax'];
            $limpiezaPentax->id_sala = $request['id_sala'];
            $limpiezaPentax->tipo_desinfeccion = $request['tipo_desinfeccion'];
            $limpiezaPentax->techo = $request['techo'];
            $limpiezaPentax->otros_equipos = $request['otros_equipos'];
            $limpiezaPentax->observaciones = $request['observacion'];
            $limpiezaPentax->paredes = $request['paredes'];
            $limpiezaPentax->path_antes = $pathAntes['data'];
            $limpiezaPentax->piso = $request['piso'];
            $limpiezaPentax->id_usuariocrea = $idusuario;
            $limpiezaPentax->id_usuariomod = $idusuario;
            $limpiezaPentax->arregloInsumos = $insumos;
            $limpiezaPentax->arregloDotacion = $productos;
            $limpiezaPentax->save();

            return redirect()->back();
        } catch (\Exception $e) {
            dd($e->getMessage());
            return ['err' => true, 'msj' => 'Hubo un error en el guardado'];
        }
    }

    public function updated_pentax(Request $request)
    {
        $user = LimpiezaPentax::find($request['id']);
        $insumos = Mantenimientos_Dotacion::where('estado', 1)->get();
        $productos = Mantenimientos_Insumos_Limpieza::where('estado', 1)->get();
        return view('limpieza_pentax.modal_editar', ['id' => $user, 'productos' => $productos, 'insumos' => $insumos]);
    }

    public function edit_pentax(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        try {

            $pentax = LimpiezaPentax::find($request['id_registro']);
            $pathAntes = [];
            if ($pentax->path_despues != '' || $pentax->path_despues != null) {
                $pathAntes = ['data' => $pentax->path_despues];
            }
            if ($request['imagen_despues'] != '' || $request['imagen_despues'] != null) {
                $pathAntes = $this->uploading($request['imagen_despues'], $idusuario);
                if ($pathAntes['err']) {
                    return ['err' => true, 'msj' => 'Hubo un error en la subida de la foto'];
                }
            }

            $pentax->path_despues = $pathAntes['data'];
            $pentax->id_usuariocrea = $idusuario;
            $pentax->id_usuariomod = $idusuario;
            $pentax->save();
            return redirect()->back();
        } catch (\Exception $e) {
            dd($e);
            return ['err' => true, 'msj' => 'Hubo un error en el guardado'];
        }
    }

    public function modal_foto(Request $request)
    {

        $foto = LimpiezaPentax::find($request['id']);
        return view('limpieza_pentax.modal_foto', ['foto' => $foto, 'tipo' => $request['tipo']]);
    }
    public function eleccion_tipo(Request $request)
    {

        $banos = new LimpiezaBanosController;
        $area = new LimpiezaAreaController;
        $tipo = $request['tipo'];
        $id = $request['id'];
        if ($tipo == 1) {
            return  $banos->create($id, $request);
        } else {
            return  $area->created($id, $request);
        }
    }

    public function eleccion_tipo2(Request $request)
    {

        $banos = new LimpiezaBanosController;
        $area = new LimpiezaAreaController;
        $tipo2 = $request['tipo2'];

        if ($tipo2 == 1) {
            return  $banos->index($request);
        } else {
            return  $area->index($request);
        }
    }

    public function subir_documento(Request $request){

      $registro = Examen_Orden::find($request['id']);
      $maestroDocumento = DB::table('labsm_documentos as ld')->where('ld.estado',1)->get();
      return view('contable.cierre_caja.modal_documento',['registro'=>$registro,'maestroDocumento'=> $maestroDocumento]);
    }

    public function uploadingPdf($imagen, $idusuario)
    {

        try {

             $nombre_archivo  =  $imagen->getClientOriginalName();
             $extension       =  $imagen->getClientOriginalExtension();
             $nuevo_nombre    =  'documento_'.$idusuario . "_" . date('YmdHis') . "." . $extension;
             $nuevo = Storage::disk('public')->put($nuevo_nombre, \File::get($imagen));
            return ['err' => false, 'data' => $nuevo_nombre];
        } catch (\Exception $e) {
            return ['err' => true, 'data' => ''];
        }
    }

    public function subir_documento_save(Request $request){
      $ip_cliente = $_SERVER["REMOTE_ADDR"];
      $idusuario  = Auth::user()->id;
      $ip_cliente = $_SERVER["REMOTE_ADDR"];
      $path = $this->uploadingPdf($request['documento'], $idusuario);
      $docs = Examen_Orden::find($request['id']);
      try {
          $documento = new Orden_documento;
          $documento->id_examen_orden = $docs->id;
          $documento->tipo_documento = $request['tipo'];
          $documento->path_documento = $path['data'];
          $documento->id_usuariocrea = $idusuario;
          $documento->id_usuariomod = $idusuario;
          $documento->ip_modificacion = $ip_cliente;
          $documento->ip_creacion = $idusuario;
          $documento->save();
          return redirect()->back();
      } catch (\Exception $e) {
        dd($e);
        return redirect()->back();
      }
    }

    public function vizualizar_docs_subidos (Request $request){
      $empresa    = Empresa::where('prioridad_labs', '1')->first();
      $each = Orden_documento::where('id_examen_orden',$request['id'])->where('estado',1)->paginate(10);
      $maestroDocumento = DB::table('labsm_documentos as ld')->where('ld.estado',1)->get();
      return view('contable.cierre_caja.reporte_documento',['each'=>$each,'empresa'=>$empresa,'maestroDocumento'=>$maestroDocumento]);
    }

    public function vizualizar_pdf(Request $request){

       $nombreArchivo = Orden_documento::where('id', $request['id'])->first();
       $path1 = storage_path() . "/app/avatars/" . $nombreArchivo->path_documento;
       return response()->file($path1);
    }

    public function cambiar_estado(Request $request){
      $search = Orden_documento::find($request['id']);
      $search->justificacion = $request['opcion'];
      $search->estado = 0;
      $search->save();
      return ['status'=>true,'msj'=>'Eliminado correctamente'];
    }

    public function buscar_documento(Request $request){
      $id_empresa       = $request->session()->get('id_empresa');
      $empresa          = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
      $each = Orden_documento::whereBetween('created_at', [$request['fecha'] . ' 00:00:00', $request['fecha'] . ' 23:59:59'])->where('tipo_documento',$request['tipo'])->where('estado',1)->paginate(10);
      $maestroDocumento = DB::table('labsm_documentos as ld')->where('ld.estado',1)->get();
      return view('contable.cierre_caja.reporte_documento',['each'=>$each,'empresa'=>$empresa,'maestroDocumento'=>$maestroDocumento]);
    }
}
