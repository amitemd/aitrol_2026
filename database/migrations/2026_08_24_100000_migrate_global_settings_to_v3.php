<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateGlobalSettingsToV3 extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        DB::table('new_db_menu')->where('id_menu', 600)->update([
            'nombre_menu' => 'Configuraciones globales',
            'icon_menu' => '<i class="fas fa-sliders-h nav-icon"></i>',
            'controlador_menu' => 'configuraciones-globales',
            'funcion_menu' => 'index',
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);

        $this->migrateConfiguredRoles();
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        DB::table('new_db_menu')->where('id_menu', 600)->update([
            'nombre_menu' => 'Configuraciones globales',
            'icon_menu' => '<i class="far fa-circle nav-icon"></i>',
            'controlador_menu' => 'legacy-route',
            'funcion_menu' => 'ctglobales.index',
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }

    private function migrateConfiguredRoles()
    {
        if (! Schema::hasTable('new_db_permisos_usuario') ||
            ! Schema::hasTable('users') ||
            ! Schema::hasTable('v3_role_action_permissions')) {
            return;
        }

        $configured = DB::table('new_db_permisos_usuario as p')
            ->join('users as u', 'u.id', '=', 'p.id_usuario_permiso')
            ->whereNotNull('p.id_empresa_permiso')
            ->whereIn('u.id_tipo_usuario', [4, 5, 20, 22])
            ->select('p.id_empresa_permiso', 'p.id_submenu_permiso', 'u.id_tipo_usuario')
            ->get();
        $now = date('Y-m-d H:i:s');

        foreach ($configured as $item) {
            $ids = json_decode($item->id_submenu_permiso, true);
            if (! is_array($ids) || ! in_array(600, array_map('intval', $ids), true)) {
                continue;
            }

            $scope = [
                'id_empresa' => $item->id_empresa_permiso,
                'id_tipo_usuario' => $item->id_tipo_usuario,
                'modulo' => 'configuraciones-globales',
                'accion' => 'update',
            ];
            if (! DB::table('v3_role_action_permissions')->where($scope)->exists()) {
                DB::table('v3_role_action_permissions')->insert(array_merge($scope, [
                    'permitido' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }
}
