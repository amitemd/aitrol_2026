<?php

namespace Sis_medico\Http\Controllers\hc4;

use Carbon\Carbon;
use Excel;
use Exception;
use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Cortesia_paciente;
use Sis_medico\Ct_Rol_Pagos;
use Sis_medico\Empresa;
use Sis_medico\Encuesta_Preguntas_Test;
use Sis_medico\Especialidad;
use Sis_medico\EstimadoSeguros;
use Sis_medico\Examen_Orden;
use Sis_medico\Excepcion_Horario;
use Sis_medico\Hc_Evolucion;
use Sis_medico\hc_imagenes_protocolo;
use Sis_medico\Hc_Log;
use Sis_medico\hc_protocolo_cirugia;
use Sis_medico\hc_procedimientos;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\hc_protocolo;
use Sis_medico\Historiaclinica;
use Sis_medico\Horario_Doctor;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Agenda;
use Sis_medico\Log_usuario;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Pais;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Pregunta_Test_Valores;
use Sis_medico\Procedimiento;
use Sis_medico\Prueba_pulmonar;
use Sis_medico\Seguro;
use Sis_medico\Test_Encuesta;
use Sis_medico\TipoUsuario;
use Sis_medico\Hc4_Polipos_Ubicacion;
use Sis_medico\Hc4_Escala_Boston;
use Sis_medico\procedimiento_completo;
use Sis_medico\User;
use Sis_medico\Exceptions\ClinicalEncounterConflictException;
use Sis_medico\Services\ClinicalEncounterService;

