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
use Sis_medico\Examen_Obligatorio;

use Sis_medico\Sala;
use Sis_medico\Examen;
use Sis_medico\Examen_Orden;
use Sis_medico\Examen_Nivel;
use Sis_medico\Examen_Agrupador;
use Sis_medico\Examen_Detalle;
use Sis_medico\Examen_Parametro;
use Sis_medico\Empresa;
use Sis_medico\Protocolo;
use Sis_medico\Convenio;
use Sis_medico\ControlDocController;
use Sis_medico\Http\Controllers\HorarioController;
use Excel;
use laravel\laravel;




class SemaforoController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 4, 5)) == false ){
          return true;
        }
        

    }
    
    public function index()
    { 

        if($this->rol()){
            return response()->view('errors.404');
        }
        $fecha = date('Y-m-d');

        $pentax_pend = $this->Cargar_pendientes($fecha);
        
        
        return view('laboratorio/semaforo/index', ['fecha' => $fecha,'pentax_pend' => $pentax_pend]);
    }

    public function search(Request $request)
    { 

        
        $pentax_pend = $this->Cargar_pendientes($request['fecha']);
        
        
        return view('laboratorio/semaforo/index', ['fecha' => $request['fecha'],'pentax_pend' => $pentax_pend]);
    }

    public function Cargar_pendientes($fecha){

        $nuevafecha = strtotime ( '-5 day' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );

        $pentaxs = DB::table('pentax_procedimiento as pp')->where('pp.created_at','>',$nuevafecha)->join('pentax as p','p.id','pp.id_pentax')->where('estado_pentax','<>','5')->join('agenda as a','a.id','p.id_agenda')->join('procedimiento as pr','pr.id','pp.id_procedimiento')->join('seguros as s','s.id','p.id_seguro')->join('paciente as pac','pac.id','a.id_paciente')->leftjoin('examen_pendiente as ep','ep.id_agenda','a.id')->select('pp.*','a.id as agenda','pr.nombre as procedimiento','p.id_seguro','a.fechaini','a.id_paciente','pac.nombre1','pac.apellido1','pac.nombre2','pac.apellido2','s.nombre as snombre','ep.observacion as epobservacion')->orderBy('a.fechaini')->get();
        $pentax_pend=[];
        $i=0;
//dd($pentaxs);
        foreach ($pentaxs as $pentax) {
            
                
            //////////////////////////

            $xtipo = Seguro::find($pentax->id_seguro)->tipo;    
    
            $pre_post = '0';
            $ex_pre = null;
            $ex_post = null;
            if($xtipo=='0'){
                /////////////CONTROL LABS/////////// BUSCA EXAMEN OBLIGATORIO

                $obligatorio = Examen_obligatorio::where('tipo','0')->where('id_procedimiento',$pentax->id_procedimiento)->first();
                $pre_post = '0';
                if(!is_null($obligatorio))
                {
                    $pre_post = $obligatorio->pre_post;//2 prey post

                }

                /////////////CONTROL LABS/////////// BUSCA EXAMEN EXCEPCION
                if($pre_post=='0'){
                    
                    $excepcion = Examen_obligatorio::where('tipo','1')->where('id_procedimiento',$pentax->id_procedimiento)->first();
                    $pre_post = '0';
                    if(is_null($excepcion))
                    {
                        $pre_post = '1';//2 pre

                    }
                    
                        
                }
                
                //ordenes del paciente de la ultima semana, pre y post
                $fecha_antes = Date('Y-m-d',strtotime('- 1 month',strtotime($pentax->fechaini)));
                $fecha_despues = Date('Y-m-d',strtotime('+5 day',strtotime($pentax->fechaini)));
                //dd($fecha_antes,$pentax->fechaini,$fecha_despues);

                $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente',$pentax->id_paciente)->where('eo.id_agenda',$pentax->agenda)->join('protocolo as p','p.id','eo.id_protocolo')->where('p.pre_post','PRE')->first();

                if(is_null($ex_pre)){
                    $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente',$pentax->id_paciente)->whereBetween('eo.created_at',[$fecha_antes, $fecha_despues])->join('protocolo as p','p.id','eo.id_protocolo')->where('p.pre_post','PRE')->first();    
                }


                $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente',$pentax->id_paciente)->where('eo.id_agenda',$pentax->agenda)->join('protocolo as p','p.id','eo.id_protocolo')->where('p.pre_post','POST')->first();

                if(is_null($ex_post)){
                    $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente',$pentax->id_paciente)->whereBetween('eo.created_at',[$fecha_antes, $fecha_despues])->join('protocolo as p','p.id','eo.id_protocolo')->where('p.pre_post','POST')->first();    
                } 
                   
            }


            if($pre_post=='2'){
                
                $p1=""; $p2="";
                if(!is_null($ex_pre)){
                    if($ex_pre->realizado=='0'){
                        $p1="<span class=bg-yellow style='border-radius: 4px;'> PRE</span>";
                    }
                }else{
                    $p1="<span class=bg-red style='border-radius: 4px;'> PRE</span>";    
                }
                if(!is_null($ex_post)){
                    if($ex_post->realizado=='0'){
                        $p2="<span class=bg-yellow style='border-radius: 4px;'> POST</span>";
                    }    
                }else{
                    $p2="<span class=bg-red style='border-radius: 4px;'> POST</span>";    
                } 
                if($p1!=""||$p2!=""){
                    $pentax_pend[$pentax->agenda] = [$pentax,$p1,$p2];    
                }   
            }
            if($pre_post=='1'){

                if(is_null($ex_pre)){
                    
                    if(!isset($pentax_pend[$pentax->agenda])){
                        $pentax_pend[$pentax->agenda] = [$pentax,"<span class=bg-red style='border-radius: 4px;'> PRE</span>",""];
                        
                    }        
                }else{

                    if($ex_pre->realizado=='0'){
                        if(!isset($pentax_pend[$pentax->agenda])){
                            $pentax_pend[$pentax->agenda] = [$pentax,"<span class=bg-yellow style='border-radius: 4px;'> PRE</span>",""];
                                
                        }
                        
                    }
                    
                }
            }
        
        } 
//dd($pentax_pend);
        return  $pentax_pend; 


    }

}    


    

    