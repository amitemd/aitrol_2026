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
use Sis_medico\Log_Agenda;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Log_usuario;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Cardiologia;
use Sis_medico\Excepcion_Horario;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\Agenda_Proc_Cardiologia;
use Sis_medico\Sala;
use Excel;
use Sis_medico\Doctor_Tiempo;


use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;






use Response;



class CardiologiaController extends Controller
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
        if(in_array($rolUsuario, array(1, 3, 6, 11,7)) == false){
          return true;
        }
    }

    private function rol_rec(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 4, 5)) == false){
          return true;
        }
    }

    public function mostrar($id){
      //return "hola";
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }    

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $id)
            ->first();

        $paciente = Paciente::find($agenda->id_paciente);

        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado','1')->get(); //3=DOCTORES; 

        $cardiologos = DB::table('users')->where('id_tipo_usuario', '=', 12)->where('estado','1')->get(); //3=CARDIO; 

        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado','1')->get(); //6=ENFERMEROS; 

        $hcp =  DB::select("SELECT h.*, e.nombre as especialidad, s.nombre as snombre ,d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
                            WHERE h.id_paciente = ".$agenda->id_paciente." AND
                            a.id = h.id_agenda AND
                            s.id = h.id_seguro AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> ".$id."
                            ORDER BY a.fechaini DESC");

        $hca = DB::table('historiaclinica')->where('id_agenda', '=', $id)->first();

        $seguro =  Seguro::find($hca->id_seguro);
          //hacer query en tbl hc_cardio filtrado por hcid   

        $cardio = DB::table('hc_cardio')->where('hcid', '=', $hca->hcid)->first();  
        //dd($cardio);
//return "hola";
        return view('hc_admision/cardiologia/cardiologia', ['agenda' => $agenda, 'paciente' => $paciente, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'hcp' => $hcp, 'cardio' => $cardio, 'hca' => $hca,  'seguro' => $seguro, 'cardiologos' => $cardiologos]);  

    }


    /*    public function crea_actualiza(Request $request)
    {
    
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $historia = Historiaclinica::find($request['hcid']);

        
        
        $rules = [
        
        ];

        $msn = [
            
        ];

        //$this->validate($request,$rules,$msn);

        //$cardio = hc_cardio::where('id',$request['idcardio'])->first();

        //$cardio = DB::table('hc_cardio')->where('id', $request['idcardio']);

        $cardio = Cardiologia::find($request['idcardio']);

        //dd($request->all());
        if(is_null($cardio)){

            $input1 = [
                'cuadro_clinico' => $request['cuadro_clinico'],
                'resumen' => $request['resumen'],
                'plan' => $request['plan'],
                'motivo' => $request['motivo'],
                
                
                'id_especialista' => $request['id_especialista'],
                'hcid' => $request['hcid'],
                'esp_id' => "8",
                'id_drsol' => "1307189140",

                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente
            ];
           
            Cardiologia::create($input1);

        }else{

            $input1a = [

                'cuadro_clinico' => $request['cuadro_clinico'],
                'resumen' => $request['resumen'],
                'plan' => $request['plan'],
                'motivo' => $request['motivo'],

                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                
            ];
           

            //$cie_10_3 = Cie_10_3::find($id); 

            $cardio->update($input1a);

        }
          
               
       

        
        return $this->mostrar($historia->id_agenda);   

        
    }*/

    public function crea_actualiza(Request $request)
    {
    
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id= $request['hcid'];
         
        $input1 = [
            'cuadro_clinico' => $request["historia_clinica"],                                       
            'resumen' => $request["resumen"],
            'hcid' => $id,
            'plan_diagnostico' => $request["plan_diagnostico"],                                       
            'plan_tratamiento' => $request["plan_tratamiento"],                                      
            'ip_creacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario, 
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];

        $input2 = [
            'cuadro_clinico' => $request["historia_clinica"],                                       
            'resumen' => $request["resumen"],
            'plan_diagnostico' => $request["plan_diagnostico"],                                       
            'plan_tratamiento' => $request["plan_tratamiento"],                                      
            'hcid' => $id,
            
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];

        $cardiologia = Cardiologia::where('hcid',$id)->first();
        if(!is_null($cardiologia)){
            $cardiologia->update($input2);
        }else{
            Cardiologia::create($input1);
        }  

        return "ok";   

        
    }

    public function agenda($id_agenda,$url_doctor){

        if($this->rol_rec()){
            return response()->view('errors.404');
        } 
        $cardiologos = DB::table('user_espe as ue')
        ->where('ue.espid','8')
        ->join('users as u','u.id','ue.usuid')
        ->where('u.estado','1')
        ->orderBy('u.nombre1')
        ->select('u.*')->get();

        $agenda = DB::table('agenda as a')->where('a.id',$id_agenda)->join('paciente as p','p.id','a.id_paciente')->select('a.*','p.nombre1','p.nombre2','p.apellido1','p.apellido2')->first();

        $fecha_desde = date('Y-m-d',strtotime($agenda->fechaini."- 3 month"));  

        $consulta_cardio = DB::table('agenda as a')->where('a.espid','8')->where('a.estado','1')->join('users as d','d.id','a.id_doctor1')->select('a.*','d.nombre1','d.apellido1')->where('a.id_paciente',$agenda->id_paciente)->where('a.fechaini','>=',$fecha_desde)->orderBy('a.fechaini','desc')->get();
        //dd($consulta_cardio);
        
        $salas = Sala::where('proc_consul_sala','0')->orderBy('id_hospital')->get();
        //dd($salas['0']->hospital()->get());
        
        return view('hc_admision.cardiologia.agenda',['agenda' => $agenda, 'url_doctor' => $url_doctor, 'cardiologos' => $cardiologos, 'salas' => $salas, 'consulta_cardio' => $consulta_cardio]);

    }

    public function calendario(Request $request){

        $fechainicio = date('Y-m-d',strtotime($request['fecha']."- 1 month"));
        $fechafin = date('Y-m-d',strtotime($request['fecha']."+ 1 month"));
        //dd($fechainicio,$fechafin,$request->all());


        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users','agenda.id_usuariocrea','=','users.id')
            ->join('users as um','agenda.id_usuariomod','=','um.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento','users.nombre1 as unombre1','users.apellido1 as uapellido1','um.apellido1 as umapellido1')
            ->where('proc_consul', '=', 1)
            ->whereBetween('fechaini', [$fechainicio, $fechafin])
            ->where(function ($query) use ($request) {
                $query->where([['id_doctor1', '=', $request['id_doctor']], ['agenda.estado', '=', '1']])
                        ->orWhere([['id_doctor2', '=', $request['id_doctor']], ['agenda.estado', '=', '1']])
                        ->orWhere([['id_doctor3', '=', $request['id_doctor']], ['agenda.estado', '=', '1']]);
                })
            ->get();

        $agenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users','agenda.id_usuariocrea','=','users.id')
            ->join('users as um','agenda.id_usuariomod','=','um.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro','users.nombre1 as unombre1','users.apellido1 as uapellido1','um.apellido1 as umapellido1','um.nombre1 as umnombre1')
            ->where('proc_consul', '=', 0)
            ->whereBetween('fechaini', [$fechainicio, $fechafin])
            ->where(function ($query) use ($request) {
                $query->where([['id_doctor1', '=', $request['id_doctor']], ['agenda.estado', '=', '1']])
                        ->orWhere([['id_doctor2', '=', $request['id_doctor']], ['agenda.estado', '=', '1']])
                        ->orWhere([['id_doctor3', '=', $request['id_doctor']], ['agenda.estado', '=', '1']]);
                })
            ->get();    

        $agenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
            ->join('users','agenda.id_usuariocrea','=','users.id')
            ->join('users as um','agenda.id_usuariomod','=','um.id')
            ->select('agenda.*', 'users.nombre1 as unombre1','users.apellido1 as uapellido1','um.apellido1 as umapellido1','um.nombre1 as umnombre1')
            ->where(function ($query) use ($request) {
                $query->where([['id_doctor1', '=', $request['id_doctor']], ['agenda.estado', '=', '1']])
                        ->orWhere([['id_doctor2', '=', $request['id_doctor']], ['agenda.estado', '=', '1']])
                        ->orWhere([['id_doctor3', '=', $request['id_doctor']], ['agenda.estado', '=', '1']]);
                })
            ->whereBetween('fechaini', [$fechainicio, $fechafin])
            ->get();    
        //dd($agenda,$agenda2,$agenda3);

        $horario = DB::table('horario_doctor')
                ->where('id_doctor', '=', $request['id_doctor'])->orderBy('ndia')
                ->orderBy('hora_ini')
                ->get();


        $extra = Excepcion_Horario::where('id_doctor1', '=', $request['id_doctor'])->get();            
        //dd($request->all(),$agendas); 
        $doctor_t = Doctor_Tiempo::where('id_doctor',$request['id_doctor'])->first();
         

        return view('hc_admision.cardiologia.calendar',['fecha' => $request['fecha'], 'agenda' => $agenda, 'agenda2' => $agenda2,  'agenda3' => $agenda3, 'horario' => $horario, 'extra' => $extra, 'paciente' => $request['paciente'], 'url_doctor' => $request['url_doctor'], 'doctor_t' => $doctor_t, 'id_doctor' => $request->id_doctor]);
    }

    public function agendar(Request $request){

        $espid = '8';
        date_default_timezone_set('America/Guayaquil');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $rule = [   'inicio' => 'required', 
                    'fin' => 'required'
                ];

        $msn = [    'inicio.required' => 'Seleccione el horario en la agenda',
                    'fin.required' => 'Seleccione el horario en la agenda',
                ];        

        $this->validate($request,$rule,$msn);
        //dd($request->all());
        $agenda_proc = Agenda::find($request['id_agenda']);

        $this->validateInput3($request);
        $this->validateInput4($request);
        $this->validate_paciente($request,$agenda_proc->id_paciente);

        $input = [
            'fechaini' => $request['inicio'],
            'fechafin' => $request['fin'],
            'id_paciente' => $agenda_proc->id_paciente,
            'id_doctor1' => $request['id_doctor'],
            'proc_consul' => '0',
            'id_sala' => $request['id_sala'],
            'espid' => $espid,
            'tipo_cita' => $agenda_proc->tipo_cita,
            'estado_cita' => '0',
            'observaciones' => $request['observaciones'],
            'est_amb_hos' => $agenda_proc->est_amb_hos,
            'id_seguro' => $agenda_proc->id_seguro,
            'estado' => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'cortesia' => $agenda_proc->cortesia,
            'procedencia' => $agenda_proc->procedencia,
            'paciente_dr' => $agenda_proc->paciente_dr,
            ];

        $especialista = User::find($request['id_doctor']);   
        Log_Agenda::create([
            'id_agenda' => $agenda_proc->id,
            'estado_cita_ant' => $agenda_proc->estado_cita,
            'fechaini_ant' => $agenda_proc->fechaini,
            'fechafin_ant' => $agenda_proc->fechafin,
            'estado_ant' => $agenda_proc->estado,
            'cortesia_ant' => $agenda_proc->cortesia,
            'observaciones_ant' => $agenda_proc->observaciones,
            'id_doctor1_ant' => $agenda_proc->id_doctor1,
            'id_doctor2_ant' => $agenda_proc->id_doctor2,
            'id_doctor3_ant' => $agenda_proc->id_doctor3,
            'id_sala_ant' => $agenda_proc->id_sala,

            'estado_cita' => $agenda_proc->estado_cita,
            'fechaini' => $agenda_proc->fechaini,
            'fechafin' => $agenda_proc->fechafin,
            'estado' => $agenda_proc->estado,
            'cortesia' => $agenda_proc->cortesia,
            'observaciones' => $request['observaciones'],
            'id_doctor1' => '',
            'id_doctor2' => '',
            'id_doctor3' => '',
            'id_sala' => $agenda_proc->id_sala,
            'descripcion' => 'ASIGNA VALORACION CARDIOLOGICA CON DR. '.$especialista->apellido1.' '.$especialista->nombre1,
            'descripcion2' => $request['inicio'].'-'.$request['fin'],
            'descripcion3' => '',
            'campos_ant' => '',
            'campos' => '',
            'id_usuarioconfirma' => $agenda_proc->id_usuarioconfirma,
        
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        ]);      

        $id_agenda = Agenda::insertGetId($input);

        $asignado = Agenda_Proc_Cardiologia::where('id_ag_procedimiento',$agenda_proc->id)->first();

        if(!is_null($asignado)){
            $a_input = [    
                'id_ag_cardiologia' => $id_agenda,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            ];

            $asignado->update($a_input);
        } else{
            $a_input = [
                'id_ag_procedimiento' => $agenda_proc->id,    
                'id_ag_cardiologia' => $id_agenda,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'id_usuariocrea' => $idusuario,
                'ip_creacion' => $ip_cliente,
            ]; 

            Agenda_Proc_Cardiologia::create($a_input);   
        }   


        return $this->agenda($agenda_proc->id,$request['url_doctor']);

    }

    private function  validateInput3($request) {
        $ini2 = date_create($request['inicio']);
        $fin2 = date_create($request['fin']);
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin = date_format($fin, 'Y/m/d H:i:s');
        $dato2 = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin){
                return $query->where('id_doctor1', '=', $request['id_doctor'])
                      ->orWhere('id_doctor2', '=', $request['id_doctor'])
                      ->orWhere('id_doctor3', '=', $request['id_doctor']);
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
            'id_doctor' =>  'unique_doctor:'.$cant_agenda,
            'observaciones' => 'max:200',
            'inicio' =>  'required|date|before:fin',
            'fin' => 'required|date|after:inicio',  
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

    private function validate_paciente($request,$id_paciente){
        $id_paciente = $id_paciente;
        $ini2 = date_create($request['inicio']);
        $fin2 = date_create($request['fin']);
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin = date_format($fin, 'Y/m/d H:i:s');
        $dato2 = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin,$id_paciente){
                return $query->where('id_paciente', '=', $id_paciente);
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
        'inicio' =>  'unique_doctor:'.$cant_agenda,
        ];
        $mensajes = [ 
        'inicio.unique_doctor' => 'El paciente ya posee una cita a esta hora',   
        ];
        $this->validate($request, $rules, $mensajes);

    }

    public function asignacion($id_ag_procedimiento,$id_ag_cardiologia){

        date_default_timezone_set('America/Guayaquil');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $ag_cardiologia = Agenda_Proc_Cardiologia::where('id_ag_procedimiento',$id_ag_procedimiento)->first();
        if(!is_null($ag_cardiologia)){
            $input = [
                'id_ag_cardiologia' => $id_ag_cardiologia,
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
            $ag_cardiologia->update($input);

            return "VALORACION CARDIOLOGICA ACTUALIZADA";
        }

        return "VALORACION NO ACTUALIZADA";

    }
    
}
