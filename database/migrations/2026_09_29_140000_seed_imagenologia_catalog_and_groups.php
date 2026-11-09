<?php

use Database\Seeders\ImagenologiaCatalogSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class SeedImagenologiaCatalogAndGroups extends Migration
{
    /**
     * Carga los agrupadores y estudios de la orden institucional de imagenes.
     * El seeder es idempotente para que pueda ejecutarse de nuevo sin duplicar
     * procedimientos existentes del catalogo de imagenologia.
     */
    public function up(): void
    {
        foreach (['users', 'procedimiento', 'grupo_procedimiento'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException('No se puede cargar Imagenologia: falta la tabla '.$table.'.');
            }
        }

        require_once database_path('seeders/ImagenologiaCatalogSeeder.php');

        (new ImagenologiaCatalogSeeder)->run();
    }

    /**
     * No se borra el catalogo en rollback porque sus procedimientos pueden
     * haber sido usados por ordenes clinicas.
     */
    public function down(): void
    {
        // Intencionalmente irreversible para preservar referencias clinicas.
    }
}
