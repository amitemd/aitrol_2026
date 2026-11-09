<?php

namespace Sis_medico\Services;

class HospitalEpisodeForm024
{
    const GENERAL = 'GENERAL_INHALATORIA';
    const SPINAL = 'RAQUIDEA';
    const OTHER = 'OTRO';

    public static function types()
    {
        return [
            self::GENERAL => 'Anestesia general / inhalatoria',
            self::SPINAL => 'Anestesia raquídea',
            self::OTHER => 'Otro procedimiento',
        ];
    }

    public static function presets()
    {
        return [
            self::GENERAL => [
                'procedimiento' => 'ANESTESIA GENERAL',
                'consiste' => 'Administración de medicamentos anestésicos para producir pérdida temporal y controlada de la conciencia, sensibilidad y reflejos durante el procedimiento indicado.',
                'realiza' => 'El anestesiólogo administra medicamentos por vía intravenosa y/o inhalatoria, controla la vía aérea y mantiene vigilancia continua de la respiración, circulación y demás funciones vitales.',
                'beneficios' => 'Mejor control hemodinámico y ventilatorio del paciente.',
                'riesgos_frecuentes' => 'Hipotensión, erupción o reacción alérgica, bradicardia, broncoespasmo y lesiones de piezas dentales.',
                'riesgos_graves' => 'Hipoxia, dificultad para la intubación, paro cardíaco y tromboembolia pulmonar.',
                'alternativas' => 'Anestesia general balanceada, inhalatoria o total intravenosa, según la valoración anestésica.',
                'manejo_posterior' => 'Analgesia, vigilancia y cuidados postquirúrgicos.',
                'consecuencias' => 'Progresión del cuadro clínico o imposibilidad de realizar el procedimiento propuesto.',
            ],
            self::SPINAL => [
                'procedimiento' => 'ANESTESIA RAQUÍDEA',
                'consiste' => 'Aplicación de un anestésico local en la región lumbar para bloquear temporalmente la sensibilidad y el movimiento de la parte inferior del cuerpo.',
                'realiza' => 'Con el paciente sentado o acostado de lado, se desinfecta la zona lumbar y se introduce una aguja fina hasta el espacio correspondiente para administrar el anestésico. Se vigilan continuamente las funciones vitales.',
                'beneficios' => 'Mantener la conciencia del paciente, con pocos efectos sobre el sistema cardiovascular, respiratorio y otros sistemas importantes.',
                'riesgos_frecuentes' => 'Posibles náuseas, mareos, cansancio, picazón, retención urinaria, dolor lumbar o cefalea.',
                'riesgos_graves' => 'Convulsiones, problemas respiratorios o cardíacos, caída importante de la presión arterial, infección, sangrado o lesión neurológica excepcional.',
                'alternativas' => 'Anestesia regional, anestesia local o anestesia general, según la valoración anestésica y el procedimiento.',
                'manejo_posterior' => 'Analgesia, vigilancia y cuidados postquirúrgicos.',
                'consecuencias' => 'Progresión del cuadro clínico o imposibilidad de realizar el procedimiento propuesto.',
            ],
            self::OTHER => [
                'procedimiento' => '', 'consiste' => '', 'realiza' => '', 'beneficios' => '',
                'riesgos_frecuentes' => '', 'riesgos_graves' => '', 'alternativas' => '',
                'manejo_posterior' => '', 'consecuencias' => '',
            ],
        ];
    }

    public static function forDisplay(array $payload)
    {
        $type = strtoupper(trim((string) ($payload['tipo_consentimiento'] ?? '')));
        if (!array_key_exists($type, self::types())) $type = self::GENERAL;

        $document = array_fill_keys([
            'tipo_consentimiento', 'fecha', 'hora', 'tipo_atencion', 'diagnostico', 'cie10',
            'procedimiento', 'consiste', 'realiza', 'duracion_estimada', 'beneficios',
            'riesgos_frecuentes', 'riesgos_graves', 'riesgos_especificos', 'alternativas',
            'manejo_posterior', 'consecuencias', 'observaciones',
        ], '');
        $document['tipo_consentimiento'] = $type;

        $legacy = [
            'riesgos_frecuentes' => 'riesgos',
            'riesgos_especificos' => 'observaciones',
        ];
        foreach ($document as $key => $value) {
            if ($key === 'tipo_consentimiento') continue;
            $document[$key] = trim((string) ($payload[$key] ?? ''));
            if ($document[$key] === '' && isset($legacy[$key])) {
                $document[$key] = trim((string) ($payload[$legacy[$key]] ?? ''));
            }
        }

        foreach (self::presets()[$type] as $key => $value) {
            if ($document[$key] === '') $document[$key] = $value;
        }
        if ($document['fecha'] === '') $document['fecha'] = date('Y-m-d');
        if ($document['hora'] === '') $document['hora'] = date('H:i');

        return $document;
    }

    public static function normalizePayload(array $payload)
    {
        $type = strtoupper(trim((string) ($payload['tipo_consentimiento'] ?? '')));
        if (!array_key_exists($type, self::types())) {
            throw new \InvalidArgumentException('Seleccione un tipo válido de consentimiento anestésico.');
        }

        $document = self::forDisplay($payload);
        foreach ($document as $key => $value) $document[$key] = trim((string) $value);
        return $document;
    }

    public static function hasData(array $payload)
    {
        foreach ($payload as $key => $value) {
            if ($key !== 'tipo_consentimiento' && trim((string) $value) !== '') return true;
        }
        return false;
    }

    public static function imageFor($type)
    {
        return $type === self::SPINAL
            ? 'assets/hospital/form024/anestesia-raquidea.png'
            : 'assets/hospital/form024/anestesia-general-inhalatoria.png';
    }
}
