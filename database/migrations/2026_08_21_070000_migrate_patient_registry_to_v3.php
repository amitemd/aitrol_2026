<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigratePatientRegistryToV3 extends Migration
{
    public function up()
    {
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', 646)->update([
                'icon_menu' => '<i class="fas fa-users nav-icon"></i>',
                'controlador_menu' => 'pacientes', 'funcion_menu' => 'index',
                'updated_at_menu' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', 646)->update([
                'icon_menu' => '<i class="far fa-circle nav-icon"></i>',
                'controlador_menu' => 'legacy-url', 'funcion_menu' => 'paciente',
                'updated_at_menu' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
