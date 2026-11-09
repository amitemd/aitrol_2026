<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateLaboratoryControlToV3 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }DB::table('new_db_menu')->where('id_menu', 672)->update(['nombre_menu' => 'Control de laboratorio', 'icon_menu' => '<i class="fas fa-microscope nav-icon"></i>', 'controlador_menu' => 'control-laboratorio', 'funcion_menu' => 'index', 'updated_at_menu' => date('Y-m-d H:i:s')]);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }DB::table('new_db_menu')->where('id_menu', 672)->update(['nombre_menu' => 'Laboratorio', 'icon_menu' => '<i class="far fa-circle nav-icon"></i>', 'controlador_menu' => 'legacy-route', 'funcion_menu' => 'orden.index_control', 'updated_at_menu' => date('Y-m-d H:i:s')]);
    }
}
