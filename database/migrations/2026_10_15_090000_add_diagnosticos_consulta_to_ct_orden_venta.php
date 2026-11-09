<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiagnosticosConsultaToCtOrdenVenta extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ct_orden_venta')
            && ! Schema::hasColumn('ct_orden_venta', 'diagnosticos_consulta')) {
            Schema::table('ct_orden_venta', function (Blueprint $table) {
                $table->mediumText('diagnosticos_consulta')->nullable()->after('observacion');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('ct_orden_venta')
            && Schema::hasColumn('ct_orden_venta', 'diagnosticos_consulta')) {
            Schema::table('ct_orden_venta', function (Blueprint $table) {
                $table->dropColumn('diagnosticos_consulta');
            });
        }
    }
}
