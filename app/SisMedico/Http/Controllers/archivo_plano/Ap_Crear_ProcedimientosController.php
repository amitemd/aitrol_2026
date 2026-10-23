<?php

namespace Sis_medico\Http\Controllers\archivo_plano;

use Carbon\Carbon;
use Cookie;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel_Worksheet_Drawing;
use Response;
use Sis_medico\Paciente;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Log_Agenda;
use Sis_medico\Historiaclinica;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\Hc_Log;
use Sis_medico\Http\Controllers\Controller;


class Ap_Crear_ProcedimientosController extends Controller
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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 11, 22)) == false) {
            return true;
        }
    }

    //Crea Procedimiento Funcionales MANOMETRÍA ESOFAGICA
    public function crear_proced_mano_esofagica(Request $request){

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_paciente =  $request['id_empl'];
        //return $id_paciente;

        return view('archivo_plano/procedimientos/proce_mano_esof', ['id_paciente' => $id_paciente]);

    }

    //Crea Procedimiento Funcionales MANOMETRÍA ANORECTAL
    public function crear_proced_mano_anorectal(Request $request){

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_paciente =  $request['id_empl'];

        return view('archivo_plano/procedimientos/proce_mano_anor', ['id_paciente' => $id_paciente]);

    }




    //Crea Procedimiento Funcionales PH-METRIA
    public function crear_proced_ph_metria(Request $request){

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_paciente =  $request['id_empl'];

        return view('archivo_plano/procedimientos/proce_ph_metria', ['id_paciente' => $id_paciente]);

    }


    //Store Procedimiento Funcionales MANOMETRÍA ESOFAGICA
    public function store_procedimiento_funcional_mano_esofagica(Request $request){
    
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id; 


        $fecha_creacion =  $request['fecha_creacion'];

        $id_paciente =  $request['id_paciente'];

        $procedimientop =  2;
        
        $paciente = Paciente::find($id_paciente);

        //Creacion en la tabla Hc_Log
        $procedimiento_fun_crear_new = [

            'anterior' => 'PROC_FUNCIONAL -> El Dr. creo nuevo procedimiento funcional',
            'nuevo' => 'PROC_FUNCIONAL -> El Dr. creo nuevo procedimiento funcional',
            'id_paciente' => $paciente->id,
            'id_usuariomod' => $id_doctor,
            'id_usuariocrea' => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        
        ];


        Hc_Log::create($procedimiento_fun_crear_new);

        if($procedimientop!=null){

            $input_agenda = [
              //'fechaini' => Date('Y-m-d H:i:s'),
              //'fechafin' => Date('Y-m-d H:i:s'),
              'fechaini' => $fecha_creacion,
              'fechafin' => $fecha_creacion,
              'id_paciente' => $paciente->id,
              'id_doctor1' => 9666666666,
              'proc_consul' => '4',
              'estado_cita' => '4',
              'id_empresa'  => '0992704152001',
              'espid' => '4',
              'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'id_seguro' => $paciente->id_seguro,
              'estado' => '4',
              'ip_creacion' => $ip_cliente,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea' => $id_doctor,
              'id_usuariomod' => $id_doctor,
              'id_procedimiento' => $procedimientop,
              'id_sala' => '10',
            ];

            $id_agenda = Agenda::insertGetId($input_agenda);

            //Insercion en la Tabla Agenda Procedimiento
            AgendaProcedimiento::create([
                
                'id_agenda' => $id_agenda,
                'id_procedimiento' => $procedimientop,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor
            
            ]);

            
            //Insercion en la Tabla
            $input_log = [
              'id_agenda' => $id_agenda,
              'estado_cita_ant' => '0',
              'estado_cita' => '0',
              'fechaini' => Date('Y-m-d H:i:s'),
              'fechafin' => Date('Y-m-d H:i:s'),
              'estado' => '4',
              'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'id_doctor1' => 9666666666,
              'descripcion' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'campos_ant' => 'PRO: '.$procedimientop,
              'id_usuariomod' => $id_doctor,
              'id_usuariocrea' => $id_doctor,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,           
            ];


            Log_Agenda::create($input_log);

            $idusuario = $id_doctor;

            //Insercion en la Tabla 
            $input_historia = [
              
              'parentesco' => $paciente->parentesco,
              'id_usuario' => $paciente->id_usuario,
              'id_agenda' => $id_agenda,
              'id_paciente' => $paciente->id,
              'id_seguro' => $paciente->id_seguro,
              'id_doctor1' => 9666666666,
              'id_usuariocrea' => $id_doctor,
              'ip_modificacion' => $ip_cliente,
              'id_usuariomod' => $id_doctor,
              'ip_creacion' => $ip_cliente,
              
            ]; 


            $id_historia=Historiaclinica::insertGetId($input_historia); 


            //Insercion en la Tabla Pentax
            $input_pentax = [
              
              'id_agenda' => $id_agenda,
              'hcid' => $id_historia,
              'id_sala' => '10',
              'id_doctor1' => 9666666666,
              'id_seguro' => $paciente->id_seguro,
              'observacion' => "PROCEDIMIENTO CREADO POR EL DOCTOR",
              'id_usuariocrea' => $idusuario,
              'ip_modificacion' => $ip_cliente,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,

            ];

            $id_pentax=Pentax::insertGetId($input_pentax);


            //Insercion en la Tabla PentaxProc
            $input_pentax_pro2 = [
                
                'id_pentax' => $id_pentax,
                'id_procedimiento' => $procedimientop,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
            
            ];

            PentaxProc::create($input_pentax_pro2);

            $input_log_px=[
              
              'id_pentax' => $id_pentax,
              'tipo_cambio' => "CREADO POR EL DOCTOR",
              'descripcion' => "EN ESPERA",
              'estado_pentax' => '0',
              'procedimientos' => $procedimientop,
              'id_doctor1' => 9666666666,
              'observacion' => "CREADO POR EL DOCTOR",
              'id_sala' => '10',
              'id_seguro' => $paciente->id_seguro,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,
              'id_usuariomod' => $idusuario,
              'id_usuariocrea' => $idusuario,
            
            ]; 

            Pentax_log::create($input_log_px); 
            
            
            //Inserta en la Tabla hc_procedimientos
            $input_hc_procedimiento = [
              
              'id_hc' => $id_historia,
              'id_seguro' => $paciente->id_seguro,
              'id_doctor_examinador' => 9666666666,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea' => $idusuario,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,
              
            ]; 

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

            //Inserta en la Tabla hc_protocolo
            $input_hc_protocolo = [
              
              'id_hc_procedimientos' => $id_hc_procedimiento,
              'hallazgos' => 'Ingresado por Convenios Públicos',
              'hora_inicio' => date('H:i:s'),
              'hora_fin' => date('H:i:s'),
              'estado_final' => ' ',
              'ip_modificacion' => $ip_cliente,
              'hcid' => $id_historia,
              'id_usuariocrea' => $idusuario,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
              'tipo_procedimiento' => 1,
            
            ]; 
            
            hc_protocolo::insert($input_hc_protocolo);


            $input_pro_final = [
                
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'id_procedimiento' => $procedimientop,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
            
            ];

            
            Hc_Procedimiento_Final::create($input_pro_final);


            return "ok";    

        
        }


        return "Ingrese el Procedimiento";

    
    }

    //Store Procedimiento Funcionales MANOMETRÍA ANORECTAL
    // 20 MANOMETRIA ANORECTAL
    public function store_procedimiento_funcional_mano_anorect(Request $request){


      $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id; 


        $fecha_creacion =  $request['fecha_creacion'];

        $id_paciente =  $request['id_paciente'];

        $procedimientop =  20;
        
        $paciente = Paciente::find($id_paciente);

        //Creacion en la tabla Hc_Log
        $procedimiento_fun_crear_new = [

            'anterior' => 'PROC_FUNCIONAL -> El Dr. creo nuevo procedimiento funcional',
            'nuevo' => 'PROC_FUNCIONAL -> El Dr. creo nuevo procedimiento funcional',
            'id_paciente' => $paciente->id,
            'id_usuariomod' => $id_doctor,
            'id_usuariocrea' => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        
        ];


        Hc_Log::create($procedimiento_fun_crear_new);

        if($procedimientop!=null){

            $input_agenda = [
              //'fechaini' => Date('Y-m-d H:i:s'),
              //'fechafin' => Date('Y-m-d H:i:s'),
              'fechaini' => $fecha_creacion,
              'fechafin' => $fecha_creacion,
              'id_paciente' => $paciente->id,
              'id_doctor1' => 9666666666,
              'proc_consul' => '4',
              'estado_cita' => '4',
              'id_empresa'  => '0992704152001',
              'espid' => '4',
              'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'id_seguro' => $paciente->id_seguro,
              'estado' => '4',
              'ip_creacion' => $ip_cliente,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea' => $id_doctor,
              'id_usuariomod' => $id_doctor,
              'id_procedimiento' => $procedimientop,
              'id_sala' => '10',
            ];

            $id_agenda = Agenda::insertGetId($input_agenda);

            //Insercion en la Tabla Agenda Procedimiento
            AgendaProcedimiento::create([
                
                'id_agenda' => $id_agenda,
                'id_procedimiento' => $procedimientop,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor
            
            ]);

            
            //Insercion en la Tabla
            $input_log = [
              'id_agenda' => $id_agenda,
              'estado_cita_ant' => '0',
              'estado_cita' => '0',
              'fechaini' => Date('Y-m-d H:i:s'),
              'fechafin' => Date('Y-m-d H:i:s'),
              'estado' => '4',
              'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'id_doctor1' => 9666666666,
              'descripcion' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'campos_ant' => 'PRO: '.$procedimientop,
              'id_usuariomod' => $id_doctor,
              'id_usuariocrea' => $id_doctor,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,           
            ];


            Log_Agenda::create($input_log);

            $idusuario = $id_doctor;

            //Insercion en la Tabla 
            $input_historia = [
              
              'parentesco' => $paciente->parentesco,
              'id_usuario' => $paciente->id_usuario,
              'id_agenda' => $id_agenda,
              'id_paciente' => $paciente->id,
              'id_seguro' => $paciente->id_seguro,
              'id_doctor1' => 9666666666,
              'id_usuariocrea' => $id_doctor,
              'ip_modificacion' => $ip_cliente,
              'id_usuariomod' => $id_doctor,
              'ip_creacion' => $ip_cliente,
              
            ]; 


            $id_historia=Historiaclinica::insertGetId($input_historia); 


            //Insercion en la Tabla Pentax
            $input_pentax = [
              
              'id_agenda' => $id_agenda,
              'hcid' => $id_historia,
              'id_sala' => '10',
              'id_doctor1' => 9666666666,
              'id_seguro' => $paciente->id_seguro,
              'observacion' => "PROCEDIMIENTO CREADO POR EL DOCTOR",
              'id_usuariocrea' => $idusuario,
              'ip_modificacion' => $ip_cliente,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,

            ];

            $id_pentax=Pentax::insertGetId($input_pentax);


            //Insercion en la Tabla PentaxProc
            $input_pentax_pro2 = [
                
                'id_pentax' => $id_pentax,
                'id_procedimiento' => $procedimientop,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
            
            ];

            PentaxProc::create($input_pentax_pro2);

            $input_log_px=[
              
              'id_pentax' => $id_pentax,
              'tipo_cambio' => "CREADO POR EL DOCTOR",
              'descripcion' => "EN ESPERA",
              'estado_pentax' => '0',
              'procedimientos' => $procedimientop,
              'id_doctor1' => 9666666666,
              'observacion' => "CREADO POR EL DOCTOR",
              'id_sala' => '10',
              'id_seguro' => $paciente->id_seguro,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,
              'id_usuariomod' => $idusuario,
              'id_usuariocrea' => $idusuario,
            
            ]; 

            Pentax_log::create($input_log_px); 
            
            
            //Inserta en la Tabla hc_procedimientos
            $input_hc_procedimiento = [
              
              'id_hc' => $id_historia,
              'id_seguro' => $paciente->id_seguro,
              'id_doctor_examinador' => 9666666666,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea' => $idusuario,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,
              
            ]; 

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

            //Inserta en la Tabla hc_protocolo
            $input_hc_protocolo = [
              
              'id_hc_procedimientos' => $id_hc_procedimiento,
              'hallazgos' => 'Ingresado por Convenios Públicos',
              'hora_inicio' => date('H:i:s'),
              'hora_fin' => date('H:i:s'),
              'estado_final' => ' ',
              'ip_modificacion' => $ip_cliente,
              'hcid' => $id_historia,
              'id_usuariocrea' => $idusuario,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
              'tipo_procedimiento' => 1,
            
            ]; 
            
            hc_protocolo::insert($input_hc_protocolo);


            $input_pro_final = [
                
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'id_procedimiento' => $procedimientop,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
            
            ];

            
            Hc_Procedimiento_Final::create($input_pro_final);


            return "ok";    

        
        }


        return "Ingrese el Procedimiento";



        

    }

    //Store Procedimiento Funcionales PH-METRIA
    // 1  PH-METRÍA
    public function store_procedimiento_funcional_ph_metria(Request $request){


        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id; 


        $fecha_creacion =  $request['fecha_creacion'];

        $id_paciente =  $request['id_paciente'];

        $procedimientop =  1;
        
        $paciente = Paciente::find($id_paciente);

        //Creacion en la tabla Hc_Log
        $procedimiento_fun_crear_new = [

            'anterior' => 'PROC_FUNCIONAL -> El Dr. creo nuevo procedimiento funcional',
            'nuevo' => 'PROC_FUNCIONAL -> El Dr. creo nuevo procedimiento funcional',
            'id_paciente' => $paciente->id,
            'id_usuariomod' => $id_doctor,
            'id_usuariocrea' => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        
        ];


        Hc_Log::create($procedimiento_fun_crear_new);

        if($procedimientop!=null){

            $input_agenda = [
              //'fechaini' => Date('Y-m-d H:i:s'),
              //'fechafin' => Date('Y-m-d H:i:s'),
              'fechaini' => $fecha_creacion,
              'fechafin' => $fecha_creacion,
              'id_paciente' => $paciente->id,
              'id_doctor1' => 9666666666,
              'proc_consul' => '4',
              'estado_cita' => '4',
              'id_empresa'  => '0992704152001',
              'espid' => '4',
              'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'id_seguro' => $paciente->id_seguro,
              'estado' => '4',
              'ip_creacion' => $ip_cliente,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea' => $id_doctor,
              'id_usuariomod' => $id_doctor,
              'id_procedimiento' => $procedimientop,
              'id_sala' => '10',
            ];

            $id_agenda = Agenda::insertGetId($input_agenda);

            //Insercion en la Tabla Agenda Procedimiento
            AgendaProcedimiento::create([
                
                'id_agenda' => $id_agenda,
                'id_procedimiento' => $procedimientop,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor
            
            ]);

            
            //Insercion en la Tabla
            $input_log = [
              'id_agenda' => $id_agenda,
              'estado_cita_ant' => '0',
              'estado_cita' => '0',
              'fechaini' => Date('Y-m-d H:i:s'),
              'fechafin' => Date('Y-m-d H:i:s'),
              'estado' => '4',
              'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'id_doctor1' => 9666666666,
              'descripcion' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'campos_ant' => 'PRO: '.$procedimientop,
              'id_usuariomod' => $id_doctor,
              'id_usuariocrea' => $id_doctor,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,           
            ];


            Log_Agenda::create($input_log);

            $idusuario = $id_doctor;

            //Insercion en la Tabla 
            $input_historia = [
              
              'parentesco' => $paciente->parentesco,
              'id_usuario' => $paciente->id_usuario,
              'id_agenda' => $id_agenda,
              'id_paciente' => $paciente->id,
              'id_seguro' => $paciente->id_seguro,
              'id_doctor1' => 9666666666,
              'id_usuariocrea' => $id_doctor,
              'ip_modificacion' => $ip_cliente,
              'id_usuariomod' => $id_doctor,
              'ip_creacion' => $ip_cliente,
              
            ]; 


            $id_historia = Historiaclinica::insertGetId($input_historia); 


            //Insercion en la Tabla Pentax
            $input_pentax = [
              
              'id_agenda' => $id_agenda,
              'hcid' => $id_historia,
              'id_sala' => '10',
              'id_doctor1' => 9666666666,
              'id_seguro' => $paciente->id_seguro,
              'observacion' => "PROCEDIMIENTO CREADO POR EL DOCTOR",
              'id_usuariocrea' => $idusuario,
              'ip_modificacion' => $ip_cliente,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,

            ];

            $id_pentax=Pentax::insertGetId($input_pentax);


            //Insercion en la Tabla PentaxProc
            $input_pentax_pro2 = [
                
                'id_pentax' => $id_pentax,
                'id_procedimiento' => $procedimientop,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
            
            ];

            PentaxProc::create($input_pentax_pro2);

            $input_log_px=[
              
              'id_pentax' => $id_pentax,
              'tipo_cambio' => "CREADO POR EL DOCTOR",
              'descripcion' => "EN ESPERA",
              'estado_pentax' => '0',
              'procedimientos' => $procedimientop,
              'id_doctor1' => 9666666666,
              'observacion' => "CREADO POR EL DOCTOR",
              'id_sala' => '10',
              'id_seguro' => $paciente->id_seguro,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,
              'id_usuariomod' => $idusuario,
              'id_usuariocrea' => $idusuario,
            
            ]; 

            Pentax_log::create($input_log_px); 


            //Inserta en la Tabla hc_procedimientos
            $input_hc_procedimiento = [
              
              'id_hc' => $id_historia,
              'id_seguro' => $paciente->id_seguro,
              'id_doctor_examinador' => 9666666666,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea' => $idusuario,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,
              
            ]; 

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

            //Inserta en la Tabla hc_protocolo
            $input_hc_protocolo = [
              
              'id_hc_procedimientos' => $id_hc_procedimiento,
              'hallazgos' => 'Ingresado por Convenios Públicos',
              'hora_inicio' => date('H:i:s'),
              'hora_fin' => date('H:i:s'),
              'estado_final' => ' ',
              'ip_modificacion' => $ip_cliente,
              'hcid' => $id_historia,
              'id_usuariocrea' => $idusuario,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
              'tipo_procedimiento' => 1,
            
            ]; 
            
            hc_protocolo::insert($input_hc_protocolo);


            $input_pro_final = [
                
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'id_procedimiento' => $procedimientop,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
            
            ];

            
            Hc_Procedimiento_Final::create($input_pro_final);

            
            return "ok";    

        
        }


        return "Ingrese el Procedimiento";

    }



    /*public function store_procedimiento_funcional_ph_metria(Request $request){

      
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id; 


        $fecha_creacion =  $request['fecha_creacion'];

        $id_paciente =  $request['id_paciente'];

        $procedimientop =  1;
        
        $paciente = Paciente::find($id_paciente);

        //Creacion en la tabla Hc_Log
        $procedimiento_fun_crear_new = [

            'anterior' => 'PROC_FUNCIONAL -> El Dr. creo nuevo procedimiento funcional',
            'nuevo' => 'PROC_FUNCIONAL -> El Dr. creo nuevo procedimiento funcional',
            'id_paciente' => $paciente->id,
            'id_usuariomod' => $id_doctor,
            'id_usuariocrea' => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        
        ];


        Hc_Log::create($procedimiento_fun_crear_new);

        if($procedimientop!=null){

            $input_agenda = [
              //'fechaini' => Date('Y-m-d H:i:s'),
              //'fechafin' => Date('Y-m-d H:i:s'),
              'fechaini' => $fecha_creacion,
              'fechafin' => $fecha_creacion,
              'id_paciente' => $paciente->id,
              'id_doctor1' => 9666666666,
              'proc_consul' => '4',
              'estado_cita' => '4',
              'id_empresa'  => '0992704152001',
              'espid' => '4',
              'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'id_seguro' => $paciente->id_seguro,
              'estado' => '4',
              'ip_creacion' => $ip_cliente,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea' => $id_doctor,
              'id_usuariomod' => $id_doctor,
              'id_procedimiento' => $procedimientop,
              'id_sala' => '10',
            ];

            $id_agenda = agenda::insertGetId($input_agenda);

            //Insercion en la Tabla Agenda Procedimiento
            AgendaProcedimiento::create([
                
                'id_agenda' => $id_agenda,
                'id_procedimiento' => $procedimientop,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor
            
            ]);

            
            //Insercion en la Tabla
            $input_log = [
              'id_agenda' => $id_agenda,
              'estado_cita_ant' => '0',
              'estado_cita' => '0',
              'fechaini' => Date('Y-m-d H:i:s'),
              'fechafin' => Date('Y-m-d H:i:s'),
              'estado' => '4',
              'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'id_doctor1' => 9666666666,
              'descripcion' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'campos_ant' => 'PRO: '.$procedimientop,
              'id_usuariomod' => $id_doctor,
              'id_usuariocrea' => $id_doctor,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,           
            ];


            Log_agenda::create($input_log);

            $idusuario = $id_doctor;

            //Insercion en la Tabla 
            $input_historia = [
              
              'parentesco' => $paciente->parentesco,
              'id_usuario' => $paciente->id_usuario,
              'id_agenda' => $id_agenda,
              'id_paciente' => $paciente->id,
              'id_seguro' => $paciente->id_seguro,
              'id_doctor1' => 9666666666,
              'id_usuariocrea' => $id_doctor,
              'ip_modificacion' => $ip_cliente,
              'id_usuariomod' => $id_doctor,
              'ip_creacion' => $ip_cliente,
              
            ]; 


            $id_historia=Historiaclinica::insertGetId($input_historia); 


            //Insercion en la Tabla Pentax
            $input_pentax = [
              
              'id_agenda' => $id_agenda,
              'hcid' => $id_historia,
              'id_sala' => '10',
              'id_doctor1' => 9666666666,
              'id_seguro' => $paciente->id_seguro,
              'observacion' => "PROCEDIMIENTO CREADO POR EL DOCTOR",
              'id_usuariocrea' => $idusuario,
              'ip_modificacion' => $ip_cliente,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,

            ];

            $id_pentax=Pentax::insertGetId($input_pentax);


            //Insercion en la Tabla PentaxProc
            $input_pentax_pro2 = [
                
                'id_pentax' => $id_pentax,
                'id_procedimiento' => $procedimientop,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
            
            ];

            PentaxProc::create($input_pentax_pro2);

            $input_log_px=[
              
              'id_pentax' => $id_pentax,
              'tipo_cambio' => "CREADO POR EL DOCTOR",
              'descripcion' => "EN ESPERA",
              'estado_pentax' => '0',
              'procedimientos' => $procedimientop,
              'id_doctor1' => 9666666666,
              'observacion' => "CREADO POR EL DOCTOR",
              'id_sala' => '10',
              'id_seguro' => $paciente->id_seguro,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,
              'id_usuariomod' => $idusuario,
              'id_usuariocrea' => $idusuario,
            
            ]; 

            Pentax_log::create($input_log_px); 
            
            
            //Inserta en la Tabla hc_procedimientos
            $input_hc_procedimiento = [
              
              'id_hc' => $id_historia,
              'id_seguro' => $paciente->id_seguro,
              'id_doctor_examinador' => 9666666666,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea' => $idusuario,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,
              
            ]; 

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);


            //Inserta en la Tabla hc_protocolo
            $input_hc_protocolo = [
              
              'id_hc_procedimientos' => $id_hc_procedimiento,
              'hora_inicio' => date('H:i:s'),
              'hora_fin' => date('H:i:s'),
              'estado_final' => ' ',
              'ip_modificacion' => $ip_cliente,
              'hcid' => $id_historia,
              'id_usuariocrea' => $idusuario,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,
              'created_at' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
              'tipo_procedimiento' => 1,
            
            ]; 
            
            hc_protocolo::insert($input_hc_protocolo);


            $input_pro_final = [
                
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'id_procedimiento' => $procedimientop,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
            
            ];

            
            Hc_Procedimiento_Final::create($input_pro_final);


            return "ok";    
        
    

    }*/


}
