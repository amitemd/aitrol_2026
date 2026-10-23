<?php
namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Support\Facades\DB;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;

class EpicrisisController extends Controller
{
    //NUEVA FUNCION ROL CON TABLA OPCION_USUARIO

    protected $redirectTo = '/';
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    //MUESTRA EL HISTORIA DE BIOPSIA_1 Y BIOPSIAS_2 DE UN PACIENTE
    //SOLO PARA DOCTORES
    public function index($id_paciente)
    {

        $paciente       = Paciente::find($id_paciente);
        $pro_completo_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')

            ->where('hc_p.estado', '1')
            ->where(function ($query) {
                $query->where('gp.tipo_procedimiento', '0')
                    ->orwhere('gp.tipo_procedimiento', '2');
            })
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'hc_proto.fecha as fecha', 'hc_proto.created_at as created_at_proto', 'hc_p.id_doctor_examinador2', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'hc_p.id_doctor_responsable')
            ->OrderBy('hc_proto.created_at', 'desc')->get();
        //dd($pro_completo_0);

        $pro_final_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')

            ->where('hc_p.estado', '1')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->where(function ($query) {
                $query->where('hc_proto.tipo_procedimiento', '0')
                    ->orwhere('hc_proto.tipo_procedimiento', '2');
            })
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'h.created_at', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'hc_proto.fecha as fecha', 'hc_proto.created_at as created_at_proto', 'hc_p.id_doctor_examinador2', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'hc_p.id_doctor_responsable')
            ->OrderBy('hc_proto.created_at', 'desc')->get();

        // dd($pro_final_0);

        return view('hc4/epicrisis/index', ['paciente' => $paciente, 'procedimientos1' => $pro_completo_0, 'procedimientos2' => $pro_final_0]);

    }
}
