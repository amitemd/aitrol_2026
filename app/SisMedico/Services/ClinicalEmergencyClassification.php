<?php

namespace Sis_medico\Services;

/** Records a clinician's Manchester decision; does not implement the MTS flowcharts. */
class ClinicalEmergencyClassification
{
    public static function levels()
    {
        return [
            1 => ['name' => 'Rojo · Inmediato', 'minutes' => 0, 'color' => '#b91c1c'],
            2 => ['name' => 'Naranja · Muy urgente', 'minutes' => 10, 'color' => '#b45309'],
            3 => ['name' => 'Amarillo · Urgente', 'minutes' => 60, 'color' => '#856000'],
            4 => ['name' => 'Verde · Estándar', 'minutes' => 120, 'color' => '#15803d'],
            5 => ['name' => 'Azul · No urgente', 'minutes' => 240, 'color' => '#1d4ed8'],
        ];
    }

    public static function components()
    {
        return [
            'eye_response' => ['label' => 'Apertura ocular', 'code' => 'E', 'max' => 4, 'options' => [4 => 'Espontánea', 3 => 'Al sonido', 2 => 'A la presión', 1 => 'Ninguna']],
            'verbal_response' => ['label' => 'Respuesta verbal', 'code' => 'V', 'max' => 5, 'options' => [5 => 'Orientada', 4 => 'Confusa', 3 => 'Palabras', 2 => 'Sonidos', 1 => 'Ninguna']],
            'motor_response' => ['label' => 'Respuesta motora', 'code' => 'M', 'max' => 6, 'options' => [6 => 'Obedece órdenes', 5 => 'Localiza', 4 => 'Flexión normal', 3 => 'Flexión anormal', 2 => 'Extensión', 1 => 'Ninguna']],
        ];
    }

    public static function glasgow($eye, $verbal, $motor)
    {
        $values = [$eye, $verbal, $motor];
        foreach ([4, 5, 6] as $index => $max) {
            if (!preg_match('/^[1-6]$/D', (string) $values[$index]) || (int) $values[$index] > $max) return null;
        }
        return array_sum(array_map('intval', $values));
    }

    public static function numericComponent($value)
    {
        return $value === 'NT' || $value === '' || $value === null ? null : (int) $value;
    }

    public static function details(array $data)
    {
        $components = [];
        foreach (self::components() as $key => $definition) {
            $value = $data[$key] ?? null;
            $components[$key] = $value === 'NT' ? 'NT' : self::numericComponent($value);
        }
        return $components + [
            'glasgow_total' => self::glasgow($components['eye_response'], $components['verbal_response'], $components['motor_response']),
            'glasgow_note' => $data['glasgow_note'] ?? null,
            'method' => $data['classification_method'] ?? 'LEGACY_CLINICIAN',
            'manchester_flowchart' => $data['manchester_flowchart'] ?? null,
            'confirmed' => !empty($data['classification_confirmed']),
        ];
    }

    public static function rules()
    {
        $rules = [
            'classification_method' => 'nullable|in:CLINICIAN_MANCHESTER_V1',
            'classification_confirmed' => 'required_if:classification_method,CLINICIAN_MANCHESTER_V1|nullable|accepted_if:classification_method,CLINICIAN_MANCHESTER_V1',
            'manchester_flowchart' => 'required_if:classification_method,CLINICIAN_MANCHESTER_V1|nullable|string|min:3|max:191',
            'discriminator' => 'required_if:classification_method,CLINICIAN_MANCHESTER_V1|nullable|string|min:3|max:191',
            'glasgow_note' => 'required_if:eye_response,NT|required_if:verbal_response,NT|required_if:motor_response,NT|nullable|string|min:3|max:500',
        ];
        foreach (self::components() as $key => $definition) {
            $rules[$key] = 'nullable|in:'.implode(',', array_keys($definition['options'])).',NT';
        }
        return $rules;
    }
}
