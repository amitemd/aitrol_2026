<?php

namespace Sis_medico\Http\Controllers\hc4;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Cortesia_paciente;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Principio_Activo;
use Sis_medico\Paciente_Observaciones;
use Sis_medico\Hc_informe_biopsia;
use Sis_medico\User;
use Sis_medico\Procedimiento;

class HistoriaPacienteController extends Controller
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
    private function rol_new($opcion)
    {
        //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO
        //dd($opcion);
        $rolUsuario     = Auth::user()->id_tipo_usuario;
        $opcion_usuario = Opcion_Usuario::where('id_opcion', $opcion)->where('id_tipo_usuario', $rolUsuario)->first();
        if (is_null($opcion_usuario)) {
            return true;

        }

    }

    public function historia_paciente_index($id_paciente)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $paciente_observaciones = DB::table('paciente_observaciones')->where('id_paciente', $id_paciente)->first();

        $paciente = Paciente::find($id_paciente);
        if (is_null($paciente)) {
            return redirect('/');
        }
        $edad = 0;
        if ($paciente->fecha_nacimiento != null) {
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }

        //dd($paciente);

        $cortesia_paciente = Cortesia_Paciente::where('id', $id_paciente)->get()->last();
        //dd($cortesia_paciente);
        $evoluciones = DB::table('hc_evolucion as e')->join('historiaclinica as h', 'e.hcid', 'h.hcid')->where('h.id_paciente', $id_paciente)->join('agenda as a', 'a.id', 'h.id_agenda')->orderBy('a.fechaini', 'desc')->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid')->orderBy('e.id', 'desc')->get()->first(); /// aquiiii

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id_paciente)->get();

        $procedimientos_observaciones = null;
        $especialidad                 = null;
        if (!is_null($evoluciones)) {
            $procedimientos_observaciones = hc_procedimientos::find($evoluciones->hc_id_procedimiento);

            $especialidad = DB::table('especialidad')->find($evoluciones->espid);
        }

        $hc_rec    = null;
        $protocolo = null;
        $estudios  = null;

        if (!is_null($paciente->historia_clinica()->get()->last())) {
            $id_hc = $paciente->historia_clinica()->get()->last()->hcid;

            //$hc_rec = hc_receta::where('id_hc', $id_hc)->get()->first(); //aquiiii

            $hc_rec = DB::table('hc_receta as r')
                ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
                ->where('h.id_paciente', $id_paciente)
                ->where('r.prescripcion', '!=', null)
                ->join('agenda as a', 'a.id', 'h.id_agenda')
                ->orderBy('a.fechaini', 'desc')
                ->select('r.*', 'a.fechaini')
                ->get()
                ->first();
            //dd($hc_rec);

            $protocolo = DB::table('hc_protocolo')->where('hcid', $id_hc)->get()->last();

            if (!is_null($protocolo)) {
                $estudios = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '3')->get()->last();
            }
        }

        $laboratorio_externo = null;
        if (!is_null($paciente->Paciente_biopsia)) {

            $laboratorio_externo = Paciente_biopsia::where('id_paciente', $id_paciente)
                ->where('estado', '1')
                ->OrderBy('created_at', 'desc')
                ->get()->first();
        }

        $biopsias_1 = Paciente_biopsia::where('id_paciente', $id_paciente)
            ->where('estado', '0')->OrderBy('created_at', 'desc')->get()->first();
        $biopsias_interno = Hc_informe_biopsia::where('id_paciente', $id_paciente)->OrderBy('updated_at', 'desc')->first();

        $biopsias_2 = DB::table('historiaclinica')
            ->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $id_paciente)
            ->where('hc_imagenes_protocolo.estado', '4')
            ->OrderBy('hc_imagenes_protocolo.created_at', 'desc')->get()->first();

        //FUNCIONALES
        $pro_completo_1 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where('gp.tipo_procedimiento', '1')
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda')
            ->OrderBy('h.created_at', 'desc')->get()->first();

        $pro_final_1 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where('hc_proto.tipo_procedimiento', '1')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda')->OrderBy('h.created_at', 'desc')->get()->first();

        //endoscopicos
        $pro_completo_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where('gp.tipo_procedimiento', '0')
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.id as hc_proto_id', 'h.created_at as h_created_at', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda')
            ->orderBy('hc_proto.created_at')
            ->get()->last();

        $pro_final_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where('hc_proto.tipo_procedimiento', '0')
            ->where('hc_p.estado', '1')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'h.hcid', 'hc_proto.id as hc_proto_id', 'h.created_at as h_created_at', 'hc_p.id as id_procedimiento', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda')
            ->orderBy('hc_proto.created_at')
            ->get()->last();
        // dd($pro_final_0);

        //ecografias
        $pro_completo_2 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where('gp.tipo_procedimiento', '2')
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.id as hc_proto_id', 'h.created_at as h_created_at', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda')
            ->orderBy('hc_proto.created_at')
            ->get()->last();

        $pro_final_2 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where('hc_proto.tipo_procedimiento', '2')
            ->where('hc_p.estado', '1')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'h.hcid', 'hc_proto.id as hc_proto_id', 'h.created_at as h_created_at', 'hc_p.id as id_procedimiento', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda')
            ->orderBy('hc_proto.created_at')
            ->get()->last();

        // dd($pro_final_0);
        //PROCEDIMIENTOS ENDOSCOPICOS//
        //TIPO PROCEDIMIENTO = 0

        $doctor_procedimiento_endoscopico = null;

        if (!is_null($pro_completo_0)) {

            $doctor_procedimiento_endoscopico = $pro_completo_0;

        }
        if (!is_null($pro_final_0)) {
            $doctor_procedimiento_endoscopico = $pro_final_0;
        }

        //PROCEDIMIENTOS FUNCIONALES//
        //TIPO PROCEDIMIENTO = 1

        $doctor_procedimiento_funcional = null;
        if (!is_null($pro_completo_1)) {
            $doctor_procedimiento_funcional = $pro_completo_1;
        }

        if (!is_null($pro_final_1)) {
            $doctor_procedimiento_funcional = $pro_final_1;
        }
        //PROCEDIMIENTOS ecografias//
        //TIPO PROCEDIMIENTO = 2

        $doctor_procedimiento_ecografia = null;

        if (!is_null($pro_completo_2)) {

            $doctor_procedimiento_ecografia = $pro_completo_2;

        }
        if (!is_null($pro_final_2)) {
            $doctor_procedimiento_ecografia = $pro_final_2;
        }

        //PROCEDIMIENTOS ecografias//
        //TIPO PROCEDIMIENTO = 2
        //nuevo_armado
        $armado1 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where(function ($query) {
                $query->where('gp.tipo_procedimiento', '0')
                    ->orwhere('gp.tipo_procedimiento', '2');
            })

            ->where('hc_p.estado', '1')
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.id as hc_proto_id', 'h.created_at as h_created_at', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda')
            ->orderBy('hc_proto.created_at')
            ->get()->last();

        $armado2 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')

            ->where('hc_p.estado', '1')
            ->where(function ($query) {
                $query->where('hc_proto.tipo_procedimiento', '0')
                    ->orwhere('hc_proto.tipo_procedimiento', '2');
            })
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'h.hcid', 'hc_proto.id as hc_proto_id', 'h.created_at as h_created_at', 'hc_p.id as id_procedimiento', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda')
            ->orderBy('hc_proto.created_at')
            ->get()->last();

        $nuevo_armado = null;

        if (!is_null($armado1)) {

            $nuevo_armado = $armado1;

        }
        if (!is_null($armado2)) {
            $nuevo_armado = $armado2;
        }

        //ORDEN DE LABORATORIO

        $orden_lab = DB::table('examen_orden as eo')
            ->where('eo.id_paciente', $id_paciente)
            ->join('paciente as p', 'p.id', 'eo.id_paciente')
            ->where('eo.estado', '1')
            ->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1')
            ->OrderBy('fecha_orden', 'desc')
            ->get()->first();

        //FIN DE ORDEN LABORATORIO

        $pro_completo_consulta = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('hc_evolucion as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.hc_id_procedimiento')
        //->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->where('hc_p.id_procedimiento_completo', '40')
            ->where('a.estado_cita', '<>', '3')
            ->OrderBy('h.created_at', 'desc')
            ->get()->first();

        //dd($pro_completo_consulta);

        $pro_final_consulta = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->join('hc_evolucion as hc_evo', 'hc_evo.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_evo.hc_id_procedimiento')
            ->where('hc_p.tipo_procedimiento', '3')
            ->where('a.estado_cita', '<>', '3')
            ->OrderBy('h.created_at', 'desc')
            ->get()->first();

        $consulta_nueva = null;

        if (!is_null($pro_completo_consulta)) {

            $consulta_nueva = $pro_completo_consulta;

        }
        if (!is_null($pro_final_consulta)) {
            $consulta_nueva = $pro_final_consulta;
        }

        //dd($consulta_nueva->id_doctor_examinador);

        //dd($paciente->agenda->last()->historia_clinica);
        // dd($paciente->agenda->last()->historia_clinica->doctor_1->nombre1);
        //dd($paciente->seguro->nombre);

        $seguro = DB::table('agenda as a')
            ->where('a.id_paciente', $id_paciente)
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->orderBy('a.fechaini', 'desc')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->select('s.nombre', 'a.*')
            ->first();
        //dd($seguro);

        //dd($doctor_procedimiento_ecografia);

        //dd($wa);
        //dd($cort_paciente->id);

        $fecha = date('Y-m-d H:i:s');
        //$prox_procedimientos = Agenda::where('id_paciente', $id_paciente)->where('estado', '<>', '0')->where('id_doctor1', '<>', '4444444444')->where('proc_consul', '1')->where('fechaini', '>=', $fecha)->OrderBy('fechaini', 'asc')->first();
        $prox_procedimientos = Agenda::where('id_paciente', $id_paciente)->where('estado', '<>', '0')->where('proc_consul', '1')->where('fechaini', '>=', $fecha)->OrderBy('fechaini', 'asc')->first();
        $procedimientos = Procedimiento::all();
        return view('hc4/buscador', ['seguro' => $seguro, 'edad' => $edad, 'paciente' => $paciente, 'cortesia_paciente' => $cortesia_paciente, 'evoluciones' => $evoluciones, 'alergiasxpac' => $alergiasxpac, 'procedimientos_observaciones' => $procedimientos_observaciones, 'especialidad' => $especialidad, 'hc_rec' => $hc_rec, 'biopsias_1' => $biopsias_1, 'biopsias_2' => $biopsias_2, 'laboratorio_externo' => $laboratorio_externo, 'estudios' => $estudios, 'doctor_procedimiento_endoscopico' => $doctor_procedimiento_endoscopico, 'doctor_procedimiento_funcional' => $doctor_procedimiento_funcional, 'doctor_procedimiento_ecografia' => $doctor_procedimiento_ecografia, 'orden_lab' => $orden_lab, 'consulta_nueva' => $consulta_nueva, 'nuevo_armado' => $nuevo_armado, 'paciente_observaciones' => $paciente_observaciones, 'prox_procedimientos' => $prox_procedimientos, 'procedimientos' => $procedimientos, 'biopsias_interno' => $biopsias_interno]);
    }

    public function ingreso(Request $request) //admision_datos.doctor

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

    public function actualizar(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $paciente = paciente::find($request->id_paciente);

        $paciente_observaciones = Paciente_Observaciones::where('id_paciente', $request->id_paciente)->first();
        //echo $request['id_paciente'];

        $observaciones_admin =[
            'id_paciente'     => $request['id_paciente'],
            'observacion'     => $request['observacion_admin'],
            'id_usuariomod'   => $idusuario,
            'id_usuariocrea'  => $idusuario,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente
        ];

        if(count($paciente_observaciones)>0){
            Paciente_Observaciones::where('id_paciente', $request->id_paciente)->update($observaciones_admin);
            
        }else if (is_null($paciente_observaciones)){
            Paciente_Observaciones::create($observaciones_admin);
        }

        if (!is_null($paciente)) {
            $observacion_paciente_new = [
                'anterior'        => 'DATOS_PRINCIPALES-> observacion: ' . $paciente->observacion . '  Habitos: ' . $paciente->alcohol . ' Antecedentes_Patologicos: ' . $paciente->antecedentes_pat . '    Antecedentes_Familiares: ' . $paciente->antecedentes_fam . '     Antecedentes_Quirurgicos: ' . $paciente->antecedentes_quir,
                'nuevo'           => 'DATOS_PRINCIPALES-> observacion: ' . $request['observacion'] . ' Habitos: ' . $request['habitos'] . '  Antecedentes_Patologicos: ' . $request['an_patologicos'] . '    Antecedentes_Familiares: ' . $request['an_familiares'] . '     Antecedentes_Quirurgicos: ' . $request['an_quirurgicos'],
                'id_paciente'     => $request['id_paciente'],
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];
            Hc_Log::create($observacion_paciente_new);
        }

        $observacion_paciente = [
            'observacion'       => $request['observacion'],
            'alcohol'           => $request['habitos'],
            'antecedentes_pat'  => $request['an_patologicos'],
            'antecedentes_fam'  => $request['an_familiares'],
            'antecedentes_quir' => $request['an_quirurgicos'],
            'id_usuariomod'     => $idusuario,
            'id_usuariocrea'    => $idusuario,
            'ip_modificacion'   => $ip_cliente,
            'ip_creacion'       => $ip_cliente,
        ];
        paciente::where('id', $request['id_paciente'])->update($observacion_paciente);

    }

    public function prueba($id_paciente)
    {
        return view('hc4/evoluciones/index');

    }

    public function d_filiacion(Request $request) //admision_datos.doctor

    {
        //return "hola";
        //return $request->all();
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $id = $request["id_paciente"];
        //return $request->all();
        $paciente = Paciente::find($id);

        $rule = [
            'telefono1' => 'required',
            'mail'      => 'required|email',
        ];
        //'required|email|unique:users,email,'.$id,
        $msn = [
            'telefono1.required' => 'Ingrese el telefono',
            'mail.required'      => 'Ingrese el mail',
            'mail.unique'        => 'Mail registrado en otro paciente',
            'mail.email'         => 'Mail ingresado con formato incorrecto',
        ];

        $this->validate($request, $rule, $msn);

        $input_u = [
            'email'           => $request['mail'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,

        ];

        if ($paciente->parentesco == 'Principal') {

            User::find($paciente->id)->update($input_u);
        } else {
            User::find($paciente->id_usuario)->update($input_u);
        }

        /*$alergiasxpac = Paciente_Alergia::where('id_paciente',$id)->get();

        foreach ($alergiasxpac as $apac) {
        $apac->delete();
        }

        $alergia_txt = "";
        $ale_flag=true;
        //return $request->ale_list;
        if($request->ale_list!=null){
        foreach ($request->ale_list as $ale) {

        if(is_numeric($ale)){
        $generico = Principio_Activo::find($ale);
        }else{
        $input_principio = [
        'nombre' => substr(strtoupper($ale), 0,-5),
        'descripcion' => substr(strtoupper($ale), 0,-5),
        'estado' => '1',
        'ip_modificacion' => $ip_cliente,
        'id_usuariomod' => $idusuario,
        'ip_creacion' => $ip_cliente,
        'id_usuariocrea' => $idusuario,
        ];
        $ale = Principio_Activo::insertGetId($input_principio);
        $generico = Principio_Activo::find($ale);
        }
        $generico = Principio_Activo::find($ale);
        $pac_ale = [

        'id_paciente' => $id,
        'id_principio_activo' => $ale,
        'ip_creacion' => $ip_cliente,
        'ip_modificacion' => $ip_cliente,
        'id_usuariomod' => $idusuario,
        'id_usuariocrea' => $idusuario,

        ];
        Paciente_Alergia::create($pac_ale);
        if($ale_flag){
        $alergia_txt = $generico->nombre;
        $ale_flag=false;
        }else{
        $alergia_txt = $alergia_txt.'+'.$generico->nombre;
        }

        }
        }
         */

        $input1 = [
            'sexo'              => $request["sexo"],
            'referido'          => strtoupper($request["referido"]),
            'alcohol'           => strtoupper($request["alcohol"]),
            'ciudad'            => strtoupper($request["ciudad"]),
            'estadocivil'       => $request["estadocivil"],
            'direccion'         => strtoupper($request["direccion"]),
            //'alergias' => $request["alergias"],
            //'alergias' => $alergia_txt,
            'lugar_nacimiento'  => strtoupper($request["lugar_nacimiento"]),
            'telefono1'         => $request["telefono1"],
            'telefono2'         => $request["telefono2"],
            'religion'          => $request["religion"],

            'ocupacion'         => strtoupper($request["ocupacion"]),
            'trabajo'           => strtoupper($request["trabajo"]),

            'transfusion'       => $request["transfusion"],
            'observacion'       => $request["observacion"],
            'vacuna'            => $request["vacuna"],
            'antecedentes_pat'  => $request["antecedentes_pat"],
            'antecedentes_fam'  => $request["antecedentes_fam"],
            'antecedentes_quir' => $request["antecedentes_quir"],
            'gruposanguineo'    => $request["gruposanguineo"],
            'fecha_nacimiento'  => $request["fecha_nacimiento"],
            'nombre1'           => $request["nombre1"],
            'nombre2'           => $request["nombre2"],
            'apellido1'         => $request["apellido1"],
            'apellido2'         => $request["apellido2"],
            //'ip_modificacion' => $ip_cliente,
            //'id_usuariomod' => $idusuario
        ];

        $paciente->update($input1);
        return "exito";
    }

    public function actualizacortesia($id, $c)
    {
        //return "holaaa";

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        //$agenda = Agenda::find('$id_paciente', $id);
        // Redirect to user list if updating user wasn't existed
        //if ($agenda == null || count($agenda) == 0) {
        //return redirect()->intended('/agenda');
        //}

        $fecha1 = date('Y/m/d ') . "00:00:00";
        $fecha2 = date('Y/m/d ') . "23:59:59";

        $cort_paciente = agenda::where('id_paciente', $id)
            ->where('proc_consul', '0')
            ->whereBetween('fechaini', [$fecha1, $fecha2])
            ->first();
          //  dd($cort_paciente);
        if ($c == 0) {$cortesia = "NO";} elseif ($c == 1) {$cortesia = "SI";}

        if (!is_null($c)) {
            $nueva_cortesia = [
                'anterior'        => 'DATOS_PRINCIPALES-> Cortesia: ',
                'nuevo'           => 'DATOS_PRINCIPALES-> Cortesia: ' . $cortesia,
                'id_paciente'     => $id,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];
            Hc_Log::create($nueva_cortesia);
        }

        if (!is_null($cort_paciente)) {
            $input = [
                'cortesia' => $cortesia,
                //'ip_modificacion' => $ip_cliente,
                //'id_usuariomod' => $idusuario
            ];
            $cort_paciente->update($input);
        }

        $cortesia_paciente = Cortesia_Paciente::find($id);

        if (is_null($cortesia_paciente)) {
            $input_cortesia = [
                'id'              => $id,
                'cortesia'        => $cortesia,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
            ];
            Cortesia_Paciente::create($input_cortesia);
        } else {
            $input_cortesia = [
                'id'              => $id,
                'cortesia'        => $cortesia,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            $cortesia_paciente->update($input_cortesia);
        }

        //return  redirect()->route("agenda.detalle", ['id' => $agenda->id]);

    }

}
