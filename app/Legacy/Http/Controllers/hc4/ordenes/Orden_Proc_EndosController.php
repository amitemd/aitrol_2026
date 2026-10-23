<?php

namespace Sis_medico\Http\Controllers\hc4\ordenes;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Procedimiento;
use Sis_medico\Paciente;
use Sis_medico\Orden;
use Sis_medico\Orden_Tipo;
use Sis_medico\Orden_Procedimiento;
use Carbon\Carbon;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Cie10;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Firma_Usuario;
use Response;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Historiaclinica;
use Sis_medico\Orden_012;

class Orden_Proc_EndosController extends Controller
{

    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
    private function rol_new($opcion){ 
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    //Muestra el historial de las ordenes de Procedimientos Endoscopicos
    //Solo para Doctores
    public function index($id_paciente){
        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
         $cedUsuario = Auth::user()->id;
         //dd($rolUsuario);

         $validar = DB::table('users as u')
           ->join('user_espe as ue', 'u.id','ue.usuid')
           ->join ('tipousuario as tu', 'u.id_tipo_usuario' , 'tu.id')
           ->join('especialidad as e','ue.espid','e.id')
           ->where('u.id', $cedUsuario)
           ->where('id_tipo_usuario', '3')
           ->where('e.id', '5')
           ->first();

         //dd($validar);
         $mostrar = false;
         if(count($validar)==0|| $validar==null){
          $mostrar = false;
         }else{
          $mostrar= true;
         }


        $paciente = Paciente::find($id_paciente);

        /*if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
        }*/

        
        $listado = Orden::where('tipo_procedimiento',0)
                          ->where('id_paciente',$paciente->id)
                          ->where('estado',1)
                          ->OrderBy('id','desc')
                          //->OrderBy('created_at','desc')
                          ->get();


        return view('hc4/ordenes/orden_procedimiento_endoscopico/index',['paciente' => $paciente,'listado' => $listado, 'mostrar'=>$mostrar]);
    }

    
    //AGREGAR PROCEDIMIENTO
    //funcion solo para los doctores
    /*public function crear_orden_endoscopico($tipo, $id_paciente){

        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        
        //Obtenemos el nombre del doctor y el seguro
        $data = DB::table('agenda as a')
                    ->where('a.id_paciente',$id_paciente)
                    ->join('historiaclinica as h','h.id_agenda','a.id')
                    //->join('users as u','u.id','h.id_doctor1')
                    ->join('seguros as s','s.id','h.id_seguro')
                    ->join('empresa as em','em.id','a.id_empresa')
                    ->where('a.espid','<>','10')
                    ->select('h.*','s.nombre','em.nombre_corto')
                    ->first();

        $paciente = Paciente::find($id_paciente);

        if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
        }


        $id_doctor = Auth::user()->id; 
        $doctor_solicitante = DB::table('users as us')
                              ->where('us.id',$id_doctor)
                              ->first();

        //Obtengo fecha de la Orden
        $fecha_orden = Date('Y-m-d h:i:s');

        
        
        
        $px = Procedimiento::where('procedimiento.estado','1')->get();
        
        $tipo_eda_diagnostica = 1;
        $tipo_colonoscopia_diagnostica = 2;
        $tipo_broncoscopia = 14;
        $tipo_enteroscopia = 3;
        $tipo_ecoendoscopia = 9;
        $tipo_cpre = 10;
     
        $evoluciones =  DB::table('historiaclinica as h')
                        ->where('h.id_paciente', $id_paciente)
                        ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
                        ->where('hc_evo.secuencia',0)
                        ->whereNotNull('hc_evo.cuadro_clinico')
                        ->orderby('h.hcid','desc')
                        
                        ->select('hc_evo.*')
                        ->first();
  
 
      
     

        return view('hc4/ordenes/orden_procedimiento_endoscopico/orden_procedendoscopico',['px' => $px, 'paciente' => $paciente,'fecha_orden' => $fecha_orden,'data' => $data,'edad' => $edad,'evoluciones' =>  $evoluciones,'doctor_solicitante' =>  $doctor_solicitante,'tipo_eda_diagnostica' => $tipo_eda_diagnostica,'tipo_colonoscopia_diagnostica' => $tipo_colonoscopia_diagnostica,'tipo_broncoscopia' => $tipo_broncoscopia ,'tipo_enteroscopia' => $tipo_enteroscopia,'tipo_ecoendoscopia' => $tipo_ecoendoscopia,'tipo_cpre' => $tipo_cpre]);

    }*/

     //AGREGAR PROCEDIMIENTO
    //funcion solo para los doctores
    public function crear_orden_endoscopico($tipo, $id_paciente){

        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];

        $id_doctor = Auth::user()->id; 

        //Obtengo fecha de la Orden
        $fecha_orden = Date('Y-m-d H:i:s');
        //dd($fecha_orden);


        //$paciente = Paciente::find($id_paciente);

        
        $orden_endoscopica_crear_new = [
              
              'anterior' => 'ORDEN_PROC_ENDOSCOPICOS -> El Dr. creo nueva orden de procedimiento endoscopico',
              'nuevo' => 'ORDEN_PROC_ENDOSCOPICOS -> El Dr. creo nueva orden de procedimiento endoscopico',
              'id_paciente' => $id_paciente,
              'id_usuariocrea' => $id_doctor,
              'id_usuariomod' => $id_doctor,
              'ip_creacion' => $ip_cliente,
              'ip_modificacion' => $ip_cliente,

      ];

      Hc_Log::create($orden_endoscopica_crear_new);

     
      $evoluciones =  DB::table('historiaclinica as h')
                        ->where('h.id_paciente', $id_paciente)
                        ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
                        ->where('hc_evo.secuencia',0)
                        ->whereNotNull('hc_evo.cuadro_clinico')
                        ->orderby('hc_evo.updated_at','desc')
                        //->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                        //->where('hc_proto.tipo_procedimiento', '0')
                        ->select('hc_evo.*')
                        ->first();
      

      $x_diagnosticos = null;
      $evol_id = null;
      $evol_motivo = null;
      $evol_cuadro_clinico = null;  
      $texto = ""; 
                  
        if(!is_null($evoluciones)){
          
          $x_diagnosticos = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento',$evoluciones->hc_id_procedimiento)->groupBy('cie10')->get();

          $evol_id = $evoluciones->id; 
          
          $evol_motivo = $evoluciones->motivo;
          
          $evol_cuadro_clinico = $evoluciones->cuadro_clinico;

        }

        if(!is_null($x_diagnosticos)){ 
            
            $mas = true;
            foreach($x_diagnosticos as $value)
            {
               
              $c3 = Cie_10_3::find($value->cie10);
              
              if(!is_null($c3)){
                $descripcion = $c3->descripcion;
              }

              $c4 = Cie_10_4::find($value->cie10);
           
              if(!is_null($c4)){
                $descripcion = $c4->descripcion;
              }    

              if($mas == true){
                $texto = $value->cie10. ':' . $descripcion. '-' . $value->presuntivo_definitivo;
                $mas = false;
                 
              }
              else{

                $texto = $texto.'<br>'.$value->cie10. ':' . $descripcion. '-' . $value->presuntivo_definitivo;
              }
            }
          
        }

        
        $input_orden = [
                  'id_paciente' => $id_paciente,
                  'id_doctor' => $id_doctor,
                  'id_evolucion' => $evol_id,
                  'motivo_consulta' => $evol_motivo,
                  'resumen_clinico' => $evol_cuadro_clinico,
                  //'observacion_medica' => $request["observacion_medica"],
                  //'observacion_recepcion' => $request["observacion_recepcion"],
                  'diagnosticos' => $texto,
                  'fecha_orden' => $fecha_orden,
                  'tipo_procedimiento' => '0',
                  'anio' => substr(date('Y-m-d'),0,4),
                  'mes' => substr(date('Y-m-d'),5,2),
                  'id_usuariocrea' => $id_doctor,
                  'id_usuariomod' => $id_doctor,
                  'ip_creacion' => $ip_cliente,
                  'ip_modificacion' => $ip_cliente,
        ];

        $id_orden = Orden::insertGetId($input_orden);

        $n_orden = Orden::find($id_orden);
        
        /////AQUI VA LA NUEVA FUNCION PARA CREAR 012
        if(!is_null($n_orden)){
          $this->crear_orden_012($n_orden);
        }
        //return "hola";
        return view('hc4/ordenes/orden_procedimiento_endoscopico/nuevo_editar', ['ordend' => $n_orden]);
      
    

    }

