<?php

namespace Sis_medico\Services;

class Form008PatientAntecedents
{
    /**
     * Build the numbered antecedent list used by section E of Form 008.
     *
     * The general patient screen stores some fields in paciente and the
     * remaining fields in ho_datos_paciente, so both sources are required.
     */
    public static function build($patient, $patientData, $allergies)
    {
        return [
            1 => self::allergies($patient, $allergies),
            2 => self::combine([
                self::value($patient, 'antecedentes_pat'),
                self::value($patientData, 'clinico'),
            ]),
            3 => self::text(self::value($patientData, 'ginecologico')),
            4 => self::text(self::value($patientData, 'traumatologico')),
            5 => self::text(self::value($patientData, 'antecedentes_pediatricos')),
            6 => self::text(self::value($patient, 'antecedentes_quir')),
            7 => self::text(self::value($patientData, 'farmacologico')),
            8 => self::combine([
                self::value($patientData, 'habitos'),
                self::value($patient, 'alcohol'),
            ]),
            9 => self::text(self::value($patient, 'antecedentes_fam')),
            10 => self::text(self::value($patientData, 'otros_antecedente')),
        ];
    }

    public static function hasAny(array $antecedents)
    {
        foreach ($antecedents as $antecedent) {
            if ($antecedent !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Format the detail rows with the same grouping used by the official
     * Formulario 008: personales, quirúrgicos, alérgicos y familiares.
     */
    public static function summary(array $antecedents)
    {
        $personal = self::combine([
            isset($antecedents[2]) ? $antecedents[2] : '',
            isset($antecedents[3]) ? $antecedents[3] : '',
            isset($antecedents[4]) ? $antecedents[4] : '',
            isset($antecedents[5]) ? $antecedents[5] : '',
            isset($antecedents[7]) ? $antecedents[7] : '',
            isset($antecedents[8]) ? $antecedents[8] : '',
            isset($antecedents[10]) ? $antecedents[10] : '',
        ]);

        return [
            'ANTECEDENTES PATOLÓGICOS PERSONALES' => self::reported($personal),
            'ANTECEDENTES PATOLÓGICOS QUIRÚRGICOS' => self::reported(isset($antecedents[6]) ? $antecedents[6] : ''),
            'ANTECEDENTES ALÉRGICOS' => self::reported(isset($antecedents[1]) ? $antecedents[1] : ''),
            'ANTECEDENTES PATOLÓGICOS FAMILIARES' => self::reported(isset($antecedents[9]) ? $antecedents[9] : ''),
        ];
    }

    public static function personalRows(array $antecedents)
    {
        $labels = [
            2 => 'Clínicos',
            3 => 'Ginecológicos',
            4 => 'Traumatológicos',
            5 => 'Pediátricos',
            7 => 'Farmacológicos',
            8 => 'Hábitos',
            10 => 'Otros',
        ];
        $rows = [];

        foreach ($labels as $number => $label) {
            $description = isset($antecedents[$number]) ? self::text($antecedents[$number]) : '';
            if ($description === '') {
                continue;
            }

            $rows[] = [
                'number' => $number,
                'label' => $label,
                'description' => $description,
            ];
        }

        return $rows;
    }

    /**
     * Return every non-empty antecedent captured by Form 008. The
     * preanesthetic assessment uses this complete list as read-only context
     * and still allows the anesthesiologist to add complementary records.
     */
    public static function rows(array $antecedents)
    {
        $labels = [
            1 => 'Alérgicos',
            2 => 'Clínicos',
            3 => 'Ginecológicos',
            4 => 'Traumatológicos',
            5 => 'Pediátricos',
            6 => 'Quirúrgicos',
            7 => 'Farmacológicos',
            8 => 'Hábitos',
            9 => 'Familiares',
            10 => 'Otros',
        ];
        $rows = [];

        foreach ($labels as $number => $label) {
            $description = isset($antecedents[$number]) ? self::text($antecedents[$number]) : '';
            if ($description === '') {
                continue;
            }

            $rows[] = [
                'number' => $number,
                'label' => $label,
                'description' => $description,
            ];
        }

        return $rows;
    }

    private static function allergies($patient, $allergies)
    {
        $names = [];

        if (is_iterable($allergies)) {
            foreach ($allergies as $allergy) {
                $activeIngredient = self::value($allergy, 'principio_activo');
                $name = self::text(self::value($activeIngredient, 'nombre'));
                if ($name === '') {
                    $name = self::text(self::value($allergy, 'nombre'));
                }
                if ($name !== '') {
                    $names[] = $name;
                }
            }
        }

        if (count($names) === 0) {
            $names[] = self::value($patient, 'alergias');
        }

        return self::combine($names);
    }

    private static function combine(array $values)
    {
        $unique = [];

        foreach ($values as $value) {
            $text = self::text($value);
            if ($text === '') {
                continue;
            }

            $key = function_exists('mb_strtolower')
                ? mb_strtolower($text, 'UTF-8')
                : strtolower($text);

            if (! isset($unique[$key])) {
                $unique[$key] = $text;
            }
        }

        return implode('; ', array_values($unique));
    }

    private static function reported($value)
    {
        $value = self::text($value);

        return $value === '' ? 'NO REFIERE' : $value;
    }

    private static function text($value)
    {
        if (! is_scalar($value)) {
            return '';
        }

        return trim((string) preg_replace('/\s+/u', ' ', (string) $value));
    }

    private static function value($source, $property)
    {
        if (is_array($source)) {
            return isset($source[$property]) ? $source[$property] : null;
        }

        if (is_object($source)) {
            return isset($source->{$property}) ? $source->{$property} : null;
        }

        return null;
    }
}
