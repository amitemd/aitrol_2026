<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Services\LaboratoryExamCatalogSeederService;

class SeedInitialLaboratoryExamCatalog extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('users')
            || ! Schema::hasTable('examen_agrupador')
            || ! Schema::hasTable('examen')) {
            return;
        }

        $actor = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        if (! DB::table('users')->where('id', $actor)->exists()) {
            $actor = DB::table('users')->orderBy('id')->value('id');
        }

        // En una instalación vacía los usuarios se crean después de migrate.
        // DatabaseSeeder repite esta carga inmediatamente después del bootstrap.
        if (! $actor) {
            return;
        }

        app(LaboratoryExamCatalogSeederService::class)->seed($actor, '127.0.0.1');
    }

    public function down()
    {
        // Catálogo no destructivo: puede estar relacionado con órdenes clínicas.
    }
}
