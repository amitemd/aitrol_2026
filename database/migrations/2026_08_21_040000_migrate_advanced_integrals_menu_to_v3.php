<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateAdvancedIntegralsMenuToV3 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 643)->update([
            'icon_menu' => '<i class="fas fa-user-clock nav-icon"></i>',
            'controlador_menu' => 'integrales-adelantados', 'funcion_menu' => 'index',
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 643)->update([
            'icon_menu' => '<i class="far fa-circle nav-icon"></i>',
            'controlador_menu' => 'legacy-url', 'funcion_menu' => 'adelantado',
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }
}
