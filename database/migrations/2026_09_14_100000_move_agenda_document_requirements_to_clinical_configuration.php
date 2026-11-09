<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MoveAgendaDocumentRequirementsToClinicalConfiguration extends Migration
{
    const MENU_ID = 741;

    const CONFIGURATION_ROOT_ID = 10;

    const CLINICAL_CONFIGURATION_ID = 698;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update([
            'nombre_menu' => 'Documentos requeridos por seguro',
            'id_pertenece_menu' => self::CLINICAL_CONFIGURATION_ID,
            'orden_menu' => 70,
            'updated_at_menu' => $now,
        ]);

        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            if (! in_array(self::MENU_ID, $ids, true)) {
                continue;
            }

            $ids = array_values(array_unique(array_merge($ids, [
                self::CONFIGURATION_ROOT_ID,
                self::CLINICAL_CONFIGURATION_ID,
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
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update([
            'nombre_menu' => 'Documentos requeridos',
            'id_pertenece_menu' => self::CONFIGURATION_ROOT_ID,
            'orden_menu' => 55,
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }
}
