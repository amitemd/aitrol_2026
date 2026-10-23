<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Sis_medico\Empresa; 
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\User;
use Sis_medico\User_espe;
use Sis_medico\Log_agenda;
use Sis_medico\PentaxProc;

use Sis_medico\Log_usuario;
use Sis_medico\Seguro;
use Sis_medico\Archivo_historico;
use Sis_medico\Sala;
use Sis_medico\Agenda;
use Sis_medico\Procedimiento;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Tipo_Anesteciologia;
use Storage;
use Sis_medico\Paciente;
use Sis_medico\Record;
use Sis_medico\historiaclinica;
use Sis_medico\Cortesia_paciente;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Routing\Redirector;
use Cookie;




class VdoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(3)) == false){
          return true;
        }
    }

    private function rol_todos(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 3, 4, 5)) == false){
          return true;
        }
    }

    public function  agenda(Request $request){  

        //dd($request->all());
        if($request['fecha']==null){
            $fecha_hoy = Date('Y-m-d');    
        }else{
            $fecha_hoy = Date('Y-m-d',strtotime($request['fecha']));
        }
        
        $fecha_desde = date('Y-m-d',strtotime($fecha_hoy."- 120 days")); 
        //dd($fecha_hoy);
        $tipo = Auth::user()->id_tipo_usuario;
        if($this->rol()){
            return response()->view('errors.404');
        }
        $id = Auth::user()->id;
        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3],['id', '=', $id],])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
         if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento')->where('agenda.estado_cita','<','4')
            ->where('agenda.proc_consul', '=', 1)
                        ->where(function ($query) use ($id) {
                            $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
                            })
            
            ->whereBetween('fechaini',[$fecha_hoy.' 0:00:00', $fecha_hoy.' 23:59:00'])
            ->get();
            
        $agenda_px = DB::table('agenda as a')
            ->join('historiaclinica as h','h.id_agenda','a.id')
            ->leftjoin('pentax as p','p.id_agenda','a.id')
            ->join('paciente', 'a.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'h.id_seguro', '=', 'seguros.id')
            ->join('procedimiento', 'a.id_procedimiento', '=', 'procedimiento.id')
            ->select('a.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento','p.id as pentax')
            ->where('a.proc_consul', '=', 1)
                        ->where(function ($query) use ($id) {
                            $query->where([['h.id_doctor1', '=', $id], ['a.estado', '=', '1']])
                                    ->orWhere([['h.id_doctor2', '=', $id], ['a.estado', '=', '1']])
                                    ->orWhere([['h.id_doctor3', '=', $id], ['a.estado', '=', '1']]);
                            })
            ->where('a.created_at','>',$fecha_desde)->where('a.estado_cita','4')
            ->whereBetween('fechaini',[$fecha_hoy.' 0:00:00', $fecha_hoy.' 23:59:00'])
            ->get();

        //dd($agenda,$agenda_px);        
        $agenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro')
            ->where('proc_consul', '=', 0)
                        ->where(function ($query) use ($id) {
                            $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
                            })
            
            ->whereBetween('fechaini',[$fecha_hoy.' 0:00:00', $fecha_hoy.' 23:59:00'])
            ->get(); 

        //dd($agenda3);     

        $agenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
                        ->where(function ($query) use ($id) {
                            $query->where([['id_doctor1', '=', $id], ['estado', '=', '1']])
                                    ->orWhere([['id_doctor2', '=', $id], ['estado', '=', '1']])
                                    ->orWhere([['id_doctor3', '=', $id], ['estado', '=', '1']]);
                            })
            
            ->whereBetween('fechaini',[$fecha_hoy.' 0:00:00', $fecha_hoy.' 23:59:00'])
            ->get();

            $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();  

        $doctores = DB::table('users')->where([['id_tipo_usuario', '=', 3]])->get();//3=DOCTORES;
        $enfermero = DB::table('users')->where([['id_tipo_usuario', '=', 6]])->get();
        $doctor = User::find($id); 
        $nombres = $request['nombres'];
        $nombres_sql='';
        $agendas_pac = [];
        if($nombres!=null){
            $agendas_pac = DB::table('paciente as p')->join('historiaclinica as h','h.id_paciente','p.id')->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento')->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento');

            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2);
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }
            $nombres_sql= $nombres_sql.'%'; 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $agendas_pac = $agendas_pac->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
                      
            }
            else{

                //$agendas_pac = $agendas_pac->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
                $agendas_pac = $agendas_pac->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                    });
            }  

            $agendas_pac = $agendas_pac->get();   
        }

        
        Cookie::queue('ruta','agenda','60');
        
        //dd($agendas_pac);

        return view('agenda/calendario2', ['user' => $user, 'doctor' => $doctor, 'users' => $doctores, 'enfermero' => $enfermero, 'id' => $id, 'agenda' => $agenda, 'agenda2' => $agenda2, 'salas' => $salas, 'agenda3' => $agenda3, 'agenda_px' => $agenda_px, 'fecha_hoy' => $fecha_hoy, 'nombres' => $nombres, 'agendas_pac' => $agendas_pac]);
    }


    public function  agenda_completa(Request $request){  

        //dd($request->all());
        if($request['fecha']==null){
            $fecha_hoy = Date('Y-m-d');    
        }else{
            $fecha_hoy = Date('Y-m-d',strtotime($request['fecha']));
        }
        
        $fecha_desde = date('Y-m-d',strtotime($fecha_hoy."- 90 days")); 
        $fecha_hasta = date('Y-m-d',strtotime($fecha_hoy."+ 90 days")); 
        //dd($fecha_hoy);
        $tipo = Auth::user()->id_tipo_usuario;
        if($this->rol()){
            return response()->view('errors.404');
        }
        $id = Auth::user()->id;
        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3],['id', '=', $id],])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
         if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento')->where('agenda.estado_cita','<','4')
            ->where('agenda.proc_consul', '=', 1)
                        ->where(function ($query) use ($id) {
                            $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
                            })
            
            ->whereBetween('fechaini',[$fecha_desde.' 0:00:00', $fecha_hasta.' 23:59:00'])
            ->get();
            
        $agenda_px = DB::table('agenda as a')
            ->join('historiaclinica as h','h.id_agenda','a.id')
            ->leftjoin('pentax as p','p.id_agenda','a.id')
            ->join('paciente', 'a.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'h.id_seguro', '=', 'seguros.id')
            ->join('procedimiento', 'a.id_procedimiento', '=', 'procedimiento.id')
            ->select('a.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento','p.id as pentax')
            ->where('a.proc_consul', '=', 1)
                        ->where(function ($query) use ($id) {
                            $query->where([['h.id_doctor1', '=', $id], ['a.estado', '=', '1']])
                                    ->orWhere([['h.id_doctor2', '=', $id], ['a.estado', '=', '1']])
                                    ->orWhere([['h.id_doctor3', '=', $id], ['a.estado', '=', '1']]);
                            })
            ->where('a.created_at','>',$fecha_desde)->where('a.estado_cita','4')
            ->whereBetween('fechaini',[$fecha_desde.' 0:00:00', $fecha_hasta.' 23:59:00'])
            ->get();

        //dd($agenda,$agenda_px);        
        $agenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro')
            ->where('proc_consul', '=', 0)
                        ->where(function ($query) use ($id) {
                            $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
                            })
            
            ->whereBetween('fechaini',[$fecha_desde.' 0:00:00', $fecha_hasta.' 23:59:00'])
            ->get(); 

        //dd($agenda3);     

        $agenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
                        ->where(function ($query) use ($id) {
                            $query->where([['id_doctor1', '=', $id], ['estado', '=', '1']])
                                    ->orWhere([['id_doctor2', '=', $id], ['estado', '=', '1']])
                                    ->orWhere([['id_doctor3', '=', $id], ['estado', '=', '1']]);
                            })
            
            ->whereBetween('fechaini',[$fecha_desde.' 0:00:00', $fecha_hasta.' 23:59:00'])
            ->get();

            $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();  
        //dd($fecha_desde);
        $doctores = DB::table('users')->where([['id_tipo_usuario', '=', 3]])->get();//3=DOCTORES;
        $enfermero = DB::table('users')->where([['id_tipo_usuario', '=', 6]])->get();
        $doctor = User::find($id); 
        $nombres = $request['nombres'];
        $nombres_sql='';
        $agendas_pac = [];
        if($nombres!=null){
            $agendas_pac = DB::table('paciente as p')->join('historiaclinica as h','h.id_paciente','p.id')->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento')->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento');

            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2);
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }
            $nombres_sql= $nombres_sql.'%'; 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $agendas_pac = $agendas_pac->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
                      
            }
            else{

                //$agendas_pac = $agendas_pac->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', ['%'.$nombres.'%']);
                $agendas_pac = $agendas_pac->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                    });
            }  

            $agendas_pac = $agendas_pac->get();   
        }

        
        Cookie::queue('ruta','agenda','60');
        
        //dd($agendas_pac);
        //return "hola";

        return view('agenda/calendario3', ['user' => $user, 'doctor' => $doctor, 'users' => $doctores, 'enfermero' => $enfermero, 'id' => $id, 'agenda' => $agenda, 'agenda2' => $agenda2, 'salas' => $salas, 'agenda3' => $agenda3, 'agenda_px' => $agenda_px, 'fecha_hoy' => $fecha_hoy, 'nombres' => $nombres, 'agendas_pac' => $agendas_pac]);
    }

    public function guardar(Request $request)
    {
        date_default_timezone_set('America/Guayaquil');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $estado_cita = 0;
        $estado  =1;

        $cuenta  =  DB::table('agenda')
                        ->where('id_paciente', '=', $idusuario)->count();       
        if($cuenta  == '0'){
            $tipo_cita = 0;
        }
        else
        {
            $tipo_cita = 1;
            
        }
        

        $fecha = date('Y-m-d H:i');             
        $this->validateInput3($request, $idusuario);
        
        $this->validateInput4($request);
            $hora_ini = $request['inicio'];
            $hora_fin = $request['fin'];
            $validacion =  DB::table('agenda')->where('id_doctor1', '=', $idusuario)
                                        ->where('estado_cita', '<>', '4')//No admisionadas 
                    /*agregado vh*/     ->where('estado', '=', '1')//Activas 
                                        ->where('proc_consul', '<>', '2')//NO reuniones             
                                        ->where(function ($query) use ($hora_ini, $hora_fin) {
                                            $query->where(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechafin', [$hora_ini, $hora_fin]);
                                                        })
                                                    ->orWhere(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechaini', [$hora_ini, $hora_fin]);
                                                        });
                                            })->get();
            foreach($validacion as $value){
                $agenda = Agenda::findOrFail($value->id);
                $ip_cliente= $_SERVER["REMOTE_ADDR"];
                $idusuario = Auth::user()->id;
                date_default_timezone_set('America/Guayaquil');
                $descripcion="Doctor bloqueo su Horario";
                $input = [
                    'estado' => '-1',
                    'estado_cita' => '2',
                    'observaciones' => $descripcion,
                    'id_usuariomod' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                ];
                Log_agenda::create([
                    'id_agenda' => $value->id,
                    'estado_cita_ant' => $value->estado_cita, 
                    'estado_ant' => $value->estado,
                    'observaciones_ant' => $value->observaciones,
                    'estado_cita' => '2',
                    'estado' => '-1',
                    'descripcion' => 'Doctor Bloqueo su Horario',
                    'id_usuarioconfirma' => $value->id_usuarioconfirma,
                    'id_usuariomod' => $idusuario,
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion' => $ip_cliente,           
                ]); 
                $agenda::where('id', $value->id)
                ->update($input);
                   
            }
            agenda::create([
            'procedencia' => $request['clase'],    
            'fechaini' => $request['inicio'],
            'fechafin' => $request['fin'],
            'id_doctor1' => $idusuario,
            'proc_consul' => 2,
            'estado_cita' => 1,
            'observaciones' => $request['observaciones'],
            'estado' => 1,
            'id_sala' => $request['id_sala'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario
            ]);

        //return  redirect()->route('agenda.agenda2');
        return "ok";
    }
     private function  validateInput3($request, $idusuario) {

        
        $ini2 = date_create($request['inicio']);
        $fin2 = date_create($request['fin']);
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin = date_format($fin, 'Y/m/d H:i:s');
        $dato2 = DB::table('agenda')->where(function ($query) use ($request, $idusuario, $inicio, $fin){
                return $query->where('id_doctor1', '=', $idusuario)
                      ->orWhere('id_doctor2', '=', $idusuario)
                      ->orWhere('id_doctor3', '=', $idusuario);
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
                ->where(function ($query) {
                     return $query->where('estado_cita', '=', 4)
                      ->orWhere('proc_consul', '=', 2);
                  })
                ->get();

         //return $dato2; 
        $r1=0;
        $r2=0;        
        foreach($dato2 as $val){
            if($val->estado_cita=='4'){
                $r1 = 1;
            }

            if($val->proc_consul=='2'){
                $r2 = 1;
            }
        } 

        $vmsn="";
        if($r1==1 && $r2==1){
            $vmsn="Existe una Reunión y una cita ya Admisionada.";
        }
        if($r1==1 && $r2==0){
            $vmsn="Existe una cita ya Admisionada.";
        }
        if($r1==0 && $r2==1){
            $vmsn="Existe una Reunión agendada.";
        }      

        $cant_agenda = $dato2->count();
        $rules = [
            'id_doctor1' =>  'unique_doctor:'.$cant_agenda,
            'observaciones' => 'required|max:200',
            'inicio' =>  'required|date|before:fin',
            'fin' => 'required|date|after:inicio',
            'id_sala' => 'required',  
            ];

        $mensajes = [
            'observaciones.max' => 'La observacion no puede ser mayor a :max caracteres',   
            'observaciones.required' => 'Ingrese el título de la reunión.',
            'id_sala.required' => 'Seleccione la Ubicación.',  
            'id_doctor1.unique_doctor' => $vmsn,   
            'inicio.required' => 'Agregue una fecha de Inicio.',
            'inicio.date' =>'fecha mal agregada.',
            'inicio.before' =>'la fecha de inicio debe ser antes que la de fin',
            'inicio.after' =>'la fecha de inicio debe ser después de la fecha actual',
            'fin.required' => 'Agregue una fecha de Inicio.',
            'fin.date' =>'fecha mal agregada.',
            'fin.before' =>'la fecha de fin debe ser después que la fecha actual',
            'fin.after' =>'la fecha de fin debe ser después que la de inicio',
            ];

        $this->validate($request, $rules, $mensajes);

    }

    private function validateInput4($request) {
        $fecha = date('Y-m-d H:i');
        $reglas =[
        'inicio' =>  'date|after:'.$fecha,
        'fin' =>  'date|after:'.$fecha,  
        ];
        $mensajes = [
        'inicio.after' =>'la fecha de inicio debe ser después de la fecha actual',
        'fin.after' =>'la fecha de fin debe ser después que la fecha actual',
        ];
        $this->validate($request,$reglas, $mensajes);
    }

    public function detalle($id)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol_todos()){
            return response()->view('errors.404');
        }

        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado','1')->get(); //3=DOCTORES;
        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado','1')->get(); //6=ENFERMEROS;
        $anestesiologos = DB::table('users')->where('id_tipo_usuario', '=', 9)->where('estado','1')->get(); //9=ANESTESIOLOGO;
        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get(); 
        
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $id)
            ->first();

        $ced_paciente = $agenda->id_paciente;
        $paciente = Paciente::find($ced_paciente);
        $hca = DB::table('historiaclinica')
        ->where('id_agenda', '=', $id)
        ->get();
        $hca_seguro= $hca[0]->id_seguro;
        $hca_id = $hca[0]->hcid;
        $seguro =  Seguro::find($hca_seguro);

        $hcp =  DB::select("SELECT h.*, e.nombre as especialidad, s.nombre as snombre ,d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
                            WHERE h.id_paciente = ".$ced_paciente." AND
                            a.id = h.id_agenda AND
                            s.id = h.id_seguro AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> ".$id."
                            ORDER BY a.fechaini DESC");

        $ag = Agenda::find($id);
        $pc = $ag->proc_consul;


        $records = Record::all();

        $archivo_vrf=array();
        if($hca[0]->verificar==1){
            $archivo_historico=Archivo_historico::where('id_historia',$hca[0]->hcid)->where('tipo_documento','VRF')->get(); 
            $archivo_vrf=$archivo_historico[0];
        } 
        
        $hcagenda = DB::table('agenda_archivo')->where('id_agenda', '=', $id)->get();

        if($pc == 0){
            return view('historiaclinica/cita', ['agenda' => $agenda, 'paciente' => $paciente,'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas,'hca' => $hca, 'hcp' => $hcp,  'seguro' => $seguro, 'archivo_vrf' => $archivo_vrf, 'hcagenda' => $hcagenda]);
        }
        if($pc == 1){
            $fotos = DB::table('archivo_historico')
        ->where('id_historia', '=', $hca_id)
        ->where('tipo_documento', '<>', 'VRF')
        ->get();
            $procedimientos = DB::table('pentax_procedimiento')
                ->join('procedimiento', 'pentax_procedimiento.id_pentax', '=', 'procedimiento.id')
                ->select('pentax_procedimiento.*', 'procedimiento.nombre')
                ->where('id_pentax', '=', $hca_id)
                ->get(); 
            $tipo_anesteciologia = Tipo_Anesteciologia::all();
            $record_anestesiologico = Hc_Anestesiologia::where('id_hc', '=', $hca_id)->get();
            
            return view('historiaclinica/Procedimiento', ['agenda' => $agenda, 'paciente' => $paciente,'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas,'hca' => $hca, 'hcp' => $hcp,  'seguro' => $seguro, 'fotos' => $fotos, 'archivo_vrf' => $archivo_vrf, 'hcagenda' => $hcagenda, 'records' => $records, 'anestesiologos' => $anestesiologos, 'record_anestesilogico' => $record_anestesiologico, 'tipo_anesteciologia' => $tipo_anesteciologia]);
        }
    }

    public function detalle3($id)//PROVISIONAL
    {

        
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol_todos()){
            return response()->view('errors.404');
        }

        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado','1')->get(); //3=DOCTORES;
        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado','1')->get(); //6=ENFERMEROS;
        $anestesiologos = DB::table('users')->where('id_tipo_usuario', '=', 9)->where('estado','1')->get(); //9=ANESTESIOLOGO;
        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get(); 
        
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $id)
            ->first();

        $ced_paciente = $agenda->id_paciente;
        $paciente = Paciente::find($ced_paciente);
        $hca = DB::table('historiaclinica')
        ->where('id_agenda', '=', $id)
        ->get();
        $hca_seguro= $hca[0]->id_seguro;
        $hca_id = $hca[0]->hcid;
        $seguro =  Seguro::find($hca_seguro);

        $hcp =  DB::select("SELECT h.*, e.nombre as especialidad, s.nombre as snombre ,d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
                            WHERE h.id_paciente = ".$ced_paciente." AND
                            a.id = h.id_agenda AND
                            s.id = h.id_seguro AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> ".$id."
                            ORDER BY a.fechaini DESC");

        $ag = Agenda::find($id);
        $pc = $ag->proc_consul;


        $records = Record::all();

        $archivo_vrf=array();
        if($hca[0]->verificar==1){
            $archivo_historico=Archivo_historico::where('id_historia',$hca[0]->hcid)->where('tipo_documento','VRF')->get(); 
            $archivo_vrf=$archivo_historico[0];
        } 
        
        $hcagenda = DB::table('agenda_archivo')->where('id_agenda', '=', $id)->get();

        if($pc == 0){
            return view('historiaclinica/cita', ['agenda' => $agenda, 'paciente' => $paciente,'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas,'hca' => $hca, 'hcp' => $hcp,  'seguro' => $seguro, 'archivo_vrf' => $archivo_vrf, 'hcagenda' => $hcagenda]);
        }
        if($pc == 1){

            $fotos = DB::table('archivo_historico')
        ->where('id_historia', '=', $hca_id)
        ->where('tipo_documento', '<>', 'VRF')
        ->get();
            $procedimientos = DB::table('pentax_procedimiento')
                ->join('procedimiento', 'pentax_procedimiento.id_pentax', '=', 'procedimiento.id')
                ->select('pentax_procedimiento.*', 'procedimiento.nombre')
                ->where('id_pentax', '=', $hca_id)
                ->get();  
            return view('historiaclinica/procedimiento3', ['agenda' => $agenda, 'paciente' => $paciente,'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas,'hca' => $hca, 'hcp' => $hcp,  'seguro' => $seguro, 'fotos' => $fotos, 'archivo_vrf' => $archivo_vrf, 'hcagenda' => $hcagenda, 'records' => $records, 'anestesiologos' => $anestesiologos]);
        }
       
        
    }

    public function anterior($id)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }

        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->get(); //3=DOCTORES;
        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->get(); //6=ENFERMEROS;
        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get(); 
        
        $agenda = DB::table('agenda')
            ->join('users', 'agenda.id_doctor1', '=', 'users.id')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'users.nombre1 as dnombre1', 'users.nombre2 as dnombre2', 'users.apellido1 as dapellido1','users.apellido2 as dapellido2')
            ->where('agenda.id', '=', $id)
            ->first();
        $ced_paciente = $agenda->id_paciente;
        $paciente = Paciente::find($ced_paciente);
        $hca = DB::table('historiaclinica')
        ->where('id_agenda', '=', $id)

        ->get();
        $hca_seguro= $hca[0]->id_seguro;
        $seguro =  Seguro::find($hca_seguro);

        $hcp =  DB::select("SELECT h.*, e.nombre as especialidad, d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, agenda a, especialidad e, users d
                            WHERE h.id_paciente = ".$ced_paciente." AND
                            a.id = h.id_agenda AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> ".$id);
        
 
       
        return view('historiaclinica/anterior', ['agenda' => $agenda, 'paciente' => $paciente,'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas,'hca' => $hca, 'hcp' => $hcp,  'seguro' => $seguro]);
    }
    public function hcagenda($id)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $hcagenda = DB::table('agenda_archivo')->where('id', '=', $id)->get();

       
        return view('historiaclinica/anterior2', ['hcagenda' => $hcagenda]);
    }

    public function actualizar(Request $request)
    {
        $hoy = date('Y-m-d H:i:s');
        $id = $request['id'];
        $evolucion = $request['evolucion'];
        $observaciones = $request['observaciones'];
        $receta = $request['receta'];
        $peso = $request['peso'];
        $altura = $request['altura'];
        $temperatura = $request['temperatura'];
        $presion = $request['presion'];
        $historia=historiaclinica::find($id);
        $cita=Agenda::find($historia->id_agenda);

        if($historia->estado==0)
        {
            $estado = 1;
            $fecha_atencion = $hoy;
        }
        else
        {
            $estado = $historia->estado;
            $fecha_atencion = $historia->fecha_atencion;
        }
        $input = [
            'peso' => $peso,
            'altura' => $altura,
            'temperatura' => $temperatura,
            'presion' => $presion,
            'evolucion' => $evolucion,
            'observaciones' =>  $observaciones,
            'receta' =>  $receta,
            'estado' => $estado,
            'fecha_atencion' => $fecha_atencion,
        ];
        $input_paciente=[
            'peso' => $peso,
            'altura' => $altura,
            'temperatura' => $temperatura,
            'presion' => $presion,
        ];

        
       if($cita->proc_consul==0){
            $this->validaHC($request);
       }
       else{
            $this->validaHC2($request);  
       }
        
        
        $this->ValidateAdmision($request);
        
        $historia=historiaclinica::find($id);

        $historia->update($input);

        $paciente=paciente::find($historia->id_paciente);

        $paciente->update($input_paciente);
             
        return  redirect()->route('agenda.agenda2'); 
       
        
    } 

    private function ValidateAdmision(Request $request){
        $mensajes = [ 
            'gruposanguineo.required' => 'Agrega el grupo sanguíneo.',
            'gruposanguineo.max' =>'El grupo sanguíneo no puede ser mayor a :max caracteres.',
            'gruposanguineo.in' =>'El grupo sanguíneo seleccionado no existe.',
            'alergias.max' =>'Las alergias no pueden ser mayor a :max caracteres.',
            'alergias.required' =>'Agrega las alergias',
            'vacuna.max' =>'Las vacunas no pueden ser mayor a :max caracteres.',
            'vacuna.required' =>'Agrega las vacunas',
            'alcohol.in' => 'Selecciona la opción correcta.',
            'alcohol.required' =>'Selecciona el consumo de alcohol',
            'hijos_vivos.between' => 'Cantidad debe ser mayor o igual a cero o menor a 100.',
            'hijos_muertos.between' => 'Cantidad debe ser mayor o igual a cero o menor a 100.',
            'anticonceptivos.max' =>'Los anticonceptivos no pueden ser mayor a :max caracteres.',
            'antecedentes_pat.max' =>'Los antecedentes no puede ser mayor a :max caracteres.',
            'antecedentes_pat.required' =>'Agrega los antecedentes patológicos',
            'antecedentes_fam.max' =>'Los antecedentes no puede ser mayor a :max caracteres.',
            'antecedentes_fam.required' =>'Agrega los antecedentes familiares',
            'antecedentes_quir.max' =>'Los antecedentes no puede ser mayor a :max caracteres.',
            'antecedentes_quir.required' =>'Agrega los antecedentes quirurgicos',
            'transfusion.in' => 'Selecciona SI o NO.',
            'transfusion.required' =>'Selecciona la transfusión',
            'primera_mens.between' => 'Edad debe ser mayor o igual a cero o menor a 100.',
            'menopausia.between' => 'Edad debe ser mayor o igual a cero o menor a 100.',
            'parto_cesarea.between' => 'Cantidad debe ser mayor o igual a cero o menor a 100.',
            'parto_normal.between' => 'Cantidad debe ser mayor o igual a cero o menor a 100.',
            'aborto2.between' => 'Cantidad debe ser mayor o igual a cero o menor a 100.',

        ];

        $constraints = [
            'gruposanguineo' => 'required|max:255|in:AB+,AB-,A+,A-,B+,B-,O+,O-',
            'alergias' => 'required|max:255', 
            'vacuna' => 'required|max:255',
            'alcohol' => 'required|in:Nunca,1 o menos veces al mes,2 o 4 veces al mes,2 o 3 veces a la semana,4 o más veces a la semana',
            'hijos_vivos' => 'between:1,100',
            'hijos_muertos' => 'between:1,100',
            'anticonceptivos' => 'max:255',
            'antecedentes_pat' => 'required|max:300',
            'antecedentes_fam' => 'required|max:300',
            'antecedentes_quir' => 'required|max:300',
            'transfusion' => 'required|in:SI,NO',
            'primera_mens' => 'between:1,100',
            'menopausia' => 'between:1,100',
            'parto_cesarea' => 'between:1,100',
            'parto_normal' => 'between:1,100',
            'aborto' => 'between:1,100',
        ];    
            
        $this->validate($request, $constraints, $mensajes); 

    }

    private function validaHC ($request)
    {
            $constraints=[
            'peso' => 'between:1,1000',
            'altura' => 'between:1,1000',
            'temperatura' => 'between:1,1000',
            'presion' => 'between:1,1000',
            'evolucion' => 'required|max:300',
            'observaciones' =>  'required|max:250',
            'receta' =>  'required|max:300',
            ];
        $mensajes=[
            'peso.between' => 'Cantidad debe ser mayor o igual a cero o menor a 1000.',
            'altura.between' => 'Cantidad debe ser mayor o igual a cero o menor a 1000.',
            'temperatura.between' => 'Cantidad debe ser mayor o igual a cero o menor a 1000.',
            'presion.between' => 'Cantidad debe ser mayor o igual a cero o menor a 1000.',
            'evolucion.max' =>'La evolución no pueden ser mayor a :max caracteres.',
            'observaciones.max' =>'La observación no pueden ser mayor a :max caracteres.',
            'receta.max' =>'La receta no pueden ser mayor a :max caracteres.',
            ];
        $this->validate($request, $constraints, $mensajes);

    } 

     private function validaHC2 ($request)
    {
            $constraints=[
            'peso' => 'between:1,1000',
            'altura' => 'between:1,1000',
            'temperatura' => 'between:1,1000',
            'presion' => 'between:1,1000',
            'evolucion' => 'required|max:300',
            'observaciones' =>  'required|max:250',
            
            ];
        $mensajes=[
            'peso.between' => 'Cantidad debe ser mayor o igual a cero o menor a 1000.',
            'altura.between' => 'Cantidad debe ser mayor o igual a cero o menor a 1000.',
            'temperatura.between' => 'Cantidad debe ser mayor o igual a cero o menor a 1000.',
            'presion.between' => 'Cantidad debe ser mayor o igual a cero o menor a 1000.',
            'evolucion.max' =>'El hallazgo no pueden ser mayor a :max caracteres.',
            'observaciones.max' =>'La conclusión no pueden ser mayor a :max caracteres.',
            
            ];
        $this->validate($request, $constraints, $mensajes);

    } 

    

    public function foto(Request $request)
    {   

        $id = $request['id'];
        $fotos = archivo_historico::find($id);
        return view('historiaclinica/foto',  ['foto' => $fotos]);
        
    }

    public function anteriorprocedimiento($id)
    {
        
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
        $agenda = DB::table('agenda')
            ->join('users', 'agenda.id_doctor1', '=', 'users.id')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'users.nombre1 as dnombre1', 'users.nombre2 as dnombre2', 'users.apellido1 as dapellido1','users.apellido2 as dapellido2')
            ->where('agenda.id', '=', $id)
            ->first();

        $ced_paciente = $agenda->id_paciente;
        $paciente = Paciente::find($ced_paciente);
        $hca = DB::table('historiaclinica')
        ->where('id_agenda', '=', $id)
        ->get();
        $hca_id = $hca[0]->hcid;
        $hca_seguro= $hca[0]->id_seguro;
        $seguro =  Seguro::find($hca_seguro);
        $fotos = DB::table('archivo_historico')
        ->where('id_historia', '=', $hca_id)
        ->where('tipo_documento', '<>', 'VRF')
        ->get();
 
       
        return view('historiaclinica/anterior-procedimiento', ['agenda' => $agenda, 'paciente' => $paciente, 'hca' => $hca,  'seguro' => $seguro,'fotos' => $fotos]);
    }

    public function foto2(Request $request)
    {
        $path = public_path().'/app/hc/';
        $files = $request->file('foto');
        $idhc = $request['id'];
        $cedula = $request['paciente'];
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $i=1;
        foreach($files as $file){
            $input_archivo = [
                'id_historia' => $idhc,
                'tipo_documento' => "ima",
                'descripcion' => "Fotos del procedimiento",
                'ruta' => "/hc/",
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
            ]; 
            //sacar la extension
            $extension= $file->getClientOriginalExtension();

            $id_archivo=Archivo_historico::insertGetId($input_archivo);
            //nuevo nombre del archivo
            $fileName = 'hc_'.$cedula.'_ima_'.$idhc.'_'.$id_archivo.'.'.$extension;
            //ingresar la foto
            \Storage::disk('hc')->put($fileName,  \File::get($file));
            //ACTUALIZAR LOS DATOS
            $archivo_historico=Archivo_historico::find($id_archivo);
            
            $archivo_historico->archivo=$fileName;
            $archivo_historico->ip_modificacion=$ip_cliente;
            $archivo_historico->id_usuariomod=$idusuario;
            $r2=$archivo_historico->save();

            $i =  $i+1;
        }
    }

    /* 23.11.2017 ACTUALIZAR LA CORTESIA */
     public function actualizacortesia($id,$c)
    {
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $agenda = Agenda::findOrFail($id);
        // Redirect to user list if updating user wasn't existed
        if ($agenda == null || count($agenda) == 0) {
            return redirect()->intended('/agenda');
        }
        
        
        if($c == 0){$cortesia="NO";}
        elseif($c == 1){$cortesia="SI";}
        $input=[
                'cortesia' => $cortesia,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario

            ];
          
        $agenda->update($input); 

        $cortesia_paciente=Cortesia_Paciente::find($agenda->id_paciente);
        
        if(is_null($cortesia_paciente)){
            $input_cortesia=[
                    'id' => $agenda->id_paciente,
                    'cortesia' => $cortesia,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario
                ];
            Cortesia_Paciente::create($input_cortesia);    
        }
        else{
            $input_cortesia=[
                    'id' => $agenda->id_paciente,
                    'cortesia' => $cortesia,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];
            $cortesia_paciente->update($input_cortesia);    
        }

        
          
        return  redirect()->route("agenda.detalle", ['id' => $agenda->id]);
        
    }

    public function agendar_reunion($id_doctor, $i)
    {
       
        if($this->rol()){
            return response()->view('errors.404');
        }
        $doctor = User::find($id_doctor);
        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();
        $procedimiento = Procedimiento::all();
        return view('agenda.ag_reunion',['i' => $i, 'doctor' => $doctor, 'procedimiento' => $procedimiento, 'paciente' => null, 'cortesia_paciente' => null, 'salas' => $salas]);

    }

    public function agendar_doctor($id_doctor, $i)
    {
        
        if($this->rol()){
            return response()->view('errors.404');
        }
        $doctor = User::find($id_doctor);
        $procedimiento = Procedimiento::all();
        return view('agenda.agregar_dr',['i' => $i, 'doctor' => $doctor, 'procedimiento' => $procedimiento, 'paciente' => null, 'cortesia_paciente' => null]);

    }

    public function valida_form(Request $request)
    {
        
        if($request['id_paciente']=='0')
        {
            $rules = [  'nombre1' => 'required',
                    'cortesia' => 'required',
                    'inicio' =>  'required|date|before:fin|after:'.date('Y/m/d H:i'),
                    'fin' => 'required|date|after:inicio',
                    'telefono1' => 'required|max:60',
                    //'procedimiento' => 'required',  
                ];
        }else{
            $rules = [  'nombre1' => 'required',
                    'cortesia' => 'required',
                    'inicio' =>  'required|date|before:fin|after:'.date('Y/m/d H:i'),
                    'fin' => 'required|date|after:inicio',
                    //'procedimiento' => 'required',  
                ];  
        }
        

        $msn =  [
                    'nombre1.required' => 'Ingrese un nombre',
                    'cortesia.required' => 'Selecciona la cortesia',
                    'inicio.required' => 'Ingresa la fecha de inicio',
                    'fin.required' => 'Ingresa la fecha de fin',
                    'inicio.date' => 'Formato de la fecha esta incorrecto',
                    'fin.date' => 'Formato de la fecha esta incorrecto',
                    'inicio.before' => 'La fecha de inicio debe ser antes de la fecha de fin',
                    'inicio.after' => 'La fecha de inicio debe ser después de hoy',
                    'fin.after' => 'La fecha de fin debe ser después de la fecha de inicio',
                    'telefono1.required' => 'El teléfono es requerido',
                    'telefono1.max' => 'El teléfono no puede ser mayor a :max caracteres.',
                    'procedimiento.required' => 'Seleccione por lo menos un procedimiento',
                ]; 

        $this->validate($request, $rules, $msn);               

    }

    public function valida_proc(Request $request)
    {
        $rules = [  
                    'procedimiento' => 'required',  
                ];

        $msn =  [
                    'procedimiento.required' => 'Seleccione por lo menos un procedimiento',
                ]; 

        $this->validate($request, $rules, $msn);          
    }

    public function crear_cita_dr(Request $request)
    {
       //return $request->all();
        date_default_timezone_set('America/Guayaquil');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        
        $this->valida_form($request);


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
                $input_usu = [

                    'id' => $codigo,
                    'nombre1' => strtoupper($nombre_arr[0]),
                    'nombre2' => strtoupper($nombre2),
                    'apellido1' => strtoupper($apellido1),
                    'apellido2' => strtoupper($apellido2),
                    'telefono1' => $request['telefono1'],
                    'telefono2' => $request['telefono1'],
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
                    //$user->update($input_usu);
                }else{
                    User::create($input_usu);
                }   

                $input_pac = [

                    'id' => $codigo,
                    'id_usuario' => $codigo,
                    'nombre1' => strtoupper($nombre_arr[0]),
                    'nombre2' => strtoupper($nombre2),
                    'apellido1' => strtoupper($apellido1),
                    'apellido2' => strtoupper($apellido2),
                    'telefono1' => $request['telefono1'],
                    'telefono2' => $request['telefono1'],
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
                    'dato2' => 'CREADO POR EL DOCTOR',
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
            'proc_consul' => 0,
            'espid' => $espid,
            'estado_cita' => 2,
            'id_sala' => 9,
            'observaciones' => 'INGRESADO POR EL DOCTOR, COMPLETAR LOS DATOS',
            'id_seguro' => 1,
            'estado' => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'cortesia' => $request['cortesia'],
            ];
            
            $id_agenda = agenda::insertGetId($input_historia);

        }
        
        if($valor == 1){

            $this->valida_proc($request);
            
            $procedimientos =  $request['procedimiento'];
            $procedimientop = $procedimientos[0];

            $input_historia = [
            'fechaini' => $request['inicio'],
            'fechafin' => $request['fin'],
            'id_paciente' => $codigo,
            'id_doctor1' => $request['id_doctor1'],
            'id_procedimiento' => $procedimientop,
            'proc_consul' => 1,            
            'id_sala' => 10,
            'espid' => $espid,
            'id_seguro' =>1,             
            'estado_cita' => 2,
            'observaciones' => 'INGRESADO POR EL DOCTOR, COMPLETAR LOS DATOS',
            'estado' => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario, 
            'cortesia' => $request['cortesia'],
            
            ];

            
            $id_agenda = agenda::insertGetId($input_historia);
            $procedimiento_enviar = null;
            foreach ($procedimientos as $value){
                
                if($procedimientop != $value ){
                    AgendaProcedimiento::create([
                    'id_agenda' => $id_agenda,
                    'id_procedimiento' => $value,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario
                    ]);
                }

            }
        }

        return "ok"; 
        
    }


     public function validaconfir(Request $request)
    {   
       
        $id_doctor =  $request['id_doctor1'];
        $hora_ini = $request['inicio'];
        $hora_fin = $request['fin'];

        $idusuario = Auth::user()->id;

        $this->validateInput3($request, $idusuario);
        
        $this->validateInput4($request);

        $validar =  DB::table('agenda')->where('id_doctor1', '=', $id_doctor)
                                        ->where('estado_cita', '=', '1')
                                        ->where('proc_consul', '<>', '2')
                                        ->where(function ($query) use ($hora_ini, $hora_fin) {
                                            $query->where(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechafin', [$hora_ini, $hora_fin]);
                                                        })
                                                    ->orWhere(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechaini', [$hora_ini, $hora_fin]);
                                                        });
                                            })
                                        ->get();

        $valida2 = count($validar);

        if($valida2>0){

            return "Tiene citas confirmadas, ¿ Desea reagendarlas ?";
        
        }else{

            return 0; 

        } 

    }      

    




}
