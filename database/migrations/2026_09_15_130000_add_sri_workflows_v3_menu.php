<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSriWorkflowsV3Menu extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $actor = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        $data = [
            'nombre_menu' => 'Procesos electrónicos SRI',
            'icon_menu' => '<i class="fas fa-sync-alt nav-icon"></i>',
            'controlador_menu' => 'legacy-url',
            'funcion_menu' => 'sri_electronico/procesos',
            'id_usuario_modificacion_menu' => $actor,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 6,
            'estado_menu' => 1,
            'id_pertenece_menu' => 66,
            'color_menu' => null,
        ];

        $menu = DB::table('new_db_menu')
            ->where('controlador_menu', 'legacy-url')
            ->where('funcion_menu', 'sri_electronico/procesos')
            ->first();

        if ($menu) {
            $id = $menu->id_menu;
            DB::table('new_db_menu')->where('id_menu', $id)->update($data);
        } else {
            $data = array_merge($data, [
                'id_menu' => 743,
                'id_usuario_creacion_menu' => $actor,
                'created_at_menu' => $now,
                'ip_creacion_menu' => '127.0.0.1',
            ]);
            $id = DB::table('new_db_menu')->insertGetId($data);
        }

        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                $ids = is_array($ids) ? array_map('intval', $ids) : [];
                $ids = array_values(array_unique(array_merge($ids, [66, (int) $id])));
                sort($ids);
                DB::table('new_db_permisos_usuario')
                    ->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($ids)]);
            }
        }
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $menu = DB::table('new_db_menu')
            ->where('controlador_menu', 'legacy-url')
            ->where('funcion_menu', 'sri_electronico/procesos')
            ->first();
        if (! $menu) {
            return;
        }

        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                $ids = is_array($ids) ? array_map('intval', $ids) : [];
                $ids = array_values(array_diff($ids, [(int) $menu->id_menu]));
                DB::table('new_db_permisos_usuario')
                    ->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($ids)]);
            }
        }

        DB::table('new_db_menu')->where('id_menu', $menu->id_menu)->delete();
    }
}
