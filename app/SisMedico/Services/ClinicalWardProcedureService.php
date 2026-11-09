<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Agenda;
use Sis_medico\AgendaProcedimiento;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Log;
use Sis_medico\Hc_Procedimiento_Final;
use Sis_medico\ClinicalEncounterEvent;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Hospitalizacion;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Log_Agenda;
use Sis_medico\Paciente;
use Sis_medico\Pentax;
use Sis_medico\PentaxProc;
use Sis_medico\Pentax_log;
use Sis_medico\Sala;
use Sis_medico\User;
use Sis_medico\hc_child_pugh;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\hc_receta;

class ClinicalWardProcedureService
{
    public function agenda(Request $request)
    {
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];
        $idusuario    = Auth::user()->id;
        $id_doctor    = Auth::user()->id;
        if (!is_null($request['id_doctor'])) {
        }
        $id_paciente = $request['id_paciente'];
        $paciente = paciente::find($id_paciente);
        $user = User::find($id_paciente);
        $especialidad = DB::table('user_espe as u_es')->where('u_es.usuid', $id_doctor)->get()->first();

        if (!is_null($especialidad)) {
            $espid = $especialidad->espid;
        } else {
            $espid = '4';
        }

        $input_agenda = [
            'fechaini'        => $request['fecha'],
            'fechafin'        => $request['fechafin'],
            'id_paciente'     => $id_paciente,
            'id_doctor1'      => $id_doctor,
            'proc_consul'     => '1',
            'estado_cita'     => '1',
            'id_empresa'      => '1391707460001',
            'espid'           => $espid,
            'observaciones'   => 'EVOLUCION CREADA POR HOSPITAL',
            'id_seguro'       => $paciente->id_seguro,
            'estado'          => '1',
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $id_doctor,
            'id_usuariomod'   => $id_doctor,
        ];

        $id_agenda = agenda::insertGetId($input_agenda);


        $consulta_crear_new = [
            'anterior'        => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
            'nuevo'           => 'CONSULTA: -> El Dr. creo nueva consulta -> id_agenda: ' . $id_agenda,
            'id_paciente'     => $id_paciente,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Hc_Log::create($consulta_crear_new);

        $input_log = [
            'id_agenda'       => $id_agenda,
            'estado_cita_ant' => '1',
            'estado_cita'     => '1',
            'fechaini'        => Date('Y-m-d H:i:s'),
            'fechafin'        => Date('Y-m-d H:i:s'),
            'estado'          => '4',
            'observaciones'   => 'EVOLUCION CREADA POR EL DOCTOR',
            'id_doctor1'      => $id_doctor,
            'descripcion'     => 'EVOLUCION CREADA POR EL DOCTOR',
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];

        Log_Agenda::create($input_log);

        $input_historia = [

            'parentesco'      => $paciente->parentesco,
            'id_usuario'      => $paciente->id_usuario,
            'id_agenda'       => $id_agenda,
            'id_paciente'     => $id_paciente,
            'id_seguro'       => $paciente->id_seguro,
            'id_doctor1'      => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $id_doctor,
            'ip_creacion'     => $ip_cliente,

        ];

        $id_procedimiento_completo = '40';

        $id_historia = Historiaclinica::insertGetId($input_historia);

        $input_hc_procedimiento = [
            'id_hc'                     => $id_historia,
            'id_seguro'                 => $paciente->id_seguro,
            'id_procedimiento_completo' => $id_procedimiento_completo,
            'ip_modificacion'           => $ip_cliente,
            'id_usuariocrea'            => $idusuario,
            'id_usuariomod'             => $idusuario,
            'ip_creacion'               => $ip_cliente,

        ];

        $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

        $input_hc_evolucion = [
            'hc_id_procedimiento' => $id_hc_procedimiento,
            'hcid'                => $id_historia,
            'secuencia'           => '0',
            'fecha_ingreso'       => ' ',
            'ip_modificacion'     => $ip_cliente,
            'id_usuariomod'       => $idusuario,
            'id_usuariocrea'      => $idusuario,
            'ip_creacion'         => $ip_cliente,

        ];
        $id_evolucion    = Hc_Evolucion::insertGetId($input_hc_evolucion);

        $input_child_pugh = [
            'id_hc_evolucion'       => $id_evolucion,
            'ip_modificacion'       => $ip_cliente,
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'examen_fisico'         => 'ESTADO CABEZA Y CUELLO:
                                                            ESTADO TORAX:
                                                            ESTADO ABDOMEN:
                                                            ESTADO MIEMBROS SUPERIORES:
                                                            ESTADO MIEMBROS INFERIORES:
                                                            OTROS: ',
        ];

        $id_child = hc_child_pugh::create($input_child_pugh);

        $input_hc_receta = [
            'id_hc'           => $id_historia,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
            'ip_creacion'     => $ip_cliente,

        ];
        hc_receta::insert($input_hc_receta);
    }

