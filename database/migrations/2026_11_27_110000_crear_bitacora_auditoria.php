<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria_eventos', function (Blueprint $tabla): void {
            $tabla->id();
            $tabla->unsignedBigInteger('id_empresa')->nullable();
            $tabla->unsignedBigInteger('id_usuario')->nullable();
            $tabla->string('nivel', 12);
            $tabla->string('origen', 15);
            $tabla->string('evento', 180);
            $tabla->uuid('solicitud');
            $tabla->string('ruta', 150)->nullable();
            $tabla->string('metodo', 12);
            $tabla->unsignedSmallInteger('estado_http');
            $tabla->timestamp('created_at');
            $tabla->index(['id_empresa', 'id']);
            $tabla->index(['id_empresa', 'nivel', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_eventos');
    }
};
