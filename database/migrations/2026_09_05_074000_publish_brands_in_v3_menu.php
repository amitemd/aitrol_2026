<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishBrandsInV3Menu extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 241)->update(['nombre_menu' => 'Marcas', 'icon_menu' => '<i class="fas fa-tags nav-icon"></i>', 'controlador_menu' => 'legacy-url', 'funcion_menu' => 'v3/marcas', 'updated_at_menu' => date('Y-m-d H:i:s'), 'ip_modificacion_menu' => '127.0.0.1', 'estado_menu' => 1]);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 241)->where('funcion_menu', 'v3/marcas')->update(['icon_menu' => '<i class="fa fa-align-justify nav-icon"></i>', 'controlador_menu' => 'legacy-url', 'funcion_menu' => 'insumos/marcas']);
    }
}
