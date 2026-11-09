<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddInitialLoadToV3Menu extends Migration
{
    const MENU_ID = 707;

    const SECURITY_PARENT_ID = 700;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $actor = $this->actorId();
        $menu = [
            'nombre_menu' => 'Carga inicial desde Excel',
            'icon_menu' => '<i class="nav-icon fas fa-file-import"></i>',
            'controlador_menu' => 'legacy-route',
            'funcion_menu' => 'initial-load.index',
            'id_usuario_modificacion_menu' => $actor,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 120,
            'estado_menu' => 1,
            'id_pertenece_menu' => self::SECURITY_PARENT_ID,
            'color_menu' => null,
        ];

        DB::transaction(function () use ($menu, $actor, $now) {
            if (DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->exists()) {
                DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update($menu);
            } else {
                DB::table('new_db_menu')->insert(array_merge($menu, [
                    'id_menu' => self::MENU_ID,
                    'id_usuario_creacion_menu' => $actor,
                    'created_at_menu' => $now,
                    'ip_creacion_menu' => '127.0.0.1',
                ]));
            }
            $this->restrictPermissionsToSystems($now);
        }, 3);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::transaction(function () {
            $this->removePermissionFromAllUsers();
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->delete();
        }, 3);
    }

    private function restrictPermissionsToSystems($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario') || ! Schema::hasTable('users')) {
            return;
        }
        $permissions = DB::table('new_db_permisos_usuario as permission')
            ->join('users as user', 'user.id', '=', 'permission.id_usuario_permiso')
            ->select('permission.id_permiso', 'permission.id_submenu_permiso', 'user.id_tipo_usuario')->get();
        foreach ($permissions as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            $ids = array_values(array_diff($ids, [self::MENU_ID]));
            if ((int) $permission->id_tipo_usuario === 1) {
                $ids = array_merge($ids, [10, self::SECURITY_PARENT_ID, self::MENU_ID]);
            }
            $ids = array_values(array_unique($ids));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids), 'updated_at_permiso' => $now,
            ]);
        }
    }

    private function removePermissionFromAllUsers()
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            $ids = array_values(array_diff($ids, [self::MENU_ID]));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                ->update(['id_submenu_permiso' => json_encode($ids)]);
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
