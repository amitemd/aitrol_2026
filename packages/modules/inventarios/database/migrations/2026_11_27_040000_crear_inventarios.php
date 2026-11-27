<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['productos', 'bodegas'] as $catalogo) {
            Schema::create('inv_'.$catalogo, function (Blueprint $table) use ($catalogo): void {
                $table->id();
                $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
                $table->string('codigo', 60);
                $table->string('nombre', 160);
                $table->string('descripcion', 500)->nullable();
                $table->boolean('activo')->default(true);
                $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
                if ($catalogo === 'productos') {
                    $table->string('unidad', 30);
                    $table->string('categoria', 100)->nullable();
                    $table->string('codigo_barras', 80)->nullable();
                    $table->unsignedBigInteger('stock_minimo')->default(0);
                    $table->unique(['id_empresa', 'codigo_barras']);
                }
                $table->unique(['id_empresa', 'id']);
                $table->unique(['id_empresa', 'codigo']);
                $table->timestamps();
            });
        }

        Schema::create('inv_existencias', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_bodega');
            $table->unsignedBigInteger('cantidad')->default(0);
            $table->unsignedBigInteger('costo_promedio')->default(0);
            $table->unique(['id_empresa', 'id_producto', 'id_bodega'], 'inv_existencias_empresa_producto_bodega');
            $this->referencia($table, 'id_producto', 'inv_productos');
            $this->referencia($table, 'id_bodega', 'inv_bodegas');
        });

        Schema::create('inv_operaciones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $table->foreignId('id_usuario')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_bodega');
            $table->unsignedBigInteger('id_bodega_destino')->nullable();
            $table->string('tipo', 24);
            $table->unsignedBigInteger('cantidad');
            $table->unsignedBigInteger('costo_unitario');
            $table->string('motivo', 250);
            $table->string('referencia', 120)->nullable();
            $table->uuid('clave_operacion')->nullable();
            $table->string('huella', 64);
            $table->timestamp('created_at');
            $table->unique(['id_empresa', 'id']);
            $table->unique(['id_empresa', 'clave_operacion']);
            $table->index(['id_empresa', 'tipo', 'id']);
            $this->referencia($table, 'id_producto', 'inv_productos');
            $this->referencia($table, 'id_bodega', 'inv_bodegas');
            $this->referencia($table, 'id_bodega_destino', 'inv_bodegas');
        });

        Schema::create('inv_kardex', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
            $table->unsignedBigInteger('id_operacion');
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_bodega');
            $table->bigInteger('variacion');
            $table->unsignedBigInteger('saldo_anterior');
            $table->unsignedBigInteger('saldo');
            $table->unsignedBigInteger('costo_unitario');
            $table->unsignedBigInteger('costo_promedio');
            $table->index(['id_empresa', 'id_producto', 'id_bodega', 'id']);
            $this->referencia($table, 'id_operacion', 'inv_operaciones');
            $this->referencia($table, 'id_producto', 'inv_productos');
            $this->referencia($table, 'id_bodega', 'inv_bodegas');
        });
    }

    public function down(): void
    {
        foreach (['inv_kardex', 'inv_operaciones', 'inv_existencias', 'inv_bodegas', 'inv_productos'] as $tabla) {
            Schema::dropIfExists($tabla);
        }
    }

    private function referencia(Blueprint $table, string $columna, string $origen): void
    {
        $table->foreign(['id_empresa', $columna])->references(['id_empresa', 'id'])->on($origen)->restrictOnDelete();
    }
};
