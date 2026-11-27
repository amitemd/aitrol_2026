<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auditoria_eventos', function (Blueprint $tabla): void {
            $tabla->string('modulo', 32)->nullable();
            $tabla->string('usuario_nombre', 255)->nullable();
            $tabla->string('canal', 12)->nullable();
            $tabla->string('controlador', 255)->nullable();
            $tabla->string('destino', 255)->nullable();
            $tabla->unsignedInteger('duracion_ms')->nullable();
            $tabla->json('traza')->nullable();
            $tabla->index(['id_empresa', 'modulo', 'id'], 'au_empresa_modulo_id');
            $tabla->index(['id_empresa', 'created_at'], 'au_empresa_fecha');
            $tabla->index(['id_empresa', 'id_usuario', 'id'], 'au_empresa_usuario_id');
        });
    }

    public function down(): void
    {
        Schema::table('auditoria_eventos', function (Blueprint $tabla): void {
            $tabla->dropIndex('au_empresa_modulo_id');
            $tabla->dropIndex('au_empresa_fecha');
            $tabla->dropIndex('au_empresa_usuario_id');
            $tabla->dropColumn(['modulo', 'usuario_nombre', 'canal', 'controlador', 'destino', 'duracion_ms', 'traza']);
        });
    }
};
