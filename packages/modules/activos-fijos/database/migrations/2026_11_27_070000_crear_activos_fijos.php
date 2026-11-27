<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('af_catalogos', function (Blueprint $table): void {
            $this->identificar($table);
            $table->string('clase', 20);
            $table->string('codigo', 40);
            $table->string('nombre', 160);
            $table->string('descripcion', 500)->nullable();
            $this->vincular($table, 'id_padre', 'af_catalogos', true);
            $table->unsignedSmallInteger('vida_util_meses')->nullable();
            $table->boolean('activo')->default(true);
            $table->unique(['id_empresa', 'clase', 'codigo']);
            $this->auditar($table);
        });

        Schema::create('af_activos', function (Blueprint $table): void {
            $this->identificar($table);
            $table->string('codigo', 40);
            $table->string('nombre', 160);
            $table->string('descripcion', 500)->nullable();
            $table->string('marca', 80)->nullable();
            $table->string('modelo', 80)->nullable();
            $table->string('serie', 100)->nullable();
            $this->vincular($table, 'id_subtipo', 'af_catalogos');
            $this->vincular($table, 'id_ubicacion', 'af_catalogos');
            $this->vincular($table, 'id_compra', 'com_documentos', true);
            $table->foreignId('id_responsable')->nullable()->constrained('users')->restrictOnDelete();
            $table->date('fecha_adquisicion');
            $table->date('inicio_depreciacion')->nullable();
            $table->unsignedSmallInteger('vida_util_meses');
            $table->unsignedBigInteger('costo');
            $table->unsignedBigInteger('valor_residual');
            $table->unsignedBigInteger('depreciado')->default(0);
            $table->date('fecha_baja')->nullable();
            $table->unique(['id_empresa', 'codigo']);
            $table->index(['id_empresa', 'fecha_adquisicion']);
            $this->auditar($table);
        });

        Schema::create('af_movimientos', function (Blueprint $table): void {
            $this->identificar($table);
            $this->vincular($table, 'id_activo', 'af_activos');
            $table->string('tipo', 20);
            $table->date('fecha');
            $table->string('motivo', 250);
            $this->vincular($table, 'id_ubicacion_anterior', 'af_catalogos', true);
            $this->vincular($table, 'id_ubicacion_destino', 'af_catalogos', true);
            $table->foreignId('id_responsable_anterior')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('id_responsable_destino')->nullable()->constrained('users')->restrictOnDelete();
            $table->index(['id_empresa', 'id_activo', 'fecha']);
            $this->auditar($table);
        });

        Schema::create('af_depreciaciones', function (Blueprint $table): void {
            $this->identificar($table);
            $this->vincular($table, 'id_activo', 'af_activos');
            $table->date('periodo');
            $table->unsignedBigInteger('importe');
            $table->unsignedBigInteger('acumulado');
            $table->unsignedBigInteger('valor_libros');
            $table->unique(['id_empresa', 'id_activo', 'periodo']);
            $this->auditar($table);
        });

        Schema::create('af_ordenes', function (Blueprint $table): void {
            $this->identificar($table);
            $this->vincular($table, 'id_activo', 'af_activos');
            $table->string('tipo', 20);
            $table->string('estado', 20);
            $table->string('detalle', 500);
            $table->date('fecha_programada');
            $table->timestamp('finalizada_at')->nullable();
            $table->string('resultado', 500)->nullable();
            $table->unsignedBigInteger('costo_mantenimiento')->nullable();
            $table->index(['id_empresa', 'estado', 'fecha_programada']);
            $this->auditar($table);
        });
    }

    public function down(): void
    {
        foreach (['af_ordenes', 'af_depreciaciones', 'af_movimientos', 'af_activos', 'af_catalogos'] as $tabla) {
            Schema::dropIfExists($tabla);
        }
    }

    private function identificar(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
        $table->unique(['id_empresa', 'id']);
    }

    private function vincular(Blueprint $table, string $columna, string $tabla, bool $opcional = false): void
    {
        $table->unsignedBigInteger($columna)->nullable($opcional);
        $table->foreign(['id_empresa', $columna])->references(['id_empresa', 'id'])->on($tabla)->restrictOnDelete();
    }

    private function auditar(Blueprint $table): void
    {
        $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    }
};
