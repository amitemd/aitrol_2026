<?php

use Database\Seeders\OdontologiaCatalogSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReassignOdontologiaCatalogSpecialty extends Migration
{
    public function up(): void
    {
        foreach (['especialidad', 'procedimiento', 'cie_10_3'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException('No se puede reparar el catalogo odontologico: falta '.$table.'.');
            }
        }

        $odontologiaId = DB::table('especialidad')
            ->where('estado', 1)
            ->whereRaw('LOWER(TRIM(nombre)) IN (?, ?)', ['odontologia', 'odontología'])
            ->orderBy('id')
            ->value('id');
        if (is_null($odontologiaId)) {
            throw new RuntimeException('No existe una especialidad ODONTOLOGIA activa.');
        }
        $odontologiaId = (int) $odontologiaId;

        DB::transaction(function () use ($odontologiaId) {
            $this->reassignProcedures($odontologiaId);
            $this->reassignCie10($odontologiaId);
        });

        require_once database_path('seeders/OdontologiaCatalogSeeder.php');
        (new OdontologiaCatalogSeeder)->run();
    }

    public function down(): void
    {
        // No se revierten catalogos clinicos que ya pueden tener referencias.
    }

    private function reassignProcedures(int $odontologiaId): void
    {
        if ($odontologiaId === 3) {
            return;
        }

        $sql = $this->readDataFile('procedimientos_odontologia.sql');
        if (! preg_match('/INSERT INTO tmp_procedimientos_odontologia.*?VALUES\s*(.*?)\s*;\s*\n\s*UPDATE/su', $sql, $block)) {
            throw new RuntimeException('No se pudo leer el catalogo de procedimientos odontologicos.');
        }
        preg_match_all("/\\('((?:''|[^'])*)',\\s*'((?:''|[^'])*)'\\)(?:,|;)/u", $block[1].';', $matches, PREG_SET_ORDER);
        $names = array_values(array_unique(array_map(function ($row) {
            return str_replace("''", "'", $row[1]);
        }, $matches)));
        if (count($names) < 250) {
            throw new RuntimeException('El catalogo de procedimientos odontologicos esta incompleto.');
        }

        foreach (array_chunk($names, 250) as $chunk) {
            $values = ['id_especialidad' => $odontologiaId];
            if (Schema::hasColumn('procedimiento', 'updated_at')) {
                $values['updated_at'] = now();
            }
            DB::table('procedimiento')
                ->where('id_especialidad', 3)
                ->whereIn('nombre', $chunk)
                ->update($values);
        }
    }

    private function reassignCie10(int $odontologiaId): void
    {
        if ($odontologiaId === 3) {
            return;
        }

        $sql = $this->readDataFile('cie10_odontologia_ecuador.sql');
        if (! preg_match('/INSERT INTO cie_10_3 .*? VALUES\s*(.*?)\s*ON DUPLICATE KEY UPDATE/su', $sql, $block)) {
            throw new RuntimeException('No se pudo leer el catalogo CIE-10 odontologico.');
        }
        preg_match_all("/\\('([^']+)',\\s*'((?:''|[^'])*)',\\s*1,/u", $block[1], $matches, PREG_SET_ORDER);
        $ids = array_values(array_unique(array_map(function ($row) {
            return $row[1];
        }, $matches)));
        if (count($ids) < 90) {
            throw new RuntimeException('El catalogo CIE-10 odontologico esta incompleto.');
        }

        foreach (array_chunk($ids, 250) as $chunk) {
            $values = ['id_especialidad' => $odontologiaId];
            if (Schema::hasColumn('cie_10_3', 'updated_at')) {
                $values['updated_at'] = now();
            }
            DB::table('cie_10_3')
                ->where('id_especialidad', 3)
                ->whereIn('id', $chunk)
                ->update($values);
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
}
