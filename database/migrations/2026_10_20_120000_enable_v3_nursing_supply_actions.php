<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnableV3NursingSupplyActions extends Migration
{
    private $module = 'insumos-usados';

    // Los permisos se heredan de Pacientes del día - Enfermería. El módulo
    // de insumos no se publica como una opción independiente del menú.
    private $menuId = 647;

    private $actions = ['view', 'create', 'update', 'delete'];

    public function up()
    {
        $now = date('Y-m-d H:i:s');
        $this->grantConfiguredLegacyActions($now);
        $this->grantLegacyRoleActions($now);
        $this->grantCompanyRoleActions($now);
        $this->grantDirectCompanyActions($now);
    }

    private function grantConfiguredLegacyActions($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario') || ! Schema::hasTable('users')
            || ! Schema::hasTable('v3_role_action_permissions')) {
            return;
        }
        $configured = DB::table('new_db_permisos_usuario as p')->join('users as u', 'u.id', '=', 'p.id_usuario_permiso')
            ->whereNotNull('p.id_empresa_permiso')
            ->get(['p.id_empresa_permiso', 'p.id_submenu_permiso', 'u.id_tipo_usuario']);
        foreach ($configured as $item) {
            $menuIds = json_decode($item->id_submenu_permiso, true);
            if (! is_array($menuIds) || ! in_array($this->menuId, array_map('intval', $menuIds), true)) {
                continue;
            }
            foreach ($this->actions as $action) {
                $scope = ['id_empresa' => $item->id_empresa_permiso, 'id_tipo_usuario' => $item->id_tipo_usuario,
                    'modulo' => $this->module, 'accion' => $action];
                if (! DB::table('v3_role_action_permissions')->where($scope)->exists()) {
                    DB::table('v3_role_action_permissions')->insert(array_merge($scope,
                        ['permitido' => 1, 'created_at' => $now, 'updated_at' => $now]));
                }
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('v3_role_action_permissions')) {
            DB::table('v3_role_action_permissions')->where('modulo', $this->module)->whereIn('accion', $this->actions)->delete();
        }
        if (Schema::hasTable('access_role_permissions')) {
            DB::table('access_role_permissions')->whereIn('permission_key', $this->permissionKeys())->delete();
        }
        if (Schema::hasTable('access_user_permissions')) {
            DB::table('access_user_permissions')->whereIn('permission_key', $this->permissionKeys())->delete();
        }
    }

    private function grantLegacyRoleActions($now)
    {
        if (! Schema::hasTable('v3_role_menu_permissions') || ! Schema::hasTable('v3_role_action_permissions')) {
            return;
        }
        $grants = DB::table('v3_role_menu_permissions')->where('id_menu', $this->menuId)->where('permitido', 1)
            ->get(['id_empresa', 'id_tipo_usuario']);
        foreach ($grants as $grant) {
            foreach ($this->actions as $action) {
                $scope = ['id_empresa' => $grant->id_empresa, 'id_tipo_usuario' => $grant->id_tipo_usuario, 'modulo' => $this->module, 'accion' => $action];
                if (! DB::table('v3_role_action_permissions')->where($scope)->exists()) {
                    DB::table('v3_role_action_permissions')->insert(array_merge($scope, ['permitido' => 1, 'created_at' => $now, 'updated_at' => $now]));
                }
            }
        }
    }

    private function grantCompanyRoleActions($now)
    {
        if (! Schema::hasTable('access_role_permissions')) {
            return;
        }
        $roleIds = DB::table('access_role_permissions')->where('permission_key', 'v3.menu.'.$this->menuId)->pluck('role_id')->all();
        foreach ($roleIds as $roleId) {
            foreach ($this->permissionKeys() as $key) {
                if (! DB::table('access_role_permissions')->where('role_id', $roleId)->where('permission_key', $key)->exists()) {
                    DB::table('access_role_permissions')->insert(['role_id' => $roleId, 'permission_key' => $key, 'created_at' => $now, 'updated_at' => $now]);
                }
            }
        }
    }

    private function grantDirectCompanyActions($now)
    {
        if (! Schema::hasTable('access_user_permissions')) {
            return;
        }
        $grants = DB::table('access_user_permissions')->where('permission_key', 'v3.menu.'.$this->menuId)->where('permitido', 1)
            ->get(['id_empresa', 'id_usuario', 'asignado_por']);
        foreach ($grants as $grant) {
            foreach ($this->permissionKeys() as $key) {
                $scope = ['id_empresa' => $grant->id_empresa, 'id_usuario' => $grant->id_usuario, 'permission_key' => $key];
                if (! DB::table('access_user_permissions')->where($scope)->exists()) {
                    DB::table('access_user_permissions')->insert(array_merge($scope, ['permitido' => 1, 'asignado_por' => $grant->asignado_por, 'created_at' => $now, 'updated_at' => $now]));
                }
            }
        }
    }

    private function permissionKeys()
    {
        return array_map(function ($action) {
            return 'v3.action.'.$this->module.'.'.$action;
        }, $this->actions);
    }
}
