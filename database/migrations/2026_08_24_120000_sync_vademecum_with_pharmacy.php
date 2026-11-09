<?php

use Illuminate\Database\Migrations\Migration;
use Sis_medico\Services\VademecumCatalogService;

class SyncVademecumWithPharmacy extends Migration
{
    public function up()
    {
        app(VademecumCatalogService::class)->syncOperationalCatalogs();
    }

    public function down()
    {
        // No se eliminan medicamentos, productos ni precios usados en operaciones.
    }
}
