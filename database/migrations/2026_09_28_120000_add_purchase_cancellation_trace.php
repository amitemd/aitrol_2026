<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseCancellationTrace extends Migration
{
    public function up()
    {
        Schema::table('ct_compras', function (Blueprint $table) {
            if (! Schema::hasColumn('ct_compras', 'id_asiento_reverso')) {
                $table->unsignedInteger('id_asiento_reverso')->nullable()->index()->after('id_asiento_cabecera');
            }
            if (! Schema::hasColumn('ct_compras', 'motivo_anulacion')) {
                $table->string('motivo_anulacion', 500)->nullable()->after('estado');
            }
            if (! Schema::hasColumn('ct_compras', 'fecha_anulacion')) {
                $table->dateTime('fecha_anulacion')->nullable()->after('motivo_anulacion');
            }
        });
    }

    public function down()
    {
        Schema::table('ct_compras', function (Blueprint $table) {
            foreach (['fecha_anulacion', 'motivo_anulacion', 'id_asiento_reverso'] as $column) {
                if (Schema::hasColumn('ct_compras', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
