<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishAmbulatoryDocumentsMenu extends Migration
{
    const MENU_ID = 748;

    const SOURCE_MENU_ID = 645;

    public function up(): void
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $values = [
            'nombre_menu' => 'Documentos ambulatorios',
            'icon_menu' => '<i class="fas fa-folder-open nav-icon"></i>',
            'controlador_menu' => 'documentos-ambulatorios', 'funcion_menu' => 'index',
            'id_usuario_modificacion_menu' => $this->actorId(), 'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1', 'orden_menu' => 5, 'estado_menu' => 1,
            'id_pertenece_menu' => 574, 'color_menu' => null,
        ];
        $existingMenu = DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->first();
        if ($existingMenu !== null && $existingMenu->controlador_menu !== 'documentos-ambulatorios') {
            throw new RuntimeException('El identificador de menú 748 ya corresponde a otro módulo. Revise la configuración de menús antes de continuar.');
        }
        if ($existingMenu === null) {
            DB::table('new_db_menu')->insert(array_merge($values, [
                'id_menu' => self::MENU_ID, 'id_usuario_creacion_menu' => $this->actorId(),
                'created_at_menu' => $now, 'ip_creacion_menu' => '127.0.0.1',
            ]));
        }
        $this->copyPermissions($now);
    }

    public function down(): void
    {
        // Se conservan los permisos que los administradores hayan configurado después de la instalación.
    }

    private function copyPermissions(string $now): void
    {
        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                $ids = is_array($ids) ? array_map('intval', $ids) : [];
                if (! in_array(self::SOURCE_MENU_ID, $ids, true) || in_array(self::MENU_ID, $ids, true)) {
                    continue;
                }
                $ids[] = self::MENU_ID;
                $ids = array_values(array_unique($ids));
                sort($ids);
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update(['id_submenu_permiso' => json_encode($ids)]);
            }
        }
        if (Schema::hasTable('v3_role_menu_permissions')) {
            foreach (DB::table('v3_role_menu_permissions')->where('id_menu', self::SOURCE_MENU_ID)->get() as $permission) {
                $scope = ['id_empresa' => $permission->id_empresa, 'id_tipo_usuario' => $permission->id_tipo_usuario, 'id_menu' => self::MENU_ID];
                $this->insertNewPermission('v3_role_menu_permissions', $scope, ['permitido' => $permission->permitido, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
        if (Schema::hasTable('access_role_permissions')) {
            foreach (DB::table('access_role_permissions')->where('permission_key', 'v3.menu.'.self::SOURCE_MENU_ID)->get() as $permission) {
                $this->insertNewPermission('access_role_permissions', ['role_id' => $permission->role_id, 'permission_key' => 'v3.menu.'.self::MENU_ID], ['created_at' => $now, 'updated_at' => $now]);
            }
        }
        if (Schema::hasTable('access_user_permissions')) {
            foreach (DB::table('access_user_permissions')->where('permission_key', 'v3.menu.'.self::SOURCE_MENU_ID)->get() as $permission) {
                $scope = ['id_empresa' => $permission->id_empresa, 'id_usuario' => $permission->id_usuario, 'permission_key' => 'v3.menu.'.self::MENU_ID];
                $this->insertNewPermission('access_user_permissions', $scope, ['permitido' => $permission->permitido, 'asignado_por' => $permission->asignado_por, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    private function insertNewPermission(string $table, array $scope, array $attributes): void
    {
        if (! DB::table($table)->where($scope)->exists()) {
            DB::table($table)->insert($scope + $attributes);
        }
    }

    private function actorId(): string
    {
        if (Schema::hasTable('users')) {
            $id = DB::table('users')->where('id_tipo_usuario', 1)->value('id');
            if ($id) {
                return (string) $id;
            }
        }

        return (string) env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
    }
}
