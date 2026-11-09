<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Sis_medico\Anamnesis;
use Sis_medico\ClinicalConsent;
use Sis_medico\ClinicalDocument;
use Sis_medico\ClinicalDocumentAmendment;
use Sis_medico\ClinicalEncounter;
use Sis_medico\ClinicalEncounterEvent;
use Sis_medico\ClinicalProcedureAuthorization;
use Sis_medico\Firma_Usuario;
use Sis_medico\Hc_Anestesiologia;
use Sis_medico\Hc_Epicrisis_Final;
use Sis_medico\Hc_Evolucion;
use Sis_medico\Hc_Evolucion_Anestesiologo;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Log_Solicitud;
use Sis_medico\Ho_Solicitud;
use Sis_medico\Ho_Traspaso_Sala008;
use Sis_medico\Rc_pre_anestesica;
use Sis_medico\hc_procedimientos;
use Sis_medico\hc_protocolo;
use Sis_medico\Support\RequestSchema;

class HospitalDocumentService
{
    const BOUND = 'HOSPITAL_SURGERY_BOUND';
    const STARTED = 'HOSPITAL_SURGERY_STARTED';
    const FINISHED = 'HOSPITAL_SURGERY_FINISHED';
    const RECOVERED = 'HOSPITAL_SURGERY_RECOVERED';
    const PREOP = 'hospital_rc_pre_anestesica';
    const RECOVERY = 'hospital_postanestesia';

    public function obtain($id)
    {
        $solicitud = Ho_Solicitud::findOrFail($id);
        $this->authorize($solicitud);
        return $solicitud;
    }

    public function authorize(Ho_Solicitud $solicitud, $action = 'hospital.documents.read')
    {
        $user = Auth::user();
        abort_unless($user && (int) $user->estado === 1, 403);
        $isSystemUser = method_exists($user, 'isSystemUser') && $user->isSystemUser();
        $companyId = $isSystemUser ? null : app(ClinicalHospitalDestinationService::class)->companyId();
        $encounter = ClinicalEncounter::find($solicitud->encounter_id);
        abort_unless($encounter && (int) $solicitud->estado_paso > 0
            && $encounter->care_setting !== ClinicalEncounterService::AMBULATORY
            && ($isSystemUser || (string) $encounter->id_empresa === $companyId)
            && (string) $encounter->id_paciente === (string) $solicitud->id_paciente, 403,
            'Se requiere un episodio hospitalario de la empresa activa.');
        try {
            app(ClinicalAccessControlService::class)->assertAllowed($encounter->id, $action,
                ['user' => $user, 'ip' => request()->ip()]);
        } catch (\Sis_medico\Exceptions\ClinicalAccessDeniedException $exception) {
            abort(403, $exception->getMessage());
        }
        return $encounter;
    }

    public static function writable($solicitud, $encounter, $documentation = false)
    {
        return (int) $solicitud->estado === 1 && !$solicitud->fecha_salida && !$encounter->ended_at
            && in_array($encounter->status, $documentation ? ['ACTIVE', 'CLOSURE_PENDING'] : ['ACTIVE'], true)
            && ($documentation || $encounter->clinical_state !== 'CLOSURE_PENDING');
    }

    private function mutate($id, $callback, $documentation = false, array $roles = ['SISTEMAS', 'DOCTORES', 'DOCTOR'])
    {
        return DB::transaction(function () use ($id, $callback, $documentation, $roles) {
            $solicitud = Ho_Solicitud::where('id', $id)->lockForUpdate()->firstOrFail();
            $encounter = $this->authorize($solicitud, 'hospital.documents.write');
            $encounter = ClinicalEncounter::where('id', $encounter->id)->lockForUpdate()->firstOrFail();
            abort_unless(Auth::user()->hasAnyUserTypeName($roles), 403,
                'Esta accion requiere un profesional clinico autorizado; Sistemas no puede firmar.');
            $this->requireTrue(self::writable($solicitud, $encounter, $documentation),
                'El episodio esta cerrado o no admite nuevas acciones asistenciales.');
            return $callback($solicitud);
        }, 3);
    }

