<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Observacion_General;
use Sis_medico\Subseguro;
use Sis_medico\Paciente;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\Pentax;
use Sis_medico\Agenda;
use Sis_medico\Pentax_log;
use Sis_medico\PentaxProc;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_usuario;
use Sis_medico\Procedimiento;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Sala;
use Sis_medico\Historiaclinica;
use Sis_medico\ControlDocController;
use Sis_medico\Archivo_historico;
use Sis_medico\Examen_obligatorio;
use Sis_medico\Examen_pendiente;
use Excel;

class ObservacionController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        if(in_array($rolUsuario, array(1, 4, 5,20)) == false){
          return true;
        }
    }
    public function index()
    {
        
        if($this->rol()){
            return response()->view('errors.404');
        }

        $observaciones = Observacion_General::paginate(10);

        $fecha = Date('Y-m-d');
        
        
        return view('observacion/index',['fecha'=> $fecha, 'observaciones' => $observaciones]);
    }
    

    public function create()
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }

        return view('observacion/create' );
    }

    public function search($fecha) {

        if($fecha==null){
            $fecha = date('Y-m-d');
        }else{
            $fecha = date('Y-m-d',$fecha);    
        }
    
        
        $fecha_hasta = date('Y-m-d',strtotime($fecha."- 1 month"));

        //dd($fecha,$fecha_hasta);

        $observaciones = Observacion_General::whereBetween('created_at',[$fecha_hasta,$fecha.' 23:59'])->OrderBy('created_at','desc')->get();
        //dd($observaciones);

        return view('observacion/index_lista', ['observaciones' => $observaciones]);
    }

    public function cantidad() {

       
        $fecha = date('Y-m-d');
        
        

        $observaciones = Observacion_General::where('estado','1')->whereBetween('created_at',[$fecha.' 00:00:00',$fecha.' 23:59:00'])->OrderBy('created_at','desc')->get();

        //dd($observaciones);

        return $observaciones->count();
    }

    

    public function store(Request $request)
    {
        
        date_default_timezone_set('America/Guayaquil');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $observacion = $request['observacion'];
        
        if($observacion!=null){

            Observacion_General::create([
                'observacion' => $observacion,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario
            
            ]);

        }
        

        return redirect()->intended('/observacion');
    }

    public function inactiva($id)
    {
        
        date_default_timezone_set('America/Guayaquil');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $observacion = Observacion_General::find($id);
        
        if($observacion!=null){

            $observacion->update([
                
                'estado' => '-1',
                'ip_modificacion' => $ip_cliente,
                
                'id_usuariomod' => $idusuario
            
            ]);

        }
        

        return redirect()->intended('/observacion');
    }

    public function activa($id)
    {
        
        date_default_timezone_set('America/Guayaquil');
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $observacion = Observacion_General::find($id);
        
        if($observacion!=null){

            $observacion->update([
                
                'estado' => '1',
                'ip_modificacion' => $ip_cliente,
                
                'id_usuariomod' => $idusuario
            
            ]);

        }
        

        return redirect()->intended('/observacion');
    }

    private function validateInput($request) {
        $this->validate($request, [
        'nombre' => 'required|max:60|unique:seguros',
        'descripcion' => 'required',
        'tipo' => 'required',
        'color' => 'required|unique:seguros'
    ]);
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $estado, $hora)
    {
        
             
             
        return view('pentax/edit',['id' => $id, 'estado' => $estado, 'hora' => $hora, 'pentax' => $pentax, 'salas' => $salas, 'procedimientos' => $procedimientos, 'pentax_procs' => $pentax_procs, 'doctores' => $doctores, 'enfermeros' => $enfermeros, 'seguros' => $seguros, 'subseguros' => $subseguros, 'pre_post' => $pre_post, 'ex_pre' => $ex_pre, 'ex_post' => $ex_post ]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
             
        
    }

    
    

    public function show($id)
    {
        //
    }

    



}
