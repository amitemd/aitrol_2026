<?php

namespace Sis_medico\Services;

use DateTime;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use RuntimeException;

class LaboratoryExamCatalogSeederService
{
    const EXPECTED_GROUPERS = 22;
    const EXPECTED_EXAMS = 886;

    /**
     * Load the verified laboratory catalogue only when both destination tables
     * are empty. Existing installations are deliberately left untouched.
     *
     * @return array
     */
    public function seed($actorId, $ip = '127.0.0.1')
    {
        $this->validateDestination($actorId);

        return DB::transaction(function () use ($actorId, $ip) {
            $existingGroupers = DB::table('examen_agrupador')->count();
            $existingExams = DB::table('examen')->count();

            if ($existingGroupers > 0 || $existingExams > 0) {
                return array(
                    'seeded' => false,
                    'groupers' => $existingGroupers,
                    'exams' => $existingExams,
                );
            }

            $groupers = $this->readCsv($this->dataPath('examen_agrupador.csv'));
            $exams = $this->readCsv($this->dataPath('examenes.csv'));
            $this->validateSource($groupers, $exams);

            $grouperColumns = array_flip(Schema::getColumnListing('examen_agrupador'));
            $examColumns = array_flip(Schema::getColumnListing('examen'));

            $groupers = array_map(function ($row) use ($actorId, $ip, $grouperColumns) {
                $row = $this->normalizeRow($row);
                $row['id_usuariocrea'] = $actorId;
                $row['id_usuariomod'] = $actorId;
                $row['ip_creacion'] = $ip;
                $row['ip_modificacion'] = $ip;

                return array_intersect_key($row, $grouperColumns);
            }, $groupers);

            $exams = array_map(function ($row) use ($actorId, $ip, $examColumns) {
                $row = $this->normalizeRow($row);
                $row['id_usuariocrea'] = $actorId;
                $row['id_usuariomod'] = $actorId;
                $row['ip_creacion'] = $ip;
                $row['ip_modificacion'] = $ip;

                return array_intersect_key($row, $examColumns);
            }, $exams);

            foreach (array_chunk($groupers, 100) as $chunk) {
                DB::table('examen_agrupador')->insert($chunk);
            }
            foreach (array_chunk($exams, 100) as $chunk) {
                DB::table('examen')->insert($chunk);
            }

            return array(
                'seeded' => true,
                'groupers' => count($groupers),
                'exams' => count($exams),
            );
        });
    }

    private function validateDestination($actorId)
    {
        foreach (array('users', 'examen_agrupador', 'examen') as $table) {
            if (!Schema::hasTable($table)) {
                throw new RuntimeException('No existe la tabla requerida para el catálogo de laboratorio: '.$table);
            }
        }

        if (!$actorId || !DB::table('users')->where('id', $actorId)->exists()) {
            throw new RuntimeException('El usuario de auditoría del catálogo de laboratorio no existe.');
        }
    }

    private function validateSource(array $groupers, array $exams)
    {
        if (count($groupers) !== self::EXPECTED_GROUPERS) {
            throw new RuntimeException('El catálogo inicial debe contener '.self::EXPECTED_GROUPERS.' agrupadores.');
        }
        if (count($exams) !== self::EXPECTED_EXAMS) {
            throw new RuntimeException('El catálogo inicial debe contener '.self::EXPECTED_EXAMS.' exámenes.');
        }

        $grouperIds = array();
        foreach ($groupers as $row) {
            $this->requireFields($row, array('id', 'nombre', 'estado'));
            if (isset($grouperIds[$row['id']])) {
                throw new RuntimeException('El catálogo contiene un agrupador duplicado: '.$row['id']);
            }
            $grouperIds[$row['id']] = true;
        }

        $examIds = array();
        foreach ($exams as $row) {
            $this->requireFields($row, array(
                'id', 'nombre', 'descripcion', 'valor', 'id_agrupador', 'tarifario', 'estado',
            ), array('tarifario'));
            if (isset($examIds[$row['id']])) {
                throw new RuntimeException('El catálogo contiene un examen duplicado: '.$row['id']);
            }
            if (!isset($grouperIds[$row['id_agrupador']])) {
                throw new RuntimeException('El examen '.$row['id'].' referencia un agrupador inexistente.');
            }
            $examIds[$row['id']] = true;
        }
    }

