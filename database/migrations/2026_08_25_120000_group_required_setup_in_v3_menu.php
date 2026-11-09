<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GroupRequiredSetupInV3Menu extends Migration
{
    const CONFIGURATION_ROOT_ID = 10;

    private $groups = [
        697 => ['Organización y emisión', 'fas fa-building', 10],
        698 => ['Configuración clínica', 'fas fa-clinic-medical', 20],
        699 => ['Configuración contable inicial', 'fas fa-balance-scale', 30],
        700 => ['Seguridad y sistema', 'fas fa-user-shield', 40],
    ];

    private $itemsByGroup = [
        697 => [
            533 => 10,  // Empresas
            536 => 20,  // Establecimientos
            596 => 30,  // Puntos de emisión
            570 => 40,  // Bodegas
            571 => 50,  // Planes
            572 => 60,  // Selección de plan
            685 => 70,  // Países, provincias y ciudades
            686 => 80,  // Ubicaciones
            601 => 90,  // Logos de documentos PDF
        ],
        698 => [
            684 => 10,  // Especialidades
            687 => 20,  // Procedimientos
            696 => 30,  // Tipos de procedimiento
            688 => 40,  // Seguros
            694 => 50,  // CIE 10 - 3 caracteres
            695 => 60,  // CIE 10 - 4 caracteres
        ],
        699 => [
            564 => 10,  // Plan de cuentas
            14 => 20,  // Lista de bancos
            689 => 30,  // Formas de pago
            599 => 40,  // Producto tarifario
            600 => 50,  // Configuraciones globales
            31 => 60,  // Saldos iniciales de inventario
            251 => 70,  // Saldos iniciales de proveedores
            259 => 80,  // Saldos iniciales de clientes
        ],
        700 => [
            534 => 10,  // Usuarios
            535 => 20,  // Roles
            271 => 30,  // Permisos
            690 => 40,  // Tipos de usuario y permisos
            270 => 50,  // Menú
            541 => 60,  // Catálogo
            542 => 70,  // Auditoría
            691 => 80,  // Referencias de inconvenientes
            692 => 90,  // Matriz de madurez
            693 => 100, // Mantenimiento y despliegue
        ],
    ];

    private $duplicateItems = [
        586 => [582, 11, 1], // Grupo Empresa
        587 => [586, 0, 1],  // Empresas (enlace duplicado)
        594 => [582, 14, 1], // Grupo Mantenimiento
        595 => [594, 0, 1],  // Establecimientos (enlace duplicado)
        597 => [594, 2, 1],  // Bodegas (enlace duplicado)
        598 => [582, 15, 1], // Antiguo grupo Configuraciones contables
        683 => [10, 12, 1],  // Antiguo grupo Administración del sistema
    ];

    private $originalPlacement = [
        533 => [10, 1], 536 => [10, 2], 596 => [594, 1],
        570 => [10, 8], 571 => [10, 9], 572 => [10, 10],
        685 => [683, 20], 686 => [683, 30], 601 => [598, 2],
        684 => [683, 10], 687 => [683, 40], 696 => [683, 45],
        688 => [683, 50], 694 => [683, 110], 695 => [683, 120],
        564 => [563, 0], 14 => [2, 1], 689 => [683, 60],
        599 => [598, 0], 600 => [598, 1], 31 => [61, 0],
        251 => [6, 0], 259 => [5, 8],
        534 => [10, 3], 535 => [10, 5], 271 => [10, 6],
        690 => [683, 70], 270 => [10, 0], 541 => [10, 4],
        542 => [10, 7], 691 => [683, 80], 692 => [683, 90],
        693 => [683, 100],
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
                foreach ($this->groups as $id => $group) {
                    $this->upsertGroup($id, $group, $actorId, $now);
                }

                foreach ($this->itemsByGroup as $groupId => $items) {
                    foreach ($items as $menuId => $order) {
                        DB::table('new_db_menu')->where('id_menu', $menuId)->update([
                            'id_pertenece_menu' => $groupId,
                            'orden_menu' => $order,
                            'updated_at_menu' => $now,
                            'id_usuario_modificacion_menu' => $actorId,
                            'ip_modificacion_menu' => '127.0.0.1',
                        ]);
                    }
                }

                DB::table('new_db_menu')->whereIn('id_menu', array_keys($this->duplicateItems))->update([
                    'estado_menu' => 0,
                    'updated_at_menu' => $now,
                    'id_usuario_modificacion_menu' => $actorId,
                    'ip_modificacion_menu' => '127.0.0.1',
                ]);

                $this->rebuildPermissions($now);
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

        $now = date('Y-m-d H:i:s');
        DB::transaction(function () use ($now) {
            foreach ($this->originalPlacement as $menuId => $placement) {
                DB::table('new_db_menu')->where('id_menu', $menuId)->update([
                    'id_pertenece_menu' => $placement[0],
                    'orden_menu' => $placement[1],
                    'updated_at_menu' => $now,
                ]);
            }

            foreach ($this->duplicateItems as $menuId => $placement) {
                DB::table('new_db_menu')->where('id_menu', $menuId)->update([
                    'id_pertenece_menu' => $placement[0],
                    'orden_menu' => $placement[1],
                    'estado_menu' => $placement[2],
                    'updated_at_menu' => $now,
                ]);
            }

            $groupIds = array_keys($this->groups);
            if (Schema::hasTable('new_db_permisos_usuario')) {
                foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                    $current = json_decode($permission->id_submenu_permiso, true);
                    $current = is_array($current) ? array_map('intval', $current) : [];
                    $current = array_values(array_diff($current, $groupIds));
                    sort($current);
                    DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                        ->update(['id_submenu_permiso' => json_encode($current)]);
                }
            }

            DB::table('new_db_menu')->whereIn('id_menu', $groupIds)->delete();
        });
    }

    private function upsertGroup($id, array $group, $actorId, $now)
    {
        $data = [
            'nombre_menu' => $group[0],
            'icon_menu' => '<i class="'.$group[1].' nav-icon"></i>',
            'controlador_menu' => null,
            'funcion_menu' => null,
            'id_usuario_modificacion_menu' => $actorId,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => $group[2],
            'estado_menu' => 1,
            'id_pertenece_menu' => self::CONFIGURATION_ROOT_ID,
            'color_menu' => null,
        ];

        if (DB::table('new_db_menu')->where('id_menu', $id)->exists()) {
            DB::table('new_db_menu')->where('id_menu', $id)->update($data);

            return;
        }

        DB::table('new_db_menu')->insert(array_merge($data, [
            'id_menu' => $id,
            'id_usuario_creacion_menu' => $actorId,
            'created_at_menu' => $now,
            'ip_creacion_menu' => '127.0.0.1',
        ]));
    }

    private function rebuildPermissions($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }

        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $current = json_decode($permission->id_submenu_permiso, true);
            $current = is_array($current) ? array_map('intval', $current) : [];

            // Quien ya veía una opción duplicada conserva acceso a su equivalente V3.
            if (in_array(587, $current, true)) {
                $current[] = 533;
            }
            if (in_array(595, $current, true)) {
                $current[] = 536;
            }
            if (in_array(597, $current, true)) {
                $current[] = 570;
            }

            foreach ($this->itemsByGroup as $groupId => $items) {
                if (count(array_intersect(array_keys($items), $current))) {
                    $current[] = self::CONFIGURATION_ROOT_ID;
                    $current[] = $groupId;
                }
            }

            $current = array_values(array_unique($current));
            sort($current);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
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
