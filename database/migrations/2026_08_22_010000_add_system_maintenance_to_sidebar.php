<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSystemMaintenanceToSidebar extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('opcion') || ! Schema::hasTable('sidebar_items') || ! Schema::hasTable('users')) {
            return;
        }

        $systems = DB::table('users')->where('id_tipo_usuario', 1)->first();
        $parent = DB::table('sidebar_items')->where('clave', 'config.system_admin')->first();
        if (! $systems || ! $parent) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $option = DB::table('opcion')->where('clave', 'system.maintenance')->first();
        $optionData = $this->existing('opcion', [
            'clave' => 'system.maintenance',
            'nombre' => 'Mantenimiento y despliegue',
            'descripcion' => 'Optimización controlada y ejecución de migraciones del sistema',
            'ruta' => '/sistema/mantenimiento',
            'estado' => 1,
            'id_usuariocrea' => $systems->id,
            'id_usuariomod' => $systems->id,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($option) {
            unset($optionData['id_usuariocrea'], $optionData['ip_creacion'], $optionData['created_at']);
            DB::table('opcion')->where('id', $option->id)->update($optionData);
            $optionId = $option->id;
        } else {
            $optionId = DB::table('opcion')->insertGetId($optionData);
        }

        $sidebarData = $this->existing('sidebar_items', [
            'parent_id' => $parent->id,
            'id_opcion' => $optionId,
            'clave' => 'system.maintenance',
            'etiqueta' => 'Mantenimiento y despliegue',
            'traduccion' => 'tsidebar.menu_system_maintenance',
            'tipo' => 'enlace',
            'nombre_ruta' => 'system-maintenance.index',
            'url' => '/sistema/mantenimiento',
            'icono' => 'fa fa-wrench',
            'orden' => 107,
            'patrones_activos' => 'sistema/mantenimiento',
            'fijable' => 1,
            'estado' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $sidebar = DB::table('sidebar_items')->where('clave', 'system.maintenance')->first();
        if ($sidebar) {
            unset($sidebarData['created_at']);
            DB::table('sidebar_items')->where('id', $sidebar->id)->update($sidebarData);
        } else {
            DB::table('sidebar_items')->insert($sidebarData);
        }

        if (! Schema::hasTable('opcion_usuario')) {
            return;
        }

        $permission = DB::table('opcion_usuario')
            ->where('id_opcion', $optionId)
            ->where('id_tipo_usuario', 1)
            ->first();
        $permissionData = $this->existing('opcion_usuario', [
            'id_opcion' => $optionId,
            'id_tipo_usuario' => 1,
            'estado' => 1,
            'id_usuariocrea' => $systems->id,
            'id_usuariomod' => $systems->id,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        if ($permission) {
            unset($permissionData['id_opcion'], $permissionData['id_tipo_usuario'], $permissionData['id_usuariocrea'], $permissionData['ip_creacion'], $permissionData['created_at']);
            DB::table('opcion_usuario')->where('id', $permission->id)->update($permissionData);
        } else {
            DB::table('opcion_usuario')->insert($permissionData);
        }
    }

    public function down()
    {
        if (Schema::hasTable('sidebar_items')) {
            DB::table('sidebar_items')->where('clave', 'system.maintenance')->delete();
        }
        if (! Schema::hasTable('opcion')) {
            return;
        }
        $option = DB::table('opcion')->where('clave', 'system.maintenance')->first();
        if (! $option) {
            return;
        }
        if (Schema::hasTable('opcion_usuario')) {
            DB::table('opcion_usuario')->where('id_opcion', $option->id)->delete();
        }
        DB::table('opcion')->where('id', $option->id)->delete();
    }

    private function existing($table, array $values)
    {
        return array_intersect_key($values, array_flip(Schema::getColumnListing($table)));
    }
}
