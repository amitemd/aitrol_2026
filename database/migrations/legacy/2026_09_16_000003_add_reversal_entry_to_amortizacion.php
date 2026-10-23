<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddReversalEntryToAmortizacion extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('amortizacion_prestamos', 'id_asiento_anulacion')) {
            DB::statement("ALTER TABLE amortizacion_prestamos ADD id_asiento_anulacion INT UNSIGNED NULL AFTER id_asiento_desembolso");
        }
    }

    public function down()
    {
        if (Schema::hasColumn('amortizacion_prestamos', 'id_asiento_anulacion')) {
            DB::statement("ALTER TABLE amortizacion_prestamos DROP COLUMN id_asiento_anulacion");
        }
    }
}
