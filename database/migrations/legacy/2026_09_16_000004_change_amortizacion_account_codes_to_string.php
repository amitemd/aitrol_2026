<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeAmortizacionAccountCodesToString extends Migration
{
    public function up()
    {
        // Los valores son códigos contables (por ejemplo 1.01.01.02.01),
        // no IDs enteros. Se conserva la información existente al ampliar el tipo.
        DB::statement("ALTER TABLE amortizacion_prestamos
            MODIFY cuenta_banco VARCHAR(50) NOT NULL,
            MODIFY cuenta_deuda VARCHAR(50) NOT NULL,
            MODIFY cuenta_interes VARCHAR(50) NOT NULL,
            MODIFY cuenta_seguro VARCHAR(50) NULL");
    }

    public function down()
    {
        // No se revierte a INT: hacerlo truncaría los códigos contables existentes.
    }
}
