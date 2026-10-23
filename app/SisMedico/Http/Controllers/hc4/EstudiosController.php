<?php

namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\User;

class EstudiosController extends Controller
{
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

    public function index($id_paciente)
    {
        $opcion = '2';
        if ($this->rol_new($opcion)) {
            return redirect('/');
        }

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
            ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_p.id_seguro as seguro_final')
            ->OrderBy('hc_proto.created_at', 'desc')->get();

        //dd($pro_completo_0);

        $pro_final_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')

            ->where('hc_p.estado', '1')
            ->where(function ($query) {
                $query->where('hc_proto.tipo_procedimiento', '0')
                    ->orwhere('hc_proto.tipo_procedimiento', '2');
            })
            ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'h.id_seguro as hc_id_seguro', 'hc_p.id_seguro as seguro_final')->OrderBy('hc_proto.created_at', 'desc')->get();

        $imagenes2 = DB::SELECT("SELECT hc_ima.*
          FROM  hc_imagenes_protocolo hc_ima,  hc_protocolo hc_proto,  historiaclinica hc, paciente p, hc_procedimientos hc_p
          WHERE hc_ima.id_hc_protocolo = hc_proto.id AND
                hc_p.id = hc_proto.id_hc_procedimientos AND
                hc_p.estado = 1 AND
                hc_proto.hcid = hc.hcid AND
                hc.id_paciente = p.id AND
                hc_ima.estado = 1 AND
                p.id = '" . $id_paciente . "'
                ORDER BY id desc;");

        return view('hc4/estudios/index', ['paciente' => $paciente, 'procedimientos1' => $pro_completo_0, 'procedimientos2' => $pro_final_0, 'imagenes2' => $imagenes2]);
    }

    public function mostrar_foto_eliminar($id)
    {
        $imagen = hc_imagenes_protocolo::find($id);
        return view('hc4/procedimiento_endoscopico/modal', ['imagen' => $imagen]);
    }

    public function eliminar_foto_eliminar($id)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $input1 = [
            'estado'          => '0',
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        hc_imagenes_protocolo::where('id', $id)
            ->update($input1);
        return "Archivo eliminado correctamente";
    }

}
