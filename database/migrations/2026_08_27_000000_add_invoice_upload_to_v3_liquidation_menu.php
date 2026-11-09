<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddInvoiceUploadToV3LiquidationMenu extends Migration
{
    private $route = 'nuevoArchivoPlano.index_archivo_factura';

    private $parent = 584;

    // Los IDs 684–700 ya están reservados por el catálogo base de V3.
    // Se mantiene este menú fuera de ese rango para no sustituirlos cuando
    // una instalación tenga pendientes esas migraciones históricas.
    private $preferredMenuId = 9000;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $menu = DB::table('new_db_menu')->where('funcion_menu', $this->route)->first();
        if (! $menu) {
            DB::table('new_db_menu')->where('id_pertenece_menu', $this->parent)->increment('orden_menu');
            $id = $this->availableMenuId();
            DB::table('new_db_menu')->insert([
                'id_menu' => $id,
                'nombre_menu' => 'Subir Factura Mes Plano',
                'icon_menu' => '<i class="fas fa-file-upload nav-icon"></i>',
                'controlador_menu' => 'legacy-route',
                'funcion_menu' => $this->route,
                'id_usuario_creacion_menu' => '0000000001',
                'id_usuario_modificacion_menu' => '0000000001',
                'created_at_menu' => date('Y-m-d H:i:s'),
                'updated_at_menu' => date('Y-m-d H:i:s'),
                'ip_creacion_menu' => '127.0.0.1',
                'ip_modificacion_menu' => '127.0.0.1',
                'orden_menu' => 0,
                'estado_menu' => 1,
                'id_pertenece_menu' => $this->parent,
                'color_menu' => null,
            ]);
        } else {
            $id = $menu->id_menu;
        }

        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }

        DB::table('new_db_permisos_usuario')->orderBy('id_permiso')->get()->each(function ($permission) use ($id) {
            $menuIds = json_decode($permission->id_submenu_permiso, true);
            if (! is_array($menuIds) || ! in_array($this->parent, $menuIds)) {
                return;
            }

            $menuIds = array_values(array_unique(array_merge($menuIds, [(int) $id])));
            DB::table('new_db_permisos_usuario')
                ->where('id_permiso', $permission->id_permiso)
                ->update(['id_submenu_permiso' => json_encode($menuIds)]);
        });
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $menu = DB::table('new_db_menu')->where('funcion_menu', $this->route)->first();
        if (! $menu) {
            return;
        }

        if (Schema::hasTable('new_db_permisos_usuario')) {
            DB::table('new_db_permisos_usuario')->orderBy('id_permiso')->get()->each(function ($permission) use ($menu) {
                $menuIds = json_decode($permission->id_submenu_permiso, true);
                if (! is_array($menuIds) || ! in_array($menu->id_menu, $menuIds)) {
                    return;
                }

                $menuIds = array_values(array_diff($menuIds, [(int) $menu->id_menu]));
                DB::table('new_db_permisos_usuario')
                    ->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($menuIds)]);
            });
        }

        DB::table('new_db_menu')->where('id_menu', $menu->id_menu)->delete();
        DB::table('new_db_menu')->where('id_pertenece_menu', $this->parent)->where('orden_menu', '>', 0)->decrement('orden_menu');
    }

    private function availableMenuId()
    {
        if (! DB::table('new_db_menu')->where('id_menu', $this->preferredMenuId)->exists()) {
            return $this->preferredMenuId;
        }

        return (int) DB::table('new_db_menu')->max('id_menu') + 1;
    }
}
