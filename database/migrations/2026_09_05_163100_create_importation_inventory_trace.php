<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('log_importaciones_movimiento')) {
            Schema::create('log_importaciones_movimiento', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_importaciones')->index();
                $table->unsignedBigInteger('id_cab_movimiento')->index();
                $table->string('id_usuariocrea', 13);
                $table->string('id_usuariomod', 13);
                $table->string('ip_creacion', 40);
                $table->string('ip_modificacion', 40);
                $table->timestamps();
                $table->unique(['id_importaciones', 'id_cab_movimiento'], 'import_inventory_trace_unique');
            });
        }
    }

    public function down(): void
    {
        // La evidencia de inventario no se elimina en una reversión del despliegue.
    }
};
