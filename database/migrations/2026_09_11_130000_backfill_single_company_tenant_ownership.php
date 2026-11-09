<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sis_medico\Services\SingleCompanyTenantBackfillService;

class BackfillSingleCompanyTenantOwnership extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('empresa') || DB::table('empresa')->where('estado', 1)->count() !== 1) {
            return;
        }
        app(SingleCompanyTenantBackfillService::class)->run();
    }

    public function down()
    {
        // La propiedad empresarial recuperada no se elimina: volver a NULL
        // reabriría una fuga entre empresas y perdería trazabilidad.
    }
}
