<?php

use Illuminate\Database\Migrations\Migration;
use Sis_medico\Services\ClinicalEmergencyCatalogService;

class SeedEmergencyTriageCatalogs extends Migration
{
    public function up()
    {
        app(ClinicalEmergencyCatalogService::class)->ensure();
    }

    public function down()
    {
        // No se eliminan catalogos clinicos que pueden estar referenciados.
    }
}
