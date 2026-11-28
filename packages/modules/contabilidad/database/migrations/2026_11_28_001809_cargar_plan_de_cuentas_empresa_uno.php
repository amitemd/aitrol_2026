<?php

use App\Services\PlanCuentasInicial;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function (): void {
            $empresa = DB::table('erp_empresas')->where('id', 1)->whereNull('deleted_at')->lockForUpdate()->value('id');
            if ($empresa !== null) {
                app(PlanCuentasInicial::class)->copiarA((int) $empresa);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Las cuentas cargadas pueden estar vinculadas a asientos y se conservan al revertir.
    }
};
