<?php

namespace Sis_medico\Http\Controllers;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Archivo_historico;
use Sis_medico\ControlDocController;
use Sis_medico\Empresa;
use Sis_medico\Especialidad;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_procedimientos;
use Sis_medico\Historiaclinica;
use Sis_medico\Orden;
use Sis_medico\Paciente_Doctor;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento;
use Sis_medico\Seguro;
use Sis_medico\User;
use Storage;

class AdelantadoController extends Controller
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

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 4, 5, 10, 11, 12, 7, 15, 20, 22)) == false) {
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
        $doctores       = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderby('apellido1')->get();
        $seguros        = Seguro::where('inactivo', '1')->get();
        $especialidades = Especialidad::where('estado', '1')->get();
        $procedimientos = Procedimiento::where('estado', '1')->orderby('nombre')->get();
        //dd($especialidades);

        $fecha       = date('Y/m/d');
        $fecha_hasta = date('Y/m/d');

        $agendas = DB::table('agenda')
            ->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->where('agenda.adelantado', '1')
            ->where('agenda.created_at', [$fecha . ' 00:00', $fecha . ' 23:59'])
            ->where('agenda.estado_cita', '<>', 3)
            ->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')
            ->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')
            ->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')
            ->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'agenda.seguro_gestionado')
            ->orderby('agenda.created_at', 'desc')
        //->where('proc_consul','<','2')->paginate(30);
        //->where('proc_consul','=','4')->paginate(30);
            ->where(function ($query) {$query->where('proc_consul', '<', '2')->orWhere('omni', '=', 'OM');})->get();

        $dp_proc              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        foreach ($agendas as $a2) {

            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();
            if (!is_null($historia)) {
                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, '1', $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                //dd($cant_ok);
                //$cant_pend = $cantidad_doc - $cant_ok;
                if ($cantidad_doc == 0) {
                    $porcentaje = 0;
                } else {
                    $porcentaje = ($cant_ok / $cantidad_doc) * 100;
                }
                $dp_proc += [
                    $a2->id => $porcentaje,
                ];
            }
        }

        return view('adelantado/index', ['agendas' => $agendas, 'proc_consul' => '2', 'cedula' => '', 'nombres' => '', 'fecha' => $fecha, 'pentax' => '2', 'dp_proc' => $dp_proc, 'fecha_hasta' => $fecha_hasta, 'doctores' => $doctores, 'id_doctor1' => null, 'seguros' => $seguros, 'id_seguro' => null, 'especialidades' => $especialidades, 'id_especialidad' => null, 'procedimientos' => $procedimientos, 'id_procedimiento' => null]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    public function search(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $proc_consul      = $request['proc_consul'];
        $cedula           = $request['cedula'];
        $nombres          = $request['nombres'];
        $fecha            = $request['fecha'];
        $fecha_hasta      = $request['fecha_hasta'];
        $pentax           = $request['pentax'];
        $id_doctor1       = $request['id_doctor1'];
        $id_seguro        = $request['id_seguro'];
        $espid            = $request['espid'];
        $id_procedimiento = $request['id_procedimiento'];

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderBy('apellido1')->get();
        if ($proc_consul == 'null') {
            $proc_consul = '1';
        }

        $seguros = Seguro::where('inactivo', '1')->get();

        $agendas = DB::table('agenda')
            ->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')
            ->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')
            ->where('agenda.estado_cita', '<>', 3)
            ->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')
            ->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'agenda.seguro_gestionado')
            ->orderby('agenda.fechaini', 'desc')
            ->where('agenda.adelantado', '1')
            ->where(function ($query) {
                $query->where('proc_consul', '<', '2')
                    ->orWhere('omni', '=', 'OM');});

        if ($proc_consul != '2') {
            $agendas = $agendas->where('agenda.proc_consul', $proc_consul);

        }

        if ($proc_consul == '1') {
            if ($pentax == '2') {
                $agendas = $agendas->where('hospital.id', '2');
            } elseif ($pentax == '0') {
                $agendas = $agendas->where('hospital.id', '<>', '2');
            }

        }

        if ($fecha != null && $fecha_hasta != null) {
            $agendas = $agendas->whereBetween('agenda.created_at', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);

        }

        if ($cedula != null) {
            $agendas = $agendas->where('agenda.id_paciente', $cedula);
        }

        if ($id_doctor1 != null) {
            $agendas = $agendas->where('agenda.id_doctor1', $id_doctor1);
        }

        if ($id_seguro != null) {
            $agendas = $agendas->where('agenda.id_seguro', $id_seguro);
        }

        if ($nombres != null) {
            //dd($agendas->get());

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $agendas = $agendas->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });

            } else {

                $agendas = $agendas->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }

        }

        if ($espid != null) {
            $agendas = $agendas->where('agenda.espid', $espid);
        }

        if ($id_procedimiento != null) {

            /*$agendas = $agendas->join('pentax as px','px.id_agenda','agenda.id')->join('pentax_procedimiento as ppx','ppx.id_pentax','px.id')->where('ppx.id_procedimiento',$id_procedimiento);*/
            //dd($agendas->get());
            $agendas = $agendas->leftjoin('agenda_procedimiento as apx', 'apx.id_agenda', 'agenda.id')
                ->where(function ($query) use ($id_procedimiento) {
                    $query->where('apx.id_procedimiento', $id_procedimiento)
                        ->orWhere('agenda.id_procedimiento', '=', $id_procedimiento);
                });
        }
        //dd($id_procedimiento,$agendas->get());

        $agendas = $agendas->paginate(30);

        $dp_proc              = [];
        $arr_prb              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        $i                    = 0;
        foreach ($agendas as $a2) {

            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();

            if (!is_null($historia)) {
                $i++;

                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, $a2->proc_consul, $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                //$cant_pend = $cantidad_doc - $cant_ok;
                if ($cantidad_doc == 0) {
                    $porcentaje = 0;
                } else {
                    $porcentaje = ($cant_ok / $cantidad_doc) * 100;
                }
                //$arr_prb += [$a2->id => [$cantidad_doc, $cant_ok, $cant_pend]];
                $dp_proc += [
                    $a2->id => $porcentaje,
                ];

            }
        }
        //dd($arr_prb);

        $especialidades = Especialidad::where('estado', '1')->get();
        $procedimientos = Procedimiento::where('estado', '1')->orderby('nombre')->get();

        return view('adelantado/index', ['agendas' => $agendas, 'proc_consul' => $proc_consul, 'cedula' => $cedula, 'nombres' => $nombres, 'fecha' => $fecha, 'pentax' => $pentax, 'dp_proc' => $dp_proc, 'fecha_hasta' => $fecha_hasta, 'doctores' => $doctores, 'id_doctor1' => $id_doctor1, 'seguros' => $seguros, 'id_seguro' => $id_seguro, 'especialidades' => $especialidades, 'id_especialidad' => $espid, 'procedimientos' => $procedimientos, 'id_procedimiento' => $id_procedimiento]);
    }

    /* Reporte de Ordenes de Biopsias*/

    private function doSearchingQuery()
    {

    }

    public function store(Request $request)
    {

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

    public function detalle($id)
    {
        /*if($this->rol()){
        return response()->view('errors.404');
        }*/

        //$agenda = Agenda::find($id);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('sala', 'sala.id', 'agenda.id_sala')
            ->leftjoin('hospital', 'hospital.id', 'sala.id_hospital')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'paciente.ocupacion', 'paciente.estadocivil', 'paciente.parentesco', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.color as color', 'seguros.nombre as sanombre', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'paciente.fecha_nacimiento', 'empresa.nombrecomercial')
            ->where('agenda.id', '=', $id)
            ->first();

        $historiaclinica = DB::table('historiaclinica as hc')
            ->join('users as d1', 'hc.id_doctor1', 'd1.id')
            ->join('seguros', 'hc.id_seguro', '=', 'seguros.id')
            ->leftjoin('pentax', 'pentax.hcid', 'hc.hcid')
            ->leftjoin('users as d2', 'hc.id_doctor2', 'd2.id')
            ->leftjoin('users as d3', 'hc.id_doctor3', 'd3.id')
            ->leftjoin('users as up', 'up.id', 'hc.id_usuario')
            ->leftjoin('subseguro', 'hc.id_subseguro', '=', 'subseguro.id')
            ->select('hc.*', 'seguros.nombre as snombre', 'seguros.color as color', 'seguros.tipo as stipo', 'subseguro.nombre as sbnombre', 'pentax.id', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'pentax.id as id_pentax', 'up.nombre1 as upnombre1', 'up.apellido1 as upapellido1', 'up.nombre2 as upnombre2', 'up.apellido2 as upapellido2')
            ->where('hc.id_agenda', '=', $agenda->id)
            ->first();

        // Redirect to user list if updating user wasn't existed
        if ($agenda == null || count($agenda) == 0) {
            return redirect()->intended('/consultam');
        }

        //dd($historiaclinica);

        $cantidad_doc = 0;
        $pentaxprocs  = null;
        $pentax_logs  = null;
        if (!is_null($historiaclinica)) {

            $ControlDocController = new hc_admision\ControlDocController;
            $cantidad_doc         = $ControlDocController->carga_documentos_union($historiaclinica->hcid, $agenda->proc_consul, $historiaclinica->stipo)->count();

            if (!is_null($historiaclinica->id_pentax)) {
                $pentaxprocs = DB::table('pentax_procedimiento')
                    ->join('procedimiento', 'procedimiento.id', 'pentax_procedimiento.id_procedimiento')
                    ->select('pentax_procedimiento.*', 'procedimiento.nombre')
                    ->where('id_pentax', $historiaclinica->id_pentax)->get();

                $pentax_logs = DB::table('pentax_log')->where('pentax_log.id_pentax', $historiaclinica->id_pentax)->join('users as d1', 'd1.id', '=', 'pentax_log.id_doctor1')->join('seguros', 'seguros.id', '=', 'pentax_log.id_seguro')->join('sala', 'sala.id', '=', 'pentax_log.id_sala')->join('users as um', 'um.id', '=', 'pentax_log.id_usuariomod')->leftJoin('users as d2', 'd2.id', '=', 'pentax_log.id_doctor2')->leftJoin('users as d3', 'd3.id', '=', 'pentax_log.id_doctor3')->select('pentax_log.*', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'sala.nombre_sala as nbrsala', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->orderBy('pentax_log.created_at')->get();
                //dd($pentaxprocs);
                //dd($pentax_logs);
            }

        }

        $agendaprocs = DB::table('agenda_procedimiento')
            ->join('procedimiento', 'procedimiento.id', 'agenda_procedimiento.id_procedimiento')
            ->select('agenda_procedimiento.*', 'procedimiento.nombre')
            ->where('id_agenda', $agenda->id)->get();
        //dd($agendaprocs);

        return view('adelantado/detalle', ['agenda' => $agenda, 'historiaclinica' => $historiaclinica, 'pentaxprocs' => $pentaxprocs, 'agendaprocs' => $agendaprocs, 'cantidad_doc' => $cantidad_doc, 'pentax_logs' => $pentax_logs]);
    }

    public function detalle_ag($id, $unix)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        //$agenda = Agenda::find($id);
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'agenda.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('sala', 'sala.id', 'agenda.id_sala')
            ->leftjoin('hospital', 'hospital.id', 'sala.id_hospital')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'paciente.ocupacion', 'paciente.estadocivil', 'paciente.parentesco', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.color as color', 'seguros.nombre as sanombre', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'paciente.fecha_nacimiento', 'empresa.nombrecomercial')
            ->where('agenda.id', '=', $id)
            ->first();

        $historiaclinica = DB::table('historiaclinica as hc')
            ->join('users as d1', 'hc.id_doctor1', 'd1.id')
            ->join('seguros', 'hc.id_seguro', '=', 'seguros.id')
            ->leftjoin('pentax', 'pentax.hcid', 'hc.hcid')
            ->leftjoin('users as d2', 'hc.id_doctor2', 'd2.id')
            ->leftjoin('users as d3', 'hc.id_doctor3', 'd3.id')
            ->leftjoin('users as up', 'up.id', 'hc.id_usuario')
            ->leftjoin('subseguro', 'hc.id_subseguro', '=', 'subseguro.id')
            ->select('hc.*', 'seguros.nombre as snombre', 'seguros.color as color', 'seguros.tipo as stipo', 'subseguro.nombre as sbnombre', 'pentax.id', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'pentax.id as id_pentax', 'up.nombre1 as upnombre1', 'up.apellido1 as upapellido1', 'up.nombre2 as upnombre2', 'up.apellido2 as upapellido2')
            ->where('hc.id_agenda', '=', $agenda->id)
            ->first();

        // Redirect to user list if updating user wasn't existed
        if ($agenda == null || count($agenda) == 0) {
            return redirect()->intended('/consultam');
        }

        //dd($historiaclinica);

        $cantidad_doc = 0;
        $pentaxprocs  = null;
        $pentax_logs  = null;
        if (!is_null($historiaclinica)) {

            $ControlDocController = new hc_admision\ControlDocController;
            $cantidad_doc         = $ControlDocController->carga_documentos_union($historiaclinica->hcid, $agenda->proc_consul, $historiaclinica->stipo)->count();

            if (!is_null($historiaclinica->id_pentax)) {
                $pentaxprocs = DB::table('pentax_procedimiento')
                    ->join('procedimiento', 'procedimiento.id', 'pentax_procedimiento.id_procedimiento')
                    ->select('pentax_procedimiento.*', 'procedimiento.nombre')
                    ->where('id_pentax', $historiaclinica->id_pentax)->get();

                $pentax_logs = DB::table('pentax_log')->where('pentax_log.id_pentax', $historiaclinica->id_pentax)->join('users as d1', 'd1.id', '=', 'pentax_log.id_doctor1')->join('seguros', 'seguros.id', '=', 'pentax_log.id_seguro')->join('sala', 'sala.id', '=', 'pentax_log.id_sala')->join('users as um', 'um.id', '=', 'pentax_log.id_usuariomod')->leftJoin('users as d2', 'd2.id', '=', 'pentax_log.id_doctor2')->leftJoin('users as d3', 'd3.id', '=', 'pentax_log.id_doctor3')->select('pentax_log.*', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1', 'seguros.nombre as snombre', 'sala.nombre_sala as nbrsala', 'um.nombre1 as umnombre1', 'um.apellido1 as umapellido1')->orderBy('pentax_log.created_at')->get();
                //dd($pentaxprocs);
                //dd($pentax_logs);
            }

        }

        $agendaprocs = DB::table('agenda_procedimiento')
            ->join('procedimiento', 'procedimiento.id', 'agenda_procedimiento.id_procedimiento')
            ->select('agenda_procedimiento.*', 'procedimiento.nombre')
            ->where('id_agenda', $agenda->id)->get();
        //dd($agendaprocs);

        return view('adelantado/detalle_ag', ['agenda' => $agenda, 'historiaclinica' => $historiaclinica, 'pentaxprocs' => $pentaxprocs, 'agendaprocs' => $agendaprocs, 'cantidad_doc' => $cantidad_doc, 'pentax_logs' => $pentax_logs, 'unix' => $unix]);
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
    public function consulta_documentos($hcid)
    {

        /*if($this->rol()){
        return response()->view('errors.404');
        }*/
        $historia = DB::table('historiaclinica')->where('historiaclinica.hcid', $hcid)->join('paciente', 'paciente.id', 'historiaclinica.id_paciente')->join('seguros', 'seguros.id', 'historiaclinica.id_seguro')->join('users', 'users.id', 'paciente.id_usuario')->join('agenda', 'agenda.id', 'historiaclinica.id_agenda')->leftjoin('subseguro', 'subseguro.id', 'historiaclinica.id_subseguro')->select('historiaclinica.*', 'paciente.nombre1', 'paciente.nombre2', 'paciente.apellido1', 'paciente.apellido2', 'seguros.nombre', 'agenda.fechaini', 'paciente.fecha_nacimiento', 'agenda.proc_consul', 'agenda.id_procedimiento', 'seguros.tipo', 'subseguro.nombre as sbnombre', 'paciente.telefono1', 'paciente.telefono2', 'users.nombre1 as unombre1', 'users.nombre2 as unombre2', 'users.apellido1 as uapellido1', 'users.apellido2 as uapellido2', 'paciente.parentesco', 'users.telefono1 as utelefono1', 'users.telefono2 as utelefono2', 'users.id as uid', 'paciente.parentescofamiliar as fparentesco', 'paciente.nombre1familiar as fnombre1', 'paciente.nombre2familiar as fnombre2', 'paciente.apellido1familiar as fapellido1', 'paciente.apellido2familiar as fapellido2', 'paciente.id_usuario')->first();

        $procs = null;

        if ($historia->proc_consul == 1) {

            $procs = Procedimiento::find($historia->id_procedimiento)->observacion;

            $procs_ag = AgendaProcedimiento::where('id_agenda', $historia->id_agenda)->get();

            if (!is_null($procs_ag)) {
                foreach ($procs_ag as $value) {
                    $p = Procedimiento::find($value->id_procedimiento)->observacion;

                    $procs = $procs . " + " . $p;
                }
            }

        }

        $ControlDocController = new hc_admision\ControlDocController;
        $documentos           = $ControlDocController->carga_documentos_union($hcid, $historia->proc_consul, $historia->tipo);

        return view('adelantado.consulta_documentos', ['documentos' => $documentos, 'historia' => $historia, 'procs' => $procs, 'hcid' => $hcid]);

    }
    public function reporte(Request $request)
    {

        $proc_consul      = $request['proc_consul'];
        $cedula           = $request['cedula'];
        $nombres          = $request['nombres'];
        $fecha            = $request['fecha'];
        $fecha_hasta      = $request['fecha_hasta'];
        $pentax           = $request['pentax'];
        $id_doctor1       = $request['id_doctor1'];
        $id_seguro        = $request['id_seguro'];
        $espid            = $request['espid'];
        $id_procedimiento = $request['id_procedimiento'];

        //dd($request->all());

        $doctores = User::where('id_tipo_usuario', 3)->where('estado', 1)->get();
        if ($proc_consul == 'null') {
            $proc_consul = '1';
        }

        /*$agendas = DB::table('agenda')->join('paciente','paciente.id','=','agenda.id_paciente')
        ->join('seguros','seguros.id','=','agenda.id_seguro')
        ->leftjoin('users as d1','d1.id','=','agenda.id_doctor1')
        ->join('users as uc','uc.id','=','agenda.id_usuariocrea')
        ->join('users as au','au.id','=','agenda.id_usuariomod')
        ->leftjoin('sala','sala.id','=','agenda.id_sala')
        ->leftjoin('procedimiento','procedimiento.id','=','agenda.id_procedimiento')
        ->leftjoin('hospital','hospital.id','=','sala.id_hospital')
        ->leftjoin('pentax','pentax.id_agenda','agenda.id')
        ->leftjoin('seguros as seguro_pentax','seguro_pentax.id','=','pentax.id_seguro')
        ->leftjoin('users as dp1','dp1.id','=','pentax.id_doctor1')
        ->leftjoin('users as d2','d2.id','pentax.id_doctor2')
        ->leftjoin('users as d3','d3.id','pentax.id_doctor3')
        ->select('agenda.*','paciente.nombre1 as pnombre1','paciente.nombre2 as pnombre2','paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','seguros.nombre as senombre','d1.nombre1 as dnombre1','d1.apellido1 as dapellido1','uc.apellido1 as ucapellido','uc.nombre1 as ucnombre','au.nombre1 as aunombre1','au.nombre2 as aunombre2','au.apellido1 as auapellido1','procedimiento.observacion as probservacion','sala.nombre_sala as snombre','d1.color as d1color','seguros.color as scolor','dp1.nombre1 as dp1nombre1','dp1.apellido1 as dp1apellido1','pentax.id as pxid','d2.apellido1 as d2apellido1','d3.apellido1 as d3apellido1','pentax.estado_pentax','pentax.ingresa_alt','paciente.ciudad', 'seguro_pentax.nombre as seguro_pentax','paciente.direccion','paciente.telefono1','paciente.telefono2','paciente.telefono3','paciente.parentesco','paciente.id_usuario','paciente.referido')
        ->orderby('agenda.fechaini','desc')
        ->where('proc_consul','<','2');*/

        $agendas = DB::table('agenda')->join('paciente', 'paciente.id', '=', 'agenda.id_paciente')
            ->join('seguros', 'seguros.id', '=', 'agenda.id_seguro')
            ->leftjoin('users as d1', 'd1.id', '=', 'agenda.id_doctor1')
            ->join('users as uc', 'uc.id', '=', 'agenda.id_usuariocrea')
            ->join('users as au', 'au.id', '=', 'agenda.id_usuariomod')
            ->leftjoin('sala', 'sala.id', '=', 'agenda.id_sala')
            ->leftjoin('procedimiento', 'procedimiento.id', '=', 'agenda.id_procedimiento')
            ->leftjoin('hospital', 'hospital.id', '=', 'sala.id_hospital')
            ->leftjoin('pentax', 'pentax.id_agenda', 'agenda.id')
            ->leftjoin('seguros as seguro_pentax', 'seguro_pentax.id', '=', 'pentax.id_seguro')
            ->leftjoin('users as dp1', 'dp1.id', '=', 'pentax.id_doctor1')
            ->leftjoin('users as d2', 'd2.id', 'pentax.id_doctor2')
            ->leftjoin('users as d3', 'd3.id', 'pentax.id_doctor3')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.fecha_nacimiento as fech_nacimiento', 'seguros.nombre as senombre', 'd1.nombre1 as dnombre1', 'd1.apellido1 as dapellido1', 'uc.apellido1 as ucapellido', 'uc.nombre1 as ucnombre', 'au.nombre1 as aunombre1', 'au.nombre2 as aunombre2', 'au.apellido1 as auapellido1', 'procedimiento.observacion as probservacion', 'sala.nombre_sala as snombre', 'd1.color as d1color', 'seguros.color as scolor', 'dp1.nombre1 as dp1nombre1', 'dp1.apellido1 as dp1apellido1', 'pentax.id as pxid', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1', 'pentax.estado_pentax', 'pentax.ingresa_alt', 'paciente.ciudad', 'seguro_pentax.nombre as seguro_pentax', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'paciente.telefono3', 'paciente.parentesco', 'paciente.id_usuario', 'paciente.referido', 'pentax.id_anestesiologo', 'paciente.origen', 'paciente.origen2', 'paciente.otro')
            ->orderby('agenda.fechaini', 'desc')
            ->where(function ($query) {$query->where('proc_consul', '<', '2')->orWhere('omni', '=', 'OM');});

        if ($proc_consul != '2') {
            $agendas = $agendas->where('agenda.proc_consul', $proc_consul);

        }

        if ($proc_consul == '1') {
            if ($pentax == '2') {
                $agendas = $agendas->where('hospital.id', '2');
            } elseif ($pentax == '0') {
                $agendas = $agendas->where('hospital.id', '<>', '2');
            }

        }

        if ($fecha != null && $fecha_hasta != null) {
            $agendas = $agendas->whereBetween('agenda.fechaini', [$fecha . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($cedula != null) {
            $agendas = $agendas->where('agenda.id_paciente', $cedula);
        }

        if ($id_doctor1 != null) {
            $agendas = $agendas->where('agenda.id_doctor1', $id_doctor1);
        }

        if ($id_seguro != null) {
            $agendas = $agendas->where('agenda.id_seguro', $id_seguro);
        }

        if ($nombres != null) {

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            if ($cantidad == '2' || $cantidad == '3') {
                $agendas = $agendas->where(function ($jq1) use ($nombres) {
                    $jq1->orwhereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%'])
                        ->orwhereraw('CONCAT(paciente.nombre1," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
                });

            } else {

                $agendas = $agendas->whereraw('CONCAT(paciente.nombre1," ",paciente.nombre2," ",paciente.apellido1," ",paciente.apellido2) LIKE ?', ['%' . $nombres . '%']);
            }

        }

        if ($espid != null) {
            $agendas = $agendas->where('agenda.espid', $espid);
        }

        if ($id_procedimiento != null) {

            /*$agendas = $agendas->join('pentax as px','px.id_agenda','agenda.id')->join('pentax_procedimiento as ppx','ppx.id_pentax','px.id')->where('ppx.id_procedimiento',$id_procedimiento);*/
            $agendas = $agendas->leftjoin('agenda_procedimiento as apx', 'apx.id_agenda', 'agenda.id')
                ->where(function ($query) use ($id_procedimiento) {
                    $query->where('apx.id_procedimiento', $id_procedimiento)
                        ->orWhere('agenda.id_procedimiento', '=', $id_procedimiento);
                });
        }

        $agendas = $agendas->get();

        $dp_proc              = [];
        $ControlDocController = new hc_admision\ControlDocController;
        $i                    = 0;
        foreach ($agendas as $a2) {

            $historia = Historiaclinica::where('id_agenda', $a2->id)->first();

            if (!is_null($historia)) {
                $i++;

                $hSeguro      = Seguro::find($historia->id_seguro);
                $cantidad_doc = $ControlDocController->carga_documentos_union($historia->hcid, $a2->proc_consul, $hSeguro->tipo)->count();
                $cant_ok      = Archivo_historico::where('id_historia', $historia->hcid)->where('estado', '1')->get()->count();
                $cant_pend    = $cantidad_doc - $cant_ok;

                $dp_proc += [
                    $a2->id => $cant_pend,
                ];

            }
        }
        //dd($agendas);

        $fecha_d = date('Y/m/d');
        Excel::create('Agenda-' . $fecha_d, function ($excel) use ($agendas, $fecha) {

            $excel->sheet('Consulta Agendas', function ($sheet) use ($agendas, $fecha) {
                $fecha_d = date('Y/m/d');
                $i       = 5;
                $sheet->mergeCells('A1:S1');

                $sheet->mergeCells('A2:P2');
                $sheet->mergeCells('Q2:V2');
                $sheet->mergeCells('W2:Z2');
                $mes = substr($fecha_d, 5, 2);
                if ($mes == 01) {$mes_letra = "ENERO";}
                if ($mes == 02) {$mes_letra = "FEBRERO";}
                if ($mes == 03) {$mes_letra = "MARZO";}
                if ($mes == 04) {$mes_letra = "ABRIL";}
                if ($mes == 05) {$mes_letra = "MAYO";}
                if ($mes == 06) {$mes_letra = "JUNIO";}
                if ($mes == 07) {$mes_letra = "JULIO";}
                if ($mes == '08') {$mes_letra = "AGOSTO";}
                if ($mes == '09') {$mes_letra = "SEPTIEMBRE";}
                if ($mes == '10') {$mes_letra = "OCTUBRE";}
                if ($mes == '11') {$mes_letra = "NOVIEMBRE";}
                if ($mes == '12') {$mes_letra = "DICIEMBRE";}
                $fecha2 = 'FECHA: ' . substr($fecha_d, 8, 2) . ' de ' . $mes_letra . ' DEL ' . substr($fecha_d, 0, 4);
                $sheet->cell('A1', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('CONSULTAS Y PROCEDIMIENTOS AGENDADOS' . ' - ' . $fecha2);

                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('A2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('DATOS DE LA AGENDA');
                    $cell->setBackground('#80ccff');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('DATOS PENTAX');
                    $cell->setBackground('#c2f0f0');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W2', function ($cell) use ($fecha2) {
                    // manipulate the cel
                    $cell->setValue('DIRECCIONES Y TELEFONOS');
                    $cell->setBackground('#c2f000');
                    $cell->setFontWeight('bold');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                $sheet->cells('A1:K3', function ($cells) {
                    // manipulate the range of cells
                    $cells->setAlignment('center');
                });

                $sheet->cells('A4:AA4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setFontWeight('bold');
                });
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HORA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('C4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PACIENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('D4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA NACIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('E4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('F4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CÉDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('G4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('VIP');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('H4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('PARTICULAR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('I4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('CORTESIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('J4', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('K4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SALA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('L4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('M4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO/CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('N4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('INGRESA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('O4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MODIFICA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('P4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Q4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONSECUTIVO/PRIMERA VEZ');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('R4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACIÓN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('S4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIUDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('T4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ULTIMA MODIFICACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('U4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DOCTOR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('V4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('W4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('SEGURO/CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('X4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Y4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('Z4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ALTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //DIRECCIONES Y TELEFONOS
                $sheet->cell('AA4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CIUDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AB4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DIRECCION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AC4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AD4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MAIL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AE4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORIGEN');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AF4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DETALLE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AG4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OTROS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AH4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('AMB/HOSP');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AI4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OMNI');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AJ4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ANESTESIOLOGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cell('AK4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ORDENES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });

                foreach ($agendas as $value) {
                    $historiaclinica = Historiaclinica::where('id_agenda', $value->id)->first();
                    $txt_pro         = '';
                    if (!is_null($historiaclinica)) {
                        $evolucion = Hc_Evolucion::where('hcid', $historiaclinica->hcid)->where('secuencia', 0)->first();
                        if (!is_null($evolucion)) {
                            $orden = Orden::where('id_evolucion', $evolucion->id)->first();
                            if (!is_null($orden)) {
                                $orden_tipo = $orden->orden_tipo;
                                foreach ($orden_tipo as $ot) {
                                    $orden_proc = $ot->orden_procedimiento;
                                    foreach ($orden_proc as $op) {
                                        $procedimiento = $op->procedimiento;
                                        if (!is_null($procedimiento)) {
                                            if ($txt_pro == '') {
                                                $txt_pro = $procedimiento->nombre;
                                            } else {
                                                $txt_pro = $txt_pro . '+' . $procedimiento->nombre;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $empresa = null;
                    if ($value->id_empresa != null) {
                        $empresa = Empresa::find($value->id_empresa);
                    }
                    $txtcolor = '#000000';
                    if ($value->estado_cita != 0) {
                        $txtcolor = $value->scolor;

                        if ($value->paciente_dr == 1) {
                            $txtcolor = $value->d1color;

                        }
                    }

                    $sheet->cell('A' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue(substr($value->fechaini, 11, 5));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        if ($value->papellido2 != "(N/A)") {
                            $vnombre = $value->papellido1 . ' ' . $value->papellido2;
                        } else {
                            $vnombre = $value->papellido1;
                        }

                        if ($value->pnombre2 != "(N/A)") {
                            $vnombre = $vnombre . ' ' . $value->pnombre1 . ' ' . $value->pnombre2;
                        } else {
                            $vnombre = $vnombre . ' ' . $value->pnombre1;
                        }
                        $cell->setFontColor($txtcolor);
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');

                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->fech_nacimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('E' . $i, function ($cell) use ($value, $txtcolor) {
                        if (!is_null($value->fech_nacimiento)) {
                            $fecha           = $value->fech_nacimiento;
                            list($Y, $m, $d) = explode("-", $fecha);
                            $edad            = (date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y);
                            $cell->setValue($edad);
                        } else {
                            $cell->setValue("");
                        }
                        //$cell->setValue($value->edad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value, $txtcolor) {

                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $vip = '';
                        if ($value->vip == '1') {
                            $vip = 'VIP';
                        }
                        $cell->setValue($vip);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $part = '';
                        if ($value->paciente_dr == '1') {
                            $part = 'PART.';
                        }
                        if ($value->proc_consul == '1') {
                            $pac_doc = Paciente_Doctor::where('id_paciente', $value->id_paciente)->first();
                            if (!is_null($pac_doc)) {

                                $xdoc = $pac_doc->doctor;
                                //dd($xdoc);
                                if (!is_null($xdoc)) {
                                    $part = $xdoc->apellido1 . ' ' . $xdoc->nombre1;
                                }

                            }
                        }
                        $cell->setValue($part);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('I' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel

                        $cell->setValue($value->cortesia);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->dnombre1 . ' ' . $value->dapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value, $txtcolor, $historiaclinica, $empresa) {
                        // manipulate the cel
                        if ($value->omni == 'OM') {
                            if ($value->proc_consul == '4') {
                                $cell->setValue($value->sala_hospital);
                            }
                        } else {
                            $cell->setValue($value->snombre);
                        }
                        //$cell->setValue($value->snombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    /*if(!is_null($value->probservacion)){
                    $vproc = $value->probservacion;
                    }
                    else{
                    if($value->observaciones = 'EVOLUCION CREADA POR EL DOCTOR')
                    {
                    if($value->omni = 'OM'){
                    $vproc = 'VISITA OMNI';
                    }else{
                    $vproc = 'VISITA';
                    }
                    }
                    }

                    $agprocedimientos = AgendaProcedimiento::where('id_agenda',$value->id)->get();
                    if(!$agprocedimientos->isEmpty()){
                    foreach($agprocedimientos as $agendaproc){
                    $vproc = $vproc.' + '.Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                    }
                    } */

                    if ($value->proc_consul == '0') {
                        $vproc = 'CONSULTA';
                    } else {
                        if ($value->proc_consul == '1') {

                            if (!is_null($value->probservacion)) {
                                $vproc = $value->probservacion;
                            }

                            $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id)->get();
                            if (!$agprocedimientos->isEmpty()) {
                                foreach ($agprocedimientos as $agendaproc) {
                                    $vproc = $vproc . ' + ' . Procedimiento::find($agendaproc->id_procedimiento)->observacion;
                                }
                            }
                        } else {

                            if ($value->observaciones == 'EVOLUCION CREADA POR EL DOCTOR') {
                                if ($value->omni == 'OM') {
                                    $vproc = 'VISITA OMNI';
                                } else {
                                    $vproc = 'VISITA';
                                }
                            }
                        }
                    }

                    $sheet->cell('L' . $i, function ($cell) use ($value, $vproc, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($vproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    /*$sheet->cell('K'.$i, function($cell) use($value, $txtcolor, $historiaclinica, $empresa) {
                    // manipulate the cel
                    $consultorio='';
                    if($value->consultorio!='0'){
                    $consultorio='/CONSULTORIO';
                    }

                    if(is_null($historiaclinica)){
                    $cell->setValue($value->senombre.$consultorio);
                    }else{
                    if($empresa!=null){
                    $cell->setValue($historiaclinica->seguro->nombre.$consultorio.'/'.$empresa->nombre_corto);
                    }else{
                    $cell->setValue($historiaclinica->seguro->nombre.$consultorio);
                    }

                    }

                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontColor($txtcolor);
                    });*/

                    //$historiaclinica = Historiaclinica::where('id_agenda',$value->id)->first();

                    $hc_seguro = null;
                    $hc        = null;
                    $hc_proc   = null;

                    $hc = Historiaclinica::where('id_agenda', $value->id)->first();

                    if (!is_null($hc)) {

                        $hc_proc = hc_procedimientos::where('id_hc', $hc->hcid)->first();

                    }

                    if (!is_null($hc_proc)) {
                        if ($hc_proc->id_seguro != null) {
                            $hc_seguro = Seguro::find($hc_proc->id_seguro)->nombre;
                        }
                    }

                    $sheet->cell('M' . $i, function ($cell) use ($value, $txtcolor, $historiaclinica, $empresa, $hc_seguro) {
                        // manipulate the cel
                        $consultorio = '';
                        if ($value->consultorio != '0') {
                            $consultorio = '/CONSULTORIO';
                        }

                        if (is_null($historiaclinica)) {
                            $cell->setValue($value->senombre . $consultorio);
                        } else {

                            if ($value->omni == 'OM') {

                                $cell->setValue($hc_seguro);

                            } else {

                                $cell->setValue($value->senombre);

                            }

                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ucnombre . ' ' . $value->ucapellido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('O' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->aunombre1 . ' ' . $value->auapellido1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $vest = null;
                    if ($value->omni == 'OM') {
                        if ($value->estado_cita == '4') {
                            $vest = 'INGRESO';
                        } elseif ($value->estado_cita == '5') {
                            $vest = 'ALTA';
                        } elseif ($value->estado_cita == '6') {
                            $vest = 'EMERGENCIA';
                        }
                    } else {
                        if ($value->estado_cita == '0') {
                            $vest = 'Por Confirmar';
                        } elseif ($value->estado_cita == '1') {
                            $vest = 'Confirmado';
                        } elseif ($value->estado_cita == '3') {
                            $vest = 'Suspendido';
                        } elseif ($value->estado_cita == '-1') {
                            $vest = 'No Asiste';
                        } elseif ($value->estado_cita == '4') {
                            $vest = 'Asistió';
                        } elseif ($value->estado_cita == '2') {
                            if ($value->estado == '1') {
                                $vest = 'Completar Datos';
                            } else {
                                $vest = 'Reagendar';
                            }
                        }
                    }

                    $sheet->cell('P' . $i, function ($cell) use ($value, $vest, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($vest);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $vcita = "";
                    if ($value->tipo_cita == '0') {$vcita = 'PRIMERA VEZ';} else { $vcita = 'CONSECUTIVO';}
                    $sheet->cell('Q' . $i, function ($cell) use ($value, $vcita, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($vcita);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('R' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->observaciones);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('S' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ciudad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('T' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->updated_at);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('U' . $i, function ($cell) use ($value, $txtcolor) {

                        if ($value->proc_consul == 0) {
                            $historia = historiaclinica::where('id_agenda', '=', $value->id)->first();
                            if (!is_null($historia)) {
                                $procedimiento = hc_procedimientos::where('id_hc', $historia->hcid)->first();
                                if (!is_null($procedimiento)) {
                                    // manipulate the cel
                                    if (!is_null($procedimiento->id_doctor_examinador)) {
                                        $cell->setValue($procedimiento->doctor->nombre1 . ' ' . $procedimiento->doctor->apellido1);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setFontColor($txtcolor);
                                    } else {
                                        // manipulate the cel
                                        $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setFontColor($txtcolor);
                                    }
                                } else {
                                    // manipulate the cel
                                    $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    $cell->setFontColor($txtcolor);
                                }
                            } else {
                                // manipulate the cel
                                $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                $cell->setFontColor($txtcolor);
                            }
                        } else {
                            // manipulate the cel
                            $cell->setValue($value->dp1nombre1 . ' ' . $value->dp1apellido1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setFontColor($txtcolor);
                        }

                    });

                    $txtpproc = "";
                    if (!is_null($value->pxid)) {
                        $pentaxprocedimientos = PentaxProc::where('id_pentax', $value->pxid)->get();
                        //dd($pentaxprocedimientos);
                        if (!is_null($pentaxprocedimientos)) {
                            $ban = '0';
                            foreach ($pentaxprocedimientos as $proc) {
                                if ($ban == '0') {
                                    $txtpproc = Procedimiento::find($proc->id_procedimiento)->observacion;
                                    $ban      = '1';
                                } else {
                                    $txtpproc = $txtpproc . ' + ' . Procedimiento::find($proc->id_procedimiento)->observacion;
                                }
                            }
                        }
                    }

                    $sheet->cell('V' . $i, function ($cell) use ($value, $txtcolor, $txtpproc) {
                        // manipulate the cel
                        $cell->setValue($txtpproc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $txtasis = $value->d2apellido1;
                    if ($value->d3apellido1 != null) {
                        $txtasis = $txtasis . ' + ' . $value->d3apellido1;
                    }
                    $sheet->cell('W' . $i, function ($cell) use ($value, $txtcolor, $txtasis, $historiaclinica, $empresa) {
                        // manipulate the cel
                        if (is_null($historiaclinica)) {
                            $cell->setValue($value->senombre);
                            if ($value->consultorio == 1 && $value->senombre == 'IESS') {
                                $cell->setValue($value->senombre . " IESS CONSULTORIO ");
                            }

                        } else {
                            if ($empresa != null) {
                                $cell->setValue($historiaclinica->seguro->nombre . '/' . $empresa->nombre_corto);
                                if ($value->consultorio == 1 && $value->senombre == 'IESS') {
                                    $cell->setValue($historiaclinica->seguro->nombre . '/' . $empresa->nombre_corto . " IESS CONSULTORIO ");
                                }
                            } else {
                                $cell->setValue($historiaclinica->seguro->nombre);
                                if ($value->consultorio == 1 && $value->senombre == 'IESS') {
                                    $cell->setValue($historiaclinica->seguro->nombre . " IESS CONSULTORIO ");
                                }
                            }

                        }
                        //$cell->setValue($value->seguro_pentax);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('X' . $i, function ($cell) use ($value, $txtcolor, $txtasis) {
                        // manipulate the cel
                        $cell->setValue($txtasis);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    if ($value->estado_pentax == '0') {$txtest = 'EN ESPERA';} elseif ($value->estado_pentax == '1') {$txtest = 'PREPARACIÓN';} elseif ($value->estado_pentax == '2') {$txtest = 'EN PROCEDIMIENTO';} elseif ($value->estado_pentax == '3') {$txtest = 'RECUPERACION';} elseif ($value->estado_pentax == '4') {$txtest = 'ALTA';} elseif ($value->estado_pentax == '5') {$txtest = 'SUSPENDER';} else { $txtest = '';}

                    $sheet->cell('Y' . $i, function ($cell) use ($value, $txtcolor, $txtest) {
                        // manipulate the cel
                        $cell->setValue($txtest);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('Z' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ingresa_alt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    //DIRECCIONES Y TELEFONOS
                    $sheet->cell('AA' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->ciudad);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AB' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->direccion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AC' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->telefono1 . '-' . $value->telefono2 . '-' . $value->telefono3);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AD' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $usuario = null;
                        $usuario = User::find($value->id_usuario);
                        if ($usuario != null) {
                            $cell->setValue($usuario->email);
                        } else {
                            $cell->setValue('');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);

                    });
                    $sheet->cell('AE' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->origen);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AF' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        //dd($value->referido);
                        $cell->setValue($value->origen2);
                        $z_txt = '';
                        /*if($value->est_amb_hos=='0'){
                        $z_txt = 'AMBULATORIO';
                        }else{
                        $z_txt = 'HOSPITALIZADO';
                        }*/
                        if ($value->origen == 'REFERIDO') {
                            $cell->setValue($value->referido);
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AG' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $cell->setValue($value->otro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AH' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $z_txt = '';
                        /*if($value->est_amb_hos=='0'){
                        $z_txt = 'AMBULATORIO';
                        }else{
                        $z_txt = 'HOSPITALIZADO';
                        }*/
                        if ($value->est_amb_hos == '0') {
                            $z_txt = 'AMBULATORIO';
                        } else {
                            /*if($value->est_amb_hos == '1'){
                            if($value->omni=='OM'){
                            $z_txt = 'OMNI ';
                            }
                            }else{*/
                            $z_txt = 'HOSPITALIZADO';
                            //}
                        }
                        $cell->setValue($z_txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AI' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $aa_txt = '';
                        /*if($value->omni=='SI'){
                        $aa_txt = 'OMNI';
                        }*/
                        if ($value->omni == 'SI') {
                            $aa_txt = 'OMNI';
                        } elseif ($value->omni == 'OM') {
                            $aa_txt = 'SI';
                        }
                        $cell->setValue($aa_txt);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });
                    $sheet->cell('AJ' . $i, function ($cell) use ($value, $txtcolor) {
                        // manipulate the cel
                        $txt_anes = '';
                        if ($value->id_anestesiologo != null) {
                            $anest    = User::find($value->id_anestesiologo);
                            $txt_anes = $anest->apellido1 . ' ' . $anest->apellido2 . ' ' . $anest->nombre1;
                        }

                        $cell->setValue($txt_anes);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $sheet->cell('AK' . $i, function ($cell) use ($value, $txtcolor, $txt_pro) {
                        // manipulate the cel

                        $cell->setValue($txt_pro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setFontColor($txtcolor);
                    });

                    $i = $i + 1;

                }

            });
        })->export('xlsx');
    }
    public function log_agenda($id)
    {

        /*if($this->rol()){
        return response()->view('errors.404');
        }*/

        $logs = DB::table('log_agenda as l')->where('l.id_agenda', $id)->join('users as u', 'u.id', 'l.id_usuariocrea')->select('l.*', 'u.nombre1', 'u.apellido1')->get();

        return view('consultam/log_agenda', ['logs' => $logs]);
    }

}
