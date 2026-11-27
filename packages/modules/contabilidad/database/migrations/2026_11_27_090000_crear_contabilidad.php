<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('co_cuentas', function (Blueprint $table): void {
            $this->identificar($table);
            $table->string('codigo', 40);
            $table->string('nombre', 160);
            $table->string('clase', 15);
            $this->referencia($table, 'id_padre', 'co_cuentas', true);
            $table->boolean('auxiliar')->default(true);
            $table->boolean('efectivo')->default(false);
            $table->boolean('activo')->default(true);
            $table->unique(['id_empresa', 'codigo']);
            $this->auditoria($table);
        });
        Schema::create('co_periodos', function (Blueprint $table): void {
            $this->identificar($table);
            $table->string('nombre', 120);
            $table->date('desde');
            $table->date('hasta');
            $table->timestamp('cerrado_at')->nullable();
            $table->string('motivo_cierre', 250)->nullable();
            $table->index(['id_empresa', 'desde', 'hasta']);
            $this->auditoria($table);
        });
        Schema::create('co_asientos', function (Blueprint $table): void {
            $this->identificar($table);
            $this->referencia($table, 'id_periodo', 'co_periodos');
            $this->referencia($table, 'id_reversa_de', 'co_asientos', true);
            $table->unsignedBigInteger('numero');
            $table->date('fecha');
            $table->string('concepto', 250);
            $table->string('referencia', 120)->nullable();
            $table->string('flujo', 20)->nullable();
            $table->unsignedBigInteger('total');
            $table->uuid('clave_operacion')->nullable();
            $table->string('huella', 64)->nullable();
            $table->unique(['id_empresa', 'numero']);
            $table->unique(['id_empresa', 'id_reversa_de']);
            $table->unique(['id_empresa', 'clave_operacion']);
            $table->index(['id_empresa', 'fecha', 'numero']);
            $this->auditoria($table);
        });
        Schema::create('co_lineas', function (Blueprint $table): void {
            $this->identificar($table);
            $this->referencia($table, 'id_asiento', 'co_asientos');
            $this->referencia($table, 'id_cuenta', 'co_cuentas');
            $table->unsignedBigInteger('debe');
            $table->unsignedBigInteger('haber');
            $table->string('detalle', 250)->nullable();
            $table->string('movimiento_patrimonio', 30)->nullable();
            $table->index(['id_empresa', 'id_cuenta', 'id_asiento']);
        });
    }

    public function down(): void
    {
        foreach (['co_lineas', 'co_asientos', 'co_periodos', 'co_cuentas'] as $tabla) {
            Schema::dropIfExists($tabla);
        }
    }

    private function identificar(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
        $table->unique(['id_empresa', 'id']);
    }

    private function referencia(Blueprint $table, string $campo, string $origen, bool $opcional = false): void
    {
        $table->unsignedBigInteger($campo)->nullable($opcional);
        $table->foreign(['id_empresa', $campo])->references(['id_empresa', 'id'])->on($origen)->restrictOnDelete();
    }

    private function auditoria(Blueprint $table): void
    {
        $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    }
};
