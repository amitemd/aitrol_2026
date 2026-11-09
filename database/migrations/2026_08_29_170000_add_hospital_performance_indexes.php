<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddHospitalPerformanceIndexes extends Migration
{
    public function up()
    {
        $this->index('ho_traspaso_sala008', ['estado', 'id_sala', 'fecha'], 'ho_traspaso_estado_sala_fecha_idx');
        $this->index('ho_traspaso_sala008', ['estado', 'id_paciente', 'fecha'], 'ho_traspaso_estado_paciente_fecha_idx');
        $this->index('sala', ['id_hospital', 'estado'], 'sala_hospital_estado_idx');
        $this->index('agenda', ['id_empresa', 'id_sala'], 'agenda_empresa_sala_idx');
        $this->index('hospital_producto', ['estado', 'nombre'], 'hospital_producto_estado_nombre_idx');
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

    public function down()
    {
        foreach (['ho_traspaso_sala008' => ['ho_traspaso_estado_sala_fecha_idx', 'ho_traspaso_estado_paciente_fecha_idx'], 'sala' => ['sala_hospital_estado_idx'], 'agenda' => ['agenda_empresa_sala_idx'], 'hospital_producto' => ['hospital_producto_estado_nombre_idx']] as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach ($indexes as $index) {
                Schema::table($table, function (Blueprint $blueprint) use ($index) {
                    $blueprint->dropIndex($index);
                });
            }
        }
    }
}
