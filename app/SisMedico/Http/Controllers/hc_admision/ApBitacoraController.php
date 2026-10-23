<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Illuminate\Http\Request;
use Sis_medico\Nivel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Sis_medico\ApBitacora;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use Sis_medico\ApPacienteBitacora;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\Biopsias;
use Sis_medico\Empresa;
use Sis_medico\Hc4_Auditoria_Biopsias;
use Sis_medico\Hc4_Biopsias;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_procedimientos;
use Sis_medico\HcFormulario013;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Paciente;
use Sis_medico\procedimiento_completo;

class ApBitacoraController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 10, 11, 12)) == false && $id_auth!='1307189140'){
          return true;
        }
        

    }


    public function index(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $bitacoras = DB::table('ap_bitacora as p')
            ->select('p.*')
            ->where('p.estado','1')
            ->orderBy('id')
            ->paginate(10);
        
        return view('hc_admision/ap_bitacora/index',['bitacoras'=>$bitacoras]);
    }

    public function search(Request $request){
        
        if($this->rol()){
            return response()->view('errors.404');
        }    
        
        $valor = $request->get('nombre');  
       
        $bitacoras = DB::table('ap_bitacora as p')
        ->select('p.*')
        ->where('p.estado','1')
        ->where('p.descripcion','LIKE','%'.$valor.'%')
        ->orderBy('id')
        ->paginate(10);
        
        return view('hc_admision/ap_bitacora/index',['bitacoras'=>$bitacoras]);
    }



    public function create(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        return view('hc_admision/ap_bitacora/create');
    }
    public function store(Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $documento = $request->get('documento');
        $descripcion = $request->get('descripcion');
     
        
        $array=[
            'documento'=>$documento,
            'descripcion'=>$descripcion,
            'tipo'=>0,
            'estado'=>1, 
            'id_usuariocrea'=>$idusuario, 
            'id_usuariomod'=>$idusuario, 
            'ip_creacion'=>$ip_cliente, 
            'ip_modificacion'=>$ip_cliente, 
        ];
        
        ApBitacora::create($array);

        return ['respuesta'=>'ok'];
        
    }
    public function edit($id){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $datos= ApBitacora::where('id',$id)->first();
        
        return view('hc_admision/ap_bitacora/edit',['datos'=>$datos]);
    }
    public function update(Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $id = $request->get('id');
        $documento = $request->get('documento');
        $descripcion = $request->get('descripcion');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        //dd($id, $documento , $descripcion, $tipo);

        $consulta_update =[
            'documento' => $documento, 
            'descripcion' => $descripcion, 
            'id_usuariomod'=>$idusuario, 
            'ip_modificacion'=>$ip_cliente,  
        ];

        ApBitacora::where('id',$id)->update($consulta_update);
        
        //return redirect('mantenimiento_bitacoras')->with('status','2');
        return ['respuesta'=>"ok"];
    }
    public function delete(Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $id = $request->get('id');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        ApBitacora::where('id',$id)
        ->update([
            'estado'=>0,
            'id_usuariomod'=>$idusuario, 
            'ip_modificacion'=>$ip_cliente, 
        ]);
        return ['respuesta'=>"ok"];
    }    
    
    public function index_apPaciente(){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $bitacoras = ApPacienteBitacora::where('estado','1')
        ->orderBy('id')
        ->paginate(10);
    
        return view('hc_admision/ap_bitacora_paciente/index',['bitacoras'=>$bitacoras]);
    }




    public function ap_paciente_consulta($id_hc_procedimiento){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $consultaBitacora =ApBitacora::where('estado','1')->get();
        $consultaHc = hc_procedimientos::find($id_hc_procedimiento);
        
        if(!is_null($consultaHc)){   
            foreach ($consultaBitacora as  $value) {
                $consulta = ApPacienteBitacora::where('id_hc_procedimiento',$id_hc_procedimiento)
                ->where('id_ap_bitacora', $value->id)
                ->first(); 
                if(is_null($consulta)){
                    $array=[
                        'id_hc_procedimiento'=>$id_hc_procedimiento,
                        'id_ap_bitacora'=>$value->id,
                        'documento'=>$value->documento, 
                        'descripcion'=>$value->descripcion,
                        'estado'=>1, 
                        'id_usuariocrea'=>$idusuario, 
                        'id_usuariomod'=>$idusuario, 
                        'ip_creacion'=>$ip_cliente, 
                        'ip_modificacion'=>$ip_cliente, 
                    ];
                    ApPacienteBitacora::create($array);
                }    
        }
        $pacienteBitacora = ApPacienteBitacora::where('id_hc_procedimiento',$id_hc_procedimiento)
        ->get();
        $itemVacio='';
        return view('hc_admision/ap_bitacora_paciente/index',['pacienteBitacora'=>$pacienteBitacora, 'consulta_hc'=>$consultaHc, 'contador'=>$contador=1, 'itemVacio'=>$itemVacio]);
    }else{
        return "No existe el procedimiento";
        }
    }




    public function cambiarTipo(Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $valor = $request->get('valor');
        $id = $request->get('id');
        ApPacienteBitacora::where('id',$id)
        ->update(['si_no'=>$valor]);        
        return ["respuesta"=>'ok'];
    }
    public function cambiarObservacion(Request $request){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $campo = $request->get('campo');
        $id = $request->get('id');
        ApPacienteBitacora::where('id',$id)
        ->update(['observacion'=>$campo]);        
        return ["respuesta"=>'ok'];
    }
    
    
    public function seguro_policia_nacional($id) {
        $fecha_actual =Carbon::now();
        $datos = ApPacienteBitacora::where('estado', '1')
        ->where('id_hc_procedimiento',$id)
        ->first();
        $campos =ApPacienteBitacora::where('estado','1')
        ->where('id_hc_procedimiento',$id)
        ->get();
        
        $id_usuario = Auth::user()->id;

        $usuarioF = User::find($id_usuario);
        $usuario = (substr($usuarioF->nombre1, 0,1).".".$usuarioF->apellido1);
        
        $edad = Carbon::parse($datos->hc_procedimientos->historia->paciente->fecha_nacimiento)->age;
        
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $f_atencion=$datos->hc_procedimientos->historia->fecha_atencion;
        $fecha = Carbon::parse($f_atencion);
        $mes = $meses[($fecha->format('n')) - 1];
        $fechaAtencion = $fecha->format('d') . ' de ' . $mes . ' de ' . $fecha->format('Y');
        $itemVacio='';
        $vistaurl = 'hc_admision/ap_bitacora_paciente/pdf_seguro_policia';
        $contador=0;
        
        $view     = \View::make($vistaurl, compact('contador','itemVacio','datos', 'campos', 'edad', 'mes', 'fechaAtencion','usuario','fecha_actual'))->render();
    
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portraint');
        return $pdf->stream('procedimiento revision');
   
    }    
    

    public function formulario_013_edit($id){
        if($this->rol()){
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $formulario013 = HcFormulario013::where('id_hc_procedimiento',$id)->first();
        $paciente = null;
        $texto = "";
        $protocolo = null;
        if(is_null($formulario013)){
            $id_empresa = Session::get('id_empresa');
            $procedimiento = hc_procedimientos::find($id);
            if(!is_null($procedimiento)){
                $historiaClinica = $procedimiento->historia;
                if(!is_null($historiaClinica)){                    
                    $paciente = $historiaClinica->paciente;
                    $evoluciones = $historiaClinica->evoluciones;
                    $evolucion_0 = "";
                    $resumen_clinico = "";
                    $fecha_toma = null;
                    foreach ($evoluciones as $value) {
                        if($value->secuencia == 0){
                            $biopsia = Hc4_Biopsias::where('hc_id_procedimiento',$id)->first();
                            if(!is_null($biopsia)){
                                $evolucion_0 = 'Se toma biopsia de ' . $biopsia->descripcion_frasco;
                                $fecha_toma = date_format(new DateTime($biopsia->created_at), 'Y-m-d');
                            }
                            $resumen_clinico = $value->cuadro_clinico;
                        }
                    }
                    $tratamiento = "";
                    $procedimiento_final = Hc_Procedimiento_Final::where('id_hc_procedimientos',$id)->get();
                    foreach ($procedimiento_final as $value) {
                        if(!is_null($value->procedimiento)){
                            if($tratamiento == ""){
                                $tratamiento = $value->procedimiento->nombre;
                            }
                            else{
                                $tratamiento = $tratamiento. ' + ' . $value->procedimiento->nombre;
                            }
                        }
                    }
                    
                    $arr_formulario013 = [
                        'id_hc_procedimiento' => $id,
                        'hcid' => $historiaClinica->hcid,
                        'id_paciente' => $paciente->id,
                        'id_empresa' => $id_empresa,
                        'resumen_clinico' => $resumen_clinico,
                        'estudio_solicitado' => 0,
                        'descripcion_estudio' => $evolucion_0,
                        'prioridad' => 2,
                        'fecha_toma' => $fecha_toma,
                        'tratamiento' => $tratamiento,
                        'estado' => 1,
                        'id_usuariocrea' => $idusuario,
                        'id_usuariomod' => $idusuario,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                    ];
                    $id_formulario013 = HcFormulario013::insertGetId($arr_formulario013);
                    
                    $formulario013 = HcFormulario013::find($id_formulario013);

                    $biopsias = Hc4_Biopsias::where('hc_id_procedimiento',$id)->where('estado',1)->get();

                    foreach ($biopsias as $value) {
                        $arr_auditoria_biopsias = [
                            'hc_id_procedimiento' => $value->hc_id_procedimiento,
                            'hcid' => $value->hcid,
                            'id_paciente' => $value->id_paciente,
                            'id_doctor' => $value->id_doctor,
                            'descripcion_frasco' => $value->descripcion_frasco,
                            'observacion' => $value->observacion,
                            'numero_frasco' => $value->numero_frasco,
                            'id_tipo_usuario' => $value->id_tipo_usuario,
                            'estado' => 1,
                            'id_usuariocrea' => $idusuario,
                            'id_usuariomod' => $idusuario,
                            'ip_creacion' => $ip_cliente,
                            'ip_modificacion' => $ip_cliente,
                            'subido' => $value->subido,
                            'muestra_biopsia' => $value->muestra_biopsia,
                            'id_seguro' => $value->id_seguro,
                            'id_hc_formulario_013' => $id_formulario013,
                        ];
                        Hc4_Auditoria_Biopsias::create($arr_auditoria_biopsias);
                    }
                }
            }
        }
        $paciente = Paciente::find($formulario013->id_paciente);
        $procedimiento = hc_procedimientos::find($formulario013->id_hc_procedimiento);
        $historiaClinica = $procedimiento->historia;
        $protocolo = DB::table('hc_protocolo as p')->join('historiaclinica as h', 'h.hcid', 'p.hcid')
        ->where('h.id_paciente', $formulario013->id_paciente)
        ->join('hc_procedimientos as hc', 'hc.id', 'p.id_hc_procedimientos')
        ->join('agenda as a', 'a.id', 'h.id_agenda')
        ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hc.id_procedimiento_completo')
        ->leftjoin('hc_receta as r', 'r.id_hc', 'h.hcid')
        ->leftjoin('users as d1', 'h.id_doctor1', 'd1.id')
        ->leftjoin('users as d2', 'h.id_doctor2', 'd2.id')
        ->leftjoin('users as d3', 'h.id_doctor3', 'd3.id')
        ->select('p.*', 'hc.id_procedimiento_completo', 'a.fechaini', 'pc.nombre_general', 'p.hallazgos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1')
        ->orderBy('a.fechaini', 'desc')->orderBy('p.created_at', 'desc')->where('a.espid', '<>', '10')->first();

        /*$protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $historiaClinica->hcid)
            ->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')
            ->leftjoin('pentax as px', 'px.hcid', 'p.hcid')
            ->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')
            ->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')
            ->orderBy('p.created_at', 'desc')->first();*/
        
        $adicionales = Hc_Procedimiento_Final::where('id_hc_procedimientos', $id)->get();
        $mas = true;
        foreach($adicionales as $value2)
        {
            if($mas == true){
                $texto = $texto.$value2->procedimiento->nombre  ;
                $mas = false;
                }
            else{
                $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                }
        }
        if($texto == ""){
            $procedimiento_nombre_2 = procedimiento_completo::find($protocolo->id_procedimiento_completo);
            if(!is_null($procedimiento_nombre_2)){
                $texto = $procedimiento_nombre_2->nombre_general;
            }
        }
        $fecha_toma = null;
        if(!is_null($formulario013)){
            $fecha_toma= date('Y-m-d', strtotime($formulario013->fecha_toma));   
        }
        $empresas = Empresa::where('estado','1')->get();
        $historiaclinica = DB::table('historiaclinica as hc')->where('hc.id_paciente', $formulario013->id_paciente)->join('hc_procedimientos as hp', 'hp.id_hc', 'hc.hcid')
        ->join('hc_evolucion as he', 'he.hc_id_procedimiento', 'hp.id')->join('users as u', 'u.id', 'hp.id_doctor_examinador')->join('agenda as a', 'a.id', 'hc.id_agenda')->join('especialidad as e', 'e.id', 'a.espid')->select('he.*', 'u.apellido1', 'u.apellido2', 'u.nombre1', 'e.nombre')->orderBy('he.created_at', 'desc')->limit(20)->get();
        $biopsias = Hc4_Biopsias::where('hc_id_procedimiento',$id)->where('estado',1)->get();
        $biopsias_auditoria = Hc4_Auditoria_Biopsias::where('id_hc_formulario_013',$formulario013->id)->where('estado',1)->get();
        //dd($formulario013);
        return view('hc_admision/formulario013/edit',['datos'=>$formulario013, 'fecha_toma'=>$fecha_toma, 'empresas'=>$empresas, 'historiaclinica' => $historiaclinica, 'biopsias' => $biopsias, 'biopsias_auditoria' => $biopsias_auditoria, 'paciente' => $paciente, 'protocolo' => $protocolo,'texto' => $texto]);
    }

    public function formulario_013_update(Request $request){

        $array=[
            'id_empresa'=>$request->get('empresa'),
            'resumen_clinico'=>$request->get('resumen_clinico'),
            'estudio_solicitado'=>$request->get('estudio_solicitado'),
            'descripcion_estudio'=>$request->get('descripcion'),
            'prioridad'=>$request->get('prioridad'),
            'tratamiento'=>$request->get('tratamiento'),
            'fecha_toma'=>$request->get('fecha'),
        ];
        HcFormulario013::where('id_hc_procedimiento',$request->get('id'))->update($array);
        return ['respuesta'=>'ok'];
    }

    public function cargar($id)
    {
        $evolucion = Hc_Evolucion::find($id);
        $historiaclinica = $evolucion->historiaclinica;
       
        return ['evolucion' => $evolucion, 'historia' => $historiaclinica];
    }

    public function storeAuditoriaBiopsias(Request $request){
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id_hc_procedimiento = $request['id_hc_procedimiento'];
        $biopsia = Hc4_Biopsias::find($request['id']);
        $formulario013 = HcFormulario013::where('id_hc_procedimiento',$id_hc_procedimiento)->first();
        //dd($formulario013);
        if(!is_null($formulario013)){
            if(!is_null($biopsia)){
                $arr_auditoria_biopsias = [
                    'hc_id_procedimiento' => $biopsia->hc_id_procedimiento,
                    'hcid' => $biopsia->hcid,
                    'id_paciente' => $biopsia->id_paciente,
                    'id_doctor' => $biopsia->id_doctor,
                    'descripcion_frasco' => $biopsia->descripcion_frasco,
                    'observacion' => $biopsia->observacion,
                    'numero_frasco' => $biopsia->numero_frasco,
                    'id_tipo_usuario' => $biopsia->id_tipo_usuario,
                    'estado' => 1,
                    'id_usuariocrea' => $idusuario,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'subido' => $biopsia->subido,
                    'muestra_biopsia' => $biopsia->muestra_biopsia,
                    'id_seguro' => $biopsia->id_seguro,
                    'id_hc_formulario_013' => $formulario013->id,
                ];
                Hc4_Auditoria_Biopsias::create($arr_auditoria_biopsias);
                return ['status' => 'success', 'msj' => 'Agregado correctamente'];
            }
            else{
                return ['status' => 'error', 'msj' => 'No se encontro la biopsia']; 
            }
        }
        else{
            return ['status' => 'error', 'msj' => 'No se encontro el formulario 013']; 
        }
        //return redirect()->route('formulario013.edit', ['id'=>$id_hc_procedimiento]);
    }

    public function deleteAuditoriaBiopsias($id){
        $biopsia = Hc4_Auditoria_Biopsias::find($id);
        //dd($biopsia);
        if(!is_null($biopsia)){
            $arr_biopsia = [
                'estado' => 0,
            ];
            $biopsia->update($arr_biopsia);
            return ['status' => 'success', 'msj' => 'Eliminado correctamente'];
            //return redirect()->route('formulario013.edit', ['id'=>$$biopsia->id_hc_procedimiento]);
        }
        else{
            return ['status' => 'error', 'msj' => 'No se encontro la biopsia'];
        }
    }
}

