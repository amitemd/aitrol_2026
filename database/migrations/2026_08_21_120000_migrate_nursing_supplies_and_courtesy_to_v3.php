<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateNursingSuppliesAndCourtesyToV3 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        DB::table('new_db_menu')->where('id_menu', 663)->update(['nombre_menu' => 'Cortesía', 'icon_menu' => '<i class="fas fa-hand-holding-heart nav-icon"></i>', 'controlador_menu' => 'cortesias-pacientes', 'funcion_menu' => 'index', 'updated_at_menu' => $now]);
        DB::table('new_db_menu')->where('id_menu', 664)->update(['nombre_menu' => 'Insumos usados', 'icon_menu' => '<i class="fas fa-box-open nav-icon"></i>', 'controlador_menu' => 'insumos-usados', 'funcion_menu' => 'index', 'updated_at_menu' => $now]);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        DB::table('new_db_menu')->where('id_menu', 663)->update(['nombre_menu' => 'Cortesía', 'icon_menu' => '<i class="far fa-circle nav-icon"></i>', 'controlador_menu' => 'legacy-route', 'funcion_menu' => 'cortesia.index', 'updated_at_menu' => $now]);
        DB::table('new_db_menu')->where('id_menu', 664)->update(['nombre_menu' => 'Anestesiólogos', 'icon_menu' => '<i class="far fa-circle nav-icon"></i>', 'controlador_menu' => 'legacy-route', 'funcion_menu' => 'enfermeria.index_insumos', 'updated_at_menu' => $now]);
    }
}
