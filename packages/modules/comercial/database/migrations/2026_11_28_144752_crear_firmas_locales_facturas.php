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
        Schema::create('com_firmas_facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $table->unsignedBigInteger('id_documento');
            $table->foreign(['id_empresa', 'id_documento'], 'com_firmas_factura_fk')
                ->references(['id_empresa', 'id'])->on('com_documentos')->restrictOnDelete();
            $table->unique(['id_empresa', 'id_documento'], 'com_firmas_factura_uq');
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('xml_firmado');
            $table->timestamp('firmado_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('com_firmas_facturas');
    }
};
