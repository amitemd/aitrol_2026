<?php

use Illuminate\Database\Migrations\Migration;
use Sis_medico\Services\CompanyConfigurationTenantService;

class RepairCompanyConfigurationOwnership extends Migration
{
    public function up()
    {
        app(CompanyConfigurationTenantService::class)->repairOrphans();
    }

    public function down()
    {
        // No se revierten asignaciones empresariales ni se recrean NULL.
    }
}
