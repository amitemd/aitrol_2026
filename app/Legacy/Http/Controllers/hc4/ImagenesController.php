<?php

namespace Sis_medico\Http\Controllers\hc4;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Cortesia_paciente;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\User;

class ImagenesController extends Controller
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

    public function index($id_protocolo)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

        $protocolo   = hc_protocolo::find($id_protocolo);
        $id_paciente = $protocolo->historiaclinica->id_paciente;
        $paciente    = Paciente::find($id_paciente);
        if (is_null($paciente)) {
            return redirect('/');
        }
        $edad = 0;
        if ($paciente->fecha_nacimiento != null) {
            $edad = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        }

        $cortesia_paciente = Cortesia_Paciente::where('id', $id_paciente)->get()->last();

        $evoluciones = DB::table('hc_evolucion as e')->join('historiaclinica as h', 'e.hcid', 'h.hcid')->where('h.id_paciente', $id_paciente)->join('agenda as a', 'a.id', 'h.id_agenda')->orderBy('a.fechaini', 'desc')->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid')->orderBy('e.id', 'desc')->get()->first(); /// aquiiii

        //dd($evoluciones->id);

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

            $hc_rec = DB::table('hc_receta as r')
                ->join('historiaclinica as h', 'r.id_hc', 'h.hcid')
                ->where('r.prescripcion', '!=', null)
                ->where('h.id_paciente', $id_paciente)
                ->join('agenda as a', 'a.id', 'h.id_agenda')
                ->orderBy('a.fechaini', 'desc')
                ->select('r.*', 'a.fechaini')
                ->get()
                ->first();

            $protocolo = DB::table('hc_protocolo')->where('hcid', $id_hc)->get()->last();

            if (!is_null($protocolo)) {
                $estudios = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '3')->get()->last();
            }
        }

        //dd($hc_rec);

        $laboratorio_externo = null;
        if (!is_null($paciente->Paciente_biopsia)) {

            $laboratorio_externo = Paciente_biopsia::where('id_paciente', $id_paciente)
                ->where('estado', '1')
                ->OrderBy('created_at', 'desc')
                ->get()->first();
        }

        $biopsias_1 = Paciente_biopsia::where('id_paciente', $id_paciente)
            ->where('estado', '0')->OrderBy('created_at', 'desc')->get()->first();

        $biopsias_2 = DB::table('historiaclinica')
            ->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $id_paciente)
            ->where('hc_imagenes_protocolo.estado', '4')
            ->OrderBy('hc_imagenes_protocolo.created_at', 'desc')->get()->first();

        //21/02/2019

        //dd($paciente->agenda->last()->observaciones);

        $pro_completo_1 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where('gp.tipo_procedimiento', '1')
            ->where('hc_p.estado', '1')
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda')
            ->OrderBy('h.created_at', 'desc')->get()->first();

        $pro_final_1 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where('hc_proto.tipo_procedimiento', '1')
            ->where('hc_p.estado', '1')
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
            ->where('hc_p.estado', '1')
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

        //ecografias
        $pro_completo_2 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            ->where('gp.tipo_procedimiento', '2')
            ->where('hc_p.estado', '1')
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
            ->where(function ($query) {
                $query->where('hc_proto.tipo_procedimiento', '0')
                    ->orwhere('hc_proto.tipo_procedimiento', '2');
            })
            ->where('hc_p.estado', '1')
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'h.hcid', 'hc_proto.id as hc_proto_id', 'h.created_at as h_created_at', 'hc_p.id as id_procedimiento', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda')
            ->orderBy('hc_proto.created_at')
            ->get()->last();

        //PROCEDIMIENTOS ENDOSCOPICOS//
        //TIPO PROCEDIMIENTO = 0

        //dd($pro_final_0);
        $doctor_procedimiento_endoscopico = null;

        if (!is_null($pro_completo_0)) {
            //$nomb_procedimiento = $pro_completo_0;
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

        $nuevo_armado = null;

        if (!is_null($armado1)) {

            $nuevo_armado = $armado1;

        }
        if (!is_null($armado2)) {
            $nuevo_armado = $armado2;
        }

        $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->orderBy('created_at', 'desc')->get();
        $cvideo   = hc_imagenes_protocolo::where('nombre', 'LIKE', '%.mp4')->where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->orderBy('id', 'desc')->count();

        $cimagenes = hc_imagenes_protocolo::where('nombre', 'NOT LIKE', '%.mp4')->where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->orderBy('id', 'desc')->count();

        $orden_lab = DB::table('examen_orden as eo')
            ->where('eo.id_paciente', $id_paciente)
            ->join('paciente as p', 'p.id', 'eo.id_paciente')
            ->where('eo.estado', '1')
            ->select('eo.*', 'p.nombre1 as pnombre1', 'p.nombre2 as pnombre2', 'p.apellido2 as papellido2', 'p.apellido1 as papellido1')
            ->OrderBy('created_at', 'desc')
            ->get()->first();

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

        $seguro = DB::table('agenda as a')
            ->where('a.id_paciente', $id_paciente)
            ->join('historiaclinica as h', 'h.id_agenda', 'a.id')
            ->orderBy('a.fechaini', 'desc')
            ->join('seguros as s', 's.id', 'h.id_seguro')
            ->select('s.nombre', 'a.*')
            ->first();
        //dd($seguro);

        $imagenes2 = DB::SELECT("SELECT hc_ima.*
          FROM  hc_imagenes_protocolo hc_ima,  hc_protocolo hc_proto,  historiaclinica hc, paciente p
          WHERE hc_ima.id_hc_protocolo = hc_proto.id AND
                hc_proto.hcid = hc.hcid AND
                hc.id_paciente = p.id AND
                hc_ima.estado = 1 AND
                p.id = '" . $id_paciente . "'
                ORDER BY id desc LIMIT 50 OFFSET 0;");

        return view('hc4/imagenes/index', ['seguro' => $seguro, 'imagenes' => $imagenes, 'id' => $id_protocolo, 'cvideo' => $cvideo, 'cimagenes' => $cimagenes, 'edad' => $edad, 'paciente' => $paciente, 'cortesia_paciente' => $cortesia_paciente, 'evoluciones' => $evoluciones, 'alergiasxpac' => $alergiasxpac, 'procedimientos_observaciones' => $procedimientos_observaciones, 'especialidad' => $especialidad, 'hc_rec' => $hc_rec, 'biopsias_1' => $biopsias_1, 'biopsias_2' => $biopsias_2, 'laboratorio_externo' => $laboratorio_externo, 'estudios' => $estudios, 'doctor_procedimiento_endoscopico' => $doctor_procedimiento_endoscopico, 'doctor_procedimiento_funcional' => $doctor_procedimiento_funcional, 'orden_lab' => $orden_lab, 'consulta_nueva' => $consulta_nueva, 'imagenes2' => $imagenes2, 'nuevo_armado' => $nuevo_armado, 'doctor_procedimiento_ecografia' => $doctor_procedimiento_ecografia]);
    }
}
