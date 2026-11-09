<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWaitingRoomVideoAccess extends Migration
{
    const OPTION_KEY = 'waiting_room.display';

    const V3_MENU_ID = 709;

    const V3_SYSTEM_GROUP_ID = 683;

    public function up()
    {
        $now = date('Y-m-d H:i:s');
        $actor = $this->actorId();
        $systemsRoleId = $this->roleIdByName('SISTEMAS');
        $videoRoleId = $this->upsertVideoRole($actor, $now);
        $optionId = $this->upsertOption($actor, $now);

        $this->upsertLegacyMenu($optionId, $now);
        $this->grantOption($optionId, $systemsRoleId, $actor, $now);
        $this->grantOption($optionId, $videoRoleId, $actor, $now);
        $this->upsertV3Menu($actor, $now);
        $this->grantV3MenuToExistingUsers([$systemsRoleId, $videoRoleId], $now);
    }

    public function down()
    {
        if (Schema::hasTable('sidebar_items')) {
            DB::table('sidebar_items')->where('clave', self::OPTION_KEY)->update(['estado' => 0]);
        }
        if (Schema::hasTable('opcion')) {
            DB::table('opcion')->where('clave', self::OPTION_KEY)->update(['estado' => 0]);
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('id_menu', self::V3_MENU_ID)->update(['estado_menu' => 0]);
        }

        // The VIDEO profile is intentionally preserved because users may have
        // already been assigned to it by the system administrator.
    }

    private function upsertVideoRole($actor, $now)
    {
        if (! Schema::hasTable('tipousuario')) {
            return null;
        }

        $role = DB::table('tipousuario')->whereRaw('UPPER(nombre) = ?', ['VIDEO'])->first();
        $data = $this->onlyExisting('tipousuario', [
            'nombre' => 'VIDEO',
            'descripcion' => 'Usuario exclusivo para configurar y presentar pantallas de sala de espera.',
            'estado' => 1,
            'id_usuariocrea' => $actor,
            'id_usuariomod' => $actor,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($role) {
            unset($data['id_usuariocrea'], $data['ip_creacion'], $data['created_at']);
            DB::table('tipousuario')->where('id', $role->id)->update($data);

            return (int) $role->id;
        }

        return (int) DB::table('tipousuario')->insertGetId($data);
    }

    private function upsertOption($actor, $now)
    {
        if (! Schema::hasTable('opcion')) {
            return null;
        }

        $option = Schema::hasColumn('opcion', 'clave')
            ? DB::table('opcion')->where('clave', self::OPTION_KEY)->first()
            : null;
        $data = $this->onlyExisting('opcion', [
            'clave' => self::OPTION_KEY,
            'nombre' => 'Pantallas de sala de espera',
            'descripcion' => 'Activa la sala y presenta pacientes y videos en un televisor.',
            'ruta' => '/sala/espera/pacientes',
            'estado' => 1,
            'id_usuariocrea' => $actor,
            'id_usuariomod' => $actor,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($option) {
            unset($data['id_usuariocrea'], $data['ip_creacion'], $data['created_at']);
            DB::table('opcion')->where('id', $option->id)->update($data);

            return (int) $option->id;
        }

        if (! $actor) {
            return null;
        }

        return (int) DB::table('opcion')->insertGetId($data);
    }

    private function upsertLegacyMenu($optionId, $now)
    {
        if (! $optionId || ! Schema::hasTable('sidebar_items')) {
            return;
        }

        $parent = DB::table('sidebar_items')->where('clave', 'config.system_admin')->first();
        if (! $parent) {
            return;
        }

        $data = $this->onlyExisting('sidebar_items', [
            'parent_id' => $parent->id,
            'id_opcion' => $optionId,
            'clave' => self::OPTION_KEY,
            'etiqueta' => 'Pantallas de sala de espera',
            'tipo' => 'enlace',
            'nombre_ruta' => 'waiting_room_display.index',
            'url' => '/sala/espera/pacientes',
            'icono' => 'fa fa-television',
            'orden' => 125,
            'patrones_activos' => 'sala/espera/pacientes,sala/espera/pantalla',
            'fijable' => 1,
            'estado' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $item = DB::table('sidebar_items')->where('clave', self::OPTION_KEY)->first();
        if ($item) {
            unset($data['created_at']);
            DB::table('sidebar_items')->where('id', $item->id)->update($data);

            return;
        }

        DB::table('sidebar_items')->insert($data);
    }

    private function grantOption($optionId, $roleId, $actor, $now)
    {
        if (! $optionId || ! $roleId || ! Schema::hasTable('opcion_usuario')) {
            return;
        }

        $permission = DB::table('opcion_usuario')
            ->where('id_opcion', $optionId)
            ->where('id_tipo_usuario', $roleId)
            ->first();
        $data = $this->onlyExisting('opcion_usuario', [
            'id_opcion' => $optionId,
            'id_tipo_usuario' => $roleId,
            'estado' => 1,
            'id_usuariocrea' => $actor,
            'id_usuariomod' => $actor,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($permission) {
            unset($data['id_usuariocrea'], $data['ip_creacion'], $data['created_at']);
            DB::table('opcion_usuario')->where('id', $permission->id)->update($data);

            return;
        }

        DB::table('opcion_usuario')->insert($data);
    }

    private function upsertV3Menu($actor, $now)
    {
        if (! Schema::hasTable('new_db_menu')) {
            return;
        }

        $menuActor = $actor ?: env('BOOTSTRAP_SYSTEMS_ID', '0000000001');
        $deferUserForeignKeys = DB::connection()->getDriverName() === 'mysql'
            && Schema::hasTable('users')
            && ! DB::table('users')->where('id', $menuActor)->exists();
        if ($deferUserForeignKeys) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        try {
            $parentId = DB::table('new_db_menu')->where('id_menu', self::V3_SYSTEM_GROUP_ID)->exists()
                ? self::V3_SYSTEM_GROUP_ID
                : 10;
            $data = $this->onlyExisting('new_db_menu', [
                'nombre_menu' => 'Pantallas de sala de espera',
                'icon_menu' => '<i class="nav-icon fas fa-tv"></i>',
                'controlador_menu' => 'legacy-route',
                'funcion_menu' => 'waiting_room_display.index',
                'id_usuario_modificacion_menu' => $menuActor,
                'updated_at_menu' => $now,
                'ip_modificacion_menu' => '127.0.0.1',
                'orden_menu' => 125,
                'estado_menu' => 1,
                'id_pertenece_menu' => $parentId,
                'color_menu' => null,
            ]);

            if (DB::table('new_db_menu')->where('id_menu', self::V3_MENU_ID)->exists()) {
                DB::table('new_db_menu')->where('id_menu', self::V3_MENU_ID)->update($data);

                return;
            }

            DB::table('new_db_menu')->insert(array_merge($data, $this->onlyExisting('new_db_menu', [
                'id_menu' => self::V3_MENU_ID,
                'id_usuario_creacion_menu' => $menuActor,
                'created_at_menu' => $now,
                'ip_creacion_menu' => '127.0.0.1',
            ])));
        } finally {
            if ($deferUserForeignKeys) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }
    }

    private function grantV3MenuToExistingUsers(array $roleIds, $now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario') || ! Schema::hasTable('users')) {
            return;
        }

        $roleIds = array_values(array_filter(array_map('intval', $roleIds)));
        if (! count($roleIds)) {
            return;
        }
        $permissions = DB::table('new_db_permisos_usuario as permission')
            ->join('users as user', 'user.id', '=', 'permission.id_usuario_permiso')
            ->whereIn('user.id_tipo_usuario', $roleIds)
            ->select('permission.id_permiso', 'permission.id_submenu_permiso')
            ->get();

        foreach ($permissions as $permission) {
            $ids = json_decode($permission->id_submenu_permiso, true);
            $ids = is_array($ids) ? array_map('intval', $ids) : [];
            $ids = array_values(array_unique(array_merge($ids, [10, self::V3_SYSTEM_GROUP_ID, self::V3_MENU_ID])));
            sort($ids);
            DB::table('new_db_permisos_usuario')->where('id_permiso', $permission->id_permiso)->update([
                'id_submenu_permiso' => json_encode($ids),
                'updated_at_permiso' => $now,
            ]);
        }
    }

    private function actorId()
    {
        if (Schema::hasTable('users') && Schema::hasTable('tipousuario')) {
            $id = DB::table('users as user')
                ->join('tipousuario as type', 'type.id', '=', 'user.id_tipo_usuario')
                ->whereRaw('UPPER(type.nombre) = ?', ['SISTEMAS'])
                ->value('user.id');
            if ($id) {
                return (string) $id;
            }
        }

        return null;
    }

    private function roleIdByName($name)
    {
        if (! Schema::hasTable('tipousuario')) {
            return null;
        }

        return DB::table('tipousuario')
            ->whereRaw('UPPER(nombre) = ?', [strtoupper($name)])
            ->value('id');
    }

    private function onlyExisting($table, array $data)
    {
        return array_intersect_key($data, array_flip(Schema::getColumnListing($table)));
    }
}
