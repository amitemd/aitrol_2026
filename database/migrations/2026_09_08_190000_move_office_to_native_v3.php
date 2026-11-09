<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MoveOfficeToNativeV3 extends Migration
{
    public function up()
    {
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('controlador_menu', 'legacy-url')->where('funcion_menu', 'oficina')
                ->update(['funcion_menu' => 'v3/oficina']);
        }
        Cache::forget('v3:general:shared:v1');
    }

    public function down()
    {
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('controlador_menu', 'legacy-url')->where('funcion_menu', 'v3/oficina')
                ->update(['funcion_menu' => 'oficina']);
        }
        Cache::forget('v3:general:shared:v1');
    }
}
