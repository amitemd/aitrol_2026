<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetRecentCashCountDefaultBase extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('ct_arqueo_caja') || ! Schema::hasColumn('ct_arqueo_caja', 'base')) {
            return;
        }

        // Hasta este cambio los arqueos se creaban automáticamente con 30.
        // Se corrigen únicamente los registros del nuevo flujo V3; los
        // arqueos históricos se conservan sin alteraciones.
        DB::table('ct_arqueo_caja')->where('fecha_proceso', '>=', '2026-09-30')
            ->where('base', 30)->update(['base' => 0]);
    }

    public function down()
    {
        // No se restaura el valor 30 porque no es posible distinguirlo de una
        // base ingresada expresamente después de aplicada la corrección.
    }
}
