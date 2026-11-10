<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCompanyCardTypesMaintenance extends Migration
{
    const MENU_ID = 745;

    const PAYMENT_TYPES_MENU_ID = 689;

    const CONFIGURATION_ROOT_ID = 10;

    const ACCOUNTING_CONFIGURATION_ID = 699;

    public function up()
    {
        $this->addCompanyScope();
        $this->publishMenu();
    }

    public function down()
    {
        if (Schema::hasTable('v3_role_menu_permissions')) {
            DB::table('v3_role_menu_permissions')->where('id_menu', self::MENU_ID)->delete();
        }

        if (Schema::hasTable('new_db_permisos_usuario')) {
            foreach (DB::table('new_db_permisos_usuario')->get() as $permission) {
                $ids = json_decode($permission->id_submenu_permiso, true);
                $ids = is_array($ids) ? array_map('intval', $ids) : [];
                $ids = array_values(array_diff($ids, [self::MENU_ID]));
                DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                    ->update(['id_submenu_permiso' => json_encode($ids)]);
            }
        }

        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', self::MENU_ID)->delete();
        }

        // id_empresa no se elimina: hacerlo volveria a mezclar catalogos de
        // tarjetas que ya pertenecen a empresas diferentes.
    }

    private function addCompanyScope()
    {
        if (! Schema::hasTable('ct_tipo_tarjeta')) {
            return;
        }

        if (! Schema::hasColumn('ct_tipo_tarjeta', 'id_empresa')) {
            Schema::table('ct_tipo_tarjeta', function (Blueprint $table) {
                $table->string('id_empresa', 13)->nullable()->index();
            });
        }

        $defaultCompany = null;
        if (Schema::hasTable('empresa')) {
            $defaultCompany = DB::table('empresa')->where('estado', 1)
                ->orderBy('prioridad')->orderBy('id')->value('id');
            if (! $defaultCompany) {
                $defaultCompany = DB::table('empresa')->orderBy('id')->value('id');
            }
        }
        if (! $defaultCompany) {
            return;
        }

        $this->assignByCreator();
        DB::table('ct_tipo_tarjeta')->whereNull('id_empresa')->orWhere('id_empresa', '')
            ->update(['id_empresa' => (string) $defaultCompany]);

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `ct_tipo_tarjeta` MODIFY `id_empresa` VARCHAR(13) NOT NULL');
        }
    }

    private function assignByCreator()
    {
        if (! Schema::hasTable('usuario_empresa')
            || ! Schema::hasColumn('ct_tipo_tarjeta', 'id_usuariocrea')) {
            return;
        }

        $owners = DB::table('usuario_empresa')
            ->select('id_usuario', DB::raw('MIN(id_empresa) as id_empresa'))
            ->groupBy('id_usuario')->havingRaw('COUNT(DISTINCT id_empresa) = 1')->get();
        foreach ($owners as $owner) {
            DB::table('ct_tipo_tarjeta')->where('id_usuariocrea', $owner->id_usuario)
                ->where(function ($query) {
                    $query->whereNull('id_empresa')->orWhere('id_empresa', '');
                })->update(['id_empresa' => (string) $owner->id_empresa]);
        }
    }

    private function publishMenu()
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $actor = $this->actorId();
        $menu = [
            'nombre_menu' => 'Tipos de tarjeta',
            'icon_menu' => '<i class="fas fa-credit-card nav-icon"></i>',
            'controlador_menu' => 'configuraciones-globales',
            'funcion_menu' => 'tipos-tarjeta',
            'id_usuario_modificacion_menu' => $actor,
            'updated_at_menu' => $now,
            'ip_modificacion_menu' => '127.0.0.1',
            'orden_menu' => 35,
            'estado_menu' => 1,
            'id_pertenece_menu' => self::ACCOUNTING_CONFIGURATION_ID,
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
            $this->grantUsers($now);
            $this->copyRolePermissions($now);
        }, 3);
    }

    private function grantUsers($now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }

        $query = DB::table('new_db_permisos_usuario as permission');
        if (Schema::hasTable('users')) {
            $query->leftJoin('users as user', 'user.id', '=', 'permission.id_usuario_permiso')
                ->select('permission.id_permiso', 'permission.id_submenu_permiso', 'user.id_tipo_usuario');
        } else {
            $query->select('permission.id_permiso', 'permission.id_submenu_permiso',
                DB::raw('NULL as id_tipo_usuario'));
        }

        foreach ($query->get() as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            $isSystems = (int) $permission->id_tipo_usuario === 1;
            if (! $isSystems && ! in_array(self::PAYMENT_TYPES_MENU_ID, $ids, true)) {
                continue;
            }

            $ids = array_values(array_unique(array_merge($ids, [
                self::CONFIGURATION_ROOT_ID,
                self::ACCOUNTING_CONFIGURATION_ID,
                self::MENU_ID,
            ])));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)
                ->update(['id_submenu_permiso' => json_encode($ids), 'updated_at_permiso' => $now]);
        }
    }

    private function copyRolePermissions($now)
    {
        if (! Schema::hasTable('v3_role_menu_permissions')) {
            return;
        }

        $permissions = DB::table('v3_role_menu_permissions')
            ->where('id_menu', self::PAYMENT_TYPES_MENU_ID)->get();
        foreach ($permissions as $permission) {
            $scope = [
                'id_empresa' => $permission->id_empresa,
                'id_tipo_usuario' => $permission->id_tipo_usuario,
                'id_menu' => self::MENU_ID,
            ];
            $values = ['permitido' => $permission->permitido, 'updated_at' => $now];
            if (DB::table('v3_role_menu_permissions')->where($scope)->exists()) {
                DB::table('v3_role_menu_permissions')->where($scope)->update($values);
            } else {
                DB::table('v3_role_menu_permissions')->insert(array_merge($scope, $values, [
                    'created_at' => $now,
                ]));
            }
        }
    }

    private function actorId()
    {
        if (Schema::hasTable('users')) {
            $id = DB::table('users')->where('id_tipo_usuario', 1)->value('id');
            if ($id) {
                return (string) $id;
            }
            $id = DB::table('users')->value('id');
            if ($id) {
                return (string) $id;
            }
        }

        return env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
    }
}
