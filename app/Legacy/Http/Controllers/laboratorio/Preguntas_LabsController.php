<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Area; 
use Sis_medico\Preguntas_Labs;
use Sis_medico\GrupoPregunta;
use Sis_medico\TipoSugerencia;
use Sis_medico\Encuesta_Labs;
use Sis_medico\Paciente;
use Sis_medico\Encuesta_Complementolabs;
use Sis_medico\Sugerencia;
use Sis_medico\Examen_Orden;
use PHPExcel_Worksheet_Drawing;
use Illuminate\Support\Facades\DB;
use Excel;


class Preguntas_LabsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1)) == false){
          return true;
        }
    }

    public function index()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $pregunta = Preguntas_Labs::paginate(25);
 

        return view('laboratorio/pregunta_labs/index', ['preguntas' => $pregunta]);
    }

    public function create(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $grupopreguntalabs = GrupoPregunta::all();
         
        return view('laboratorio/pregunta_labs/create', ['grupopreguntalabs' => $grupopreguntalabs]);
    }

    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        Preguntas_Labs::create([

            'nombre' => $request['nombre'],
            'id_grupopregunta' => $request['id_grupopregunta'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        //return redirect()->intended('/sala-management');
        return redirect()->intended('/pregunta_labs');
    }

    private function validateInput($request) {
       $messages = [
        'nombre.required' => 'Agrega el nombre de la pregunta.',
        'id_grupopregunta.required' => 'Agrega al grupo que pertece la pregunta.',
        ];
        
        $constraints = [
            'nombre' => 'required',
            'id_grupopregunta' => 'required',
        ];

        $this->validate($request, $constraints, $messages);

    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $pregunta = Preguntas_Labs::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($pregunta == null || count($pregunta) == 0) {
            return redirect()->intended('/pregunta_labs');
        }
        $grupopreguntalabs = GrupoPregunta::all();
        return view('laboratorio/pregunta_labs/edit', ['pregunta' => $pregunta, 'grupopreguntalabs' => $grupopreguntalabs]);
    }

    public function update(Request $request,  $id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $Pregunta = Preguntas_Labs::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $messages = [
        'nombre.required' => 'Agrega el nombre del area.',
        'id_grupos_Labs.required' => 'Agrega la descripcion del area.',
          
        ];

        
        $constraints = [
        'nombre' => 'required',           
        'id_grupopregunta' => 'required',           
        'estado' => 'required'
            ];
  
                

        $input = [
            'nombre' => $request['nombre'],
            'id_grupopregunta' => $request['id_grupopregunta'],
            'estado' => $request['estado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];
       
        

        $this->validate($request, $constraints, $messages);

        Preguntas_Labs::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/pregunta_labs');
    }
    public function resultados_labs(){
        if($this->rol()){
            return response()->view('errors.404');
        } 
       
        $grupopregunta = GrupoPregunta::all();
        $preguntas = DB::table('encuesta_labs as el')
                        ->join('encuesta_complementolabs as ecl', 'el.id', 'ecl.id_encuesta_labs')
                        ->join('pregunta_labs as pl' ,'ecl.id_pregunta_labs', 'pl.id')
                        ->get();
       // dd($preguntas);
        
        $encuesta = Encuesta_Labs::get();
        //dd($encuesta);
        return view('laboratorio/encuesta_laboratorio/list_encuesta_labs', [ 'encuesta'=>$encuesta, 'grupopregunta'=>$grupopregunta, 'preguntas'=>$preguntas]);
    }
    public function estadisticalabs (Request $request){
        $anio=$request['anio'];
        $mes=$request['mes'];

        if($request['anio']==null){
             $anio=date('Y');
        }
        if($request['mes']==null){
            $mes=date('m');
        }
        $encuestas=Encuesta_Labs::whereNotNull('id_paciente')->where('mes',$mes)->where('anio',$anio)->get(); 
        
        $preguntas = Preguntas_Labs::where('id_grupopregunta','1')->get();
        //dd($preguntas);
        $preguntas_e = Preguntas_Labs::where('id_grupopregunta','2')->get();
       //dd($preguntas_e);
        return view('laboratorio/encuesta_laboratorio/estadistica_labs',['preguntas'=>$preguntas, 'encuestas'=>$encuestas,'anio'=>$anio,'preguntas_e'=>$preguntas_e,'mes'=>$mes]);
    }
      public function detalle_mes_labs(Request $request)
    {

        //return "hola";
        $anio = $request['anio'];
        $mes = $request['mes'];

        if ($request['anio'] == null) {
            $anio = date('Y');
        }
        if ($request['mes'] == null) {
            $mes = date('m');
        }
        $encuestas = Encuesta_Labs::whereNotNull('id_paciente')->where('mes', $mes)->where('anio', $anio)->get();

        $preguntas = Preguntas_Labs::where('id_grupopregunta', '1')->get();
        //dd($preguntas);
        $preguntas_e = Preguntas_Labs::where('id_grupopregunta', '2')->get();

        //dd($preguntas_procedimientos);

        Excel::create('Detalle de Encuestas LABS', function ($excel) use ($encuestas, $preguntas, $anio, $mes, $preguntas_e) {

            $excel->sheet('Consulta LABS', function ($sheet) use ($encuestas, $preguntas, $anio, $mes, $preguntas_e) {

                $sheet->mergeCells('A1:S1');
                $sheet->mergeCells('A2:P2');

                if ($mes == "01") {
                    $mes_letra = "ENERO";
                }
                if ($mes == "02") {
                    $mes_letra = "FEBRERO";
                }
                if ($mes == "03") {
                    $mes_letra = "MARZO";
                }
                if ($mes == "04") {
                    $mes_letra = "ABRIL";
                }
                if ($mes == "05") {
                    $mes_letra = "MAYO";
                }
                if ($mes == "06") {
                    $mes_letra = "JUNIO";
                }
                if ($mes == "07") {
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
                $sheet->cell('A1', function ($cell) use ($mes_letra, $anio) {
                    // manipulate the cel
                    $cell->setValue('Encuestas LABS' . ' ' . $mes_letra . ' - ' . $anio);
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE DE PREGUNTAS');
                    $cell->setFontWeight('bold');
                });
                $sheet->mergeCells('D3:K3');
                $sheet->cell('C3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('Pregunta');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $cont = 1;
                $i = 4;

                foreach ($preguntas as $value) {
                    $sheet->mergeCells('D' . $i . ':K' . $i);
                    $sheet->cell('C' . $i, function ($cell) use ($cont) {
                        // manipulate the cel
                        $cell->setValue($cont);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    $cont++;
                }
                $i++;

                $sheet->cell('A' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B' . $i, function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                $ltr = 0;
                $x = 1;
                foreach ($preguntas as $value) {
                    $sheet->cell($arr[$ltr] . $i, function ($cell) use ($ltr, $x) {
                        // manipulate the cel
                        $cell->setValue('Pregunta ' . $x);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $ltr++;
                    $x++;
                }

                $sheet->cell('G' . $i, function ($cell) use ($ltr, $x) {
                    // manipulate the cel
                    $cell->setValue('Fecha Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;
                $cantidad = 1;
                foreach ($encuestas as $val) {
                    // dd($val);
                    $fecha_crea = substr($val->created_at, 0, 10);
                    $valexamen = Examen_Orden::where('id_paciente', $val->id_paciente)->where('anio', $anio)->where('mes', $mes)->first();

                    $sheet->cell('A' . $i, function ($cell) use ($cantidad) {
                        // manipulate the cel
                        $cell->setValue($cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $paciente = Paciente::find($val->id_paciente);
                    $nombre = '';
                    if (!is_null($paciente)) {
                        $nombre = $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2;
                    }
                    $sheet->cell('B' . $i, function ($cell) use ($val, $nombre) {
                        // manipulate the cel
                        $cell->setValue($nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                    $ltr = 0;
                    $x = 1;
                    foreach ($preguntas as $p) {
                        $txt = '';
                        $complemento = Encuesta_Complementolabs::where('id_encuesta_labs', $val->id)->where('id_pregunta_labs', $p->id)->first();
                        if (!is_null($complemento)) { //4-2=>bueno-regular-malo
                            $txt = $complemento->valor;

                            if ($complemento->valor == '5') {
                                $txt = 'MUY BUENO';
                            }
                            if ($complemento->valor == '4') {
                                $txt = 'BUENO';
                            }
                            if ($complemento->valor == '3') {
                                $txt = 'NI BUENO NI MALO';
                            }
                            if ($complemento->valor == '2') {
                                $txt = 'MALO';
                            }
                            if ($complemento->valor == '1') {
                                $txt = 'MUY MALO';
                            }
                        }
                        $sheet->cell($arr[$ltr] . $i, function ($cell) use ($txt, $ltr, $x) {
                            // manipulate the cel
                            $cell->setValue($txt);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $ltr++;
                        $x++;
                    }

                    $sheet->cell('G' . $i, function ($cell) use ($val, $ltr, $x) {
                        $cell->setValue($val->created_at);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $cantidad++;
                    $i++;
                }
                // CALIFICACION LABS

                $c = $i + 5;

                $sheet->cell('A15', function ($cell) use ($mes_letra, $anio) {
                    // manipulate the cel
                    $cell->setValue('Calificaciones LABS' . ' ' . $mes_letra . ' - ' . $anio);
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('A16', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE DE PREGUNTAS');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('A18' , function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CRITERIOS DE EVALUACIÓN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B18' , function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MUY BUENO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C18', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BUENO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D18', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NI BUENO NI MALO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E18', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MALO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F18', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MUY MALO');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G18', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL DE RESPUESTAS RECIBIDA POR PREGUNTAS');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A20', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TOTAL DE RESPUESTAS RECIBIDA POR CALIFICACIÓNTOTAL DE RESPUESTAS RECIBIDA POR CALIFICACIÓN');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $total_encuestas2= count($encuestas); 
							$suma=0;
							$suma1=0;
							$suma2=0;
							$suma3=0;
							$suma4=0;
							//armamos un array
							$estadistico2=array();
                foreach ($preguntas_e as $value2)

                $calificacion1 = 0;
                $calificacion2 = 0;
                $calificacion3 = 0;
                $calificacion4 = 0;
                $calificacion5 = 0;
                // detalle para sumar
                foreach ($encuestas as $x) {
                    //dd($x->complementos);
                    $calificacion1 = $calificacion1 + $x->complementos->where('id_pregunta_labs', $value2->id)->where('calificacion', '5')->count();

                    $calificacion2 = $calificacion2 + $x->complementos->where('id_pregunta_labs', $value2->id)->where('calificacion', '4')->count();

                    $calificacion3 = $calificacion3 + $x->complementos->where('id_pregunta_labs', $value2->id)->where('calificacion', '3')->count();

                    $calificacion4 = $calificacion4 + $x->complementos->where('id_pregunta_labs', $value2->id)->where('calificacion', '2')->count();

                    $calificacion5 = $calificacion5 + $x->complementos->where('id_pregunta_labs', $value2->id)->where('calificacion', '1')->count();
                }

                $suma += $calificacion1;
                $suma1 += $calificacion2;
                $suma2 += $calificacion3;
                $suma3 += $calificacion4;
                $suma4 += $calificacion5;

                if ($total_encuestas2 <> 0) {
                }
                $preguntasf = ['Muy bueno', 'Bueno', 'Ni bueno ni malo', 'Malo', 'Muy malo'];

                foreach ($preguntasf as $p) {
                    $armar2 = array();
                    if ($p == "Muy bueno") {
                        $armar2['clasificador'] = $p;
                        $armar2['valor'] = $calificacion1;
                    } elseif ($p == "Bueno") {
                        $armar2['clasificador'] = $p;
                        $armar2['valor'] = $calificacion2;
                    } elseif ($p == "Ni bueno ni malo") {
                        $armar2['clasificador'] = $p;
                        $armar2['valor'] = $calificacion3;
                    } elseif ($p == "Malo") {
                        $armar2['clasificador'] = $p;
                        $armar2['valor'] = $calificacion4;
                    } elseif ($p == "Muy malo") {
                        $armar2['clasificador'] = $p;
                        $armar2['valor'] = $calificacion5;
                    }
                    array_push($estadistico2, $armar2);
                }
                $sheet->cell('A19',function ($cell) use ($value2) {
                    // manipulate the cel
                    $cell->setValue($value2->nombre);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B19',function ($cell) use ($calificacion1) {
                    // manipulate the cel
                    $cell->setValue($calificacion1);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C19' , function ($cell) use ($calificacion2) {
                    // manipulate the cel
                    $cell->setValue($calificacion2);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D19', function ($cell) use ($calificacion3) {
                    // manipulate the cel
                    $cell->setValue($calificacion3);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E19',function ($cell) use ($calificacion4) {
                    // manipulate the cel
                    $cell->setValue($calificacion4);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F19', function ($cell) use ($calificacion5) {
                    // manipulate the cel
                    $cell->setValue($calificacion5);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G19',function ($cell) use ($total_encuestas2) {
                    // manipulate the cel
                    $cell->setValue($total_encuestas2);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B20',function ($cell) use ($suma) {
                    // manipulate the cel
                    $cell->setValue($suma);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C20' , function ($cell) use ($suma1) {
                    // manipulate the cel
                    $cell->setValue($suma1);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D20', function ($cell) use ($suma2) {
                    // manipulate the cel
                    $cell->setValue($suma2);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E20',function ($cell) use ($suma3) {
                    // manipulate the cel
                    $cell->setValue($suma3);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F20', function ($cell) use ($suma4) {
                    // manipulate the cel
                    $cell->setValue($suma4);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
            });
        })->export('xlsx');
    }

}
 