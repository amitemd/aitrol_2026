<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Services\InstallationReferenceDataService;

class SeedVerifiedInstallationReferenceData extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('users')) {
            return;
        }
        $actor = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        if (! DB::table('users')->where('id', $actor)->exists()) {
            $actor = DB::table('users')->orderBy('id')->value('id');
        }
        if (empty($actor)) {
            return;
        }
        app(InstallationReferenceDataService::class)->seed($actor, '127.0.0.1');
    }

    public function down()
    {
        // Los catálogos pueden estar relacionados con transacciones clínicas o contables.
        // No se eliminan automáticamente para preservar la integridad de instalaciones activas.
    }
}
