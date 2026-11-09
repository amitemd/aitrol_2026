<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalesCancellationTrace extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ct_ventas')) {
            return;
        }
        Schema::table('ct_ventas', function (Blueprint $table) {
            if (! Schema::hasColumn('ct_ventas', 'id_asiento_reverso')) {
                $table->unsignedInteger('id_asiento_reverso')->nullable()->index();
            }
            if (! Schema::hasColumn('ct_ventas', 'motivo_anulacion')) {
                $table->text('motivo_anulacion')->nullable();
            }
            if (! Schema::hasColumn('ct_ventas', 'fecha_anulacion')) {
                $table->dateTime('fecha_anulacion')->nullable();
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('ct_ventas')) {
            return;
        }
        Schema::table('ct_ventas', function (Blueprint $table) {
            foreach (['id_asiento_reverso', 'motivo_anulacion', 'fecha_anulacion'] as $column) {
                if (Schema::hasColumn('ct_ventas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
