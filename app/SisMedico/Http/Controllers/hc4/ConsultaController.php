<?php

namespace Sis_medico\Http\Controllers\hc4;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\Cie_10_3;
use Sis_medico\Cie_10_4;
use Sis_medico\hc_child_pugh;
use Sis_medico\Hc_Cie10;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_receta;
use Sis_medico\Historiaclinica;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Log_Agenda;
use Sis_medico\Medicina;
use Sis_medico\Opcion_Usuario;
use Sis_medico\Paciente;
use Sis_medico\Paciente_Alergia;
use Sis_medico\Seguro;
use Sis_medico\Interconsulta;
use Sis_medico\User;
use Sis_medico\Empresa;
use Sis_medico\Ap_Procedimiento;
use Sis_medico\Interconsulta_Diagnostico;
use Carbon\Carbon;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Form008;
use Sis_medico\Exceptions\ClinicalEncounterConflictException;
use Sis_medico\Services\ClinicalEncounterService;


class ConsultaController extends Controller
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

        $paciente = Paciente::findOrFail($id_paciente);
        $recordOnly = request()->boolean('record_only');
        $procedureId = request()->integer('procedure_id');
        abort_if($recordOnly && $procedureId < 1, 422);

        //Obtengo el id_doctor visita
        $id_doctor_visita = Auth::user()->id;

        //dd($paciente->agenda);
        $pro_completo_0 = DB::table('historiaclinica as h')
            ->where('h.id_paciente', $id_paciente)
            ->join('agenda as a', 'a.id', 'h.id_agenda')
            ->leftjoin('especialidad as e', 'e.id', 'a.espid')
            ->join('hc_evolucion as hc_proto', 'hc_proto.hcid', 'h.hcid')
            ->join('hc_procedimientos as hc_p', 'hc_p.id', 'hc_proto.hc_id_procedimiento')
        //->leftjoin('users as u', 'u.id', 'hc_p.id_doctor_examinador')
            ->join('procedimiento_completo as pc', 'pc.id', 'hc_p.id_procedimiento_completo')
            ->where('hc_p.id_procedimiento_completo', '40')
            ->where('a.estado_cita', '<>', '3')
            ->when($recordOnly, function ($query) use ($procedureId) {
                $query->where('hc_p.id', $procedureId);
            })
            ->select('h.*', 'a.*', 'hc_proto.*', 'hc_p.*', 'e.nombre as espe_nombre', 'a.estado as agenda_estado')
            ->OrderBy('h.created_at', 'desc')
            ->orderBy('hc_p.id', 'desc')
            ->get();

        abort_if($recordOnly && $pro_completo_0->isEmpty(), 404);

        //dd($pro_completo_0);

        //dd($pro_completo_0.' -- '.$pro_final_0);

        $doctores     = User::where('id_tipo_usuario', '3')->where('estado', '1')->OrderBy('apellido1')->get();
        $seguros      = Seguro::where('inactivo', '1')->get();
        $alergiasxpac = Paciente_Alergia::where('id_paciente', $id_paciente)->get();

        return view($recordOnly ? 'hc4/consulta/records' : 'hc4/consulta/index', ['paciente' => $paciente, 'procedimientos2' => $pro_completo_0, 'doctores' => $doctores, 'seguros' => $seguros, 'alergiasxpac' => $alergiasxpac, 'id_doctor_visita' => $id_doctor_visita]);
    }

    /**
     * Completa, sin duplicar la agenda ni la historia clínica, los registros
     * necesarios para abrir una consulta que ya fue admitida.
     */
    public function abrirConsultaAdmitida($idPaciente, $idAgenda)
    {
        if ($this->rol_new('2')) {
            return response()->json(array('message' => 'No tiene permiso para atender esta consulta.'), 403);
        }

        try {
            $procedureId = DB::transaction(function () use ($idPaciente, $idAgenda) {
                $inicioHoy = Carbon::today()->startOfDay();
                $finHoy = Carbon::today()->endOfDay();
                $agenda = Agenda::where('id', $idAgenda)
                    ->where('id_paciente', $idPaciente)
                    ->where('proc_consul', 0)
                    ->where('estado_cita', 4)
                    ->where('estado', 1)
                    ->whereBetween('fechaini', array($inicioHoy, $finHoy))
                    ->lockForUpdate()
                    ->first();

                if (is_null($agenda)) {
                    throw new \RuntimeException('La agenda ya no cumple las condiciones para ser atendida.');
                }

                $history = Historiaclinica::where('id_agenda', $agenda->id)
                    ->where('id_paciente', $idPaciente)
                    ->lockForUpdate()
                    ->first();
                if (is_null($history)) {
                    throw new \RuntimeException('La agenda no tiene una historia clínica asociada.');
                }

                $procedure = hc_procedimientos::where('id_hc', $history->hcid)
                    ->where('id_procedimiento_completo', 40)
                    ->orderBy('id', 'desc')
                    ->first();

                if (is_null($procedure)) {
                    $procedure = hc_procedimientos::create(array(
                        'id_hc' => $history->hcid,
                        'id_seguro' => $history->id_seguro ?: $agenda->id_seguro,
                        'id_empresa' => $agenda->id_empresa,
                        'id_procedimiento_completo' => 40,
                        'id_doctor_examinador' => $agenda->id_doctor1,
                        'fecha' => Carbon::today()->format('Y-m-d'),
                        'estado' => 1,
                        'id_usuariocrea' => Auth::id(),
                        'id_usuariomod' => Auth::id(),
                        'ip_creacion' => request()->ip(),
                        'ip_modificacion' => request()->ip(),
                    ));
                }

                if (!Hc_Evolucion::where('hcid', $history->hcid)
                    ->where('hc_id_procedimiento', $procedure->id)->exists()) {
                    Hc_Evolucion::create(array(
                        'hcid' => $history->hcid,
                        'hc_id_procedimiento' => $procedure->id,
                        'secuencia' => 0,
                        'fecha_ingreso' => ' ',
                        'id_usuariocrea' => Auth::id(),
                        'id_usuariomod' => Auth::id(),
                        'ip_creacion' => request()->ip(),
                        'ip_modificacion' => request()->ip(),
                    ));
                }

                if (!hc_receta::where('id_hc', $history->hcid)->exists()) {
                    hc_receta::create(array(
                        'id_hc' => $history->hcid,
                        'id_usuariocrea' => Auth::id(),
                        'id_usuariomod' => Auth::id(),
                        'ip_creacion' => request()->ip(),
                        'ip_modificacion' => request()->ip(),
                    ));
                }

                return $procedure->id;
            }, 3);
        } catch (\RuntimeException $exception) {
            return response()->json(array('message' => $exception->getMessage()), 422);
        } catch (\Exception $exception) {
            \Log::error('No se pudo preparar la consulta admitida.', array(
                'id_agenda' => $idAgenda,
                'id_paciente' => $idPaciente,
                'exception' => $exception,
            ));
            return response()->json(array(
                'message' => 'Ocurrió un error al preparar el formulario clínico de la consulta.'
            ), 500);
        }

        return response()->json(array(
            'ok' => true,
            'procedure_id' => $procedureId,
        ));
    }

    public function crear_evolucion($id, $ag)
    {
        try {
            $created = app(\Sis_medico\Services\ClinicalEvolutionInitializationService::class)->initialize(
                $id, $ag, Auth::user()->id, request()->ip(), array(
                    'mode' => 'consultation',
                    'override' => request()->input('clinical_override'),
                    'override_reason' => request()->input('clinical_override_reason'),
                )
            );
        } catch (ClinicalEncounterConflictException $exception) {
            $payload = array(
                'estado' => 'conflicto_clinico',
                'codigo' => 'R-01',
                'msn' => $exception->getMessage(),
                'episodio_activo' => $exception->conflictingEncounterId(),
                'requiere_autorizacion' => true,
                'puede_autorizar' => in_array((int) Auth::user()->id_tipo_usuario, array(1, 3), true),
            );
            if (request()->ajax() || request()->expectsJson()) return response()->json($payload, 409);
            return back()->withErrors(array('consulta' => $exception->getMessage()));
        }

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json(array(
                'ok' => true,
                'procedure_id' => $created['procedure_id'],
                'message' => 'La consulta fue agregada correctamente.',
            ));
        }

        return redirect()->route('paciente.consulta', ['id_paciente' => $id]);
    }
    public function actualizar_consulta(Request $request)
    {

        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $consulta_datos = hc_procedimientos::find($request['id_hc_procedimiento']);

        if (!is_null($consulta_datos)) {
            $consulta_datos_new = [
                'anterior'         => 'CONSULTA-> DATOS_GENERALES Medico_Examinador: ' . $consulta_datos->id_doctor_examinador . '  Seguro: ' . $consulta_datos->id_seguro . '   Observaciones: ' . $consulta_datos->observaciones,
                'nuevo'            => 'CONSULTA-> DATOS_GENERALES Medico_Examinador: ' . $request["id_doctor_examinador"] . '  Seguro: ' . $request["id_seguro"] . '  Cortesia: ' . $request['consulta_cortesia_paciente'] . '   Observaciones: ' . $request["observaciones"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_evolucion'     => $request['id_evolucion'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($consulta_datos_new);
        }

        $hist_clinica = Historiaclinica::find($request['hcid']);

        if (!is_null($hist_clinica)) {
            $consulta_preparacion_new = [
                'anterior'         => 'CONSULTA->PREPARACION-> P.arterial: ' . $hist_clinica->presion . '  Pulso: ' . $hist_clinica->pulso . '    Temperatura: ' . $hist_clinica->temperatura . '   SaO2: ' . $hist_clinica->o2 . '   Estatura: ' . $hist_clinica->altura . '   Peso: ' . $hist_clinica->peso . '   Perimetro_Abdominal: ' . $hist_clinica->perimetro . '   Examenes_Realizar: ' . $hist_clinica->examenes_realizar,
                'nuevo'            => 'CONSULTA->PREPARACION-> P.arterial: ' . $request["presion"] . '  Pulso: ' . $request["pulso"] . '    Temperatura: ' . $request["temperatura"] . '   SaO2: ' . $request["o2"] . '   Estatura: ' . $request["estatura"] . '   Peso: ' . $request["peso"] . '   Perimetro_Abdominal: ' . $request["perimetro"] . '   Examenes_Realizar: ' . $request["examenes_realizar"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_evolucion'     => $request['id_evolucion'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($consulta_preparacion_new);

            
        }

        $receta = hc_receta::where('id_hc', $request['hcid'])->OrderBy('created_at', 'desc')->first();

        $child_pugh = hc_child_pugh::find($request['id_child_pugh']);

        if (!is_null($child_pugh)) {
            $consulta_child_pugh_new = [
                'anterior'         => 'CONSULTA->Child_Pugh-> Ascitis: ' . $child_pugh->ascitis . '  Encefalopatia: ' . $child_pugh->encefalopatia . '    Albumina: ' . $child_pugh->albumina . '   Bilirrubina: ' . $child_pugh->bilirrubina . '   Inr: ' . $child_pugh->inr . '   Examen_Fisico: ' . $child_pugh->examen_fisico,
                'nuevo'            => 'CONSULTA->Child_Pugh-> Ascitis: ' . $request["ascitis"] . '  Encefalopatia: ' . $request["encefalopatia"] . '    Albumina: ' . $request["albumina"] . '   Bilirrubina: ' . $request["bilirrubina"] . '   Inr: ' . $request["inr"] . '   Examen_Fisico: ' . $request["examen_fisico"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_evolucion'     => $request['id_evolucion'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($consulta_child_pugh_new);
        }

        $consulta_evol = Hc_Evolucion::find($request['id_evolucion']);

        if (!is_null($consulta_evol)) {
            $consulta_evolucion_new = [
                'anterior'         => 'CONSULTA-> Motivo: ' . $consulta_evol->motivo . '  Evolucion: ' . $consulta_evol->cuadro_clinico . ' Indicacion: ' . $consulta_evol->indicaciones . ' Resultado: ' . $consulta_evol->resultado,
                'nuevo'            => 'CONSULTA-> Motivo: ' . $request["motivo"] . '  Evolucion: ' . $request["historia_clinica"] . ' Indicacion: ' . $request["indicacion"] . ' Resultado: ' . $request["resultado_exam"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_evolucion'     => $request['id_evolucion'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($consulta_evolucion_new);
        }

        $hist_recetas = DB::table('hc_receta as r')->where('r.id_hc', $request['hcid'])->first();

        if (!is_null($hist_recetas)) {
            $receta_new = [
                'anterior'         => 'CONSULTA-> RECETA -> Rp: ' . $hist_recetas->rp . ' Prescripcion: ' . $hist_recetas->prescripcion,
                'nuevo'            => 'CONSULTA-> RECETA -> Rp:' . $request["rp"] . ' Prescripcion:' . $request["prescripcion"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($receta_new);
        }

        $id = $request['hcid'];

        $input1 = [
            'id_doctor1'        => $request['id_doctor_examinador'],
            'presion'           => $request["presion"],
            'pulso'             => $request["pulso"],
            'temperatura'       => $request["temperatura"],
            'o2'                => $request["o2"],
            'altura'            => $request["estatura"],
            'peso'              => $request["peso"],
            'perimetro'         => $request["perimetro"],
            'examenes_realizar' => $request["examenes_realizar"],
            'ip_modificacion'   => $ip_cliente,
            'id_usuariomod'     => $idusuario,
        ];
        Historiaclinica::where('hcid', $id)
            ->update($input1);

        $input_evo = [
            'motivo'          => $request["motivo"],
            'cuadro_clinico'  => $request["historia_clinica"],
            //'indicacion' => $request["indicacion"],
            'resultado'       => $request["resultado_exam"],
            'fecha_doctor'    => $request["fecha_doctor"],
            'indicaciones'    => $request["indicacion"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $id_evolucion = $request['id_evolucion'];
        Hc_Evolucion::where('id', $id_evolucion)
            ->update($input_evo);

        $input_child = [
            'ascitis'         => $request["ascitis"],
            'encefalopatia'   => $request["encefalopatia"],
            'albumina'        => $request["albumina"],
            'bilirrubina'     => $request["bilirrubina"],
            'inr'             => $request["inr"],
            'examen_fisico'   => $request["examen_fisico"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $id_child = $request['id_child_pugh'];
        hc_child_pugh::where('id', $id_child)
            ->update($input_child);

        $input_hc_receta = [
            'rp'              => $request["rp"],
            'prescripcion'    => $request["prescripcion"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $id_receta = $request['id_receta'];
        hc_receta::where('id', $id_receta)->update($input_hc_receta);

        if (($request['id_doctor_examinador'] == '9666666666') || ($request['id_doctor_examinador'] == 'GASTRO')) {
            $input_hc_procedimiento = [
                'id_doctor_examinador' => $request["id_doctor_examinador"],
                'id_seguro' => $request["id_seguro"],
                'observaciones' => $request["observaciones"],
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod' => $idusuario,
            ];
        } else {
            $input_hc_procedimiento = [
                'id_doctor_examinador' => $request["id_doctor_examinador"],
                'id_seguro'            => $request["id_seguro"],
                'observaciones'        => $request["observaciones"],
                'ip_modificacion'      => $ip_cliente,
                'id_usuariomod'        => $idusuario,
            ];
        }

        $id_hc_procedimiento = $request['id_hc_procedimiento'];
        hc_procedimientos::where('id', $id_hc_procedimiento)
            ->update($input_hc_procedimiento);

        $agenda_cortesia = agenda::where('id', $request['id_agenda'])->first();
        //dd ($agenda_cortesia);

        //Agregar Visita OMNI ubicacion_omni
        $ubicacion_omni = $request['ubicacion_omni'];
        //Agregar Visita OMNI sala_omni
        $sala_omni = $request['sala'];
        //Agregar Visita OMNI estado_omni
        //dd($sala_omni);
        $estado_omni = $request['estado_visita'];

        //dd($estado_omni);

        if (!is_null($agenda_cortesia)) {

            //Agregar Visita OMNI
            //if ((!is_null($ubicacion_omni)) && (!is_null($sala_omni)) && (!is_null($estado_omni))) {
            if ((!is_null($ubicacion_omni)) && (!is_null($estado_omni))) {

                $input = [
                    'cortesia'      => $request['consulta_cortesia_paciente'],
                    'procedencia'   => $ubicacion_omni,
                    'sala_hospital' => $sala_omni,
                    'estado_cita'   => $estado_omni,
                ];
                $agenda_cortesia->update($input);

            } else {

                $input = [
                    'cortesia' => $request['consulta_cortesia_paciente'],
                    //'ip_modificacion' => $ip_cliente,
                    //'id_usuariomod' => $idusuario
                ];
                $agenda_cortesia->update($input);
            }

        }

        //BLOQUE EMERGENCIA
        $solicitud = Ho_Solicitud::where('id_agenda',$hist_clinica->id_agenda)->where('estado','1')->first();
        if(!is_null($solicitud)){

        }else{
            $encounterResult = $this->crea_solic_ambulatoria($hist_clinica->id_agenda);
            if (isset($encounterResult['estado']) && $encounterResult['estado'] === 'conflicto_clinico') {
                return response()->json($encounterResult, 409);
            }
        } 

        return "ok";

    }

    public function crea_solic_ambulatoria($id_agenda){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $agenda = Agenda::find($id_agenda);
        if(!is_null($agenda)){
            $encounterService = app(ClinicalEncounterService::class);
            try {
                $encounter = $encounterService->openOrReuse(
                    $agenda->id_paciente,
                    ClinicalEncounterService::AMBULATORY,
                    array(
                        'id_empresa' => $agenda->id_empresa,
                        'started_at' => $agenda->fechaini,
                        'actor_id' => $idusuario,
                        'ip' => $ip_cliente,
                        'override' => request()->input('clinical_override'),
                        'override_reason' => request()->input('clinical_override_reason'),
                    )
                );
                $encounterService->link($encounter, array('agenda' => $agenda->id));
            } catch (ClinicalEncounterConflictException $exception) {
                return array(
                    'estado' => 'conflicto_clinico',
                    'codigo' => 'R-01',
                    'msn' => $exception->getMessage(),
                    'episodio_activo' => $exception->conflictingEncounterId(),
                    'requiere_autorizacion' => true,
                    'puede_autorizar' => in_array((int) Auth::user()->id_tipo_usuario, array(1, 3), true),
                );
            }
            $solicitud = Ho_Solicitud::where('id_agenda',$id_agenda)->where('estado',1)->first();
            if(is_null($solicitud)){
                //CREAR SOLICITUD / FORM008 Y LOG 
                $arr_solic = [
                    'id_paciente'       => $agenda->id_paciente,
                    'id_seguro'         => $agenda->id_seguro,
                    'id_agenda'         => $agenda->id,
                    'fecha_ingreso'     => $agenda->fechaini,
                    'fecha_salida'      => $agenda->fechaini,
                    'estado_paso'       => '0',
                    'estado'            => '1',
                    'id_usuariocrea'    => $idusuario,
                    'id_usuariomod'     => $idusuario, 
                    'ip_creacion'       => $ip_cliente,
                    'ip_modificacion'   => $ip_cliente,
                ];

                $id_solicitud = Ho_Solicitud::insertGetId($arr_solic);

                $sol_log = [
                    'id_ho_solicitud'       => $id_solicitud,
                    'estado_paso'           => '0',
                    'id_agenda'             => $id_agenda,
                    'fecha_ingreso'         => $agenda->fechaini,
                    'fecha_salida'          => $agenda->fechaini,
                    'ip_creacion'           => $ip_cliente,
                    'ip_modificacion'       => $ip_cliente,
                    'id_usuariocrea'        => $idusuario, 
                ];
    
                Ho_Log_Solicitud::create($sol_log);
                $encounterService->link($encounter, array('ho_solicitud' => $id_solicitud));

                $this->crea_form_008($id_solicitud);
                //$this->crea_datos_paciente($agenda->id_paciente);
                
                return [ 'estado' => 'ok', 'id_solicitud' => $id_solicitud ];
            }else{

                $encounterService->link($encounter, array('ho_solicitud' => $solicitud->id));

                $this->crea_form_008($solicitud->id);
                //$this->crea_datos_paciente($agenda->id_paciente);
                return [ 'estado' => 'ok', 'id_solicitud' => $solicitud->id ];   
            }
                
        }else{

            return ['estado' => 'err', 'msn' => 'Sin Agenda'];
        }

    }

    public function crea_form_008($id_solic){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $solicitud = Ho_Solicitud::find($id_solic);
        if(!is_null($solicitud)){
            $form008 = app(\Sis_medico\Services\ClinicalEmergencyFormService::class)->ensure($solicitud, [
                'fecha_creacion'    => $solicitud->fecha_ingreso,
                'id_usuariocrea'    => $idusuario,
                'id_usuariomod'     => $idusuario,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'id_agenda'         => $solicitud->id_agenda,
            ]);
            return [ 'estado' => 'ok', 'id_form' => $form008->id ];
        }else{

            return ['estado' => 'err', 'msn' => 'Sin Solicitud'];    
        }
    }

    public function actualizar_receta(Request $request)
    {

        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $receta       = hc_receta::where('id_hc', $request['hcid'])->OrderBy('created_at', 'desc')->first();
        $hist_recetas = DB::table('hc_receta as r')->where('r.id_hc', $request['hcid'])->first();
        if (!is_null($hist_recetas)) {
            $receta_new = [
                'anterior'         => 'CONSULTA-> RECETA -> Rp: ' . $hist_recetas->rp . ' Prescripcion: ' . $hist_recetas->prescripcion,
                'nuevo'            => 'CONSULTA-> RECETA -> Rp:' . $request["rp"] . ' Prescripcion:' . $request["prescripcion"],
                'hc_id'            => $request['hcid'],
                'id_paciente'      => $request['id_paciente'],
                'id_procedimiento' => $request['id_hc_procedimiento'],
                'id_usuariomod'    => $idusuario,
                'id_usuariocrea'   => $idusuario,
                'ip_modificacion'  => $ip_cliente,
                'ip_creacion'      => $ip_cliente,
            ];
            Hc_Log::create($receta_new);
        }

        $input_hc_receta = [
            'rp'              => $request["rp"],
            'prescripcion'    => $request["prescripcion"],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];

        $id_receta = $request['id_receta'];
        hc_receta::where('id', $id_receta)->update($input_hc_receta);

        return "ok";

    }

    public function agregar_cie10(Request $request)
    {
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($request['codigo'] == null) {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }

        $cie10 = DB::table('hc_cie10')->where('hc_cie10.hc_id_procedimiento', $request['hc_id_procedimiento'])->get();

        //dd($cie10);
        if (!is_null($cie10)) {
            foreach ($cie10 as $value) {

                $diagnostico_new = [
                    'anterior'         => 'CONSULTA -> Diagnostico: ' . $value->cie10,
                    'nuevo'            => 'CONSULTA -> Diagnostico: ' . $request['codigo'],
                    'hc_id'            => $value->hcid,
                    'id_paciente'      => $request['id_paciente'],
                    'id_procedimiento' => $value->hc_id_procedimiento,
                    'id_usuariomod'    => $idusuario,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'ip_creacion'      => $ip_cliente,
                ];
                Hc_Log::create($diagnostico_new);

            }
        }

        $input2 = [
            'hcid'                  => $request['hcid'],
            'cie10'                 => $request['codigo'],
            'hc_id_procedimiento'   => $request['hc_id_procedimiento'],
            'ingreso_egreso'        => $request['in_eg'],
            'presuntivo_definitivo' => $request['pre_def'],

            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,

        ];
        $id = Hc_Cie10::insertGetId($input2);

        $count = Hc_Cie10::where('hc_id_procedimiento', $request['hc_id_procedimiento'])->get()->count();

        $cie10 = Hc_Cie10::find($id);

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

    //Agregar Visita HC4
    public function crear_visita($id, $ag)
    {
        app(\Sis_medico\Services\ClinicalEvolutionInitializationService::class)->initialize(
            $id, $ag, Auth::user()->id, request()->ip(), array('mode' => 'hospital_visit')
        );

        return redirect()->route('paciente.consulta', ['id_paciente' => $id]);
    }
    public function vademecun(Request $request)
    {
        $variable = $request['variable'];
        $texto    = trim((string) $request->input('texto', ''));
        $nombre   = $this->consultaVademecum($texto);

        return view('hc4/consulta/vademecum', compact('nombre', 'variable', 'texto'));
    }

    public function vademecun2(Request $request)
    {
        $texto  = trim((string) $request->input('texto', ''));
        $nombre = $this->consultaVademecum($texto);

        return view('hc4/consulta/vademecum_interno', compact('nombre', 'texto'));
    }

    private function consultaVademecum($texto)
    {
        $consulta = Medicina::where('estado', 1);

        if ($texto !== '') {
            $consulta->whereRaw(
                "CONCAT(COALESCE(nombre, ''), ' ', COALESCE(presentacion, '')) LIKE ?",
                array('%' . $texto . '%')
            );
        }

        return $consulta
            ->orderBy('nombre', 'asc')
            ->orderBy('presentacion', 'asc')
            ->paginate(8);
    }


    public function carga_hora_inicio($hcid)
    {
        $hc_procedimientos = hc_procedimientos::where('id_hc', $hcid)->where('estado','1')->first();
        //dd($hc_procedimientos);
        $arr = [
            'hora_inicio'   => date('H:i:s'),
        ];
        //dd($arr);

        $hc_procedimientos->update($arr);

        return "ok";
    }

    public function carga_hora_fin($hcid)
    {
        $hc_procedimientos = hc_procedimientos::where('id_hc', $hcid)->where('estado','1')->first();
        //dd($hc_procedimientos);
        $arr2 = [
            'hora_fin'   => date('H:i:s'),
        ];
        //dd($arr);

        $hc_procedimientos->update($arr2);

        return "ok";
    }

    public function hc4_crear( $hcid ){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $historia = Historiaclinica::find($hcid);
        $paciente = $historia->paciente;
        $empresa = Empresa::where('prioridad', 1)->first();
        $interconsulta = Interconsulta::where('id_hc',$hcid)->where('estado',1)->first();
        $tarifario = Ap_Procedimiento::where('estado', 1)->get();
        $id_doctor = Auth::user()->id;
        $tevolucion = null; 
            $resultados = null;
            $planes = null;
        $evolucion = $historia->evoluciones->where('secuencia',0)->first();
        if(is_null($interconsulta)){
            //CREAR LA INTERCONSULTA
            if( !is_null($evolucion) ){
                $tevolucion = $evolucion->cuadro_clinico; 
                $resultados = $evolucion->resultado;
                $planes = $evolucion->indicaciones;
            }
            $input_ex = [
                'id_paciente'       => $paciente->id,
                'id_doctor'         => $id_doctor,
                'id_empresa'        => $empresa->id,
                'ip_creacion'       => $ip_cliente,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariocrea'    => $id_doctor,
                'id_usuariomod'     => $id_doctor,
                'fecha'             => date('Y-m-d H:i:s'),
                'estado'            => '1',
                'evolucion'         => $tevolucion,
                'resultados_exa'    => $resultados,
                'plan_terapeuticos' => $planes,
                'id_hc' => $hcid,  
            ];   
            $id_editar = Interconsulta::insertGetId($input_ex);
            app(\Sis_medico\Services\ClinicalConsultWorkflowService::class)->sync(
                Interconsulta::find($id_editar), 'ROUTINE', array('actor_id'=>$id_doctor,'ip'=>$ip_cliente)
            );
        }else{
            /*if($interconsulta->evolucion == null){
                $tevolucion=$evolucion->cuadro_clinico;
            } 
            if($interconsulta->resultados_exa == null){
                $resultados=$evolucion->resultado;
            }
            if($interconsulta->plan_terapeuticos == null){
                $planes=$evolucion->indicacion;
            }*/   
            $tevolucion=$evolucion->cuadro_clinico;
            $resultados=$evolucion->resultado;
            $planes=$evolucion->indicaciones;
            $id_editar = $interconsulta->id;
               $interconsulta->update(
                [
                'evolucion'         => $tevolucion,
                'resultados_exa'    => $resultados,
                'plan_terapeuticos' => $planes,
                ]
                );
        }
        $dxs=$historia->diagnosticos;
        foreach ($dxs as $dx){
            $i_diagnostico = Interconsulta_Diagnostico::where('cie10', $dx->cie10)->where('id_interconsulta', $id_editar)->first();
            if(is_null($i_diagnostico)){
                Interconsulta_Diagnostico::create([
                'id_interconsulta' => $id_editar,
                'cie10'=> $dx->cie10,
                'ingreso_egreso' => $dx->ingreso_egreso,
                'presuntivo_definitivo'=> $dx->presuntivo_definitivo,
                'ip_creacion'       => $ip_cliente,
                'id_usuariomod'     => $id_doctor,
                'id_usuariocrea'    => $id_doctor,
                'ip_modificacion'   => $ip_cliente,
                ]);
            }
        }
        $interconsulta = Interconsulta::find($id_editar);
        return view('hc4.consulta.interconsulta', ['interconsulta' => $interconsulta, 'tarifario' => $tarifario]);
    }

    public function agregar_cie10_007(Request $request)
    {
        //dd($request->all());
        //return $request->all();
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        if ($request['codigo'] == null) {
            return ['estado' => 'err', 'msn' => 'Seleccione un CIE10'];
        }

        $input2 = [
            'id_interconsulta'          => $request['id_orden'],
            'cie10'                 => $request['codigo'],
            'presuntivo_definitivo' => $request['pre_def'],
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,

        ];
        $id = Interconsulta_Diagnostico::insertGetId($input2);

        $count = Interconsulta_Diagnostico::where('id_interconsulta', $request['id_orden'])->get()->count();

        $cie10 = Interconsulta_Diagnostico::find($id);
        $descripcion = '';
        $c3 = Cie_10_3::find($cie10->cie10);
        if (!is_null($c3)) {
            $descripcion = $c3->descripcion;
        }
        $c4 = Cie_10_4::find($cie10->cie10);
        if (!is_null($c4)) {
            $descripcion = $c4->descripcion;
        }
        //dd($cie10->cie10);

        return ['count' => $count, 'id' => $id, 'cie10' => $cie10->cie10, 'descripcion' => $descripcion, 'pre_def' => $request['pre_def'], 'in_eg' => $request['in_eg']];
    }
    public function hc4_guardar(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');
        $interconsulta = Interconsulta::find($request->id_interconsulta);
        $interconsulta->update([
            'servicio'        => $request->servicio,
            'especialidad'    => $request->especialidad,
            //'evolucion'       => $request->evolucion,
            'tarifario'       => $request->tarifario,
            'descripcion'     => $request->descripcion, 
            //'resultados_exa'  => $request->resultados_exa,
            //'plan_terapeuticos'  => $request->plan_terapeuticos,
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ]);
        app(\Sis_medico\Services\ClinicalConsultWorkflowService::class)->sync(
            $interconsulta->fresh(), $request->get('clinical_priority', 'ROUTINE'),
            array('actor_id'=>$idusuario,'ip'=>$ip_cliente)
        );
        return "ok";
    }
    public function hc4_imprimir($id)
    {
        $cie10 = DB::table('interconsulta_diagnostico')->where('interconsulta_diagnostico.id_interconsulta', $id)->get();
        $interconsulta  = Interconsulta::find($id);
        $paciente       = $interconsulta->paciente;
        $age            = Carbon::createFromDate(substr($interconsulta->paciente->fecha_nacimiento, 0, 4), substr($interconsulta->paciente->fecha_nacimiento, 5, 2), substr($interconsulta->paciente->fecha_nacimiento, 8, 2))->age;
        $empresa        = Empresa::where('prioridad', 1)->get()->first();
        $vistaurl = "hc4.consulta.formulario_007";
        $view     = \View::make($vistaurl, compact('interconsulta', 'age', 'empresa' , 'paciente', 'cie10'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setOptions(['dpi' => 150, 'isPhpEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('007-' . $interconsulta->paciente->apellido1 . '.pdf', array("Attachment" => false));
        exit(0);
    }
    public function examenes_buscar_publicos_otros(Request $request)
    {
        $examenes = Examen::where('estado', 1)->where('id_agrupador', 7)->where('publico_privado', 0)->where('no_orden_pub', 0)->where('nombre', 'like', '%' . $request->term . '%')->get();
        $arr = null;
        foreach ($examenes as $value) {
            $arr[] = array('value' => $value->nombre, 'id' => $value->id);
        }
        return $arr;
    }
}
