<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MoveInvoiceUploadMenuOutOfReservedRange extends Migration
{
    private $route = 'nuevoArchivoPlano.index_archivo_factura';

    private $parent = 584;

    private $reservedId = 684;

    private $preferredMenuId = 9000;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $menu = DB::table('new_db_menu')->where('funcion_menu', $this->route)->first();
        if (! $menu || (int) $menu->id_menu !== $this->reservedId) {
            return;
        }

        $newId = DB::table('new_db_menu')->where('id_menu', $this->preferredMenuId)->exists()
            ? ((int) DB::table('new_db_menu')->max('id_menu') + 1)
            : $this->preferredMenuId;

        DB::transaction(function () use ($menu, $newId) {
            DB::table('new_db_menu')
                ->where('id_menu', $menu->id_menu)
                ->update(['id_menu' => $newId]);

            if (! Schema::hasTable('new_db_permisos_usuario')) {
                return;
            }

            DB::table('new_db_permisos_usuario')->orderBy('id_permiso')->get()->each(function ($permission) use ($newId) {
                $menuIds = json_decode($permission->id_submenu_permiso, true);
                if (! is_array($menuIds) || ! in_array($this->parent, $menuIds)) {
                    return;
                }

                // Se conserva 684 por si el usuario ya tenía acceso al catálogo
                // de especialidades y se agrega el nuevo identificador al menú
                // de liquidación correspondiente.
                $menuIds = array_values(array_unique(array_merge($menuIds, [(int) $newId])));
                DB::table('new_db_permisos_usuario')
                    ->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($menuIds)]);
            });
        });
    }

    public function down()
    {
        // El cambio corrige una colisión de IDs; no se revierte para evitar
        // reintroducir el conflicto con el menú de Especialidades.
    }
}
