<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\ClinicalDocument;
use Sis_medico\ClinicalDocumentCompleteness;
use Sis_medico\ClinicalDeathCase;
use Sis_medico\Exceptions\ClinicalDocumentIncompleteException;
use Sis_medico\Hc_Epicrisis_Final;
use Sis_medico\Historiaclinica;
use Sis_medico\Ho_Form008;
use Sis_medico\hc_protocolo;

class ClinicalDocumentCompletenessService
{
    public function available()
    {
        return Schema::hasTable('clinical_documents') && Schema::hasTable('clinical_document_completeness');
    }

    public function assess(ClinicalDocument $document, array $fields, array $context = array())
    {
        if (!$this->available()) return null;
        if ($document->document_type === 'EPICRISIS' && !isset($context['closure_type'])) {
            $defuncion = isset($fields['defuncion']) ? strtoupper(trim((string) $fields['defuncion'])) : '';
            $context['closure_type'] = $defuncion !== '' && !in_array($defuncion, array('NO','0','N/A'), true) ? 'DECEASED' : 'DISCHARGE';
        }
        $required = $this->requirements($document->document_type, $context);
        $missing = array();
        foreach ($required as $field => $label) {
            if (!$this->filled(isset($fields[$field]) ? $fields[$field] : null)) $missing[] = $label;
        }
        $total = count($required);
        $percent = $total === 0 ? 100 : (int) round((($total - count($missing)) / $total) * 100);
        return ClinicalDocumentCompleteness::updateOrCreate(
            array('clinical_document_id' => $document->id),
            array(
                'encounter_id' => $document->encounter_id,
                'document_type' => $document->document_type,
                'status' => count($missing) ? 'INCOMPLETE' : 'COMPLETE',
                'required_fields' => json_encode(array_values($required)),
                'missing_fields' => json_encode($missing),
                'completion_percent' => $percent,
                'assessed_at' => date('Y-m-d H:i:s'),
                'assessed_by' => $this->actor($context),
                'ip_address' => $this->ip($context),
            )
        );
    }

    public function reassessDocument(ClinicalDocument $document, array $context = array())
    {
        return $this->assess($document, $this->sourceFields($document), $context);
    }

    public function assertComplete(ClinicalDocument $document, array $context = array())
    {
        $assessment = $this->reassessDocument($document, $context);
        if (!$assessment) throw new ClinicalDocumentIncompleteException(array('evaluación de completitud no disponible'));
        if ($assessment->status !== 'COMPLETE') throw new ClinicalDocumentIncompleteException($assessment->missingFields());
        return $assessment;
    }

    public function summary($encounterId)
    {
        if (!$this->available()) return array();
        $documents = ClinicalDocument::where('encounter_id', $encounterId)
            ->where('status', '<>', 'PLACEHOLDER')
            ->orderBy('authored_at')->get();
        $summary = array();
        foreach ($documents as $document) {
            $assessment = $this->reassessDocument($document);
            $summary[] = array(
                'document' => $document,
                'status' => $assessment ? $assessment->status : 'UNAVAILABLE',
                'percent' => $assessment ? $assessment->completion_percent : 0,
                'missing' => $assessment ? $assessment->missingFields() : array('Evaluación no disponible'),
            );
        }
        return $summary;
    }

    public function backfill()
    {
        if (!$this->available()) return 0;
        $count = 0;
        ClinicalDocument::orderBy('id')->chunk(200, function ($documents) use (&$count) {
            foreach ($documents as $document) {
                $this->reassessDocument($document);
                $count++;
            }
        });
        return $count;
    }

    public function integrity()
    {
        if (!$this->available()) return array('available' => false, 'pending' => null);
        $withoutAssessment = DB::table('clinical_documents as d')
            ->leftJoin('clinical_document_completeness as c', 'c.clinical_document_id', '=', 'd.id')
            ->whereNull('c.id')->count();
        $signedWithoutAssessment = DB::table('clinical_documents as d')
            ->leftJoin('clinical_document_completeness as c', 'c.clinical_document_id', '=', 'd.id')
            ->whereNotNull('d.signed_at')->whereNull('c.id')->count();
        $signedIncomplete = DB::table('clinical_documents as d')
            ->join('clinical_document_completeness as c', 'c.clinical_document_id', '=', 'd.id')
            ->whereNotNull('d.signed_at')->where('c.status', 'INCOMPLETE')->count();
        $draftIncomplete = ClinicalDocumentCompleteness::where('status', 'INCOMPLETE')->whereHas('document', function ($query) {
            $query->whereNull('signed_at');
        })->count();
        $finalizedIncomplete=DB::table('clinical_document_completeness as c')->join('clinical_documents as d','d.id','=','c.clinical_document_id')
            ->join('clinical_encounters as e','e.id','=','d.encounter_id')->where('c.status','INCOMPLETE')
            ->whereNull('d.signed_at')->where('e.status','<>','ACTIVE')->count();
        $supported=array('ENCOUNTER_RECORD','EMERGENCY_FORM_008','EPICRISIS','PROCEDURE_REPORT','DEATH_RECORD',
            'HOSPITAL_PREANESTHETIC','HOSPITAL_POSTANESTHETIC');
        $unsupported=ClinicalDocument::whereNotIn('document_type',$supported)->count();
        $corrupt=DB::table('clinical_document_completeness as c')->join('clinical_documents as d','d.id','=','c.clinical_document_id')
            ->where(function($q){
                $q->whereRaw('c.encounter_id <> d.encounter_id')->orWhereRaw('c.document_type <> d.document_type')
                    ->orWhere(function($complete){$complete->where('c.status','COMPLETE')->where('c.completion_percent','<>',100);})
                    ->orWhere(function($invalid){$invalid->whereNotIn('c.status',array('COMPLETE','INCOMPLETE'));});
            })->count();
        return array(
            'available' => true,
            'pending' => $withoutAssessment + $signedIncomplete + $finalizedIncomplete + $unsupported + $corrupt,
            'documents_without_assessment' => $withoutAssessment,
            'signed_without_assessment' => $signedWithoutAssessment,
            'signed_incomplete' => $signedIncomplete,
            'draft_incomplete' => $draftIncomplete,
            'finalized_incomplete' => $finalizedIncomplete,
            'unsupported_document_types' => $unsupported,
            'corrupt_assessments' => $corrupt,
        );
    }

