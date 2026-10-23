<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Excepcion_Sala;
use Sis_medico\Horario_Sala;
use Sis_medico\User;
use Sis_medico\Sala;

class HorarioSalaController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user-management';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 5, 20)) == false && Auth::user()->id != '0916593445') {
            return true;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $idusuario = Auth::user()->id;
        $usuario   = User::find($idusuario);

        return view('horario_sala/index', ['id' => $idusuario, 'usuario' => $usuario]);

    }

    public function crear(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('horario_sala/agregar_sala');
    }

    public function index_admin_ingreso($id)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $idsala = $id;
        //$usuario = User::find($idsala);
        /* $horarios = Horario_Doctor::where('id_doctor',$idusuario)->orderBy('ndia', 'asc')->get();

        return view('horario/index', ['id' => $idusuario, 'usuario' => $usuario, 'horarios' => $horarios]);*/

        $horarios  = Horario_Sala::where('id_sala', $idsala)->orderBy('ndia', 'asc')->get();
        $horarios1 = Horario_Sala::where('id_sala', $idsala)->where('ndia', 1)->orderBy('ndia', 'asc')->get();
        $horarios2 = Horario_Sala::where('id_sala', $idsala)->where('ndia', 2)->orderBy('ndia', 'asc')->get();
        $horarios3 = Horario_Sala::where('id_sala', $idsala)->where('ndia', 3)->orderBy('ndia', 'asc')->get();
        $horarios4 = Horario_Sala::where('id_sala', $idsala)->where('ndia', 4)->orderBy('ndia', 'asc')->get();
        $horarios5 = Horario_Sala::where('id_sala', $idsala)->where('ndia', 5)->orderBy('ndia', 'asc')->get();
        $horarios6 = Horario_Sala::where('id_sala', $idsala)->where('ndia', 6)->orderBy('ndia', 'asc')->get();
        $horarios7 = Horario_Sala::where('id_sala', $idsala)->where('ndia', 7)->orderBy('ndia', 'asc')->get();

        $cantidad = $horarios->count();
        $c[0]     = $horarios1->count();
        $c[1]     = $horarios2->count();
        $c[2]     = $horarios3->count();
        $c[3]     = $horarios4->count();
        $c[4]     = $horarios5->count();
        $c[5]     = $horarios6->count();
        $c[6]     = $horarios7->count();

        $diaInicio = "Monday";
        $diaFin    = "Sunday";

        $fecha    = date('Y-m-d');
        $strFecha = strtotime($fecha);

        $fechaInicio = date('Y-m-d', strtotime('last ' . $diaInicio, $strFecha));
        $fechaFin    = date('Y-m-d', strtotime('next ' . $diaFin, $strFecha));

        if (date("l", $strFecha) == $diaInicio) {
            $fechaInicio = date("Y-m-d", $strFecha);
        }
        if (date("l", $strFecha) == $diaFin) {
            $fechaFin = date("Y-m-d", $strFecha);
        }
        $fechaInicio = $fechaInicio . ' 00:00';
        $fechaFin    = $fechaFin . ' 23:59';
        $extra       = Excepcion_Sala::where('id_sala1', $idsala)->whereBetween('inicio', [$fechaInicio, $fechaFin])->get();

        return view('horario_sala/horario_sala',['id' => $idsala, 'horarios1' => $horarios1, 'horarios2' => $horarios2, 'horarios3' => $horarios3, 'horarios4' => $horarios4, 'horarios5' => $horarios5,'horarios6' => $horarios6,'horarios7' => $horarios7,'horarios' => $horarios, 'c' => $c, 'cantidad' => $cantidad, 'extra' => $extra]);
    }

    public function unicodia2(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $idusuario = Auth::user()->id;        
        $sala = Sala::find($request['id_sala1']);
        $inicio = $request['inicio'];
        $fin = $request['fin'];
        $inicio_2 = strtotime ( '+1 minute' , strtotime ( $inicio));
        $inicio_2 = date("Y-m-d H:i:s", $inicio_2);
        $fin_2 = strtotime ( '-1 minute' , strtotime ( $fin));
        $fin_2 = date("Y-m-d H:i:s", $fin_2);
        $horarios = DB::Select("SELECT *
                    FROM excepcion_sala
                    WHERE id_sala1 = '".$request['id_sala1']."'  AND ((inicio BETWEEN '".$inicio_2."' AND '".$fin_2."') OR (fin BETWEEN '".$inicio_2."' AND '".$fin_2."') OR ('".$inicio_2."' BETWEEN inicio AND fin) OR ('".$fin_2."' BETWEEN inicio AND fin));");

        $cuenta_excepcion  = count($horarios);
        if($cuenta_excepcion  > 0)
        {
           return back()->with('error', 'Ya existe una excepcion de horario registrado')->with('inicio', $inicio)->with('fin', $fin); 
        }

        $start =  $inicio_2;
        $end =  $fin_2;
        date_default_timezone_set('UTC');
        $ndia =  date('N',strtotime($start));
        $start2 = date('H:i', strtotime($start));
        $end2 = date('H:i', strtotime($end));
        $horarios2 = DB::Select("SELECT *
                    FROM horario_sala
                    WHERE id_sala = '".$request['id_sala1']."'  AND  ndia = '".$ndia."'  AND ((hora_ini BETWEEN '".$start2."' AND '".$end2."') OR (hora_fin BETWEEN '".$start2."' AND '".$end2."') OR ('".$end2."' BETWEEN hora_ini AND hora_fin) OR ('".$end2."' BETWEEN hora_ini AND hora_fin));");       
        $cuenta_horario  = count($horarios2);

        if($cuenta_horario  > 0)
        {
           return back()->with('error', 'Ya existe un horario registrado')->with('inicio', $inicio)->with('fin', $fin); 
        }
        $input = [
            'id_sala1' => $request['id_sala1'],
            'inicio' => $request['inicio'],
            'fin' => $request['fin'],
            'tipo' => $request['tipo'],
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            ];

        Excepcion_Sala::create($input);
        return back();
    }

    public function actualizar($id, $start, $end, $extra){
        if($extra == 0){
            $existe = Horario_Sala::find($id);
            if($existe != array()){
                date_default_timezone_set('America/Guayaquil');
                $ip_cliente= $_SERVER["REMOTE_ADDR"];
                $idusuario = Auth::user()->id;
                $start =  substr($start, 0,10);
                $end =  substr($end, 0,10);
                date_default_timezone_set('UTC');
                $start2 = date('H:i', $start);
                $end2 = date('H:i', $end);
                $input = [
                    'hora_ini' => $start2,
                    'hora_fin' => $end2,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];

                Horario_Sala::where('id', $id)->update($input);
                return "Se ha modificado el horario";
            }
            return "No existe Horario";
        }
        if($extra == 1){
            $existe = Excepcion_Sala::find($id);
            if($existe != array()){
                date_default_timezone_set('America/Guayaquil');
                $ip_cliente= $_SERVER["REMOTE_ADDR"];
                $idusuario = Auth::user()->id;
                $start =  substr($start, 0,10);
                $end =  substr($end, 0,10);
                date_default_timezone_set('UTC');
                $start2 = date('Y-m-d H:i', $start);
                $end2 = date('Y-m-d H:i', $end);
                $input = [
                    'inicio' => $start2,
                    'fin' => $end2,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];

                Excepcion_Sala::where('id', $id)->update($input);
                return "Se ha modificado el horario";
            }
            return "No existe Horario";
        }
        return "No existe Horario";
    }

    public function dato_agregar2($start, $end, $id){
        if($this->rol()){
            return response()->view('errors.404');
        }
        date_default_timezone_set('UTC');
        setlocale(LC_ALL,"es_ES");
        $inicio  = substr($start, 0,10);
        $inicio2 = date('H:i', $inicio);
        $fin  = substr($end, 0,10);
        $fin2 = date('H:i', $fin);
        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $fin2 = $dias[date('w', $fin)]." ".$fin2;
        $inicio2 = $dias[date('w', $inicio)]." ".$inicio2;

        return view('horario_admin_sala/agregar', ['start' => $start, 'end' => $end, 'inicio' => $inicio2, 'fin' => $fin2,'id' => $id]); 
    }

    public function agregarmodal2(Request $request){
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $start =  $request['inicio'];
        $end =  $request['fin'];
        $start =  substr($start, 0,10);
        $end =  substr($end, 0,10);
        
        date_default_timezone_set('UTC');
        $ndia =  date('N', $start);
        if($ndia == 1){
            $dia = 'Lun.';
        }
        if($ndia == 2){
            $dia = 'Mar.';
        }
        if($ndia == 3){
            $dia = 'Mié.';
        }
        if($ndia == 4){
            $dia = 'Jue.';
        }
        if($ndia == 5){
            $dia = 'Vie.';
        }
        if($ndia == 6){
            $dia = 'Sáb.';
        }
        if($ndia == 7){
            $dia = 'Dom.';
        }
        $start2 = date('H:i', $start);
        $end2 = date('H:i', $end);
        $input = [
            'dia' => $dia,
            'ndia' => $ndia,
            'hora_ini' => $start2,
            'hora_fin' => $end2,
            'id_sala' => $request['id_sala'],
            'tipo' => $request['tipo'],
            'ip_creacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
        ];

        $id_horario = Horario_Sala::insertGetId($input);

        return back();
    }
    public function actualizar2($id, $start, $end, $extra)
    {
        if($extra == 0){
            date_default_timezone_set('America/Guayaquil');
            $ip_cliente= $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            $start =  substr($start, 0,10);
            $end =  substr($end, 0,10);
           
            date_default_timezone_set('UTC');
            $ndia =  date('N', $start);
            if($ndia == 1){
                $dia = 'Lun.';
            }
            if($ndia == 2){
                $dia = 'Mar.';
            }
            if($ndia == 3){
                $dia = 'Mié.';
            }
            if($ndia == 4){
                $dia = 'Jue.';
            }
            if($ndia == 5){
                $dia = 'Vie.';
            }
            if($ndia == 6){
                $dia = 'Sáb.';
            }
            if($ndia == 7){
                $dia = 'Dom.';
            }
            $start2 = date('H:i', $start);
            $end2 = date('H:i', $end);
            $input = [
                'hora_ini' => $start2,
                'hora_fin' => $end2,
                'ndia' => $ndia,
                'dia' => $dia,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            ];

            Horario_Sala::where('id', $id)->update($input);
            return "Se ha modificado el horario";
        }
        if($extra == 1){
            $existe = Excepcion_Sala::find($id);
            if($existe != array()){
                date_default_timezone_set('America/Guayaquil');
                $ip_cliente= $_SERVER["REMOTE_ADDR"];
                $idusuario = Auth::user()->id;
                $start =  substr($start, 0,10);
                $end =  substr($end, 0,10);
                date_default_timezone_set('UTC');
                $start2 = date('Y-m-d H:i', $start);
                $end2 = date('Y-m-d H:i', $end);
                $input = [
                    'inicio' => $start2,
                    'fin' => $end2,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                ];

                Excepcion_Sala::where('id', $id)->update($input);
                return "Se ha modificado el horario";
            }
            return "No existe Horario";
        }
    }

    public function eliminar($id)
    {
        Horario_Sala::where('id', $id)->delete();
         return "Se ha eliminado un horario";
    }

    public function eliminarunico($id)
    {   
        $horario = Excepcion_Sala::find($id);
        $id_sala = $horario->id_sala1;
        $hora_ini = $horario->inicio;
        $hora_fin = $horario->fin;

            $validacion =  DB::table('agenda')->where('id_sala', '=', $id_sala)
                                        ->where('estado_cita', '<>', '4')//No admisionadas 
                                        ->where('estado', '=', '1')//Activas 
                                        ->where('proc_consul', '<>', '2')//NO reuniones 
                                        ->where(function ($query) use ($hora_ini, $hora_fin) {
                                            $query->where(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechafin', [$hora_ini, $hora_fin]);
                                                        })
                                                    ->orWhere(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechaini', [$hora_ini, $hora_fin]);
                                                        });
                                            })->get();
            foreach($validacion as $value){
                $agenda = Agenda::findOrFail($value->id);
                $ip_cliente= $_SERVER["REMOTE_ADDR"];
                $idusuario = Auth::user()->id;
                date_default_timezone_set('America/Guayaquil');
                $descripcion="Horario Eliminado";
                $input = [
                    'estado' => '-1',
                    'estado_cita' => '2',
                    'observaciones' => $descripcion,
                    'id_usuariomod' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                ];

                $input_log = [
                    'id_agenda' =>  $value->id,
                    'descripcion' =>  'Horario Eliminado',
                    'estado_ant' =>  $agenda->estado,
                    'estado' =>  '-1',
                    'estado_cita' =>  '2',
                    'estado_cita_ant' =>  $agenda->estado_cita,
                    'observaciones_ant' =>  $agenda->observaciones,
                    'observaciones' =>  $descripcion,
                    'fechaini_ant' =>  $agenda->fechaini,
                    'fechafin_ant' =>  $agenda->fechafin,
                    'id_usuariomod' => $idusuario,
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'ip_creacion' => $ip_cliente,
                ];

                $agenda::where('id', $value->id)
                ->update($input);
                Log_Agenda::create($input_log);

            }
            Excepcion_Sala::destroy($id);   
            return "Horario Extra eliminado exitosamente";
            /*
                
                return "Horario Extra eliminado exitosamente";*/
        //}
    }
    public function valida_horarioxsala($request){

       //dd($request->all());
        $id_sala = $request['id_sala'];
        $fechaini = $request['inicio'];
        $fechafin = $request['fin'];

        $ndiaini = $this->saber_dia($fechaini);
        $horaini = date('H:i:s',strtotime($fechaini));

        $ndiafin = $this->saber_dia($fechafin);
        $horafin = date('H:i:s',strtotime($fechafin));

        $inicio = date('Y-m-d H:i:s',strtotime($fechaini));
        $final = date('Y-m-d H:i:s',strtotime($fechafin));

        
        
       
        $cantidad_ini = Horario_Sala::where('id_sala',$id_sala)
                                     ->where('ndia',$ndiaini)
                                     ->where('estado','1')
                                     ->where('hora_ini','<=',$horaini)
                                     ->where('hora_fin','>=',$horaini)
                                     ->count();
           //dd($cantidad_ini, $id_sala, $ndiaini,$horaini);  
        $cantidad_fin = Horario_Sala::where('id_sala',$id_sala)
                                     ->where('ndia',$ndiafin) 
                                     ->where('estado','1') 
                                     ->where('hora_ini','<=',$horafin) 
                                     ->where('hora_fin','>=',$horafin) 
                                     ->count();
           // dd($cantidad_fin, $id_sala, $ndiafin,$horafin);  

        if($cantidad_ini == 0){
            $cantidad_ini = Excepcion_Sala::where('id_sala1',$id_sala) 
                                          ->where('inicio','<=',$inicio) 
                                          ->where('fin','>=',$inicio)
                                          ->count();
        }

        if($cantidad_fin == 0){
            $cantidad_fin = Excepcion_Sala::where('id_sala1',$id_sala) 
                                          ->where('inicio','<=',$final)
                                          ->where('fin','>=',$final)
                                          ->count();    
        }
      

        $reglas=['inicio' => 'comparamayor:0,'.$cantidad_ini,
                'fin' => 'comparamayor:0,'.$cantidad_fin
                    ];
        $mensajes=['inicio.comparamayor'=>'fecha de inicio esta fuera del horario laborable de la Sala',
                    'fin.comparamayor'=>'fecha de fin esta fuera del horario laborable de la Sala'
                    ];            

        $this->validate($request,$reglas,$mensajes);  
         
    }
    public function saber_dia($fecha) {

        $dias = array('0','1','2','3','4','5','6','7');//12/1/2018

        $nombre_dia = $dias[date('N', strtotime($fecha))];

        return $nombre_dia;
    }
}
