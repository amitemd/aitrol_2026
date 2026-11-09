<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSupplierAccountCrossingTrace extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ct_cruce_cuentas')) {
            return;
        }

        // El cruce se crea dentro de la misma transacción antes de contabilizarlo;
        // después se enlaza al asiento real. La FK se conserva y admite NULL.
        DB::statement('ALTER TABLE ct_cruce_cuentas MODIFY id_asiento_cabecera INT NULL');

        Schema::table('ct_cruce_cuentas', function (Blueprint $table) {
            if (! Schema::hasColumn('ct_cruce_cuentas', 'crossing_token')) {
                $table->string('crossing_token', 100)->nullable()->unique();
            }
            if (! Schema::hasColumn('ct_cruce_cuentas', 'saldo_base')) {
                $table->decimal('saldo_base', 18, 4)->nullable();
            }
            if (! Schema::hasColumn('ct_cruce_cuentas', 'motivo_anulacion')) {
                $table->string('motivo_anulacion', 500)->nullable();
            }
            if (! Schema::hasColumn('ct_cruce_cuentas', 'id_asiento_reverso')) {
                $table->unsignedInteger('id_asiento_reverso')->nullable()->index();
            }
            if (! Schema::hasColumn('ct_cruce_cuentas', 'fecha_anulacion')) {
                $table->dateTime('fecha_anulacion')->nullable();
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('ct_cruce_cuentas')) {
            return;
        }

        Schema::table('ct_cruce_cuentas', function (Blueprint $table) {
            foreach (['fecha_anulacion', 'id_asiento_reverso', 'motivo_anulacion', 'saldo_base', 'crossing_token'] as $column) {
                if (Schema::hasColumn('ct_cruce_cuentas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        // El rollback no debe fallar a mitad de camino si existieran cruces
        // incompletos creados durante una transacción interrumpida.
        if (! DB::table('ct_cruce_cuentas')->whereNull('id_asiento_cabecera')->exists()) {
            DB::statement('ALTER TABLE ct_cruce_cuentas MODIFY id_asiento_cabecera INT NOT NULL');
        }
    }
}
