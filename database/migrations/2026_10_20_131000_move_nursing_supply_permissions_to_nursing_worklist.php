<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MoveNursingSupplyPermissionsToNursingWorklist extends Migration
{
    private $menuId = 647;

    private $module = 'insumos-usados';

    private $actions = ['view', 'create', 'update', 'delete'];

    public function up()
    {
        $now = date('Y-m-d H:i:s');
        $this->copyLegacyConfiguredRoles($now);
        $this->copyV3Roles($now);
        $this->copyCompanyRoles($now);
        $this->copyDirectUsers($now);
    }

    public function down()
    {
        // Los permisos pueden haber sido personalizados después del despliegue.
        // No se eliminan automáticamente para evitar retirar accesos legítimos.
    }

    private function copyLegacyConfiguredRoles($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario') || ! Schema::hasTable('users')
            || ! Schema::hasTable('v3_role_action_permissions')) {
            return;
        }
        $rows = DB::table('new_db_permisos_usuario as p')->join('users as u', 'u.id', '=', 'p.id_usuario_permiso')
            ->whereNotNull('p.id_empresa_permiso')
            ->get(['p.id_empresa_permiso', 'p.id_submenu_permiso', 'u.id_tipo_usuario']);
        foreach ($rows as $row) {
            $ids = json_decode($row->id_submenu_permiso, true);
            if (! is_array($ids) || ! in_array($this->menuId, array_map('intval', $ids), true)) {
                continue;
            }
            foreach ($this->actions as $action) {
                $this->insertRoleAction($row->id_empresa_permiso, $row->id_tipo_usuario, $action, $now);
            }
        }
    }

    private function copyV3Roles($now)
    {
        if (! Schema::hasTable('v3_role_menu_permissions') || ! Schema::hasTable('v3_role_action_permissions')) {
            return;
        }
        $rows = DB::table('v3_role_menu_permissions')->where('id_menu', $this->menuId)->where('permitido', 1)
            ->get(['id_empresa', 'id_tipo_usuario']);
        foreach ($rows as $row) {
            foreach ($this->actions as $action) {
                $this->insertRoleAction($row->id_empresa, $row->id_tipo_usuario, $action, $now);
            }
        }
    }

    private function copyCompanyRoles($now)
    {
        if (! Schema::hasTable('access_role_permissions')) {
            return;
        }
        $roleIds = DB::table('access_role_permissions')->where('permission_key', 'v3.menu.'.$this->menuId)
            ->pluck('role_id')->all();
        foreach ($roleIds as $roleId) {
            foreach ($this->permissionKeys() as $key) {
                if (! DB::table('access_role_permissions')->where('role_id', $roleId)->where('permission_key', $key)->exists()) {
                    DB::table('access_role_permissions')->insert(['role_id' => $roleId, 'permission_key' => $key,
                        'created_at' => $now, 'updated_at' => $now]);
                }
            }
        }
    }

    private function copyDirectUsers($now)
    {
        if (! Schema::hasTable('access_user_permissions')) {
            return;
        }
        $rows = DB::table('access_user_permissions')->where('permission_key', 'v3.menu.'.$this->menuId)
            ->where('permitido', 1)->get(['id_empresa', 'id_usuario', 'asignado_por']);
        foreach ($rows as $row) {
            foreach ($this->permissionKeys() as $key) {
                $scope = ['id_empresa' => $row->id_empresa, 'id_usuario' => $row->id_usuario, 'permission_key' => $key];
                if (! DB::table('access_user_permissions')->where($scope)->exists()) {
                    DB::table('access_user_permissions')->insert(array_merge($scope, ['permitido' => 1,
                        'asignado_por' => $row->asignado_por, 'created_at' => $now, 'updated_at' => $now]));
                }
            }
        }
    }

    private function insertRoleAction($companyId, $roleType, $action, $now)
    {
        $scope = ['id_empresa' => $companyId, 'id_tipo_usuario' => $roleType,
            'modulo' => $this->module, 'accion' => $action];
        if (! DB::table('v3_role_action_permissions')->where($scope)->exists()) {
            DB::table('v3_role_action_permissions')->insert(array_merge($scope,
                ['permitido' => 1, 'created_at' => $now, 'updated_at' => $now]));
        }
    }

    private function permissionKeys()
    {
        return array_map(function ($action) {
            return 'v3.action.'.$this->module.'.'.$action;
        }, $this->actions);
    }
}
