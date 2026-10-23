<?php
namespace Sis_medico\Http\Controllers;

use Cookie;
use DateInterval;
use DatePeriod;
use DateTime;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Agenda_archivo;
use Sis_medico\Agenda_Permiso;
use Sis_medico\Bloqueo_Sala;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Empresa;
use Sis_medico\Examen_Obligatorio;
use Sis_medico\Historiaclinica;
use Sis_medico\Horario_doctor;
use Sis_medico\Log_Agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Max_Procedimiento;
use Sis_medico\Orden;
use Sis_medico\Orden_Procedimiento;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Observaciones;
use Sis_medico\Procedimiento;
use Sis_medico\Sala;
use Sis_medico\Seguro;
use Sis_medico\Tipousuario;
use Sis_medico\User;
use Sis_medico\Triaje_Telefonico;
use Storage;

date_default_timezone_set('America/Guayaquil');

class PreAgendaController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/preagenda';

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
        if (in_array($rolUsuario, array(1, 4, 5, 20)) == false) {
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
        $users        = User::where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->paginate(5); //3=DOCTORES
        $tipousuarios = tipousuario::all();

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        return view('agenda/index', ['users' => $users, 'tipousuarios' => $tipousuarios]);
    }

    public function pentax(Request $request)
    {
        //dd($id);
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //dd($request->all());
        //solo para pentax id_hospital 2
        $salas = DB::table('sala')->where('id_hospital', '=', '2')->Where('proc_consul_sala', '1')->get();

        //($salas);
        $salas_unica = Sala::where('id_hospital', '=', '2')->Where('proc_consul_sala', '1')->get();
        //  ->where('id',$id )->get();
        //dd($salas_unica);
        $i        = 0;
        $fecha    = $request['fecha'];
        $sel_sala = $request['sel_sala'];

        if ($fecha == "") {
            $fecha = date('Y-m-d');

        } else {
            $valoresfecha = explode("/", $fecha);
            $fecha        = "";
            $j            = 0;

            foreach ($valoresfecha as $value) {

                if ($j < 2) {
                    $fecha = $fecha . $value . '-';

                } else {
                    $fecha = $fecha . $value;
                }
                $j = $j + 1;
            };
            if ($j == 1) {
                $fecha = substr($fecha, 0, -1);
                $fecha = date('Y-m-d', $fecha);

            }
        }

        $dia = date('w', strtotime($fecha));
        if ($dia == '0') {
            $fechainicio = strtotime('-6 days', strtotime($fecha));
            $fechainicio = date('Y-m-d', $fechainicio);
            //$fechafinal = $fecha;
            $fechafinal = strtotime('+1 days', strtotime($fecha));
            $fechafinal = date('Y-m-d', $fechafinal);
        }

        if ($dia == '1') {
            $fechainicio = $fecha;
            $fechafinal  = strtotime('+6 days', strtotime($fecha));
            $fechafinal  = date('Y-m-d', $fechafinal);
        }
        if ($dia == '2') {
            $fechainicio = strtotime('-1 days', strtotime($fecha));
            $fechainicio = date('Y-m-d', $fechainicio);
            $fechafinal  = strtotime('+5 days', strtotime($fecha));
            $fechafinal  = date('Y-m-d', $fechafinal);
        }
        if ($dia == '3') {
            $fechainicio = strtotime('-2 days', strtotime($fecha));
            $fechainicio = date('Y-m-d', $fechainicio);
            $fechafinal  = strtotime('+4 days', strtotime($fecha));
            $fechafinal  = date('Y-m-d', $fechafinal);
        }
        if ($dia == '4') {
            $fechainicio = strtotime('-3 days', strtotime($fecha));
            $fechainicio = date('Y-m-d', $fechainicio);
            $fechafinal  = strtotime('+3 days', strtotime($fecha));
            $fechafinal  = date('Y-m-d', $fechafinal);
        }
        if ($dia == '5') {
            $fechainicio = strtotime('-4 days', strtotime($fecha));
            $fechainicio = date('Y-m-d', $fechainicio);
            $fechafinal  = strtotime('+2 days', strtotime($fecha));
            $fechafinal  = date('Y-m-d', $fechafinal);
        }
        if ($dia == '6') {
            $fechainicio = strtotime('-5 days', strtotime($fecha));
            $fechainicio = date('Y-m-d', $fechainicio);
            $fechafinal  = strtotime('+1 days', strtotime($fecha));
            $fechafinal  = date('Y-m-d', $fechafinal);
        }
        //return $fechainicio.' // '.$fechafinal;
        Cookie::queue('ruta_p', 'normal', '1000');
        foreach ($salas as $value) {
            $idsala = $value->id;
            $valor  = DB::table('agenda')
                ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
                ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
                ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
                ->leftjoin('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
                ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'paciente.ciudad')
                ->where('proc_consul', '=', 1)
                ->where('id_sala', '=', $idsala)
                ->where('agenda.estado', '!=', '0')
                ->whereBetween('fechaini', [$fechainicio, $fechafinal])->get();
            //dd($valor);
            $salas_encontradas[$i] = $value;
            $arreglo[$i]           = $valor;
            $salas_blq_rec = Bloqueo_Sala::where('estado', 1)->where('tipo', 0)->where('ndia', $dia)->where('id_sala', $value->id)->get();
            $arr_rec[$i]   = $salas_blq_rec;

            $salas_blq_dia = Bloqueo_Sala::where('estado', 1)->where('tipo', 1)->where('id_sala', $value->id)->whereBetween('fecha_ini', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->get();

            $arr_dia[$i] = $salas_blq_dia;
            //oggi
            $i = $i + 1;
        }
        //dd($sel_sala);
        //SEMAFORO
        $sem_controller = new laboratorio\SemaforoController;
        //$pentax_pend    = $sem_controller->Cargar_pendientes(date('Y-m-d'));
        $pentax_pend    = [];
        //dd($pentax_pend);
        //SEMAFORO
        //dd($reuniones);
        $reuniones_sin_id = null;
        $reuniones = DB::table('agenda as a')->where('a.estado', 1)->where('a.proc_consul', 2)->whereBetween('a.fechaini', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->join('users as d', 'd.id', 'a.id_doctor1')->join('users as uc', 'uc.id', 'a.id_usuariocrea')->join('users as um', 'um.id', 'a.id_usuariomod')->select('a.*', 'd.nombre1 as dnombre', 'd.apellido1 as dapellido', 'uc.nombre1 as ucnombre1', 'uc.apellido1 as ucapellido1', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->orderBy('a.fechaini')->get();
        //return $fecha;
        $salas_blq_rec = Bloqueo_Sala::where('estado', 1)->where('tipo', 0)->where('ndia', $dia)->get();

        $salas_blq_dia = Bloqueo_Sala::where('estado', 1)->where('tipo', 1)->whereBetween('fecha_ini', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->get();
        //return $arr_rec;

        //No se puede obtener el Id del Doctor
        /*$horario = DB::table('horario_doctor')
        ->where('id_doctor', '=', $id)->orderBy('ndia')
        ->orderBy('hora_ini')
        ->get();*/
        return view('preagenda/salas', ['salas_encontradas' => $salas_encontradas, 'calendarios' => $arreglo, 'fecha' => $fecha, 'sel_sala' => $sel_sala, 'reuniones' => $reuniones, 'pentax_pend' => $pentax_pend, 'arr_rec' => $arr_rec, 'arr_dia' => $arr_dia, 'salas_unica' => $salas_unica,'reuniones_sin_id'=>$reuniones_sin_id]);
    }

    /*public function cambiarhorario($id, $start, $end){

    //date_default_timezone_set('America/Guayaquil');
    $ip_cliente= $_SERVER["REMOTE_ADDR"];
    $idusuario = Auth::user()->id;
    $agendamiento = Agenda::find($id);
    $id_sala = $agendamiento->id_sala;
    $start =  substr($start, 0,10);
    $end =  substr($end, 0,10);
    date_default_timezone_set('UTC');
    $start2 = date('Y-m-d H:i', $start);
    $end2 = date('Y-m-d H:i', $end);
    $terminar = strtotime ( '-1 minute' , strtotime ( $end2));
    $terminar = date ( 'Y-m-d H:i' , $terminar);
    $nro_reagenda = $agendamiento->nro_reagenda;
    $agenda_nueva = DB::select("SELECT *
    FROM agenda
    WHERE id_sala = ".$id_sala." AND (
    fechaini BETWEEN '".$start2."' AND '".$terminar  ."') AND id !=  ".$id."  AND  estado != 0 AND  estado_cita != 2 AND id !=  0 ORDER BY fechaini ASC;");
    $id_doctor = $agendamiento->id_doctor1;
    $valida = DB::select("SELECT *
    FROM agenda
    WHERE proc_consul = 0 AND (
    fechaini BETWEEN '".$start2."' AND '".$terminar  ."') AND id !=  ".$id." AND  estado != 0 AND id_doctor1 =  '".$id_doctor."'  AND id !=  0 ORDER BY fechaini ASC;");

    $cuenta_validacion =  count($valida);
    //validacion si posee una reunion
    $valida_reunion = DB::select("SELECT *
    FROM agenda
    WHERE proc_consul = 2 AND (
    fechaini BETWEEN '".$start2."' AND '".$terminar."') AND id !=  ".$id."  AND  estado != 0 AND id_doctor1 =  '".$id_doctor."'  AND id !=  0 ORDER BY fechaini ASC;");
    $cuenta_validacion_reunion =  count($valida_reunion);
    if( $cuenta_validacion_reunion > 0)
    {
    return "Doctor ya posee una reunion en el horario";
    }
    $valida2 = DB::select("SELECT *
    FROM agenda
    WHERE (
    fechaini BETWEEN '".$start2."' AND '".$terminar  ."') AND id !=  ".$id." AND estado_cita >= 4 AND  estado != 0 AND id !=  0 ORDER BY fechaini ASC;");
    $cuenta_validacion2 =  count($valida2);
    if( $cuenta_validacion2 != 0)
    {
    return "Paciente ya admisionado, no puede reagendar";
    }

    if( $cuenta_validacion != 0)
    {
    return "Doctor ya posee consulta en ese horario";
    }
    date_default_timezone_set('America/Guayaquil');
    $input = [
    'nro_reagenda' => $nro_reagenda,
    'id_usuariomod' => $idusuario,
    'ip_modificacion' => $ip_cliente,
    'fechaini' => $start2,
    'fechafin' => $end2,
    ];
    Agenda::where('id', $id)
    ->update($input);
    //falta log de agenda
    Log_agenda::create([
    'id_agenda' => $agendamiento->id,
    'estado_cita_ant' => $agendamiento->estado_cita,
    'fechaini_ant' => $agendamiento->fechaini,
    'fechafin_ant' => $agendamiento->fechafin,
    'estado_ant' => $agendamiento->estado,
    'cortesia_ant' => $agendamiento->cortesia,
    'observaciones_ant' => $agendamiento->observaciones,
    'id_doctor1_ant' => $agendamiento->id_doctor1,
    'id_doctor2_ant' => $agendamiento->id_doctor2,
    'id_doctor3_ant' => $agendamiento->id_doctor3,
    'id_sala_ant' => $agendamiento->id_sala,

    'estado_cita' => $agendamiento->estado_cita,
    'fechaini' => $start2,
    'fechafin' => $end2,
    'estado' => $agendamiento->estado,
    'cortesia' => $agendamiento->cortesia,
    'observaciones' => "DESPLAZAMIENTO RÁPIDO",
    'id_doctor1' => $agendamiento->id_doctor1,
    'id_doctor2' => $agendamiento->id_doctor2,
    'id_doctor3' => $agendamiento->id_doctor3,
    'id_sala' => $agendamiento->id_sala,

    'descripcion' => "DESPLAZAMIENTO RÁPIDO",
    'descripcion2' => "",
    'descripcion3' => "",
    'campos_ant' => "",
    'campos' => "",
    'id_usuarioconfirma' => $agendamiento->id_usuarioconfirma,

    'id_usuariomod' => $idusuario,
    'id_usuariocrea' => $idusuario,
    'ip_modificacion' => $ip_cliente,
    'ip_creacion' => $ip_cliente,
    ]);

    $cuenta = count($agenda_nueva);
    if( $cuenta != 0){
    do{

    $tfin =  strtotime($agenda_nueva[0]->fechafin);
    $tinicio =  strtotime($agenda_nueva[0]->fechaini);
    $tiempo =  $tfin - $tinicio;
    $tiempo = $tiempo;
    $start2 =  $end2;
    $end2 = strtotime ( '+'.$tiempo.' seconds' , strtotime ( $end2));
    $end2 = date('Y-m-d H:i', $end2);
    $terminar = strtotime ( '-1 minute' , strtotime ( $end2));
    $terminar = date ( 'Y-m-d H:i' , $terminar);

    $id_nuevo = $agenda_nueva[0]->id;

    $id_doctor = $agenda_nueva[0]->id_doctor1;
    //validacion si posee una consulta
    $valida = DB::select("SELECT *
    FROM agenda
    WHERE proc_consul = 0 AND (
    fechaini BETWEEN '".$start2."' AND '".$terminar."') AND id !=  ".$id_nuevo."  AND  estado != 0 AND id_doctor1 =  '".$id_doctor."'  AND id !=  0 ORDER BY fechaini ASC;");

    $cuenta_validacion =  count($valida);

    if( $cuenta_validacion > 0)
    {
    return "Doctor ya posee consulta en ese horario";
    }
    //validacion si posee una reunion
    $valida_reunion = DB::select("SELECT *
    FROM agenda
    WHERE proc_consul = 2 AND (
    fechaini BETWEEN '".$start2."' AND '".$terminar."') AND id !=  ".$id_nuevo."  AND  estado != 0 AND id_doctor1 =  '".$id_doctor."'  AND id !=  0 ORDER BY fechaini ASC;");
    $cuenta_validacion_reunion =  count($valida_reunion);
    if( $cuenta_validacion_reunion > 0)
    {
    return "Doctor ya posee una reunion en el horario";
    }
    $valida2 = DB::select("SELECT *
    FROM agenda
    WHERE (
    fechaini BETWEEN '".$start2."' AND '".$terminar  ."') AND id !=  ".$id_nuevo." AND estado_cita >= 4 AND  estado != 0 AND id !=  0 ORDER BY fechaini ASC;");
    $cuenta_validacion2 =  count($valida2);
    if( $cuenta_validacion2 != 0)
    {
    return "Paciente ya admisionado, no puede reagendar";
    }

    $input = [
    'nro_reagenda' => $nro_reagenda,
    'id_usuariomod' => $idusuario,
    'ip_modificacion' => $ip_cliente,
    'fechaini' => $start2,
    'fechafin' => $end2,
    ];
    Agenda::where('id', $id_nuevo)
    ->update($input);

    //falta log de agenda
    Log_agenda::create([
    'id_agenda' => $agenda_nueva[0]->id,
    'estado_cita_ant' => $agenda_nueva[0]->estado_cita,
    'fechaini_ant' => $agenda_nueva[0]->fechaini,
    'fechafin_ant' => $agenda_nueva[0]->fechafin,
    'estado_ant' => $agenda_nueva[0]->estado,
    'cortesia_ant' => $agenda_nueva[0]->cortesia,
    'observaciones_ant' => $agenda_nueva[0]->observaciones,
    'id_doctor1_ant' => $agenda_nueva[0]->id_doctor1,
    'id_doctor2_ant' => $agenda_nueva[0]->id_doctor2,
    'id_doctor3_ant' => $agenda_nueva[0]->id_doctor3,
    'id_sala_ant' => $agenda_nueva[0]->id_sala,

    'estado_cita' => $agenda_nueva[0]->estado_cita,
    'fechaini' => $start2,
    'fechafin' => $end2,
    'estado' => $agenda_nueva[0]->estado,
    'cortesia' => $agenda_nueva[0]->cortesia,
    'observaciones' => $agenda_nueva[0]->observaciones,
    'id_doctor1' => $agenda_nueva[0]->id_doctor1,
    'id_doctor2' => $agenda_nueva[0]->id_doctor2,
    'id_doctor3' => $agenda_nueva[0]->id_doctor3,
    'id_sala' => $agenda_nueva[0]->id_sala,

    'descripcion' => "DESPLAZAMIENTO RÁPIDO",
    'descripcion2' => "",
    'descripcion3' => "",
    'campos_ant' => "",
    'campos' => "",
    'id_usuarioconfirma' => $agenda_nueva[0]->id_usuarioconfirma,

    'id_usuariomod' => $idusuario,
    'id_usuariocrea' => $idusuario,
    'ip_modificacion' => $ip_cliente,
    'ip_creacion' => $ip_cliente,
    ]);
    $agenda_nueva = DB::select("SELECT *
    FROM agenda
    WHERE id_sala = ".$id_sala." AND (
    fechaini BETWEEN '".$start2."' AND '".$terminar  ."') AND id !=  ".$id_nuevo." AND  estado != 0 AND  estado_cita != 2 AND id !=  0 ORDER BY fechaini ASC;");
    $cuenta = count($agenda_nueva);
    }while($cuenta != 0);
    }
    return "Proceso completado correctamente";  9/10/2018 CAMBIOS CON BLOQUEO DE AGENDA
    }*/

    public function cambiarhorario($id, $start, $end)
    {

        $new_val = $this->Movimientos_Permitidos($id, $start, $end);

        if ($new_val != "OK") {
            return $new_val;
        }

        //date_default_timezone_set('America/Guayaquil');
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $agendamiento = Agenda::find($id);
        $id_sala      = $agendamiento->id_sala;
        $start        = substr($start, 0, 10);
        $end          = substr($end, 0, 10);
        date_default_timezone_set('UTC');
        $start2       = date('Y-m-d H:i', $start);
        $end2         = date('Y-m-d H:i', $end);
        $terminar     = strtotime('-1 minute', strtotime($end2));
        $terminar     = date('Y-m-d H:i', $terminar);
        $nro_reagenda = $agendamiento->nro_reagenda;
        $agenda_nueva = DB::select("SELECT *
            FROM agenda
            WHERE id_sala = " . $id_sala . " AND (
            fechaini BETWEEN '" . $start2 . "' AND '" . $terminar . "') AND id !=  " . $id . "  AND  estado != 0 AND  estado_cita != 2 AND id !=  0 ORDER BY fechaini ASC;");
        $id_doctor = $agendamiento->id_doctor1;
        /*$valida = DB::select("SELECT *
        FROM agenda
        WHERE proc_consul = 0 AND (
        fechaini BETWEEN '".$start2."' AND '".$terminar  ."') AND id !=  ".$id." AND  estado != 0 AND id_doctor1 =  '".$id_doctor."'  AND id !=  0 ORDER BY fechaini ASC;");

        $cuenta_validacion =  count($valida);*/
        //validacion si posee una reunion
        /*$valida_reunion = DB::select("SELECT *
        FROM agenda
        WHERE proc_consul = 2 AND (
        fechaini BETWEEN '".$start2."' AND '".$terminar."') AND id !=  ".$id."  AND  estado != 0 AND id_doctor1 =  '".$id_doctor."'  AND id !=  0 ORDER BY fechaini ASC;");
        $cuenta_validacion_reunion =  count($valida_reunion);
        if( $cuenta_validacion_reunion > 0)
        {
        return "Doctor ya posee una reunion en el horario";
        }*/
        /*$valida2 = DB::select("SELECT *
        FROM agenda
        WHERE (
        fechaini BETWEEN '".$start2."' AND '".$terminar  ."') AND id !=  ".$id." AND estado_cita >= 4 AND  estado != 0 AND id !=  0 ORDER BY fechaini ASC;");
        $cuenta_validacion2 =  count($valida2);
        if( $cuenta_validacion2 != 0)
        {
        return "Paciente ya admisionado, no puede reagendar";
        }*/

        /*if( $cuenta_validacion != 0)
        {
        return "Doctor ya posee consulta en ese horario";
        }*/
        date_default_timezone_set('America/Guayaquil');
        $input = [
            'nro_reagenda'    => $nro_reagenda,
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'fechaini'        => $start2,
            'fechafin'        => $end2,
        ];
        Agenda::where('id', $id)
            ->update($input);
        //falta log de agenda
        Log_agenda::create([
            'id_agenda'          => $agendamiento->id,
            'estado_cita_ant'    => $agendamiento->estado_cita,
            'fechaini_ant'       => $agendamiento->fechaini,
            'fechafin_ant'       => $agendamiento->fechafin,
            'estado_ant'         => $agendamiento->estado,
            'cortesia_ant'       => $agendamiento->cortesia,
            'observaciones_ant'  => $agendamiento->observaciones,
            'id_doctor1_ant'     => $agendamiento->id_doctor1,
            'id_doctor2_ant'     => $agendamiento->id_doctor2,
            'id_doctor3_ant'     => $agendamiento->id_doctor3,
            'id_sala_ant'        => $agendamiento->id_sala,

            'estado_cita'        => $agendamiento->estado_cita,
            'fechaini'           => $start2,
            'fechafin'           => $end2,
            'estado'             => $agendamiento->estado,
            'cortesia'           => $agendamiento->cortesia,
            'observaciones'      => "DESPLAZAMIENTO RÁPIDO PROC",
            'id_doctor1'         => $agendamiento->id_doctor1,
            'id_doctor2'         => $agendamiento->id_doctor2,
            'id_doctor3'         => $agendamiento->id_doctor3,
            'id_sala'            => $agendamiento->id_sala,

            'descripcion'        => "DESPLAZAMIENTO RÁPIDO PROC",
            'descripcion2'       => "",
            'descripcion3'       => "",
            'campos_ant'         => "",
            'campos'             => "",
            'id_usuarioconfirma' => $agendamiento->id_usuarioconfirma,

            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,
        ]);

        $cuenta = count($agenda_nueva);
        if ($cuenta != 0) {
            do {

                $tfin     = strtotime($agenda_nueva[0]->fechafin);
                $tinicio  = strtotime($agenda_nueva[0]->fechaini);
                $tiempo   = $tfin - $tinicio;
                $tiempo   = $tiempo;
                $start2   = $end2;
                $end2     = strtotime('+' . $tiempo . ' seconds', strtotime($end2));
                $end2     = date('Y-m-d H:i', $end2);
                $terminar = strtotime('-1 minute', strtotime($end2));
                $terminar = date('Y-m-d H:i', $terminar);

                $id_nuevo = $agenda_nueva[0]->id;

                $id_doctor = $agenda_nueva[0]->id_doctor1;
                //validacion si posee una consulta
                /*$valida = DB::select("SELECT *
                FROM agenda
                WHERE proc_consul = 0 AND (
                fechaini BETWEEN '".$start2."' AND '".$terminar."') AND id !=  ".$id_nuevo."  AND  estado != 0 AND id_doctor1 =  '".$id_doctor."'  AND id !=  0 ORDER BY fechaini ASC;");

                $cuenta_validacion =  count($valida);

                if( $cuenta_validacion > 0)
                {
                return "Doctor ya posee consulta en ese horario";
                }*/
                //validacion si posee una reunion
                /*$valida_reunion = DB::select("SELECT *
                FROM agenda
                WHERE proc_consul = 2 AND (
                fechaini BETWEEN '".$start2."' AND '".$terminar."') AND id !=  ".$id_nuevo."  AND  estado != 0 AND id_doctor1 =  '".$id_doctor."'  AND id !=  0 ORDER BY fechaini ASC;");
                $cuenta_validacion_reunion =  count($valida_reunion);
                if( $cuenta_validacion_reunion > 0)
                {
                return "Doctor ya posee una reunion en el horario";
                }*/
                /*$valida2 = DB::select("SELECT *
                FROM agenda
                WHERE (
                fechaini BETWEEN '".$start2."' AND '".$terminar  ."') AND id !=  ".$id_nuevo." AND estado_cita >= 4 AND  estado != 0 AND id !=  0 ORDER BY fechaini ASC;");
                $cuenta_validacion2 =  count($valida2);
                if( $cuenta_validacion2 != 0)
                {
                return "Paciente ya admisionado, no puede reagendar";
                }*/

                $input = [
                    'nro_reagenda'    => $nro_reagenda,
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'fechaini'        => $start2,
                    'fechafin'        => $end2,
                ];
                Agenda::where('id', $id_nuevo)
                    ->update($input);

                //falta log de agenda
                Log_agenda::create([
                    'id_agenda'          => $agenda_nueva[0]->id,
                    'estado_cita_ant'    => $agenda_nueva[0]->estado_cita,
                    'fechaini_ant'       => $agenda_nueva[0]->fechaini,
                    'fechafin_ant'       => $agenda_nueva[0]->fechafin,
                    'estado_ant'         => $agenda_nueva[0]->estado,
                    'cortesia_ant'       => $agenda_nueva[0]->cortesia,
                    'observaciones_ant'  => $agenda_nueva[0]->observaciones,
                    'id_doctor1_ant'     => $agenda_nueva[0]->id_doctor1,
                    'id_doctor2_ant'     => $agenda_nueva[0]->id_doctor2,
                    'id_doctor3_ant'     => $agenda_nueva[0]->id_doctor3,
                    'id_sala_ant'        => $agenda_nueva[0]->id_sala,

                    'estado_cita'        => $agenda_nueva[0]->estado_cita,
                    'fechaini'           => $start2,
                    'fechafin'           => $end2,
                    'estado'             => $agenda_nueva[0]->estado,
                    'cortesia'           => $agenda_nueva[0]->cortesia,
                    'observaciones'      => "DESPLAZAMIENTO RÁPIDO PROC",
                    'id_doctor1'         => $agenda_nueva[0]->id_doctor1,
                    'id_doctor2'         => $agenda_nueva[0]->id_doctor2,
                    'id_doctor3'         => $agenda_nueva[0]->id_doctor3,
                    'id_sala'            => $agenda_nueva[0]->id_sala,

                    'descripcion'        => "DESPLAZAMIENTO RÁPIDO PROC",
                    'descripcion2'       => "",
                    'descripcion3'       => "",
                    'campos_ant'         => "",
                    'campos'             => "",
                    'id_usuarioconfirma' => $agenda_nueva[0]->id_usuarioconfirma,

                    'id_usuariomod'      => $idusuario,
                    'id_usuariocrea'     => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                    'ip_creacion'        => $ip_cliente,
                ]);
                $agenda_nueva = DB::select("SELECT *
                FROM agenda
                WHERE id_sala = " . $id_sala . " AND (
                fechaini BETWEEN '" . $start2 . "' AND '" . $terminar . "') AND id !=  " . $id_nuevo . " AND  estado != 0 AND  estado_cita != 2 AND id !=  0 ORDER BY fechaini ASC;");
                $cuenta = count($agenda_nueva);
            } while ($cuenta != 0);
        }
        return "Proceso completado correctamente";
    }

    public function Movimientos_Permitidos($id, $start, $end)
    {

        $agendamiento = Agenda::find($id);
        $id_sala      = $agendamiento->id_sala;
        $start        = substr($start, 0, 10);
        $end          = substr($end, 0, 10);
        date_default_timezone_set('UTC');
        $start2       = date('Y-m-d H:i', $start);
        $end2         = date('Y-m-d H:i', $end);
        $terminar     = strtotime('-1 minute', strtotime($end2));
        $terminar     = date('Y-m-d H:i', $terminar);
        $nro_reagenda = $agendamiento->nro_reagenda;
        $id_doctor    = $agendamiento->id_doctor1;

        //-------------------------------
        if ($id_doctor != null) {

            if ($id_doctor != '9666666666') {

                // VH: 10102018 VALIDA SI TIENE UNA CONSULTA
                $cant_consultas = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($start2, $terminar, $id_doctor) {
                    return $query->where('id_doctor1', '=', $id_doctor)
                        ->orWhere('id_doctor2', '=', $id_doctor)
                        ->orWhere('id_doctor3', '=', $id_doctor);
                })
                    ->where(function ($query) use ($start2, $terminar, $id_doctor) {
                        return $query->whereRaw("(('" . $start2 . "' BETWEEN fechaini and fechafin)")
                            ->orWhere(function ($query) use ($start2, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                            )
                            ->orWhere(function ($query) use ($start2, $terminar) {
                                $query->whereRaw("(fechaini BETWEEN '" . $start2 . "' and '" . $terminar . "'");
                            })
                            ->orWhere(function ($query) use ($start2, $terminar) {
                                $query->whereRaw("fechafin BETWEEN '" . $start2 . "' and '" . $terminar . "')");
                            });
                    })
                    ->where(function ($query) {
                        return $query->where('estado', '<>', '0')->where('proc_consul', '0');
                    })
                    ->count();

                if ($cant_consultas > 0) {
                    return "Doctor posee " . $cant_consultas . " consulta(s)";
                }

                // VH: 10102018 VALIDA SI TIENE UNA REUNION
                $cant_reuniones = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($start2, $terminar, $id_doctor) {
                    return $query->where('id_doctor1', '=', $id_doctor)
                        ->orWhere('id_doctor2', '=', $id_doctor)
                        ->orWhere('id_doctor3', '=', $id_doctor);
                })
                    ->where(function ($query) use ($start2, $terminar, $id_doctor) {
                        return $query->whereRaw("(('" . $start2 . "' BETWEEN fechaini and fechafin)")
                            ->orWhere(function ($query) use ($start2, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                            )
                            ->orWhere(function ($query) use ($start2, $terminar) {
                                $query->whereRaw("(fechaini BETWEEN '" . $start2 . "' and '" . $terminar . "'");
                            })
                            ->orWhere(function ($query) use ($start2, $terminar) {
                                $query->whereRaw("fechafin BETWEEN '" . $start2 . "' and '" . $terminar . "')");
                            });
                    })
                    ->where(function ($query) {
                        return $query->where('estado', '<>', '0')->where('proc_consul', '2');
                    })
                    ->count();

                if ($cant_reuniones > 0) {
                    return "Doctor posee " . $cant_reuniones . " reunion(es)";
                }

                // VH: 10102018 VALIDA SI TIENE UNA REUNION
                $cant_proc = DB::table('agenda')->where('id', '<>', $id)->where('id_sala', '<>', $id_sala)->where(function ($query) use ($start2, $terminar, $id_doctor) {
                    return $query->where('id_doctor1', '=', $id_doctor)
                        ->orWhere('id_doctor2', '=', $id_doctor)
                        ->orWhere('id_doctor3', '=', $id_doctor);
                })
                    ->where(function ($query) use ($start2, $terminar, $id_doctor) {
                        return $query->whereRaw("(('" . $start2 . "' BETWEEN fechaini and fechafin)")
                            ->orWhere(function ($query) use ($start2, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                            )
                            ->orWhere(function ($query) use ($start2, $terminar) {
                                $query->whereRaw("(fechaini BETWEEN '" . $start2 . "' and '" . $terminar . "'");
                            })
                            ->orWhere(function ($query) use ($start2, $terminar) {
                                $query->whereRaw("fechafin BETWEEN '" . $start2 . "' and '" . $terminar . "')");
                            });
                    })
                    ->where(function ($query) {
                        return $query->where('estado', '<>', '0')->where('proc_consul', '1');
                    })
                    ->count();

                if ($cant_proc > 0) {
                    return "Doctor posee " . $cant_proc . " procedimiento(s) en otra sala";
                }

                // HORARIO LABORABLE
                $horariocontroller = new HorarioController();
                $horario           = $horariocontroller->valida_horarioxdoctor_dia_2($id_doctor, $start2, $terminar);

                if ($horario == "INI") {
                    return "Fecha de inicio fuera del Horario Laborable del Doctor";
                }

                if ($horario == "FIN") {
                    return "Fecha de fin fuera del Horario Laborable del Doctor";
                }
                // $horarioscontroller = new HorarioController();
                // $horarios           = $horarioscontroller->valida_horarioxsala_2($id_sala, $start2, $terminar);

                // HORARIO SALA
                if ($horario == "INI") {
                    return "Fecha de inicio fuera del Horario de la Sala";
                }

                if ($horario == "FIN") {
                    return "Fecha de fin fuera del Horario de la Sala";
                }

            }

        }
        //return "ok";
        //-------------------------------
        $agenda_nueva = DB::select("SELECT *
            FROM agenda
            WHERE id_sala = " . $id_sala . " AND (
            fechaini BETWEEN '" . $start2 . "' AND '" . $terminar . "') AND id !=  " . $id . "  AND  estado != 0 AND  estado_cita != 2 AND id !=  0 ORDER BY fechaini ASC;");

        date_default_timezone_set('America/Guayaquil');

        $cuenta = count($agenda_nueva);
        //return $cuenta;
        if ($cuenta != 0) {
            do {

                $tfin     = strtotime($agenda_nueva[0]->fechafin);
                $tinicio  = strtotime($agenda_nueva[0]->fechaini);
                $tiempo   = $tfin - $tinicio;
                $tiempo   = $tiempo;
                $start2   = $end2;
                $end2     = strtotime('+' . $tiempo . ' seconds', strtotime($end2));
                $end2     = date('Y-m-d H:i', $end2);
                $terminar = strtotime('-1 minute', strtotime($end2));
                $terminar = date('Y-m-d H:i', $terminar);

                $id_nuevo = $agenda_nueva[0]->id;

                $id_doctor = $agenda_nueva[0]->id_doctor1;

                $id_sala = $agenda_nueva[0]->id_sala;

                if ($id_doctor != null) {

                    if ($id_doctor != '9666666666') {

                        // VH: 10102018 VALIDA SI TIENE UNA CONSULTA
                        $cant_consultas = DB::table('agenda')->where('id', '<>', $id_nuevo)->where(function ($query) use ($start2, $terminar, $id_doctor) {
                            return $query->where('id_doctor1', '=', $id_doctor)
                                ->orWhere('id_doctor2', '=', $id_doctor)
                                ->orWhere('id_doctor3', '=', $id_doctor);
                        })
                            ->where(function ($query) use ($start2, $terminar, $id_doctor) {
                                return $query->whereRaw("(('" . $start2 . "' BETWEEN fechaini and fechafin)")
                                    ->orWhere(function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                                    )
                                    ->orWhere(function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("(fechaini BETWEEN '" . $start2 . "' and '" . $terminar . "'");
                                    })
                                    ->orWhere(function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("fechafin BETWEEN '" . $start2 . "' and '" . $terminar . "')");
                                    });
                            })
                            ->where(function ($query) {
                                return $query->where('estado', '<>', '0')->where('proc_consul', '0');
                            })
                            ->count();

                        if ($cant_consultas > 0) {
                            return "Una de las Agendas a desplazar tiene " . $cant_consultas . " consulta(s)";
                        }

                        // VH: 10102018 VALIDA SI TIENE UNA REUNION
                        $cant_reuniones = DB::table('agenda')->where('id', '<>', $id_nuevo)->where(function ($query) use ($start2, $terminar, $id_doctor) {
                            return $query->where('id_doctor1', '=', $id_doctor)
                                ->orWhere('id_doctor2', '=', $id_doctor)
                                ->orWhere('id_doctor3', '=', $id_doctor);
                        })
                            ->where(function ($query) use ($start2, $terminar, $id_doctor) {
                                return $query->whereRaw("(('" . $start2 . "' BETWEEN fechaini and fechafin)")
                                    ->orWhere(function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                                    )
                                    ->orWhere(function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("(fechaini BETWEEN '" . $start2 . "' and '" . $terminar . "'");
                                    })
                                    ->orWhere(function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("fechafin BETWEEN '" . $start2 . "' and '" . $terminar . "')");
                                    });
                            })
                            ->where(function ($query) {
                                return $query->where('estado', '<>', '0')->where('proc_consul', '2');
                            })
                            ->count();

                        if ($cant_reuniones > 0) {
                            return "Una de las Agendas a desplazar tiene " . $cant_reuniones . " reunion(es)";
                        }

                        // VH: 10102018 VALIDA SI TIENE UNA REUNION
                        $cant_proc = DB::table('agenda')->where('id', '<>', $id_nuevo)->where('id_sala', '<>', $id_sala)->where(function ($query) use ($start2, $terminar, $id_doctor) {
                            return $query->where('id_doctor1', '=', $id_doctor)
                                ->orWhere('id_doctor2', '=', $id_doctor)
                                ->orWhere('id_doctor3', '=', $id_doctor);
                        })
                            ->where(function ($query) use ($start2, $terminar, $id_doctor) {
                                return $query->whereRaw("(('" . $start2 . "' BETWEEN fechaini and fechafin)")
                                    ->orWhere(function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                                    )
                                    ->orWhere(function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("(fechaini BETWEEN '" . $start2 . "' and '" . $terminar . "'");
                                    })
                                    ->orWhere(function ($query) use ($start2, $terminar) {
                                        $query->whereRaw("fechafin BETWEEN '" . $start2 . "' and '" . $terminar . "')");
                                    });
                            })
                            ->where(function ($query) {
                                return $query->where('estado', '<>', '0')->where('proc_consul', '1');
                            })
                            ->count();

                        if ($cant_proc > 0) {
                            return "Una de las Agendas a desplazar tiene " . $cant_proc . " procedimiento(s) en otra sala";
                        }

                        // HORARIO LABORABLE
                        $horariocontroller = new HorarioController();
                        $horario           = $horariocontroller->valida_horarioxdoctor_dia_2($id_doctor, $start2, $terminar);

                        if ($horario == "INI") {
                            return "Una de las Agendas a desplazar esta fuera del Horario Laborable del Doctor";
                        }

                        if ($horario == "FIN") {
                            return "Una de las Agendas a desplazar esta fuera del Horario Laborable del Doctor";
                        }
                        //HORARIO LABORABLE
                        /*$horarioscontroller = new HorarioController();
                        $horarios           = $horarioscontroller->valida_horarioxsala_2($id_sala, $start2, $terminar);*/

                        if ($horario == "INI") {
                            return "Una de las Agendas a desplazar esta fuera del Horario de L";
                        }

                        if ($horario == "FIN") {
                            return "Una de las Agendas a desplazar esta fuera del Horario Laborable del Doctor";
                        }
                    }

                }

                $agenda_nueva = DB::select("SELECT *
                FROM agenda
                WHERE id_sala = " . $id_sala . " AND (
                fechaini BETWEEN '" . $start2 . "' AND '" . $terminar . "') AND id !=  " . $id_nuevo . " AND  estado != 0 AND  estado_cita != 2 AND id !=  0 ORDER BY fechaini ASC;");
                $cuenta = count($agenda_nueva);

            } while ($cuenta != 0);
        }
        return "OK";
    }

    public function preagenda()
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fecha      = date('Y-m-j');
        $nuevafecha = strtotime('-1 month', strtotime($fecha));
        $bfecha     = date('Y-m-j', $nuevafecha);

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'paciente.ciudad')
            ->where('proc_consul', '=', 1)
            ->where('fechaini', '>=', $bfecha)
            ->where('agenda.estado', '=', '-1')
            ->get();

        return view('preagenda/calendario', ['agenda' => $agenda, 'fecha' => '0']);
    }

    public function preagenda2($fecha_proc)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $fecha      = date('Y-m-j');
        $nuevafecha = strtotime('-1 month', strtotime($fecha));
        $bfecha     = date('Y-m-j', $nuevafecha);

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 1)
            ->where('fechaini', '>=', $bfecha)
            ->where('agenda.estado', '=', '-1')
            ->get();

        return view('preagenda/calendario', ['agenda' => $agenda, 'fecha' => $fecha_proc]);
    }

    public function nuevo($fecha, $i, $sala)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
     //   dd('Hola');

        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->where('proc_consul_sala', '1')
            ->where('sala.id_hospital', '2')->where('sala.nombre_sala', '!=', 'RECUPERACION')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital', 'hospital.direccion as direccion_hospital')->orderBy('hospital.nombre_hospital')
            ->get();

        $paciente = paciente::find($i);

        //$paciente_observaciones = Paciente_Observaciones::where('id_paciente', $i)->first();

        //SI NO SE ENCUENTRA EL PACIENTE
        if ($paciente == array() && $i != '0') {

            return redirect()->route('agenda.paciente', ['id' => '1', 'i' => $i, 'fecha' => $fecha, 'sala' => $sala]);
        }

        $cortesia_paciente = Cortesia_paciente::find($i);

        $procedimiento = Procedimiento::all();
        $empresa       = Empresa::where('admision', '1')->get();
        $seguro        = Seguro::where('inactivo', '1')->orderBy('nombre', 'asc')->get();

        date_default_timezone_set('UTC');
        $fecha  = substr($fecha, 0, 10);
        $fecha2 = date('Y/m/d H:i', $fecha);

        $citas   = array();
        $ordenes = array();
        if (!is_null($paciente)) {
            $agendacontroller = new AgendaController;

            $citas   = $agendacontroller->busca_citasxpaciente_dia_mes($fecha2, $paciente->id);
            $ordenes = Orden::where('id_paciente', $paciente->id)->where('estado', 1)->orderBy('fecha_orden', 'desc')->limit(10)->get();
            //dd($citas);

        }
        $paciente_obser = Paciente_Observaciones::where('id_paciente',$i)->first();
        //
        //dd($paciente_obser);
        return view('preagenda/agregar', ['salas' => $salas, 'paciente' => $paciente, 'procedimiento' => $procedimiento, 'procedimiento2' => $procedimiento, 'i' => $i, 'empresa' => $empresa, 'seguro' => $seguro, 'hora' => $fecha2, 'unix' => $fecha, 'cortesia_paciente' => $cortesia_paciente, 'citas' => $citas, 'sala2' => $sala, 'ordenes' => $ordenes, 'paciente_obser' => $paciente_obser]);
    }

    public function search(Request $request)
    {
        $constraints = [
            'id'        => $request['id'],
            'apellido1' => $request['apellido'],
        ];

        $constraints2 = [
            'apellido2' => $request['apellido'],
        ];

        $users = $this->doSearchingQuery($constraints, $constraints2);

        $tipousuarios = tipousuario::all();
        return view('agenda/index', ['users' => $users, 'searchingVals' => $constraints, 'tipousuarios' => $tipousuarios]);
    }

    private function doSearchingQuery($constraints, $constraints2)
    {
        $query   = User::query();
        $fields  = array_keys($constraints);
        $fields2 = array_keys($constraints2);
        $index   = 0;
        $index2  = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                $query = $query->where('id_tipo_usuario', '=', '3');
                $index++;
            }
            $index++;
        }
        foreach ($constraints2 as $constraint2) {
            if ($constraint2 != null) {
                $query = $query->orwhere($fields2[$index2], 'like', '%' . $constraint2 . '%');
                $query = $query->where('id_tipo_usuario', '=', '3');
                $index++;
            }

            $index++;
        }
        $query = $query->where('id_tipo_usuario', '=', '3');
        return $query->paginate(5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function validateprincipal($request)
    {

        $rules = [
            'id2' => 'different:id',
        ];

        $messages = [
            'id2.different' => 'Cédula es la misma que la del principal.',
        ];

        $this->validate($request, $rules, $messages);
    }

    private function validateInput($request)
    {
        $rules = [
            'parentesco'        => 'required',
            'id'                => 'required|max:10|unique:users',
            'nombre1'           => 'required|max:60',
            'nombre2'           => 'required|max:60',
            'apellido1'         => 'required|max:60',
            'apellido2'         => 'required|max:60',
            'telefono1'         => 'required|numeric|max:9999999999',
            'telefono2'         => 'required|numeric|max:9999999999',
            'id_pais'           => 'required',
            'fecha_nacimiento'  => 'required|date|edad_fecha',
            'email'             => 'required|email|max:191|unique:users',
            'id_seguro'         => 'required',
            'id2'               => 'required|max:10|unique:paciente,id',
            'nombre12'          => 'required|max:60',
            'nombre22'          => 'required|max:60',
            'apellido12'        => 'required|max:60',
            'apellido22'        => 'required|max:60',
            'telefono12'        => 'required|numeric|max:9999999999',
            'telefono22'        => 'required|numeric|max:9999999999',
            'id_pais2'          => 'required',
            'fecha_nacimiento2' => 'required|date',
        ];
        if ($request['parentesco'] == "Principal") {
            $rules = array_add($rules, 'menoredad', 'in:0');
        }
        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar Ninguno.',
            'id.required'                => 'Agrega la cédula.',
            'id.max'                     => 'La cédula no puede ser mayor a :max caracteres.',
            'id.unique'                  => 'Cedula ya se encuentra registrada.',
            'nombre1.required'           => 'Agrega el primer nombre.',
            'nombre22.required'          => 'Agrega el segundo nombre.',
            'nombre1.max'                => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre2.max'                => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido1.required'         => 'Agrega el primer apellido.',
            'apellido1.max'              => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido2.required'         => 'Agrega el segundo apellido.',
            'apellido2.max'              => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono1.required'         => 'Agrega el teléfono del domicilio.',
            'telefono1.numeric'          => 'El teléfono de domicilio debe ser numérico.',
            'telefono1.max'              => 'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
            'telefono2.required'         => 'Agrega el teléfono celular.',
            'telefono2.numeric'          => 'El teléfono celular debe ser numérico.',
            'telefono2.max'              => 'El teléfono celular no puede ser mayor a 10 caracteres.',
            'id_pais.required'           => 'Selecciona el pais.',
            'fecha_nacimiento.required'  => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento.date'      => 'La fecha de nacimiento tiene formato incorrecto.',
            'email.required'             => 'Agrega el Email.',
            'email.email'                => 'El Email tiene error en el formato.',
            'email.max'                  => 'El Email no puede ser mayor a :max caracteres.',
            'email.unique'               => 'el Email ya se encuentra registrado.',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'id2.required'               => 'Agrega la cédula.',
            'id2.max'                    => 'La cédula no puede ser mayor a :max caracteres.',
            'id2.unique'                 => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre12.required'          => 'Agrega el primer nombre.',
            'nombre12.max'               => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre22.max'               => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido12.required'        => 'Agrega el primer apellido.',
            'apellido12.max'             => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido22.required'        => 'Agrega el segundo apellido.',
            'apellido22.max'             => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a 10 caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        //return $rules;
        $this->validate($request, $rules, $messages);
    }

    private function validateInput2($request)
    {

        $rules = [
            'parentesco'        => 'required',
            'id_seguro'         => 'required',
            'id2'               => 'required|max:10|unique:paciente,id',
            'nombre12'          => 'required|max:60',
            'nombre22'          => 'required|max:60',
            'apellido12'        => 'required|max:60',
            'apellido22'        => 'required|max:60',
            'telefono12'        => 'required|numeric|max:9999999999',
            'telefono22'        => 'required|numeric|max:9999999999',
            'id_pais2'          => 'required',
            'fecha_nacimiento2' => 'required|date',
        ];
        if ($request['parentesco'] == "Principal") {

            $rules = array_add($rules, 'menoredad', 'in:0');

        }
        $messages = [
            'parentesco.required'        => 'Selecciona el parentesco.',
            'parentesco.in'              => 'Debe seleccionar entre Padre/Madre,Conyugue,Hijo(a).',
            'id_seguro.required'         => 'Selecciona el seguro.',
            'id2.required'               => 'Agrega la cédula.',
            'id2.max'                    => 'La cédula no puede ser mayor a :max caracteres.',
            'id2.unique'                 => 'Cedula ya se encuentra registrada a un paciente.',
            'nombre12.required'          => 'Agrega el primer nombre.',
            'nombre22.required'          => 'Agrega el segundo nombre.',
            'nombre12.max'               => 'El primer nombre no puede ser mayor a :max caracteres.',
            'nombre22.max'               => 'El segundo nombre no puede ser mayor a :max caracteres.',
            'apellido12.required'        => 'Agrega el primer apellido.',
            'apellido12.max'             => 'El primer apellido no puede ser mayor a :max caracteres.',
            'apellido22.required'        => 'Agrega el segundo apellido.',
            'apellido22.max'             => 'El segundo apellido no puede ser mayor a :max caracteres.',
            'telefono12.required'        => 'Agrega el teléfono del domicilio.',
            'telefono12.numeric'         => 'El teléfono de domicilio debe ser numérico.',
            'telefono12.max'             => 'El teléfono del domicilio no puede ser mayor a 10 caracteres.',
            'telefono22.required'        => 'Agrega el teléfono celular.',
            'telefono22.numeric'         => 'El teléfono celular debe ser numérico.',
            'telefono22.max'             => 'El teléfono celular no puede ser mayor a 10 caracteres.',
            'id_pais2.required'          => 'Selecciona el pais.',
            'fecha_nacimiento2.required' => 'Agrega la fecha de nacimiento.',
            'fecha_nacimiento2.date'     => 'La fecha de nacimiento tiene formato incorrecto.',
            'menoredad.in'               => 'El Asegurado Principal no puede ser menor de edad.',
        ];

        $this->validate($request, $rules, $messages);
    }

    public function cuenta_cortesias($fechaini, $id_doctor)
    {
        $fecha_dia = date('Y-m-d', strtotime($fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $cant_cortesias = DB::table('agenda')
            ->join('cortesia_paciente', function ($join) use ($fecha_dia, $id_doctor, $nuevafecha) {
                $join->on('agenda.id_paciente', '=', 'cortesia_paciente.id')
                    ->where('agenda.id_doctor1', $id_doctor)->where('agenda.fechaini', '>', $fecha_dia)->where('agenda.fechaini', '<', $nuevafecha)->where('agenda.cortesia', 'SI')->where('cortesia_paciente.ilimitado', 'NO')->where('cortesia_paciente.cortesia', 'SI');
            })
            ->count();

        return $cant_cortesias;
    }

    /**
     *  a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        date_default_timezone_set('America/Guayaquil');
        $ip_cliente  = $_SERVER["REMOTE_ADDR"];
        $idusuario   = Auth::user()->id;
        $estado_cita = 0;
        $estado      = 1;
        $cortesia    = "NO";
        $cant_cort   = 0;

        $cuenta = DB::table('agenda')
            ->where('id_paciente', '=', $request['id_paciente'])->count();
        if ($cuenta == '0') {
            $tipo_cita = 0;
        } else {
            $tipo_cita = 1;

        }
        $valor = $request['proc_consul'];

        $fecha          = date('Y-m-d H:i');
        $procedimientos = $request['procedimiento'];
        $procedimientop = $procedimientos[0];

        //12/1/2018 validacion de sala ocupada
        $idhospital = Sala::find($request['id_sala'])->id_hospital;
        if ($idhospital == '2') {
            $agendacontroller = new AgendaController;
            //$agendacontroller->valida_salaPentax($request,'0','0');
        } //--
        //VALIDA SI TIENE AGREGADO PROCEDIMIENTO 22012019
        $rules1 = [
            'procedimiento' => 'required',
        ];
        $mensajes1 = [
            'procedimiento.required' => 'Ingrese el Procedimiento',
        ];

        if ($request->est_amb_hos == '1') {
            $arr_hos = [
                'omni' => 'required',
            ];
            $arr_men = [
                'omni.required' => 'Seleccione Si es Omni Hospital',
            ];
            $this->validate($request, $arr_hos, $arr_men);
            $omni = $request->omni;
        } else {
            $omni = null;
        }

        $this->validate($request, $rules1, $mensajes1);
        //
        //dd("paso");
        $this->validateInput3($request);

        $this->validateInput4($request);

        if ($valor != 2) {
            //dd($request->tipo_horario);

            //valida horario de la sala
            $horariosalacontroller = new HorarioSalaController();
            $cantidad_horarios     = $horariosalacontroller->valida_horarioxsala($request);
            //dd($cantidad_horarios);

            $this->validateInput6($request);

            $paciente = Paciente::find($request['id_paciente']);

            $this->validate_paciente($request);
            $usuario_prin = User::find($paciente->id_usuario);
            $correo       = $usuario_prin->email;

            if ($request->id_seguro == '2') {

                $val_arr = [
                    'id_empresa' => 'required',
                ];
                $msn_arr = [
                    'id_empresa.required' => 'Seleccione la Empresa',
                ];
                $this->validate($request, $val_arr, $msn_arr);

            }

        }
        $cv                    = $request['validacion_cv_msp'];
        $nc                    = $request['validacion_nc_msp'];
        $sec                   = $request['validacion_sec_msp'];
        $codigo_validacion_msp = $cv . '-' . $nc . '-' . $sec;

        if ($valor == 1) {
            //VALIDA SI TIENE CODIGO DE VALIDACION Y FECHA DE VALIDACION
            // ISSFA= 3;
            // MSP=5;
            //ISSPOL=6;

            //if($request['id_seguro']=='3')
            //dd();

            if ($request['id_seguro'] == '3') {
                // dd($request['id_seguro']);
                if ($request['adelantado'] == 1) {
                    //dd($request['adelantado']);
                    $rules_observacionissfa = [

                        'observaciones' => 'required',

                    ];

                    $mensajes_observacionissfa = [
                        'observaciones.required' => 'Ingrese una observacion',
                    ];
                    $this->validate($request, $rules_observacionissfa, $mensajes_observacionissfa);
                }

                $rules_issfa = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];

                $mensajes_issfa = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',
                ];

                $this->validate($request, $rules_issfa, $mensajes_issfa);

            } elseif ($request['id_seguro'] == '5') {

                if ($request['adelantado'] == 1) {
                    $rules_observacionmsp = [

                        'observaciones' => 'required',

                    ];

                    $mensajes_observacionmsp = [
                        'observaciones.required' => 'Ingrese la fecha de validación',
                    ];
                    //$this->validate($request, $rules_observacionmsp, $mensajes_observacionmsp);
                }

                $rules_msp = [
                    'fecha_val'          => 'required',
                    'validacion_cv_msp'  => 'required',
                    'validacion_nc_msp'  => 'required',
                    'validacion_sec_msp' => 'required',

                ];
                $mensajes_msp = [
                    'fecha_val.required'          => 'Ingrese la fecha de validación',
                    'validacion_cv_msp.required'  => 'codigo',
                    'validacion_nc_msp.required'  => 'numero',
                    'validacion_sec_msp.required' => 'secuencia',

                ];

                //$this->validate($request, $rules_msp, $mensajes_msp);

            }elseif ($request['id_seguro'] == '2') {

                if ($request['adelantado'] == 1) {
                    $rules_observacioniess = [

                        'obervaciones' => 'required',

                    ];

                    $mensajes_observacioniess = [
                        'obervaciones.required' => 'Ingrese la fecha de validación',
                    ];
                    //$this->validate($request, $rules_observacioniess, $mensajes_observacioniess);
                }

                $rules_iess = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];
                $mensajes_iess = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',

                ];
                //dd($rules_iess,$request['adelantado']);
                //$this->validate($request, $rules_iess, $mensajes_iess);
                
            }elseif ($request['id_seguro'] == '6') {

                if ($request['adelantado'] == 1) {
                    $rules_observacionisspol = [

                        'obervaciones' => 'required',

                    ];

                    $mensajes_observacionisspol = [
                        'obervaciones.required' => 'Ingrese la fecha de validación',
                    ];
                    $this->validate($request, $rules_observacionisspol, $mensajes_observacionisspol);
                }

                $rules_isspol = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];
                $mensajes_isspol = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',

                ];
                $this->validate($request, $rules_isspol, $mensajes_isspol);
            }

            $input_historia = [
                'fechaini'              => $request['inicio'],
                'fechafin'              => $request['fin'],
                'id_paciente'           => $request['id_paciente'],
                'id_empresa'            => $request['id_empresa'],

                'id_procedimiento'      => $procedimientop,
                'proc_consul'           => $request['proc_consul'],
                'id_sala'               => $request['id_sala'],

                'id_seguro'             => $request['id_seguro'],
                'tipo_cita'             => $request['tipo_cita'],
                'estado_cita'           => $estado_cita,
                'observaciones'         => $request['observaciones'],
                'est_amb_hos'           => $request['est_amb_hos'],
                'estado'                => '-1',
                'ip_creacion'           => $ip_cliente,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'cortesia'              => $request->cortesia,
                'omni'                  => $omni,
                'fecha_val'             => $request['fecha_val'],
                'cod_val'               => $request['cod_val'],
                'validacion_cv_msp'     => $request['validacion_cv_msp'],
                'validacion_nc_msp'     => $request['validacion_nc_msp'],
                'validacion_sec_msp'    => $request['validacion_sec_msp'],
                'codigo_validacion_msp' => $codigo_validacion_msp,
                'adelantado'            => $request['adelantado'],

            ];

            $id_agenda = agenda::insertGetId($input_historia);
            foreach ($procedimientos as $value) {
                if ($procedimientop != $value) {
                    AgendaProcedimiento::create([
                        'id_agenda'        => $id_agenda,
                        'id_procedimiento' => $value,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => $idusuario,
                        'id_usuariomod'    => $idusuario,
                    ]);
                }

            }
            if ($request['hc'] != null) {
                Agenda_archivo::create([
                    'id_agenda'       => $id_agenda,
                    'tipo_documento'  => 'txt',
                    'texto'           => $request['hc'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                ]);
            }
            if ($request['archivo'] != null) {
                $input_archivo = [
                    'id_agenda'       => $id_agenda,
                    'tipo_documento'  => "HCAGENDA",
                    'descripcion'     => "Historia Clinica creada de la agenda",
                    'ruta'            => "/hc_agenda/",
                    'id_usuariocrea'  => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];
                $id_archivo = Agenda_archivo::insertGetId($input_archivo);
                $this->subir_archivo_validacion($request, $id_agenda, $id_archivo);
            }
            $input = [
                'id_seguro'        => $request['id_seguro'],
                'fecha_nacimiento' => $request['fecha_nacimiento'],
                'ip_modificacion'  => $ip_cliente,
                'id_usuariomod'    => $idusuario,

            ];

            if ($paciente->fecha_nacimiento != $request['fecha_nacimiento']) {
                $paciente->update($input);
            }

        }

        $necesita_cardio = false;
        foreach ($procedimientos as $value) {
            $procedimiento = Procedimiento::find($value);
            if ($procedimiento->cardiologia == '1') {
                $necesita_cardio = true;
            }
        }
        if ($necesita_cardio) {

            return redirect()->route('cardiologia.agenda', ['id_agenda' => $id_agenda, 'url' => '0']);

        }

        //ORDENES DE PROCEDIMIENTOS
        /*
        $valor_procedimiento = $request['proc_consul'];
        $orden_tipo = null;
        if($valor_procedimiento == '1'){

        foreach($procedimientos as $od_px){
        //Obtengo el Id del grupo Procedimiento del la Tabla Procedimiento
        //$od_proc = Procedimiento::find($value);
        $od_proc = Procedimiento::find($od_px);
        if(!is_null($od_proc)){
        if($od_proc->id_grupo_procedimiento!=null){
        $orden = DB::table('orden as or')->where('or.id_paciente',$request['id_paciente'])->where('or.estado',1)->join('orden_tipo as ot','ot.id_orden','or.id')
        ->where('ot.id_grupo_procedimiento',$od_proc->id_grupo_procedimiento)->join('orden_procedimiento as op','op.id_orden_tipo','ot.id')
        ->whereNull('op.id_agenda')->where('op.id_procedimiento',$od_px)->orderBy('or.created_at','desc')
        ->select('or.*','ot.id as id_or_tipo','op.id as id_or_proc')->first();
        if(!is_null($orden)){
        $od_arr = [
        'id_agenda' => $id_agenda,
        'id_recepcion' => $idusuario,
        'fecha_recepcion' => date('Y-m-d H:i:s'),
        ];
        $orden_procedimiento = Orden_Procedimiento::find($orden->id_or_proc);
        $orden_procedimiento->update($od_arr);

        }
        }

        }

        }

        }*/

        return redirect()->route('preagenda.pentax', ['fecha' => $request['unix']])->withInput(['sel_sala' => $request['id_sala']]);
    }

    private function validate_paciente($request)
    {
        $id_paciente = $request['id_paciente'];
        $ini2        = date_create($request['inicio']);
        $fin2        = date_create($request['fin']);
        $inicio      = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin         = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio      = date_format($inicio, 'Y/m/d H:i:s');
        $fin         = date_format($fin, 'Y/m/d H:i:s');
        $dato2       = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_paciente', '=', $request['id_paciente']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");}
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', -1);
            })
            ->get();

        $cant_agenda = $dato2->count();
        $rules       = [
            'id_paciente' => 'unique_doctor:' . $cant_agenda,
        ];
        $mensajes = [
            'id_paciente.unique_doctor' => 'El paciente ya posee una cita a esta hora',
        ];
        $this->validate($request, $rules, $mensajes);

    }

    public function subir_archivo_validacion(Request $request, $id_agenda, $id_archivo)
    {
        $extension    = strtolower($request['archivo']->getClientOriginalExtension());
        $nuevo_nombre = "hc_ESCPRO_" . $id_agenda . "_" . $id_archivo . "." . $extension;
        $r1           = Storage::disk('hc_agenda')->put($nuevo_nombre, \File::get($request['archivo']));
        if ($r1) {

            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario  = Auth::user()->id;
            date_default_timezone_set('America/Guayaquil');

            $archivo_historico = Agenda_archivo::find($id_archivo);

            $archivo_historico->archivo         = $nuevo_nombre;
            $archivo_historico->ip_modificacion = $ip_cliente;
            $archivo_historico->id_usuariomod   = $idusuario;
            $r2                                 = $archivo_historico->save();

        }

    }

    private function validateInput3($request)
    {

        $rules = [
            //'observaciones' => 'max:200',
            'inicio'  => 'required|date|before:fin',
            'fin'     => 'required|date|after:inicio',
            'id_sala' => 'required',
        ];
        $mensajes = [
            'observaciones.max'         => 'La observacion no puede ser mayor a :max caracteres',
            'id_doctor1.unique_doctor'  => 'La fecha seleccionada esta ocupada para el Doctor Principal',
            'inicio.required'           => 'Agregue una fecha de Inicio.',
            'inicio.date'               => 'fecha mal agregada.',
            'inicio.before'             => 'la fecha de inicio debe ser antes que la de fin',
            'inicio.after'              => 'la fecha de inicio debe ser después de la fecha actual',
            'fin.required'              => 'Agregue una fecha de Inicio.',
            'fin.date'                  => 'fecha mal agregada.',
            'fin.before'                => 'la fecha de fin debe ser después que la fecha actual',
            'fin.after'                 => 'la fecha de fin debe ser después que la de inicio',
            'procedencia.required'      => 'Agregue la procedencia.',
            'procedencia.max'           => 'La procedencia no puede ser mayor a :max caracteres',
            'fecha_nacimiento.required' => 'Agregue la fecha de nacimiento.',
        ];
        $this->validate($request, $rules, $mensajes);

    }

    private function validateInput3_2($request, $id)
    {

        $ini2   = date_create($request['inicio']);
        $fin2   = date_create($request['fin']);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor1'])
                ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                ->orWhere('id_doctor3', '=', $request['id_doctor1']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");}
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();
        $rules       = [
            'id_doctor1' => 'unique_doctor:' . $cant_agenda,
            //'observaciones' => 'max:200',
            'inicio'     => 'required|date|before:fin',
            'fin'        => 'required|date|after:inicio',
            'id_sala'    => 'required',

        ];
        $mensajes = [
            'observaciones.max'        => 'La observacion no puede ser mayor a :max caracteres',
            'id_doctor1.unique_doctor' => 'La fecha seleccionada esta ocupada para el Doctor Principal',
            'inicio.required'          => 'Agregue una fecha de Inicio.',
            'inicio.date'              => 'fecha mal agregada.',
            'inicio.before'            => 'la fecha de inicio debe ser antes que la de fin',
            'inicio.after'             => 'la fecha de inicio debe ser después de la fecha actual',
            'fin.required'             => 'Agregue una fecha de Inicio.',
            'fin.date'                 => 'fecha mal agregada.',
            'fin.before'               => 'la fecha de fin debe ser después que la fecha actual',
            'fin.after'                => 'la fecha de fin debe ser después que la de inicio',
        ];

        $this->validate($request, $rules, $mensajes);

    }

    private function validateInput3_2_SR($request, $agenda, $robles)
    {

        if ($request['inicio'] != null) {
            $ini2 = date_create($request['inicio']);
        } else {
            $ini2 = date_create($agenda->fechaini);
        }
        if ($request['fin'] != null) {
            $fin2 = date_create($request['fin']);
        } else {
            $fin2 = date_create($agenda->fechafin);
        }

        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where('id', '<>', $agenda->id)->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $robles)
                ->orWhere('id_doctor2', '=', $robles)
                ->orWhere('id_doctor3', '=', $robles);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");}
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();
        $rules       = [
            'id_doctor1' => 'unique_doctor:' . $cant_agenda,
            //'observaciones' => 'max:200',
            'inicio'     => 'required|date|before:fin',
            'fin'        => 'required|date|after:inicio',
            'id_sala'    => 'required',

        ];
        $mensajes = [
            'observaciones.max'        => 'La observacion no puede ser mayor a :max caracteres',
            'id_doctor1.unique_doctor' => 'La fecha seleccionada esta ocupada para el Doctor Principal',
            'inicio.required'          => 'Agregue una fecha de Inicio.',
            'inicio.date'              => 'fecha mal agregada.',
            'inicio.before'            => 'la fecha de inicio debe ser antes que la de fin',
            'inicio.after'             => 'la fecha de inicio debe ser después de la fecha actual',
            'fin.required'             => 'Agregue una fecha de Inicio.',
            'fin.date'                 => 'fecha mal agregada.',
            'fin.before'               => 'la fecha de fin debe ser después que la fecha actual',
            'fin.after'                => 'la fecha de fin debe ser después que la de inicio',
        ];

        $this->validate($request, $rules, $mensajes);

    }

    private function validateMax1($request)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);
        return

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor1'])
                ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                ->orWhere('id_doctor3', '=', $request['id_doctor1']);})
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();
        $doctor   = User::find($request['id_doctor1']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor1' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor1' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor1.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor1.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);

    }
    private function validateMax1_2($request, $id)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor1'])
                ->orWhere('id_doctor2', '=', $request['id_doctor1'])
                ->orWhere('id_doctor3', '=', $request['id_doctor1']);})
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where('id', '<>', $id)
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();
        $doctor   = User::find($request['id_doctor1']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor1' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor1' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor1.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor1.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);

    }

    private function validateMax1_2_SR($request, $agenda, $robles)
    {

        $fecha_req = $request['inicio'];
        if ($fecha_req == null) {
            $fecha_req = $agenda->fechaini;
        }
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $robles)
                ->orWhere('id_doctor2', '=', $robles)
                ->orWhere('id_doctor3', '=', $robles);})
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where('id', '<>', $agenda->id)
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();
        $doctor   = User::find($robles);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor1' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor1' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor1.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor1.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);

    }

    private function validateEdit($request)
    {

        $mensajes = ['estado_cita.required' => 'Selecciona el Estado de la Cita.',
            'observaciones.max'                 => 'La observacion no puede ser mayor a :max caracteres.',
            'fecha_nacimiento.required'         => 'Ingresa la fecha de nacimiento.',
        ];
        $constraints = ['estado_cita' => 'required',

            //'observaciones' => 'max:200',

        ];

        $this->validate($request, $constraints, $mensajes);

    }

    private function validateDoctores($request)
    {
        $rules = ['id_doctor3' => 'different:id_doctor2',
            'id_doctor2'           => 'different:id_doctor3'];
        $mensajes = [
            'id_doctor2.different' => 'Los Doctores asistentes no pueden ser la misma persona',
            'id_doctor3.different' => 'Los Doctores asistentes no pueden ser la misma persona'];

        $this->validate($request, $rules, $mensajes);

    }

    private function validateDoctores2($request)
    {
        $rules = ['id_doctor1' => 'different:id_doctor2',
            'id_doctor1'           => 'different:id_doctor3',
            'id_doctor2'           => 'different:id_doctor1',
            'id_doctor3'           => 'different:id_doctor1',
        ];
        $mensajes = [
            'id_doctor1.different' => 'El Doctor principal no puede ser un asistente',
            'id_doctor2.different' => 'El Doctor asistente no pueden ser principal',
            'id_doctor3.different' => 'El Doctor asistente no pueden ser principal'];

        $this->validate($request, $rules, $mensajes);

    }

    private function validateMax2($request)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor2'])
                ->orWhere('id_doctor2', '=', $request['id_doctor2'])
                ->orWhere('id_doctor3', '=', $request['id_doctor2']);})
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();
        $doctor   = User::find($request['id_doctor2']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor2' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor2' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor2.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor2.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);

    }
    private function validateMax3($request)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor3'])
                ->orWhere('id_doctor2', '=', $request['id_doctor3'])
                ->orWhere('id_doctor3', '=', $request['id_doctor3']);})
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();

        $doctor = User::find($request['id_doctor3']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor3' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor3' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor3.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor3.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);

    }
    private function validateMax2_2($request, $id)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor2'])
                ->orWhere('id_doctor2', '=', $request['id_doctor2'])
                ->orWhere('id_doctor3', '=', $request['id_doctor2']);})
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where('id', '<>', $id)
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();
        $doctor   = User::find($request['id_doctor2']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor2' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor2' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor2.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor2.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);

    }
    private function validateMax3_2($request, $id)
    {

        $fecha_req = $request['inicio'];
        $fecha_req = substr($fecha_req, 0, 10);
        $fecha_req = strtotime($fecha_req);
        $fecha_min = date('Y-m-d H:i', $fecha_req);
        $fecha_max = strtotime('+1 day', strtotime($fecha_min));
        $fecha_max = date('Y-m-d H:i', $fecha_max);

        $dato2 = DB::table('agenda')->where(function ($query) use ($request) {
            $query->where('id_doctor1', '=', $request['id_doctor3'])
                ->orWhere('id_doctor2', '=', $request['id_doctor3'])
                ->orWhere('id_doctor3', '=', $request['id_doctor3']);})
            ->where('proc_consul', '=', $request['proc_consul'])
            ->where('estado', '<>', '0')
            ->where('id', '<>', $id)
            ->where(function ($query) use ($request, $fecha_min, $fecha_max) {
                $query->whereBetween('fechaini', array($fecha_min, $fecha_max));
            })
            ->get();
        $cantidad = $dato2->count();

        $doctor = User::find($request['id_doctor3']);
        if ($request['proc_consul'] == 0) {
            $rules = [
                'id_doctor3' => 'max_consulta:' . $cantidad . ',' . $doctor->max_consulta . ',',
            ];
        } else if ($request['proc_consul'] == 1) {
            $rules = [
                'id_doctor3' => 'max_procedimiento:' . $cantidad . ',' . $doctor->max_procedimiento . ',',
            ];
        }
        $mensajes = [
            'id_doctor3.max_consulta'      => 'La cantidad máxima de consultas a atender por día es : ' . $doctor->max_consulta,
            'id_doctor3.max_procedimiento' => 'La cantidad máxima de procedimientos a atender por día es : ' . $doctor->max_procedimiento,
        ];
        $this->validate($request, $rules, $mensajes);

    }

    private function validateInput5($request)
    {

        $ini2   = date_create($request['inicio']);
        $fin2   = date_create($request['fin']);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor2'])
                ->orWhere('id_doctor2', '=', $request['id_doctor2'])
                ->orWhere('id_doctor3', '=', $request['id_doctor2']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");}
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();

        $dato3 = DB::table('agenda')->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor3'])
                ->orWhere('id_doctor2', '=', $request['id_doctor3'])
                ->orWhere('id_doctor3', '=', $request['id_doctor3']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("'" . $request['fin'] . "' BETWEEN fechaini and fechafin)");}
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda2 = $dato3->count();

        $rules3 = [
            'id_doctor3' => 'unique_doctor:' . $cant_agenda2,
        ];
        $rules2 = [
            'id_doctor2' => 'unique_doctor:' . $cant_agenda,
        ];
        $mensajes2 = [
            'id_doctor2.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 1',
        ];
        $mensajes3 = [
            'id_doctor3.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 2',
        ];

        if ($request['id_doctor2'] != "" && $request['id_doctor3'] == "") {
            $this->validate($request, $rules2, $mensajes2);
        }

        if ($request['id_doctor2'] == "" && $request['id_doctor3'] != "") {
            $this->validate($request, $rules3, $mensajes3);
        }

        $rules = [
            'id_doctor2' => 'unique_doctor:' . $cant_agenda,
            'id_doctor3' => 'unique_doctor:' . $cant_agenda2,
        ];
        $mensajes = [
            'id_doctor3.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 2',
            'id_doctor2.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 1',
        ];

        if ($request['id_doctor2'] != "" && $request['id_doctor3'] != "") {
            $this->validate($request, $rules, $mensajes);
        }

    }

    private function validateInput5_2($request, $id)
    {

        $ini2   = date_create($request['inicio']);
        $fin2   = date_create($request['fin']);
        $inicio = date_add($ini2, date_interval_create_from_date_string('1 seconds'));
        $fin    = date_sub($fin2, date_interval_create_from_date_string('1 seconds'));
        $inicio = date_format($inicio, 'Y/m/d H:i:s');
        $fin    = date_format($fin, 'Y/m/d H:i:s');
        $dato2  = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor2'])
                ->orWhere('id_doctor2', '=', $request['id_doctor2'])
                ->orWhere('id_doctor3', '=', $request['id_doctor2']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("'" . $fin . "' BETWEEN fechaini and fechafin)");}
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda = $dato2->count();

        $dato3 = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($request, $inicio, $fin) {
            return $query->where('id_doctor1', '=', $request['id_doctor3'])
                ->orWhere('id_doctor2', '=', $request['id_doctor3'])
                ->orWhere('id_doctor3', '=', $request['id_doctor3']);
        })
            ->where(function ($query) use ($request, $inicio, $fin) {
                return $query->whereRaw("(('" . $inicio . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("'" . $request['fin'] . "' BETWEEN fechaini and fechafin)");}
                    )
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("(fechaini BETWEEN '" . $inicio . "' and '" . $fin . "'");
                    })
                    ->orWhere(function ($query) use ($request, $inicio, $fin) {
                        $query->whereRaw("fechafin BETWEEN '" . $inicio . "' and '" . $fin . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', 1);
            })
            ->get();

        $cant_agenda2 = $dato3->count();

        $rules3 = [
            'id_doctor3' => 'unique_doctor:' . $cant_agenda2,
        ];
        $rules2 = [
            'id_doctor2' => 'unique_doctor:' . $cant_agenda,
        ];
        $mensajes2 = [
            'id_doctor2.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 1',
        ];
        $mensajes3 = [
            'id_doctor3.unique_doctor' => 'La fecha seleccionada esta ocupada para el Medico Asistente 2',
        ];

        if ($request['id_doctor2'] != "" && $request['id_doctor3'] == "") {
            $this->validate($request, $rules2, $mensajes2);
        }

        if ($request['id_doctor2'] == "" && $request['id_doctor3'] != "") {
            $this->validate($request, $rules3, $mensajes3);
        }

        $rules = [
            'id_doctor2' => 'unique_doctor:' . $cant_agenda,
            'id_doctor3' => 'unique_doctor:' . $cant_agenda2,
        ];
        $mensajes = [
            'id_doctor3.unique_doctor' => 'La fecha seleccionada esta ocupada para el Médico Asistente 2',
            'id_doctor2.unique_doctor' => 'La fecha seleccionada esta ocupada para el Médico Asistente 1',
        ];

        if ($request['id_doctor2'] != "" && $request['id_doctor3'] != "") {
            $this->validate($request, $rules, $mensajes);
        }
    }

    private function validateCortesias($request, $cant_cort)
    {
        $reglas = [
            'id' => 'comparamayor:' . $cant_cort . ',2,',
        ];
        $mensajes = [
            'id.comparamayor' => 'Existen ' . $cant_cort . ' cortesias en el día, consulte con el Dr.',
        ];
        $this->validate($request, $reglas, $mensajes);
    }

    private function validateInput4($request)
    {
        $fecha  = date('Y-m-d H:i');
        $reglas = [
            'inicio' => 'date|after:' . $fecha,
            'fin'    => 'date|after:' . $fecha,
        ];
        $mensajes = [
            'inicio.after' => 'la fecha de inicio debe ser después de la fecha actual',
            'fin.after'    => 'la fecha de fin debe ser después que la fecha actual',
        ];
        $this->validate($request, $reglas, $mensajes);
    }
    private function validateInput6($request)
    {
        $reglas = [
            'id'          => 'exists:paciente',

            'id_paciente' => 'required',
            'est_amb_hos' => 'required',
            'tipo_cita'   => 'required',
        ];
        $mensajes = [
            'id.exists'            => 'Paciente ingresado no existe',
            'id_paciente.required' => 'se requiere numero de cedula del paciente',
            'est_amb_hos.required' => 'Seleccione el estado de ingreso del paciente',
            'tipo_cita.required'   => 'Seleccione es consecutivo o primera vez',
        ];
        $this->validate($request, $reglas, $mensajes);
    }

    public function edit($id)
    {

        $ruta = Cookie::get('ruta_p');

        $rolUsuario = Auth::user()->id_tipo_usuario;
        // $id = Auth::user()->id;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $usuarios   = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '=', 1)->where('training', '0')->where('uso_sistema', '0')->orderBy('apellido1')->get(); //3=DOCTORES;
        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->get()->where('estado', '=', 1); //6=ENFERMEROS;
        $salas      = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->where('proc_consul_sala', '1')
            ->where('sala.id_hospital', '2')->where('sala.nombre_sala', '!=', 'RECUPERACION')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')->orderBy('hospital.nombre_hospital')
            ->get();
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $id)
            ->first();
        $historia = Historiaclinica::where('id_agenda', $id)->get();

        //cedula y nombre del paciente cambiar a produ 7/11/2017
        $especialidades = DB::table('user_espe')->join('especialidad', 'user_espe.espid', '=', 'especialidad.id')
            ->select('user_espe.*', 'especialidad.nombre as enombre')->get();

        $seguros              = Seguro::all();
        $procedimientos       = Procedimiento::all();
        //$empresas             = Empresa::all();
        $empresas             = Empresa::where('admision', '1')->get();
        $agendaprocedimientos = AgendaProcedimiento::where('id_agenda', $agenda->id)->get();
        //cedula y nombre del paciente cambiar a produ 7/11/2017
        $id_doc = $agenda->id_doctor1;
        //agendas
        $fechainicio = strtotime('-15 days', strtotime($agenda->fechaini));
        $fechainicio = date('Y-m-d', $fechainicio);

        $fechafinal = strtotime('+15 days', strtotime($agenda->fechaini));
        $fechafinal = date('Y-m-d', $fechafinal);

        $cagenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 1)
            ->where(function ($query) use ($id_doc) {
                $query->where([['id_doctor1', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id_doc], ['agenda.estado', '=', '1']]);
            })
            ->whereBetween('fechaini', [$fechainicio, $fechafinal])->get();

        $cagenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 0)
            ->where(function ($query) use ($id_doc) {
                $query->where([['id_doctor1', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor2', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['id_doctor3', '=', $id_doc], ['agenda.estado', '=', '1']]);
            })
            ->whereBetween('fechaini', [$fechainicio, $fechafinal])->get();

        $cagenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->select('agenda.*', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where(function ($query) use ($id_doc) {
                $query->where([['agenda.id_doctor1', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['agenda.id_doctor2', '=', $id_doc], ['agenda.estado', '=', '1']])
                    ->orWhere([['agenda.id_doctor3', '=', $id_doc], ['agenda.estado', '=', '1']]);
            })
            ->whereBetween('fechaini', [$fechainicio, $fechafinal])->get();

        $citas = array();

        $agendacontroller = new AgendaController;
        $citas            = $agendacontroller->busca_citasxpaciente_dia($agenda->fechaini, $agenda->id_paciente);
        $citas            = $citas->where('id', '!=', $id);

        $cortesia_paciente = Cortesia_paciente::find($agenda->id_paciente);

        $ar_historia = DB::table('agenda_archivo')->where('id_agenda', $id)->where('tipo_documento', '=', 'HCAGENDA')->get();

        $ar_historiatxt = Agenda_archivo::where('id_agenda', $id)->where('tipo_documento', 'txt')->first();

        $xtipo = Seguro::find($agenda->id_seguro)->tipo;

        $pre_post = '0';
        $ex_pre   = null;
        $ex_post  = null;

        if ($xtipo == '0') {
            /////////////CONTROL LABS/////////// BUSCA EXAMEN OBLIGATORIO
            $obligatorio = Examen_obligatorio::where('tipo', '0')->where('id_procedimiento', $agenda->id_procedimiento)->first();

            $pre_post = '0';
            if (!is_null($obligatorio)) {
                $pre_post = $obligatorio->pre_post; //2 prey post

            }
            if ($pre_post == '0') {
                $bandera = true;
                $agi     = '0';
                if ($agendaprocedimientos->count() > 0) {
                    while ($bandera) {

                        $obligatorio = Examen_obligatorio::where('tipo', '0')->where('id_procedimiento', $agendaprocedimientos[$agi]->id_procedimiento)->first();
                        $pre_post    = '0';
                        if (!is_null($obligatorio)) {
                            $pre_post = $obligatorio->pre_post; //2 prey post

                        }
                        $agi++;
                        if ($pre_post != '0') {
                            $bandera = false;
                        }
                        if ($agi >= $agendaprocedimientos->count()) {
                            $bandera = false;
                        }
                    }
                }

            }
            /////////////CONTROL LABS/////////// BUSCA EXAMEN EXCEPCION
            if ($pre_post == '0') {
                $excepcion = Examen_obligatorio::where('tipo', '1')->where('id_procedimiento', $agenda->id_procedimiento)->first();
                $pre_post  = '0';
                if (is_null($excepcion)) {
                    $pre_post = '1'; //2 pre

                }

            }
            if ($pre_post == '0') {
                $bandera = true;
                $agi     = '0';
                if ($agendaprocedimientos->count() > 0) {
                    while ($bandera) {

                        $excepcion = Examen_obligatorio::where('tipo', '1')->where('id_procedimiento', $agendaprocedimientos[$agi]->id_procedimiento)->first();
                        $pre_post  = '0';
                        if (is_null($excepcion)) {
                            $pre_post = '1'; //2 prey post

                        }
                        $agi++;
                        if ($pre_post != '0') {
                            $bandera = false;
                        }
                        if ($agi >= $agendaprocedimientos->count()) {
                            $bandera = false;
                        }
                    }
                }

            }

            //dd($pre_post);

            //ordenes del paciente de la ultima semana, pre y post
            //$hoy = Date('Y-m-d');
            $fecha_antes   = Date('Y-m-d', strtotime('- 1 month', strtotime($agenda->fechaini)));
            $fecha_despues = Date('Y-m-d', strtotime('+5 day', strtotime($agenda->fechaini)));

            $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->where('eo.id_agenda', $id)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();

            if (is_null($ex_pre)) {
                $ex_pre = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->whereBetween('eo.created_at', [$fecha_antes, $fecha_despues])->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'PRE')->first();
            }

            $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->where('eo.id_agenda', $id)->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();

            if (is_null($ex_post)) {
                $ex_post = DB::table('examen_orden as eo')->where('eo.id_paciente', $agenda->id_paciente)->whereBetween('eo.created_at', [$fecha_antes, $fecha_despues])->join('protocolo as p', 'p.id', 'eo.id_protocolo')->where('p.pre_post', 'POST')->first();
            }

        }

        $logs = DB::table('log_agenda as l')->where('l.id_agenda', $agenda->id)->join('users as u', 'u.id', 'l.id_usuariocrea')->select('l.*', 'u.nombre1', 'u.apellido1')->orderBy('l.id', 'desc')->get();

        //Nueva Funcionalidad Ordenes Endoscopica, Funcional, Imagenes
        if (!is_null($agenda->id_paciente)) {

            $ordenes = Orden::where('id_paciente', $agenda->id_paciente)->where('estado', 1)->orderBy('fecha_orden', 'desc')->get();

        }
        $triaje_Check = Triaje_Telefonico::where("id_agenda",$id)->first();

        return view('preagenda/edit', ['triaje_Check'=>$triaje_Check, 'agenda' => $agenda, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas, 'historia' => $historia, 'especialidades' => $especialidades, 'seguros' => $seguros, 'procedimientos' => $procedimientos, 'empresas' => $empresas, 'agendaprocedimientos' => $agendaprocedimientos, 'cagenda' => $cagenda, 'cagenda2' => $cagenda2, 'cagenda3' => $cagenda3, 'citas' => $citas, 'cortesia_paciente' => $cortesia_paciente, 'ar_historia' => $ar_historia, 'ar_historiatxt' => $ar_historiatxt, 'pre_post' => $pre_post, 'ex_pre' => $ex_pre, 'ex_post' => $ex_post, 'ruta' => $ruta, 'logs' => $logs, 'ordenes' => $ordenes]);

    }

    public function detalle($id)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(3)) == false) {
            return redirect()->intended('/');
        }

        $usuarios   = DB::table('users')->where('id_tipo_usuario', '=', 3)->get(); //3=DOCTORES;
        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->get(); //6=ENFERMEROS;
        $salas      = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $id)
            ->first();
        $agendaprocedimientos = AgendaProcedimiento::where('id_agenda', $agenda->id)->get();

        /*23/11/2017 AGREGAR LA HISTORIA CLINICA CANTIDAD DE HISTORIA CLINICA */
        $hcp = DB::select("SELECT h.*, s.nombre as snombre ,e.nombre as especialidad, d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
                            WHERE h.id_paciente = " . $agenda->id_paciente . " AND
                            a.id = h.id_agenda AND
                            s.id = h.id_seguro AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> " . $id . "
                            ORDER BY a.fechaini DESC");

        $fecha_dia = date('Y-m-d', strtotime($agenda->fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $cant_cortesias = Agenda::where('id_doctor1', $agenda->id_doctor1)->where('fechaini', '>', $fecha_dia)->where('fechaini', '<', $nuevafecha)->where('cortesia', 'SI')->count();

        /*23/11/2017 AGREGAR LA HISTORIA CLINICA CANTIDAD DE HISTORIA CLINICA */

        return view('agenda/detalle', ['agenda' => $agenda, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas, 'agendaprocedimientos' => $agendaprocedimientos, 'hcp' => $hcp, 'cant_cortesias' => $cant_cortesias]);
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

    public function Valida_Solo_Robles($id, $request, $agenda)
    {

        /* horario */
        $id_doctor = $id;
        $fechaini  = $request['inicio'];
        if ($fechaini == null) {
            $fechaini = $agenda->fechaini;
        }
        $fechafin = $request['fin'];
        if ($fechafin == null) {
            $fechafin = $agenda->fechafin;
        }

        $ndiaini = $this->saber_dia($fechaini);
        $horaini = date('H:i:s', strtotime($fechaini));

        $ndiafin = $this->saber_dia($fechafin);
        $horafin = date('H:i:s', strtotime($fechafin));

        $inicio = date('Y-m-d H:i:s', strtotime($fechaini));
        $final  = date('Y-m-d H:i:s', strtotime($fechafin));

        $cantidad_ini = Horario_Doctor::where('id_doctor', $id_doctor)->where('ndia', $ndiaini)->where('estado', '1')->where('hora_ini', '<=', $horaini)->where('hora_fin', '>=', $horaini)->count();

        $cantidad_fin = Horario_Doctor::where('id_doctor', $id_doctor)->where('ndia', $ndiafin)->where('estado', '1')->where('hora_ini', '<=', $horafin)->where('hora_fin', '>=', $horafin)->count();

        if ($cantidad_ini == 0) {
            $cantidad_ini = Excepcion_Horario::where('id_doctor1', $id_doctor)->where('inicio', '<=', $inicio)->where('fin', '>=', $inicio)->count();
        }

        if ($cantidad_fin == 0) {
            $cantidad_fin = Excepcion_Horario::where('id_doctor1', $id_doctor)->where('inicio', '<=', $final)->where('fin', '>=', $final)->count();
        }

        $reglas = ['inicio' => 'comparamayor:0,' . $cantidad_ini,
            'fin'               => 'comparamayor:0,' . $cantidad_fin,
        ];
        $mensajes = ['inicio.comparamayor' => 'fecha de inicio esta fuera del horario laborable del Doctor',
            'fin.comparamayor'                 => 'fecha de fin esta fuera del horario laborable del Doctor',
        ];

        $this->validate($request, $reglas, $mensajes);
        /* horario */

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

      private function validatereagendar($request, $agenda)
    {

        $cantidad= 0;
        if($agenda->proc_consul ==1){
            if(date('Y-m-d',strtotime($request['inicio']))!=date('Y-m-d',strtotime($agenda->fechaini))){
                $cantidad = Agenda::join('sala as s', 'agenda.id_sala', '=', 's.id')->where('agenda.fechaini','>', date( 'Y-m-d',strtotime($request['inicio'])).'  0:00:00')->where('agenda.fechaini','<',date( 'Y-m-d',strtotime($request['inicio'])).' 23:59:59')->where('agenda.proc_consul', '1')->where('agenda.estado' , '<>','0')->where('s.id_hospital', '2') ->get()->count();
            }
    
        }
           
          
           
        $maximo_procedimientos = Max_Procedimiento::find('1')->cantidad;
        
        
        $rules = [
            'inicio' => 'max_procedimiento:'.$cantidad.','.$maximo_procedimientos.','
        ]; 
                    

        $mensajes = [
            'inicio.max_procedimiento' => 'La fecha seleccionada tiene: ' . $cantidad. ' procedimientos' ,
        ];
        
        $this->validate($request, $rules, $mensajes);
        //dd($cantidad,$maximo_procedimientos);

    }

    public function update(Request $request, $id)
    {
          

        $ruta = $request->ruta;
        //dd($request->all());
        $fecha      = date('Y-m-d H:i');
        $agenda     = Agenda::findOrFail($id);
        $paciente   = paciente::find($agenda->id_paciente);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $descripcion  = "";
        $descripcion2 = "";
        $descripcion3 = "CAMBIO: ";
        $est_cita     = $request['estado_cita'];
        $est          = '1';
        $bandera      = false;
        $cambio       = false;
        $flag2        = false;
        $proc         = $request['proc'];
        $aux_ant      = "";
        $aux          = "";
        $estado       = '-1';
        $cortesia     = $request->cortesia;
        $agproc       = AgendaProcedimiento::where('id_agenda', $agenda->id)->get();



       
        $idPaciente = $agenda->id_paciente;
       // dd($idPaciente);
        $obser_admin="";
        if($request['observaciones_admin'] != "" || $request['observaciones_admin'] != null){
            $obser_admin = Paciente_Observaciones::where('id_paciente', $idPaciente)->first();
            $obser = [
                    'id_paciente'     => $idPaciente,
                    'observacion'     => $request['observaciones_admin'],
                    'ip_creacion'     => $ip_cliente,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario
            ];
                if(count($obser_admin)>0){
                    //actualiza
                     Paciente_Observaciones::where('id_paciente', $idPaciente)->update($obser);
                }else{
                    //crea
                    Paciente_Observaciones::create($obser);
                }
        }

        //Inicio Orden Procedimiento
        $id_pro_ant = $request['id_proced'];

        $id_pro_act = $proc[0];

        if ($id_pro_act != $id_pro_ant) {

            $orden_act = DB::table('orden_procedimiento as op')
                ->where('op.id_agenda', $id)
                ->select('op.id as id_or_proc')
                ->get();

            if (!is_null($orden_act)) {

                foreach ($orden_act as $od_act) {

                    $od_arr_act = [
                        'id_agenda' => null,
                    ];

                    $orden_procedimiento = Orden_Procedimiento::find($od_act->id_or_proc);
                    $orden_procedimiento->update($od_arr_act);
                }
            }

            foreach ($proc as $od_px) {

                $od_proc = Procedimiento::find($od_px);

                if (!is_null($od_proc)) {

                    if ($od_proc->id_grupo_procedimiento != null) {

                        $orden = DB::table('orden as or')
                            ->where('or.id_paciente', $request['id_paciente'])
                            ->where('or.estado', 1)
                            ->join('orden_tipo as ot', 'ot.id_orden', 'or.id')
                            ->where('ot.id_grupo_procedimiento', $od_proc->id_grupo_procedimiento)
                            ->join('orden_procedimiento as op', 'op.id_orden_tipo', 'ot.id')
                            ->whereNull('op.id_agenda')
                            ->where('op.id_procedimiento', $od_px)
                            ->orderBy('or.created_at', 'desc')
                            ->select('or.*', 'ot.id as id_or_tipo', 'op.id as id_or_proc')
                            ->first();

                        if (!is_null($orden)) {
                            $od_arr = [
                                'id_agenda'       => $id,
                                'id_recepcion'    => $idusuario,
                                'fecha_recepcion' => date('Y-m-d H:i:s'),
                            ];

                            $orden_procedimiento = Orden_Procedimiento::find($orden->id_or_proc);
                            $orden_procedimiento->update($od_arr);

                        }

                    }
                }
            }

        }

        //Fin Orden Procedimiento

        if ($request->est_amb_hos == '1') {
            $arr_hos = [
                'omni' => 'required',
            ];
            $arr_men = [
                'omni.required' => 'Seleccione Si es Omni Hospital',
            ];
            $this->validate($request, $arr_hos, $arr_men);
            $omni = $request->omni;
        } else {
            $omni = null;
        }
        $this->validateEdit($request);
        //12/1/2018 validacion de sala ocupada
        $idhospital = Sala::find($request['id_sala'])->id_hospital;
        if ($idhospital == '2') {
            $agendacontroller = new AgendaController;
            //$agendacontroller->valida_salaPentax($request,'1',$id);
        } //--

        //cambio en campo
        if ($agenda->espid != $request['espid']) {
            $descripcion3 = $descripcion3 . " ESPECIALIZACIÓN,";
            $cambio       = true;

        }
        if ($agenda->id_seguro != $request['id_seguro']) {
            $descripcion3 = $descripcion3 . " SEGURO,";
            $cambio       = true;

        }
        if ($agenda->est_amb_hos != $request['est_amb_hos']) {
            $descripcion3 = $descripcion3 . " INGRESO,";
            $cambio       = true;

        }
        if ($agenda->tipo_cita != $request['tipo_cita']) {
            $descripcion3 = $descripcion3 . " TIPO_CITA,";
            $cambio       = true;

        }
        if ($agenda->id_empresa != $request['id_empresa']) {
            $descripcion3 = $descripcion3 . " EMPRESA,";
            $cambio       = true;

        }
        if ($agenda->procedencia != $request['procedencia']) {
            $descripcion3 = $descripcion3 . " PROCEDENCIA,";
            $cambio       = true;

        }
        if (date('Y/m/d', strtotime($paciente->fecha_nacimiento)) != $request['fecha_nacimiento']) {
            $descripcion3 = $descripcion3 . " NACIMIENTO,";
            $cambio       = true;

        }

        if ($agenda->supervisa_robles != $request['supervisa_robles']) {
            $descripcion3 = $descripcion3 . " SUPERVISA DR. ROBLES,";
            $cambio       = true;

        }

        if ($agenda->paciente_dr != $request['paciente_dr']) {
            $descripcion3 = $descripcion3 . " PACIENTE_DR,";
            $cambio       = true;

        }

        if ($agenda->solo_robles != $request['solo_robles']) {
            $descripcion3 = $descripcion3 . " SOLO LO PUEDE REALIZAR EL DR. ROBLES,";
            $cambio       = true;

        }

        if ($agenda->observaciones != $request['observaciones']) {
            $descripcion3 = $descripcion3 . " OBSERVACION,";
            $cambio       = true;

        }

        if ($agenda->cortesia != $request['cortesia']) {
            $descripcion3 = $descripcion3 . " CORTESIA,";
            $cambio       = true;

        }
        if ($agenda->fecha_val != $request['fecha_val']) {
            $descripcion3 = $descripcion3 . " FECHA DE VALIDACION,";
            $cambio       = true;

        }
        if ($agenda->cod_val != $request['cod_val']) {
            $descripcion3 = $descripcion3 . " CODIGO DE VALIDACION,";
            $cambio       = true;

        }
        if ($agenda->validacion_cv_msp != $request['validacion_cv_msp']) {
            $descripcion3 = $descripcion3 . " CODIGO DE VALIDACION MSP,";
            $cambio       = true;

        }
        if ($agenda->validacion_nc_msp != $request['validacion_nc_msp']) {
            $descripcion3 = $descripcion3 . " CODIGO DE VALIDACION MSP,";
            $cambio       = true;

        }
        if ($agenda->validacion_sec_msp != $request['validacion_sec_msp']) {
            $descripcion3 = $descripcion3 . " CODIGO DE VALIDACION MSP,";
            $cambio       = true;

        }
        if ($agenda->adelantado != $request['adelantado']) {
            $descripcion3 = $descripcion3 . " ADELANTADO,";
            $cambio       = true;

        }

        if ($agenda->omni != $request['omni']) {
            $descripcion3 = $descripcion3 . " OMNI,";
            $cambio       = true;

        }

        if (!is_null($request['id_ag_artxt'])) {

            $agenda_archivotxt = Agenda_archivo::find($request['id_ag_artxt']);
            if ($agenda_archivotxt->texto != $request['hc']) {
                $descripcion3 = $descripcion3 . " AGENDA_ARCHIVO_TXT,";
                $cambio       = true;
            }
        } else {
            if (!is_null($request['hc'])) {
                $descripcion3 = $descripcion3 . " AGENDA_ARCHIVO_TXT,";
                $cambio       = true;
            }
        }

        if ($request->id_seguro == '2') {

            $val_arr = [
                'id_empresa' => 'required',
            ];
            $msn_arr = [
                'id_empresa.required' => 'Seleccione la Empresa',
            ];
            $this->validate($request, $val_arr, $msn_arr);

        }

        if ($agenda->proc_consul == '1') {
            //cambio procedimiento
            //cambio el primero
            if ($proc[0] != $agenda->id_procedimiento) {
                $flag2 = true;

            } else {
                if (count($proc) - 1 != $agproc->count()) {
                    $flag2 = true;

                }
                for ($x = 1; $x < count($proc); $x++) {
                    if ($x <= $agproc->count()) {
                        if ($proc[$x] != $agproc[$x - 1]->id_procedimiento) {
                            $flag2 = true;
                        }
                    }
                }

            }
            if ($flag2) {
                $descripcion3 = $descripcion3 . " PROCEDIMIENTO";
            }
        }

        if ($request['estado_cita'] == '0') //Por Confirmar
        {
            if (!$cambio && !$flag2 && $request['archivo'] == null) {

                if ($ruta == 'tsalas') {
//aquiestoy

                    return redirect()->route('salas_todas.cargar', ['id' => $agenda->id]);
                }
                return redirect()->route('preagenda.pentax', ['fecha' => $request['unix']])->withInput(['sel_sala' => $request['id_sala']]);

            }
        }

        if ($request['estado_cita'] == '1') //confirmar
        {
            if ($agenda->estado_cita == '1') {
                if (!$cambio && !$flag2 && $request['archivo'] == null) {

                    if ($ruta == 'tsalas') {
//aquiestoy

                        return redirect()->route('salas_todas.cargar', ['id' => $agenda->id]);
                    }
                    return redirect()->route('preagenda.pentax', ['fecha' => $request['unix']])->withInput(['sel_sala' => $request['id_sala']]);

                }
            } else {
                $descripcion = "CONFIRMO LA CITA";
                $bandera     = true;
                $input       = [
                    'estado_cita'        => $request['estado_cita'],
                    'observaciones'      => $request['observaciones'],
                    'id_usuarioconfirma' => $idusuario,
                    'id_usuariomod'      => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                ];
            }
        }

        if ($request['estado_cita'] == '2') //reagendar
        {

            $nro_reagenda = 0;
            $bandera      = true;
            $est_cita     = '0';
            $descripcion  = "DISTRIBUYE LA PRE-AGENDA";

            if (!is_null($request['id_sala'])) {

                //dd($request['id_sala']);
                $horariosalacontroller = new HorarioSalaController();
                $cantidad_horarios     = $horariosalacontroller->valida_horarioxsala($request);

            }

            if (!is_null($request['id_doctor1'])) {

                //valida horario del doctor
                $horariocontroller = new HorarioController();
                $cantidad_horarios = $horariocontroller->valida_horarioxdoctor_dia($request);

                $this->validateInput4($request);

                $this->validateMax1_2($request, $id);
                if ($request->id_doctor1 != '9666666666') {
                    $this->validateInput3_2($request, $id);
                }

                $cortesia_paciente = Cortesia_paciente::find($agenda->id_paciente);

                if (!is_null($cortesia_paciente)) {
                    $cortesia = $cortesia_paciente->cortesia;
                    if ($cortesia_paciente->cortesia == "SI" && $cortesia_paciente->ilimitado == "NO") {
                        $agendacontroller = new AgendaController;
                        $cant_cort        = $agendacontroller->cuenta_cortesias($request['inicio'], $request['id_doctor1']);
                        $agendacontroller->validateCortesias($request, $cant_cort);
                    }
                }

                $estado   = '1';
                $est_cita = $agenda->estado_cita;
                if ($agenda->estado_cita == '3') {
                    $est_cita = '0';
                }

                $descripcion = "ASIGNA EL DOCTOR";

            }

            if ($request['id_doctor2'] != '' || $request['id_doctor3'] != '') {
                $this->validateInput5_2($request, $id);

            }
            if ($request['id_doctor2'] != '' && $request['id_doctor3'] != '') {

                $this->validateDoctores($request);
                $this->validateDoctores2($request);
            }

            if ($request['id_doctor2'] != '') {
                $this->validateMax2_2($request, $id);

            }
            if ($request['id_doctor3'] != '') {
                $this->validateMax3_2($request, $id);
            }
            //Preagenda reagendamiento
            //dd($agenda);
            $permiso     = Agenda_Permiso::where('id_usuario', $idusuario)->where('proc_consul', '1')->where('estado', '2')->first();

            if(is_null($permiso)){
                $this->validatereagendar($request,$agenda);
            }
            


            $input = [
                'nro_reagenda'    => $nro_reagenda,
                'cortesia'        => $cortesia,

                'estado'          => $estado,
                'observaciones'   => $request['observaciones'],
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'fechaini'        => $request['inicio'],
                'fechafin'        => $request['fin'],
                'id_doctor1'      => $request['id_doctor1'],
                'id_sala'         => $request['id_sala'],
                'id_doctor2'      => $request['id_doctor2'],
                'id_doctor3'      => $request['id_doctor3'],
                'estado_cita'     => $est_cita,

            ];

        }
        if ($request['estado_cita'] == '3') {
            //suspender
            $bandera     = true;
            $est         = '0';
            $descripcion = "SUSPENDIO LA CITA";
            $input       = [
                'estado_cita'     => $request['estado_cita'],
                'observaciones'   => $request['observaciones'],
                'estado'          => '0',
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
            $mensajes    = ['observaciones.required' => 'Ingresa una Observación.'];
            $constraints = ['observaciones' => 'required'];
            $this->validate($request, $constraints, $mensajes);
        }
        if ($request['estado_cita'] == '4') //ASISTIÓ
        {
            $est_cita = $agenda->estado_cita;
            if (!$cambio && !$flag2 && $request['archivo'] == null) {
                return redirect()->route('admisiones.admision', ['id' => $request['id_paciente'], 'cita' => $id, 'i' => $paciente->id_seguro]);
            }
        }

        if ($request['inicio'] == '') {
            $ini = $agenda->fechaini;
        } else {
            $ini = $request['inicio'];
        }
        if ($request['fin'] == '') {
            $fin = $agenda->fechafin;
        } else {
            $fin = $request['fin'];
        }
        if ($cambio) {
//cambia especialidad, seguro, ingreso o empresa

            //VALIDA SI TIENE CODIGO DE VALIDACION Y FECHA DE VALIDACION
            // ISSFA= 3;
            // MSP=5;
            //ISSPOL=6;

            //if($request['id_seguro']=='3')
            //dd();

            //VALIDA SI TIENE CODIGO DE VALIDACION Y FECHA DE VALIDACION
            // ISSFA= 3;
            // MSP=5;
            //ISSPOL=6;

            //if($request['id_seguro']=='3')

            if ($request['id_seguro'] == '3') {
                // dd($request['id_seguro']);
                if ($request['adelantado'] == 1) {
                    //dd($request['adelantado']);
                    $rules_observacionissfa = [

                        'observaciones' => 'required',

                    ];

                    $mensajes_observacionissfa = [
                        'observaciones.required' => 'Ingrese una observacion',
                    ];
                    $this->validate($request, $rules_observacionissfa, $mensajes_observacionissfa);
                }

                $rules_issfa = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];

                $mensajes_issfa = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',
                ];

                $this->validate($request, $rules_issfa, $mensajes_issfa);

            } elseif ($request['id_seguro'] == '5') {

                if ($request['adelantado'] == 1) {
                    $rules_observacionmsp = [

                        'observaciones' => 'required',

                    ];

                    $mensajes_observacionmsp = [
                        'observaciones.required' => 'Ingrese la fecha de validación',
                    ];
                   // $this->validate($request, $rules_observacionmsp, $mensajes_observacionmsp);
                }

                $rules_msp = [
                    'fecha_val'          => 'required',
                    'validacion_cv_msp'  => 'required',
                    'validacion_nc_msp'  => 'required',
                    'validacion_sec_msp' => 'required',

                ];
                $mensajes_msp = [
                    'fecha_val.required'          => 'Ingrese la fecha de validación',
                    'validacion_cv_msp.required'  => 'codigo',
                    'validacion_nc_msp.required'  => 'numero',
                    'validacion_sec_msp.required' => 'secuencia',

                ];

               // $this->validate($request, $rules_msp, $mensajes_msp);

            } elseif ($request['id_seguro'] == '6') {

                if ($request['adelantado'] == 1) {
                    $rules_observacionisspol = [

                        'obervaciones' => 'required',

                    ];

                    $mensajes_observacionisspol = [
                        'obervaciones.required' => 'Ingrese la fecha de validación',
                    ];
                    $this->validate($request, $rules_observacionisspol, $mensajes_observacionisspol);
                }

                $rules_isspol = [
                    'fecha_val' => 'required',
                    'cod_val'   => 'required',
                ];
                $mensajes_isspol = [
                    'fecha_val.required' => 'Ingrese la fecha de validación',
                    'cod_val.required'   => 'Ingrese la código de validación',

                ];
                $this->validate($request, $rules_isspol, $mensajes_isspol);
            }

                //cambia especialidad, seguro, ingreso o empresa

            $cv                    = $request['validacion_cv_msp'];
            $nc                    = $request['validacion_nc_msp'];
            $sec                   = $request['validacion_sec_msp'];
            $codigo_validacion_msp = $cv . '-' . $nc . '-' . $sec;

                $input_cambios = [
                    'espid'                 => $request['espid'],
                    'id_seguro'             => $request['id_seguro'],
                    'est_amb_hos'           => $request['est_amb_hos'],
                    'tipo_cita'             => $request['tipo_cita'],
                    'id_empresa'            => $request['id_empresa'],
                    'procedencia'           => $request['procedencia'],
                    'paciente_dr'           => $request['paciente_dr'],
                    'supervisa_robles'      => $request['supervisa_robles'],
                    'solo_robles'           => $request['solo_robles'],
                    'observaciones'         => $request['observaciones'],
                    'cortesia'              => $cortesia,
                    'omni'                  => $omni,
                    'id_usuariomod'         => $idusuario,
                    'ip_modificacion'       => $ip_cliente,
                    'fecha_val'             => $request['fecha_val'],
                    'cod_val'               => $request['cod_val'],
                    'validacion_cv_msp'     => $request['validacion_cv_msp'],
                    'validacion_nc_msp'     => $request['validacion_nc_msp'],
                    'validacion_sec_msp'    => $request['validacion_sec_msp'],
                    'codigo_validacion_msp' => $codigo_validacion_msp,
                    'adelantado'            => $request['adelantado'],

                ];
        }
        if ($flag2) {

            $input_proc = [
                'id_procedimiento' => $proc[0],

                'id_usuariomod'    => $idusuario,
                'ip_modificacion'  => $ip_cliente,
            ];
        }

        if ($agenda->proc_consul == '1') {
            foreach ($agproc as $a1) {$aux_ant = $aux_ant . $a1->id_procedimiento . ";";}
            if ($agenda->estado_cita != '4') {
                foreach ($proc as $a2) {$aux = $aux . $a2 . ";";}
            }
        }

        Log_agenda::create([
            'id_agenda'          => $agenda->id,
            'estado_cita_ant'    => $agenda->estado_cita,
            'fechaini_ant'       => $agenda->fechaini,
            'fechafin_ant'       => $agenda->fechafin,
            'estado_ant'         => $agenda->estado,
            'cortesia_ant'       => $agenda->cortesia,
            'observaciones_ant'  => $agenda->observaciones,
            'id_doctor1_ant'     => $agenda->id_doctor1,
            'id_doctor2_ant'     => $agenda->id_doctor2,
            'id_doctor3_ant'     => $agenda->id_doctor3,
            'id_sala_ant'        => $agenda->id_sala,

            'estado_cita'        => $est_cita,
            'fechaini'           => $ini,
            'fechafin'           => $fin,
            'estado'             => $est,
            'cortesia'           => $request['cortesia'],
            'observaciones'      => $request['observaciones'],
            'id_doctor1'         => $request['id_doctor1'],
            'id_doctor2'         => $request['id_doctor2'],
            'id_doctor3'         => $request['id_doctor3'],
            'id_sala'            => $request['id_sala'],

            'descripcion'        => $descripcion,
            'descripcion2'       => $descripcion2,
            'descripcion3'       => $descripcion3,
            'campos_ant'         => "ESP:" . $agenda->espid . " SEG:" . $agenda->id_seguro . " ING:" . $agenda->est_amb_hos . " EMP:" . $agenda->id_empresa . " PRO:" . $agenda->id_procedimiento . ";" . $aux_ant . " PEN:" . $agenda->procedencia . " PDR:" . $agenda->paciente_dr . " FNA:" . $paciente->fecha_nacimiento,
            'campos'             => "ESP:" . $request['espid'] . " SEG:" . $request['id_seguro'] . " ING:" . $request['est_amb_hos'] . " EMP:" . $request['id_empresa'] . " PRO:" . $aux . " PEN:" . $request['procedencia'] . " PDR:" . $request['paciente_dr'] . " FNA:" . $request['fecha_nacimiento'],
            'id_usuarioconfirma' => $agenda->id_usuarioconfirma,

            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,
        ]);

        if ($bandera) {
            $agenda->update($input);
            //envio de correos electronicos
            if ($request['estado_cita'] == '1') //confirmar
            {
                $agenda = Agenda::findOrFail($id);
                $inicio = $agenda->fechaini;

                $id_paciente     = $agenda->id_paciente;
                $paciente2       = DB::table('paciente')->where('id', '=', $id_paciente)->get();
                $usuario         = DB::table('users')->where('id', '=', $paciente2[0]->id_usuario)->get();
                $correo          = $usuario[0]->email;
                $nombre_paciente = $paciente2[0]->nombre1 . " ";
                if ($paciente2[0]->nombre2 != '(N/A)') {
                    $nombre_paciente = $nombre_paciente . $paciente2[0]->nombre2 . " ";
                }
                $nombre_paciente = $nombre_paciente . $paciente2[0]->apellido1 . " ";
                if ($paciente2[0]->apellido2 != '(N/A)') {
                    $nombre_paciente = $nombre_paciente . $paciente2[0]->apellido2 . " ";
                }
                $sala     = DB::table('sala')->where('id', '=', $request['id_sala'])->get();
                $cnombre  = $sala[0]->nombre_sala;
                $hospital = DB::table('hospital')->where('id', '=', $sala[0]->id_hospital)->get();
                $hnombre  = $hospital[0]->nombre_hospital;

                $hdireccion = $hospital[0]->direccion;

                //envio del procedimiento
                $procedimiento_enviar    = null;
                $procedimiento_de_agenda = $agenda->id_procedimiento;
                $procedimiento_a         = DB::table('procedimiento')->where('id', '=', $procedimiento_de_agenda)->get();
                $procedimiento_enviar    = $procedimiento_a[0]->nombre . '+' . $procedimiento_enviar;

                $procedimientos = DB::table('agenda_procedimiento')->where('id_agenda', '=', $id)->get();
                foreach ($procedimientos as $value) {
                    $procedimiento_a = DB::table('procedimiento')->where('id', '=', $value->id_procedimiento)->get();

                    $procedimiento_enviar = $procedimiento_a[0]->nombre . '+' . $procedimiento_enviar;
                }

                $procedimiento_enviar = substr($procedimiento_enviar, 0, -1);

                $avanza = array("procedimiento_nombre" => $procedimiento_enviar, "nombre_paciente" => $nombre_paciente, "inicio" => $request['inicio'], "hospital_nombre" => $hnombre, "consultorio_nombre" => $cnombre, "hospital_direccion" => $hdireccion);
                Mail::send('mails.preagenda', $avanza, function ($msj) use ($correo) {
                    $msj->subject('Reservación de procedimiento médico IECED');
                    $msj->to($correo);
                    $msj->bcc('torbi10@hotmail.com');
                });

            }
        }

        if ($agenda->estado_cita != '4') {
            if ($cambio) {
                $agenda->update($input_cambios);
            }

            $input_paciente = [
                'id_seguro'        => $request['id_seguro'],
                'id_subseguro'     => null,
                'fecha_nacimiento' => $request['fecha_nacimiento'],
            ];
            $paciente->update($input_paciente);
        }

        if ($agenda->proc_consul == '1') {
            if ($flag2) {

                foreach ($agproc as $ad) {
                    $ad->delete();
                }
                $agenda->update($input_proc);
                foreach ($proc as $value) {
                    if ($proc[0] != $value) {
                        AgendaProcedimiento::create([
                            'id_agenda'        => $id,
                            'id_procedimiento' => $value,

                            'ip_creacion'      => $ip_cliente,
                            'ip_modificacion'  => $ip_cliente,
                            'id_usuariocrea'   => $idusuario,
                            'id_usuariomod'    => $idusuario,
                        ]);
                    }
                }
            }
        }
        if (is_null($request['id_ag_artxt']) && !is_null($request['hc'])) {

            Agenda_archivo::create([
                'id_agenda'       => $id,
                'tipo_documento'  => 'txt',
                'texto'           => $request['hc'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ]);
        }

        if (!is_null($request['id_ag_artxt'])) {
            if ($agenda_archivotxt->texto != $request['hc']) {
                $input_hc_txt = [
                    'texto'           => $request['hc'],
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod'   => $idusuario,
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];
                $agenda_archivotxt->update($input_hc_txt);
            }

        }
        if ($request['archivo'] != null) {
            if (!is_null($request['id_ag_ar'])) {

                Agenda_archivo::find($request['id_ag_ar'])->delete();
            }
            $input_archivo = [
                'id_agenda'       => $id,
                'tipo_documento'  => "HCAGENDA",
                'descripcion'     => "Historia Clinica creada de la agenda",
                'ruta'            => "/hc_agenda/",
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            $id_archivo = Agenda_archivo::insertGetId($input_archivo);

            $this->subir_archivo_validacion($request, $id, $id_archivo);

        }
    

        //dd($ruta);

        //return  redirect()->route('preagenda.procedimiento2',['fecha' => $request['unix']]);
        if ($ruta == 'tsalas') {


            return redirect()->route('salas_todas.cargar', ['id' => $agenda->id]);
        }
        return redirect()->route('preagenda.pentax', ['fecha' => $request['unix']])->withInput(['sel_sala' => $request['id_sala']]);

    }



    public function regresar($idagenda, $unix, $url_doctor)
    {

        $agenda = Agenda::find($idagenda);

        if ($url_doctor != '0') {
            return redirect()->route('agenda.fecha', ['id' => $url_doctor, 'i' => $unix]);
        } else {
            return redirect()->route('preagenda.pentax', ['fecha' => $unix])->withInput(['sel_sala' => $agenda->id_sala]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function destroy($id)
    {
    User::where('id', $id)->delete();
    return redirect()->intended('/user-management');
    }*/

    /*public function reunion($id)
    {
    $rolUsuario = Auth::user()->id_tipo_usuario;
    if(in_array($rolUsuario, array(1, 4 ,5)) == false){
    return redirect()->intended('/');
    }

    $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->get(); //3=DOCTORES;
    $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->get(); //6=ENFERMEROS;
    $salas = DB::table('sala')
    ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
    ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
    ->get();

    $agenda = DB::table('agenda')
    ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
    ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
    ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2', 'seguros.color as color')
    ->where('agenda.id', '=', $id)
    ->first();

    //dd($agenda);

    return view('agenda/reunion', ['agenda' => $agenda, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas]);
    }*/

    public function updatereunion(Request $request, $id)
    {

        $agenda     = Agenda::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $descripcion = "SUSPENDIO LA CITA";

        $input = [
            'estado_cita'     => '3',
            'observaciones'   => $request['observaciones'],
            'estado'          => '0',
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];

        Log_agenda::create([
            'id_agenda'          => $agenda->id,
            'estado_cita_ant'    => $agenda->estado_cita,
            'fechaini_ant'       => $agenda->fechaini,
            'fechafin_ant'       => $agenda->fechafin,
            'estado_ant'         => $agenda->estado,
            'cortesia_ant'       => $agenda->cortesia,
            'observaciones_ant'  => $agenda->observaciones,
            'id_doctor1_ant'     => $agenda->id_doctor1,
            'id_doctor2_ant'     => $agenda->id_doctor2,
            'id_doctor3_ant'     => $agenda->id_doctor3,
            'id_sala_ant'        => $agenda->id_sala,

            'estado_cita'        => '3',
            'fechaini'           => $agenda->fechaini,
            'fechafin'           => $agenda->fechafin,
            'estado'             => '0',
            'cortesia'           => $agenda->cortesia,
            'observaciones'      => $request['observaciones'],
            'id_doctor1'         => $agenda->id_doctor1,
            'id_doctor2'         => $agenda->id_doctor2,
            'id_doctor3'         => $agenda->id_doctor3,
            'id_sala'            => $agenda->id_sala,

            'descripcion'        => $descripcion,
            'descripcion2'       => '',
            'id_usuarioconfirma' => $agenda->id_usuarioconfirma,

            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,

        ]);

        $agenda::where('id', $id)
            ->update($input);

        return redirect()->route('agenda.agenda', ['id' => $agenda->id_doctor1, 'i' => 0]);

    }

    public function reunionsearch($id)
    {

        $agenda = Agenda::find($id);

        return view('agenda/editreunion', ['id' => $id, 'agenda' => $agenda]);
    }

    public function reunionedit($id)
    {

        $agenda = Agenda::find($id);
        $salas  = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();

        //agendas

        $cagenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 1)->where('agenda.estado', '=', '1')
            ->get();

        $cagenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 0)->where('agenda.estado', '=', '1')
            ->get();

        $cagenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->select('agenda.*', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')->where('agenda.estado', '=', '1')
            ->get();
        //agendas

        return view('agenda/editreunion2', ['id' => $id, 'agenda' => $agenda, 'salas' => $salas, 'cagenda' => $cagenda, 'cagenda2' => $cagenda2, 'cagenda3' => $cagenda3]);
    }

    public function updatereunion2(Request $request, $id)
    {

        $agenda     = Agenda::findOrFail($id);
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($request['estado_cita'] == 3) {

            $descripcion = "SUSPENDIO LA CITA";
            $fechaini    = $agenda->fechaini;
            $fechafin    = $agenda->fechafin;
            $estado_cita = '3';
            $estado      = '0';
            $input       = [
                'estado_cita'     => '3',
                'observaciones'   => $request['observaciones'],
                'estado'          => '0',
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
            ];
        }

        if ($request['estado_cita'] == '2') //reagendar
        {
            $estado_cita = '1';
            $estado      = '1';
            $fechaini    = $request['inicio'];
            $fechafin    = $request['fin'];
            $descripcion = "RE-AGENDO LA CITA";
            $this->validateInput3_2($request, $id);
            $input = [
                'nro_reagenda'    => $agenda->nro_reagenda + 1,
                'estado_cita'     => '1',
                'observaciones'   => $request['observaciones'],
                'id_usuariomod'   => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'fechaini'        => $request['inicio'],
                'fechafin'        => $request['fin'],
                'id_doctor1'      => $request['id_doctor1'],
                'id_sala'         => $request['id_sala'],
            ];
        }

        if ($request['estado_cita'] == '2' || $request['estado_cita'] == '3') {
            $this->validateInput4($request);

            Log_agenda::create([
                'id_agenda'          => $agenda->id,
                'estado_cita_ant'    => $agenda->estado_cita,
                'fechaini_ant'       => $agenda->fechaini,
                'fechafin_ant'       => $agenda->fechafin,
                'estado_ant'         => $agenda->estado,
                'cortesia_ant'       => $agenda->cortesia,
                'observaciones_ant'  => $agenda->observaciones,
                'id_doctor1_ant'     => $agenda->id_doctor1,
                'id_doctor2_ant'     => $agenda->id_doctor2,
                'id_doctor3_ant'     => $agenda->id_doctor3,
                'id_sala_ant'        => $agenda->id_sala,

                'estado_cita'        => $estado_cita,
                'fechaini'           => $fechaini,
                'fechafin'           => $fechafin,
                'estado'             => $estado,
                'cortesia'           => $agenda->cortesia,
                'observaciones'      => $request['observaciones'],
                'id_doctor1'         => $agenda->id_doctor1,
                'id_doctor2'         => $agenda->id_doctor2,
                'id_doctor3'         => $agenda->id_doctor3,
                'id_sala'            => $request['id_sala'],

                'descripcion'        => $descripcion,
                'descripcion2'       => '',
                'id_usuarioconfirma' => $agenda->id_usuarioconfirma,

                'id_usuariomod'      => $idusuario,
                'id_usuariocrea'     => $idusuario,
                'ip_modificacion'    => $ip_cliente,
                'ip_creacion'        => $ip_cliente,

            ]);

            $agenda::where('id', $id)
                ->update($input);

        }

        return redirect()->route('agenda.agenda', ['id' => $agenda->id_doctor1, 'i' => 0]);

    }

    public function suspendidas($id, $i)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 4, 5)) == false) {
            return redirect()->intended('/');
        }
        $user = DB::table('users')->where([['id_tipo_usuario', '=', 3], ['id', '=', $id]])->get(); //3=DOCTORES;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $doctor = User::find($id);
        $salas  = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital', 'hospital.direccion as direccion_hospital')
            ->get();

        $paciente = paciente::find($i);

        //SI NO SE ENCUENTRA EL PACIENTE
        if ($paciente == array() && $i != '0') {

            return redirect()->route('agenda.paciente', ['id' => $id, 'i' => $i]);
        }

        $user      = DB::table('users')->where([['id_tipo_usuario', '=', 3]])->get(); //3=DOCTORES;
        $enfermero = DB::table('users')->where([['id_tipo_usuario', '=', 6]])->get(); //6=ENFERMEROS;
        // Redirect to user list if updating user wasn't existed
        if ($user == null || count($user) == 0) {
            return redirect()->intended('/agenda');
        }

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 1)
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '0']]);
            })
            ->get();

        $agenda3 = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where('proc_consul', '=', 0)
            ->where(function ($query) use ($id) {
                $query->where([['id_doctor1', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['id_doctor2', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['id_doctor3', '=', $id], ['agenda.estado', '=', '0']]);
            })
            ->get();

        $agenda2 = DB::table('agenda')->where('proc_consul', '=', 2)
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->select('agenda.*', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1')
            ->where(function ($query) use ($id) {
                $query->where([['agenda.id_doctor1', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['agenda.id_doctor2', '=', $id], ['agenda.estado', '=', '0']])
                    ->orWhere([['agenda.id_doctor3', '=', $id], ['agenda.estado', '=', '0']]);
            })
            ->get();

        $procedimiento = Procedimiento::all();
        $empresa       = Empresa::all();
        $seguro        = Seguro::all();
        $especialidad  = DB::table('user_espe')->join('especialidad', 'user_espe.espid', '=', 'especialidad.id')
            ->select('user_espe.*', 'especialidad.nombre as enombre')->where('usuid', '=', $id)->get();
        return view('agenda/calendario', ['users' => $user, 'id' => $id, 'salas' => $salas, 'doctor' => $doctor, 'paciente' => $paciente, 'procedimiento' => $procedimiento, 'procedimiento2' => $procedimiento, 'i' => $i, 'agenda' => $agenda, 'agenda2' => $agenda2, 'agenda3' => $agenda3, 'especialidad' => $especialidad, 'empresa' => $empresa, 'enfermero' => $enfermero, 'seguro' => $seguro, 'versuspendidas' => '1']);
    }

    public function load($name)
    {
        $path = storage_path() . '/app/hc_agenda/' . $name;
        if (file_exists($path)) {

            return Response::make(file_get_contents($path), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $name . '"',
            ]);
        }
    }

    public function eliminarfoto($id)
    {

        $archivo   = DB::table('agenda_archivo')->where('id', '=', $id)->get();
        $id_agenda = $archivo[0]->id_agenda;
        $agenda    = DB::table('agenda')->where('id', '=', $id_agenda)->get();
        $id_doctor = $agenda[0]->id_doctor1;
        $foto      = $archivo[0]->archivo;

        $r1 = Storage::disk('hc_agenda')->delete($foto);
        if ($r1) {
            agenda_archivo::destroy($id);
        }
        return redirect()->route('preagenda.edit', ['agenda' => $id_agenda]);

    }

    public function salas_todas(Request $request)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        Cookie::queue('ruta_p', 'tsalas', '1000');

        //solo para pentax id_hospital 2
        $salas = DB::table('sala')->where('id_hospital', '=', '2')->where('proc_consul_sala', '=', 1)->get();
        $i     = 0;
        $fecha = $request['fecha'];

        if ($fecha == "") {
            $fecha = date('Y-m-d');

        } else {
            /*$valoresfecha =  explode("/", $fecha);
        $fecha = "";
        $j= 0;

        foreach($valoresfecha as $value){

        if($j < 2)
        {
        $fecha = $fecha.$value.'-';

        }
        else{
        $fecha =  $fecha.$value;
        }
        $j = $j+1;
        };
        if($j == 1)
        {
        $fecha = substr($fecha, 0, -1);
        $fecha = date('Y-m-d', $fecha);

        }*/
        }

        $fechainicio = strtotime('-15 days', strtotime($fecha));
        $fechainicio = date('Y-m-d', $fechainicio);

        $fechafinal = strtotime('+15 days', strtotime($fecha));
        $fechafinal = date('Y-m-d', $fechafinal);

        /*$dia = date('w', strtotime($fecha));
        if($dia == '0'){
        $fechainicio = strtotime ( '-6 days' , strtotime ( $fecha));
        $fechainicio = date ( 'Y-m-d' , $fechainicio);
        //$fechafinal = $fecha;
        $fechafinal = strtotime ( '+1 days' , strtotime ( $fecha));
        $fechafinal = date ( 'Y-m-d' , $fechafinal);
        }

        if($dia == '1'){
        $fechainicio = $fecha;
        $fechafinal = strtotime ( '+6 days' , strtotime ( $fecha));
        $fechafinal = date ( 'Y-m-d' , $fechafinal);
        }
        if($dia == '2'){
        $fechainicio = strtotime ( '-1 days' , strtotime ( $fecha));
        $fechainicio = date ( 'Y-m-d' , $fechainicio);
        $fechafinal = strtotime ( '+5 days' , strtotime ( $fecha));
        $fechafinal = date ( 'Y-m-d' , $fechafinal);
        }
        if($dia == '3'){
        $fechainicio = strtotime ( '-2 days' , strtotime ( $fecha));
        $fechainicio = date ( 'Y-m-d' , $fechainicio);
        $fechafinal = strtotime ( '+4 days' , strtotime ( $fecha));
        $fechafinal = date ( 'Y-m-d' , $fechafinal);
        }
        if($dia == '4'){
        $fechainicio = strtotime ( '-3 days' , strtotime ( $fecha));
        $fechainicio = date ( 'Y-m-d' , $fechainicio);
        $fechafinal = strtotime ( '+3 days' , strtotime ( $fecha));
        $fechafinal = date ( 'Y-m-d' , $fechafinal);
        }
        if($dia == '5'){
        $fechainicio = strtotime ( '-4 days' , strtotime ( $fecha));
        $fechainicio = date ( 'Y-m-d' , $fechainicio);
        $fechafinal = strtotime ( '+2 days' , strtotime ( $fecha));
        $fechafinal = date ( 'Y-m-d' , $fechafinal);
        }
        if($dia == '6'){
        $fechainicio = strtotime ( '-5 days' , strtotime ( $fecha));
        $fechainicio = date ( 'Y-m-d' , $fechainicio);
        $fechafinal = strtotime ( '+1 days' , strtotime ( $fecha));
        $fechafinal = date ( 'Y-m-d' , $fechafinal);
        }
        //return $fechainicio.' // '.$fechafinal;*/

        $agendas = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->leftjoin('users as d', 'd.id', 'agenda.id_doctor1')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')
            ->where('proc_consul', '=', 1)
            ->where('agenda.estado', '!=', '0')
            ->whereBetween('fechaini', [$fechainicio, $fechafinal])->get();

        //dd($sel_sala);
        //SEMAFORO
        $sem_controller = new laboratorio\SemaforoController;
        $pentax_pend    = $sem_controller->Cargar_pendientes(date('Y-m-d'));
        //dd($pentax_pend);
        //SEMAFORO
        $reuniones = DB::table('agenda as a')->where('a.estado', 1)->where('a.proc_consul', 2)->whereBetween('a.fechaini', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->join('users as d', 'd.id', 'a.id_doctor1')->join('users as uc', 'uc.id', 'a.id_usuariocrea')->join('users as um', 'um.id', 'a.id_usuariomod')->select('a.*', 'd.nombre1 as dnombre', 'd.apellido1 as dapellido', 'uc.nombre1 as ucnombre1', 'uc.apellido1 as ucapellido1', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->get();
        //dd($arreglo);
        return view('preagenda/salas_todas', ['agendas' => $agendas, 'fecha' => $fecha, 'reuniones' => $reuniones, 'pentax_pend' => $pentax_pend, 'salas' => $salas]);
    }

    public function st_cargar($id)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $agenda = Agenda::find($id);

        Cookie::queue('ruta_p', 'tsalas', '1000');

        //solo para pentax id_hospital 2
        $salas = DB::table('sala')->where('proc_consul_sala', '=', 1)->where('id_hospital', '=', '2')->get();
        $i     = 0;
        $fecha = $agenda->fechaini;

        if ($fecha == "") {
            $fecha = date('Y-m-d');

        }

        $fechainicio = strtotime('-15 days', strtotime($fecha));
        $fechainicio = date('Y-m-d', $fechainicio);

        $fechafinal = strtotime('+15 days', strtotime($fecha));
        $fechafinal = date('Y-m-d', $fechafinal);

        $agendas = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->leftjoin('users as d', 'd.id', 'agenda.id_doctor1')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')
            ->where('proc_consul', '=', 1)
            ->where('agenda.estado', '!=', '0')
            ->whereBetween('fechaini', [$fechainicio, $fechafinal])->get();

        //dd($sel_sala);
        //SEMAFORO
        $sem_controller = new laboratorio\SemaforoController;
        $pentax_pend    = $sem_controller->Cargar_pendientes(date('Y-m-d'));
        //dd($pentax_pend);
        //SEMAFORO
        $reuniones = DB::table('agenda as a')->where('a.estado', 1)->where('a.proc_consul', 2)->whereBetween('a.fechaini', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->join('users as d', 'd.id', 'a.id_doctor1')->join('users as uc', 'uc.id', 'a.id_usuariocrea')->join('users as um', 'um.id', 'a.id_usuariomod')->select('a.*', 'd.nombre1 as dnombre', 'd.apellido1 as dapellido', 'uc.nombre1 as ucnombre1', 'uc.apellido1 as ucapellido1', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->get();
        //dd($arreglo);
        return view('preagenda/salas_todas', ['agendas' => $agendas, 'fecha' => $fecha, 'reuniones' => $reuniones, 'pentax_pend' => $pentax_pend, 'salas' => $salas]);
    }

    public function salas_todas_ajax(Request $request)
    {

        //return "ok";
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //solo para pentax id_hospital 2
        $salas = DB::table('sala')->where('proc_consul_sala', '=', 1)->where('id_hospital', '=', '2')->get();
        $i     = 0;
        $fecha = $request['fecha'];

        if ($fecha == "") {
            $fecha = date('Y-m-d');

        }

        $fechainicio = strtotime('-15 days', strtotime($fecha));
        $fechainicio = date('Y-m-d', $fechainicio);

        $fechafinal = strtotime('+15 days', strtotime($fecha));
        $fechafinal = date('Y-m-d', $fechafinal);

        $agendas = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->leftjoin('users as d', 'd.id', 'agenda.id_doctor1')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')
            ->where('proc_consul', '=', 1)
            ->where('agenda.estado', '!=', '0')
            ->whereBetween('fechaini', [$fechainicio, $fechafinal])->get();

        //dd($sel_sala);
        //SEMAFORO
        $sem_controller = new laboratorio\SemaforoController;
        $pentax_pend    = $sem_controller->Cargar_pendientes(date('Y-m-d'));
        //dd($pentax_pend);
        //SEMAFORO
        $reuniones = DB::table('agenda as a')->where('a.estado', 1)->where('a.proc_consul', 2)->whereBetween('a.fechaini', [$fecha . ' 00:00:00', $fecha . ' 23:59:00'])->join('users as d', 'd.id', 'a.id_doctor1')->join('users as uc', 'uc.id', 'a.id_usuariocrea')->join('users as um', 'um.id', 'a.id_usuariomod')->select('a.*', 'd.nombre1 as dnombre', 'd.apellido1 as dapellido', 'uc.nombre1 as ucnombre1', 'uc.apellido1 as ucapellido1', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->get();
        //dd($arreglo);
        return view('preagenda/salas_todas_div', ['agendas' => $agendas, 'fecha' => $fecha, 'reuniones' => $reuniones, 'pentax_pend' => $pentax_pend, 'salas' => $salas]);
    }
    public function to_excel(Request $request)
    {

        $intervalo = 15; //CADA 30 MINUTOS
        //solo para pentax id_hospital 2
        $salas = DB::table('sala')->where('id_hospital', '=', '2')->where('proc_consul_sala', '1')->get();
        $i     = 0;
        $fecha = $request['fecha'];
        //$sel_sala = $request['sel_sala'];

        //dd($fecha);

        $agendas = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users', 'agenda.id_usuariocrea', '=', 'users.id')
            ->join('sala', 'sala.id', 'agenda.id_sala')
            ->leftjoin('users as d', 'd.id', 'agenda.id_doctor1')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('historiaclinica as hc', 'hc.id_agenda', 'agenda.id')
            ->leftjoin('seguros as sh', 'sh.id', 'hc.id_seguro')
            ->join('procedimiento', 'agenda.id_procedimiento', '=', 'procedimiento.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.observacion as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'empresa.nombre_corto', 'sh.nombre as shnombre', 'paciente.fecha_nacimiento')
            ->where('proc_consul', '=', 1)
            ->where('sala.id_hospital', '2')
            ->where('agenda.estado', '!=', '0')
            ->whereBetween('fechaini', [$fecha . ' 0:00:00', $fecha . ' 23:59:00'])->orderBy('fechaini')->orderBy('fechafin')->get();

        if ($agendas->count() > 0) {
            $rango_inicio = substr($agendas->first()->fechaini, 11, 8);
            $rango_final  = substr($agendas->last()->fechafin, 11, 8);

            $horaini = new DateTime($rango_inicio);
            $horafin = new DateTime($rango_final);
            //return $horafin;

            $intervalo = new DateInterval('PT' . $intervalo . 'M');
            //dd($agendas);
            $periodo = new DatePeriod($horaini, $intervalo, $horafin);

            Excel::create('Agenda Pentax-' . $fecha, function ($excel) use ($agendas, $salas, $periodo, $fecha) {

                $excel->sheet('Examenes', function ($sheet) use ($agendas, $salas, $periodo, $fecha) {

                    $sheet->mergeCells('C3:K3');

                    $mes = substr($fecha, 5, 2);

                    $mes_letra = '';
                    if ($mes == '01') {$mes_letra = "ENERO";}
                    if ($mes == '02') {$mes_letra = "FEBRERO";}
                    if ($mes == '03') {$mes_letra = "MARZO";}
                    if ($mes == '04') {$mes_letra = "ABRIL";}
                    if ($mes == '05') {$mes_letra = "MAYO";}
                    if ($mes == '06') {$mes_letra = "JUNIO";}
                    if ($mes == '07') {$mes_letra = "JULIO";}
                    if ($mes == '08') {$mes_letra = "AGOSTO";}
                    if ($mes == '09') {$mes_letra = "SEPTIEMBRE";}
                    if ($mes == '10') {$mes_letra = "OCTUBRE";}
                    if ($mes == '11') {$mes_letra = "NOVIEMBRE";}
                    if ($mes == '12') {$mes_letra = "DICIEMBRE";}
                    $fecha2 = 'FECHA: ' . substr($fecha, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha, 0, 4);

                    $sheet->cells('A1:L5', function ($cells) {
                        // manipulate the range of cells
                        $cells->setAlignment('center');
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cell('C3', function ($cell) use ($fecha2) {
                        // manipulate the cel
                        $cell->setValue('AGENDA PENTAX ' . $fecha2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];
                    $l      = 2;

                    $s = 0;
                    foreach ($salas as $sala) {
                        if ($sala->proc_consul_sala == '1') {
                            $a_sala[$s] = $sala->id;
                            $l2         = $l + 2;
                            //dd($letras[$l2]);
                            $sheet->mergeCells($letras[$l] . '4:' . $letras[$l2] . '4');
                            $sheet->cell($letras[$l] . '4', function ($cell) use ($sala) {
                                // manipulate the cel
                                $cell->setValue($sala->nombre_sala);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell($letras[$l] . '5', function ($cell) use ($sala) {
                                // manipulate the cel
                                $cell->setValue('APELLIDOS');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell($letras[$l + 1] . '5', function ($cell) use ($sala) {
                                // manipulate the cel
                                $cell->setValue('NOMBRES');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell($letras[$l + 2] . '5', function ($cell) use ($sala) {
                                // manipulate the cel
                                $cell->setValue('PROCEDIMIENTO');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $l = $l + 3;
                            $s++;
                        }
                    }
                    //dd($a_sala);
                    $sheet->cell('A5', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('No.');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B5', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Horarios');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i = 6;
                    $x = 1;
                    $a_per= [];
                    //dd($periodo);
                    foreach ($periodo as $hora) {
                        //dd($hora);
                        $a_per[$x - 1] = $hora->format('H:i');
                        //dd($a_per);
                        $sheet->cell('A' . $i, function ($cell) use ($x) {
                            // manipulate the cel
                            $cell->setValue($x);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $i, function ($cell) use ($hora) {
                            // manipulate the cel
                            $cell->setValue($hora->format('H:i'));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell($letras[($salas->count()) * 3 + 2] . $i, function ($cell) use ($hora) {
                            // manipulate the cel
                            $cell->setValue($hora->format('H:i'));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        for ($b = 2; $b < ($salas->count()) * 3 + 2; $b++) {

                            $sheet->cell($letras[$b] . $i, function ($cell) use ($hora) {
                                // manipulate the cel
                                $cell->setValue(' ');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                        }
                        //agenda

                        $i++;
                        $x++;

                    }
                    //dd($a_per);
                    //dd($a_sala);
                    foreach ($agendas as $value) {

                        $k_x = array_search(Date('H:i', strtotime($value->fechaini)), $a_per);
                        $k_y = array_search($value->id_sala, $a_sala);
                        //dd($k_x,$k_y);
                        $k_x = $k_x + 6;

                        $a_horaini = new DateTime($value->fechaini);
                        $a_horafin = new DateTime($value->fechafin);

                        $a_intervalo = new DateInterval('PT15M');

                        $a_periodo = new DatePeriod($a_horaini, $a_intervalo, $a_horafin);

                        $a_contador = 0;
                        foreach ($a_periodo as $a_hora) {
                            $a_contador++;
                        }
                        //dd($a_contador);
                        if ($a_contador > 1) {
                            $k_x2 = $k_x + $a_contador - 1;
                            $sheet->mergeCells($letras[$k_y * 3 + 2] . $k_x . ':' . $letras[$k_y * 3 + 2] . $k_x2);
                            $sheet->mergeCells($letras[$k_y * 3 + 3] . $k_x . ':' . $letras[$k_y * 3 + 3] . $k_x2);
                            $sheet->mergeCells($letras[$k_y * 3 + 4] . $k_x . ':' . $letras[$k_y * 3 + 4] . $k_x2);
                        }

                        $sheet->cell($letras[$k_y * 3 + 2] . $k_x, function ($cell) use ($value) {
                            // manipulate the cel
                            $x_apellido2 = $value->papellido2;
                            if ($value->papellido2 == '(N/A)' || $value->papellido2 == 'N/A') {
                                $x_apellido2 = '';
                            }
                            $edad = '';
                            if (!is_null($value->fecha_nacimiento)) {
                                $fecha           = $value->fecha_nacimiento;
                                list($Y, $m, $d) = explode("-", $fecha);
                                $edad            = (date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y);

                            }

                            $cell->setValue($value->papellido1 . ' ' . $x_apellido2 . "\n" . 'Edad:' . $edad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontSize(10);
                            if ($value->supervisa_robles == '1') {
                                $cell->setFontColor('#FF0000');
                            }
                            if ($value->solo_robles == '1') {
                                $cell->setFontColor('#8A2BE2');
                            }
                            //$cell->setAlignment('center');

                        });

                        $sheet->getStyle($letras[$k_y * 3 + 2] . $k_x)->getAlignment()->setWrapText(true)->setVertical('center');

                        $sheet->cell($letras[$k_y * 3 + 3] . $k_x, function ($cell) use ($value) {
                            // manipulate the cel
                            $x_nombre2 = $value->pnombre2;
                            $xseguro   = '';
                            if ($value->shnombre == null) {
                                $xseguro = $value->nombre_seguro;
                            } else {
                                $xseguro = $value->shnombre;
                            }
                            if ($value->pnombre2 == '(N/A)' || $value->pnombre2 == 'N/A') {
                                //regresar aqui
                                $x_nombre2 = '';
                            }
                            $cell->setValue($value->pnombre1 . ' ' . $x_nombre2 . "\n" . $value->nombre_corto . '/' . $xseguro);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontSize(10);
                            if ($value->supervisa_robles == '1') {
                                $cell->setFontColor('#FF0000');
                            }
                            if ($value->solo_robles == '1') {
                                $cell->setFontColor('#8A2BE2');
                            }
                        });
                        $sheet->getStyle($letras[$k_y * 3 + 3] . $k_x)->getAlignment()->setWrapText(true)->setVertical('center');

                        $sheet->cell($letras[$k_y * 3 + 4] . $k_x, function ($cell) use ($value) {
                            // manipulate the cel
                            $procedimientos        = $value->nombre_procedimiento;
                            $agenda_procedimientos = DB::table('agenda_procedimiento as ag')->where('ag.id_agenda', $value->id)->join('procedimiento as p', 'p.id', 'ag.id_procedimiento')->select('p.observacion')->get();
                            foreach ($agenda_procedimientos as $proc) {
                                $procedimientos = $procedimientos . '+' . $proc->observacion;
                            }

                            $procedimientos2 = '';
                            $bandera         = 0;
                            $pentax          = DB::table('pentax')->where('id_agenda', $value->id)->first();
                            if (!is_null($pentax)) {
                                $pentax_procs = DB::table('pentax_procedimiento as px')->where('px.id_pentax', $pentax->id)->join('procedimiento as p', 'p.id', 'px.id_procedimiento')->select('p.observacion')->get();
                                foreach ($pentax_procs as $pproc) {
                                    if ($bandera == 0) {
                                        $procedimientos2 = $pproc->observacion;
                                        $bandera         = 1;
                                    } else {
                                        $procedimientos2 = $procedimientos2 . '+' . $pproc->observacion;
                                    }

                                }

                            }
                            if ($procedimientos2 != '') {
                                $procedimientos = $procedimientos2;
                            }
                            $amb_hos = '';
                            if ($value->est_amb_hos == '0') {
                                $amb_hos = 'AMBULATORIO';
                            } else {
                                $amb_hos = 'HOSPITALIZADO';
                            }
                            $cell->setValue($procedimientos . "\n" . $amb_hos);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontSize(10);
                            if ($value->supervisa_robles == '1') {
                                $cell->setFontColor('#FF0000');
                            }
                            if ($value->solo_robles == '1') {
                                $cell->setFontColor('#8A2BE2');
                            }
                        });
                        $sheet->getStyle($letras[$k_y * 3 + 4] . $k_x)->getAlignment()->setWrapText(true)->setVertical('center');
                    }
                    //dd($a_per,$a_sala);
                    $l2 = 2;
                    $i++;
                    foreach ($salas as $sala) {
                        if ($sala->proc_consul_sala == '1') {

                            $sheet->cell($letras[$l2] . $i, function ($cell) use ($sala) {
                                // manipulate the cel
                                $cell->setValue($sala->nombre_sala);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $l2++;
                        }
                    }
                    $sheet->cell($letras[$l2] . $i, function ($cell) use ($sala) {
                        // manipulate the cel
                        $cell->setValue('TOTAL');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i++;
                    $sheet->cell('B' . $i, function ($cell) use ($sala) {
                        // manipulate the cel
                        $cell->setValue('PACIENTES');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $Total_sala = 0;
                    $l2         = 2;
                    foreach ($salas as $sala) {
                        if ($sala->proc_consul_sala == '1') {
                            $cantidad_sala = DB::table('agenda')->where('proc_consul', '=', 1)->where('agenda.estado', '!=', '0')
                                ->where('id_sala', $sala->id)->whereBetween('fechaini', [$fecha . ' 0:00:00', $fecha . ' 23:59:00'])->count();
                            $Total_sala = $Total_sala + $cantidad_sala;
                            $sheet->cell($letras[$l2] . $i, function ($cell) use ($cantidad_sala) {
                                // manipulate the cel
                                $cell->setValue($cantidad_sala);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $l2++;
                        }
                    }
                    $sheet->cell($letras[$l2] . $i, function ($cell) use ($Total_sala) {
                        // manipulate the cel
                        $cell->setValue($Total_sala);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i++;
                    $sheet->cell('B' . $i, function ($cell) use ($sala) {
                        // manipulate the cel
                        $cell->setValue('PROCEDIMIENTOS');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $Total_p = 0;
                    $l2      = 2;
                    foreach ($salas as $sala) {
                        if ($sala->proc_consul_sala == '1') {
                            $cantidad_sala = DB::table('agenda')->where('proc_consul', '=', 1)->where('agenda.estado', '!=', '0')
                                ->where('id_sala', $sala->id)->whereBetween('fechaini', [$fecha . ' 0:00:00', $fecha . ' 23:59:00'])->count();
                            $cantidad_p = DB::table('agenda as a')->where('a.proc_consul', '=', 1)->where('a.estado', '!=', '0')
                                ->where('a.id_sala', $sala->id)->whereBetween('a.fechaini', [$fecha . ' 0:00:00', $fecha . ' 23:59:00'])->join('agenda_procedimiento as p', 'p.id_agenda', 'a.id')->select('p.*')->count();
                            //dd($cantidad_p);
                            $cantidad_p2 = $cantidad_p + $cantidad_sala;
                            $Total_p     = $Total_p + $cantidad_p2;

                            $sheet->cell($letras[$l2] . $i, function ($cell) use ($cantidad_p2) {
                                // manipulate the cel
                                $cell->setValue($cantidad_p2);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $l2++;
                        }
                    }
                    $sheet->cell($letras[$l2] . $i, function ($cell) use ($Total_p) {
                        // manipulate the cel
                        $cell->setValue($Total_p);
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                });
                $excel->getActiveSheet()->getColumnDimension("C")->setWidth(20)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("D")->setWidth(20)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("E")->setWidth(25)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("F")->setWidth(20)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("G")->setWidth(20)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("H")->setWidth(25)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("I")->setWidth(20)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("J")->setWidth(20)->setAutosize(false);
                $excel->getActiveSheet()->getColumnDimension("K")->setWidth(25)->setAutosize(false);
            })->export('xlsx');

        } else {

            Excel::create('Agenda Pentax-' . $fecha, function ($excel) use ($salas, $fecha) {

                $excel->sheet('Examenes', function ($sheet) use ($salas, $fecha) {

                    $sheet->mergeCells('C3:K3');

                    $mes = substr($fecha, 5, 2);

                    $mes_letra = '';
                    if ($mes == '01') {$mes_letra = "ENERO";}
                    if ($mes == '02') {$mes_letra = "FEBRERO";}
                    if ($mes == '03') {$mes_letra = "MARZO";}
                    if ($mes == '04') {$mes_letra = "ABRIL";}
                    if ($mes == '05') {$mes_letra = "MAYO";}
                    if ($mes == '06') {$mes_letra = "JUNIO";}
                    if ($mes == '07') {$mes_letra = "JULIO";}
                    if ($mes == '08') {$mes_letra = "AGOSTO";}
                    if ($mes == '09') {$mes_letra = "SEPTIEMBRE";}
                    if ($mes == '10') {$mes_letra = "OCTUBRE";}
                    if ($mes == '11') {$mes_letra = "NOVIEMBRE";}
                    if ($mes == '12') {$mes_letra = "DICIEMBRE";}
                    $fecha2 = 'FECHA: ' . substr($fecha, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha, 0, 4);

                    $sheet->cells('A1:L5', function ($cells) {
                        // manipulate the range of cells
                        $cells->setAlignment('center');
                        $cells->setFontWeight('bold');
                    });

                    $sheet->cell('C3', function ($cell) use ($fecha2) {
                        // manipulate the cel
                        $cell->setValue('AGENDA PENTAX ' . $fecha2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
                    $l      = 2;

                    $s = 0;
                    foreach ($salas as $sala) {
                        if ($sala->proc_consul_sala == '2') {
                            $a_sala[$s] = $sala->id;
                            $l2         = $l + 2;
                            //dd($letras[$l2]);
                            $sheet->mergeCells($letras[$l] . '4:' . $letras[$l2] . '4');
                            $sheet->cell($letras[$l] . '4', function ($cell) use ($sala) {
                                // manipulate the cel
                                $cell->setValue($sala->nombre_sala);
                                $cell->setAlignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell($letras[$l] . '5', function ($cell) use ($sala) {
                                // manipulate the cel
                                $cell->setValue('APELLIDOS');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell($letras[$l + 1] . '5', function ($cell) use ($sala) {
                                // manipulate the cel
                                $cell->setValue('NOMBRES');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell($letras[$l + 2] . '5', function ($cell) use ($sala) {
                                // manipulate the cel
                                $cell->setValue('PROCEDIMIENTO');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $l = $l + 3;
                            $s++;
                        }
                    }
                    //dd($a_sala);
                    $sheet->cell('A5', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('No.');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B5', function ($cell) {
                        // manipulate the cel
                        $cell->setValue('Horarios');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $i = 6;
                    $x = 1;

                });
            })->export('xlsx');

        }

    }

    public function desplazamiento($id, $start, $end, $sala)
    {

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $agendamiento = Agenda::find($id);
        $id_sala      = $sala;
        $start        = substr($start, 0, 10);
        $end          = substr($end, 0, 10);
        date_default_timezone_set('UTC');
        $start2   = date('Y-m-d H:i', $start);
        $end2     = date('Y-m-d H:i', $end);
        $iniciar  = strtotime('+1 minute', strtotime($start2));
        $terminar = strtotime('-1 minute', strtotime($end2));
        $iniciar  = date('Y-m-d H:i', $iniciar);
        $terminar = date('Y-m-d H:i', $terminar);
        //return $terminar;

        $fechaini = $start2;
        $fechafin = $end2;

        $nro_reagenda = $agendamiento->nro_reagenda;
        $id_doctor    = $agendamiento->id_doctor1;
        //return $start2.' '.$terminar;

        if ($id_doctor != null) {
            //return $id_doctor;
            //VALIDA SI TIENE UNA CONSULTA
            if ($id_doctor != '9666666666') {
                $cant_consultas = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                    return $query->where('id_doctor1', '=', $id_doctor)
                        ->orWhere('id_doctor2', '=', $id_doctor)
                        ->orWhere('id_doctor3', '=', $id_doctor);
                })
                    ->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                        return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                            )
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                            })
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                            });
                    })
                    ->where(function ($query) {
                        return $query->where('estado', '<>', '0')->where('proc_consul', '0');
                    })
                    ->count();

                if ($cant_consultas > 0) {
                    return "Doctor posee " . $cant_consultas . " consulta(s)";
                }

                // VH: 10102018 VALIDA SI TIENE UNA REUNION
                $cant_reuniones = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                    return $query->where('id_doctor1', '=', $id_doctor)
                        ->orWhere('id_doctor2', '=', $id_doctor)
                        ->orWhere('id_doctor3', '=', $id_doctor);
                })
                    ->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                        return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                            )
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                            })
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                            });
                    })
                    ->where(function ($query) {
                        return $query->where('estado', '<>', '0')->where('proc_consul', '2');
                    })
                    ->count();

                if ($cant_reuniones > 0) {
                    return "Doctor posee " . $cant_reuniones . " reunion(es)";
                }

                // VH: 10102018 VALIDA SI TIENE PROCEDIMIENTO
                $cant_proc = DB::table('agenda')->where('id', '<>', $id)->where('id_sala', '<>', $id_sala)->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                    return $query->where('id_doctor1', '=', $id_doctor)
                        ->orWhere('id_doctor2', '=', $id_doctor)
                        ->orWhere('id_doctor3', '=', $id_doctor);
                })
                    ->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                        return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                            )
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                            })
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                            });
                    })
                    ->where(function ($query) {
                        return $query->where('estado', '<>', '0')->where('proc_consul', '1');
                    })
                    ->count();

                if ($cant_proc > 0) {
                    return "Doctor posee " . $cant_proc . " procedimiento(s) en otra sala";
                }

                // HORARIO LABORABLE
                $horariocontroller = new HorarioController();
                $horario           = $horariocontroller->valida_horarioxdoctor_dia_2($id_doctor, $iniciar, $terminar);

                if ($horario == "INI") {
                    return "Fecha de inicio fuera del Horario Laborable del Doctor";
                }

                if ($horario == "FIN") {
                    return "Fecha de fin fuera del Horario Laborable del Doctor";
                }

            }
        }

        $agenda_nueva = DB::table('agenda')->where('id', '<>', $id)->where('id_sala', $id_sala)->where('id', '<>', '0')
            ->where(function ($query) use ($iniciar, $terminar) {
                return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                        $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                    )
                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                        $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                    })
                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                        $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                    });
            })
            ->where(function ($query) {
                return $query->where('estado', '<>', '0')->where('estado_cita', '<>', '2')->where('proc_consul', '<', '2');
            })
            ->orderBy('fechaini', 'asc')->get();

        date_default_timezone_set('America/Guayaquil');
        //return $start2.' '.$terminar;
        //return $agenda_nueva;
        $cuenta = count($agenda_nueva);
        //return $agenda_nueva;

        if ($cuenta != 0) {
            do {

                $tfin     = strtotime($agenda_nueva[0]->fechafin);
                $tinicio  = strtotime($agenda_nueva[0]->fechaini);
                $tiempo   = $tfin - $tinicio;
                $start2   = $end2;
                $iniciar  = strtotime('+1 minute', strtotime($end2));
                $iniciar  = date('Y-m-d H:i', $iniciar);
                $end2     = strtotime('+' . $tiempo . ' seconds', strtotime($end2));
                $end2     = date('Y-m-d H:i', $end2);
                $terminar = strtotime('-1 minute', strtotime($end2));
                $terminar = date('Y-m-d H:i', $terminar);

                //return $terminar;

                $id_nuevo = $agenda_nueva[0]->id;

                $id_doctor = $agenda_nueva[0]->id_doctor1;

                $id_sala = $agenda_nueva[0]->id_sala;

                //return $start2." ".$end2;
                if ($id_doctor != null) {

                    if ($id_doctor != '9666666666') {
                        // VH: 10102018 VALIDA SI TIENE UNA CONSULTA
                        $cant_consultas = DB::table('agenda')->where('id', '<>', $id_nuevo)->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                            return $query->where('id_doctor1', '=', $id_doctor)
                                ->orWhere('id_doctor2', '=', $id_doctor)
                                ->orWhere('id_doctor3', '=', $id_doctor);
                        })
                            ->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                                return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                                        $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                                    )
                                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                                        $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                                    })
                                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                                        $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                                    });
                            })
                            ->where(function ($query) {
                                return $query->where('estado', '<>', '0')->where('proc_consul', '0');
                            })
                            ->count();
                        //return ($cant_consultas);

                        if ($cant_consultas > 0) {
                            return "Una de las Agendas a desplazar tiene " . $cant_consultas . " consulta(s)";
                        }

                        // VH: 10102018 VALIDA SI TIENE UNA REUNION
                        $cant_reuniones = DB::table('agenda')->where('id', '<>', $id_nuevo)->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                            return $query->where('id_doctor1', '=', $id_doctor)
                                ->orWhere('id_doctor2', '=', $id_doctor)
                                ->orWhere('id_doctor3', '=', $id_doctor);
                        })
                            ->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                                return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                                        $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                                    )
                                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                                        $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                                    })
                                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                                        $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                                    });
                            })
                            ->where(function ($query) {
                                return $query->where('estado', '<>', '0')->where('proc_consul', '2');
                            })
                            ->count();

                        if ($cant_reuniones > 0) {
                            return "Una de las Agendas a desplazar tiene " . $cant_reuniones . " reunion(es)";
                        }

                        // VH: 10102018 VALIDA SI TIENE UNA REUNION
                        $cant_proc = DB::table('agenda')->where('id', '<>', $id_nuevo)->where('id_sala', '<>', $id_sala)->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                            return $query->where('id_doctor1', '=', $id_doctor)
                                ->orWhere('id_doctor2', '=', $id_doctor)
                                ->orWhere('id_doctor3', '=', $id_doctor);
                        })
                            ->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                                return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                                        $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                                    )
                                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                                        $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                                    })
                                    ->orWhere(function ($query) use ($iniciar, $terminar) {
                                        $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                                    });
                            })
                            ->where(function ($query) {
                                return $query->where('estado', '<>', '0')->where('proc_consul', '1');
                            })
                            ->count();

                        if ($cant_proc > 0) {
                            return "Una de las Agendas a desplazar tiene " . $cant_proc . " procedimiento(s) en otra sala";
                        }

                        // HORARIO LABORABLE
                        $horariocontroller = new HorarioController();
                        $horario           = $horariocontroller->valida_horarioxdoctor_dia_2($id_doctor, $start2, $terminar);

                        if ($horario == "INI") {
                            return "Una de las Agendas a desplazar esta fuera del Horario Laborable del Doctor";
                        }

                        if ($horario == "FIN") {
                            return "Una de las Agendas a desplazar esta fuera del Horario Laborable del Doctor";
                        }
                    }

                }

                $input_log = [

                    'id_agenda'          => $agenda_nueva[0]->id,
                    'estado_cita_ant'    => $agenda_nueva[0]->estado_cita,
                    'fechaini_ant'       => $agenda_nueva[0]->fechaini,
                    'fechafin_ant'       => $agenda_nueva[0]->fechafin,
                    'estado_ant'         => $agenda_nueva[0]->estado,
                    'cortesia_ant'       => $agenda_nueva[0]->cortesia,
                    'observaciones_ant'  => $agenda_nueva[0]->observaciones,
                    'id_doctor1_ant'     => $agenda_nueva[0]->id_doctor1,
                    'id_doctor2_ant'     => $agenda_nueva[0]->id_doctor2,
                    'id_doctor3_ant'     => $agenda_nueva[0]->id_doctor3,
                    'id_sala_ant'        => $agenda_nueva[0]->id_sala,

                    'estado_cita'        => $agenda_nueva[0]->estado_cita,
                    'fechaini'           => $start2,
                    'fechafin'           => $end2,
                    'estado'             => $agenda_nueva[0]->estado,
                    'cortesia'           => $agenda_nueva[0]->cortesia,
                    'observaciones'      => "DESPLAZAMIENTO RÁPIDO PENTAX",
                    'id_doctor1'         => $agenda_nueva[0]->id_doctor1,
                    'id_doctor2'         => $agenda_nueva[0]->id_doctor2,
                    'id_doctor3'         => $agenda_nueva[0]->id_doctor3,
                    'id_sala'            => $agenda_nueva[0]->id_sala,

                    'descripcion'        => "DESPLAZAMIENTO RÁPIDO PENTAX",
                    'descripcion2'       => "",
                    'descripcion3'       => "",
                    'campos_ant'         => "",
                    'campos'             => "",
                    'id_usuarioconfirma' => $agenda_nueva[0]->id_usuarioconfirma,

                    'id_usuariomod'      => $idusuario,
                    'id_usuariocrea'     => $idusuario,
                    'ip_modificacion'    => $ip_cliente,
                    'ip_creacion'        => $ip_cliente,

                ];

                //return $iniciar." ".$terminar;
                $agenda_nueva = DB::table('agenda')->where('id', '<>', $id)->where('id_sala', $id_sala)->where('id', '<>', '0')
                    ->where(function ($query) use ($iniciar, $terminar) {
                        return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                            )
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                            })
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                            });
                    })
                    ->where(function ($query) {
                        return $query->where('estado', '<>', '0')->where('estado_cita', '<>', '2');
                    })
                    ->orderBy('fechaini', 'asc')->get();

                $cuenta = count($agenda_nueva);

                $input = [
                    'id_usuariomod'   => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'fechaini'        => $start2,
                    'fechafin'        => $end2,
                ];
                Agenda::find($id_nuevo)->update($input);

                //falta log de agenda
                Log_agenda::create($input_log);

            } while ($cuenta != 0);
        }

        date_default_timezone_set('America/Guayaquil');

        $input = [
            'nro_reagenda'    => $nro_reagenda,
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'fechaini'        => $fechaini,
            'fechafin'        => $fechafin,
            'id_sala'         => $id_sala,
        ];
        Agenda::find($id)->update($input);
        //falta log de agenda
        Log_agenda::create([
            'id_agenda'          => $agendamiento->id,
            'estado_cita_ant'    => $agendamiento->estado_cita,
            'fechaini_ant'       => $agendamiento->fechaini,
            'fechafin_ant'       => $agendamiento->fechafin,
            'estado_ant'         => $agendamiento->estado,
            'cortesia_ant'       => $agendamiento->cortesia,
            'observaciones_ant'  => $agendamiento->observaciones,
            'id_doctor1_ant'     => $agendamiento->id_doctor1,
            'id_doctor2_ant'     => $agendamiento->id_doctor2,
            'id_doctor3_ant'     => $agendamiento->id_doctor3,
            'id_sala_ant'        => $agendamiento->id_sala,

            'estado_cita'        => $agendamiento->estado_cita,
            'fechaini'           => $fechaini,
            'fechafin'           => $fechafin,
            'estado'             => $agendamiento->estado,
            'cortesia'           => $agendamiento->cortesia,
            'observaciones'      => "DESPLAZAMIENTO RÁPIDO SALAS PENTAX",
            'id_doctor1'         => $agendamiento->id_doctor1,
            'id_doctor2'         => $agendamiento->id_doctor2,
            'id_doctor3'         => $agendamiento->id_doctor3,
            'id_sala'            => $id_sala,

            'descripcion'        => "DESPLAZAMIENTO RÁPIDO SALAS PENTAX",
            'descripcion2'       => "",
            'descripcion3'       => "",
            'campos_ant'         => "",
            'campos'             => "",
            'id_usuarioconfirma' => $agendamiento->id_usuarioconfirma,

            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,
        ]);

        return "Proceso completado correctamente";
    }

    public function intervalo($id, $start, $end)
    {

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $agendamiento = Agenda::find($id);
        $id_sala      = $agendamiento->id_sala;
        $start        = substr($start, 0, 10);
        $end          = substr($end, 0, 10);
        date_default_timezone_set('UTC');
        $start2   = date('Y-m-d H:i', $start);
        $end2     = date('Y-m-d H:i', $end);
        $iniciar  = strtotime('+1 minute', strtotime($start2));
        $terminar = strtotime('-1 minute', strtotime($end2));
        $iniciar  = date('Y-m-d H:i', $iniciar);
        $terminar = date('Y-m-d H:i', $terminar);

        $fechaini = $start2;
        $fechafin = $end2;

        $nro_reagenda = $agendamiento->nro_reagenda;
        $id_doctor    = $agendamiento->id_doctor1;
        //return $start2.' '.$terminar;

        if ($id_doctor != null) {
            if ($id_doctor != '9666666666') {
                //VALIDA SI TIENE UNA CONSULTA
                $cant_consultas = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                    return $query->where('id_doctor1', '=', $id_doctor)
                        ->orWhere('id_doctor2', '=', $id_doctor)
                        ->orWhere('id_doctor3', '=', $id_doctor);
                })
                    ->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                        return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                            )
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                            })
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                            });
                    })
                    ->where(function ($query) {
                        return $query->where('estado', '<>', '0')->where('proc_consul', '0');
                    })
                    ->count();

                if ($cant_consultas > 0) {
                    return "Doctor posee " . $cant_consultas . " consulta(s)";
                }

                // VH: 10102018 VALIDA SI TIENE UNA REUNION
                $cant_reuniones = DB::table('agenda')->where('id', '<>', $id)->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                    return $query->where('id_doctor1', '=', $id_doctor)
                        ->orWhere('id_doctor2', '=', $id_doctor)
                        ->orWhere('id_doctor3', '=', $id_doctor);
                })
                    ->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                        return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                            )
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                            })
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                            });
                    })
                    ->where(function ($query) {
                        return $query->where('estado', '<>', '0')->where('proc_consul', '2');
                    })
                    ->count();

                if ($cant_reuniones > 0) {
                    return "Doctor posee " . $cant_reuniones . " reunion(es)";
                }

                // VH: 10102018 VALIDA SI TIENE PROCEDIMIENTO
                $cant_proc = DB::table('agenda')->where('id', '<>', $id)->where('id_sala', '<>', $id_sala)->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                    return $query->where('id_doctor1', '=', $id_doctor)
                        ->orWhere('id_doctor2', '=', $id_doctor)
                        ->orWhere('id_doctor3', '=', $id_doctor);
                })
                    ->where(function ($query) use ($iniciar, $terminar, $id_doctor) {
                        return $query->whereRaw("(('" . $iniciar . "' BETWEEN fechaini and fechafin)")
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("'" . $terminar . "' BETWEEN fechaini and fechafin)");}
                            )
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("(fechaini BETWEEN '" . $iniciar . "' and '" . $terminar . "'");
                            })
                            ->orWhere(function ($query) use ($iniciar, $terminar) {
                                $query->whereRaw("fechafin BETWEEN '" . $iniciar . "' and '" . $terminar . "')");
                            });
                    })
                    ->where(function ($query) {
                        return $query->where('estado', '<>', '0')->where('proc_consul', '1');
                    })
                    ->count();

                if ($cant_proc > 0) {
                    return "Doctor posee " . $cant_proc . " procedimiento(s) en otra sala";
                }

                // HORARIO LABORABLE
                $horariocontroller = new HorarioController();
                $horario           = $horariocontroller->valida_horarioxdoctor_dia_2($id_doctor, $iniciar, $terminar);

                if ($horario == "INI") {
                    return "Fecha de inicio fuera del Horario Laborable del Doctor";
                }

                if ($horario == "FIN") {
                    return "Fecha de fin fuera del Horario Laborable del Doctor";
                }

                // HORARIO LABORABLE
                /*$horarioscontroller = new HorarioController();
                $horarios           = $horarioscontroller->valida_horarioxsala_2($id_sala, $iniciar, $terminar);*/

                if ($horario == "INI") {
                    return "Fecha de inicio fuera del Horario Laborable del Doctor";
                }

                if ($horario == "FIN") {
                    return "Fecha de fin fuera del Horario Laborable del Doctor";
                }
            }

        }

        date_default_timezone_set('America/Guayaquil');

        $input = [
            'nro_reagenda'    => $nro_reagenda,
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'fechaini'        => $fechaini,
            'fechafin'        => $fechafin,

        ];
        Agenda::find($id)->update($input);
        //falta log de agenda
        Log_agenda::create([
            'id_agenda'          => $agendamiento->id,
            'estado_cita_ant'    => $agendamiento->estado_cita,
            'fechaini_ant'       => $agendamiento->fechaini,
            'fechafin_ant'       => $agendamiento->fechafin,
            'estado_ant'         => $agendamiento->estado,
            'cortesia_ant'       => $agendamiento->cortesia,
            'observaciones_ant'  => $agendamiento->observaciones,
            'id_doctor1_ant'     => $agendamiento->id_doctor1,
            'id_doctor2_ant'     => $agendamiento->id_doctor2,
            'id_doctor3_ant'     => $agendamiento->id_doctor3,
            'id_sala_ant'        => $agendamiento->id_sala,

            'estado_cita'        => $agendamiento->estado_cita,
            'fechaini'           => $fechaini,
            'fechafin'           => $fechafin,
            'estado'             => $agendamiento->estado,
            'cortesia'           => $agendamiento->cortesia,
            'observaciones'      => "CAMBIO INTERVALO RÁPIDO SALAS PENTAX",
            'id_doctor1'         => $agendamiento->id_doctor1,
            'id_doctor2'         => $agendamiento->id_doctor2,
            'id_doctor3'         => $agendamiento->id_doctor3,
            'id_sala'            => $agendamiento->id_sala,

            'descripcion'        => "CAMBIO INTERVALO RÁPIDO SALAS PENTAX",
            'descripcion2'       => "",
            'descripcion3'       => "",
            'campos_ant'         => "",
            'campos'             => "",
            'id_usuarioconfirma' => $agendamiento->id_usuarioconfirma,

            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,
        ]);

        return "Proceso completado correctamente";
    }

    //nueva funcion agregada directamente a produccion
    public function maximo_dia($tiempo)
    {
        date_default_timezone_set('UTC');
        $start  = substr($tiempo, 0, 10);
        $start2 = date('Y-m-d', $start);

        $agendas = DB::table('agenda as a')
            ->join('paciente', 'a.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'a.id_seguro', '=', 'seguros.id')
            ->join('users', 'a.id_usuariocrea', '=', 'users.id')
            ->leftjoin('users as d', 'd.id', 'a.id_doctor1')
            ->join('procedimiento', 'a.id_procedimiento', '=', 'procedimiento.id')
            ->join('sala as s', 's.id', 'a.id_sala')
            ->select('a.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color', 'seguros.nombre as nombre_seguro', 'procedimiento.nombre as nombre_procedimiento', 'users.nombre1 as unombre1', 'users.apellido1 as uapellido1', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1')
            ->where('s.id_hospital', '2')
            ->where('proc_consul', '=', 1)
            ->where('a.estado', '!=', '0')
            ->whereBetween('fechaini', [$start2 . ' 00:00:00', $start2 . ' 23:59:59'])->get()->count();
        return $agendas;
    }

    public function agenda_pnombre($fecha, $sala)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $salas = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->where('sala.id_hospital', '2')->where('sala.nombre_sala', '!=', 'RECUPERACION')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital', 'hospital.direccion as direccion_hospital')->orderBy('hospital.nombre_hospital')
            ->get();

        //$paciente = paciente::find($i);

        //SI NO SE ENCUENTRA EL PACIENTE
        /*if ($paciente==array() && $i!='0'){

        return  redirect()->route('agenda.paciente', ['id' => '1', 'i' => $i, 'fecha' => $fecha, 'sala' => $sala]);
        }

        $cortesia_paciente = Cortesia_paciente::find($i);
         */

        $procedimiento = Procedimiento::all();
        $empresa       = Empresa::all();
        $seguro        = Seguro::where('inactivo', '1')->orderBy('nombre', 'asc')->get();

        date_default_timezone_set('UTC');
        $fecha  = substr($fecha, 0, 10);
        $fecha2 = date('Y/m/d H:i', $fecha);

        $citas = array();
        //$ordenes= array();
        /*if(!is_null($paciente)){
        $agendacontroller = new AgendaController;

        $citas = $agendacontroller->busca_citasxpaciente_dia_mes($fecha2,$paciente->id);
        //$ordenes = Orden::where('id_paciente',$paciente->id)->where('estado',1)->orderBy('fecha_orden','desc')->get();
        //dd($citas);

        }*/
        return view('preagenda/agendar_pnombre', ['salas' => $salas, 'procedimiento' => $procedimiento, 'empresa' => $empresa, 'seguro' => $seguro, 'hora' => $fecha2, 'unix' => $fecha, 'sala2' => $sala]);
    }

    public function pnombre_guardar(Request $request)
    {
        //dd($request);

        date_default_timezone_set('America/Guayaquil');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $procedimientos = $request['procedimiento'];
        $procedimientop = $procedimientos[0];

        $rules1 = [
            'nombre1'       => 'required',
            'nombre2'       => 'required',
            'apellido1'     => 'required',
            'apellido2'     => 'required',
            'procedimiento' => 'required',
            'edad'          => 'required',
        ];
        $mensajes1 = [
            'procedimiento.required' => 'Ingrese el Procedimiento',
            'nombre1.required'       => 'Ingrese el Primer Nombre',
            'nombre2.required'       => 'Ingrese el Segundo Nombre',
            'apellido1.required'     => 'Ingrese el Primer Apellido',
            'apellido2.required'     => 'Ingrese el Segundo Apellido',
            'edad.required'          => 'Ingrese La Edad',
        ];

        $this->validate($request, $rules1, $mensajes1);

        if ($request->est_amb_hos == '1') {
            $arr_hos = [
                'omni' => 'required',
            ];
            $arr_men = [
                'omni.required' => 'Seleccione Si es Omni Hospital',
            ];
            $this->validate($request, $arr_hos, $arr_men);
            $omni = $request->omni;
        } else {
            $omni = null;
        }

        $this->validateInput3($request);
        //fecha de inicio y fin

        $this->validateInput4($request);
        //fecha de inicio antes de fin

        //$this->validateInput6($request);

        $pac_controller = new PacienteController;
        $id_paciente    = $pac_controller->pacientexnombre($request);
        if($id_paciente != '0'){
            $id_paciente = json_decode($id_paciente);
            $id_paciente = $id_paciente->id;
        }
        

        //dd($id_paciente->id);

        if ($id_paciente == '0') {

            $fecha  = date('Y-m-d H:i:s');
            $codigo = 'AUX' . substr($fecha, 2, 2) . substr($fecha, 5, 2) . substr($fecha, 8, 2) . substr($fecha, 11, 2) . substr($fecha, 14, 2);

            $cont = 0;
            $flag = true;
            while ($flag) {
                $cont++;
                $arreglo[$cont] = $codigo;
                $paciente_aux   = Paciente::find($codigo);
                if (!is_null($paciente_aux)) {
                    $fecha  = date('Y-m-d H:i:s', strtotime('+1 minute', strtotime($fecha)));
                    $codigo = 'AUX' . substr($fecha, 2, 2) . substr($fecha, 5, 2) . substr($fecha, 8, 2) . substr($fecha, 11, 2) . substr($fecha, 14, 2);
                } else {
                    $flag = false;
                }

            }

            $anio             = date('Y');
            $anio             = $anio - $request->edad;
            $fecha_nacimiento = $anio . '-01-01';

            $input_pac = [

                'id'                 => $codigo,
                'id_usuario'         => $codigo,
                'nombre1'            => strtoupper($request->nombre1),
                'nombre2'            => strtoupper($request->nombre2),
                'apellido1'          => strtoupper($request->apellido1),
                'apellido2'          => strtoupper($request->apellido2),
                'telefono1'          => '1',
                'telefono2'          => '1',
                'nombre1familiar'    => strtoupper($request->nombre1),
                'nombre2familiar'    => strtoupper($request->nombre2),
                'apellido1familiar'  => strtoupper($request->apellido1),
                'apellido2familiar'  => strtoupper($request->apellido2),
                'parentesco'         => 'Principal',
                'parentescofamiliar' => 'Principal',
                'tipo_documento'     => 1,
                'id_seguro'          => $request->id_seguro,
                'imagen_url'         => ' ',
                'menoredad'          => 0,
                'fecha_nacimiento'   => $fecha_nacimiento,

                'ip_creacion'        => $ip_cliente,
                'ip_modificacion'    => $ip_cliente,
                'id_usuariocrea'     => $idusuario,
                'id_usuariomod'      => $idusuario,

            ];

            //CREAR USUARIO
            $input_usu_c = [

                'id'              => $codigo,
                'nombre1'         => strtoupper($request->nombre1),
                'nombre2'         => strtoupper($request->nombre2),
                'apellido1'       => strtoupper($request->apellido1),
                'apellido2'       => strtoupper($request->apellido2),
                'telefono1'       => '1',
                'telefono2'       => '1',
                'id_tipo_usuario' => 2,
                'email'           => $codigo . '@mail.com',
                'password'        => bcrypt($codigo),
                'tipo_documento'  => 1,
                'estado'          => 1,
                'imagen_url'      => ' ',
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,

            ];

            $user = User::find($codigo);

            if (!is_null($user)) {

            } else {
                User::create($input_usu_c);
            }

            paciente::create($input_pac);

            $input_log = [
                'id_usuario'  => $idusuario,
                'ip_usuario'  => $ip_cliente,
                'descripcion' => "CREA NUEVO PACIENTE",
                'dato_ant1'   => $codigo,
                'dato1'       => strtoupper($request->nombre1) . " " . strtoupper($request->nombre2) . " " . strtoupper($request->apellido1) . " " . strtoupper($request->apellido2),
                'dato_ant2'   => " PARENTESCO: Principal",
                'dato2'       => 'PENTAX',
            ];

            Log_usuario::create($input_log);

            $id_paciente = $codigo;

        }

        $input_historia = [
            'fechaini'         => $request['inicio'],
            'fechafin'         => $request['fin'],
            'id_paciente'      => $id_paciente,

            'id_procedimiento' => $procedimientop,
            'proc_consul'      => '1',
            'id_sala'          => $request['id_sala'],

            'id_seguro'        => $request['id_seguro'],
            'tipo_cita'        => $request['tipo_cita'],
            'estado_cita'      => '0',
            'observaciones'    => $request['observaciones'],
            'est_amb_hos'      => $request['est_amb_hos'],
            'estado'           => '-1',
            'ip_creacion'      => $ip_cliente,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariocrea'   => $idusuario,
            'id_usuariomod'    => $idusuario,
            'cortesia'         => $request->cortesia,
            'omni'             => $omni,

        ];

        $id_agenda = agenda::insertGetId($input_historia);
        foreach ($procedimientos as $value) {
            if ($procedimientop != $value) {
                AgendaProcedimiento::create([
                    'id_agenda'        => $id_agenda,
                    'id_procedimiento' => $value,
                    'ip_creacion'      => $ip_cliente,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariocrea'   => $idusuario,
                    'id_usuariomod'    => $idusuario,
                ]);
            }

        }

        if($request['observaciones_admin'] != "" || $request['observaciones_admin'] != null){
            $obser_admin = Paciente_Observaciones::where('id_paciente', $id_paciente)->first();
            $obser       = [
                'id_paciente'     => $id_paciente,
                'observacion'     => $request['observaciones_admin'],
                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];
                if(count($obser_admin)>0){
                    //actualiza
                     Paciente_Observaciones::where('id_paciente', $id_paciente)->update($obser);
                }else{
                    //crea
                    Paciente_Observaciones::create($obser);
                }
        }

        return redirect()->route('salas_todas.cargar', ['id' => $id_agenda]);
    }

    public function ocupar_sala()
    {

        $salas = Sala::where('proc_consul_sala',1)->where('estado',1)->where('id_hospital',2)->get();
        //dd($salas);
        return view('preagenda/modalsala',['salas'=>$salas]);
    }
    public function guardar_sala(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $sala = $request['salas'];
        $desde = $request['desde'];
        $hasta = $request['hasta'];
        $nombre_reunion = $request['nombre_reunion'];
        $id_empresa     = $request->session()->get('id_empresa');
        $nuevo_desde = str_replace('T',' ',$desde);
        $nuevo_hasta = str_replace('T',' ',$hasta);
        Agenda::create([
            'fechaini'              =>$nuevo_desde,
            'fechafin'              =>$nuevo_hasta,
            'proc_consul'           =>2,//reuniones
            'id_sala'               =>$sala,
            'estado_cita'           =>1,
            'nro_reagenda'          =>0,
            'cortesia'              =>"NO",
            'estado'                =>1,
            'observaciones'         =>"ocupar sala",
            'id_empresa'            =>$id_empresa,
            'adelantado'            =>0,
            'id_usuariocrea'         => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'procedencia'           => $nombre_reunion,
        ]);

        return response()->json("Ok");
    }
    public function modal_modificar(Request $request, $id)
    {

        $sala = Agenda::where('id',$id)->first();
        $nombre = User::where('id',$sala->id_usuariomod)->first();
        $nombresala=Sala::where('id',$sala->id_sala)->first();

        return view('preagenda/modal_modificar',['sala'=>$sala,'nombre'=>$nombre,'nombresala'=>$nombresala,'id'=>$id]);
    }
    public function guardar_modificaciones_sala(Request $request)
    {

        $ip_cliente     = $_SERVER["REMOTE_ADDR"];
        $idusuario      = Auth::user()->id;
        $id_paciente    = $request['id'];
        $estado         = $request['estado'];
        $id_paciente    = $request['id'];
        $observaciones  = $request['observaciones'];
        $nombre_reunion = $request['nombre_reunion'];
        $sala = Agenda::where('id',$id_paciente)->first();
        $fecha = date("Y-m-d");
        //dd($id_paciente,$observaciones,$estado);
        $input = [
          'estado' => $estado,
          'observaciones' =>$observaciones,
          'updated_at' =>$fecha,
          'procedencia'  => $nombre_reunion,
        ];
        $sala->update($input);
        return  response()->json($estado);
    }
    public function validar_hora(Request $request)
    {
        $desde       = $request['desde'];
        $hasta       = $request['hasta'];
        $nuevo_desde = str_replace('T', ' ', $desde);
        $nuevo_hasta = str_replace('T', ' ', $hasta);
        $fecha_com   = date("Y-m-d H:i:s");
        if ($nuevo_desde < $nuevo_hasta) {

            return  response()->json("ok");
        }else{
            return  response()->json("no");
        }
    }

}
