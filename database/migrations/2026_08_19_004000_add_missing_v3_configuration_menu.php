<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMissingV3ConfigurationMenu extends Migration
{
    private $menuItems = [
        ['id_menu' => 570, 'nombre_menu' => 'Bodegas', 'icon_menu' => '<i class="fas fa-warehouse nav-icon"></i>', 'funcion_menu' => 'bodegas', 'orden_menu' => 8],
        ['id_menu' => 571, 'nombre_menu' => 'Planes', 'icon_menu' => '<i class="fas fa-layer-group nav-icon"></i>', 'funcion_menu' => 'planes', 'orden_menu' => 9],
        ['id_menu' => 572, 'nombre_menu' => 'Selección de plan', 'icon_menu' => '<i class="fas fa-check-square nav-icon"></i>', 'funcion_menu' => 'selecionarplan', 'orden_menu' => 10],
    ];

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $systemsId = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        $now = date('Y-m-d H:i:s');
        $menuIds = [];
        $driver = DB::connection()->getDriverName();
        $deferUserForeignKeys = $driver === 'mysql'
            && Schema::hasTable('users')
            && ! DB::table('users')->where('id', $systemsId)->exists();

        // En una instalación nueva el menú se migra antes de crear los usuarios
        // iniciales. Se aplaza únicamente la comprobación FK de esta conexión;
        // BootstrapUsersSeeder creará el usuario configurado en la etapa seed.
        if ($deferUserForeignKeys) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        try {
            foreach ($this->menuItems as $item) {
                $existing = DB::table('new_db_menu')
                    ->where('controlador_menu', 'configuraciones')
                    ->where('funcion_menu', $item['funcion_menu'])
                    ->first();

                if ($existing) {
                    $menuIds[] = (int) $existing->id_menu;

                    continue;
                }

                if (DB::table('new_db_menu')->where('id_menu', $item['id_menu'])->exists()) {
                    continue;
                }

                DB::table('new_db_menu')->insert([
                    'id_menu' => $item['id_menu'],
                    'nombre_menu' => $item['nombre_menu'],
                    'icon_menu' => $item['icon_menu'],
                    'controlador_menu' => 'configuraciones',
                    'funcion_menu' => $item['funcion_menu'],
                    'id_usuario_creacion_menu' => $systemsId,
                    'id_usuario_modificacion_menu' => $systemsId,
                    'created_at_menu' => $now,
                    'updated_at_menu' => $now,
                    'ip_creacion_menu' => '127.0.0.1',
                    'ip_modificacion_menu' => '127.0.0.1',
                    'orden_menu' => $item['orden_menu'],
                    'estado_menu' => 1,
                    'id_pertenece_menu' => 10,
                    'color_menu' => null,
                ]);

                $menuIds[] = (int) $item['id_menu'];
            }

            $this->grantSystemsAccess($systemsId, $menuIds, $now);
        } finally {
            if ($deferUserForeignKeys) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }
    }

    public function down()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $systemsId = env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        $ids = [];

        foreach ($this->menuItems as $item) {
            $row = DB::table('new_db_menu')
                ->where('id_menu', $item['id_menu'])
                ->where('controlador_menu', 'configuraciones')
                ->where('funcion_menu', $item['funcion_menu'])
                ->first();

            if ($row) {
                $ids[] = (int) $row->id_menu;
                DB::table('new_db_menu')->where('id_menu', $row->id_menu)->delete();
            }
        }

        if (! Schema::hasTable('new_db_permisos_usuario') || ! count($ids)) {
            return;
        }

        $rows = DB::table('new_db_permisos_usuario')
            ->where('id_usuario_permiso', $systemsId)
            ->get();

        foreach ($rows as $row) {
            $current = json_decode($row->id_submenu_permiso, true);
            $current = is_array($current) ? array_map('intval', $current) : [];
            $current = array_values(array_diff($current, $ids));

            DB::table('new_db_permisos_usuario')
                ->where('id_permiso', $row->id_permiso)
                ->update(['id_submenu_permiso' => json_encode($current)]);
        }
    }

    private function grantSystemsAccess($systemsId, array $menuIds, $now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario') || ! count($menuIds)) {
            return;
        }

        $rows = DB::table('new_db_permisos_usuario')
            ->where('id_usuario_permiso', $systemsId)
            ->get();

        if (! count($rows)) {
            $activeRows = DB::table('new_db_menu')->where('estado_menu', 1)->get();
            $activeIds = [];
            foreach ($activeRows as $activeRow) {
                $activeIds[] = (int) $activeRow->id_menu;
            }
            $activeIds = array_values(array_unique($activeIds));
            sort($activeIds);

            DB::table('new_db_permisos_usuario')->insert([
                'id_empresa_permiso' => null,
                'id_usuario_permiso' => $systemsId,
                'id_submenu_permiso' => json_encode($activeIds),
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
            $current = json_decode($row->id_submenu_permiso, true);
            $current = is_array($current) ? array_map('intval', $current) : [];
            $current = array_values(array_unique(array_merge($current, [10], $menuIds)));
            sort($current);

            DB::table('new_db_permisos_usuario')
                ->where('id_permiso', $row->id_permiso)
                ->update([
                    'id_submenu_permiso' => json_encode($current),
                    'updated_at_permiso' => $now,
                ]);
        }
    }
}
