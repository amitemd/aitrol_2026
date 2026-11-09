<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExpandProcedimientoNombreColumn extends Migration
{
    /**
     * Permite importar nombres clínicos extensos sin truncar información.
     */
    public function up()
    {
        if (Schema::hasTable('procedimiento') && Schema::hasColumn('procedimiento', 'nombre')) {
            DB::statement('ALTER TABLE `procedimiento` MODIFY `nombre` TEXT NOT NULL');
        }
    }

    /**
     * Restaura el tamaño anterior de la columna.
     */
    public function down()
    {
        if (Schema::hasTable('procedimiento') && Schema::hasColumn('procedimiento', 'nombre')) {
            DB::statement('ALTER TABLE `procedimiento` MODIFY `nombre` VARCHAR(100) NOT NULL');
        }
    }
}