    private function procedure(Ho_Solicitud $solicitud, $id)
    {
        $procedure = hc_procedimientos::with('historia')->where('id', $id)->firstOrFail();
        $history = $procedure->historia;
        abort_unless($history && (string) $history->encounter_id === (string) $solicitud->encounter_id
            && (string) $history->id_paciente === (string) $solicitud->id_paciente, 403,
            'El procedimiento no corresponde al episodio.');
        $this->requireTrue((int) $procedure->id_procedimiento_completo !== 40
            && \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedure->id)->exists(),
            'El procedimiento tecnico de ingreso no es un caso quirurgico. Seleccione o cree la operacion real.');
        return $procedure;
    }

    public function assertCanPlan(Ho_Solicitud $solicitud)
    {
        $encounter = $this->authorize($solicitud, 'hospital.documents.write');
        $this->requireTrue(self::writable($solicitud, $encounter), 'El episodio no admite nuevos procedimientos.');
        $this->receipt($solicitud);
        foreach ($this->bindings($solicitud) as $binding) $this->assertCycleComplete($solicitud, $binding);
    }

    private function receipt(Ho_Solicitud $solicitud)
    {
        $this->requireTrue((int) $solicitud->estado_paso === 5, 'El episodio no esta recibido en Cirugia.');
        $logs = Ho_Log_Solicitud::where('id_ho_solicitud', $solicitud->id)->where('estado_paso', 5)
            ->whereNull('fecha_salida')->get();
        $transfers = Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)->where('paso', 5)
            ->where('estado', 2)->get();
        $this->requireTrue($logs->count() === 1 && $transfers->count() === 1,
            'No hay una recepcion quirurgica inequivoca. Requiere conciliacion manual del episodio legado.');
        return $logs->first();
    }

    private function bindings(Ho_Solicitud $solicitud)
    {
        return ClinicalEncounterEvent::where('encounter_id', $solicitud->encounter_id)
            ->where('event_type', self::BOUND)->orderBy('id')->get();
    }

    private function binding(Ho_Solicitud $solicitud, $procedureId, $current = true)
    {
        $this->procedure($solicitud, $procedureId);
        $bindings = $this->bindings($solicitud)->filter(function ($event) use ($procedureId) {
            return (string) $event->to_value === (string) $procedureId;
        });
        $this->requireTrue($bindings->count() === 1, 'Vincule explicitamente el procedimiento a la recepcion actual.');
        $binding = $bindings->first();
        $log = Ho_Log_Solicitud::find($binding->from_value);
        $this->requireTrue($log && (string) $log->id_ho_solicitud === (string) $solicitud->id
            && (int) $log->estado_paso === 5, 'El ciclo pertenece a otra solicitud.');
        if ($current) $this->requireTrue((string) $binding->from_value === (string) $this->receipt($solicitud)->id,
            'El procedimiento pertenece a una recepcion quirurgica anterior.');
        return $binding;
    }

    public function bindProcedure(Ho_Solicitud $solicitud, $procedureId)
    {
        return $this->mutate($solicitud->id, function ($solicitud) use ($procedureId) {
            $receipt = $this->receipt($solicitud);
            $procedure = $this->procedure($solicitud, $procedureId);
            foreach ($this->bindings($solicitud) as $binding) {
                if ((string) $binding->to_value === (string) $procedureId) {
                    $this->requireTrue((string) $binding->from_value === (string) $receipt->id,
                        'Un procedimiento de otro ciclo no puede reutilizarse.');
                    return $binding;
                }
                $this->requireTrue($this->checkpoint($binding, self::RECOVERED),
                    'Complete el ciclo quirurgico anterior antes de vincular otro procedimiento.');
            }
            $used = ClinicalProcedureAuthorization::where('procedure_key', 'hc_procedimientos:'.$procedureId)
                ->where('status', 'USED')->exists();
            $this->requireTrue(!$used && !Hc_Evolucion_Anestesiologo::where('hc_id_procedimiento', $procedureId)->exists(),
                'El procedimiento ya tiene actividad quirurgica previa. Requiere conciliacion manual, no reutilizacion.');
            $this->requireTrue(!hc_protocolo::where('id_hc_procedimientos', $procedureId)
                ->whereNotNull('conclusion')->where('conclusion', '<>', '')->exists(),
                'El procedimiento ya tiene un informe. No puede asociarse a otro ciclo.');
            return $this->event($solicitud, self::BOUND, $receipt->id, $procedure->id, 'Procedimiento seleccionado explicitamente.');
        });
    }

    public function prepareAnesthesia($id, $procedureId)
    {
        return $this->mutate($id, function ($solicitud) use ($procedureId) {
            $binding = $this->binding($solicitud, $procedureId);
            $this->requireTrue(!$this->checkpoint($binding, self::STARTED), 'El procedimiento ya fue iniciado.');
            $procedure = $this->procedure($solicitud, $procedureId);
            $anesthesia = $this->one(Hc_Anestesiologia::where('id_hc_procedimientos', $procedureId)->get(), false);
            if (!$anesthesia) $anesthesia = Hc_Anestesiologia::create(array_merge($this->audit(), [
                'id_hc' => $procedure->id_hc, 'id_hc_procedimientos' => $procedureId, 'fecha' => date('Y-m-d H:i:s'),
            ]));
            $preop = $this->one(Rc_pre_anestesica::where('id_hc_anestesiologia', $anesthesia->id)->where('estado', 1)->get(), false);
            if (!$preop) Rc_pre_anestesica::create(array_merge($this->audit(), ['id_hc_anestesiologia' => $anesthesia->id, 'estado' => 1]));
            return $anesthesia;
        }, false, ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ANESTESIÓLOGO', 'ANESTESIOLOGO']);
    }

    private function anesthesia($procedure)
    {
        $anesthesia = $this->one(Hc_Anestesiologia::where('id_hc_procedimientos', $procedure->id)->get());
        $this->requireTrue((string) $anesthesia->id_hc === (string) $procedure->id_hc, 'La anestesia no coincide con la historia.');
        return $anesthesia;
    }

    private function source(Ho_Solicitud $solicitud, $procedureId, $kind)
    {
        $procedure = $this->procedure($solicitud, $procedureId);
        if ($kind === 'preop') {
            return $this->one(Rc_pre_anestesica::where('id_hc_anestesiologia', $this->anesthesia($procedure)->id)
                ->where('estado', 1)->get());
        }
        if ($kind === 'protocol') return $this->latestProtocol($procedureId);
        if ($kind === 'recovery') {
            $source = $this->one(Hc_Evolucion_Anestesiologo::where('hc_id_procedimiento', $procedureId)->get());
            $this->requireTrue((string) $source->id_hc === (string) $procedure->id_hc
                && (string) $source->id_hc_anestesiologia === (string) $this->anesthesia($procedure)->id,
                'El registro postanestesico no corresponde al procedimiento.');
            return $source;
        }
        throw new \InvalidArgumentException('Tipo documental no permitido.');
    }

    private function documentSource($kind)
    {
        return ['preop' => self::PREOP, 'protocol' => 'hc_protocolo', 'recovery' => self::RECOVERY][$kind];
    }

    private function document($kind, $sourceId)
    {
        return ClinicalDocument::where('source_type', $this->documentSource($kind))->where('source_id', (string) $sourceId)->first();
    }

    public static function recoveryFields()
    {
        return ['diagnostico' => 'Diagnostico', 'cirugia_realizada' => 'Cirugia realizada',
            'tecnica_anestesica' => 'Tecnica anestesica',
            'evolucion' => 'Evolucion postanestesica', 'condicion_salir' => 'Condicion al salir', 'conducido_a' => 'Destino'];
    }

    public static function recoveryOptions()
    {
        return ['tecnica_anestesica' => ['0' => 'General', '1' => 'Local', '2' => 'Troncular',
            '3' => 'Neuroaxial', '4' => 'Intravenosa', '5' => 'Sedacion'],
            'condicion_salir' => ['1' => 'Extubado', '2' => 'Intubado'],
            'conducido_a' => ['1' => 'Hospitalizacion', '2' => 'Cuidados intensivos', '3' => 'Criticos de emergencia',
                '4' => 'Domicilio', '5' => 'Morgue']];
    }

    private function fields($kind, $source, $binding)
    {
        $fields = $source->toArray();
        foreach (['created_at', 'updated_at', 'ip_creacion', 'ip_modificacion', 'id_usuariomod'] as $key) unset($fields[$key]);
        if ($kind !== 'protocol') {
            $fields['_hospital_binding_id'] = (string) $binding->id;
            $fields['_hospital_procedure_id'] = (string) $binding->to_value;
        }
        return $fields;
    }

    public static function effectiveFields($document)
    {
        $amendment = ClinicalDocumentAmendment::where('clinical_document_id', $document->id)->orderByDesc('version')->first();
        return json_decode($amendment ? $amendment->amended_content : $document->content_snapshot, true) ?: [];
    }

    public static function sameContent(array $left, array $right)
    {
        $normalize = function ($value) use (&$normalize) {
            if (!is_array($value)) return $value === null ? null : (string) $value;
            ksort($value);
            foreach ($value as $key => $item) $value[$key] = $normalize($item);
            return $value;
        };
        return $normalize($left) === $normalize($right);
    }

    private function signedSource($solicitud, $binding, $kind)
    {
        $source = $this->source($solicitud, $binding->to_value, $kind);
        $documentLabels = [
            'preop' => 'la valoración preanestésica',
            'protocol' => 'el protocolo quirúrgico',
            'recovery' => 'la recuperación postanestésica',
        ];
        $documentLabel = isset($documentLabels[$kind]) ? $documentLabels[$kind] : 'el documento clínico';
        $this->requireTrue($source, $kind === 'protocol'
            ? 'El protocolo quirurgico aun no esta creado. Inicie el procedimiento antes de firmarlo.'
            : 'La valoracion preanestesica aun no esta creada. Prepare primero el procedimiento.');
        $document = $this->document($kind, $source->id);
        $this->requireTrue($document && $document->signed_at
            && (string) $document->encounter_id === (string) $solicitud->encounter_id,
            'Falta firmar '.$documentLabel.' de este procedimiento. Abra el documento, guarde la información y presione Firmar.');
        $effective = self::effectiveFields($document);
        if ($kind !== 'protocol') $this->requireTrue(isset($effective['_hospital_binding_id']) && (string) $effective['_hospital_binding_id'] === (string) $binding->id
            && (string) $effective['_hospital_procedure_id'] === (string) $binding->to_value, 'La firma corresponde a otro ciclo.');
        // Recovery amendments preserve the original source row; shared forms edit their live source.
        $expected = $kind === 'recovery' ? (json_decode($document->content_snapshot, true) ?: []) : $effective;
        $this->requireTrue(self::sameContent($expected, $this->fields($kind, $source, $binding)),
            'El contenido cambio fuera del documento firmado. Revise y firme una adenda antes de continuar.');
        return $document;
    }

    private function register($solicitud, $binding, $kind, $source, array $fields, $sign, $reason = '')
    {
        $records = app(ClinicalRecordService::class);
        $this->requireTrue($records->available(), 'El expediente canonico no esta disponible.');
        $types = ['preop' => 'HOSPITAL_PREANESTHETIC', 'protocol' => 'PROCEDURE_REPORT', 'recovery' => 'HOSPITAL_POSTANESTHETIC'];
        $existing = $this->document($kind, $source->id);
        if ($existing) {
            $this->requireTrue((string) $existing->encounter_id === (string) $solicitud->encounter_id, 'Documento vinculado a otro episodio.');
            if ($existing->signed_at) {
                $this->requireTrue($sign, 'Las correcciones de documentos firmados se registran como adendas firmadas.');
                $this->requireTrue(strlen(trim($reason)) >= 5, 'Indique el motivo de la adenda.');
                if (self::sameContent(self::effectiveFields($existing), $fields)) return $existing;
            }
        }
        if ($sign) {
            $this->authorize($solicitud, 'hospital.documents.sign');
            $userId = trim((string) Auth::id());
            abort_unless(Firma_Usuario::whereRaw('TRIM(id_usuario) = ?', [$userId])->exists(), 403,
                'No hay firma registrada para el usuario '.$userId.'. Registrela en firma_usuario.id_usuario.');
            $this->validateComplete($kind, $fields);
        }
        $records->registerDocument($solicitud->encounter_id, $solicitud->id_paciente, $types[$kind],
            $this->documentSource($kind), $source->id,
            ['title' => ['preop' => 'Valoracion preanestesica hospitalaria', 'protocol' => 'Informe de procedimiento',
                'recovery' => 'Registro postanestesico hospitalario'][$kind], 'fields' => $fields, 'amendment_reason' => $reason], $this->context());
        if ($sign && (!$existing || !$existing->signed_at)) {
            $records->signDocument($this->documentSource($kind), $source->id, Auth::id(), $this->context());
        }
        return $this->document($kind, $source->id);
    }

    private function validateComplete($kind, array $fields)
    {
        $required = $kind === 'preop' ? ['estado_fisico', 'riesgo_quirurgico', 'plan_anestesico']
            : ($kind === 'protocol' ? ['hallazgos', 'conclusion', 'hora_inicio', 'hora_fin', 'estado_final'] : array_keys(self::recoveryFields()));
        $labels = [
            'estado_fisico' => 'Estado físico ASA',
            'riesgo_quirurgico' => 'Riesgo quirúrgico',
            'plan_anestesico' => 'Plan anestésico',
            'hallazgos' => 'Hallazgos de la cirugía',
            'conclusion' => 'Conclusión del protocolo',
            'hora_inicio' => 'Hora de inicio',
            'hora_fin' => 'Hora de finalización',
            'estado_final' => 'Estado final del paciente',
            'diagnostico' => 'Diagnóstico postanestésico',
            'cirugia_realizada' => 'Cirugía realizada',
            'tecnica_anestesica' => 'Técnica anestésica',
            'evolucion' => 'Evolución postanestésica',
            'condicion_salir' => 'Condición al salir',
            'conducido_a' => 'Destino del paciente',
        ];
        foreach ($required as $key) $this->requireTrue(isset($fields[$key]) && trim(strip_tags((string) $fields[$key])) !== '',
            'Complete el campo '.(isset($labels[$key]) ? $labels[$key] : $key).' antes de firmar.');
    }

    public function signSource($id, $procedureId, $kind, $reason = '')
    {
        $this->requireTrue(in_array($kind, ['preop', 'protocol'], true), 'Tipo documental no permitido.');
        return $this->mutate($id, function ($solicitud) use ($procedureId, $kind, $reason) {
            $binding = $this->binding($solicitud, $procedureId, false);
            if ($kind === 'protocol') $this->requireTrue($this->checkpoint($binding, self::STARTED), 'Inicie primero el procedimiento.');
            $source = $this->source($solicitud, $procedureId, $kind);
            $this->requireTrue($source, $kind === 'protocol'
                ? 'El protocolo quirurgico aun no esta creado. Inicie el procedimiento antes de firmarlo.'
                : 'La valoracion preanestesica aun no esta creada. Prepare primero el procedimiento.');
            $existing = $this->document($kind, $source->id);
            if (!$existing || !$existing->signed_at) $this->binding($solicitud, $procedureId);
            return $this->register($solicitud, $binding, $kind, $source, $this->fields($kind, $source, $binding), true, $reason);
        }, true, $kind === 'preop'
            ? ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ANESTESIÓLOGO', 'ANESTESIOLOGO']
            : ['SISTEMAS', 'DOCTORES', 'DOCTOR']);
    }

    public function start($id, $procedureId, array $data)
    {
        return $this->mutate($id, function ($solicitud) use ($procedureId, $data) {
            $binding = $this->binding($solicitud, $procedureId);
            if ($event = $this->checkpoint($binding, self::STARTED)) return $event;
            $this->signedSource($solicitud, $binding, 'preop');
            $safety = app(ClinicalProcedureSafetyService::class);
            $authorization = $safety->authorizeForProcedure($procedureId, $data, $this->context());
            $this->assertAuthorization($solicitud, $procedureId, $authorization, 'AUTHORIZED');
            $protocol = $this->latestProtocol($procedureId);
            if (!$protocol) $protocol = hc_protocolo::create(array_merge($this->audit(), [
                'id_hc_procedimientos' => $procedureId, 'hcid' => $this->procedure($solicitud, $procedureId)->id_hc,
                'fecha' => date('Y-m-d'), 'hora_inicio' => date('H:i:s'), 'tipo_procedimiento' => 3,
            ]));
            $safety->consume($authorization, 'hc_protocolo', $protocol->id, $this->context());
            return $this->event($solicitud, self::STARTED, $binding->id, $protocol->id, 'Inicio quirurgico autorizado.');
        });
    }

    private function assertAuthorization($solicitud, $procedureId, $authorization, $status)
    {
        $this->requireTrue($authorization && $authorization->status === $status
            && $authorization->procedure_key === 'hc_procedimientos:'.$procedureId
            && (string) $authorization->encounter_id === (string) $solicitud->encounter_id
            && (string) $authorization->patient_id === (string) $solicitud->id_paciente, 'Autorizacion de otro procedimiento o no vigente.');
        if ($authorization->emergency_exception) {
            $this->requireTrue($authorization->exception_authorized_by && strlen(trim($authorization->emergency_reason)) >= 10,
                'La excepcion quirurgica requiere autorizacion y motivo.');
            return;
        }
        $consent = ClinicalConsent::find($authorization->consent_id);
        $this->requireTrue($consent && $consent->status === 'SIGNED' && $consent->identity_verified
            && $authorization->identity_verified && $authorization->checklist_complete
            && (string) $consent->encounter_id === (string) $solicitud->encounter_id
            && (string) $consent->patient_id === (string) $solicitud->id_paciente
            && $consent->procedure_key === $authorization->procedure_key
            && ($status !== 'AUTHORIZED' || !$consent->expires_at || strtotime($consent->expires_at) > time()),
            'Consentimiento ausente, vencido, revocado o de otro procedimiento.');
    }

    public function finish($id, $procedureId)
    {
        return $this->mutate($id, function ($solicitud) use ($procedureId) {
            $binding = $this->binding($solicitud, $procedureId);
            $start = $this->checkpoint($binding, self::STARTED);
            $this->requireTrue($start, 'No existe un inicio quirurgico autorizado.');
            $this->signedSource($solicitud, $binding, 'preop');
            $document = $this->signedSource($solicitud, $binding, 'protocol');
            $this->requireTrue((string) $document->source_id === (string) $start->to_value, 'El protocolo no es el utilizado al iniciar.');
            if ($event = $this->checkpoint($binding, self::FINISHED)) return $event;
            return $this->event($solicitud, self::FINISHED, $binding->id, $document->id, 'Protocolo firmado; ingreso a recuperacion.');
        });
    }

    public function recovery($id, $procedureId)
    {
        $solicitud = $this->obtain($id);
        $binding = $this->binding($solicitud, $procedureId, false);
        $procedure = $this->procedure($solicitud, $procedureId);
        $source = $this->one(Hc_Evolucion_Anestesiologo::where('hc_id_procedimiento', $procedureId)->get(), false);
        $document = $source ? $this->document('recovery', $source->id) : null;
        $fields = $document && $document->signed_at ? self::effectiveFields($document) : ($source ? $source->toArray() : []);
        return compact('solicitud', 'binding', 'procedure', 'source', 'document', 'fields');
    }

    public function saveRecovery($id, $procedureId, array $data)
    {
        $rules = ['status' => 'required|in:DRAFT,SIGNED', 'amendment_reason' => 'nullable|string|min:5|max:2000'];
        foreach (self::recoveryFields() as $key => $label) $rules[$key] = 'nullable|string|max:250';
        foreach (self::recoveryOptions() as $key => $options) $rules[$key] = 'nullable|in:'.implode(',', array_keys($options));
        $validated = Validator::make($data, $rules)->validate();
        return $this->mutate($id, function ($solicitud) use ($procedureId, $validated) {
            $binding = $this->binding($solicitud, $procedureId, false);
            $this->requireTrue($this->checkpoint($binding, self::FINISHED), 'Finalice el procedimiento con su protocolo firmado.');
            $this->signedSource($solicitud, $binding, 'protocol');
            $procedure = $this->procedure($solicitud, $procedureId);
            $anesthesia = $this->anesthesia($procedure);
            $source = $this->one(Hc_Evolucion_Anestesiologo::where('hc_id_procedimiento', $procedureId)->get(), false);
            if ($source) $this->source($solicitud, $procedureId, 'recovery');
            $document = $source ? $this->document('recovery', $source->id) : null;
            if (!$document || !$document->signed_at) $this->binding($solicitud, $procedureId);
            $values = [];
            foreach (self::recoveryFields() as $key => $label) $values[$key] = isset($validated[$key]) ? $validated[$key] : null;
            $sign = $validated['status'] === 'SIGNED';
            if (!$document || !$document->signed_at) {
                $source = HospitalPostanesthesiaGuard::run(function () use ($source, $procedure, $anesthesia, $values) {
                    $source = $source ?: new Hc_Evolucion_Anestesiologo();
                    $source->fill(array_merge($this->audit(), $values, ['id_hc' => $procedure->id_hc,
                        'hc_id_procedimiento' => $procedure->id, 'id_hc_anestesiologia' => $anesthesia->id, 'estado' => 1]));
                    $source->save();
                    return $source;
                });
            }
            $fields = array_merge($this->fields('recovery', $source, $binding), $values);
            $document = $this->register($solicitud, $binding, 'recovery', $source, $fields, $sign,
                isset($validated['amendment_reason']) ? $validated['amendment_reason'] : '');
            if ($sign && !$this->checkpoint($binding, self::RECOVERED)) {
                $this->event($solicitud, self::RECOVERED, $binding->id, $document->id, 'Recuperacion documentada y firmada.');
            }
            return $document;
        }, true, ['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ANESTESIÓLOGO', 'ANESTESIOLOGO']);
    }

    public function assertReadyForTransfer(Ho_Solicitud $solicitud)
    {
        if ((int) $solicitud->estado_paso !== 5) return;
        $this->authorize($solicitud);
        $receipt = $this->receipt($solicitud);
        $bindings = $this->bindings($solicitud)->filter(function ($binding) use ($receipt) {
            return (string) $binding->from_value === (string) $receipt->id;
        });
        $this->requireTrue($bindings->count() > 0, 'Falta el procedimiento de la recepcion quirurgica actual.');
        foreach ($bindings as $binding) $this->assertCycleComplete($solicitud, $binding);
    }

    public function assertReadyForDischarge(Ho_Solicitud $solicitud, $closureType = 'DISCHARGE')
    {
        // The closure owner supplies the validated decision, never raw request data.
        if ($closureType !== 'DISCHARGE') return;
        $this->authorize($solicitud);
        $logs = Ho_Log_Solicitud::where('id_ho_solicitud', $solicitud->id)->where('estado_paso', 5)->get();
        foreach ($logs as $log) {
            $bindings = $this->bindings($solicitud)->filter(function ($binding) use ($log) {
                return (string) $binding->from_value === (string) $log->id;
            });
            $this->requireTrue($bindings->count() > 0, 'Hay una estancia quirurgica sin procedimiento conciliado.');
            foreach ($bindings as $binding) $this->assertCycleComplete($solicitud, $binding);
        }
        if ((int) $solicitud->estado_paso === 5) $this->assertReadyForTransfer($solicitud);
    }

    private function assertCycleComplete($solicitud, $binding)
    {
        $start = $this->checkpoint($binding, self::STARTED);
        $finish = $this->checkpoint($binding, self::FINISHED);
        $recovery = $this->checkpoint($binding, self::RECOVERED);
        $this->requireTrue($start && $finish && $recovery, 'Faltan inicio, protocolo finalizado o recuperacion firmada del ciclo.');
        $this->signedSource($solicitud, $binding, 'preop');
        $protocol = $this->signedSource($solicitud, $binding, 'protocol');
        $postop = $this->signedSource($solicitud, $binding, 'recovery');
        $this->requireTrue((string) $start->to_value === (string) $protocol->source_id
            && (string) $finish->to_value === (string) $protocol->id && (string) $recovery->to_value === (string) $postop->id,
            'Los checkpoints no corresponden a los documentos del procedimiento.');
    }

    private function primaryLinks(Ho_Solicitud $solicitud, $histories, $readOnly, array $availability)
    {
        $links = [];
        if ($solicitud->id_agenda && Route::has('formulario.001')) {
            $links[] = ['key' => 'admission', 'label' => '001 - Admisión'];
        }
        if (!empty($availability[ClinicalHospitalFormAvailabilityService::FORM_008])
            && Route::has('hospital.nuevo.formulario008_pdf')
            && Ho_Log_Solicitud::where('id_ho_solicitud', $solicitud->id)
                ->where('estado_paso', 2)->whereNotNull('id_agenda')->exists()) {
            $links[] = ['key' => 'emergency', 'label' => '008 - Emergencia'];
        }
        if (Route::has('epicrisis.mostrar_final')) {
            $links[] = ['key' => 'epicrisis', 'label' => 'Epicrisis final'];
        }
        if (!$readOnly && $histories->isNotEmpty() && Route::has('formulario005.index_005')) {
            $links[] = ['key' => 'evolution', 'label' => '005 - Evolución'];
        }
        return $links;
    }

    public function index($id)
    {
        $solicitud = Ho_Solicitud::findOrFail($id);
        $encounter = $this->authorize($solicitud);
        $documents = ClinicalDocument::with('amendments')->where('encounter_id', $encounter->id)->orderBy('authored_at')->get();
        $histories = Historiaclinica::where('encounter_id', $encounter->id)->where('id_paciente', $solicitud->id_paciente)->pluck('hcid');
        $procedures = hc_procedimientos::whereIn('id_hc', $histories)
            ->where(function ($query) {
                $query->whereNull('id_procedimiento_completo')->orWhere('id_procedimiento_completo', '<>', 40);
            })->orderBy('id')->get();
        $cycles = [];
        foreach ($this->bindings($solicitud) as $binding) {
            $log = Ho_Log_Solicitud::find($binding->from_value);
            if (!$log || (string) $log->id_ho_solicitud !== (string) $solicitud->id) continue;
            $pending = null;
            try { $this->assertCycleComplete($solicitud, $binding); }
            catch (\InvalidArgumentException $exception) { $pending = $exception->getMessage(); }
            $cycles[] = ['binding' => $binding, 'pending' => $pending,
                'current' => (int) $solicitud->estado_paso === 5 && !$log->fecha_salida,
                'started' => $this->checkpoint($binding, self::STARTED),
                'finished' => $this->checkpoint($binding, self::FINISHED),
                'recovered' => $this->checkpoint($binding, self::RECOVERED)];
        }
        $pending = [];
        foreach ($cycles as $cycle) if ($cycle['pending']) $pending[] = 'Procedimiento '.$cycle['binding']->to_value.': '.$cycle['pending'];
        $logs = Ho_Log_Solicitud::where('id_ho_solicitud', $solicitud->id)->where('estado_paso', 5)->get();
        foreach ($logs as $log) {
            $found = false;
            foreach ($cycles as $cycle) if ((string) $cycle['binding']->from_value === (string) $log->id) $found = true;
            if (!$found) $pending[] = 'Recepcion quirurgica '.$log->id.': procedimiento sin conciliar.';
        }
        $readOnly = !self::writable($solicitud, $encounter, true);
        $operational = self::writable($solicitud, $encounter);
        $availability = app(ClinicalHospitalFormAvailabilityService::class)
            ->forSolicitudes([(int) $solicitud->id])[(int) $solicitud->id];
        $links = $this->primaryLinks($solicitud, $histories, $readOnly, $availability);
        $role = (int) Auth::user()->id_tipo_usuario;
        $received = (int) $solicitud->estado_paso === 5
            ? Ho_Traspaso_Sala008::where('id_solicitud', $solicitud->id)->where('paso', 5)->where('estado', 2)->get()
            : collect();
        $surgicalReceipt = $received->count() === 1 ? $received->first() : null;
        $canOperateSurgery = (int) $solicitud->estado_paso === 5 && $operational
            && Auth::user()->hasAnyUserTypeName(['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ANESTESIÓLOGO', 'ANESTESIOLOGO']);
        $needsConsentFormats = $canOperateSurgery && !$readOnly
            && collect($cycles)->contains(function ($cycle) { return $cycle['current'] && !$cycle['started']; });
        $consentFormats = $needsConsentFormats
            ? \Sis_medico\Formato_consentimiento::where('estado', 1)
                ->whereNotNull('nombre')->where('nombre', '<>', '')
                ->selectRaw('MIN(id) as id, MIN(nombre) as nombre')
                ->groupBy(\Illuminate\Support\Facades\DB::raw('UPPER(TRIM(nombre))'))
                ->orderBy('nombre')->get()
            : collect();
        $catalog = $canOperateSurgery && $surgicalReceipt
            ? \Sis_medico\Procedimiento::where('estado', 1)
                ->where('nombre', '<>', 'EVOLUCION HOSPITALARIA')->orderBy('nombre')->get()
            : collect();
        $doctors = collect();
        if ($canOperateSurgery && $surgicalReceipt) {
            $doctorRoleId = \Sis_medico\User::roleIdForName(array('DOCTORES', 'DOCTOR'));
            $companyId = app(ClinicalHospitalDestinationService::class)->companyId();
            $doctors = \Sis_medico\User::withoutGlobalScopes()
                ->where('estado', 1)
                ->when($doctorRoleId !== null, function ($query) use ($doctorRoleId) {
                    $query->where('id_tipo_usuario', $doctorRoleId);
                })
                ->where(function ($query) use ($companyId) {
                    $hasCompanyRelation = false;
                    if (\Sis_medico\Support\RequestSchema::hasTable('usuario_empresa')) {
                        $hasCompanyRelation = true;
                        $query->whereExists(function ($membership) use ($companyId) {
                            $membership->select(\Illuminate\Support\Facades\DB::raw(1))
                                ->from('usuario_empresa as doctor_company')
                                ->whereColumn('doctor_company.id_usuario', 'users.id')
                                ->where('doctor_company.id_empresa', $companyId);
                        });
                    }
                    if (\Sis_medico\Support\RequestSchema::hasTable('persona_empresa')) {
                        $method = $hasCompanyRelation ? 'orWhereExists' : 'whereExists';
                        $query->{$method}(function ($membership) use ($companyId) {
                            $membership->select(\Illuminate\Support\Facades\DB::raw(1))
                                ->from('persona_empresa as doctor_person_company')
                                ->whereColumn('doctor_person_company.persona_id', 'users.persona_id')
                                ->where('doctor_person_company.id_empresa', $companyId)
                                ->where('doctor_person_company.rol', 'USUARIO')
                                ->where('doctor_person_company.estado', 1);
                        });
                    }
                })
                ->orderBy('apellido1')->orderBy('nombre1')->get();
        }
        $plannedProcedureIds = [];
        $plannedDoctorId = null;
        $plannedStart = null;
        $plannedEnd = null;
        $plannedCycle = null;
        if ($canOperateSurgery && $surgicalReceipt) {
            $plannedCycle = collect($cycles)->first(function ($cycle) {
                return $cycle['current'] && !$cycle['started'];
            });
            if (!$plannedCycle) $plannedCycle = collect($cycles)->first(function ($cycle) {
                return $cycle['current'];
            });
            if (!$plannedCycle) $plannedCycle = collect($cycles)->first();
        }
        if ($plannedCycle) {
            $plannedProcedure = hc_procedimientos::find($plannedCycle['binding']->to_value);
            if ($plannedProcedure) {
                $plannedHistory = Historiaclinica::where('hcid', $plannedProcedure->id_hc)->first();
                $plannedAgenda = $plannedHistory ? DB::table('agenda')->where('id', $plannedHistory->id_agenda)->first() : null;
                $plannedProcedureIds = DB::table('hc_procedimiento_final')
                    ->where('id_hc_procedimientos', $plannedProcedure->id)->pluck('id_procedimiento')->all();
                if (!$plannedProcedureIds && $plannedAgenda && $plannedAgenda->id_procedimiento) {
                    $plannedProcedureIds = [(int) $plannedAgenda->id_procedimiento];
                }
                $plannedDoctorId = $plannedAgenda && $plannedAgenda->id_doctor1
                    ? $plannedAgenda->id_doctor1 : $plannedProcedure->id_doctor_examinador;
                $plannedStart = $plannedAgenda && $plannedAgenda->fechaini ? date('Y-m-d\\TH:i', strtotime($plannedAgenda->fechaini)) : null;
                $plannedEnd = $plannedAgenda && $plannedAgenda->fechafin ? date('Y-m-d\\TH:i', strtotime($plannedAgenda->fechafin)) : null;
            }
        }
        if ($canOperateSurgery && $surgicalReceipt && $plannedDoctorId && !$doctors->contains('id', $plannedDoctorId)) {
            $storedDoctor = \Sis_medico\User::withoutGlobalScopes()->find($plannedDoctorId);
            if ($storedDoctor) $doctors->push($storedDoctor);
        }
        $draftCounts = \Sis_medico\Support\RequestSchema::hasTable('hospital_episode_form_drafts')
            ? DB::table('hospital_episode_form_drafts')->where('encounter_id', $encounter->id)
                ->where('solicitud_id', $solicitud->id)
                ->select('form_code')->selectRaw('COUNT(*) as total')->groupBy('form_code')
                ->pluck('total', 'form_code')->all()
            : [];
        $pdfLinks = $this->episodePdfLinks($solicitud, $encounter, $procedures, $histories);
        if (in_array('admission', array_column($links, 'key'), true) && Route::has('formulario.001')
            && !empty($availability[ClinicalHospitalFormAvailabilityService::FORM_001])) {
            $pdfLinks['001'][] = ['url' => route('formulario.001', ['id' => $solicitud->id]), 'label' => null];
        }
        if (in_array('emergency', array_column($links, 'key'), true) && Route::has('hospital.nuevo.formulario008_pdf')) {
            $pdfLinks['008'][] = ['url' => route('hospital.nuevo.formulario008_pdf', ['id' => $solicitud->id]), 'label' => null];
        }
        $hasEmar022 = RequestSchema::hasTable('clinical_medication_administrations')
            && RequestSchema::hasColumns('clinical_medication_administrations', [
                'encounter_id', 'patient_id', 'status',
            ])
            && DB::table('clinical_medication_administrations')
                ->where('encounter_id', $encounter->id)
                ->where('patient_id', $solicitud->id_paciente)
                ->where('status', 'ADMINISTERED')
                ->exists();
        if ($hasEmar022 && Route::has('hospital.documents.form.pdf')) {
            $pdfLinks['022'][] = [
                'url' => route('hospital.documents.form.pdf', [
                    'code' => '022', 'id_solicitud' => $solicitud->id,
                ]),
                'label' => null,
                'status' => 'Datos eMAR disponibles',
                'preview_label' => 'Ver 022 actualizado',
                'official' => false,
            ];
        }
        $formCatalog = HospitalEpisodeFormCatalog::forEpisode($solicitud->id, array_column($links, 'key'),
            (int) $solicitud->estado_paso === 5 || count($cycles) > 0, $readOnly, $draftCounts, $pdfLinks,
            HospitalEpisodeFormCatalog::canAuthor(Auth::user(), '003'),
            array_values(array_filter(array_column(HospitalEpisodeFormCatalog::definitions(), 'code'), function ($code) {
                return HospitalEpisodeFormCatalog::canAuthor(Auth::user(), $code);
            })));
        $reportCatalog = HospitalEpisodeFormCatalog::availableReports($pdfLinks);
        return compact('solicitud', 'encounter', 'documents', 'procedures', 'cycles', 'pending', 'links', 'formCatalog', 'reportCatalog', 'readOnly', 'operational', 'role', 'consentFormats', 'catalog', 'doctors', 'surgicalReceipt', 'plannedProcedureIds', 'plannedDoctorId', 'plannedStart', 'plannedEnd');
    }

    private function episodePdfLinks(Ho_Solicitud $solicitud, ClinicalEncounter $encounter, $procedures, $historyIds = null)
    {
        $links = [];
        $historyQuery = Historiaclinica::where('encounter_id', $encounter->id)
            ->where('id_paciente', $solicitud->id_paciente);
        $historyIds = $historyIds === null ? (clone $historyQuery)->pluck('hcid') : collect($historyIds);
        $history = $solicitud->id_agenda
            ? (clone $historyQuery)->where('id_agenda', $solicitud->id_agenda)->first() : null;
        if (!$history) $history = $historyQuery->orderByDesc('hcid')->first();
        if ($historyIds->isNotEmpty() && Route::has('formulario.002') && RequestSchema::hasTable('anamnesis')) {
            foreach (Anamnesis::whereIn('hcid', $historyIds)->where('estado', 1)->orderByDesc('id')->get(['id']) as $anamnesis) {
                $links['002'][] = ['url' => route('formulario.002', ['id' => $anamnesis->id]), 'label' => '#'.$anamnesis->id];
            }
        }
        if ($history) {
            $procedure = $solicitud->id_hcproc
                ? hc_procedimientos::where('id', $solicitud->id_hcproc)->where('id_hc', $history->hcid)->first()
                : hc_procedimientos::where('id_hc', $history->hcid)->orderByDesc('id')->first();
            if (!$solicitud->id_hcproc || $procedure) {
                $evolutions = Hc_Evolucion::whereIn('hcid', $historyIds)
                    ->get(['motivo', 'cuadro_clinico']);
                if ($evolutions->contains(function ($evolution) {
                    return trim(strip_tags((string) $evolution->motivo)) !== ''
                        || trim(strip_tags((string) $evolution->cuadro_clinico)) !== '';
                }) && Route::has('hospital.formulario005_pdf')) {
                    $links['005'][] = ['url' => route('hospital.formulario005_pdf', ['id_solicitud' => $solicitud->id]), 'label' => null];
                }
            }
        }
        if ($history && (!$solicitud->id_hcproc || $procedure) && !isset($links['005'])
            && Route::has('hospital.formulario005_pdf')
            && RequestSchema::hasTable('clinical_emergency_care_entries')
            && DB::table('clinical_emergency_care_entries')
                ->where('solicitud_id', $solicitud->id)->where('section', 'evolution')
                ->whereNotNull('content')->where('content', '<>', '')->exists()) {
            $links['005'][] = ['url' => route('hospital.formulario005_pdf', ['id_solicitud' => $solicitud->id]), 'label' => null];
        }
        if (Route::has('epicrisis.pdf_final') && Hc_Epicrisis_Final::where('id_ho_solicitud', $solicitud->id)->exists()) {
            $links['006'][] = ['url' => route('epicrisis.pdf_final', ['id_solicitud' => $solicitud->id]), 'label' => null];
        }

        if (RequestSchema::hasTable('ho_ordenes') && RequestSchema::hasTable('interconsulta')) {
            $consultations = DB::table('ho_ordenes as episode_order')
                ->join('interconsulta as consultation', 'consultation.id', '=', 'episode_order.clave_tipo')
                ->join('historiaclinica as consultation_history', 'consultation_history.hcid', '=', 'consultation.id_hc')
                ->where('episode_order.id_ho_solicitud', $solicitud->id)
                ->where('episode_order.tipo', 'INTERCONSULTA')
                ->where('consultation.id_paciente', $solicitud->id_paciente)
                ->where('consultation_history.encounter_id', $encounter->id)
                ->where('consultation_history.id_paciente', $solicitud->id_paciente)
                ->where('consultation.estado', 1)
                ->distinct()->orderByDesc('consultation.id')
                ->get(['consultation.id']);
            foreach ($consultations as $consultation) {
                if (Route::has('formulario.007')) {
                    $links['007'][] = ['url' => route('formulario.007', ['id' => $consultation->id]), 'label' => '#'.$consultation->id];
                }
                if (Route::has('informe.007')) {
                    $links['007B'][] = ['url' => route('informe.007', ['id' => $consultation->id]), 'label' => '#'.$consultation->id];
                }
            }
        }

        foreach (app(HospitalEpisodeLaboratoryService::class)->links($solicitud, $encounter) as $code => $laboratoryLinks) {
            if ($laboratoryLinks) $links[$code] = $laboratoryLinks;
        }
        if ($historyIds->isNotEmpty() && Route::has('formulario.12A') && RequestSchema::hasTable('orden_012')) {
            $imagingIds = DB::table('orden_012 as imaging_order')
                ->join('hc_evolucion as evolution', 'evolution.id', '=', 'imaging_order.id_hc_evolucion')
                ->whereIn('evolution.hcid', $historyIds)
                ->where('imaging_order.id_paciente', $solicitud->id_paciente)
                ->where('imaging_order.estado', 1)
                ->orderByDesc('imaging_order.id')->pluck('imaging_order.id');
            foreach ($imagingIds as $imagingId) {
                $links['012A'][] = ['url' => route('formulario.12A', ['id' => $imagingId]), 'label' => '#'.$imagingId];
            }
        }

        $procedureIds = $procedures->pluck('id')->all();
        if (!$procedureIds) return $links;
        if (Route::has('formulario.12B')) {
            $imagingReports = DB::table('hc_protocolo as protocol')
                ->join('hc_procedimientos as procedure', 'procedure.id', '=', 'protocol.id_hc_procedimientos')
                ->whereIn('protocol.id_hc_procedimientos', $procedureIds)
                ->whereIn('protocol.hcid', $historyIds)
                ->where('protocol.tipo_procedimiento', 2)
                ->whereNotNull('procedure.id_doctor_examinador2')
                ->orderByDesc('protocol.id')->get(['protocol.id', 'protocol.hallazgos']);
            foreach ($imagingReports as $imagingReport) {
                if (trim(strip_tags((string) $imagingReport->hallazgos)) === '') continue;
                $links['012B'][] = ['url' => route('formulario.12B', ['id' => $imagingReport->id]),
                    'label' => '#'.$imagingReport->id];
            }
        }
        if (Route::has('formulario.013B') && RequestSchema::hasTable('hc_informe_biopsia')) {
            $biopsyReports = DB::table('hc_informe_biopsia')
                ->whereIn('id_hc_procedimiento', $procedureIds)
                ->whereIn('hcid', $historyIds)
                ->where('id_paciente', $solicitud->id_paciente)
                ->where('estado', 1)
                ->orderByDesc('id')->get(['id_hc_procedimiento', 'diagnostico', 'descripcion_microscopica']);
            foreach ($biopsyReports as $biopsyReport) {
                if (trim(strip_tags((string) ($biopsyReport->diagnostico ?: $biopsyReport->descripcion_microscopica))) === '') continue;
                $links['013B'][] = ['url' => route('formulario.013B', ['id' => $biopsyReport->id_hc_procedimiento]),
                    'label' => '#'.$biopsyReport->id_hc_procedimiento];
            }
        }
        if (Route::has('formulario.017') && RequestSchema::hasTable('hc_protocolo_cirugia')) {
            $protocols = DB::table('hc_protocolo as protocol')
                ->join('hc_protocolo_cirugia as surgery', 'surgery.id_hc_protocolo', '=', 'protocol.id')
                ->whereIn('protocol.id_hc_procedimientos', $procedureIds)
                ->whereIn('protocol.hcid', $historyIds)
                ->orderByDesc('protocol.id')->get(['protocol.id', 'protocol.id_hc_procedimientos', 'protocol.conclusion']);
            foreach ($protocols as $protocol) {
                if (trim(strip_tags((string) $protocol->conclusion)) === '') continue;
                $links['017'][] = ['url' => route('formulario.017', ['id' => $protocol->id]),
                    'label' => '#'.$protocol->id_hc_procedimientos];
            }
        }
        if (Route::has('formato.pdf_019') && RequestSchema::hasTable('hc_evolucion_anestesiologo')) {
            $recoveries = DB::table('hc_evolucion_anestesiologo')
                ->whereIn('hc_id_procedimiento', $procedureIds)
                ->whereIn('id_hc', $historyIds)
                ->where('estado', 1)
                ->orderByDesc('id')->get(['hc_id_procedimiento', 'evolucion']);
            foreach ($recoveries as $recovery) {
                if (trim(strip_tags((string) $recovery->evolucion)) === '') continue;
                $links['019'][] = ['url' => route('formato.pdf_019', ['id_historia' => $recovery->hc_id_procedimiento]),
                    'label' => '#'.$recovery->hc_id_procedimiento];
            }
        }
        $anesthesias = Hc_Anestesiologia::whereIn('id_hc_procedimientos', $procedureIds)->get();
        $preops = Rc_pre_anestesica::whereIn('id_hc_anestesiologia', $anesthesias->pluck('id'))
            ->where('estado', 1)->get()->groupBy('id_hc_anestesiologia');
        foreach ($anesthesias as $anesthesia) {
            $procedure = $procedures->firstWhere('id', $anesthesia->id_hc_procedimientos);
            if (!$procedure || (string) $anesthesia->id_hc !== (string) $procedure->id_hc) continue;
            $label = '#'.$procedure->id;
            if (Route::has('formulario.018')) {
                foreach ($preops->get($anesthesia->id, collect()) as $preop) {
                    $links['018'][] = ['url' => route('formulario.018', ['id' => $preop->id]), 'label' => $label];
                }
            }
            $anesthesiaDocumented = $anesthesia->id_tipoanestesiologia !== null
                || trim((string) $anesthesia->diagnostico_preoperatorio) !== ''
                || trim((string) $anesthesia->comentarios) !== '';
            if ($anesthesiaDocumented && Route::has('formulario.018A')) {
                $links['018A'][] = ['url' => route('formulario.018A', ['id' => $anesthesia->id]), 'label' => $label];
            }
        }
        return $links;
    }

    public function externalLink($id, $key, $procedureId = null)
    {
        $solicitud = $this->obtain($id);
        $encounter = ClinicalEncounter::find($solicitud->encounter_id);
        $editable = in_array($key, ['preop', 'protocol'], true);
        if (in_array($key, ['evolution', 'interconsultation'], true)) {
            // La evolución y las interconsultas solo se abren mientras el episodio admite documentación.
            $this->requireTrue(self::writable($solicitud, $encounter, true), 'El episodio es de solo lectura.');
        } else {
            $this->requireTrue(!$editable || self::writable($solicitud, $encounter), 'El episodio es de solo lectura.');
        }
        if ($key === 'admission') {
            $this->requireTrue($solicitud->id_agenda, 'Admision no registrada.');
            $route = 'formulario.001'; $parameters = ['id' => $solicitud->id];
        } elseif ($key === 'emergency') {
            $this->requireTrue(app(ClinicalHospitalFormAvailabilityService::class)
                ->isAvailable($solicitud->id, ClinicalHospitalFormAvailabilityService::FORM_008),
                'Formulario 008 sin información clínica suficiente.');
            $this->requireTrue(Ho_Log_Solicitud::where('id_ho_solicitud', $solicitud->id)
                ->where('estado_paso', 2)->whereNotNull('id_agenda')->exists(),
                'El ingreso administrativo del Formulario 008 está incompleto.');
            $route = 'hospital.nuevo.formulario008_pdf'; $parameters = ['id' => $solicitud->id];
        } elseif ($key === 'epicrisis') {
            $route = 'epicrisis.mostrar_final'; $parameters = ['id_solicitud' => $solicitud->id];
        } elseif ($key === 'evolution') {
            $route = 'formulario005.index_005'; $parameters = ['id_solicitud' => $solicitud->id];
        } elseif ($key === 'interconsultation') {
            $route = 'decimo.interconsulta'; $parameters = ['id' => $solicitud->id];
        } elseif (in_array($key, ['preop', 'protocol', 'protocol_pdf', 'anesthesia_pdf'], true)) {
            $this->binding($solicitud, $procedureId, $editable);
            $procedure = $this->procedure($solicitud, $procedureId);
            if ($key === 'preop') {
                $this->source($solicitud, $procedureId, 'preop');
                $route = 'formato.rc_preanestesico_edit'; $parameters = ['id' => $this->anesthesia($procedure)->id];
            } elseif ($key === 'protocol') {
                $protocol = $this->source($solicitud, $procedureId, 'protocol');
                $this->requireTrue($protocol, 'El protocolo quirurgico aun no esta creado. Prepare el procedimiento e ingrese la informacion antes de abrirlo.');
                // Despite its route placeholder "ag", mostrar() expects hc_procedimientos.id.
                $route = 'protocolo.mostrar'; $parameters = ['ag' => $procedureId];
            } elseif ($key === 'protocol_pdf') {
                $protocol = $this->source($solicitud, $procedureId, 'protocol');
                $this->requireTrue($protocol, 'El protocolo quirurgico aun no esta creado. Abra Protocolo y registre la informacion antes de generar el PDF.');
                $this->requireTrue(trim((string) $protocol->conclusion) !== '', 'El protocolo solo contiene un marcador inicial.');
                $route = 'hc_protocolo.imprime'; $parameters = ['id' => $protocol->id];
            } else {
                $preop = $this->source($solicitud, $procedureId, 'preop');
                $route = 'formulario.018'; $parameters = ['id' => $preop->id];
            }
        } else throw new \InvalidArgumentException('Enlace documental no permitido.');
        $this->requireTrue(Route::has($route), 'El formulario no tiene una ruta disponible.');
        return route($route, $parameters);
    }

    public function readDocument($id, $documentId)
    {
        $solicitud = $this->obtain($id);
        $document = ClinicalDocument::with('amendments')->where('encounter_id', $solicitud->encounter_id)
            ->where('id', $documentId)->firstOrFail();
        $fields = self::effectiveFields($document);
        return compact('solicitud', 'document', 'fields');
    }

    private function checkpoint($binding, $type)
    {
        return ClinicalEncounterEvent::where('encounter_id', $binding->encounter_id)->where('event_type', $type)
            ->where('from_value', (string) $binding->id)->first();
    }

    private function event($solicitud, $type, $from, $to, $reason)
    {
        return ClinicalEncounterEvent::create(['encounter_id' => $solicitud->encounter_id, 'event_type' => $type,
            'from_value' => (string) $from, 'to_value' => (string) $to, 'reason' => $reason,
            'actor_id' => Auth::id(), 'ip_address' => request()->ip()]);
    }

    private function one($rows, $required = true)
    {
        $this->requireTrue($rows->count() <= 1 && (!$required || $rows->count() === 1),
            'Registro ausente o ambiguo. No se selecciona el primero por historia; concilie el procedimiento.');
        return $rows->first();
    }

    private function latestProtocol($procedureId)
    {
        return hc_protocolo::where('id_hc_procedimientos', $procedureId)
            ->orderByDesc('id')
            ->first();
    }

    private function requireTrue($condition, $message)
    {
        if (!$condition) throw new \InvalidArgumentException($message);
    }

    private function context()
    {
        return ['actor_id' => Auth::id(), 'ip' => request()->ip(), 'hospital_document' => true];
    }

    private function audit()
    {
        return ['id_usuariocrea' => Auth::id(), 'id_usuariomod' => Auth::id(),
            'ip_creacion' => request()->ip(), 'ip_modificacion' => request()->ip()];
    }
}
