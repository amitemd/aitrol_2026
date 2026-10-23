<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Sis_medico\tipousuario;
use Sis_medico\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\Seguro;
use Sis_medico\Empresa;
use Sis_medico\Subseguro;
use Sis_medico\Documento;
use Sis_medico\Archivo_historico;
use Sis_medico\Historiaclinica;
use Sis_medico\Agenda;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\hc_receta;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Medicina;
use Sis_medico\Medicina_Principio;
use Sis_medico\Principio_Activo;
use Sis_medico\Log_usuario;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Evolucion_Indicacion;

use Excel;


use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;






use Response;

class MedicinaController extends Controller
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


    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 3, 6,11)) == false){
          return true;
        }
    }

    public function index($agenda){
        
        $medicinas = Medicina::where('estado',1)->orderBy('nombre')->paginate(20);  
        $genericos = Principio_Activo::where('estado',1)->get(); 


        return view('hc_admision.medicina.index', ['medicinas' => $medicinas, 'genericos' => $genericos, 'nombre' => null, 'agenda' => $agenda]);  
    }

    public function edit($agenda, $id){
        
        

        $medicina = Medicina::find($id);
        $genericos = Principio_Activo::where('estado',1)->get();
        //dd($genericos);
        $medicina_principio = Medicina_Principio::where('id_medicina',$id)->get();

        //dd($medicina);           

        return view('hc_admision.medicina.edit', ['medicina' => $medicina, 'genericos' => $genericos,'medicina_principio' => $medicina_principio, 'agenda' => $agenda]);  
    }

    public function create($agenda){
        
        $genericos = Principio_Activo::where('estado',1)->get();
        //dd($genericos);
        

        //dd($medicina);           

        return view('hc_admision.medicina.create', ['genericos' => $genericos, 'agenda' => $agenda]);  
    }

    public function create2($agenda,$ruta){
        
        $genericos = Principio_Activo::where('estado',1)->get();
        //dd($genericos);
        

        //dd($medicina);           

        return view('hc_admision.medicina.create2', ['genericos' => $genericos, 'agenda' => $agenda,'ruta' => $ruta]);  
    }

    public function update(Request $request, $id){
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $medicina = Medicina::findOrFail($id);


        if($request['dieta'] != 1){
            $rules = [
                
                'nombre' => 'required',
                'cantidad' => 'required',
                'dosis' => 'required',
                //'concentracion' => 'required', 
                //'presentacion' => 'required',
                'genericos' => 'required',
                'estado' => 'required',
                //'publico_privado' => 'required',

            ];
        }else{
            $rules = [
                
                'nombre' => 'required',
                'cantidad' => 'required',
                'dosis' => 'required',
                //'concentracion' => 'required', 
                //'presentacion' => 'required',
                //'genericos' => 'required',
                'estado' => 'required',
                //'publico_privado' => 'required',

            ];
        }
        $mensajes = [
              
            'nombre.required' => 'Ingrese el nombre.',
            'cantidad.required' => 'Ingrese la cantidad.',
            'dosis.required' => 'Ingrese la dosis.',
            'concentracion.required' => 'Ingrese la concentración.',
            'presentacion.required' => 'Ingrese la presentación.',
            'genericos.required' => 'Seleccione los genéricos.',
            'estado.required' => 'Seleccione el estado.',
            'publico_privado.required' => 'Seleccione el tipo.',
            
            ];

        $this->validate($request, $rules, $mensajes);

        $medicina_principio = Medicina_Principio::where('id_medicina',$id)->get();
        

        

        

        $input = [

            'nombre' => $request['nombre'],
            'cantidad' => $request['cantidad'],
            'dosis' => $request['dosis'],

            'dieta' => $request['dieta'],
            'concentracion' => $request['concentracion'], 
            'presentacion' => $request['presentacion'],
            'estado' => $request['estado'],
            //'publico_privado' => $request['publico_privado'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,    

        ];

        $medicina->update($input);
        
        $genericos_m = $request['genericos'];
        if($request['dieta'] != 1){
            foreach ($medicina_principio as $md) {
                $md->delete();    
            }

            $genericos_m = $request['genericos'];
            foreach ($genericos_m as $md) {
                if(is_numeric($md)){
                    $inputg = [

                        'id_medicina' => $id,
                        'id_principio_activo' => $md,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,

                    ];
                    Medicina_Principio::create($inputg);
                    
                }else{
                    $input_principio = [
                        'nombre' => substr(strtoupper($md), 0,-5),
                        'descripcion' => substr(strtoupper($md), 0,-5),
                        'estado' => '1',
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario, 
                    ];
                    $id_generico = Principio_Activo::insertGetId($input_principio);
                    $inputg = [

                        'id_medicina' => $id,
                        'id_principio_activo' => $id_generico,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,

                    ];

                    Medicina_Principio::create($inputg);

                }
            }
        }

        //return redirect()->route('medicina.index'); 
        $medicinas = Medicina::where('estado',1)->where('nombre','like','%'.$request['nombre'].'%')->orderBy('nombre')->paginate(20); 

        $genericos = Principio_Activo::where('estado',1)->get(); 

        return view('hc_admision.medicina.index', ['medicinas' => $medicinas, 'genericos' => $genericos, 'nombre' => $request['nombre'], 'agenda' => $request['agenda'] ]);   
    
    }

    public function store(Request $request){
        //dd($request->all());
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        if($request['dieta'] == 0){
           $rules = [
            
            'nombre' => 'required',
            'cantidad' => 'required',
            'dosis' => 'required',
            //'concentracion' => 'required', 
            //'presentacion' => 'required',
            'genericos' => 'required',
            'estado' => 'required',
            //'publico_privado' => 'required',

            ]; 
        }else{
            $rules = [
            'nombre' => 'required',
            'cantidad' => 'required',
            'dosis' => 'required',
            //'concentracion' => 'required', 
            //'presentacion' => 'required',
            //'genericos' => 'required',
            'estado' => 'required',
            //'publico_privado' => 'required',

            ];
        }

        

        $mensajes = [
              
            'nombre.required' => 'Ingrese el nombre.',
            'cantidad.required' => 'Ingrese la cantidad.',
            'dosis.required' => 'Ingrese la dosis.',
            'concentracion.required' => 'Ingrese la concentración.',
            'presentacion.required' => 'Ingrese la presentación.',
            'genericos.required' => 'Seleccione los genéricos.',
            'estado.required' => 'Seleccione el estado.',
            'publico_privado.required' => 'Seleccione el tipo.',
            
            ];

        $this->validate($request, $rules, $mensajes);


        $input = [

            'nombre' => $request['nombre'],
            'cantidad' => $request['cantidad'],
            'dosis' => $request['dosis'],
            'concentracion' => $request['concentracion'], 
            'presentacion' => $request['presentacion'],
            'estado' => $request['estado'],
            'dieta' => $request['dieta'],
            //'publico_privado' => $request['publico_privado'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,    

        ];

        $id = Medicina::insertGetId($input);
        if($request['dieta'] != 1){
            $genericos_m = $request['genericos'];

            foreach ($genericos_m as $md) {
                
                $inputg = [

                    'id_medicina' => $id,
                    'id_principio_activo' => $md,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,

                ];

                Medicina_Principio::create($inputg);

            }
        }


        return redirect()->route('medicina.index',['agenda' => $request['agenda'] ]);
         
    
    }

    public function store2(Request $request){
        //dd($request->all());
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');



        if($request['dieta'] == 0){
           $rules = [
            
            'nombre' => 'required',
            'cantidad' => 'required',
            'dosis' => 'required',
            
            'genericos' => 'required',
            
            

            ]; 
        }else{
            $rules = [
            'nombre' => 'required',
            'cantidad' => 'required',
            'dosis' => 'required',
            
           
            

            ];
        }

        

        $mensajes = [
              
            'nombre.required' => 'Ingrese el nombre.',
            'cantidad.required' => 'Ingrese la cantidad.',
            'dosis.required' => 'Ingrese la dosis.',
            'concentracion.required' => 'Ingrese la concentración.',
            'presentacion.required' => 'Ingrese la presentación.',
            'genericos.required' => 'Seleccione los genéricos.',
            'estado.required' => 'Seleccione el estado.',
            'publico_privado.required' => 'Seleccione el tipo.',
            
            ];

        $this->validate($request, $rules, $mensajes);


        $input = [

            'nombre' => $request['nombre'],
            'cantidad' => $request['cantidad'],
            'dosis' => $request['dosis'],
            'concentracion' => $request['concentracion'], 
            'presentacion' => $request['presentacion'],
            'estado' => '1',
            'dieta' => $request['dieta'],
           
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,    

        ];
        

        $id = Medicina::insertGetId($input);
        if($request['dieta'] != 1){
            $genericos_m = $request['genericos'];
            foreach ($genericos_m as $md) {
                if(is_numeric($md)){
                    $inputg = [

                        'id_medicina' => $id,
                        'id_principio_activo' => $md,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,

                    ];

                    Medicina_Principio::create($inputg);
                }else{
                    $input_principio = [
                        'nombre' => substr(strtoupper($md), 0,-5),
                        'descripcion' => substr(strtoupper($md), 0,-5),
                        'estado' => '1',
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario, 
                    ];
                    $id_generico = Principio_Activo::insertGetId($input_principio);
                    $inputg = [

                        'id_medicina' => $id,
                        'id_principio_activo' => $id_generico,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,

                    ];

                    Medicina_Principio::create($inputg);

                }
            }
        }
        //dd(redirect()->back());
        if($request['ruta']=='1'){
            $url = route("agenda.detalle", ['id' => $request['agenda']]).'#medicina_div';    
        }else{
            $url = route("agenda.detalle", ['id' => $request['agenda']]).'#medicina_div';
        }
        
        //dd($url);
        return redirect($url);
        
    
    }

    public function search(Request $request, $agenda){
        $nombre = $request['nombre'];

        $medicinas = Medicina::where('estado',1)->where('nombre','like','%'.$nombre.'%')->orderBy('nombre')->paginate(20); 

        $genericos = Principio_Activo::where('estado',1)->get(); 

        return view('hc_admision.medicina.index', ['medicinas' => $medicinas, 'genericos' => $genericos, 'nombre' => $request['nombre'], 'agenda' => $agenda]);  
        
    }

    public function show($id){

    }

    public function reporte() {

        $medicinas = Medicina::where('estado',1)->orderBy('nombre')->where('dieta','0')->get();

        $genericos = Principio_Activo::where('estado',1)->get(); 

        $fecha_d = date('Y/m/d'); 

        Excel::create('Agenda-'.$fecha_d, function($excel) use($medicinas, $genericos) {

            $excel->sheet('Consulta Agenda', function($sheet) use($medicinas, $genericos) {
                $fecha_d = date('Y/m/d');
                $i = 5;
                $sheet->mergeCells('A1:S1');
                
                $sheet->mergeCells('A2:N2'); 
                $sheet->mergeCells('O2:T2');
                $sheet->mergeCells('U2:X2'); 
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

                $sheet->cell('A1', function($cell) use($fecha2){
                    // manipulate the cel
                    $cell->setValue('MEDICINAS AL:'.$fecha2);
                    
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

               
                
                $sheet->cells('A1:E3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:E4', function($cells) {
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
                    $cell->setValue('NOMBRE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DOSIS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CANTIDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('E4', function($cell) {
                    // manipulate the cel

                    $cell->setValue('GENERICO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                

                
                foreach($medicinas as $value){
                    
                    $sheet->cell('A'.$i, function($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });
                    
                    $sheet->cell('B'.$i, function($cell) use($value) {
                        // manipulate the cel
                        $cell->setValue($value->nombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('C'.$i, function($cell) use($value) {
                        // manipulate the cel
                       
                        $cell->setValue($value->dosis);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                    });

                    $sheet->cell('D'.$i, function($cell) use($value) {
                            
                        $cell->setValue($value->cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('E'.$i, function($cell) use($value, $genericos) {
                        // manipulate the cel
                        $medicina_principio = DB::table('medicina_principio')->where('id_medicina',$value->id)->get();
                        $texto_gen = '';

                        foreach($medicina_principio as $md){
                            $texto_gen = $texto_gen.$genericos->where('id',$md->id_principio_activo)->first()->nombre.'+';
                        }
                        $cell->setValue($texto_gen);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    

                    $i= $i+1;



                }
                    
                             
                    
                    
            });
        })->export('xlsx');
    }

    

}
 