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
        Schema::create('com_presupuestos_compra', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_sucursal');
            $table->unsignedBigInteger('id_centro_costo');
            $table->unsignedSmallInteger('anio');
            $table->unsignedBigInteger('monto');
            $table->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['id_empresa', 'id_sucursal', 'id'], 'com_presupuesto_ambito_uq');
            $table->unique(['id_empresa', 'id_sucursal', 'anio', 'id_centro_costo'], 'com_presupuesto_dimension_uq');
            $table->foreign(['id_empresa', 'id_sucursal'], 'com_presupuesto_sucursal_fk')
                ->references(['id_empresa', 'id'])->on('erp_sucursales')->restrictOnDelete();
            $table->foreign(['id_empresa', 'id_centro_costo'], 'com_presupuesto_centro_fk')
                ->references(['id_empresa', 'id'])->on('co_centros_costos')->restrictOnDelete();
        });

        Schema::create('com_solicitudes_compra', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_sucursal');
            $table->unsignedBigInteger('id_presupuesto');
            $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha');
            $table->string('motivo', 500);
            $table->string('estado', 16);
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('impuesto');
            $table->unsignedBigInteger('total');
            $table->timestamps();
            $table->unique(['id_empresa', 'id'], 'com_solicitud_empresa_id_uq');
            $table->unique(['id_empresa', 'id_sucursal', 'id'], 'com_solicitud_ambito_uq');
            $table->index(['id_empresa', 'id_sucursal', 'estado'], 'com_solicitud_estado_idx');
            $table->index(['id_presupuesto', 'estado'], 'com_solicitud_presupuesto_idx');
            $table->foreign(['id_empresa', 'id_sucursal', 'id_presupuesto'], 'com_solicitud_presupuesto_fk')
                ->references(['id_empresa', 'id_sucursal', 'id'])->on('com_presupuestos_compra')->restrictOnDelete();
        });

        Schema::create('com_lineas_solicitud_compra', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_solicitud');
            $table->unsignedBigInteger('id_producto')->nullable();
            $table->string('descripcion', 255);
            $table->unsignedBigInteger('cantidad');
            $table->unsignedBigInteger('precio');
            $table->unsignedInteger('tasa');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('impuesto');
            $table->unsignedBigInteger('total');
            $table->foreign(['id_empresa', 'id_solicitud'], 'com_linea_solicitud_fk')
                ->references(['id_empresa', 'id'])->on('com_solicitudes_compra')->restrictOnDelete();
            $table->foreign(['id_empresa', 'id_producto'], 'com_linea_solicitud_producto_fk')
                ->references(['id_empresa', 'id'])->on('inv_productos')->restrictOnDelete();
        });

        Schema::create('com_eventos_solicitud_compra', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('id_solicitud');
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->string('responsable', 255);
            $table->string('estado', 16);
            $table->string('motivo', 500)->nullable();
            $table->timestamp('created_at');
            $table->index(['id_empresa', 'id_solicitud', 'id'], 'com_eventos_solicitud_idx');
            $table->foreign(['id_empresa', 'id_solicitud'], 'com_evento_solicitud_fk')
                ->references(['id_empresa', 'id'])->on('com_solicitudes_compra')->restrictOnDelete();
        });

        Schema::table('com_documentos', function (Blueprint $table): void {
            $table->unsignedBigInteger('id_solicitud_compra')->nullable();
            $table->unique('id_solicitud_compra', 'com_documento_solicitud_uq');
            $table->foreign(['id_empresa', 'id_sucursal', 'id_solicitud_compra'], 'com_documento_solicitud_fk')
                ->references(['id_empresa', 'id_sucursal', 'id'])->on('com_solicitudes_compra')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('com_documentos', function (Blueprint $table): void {
            $table->dropForeign('com_documento_solicitud_fk');
            $table->dropUnique('com_documento_solicitud_uq');
            $table->dropColumn('id_solicitud_compra');
        });
        Schema::dropIfExists('com_eventos_solicitud_compra');
        Schema::dropIfExists('com_lineas_solicitud_compra');
        Schema::dropIfExists('com_solicitudes_compra');
        Schema::dropIfExists('com_presupuestos_compra');
    }
};
