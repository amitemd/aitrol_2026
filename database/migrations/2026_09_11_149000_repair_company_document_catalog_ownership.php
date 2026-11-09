<?php

use Illuminate\Database\Migrations\Migration;
use Sis_medico\Services\CompanyDocumentCatalogTenantService;

class RepairCompanyDocumentCatalogOwnership extends Migration
{
    public function up()
    {
        app(CompanyDocumentCatalogTenantService::class)->repairOrphans();
    }

    public function down()
    {
        // No se revierten catálogos a NULL ni se eliminan copias empresariales.
    }
}
