<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDocumentTraceToV3Menu extends Migration
{
    const MENU_ID = 704;

    const ACCOUNTING_PARENT_ID = 563;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $actor = $this->actorId();
        $menu = [
            'nombre_menu' => 'Expediente contable',
            'icon_menu' => '<i class="nav-icon fas fa-project-diagram"></i>',
            'controlador_menu' => 'contabilidad',
            'funcion_menu' => 'expediente',
            'id_usuario_modificacion_menu' => $actor,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 15,
            'estado_menu' => 1,
            'id_pertenece_menu' => self::ACCOUNTING_PARENT_ID,
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
            $this->grantExistingAccountingUsers($now);
        }, 3);
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }
        DB::transaction(function () {
            if (Schema::hasTable('new_db_permisos_usuario')) {
                foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                    $ids = json_decode($permission->id_submenu_permiso, true);
                    $ids = is_array($ids) ? array_map('intval', $ids) : [];
                    $ids = array_values(array_diff($ids, [self::MENU_ID]));
                    sort($ids);
                    DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                        ->update(['id_submenu_permiso' => json_encode($ids)]);
                }
            }
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->delete();
        }, 3);
    }

    private function grantExistingAccountingUsers($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            if (! count(array_intersect([self::ACCOUNTING_PARENT_ID, 682, 701], $ids))) {
                continue;
            }
            $ids = array_values(array_unique(array_merge($ids, [577, 582, self::ACCOUNTING_PARENT_ID, self::MENU_ID])));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids), 'updated_at_permiso' => $now,
            ]);
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
