<?php

namespace Sis_medico\Http\Controllers\bo;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\User;
use Sis_medico\Paciente;
use Sis_medico\Excepcion_Horario;

use Sis_medico\Bo_Solicitud;
use Sis_medico\Bo_Estado;
use Sis_medico\Seguro;
use Sis_medico\Sala;
use Sis_medico\Pais;


use Excel;
use laravel\laravel;
use Carbon\Carbon;
use Sis_medico\Http\Controllers\AgendaController;
use Sis_medico\Http\Controllers\HorarioController;
use Sis_medico\Http\Controllers\hc_admision\ControlDocController;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Historiaclinica;
use Sis_medico\Examen_Orden;
use Sis_medico\Procedimiento;
use Sis_medico\Empresa;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Agenda_archivo;
use Sis_medico\Hospital;
use Sis_medico\Log_agenda;
use Sis_medico\Especialidad;
use Sis_medico\Archivo_historico;
use Sis_medico\Opcion_Usuario;
use Sis_medico\hc_procedimientos;
use Sis_medico\PentaxProc;

use Mail;





class SolicitudController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol_new($opcion){ //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
        //dd($opcion);
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          
          return true;
        
        }

    }

    public function  index(){  

        if($this->rol()){
            return response()->view('errors.404');
        }

        

        return view('bo/index');
    }

    public function  listado(){  

        if($this->rol()){
            return response()->view('errors.404');
        }

        $desde = date('Y-m-d');
        $hasta = date('Y-m-d');
        $nombres = null;
        
        $solicitudes = Bo_Solicitud::where('estado','1')->paginate(20);
        $estados = Bo_Estado::all();
        //dd($solicitudes);

        return view('bo/listado', [ 'solicitudes' => $solicitudes, 'desde' => $desde, 'hasta' => $hasta, 'nombres' => $nombres, 'estados' => $estados, 'id_estado' => null]);
    }

    public function  data(){  

        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $solicitud = Bo_Solicitud::where('estado','1')->get()->last();
        $estados = Bo_Estado::all();
        //return $solicitud;

        return view('bo/datos', [ 'solicitud' => $solicitud]);
    }

    public function  empresa_editar($id){  

        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $empresa = Empresa::find($id);
        //dd($empresa);

        return view('contable/facturacion/editar_empresa', [ 'empresa' => $empresa]);
    }

    public function  facturas($id){  

        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $desde = date('Y-m-d');
        $hasta = date('Y-m-d');
        $empresa = Empresa::find($id);
        $empresas = Empresa::where('id','<>','9999999999')->where('id','<>',$id)->get();
        $facturas = Factura_Cabecera::where('id_empresa',$id)->paginate(20);
        $seguros = Seguro::where('inactivo','1')->get();

        return view('contable/facturacion/index', [ 'facturas' => $facturas, 'empresa' => $empresa, 'empresas' => $empresas, 'seguros' => $seguros, 'cedula' => null, 'nombres' => null, 'factura' => null, 'id_seguro' => null, 'suc' => null, 'caj' => null, 'desde' => $desde, 'hasta' => $hasta]);
    }

    public function  factura_crear($id){  

        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $empresa = Empresa::find($id);
        $seguros = Seguro::where('inactivo','1')->get();

        return view('contable/facturacion/crear_factura', [ 'empresa' => $empresa, 'seguros' => $seguros]);
    }

    public function  factura_store(Request $request){  

        if($this->rol()){
            return response()->view('errors.404');
        }

        $rules = [
            'suc' => 'required',
            'caj' => 'required',
            'factura' => 'required'
        ];

        $msn = [
            'suc.required' => 'Ingresar Sucursal',
            'caj.required' => 'Ingresar Caja',
            'factura.required' => 'Ingresar Factura'
        ];

        $this->validate($request, $rules, $msn);

        $factura_nro = (int)$request->factura;
        //valida factura unica
        $factura_uni = $this->existe_factura($request->suc,$request->caj,$factura_nro);
        
        $rules = [
            'factura' => 'comparamayor:'.$factura_uni.',1',
        ];

        $msn = [
           
            'factura.comparamayor' => 'Factura ya ingresada'
        ];

        return $this->validate($request, $rules, $msn);

        return "ok";
    }

    private function existe_factura($suc, $caj, $factura){

        $cantidad = Factura_Cabecera::where('sucursal',$suc)->where('caja',$caj)->where('numero',$factura)->get()->count();
        if($cantidad>0){
            return 1;
        }

        return 0;
    
    }

    public function agenda()
    {
        
        $opcion = '4'; //AGENDA DE SEGUROS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }

        $users = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->join('doctor_tiempo as dt', 'dt.id_doctor', 'users.id')->orderBy('tipo_documento','asc')->select('users.*','dt.id_doctor')->get(); //3=DOCTORES STAFF
        

        return view('bo/agenda', ['users' => $users]);
    }

 

    public function calendario($id, $fecha)
    {
        $opcion = '4'; //AGENDA DE SEGUROS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }

        $users = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->join('doctor_tiempo as dt', 'dt.id_doctor', 'users.id')->orderBy('tipo_documento','asc')->select('users.*','dt.id_doctor')->where('users.id','<>',$id)->get(); //3=DOCTORES STAFF
        // Redirect to user list if updating user wasn't existed
       
        $horario = DB::table('horario_doctor')
                ->where('id_doctor', '=', $id)
                ->orderBy('ndia')
                ->orderBy('hora_ini')
                ->get();
        $doctor = User::find($id); 
        

        date_default_timezone_set('UTC');
        if($fecha=='0'){
            $fecha2 = date('Y/m/d H:i');    
        }else{
            $fecha  = substr($fecha, 0,10);
            $fecha2 = date('Y/m/d H:i', $fecha);
        }
        
        //dd($fecha2);
        $nuevafecha = strtotime ( '-1 month' , strtotime ($fecha2) );

        $bfecha = date ( 'Y-m-j' , $nuevafecha );

        date_default_timezone_set('America/Guayaquil');
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users','agenda.id_usuariocrea','=','users.id')
            ->join('users as um','agenda.id_usuariomod','=','um.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento','users.nombre1 as unombre1','users.apellido1 as uapellido1','um.apellido1 as umapellido1','um.nombre1 as umnombre1')
            ->where('proc_consul', '=', 1)
            ->where('fechaini', '>=', $bfecha)
                        ->where(function ($query) use ($id) {
                            $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
                            })
            ->get();    

        $agenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users','agenda.id_usuariocrea','=','users.id')
            ->join('users as um','agenda.id_usuariomod','=','um.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro','users.nombre1 as unombre1','users.apellido1 as uapellido1','um.apellido1 as umapellido1','um.nombre1 as umnombre1','seguros.tipo as stipo')
            ->where('proc_consul', '=', 0)
            ->where('fechaini', '>=', $bfecha)
                        ->where(function ($query) use ($id) {
                            $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
                            })
            ->get();    

        $agenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
                    ->join('users','agenda.id_usuariocrea','=','users.id')
                    ->join('users as um','agenda.id_usuariomod','=','um.id')
                    ->select('agenda.*', 'users.nombre1 as unombre1','users.apellido1 as uapellido1','um.apellido1 as umapellido1','um.nombre1 as umnombre1')

            ->where('fechaini', '>=', $bfecha)
                        ->where(function ($query) use ($id) {
                            $query->where([['agenda.id_doctor1', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['agenda.id_doctor2', '=', $id], ['agenda.estado', '=', '1']])
                                    ->orWhere([['agenda.id_doctor3', '=', $id], ['agenda.estado', '=', '1']]);
                            })
            ->get();

        
        //horas extras aceptadas    
        $aceptadas_extra = Excepcion_Horario::where('id_doctor1', '=', $id)->get(); 

        return view('bo/calendario', ['users' => $users, 'id' => $id,  'doctor' => $doctor, 'agenda' => $agenda, 'agenda2' => $agenda2,  'agenda3' => $agenda3, 'fecha' => $fecha, 'horario' => $horario, 'extra' => $aceptadas_extra]);
    }

    public function agendar($id, $fecha, $i)
    {
        $opcion = '4'; //AGENDA DE SEGUROS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }

        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3],['id', '=', $id],])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
         if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $doctor = User::find($id); 
        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital', 'hospital.direccion as direccion_hospital')->orderBy('hospital.nombre_hospital')
            ->get();  

        $paciente = Paciente::find($i);


        //SI NO SE ENCUENTRA EL PACIENTE
        if ($paciente==array() && $i!='0'){
            
            return  redirect()->route('solicitud.paciente', ['id' => $id, 'i' => $i, 'fecha' => $fecha]); 
        }

        
        //$cortesia_paciente = Cortesia_paciente::find($i); 
        $cortesia_paciente = [];

        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3]])->get();//3=DOCTORES;
        $enfermero = DB::table('users')->where([['id_tipo_usuario', '=', 6]])->get(); //6=ENFERMEROS;
        // Redirect to user list if updating user wasn't existed
         if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        //$procedimiento = Procedimiento::all();
        $procedimiento = [];
        //$empresa = Empresa::all();
        $empresa = []; 
        $seguros = Seguro::where('tipo','<>','0')->where('inactivo','1')->get();

        $especialidad = DB::table('user_espe')->join('especialidad', 'user_espe.espid', '=', 'especialidad.id')
            ->select('user_espe.*', 'especialidad.nombre as enombre')->where('usuid', '=', $id)->get();

        date_default_timezone_set('UTC');

        $fecha  = substr($fecha, 0,10);
        $fecha2 = date('Y/m/d H:i', $fecha);
        $n_dia = date('N', $fecha);
        $hora = date('H:i', $fecha);
        $hora = date('H:i',strtotime('+1 minute',strtotime($hora)));
        
        
        $tipo_horario = DB::select("SELECT tipo 
                                    FROM horario_doctor
                                    Where id_doctor = '".$id."' AND 
                                    ndia = '".$n_dia."' AND
                                    '".$hora."' BETWEEN hora_ini AND hora_fin ; ");

        //dd($id,$n_dia,$hora,$tipo_horario);

        if($tipo_horario != Array())
        {
            $tipo_horario =  $tipo_horario[0]->tipo;
        }
        else
        {
            
            //$tipo_horario = 0;
            $tipo_horario2 = DB::select("SELECT tipo 
                                    FROM excepcion_horario
                                    Where id_doctor1 = '".$id."' AND
                                    '".$fecha2."' BETWEEN inicio AND fin ; ");

            if($tipo_horario2 != Array()){

                $tipo_horario=$tipo_horario2[0]->tipo;

            }else{

                $tipo_horario = -1;

            }

        }


        $citas= array();
        if(!is_null($paciente)){

            $agendacontroller = new AgendaController(); 
            $citas = $agendacontroller->busca_citasxpaciente_dia_mes($fecha2,$paciente->id); 
             
        }
      //  $doctor_todo = Sis_medico\Doctor_Tiempo::where('id_doctor',$doctor->id)->first();
        

        return view('bo/agendar', ['users' => $user, 'id' => $id, 'salas' => $salas, 'doctor' => $doctor, 'paciente' => $paciente, 'procedimiento' => $procedimiento, 'procedimiento2' => $procedimiento, 'i' => $i, 'especialidad' => $especialidad, 'empresa' => $empresa, 'enfermero' => $enfermero, 'seguros' => $seguros, 'hora' => $fecha2, 'unix' => $fecha, 'cortesia_paciente' => $cortesia_paciente, 'citas' => $citas, 'tipo_horario' => $tipo_horario]);
    }

    public function guarda_agenda(Request $request)
    {

        date_default_timezone_set('America/Guayaquil');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $estado_cita = 0;
        $estado  =1;
        //cortesia corregido
        $cortesia="NO";
        $omni = null;
        $cant_cort=0;

        $cuenta  =  DB::table('agenda')->where('id_paciente', '=', $request['id_paciente'])->count();       
        if($cuenta  == '0'){
            $tipo_cita = 0;
        }
        else
        {
            $tipo_cita = 1;
            
        }
        $valor = $request['proc_consul'];

        $fecha = date('Y-m-d H:i');
        $procedimientos =  $request['procedimiento'];
        $procedimientop = $procedimientos[0];
        
         
        //12/1/2018 validacion de sala ocupada
        $idhospital=Sala::find($request['id_sala'])->id_hospital;
        if($idhospital=='2'){
            //$this->valida_salaPentax($request,'0','0');    
        }//--
        
        $this->validateInput3($request);
        
        $this->validateInput4($request);
        
        if($valor != 2){
            
            //dd($request['tipo_horario']);
            if($request['tipo_horario']!=-1){

                //valida horario del doctor
                $horariocontroller = new HorarioController();
                $cantidad_horarios = $horariocontroller->valida_horarioxdoctor_dia($request);

            }else{

                $rules_e = ['observaciones' => 'required'];
                $mensajes_e = ['observaciones.required' => 'Debe ingresar una observación'];
                $this->validate($request,$rules_e,$mensajes_e);
            }
            
            $this->validateInput6($request);
            $this->validate_paciente($request);
            $this->validateMax1($request);
            $paciente = Paciente::find($request['id_paciente']);
            /* cortesias 
            if(!is_null($paciente)){
                $cortesia_paciente = Cortesia_paciente::find($paciente->id);

                if(!is_null($cortesia_paciente)){
                      $cortesia=$cortesia_paciente->cortesia;  
                      if($cortesia_paciente->cortesia=="SI" && $cortesia_paciente->ilimitado=="NO"){
                        
                        $cant_cort = $this->cuenta_cortesias($request['inicio'],$request['id_doctor1']);
                        $this->validateCortesias($request,$cant_cort);
                      }  
                }
            }*/

            $usuario_prin = User::find($paciente->id_usuario);
            $correo =  $usuario_prin->email;

        }

        if($valor == 0){

            $input_historia = [
                'fechaini' => $request['inicio'],
                'fechafin' => $request['fin'],
                'id_paciente' => $request['id_paciente'],
                'id_doctor1' => $request['id_doctor1'],
                'proc_consul' => $request['proc_consul'],
                'id_sala' => $request['id_sala'],
                'espid' => $request['espid'],
                'tipo_cita' => $request['tipo_cita'],
                'estado_cita' => $estado_cita,
                'observaciones' => $request['observaciones'],
                'est_amb_hos' => $request['est_amb_hos'],
                'id_seguro' => $request['id_seguro'],
                'estado' => 1,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'cortesia' => $cortesia,
                'procedencia' => $request['procedencia'],
                'paciente_dr' => $request['paciente_dr'],
                'omni' => $omni,
            ];
            if($request['hc'] != null){
                $paciente = Paciente::find($request['id_paciente']);
                $historiaclinica_nueva = $request['hc'].$paciente->historia_clinica;
                $nuevo_historia_clinica = [                                   
                    'historia_clinica' => $historiaclinica_nueva,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario 
                ];
                Paciente::where('id', $paciente->id)->update($nuevo_historia_clinica); 
            }
             

            $id_agenda = Agenda::insertGetId($input_historia);
            if($request['hc'] != null)
            {
                    Agenda_archivo::create([
                        'id_agenda' => $id_agenda,
                        'tipo_documento' => 'txt',
                        'texto' => $request['hc'],
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario
                    ]);
            }
            if($request['archivo'] != null)
            {
                $input_archivo = [
                    'id_agenda' => $id_agenda,
                    'tipo_documento' => "HCAGENDA",
                    'descripcion' => "Historia Clinica creada de la agenda",
                    'ruta' => "/hc_agenda/",
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]; 
                $id_archivo=Agenda_archivo::insertGetId($input_archivo);
                $this->subir_archivo_validacion($request, $id_agenda, $id_archivo);
            }
            /* validacion correo
            Mail::send('mails.consulta', $request->all(), function($msj)  use ($correo){
                $msj->subject('Reservacion de cita medica IECED');
                $msj->to($correo);  
                $msj->bcc('torbi10@hotmail.com');  
            });*/
            $input = [
            'id_seguro' => $request['id_seguro'],
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario

            ];
            if($paciente->fecha_nacimiento != $request['fecha_nacimiento']){
                //dd($request->all());
                Paciente::where('id', $request['id_paciente'])->update($input);
            } 

        }
        if($valor == 2){
             
            Agenda::create([
            'fechaini' => $request['inicio'],
            'fechafin' => $request['fin'],
            'procedencia' => $request['clase'],
            'id_doctor1' => $request['id_doctor1'],
            'proc_consul' => $request['proc_consul'],
            'id_sala' => $request['id_sala'],
            'estado_cita' => 1,
            'observaciones' => $request['observaciones'],
            'estado' => 1,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario

            ]);
        }
        if($valor == 1){

            //VALIDA SI TIENE AGREGADO PROCEDIMIENTO 22012019
            $rules1 = [
                'procedimiento' => 'required'
            ];
            $mensajes1 = [
                'procedimiento.required' => 'Ingrese el Procedimiento'
            ];
            $this->validate($request, $rules1, $mensajes1);
            //
            //dd("paso");
            
            if($request['id_doctor2'] != '' || $request['id_doctor3'] != ''){
                $this->validateInput5($request);
                
                
            }
            if($request['id_doctor2'] != '' && $request['id_doctor3'] != ''){
                
                $this->validateDoctores($request);
                
            }
            if($request['id_doctor2'] != ''){
                $this->validateMax2($request);
                
            }
            if($request['id_doctor3'] != ''){
                $this->validateMax3($request);
                
            }
            
            $input_historia = [
                'fechaini' => $request['inicio'],
                'fechafin' => $request['fin'],
                'id_paciente' => $request['id_paciente'],
                'id_doctor1' => $request['id_doctor1'],
                'id_doctor2' => $request['id_doctor2'],
                'id_doctor3' => $request['id_doctor3'],
                'id_procedimiento' => $procedimientop,
                'proc_consul' => $request['proc_consul'],
                'id_sala' => $request['id_sala'],
                'espid' => $request['espid'],
                'id_seguro' => $request['id_seguro'],
                'tipo_cita' => $request['tipo_cita'],
                'estado_cita' => $estado_cita,
                'observaciones' => $request['observaciones'],
                'est_amb_hos' => $request['est_amb_hos'],
                'estado' => 1,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                //CORRECCION CORTESIA
                'cortesia' => $cortesia,
                'procedencia' => $request['procedencia'],
                'paciente_dr' => $request['paciente_dr'],
                'omni' => $omni,
            ];

            $id_agenda = agenda::insertGetId($input_historia);
            $procedimiento_enviar = null;
            foreach ($procedimientos as $value){
                $procedimiento_a = DB::table('procedimiento')->where('id', '=', $value)->get();
                $procedimiento_enviar =  $procedimiento_a[0]->nombre.'+'.$procedimiento_enviar;
                
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
            if($request['hc'] != null){
                $paciente = Paciente::find($request['id_paciente']);
                $historiaclinica_nueva = $request['hc'].$paciente->historia_clinica;
                $nuevo_historia_clinica = [                                   
                    'historia_clinica' => $historiaclinica_nueva,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario 
                ];
                Paciente::where('id', $paciente->id)->update($nuevo_historia_clinica); 
            }

            if($request['hc'] != null)
            {
                    Agenda_archivo::create([
                        'id_agenda' => $id_agenda,
                        'tipo_documento' => 'txt',
                        'texto' => $request['hc'],
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario
                    ]);
            }
            if($request['archivo'] != null)
            {
                $input_archivo = [ 
                    'id_agenda' => $id_agenda,
                    'tipo_documento' => "HCAGENDA",
                    'descripcion' => "Historia Clinica creada de la agenda",
                    'ruta' => "/hc_agenda/",
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]; 
                $id_archivo=Agenda_archivo::insertGetId($input_archivo);
                $this->subir_archivo_validacion($request, $id_agenda, $id_archivo);
            }
            $input = [
                'id_seguro' => $request['id_seguro'],
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario

            ];
            if($paciente->fecha_nacimiento != $request['fecha_nacimiento']){
                //dd($request->all());
                Paciente::where('id', $request['id_paciente'])->update($input);
            } 
            /* enviar correo procedimiento
            $procedimiento_enviar = substr($procedimiento_enviar, 0, -1);
            $avanza = array("procedimiento_nombre" => $procedimiento_enviar, "nombre_paciente" => $request['nombre_paciente'], "especialidad_nombre" => $request['especialidad_nombre'], "inicio" => $request['inicio'], "nombre_doctor" => $request['nombre_doctor'], "hospital_nombre" => $request['hospital_nombre'], "consultorio_nombre" => $request['consultorio_nombre'], "hospital_direccion" => $request['hospital_direccion']);
            if($paciente->fecha_nacimiento != $request['fecha_nacimiento']){
                Paciente::where('id', $request['id_paciente'])->update($input);
            }    
            Mail::send('mails.procedimiento', $avanza, function($msj)  use ($correo){
                $msj->subject('Reservacion de procedimiento medico IECED');
                $msj->to($correo);
                $msj->bcc('torbi10@hotmail.com');      
            });*/
        }
        return  redirect()->route('solicitud.calendario', ['id' => $request['id_doctor1'], 'fecha' => $request['unix']]);
    }

    private function  validateInput3($request) {
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

    private function validateInput6($request) {
        $reglas =[
        'id' => 'exists:paciente', 
        'espid' =>  'required',
        'id_paciente' =>  'required',
        'est_amb_hos' => 'required',
        'tipo_cita' => 'required',
        ];
        $mensajes = [
        'id.exists' =>'Paciente ingresado no existe',    
        'id_paciente.required' =>'se requiere numero de cedula del paciente',
        'est_amb_hos.required' =>'Seleccione el estado de ingreso del paciente',
        'tipo_cita.required' =>'Seleccione es consecutivo o primera vez',
        ];
        $this->validate($request,$reglas, $mensajes);
    }

    private function validate_paciente($request){
        $id_paciente = $request['id_paciente'];
        $ini2 = date_create($request['inicio']);
        $fin2 = date_create($request['fin']);
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin = date_format($fin, 'Y/m/d H:i:s');
        $dato2 = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin){
                return $query->where('id_paciente', '=', $request['id_paciente']);
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
        'id_paciente' =>  'unique_doctor:'.$cant_agenda,
        ];
        $mensajes = [ 
        'id_paciente.unique_doctor' => 'El paciente ya posee una cita a esta hora',   
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
        //return  9/10/2018 se habilita bloqueo

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
        //dd($cantidad);
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

    public function paciente($id, $i, $fecha)
    {
        $opcion = '4'; //AGENDA DE SEGUROS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }
        $seguros = Seguro::where('tipo','<>','0')->where('inactivo','1')->get();
        $user = DB::table('users')->where([['id', '=', $i],])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed

         if ($user == null || count($user) == 0) {
            $user = Array();
  
        }
          
        $pais=pais::all();

        
        return view('bo/paciente', ['user' => $user, 'seguros' => $seguros, 'pais' => $pais, 'id' => $id, 'i' => $i, 'fecha' => $fecha]);
    }

    public function guarda_paciente(Request $request)
    {
        //dd($request->all());
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        
        date_default_timezone_set('America/Guayaquil');
        $bandera=0;
        $id = $request['id'];

        $user = User::find($id);

        // Redirect to user list if updating user wasn't existed

         if ($request['parentesco']<>"Principal"){
            $this->validateprincipal($request);
         }

         if ($user != Array()) {

            $this->validateInput2($request);
            Paciente::create([
                'id' => $request['id2'],
                'nombre1' => strtoupper($request['nombre12']),
                'nombre2' => strtoupper($request['nombre22']),
                'apellido1' => strtoupper($request['apellido12']),
                'apellido2' => strtoupper($request['apellido22']),
                'telefono1' => $request['telefono12'],
                'telefono2' => $request['telefono22'],
                'nombre1familiar' => strtoupper($request['nombre1']),
                'nombre2familiar' => strtoupper($request['nombre2']),
                'apellido1familiar' => strtoupper($request['apellido1']),
                'apellido2familiar' => strtoupper($request['apellido2']),
                'parentesco' => $request['parentesco'],
                'parentescofamiliar' => $request['parentesco'],
                'id_pais' => $request['id_pais2'],
                'fecha_nacimiento' => $request['fecha_nacimiento2'],
                'telefono3' => $request['telefono2'],
                'tipo_documento' => 1,
                'imagen_url' => ' ',
                'menoredad' => $request['menoredad'],
                'id_seguro' => $request['id_seguro'],
                'id_usuario' => $request['id'],
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario
                ]);
                Log_usuario::create([
                'id_usuario' => $idusuario,
                'ip_usuario' => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1' => $request['id2'],
                'dato1' => strtoupper($request['nombre12'])." ".strtoupper($request['nombre22'])." ".strtoupper($request['apellido12'])." ".strtoupper($request['apellido22']),
                'dato_ant2' => "SEGURO: ".$request['id_seguro']." PARENTESCO: ".$request['parentesco'],
                ]);
        }
        else{

            $this->validateInput($request);
            User::create([
            'id' => $request['id'],
            'nombre1' => strtoupper($request['nombre1']),
            'nombre2' => strtoupper($request['nombre2']),
            'apellido1' => strtoupper($request['apellido1']),
            'apellido2' => strtoupper($request['apellido2']),
            'telefono1' => $request['telefono1'],
            'telefono2' => $request['telefono2'],
            'id_pais' => $request['id_pais'],
            'fecha_nacimiento' => $request['fecha_nacimiento'],
            'id_tipo_usuario' => 2,
            'email' => $request['email'],
            'password' => bcrypt($request['id']),
            'tipo_documento' => 1,
            'estado' => 1,
            'imagen_url' => ' ',
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario]);
           paciente::create([
                'id' => $request['id2'],
                'nombre1' => strtoupper($request['nombre12']),
                'nombre2' => strtoupper($request['nombre22']),
                'apellido1' => strtoupper($request['apellido12']),
                'apellido2' => strtoupper($request['apellido22']),
                'telefono1' => $request['telefono12'],
                'telefono2' => $request['telefono22'],
                'nombre1familiar' => strtoupper($request['nombre1']),
                'nombre2familiar' => strtoupper($request['nombre2']),
                'apellido1familiar' => strtoupper($request['apellido1']),
                'apellido2familiar' => strtoupper($request['apellido2']),
                'parentesco' => $request['parentesco'],
                'parentescofamiliar' => $request['parentesco'],
                'id_pais' => $request['id_pais2'],
                'fecha_nacimiento' => $request['fecha_nacimiento2'],
                'telefono3' => $request['telefono2'],
                'tipo_documento' => 1,
                'imagen_url' => ' ',
                'menoredad' => $request['menoredad'],
                'id_seguro' => $request['id_seguro'],
                'id_usuario' => $request['id'],
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario
                ]);
           Log_usuario::create([
                'id_usuario' => $idusuario,
                'ip_usuario' => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1' => $request['id2'],
                'dato1' => strtoupper($request['nombre12'])." ".strtoupper($request['nombre22'])." ".strtoupper($request['apellido12'])." ".strtoupper($request['apellido22']),
                'dato_ant2' => "SEGURO: ".$request['id_seguro']." PARENTESCO: ".$request['parentesco'],
                ]);
        }
        
        
        if ($request['doctor']=="0"){

             $paciente = DB::table('paciente')->where('id','!=','9999999999')
            ->paginate(10);

            return view('paciente/index', ['paciente' => $paciente]);
             
        }
        else{

            return  redirect()->route('solicitud.agendar', ['id' => $request['doctor'], 'fecha' => $request['fecha'], 'i' => $request['id2']]); 
        }
            
        
    }

    private function validateprincipal($request) {

        $rules = [
        'id2' =>  'different:id',       
        ];
       
        $messages= [
        'id2.different' => 'Cédula es la misma que la del principal.',  
        ];    

        $this->validate($request, $rules, $messages);
    }

    private function validateInput2($request) {

        $rules = [
        'parentesco' =>  'required',   
        'id_seguro' =>  'required',
        'id2' =>  'required|max:10|unique:paciente,id',
        'nombre12' =>  'required|max:60',
        'nombre22' =>  'required|max:60',
        'apellido12' =>  'required|max:60',
        'apellido22' =>  'required|max:60',     
        'telefono12' =>  'required|max:50', 
        'telefono22' =>  'required|max:50', 
        'id_pais2' =>  'required',
        'fecha_nacimiento2' => 'required|date',     
        ];
         if ($request['parentesco']=="Principal"){
            
            $rules=array_add($rules, 'menoredad', 'in:0');
           
            
        }
        $messages= [
        'parentesco.required' => 'Selecciona el parentesco.', 
        'parentesco.in' => 'Debe seleccionar entre Padre/Madre,Conyugue,Hijo(a).',    
        'id_seguro.required' => 'Selecciona el seguro.',   
        'id2.required' => 'Agrega la cédula.',
        'id2.max' =>'La cédula no puede ser mayor a :max caracteres.',
        'id2.unique' => 'Cedula ya se encuentra registrada a un paciente.', 
        'nombre12.required' => 'Agrega el primer nombre.',
        'nombre22.required' => 'Agrega el segundo nombre.',
        'nombre12.max' =>'El primer nombre no puede ser mayor a :max caracteres.',
        'nombre22.max' =>'El segundo nombre no puede ser mayor a :max caracteres.',
        'apellido12.required' => 'Agrega el primer apellido.',
        'apellido12.max' =>'El primer apellido no puede ser mayor a :max caracteres.',
        'apellido22.required' => 'Agrega el segundo apellido.',
        'apellido22.max' =>'El segundo apellido no puede ser mayor a :max caracteres.', 
        'telefono12.required' => 'Agrega el teléfono del domicilio.',
        'telefono12.numeric' =>'El teléfono de domicilio debe ser numérico.',
        'telefono12.max' =>'El teléfono del domicilio no puede ser mayor a :max caracteres.',
        'telefono22.required' => 'Agrega el teléfono celular.',
        'telefono22.numeric' =>'El teléfono celular debe ser numérico.', 
        'telefono22.max' =>'El teléfono celular no puede ser mayor a :max caracteres.',
        'id_pais2.required' => 'Selecciona el pais.',
        'fecha_nacimiento2.required' =>'Agrega la fecha de nacimiento.',
        'fecha_nacimiento2.date' =>'La fecha de nacimiento tiene formato incorrecto.',
        'menoredad.in' => 'El Asegurado Principal no puede ser menor de edad.', 
        ];    

        $this->validate($request, $rules, $messages);
    }

    private function validateInput($request) {
        $rules = [
        'parentesco' =>  'required',   
        'id' =>  'required|max:10|unique:users',
        'nombre1' =>  'required|max:60',
        'nombre2' =>  'required|max:60',
        'apellido1' =>  'required|max:60',
        'apellido2' =>  'required|max:60',
        'telefono1' => 'required|max:50',
        'telefono2' => 'required|max:50',
        'id_pais' =>  'required',
        'fecha_nacimiento' => 'required|date|edad_fecha',
        'email' =>  'required|email|max:191|unique:users',  
        'id_seguro' =>  'required',
        'id2' =>  'required|max:10|unique:paciente,id',
        'nombre12' =>  'required|max:60',
        'nombre22' =>  'required|max:60',
        'apellido12' =>  'required|max:60',
        'apellido22' =>  'required|max:60',     
        'telefono12' =>  'required|max:50', 
        'telefono22' =>  'required|max:50', 
        'id_pais2' =>  'required',
        'fecha_nacimiento2' => 'required|date'     
        ];
        if ($request['parentesco']=="Principal"){
            $rules=array_add($rules, 'menoredad', 'in:0');
        }
        $messages= [
        'parentesco.required' => 'Selecciona el parentesco.', 
        'parentesco.in' => 'Debe seleccionar Ninguno.',    
        'id.required' => 'Agrega la cédula.',
        'id.max' =>'La cédula no puede ser mayor a :max caracteres.',
        'id.unique' => 'Cedula ya se encuentra registrada.',      
        'nombre1.required' => 'Agrega el primer nombre.',
        'nombre22.required' => 'Agrega el segundo nombre.',
        'nombre1.max' =>'El primer nombre no puede ser mayor a :max caracteres.',
        'nombre2.max' =>'El segundo nombre no puede ser mayor a :max caracteres.',
        'apellido1.required' => 'Agrega el primer apellido.',
        'apellido1.max' =>'El primer apellido no puede ser mayor a :max caracteres.',
        'apellido2.required' => 'Agrega el segundo apellido.',
        'apellido2.max' =>'El segundo apellido no puede ser mayor a :max caracteres.',
        'telefono1.required' => 'Agrega el teléfono del domicilio.',
        'telefono1.numeric' =>'El teléfono de domicilio debe ser numérico.',
        'telefono1.max' =>'El teléfono del domicilio no puede ser mayor a :max caracteres.',
        'telefono2.required' => 'Agrega el teléfono celular.',
        'telefono2.numeric' =>'El teléfono celular debe ser numérico.', 
        'telefono2.max' =>'El teléfono celular no puede ser mayor a :max caracteres.',
        'id_pais.required' => 'Selecciona el pais.',
        'fecha_nacimiento.required' =>'Agrega la fecha de nacimiento.',
        'fecha_nacimiento.date' =>'La fecha de nacimiento tiene formato incorrecto.',
        'email.required' => 'Agrega el Email.',
        'email.email' =>'El Email tiene error en el formato.',
        'email.max' =>'El Email no puede ser mayor a :max caracteres.',
        'email.unique' => 'el Email ya se encuentra registrado.',
        'id_seguro.required' => 'Selecciona el seguro.',   
        'id2.required' => 'Agrega la cédula.',
        'id2.max' =>'La cédula no puede ser mayor a :max caracteres.',
        'id2.unique' => 'Cedula ya se encuentra registrada a un paciente.', 
        'nombre12.required' => 'Agrega el primer nombre.',
        'nombre12.max' =>'El primer nombre no puede ser mayor a :max caracteres.',
        'nombre22.max' =>'El segundo nombre no puede ser mayor a :max caracteres.',
        'apellido12.required' => 'Agrega el primer apellido.',
        'apellido12.max' =>'El primer apellido no puede ser mayor a :max caracteres.',
        'apellido22.required' => 'Agrega el segundo apellido.',
        'apellido22.max' =>'El segundo apellido no puede ser mayor a :max caracteres.', 
        'telefono12.required' => 'Agrega el teléfono del domicilio.',
        'telefono12.numeric' =>'El teléfono de domicilio debe ser numérico.',
        'telefono12.max' =>'El teléfono del domicilio no puede ser mayor a :max caracteres.',
        'telefono22.required' => 'Agrega el teléfono celular.',
        'telefono22.numeric' =>'El teléfono celular debe ser numérico.', 
        'telefono22.max' =>'El teléfono celular no puede ser mayor a :max caracteres.',
        'id_pais2.required' => 'Selecciona el pais.',
        'fecha_nacimiento2.required' =>'Agrega la fecha de nacimiento.',
        'fecha_nacimiento2.date' =>'La fecha de nacimiento tiene formato incorrecto.',
        'menoredad.in' => 'El Asegurado Principal no puede ser menor de edad.', 
        ];    

        //return $rules;
        $this->validate($request, $rules, $messages);
    }

    public function nombre_paciente($id_doc, $fecha)
    {
        $opcion = '4'; //AGENDA DE SEGUROS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }

        $paciente = DB::table('paciente')->where('id','!=','9999999999')
        ->paginate(10);


        return view('bo/buscaxnombre', ['paciente' => $paciente, 'id_doc' => $id_doc, 'fecha' => $fecha]);
    }

    public function search_paciente(Request $request ) {


        $opcion = '4'; //AGENDA DE SEGUROS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }

        $id_doc=$request['id_doc'];
        $fecha=$request['fecha'];
        $sala=$request['sala'];
        
        $constraints = [
            'nombre1' => $request['nombres'],
            ];


        $nombres = explode(" ", $request['nombres']);
         
        $cantidad = count($nombres);

        $query = Paciente::where('id', '!=', '9999999999');
        

        if($nombres[0]!=""){
            if($cantidad>0){

                if($cantidad>=4){
                    $query = $query->Where('nombre1', 'like', '%'.$nombres[0].'%')
                    ->Where('nombre2', 'like', '%'.$nombres[1].'%')
                    ->Where('apellido1', 'like', '%'.$nombres[2].'%')
                    ->Where('apellido2', 'like', '%'.$nombres[3].'%');
                }
                if($cantidad==3){
                    $query = $query->where('nombre1', 'like',  $nombres[0].'%')
                    ->Where(function($jquery2) use($nombres) {$jquery2->orwhere('nombre2', 'like',  $nombres[1].'%')
                        ->orwhere('apellido1', 'like',  $nombres[1].'%');})
                    ->Where(function($jquery3) use($nombres) {$jquery3->orwhere('apellido1', 'like',  $nombres[2].'%')
                        ->orwhere('apellido2', 'like',  $nombres[2].'%');});
                }
                if($cantidad==2){
                    $query = $query->Where(function($jquery1) use($nombres) {$jquery1->orwhere('nombre1', 'like', $nombres[0].'%')
                        ->orwhere('apellido1', 'like',  $nombres[0].'%');})
                    ->Where(function($jquery2) use($nombres) {$jquery2->orwhere('nombre1', 'like',  $nombres[1].'%')
                        ->orwhere('nombre2', 'like',  $nombres[1].'%')
                        ->orwhere('apellido1', 'like',  $nombres[1].'%')->orwhere('apellido2', 'like',  $nombres[1].'%');});
                }
                if($cantidad==1){       
                    $query = $query->Where('nombre1', 'like', '%'.$nombres[0].'%')
                    ->orWhere('nombre2', 'like', '%'.$nombres[0].'%')
                    ->orWhere('apellido1', 'like', '%'.$nombres[0].'%')
                    ->orWhere('apellido2', 'like', '%'.$nombres[0].'%');
                        
                    
                }

                $query = $query->ORWHERERAW('CONCAT(nombre1," ",nombre2," ",apellido1," ",apellido2) LIKE ?', ['%'.$request['nombres'].'%']);      
                    
            }

        }
        $query = $query->paginate(10);
         
      return view('bo/buscaxnombre', ['paciente' => $query,  'searchingVals' => $constraints, 'id_doc' => $id_doc, 'fecha' => $fecha]);
    }

    public function editar_agenda($id, $url_doctor)
    {
        

        $opcion = '4'; //AGENDA DE SEGUROS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }

        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado','=',1)->get(); //3=DOCTORES;
        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->get()->where('estado','=',1); //6=ENFERMEROS;
        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')->orderBy('hospital.nombre_hospital')
            ->get();        
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'paciente.ocupacion', 'paciente.referido')
            ->where('agenda.id', '=', $id)
            ->first();

        $logs = DB::table('log_agenda as l')->where('l.id_agenda',$agenda->id)->join('users as u','u.id','l.id_usuariocrea')->select('l.*','u.nombre1','u.apellido1')->orderBy('l.id','desc')->get();
            
        $historia =  Historiaclinica::where('id_agenda',$id)->get();

        $ordenes=Examen_Orden::where('id_paciente',$agenda->id_paciente)->count();
        //dd($ordenes);
        $cantidad_doc=0;
        if(!is_null($historia->first())){

            $ControlDocController = new ControlDocController;
            $hSeguro = Seguro::find($historia['0']->id_seguro);
            
            $cantidad_doc = $ControlDocController->carga_documentos_union($historia['0']->hcid, $agenda->proc_consul, $hSeguro->tipo)->count();

            $ordenes = Examen_Orden::where('hcid',$historia[0]->hcid)->count();
            

        }
         

        //cedula y nombre del paciente cambiar a produ 7/11/2017
        $especialidades = DB::table('user_espe')->join('especialidad', 'user_espe.espid', '=', 'especialidad.id')
            ->select('user_espe.*', 'especialidad.nombre as enombre')->where('usuid', '=', $agenda->id_doctor1)->get();

           
        
        $seguros = Seguro::where('tipo','<>','0')->where('inactivo','1')->get();
        $procedimientos=Procedimiento::all();
        $empresas=Empresa::all();
        $agendaprocedimientos=AgendaProcedimiento::where('id_agenda',$agenda->id)->get();
        //cedula y nombre del paciente cambiar a produ 7/11/2017

        $id_doc = $agenda->id_doctor1;       
        
        //agendas
        $cagenda = [];
        $cagenda3 = [];
        $cagenda2 = [];

        $ar_historia = DB::table('agenda_archivo')->where('id_agenda',$id)->where('tipo_documento', '=','HCAGENDA')->get(); 
        $ar_historiatxt = Agenda_archivo::where('id_agenda',$id)->where('tipo_documento','txt')->first(); 

        $sala=null;
        $hospital=null;
        if(!is_null($agenda->id_sala)){
            $sala = Sala::find($agenda->id_sala); 
            $hospital = Hospital::find($sala->id_hospital);    
        }

        if(!is_null($historia->first())){
            $xtipo = $hSeguro->tipo;
        }else{
            $xtipo = Seguro::find($agenda->id_seguro)->tipo;    
        }

        $pre_post = '0';
        $ex_pre = null;
        $ex_post = null;
        if($xtipo=='0'){
            /////////////CONTROL LABS/////////// BUSCA EXAMEN OBLIGATORIO
            $obligatorio = Examen_obligatorio::where('tipo','0')->where('id_procedimiento',$agenda->id_procedimiento)->first();

            $pre_post = '0';
            if(!is_null($obligatorio))
            {
                $pre_post = $obligatorio->pre_post;//2 prey post

            }

            if($pre_post=='0'){
                $bandera=true;
                $agi='0';
                if($agendaprocedimientos->count()>0){
                    while($bandera){

                        $obligatorio = Examen_obligatorio::where('tipo','0')->where('id_procedimiento',$agendaprocedimientos[$agi]->id_procedimiento)->first();
                        $pre_post = '0';
                        if(!is_null($obligatorio))
                        {
                            $pre_post = $obligatorio->pre_post;//2 prey post

                        }
                        $agi++;
                        if($pre_post!='0'){
                            $bandera=false;   
                        }
                        if($agi>=$agendaprocedimientos->count()){
                            $bandera=false;    
                        }
                    }    
                }
                
            }


            /////////////CONTROL LABS/////////// BUSCA EXAMEN EXCEPCION
            if($pre_post=='0'){
                if($agenda->id_procedimiento!=null){
                    $excepcion = Examen_obligatorio::where('tipo','1')->where('id_procedimiento',$agenda->id_procedimiento)->first();
                    $pre_post = '0';
                    if(is_null($excepcion))
                    {
                        $pre_post = '1';//2 pre

                    }
                }
                    
            }
            
            if($pre_post=='0'){
                $bandera=true;
                $agi='0';
                if($agendaprocedimientos->count()>0){
                    while($bandera){

                        $excepcion = Examen_obligatorio::where('tipo','1')->where('id_procedimiento',$agendaprocedimientos[$agi]->id_procedimiento)->first();
                        $pre_post = '0';
                        if(is_null($excepcion))
                        {
                            $pre_post = '1';//2 prey post

                        }
                        $agi++;
                        if($pre_post!='0'){
                            $bandera=false;   
                        }
                        if($agi>=$agendaprocedimientos->count()){
                            $bandera=false;    
                        }
                    }    
                }
                
            }

            
            //ordenes del paciente de la ultima semana, pre y post
            //$hoy = Date('Y-m-d');
            $fecha_antes = Date('Y-m-d',strtotime('- 1 month',strtotime($agenda->fechaini)));
            $fecha_despues = Date('Y-m-d',strtotime('+5 day',strtotime($agenda->fechaini)));
            //dd($fecha_antes,$agenda->fechaini,$fecha_despues);

            $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente',$agenda->id_paciente)->where('eo.id_agenda',$id)->join('protocolo as p','p.id','eo.id_protocolo')->where('p.pre_post','PRE')->first();

            if(is_null($ex_pre)){
                $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente',$agenda->id_paciente)->whereBetween('eo.created_at',[$fecha_antes, $fecha_despues])->join('protocolo as p','p.id','eo.id_protocolo')->where('p.pre_post','PRE')->first();    
            }


            $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente',$agenda->id_paciente)->where('eo.id_agenda',$id)->join('protocolo as p','p.id','eo.id_protocolo')->where('p.pre_post','POST')->first();

            if(is_null($ex_post)){
                $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente',$agenda->id_paciente)->whereBetween('eo.created_at',[$fecha_antes, $fecha_despues])->join('protocolo as p','p.id','eo.id_protocolo')->where('p.pre_post','POST')->first();    
            }    
        }
        
            

        
        //dd($ex_pre, $ex_post);
            

        return view('bo/edit_agenda', ['agenda' => $agenda, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas, 'historia' => $historia, 'especialidades' => $especialidades, 'seguros' => $seguros, 'procedimientos' => $procedimientos, 'empresas' => $empresas, 'agendaprocedimientos' => $agendaprocedimientos, 'cagenda' => $cagenda, 'cagenda2' => $cagenda2, 'cagenda3' => $cagenda3, 'url_doctor' => $url_doctor, 'ar_historia' => $ar_historia, 'ar_historiatxt' => $ar_historiatxt, 'cantidad_doc' => $cantidad_doc, 'id_doc' => $id_doc, 'sala' => $sala, 'hospital' => $hospital, 'ordenes' => $ordenes, 'pre_post' => $pre_post, 'ex_pre' => $ex_pre, 'ex_post' => $ex_post, 'logs' => $logs]);
         
    }

    public function actualiza_agenda(Request $request, $id, $url_doctor)
    {
        $ruta = $request->ruta;
        //dd($request->all());
        $fecha = date('Y-m-d H:i');
        $agenda = Agenda::findOrFail($id);
        $paciente = Paciente::find($agenda->id_paciente);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $descripcion=""; 
        $descripcion2="";
        $descripcion3="CAMBIO: ";
        $est_cita=$request['estado_cita']; 
        $est='1';
        $bandera=false;
        $cambio=false;
        $flag2=false;
        $proc=$request['proc'];
        $aux_ant="";
        $aux="";
        //$agproc=AgendaProcedimiento::where('id_agenda',$agenda->id)->get();
        $agproc = [];
        
        if($request->est_amb_hos=='1'){
            $arr_hos = [
                'omni' => 'required',
            ];
            $arr_men = [
                'omni.required' => 'Seleccione Si es Omni Hospital',
            ];
            $this->validate($request, $arr_hos, $arr_men);
            $omni = $request->omni;
        }else{
            $omni = null;
        }
        $this->validateEdit($request);        
        $this->validateMax1_2($request, $id);
        $this->validateInput4($request);

        if($agenda->estado_cita!='4'){

            //cambio en campo
            if($agenda->espid!=$request['espid']){
                $descripcion3=$descripcion3." ESPECIALIZACIÓN,";
                $cambio=true;
                
            }
            if($agenda->id_seguro!=$request['id_seguro']){
                $descripcion3=$descripcion3." SEGURO,";
                $cambio=true;
                 
            }
            
            if($agenda->tipo_cita!=$request['tipo_cita']){
                $descripcion3=$descripcion3." TIPO_CITA,";
                $cambio=true;
                 
            }
            /*if($agenda->id_empresa!=$request['id_empresa']){
                $descripcion3=$descripcion3." EMPRESA,";
                $cambio=true;
                 
            }*/
            if($agenda->procedencia!=$request['procedencia']){
                $descripcion3=$descripcion3." PROCEDENCIA,";
                $cambio=true;
                 
            }
            
            if(date('Y/m/d',strtotime($paciente->fecha_nacimiento))!=$request['fecha_nacimiento']){//12/1/2018
                $descripcion3=$descripcion3." NACIMIENTO,";
                $cambio=true;
                 
            }

            if($agenda->supervisa_robles!=$request['supervisa_robles']){
                $descripcion3=$descripcion3." SUPERVISA DR. ROBLES,";
                $cambio=true;
                 
            }

            if($agenda->paciente_dr!=$request['paciente_dr']){
                $descripcion3=$descripcion3." PACIENTE_DR,";
                $cambio=true;
                 
            }

            if($agenda->solo_robles!=$request['solo_robles']){
                $descripcion3=$descripcion3." SOLO LO PUEDE REALIZAR EL DR. ROBLES,";
                $cambio=true;
                 
            }

            if($agenda->id_sala!=$request['id_sala']){
                $descripcion3=$descripcion3." SALA,";
                $cambio=true;
                 
            }

            if(!is_null($request['id_ag_artxt'])){

                $agenda_archivotxt = Agenda_archivo::find($request['id_ag_artxt']);
                if($agenda_archivotxt->texto != $request['hc']){
                    $descripcion3=$descripcion3." AGENDA_ARCHIVO_TXT,";
                    $cambio=true;    
                } 
            }
            else{
                if(!is_null($request['hc'])){
                    $descripcion3=$descripcion3." AGENDA_ARCHIVO_TXT,";
                    $cambio=true;
                }        
            }


            if($agenda->proc_consul=='1')
            {
                //cambio procedimiento
                //cambio el primero
                if($proc[0]!=$agenda->id_procedimiento)
                {
                    $flag2=true;
                   
                }
                else
                {
                    if(count($proc)-1!=$agproc->count())
                    {
                        $flag2=true; 
                        
                    }    
                    for($x=1; $x<count($proc); $x++)
                    {
                        if($x<=$agproc->count())
                        {
                            if($proc[$x]!=$agproc[$x-1]->id_procedimiento)
                            {
                                $flag2=true;
                            }        
                        }       
                    }

                }
                if($flag2){
                    $descripcion3=$descripcion3." PROCEDIMIENTO";    
                }
                    
            } 

        
        }

        if($agenda->est_amb_hos!=$request['est_amb_hos']){
            $descripcion3=$descripcion3." INGRESO,";
            $cambio=true;
             
        }

        //CORTESIA/OCUPACION/REFERIDO
        /*if($agenda->cortesia!=$request['cortesia']){
            $descripcion3=$descripcion3." CORTESIA,";
            $cambio=true;
            
        }*/

        if($agenda->omni!=$omni){
            $descripcion3=$descripcion3." OMNI,";
            $cambio=true;
            //return "ok";
        }

        if($paciente->ocupacion!=$request['ocupacion']){

            $descripcion3=$descripcion3." OCUPACION,";
            $cambio=true;
            
        }

        if($paciente->referido!=$request['referido']){
            $descripcion3=$descripcion3." REFERIDO,";
            $cambio=true;
            
        }

        if($agenda->observaciones!=$request['observaciones']){ ///1408
            $descripcion3=$descripcion3." OBSERVACION,";
            $cambio=true;
             
        }
    
        
        
        if($request['estado_cita']=='0' && $url_doctor != '0')//Por Confirmar
        {
            if(!$cambio&&!$flag2&&$request['archivo'] == null){

                return  redirect()->route('solicitud.calendario', ['id' => $url_doctor, 'i' => $request['unix'] ]);
                }   
        }  
         
        if($request['estado_cita']=='1')//confirmar
        {
            if($agenda->estado_cita=='1' && $url_doctor != '0')
            {
                if(!$cambio&&!$flag2&&$request['archivo'] == null){
                return  redirect()->route('solicitud.calendario', ['id' => $url_doctor, 'i' => $request['unix'], ]);
                }  
            }    
            else
            {
                
                $this->validateInput3_3($request, $id, $agenda->fechaini, $agenda->fechafin);
                $descripcion="CONFIRMO LA CITA";
                $bandera=true;
                $input = [
                    'estado' => '1',
                    'estado_cita' => $request['estado_cita'],
                    'observaciones' => $request['observaciones'],
                    'id_usuarioconfirma' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                ];
            }
        } 
        if($request['estado_cita']=='2' ) //reagendar
        {

            $bandera=true; 
            $est_cita='0';
            $descripcion="RE-AGENDO LA CITA";
            $nro_reagenda = $agenda->nro_reagenda + 1;

            //12/1/2018 validacion de sala ocupada
            $idhospital=Sala::find($request['id_sala'])->id_hospital;
            if($idhospital=='2'){

            //$this->valida_salaPentax($request,'1',$id);    
            }//--
            // 13/01/2018   Solo cuenta reagenda para procedimientos si:
            //cambia de hospital, fecha de inicio y fin a otro día
            if($request['proc_consul']=='1')
            {
                $flag_act='0';
                $flag_reag='0';
                $descripcion2="";
                 
                $nro_reagenda=$agenda->nro_reagenda;
                if($request['id_doctor1']!=$agenda->id_doctor1){
                    $flag_act='1'; 
                     
                    $descripcion2=$descripcion2." DOCTOR";        
                }
                if($request['id_doctor2']!=$agenda->id_doctor2){
                    $flag_act='1';
                     
                    $descripcion2=$descripcion2." ASISTENTE";        
                }
                if($request['id_doctor3']!=$agenda->id_doctor3){
                    $flag_act='1'; 
                     
                    $descripcion2=$descripcion2." ASISTENTE";        
                }
                $req_idhos=Sala::find($request['id_sala'])->id_hospital;
                $ag_idhos="";
                $vid_sala="";
                if(!is_null($agenda->id_sala)){
                    $ag_idhos=Sala::find($agenda->id_sala)->id_hospital;
                    $vid_sala=$agenda->id_sala;    
                }
                
                if($request['id_sala']!=$vid_sala){
                    
                    if($req_idhos!=$ag_idhos){
                        $flag_reag='1';
                        
                        $descripcion2=$descripcion2." HOSPITAL";
                        $nro_reagenda=$agenda->nro_reagenda + 1;            
                    }else{
                        $flag_act='1'; 
                         
                        $descripcion2=$descripcion2." SALA";
                    }
                            
                }

                if($request['inicio']!=null){
                    if(date('Y-m-d H:i:s',strtotime($request['inicio']))!=$agenda->fechaini)
                    {
                        $req_ini=substr($request['inicio'],0,10);
                        $new_ini=substr($agenda->fechaini,0,10);
                        $req_ini=date('Y/m/d',strtotime($req_ini));
                        $new_ini=date('Y/m/d',strtotime($new_ini));
                        if($new_ini!=$req_ini){
                            $flag_reag='1';
                            
                            $descripcion2=$descripcion2." FECHA";
                            $nro_reagenda=$agenda->nro_reagenda + 1;
                        }
                        else{
                            $flag_act='1'; 
                            
                            $descripcion2=$descripcion2." HORA";      
                        }
                    }    
                }
                if($flag_reag=='1'){
                    $descripcion="RE-AGENDO LA CITA";
                }elseif($flag_act=='1'){
                    $descripcion="ACTUALIZA";
                }


                
            }//-------

            $this->validateInput3_2($request, $id);

             //valida horario del doctor
                $horariocontroller = new HorarioController();
                $cantidad_horarios = $horariocontroller->valida_horarioxdoctor_dia($request);

                $cantidad_horarios2 = $horariocontroller->ValidaHorarioConsulta($request); //SOLO EN FRANJA DE CONSULTAS
            
            $input = [
                'estado' => '1',
                'nro_reagenda' => $nro_reagenda,   
                'estado_cita' => '0',
                'observaciones' => $request['observaciones'],
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'fechaini' => $request['inicio'],
                'fechafin' => $request['fin'],
                'id_doctor1' => $request['id_doctor1'], 
                'id_sala' => $request['id_sala'],         
                ];
            if($request['proc_consul']=='1')
            { 
                $input2=[
                    'id_doctor2' => $request['id_doctor2'],
                    'id_doctor3' => $request['id_doctor3'], 
                ];   
                $input=array_merge($input,$input2);
                if($request['id_doctor2'] != '' || $request['id_doctor3'] != ''){
                    $this->validateInput5_2($request, $id);
                    
                }
                if($request['id_doctor2'] != '' && $request['id_doctor3'] != ''){
                    
                    $this->validateDoctores($request);
                    $this->validateDoctores2($request);
                }

                if($request['id_doctor2'] != ''){
                    $this->validateMax2_2($request, $id);
                
                }
                if($request['id_doctor3'] != ''){
                    $this->validateMax3_2($request, $id);
                }
                
                
                
            }

            $agenda_datos = DB::table('agenda')->where('id', '=', $id)->get();
            $id_paciente = $agenda_datos[0]->id_paciente;
            

            $tipo = $agenda_datos[0]->proc_consul;
            $especialidad =  DB::table('especialidad')->where('id', '=', $agenda_datos[0]->espid)->get();
            $especialidad_nombre = $especialidad[0]->nombre;
            $paciente2 = DB::table('paciente')->where('id', '=', $id_paciente)->get();
            $usuario =  DB::table('users')->where('id', '=', $paciente2[0]->id_usuario)->get();
            $correo = $usuario[0]->email;
            $nombre_paciente =  $paciente2[0]->nombre1." ";
            if($paciente2[0]->nombre2 != '(N/A)')
            {
                $nombre_paciente = $nombre_paciente.$paciente2[0]->nombre2." ";
            }
            $nombre_paciente = $nombre_paciente.$paciente2[0]->apellido1." ";
            if($paciente2[0]->apellido2 != '(N/A)')
            {
                $nombre_paciente = $nombre_paciente.$paciente2[0]->apellido2." ";
            }

            $doctor = DB::table('users')->where('id', '=', $request['id_doctor1'])->get();
            $nombre_doctor =  $doctor[0]->nombre1." ";
            if($doctor[0]->nombre2 != '(N/A)')
            {
                $nombre_doctor = $nombre_doctor.$doctor[0]->nombre2." ";
            }
            $nombre_doctor = $nombre_doctor.$doctor[0]->apellido1." ";
            if($doctor[0]->apellido2 != '(N/A)')
            {
                $nombre_doctor = $nombre_doctor.$doctor[0]->apellido2." ";
            }
            $sala =  DB::table('sala')->where('id', '=', $request['id_sala'])->get();
            $cnombre = $sala[0]->nombre_sala;
            $hospital =  DB::table('hospital')->where('id', '=', $sala[0]->id_hospital)->get();
            $hnombre = $hospital[0]->nombre_hospital;

            $hdireccion = $hospital[0]->direccion;
           

        }  

        
        if($request['estado_cita']=='3' || $request['estado_cita']=='-1')
        { //suspender
            $bandera=true;
            $est='0';
            if($request['estado_cita']=='3'){
                $descripcion="SUSPENDIO LA CITA";
            }
            if($request['estado_cita']=='-1'){
                $descripcion="NO ASISTE A LA CITA";
            }
            
            $input = [
                'estado_cita' => $request['estado_cita'],
                'observaciones' => $request['observaciones'],
                'estado' => '0',
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
            $mensajes = ['observaciones.required' => 'Ingresa una Observación.'];        
            $constraints = ['observaciones' => 'required'];
            $this->validate($request, $constraints, $mensajes);

            //si existe en pentax debe dejar suspendido el paciente

            //$s_pentax = Pentax::where('id_agenda',$agenda->id)->first();
            $s_pentax = null;

            if(!is_null($s_pentax)){

                //log
                $input_log_p=[
                    'id_pentax' => $s_pentax->id,
                    'tipo_cambio' => "ESTADO",
                    'descripcion' => "SUSPENDIDO",
                    'estado_pentax' => '5',
                    'id_seguro' => $s_pentax->id_seguro,
                    'id_subseguro' => $s_pentax->id_subseguro,
                    'procedimientos' => '',
                    'id_doctor1' => $s_pentax->id_doctor1,
                    'id_doctor2' => $s_pentax->id_doctor2,
                    'id_doctor3' => $s_pentax->id_doctor3,
                    'id_sala' => $s_pentax->id_sala,
                    'observacion' => 'Suspendido desde Recepción: '.$request['observaciones'],
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'id_usuariocrea' => $idusuario,
                ]; 

                Pentax_log::create($input_log_p);

                //suspender
                $input_px=[
                    'estado_pentax' => '5',
                    'id_sala' => $s_pentax->id_sala,
                    'id_seguro' => $s_pentax->id_seguro,
                    'id_subseguro' => $s_pentax->id_subseguro,
                    'id_doctor1' => $s_pentax->id_doctor1,
                    'id_doctor2' => $s_pentax->id_doctor2,
                    'id_doctor3' => $s_pentax->id_doctor3,
                    'observacion' => 'Suspendido desde Recepción: '.$request['observaciones'],
                    'ingresa_prepa' => $s_pentax->ingresa_prepa,
                    'ingresa_proc' => $s_pentax->ingresa_proc,
                    'ingresa_rec' => $s_pentax->ingresa_rec,
                    'ingresa_alt' => $s_pentax->ingresa_alt,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];      

                $s_pentax->update($input_px);

            }            

            //pentax
        }
       
        if($request['estado_cita']=='4' && $agenda->estado_cita!='4')//ASISTIÓ
        {
            //return "ok";
            $est_cita=$agenda->estado_cita;
            if(!$cambio&&!$flag2&&$request['archivo'] == null){  
                
               return  redirect()->route('solicitud.calendario', ['id' => $url_doctor, 'i' => $request['unix'], ]);
            }  
        }    
              
        if($request['inicio']=='') {
            $ini=$agenda->fechaini;
        }
        else{
            $ini=$request['inicio'];
        } 
        if($request['fin']=='') {
            $fin=$agenda->fechafin;
        }
        else{
            $fin=$request['fin'];
        } 

        
        //return $omni;
        if($cambio){//cambia especialidad, seguro, ingreso o empresa
            $input_cambios = [
                'espid' => $request['espid'],
                'id_seguro' => $request['id_seguro'],
                'est_amb_hos' => $request['est_amb_hos'],
                'tipo_cita' => $request['tipo_cita'],
                //'id_empresa' => $request['id_empresa'],
                'procedencia' => $request['procedencia'],
                'paciente_dr' => $request['paciente_dr'],
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_sala' => $request['id_sala'],
                'cortesia' => $request['cortesia'],
                'observaciones' => $request['observaciones'],
                'supervisa_robles' => $request['supervisa_robles'],
                'solo_robles' => $request['solo_robles'],
                'omni' => $omni,


                ];    
        }  
        if($flag2){//procedimientos
            $input_proc = [
                'id_procedimiento' => $proc[0],
   
                'id_usuariomod' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                ];       
        } 

        if($agenda->proc_consul=='1'){
            foreach ($agproc as $a1 ) { $aux_ant=$aux_ant.$a1->id_procedimiento.";"; }
            if($agenda->estado_cita!='4'){
                foreach ($proc as $a2 ) { $aux=$aux.$a2.";"; }    
            }    
        }
        $fecha_ant = "";
        if(!is_null($paciente->fecha_nacimiento)){
            $fecha_ant = date('Y/m/d',strtotime($paciente->fecha_nacimiento));    
        }
        
      
       
        if($bandera){
            $agenda->update($input);
            //envio de correos electronicos
            if($request['estado_cita']=='1')//confirmar
            {
                
                $agenda = Agenda::findOrFail($id);
                $inicio = $agenda->fechaini;
                $tipo = $agenda->proc_consul;

                $id_paciente = $agenda->id_paciente;

                $especialidad =  DB::table('especialidad')->where('id', '=', $agenda->espid)->get();
                $especialidad_nombre = $especialidad[0]->nombre;
                $paciente2 = DB::table('paciente')->where('id', '=', $id_paciente)->get();
                $usuario =  DB::table('users')->where('id', '=', $paciente2[0]->id_usuario)->get();
                $correo = $usuario[0]->email;
                $nombre_paciente =  $paciente2[0]->nombre1." ";
                if($paciente2[0]->nombre2 != '(N/A)')
                {
                    $nombre_paciente = $nombre_paciente.$paciente2[0]->nombre2." ";
                }
                $nombre_paciente = $nombre_paciente.$paciente2[0]->apellido1." ";
                if($paciente2[0]->apellido2 != '(N/A)')
                {
                    $nombre_paciente = $nombre_paciente.$paciente2[0]->apellido2." ";
                }

                $doctor = DB::table('users')->where('id', '=', $request['id_doctor1'])->get();
                $nombre_doctor =  $doctor[0]->nombre1." ";
                if($doctor[0]->nombre2 != '(N/A)')
                {
                    $nombre_doctor = $nombre_doctor.$doctor[0]->nombre2." ";
                }
                $nombre_doctor = $nombre_doctor.$doctor[0]->apellido1." ";
                if($doctor[0]->apellido2 != '(N/A)')
                {
                    $nombre_doctor = $nombre_doctor.$doctor[0]->apellido2." ";
                }
                $sala =  DB::table('sala')->where('id', '=', $request['id_sala'])->get();
                $cnombre = $sala[0]->nombre_sala;
                $hospital =  DB::table('hospital')->where('id', '=', $sala[0]->id_hospital)->get();
                $hnombre = $hospital[0]->nombre_hospital;

                $hdireccion = $hospital[0]->direccion;
                if($tipo == 1){

                    $procedimiento_enviar = null;

                    $procedimiento_de_agenda = $agenda->id_procedimiento;
                    $procedimiento_a = DB::table('procedimiento')->where('id', '=', $procedimiento_de_agenda)->get();
                    $procedimiento_enviar =  $procedimiento_a[0]->nombre.'+'.$procedimiento_enviar;
                    
                    $procedimientos =  DB::table('agenda_procedimiento')->where('id_agenda', '=', $id)->get();    
                    foreach ($procedimientos as $value){
                        $procedimiento_a = DB::table('procedimiento')->where('id', '=', $value->id_procedimiento)->get();

                        $procedimiento_enviar =  $procedimiento_a[0]->nombre.'+'.$procedimiento_enviar;
                    }

                    $procedimiento_enviar = substr($procedimiento_enviar, 0, -1);
                    
                    $avanza = array("procedimiento_nombre" => $procedimiento_enviar, "nombre_paciente" => $nombre_paciente, "especialidad_nombre" => $especialidad_nombre, "inicio" => $agenda->fechaini, "nombre_doctor" => $nombre_doctor, "hospital_nombre" => $hnombre, "consultorio_nombre" => $cnombre, "hospital_direccion" => $hdireccion);
                    Mail::send('mails.procedimiento', $avanza, function($msj)  use ($correo){
                        $msj->subject('Reservación de procedimiento médico IECED');
                        $msj->to($correo);
                        $msj->bcc('torbi10@hotmail.com');      
                    });
                }
                if($tipo == 0){
                    
                    $avanza = array("nombre_paciente" => $nombre_paciente, "especialidad_nombre" => $especialidad_nombre, "inicio" => $inicio, "nombre_doctor" => $nombre_doctor, "hospital_nombre" => $hnombre, "consultorio_nombre" => $cnombre, "hospital_direccion" => $hdireccion);

                    Mail::send('mails.consulta', $avanza, function($msj)  use ($correo){
                        $msj->subject('Reservacion de cita médica IECED');
                        $msj->to($correo);
                        $msj->bcc('torbi10@hotmail.com');      
                    });
                    
                } 
            }
        }    

        /*if($agenda->cortesia!=$request['cortesia']){
            $agenda->update($input_cortesia);
        }*/     

        
        if($agenda->estado_cita!='4')
        {
            if($cambio)
            {//cambia especialidad, seguro, ingreso o empresa
                Log_agenda::create([
                    'id_agenda' => $agenda->id,
                    'estado_cita_ant' => $agenda->estado_cita,
                    'fechaini_ant' => $agenda->fechaini,
                    'fechafin_ant' => $agenda->fechafin,
                    'estado_ant' => $agenda->estado,
                    'cortesia_ant' => $agenda->cortesia,
                    'observaciones_ant' => $agenda->observaciones,
                    'id_doctor1_ant' => $agenda->id_doctor1,
                    'id_doctor2_ant' => $agenda->id_doctor2,
                    'id_doctor3_ant' => $agenda->id_doctor3,
                    'id_sala_ant' => $agenda->id_sala,

                    'estado_cita' => $est_cita,
                    'fechaini' => $ini,
                    'fechafin' => $fin,
                    'estado' => $est,
                    'cortesia' => $request->cortesia,
                    'observaciones' => $request['observaciones'],
                    'id_doctor1' => $request['id_doctor1'],
                    'id_doctor2' => $request['id_doctor2'],
                    'id_doctor3' => $request['id_doctor3'],
                    'id_sala' => $request['id_sala'],

                    'descripcion' => $descripcion,
                    'descripcion2' => $descripcion2,
                    'descripcion3' => $descripcion3,
                    'campos_ant' => "ESP:".$agenda->espid." SEG:".$agenda->id_seguro." ING:".$agenda->est_amb_hos." PRO:".$agenda->id_procedimiento.";".$aux_ant." PEN:".$agenda->procedencia." PDR:".$agenda->paciente_dr." FNA:".$fecha_ant,//12/1/2018
                    'campos' => "ESP:".$request['espid']." SEG:".$request['id_seguro']." ING:".$request['est_amb_hos']." PRO:".$aux." PEN:".$request['procedencia']." PDR:".$request['paciente_dr']." FNA:".$request['fecha_nacimiento'],
                    'id_usuarioconfirma' => $agenda->id_usuarioconfirma,
                
                    'id_usuariomod' => $idusuario,
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion' => $ip_cliente,           
                ]); 
                $agenda->update($input_cambios);
                $input_paciente = [
                   'id_seguro' => $request['id_seguro'],
                   'id_subseguro' => null,
                   'fecha_nacimiento' => $request['fecha_nacimiento'],
                   'ocupacion' => $request['ocupacion'],
                   'referido' => $request['referido'],
                ]; 
                $paciente->update($input_paciente); 
            }  
            if($agenda->proc_consul=='1')
            {
                if($flag2)

                {//procedimientos
                    foreach ($agproc as $ad ) 
                    {
                        $ad->delete();
                    }
                    $agenda->update($input_proc);
                    foreach ($proc as $value)
                    {
                        if($proc[0] != $value )
                        {
                            AgendaProcedimiento::create([
                            'id_agenda' => $id,
                            'id_procedimiento' => $value,

                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario
                            ]);
                        }
                   }      
                }
            }

            if(is_null($request['id_ag_artxt'])&&!is_null($request['hc']))
            {
                    
                    Agenda_archivo::create([
                        'id_agenda' => $id,
                        'tipo_documento' => 'txt',
                        'texto' => $request['hc'],
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario
                    ]);
            }

            if(!is_null($request['id_ag_artxt']))
            {
                if($agenda_archivotxt->texto != $request['hc']){
                    $input_hc_txt = [
                    'texto' => $request['hc'],
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]; 
                    $agenda_archivotxt->update($input_hc_txt);    
                }

            }
            if($request['archivo'] != null)
            {

                if(!is_null($request['id_ag_ar'])){

                           
                    Agenda_archivo::find($request['id_ag_ar'])->delete();
                } 
                  
                $input_archivo = [
                    'id_agenda' => $id,
                    'tipo_documento' => "HCAGENDA",
                    'descripcion' => "Historia Clinica creada de la agenda",
                    'ruta' => "/hc_agenda/",
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];


                
                $id_archivo=Agenda_archivo::insertGetId($input_archivo);


                $this->subir_archivo_validacion($request, $id, $id_archivo);

            } 
        }else{
            if($cambio)
            {//cambia especialidad, seguro, ingreso o empresa
                
                Log_agenda::create([
                    'id_agenda' => $agenda->id,
                    'estado_cita_ant' => $agenda->estado_cita,
                    'fechaini_ant' => $agenda->fechaini,
                    'fechafin_ant' => $agenda->fechafin,
                    'estado_ant' => $agenda->estado,
                    'cortesia_ant' => $agenda->cortesia,
                    'observaciones_ant' => $agenda->observaciones,
                    'id_doctor1_ant' => $agenda->id_doctor1,
                    'id_doctor2_ant' => $agenda->id_doctor2,
                    'id_doctor3_ant' => $agenda->id_doctor3,
                    'id_sala_ant' => $agenda->id_sala,

                    'estado_cita' => $est_cita,
                    'fechaini' => $ini,
                    'fechafin' => $fin,
                    'estado' => $est,
                    'cortesia' => $request->cortesia,
                    'observaciones' => $request['observaciones'],
                    'id_doctor1' => $request['id_doctor1'],
                    'id_doctor2' => $request['id_doctor2'],
                    'id_doctor3' => $request['id_doctor3'],
                    'id_sala' => $request['id_sala'],

                    'descripcion' => $descripcion,
                    'descripcion2' => $descripcion2,
                    'descripcion3' => $descripcion3,
                    'campos_ant' => "ESP:".$agenda->espid." SEG:".$agenda->id_seguro." ING:".$agenda->est_amb_hos." PRO:".$agenda->id_procedimiento.";".$aux_ant." PEN:".$agenda->procedencia." PDR:".$agenda->paciente_dr." FNA:".$fecha_ant,//12/1/2018
                    'campos' => "ESP:".$request['espid']." SEG:".$request['id_seguro']." ING:".$request['est_amb_hos']." PRO:".$aux." PEN:".$request['procedencia']." PDR:".$request['paciente_dr']." FNA:".$request['fecha_nacimiento'],
                    'id_usuarioconfirma' => $agenda->id_usuarioconfirma,
                
                    'id_usuariomod' => $idusuario,
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion' => $ip_cliente,           
                ]); 
                $input_cambios2 = [
                    
                    'est_amb_hos' => $request['est_amb_hos'],
                    'id_usuariomod' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'cortesia' => $request['cortesia'],
                    'observaciones' => $request['observaciones'],
                    'omni' => $omni,
                ];   
                $agenda->update($input_cambios2);
                $input_paciente2 = [
                   'id_seguro' => $request['id_seguro'],
                   'id_subseguro' => null,
                   'fecha_nacimiento' => $request['fecha_nacimiento'],
                   'ocupacion' => $request['ocupacion'],
                   'referido' => $request['referido'],
                ]; 
                $paciente->update($input_paciente2); 
            }  
        }

        return  redirect()->route('solicitud.calendario', ['id' => $url_doctor, 'i' => $request['unix'], ]);
        
    }

    private function validateEdit($request) {
        
        $mensajes = ['estado_cita.required' => 'Selecciona el Estado de la Cita.',
                     'observaciones.max' => 'La observacion no puede ser mayor a :max caracteres.',
                     'fecha_nacimiento.required' => 'Ingresa la fecha de nacimiento.',
                     ];        
        $constraints = ['estado_cita' => 'required', 
                        
                        'observaciones' => 'max:200',
                        
                        ];

                       
        $this->validate($request, $constraints, $mensajes);
       

    }

    private function validateMax1_2($request,$id) {
    
        $fecha_req=$request['inicio'];
        $fecha_req=substr($fecha_req,0,10);
        $fecha_req=strtotime($fecha_req);
        $fecha_min=date('Y-m-d H:i',$fecha_req);
        $fecha_max = strtotime ( '+1 day' , strtotime ( $fecha_min ) ) ;
        $fecha_max = date ( 'Y-m-d H:i' , $fecha_max ); 

        $dato2 = DB::table('agenda')->where(function ($query) use ($request){
                $query->where('id_doctor1', '=', $request['id_doctor1'])
                      ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                      ->orWhere('id_doctor3', '=', $request['id_doctor1']);})
                      ->where('proc_consul', '=', $request['proc_consul']) 
                      ->where('estado', '<>', '0')
                      ->where('id', '<>', $id)
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

    private function  validateInput3_3($request, $id, $ini2, $fin2) {

        $ini2 = date_create($ini2);
        $fin2 = date_create($fin2);
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin = date_format($fin, 'Y/m/d H:i:s');
        $dato2 = DB::table('agenda')->where('id','<>', $id)->where(function ($query) use ($request, $inicio, $fin){
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
            'observaciones' => 'max:200',
              
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
        ];

        $this->validate($request, $rules, $mensajes);

    }

    private function  validateInput3_2($request, $id) {

        $ini2 = date_create($request['inicio']);
        $fin2 = date_create($request['fin']);
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin = date_format($fin, 'Y/m/d H:i:s');
        $dato2 = DB::table('agenda')->where('id','<>', $id)->where(function ($query) use ($request, $inicio, $fin){
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
        ];

        $this->validate($request, $rules, $mensajes);

    }

    private function validateInput5_2($request, $id) {

        $ini2 = date_create($request['inicio']);
        $fin2 = date_create($request['fin']);
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin = date_format($fin, 'Y/m/d H:i:s');
        $dato2 = DB::table('agenda')->where('id','<>', $id)->where(function ($query) use ($request, $inicio, $fin){
                return $query->where('id_doctor1', '=', $request['id_doctor2'])
                      ->orWhere('id_doctor2', '=', $request['id_doctor2'])
                      ->orWhere('id_doctor3', '=', $request['id_doctor2']);
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

        $dato3 = DB::table('agenda')->where('id','<>', $id)->where(function ($query) use ($request, $inicio, $fin){
                return $query->where('id_doctor1', '=', $request['id_doctor3'])
                      ->orWhere('id_doctor2', '=', $request['id_doctor3'])
                      ->orWhere('id_doctor3', '=', $request['id_doctor3']);
                  })
                ->where(function ($query) use ($request, $inicio, $fin) {
                            return $query->whereRaw("(('".$inicio."' BETWEEN fechaini and fechafin)")
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("'".$request['fin']."' BETWEEN fechaini and fechafin)");}
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
        
        $cant_agenda2 = $dato3->count();

        $rules3 = [
        'id_doctor3'=> 'unique_doctor:'.$cant_agenda2,              
        ];
        $rules2 = [
        'id_doctor2'=> 'unique_doctor:'.$cant_agenda,            
        ];
        $mensajes2 = [
        'id_doctor2.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 1', 
        ];
        $mensajes3 = [
        'id_doctor3.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 2', 
        ];

        if($request['id_doctor2'] != "" && $request['id_doctor3'] == ""){
            $this->validate($request, $rules2, $mensajes2);
        }

        if($request['id_doctor2'] == "" && $request['id_doctor3'] != ""){
            $this->validate($request, $rules3, $mensajes3);    
        }

        $rules = [
            'id_doctor2'=> 'unique_doctor:'.$cant_agenda, 
            'id_doctor3'=> 'unique_doctor:'.$cant_agenda2,              
            ];
            $mensajes = [
            'id_doctor3.unique_doctor' => 'La fecha seleccionada esta ocupada para el Médico Asistente 2', 
            'id_doctor2.unique_doctor' => 'La fecha seleccionada esta ocupada para el Médico Asistente 1', 
            ];

        if($request['id_doctor2'] != "" && $request['id_doctor3'] != ""){
            $this->validate($request, $rules, $mensajes);
        }
    }

    private function validateDoctores($request){
        $rules = ['id_doctor3' =>  'different:id_doctor2', 
        'id_doctor2' =>  'different:id_doctor3'];
        $mensajes = [
        'id_doctor2.different' => 'Los Doctores asistentes no pueden ser la misma persona',
        'id_doctor3.different' => 'Los Doctores asistentes no pueden ser la misma persona'];
        
         
        $this->validate($request, $rules, $mensajes);

     }

     private function validateDoctores2($request){
        $rules = ['id_doctor1' =>  'different:id_doctor2', 
                  'id_doctor1' =>  'different:id_doctor3',
                  'id_doctor2' =>  'different:id_doctor1',
                  'id_doctor3' =>  'different:id_doctor1'
                ];
        $mensajes = [
        'id_doctor1.different' => 'El Doctor principal no puede ser un asistente',
        'id_doctor2.different' => 'El Doctor asistente no pueden ser principal',
        'id_doctor3.different' => 'El Doctor asistente no pueden ser principal'];
        
         
        $this->validate($request, $rules, $mensajes);

     }

    private function validateMax2_2($request, $id) {
    
        $fecha_req=$request['inicio'];
        $fecha_req=substr($fecha_req,0,10);
        $fecha_req=strtotime($fecha_req);
        $fecha_min=date('Y-m-d H:i',$fecha_req);
        $fecha_max = strtotime ( '+1 day' , strtotime ( $fecha_min ) ) ;
        $fecha_max = date ( 'Y-m-d H:i' , $fecha_max ); 

        $dato2 = DB::table('agenda')->where(function ($query) use ($request){
                $query->where('id_doctor1', '=', $request['id_doctor2'])
                      ->orWhere('id_doctor2', '=', $request['id_doctor2'])
                      ->orWhere('id_doctor3', '=', $request['id_doctor2']);})
                      ->where('proc_consul', '=', $request['proc_consul']) 
                      ->where('estado', '<>', '0')
                      ->where('id', '<>', $id)
                      ->where(function ($query) use ($request,$fecha_min,$fecha_max) {
                            $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
                            })
                    ->get();
        $cantidad= $dato2->count();
        $doctor= User::find($request['id_doctor2']);
        if($request['proc_consul']==0){
            $rules = [        
        'id_doctor2' =>  'max_consulta:'.$cantidad.','.$doctor->max_consulta.','
        ];
        }else if($request['proc_consul']==1){
            $rules = [        
        'id_doctor2' =>  'max_procedimiento:'.$cantidad.','.$doctor->max_procedimiento.',' 
        ];
        }
        $mensajes = [
        'id_doctor2.max_consulta' => 'La cantidad máxima de consultas a atender por día es : '.$doctor->max_consulta,  
        'id_doctor2.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : '.$doctor->max_procedimiento,       
        ];       
        $this->validate($request, $rules, $mensajes);

    }
    private function validateMax3_2($request, $id) {
    

        $fecha_req=$request['inicio'];
        $fecha_req=substr($fecha_req,0,10);
        $fecha_req=strtotime($fecha_req);
        $fecha_min=date('Y-m-d H:i',$fecha_req);
        $fecha_max = strtotime ( '+1 day' , strtotime ( $fecha_min ) ) ;
        $fecha_max = date ( 'Y-m-d H:i' , $fecha_max ); 

        $dato2 = DB::table('agenda')->where(function ($query) use ($request){
                $query->where('id_doctor1', '=', $request['id_doctor3'])
                      ->orWhere('id_doctor2', '=', $request['id_doctor3'])
                      ->orWhere('id_doctor3', '=', $request['id_doctor3']);})
                      ->where('proc_consul', '=', $request['proc_consul']) 
                      ->where('estado', '<>', '0')
                      ->where('id', '<>', $id)
                      ->where(function ($query) use ($request,$fecha_min,$fecha_max) {
                            $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
                            })
                    ->get();
        $cantidad= $dato2->count();

        $doctor= User::find($request['id_doctor3']);
        if($request['proc_consul']==0){
            $rules = [        
        'id_doctor3' =>  'max_consulta:'.$cantidad.','.$doctor->max_consulta.','
        ];
        }else if($request['proc_consul']==1){
            $rules = [        
        'id_doctor3' =>  'max_procedimiento:'.$cantidad.','.$doctor->max_procedimiento.',' 
        ];
        }
        $mensajes = [
        'id_doctor3.max_consulta' => 'La cantidad máxima de consultas a atender por día es : '.$doctor->max_consulta,  
        'id_doctor3.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : '.$doctor->max_procedimiento,       
        ];       
        $this->validate($request, $rules, $mensajes);

    } 

    public function consulta()
    {
        
        $opcion = '4'; //AGENDA DE SEGUROS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }
        
        $doctores = User::where('id_tipo_usuario',3)->where('estado',1)->get();
        $seguros = Seguro::where('tipo','<>','0')->where('inactivo','1')->get();
        $especialidades = Especialidad::where('estado','1')->get();
        $procedimientos = Procedimiento::where('estado','1')->orderby('nombre')->get();
        //dd($especialidades);
        
        $fecha = date('Y/m/d');
        $fecha_hasta = date('Y/m/d');
        $agendas = DB::table('agenda')->join('paciente','paciente.id','=','agenda.id_paciente')->join('seguros','seguros.id','=','agenda.id_seguro')->whereBetween('agenda.fechaini', [$fecha.' 00:00', $fecha.' 23:59'])->leftjoin('users as d1','d1.id','=','agenda.id_doctor1')->join('users as au','au.id','=','agenda.id_usuariomod')->join('sala','sala.id','=','agenda.id_sala')->leftjoin('procedimiento','procedimiento.id','=','agenda.id_procedimiento')->join('hospital','hospital.id','=','sala.id_hospital')->select('agenda.*','paciente.nombre1 as pnombre1','paciente.nombre2 as pnombre2','paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','seguros.nombre as senombre','d1.nombre1 as dnombre1','d1.apellido1 as dapellido1','au.nombre1 as aunombre1','au.nombre2 as aunombre2','au.apellido1 as auapellido1','procedimiento.observacion as probservacion','sala.nombre_sala as snombre','d1.color as d1color','seguros.color as scolor')->orderby('agenda.fechaini','desc')
        ->where('seguros.tipo','<>','0')->where('proc_consul','<','2')->paginate(30);

        $dp_proc = [];
        $ControlDocController = new ControlDocController;
        foreach($agendas as $a2){

            $historia = Historiaclinica::where('id_agenda',$a2->id)->first();
            if(!is_null($historia)){
                $hSeguro = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, '1', $hSeguro->tipo)->count();
                $cant_ok = Archivo_historico::where('id_historia',$historia->hcid)->where('estado','1')->get()->count();
                $cant_pend = $cantidad_doc - $cant_ok;
                $dp_proc += [
                    $a2->id => $cant_pend,
                ];   
            }       
        }

       

        return view('bo/consulta', ['agendas' => $agendas, 'proc_consul' => '2', 'cedula' => '', 'nombres' => '', 'fecha' => $fecha, 'pentax' => '2', 'dp_proc' => $dp_proc, 'fecha_hasta' => $fecha_hasta, 'doctores' => $doctores, 'id_doctor1' => null, 'seguros' => $seguros, 'id_seguro' => null, 'especialidades' => $especialidades, 'id_especialidad' => null, 'procedimientos' => $procedimientos, 'id_procedimiento' => null]);
    }

    public function search_consulta(Request $request) {

         
        $opcion = '4'; //AGENDA DE SEGUROS PRIVADOS
        
        if($this->rol_new($opcion)){
            return response()->view('errors.404');
        }
        $proc_consul=$request['proc_consul'];
        $cedula = $request['cedula'];
        $nombres = $request['nombres'];
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $pentax = $request['pentax'];
        $id_doctor1 = $request['id_doctor1'];
        $id_seguro = $request['id_seguro'];
        $espid = $request['espid'];
        $id_procedimiento = $request['id_procedimiento'];

        $doctores = User::where('id_tipo_usuario',3)->where('estado',1)->get();
        if($proc_consul=='null'){
            $proc_consul='1';
        }

        $seguros = Seguro::where('tipo','<>','0')->where('inactivo','1')->get();

        $agendas = DB::table('agenda')->join('paciente','paciente.id','=','agenda.id_paciente')->join('seguros','seguros.id','=','agenda.id_seguro')->leftjoin('users as d1','d1.id','=','agenda.id_doctor1')->join('users as au','au.id','=','agenda.id_usuariomod')->leftjoin('sala','sala.id','=','agenda.id_sala')->leftjoin('procedimiento','procedimiento.id','=','agenda.id_procedimiento')->leftjoin('hospital','hospital.id','=','sala.id_hospital')->select('agenda.*','paciente.nombre1 as pnombre1','paciente.nombre2 as pnombre2','paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','seguros.nombre as senombre','d1.nombre1 as dnombre1','d1.apellido1 as dapellido1','au.nombre1 as aunombre1','au.nombre2 as aunombre2','au.apellido1 as auapellido1','procedimiento.observacion as probservacion','sala.nombre_sala as snombre','d1.color as d1color','seguros.color as scolor')->orderby('agenda.fechaini','desc')->where('proc_consul','<','2')->where('seguros.tipo','<>','0');

        if($proc_consul!='2')
        {
            $agendas = $agendas->where('agenda.proc_consul',$proc_consul);
        }
                    
        if($proc_consul=='1'){
            if($pentax=='2'){
                $agendas = $agendas->where('hospital.id','2');
            }elseif($pentax=='0'){
                $agendas = $agendas->where('hospital.id','<>','2');
            }
                
        }

        if($fecha!=null && $fecha_hasta!=null){
            $agendas = $agendas->whereBetween('agenda.fechaini', [$fecha.' 00:00', $fecha_hasta.' 23:59']);    
        }

        if($cedula!=null){
            $agendas = $agendas->where('agenda.id_paciente',$cedula);    
        }

        if($id_doctor1!=null){
            $agendas = $agendas->where('agenda.id_doctor1',$id_doctor1);    
        }

        if($id_seguro!=null){
            $agendas = $agendas->where('agenda.id_seguro',$id_seguro);    
        }

        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $agendas = $agendas->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%'.$nombres.'%']);    
                    });
                      
            }
            else{

                $agendas = $agendas->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }

        if($espid!=null){
            $agendas = $agendas->where('agenda.espid',$espid);
        }

        if($id_procedimiento!=null){

            /*$agendas = $agendas->join('pentax as px','px.id_agenda','agenda.id')->join('pentax_procedimiento as ppx','ppx.id_pentax','px.id')->where('ppx.id_procedimiento',$id_procedimiento);*/
            //dd($agendas->get());
            $agendas = $agendas->leftjoin('agenda_procedimiento as apx','apx.id_agenda','agenda.id')
                ->where(function ($query) use($id_procedimiento){
                    $query->where('apx.id_procedimiento',$id_procedimiento)
                    ->orWhere('agenda.id_procedimiento','=', $id_procedimiento);
                });
        }
        //dd($id_procedimiento,$agendas->get());
        

        $agendas = $agendas->paginate(30);

        $dp_proc = [];
        $arr_prb = [];
        $ControlDocController = new ControlDocController;
        $i=0;
        foreach($agendas as $a2){

            $historia = Historiaclinica::where('id_agenda',$a2->id)->first();
            
            if(!is_null($historia)){
            $i++;    

                $hSeguro = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, $a2->proc_consul, $hSeguro->tipo)->count();
                $cant_ok = Archivo_historico::where('id_historia',$historia->hcid)->where('estado','1')->get()->count();
                $cant_pend = $cantidad_doc - $cant_ok;
                //$arr_prb += [$a2->id => [$cantidad_doc, $cant_ok, $cant_pend]];
                $dp_proc += [
                    $a2->id => $cant_pend,
                ]; 
              

            }       
        }
        //dd($arr_prb);

        $especialidades = Especialidad::where('estado','1')->get();
        $procedimientos = Procedimiento::where('estado','1')->orderby('nombre')->get();

         
    
      return view('bo/consulta', ['agendas' => $agendas, 'proc_consul' => $proc_consul, 'cedula' => $cedula, 'nombres' => $nombres, 'fecha' => $fecha, 'pentax' => $pentax, 'dp_proc' => $dp_proc, 'fecha_hasta' => $fecha_hasta, 'doctores' => $doctores, 'id_doctor1' => $id_doctor1, 'seguros' => $seguros, 'id_seguro' => $id_seguro, 'especialidades' => $especialidades, 'id_especialidad' => $espid, 'procedimientos' => $procedimientos, 'id_procedimiento' => $id_procedimiento]);
    }

    public function reporte(Request $request) {

        $proc_consul=$request['proc_consul'];
        $cedula = $request['cedula'];
        $nombres = $request['nombres'];
        $fecha = $request['fecha'];
        $fecha_hasta = $request['fecha_hasta'];
        $pentax = $request['pentax'];
        $id_doctor1 = $request['id_doctor1'];
        $id_seguro = $request['id_seguro'];
        $espid = $request['espid'];
        $id_procedimiento = $request['id_procedimiento'];

        //dd($request->all());

        $doctores = User::where('id_tipo_usuario',3)->where('estado',1)->get();
        if($proc_consul=='null'){
            $proc_consul='1';
        }

        $agendas = DB::table('agenda')->join('paciente','paciente.id','=','agenda.id_paciente')
                    ->join('seguros','seguros.id','=','agenda.id_seguro')
                    ->leftjoin('users as d1','d1.id','=','agenda.id_doctor1')
                    ->join('users as au','au.id','=','agenda.id_usuariomod')
                    ->leftjoin('sala','sala.id','=','agenda.id_sala')
                    ->leftjoin('procedimiento','procedimiento.id','=','agenda.id_procedimiento')
                    ->leftjoin('hospital','hospital.id','=','sala.id_hospital')
                    ->leftjoin('pentax','pentax.id_agenda','agenda.id')
                    ->leftjoin('seguros as seguro_pentax','seguro_pentax.id','=','pentax.id_seguro')
                    ->leftjoin('users as dp1','dp1.id','=','pentax.id_doctor1')
                    ->leftjoin('users as d2','d2.id','pentax.id_doctor2')
                    ->leftjoin('users as d3','d3.id','pentax.id_doctor3')
                    ->select('agenda.*','paciente.nombre1 as pnombre1','paciente.nombre2 as pnombre2','paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','seguros.nombre as senombre','d1.nombre1 as dnombre1','d1.apellido1 as dapellido1','au.nombre1 as aunombre1','au.nombre2 as aunombre2','au.apellido1 as auapellido1','procedimiento.observacion as probservacion','sala.nombre_sala as snombre','d1.color as d1color','seguros.color as scolor','dp1.nombre1 as dp1nombre1','dp1.apellido1 as dp1apellido1','pentax.id as pxid','d2.apellido1 as d2apellido1','d3.apellido1 as d3apellido1','pentax.estado_pentax','pentax.ingresa_alt','paciente.ciudad', 'seguro_pentax.nombre as seguro_pentax','paciente.direccion','paciente.telefono1','paciente.telefono2','paciente.telefono3','paciente.parentesco','paciente.id_usuario','paciente.referido')
                    ->orderby('agenda.fechaini','desc')
                    ->where('proc_consul','<','2')
                    ->where('seguros.tipo','<>','0');

        if($proc_consul!='2')
        {
            $agendas = $agendas->where('agenda.proc_consul',$proc_consul);
        }
                    

        if($proc_consul=='1'){
            if($pentax=='2'){
                $agendas = $agendas->where('hospital.id','2');
            }elseif($pentax=='0'){
                $agendas = $agendas->where('hospital.id','<>','2');
            }
                
        }

        if($fecha!=null && $fecha_hasta!=null){
            $agendas = $agendas->whereBetween('agenda.fechaini', [$fecha.' 00:00', $fecha_hasta.' 23:59']);    
        }


        if($cedula!=null){
            $agendas = $agendas->where('agenda.id_paciente',$cedula);    
        }

        if($id_doctor1!=null){
            $agendas = $agendas->where('agenda.id_doctor1',$id_doctor1);    
        }

        if($id_seguro!=null){
            $agendas = $agendas->where('agenda.id_seguro',$id_seguro);    
        }

        

        if($nombres!=null)
        {
  
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $agendas = $agendas->where(function($jq1) use($nombres){
                        $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%'.$nombres.'%'])
                            ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%'.$nombres.'%']);    
                    });
                      
            }
            else{

                $agendas = $agendas->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%'.$nombres.'%']);
            }    
  
        }

        if($espid!=null){
            $agendas = $agendas->where('agenda.espid',$espid);
        }

        if($id_procedimiento!=null){

            /*$agendas = $agendas->join('pentax as px','px.id_agenda','agenda.id')->join('pentax_procedimiento as ppx','ppx.id_pentax','px.id')->where('ppx.id_procedimiento',$id_procedimiento);*/
            $agendas = $agendas->leftjoin('agenda_procedimiento as apx','apx.id_agenda','agenda.id')
                ->where(function ($query) use($id_procedimiento){
                    $query->where('apx.id_procedimiento',$id_procedimiento)
                    ->orWhere('agenda.id_procedimiento','=', $id_procedimiento);
                });
        }
        
        $agendas = $agendas->get();


        $dp_proc = [];
        $ControlDocController = new ControlDocController;
        $i=0;
        foreach($agendas as $a2){

            $historia = Historiaclinica::where('id_agenda',$a2->id)->first();
            
            if(!is_null($historia)){
            $i++;    

                $hSeguro = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, $a2->proc_consul, $hSeguro->tipo)->count();
                $cant_ok = Archivo_historico::where('id_historia',$historia->hcid)->where('estado','1')->get()->count();
                $cant_pend = $cantidad_doc - $cant_ok;
                
                $dp_proc += [
                    $a2->id => $cant_pend,
                ]; 
              

            }       
        }
        //dd($agendas);
        $fecha_d = date('Y/m/d'); 
        Excel::create('Agenda-'.$fecha_d, function($excel) use($agendas, $fecha) {

            $excel->sheet('Consulta Agenda', function($sheet) use($agendas, $fecha) {
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
                    $cell->setValue('CONSULTAS Y PROCEDIMIENTOS AGENDADOS'.' - '.$fecha2);
                    
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function($cell) use($fecha2) {
                    // manipulate the cel
                    $cell->setValue('DATOS DE LA AGENDA');
                    $cell->setBackground('#80ccff');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O2', function($cell) use($fecha2) {
                    // manipulate the cel
                    $cell->setValue('DATOS PENTAX');
                    $cell->setBackground('#c2f0f0');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U2', function($cell) use($fecha2) {
                    // manipulate the cel
                    $cell->setValue('DIRECCIONES Y TELEFONOS');
                    $cell->setBackground('#c2f000');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
                $sheet->cells('A1:K3', function($cells) {
                // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:AA4', function($cells) {
                // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function($cell) {
                    // manipulate the cel

                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SALA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO/CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('MODIFICA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CONSECUTIVO/PRIMERA VEZ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CIUDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ULTIMA MODIFICACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO/CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('ALTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //DIRECCIONES Y TELEFONOS
                $sheet->cell('U4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('CIUDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('DIRECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('MAIL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('REFERIDO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Z4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('AMB/HOSP');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AA4', function($cell) {
                    // manipulate the cel
                    $cell->setValue('OMNI');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                
                foreach($agendas as $value){
                    $historiaclinica = Historiaclinica::where('id_agenda',$value->id)->first();
                    $empresa = null;
                    if($value->id_empresa!=null){
                        $empresa = Empresa::find($value->id_empresa);
                    }
                    $txtcolor='#000000';
                    if($value->estado_cita != 0){
                            $txtcolor=$value->scolor;

                            if($value->paciente_dr == 1){
                                $txtcolor=$value->d1color;

                                
                            }
                        }
                    
                    $sheet->cell('A'.$i, function($cell) use($value, $txtcolor){
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini,0,10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    
                    $sheet->cell('B'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini,11,5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('C'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        if($value->papellido2 != "(N/A)"){
                            $vnombre= $value->papellido1.' '.$value->papellido2;   
                        }
                        else{
                            $vnombre= $value->papellido1;   
                        }

                        if($value->pnombre2 != "(N/A)"){
                            $vnombre= $vnombre.' '.$value->pnombre1.' '.$value->pnombre2;   
                        }
                        else
                        {
                            $vnombre= $vnombre.' '.$value->pnombre1;
                        }   
                        $cell->setFontColor($txtcolor);
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    
                    });


                    $sheet->cell('D'.$i, function($cell) use($value, $txtcolor) {
                            
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('E'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->dnombre1.' '.$value->dapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F'.$i, function($cell) use($value, $txtcolor, $historiaclinica, $empresa) {
                        // manipulate the cel
                        $cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                         

                    if(!is_null($value->probservacion)){
                        $vproc = $value->probservacion;
                    }
                    else{
                        $vproc = 'Consulta';  
                    }
                    $agprocedimientos = AgendaProcedimiento::where('id_agenda',$value->id)->get();
                    if(!$agprocedimientos->isEmpty()){
                        foreach($agprocedimientos as $agendaproc){
                            $vproc = $vproc.' + '.Procedimiento::find($agendaproc->id_procedimiento)->observacion;    
                        }
                    } 
                    $sheet->cell('G'.$i, function($cell) use($value, $vproc, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($vproc);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('H'.$i, function($cell) use($value, $txtcolor, $historiaclinica, $empresa) {
                        // manipulate the cel
                        $consultorio='';
                        if($value->consultorio!='0'){
                            $consultorio='/CONSULTORIO';
                        }
                        
                        if(is_null($historiaclinica)){
                            $cell->setValue($value->senombre.$consultorio);   
                        }else{
                            if($empresa!=null){
                                $cell->setValue($historiaclinica->seguro->nombre.$consultorio.'/'.$empresa->nombre_corto);
                            }else{
                                $cell->setValue($historiaclinica->seguro->nombre.$consultorio);
                            }
                            
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('I'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->aunombre1.' '.$value->auapellido1);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    if($value->estado_cita=='0'){ $vest = 'Por Confirmar'; } 
                    elseif($value->estado_cita=='1'){ $vest = 'Confirmado';} 
                    elseif($value->estado_cita=='3'){ $vest = 'Suspendido';}
                    elseif($value->estado_cita=='-1'){ $vest = 'No Asiste';}
                    elseif($value->estado_cita=='4'){ $vest = 'Asistió';}
                    elseif($value->estado_cita=='2'){ if($value->estado=='1'){ $vest = 'Completar Datos';} else{ $vest = 'Reagendar'; }}
                    $sheet->cell('J'.$i, function($cell) use($value, $vest, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($vest);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $vcita="";
                    if($value->tipo_cita=='0'){ $vcita = 'PRIMERA VEZ'; } 
                    else{ $vcita = 'CONSECUTIVO';} 
                    $sheet->cell('K'.$i, function($cell) use($value, $vcita, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($vcita);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('L'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->observaciones);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('M'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ciudad);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('N'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->updated_at);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('O'.$i, function($cell) use($value, $txtcolor) {

                        if($value->proc_consul == 0){
                            $historia = historiaclinica::where('id_agenda', '=', $value->id)->first();
                            if(!is_null($historia)){
                                $procedimiento = hc_procedimientos::where('id_hc', $historia->hcid)->first();
                                if(!is_null($procedimiento)){
                                    // manipulate the cel
                                    if(!is_null($procedimiento->id_doctor_examinador)){
                                        $cell->setValue($procedimiento->doctor->nombre1.' '.$procedimiento->doctor->apellido1);    
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setFontColor($txtcolor);
                                    }else{
                                        // manipulate the cel
                                        $cell->setValue($value->dp1nombre1.' '.$value->dp1apellido1);    
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setFontColor($txtcolor);
                                    }                                                                       
                                }else{
                                    // manipulate the cel
                                    $cell->setValue($value->dp1nombre1.' '.$value->dp1apellido1);    
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setFontColor($txtcolor);
                                }
                            }else{
                                // manipulate the cel
                                $cell->setValue($value->dp1nombre1.' '.$value->dp1apellido1);    
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setFontColor($txtcolor);
                            }
                        }
                        else{
                            // manipulate the cel
                            $cell->setValue($value->dp1nombre1.' '.$value->dp1apellido1);    
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        }
                        
                    });

                    $txtpproc=""; 
                    if(!is_null($value->pxid)){
                        $pentaxprocedimientos = PentaxProc::where('id_pentax',$value->pxid)->get();
                        //dd($pentaxprocedimientos);
                        if(!is_null($pentaxprocedimientos)){
                            $ban='0';
                            foreach($pentaxprocedimientos as $proc){
                            if($ban=='0'){
                                $txtpproc=Procedimiento::find($proc->id_procedimiento)->observacion;
                                $ban='1';        
                            }else{
                                $txtpproc=$txtpproc.' + '.Procedimiento::find($proc->id_procedimiento)->observacion;
                            }  
                        }        
                        }
                    }
                    


                    $sheet->cell('P'.$i, function($cell) use($value, $txtcolor, $txtpproc) {
                        // manipulate the cel
                        $cell->setValue($txtpproc);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $txtasis= $value->d2apellido1;   
                    if($value->d3apellido1!=null){
                        $txtasis=$txtasis.' + '.$value->d3apellido1;
                    }
                    $sheet->cell('Q'.$i, function($cell) use($value, $txtcolor, $txtasis, $historiaclinica, $empresa) {
                        // manipulate the cel
                        if(is_null($historiaclinica)){
                            $cell->setValue($value->senombre);    
                        }else{
                            if($empresa!=null){
                                $cell->setValue($historiaclinica->seguro->nombre.'/'.$empresa->nombre_corto);
                            }else{
                                $cell->setValue($historiaclinica->seguro->nombre);
                            }
                            
                        }
                        //$cell->setValue($value->seguro_pentax);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });    
                    $sheet->cell('R'.$i, function($cell) use($value, $txtcolor, $txtasis) {
                        // manipulate the cel
                        $cell->setValue($txtasis);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    if($value->estado_pentax=='0') {$txtest='EN ESPERA';}  
                    elseif($value->estado_pentax=='1') {$txtest='PREPARACIÓN'; } 
                    elseif($value->estado_pentax=='2') {$txtest='EN PROCEDIMIENTO';}  
                    elseif($value->estado_pentax=='3') {$txtest='RECUPERACION';}  
                    elseif($value->estado_pentax=='4') {$txtest='ALTA';}  
                    elseif($value->estado_pentax=='5') {$txtest='SUSPENDER';} 
                    else {$txtest='';}

                    $sheet->cell('S'.$i, function($cell) use($value, $txtcolor, $txtest) {
                        // manipulate the cel
                        $cell->setValue($txtest);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('T'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ingresa_alt);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    //DIRECCIONES Y TELEFONOS
                    $sheet->cell('U'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ciudad);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('V'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->direccion);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('W'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->telefono1.'-'.$value->telefono2.'-'.$value->telefono3);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('X'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $usuario=null;
                        $usuario = User::find($value->id_usuario);    
                        if($usuario!=null){
                            $cell->setValue($usuario->email);    
                        }else{
                            $cell->setValue('');    
                        }
                            
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                        
                    });
                    $sheet->cell('Y'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->referido);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('Z'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $z_txt='';
                        if($value->est_amb_hos=='0'){
                            $z_txt = 'AMBULATORIO';    
                        }else{
                            $z_txt = 'HOSPITALIZADO';
                        }
                        $cell->setValue($z_txt);    
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AA'.$i, function($cell) use($value, $txtcolor) {
                        // manipulate the cel
                        $aa_txt='';
                        if($value->omni=='SI'){
                            $aa_txt = 'OMNI';    
                        }
                        $cell->setValue($aa_txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $i= $i+1;



                }
                    
                             
                    
                    
            });
        })->export('xlsx');
    }

    

    
   
  
               

    


}