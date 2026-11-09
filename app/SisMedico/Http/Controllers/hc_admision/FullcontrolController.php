<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\Bodega;
use Illuminate\Support\Facades\DB;
use Sis_medico\tipousuario;
use Sis_medico\Hospital;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Sis_medico\Seguro;
use Sis_medico\Empresa;
use Sis_medico\Subseguro;
use Sis_medico\Documento;
use Sis_medico\Archivo_historico;
use Sis_medico\Historiaclinica;
use Sis_medico\Agenda;
use Sis_medico\Sala;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\Log_usuario;
use Sis_medico\Log_agenda;
use Sis_medico\Pentax_log;
use Sis_medico\Pentax;
use Sis_medico\Especialidad;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Response;
use Cookie;

class FullcontrolController extends Controller
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
    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1, 3, 6, 11, 13,5, 7,20)) == false){
          return true;
        }
    }

    /* public function index(Request $request){
    	 //return $request->all();
         $rolUsuario = Auth::user()->id_tipo_usuario;  
        if($this->rol()){
            return response()->view('errors.404');
        }

        $nomb_proc = null;
        $id_seguro = $request->id_seguro;
        $espid = $request->espid;
        $proc_consul = $request->proc_consul;
        $id_especialidad = $request->id_especialidad;
        $id_doctor1 = $request->id_doctor1;
        $nombres = $request['nombres'];
        $fecha = $request['fecha']; 
        $fecha_hasta = $request['fecha_hasta'];  

        $especialidades = Especialidad::where('estado', 1)->orderBy('nombre', 'asc')->get();
        $doctores = User::where('id_tipo_usuario','3')->Orderby('apellido1','asc')->get();
        $seguros = Seguro::where('inactivo','1')->Orderby('nombre','asc')->get();


        $agendas = [];
        $consultas = []; 
      
        if($fecha==null){
            $fecha = Date('Y-m-d');
        } 

        if($fecha_hasta==null){
            $fecha_hasta = Date('Y-m-d');
        } 
        if($proc_consul==null){
            $proc_consul = '2';
        }



        $nombres_sql='';
        $agendas_pac = [];

        /*if($nombres!=null){//BUSCA POR PACIENTE
            $agendas_pac = DB::table('paciente as p')->leftjoin('historiaclinica as h','h.id_paciente','p.id')->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento')->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento');

            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2); 
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }
            $nombres_sql= $nombres_sql.'%';

            
            if($cantidad=='2' || $cantidad=='3'){       
                    $agendas_pac = $agendas_pac->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
                      
            }
            else{

                //$agendas_pac = $agendas_pac->whereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                $agendas_pac = $agendas_pac->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                    });
            }  

            $agendas_pac = $agendas_pac->limit('100')->get(); 
            
            $agendas_proc=null;
            

        }else{//BUSCA POR FECHAS

            $agendas_pac = DB::table('agenda as a')->where('a.estado',1)->whereBetween('fechaini',[$fecha.' 00:00',$fecha_hasta.' 23:59'])->join('historiaclinica as h','h.id_agenda','a.id')->join('paciente as p','p.id','a.id_paciente')->join('users as d','d.id','h.id_doctor1')->join('empresa as em','em.id','a.id_empresa')->join('seguros as se','se.id','h.id_seguro')->where('a.espid','<>','10')->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto')->Orderby('a.fechaini','asc');

            $agendas_pac2 = DB::table('agenda as a')->where('a.omni','OM')->whereBetween('fechaini',[$fecha.' 00:00',$fecha_hasta.' 23:59'])->join('historiaclinica as h','h.id_agenda','a.id')->join('paciente as p','p.id','a.id_paciente')->join('users as d','d.id','h.id_doctor1')->join('empresa as em','em.id','a.id_empresa')->join('seguros as se','se.id','h.id_seguro')->where('a.espid','<>','10')->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto')->Orderby('a.fechaini','asc');

            $agendas_pac = $agendas_pac->union($agendas_pac2)->get();

            $agendas_proc=null;
            foreach($agendas_pac as $pac){

                if($pac->proc_consul=='1'){
                    $pentax = Pentax::where('id_agenda',$pac->id_agenda)->first();
                    if(!is_null($pentax)){
                        $txt_px='';
                        foreach ($pentax->procedimientos as $p) {
                            if($txt_px==''){
                                $txt_px=$p->procedimiento->nombre;
                            }else{
                                $txt_px = $txt_px.'+'.$p->procedimiento->nombre;
                            }
                            
                        }
                        //dd($txt_px);
                        $agendas_proc[$pac->id_agenda] = [$txt_px];        
                    }
                         
                }


            }
            

        } 
        if($nombres!=null)
        {

            $pacientes = DB::table('paciente as p')
                         ->leftjoin('historiaclinica as h','h.id_paciente','p.id')
                         ->leftjoin('agenda as a','h.id_agenda','a.id')
                         ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento')
                         ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento', 'a.estado_cita');
           
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2);
            
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }

            $nombres_sql= $nombres_sql.'%';
            
            
            if($cantidad > '1'){       
                    $pacientes = $pacientes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });

                   // dd($pacientes);
                
            }
            else{

                $pacientes = $pacientes->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                });
                
            }  

            $pacientes = $pacientes->limit('300')->get(); 
            $agendas_proc=null;
        
        } else { //BUSCA POR FECHAS
                  

            $pacientes1 = DB::table('agenda as a')->where('a.estado',1)
                          ->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
                          ->join('paciente as p','p.id','a.id_paciente')
                          ->join('users as d','d.id','a.id_doctor1')
                          ->leftjoin('empresa as em','em.id','a.id_empresa')
                          ->join('seguros as se','se.id','a.id_seguro')
                          ->leftjoin('historiaclinica as h','h.id_agenda','a.id')
                          ->leftjoin('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                          ->whereNull('h.hcid')
                          ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'a.id_doctor1 as doctor', 'a.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');
            

            $pacientes2 = DB::table('agenda as a')->where('a.estado',1)
                          ->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
                          ->join('paciente as p','p.id','a.id_paciente')
                          ->join('users as d','d.id','a.id_doctor1')
                          ->leftjoin('empresa as em','em.id','a.id_empresa')
                          ->join('seguros as se','se.id','a.id_seguro')
                          ->join('historiaclinica as h','h.id_agenda','a.id')
                          ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                          ->where('a.proc_consul', '0')
                          ->whereNull('hc_pro.id_doctor_examinador')
                          ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');



            $pacientes2_0 = DB::table('agenda as a')->where('a.estado',1)
                          ->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
                          ->join('paciente as p','p.id','a.id_paciente')
                          ->join('users as d','d.id','a.id_doctor1')
                          ->leftjoin('empresa as em','em.id','a.id_empresa')
                          ->join('seguros as se','se.id','a.id_seguro')
                          ->join('historiaclinica as h','h.id_agenda','a.id')
                          ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                          ->where('a.proc_consul', '0')
                          ->whereNotNull('hc_pro.id_doctor_examinador')
                          ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');             

 
            $pacientes3 = DB::table('agenda as a')->where('a.estado',1)
                          ->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
                          ->join('paciente as p','p.id','a.id_paciente')
                          ->join('users as d','d.id','a.id_doctor1')
                          ->leftjoin('empresa as em','em.id','a.id_empresa')
                          ->join('seguros as se','se.id','a.id_seguro')
                          ->join('historiaclinica as h','h.id_agenda','a.id')
                          ->where('a.proc_consul', '1')
                          ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');
                          

            $pacientes4 = DB::table('agenda as a')->where('a.estado',4)
                          ->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
                          ->join('paciente as p','p.id','a.id_paciente')
                          ->join('users as d','d.id','a.id_doctor1')
                          ->leftjoin('empresa as em','em.id','a.id_empresa')
                          ->join('seguros as se','se.id','a.id_seguro')
                          ->join('historiaclinica as h','h.id_agenda','a.id')
                          ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                          ->where('a.proc_consul', '4')
                          ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');
            


              if($proc_consul != 2){
              //  $pacientes = $pacientes->where('a.proc_consul', $proc_consul);
                if($proc_consul == 3){
                  $pacientes1 = $pacientes1->where('a.omni', 'OM');
                  $pacientes2 = $pacientes2->where('a.omni', 'OM');
                  $pacientes2_0 = $pacientes2_0->where('a.omni', 'OM');
                  $pacientes3 = $pacientes3->where('a.omni', 'OM');
                  $pacientes4 = $pacientes4->where('a.omni', 'OM');
                }elseif ($proc_consul == 4) {
                  $pacientes1 = $pacientes1->where('a.omni', 'SI');
                  $pacientes2 = $pacientes2->where('a.omni', 'SI');
                  $pacientes2_0 = $pacientes2_0->where('a.omni', 'SI');
                  $pacientes3 = $pacientes3->where('a.omni', 'SI');
                  $pacientes4 = $pacientes4->where('a.omni', 'SI');
                }
                else{
                  $pacientes1 = $pacientes1->where('a.proc_consul', $proc_consul);
                  $pacientes2 = $pacientes2->where('a.proc_consul', $proc_consul);
                  $pacientes2_0 = $pacientes2_0->where('a.proc_consul', $proc_consul);
                  $pacientes3 = $pacientes3->where('a.proc_consul', $proc_consul);
                  $pacientes4 = $pacientes4->where('a.proc_consul', $proc_consul);
                }
                  
              }
  

              if(!is_null($id_doctor1)){
              //  $pacientes = $pacientes->where('a.id_doctor1', $id_doctor1);
                $pacientes1 = $pacientes1->where('a.id_doctor1', $id_doctor1);
                $pacientes2 = $pacientes2->where('h.id_doctor1', $id_doctor1);
                $pacientes2_0 = $pacientes2_0->where('hc_pro.id_doctor_examinador', $id_doctor1);
                $pacientes3 = $pacientes3->where('h.id_doctor1', $id_doctor1);
                $pacientes4 = $pacientes4->where('hc_pro.id_doctor_examinador', $id_doctor1);

              }


              if(!is_null($id_seguro)){
              //  $pacientes = $pacientes->where('a.id_seguro', $id_seguro);
                $pacientes1 = $pacientes1->where('a.id_seguro', $id_seguro);
                $pacientes2 = $pacientes2->where('h.id_seguro', $id_seguro);
                $pacientes2_0 = $pacientes2_0->where('h.id_seguro', $id_seguro);
                $pacientes3 = $pacientes3->where('h.id_seguro', $id_seguro);
                $pacientes4 = $pacientes4->where('h.id_seguro', $id_seguro);
              }

              if(!is_null($espid)){
              //  $pacientes = $pacientes->where('a.espid', $espid);
                $pacientes1 = $pacientes1->where('a.espid', $espid);
                $pacientes2 = $pacientes2->where('a.espid', $espid);
                $pacientes2_0 = $pacientes2_0->where('a.espid', $espid);
                $pacientes3 = $pacientes3->where('a.espid', $espid);
                $pacientes4 = $pacientes4->where('a.espid', $espid);

              }                         
                          
         
              $pacientes1 = $pacientes1->union($pacientes2)->union($pacientes2_0)->union($pacientes3)->union($pacientes4);            

              $pacientes1 = $pacientes1->limit('300')->get();
              $pacientes = $pacientes1;

                        
                $agendas_proc=null;
                foreach($pacientes as $pac){
                    //dd($pac);
                    if($pac->proc_consul=='1'){
                        $pentax = Pentax::where('id_agenda',$pac->id_agenda)->first();
                        if(!is_null($pentax)){
                            $txt_px='';
                            foreach ($pentax->procedimientos as $p) {
                                if($txt_px==''){
                                    $txt_px=$p->procedimiento->nombre;
                                }else{
                                    $txt_px = $txt_px.'+'.$p->procedimiento->nombre;
                                }
                                
                            }
                            //dd($txt_px);
                            $agendas_proc[$pac->id_agenda] = [$txt_px];        
                        }
                        // dd($agendas_proc[$pac->id_agenda]);    
                    }

                }


        }


        Cookie::queue('ruta','historia','1000');


        return view('hc_admision/fullcontrol/index', ['nombres' => $nombres, 'agendas_pac' => $pacientes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'agendas_proc' => $agendas_proc, 'proc_consul' => $proc_consul, 'especialidades' => $especialidades, 'id_especialidad' => '0', 'doctores' => $doctores, 'id_doctor1' => $id_doctor1, 'seguros' => $seguros, 'id_seguro' => $id_seguro]);
    } */

    public function index(Request $request){
        //return $request->all();
        $rolUsuario = Auth::user()->id_tipo_usuario;  
        if($this->rol()){
            return response()->view('errors.404');
        }

        $nomb_proc = null;
        $id_seguro = $request->id_seguro;
        $espid = $request->espid;
        $proc_consul = $request->proc_consul;
        $id_especialidad = $request->id_especialidad;
        $id_doctor1 = $request->id_doctor1;
        $nombres = $request['nombres'];
        $fecha = $request['fecha']; 
        $fecha_hasta = $request['fecha_hasta'];  

        $especialidades = Especialidad::where('estado', 1)->orderBy('nombre', 'asc')->get();
        $doctores = User::where('id_tipo_usuario','3')->Orderby('apellido1','asc')->get();
        $seguros = Seguro::Orderby('nombre','asc')->get();

        $agendas = [];
        $consultas = []; 
      
        if($fecha==null && $nombres==null){
            $fecha = Date('Y-m-d');
            if($rolUsuario == '11'){
                $fecha = Date('Y-m-d', strtotime("-2 months"));

            }
        } 
        if($proc_consul==null && $rolUsuario == '11'){
        
            $proc_consul = '1';
        }

        if($fecha_hasta==null){
            $fecha_hasta = Date('Y-m-d');
        } 
        if($proc_consul==null){
            $proc_consul = '2';
        }

        $nombres_sql='';
        $agendas_pac = [];

        
        $pacientes1 = DB::table('agenda as a')->where('a.estado',1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->leftjoin('historiaclinica as h','h.id_agenda','a.id')
            ->leftjoin('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->whereNull('h.hcid')
            ->where('a.id_doctor1','<>','4444444444')
            ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.id')
            ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'a.id_doctor1 as doctor', 'a.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');

        if($fecha != null){
            $pacientes1 = $pacientes1->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }    
            

        $pacientes2 = DB::table('agenda as a')->where('a.estado',1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->join('historiaclinica as h','h.id_agenda','a.id')
            ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->where('a.proc_consul', '0')
            ->where('a.id_doctor1','<>','4444444444')
            ->whereNull('hc_pro.id_doctor_examinador')
            ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.id')
            ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');

        if($fecha != null){
                $pacientes2 = $pacientes2->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }           

        $pacientes2_0 = DB::table('agenda as a')->where('a.estado',1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->join('historiaclinica as h','h.id_agenda','a.id')
            ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->where('a.proc_consul', '0')
            ->where('a.id_doctor1','<>','4444444444')
            ->whereNotNull('hc_pro.id_doctor_examinador')
            ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.id')
            ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');            

        if($fecha != null){
                $pacientes2_0 = $pacientes2_0->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }    

        $pacientes3 = DB::table('agenda as a')->where('a.estado',1)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->join('historiaclinica as h','h.id_agenda','a.id')
            ->where('a.proc_consul', '1')
            ->where('a.id_doctor1','<>','4444444444')
            ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.id')
            ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');
                          
        if($fecha != null){
                if(Auth::user()->id_tipo_usuario == '1' ){                  
                    //dd($pacientes3->where('p.id','0955695630')->get());
                } 
                $pacientes3 = $pacientes3->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59']);
                
        }            

        $pacientes4 = DB::table('agenda as a')->where('a.estado',4)
            //->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59'])
            ->join('paciente as p','p.id','a.id_paciente')
            ->join('users as d','d.id','a.id_doctor1')
            ->leftjoin('empresa as em','em.id','a.id_empresa')
            ->join('seguros as se','se.id','a.id_seguro')
            ->join('historiaclinica as h','h.id_agenda','a.id')
            ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
            ->where('a.proc_consul', '4')
            ->where('a.id_doctor1','<>','4444444444')
            ->groupBy('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.id')
            ->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','a.omni');
        
        if($fecha != null){
                $pacientes4 = $pacientes4->whereBetween('fechaini',[$fecha.' 00:00', $fecha_hasta.' 23:59']);
        }        

        if($proc_consul != 2){
            if($proc_consul == 3){
                $pacientes1 = $pacientes1->where('a.omni', 'OM');
                $pacientes2 = $pacientes2->where('a.omni', 'OM');
                $pacientes2_0 = $pacientes2_0->where('a.omni', 'OM');
                $pacientes3 = $pacientes3->where('a.omni', 'OM');
                $pacientes4 = $pacientes4->where('a.omni', 'OM');
            }elseif ($proc_consul == 4) {
                $pacientes1 = $pacientes1->where('a.omni', 'SI');
                $pacientes2 = $pacientes2->where('a.omni', 'SI');
                $pacientes2_0 = $pacientes2_0->where('a.omni', 'SI');
                $pacientes3 = $pacientes3->where('a.omni', 'SI');
                $pacientes4 = $pacientes4->where('a.omni', 'SI');
            }
            else{
                $pacientes1 = $pacientes1->where('a.proc_consul', $proc_consul);
                $pacientes2 = $pacientes2->where('a.proc_consul', $proc_consul);
                $pacientes2_0 = $pacientes2_0->where('a.proc_consul', $proc_consul);
                $pacientes3 = $pacientes3->where('a.proc_consul', $proc_consul);
                $pacientes4 = $pacientes4->where('a.proc_consul', $proc_consul);
            }
              
        }
  
        if(!is_null($id_doctor1)){
            $pacientes1 = $pacientes1->where('a.id_doctor1', $id_doctor1);
            $pacientes2 = $pacientes2->where('h.id_doctor1', $id_doctor1);
            $pacientes2_0 = $pacientes2_0->where('hc_pro.id_doctor_examinador', $id_doctor1);
            $pacientes3 = $pacientes3->where('h.id_doctor1', $id_doctor1);
            $pacientes4 = $pacientes4->where('hc_pro.id_doctor_examinador', $id_doctor1);

        }


        if(!is_null($id_seguro)){
            $pacientes1 = $pacientes1->where('a.id_seguro', $id_seguro);
            $pacientes2 = $pacientes2->where('h.id_seguro', $id_seguro);
            $pacientes2_0 = $pacientes2_0->where('h.id_seguro', $id_seguro);
            $pacientes3 = $pacientes3->where('h.id_seguro', $id_seguro);
            $pacientes4 = $pacientes4->where('h.id_seguro', $id_seguro);
        }

        if(!is_null($espid)){
            $pacientes1 = $pacientes1->where('a.espid', $espid);
            $pacientes2 = $pacientes2->where('a.espid', $espid);
            $pacientes2_0 = $pacientes2_0->where('a.espid', $espid);
            $pacientes3 = $pacientes3->where('a.espid', $espid);
            $pacientes4 = $pacientes4->where('a.espid', $espid);

        } 

        if($nombres!=null)
        {
            $nombres2 = explode(" ", $nombres); 
            $cantidad = count($nombres2);
            
            foreach($nombres2 as $n){
                $nombres_sql = $nombres_sql.'%'.$n;
            }

            $nombres_sql= $nombres_sql.'%';
            
            if($cantidad > '1'){       
                    $pacientes1 = $pacientes1->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
                    $pacientes2 = $pacientes2->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
                    $pacientes2_0 = $pacientes2_0->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
                    $pacientes3 = $pacientes3->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
                    $pacientes4 = $pacientes4->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);    
                    });
                    
                    
            }
            else{
                $pacientes1 = $pacientes1->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                }); 
                $pacientes2 = $pacientes2->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                }); 
                $pacientes2_0 = $pacientes2_0->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                }); 
                $pacientes3 = $pacientes3->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                }); 
                $pacientes4 = $pacientes4->where(function($jq1) use($nombres_sql){
                        $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                            ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);    
                }); 

            }  

        
        }                         
        
        if(Auth::user()->id_tipo_usuario == '1' ){                  
            //dd($nombres ,$pacientes1->get(), $pacientes2->get(), $pacientes2_0->get() , $pacientes3->get() , $pacientes4->get());
        } 
        $pacientes1 = $pacientes1->union($pacientes2)->union($pacientes2_0)->union($pacientes3)->union($pacientes4);          

        $pacientes1 = $pacientes1->limit('300')->get();
        $pacientes = $pacientes1;
      
        $agendas_proc=null;
        foreach($pacientes as $pac){

            if($pac->proc_consul=='1'){
                $pentax = Pentax::where('id_agenda',$pac->id_agenda)->first();
                if(!is_null($pentax)){
                    $txt_px='';
                    foreach ($pentax->procedimientos as $p) {
                        if($txt_px==''){
                            $txt_px=$p->procedimiento->nombre;
                        }else{
                            $txt_px = $txt_px.'+'.$p->procedimiento->nombre;
                        }
                        
                    }
                 
                    $agendas_proc[$pac->id_agenda] = [$txt_px];        
                }
                  
            }

        }


        Cookie::queue('ruta','historia','1000');

        return view('hc_admision/fullcontrol/index', ['nombres' => $nombres, 'agendas_pac' => $pacientes, 'fecha' => $fecha, 'fecha_hasta' => $fecha_hasta, 'agendas_proc' => $agendas_proc, 'proc_consul' => $proc_consul, 'especialidades' => $especialidades, 'id_especialidad' => '0', 'doctores' => $doctores, 'id_doctor1' => $id_doctor1, 'seguros' => $seguros, 'id_seguro' => $id_seguro]);
    }
}
    
