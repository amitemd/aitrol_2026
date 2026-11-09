<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublishWithholdingPercentagesInV3Menu extends Migration
{
    const MENU_ID = 705;

    const ACCOUNTING_SETUP_PARENT_ID = 699;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $actor = $this->actorId();
        $data = [
            'nombre_menu' => 'Porcentajes de retención',
            'icon_menu' => '<i class="nav-icon fas fa-percent"></i>',
            'controlador_menu' => 'legacy-route',
            'funcion_menu' => 'porcentaje_retencion.index',
            'id_usuario_modificacion_menu' => $actor,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 90,
            'estado_menu' => 1,
            'id_pertenece_menu' => self::ACCOUNTING_SETUP_PARENT_ID,
            'color_menu' => null,
        ];

        DB::transaction(function () use ($data, $actor, $now) {
            if (DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->exists()) {
                DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update($data);
            } else {
                DB::table('new_db_menu')->insert(array_merge($data, [
                    'id_menu' => self::MENU_ID,
                    'id_usuario_creacion_menu' => $actor,
                    'created_at_menu' => $now,
                    'ip_creacion_menu' => '127.0.0.1',
                ]));
            }

            $this->grantConfiguredUsers($now);
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

    private function grantConfiguredUsers($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario') || ! Schema::hasTable('users')) {
            return;
        }

        $permissions = DB::table('new_db_permisos_usuario as p')
            ->join('users as u', 'u.id', '=', 'p.id_usuario_permiso')
            ->whereIn('u.id_tipo_usuario', [1, 4, 5, 20, 22])
            ->select('p.id_permiso', 'p.id_submenu_permiso')
            ->get();

        foreach ($permissions as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            if (! count(array_intersect([564, self::ACCOUNTING_SETUP_PARENT_ID], $ids))) {
                continue;
            }

            $ids = array_values(array_unique(array_merge($ids, [
                10, self::ACCOUNTING_SETUP_PARENT_ID, self::MENU_ID,
            ])));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids),
                'updated_at_permiso' => $now,
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
