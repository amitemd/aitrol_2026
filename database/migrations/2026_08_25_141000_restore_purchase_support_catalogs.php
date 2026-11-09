<?php

use Illuminate\Database\Migrations\Migration;
use Sis_medico\Services\PurchaseSupportCatalogService;

class RestorePurchaseSupportCatalogs extends Migration
{
    public function up()
    {
        app(PurchaseSupportCatalogService::class)->ensure();
    }

    public function down()
    {
        // Se conservan los catálogos porque pueden estar referenciados por compras existentes.
    }
}
