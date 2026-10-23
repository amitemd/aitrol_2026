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
 
class Orden_Proc_ImagenesController extends Controller
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

    //Muestra el historial de las ordenes de Imagenes
    //Solo para Doctores
    public function index($id_paciente){
        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        //Obtenemos los datos del Paciente
        $paciente = Paciente::find($id_paciente);

        //Calculamos la Edad del Paciente 
        if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
        }

        $listado_ordImagenes = Orden::where('tipo_procedimiento',2)
                                    ->where('id_paciente',$paciente->id)
                                    ->where('estado',1)
                                    ->OrderBy('id','desc')
                                    //->OrderBy('created_at','desc')
                                    ->get();


        return view('hc4/ordenes/orden_procedimiento_imagenes/index', ['paciente' => $paciente,'listado_ordImagenes' => $listado_ordImagenes,'edad' => $edad]);
    }

    //AGREGAR PROCEDIMIENTO IMAGENES
    //Solo para Doctores
    /*public function crear_orden_imagenes($tipo, $id_paciente){

        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        
        $tipo_imagenes = 16;

        //Obtenemos el nombre del Seguro y el Convenio
        $data = DB::table('agenda as a')
                    ->where('a.id_paciente',$id_paciente)
                    ->join('historiaclinica as h','h.id_agenda','a.id')
                    ->join('seguros as s','s.id','h.id_seguro')
                    ->join('empresa as em','em.id','a.id_empresa')
                    ->where('a.espid','<>','10')
                    ->select('h.*','s.nombre','em.nombre_corto')
                    ->first();

        //Obtenemos los datos del Paciente
        $paciente = Paciente::find($id_paciente);

        //Obtenemos la Edad del Paciente
        if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
        }

        //Obtenemos la cedula del Doctor
        $id_doctor = Auth::user()->id; 
        
        //Obtenemos el nombre del Doctor Solicitante
        $doctor_solicitante = DB::table('users as us')
                              ->where('us.id',$id_doctor)
                              ->first();

        //Obtengo fecha de la Orden
        $fecha_orden = Date('Y-m-d h:i:s');

        
        //Obtenemos todos los procedimientos de la Tabla Procedimiento que estan en estado activo
        $px = Procedimiento::where('procedimiento.estado','1')->get();              

        $evoluciones =  DB::table('historiaclinica as h')
                        ->where('h.id_paciente', $id_paciente)
                        ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
                        ->where('hc_evo.secuencia',0)
                        ->whereNotNull('hc_evo.cuadro_clinico')
                        ->orderby('h.hcid','desc')
                        //->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                        //->where('hc_proto.tipo_procedimiento', '0')
                        ->select('hc_evo.*')
                        ->first();

    
      return view('hc4/ordenes/orden_procedimiento_imagenes/orden_procedImagenes',['px' => $px, 'paciente' => $paciente,'fecha_orden' => $fecha_orden,'data' => $data,'edad' => $edad,'evoluciones' =>  $evoluciones,'doctor_solicitante' =>  $doctor_solicitante,'tipo_imagenes' =>  $tipo_imagenes]);

    }*/

        //AGREGAR PROCEDIMIENTO IMAGENES
    //Solo para Doctores
    public function crear_orden_imagenes($tipo, $id_paciente){

        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        
        //$tipo_imagenes = 16;
        $ip_cliente= $_SERVER["REMOTE_ADDR"];

        //Obtenemos la cedula del Doctor
        $id_doctor = Auth::user()->id;

        //Obtengo fecha de la Orden
        $fecha_orden = Date('Y-m-d H:i:s');

        $orden_imagenes_crear_new = [
            
                'anterior' => 'ORDEN_PROC_IMAGENES -> El Dr. creo nueva orden de procedimiento imagenes',
                'nuevo' => 'ORDEN_PROC_IMAGENES -> El Dr. creo nueva orden de procedimiento imagenes',
                'id_paciente' => $id_paciente,
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

        ];

        Hc_Log::create($orden_imagenes_crear_new);



        $evoluciones =  DB::table('historiaclinica as h')
                        ->where('h.id_paciente', $id_paciente)
                        ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
                        ->where('hc_evo.secuencia',0)
                        ->whereNotNull('hc_evo.cuadro_clinico')
                        ->orderby('hc_evo.updated_at','desc')
                        ->select('hc_evo.*')
                        ->first();



        //dd($evoluciones); 

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
                    'id_evolucion' =>  $evol_id,
                    'motivo_consulta' => $evol_motivo,
                    'resumen_clinico' => $evol_cuadro_clinico,
                    'diagnosticos' => $texto,
                    'fecha_orden' => $fecha_orden,
                    'tipo_procedimiento' => '2',
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
      
            return view('hc4/ordenes/orden_procedimiento_imagenes/nuevo_editar', ['ordimag' => $n_orden]);

    }


    //FUNCION GUARDAR ORDEN DE PROCEDIMIENTO IMAGENES
    //SOLO PARA DOCTORES
    public function guardar_orden_imagenes(Request $request){

        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        $procedimientos_imagenes =  $request['procedimiento'];
        $count_pro_imagenes = count($procedimientos_imagenes);

        $paciente = Paciente::find($request["id_paciente"]);

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id; 

       //Obtengo fecha creacion
        $fecha_creacion = Date('Y-m-d H:i:s');

        if($count_pro_imagenes>0){

            //$this->validateInput($request);

            $orden_imagenes_crear_new = [
            
                'anterior' => 'ORDEN_PROC_IMAGENES -> El Dr. creo nueva orden de procedimiento imagenes',
                'nuevo' => 'ORDEN_PROC_IMAGENES -> El Dr. creo nueva orden de procedimiento imagenes',
                'id_paciente' => $request["id_paciente"],
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,

            ];

            Hc_Log::create($orden_imagenes_crear_new);

            $input_orden = [
                'id_paciente' => $request["id_paciente"],
                'id_doctor' => $id_doctor,
                'motivo_consulta' => $request["motivo_orden"],
                'resumen_clinico' => $request["historia_clinica"],
                'observacion_medica' => $request["observacion_orden"],
                'diagnosticos' => $request["x_diagnostico"],
                'fecha_orden' => $request["fecha"],
                'tipo_procedimiento' => '2',
                'anio' => substr(date('Y-m-d'),0,4),
                'mes' => substr(date('Y-m-d'),5,2),
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ];

            $id_orden = Orden::insertGetId($input_orden);

            $input_orden_tipo = [
                    'id_orden' => $id_orden,
                    'id_grupo_procedimiento' => '20',
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_creacion
            ];

            $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);

            foreach($procedimientos_imagenes as $value)
            {
                    $input_orden_procedimiento_imagenes = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $id_doctor,
                      'id_usuariomod' => $id_doctor,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_creacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_imagenes); 
                
            }
        
        }else{
            
            return "false";   
        }

        if($paciente->fecha_nacimiento!=null){
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
        }


        $listado_ordImagenes = DB::table('orden as o')
                               ->where('o.estado',1)
                               ->where('o.tipo_procedimiento',2)
                               ->where('o.id_paciente',$paciente->id)
                               ->OrderBy('o.created_at','desc')
                               ->get();



        return view('hc4/ordenes/orden_procedimiento_imagenes/index', ['paciente' => $paciente,'listado_ordImagenes' => $listado_ordImagenes,'edad' => $edad]);

    }

    //Mensaje de Validacion
    private function validateInput($request) {
        
        $rules = [
            'motivo_orden' => 'required',
            //'resumen_orden' => 'required',
            'observacion_orden' => 'required',
            
            ];
            
        $mensajes = [
            'motivo_orden.required' => 'Ingrese el Motivo.',
            //'resumen_orden.required' => 'Ingrese el resumen.',
            'observacion_orden.required' => 'Ingrese la Observación Médica.',
        ];
        
        $this->validate($request, $rules, $mensajes);
    }


    //FUNCION PARA EDITAR LA ORDEN DE PROCEDIMIENTO IMAGENES
    //SOLO PARA DOCTORES
    public function editar_orden_imagenes($id_orden, $id_paciente){

      $paciente = Paciente::find($id_paciente);

      if($paciente->fecha_nacimiento!=null){
        $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
      }

      $data_orden_imag = Orden::find($id_orden);

      $ndoctor = DB::table('users as us')->where('us.id',$data_orden_imag->id_doctor)->first();

      //$tipo_imagenes = 16;

      $px = Procedimiento::where('procedimiento.estado','1')->get();


      return view('hc4/ordenes/orden_procedimiento_imagenes/editar_orden',['data_orden_imag'=> $data_orden_imag,'ndoctor'=> $ndoctor,'edad'=> $edad,'paciente' => $paciente,'px' => $px]);
    
    }


    //FUNCION PARA ACTUALIZAR LA ORDEN DE PROCEDIMIENTO FUNCIONAL
    //SOLO PARA DOCTORES
    public function actualiza_orden_imagenes(Request $request){

        $opcion = '2';
        if($this->rol_new($opcion)){
          return redirect('/');
        }
       
        //return $request->all();

        //Check Doctor Robles
        $check_selec =  $request['firma_doctor_rob'];
        //return $check_selec;



        $proced_imagenes =  $request['x_procedimiento_imag'];
        $count_proced_imagenes = count($proced_imagenes);

        //Obtengo la fecha de actualizacion
        $fecha_actualizacion = Date('Y-m-d H:i:s');
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $id= $request['id_ordenimag'];
        
        
        $orden_procedimagenes = Orden::find($id);
        
        $anteriores_orden_tipo = DB::table('orden_tipo as ot')->where('ot.id_orden',$request['id_ordenimag'])->get();
     
     
        if($anteriores_orden_tipo!=null){

            foreach($anteriores_orden_tipo as $value){

              $anteriores_orden_procedimiento  = Orden_Procedimiento::where('id_orden_tipo', $value->id);
              $anteriores_orden_procedimiento->delete();
         
            }

        }

        $x_anteriores_orden_tipo  = Orden_Tipo::where('id_orden', $request['id_ordenimag']);
        $x_anteriores_orden_tipo->delete();
        
      
        if($count_proced_imagenes>0){

            //$this->validateInput2($request);

            $orden_proimagenes_new = $orden_procedimagenes;
            if(!is_null($orden_proimagenes_new)){

                $orden_imagenes_act_new = [
                  'anterior' => 'ORDEN_PROC_IMAGENES -> Motivo: ' .$orden_proimagenes_new->motivo_consulta.' Resumen_Historia_Clinica: ' .$orden_proimagenes_new->resumen_clinico.' Diagnosticos: ' .$orden_proimagenes_new->diagnosticos.' Observacion_Medica: ' .$orden_proimagenes_new->observacion_medica.'Observacion_Recepcion:' .$orden_proimagenes_new->observacion_recepcion,
                  'nuevo' => 'ORDEN_PROC_IMAGENES -> Motivo: ' .$request['xmotivo_orden'].' Resumen_Historia_Clinica: ' .$request['imag_historia_clinica'].' Diagnosticos: ' .$request['imag_des_diagnostico'].' Observacion_Medica: ' .$request['xobservacion_orden'].' Observacion_Recepcion:' .$request['xobservacion_recepcion'],
                  'id_paciente' => $request["id_paciente"],
                  'id_usuariocrea' => $idusuario,
                  'id_usuariomod' => $idusuario,
                  'ip_creacion' => $ip_cliente,
                  'ip_modificacion' => $ip_cliente,           
                ];
                 
                Hc_Log::create($orden_imagenes_act_new);
            }

            $input_orden_tipo = [
                'id_orden' => $id,
                'id_grupo_procedimiento' => '20',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'created_at' => $fecha_actualizacion
            ];

            $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

            foreach($proced_imagenes  as $value)
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


           //Check Firma Doctor Robles
           if($check_selec == '1'){

               $doctor_firma = '1307189140'; 
           
           }else
           {
               $doctor_firma = $orden_procedimagenes->id_doctor;

           }

           //Fin Check Firma Doctor Robles
            $input = [
            
                'motivo_consulta' => $request['xmotivo_orden'],
                'resumen_clinico' => $request['imag_historia_clinica'],
                'observacion_medica' => $request['xobservacion_orden'],
                'observacion_recepcion' => $request['xobservacion_recepcion'],
                'diagnosticos' => $request['imag_des_diagnostico'],
                'check_doctor' => $check_selec,
                'id_doctor_firma' => $doctor_firma,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'updated_at' => $fecha_actualizacion, 
                'otros' => $request['xotros'], 
        
            ];

            $orden_procedimagenes->update($input);
      
        }
      
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


    //FUNCION IMPRIMIR ORDEN DE PROCEDIMIENTO IMAGENES
    //SOLO PARA DOCTORES
    public function imprimir_orden_imagenes($id){


      $orden_proc_imagenes = Orden::find($id);

      if((is_null($orden_proc_imagenes->check_doctor))&&(is_null($orden_proc_imagenes->id_doctor_firma))){

        
        $doctor_firma = $orden_proc_imagenes->id_doctor; 

      }else{
       
        $doctor_firma = $orden_proc_imagenes->id_doctor_firma;
      
      }

      /*if (!is_null($orden_proc_imagenes)) {
                $firma = Firma_Usuario::where('id_usuario', $orden_proc_imagenes->id_doctor)->first();
      }*/

      if (!is_null($orden_proc_imagenes)) {
                $firma = Firma_Usuario::where('id_usuario', $doctor_firma)->first();
      }
       
      $paciente = Paciente::find($orden_proc_imagenes->id_paciente);

      if($paciente->fecha_nacimiento!=null){
          $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;    
      }

      //$id_doctor = Auth::user()->id; 
      $doctor_solicitante = DB::table('users as us')
                            ->where('us.id',$orden_proc_imagenes->id_doctor)
                            ->first();
      
      $vistaurl="hc4.ordenes.orden_procedimiento_imagenes.pdf_orden_imagenes";
      $view =  \View::make($vistaurl, compact('orden_proc_imagenes','paciente','edad','doctor_solicitante','firma'))->render();

      $pdf = \App::make('dompdf.wrapper');
      $pdf->loadHTML($view);
      $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot'  => base_path('/')]);
      $pdf->setPaper('A4', 'portrait');
      return $pdf->stream('resultado-'.$id.'.pdf');
    }

    //FUNCION BUSCA EVOLUCION LLENA
    public function buscar_evolucion_imagenes($id_paciente){

        

        $evoluciones =  DB::table('historiaclinica as h')
                        ->where('h.id_paciente', $id_paciente)
                        ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
                        ->where('hc_evo.secuencia',0)
                        ->whereNotNull('hc_evo.cuadro_clinico')
                        ->orderby('h.hcid','desc')
                        ->select('hc_evo.*')
                        ->first();

        if(!is_null($evoluciones)){
          
          return $evoluciones;

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
