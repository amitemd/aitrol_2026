<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('com_documentos', function (Blueprint $table): void {
            $table->string('estado_aprobacion', 16)->nullable();
            $table->index(['id_empresa', 'id_sucursal', 'modulo', 'tipo', 'estado_aprobacion', 'anulado_at'], 'com_documentos_aprobacion_idx');
        });

        Schema::create('com_politicas_aprobacion_compra', function (Blueprint $table): void {
            $table->foreignId('id_empresa')->primary()->constrained('erp_empresas')->restrictOnDelete();
            $table->boolean('activa');
            $table->unsignedBigInteger('monto_minimo');
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('com_decisiones_orden_compra', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_orden');
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->string('responsable', 255);
            $table->string('estado', 16);
            $table->string('motivo', 500)->nullable();
            $table->unsignedBigInteger('monto_minimo')->nullable();
            $table->timestamp('created_at');
            $table->index(['id_empresa', 'id_orden', 'id'], 'com_decisiones_orden_idx');
            $table->foreign(['id_empresa', 'id_orden'], 'com_decisiones_orden_fk')
                ->references(['id_empresa', 'id'])->on('com_documentos')->restrictOnDelete();
        });

        DB::table('erp_empresas')->select('id')->chunkById(200, function ($empresas): void {
            DB::table('permisos')->insertOrIgnore($empresas->map(fn ($empresa): array => [
                'id_empresa' => $empresa->id, 'opcion' => 'compras.aprobaciones.index', 'accion' => 'actualizar',
            ])->all());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('com_decisiones_orden_compra');
        Schema::dropIfExists('com_politicas_aprobacion_compra');
        Schema::table('com_documentos', function (Blueprint $table): void {
            $table->dropIndex('com_documentos_aprobacion_idx');
            $table->dropColumn('estado_aprobacion');
        });
    }
};
