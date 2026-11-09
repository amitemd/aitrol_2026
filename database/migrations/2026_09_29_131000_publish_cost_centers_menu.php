<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishCostCentersMenu extends Migration
{
    const MENU_ID = 744;

    const ACCOUNTING_PARENT_ID = 563;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $actor = $this->actorId();
        $menu = [
            'nombre_menu' => 'Centros de costos', 'icon_menu' => '<i class="nav-icon fas fa-sitemap"></i>',
            'controlador_menu' => 'contabilidad', 'funcion_menu' => 'centros-costos',
            'id_usuario_modificacion_menu' => $actor, 'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1', 'orden_menu' => 2, 'estado_menu' => 1,
            'id_pertenece_menu' => self::ACCOUNTING_PARENT_ID, 'color_menu' => null,
        ];
        DB::transaction(function () use ($menu, $actor, $now) {
            if (DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->exists()) {
                DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update($menu);
            } else {
                DB::table('new_db_menu')->insert(array_merge($menu, [
                    'id_menu' => self::MENU_ID, 'id_usuario_creacion_menu' => $actor,
                    'created_at_menu' => $now, 'ip_creacion_menu' => '127.0.0.1',
                ]));
            }
            $this->grantAccountingUsers($now);
            $this->copyRolePermissions($now);
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

    private function grantAccountingUsers($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            if (! count(array_intersect([self::ACCOUNTING_PARENT_ID, 682, 701, 704], $ids))) {
                continue;
            }
            $ids = array_values(array_unique(array_merge($ids, [577, 582, self::ACCOUNTING_PARENT_ID, self::MENU_ID])));
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
                'id_tipo_usuario' => $permission->id_tipo_usuario, 'id_menu' => self::MENU_ID];
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
