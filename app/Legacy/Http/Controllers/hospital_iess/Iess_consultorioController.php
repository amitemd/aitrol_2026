<?php

namespace Sis_medico\Http\Controllers\hospital_iess;

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
use Sis_medico\Empresa;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;




class Iess_consultorioController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 4, 5)) == false){
          return true;
        }
    }

    public function crear_consulta($id, $fecha)
    {
        
        if($this->rol()){
            return response()->view('errors.404');
        }
        

        $doctor = User::find($id); 
        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital', 'hospital.direccion as direccion_hospital')->orderBy('hospital.nombre_hospital')
            ->get();  

        $especialidad = DB::table('user_espe')->join('especialidad', 'user_espe.espid', '=', 'especialidad.id')
            ->select('user_espe.*', 'especialidad.nombre as enombre')->where('usuid', '=', $id)->get();

        date_default_timezone_set('UTC');

        $fecha  = substr($fecha, 0,10);
        $fecha2 = date('Y/m/d H:i', $fecha);

        //dd($fecha,$fecha2);

        return view('hospital_iess/iess_consultorio/agregar', ['id' => $id, 'salas' => $salas, 'doctor' => $doctor, 'especialidad' => $especialidad, 'hora' => $fecha2, 'unix' => $fecha ]);
    }


    private function  validateInput3($request) {
        $fecha = date('Y-m-d H:i');
        $ini2 = date_create($request['inicio']);
        $fin2 = date_create($request['fin']);
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin = date_format($fin, 'Y/m/d H:i:s');
        $dato2 = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin){
                return $query->where('id_doctor1', '=', $request['id_doctor1'])
                      ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                      ->orWhere('id_doctor3', '=', $request['id_doctor1']);
                  })
                ->where(function ($query) use ($request, $inicio, $fin) {
                            return $query->whereRaw("(('".$inicio."' BETWEEN fechaini and fechafin)")
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("'".$fin."' BETWEEN fechaini and fechafin)");}
                                )                  
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("(fechaini BETWEEN '".$inicio."' and '".$fin."'");
                               })
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("fechafin BETWEEN '".$inicio."' and '".$fin."')");
                               });
                            })
                ->where(function ($query) {
                    return $query->where('estado', 1);
                })
                ->get();

        $cant_agenda = $dato2->count();
        $rules = [
            'id_doctor1' =>  'unique_doctor:'.$cant_agenda,
            //'observaciones' => 'max:200',
            'inicio' =>  'required|date|before:fin|after:'.$fecha,
            'fin' => 'required|date|after:inicio|after:'.$fecha,  
            'id_sala' =>  'required',
            ];
        $mensajes = [
            'observaciones.max' => 'La observacion no puede ser mayor a :max caracteres',    
            'id_doctor1.unique_doctor' => 'La fecha seleccionada esta ocupada para el Doctor Principal',   
            'inicio.required' => 'Agregue una fecha de Inicio.',
            'inicio.date' =>'fecha mal agregada.',
            'inicio.before' =>'la fecha de inicio debe ser antes que la de fin',
            'inicio.after' =>'la fecha de inicio debe ser después de la fecha actual',
            'fin.required' => 'Agregue una fecha de Inicio.',
            'fin.date' =>'fecha mal agregada.',
            'fin.before' =>'la fecha de fin debe ser después que la fecha actual',
            'fin.after' =>'la fecha de fin debe ser después que la de inicio',
            'procedencia.required' => 'Agregue la procedencia.',
            'procedencia.max' => 'La procedencia no puede ser mayor a :max caracteres',
            'fecha_nacimiento.required' => 'Agregue la fecha de nacimiento.',
            'inicio.after' =>'la fecha de inicio debe ser después de la fecha actual',
            'fin.after' =>'la fecha de fin debe ser después que la fecha actual',
            ];
        
        $this->validate($request, $rules, $mensajes);

    }

    private function validateMax1($request) {
    
        $fecha_req=$request['inicio'];
        $fecha_req=substr($fecha_req,0,10);
        $fecha_req=strtotime($fecha_req);
        $fecha_min=date('Y-m-d H:i',$fecha_req);
        $fecha_max = strtotime ( '+1 day' , strtotime ( $fecha_min ) ) ;
        $fecha_max = date ( 'Y-m-d H:i' , $fecha_max ); 
        return 

        $dato2 = DB::table('agenda')->where(function ($query) use ($request){
                $query->where('id_doctor1', '=', $request['id_doctor1'])
                      ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                      ->orWhere('id_doctor3', '=', $request['id_doctor1']);})
                      ->where('proc_consul', '=', $request['proc_consul']) 
                      ->where('estado', '<>', '0')
                      ->where(function ($query) use ($request,$fecha_min,$fecha_max) {
                            $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
                            })
                    ->get();
        $cantidad= $dato2->count();
        $doctor= User::find($request['id_doctor1']);
        if($request['proc_consul']==0){
            $rules = [        
        'id_doctor1' =>  'max_consulta:'.$cantidad.','.$doctor->max_consulta.','
        ];
        }else if($request['proc_consul']==1){
            $rules = [        
        'id_doctor1' =>  'max_procedimiento:'.$cantidad.','.$doctor->max_procedimiento.',' 
        ];
        }
        $mensajes = [
        'id_doctor1.max_consulta' => 'La cantidad máxima de consultas a atender por día es : '.$doctor->max_consulta,  
        'id_doctor1.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : '.$doctor->max_procedimiento,       
        ];       
        $this->validate($request, $rules, $mensajes);

    }

    public function crear(Request $request)
    {
       if($this->rol()){
            return response()->view('errors.404');
        }
       //return $request->all();
        date_default_timezone_set('America/Guayaquil');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        
        $this->validateInput3($request);

        //valida horario del doctor
        $horariocontroller = new HorarioController();
        $cantidad_horarios = $horariocontroller->valida_horarioxdoctor_dia($request);

        $this->validateMax1($request);

        $valor = $request['proc_consul'];
        $espid = user_espe::where('usuid',$request['id_doctor1'])->first()->espid;

        $fecha = date('Y-m-d H:i');
        
        $arreglo=[];
           
            if($request['id_paciente']!='0')
            {
                $paciente = Paciente::find($request['id_paciente']);
                $codigo = $paciente->id;    
            }
            else
            {
                $fecha = date('Y-m-d H:i:s');
                $codigo = 'AUX'.substr($fecha, 2, 2).substr($fecha, 5, 2).substr($fecha, 8, 2).substr($fecha, 11, 2).substr($fecha, 14, 2)/*.substr($fecha, 8, 2)*/;
                $cont=0;
                $flag=true;
                while($flag){
                    $cont++;
                    $arreglo[$cont]=$codigo;
                    $paciente_aux = Paciente::find($codigo);
                    if(!is_null($paciente_aux)){
                        $fecha = date('Y-m-d H:i:s', strtotime ( '+1 minute' , strtotime ( $fecha ) )) ;
                        $codigo = 'AUX'.substr($fecha, 2, 2).substr($fecha, 5, 2).substr($fecha, 8, 2).substr($fecha, 11, 2).substr($fecha, 14, 2)/*.substr($fecha, 8, 2)*/;
                    }else{
                        $flag=false;
                    } 

                }


                //crear al paciente como principal
                $nombre_arr = explode(' ',$request['nombre1']);
                $nombre2=""; $apellido1=""; $apellido2="";
                
                if(count($nombre_arr)==2){
                        
                        $apellido1=$nombre_arr[1];
                    
                    }
                if(count($nombre_arr)==3){
                        
                        $nombre2=$nombre_arr[1];
                        $apellido1=$nombre_arr[2];
                    }        
                if(count($nombre_arr)>=4){
                        
                        $nombre2=$nombre_arr[1];
                        $apellido1=$nombre_arr[2];
                        for($fi=3; $fi<count($nombre_arr); $fi++){
                            $apellido2=$apellido2.' '.$nombre_arr[$fi];   
                        }
                        
                    }

                //CREAR USUARIO
                $input_usu_c = [

                    'id' => $codigo,
                    'nombre1' => strtoupper($nombre_arr[0]),
                    'nombre2' => strtoupper($nombre2),
                    'apellido1' => strtoupper($apellido1),
                    'apellido2' => strtoupper($apellido2),
                    'telefono1' => '1',
                    'telefono2' => '1',
                    'id_tipo_usuario' => 2,
                    'email' => $codigo.'@mail.com',
                    'password' => bcrypt($codigo),
                    'tipo_documento' => 1,
                    'estado' => 1,
                    'imagen_url' => ' ',
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario    

                    ];   

                $user = User::find($codigo); 
                
                if (!is_null($user)) {
                    //$user->update($input_usu_a);
                }else{
                    User::create($input_usu_c);
                }   

                $input_pac = [

                    'id' => $codigo,
                    'id_usuario' => $codigo,
                    'nombre1' => strtoupper($nombre_arr[0]),
                    'nombre2' => strtoupper($nombre2),
                    'apellido1' => strtoupper($apellido1),
                    'apellido2' => strtoupper($apellido2),
                    'telefono1' => '1',
                    'telefono2' => '1',
                    'nombre1familiar' => strtoupper($nombre_arr[0]),
                    'nombre2familiar' => strtoupper($nombre2),
                    'apellido1familiar' => strtoupper($apellido1),
                    'apellido2familiar' => strtoupper($apellido2),
                    'parentesco' => 'Principal',
                    'parentescofamiliar' => 'Principal',
                    'tipo_documento' => 1,
                    'id_seguro' => 1,
                    'imagen_url' => ' ',
                    'menoredad' => 0,
                
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario

                    ];  

                $paciente = Paciente::find($codigo);
                if(is_null($paciente)){
                    paciente::create($input_pac);

                    $input_log = [
                    'id_usuario' => $idusuario,
                    'ip_usuario' => $ip_cliente,
                    'descripcion' => "CREA NUEVO PACIENTE",
                    'dato_ant1' => $codigo,
                    'dato1' => strtoupper($nombre_arr[0])." ".strtoupper($nombre2)." ".strtoupper($apellido1)." ".strtoupper($apellido2),
                    'dato_ant2' => " PARENTESCO: Principal",
                    'dato2' => 'IESS CONSULTORIO',
                    ]; 

                    Log_usuario::create($input_log);     
                }
                

                    

            }//termino de crear paciente y usuario

        
        if($valor == 0){

            $input_historia = [
            'fechaini' => $request['inicio'],
            'fechafin' => $request['fin'],
            'id_paciente' => $codigo,
            'id_doctor1' => $request['id_doctor1'],
            'id_sala' => $request['id_sala'],
            'tipo_cita' => $request['tipo_cita'],
            'proc_consul' => 0,
            'espid' => $espid,
            'estado_cita' => 0,
            'observaciones' => 'PACIENTE IESS CONSULTORIO',
            'id_seguro' => 2,
            'estado' => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'cortesia' => 'NO',
            'consultorio' => '1',
            ];
            
            $id_agenda = agenda::insertGetId($input_historia);

        }
        

        return  redirect()->route('agenda.fecha', ['id' => $request['id_doctor1'], 'fecha' => $request['unix']]);
        
    }

}