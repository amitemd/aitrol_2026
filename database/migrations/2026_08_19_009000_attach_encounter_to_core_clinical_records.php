<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Services\ClinicalEncounterBackfillService;

class AttachEncounterToCoreClinicalRecords extends Migration
{
    private $tables = ['agenda', 'historiaclinica', 'ho_solicitud', 'ho_form008', 'ho_log_solicitud'];

    public function up()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'encounter_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('encounter_id', 36)->nullable()->index();
                });
            }
        }

        // Idempotente: recupera episodios y referencias históricas sin alterar
        // los identificadores que aún utilizan los módulos heredados.
        app(ClinicalEncounterBackfillService::class)->run();
    }

    public function down()
    {
        foreach (array_reverse($this->tables) as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'encounter_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('encounter_id');
                });
            }
        }
    }
}
