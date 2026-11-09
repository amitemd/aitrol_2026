<?php

use Database\Seeders\OdontologiaCatalogSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class SeedOdontologiaCatalogAndGroups extends Migration
{
    /**
     * Carga el catalogo dental despues de que las migraciones anteriores hayan
     * creado sus columnas y tablas. El seeder es idempotente: puede volver a
     * ejecutarse sin duplicar grupos, especialidades ni procedimientos.
     */
    public function up(): void
    {
        foreach (['procedimiento', 'grupo_procedimiento', 'especialidad', 'cie_10_3', 'cie_10_4'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException('No se puede cargar Odontologia: falta la tabla '.$table.'.');
            }
        }

        foreach ([
            ['procedimiento', 'id_especialidad'],
            ['procedimiento', 'precio'],
            ['cie_10_3', 'id_especialidad'],
        ] as $requiredColumn) {
            if (! Schema::hasColumn($requiredColumn[0], $requiredColumn[1])) {
                throw new RuntimeException(
                    'No se puede cargar Odontologia: falta '.$requiredColumn[0].'.'.$requiredColumn[1].'.'
                );
            }
        }

        require_once database_path('seeders/OdontologiaCatalogSeeder.php');

        (new OdontologiaCatalogSeeder)->run();
    }

    /**
     * No se elimina informacion clinica en rollback. Los procedimientos pueden
     * haber sido utilizados por historias, ordenes o cotizaciones.
     */
    public function down(): void
    {
        // Intencionalmente irreversible para preservar referencias clinicas.
    }
}
