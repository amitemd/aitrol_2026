<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateMedicalProductionToV3 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 666)->update([
            'nombre_menu' => 'Producción de médicos', 'icon_menu' => '<i class="fas fa-chart-line nav-icon"></i>',
            'controlador_menu' => 'produccion-medica', 'funcion_menu' => 'index', 'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 666)->update([
            'nombre_menu' => 'Producción de médicos', 'icon_menu' => '<i class="far fa-circle nav-icon"></i>',
            'controlador_menu' => 'legacy-route', 'funcion_menu' => 'produccion.produccion_estad', 'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }
}
