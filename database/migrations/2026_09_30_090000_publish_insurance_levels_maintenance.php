<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishInsuranceLevelsMaintenance extends Migration
{
    const MENU_ID = 746;

    const INSURANCE_MENU_ID = 688;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $actor = DB::table('users')->where('id_tipo_usuario', 1)->value('id') ?: env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        $values = ['nombre_menu' => 'Niveles de seguro', 'icon_menu' => '<i class="fas fa-layer-group nav-icon"></i>', 'controlador_menu' => 'configuraciones-globales', 'funcion_menu' => 'niveles-seguro', 'id_usuario_modificacion_menu' => $actor, 'updated_at_menu' => $now, 'ip_modificacion_menu' => '127.0.0.1', 'orden_menu' => 55, 'estado_menu' => 1, 'id_pertenece_menu' => 683, 'color_menu' => null];
        if (DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->exists()) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update($values);
        } else {
            DB::table('new_db_menu')->insert(array_merge($values, ['id_menu' => self::MENU_ID, 'id_usuario_creacion_menu' => $actor, 'created_at_menu' => $now, 'ip_creacion_menu' => '127.0.0.1']));
        }

        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                $ids = is_array($ids) ? array_map('intval', $ids) : [];
                if (! in_array(self::INSURANCE_MENU_ID, $ids, true)) {
                    continue;
                }
                $ids[] = self::MENU_ID;
                $ids = array_values(array_unique($ids));
                sort($ids);
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update(['id_submenu_permiso' => json_encode($ids), 'updated_at_permiso' => $now]);
            }
        }
        if (Schema::hasTable('v3_role_menu_permissions')) {
            foreach (DB::table('v3_role_menu_permissions')->where('id_menu', self::INSURANCE_MENU_ID)->get() as $permission) {
                $scope = ['id_empresa' => $permission->id_empresa, 'id_tipo_usuario' => $permission->id_tipo_usuario, 'id_menu' => self::MENU_ID];
                DB::table('v3_role_menu_permissions')->updateOrInsert($scope, ['permitido' => $permission->permitido, 'updated_at' => $now, 'created_at' => $now]);
            }
        }
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
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update(['id_submenu_permiso' => json_encode($ids)]);
            }
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->delete();
        }
    }
}