    private function requireFields(array $row, array $fields, array $allowEmpty = array())
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $row)
                || ($row[$field] === '' && !in_array($field, $allowEmpty, true))
                || strcasecmp((string) $row[$field], 'null') === 0) {
                throw new RuntimeException('Falta el campo requerido '.$field.' en el catálogo de laboratorio.');
            }
        }
    }

    private function normalizeRow(array $row)
    {
        $integerColumns = array(
            'id', 'id_gye', 'id_agrupador', 'publico_privado', 'especial_publico',
            'sexo_n_s', 'estado', 'maquina', 'tiene_detalle', 'no_resultado',
            'no_orden_pub', 'secuencia', 'humanlabs', 'tiempo_dias', 'synlab',
            'illinworth', 'cantidad_tubos', 'id_labs_tubo', 'tipo_muestra',
            'tipo_especial',
        );
        $decimalColumns = array(
            'valor', 'valor_reactivo', 'valor_implementos', 'valor_interlab', 'valor_referido',
        );
        $dateColumns = array('created_at', 'updated_at');

        foreach ($row as $column => $value) {
            if (strcasecmp(trim((string) $value), 'null') === 0) {
                $row[$column] = null;
                continue;
            }
            if (in_array($column, $integerColumns, true) && $value !== '') {
                $normalized = trim($value);
                if (!preg_match('/^-?\d+$/', $normalized)) {
                    throw new RuntimeException('Valor entero inválido en '.$column.': '.$value);
                }
                $row[$column] = (int) $normalized;
                continue;
            }
            if (in_array($column, $decimalColumns, true) && $value !== '') {
                $normalized = str_replace(',', '.', trim($value));
                if (!is_numeric($normalized)) {
                    throw new RuntimeException('Valor numérico inválido en '.$column.': '.$value);
                }
                $row[$column] = $normalized;
                continue;
            }
            if (in_array($column, $dateColumns, true) && $value !== '') {
                $row[$column] = $this->normalizeDate($value, $column);
            }
        }

        return $row;
    }

    private function normalizeDate($value, $column)
    {
        $date = DateTime::createFromFormat('!j/n/Y G:i:s', trim($value));
        $errors = DateTime::getLastErrors();
        if (!$date || (is_array($errors) && ($errors['warning_count'] || $errors['error_count']))) {
            throw new RuntimeException('Fecha inválida en '.$column.': '.$value);
        }

        return $date->format('Y-m-d H:i:s');
    }

    private function readCsv($path)
    {
        if (!is_file($path)) {
            throw new RuntimeException('No se encontró el catálogo de laboratorio: '.$path);
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException('No se pudo abrir el catálogo de laboratorio: '.$path);
        }

        try {
            $headers = fgetcsv($handle, 0, ',', '"', '\\');
            if (!is_array($headers) || !$headers) {
                throw new RuntimeException('El catálogo de laboratorio no contiene encabezados: '.$path);
            }
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
            if (count($headers) !== count(array_unique($headers))) {
                throw new RuntimeException('El catálogo de laboratorio contiene encabezados duplicados: '.$path);
            }

            $rows = array();
            $line = 1;
            while (($values = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                $line++;
                if ($values === array(null) || $values === array()) {
                    continue;
                }
                if (count($values) !== count($headers)) {
                    throw new RuntimeException('Número de columnas inválido en '.$path.', línea '.$line.'.');
                }
                $rows[] = array_combine($headers, $values);
            }

            return $rows;
        } finally {
            fclose($handle);
        }
    }

    private function dataPath($file)
    {
        return database_path('data/laboratory/'.$file);
    }
}
