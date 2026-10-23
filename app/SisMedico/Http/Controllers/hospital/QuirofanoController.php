<?php

namespace Sis_medico\Http\Controllers\hospital;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Pentax;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Especialidad;
use Sis_medico\Seguro;
use Sis_medico\User;
use Sis_medico\Agenda;
use Sis_medico\Paciente;
use Sis_medico\Orden;
use Sis_medico\Historiaclinica;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\Procedimiento;
use Sis_medico\procedimiento_completo;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_child_pugh;
use Sis_medico\Examen_Orden;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Principio_Activo;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Epicrisis;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion_Indicacion;
use Sis_medico\Ho_Evoluciones_Enfermeria;

class QuirofanoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function quirofano($tipo)
    {   
        //tipo 1 cirugia 0 imagenes
        $id_usuario = Auth::user()->id;

        $especialidades = Especialidad::where('estado', '1')->get();
        $seguros        = Seguro::where('inactivo', '1')->get();
        $doctores       = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderby('apellido1')->get();

        $fecha_desde = date('Y-m-d');
        $fecha_hasta = date('Y-m-d');

        $agendas_pac = [];
      

        if ($tipo == 1) {
            $agendas_pac = Agenda::where('agenda.estado', '1')
                ->whereBetween('agenda.fechaini', [$fecha_desde . ' 00:00', $fecha_hasta . ' 23:59'])
                ->join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
                ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                ->join('paciente as p', 'p.id', 'agenda.id_paciente')
                ->join('users as d', 'd.id', 'h.id_doctor1')
                ->join('empresa as em', 'em.id', 'agenda.id_empresa')
                ->join('seguros as se', 'se.id', 'h.id_seguro')
                ->where('agenda.espid', '<>', '10')
                ->where('agenda.proc_consul', '1')
                ->where('agenda.ho_tipo', '1')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'agenda.fechaini', 'agenda.fechafin', 'agenda.proc_consul', 'agenda.id as id_agenda', 'agenda.cortesia', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'se.nombre as seguro_nombre', 'em.nombre_corto as empresa_nombre', 'h.hcid', 'agenda.omni', 'agenda.estado_cita', 'agenda.tc', 'agenda.teleconsulta')->Orderby('agenda.fechaini', 'asc')->get();
        } else {
            $agendas_pac = Agenda::where('agenda.estado', '1')
                ->whereBetween('agenda.fechaini', [$fecha_desde . ' 00:00', $fecha_hasta . ' 23:59'])
                ->join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
                ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                ->join('paciente as p', 'p.id', 'agenda.id_paciente')
                ->join('users as d', 'd.id', 'h.id_doctor1')
                ->join('empresa as em', 'em.id', 'agenda.id_empresa')
                ->join('seguros as se', 'se.id', 'h.id_seguro')
                ->where('agenda.espid', '<>', '10')
                ->where('agenda.proc_consul', '1')
                ->where('agenda.ho_tipo', '0')
                ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'agenda.fechaini', 'agenda.fechafin', 'agenda.proc_consul', 'agenda.id as id_agenda', 'agenda.cortesia', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'se.nombre as seguro_nombre', 'em.nombre_corto as empresa_nombre', 'h.hcid', 'agenda.omni', 'agenda.estado_cita', 'agenda.tc', 'agenda.teleconsulta')->Orderby('agenda.fechaini', 'asc')->get();
        }
        
        $agendas_proc = null;
        foreach ($agendas_pac as $pac) {
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
                    //dd($agendas_proc[$pac->id_agenda]);
                }
            }
        }
    
        return view('hospital/quirofano/quirofano', ['agendas_pac' => $agendas_pac, 'agendas_proc' => $agendas_proc, 'especialidades' => $especialidades, 'seguros' => $seguros, 'doctores' => $doctores, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'tipo' => $tipo]);
    }

    public function buscar_quirofano($tipo, Request $request )
    {
        
        //tipo 1: cirugia, 0: imagenes
        $fecha_desde = $request['fecha_desde'];
        $fecha_hasta = $request['fecha_hasta'];
        $nombres     = $request['nombres'];
        $apellidos   = $request['apellidos'];
        $id_doctor1  = $request['id_doctor1'];
        $id_seguro   = $request['id_seguro'];
        $espid       = $request['espid'];

        $especialidades = Especialidad::where('estado', '1')->get();
        $seguros        = Seguro::where('inactivo', '1')->get();
        $doctores       = User::where('id_tipo_usuario', 3)->where('estado', 1)->orderby('apellido1')->get();


        $agendas_pac = Agenda::where('agenda.estado', '1')
            ->join('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('paciente as p', 'p.id', 'agenda.id_paciente')
            ->join('users as d', 'd.id', 'h.id_doctor1')
            ->join('empresa as em', 'em.id', 'agenda.id_empresa')
            ->join('seguros as se', 'se.id', 'h.id_seguro')
            ->where('agenda.espid', '<>', '10')
            ->where('agenda.proc_consul', '1')
            ->select('p.id', 'p.nombre1', 'p.nombre2', 'p.apellido1', 'p.apellido2', 'p.fecha_nacimiento', 'agenda.fechaini', 'agenda.fechafin', 'agenda.proc_consul', 'agenda.id as id_agenda', 'agenda.cortesia', 'd.nombre1 as dnombre1', 'd.apellido1 as dapellido1', 'se.nombre as seguro_nombre', 'em.nombre_corto as empresa_nombre', 'h.hcid', 'agenda.omni', 'agenda.estado_cita', 'agenda.tc', 'agenda.teleconsulta');
        if ($tipo == 1) {
            $agendas_pac = $agendas_pac->where('hc_proto.tipo_procedimiento', '!=', '2');
        } else {
            $agendas_pac = $agendas_pac->where('hc_proto.tipo_procedimiento', '2');
        }
        if ($fecha_desde != null && $fecha_hasta != null) {

            $agendas_pac = $agendas_pac->whereBetween('agenda.fechaini', [$fecha_desde . ' 00:00', $fecha_hasta . ' 23:59']);
        }

        if ($id_seguro != null) {
            $agendas_pac = $agendas_pac->where('agenda.id_seguro', $id_seguro);
        }

        if ($id_doctor1 != null) {
            $agendas_pac = $agendas_pac->where('agenda.id_doctor1', $id_doctor1);
        }

        $agendas_pac = $agendas_pac->orderby('agenda.fechaini', 'asc')->get();
        $agendas_proc = null;
        foreach ($agendas_pac as $pac) {
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
                    //dd($agendas_proc[$pac->id_agenda]);
                }
            }
        }

        //dd($agendas_pac);

        return view('hospital/quirofano/quirofano', ['agendas_pac' => $agendas_pac, 'agendas_proc' => $agendas_proc, 'especialidades' => $especialidades, 'seguros' => $seguros, 'doctores' => $doctores, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'tipo' => $tipo]);
    }

    public function quirofano_paciente($tipo, $id_solicitud)
    {
        //tipo 1: cirugia, 0: imagenes

        $solicitud = Ho_Solicitud::find($id_solicitud);
        $pro_final = [];
        $edad = 0;
        $alergias = [];
        $evolucion = [];
        $historia = [];
        if(!is_null($solicitud)){
            $historia = $solicitud->agenda->historia_clinica;
            $evolucion = $historia->evoluciones->last();
            $paciente = Paciente::find($solicitud->id_paciente);
           
            if ($paciente->fecha_nacimiento != null) {
                $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
            }
    
            $alergias = $solicitud->paciente->a_alergias;
            $txt_al = '';
            $cont = 0;
            foreach ($alergias as $alergia) {
                if ($cont == 0) {
                    $txt_al = $alergia->principio_activo->nombre;
                } else {
                    $txt_al = $txt_al . ' + ' . $alergia->principio_activo->nombre;
                }
                $cont++;
            }
            $log = $solicitud->log->last();
            $pro_final = Historiaclinica::where('historiaclinica.hcid', $historia->hcid)
                ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
                ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                ->where('hc_p.estado', '1')
                ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'historiaclinica.created_at', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'hc_proto.fecha as fecha', 'hc_proto.created_at as created_at_proto', 'hc_p.id_doctor_examinador2', 'hc_proto.fecha_operacion as f_operacion', 'historiaclinica.id_agenda as id_agenda', 'hc_p.id_doctor_responsable')
                ->OrderBy('hc_proto.created_at', 'desc')->first();

            $notas = Hc_Evolucion_Indicacion::where('id_hc', $historia->hcid)->latest()->first();
            $evolucion_enfermeria = Ho_Evoluciones_Enfermeria::where('hcid', $historia->hcid)->latest()->first();

            $epicrisis = Hc_Epicrisis::where('hcid', $historia->hcid)->get()->last();
        }
       
        //nuevo cambio
        $sql = "SELECT sol.fecha_ingreso, a.id_paciente,p.religion,TIMESTAMPDIFF(DAY,sol.fecha_ingreso, CURDATE()) as dias_hospitalizados,
        CONCAT(p.apellido1, ' ', p.apellido2, ' ', p.nombre1, ' ', p.nombre2) as nombre_paciente,seg.nombre as seguro, 
        CONCAT(u.apellido1, ' ', u.apellido2, ' ', u.nombre1, ' ', u.nombre2) as nombre_doctor,
        p.fecha_nacimiento, YEAR(CURDATE())-YEAR(p.fecha_nacimiento) + IF(DATE_FORMAT(CURDATE(),'%m-%d') > DATE_FORMAT(p.fecha_nacimiento,'%m-%d'), 0 , -1 ) as edad, 
        if(p.antecedentes_pat is null,'No tiene antecedentes patologicos',p.antecedentes_pat) as ante_patologicos, 
        if(p.antecedentes_fam is null,'No tiene antecedentes familiares',p.antecedentes_fam) as ante_familiares, 
        if(p.antecedentes_quir is null,'No tiene antecedentes quirurgicos',p.antecedentes_quir) as ante_quirurgicos from ho_solicitud sol
        join agenda a on a.id = sol.id_agenda
        join paciente p on p.id = a.id_paciente 
        join users u on u.id = a.id_doctor1
        join seguros seg on seg.id = a.id_seguro and sol.id = $id_solicitud";
        $data = DB::select($sql);

        $paciente = Paciente::find($solicitud->id_paciente);
        $alergiasDB = Paciente_Alergia::where('id_paciente',$paciente->id)
        ->join('principio_activo as pa','pa.id','paciente_alergia.id_principio_activo')
        ->where('paciente_alergia.id_paciente', $paciente->id)->select('pa.nombre')->get();
        $str_alergias = '';
        $separador = '';
        if(count($alergiasDB)==1){
            $separador = '';
        }
        else{
            $separador = ', ';
        }
        foreach ($alergiasDB as  $alergia) {
            $str_alergias .= $alergia->nombre.$separador;
        }
        $id_paciente = $paciente->id;
        $arr_listado = [];
        $examen = Examen_Orden::where('id_paciente', $id_paciente)->where('estado',1)->select('fecha_orden','id')->first();
        if(!is_null($examen)){
            $arr_listado[0] = [$examen->fecha_orden,'LABORATORIO',$examen->id];
        }
        $procedimiento = Agenda::join('historiaclinica as hc','hc.id_agenda','=','agenda.id')->join('hc_protocolo as proto', 'proto.hcid','=','hc.hcid')->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc','=','hc.hcid')->join('hc_procedimiento_final as hc_procf','hc_procf.id_hc_procedimientos','=','hc_proc.id')->join('procedimiento as proc','proc.id','=','hc_procf.id_procedimiento')->where('agenda.id_paciente', $id_paciente)->where('agenda.estado',1)->where('agenda.proc_consul',1)->wherenotNull('proto.hallazgos')->wherenotNull('proc.id_grupo_procedimiento')->select('agenda.id as id_agenda','agenda.fechaini','proto.id_hc_procedimientos','proc.nombre', 'proto.id as id_protocolo')->first();
        if(!is_null($procedimiento)){
            $arr_listado[1] = [$procedimiento->fechaini,$procedimiento->nombre, $procedimiento->id_protocolo];
        }
        $consulta = Agenda::join('historiaclinica as hc','hc.id_agenda','=','agenda.id')->join('hc_evolucion as evo','evo.hcid','=','hc.hcid')->join('especialidad as esp','esp.id','=','agenda.espid')->where('agenda.id_paciente', $id_paciente)->where('agenda.estado',1)->where('agenda.proc_consul',0)->where('evo.secuencia','=','0')->wherenotNull('evo.cuadro_clinico')->orderby('agenda.fechaini','desc')->select('agenda.id as id_agenda', 'agenda.fechaini', 'evo.id as id_hc_evolucion','esp.nombre')->first();
        if(!is_null($consulta)){
            $arr_listado[2] = [$consulta->fechaini,$consulta->nombre, $consulta->id_hc_evolucion];
        }
        return view('hospital/quirofano/quirofano_paciente', ['arr_listado'=>$arr_listado,'solicitud' => $solicitud, 'edad' => $edad, 'alergias' => $alergias, 'tipo' => $tipo, 'evolucion' => $evolucion, 'historia' => $historia, 'pro_final' => $pro_final, 'notas' => $notas, 'evolucion_enfermeria' => $evolucion_enfermeria, 'log' => $log, 'data' => $data[0], 'str_alergias' => $str_alergias, 'epicrisis' => $epicrisis]);
    }

    public function index_funcionales($id_solicitud)
    {

        $solicitud = Ho_Solicitud::find($id_solicitud);

        $historia = $solicitud->agenda->historia_clinica;
        //dd($historia);

        /*$pro_completo_1 = Historiaclinica::where('historiaclinica.hcid',$historia->hcid)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where('gp.tipo_procedimiento', '1')
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'historiaclinica.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'historiaclinica.id_agenda as id_agenda','hc_p.id_seguro as hc_p_id_seguro')
            ->OrderBy('historiaclinica.created_at', 'desc')
            ->get();*/


        $pro_final1 = Historiaclinica::where('historiaclinica.hcid', $historia->hcid)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            //->where('hc_proto.tipo_procedimiento', '1')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'historiaclinica.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'historiaclinica.id_agenda as id_agenda', 'hc_p.id_seguro as hc_p_id_seguro')->OrderBy('historiaclinica.created_at', 'desc')->get();

        //dd($pro_final1);


        return view('hospital.quirofano.index_funcionales', ['procedimientos1' => $pro_final1, 'historia' => $historia, 'solicitud' => $solicitud]);
    }

    public function crear_funcionales($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id;
        $fecha_orden = Date('Y-m-d H:i:s');
        $anio = date('Y');
        $mes = date('m');

        $orden_funcional_crear_new = [

            'anterior'          => 'ORDEN_PROC_FUNCIONAL -> El Dr. creo nueva orden de procedimiento funcional',
            'nuevo'             => 'ORDEN_PROC_FUNCIONAL -> El Dr. creo nueva orden de procedimiento funcional',
            'id_paciente'       => $id_paciente,
            'id_usuariocrea'    => $id_doctor,
            'id_usuariomod'     => $id_doctor,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,

        ];

        Hc_Log::create($orden_funcional_crear_new);

        $input_orden = [

            'id_paciente'       => $id_paciente,
            'id_doctor'         => $id_doctor,
            'id_evolucion'      => '',
            'motivo_consulta'   => '',
            'resumen_clinico'   => '',
            'diagnosticos'      => '',
            'fecha_orden'       => $fecha_orden,
            'tipo_procedimiento' => '1',
            'anio'              => $anio,
            'mes'               => $mes,
            'id_usuariocrea'    => $id_doctor,
            'id_usuariomod'     => $id_doctor,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
        ];

        $id_orden = Orden::insertGetId($input_orden);
    }


    public function armar_estudio($id_solicitud)
    {

        $solicitud = Ho_Solicitud::find($id_solicitud);

        $historia = $solicitud->agenda->historia_clinica;
        $pro_completo_0 = [];
        $pro_final_0 = Historiaclinica::where('historiaclinica.hcid', $historia->hcid)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where('hc_p.estado', '1')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'historiaclinica.created_at', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'hc_proto.fecha as fecha', 'hc_proto.created_at as created_at_proto', 'hc_p.id_doctor_examinador2', 'hc_proto.fecha_operacion as f_operacion', 'historiaclinica.id_agenda as id_agenda', 'hc_p.id_doctor_responsable')
            ->OrderBy('hc_proto.created_at', 'desc')->get();

        $doctores = User::where('id_tipo_usuario', 3)->get();

        return view('hospital.quirofano.armar_estudio', ['solicitud' => $solicitud, 'procedimientos1' => $pro_completo_0, 'procedimientos2' => $pro_final_0, 'doctores' => $doctores]);
    }

    public function ver_estudio($id_solicitud)
    {

        $solicitud = Ho_Solicitud::find($id_solicitud);
        $historia = $solicitud->agenda->historia_clinica;

        $pro_completo_0 = [];



        $pro_final_0 = Historiaclinica::where('historiaclinica.hcid', $historia->hcid)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')

            /*->where(function ($query) {
                $query->where('hc_proto.tipo_procedimiento', '0')
                    ->orwhere('hc_proto.tipo_procedimiento', '2');
            })*/
            ->where('hc_p.estado', '1')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'historiaclinica.created_at', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'hc_proto.fecha as fecha', 'hc_proto.created_at as created_at_proto', 'hc_p.id_doctor_examinador2', 'hc_proto.fecha_operacion as f_operacion', 'historiaclinica.id_agenda as id_agenda')
            ->OrderBy('hc_proto.created_at', 'desc')->get();


        return view('hospital/quirofano/ver_estudios', ['solicitud' => $solicitud, 'procedimientos1' => $pro_completo_0, 'procedimientos2' => $pro_final_0]);
    }

    public function ecografia($id_solicitud)
    {

        $solicitud = Ho_Solicitud::find($id_solicitud);
        $historia = $solicitud->agenda->historia_clinica;

        $paciente = $solicitud->paciente;
        //dd($paciente);

        $pro_completo_0 = Historiaclinica::where('historiaclinica.hcid', $historia->hcid)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where('gp.tipo_procedimiento', '2')
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'historiaclinica.hcid', 'hc_proto.fecha_operacion as f_operacion', 'historiaclinica.id_agenda as id_agenda', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'historiaclinica.hcid as id_hc', 'hc_p.id_seguro as seguro_final')
            ->OrderBy('a.fechaini', 'desc')->get();

        //dd($pro_completo_0);

        $pro_final_0 = Historiaclinica::where('historiaclinica.hcid', $historia->hcid)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'historiaclinica.hcid')
            ->join('agenda as a', 'a.id', 'historiaclinica.id_agenda')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where('hc_proto.tipo_procedimiento', '2')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'historiaclinica.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'historiaclinica.id_agenda as id_agenda', 'historiaclinica.id_seguro as hc_id_seguro', 'hc_p.id_seguro as seguro_final')->OrderBy('a.fechaini', 'desc')->get();

           // dd($pro_final_0);

        return view('hospital/quirofano/ecografia/ecografia', ['solicitud' => $solicitud, 'procedimientos1' => $pro_completo_0, 'procedimientos2' => $pro_final_0, 'paciente' => $paciente]);
    }

    public function editar($id_procedimiento, $id_paciente)
    {
        
        //dd($proto_hcid);
        $protocolo = hc_protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
        //dd($protocolo->hcid);
        $id_seguro_pro = hc_procedimientos::where('id', $id_procedimiento)->first();
        //dd($id_seguro_pro);
        $hc_seguro = Seguro::where('id', $id_seguro_pro->id_seguro)->first();
        $px = Procedimiento::where('procedimiento.estado', '1')->get();
        //dd($hc_seguro->nombre);

        $procedimiento = hc_procedimientos::find($id_procedimiento);
        $tipo = 2;
        $doctores = User::where('id_tipo_usuario', '=', 3)->where('estado', '1')->get();

        $proc_completo = procedimiento_completo::all();
        //dd($protocolo);

        return view('hospital/quirofano/ecografia/editar', ['protocolo' => $protocolo, 'procedimiento' => $procedimiento, 'tipo' => $tipo, 'id_paciente' => $id_paciente, 'hc_seguro' => $hc_seguro, 'px' => $px, 'doctores' => $doctores, 'proc_completo' => $proc_completo, 'procedimiento_completo_plantilla' => null]);
    }

    public function editar_evolucion($id_procedimiento, $id_paciente)
    {
        $evolucion = hc_evolucion::find($id_procedimiento);

        return view('hospital/quirofano/ecografia/editar_evolucion', ['evolucion' => $evolucion, 'id_paciente' => $id_paciente]);
    }

    public function agregar_evolucion($id_procedimiento)
    {

        $procedimiento = hc_procedimientos::find($id_procedimiento);
        $id_historia   = $procedimiento->id_hc;
        $ip_cliente    = $_SERVER["REMOTE_ADDR"];
        $idusuario     = Auth::user()->id;
        $evolucion     = hc_evolucion::where('hc_id_procedimiento', $id_procedimiento)->OrderBy('id', 'Desc')->first();
        if (!is_null($evolucion)) {
            $secuencia = $evolucion->secuencia + 1;
        } else {
            $secuencia = 0;
        }
        $input_hc_evolucion_pos = [
            'hc_id_procedimiento' => $id_procedimiento,
            'hcid'                => $id_historia,
            'secuencia'           => $secuencia,
            'motivo'              => ' ',
            'ip_modificacion'     => $ip_cliente,
            'fecha_ingreso'       => ' ',
            'id_usuariocrea'      => $idusuario,
            'id_usuariomod'       => $idusuario,
            'ip_creacion'         => $ip_cliente,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ];
        $id_evolucion = hc_evolucion::insertGetId($input_hc_evolucion_pos);
        $evolucion    = hc_evolucion::find($id_evolucion);
        return view('hospital/quirofano/agregar_evolucion', ['evolucion' => $evolucion]);
    }


    public function editar_funcional($id_procedimiento, $id_paciente)
    {
        $protocolo = hc_protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();

        $hc_historia_clinica = Historiaclinica::where('id_paciente', $id_paciente)->OrderBy('created_at', 'desc')->first();
        $procedimiento = hc_procedimientos::find($id_procedimiento);
        $hc_seguro = Seguro::where('id', $procedimiento->id_seguro)->OrderBy('created_at', 'desc')->first();
        $tipo = 1;
        $px = Procedimiento::where('procedimiento.estado', '1')->get();
        $doctores = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->get();
        return view('hospital/quirofano/cirugia/editar_funcional', ['protocolo' => $protocolo, 'procedimiento' => $procedimiento, 'tipo' => $tipo, 'id_paciente' => $id_paciente, 'hc_seguro' => $hc_seguro, 'px' => $px, 'doctores' => $doctores]);
    }

    public function index_epicrisis($id_solicitud)
    {

        $solicitud = Ho_solicitud::find($id_solicitud);
        $paciente = Paciente::find($solicitud->id_paciente);

        $log = $solicitud->log->last();

        $historia = $solicitud->agenda->historia_clinica;
        $evolucion = $historia->evoluciones->last();

        $child_pugh = $evolucion->child_pug;
        $examenes = Examen_Orden::where('id_paciente', $solicitud->id_paciente)->latest('created_at')->first();
        return view('hospital/quirofano/epicrisis/index_epi', ['examenes' => $examenes, 'paciente' => $paciente, 'solicitud' => $solicitud, 'log' => $log, 'evolucion' => $evolucion, 'child_pugh' => $child_pugh]);
    }
      public function epicrisis($id)
    {

        $solicitud = Ho_Solicitud::where('ho_solicitud.id', $id)
            ->join('agenda as ag', 'ag.id', 'ho_solicitud.id_agenda')
            ->join('historiaclinica as h', 'h.id_agenda', 'ag.id')
            ->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', 'h.hcid')
            ->select('ag.id as id_agenda', 'h.hcid', 'hc_proc.id as id_hcproc', 'ho_solicitud.id_paciente', 'ho_solicitud.id')
            ->first();
        //dd($solicitud);
        $epicrisis = Hc_Epicrisis::where('hc_id_procedimiento', $solicitud->id_hcproc)->Orderby('created_at', 'DESC')->get();

        return view('hospital.quirofano.epicrisis.ver_epicrisis', ['epicrisis' => $epicrisis,'solicitud' => $solicitud]);
    }
    public function guardar_epicrisis(Request $request, $id_epi)
    {

        //dd($request->all());

        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;

        $id_solicitud = $request['solicitud_id'];
        $solicitud = Ho_solicitud::find($id_solicitud);
        $epicrisis = Hc_Epicrisis::find($id_epi);


        if (!is_null($epicrisis)) {
            $arr_epicrisis = [
               // 'motivo'            => $request['motivo'],
                'cuadro_clinico'    => $request['n_epicrisis'],
                'favorable_des'     => $request['favorable_des'],
                'complicacion'      => $request['complicacion'],
                'hallazgo'          => $request['hallazgo'],
                'id_usuariomod'     => $idusuario,
                'ip_modificacion'   => $ip_cliente,
            ];

            $epicrisis->update($arr_epicrisis);

         
        }

        return view('hospital.quirofano.epicrisis.nueva_epicrisis', ['epicrisis' => $epicrisis,'solicitud' => $solicitud]);
    }
    public function epicrisis_detalle($id, Request $request)
    {
        //dd($request->all());
        $id_solicitud = $request['id_solicitud'];
        $epicrisis = Hc_Epicrisis::find($id);
      
        $solicitud = Ho_Solicitud::where('ho_solicitud.id', $id_solicitud)
            ->join('agenda as ag', 'ag.id', 'ho_solicitud.id_agenda')
            ->join('historiaclinica as h', 'h.id_agenda', 'ag.id')
            ->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', 'h.hcid')
            ->select('ag.id as id_agenda', 'h.hcid', 'hc_proc.id as id_hcproc', 'ho_solicitud.id_paciente', 'ho_solicitud.id')
            ->first();


        return view('hospital.quirofano.epicrisis.nueva_epicrisis', ['epicrisis' => $epicrisis, 'solicitud' => $solicitud]);
    }

  
    public function crear_epicrisis($id)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
            //$epicrisis = Hc_Epicrisis::where('hc_id_procedimiento', $proc)->first();
            $solicitud = Ho_Solicitud::where('ho_solicitud.id', $id)
            ->join('agenda as ag', 'ag.id', 'ho_solicitud.id_agenda')
            ->join('historiaclinica as h', 'h.id_agenda', 'ag.id')
            ->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', 'h.hcid')
            ->select('ag.id as id_agenda', 'h.hcid', 'hc_proc.id as id_hcproc', 'ho_solicitud.id_paciente', 'ho_solicitud.id')
            ->first();

            $arr_epicrisis = [
                    'hc_id_procedimiento' => $solicitud->id_hcproc,
                    'hcid'                => $solicitud->hcid,
                    'alta'                => $solicitud->alta,
                    'cuadro_clinico'      => '',
                   // 'fecha_ingreso'       => ' ',
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariocrea'      => $idusuario,
                    'id_usuariomod'       => $idusuario,
                ];
                $epicrisis_a = Hc_Epicrisis::insertGetId($arr_epicrisis);
     
      

        $epicrisis = Hc_Epicrisis::where('hc_id_procedimiento', $solicitud->id_hcproc)->Orderby('created_at', 'DESC')->get();
       // return view('hospital/quirofano/epicrisis/nueva_epicrisis', ['solicitud' => $solicitud, 'epicrisis' => $epicrisis, 'epicrisis_a' => $epicrisis_a]);
       //return redirect("hospital/quirofano/epicrisis/{$id}");
       return ['id' => $epicrisis_a];

    }
  
    public function editar_epicrisis($id_procedimiento, $id_paciente)
    {
            // dd('dgdfgf');
            $solicitud = Ho_Solicitud::where('ho_solicitud.id', $id)
            ->join('agenda as ag', 'ag.id', 'ho_solicitud.id_agenda')
            ->join('historiaclinica as h', 'h.id_agenda', 'ag.id')
            ->join('hc_procedimientos as hc_proc', 'hc_proc.id_hc', 'h.hcid')
            ->select('ag.id as id_agenda', 'h.hcid', 'hc_proc.id as id_hcproc', 'ho_solicitud.id_paciente', 'ho_solicitud.id')
            ->first();
        $evoluciones = Hc_Evolucion::where('hc_id_procedimiento', $solicitud->id_hcproc)->Orderby('created_at','DESC')->get();
        //dd($proto_hcid);
        $protocolo = hc_protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
        //dd($protocolo->hcid);
        $id_seguro_pro = hc_procedimientos::where('id', $id_procedimiento)->first();
        //dd($id_seguro_pro);
        $hc_seguro = Seguro::where('id', $id_seguro_pro->id_seguro)->first();
        $px = Procedimiento::where('procedimiento.estado', '1')->get();
        //dd($hc_seguro->nombre);

        $procedimiento = hc_procedimientos::find($id_procedimiento);
        $tipo = 2;
        $doctores = User::where('id_tipo_usuario', '=', 3)->where('estado', '1')->get();

        $proc_completo = procedimiento_completo::all();

        return view('hospital/quirofano/epicrisis/nueva_epicrisis', ['protocolo' => $protocolo, 'procedimiento' => $procedimiento, 'tipo' => $tipo, 'id_paciente' => $id_paciente, 'hc_seguro' => $hc_seguro, 'px' => $px, 'doctores' => $doctores, 'proc_completo' => $proc_completo, 'procedimiento_completo_plantilla' => null, 'evoluciones'=>$evoluciones]);
    }

    public function guardar_alergia(Request $request) 

    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $id = $request["id_paciente"];
        //return $request->all();
        $paciente = Paciente::find($id);

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id)->get();

        $todo = null;
        if (!is_null($alergiasxpac)) {
            foreach ($alergiasxpac as $alergias) {
                $nombre_alergia = Principio_Activo::find($alergias->id_principio_activo);
                $todo .= $nombre_alergia->nombre . ', ';
            }
        }

        $todo_nuevo = null;
        //dd($request["ale_list"]);
        if (!is_null($request["ale_list"])) {
            foreach ($request["ale_list"] as $alergias_nuevas) {
                //$nombre_alergia_nueva = Principio_Activo::find($alergias_nuevas);
                $todo_nuevo .= $alergias_nuevas . ', ';
            }
        }

        if ($todo != null && $todo_nuevo != null) {
            $alergias_new = [
                'anterior'        => 'DATOS_PRINCIPALES-> Alergia: ' . $todo,
                'nuevo'           => 'DATOS_PRINCIPALES-> Alergia: ' . $todo_nuevo,
                'id_paciente'     => $paciente->id,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];
            Hc_Log::create($alergias_new);
        }

        foreach ($alergiasxpac as $apac) {
            $apac->delete();
        }

        $alergia_txt = "";
        $ale_flag    = true;
        //return $request->ale_list;
        if ($request->ale_list != null) {
            foreach ($request->ale_list as $ale) {
                if (is_numeric($ale)) {
                    $generico = Principio_Activo::find($ale);
                } else {
                    $generico = Principio_Activo::where('nombre', 'like', substr(strtoupper($ale), 0, -5))->first();
                    if (is_null($generico)) {
                        $input_principio = [
                            'nombre'          => substr(strtoupper($ale), 0, -5),
                            'descripcion'     => substr(strtoupper($ale), 0, -5),
                            'estado'          => '1',
                            'ip_modificacion' => $ip_cliente,
                            'id_usuariomod'   => $idusuario,
                            'ip_creacion'     => $ip_cliente,
                            'id_usuariocrea'  => $idusuario,
                        ];
                        $ale      = Principio_Activo::insertGetId($input_principio);
                        $generico = Principio_Activo::find($ale);
                    } else {
                        $ale = $generico->id;
                    }
                }
                $generico = Principio_Activo::find($ale);
                $pac_ale  = [

                    'id_paciente'         => $id,
                    'id_principio_activo' => $ale,
                    'ip_creacion'         => $ip_cliente,
                    'ip_modificacion'     => $ip_cliente,
                    'id_usuariomod'       => $idusuario,
                    'id_usuariocrea'      => $idusuario,

                ];
                Paciente_Alergia::create($pac_ale);
                if ($ale_flag) {
                    $alergia_txt = $generico->nombre;
                    $ale_flag    = false;
                } else {
                    $alergia_txt = $alergia_txt . '+' . $generico->nombre;
                }
            }
        }

        return "exito";
    }
}
