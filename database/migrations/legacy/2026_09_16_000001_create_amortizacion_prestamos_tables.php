<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmortizacionPrestamosTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('amortizacion_prestamos')) {
            Schema::create('amortizacion_prestamos', function (Blueprint $table) {
                $table->bigIncrements('id');
                // empresa.id es el RUC/código de la empresa y se maneja como texto en este proyecto.
                $table->string('id_empresa', 20);
                $table->string('institucion', 150);
                $table->string('numero_contrato', 80)->nullable();
                $table->date('fecha_desembolso');
                $table->date('fecha_primer_pago');
                $table->decimal('monto', 18, 2);
                $table->decimal('tasa_anual', 10, 6);
                $table->unsignedInteger('plazo_meses');
                $table->enum('metodo', ['frances', 'aleman']);
                $table->enum('seguro_tipo', ['ninguno', 'porcentaje_saldo', 'fijo'])->default('ninguno');
                $table->decimal('seguro_valor', 18, 6)->default(0);
                $table->string('cuenta_banco', 50);
                $table->string('cuenta_deuda', 50);
                $table->string('cuenta_interes', 50);
                $table->string('cuenta_seguro', 50)->nullable();
                $table->unsignedInteger('id_asiento_desembolso')->nullable();
                $table->unsignedInteger('id_asiento_anulacion')->nullable();
                $table->enum('estado', ['activo', 'cancelado'])->default('activo');
                $table->unsignedInteger('id_usuariocrea')->nullable();
                $table->unsignedInteger('id_usuariomod')->nullable();
                $table->timestamps();
                $table->index(['id_empresa', 'estado']);
            });
        }

        if (!Schema::hasTable('amortizacion_prestamos_detalle')) {
            Schema::create('amortizacion_prestamos_detalle', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('id_prestamo');
                $table->unsignedInteger('numero_cuota');
                $table->date('fecha_pago');
                $table->decimal('saldo_inicial', 18, 2);
                $table->decimal('cuota_base', 18, 2);
                $table->decimal('interes', 18, 2);
                $table->decimal('capital', 18, 2);
                $table->decimal('seguro', 18, 2)->default(0);
                $table->decimal('total_cuota', 18, 2);
                $table->decimal('saldo_final', 18, 2);
                $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente');
                $table->date('fecha_pago_real')->nullable();
                $table->unsignedInteger('id_asiento_pago')->nullable();
                $table->timestamps();
                $table->foreign('id_prestamo')->references('id')->on('amortizacion_prestamos')->onDelete('cascade');
                $table->unique(['id_prestamo', 'numero_cuota']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('amortizacion_prestamos_detalle');
        Schema::dropIfExists('amortizacion_prestamos');
    }
}
