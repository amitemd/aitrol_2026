<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureProductGroupCatalog extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_catalogo') || DB::table('new_db_catalogo')->whereNull('id_catalogo_pertenece')->where('valor', 'PRODUCT_GROUPS')->exists()) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $actor = Schema::hasTable('users') ? DB::table('users')->orderBy('id')->value('id') : null;
        DB::table('new_db_catalogo')->insert([
            'catalogo' => 'GRUPOS DE PRODUCTOS',
            'valor' => 'PRODUCT_GROUPS',
            'id_catalogo_pertenece' => null,
            'estado_catalogo' => 1,
            'id_usuariocrea' => $actor,
            'id_usuariomod' => $actor,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down()
    {
        // El catálogo puede contener grupos creados por usuarios; no se elimina al revertir.
    }
}
