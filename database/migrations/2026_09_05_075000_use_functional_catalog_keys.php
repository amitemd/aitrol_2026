<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UseFunctionalCatalogKeys extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_catalogo')) {
            return;
        }

        $this->ensureRoot('PRODUCT_GROUPS', ['GRUPOS DE PRODUCTOS', 'GRUPO DE PRODUCTOS']);
        $this->ensureRoot('VAT_PERCENTAGES', ['PORCENTAJES DE IVA', 'PORCENTAJE IVA', 'IVA']);
    }

    private function ensureRoot($key, array $names)
    {
        if (DB::table('new_db_catalogo')->whereNull('id_catalogo_pertenece')->where('valor', $key)->exists()) {
            return;
        }

        $actor = Schema::hasTable('users') ? DB::table('users')->orderBy('id')->value('id') : null;

        $root = DB::table('new_db_catalogo')->whereNull('id_catalogo_pertenece')
            ->where(function ($query) use ($names) {
                $query->whereIn(DB::raw('UPPER(catalogo)'), $names)->orWhereIn(DB::raw('UPPER(valor)'), $names);
            })->orderBy('id_catalogo')->first();

        if ($root) {
            DB::table('new_db_catalogo')->where('id_catalogo', $root->id_catalogo)->update([
                'valor' => $key,
                'id_usuariocrea' => $root->id_usuariocrea ?: $actor,
                'id_usuariomod' => $root->id_usuariomod ?: $actor,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return;
        }

        $now = date('Y-m-d H:i:s');
        DB::table('new_db_catalogo')->insert([
            'catalogo' => $key === 'PRODUCT_GROUPS' ? 'GRUPOS DE PRODUCTOS' : 'PORCENTAJES DE IVA',
            'valor' => $key, 'id_catalogo_pertenece' => null, 'estado_catalogo' => 1,
            'id_usuariocrea' => $actor, 'id_usuariomod' => $actor,
            'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1',
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    public function down()
    {
        // Las claves funcionales pueden tener hijos asociados; no se eliminan ni revierten.
    }
}
