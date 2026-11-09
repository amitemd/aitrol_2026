<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Paciente;
use Sis_medico\Support\RequestSchema as Schema;

class V3ClinicalAttentionService
{
    public function detail($appointmentId, $companyId)
    {
        $appointment = DB::table('agenda as appointment')
            ->join('paciente as patient', 'patient.id', '=', 'appointment.id_paciente')
            ->leftJoin('historiaclinica as history', 'history.id_agenda', '=', 'appointment.id')
            ->leftJoin('users as doctor', 'doctor.id', '=', 'appointment.id_doctor1')
            ->leftJoin('tipousuario as doctor_type', 'doctor_type.id', '=', 'doctor.id_tipo_usuario')
            ->leftJoin('especialidad as specialty', 'specialty.id', '=', 'appointment.espid')
            ->leftJoin('procedimiento as primary_procedure', 'primary_procedure.id', '=', 'appointment.id_procedimiento')
            ->leftJoin('seguros as patient_insurance', 'patient_insurance.id', '=', 'patient.id_seguro')
            ->leftJoin('seguros as history_insurance', 'history_insurance.id', '=', 'history.id_seguro')
            ->leftJoin('sala as room', 'room.id', '=', 'appointment.id_sala')
            ->leftJoin('hospital as hospital', 'hospital.id', '=', 'room.id_hospital')
            ->where('appointment.id', (int) $appointmentId)
            ->where('appointment.id_empresa', $companyId)
            ->select(
                'appointment.*', 'history.hcid', 'history.id_seguro as history_insurance_id',
                'doctor.nombre1 as doctor_name1', 'doctor.nombre2 as doctor_name2',
                'doctor.apellido1 as doctor_lastname1', 'doctor.apellido2 as doctor_lastname2',
                'doctor_type.nombre as doctor_type_name', 'specialty.nombre as specialty_name',
                'primary_procedure.nombre as primary_procedure_name',
                'patient_insurance.nombre as patient_insurance_name',
                'history_insurance.nombre as history_insurance_name',
                'room.nombre_sala as room_name', 'hospital.nombre_hospital as hospital_name'
            )->first();

        if (!$appointment) abort(404, 'La atención solicitada no existe en la empresa activa.');
        app(PatientTenantService::class)->requireAccessible($appointment->id_paciente, $companyId);

        $patient = Paciente::withoutGlobalScope('active_company')->find($appointment->id_paciente);
        if (!$patient) abort(404, 'El paciente asociado a la atención no existe.');

        $doctorType = strtoupper(trim((string) $appointment->doctor_type_name));
        $appointment->doctor_is_valid = in_array($doctorType, DoctorUserType::names(), true);
        $appointment->doctor_name = trim($appointment->doctor_lastname1.' '.$appointment->doctor_lastname2.' '.$appointment->doctor_name1.' '.$appointment->doctor_name2);

        $observation = DB::table('paciente_observaciones')->where('id_paciente', $patient->id)->first();
        $allergies = DB::table('paciente_alergia as patient_allergy')
            ->join('principio_activo as allergy', 'allergy.id', '=', 'patient_allergy.id_principio_activo')
            ->where('patient_allergy.id_paciente', $patient->id)
            ->orderBy('allergy.nombre')->get(array('allergy.id', 'allergy.nombre'));

        $day = substr((string) $appointment->fechaini, 0, 10);
        $courtesies = DB::table('agenda')->where('id_empresa', $companyId)
            ->where('id_doctor1', $appointment->id_doctor1)->where('cortesia', 'SI')
            ->whereBetween('fechaini', array($day.' 00:00:00', $day.' 23:59:59'))->count();

        return array(
            'appointment' => $appointment,
            'patient' => $patient,
            'administrativeObservation' => $observation ? $observation->observacion : '',
            'allergies' => $allergies,
            'courtesies' => $courtesies,
            'additionalProcedures' => $this->additionalProcedures($appointment->id),
            'agendaDocuments' => $this->agendaDocuments($patient->id, $companyId),
            'externalExams' => $this->externalExams($patient->id, $companyId),
            'evolutions' => $this->evolutions($patient->id, $companyId),
            'protocols' => $this->protocols($patient->id, $companyId),
            'prescriptions' => $this->prescriptions($patient->id, $companyId),
            'clinicalFiles' => $this->clinicalFileSummary($patient->id, $companyId),
            'laboratoryCount' => $this->laboratoryCount($patient->id, $companyId),
        );
    }

