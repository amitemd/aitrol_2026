<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class OdontologiaCatalogSeeder extends Seeder
{
    private const USER_IP = '127.0.0.1';

    private const IMAGE_PROCEDURES = [
        'Toma de fotografías clínicas',
        'Solicitud e interpretación de radiografía periapical',
        'Solicitud e interpretación de radiografía oclusal',
        'Solicitud e interpretación de radiografía panorámica',
        'Solicitud e interpretación de radiografía cefalométrica',
        'Solicitud de tomografía para diagnóstico oral o maxilofacial',
        'Control radiográfico endodóntico',
        'Registro fotográfico ortodóncico',
    ];

    public function run(): void
    {
        foreach (['users', 'especialidad', 'procedimiento', 'grupo_procedimiento', 'cie_10_3', 'cie_10_4', 'procedimiento_odontologia_precios_bkp_20260914'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException('Falta la tabla '.$table.'. Ejecute primero las migraciones.');
            }
        }
        foreach ([
            ['procedimiento', 'id_especialidad'],
            ['procedimiento', 'precio'],
            ['cie_10_3', 'id_especialidad'],
        ] as $requiredColumn) {
            if (! $this->hasColumn($requiredColumn[0], $requiredColumn[1])) {
                throw new RuntimeException('Falta '.$requiredColumn[0].'.'.$requiredColumn[1].'. Ejecute primero las migraciones de Odontologia.');
            }
        }

        $actor = (string) DB::table('users')->where('estado', 1)->orderBy('id')->value('id');
        $odontologiaId = $this->odontologySpecialtyId();
        if ($actor === '' || is_null($odontologiaId)) {
            throw new RuntimeException('Se requiere un usuario activo y una especialidad Odontologia activa.');
        }

        DB::disableQueryLog();
        DB::transaction(function () use ($actor, $odontologiaId) {
            $now = now();
            $periodontalId = $this->periodontalSpecialty($actor, $now);
            $groupIds = $this->seedProcedureGroups($actor, $now);
            $procedureIds = $this->seedProcedures($actor, $now, $odontologiaId, $periodontalId, $groupIds);
            $this->applyReferencePrices($procedureIds, $now);
            $this->classifyPeriodontalProcedures($procedureIds, $periodontalId, $actor, $now);
            $this->seedDentalCie10($actor, $now, $odontologiaId);
        });

        if ($this->command) {
            $this->command->info('Catalogos odontologicos y periodontales cargados de forma idempotente.');
        }
    }

    private function odontologySpecialtyId(): ?int
    {
        $id = DB::table('especialidad')
            ->where('estado', 1)
            ->whereRaw('LOWER(TRIM(nombre)) IN (?, ?)', ['odontologia', 'odontología'])
            ->orderBy('id')
            ->value('id');

        return is_null($id) ? null : (int) $id;
    }

    /**
     * Los grupos siguen el area clinica del catalogo dental. El tipo 3 evita
     * mezclar tratamientos odontologicos con los flujos endoscopicos y
     * funcionales. Solo los estudios radiograficos/fotograficos usan tipo 2.
     */
    private function seedProcedureGroups(string $actor, $now): array
    {
        $definitions = [
            'diagnostico' => ['ODONTOLOGIA - DIAGNOSTICO Y PLANIFICACION', 'Consulta, examen, diagnostico y planificacion odontologica', 3],
            'prevencion' => ['ODONTOLOGIA - PROMOCION Y PREVENCION', 'Promocion, higiene y prevencion de enfermedades bucales', 3],
            'anestesia' => ['ODONTOLOGIA - ANESTESIA Y MANEJO DEL PACIENTE', 'Anestesia y manejo clinico del paciente odontologico', 3],
            'operatoria' => ['ODONTOLOGIA - OPERATORIA Y RESTAURACION DENTAL', 'Operatoria, restauraciones y reconstrucciones dentales', 3],
            'endodoncia' => ['ODONTOLOGIA - ENDODONCIA', 'Diagnostico y tratamiento pulpar y perirradicular', 3],
            'periodoncia' => ['PERIODONTOLOGIA - PERIODONCIA CLINICA', 'Prevencion, diagnostico y tratamiento de tejidos periodontales', 3],
            'odontopediatria' => ['ODONTOLOGIA - ODONTOPEDIATRIA', 'Atencion preventiva y terapeutica de pacientes pediatricos', 3],
            'cirugia_oral' => ['ODONTOLOGIA - CIRUGIA ORAL', 'Exodoncias y procedimientos quirurgicos de la cavidad oral', 3],
            'maxilofacial' => ['ODONTOLOGIA - CIRUGIA MAXILOFACIAL Y ATM', 'Cirugia maxilofacial y tratamiento de la articulacion temporomandibular', 3],
            'ortodoncia' => ['ODONTOLOGIA - ORTODONCIA Y ORTOPEDIA DENTOFACIAL', 'Diagnostico y tratamiento de maloclusiones y alteraciones dentofaciales', 3],
            'rehabilitacion' => ['ODONTOLOGIA - REHABILITACION ORAL Y PROTESIS', 'Rehabilitacion oral mediante protesis e implantes', 3],
            'imagenes' => ['ODONTOLOGIA Y PERIODONTOLOGIA - IMAGENES', 'Radiografias, tomografias y fotografias clinicas odontologicas', 2],
        ];

        $ids = [];
        foreach ($definitions as $key => $definition) {
            $existing = DB::table('grupo_procedimiento')->where('nombre', $definition[0])->orderBy('id')->first();
            $values = $this->onlyColumns('grupo_procedimiento', [
                'nombre' => $definition[0],
                'descripcion' => $definition[1],
                'tipo_procedimiento' => $definition[2],
                'id_usuariocrea' => $actor,
                'id_usuariomod' => $actor,
                'ip_creacion' => self::USER_IP,
                'ip_modificacion' => self::USER_IP,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($existing) {
                unset($values['id_usuariocrea'], $values['ip_creacion'], $values['created_at']);
                DB::table('grupo_procedimiento')->where('id', $existing->id)->update($values);
                $ids[$key] = (int) $existing->id;
            } else {
                $ids[$key] = (int) DB::table('grupo_procedimiento')->insertGetId($values);
            }
        }

        return $ids;
    }

    private function periodontalSpecialty(string $actor, $now): int
    {
        $existingRows = DB::table('especialidad')
            ->whereRaw('LOWER(TRIM(nombre)) IN (?, ?, ?)', ['periodoncia', 'periodontologia', 'periodontología'])
            ->orderBy('id')
            ->get();
        $existing = $existingRows->first(function ($especialidad) {
            return strpos(strtolower(trim((string) $especialidad->nombre)), 'periodontolog') === 0;
        }) ?: $existingRows->first();
        $values = $this->onlyColumns('especialidad', [
            'nombre' => 'PERIODONTOLOGIA',
            'descripcion' => 'Prevención, diagnóstico y tratamiento de enfermedades periodontales',
            'estado' => 1,
            'id_usuariocrea' => $actor,
            'id_usuariomod' => $actor,
            'ip_creacion' => self::USER_IP,
            'ip_modificacion' => self::USER_IP,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        if ($existing) {
            foreach ($existingRows as $duplicate) {
                if ((int) $duplicate->id === (int) $existing->id) {
                    continue;
                }
                $this->mergePeriodontalReferences((int) $duplicate->id, (int) $existing->id, $now);
            }
            unset($values['id_usuariocrea'], $values['ip_creacion'], $values['created_at']);
            DB::table('especialidad')->where('id', $existing->id)->update($values);

            return (int) $existing->id;
        }

        return (int) DB::table('especialidad')->insertGetId($values);
    }

    private function mergePeriodontalReferences(int $sourceId, int $targetId, $now): void
    {
        if (Schema::hasTable('user_espe') && $this->hasColumn('user_espe', 'espid')) {
            $assignedUsers = DB::table('user_espe')->where('espid', $targetId)->pluck('usuid')->all();
            if ($assignedUsers) {
                DB::table('user_espe')->where('espid', $sourceId)->whereIn('usuid', $assignedUsers)->delete();
            }
            DB::table('user_espe')->where('espid', $sourceId)->update(['espid' => $targetId]);
        }

        foreach ([
            ['agenda', 'espid'],
            ['procedimiento', 'id_especialidad'],
            ['cie_10_3', 'id_especialidad'],
        ] as $reference) {
            if (Schema::hasTable($reference[0]) && $this->hasColumn($reference[0], $reference[1])) {
                DB::table($reference[0])->where($reference[1], $sourceId)->update([$reference[1] => $targetId]);
            }
        }

        $inactive = $this->onlyColumns('especialidad', ['estado' => 0, 'updated_at' => $now]);
        DB::table('especialidad')->where('id', $sourceId)->update($inactive);
    }

    private function seedProcedures(string $actor, $now, int $odontologiaId, int $periodontalId, array $groupIds): array
    {
        $sql = $this->readDataFile('procedimientos_odontologia.sql');
        if (! preg_match('/INSERT INTO tmp_procedimientos_odontologia.*?VALUES\s*(.*?)\s*;\s*\n\s*UPDATE/su', $sql, $block)) {
            throw new RuntimeException('No se pudo leer el catalogo de procedimientos odontologicos.');
        }
        preg_match_all("/\\('((?:''|[^'])*)',\\s*'((?:''|[^'])*)'\\)(?:,|;)/u", $block[1].';', $matches, PREG_SET_ORDER);
        if (count($matches) < 250) {
            throw new RuntimeException('El catalogo de procedimientos odontologicos esta incompleto.');
        }

        $ids = [];
        foreach ($matches as $row) {
            $name = str_replace("''", "'", $row[1]);
            $category = str_replace("''", "'", $row[2]);
            $isPeriodontal = $category === 'Periodoncia'
                || in_array($name, ['Evaluación periodontal', 'Sondaje periodontal'], true);
            $normalizedCategory = $isPeriodontal ? 'Periodoncia' : $category;
            $existing = DB::table('procedimiento')
                ->whereRaw('BINARY nombre = BINARY ?', [$name])
                ->whereIn('id_especialidad', [$odontologiaId, $periodontalId])
                ->orderBy('id')->first();
            $values = $this->onlyColumns('procedimiento', [
                'nombre' => $name, 'observacion' => $normalizedCategory, 'cardiologia' => 0, 'estado' => 1,
                'id_especialidad' => $isPeriodontal ? $periodontalId : $odontologiaId,
                'deleted_at' => null,
                'id_grupo_procedimiento' => $this->procedureGroupId($name, $normalizedCategory, $groupIds),
                'laboratorio' => 0,
                'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                'ip_creacion' => self::USER_IP, 'ip_modificacion' => self::USER_IP,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            if ($existing) {
                unset($values['nombre'], $values['id_usuariocrea'], $values['ip_creacion'], $values['created_at']);
                DB::table('procedimiento')->where('id', $existing->id)->update($values);
                $ids[] = (int) $existing->id;
            } else {
                $ids[] = (int) DB::table('procedimiento')->insertGetId($values);
            }
        }

        return array_values(array_unique($ids));
    }

    private function procedureGroupId(string $name, string $category, array $groupIds): int
    {
        if (in_array($name, self::IMAGE_PROCEDURES, true)) {
            return $groupIds['imagenes'];
        }

        $categoryGroups = [
            'Consulta, diagnóstico y planificación' => 'diagnostico',
            'Promoción y prevención de salud bucal' => 'prevencion',
            'Anestesia y manejo del paciente' => 'anestesia',
            'Operatoria y restauración dental' => 'operatoria',
            'Endodoncia' => 'endodoncia',
            'Periodoncia' => 'periodoncia',
            'Odontopediatría' => 'odontopediatria',
            'Odontopediatría / Cirugía oral' => 'cirugia_oral',
            'Cirugía oral' => 'cirugia_oral',
            'Cirugía maxilofacial y ATM' => 'maxilofacial',
            'Ortodoncia y ortopedia dentofacial' => 'ortodoncia',
            'Rehabilitación oral y prótesis' => 'rehabilitacion',
        ];

        if (! isset($categoryGroups[$category], $groupIds[$categoryGroups[$category]])) {
            throw new RuntimeException('No existe grupo odontologico para '.$name.' ('.$category.').');
        }

        return $groupIds[$categoryGroups[$category]];
    }

    private function applyReferencePrices(array $procedureIds, $now): void
    {
        if (! $procedureIds) {
            return;
        }
        if (Schema::hasTable('procedimiento_odontologia_precios_bkp_20260914')) {
            foreach (array_chunk($procedureIds, 300) as $ids) {
                $rows = DB::table('procedimiento')->whereIn('id', $ids)->whereNull('precio')
                    ->get(['id', 'nombre', 'precio']);
                $backup = [];
                foreach ($rows as $row) {
                    $backup[] = ['id_procedimiento' => $row->id, 'nombre' => $row->nombre,
                        'precio_anterior' => $row->precio, 'respaldado_en' => $now];
                }
                if ($backup) {
                    DB::table('procedimiento_odontologia_precios_bkp_20260914')->insertOrIgnore($backup);
                }
            }
        }

        $sql = $this->readDataFile('precios_procedimientos_odontologia.sql');
        if (! preg_match('/UPDATE procedimiento p\s+SET p\.precio = CASE.*?AND p\.precio IS NULL;/su', $sql, $match)) {
            throw new RuntimeException('No se pudo leer el tarifario odontologico.');
        }
        foreach (array_chunk($procedureIds, 300) as $ids) {
            $statement = str_replace(
                'AND p.precio IS NULL;',
                'AND p.precio IS NULL AND p.id IN ('.implode(',', array_map('intval', $ids)).');',
                $match[0]
            );
            DB::unprepared($statement);
        }
    }

    private function classifyPeriodontalProcedures(array $procedureIds, int $periodontalId, string $actor, $now): void
    {
        DB::table('procedimiento')->whereIn('id', $procedureIds)->where(function ($query) {
            $query->where('observacion', 'Periodoncia')
                ->orWhereIn('nombre', ['Evaluación periodontal', 'Sondaje periodontal']);
        })->update([
            'id_especialidad' => $periodontalId,
            'observacion' => 'Periodoncia',
            'id_usuariomod' => $actor,
            'ip_modificacion' => self::USER_IP,
            'updated_at' => $now,
        ]);
    }

    private function seedDentalCie10(string $actor, $now, int $odontologiaId): void
    {
        $sql = $this->readDataFile('cie10_odontologia_ecuador.sql');
        if (! preg_match('/INSERT INTO cie_10_3 .*? VALUES\s*(.*?)\s*ON DUPLICATE KEY UPDATE/su', $sql, $parentsBlock)
            || ! preg_match('/INSERT INTO cie_10_4 .*? VALUES\s*(.*?)\s*ON DUPLICATE KEY UPDATE/su', $sql, $childrenBlock)) {
            throw new RuntimeException('No se pudo leer el catalogo CIE-10 odontologico.');
        }
        preg_match_all("/\\('([^']+)',\\s*'((?:''|[^'])*)',\\s*1,/u", $parentsBlock[1], $parents, PREG_SET_ORDER);
        preg_match_all("/\\('([^']+)',\\s*'((?:''|[^'])*)',\\s*'([^']+)',\\s*1,/u", $childrenBlock[1], $children, PREG_SET_ORDER);
        if (count($parents) < 90 || count($children) < 250) {
            throw new RuntimeException('El catalogo CIE-10 odontologico esta incompleto.');
        }

        foreach ($parents as $row) {
            $existing = DB::table('cie_10_3')->where('id', $row[1])->exists();
            $values = $this->onlyColumns('cie_10_3', [
                'descripcion' => str_replace("''", "'", $row[2]), 'estado' => 1, 'id_especialidad' => $odontologiaId,
                'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                'ip_creacion' => self::USER_IP, 'ip_modificacion' => self::USER_IP,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            if ($existing) {
                unset($values['descripcion'], $values['id_usuariocrea'], $values['ip_creacion'], $values['created_at']);
                DB::table('cie_10_3')->where('id', $row[1])->update($values);
            } else {
                DB::table('cie_10_3')->insert(array_merge(['id' => $row[1]], $values));
            }
        }
        foreach ($children as $row) {
            $existing = DB::table('cie_10_4')->where('id', $row[1])->exists();
            $values = $this->onlyColumns('cie_10_4', [
                'descripcion' => str_replace("''", "'", $row[2]), 'id_cie_10_3' => $row[3], 'estado' => 1,
                'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
                'ip_creacion' => self::USER_IP, 'ip_modificacion' => self::USER_IP,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            if ($existing) {
                unset($values['descripcion'], $values['id_cie_10_3'], $values['id_usuariocrea'], $values['ip_creacion'], $values['created_at']);
                DB::table('cie_10_4')->where('id', $row[1])->update($values);
            } else {
                DB::table('cie_10_4')->insert(array_merge(['id' => $row[1]], $values));
            }
        }
    }

    private function readDataFile(string $name): string
    {
        $path = database_path('data/odontologia/'.$name);
        $contents = is_file($path) ? file_get_contents($path) : false;
        if ($contents === false) {
            throw new RuntimeException('No se encontro '.$path.'.');
        }

        return $contents;
    }

    private function hasColumn(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        return DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)->where('COLUMN_NAME', $column)->exists();
    }

    private function onlyColumns(string $table, array $values): array
    {
        return array_intersect_key($values, array_flip(Schema::getColumnListing($table)));
    }
}
