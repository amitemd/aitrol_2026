<?php

namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller; 
use Sis_medico\User; 
use Sis_medico\Bodega; 
use Sis_medico\hc_procedimientos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input; 
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Sis_medico\Hc_Cie10;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Agenda;
use Sis_medico\Seguro;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\hc_receta;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Historiaclinica;
use Sis_medico\Paciente;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Principio_Activo;
use Response;

class EvolucionesController extends Controller
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

        $evoluciones = DB::table('hc_evolucion as e')->join('historiaclinica as h','e.hcid','h.hcid')->where('h.id_paciente',$id_paciente)->join('agenda as a','a.id','h.id_agenda')->orderBy('a.fechaini','desc')->select('e.*','a.fechaini','a.proc_consul','a.espid', 'h.id_seguro')->orderBy('e.id','desc')->get();//value
       // dd($evoluciones);
        $id_agenda = null;
       if(!is_null($paciente->agenda()->get()->last())){
        $id_agenda = $paciente->agenda()->get()->last()->id;
        }

        $agenda = null;

        if(!is_null($id_agenda)){
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->leftjoin('historiaclinica as h','h.id_agenda','agenda.id')
            ->leftjoin('seguros as hs','hs.id','h.id_seguro')
            ->leftjoin('users as ud','ud.id','agenda.id_doctor1')
            ->select('agenda.*','seguros.nombre as snombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido')
            ->where('agenda.id', '=', $id_agenda)
            ->first();
          }
