<?php

namespace Sis_medico\Http\Controllers\hc_admision;

 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_cpre_eco;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Historiaclinica;
use Sis_medico\Orden;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\procedimiento_completo;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\tipo_procedimiento;


class OrdenesExamenesController extends Controller
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
     private function rol_paciente(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(2)) == false){
          return true;
        }
    }

    public function historial_examenes(){
        
        if($this->rol_paciente()){
            return response()->view('errors.404');
        }
        
        $id_paciente = Auth::user()->id;
         
        //Buscamos si el Usuario que se Loguea se encuentra Registrado en la Tabla Pacientes
        $paciente = Paciente::find($id_paciente);

       
        if(!is_null($paciente)){

            $listado_ordenes = DB::table('orden as ord')
                          ->join('paciente as p','p.id','ord.id_paciente')
                          ->join('users as usuario','usuario.id','p.id_usuario')
                          ->join('hc_evolucion as evo','evo.id','ord.id_evolucion')
                          ->join('historiaclinica as h', 'h.hcid', 'evo.hcid')
                          ->join('seguros as s', 's.id', 'h.id_seguro')
                          ->join('users as d', 'd.id', 'h.id_doctor1')
                          ->where('usuario.id',$id_paciente)
                          ->where('ord.estado',1)
                          ->select('s.nombre as snombre','p.nombre1 as pnombre1','p.nombre2 as pnombre2','p.apellido2 as papellido2','p.apellido1 as papellido1','p.id as id_paciente','p.parentesco as parentesco','d.nombre1 as dnombre1', 'd.apellido1 as dapellido1','h.id_doctor1','p.fecha_nacimiento as fecha_nacimiento'
                                   ,'ord.fecha_orden as fecha_orden','ord.id as id_orden','ord.tipo_procedimiento as tipo_procedimiento'
                                   ,'ord.necesita_valoracion as necesita_valoracion'
                                   ,'p.antecedentes_pat as antecedentes_patologico','p.antecedentes_fam as antecedentes_familiares'
                                   ,'p.antecedentes_quir as antecedentes_quirurgico'
                                   ,'ord.motivo_consulta as motivo_consulta'
                                   ,'ord.resumen_clinico as resumen_clinico'
                                   ,'ord.diagnosticos as diagnostico'
                                   ,'ord.observacion_medica as obs_medica'
                                   ,'ord.observacion_recepcion as obs_recepcion')
                          ->OrderBy('ord.created_at', 'desc')
                          ->get();

            return view('hc_admision/orden_proc/ordenes_examenes',['listado_ordenes' => $listado_ordenes, 'paciente' => $paciente]);
    
        }
    
    }


}
    