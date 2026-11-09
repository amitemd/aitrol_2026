<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateOrder012MenuToV3 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 641)->update([
            'controlador_menu' => 'ordenes-formato-012',
            'funcion_menu' => 'index',
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 641)->update([
            'controlador_menu' => 'legacy-route',
            'funcion_menu' => 'orden_ingresada_formato012',
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }
}
