<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepairEmergencyMedicationExceptionIndexes extends Migration
{
    private $tableName = 'clinical_emergency_medication_exceptions';

    public function up()
    {
        if (! Schema::hasTable($this->tableName)) {
            return;
        }

        $indexes = [
            'provisional_order_id' => 'cem_exc_prov_idx',
            'administration_id' => 'cem_exc_admin_idx',
            'regularized_order_id' => 'cem_exc_reg_order_idx',
            'status' => 'cem_exc_status_idx',
            'regularization_due_at' => 'cem_exc_due_idx',
        ];

        foreach ($indexes as $column => $index) {
            if (! Schema::hasColumn($this->tableName, $column) || $this->hasIndex($index)) {
                continue;
            }
            Schema::table($this->tableName, function (Blueprint $table) use ($column, $index) {
                $table->index($column, $index);
            });
        }
    }

    public function down()
    {
        // Reparación no destructiva: no se eliminan índices clínicos al revertir este parche.
    }

    private function hasIndex($index)
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::connection()->getDatabaseName())
            ->where('table_name', $this->tableName)
            ->where('index_name', $index)
            ->exists();
    }
}
