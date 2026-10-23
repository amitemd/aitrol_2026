<?php

namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User; 
use Sis_medico\Bodega; 
use Sis_medico\hc_procedimientos; 
use Sis_medico\Procedimiento;
use Sis_medico\hc_protocolo;
use Sis_medico\Historiaclinica;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Empresa;
use Sis_medico\Pentax_log;
use Sis_medico\Hc_Procedimiento_Final;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage; 
use Carbon\Carbon;
use Sis_medico\Seguro;
use Sis_medico\Agenda;
use Sis_medico\Log_Agenda;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\hc_receta;
use Sis_medico\Hc_Log;
use Sis_medico\Paciente;
use Sis_medico\Cortesia_Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Principio_Activo;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Sala;
use Response;

class ProcedimientosFuncionalesController extends Controller
{
    private function rol_new($opcion){ //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
        //dd($opcion);
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion',$opcion)->where('id_tipo_usuario',$rolUsuario)->first();
        if(is_null($opcion_usuario)){
          return true;
        
        }

    }

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

    public function index($id_paciente){
        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        
        $paciente = Paciente::find($id_paciente);
        $pro_completo_1 = DB::table('historiaclinica as h')
                            ->where('h.id_paciente', $id_paciente)
                            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
                            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
                            ->where('gp.tipo_procedimiento', '1')
                            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda','hc_p.id_seguro as hc_p_id_seguro')
                            ->OrderBy('h.created_at', 'desc')->get();

        //dd($pro_completo_1);

        $pro_final_1 =DB::table('historiaclinica as h')
                            ->where('h.id_paciente', $id_paciente)
                            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                            ->where('hc_proto.tipo_procedimiento', '1')
                            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda','hc_p.id_seguro as hc_p_id_seguro')->OrderBy('h.created_at', 'desc')->get();
    //dd($pro_completo_1);

    
    $agenda = $paciente->agenda->last();
   // dd($agenda->id);

     //dd($pro_final_1);



       	return view('hc4/procedimiento_funcional/index', ['paciente' => $paciente, 'agenda' => $agenda, 'procedimientos1' => $pro_completo_1, 'procedimientos2' => $pro_final_1]);
    }


    public function editar($id_procedimiento, $id_paciente){
        $protocolo = hc_protocolo::where('id_hc_procedimientos',$id_procedimiento)->first();

        $hc_historia_clinica = Historiaclinica::where('id_paciente', $id_paciente)->OrderBy('created_at', 'desc')->first();
        $procedimiento = hc_procedimientos::find($id_procedimiento);
        $hc_seguro = Seguro::where('id', $procedimiento->id_seguro)->OrderBy('created_at', 'desc')->first();
        $tipo = 1;
        $px = Procedimiento::where('procedimiento.estado','1')->get();
        $doctores = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado','1')->get();
        return view('hc4/procedimiento_funcional/editar', ['protocolo'=> $protocolo, 'procedimiento' => $procedimiento, 'tipo' => $tipo, 'id_paciente' => $id_paciente,'hc_seguro' => $hc_seguro, 'px' => $px, 'doctores' => $doctores]);
    }

