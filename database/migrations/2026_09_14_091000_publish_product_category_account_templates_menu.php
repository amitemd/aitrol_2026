<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishProductCategoryAccountTemplatesMenu extends Migration
{
    const MENU_ID = 742;

    const GLOBAL_SETTINGS_MENU_ID = 600;

    const ACCOUNTING_SETUP_PARENT_ID = 699;

    const LEGACY_PARENT_ID = 598;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $source = DB::table('new_db_menu')->where('id_menu', self::GLOBAL_SETTINGS_MENU_ID)->first();
        $actorId = $source ? $source->id_usuario_creacion_menu : $this->actorId();
        $parentId = DB::table('new_db_menu')->where('id_menu', self::ACCOUNTING_SETUP_PARENT_ID)->exists()
            ? self::ACCOUNTING_SETUP_PARENT_ID : self::LEGACY_PARENT_ID;
        $menu = [
            'nombre_menu' => 'Cuentas de productos por categoría',
            'icon_menu' => '<i class="fas fa-layer-group nav-icon"></i>',
            'controlador_menu' => 'configuraciones-globales',
            'funcion_menu' => 'plantillas-productos',
            'id_usuario_modificacion_menu' => $actorId,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 55,
            'estado_menu' => 1,
            'id_pertenece_menu' => $parentId,
            'color_menu' => null,
        ];

        if (DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->exists()) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update($menu);
        } else {
            DB::table('new_db_menu')->insert(array_merge($menu, [
                'id_menu' => self::MENU_ID,
                'id_usuario_creacion_menu' => $actorId,
                'created_at_menu' => $now,
                'ip_creacion_menu' => '127.0.0.1',
            ]));
        }

        $this->copyUserPermissions($parentId, $now);
        $this->copyRolePermissions($now);
    }

    public function down()
    {
        if (Schema::hasTable('v3_role_menu_permissions')) {
            DB::table('v3_role_menu_permissions')->where('id_menu', self::MENU_ID)->delete();
        }
        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                $ids = is_array($ids) ? array_map('intval', $ids) : [];
                $ids = array_values(array_diff($ids, [self::MENU_ID]));
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($ids)]);
            }
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->delete();
        }
    }

    private function copyUserPermissions($parentId, $now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            if (! in_array(self::GLOBAL_SETTINGS_MENU_ID, $ids, true)) {
                continue;
            }

            $ids = array_values(array_unique(array_merge($ids, [$parentId, self::MENU_ID])));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids),
                'updated_at_permiso' => $now,
            ]);
        }
    }

    private function copyRolePermissions($now)
    {
        if (! Schema::hasTable('v3_role_menu_permissions')) {
            return;
        }
        $permissions = DB::table('v3_role_menu_permissions')->where('id_menu', self::GLOBAL_SETTINGS_MENU_ID)->get();
        foreach ($permissions as $permission) {
            $scope = [
                'id_empresa' => $permission->id_empresa,
                'id_tipo_usuario' => $permission->id_tipo_usuario,
                'id_menu' => self::MENU_ID,
            ];
            $values = ['permitido' => $permission->permitido, 'updated_at' => $now];
            $existing = DB::table('v3_role_menu_permissions')->where($scope)->first();
            if ($existing) {
                DB::table('v3_role_menu_permissions')->where($scope)->update($values);
            } else {
                DB::table('v3_role_menu_permissions')->insert(array_merge($scope, $values, ['created_at' => $now]));
            }
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
