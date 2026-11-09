<?php

namespace Sis_medico\Http\Controllers\hc_admision;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Sis_medico\Agenda;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Paciente_Biopsia;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Procedimiento;
use Sis_medico\procedimiento_completo;
use Sis_medico\Seguro;use Sis_medico\User;

class ProcedimientosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 3, 6, 11, 5, 7,20)) == false) {
            return true;
        }
    }

    /**
     * Obtiene las imagenes activas que pertenecen exclusivamente al protocolo.
     * Si el usuario no marco ninguna, conserva el orden habitual del modulo y
     * permite que cada formato tome las primeras que necesita.
     */
    private function imagenesParaResumen($idProtocolo)
    {
        $consulta = hc_imagenes_protocolo::where('id_hc_protocolo', $idProtocolo)
            ->where('estado', '1')
            ->orderBy('id', 'desc');

        $seleccionadas = (clone $consulta)->where('seleccionado', '1')->get();

        return $seleccionadas->isEmpty() ? $consulta->get() : $seleccionadas;
    }
    public function mostrar($id)
    {

        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'seguros.color as color')
            ->where('agenda.id', '=', $id)
            ->first();

        $paciente = Paciente::find($agenda->id_paciente);

        $usuarios = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->get(); //3=DOCTORES;

        $enfermeros = DB::table('users')->where('id_tipo_usuario', '=', 6)->where('estado', '1')->get(); //6=ENFERMEROS;

        $hcp = DB::select("SELECT h.*, e.nombre as especialidad, s.nombre as snombre ,d.nombre1 as dnombre1, d.nombre2 as dnombre2, d.apellido1 as dapellido1, d.apellido2 as dapellido2, a.proc_consul as tipo_cita, a.fechaini as fechainicio
                            FROM historiaclinica h, seguros s, agenda a, especialidad e, users d
                            WHERE h.id_paciente = " . $agenda->id_paciente . " AND
                            a.id = h.id_agenda AND
                            s.id = h.id_seguro AND
                            a.espid =  e.id AND
                            a.id_doctor1 = d.id AND
                            h.id_agenda <> " . $id . "
                            ORDER BY a.fechaini DESC");

        $hca = DB::table('historiaclinica')->where('id_agenda', '=', $id)->first();

        $seguro = Seguro::find($hca->id_seguro);
        $pentax = Pentax::where('hcid', '=', $hca->hcid)->first();

        $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $pentax->id)->get();
        $procedimientos_hc     = hc_procedimientos::where('id_hc', '=', $hca->hcid)->get();

        return view('hc_admision/procedimientos/procedimientos', ['agenda' => $agenda, 'paciente' => $paciente, 'usuarios' => $usuarios, 'enfermeros' => $enfermeros, 'hcp' => $hcp, 'hca' => $hca, 'seguro' => $seguro, 'procedimientos_pentax' => $procedimientos_pentax, 'procedimientos_hc' => $procedimientos_hc]);
    }

    public function agregar($hc_id)
    {

        $historia_clinica         = Historiaclinica::find($hc_id);
        $procedimientos_completos = procedimiento_completo::all();
        return view('hc_admision/procedimientos/modal', ['procedimientos_completos' => $procedimientos_completos, 'historia_clinica' => $historia_clinica]);
    }

    public function guardar(Request $request)
    {
        $id_procedimiento_completo = $request['id_procedimiento_completo'];
        $id_seguro                 = $request['id_seguro'];
        $id_hc                     = $request['id_hc'];
        $ip_cliente                = $_SERVER["REMOTE_ADDR"];
        $idusuario                 = Auth::user()->id;

        $historia_clinica = Historiaclinica::find($id_hc);
        $id_agenda        = $historia_clinica->id_agenda;
        $input1           = [
            'id_hc'                     => $id_hc,
            'id_procedimiento_completo' => $id_procedimiento_completo,
            'id_seguro'                 => $id_seguro,
            'ip_creacion'               => $ip_cliente,
            'ip_modificacion'           => $ip_cliente,
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
        ];

        hc_procedimientos::insert($input1);
        return redirect()->route('procedimientos_historia.mostrar', ['ag' => $id_agenda]);
    }

    public function eliminar($hc_id)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $input = [
            'estado'          => '0',
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        hc_procedimientos::where('id', $hc_id)
            ->update($input);
        $hc_procedimiento = hc_procedimientos::find($hc_id);
        $id_agenda        = $hc_procedimiento->historia->id_agenda;
            return redirect()->route('procedimientos_historia.mostrar', ['ag' => $id_agenda]);
    }

    public function estudio_agregar($id)
    {
        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->leftjoin('pentax as p', 'p.id_agenda', 'agenda.id')
            ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
            ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial', 'paciente.sexo', 'paciente.gruposanguineo', 'paciente.transfusion', 'paciente.alergias', 'paciente.vacuna', 'paciente.historia_clinica', 'paciente.antecedentes_pat', 'paciente.antecedentes_fam', 'paciente.antecedentes_quir', 'h.hcid', 'p.estado_pentax', 'paciente.observacion as pobservacion', 'p.id as pxid')
            ->where('agenda.id', '=', $id)
            ->first();

        //dd($agenda);

        $fecha_dia = date('Y-m-d', strtotime($agenda->fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $cant_cortesias = Agenda::where('id_doctor1', $agenda->id_doctor1)->where('fechaini', '>', $fecha_dia)->where('fechaini', '<', $nuevafecha)->where('cortesia', 'SI')->count();

        $proc_completo = procedimiento_completo::orderBy('nombre_general')->get();

        $imagenes = null;
        $no_admin = false;
        //dd($agenda->hcid);
        $protocolo             = null;
        $procedimientos_pentax = null;
        if (!is_null($agenda->hcid)) {
            $protocolo = DB::table('hc_protocolo as p')->where('p.hcid', $agenda->hcid)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->join('pentax as px', 'px.hcid', 'p.hcid')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3')->orderBy('p.created_at', 'desc')->first();

            $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $agenda->pxid)->get();

            $imagenes   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->where('estado', '1')->orderBy('id', 'desc')->get();
            $hc_receta  = hc_receta::where('id_hc', $agenda->hcid)->first();
            $idusuario  = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            if (is_null($hc_receta)) {
                $input_hc_receta = [
                    'id_hc'           => $agenda->hcid,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariocrea'  => $idusuario,
                    'id_usuariomod'   => $idusuario,
                    'ip_creacion'     => $ip_cliente,
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];
                hc_receta::insert($input_hc_receta);
            }
            $hc_receta = hc_receta::where('id_hc', $agenda->hcid)->first();

            //dd($imagenes);
        } else {
            $no_admin = true;
        }

        $doctores = User::where('estado', '1')->where('id_tipo_usuario', '3')->get();

        $anestesiologos = User::where('estado', '1')->where('id_tipo_usuario', '9')->get();

        $enfermeros = User::where('estado', '1')->where('id_tipo_usuario', '6')->get();

        $seguros = Seguro::where('inactivo', '1')->get();

        $documentos = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '2')->get();

        $estudios = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '3')->get();
        $biopsias = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '4')->get();

        return $this->Crear_Editar($agenda, $cant_cortesias, $proc_completo, $protocolo, $doctores, $anestesiologos, $enfermeros, $seguros, $procedimientos_pentax, $imagenes, $id, $documentos, $estudios, $biopsias, $hc_receta);
    }

    public function estudio_nuevo($id)
    {
        //dd($id_agenda);

        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');

        $historia      = Historiaclinica::where('id_agenda', $id)->first();
        $procedimiento = hc_procedimientos::where('id_hc', $historia->hcid)->first();
        $evoluciones   = hc_evolucion::where('hc_id_procedimiento', $procedimiento->id)->get();

        //ingreso de datos de para protocolo, evolucion, preparacion para consulta externa
        $input_hc_procedimiento = [
            'id_hc'           => $historia->hcid,
            'id_seguro'       => $historia->id_seguro,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

        foreach ($evoluciones as $value) {
            $input = [
                'hc_id_procedimiento' => $id_hc_procedimiento,
                'hcid'                => $historia->hcid,
                'secuencia'           => $value->secuencia,
                'cuadro_clinico'      => $value->cuadro_clinico,
                'fecha_ingreso'       => $value->fecha_ingreso,
                'ip_creacion'         => $ip_cliente,
                'ip_modificacion'     => $ip_cliente,
                'id_usuariocrea'      => $idusuario,
                'id_usuariomod'       => $idusuario,
            ];

            $id_evolucion = Hc_Evolucion::insertGetId($input);
        }
        /*$input_hc_evolucion = [
        'hc_id_procedimiento' => $id_hc_procedimiento,
        'hcid' => $historia->hcid,
        'secuencia' => '0',
        'cuadro_clinico' => ' ',
        'fecha_ingreso' => ' ',
        'ip_modificacion' => $ip_cliente,
        'id_usuariomod' => $idusuario,
        'id_usuariocrea' => $idusuario,
        'ip_creacion' => $ip_cliente,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        ];
        Hc_evolucion::insert($input_hc_evolucion);*/
        $input_hc_protocolo = [
            'id_hc_procedimientos' => $id_hc_procedimiento,
            'hora_inicio'          => date('H:i:s'),
            'hora_fin'             => date('H:i:s'),
            'estado_final'         => ' ',
            'ip_modificacion'      => $ip_cliente,
            'hcid'                 => $historia->hcid,
            'id_usuariocrea'       => $idusuario,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'created_at'           => date('Y-m-d H:i:s'),
            'updated_at'           => date('Y-m-d H:i:s'),
        ];
        $id_protocolo = hc_protocolo::insertGetId($input_hc_protocolo);
        $hc_receta    = hc_receta::where('id_hc', $id)->first();
        if (is_null($hc_receta)) {
            $input_hc_receta = [
                'id_hc'           => $id,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
            hc_receta::insert($input_hc_receta);
        }
        $hc_receta = hc_receta::where('id_hc', $id)->first();

        //return $this->estudio_editar($id_protocolo,$id);
        return redirect()->route('estudio.editar', ['id' => $id_protocolo, 'id_agenda' => $id]);
    }

    public function estudio_editar($id, $id_agenda)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];

        $protocolo = DB::table('hc_protocolo as p')->where('p.id', $id)->join('hc_procedimientos as hp', 'hp.id', 'p.id_hc_procedimientos')->join('pentax as px', 'px.hcid', 'p.hcid')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hp.id_procedimiento_completo')->select('p.*', 'px.estado_pentax', 'px.id as pxid', 'hp.id_procedimiento_completo', 'px.id_doctor1', 'px.id_doctor2', 'px.id_doctor3', 'px.id_agenda')->orderBy('p.created_at', 'desc')->first();

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->leftjoin('pentax as p', 'p.id_agenda', 'agenda.id')
            ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
            ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial', 'paciente.sexo', 'paciente.gruposanguineo', 'paciente.transfusion', 'paciente.alergias', 'paciente.vacuna', 'paciente.historia_clinica', 'paciente.antecedentes_pat', 'paciente.antecedentes_fam', 'paciente.antecedentes_quir', 'h.hcid', 'p.estado_pentax', 'paciente.observacion as pobservacion', 'p.id as pxid')
            ->where('agenda.id', '=', $protocolo->id_agenda)
            ->first();
        //dd($agenda);
        $hc_receta = hc_receta::where('id_hc', $agenda->hcid)->first();
        if (is_null($hc_receta)) {
            $input_hc_receta = [
                'id_hc'           => $id,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
            hc_receta::insert($input_hc_receta);
        }
        $hc_receta = hc_receta::where('id_hc', $agenda->hcid)->first();

        $fecha_dia = date('Y-m-d', strtotime($agenda->fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $cant_cortesias = Agenda::where('id_doctor1', $agenda->id_doctor1)->where('fechaini', '>', $fecha_dia)->where('fechaini', '<', $nuevafecha)->where('cortesia', 'SI')->count();

        $proc_completo = procedimiento_completo::orderBy('nombre_general')->get();

        $procedimientos_pentax = PentaxProc::where('id_pentax', '=', $agenda->pxid)->get();

        $imagenes   = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '1')->get();
        $documentos = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '2')->get();

        $estudios = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '3')->get();
        $biopsias = hc_imagenes_protocolo::where('id_hc_protocolo', $protocolo->id)->orderBy('id', 'desc')->where('estado', '4')->get();

        $doctores = User::where('estado', '1')->where('id_tipo_usuario', '3')->get();

        $anestesiologos = User::where('estado', '1')->where('id_tipo_usuario', '9')->get();

        $enfermeros = User::where('estado', '1')->where('id_tipo_usuario', '6')->get();

        $seguros = Seguro::where('inactivo', '1')->get();

        return $this->Crear_Editar($agenda, $cant_cortesias, $proc_completo, $protocolo, $doctores, $anestesiologos, $enfermeros, $seguros, $procedimientos_pentax, $imagenes, $id_agenda, $documentos, $estudios, $biopsias, $hc_receta);

    }

    public function descarga_seleccion($id_protocolo, $agenda_ori, $ruta)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo              = hc_protocolo::find($id_protocolo);
        $imagenes               = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('estado', '1')->orderBy('id', 'desc')->get();
        $agenda                 = Agenda::find($protocolo->historiaclinica->id_agenda);
        $paciente               = paciente::find($protocolo->historiaclinica->id_paciente);
        $edad                   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $procedimiento_completo = procedimiento_completo::find($protocolo->procedimiento->id_procedimiento_completo);
        $historia               = Historiaclinica::find($protocolo->hcid);
        $imagenes2              = DB::SELECT("SELECT hc_ima.*
          FROM  hc_imagenes_protocolo hc_ima,  hc_protocolo hc_proto,  historiaclinica hc, paciente p
          WHERE hc_ima.id_hc_protocolo = hc_proto.id AND
                hc_proto.hcid = hc.hcid AND
                hc.id_paciente = p.id AND
                hc_ima.estado = 1 AND
                p.id = '" . $protocolo->historiaclinica->id_paciente . "'
                ORDER BY id desc;");
        if (is_null($protocolo->procedimiento->id_doctor_examinador2)) {
            $input = [
                'id_doctor_examinador2' => $protocolo->procedimiento->id_doctor_examinador,
            ];
            hc_procedimientos::where('id', $protocolo->procedimiento->id)->update($input);

            $protocolo = hc_protocolo::find($id_protocolo);
        }
        $doctores = User::where('id_tipo_usuario', 3)->orderBy('apellido1')->get();
        //return $doctores;
        //return $protocolo->procedimiento->id_doctor_examinador2;

        return view('hc_admision/video/seleccion', ['protocolo' => $protocolo, 'imagenes' => $imagenes, 'imagenes2' => $imagenes2, 'paciente' => $paciente, 'edad' => $edad, 'procedimiento_completo' => $procedimiento_completo, 'historia' => $historia, 'agenda' => $agenda, 'agenda_ori' => $agenda_ori, 'ruta' => $ruta, 'id' => $id_protocolo, 'doctores' => $doctores]);
    }

    public function imagen_cambio($id_imagen)
    {
        $imagen = hc_imagenes_protocolo::find($id_imagen);
        if ($imagen->seleccionado == 1) {
            $idusuario  = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            date_default_timezone_set('America/Guayaquil');
            $input = [
                'seleccionado'    => 0,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            $imagen->update($input);
        } else {
            $idusuario  = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            date_default_timezone_set('America/Guayaquil');
            $input = [
                'seleccionado'    => 1,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            $imagen->update($input);
        }
    }

    public function imagen_cambio2($id_imagen)
    {
        $imagen = hc_imagenes_protocolo::find($id_imagen);
        if ($imagen->seleccionado_recortada == 1) {
            $idusuario  = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            date_default_timezone_set('America/Guayaquil');
            $input = [
                'seleccionado_recortada' => 0,
                'ip_modificacion'        => $ip_cliente,
                'id_usuariomod'          => $idusuario,
            ];
            $imagen->update($input);
        } else {
            $idusuario  = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            date_default_timezone_set('America/Guayaquil');
            $input = [
                'seleccionado_recortada' => 1,
                'ip_modificacion'        => $ip_cliente,
                'id_usuariomod'          => $idusuario,
            ];
            $imagen->update($input);
        }
    }

    public function imagen_cambio3($id_imagen)
    {
        $imagen = hc_imagenes_protocolo::find($id_imagen);
        if ($imagen->seleccion_rx == 1) {
            $idusuario  = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            date_default_timezone_set('America/Guayaquil');
            $input = [
                'seleccion_rx'    => 0,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            $imagen->update($input);
        } else {
            $idusuario  = Auth::user()->id;
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            date_default_timezone_set('America/Guayaquil');
            $input = [
                'seleccion_rx'    => 1,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
            ];
            $imagen->update($input);
        }
    }
    //cargar de images de grupo_procedimientos
    public function load2($name)
    {

        $path = storage_path() . '/app/procedimiento_completo/' . $name;
        if (file_exists($path)) {
            ob_end_clean();
            return Response::download($path);
        }
    }

    /**
     * DomPDF no resuelve de forma consistente las rutas absolutas de las
     * fotografias clinicas. Las incrustamos para que el PDF sea autocontenido.
     * El antiguo header.jpg ya no existe, por lo que se reemplaza por el logo
     * real de la empresa que atendio al paciente.
     */
    private function prepararImagenesResumenPdf($html, $logoEmpresaPath = null)
    {
        return preg_replace_callback('/<img\b[^>]*\bsrc\s*=\s*(["\'])(.*?)\1[^>]*>/is', function ($coincidencia) use ($logoEmpresaPath) {
            $rutaOriginal = html_entity_decode($coincidencia[2], ENT_QUOTES, 'UTF-8');
            $esCabeceraAntigua = stripos(str_replace('\\', '/', $rutaOriginal), '/public/formato/header.jpg') !== false;
            $ruta = $esCabeceraAntigua ? $logoEmpresaPath : $rutaOriginal;

            if (is_null($ruta) || $ruta === '') {
                return '';
            }
            if (strpos($ruta, 'data:image/') === 0) {
                return $coincidencia[0];
            }
            if (strpos($ruta, 'file://') === 0) {
                $ruta = substr($ruta, 7);
            }
            if (!is_file($ruta) || !is_readable($ruta)) {
                // Las imagenes cargadas desde el modulo hospitalario antiguo se
                // guardaron directamente en public/, no en storage/app/hc_ima/.
                // Solo se intenta este respaldo para ese directorio clinico.
                $rutaNormalizada = str_replace('\\', '/', $ruta);
                if (strpos($rutaNormalizada, '/storage/app/hc_ima/') !== false) {
                    $rutaPublicaAntigua = public_path(basename($rutaNormalizada));
                    if (is_file($rutaPublicaAntigua) && is_readable($rutaPublicaAntigua)) {
                        $ruta = $rutaPublicaAntigua;
                    }
                }
            }
            if (!is_file($ruta) || !is_readable($ruta)) {
                // No dejamos que DomPDF dibuje la X de una imagen inexistente.
                return preg_match('/^(?:https?:)?\/\//i', $ruta) ? $coincidencia[0] : '';
            }

            $mime = function_exists('mime_content_type') ? mime_content_type($ruta) : null;
            if (!in_array($mime, array('image/png', 'image/jpeg', 'image/gif'), true)) {
                return '';
            }
            $imagen = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($ruta));

            if ($esCabeceraAntigua) {
                return '<img src="' . $imagen . '" alt="Logo de la empresa" style="display:block;max-width:180px;max-height:70px;width:auto;height:auto;margin:0 auto;">';
            }

            return str_replace($coincidencia[2], $imagen, $coincidencia[0]);
        }, $html);
    }

    //descargar el resumen del procedimiento
    public function descarga_resumen($id_protocolo, $tipo)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo = hc_protocolo::with(array('historiaclinica', 'procedimiento'))->find($id_protocolo);
        if (is_null($protocolo) || is_null($protocolo->historiaclinica) || is_null($protocolo->procedimiento)) {
            return response()->view('errors.404', array(), 404);
        }

        $procedimiento = $protocolo->procedimiento;
        $ayudante= User::find($protocolo->id_ayudante);
        $anestesiologo= User::find($protocolo->id_anestesiologo);
        //dd($protocolo);
        $imagenes = $this->imagenesParaResumen($id_protocolo);

        $paciente = paciente::find($protocolo->historiaclinica->id_paciente);
        if (is_null($paciente)) {
            return response()->view('errors.404', array(), 404);
        }
        $seguro   = Seguro::find($procedimiento->id_seguro);

        // Los procedimientos antiguos no siempre tienen id_doctor_examinador2.
        // Usamos la misma cadena de respaldo del protocolo para que el resumen
        // pueda generarse sin intentar leer la relacion doctor_firma en null.
        $idDoctorFirma = $procedimiento->id_doctor_examinador2;
        if (!is_null($procedimiento->id_doctor_responsable)) {
            $idDoctorFirma = $procedimiento->id_doctor_responsable;
        }
        if (is_null($idDoctorFirma)) {
            $idDoctorFirma = $procedimiento->id_doctor_examinador;
        }
        if (is_null($idDoctorFirma)) {
            $idDoctorFirma = $protocolo->historiaclinica->id_doctor1;
        }

        if (!is_null($idDoctorFirma)) {
            $firma = Firma_Usuario::where('id_usuario', $idDoctorFirma)->get();
            if (!is_null($seguro)) {
                if ($seguro->tipo == '0') {
                    if ($procedimiento->id_empresa == '0992704152001') {
                        if ($idDoctorFirma == '0924611882') {
                            $firma = Firma_Usuario::where('id_usuario', '094346835')->get();
                        }
                    }
                }
            }
        } else {
            $firma = collect();
        }
        //dd($firma);
        $edad                   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $procedimiento_completo = procedimiento_completo::find($procedimiento->id_procedimiento_completo);
        $historia               = Historiaclinica::find($protocolo->hcid);
        $doctor1                = !is_null($idDoctorFirma) ? User::find($idDoctorFirma) : null;
        $agendaPdf              = !is_null($historia) ? Agenda::find($historia->id_agenda) : null;
        $empresaPdf             = !is_null($agendaPdf) ? $agendaPdf->empresa : null;
        $logoEmpresaPath        = !is_null($empresaPdf)
            ? app(\Sis_medico\Services\PdfBrandingService::class)->companyLogoPath($empresaPdf->id)
            : null;
        if (is_null($logoEmpresaPath)) {
            $logoEmpresaPath = public_path('assets/images/aitrol-logo.png');
        }
       
        //$elasto = $protocolo->procedimiento->hc_procedimiento_final->where('id_procedimiento','26')->first();
        $elasto = Hc_Procedimiento_Final::where('id_hc_procedimientos', $protocolo->procedimiento->id)->where('id_procedimiento', '26')->first();
        //dd($elasto);
        if (!is_null($elasto)) {
            //dd('entra');
            $view = \View::make('hc_admision.formato.resumen_elasto', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma', 'elasto'))->render();
        } else {
            ///d('entra22');
            if ($tipo == 0) {
                $view = \View::make('hc_admision.formato.resumen_procedimiento', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            } elseif ($tipo == 1) {
                $view = \View::make('hc_admision.formato.resumen_procedimiento_sin_recorte', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            } elseif ($tipo == 2) {
                $view = \View::make('hc_admision.formato.resumen_2_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            } elseif ($tipo == 3) {
                $view = \View::make('hc_admision.formato.resumen_3_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            } elseif ($tipo == 4) {
                $view = \View::make('hc_admision.formato.resumen_4_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            } elseif ($tipo == 5) {
                $view = \View::make('hc_admision.formato.resumen_5_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            } elseif ($tipo == 6) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            } elseif ($tipo == 7) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('hc_admision.formato.resumen_7_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            } elseif ($tipo == 8) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('hc_admision.formato.resumen_8_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            } elseif ($tipo == 9) {
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('hc_admision.formato.resumen_9_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            } elseif ($tipo == 'rx') {
                $imagenes = hc_imagenes_protocolo::where('id_hc_protocolo', $id_protocolo)->where('seleccion_rx', '1')->where('estado', '1')->orderBy('id', 'desc')->get();
                //return view('hc_admision.formato.resumen_6_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma' ));
                $view = \View::make('hc_admision.formato.resumen_rx', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            }elseif ($tipo==11){
                $view = \View::make('hc_admision.formato.resumen_11_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            }elseif  ($tipo==10){
                $view = \View::make('hc_admision.formato.resumen_10_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            }elseif  ($tipo==12){
                $view = \View::make('hc_admision.formato.resumen_12_fotos', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma','anestesiologo','doctor1','ayudante'))->render();
            }
        }

        if (!isset($view)) {
            return response()->view('errors.404', array(), 404);
        }
        $view = $this->prepararImagenesResumenPdf($view, $logoEmpresaPath);

        //return view('hc_admision.formato.resumen_procedimiento', ['protocolo' => $protocolo, 'imagenes' => $imagenes, 'edad'=> $edad, 'paciente' => $paciente, 'procedimiento_completo' => $procedimiento_completo, 'historia' => $historia]);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setPaper('A4');

        $pdf->loadHTML($view);
        return $pdf->stream('Resumen_procedimiento_' . $protocolo->historiaclinica->id_paciente . '_' . $protocolo->hcid . '.pdf');
    }

    //modal para cambiar fecha
    public function descarga_resumen2($id_protocolo, $tipo)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo = hc_protocolo::find($id_protocolo);
        $doctores  = User::where('id_tipo_usuario', 3)->get();
        return view('hc_admision/video/fecha_firma', ['protocolo' => $protocolo, 'doctores' => $doctores, 'tipo' => $tipo]);

    }

    //guardado de fecha
    public function descarga_resumen3(Request $request)
    {
        //guardado de fecha y firma
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $id         = $request['id'];
        $protocolo  = hc_protocolo::find($id);
        $input_ex1  = [
            'fecha'           => $request['fecha'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        $procedimiento = hc_procedimientos::find($protocolo->id_hc_procedimientos);
        $input_ex2     = [
            'id_doctor_examinador2' => $request['id_doctor_examinador2'],
        ];
        $procedimiento->update($input_ex2);
        $protocolo->update($input_ex1);

        //descarga
        $id_protocolo = $request['id'];
        $tipo         = $request['tipo'];
        $idusuario    = Auth::user()->id;
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $rolUsuario   = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $protocolo = hc_protocolo::find($id_protocolo);
        $imagenes  = $this->imagenesParaResumen($id_protocolo);

        $paciente               = paciente::find($protocolo->historiaclinica->id_paciente);
        $firma                  = Firma_Usuario::where('id_usuario', $protocolo->procedimiento->doctor_firma->id)->get();
        $edad                   = Carbon::createFromDate(substr($paciente->fecha_nacimiento, 0, 4), substr($paciente->fecha_nacimiento, 5, 2), substr($paciente->fecha_nacimiento, 8, 2))->age;
        $procedimiento_completo = procedimiento_completo::find($protocolo->procedimiento->id_procedimiento_completo);
        $historia               = Historiaclinica::find($protocolo->hcid);
        $agendaPdf              = !is_null($historia) ? Agenda::find($historia->id_agenda) : null;
        $empresaPdf             = !is_null($agendaPdf) ? $agendaPdf->empresa : null;
        $logoEmpresaPath        = !is_null($empresaPdf)
            ? app(\Sis_medico\Services\PdfBrandingService::class)->companyLogoPath($empresaPdf->id)
            : null;
        if (is_null($logoEmpresaPath)) {
            $logoEmpresaPath = public_path('assets/images/aitrol-logo.png');
        }

        if ($tipo == 0) {
            $view = \View::make('hc_admision.formato.resumen_procedimiento', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
        } else {
            $view = \View::make('hc_admision.formato.resumen_procedimiento_sin_recorte', compact('protocolo', 'imagenes', 'paciente', 'edad', 'procedimiento_completo', 'historia', 'firma'))->render();
        }

        $view = $this->prepararImagenesResumenPdf($view, $logoEmpresaPath);

        //return view('hc_admision.formato.resumen_procedimiento', ['protocolo' => $protocolo, 'imagenes' => $imagenes, 'edad'=> $edad, 'paciente' => $paciente, 'procedimiento_completo' => $procedimiento_completo, 'historia' => $historia]);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setPaper('A4');

        $pdf->loadHTML($view);
        return $pdf->stream('Resumen_procedimiento_' . $protocolo->historiaclinica->id_paciente . '_' . $protocolo->hcid . '.pdf');
    }

    public function actualizar_doctor_seguro(Request $request)
    {
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $input_hc_procedimiento = [
            'id_doctor_examinador' => $request["id_doctor_examinador"],
            'id_seguro'            => $request["id_seguro"],
            'id_empresa'           => $request["id_empresa"],
            'observaciones'        => $request["observaciones"],
            'ip_modificacion'      => $ip_cliente,
            'id_usuariomod'        => $idusuario,
        ];

        $id_hc_procedimiento = $request['id_hc_procedimiento'];
        //return $input_evo;
        hc_procedimientos::where('id', $id_hc_procedimiento)
            ->update($input_hc_procedimiento);
        return "ok";
    }

    public function Crear_Editar($agenda, $cant_cortesias, $proc_completo, $protocolo, $doctores, $anestesiologos, $enfermeros, $seguros, $procedimientos_pentax, $imagenes, $id_agenda, $documentos, $estudios, $biopsias, $hc_receta)
    {

        $alergiasxpac = Paciente_Alergia::where('id_paciente', $agenda->id_paciente)->get();

        //$evoluciones_proc = DB::table('hc_evolucion as e')->where('e.hc_id_procedimiento',$protocolo->id_hc_procedimientos)->get();

        $evoluciones_proc    = DB::table('hc_evolucion as e')->join('historiaclinica as h', 'e.hcid', 'h.hcid')->join('agenda as a', 'a.id', 'h.id_agenda')->orderBy('a.fechaini', 'desc')->select('e.*', 'a.fechaini', 'a.proc_consul', 'a.espid')->where('e.hc_id_procedimiento', $protocolo->id_hc_procedimientos)->get();
        $id_hc_procedimiento = $protocolo->id_hc_procedimientos;
        $hc_procedimiento    = null;
        //dd($agenda);
        $historia         = historiaclinica::find($agenda->hcid);
        $hc_procedimiento = hc_procedimientos::find($id_hc_procedimiento);
        if ($hc_procedimiento->id_seguro == null) {
            $input_procedimiento = [
                'id_seguro' => $agenda->id_seguro,
            ];
            hc_procedimientos::where('id', $id_hc_procedimiento)->update($input_procedimiento);
            $hc_procedimiento = hc_procedimientos::find($id_hc_procedimiento);
        }
        if ($hc_procedimiento->id_doctor_examinador == null) {
            $input_procedimiento = [
                'id_doctor_examinador' => $historia->id_doctor1,
            ];
            hc_procedimientos::where('id', $id_hc_procedimiento)->update($input_procedimiento);
            $hc_procedimiento = hc_procedimientos::find($id_hc_procedimiento);
        }
        //dd($hc_procedimiento);
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        if (is_null($hc_receta)) {
            $input_hc_receta = [
                'id_hc'           => $agenda->hcid,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea'  => $idusuario,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
            $id_receta = hc_receta::insertGetId($input_hc_receta);
            $hc_receta = hc_receta::find($id_receta);

        }

        $examenes_externos = Paciente_Biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', 1)->get();
        $biopsias_1        = Paciente_biopsia::where('id_paciente', $agenda->id_paciente)->where('estado', '0')->get();
        //$biopsias = hc_imagenes_protocolo::where('id_hc_protocolo',$protocolo->id)->where('estado', '4')->get();
        $biopsias_2 = DB::table('historiaclinica')->join('hc_protocolo', 'historiaclinica.hcid', '=', 'hc_protocolo.hcid')
            ->join('hc_imagenes_protocolo', 'hc_protocolo.id', '=', 'hc_imagenes_protocolo.id_hc_protocolo')
            ->where('id_paciente', $agenda->id_paciente)
            ->where('hc_imagenes_protocolo.estado', '4')->get();

        return view('hc_admision/procedimientos/procedimiento', ['agenda' => $agenda, 'cant_cortesias' => $cant_cortesias, 'proc_completo' => $proc_completo, 'protocolo' => $protocolo, 'doctores' => $doctores, 'anestesiologos' => $anestesiologos, 'enfermeros' => $enfermeros, 'seguros' => $seguros, 'procedimientos_pentax' => $procedimientos_pentax, 'imagenes' => $imagenes, 'documentos' => $documentos, 'biopsias' => $biopsias, 'estudios' => $estudios, 'id_agenda' => $id_agenda, 'hc_receta' => $hc_receta, 'alergiasxpac' => $alergiasxpac, 'evoluciones_proc' => $evoluciones_proc, 'hc_procedimiento' => $hc_procedimiento, 'laboratorio_externo' => $examenes_externos, 'biopsias_1' => $biopsias_1, 'biopsias_2' => $biopsias_2]);

    }

    public function estudio_lista($id)
    {

        $agenda = DB::table('agenda')
            ->join('paciente', 'agenda.id_paciente', '=', 'paciente.id')
            ->join('seguros', 'paciente.id_seguro', '=', 'seguros.id')
            ->join('users as uc', 'uc.id', 'agenda.id_usuariocrea')
            ->join('users as um', 'um.id', 'agenda.id_usuariomod')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->leftjoin('pentax as p', 'p.id_agenda', 'agenda.id')
            ->leftjoin('sala', 'agenda.id_sala', 'sala.id')
            ->leftjoin('hospital', 'sala.id_hospital', 'hospital.id')
            ->leftjoin('users as ud', 'ud.id', 'agenda.id_doctor1')
            ->leftjoin('especialidad', 'especialidad.id', 'agenda.espid')
            ->leftjoin('empresa', 'empresa.id', 'agenda.id_empresa')
            ->leftjoin('procedimiento', 'procedimiento.id', 'agenda.id_procedimiento')
            ->select('agenda.*', 'paciente.nombre1 as pnombre1', 'paciente.nombre2 as pnombre2', 'paciente.apellido1 as papellido1', 'paciente.apellido2 as papellido2', 'paciente.alergias as palergias', 'paciente.antecedentes_pat as pantecedentes_pat', 'paciente.antecedentes_fam as pantecedentes_fam', 'paciente.antecedentes_quir as pantecedentes_quir', 'seguros.color as color', 'uc.apellido1 as ucapellido', 'um.apellido1 as umapellido', 'uc.nombre1 as ucnombre', 'um.nombre1 as umnombre', 'paciente.fecha_nacimiento', 'paciente.ocupacion', 'h.parentesco as hparentesco', 'paciente.parentesco as pparentesco', 'paciente.estadocivil', 'paciente.ciudad', 'paciente.lugar_nacimiento', 'paciente.direccion', 'paciente.telefono1', 'paciente.telefono2', 'seguros.nombre as snombre', 'sala.nombre_sala as slnombre', 'hospital.nombre_hospital as hsnombre', 'ud.nombre1 as udnombre', 'ud.apellido1 as udapellido', 'especialidad.nombre as esnombre', 'procedimiento.nombre as pnombre', 'empresa.nombrecomercial', 'paciente.sexo', 'paciente.gruposanguineo', 'paciente.transfusion', 'paciente.alergias', 'paciente.vacuna', 'paciente.historia_clinica', 'paciente.antecedentes_pat', 'paciente.antecedentes_fam', 'paciente.antecedentes_quir', 'h.hcid', 'p.estado_pentax')
            ->where('agenda.id', '=', $id)
            ->first();

        $fecha_dia = date('Y-m-d', strtotime($agenda->fechaini));

        $nuevafecha = strtotime('+1 day', strtotime($fecha_dia));
        $nuevafecha = date('Y-m-d', $nuevafecha);

        $cant_cortesias = Agenda::where('id_doctor1', $agenda->id_doctor1)->where('fechaini', '>', $fecha_dia)->where('fechaini', '<', $nuevafecha)->where('cortesia', 'SI')->count();

        $protocolos = DB::table('historiaclinica as h')->where('h.id_paciente', $agenda->id_paciente)->join('hc_protocolo as p', 'h.hcid', 'p.hcid')->join('hc_procedimientos as hc', 'hc.id', 'p.id_hc_procedimientos')->join('agenda as a', 'a.id', 'h.id_agenda')->leftjoin('procedimiento_completo as pc', 'pc.id', 'hc.id_procedimiento_completo')->leftjoin('hc_receta as r', 'r.id_hc', 'h.hcid')->where('a.proc_consul', '1')->leftjoin('users as d1', 'h.id_doctor1', 'd1.id')->leftjoin('users as d2', 'h.id_doctor2', 'd2.id')->leftjoin('users as d3', 'h.id_doctor3', 'd3.id')->select('h.*', 'p.id as protocolo', 'hc.id_procedimiento_completo', 'a.fechaini', 'pc.nombre_general', 'p.hallazgos', 'd1.apellido1 as d1apellido1', 'd2.apellido1 as d2apellido1', 'd3.apellido1 as d3apellido1')->orderBy('p.created_at', 'desc')->get();

//dd($protocolos);
        return view('hc_admision/procedimientos/estudios', ['agenda' => $agenda, 'cant_cortesias' => $cant_cortesias, 'protocolos' => $protocolos]);

    }

    public function ruta($id)
    {

        $agenda = DB::table('agenda')
            ->leftjoin('historiaclinica as h', 'h.id_agenda', 'agenda.id')
            ->select('agenda.*', 'h.hcid')
            ->where('agenda.id', '=', $id)
            ->first();

        if (!is_null($agenda->hcid)) {

            $protocolo = DB::table('historiaclinica as h')->where('h.hcid', $agenda->hcid)->join('hc_protocolo as p', 'h.hcid', 'p.hcid')->join('hc_procedimientos as pc', 'pc.id', 'p.id_hc_procedimientos')->join('hc_receta as r', 'r.id_hc', 'h.hcid')->select('h.*', 'p.motivo', 'p.hallazgos', 'p.id_hc_procedimientos', 'pc.id_procedimiento_completo', 'p.id as protocolo', 'r.*')->orderBy('h.created_at', 'desc')->first();

            if ($agenda->estado_cita == '4') {
                if (is_null($protocolo)) {
                    return redirect()->route('agenda.detalle', ['id' => $id]); //filiación
                }
            }
        } else {
            return redirect()->route('agenda.detalle', ['id' => $id]); //filiación
        }

        if (!is_null($protocolo->id_procedimiento_completo)) {

            return redirect()->route('estudio.lista', ['id' => $id]); //estudios

        } else {

            return $this->estudio_agregar($id); //crear

        }

    }

    public function actualiza_paciente(Request $request)
    {
        //return $request->all();
        $idusuario  = Auth::user()->id;
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        date_default_timezone_set('America/Guayaquil');
        $id                   = $request["id_paciente"];
        $id_hc_procedimientos = $request['id_hc_procedimientos'];
        $hcid                 = $request['hcid'];
        $protocolo            = $request['protocolo'];

        $proc_com = procedimiento_completo::find($request['proc_com']);

        $input1 = [
            'alergias'        => $request["alergias"],
            'observacion'     => $request["observacion"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        Paciente::where('id', $id)->update($input1);

        $input2 = [
            'id_procedimiento_completo' => $request['proc_com'],
            'ip_modificacion'           => $ip_cliente,
            'id_usuariomod'             => $idusuario,
        ];
        hc_procedimientos::find($id_hc_procedimientos)->update($input2);

        $input3 = [
            'id_doctor1'      => $request['id_doctor1'],
            'id_doctor2'      => $request['id_doctor2'],
            'id_doctor3'      => $request['id_doctor3'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        //Historiaclinica::find($hcid)->update($input3);
        //Pentax::where('hcid',$hcid)->update($input3);

        $input4 = [
            'ip_modificacion'    => $ip_cliente,
            'id_usuariomod'      => $idusuario,
            'id_anestesiologo'   => $request['id_anestesiologo'],
            'motivo'             => $request['motivo'],
            'conclusion'         => $request['conclusion'],
            'hallazgos'          => $request['hallazgos'],
            'complicacion'       => $request['complicacion'],
            'estado_paciente'    => $request['estado_paciente'],
            'plan'               => $request['plan'],
            'estudio_patologico' => $request['estudio_patologico'],
        ];

        hc_protocolo::find($protocolo)->update($input4);

        $hc_anestesiologia = Hc_Anestesiologia::where('id_hc_procedimientos', $id_hc_procedimientos)->first();

        if (!is_null($proc_com)) {

            $an_var = $proc_com->estado_anestesia;

        } else {

            $an_var = '1';

        }

        $input_an_crea = [
            'id_hc'                => $hcid,
            'id_hc_procedimientos' => $id_hc_procedimientos,
            'id_anestesiologo'     => $request['id_anestesiologo'],
            'estado'               => $an_var,
            'ip_modificacion'      => $ip_cliente,
            'id_usuariomod'        => $idusuario,
            'ip_creacion'          => $ip_cliente,
            'id_usuariocrea'       => $idusuario,
        ];

        $input_an_mod = [

            'id_anestesiologo' => $request['id_anestesiologo'],
            'estado'           => $an_var,
            'ip_modificacion'  => $ip_cliente,
            'id_usuariomod'    => $idusuario,

        ];

        if (!is_null($hc_anestesiologia)) {

            $hc_anestesiologia->update($input_an_mod);

        } else {

            if (!is_null($proc_com)) {

                if ($proc_com->estado_anestesia == '1') {

                    Hc_Anestesiologia::create($input_an_crea);

                }

            }

        }

        return "ok";

    }

    public function tecnica(Request $request)
    {
        //return $request->all();
        $proc_com = $request['proc_com'];

        $procedimiento = procedimiento_completo::find($proc_com);

        return $procedimiento;

    }

    public function load($name)
    {
        //dd($name);
        $path = storage_path() . '/app/hc_ima/' . $name;
        if (file_exists($path)) {
            ob_end_clean();
            return Response::download($path);
        }
    }
    //cargar de images de grupo_procedimientos
    public function load3($name)
    {
        $imagen          = hc_imagenes_protocolo::find($name);
        $protocolo       = $imagen->protocolo()->first();
        $historia        = $protocolo->historiaclinica()->first();
        $paciente        = $historia->paciente()->first();
        $nombre_paciente = null;
        $nombre_paciente = $paciente->apellido1;
        $path            = storage_path() . '/app/hc_ima/' . $imagen->nombre;

        $nombre_paciente = $nombre_paciente . '_' . $paciente->nombre1;

        //dd($path);

        if ($nombre_paciente == null) {
            $nombre_archivo = $imagen->nombre;
        } else {
            $nombre_temporal = $imagen->nombre;
            $datos           = explode(".", $nombre_temporal);
            if (count($datos) == 2) {
                $extension      = $datos[1];
                $nombre_archivo = $nombre_paciente . '.' . $extension;
                if ($extension == 'mp4') {
                    $path = public_path('uploads/') . $imagen->nombre;
                }
            } else {
                $nombre_archivo = $imagen->nombre;
            }

        }

        //dd($path);
        if (file_exists($path)) {
            ob_end_clean();
            return Response::download($path, $nombre_archivo);
        }
    }

    public function seleccion_descargar($id_protocolo)
    {
        return view('hc_admision/video/modal_seleccion', ['id_protocolo' => $id_protocolo]);
    }

}
