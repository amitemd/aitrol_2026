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
use Sis_medico\grupo_procedimiento;
use Carbon\Carbon;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Cie10;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Firma_Usuario;
use Sis_medico\Empresa;
use Sis_medico\Seguro;
use Response;
 
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
     * Calcula la edad sin depender de una zona horaria vacia en la
     * configuracion del servidor.
     */
    private function calcularEdadPaciente($paciente)
    {
        if (is_null($paciente) || empty($paciente->fecha_nacimiento)) {
            return 0;
        }

        $fechaNacimiento = substr((string) $paciente->fecha_nacimiento, 0, 10);

        try {
            $nacimiento = Carbon::createFromFormat('!Y-m-d', $fechaNacimiento, 'America/Guayaquil');

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

    //Muestra el historial de las ordenes de Imagenes
    //Solo para Doctores
    public function index($id_paciente){
        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        //Obtenemos los datos del Paciente
        $paciente = Paciente::findOrFail($id_paciente);

        //Calculamos la Edad del Paciente 
        $edad = $this->calcularEdadPaciente($paciente);

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
                        ->orderby('h.hcid','desc')
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
      
            return view('hc4/ordenes/orden_procedimiento_imagenes/nuevo_editar', ['ordimag' => $n_orden]);

    }


    //FUNCION GUARDAR ORDEN DE PROCEDIMIENTO IMAGENES
    //SOLO PARA DOCTORES
    public function guardar_orden_imagenes(Request $request){

        $opcion = '2';
        if($this->rol_new($opcion)){
            return redirect('/');
        }

        $procedimientos_imagenes = array_values(array_unique(array_filter(
            (array) $request->input('procedimiento', array()),
            function ($idProcedimiento) {
                return is_numeric($idProcedimiento);
            }
        )));
        $count_pro_imagenes = count($procedimientos_imagenes);

        $paciente = Paciente::findOrFail($request["id_paciente"]);

        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id; 

       //Obtengo fecha creacion
        $fecha_creacion = Date('Y-m-d H:i:s');

        if($count_pro_imagenes>0){

            //$this->validateInput($request);

            $procedimientosCatalogo = Procedimiento::whereIn('id', $procedimientos_imagenes)
                ->where('estado', 1)
                ->whereHas('grupo_procedimiento', function ($query) {
                    $query->where('tipo_procedimiento', 2);
                })
                ->get(array('id', 'id_grupo_procedimiento'));

            if ($procedimientosCatalogo->count() !== $count_pro_imagenes) {
                return response()->json(array('message' => 'Uno de los procedimientos de imagen ya no está disponible.'), 422);
            }

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

            foreach ($procedimientosCatalogo->groupBy('id_grupo_procedimiento') as $idGrupo => $procedimientosGrupo) {
                $id_orden_tipo = Orden_Tipo::insertGetId(array(
                    'id_orden' => $id_orden,
                    'id_grupo_procedimiento' => $idGrupo,
                    'id_usuariocrea' => $id_doctor,
                    'id_usuariomod' => $id_doctor,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'created_at' => $fecha_creacion,
                ));

                foreach ($procedimientosGrupo as $procedimientoImagen) {
                    $input_orden_procedimiento_imagenes = [
                      'id_orden_tipo' => $id_orden_tipo,
                      'id_procedimiento' => $procedimientoImagen->id,
                      'id_usuariocrea' => $id_doctor,
                      'id_usuariomod' => $id_doctor,
                      'ip_creacion' => $ip_cliente,
                      'ip_modificacion' =>$ip_cliente,
                      'created_at' => $fecha_creacion
                    ];

                    Orden_Procedimiento::create($input_orden_procedimiento_imagenes); 
                }
            }
        
        }else{
            
            return "false";   
        }

        $edad = $this->calcularEdadPaciente($paciente);


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

      $paciente = Paciente::findOrFail($id_paciente);
      $edad = $this->calcularEdadPaciente($paciente);

      $data_orden_imag = Orden::find($id_orden);

      $ndoctor = DB::table('users as us')->where('us.id',$data_orden_imag->id_doctor)->first();

      $tiposOrden = Orden_Tipo::where('id_orden', $data_orden_imag->id)->pluck('id');
      $procedimientosSeleccionados = $tiposOrden->isEmpty()
          ? collect()
          : Orden_Procedimiento::whereIn('id_orden_tipo', $tiposOrden)
              ->pluck('id_procedimiento')
              ->unique()
              ->values();
      $idsSeleccionados = $procedimientosSeleccionados->all();

      $gruposImagenes = grupo_procedimiento::where('tipo_procedimiento', 2)
          ->with(array('procedimientos' => function ($query) use ($idsSeleccionados) {
              $query->where(function ($procedimientos) use ($idsSeleccionados) {
                  $procedimientos->where('estado', 1);
                  if (!empty($idsSeleccionados)) {
                      $procedimientos->orWhereIn('id', $idsSeleccionados);
                  }
              })->orderBy('nombre');
          }))
          ->get();

      $gruposImagenes = $gruposImagenes
          ->filter(function ($grupo) {
              return $grupo->procedimientos->isNotEmpty();
          })
          ->sort(function ($grupoA, $grupoB) {
              $comparacionCantidad = $grupoA->procedimientos->count() <=> $grupoB->procedimientos->count();

              return $comparacionCantidad !== 0
                  ? $comparacionCantidad
                  : strcasecmp($grupoA->nombre, $grupoB->nombre);
          })
          ->values();

      $idsCatalogo = $gruposImagenes->flatMap(function ($grupo) {
          return $grupo->procedimientos->pluck('id');
      })->all();
      $idsAnteriores = array_values(array_diff($idsSeleccionados, $idsCatalogo));
      $procedimientosAnteriores = empty($idsAnteriores)
          ? collect()
          : Procedimiento::whereIn('id', $idsAnteriores)->orderBy('nombre')->get();

      return view('hc4/ordenes/orden_procedimiento_imagenes/editar_orden',[
          'data_orden_imag'=> $data_orden_imag,
          'ndoctor'=> $ndoctor,
          'edad'=> $edad,
          'paciente' => $paciente,
          'gruposImagenes' => $gruposImagenes,
          'procedimientosSeleccionados' => $idsSeleccionados,
          'procedimientosAnteriores' => $procedimientosAnteriores,
      ]);
    
    }


    //FUNCION PARA ACTUALIZAR LA ORDEN DE PROCEDIMIENTO FUNCIONAL
    //SOLO PARA DOCTORES
    public function actualiza_orden_imagenes(Request $request){

        $opcion = '2';
        if($this->rol_new($opcion)){
          return response()->json(['message' => 'No tiene permisos para actualizar esta orden.'], 403);
        }

        $id = $request->input('id_ordenimag');
        $idPaciente = $request->input('id_paciente');
        $procedimientos = array_values(array_unique(array_filter(
            (array) $request->input('x_procedimiento_imag', array()),
            function ($idProcedimiento) {
                return is_numeric($idProcedimiento);
            }
        )));

        if (count($procedimientos) === 0) {
            return response()->json(['message' => 'Seleccione al menos un procedimiento de imagen.'], 422);
        }

        $orden = Orden::where('id', $id)
            ->where('id_paciente', $idPaciente)
            ->where('tipo_procedimiento', 2)
            ->first();
        if (is_null($orden)) {
            return response()->json(['message' => 'La orden de imagen no existe o no pertenece al paciente.'], 404);
        }

        $tiposActuales = Orden_Tipo::where('id_orden', $orden->id)->pluck('id');
        $procedimientosActuales = $tiposActuales->isEmpty()
            ? array()
            : Orden_Procedimiento::whereIn('id_orden_tipo', $tiposActuales)
                ->pluck('id_procedimiento')
                ->all();

        $procedimientosSeleccionados = Procedimiento::whereIn('id', $procedimientos)
            ->where(function ($query) use ($procedimientosActuales) {
                $query->where('estado', 1);
                if (!empty($procedimientosActuales)) {
                    $query->orWhereIn('id', $procedimientosActuales);
                }
            })
            ->whereNotNull('id_grupo_procedimiento')
            ->where(function ($query) use ($procedimientosActuales) {
                $query->whereHas('grupo_procedimiento', function ($grupo) {
                    $grupo->where('tipo_procedimiento', 2);
                });
                if (!empty($procedimientosActuales)) {
                    $query->orWhereIn('id', $procedimientosActuales);
                }
            })
            ->get(array('id', 'id_grupo_procedimiento'));
        if ($procedimientosSeleccionados->count() !== count($procedimientos)) {
            return response()->json(['message' => 'Uno de los procedimientos seleccionados ya no esta disponible.'], 422);
        }

        $fechaActualizacion = date('Y-m-d H:i:s');
        $ipCliente = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $idUsuario = Auth::user()->id;
        // Este control esta comentado en la vista y, por tanto, no siempre se envia.
        $checkSeleccionado = (string) $request->input('firma_doctor_rob', '0') === '1' ? '1' : '0';
        $doctorFirma = $checkSeleccionado === '1' ? '1307189140' : $orden->id_doctor;

        DB::transaction(function () use ($request, $orden, $procedimientosSeleccionados, $fechaActualizacion, $ipCliente, $idUsuario, $checkSeleccionado, $doctorFirma) {
            Hc_Log::create(array(
                'anterior' => 'ORDEN_PROC_IMAGENES -> Motivo: ' . $orden->motivo_consulta . ' Resumen_Historia_Clinica: ' . $orden->resumen_clinico . ' Diagnosticos: ' . $orden->diagnosticos . ' Observacion_Medica: ' . $orden->observacion_medica . ' Observacion_Recepcion: ' . $orden->observacion_recepcion,
                'nuevo' => 'ORDEN_PROC_IMAGENES -> Motivo: ' . $request->input('xmotivo_orden') . ' Resumen_Historia_Clinica: ' . $request->input('imag_historia_clinica') . ' Diagnosticos: ' . $request->input('imag_des_diagnostico') . ' Observacion_Medica: ' . $request->input('xobservacion_orden') . ' Observacion_Recepcion: ' . $request->input('xobservacion_recepcion'),
                'id_paciente' => $orden->id_paciente,
                'id_usuariocrea' => $idUsuario,
                'id_usuariomod' => $idUsuario,
                'ip_creacion' => $ipCliente,
                'ip_modificacion' => $ipCliente,
            ));

            $tiposAnteriores = Orden_Tipo::where('id_orden', $orden->id)->pluck('id');
            if ($tiposAnteriores->count() > 0) {
                Orden_Procedimiento::whereIn('id_orden_tipo', $tiposAnteriores)->delete();
                Orden_Tipo::whereIn('id', $tiposAnteriores)->delete();
            }

            // Una orden puede contener procedimientos de distintos grupos. Se
            // crea un Orden_Tipo por cada grupo real; el antiguo valor fijo 20
            // provocaba una violacion de clave foranea en Odontologia.
            foreach ($procedimientosSeleccionados->groupBy('id_grupo_procedimiento') as $idGrupo => $procedimientosGrupo) {
                $idOrdenTipo = Orden_Tipo::insertGetId(array(
                    'id_orden' => $orden->id,
                    'id_grupo_procedimiento' => $idGrupo,
                    'id_usuariocrea' => $idUsuario,
                    'id_usuariomod' => $idUsuario,
                    'ip_creacion' => $ipCliente,
                    'ip_modificacion' => $ipCliente,
                    'created_at' => $fechaActualizacion,
                ));

                foreach ($procedimientosGrupo as $procedimientoSeleccionado) {
                    Orden_Procedimiento::create(array(
                        'id_orden_tipo' => $idOrdenTipo,
                        'id_procedimiento' => $procedimientoSeleccionado->id,
                        'id_usuariocrea' => $idUsuario,
                        'id_usuariomod' => $idUsuario,
                        'ip_creacion' => $ipCliente,
                        'ip_modificacion' => $ipCliente,
                        'created_at' => $fechaActualizacion,
                    ));
                }
            }

            $orden->update(array(
                'motivo_consulta' => $request->input('xmotivo_orden'),
                'resumen_clinico' => $request->input('imag_historia_clinica', $orden->resumen_clinico),
                'observacion_medica' => $request->input('xobservacion_orden'),
                'observacion_recepcion' => $request->input('xobservacion_recepcion'),
                'diagnosticos' => $request->input('imag_des_diagnostico', $orden->diagnosticos),
                'check_doctor' => $checkSeleccionado,
                'id_doctor_firma' => $doctorFirma,
                'ip_modificacion' => $ipCliente,
                'id_usuariomod' => $idUsuario,
                'updated_at' => $fechaActualizacion,
            ));
        });

        return response()->json(['ok' => true, 'message' => 'Orden de imagen guardada correctamente.']);
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
      $orden_proc_imagenes = Orden::findOrFail($id);

      if((is_null($orden_proc_imagenes->check_doctor))&&(is_null($orden_proc_imagenes->id_doctor_firma))){

        
        $doctor_firma = $orden_proc_imagenes->id_doctor; 

      }else{
       
        $doctor_firma = $orden_proc_imagenes->id_doctor_firma;
      
      }

      /*if (!is_null($orden_proc_imagenes)) {
                $firma = Firma_Usuario::where('id_usuario', $orden_proc_imagenes->id_doctor)->first();
      }*/

      $firma = Firma_Usuario::where('id_usuario', $doctor_firma)->first();
       
      $paciente = Paciente::findOrFail($orden_proc_imagenes->id_paciente);
      $edad = $this->calcularEdadPaciente($paciente);
      $seguro = Seguro::find($paciente->id_seguro);

      $doctor_solicitante = DB::table('users as us')
                            ->where('us.id',$orden_proc_imagenes->id_doctor)
                            ->first();

      $idsTiposOrden = Orden_Tipo::where('id_orden', $orden_proc_imagenes->id)->pluck('id');
      $idsProcedimientos = $idsTiposOrden->isEmpty()
          ? array()
          : Orden_Procedimiento::whereIn('id_orden_tipo', $idsTiposOrden)
              ->pluck('id_procedimiento')
              ->unique()
              ->values()
              ->all();

      $procedimientosImagenes = empty($idsProcedimientos)
          ? collect()
          : Procedimiento::with('grupo_procedimiento')
              ->whereIn('id', $idsProcedimientos)
              ->orderBy('nombre')
              ->get()
              ->groupBy('id_grupo_procedimiento')
              ->sortBy(function ($procedimientosGrupo) {
                  $grupo = optional($procedimientosGrupo->first())->grupo_procedimiento;

                  return $grupo ? $grupo->nombre : '';
              });

      $empresa = Empresa::find(session()->get('id_empresa'));
      $pdfImageData = function ($path) {
          if (!is_file($path) || !is_readable($path)) {
              return null;
          }

          $contents = file_get_contents($path);
          if ($contents === false) {
              return null;
          }

          $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'image/png';

          return 'data:' . ($mime ?: 'image/png') . ';base64,' . base64_encode($contents);
      };
      $recipeLogo = $pdfImageData(public_path('assets/images/logo_receta_borja.png'));
      $recipeWatermark = $pdfImageData(public_path('assets/images/fondo_receta_borja.png'));
      $recipeFooter = $pdfImageData(public_path('assets/images/pie_receta_borja.png'));
      $firmaImagen = !is_null($firma) && !empty($firma->nombre)
          ? $pdfImageData(storage_path('app/avatars/' . basename($firma->nombre)))
          : null;
      
      $vistaurl="hc4.ordenes.orden_procedimiento_imagenes.pdf_orden_imagenes";
      $pdfData = compact(
          'orden_proc_imagenes',
          'paciente',
          'edad',
          'doctor_solicitante',
          'seguro',
          'procedimientosImagenes',
          'empresa',
          'recipeLogo',
          'recipeWatermark',
          'recipeFooter',
          'firmaImagen'
      );
      $pdfData['_pdf_branding_view'] = 'hc_admision.receta.menbretadaor';
      $pdfData['_pdf_branding_company_id'] = optional($empresa)->id ?: session()->get('id_empresa');

      $pdf = \App::make('dompdf.wrapper');
      $pdf->loadView($vistaurl, $pdfData);
      $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true, 'chroot' => base_path('/')]);
      $pdf->setPaper('A4', 'portrait');

      return $pdf->stream($paciente->id . '_Orden_Imagenes_' . $id . '.pdf');
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


    
}
