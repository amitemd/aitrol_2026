<?php

namespace Sis_medico\v3\contable;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class PlanCuentasImportService
{
    const MAX_FILE_SIZE = 5242880;
    const MAX_ROWS = 5000;

    public static function importFile(Request $request)
    {
        if (!$request->hasFile('archivo_plan')) {
            throw new Exception('Seleccione un archivo para importar.');
        }

        $file = $request->file('archivo_plan');

        if (!$file->isValid()) {
            throw new Exception('El archivo no pudo cargarse correctamente.');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new Exception('El archivo supera el tamaño máximo permitido de 5 MB.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, array('xlsx', 'csv'))) {
            throw new Exception('Formato no permitido. Utilice un archivo XLSX o CSV.');
        }

        $rows = self::readRows($file->getRealPath(), $extension);
        return self::persistRows($rows, $request);
    }

    private static function readRows($path, $extension)
    {
        $matrix = $extension === 'csv' ? self::readCsv($path) : self::readXlsx($path);
        $headerRow = null;
        $columns = array();

        foreach (array_slice($matrix, 0, 15, true) as $row => $cells) {
            $candidate = array();
            foreach ($cells as $column => $value) {
                $header = self::normalizeHeader($value);
                if ($header !== '') {
                    $candidate[$header] = $column;
                }
            }

            $codeColumn = self::findColumn($candidate, array('codigo', 'codigocontable', 'plan', 'cuenta'));
            $nameColumn = self::findColumn($candidate, array('nombre', 'descripcion', 'nombrecuenta'));
            if ($codeColumn !== null && $nameColumn !== null) {
                $headerRow = $row;
                $columns = array(
                    'codigo' => $codeColumn,
                    'nombre' => $nameColumn,
                    'tipo' => self::findColumn($candidate, array('tipo', 'tipocuenta')),
                    'naturaleza' => self::findColumn($candidate, array('naturaleza')),
                    'calcula_en' => self::findColumn($candidate, array('calculaen', 'naturaleza2', 'debehaber')),
                    'cierre_anio' => self::findColumn($candidate, array('cierreanio', 'cierredeano', 'estadocierreano')),
                );
                break;
            }
        }

        if ($headerRow === null) {
            throw new Exception('No se encontraron las columnas obligatorias CÓDIGO y NOMBRE.');
        }

        $result = array();
        $seen = array();
        $errors = array();

        foreach ($matrix as $row => $cells) {
            if ($row <= $headerRow) {
                continue;
            }
            $rawCode = self::matrixValue($cells, $columns['codigo']);
            $rawName = self::matrixValue($cells, $columns['nombre']);

            if (trim((string) $rawCode) === '' && trim((string) $rawName) === '') {
                continue;
            }

            $code = self::normalizeCode($rawCode);
            $name = trim((string) $rawName);

            if ($code === '' || !preg_match('/^\d+(?:\.\d+)*$/', $code)) {
                $errors[] = 'Fila ' . $row . ': el código contable no es válido.';
                continue;
            }
            if ($name === '') {
                $errors[] = 'Fila ' . $row . ': el nombre de la cuenta es obligatorio.';
                continue;
            }
            if (strlen($name) > 255) {
                $errors[] = 'Fila ' . $row . ': el nombre supera los 255 caracteres.';
                continue;
            }
            if (isset($seen[$code])) {
                $errors[] = 'Fila ' . $row . ': el código ' . $code . ' está duplicado en el archivo.';
                continue;
            }

            $seen[$code] = true;
            $result[] = array(
                'fila' => $row,
                'codigo' => $code,
                'nombre' => strtoupper($name),
                'tipo' => self::optionalEnum($cells, $columns['tipo'], $row, array('grupo' => 1, '1' => 1, 'detalle' => 2, '2' => 2), 'tipo', $errors),
                'naturaleza' => self::optionalEnum($cells, $columns['naturaleza'], $row, array('positivo' => 1, '1' => 1, 'negativo' => 0, '0' => 0), 'naturaleza', $errors),
                'naturaleza_2' => self::optionalEnum($cells, $columns['calcula_en'], $row, array('debe' => 0, 'cuentadebe' => 0, '0' => 0, 'haber' => 1, 'cuentahaber' => 1, '1' => 1), 'cálculo', $errors),
                'estado_cierre_ano' => self::optionalEnum($cells, $columns['cierre_anio'], $row, array('si' => 1, '1' => 1, 'no' => 0, '0' => 0), 'cierre de año', $errors),
            );

            if (count($result) > self::MAX_ROWS) {
                throw new Exception('El archivo supera el máximo permitido de ' . self::MAX_ROWS . ' cuentas.');
            }
        }

        if (empty($result)) {
            $errors[] = 'El archivo no contiene cuentas para importar.';
        }

        if (!empty($errors)) {
            throw new Exception(implode("\n", array_slice($errors, 0, 15)));
        }

        usort($result, function ($left, $right) {
            $depth = substr_count($left['codigo'], '.') - substr_count($right['codigo'], '.');
            return $depth === 0 ? strnatcasecmp($left['codigo'], $right['codigo']) : $depth;
        });

        return $result;
    }

    private static function readCsv($path)
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new Exception('No fue posible leer el archivo CSV.');
        }
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = self::detectDelimiter($firstLine);
        $rows = array();
        $rowNumber = 1;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            foreach ($data as $index => $value) {
                if (!mb_check_encoding($value, 'UTF-8')) {
                    $data[$index] = mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
                }
                if ($rowNumber === 1 && $index === 0) {
                    $data[$index] = preg_replace('/^\xEF\xBB\xBF/', '', $data[$index]);
                }
            }
            $rows[$rowNumber] = $data;
            $rowNumber++;
        }
        fclose($handle);
        return $rows;
    }

    private static function readXlsx($path)
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new Exception('El archivo XLSX está dañado o no se puede abrir.');
        }

        $sharedStrings = array();
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            if (strlen($sharedXml) > 20971520 || stripos($sharedXml, '<!DOCTYPE') !== false) {
                $zip->close();
                throw new Exception('El archivo XLSX contiene datos no permitidos.');
            }
            $shared = simplexml_load_string($sharedXml, 'SimpleXMLElement', LIBXML_NONET);
            if ($shared === false) {
                $zip->close();
                throw new Exception('No se pudieron leer los textos del archivo XLSX.');
            }
            $shared->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            foreach ($shared->xpath('//x:si') as $item) {
                $item->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $parts = $item->xpath('.//x:t');
                $text = '';
                foreach ($parts as $part) {
                    $text .= (string) $part;
                }
                $sharedStrings[] = $text;
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetXml === false) {
            throw new Exception('El archivo XLSX no contiene una hoja de cálculo válida.');
        }
        if (strlen($sheetXml) > 20971520 || stripos($sheetXml, '<!DOCTYPE') !== false) {
            throw new Exception('La hoja de cálculo supera el tamaño permitido o contiene datos no válidos.');
        }

        $sheet = simplexml_load_string($sheetXml, 'SimpleXMLElement', LIBXML_NONET);
        if ($sheet === false) {
            throw new Exception('No se pudo interpretar la hoja del archivo XLSX.');
        }
        $sheet->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rows = array();

        foreach ($sheet->xpath('//x:sheetData/x:row') as $rowNode) {
            $rowNumber = (int) $rowNode['r'];
            $rows[$rowNumber] = array();
            foreach ($rowNode->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->c as $cell) {
                $attributes = $cell->attributes();
                $reference = (string) $attributes['r'];
                $column = self::columnIndex($reference);
                $type = (string) $attributes['t'];
                $value = '';
                $cell->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $cellChildren = $cell->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                if ($type === 'inlineStr') {
                    $parts = $cell->xpath('.//x:t');
                    foreach ($parts as $part) {
                        $value .= (string) $part;
                    }
                } else {
                    $value = isset($cellChildren->v) ? (string) $cellChildren->v : '';
                    if ($type === 's' && isset($sharedStrings[(int) $value])) {
                        $value = $sharedStrings[(int) $value];
                    }
                }
                $rows[$rowNumber][$column] = $value;
            }
        }
        return $rows;
    }

    private static function persistRows(array $rows, Request $request)
    {
        $companyId = session('id_empresa');
        $userId = Auth::user()->id;
        $ip = Utilidades::getRealIP();
        $now = date('Y-m-d H:i:s');
        $existingCompany = array();
        $existingMaster = array();
        $importedCodes = array();
        $errors = array();

        foreach (PlanCuentas::where('id_empresa', $companyId)->get() as $account) {
            $existingCompany[$account->id_plan] = $account;
        }
        foreach (Plan_Cuentas::get(array('id')) as $account) {
            $existingMaster[$account->id] = true;
        }
        foreach ($rows as $row) {
            $importedCodes[$row['codigo']] = true;
            $parent = self::parentCode($row['codigo']);
            if ($parent !== null && !isset($existingCompany[$parent]) && !isset($importedCodes[$parent])) {
                $errors[] = 'Fila ' . $row['fila'] . ': no existe la cuenta padre ' . $parent . ' para ' . $row['codigo'] . '.';
            }
        }
        if (!empty($errors)) {
            throw new Exception(implode("\n", array_slice($errors, 0, 15)));
        }

        $created = 0;
        $updated = 0;
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $code = $row['codigo'];
                $parent = self::parentCode($code);
                $current = isset($existingCompany[$code]) ? $existingCompany[$code] : null;
                $type = $row['tipo'];

                if ($type === null && $current && in_array((int) $current->estado, array(1, 2))) {
                    $type = (int) $current->estado;
                }
                if ($type === null) {
                    $type = self::hasImportedChildren($code, $importedCodes) ? 1 : 2;
                }

                $nature = $row['naturaleza'] === null ? ($current ? (int) $current->naturaleza : 1) : $row['naturaleza'];
                $calculation = $row['naturaleza_2'] === null ? ($current ? (int) $current->naturaleza_2 : 0) : $row['naturaleza_2'];
                $yearClose = $row['estado_cierre_ano'] === null ? ($current ? (int) $current->estado_cierre_ano : 0) : $row['estado_cierre_ano'];

                if (!isset($existingMaster[$code])) {
                    DB::table('plan_cuentas')->insert(array(
                        'id' => $code,
                        'nombre' => $row['nombre'],
                        'descripcion' => $row['nombre'],
                        'estado' => $type,
                        'id_padre' => $parent,
                        'id_usuariocrea' => $userId,
                        'id_usuariomod' => $userId,
                        'ip_creacion' => $ip,
                        'ip_modificacion' => $ip,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'naturaleza' => $nature,
                        'naturaleza_2' => $calculation,
                    ));
                    $existingMaster[$code] = true;
                }

                $companyData = array(
                    'nombre' => $row['nombre'],
                    'descripcion' => $row['nombre'],
                    'estado' => $type,
                    'id_padre' => $parent,
                    'id_usuariomod' => $userId,
                    'ip_modificacion' => $ip,
                    'updated_at' => $now,
                    'naturaleza' => $nature,
                    'naturaleza_2' => $calculation,
                    'plan' => $code,
                    'estado_cierre_ano' => $yearClose,
                );

                if ($current) {
                    DB::table('plan_cuentas_empresa')->where('id', $current->id)->update($companyData);
                    $updated++;
                } else {
                    $companyData['id_empresa'] = $companyId;
                    $companyData['id_plan'] = $code;
                    $companyData['id_usuariocrea'] = $userId;
                    $companyData['ip_creacion'] = $ip;
                    $companyData['created_at'] = $now;
                    DB::table('plan_cuentas_empresa')->insert($companyData);
                    $created++;
                }
            }

            $request->c = 'contabilidad';
            $request->s = 'importarPlan';
            $request->m = 'Plan_Cuentas';
            $request->v = 'view_plan_cuenta_tree';
            $request->d = $request->input('d', '|');
            $request->o = 'Importación de plan de cuentas: ' . $created . ' creadas y ' . $updated . ' actualizadas.';
            Auditorias::saveAuditoria($request);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return array('creadas' => $created, 'actualizadas' => $updated, 'total' => count($rows));
    }

    public static function outputTemplate()
    {
        $headers = array('CÓDIGO', 'NOMBRE', 'TIPO', 'NATURALEZA', 'CALCULA_EN', 'CIERRE_ANIO');
        $examples = array(
            array('1', 'ACTIVO', 'GRUPO', 'POSITIVO', 'DEBE', 'NO'),
            array('1.01', 'ACTIVO CORRIENTE', 'GRUPO', 'POSITIVO', 'DEBE', 'NO'),
            array('1.01.01', 'EFECTIVO Y EQUIVALENTES', 'GRUPO', 'POSITIVO', 'DEBE', 'NO'),
            array('1.01.01.01', 'CAJA GENERAL', 'DETALLE', 'POSITIVO', 'DEBE', 'NO'),
        );
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename="PLANTILLA_PLAN_DE_CUENTAS.csv"');
        header('Cache-Control: max-age=0');
        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers, ';');
        foreach ($examples as $example) {
            fputcsv($output, $example, ';');
        }
        fclose($output);
        exit;
    }

    private static function findColumn(array $candidate, array $aliases)
    {
        foreach ($aliases as $alias) {
            if (array_key_exists($alias, $candidate)) {
                return $candidate[$alias];
            }
        }
        return null;
    }

    private static function matrixValue(array $cells, $column)
    {
        return $column === null || !array_key_exists($column, $cells) ? '' : $cells[$column];
    }

    private static function detectDelimiter($line)
    {
        $counts = array(
            ';' => substr_count((string) $line, ';'),
            ',' => substr_count((string) $line, ','),
            "\t" => substr_count((string) $line, "\t"),
        );
        arsort($counts);
        return key($counts);
    }

    private static function columnIndex($reference)
    {
        preg_match('/^[A-Z]+/i', $reference, $matches);
        $letters = strtoupper(isset($matches[0]) ? $matches[0] : 'A');
        $index = 0;
        for ($position = 0; $position < strlen($letters); $position++) {
            $index = ($index * 26) + (ord($letters[$position]) - 64);
        }
        return $index - 1;
    }

    private static function normalizeHeader($value)
    {
        $value = strtolower(trim((string) $value));
        $value = strtr($value, array('á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n'));
        return preg_replace('/[^a-z0-9]/', '', $value);
    }

    private static function normalizeCode($value)
    {
        $value = trim((string) $value);
        $value = ltrim($value, "'\"");
        $value = str_replace(array(',', ' '), array('.', ''), $value);
        return trim($value, '.');
    }

    private static function optionalEnum(array $cells, $column, $row, array $values, $field, array &$errors)
    {
        if ($column === null) {
            return null;
        }
        $raw = trim((string) self::matrixValue($cells, $column));
        if ($raw === '') {
            return null;
        }
        $key = self::normalizeHeader($raw);
        if (!array_key_exists($key, $values)) {
            $errors[] = 'Fila ' . $row . ': el valor de ' . $field . ' no es válido.';
            return null;
        }
        return $values[$key];
    }

    private static function parentCode($code)
    {
        $position = strrpos($code, '.');
        return $position === false ? null : substr($code, 0, $position);
    }

    private static function hasImportedChildren($code, array $codes)
    {
        $prefix = $code . '.';
        foreach ($codes as $candidate => $unused) {
            if (strpos($candidate, $prefix) === 0) {
                return true;
            }
        }
        return false;
    }
}
