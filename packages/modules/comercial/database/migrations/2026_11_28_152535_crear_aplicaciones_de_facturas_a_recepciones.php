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
            $table->unique(['id_empresa', 'id'], 'com_lineas_empresa_id_unique');
        });

        Schema::create('com_aplicaciones_recepcion', function (Blueprint $table): void {
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_factura');
            $table->unsignedBigInteger('id_linea_recepcion');
            $table->unsignedBigInteger('cantidad');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('impuesto');
            $table->primary(['id_empresa', 'id_factura', 'id_linea_recepcion'], 'com_aplicacion_recepcion_pk');
            $table->index(['id_empresa', 'id_linea_recepcion'], 'com_aplicacion_recepcion_linea_idx');
            $table->foreign(['id_empresa', 'id_factura'], 'com_aplicacion_recepcion_factura_fk')
                ->references(['id_empresa', 'id'])->on('com_documentos')->restrictOnDelete();
            $table->foreign(['id_empresa', 'id_linea_recepcion'], 'com_aplicacion_recepcion_linea_fk')
                ->references(['id_empresa', 'id'])->on('com_lineas')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('com_aplicaciones_recepcion');

        Schema::table('com_lineas', function (Blueprint $table): void {
            $table->dropUnique('com_lineas_empresa_id_unique');
        });
    }
};
