<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_empresas', function (Blueprint $tabla): void {
            $tabla->boolean('activo')->default(true);
        });

        Schema::create('erp_identidad_empresas', function (Blueprint $tabla): void {
            $tabla->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete()->primary();
            $tabla->longText('logo')->nullable();
            $tabla->string('logo_mime', 30)->nullable();
            $tabla->longText('certificado_cifrado')->nullable();
            $tabla->dateTime('firma_vence')->nullable();
            $tabla->dateTime('updated_at')->nullable();
        });

        Schema::create('erp_parametros_fiscales', function (Blueprint $tabla): void {
            $tabla->id();
            $tabla->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $tabla->string('concepto', 24);
            $tabla->string('codigo', 30);
            $tabla->string('nombre', 100);
            $tabla->string('unidad', 12);
            $tabla->decimal('valor', 12, 6);
            $tabla->date('vigente_desde');
            $tabla->timestamps();
            $tabla->unique(['id_empresa', 'concepto', 'codigo', 'vigente_desde'], 'erp_parametro_vigencia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_parametros_fiscales');
        Schema::dropIfExists('erp_identidad_empresas');
        Schema::table('erp_empresas', function (Blueprint $tabla): void {
            $tabla->dropColumn('activo');
        });
    }
};
