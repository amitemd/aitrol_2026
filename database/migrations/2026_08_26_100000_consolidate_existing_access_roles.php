<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConsolidateExistingAccessRoles extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('access_roles')) {
            return;
        }

        // Permite desplegar la consolidación aunque la migración base ya haya
        // sido ejecutada antes de incorporar los campos de trazabilidad.
        if (! Schema::hasColumn('access_roles', 'origen')) {
            Schema::table('access_roles', function (Blueprint $table) {
                $table->string('origen', 30)->nullable()->after('estado');
                $table->string('referencia_origen', 60)->nullable()->after('origen');
                $table->unique(['id_empresa', 'origen', 'referencia_origen'], 'access_roles_origin_uq');
            });
        }

        require_once __DIR__.'/2026_08_26_090000_create_company_access_roles.php';
        $consolidator = new CreateCompanyAccessRoles;
        $consolidator->migrateExistingAccess();
        $consolidator->consolidateLegacyMenus();
    }

    public function down()
    {
        // Los datos importados se conservan: ya pueden haber sido modificados
        // desde el panel unificado y no es seguro reconstruir su estado previo.
    }
}
