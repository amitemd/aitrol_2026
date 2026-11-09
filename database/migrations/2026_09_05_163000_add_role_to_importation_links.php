<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('log_importaciones') && ! Schema::hasColumn('log_importaciones', 'principal')) {
            Schema::table('log_importaciones', function (Blueprint $table) {
                // 0: factura de proveedor; 1: recepcion; 2: documento interno de costeo.
                $table->unsignedTinyInteger('principal')->default(0);
                $table->index(['id_import_cab', 'principal'], 'importation_link_role');
            });
        }
    }

    public function down(): void
    {
        // Se conserva la clasificacion documental para no perder trazabilidad.
    }
};