     public function guardar_proc_fun(Request $request){

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $protocolo = hc_protocolo::find($request['id_protocolo']);
        $procedimiento = hc_procedimientos::find($request['id_procedimiento']);
        $hc_seguro = Seguro::where('id', $procedimiento->id_seguro)->OrderBy('created_at', 'desc')->first();


         $protocolo_new = $protocolo;
        $procedimiento_new = $procedimiento;

        if($protocolo_new != null && $procedimiento_new != null){ 
          $procedimientos_fun_new = [
              'anterior' => 'PROC_FUNC -> Conclusion: ' .$protocolo_new->conclusion.' hallazgos:' .$protocolo_new->hallazgos,
              'nuevo' => 'PROC_FUNC -> Conclusion: ' .$request['conclusion'].' hallazgos:' .$request['hallazgos'],
              'hc_id' => $protocolo_new->hcid,
              'id_paciente' => $request['id_paciente'],
              'id_procedimiento' => $procedimiento_new->id,
              'id_protocolo' => $protocolo_new->id,
              'id_usuariomod' => $idusuario,
              'id_usuariocrea' => $idusuario,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,           
          ];
          Hc_Log::create($procedimientos_fun_new);
        }


        $input = [
            'conclusion' => $request['conclusion'],
            'hallazgos' => $request['hallazgos'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario
        ];

         if(($request['id_doctor_examinador'] == '9666666666') || ($request['id_doctor_examinador'] == 'GASTRO')){
            $input2 = [
                'id_doctor_examinador' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario
            ];
        }else{
            $input2 = [
                'id_doctor_examinador' => $request['id_doctor_examinador'],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario
            ];
        }
         $procedimiento->update($input2);
        $protocolo->update($input); 
        
        $procedimientos = $request['procedimiento']; 

        $anteriores = Hc_Procedimiento_Final::where('id_hc_procedimientos', $request['id_procedimiento']);
        $anteriores->delete();
        if (!is_null($procedimientos)) { 
          foreach ($procedimientos as $value) {
              $input_pro_final = [
                'id_hc_procedimientos' => $request['id_procedimiento'],
                'id_procedimiento' => $value,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
              ];

              Hc_Procedimiento_Final::create($input_pro_final);
          }
        }
        return view('hc4/procedimiento_funcional/unico', ['protocolo'=> $protocolo, 'procedimiento' => $procedimiento, 'tipo' => $request['tipo'],'hc_seguro' => $hc_seguro]);
    }
    
 public function selecciona_procedimiento_fun($tipo, $paciente){
        
       
        //'0: endoscopico, 1: funcional, 2:imagen, 3:consulta', 4:broncoscopias
        $px = Procedimiento::where('procedimiento.estado','1')->get();
       
        $paciente = Paciente::find($paciente);

        //dd($paciente);
        return view('hc4/procedimiento_funcional/selecciona_fun',['px' => $px, 'paciente' => $paciente, 'tipo' => $tipo]);

    }

 public function crear_procedimiento_funcional(Request $request){
        
        //return $request->all();
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id; 
        $id_empresa = Empresa::where('prioridad', '1')->first()->id;
        $procedimientos =  $request['procedimiento'];
        //$tipo_procedimiento = $request['procedimiento'];
        $procedimientop = $procedimientos[0];
        $paciente = Paciente::find($request->paciente);

        $sala_proc_fun = Sala::where('nombre_sala', 'like', '%FUNCIONALES%')->where('estado', 1)->first();


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

        if($procedimientos!=null){

          $input_agenda = [
              'fechaini' => Date('Y-m-d H:i:s'),
              'fechafin' => Date('Y-m-d H:i:s'),
              'id_paciente' => $paciente->id,
              'id_doctor1' => $id_doctor,
              'proc_consul' => '4',
              'estado_cita' => '4',
              'id_empresa'  => $id_empresa,
              'espid' => '4',
              'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'id_seguro' => $paciente->id_seguro,
              'estado' => '4',
              'ip_creacion' => $ip_cliente,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea' => $id_doctor,
              'id_usuariomod' => $id_doctor,
              'id_procedimiento' => $procedimientop,
              'id_sala' => $sala_proc_fun->id,
          ];

          $id_agenda = agenda::insertGetId($input_agenda);
          //return $id_agenda;

          $txt_pro = '';
          foreach ($procedimientos as $value){
              
              if($procedimientop != $value ){
                  $txt_pro = $txt_pro.'+'.$value;
                  AgendaProcedimiento::create([
                  'id_agenda' => $id_agenda,
                  'id_procedimiento' => $value,
                  'ip_creacion' => $ip_cliente,
                  'ip_modificacion' => $ip_cliente,
                  'id_usuariocrea' => $id_doctor,
                  'id_usuariomod' => $id_doctor
                  ]);
              }

          }

          $input_log = [
              'id_agenda' => $id_agenda,
              'estado_cita_ant' => '0',
              'estado_cita' => '0',
              'fechaini' => Date('Y-m-d H:i:s'),
              'fechafin' => Date('Y-m-d H:i:s'),
              'estado' => '4',
              'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'id_doctor1' => $id_doctor,
              'descripcion' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
              'campos_ant' => 'PRO: '.$procedimientop.$txt_pro,
          
              'id_usuariomod' => $id_doctor,
              'id_usuariocrea' => $id_doctor,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,           
          ];

          $idusuario = $id_doctor;

          Log_agenda::create($input_log);

          $input_historia = [
              
              'parentesco' => $paciente->parentesco,
              'id_usuario' => $paciente->id_usuario,
              'id_agenda' => $id_agenda,
              'id_paciente' => $paciente->id,
              'id_seguro' => $paciente->id_seguro,
              
              'id_doctor1' => $id_doctor,
              'id_usuariocrea' => $id_doctor,
              'ip_modificacion' => $ip_cliente,
              'id_usuariomod' => $id_doctor,
              'ip_creacion' => $ip_cliente,
              
          ]; 


          $id_historia=Historiaclinica::insertGetId($input_historia); 

          $input_pentax = [
              'id_agenda' => $id_agenda,
              'hcid' => $id_historia,
              'id_sala' => $sala_proc_fun->id,
              'id_doctor1' => $idusuario,
              'id_seguro' => $paciente->id_seguro,
              'observacion' => "PROCEDIMIENTO CREADO POR EL DOCTOR",
              'id_usuariocrea' => $idusuario,
              'ip_modificacion' => $ip_cliente,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,

          ];

          $id_pentax=Pentax::insertGetId($input_pentax);

          $list_proc = '';
          foreach($procedimientos as $value)
          {
              $input_pentax_pro2 = [
                  'id_pentax' => $id_pentax,
                  'id_procedimiento' => $value,
                  'id_usuariocrea' => $idusuario,
                  'ip_modificacion' => $ip_cliente,
                  'id_usuariomod' => $idusuario,
                  'ip_creacion' => $ip_cliente,
              ];

              PentaxProc::create($input_pentax_pro2); 
              $list_proc = $list_proc."+".$value;                       
          }

          $input_log_px=[
              'id_pentax' => $id_pentax,
              'tipo_cambio' => "CREADO POR EL DOCTOR",
              'descripcion' => "EN ESPERA",
              'estado_pentax' => '0',
              'procedimientos' => $list_proc,
              'id_doctor1' => $idusuario,
              'observacion' => "CREADO POR EL DOCTOR",
              'id_sala' => $sala_proc_fun->id,
              'id_seguro' => $paciente->id_seguro,
             
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,
              'id_usuariomod' => $idusuario,
              'id_usuariocrea' => $idusuario,
          ]; 

          Pentax_log::create($input_log_px);

          $input_hc_procedimiento = [
              'id_hc' => $id_historia,
              'id_seguro' => $paciente->id_seguro,
              'id_doctor_examinador' => $idusuario,
              'ip_modificacion' => $ip_cliente,
              'id_usuariocrea' => $idusuario,
              'id_usuariomod' => $idusuario,
              'ip_creacion' => $ip_cliente,
              
          ]; 

          $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

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
              'tipo_procedimiento' => $request->tipo_procedimiento,
          ]; 
          hc_protocolo::insert($input_hc_protocolo);

          foreach($procedimientos as $value)
          {
              $input_pro_final = [
                  'id_hc_procedimientos' => $id_hc_procedimiento,
                  'id_procedimiento' => $value,
                  'id_usuariocrea' => $idusuario,
                  'ip_modificacion' => $ip_cliente,
                  'id_usuariomod' => $idusuario,
                  'ip_creacion' => $ip_cliente,
              ];

              Hc_Procedimiento_Final::create($input_pro_final);                       
          }

          return "ok";  
        }

        return "Ingrese el Procedimiento";
          


    }


    public function mostrar_div($id){
      $imagen = hc_imagenes_protocolo::find($id);
      $enviar = "<div class='col-md-4 col-sm-6 col-12' style='margin: 10px 0;text-align: center;' >";                              
      $explotar = explode( '.', $imagen->nombre);
      $extension = end($explotar);    
      //dd("hola");
      return view('hc4/procedimiento_funcional/nuevo_div',['imagen' => $imagen]);
    }

}
