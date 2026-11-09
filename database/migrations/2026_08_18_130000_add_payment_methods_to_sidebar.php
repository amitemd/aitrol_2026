<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodsToSidebar extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('opcion') || ! Schema::hasTable('sidebar_items')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $auditUser = $this->auditUserId();
        if (! $auditUser) {
            return;
        }

        $option = DB::table('opcion')->where('clave', 'system.payment_methods')->first();
        $optionData = [
            'clave' => 'system.payment_methods',
            'nombre' => 'Formas de pago',
            'descripcion' => 'Administración de las formas de pago utilizadas en facturación y caja',
            'ruta' => '/contable/tipo_pago',
            'estado' => 1,
            'id_usuariocrea' => $auditUser,
            'id_usuariomod' => $auditUser,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if ($option) {
            unset($optionData['id_usuariocrea'], $optionData['ip_creacion'], $optionData['created_at']);
            DB::table('opcion')->where('id', $option->id)->update($this->onlyExistingColumns('opcion', $optionData));
            $optionId = $option->id;
        } else {
            $optionId = DB::table('opcion')->insertGetId($this->onlyExistingColumns('opcion', $optionData));
        }

        $parent = DB::table('sidebar_items')->where('clave', 'config.system_admin')->first();
        if (! $parent) {
            return;
        }

        $sidebarData = [
            'parent_id' => $parent->id,
            'id_opcion' => $optionId,
            'clave' => 'system.payment_methods',
            'etiqueta' => 'Formas de pago',
            'traduccion' => 'tsidebar.menu_system_payment_methods',
            'tipo' => 'enlace',
            'nombre_ruta' => 'tipo_pago.index',
            'url' => null,
            'icono' => 'fa fa-credit-card',
            'orden' => 65,
            'patrones_activos' => 'contable/tipo_pago',
            'fijable' => 1,
            'estado' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $sidebar = DB::table('sidebar_items')->where('clave', 'system.payment_methods')->first();
        if ($sidebar) {
            unset($sidebarData['created_at']);
            DB::table('sidebar_items')->where('id', $sidebar->id)->update($sidebarData);
        } else {
            DB::table('sidebar_items')->insert($sidebarData);
        }

        if (! Schema::hasTable('opcion_usuario')) {
            return;
        }
        foreach ([1, 4, 5, 20] as $roleId) {
            if (Schema::hasTable('tipousuario') && ! DB::table('tipousuario')->where('id', $roleId)->exists()) {
                continue;
            }
            $permission = DB::table('opcion_usuario')->where('id_opcion', $optionId)->where('id_tipo_usuario', $roleId)->first();
            if ($permission) {
                DB::table('opcion_usuario')->where('id', $permission->id)->update($this->onlyExistingColumns('opcion_usuario', [
                    'estado' => 1,
                    'id_usuariomod' => $auditUser,
                    'ip_modificacion' => '127.0.0.1',
                    'updated_at' => $now,
                ]));

                continue;
            }
            DB::table('opcion_usuario')->insert($this->onlyExistingColumns('opcion_usuario', [
                'id_opcion' => $optionId,
                'id_tipo_usuario' => $roleId,
                'estado' => 1,
                'id_usuariocrea' => $auditUser,
                'id_usuariomod' => $auditUser,
                'ip_creacion' => '127.0.0.1',
                'ip_modificacion' => '127.0.0.1',
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down()
    {
        if (Schema::hasTable('sidebar_items')) {
            DB::table('sidebar_items')->where('clave', 'system.payment_methods')->update(['estado' => 0]);
        }
        if (Schema::hasTable('opcion')) {
            DB::table('opcion')->where('clave', 'system.payment_methods')->update(['estado' => 0]);
        }
    }

    private function auditUserId()
    {
        if (! Schema::hasTable('users')) {
            return null;
        }
        $user = DB::table('users')->where('id_tipo_usuario', 1)->first();
        if (! $user) {
            $user = DB::table('users')->first();
        }

        return $user ? $user->id : null;
    }

    private function onlyExistingColumns($table, array $data)
    {
        return array_intersect_key($data, array_flip(Schema::getColumnListing($table)));
    }
}
