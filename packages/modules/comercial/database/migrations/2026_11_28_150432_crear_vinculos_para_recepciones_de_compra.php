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
            $table->foreignId('id_linea_orden')->nullable()->constrained('com_lineas')->restrictOnDelete();
        });

        Schema::create('com_facturas_recepciones', function (Blueprint $table): void {
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_factura');
            $table->unsignedBigInteger('id_recepcion');
            $table->primary(['id_empresa', 'id_factura', 'id_recepcion'], 'com_factura_recepcion_pk');
            $table->index(['id_empresa', 'id_recepcion'], 'com_factura_recepcion_busqueda');
            $table->foreign(['id_empresa', 'id_factura'], 'com_factura_recepcion_factura_fk')
                ->references(['id_empresa', 'id'])->on('com_documentos')->restrictOnDelete();
            $table->foreign(['id_empresa', 'id_recepcion'], 'com_factura_recepcion_recepcion_fk')
                ->references(['id_empresa', 'id'])->on('com_documentos')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('com_facturas_recepciones');
        Schema::table('com_lineas', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('id_linea_orden');
        });
    }
};
