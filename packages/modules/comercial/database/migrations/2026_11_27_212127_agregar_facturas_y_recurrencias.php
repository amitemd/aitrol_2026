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
        Schema::table('com_documentos', function (Blueprint $tabla): void {
            $tabla->json('datos_factura')->nullable();
        });
        Schema::create('com_recurrencias', function (Blueprint $tabla): void {
            $tabla->id();
            $tabla->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $tabla->foreignId('id_sucursal')->constrained('erp_sucursales')->restrictOnDelete();
            $tabla->foreignId('id_documento')->unique()->constrained('com_documentos')->restrictOnDelete();
            $tabla->string('frecuencia', 12);
            $tabla->date('primera_fecha');
            $tabla->date('proxima_fecha');
            $tabla->boolean('activa')->default(true);
            $tabla->json('datos');
            $tabla->timestamps();
            $tabla->index(['activa', 'proxima_fecha']);
            $tabla->index(['id_empresa', 'id_sucursal']);
        });
        Schema::create('com_facturas_programadas', function (Blueprint $tabla): void {
            $tabla->id();
            $tabla->foreignId('id_recurrencia')->constrained('com_recurrencias')->restrictOnDelete();
            $tabla->foreignId('id_documento')->nullable()->unique()->constrained('com_documentos')->restrictOnDelete();
            $tabla->date('fecha');
            $tabla->timestamps();
            $tabla->unique(['id_recurrencia', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('com_facturas_programadas');
        Schema::dropIfExists('com_recurrencias');
        Schema::table('com_documentos', fn (Blueprint $tabla) => $tabla->dropColumn('datos_factura'));
    }
};