    public function crear_procedimiento(Request $request)
    {
        $data = $request->validate([
            'id_solicitud' => 'required|integer|min:1', 'procedimiento' => 'required|array|min:1|max:10',
            'procedimiento.*' => 'required|integer|min:1|distinct', 'doctor' => 'required|string|max:20',
            'sala' => 'required|integer|min:1', 'fecha' => 'required|date', 'fechafin' => 'required|date|after:fecha',
        ]);
        return DB::transaction(function () use ($data) {
            $solicitud = Ho_Solicitud::where('id', $data['id_solicitud'])->lockForUpdate()->firstOrFail();
            $encounter = app(ClinicalHospitalTransferService::class)->authorize($solicitud, 'hospital.documents.write', ['SISTEMAS', 'DOCTORES', 'DOCTOR']);
            $documents = app(HospitalDocumentService::class);
            // La primera planificación crea el procedimiento que luego se concilia.
            // assertCanPlan() exige una conciliación previa y provocaba un bloqueo circular.
            $transfer = Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)->where('paso', 5)->where('estado', 2)->firstOrFail();
            if ((string) $data['sala'] !== (string) $transfer->id_sala) {
                throw new \InvalidArgumentException('Seleccione el quirofano en el que fue recibido el episodio.');
            }
            app(ClinicalHospitalDestinationService::class)->assertSala($data['sala'], 5);
            $doctorRoleId = User::roleIdForName(array('DOCTORES', 'DOCTOR'));
            $doctorQuery = User::withoutGlobalScopes()->where('id', $data['doctor'])->where('estado', 1);
            if ($doctorRoleId !== null) {
                $doctorQuery->where('id_tipo_usuario', $doctorRoleId);
            }
            $doctor = $doctorQuery->first();
            if (!$doctor) {
                throw new \InvalidArgumentException('El médico seleccionado no está activo o no pertenece al tipo DOCTOR.');
            }
            $catalog = \Sis_medico\Procedimiento::whereIn('id', $data['procedimiento'])->where('estado', 1)->get();
            if ($catalog->count() !== count($data['procedimiento'])) {
                throw new \InvalidArgumentException('Seleccione procedimientos vigentes del catalogo.');
            }
            foreach ($catalog as $item) {
                if (strtoupper(trim((string) $item->nombre)) === 'EVOLUCION HOSPITALARIA') {
                    throw new \InvalidArgumentException('La evolucion tecnica de ingreso no es una operacion.');
                }
            }
            // Un ingreso por Emergencia puede no tener agenda raíz. En ese caso
            // la agenda quirúrgica nace directamente del episodio canónico.
            $root = $solicitud->agenda;
            $rootIsValid = $root && (string) $root->encounter_id === (string) $encounter->id
                && (string) $root->id_empresa === (string) $encounter->id_empresa
                && (string) $root->id_paciente === (string) $solicitud->id_paciente;
            $patient = \Sis_medico\Paciente::find($solicitud->id_paciente);
            $insuranceId = $rootIsValid && $root->id_seguro ? $root->id_seguro
                : ($solicitud->id_seguro ?: ($patient ? $patient->id_seguro : null));
            if (!$insuranceId) {
                throw new \InvalidArgumentException('El paciente no tiene un seguro configurado para crear la planificación quirúrgica.');
            }

            // Si ya existe una planificación pendiente, se actualiza la misma
            // agenda/procedimiento en lugar de crear un caso duplicado.
            // La vinculacion usa el id del registro de recepcion en
            // ho_log_solicitud, no el id del traslado de sala.
            $receiptLog = Ho_Log_Solicitud::where('id_ho_solicitud', $solicitud->id)
                ->where('estado_paso', 5)->whereNull('fecha_salida')->first();
            $existingBinding = $receiptLog
                ? ClinicalEncounterEvent::where('encounter_id', $encounter->id)
                    ->where('event_type', HospitalDocumentService::BOUND)
                    ->where('from_value', $receiptLog->id)->first()
                : null;
            if ($existingBinding) {
                $procedure = hc_procedimientos::find($existingBinding->to_value);
                $history = $procedure ? Historiaclinica::where('hcid', $procedure->id_hc)->first() : null;
                $agenda = $history ? Agenda::find($history->id_agenda) : null;
                if ($procedure && $history && $agenda) {
                    $audit = ['id_usuariomod' => Auth::id(), 'ip_modificacion' => request()->ip()];
                    $agenda->update(array_merge($audit, [
                        'id_doctor1' => $doctor->id,
                        'fechaini' => date('Y-m-d H:i:s', strtotime($data['fecha'])),
                        'fechafin' => date('Y-m-d H:i:s', strtotime($data['fechafin'])),
                        'id_procedimiento' => $data['procedimiento'][0],
                        'id_sala' => $data['sala'],
                    ]));
                    $procedure->update(array_merge($audit, [
                        'id_doctor_examinador' => $doctor->id,
                        'id_doctor_examinador2' => $doctor->id,
                    ]));
                    Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedure->id)->delete();
                    AgendaProcedimiento::where('id_agenda', $agenda->id)->delete();
                    $createdAudit = array_merge($audit, ['id_usuariocrea' => Auth::id(), 'ip_creacion' => request()->ip()]);
                    foreach ($data['procedimiento'] as $catalogId) {
                        Hc_Procedimiento_Final::create(array_merge($createdAudit, ['id_hc_procedimientos' => $procedure->id, 'id_procedimiento' => $catalogId]));
                        if ((string) $catalogId !== (string) $agenda->id_procedimiento) {
                            AgendaProcedimiento::create(array_merge($createdAudit, ['id_agenda' => $agenda->id, 'id_procedimiento' => $catalogId]));
                        }
                    }
                    return $agenda->id;
                }
            }
            $specialtyId = $rootIsValid && $root->espid ? $root->espid : 4;
            $audit = ['id_usuariocrea' => Auth::id(), 'id_usuariomod' => Auth::id(),
                'ip_creacion' => request()->ip(), 'ip_modificacion' => request()->ip()];
            $agenda = Agenda::create(array_merge($audit, [
                'encounter_id' => $encounter->id, 'id_paciente' => $solicitud->id_paciente,
                'id_empresa' => $encounter->id_empresa, 'id_doctor1' => $doctor->id,
                'espid' => $specialtyId, 'id_seguro' => $insuranceId,
                'fechaini' => date('Y-m-d H:i:s', strtotime($data['fecha'])),
                'fechafin' => date('Y-m-d H:i:s', strtotime($data['fechafin'])),
                'proc_consul' => 1, 'estado_cita' => 0, 'estado' => 1, 'ho_tipo' => 5,
                'id_procedimiento' => $data['procedimiento'][0], 'id_sala' => $data['sala'],
                'observaciones' => 'PROCEDIMIENTO HOSPITALARIO PLANIFICADO',
            ]));
            app(ClinicalEncounterService::class)->link($encounter, ['agenda' => $agenda->id]);
            $history = Historiaclinica::create(array_merge($audit, [
                'encounter_id' => $encounter->id, 'id_agenda' => $agenda->id,
                'id_paciente' => $solicitud->id_paciente, 'id_seguro' => $insuranceId, 'id_doctor1' => $doctor->id,
            ]));
            $procedure = hc_procedimientos::create(array_merge($audit, [
                'id_hc' => $history->hcid, 'id_seguro' => $insuranceId,
                'id_doctor_examinador' => $doctor->id, 'id_doctor_examinador2' => $doctor->id,
            ]));
            $pentax = Pentax::create(array_merge($audit, ['id_agenda' => $agenda->id, 'hcid' => $history->hcid,
                'id_sala' => $data['sala'], 'id_doctor1' => $doctor->id, 'id_seguro' => $insuranceId,
                'observacion' => 'CASO HOSPITALARIO PLANIFICADO']));
            foreach ($data['procedimiento'] as $catalogId) {
                Hc_Procedimiento_Final::create(array_merge($audit, ['id_hc_procedimientos' => $procedure->id, 'id_procedimiento' => $catalogId]));
                PentaxProc::create(array_merge($audit, ['id_pentax' => $pentax->id, 'id_procedimiento' => $catalogId]));
                if ((string) $catalogId !== (string) $agenda->id_procedimiento) {
                    AgendaProcedimiento::create(array_merge($audit, ['id_agenda' => $agenda->id, 'id_procedimiento' => $catalogId]));
                }
            }
            $documents->bindProcedure($solicitud, $procedure->id);
            // A plan has no protocol or consumed authorization. The original agenda and bed are untouched.
            return $agenda->id;
        }, 3);
    }

    public function crear_imagen(Request $request)
    {

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $id_doctor  = Auth::user()->id;

        $procedimientos = $request['procedimiento'];

        $procedimientop = $procedimientos[0];
        $paciente       = Paciente::find($request->id_paciente);
        $sala           = \Sis_medico\Sala::find($request['sala']);

        $hospitalizacion = Ho_Hospitalizacion::find($request['id_hospitalizacion']);
        $traspaso = Ho_Traspaso_Sala008::find($hospitalizacion->id_traspaso);
        $solicitud = Ho_Solicitud::find($traspaso->id_solicitud);

        $procedimiento_crear_new = [
            'anterior'        => 'IMAGEN HOSPITAL',
            'nuevo'           => 'IMAGEN HOSPITAL',
            'id_paciente'     => $paciente->id,
            'id_usuariomod'   => $id_doctor,
            'id_usuariocrea'  => $id_doctor,
            'ip_modificacion' => $ip_cliente,
            'ip_creacion'     => $ip_cliente,
        ];
        Hc_Log::create($procedimiento_crear_new);

        if ($procedimientos != null) {

            $input_agenda = [
                'fechaini'         => $request['fecha'],
                'fechafin'         => $request['fechafin'],
                'id_paciente'      => $paciente->id,
                'id_doctor1'       => $request->doctor,
                'proc_consul'      => '1',
                'estado_cita'      => '0',
                'id_empresa'       => $solicitud->encounter->id_empresa,
                'espid'            => '7',
                'observaciones'    => 'IMAGEN CREADO POR EL DOCTOR',
                'id_seguro'        => $paciente->id_seguro,
                'estado'           => '1',
                'ip_creacion'      => $ip_cliente,
                'ip_modificacion'  => $ip_cliente,
                'id_usuariocrea'   => $id_doctor,
                'id_usuariomod'    => $id_doctor,
                'id_procedimiento' => $procedimientop,
                'id_sala'          => $sala->id,
                'ho_tipo'          => $request->ho_tipo,
            ];

            $id_agenda = agenda::insertGetId($input_agenda);
            //return $id_agenda;

            $txt_pro = '';
            foreach ($procedimientos as $value) {

                if ($procedimientop != $value) {
                    $txt_pro = $txt_pro . '+' . $value;
                    AgendaProcedimiento::create([
                        'id_agenda'        => $id_agenda,
                        'id_procedimiento' => $value,
                        'ip_creacion'      => $ip_cliente,
                        'ip_modificacion'  => $ip_cliente,
                        'id_usuariocrea'   => $id_doctor,
                        'id_usuariomod'    => $id_doctor,
                    ]);
                }
            }

            $input_log = [
                'id_agenda'       => $id_agenda,
                'estado_cita_ant' => '0',
                'estado_cita'     => '0',
                'fechaini'        => Date('Y-m-d H:i:s'),
                'fechafin'        => Date('Y-m-d H:i:s'),
                'estado'          => '4',
                'observaciones'   => 'IMAGEN HOSPITAL',
                'id_doctor1'      => $request->doctor,
                'descripcion'     => 'IMAGEN HOSPITAL',
                'campos_ant'      => 'PRO: ' . $procedimientop . $txt_pro,

                'id_usuariomod'   => $id_doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
            ];
            $idusuario = $id_doctor;
            Log_Agenda::create($input_log);
            $input_historia = [

                'parentesco'      => $paciente->parentesco,
                'id_usuario'      => $paciente->id_usuario,
                'id_agenda'       => $id_agenda,
                'id_paciente'     => $paciente->id,
                'encounter_id'    => $solicitud->encounter_id,
                'id_seguro'       => $paciente->id_seguro,

                'id_doctor1'      => $request->doctor,
                'id_usuariocrea'  => $id_doctor,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $id_doctor,
                'ip_creacion'     => $ip_cliente,

            ];

            $id_historia = Historiaclinica::insertGetId($input_historia);

            $input_pentax = [
                'id_agenda'       => $id_agenda,
                'hcid'            => $id_historia,
                'id_sala'         => $sala->id,
                'id_doctor1'      => $request->doctor,
                'id_seguro'       => $paciente->id_seguro,
                'observacion'     => "HOSPITAL IMAGEN",
                'id_usuariocrea'  => $idusuario,
                'ip_modificacion' => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'ip_creacion'     => $ip_cliente,
            ];

            $id_pentax = Pentax::insertGetId($input_pentax);

            $list_proc = '';
            foreach ($procedimientos as $value) {
                $input_pentax_pro2 = [
                    'id_pentax'        => $id_pentax,
                    'id_procedimiento' => $value,
                    'id_usuariocrea'   => $idusuario,
                    'ip_modificacion'  => $ip_cliente,
                    'id_usuariomod'    => $idusuario,
                    'ip_creacion'      => $ip_cliente,
                ];

                PentaxProc::create($input_pentax_pro2);
                $list_proc = $list_proc . "+" . $value;
            }

            $input_log_px = [
                'id_pentax'       => $id_pentax,
                'tipo_cambio'     => "HOSPITAL IMAGEN",
                'descripcion'     => "EN ESPERA",
                'estado_pentax'   => '0',
                'procedimientos'  => $list_proc,
                'id_doctor1'      => $request->doctor,
                'observacion'     => "HOSPITAL IMAGEN",
                'id_sala'         => $sala->id,
                'id_seguro'       => $paciente->id_seguro,

                'ip_modificacion' => $ip_cliente,
                'ip_creacion'     => $ip_cliente,
                'id_usuariomod'   => $idusuario,
                'id_usuariocrea'  => $idusuario,
            ];

            Pentax_log::create($input_log_px);

            $input_hc_procedimiento = [
                'id_hc'                 => $id_historia,
                'id_seguro'             => $paciente->id_seguro,
                'id_doctor_examinador'  => $idusuario,
                'id_doctor_examinador2' => $idusuario,
                'ip_modificacion'       => $ip_cliente,
                'id_usuariocrea'        => $idusuario,
                'id_usuariomod'         => $idusuario,
                'ip_creacion'           => $ip_cliente,

            ];

            $id_hc_procedimiento = hc_procedimientos::insertGetId($input_hc_procedimiento);

            $safetyContext = ['actor_id' => $idusuario, 'ip' => $ip_cliente];
            $procedureAuthorization = app(ClinicalProcedureSafetyService::class)
                ->authorizeForProcedure($id_hc_procedimiento, $request->all(), $safetyContext);

            $input_hc_protocolo = [
                'fecha'                => date('Y-m-d'),
                'id_hc_procedimientos' => $id_hc_procedimiento,
                'hora_inicio'          => date('H:i:s'),
                'hora_fin'             => date('H:i:s'),
                'estado_final'         => ' ',
                'ip_modificacion'      => $ip_cliente,
                'hcid'                 => $id_historia,
                'id_usuariocrea'       => $idusuario,
                'id_usuariomod'        => $idusuario,
                'ip_creacion'          => $ip_cliente,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
                'tipo_procedimiento'   => '2',
            ];
            $protocolId = hc_protocolo::insertGetId($input_hc_protocolo);
            app(ClinicalProcedureSafetyService::class)
                ->consume($procedureAuthorization, 'hc_protocolo', $protocolId, $safetyContext);

            foreach ($procedimientos as $value) {
                $input_pro_final = [
                    'id_hc_procedimientos' => $id_hc_procedimiento,
                    'id_procedimiento'     => $value,
                    'id_usuariocrea'       => $idusuario,
                    'ip_modificacion'      => $ip_cliente,
                    'id_usuariomod'        => $idusuario,
                    'ip_creacion'          => $ip_cliente,
                ];

                Hc_Procedimiento_Final::create($input_pro_final);
            }
            // El cambio de módulo pertenece a la transacción de traslado del
            // controlador. Aquí solo se crea la atención y se devuelve su agenda.
            return $id_agenda;
        }

        return "Ingrese LA Imagen";
    }


}
