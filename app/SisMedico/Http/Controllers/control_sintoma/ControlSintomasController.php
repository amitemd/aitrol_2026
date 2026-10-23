<?php

namespace  Sis_medico\Http\Controllers\control_sintoma;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sis_medico\ControlSintoma;
use Sis_medico\UsuariosControl;
use Illuminate\Support\Facades\Auth;
use Excel;
use PHPExcel_Style_Alignment;
use Sis_medico\User;
use Carbon\Carbon;

class ControlSintomasController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function index()
    {
        $control = ControlSintoma::paginate(10);
        return view('control_sintoma.index', ['user' => $control]);
    }
    public function save(Request $request)
    {

        $form = $request->all();
        $myValue =  array();
        parse_str($form['formulario'], $myValue);
        $general = 1;
        $diffrespira = 1;
        $contacto = 1;
        $res1 = array_key_exists('general', $myValue);
        $res2 = array_key_exists('diffrespira', $myValue);
        $res3 = array_key_exists('contacto', $myValue);

        if ($res1 == false) {

            $general = 0;
        }
        if ($res2 == false) {
            $diffrespira = 0;
        }
        if ($res3 == false) {
            $contacto = 0;
        }
        try {
            $control = new ControlSintoma;
            $control->fecha_registro = $myValue['fecha'];
            $control->cedula = $myValue['usuarios'];
            $control->sexo = $myValue['sexo'];
            $control->edad = $myValue['edad'];
            $control->ciudad_pais = $myValue['pais'];
            $control->temperatura = $myValue['temperatura'];
            $control->tos = $myValue['temperatura'];
            $control->tos = $general;
            $control->dificultad_respiratoria = $diffrespira;
            $control->ultima_dosis = $myValue['dosi'];
            $control->contacto_paciente = $contacto;
            $control->sintoma_compatible = $myValue['sintomas'];
            $control->save();
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }

        return json_encode(true);
    }

    public function editar(Request $request)
    {

        $form = $request->all();
        $myValue =  array();
        parse_str($form['formulario'], $myValue);
        $general = 1;
        $diffrespira = 1;
        $contacto = 1;
        $res1 = array_key_exists('general', $myValue);
        $res2 = array_key_exists('diffrespira', $myValue);
        $res3 = array_key_exists('contacto', $myValue);

        if ($res1 == false) {
            //dd("asd");
            $general = 0;
        }
        if ($res2 == false) {
            $diffrespira = 0;
        }
        if ($res3 == false) {
            $contacto = 0;
        }

        try {
            $control = ControlSintoma::find($myValue['id']);
            $control->fecha_registro = $myValue['fecha'];
            $control->cedula = $myValue['usuarios'];
            $control->sexo = $myValue['sexo'];
            $control->edad = $myValue['edad'];
            $control->ciudad_pais = $myValue['pais'];
            $control->temperatura = $myValue['temperatura'];
            $control->tos = $general;
            $control->dificultad_respiratoria = $diffrespira;
            $control->ultima_dosis = $myValue['dosi'];
            $control->contacto_paciente = $contacto;
            $control->sintoma_compatible = $myValue['sintomas'];
            $control->save();
        } catch (\Exception $e) {
            return json_encode($e->getMessage());
        }

        return json_encode(true);
    }

    public function buscar(Request $request)
    {
        //dd($request->all());
        $fecheDesde = $request['desde'];
        $fecheHasta = $request['hasta'];
        $control     = ControlSintoma::query();
        $date = date('Y-m-d');
        if ($request['excel'] == 1) {

            if (!is_null($request['usuarios'])) {

                $control = $control->where('cedula', $request['usuarios']);
            }

            if (!is_null($fecheDesde) && !is_null($fecheHasta)) {

                $control = $control->where('fecha_registro', '>=', $fecheDesde . ' 00:00:00')->where('fecha_registro', '<=', $fecheHasta . ' 23:59:59');
            }
            $control = $control->get();

            $titulos = array("FECHA", "NOMBRES Y APELLIDOS", "SEXO", "EDAD", "CIUDAD O PAIS QUE VISITO RECIENTEMENTE", "TEMPERATURA", "TOS EN GENERAL", "DIFICULTAD RESPITATORIA", "INDIQUE SI HA TENIDO ALGUN SINTOMA O TIENE ALGUN OTRO SINTOMA COMPATIBLE AL COVID COMO: SIN GUSTO U OLFATO,DOLOR DE CABEZA,DEARREA,MALESTAR GENERAL,ESCALOFRIOS", "ULTIMA DOSIS DE LA VACUNA", "HA TENIDO CONTACTO ESTRECHO CON COVID");


            //Posiciones en el excel
            $posicion = array("B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");
            $posicion1 = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T");






            Excel::create('Control de Sintomas', function ($excel) use ($titulos, $posicion, $control, $posicion1) {
                $excel->sheet('Control de Sintomas', function ($sheet) use ($titulos, $posicion, $control, $posicion1) {
                    $sheet->mergeCells('B1:E1');
                    $sheet->cell('B1', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('DATOS');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBackground('#FEE9D1');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->mergeCells('F1:L1');
                    $sheet->cell('F1', function ($cell) {
                        $cell->setValue('SINTOMAS');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBackground('#EEFFDA');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->mergeCells('A1:A2');
                    $sheet->cell('A1', function ($cell) {
                        $cell->setValue('NO.-');
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center');
                        $cell->setBackground('#EEFFDA');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });





                    $comienzo = 2; //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL 
                    /****************TITULOS DEL EXCEL*********************/
                    //crear los titulos en el excel
                    for ($i = 0; $i < count($titulos); $i++) {
                        //dd('' . $posicion[$i] . '' . $comienzo);
                        $sheet->cell('' . $posicion[$i] . '' . $comienzo, function ($cell) use ($titulos, $i) {
                            $cell->setValue($titulos[$i]);
                            $cell->setFontWeight('bold');
                            $cell->setBackground('#CCE1A7');
                            $cell->setAlignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                    $comienzo++;
                    $sexo = '';
                    $tos = '';
                    $contacto_paciente = '';
                    $dificultad_respiratoria = '';
                    foreach ($control as $sintoma) {
                        if ($sintoma->sexo == 1) {

                            $sexo = 'mujer';
                        } else {
                            $sexo = 'hombre';
                        }

                        if ($sintoma->tos == 1) {

                            $tos = 'si';
                        } else {
                            $tos = 'no';
                        }

                        if ($sintoma->contacto_paciente == 1) {

                            $contacto_paciente = 'si';
                        } else {
                            $contacto_paciente = 'no';
                        }
                        if ($sintoma->dificultad_respiratoria == 1) {

                            $dificultad_respiratoria = 'si';
                        } else {
                            $dificultad_respiratoria = 'no';
                        }


                        $datos_excel = array();
                        array_push($datos_excel, $sintoma->id, $sintoma->fecha_registro, $sintoma->usuario->nombre1 . ' ' . $sintoma->usuario->apellido1 . ' ' . $sintoma->usuario->apellido2, $sexo, $sintoma->edad, $sintoma->ciudad_pais, $sintoma->temperatura, $tos, $dificultad_respiratoria, $sintoma->sintoma_compatible, $sintoma->ultima_dosis, $contacto_paciente);
                        for ($i = 0; $i < count($datos_excel); $i++) {
                            $sheet->cell('' . $posicion1[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                $cell->setValue($datos_excel[$i]);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setAlignment('center');
                            });
                        }
                        $comienzo++;
                    }
                });



                $excel->getActiveSheet()->getColumnDimension("A")->setWidth(14)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("J")->setWidth(30)->setAutosize(false);
                $excel->getActiveSheet()->getStyle('J2')->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
                $excel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('B2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('I2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('L2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('C2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('D2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('E2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('F2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('G2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('H2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('J2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('K2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $excel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $excel->getActiveSheet()->getStyle('F1:K1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            })->export('xlsx');
        } else {

            //dd("as");


            if (!is_null($fecheDesde) && !is_null($fecheHasta)) {
                $control = $control->whereBetween('fecha_registro', [$request['desde'] . ' 00:00:00', $request['hasta'] . ' 23:59:59']);
            }
            if (!is_null($request['usuarios'])) {
                $control = $control->where('cedula', $request['usuarios']);
            }

            $control = $control->paginate(10);
        }


        return view('control_sintoma.index', ['user' => $control]);
    }

    public function usuarios(Request $request)
    {
        $campo      = strtoupper($request['term']);
        $valid_tags = [];
        $usuarios   = User::where('estado', '1')->where('id_tipo_usuario', '<>', '2');
        $usuarios   = $usuarios->where(function ($jq1) use ($campo) {
            $jq1->whereRaw('CONCAT(nombre1," ",apellido1) LIKE ?', ['%' . $campo . '%']);
        });
        $usuarios = $usuarios->get();
        foreach ($usuarios as $id => $users) {
            $edad         = Carbon::parse($users->fecha_nacimiento)->age;
            $valid_tags[] = ['id' => $users->id, 'nombreappe' => $users->nombre1 . ' ' . $users->apellido1 . ' ' . $users->apellido2, 'edad' => $edad];
        }

        return response()->json($valid_tags);
    }
}
