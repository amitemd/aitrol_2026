<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RepairCostCentersMenuVisibility extends Migration
{
    const COST_CENTER_MENU_ID = 744;

    const SRI_MENU_ID = 743;

    const ACCOUNTING_PARENT_ID = 563;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $actor = $this->actorId();
        $driver = DB::connection()->getDriverName();
        $deferUserForeignKeys = $driver === 'mysql' && Schema::hasTable('users')
            && ! DB::table('users')->where('id', $actor)->exists();
        if ($deferUserForeignKeys) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        try {
            DB::transaction(function () use ($now, $actor) {
                $this->restoreSriMenu($now, $actor);
                $this->publishCostCenterMenu($now, $actor);
                $this->grantUserPermissions($now);
                $this->grantRolePermissions($now);
            }, 3);
        } finally {
            if ($deferUserForeignKeys) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('v3_role_menu_permissions')) {
            DB::table('v3_role_menu_permissions')->where('id_menu', self::COST_CENTER_MENU_ID)->delete();
        }
        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                $ids = is_array($ids) ? array_map('intval', $ids) : [];
                $ids = array_values(array_diff($ids, [self::COST_CENTER_MENU_ID]));
                sort($ids);
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($ids)]);
            }
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', self::COST_CENTER_MENU_ID)->delete();
        }
    }

    private function restoreSriMenu($now, $actor)
    {
        $values = [
            'nombre_menu' => 'Procesos electrónicos SRI',
            'icon_menu' => '<i class="fas fa-sync-alt nav-icon"></i>',
            'controlador_menu' => 'legacy-url', 'funcion_menu' => 'sri_electronico/procesos',
            'id_usuario_modificacion_menu' => $actor, 'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1', 'orden_menu' => 6,
            'estado_menu' => 1, 'id_pertenece_menu' => 66, 'color_menu' => null,
        ];
        if (DB::table('new_db_menu')->where('id_menu', self::SRI_MENU_ID)->exists()) {
            DB::table('new_db_menu')->where('id_menu', self::SRI_MENU_ID)->update($values);
        } else {
            DB::table('new_db_menu')->insert(array_merge($values, [
                'id_menu' => self::SRI_MENU_ID, 'id_usuario_creacion_menu' => $actor,
                'created_at_menu' => $now, 'ip_creacion_menu' => '127.0.0.1',
            ]));
        }
    }

    private function publishCostCenterMenu($now, $actor)
    {
        $values = [
            'nombre_menu' => 'Centros de costos',
            'icon_menu' => '<i class="nav-icon fas fa-sitemap"></i>',
            'controlador_menu' => 'contabilidad', 'funcion_menu' => 'centros-costos',
            'id_usuario_modificacion_menu' => $actor, 'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1', 'orden_menu' => 2,
            'estado_menu' => 1, 'id_pertenece_menu' => self::ACCOUNTING_PARENT_ID, 'color_menu' => null,
        ];
        if (DB::table('new_db_menu')->where('id_menu', self::COST_CENTER_MENU_ID)->exists()) {
            DB::table('new_db_menu')->where('id_menu', self::COST_CENTER_MENU_ID)->update($values);
        } else {
            DB::table('new_db_menu')->insert(array_merge($values, [
                'id_menu' => self::COST_CENTER_MENU_ID, 'id_usuario_creacion_menu' => $actor,
                'created_at_menu' => $now, 'ip_creacion_menu' => '127.0.0.1',
            ]));
        }
    }

    private function grantUserPermissions($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        $systemsId = (string) env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            $hasAccountingAccess = count(array_intersect([self::ACCOUNTING_PARENT_ID, 568, 569, 682, 701, 704], $ids)) > 0;
            if (! $hasAccountingAccess && (string) $permission->id_usuario_permiso !== $systemsId) {
                continue;
            }
            $ids = array_values(array_unique(array_merge($ids, [
                577, 582, self::ACCOUNTING_PARENT_ID, self::COST_CENTER_MENU_ID,
            ])));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids), 'updated_at_permiso' => $now,
            ]);
        }
    }

    private function grantRolePermissions($now)
    {
        if (! Schema::hasTable('v3_role_menu_permissions')) {
            return;
        }
        $permissions = DB::table('v3_role_menu_permissions')
            ->whereIn('id_menu', [self::ACCOUNTING_PARENT_ID, 682, 701, 704])
            ->orderBy('permitido', 'desc')->get();
        $processed = [];
        foreach ($permissions as $permission) {
            $key = (string) $permission->id_empresa.'|'.(string) $permission->id_tipo_usuario;
            if (isset($processed[$key])) {
                continue;
            }
            $processed[$key] = true;
            $scope = ['id_empresa' => $permission->id_empresa,
                'id_tipo_usuario' => $permission->id_tipo_usuario, 'id_menu' => self::COST_CENTER_MENU_ID];
            $values = ['permitido' => $permission->permitido, 'updated_at' => $now];
            if (DB::table('v3_role_menu_permissions')->where($scope)->exists()) {
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
