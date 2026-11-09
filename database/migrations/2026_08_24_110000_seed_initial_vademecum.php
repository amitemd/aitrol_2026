<?php

use Illuminate\Database\Migrations\Migration;
use Sis_medico\Services\VademecumCatalogService;

class SeedInitialVademecum extends Migration
{
    public function up()
    {
        app(VademecumCatalogService::class)->seedCanonicalCatalog();
    }

    public function down()
    {
        // Catalogo clinico no destructivo: puede estar referenciado por recetas.
    }
}
