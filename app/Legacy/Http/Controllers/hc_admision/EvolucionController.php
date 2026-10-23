<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use DateTime;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPExcel_Worksheet_Drawing;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Cardiologia;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\Empresa;
use Sis_medico\Firma_Usuario;
use Sis_medico\Aud_Hc_Cie10;
use Sis_medico\FormatoDescargaInsumos;
use Sis_medico\Hc_cardio_cie10;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_agenda;
use Sis_medico\Paciente;
use Sis_medico\Procedimiento;
use Sis_medico\procedimiento_completo;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Aud_Hc_Protocolo;
use Sis_medico\Aud_Hc_Evolucion;
use Sis_medico\Interconsulta;

class EvolucionController extends Controller
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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 6, 11, 5, 7)) == false) {
            return true;
        }
    }

    private function rol_doctor()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(3)) == false) {
            return true;
        }
    }

    public function crea_actualiza(Request $request)
    {
        //dd($request);
        $hc_id_procedimiento = $request['hc_id_procedimiento'];
        $secuencia           = $request['secuencia'];
        $hcid                = $request['hcid'];
        $cuadro              = $request['cuadro_clinico'];
        $laboratorio         = $request['laboratorio'];
        $id_evolucion        = $request['id_evolucion'];
        $ip_cliente          = $_SERVER["REMOTE_ADDR"];
        $idusuario           = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $historia      = Historiaclinica::find($hcid);
        $fecha_ingreso = $request['inicio'];
        $paciente      = Paciente::find($historia->id_paciente);
        $seguro        = Seguro::find($historia->id_seguro);

        $rules = [
            'cuadro_clinico' => 'required',
            'inicio'         => 'required',
        ];

        $msn = [
            'cuadro_clinico.required' => 'Ingrese una evolución',
            'inicio.required'         => 'Ingrese una fecha',
        ];

        $this->validate($request, $rules, $msn);

        if ($id_evolucion == null) {
            $input1 = [
                'hcid'                => $hcid,
                'hc_id_procedimiento' => $hc_id_procedimiento,
                'secuencia'           => $secuencia,
                'cuadro_clinico'      => $cuadro,
                'laboratorio'         => $laboratorio,
                'fecha_ingreso'       => $fecha_ingreso,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id = Hc_Evolucion::insertGetId($input1);
        } else {
            $id        = $id_evolucion;
            $evolucion = Hc_Evolucion::find($id_evolucion);

            $input2 = [

                'cuadro_clinico'  => $cuadro,
                'fecha_ingreso'   => $fecha_ingreso,
                'laboratorio'     => $laboratorio,
                'fecha_ingreso'   => $fecha_ingreso,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            //return $request->all();
            $evolucion->update($input2);
        }

        return $id;
        //return view('hc_admision/evolucion/evolucion',['paciente' => $paciente, 'seguro' => $seguro, 'hcid' => $hcid, 'evolucion_0' => $evolucion_0, 'indicaciones' => $indicaciones ]);
    }

    public function crea_indicacion(Request $request)
    {

        $indicacion   = $request['indicacion'];
        $hcid         = $request['hcid'];
        $id_evolucion = $request['id_evolucion'];

        $rules = [
            'indicacion' => 'required',
        ];

        $msn = [
            'indicacion.required' => 'Ingrese una evolución',
        ];

        $this->validate($request, $rules, $msn);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $historia      = Historiaclinica::find($hcid);
        $fecha_ingreso = Date('Y-m-d h:i:s');

        $evolucion = Hc_Evolucion::find($id_evolucion);
        if (!is_null($evolucion)) {

            $indicaciones = Hc_Evolucion_Indicacion::where('id_evolucion', $id_evolucion)->get();
            $contador     = $indicaciones->count();

            $input2 = [
                'id_evolucion'    => $id_evolucion,
                'secuencia'       => $contador + 1,
                'descripcion'     => $indicacion,

                'ip_creacion'     => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
            ];

            Hc_Evolucion_Indicacion::create($input2);

            $indicaciones2 = Hc_Evolucion_Indicacion::where('id_evolucion', $id_evolucion)->get();
        }

        return view('hc_admision/evolucion/indicacion', ['indicaciones' => $indicaciones2]);
        //return view('hc_admision/evolucion/evolucion',['paciente' => $paciente, 'seguro' => $seguro, 'hcid' => $hcid, 'evolucion_0' => $evolucion_0, 'indicaciones' => $indicaciones ]);

    }

    public function mostrar($id, $id_evolucion)
    {

        $hc_procedimiento = Hc_procedimientos::find($id);
        $historia         = Historiaclinica::find($hc_procedimiento->id_hc);
        $paciente         = Paciente::find($historia->id_paciente);
        $seguro           = Seguro::find($historia->id_seguro);

        $evoluciones = Hc_Evolucion::where('hc_id_procedimiento', $id)->get();

        $evolucion    = null;
        $indicaciones = null;
        if ($id_evolucion != null) {
            $evolucion    = Hc_Evolucion::find($id_evolucion);
            $indicaciones = Hc_Evolucion_Indicacion::where('id_evolucion', $id_evolucion)->get();
        }

        return view('hc_admision/evolucion/evolucion', ['paciente' => $paciente, 'seguro' => $seguro, 'hcid' => $historia->hcid, 'evoluciones' => $evoluciones, 'indicaciones' => $indicaciones, 'evolucion' => $evolucion, 'id' => $id]);
    }

    public function indicaciones(Request $request)
    {
        $id_evolucion = $request['id_evolucion'];

        if ($id_evolucion != null) {

            $indicaciones2 = Hc_Evolucion_Indicacion::where('id_evolucion', $id_evolucion)->get();

            return view('hc_admision/evolucion/indicacion', ['indicaciones' => $indicaciones2]);
        } else {

            return "no";
        }
    }

    public function evolucion($id)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $usuarios       = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->get(); //3=DOCTORES;
        $enfermeros     = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado', '1')->get(); //6=ENFERMEROS;
        $anestesiologos = DB::table('users')->where('id_tipo_usuario', '=', 9)->where('estado', '1')->get(); //9=ANESTESIOLOGO;
        $salas          = DB::table('sala')
            ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
            ->get();

        $hc_procedimiento = Hc_procedimientos::find($id);
        $historia         = Historiaclinica::find($hc_procedimiento->id_hc);

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $historia->id_agenda)
            ->first();

        $paciente = Paciente::find($historia->id_paciente);

        $seguro = Seguro::find($historia->id_seguro);

        $evoluciones = Hc_Evolucion::where('hc_id_procedimiento', $id)->get();

        $procedimientos_completo = procedimiento_completo::all();

        return view('hc_admision/evolucion/evoluciones', ['agenda' => $agenda, 'paciente' => $paciente, 'hca' => $historia, 'seguro' => $seguro, 'evoluciones' => $evoluciones, 'id' => $id, 'procedimientos_completo' => $procedimientos_completo, 'hc_procedimiento' => $hc_procedimiento]);
    }

    public function show($id)
    {
        //

    }

    public function imprimir($id)
    {

        $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();

        $indicaciones = [];
        foreach ($evolucion as $value) {
            $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion', $value->id)->get();
        }
        $procedimiento = Hc_procedimientos::find($id);

        //dd($indicaciones);

        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $procedimiento->id_hc)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo')->first();

        $data = $historiaclinica;
        $view = \View::make('hc_admision.formato.evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;

        return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    }

    public function imprimir_stream($id)
    {

        $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();

        $indicaciones = [];
        foreach ($evolucion as $value) {
            $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion', $value->id)->get();
        }

        $procedimiento = Hc_procedimientos::find($id);

        //dd($indicaciones);

        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $procedimiento->id_hc)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo')->first();

        $data = $historiaclinica;
        $view = \View::make('hc_admision.formato.evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones'))->render();
        $pdf  = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;

        //return $pdf->download('evolucion-'.$historiaclinica->id_paciente.'-'.$historiaclinica->hcid.'.pdf');

        return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    }

    //funcion solo para los doctores
    public function crear_evolucion($id, $ag)
    {
        //dd($ag);

        //1. crear agenda y log proc_consul='4' estado='2'
        $paciente = Paciente::find($id);

        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        if ($this->rol_doctor()) {
            return response()->view('errors.404');
        }

        $id_doctor = Auth::user()->id;

        $input_agenda = [
            'fechaini'        => Date('Y-m-d H:i:s'),
            'fechafin'        => Date('Y-m-d H:i:s'),
            'id_paciente'     => $id,
            'id_doctor1'      => $id_doctor,
            'proc_consul'     => '4',
            'estado_cita'     => '4',
            'espid'           => '4',
            'observaciones'   => 'EVOLUCION CREADA POR EL DOCTOR',
            'id_seguro'       => $paciente->id_seguro,
            'estado'          => '4',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
        ];

        $id_agenda = agenda::insertGetId($input_agenda);

        if ($ag == 'no') {
            $ag = $id_agenda;
        }

        $input_log = [
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => '0',
            'estado_cita'     => '0',
            'fechaini'        => Date('Y-m-d H:i:s'),
            'fechafin'        => Date('Y-m-d H:i:s'),
            'estado'          => '4',
            'observaciones'   => 'EVOLUCION CREADA POR EL DOCTOR',
            'id_doctor1'      => $id_doctor,
            'descripcion'     => 'EVOLUCION CREADA POR EL DOCTOR',

            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        $idusuario = $id_doctor;

        Log_agenda::create($input_log);

        $input_historia = [

            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $id,
            'id_seguro'       => $paciente->id_seguro,

            'id_doctor1'      => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $id_doctor,
            'ip_creacion'     => $ip_cliente,

        ];

        $id_procedimiento_completo = '40';

        $id_historia = Historiaclinica::insertGetId($input_historia);

        $input_hc_procedimiento = [
            'id_hc'                     => $id_historia,
            'id_seguro'                 => $paciente->id_seguro,
            'id_procedimiento_completo' => $id_procedimiento_completo,
            'ip_modificacion'           => $ip_cliente,
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
            'ip_creacion'               => $ip_cliente,

        ];

        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

        $tsexo          = '';
        $xcuadroclinico = null;

        if ($paciente->sexo == '1') {
            $tsexo = 'MASCULINO';
        } elseif ($paciente->sexo == '2') {
            $tsexo = 'FEMENINO';
        }

        $tedad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;

        if (!is_null($paciente->seguro)) {
            if ($paciente->seguro->tipo == '0') {
                $xcuadroclinico = 'PACIENTE DE SEXO ' . $tsexo . ' DE ' . $tedad . ' AÑOS DE EDAD CON CUADRO CLINICO DE  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; MESES DE EVOLUCION CARACTERIZADO POR &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; , &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ,<br>
                                    (DESCRIPCION DE SINTOMAS INTENSIDAD, HORARIO DE APARICION QUE LO EXACERBA)<br>
                                    EN LA ACTUALIDAD SINTOMAS SE INTESIFICAN POR LO QUE ACUDE A CONSULTA.';
            }
        }

        $input_hc_evolucion = [
            'hc_id_procedimiento' => $id_hc_procedimiento,
            'hcid'                => $id_historia,
            'secuencia'           => '0',
            'cuadro_clinico'      => $xcuadroclinico,
            'fecha_ingreso'       => ' ',
            'ip_modificacion'     => $ip_cliente,
            'id_usuariomod'       => $idusuario,
            'id_usuariocrea'      => $idusuario,
            'ip_creacion'         => $ip_cliente,

        ];
        $id_evolucion    = Hc_Evolucion::insertGetId($input_hc_evolucion);
        $input_hc_receta = [
            'id_hc'           => $id_historia,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,

        ];
        hc_receta::insert($input_hc_receta);

        return redirect()->route('visita.crea_actualiza_funcion', ['ev' => $id_evolucion, 'ag' => $ag]);
    }

    public function pr_modal($id)
    {

        $protocolo = hc_protocolo::find($id);
        $id_agenda = $protocolo->historiaclinica->id_agenda;
        $agenda    = Agenda::find($id_agenda);

        if ($protocolo->hora_inicio == null) {
            $hora_inicio = substr($agenda->fechaini, 11, 5);
        } else {
            $hora_inicio = substr($protocolo->hora_inicio, 0, 5);
        }

        $fecha_operacion = $protocolo->fecha_operacion;
        if ($fecha_operacion == null) {
            $fecha_operacion = substr($agenda->fechaini, 0, 10);
        }

        $id_doctor_firma = $protocolo->procedimiento->id_doctor_examinador2;
        if ($protocolo->procedimiento->id_doctor_responsable != null) {
            $id_doctor_firma = $protocolo->procedimiento->id_doctor_responsable;
        }
        if ($protocolo->procedimiento->id_seguro != null) {
            $seguro = Seguro::find($protocolo->procedimiento->id_seguro);
            if ($seguro->tipo == 0) {
                if ($protocolo->procedimiento->id_empresa == '1307189140001') {
                    $id_doctor_firma = '1307189140';
                }
                if ($protocolo->procedimiento->id_empresa == '0992704152001') {
                    if ($id_doctor_firma == '0924611882') {
                        $id_doctor_firma = '094346835';
                    }
                }
            }
        }

        if ($id_doctor_firma == null) {
            $id_doctor_firma = $protocolo->procedimiento->id_doctor_examinador;
        }

        if ($id_doctor_firma == null) {
            $id_doctor_firma = $protocolo->historiaclinica->doctor_1->id;
        }

        $id_doctor_ayudante_con = $protocolo->procedimiento->id_doctor_ayudante_con;

        if ($id_doctor_ayudante_con == null) {
            if ($protocolo->historiaclinica->doctor_2 != null) {
                $id_doctor_ayudante_con = $protocolo->historiaclinica->doctor_2->id;
            }
        }

        $cardiologia = Agenda::where('agenda.id_paciente', $agenda->id_paciente)->join('historiaclinica as h', 'agenda.id', 'h.id_agenda')->join('hc_cardio as c', 'c.hcid', 'h.hcid')->where('espid', '8')->select('agenda.*', 'h.hcid', 'c.resumen', 'c.id as id_cardio')->orderBy('fechaini', 'desc')->first();
        //return $cardiologia;

        //dd($id_doctor_firma);
        $doctores = User::where('estado', '1')->where('id_tipo_usuario', '3')->get();

        return view('hc_admision/evolucion/pr_modal', ['protocolo' => $protocolo, 'hora_inicio' => $hora_inicio, 'id_doctor_firma' => $id_doctor_firma, 'doctores' => $doctores, 'fecha_operacion' => $fecha_operacion, 'id_doctor_ayudante_con' => $id_doctor_ayudante_con, 'cardiologia' => $cardiologia]);
    }

    public function guardar_op(Request $request)
    {
        //return $request->all();
        $protocolo = hc_protocolo::find($request->protocolo);

        if ($request->tipo_anestesia == 'GENERAL') {
            $duracion = '60';
            $hora_fin = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL90') {
            $duracion = '90';
            $hora_fin = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL120') {
            $duracion = '120';
            $hora_fin = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL150') {
            $duracion = '150';
            $hora_fin = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL180') {
            $duracion = '180';
            $hora_fin = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL210') {
            $duracion = '210';
            $hora_fin = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } else {
            $duracion = '30';
            $hora_fin = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        }
        $input = [
            'fecha_operacion'     => $request->fecha_operacion,
            'hora_inicio'         => $request->hora_ini,
            'hora_fin'            => $hora_fin,
            'tipo_anestesia'      => $request->tipo_anestesia,
            'intervalo_anestesia' => $duracion,
        ];
        $protocolo->update($input);

        $input2 = [
            'id_doctor_examinador2'  => $request->id_doctor_examinador2,
            'id_doctor_ayudante_con' => $request->id_doctor_ayudante_con,
        ];
        $protocolo->procedimiento->update($input2);

        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();

        $id = $protocolo->procedimiento->id;

        $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();

        $indicaciones = [];
        foreach ($evolucion as $value) {
            $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion', $value->id)->get();
        }

        $procedimiento = Hc_procedimientos::find($id);
        $proc_finales  = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
        $id_principal  = '';
        foreach ($proc_finales as $px) {
            if ($px->procedimiento->id_grupo_procedimiento != null) {
                $id_principal = $px->id_procedimiento;
                break;
            }
        }

        //dd($indicaciones);

        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $procedimiento->id_hc)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'especialidad.nombre as enombre', 'historiaclinica.id_seguro as id_seguro')->first();

        $data            = $historiaclinica;
        $fecha_operacion = $request->fecha_operacion;
        $hora_ini        = $request->hora_ini;

        $cardiologia = null;
        if ($request->cardio == '1') {

            if ($request->id_cardio != null) {

                $cardiologia = Cardiologia::find($request->id_cardio);
            }
        }

        if (count($evolucion) <= '1') {
            $view = \View::make('hc_admision.formato.evolucion_sin_nada', compact('data', 'evolucion', 'procedimiento', 'indicaciones'))->render();
            $pdf  = \App::make('dompdf.wrapper');

            $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }

        if ($id_principal != '') {

            // ENTRR CON LO NUEVOS OPTION DEL EXCEL
            //PH 24H
            //MANOMETRIA ESOFAGICA 2H Y 30 MIN
            //MANOMETRIA ANORECTAL 2H Y 30 MIN
            //CAPSULA ENDOSCOPICA 24H
            //EDA + PH METRIA 24 H

            if ($request->tipo_anestesia == '8' || $request->tipo_anestesia == '9' || $request->tipo_anestesia == '10' || $request->tipo_anestesia == '11' || $request->tipo_anestesia == '12') {
                $hor_f   = DB::table('tiempo_procedimiento')->where('id_procedimiento', $request->tipo_anestesia)->where('estado', '1')->first();
                $minutos = $hor_f->minutos;
                //dd($minutos);
                if ($minutos >= 1440) {
                    $fecha_final = strtotime("+$minutos minute", strtotime($request->fecha_operacion));
                    $fecha_final = date('d/m/Y', $fecha_final);
                    $hora_fin    = strtotime("+$minutos minute", strtotime($request->hora_ini));
                    $hora_fin    = date('H:i', $hora_fin);
                    //dd($hora_fin);
                    $view = \View::make('hc_admision.formato.evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia', 'fecha_final'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                } else {
                    $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime("+$minutos minute", strtotime($request->hora_ini));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = \View::make('hc_admision.formato.evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
            } else {
                //7: FIBROSCAN 20: MANOMETRIA ANORECTAL 2: MANOMETRÍA ESOFAGICA
                if ($id_principal == '7' || $id_principal == '20' || $id_principal == '2') {
                    $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = \View::make('hc_admision.formato.evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
                // 3: CÁPSULA ENDOSCÓPICA 1: PH-METRÍA
                if ($id_principal == '3' || $id_principal == '1' || $id_principal == '8' && $id_principal == '1') {
                    // add PH-METRIA Y EDA 16 Nov
                    $fecha_final = strtotime('+1440 minute', strtotime($request->fecha_operacion));
                    $fecha_final = date('d/m/Y', $fecha_final);
                    $hora_fin    = strtotime('+1440 minute', strtotime($request->hora_ini));
                    $hora_fin    = date('H:i', $hora_fin);
                    $view        = \View::make('hc_admision.formato.evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'fecha_final', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
            }
        } else {
        }

        if ($request->tipo_anestesia == 'GENERAL') {
            $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            //new changes times with new tables

            $view = \View::make('hc_admision.formato.evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');

            $pdf->loadHTML($view);

            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'GENERAL90') {
            $hora_media = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'GENERAL120') {
            $hora_media = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'GENERAL150') {
            $hora_media = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'GENERAL180') {
            $hora_media = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+240 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'GENERAL210') {
            $hora_media = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+270 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'SEDACION') {
            $hora_media = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        //change proced with new tables
        if (($procedimiento->id_procedimiento_completo == '38') || ($procedimiento->id_procedimiento_completo == '12') || ($procedimiento->id_procedimiento_completo == '27')) {
            $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if (($procedimiento->id_procedimiento_completo == '13') || ($procedimiento->id_procedimiento_completo == '14')) {
            $fecha_final = strtotime('+1440 minute', strtotime($request->fecha_operacion));
            $fecha_final = date('d/m/Y', $fecha_final);
            $hora_fin    = strtotime('+1440 minute', strtotime($request->hora_ini));
            $hora_fin    = date('H:i', $hora_fin);
            $view        = \View::make('hc_admision.formato.evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'fecha_final', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }

        //return "hola";
        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;

        //return $pdf->download('evolucion-'.$historiaclinica->id_paciente.'-'.$historiaclinica->hcid.'.pdf');

        return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
    }
    public function nuevo_guardar_op(Request $request)
    {
        //dd($request->all());
        $protocolo_original = null;
        $protocolo = hc_protocolo::find($request->protocolo);
        if ($request->xtipo == 'auditoria') {
            $protocolo = Aud_Hc_Protocolo::find($request->protocolo);
            $protocolo_original = hc_protocolo::find($protocolo->id_protocolo_org);
        }

        if ($request->tipo_anestesia == 'GENERAL') {
            $duracion = '60';
            $hora_fin = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL90') {
            $duracion = '90';
            $hora_fin = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL120') {
            $duracion = '120';
            $hora_fin = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL150') {
            $duracion = '150';
            $hora_fin = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL180') {
            $duracion = '180';
            $hora_fin = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } elseif ($request->tipo_anestesia == 'GENERAL210') {
            $duracion = '210';
            $hora_fin = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        } else {
            $duracion = '30';
            $hora_fin = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_fin = date('H:i', $hora_fin);
        }
        $input = [
            'fecha_operacion'     => $request->fecha_operacion,
            'hora_inicio'         => $request->hora_ini,
            'hora_fin'            => $hora_fin,
            'tipo_anestesia'      => $request->tipo_anestesia,
            'intervalo_anestesia' => $duracion,
        ];
        $protocolo->update($input);


        $input2 = [
            'id_doctor_examinador2'  => $request->id_doctor_examinador2,
            'id_doctor_ayudante_con' => $request->id_doctor_ayudante_con,
        ];



        if ($request->xtipo == 'auditoria') {

            $protocolo->auditoria_procedimiento->update($input2);
        } else {

            $protocolo->procedimiento->update($input2);
        }

        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();

        $id = $protocolo->id_hc_procedimientos;

        if ($request->xtipo == 'auditoria') {

            $evolucion = Aud_Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();
        } else {

            //$id = $protocolo->procedimiento->id;
            $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();
        }

        $indicaciones = [];
        foreach ($evolucion as $value) {
            if ($request->xtipo == 'auditoria') {

                $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion', $value->id_evolucion_org)->get();
            } else {

                $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion', $value->id)->get();
            }
        }

        $procedimiento = Hc_procedimientos::find($id);
        $proc_finales  = Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->get();
        $id_principal  = '';
        foreach ($proc_finales as $px) {
            if ($px->procedimiento->id_grupo_procedimiento != null) {
                $id_principal = $px->id_procedimiento;
                break;
            }
        }

        //dd($indicaciones);

        $historiaclinica = DB::table('historiaclinica')->where('historiaclinica.hcid', $procedimiento->id_hc)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario', 'paciente.cedulafamiliar', 'paciente.sexo', 'especialidad.nombre as enombre', 'historiaclinica.id_seguro as id_seguro')->first();

        $data            = $historiaclinica;
        $fecha_operacion = $request->fecha_operacion;
        $hora_ini        = $request->hora_ini;

        $cardiologia = null;
        if ($request->cardio == '1') {

            if ($request->id_cardio != null) {

                $cardiologia = Cardiologia::find($request->id_cardio);
            }
        }

        if (count($evolucion) <= '1') {
            $view = \View::make('hc_admision.formato.nuevo_evolucion_sin_nada', compact('data', 'evolucion', 'procedimiento', 'indicaciones'))->render();
            $pdf  = \App::make('dompdf.wrapper');

            $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }

        if ($id_principal != '') {

            // ENTRR CON LO NUEVOS OPTION DEL EXCEL
            //PH 24H
            //MANOMETRIA ESOFAGICA 2H Y 30 MIN
            //MANOMETRIA ANORECTAL 2H Y 30 MIN
            //CAPSULA ENDOSCOPICA 24H
            //EDA + PH METRIA 24 H

            if ($request->tipo_anestesia == '8' || $request->tipo_anestesia == '9' || $request->tipo_anestesia == '10' || $request->tipo_anestesia == '11' || $request->tipo_anestesia == '12') {
                $hor_f   = DB::table('tiempo_procedimiento')->where('id_procedimiento', $request->tipo_anestesia)->where('estado', '1')->first();
                $minutos = $hor_f->minutos;
                //dd($minutos);
                if ($minutos >= 1440) {
                    $fecha_final = strtotime("+$minutos minute", strtotime($request->fecha_operacion));
                    $fecha_final = date('d/m/Y', $fecha_final);
                    $hora_fin    = strtotime("+$minutos minute", strtotime($request->hora_ini));
                    $hora_fin    = date('H:i', $hora_fin);
                    //dd($hora_fin);
                    $view = \View::make('hc_admision.formato.nuevo_evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia', 'fecha_final'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                } else {
                    $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime("+$minutos minute", strtotime($request->hora_ini));
                    $hora_fin   = date('H:i', $hora_fin);

                    $view       = \View::make('hc_admision.formato.nuevo_evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
            } else {
                //7: FIBROSCAN 20: MANOMETRIA ANORECTAL 2: MANOMETRÍA ESOFAGICA
                if ($id_principal == '7' || $id_principal == '20' || $id_principal == '2') {
                    $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
                    $hora_media = date('H:i', $hora_media);
                    $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
                    $hora_fin   = date('H:i', $hora_fin);
                    $view       = \View::make('hc_admision.formato.nuevo_evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
                // 3: CÁPSULA ENDOSCÓPICA 1: PH-METRÍA
                if ($id_principal == '3' || $id_principal == '1' || $id_principal == '8' && $id_principal == '1') {
                    // add PH-METRIA Y EDA 16 Nov
                    $fecha_final = strtotime('+1440 minute', strtotime($request->fecha_operacion));
                    $fecha_final = date('d/m/Y', $fecha_final);
                    $hora_fin    = strtotime('+1440 minute', strtotime($request->hora_ini));
                    $hora_fin    = date('H:i', $hora_fin);
                    $view        = \View::make('hc_admision.formato.nuevo_evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'fecha_final', 'hora_fin', 'firma', 'cardiologia'))->render();

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($view);
                    return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
                }
            }
        } else {
        }

        if ($request->tipo_anestesia == 'GENERAL') {
            $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            //new changes times with new tables

            $view = \View::make('hc_admision.formato.nuevo_evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');

            $pdf->loadHTML($view);

            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'GENERAL90') {
            $hora_media = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.nuevo_evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'GENERAL120') {
            $hora_media = strtotime('+120 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.nuevo_evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'GENERAL150') {
            $hora_media = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.nuevo_evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'GENERAL180') {
            $hora_media = strtotime('+180 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+240 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.nuevo_evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'GENERAL210') {
            $hora_media = strtotime('+210 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+270 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.nuevo_evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }
        if ($request->tipo_anestesia == 'SEDACION') {
            $hora_media = strtotime('+30 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+90 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.nuevo_evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }

        $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
        $hora_media = date('H:i', $hora_media);
        $hora_fin   = strtotime('+120 minute', strtotime($request->hora_ini));
        $hora_fin   = date('H:i', $hora_fin);
        //new changes times with new tables

        $view = \View::make('hc_admision.formato.nuevo_evolucion', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view);

        return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        //change proced with new tables
        /*if (($procedimiento->id_procedimiento_completo == '38') || ($procedimiento->id_procedimiento_completo == '12') || ($procedimiento->id_procedimiento_completo == '27')) {
            $hora_media = strtotime('+60 minute', strtotime($request->hora_ini));
            $hora_media = date('H:i', $hora_media);
            $hora_fin   = strtotime('+150 minute', strtotime($request->hora_ini));
            $hora_fin   = date('H:i', $hora_fin);
            $view       = \View::make('hc_admision.formato.nuevo_evolucion3', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'hora_media', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');

        }
        if (($procedimiento->id_procedimiento_completo == '13') || ($procedimiento->id_procedimiento_completo == '14')) {
            $fecha_final = strtotime('+1440 minute', strtotime($request->fecha_operacion));
            $fecha_final = date('d/m/Y', $fecha_final);
            $hora_fin    = strtotime('+1440 minute', strtotime($request->hora_ini));
            $hora_fin    = date('H:i', $hora_fin);
            $view        = \View::make('hc_admision.formato.nuevo_evolucion4', compact('data', 'evolucion', 'procedimiento', 'indicaciones', 'fecha_operacion', 'hora_ini', 'fecha_final', 'hora_fin', 'firma', 'cardiologia'))->render();

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        }*/

        //return "hola";
        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view) /*->setPaper($paper_size, 'portrait')*/;

        //return $pdf->download('evolucion-'.$historiaclinica->id_paciente.'-'.$historiaclinica->hcid.'.pdf');

        return $pdf->stream('evolucion-' . $historiaclinica->id_paciente . '-' . $historiaclinica->hcid . '.pdf');
        exit(0);
    }

    public function cargar($id, $cardio)
    {
        //dd($id,$cardio);
        $id_doctor  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $evolucion = Hc_Evolucion::find($id);

        $historiaclinica = $evolucion->historiaclinica;

        $cie10 = Hc_Cie10::where('hcid', $historiaclinica->hcid)->get();
        //dd($cie10);
        foreach ($cie10 as $value) {

            $cardio_cie = Hc_cardio_cie10::where('cie10', $value->cie10)->where('presuntivo_definitivo', $value->presuntivo_definitivo)->where('id_cardio', $cardio)->first();
            if (is_null($cardio_cie)) {
                $arr = [

                    'id_cardio'             => $cardio,
                    'cie10'                 => $value->cie10,
                    'presuntivo_definitivo' => $value->presuntivo_definitivo,
                    'id_usuariocrea'        => $id_doctor,
                    'id_usuariomod'         => $id_doctor,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,

                ];

                Hc_cardio_cie10::create($arr);
            }
        }

        $cie102 = Aud_Hc_Cie10::where('hcid', $historiaclinica->hcid)->get();

        foreach ($cie102 as $value) {

            $cardio_cie = Hc_cardio_cie10::where('cie10', $value->cie10)->where('presuntivo_definitivo', $value->presuntivo_definitivo)->where('id_cardio', $cardio)->first();
            if (is_null($cardio_cie)) {
                $arr = [

                    'id_cardio'             => $cardio,
                    'cie10'                 => $value->cie10,
                    'presuntivo_definitivo' => $value->presuntivo_definitivo,
                    'id_usuariocrea'        => $id_doctor,
                    'id_usuariomod'         => $id_doctor,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,

                ];

                Hc_cardio_cie10::create($arr);
            }
        }

        return ['evolucion' => $evolucion, 'historia' => $historiaclinica];
    }

    public function actualizar_cardio(Request $request)
    {

        $id     = $request->cardioid;
        $cardio = Cardiologia::find($id);

        $arr = [
            'cuadro_actual'     => $request->historia_clinica_ev,
            'resultados'        => $request->resultado_ev,
            'examenes_realizar' => $request->examenes_realizar,
            'id_evo_orden'      => $request->id_evo_orden,
            'id_drsol'          => $request->id_doctor_examinador,

        ];

        $cardio->update($arr);

        return 'ok';
    }

    public function actualizar_cardio2(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id = $request['hcid'];

        $input2 = [
            'cuadro_clinico'   => $request["historia_clinica"],
            'resumen'          => $request["resumen"],
            'plan_diagnostico' => $request["plan_diagnostico"],
            'plan_tratamiento' => $request["plan_tratamiento"],
            'fecha_formato'    => $request["fecha_formato"],

            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,
        ];

        $cardiologia = Cardiologia::where('hcid', $id)->first();

        $cardiologia->update($input2);

        return "ok";
    }

    public function cargar_cie10($id)
    {

        $cie10 = Hc_cardio_cie10::where('id_cardio', $id)->get();
        //return $cie10;

        if (!is_null($cie10)) {
            $c10_arr = [];
            foreach ($cie10 as $c10) {
                $c3 = Cie_10_3::find($c10->cie10);
                $c4 = Cie_10_4::find($c10->cie10);
                if ($c3 != null) {
                    $c10_arr[$c10->id] = ['id' => $c10->id, 'cie10' => $c10->cie10, 'descripcion' => $c3->descripcion, 'pre_def' => $c10->presuntivo_definitivo];
                }
                if ($c4 != null) {
                    $c10_arr[$c10->id] = ['id' => $c10->id, 'cie10' => $c10->cie10, 'descripcion' => $c4->descripcion, 'pre_def' => $c10->presuntivo_definitivo];
                }
            }
        }

        return $c10_arr;
    }

    public function eliminar_cie10_cardio($id)
    {

        $cie10 = Hc_cardio_cie10::find($id);
        //return $cie10;
        if (!is_null($cie10)) {
            $cie10->delete();
        }

        return "ok";
    }

    public function agregar_cie10_ev(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($request['codigo'] == null) {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }

        $input2 = [
            'id_cardio'             => $request['cardio'],
            'cie10'                 => $request['codigo'],
            'presuntivo_definitivo' => $request['pre_def'],

            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,

        ];

        $id = Hc_cardio_cie10::insertGetId($input2);
        //return $id;

        $count = Hc_cardio_cie10::where('id_cardio', $request['cardio'])->get()->count();

        $cie10 = Hc_cardio_cie10::find($id);

        $c3 = Cie_10_3::find($cie10->cie10);
        if (!is_null($c3)) {
            $descripcion = $c3->descripcion;
        }
        $c4 = Cie_10_4::find($cie10->cie10);
        if (!is_null($c4)) {
            $descripcion = $c4->descripcion;
        }

        return ['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => $request['in_eg']];
    }

    public function actualizar_evolucion(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $id = $request['hcid'];

        $input1 = [

            'examenes_realizar' => $request["examenes_realizar"],
            'ip_modificacion'   => $ip_cliente,
            'id_usuariomod'     => $idusuario,
        ];
        Historiaclinica::where('hcid', $id)
            ->update($input1);

        $input_evo = [

            'cuadro_clinico'  => $request["historia_clinica"],
            'resultado'       => $request["resultado_ev"],

            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $id_evolucion = $request['id_evolucion'];

        Hc_Evolucion::where('id', $id_evolucion)
            ->update($input_evo);
        $interconsulta = Interconsulta::where('id_hc', $id)->where('estado', 1)->first();


        $input_evolucion = [
            'evolucion' => $request["historia_clinica"],
        ];
        /*if($id == 248046){
            dd($interconsulta,$input_evolucion,$interconsulta->update($input_evolucion));
        }*/
        $interconsulta->update($input_evolucion);


        return "ok";
    }

    public function formato($id)
    {

        $evolucion = Hc_Evolucion::find($id);

        $cardio = $evolucion->historiaclinica->cardio;
        //dd($cardio);
        $paciente = $evolucion->historiaclinica->paciente;

        $historiaclinica = DB::table('historiaclinica as hc')->where('hc.id_paciente', $paciente->id)->join('hc_procedimientos as hp', 'hp.id_hc', 'hc.hcid')
            ->join('hc_evolucion as he', 'he.hc_id_procedimiento', 'hp.id')->join('users as u', 'u.id', 'hp.id_doctor_examinador')->join('agenda as a', 'a.id', 'hc.id_agenda')->join('especialidad as e', 'e.id', 'a.espid')->select('he.*', 'u.apellido1', 'u.apellido2', 'u.nombre1', 'e.nombre')->orderBy('he.created_at', 'desc')->limit(15)->get();

        //dd($historiaclinica);
        return view('hc_admision/evolucion/cardiologia', ['evolucion' => $evolucion, 'historiaclinica' => $historiaclinica, 'cardio' => $cardio]);
    }

    public function imprimir_007($id)
    {
        $tipo_usuario  = Auth::user()->id_tipo_usuario;
        $evolucion = Hc_Evolucion::find($id);

        $cardio = $evolucion->historiaclinica->cardio;

        $age = Carbon::createFromDate(substr($evolucion->historiaclinica->paciente->fecha_nacimiento, 0, 4), substr($evolucion->historiaclinica->paciente->fecha_nacimiento, 5, 2), substr($evolucion->historiaclinica->paciente->fecha_nacimiento, 8, 2))->age;

        $cie10 = Hc_cardio_cie10::where('id_cardio', $cardio->id)->get();
        //return $cie10;

        if (!is_null($cie10)) {
            $c10_arr = [];
            foreach ($cie10 as $c10) {
                $c3 = Cie_10_3::find($c10->cie10);
                $c4 = Cie_10_4::find($c10->cie10);
                if ($c3 != null) {
                    $c10_arr[$c10->id] = ['cie10' => $c10->cie10, 'descripcion' => $c3->descripcion, 'pre_def' => $c10->presuntivo_definitivo];
                }
                if ($c4 != null) {
                    $c10_arr[$c10->id] = ['cie10' => $c10->cie10, 'descripcion' => $c4->descripcion, 'pre_def' => $c10->presuntivo_definitivo];
                }
            }
        }

        $cie10_2 = Hc_Cie10::where('hcid', $evolucion->historiaclinica->hcid)->get();
        //return $cie10;

        if (!is_null($cie10_2)) {
            $c10_arr_2 = [];
            foreach ($cie10_2 as $c10) {
                $c3 = Cie_10_3::find($c10->cie10);
                $c4 = Cie_10_4::find($c10->cie10);
                if ($c3 != null) {
                    $c10_arr_2[$c10->id] = ['cie10' => $c10->cie10, 'descripcion' => $c3->descripcion, 'pre_def' => $c10->presuntivo_definitivo];
                }
                if ($c4 != null) {
                    $c10_arr_2[$c10->id] = ['cie10' => $c10->cie10, 'descripcion' => $c4->descripcion, 'pre_def' => $c10->presuntivo_definitivo];
                }
            }
        }

        //dd($c10_arr);

        $view = \View::make('hc_admision.formato.f007', compact('evolucion', 'cardio', 'age', 'c10_arr', 'c10_arr_2'))->render();
        $pdf  = \App::make('dompdf.wrapper');
        $pdf->setOptions(['chroot'  => base_path('/')]);
        $pdf->loadHTML($view);
        $vt_name = '';
        if ($tipo_usuario == 11) {
            $vt_name = 'HCU_007.pdf';
            return $pdf->stream($vt_name);
        }
        return $pdf->stream('f007_' . $evolucion->historiaclinica->paciente->apellido1 . '_' . $evolucion->historiaclinica->paciente->nombre1 . '.pdf');
    }

    //new change oxigen control with patient
    public function oxigeno_modal(Request $request, $id)
    {
        //dd("hola");
        $protocolo = hc_protocolo::find($id);
        $proce = Hc_Procedimiento_Final::where("id_hc_procedimientos", $protocolo->id_hc_procedimientos)->get();
        $isCpre = $proce->contains('id_procedimiento', 38);
        $id_agenda = $protocolo->historiaclinica->id_agenda;
        $agenda    = Agenda::find($id_agenda);
        $fecha     = $agenda->fechaini;
        $horaini   = substr($agenda->fechaini, 11, -3);
        $horafin   = substr($agenda->fechafin, 11, -3);
        $anestesiologos = User::where('estado', '1')->where('id_tipo_usuario', '9')->get();
        return view('hc_admision/historia/modal', ['hora_inicio' => $horaini, 'fecha' => $fecha, 'hora_fin' => $horafin, 'protocolo' => $protocolo, 'agenda' => $agenda, 'anestesiologos' => $anestesiologos, 'isCpre' => $isCpre]);
    }
    public function pdfOxigeno(Request $request)
    {


        $empresa       = Empresa::find(session("id_empresa"));
        $agenda        =  $agenda = Agenda::find($request->agenda);
        $aud_protocolo = Aud_Hc_Protocolo::where("id_protocolo_org", $request->protocolo)->first() ?? hc_protocolo::find($request->protocolo);
        $anestesiologo = $request->anestesiologo;
        $porcentaje    = $request->porcentaje;
        $fecha    = $request->fecha_oxigeno;
        $hora_ini      = $request->hora_ini;
        $hora_fin      = $request->hora_fin;
        $horasSegundos = 0;
        $fechaini = date('Y-m-d') . " " . $hora_ini;
        $fechafin = date('Y-m-d') . " " . $hora_fin;
        $horasDiff = strtotime($fechaini) - strtotime($fechafin);
        $minutos  = ($horasDiff / 60) * (-1);
        $horas    = ($horasDiff / 3600) * (-1);
        $minutosDuracion = (int) round($minutos);
        $duracionHoras = sprintf('%d:%02d', intdiv($minutosDuracion, 60), $minutosDuracion % 60);
        $litros   = $request->litros;
        $totalLitros = $litros * $minutos;
        $valorUnitario = 0.01;
        $valorTotal  = $valorUnitario * $totalLitros;

        $logoBase64 = null;
        $logoPath = base_path() . '/storage/app/logo/' . $empresa->logo;
        if (!empty($empresa->logo) && file_exists($logoPath)) {
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $logoBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
        $firmaBase64 = null;
        $firma = Firma_Usuario::where('id_usuario', $anestesiologo)->first();
        if ($firma && $firma->nombre) {
            $firmaPath = base_path() . '/storage/app/avatars/' . $firma->nombre;
            if (file_exists($firmaPath)) {
                $ext = strtolower(pathinfo($firmaPath, PATHINFO_EXTENSION));
                $firmaBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($firmaPath));
            }
        }

        $fecha_operacion = $aud_protocolo->fecha_operacion;
        if ($fecha_operacion == null) {
            $fecha_operacion = substr($agenda->fechaini, 0, 10);
        }

        $cantidadSevo = null;
        if ($anestesiologo && $porcentaje) {
            $constante    = 3.33;
            $cantidadSevo = floor($constante * $porcentaje * $litros);
            $horasSegundos = $horasDiff * (-1);
        }

        $idsPermitidos = [24, 30, 31, 38, 41, 92, 99, 108, 109, 123, 132, 323, 343];


        $idsProcedimientos = $aud_protocolo->procedimiento
            ->hc_procedimiento_f
            ->pluck('id_procedimiento')
            ->toArray();

        $isProc = Procedimiento::whereIn('id', $idsProcedimientos)
            ->whereIn('id', $idsPermitidos)
            ->exists();

        if($agenda->id_seguro == 6){
            $isProc=true;
        }

        $view = \View::make('hc_admision.historia.consumo_oxigeno_pdf', compact(
            'empresa',
            'agenda',
            'fecha',
            'hora_ini',
            'hora_fin',
            'litros',
            'minutos',
            'horas',
            'duracionHoras',
            'totalLitros',
            'valorUnitario',
            'valorTotal',
            'logoBase64',
            'firmaBase64',
            'anestesiologo',
            'porcentaje',
            'cantidadSevo',
            'horasSegundos',
            'fecha_operacion',
            'aud_protocolo',
            'isProc'
        ))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Reporte_Consumo_Oxigeno_' . $fecha . '.pdf');
    }
    public function oxigenExcel(Request $request)
    {
        if (isset($request->pdf)) {
            return $this->pdfOxigeno($request);
        }
        $this->excelCabecera($request);
    }
    public function excelCabecera(Request $request)
    {
        $idagenda = $request->agenda;
        $agenda = Agenda::find($idagenda);
        $aud_protocolo = Aud_Hc_Protocolo::where("id_protocolo_org", $request->protocolo)->first() ?? hc_protocolo::find($request->protocolo);
        $hora_ini = $aud_protocolo->hora_inicio;
        $hora_fin = $aud_protocolo->hora_fin;
        $fecha    = $aud_protocolo->fecha_operacion;
        $anestesiologo = $request->anestesiologo;
        $porcentaje = $request->porcentaje;

        $id_empresa = $agenda->id_empresa;
        $empresa    = Empresa::find($id_empresa);
        $idsPermitidos = [24, 30, 31, 38, 41, 92, 99, 108, 109, 123, 132, 323, 343];


        $idsProcedimientos = $aud_protocolo->procedimiento
            ->hc_procedimiento_f
            ->pluck('id_procedimiento')
            ->toArray();
        
        
        $isProc = Procedimiento::whereIn('id', $idsProcedimientos)
            ->whereIn('id', $idsPermitidos)
            ->exists();
        if($agenda->id_seguro == 6){
            $isProc=true;
        }
        Excel::create('Reporte de consumo de oxigeno en pacientes ' . $fecha, function ($excel) use ($isProc, $fecha, $hora_ini, $hora_fin, $empresa, $agenda, $request, $anestesiologo, $porcentaje) {
            $excel->sheet('Informe Saldo', function ($sheet) use ($isProc, $fecha, $hora_ini, $hora_fin, $empresa, $agenda, $request, $anestesiologo, $porcentaje) {
                $logoPath = base_path() . '/storage/app/logo/' . $empresa->logo;
                if (!empty($empresa->logo) && file_exists($logoPath)) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setPath($logoPath);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }

                $sheet->mergeCells('A5:I5');

                $sheet->cell('A5', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setValue($empresa->razonsocial);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });

                $sheet->mergeCells('A8:I8');

                $sheet->cell('A8', function ($cell) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(16);
                    $cell->setValue('                                   REPORTE DE CONSUMO DE OXIGENO EN PACIENTES');
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });

                $sheet->cell('A10', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');

                    $cell->setValue('HC: ');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->cell('B10', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($agenda->paciente->id);
                    //   $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('A11', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('PACIENTE: ');

                    //   $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('B11', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue($agenda->paciente->apellido1 . " " . $agenda->paciente->apellido2 . " " . $agenda->paciente->nombre1 . " " . $agenda->paciente->nombre2);
                    //   $cell->setBorder('thin', '', '', '');
                });

                $sheet->cell('A14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->mergeCells('B14:C14');
                $sheet->cell('B14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('HORAS');

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('CODIGO');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('E14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('SERVICIO');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('F14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('litros');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('G14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('minutos');
                    $cell->setValignment('center');
                    //$cell->setBackground('#FFFF00');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('H14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('horas');
                    $cell->setValignment('center');
                    //$cell->setBackground('#FFFF00');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('I14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('TOTAL/LITROS');
                    //$cell->setBackground('#FFFF00');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('J14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('VALOR UNITARIO');
                    $cell->setValignment('center');
                    //$cell->setBackground('#FFFF00');
                    $cell->setBorder('thin', '', '', '');
                });
                $sheet->cell('K14', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('VALOR TOTAL');
                    //$cell->setBackground('#FFFF00');
                    // arriba,derecha,abajo,izquierda
                    //$cell->setBorder('thin', 'thin', '', '');
                    $cell->setBorder('thin', 'thin', '', '');
                });

                $sheet->cell('A15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                });
                //$sheet->mergeCells('B15:C15');
                $sheet->cell('B15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue('INICIO');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue('FINAL');
                    $cell->setValignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('D15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', 'thin');
                });
                $sheet->cell('E15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('F15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');

                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('G15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('H15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('I15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');
                });
                $sheet->cell('J15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('O');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('K15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValignment('center');
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('A15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                });
                //$sheet->mergeCells('B15:C15');
                $sheet->cell('B15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue('INICIO');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue('FINAL');
                    $cell->setValignment('center');
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('D15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', 'thin');
                });
                $sheet->cell('E15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('F15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');

                    $cell->setBorder('', '', 'thin', '');
                });
                $sheet->cell('G15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('H15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('I15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');
                });
                $sheet->cell('J15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('K15', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('');
                    $cell->setBorder('', 'thin', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });

                //AAAAAAA

                $sheet->cell('A16', function ($cell) use ($fecha) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue($fecha);
                    $cell->setBorder('thin', '', 'thin', '');
                });
                //$sheet->mergeCells('B15:C15');
                $sheet->cell('B16', function ($cell) use ($hora_ini) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($hora_ini);
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C16', function ($cell) use ($hora_fin) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $cell->setValue($hora_fin);
                    $cell->setBorder('thin', '', 'thin', '');
                });
                $sheet->cell('D16', function ($cell) use ($request) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    //$cell->setFontWeight('');
                    $cell->setValue('H900034');
                    $cell->setBorder('thin', '', 'thin', 'thin');
                });
                $sheet->cell('E16', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    //$cell->setFontWeight('bold');
                    $cell->setValue('OXIGENO (LITROS/MINUTO)');
                    $cell->setBorder('thin', '', 'thin', 'thin');
                });
                $sheet->cell('F16', function ($cell) use ($request) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue($request->litros);

                    $cell->setBorder('', '', 'thin', 'thin');
                });
                $sheet->cell('G16', function ($cell) use ($agenda, $request) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);

                    $horaini  = substr($request->hora_ini, 3);
                    $horafin  = substr($request->hora_fin, 3);
                    $horaini  = substr($request->hora_ini, 3);
                    $horafin  = substr($request->hora_fin, 3);
                    $fechaini = date('Y-m-d') . " " . $request->hora_ini;
                    $fechafin = date('Y-m-d') . " " . $request->hora_fin;
                    $hora     = strtotime($fechaini) - strtotime($fechafin);
                    $minutos  = $hora / 60 * (-1);
                    $segundos = $minutos / 60;
                    //$hora_final= $horafin-$horaini;
                    $cell->setFontWeight('bold');
                    $cell->setValue($minutos);
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('H16', function ($cell) use ($request, $agenda) {
                    // manipulate the cel
                    $fechaini = date('Y-m-d') . " " . $request->hora_ini;
                    $fechafin = date('Y-m-d') . " " . $request->hora_fin;
                    $hora     = strtotime($fechaini) - strtotime($fechafin);
                    $horas = intdiv($hora, 3600);
                    $minutos = intdiv($hora % 3600, 60);
                    $cell->setFontWeight('bold');
                    $cell->setValue((-1)*$horas . ':' . str_pad((-1)*$minutos, 2, '0', STR_PAD_LEFT));
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('I16', function ($cell) use ($request, $agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $litros = $request->litros;

                    $horaini  = substr($request->hora_ini, 3);
                    $horafin  = substr($request->hora_fin, 3);
                    $fechaini = date('Y-m-d') . " " . $request->hora_ini;
                    $fechafin = date('Y-m-d') . " " . $request->hora_fin;
                    $hora     = strtotime($fechaini) - strtotime($fechafin);
                    $minutos  = $hora / 60;
                    $segundos = $minutos / 60;
                    //dd($seconds/60);
                    $horafinal = $minutos;
                    $total     = $litros * $horafinal;
                    $cell->setFontWeight('bold');
                    $cell->setValue($total * (-1));
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');
                });
                $sheet->cell('J16', function ($cell) use ($request) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('0.01');
                    $cell->setBorder('', '', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $sheet->cell('K16', function ($cell) use ($request, $agenda) {
                    // manipulate the cel
                    $litros   = $request->litros;
                    $horaini  = substr($request->hora_ini, 3);
                    $horafin  = substr($request->hora_fin, 3);
                    $fechaini = date('Y-m-d') . " " . $request->hora_ini;
                    $fechafin = date('Y-m-d') . " " . $request->hora_fin;
                    $hora     = strtotime($fechaini) - strtotime($fechafin);
                    $minutos  = $hora / 60;
                    $segundos = $minutos / 60;
                    $time1    = new DateTime($fechaini);
                    $time2    = new DateTime($fechafin);
                    $interval = $time1->diff($time2);
                    //dd($interval);
                    $hora = $interval->format("%i min");
                    $hora = intval(substr($hora, 0, 2));
                    if ($hora == 0) {
                        $hora = 60;
                    }
                    $horafinal = $horafin - $horaini;
                    $total     = $litros * $minutos * (-1);
                    $tot       = 0.01 * $total;
                    $cell->setFontWeight('bold');
                    $cell->setValue($tot);
                    $cell->setBorder('', 'thin', 'thin', '');
                    //$cell->setBackground('#FFFF00');

                });
                $cordenadas = 'E20:G23';
                if ($isProc) {
                    $cordenadas = 'E24:G27';
                    $sheet->mergeCells('C18:I18');
                    $sheet->cell('C18', function ($cell) use ($empresa) {
                        $cell->setFontWeight('bold');
                        $cell->setFontSize(16);
                        $cell->setValue("REPORTE DE CONSUMO DE SEVOFLUORANO EN PACIENTES");
                        $cell->setValignment('center');
                    });


                    // FECHA
                    $sheet->mergeCells('B20:B21');
                    $sheet->cell('B20', function ($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setValue('FECHA');
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    // HORAS
                    $sheet->mergeCells('C20:D20');
                    $sheet->cell('C20', function ($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setValue('HORAS');
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    // INICIO
                    $sheet->cell('C21', function ($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setValue('INICIO');
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    // FINAL
                    $sheet->cell('D21', function ($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setValue('FINAL');
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    // PORCENTAJE % SEVOFLUORANO
                    $sheet->cell('E20', function ($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setValue("PORCENTAJE %\nSEVOFLUORANO");
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', '', '', 'thin');
                    });

                    // LITROS OXIGENO
                    $sheet->mergeCells('F20:F21');
                    $sheet->cell('F20', function ($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setValue('LITROS OXIGENO');
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    // HORAS
                    $sheet->mergeCells('G20:G21');
                    $sheet->cell('G20', function ($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setValue('HORAS');
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    // CONSTANTE
                    $sheet->mergeCells('H20:H21');
                    $sheet->cell('H20', function ($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setValue('CONSTANTE');
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    // CANTIDAD
                    $sheet->mergeCells('I20:I21');
                    $sheet->cell('I20', function ($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setValue('CANTIDAD');
                        $cell->setValignment('center');
                        $cell->setAlignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });



                    $sheet->cell('B22', function ($cell) use ($fecha) {
                        $cell->setFontWeight('bold');
                        $cell->setValue($fecha);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });


                    $sheet->cell('C22', function ($cell) use ($hora_ini) {
                        $cell->setValue($hora_ini);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D22', function ($cell) use ($hora_fin) {
                        $cell->setValue($hora_fin);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('E22', function ($cell) use ($porcentaje) {
                        $cell->setValue($porcentaje);
                        $cell->setFontWeight('bold');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F22', function ($cell) use ($request) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        $cell->setFontWeight('bold');
                        $cell->setValue($request->litros);

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G22', function ($cell) use ($request) {


                        $fechaini = date('Y-m-d') . " " . $request->hora_ini;
                        $fechafin = date('Y-m-d') . " " . $request->hora_fin;
                        $hora = strtotime($fechafin) - strtotime($fechaini);
                        $horas = intdiv($hora, 3600);
                        $minutos = intdiv($hora % 3600, 60);
                        $cell->setFontWeight('bold');
                        $cell->setValue($horas . ':' . str_pad($minutos, 2, '0', STR_PAD_LEFT));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H22', function ($cell) {

                        $cell->setFontWeight('bold');
                        $cell->setValue(3.33);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('I22', function ($cell) use ($request, $porcentaje) {
                        $constante = 3.33;
                        $cantidad = floor($constante * $porcentaje * $request->litros);

                        $cell->setFontWeight('bold');
                        $cell->setValue($cantidad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                }
                //final

                //FIRMA

                $sheet->mergeCells($cordenadas);
                $firma = Firma_Usuario::where('id_usuario', $anestesiologo)->first();

                if ($firma && $firma->nombre) {
                    $logoPath = base_path() . '/storage/app/avatars/' . $firma->nombre;
                    if (file_exists($logoPath)) {
                        $objDrawing = new PHPExcel_Worksheet_Drawing();
                        $objDrawing->setPath($logoPath);
                        $objDrawing->setCoordinates($isProc ? 'E24' : 'E20');
                        $objDrawing->setWidthAndHeight(200, 120);
                        $objDrawing->setWorksheet($sheet);
                    }
                }

                // DETALLES

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                ));

                //$i = $this->setDetalles($consulta, $sheet, 6);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                    'H' => 12,
                ));
            });
        })->export('xlsx');
    }
    //new visual download products
    public function productos_modal(Request $request, $id)
    {
        $protocolo = hc_protocolo::find($id);
        $id_agenda = $protocolo->historiaclinica->id_agenda;
        $agenda    = Agenda::find($id_agenda);
        $formatos  = FormatoDescargaInsumos::where('estado', '1')->get();
        return view('hc_admision/historia/modal_productos', ['protocolo' => $protocolo, 'agenda' => $agenda, 'formatos' => $formatos]);
    }
    public function descargo_productos(Request $request)
    {
        $protocolo = hc_protocolo::find($request->protocolo);
        $id_agenda = $protocolo->historiaclinica->id_agenda;
        //dd($id_agenda);
        $excel = $this->excelProductos($request, $protocolo);
    }
    public function excelProductos(Request $request, $protocolo)
    {
        $id_agenda = $protocolo->historiaclinica->id_agenda;
        $formato   = FormatoDescargaInsumos::find($request->tipo_formato);
        $fecha     = $request->fecha_oxigeno;
        $agenda    = Agenda::find($id_agenda);
        $empresaf  = $agenda->id_empresa;
        $empresa   = Empresa::find($empresaf);
        //dd($empresa);
        Excel::create('Reporte de consumo de oxigeno en pacientes ' . $fecha, function ($excel) use ($fecha, $empresa, $agenda, $request, $formato) {
            $excel->sheet('Informe Saldo', function ($sheet) use ($fecha, $empresa, $agenda, $request, $formato) {

                if ($empresa->logo != null) {
                    $sheet->mergeCells('A1:H1');
                    $objDrawing = new PHPExcel_Worksheet_Drawing;
                    $objDrawing->setPath(base_path() . '/storage/app/logo/' . $empresa->logo);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setHeight(70);
                    $objDrawing->setWorksheet($sheet);
                }

                $sheet->mergeCells('B1:F1');

                $sheet->cell('B1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(14);
                    $cell->setValue('                                 ' . $empresa->nombrecomercial . '/' . $empresa->razonsocial);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A2:F2');

                $sheet->cell('A2', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('                                 ' . $empresa->direccion);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A3:F3');

                $sheet->cell('A3', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('                                 Telefono:' . $empresa->telefono);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A4:F4');

                $sheet->cell('A4', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('                                 GUAYAQUIL-ECUADOR');
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A7:F7');

                $sheet->cell('A7', function ($cell) use ($empresa) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(14);
                    $cell->setValue('                                     LISTA DE MEDICAMENTOS Y MATERIALES USADOS EN SALA DE ENDOSCOPIA    ');
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A9:F9');

                $sheet->cell('A9', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('PACIENTE:  ' . $agenda->paciente->apellido1 . " " . $agenda->paciente->apellido2 . " " . $agenda->paciente->nombre1 . " " . $agenda->paciente->nombre2);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A10:C10');
                $sheet->cell('A10', function ($cell) use ($formato) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('PROCEDIMIENTO:  ' . $formato->descripcion);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->cell('D10', function ($cell) use ($fecha) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('FECHA:   ' . $fecha);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->mergeCells('A11:C11');
                $sheet->cell('A11', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('SEGURO:  ' . $agenda->seguro->nombre);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->cell('D11', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setValue('H.CLINICA:  ' . $agenda->paciente->id);
                    $cell->setValignment('center');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->cell('A13', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('NOMBRE');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    //   $cell->setBorder('', '', 'thin','');
                });
                $sheet->cell('B13', function ($cell) use ($agenda) {
                    // manipulate the cel
                    // $this->setSangria($cont, $cell);
                    $cell->setFontWeight('bold');
                    $cell->setValue('CANTIDAD');
                    $cell->setValignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //   $cell->setBorder('', '', 'thin','');
                });
                $acumulador  = 14;
                $contador    = 0;
                $cuentamitad = count($formato->detalles);
                $mitad       = count($formato->detalles) / 2;
                $mitadint    = intval($mitad);
                if ($cuentamitad > 1) {
                    $sheet->cell('D13', function ($cell) use ($agenda) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        $cell->setFontWeight('bold');
                        $cell->setValue('NOMBRE');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                        //   $cell->setBorder('', '', 'thin','');
                    });
                    $sheet->cell('E13', function ($cell) use ($agenda) {
                        // manipulate the cel
                        // $this->setSangria($cont, $cell);
                        $cell->setFontWeight('bold');
                        $cell->setValue('CANTIDAD');
                        $cell->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //   $cell->setBorder('', '', 'thin','');
                    });
                }

                $contadore   = 0;
                $acumuladore = 14;
                foreach ($formato->detalles as $val) {
                    //dd($val->hc_procedimiento_f);
                    //dd($val);
                    if ($cuentamitad == 1) {
                        $sheet->cell('A' . $acumulador, function ($cell) use ($val) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            //$cell->setFontWeight('bold');
                            $cell->setValue($val->nombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('B' . $acumulador, function ($cell) use ($val) {
                            // manipulate the cel
                            // $this->setSangria($cont, $cell);
                            //$cell->setFontWeight('bold');
                            $cell->setValue($val->cantidad);
                            $cell->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $acumulador++;
                    } else {
                        if ($contador < $mitadint) {
                            $sheet->cell('A' . $acumulador, function ($cell) use ($val) {
                                // manipulate the cel
                                // $this->setSangria($cont, $cell);
                                //$cell->setFontWeight('bold');
                                $cell->setValue($val->nombre);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('B' . $acumulador, function ($cell) use ($val) {
                                // manipulate the cel
                                // $this->setSangria($cont, $cell);
                                //$cell->setFontWeight('bold');
                                $cell->setValue($val->cantidad);
                                $cell->setValignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $acumulador++;
                        } else {
                            $sheet->cell('D' . $acumuladore, function ($cell) use ($val) {
                                // manipulate the cel
                                // $this->setSangria($cont, $cell);
                                //$cell->setFontWeight('bold');
                                $cell->setValue($val->nombre);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('E' . $acumuladore, function ($cell) use ($val) {
                                // manipulate the cel
                                // $this->setSangria($cont, $cell);
                                //$cell->setFontWeight('bold');
                                $cell->setValue($val->cantidad);
                                $cell->setValignment('center');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $acumuladore++;
                        }
                    }

                    $contador++;
                }

                foreach ($formato->detalles as $val) {
                    //dd($val->hc_procedimiento_f);
                    //dd($val);
                    if ($mitadint <= $cuentamitad) {

                        $acumuladore++;
                    }
                    $contador++;
                }

                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'F' => '0.00',
                    'G' => '0.00',
                    'H' => '0.00',
                ));

                //$i = $this->setDetalles($consulta, $sheet, 6);
                // $i = $this->setDetalles($pasivos, $sheet, $i);
                // $i = $this->setDetalles($patrimonio, $sheet, $i, $totpyg);

                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                    'H' => 12,
                ));
            });
            $excel->getActiveSheet()->getColumnDimension("A")->setWidth(27)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("B")->setWidth(27)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("C")->setWidth(27)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("D")->setWidth(27)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("E")->setWidth(27)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("F")->setWidth(27)->setAutosize(false);
        })->export('xlsx');
    }
}