    public function update(Request $request, $appointmentId, $companyId, $patientId)
    {
        $appointment = DB::table('agenda')->where('id', (int) $appointmentId)
            ->where('id_empresa', $companyId)->first();
        if (!$appointment) abort(404, 'La atención solicitada no existe en la empresa activa.');
        if ((string) $appointment->id_paciente !== trim((string) $patientId)) abort(404, 'El paciente no corresponde a la atención solicitada.');
        app(PatientTenantService::class)->requireAccessible($appointment->id_paciente, $companyId);

        $actor = Auth::id();
        $ip = $request->ip();
        $now = date('Y-m-d H:i:s');
        DB::transaction(function () use ($request, $appointment, $actor, $ip, $now) {
            DB::table('paciente')->where('id', $appointment->id_paciente)->update(array(
                'sexo' => (int) $request->input('sexo'),
                'fecha_nacimiento' => $request->input('fecha_nacimiento'),
                'estadocivil' => $request->input('estadocivil'),
                'ocupacion' => $this->upper($request->input('ocupacion')),
                'trabajo' => $this->upper($request->input('trabajo')),
                'ciudad' => $this->upper($request->input('ciudad')),
                'lugar_nacimiento' => $this->upper($request->input('lugar_nacimiento')),
                'direccion' => $this->upper($request->input('direccion')),
                'telefono1' => trim((string) $request->input('telefono1')),
                'telefono2' => trim((string) $request->input('telefono2')),
                'mail_opcional' => strtolower(trim((string) $request->input('mail_opcional'))),
                'referido' => $this->upper($request->input('referido')),
                'alcohol' => $this->upper($request->input('alcohol')),
                'gruposanguineo' => $this->upper($request->input('gruposanguineo')),
                'transfusion' => $request->input('transfusion'),
                'observacion' => trim((string) $request->input('observacion')),
                'vacuna' => trim((string) $request->input('vacuna')),
                'antecedentes_pat' => trim((string) $request->input('antecedentes_pat')),
                'antecedentes_fam' => trim((string) $request->input('antecedentes_fam')),
                'antecedentes_quir' => trim((string) $request->input('antecedentes_quir')),
                'id_usuariomod' => $actor, 'ip_modificacion' => $ip, 'updated_at' => $now,
            ));

            DB::table('agenda')->where('id', $appointment->id)->update(array(
                'cortesia' => $request->input('cortesia'),
                'id_usuariomod' => $actor, 'ip_modificacion' => $ip, 'updated_at' => $now,
            ));

            $adminData = array(
                'observacion' => trim((string) $request->input('observacion_admin')),
                'id_usuariomod' => $actor, 'ip_modificacion' => $ip, 'updated_at' => $now,
            );
            $existingObservation = DB::table('paciente_observaciones')->where('id_paciente', $appointment->id_paciente)->first();
            if ($existingObservation) {
                DB::table('paciente_observaciones')->where('id', $existingObservation->id)->update($adminData);
            } else {
                DB::table('paciente_observaciones')->insert(array_merge($adminData, array(
                    'id_paciente' => $appointment->id_paciente, 'id_usuariocrea' => $actor,
                    'ip_creacion' => $ip, 'created_at' => $now,
                )));
            }

            $allergyIds = array_values(array_unique(array_map('intval', $request->input('ale_list', array()))));
            DB::table('paciente_alergia')->where('id_paciente', $appointment->id_paciente)->delete();
            $allergyNames = array();
            if (count($allergyIds)) {
                $allergyNames = DB::table('principio_activo')->whereIn('id', $allergyIds)->pluck('nombre')->all();
                foreach ($allergyIds as $allergyId) {
                    DB::table('paciente_alergia')->insert(array(
                        'id_paciente' => $appointment->id_paciente, 'id_principio_activo' => $allergyId,
                        'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                        'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                        'created_at' => $now, 'updated_at' => $now,
                    ));
                }
            }
            DB::table('paciente')->where('id', $appointment->id_paciente)->update(array('alergias' => implode('+', $allergyNames)));

            DB::table('log_usuario')->insert(array(
                'id_usuario' => $actor, 'ip_usuario' => $ip,
                'descripcion' => 'ACTUALIZA FILIACION DESDE ATENCION CLINICA V3',
                'dato_ant1' => (string) $appointment->id, 'dato1' => (string) $appointment->id_paciente,
                'created_at' => $now, 'updated_at' => $now,
            ));
        }, 3);

        $patientRecord = Paciente::withoutGlobalScope('active_company')->find($appointment->id_paciente);
        if ($patientRecord) {
            app(PersonIdentityService::class)->sync($patientRecord, 'PACIENTE', 'id', $companyId);
        }
    }

    private function additionalProcedures($appointmentId)
    {
        return DB::table('agenda_procedimiento as appointment_procedure')
            ->join('procedimiento as procedure', 'procedure.id', '=', 'appointment_procedure.id_procedimiento')
            ->where('appointment_procedure.id_agenda', $appointmentId)
            ->orderBy('procedure.nombre')->get(array('procedure.id', 'procedure.nombre'));
    }

    private function agendaDocuments($patientId, $companyId)
    {
        return DB::table('agenda_archivo as archive')->join('agenda as appointment', 'appointment.id', '=', 'archive.id_agenda')
            ->where('appointment.id_paciente', $patientId)->where('appointment.id_empresa', $companyId)
            ->where(function ($query) { $query->whereNotNull('archive.archivo')->orWhereNotNull('archive.texto'); })
            ->orderBy('archive.created_at', 'desc')->limit(6)
            ->get(array('archive.id', 'archive.archivo', 'archive.texto', 'archive.tipo_documento', 'archive.created_at', 'appointment.id as appointment_id'));
    }