    //FUNCION GUARDAR ORDEN DE PROCEDIMIENTO ENDOSCOPICO
    //SOLO PARA DOCTORES
    public function guardar_orden_endoscopico(Request $request){
        
        
        //return $request->all();

        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        
        $procedimientos_endo_digest =  $request['procedimiento'];
        $count_pro_endo_digest = count($procedimientos_endo_digest);
        
        $procedimientos_broncoscopia =  $request['procedimiento_bron'];
        $count_pro_broncoscopia = count($procedimientos_broncoscopia);


        $procedimientos_colono =  $request['procedimiento_colono'];
        $count_pro_colono = count($procedimientos_colono);
        $procedimientos_enter =  $request['procedimiento_enter'];
        $count_pro_enter = count($procedimientos_enter);

        $procedimientos_ecoend =  $request['procedimiento_ecoend'];
        $count_pro_ecoend = count($procedimientos_ecoend);
        $procedimientos_cpre =  $request['procedimiento_cpre'];
        $count_pro_cpre = count($procedimientos_cpre);
        
    
        $paciente = Paciente::find($request["id_paciente"]);

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id; 

        //Obtengo fecha creacion
        $fecha_creacion = Date('Y-m-d H:i:s');


        
        if(($count_pro_endo_digest>0)||($count_pro_broncoscopia>0)||($count_pro_colono>0)||($count_pro_enter>0)||($count_pro_ecoend>0)||($count_pro_cpre>0)){

            //$this->validateInput($request);

            $orden_endoscopica_crear_new = [
              
              'anterior' => 'ORDEN_PROC_ENDOSCOPICOS -> El Dr. creo nueva orden de procedimiento endoscopico',
              'nuevo' => 'ORDEN_PROC_ENDOSCOPICOS -> El Dr. creo nueva orden de procedimiento endoscopico',
              'id_paciente' => $request["id_paciente"],
              'id_usuariocrea' => $id_doctor,
              'id_usuariomod' => $id_doctor,
              'ip_creacion' => $ip_cliente,
              'ip_modificacion' => $ip_cliente,

            ];

            Hc_Log::create($orden_endoscopica_crear_new);

            $input_orden = [
                'id_paciente' => $request["id_paciente"],
                'id_doctor' => $id_doctor,
                'motivo_consulta' => $request["motivo_orden"],
                'resumen_clinico' => $request["endos_historia_clinica"],
                'observacion_medica' => $request["observacion_orden"],
                'diagnosticos' => $request["endos_desc_diagnostico"],
                'fecha_orden' => $request["fecha"],
                'tipo_procedimiento' => '0',
                'anio' => substr(date('Y-m-d'),0,4),
                'mes' => substr(date('Y-m-d'),5,2),
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_orden = Orden::insertGetId($input_orden);
            //dd($id_orden);

            //$txt_pro = '';

            if($count_pro_endo_digest>0){


                $input_orden_tipo = [
                    'id_orden' => $id_orden,
                    'id_grupo_procedimiento' => '1',
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_creacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_endo_digest as $value)
                {
                    $input_orden_procedimiento = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $id_doctor,
                      'id_usuariomod' => $id_doctor,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_creacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento); 
                
                }
            }

            if($count_pro_broncoscopia>0){


                $input_orden_tipo = [
                    'id_orden' => $id_orden,
                    'id_grupo_procedimiento' => '14',
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_creacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_broncoscopia as $value)
                {
                    $input_orden_procedimiento = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $id_doctor,
                      'id_usuariomod' => $id_doctor,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_creacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento); 
                
                }
            }


            
            if($count_pro_colono>0){
                $input_orden_tipo = [
                    'id_orden' => $id_orden,
                    'id_grupo_procedimiento' => '2',
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_creacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_colono as $value)
                {
                    $input_orden_procedimiento_colono = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $id_doctor,
                      'id_usuariomod' => $id_doctor,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_creacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_colono); 
                
                }

            }

