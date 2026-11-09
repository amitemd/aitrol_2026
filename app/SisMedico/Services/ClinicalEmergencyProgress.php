<?php

namespace Sis_medico\Services;

class ClinicalEmergencyProgress
{
    public static function labels()
    {
        return ['nombre1'=>'Primer nombre','nombre2'=>'Segundo nombre','apellido1'=>'Primer apellido','apellido2'=>'Segundo apellido',
            'sexo'=>'Sexo','fecha_nacimiento'=>'Nacimiento','telefono1'=>'Teléfono','telefono2'=>'Teléfono alternativo',
            'direccion'=>'Dirección','mail_opcional'=>'Correo','clinical_reason'=>'Motivo de consulta','priority_id'=>'Prioridad propuesta',
            'manchester_flowchart'=>'Diagrama Manchester','discriminator'=>'Discriminador','glasgow_note'=>'Observación Glasgow',
            'eye_response'=>'Glasgow ocular','verbal_response'=>'Glasgow verbal','motor_response'=>'Glasgow motor',
            'systolic'=>'P. sistólica (mmHg)','diastolic'=>'P. diastólica (mmHg)','heart_rate'=>'Frec. cardíaca (lpm)',
            'respiratory_rate'=>'Frec. respiratoria (rpm)','temperature'=>'Temperatura (°C)','oxygen_saturation'=>'Saturación O₂ (%)',
            'pain_score'=>'Dolor referido','high_risk'=>'Signo de alto riesgo','isolation'=>'Requiere aislamiento',
            'reported_document'=>'Documento comunicado','information_source'=>'Informante',
            'progress_note'=>'Información y pendientes','weight'=>'Peso (kg)','height'=>'Talla (cm)',
            'capillary_refill'=>'Llenado capilar','pupillary_response'=>'Respuesta pupilar'];
    }

    public static function draft($latest, $triageId)
    {
        if (!$latest) return [];
        if ($latest->stage === 'DRAFT' && (string)$latest->triage_assessment_id === (string)$triageId) return $latest->clinical_data;
        // Reported identity persists; past observations must not look like a new examination.
        return array_intersect_key($latest->clinical_data, array_flip(['reported_document']));
    }

    public static function clinicalRules()
    {
        $rules = (new ClinicalEmergencyFlowService)->triageRules();
        // A draft can be incomplete but cannot contain malformed measurements.
        unset($rules['classification_confirmed']);
        foreach ($rules as $field => $rule) {
            $parts = array_filter(explode('|', $rule), function ($part) {
                return !str_starts_with($part, 'required') && !str_starts_with($part, 'accepted');
            });
            if (in_array($field, ['clinical_reason','discriminator','manchester_flowchart','glasgow_note'], true)) {
                $parts = array_filter($parts, function ($part) { return !str_starts_with($part, 'min:'); });
            }
            $rules[$field] = 'nullable|'.implode('|', $parts);
        }
        $rules['reported_document'] = 'nullable|string|max:30';
        $rules['information_source'] = 'nullable|in:PATIENT,FAMILY,STAFF,UNAVAILABLE';
        $rules['progress_note'] = 'nullable|string|max:2000';
        return $rules;
    }

    public static function patientRules()
    {
        return ['nombre1'=>'nullable|string|max:60', 'nombre2'=>'nullable|string|max:60',
            'apellido1'=>'nullable|string|max:60', 'apellido2'=>'nullable|string|max:60',
            'sexo'=>'nullable|in:0,1,2', 'fecha_nacimiento'=>'nullable|date_format:Y-m-d|before_or_equal:today',
            'telefono1'=>'nullable|string|max:60', 'telefono2'=>'nullable|string|max:60',
            'direccion'=>'nullable|string|max:255', 'mail_opcional'=>'nullable|email|max:191'];
    }

    public static function nonEmpty(array $data)
    {
        return array_filter($data, function ($value) { return $value !== null && $value !== ''; });
    }

    public static function merge(array $previous, array $incoming)
    {
        // Empty controls mean "not provided", never "erase another professional's data".
        return array_replace($previous, self::nonEmpty($incoming));
    }

    public static function autosaveFieldAllowed($field)
    {
        if (!is_string($field)) return false;
        if (preg_match('/^patient\[([a-z0-9_]+)\]$/D', $field, $match)) {
            return array_key_exists($match[1], self::patientRules());
        }
        return $field !== 'classification_method' && $field !== 'classification_confirmed'
            && array_key_exists($field, self::clinicalRules());
    }

    public static function clearablePatientField($field)
    {
        return in_array($field, ['nombre2','apellido2','telefono1','telefono2','direccion','mail_opcional'], true);
    }
}
