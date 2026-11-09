<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RouteDoctorScheduleMenuToV3Directory extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('new_db_menu')) return;

        DB::table('new_db_menu')->where('id_menu', 665)->update(array(
            'controlador_menu' => 'agenda-clinica',
            'funcion_menu' => 'horarios',
            'icon_menu' => '<i class="fas fa-user-clock nav-icon"></i>',
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ));
    }

    public function down()
    {
        if (!Schema::hasTable('new_db_menu')) return;

        DB::table('new_db_menu')->where('id_menu', 665)->update(array(
            'controlador_menu' => 'agenda-clinica',
            'funcion_menu' => 'index',
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ));
    }
}
