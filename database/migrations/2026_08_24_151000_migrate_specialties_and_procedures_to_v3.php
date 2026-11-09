<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateSpecialtiesAndProceduresToV3 extends Migration
{
    private $menus = [
        684 => ['module' => 'especialidades', 'roles' => [4]],
        687 => ['module' => 'procedimientos', 'roles' => [4]],
    ];

    public function up()
    {
        if (Schema::hasTable('new_db_menu')) {
            foreach ($this->menus as $id => $config) {
                DB::table('new_db_menu')->where('id_menu', $id)->update([
                    'controlador_menu' => $config['module'],
                    'funcion_menu' => 'index',
                    'updated_at_menu' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        $this->migrateRolePermissions();
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', 684)->update(['controlador_menu' => 'legacy-route', 'funcion_menu' => 'especialidad.index']);
        DB::table('new_db_menu')->where('id_menu', 687)->update(['controlador_menu' => 'legacy-route', 'funcion_menu' => 'procedimiento.index']);
    }

    private function migrateRolePermissions()
    {
        if (! Schema::hasTable('v3_role_action_permissions') || ! Schema::hasTable('empresa')) {
            return;
        }
        $companies = DB::table('empresa')->where('estado', 1)->pluck('id')->toArray();
        $now = date('Y-m-d H:i:s');
        foreach ($companies as $companyId) {
            foreach ($this->menus as $menuId => $config) {
                foreach ($config['roles'] as $roleId) {
                    if (Schema::hasTable('v3_role_menu_permissions')) {
                        $menuScope = [
                            'id_empresa' => $companyId,
                            'id_tipo_usuario' => $roleId,
                            'id_menu' => $menuId,
                        ];
                        if (! DB::table('v3_role_menu_permissions')->where($menuScope)->exists()) {
                            DB::table('v3_role_menu_permissions')->insert(array_merge($menuScope, [
                                'permitido' => 1, 'created_at' => $now, 'updated_at' => $now,
                            ]));
                        }
                    }
                    $scope = [
                        'id_empresa' => $companyId,
                        'id_tipo_usuario' => $roleId,
                        'modulo' => $config['module'],
                        'accion' => 'update',
                    ];
                    if (! DB::table('v3_role_action_permissions')->where($scope)->exists()) {
                        DB::table('v3_role_action_permissions')->insert(array_merge($scope, [
                            'permitido' => 1, 'created_at' => $now, 'updated_at' => $now,
                        ]));
                    }
                }
            }
        }
    }
}
