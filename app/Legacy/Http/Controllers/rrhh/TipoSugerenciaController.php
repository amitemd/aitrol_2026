<?php

namespace Sis_medico\Http\Controllers\rrhh;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Area;
use Sis_medico\TipoSugerencia;
use Sis_medico\Sugerencia;
use Sis_medico\Pregunta;
use Sis_medico\GrupoPregunta;
use Sis_medico\Master_encuesta;
use Sis_medico\Encuesta_1;
use Sis_medico\Encuesta_Labs;
use Sis_medico\Paciente;
use Sis_medico\Encuesta_Complemento;
use Sis_medico\Encuesta_Complementolabs;
use Excel;
use Sis_medico\Agenda;
use Sis_medico\Empresa;
use Sis_medico\Historiaclinica;
use Sis_medico\Sala;
use Sis_medico\Http\Controllers\excelCreate;
use Sis_medico\Seguro;
use Sis_medico\Http\Controllers\SinLoginController;
class TipoSugerenciaController extends Controller
{
    protected $redirectTo = '/area';

         /**
     * Create a new controller instance.
     *
     * @return void
     */ 
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 11)) == false) {
            return true;
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $tiposugerencia = TipoSugerencia::paginate(25);
      

        return view('rrhh/tiposugerencia/index', ['tiposugerencia' => $tiposugerencia]);
    }

    public function create()
    {
        if($this->rol()){
            return response()->view('errors.404');
        }

        return view('rrhh/tiposugerencia/create');
    }

    public function store(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $this->validateInput($request);
        date_default_timezone_set('America/Guayaquil');
        TipoSugerencia::create([

            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
        ]);

        //return redirect()->intended('/sala-management');
        return redirect()->intended('/tipo_sugerencia');
    }

    private function validateInput($request)
    {
        $messages = [
            'nombre.required' => 'Agrega el nombre del Area.',
            'descripcion.required' => 'Agrega la descripcion del Area.',
        ];
        
        $constraints = [
        	'nombre' => 'required',
        	'descripcion' => 'required',
        ];

        $this->validate($request, $constraints, $messages);

    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $tiposugerencia = TipoSugerencia::find($id);
        // Redirect to user list if updating user wasn't existed
        if ($tiposugerencia == null || count($tiposugerencia) == 0) {
            return redirect()->intended('/tipo_sugerencia');
        }

        return view('rrhh/tiposugerencia/edit', ['tiposugerencia' => $tiposugerencia]);
    }

    public function update(Request $request,  $id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $tipo_sugerencia = TipoSugerencia::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $messages = [
        'nombre.required' => 'Agrega el nombre del area.',
        'descripcion.required' => 'Agrega la descripcion del area.',
          
        ];

        
        $constraints = [
        'nombre' => 'required',           
        'descripcion' => 'required'

            ];
  
                

        $input = [
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => $request['descripcion'],
            'estado' => $request['estado'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];
       
        

        $this->validate($request, $constraints, $messages);

        TipoSugerencia::where('id', $id)
            ->update($input);
        
        return redirect()->intended('/tipo_sugerencia');
    }

    public function resultados()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $sugerencia = Sugerencia::paginate(25);
        $area = Area::all();
        $tiposugerencia  = TipoSugerencia::all();
        $id_area = null;
        $id_tiposugerencia = null;
        $fecha = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');    

        return view('rrhh/resultados/index', ['sugerencia' => $sugerencia, 'area' => $area,  'tiposugerencia' => $tiposugerencia, 'id_area' => $id_area, 'id_tiposugerencia' => $id_tiposugerencia, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta]);
    }

    public function search(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }


        $id_area = $request['id_area'];
        $id_tiposugerencia = $request['id_tiposugerencia'];;
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];    
        $sugerencia = Sugerencia::where('id_area', 'like', '%'.$id_area.'%')
                            ->where('id_tiposugerencia', 'like', '%'.$id_tiposugerencia.'%')
                            ->whereBetween('created_at', [$fecha.' 00:00:00', $fecha_hasta.' 23:59:59'])
                            ->paginate(25);
        if($request['fecha'] == "" || $request['fecha'] == null){
                $sugerencia = Sugerencia::where('id_area', 'like', '%'.$id_area.'%')
                            ->where('id_tiposugerencia', 'like', '%'.$id_tiposugerencia.'%')
                            ->paginate(25);
        }                   
        $area = Area::all();
        $tiposugerencia  = TipoSugerencia::all();
        

        return view('rrhh/resultados/index', ['sugerencia' => $sugerencia, 'area' => $area,  'tiposugerencia' => $tiposugerencia, 'id_area' => $id_area, 'id_tiposugerencia' => $id_tiposugerencia, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta]);
    }

    public function resultados_ok()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        } 
        
        $grupopregunta = GrupoPregunta::all();
        
        $encuesta = Encuesta_1::get();

        return view('rrhh/listado_encuesta', [ 'encuesta'=>$encuesta, 'grupopregunta'=>$grupopregunta]);
    }

    public function listado_index(){
        $encuestas = Master_encuesta::where('estado', 1)->get();

        return view('rrhh/listado_index',['encuestas'=>$encuestas]);
    }


    public function listado_detalle($id, Request $request){
                
        $grupopregunta = GrupoPregunta::all();

        $anio = $request->anio;
        $mes = $request->mes;

        if (is_null($anio)) {
            $anio = date("Y");
        }

        if (is_null($mes)) {
            $mes = date("m");
        }
        
        $encuesta = Encuesta_1::where('id_area',$id)->where('mes', $mes)->where('anio', $anio)->get();
       // dd($encuesta);

        return view('rrhh/nuevo_listado_encuesta', [ 'encuesta'=>$encuesta, 'grupopregunta'=>$grupopregunta, 'anio' => $anio, 'mes' => $mes, 'id' => $id]);
    }
    public function rrhh_estadisticas (Request $request){
        $anio=$request['anio'];
        $mes=$request['mes'];

        if($request['anio']==null){
             $anio=date('Y');
        }
        if($request['mes']==null){
            $mes=date('m');
        }
       
        
        //$anio='2019';
        //$mes='12';
        $encuestas = Encuesta_1::where('anio',$anio)->where('mes',$mes)->get();
        $master_encuestas = Master_encuesta::all();
        $preguntas = Pregunta::where('id_grupopregunta','1')->join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*','pe.id_masterencuesta')->get();
        //dd($preguntas->where('id','11')->first()->nombre);
        $preguntas_tiempo = Pregunta::where('id_grupopregunta','2')->join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*')->where('id_masterencuesta','1')->get();
       
        return view('rrhh.estadisticas',['preguntas'=>$preguntas, 'encuestas'=>$encuestas,'anio'=>$anio,'master_encuestas'=>$master_encuestas,'preguntas_tiempo'=>$preguntas_tiempo,'mes'=>$mes]);
    }
    public function detalle_mes (Request $request){

        //return "hola";
        $anio=$request['anio'];
        $mes=$request['mes'];

        if($request['anio']==null){
             $anio=date('Y');
        }
        if($request['mes']==null){
            $mes=date('m');
        }
       
        $encuestas_consultas = Encuesta_1::where('anio',$anio)->where('mes',$mes)->where('id_area','1')->get();
        $encuestas_procedimientos = Encuesta_1::where('anio',$anio)->where('mes',$mes)->where('id_area','2')->get();
        

        
        $preguntas_consultas = Pregunta::join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*','pe.id_masterencuesta')->where('pe.id_masterencuesta','1')->get();
        $preguntas_procedimientos = Pregunta::join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*','pe.id_masterencuesta')->where('pe.id_masterencuesta','2')->get();
        //dd($preguntas_procedimientos);

        Excel::create('Detalle de Encuestas', function($excel) use($encuestas_consultas, $preguntas_consultas, $anio, $mes, $preguntas_procedimientos, $encuestas_procedimientos) {

            $excel->sheet('Consulta Agendas', function($sheet) use($encuestas_consultas, $preguntas_consultas, $anio, $mes, $preguntas_procedimientos, $encuestas_procedimientos) {
                
                $sheet->mergeCells('A1:S1');
                $sheet->mergeCells('A2:P2');  

                if($mes == 01){ $mes_letra = "ENERO";} 
                if($mes == 02){ $mes_letra = "FEBRERO";} 
                if($mes == 03){ $mes_letra = "MARZO";} 
                if($mes == 04){ $mes_letra = "ABRIL";} 
                if($mes == 05){ $mes_letra = "MAYO";} 
                if($mes == 06){ $mes_letra = "JUNIO";} 
                if($mes == 07){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $sheet->cell('A1', function($cell) use($mes_letra, $anio){
                    // manipulate the cel
                    $cell->setValue('ENCUESTAS DE CONSULTAS '.$mes_letra.' - '.$anio);
                    $cell->setFontWeight('bold');
                    
                });
                $sheet->cell('A2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE DE PREGUNTAS');
                    $cell->setFontWeight('bold');
                    
                });
                $sheet->mergeCells('D3:K3');
                $sheet->cell('C3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('pregunta');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $cont = 1;$i = 4;

                foreach ($preguntas_consultas as $value) {
                    $sheet->mergeCells('D'.$i.':K'.$i);
                    $sheet->cell('C'.$i, function($cell) use($cont) {
                        // manipulate the cel
                        $cell->setValue($cont);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $i++;$cont++;

                }
                $i++;

                $sheet->cell('A'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                $ltr = 0;$x=1;
                foreach ($preguntas_consultas as $value) {
                     $sheet->cell($arr[$ltr].$i, function($cell) use($ltr,$x) {
                        // manipulate the cel
                        $cell->setValue('Pregunta '.$x);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                     $ltr++;$x++;
                }
                $sheet->cell('L'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('IP Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('N'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('Unidad');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;$cantidad = 1;
                foreach($encuestas_consultas as $val){
                    $fecha_crea = substr($val->created_at, 0,10);
                    $agenda = Agenda::where('id_paciente',$val->id_paciente)->whereBetween('fecha_ini', [$fecha_crea . ' 00:00', $fecha_crea . ' 23:59'])->first();
                    $sala = Sala::where('id',$agenda->id_sala)->first();

                    $sheet->cell('A'.$i, function($cell) use($cantidad) {
                        // manipulate the cel
                        $cell->setValue($cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $paciente = Paciente::find($val->id_paciente);
                    $nombre = '';
                    if(!is_null($paciente)){
                        $nombre = $paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2;
                    }
                    $sheet->cell('B'.$i, function($cell) use($val, $nombre) {
                        // manipulate the cel
                        $cell->setValue($nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                    $ltr = 0;$x=1;
                    foreach ($preguntas_consultas as $p) {
                        $txt = '';
                        $complemento = Encuesta_Complemento::where('id_encuesta_1',$val->id)->where('id_pregunta',$p->id)->first();
                        if(!is_null($complemento)){//4-2=>bueno-regular-malo
                            $txt = $complemento->valor;
                            if($complemento->valor=='4'){
                                $txt = 'BUENO';
                            }
                            if($complemento->valor=='3'){
                                $txt = 'REGULAR';
                            }
                            if($complemento->valor=='2'){
                                $txt = 'MALO';
                            }
                        }
                        $sheet->cell($arr[$ltr].$i, function($cell) use($txt, $ltr, $x) {
                            // manipulate the cel
                            $cell->setValue($txt);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $ltr++;$x++;
                    }
                    $sheet->cell('L'.$i, function($cell) use($val) {
                        // manipulate the cel
                        if($val->ip_ingreso == '192.168.80.7'){
                            $cell->setValue("SLIGNA");}
                        elseif ($val->ip_ingreso == '192.168.80.6') {
                           $cell->setValue("ESANCHEZ");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.8') {
                           $cell->setValue("MCIFUENTES");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.9') {
                           $cell->setValue("OGALLARDO");
                        }
                        elseif ($val->ip_ingreso == '192.168.75.216') {
                           $cell->setValue("VZAMBRANO");
                        }
                        elseif ($val->ip_ingreso == '10.0.0.1') {
                           $cell->setValue("WEB");
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $sheet->cell('M'.$i, function($cell) use($val) {
                        $cell->setValue($val->created_at);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 

                    $sheet->cell('N'.$i, function($cell) use($sala) {
                        $cell->setValue($sala->hospital->nombre_hospital);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $cantidad++;$i++;      
                }
                $i= $i+2;
                $sheet->mergeCells('A'.$i.':S'.$i); 
                $sheet->cell('A'.$i, function($cell) use($mes_letra, $anio){
                    // manipulate the cel
                    $cell->setValue('ENCUESTAS DE PROCEDIMIENTOS '.$mes_letra.' - '.$anio);
                    $cell->setFontWeight('bold');
                    
                });
                $i++;
                $sheet->mergeCells('A'.$i.':S'.$i); 
                $sheet->cell('A'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE DE PREGUNTAS');
                    $cell->setFontWeight('bold');
                    
                });
                $i++;
                $sheet->mergeCells('D'.$i.':K'.$i);
                $sheet->cell('C'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('pregunta');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;$cont = 1;
                foreach ($preguntas_procedimientos as $value) {
                    $sheet->mergeCells('D'.$i.':K'.$i);
                    $sheet->cell('C'.$i, function($cell) use($cont) {
                        // manipulate the cel
                        $cell->setValue($cont);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $i++;$cont++;

                }
                $i++;

                $sheet->cell('A'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                $ltr = 0;$x=1;
                foreach ($preguntas_procedimientos as $value) {
                     $sheet->cell($arr[$ltr].$i, function($cell) use($ltr,$x) {
                        // manipulate the cel
                        $cell->setValue('Pregunta '.$x);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                     $ltr++;$x++;
                }
                $sheet->cell('K'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('IP Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('Fecha Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('M'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('Unidad');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $i++;$cantidad = 1;
                foreach($encuestas_procedimientos as $val){
                    $fecha_crea = substr($val->created_at, 0,10);
                    $agenda = Agenda::where('id_paciente',$val->id_paciente)->whereBetween('fecha_ini', [$fecha_crea . ' 00:00', $fecha_crea . ' 23:59'])->first();
                    $sala = Sala::where('id',$agenda->id_sala)->first();
                    $sheet->cell('A'.$i, function($cell) use($cantidad) {
                        // manipulate the cel
                        $cell->setValue($cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $paciente = Paciente::find($val->id_paciente);
                    $nombre = '';
                    if(!is_null($paciente)){
                        $nombre = $paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2;
                    }
                    $sheet->cell('B'.$i, function($cell) use($val, $nombre) {
                        // manipulate the cel
                        $cell->setValue($nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                    $ltr = 0;$x=1;
                    foreach ($preguntas_procedimientos as $p) {
                        $txt = '';
                        $complemento = Encuesta_Complemento::where('id_encuesta_1',$val->id)->where('id_pregunta',$p->id)->first();
                        if(!is_null($complemento)){//4-2=>bueno-regular-malo
                            $txt = $complemento->valor;
                            if($complemento->valor=='4'){
                                $txt = 'BUENO';
                            }
                            if($complemento->valor=='3'){
                                $txt = 'REGULAR';
                            }
                            if($complemento->valor=='2'){
                                $txt = 'MALO';
                            }
                        }
                        $sheet->cell($arr[$ltr].$i, function($cell) use($txt, $ltr, $x) {
                            // manipulate the cel
                            $cell->setValue($txt);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $ltr++;$x++;
                    }
                    $sheet->cell('K'.$i, function($cell) use($val) {
                        // manipulate the cel
                        if($val->ip_ingreso == '192.168.80.7'){
                            $cell->setValue("SLIGNA");}
                        elseif ($val->ip_ingreso == '192.168.80.6') {
                           $cell->setValue("ESANCHEZ");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.8') {
                           $cell->setValue("MCIFUENTES");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.9') {
                           $cell->setValue("OGALLARDO");
                        }
                        elseif ($val->ip_ingreso == '192.168.75.216') {
                           $cell->setValue("VZAMBRANO");
                        }
                        elseif ($val->ip_ingreso == '10.0.0.1') {
                           $cell->setValue("WEB");
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $sheet->cell('L'.$i, function($cell) use($val) {
                        $cell->setValue($val->created_at);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $sheet->cell('M'.$i, function($cell) use($sala) {
                        $cell->setValue($sala->hospital->nombre_hospital);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $cantidad++;$i++;      
                }
                                 
                    
                    
            });
        })->export('xlsx');
        
        
       
        
    }

    public function encuesta_estadistica(){
        
        $encuestas = Master_encuesta::where('estado', 1)->get();
        return view('rrhh/encuestas_estadistica',['encuestas' => $encuestas]);
    }

    public function rrhh_estadisticas_2 (Request $request, $id){
        $anio=$request['anio'];
        $mes=$request['mes'];

        if($request['anio']==null){
             $anio=date('Y');
        }
        if($request['mes']==null){
            $mes=date('m');
        }
       
        
        //$anio='2019';
        //$mes='12';
        $encuestas = Encuesta_1::where('anio',$anio)->where('mes',$mes)->get();
        $master_encuestas = Master_encuesta::find($id);
        $preguntas = Pregunta::where('id_grupopregunta','1')->join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*','pe.id_masterencuesta')->where('id_masterencuesta', $id)->get();
        //dd($preguntas->where('id','11')->first()->nombre);
        $preguntas_tiempo = Pregunta::where('id_grupopregunta','2')->join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id)->get();
       
        return view('rrhh/prueba_estadisticas',['preguntas'=>$preguntas, 'encuestas'=>$encuestas,'anio'=>$anio,'master_encuestas'=>$master_encuestas,'preguntas_tiempo'=>$preguntas_tiempo,'mes'=>$mes, 'id' => $id]);
    }

    public function detalle_mes_2 (Request $request,$id){

        //return "hola";
        $anio=$request['anio'];
        $mes=$request['mes'];
        $fecha = $request->fecha;

        if($request['anio']==null){
             $anio=date('Y');
        }
        if($request['mes']==null){
            $mes=date('m');
        }
        $master_encuestas = Master_encuesta::find($id);
        if($fecha != null){
            $encuestas = Encuesta_1::whereBetween('created_at', [$fecha . ' 00:00:00', $fecha . ' 23:59:59'])->where('id_area', $id)->get();
        }else{
            $encuestas = Encuesta_1::where('anio',$anio)->where('mes',$mes)->where('id_area', $id)->get();
        }
        $preguntas = Pregunta::join('preguntas_encuestas as pe','pe.id_pregunta','pregunta.id')->select('pregunta.*','pe.id_masterencuesta')->where('pe.id_masterencuesta',$id)->get();
        
        //dd($preguntas_procedimientos);

        Excel::create('Detalle de Encuestas', function($excel) use($encuestas, $preguntas, $anio, $mes, $master_encuestas) {

            $excel->sheet('Consulta Agendas', function($sheet) use($encuestas, $preguntas, $anio, $mes, $master_encuestas) {
                
                $sheet->mergeCells('A1:S1');
                $sheet->mergeCells('A2:P2');  

                if($mes == '01'){ $mes_letra = "ENERO";} 
                if($mes == '02'){ $mes_letra = "FEBRERO";} 
                if($mes == '03'){ $mes_letra = "MARZO";} 
                if($mes == '04'){ $mes_letra = "ABRIL";} 
                if($mes == '05'){ $mes_letra = "MAYO";} 
                if($mes == '06'){ $mes_letra = "JUNIO";} 
                if($mes == '07'){ $mes_letra = "JULIO";} 
                if($mes == '08'){ $mes_letra = "AGOSTO";}  
                if($mes == '09'){ $mes_letra = "SEPTIEMBRE";} 
                if($mes == '10'){ $mes_letra = "OCTUBRE";} 
                if($mes == '11'){ $mes_letra = "NOVIEMBRE";} 
                if($mes == '12'){ $mes_letra = "DICIEMBRE";} 
                $sheet->cell('A1', function($cell) use($mes_letra, $anio, $master_encuestas){
                    // manipulate the cel
                    $cell->setValue($master_encuestas->descripcion.' '.$mes_letra.' - '.$anio);
                    $cell->setFontWeight('bold');
                    
                });
                $sheet->cell('A2', function($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE DE PREGUNTAS');
                    $cell->setFontWeight('bold');
                    
                });
                $sheet->mergeCells('D3:K3');
                $sheet->cell('C3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D3', function($cell) {
                    // manipulate the cel
                    $cell->setValue('pregunta');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $cont = 1;$i = 4;

                foreach ($preguntas as $value) {
                    $sheet->mergeCells('D'.$i.':K'.$i);
                    $sheet->cell('C'.$i, function($cell) use($cont) {
                        // manipulate the cel
                        $cell->setValue($cont);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $i++;$cont++;

                }
                $i++;

                $sheet->cell('A'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('No.');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B'.$i, function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                $ltr = 0;$x=1;
                foreach ($preguntas as $value) {
                     $sheet->cell($arr[$ltr].$i, function($cell) use($ltr,$x) {
                        // manipulate the cel
                        $cell->setValue('Pregunta '.$x);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                     $ltr++;$x++;
                }
                $sheet->cell('L'.$i, function($cell) use($ltr,$x){
                    // manipulate the cel
                    $cell->setValue('IP Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M'.$i, function($cell) use($ltr,$x) {
                    // manipulate the cel
                    $cell->setValue('Fecha Creacion');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('N'.$i, function($cell) use($ltr,$x) {
                    // manipulate the cel
                    $cell->setValue('Unidad');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $i++;$cantidad = 1;

                foreach($encuestas as $val){
                    $fecha_crea = substr($val->created_at, 0,10);
                    // dd($val);
                    //dd($fecha_crea);
                        $agenda = Agenda::where('id_paciente',$val->id_paciente)->whereBetween('fechaini', [$fecha_crea . ' 00:00:00', $fecha_crea . ' 23:59:59'])->first();
                        //dd($agenda);

                        if(!is_null($agenda)){
                           $sala = Sala::where('id',$agenda->id_sala)->first();
                           if(!is_null($sala)){
                           // dd($sala);
                        //dd($sala);
                        $sheet->cell('A'.$i, function($cell) use($cantidad) {
                            // manipulate the cel
                            $cell->setValue($cantidad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $paciente = Paciente::find($val->id_paciente);
                        $nombre = '';
                        if(!is_null($paciente)){
                            $nombre = $paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2;
                        }
                        $sheet->cell('B'.$i, function($cell) use($val, $nombre) {
                            // manipulate the cel
                            $cell->setValue($nombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }); 
                        $arr = ['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                        $ltr = 0;$x=1;
                    
                    foreach ($preguntas as $p) {
                        $txt = '';
                        $complemento = Encuesta_Complemento::where('id_encuesta_1',$val->id)->where('id_pregunta',$p->id)->first();
                        if(!is_null($complemento)){//4-2=>bueno-regular-malo
                            $txt = $complemento->valor;
                            if($complemento->valor=='5'){
                                $txt = 'EXCELENTE';
                            }
                            if($complemento->valor=='4'){
                                $txt = 'MUY BUENO';
                            }
                            if($complemento->valor=='3.5'){
                                $txt = 'BUENO';
                            }
                            if($complemento->valor=='3'){
                                $txt = 'NI BUENO NI MALO';
                            }
                            if($complemento->valor=='2.5'){
                                $txt = 'MALO';
                            }
                            if($complemento->valor=='1'){
                                $txt = 'MUY MALO';
                            }
                            if($complemento->valor=='otros'){
                                $txt = 'Otros - '.$complemento->valor2;
                            }

                        }
                        $sheet->cell($arr[$ltr].$i, function($cell) use($txt, $ltr, $x) {
                            // manipulate the cel
                            $cell->setValue($txt);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $ltr++;$x++;
                    }
                    $sheet->cell('L'.$i, function($cell) use($val, $ltr, $x) {
                        // manipulate the cel
                        /*if($val->ip_ingreso == '192.168.80.7'){
                            $cell->setValue("SLIGNA");}
                        elseif ($val->ip_ingreso == '192.168.80.6') {
                           $cell->setValue("ESANCHEZ");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.8') {
                           $cell->setValue("MCIFUENTES");
                        }
                        elseif ($val->ip_ingreso == '192.168.80.9') {
                           $cell->setValue("OGALLARDO");
                        }
                        elseif ($val->ip_ingreso == '192.168.75.216') {
                           $cell->setValue("VZAMBRANO");
                        }
                        elseif ($val->ip_ingreso == '10.0.0.1') {
                           $cell->setValue("WEB");
                        }*/
                        $cell->setValue("");
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 
                    $sheet->cell('M'.$i, function($cell) use($val, $ltr, $x) {
                        $cell->setValue($val->created_at);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    }); 

                    $sheet->cell('N'.$i, function($cell) use($sala) {
                        $cell->setValue($sala->hospital->nombre_hospital);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $cantidad++;$i++;      
                } 
              }
            }      
        });
        })->export('xlsx');
        
        
       
        
    }

    public function new_estadisticas()
    {

        $encuestas = Master_encuesta::where('estado', '1')->where('tipo',2)->get();

        return view('nueva_encuesta/estadisticas',['encuestas' => $encuestas]);
    }

    public function estadisticas_tipo(Request $request, $id)
    {
        $anio = $request['anio'];
        $mes = $request['mes'];
        $fecha= $request['fecha'];
        $idempresa = $request->empresa;
        $id_seguro = $request->id_seguro;
        if ($request['anio'] == null) {
            $anio = date('Y');
        }
        if ($request['mes'] == null) {
            $mes = date('m');
        }
        //$fecha = $request['created_at'];
        // if (is_null($fecha)) {
        //     $fecha = date('Y-m-d');
        // }

        $encuestas = Encuesta_1::join('agenda as a', 'a.id', 'encuesta_1.id_agenda');

        if (!is_null($fecha)) {
            $encuestas = $encuestas->whereBetween('encuesta_1.created_at', [$fecha . ' 00:00', $fecha . ' 23:59']);
        }else{
            $encuestas = $encuestas->where('encuesta_1.anio', $anio)->where('encuesta_1.mes', $mes);
        }

        if (!is_null($idempresa)) {
            $encuestas = $encuestas->where('a.id_empresa', $idempresa);
            //dd($encuestas->get());
        }

        if (!is_null($id_seguro)) {
            $encuestas = $encuestas->where('a.id_seguro', $id_seguro);
        }

        $encuestas = $encuestas->select('encuesta_1.*')->get();
        //dd($encuestas);

        $master_encuestas = Master_encuesta::find($id);
        $empresa       = Empresa::where('estado', '1')->orderBy('nombrecomercial', 'asc')->where('admision', '1')->get();
        $seguros = Seguro::where('inactivo', '1')->get();
        $preguntas = Pregunta::where('id_grupopregunta', '1')->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*', 'pe.id_masterencuesta')->where('id_masterencuesta', $id)->get();
        //dd($preguntas->where('id','11')->first()->nombre);
        $preguntas_tiempo = Pregunta::where('id_grupopregunta', '2')->join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*')->where('id_masterencuesta', $id)->get();

        return view('nueva_encuesta/detalle_estadisticas', ['preguntas' => $preguntas, 'encuestas' => $encuestas, 'anio' => $anio, 'master_encuestas' => $master_encuestas, 'preguntas_tiempo' => $preguntas_tiempo, 'mes' => $mes, 'id' => $id, 'idempresa' => $idempresa, 'empresa' => $empresa, 'seguros' => $seguros, 'id_seguro' => $id_seguro, 'fecha'=>$fecha]);
    }

    public function reporte_new_encuesta(Request $request, $id)
    {

        $anio = $request['anio'];
        $mes = $request['mes'];
        $fecha = $request->fecha;
        $idempresa = $request->empresa;
        $id_seguro = $request->id_seguro;
        $titulos = array("No.", "PREGUNTA");
        $titulos2 = array("No.", "PACIENTE", "EMPRESA", "SEGURO", "PREGUNTA 1", "PREGUNTA 2", "PREGUNTA 3", "PREGUNTA 4", "PREGUNTA 5", "PREGUNTA 6", "PREGUNTA 7");
        $titulos3 = array("IP_CREACION", "FECHA CREACION", "UNIDAD", "DOCTOR");
        $titulos4 = array("SIMBOLOGIA","PUNTAJE");
        //Posiciones en el excel
        $posicion = array("C", "D", "E", "F", "G", "H", "I", "J", "K");

        //Posiciones en el excel
        $posicion2 = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");

        $posicion3 = array("M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W");
        $posicion4 = array("H", "I", "J", "K");

        if ($request['anio'] == null) {
            $anio = date('Y');
        }
        if ($request['mes'] == null) {
            $mes = date('m');
        }
        $master_encuestas = Master_encuesta::find($id);

        $encuestas = Encuesta_1::where('id_area', $id)->join('agenda as a', 'a.id', 'encuesta_1.id_agenda');

        if ($fecha != null) {
            $encuestas = $encuestas->whereBetween('encuesta_1.created_at', [$fecha . ' 00:00:00', $fecha . ' 23:59:59']);
        } else {
            $encuestas = $encuestas->where('encuesta_1.anio', $anio)->where('encuesta_1.mes', $mes);
        }

        if (!is_null($idempresa)) {
            $encuestas = $encuestas->where('a.id_empresa', $idempresa);
        }

        if (!is_null($id_seguro)) {
            $encuestas = $encuestas->where('a.id_seguro', $id_seguro);
        }

        $encuestas = $encuestas->select('encuesta_1.*')->get();

        $preguntas = Pregunta::join('preguntas_encuestas as pe', 'pe.id_pregunta', 'pregunta.id')->select('pregunta.*', 'pe.id_masterencuesta')->where('pe.id_masterencuesta', $id)->get();

        //dd($preguntas_procedimientos);

        Excel::create('Detalle de Encuestas', function ($excel) use ($encuestas, $preguntas, $anio, $mes, $master_encuestas, $titulos, $posicion, $titulos2, $posicion2, $titulos3, $posicion3, $titulos4, $posicion4) {
            $excel->sheet('Consulta Agendas', function ($sheet) use ($encuestas, $preguntas, $anio, $mes, $master_encuestas, $titulos, $posicion, $titulos2, $posicion2, $titulos3, $posicion3, $titulos4, $posicion4) {

                $sheet->mergeCells('A1:S1');
                $sheet->mergeCells('A2:P2');

                $array_mes = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICEMBRE'];

                $mes_buscar = '';
                if (substr($mes, 0, 1) === '0') {

                    $mes_buscar = substr($mes, 1, 2);
                } else {
                    $mes_buscar = $mes;
                }
                //$array_main = array_search(intval($mes_buscar), array_keys($array_mes));
                $mes_ori = $array_mes[$mes_buscar - 1];
                $sheet->cell('A1', function ($cell) use ($mes_ori, $anio, $master_encuestas) {
                    // manipulate the cel
                    $cell->setValue($master_encuestas->descripcion . ' ' . $mes_ori . ' - ' . $anio);
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('BASE DE PREGUNTAS');
                    $cell->setFontWeight('bold');
                });
              
                //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL
                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
              $comienzo=5;
                $cab1["data"] = ["No"];
                $cab1["background-color"] = "#92CFEF";
                $cab1["num_inicial"]    = $comienzo;
                $cab1["num_final"]      = $comienzo;
                $cab1["letra_inicial"]  = "C";
                $cab1["letra_final"]    = "C";
                $cab1["direccion"]      = "normal";
                $cab1["font-size"]      = 10;
                $excel_body = excelCreate::mergeCells($sheet, $cab1);

         
                $cab1["data"] = ["PREGUNTAS"];
                $cab1["letra_inicial"]  = $excel_body["letra_final"] ;
                $cab1["letra_final"]    = "F";
                $excel_body = excelCreate::mergeCells($sheet, $cab1);

                $comienzo++;

                $cab1["data"] = ["VALOR"];
                $cab1["background-color"] = "#92CFEF";
                $cab1["num_inicial"]    = $comienzo;
                $cab1["num_final"]      = $comienzo;
                $cab1["letra_inicial"]  = "H";
                $cab1["letra_final"]    = "H";
                $cab1["direccion"]      = "normal";
                $cab1["font-size"]      = 10;
                $excel_body = excelCreate::mergeCells($sheet, $cab1);

         
                $cab1["data"] = ["SIMBOLOGIA"];
                $cab1["letra_inicial"]  = $excel_body["letra_final"] ;
                $cab1["letra_final"]    = "I";
                $excel_body = excelCreate::mergeCells($sheet, $cab1);


                $cab1["data"] = ["10"];
                $cab1["background-color"] = "#FFFFFF";
                $cab1["num_inicial"]    = 7;
                $cab1["num_final"]      = 7;
                $cab1["letra_inicial"]  = "H";
                $cab1["letra_final"]    = "H";
                $cab1["direccion"]      = "normal";
                $cab1["font-size"]      = 10;
                $excel_body = excelCreate::mergeCells($sheet, $cab1);

         
                $cab1["data"] = ["MUY BUENO"];
                $cab1["background-color"] = "#FFFFFF";
                $cab1["num_inicial"]    = 7;
                $cab1["num_final"]      = 7;
                $cab1["letra_inicial"]  = "I";
                $cab1["letra_final"]    = "I";
                $excel_body = excelCreate::mergeCells($sheet, $cab1);
                
                $cab1["data"] = ["5"];
                $cab1["background-color"] = "#FFFFFF";
                $cab1["num_inicial"]    = 8;
                $cab1["num_final"]      = 8;
                $cab1["letra_inicial"]  = "H";
                $cab1["letra_final"]    = "H";
                $cab1["direccion"]      = "normal";
                $cab1["font-size"]      = 10;
                $excel_body = excelCreate::mergeCells($sheet, $cab1);

         
                $cab1["data"] = ["NI BUENO NI MALO"];
                $cab1["background-color"] = "#FFFFFF";
                $cab1["num_inicial"]    = 8;
                $cab1["num_final"]      = 8;
                $cab1["letra_inicial"]  = "I";
                $cab1["letra_final"]    = "I";
                $excel_body = excelCreate::mergeCells($sheet, $cab1);

                $cab1["data"] = ["0"];
                $cab1["background-color"] = "#FFFFFF";
                $cab1["num_inicial"]    = 9;
                $cab1["num_final"]      = 9;
                $cab1["letra_inicial"]  = "H";
                $cab1["letra_final"]    = "H";
                $cab1["direccion"]      = "normal";
                $cab1["font-size"]      = 10;
                $excel_body = excelCreate::mergeCells($sheet, $cab1);

         
                $cab1["data"] = ["MALO"];
                $cab1["background-color"] = "#FFFFFF";
                $cab1["num_inicial"]    = 9;
                $cab1["num_final"]      = 9;
                $cab1["letra_inicial"]  = "I";
                $cab1["letra_final"]    = "I";
                $excel_body = excelCreate::mergeCells($sheet, $cab1);



                $contador = 1;

                foreach ($preguntas as $value) {

                    $datos["data"] = [ $value->nombre];
                    $datos["background-color"] = "#FFFFFF";
                    $datos["num_inicial"]    = $comienzo;
                    $datos["num_final"]      = $comienzo+1;
                    $datos["letra_inicial"]  = "D";
                    $datos["letra_final"]    = "F";
                    $datos["font-size"]      = 10;
                    $excel_body = excelCreate::mergeCells($sheet, $datos);

                    
                    $datos["data"] = [ $contador];
                    $datos["letra_inicial"]  = "C";
                    $datos["letra_final"]    = "C";
                    $excel_body = excelCreate::mergeCells($sheet, $datos);
    
                    $contador++;
                    $comienzo=$comienzo+2;
                }
              

                $comienzo = $comienzo+5;


               
                //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL
                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos2); $i++) {
                    $sheet->cell('' . $posicion2[$i] . '' . $comienzo, function ($cell) use ($titulos2, $i) {
                        $cell->setValue($titulos2[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#92CFEF');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                $contador1 = 1;

                foreach ($encuestas as $value) {

                    
                    $arr_valor = array();
                    foreach ($preguntas as $p) {
                        $txt = '';
                        $complemento = Encuesta_Complemento::where('id_encuesta_1', $value->id)->where('id_pregunta', $p->id)->first();
                        if (!is_null($complemento)) { //4-2=>bueno-regular-malo
                           $txt = $complemento->valor;
                            if ($complemento->id_grupo == 1) {
                                if ($complemento->valor == '10') {
                                    $txt = 'MUY BUENO';
                                }
    
                                if ($complemento->valor == '5') {
                                    $txt = 'NI BUENO NI MALO';
                                }
    
                                if ($complemento->valor == '0') {
                                    $txt = 'MALO';
                                }
                            }

                            if ($complemento->id_grupo == 2) {

                                if ($complemento->valor == '10') {
                                    $txt = $complemento->valor;
                                }

                                if ($complemento->valor == 'otros') {
                                    $txt = 'Otros - ' . $complemento->valor2;
                                }
                            }
                            

                            
                        }

                        array_push($arr_valor, $txt);
                    
                    }

                    //dd($arr_valor);
                    $paciente = Paciente::find($value->id_paciente);
                    $nombre = '';
                    if (!is_null($paciente)) {
                        $nombre = $paciente->apellido1 . ' ' . $paciente->apellido2 . ' ' . $paciente->nombre1 . ' ' . $paciente->nombre2;
                    }
                    
                    $fecha_crea = substr($value->created_at, 0, 10);
                    // $agenda = Agenda::where('id_paciente', $value->id_paciente)->whereBetween('fechaini', [$fecha_crea . ' 00:00:00', $fecha_crea . ' 23:59:59'])->first();
                    $agenda = Agenda::find($value->id_agenda);
                   
                    if (!is_null($agenda)) {
                        $sala = Sala::where('id', $agenda->id_sala)->first();
                        $seguro = Seguro::where('id', $agenda->id_seguro)->first();
                        $empresa = Empresa::where('id', $agenda->id_empresa)->first();
                        
                        if (!is_null($sala)) {
                            //dd($sala);
                            $datos_excel = array();
                            $nombre_empresa = "";
                            $nombre_seguro = $seguro->nombre;
                            if (!is_null($empresa)) {
                                $nombre_empresa = $empresa->nombrecomercial;
                            }
                    
                            array_push($datos_excel, $contador1, $nombre, $nombre_empresa, $nombre_seguro, isset($arr_valor[0]) ? $arr_valor[0] : '', isset($arr_valor[1]) ? $arr_valor[1] : '', isset($arr_valor[2]) ? $arr_valor[2] : '', isset($arr_valor[3]) ? $arr_valor[3] : '', isset($arr_valor[4]) ? $arr_valor[4] : '', isset($arr_valor[5]) ? $arr_valor[5] : '', isset($arr_valor[6]) ? $arr_valor[6] : '');


                            for ($i = 0; $i < count($datos_excel); $i++) {
                                $sheet->cell('' . $posicion2[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                    $cell->setValue($datos_excel[$i]);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                            }
                            $contador1++;
                            $comienzo++;
                        }
                    }
                }
                $comienzo=25;
                
                //EN QUE FILA ESTARN LOS TITULOS DEL EXCEL
                /****************TITULOS DEL EXCEL*********************/
                //crear los titulos en el excel
                for ($i = 0; $i < count($titulos3); $i++) {
                    $sheet->cell('' . $posicion3[$i] . '' . $comienzo, function ($cell) use ($titulos3, $i) {
                        $cell->setValue($titulos3[$i]);
                        $cell->setFontWeight('bold');
                        $cell->setBackground('#92CFEF');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                $comienzo++;
                $contador1 = 1;

                foreach ($encuestas as $val) {

                    $fecha_crea = substr($val->created_at, 0, 10);
                    // $agenda = Agenda::where('id_paciente', $val->id_paciente)->whereBetween('fechaini', [$fecha_crea . ' 00:00:00', $fecha_crea . ' 23:59:59'])->first();
                    $agenda = Agenda::find($val->id_agenda);

                    if (!is_null($agenda)) {
                        $sala = Sala::where('id', $agenda->id_sala)->first();
                        $historia = Historiaclinica::where('id_agenda', $agenda->id)->leftjoin('users', 'users.id', 'historiaclinica.id_doctor1')->select('users.nombre1', 'users.nombre2', 'users.apellido1', 'users.apellido2')->first();

                        $nombre_doctor = "";
                        if (!is_null($historia)) {
                            $nombre_doctor = $historia->nombre1 . ' ' . $historia->nombre2 . ' ' . $historia->apellido1 . ' ' . $historia->apellido2;
                        }else{
                            $nombre_doctor = $agenda->doctor1->nombre1 . ' ' . $agenda->doctor1->nombre2 . ' ' . $agenda->doctor1->apellido1 . ' ' . $agenda->doctor1->apellido2;
                        }

                        if (!is_null($sala)) {
                            //dd($sala);
                            $datos_excel = array();
                            $nombre_sala = $sala->hospital->nombre_hospital;
                            array_push($datos_excel, '', $val->created_at, $nombre_sala, $nombre_doctor);


                            for ($i = 0; $i < count($datos_excel); $i++) {
                                $sheet->cell('' . $posicion3[$i] . '' . $comienzo, function ($cell) use ($datos_excel, $i) {
                                    $cell->setValue($datos_excel[$i]);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setAlignment('center');
                                });
                            }
                            $contador1++;
                            $comienzo++;
                        }
                    }
                }
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(45)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(30)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("H")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("I")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("J")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("K")->setWidth(25)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("L")->setWidth(30)->setAutosize(false);

            $excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
            $excel->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
            $excel->getActiveSheet()->getStyle('D5:D900')->getAlignment()->setWrapText(true);
        })->export('xlsx');
    }



    public function nuevo_estadistico_labs(){


        return view("nueva_encuesta_labs/index");;
    }

    public function nuevo_estadistico_buscar(Request $request){
       
        SinLoginController::excel_nuevo_formato($request->desde,$request->hasta);
    }
}
