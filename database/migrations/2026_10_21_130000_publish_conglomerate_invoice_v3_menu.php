<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishConglomerateInvoiceV3Menu extends Migration
{
    const MENU_ID = 747;

    const SALES_MENU_ID = 3;

    const INVOICE_CONTAINER_ID = 29;

    const SALES_INVOICE_MENU_ID = 561;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $actor = $this->actorId();
        $menu = [
            'nombre_menu' => 'Factura conglomerada',
            'icon_menu' => '<i class="nav-icon fas fa-layer-group"></i>',
            'controlador_menu' => 'facturar',
            'funcion_menu' => 'facturaconglomerada',
            'id_usuario_modificacion_menu' => $actor,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 3,
            'estado_menu' => 1,
            'id_pertenece_menu' => self::SALES_MENU_ID,
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

            $this->grantLegacyUsers($now);
            $this->copyRolePermissions($now);
            $this->copyCompanyRolePermissions($now);
            $this->copyDirectUserPermissions($now);
        }, 3);
    }

    public function down()
    {
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
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->delete();
        }
    }

    private function sourceMenuIds()
    {
        return [self::SALES_MENU_ID, self::INVOICE_CONTAINER_ID, self::SALES_INVOICE_MENU_ID];
    }

    private function grantLegacyUsers($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }

        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            if (! count(array_intersect($this->sourceMenuIds(), $ids))) {
                continue;
            }

            // El controlador historico autoriza primero el contenedor Facturar.
            $ids = array_values(array_unique(array_merge($ids, [
                self::SALES_MENU_ID,
                self::INVOICE_CONTAINER_ID,
                self::MENU_ID,
            ])));
            sort($ids);
            $values = ['id_submenu_permiso' => json_encode($ids)];
            if (Schema::hasColumn('new_db_permisos_usuario', 'updated_at_permiso')) {
                $values['updated_at_permiso'] = $now;
            }
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update($values);
        }
    }

    private function copyRolePermissions($now)
    {
        if (! Schema::hasTable('v3_role_menu_permissions')) {
            return;
        }

        $permissions = DB::table('v3_role_menu_permissions')
            ->whereIn('id_menu', $this->sourceMenuIds())->orderBy('permitido', 'desc')->get();
        $processed = [];
        foreach ($permissions as $permission) {
            $key = (string) $permission->id_empresa.'|'.(string) $permission->id_tipo_usuario;
            if (isset($processed[$key])) {
                continue;
            }
            $processed[$key] = true;
            $scope = [
                'id_empresa' => $permission->id_empresa,
                'id_tipo_usuario' => $permission->id_tipo_usuario,
                'id_menu' => self::MENU_ID,
            ];
            $values = ['permitido' => $permission->permitido, 'updated_at' => $now];
            if (DB::table('v3_role_menu_permissions')->where($scope)->exists()) {
                DB::table('v3_role_menu_permissions')->where($scope)->update($values);
            } else {
                DB::table('v3_role_menu_permissions')->insert(array_merge($scope, $values, ['created_at' => $now]));
            }
        }
    }

    private function copyCompanyRolePermissions($now)
    {
        if (! Schema::hasTable('access_role_permissions')) {
            return;
        }

        $sourceKeys = array_map(function ($id) {
            return 'v3.menu.'.$id;
        }, $this->sourceMenuIds());
        $roleIds = DB::table('access_role_permissions')->whereIn('permission_key', $sourceKeys)
            ->distinct()->pluck('role_id');
        foreach ($roleIds as $roleId) {
            $scope = ['role_id' => $roleId, 'permission_key' => 'v3.menu.'.self::MENU_ID];
            if (! DB::table('access_role_permissions')->where($scope)->exists()) {
                DB::table('access_role_permissions')->insert(array_merge($scope, [
                    'created_at' => $now, 'updated_at' => $now,
                ]));
            }
        }
    }

    private function copyDirectUserPermissions($now)
    {
        if (! Schema::hasTable('access_user_permissions')) {
            return;
        }

        $sourceKeys = array_map(function ($id) {
            return 'v3.menu.'.$id;
        }, $this->sourceMenuIds());
        $grants = DB::table('access_user_permissions')->whereIn('permission_key', $sourceKeys)
            ->where('permitido', 1)->get();
        $processed = [];
        foreach ($grants as $grant) {
            $key = (string) $grant->id_empresa.'|'.(string) $grant->id_usuario;
            if (isset($processed[$key])) {
                continue;
            }
            $processed[$key] = true;
            $scope = [
                'id_empresa' => $grant->id_empresa,
                'id_usuario' => $grant->id_usuario,
                'permission_key' => 'v3.menu.'.self::MENU_ID,
            ];
            if (! DB::table('access_user_permissions')->where($scope)->exists()) {
                DB::table('access_user_permissions')->insert(array_merge($scope, [
                    'permitido' => 1,
                    'asignado_por' => $grant->asignado_por,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
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
            $id = DB::table('users')->value('id');
            if ($id) {
                return (string) $id;
            }
        }

        return env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
    }
}
