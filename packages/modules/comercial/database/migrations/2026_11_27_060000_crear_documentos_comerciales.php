<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('com_documentos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $table->string('modulo', 10);
            $table->string('tipo', 24);
            $table->string('numero', 60);
            $table->unsignedBigInteger('emisor')->default(0);
            $table->date('fecha');
            $table->foreignId('id_cliente')->nullable()->constrained('erp_clientes')->restrictOnDelete();
            $table->foreignId('id_proveedor')->nullable()->constrained('erp_proveedores')->restrictOnDelete();
            $table->string('tercero', 255);
            $table->unsignedBigInteger('id_documento_origen')->nullable();
            $table->unsignedBigInteger('id_bodega')->nullable();
            $table->string('observacion', 500)->nullable();
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('impuesto');
            $table->unsignedBigInteger('total');
            $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_usuario_anulacion')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('anulado_at')->nullable();
            $table->string('motivo_anulacion', 250)->nullable();
            $table->timestamps();
            $table->unique(['id_empresa', 'modulo', 'tipo', 'emisor', 'numero'], 'com_documentos_numero_unique');
            $table->unique(['id_empresa', 'id']);
            $table->index(['id_empresa', 'modulo', 'tipo', 'fecha'], 'com_documentos_fecha_index');
            $table->foreign(['id_empresa', 'id_documento_origen'], 'com_documentos_origen_fk')->references(['id_empresa', 'id'])->on('com_documentos')->restrictOnDelete();
            $table->foreign(['id_empresa', 'id_bodega'])->references(['id_empresa', 'id'])->on('inv_bodegas')->restrictOnDelete();
        });

        Schema::create('com_lineas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $table->unsignedBigInteger('id_documento');
            $table->unsignedBigInteger('id_producto')->nullable();
            $table->unsignedBigInteger('id_operacion')->nullable();
            $table->unsignedBigInteger('id_operacion_reversa')->nullable();
            $table->string('descripcion', 255);
            $table->string('tipo_retencion', 12)->nullable();
            $table->string('codigo_retencion', 32)->nullable();
            $table->unsignedBigInteger('base_retencion')->nullable();
            $table->unsignedInteger('porcentaje_retencion')->nullable();
            $table->unsignedBigInteger('cantidad');
            $table->unsignedBigInteger('precio');
            $table->unsignedInteger('tasa');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('impuesto');
            $table->unsignedBigInteger('total');
            foreach (['id_documento' => 'com_documentos', 'id_producto' => 'inv_productos', 'id_operacion' => 'inv_operaciones', 'id_operacion_reversa' => 'inv_operaciones'] as $columna => $referencia) {
                $table->foreign(['id_empresa', $columna])->references(['id_empresa', 'id'])->on($referencia)->restrictOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('com_lineas');
        Schema::dropIfExists('com_documentos');
    }
};
