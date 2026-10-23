<?php

namespace Sis_medico\Http\Controllers\disponibilidad;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;   
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_acreedores;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Forma_de_pago;
use Sis_medico\Pais;
use Sis_medico\Hospital;
use Sis_medico\Sala;
use Sis_medico\AgendaQ;
use laravel\laravel;
use Carbon\Carbon;
use Sis_medico\Agenda;




class DisponibilidadController extends Controller
{

	 public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;      
        if(in_array($rolUsuario, array(1, 4, 5)) == false){
          return true;
        }
    }

    public function disponibilidad_menu()
     {
       $hospital = Hospital::where('estado', 1)->get();

        return view('disponibilidad/index',['hospital'=>$hospital]);
       //return view('dashboard/dashboard2',['modulo'=>$modulo]);

 }

  public function sala_opciones($id)
     {
       $sala= Sala::where('id_hospital', $id)->get();

       $hospital = Hospital::find($id);
       //dd($hospital);
       //return $id;

        return view('disponibilidad/sala_opciones', ['sala'=> $sala,'id'=> $id,'hospital'=>$hospital]);
       //return view('dashboard/dashboard2',['modulo'=>$modulo]);
    }



     public function salas_todas(Request $request, $id_hospital)
     {
       //dd($request->all());
       $salas= Sala::where('id_hospital',$id_hospital)->get();
       //dd($salas);
        //$salas = DB::table('sala')->where('id_hospital', '=', '2')->get();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $fecha = date('Y-m-d');
    
        if(!is_null($request['fecha'])){
          $fecha = $request['fecha'];


            }
             
              $procedimientos = DB::table('agenda as a')
            ->join('paciente', 'a.id_paciente', '=', 'paciente.id')
            ->join('sala as s', 's.id', 'a.id_sala')
             ->join('hospital as h', 'h.id', 's.id_hospital')
            ->join('seguros', 'a.id_seguro', '=', 'seguros.id')
            ->join('users', 'a.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'a.id_usuariomod', '=', 'um.id')
            ->join('procedimiento', 'a.id_procedimiento', '=', 'procedimiento.id')
            ->select('a.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'paciente.ciudad')
            ->where('proc_consul', '=', 1)
            ->whereBetween('fechaini',[$fecha.' 00:00:00',$fecha.' 23:59:59'])
            ->where('s.id_hospital',$id_hospital)
            ->get();
             //dd($fecha);

            $consultas = DB::table('agenda as a')
            ->join('paciente', 'a.id_paciente', '=', 'paciente.id')
            ->join('sala as s', 's.id', 'a.id_sala')
            ->join('hospital as h', 'h.id', 's.id_hospital')
            ->join('seguros', 'a.id_seguro', '=', 'seguros.id')
            ->join('users', 'a.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'a.id_usuariomod', '=', 'um.id')
            ->select('a.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1', 'paciente.ciudad')
            ->where('proc_consul', '=', 0)
            ->whereBetween('fechaini',[$fecha.' 00:00:00',$fecha.' 23:59:59'])
            ->where('s.id_hospital',$id_hospital)
            ->get();
           
               //dd($fecha);
           $reuniones = DB::table('agenda as a')->where('proc_consul', '=', 2)
            ->join('users', 'a.id_usuariocrea', '=', 'users.id')
            ->join('sala as s', 's.id', 'a.id_sala')
            ->join('hospital as h', 'h.id', 's.id_hospital')
            ->join('users as um', 'a.id_usuariomod', '=', 'um.id')
            ->select('a.*', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1')
            ->whereBetween('fechaini',[$fecha.' 00:00:00',$fecha.' 23:59:59'])
            ->where('s.id_hospital',$id_hospital)
            ->get();
              //return($fecha);
             //return view('disponibilidad/salas_todas', ['salas'=>$salas,'procedimientos'=> $procedimientos, 'consultas'=> $consultas,'salas'=>$salas, 'reuniones'=> $reuniones,'id'=> $id,  'fecha'=>$fecha,'fecha'=> $fecha,'versuspendidas' => '0', 'fecha2'=>$fecha, ]);
     
      
            return view('disponibilidad/salas_todas', ['procedimientos'=> $procedimientos, 'consultas'=> $consultas,'salas'=>$salas,'fecha'=> $fecha, 'id_hospital'=>$id_hospital,'reuniones'=> $reuniones, 'versuspendidas' => '0']);
     
   }


    public function sala_agenda(Request $request, $id)
     {
          //dd($request->all());
        $sala_nombre= Sala::findorfail($id);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $fecha = date('Y-m-d');
      
        if(!is_null($request['fecha'])){
          $fecha = $request['fecha'];
         
        }

        //Agendaf
        $procedimientos = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'paciente.ciudad')
            ->where('proc_consul', '=', 1)
            ->whereBetween('fechaini',[$fecha.' 00:00:00',$fecha.' 23:59:59'])
            ->where('id_sala',$id)
            ->get();
           
           // dd($procedimientos);

              //Consultas - Agenda3

        $consultas = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1', 'paciente.ciudad')
            ->where('proc_consul', '=', 0)
            ->whereBetween('fechaini',[$fecha.' 00:00:00',$fecha.' 23:59:59'])
            ->where('id_sala',$id)
            ->get();
            //dd($procedimientos, $consultas);
            //Reuniones- Agenda 2


            $reuniones = DB::table('agenda')->where('proc_consul', '=', 2)
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('users as um', 'agenda.id_usuariomod', '=', 'um.id')
            ->select('agenda.*', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'um.apellido1 as umapellido1', 'um.nombre1 as umnombre1')
            ->whereBetween('fechaini',[$fecha.' 00:00:00',$fecha.' 23:59:59'])
            ->where('id_sala',$id)
            ->get();


            //dd($procedimientos, $consultas, $reuniones);
      
             return view('disponibilidad/sala_agenda', ['procedimientos'=> $procedimientos, 'consultas'=> $consultas,'sala_nombre'=>$sala_nombre, 'reuniones'=> $reuniones,'id'=> $id, 'fecha'=> $fecha, 'versuspendidas' => '0']);
     }

    
     public function sala_ajax($id)
     {
       
       $agenda = Agenda::find($id);
       //dd($agenda);
       //return $id;
        
     
        return view('disponibilidad/sala_ajax', ['agenda'=> $agenda,'id'=> $id]);
       //return view('dashboard/dashboard2',['modulo'=>$modulo]);
    }
     /*public function sala_ajax2($id)
     {
       
       $agenda = Agenda::find($id);
       //dd($hospital);
       //return $id;
        
         //dd($agenda);
        return view('disponibilidad/sala_ajax2', ['agenda'=> $agenda,'id'=> $id]);
       //return view('dashboard/dashboard2',['modulo'=>$modulo]);
    }*/


  
}