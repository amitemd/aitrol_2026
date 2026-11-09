<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDocumentationCenterToV3Menu extends Migration
{
    const CATALOG_ID = 702;

    const PARENT_ID = 573;

    const ROUTE_PATH = 'documentacion';

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $actorId = $this->actorId();
        $menu = DB::table('new_db_menu')
            ->where('controlador_menu', 'legacy-url')
            ->where('funcion_menu', self::ROUTE_PATH)
            ->first();

        $data = [
            'nombre_menu' => 'Centro de documentación',
            'icon_menu' => '<i class="fas fa-book-open nav-icon"></i>',
            'controlador_menu' => 'legacy-url',
            'funcion_menu' => self::ROUTE_PATH,
            'id_usuario_modificacion_menu' => $actorId,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 10,
            'estado_menu' => 1,
            'id_pertenece_menu' => self::PARENT_ID,
            'color_menu' => null,
        ];

        if ($menu) {
            $menuId = (int) $menu->id_menu;
            DB::table('new_db_menu')->where('id_menu', $menuId)->update($data);
        } else {
            $insert = array_merge($data, [
                'id_usuario_creacion_menu' => $actorId,
                'created_at_menu' => $now,
                'ip_creacion_menu' => '127.0.0.1',
            ]);
            if (! DB::table('new_db_menu')->where('id_menu', self::CATALOG_ID)->exists()) {
                $insert['id_menu'] = self::CATALOG_ID;
            }
            $menuId = (int) DB::table('new_db_menu')->insertGetId($insert);
        }

        $this->grantToAllExistingUsers($menuId, $now);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $menu = DB::table('new_db_menu')
            ->where('controlador_menu', 'legacy-url')
            ->where('funcion_menu', self::ROUTE_PATH)
            ->first();
        if (! $menu) {
            return;
        }

        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                $ids = is_array($ids) ? array_map('intval', $ids) : [];
                $ids = array_values(array_diff($ids, [(int) $menu->id_menu]));
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($ids)]);
            }
        }

        DB::table('new_db_menu')->where('id_menu', $menu->id_menu)->delete();
    }

    private function grantToAllExistingUsers($menuId, $now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }

        $hasUpdatedAt = Schema::hasColumn('new_db_permisos_usuario', 'updated_at_permiso');
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            $ids = array_values(array_unique(array_merge($ids, [self::PARENT_ID, (int) $menuId])));
            sort($ids);

            $update = ['id_submenu_permiso' => json_encode($ids)];
            if ($hasUpdatedAt) {
                $update['updated_at_permiso'] = $now;
            }
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update($update);
        }
    }

    private function actorId()
    {
        if (Schema::hasTable('users')) {
            $id = DB::table('users')->where('id_tipo_usuario', 1)->value('id');
            if ($id) {
                return (string) $id;
            }
        }

        return env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
    }
}
