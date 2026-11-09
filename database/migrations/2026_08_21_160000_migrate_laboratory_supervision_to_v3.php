<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateLaboratorySupervisionToV3 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }DB::table('new_db_menu')->where('id_menu', 673)->update(['nombre_menu' => 'Supervisión de laboratorio', 'icon_menu' => '<i class="fas fa-chart-line nav-icon"></i>', 'controlador_menu' => 'supervision-laboratorio', 'funcion_menu' => 'index', 'updated_at_menu' => date('Y-m-d H:i:s')]);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }DB::table('new_db_menu')->where('id_menu', 673)->update(['nombre_menu' => 'Supervisión', 'icon_menu' => '<i class="far fa-circle nav-icon"></i>', 'controlador_menu' => 'legacy-route', 'funcion_menu' => 'orden.index_supervision', 'updated_at_menu' => date('Y-m-d H:i:s')]);
    }
}
