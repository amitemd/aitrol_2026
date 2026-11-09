<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierPaymentCancellationTrace extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ct_comprobante_egreso')) {
            return;
        }

        Schema::table('ct_comprobante_egreso', function (Blueprint $table) {
            if (! Schema::hasColumn('ct_comprobante_egreso', 'motivo_anulacion')) {
                $table->string('motivo_anulacion', 500)->nullable();
            }
            if (! Schema::hasColumn('ct_comprobante_egreso', 'id_asiento_reverso')) {
                $table->unsignedInteger('id_asiento_reverso')->nullable()->index();
            }
            if (! Schema::hasColumn('ct_comprobante_egreso', 'fecha_anulacion')) {
                $table->dateTime('fecha_anulacion')->nullable();
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('ct_comprobante_egreso')) {
            return;
        }

        Schema::table('ct_comprobante_egreso', function (Blueprint $table) {
            foreach (['fecha_anulacion', 'id_asiento_reverso', 'motivo_anulacion'] as $column) {
                if (Schema::hasColumn('ct_comprobante_egreso', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
