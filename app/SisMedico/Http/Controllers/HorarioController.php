<?php

namespace Sis_medico\Http\Controllers;

use Illuminate\Http\Request;
use Sis_medico\Http\Requests\UsuarioRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Sis_medico\User;
use Sis_medico\pais;
use Sis_medico\especialidad;
use Sis_medico\user_espe;
use Sis_medico\TipoUsuario;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Log_Usuario;
use Sis_medico\Log_Agenda;
use Sis_medico\Horario_Doctor;
use Sis_medico\Excepcion_Horario;
use Sis_medico\Agenda;
use Validator;
use Illuminate\Support\ServiceProvider;

class HorarioController extends Controller
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
    private function rol(){
        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if(in_array($rolUsuario, array(1,3,5)) == false && Auth::user()->id != '0916593445'){
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
        if($this->rol()){
            return response()->view('errors.404');
        }
        $idusuario = Auth::user()->id;
        $usuario = User::find($idusuario);
       /* $horarios = Horario_Doctor::where('id_doctor',$idusuario)->orderBy('ndia', 'asc')->get();

       
       return view('horario/index', ['id' => $idusuario, 'usuario' => $usuario, 'horarios' => $horarios]);*/
       
       $horarios = Horario_Doctor::where('id_doctor',$idusuario)->orderBy('ndia', 'asc')->get();
       $horarios1 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',1)->orderBy('ndia', 'asc')->get();
       $horarios2 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',2)->orderBy('ndia', 'asc')->get();
       $horarios3 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',3)->orderBy('ndia', 'asc')->get();
       $horarios4 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',4)->orderBy('ndia', 'asc')->get();
       $horarios5 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',5)->orderBy('ndia', 'asc')->get();
       $horarios6 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',6)->orderBy('ndia', 'asc')->get();
       $horarios7 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',7)->orderBy('ndia', 'asc')->get();

       $cantidad=$horarios->count();
       $c[0]=$horarios1->count();
       $c[1]=$horarios2->count();
       $c[2]=$horarios3->count();
       $c[3]=$horarios4->count();
       $c[4]=$horarios5->count();
       $c[5]=$horarios6->count();
       $c[6]=$horarios7->count();

       $diaInicio="Monday";
        $diaFin="Sunday";

        $fecha = date('Y-m-d');
        $strFecha = strtotime($fecha);

        $fechaInicio = date('Y-m-d',strtotime('last '.$diaInicio,$strFecha));
        $fechaFin = date('Y-m-d',strtotime('next '.$diaFin,$strFecha));

        if(date("l",$strFecha)==$diaInicio){
            $fechaInicio= date("Y-m-d",$strFecha);
        }
        if(date("l",$strFecha)==$diaFin){
            $fechaFin= date("Y-m-d",$strFecha);
        }
        $fechaInicio= $fechaInicio.' 00:00';
        $fechaFin= $fechaFin.' 23:59';
        $extra =  Excepcion_Horario::where('id_doctor1', $idusuario)->whereBetween('inicio', [$fechaInicio, $fechaFin])->get();


       return view('horario/index', ['id' => $idusuario, 'usuario' => $usuario, 'horarios1' => $horarios1, 'horarios2' => $horarios2, 'horarios3' => $horarios3, 'horarios4' => $horarios4, 'horarios5' => $horarios5,'horarios6' => $horarios6,'horarios7' => $horarios7,'horarios' => $horarios, 'c' => $c, 'cantidad' => $cantidad, 'extra' => $extra]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unicodia(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $idusuario = Auth::user()->id;        
        $usuario = User::find($idusuario);
        $inicio = $request['inicio'];
        $fin = $request['fin'];
        $horarios = DB::Select("SELECT *
                    FROM excepcion_horario
                    WHERE id_doctor1 = '".$request['id_doctor1']."'  AND ((inicio BETWEEN '".$inicio."' AND '".$fin."') OR (fin BETWEEN '".$inicio."' AND '".$fin."') OR ('".$inicio."' BETWEEN inicio AND fin) OR ('".$fin."' BETWEEN inicio AND fin));");

        $cuenta_excepcion  = count($horarios);
        if($cuenta_excepcion  > 0)
        {
           return back()->with('error', 'Ya existe una excepcion de horario registrado')->with('inicio', $inicio)->with('fin', $fin); 
        }

        $start =  $request['inicio'];
        $end =  $request['fin'];
        date_default_timezone_set('UTC');
        $ndia =  date('N',strtotime($start));
        $start2 = date('H:i', strtotime($start));
        $end2 = date('H:i', strtotime($end));
        $horarios2 = DB::Select("SELECT *
                    FROM horario_doctor
                    WHERE id_doctor = '".$request['id_doctor1']."'  AND  ndia = '".$ndia."'  AND ((hora_ini BETWEEN '".$start2."' AND '".$end2."') OR (hora_fin BETWEEN '".$start2."' AND '".$end2."') OR ('".$end2."' BETWEEN hora_ini AND hora_fin) OR ('".$end2."' BETWEEN hora_ini AND hora_fin));");       
        $cuenta_horario  = count($horarios2);

        if($cuenta_horario  > 0)
        {
           return back()->with('error', 'Ya existe un horario registrado')->with('inicio', $inicio)->with('fin', $fin); 
        }
        $input = [
            'id_doctor1' => $request['id_doctor1'],
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

        Excepcion_Horario::create($input);
        return redirect('horario');
    }
    public function unicodia2(Request $request)
    {
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $idusuario = Auth::user()->id;        
        $usuario = User::find($request['id_doctor1']);
        $inicio = $request['inicio'];
        $fin = $request['fin'];
        $inicio_2 = strtotime ( '+1 minute' , strtotime ( $inicio));
        $inicio_2 = date("Y-m-d H:i:s", $inicio_2);
        $fin_2 = strtotime ( '-1 minute' , strtotime ( $fin));
        $fin_2 = date("Y-m-d H:i:s", $fin_2);
        $horarios = DB::Select("SELECT *
                    FROM excepcion_horario
                    WHERE id_doctor1 = '".$request['id_doctor1']."'  AND ((inicio BETWEEN '".$inicio_2."' AND '".$fin_2."') OR (fin BETWEEN '".$inicio_2."' AND '".$fin_2."') OR ('".$inicio_2."' BETWEEN inicio AND fin) OR ('".$fin_2."' BETWEEN inicio AND fin));");

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
                    FROM horario_doctor
                    WHERE id_doctor = '".$request['id_doctor1']."'  AND  ndia = '".$ndia."'  AND ((hora_ini BETWEEN '".$start2."' AND '".$end2."') OR (hora_fin BETWEEN '".$start2."' AND '".$end2."') OR ('".$end2."' BETWEEN hora_ini AND hora_fin) OR ('".$end2."' BETWEEN hora_ini AND hora_fin));");       
        $cuenta_horario  = count($horarios2);

        if($cuenta_horario  > 0)
        {
           return back()->with('error', 'Ya existe un horario registrado')->with('inicio', $inicio)->with('fin', $fin); 
        }
        $input = [
            'id_doctor1' => $request['id_doctor1'],
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

        Excepcion_Horario::create($input);
        return back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsuarioRequest $request)
    {
         
    }

    public function validarhorario($id)
    {   
        $horario = Excepcion_Horario::find($id);
        $id_doctor =  $horario->id_doctor1;
        $hora_ini = $horario->inicio;
        $hora_fin = $horario->fin;

        $validar =  DB::table('agenda')->where('id_doctor1', '=', $id_doctor)
                                        ->where('estado', '=', '1')
                                        ->where(function ($query) use ($hora_ini, $hora_fin) {
                                            $query->where(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechafin', [$hora_ini, $hora_fin]);
                                                        })
                                                    ->orWhere(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechaini', [$hora_ini, $hora_fin]);
                                                        });
                                            })
                                        ->where(function ($query) {
                                            return $query->where('estado_cita', '=', 4)
                                                        ->orWhere('proc_consul', '=', 2);
                                        })
                                        ->get();
        $valida2 = count($validar);

        if($valida2>0){

            $r1=0;
            $r2=0;        
            foreach($validar as $val){
                if($val->estado_cita=='4'){
                    $r1 = 1;
                }

                if($val->proc_consul=='2'){
                    $r2 = 1;
                }
            } 

            $vmsn="";
            if($r1==1 && $r2==1){
                $vmsn="Existe una Reunión y una cita ya Admisionada.";
            }
            if($r1==1 && $r2==0){
                $vmsn="Existe una cita ya Admisionada.";
            }
            if($r1==0 && $r2==1){
                $vmsn="Existe una Reunión agendada.";
            }    

            return $vmsn;

        }else{
            return 0;
        }
    } 

    public function validarhorario2($id)
    {   
        $horario = Excepcion_Horario::find($id);
        $id_doctor =  $horario->id_doctor1;
        $hora_ini = $horario->inicio;
        $hora_fin = $horario->fin;

        $validar =  DB::table('agenda')->where('id_doctor1', '=', $id_doctor)
                                        ->where('estado_cita', '=', '1')
                                        ->where('proc_consul', '<>', '2')
                                        ->where(function ($query) use ($hora_ini, $hora_fin) {
                                            $query->where(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechafin', [$hora_ini, $hora_fin]);
                                                        })
                                                    ->orWhere(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechaini', [$hora_ini, $hora_fin]);
                                                        });
                                            })
                                        ->get();

        $valida2 = count($validar);

        if($valida2>0){

            return "Tiene citas confirmadas, ¿ Desea reagendarlas ?";
        
        }else{

            return 0; 

        } 

    }      


    public function eliminarunico($id)
    {   
        $horario = Excepcion_Horario::find($id);
        $id_doctor =  $horario->id_doctor1;
        $hora_ini = $horario->inicio;
        $hora_fin = $horario->fin;

        /*$validar =  DB::table('agenda')->where('id_doctor1', '=', $id_doctor)
                                       ->where('estado_cita', '=', '1')
                                       ->where(function ($query) use ($hora_ini, $hora_fin) {
                                            $query->where(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechafin', [$hora_ini, $hora_fin]);
                                                        })
                                                    ->orWhere(function ($query) use ($hora_ini, $hora_fin) {
                                                        $query->whereBetween('fechaini', [$hora_ini, $hora_fin]);
                                                        });
                                            })->get();
        $valida2 = count($validar);

        if($valida2 >= 1)
        {
            return 0;
        }

        if($valida2 == 0)
        {  */ 
            $validacion =  DB::table('agenda')->where('id_doctor1', '=', $id_doctor)
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
            Excepcion_Horario::destroy($id);   
            return "Horario Extra eliminado exitosamente";
            /*
                
                return "Horario Extra eliminado exitosamente";*/
        //}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Horario_Doctor::where('id', $id)->delete();
         return "Se ha eliminado un horario";
    }

    /**
     * Search user from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function search(Request $request) {

         
    }
    

    private function validateInput($request) {
            


        $this->validate($request,[]);


    }
    public function actualizar($id, $start, $end, $extra){
        if($extra == 0){
            $existe = Horario_doctor::find($id);
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

                Horario_Doctor::where('id', $id)->update($input);
                return "Se ha modificado el horario";
            }
            return "No existe Horario";
        }
        if($extra == 1){
            $existe = Excepcion_Horario::find($id);
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

                Excepcion_Horario::where('id', $id)->update($input);
                return "Se ha modificado el horario";
            }
            return "No existe Horario";
        }
        return "No existe Horario";
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

            Horario_Doctor::where('id', $id)->update($input);
            return "Se ha modificado el horario";
        }
        if($extra == 1){
            $existe = Excepcion_Horario::find($id);
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

                Excepcion_Horario::where('id', $id)->update($input);
                return "Se ha modificado el horario";
            }
            return "No existe Horario";
        }
    }

    
    public function creahorario(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $reglas = ['dia' => 'required',
                    'hora_ini' => 'required',
                    'hora_fin' => 'required'
            ];
        
        $mensajes = [
                'dia.required' => 'Selecciona el día.',
                'hora_ini.required' => 'Selecciona la hora de inicio.',
                'hora_fin.required' => 'Selecciona la hora de fin.',
                ];

        $this->validate($request, $reglas, $mensajes); 

        if($request['dia']=='TD')
        {
            for($x=1; $x<6; $x++)
            {
                if($x==1)
                {
                    $dia='Lun.';
                }
                elseif($x==2)
                {
                    $dia='Mar.';
                }
                elseif($x==3){
                    $dia='Mié.';
                }
                elseif($x==4)
                {
                    $dia='Jue.';
                }
                elseif($x==5)
                {
                    $dia='Vie.';
                }

                $this->validatehorario3($request,$id,$dia); 
            } 

            for($y=1; $y<6; $y++)
            {

                if($y==1)
                {
                    $dia='Lun.';

                }
                elseif($y==2)
                {
                    $dia='Mar.';

                }
                elseif($y==3)
                {
                    $dia='Mié.';
                    
                }
                elseif($y==4)
                {
                    $dia='Jue.';

                }
                elseif($y==5)
                {
                    $dia='Vie.';
                }
                
                $input = [
                    'dia' => $dia,
                    'ndia' => $y,
                    'hora_ini' => $request['hora_ini'],
                    'hora_fin' => $request['hora_fin'],
                    'id_doctor' => $id,

                    'ip_creacion' => $ip_cliente,
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
            
                ];
                

                Horario_Doctor::create($input);

            }
                    

        }
        else{
            if($request['dia']=='Lun.'){
                $ndia=1;
            }elseif($request['dia']=='Mar.'){
                $ndia=2;
            }elseif($request['dia']=='Mié.'){
                $ndia=3;
            }elseif($request['dia']=='Jue.'){
                $ndia=4;
            }elseif($request['dia']=='Vie.'){
                $ndia=5;
            }elseif($request['dia']=='Sáb.'){
                $ndia=6;
            }
            elseif($request['dia']=='Dom.'){
                $ndia=7;
            }

            

            $this->validatehorario($request,$id);

             $input = [
                'dia' => $request['dia'],
                'ndia' => $ndia,
                'hora_ini' => $request['hora_ini'],
                'hora_fin' => $request['hora_fin'],
                'id_doctor' => $id,

                'ip_creacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            
            ];

            Horario_Doctor::create($input);

        }

        
        
        return redirect()->intended('/agenda');  
    }

    public function crearnuevo($start, $end)
    {
          
    }
    public function eliminar($id)
    {
        Horario_Doctor::where('id', $id)->delete();
         return "Se ha eliminado un horario";
    }

    public function index_admin()
    {
        $users = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->orderBy('tipo_documento','asc')->paginate(10); //3=DOCTORES
        $tipousuarios=TipoUsuario::all();

        $rolUsuario = Auth::user()->id_tipo_usuario;      
        if($this->rol()){
            return response()->view('errors.404');
        }

        return view('horario_admin/index', ['users' => $users, 'tipousuarios' => $tipousuarios]);
    }

    public function index_admin_ingreso($id)
    {
        if($this->rol()){
            return response()->view('errors.404');
        }
        $idusuario = $id;
        $usuario = User::find($idusuario);
       /* $horarios = Horario_Doctor::where('id_doctor',$idusuario)->orderBy('ndia', 'asc')->get();

       
       return view('horario/index', ['id' => $idusuario, 'usuario' => $usuario, 'horarios' => $horarios]);*/
       
       $horarios = Horario_Doctor::where('id_doctor',$idusuario)->orderBy('ndia', 'asc')->get();
       $horarios1 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',1)->orderBy('ndia', 'asc')->get();
       $horarios2 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',2)->orderBy('ndia', 'asc')->get();
       $horarios3 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',3)->orderBy('ndia', 'asc')->get();
       $horarios4 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',4)->orderBy('ndia', 'asc')->get();
       $horarios5 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',5)->orderBy('ndia', 'asc')->get();
       $horarios6 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',6)->orderBy('ndia', 'asc')->get();
       $horarios7 = Horario_Doctor::where('id_doctor',$idusuario)->where('ndia',7)->orderBy('ndia', 'asc')->get();

       $cantidad=$horarios->count();
       $c[0]=$horarios1->count();
       $c[1]=$horarios2->count();
       $c[2]=$horarios3->count();
       $c[3]=$horarios4->count();
       $c[4]=$horarios5->count();
       $c[5]=$horarios6->count();
       $c[6]=$horarios7->count();

       $diaInicio="Monday";
        $diaFin="Sunday";

        $fecha = date('Y-m-d');
        $strFecha = strtotime($fecha);

        $fechaInicio = date('Y-m-d',strtotime('last '.$diaInicio,$strFecha));
        $fechaFin = date('Y-m-d',strtotime('next '.$diaFin,$strFecha));

        if(date("l",$strFecha)==$diaInicio){
            $fechaInicio= date("Y-m-d",$strFecha);
        }
        if(date("l",$strFecha)==$diaFin){
            $fechaFin= date("Y-m-d",$strFecha);
        }
        $fechaInicio= $fechaInicio.' 00:00';
        $fechaFin= $fechaFin.' 23:59';
        $extra =  Excepcion_Horario::where('id_doctor1', $idusuario)->whereBetween('inicio', [$fechaInicio, $fechaFin])->get();


       return view('horario_admin/index_ingreso', ['id' => $idusuario, 'usuario' => $usuario, 'horarios1' => $horarios1, 'horarios2' => $horarios2, 'horarios3' => $horarios3, 'horarios4' => $horarios4, 'horarios5' => $horarios5,'horarios6' => $horarios6,'horarios7' => $horarios7,'horarios' => $horarios, 'c' => $c, 'cantidad' => $cantidad, 'extra' => $extra]);
    }

    private function validatehorario(Request $request, $id_doctor)
    {
        

        $ini2 = date_create($request['hora_ini']);
        $fin2 = date_create($request['hora_fin']);
        
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));

        $inicio = date_format($inicio, 'H:i:s');
        $fin = date_format($fin, 'H:i:s');
         
        $dato = Horario_Doctor::where('id_doctor',$id_doctor)->where('dia', $request['dia'])
                ->where(function ($query) use ($request, $inicio, $fin) {
                            return $query->whereRaw("(('".$inicio."' BETWEEN hora_ini and hora_fin)")
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("'".$fin."' BETWEEN hora_ini and hora_fin)");}
                                )                  
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("(hora_ini BETWEEN '".$inicio."' and '".$fin."'");
                               })
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("hora_fin BETWEEN '".$inicio."' and '".$fin."')");
                               });
                            })
                ->where(function ($query) {
                    return $query->where('estado', 1);
                })
                ->get();        

        $cantidad = $dato->count();



        $reglas = [
                    'hora_ini' => 'comparahoras:'.$request['hora_fin'],
                    'hora_fin' => 'comparahoras:'.$request['hora_ini'],
                    'dia' => 'unique_doctor:'.$cantidad,
                    
            ];

        
        $mensajes = [
            'hora_ini.comparahoras' => 'Hora de Inicio debe ser menor a hora de Fin.',
            'hora_fin.comparahoras' => 'Hora de Fin debe ser mayor a hora de Inicio.',
            'dia.unique_doctor' => 'El rango de Horario ya se encuentra incluido .',
             ];

        $this->validate($request, $reglas, $mensajes); 
        
    }

    private function validatehorario3(Request $request, $id_doctor, $dia)
    {
        

        $ini2 = date_create($request['hora_ini']);
        $fin2 = date_create($request['hora_fin']);
        
        $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));

        $inicio = date_format($inicio, 'H:i:s');
        $fin = date_format($fin, 'H:i:s');
         
        $dato = Horario_Doctor::where('id_doctor',$id_doctor)->where('dia', $dia)
                ->where(function ($query) use ($request, $inicio, $fin) {
                            return $query->whereRaw("(('".$inicio."' BETWEEN hora_ini and hora_fin)")
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("'".$fin."' BETWEEN hora_ini and hora_fin)");}
                                )                  
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("(hora_ini BETWEEN '".$inicio."' and '".$fin."'");
                               })
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("hora_fin BETWEEN '".$inicio."' and '".$fin."')");
                               });
                            })
                ->where(function ($query) {
                    return $query->where('estado', 1);
                })
                ->get();        

        $cantidad = $dato->count();



        $reglas = [
                    'hora_ini' => 'comparahoras:'.$request['hora_fin'],
                    'hora_fin' => 'comparahoras:'.$request['hora_ini'],
                    'dia' => 'unique_doctor:'.$cantidad,
                    
            ];

        
        $mensajes = [
            'hora_ini.comparahoras' => 'Hora de Inicio debe ser menor a hora de Fin.',
            'hora_fin.comparahoras' => 'Hora de Fin debe ser mayor a hora de Inicio.',
            'dia.unique_doctor' => 'El rango de Horario ya se encuentra incluido .',
             ];

        $this->validate($request, $reglas, $mensajes); 
        
    }

    public function editahorario(Request $request, $id)
    {
        
        
        $user = User::findOrFail($id);
        $ip_cliente= $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $horarios = Horario_Doctor::where('id_doctor',$id)->get();
       /* $reglas = ['dia' => 'required|in:LU,MA,MI,JU,VI,SA,DO,TD',
                    'hora_ini' => 'required',
                    'hora_fin' => 'required'
            ];
        
        $mensajes = [
            'dia.required' => 'Selecciona el día.',
            'dia.in' => 'Selecciona el día correcto.',
            'hora_ini.required' => 'Selecciona la hora de inicio.',
            'hora_fin.required' => 'Selecciona la hora de fin.',
             ];

        $this->validate($request, $reglas, $mensajes); */

        if(!is_null($horarios)){
            foreach($horarios as $horario){

                $this->validatehorario2($request, $horario->id, $horario->id_doctor, $horario->dia);
                
                
                $estado=$request['estado'.$horario->id];
                if(is_null($estado)){
                    $estado=0;
                }

                $input = [
                'hora_ini' => $request['hora_ini'.$horario->id],
                'hora_fin' => $request['hora_fin'.$horario->id],
                'estado' => $estado,

                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            
                ];
                
                Horario_doctor::find($request['hid'.$horario->id])->update($input);       
            } 
             
        }
        

        
        
        return redirect()->intended('/agenda');
        
    }

    private function validatehorario2(Request $request, $hid, $id_doctor, $dia)
    {
        
        if($request['estado'.$hid]==1){
            $ini2 = date_create($request['hora_ini'.$hid]);
            $fin2 = date_create($request['hora_fin'.$hid]);
        
            $inicio  =  date_add($ini2, date_interval_create_from_date_string('1 seconds'));
            $fin  =  date_sub($fin2, date_interval_create_from_date_string('1 seconds'));

            $inicio = date_format($inicio, 'H:i:s');
            $fin = date_format($fin, 'H:i:s');
         
            $dato = Horario_Doctor::where('id_doctor',$id_doctor)->where('dia', $dia)->where('id','<>',$hid)
                ->where(function ($query) use ($request, $inicio, $fin) {
                            return $query->whereRaw("(('".$inicio."' BETWEEN hora_ini and hora_fin)")
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("'".$fin."' BETWEEN hora_ini and hora_fin)");}
                                )                  
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("(hora_ini BETWEEN '".$inicio."' and '".$fin."'");
                               })
                                ->orWhere(function ($query) use ($request, $inicio, $fin){
                                 $query->whereRaw("hora_fin BETWEEN '".$inicio."' and '".$fin."')");
                               });
                            })
                ->where(function ($query) {
                    return $query->where('estado', 1);
                })
                ->get();        

            $cantidad = $dato->count();
            


        }
        else{ $cantidad=0; }
                

        $reglas = [
                    'hora_ini'.$hid => 'comparahoras:'.$request['hora_fin'.$hid].'|unique_doctor:'.$cantidad,
                    'hora_fin'.$hid => 'comparahoras:'.$request['hora_ini'.$hid].'|unique_doctor:'.$cantidad,                              
            ];

        
        $mensajes = [
            'hora_ini'.$hid.'.comparahoras' => 'Hora de Inicio debe ser menor a hora de Fin.',
             'hora_fin'.$hid.'.comparahoras' => 'Hora de Fin debe ser mayor a hora de Inicio.',
             'hora_ini'.$hid.'.unique_doctor' => 'El rango de Horario ya se encuentra incluido .',
             'hora_fin'.$hid.'.unique_doctor' => 'El rango de Horario ya se encuentra incluido .'
             ];


           $this->validate($request, $reglas, $mensajes);  
        
        
    }

    public function valida_horarioxdoctor_dia($request){

        $id_doctor = $request['id_doctor1'];
        $fechaini = $request['inicio'];
        $fechafin = $request['fin'];

        $ndiaini = $this->saber_dia($fechaini);
        $horaini = date('H:i:s',strtotime($fechaini));

        $ndiafin = $this->saber_dia($fechafin);
        $horafin = date('H:i:s',strtotime($fechafin));

        $inicio = date('Y-m-d H:i:s',strtotime($fechaini));
        $final = date('Y-m-d H:i:s',strtotime($fechafin));

        
        
       
        $cantidad_ini = Horario_Doctor::where('id_doctor',$id_doctor)->where('ndia',$ndiaini)->where('estado','1')->where('hora_ini','<=',$horaini)->where('hora_fin','>=',$horaini)->count();

        $cantidad_fin = Horario_Doctor::where('id_doctor',$id_doctor)->where('ndia',$ndiafin)->where('estado','1')->where('hora_ini','<=',$horafin)->where('hora_fin','>=',$horafin)->count();


        if($cantidad_ini == 0){
            $cantidad_ini = Excepcion_Horario::where('id_doctor1',$id_doctor)->where('inicio','<=',$inicio)->where('fin','>=',$inicio)->count();
        }

        if($cantidad_fin == 0){
            $cantidad_fin = Excepcion_Horario::where('id_doctor1',$id_doctor)->where('inicio','<=',$final)->where('fin','>=',$final)->count();    
        }
      

        $reglas=['inicio' => 'comparamayor:0,'.$cantidad_ini,
                'fin' => 'comparamayor:0,'.$cantidad_fin
                    ];
        $mensajes=['inicio.comparamayor'=>'fecha de inicio esta fuera del horario laborable del Doctor',
                    'fin.comparamayor'=>'fecha de fin esta fuera del horario laborable del Doctor'
                    ];            

        $this->validate($request,$reglas,$mensajes);  
         
    }
    // 10/10/2018 NUEVA VALIDACION PARA LOS DESPLAZAMIENTOS RAPIDOS
    public function valida_horarioxdoctor_dia_2($id_doctor, $fechaini, $fechafin){

        /*$id_doctor = $request['id_doctor1'];
        $fechaini = $request['inicio'];
        $fechafin = $request['fin'];*/

        $ndiaini = $this->saber_dia($fechaini);
        $horaini = date('H:i:s',strtotime($fechaini));

        $ndiafin = $this->saber_dia($fechafin);
        $horafin = date('H:i:s',strtotime($fechafin));

        $inicio = date('Y-m-d H:i:s',strtotime($fechaini));
        $final = date('Y-m-d H:i:s',strtotime($fechafin));

        
        
       
        $cantidad_ini = Horario_Doctor::where('id_doctor',$id_doctor)->where('ndia',$ndiaini)->where('estado','1')->where('hora_ini','<=',$horaini)->where('hora_fin','>=',$horaini)->count();


        $cantidad_fin = Horario_Doctor::where('id_doctor',$id_doctor)->where('ndia',$ndiafin)->where('estado','1')->where('hora_ini','<=',$horafin)->where('hora_fin','>=',$horafin)->count();


        if($cantidad_ini == 0){
            $cantidad_ini = Excepcion_Horario::where('id_doctor1',$id_doctor)->where('inicio','<=',$inicio)->where('fin','>=',$inicio)->count();
            if($cantidad_ini==0){
                return "INI";
            }
        }

        if($cantidad_fin == 0){
            $cantidad_fin = Excepcion_Horario::where('id_doctor1',$id_doctor)->where('inicio','<=',$final)->where('fin','>=',$final)->count();
            if($cantidad_fin==0){
                return "FIN";
            }    
        }
       
        return "OK";     
    }

    public function saber_dia($fecha) {

        $dias = array('0','1','2','3','4','5','6','7');//12/1/2018

        $nombre_dia = $dias[date('N', strtotime($fecha))];

        return $nombre_dia;
    }


    public function dato_agregar($start, $end){
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

        return view('horario/agregar', ['start' => $start, 'end' => $end, 'inicio' => $inicio2, 'fin' => $fin2]); 
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

        return view('horario_admin/agregar', ['start' => $start, 'end' => $end, 'inicio' => $inicio2, 'fin' => $fin2,'id' => $id]); 
    }

    public function agregarmodal(Request $request){
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
            'id_doctor' => $idusuario,
            'tipo' => $request['tipo'],
            'ip_creacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
        ];

        $id_horario = Horario_Doctor::insertGetId($input);

        return redirect()->intended('/horario');
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
            'id_doctor' => $request['id_doctor'],
            'tipo' => $request['tipo'],
            'ip_creacion' => $ip_cliente,
            'id_usuariocrea' => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
        ];

        $id_horario = Horario_Doctor::insertGetId($input);

        return back();
    }

    public function ValidaHorarioConsulta($request){

        
        $id_doctor = $request['id_doctor1'];
        $fechaini = $request['inicio'];
        $fechafin = $request['fin'];

        $ndiaini = $this->saber_dia($fechaini);
        $horaini = date('H:i:s',strtotime($fechaini));

        $ndiafin = $this->saber_dia($fechafin);
        $horafin = date('H:i:s',strtotime($fechafin));

        $inicio = date('Y-m-d H:i:s',strtotime($fechaini));
        $final = date('Y-m-d H:i:s',strtotime($fechafin));

        
        $cantidad_ini = Horario_Doctor::where('id_doctor',$id_doctor)->where('ndia',$ndiaini)->where('estado','1')->where('hora_ini','<=',$horaini)->where('hora_fin','>=',$horaini)->where('tipo','1')->count();
        

        $cantidad_fin = Horario_Doctor::where('id_doctor',$id_doctor)->where('ndia',$ndiafin)->where('estado','1')->where('hora_ini','<=',$horafin)->where('hora_fin','>=',$horafin)->where('tipo','1')->count();
        //dd($cantidad_ini,$cantidad_fin);

        if($cantidad_ini == 0){
            $cantidad_ini = Excepcion_Horario::where('id_doctor1',$id_doctor)->where('inicio','<=',$inicio)->where('fin','>=',$inicio)->count();
        }

        if($cantidad_fin == 0){
            $cantidad_fin = Excepcion_Horario::where('id_doctor1',$id_doctor)->where('inicio','<=',$final)->where('fin','>=',$final)->count();    
        }
      

        $reglas=['inicio' => 'comparamayor:0,'.$cantidad_ini,
                'fin' => 'comparamayor:0,'.$cantidad_fin
                    ];
        $mensajes=['inicio.comparamayor'=>'fecha de inicio esta fuera del horario de Consultas del Doctor',
                    'fin.comparamayor'=>'fecha de fin esta fuera del horario de Consultas del Doctor'
                    ];            

        $this->validate($request,$reglas,$mensajes);  
             
     

    }
}