    private function requirements($type, array $context = array())
    {
        $schemas = array(
            'ENCOUNTER_RECORD' => array('id_agenda' => 'agenda', 'id_paciente' => 'paciente', 'id_doctor1' => 'profesional responsable'),
            'EMERGENCY_FORM_008' => array('id_solicitud' => 'solicitud', 'id_agenda' => 'agenda', 'encounter_id' => 'episodio clínico',
                'motivo' => 'motivo de atención', 'condicion' => 'condición de llegada', 'id_usuariocrea' => 'responsable del registro'),
            'EPICRISIS' => array('cuadro_clinico' => 'resumen del cuadro clínico',
                'ep_resumen_evolucion' => 'resumen de evolución', 'complicacion' => 'complicaciones',
                'hallazgo' => 'hallazgos relevantes', 'resumen' => 'tratamiento y procedimientos terapéuticos',
                'conclusion' => 'conclusión', 'condicion' => 'condición de egreso', 'pronostico' => 'pronóstico'),
            'PROCEDURE_REPORT' => array('hallazgos' => 'hallazgos', 'conclusion' => 'conclusión', 'hora_inicio' => 'hora de inicio', 'hora_fin' => 'hora de finalización', 'estado_final' => 'estado final del paciente'),
            'HOSPITAL_PREANESTHETIC' => array('_hospital_binding_id' => 'ciclo quirurgico', '_hospital_procedure_id' => 'procedimiento',
                'estado_fisico' => 'valoracion', 'riesgo_quirurgico' => 'riesgo registrado', 'plan_anestesico' => 'plan anestesico'),
            'HOSPITAL_POSTANESTHETIC' => array_merge(array('_hospital_binding_id' => 'ciclo quirurgico',
                '_hospital_procedure_id' => 'procedimiento'), HospitalDocumentService::recoveryFields()),
            'DEATH_RECORD' => array('occurred_at' => 'fecha y hora de defunción', 'pronounced_by' => 'profesional que constató',
                'pronounced_at' => 'fecha y hora de constatación', 'death_location' => 'lugar de defunción',
                'probable_cause' => 'causa probable', 'death_manner' => 'manera de muerte', 'certificate_number' => 'certificado',
                'family_contact_name' => 'contacto familiar', 'family_notified_at' => 'notificación familiar',
                'family_notified_by' => 'responsable de notificación familiar', 'morgue_notified_at' => 'notificación a morgue',
                'morgue_notified_by' => 'responsable de notificación a morgue', 'completed_by' => 'responsable del cierre'),
        );
        $required = isset($schemas[$type]) ? $schemas[$type] : array('__document_schema' => 'esquema de validación para '.$type);
        if ($type === 'EPICRISIS' && isset($context['closure_type']) && $context['closure_type'] === 'DISCHARGE') {
            $required['indicaciones_alta'] = 'indicaciones de alta';
            $required['alta'] = 'tipo de alta';
        }
        if ($type === 'EPICRISIS' && isset($context['closure_type']) && $context['closure_type'] === 'DECEASED') {
            $required['defuncion'] = 'condición de defunción';
        }
        return $required;
    }

    private function sourceFields(ClinicalDocument $document)
    {
        if (in_array($document->source_type, array(HospitalDocumentService::PREOP, HospitalDocumentService::RECOVERY), true)) {
            return HospitalDocumentService::effectiveFields($document);
        }
        if ($document->source_type === 'historiaclinica') {
            $source = Historiaclinica::find($document->source_id);
            return $source ? $source->toArray() : array();
        }
        if ($document->source_type === 'hc_epicrisis_final') {
            $source = Hc_Epicrisis_Final::find($document->source_id);
            return $source ? $source->toArray() : array();
        }
        if ($document->source_type === 'ho_form008') {
            $source = Ho_Form008::find($document->source_id);
            return $source ? $source->toArray() : array();
        }
        if ($document->source_type === 'hc_protocolo') {
            $source = hc_protocolo::find($document->source_id);
            return $source ? $source->toArray() : array();
        }
        if ($document->source_type === 'clinical_death_case') {
            $source = ClinicalDeathCase::find($document->source_id);
            return $source ? $source->toArray() : array();
        }
        return array();
    }

    private function filled($value)
    {
        if (is_bool($value)) return $value;
        if (is_numeric($value)) return true;
        $text = trim(str_replace('&nbsp;', ' ', strip_tags((string) $value)));
        return $text !== '' && !in_array(strtoupper($text), array('SELECCIONE', 'PENDIENTE DE COMPLETAR'), true);
    }

    private function actor(array $context)
    {
        return isset($context['actor_id']) ? $context['actor_id'] : (Auth::check() ? Auth::user()->id : null);
    }

    private function ip(array $context)
    {
        return isset($context['ip']) ? $context['ip'] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
    }
}
