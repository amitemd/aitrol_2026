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
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Log_usuario;
use Sis_medico\hc_receta;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_protocolo;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_child_pugh;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\procedimiento_completo;
use Excel;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Response;



class ConsultaController extends Controller
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
        if(in_array($rolUsuario, array(1, 3, 6, 11, 7,5,20)) == false){
          return true;
        }
    }

    private function rol_dr(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(3)) == false){
          return true;
        }
    }

    public function actualizar(Request $request)
    {   
        //return $request->all();
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id= $request['hcid'];
         
        $input1 = [
            'presion' => $request["presion"],
            'id_seguro' => $request["id_seguro"],                                        
            'pulso' => $request["pulso"],
            'temperatura' => $request["temperatura"],                                       
            'o2' => $request["o2"],                                      
            'altura' => $request["estatura"],
            'peso' => $request["peso"],                                       
            'perimetro' => $request["perimetro"],
            'examenes_realizar' => $request["examenes_realizar"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];
        Historiaclinica::where('hcid', $id)
        ->update($input1);
        $input_evo = [
            'motivo' => $request["motivo"], 
            'cuadro_clinico' => $request["historia_clinica"], 
            'resultado' => $request["resultado_ev"],                 
            'fecha_doctor' => $request["fecha_doctor"],
            'indicaciones' => $request["indicaciones"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];

        $id_evolucion = $request['id_evolucion'];
        //return $input_evo;
        Hc_Evolucion::where('id', $id_evolucion)
        ->update($input_evo);  


        $input_child= [
            'ascitis' => $request["ascitis"], 
            'encefalopatia' => $request["encefalopatia"],                  
            'albumina' => $request["albumina"],
            'bilirrubina' => $request["bilirrubina"],                
            'inr' => $request["inr"],
            'examen_fisico' => $request["examen_fisico"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];

        $id_child = $request['id_child_pugh'];
        //return $input_evo;
        hc_child_pugh::where('id', $id_child)
        ->update($input_child);  

        
        $input_hc_procedimiento= [
            'id_doctor_examinador' => $request["id_doctor_examinador"], 
            'id_seguro' => $request["id_seguro"], 
            'id_empresa' => $request["id_empresa"],     
            'observaciones' => $request["observaciones"],  
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];

        $id_hc_procedimiento = $request['id_hc_procedimiento'];
        //return $input_evo;
        hc_procedimientos::where('id', $id_hc_procedimiento)
        ->update($input_hc_procedimiento);  
        return "ok";   
    }

    public function actualiza_historia(Request $request)
    {
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        
        
        $fecha_ingreso = Date('Y-m-d h:i:s');
          
        $id_hc_procedimientos = $request['id_hc_procedimientos2'];
        $input1 = [
            'cuadro_clinico' => $request['historia_clinica'],
            'fecha_ingreso' => $fecha_ingreso,

            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
        ];
        
        $evolucion = Hc_Evolucion::where('hc_id_procedimiento',$id_hc_procedimientos)->first();
        $evolucion->update($input1); 
        return "ok";   
    }

    public function crea_indicacion(Request $request)
    {
        
        $indicacion = $request['indicacion'];
        $hcid = $request['hcid'];
        $id_evolucion = $request['id_evolucion'];

        $rules = [
            'indicacion' => 'required',
        ];

        $msn = [
            'indicacion.required' => 'Ingrese una evolución',
        ];

        $this->validate($request,$rules,$msn);
        
        
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $historia = Historiaclinica::find($hcid);
        $fecha_ingreso = Date('Y-m-d h:i:s');
        
        
        $evolucion = Hc_Evolucion::find($id_evolucion);
        if(!is_null($evolucion)){

            $indicaciones = Hc_Evolucion_Indicacion::where('id_evolucion',$id_evolucion)->get();
            $contador = $indicaciones->count();
            
            $input2 = [
                'id_evolucion' => $id_evolucion,
                'secuencia' => $contador + 1,
                'descripcion' => $indicacion,
                
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario 
            ];

            Hc_Evolucion_Indicacion::create($input2);

            $indicaciones2 = Hc_Evolucion_Indicacion::where('id_evolucion',$id_evolucion)->get();


        }
        

        return view('hc_admision/evolucion/indicacion',['indicaciones' => $indicaciones2 ]);
        //return view('hc_admision/evolucion/evolucion',['paciente' => $paciente, 'seguro' => $seguro, 'hcid' => $hcid, 'evolucion_0' => $evolucion_0, 'indicaciones' => $indicaciones ]);    
    }


       

    public function mostrar($id, $id_evolucion)
    {
        
        $hc_procedimiento = Hc_procedimientos::find($id);
        $historia = Historiaclinica::find($hc_procedimiento->id_hc);
        $paciente = Paciente::find($historia->id_paciente);
        $seguro = Seguro::find($historia->id_seguro);

        $evoluciones = Hc_Evolucion::where('hc_id_procedimiento',$id)->get();
        

        $evolucion = null;
        $indicaciones = null;
        if($id_evolucion!=null){
            $evolucion = Hc_Evolucion::find($id_evolucion);
            $indicaciones = Hc_Evolucion_Indicacion::where('id_evolucion',$id_evolucion)->get();
        }
        
        return view('hc_admision/evolucion/evolucion',['paciente' => $paciente, 'seguro' => $seguro, 'hcid' => $historia->hcid, 'evoluciones' => $evoluciones, 'indicaciones' => $indicaciones, 'evolucion' => $evolucion, 'id' => $id ]);
    }

    public function indicaciones(Request $request)
    {
        $id_evolucion = $request['id_evolucion'];

        if($id_evolucion!=null){

            $indicaciones2 = Hc_Evolucion_Indicacion::where('id_evolucion',$id_evolucion)->get();
        

            return view('hc_admision/evolucion/indicacion',['indicaciones' => $indicaciones2 ]);
            
        }else{

            return "no";

        }     
    }

   
    public function evolucion($id)
    {
        
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }


        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado','1')->get(); //3=DOCTORES;
        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado','1')->get(); //6=ENFERMEROS;
        $anestesiologos = DB::table('users')->where('id_tipo_usuario', '=', 9)->where('estado','1')->get(); //9=ANESTESIOLOGO;
        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();

        $hc_procedimiento=Hc_procedimientos::find($id);
        $historia = Historiaclinica::find($hc_procedimiento->id_hc);
            
        
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $historia->id_agenda)
            ->first();



        $paciente = Paciente::find($historia->id_paciente);
       
        $seguro =  Seguro::find($historia->id_seguro);

        $evoluciones = Hc_Evolucion::where('hc_id_procedimiento',$id)->get();

        $procedimientos_completo = procedimiento_completo::all();
       

        return view('hc_admision/evolucion/evoluciones', ['agenda' => $agenda, 'paciente' => $paciente,'hca' => $historia, 'seguro' => $seguro, 'evoluciones' => $evoluciones, 'id' => $id, 'procedimientos_completo' => $procedimientos_completo, 'hc_procedimiento' => $hc_procedimiento]);  
    }


    public function show($id)
    {
        //
    }

   

   

      public function imprimir($id)
    {
    
        $evolucion = Hc_Evolucion::where('hc_id_procedimiento',$id)->orderBy('secuencia')->get();

        $indicaciones=[];
        foreach($evolucion as $value){
            $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion',$value->id)->get();

        }


        $procedimiento = Hc_procedimientos::find($id);

        //dd($indicaciones);

        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid',$procedimiento->id_hc)->join('paciente','paciente.id','historiaclinica.id_paciente')->join('seguros','seguros.id','historiaclinica.id_seguro')->join('users','users.id','paciente.id_usuario')->join('agenda','agenda.id','historiaclinica.id_agenda')->leftjoin('subseguro','subseguro.id','historiaclinica.id_subseguro')->select('historiaclinica.*','paciente.nombre1','paciente.nombre2','paciente.apellido1','paciente.apellido2','seguros.nombre','agenda.fechaini','paciente.fecha_nacimiento','agenda.proc_consul','agenda.id_procedimiento', 'seguros.tipo','subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2','paciente.id_usuario', 'paciente.cedulafamiliar','paciente.sexo' )->first();



        $data = $historiaclinica;
        $view =  \View::make('hc_admision.formato.evolucion', compact('data','evolucion','procedimiento','indicaciones'))->render();
        $pdf = \App::make('dompdf.wrapper');

        
        $pdf->loadHTML($view)/*->setPaper($paper_size, 'portrait')*/;
        
        
       
        

       return $pdf->download('evolucion-'.$historiaclinica->id_paciente.'-'.$historiaclinica->hcid.'.pdf');
    }

    public function consulta_sig_ant($id, $agenda_hoy)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol_dr()){
            return response()->view('errors.404');
        }

        $protocolo = DB::table('hc_protocolo as p')->join('historiaclinica as h','h.hcid','p.hcid')->join('hc_procedimientos as pc','pc.id','p.id_hc_procedimientos')->join('hc_receta as r','r.id_hc','h.hcid')->select('p.*','p.id as protocolo','h.id_paciente','h.hcid','h.id_agenda','pc.id_procedimiento_completo', 'r.prescripcion','r.rp')->where('p.id',$id)->first();
        //dd($protocolo);

        $evolucion = Hc_Evolucion::where('hc_id_procedimiento',$protocolo->id_hc_procedimientos)->first();


        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc','uc.id','agenda.id_usuariocrea')
            ->join('users as um','um.id','agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h','h.id_agenda','agenda.id')
            ->leftjoin('sala','agenda.id_sala','sala.id')
            ->leftjoin('hospital','sala.id_hospital','hospital.id')
            ->leftjoin('users as ud','ud.id','agenda.id_doctor1')
            ->leftjoin('especialidad','especialidad.id','agenda.espid')
            ->leftjoin('empresa','empresa.id','agenda.id_empresa')
            ->leftjoin('procedimiento','procedimiento.id','agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre','paciente.fecha_nacimiento','paciente.ocupacion','h.parentesco as hparentesco','paciente.parentesco as pparentesco','paciente.estadocivil','paciente.ciudad','paciente.lugar_nacimiento','paciente.direccion','paciente.telefono1','paciente.telefono2','seguros.nombre as snombre','sala.nombre_sala as slnombre','hospital.nombre_hospital as hsnombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido','especialidad.nombre as esnombre','procedimiento.nombre as pnombre','empresa.nombrecomercial','paciente.sexo','paciente.gruposanguineo','paciente.transfusion','paciente.alergias','paciente.vacuna','paciente.historia_clinica','paciente.antecedentes_pat','paciente.antecedentes_fam','paciente.antecedentes_quir','h.hcid')
            ->where('agenda.id', '=', $protocolo->id_agenda)
            ->first();


        $protocolo_des = DB::table('hc_protocolo as p')->join('historiaclinica as h','h.hcid','p.hcid')->select('p.*','h.id_paciente','h.hcid','h.id_agenda')->where('h.id_paciente',$protocolo->id_paciente)->where('p.created_at','>',$protocolo->created_at)->orderBy('p.created_at','asc')->first();

        $protocolo_ant = DB::table('hc_protocolo as p')->join('historiaclinica as h','h.hcid','p.hcid')->select('p.*','h.id_paciente','h.hcid','h.id_agenda')->where('h.id_paciente',$protocolo->id_paciente)->where('p.created_at','<',$protocolo->created_at)->orderBy('p.created_at','desc')->first();

        $protocolos = DB::table('hc_protocolo as p')->join('historiaclinica as h','h.hcid','p.hcid')->select('p.*','h.id_paciente','h.hcid','h.id_agenda')->where('h.id_paciente',$protocolo->id_paciente)->count();

        //dd($protocolo_ant,$protocolo->protocolo,$protocolo_des,$protocolo->created_at);


        $fecha_dia = date('Y-m-d',strtotime($agenda->fechaini));
        
        $nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha_dia ) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );

        $cant_cortesias =Agenda::where('id_doctor1',$agenda->id_doctor1)->where('fechaini','>',$fecha_dia)->where('fechaini','<',$nuevafecha)->where('cortesia','SI')->count();

        $proc_completo = procedimiento_completo::orderBy('nombre_general')->get();

        $no_admin = false;

        $evoluciones = DB::table('hc_evolucion as e')->join('historiaclinica as h','e.hcid','h.hcid')->where('h.id_paciente',$protocolo->id_paciente)->join('agenda as a','a.id','h.id_agenda')->where('e.created_at','<',$evolucion->created_at)->orderBy('e.created_at','desc')->select('e.*','a.fechaini')->get();
        
         //dd($evoluciones);
        
        return view('agenda/detalle_consulta', ['agenda' => $agenda, 'cant_cortesias' => $cant_cortesias, 'protocolo' => $protocolo, 'no_admin' => $no_admin, 'proc_completo' => $proc_completo, 'protocolo_des' => $protocolo_des, 'protocolo_ant' => $protocolo_ant, 'protocolos' => $protocolos, 'agenda_hoy' => $agenda_hoy, 'evolucion' => $evolucion, 'evoluciones' => $evoluciones]);        
    }

    public function visitas($id_paciente, $id_agenda)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol_dr()){
            return response()->view('errors.404');
        }

        $protocolo = DB::table('hc_protocolo as p')->join('historiaclinica as h','h.hcid','p.hcid')->join('hc_procedimientos as pc','pc.id','p.id_hc_procedimientos')->join('agenda as a','a.id','h.id_agenda')->join('hc_receta as r','r.id_hc','h.hcid')->select('p.*','p.id as protocolo','h.*','pc.id_procedimiento_completo', 'a.fechaini', 'r.*')->where('h.id_paciente',$id_paciente)->where('pc.id_procedimiento_completo','=',40)->get();


        //dd($protocolo);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc','uc.id','agenda.id_usuariocrea')
            ->join('users as um','um.id','agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h','h.id_agenda','agenda.id')
            ->leftjoin('sala','agenda.id_sala','sala.id')
            ->leftjoin('hospital','sala.id_hospital','hospital.id')
            ->leftjoin('users as ud','ud.id','agenda.id_doctor1')
            ->leftjoin('especialidad','especialidad.id','agenda.espid')
            ->leftjoin('empresa','empresa.id','agenda.id_empresa')
            ->leftjoin('procedimiento','procedimiento.id','agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre','paciente.fecha_nacimiento','paciente.ocupacion','h.parentesco as hparentesco','paciente.parentesco as pparentesco','paciente.estadocivil','paciente.ciudad','paciente.lugar_nacimiento','paciente.direccion','paciente.telefono1','paciente.telefono2','seguros.nombre as snombre','sala.nombre_sala as slnombre','hospital.nombre_hospital as hsnombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido','especialidad.nombre as esnombre','procedimiento.nombre as pnombre','empresa.nombrecomercial','paciente.sexo','paciente.gruposanguineo','paciente.transfusion','paciente.alergias','paciente.vacuna','paciente.historia_clinica','paciente.antecedentes_pat','paciente.antecedentes_fam','paciente.antecedentes_quir','h.hcid')
            ->where('agenda.id', '=', $id_agenda)
            ->first();

            $fecha_dia = date('Y-m-d',strtotime($agenda->fechaini));
            $nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha_dia ) ) ;
            $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
            $cant_cortesias =Agenda::where('id_doctor1',$agenda->id_doctor1)->where('fechaini','>',$fecha_dia)->where('fechaini','<',$nuevafecha)->where('cortesia','SI')->count();


            //return "hola";
            return view('hc_admision/visita/visita', ['agenda' => $agenda, 'fecha_dia' => $fecha_dia, 'protocolo' => $protocolo,'cant_cortesias' => $cant_cortesias]);
    }
 
    public function ingreso_actualiza_visita($id,$id_agenda)
    {
        //dd($id);
        $examen_fisico = null;
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }
        $idusuario = Auth::user()->id;
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        //$hc_evolucion = hc_receta::where('id_hc', '12686')->first(); 
        //dd($hc_evolucion);

        /*$protocolo = hc_protocolo::find($id_protocolo);
        $paciente = Paciente::find($protocolo->historiaclinica->id_paciente);
        $agenda = Agenda::find($protocolo->historiaclinica->id_agenda);
        $receta = hc_receta::where('id_hc', $protocolo->historiaclinica->hcid)->first();

        return view('hc_admision/visita/visita_crea_actualiza', ['paciente' => $paciente, 'protocolo' => $protocolo, 'agenda' => $agenda, 'receta' => $receta]);*/

        $evolucion = DB::table('hc_evolucion as e')->where('e.id',$id)->leftjoin('hc_receta as r','r.id_hc','e.hcid')->select('e.*','r.rp')->first(); 
        //dd($evolucion);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc','uc.id','agenda.id_usuariocrea')
            ->join('users as um','um.id','agenda.id_usuariomod')
            ->join('historiaclinica as h','h.id_agenda','agenda.id')
            ->leftjoin('seguros as hs','hs.id','h.id_seguro')
            ->leftjoin('sala','agenda.id_sala','sala.id')
            ->leftjoin('hospital','sala.id_hospital','hospital.id')
            ->leftjoin('users as ud','ud.id','agenda.id_doctor1')
            ->leftjoin('especialidad','especialidad.id','agenda.espid')
            ->leftjoin('empresa','empresa.id','agenda.id_empresa')
            ->leftjoin('procedimiento','procedimiento.id','agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre','paciente.fecha_nacimiento','paciente.ocupacion','h.parentesco as hparentesco','paciente.parentesco as pparentesco','paciente.estadocivil','paciente.ciudad','paciente.lugar_nacimiento','paciente.direccion','paciente.telefono1','paciente.telefono2','seguros.nombre as snombre','sala.nombre_sala as slnombre','hospital.nombre_hospital as hsnombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido','especialidad.nombre as esnombre','procedimiento.nombre as pnombre','empresa.nombrecomercial','paciente.sexo','paciente.gruposanguineo','paciente.transfusion','paciente.alergias','paciente.vacuna','paciente.historia_clinica','paciente.antecedentes_pat','paciente.antecedentes_fam','paciente.antecedentes_quir','h.hcid','paciente.referido','paciente.id_usuario','paciente.trabajo','paciente.observacion','paciente.alcohol','hs.nombre as hsnombre','h.presion','h.pulso','h.temperatura','h.o2','h.altura','h.peso','h.perimetro','h.examenes_realizar', 'h.id_seguro')
            ->where('h.hcid', '=', $evolucion->hcid)
            ->first();
           //dd($agenda->esnombre);

        $fecha_dia = date('Y-m-d',strtotime($agenda->fechaini));
        
        $nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha_dia ) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );

        $cant_cortesias =Agenda::where('id_doctor1',$agenda->id_doctor1)->where('fechaini','>',$fecha_dia)->where('fechaini','<',$nuevafecha)->where('cortesia','SI')->count();

        $hc_receta = hc_receta::where('id_hc',$agenda->hcid)->first();
        if(is_null($hc_receta)){
            $input_hc_receta = [
                'id_hc' => $agenda->hcid,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]; 
            hc_receta::insert($input_hc_receta);     
        }

        $historia = historiaclinica::find($agenda->hcid);
        //dd($agenda->estado_cita);
        //return $historia;
        $hc_receta = hc_receta::where('id_hc',$agenda->hcid)->first();
        $alergiasxpac = Paciente_Alergia::where('id_paciente',$agenda->id_paciente)->get();
        $child_pugh = null;
        $child_pugh = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
        //dd($child_pugh);
        //dd($evolucion);
        //Nuevo 

        if($agenda->espid=='4'){

            $examen_fisico = 'ESTADO CABEZA Y CUELLO:
ESTADO TORAX:
ESTADO ABDOMEN:
ESTADO MIEMBROS SUPERIORES:
ESTADO MIEMBROS INFERIORES:     
OTROS:
';
            if(!is_null($historia->seguro)){
                if($historia->seguro->tipo=='0'){
                    $examen_fisico ='REVISION ACTUAL ORGANOS Y SISTEMAS
PIEL: TEXTURA NORMAL, HIDRATADA, SIN LESIONES
CABEZA: NORMOCEFALO, CABELLO BIEN IMPLANTADO MAREO
CARA: SIN ALTERACIONES
CUELLO: MOVIL, CENTRAL, SIN ADENOPATIAS
TORAX: SIMETRICO, SIN LESIONES
CSPS: VENTILADOS.
RSCS: RITMICOS

ABDOMEN:BANDO DEPRESIBLE SIN DOLOR A LA PALPACION SUPERFICIAL Y PROFUNDA.
RSHS(+), PUNTOS URETERALES(-),
COLUMNA VERTEBRAL: CENTRAL, SIN DESVIACION, NI LESIONES, PUÑOPERCUSION:(-) 
EXTREMIDADES: SIN ALTERACIONES, SIMETRICOS

EXAMEN FISICO REGIONAL
CABEZA: NORMOCEFALO, CABELLO BIEN IMPLANTADO, CEFALEA
EXTREMIDADES: SIN LESIONES NI ALTERACIONES';
                }
            }
        }

                if(is_null($child_pugh)){
                    if($agenda->estado_cita=='4'){
                        $input_child_pugh = [
                            'id_hc_evolucion' => $evolucion->id,
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod' => $idusuario,                    
                            'id_usuariocrea' => $idusuario,
                            'examen_fisico' => $examen_fisico,
                            'ip_creacion' => $ip_cliente,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]; 
                        hc_child_pugh::insert($input_child_pugh);
                        
                        $child_pugh = hc_child_pugh::where('id_hc_evolucion', $evolucion->id)->first();
                    }
                }
        $hc_procedimiento = null;
        $hc_procedimiento =  hc_procedimientos::find($evolucion->hc_id_procedimiento);
        //return $agenda->id_seguro;
        if($hc_procedimiento->id_seguro == null){
            $input_procedimiento = [
                'id_seguro' => $agenda->id_seguro,
            ]; 
            hc_procedimientos::where('id_hc', $agenda->hcid)->update($input_procedimiento);
            $hc_procedimiento =  hc_procedimientos::where('id_hc', $agenda->hcid)->first();
        }
        if($hc_procedimiento->id_doctor_examinador == null){
            $input_procedimiento = [
                'id_doctor_examinador' => $historia->id_doctor1,
            ];        
            hc_procedimientos::where('id_hc', $agenda->hcid)->update($input_procedimiento);
            $hc_procedimiento =  hc_procedimientos::where('id_hc', $agenda->hcid)->first();
        }
        $seguros = Seguro::where('inactivo', '1')->get();
        $doctores = User::where('id_tipo_usuario', '3')->where('estado', '1')->get();
        //dd($doctores);

        $examenes_externos = Paciente_Biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', 1)->get();
        $biopsias_1 = Paciente_biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', '0')->get();
          //$biopsias = hc_imagenes_protocolo::where('id_hc_protocolo',$protocolo->id)->where('estado', '4')->get();
        $biopsias_2 = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
                                        ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
                                        ->where('id_paciente', $agenda->id_paciente)
                                        ->where('hc_imagenes_protocolo.estado', '4')->get();
        //return $examenes_externos;
        //dd($child_pugh);

         //dd($hc_receta);                               
        return view('hc_admision/visita/visita_crea_actualiza', ['evolucion' => $evolucion, 'agenda' => $agenda, 'cant_cortesias' => $cant_cortesias, 'id_agenda' => $id_agenda, 'hc_receta' => $hc_receta, 'alergiasxpac' => $alergiasxpac, 'child_pugh' => $child_pugh, 'hc_procedimiento' => $hc_procedimiento, 'seguros' => $seguros, 'doctores' => $doctores, 'laboratorio_externo' => $examenes_externos, 'biopsias_1' => $biopsias_1, 'biopsias_2' => $biopsias_2]);  
    }

    public function crear_evolucion_procedimiento($id_agenda, $hc_id_procedimiento){

        $idusuario = Auth::user()->id;
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $historia = Historiaclinica::where('id_agenda', $id_agenda)->get();
        $evolucion = hc_evolucion::where('hc_id_procedimiento', $hc_id_procedimiento)->orderByRaw('created_at DESC')->first(); 
        
        $procedimientos_2 = hc_procedimientos::where('id_hc', $historia[0]->hcid)->get();
        //dd($procedimientos_2);
        $agenda = Agenda::find($id_agenda);
        $edad = Carbon::createFromDate(substr($agenda->paciente->fecha_nacimiento, 0, 4), substr($agenda->paciente->fecha_nacimiento, 5, 2), substr($agenda->paciente->fecha_nacimiento, 8, 2))->age;
        $alergias = Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();
        if($alergias == "[]"){
            $alergia = "No";
        }else{
            $alergia  = "";
            foreach ($alergias as $value) {
                if($alergia == ""){
                    $alergia = $value->principio_activo->nombre;
                }else{
                    $alergia = $alergia.", ".$value->principio_activo->nombre;
                }
                
            }
        }
        if($agenda->paciente->sexo == 1){
            $sexo = "MASCULINO";
        }else{
            $sexo = "FEMENINO";
        }

        $procedimientos =  hc_procedimientos::find($hc_id_procedimiento);
        if($evolucion != ""){
            $secuencia  = $evolucion->secuencia+1;
            $cuadro_clinico = "";
        }else{
            $secuencia = 0;
            $nombre_procedimiento = "";
            //return $procedimientos;
            if($procedimientos->id_procedimiento != null){
                $nombre_procedimiento = $procedimientos->procedimiento_completo->nombre_completo;
            }
            $cuadro_clinico ="<p>PACIENTE ".$sexo." DE ".$edad." AÑOS DE EDAD ACUDE CON ORDEN DEL ".$agenda->seguro->nombre." PARA LA REALIZACION DE ".$nombre_procedimiento."<br> APP: ".$agenda->paciente->antecedentes_pat." <br> APF: ".$agenda->paciente->antecedentes_fam."<br> APQX: ".$agenda->paciente->antecedentes_quir."<br> ALERGIAS: ".$alergia."<br></p>";
        }

        foreach ($procedimientos_2 as $value) {
            $input = [
                'hc_id_procedimiento' => $value->id,
                'hcid' => $historia[0]->hcid,
                'secuencia' => $secuencia,
                'cuadro_clinico' => $cuadro_clinico,
                'fecha_ingreso' => ' ',
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
            ];                 

            $id_evolucion = Hc_Evolucion::insertGetId($input);
        }

        return redirect()->route('visita.crea_actualiza_funcion', ['id_protocolo' => $id_evolucion, 'agenda' => $id_agenda]);
    }

    public function actualiza_paciente(Request $request){
        $idusuario = Auth::user()->id;
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $id= $request->id_paciente;
        $input1 = [
            'vacuna' => $request["vacuna"],                                       
            'alergias' => $request["alergias"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];
        paciente::where('id', $id)
        ->update($input1);
        return "procesado"; 
    }
    
    public function actualizar_visita(Request $request){
        $idusuario = Auth::user()->id;
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $id= $request->id_historia;
        $input1 = [
            'presion' => $request["presion"],                                       
            'pulso' => $request["pulso"],
            'temperatura' => $request["temperatura"],                                       
            'o2' => $request["o2"],                                      
            'altura' => $request["estatura"],
            'peso' => $request["peso"],                                       
            'perimetro' => $request["perimetro"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];
        Historiaclinica::where('hcid', $id)
        ->update($input1);
        $id2= $request->id_protocolo;
        $input2 = [
            'motivo' => $request["motivo"],                                       
            'hallazgos' => $request["hallazgos"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];
        hc_protocolo::where('id', $id2)
        ->update($input2);
    }
    public function actualizar_visita2(Request $request){
        $idusuario = Auth::user()->id;
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $id= $request->id_historia;
        $input1 = [
            'observaciones' => $request["observaciones"],                                       
            'examenes_realizar' => $request["examenes_realizar"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario 
        ];
        Historiaclinica::where('hcid', $id)
        ->update($input1);
    }

    public function regresar(){
        return back();

    }

    public function cargar_empresa2($id_proc, $id_agenda, $id_seguro){
        $agenda = Agenda::find($id_agenda);
        $seguro = Seguro::find($id_seguro);
        if($seguro->tipo==0){
            $empresas = DB::table('convenio as c')->join('empresa as e','e.id','c.id_empresa')->select('e.*')->where('c.id_seguro',$seguro->id)->get();
        }else{
            $empresas = DB::table('empresa as e')->where('e.estado','1')->where('e.id','<>','9999999999')->get();
        }
        $procedimiento = hc_procedimientos::find($id_proc);
        $id_empresa = $procedimiento->id_empresa;
        
        if($procedimiento->id_empresa==null){
            $procedimiento->update(['id_empresa' => $agenda->id_empresa]);
            $id_empresa = $agenda->id_empresa;
        }
        

        return view('hc_admision.empresas2',['empresas'=>$empresas, 'id_empresa' => $id_empresa]);
    }
}
    