<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNetworkAccessToSidebar extends Migration
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
        $option = DB::table('opcion')->where('clave', 'system.network_access')->first();
        $values = $this->existing('opcion', ['clave' => 'system.network_access', 'nombre' => 'Acceso por IP y horario',
            'descripcion' => 'Reglas de red y horario por usuario', 'ruta' => '/sistema/acceso-red', 'estado' => 1,
            'id_usuariocrea' => $systems->id, 'id_usuariomod' => $systems->id, 'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1', 'created_at' => $now, 'updated_at' => $now]);
        if ($option) {
            unset($values['id_usuariocrea'],$values['ip_creacion'],$values['created_at']);
            DB::table('opcion')->where('id', $option->id)->update($values);
            $optionId = $option->id;
        } else {
            $optionId = DB::table('opcion')->insertGetId($values);
        }

        $sidebarValues = $this->existing('sidebar_items', ['parent_id' => $parent->id, 'id_opcion' => $optionId,
            'clave' => 'system.network_access', 'etiqueta' => 'Acceso por IP y horario', 'tipo' => 'enlace',
            'nombre_ruta' => 'network-access.index', 'url' => '/sistema/acceso-red', 'icono' => 'fa fa-shield',
            'orden' => 106, 'patrones_activos' => 'sistema/acceso-red', 'fijable' => 1, 'estado' => 1, 'created_at' => $now, 'updated_at' => $now]);
        $sidebar = DB::table('sidebar_items')->where('clave', 'system.network_access')->first();
        if ($sidebar) {
            unset($sidebarValues['created_at']);
            DB::table('sidebar_items')->where('id', $sidebar->id)->update($sidebarValues);
        } else {
            DB::table('sidebar_items')->insert($sidebarValues);
        }

        if (! Schema::hasTable('opcion_usuario')) {
            return;
        }
        $permission = DB::table('opcion_usuario')->where('id_opcion', $optionId)->where('id_tipo_usuario', 1)->first();
        if (! $permission) {
            DB::table('opcion_usuario')->insert($this->existing('opcion_usuario', ['id_opcion' => $optionId,
                'id_tipo_usuario' => 1, 'estado' => 1, 'id_usuariocrea' => $systems->id, 'id_usuariomod' => $systems->id,
                'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1', 'created_at' => $now, 'updated_at' => $now]));
        }
    }

    public function down()
    {
        if (Schema::hasTable('sidebar_items')) {
            DB::table('sidebar_items')->where('clave', 'system.network_access')->delete();
        }
        if (! Schema::hasTable('opcion')) {
            return;
        }
        $option = DB::table('opcion')->where('clave', 'system.network_access')->first();
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
