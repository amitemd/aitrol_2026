<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use RuntimeException;
use Sis_medico\User;

class InitialLoadWorkbookService
{
    private $reader;

    public function __construct(SimpleXlsxReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Analiza todo el libro sin modificar la base de datos.
     */
    public function analyze($path)
    {
        $book = $this->reader->read($path);
        $definitions = $this->definitions();
        $prepared = array();
        $summary = array();
        $errors = array();
        $warnings = array();

        foreach ($definitions as $sheetName => $definition) {
            $sheet = array(
                'sheet' => $sheetName,
                'label' => $definition['label'],
                'destination' => $definition['destination'],
                'mode' => $definition['mode'],
                'total' => 0,
                'ready' => 0,
                'skipped' => 0,
                'manual' => 0,
                'errors' => array(),
                'warnings' => array(),
            );

            if (!isset($book[$sheetName])) {
                $sheet['warnings'][] = 'La hoja no esta incluida en este archivo y sera omitida.';
                $prepared[$sheetName] = array();
                $summary[$sheetName] = $sheet;
                continue;
            }

            try {
                $parsed = $this->extractRows($book[$sheetName], $definition['headers'], $sheetName);
                $prepared[$sheetName] = $parsed;
                $sheet['total'] = count($parsed);
                if (!$sheet['total']) $sheet['warnings'][] = 'Sin datos para importar; la hoja sera omitida.';
            } catch (RuntimeException $exception) {
                $sheet['errors'][] = $exception->getMessage();
                $summary[$sheetName] = $sheet;
                continue;
            }

            if ($definition['mode'] !== 'automatic') {
                $sheet['manual'] = count($prepared[$sheetName]);
                if ($sheet['manual']) {
                    $message = $definition['mode'] === 'files'
                        ? 'Estas filas requieren adjuntar y revisar los archivos fisicos indicados; no se guardaran automaticamente.'
                        : 'Estas filas requieren revision del tarifario antes de guardarse; no se importaran automaticamente.';
                    $sheet['warnings'][] = $message;
                }
                $summary[$sheetName] = $sheet;
                continue;
            }

            $summary[$sheetName] = $sheet;
        }

        $this->validatePreparedRows($prepared, $summary);
        $this->validateCrossSheetUsers($prepared, $summary);

        foreach ($summary as $sheet) {
            foreach ($sheet['errors'] as $message) $errors[] = $sheet['sheet'] . ': ' . $message;
            foreach ($sheet['warnings'] as $message) $warnings[] = $sheet['sheet'] . ': ' . $message;
        }

        $readyRows = array_sum(array_map(function ($sheet) { return (int) $sheet['ready']; }, $summary));
        $skippedRows = array_sum(array_map(function ($sheet) { return (int) $sheet['skipped']; }, $summary));
        $manualRows = array_sum(array_map(function ($sheet) { return (int) $sheet['manual']; }, $summary));
        if (!$readyRows && !$errors) {
            $errors[] = 'No se encontraron filas nuevas para importar. Conserva la fila amarilla de ejemplo y escribe los datos a partir de la fila siguiente.';
        }

        return array(
            'ready' => count($errors) === 0,
            'ready_rows' => $readyRows,
            'skipped_rows' => $skippedRows,
            'manual_rows' => $manualRows,
            'summary' => array_values($summary),
            'errors' => $errors,
            'warnings' => $warnings,
            'prepared' => $prepared,
        );
    }

    /**
     * Ejecuta solamente las hojas automaticas y devuelve el resultado por hoja.
     * El controlador administra la transaccion global.
     */
    public function execute($path, $actorId, $ip)
    {
        $analysis = $this->analyze($path);
        if (!$analysis['ready']) throw new RuntimeException('El archivo ya no supera la validacion previa. Vuelve a cargarlo.');

        $order = array(
            '16_Empresa', '04_Especialidades', '05_Doctores', '11_Usuarios_Roles',
            '06_Horarios_Doctor', '01_Salas', '02_Procedimientos', '03_Medicinas',
            '12_Productos', '15_Seguros', '18_Bancos', '19_Formas_Pago',
            '20_CIE10_CIE11', '21_Examenes_Lab',
        );
        $results = array();
        foreach ($order as $sheetName) {
            $method = 'import' . preg_replace('/[^A-Za-z0-9]/', '', $sheetName);
            if (!method_exists($this, $method)) continue;
            $results[$sheetName] = $this->{$method}(
                isset($analysis['prepared'][$sheetName]) ? $analysis['prepared'][$sheetName] : array(),
                $actorId,
                $ip
            );
        }
        return $results;
    }

    public function definitions()
    {
        return array(
            '01_Salas' => $this->definition('Salas', 'sala', array('hospital_ruc', 'nombre_sala', 'tipo_atencion', 'estado')),
            '02_Procedimientos' => $this->definition('Procedimientos', 'procedimiento', array('nombre', 'observacion', 'grupo', 'cardiologia', 'laboratorio', 'quirofano', 'estado')),
            '03_Medicinas' => $this->definition('Medicinas / Vademecum', 'medicina', array('nombre_comercial', 'nombre_generico', 'codigo_atc', 'concentracion', 'presentacion', 'laboratorio', 'precio_unitario', 'indicaciones', 'contraindicaciones', 'estado')),
            '04_Especialidades' => $this->definition('Especialidades medicas', 'especialidad', array('nombre', 'descripcion', 'estado')),
            '05_Doctores' => $this->definition('Doctores', 'users + user_espe', array('cedula', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'email', 'fecha_nacimiento', 'registro_doctor', 'especialidades', 'telefono', 'direccion', 'estado')),
            '06_Horarios_Doctor' => $this->definition('Horarios de doctores', 'horario_doctor', array('cedula_doctor', 'dia_semana', 'hora_inicio', 'hora_fin', 'tipo', 'estado')),
            '08_Logos' => $this->definition('Logos del centro medico', 'empresa + archivos', array('ruc_empresa', 'tipo_logo', 'nombre_archivo', 'formato', 'observacion'), 'files'),
            '09_Formatos_Informes' => $this->definition('Formatos de informes', 'plantillas + archivos', array('especialidad', 'nombre_formato', 'tipo_documento', 'nombre_archivo', 'campos_variables', 'estado'), 'files'),
            '11_Usuarios_Roles' => $this->definition('Usuarios y roles', 'users', array('cedula', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'email', 'fecha_nacimiento', 'rol', 'ocupacion', 'estado')),
            '12_Productos' => $this->definition('Productos para facturacion', 'producto', array('codigo', 'nombre', 'descripcion', 'unidad_medida', 'precio_compra', 'precio_venta', 'iva', 'registro_sanitario', 'farmacia', 'estado')),
            '13_Valores_Servicios' => $this->definition('Valores de servicios', 'tarifarios', array('tipo_servicio', 'servicio', 'nivel', 'seguro', 'empresa_ruc', 'valor', 'vigente_desde', 'estado'), 'review'),
            '14_Plantillas_Medicas' => $this->definition('Plantillas medicas', 'plantillas + archivos', array('especialidad', 'procedimiento', 'nombre_plantilla', 'nombre_archivo', 'contenido_o_campos', 'estado'), 'files'),
            '15_Seguros' => $this->definition('Seguros medicos', 'seguros', array('nombre', 'descripcion', 'tipo_seguro', 'color_hex', 'ruc_cliente', 'email', 'copago', 'visible', 'estado')),
            '16_Empresa' => $this->definition('Informacion de empresa', 'empresa', array('ruc', 'razon_social', 'nombre_comercial', 'ciudad', 'direccion', 'email', 'telefono_1', 'telefono_2', 'establecimiento', 'punto_emision', 'obligado_contabilidad', 'estado')),
            '18_Bancos' => $this->definition('Bancos', 'ct_bancos', array('nombre', 'estado')),
            '19_Formas_Pago' => $this->definition('Formas de pago', 'ct_tipo_pago', array('codigo', 'descripcion', 'requiere_banco', 'requiere_referencia', 'estado')),
            '20_CIE10_CIE11' => $this->definition('Diagnosticos CIE', 'cie_10_3 / cie_10_4', array('version', 'codigo', 'descripcion', 'codigo_padre', 'especialidades', 'estado')),
            '21_Examenes_Lab' => $this->definition('Examenes de laboratorio', 'examen', array('codigo', 'nombre', 'descripcion', 'agrupador', 'valor', 'tarifario', 'valor_reactivo', 'valor_implementos', 'tipo_muestra', 'estado')),
        );
    }

    private function definition($label, $destination, array $headers, $mode = 'automatic')
    {
        return compact('label', 'destination', 'headers', 'mode');
    }

    private function extractRows(array $rows, array $requiredHeaders, $sheetName)
    {
        $headerRow = null;
        $columnIndexes = array();
        foreach ($rows as $rowNumber => $values) {
            if ($rowNumber > 20) break;
            $normalized = array_map(array($this, 'normalizeHeader'), $values);
            if (count(array_intersect($requiredHeaders, $normalized)) >= min(3, count($requiredHeaders))) {
                $headerRow = $rowNumber;
                foreach ($requiredHeaders as $header) {
                    $index = array_search($header, $normalized, true);
                    if ($index !== false) $columnIndexes[$header] = $index;
                }
                break;
            }
        }
        if ($headerRow === null) throw new RuntimeException('No se encontro la fila de encabezados.');
        $missing = array_diff($requiredHeaders, array_keys($columnIndexes));
        if ($missing) throw new RuntimeException('Faltan columnas: ' . implode(', ', $missing) . '.');

        $result = array();
        foreach ($rows as $rowNumber => $values) {
            if ($rowNumber <= $headerRow + 1) continue; // la fila inmediata es el ejemplo amarillo
            $marker = array_map(array($this, 'normalizeHeader'), array_slice($values, 0, 3));
            if (isset($marker[0], $marker[1], $marker[2]) && $marker[0] === 'campo' && $marker[1] === 'tipo' && $marker[2] === 'obligatorio') break;
            $data = array('_row' => $rowNumber);
            foreach ($columnIndexes as $field => $column) {
                $data[$field] = isset($values[$column]) ? $this->clean($values[$column]) : '';
            }
            if (!$this->rowIsEmpty($data)) $result[] = $data;
        }
        return $result;
    }

    private function rowIsEmpty(array $data)
    {
        foreach ($data as $field => $value) {
            if ($field !== '_row' && $value !== '') return false;
        }
        return true;
    }

    private function normalizeHeader($value)
    {
        $value = mb_strtolower($this->clean($value), 'UTF-8');
        $value = str_replace(array(' ', '-'), '_', $value);
        return preg_replace('/[^a-z0-9_]/', '', $this->withoutAccents($value));
    }

    private function clean($value)
    {
        $value = preg_replace('/\x{00A0}/u', ' ', (string) $value);
        return trim($value);
    }

    private function key($value)
    {
        return mb_strtoupper($this->withoutAccents($this->clean($value)), 'UTF-8');
    }

    private function withoutAccents($value)
    {
        return strtr($value, array(
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n',
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N',
        ));
    }

    private function validatePreparedRows(array $prepared, array &$summary)
    {
        $definitions = $this->definitions();
        foreach ($definitions as $sheetName => $definition) {
            if ($definition['mode'] !== 'automatic' || !isset($prepared[$sheetName], $summary[$sheetName])) continue;
            $seen = array();
            $secondarySeen = array();
            foreach ($prepared[$sheetName] as $row) {
                $rowErrors = $this->validateRow($sheetName, $row, $prepared);
                foreach ($this->rowWarnings($sheetName, $row, $prepared) as $warning) {
                    $summary[$sheetName]['warnings'][] = 'Fila ' . $row['_row'] . ': ' . $warning . '.';
                }
                $uniqueKey = $this->rowUniqueKey($sheetName, $row);
                if ($uniqueKey !== '' && isset($seen[$uniqueKey])) {
                    $rowErrors[] = 'el registro esta repetido en el archivo (primera aparicion en fila ' . $seen[$uniqueKey] . ')';
                } elseif ($uniqueKey !== '') {
                    $seen[$uniqueKey] = $row['_row'];
                }
                $secondaryKey = $this->secondaryUniqueKey($sheetName, $row);
                if ($secondaryKey !== '' && isset($secondarySeen[$secondaryKey])) {
                    $rowErrors[] = 'un dato que debe ser unico esta repetido en el archivo (primera aparicion en fila ' . $secondarySeen[$secondaryKey] . ')';
                } elseif ($secondaryKey !== '') {
                    $secondarySeen[$secondaryKey] = $row['_row'];
                }

                if ($sheetName === '20_CIE10_CIE11' && $this->key($row['version']) === 'CIE11') {
                    $summary[$sheetName]['manual']++;
                    $summary[$sheetName]['warnings'][] = 'Fila ' . $row['_row'] . ': CIE-11 no tiene un catalogo de destino habilitado y requiere revision manual.';
                    continue;
                }

                if ($rowErrors) {
                    $summary[$sheetName]['errors'][] = 'Fila ' . $row['_row'] . ': ' . implode('; ', $rowErrors) . '.';
                    continue;
                }
                if ($this->rowExists($sheetName, $row)) {
                    $summary[$sheetName]['skipped']++;
                    continue;
                }
                $summary[$sheetName]['ready']++;
            }
            if ($sheetName === '20_CIE10_CIE11') {
                foreach ($prepared[$sheetName] as $row) {
                    if ($this->key($row['version']) === 'CIE10' && $row['especialidades'] !== '') {
                        $summary[$sheetName]['warnings'][] = 'Las especialidades de CIE-10 se validan como referencia, pero el esquema actual no dispone de una tabla para vincularlas al diagnostico.';
                        break;
                    }
                }
            }
        }
    }

    private function validateCrossSheetUsers(array $prepared, array &$summary)
    {
        $doctors = array();
        $emails = array();
        foreach (isset($prepared['05_Doctores']) ? $prepared['05_Doctores'] : array() as $row) {
            if ($row['cedula'] !== '') $doctors[$row['cedula']] = $row['_row'];
            if ($row['email'] !== '') $emails[$this->key($row['email'])] = array('sheet'=>'05_Doctores', 'row'=>$row['_row'], 'cedula'=>$row['cedula']);
        }
        foreach (isset($prepared['11_Usuarios_Roles']) ? $prepared['11_Usuarios_Roles'] : array() as $row) {
            if ($row['cedula'] !== '' && isset($doctors[$row['cedula']])) {
                $message = 'La cedula ' . $row['cedula'] . ' tambien aparece en 05_Doctores (fila ' . $doctors[$row['cedula']] . '). Debe existir en una sola hoja para evitar asignar dos roles.';
                $summary['05_Doctores']['errors'][] = $message;
                $summary['11_Usuarios_Roles']['errors'][] = 'Fila ' . $row['_row'] . ': ' . $message;
            }
            $emailKey = $this->key($row['email']);
            if ($emailKey !== '' && isset($emails[$emailKey]) && $emails[$emailKey]['cedula'] !== $row['cedula']) {
                $summary['11_Usuarios_Roles']['errors'][] = 'Fila ' . $row['_row'] . ': el email tambien aparece en ' . $emails[$emailKey]['sheet'] . ' (fila ' . $emails[$emailKey]['row'] . ') para otra cedula.';
            }
        }
    }

    private function validateRow($sheetName, array $row, array $prepared)
    {
        $errors = array();
        $required = array(
            '01_Salas' => array('hospital_ruc','nombre_sala','tipo_atencion','estado'),
            '02_Procedimientos' => array('nombre','observacion','cardiologia','laboratorio','quirofano','estado'),
            '03_Medicinas' => array('nombre_comercial','estado'),
            '04_Especialidades' => array('nombre','descripcion','estado'),
            '05_Doctores' => array('cedula','primer_nombre','primer_apellido','segundo_apellido','email','fecha_nacimiento','registro_doctor','especialidades','estado'),
            '06_Horarios_Doctor' => array('cedula_doctor','dia_semana','hora_inicio','hora_fin','tipo','estado'),
            '11_Usuarios_Roles' => array('cedula','primer_nombre','primer_apellido','segundo_apellido','email','fecha_nacimiento','rol','ocupacion','estado'),
            '12_Productos' => array('codigo','nombre','descripcion','unidad_medida','precio_compra','precio_venta','iva','registro_sanitario','farmacia','estado'),
            '15_Seguros' => array('nombre','descripcion','tipo_seguro','color_hex','copago','visible','estado'),
            '16_Empresa' => array('ruc','razon_social','nombre_comercial','ciudad','direccion','email','telefono_1','telefono_2','establecimiento','punto_emision','obligado_contabilidad','estado'),
            '18_Bancos' => array('nombre','estado'),
            '19_Formas_Pago' => array('codigo','descripcion','requiere_banco','requiere_referencia','estado'),
            '20_CIE10_CIE11' => array('version','codigo','descripcion','estado'),
            '21_Examenes_Lab' => array('codigo','nombre','descripcion','agrupador','valor','tarifario','valor_reactivo','valor_implementos','tipo_muestra','estado'),
        );
        foreach (isset($required[$sheetName]) ? $required[$sheetName] : array() as $field) {
            if (!isset($row[$field]) || $row[$field] === '') $errors[] = $field . ' es obligatorio';
        }
        if ($errors) return $errors;

        if (isset($row['estado']) && !in_array($this->key($row['estado']), array('ACTIVO','INACTIVO'), true)) {
            $errors[] = 'estado debe ser ACTIVO o INACTIVO';
        }

        switch ($sheetName) {
            case '01_Salas':
                if (!in_array($this->key($row['tipo_atencion']), array('PROCEDIMIENTO','CONSULTA'), true)) $errors[] = 'tipo_atencion debe ser PROCEDIMIENTO o CONSULTA';
                if (!$this->hospitalForCompany($row['hospital_ruc'], $prepared)) $errors[] = 'no existe una relacion entre el RUC indicado y un hospital';
                if (mb_strlen($row['nombre_sala'], 'UTF-8') > 30) $errors[] = 'nombre_sala no puede superar 30 caracteres';
                break;
            case '02_Procedimientos':
                foreach (array('cardiologia','laboratorio','quirofano') as $field) $this->validateYesNo($row[$field], $field, $errors);
                break;
            case '03_Medicinas':
                if ($row['precio_unitario'] !== '' && !$this->isNonNegativeNumber($row['precio_unitario'])) $errors[] = 'precio_unitario debe ser numerico y no negativo';
                break;
            case '05_Doctores':
                $this->validateUserRow($row, 3, $errors);
                foreach ($this->splitList($row['especialidades']) as $specialty) {
                    if (!$this->nameExists('especialidad', 'nombre', $specialty) && !$this->planned($prepared, '04_Especialidades', 'nombre', $specialty)) {
                        $errors[] = 'la especialidad "' . $specialty . '" no existe ni esta incluida en 04_Especialidades';
                    }
                }
                break;
            case '06_Horarios_Doctor':
                $days = $this->dayMap();
                if (!isset($days[$this->key($row['dia_semana'])])) $errors[] = 'dia_semana no es valido';
                if (!$this->validTime($row['hora_inicio']) || !$this->validTime($row['hora_fin'])) $errors[] = 'las horas deben usar el formato HH:MM';
                elseif ($row['hora_inicio'] >= $row['hora_fin']) $errors[] = 'hora_inicio debe ser menor que hora_fin';
                if (!in_array($this->key($row['tipo']), array('CONSULTA','PROCEDIMIENTO','TODOS'), true)) $errors[] = 'tipo debe ser CONSULTA, PROCEDIMIENTO o TODOS';
                $doctor = DB::table('users')->where('id', $row['cedula_doctor'])->first();
                $doctorRoleId = User::roleIdForName(array('DOCTORES', 'DOCTOR'));
                if ($doctor && ($doctorRoleId === null || (int) $doctor->id_tipo_usuario !== $doctorRoleId)) $errors[] = 'la cedula pertenece a un usuario que no es doctor';
                if (!$doctor && !$this->planned($prepared, '05_Doctores', 'cedula', $row['cedula_doctor'])) $errors[] = 'el doctor no existe ni esta incluido en 05_Doctores';
                break;
            case '11_Usuarios_Roles':
                $role = $this->roleByName($row['rol']);
                if (!$role) $errors[] = 'el rol "' . $row['rol'] . '" no existe';
                $this->validateUserRow($row, $role ? $role->id : null, $errors);
                break;
            case '12_Productos':
                if (mb_strlen($row['codigo'], 'UTF-8') > 50) $errors[] = 'codigo no puede superar 50 caracteres';
                if (mb_strlen($row['unidad_medida'], 'UTF-8') > 5) $errors[] = 'unidad_medida no puede superar 5 caracteres';
                foreach (array('precio_compra','precio_venta') as $field) if (!$this->isNonNegativeNumber($row[$field])) $errors[] = $field . ' debe ser numerico y no negativo';
                foreach (array('iva','farmacia') as $field) $this->validateYesNo($row[$field], $field, $errors);
                break;
            case '15_Seguros':
                if (!in_array($this->key($row['tipo_seguro']), array('PUBLICO','PRIVADO'), true)) $errors[] = 'tipo_seguro debe ser PUBLICO o PRIVADO';
                if (!preg_match('/^#[0-9A-F]{6}$/', mb_strtoupper($row['color_hex'], 'UTF-8'))) $errors[] = 'color_hex debe usar el formato #RRGGBB';
                foreach (array('copago','visible') as $field) $this->validateYesNo($row[$field], $field, $errors);
                if ($row['email'] !== '' && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'email no es valido';
                if (!$this->nameExists('seguro_tipos', 'nombre', $row['tipo_seguro'])) $errors[] = 'el tipo de seguro no existe en seguro_tipos';
                $colorOwner = \Sis_medico\Seguro::query()->whereRaw('UPPER(color) = ?', array(mb_strtoupper($row['color_hex'], 'UTF-8')))->first();
                if ($colorOwner && $this->key($colorOwner->nombre) !== $this->key($row['nombre'])) $errors[] = 'el color ya esta asignado a otro seguro';
                break;
            case '16_Empresa':
                if (!preg_match('/^[A-Za-z0-9]{10,13}$/', $row['ruc'])) $errors[] = 'ruc debe contener entre 10 y 13 caracteres alfanumericos';
                if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'email no es valido';
                $this->validateYesNo($row['obligado_contabilidad'], 'obligado_contabilidad', $errors);
                break;
            case '19_Formas_Pago':
                foreach (array('requiere_banco','requiere_referencia') as $field) $this->validateYesNo($row[$field], $field, $errors);
                break;
            case '20_CIE10_CIE11':
                if (!in_array($this->key($row['version']), array('CIE10','CIE11'), true)) $errors[] = 'version debe ser CIE10 o CIE11';
                if ($this->key($row['version']) === 'CIE10' && $this->isCieFourCharacter($row['codigo'])) {
                    $parent = $row['codigo_padre'];
                    if ($parent === '') $errors[] = 'codigo_padre es obligatorio para codigos CIE-10 de cuatro caracteres';
                    elseif (!DB::table('cie_10_3')->where('id', $parent)->exists() && !$this->planned($prepared, '20_CIE10_CIE11', 'codigo', $parent)) $errors[] = 'el codigo padre no existe ni esta incluido en esta hoja';
                }
                break;
            case '21_Examenes_Lab':
                foreach (array('valor','valor_reactivo','valor_implementos') as $field) if (!$this->isNonNegativeNumber($row[$field])) $errors[] = $field . ' debe ser numerico y no negativo';
                if (!$this->nameExists('examen_agrupador', 'nombre', $row['agrupador'])) $errors[] = 'el agrupador de laboratorio no existe';
                if (!in_array($this->key($row['tipo_muestra']), array('SUERO','SANGRE'), true)) $errors[] = 'tipo_muestra debe ser SUERO o SANGRE';
                break;
        }
        return $errors;
    }

    /**
     * Devuelve avisos que no deben impedir una carga valida.
     */
    private function rowWarnings($sheetName, array $row, array $prepared)
    {
        if ($sheetName !== '02_Procedimientos' || $row['grupo'] === '') return array();
        if ($this->nameExists('grupo_procedimiento', 'nombre', $row['grupo'])) return array();

        $isSpecialty = $this->nameExists('especialidad', 'nombre', $row['grupo'])
            || $this->planned($prepared, '04_Especialidades', 'nombre', $row['grupo']);

        if ($isSpecialty) {
            return array('"' . $row['grupo'] . '" es una especialidad, no un grupo de procedimiento; el procedimiento se importara sin clasificacion');
        }

        return array('el grupo de procedimiento "' . $row['grupo'] . '" no existe; el procedimiento se importara sin clasificacion');
    }

    private function validateUserRow(array $row, $expectedRole, array &$errors)
    {
        if (!preg_match('/^[A-Za-z0-9-]{3,13}$/', $row['cedula'])) $errors[] = 'cedula debe tener entre 3 y 13 caracteres';
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'email no es valido';
        if (!$this->validDate($row['fecha_nacimiento'])) $errors[] = 'fecha_nacimiento debe usar AAAA-MM-DD';
        $existing = DB::table('users')->where('id', $row['cedula'])->first();
        if ($existing && $expectedRole && (int) $existing->id_tipo_usuario !== (int) $expectedRole) $errors[] = 'la cedula ya existe con otro rol';
        $emailOwner = DB::table('users')->whereRaw('LOWER(email) = ?', array(mb_strtolower($row['email'], 'UTF-8')))->first();
        if ($emailOwner && (string) $emailOwner->id !== (string) $row['cedula']) $errors[] = 'el email ya pertenece a otra cedula';
    }

    private function rowUniqueKey($sheetName, array $row)
    {
        $fields = array(
            '01_Salas'=>'hospital_ruc|nombre_sala', '02_Procedimientos'=>'nombre', '03_Medicinas'=>'nombre_comercial',
            '04_Especialidades'=>'nombre', '05_Doctores'=>'cedula', '06_Horarios_Doctor'=>'cedula_doctor|dia_semana|hora_inicio|hora_fin|tipo',
            '11_Usuarios_Roles'=>'cedula', '12_Productos'=>'codigo', '15_Seguros'=>'nombre', '16_Empresa'=>'ruc',
            '18_Bancos'=>'nombre', '19_Formas_Pago'=>'codigo', '20_CIE10_CIE11'=>'version|codigo', '21_Examenes_Lab'=>'codigo',
        );
        if (!isset($fields[$sheetName])) return '';
        $parts = array();
        foreach (explode('|', $fields[$sheetName]) as $field) $parts[] = isset($row[$field]) ? $this->key($row[$field]) : '';
        return implode('|', $parts);
    }

    private function secondaryUniqueKey($sheetName, array $row)
    {
        $fields = array('05_Doctores'=>'email', '11_Usuarios_Roles'=>'email', '15_Seguros'=>'color_hex', '19_Formas_Pago'=>'descripcion');
        if (!isset($fields[$sheetName]) || !isset($row[$fields[$sheetName]])) return '';
        return $this->key($row[$fields[$sheetName]]);
    }

    private function rowExists($sheetName, array $row)
    {
        switch ($sheetName) {
            case '01_Salas':
                $hospital = $this->hospitalForCompany($row['hospital_ruc'], array());
                return $hospital && DB::table('sala')->where('id_hospital', $hospital)->whereRaw('UPPER(nombre_sala) = ?', array(mb_strtoupper($row['nombre_sala'], 'UTF-8')))->exists();
            case '02_Procedimientos': return $this->nameExists('procedimiento', 'nombre', $row['nombre']);
            case '03_Medicinas': return $this->nameExists('medicina', 'nombre', $row['nombre_comercial']);
            case '04_Especialidades': return $this->nameExists('especialidad', 'nombre', $row['nombre']);
            case '05_Doctores':
            case '11_Usuarios_Roles': return DB::table('users')->where('id', $row['cedula'])->exists();
            case '06_Horarios_Doctor':
                $day = $this->dayMap(); $dayNumber = isset($day[$this->key($row['dia_semana'])]) ? $day[$this->key($row['dia_semana'])][0] : 0;
                return DB::table('horario_doctor')->where('id_doctor', $row['cedula_doctor'])->where('ndia', $dayNumber)->where('hora_ini', $row['hora_inicio'])->where('hora_fin', $row['hora_fin'])->where('tipo', $this->scheduleType($row['tipo']))->exists();
            case '12_Productos': return DB::table('producto')->whereRaw('UPPER(codigo) = ?', array(mb_strtoupper($row['codigo'], 'UTF-8')))->exists();
            case '15_Seguros': return $this->nameExists('seguros', 'nombre', $row['nombre']);
            case '16_Empresa': return DB::table('empresa')->where('id', $row['ruc'])->exists();
            case '18_Bancos': return $this->nameExists('ct_bancos', 'nombre', $row['nombre']);
            case '19_Formas_Pago': return \Sis_medico\Ct_Tipo_Pago::query()->where(function ($query) use ($row) {
                $query->whereRaw('UPPER(codigo) = ?', array(mb_strtoupper($row['codigo'], 'UTF-8')))
                    ->orWhereRaw('UPPER(nombre) = ?', array(mb_strtoupper($row['descripcion'], 'UTF-8')));
            })->exists();
            case '20_CIE10_CIE11':
                if ($this->key($row['version']) !== 'CIE10') return false;
                return DB::table($this->isCieFourCharacter($row['codigo']) ? 'cie_10_4' : 'cie_10_3')->where('id', $row['codigo'])->exists();
            case '21_Examenes_Lab': return DB::table('examen')->whereRaw('UPPER(codigo_inter) = ?', array(mb_strtoupper($row['codigo'], 'UTF-8')))->exists();
        }
        return false;
    }

    private function planned(array $prepared, $sheetName, $field, $value)
    {
        foreach (isset($prepared[$sheetName]) ? $prepared[$sheetName] : array() as $row) {
            if (isset($row[$field]) && $this->key($row[$field]) === $this->key($value)) return true;
        }
        return false;
    }

    private function nameExists($table, $column, $value)
    {
        if (!Schema::hasTable($table)) return false;
        return DB::table($table)->whereRaw('UPPER(' . $column . ') = ?', array(mb_strtoupper($this->clean($value), 'UTF-8')))->exists();
    }

    private function roleByName($name)
    {
        foreach (DB::table('tipousuario')->where('estado', 1)->get() as $role) {
            $roleKey = $this->key($role->nombre);
            $requested = $this->key($name);
            if ($roleKey === $requested || rtrim($roleKey, 'S') === rtrim($requested, 'S')) return $role;
        }
        return null;
    }

    private function hospitalForCompany($companyId, array $prepared)
    {
        if (Schema::hasTable('company_hospital_contexts')) {
            $query = DB::table('company_hospital_contexts as chc')
                ->join('hospital as h', 'h.id', '=', 'chc.hospital_id')
                ->where('chc.company_id', $companyId)->where('chc.active', 1)->where('h.estado', 1);
            if (Schema::hasColumn('hospital', 'id_empresa')) $query->where('h.id_empresa', (string) $companyId);
            $hospital = $query->value('chc.hospital_id');
            if ($hospital) return (int) $hospital;
        }
        return null;
    }

    private function validateYesNo($value, $field, array &$errors)
    {
        if (!in_array($this->key($value), array('SI','NO'), true)) $errors[] = $field . ' debe ser SI o NO';
    }

    private function yes($value)
    {
        return $this->key($value) === 'SI' ? 1 : 0;
    }

    private function active($value)
    {
        return $this->key($value) === 'ACTIVO' ? 1 : 0;
    }

    private function isNonNegativeNumber($value)
    {
        return is_numeric(str_replace(',', '.', $value)) && (float) str_replace(',', '.', $value) >= 0;
    }

    private function number($value)
    {
        return (float) str_replace(',', '.', $value);
    }

    private function validDate($value)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }

    private function validTime($value)
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value) === 1;
    }

    private function splitList($value)
    {
        return array_values(array_filter(array_map('trim', preg_split('/[;,]/', $value)), function ($item) { return $item !== ''; }));
    }

    private function dayMap()
    {
        return array(
            'LUNES'=>array(1,'Lun.'), 'MARTES'=>array(2,'Mar.'), 'MIERCOLES'=>array(3,'Mie.'),
            'JUEVES'=>array(4,'Jue.'), 'VIERNES'=>array(5,'Vie.'), 'SABADO'=>array(6,'Sab.'), 'DOMINGO'=>array(7,'Dom.'),
        );
    }

    private function scheduleType($value)
    {
        $key = $this->key($value);
        return $key === 'CONSULTA' ? 1 : ($key === 'PROCEDIMIENTO' ? 2 : 0);
    }

    private function isCieFourCharacter($code)
    {
        return strlen(str_replace('.', '', $code)) > 3;
    }

    private function import04Especialidades(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('04_Especialidades', $rows, function ($row) use ($actorId, $ip) {
            \Sis_medico\Especialidad::create(array_merge(array(
                'id_empresa' => app(CompanyCatalogService::class)->companyId(true),
                'nombre' => mb_strtoupper($row['nombre'], 'UTF-8'),
                'descripcion' => $row['descripcion'],
                'estado' => $this->active($row['estado']),
            ), $this->audit($actorId, $ip)));
        });
    }

    private function import16Empresa(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('16_Empresa', $rows, function ($row) use ($actorId, $ip) {
            DB::table('empresa')->insert(array_merge(array(
                'id' => $row['ruc'],
                'razonsocial' => mb_strtoupper($row['razon_social'], 'UTF-8'),
                'nombrecomercial' => mb_strtoupper($row['nombre_comercial'], 'UTF-8'),
                'ciudad' => mb_strtoupper($row['ciudad'], 'UTF-8'),
                'direccion' => $row['direccion'],
                'email' => mb_strtolower($row['email'], 'UTF-8'),
                'telefono1' => $row['telefono_1'],
                'telefono2' => $row['telefono_2'],
                'establecimiento' => $row['establecimiento'],
                'punto_emision' => $row['punto_emision'],
                'ocontabilidad' => $this->yes($row['obligado_contabilidad']),
                'estado' => $this->active($row['estado']),
            ), $this->audit($actorId, $ip)));

            // La empresa importada debe crear su propia unidad de atención;
            // nunca se enlaza automáticamente a una sede global existente.
        });
    }

    private function import05Doctores(array $rows, $actorId, $ip)
    {
        $result = array('inserted' => 0, 'skipped' => 0);
        foreach ($rows as $row) {
            $exists = DB::table('users')->where('id', $row['cedula'])->exists();
            if (!$exists) {
                $this->insertUser($row, 3, $actorId, $ip, true);
                $result['inserted']++;
            } else {
                $result['skipped']++;
            }
            foreach ($this->splitList($row['especialidades']) as $specialty) {
                $specialtyId = \Sis_medico\Especialidad::query()->whereRaw('UPPER(nombre) = ?', array(mb_strtoupper($specialty, 'UTF-8')))->value('id');
                if (!$specialtyId) continue;
                $relationExists = DB::table('user_espe')->where('usuid', $row['cedula'])->where('espid', $specialtyId)->exists();
                if (!$relationExists) {
                    DB::table('user_espe')->insert(array_merge(array('usuid' => $row['cedula'], 'espid' => $specialtyId), $this->audit($actorId, $ip)));
                }
            }
        }
        return $result;
    }

    private function import11UsuariosRoles(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('11_Usuarios_Roles', $rows, function ($row) use ($actorId, $ip) {
            $role = $this->roleByName($row['rol']);
            $this->insertUser($row, $role->id, $actorId, $ip, false);
        });
    }

    private function insertUser(array $row, $roleId, $actorId, $ip, $doctor)
    {
        DB::table('users')->insert(array_merge(array(
            'id' => $row['cedula'],
            'nombre1' => mb_strtoupper($row['primer_nombre'], 'UTF-8'),
            'nombre2' => mb_strtoupper(isset($row['segundo_nombre']) ? $row['segundo_nombre'] : '', 'UTF-8'),
            'apellido1' => mb_strtoupper($row['primer_apellido'], 'UTF-8'),
            'apellido2' => mb_strtoupper($row['segundo_apellido'], 'UTF-8'),
            'email' => mb_strtolower($row['email'], 'UTF-8'),
            'fecha_nacimiento' => $row['fecha_nacimiento'],
            'telefono1' => isset($row['telefono']) ? $row['telefono'] : null,
            'direccion' => isset($row['direccion']) ? $row['direccion'] : null,
            'ocupacion' => isset($row['ocupacion']) ? $row['ocupacion'] : ($doctor ? 'DOCTOR' : null),
            'registro_doctor' => isset($row['registro_doctor']) ? $row['registro_doctor'] : null,
            'tipo_documento' => 1,
            'id_tipo_usuario' => $roleId,
            'estado' => $this->active($row['estado']),
            'password' => bcrypt(str_random(40)),
            'imagen_url' => 'avatar.jpg',
        ), $this->audit($actorId, $ip)));
    }

    private function import06HorariosDoctor(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('06_Horarios_Doctor', $rows, function ($row) use ($actorId, $ip) {
            $day = $this->dayMap()[$this->key($row['dia_semana'])];
            DB::table('horario_doctor')->insert(array_merge(array(
                'id_doctor' => $row['cedula_doctor'],
                'ndia' => $day[0],
                'dia' => $day[1],
                'hora_ini' => $row['hora_inicio'],
                'hora_fin' => $row['hora_fin'],
                'tipo' => $this->scheduleType($row['tipo']),
                'estado' => $this->active($row['estado']),
            ), $this->audit($actorId, $ip)));
        });
    }

    private function import01Salas(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('01_Salas', $rows, function ($row) use ($actorId, $ip) {
            DB::table('sala')->insert(array_merge(array(
                'nombre_sala' => mb_strtoupper($row['nombre_sala'], 'UTF-8'),
                'id_hospital' => $this->hospitalForCompany($row['hospital_ruc'], array()),
                'proc_consul_sala' => $this->key($row['tipo_atencion']) === 'PROCEDIMIENTO' ? 1 : 0,
                'estado' => $this->active($row['estado']),
            ), $this->audit($actorId, $ip)));
        });
    }

    private function import02Procedimientos(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('02_Procedimientos', $rows, function ($row) use ($actorId, $ip) {
            $groupId = $row['grupo'] === '' ? null : DB::table('grupo_procedimiento')->whereRaw('UPPER(nombre) = ?', array(mb_strtoupper($row['grupo'], 'UTF-8')))->value('id');
            DB::table('procedimiento')->insert(array_merge(array(
                'nombre' => mb_strtoupper($row['nombre'], 'UTF-8'),
                'observacion' => $row['observacion'],
                'id_grupo_procedimiento' => $groupId,
                'cardiologia' => $this->yes($row['cardiologia']),
                'laboratorio' => $this->yes($row['laboratorio']),
                'quirofano' => $this->yes($row['quirofano']),
                'estado' => $this->active($row['estado']),
            ), $this->audit($actorId, $ip)));
        });
    }

    private function import03Medicinas(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('03_Medicinas', $rows, function ($row) use ($actorId, $ip) {
            DB::table('medicina')->insert(array_merge(array(
                'nombre' => mb_strtoupper($row['nombre_comercial'], 'UTF-8'),
                'nombre_generico' => $row['nombre_generico'],
                'codigo_atc' => $row['codigo_atc'],
                'concentracion' => $row['concentracion'],
                'presentacion' => $row['presentacion'],
                'laboratorio' => $row['laboratorio'],
                'precio_unitario' => $row['precio_unitario'] === '' ? null : $this->number($row['precio_unitario']),
                'indicaciones' => $row['indicaciones'],
                'contraindicaciones' => $row['contraindicaciones'],
                'estado' => $this->active($row['estado']),
            ), $this->audit($actorId, $ip)));
        });
    }

    private function import12Productos(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('12_Productos', $rows, function ($row) use ($actorId, $ip) {
            DB::table('producto')->insert(array_merge(array(
                'nombre' => mb_strtoupper($row['nombre'], 'UTF-8'),
                'descripcion' => $row['descripcion'],
                'estado' => $this->active($row['estado']),
                'medida' => mb_strtoupper($row['unidad_medida'], 'UTF-8'),
                'minimo' => 0,
                'precio_compra' => $this->number($row['precio_compra']),
                'precio_venta' => $this->number($row['precio_venta']),
                'codigo' => mb_strtoupper($row['codigo'], 'UTF-8'),
                'despacho' => 0,
                'registro_sanitario' => $row['registro_sanitario'],
                'iva' => $this->yes($row['iva']),
                'farmacia' => $this->yes($row['farmacia']),
            ), $this->audit($actorId, $ip)));
        });
    }

    private function import15Seguros(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('15_Seguros', $rows, function ($row) use ($actorId, $ip) {
            $type = DB::table('seguro_tipos')->whereRaw('UPPER(nombre) = ?', array(mb_strtoupper($row['tipo_seguro'], 'UTF-8')))->first();
            \Sis_medico\Seguro::create(array_merge(array(
                'id_empresa' => app(CompanyCatalogService::class)->companyId(true),
                'nombre' => mb_strtoupper($row['nombre'], 'UTF-8'),
                'descripcion' => $row['descripcion'],
                'tipo' => $this->key($row['tipo_seguro']) === 'PUBLICO' ? 0 : 1,
                'color' => mb_strtoupper($row['color_hex'], 'UTF-8'),
                'ruc_cliente' => $row['ruc_cliente'] === '' ? null : $row['ruc_cliente'],
                'email' => $row['email'] === '' ? null : mb_strtolower($row['email'], 'UTF-8'),
                'copago' => $this->yes($row['copago']),
                'visualizar' => $this->yes($row['visible']),
                'inactivo' => $this->active($row['estado']),
                'id_seguro_tipos' => $type->id,
            ), $this->audit($actorId, $ip)));
        });
    }

    private function import18Bancos(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('18_Bancos', $rows, function ($row) use ($actorId, $ip) {
            DB::table('ct_bancos')->insert(array_merge(array(
                'nombre' => mb_strtoupper($row['nombre'], 'UTF-8'),
                'estado' => $this->active($row['estado']),
            ), $this->audit($actorId, $ip)));
        });
    }

    private function import19FormasPago(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('19_Formas_Pago', $rows, function ($row) use ($actorId, $ip) {
            \Sis_medico\Ct_Tipo_Pago::create(array_merge(array(
                'id_empresa' => app(CompanyCatalogService::class)->companyId(true),
                'nombre' => $row['descripcion'],
                'codigo' => mb_strtoupper($row['codigo'], 'UTF-8'),
                'codigo_sri' => '01',
                'requiere_banco' => $this->yes($row['requiere_banco']),
                'requiere_referencia' => $this->yes($row['requiere_referencia']),
                'porcentaje' => 0,
                'estado' => $this->active($row['estado']),
            ), $this->audit($actorId, $ip)));
        });
    }

    private function import20CIE10CIE11(array $rows, $actorId, $ip)
    {
        $result = array('inserted' => 0, 'skipped' => 0);
        usort($rows, function ($left, $right) {
            return strlen(str_replace('.', '', $left['codigo'])) <=> strlen(str_replace('.', '', $right['codigo']));
        });
        foreach ($rows as $row) {
            if ($this->key($row['version']) !== 'CIE10' || $this->rowExists('20_CIE10_CIE11', $row)) {
                $result['skipped']++;
                continue;
            }
            $data = array_merge(array(
                'id' => mb_strtoupper($row['codigo'], 'UTF-8'),
                'descripcion' => mb_strtoupper($row['descripcion'], 'UTF-8'),
                'estado' => $this->active($row['estado']),
            ), $this->audit($actorId, $ip));
            if ($this->isCieFourCharacter($row['codigo'])) {
                $data['id_cie_10_3'] = mb_strtoupper($row['codigo_padre'], 'UTF-8');
                DB::table('cie_10_4')->insert($data);
            } else {
                DB::table('cie_10_3')->insert($data);
            }
            $result['inserted']++;
        }
        return $result;
    }

    private function import21ExamenesLab(array $rows, $actorId, $ip)
    {
        return $this->importSimpleRows('21_Examenes_Lab', $rows, function ($row) use ($actorId, $ip) {
            $groupId = DB::table('examen_agrupador')->whereRaw('UPPER(nombre) = ?', array(mb_strtoupper($row['agrupador'], 'UTF-8')))->value('id');
            DB::table('examen')->insert(array_merge(array(
                'codigo_inter' => mb_strtoupper($row['codigo'], 'UTF-8'),
                'nombre' => mb_strtoupper($row['nombre'], 'UTF-8'),
                'descripcion' => $row['descripcion'],
                'id_agrupador' => $groupId,
                'valor' => $this->number($row['valor']),
                'tarifario' => mb_strtoupper($row['tarifario'], 'UTF-8'),
                'valor_reactivo' => $this->number($row['valor_reactivo']),
                'valor_implementos' => $this->number($row['valor_implementos']),
                'tipo_muestra' => $this->key($row['tipo_muestra']) === 'SANGRE' ? 2 : 1,
                'estado' => $this->active($row['estado']),
            ), $this->audit($actorId, $ip)));
        });
    }

    private function importSimpleRows($sheetName, array $rows, $callback)
    {
        $result = array('inserted' => 0, 'skipped' => 0);
        foreach ($rows as $row) {
            if ($this->rowExists($sheetName, $row)) {
                $result['skipped']++;
                continue;
            }
            $callback($row);
            $result['inserted']++;
        }
        return $result;
    }

    private function audit($actorId, $ip)
    {
        return array(
            'id_usuariocrea' => $actorId,
            'id_usuariomod' => $actorId,
            'ip_creacion' => $ip,
            'ip_modificacion' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
    }
}
