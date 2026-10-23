<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\User_espe;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Historiaclinica;
use Sis_medico\Archivo_historico;
use Sis_medico\Agenda;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Procedimiento;
use Sis_medico\Pentax_log;

use Sis_medico\Sala;
use Sis_medico\Examen;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Parametro;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Empresa;
use Sis_medico\Nivel;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;

use Sis_medico\Examen_Parametro_Referencia_Anterior;
use Sis_medico\Examenes;

class ExamenController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 10, 11, 12, 22)) == false && $id_auth!='1307189140'){
          return true;
        }
        

    }

    

    public function index()
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }

        $niveles = Nivel::where('grupo','1')->get();
        /*$examenes = DB::table('examen as e')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->paginate(30);
        $examenes = DB::table('examen as e')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->get();
        //dd($examenes->count());*/
        $examenes = DB::table('examen as e')
            ->leftjoin('examen_agrupador_sabana as ea','ea.id_examen','e.id')
            ->join('examen_agrupador as eag','eag.id','e.id_agrupador')
            ->select('e.*','eag.nombre as anombre')
            ->where('e.estado','1')
            ->whereNull('ea.id')
            ->paginate(50);
            //->get();
        //dd($examenes->count());    
            //
        $examenes_part = DB::table('examen as e')
            ->join('examen_agrupador_sabana as ea','ea.id_examen','e.id')
            ->join('examen_agrupador_labs as el','el.id','ea.id_examen_agrupador_labs')
            ->select('e.*','el.nombre as elnombre')
            ->where('e.estado','1')
            ->paginate(50);

        $nivel_seg=[];
        foreach ($examenes_part as $examen) {
            $valor = [];
            foreach ($niveles as $nivel) {
                
                $ex_niv = Examen_Nivel::where('nivel',$nivel->id)->where('id_examen',$examen->id)->first();
                if(!is_null($ex_niv)){
                    //dd($ex_niv);
                    $valor[$nivel->id] = $ex_niv->valor1;
                }else{
                    $valor[$nivel->id] = 0;
                }
                   
            }
            $nivel_seg[$examen->id]=$valor;
        }
        $nivel_seg2=[];
        foreach ($examenes as $examen) {
            $valor = [];
            foreach ($niveles as $nivel) {
                
                $ex_niv = Examen_Nivel::where('nivel',$nivel->id)->where('id_examen',$examen->id)->first();
                if(!is_null($ex_niv)){
                    //dd($ex_niv);
                    $valor[$nivel->id] = $ex_niv->valor1;
                }else{
                    $valor[$nivel->id] = 0;
                }
                   
            }
            $nivel_seg2[$examen->id]=$valor;
        }
        //dd($nivel_seg);
        return view('laboratorio/examen/index', ['examenes' => $examenes, 'niveles' => $niveles, 'nivel_seg' => $nivel_seg, 'nivel_seg2' => $nivel_seg2, 'examenes_part' => $examenes_part, 'nombre' => null, 'estado' => null, 'rnivel' => '1' ]);
    }

    public function parametro($id_examen)
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }

        $examen_parametros = Examen_Parametro::where('id_examen',$id_examen)->orderBy('orden')->paginate(30);
        $examen = Examen::find($id_examen);

        return view('laboratorio/examen/parametro',['examen' => $examen,'examen_parametros' => $examen_parametros]);
    }

    public function create(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $agrupadores = Examen_Agrupador::where('estado','1')->get();
        return view('laboratorio/examen/create',['agrupadores' => $agrupadores]);

    }

    public function create_parametro($id_examen){
        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $examen = Examen::find($id_examen);
        return view('laboratorio/examen/create_parametro',['examen' => $examen]);

    }

     public function store(Request $request)
    {
    
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput($request);
        //dd($request->all());
        
        $input = [    
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => strtoupper($request['descripcion']),
            'valor' => $request['valor'],
            'id_agrupador' => $request['id_agrupador'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            'tarifario' => $request['tarifario'],
            ];


        $id_examen = Examen::insertGetId($input);           

        return redirect()->route('examen.index');
        //return redirect()->route('examen.parametro',['id_examen' => $id_examen]);
    }

    public function store_parametro(Request $request)
    {
    
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        //$this->validateInput($request);

        
        $input = [    
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => strtoupper($request['nombre']),
            'texto1' => strtoupper($request['nombre']),
            
            'valor1' => $request['valor1'],
            
            'valor1g' => $request['valor1g'],
            
            'unidad1' => $request['unidad1'],
            'sexo' => $request['sexo'],
            'texto_referencia' => $request['texto_referencia'],
            'id_examen' => $request['id_examen'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            ];


        Examen_parametro::create($input);           

       
        return redirect()->route('examen.parametro',['id_examen' => $request['id_examen']]);
    }

    public function update_parametro(Request $request)
    {
    
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        //$this->validateInput($request);

        $id = $request['id'];
        //return $request->all();
        $input = [    
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => strtoupper($request['nombre']),
            'texto1' => strtoupper($request['nombre']),
           
            'valor1' => $request['valor1'],
            'orden' => $request['orden'],
            
            'valor1g' => $request['valor1g'],
            
            'unidad1' => $request['unidad'],
            'texto_referencia' => $request['texto_referencia'],
            'id_examen' => $request['id_examen'],
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
            'sexo' => $request['sexo'],
            'edad_ini' => $request['edad_ini'],
            'edad_fin' => $request['edad_fin'],
            ];         
        $examen = Examen_parametro::find($id);    


        $examen->update($input);
       
        return redirect()->route('examen.parametro',['id_examen' => $request['id_examen']]);
    }

    private function validateInput($request) {

        $rules = [
           
            'nombre' =>  'required|unique:examen,nombre|max:200',
            'descripcion' =>  'required|max:200',
            'valor' => 'required|numeric',
            //'tarifario' => 'required|unique:examen,tarifario|max:10',
            'tarifario' => 'required|max:10',

        ];
         
        $messages= [
       
        'nombre.required' => 'Ingresa el Nombre.', 
        'nombre.unique' => 'El nombre ingresado ya existe.',     
        'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        'descripcion.required' => 'Agrega una descripcion.',
        'descripcion.max' =>'La descripción no puede ser mayor a :max caracteres.',
        'valor.required' => 'Ingresa el Valor.',
        'valor.numeric' => 'Valor debe ser numérico.',
        'tarifario.required' => 'Ingresa el tarifario.', 
        'tarifario.unique' => 'El tarifario ingresado ya existe.',     
        'tarifario.max' =>'El tarifario no puede ser mayor a :max caracteres.', 
        ];    

        $this->validate($request, $rules, $messages);
    }

    private function validateInput2($request,$id) {

        $rules = [
           
            'nombre' =>  'required|unique:examen,nombre,'.$id.'|max:200',
            'descripcion' =>  'required|max:200',
            'valor' => 'required|numeric',
            //'tarifario' => 'required|unique:examen,tarifario,'.$id.'|max:10',
            
            'tarifario' => 'required|max:10',

        ];

         
        $messages= [
       
        'nombre.required' => 'Ingresa el Nombre.', 
        'nombre.unique' => 'El nombre ingresado ya existe.',     
        'nombre.max' =>'El nombre no puede ser mayor a :max caracteres.', 
        'descripcion.required' => 'Agrega una descripcion.',
        'descripcion.max' =>'La descripción no puede ser mayor a :max caracteres.',
        'valor.required' => 'Ingresa el Valor.',
        'valor.numeric' => 'Valor debe ser numérico.',
        'tarifario.required' => 'Ingresa el tarifario.', 
        'tarifario.unique' => 'El tarifario ingresado ya existe.',     
        'tarifario.max' =>'El tarifario no puede ser mayor a :max caracteres.', 
        ];    

        $this->validate($request, $rules, $messages);
    }

    

    public function show($id)
    {
        //
    }

    public function edit_parametro($id){
        if($this->rol()){
            return response()->view('errors.404');
        }

        $parametro = Examen_Parametro::find($id);
        return view('laboratorio.examen.edit_parametro', ['parametro' => $parametro]);
    }

    public function edit($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }

        $nivel_seg=[];
        $examen = Examen::find($id);
        $niveles = Nivel::all();
        foreach ($niveles as $nivel) {
            
            $ex_niv = Examen_Nivel::where('nivel',$nivel->id)->where('id_examen',$id)->first();
            if(!is_null($ex_niv)){
                $valor = $ex_niv->valor1;    
            }else{
                $valor = 0;
            }
            $nivel_seg[$nivel->id]=$valor;
        }

        //dd($nivel_seg);

        //dd($nivel_seg,$nivel_seg[1][1]);
        
        if(!is_null($examen)){

        $agrupadores = Examen_Agrupador::where('estado','1')->get();
            return view('laboratorio/examen/edit', ['examen' => $examen, 'agrupadores' => $agrupadores, 'niveles' => $niveles, 'nivel_seg' => $nivel_seg]);

        }

    }

    public function update(Request $request, $id)
    {
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput2($request,$id);

        
        $input = [    
            'nombre' => strtoupper($request['nombre']),
            'descripcion' => strtoupper($request['descripcion']),
            'id_agrupador' => $request['id_agrupador'],
            'valor' => $request['valor'],
            'tarifario' => $request['tarifario'],
            
            'id_usuariomod' => $idusuario,
            
            'ip_modificacion' => $ip_cliente, 
            'estado' => $request['estado'],
            'tiempos'=> strtoupper($request['tiempo']),
            ];

        $examen = Examen::find($id);    


        $examen->update($input);

        $niveles = Nivel::all();
        foreach ($niveles as $nivel) {
            
            
            $nivel_ex = Examen_Nivel::where('nivel',$nivel->id)->where('id_examen',$id)->first();
            if(!is_null($nivel_ex)){
                $nivel_in = [
                    'valor1' => $request['valor'.$nivel->id],
                    'id_usuariomod' => $idusuario,
                    'ip_modificacion' => $ip_cliente, 
                ];
                $nivel_ex->update($nivel_in);
            }
            else{
                $nivel_in2 = [
                    'nivel' => $nivel->id,
                    'id_examen' => $id, 
                    'valor1' => $request['valor'.$nivel->id],
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente, 
                ];
                Examen_Nivel::create($nivel_in2);
            }

        }          

        return redirect()->route('examen.index');
        //return redirect()->route('examen.parametro',['id_examen' => $examen->id]);
    }    

    

    public function buscapaciente($id){
        $paciente = Paciente::find($id);
        if(!is_null($paciente))
        {
            return $paciente;    
        }
        else
        {
            return 'no';
        }     
    }

    public function search(Request $request){

        if($this->rol()){
            return response()->view('errors.404');
        }    

        $niveles = Nivel::where('grupo',$request->rnivel)->get();
        $nombre = $request['nombre'];
        /*$examenes = DB::table('examen as e')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->where('e.nombre','like','%'.$nombre.'%')->paginate(30);*/
        $examenes = DB::table('examen as e')
            ->leftjoin('examen_agrupador_sabana as ea','ea.id_examen','e.id')
            ->join('examen_agrupador as eag','eag.id','e.id_agrupador')
            ->select('e.*','eag.nombre as anombre')
            ->where('e.estado',$request->estado)
            ->where('e.nombre','like','%'.$nombre.'%')
            ->whereNull('ea.id')
            ->paginate(50);
        $examenes_part = DB::table('examen as e')
            ->join('examen_agrupador_sabana as ea','ea.id_examen','e.id')
            ->join('examen_agrupador_labs as el','el.id','ea.id_examen_agrupador_labs')
            ->select('e.*','el.nombre as elnombre')
            ->where('e.nombre','like','%'.$nombre.'%')
            ->where('e.estado',$request->estado)
            ->paginate(50);


        //dd($hospitalizados);
        $nivel_seg=[];
        foreach ($examenes_part as $examen) {
            $valor = [];
            foreach ($niveles as $nivel) {
                
                $ex_niv = Examen_Nivel::where('nivel',$nivel->id)->where('id_examen',$examen->id)->first();
                if(!is_null($ex_niv)){
                    //dd($ex_niv);
                    $valor[$nivel->id] = $ex_niv->valor1;
                }else{
                    $valor[$nivel->id] = 0;
                }
                   
            }
            $nivel_seg[$examen->id]=$valor;
        }
        //dd($nivel_seg);
        $nivel_seg2=[];
        foreach ($examenes as $examen) {
            $valor2 = [];
            foreach ($niveles as $nivel) {
                
                $ex_niv = Examen_Nivel::where('nivel',$nivel->id)->where('id_examen',$examen->id)->first();
                if(!is_null($ex_niv)){
                    //dd($ex_niv);
                    $valor2[$nivel->id] = $ex_niv->valor1;
                }else{
                    $valor2[$nivel->id] = 0;
                }
                   
            }
            $nivel_seg2[$examen->id]=$valor2;
        }
        return view('laboratorio/examen/index', ['examenes' => $examenes, 'niveles' => $niveles, 'nivel_seg' => $nivel_seg, 'nivel_seg2' => $nivel_seg2, 'examenes_part' => $examenes_part, 'nombre' => $request->nombre, 'estado' => $request->estado, 'rnivel' => $request->rnivel]); 
    }

    public function buscar2(Request $request){

        if($this->rol()){
            return response()->view('errors.404');
        }   
        $nombre_encargado = $request['paciente'];
        $nuevo_nombre = explode(' ', $nombre_encargado);
        $seteo = "%";
        foreach ($nuevo_nombre as $value) {
            $seteo =  $seteo.$value.'%';
        }
        $hospitalizados = DB::table('agenda as a')->where('a.proc_consul','3')->where('a.estado','2')->join('paciente as p','p.id','a.id_paciente')->join('seguros as s','s.id','a.id_seguro')->leftjoin('users as d','d.id','a.id_doctor1')->select('a.*','p.nombre1','p.nombre2','p.apellido1','p.apellido2','s.nombre as snombre','d.nombre1 as dnombre1','d.apellido1 as dapellido1')->selectRaw("CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) as completo")->whereRaw("CONCAT_WS(' ', p.nombre1, p.nombre2, p.apellido1, p.apellido2) like '".$seteo."'")->orderBy('a.fechafin','desc')->paginate(30);
        
        return view('hospital_iess/hospitalizados/altas', ['hospitalizados' => $hospitalizados, 'paciente' => $request['paciente']]);
        
    }

    public function log($id){

        if($this->rol()){
            return response()->view('errors.404');
        }

        $hosp = Agenda::find($id);
        $paciente = Paciente::find($hosp->id_paciente);
        $logs = DB::table('log_agenda as l')->where('l.id_agenda',$id)->get();

        
        return view('hospital_iess/hospitalizados/log', ['logs' => $logs, 'paciente' => $paciente]);
        
    }

    public function reporte() {

        $niveles = Nivel::all();
        $examenes = DB::table('examen as e')->where('e.estado','1')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->get();

        $nivel_seg=[];
        foreach ($examenes as $examen) {
            $valor = [];
            foreach ($niveles as $nivel) {
                
                $ex_niv = Examen_Nivel::where('nivel',$nivel->id)->where('id_examen',$examen->id)->first();
                if(!is_null($ex_niv)){
                    //dd($ex_niv);
                    $valor[$nivel->id] = $ex_niv->valor1;
                }else{
                    $valor[$nivel->id] = 0;
                }
                   
            }
            $nivel_seg[$examen->id]=$valor;
        }
        $i=0;


        $convenios = DB::table('convenio as c')->where('c.estado','1')->join('seguros as s','s.id','c.id_seguro')->join('empresa as e','e.id','c.id_empresa')->join('nivel as n','n.id','c.id_nivel')->select('c.*','s.nombre as snombre','e.nombrecomercial','n.nombre as nnombre')->get();
        
        $fecha_d = date('Y/m/d'); 
        Excel::create('Examenes-'.$fecha_d, function($excel) use($examenes, $nivel_seg, $convenios, $niveles) {

            $excel->sheet('Examenes', function($sheet) use($examenes, $nivel_seg, $niveles) {
                $fecha_d = date('Y/m/d');
                $i = 5;
                $sheet->mergeCells('A2:V2');
                
                $mes = substr($fecha_d, 5, 2); 
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
                $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' DEL '.substr($fecha_d, 0, 4);
                $sheet->cell('A2', function($cell) use($fecha2){
                    // manipulate the cel
                    $cell->setValue('VALORES DE EXAMENES'.' - '.$fecha2);
                    
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cells('A1:V3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:V4', function($cells) {
                // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ID');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                /*$sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CODIGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('AGRUPADOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });*/
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PARTICULAR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $l = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];
                $lastColumn = 'ZZ';$l = [];
                for ($column = 'D'; $column != $lastColumn; $column++) {
                    array_push($l, $column);    
                }
                //dd($l);
                $x = 0;
                foreach($niveles as $nivel){
                    $sheet->cell($l[$x].'4', function($cell) use($nivel) {
                        // manipulate the cel
                        $cell->setValue($nivel->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $x ++;  
                } 

                $sheet->cell($l[$x+1].'4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('INTERLAB');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });     

                $sheet->cell($l[$x+2].'4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERIDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell($l[$x+3].'4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('HLABS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell($l[$x+4].'4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TIEMPOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                    

                foreach($examenes as $value){
                    
                    
                    $sheet->cell('A'.$i, function($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('B'.$i, function($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });
                    
                    /*$sheet->cell('C'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->tarifario);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('D'.$i, function($cell) use($value) {
                        // manipulate the cel
                        
                        $cell->setValue($value->eanombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                    });*/
                    $sheet->cell('C'.$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($value->valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });    

                    $x = 0;    
                    foreach($niveles as $nivel){

                        $sheet->cell($l[$x].$i, function($cell) use($value, $nivel_seg, $nivel) {
                            // manipulate the cel
                            $cell->setValue($nivel_seg[$value->id][$nivel->id]);    
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            
                        });
                        $x ++;

                    }   

                    $sheet->cell($l[$x+1].$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($value->valor_interlab);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell($l[$x+2].$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($value->valor_referido);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell($l[$x+3].$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($value->tarifario);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    }); 

                    $sheet->cell($l[$x+3].$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($value->humanlabs);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });  

                    $sheet->cell($l[$x+4].$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($value->tiempos);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    }); 
                    
                    $i= $i+1;
                }
                    
            });
            $excel->sheet('Convenios', function($sheet) use($convenios) {
                $fecha_d = date('Y/m/d');
                $i = 5;
                $sheet->mergeCells('A2:C2');
                
                $mes = substr($fecha_d, 5, 2); 
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
                $fecha2 = 'FECHA: '.substr($fecha_d, 8, 2).' de '.$mes_letra.' DEL '.substr($fecha_d, 0, 4);
                $sheet->cell('A2', function($cell) use($fecha2){
                    // manipulate the cel
                    $cell->setValue('CONVENIOS'.' - '.$fecha2);
                    
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cells('A1:C3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:C4', function($cells) {
                // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('EMPRESA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NIVEL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                

                foreach($convenios as $value){
                    
                    
                    $sheet->cell('A'.$i, function($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });
                    
                    $sheet->cell('B'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombrecomercial);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('C'.$i, function($cell) use($value) {
                        // manipulate the cel
                        
                        $cell->setValue($value->nnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                    });
                    

                    $i= $i+1;



                }
                    
                             
                    
                    
            });
        })->export('xlsx');

    }

    public function examenes_buscar_aj(Request $request)
    {
        $examenes_labs  = DB::table('examen_agrupador_sabana as sa')->join('examen as e', 'e.id', 'sa.id_examen')->where('e.estado', '1')->select('sa.*', 'e.descripcion', 'e.nombre', 'e.valor', 'e.id as ex_id')->where('e.descripcion', 'like', '%' . $request->term . '%')->where('sa.estado','1')->get();
        $arr=null;

        foreach ($examenes_labs as $value) {
            $arr[] = array('value' => $value->descripcion , 'id' => $value->ex_id );
        }

        return $arr;
    }

    public function crear_referencia_anterior( $id, Request $request ){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $parametro = Examen_Parametro::find($id);
        $array=[
            'id_parametro' => $id, 
            'valor1' => $parametro->valor1,
            'valor1g' => $parametro->valor1g, 
            'unidad1' => $parametro->unidad1,
            'texto_referencia' => $parametro->texto_referencia,
            'fecha_valida' =>$request->fecha_valida,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente, 
        ]; 
        Examen_Parametro_Referencia_Anterior::create($array);
    }
    public function listar_referencia_anterior( $id ){
        $parametros_anteriores = Examen_Parametro_Referencia_Anterior::where('id_parametro', $id)->where('estado',1)->get();
        return view('laboratorio/examen/listar_referencia_anterior', ['parametros_anteriores' => $parametros_anteriores]);
 
    }
    public function eliminar_referencia_anterio( $id ){
        $parametro = Examen_Parametro::find($id);
    }
    public function listado_examenes( Request $request )
    { 
        if($this->rol()){
            return response()->view('errors.404');
        }
        $examenes = Examen::where('estado','1');
        if($request->nombres != null){
            $examenes = $examenes->where(function ($query) use ($request) {
                $query->orwhere('nombre','like','%' . $request->nombres . '%')
                    ->orWhere('nombre_largo','like','%' . $request->nombres . '%')
                    ->orWhere('descripcion','like','%' . $request->nombres . '%');
            });
        }
        $examenes = $examenes->paginate(50);
        return view('laboratorio/examen/index2', ['examenes' => $examenes, 'nombres' => $request->nombres ]);
    }
    public function examen_tiempo_update(Request $request){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $examen_tiempo = examen::where('id', $request['id_examen'])->where('estado', 1)->first();
       
        if(!is_null($examen_tiempo)){
            $consulta_update =[

                'tiempos' => $request['tiempo'] 
            ];
            examen::where('id',$request['id_examen'])
            ->update($consulta_update);
        }
        
        Log_usuario::create([
            'id_usuario'       => $idusuario,
            'ip_usuario'       => $ip_cliente,
            'descripcion'      => "ACTUALIZA TIEMPO DE EXAMEN DE LABORATORIO",
            'dato_ant1'        => "EXAMEN ID: " . $request['id_examen'],
            'dato1'            => "EXAMEN: " . $examen_tiempo->nombre,
            'dato_ant2'        => $request['tiempo'] ,
        ]);
        return 'ok';
    }
}