    private function externalExams($patientId, $companyId)
    {
        $query = DB::table('paciente_biopsia')->where('id_paciente', $patientId)->where('estado', 1);
        if (Schema::hasColumn('paciente_biopsia', 'id_empresa')) $query->where('id_empresa', $companyId);

        $columns = array('id', 'nombre', 'created_at');
        $columns[] = Schema::hasColumn('paciente_biopsia', 'nombre_anterior')
            ? 'nombre_anterior'
            : DB::raw('nombre as nombre_anterior');

        return $query->orderBy('created_at', 'desc')->limit(6)->get($columns);
    }

    private function evolutions($patientId, $companyId)
    {
        return DB::table('hc_evolucion as evolution')->join('historiaclinica as history', 'history.hcid', '=', 'evolution.hcid')
            ->join('agenda as appointment', 'appointment.id', '=', 'history.id_agenda')
            ->leftJoin('users as doctor', 'doctor.id', '=', 'history.id_doctor1')
            ->leftJoin('especialidad as specialty', 'specialty.id', '=', 'appointment.espid')
            ->where('history.id_paciente', $patientId)->where('appointment.id_empresa', $companyId)
            ->where('appointment.estado_cita', '<>', 3)->orderBy('appointment.fechaini', 'desc')->limit(5)
            ->get(array('evolution.id', 'evolution.hcid', 'evolution.cuadro_clinico', 'evolution.created_at',
                'appointment.id as appointment_id', 'appointment.fechaini', 'specialty.nombre as specialty_name',
                'doctor.nombre1 as doctor_name', 'doctor.apellido1 as doctor_lastname'));
    }

    private function protocols($patientId, $companyId)
    {
        return DB::table('hc_protocolo as protocol')->join('historiaclinica as history', 'history.hcid', '=', 'protocol.hcid')
            ->join('agenda as appointment', 'appointment.id', '=', 'history.id_agenda')
            ->leftJoin('hc_procedimientos as clinical_procedure', 'clinical_procedure.id', '=', 'protocol.id_hc_procedimientos')
            ->leftJoin('procedimiento_completo as procedure', 'procedure.id', '=', 'clinical_procedure.id_procedimiento_completo')
            ->where('history.id_paciente', $patientId)->where('appointment.id_empresa', $companyId)
            ->orderBy('protocol.created_at', 'desc')->limit(5)
            ->get(array('protocol.id', 'protocol.hcid', 'protocol.hallazgos', 'protocol.created_at',
                'appointment.id as appointment_id', 'appointment.fechaini', 'procedure.nombre_general as procedure_name'));
    }

    private function prescriptions($patientId, $companyId)
    {
        return DB::table('hc_receta as prescription')->join('historiaclinica as history', 'history.hcid', '=', 'prescription.id_hc')
            ->join('agenda as appointment', 'appointment.id', '=', 'history.id_agenda')
            ->leftJoin('users as doctor', 'doctor.id', '=', 'history.id_doctor1')
            ->where('history.id_paciente', $patientId)->where('appointment.id_empresa', $companyId)
            ->where(function ($query) {
                $query->whereNotNull('prescription.prescripcion')->orWhereNotNull('prescription.rp')
                    ->orWhereExists(function ($detail) {
                        $detail->select(DB::raw(1))->from('hc_receta_detalle as prescription_detail')
                            ->whereRaw('prescription_detail.id_hc_receta = prescription.id');
                    });
            })->orderBy('prescription.created_at', 'desc')->limit(5)
            ->get(array('prescription.id', 'prescription.id_hc', 'prescription.created_at', 'appointment.fechaini',
                'doctor.nombre1 as doctor_name', 'doctor.apellido1 as doctor_lastname'));
    }

    private function clinicalFileSummary($patientId, $companyId)
    {
        $rows = DB::table('hc_imagenes_protocolo as file')->join('hc_protocolo as protocol', 'protocol.id', '=', 'file.id_hc_protocolo')
            ->join('historiaclinica as history', 'history.hcid', '=', 'protocol.hcid')
            ->join('agenda as appointment', 'appointment.id', '=', 'history.id_agenda')
            ->where('history.id_paciente', $patientId)->where('appointment.id_empresa', $companyId)
            ->whereIn('file.estado', array(1, 2, 3))->groupBy('file.estado')
            ->get(array('file.estado', DB::raw('COUNT(file.id) as total')))->keyBy('estado');
        return array(
            'images' => isset($rows[1]) ? (int) $rows[1]->total : 0,
            'documents' => isset($rows[2]) ? (int) $rows[2]->total : 0,
            'studies' => isset($rows[3]) ? (int) $rows[3]->total : 0,
        );
    }

    private function laboratoryCount($patientId, $companyId)
    {
        $query = DB::table('examen_orden')->where('id_paciente', $patientId)->where('estado', 1);
        if (Schema::hasColumn('examen_orden', 'id_empresa')) $query->where('id_empresa', $companyId);
        return $query->count();
    }

    private function upper($value)
    {
        $value = trim((string) $value);
        return function_exists('mb_strtoupper') ? mb_strtoupper($value, 'UTF-8') : strtoupper($value);
    }
}