//dd($evoluciones);

        return view('hc4/evoluciones/index', ['id_agenda' => $id_agenda,  'evoluciones' => $evoluciones, 'agenda' => $agenda]);
    }

      public function editar($id, $id_agenda){
        
        $opcion = '2';
         if($this->rol_new($opcion)){ 
             return redirect('/');
         }

           $evoluciones = DB::table('hc_evolucion as e')->join('historiaclinica as h','e.hcid','h.hcid')->where('e.id', $id)->join('agenda as a','a.id','h.id_agenda')->orderBy('a.fechaini','desc')->select('h.id_paciente', 'e.*','a.fechaini','a.proc_consul','a.espid')->first();

          //dd($evoluciones);
           $procedimientos=null;
        
        if(!is_null($evoluciones))
        {
            $procedimientos = hc_procedimientos::find($evoluciones->hc_id_procedimiento);
        }

        $seguros = Seguro::where('inactivo', '1')->get();
     
        $doctores = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->leftjoin('historiaclinica as h','h.id_agenda','agenda.id')
            ->leftjoin('seguros as hs','hs.id','h.id_seguro')
            ->leftjoin('users as ud','ud.id','agenda.id_doctor1')
            ->select('agenda.*','seguros.nombre as snombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido')
            ->where('agenda.id', '=', $id_agenda)
            ->first();


            //dd($agenda);
        return view('hc4/evoluciones/editar', ['id' => $id, 'seguros' => $seguros, 'evoluciones' => $evoluciones, 'procedimientos' => $procedimientos, 'seguros' => $seguros, 'doctores' => $doctores, 'agenda' => $agenda]);
     }




 public function actualizar (Request $request){

    //return $request['id_paciente'];
    $ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;

    $evolucion = hc_evolucion::find($request->id);

    $procedimientos=null;
    if(!is_null($evolucion))
        {
          $procedimientos = hc_procedimientos::find($evolucion->hc_id_procedimiento);
        }

      $proc = $procedimientos;
      if (!is_null($proc)){ 
        
      $evoluciones_new = [
            'anterior' => 'EVOLUCION -> Observacion: '.$proc->observaciones.'   Seguro:'.$proc->id_seguro.'   doctor:'.$proc->id_doctor_examinador,
            'nuevo' => 'EVOLUCION -> Observacion: '.$request['observacion'].'   Seguro:'.$request['seguro'].'   doctor:'.$request['med_examinador'],
            'hc_id' => $procedimientos->id_hc,
            'id_paciente' => $request['id_paciente'],
            'id_procedimiento' => $procedimientos->id,
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        ];
        Hc_Log::create($evoluciones_new);
      }

      $evol = $evolucion;
      $evolucion_new = [
            'anterior' => 'EVOLUCION -> Evolucion: '.$evol->cuadro_clinico.'  Motivo:'.$evol->motivo. '  Resultado: '.$evol->resultado,
            'nuevo' => 'EVOLUCION -> Evolucion: '.$request['evolucion'].'   Motivo:'.$request['motivo']. '  Resultado: '.$request['resultado_exam'],
            'hc_id' => $evol->hcid,
            'id_paciente' => $request['id_paciente'],
            'id_procedimiento' => $evol->hc_id_procedimiento,
            'id_evolucion' => $evol->id,
            'id_usuariomod' => $idusuario,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,           
        ];
        Hc_Log::create($evolucion_new);

      $procedimientos->observaciones = $request['observacion'];
      $procedimientos->id_seguro = $request['seguro'];
      $procedimientos->id_doctor_examinador = $request['med_examinador'];
      $procedimientos->id_usuariomod = $idusuario;
      $procedimientos->ip_modificacion = $ip_cliente;
      $procedimientos->save();

      $evolucion->cuadro_clinico = $request['evolucion'];
      $evolucion->motivo = $request['motivo'];
      $evolucion->resultado = $request['resultado_exam'];
      $evolucion->ip_modificacion = $ip_cliente;
      $evolucion->id_usuariomod = $idusuario;
      $evolucion->save();

      //LAMA A LA VISTA UNICO
    
      $evoluciones = DB::table('hc_evolucion as e')->join('historiaclinica as h','e.hcid','h.hcid')->where('e.id',$request->id)->join('agenda as a','a.id','h.id_agenda')->orderBy('a.fechaini','desc')->select('e.*','a.fechaini','a.proc_consul','a.espid')->orderBy('e.id','desc')->get()->first();//value

      //dd($evoluciones);
      $agenda = null;
      if(!is_null($request->id_agenda)){
      $agenda = DB::table('agenda')
          ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
          ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
          ->leftjoin('historiaclinica as h','h.id_agenda','agenda.id')
          ->leftjoin('seguros as hs','hs.id','h.id_seguro')
          ->leftjoin('users as ud','ud.id','agenda.id_doctor1')
          ->select('agenda.*','seguros.nombre as snombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido')
          ->where('agenda.id', '=', $request->id_agenda)
          ->first();
        }
 //dd("dfsf");
      return view('hc4/evoluciones/unico', ['evoluciones' => $evoluciones, 'agenda' => $agenda ]);

    }

      public function hc4_agregar_cie10(Request $request) {

        $ip_cliente= $_SERVER["REMOTE_ADDR"]; 
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if($request['codigo']==null){
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];   
        }

        $cie10= DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento',$request['hc_id_procedimiento'])->get();
        
        //dd($cie10);
        if(!is_null($cie10)){
            foreach($cie10 as $value){

            $diagnostico_new = [
              'anterior' => 'EVOLUCION -> Diagnostico: '.$value->cie10,
              'nuevo' => 'EVOLUCION -> Diagnostico: '.$request['codigo'],
              'hc_id' => $value->hcid,
              'id_paciente' => $request['id_paciente'],
              'id_procedimiento' => $value->hc_id_procedimiento,
              'id_usuariomod' => $idusuario,
              'id_usuariocrea' => $idusuario,
              'ip_modificacion' => $ip_cliente,
              'ip_creacion' => $ip_cliente,           
            ];
        Hc_Log::create($diagnostico_new);

            }
          }


        $input2 = [
                    'hcid' => $request['hcid'],
                    'cie10' => $request['codigo'],
                    'hc_id_procedimiento' => $request['hc_id_procedimiento'],
                    'ingreso_egreso' => $request['in_eg'],
                    'presuntivo_definitivo' => $request['pre_def'],

                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,

                    ];
        $id = Hc_Cie10::insertGetId($input2);

        $count = Hc_Cie10::where('hc_id_procedimiento', $request['hc_id_procedimiento'])->get()->count();
        //dd($count);
        
        $cie10 = Hc_Cie10::find($id);

        
        $c3 = Cie_10_3::find($cie10->cie10);
        if(!is_null($c3)){
            $descripcion = $c3->descripcion;
        }    
        $c4 = Cie_10_4::find($cie10->cie10);
        if(!is_null($c4)){
            $descripcion = $c4->descripcion;
        }

        return ['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => $request['in_eg']];
    }  

}
