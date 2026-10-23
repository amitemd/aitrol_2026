<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Empresa;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Hc_protocolo_training;
use Sis_medico\hc_receta;
use Sis_medico\hc_receta_detalle;
use Sis_medico\Historiaclinica;
use Sis_medico\Hospital;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Agenda;
use Sis_medico\Hc_Cie10;
use Sis_medico\Log_usuario;
use Sis_medico\Medicina;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Pentax;
use Sis_medico\Pentax_log;
use Sis_medico\Procedimiento;
use Sis_medico\Sala;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;

class HistoriaController extends Controller
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
        if (in_array($rolUsuario, array(1, 3, 4, 5, 6, 11, 7)) == false) {
            return true;
        }
    }

    public function historia($id)
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
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
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
            ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial')
            ->where('agenda.id', '=', $id)
            ->first();

        $agendaprocs = DB::table('agenda_procedimiento')
            ->join('procedimiento', 'procedimiento.id', 'agenda_procedimiento.id_procedimiento')
            ->select('agenda_procedimiento.*', 'procedimiento.nombre')
            ->where('id_agenda', $agenda->id)->get();

        /*23/11/2017 AGREGAR LA HISTORIA CLINICA CANTIDAD DE HISTORIA CLINICA */
        $hcp = DB::select("SELECT h.*, s.nombre as snombre ,e.nombre as especialidad, d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
                            WHERE h.id_paciente = '" . $agenda->id_paciente . "'  AND
                            a.id = h.id_agenda AND
                            s.id = h.id_seguro AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> " . $id . "
                            ORDER BY a.fechaini DESC");

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

        $cantidad_doc = 0;
        $pentaxprocs  = null;
        $pentax_logs  = null;
        if (!is_null($historiaclinica)) {

            $ControlDocController = new ControlDocController;
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

        $fecha_dia = date('Y-m-d', strtotime($agenda->fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $cant_cortesias = Agenda::where('id_doctor1', $agenda->id_doctor1)->where('fechaini', '>', $fecha_dia)->where('fechaini', '<', $nuevafecha)->where('cortesia', 'SI')->count();

        $hcagenda = DB::table('agenda_archivo')->where('id_agenda', '=', $id)->get();

        $sala     = null;
        $hospital = null;
        if (!is_null($agenda->id_sala)) {
            $sala     = Sala::find($agenda->id_sala);
            $hospital = Hospital::find($sala->id_hospital);
        }

        /*23/11/2017 AGREGAR LA HISTORIA CLINICA CANTIDAD DE HISTORIA CLINICA */

        return view('historiaclinica/detalle', ['agenda' => $agenda, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas, 'agendaprocs' => $agendaprocs, 'hcp' => $hcp, 'cant_cortesias' => $cant_cortesias, 'hcagenda' => $hcagenda, 'sala' => $sala, 'hospital' => $hospital, 'historiaclinica' => $historiaclinica, 'pentaxprocs' => $pentaxprocs, 'pentax_logs' => $pentax_logs]);
    }

    /*public function procedimiento($id)
    {
    $rolUsuario = Auth::user()->id_tipo_usuario;
    if($this->rol()){
    return response()->view('errors.404');
    }

    return view('hc_admision/historia/procedimiento');
    $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->get(); //3=DOCTORES;
    $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->get(); //6=ENFERMEROS;
    $salas = DB::table('sala')
    ->join('hospital', 'hospital.id', '=', 'sala.id_hospital')
    ->select('sala.*', 'hospital.nombre_hospital as nombre_hospital')
    ->get();

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
    ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1','paciente.apellido2 as papellido2','paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre','paciente.fecha_nacimiento','paciente.ocupacion','h.parentesco as hparentesco','paciente.parentesco as pparentesco','paciente.estadocivil','paciente.ciudad','paciente.lugar_nacimiento','paciente.direccion','paciente.telefono1','paciente.telefono2','seguros.nombre as snombre','sala.nombre_sala as slnombre','hospital.nombre_hospital as hsnombre','ud.nombre1 as udnombre','ud.apellido1 as udapellido','especialidad.nombre as esnombre','procedimiento.nombre as pnombre','empresa.nombrecomercial')
    ->where('agenda.id', '=', $id)
    ->first();

    $agendaprocs= DB::table('agenda_procedimiento')
    ->join('procedimiento','procedimiento.id','agenda_procedimiento.id_procedimiento')
    ->select('agenda_procedimiento.*','procedimiento.nombre')
    ->where('id_agenda',$agenda->id)->get();

    /*23/11/2017 AGREGAR LA HISTORIA CLINICA CANTIDAD DE HISTORIA CLINICA
    $hcp =  DB::select("SELECT h.*, s.nombre as snombre ,e.nombre as especialidad, d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
    FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
    WHERE h.id_paciente = '".$agenda->id_paciente."'  AND
    a.id = h.id_agenda AND
    s.id = h.id_seguro AND
    a.espid =  e.id AND
    a.id_doctor1 = d.id AND
    h.id_agenda <> ".$id."
    ORDER BY a.fechaini DESC");

    $historiaclinica = DB::table('historiaclinica as hc')
    ->join('users as d1','hc.id_doctor1', 'd1.id')
    ->join('seguros', 'hc.id_seguro', '=', 'seguros.id')
    ->leftjoin('pentax', 'pentax.hcid','hc.hcid')
    ->leftjoin('users as d2','hc.id_doctor2', 'd2.id')
    ->leftjoin('users as d3','hc.id_doctor3', 'd3.id')
    ->leftjoin('users as up', 'up.id', 'hc.id_usuario')
    ->leftjoin('subseguro', 'hc.id_subseguro', '=', 'subseguro.id')
    ->select('hc.*', 'seguros.nombre as snombre','seguros.color as color', 'seguros.tipo as stipo', 'subseguro.nombre as sbnombre', 'pentax.id', 'd1.nombre1 as d1nombre1', 'd1.apellido1 as d1apellido1', 'd2.nombre1 as d2nombre1', 'd2.apellido1 as d2apellido1', 'd3.nombre1 as d3nombre1', 'd3.apellido1 as d3apellido1','pentax.id as id_pentax','up.nombre1 as upnombre1','up.apellido1 as upapellido1' ,'up.nombre2 as upnombre2','up.apellido2 as upapellido2' )
    ->where('hc.id_agenda', '=', $agenda->id)
    ->first();

    $cantidad_doc=0;
    $pentaxprocs=null;
    $pentax_logs=null;
    if(!is_null($historiaclinica)){

    $ControlDocController = new ControlDocController;
    $cantidad_doc = $ControlDocController->carga_documentos_union($historiaclinica->hcid, $agenda->proc_consul, $historiaclinica->stipo)->count();

    if(!is_null($historiaclinica->id_pentax)){
    $pentaxprocs= DB::table('pentax_procedimiento')
    ->join('procedimiento','procedimiento.id','pentax_procedimiento.id_procedimiento')
    ->select('pentax_procedimiento.*','procedimiento.nombre')
    ->where('id_pentax',$historiaclinica->id_pentax)->get();

    $pentax_logs = DB::table('pentax_log')->where('pentax_log.id_pentax',$historiaclinica->id_pentax)->join('users as d1','d1.id','=','pentax_log.id_doctor1')->join('seguros','seguros.id','=','pentax_log.id_seguro')->join('sala','sala.id','=','pentax_log.id_sala')->join('users as um','um.id','=','pentax_log.id_usuariomod')->leftJoin('users as d2','d2.id','=','pentax_log.id_doctor2')->leftJoin('users as d3','d3.id','=','pentax_log.id_doctor3')->select('pentax_log.*','d1.nombre1 as d1nombre1','d1.apellido1 as d1apellido1','d2.nombre1 as d2nombre1','d2.apellido1 as d2apellido1','d3.nombre1 as d3nombre1','d3.apellido1 as d3apellido1','seguros.nombre as snombre','sala.nombre_sala as nbrsala','um.nombre1 as umnombre1','um.apellido1 as umapellido1')->orderBy('pentax_log.created_at')->get();
    //dd($pentaxprocs);
    //dd($pentax_logs);
    }

    }

    $fecha_dia = date('Y-m-d',strtotime($agenda->fechaini));

    $nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha_dia ) ) ;
    $nuevafecha = date ( 'Y-m-d' , $nuevafecha );

    $cant_cortesias =Agenda::where('id_doctor1',$agenda->id_doctor1)->where('fechaini','>',$fecha_dia)->where('fechaini','<',$nuevafecha)->where('cortesia','SI')->count();

    $hcagenda = DB::table('agenda_archivo')->where('id_agenda', '=', $id)->get();

    $sala=null;
    $hospital=null;
    if(!is_null($agenda->id_sala)){
    $sala = Sala::find($agenda->id_sala);
    $hospital = Hospital::find($sala->id_hospital);
    }

    /*23/11/2017 AGREGAR LA HISTORIA CLINICA CANTIDAD DE HISTORIA CLINICA

    return view('hc_admision/historia/detalle', ['agenda' => $agenda, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'salas' => $salas, 'agendaprocs' => $agendaprocs, 'hcp' => $hcp, 'cant_cortesias' => $cant_cortesias, 'hcagenda' => $hcagenda, 'sala' => $sala, 'hospital' => $hospital, 'historiaclinica' => $historiaclinica, 'pentaxprocs' => $pentaxprocs, 'pentax_logs' => $pentax_logs]);
    }*/

    public function index()
    {
    }

    public function drogasadministradas($id_record)
    {
        $drogasadministradas = DB::table('insumo_record as ir')->where('ir.tipo', 'DR')->where('ir.id_record', $id_record)->join('insumo_general as i', 'i.id', 'ir.id_insumo')->select('ir.*', 'i.nombre as dnombre', 'i.presentacion as dpresentacion')->get();

        $gasesadministrados = DB::table('insumo_record as ir')->where('ir.tipo', 'GS')->where('ir.id_record', $id_record)->join('insumo_general as i', 'i.id', 'ir.id_insumo')->select('ir.*', 'i.nombre as dnombre', 'i.presentacion as dpresentacion')->get();

        //dd($gasesadministrados);
        //return $drogasadministradas;

        return view('historiaclinica/drogas_suministradas', ['drogasadministradas' => $drogasadministradas, 'gasesadministrados' => $gasesadministrados]);
    }

    public function create()
    {
    }
    public function store(Request $request)
    {
    }

    public function suspension($url_doctor, Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $historia = Historiaclinica::findOrFail($request['hcid']);
        $agenda   = Agenda::find($historia->id_agenda);
        $procedimiento = $historia->hc_procedimientos;
        if (!is_null($procedimiento)) {
            $procedimiento->update(['id_doctor_examinador' => null]);
        }

        $rules = ['observaciones' => 'required|min:10'];
        $msn   = [
            'observaciones.required' => 'La agenda ya fue admisionada, ingrese el motivo por el cual suspende la cita',
            'observaciones.min'                => 'La agenda ya fue admisionada, ingrese mínimo :min caracteres',
        ];

        $this->validate($request, $rules, $msn);

        $input_historia = [
            'peso'        => '0',
            'pulso'       => '0',
            'altura'      => '0',
            'temperatura' => '0',
            'presion'     => '0',
        ];
        $historia->update($input_historia);

        Log_usuario::create([
            'id_usuario'  => $idusuario,
            'ip_usuario'  => $ip_cliente,
            'descripcion' => "SUSPENDE POST - ADMISION",
            'dato_ant1'   => "PAC: " . $historia->id_paciente,
            'dato1'       => "CIT: " . $historia->id_agenda . " HIS: " . $historia->hcid,
            'dato_ant3'   => "",
            'dato3'       => "",
            'dato_ant4'   => "PRE: " . $historia->presion . " PUL: " . $historia->pulso . " TEM: " . $historia->temperatura . " EST: " . $historia->altura . " PES: " . $historia->peso,
            'dato4'       => "PRE: 0 PUL: 0 TEM: 0 EST: 0 PES: 0",
        ]);

        Log_Agenda::create([
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

            'descripcion'        => 'SUSPENDIO LA CITA POST - ADMISION',
            'descripcion2'       => '',
            'descripcion3'       => '',

            'id_usuarioconfirma' => $agenda->id_usuarioconfirma,

            'id_usuariomod'      => $idusuario,
            'id_usuariocrea'     => $idusuario,
            'ip_modificacion'    => $ip_cliente,
            'ip_creacion'        => $ip_cliente,
        ]);

        $input = [
            'estado_cita'     => '3',
            'observaciones'   => $request['observaciones'],
            'estado'          => '0',
            'id_usuariomod'   => $idusuario,
            'ip_modificacion' => $ip_cliente,
        ];

        $agenda->update($input);

        $s_pentax = Pentax::where('id_agenda', $agenda->id)->first();

        if (!is_null($s_pentax)) {

            //log
            $input_log_p = [
                'id_pentax'       => $s_pentax->id,
                'tipo_cambio'     => "ESTADO",
                'descripcion'     => "SUSPENDIDO",
                'estado_pentax'   => '5',
                'id_seguro'       => $s_pentax->id_seguro,
                'id_subseguro'    => $s_pentax->id_subseguro,
                'procedimientos'  => '',
                'id_doctor1'      => $s_pentax->id_doctor1,
                'id_doctor2'      => $s_pentax->id_doctor2,
                'id_doctor3'      => $s_pentax->id_doctor3,
                'id_sala'         => $s_pentax->id_sala,
                'observacion'     => 'Suspendido desde Recepción: ' . $request['observaciones'],
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
            ];

            Pentax_log::create($input_log_p);

            //suspender
            $input_px = [
                'estado_pentax'   => '5',
                'id_sala'         => $s_pentax->id_sala,
                'id_seguro'       => $s_pentax->id_seguro,
                'id_subseguro'    => $s_pentax->id_subseguro,
                'id_doctor1'      => $s_pentax->id_doctor1,
                'id_doctor2'      => $s_pentax->id_doctor2,
                'id_doctor3'      => $s_pentax->id_doctor3,
                'observacion'     => 'Suspendido desde Recepción: ' . $request['observaciones'],
                'ingresa_prepa'   => $s_pentax->ingresa_prepa,
                'ingresa_proc'    => $s_pentax->ingresa_proc,
                'ingresa_rec'     => $s_pentax->ingresa_rec,
                'ingresa_alt'     => $s_pentax->ingresa_alt,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];

            $s_pentax->update($input_px);
        }

        if ($url_doctor != '0') {
            return redirect()->route('agenda.fecha', ['id' => $url_doctor, 'i' => $request['unix']]);
        } else {
            return redirect()->route('preagenda.pentax', ['fecha' => $request['unix']]);
        }

        //dd($request->all());
    }

    public function show($id)
    {
        //

    }

    /*public function reporte_hc(Request $request) {

    $desde = $request->fecha;
    $hasta = $request->fecha_hasta;

    $fecha_d = Date('Y-m-d');

    $historiaclinica_pro = DB::table('historiaclinica as hc')->join('paciente as p','p.id','hc.id_paciente')->join('hc_procedimientos as hp','hp.id_hc','hc.hcid')->join('agenda as a','a.id','hc.id_agenda')->join('users as d1','d1.id','hc.id_doctor1')->leftjoin('procedimiento_completo as pp','pp.id','hp.id_procedimiento_completo')->leftjoin('grupo_procedimiento as gp','gp.id','pp.id_grupo_procedimiento')->leftjoin('hc_protocolo as pro','pro.id_hc_procedimientos','hp.id')->leftjoin('hc_receta as rta','rta.id_hc','hc.hcid')->leftjoin('users as d2','d2.id','hc.id_doctor2')->leftjoin('users as d3','d3.id','hc.id_doctor3')->join('seguros as seg','seg.id','hc.id_seguro')->leftjoin('pentax as px','px.hcid','hc.hcid')->select('hc.*','p.nombre1','p.nombre2','p.apellido1','p.apellido2', 'p.fecha_nacimiento', 'p.telefono1', 'p.telefono2', 'p.sexo', 'p.observacion','a.est_amb_hos','d1.apellido1 as d1apellido1','d1.nombre1 as d1nombre1','a.proc_consul','gp.nombre as gpnombre','pp.nombre_general as ppnombregeneral','pro.motivo as promotivo','pro.hallazgos as prohallazgo', 'pro.conclusion as proconclusion','rta.id as receta','d3.apellido1 as d3apellido1','d3.nombre1 as d3nombre1','d2.apellido1 as d2apellido1','d2.nombre1 as d2nombre1','seg.nombre as seguro','rta.prescripcion','rta.rp', 'hp.id_doctor_examinador', 'hp.id_seguro as id_seguro_procedimiento','px.estado_pentax','pro.id as protocolo','a.id_empresa')->where('a.estado','!=','0')->where('a.proc_consul','1')->whereBetween('a.fechaini',[$desde.' 00:00',$hasta.' 23:59'])->get();

    $historiaclinica_con = DB::table('historiaclinica as hc')->join('paciente as p','p.id','hc.id_paciente')->join('hc_procedimientos as hp','hp.id_hc','hc.hcid')->join('agenda as a','a.id','hc.id_agenda')->join('users as d1','d1.id','hc.id_doctor1')->leftjoin('procedimiento_completo as pp','pp.id','hp.id_procedimiento_completo')->leftjoin('grupo_procedimiento as gp','gp.id','pp.id_grupo_procedimiento')->leftjoin('hc_protocolo as pro','pro.id_hc_procedimientos','hp.id')->leftjoin('hc_evolucion as evo','evo.hc_id_procedimiento','hp.id')->leftjoin('hc_child_pugh as chi','chi.id_hc_evolucion','evo.id')->leftjoin('hc_receta as rta','rta.id_hc','hc.hcid')->leftjoin('users as d2','d2.id','hc.id_doctor2')->leftjoin('users as d3','d3.id','hc.id_doctor3')->join('seguros as seg','seg.id','hc.id_seguro')->leftjoin('pentax as px','px.hcid','hc.hcid')->select('hc.*','p.nombre1','p.nombre2','p.apellido1','p.apellido2', 'p.fecha_nacimiento', 'p.telefono1', 'p.telefono2', 'p.sexo', 'p.observacion','a.est_amb_hos','d1.apellido1 as d1apellido1','d1.nombre1 as d1nombre1','a.proc_consul','gp.nombre as gpnombre','pp.nombre_general as ppnombregeneral','pro.motivo as promotivo','pro.hallazgos as prohallazgo', 'evo.motivo as evomotivo','chi.examen_fisico','pro.conclusion as proconclusion','rta.id as receta','d3.apellido1 as d3apellido1','d3.nombre1 as d3nombre1','d2.apellido1 as d2apellido1','d2.nombre1 as d2nombre1','seg.nombre as seguro','rta.prescripcion','rta.rp', 'hp.id_doctor_examinador', 'hp.id_seguro as id_seguro_procedimiento','px.estado_pentax','a.id_empresa')->where('a.estado','!=','0')->where('a.proc_consul','<>','1')->whereBetween('a.fechaini',[$desde.' 00:00',$hasta.' 23:59'])->get();

    Excel::create('Historiaclinica-'.$fecha_d, function($excel) use($historiaclinica_pro, $historiaclinica_con, $fecha_d) {

    $excel->sheet('Historiaclinica', function($sheet) use($historiaclinica_pro, $historiaclinica_con, $fecha_d) {

    $sheet->cell('A1', function($cell) {
    // manipulate the cel
    $cell->setValue('CEDULA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('B1', function($cell) {
    // manipulate the cel
    $cell->setValue('APELLIDOS');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('C1', function($cell) {
    // manipulate the cel
    $cell->setValue('NOMBRES');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');;
    });
    $sheet->cell('D1', function($cell) {
    // manipulate the cel
    $cell->setValue('FECHA NACIMIENTO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('E1', function($cell) {
    // manipulate the cel

    $cell->setValue('SEXO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('F1', function($cell) {
    // manipulate the cel
    $cell->setValue('TELEFONOS');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('G1', function($cell) {
    // manipulate the cel
    $cell->setValue('OBSERVACION SI/NO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('H1', function($cell) {
    // manipulate the cel
    $cell->setValue('OBSERVACION');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('I1', function($cell) {
    // manipulate the cel
    $cell->setValue('ALERGIA SI/NO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('J1', function($cell) {
    // manipulate the cel
    $cell->setValue('ALERGIA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('K1', function($cell) {
    // manipulate the cel
    $cell->setValue('FECHA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('L1', function($cell) {
    // manipulate the cel
    $cell->setValue('HOSPITALIZADO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('M1', function($cell) {
    // manipulate the cel
    $cell->setValue('MEDICO SOLICITA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('N1', function($cell) {
    // manipulate the cel
    $cell->setValue('MEDICO EXAMINA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('O1', function($cell) {
    // manipulate the cel
    $cell->setValue('TIPO CONSULTA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('P1', function($cell) {
    // manipulate the cel
    $cell->setValue('PROCEDIMIENTOS');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('Q1', function($cell) {
    // manipulate the cel
    $cell->setValue('MOTIVO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('R1', function($cell) {
    // manipulate the cel
    $cell->setValue('HALLAZGO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('S1', function($cell) {
    // manipulate the cel
    $cell->setValue('DIAGNOSTICO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });

    //DIRECCIONES Y TELEFONOS
    $sheet->cell('T1', function($cell) {
    // manipulate the cel
    $cell->setValue('PRESION');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('U1', function($cell) {
    // manipulate the cel
    $cell->setValue('TEMPERATURA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('V1', function($cell) {
    // manipulate the cel
    $cell->setValue('PESO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('W1', function($cell) {
    // manipulate the cel
    $cell->setValue('ESTATURA');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('X1', function($cell) {
    // manipulate the cel
    $cell->setValue('IMC');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('Y1', function($cell) {
    // manipulate the cel
    $cell->setValue('IMC VAL');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('Z1', function($cell) {
    // manipulate the cel
    $cell->setValue('GCT');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('AA1', function($cell) {
    // manipulate the cel
    $cell->setValue('PESO IDEAL');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('AB1', function($cell) {
    // manipulate the cel
    $cell->setValue('PERIMETRO ABDOMINAL');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('AC1', function($cell) {
    // manipulate the cel
    $cell->setValue('RP');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('AD1', function($cell) {
    // manipulate the cel
    $cell->setValue('PR');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('AE1', function($cell) {
    // manipulate the cel
    $cell->setValue('ASISTENTE 1');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('AF1', function($cell) {
    // manipulate the cel
    $cell->setValue('ASISTENTE 2');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('AG1', function($cell) {
    // manipulate the cel
    $cell->setValue('CONVENIO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('AH1', function($cell) {
    // manipulate the cel
    $cell->setValue('ESTADO');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });
    $sheet->cell('AI1', function($cell) {
    // manipulate the cel
    $cell->setValue('DR.TRAINING');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    $cell->setFontWeight('bold');
    });

    $i=2;
    foreach($historiaclinica_pro as $value){

    $sheet->cell('A'.$i, function($cell) use($value){
    // manipulate the cel
    $cell->setValue($value->id_paciente);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('B'.$i, function($cell) use($value) {
    // manipulate the cel
    if($value->apellido2 != "(N/A)"){
    $vapellido= $value->apellido1.' '.$value->apellido2;
    }
    else{
    $vapellido= $value->apellido1;
    }

    $cell->setValue($vapellido);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('C'.$i, function($cell) use($value) {
    // manipulate the cel

    if($value->nombre2 != "(N/A)"){
    $vnombre= $value->nombre1.' '.$value->nombre2;
    }
    else
    {
    $vnombre= $value->nombre1;
    }

    $cell->setValue($vnombre);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('D'.$i, function($cell) use($value) {

    $cell->setValue($value->fecha_nacimiento);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('E'.$i, function($cell) use($value) {
    // manipulate the cel
    if($value->sexo=='2'){
    $cell->setValue('FEMENINO');

    }else{
    $cell->setValue('MASCULINO');
    }

    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('F'.$i, function($cell) use($value) {
    // manipulate the cel
    $cell->setValue($value->telefono1.'/'.$value->telefono2);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('G'.$i, function($cell) use($value) {
    // manipulate the cel
    if($value->observacion!=null){
    $cell->setValue('SI');
    }else{
    $cell->setValue('NO');
    }

    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('H'.$i, function($cell) use($value) {
    // manipulate the cel

    $cell->setValue($value->observacion);

    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $alergiasxpac=null;
    $txt_ale = '';
    $alergiasxpac = Paciente_Alergia::where('id_paciente',$value->id_paciente)->get();
    if(!is_null($alergiasxpac)){

    foreach($alergiasxpac as $ale_pac){
    $txt_ale = $txt_ale.' '.$ale_pac->principio_activo->nombre;
    }

    }

    $sheet->cell('I'.$i, function($cell) use($value, $txt_ale) {
    // manipulate the cel

    if($txt_ale!=''){
    $cell->setValue('SI');
    }else{
    $cell->setValue('NO');
    }
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('J'.$i, function($cell) use($value, $txt_ale) {
    // manipulate the cel

    $cell->setValue($txt_ale);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('K'.$i, function($cell) use($value) {
    // manipulate the cel

    $cell->setValue(substr($value->created_at,0,10));
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('L'.$i, function($cell) use($value) {
    // manipulate the cel
    if($value->est_amb_hos=='1'){
    $cell->setValue('SI');
    }else{
    $cell->setValue('NO');
    }

    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('M'.$i, function($cell) use($value) {
    // manipulate the cel

    $cell->setValue(' ');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('N'.$i, function($cell) use($value) {
    // manipulate the cel
    if($value->id_doctor_examinador == null){
    $cell->setValue($value->d1apellido1.' '.$value->d1nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }else{
    $doctor = User::find($value->id_doctor_examinador);
    $cell->setValue($doctor->apellido1.' '.$doctor->nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }

    });

    $sheet->cell('O'.$i, function($cell) use($value) {
    // manipulate the cel
    $tipo = 'PROCEDIMIENTO';
    if($value->proc_consul=='1'){
    $tipo = $value->gpnombre;
    }else{
    $tipo = 'CONSULTA';
    }
    if($tipo==''){
    $tipo = 'PROCEDIMIENTO';
    }

    $cell->setValue($tipo);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('P'.$i, function($cell) use($value) {
    // manipulate the cel
    $pnombre = 'NO INGRESADO';
    if($value->proc_consul=='1'){
    $pnombre = $value->ppnombregeneral;
    }else{
    $pnombre = 'CONSULTA';
    }
    if($pnombre==''){
    $pnombre = 'NO INGRESADO';
    }
    $cell->setValue($pnombre);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('Q'.$i, function($cell) use($value) {
    // manipulate the cel
    $motivo = '';
    if($value->proc_consul=='1'){
    $motivo = $value->promotivo;
    }else{
    //$motivo = $value->evomotivo;
    }
    $cell->setValue($motivo);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('R'.$i, function($cell) use($value) {
    // manipulate the cel
    $hallazgo = '';
    if($value->proc_consul=='1'){
    $hallazgo = $value->prohallazgo;
    $hallazgo = html_entity_decode(preg_replace('/&nbsp;/',' ',strip_tags($hallazgo)),ENT_QUOTES, "UTF-8");
    }else{
    //$hallazgo = $value->examen_fisico;
    }
    $cell->setValue($hallazgo);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('S'.$i, function($cell) use($value) {
    // manipulate the cel
    $conclusion = '';
    if($value->proc_consul=='1'){
    $conclusion = $value->proconclusion;
    $conclusion = html_entity_decode(preg_replace('/&nbsp;/',' ',strip_tags($conclusion)),ENT_QUOTES, "UTF-8");
    }else{
    $conclusion = '';
    }
    $cell->setValue($conclusion);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('T'.$i, function($cell) use($value) {

    $cell->setValue($value->presion);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('U'.$i, function($cell) use($value) {

    $cell->setValue($value->temperatura);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('V'.$i, function($cell) use($value) {

    $cell->setValue($value->peso);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('W'.$i, function($cell) use($value) {

    $cell->setValue($value->altura);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('X'.$i, function($cell) use($value) {
    $peso =  $value->peso;
    $estatura = $value->altura;

    if($value->sexo == 1){
    $sexo = $value->sexo;
    }else{
    $sexo = 0;
    }
    $edad = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
    $estatura2 = pow(($estatura/100), 2);

    $peso_ideal = 21.45 * ($estatura2);
    if($estatura2 == 0){
    $imc = 0;
    }else{
    $imc = $peso/$estatura2;
    }
    $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
    $texto = "";
    if($imc < 16){
    $texto = "Desnutrición";
    }
    else if($imc < 18){
    $texto = "Bajo de Peso";
    }
    else if($imc < 25){
    $texto = "Normal";
    }
    else if($imc < 27){
    $texto = "Sobrepeso";
    }
    else if($imc < 30){
    $texto = "Obesidad Tipo 1";
    }
    else if($imc < 40){
    $texto = "Obesidad Clinica";
    }
    else{
    $texto = "Obesidad Mordida";
    }
    $cell->setValue($texto);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('Y'.$i, function($cell) use($value) {
    $peso =  $value->peso;
    $estatura = $value->altura;

    if($value->sexo == 1){
    $sexo = $value->sexo;
    }else{
    $sexo = 0;
    }
    $edad = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
    $estatura2 = pow(($estatura/100), 2);

    $peso_ideal = 21.45 * ($estatura2);
    if($estatura2 == 0){
    $imc = 0;
    }else{
    $imc = $peso/$estatura2;
    }
    $cell->setValue($imc);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('Z'.$i, function($cell) use($value) {
    $peso =  $value->peso;
    $estatura = $value->altura;

    if($value->sexo == 1){
    $sexo = $value->sexo;
    }else{
    $sexo = 0;
    }
    $edad = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
    $estatura2 = pow(($estatura/100), 2);

    $peso_ideal = 21.45 * ($estatura2);
    if($estatura2 == 0){
    $imc = 0;
    }else{
    $imc = $peso/$estatura2;
    }

    if($imc == 0){
    $gct = 0;
    }else{
    $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
    }
    $cell->setValue($gct);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('AA'.$i, function($cell) use($value) {
    $peso =  $value->peso;
    $estatura = $value->altura;

    if($value->sexo == 1){
    $sexo = $value->sexo;
    }else{
    $sexo = 0;
    }
    $edad = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
    $estatura2 = pow(($estatura/100), 2);

    $peso_ideal = 21.45 * ($estatura2);
    if($estatura2 == 0){
    $imc = 0;
    }else{
    $imc = $peso/$estatura2;
    }
    $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
    $cell->setValue($peso_ideal);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('AB'.$i, function($cell) use($value) {

    $cell->setValue($value->perimetro);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $receta = '';
    $receta2 = '';

    if($value->receta!=null){

    $detalles = hc_receta_detalle::where('id_hc_receta',$value->receta)->get();

    foreach($detalles as $value2){

    $genericos = Medicina::where('id',$value2->id_medicina)->first()->genericos;

    $receta = $receta.' - '.$value2->medicina->nombre.' (';
    $receta2 = $receta2.' - '.$value2->medicina->nombre.': '.$value2->dosis;
    foreach($genericos as $gen){

    $receta = $receta.$gen->generico->nombre.' ';
    }
    $receta = $receta.' ) '.$value2->cantidad;

    }

    }

    $sheet->cell('AC'.$i, function($cell) use($value, $receta) {
    $rece_1 = html_entity_decode(preg_replace('/&nbsp;/',' ',strip_tags($value->rp)),ENT_QUOTES, "UTF-8");
    $cell->setValue($receta.' '.$rece_1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('AD'.$i, function($cell) use($value, $receta2) {
    $rece_2 = html_entity_decode(preg_replace('/&nbsp;/',' ',strip_tags($value->prescripcion)),ENT_QUOTES, "UTF-8");
    $cell->setValue($receta2.' '.$rece_2);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('AE'.$i, function($cell) use($value) {

    $cell->setValue($value->d2apellido1.' '.$value->d2nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('AF'.$i, function($cell) use($value) {

    $cell->setValue($value->d3apellido1.' '.$value->d3nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    //CONVENIO
    $sheet->cell('AG'.$i, function($cell) use($value) {
    $empresa = Empresa::find($value->id_empresa);
    $tempresa = '';
    if(!is_null($empresa)){
    $tempresa = '/'.$empresa->nombre_corto;
    }
    if($value->id_seguro_procedimiento == null){
    $cell->setValue($value->seguro.$tempresa);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }else{
    $seguro_hc_procedimiento = Seguro::find($value->id_seguro_procedimiento);
    $cell->setValue($seguro_hc_procedimiento->nombre.$tempresa);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }

    });

    $sheet->cell('AH'.$i, function($cell) use($value) {

    $estado = '';
    if($value->estado_pentax=='0'){
    $estado = 'EN ESPERA';
    }elseif ($value->estado_pentax=='1') {
    $estado = 'PREPARACION';
    }elseif ($value->estado_pentax=='2') {
    $estado = 'EN PROCEDIMIENTO';
    }elseif ($value->estado_pentax=='3') {
    $estado = 'RECUPERACION';
    }elseif ($value->estado_pentax=='4') {
    $estado = 'ALTA';
    }elseif ($value->estado_pentax=='5') {
    $estado = 'SUSPENDIDO';
    }

    $cell->setValue($estado);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $tr_arr = ['I','J','K','L','M','N','O','P','Q','R','S','T','U'];
    if($value->protocolo!=null){
    $trainin_pro = Hc_protocolo_training::where('id_hc_protocolo',$value->protocolo)->get();
    $tr_i = 0;
    foreach($trainin_pro as $tr){
    $sheet->cell('A'.$tr_arr[$tr_i].$i, function($cell) use($tr,$tr_i) {

    $cell->setValue($tr->doctor->apellido1.' '.$tr->doctor->nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $tr_i ++;
    }
    }

    $i= $i+1;

    }

    foreach($historiaclinica_con as $value){

    $sheet->cell('A'.$i, function($cell) use($value){
    // manipulate the cel
    $cell->setValue($value->id_paciente);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('B'.$i, function($cell) use($value) {
    // manipulate the cel
    if($value->apellido2 != "(N/A)"){
    $vapellido= $value->apellido1.' '.$value->apellido2;
    }
    else{
    $vapellido= $value->apellido1;
    }

    $cell->setValue($vapellido);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('C'.$i, function($cell) use($value) {
    // manipulate the cel

    if($value->nombre2 != "(N/A)"){
    $vnombre= $value->nombre1.' '.$value->nombre2;
    }
    else
    {
    $vnombre= $value->nombre1;
    }

    $cell->setValue($vnombre);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('D'.$i, function($cell) use($value) {

    $cell->setValue($value->fecha_nacimiento);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('E'.$i, function($cell) use($value) {
    // manipulate the cel
    if($value->sexo=='2'){
    $cell->setValue('FEMENINO');

    }else{
    $cell->setValue('MASCULINO');
    }

    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('F'.$i, function($cell) use($value) {
    // manipulate the cel
    $cell->setValue($value->telefono1.'/'.$value->telefono2);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('G'.$i, function($cell) use($value) {
    // manipulate the cel
    if($value->observacion!=null){
    $cell->setValue('SI');
    }else{
    $cell->setValue('NO');
    }

    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('H'.$i, function($cell) use($value) {
    // manipulate the cel

    $cell->setValue($value->observacion);

    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $alergiasxpac=null;
    $txt_ale = '';
    $alergiasxpac = Paciente_Alergia::where('id_paciente',$value->id_paciente)->get();
    if(!is_null($alergiasxpac)){

    foreach($alergiasxpac as $ale_pac){
    $txt_ale = $txt_ale.' '.$ale_pac->principio_activo->nombre;
    }

    }

    $sheet->cell('I'.$i, function($cell) use($value, $txt_ale) {
    // manipulate the cel

    if($txt_ale!=''){
    $cell->setValue('SI');
    }else{
    $cell->setValue('NO');
    }
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('J'.$i, function($cell) use($value, $txt_ale) {
    // manipulate the cel

    $cell->setValue($txt_ale);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('K'.$i, function($cell) use($value) {
    // manipulate the cel

    $cell->setValue(substr($value->created_at,0,10));
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('L'.$i, function($cell) use($value) {
    // manipulate the cel
    if($value->est_amb_hos=='1'){
    $cell->setValue('SI');
    }else{
    $cell->setValue('NO');
    }

    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('M'.$i, function($cell) use($value) {
    // manipulate the cel

    $cell->setValue(' ');
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('N'.$i, function($cell) use($value) {
    // manipulate the cel
    if($value->id_doctor_examinador == null){
    $cell->setValue($value->d1apellido1.' '.$value->d1nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }else{
    $doctor = User::find($value->id_doctor_examinador);
    $cell->setValue($doctor->apellido1.' '.$doctor->nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }

    });

    $sheet->cell('O'.$i, function($cell) use($value) {
    // manipulate the cel
    $tipo = 'PROCEDIMIENTO';
    if($value->proc_consul=='1'){
    $tipo = $value->gpnombre;
    }else{
    $tipo = 'CONSULTA';
    }
    if($tipo==''){
    $tipo = 'PROCEDIMIENTO';
    }

    $cell->setValue($tipo);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('P'.$i, function($cell) use($value) {
    // manipulate the cel
    $pnombre = 'NO INGRESADO';
    if($value->proc_consul=='1'){
    $pnombre = $value->ppnombregeneral;
    }else{
    $pnombre = 'CONSULTA';
    }
    if($pnombre==''){
    $pnombre = 'NO INGRESADO';
    }
    $cell->setValue($pnombre);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('Q'.$i, function($cell) use($value) {
    // manipulate the cel
    $motivo = '';
    if($value->proc_consul=='1'){
    $motivo = $value->promotivo;
    }else{
    $motivo = $value->evomotivo;
    }
    $cell->setValue($motivo);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('R'.$i, function($cell) use($value) {
    // manipulate the cel
    $hallazgo = '';
    if($value->proc_consul=='1'){
    $hallazgo = $value->prohallazgo;
    $hallazgo = html_entity_decode(preg_replace('/&nbsp;/',' ',strip_tags($hallazgo)),ENT_QUOTES, "UTF-8");
    }else{
    $hallazgo = $value->examen_fisico;
    }
    $cell->setValue($hallazgo);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('S'.$i, function($cell) use($value) {
    // manipulate the cel
    $conclusion = '';
    if($value->proc_consul=='1'){
    $conclusion = $value->proconclusion;
    $conclusion = html_entity_decode(preg_replace('/&nbsp;/',' ',strip_tags($conclusion)),ENT_QUOTES, "UTF-8");
    }else{
    $conclusion = '';
    }
    $cell->setValue($conclusion);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('T'.$i, function($cell) use($value) {

    $cell->setValue($value->presion);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('U'.$i, function($cell) use($value) {

    $cell->setValue($value->temperatura);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('V'.$i, function($cell) use($value) {

    $cell->setValue($value->peso);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('W'.$i, function($cell) use($value) {

    $cell->setValue($value->altura);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('X'.$i, function($cell) use($value) {
    $peso =  $value->peso;
    $estatura = $value->altura;

    if($value->sexo == 1){
    $sexo = $value->sexo;
    }else{
    $sexo = 0;
    }
    $edad = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
    $estatura2 = pow(($estatura/100), 2);

    $peso_ideal = 21.45 * ($estatura2);
    if($estatura2 == 0){
    $imc = 0;
    }else{
    $imc = $peso/$estatura2;
    }
    $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
    $texto = "";
    if($imc < 16){
    $texto = "Desnutrición";
    }
    else if($imc < 18){
    $texto = "Bajo de Peso";
    }
    else if($imc < 25){
    $texto = "Normal";
    }
    else if($imc < 27){
    $texto = "Sobrepeso";
    }
    else if($imc < 30){
    $texto = "Obesidad Tipo 1";
    }
    else if($imc < 40){
    $texto = "Obesidad Clinica";
    }
    else{
    $texto = "Obesidad Mordida";
    }
    $cell->setValue($texto);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('Y'.$i, function($cell) use($value) {
    $peso =  $value->peso;
    $estatura = $value->altura;

    if($value->sexo == 1){
    $sexo = $value->sexo;
    }else{
    $sexo = 0;
    }
    $edad = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
    $estatura2 = pow(($estatura/100), 2);

    $peso_ideal = 21.45 * ($estatura2);
    if($estatura2 == 0){
    $imc = 0;
    }else{
    $imc = $peso/$estatura2;
    }
    $cell->setValue($imc);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('Z'.$i, function($cell) use($value) {
    $peso =  $value->peso;
    $estatura = $value->altura;

    if($value->sexo == 1){
    $sexo = $value->sexo;
    }else{
    $sexo = 0;
    }
    $edad = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
    $estatura2 = pow(($estatura/100), 2);

    $peso_ideal = 21.45 * ($estatura2);
    if($estatura2 == 0){
    $imc = 0;
    }else{
    $imc = $peso/$estatura2;
    }

    if($imc == 0){
    $gct = 0;
    }else{
    $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
    }
    $cell->setValue($gct);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('AA'.$i, function($cell) use($value) {
    $peso =  $value->peso;
    $estatura = $value->altura;

    if($value->sexo == 1){
    $sexo = $value->sexo;
    }else{
    $sexo = 0;
    }
    $edad = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
    $estatura2 = pow(($estatura/100), 2);

    $peso_ideal = 21.45 * ($estatura2);
    if($estatura2 == 0){
    $imc = 0;
    }else{
    $imc = $peso/$estatura2;
    }
    $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
    $cell->setValue($peso_ideal);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    $sheet->cell('AB'.$i, function($cell) use($value) {

    $cell->setValue($value->perimetro);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $receta = '';
    $receta2 = '';

    if($value->receta!=null){

    $detalles = hc_receta_detalle::where('id_hc_receta',$value->receta)->get();

    foreach($detalles as $value2){

    $genericos = Medicina::where('id',$value2->id_medicina)->first()->genericos;

    $receta = $receta.' - '.$value2->medicina->nombre.' (';
    $receta2 = $receta2.' - '.$value2->medicina->nombre.': '.$value2->dosis;
    foreach($genericos as $gen){

    $receta = $receta.$gen->generico->nombre.' ';
    }
    $receta = $receta.' ) '.$value2->cantidad;

    }

    }

    $sheet->cell('AC'.$i, function($cell) use($value, $receta) {
    $rece_1 = html_entity_decode(preg_replace('/&nbsp;/',' ',strip_tags($value->rp)),ENT_QUOTES, "UTF-8");
    $cell->setValue($receta.' '.$rece_1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('AD'.$i, function($cell) use($value, $receta2) {
    $rece_2 = html_entity_decode(preg_replace('/&nbsp;/',' ',strip_tags($value->prescripcion)),ENT_QUOTES, "UTF-8");
    $cell->setValue($receta2.' '.$rece_2);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('AE'.$i, function($cell) use($value) {

    $cell->setValue($value->d2apellido1.' '.$value->d2nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $sheet->cell('AF'.$i, function($cell) use($value) {

    $cell->setValue($value->d3apellido1.' '.$value->d3nombre1);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });
    //convenio
    $sheet->cell('AG'.$i, function($cell) use($value) {
    $empresa = Empresa::find($value->id_empresa);
    $tempresa = '';
    if(!is_null($empresa)){
    $tempresa = '/'.$empresa->nombre_corto;
    }
    if($value->id_seguro_procedimiento == null){
    $cell->setValue($value->seguro.$tempresa);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }else{
    $seguro_hc_procedimiento = Seguro::find($value->id_seguro_procedimiento);
    $cell->setValue($seguro_hc_procedimiento->nombre.$tempresa);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');
    }

    });

    $sheet->cell('AH'.$i, function($cell) use($value) {

    $estado = '';
    if($value->estado_pentax=='0'){
    $estado = 'EN ESPERA';
    }elseif ($value->estado_pentax=='1') {
    $estado = 'PREPARACION';
    }elseif ($value->estado_pentax=='2') {
    $estado = 'EN PROCEDIMIENTO';
    }elseif ($value->estado_pentax=='3') {
    $estado = 'RECUPERACION';
    }elseif ($value->estado_pentax=='4') {
    $estado = 'ALTA';
    }elseif ($value->estado_pentax=='5') {
    $estado = 'SUSPENDIDO';
    }

    $cell->setValue($estado);
    $cell->setBorder('thin', 'thin', 'thin', 'thin');

    });

    $i= $i+1;

    }

    });

    $excel->getActiveSheet()->getColumnDimension("R")->setWidth(15)->setAutosize(false);
    $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(15)->setAutosize(false);
    $excel->getActiveSheet()->getColumnDimension("S")->setWidth(15)->setAutosize(false);
    $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(15)->setAutosize(false);
    $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(15)->setAutosize(false);

    })->export('xlsx');
    }*/

    public function reporte_hc(Request $request)
    {

        $nombres_sql = '';
        $nombres     = $request['nombres'];
        $apellidos   = $request['apellidos'];
        $proc_consul = $request['proc_consul'];
        $id_doctor1  = $request['id_doctor1'];
        $id_seguro   = $request['id_seguro'];
        $espid       = $request['espid'];
        $cedula      = $request['cedula'];
        $agendas_proc = null;

        if ($proc_consul == null) {
            $proc_consul = '2';
        }

        $desde = $request->fecha;
        $hasta = $request->fecha_hasta;

        if ($desde == null) {
            $desde = Date('Y-m-d');
        }

        if ($hasta == null) {
            $hasta = Date('Y-m-d');
        }

        $fecha_d = Date('Y-m-d');

        $historiaclinica_pro = DB::table('historiaclinica as hc')->join('paciente as p', 'p.id', 'hc.id_paciente')->join('hc_procedimientos as hp', 'hp.id_hc', 'hc.hcid')->join('agenda as a', 'a.id', 'hc.id_agenda')->join('users as d1', 'd1.id', 'hc.id_doctor1')->leftjoin('procedimiento_completo as pp', 'pp.id', 'hp.id_procedimiento_completo')->leftjoin('grupo_procedimiento as gp', 'gp.id', 'pp.id_grupo_procedimiento')->leftjoin('hc_protocolo as pro', 'pro.id_hc_procedimientos', 'hp.id')->leftjoin('hc_receta as rta', 'rta.id_hc', 'hc.hcid')->leftjoin('users as d2', 'd2.id', 'hc.id_doctor2')->leftjoin('users as d3', 'd3.id', 'hc.id_doctor3')->join('seguros as seg', 'seg.id', 'hc.id_seguro')->leftjoin('pentax as px', 'px.hcid', 'hc.hcid')->select('hc.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'p.telefono1', 'p.telefono2', 'p.sexo', 'p.observacion', 'a.est_amb_hos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'a.proc_consul', 'gp.nombre as gpnombre', 'pp.nombre_general as ppnombregeneral', 'pro.motivo as promotivo', 'pro.hallazgos as prohallazgo', 'pro.conclusion as proconclusion', 'rta.id as receta', 'd3.apellido1 as d3apellido1', 'd3.nombre1 as d3nombre1', 'd2.apellido1 as d2apellido1', 'd2.nombre1 as d2nombre1', 'seg.nombre as seguro', 'rta.prescripcion', 'rta.rp', 'hp.id_doctor_examinador', 'hp.id_seguro as id_seguro_procedimiento', 'px.estado_pentax', 'pro.id as protocolo', 'a.id_empresa')->where('a.estado', '!=', '0')->where('a.proc_consul', '1')->whereBetween('a.fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])->get();

        $historiaclinica_con = DB::table('historiaclinica as hc')->join('paciente as p', 'p.id', 'hc.id_paciente')->join('hc_procedimientos as hp', 'hp.id_hc', 'hc.hcid')->join('agenda as a', 'a.id', 'hc.id_agenda')->join('users as d1', 'd1.id', 'hc.id_doctor1')->leftjoin('procedimiento_completo as pp', 'pp.id', 'hp.id_procedimiento_completo')->leftjoin('grupo_procedimiento as gp', 'gp.id', 'pp.id_grupo_procedimiento')->leftjoin('hc_protocolo as pro', 'pro.id_hc_procedimientos', 'hp.id')->leftjoin('hc_evolucion as evo', 'evo.hc_id_procedimiento', 'hp.id')->leftjoin('hc_child_pugh as chi', 'chi.id_hc_evolucion', 'evo.id')->leftjoin('hc_receta as rta', 'rta.id_hc', 'hc.hcid')->leftjoin('users as d2', 'd2.id', 'hc.id_doctor2')->leftjoin('users as d3', 'd3.id', 'hc.id_doctor3')->join('seguros as seg', 'seg.id', 'hc.id_seguro')->leftjoin('pentax as px', 'px.hcid', 'hc.hcid')->select('hc.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'p.telefono1', 'p.telefono2', 'p.sexo', 'p.observacion', 'a.est_amb_hos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'a.proc_consul', 'gp.nombre as gpnombre', 'pp.nombre_general as ppnombregeneral', 'pro.motivo as promotivo', 'pro.hallazgos as prohallazgo', 'evo.motivo as evomotivo', 'chi.examen_fisico', 'pro.conclusion as proconclusion', 'rta.id as receta', 'd3.apellido1 as d3apellido1', 'd3.nombre1 as d3nombre1', 'd2.apellido1 as d2apellido1', 'd2.nombre1 as d2nombre1', 'seg.nombre as seguro', 'rta.prescripcion', 'rta.rp', 'hp.id_doctor_examinador', 'hp.id_seguro as id_seguro_procedimiento', 'px.estado_pentax', 'a.id_empresa')->where('a.estado', '!=', '0')->where('a.proc_consul', '<>', '1')->whereBetween('a.fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])->get();

        $flag_nombres = 0;
        //BUSCA POR NOMBRE Y APELLIDO DEL PACIENTE
        if ($nombres != null || $apellidos != null) {

            $flag_nombres = 1;
            $pacientes    = DB::table('paciente as p')
                ->leftjoin('historiaclinica as h', 'h.id_paciente', 'p.id')
                ->leftjoin('agenda as a', 'h.id_agenda', 'a.id')
                ->groupBy('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'p.sexo', 'p.telefono1', 'p.telefono2', 'p.observacion')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.estado_cita', 'p.sexo', 'p.telefono1', 'p.telefono2', 'p.observacion');

            $nombres2 = explode(" ", $nombres);
            $cantidad = count($nombres2);

            foreach ($nombres2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }

            $apellidos2 = explode(" ", $apellidos);
            $cantidad   = $cantidad + count($nombres2);

            foreach ($apellidos2 as $n) {
                $nombres_sql = $nombres_sql . '%' . $n;
            }

            $nombres_sql = $nombres_sql . '%';

            if ($cantidad > '1') {
                $pacientes = $pacientes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1) LIKE ?', [$nombres_sql]);
                });
            } else {
                $pacientes = $pacientes->where(function ($jq1) use ($nombres_sql) {
                    $jq1->orwhereraw('CONCAT(p.apellido1," ",p.apellido2," ",p.nombre1," ",p.nombre2) LIKE ?', [$nombres_sql])
                        ->orwhereraw('CONCAT(p.nombre1," ",p.nombre2," ",p.apellido1," ",p.apellido2) LIKE ?', [$nombres_sql]);
                });
            }

            $pacientes = $pacientes->limit('100')->get();
        } else {
            //BUSCA POR FECHAS

            $pacientes1 = DB::table('agenda as a')->where('a.estado', 1)
                ->whereBetween('fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('seguros as se', 'se.id', 'a.id_seguro')
                ->leftjoin('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->leftjoin('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->whereNull('h.hcid')->where('a.espid', '<>', '10')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'a.id_doctor1 as doctor', 'a.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'p.sexo', 'p.telefono1', 'p.telefono2', 'p.observacion', 'a.est_amb_hos', 'h.presion', 'h.temperatura', 'h.peso', 'h.altura', 'h.perimetro', 'h.id_doctor2', 'h.id_doctor3', 'a.omni');

            $pacientes2 = DB::table('agenda as a')->where('a.estado', 1)
                ->whereBetween('fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('seguros as se', 'se.id', 'a.id_seguro')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->where('a.proc_consul', '0')->where('a.espid', '<>', '10')
                ->whereNull('hc_pro.id_doctor_examinador')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'p.sexo', 'p.telefono1', 'p.telefono2', 'p.observacion', 'a.est_amb_hos', 'h.presion', 'h.temperatura', 'h.peso', 'h.altura', 'h.perimetro', 'h.id_doctor2', 'h.id_doctor3', 'a.omni');

            $pacientes2_0 = DB::table('agenda as a')->where('a.estado', 1)
                ->whereBetween('fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('seguros as se', 'se.id', 'a.id_seguro')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->where('a.proc_consul', '0')->where('a.espid', '<>', '10')
                ->whereNotNull('hc_pro.id_doctor_examinador')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'p.sexo', 'p.telefono1', 'p.telefono2', 'p.observacion', 'a.est_amb_hos', 'h.presion', 'h.temperatura', 'h.peso', 'h.altura', 'h.perimetro', 'h.id_doctor2', 'h.id_doctor3', 'a.omni');

            $pacientes3 = DB::table('agenda as a')->where('a.estado', 1)
                ->whereBetween('fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('seguros as se', 'se.id', 'a.id_seguro')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->where('a.proc_consul', '1')->where('a.espid', '<>', '10')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'h.id_doctor1 as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'p.sexo', 'p.telefono1', 'p.telefono2', 'p.observacion', 'a.est_amb_hos', 'h.presion', 'h.temperatura', 'h.peso', 'h.altura', 'h.perimetro', 'h.id_doctor2', 'h.id_doctor3', 'a.omni');

            $pacientes4 = DB::table('agenda as a')->where('a.estado', 4)
                ->whereBetween('fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])
                ->join('paciente as p', 'p.id', 'a.id_paciente')
                ->join('users as d', 'd.id', 'a.id_doctor1')
                ->leftjoin('empresa as em', 'em.id', 'a.id_empresa')
                ->join('seguros as se', 'se.id', 'a.id_seguro')
                ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                ->join('hc_procedimientos as hc_pro', 'hc_pro.id_hc', 'h.hcid')
                ->where('a.proc_consul', '4')->where('a.espid', '<>', '10')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'a.fechaini', 'a.fechafin', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'se.nombre', 'em.nombre_corto', 'h.hcid', 'a.estado_cita', 'hc_pro.id_doctor_examinador as doctor', 'h.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento', 'p.sexo', 'p.telefono1', 'p.telefono2', 'p.observacion', 'a.est_amb_hos', 'h.presion', 'h.temperatura', 'h.peso', 'h.altura', 'h.perimetro', 'h.id_doctor2', 'h.id_doctor3', 'a.omni');

            if ($proc_consul != 2) {
                //  $pacientes = $pacientes->where('a.proc_consul', $proc_consul);
                if ($proc_consul == 3) {
                    $pacientes1 = $pacientes1->where('a.omni', 'OM');
                    $pacientes2 = $pacientes2->where('a.omni', 'OM');
                    $pacientes2_0 = $pacientes2_0->where('a.omni', 'OM');
                    $pacientes3 = $pacientes3->where('a.omni', 'OM');
                    $pacientes4 = $pacientes4->where('a.omni', 'OM');
                } elseif ($proc_consul == 4) {
                    $pacientes1 = $pacientes1->where('a.omni', 'SI');
                    $pacientes2 = $pacientes2->where('a.omni', 'SI');
                    $pacientes2_0 = $pacientes2_0->where('a.omni', 'SI');
                    $pacientes3 = $pacientes3->where('a.omni', 'SI');
                    $pacientes4 = $pacientes4->where('a.omni', 'SI');
                } else {
                    $pacientes1 = $pacientes1->where('a.proc_consul', $proc_consul);
                    $pacientes2 = $pacientes2->where('a.proc_consul', $proc_consul);
                    $pacientes2_0 = $pacientes2_0->where('a.proc_consul', $proc_consul);
                    $pacientes3 = $pacientes3->where('a.proc_consul', $proc_consul);
                    $pacientes4 = $pacientes4->where('a.proc_consul', $proc_consul);
                }
            }

            if (!is_null($id_doctor1)) {
                //  $pacientes = $pacientes->where('a.id_doctor1', $id_doctor1);
                $pacientes1   = $pacientes1->where('a.id_doctor1', $id_doctor1);
                $pacientes2   = $pacientes2->where('h.id_doctor1', $id_doctor1);
                $pacientes2_0 = $pacientes2_0->where('hc_pro.id_doctor_examinador', $id_doctor1);
                $pacientes3   = $pacientes3->where('h.id_doctor1', $id_doctor1);
                $pacientes4   = $pacientes4->where('hc_pro.id_doctor_examinador', $id_doctor1);
            }

            if (!is_null($id_seguro)) {
                //  $pacientes = $pacientes->where('a.id_seguro', $id_seguro);
                $pacientes1   = $pacientes1->where('a.id_seguro', $id_seguro);
                $pacientes2   = $pacientes2->where('h.id_seguro', $id_seguro);
                $pacientes2_0 = $pacientes2_0->where('h.id_seguro', $id_seguro);
                $pacientes3   = $pacientes3->where('h.id_seguro', $id_seguro);
                $pacientes4   = $pacientes4->where('h.id_seguro', $id_seguro);
            }

            if (!is_null($espid)) {
                //  $pacientes = $pacientes->where('a.espid', $espid);
                $pacientes1   = $pacientes1->where('a.espid', $espid);
                $pacientes2   = $pacientes2->where('a.espid', $espid);
                $pacientes2_0 = $pacientes2_0->where('a.espid', $espid);
                $pacientes3   = $pacientes3->where('a.espid', $espid);
                $pacientes4   = $pacientes4->where('a.espid', $espid);
            }

            if (!is_null($cedula)) {
                //  $pacientes = $pacientes->where('a.id_paciente', $cedula);
                $pacientes1   = $pacientes1->where('a.id_paciente', $cedula);
                $pacientes2   = $pacientes2->where('a.id_paciente', $cedula);
                $pacientes2_0 = $pacientes2_0->where('a.id_paciente', $cedula);
                $pacientes3   = $pacientes3->where('a.id_paciente', $cedula);
                $pacientes4   = $pacientes4->where('a.id_paciente', $cedula);

            }

            $pacientes1 = $pacientes1->union($pacientes2)->union($pacientes2_0)->union($pacientes3)->union($pacientes4);

            $pacientes1 = $pacientes1->get();
            $pacientes  = $pacientes1;


            foreach ($pacientes as $pac) {
                //dd($pac);
                if ($pac->proc_consul == '1') {
                    $pentax = Pentax::where('id_agenda', $pac->id_agenda)->first();
                    if (!is_null($pentax)) {
                        $txt_px = '';
                        foreach ($pentax->procedimientos as $p) {
                            if ($txt_px == '') {
                                $txt_px = $p->procedimiento->nombre;
                            } else {
                                $txt_px = $txt_px . '+' . $p->procedimiento->nombre;
                            }
                        }
                        //dd($txt_px);
                        $agendas_proc[$pac->id_agenda] = [$txt_px];
                    }
                    // dd($agendas_proc[$pac->id_agenda]);
                }
            }
        }

        Excel::create('Historiaclinica-' . $fecha_d, function ($excel) use ($historiaclinica_pro, $historiaclinica_con, $fecha_d, $pacientes, $flag_nombres, $agendas_proc) {

            $excel->sheet('Historiaclinica', function ($sheet) use ($historiaclinica_pro, $historiaclinica_con, $fecha_d, $pacientes, $flag_nombres, $agendas_proc) {

                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');;
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA NACIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('SEXO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACION SI/NO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('I1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ALERGIA SI/NO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('J1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ALERGIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HOSPITALIZADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO SOLICITA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO EXAMINA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO CONSULTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MOTIVO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('R1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HALLAZGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('S1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DIAGNOSTICO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                //DIRECCIONES Y TELEFONOS
                $sheet->cell('T1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('U1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TEMPERATURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('V1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('W1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTATURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('X1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMC');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('Y1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMC VAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Z1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GCT');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AA1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PESO IDEAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AB1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PERIMETRO ABDOMINAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AC1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('EDAD');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AD1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RP');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AE1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AF1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTE 1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AG1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTE 2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AH1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AI1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AJ1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DR.TRAINING');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = 2;
                foreach ($pacientes as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->apellido2 != "(N/A)") {
                            $vapellido = $value->apellido1 . ' ' . $value->apellido2;
                        } else {
                            $vapellido = $value->apellido1;
                        }
                        $cell->setValue($vapellido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->nombre2 != "(N/A)") {
                            $vnombre = $value->nombre1 . ' ' . $value->nombre2;
                        } else {
                            $vnombre = $value->nombre1;
                        }
                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('D' . $i, function ($cell) use ($value) {
                        $cell->setValue($value->fecha_nacimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->sexo == '2') {
                            $cell->setValue('FEMENINO');
                        } else {
                            $cell->setValue('MASCULINO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->telefono1 . '/' . $value->telefono2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->observacion != null) {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->observacion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $alergiasxpac = null;
                    $txt_ale      = '';
                    $alergiasxpac = Paciente_Alergia::where('id_paciente', $value->id)->get();
                    if (!is_null($alergiasxpac)) {
                        foreach ($alergiasxpac as $ale_pac) {
                            $txt_ale = $txt_ale . ' ' . $ale_pac->principio_activo->nombre;
                        }
                    }
                    $sheet->cell('I' . $i, function ($cell) use ($value, $txt_ale) {
                        // manipulate the cel
                        if ($txt_ale != '') {
                            if ($txt_ale == 'NO') {
                                $cell->setValue('NO');
                            } else {
                                $cell->setValue('SI');
                            }
                        } else {
                            $cell->setValue('NO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('J' . $i, function ($cell) use ($value, $txt_ale) {
                        // manipulate the cel
                        $cell->setValue($txt_ale);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    if ($flag_nombres) {
                        $agenda_last = DB::table('agenda as a')
                            ->where('a.id_paciente', $value->id)
                            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
                            ->where('a.espid', '<>', '10')
                            ->orderBy('a.fechaini', 'desc')
                            ->join('seguros as s', 's.id', 'h.id_seguro')
                            ->join('empresa as em', 'em.id', 'a.id_empresa')
                            ->select('h.*', 's.nombre', 'a.fechaini', 'a.proc_consul', 'a.id as id_agenda', 'a.cortesia', 'em.nombre_corto', 'a.fechaini', 'a.est_amb_hos')
                            ->first();
                        if (!is_null($agenda_last)) {
                            $sheet->cell('K' . $i, function ($cell) use ($value, $agenda_last) {
                                // manipulate the cel
                                $cell->setValue(substr($agenda_last->fechaini, 0, 10));
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('L' . $i, function ($cell) use ($value, $agenda_last) {
                                // manipulate the cel
                                if ($agenda_last->est_amb_hos == '1') {
                                    $cell->setValue('SI');
                                } else {
                                    $cell->setValue('NO');
                                }
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('M' . $i, function ($cell) use ($value) {
                                // manipulate the cel
                                $cell->setValue(' ');
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $hc_procedimiento = hc_procedimientos::where('id_hc', $agenda_last->hcid)->first();
                            $hc_cie10 = Hc_Cie10::where('hcid', $agenda_last->hcid)->get();

                            $sheet->cell('N' . $i, function ($cell) use ($value, $agenda_last, $hc_procedimiento) {
                                // manipulate the cel AQUI SE DEBE RECUPERAR EL DOCTOR SEGUN EL CASO
                                $nombre_dr = '';
                                if (!is_null($hc_procedimiento)) {
                                    $doctor    = User::find($hc_procedimiento->id_doctor_examinador);
                                    $nombre_dr = $doctor->apellido1 . ' ' . $doctor->nombre1;
                                } else {
                                    $doctor    = User::find($agenda_last->id_doctor1);
                                    $nombre_dr = $doctor->apellido1 . ' ' . $doctor->nombre1;
                                }
                                $cell->setValue($nombre_dr);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('O' . $i, function ($cell) use ($value, $agenda_last) {
                                // manipulate the cel
                                $tipo = '';
                                if ($agenda_last->proc_consul == '1') {
                                    $tipo = 'PROCEDIMIENTO';
                                } else {
                                    $tipo = 'CONSULTA';
                                }
                                $cell->setValue($tipo);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('P' . $i, function ($cell) use ($value, $agenda_last, $hc_procedimiento) {
                                // manipulate the cel
                                $pnombre = '';
                                if ($agenda_last->proc_consul == '0') {
                                    $pnombre = 'CONSULTA';
                                }
                                if ($agenda_last->proc_consul == '1') {
                                    if (!is_null($hc_procedimiento)) {
                                        $flag1     = 1;
                                        $procs_fin = Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_procedimiento->id)->get();
                                        //dd($procs_fin);
                                        foreach ($procs_fin as $pval) {
                                            //dd($pval);
                                            if ($flag1) {
                                                $pnombre = $pval->procedimiento->nombre;
                                                $flag1   = 0;
                                            } else {
                                                $pnombre = $pnombre . '+ ' . $pval->procedimiento->nombre;
                                            }
                                        }
                                        if ($pnombre == '') {
                                            if ($hc_procedimiento->id_procedimiento_completo != null) {
                                                $pnombre = $hc_procedimiento->procedimiento_completo;
                                            }
                                        }
                                        if ($pnombre == '') {
                                            $pentax = Pentax::where('hcid', $agenda_last->hcid)->first();
                                            if (!is_null($pentax)) {
                                                $flag2 = 1;
                                                foreach ($pentax->procedimientos as $pf) {
                                                    if ($flag2) {
                                                        $pnombre = $pf->nombre;
                                                        $flag2   = 0;
                                                    } else {
                                                        $pnombre = $pnombre + '+ ' . $pf->nombre;
                                                    }
                                                }
                                            }
                                        }
                                        if ($pnombre == '') {
                                            $pnombre = 'NO INGRESADO';
                                        }
                                    }
                                }
                                $cell->setValue($pnombre);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('Q' . $i, function ($cell) use ($value, $agenda_last, $hc_procedimiento) {
                                //manipulate the cel
                                $motivo = ' ';
                                if ($agenda_last->proc_consul == '1') {
                                    if (!is_null($hc_procedimiento)) {
                                        $protocolo = hc_protocolo::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                                        if (!is_null($protocolo)) {
                                            $motivo = $protocolo->motivo;
                                        }
                                    }
                                } elseif ($agenda_last->proc_consul == '0') {
                                    if (!is_null($hc_procedimiento)) {
                                        $evolucion = Hc_Evolucion::where('hcid', $hc_procedimiento->id_hc);
                                        if (!is_null($evolucion)) {
                                            $motivo = isset($evolucion->motivo) ? $evolucion->motivo : '';
                                        }
                                    }
                                }
                                $cell->setValue($motivo);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('R' . $i, function ($cell) use ($value, $agenda_last, $hc_procedimiento) {
                                //manipulate the cel
                                $hallazgo = ' ';
                                if ($agenda_last->proc_consul == '1') {
                                    if (!is_null($hc_procedimiento)) {
                                        $protocolo = hc_protocolo::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                                        if (!is_null($protocolo)) {
                                            $hallazgo = $protocolo->hallazgos;
                                        }
                                    }
                                } elseif ($agenda_last->proc_consul == '0') {
                                    if (!is_null($hc_procedimiento)) {
                                        $evolucion = Hc_Evolucion::where('hcid', $hc_procedimiento->id_hc);
                                        if (!is_null($evolucion)) {
                                            $hallazgo = $evolucion->cuadro_clinico;
                                        }
                                    }
                                }
                                $hallazgo = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($hallazgo)), ENT_QUOTES, "UTF-8");
                                $cell->setValue($hallazgo);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('S' . $i, function ($cell) use ($value, $agenda_last, $hc_procedimiento, $hc_cie10) {
                                // manipulate the cel
                                $conclusion = ' ';
                                if ($agenda_last->proc_consul == '1') {
                                    if (!is_null($hc_procedimiento)) {
                                        $protocolo = hc_protocolo::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                                        if (!is_null($protocolo)) {
                                            $conclusion = $protocolo->conclusion;
                                        }
                                    }
                                } elseif ($agenda_last->proc_consul == '0') {
                                    if (!is_null($hc_procedimiento)) {
                                        $evolucion = Hc_Evolucion::where('hcid', $hc_procedimiento->id_hc);
                                        if (!is_null($evolucion)) {
                                            $conclusion = $evolucion->indicaciones;
                                        }
                                    }
                                }
                                $conclusion = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($conclusion)), ENT_QUOTES, "UTF-8");
                                $tcie10 = '';
                                foreach ($hc_cie10 as $c10) {
                                    $c3 = Cie_10_3::find($c10->cie10);
                                    $c4 = Cie_10_4::find($c10->cie10);
                                    if ($c3 != null) {
                                        $tcie10 = $c10->cie10 . ": " . $c3->descripcion;
                                    }
                                    if ($c4 != null) {
                                        $tcie10 = $c10->cie10 . ": " . $c4->descripcion;
                                    }
                                }
                                $cell->setValue($conclusion . " " . $tcie10);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('T' . $i, function ($cell) use ($value, $agenda_last) {
                                $cell->setValue($agenda_last->presion);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('U' . $i, function ($cell) use ($value, $agenda_last) {
                                $cell->setValue($agenda_last->temperatura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('V' . $i, function ($cell) use ($value, $agenda_last) {
                                $cell->setValue($agenda_last->peso);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('W' . $i, function ($cell) use ($value, $agenda_last) {
                                $cell->setValue($agenda_last->altura);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            if ($value->fecha_nacimiento == null) {
                                $edad = 0;
                            } else {
                                $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                            }
                            $sheet->cell('X' . $i, function ($cell) use ($value, $agenda_last, $edad) {
                                $peso     = $agenda_last->peso;
                                $estatura = $agenda_last->altura;

                                if ($value->sexo == 1) {
                                    $sexo = $value->sexo;
                                } else {
                                    $sexo = 0;
                                }


                                $estatura2 = pow(($estatura / 100), 2);

                                $peso_ideal = 21.45 * ($estatura2);
                                if ($estatura2 == 0) {
                                    $imc = 0;
                                } else {
                                    $imc = $peso / $estatura2;
                                }
                                $gct   = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                                $texto = "";
                                if ($imc < 16) {
                                    $texto = "Desnutrición";
                                } else if ($imc < 18) {
                                    $texto = "Bajo de Peso";
                                } else if ($imc < 25) {
                                    $texto = "Normal";
                                } else if ($imc < 27) {
                                    $texto = "Sobrepeso";
                                } else if ($imc < 30) {
                                    $texto = "Obesidad Tipo 1";
                                } else if ($imc < 40) {
                                    $texto = "Obesidad Clinica";
                                } else {
                                    $texto = "Obesidad Mordida";
                                }
                                $cell->setValue($texto);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('Y' . $i, function ($cell) use ($value, $agenda_last, $edad) {
                                $peso     = $agenda_last->peso;
                                $estatura = $agenda_last->altura;

                                if ($value->sexo == 1) {
                                    $sexo = $value->sexo;
                                } else {
                                    $sexo = 0;
                                }

                                $estatura2 = pow(($estatura / 100), 2);

                                $peso_ideal = 21.45 * ($estatura2);
                                if ($estatura2 == 0) {
                                    $imc = 0;
                                } else {
                                    $imc = $peso / $estatura2;
                                }
                                $cell->setValue($imc);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('Z' . $i, function ($cell) use ($value, $agenda_last, $edad) {
                                $peso     = $agenda_last->peso;
                                $estatura = $agenda_last->altura;

                                if ($value->sexo == 1) {
                                    $sexo = $value->sexo;
                                } else {
                                    $sexo = 0;
                                }

                                $estatura2 = pow(($estatura / 100), 2);

                                $peso_ideal = 21.45 * ($estatura2);
                                if ($estatura2 == 0) {
                                    $imc = 0;
                                } else {
                                    $imc = $peso / $estatura2;
                                }

                                if ($imc == 0) {
                                    $gct = 0;
                                } else {
                                    $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                                }
                                $cell->setValue($gct);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('AA' . $i, function ($cell) use ($value, $agenda_last, $edad) {
                                $peso     = $agenda_last->peso;
                                $estatura = $agenda_last->altura;

                                if ($value->sexo == 1) {
                                    $sexo = $value->sexo;
                                } else {
                                    $sexo = 0;
                                }

                                $estatura2 = pow(($estatura / 100), 2);

                                $peso_ideal = 21.45 * ($estatura2);
                                if ($estatura2 == 0) {
                                    $imc = 0;
                                } else {
                                    $imc = $peso / $estatura2;
                                }
                                $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                                $cell->setValue($peso_ideal);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('AB' . $i, function ($cell) use ($value, $agenda_last) {

                                $cell->setValue($agenda_last->perimetro);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('AC' . $i, function ($cell) use ($value, $agenda_last, $edad) {

                                $cell->setValue($edad);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $receta    = '';
                            $receta2   = '';
                            $rp        = '';
                            $pres      = '';
                            $hc_receta = hc_receta::where('id_hc', $agenda_last->hcid)->first();
                            if (!is_null($hc_receta)) {
                                $detalles = hc_receta_detalle::where('id_hc_receta', $hc_receta->id)->get();
                                $rp       = $hc_receta->rp;
                                $pres     = $hc_receta->prescripcion;
                                foreach ($detalles as $value2) {
                                    $genericos = Medicina::where('id', $value2->id_medicina)->first()->genericos;
                                    $receta    = $receta . ' - ' . $value2->medicina->nombre . ' (';
                                    $receta2   = $receta2 . ' - ' . $value2->medicina->nombre . ': ' . $value2->dosis;
                                    foreach ($genericos as $gen) {

                                        $receta = $receta . $gen->generico->nombre . ' ';
                                    }
                                    $receta = $receta . ' ) ' . $value2->cantidad;
                                }
                            }
                            $sheet->cell('AD' . $i, function ($cell) use ($value, $receta, $rp, $pres) {
                                $rece_1 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($rp)), ENT_QUOTES, "UTF-8");
                                $cell->setValue($receta . ' ' . $rece_1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('AE' . $i, function ($cell) use ($value, $receta2, $rp, $pres) {
                                $rece_2 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($pres)), ENT_QUOTES, "UTF-8");
                                $cell->setValue($receta2 . ' ' . $rece_2);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('AF' . $i, function ($cell) use ($value, $agenda_last) {
                                $nombre_dr2 = '';
                                if ($agenda_last->id_doctor2 != null) {
                                    $doctor2    = User::find($agenda_last->id_doctor2);
                                    $nombre_dr2 = $doctor2->apellido1 . ' ' . $doctor2->nombre1;
                                }
                                $cell->setValue($nombre_dr2);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $sheet->cell('AG' . $i, function ($cell) use ($value, $agenda_last) {
                                $nombre_dr3 = '';
                                if ($agenda_last->id_doctor3 != null) {
                                    $doctor3    = User::find($agenda_last->id_doctor3);
                                    $nombre_dr3 = $doctor3->apellido1 . ' ' . $doctor3->nombre1;
                                }
                                $cell->setValue($nombre_dr3);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            //CONVENIO
                            $sheet->cell('AH' . $i, function ($cell) use ($value, $agenda_last) {
                                $tempresa = $agenda_last->nombre . '/' . $agenda_last->nombre_corto;
                                $cell->setValue($tempresa);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            //estado
                            $sheet->cell('AI' . $i, function ($cell) use ($value, $agenda_last) {
                                $pentax = Pentax::where('hcid', $agenda_last->hcid)->first();
                                $estado = '';
                                if (!is_null($pentax)) {
                                    if ($pentax->estado_pentax == '0') {
                                        $estado = 'EN ESPERA';
                                    } elseif ($pentax->estado_pentax == '1') {
                                        $estado = 'PREPARACION';
                                    } elseif ($pentax->estado_pentax == '2') {
                                        $estado = 'EN PROCEDIMIENTO';
                                    } elseif ($pentax->estado_pentax == '3') {
                                        $estado = 'RECUPERACION';
                                    } elseif ($pentax->estado_pentax == '4') {
                                        $estado = 'ALTA';
                                    } elseif ($pentax->estado_pentax == '5') {
                                        $estado = 'SUSPENDIDO';
                                    }
                                }
                                $cell->setValue($estado);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $tr_arr = ['J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
                            if ($agenda_last->proc_consul == '1') {
                                if (!is_null($hc_procedimiento)) {
                                    $protocolo = hc_protocolo::where('id_hc_procedimientos', $hc_procedimiento->id)->first();
                                    if (!is_null($protocolo)) {
                                        $trainin_pro = Hc_protocolo_training::where('id_hc_protocolo', $protocolo->id)->get();
                                        $tr_i        = 0;
                                        foreach ($trainin_pro as $tr) {
                                            $sheet->cell('A' . $tr_arr[$tr_i] . $i, function ($cell) use ($tr, $tr_i) {
                                                $cell->setValue($tr->doctor->apellido1 . ' ' . $tr->doctor->nombre1);
                                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                            });
                                            $tr_i++;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        //AUN NO REALIZADO EL CAMBIO
                        $sheet->cell('K' . $i, function ($cell) use ($value) {
                            //manipulate the cel
                            $cell->setValue(substr($value->fechaini, 0, 10));
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('L' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->est_amb_hos == '1') {
                                $cell->setValue('SI');
                            } else {
                                $cell->setValue('NO');
                            }
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('M' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $cell->setValue(' ');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('N' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            if ($value->doctor != null) {
                                $doctor = User::find($value->doctor);
                                $cell->setValue($doctor->apellido1 . ' ' . $doctor->nombre1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            }
                        });
                        $sheet->cell('O' . $i, function ($cell) use ($value) {
                            // manipulate the cel
                            $tipo = '';
                            if ($value->proc_consul == '0') {
                                $tipo = 'CONSULTA';
                            } elseif ($value->proc_consul == '1') {
                                $tipo = 'PROCEDIMIENTO';
                            } else {
                                if ($value->observaciones == 'PROCEDIMIENTO CREADO POR EL DOCTOR') {
                                    $tipo = 'PROCEDIMIENTO';
                                } else {
                                    if ($value->omni == 'OM') {
                                        $tipo = 'VISITA OMNI';
                                    } else {
                                        $tipo = 'VISITA';
                                    }
                                }
                            }
                            $cell->setValue($tipo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $hc_proce = null;
                        $hc_cie10 = null;
                        if ($value->hcid != '') {
                            $hc_proce = hc_procedimientos::where('id_hc', $value->hcid)->first();
                            $hc_cie10 = Hc_Cie10::where('hcid', $value->hcid)->get();
                        }
                        $sheet->cell('P' . $i, function ($cell) use ($value, $agendas_proc, $hc_proce) {
                            // manipulate the cel
                            $pnombre = '';
                            if ($value->proc_consul == '0') {
                                $pnombre = 'CONSULTA';
                            } elseif ($value->proc_consul == '1') {
                                if (isset($agendas_proc[$value->id_agenda])) {
                                    $pnombre = $agendas_proc[$value->id_agenda]['0'];
                                } else {
                                    $nomb_proc        = Procedimiento::where('id', $value->id_procedimiento)->first();
                                    $agprocedimientos = AgendaProcedimiento::where('id_agenda', $value->id_agenda)->get();
                                    if (!is_null($nomb_proc)) {
                                        $pnombre = $nomb_proc->nombre;
                                    }
                                    if (!is_null($agprocedimientos)) {
                                        foreach ($agprocedimientos as $agendaproc) {
                                            $pnombre = $pnombre . ' + ' . Procedimiento::find($agendaproc->id_procedimiento)->nombre;
                                        }
                                    }
                                }
                            } else {
                                if ($value->observaciones == 'PROCEDIMIENTO CREADO POR EL DOCTOR') {
                                    if ($value->hcid != '') {
                                        if (!is_null($hc_proce)) {
                                            $proc_final = Hc_Procedimiento_Final::where('id_hc_procedimientos', $hc_proce->id)->get();
                                            $fl_pf      = 1;
                                            foreach ($proc_final as $pf) {
                                                if ($fl_pf) {
                                                    $pnombre = Procedimiento::where('id', $pf->id_procedimiento)->first()->nombre;
                                                    $fl_pf   = 0;
                                                } else {
                                                    $pnombre = $pnombre . ' + ' . Procedimiento::where('id', $pf->id_procedimiento)->first()->nombre;
                                                }
                                            }
                                        }
                                    }
                                } else {

                                    if ($value->omni == 'OM') {
                                        $pnombre = 'VISITA OMNI';
                                    } else {
                                        $pnombre = 'VISITA';
                                    }
                                }
                            }
                            $cell->setValue($pnombre);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('Q' . $i, function ($cell) use ($value, $hc_proce) {
                            // manipulate the cel
                            $motivo = ' ';
                            if ($value->proc_consul == '1') {
                                if (!is_null($hc_proce)) {
                                    $protocolo = hc_protocolo::where('id_hc_procedimientos', $hc_proce->id)->first();
                                    if (!is_null($protocolo)) {
                                        $motivo = $protocolo->motivo;
                                    }
                                }
                            } elseif ($value->proc_consul == '0') {
                                if (!is_null($hc_proce)) {
                                    $evolucion = Hc_Evolucion::where('hcid', $hc_proce->id_hc)->first();
                                    if (!is_null($evolucion)) {
                                        $motivo = $evolucion->motivo;
                                    }
                                }
                            }
                            $cell->setValue($motivo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('R' . $i, function ($cell) use ($value, $hc_proce) {
                            // manipulate the cel
                            $hallazgo = ' ';
                            if ($value->proc_consul == '1') {
                                if (!is_null($hc_proce)) {
                                    $protocolo = hc_protocolo::where('id_hc_procedimientos', $hc_proce->id)->first();
                                    if (!is_null($protocolo)) {
                                        $hallazgo = $protocolo->hallazgos;
                                    }
                                }
                            } elseif ($value->proc_consul == '0') {
                                if (!is_null($hc_proce)) {
                                    $evolucion = Hc_Evolucion::where('hcid', $hc_proce->id_hc)->first();
                                    if (!is_null($evolucion)) {
                                        $hallazgo = $evolucion->cuadro_clinico;
                                    }
                                }
                            }
                            $hallazgo = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($hallazgo)), ENT_QUOTES, "UTF-8");
                            $cell->setValue($hallazgo);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('S' . $i, function ($cell) use ($value, $hc_proce, $hc_cie10) {
                            // manipulate the cel
                            $conclusion = ' ';
                            if ($value->proc_consul == '1') {
                                if (!is_null($hc_proce)) {
                                    $protocolo = hc_protocolo::where('id_hc_procedimientos', $hc_proce->id)->first();
                                    if (!is_null($protocolo)) {
                                        $conclusion = $protocolo->conclusion;
                                    }
                                }
                            } elseif ($value->proc_consul == '0') {
                                if (!is_null($hc_proce)) {
                                    $evolucion = Hc_Evolucion::where('hcid', $hc_proce->id_hc)->first();
                                    if (!is_null($evolucion)) {
                                        $conclusion = $evolucion->indicaciones;
                                    }
                                }
                            }
                            $conclusion = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($conclusion)), ENT_QUOTES, "UTF-8");
                            $tcie10 = '';
                            if (!is_null($hc_cie10)) {
                                foreach ($hc_cie10 as $c10) {
                                    $c3 = Cie_10_3::find($c10->cie10);
                                    $c4 = Cie_10_4::find($c10->cie10);
                                    if ($c3 != null) {
                                        $tcie10 = $c10->cie10 . ": " . $c3->descripcion;
                                    }
                                    if ($c4 != null) {
                                        $tcie10 = $c10->cie10 . ": " . $c4->descripcion;
                                    }
                                }
                            }

                            $cell->setValue($conclusion . " " . $tcie10);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('T' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->presion);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('U' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->temperatura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('V' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->peso);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('W' . $i, function ($cell) use ($value) {
                            $cell->setValue($value->altura);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        if ($value->fecha_nacimiento == null) {
                            $edad = 0;
                        } else {
                            $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        }
                        $sheet->cell('X' . $i, function ($cell) use ($value, $edad) {
                            $peso     = $value->peso;
                            $estatura = $value->altura;

                            if ($value->sexo == 1) {
                                $sexo = $value->sexo;
                            } else {
                                $sexo = 0;
                            }


                            $estatura2 = pow(($estatura / 100), 2);

                            $peso_ideal = 21.45 * ($estatura2);
                            if ($estatura2 == 0) {
                                $imc = 0;
                            } else {
                                $imc = $peso / $estatura2;
                            }
                            $gct   = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                            $texto = "";
                            if ($imc < 16) {
                                $texto = "Desnutrición";
                            } else if ($imc < 18) {
                                $texto = "Bajo de Peso";
                            } else if ($imc < 25) {
                                $texto = "Normal";
                            } else if ($imc < 27) {
                                $texto = "Sobrepeso";
                            } else if ($imc < 30) {
                                $texto = "Obesidad Tipo 1";
                            } else if ($imc < 40) {
                                $texto = "Obesidad Clinica";
                            } else {
                                $texto = "Obesidad Mordida";
                            }
                            $cell->setValue($texto);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('Y' . $i, function ($cell) use ($value, $edad) {
                            $peso     = $value->peso;
                            $estatura = $value->altura;

                            if ($value->sexo == 1) {
                                $sexo = $value->sexo;
                            } else {
                                $sexo = 0;
                            }

                            $estatura2 = pow(($estatura / 100), 2);

                            $peso_ideal = 21.45 * ($estatura2);
                            if ($estatura2 == 0) {
                                $imc = 0;
                            } else {
                                $imc = $peso / $estatura2;
                            }
                            $cell->setValue($imc);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('Z' . $i, function ($cell) use ($value, $edad) {
                            $peso     = $value->peso;
                            $estatura = $value->altura;

                            if ($value->sexo == 1) {
                                $sexo = $value->sexo;
                            } else {
                                $sexo = 0;
                            }

                            $estatura2 = pow(($estatura / 100), 2);

                            $peso_ideal = 21.45 * ($estatura2);
                            if ($estatura2 == 0) {
                                $imc = 0;
                            } else {
                                $imc = $peso / $estatura2;
                            }

                            if ($imc == 0) {
                                $gct = 0;
                            } else {
                                $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                            }
                            $cell->setValue($gct);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AA' . $i, function ($cell) use ($value, $edad) {
                            $peso     = $value->peso;
                            $estatura = $value->altura;

                            if ($value->sexo == 1) {
                                $sexo = $value->sexo;
                            } else {
                                $sexo = 0;
                            }

                            $estatura2 = pow(($estatura / 100), 2);

                            $peso_ideal = 21.45 * ($estatura2);
                            if ($estatura2 == 0) {
                                $imc = 0;
                            } else {
                                $imc = $peso / $estatura2;
                            }
                            $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                            $cell->setValue($peso_ideal);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AB' . $i, function ($cell) use ($value) {

                            $cell->setValue($value->perimetro);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AC' . $i, function ($cell) use ($value, $edad) {

                            $cell->setValue($edad);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $receta  = '';
                        $receta2 = '';
                        $rp      = '';
                        $pres    = '';
                        if ($value->hcid != null) {
                            $hc_receta = hc_receta::where('id_hc', $value->hcid)->first();
                            if (!is_null($hc_receta)) {
                                $detalles = hc_receta_detalle::where('id_hc_receta', $hc_receta->id)->get();
                                $rp       = $hc_receta->rp;
                                $pres     = $hc_receta->prescripcion;
                                foreach ($detalles as $value2) {
                                    $genericos = Medicina::where('id', $value2->id_medicina)->first()->genericos;
                                    $receta    = $receta . ' - ' . $value2->medicina->nombre . ' (';
                                    $receta2   = $receta2 . ' - ' . $value2->medicina->nombre . ': ' . $value2->dosis;
                                    foreach ($genericos as $gen) {

                                        $receta = $receta . $gen->generico->nombre . ' ';
                                    }
                                    $receta = $receta . ' ) ' . $value2->cantidad;
                                }
                            }
                        }
                        $sheet->cell('AD' . $i, function ($cell) use ($value, $receta, $rp, $pres) {
                            $rece_1 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($rp)), ENT_QUOTES, "UTF-8");
                            $cell->setValue($receta . ' ' . $rece_1);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AE' . $i, function ($cell) use ($value, $receta2, $rp, $pres) {
                            $rece_2 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($pres)), ENT_QUOTES, "UTF-8");
                            $cell->setValue($receta2 . ' ' . $rece_2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        /*->select('p.id','p.nombre1','p.nombre2','p.apellido1','p.apellido2','p.fecha_nacimiento','a.fechaini','a.fechafin','d.nombre1 as dnombre1','d.apellido1 as dapellido1','a.proc_consul','a.id as id_agenda', 'a.cortesia','se.nombre','em.nombre_corto','h.hcid', 'a.estado_cita', 'a.id_doctor1 as doctor', 'a.id_seguro as seguro_nom', 'a.observaciones', 'a.id_procedimiento','p.sexo', 'p.telefono1',  'p.telefono2', 'p.observacion', 'a.est_amb_hos', 'h.presion', 'h.temperatura', 'h.peso', 'h.altura', 'h.perimetro', 'h.id_doctor2', 'h.id_doctor3');*/
                        $sheet->cell('AF' . $i, function ($cell) use ($value) {
                            $nombre_dr2 = '';
                            if ($value->id_doctor2 != null) {
                                $doctor2    = User::find($value->id_doctor2);
                                $nombre_dr2 = $doctor2->apellido1 . ' ' . $doctor2->nombre1;
                            }
                            $cell->setValue($nombre_dr2);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AG' . $i, function ($cell) use ($value) {
                            $nombre_dr3 = '';
                            if ($value->id_doctor3 != null) {
                                $doctor3    = User::find($value->id_doctor3);
                                $nombre_dr3 = $doctor3->apellido1 . ' ' . $doctor3->nombre1;
                            }
                            $cell->setValue($nombre_dr3);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        //CONVENIO
                        $sheet->cell('AH' . $i, function ($cell) use ($value) {
                            $hc_seguro = '';
                            $hc_proc = hc_procedimientos::where('id_hc', $value->hcid)->first();
                            if (!is_null($hc_proc)) {
                                if ($hc_proc->id_seguro != null) {
                                    $hc_seguro = Seguro::find($hc_proc->id_seguro)->nombre;
                                }
                            }

                            $seguro_nombre = Seguro::find($value->seguro_nom)->nombre;
                            $tempresa      = '';
                            if ($value->nombre_corto != null) {
                                $tempresa = '/' . $value->nombre_corto;
                            }
                            if ($hc_seguro != '') {
                                $seguro_nombre = $hc_seguro;
                            }
                            $cell->setValue($seguro_nombre . $tempresa);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cell('AI' . $i, function ($cell) use ($value) {
                            $estado = '';
                            if ($value->hcid != null) {
                                $pentax = Pentax::where('hcid', $value->hcid)->first();
                                if (!is_null($pentax)) {
                                    if ($pentax->estado_pentax == '0') {
                                        $estado = 'EN ESPERA';
                                    } elseif ($pentax->estado_pentax == '1') {
                                        $estado = 'PREPARACION';
                                    } elseif ($pentax->estado_pentax == '-1') {
                                        $estado = 'PRE-ADMISION';
                                    } elseif ($pentax->estado_pentax == '2') {
                                        $estado = 'EN PROCEDIMIENTO';
                                    } elseif ($pentax->estado_pentax == '3') {
                                        $estado = 'RECUPERACION';
                                    } elseif ($pentax->estado_pentax == '4') {
                                        $estado = 'ALTA';
                                    } elseif ($pentax->estado_pentax == '5') {
                                        if ($value->estado_cita == '4') {
                                            $estado = 'SUSPENDIDO';
                                        }
                                    }
                                }
                            }
                            if ($estado == '') {
                                if ($value->omni == 'OM') {
                                    if ($value->estado_cita == '4') {
                                        $estado = 'INGRESO';
                                    } elseif ($value->estado_cita == '5') {
                                        $estado = 'ALTA';
                                    } elseif ($value->estado_cita == '6') {
                                        $estado = 'EMERGENCIA';
                                    }
                                } else {
                                    if ($value->estado_cita == '0') {
                                        $estado = 'POR CONFIRMAR';
                                    } elseif ($value->estado_cita == '1') {
                                        $estado = 'CONFIRMADA';
                                    } elseif ($value->estado_cita == '3') {
                                        $estado = 'SUSPENDIDA';
                                    }
                                }
                            }
                            $cell->setValue($estado);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $tr_arr = ['J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X'];
                        if ($value->proc_consul == '1') {
                            if (!is_null($hc_proce)) {
                                $protocolo = hc_protocolo::where('id_hc_procedimientos', $hc_proce->id)->first();
                                if (!is_null($protocolo)) {
                                    $trainin_pro = Hc_protocolo_training::where('id_hc_protocolo', $protocolo->id)->get();
                                    $tr_i        = 0;
                                    foreach ($trainin_pro as $tr) {
                                        $sheet->cell('A' . $tr_arr[$tr_i] . $i, function ($cell) use ($tr, $tr_i) {
                                            $cell->setValue($tr->doctor->apellido1 . ' ' . $tr->doctor->nombre1);
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        });
                                        $tr_i++;
                                    }
                                }
                            }
                        }
                    }
                    $i = $i + 1;
                }
            });

            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(15)->setAutosize(false);
        })->export('xlsx');
    }

    public function reporte_hc_iess(Request $request)
    {

        //return $request->all();
        $desde = $request->fecha;
        $hasta = $request->fecha_hasta;

        $fecha_d = Date('Y-m-d');


        $historiaclinica_pro = DB::table('historiaclinica as hc')->join('paciente as p', 'p.id', 'hc.id_paciente')->join('hc_procedimientos as hp', 'hp.id_hc', 'hc.hcid')->join('agenda as a', 'a.id', 'hc.id_agenda')->join('users as d1', 'd1.id', 'hc.id_doctor1')->leftjoin('procedimiento_completo as pp', 'pp.id', 'hp.id_procedimiento_completo')->leftjoin('grupo_procedimiento as gp', 'gp.id', 'pp.id_grupo_procedimiento')->leftjoin('hc_protocolo as pro', 'pro.id_hc_procedimientos', 'hp.id')->leftjoin('hc_receta as rta', 'rta.id_hc', 'hc.hcid')->leftjoin('users as d2', 'd2.id', 'hc.id_doctor2')->leftjoin('users as d3', 'd3.id', 'hc.id_doctor3')->join('seguros as seg', 'seg.id', 'hc.id_seguro')->leftjoin('pentax as px', 'px.hcid', 'hc.hcid')->select('hc.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'p.telefono1', 'p.telefono2', 'p.sexo', 'p.observacion', 'a.est_amb_hos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'a.proc_consul', 'gp.nombre as gpnombre', 'pp.nombre_general as ppnombregeneral', 'pro.motivo as promotivo', 'pro.hallazgos as prohallazgo', 'pro.conclusion as proconclusion', 'rta.id as receta', 'd3.apellido1 as d3apellido1', 'd3.nombre1 as d3nombre1', 'd2.apellido1 as d2apellido1', 'd2.nombre1 as d2nombre1', 'seg.nombre as seguro', 'rta.prescripcion', 'rta.rp', 'hp.id_doctor_examinador', 'hp.id_seguro as id_seguro_procedimiento', 'px.estado_pentax', 'pro.id as protocolo', 'a.id_empresa', 'hp.id as hc_pro')->where('hp.id_seguro', '2')->where('a.estado', '!=', '0')->where('a.proc_consul', '1')->where('a.id_doctor1', '<>', '4444444444')->whereBetween('a.fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])->where('hp.estado', '1')->get();

        $historiaclinica_con = DB::table('historiaclinica as hc')->join('paciente as p', 'p.id', 'hc.id_paciente')->join('hc_procedimientos as hp', 'hp.id_hc', 'hc.hcid')->join('agenda as a', 'a.id', 'hc.id_agenda')->join('users as d1', 'd1.id', 'hc.id_doctor1')->leftjoin('procedimiento_completo as pp', 'pp.id', 'hp.id_procedimiento_completo')->leftjoin('grupo_procedimiento as gp', 'gp.id', 'pp.id_grupo_procedimiento')->leftjoin('hc_protocolo as pro', 'pro.id_hc_procedimientos', 'hp.id')->leftjoin('hc_evolucion as evo', 'evo.hc_id_procedimiento', 'hp.id')->leftjoin('hc_child_pugh as chi', 'chi.id_hc_evolucion', 'evo.id')->leftjoin('hc_receta as rta', 'rta.id_hc', 'hc.hcid')->leftjoin('users as d2', 'd2.id', 'hc.id_doctor2')->leftjoin('users as d3', 'd3.id', 'hc.id_doctor3')->join('seguros as seg', 'seg.id', 'hc.id_seguro')->leftjoin('pentax as px', 'px.hcid', 'hc.hcid')->select('hc.*', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'p.telefono1', 'p.telefono2', 'p.sexo', 'p.observacion', 'a.est_amb_hos', 'd1.apellido1 as d1apellido1', 'd1.nombre1 as d1nombre1', 'a.proc_consul', 'gp.nombre as gpnombre', 'pp.nombre_general as ppnombregeneral', 'pro.motivo as promotivo', 'pro.hallazgos as prohallazgo', 'evo.motivo as evomotivo', 'chi.examen_fisico', 'pro.conclusion as proconclusion', 'rta.id as receta', 'd3.apellido1 as d3apellido1', 'd3.nombre1 as d3nombre1', 'd2.apellido1 as d2apellido1', 'd2.nombre1 as d2nombre1', 'seg.nombre as seguro', 'rta.prescripcion', 'rta.rp', 'hp.id_doctor_examinador', 'hp.id_seguro as id_seguro_procedimiento', 'px.estado_pentax', 'a.id_empresa', 'a.espid', 'a.estado_cita')->where('hc.id_seguro', '2')->where('a.estado', '!=', '0')->where('a.proc_consul', '=', '0')->whereBetween('a.fechaini', [$desde . ' 00:00', $hasta . ' 23:59'])->get();


        Excel::create('Historiaclinica-' . $fecha_d, function ($excel) use ($historiaclinica_pro, $historiaclinica_con, $fecha_d) {

            $excel->sheet('Historiaclinica', function ($sheet) use ($historiaclinica_pro, $historiaclinica_con, $fecha_d) {

                //$sheet->getDefaultColumnDimension('R')->setAutosize(false);
                //$sheet->getDefaultColumnDimension('R')->setWidth(5);
                $sheet->cell('A1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CEDULA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('APELLIDOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRES');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');;
                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA NACIMIENTO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cel

                    $cell->setValue('SEXO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('F1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TELEFONOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('G1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACION SI/NO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('H1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('OBSERVACION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('I1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ALERGIA SI/NO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('J1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ALERGIA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('K1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('FECHA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('L1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HOSPITALIZADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('M1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO PRINCIPAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('N1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MEDICO ASISTENTE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('O1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TIPO CONSULTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('P1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PROCEDIMIENTOS');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Q1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('MOTIVO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('R1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('HALLAZGO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('S1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DIAGNOSTICO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                //DIRECCIONES Y TELEFONOS
                $sheet->cell('T1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PRESION');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('U1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('TEMPERATURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('V1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PESO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('W1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTATURA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('X1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMC');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Y1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('IMC VAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('Z1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('GCT');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AA1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PESO IDEAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AB1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PERIMETRO ABDOMINAL');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AC1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('RP');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AD1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('PR');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AE1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTE 1');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AF1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ASISTENTE 2');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AG1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CONVENIO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AH1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('ESTADO');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell('AI1', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('DR.TRAINING');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    $cell->setFontWeight('bold');
                });

                $i = 2;
                foreach ($historiaclinica_pro as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->apellido2 != "(N/A)") {
                            $vapellido = $value->apellido1 . ' ' . $value->apellido2;
                        } else {
                            $vapellido = $value->apellido1;
                        }

                        $cell->setValue($vapellido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        if ($value->nombre2 != "(N/A)") {
                            $vnombre = $value->nombre1 . ' ' . $value->nombre2;
                        } else {
                            $vnombre = $value->nombre1;
                        }

                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->fecha_nacimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->sexo == '2') {
                            $cell->setValue('FEMENINO');
                        } else {
                            $cell->setValue('MASCULINO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->telefono1 . '/' . $value->telefono2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->observacion != null) {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue($value->observacion);

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $alergiasxpac = null;
                    $txt_ale      = '';
                    $alergiasxpac = Paciente_Alergia::where('id_paciente', $value->id_paciente)->get();
                    if (!is_null($alergiasxpac)) {

                        foreach ($alergiasxpac as $ale_pac) {
                            $txt_ale = $txt_ale . ' ' . $ale_pac->principio_activo->nombre;
                        }
                    }

                    $sheet->cell('I' . $i, function ($cell) use ($value, $txt_ale) {
                        // manipulate the cel

                        if ($txt_ale != '') {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value, $txt_ale) {
                        // manipulate the cel

                        $cell->setValue($txt_ale);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue(substr($value->created_at, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->est_amb_hos == '1') {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->id_empresa == '1307189140001') {
                            $cell->setValue('ROBLES CARLOS');
                        } else {
                            if ($value->id_doctor_examinador == null) {
                                $cell->setValue($value->d1apellido1 . ' ' . $value->d1nombre1);
                            } else {
                                $doctor = User::find($value->id_doctor_examinador);
                                $cell->setValue($doctor->apellido1 . ' ' . $doctor->nombre1);
                            }
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->id_empresa != '1307189140001') {
                            $cell->setValue(' ');
                        } else {

                            if ($value->id_doctor_examinador == null) {
                                if ($value->id_doctor1 == '1307189140') {
                                    $cell->setValue(' ');
                                } else {
                                    $cell->setValue($value->d1apellido1 . ' ' . $value->d1nombre1);
                                }
                                $cell->setValue($value->d1apellido1 . ' ' . $value->d1nombre1);
                            } else {
                                if ($value->id_doctor_examinador == '1307189140') {
                                    $cell->setValue(' ');
                                } else {
                                    $doctor = User::find($value->id_doctor_examinador);
                                    $cell->setValue($doctor->apellido1 . ' ' . $doctor->nombre1);
                                }
                            }
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $tipo = 'PROCEDIMIENTO';
                        if ($value->proc_consul == '1') {
                            $tipo = $value->gpnombre;
                        } else {
                            $tipo = 'CONSULTA';
                        }
                        if ($tipo == '') {
                            $tipo = 'PROCEDIMIENTO';
                        }

                        $cell->setValue($tipo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $pnombre = '';
                        if ($value->proc_consul == '1') {
                            $pentax = Pentax::where('id_agenda', $value->id_agenda)->first();
                            if (!is_null($pentax)) {
                                $txt_px = '';
                                foreach ($pentax->procedimientos as $p) {
                                    if ($txt_px == '') {
                                        $txt_px = $p->procedimiento->nombre;
                                    } else {
                                        $txt_px = $txt_px . '+' . $p->procedimiento->nombre;
                                    }
                                }
                                $agendas_proc[$value->id_agenda] = [$txt_px];
                                $pnombre  = implode(" ", $agendas_proc[$value->id_agenda]);
                            }
                        } else {
                            $pnombre = 'CONSULTA';
                        }
                        $cell->setValue($pnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('Q' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $motivo = '';
                        if ($value->proc_consul == '1') {
                            $motivo = $value->promotivo;
                        } else {
                            //$motivo = $value->evomotivo;
                        }
                        $cell->setValue($motivo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('R' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $hallazgo = '';
                        if ($value->proc_consul == '1') {
                            $hallazgo = $value->prohallazgo;
                            $hallazgo = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($hallazgo)), ENT_QUOTES, "UTF-8");
                        } else {
                            //$hallazgo = $value->examen_fisico;
                        }
                        $cell->setValue($hallazgo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('S' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $conclusion = '';
                        if ($value->proc_consul == '1') {
                            $conclusion = $value->proconclusion;
                            $conclusion = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($conclusion)), ENT_QUOTES, "UTF-8");
                        } else {
                            $conclusion = '';
                        }
                        $cell->setValue($conclusion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('T' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->presion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('U' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->temperatura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('V' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->peso);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('W' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->altura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('X' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $gct   = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        $texto = "";
                        if ($imc < 16) {
                            $texto = "Desnutrición";
                        } else if ($imc < 18) {
                            $texto = "Bajo de Peso";
                        } else if ($imc < 25) {
                            $texto = "Normal";
                        } else if ($imc < 27) {
                            $texto = "Sobrepeso";
                        } else if ($imc < 30) {
                            $texto = "Obesidad Tipo 1";
                        } else if ($imc < 40) {
                            $texto = "Obesidad Clinica";
                        } else {
                            $texto = "Obesidad Mordida";
                        }
                        $cell->setValue($texto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('Y' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $cell->setValue($imc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('Z' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }

                        if ($imc == 0) {
                            $gct = 0;
                        } else {
                            $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        }
                        $cell->setValue($gct);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AA' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        $cell->setValue($peso_ideal);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AB' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->perimetro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $receta  = '';
                    $receta2 = '';

                    if ($value->receta != null) {

                        $detalles = hc_receta_detalle::where('id_hc_receta', $value->receta)->get();

                        foreach ($detalles as $value2) {

                            $genericos = Medicina::where('id', $value2->id_medicina)->first()->genericos;

                            $receta  = $receta . ' - ' . $value2->medicina->nombre . ' (';
                            $receta2 = $receta2 . ' - ' . $value2->medicina->nombre . ': ' . $value2->dosis;
                            foreach ($genericos as $gen) {

                                $receta = $receta . $gen->generico->nombre . ' ';
                            }
                            $receta = $receta . ' ) ' . $value2->cantidad;
                        }
                    }

                    $sheet->cell('AC' . $i, function ($cell) use ($value, $receta) {
                        $rece_1 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($value->rp)), ENT_QUOTES, "UTF-8");
                        $cell->setValue($receta . ' ' . $rece_1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('AD' . $i, function ($cell) use ($value, $receta2) {
                        $rece_2 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($value->prescripcion)), ENT_QUOTES, "UTF-8");
                        $cell->setValue($receta2 . ' ' . $rece_2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('AE' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->d2apellido1 . ' ' . $value->d2nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('AF' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->d3apellido1 . ' ' . $value->d3nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //CONVENIO
                    $sheet->cell('AG' . $i, function ($cell) use ($value) {
                        $empresa  = Empresa::find($value->id_empresa);
                        $tempresa = '';
                        if (!is_null($empresa)) {
                            $tempresa = '/' . $empresa->nombre_corto;
                        }
                        if ($value->id_seguro_procedimiento == null) {
                            $cell->setValue($value->seguro . $tempresa);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $seguro_hc_procedimiento = Seguro::find($value->id_seguro_procedimiento);
                            $cell->setValue($seguro_hc_procedimiento->nombre . $tempresa);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });

                    $sheet->cell('AH' . $i, function ($cell) use ($value) {

                        $estado = '';
                        if ($value->estado_pentax == '0') {
                            $estado = 'EN ESPERA';
                        } elseif ($value->estado_pentax == '1') {
                            $estado = 'PREPARACION';
                        } elseif ($value->estado_pentax == '-1') {
                            $estado = 'PRE ADMISION';
                        } elseif ($value->estado_pentax == '2') {
                            $estado = 'EN PROCEDIMIENTO';
                        } elseif ($value->estado_pentax == '3') {
                            $estado = 'RECUPERACION';
                        } elseif ($value->estado_pentax == '4') {
                            $estado = 'ALTA';
                        } elseif ($value->estado_pentax == '5') {
                            $estado = 'SUSPENDIDO';
                        } else {
                            if ($value->proc_consul == '4') {
                                $estado = 'ALTA';
                            }
                        }

                        if ($estado == '') {
                            if ($value->proc_consul == '4') {
                                $estado = 'ALTA';
                            }
                        }

                        if ($value->estado_pentax < '4') {
                            if ($value->prohallazgo != null) {
                                $estado = 'ALTA';
                            }
                            if ($value->proconclusion != null) {
                                $estado = 'ALTA';
                            }
                            $cie10 = Hc_Cie10::where('hcid', $value->hcid)->first();
                            if (!is_null($cie10)) {
                                $estado = 'ALTA';
                            }
                        }


                        $cell->setValue($estado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $tr_arr = ['I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
                    if ($value->protocolo != null) {
                        $trainin_pro = Hc_protocolo_training::where('id_hc_protocolo', $value->protocolo)->get();
                        $tr_i        = 0;
                        foreach ($trainin_pro as $tr) {
                            $sheet->cell('A' . $tr_arr[$tr_i] . $i, function ($cell) use ($tr, $tr_i) {

                                $cell->setValue($tr->doctor->apellido1 . ' ' . $tr->doctor->nombre1);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });
                            $tr_i++;
                        }
                    }

                    $i = $i + 1;
                }

                foreach ($historiaclinica_con as $value) {

                    $sheet->cell('A' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->id_paciente);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('B' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->apellido2 != "(N/A)") {
                            $vapellido = $value->apellido1 . ' ' . $value->apellido2;
                        } else {
                            $vapellido = $value->apellido1;
                        }

                        $cell->setValue($vapellido);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('C' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        if ($value->nombre2 != "(N/A)") {
                            $vnombre = $value->nombre1 . ' ' . $value->nombre2;
                        } else {
                            $vnombre = $value->nombre1;
                        }

                        $cell->setValue($vnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('D' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->fecha_nacimiento);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('E' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->sexo == '2') {
                            $cell->setValue('FEMENINO');
                        } else {
                            $cell->setValue('MASCULINO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('F' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue($value->telefono1 . '/' . $value->telefono2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('G' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->observacion != null) {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('H' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue($value->observacion);

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $alergiasxpac = null;
                    $txt_ale      = '';
                    $alergiasxpac = Paciente_Alergia::where('id_paciente', $value->id_paciente)->get();
                    if (!is_null($alergiasxpac)) {

                        foreach ($alergiasxpac as $ale_pac) {
                            $txt_ale = $txt_ale . ' ' . $ale_pac->principio_activo->nombre;
                        }
                    }

                    $sheet->cell('I' . $i, function ($cell) use ($value, $txt_ale) {
                        // manipulate the cel

                        if ($txt_ale != '') {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('J' . $i, function ($cell) use ($value, $txt_ale) {
                        // manipulate the cel

                        $cell->setValue($txt_ale);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('K' . $i, function ($cell) use ($value) {
                        // manipulate the cel

                        $cell->setValue(substr($value->created_at, 0, 10));
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('L' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->est_amb_hos == '1') {
                            $cell->setValue('SI');
                        } else {
                            $cell->setValue('NO');
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('M' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        if ($value->espid == '8') {
                            if ($value->id_doctor_examinador == null) {
                                $cell->setValue($value->d1apellido1 . ' ' . $value->d1nombre1);
                            } else {
                                $doctor = User::find($value->id_doctor_examinador);
                                $cell->setValue($doctor->apellido1 . ' ' . $doctor->nombre1);
                            }
                        } else {

                            if ($value->id_empresa == '1307189140001') {
                                $cell->setValue('ROBLES CARLOS');
                            } else {
                                if ($value->id_doctor_examinador == null) {
                                    $cell->setValue($value->d1apellido1 . ' ' . $value->d1nombre1);
                                } else {
                                    $doctor = User::find($value->id_doctor_examinador);
                                    $cell->setValue($doctor->apellido1 . ' ' . $doctor->nombre1);
                                }
                            }
                        }

                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('N' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $cell->setValue(' ');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('O' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $tipo = 'PROCEDIMIENTO';
                        if ($value->proc_consul == '1') {
                            $tipo = $value->gpnombre;
                        } else {
                            $tipo = 'CONSULTA';
                        }
                        if ($tipo == '') {
                            $tipo = 'PROCEDIMIENTO';
                        }

                        $cell->setValue($tipo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('P' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $pnombre = 'NO INGRESADO';
                        if ($value->proc_consul == '1') {
                            $pentax = Pentax::where('id_agenda', $value->id_agenda)->first();
                            if (!is_null($pentax)) {
                                $txt_px = '';
                                foreach ($pentax->procedimientos as $p) {
                                    if ($txt_px == '') {
                                        $txt_px = $p->procedimiento->nombre;
                                    } else {
                                        $txt_px = $txt_px . '+' . $p->procedimiento->nombre;
                                    }
                                }
                                $agendas_proc[$value->id_agenda] = [$txt_px];
                                $pnombre  = implode(" ", $agendas_proc[$value->id_agenda]);
                            }
                        } else {
                            if ($value->espid == '8') {
                                $pnombre = 'CONSULTA CARDIOLÓGICA';
                            } else {
                                $pnombre = 'CONSULTA';
                            }
                        }
                        if ($pnombre == '') {
                            $pnombre = 'NO INGRESADO';
                        }
                        $cell->setValue($pnombre);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('Q' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $motivo = '';
                        if ($value->proc_consul == '1') {
                            $motivo = $value->promotivo;
                        } else {
                            $motivo = $value->evomotivo;
                        }
                        $cell->setValue($motivo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('R' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $hallazgo = '';
                        if ($value->proc_consul == '1') {
                            $hallazgo = $value->prohallazgo;
                            $hallazgo = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($hallazgo)), ENT_QUOTES, "UTF-8");
                        } else {
                            $hallazgo = $value->examen_fisico;
                        }
                        $cell->setValue($hallazgo);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('S' . $i, function ($cell) use ($value) {
                        // manipulate the cel
                        $conclusion = '';
                        if ($value->proc_consul == '1') {
                            $conclusion = $value->proconclusion;
                            $conclusion = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($conclusion)), ENT_QUOTES, "UTF-8");
                        } else {
                            $conclusion = '';
                        }
                        $cell->setValue($conclusion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('T' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->presion);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('U' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->temperatura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('V' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->peso);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('W' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->altura);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('X' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $gct   = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        $texto = "";
                        if ($imc < 16) {
                            $texto = "Desnutrición";
                        } else if ($imc < 18) {
                            $texto = "Bajo de Peso";
                        } else if ($imc < 25) {
                            $texto = "Normal";
                        } else if ($imc < 27) {
                            $texto = "Sobrepeso";
                        } else if ($imc < 30) {
                            $texto = "Obesidad Tipo 1";
                        } else if ($imc < 40) {
                            $texto = "Obesidad Clinica";
                        } else {
                            $texto = "Obesidad Mordida";
                        }
                        $cell->setValue($texto);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('Y' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $cell->setValue($imc);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('Z' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }

                        if ($imc == 0) {
                            $gct = 0;
                        } else {
                            $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        }
                        $cell->setValue($gct);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AA' . $i, function ($cell) use ($value) {
                        $peso     = $value->peso;
                        $estatura = $value->altura;

                        if ($value->sexo == 1) {
                            $sexo = $value->sexo;
                        } else {
                            $sexo = 0;
                        }
                        $edad      = Carbon::createFromDate(substr($value->fecha_nacimiento, 0, 4), substr($value->fecha_nacimiento, 5, 2), substr($value->fecha_nacimiento, 8, 2))->age;
                        $estatura2 = pow(($estatura / 100), 2);

                        $peso_ideal = 21.45 * ($estatura2);
                        if ($estatura2 == 0) {
                            $imc = 0;
                        } else {
                            $imc = $peso / $estatura2;
                        }
                        $gct = ((1.2 * $imc) + (0.23 * $edad) - (10.8 * $sexo) - 5.4);
                        $cell->setValue($peso_ideal);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cell('AB' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->perimetro);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $receta  = '';
                    $receta2 = '';

                    if ($value->receta != null) {

                        $detalles = hc_receta_detalle::where('id_hc_receta', $value->receta)->get();

                        foreach ($detalles as $value2) {

                            $genericos = Medicina::where('id', $value2->id_medicina)->first()->genericos;

                            $receta  = $receta . ' - ' . $value2->medicina->nombre . ' (';
                            $receta2 = $receta2 . ' - ' . $value2->medicina->nombre . ': ' . $value2->dosis;
                            foreach ($genericos as $gen) {

                                $receta = $receta . $gen->generico->nombre . ' ';
                            }
                            $receta = $receta . ' ) ' . $value2->cantidad;
                        }
                    }

                    $sheet->cell('AC' . $i, function ($cell) use ($value, $receta) {
                        $rece_1 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($value->rp)), ENT_QUOTES, "UTF-8");
                        $cell->setValue($receta . ' ' . $rece_1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('AD' . $i, function ($cell) use ($value, $receta2) {
                        $rece_2 = html_entity_decode(preg_replace('/&nbsp;/', ' ', strip_tags($value->prescripcion)), ENT_QUOTES, "UTF-8");
                        $cell->setValue($receta2 . ' ' . $rece_2);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('AE' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->d2apellido1 . ' ' . $value->d2nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->cell('AF' . $i, function ($cell) use ($value) {

                        $cell->setValue($value->d3apellido1 . ' ' . $value->d3nombre1);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    //convenio
                    $sheet->cell('AG' . $i, function ($cell) use ($value) {
                        $empresa  = Empresa::find($value->id_empresa);
                        $tempresa = '';
                        if (!is_null($empresa)) {
                            $tempresa = '/' . $empresa->nombre_corto;
                        }
                        if ($value->id_seguro_procedimiento == null) {
                            $cell->setValue($value->seguro . $tempresa);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        } else {
                            $seguro_hc_procedimiento = Seguro::find($value->id_seguro_procedimiento);
                            $cell->setValue($seguro_hc_procedimiento->nombre . $tempresa);
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        }
                    });

                    $sheet->cell('AH' . $i, function ($cell) use ($value) {

                        $estado = '';
                        if ($value->estado_cita == '4') {
                            $estado = 'ATENDIDO';
                        } elseif ($value->estado_cita == '3') {
                            $estado = 'SUSPENDIDO';
                        } elseif ($value->estado_cita == '-1') {
                            $estado = 'NO ASISTE';
                        } else {
                            if ($value->proc_consul == '4') {
                                $estado = 'ATENDIDO';
                            }
                        }

                        $cell->setValue($estado);
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $i = $i + 1;
                }
            });
            //$excel->getActiveSheet()->setAutosize(false);
            //$excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(5);
            $excel->getActiveSheet()->getColumnDimension("R")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("Q")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("S")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AC")->setWidth(15)->setAutosize(false);
            $excel->getActiveSheet()->getColumnDimension("AD")->setWidth(15)->setAutosize(false);
            //dd($excel->getActiveSheet());
        })->export('xlsx');
    }

    public function planilla_iess()
    {
        return view('hc_admision/planilla/planilla_iess');
    }
}
