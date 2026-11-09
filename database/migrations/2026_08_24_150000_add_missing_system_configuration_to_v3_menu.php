<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMissingSystemConfigurationToV3Menu extends Migration
{
    const GROUP_ID = 683;

    private $items = [
        684 => ['Especialidades', 'fas fa-stethoscope', 'index', 10, [1, 4], 'especialidades'],
        685 => ['Países, provincias y ciudades', 'fas fa-map-marked-alt', 'ubicacion-geografica.index', 20, [1, 4]],
        686 => ['Ubicaciones', 'fas fa-hospital', 'hospital-management.index', 30, [1, 4]],
        687 => ['Procedimientos', 'fas fa-notes-medical', 'index', 40, [1, 4], 'procedimientos'],
        688 => ['Seguros', 'fas fa-shield-alt', 'form_enviar_seguro.index', 50, [1, 7]],
        689 => ['Formas de pago', 'fas fa-credit-card', 'tipo_pago.index', 60, [1, 4, 5, 20]],
        690 => ['Tipos de usuario y permisos', 'fas fa-user-shield', 'tipo_usuario-management.index', 70, [1, 7]],
        691 => ['Referencias de inconvenientes', 'fas fa-life-ring', 'system-errors.index', 80, [1]],
        692 => ['Matriz de madurez', 'fas fa-chart-line', 'system-maturity.index', 90, [1]],
        693 => ['Mantenimiento y despliegue', 'fas fa-tools', 'system-maintenance.index', 100, [1]],
        694 => ['CIE 10 - 3 caracteres', 'fas fa-code', 'cie_10_3.index', 110, [1, 10]],
        695 => ['CIE 10 - 4 caracteres', 'fas fa-code', 'cie_10_4.index', 120, [1, 10]],
    ];

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $actorId = $this->actorId();
        $deferUserForeignKeys = DB::connection()->getDriverName() === 'mysql'
            && Schema::hasTable('users')
            && ! DB::table('users')->where('id', $actorId)->exists();

        if ($deferUserForeignKeys) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        try {
            DB::transaction(function () use ($now, $actorId) {
                $this->upsertMenu(self::GROUP_ID, [
                    'nombre_menu' => 'Administración del sistema',
                    'icon_menu' => '<i class="fas fa-cogs nav-icon"></i>',
                    'controlador_menu' => null,
                    'funcion_menu' => null,
                    'orden_menu' => 12,
                    'estado_menu' => 1,
                    'id_pertenece_menu' => 10,
                ], $actorId, $now);

                foreach ($this->items as $id => $item) {
                    $this->upsertMenu($id, [
                        'nombre_menu' => $item[0],
                        'icon_menu' => '<i class="'.$item[1].' nav-icon"></i>',
                        'controlador_menu' => isset($item[5]) ? $item[5] : 'legacy-route',
                        'funcion_menu' => $item[2],
                        'orden_menu' => $item[3],
                        'estado_menu' => 1,
                        'id_pertenece_menu' => self::GROUP_ID,
                    ], $actorId, $now);
                }

                $this->grantExistingUsers($now);
            });
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

        $ids = array_merge([self::GROUP_ID], array_keys($this->items));
        DB::transaction(function () use ($ids) {
            if (Schema::hasTable('new_db_permisos_usuario')) {
                foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                    $current = json_decode($permission->id_submenu_permiso, true);
                    $current = is_array($current) ? array_map('intval', $current) : [];
                    $current = array_values(array_diff($current, $ids));
                    DB::table('new_db_permisos_usuario')
                        ->where('id_permiso', $permission->id_permiso)
                        ->update(['id_submenu_permiso' => json_encode($current)]);
                }
            }

            DB::table('new_db_menu')->whereIn('id_menu', array_keys($this->items))->delete();
            DB::table('new_db_menu')->where('id_menu', self::GROUP_ID)->delete();
        });
    }

    private function upsertMenu($id, array $data, $actorId, $now)
    {
        $audit = [
            'id_usuario_modificacion_menu' => $actorId,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'color_menu' => null,
        ];

        if (DB::table('new_db_menu')->where('id_menu', $id)->exists()) {
            DB::table('new_db_menu')->where('id_menu', $id)->update(array_merge($data, $audit));

            return;
        }

        DB::table('new_db_menu')->insert(array_merge($data, $audit, [
            'id_menu' => $id,
            'id_usuario_creacion_menu' => $actorId,
            'created_at_menu' => $now,
            'ip_creacion_menu' => '127.0.0.1',
        ]));
    }

    private function grantExistingUsers($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario') || ! Schema::hasTable('users')) {
            return;
        }

        $rolesByMenu = [];
        foreach ($this->items as $menuId => $item) {
            $rolesByMenu[$menuId] = $item[4];
        }

        $permissions = DB::table('new_db_permisos_usuario as permission')
            ->join('users as user', 'user.id', '=', 'permission.id_usuario_permiso')
            ->select('permission.id_permiso', 'permission.id_submenu_permiso', 'user.id_tipo_usuario')
            ->get();

        foreach ($permissions as $permission) {
            $allowed = [];
            foreach ($rolesByMenu as $menuId => $roles) {
                if (in_array((int) $permission->id_tipo_usuario, $roles, true)) {
                    $allowed[] = (int) $menuId;
                }
            }
            if (! count($allowed)) {
                continue;
            }

            $current = json_decode($permission->id_submenu_permiso, true);
            $current = is_array($current) ? array_map('intval', $current) : [];
            $current = array_values(array_unique(array_merge($current, [10, self::GROUP_ID], $allowed)));
            sort($current);

            DB::table('new_db_permisos_usuario')
                ->where('id_permiso', $permission->id_permiso)
                ->update([
                    'id_submenu_permiso' => json_encode($current),
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
