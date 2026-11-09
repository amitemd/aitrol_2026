<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClassifyV3FinancialAccounts extends Migration
{
    public function up()
    {
        Schema::table('plan_cuentas_empresa', function (Blueprint $table) {
            if (! Schema::hasColumn('plan_cuentas_empresa', 'categoria_niif')) {
                $table->string('categoria_niif', 20)->nullable()->index('v3_pce_niif_idx');
            }
            if (! Schema::hasColumn('plan_cuentas_empresa', 'flujo_efectivo_categoria')) {
                $table->string('flujo_efectivo_categoria', 20)->nullable()->index('v3_pce_cashflow_idx');
            }
            if (! Schema::hasColumn('plan_cuentas_empresa', 'es_efectivo')) {
                $table->boolean('es_efectivo')->default(false)->index('v3_pce_cash_idx');
            }
        });

        DB::statement("UPDATE plan_cuentas_empresa SET categoria_niif=CASE LEFT(TRIM(plan),1) WHEN '1' THEN 'ACTIVO' WHEN '2' THEN 'PASIVO' WHEN '3' THEN 'PATRIMONIO' WHEN '4' THEN 'INGRESO' WHEN '5' THEN 'COSTO' WHEN '6' THEN 'GASTO' ELSE categoria_niif END WHERE categoria_niif IS NULL");
        DB::statement("UPDATE plan_cuentas_empresa SET flujo_efectivo_categoria=CASE WHEN LEFT(TRIM(plan),1) IN ('4','5','6') THEN 'OPERACION' ELSE flujo_efectivo_categoria END WHERE flujo_efectivo_categoria IS NULL");
        DB::statement("UPDATE plan_cuentas_empresa SET es_efectivo=1 WHERE plan LIKE '1.01.01%' OR (categoria_niif='ACTIVO' AND (UPPER(nombre) LIKE 'CAJA%' OR UPPER(nombre) LIKE 'BANCO%'))");
    }

    public function down()
    {
        Schema::table('plan_cuentas_empresa', function (Blueprint $table) {
            foreach (['categoria_niif', 'flujo_efectivo_categoria', 'es_efectivo'] as $column) {
                if (Schema::hasColumn('plan_cuentas_empresa', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
