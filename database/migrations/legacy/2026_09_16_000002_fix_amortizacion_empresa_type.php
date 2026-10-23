<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixAmortizacionEmpresaType extends Migration
{
    public function up()
    {
        // La migración inicial creó este campo como entero, pero empresa.id es VARCHAR (RUC).
        DB::statement("ALTER TABLE amortizacion_prestamos MODIFY id_empresa VARCHAR(20) NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE amortizacion_prestamos MODIFY id_empresa INT UNSIGNED NOT NULL");
    }
}
