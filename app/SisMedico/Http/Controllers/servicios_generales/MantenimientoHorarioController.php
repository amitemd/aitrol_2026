<?php

namespace Sis_medico\Http\Controllers\servicios_generales;

use FFMpeg\Media\Concat;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use Sis_medico\Paciente;
use Sis_medico\Equipo;
use Sis_medico\User;
use Sis_medico\Procedimiento;
use Sis_medico\Empresa;
use PHPExcel_Worksheet_Drawing;
use Excel;
use Sis_medico\Sala;
use Sis_medico\Piso;
use Sis_medico\Pentax;
use Sis_medico\FrecuenciaMantenimiento;
use Sis_medico\MantenimientoHorario;
use Sis_medico\Hospital;

class MantenimientoHorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $mant = MantenimientoHorario::whereBetween('estado', ['0', '1'])->wherebetween('created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->paginate(10);
        $fecha_desde = date('Y-m-d');
        $sala = Sala::where('estado', '1')->get();
        return view('servicios_generales.limpieza_horarios.index', ['fecha_desde' => $fecha_desde, 'sala' => $sala, 'mant' => $mant]);
    }
    public function registrar()
    {
        $piso = Hospital::where('estado', '1')->get();
        return view('servicios_generales.limpieza_horarios.registrar', ['piso' => $piso]);
    }
    public function modaleditar()
    {
        $mantemientoHorario = MantenimientoHorario::where('estado', 0)->wherebetween('created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->paginate(5);
        return view('servicios_generales.limpieza_horarios.modaleditar', ['mantemientoHorario' => $mantemientoHorario]);
    }
    public function nombre_piso(Request $request)
    {

        $array = [];
        $fecha =  date("Y-m-d");
        $id = $request['term'];
        $retorno = Sala::where('id_hospital', $id)->get();
        foreach ($retorno as $x) {
            $data = array();
            $data['id'] = $x->id;
            $data['nombre'] = $x->nombre_sala;
            $array[] = $data;
        }
        return response()->json($array);
    }

    public function guardar(Request $request)
    {
        $hoy = date("Y-m-d H:i:s");
        $ip_cliente      = $_SERVER["REMOTE_ADDR"];
        $id_empresa      = $request->session()->get('id_empresa');
        $idusuario       = Auth::user()->id;

        MantenimientoHorario::create([

            'id_sala'           => $request['sala'],
            'id_area'           => $request['piso'],
            'id_encargado'      => $idusuario,
            'desinfectante'     => $request['desinfectante'],
            'frecuencia1'       => $hoy,
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'estado'            => '1',
        ]);
        $sala = Sala::where('estado', '1')->get();
        return json_encode('ok');
    }

    public function reporte(){

        $sala = Sala::where('estado',1)->get();



        return view('servicios_generales.limpieza_horarios.reporte',['sala'=>$sala]);
    }

    public function agregarhor(Request $request)
    {
        $hoy = date("Y-m-d H:i:s");
        $mant  = MantenimientoHorario::where('id', $request['id'])->first();
        if ($mant->frecuencia1 == null) {
            $mant->frecuencia1 = $hoy;
            $mant->save();
            return json_encode('ok');
        } elseif ($mant->frecuencia2 == null) {
            $mant->frecuencia2 = $hoy;
            $mant->save();
            return json_encode('ok');
        } elseif ($mant->frecuencia3 == null) {
            $mant->frecuencia3 = $hoy;
            $mant->save();
            return json_encode('ok');
        } elseif ($mant->frecuencia4 == null) {
            $mant->frecuencia4 = $hoy;
            $mant->save();
            return json_encode('ok');
        } elseif ($mant->frecuencia5 == null) {
            $mant->frecuencia5 = $hoy;
            $mant->save();
            return json_encode('ok');
        } elseif ($mant->frecuencia6 == null) {
            $mant->frecuencia6 = $hoy;
            $mant->save();
            return json_encode('ok');
        } elseif ($mant->frecuencia7 == null) {
            $mant->frecuencia7 = $hoy;
            $mant->save();
            return json_encode('ok');
        } elseif ($mant->frecuencia8 == null) {
            $mant->frecuencia8 = $hoy;
            $mant->save();
            return json_encode('ok');
        } else {
            $array = array('lleno', $request['id']);
            return json_encode($array);
        }
    }
    public function modalobs(Request $request, $id)
    {
        return view('servicios_generales.limpieza_horarios.modalobs', ['id' => $id]);
    }

    public function agragsobs(Request $request)
    {


        $man = MantenimientoHorario::where('id', $request['id'])->first();
        $man->observaciones = $request['obs'];
        $man->estado = '0';
        $man->save();
        return json_encode('ok');
    }
    public function  buscar(Request $request)
    {

        $sala = Sala::where('estado', '1')->get();
        $constraints = [
            'estado'  => $request['estado'],
            'id_sala'  => $request['id_sala'],
            'created_at' => $request['fecha'],
        ];

        $mant = $this->doSearchingQuery($constraints);
        return view('servicios_generales.limpieza_horarios.index', ['mant' => $mant, 'sala' => $sala, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints)
    {
        $query  = MantenimientoHorario::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
            }

            $index++;
        }
        return $query->paginate(5);
    }
    public function excel(Request $request)
    {
         $idusuario       = Auth::user()->id;
        $tipo = $request['tipo'];
        $fecha_desde = $request['desde'];
        $fecha_hasta = $request['hasta'];

        $fecha_desde = date('Y-m-d', strtotime($fecha_desde));

        $fecha_hasta = date('Y-m-d', strtotime($fecha_hasta));

        //dd($fecha_hasta);
      
        $id_empresa = $request->session()->get('id_empresa');
        $empresa = Empresa::where('id', $id_empresa)->first();
        if (!is_null($request['area'])) {

            $control = MantenimientoHorario::wherebetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->where('id_sala',$request['area'])->get();
            //dd($control);
        } else {
            $control = MantenimientoHorario::whereBetween('created_at', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59'])->get();
        }
        
        Excel::create('Limpieza y Desinfección de Salas Vers. 0.1', function ($excel) use ($empresa, $control,$tipo) {

            $excel->sheet('REPORTE LIMPIEZA', function ($sheet) use ($empresa, $control,$tipo) {

                $sheet->mergeCells('C1:K1');
                $sheet->cell('C1', function ($cell) use ($empresa,$tipo) {
                    if (!is_null($empresa) || isset($empresa)) {
                        $cell->setValue($tipo == 1 ? $empresa->nombrecomercial . ':' . $empresa->id : 'CARLOS ROBLES MEDRANDA');
                    }
                    $cell->setFontColor('#010101');
                    $cell->setBackground('#FFFFFF');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->mergeCells('C2:K2');
                $sheet->cell('C2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Limpieza y Desinfección de Salas');
                    $cell->setFontColor('#010101');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('L1:L2');
                $sheet->cell('L1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Vers. 0.1');
                    $cell->setFontColor('#010101');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                if(isset($empresa) || !is_null($empresa)){
                    if ($empresa->logo != null) {
                        $sheet->mergeCells('A1:B1');
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                        $objDrawing->setCoordinates('A1');
                        $objDrawing->setHeight(220);
                        $objDrawing->setWidth(120);
                        $objDrawing->setWorksheet($sheet);
                    }
                }else{
                        $sheet->mergeCells('A1:B1');
                        $objDrawing = new PHPExcel_Worksheet_Drawing;
                        //$objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                        $objDrawing->setCoordinates('A1');
                        $objDrawing->setHeight(220);
                        $objDrawing->setWidth(120);
                        $objDrawing->setWorksheet($sheet);
                }
                
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Area');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Frecuencia 1');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Frecuencia 2');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Frecuencia 3');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Frecuencia 4');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Frecuencia 5');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Frecuencia 6');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Frecuencia 7');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Frecuencia 8');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Desinfectante');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Encargado');
                    $cell->setFontColor('#FFFFFF');
                    $cell->setBackground('#ff8000');
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i = 4;
                foreach ($control as $value) {
                    $sala = Sala::where('id', $value->id_sala)->first();
                    $user = User::where('id', $value->id_usuariocrea)->first();
                    if (!is_null($value)) {
                        $sheet->cell('A' . $i, function ($cell) use ($value) {
                            $cell->setValue(substr($value->created_at, 0, 10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($sala) {
                            if(!is_null($sala)){
                                $cell->setValue($sala->nombre_sala);
                            }
                            $cell->setValue('');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('C' . $i, function ($cell) use ($value) {
                            if (!is_null($value->frecuencia1)) {
                                $cell->setValue(substr($value->frecuencia1, 10, 20));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            } else {
                                $cell->setValue("");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        });
                        $sheet->cell('D' . $i, function ($cell) use ($value) {
                            if (!is_null($value->frecuencia2)) {
                                $cell->setValue(substr($value->frecuencia2, 10, 20));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            } else {
                                $cell->setValue("");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        });
                        $sheet->cell('E' . $i, function ($cell) use ($value) {
                            if (!is_null($value->frecuencia3)) {
                                $cell->setValue(substr($value->frecuencia3, 10, 20));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            } else {
                                $cell->setValue("");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        });
                        $sheet->cell('F' . $i, function ($cell) use ($value) {
                            if (!is_null($value->frecuencia4)) {
                                $cell->setValue(substr($value->frecuencia4, 10, 20));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            } else {
                                $cell->setValue("");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        });
                        $sheet->cell('G' . $i, function ($cell) use ($value) {
                            if (!is_null($value->frecuencia5)) {
                                $cell->setValue(substr($value->frecuencia5, 10, 20));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            } else {
                                $cell->setValue("");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        });
                        $sheet->cell('H' . $i, function ($cell) use ($value) {
                            if (!is_null($value->frecuencia6)) {
                                $cell->setValue(substr($value->frecuencia6, 10, 20));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            } else {
                                $cell->setValue("");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        });
                        $sheet->cell('I' . $i, function ($cell) use ($value) {
                            if (!is_null($value->frecuencia7)) {
                                $cell->setValue(substr($value->frecuencia7, 10, 20));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            } else {
                                $cell->setValue("");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        });
                        $sheet->cell('J' . $i, function ($cell) use ($value) {
                            if (!is_null($value->frecuencia8)) {
                                $cell->setValue(substr($value->frecuencia8, 10, 20));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            } else {
                                $cell->setValue("");
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        });
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->desinfectante);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('L' . $i, function ($cell) use ($user) {
                            $cell->setValue($user->nombre1 . ' ' . $user->nombre2 . ' ' . $user->apellido1 . ' ' . $user->apellido2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $i++;
                    }
                }
            });
        })->export('xlsx');
    }
    public function editar_horario($id)
    {

        return view('servicios_generales.limpieza_horarios.vistaeditar', ['dato' => MantenimientoHorario::where('id', $id)->first()]);
    }

    public function editarcompleto(Request $request)
    {

        $control = MantenimientoHorario::where('id', $request['id'])->first();
        $control->frecuencia1 = date('Y-m-d h:i:s', strtotime($request['frecuencia1']));
        $control->frecuencia2 = date('Y-m-d h:i:s', strtotime($request['frecuencia2']));
        $control->frecuencia3 = date('Y-m-d h:i:s', strtotime($request['frecuencia3']));
        $control->frecuencia4 = date('Y-m-d h:i:s', strtotime($request['frecuencia4']));
        $control->frecuencia5 = date('Y-m-d h:i:s', strtotime($request['frecuencia5']));
        $control->frecuencia6 = date('Y-m-d h:i:s', strtotime($request['frecuencia6']));
        $control->frecuencia7 = date('Y-m-d h:i:s', strtotime($request['frecuencia7']));
        $control->frecuencia8 = date('Y-m-d h:i:s', strtotime($request['frecuencia8']));
        $control->save();
        return json_encode('ok');
    }
}
