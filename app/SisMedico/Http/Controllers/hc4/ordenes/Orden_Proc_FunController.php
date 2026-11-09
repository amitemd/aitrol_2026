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
use Sis_medico\Seguro;
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

class Orden_Proc_FunController extends Controller
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

    private function calcularEdadPaciente($paciente)
    {
        if (is_null($paciente) || empty($paciente->fecha_nacimiento)) {
            return 0;
        }

        try {
            $nacimiento = Carbon::createFromFormat(
                '!Y-m-d',
                substr((string) $paciente->fecha_nacimiento, 0, 10),
                'America/Guayaquil'
            );

            return (int) $nacimiento->diffInYears(Carbon::now('America/Guayaquil'));
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Muestra el historial de las ordenes de Procedimientos Funcionales
    //Solo para Doctores
    public function index($id_paciente){
        
        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        //Obtenemos los datos del Paciente, la fecha de nacimiento
        $paciente = Paciente::findOrFail($id_paciente);
        $edad = $this->calcularEdadPaciente($paciente);

        $listado_ordenes = Orden::where('tipo_procedimiento',1)
                                  ->where('id_paciente',$paciente->id)
                                  ->where('estado',1)
                                  ->OrderBy('id','desc')
                                  //->OrderBy('created_at','desc')
                                  ->get();

      

        return view('hc4/ordenes/orden_procedimiento_funcional/index', ['paciente' => $paciente,'listado_ordenes' => $listado_ordenes,'edad' => $edad]);
    }


    //AGREGAR PROCEDIMIENTO FUNCIONAL
    //Solo para Doctores
   /* public function crear_orden_funcional($tipo, $id_paciente){

        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }
        
        $tipo_funcional = 19;

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
        $edad = $this->calcularEdadPaciente($paciente);

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

    
      return view('hc4/ordenes/orden_procedimiento_funcional/orden_procedfuncional',['px' => $px, 'paciente' => $paciente,'fecha_orden' => $fecha_orden,'data' => $data,'edad' => $edad,'evoluciones' =>  $evoluciones,'doctor_solicitante' =>  $doctor_solicitante,'tipo_funcional' =>  $tipo_funcional]);

    }*/

    //AGREGAR PROCEDIMIENTO FUNCIONAL
    //Solo para Doctores
    public function crear_orden_funcional(Request $request, $tipo, $id_paciente){
        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        $paciente = Paciente::findOrFail($id_paciente);
        $procedimientosSolicitados = array_values(array_filter((array) $request->input('procedimiento', [])));

        // Abrir el selector no debe crear una orden vacía. La escritura se
        // realiza únicamente cuando el médico confirma uno o más estudios.
        if (count($procedimientosSolicitados) === 0) {
            return view('hc4.ordenes.orden_procedimiento_funcional.seleccionar_procedimiento', [
                'paciente' => $paciente,
                'tipo' => $tipo,
            ]);
        }

        $procedimientos = Procedimiento::where('estado', '1')
            ->whereIn('id', $procedimientosSolicitados)
            ->where(function ($query) {
                $query->where('id_grupo_procedimiento', '18')
                    ->orWhereNull('id_grupo_procedimiento');
            })
            ->pluck('id')
            ->all();

        if (count($procedimientos) === 0) {
            return response()->json([
                'message' => 'Seleccione al menos un procedimiento funcional válido.',
            ], 422);
        }

        $ip_cliente = $request->ip();

        //Obtenemos la cedula del Doctor
        $id_doctor = Auth::user()->id;

        //Obtengo fecha de la Orden
        $fecha_orden = Date('Y-m-d H:i:s');

        $evoluciones =  DB::table('historiaclinica as h')
                        ->where('h.id_paciente', $id_paciente)
                        ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
                        ->where('hc_evo.secuencia',0)
                        ->whereNotNull('hc_evo.cuadro_clinico')
                        ->orderby('h.hcid','desc')
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
              foreach($x_diagnosticos as $value) {
                $descripcion = '';
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

           
        $id_orden = DB::transaction(function () use (
            $id_paciente,
            $id_doctor,
            $ip_cliente,
            $fecha_orden,
            $evol_id,
            $evol_motivo,
            $evol_cuadro_clinico,
            $texto,
            $procedimientos
        ) {
            Hc_Log::create([
                'anterior' => 'ORDEN_PROC_FUNCIONAL -> El Dr. creo nueva orden de procedimiento funcional',
                'nuevo' => 'ORDEN_PROC_FUNCIONAL -> El Dr. creo nueva orden de procedimiento funcional',
                'id_paciente' => $id_paciente,
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            $idOrden = Orden::insertGetId([
                'id_paciente' => $id_paciente,
                'id_doctor' => $id_doctor,
                'id_evolucion' => $evol_id,
                'motivo_consulta' => $evol_motivo,
                'resumen_clinico' => $evol_cuadro_clinico,
                'diagnosticos' => $texto,
                'fecha_orden' => $fecha_orden,
                'tipo_procedimiento' => '1',
                'anio' => substr($fecha_orden, 0, 4),
                'mes' => substr($fecha_orden, 5, 2),
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);

            $idOrdenTipo = Orden_Tipo::insertGetId([
                'id_orden' => $idOrden,
                'id_grupo_procedimiento' => '18',
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'created_at' => $fecha_orden,
                'updated_at' => $fecha_orden,
            ]);

            foreach ($procedimientos as $idProcedimiento) {
                Orden_Procedimiento::create([
                    'id_orden_tipo' => $idOrdenTipo,
                    'id_procedimiento' => $idProcedimiento,
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_orden,
                    'updated_at' => $fecha_orden,
                ]);
            }

            return $idOrden;
        });

        return response()->json([
            'ok' => true,
            'id_orden' => $id_orden,
            'historial_url' => route('paciente.orden_proc_funcional', ['id_paciente' => $paciente->id]),
        ]);

    }


    //FUNCION GUARDAR ORDEN DE PROCEDIMIENTO FUNCIONAL
    //SOLO PARA DOCTORES
    public function guardar_orden_funcional(Request $request){

        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        $procedimientos_funcional =  $request['procedimiento'];
        $count_pro_funcional = count($procedimientos_funcional);

        $paciente = Paciente::find($request["id_paciente"]);

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id; 

       //Obtengo fecha creacion
        $fecha_creacion = Date('Y-m-d H:i:s');

        if($count_pro_funcional>0){

            //$this->validateInput($request);

            $orden_endoscopica_crear_new = [
            
                'anterior' => 'ORDEN_PROC_FUNCIONAL -> El Dr. creo nueva orden de procedimiento funcional',
                'nuevo' => 'ORDEN_PROC_FUNCIONAL -> El Dr. creo nueva orden de procedimiento funcional',
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
                'resumen_clinico' => $request["historia_clinica"],
                'observacion_medica' => $request["observacion_orden"],
                'diagnosticos' => $request["x_diagnostico"],
                'fecha_orden' => $request["fecha"],
                'tipo_procedimiento' => '1',
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
                    'id_grupo_procedimiento' => '18',
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_creacion
            ];

            $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);

            foreach($procedimientos_funcional as $value)
            {
                    $input_orden_procedimiento_funcional = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $value,
                      'id_usuariocrea' => $id_doctor,
                      'id_usuariomod' => $id_doctor,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_creacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_funcional); 
                
            }
        
        }else{
            
            return "false";   
        }

        $edad = $this->calcularEdadPaciente($paciente);


        $listado_ordenes = DB::table('orden as o')
                                   ->where('o.estado',1)
                                   ->where('o.tipo_procedimiento',1)
                                   ->where('o.id_paciente',$paciente->id)
                                   ->OrderBy('o.created_at','desc')
                                   ->get();


        return view('hc4/ordenes/orden_procedimiento_funcional/index', ['paciente' => $paciente,'listado_ordenes' => $listado_ordenes,'edad' => $edad]);



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


    //FUNCION PARA EDITAR LA ORDEN DE PROCEDIMIENTO FUNCIONAL
    //SOLO PARA DOCTORES
    public function editar_orden_funcional($id_orden, $id_paciente){

      $paciente = Paciente::find($id_paciente);

      $edad = $this->calcularEdadPaciente($paciente);

      $data_orden_fun = Orden::find($id_orden);

      $ndoctor = DB::table('users as us')->where('us.id',$data_orden_fun->id_doctor)->first();

      //$tipo_funcional = 19;

      $procedimientos_seleccionados = DB::table('orden_tipo as ot')
          ->join('orden_procedimiento as op', 'op.id_orden_tipo', '=', 'ot.id')
          ->join('procedimiento as p', 'p.id', '=', 'op.id_procedimiento')
          ->where('ot.id_orden', $id_orden)
          ->where('ot.id_grupo_procedimiento', 18)
          ->select('p.id', 'p.nombre')
          ->orderBy('p.nombre')
          ->get();
      $seguro = $paciente ? Seguro::find($paciente->id_seguro) : null;

      return view('hc4/ordenes/orden_procedimiento_funcional/editar_orden', compact(
          'data_orden_fun',
          'ndoctor',
          'edad',
          'paciente',
          'seguro',
          'procedimientos_seleccionados'
      ));
    
    }


    //FUNCION PARA ACTUALIZAR LA ORDEN DE PROCEDIMIENTO FUNCIONAL
    //SOLO PARA DOCTORES
    public function actualiza_orden_funcional(Request $request){

        $opcion = '2';
        if($this->rol_new($opcion)){
          return redirect('/');
        }

        // La firma CRM ya no forma parte del formulario de procedimientos funcionales.
        $check_selec = $request->input('firma_doctor_rob_fun', '0');

        $proced_funcional = (array) $request->input('x_procedimiento_func', []);
        $count_proced_funcional = count($proced_funcional);
       

        //Obtengo la fecha de actualizacion
        $fecha_actualizacion = Date('Y-m-d H:i:s');
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $id= $request['id_ordenfun'];
        
        $orden_procedfuncional = Orden::find($id);
       

        $anteriores_orden_tipo = DB::table('orden_tipo as ot')->where('ot.id_orden',$request['id_ordenfun'])->get();
     
     
        if($anteriores_orden_tipo!=null){

            foreach($anteriores_orden_tipo as $value){

              $anteriores_orden_procedimiento  = Orden_Procedimiento::where('id_orden_tipo', $value->id);
              $anteriores_orden_procedimiento->delete();
         
            }

        }

        $x_anteriores_orden_tipo  = Orden_Tipo::where('id_orden', $request['id_ordenfun']);
        $x_anteriores_orden_tipo->delete();
        
      
        if($count_proced_funcional>0){

            //$this->validateInput2($request);

            $orden_profuncional_new = $orden_procedfuncional;
            if(!is_null($orden_profuncional_new)){

                $orden_funcional_act_new = [
                  'anterior' => 'ORDEN_PROC_FUNCIONAL -> Motivo: ' .$orden_profuncional_new->motivo_consulta.' Resumen_Historia_Clinica: ' .$orden_profuncional_new->resumen_clinico.' Diagnosticos: ' .$orden_profuncional_new->diagnosticos.' Observacion_Medica: ' .$orden_profuncional_new->observacion_medica.' Observacion_Recepcion:' .$orden_profuncional_new->observacion_recepcion,
                  'nuevo' => 'ORDEN_PROC_FUNCIONAL -> Motivo: ' .$request['xmotivo_orden'].' Resumen_Historia_Clinica: ' .$request['func_historia_clinica'].' Diagnosticos: ' .$request['func_des_diagnostico'].' Observacion_Medica: ' .$request['xobservacion_orden'].' Observacion_Recepcion:' .$request['xobservacion_recepcion'],
                  'id_paciente' => $request["id_paciente"],
                  'id_usuariocrea' => $idusuario,
                  'id_usuariomod' => $idusuario,
                  'ip_creacion' => $ip_cliente,
                  'ip_modificacion' => $ip_cliente,           
                ];
                 
                Hc_Log::create($orden_funcional_act_new);
            }

            $input_orden_tipo = [
                'id_orden' => $id,
                'id_grupo_procedimiento' => '18',
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'created_at' => $fecha_actualizacion
            ];

            $id_orden_tipo = Orden_Tipo::insertGetId($input_orden_tipo);
            

            foreach($proced_funcional as $value)
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
               $doctor_firma = $orden_procedfuncional->id_doctor;
            }


            $input = [
            
                'motivo_consulta' => $request['xmotivo_orden'],
                'resumen_clinico' => $request['func_historia_clinica'],
                'observacion_medica' => $request['xobservacion_orden'],
                'observacion_recepcion' => $request['xobservacion_recepcion'],
                'diagnosticos' => $request['func_des_diagnostico'],
                'check_doctor' => $check_selec,
                'id_doctor_firma' => $doctor_firma,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'updated_at' => $fecha_actualizacion,  
        
            ];

            $orden_procedfuncional->update($input);
      
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


     //FUNCION IMPRIMIR ORDEN DE PROCEDIMIENTO ENDOSCOPICO
    //SOLO PARA DOCTORES
    public function imprimir_orden_funcional($id){
      /*$opcion = '2';
      if($this->rol_new($opcion)){
        return redirect('/');
      }*/

      $orden_proc_funcional = Orden::find($id);

      if((is_null($orden_proc_funcional->check_doctor))&&(is_null($orden_proc_funcional->id_doctor_firma))){

        
        $doctor_firma = $orden_proc_funcional->id_doctor; 

      }else{
       
        $doctor_firma = $orden_proc_funcional->id_doctor_firma;
      
      }

      /*if (!is_null($orden_proc_funcional)) {
                $firma = Firma_Usuario::where('id_usuario', $orden_proc_funcional->id_doctor)->first();
      }*/

      if (!is_null($orden_proc_funcional)) {
                $firma = Firma_Usuario::where('id_usuario',$doctor_firma)->first();
      }
       
      $paciente = Paciente::find($orden_proc_funcional->id_paciente);

      $edad = $this->calcularEdadPaciente($paciente);

      //$id_doctor = Auth::user()->id; 
      $doctor_solicitante = DB::table('users as us')
                            ->where('us.id',$orden_proc_funcional->id_doctor)
                            ->first();
      
      $vistaurl="hc4.ordenes.orden_procedimiento_funcional.pdf_orden_funcional";
      $view =  \View::make($vistaurl, compact('orden_proc_funcional','paciente','edad','doctor_solicitante','firma'))->render();

      /*$view =  \View::make($vistaurl, compact('orden','pct','detalle', 'resultados', 'agrupador', 'parametros', 'age', 'ucreador'))->render();*/

      $pdf = \App::make('dompdf.wrapper');
      $pdf->loadHTML($view);
      $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
      $pdf->setPaper('A4', 'portrait');
      return $pdf->stream('resultado-'.$id.'.pdf');
    }
   
}
