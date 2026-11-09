<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddProcedureTypesToV3Menu extends Migration
{
    const MENU_ID = 696;

    const PROCEDURES_MENU_ID = 687;

    const SYSTEM_GROUP_ID = 683;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $source = DB::table('new_db_menu')->where('id_menu', self::PROCEDURES_MENU_ID)->first();
        $actorId = $source ? $source->id_usuario_creacion_menu : $this->actorId();
        $data = [
            'nombre_menu' => 'Tipos de procedimiento',
            'icon_menu' => '<i class="fas fa-layer-group nav-icon"></i>',
            'controlador_menu' => 'tipos-procedimiento',
            'funcion_menu' => 'index',
            'orden_menu' => 45,
            'estado_menu' => 1,
            'id_pertenece_menu' => self::SYSTEM_GROUP_ID,
            'id_usuario_modificacion_menu' => $actorId,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'color_menu' => null,
        ];

        if (DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->exists()) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update($data);
        } else {
            DB::table('new_db_menu')->insert(array_merge($data, [
                'id_menu' => self::MENU_ID,
                'id_usuario_creacion_menu' => $actorId,
                'created_at_menu' => $now,
                'ip_creacion_menu' => '127.0.0.1',
            ]));
        }

        $this->copyUserPermissions($now);
        $this->copyRolePermissions($now);
        $this->copyActionPermissions($now);
    }

    public function down()
    {
        if (Schema::hasTable('v3_role_menu_permissions')) {
            DB::table('v3_role_menu_permissions')->where('id_menu', self::MENU_ID)->delete();
        }
        if (Schema::hasTable('v3_role_action_permissions')) {
            DB::table('v3_role_action_permissions')->where('modulo', 'tipos-procedimiento')->delete();
        }
        if (Schema::hasTable('v3_action_permissions')) {
            DB::table('v3_action_permissions')->where('modulo', 'tipos-procedimiento')->delete();
        }
        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $menus = json_decode($permission->id_submenu_permiso, true);
                $menus = is_array($menus) ? array_map('intval', $menus) : [];
                $menus = array_values(array_diff($menus, [self::MENU_ID]));
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($menus)]);
            }
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->delete();
        }
    }

    private function copyUserPermissions($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $menus = json_decode($permission->id_submenu_permiso, true);
            $menus = is_array($menus) ? array_map('intval', $menus) : [];
            if (! in_array(self::PROCEDURES_MENU_ID, $menus, true)) {
                continue;
            }
            $menus[] = self::MENU_ID;
            $menus = array_values(array_unique($menus));
            sort($menus);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($menus), 'updated_at_permiso' => $now,
            ]);
        }
    }

    private function copyRolePermissions($now)
    {
        if (! Schema::hasTable('v3_role_menu_permissions')) {
            return;
        }
        $rows = DB::table('v3_role_menu_permissions')->where('id_menu', self::PROCEDURES_MENU_ID)->get();
        foreach ($rows as $row) {
            $scope = ['id_empresa' => $row->id_empresa, 'id_tipo_usuario' => $row->id_tipo_usuario, 'id_menu' => self::MENU_ID];
            if (! DB::table('v3_role_menu_permissions')->where($scope)->exists()) {
                DB::table('v3_role_menu_permissions')->insert(array_merge($scope, [
                    'permitido' => $row->permitido, 'created_at' => $now, 'updated_at' => $now,
                ]));
            }
        }
    }

    private function copyActionPermissions($now)
    {
        if (Schema::hasTable('v3_role_action_permissions')) {
            $rows = DB::table('v3_role_action_permissions')->where('modulo', 'procedimientos')->get();
            foreach ($rows as $row) {
                $scope = [
                    'id_empresa' => $row->id_empresa, 'id_tipo_usuario' => $row->id_tipo_usuario,
                    'modulo' => 'tipos-procedimiento', 'accion' => $row->accion,
                ];
                if (! DB::table('v3_role_action_permissions')->where($scope)->exists()) {
                    DB::table('v3_role_action_permissions')->insert(array_merge($scope, [
                        'permitido' => $row->permitido, 'otorgado_por' => $row->otorgado_por,
                        'created_at' => $now, 'updated_at' => $now,
                    ]));
                }
            }
        }

        if (Schema::hasTable('v3_action_permissions')) {
            $rows = DB::table('v3_action_permissions')->where('modulo', 'procedimientos')->get();
            foreach ($rows as $row) {
                $scope = [
                    'id_usuario' => $row->id_usuario, 'id_empresa' => $row->id_empresa,
                    'modulo' => 'tipos-procedimiento', 'accion' => $row->accion,
                ];
                if (! DB::table('v3_action_permissions')->where($scope)->exists()) {
                    DB::table('v3_action_permissions')->insert(array_merge($scope, [
                        'permitido' => $row->permitido, 'otorgado_por' => $row->otorgado_por,
                        'created_at' => $now, 'updated_at' => $now,
                    ]));
                }
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