            if($count_pro_enter>0){
              
               $input_orden_tipo = [
                    'id_orden' => $id_orden,
                    'id_grupo_procedimiento' => '3',
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_creacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_enter as $value)
                {
                    $input_orden_procedimiento_enter = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $id_doctor,
                      'id_usuariomod' => $id_doctor,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_creacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_enter); 
                
                }

            }

             
            //ECOENDOSCOPIA
             if($count_pro_ecoend>0){
              
               $input_orden_tipo = [
                    'id_orden' => $id_orden,
                    'id_grupo_procedimiento' => '9',
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_creacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_ecoend as $value)
                {
                    $input_orden_procedimiento_ecoend = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $id_doctor,
                      'id_usuariomod' => $id_doctor,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_creacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_ecoend); 
                
                }

            }

            //CPRE
            if($count_pro_cpre>0){
              
               $input_orden_tipo = [
                    'id_orden' => $id_orden,
                    'id_grupo_procedimiento' => '10',
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_creacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_cpre as $value)
                {
                    $input_orden_procedimiento_cpre = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $id_doctor,
                      'id_usuariomod' => $id_doctor,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_creacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_cpre); 
                
                }

            }

        }else{
            
           return "false";
        
        }


        if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
        }

        
        $listado = DB::table('orden as o')
                       ->where('o.estado',1)
                       ->where('o.tipo_procedimiento',0)
                       ->where('o.id_paciente',$paciente->id)
                       ->OrderBy('o.created_at','desc')
                       ->get();


        /*$listado = Orden::where('id_paciente',$request["id_paciente"])
                        ->OrderBy('created_at','desc')
                        ->get();*/


        return view('hc4/ordenes/orden_procedimiento_endoscopico/index', ['paciente' => $paciente,'listado' => $listado,'edad' => $edad]);


    }

    //Mensaje de Validacion
    private function validateInput($request) {
        
        $rules = [
            'motivo_orden' => 'required',
            'observacion_orden' => 'required',
            
            ];
            
        $mensajes = [
            'motivo_orden.required' => 'Ingrese el Motivo.',
            'observacion_orden.required' => 'Ingrese la Observación Médica.',
            
        ];
        
        $this->validate($request, $rules, $mensajes);
    }

    //FUNCION PARA EDITAR LA ORDEN DE PROCEDIMIENTO ENDOSCOPICO
    //SOLO PARA DOCTORES
    public function editar_orden($id_orden, $id_paciente){

      
      $paciente = Paciente::find($id_paciente);

      if($paciente->fecha_nacimiento!=null){
        $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
      }

      $data_orden = Orden::find($id_orden);
      
      //$ndoctor = DB::table('users as us')->where('us.id',$data_orden->id_doctor)->first();
      $ndoctor = User::find($data_orden->id_doctor);

      
      //$tipo_eda_diagnostica = 1;
      //$tipo_colonoscopia_diagnostica = 2;
      //$tipo_enteroscopia = 3;
      //$tipo_ecoendoscopia = 9;
      //$tipo_cpre = 10;
      
      $px = Procedimiento::where('estado','1')->whereNull('id_grupo_procedimiento');


      $px1 = Procedimiento::where('estado','1')->where('id_grupo_procedimiento','<>','18')->where('id_grupo_procedimiento','<>','20')->where('id_grupo_procedimiento','<>','11');


      $px = $px->union($px1)->get();

      //dd($px);


      /*return view('hc4/ordenes/orden_procedimiento_endoscopico/editar_orden',['data_orden'=> $data_orden,'ndoctor'=> $ndoctor,'edad'=> $edad,'paciente' => $paciente,'px' => $px,'tipo_eda_diagnostica' => $tipo_eda_diagnostica,'tipo_colonoscopia_diagnostica' => $tipo_colonoscopia_diagnostica,'tipo_enteroscopia' => $tipo_enteroscopia,'tipo_ecoendoscopia' => $tipo_ecoendoscopia,'tipo_cpre' => $tipo_cpre]);*/
      //return "hola";
      return view('hc4/ordenes/orden_procedimiento_endoscopico/editar_orden',['data_orden'=> $data_orden,'ndoctor'=> $ndoctor,'edad'=> $edad,'paciente' => $paciente,'px' => $px]);
    
    }

    //FUNCION PARA ACTUALIZAR LA ORDEN DE PROCEDIMIENTO ENDOSCOPICO
    //SOLO PARA DOCTORES
    public function actualiza_orden_endoscopico(Request $request){

        //return $request->all();

   

        $opcion = '2';
        if($this->rol_new($opcion)){
          return redirect('/');
        }

        $check_selec =  $request['firma_doctor_rob_end'];

        $procedimientos_endo_digest =  $request['x_procedimiento'];
        $count_pro_endo_digest = count($procedimientos_endo_digest);
        $procedimientos_colono =  $request['x_procedimiento_colono'];
        $count_pro_colono = count($procedimientos_colono);
        $procedimientos_enter =  $request['x_procedimiento_entero'];
        $count_pro_enter = count($procedimientos_enter);
        $procedimientos_ecoend =  $request['x_procedimiento_ecoend'];
        $count_pro_ecoend = count($procedimientos_ecoend);
        $procedimientos_cpre =  $request['x_procedimiento_cpre'];
        $count_pro_cpre = count($procedimientos_cpre);
        $procedimientos_bronc =  $request['x_procedimiento_bronc'];
        $count_pro_bronc = count($procedimientos_bronc);


        //Obtengo la fecha de actualizacion
        $fecha_actualizacion = Date('Y-m-d H:i:s');
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $id= $request['id_ordenendos'];
        //dd($id);
        
        $orden_proendoscopico = Orden::find($id);
        //dd($orden_proendoscopico);

        //$this->validateInput($request);

        $ndoctor = DB::table('users as us')->where('us.id',$orden_proendoscopico->id_doctor)->first();
        
        $paciente = Paciente::find($request['id_pacient']);

        if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
        }

        
        
      //$anteriores_orden_tipo = DB::table('orden_tipo as ot')->where('ot.id_orden',$request['id_ordenendos'])->get();

      $anteriores_orden_tipo = DB::table('orden_tipo as ot')->where('ot.id_orden',$request['id_ordenendos'])->get();
     
     
      if($anteriores_orden_tipo!=null){

        foreach($anteriores_orden_tipo as $value){

          $anteriores_orden_procedimiento  = Orden_Procedimiento::where('id_orden_tipo', $value->id);
          $anteriores_orden_procedimiento->delete();
     
        }

      }

      $x_anteriores_orden_tipo  = Orden_Tipo::where('id_orden', $request['id_ordenendos']);
      $x_anteriores_orden_tipo->delete();


     
        if($request->necesita_valoracion==null){

          return 1;//editar_orden retorna 1 si no tiene cardio
          
        }
        
      
      if(($count_pro_endo_digest>0)||($count_pro_colono>0)||($count_pro_enter>0)||($count_pro_ecoend>0)||($count_pro_cpre>0)||($count_pro_bronc>0)){

            //$this->validateInput2($request);

            $orden_proendoscopico_new = $orden_proendoscopico;
            if(!is_null($orden_proendoscopico_new)){

              $orden_endoscopica_act_new = [
                  'anterior' => 'ORDEN_PROC_ENDOSCOPICOS -> Motivo: ' .$orden_proendoscopico_new->motivo_consulta.' Resumen_Historia_Clinica: ' .$orden_proendoscopico_new->resumen_clinico.' Diagnosticos: ' .$orden_proendoscopico_new->diagnosticos.' Observacion_Medica: ' .$orden_proendoscopico_new->observacion_medica.' Observacion_Recepcion:' .$orden_proendoscopico_new->observacion_recepcion,
                  'nuevo' => 'ORDEN_PROC_ENDOSCOPICOS -> Motivo: ' .$request['xmotivo_orden'].' Resumen_Historia_Clinica: ' .$request['endos_historia_clinica'].' Diagnosticos: ' .$request['endos_desc_diagnostico'].' Observacion_Medica: ' .$request['xobservacion_orden'].' Observacion_Recepcion:' .$request['xobservacion_recepcion'],
                  'id_paciente' => $request["id_pacient"],
                  'id_usuariocrea' => $idusuario,
                  'id_usuariomod' => $idusuario,
                  'ip_creacion' => $ip_cliente,
                  'ip_modificacion' => $ip_cliente,           
              ];
                 Hc_Log::create($orden_endoscopica_act_new);
            }

            //ENDOSCOPIA DIGESTIVA
            if($count_pro_endo_digest>0){


                $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '1',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_endo_digest as $value)
                {
                    $input_orden_procedimiento = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $idusuario,
                      'id_usuariomod' => $idusuario,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento); 
                
                }
            }

            
            //COLONOSCOPIA
            if($count_pro_colono>0){
                $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '2',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_colono as $value)
                {
                    $input_orden_procedimiento_colono = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $idusuario,
                      'id_usuariomod' => $idusuario,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_colono); 
                
                }

            }

            //INTESTINO DELGADO
            if($count_pro_enter>0){
              
               $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '3',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_enter as $value)
                {
                    $input_orden_procedimiento_enter = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $idusuario,
                      'id_usuariomod' => $idusuario,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_enter); 
                
                }

            }


            //ECOENDOSCOPIA
             if($count_pro_ecoend>0){
              
               $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '9',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_ecoend as $value)
                {
                    $input_orden_procedimiento_ecoend = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $idusuario,
                      'id_usuariomod' => $idusuario,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_ecoend); 
                
                }

            }

            //CPRE
            if($count_pro_cpre>0){
              
               $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '10',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_cpre as $value)
                {
                    $input_orden_procedimiento_cpre = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $idusuario,
                      'id_usuariomod' => $idusuario,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_cpre); 
                
                }

            }

            //BRONCOSCOPIA
            if($count_pro_bronc>0){
              
               $input_orden_tipo = [
                    'id_orden' => $id,
                    'id_grupo_procedimiento' => '14',
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_actualizacion
                ];

                $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

                foreach($procedimientos_bronc as $value)
                {
                    $input_orden_procedimiento_bronc = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $idusuario,
                      'id_usuariomod' => $idusuario,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_actualizacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_bronc); 
                
                }

            }

        
        //Check Firma Doctor Robles
        if($check_selec == '1'){

          $doctor_firma = '1307189140'; 
           
        }else
        {
          $doctor_firma = $orden_proendoscopico->id_doctor;

        }

        $input = [
            
            'motivo_consulta' => $request['xmotivo_orden'],
            'resumen_clinico' => $request['endos_historia_clinica'],
            'observacion_medica' => $request['xobservacion_orden'],
            'observacion_recepcion' => $request['xobservacion_recepcion'],
            'diagnosticos' => $request['endos_desc_diagnostico'],
            'check_doctor' => $check_selec,
            'id_doctor_firma' => $doctor_firma,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
            'updated_at' => $fecha_actualizacion, 
            'necesita_valoracion' => $request->necesita_valoracion, 
            'otros' => $request['xotros'], 
        
        ];

        $orden_proendoscopico->update($input);
      
      

      }/*else{
            
        return "false";   
      }*/

      //return "ok";   
      
      /*return view('hc4/ordenes/orden_procedimiento_endoscopico/unico_orden',['orden_proendoscopico'=> $orden_proendoscopico,'paciente' => $paciente,'edad' => $edad,'ndoctor' => $ndoctor]);*/
    
    }


    //Mensaje de Validacion
    private function validateInput2($request) {
        
        $rules = [
            'xmotivo_orden' => 'required',
            'xobservacion_orden' => 'required',
            
            ];
            
        $mensajes = [
            'xmotivo_orden.required' => 'Ingrese el Motivo.',
            'xobservacion_orden.required' => 'Ingrese la Observación Médica.',
            
        ];
        
        $this->validate($request, $rules, $mensajes);
    }


    //FUNCION IMPRIMIR ORDEN DE PROCEDIMIENTO ENDOSCOPICO
    //SOLO PARA DOCTORES
    public function imprimir_orden_endoscopica($id){
      
        
        $orden_proc_endoscopico = Orden::find($id);

        if((is_null($orden_proc_endoscopico->check_doctor))&&(is_null($orden_proc_endoscopico->id_doctor_firma))){

          $doctor_firma = $orden_proc_endoscopico->id_doctor; 
        
        }else{
       
          $doctor_firma = $orden_proc_endoscopico->id_doctor_firma;
        
        }
       
        /*if (!is_null($orden_proc_endoscopico)) {
                $firma = Firma_Usuario::where('id_usuario', $orden_proc_endoscopico->id_doctor)->first();
        }*/
        
        if (!is_null($orden_proc_endoscopico)) {
                $firma = Firma_Usuario::where('id_usuario',$doctor_firma)->first();
        }

        $paciente = Paciente::find($orden_proc_endoscopico->id_paciente);

        if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
        }

        //$id_doctor = Auth::user()->id; 
        $doctor_solicitante = DB::table('users as us')
                              ->where('us.id',$orden_proc_endoscopico->id_doctor)
                              ->first();
        
        $vistaurl="hc4.ordenes.orden_procedimiento_endoscopico.pdf_orden_endoscopica";
        $view =  \View::make($vistaurl, compact('orden_proc_endoscopico','paciente','edad','doctor_solicitante','firma'))->render();

        /*$view =  \View::make($vistaurl, compact('orden','pct','detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();*/

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        //return $pdf->stream('resultado-'+"ORDEN"+'.pdf');
        return $pdf->stream('resultado-'.$id.'.pdf');

        //return $pdf->stream('epicrisis-'.$historiaclinica->id_paciente.'-'.$historiaclinica->hcid.'.pdf');
    }

    //PARA IMPRIMIR EL PDF EN CIR
    public function imprimir_orden_endoscopica_cir($id){

        $orden_proc_endoscopico = Orden::find($id);

        if((is_null($orden_proc_endoscopico->check_doctor))&&(is_null($orden_proc_endoscopico->id_doctor_firma))){

          $doctor_firma = $orden_proc_endoscopico->id_doctor; 
        
        }else{
       
          $doctor_firma = $orden_proc_endoscopico->id_doctor_firma;
        
        }
       
        
        if (!is_null($orden_proc_endoscopico)) {
                $firma = Firma_Usuario::where('id_usuario',$doctor_firma)->first();
        }

        $paciente = Paciente::find($orden_proc_endoscopico->id_paciente);

        if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
        }

        //$id_doctor = Auth::user()->id; 
        $doctor_solicitante = DB::table('users as us')
                              ->where('us.id',$orden_proc_endoscopico->id_doctor)
                              ->first();
        
        $vistaurl="hc4.ordenes.orden_procedimiento_endoscopico.pdf_orden_endoscopica_cir";
        $view =  \View::make($vistaurl, compact('orden_proc_endoscopico','paciente','edad','doctor_solicitante','firma'))->render();

        /*$view =  \View::make($vistaurl, compact('orden','pct','detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();*/

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
        $pdf->setPaper('A4', 'portrait');
        //return $pdf->stream('resultado-'+"ORDEN"+'.pdf');
        return $pdf->stream('resultado_cir-'.$id.'.pdf');
    }


    public function buscador_contador_ordenes_proced(){

      $anio_mes = DB::table('orden as o')
                            ->where('o.estado','1')
                            ->groupBy('o.anio','o.mes')
                            ->select('o.anio','o.mes')
                            ->orderby('o.mes','desc')
                            ->orderby('o.anio','desc')
                            ->get();

      $ordenes_procedimiento = DB::table('orden as o')
                               ->join('orden_tipo as od','od.id_orden','o.id')
                               ->join('users as d','d.id','o.id_doctor')
                               ->where('o.estado','1')
                               //->where('o.anio',$anio)
                               //->where('o.mes',$mes)
                               ->groupBy('o.id_doctor','od.id_grupo_procedimiento')
                               ->select('o.id_doctor','od.id_grupo_procedimiento',DB::raw('count(*) as cant'))
                               ->get();

      $users = User::where('id_tipo_usuario','3')->get();

      $array = [];

      $i=0;
  
      foreach($users as $user){

        $x =  $ordenes_procedimiento->where('id_doctor', $user->id)->count();
     
        if($x>0){
      
          $procedimientos = [1,2,3,9,10,14,18,20];
          $arrayp = [];
          
          foreach ($procedimientos as $procedimiento){
       
            $px=$ordenes_procedimiento->where('id_doctor', $user->id)->where('id_grupo_procedimiento',$procedimiento)->first();
            $cantidad=0;
           
            if(!is_null($px)){
              $cantidad=  $px->cant;
            }

            $arrayp[$procedimiento]=$cantidad;
            
          }
       
          $array[$i]=['doctor' => $user->id, 'proc' =>$arrayp];
          $i++;
       
        }
      
      }


      return view('hc4/ordenes_procedimientos',['anio_mes' => $anio_mes,'array' => $array]); 

    }

    public function buscar_anio_mes_ord(Request $request){
      
      $opcion = '2';
      if($this->rol_new($opcion)){
        return redirect('/');
      }

      $anio_mes =  $request['id_anio_mes'];
      //dd($anio_mes);
      $anio = substr($anio_mes, 0, 4); // Devuelve el anio
      //dd($anio);
      $mes=substr($anio_mes, 5, 1); ; // Devuelve el mes 
      //dd($mes);

     
      //$anio='2019';
      //$mes='08';  

      $ordenes_procedimiento = DB::table('orden as o')
                             ->join('orden_tipo as od','od.id_orden','o.id')
                             ->join('users as d','d.id','o.id_doctor')
                             ->where('o.estado','1')
                             ->where('o.anio',$anio)
                             ->where('o.mes',$mes)
                             ->groupBy('o.anio','o.mes','o.id_doctor','od.id_grupo_procedimiento')
                             ->select('o.anio','o.mes','o.id_doctor','od.id_grupo_procedimiento',DB::raw('count(*) as cant'))
                             ->get();

                       
      $users = User::where('id_tipo_usuario','3')->get();

      $array = [];

      $i=0;
  
      foreach($users as $user){

        $x =  $ordenes_procedimiento->where('id_doctor', $user->id)->count();
     
        if($x>0){
      
          $procedimientos = [1,2,3,9,10,14,18,20];
          $arrayp = [];
          
          foreach ($procedimientos as $procedimiento){
       
            $px=$ordenes_procedimiento->where('id_doctor', $user->id)->where('id_grupo_procedimiento',$procedimiento)->first();
            $cantidad=0;
           
            if(!is_null($px)){
              $cantidad=  $px->cant;
            }

            $arrayp[$procedimiento]=$cantidad;
            
          }
       
          $array[$i]=['doctor' => $user->id, 'proc' =>$arrayp];
          $i++;
       
        }
      
      }

      return view('hc4/contador_ordenes_procedimientos',['array' => $array]); 
  
    }
   
    public function eliminar_orden_procedimiento($id)
    {
      DB::beginTransaction();
      try {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $orden = orden::find($id);
        $orden->estado = 0;
        $orden->id_usuariomod = $idusuario;
        $orden->save();

        $orden_endoscopica_crear_new = [
          'anterior' => "ORDEN_PROC_ENDOSCOPICOS -> El Dr. eliminó la orden del procedimiento endoscópico #$id",
          'nuevo' => "ORDEN_PROC_ENDOSCOPICOS -> El Dr. eliminó la orden del procedimiento endoscópico #$id",
          'id_paciente' => $orden->id_paciente,
          'id_usuariocrea' => $idusuario,
          'id_usuariomod' => $idusuario,
          'ip_creacion' => $ip_cliente,
          'ip_modificacion' => $ip_cliente,
        ];

        Hc_Log::create($orden_endoscopica_crear_new);
        DB::commit();
        return ['status' => 'success', 'msj' => 'Guardado con exito'];
      } catch (\Exception $e) {
        DB::rollback();
        return ['status' => 'error', 'msj' => $e->getMessage()];
      }
    }
    public function crear_orden_012($n_orden){
      
      $ip_cliente= $_SERVER["REMOTE_ADDR"];
      
        $id_empresa = "";
        if(!is_null($n_orden->id_evolucion)){
          $evolucion = Hc_Evolucion::find($n_orden->id_evolucion);
          $hcid            = $evolucion->hcid;
          $historiaclinica = Historiaclinica::find($hcid);
          $agenda = $historiaclinica->agenda;
          $id_empresa = $agenda->id_empresa;
        }

          $arr = [
            'id_paciente'        => $n_orden->id_paciente,
            'id_doctor_solicita' => $n_orden->id_doctor,
            'id_doctor_firma'    => $n_orden->id_doctor,
            'fecha_orden'        => $n_orden->fecha_orden,
            'id_hc_evolucion'    => $n_orden->id_evolucion,
            'id_orden'           => $n_orden->id,
            'estado'             => '1',
            'rutina'             => '1',
            'puede_mover'        => '1',
            //'descripcion' => $ordenes->descripcion,  Esta mal la Definicion
            'motivo'             => $n_orden->motivo_consulta, //Evolucion Motivo
            'id_empresa'         => $id_empresa,
            'cuadro_clinico'     => $n_orden->resumen_clinico, //Evolucion Cuadro Clinico
            'id_usuariocrea'     => $n_orden->id_doctor,
            'id_usuariomod'      => $n_orden->id_doctor,
            'ip_creacion'        => $ip_cliente,
            'ip_modificacion'    => $ip_cliente,

        ];

        //Insertamos en la Tabla Orden_012 y Obtenemos el id de la Tabla
        //$id_012 = Orden_012::insertGetId($arr);
        $orden_012 = Orden_012::create($arr);
      

    }
}


