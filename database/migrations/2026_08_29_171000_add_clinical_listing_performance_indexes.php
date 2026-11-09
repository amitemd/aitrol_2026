<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddClinicalListingPerformanceIndexes extends Migration
{
    public function up()
    {
        $this->index('ho_solicitud', ['estado_paso', 'id_agenda'], 'ho_solicitud_paso_agenda_idx');
        $this->index('ho_hospitalizacion', ['estado', 'id_traspaso'], 'ho_hospitalizacion_estado_traspaso_idx');
        $this->index('ho_triaje_manchester', ['id_ho_solicitud', 'prioridad'], 'ho_triaje_solicitud_prioridad_idx');
    }

    private function index($table, array $columns, $name)
    {
        if (! Schema::hasTable($table)) {
            return;
        }
        $exists = DB::table('information_schema.statistics')->where('table_schema', DB::connection()->getDatabaseName())
            ->where('table_name', $table)->where('index_name', $name)->exists();
        if (! $exists) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns, $name) {
                $blueprint->index($columns, $name);
            });
        }
    }

    public function down() {}
}
