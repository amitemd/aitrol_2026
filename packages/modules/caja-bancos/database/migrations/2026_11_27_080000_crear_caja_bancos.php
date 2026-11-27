<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cb_cuentas', function (Blueprint $table): void {
            $this->empresa($table);
            $table->string('codigo', 40);
            $table->string('nombre', 160);
            $table->string('tipo', 10);
            $table->char('moneda', 3);
            $table->string('banco', 120)->nullable();
            $table->char('ultimos_digitos', 4)->nullable();
            $table->unsignedBigInteger('saldo')->default(0);
            $table->boolean('activo')->default(true);
            $table->unique(['id_empresa', 'codigo']);
            $this->auditoria($table);
        });

        Schema::create('cb_cierres', function (Blueprint $table): void {
            $this->empresa($table);
            $this->vinculo($table, 'id_cuenta', 'cb_cuentas');
            $table->unsignedBigInteger('saldo_apertura');
            $table->unsignedBigInteger('saldo_cierre')->nullable();
            $table->unsignedBigInteger('efectivo_contado')->nullable();
            $table->bigInteger('diferencia')->nullable();
            $table->string('observacion', 500)->nullable();
            $table->timestamp('cerrado_at')->nullable();
            $table->index(['id_empresa', 'id_cuenta', 'cerrado_at']);
            $this->auditoria($table);
        });

        Schema::create('cb_operaciones', function (Blueprint $table): void {
            $this->empresa($table);
            $table->string('tipo', 20);
            $table->date('fecha');
            $table->char('moneda', 3);
            $table->unsignedBigInteger('importe');
            $table->string('concepto', 250);
            $table->string('referencia', 120)->nullable();
            $table->uuid('clave_operacion')->nullable();
            $table->string('huella', 64)->nullable();
            $this->vinculo($table, 'id_reversa_de', 'cb_operaciones', true);
            $table->unique(['id_empresa', 'id_reversa_de']);
            $table->unique(['id_empresa', 'clave_operacion']);
            $table->index(['id_empresa', 'tipo', 'fecha']);
            $this->auditoria($table);
        });

        Schema::create('cb_asientos', function (Blueprint $table): void {
            $this->empresa($table);
            $this->vinculo($table, 'id_operacion', 'cb_operaciones');
            $this->vinculo($table, 'id_cuenta', 'cb_cuentas');
            $this->vinculo($table, 'id_cierre', 'cb_cierres', true);
            $table->bigInteger('variacion');
            $table->unsignedBigInteger('saldo_anterior');
            $table->unsignedBigInteger('saldo');
            $table->index(['id_empresa', 'id_cuenta', 'id']);
        });

        Schema::create('cb_extractos', function (Blueprint $table): void {
            $this->empresa($table);
            $this->vinculo($table, 'id_cuenta', 'cb_cuentas');
            $table->string('nombre', 160);
            $table->unsignedInteger('importadas');
            $table->unsignedInteger('omitidas');
            $this->auditoria($table);
        });

        Schema::create('cb_lineas_extracto', function (Blueprint $table): void {
            $this->empresa($table);
            $this->vinculo($table, 'id_extracto', 'cb_extractos');
            $this->vinculo($table, 'id_cuenta', 'cb_cuentas');
            $this->vinculo($table, 'id_asiento', 'cb_asientos', true);
            $table->date('fecha');
            $table->string('referencia', 120);
            $table->string('descripcion', 250);
            $table->bigInteger('importe');
            $table->unique(['id_empresa', 'id_cuenta', 'referencia'], 'cb_extracto_referencia_unica');
            $table->unique(['id_empresa', 'id_asiento']);
            $table->index(['id_empresa', 'id_cuenta', 'fecha']);
            $this->auditoria($table);
        });

        Schema::create('cb_conciliaciones', function (Blueprint $table): void {
            $this->empresa($table);
            $this->vinculo($table, 'id_linea_extracto', 'cb_lineas_extracto');
            $this->vinculo($table, 'id_asiento', 'cb_asientos');
            $table->string('accion', 16);
            $table->string('motivo', 250)->nullable();
            $this->auditoria($table);
        });
    }

    public function down(): void
    {
        foreach (['cb_conciliaciones', 'cb_lineas_extracto', 'cb_extractos', 'cb_asientos', 'cb_operaciones', 'cb_cierres', 'cb_cuentas'] as $tabla) {
            Schema::dropIfExists($tabla);
        }
    }

    private function empresa(Blueprint $table): void
    {
        $table->id();
        $table->foreignId('id_empresa')->constrained('erp_empresas')->restrictOnDelete();
        $table->unique(['id_empresa', 'id']);
    }

    private function vinculo(Blueprint $table, string $campo, string $tabla, bool $opcional = false): void
    {
        $table->unsignedBigInteger($campo)->nullable($opcional);
        $table->foreign(['id_empresa', $campo])->references(['id_empresa', 'id'])->on($tabla)->restrictOnDelete();
    }

    private function auditoria(Blueprint $table): void
    {
        $table->foreignId('id_usuario_creacion')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('id_usuario_modificacion')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    }
};
