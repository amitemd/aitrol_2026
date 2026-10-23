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




class ExamenCostoController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 12, 22)) == false ){
          return true;
        }
        

    }

    

    public function index()
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }

        $niveles = Nivel::all();
        $examenes = DB::table('examen as e')->where('e.estado','1')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->paginate(30);
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
        //dd($nivel_seg);
        return view('laboratorio/examen_costo/index', ['examenes' => $examenes, 'niveles' => $niveles, 'nivel_seg' => $nivel_seg]);
    }

    public function parametro($id_examen)
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }

        $examen_parametros = Examen_Parametro::where('id_examen',$id_examen)->paginate(30);
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
           
            
            'valor_reactivo' => 'required|numeric',
            'valor_implementos' => 'required|numeric',
            

        ];

         
        $messages= [
       
        
        'valor_reactivo.required' => 'Ingresa el Valor.',
        'valor_reactivo.numeric' => 'Valor debe ser numérico.',
        'valor_implementos.required' => 'Ingresa el Valor.',
        'valor_implementos.numeric' => 'Valor debe ser numérico.',
        
        ];    

        $this->validate($request, $rules, $messages);
    }

    

    public function show($id)
    {
        //
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
            return view('laboratorio/examen_costo/edit', ['examen' => $examen, 'agrupadores' => $agrupadores, 'niveles' => $niveles, 'nivel_seg' => $nivel_seg]);

        }


    }

    public function update(Request $request, $id)
    {
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        $this->validateInput2($request,$id);

        
        $input = [    
            
            'valor_reactivo' => $request['valor_reactivo'],
            'valor_implementos' => $request['valor_implementos'],
            'id_usuariomod' => $idusuario,
            'ip_modificacion' => $ip_cliente, 
            
            ];

        $examen = Examen::find($id);    


        $examen->update($input);     

        return redirect()->route('examen_costo.index');
        //return redirect()->route('examen.parametro',['id_examen' => $examen->id]);
    }    


    public function search(Request $request){

        if($this->rol()){
            return response()->view('errors.404');
        }    

        $niveles = Nivel::all();
        $nombre = $request['nombre'];
        $examenes = DB::table('examen as e')->where('e.estado','1')->join('examen_agrupador as ea','ea.id','e.id_agrupador')->select('e.*','ea.nombre as eanombre')->where('e.nombre','like','%'.$nombre.'%')->paginate(30);
        //dd($hospitalizados);
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
        //dd($nivel_seg);
        return view('laboratorio/examen_costo/index', ['examenes' => $examenes, 'niveles' => $niveles, 'nivel_seg' => $nivel_seg]);
        
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

        //dd($convenios);
        
        $fecha_d = date('Y/m/d'); 
        Excel::create('Examenes-'.$fecha_d, function($excel) use($examenes, $nivel_seg, $convenios) {

            $excel->sheet('Examenes', function($sheet) use($examenes, $nivel_seg) {
                $fecha_d = date('Y/m/d');
                $i = 5;
                $sheet->mergeCells('A2:N2');
                
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
                
                $sheet->cells('A1:Z3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:Z4', function($cells) {
                // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('AGRUPADOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NIVEL1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function($cell) {
                    // manipulate the cel

                    $cell->setValue('NIVEL2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('NIVEL3');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cell('F4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('HUMANA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('G4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PARTICULAR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('REACTIVOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('IMPLEMENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SUB-TOTAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cell('K4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('I.NIVEL1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function($cell) {
                    // manipulate the cel

                    $cell->setValue('I.NIVEL2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('I.NIVEL3');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('I.HUMANA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('I.PARTICULAR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('%');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                

                foreach($examenes as $value){
                    
                    
                    $sheet->cell('A'.$i, function($cell) use($value){
                        // manipulate the cel
                        $cell->setValue($value->descripcion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });
                    

                    $sheet->cell('B'.$i, function($cell) use($value) {
                        // manipulate the cel
                        
                        $cell->setValue($value->eanombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                    });


                    $sheet->cell('C'.$i, function($cell) use($value, $nivel_seg) {
                            
                        $cell->setValue($nivel_seg[$value->id]['1']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('D'.$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($nivel_seg[$value->id]['2']);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });
                         

                     
                    $sheet->cell('E'.$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($nivel_seg[$value->id]['3']);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('F'.$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($nivel_seg[$value->id]['4']);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('G'.$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($value->valor);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('H'.$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($value->valor_reactivo);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $sheet->cell('I'.$i, function($cell) use($value, $nivel_seg) {
                        // manipulate the cel
                        $cell->setValue($value->valor_implementos);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });
                    $subtotal = $value->valor_reactivo + $value->valor_implementos;
                    $sheet->cell('J'.$i, function($cell) use($value, $nivel_seg,$subtotal) {
                        // manipulate the cel

                        $cell->setValue($subtotal);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $nivel1 = $nivel_seg[$value->id]['1'] - $subtotal;
                    $sheet->cell('K'.$i, function($cell) use($value, $nivel_seg, $nivel1) {
                        
                        $cell->setValue($nivel1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });
                    $pcr1 = 0;
                    if($subtotal>0){ $pcr1 =  round($nivel1 / $subtotal * 100,2); }
                    $sheet->cell('L'.$i, function($cell) use($value, $nivel_seg, $pcr1) {
                        
                        $cell->setValue($pcr1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $nivel2 = $nivel_seg[$value->id]['2'] - $subtotal;
                    $sheet->cell('M'.$i, function($cell) use($value, $nivel_seg, $nivel2) {
                        // manipulate the cel
                        $cell->setValue($nivel2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });
                    $pcr2 = 0;
                    if($subtotal>0){ $pcr2 =  round($nivel2 / $subtotal * 100,2); }
                    $sheet->cell('N'.$i, function($cell) use($value, $nivel_seg, $pcr2) {
                        // manipulate the cel
                        $cell->setValue($pcr2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });
                         

                    $nivel3 = $nivel_seg[$value->id]['3'] - $subtotal; 
                    $sheet->cell('O'.$i, function($cell) use($value, $nivel_seg, $nivel3) {
                        // manipulate the cel
                        $cell->setValue($nivel3);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $pcr3 = 0;
                    if($subtotal>0){ $pcr3 =  round($nivel3 / $subtotal * 100,2); }
                    $sheet->cell('P'.$i, function($cell) use($value, $nivel_seg, $pcr3) {
                        // manipulate the cel
                        $cell->setValue($pcr3);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $nivel4 = $nivel_seg[$value->id]['4'] - $subtotal; 
                    $sheet->cell('Q'.$i, function($cell) use($value, $nivel_seg, $nivel4) {
                        // manipulate the cel
                        $cell->setValue($nivel4);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $pcr4 = 0;
                    if($subtotal>0){ $pcr4 =  round($nivel4 / $subtotal * 100,2); }
                    $sheet->cell('R'.$i, function($cell) use($value, $nivel_seg, $pcr4) {
                        // manipulate the cel
                        $cell->setValue($pcr4);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $nivel5 = $value->valor - $subtotal; 
                    $sheet->cell('S'.$i, function($cell) use($value, $nivel_seg, $nivel5) {
                        // manipulate the cel
                        $cell->setValue($nivel5);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    $pcr5 = 0;
                    if($subtotal>0){ $pcr5 =  round($nivel5 / $subtotal * 100,2); }
                    $sheet->cell('T'.$i, function($cell) use($value, $nivel_seg, $pcr5) {
                        // manipulate the cel
                        $cell->setValue($pcr5);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        
                    });

                    

                    $i= $i+1;



                }
                    
                             
                    
                    
            });
            
        })->export('xlsx');

    }

    



}
