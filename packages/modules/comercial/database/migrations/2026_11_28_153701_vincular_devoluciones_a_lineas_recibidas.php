<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('com_lineas', function (Blueprint $table): void {
            $table->unsignedBigInteger('id_linea_recepcion')->nullable();
            $table->index(['id_empresa', 'id_linea_recepcion'], 'com_lineas_devolucion_recepcion_idx');
            $table->foreign(['id_empresa', 'id_linea_recepcion'], 'com_lineas_devolucion_recepcion_fk')
                ->references(['id_empresa', 'id'])->on('com_lineas')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('com_lineas', function (Blueprint $table): void {
            $table->dropForeign('com_lineas_devolucion_recepcion_fk');
            $table->dropIndex('com_lineas_devolucion_recepcion_idx');
            $table->dropColumn('id_linea_recepcion');
        });
    }
};
