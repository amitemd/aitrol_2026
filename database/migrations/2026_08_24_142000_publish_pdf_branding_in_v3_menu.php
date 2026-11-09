<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishPdfBrandingInV3Menu extends Migration
{
    const MENU_ID = 601;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $systemsIds = $this->systemsUserIds();
        $actorId = count($systemsIds) ? $systemsIds[0] : env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        $data = [
            'nombre_menu' => 'Logos de documentos PDF',
            'icon_menu' => '<i class="fas fa-file-pdf nav-icon"></i>',
            'controlador_menu' => 'pdf-branding',
            'funcion_menu' => 'index',
            'id_usuario_modificacion_menu' => $actorId,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 2,
            'estado_menu' => 1,
            'id_pertenece_menu' => 598,
            'color_menu' => null,
        ];

        if (DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->exists()) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update($data);
        } else {
            DB::table('new_db_menu')->insert(array_merge($data, [
                'id_menu' => self::MENU_ID,
                'id_usuario_creacion_menu' => $actorId,
                'created_at_menu' => $now,
                'ip_creacion_menu' => '127.0.0.1',
            ]));
        }

        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach ($systemsIds as $systemsId) {
            $this->grantSystemsAccess($systemsId, $now);
        }
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update([
            'nombre_menu' => 'Configuraciones PDF',
            'icon_menu' => '<i class="far fa-circle nav-icon"></i>',
            'controlador_menu' => 'legacy-route',
            'funcion_menu' => 'configuraciones_pdf_index',
            'updated_at_menu' => date('Y-m-d H:i:s'),
        ]);
    }

    private function systemsUserIds()
    {
        if (! Schema::hasTable('users')) {
            return [];
        }

        return DB::table('users')->where('id_tipo_usuario', 1)->pluck('id')->map(function ($id) {
            return (string) $id;
        })->toArray();
    }

    private function grantSystemsAccess($systemsId, $now)
    {
        $rows = DB::table('new_db_permisos_usuario')->where('id_usuario_permiso', $systemsId)->get();
        if (! count($rows)) {
            $ids = DB::table('new_db_menu')->where('estado_menu', 1)->pluck('id_menu')->map(function ($id) {
                return (int) $id;
            })->toArray();
            $ids = array_values(array_unique(array_merge($ids, [577, 582, 598, self::MENU_ID])));
            sort($ids);
            DB::table('new_db_permisos_usuario')->insert([
                'id_empresa_permiso' => null,
                'id_usuario_permiso' => $systemsId,
                'id_submenu_permiso' => json_encode($ids),
                'id_usuario_creacion_permiso' => $systemsId,
                'id_usuario_modificacion_permiso' => $systemsId,
                'ip_creacion_persmiso' => '127.0.0.1',
                'ip_modificacion_permiso' => '127.0.0.1',
                'created_at_permiso' => $now,
                'updated_at_permiso' => $now,
            ]);

            return;
        }

        foreach ($rows as $row) {
            $ids = json_decode($row->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            $ids = array_values(array_unique(array_merge($ids, [577, 582, 598, self::MENU_ID])));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $row->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids),
                'id_usuario_modificacion_permiso' => $systemsId,
                'ip_modificacion_permiso' => '127.0.0.1',
                'updated_at_permiso' => $now,
            ]);
        }
    }
}
