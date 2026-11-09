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
use Sis_medico\Services\ClinicalEvolutionInitializationService;

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
        if (in_array($rolUsuario, array(1, 3, 6, 11, 5, 7,20)) == false) {
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
        if ($this->rol_doctor()) {
            return response()->view('errors.404');
        }
        $result = app(ClinicalEvolutionInitializationService::class)->initialize(
            $id,
            $ag,
            Auth::user()->id,
            request()->ip()
        );

        return app(ConsultaController::class)->ingreso_actualiza_visita(
            $result['evolution_id'],
            $result['agenda_id']
        );

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
        if($request->xtipo == 'auditoria'){
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

        

        if($request->xtipo == 'auditoria'){

            $protocolo->auditoria_procedimiento->update($input2);
        
        }else{

            $protocolo->procedimiento->update($input2);
        
        }

        $firma = Firma_Usuario::where('id_usuario', $request->id_doctor_examinador2)->first();

        $id = $protocolo->id_hc_procedimientos;

        if($request->xtipo == 'auditoria'){
        
            $evolucion = Aud_Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();

        }else{

            //$id = $protocolo->procedimiento->id;
            $evolucion = Hc_Evolucion::where('hc_id_procedimiento', $id)->orderBy('secuencia')->get();

        }    

        $indicaciones = [];
        foreach ($evolucion as $value) {
            if($request->xtipo == 'auditoria'){

                $indicaciones[$value->id] = Hc_Evolucion_Indicacion::where('id_evolucion', $value->id_evolucion_org)->get();

            }else{

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
        exit (0);

    }

    public function cargar($id, $cardio)
    {
        //dd($id,$cardio);
        $id_doctor  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $evolucion = Hc_Evolucion::find($id);

        $historiaclinica = $evolucion->historiaclinica;

        $cie10 = Hc_Cie10::where('hcid', $historiaclinica->hcid)->get();

        foreach ($cie10 as $value) {

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

        $pdf->loadHTML($view);
        $vt_name = '';
        if($tipo_usuario == 11 ){
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
        $id_agenda = $protocolo->historiaclinica->id_agenda;
        $agenda    = Agenda::find($id_agenda);
        $fecha     = $agenda->fechaini;
        $horaini   = substr($agenda->fechaini, 11, -3);
        $horafin   = substr($agenda->fechafin, 11, -3);
        return view('hc_admision/historia/modal', ['hora_inicio' => $horaini, 'fecha' => $fecha, 'hora_fin' => $horafin, 'protocolo' => $protocolo, 'agenda' => $agenda]);
    }
    public function oxigenExcel(Request $request)
    {
        return app(\Sis_medico\Services\ClinicalConsumptionExportService::class)->oxygen($request);
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
        return app(\Sis_medico\Services\ClinicalConsumptionExportService::class)->products($request);
    }
}