class ProcedimientosQuirurgicosController extends Controller
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

    public function index1($id_paciente)
    { {
            $opcion = '2';
            if ($this->rol_new($opcion)) {
                return redirect('/');
            }

            $paciente       = Paciente::find($id_paciente);
            // $pro_completo_0 = DB::table('historiaclinica as h')
            //     ->where('h.id_paciente', $id_paciente)
            //     ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
            //     ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
            //     ->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            //     ->join('agenda as a', 'a.id', 'h.id_agenda')
            //     ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            //     ->join('grupo_procedimiento as gp', 'gp.id', 'pc.id_grupo_procedimiento')
            //     ->where('gp.tipo_procedimiento', '0')
            //     ->where('hc_p.estado', '1')
            //     ->select('pc.nombre_general as nombre', 'u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'gp.tipo_procedimiento', 'h.hcid', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_p.id_seguro as seguro_final', 'a.id as id_agenda', 'hc_p.cuadro_clinico_bp as cuadro_clinico', 'hc_p.diagnosticos_bp as diagnosticos')
            //     ->OrderBy('a.fechaini', 'desc')->get();

            //dd($pro_completo_0);

            $pro_final_0 = DB::table('historiaclinica as h')
                ->where('h.id_paciente', $id_paciente)
                ->join('hc_protocolo as hc_proto', 'hc_proto.hcid', 'h.hcid')
                ->join('agenda as a', 'a.id', 'h.id_agenda')
                ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.id_hc_procedimientos')
                ->join('users as u', 'u.id', 'hc_p.id_doctor_examinador')
                ->where('hc_proto.tipo_procedimiento', '5')
                ->where('hc_p.estado', '1')
                ->select('u.nombre1', 'u.apellido1', 'u.id', 'hc_p.id_doctor_examinador', 'hc_proto.hallazgos', 'hc_proto.conclusion', 'hc_proto.id as id_protocolo', 'hc_proto.id_hc_procedimientos as id_procedimiento', 'h.hcid as id_hc', 'hc_proto.fecha_operacion as f_operacion', 'h.id_agenda as id_agenda', 'h.id_seguro as hc_id_seguro', 'hc_p.id_seguro as seguro_final', 'a.id as id_agenda', 'hc_p.cuadro_clinico_bp as cuadro_clinico', 'hc_p.diagnosticos_bp as diagnosticos')->OrderBy('a.fechaini', 'desc')->get();
            //dd($)

            //dd($pro_completo_0);
            //dd($pro_final_0);

            return view('hc4/procedimiento_quirurgicos/index1', ['paciente' => $paciente, 'procedimientos2' => $pro_final_0]);
        }
    }

    public function selecciona_quirurgicos($tipo, $paciente)
    {

        //'0: endoscopico, 1: funcional, 2:imagen, 3:consulta', 4:broncoscopias
        $px = Procedimiento::with('grupo_procedimiento')
            ->where('procedimiento.estado', '1')
            ->orderBy('procedimiento.nombre')
            ->get();

        $paciente = Paciente::find($paciente);

        //dd($px);
        return view('hc4.selecciona_quirurgicos', ['px' => $px, 'paciente' => $paciente, 'tipo' => $tipo]);
    }




    public function crear_procedimiento_quirurgicos(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor = Auth::user()->id;

        $procedimientos = array_values(array_filter((array) $request->input('procedimiento', [])));

        if (empty($procedimientos)) {
            return response('seleccione', 200);
        }

        $procedimientop = $procedimientos[0];
        $paciente = Paciente::find($request->paciente);
        $sala = \Sis_medico\Sala::where('proc_consul_sala', 1)->first();

        $empresa = Empresa::where('prioridad', '1')->first();
        $encounterService = app(ClinicalEncounterService::class);
        $encounter = null;
        if ($encounterService->available()) {
            try {
                $encounter = $encounterService->openOrReuse($paciente->id, ClinicalEncounterService::AMBULATORY, [
                    'id_empresa' => $empresa ? $empresa->id : null,
                    'started_at' => date('Y-m-d H:i:s'),
                    'actor_id' => $id_doctor,
                    'ip' => $ip_cliente,
                ]);
            } catch (ClinicalEncounterConflictException $exception) {
                return response($exception->getMessage(), 409);
            }
        }
        //dd($empresa);
        $procedimiento_crear_new = [
            'anterior' => 'PROC_ENDOSCOPICOS -> El Dr. creo nuevo procedimiento endoscopico',
            'nuevo' => 'PROC_ENDOSCOPICOS -> El Dr. creo nuevo procedimiento endoscopico',
            'id_paciente' => $paciente->id,
            'id_usuariomod' => $id_doctor,
            'id_usuariocrea' => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion' => $ip_cliente,
        ];
        Hc_Log::create($procedimiento_crear_new);

        if ($procedimientos != null) {

            $input_agenda = [
                'fechaini' => Date('Y-m-d H:i:s'),
                'fechafin' => Date('Y-m-d H:i:s'),
                'id_paciente' => $paciente->id,
                'id_doctor1' => $id_doctor,
                'proc_consul' => '4',
                'estado_cita' => '4',
                'id_empresa' => $empresa->id,
                'espid' => '4',
                'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'id_seguro' => $paciente->id_seguro,
                'estado' => '4',
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $id_doctor,
                'id_usuariomod' => $id_doctor,
                'id_procedimiento' => $procedimientop,
                'id_sala' => $sala->id,
            ];

            $id_agenda = agenda::insertGetId($input_agenda);
            if ($encounter) {
                $encounterService->link($encounter, ['agenda' => $id_agenda]);
            }
            //return $id_agenda;

            $txt_pro = '';
            foreach ($procedimientos as $value) {

                if ($procedimientop != $value) {
                    $txt_pro = $txt_pro . '+' . $value;
                    AgendaProcedimiento::create([
                        'id_agenda' => $id_agenda,
                        'id_procedimiento' => $value,
                        'ip_creacion' => $ip_cliente,
                        'ip_modificacion' => $ip_cliente,
                        'id_usuariocrea' => $id_doctor,
                        'id_usuariomod' => $id_doctor,
                    ]);
                }
            }

            $input_log = [
                'id_agenda' => $id_agenda,
                'estado_cita_ant' => '0',
                'estado_cita' => '0',
                'fechaini' => Date('Y-m-d H:i:s'),
                'fechafin' => Date('Y-m-d H:i:s'),
                'estado' => '4',
                'observaciones' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'id_doctor1' => $id_doctor,
                'descripcion' => 'PROCEDIMIENTO CREADO POR EL DOCTOR',
                'campos_ant' => 'PRO: ' . $procedimientop . $txt_pro,

                'id_usuariomod' => $id_doctor,
                'id_usuariocrea' => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion' => $ip_cliente,
            ];

            $idusuario = $id_doctor;

            Log_agenda::create($input_log);

            $input_historia = [

                'parentesco' => $paciente->parentesco,
                'id_usuario' => $paciente->id_usuario,
                'id_agenda' => $id_agenda,
                'id_paciente' => $paciente->id,
                'id_seguro' => $paciente->id_seguro,

                'id_doctor1' => $id_doctor,
                'id_usuariocrea' => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $id_doctor,
                'ip_creacion' => $ip_cliente,

            ];

            $id_historia = Historiaclinica::insertGetId($input_historia);
            if ($encounter) {
                $encounterService->link($encounter, [
                    'agenda' => $id_agenda,
                    'historiaclinica' => $id_historia,
                ]);
            }

            $input_pentax = [
                'id_agenda' => $id_agenda,
                'hcid' => $id_historia,
                'id_sala' => $sala->id,
                'id_doctor1' => $idusuario,
                'id_seguro' => $paciente->id_seguro,
                'observacion' => "PROCEDIMIENTO CREADO POR EL DOCTOR",
                'id_usuariocrea' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
            ];

            $id_pentax = Pentax::insertGetId($input_pentax);

            $list_proc = '';
            foreach ($procedimientos as $value) {
                $input_pentax_pro2 = [
                    'id_pentax' => $id_pentax,
                    'id_procedimiento' => $value,
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,
                ];

                PentaxProc::create($input_pentax_pro2);
                $list_proc = $list_proc . "+" . $value;
            }

            $input_log_px = [
                'id_pentax' => $id_pentax,
                'tipo_cambio' => "CREADO POR EL DOCTOR",
                'descripcion' => "EN ESPERA",
                'estado_pentax' => '0',
                'procedimientos' => $list_proc,
                'id_doctor1' => $idusuario,
                'observacion' => "CREADO POR EL DOCTOR",
                'id_sala' => $sala->id,
                'id_seguro' => $paciente->id_seguro,

                'ip_modificacion' => $ip_cliente,
                'ip_creacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
                'id_usuariocrea' => $idusuario,
            ];

            Pentax_log::create($input_log_px);

            $input_hc_procedimiento = [
                'id_hc' => $id_historia,
                'id_seguro' => $paciente->id_seguro,
                'id_doctor_examinador' => $idusuario,
                'id_doctor_examinador2' => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,

            ];

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);


            $input_hc_protocolo = [
                'fecha' => date('Y-m-d'),
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'hora_inicio' => date('H:i:s'),
                'hora_fin' => date('H:i:s'),
                'estado_final' => ' ',
                'ip_modificacion' => $ip_cliente,
                'hcid' => $id_historia,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'tipo_procedimiento' => $request->tipo_procedimiento,
            ];

            $id_hc_protocolo = hc_protocolo::insertGetId($input_hc_protocolo);

            $input_hc_cirugia = [

                'id_hc_protocolo' => $id_hc_protocolo,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'tipo_procedimiento' => $request->tipo_procedimiento,


            ];

            hc_protocolo_cirugia::create($input_hc_cirugia);

            foreach ($procedimientos as $value) {
                $input_pro_final = [
                    'id_hc_procedimientos' => $id_hc_procedimiento,
                    'id_procedimiento' => $value,
                    'id_usuariocrea' => $idusuario,
                    'ip_modificacion' => $ip_cliente,
                    'id_usuariomod' => $idusuario,
                    'ip_creacion' => $ip_cliente,

                ];

                Hc_Procedimiento_Final::create($input_pro_final);
            }

            return "ok";
        }

        return "Ingrese el Procedimiento";
    }


    public function editar_protocolo($id_procedimiento, $id_paciente)
    {

        //dd($proto_hcid);
        $protocolo = hc_protocolo::where('id_hc_procedimientos', $id_procedimiento)->first();
        //dd($protocolo->id_hc_procedimientos);


        $id_seguro_pro = hc_procedimientos::where('id', $id_procedimiento)->first();
        //dd($id_seguro_pro);
        $hc_seguro = Seguro::where('id', $id_seguro_pro->id_seguro)->first();
        $px        = Procedimiento::where('procedimiento.estado', '1')->get();
        //dd($hc_seguro->nombre);

        $procedimiento = hc_procedimientos::findOrFail($id_procedimiento);
        $this->ensureCurrentSurgicalEncounter($procedimiento);
        $tipo          = 0;
        $doctores      = DB::table('users')->where('id_tipo_usuario', '=', 3)->where('estado', '1')->orderBy('apellido1')->get();


        $proc_completo = procedimiento_completo::all();
        $consentFormats = \Sis_medico\Formato_consentimiento::where('estado', 1)
            ->where(function ($query) use ($procedimiento) {
                $history = $procedimiento->historia;
                $companyId = $history && $history->agenda ? $history->agenda->id_empresa : null;
                $query->whereNull('id_empresa')->orWhere('id_empresa', $companyId);
            })->orderBy('nombre')->get();
        $polipos_ubicacion = Hc4_Polipos_Ubicacion::where('estado', '1')->orderBy('nombre')->get();
        $escala_boston = Hc4_Escala_Boston::where('id_paciente', $id_paciente)->where('id_hc_protocolo', $protocolo->id)->where('id_hc_procedimiento', $id_procedimiento)->first();
        //dd($procedimiento);

        $id_protocolo_cirugia = hc_protocolo_cirugia::where("id_hc_protocolo", $protocolo->id)->first();
        //dd($id_protocolo_cirugia->id_hc_protocolo);


        return view('hc4/procedimiento_quirurgicos/editar2', ['id_protocolo_cirugia' => $id_protocolo_cirugia, 'polipos_ubicacion' => $polipos_ubicacion, 'protocolo' => $protocolo, 'procedimiento' => $procedimiento, 'tipo' => $tipo, 'id_paciente' => $id_paciente, 'hc_seguro' => $hc_seguro, 'px' => $px, 'doctores' => $doctores, 'proc_completo' => $proc_completo, 'procedimiento_completo_plantilla' => null, 'escala_boston' => $escala_boston, 'consentFormats' => $consentFormats]);
    }

    private function ensureCurrentSurgicalEncounter(hc_procedimientos $procedimiento)
    {
        $historia = $procedimiento->historia;
        if (!$historia || !empty($historia->encounter_id)) {
            return;
        }

        $agenda = $historia->agenda;
        if (!$agenda || date('Y-m-d', strtotime($agenda->fechaini)) !== date('Y-m-d')) {
            return;
        }

        $service = app(ClinicalEncounterService::class);
        if (!$service->available()) {
            return;
        }

        $encounter = $service->findBySource('agenda', $agenda->id);
        if (!$encounter) {
            $encounter = $service->openOrReuse($historia->id_paciente, ClinicalEncounterService::AMBULATORY, [
                'id_empresa' => $agenda->id_empresa,
                'started_at' => $agenda->fechaini,
                'actor_id' => Auth::user()->id,
                'ip' => request()->ip(),
            ]);
        }

        $service->link($encounter, [
            'agenda' => $agenda->id,
            'historiaclinica' => $historia->hcid,
        ]);
        $procedimiento->unsetRelation('historia');
    }





    public function guardar_protocolo(Request $request)
    {
        //dd($request->all());
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        //dd($request['id_procedimiento']);

        $id_seguro_pro  = hc_procedimientos::where('id', $request['id_procedimiento'])->first();

        $procedimientos = array_values(array_filter((array) $request->input('procedimiento', [])));
        if (empty($procedimientos)) {
            return "No puede Guardar sin Procedimiento";
        }

        $hc_seguro = Seguro::where('id', $id_seguro_pro->id_seguro)->first();


        //dd($request);

        $protocolo     = hc_protocolo::find($request['id_protocolo']);
        $procedimiento = hc_procedimientos::find($request['id_procedimiento']);

        try {
            app(\Sis_medico\Services\ClinicalProcedureSafetyService::class)->authorizeExistingProtocol(
                $procedimiento->id,
                $protocolo->id,
                $request->all(),
                array('actor_id' => $idusuario, 'ip' => $ip_cliente)
            );
        } catch (\Sis_medico\Exceptions\ClinicalProcedureBlockedException $exception) {
            return response($exception->getMessage(), 422);
        }

        $protocolo_new     = $protocolo;
        $procedimiento_new = $procedimiento;

        if ($protocolo_new != null && $procedimiento_new != null) {

            $procedimientos_new = [
                'anterior'         => 'PROC_ENDOS -> Conclusion: ' . $protocolo_new->conclusion . ' hallazgos:' . $protocolo_new->hallazgos . ' doctor: ' . $procedimiento_new->id_doctor_examinador,
                'nuevo'            => 'PROC_ENDOS -> Conclusion: ' . $request['conclusion'] . ' hallazgos:' . $request['hallazgos'] . ' doctor:' . $request['id_doctor_examinador'],
                'hc_id'            => $protocolo_new->hcid,
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $procedimiento_new->id,
                'id_protocolo'     => $protocolo_new->id,
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($procedimientos_new);
        }

        $input = [
            'conclusion'         => $request['conclusion'],
            'hallazgos'          => $request['hallazgos'],
            'tipo_procedimiento' => '5',
            'ip_modificacion'    => $ip_cliente,
            'id_usuariomod'      => $idusuario,
        ];
        if (($request['id_doctor_examinador'] == '9666666666') || ($request['id_doctor_examinador'] == 'GASTRO')) {
            $input2 = [
                'id_doctor_examinador'      => $idusuario,
                'ip_modificacion'           => $ip_cliente,
                'id_procedimiento_completo' => null,
                'id_usuariomod'             => $idusuario,
            ];
        } else {
            $input2 = [
                'id_doctor_examinador'      => $request['id_doctor_examinador'],
                'ip_modificacion'           => $ip_cliente,
                'id_procedimiento_completo' => null,
                'id_usuariomod'             => $idusuario,
            ];
        }

        $procedimiento->update($input2);
        $protocolo->update($input);
        $anteriores = Hc_Procedimiento_Final::where('id_hc_procedimientos', $request['id_procedimiento']);

        //dd($procedimientos);
        $colono = 0;
        if (!empty($procedimientos)) {
            $anteriores->delete();
            $colono = 0;
            foreach ($procedimientos as $value) {
                if ($value == '9') {
                    $colono = 1;
                }
                $input_pro_final = [
                    'id_hc_procedimientos' => $request['id_procedimiento'],
                    'id_procedimiento'     => $value,
                    'id_usuariocrea'       => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                ];

                Hc_Procedimiento_Final::create($input_pro_final);
            }
        }

        //dd($colono);

        if ($colono == '1') {
            $boston = Hc4_Escala_Boston::where('id_paciente', $request['id_paciente'])->where('id_hc_protocolo', $request['id_protocolo'])->where('id_hc_procedimiento', $request['id_procedimiento'])->first();

            if (is_null($boston)) {
                $escala_boston = [
                    'id_paciente'      => $request['id_paciente'],
                    'id_hc_protocolo'  => $request['id_protocolo'],
                    'id_hc_procedimiento' => $request['id_procedimiento'],
                    'colon_derecho'     => $request['colon_derecho'],
                    'colon_transverso'  => $request['colon_transverso'],
                    'colon_izquierdo'  => $request['colon_izquierdo'],
                    'puntaje_final'  => $request['puntaje_final'],
                    'polipo_adenomatosos'  => $request['resp_polipo_adenomatosos'],
                    'ubicacion_polipo_adenomatosos'  =>  $request['ubicacion_polipo_adenomatosos'],
                    'tamano_polipo_adenomatosos'  =>  $request['tamano_adenomatosos'],
                    'polipo_hiperplasico'  => $request['resp_polipo_hiperplasico'],
                    'ubicacion_polipo_hiperplasico'  => $request['ubicacion_polipo_hiperplasico'],
                    'tamano_polipo_hiperplasico'  =>  $request['tamano_hiperplasico'],
                    'intubacion'  =>  $request['intubacion'],
                    'id_usuariomod'    => $idusuario,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'ip_creacion'      => $ip_cliente,
                ];
                Hc4_Escala_Boston::create($escala_boston);
            } else {
                $boston->colon_derecho = $request['colon_derecho'];
                $boston->colon_transverso = $request['colon_transverso'];
                $boston->colon_izquierdo = $request['colon_izquierdo'];
                $boston->puntaje_final = $request['puntaje_final'];
                $boston->polipo_adenomatosos = $request['resp_polipo_adenomatosos'];
                $boston->ubicacion_polipo_adenomatosos = $request['ubicacion_polipo_adenomatosos'];
                $boston->tamano_polipo_adenomatosos = $request['tamano_adenomatosos'];
                $boston->polipo_hiperplasico = $request['resp_polipo_hiperplasico'];
                $boston->ubicacion_polipo_hiperplasico = $request['ubicacion_polipo_hiperplasico'];
                $boston->tamano_polipo_hiperplasico = $request['tamano_hiperplasico'];
                $boston->intubacion = $request['intubacion'];
                $boston->id_usuariomod = $idusuario;
                $boston->ip_modificacion = $ip_cliente;
                $boston->save();
            }
        }

        return view('hc4/procedimiento_quirurgicos/unico_quirurgico', ['hc_seguro' => $hc_seguro, 'protocolo' => $protocolo, 'procedimiento' => $procedimiento, 'tipo' => $request['tipo']]);
    }

    public function guardar_autoguardado(Request $request)
    {
        $procedimientos = array_values(array_filter((array) $request->input('procedimiento', [])));
        if (empty($procedimientos)) {
            return response('No puede Guardar sin Procedimiento', 422);
        }

        $procedimiento = hc_procedimientos::find($request->input('id_procedimiento'));
        $protocolo = hc_protocolo::find($request->input('id_protocolo'));
        if (is_null($procedimiento) || is_null($protocolo)) {
            return response('Procedimiento quirúrgico no encontrado', 404);
        }

        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $idusuario = Auth::user()->id;

        try {
            app(\Sis_medico\Services\ClinicalProcedureSafetyService::class)->authorizeExistingProtocol(
                $procedimiento->id,
                $protocolo->id,
                $request->all(),
                array('actor_id' => $idusuario, 'ip' => $ip_cliente)
            );
        } catch (\Sis_medico\Exceptions\ClinicalProcedureBlockedException $exception) {
            return response($exception->getMessage(), 422);
        }

        $protocolo->update([
            'conclusion' => $request->input('conclusion'),
            'hallazgos' => $request->input('hallazgos'),
            'tipo_procedimiento' => '5',
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod' => $idusuario,
        ]);

        if ($request->filled('id_doctor_examinador')) {
            $doctor = $request->input('id_doctor_examinador');
            if ($doctor === '9666666666' || $doctor === 'GASTRO') {
                $doctor = $idusuario;
            }
            $procedimiento->update([
                'id_doctor_examinador' => $doctor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            ]);
        }

        Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id)->delete();
        foreach ($procedimientos as $procedimientoId) {
            Hc_Procedimiento_Final::create([
                'id_hc_procedimientos' => $procedimiento->id,
                'id_procedimiento' => $procedimientoId,
                'id_usuariocrea' => $idusuario,
                'id_usuariomod' => $idusuario,
                'ip_creacion' => $ip_cliente,
                'ip_modificacion' => $ip_cliente,
            ]);
        }

        return response('ok');
    }



    public function guardar_quirurgico(Request $request)
    {

        $params = array();
        parse_str($request["formulario"], $params);
        $idusuario  = Auth::user()->id;

        if ($request->hasFile('archivo')) {
            $pathAntes = $this->uploading($request['archivo'], $idusuario);
            if ($pathAntes['err']) {
                return ['err' => true, 'msj' => 'Hubo un error en la subida de la foto'];
            }
        }

        $quirurgico = hc_protocolo_cirugia::find($params['id_protocolo_cirugia']);

        if (!is_null($quirurgico)) {
            $array = [
                'dieresis'                  => $params['dieresis'],
                'complicaciones'            => $params['complicaciones'],
                'perdida_sangre'            => $params['perdida_sangre'],
                'muestra1'                  => $params['muestra1'],
                'exploracion'               => $params['exploracion'],
                'descripcion'               => $params['descripcion'],
                'transquirurgico'           => $params['transquirurgico'],
                'tipo_anestesia'            => $params['servicio'],
                'bioxsia'                   => $params['bioxsia'],
                'histopatologico'           => $params['histopatologico'],
                'muestra'                   => $params['muestra123'],
                'resultado'                   => $params['resultado'],
                'procedimiento'                 => $params['procedimiento123'],
                'proyectado'                 => $params['proyectado'],
                'realizado'                 => $params['realizado'],
                'intrumentista'                 => $params['intrumentista'],
                'circulante'                 => $params['circulante'],
                'ayudante_anestesia'         => $params['ayudante_anestesia'],
                'anestesiologo'             => $params['anestesiologo'],
                'ayudante1'                 => $params['ayudante1'],
                'ayudante2'                 => $params['ayudante2'],

            ];

            if (isset($pathAntes['data'])) {
                $array['archivo'] = $pathAntes['data'];
            }

            $quirurgico->update($array);
            return ['status' => 'success', 'msj' => 'se guardo el procedimiento'];
        } else {
            return ['status' => 'error', 'msj' => 'no se encontro el procedimiento'];
        }
    }



    public function uploading($imagen, $idusuario)
    {
        //dd($imagen, $idusuario);

        try {
            if($imagen!='undefined'){
                $extension       = $imagen->getClientOriginalExtension();
                $nuevo_nombre    = "foto_archivo" . date('YmdHis') . $idusuario . "." . $extension;
                $r1              = Storage::disk('public')->put($nuevo_nombre, \File::get($imagen));
                $image           = Image::make(Storage::disk('public')->get($nuevo_nombre));
                $image->resize(1280, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                Storage::disk('public')->put($nuevo_nombre, (string) $image->encode('jpg', 30));
    
                return ['err' => false, 'data' => $nuevo_nombre];
            }
            else
            return ['err' => false, 'msj' => 'eliminado con exito'];
           
        } catch (\Exception $e) {
            //dd($e);
            return ['err' => true, 'data' => $e->getMessage()];
        }
    }



    public function eliminar(Request $request)
    {
        try {

            // Obtener el archivo de la base de datos
            $archivo = hc_protocolo_cirugia::find($request['id']);

            if (!$archivo) {
                // Manejar el caso en el que el archivo no se encuentre en la base de datos
                return (['status' => false, 'msj' => 'No se encontró el archivo.']);
            }

            // Eliminar el archivo de la carpeta de almacenamiento
            $rutaArchivo = storage_path('app/avatars/' . $archivo->archivo);


            // Eliminar el archivo de la base de datos
            $archivo->archivo = null;
            $archivo->save();
            return (['status' =>true, 'msj' => 'eliminado correctamente.']);
        } catch (\Exception $e) {
            return (['status' => false, 'msj' => $e->getMessage()]);
        }
    }
}
