<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateLaboratoryReceptionToV3 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 671)->update([
            'nombre_menu' => 'Recepción de laboratorio', 'icon_menu' => '<i class="fas fa-flask nav-icon"></i>',
            'controlador_menu' => 'ordenes-laboratorio', 'funcion_menu' => 'index', 'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 671)->update([
            'nombre_menu' => 'Recepción', 'icon_menu' => '<i class="far fa-circle nav-icon"></i>',
            'controlador_menu' => 'legacy-route', 'funcion_menu' => 'orden.index', 'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }
}
