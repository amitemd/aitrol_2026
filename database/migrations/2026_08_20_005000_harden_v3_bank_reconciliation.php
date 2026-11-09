<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HardenV3BankReconciliation extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ct_conciliacion_mes') && ! $this->indexExists('ct_conciliacion_mes', 'v3_con_mes_scope_uq')) {
            if ($this->hasDuplicates('ct_conciliacion_mes', ['id_empresa', 'id_caja_banco', 'anio', 'mes', 'tipo'])) {
                throw new RuntimeException('Existen cierres de conciliacion duplicados. Resuelvalos antes de aplicar v3_con_mes_scope_uq; no se elimina informacion financiera automaticamente.');
            }
            Schema::table('ct_conciliacion_mes', function (Blueprint $table) {
                $table->unique(['id_empresa', 'id_caja_banco', 'anio', 'mes', 'tipo'], 'v3_con_mes_scope_uq');
            });
        }
        if (Schema::hasTable('ct_conciliacion_bancaria') && ! $this->indexExists('ct_conciliacion_bancaria', 'v3_con_line_scope_uq')) {
            if ($this->hasDuplicates('ct_conciliacion_bancaria', ['empresa', 'id_caja_banco', 'anio', 'mes', 'tipo', 'id_tipo'])) {
                throw new RuntimeException('Existen lineas bancarias duplicadas. Resuelvalas antes de aplicar v3_con_line_scope_uq; no se elimina informacion financiera automaticamente.');
            }
            Schema::table('ct_conciliacion_bancaria', function (Blueprint $table) {
                $table->unique(['empresa', 'id_caja_banco', 'anio', 'mes', 'tipo', 'id_tipo'], 'v3_con_line_scope_uq');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('ct_conciliacion_mes') && $this->indexExists('ct_conciliacion_mes', 'v3_con_mes_scope_uq')) {
            Schema::table('ct_conciliacion_mes', function (Blueprint $table) {
                $table->dropUnique('v3_con_mes_scope_uq');
            });
        }
        if (Schema::hasTable('ct_conciliacion_bancaria') && $this->indexExists('ct_conciliacion_bancaria', 'v3_con_line_scope_uq')) {
            Schema::table('ct_conciliacion_bancaria', function (Blueprint $table) {
                $table->dropUnique('v3_con_line_scope_uq');
            });
        }
    }

    private function indexExists($table, $index)
    {
        return count(DB::select('SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1', [$table, $index])) > 0;
    }

    private function hasDuplicates($table, array $columns)
    {
        return DB::table($table)->select($columns)->groupBy($columns)->havingRaw('COUNT(*) > 1')->limit(1)->exists();
    }
}
