<?php

namespace Sis_medico\Http\Controllers\laboratorio;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Seguro;
use Sis_medico\Subseguro;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\User_espe;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Historiaclinica;
use Sis_medico\Archivo_historico;
use Sis_medico\Agenda;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Procedimiento;
use Sis_medico\Pentax_log;

use Sis_medico\Sala;
use Sis_medico\Examen;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Parametro;

use Sis_medico\Examen_Resultado;
use Sis_medico\Empresa;
use Sis_medico\Protocolo;
use Sis_medico\Convenio;
use Sis_medico\Examen_Detalle_Costo;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;
use laravel\laravel;
use Carbon\Carbon;




class AgendaLabsController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 10)) == false){
          return true;
        }
    }

    public function  agenda(Request $request){  

        if($this->rol()){
            return response()->view('errors.404');
        }
        
        if($request['fecha']==null){
            $fecha_hoy = Date('Y-m-d');    
        }else{
            $fecha_hoy = Date('Y-m-d',strtotime($request['fecha']));
        }
        
        $fecha_desde = date('Y-m-d',strtotime($fecha_hoy."- 120 days")); 

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            //->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro')
//            ->where('agenda.proc_consul', '=', 1)
            ->where('agenda.espid',10)
            ->where('agenda.estado',1)
            ->where('agenda.created_at','>',$fecha_desde)
            ->get();

        //dd($agenda);    
            
    

        return view('laboratorio/agenda/calendario', [ 'agenda' => $agenda, 'fecha_hoy' => $fecha_hoy]);
    }

    public function  laboratorio($paciente){  

        if($this->rol()){
            return response()->view('errors.404');
        }
        
        $pac = Paciente::find($paciente);

        $nombres = $pac->nombre1.' '.$pac->apellido1;

        $fecha_hasta = Date('Y-m-d');    
        
        
        $fecha = date('Y-m-d',strtotime($fecha_hasta."- 31 days")); 

        $ordenes = DB::table('examen_orden as eo')->join('paciente as p','p.id','eo.id_paciente')->join('seguros as s','s.id','eo.id_seguro')->leftjoin('empresa as em','em.id','eo.id_empresa')->leftjoin('nivel as n','n.id','eo.id_nivel')->leftjoin('protocolo as proto','proto.id','eo.id_protocolo')->leftjoin('users as d','d.id','eo.id_doctor_ieced')->join('users as cu','cu.id','eo.id_usuariocrea')->join('users as mu','mu.id','eo.id_usuariomod')->select('eo.*','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','d.nombre1 as dnombre1','d.apellido1 as dapellido1','s.nombre as snombre','n.nombre as nnombre','em.nombrecomercial','cu.nombre1 as cnombre1','cu.apellido1 as capellido1','mu.nombre1 as mnombre1','mu.apellido1 as mapellido1','em.nombre_corto', 'proto.pre_post','s.tipo as stipo');

        $ordenes = $ordenes->whereBetween('eo.created_at', [$fecha.' 00:00', $fecha_hasta.' 23:59']);

        $ordenes = $ordenes->where('eo.id_paciente',$paciente);

        //dd($ordenes->get());
        
        $ordenes = $ordenes->where('eo.estado','1')->paginate(30);



        $ex_det=[];
        foreach ($ordenes as $orden) {
            
            $examen_par = DB::table('examen_detalle as ed')->where('ed.id_examen_orden',$orden->id)->join('examen_parametro as ep','ep.id_examen','ed.id_examen')->select('ed.id_examen')->groupBy('ed.id_examen')->get(); 
            $resultado = DB::table('examen_resultado as er')->where('er.id_orden',$orden->id)->join('examen_parametro as ep','ep.id','er.id_parametro')->select('ep.id_examen')->groupBy('ep.id_examen')->get();
            $ex_det[$orden->id] = $examen_par->count() - $resultado->count();
            //dd($resultado->count(),$examen_par->count());
        }

        $seguros = Seguro::where('inactivo','1')->get();

        return view('laboratorio/orden/index_control', ['ordenes' => $ordenes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'ex_det' => $ex_det, 'nombres' => $nombres, 'seguros' => $seguros, 'seguro' => null]); 
            

    }

   
  
               

    


}