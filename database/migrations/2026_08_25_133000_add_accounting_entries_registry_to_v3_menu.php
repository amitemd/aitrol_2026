<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAccountingEntriesRegistryToV3Menu extends Migration
{
    const MENU_ID = 701;

    const ACCOUNTING_PARENT_ID = 563;

    public function up()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $actorId = $this->actorId();
        $menu = [
            'nombre_menu' => 'Asientos contables',
            'icon_menu' => '<i class="nav-icon fas fa-book-open"></i>',
            'controlador_menu' => 'contabilidad',
            'funcion_menu' => 'asientos',
            'id_usuario_modificacion_menu' => $actorId,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 0,
            'estado_menu' => 1,
            'id_pertenece_menu' => self::ACCOUNTING_PARENT_ID,
            'color_menu' => null,
        ];

        $deferUserForeignKeys = DB::connection()->getDriverName() === 'mysql'
            && Schema::hasTable('users')
            && ! DB::table('users')->where('id', $actorId)->exists();
        if ($deferUserForeignKeys) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        try {
            DB::transaction(function () use ($menu, $actorId, $now) {
                if (DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->exists()) {
                    DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->update($menu);
                } else {
                    DB::table('new_db_menu')->insert(array_merge($menu, [
                        'id_menu' => self::MENU_ID,
                        'id_usuario_creacion_menu' => $actorId,
                        'created_at_menu' => $now,
                        'ip_creacion_menu' => '127.0.0.1',
                    ]));
                }

                $this->grantExistingAccountingUsers($now);
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

        DB::transaction(function () {
            if (Schema::hasTable('new_db_permisos_usuario')) {
                foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                    $items = json_decode($permission->id_submenu_permiso, true);
                    $items = is_array($items) ? array_map('intval', $items) : [];
                    $items = array_values(array_diff($items, [self::MENU_ID]));
                    sort($items);
                    DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                        ->update(['id_submenu_permiso' => json_encode($items)]);
                }
            }
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->delete();
        });
    }

    private function grantExistingAccountingUsers($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }

        foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
            $items = json_decode($permission->id_submenu_permiso, true);
            $items = is_array($items) ? array_map('intval', $items) : [];
            $hasAccountingAccess = count(array_intersect([self::ACCOUNTING_PARENT_ID, 568, 569, 682], $items)) > 0;
            if (! $hasAccountingAccess) {
                continue;
            }

            $items[] = 577; // Cuentas y finanzas
            $items[] = 582; // Contable
            $items[] = self::ACCOUNTING_PARENT_ID;
            $items[] = self::MENU_ID;
            $items = array_values(array_unique($items));
            sort($items);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($items),
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
