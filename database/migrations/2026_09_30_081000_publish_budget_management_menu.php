<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishBudgetManagementMenu extends Migration
{
    const MENU_ID = 746;

    const FINANCE_PARENT_ID = 583;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $actor = $this->actorId();
        $menu = [
            'nombre_menu' => 'Presupuesto integral',
            'icon_menu' => '<i class="nav-icon fas fa-chart-pie"></i>',
            'controlador_menu' => 'finanzas',
            'funcion_menu' => 'presupuesto',
            'id_usuario_modificacion_menu' => $actor,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 0,
            'estado_menu' => 1,
            'id_pertenece_menu' => self::FINANCE_PARENT_ID,
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
            $this->grantUsers($now);
            $this->copyRolePermissions($now);
            $this->copyActionPermissions($now);
            $this->copyCompanyRolePermissions($now);
            $this->copyDirectUserPermissions($now);
        }, 3);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        if (Schema::hasTable('v3_role_menu_permissions')) {
            DB::table('v3_role_menu_permissions')->where('id_menu', self::MENU_ID)->delete();
        }
        if (Schema::hasTable('access_role_permissions')) {
            DB::table('access_role_permissions')->where('permission_key', 'v3.menu.'.self::MENU_ID)->delete();
        }
        if (Schema::hasTable('access_user_permissions')) {
            DB::table('access_user_permissions')->where('permission_key', 'v3.menu.'.self::MENU_ID)->delete();
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
        DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->delete();
    }

    private function grantUsers($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            if (! count(array_intersect([577, self::FINANCE_PARENT_ID, 563, 744], $ids))) {
                continue;
            }
            $ids = array_values(array_unique(array_merge($ids, [577, self::FINANCE_PARENT_ID, self::MENU_ID])));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids), 'updated_at_permiso' => $now,
            ]);
        }
    }

    private function copyRolePermissions($now)
    {
        if (! Schema::hasTable('v3_role_menu_permissions')) {
            return;
        }
        $permissions = DB::table('v3_role_menu_permissions')
            ->whereIn('id_menu', [self::FINANCE_PARENT_ID, 563, 744])->orderBy('permitido', 'desc')->get();
        $processed = [];
        foreach ($permissions as $permission) {
            $key = (string) $permission->id_empresa.'|'.(string) $permission->id_tipo_usuario;
            if (isset($processed[$key])) {
                continue;
            }
            $processed[$key] = true;
            $scope = ['id_empresa' => $permission->id_empresa,
                'id_tipo_usuario' => $permission->id_tipo_usuario, 'id_menu' => self::MENU_ID];
            $values = ['permitido' => $permission->permitido, 'updated_at' => $now];
            if (DB::table('v3_role_menu_permissions')->where($scope)->exists()) {
                DB::table('v3_role_menu_permissions')->where($scope)->update($values);
            } else {
                DB::table('v3_role_menu_permissions')->insert(array_merge($scope, $values, ['created_at' => $now]));
            }
        }
    }

    private function copyActionPermissions($now)
    {
        if (! Schema::hasTable('v3_role_action_permissions') || ! Schema::hasTable('v3_role_menu_permissions')) {
            return;
        }
        $roles = DB::table('v3_role_menu_permissions')
            ->whereIn('id_menu', [self::FINANCE_PARENT_ID, 563, 744])->where('permitido', 1)
            ->select('id_empresa', 'id_tipo_usuario')->distinct()->get();
        foreach ($roles as $role) {
            foreach (['view', 'create', 'update', 'delete', 'approve', 'export'] as $action) {
                $allowed = DB::table('v3_role_action_permissions')
                    ->where('id_empresa', $role->id_empresa)->where('id_tipo_usuario', $role->id_tipo_usuario)
                    ->whereIn('modulo', ['contabilidad', 'financiero', 'finanzas'])
                    ->where('accion', $action)->where('permitido', 1)->exists();
                if (! $allowed && $action !== 'view') {
                    continue;
                }
                $scope = ['id_empresa' => $role->id_empresa, 'id_tipo_usuario' => $role->id_tipo_usuario,
                    'modulo' => 'finanzas', 'accion' => $action];
                if (! DB::table('v3_role_action_permissions')->where($scope)->exists()) {
                    DB::table('v3_role_action_permissions')->insert(array_merge($scope, [
                        'permitido' => 1, 'created_at' => $now, 'updated_at' => $now,
                    ]));
                }
            }
        }
    }

    private function copyCompanyRolePermissions($now)
    {
        if (! Schema::hasTable('access_role_permissions')) {
            return;
        }
        $sourceMenuKeys = ['v3.menu.'.self::FINANCE_PARENT_ID, 'v3.menu.563', 'v3.menu.744'];
        $roleIds = DB::table('access_role_permissions')->whereIn('permission_key', $sourceMenuKeys)
            ->distinct()->pluck('role_id');
        foreach ($roleIds as $roleId) {
            $this->insertAccessRolePermission($roleId, 'v3.menu.'.self::MENU_ID, $now);
            foreach (['view', 'create', 'update', 'delete', 'approve', 'export'] as $action) {
                $sourceKeys = ['v3.action.contabilidad.'.$action, 'v3.action.financiero.'.$action,
                    'v3.action.finanzas.'.$action];
                if ($action !== 'view' && ! DB::table('access_role_permissions')
                    ->where('role_id', $roleId)->whereIn('permission_key', $sourceKeys)->exists()) {
                    continue;
                }
                $this->insertAccessRolePermission($roleId, 'v3.action.finanzas.'.$action, $now);
            }
        }
    }

    private function copyDirectUserPermissions($now)
    {
        if (! Schema::hasTable('access_user_permissions')) {
            return;
        }
        $sourceMenuKeys = ['v3.menu.'.self::FINANCE_PARENT_ID, 'v3.menu.563', 'v3.menu.744'];
        $grants = DB::table('access_user_permissions')->whereIn('permission_key', $sourceMenuKeys)
            ->where('permitido', 1)->get();
        $processed = [];
        foreach ($grants as $grant) {
            $key = $grant->id_empresa.'|'.$grant->id_usuario;
            if (isset($processed[$key])) {
                continue;
            }
            $processed[$key] = true;
            $this->insertAccessUserPermission($grant->id_empresa, $grant->id_usuario,
                'v3.menu.'.self::MENU_ID, $grant->asignado_por, $now);
            foreach (['view', 'create', 'update', 'delete', 'approve', 'export'] as $action) {
                $sourceKeys = ['v3.action.contabilidad.'.$action, 'v3.action.financiero.'.$action,
                    'v3.action.finanzas.'.$action];
                if ($action !== 'view' && ! DB::table('access_user_permissions')
                    ->where('id_empresa', $grant->id_empresa)->where('id_usuario', $grant->id_usuario)
                    ->whereIn('permission_key', $sourceKeys)->where('permitido', 1)->exists()) {
                    continue;
                }
                $this->insertAccessUserPermission($grant->id_empresa, $grant->id_usuario,
                    'v3.action.finanzas.'.$action, $grant->asignado_por, $now);
            }
        }
    }

    private function insertAccessRolePermission($roleId, $key, $now)
    {
        if (! DB::table('access_role_permissions')->where('role_id', $roleId)->where('permission_key', $key)->exists()) {
            DB::table('access_role_permissions')->insert(['role_id' => $roleId, 'permission_key' => $key,
                'created_at' => $now, 'updated_at' => $now]);
        }
    }

    private function insertAccessUserPermission($companyId, $userId, $key, $actor, $now)
    {
        $scope = ['id_empresa' => $companyId, 'id_usuario' => $userId, 'permission_key' => $key];
        if (! DB::table('access_user_permissions')->where($scope)->exists()) {
            DB::table('access_user_permissions')->insert(array_merge($scope, ['permitido' => 1,
                'asignado_por' => $actor, 'created_at' => $now, 'updated_at' => $now]));
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
