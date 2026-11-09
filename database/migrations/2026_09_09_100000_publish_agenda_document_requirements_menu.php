<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishAgendaDocumentRequirementsMenu extends Migration
{
    const MENU_ID = 741;

    const ROOT_MENU_ID = 10;

    const CLINICAL_MENU_ID = 698;

    const INSURANCE_MENU_ID = 688;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update([
            'nombre_menu' => 'Documentos requeridos por seguro',
            'icon_menu' => '<i class="fas fa-file-upload nav-icon"></i>',
            'controlador_menu' => 'documentos-requeridos',
            'funcion_menu' => 'index',
            'id_pertenece_menu' => self::CLINICAL_MENU_ID,
            'orden_menu' => 70,
            'estado_menu' => 1,
            'updated_at_menu' => $now,
        ]);

        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        $permissions = DB::table('new_db_permisos_usuario as permission')
            ->leftJoin('users as user', 'user.id', '=', 'permission.id_usuario_permiso')
            ->select('permission.id_permiso', 'permission.id_submenu_permiso', 'user.id_tipo_usuario')
            ->get();

        foreach ($permissions as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            $canConfigure = in_array(self::INSURANCE_MENU_ID, $ids, true)
                || in_array((int) $permission->id_tipo_usuario, [1, 4, 7], true);
            if (! $canConfigure) {
                continue;
            }

            $ids = array_values(array_unique(array_merge($ids, [
                self::ROOT_MENU_ID,
                self::CLINICAL_MENU_ID,
                self::MENU_ID,
            ])));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids),
                'updated_at_permiso' => $now,
            ]);
        }
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            $ids = array_values(array_diff($ids, [self::MENU_ID]));
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids),
            ]);
        }
    }
}
