<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HideStandaloneNursingSuppliesMenu extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 664)->update([
            'estado_menu' => 0,
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 664)->update([
            'estado_menu' => 1,
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }
}
