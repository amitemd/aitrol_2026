<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLegacySidebarMenu extends Migration
{
    public function up()
    {
        if (Schema::hasTable('opcion') && ! Schema::hasColumn('opcion', 'clave')) {
            Schema::table('opcion', function (Blueprint $table) {
                $table->string('clave', 120)->nullable()->after('id');
                $table->unique('clave', 'opcion_clave_unique');
            });
        }

        if (! Schema::hasTable('sidebar_items')) {
            Schema::create('sidebar_items', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('parent_id')->nullable();
                $table->unsignedInteger('id_opcion')->nullable();
                $table->string('clave', 120)->unique();
                $table->string('etiqueta', 150);
                $table->string('traduccion', 180)->nullable();
                $table->enum('tipo', ['grupo', 'enlace'])->default('enlace');
                $table->string('nombre_ruta', 190)->nullable();
                $table->string('url', 255)->nullable();
                $table->string('icono', 100)->nullable();
                $table->unsignedInteger('orden')->default(0);
                $table->string('patrones_activos', 255)->nullable();
                $table->boolean('fijable')->default(true);
                $table->boolean('estado')->default(true);
                $table->timestamps();

                $table->index('parent_id', 'sidebar_items_parent_index');
                $table->index('id_opcion', 'sidebar_items_opcion_index');
                $table->index(['estado', 'orden'], 'sidebar_items_estado_orden_index');
            });
        }

        $this->seedAdministrationMenu();
    }

    private function seedAdministrationMenu()
    {
        if (! Schema::hasTable('opcion') || ! Schema::hasTable('sidebar_items')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $auditUserId = $this->auditUserId();
        $rootId = $this->upsertSidebarItem([
            'clave' => 'config.system_admin',
            'etiqueta' => 'Administración de sistemas',
            'traduccion' => 'tsidebar.administracion_sistemas',
            'tipo' => 'grupo',
            'icono' => 'fa fa-cogs',
            'orden' => 10,
            'fijable' => 0,
            'estado' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $items = [
            ['system.specialties', 'Especialidades', 'Administración de especialidades', '/especialidad', 'especialidad.index', null, 'fa fa-briefcase', 10, [1, 4], 'tsidebar.menu_system_specialties'],
            ['system.geography', 'Países, provincias y ciudades', 'Administración del catálogo geográfico', '/ubicaciones-geograficas', 'ubicacion-geografica.index', null, 'fa fa-map-marker', 20, [1, 4], 'tsidebar.menu_system_geography'],
            ['system.locations', 'Ubicaciones', 'Administración de hospitales y ubicaciones', '/hospital-management', 'hospital-management.index', null, 'fa fa-building', 30, [1, 4], 'tsidebar.menu_system_locations'],
            ['system.procedures', 'Procedimientos', 'Administración de procedimientos', '/procedimiento', 'procedimiento.index', null, 'fa fa-book', 40, [1, 4], 'tsidebar.menu_system_procedures'],
            ['system.insurances', 'Seguros', 'Administración de seguros', '/form_enviar_seguro', 'form_enviar_seguro.index', null, 'fa fa-medkit', 50, [1, 7], 'tsidebar.menu_system_insurances'],
            ['system.companies', 'Empresas', 'Administración de empresas', '/empresa', 'empresa.maintenance.index', null, 'fa fa-building-o', 60, [1], 'tsidebar.menu_system_companies'],
            ['system.complete_procedures', 'Procedimientos completos', 'Administración de procedimientos completos', '/tecnicas', null, 'tecnicas', 'fa fa-list-alt', 70, [1, 4], 'tsidebar.menu_system_complete_procedures'],
            ['system.user_types', 'Tipos de usuario y permisos', 'Administración de perfiles y permisos', '/tipo_usuario-management', 'tipo_usuario-management.index', null, 'fa fa-id-badge', 80, [1, 7], 'tsidebar.menu_system_user_types'],
            ['system.users', 'Administración de usuarios', 'Administración de usuarios del sistema', '/user-management', 'user-management.index', null, 'fa fa-user-md', 90, [1], 'tsidebar.menu_system_users'],
            ['system.error_references', 'Referencias de inconvenientes', 'Consulta del registro técnico de errores', '/sistema/referencias', 'system-errors.index', null, 'fa fa-life-ring', 100, [1], 'tsidebar.menu_system_error_references'],
            ['system.sidebar', 'Menú y permisos', 'Administración del sidebar dinámico', '/sidebar-management', 'sidebar-management.index', null, 'fa fa-sitemap', 110, [1], 'tsidebar.menu_system_sidebar'],
            ['system.cie10_3', 'CIE 10 - 3 caracteres', 'Administración del catálogo CIE 10 de tres caracteres', '/cie_10_3', 'cie_10_3.index', null, 'fa fa-code', 120, [1, 10], 'tsidebar.menu_system_cie10_3'],
            ['system.cie10_4', 'CIE 10 - 4 caracteres', 'Administración del catálogo CIE 10 de cuatro caracteres', '/cie_10_4', 'cie_10_4.index', null, 'fa fa-code', 130, [1, 10], 'tsidebar.menu_system_cie10_4'],
        ];

        foreach ($items as $item) {
            $optionId = $this->upsertOption($item[0], $item[1], $item[2], $item[3], $now, $auditUserId);
            $this->upsertSidebarItem([
                'parent_id' => $rootId,
                'id_opcion' => $optionId,
                'clave' => $item[0],
                'etiqueta' => $item[1],
                'traduccion' => $item[9],
                'tipo' => 'enlace',
                'nombre_ruta' => $item[4],
                'url' => $item[5],
                'icono' => $item[6],
                'orden' => $item[7],
                'patrones_activos' => ltrim($item[3], '/'),
                'fijable' => 1,
                'estado' => $optionId ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->grantLegacyRoles($optionId, $item[8], $now, $auditUserId);
        }

        // La ruta histórica /tecnicas requiere un identificador de agenda.
        // Se conserva en el catálogo, pero no se muestra hasta definir un destino sin parámetros.
        DB::table('sidebar_items')->where('clave', 'system.complete_procedures')->update(['estado' => 0]);
    }

    private function upsertOption($key, $name, $description, $path, $now, $auditUserId)
    {
        $existing = DB::table('opcion')->where('clave', $key)->first();
        if ($existing) {
            DB::table('opcion')->where('id', $existing->id)->update([
                'nombre' => $name,
                'descripcion' => $description,
                'ruta' => $path,
                'estado' => 1,
                'updated_at' => $now,
            ]);

            return $existing->id;
        }

        if (! $auditUserId) {
            return null;
        }

        $data = [
            'clave' => $key,
            'nombre' => $name,
            'descripcion' => $description,
            'ruta' => $path,
            'estado' => 1,
            'id_usuariocrea' => $auditUserId,
            'id_usuariomod' => $auditUserId,
            'ip_creacion' => '127.0.0.1',
            'ip_modificacion' => '127.0.0.1',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        return DB::table('opcion')->insertGetId($this->onlyExistingColumns('opcion', $data));
    }

    private function upsertSidebarItem(array $data)
    {
        $existing = DB::table('sidebar_items')->where('clave', $data['clave'])->first();
        if ($existing) {
            DB::table('sidebar_items')->where('id', $existing->id)->update($data);

            return $existing->id;
        }

        return DB::table('sidebar_items')->insertGetId($data);
    }

    private function grantLegacyRoles($optionId, array $roles, $now, $auditUserId)
    {
        if (! $optionId || ! $auditUserId || ! Schema::hasTable('opcion_usuario')) {
            return;
        }

        foreach ($roles as $roleId) {
            $roleExists = ! Schema::hasTable('tipousuario') || DB::table('tipousuario')->where('id', $roleId)->exists();
            $exists = DB::table('opcion_usuario')
                ->where('id_opcion', $optionId)
                ->where('id_tipo_usuario', $roleId)
                ->exists();
            if (! $roleExists || $exists) {
                continue;
            }

            $data = [
                'id_opcion' => $optionId,
                'id_tipo_usuario' => $roleId,
                'estado' => 1,
                'id_usuariocrea' => $auditUserId,
                'id_usuariomod' => $auditUserId,
                'ip_creacion' => '127.0.0.1',
                'ip_modificacion' => '127.0.0.1',
                'created_at' => $now,
                'updated_at' => $now,
            ];
            DB::table('opcion_usuario')->insert($this->onlyExistingColumns('opcion_usuario', $data));
        }
    }

    private function auditUserId()
    {
        if (! Schema::hasTable('users')) {
            return null;
        }

        $systemUser = DB::table('users')->where('id_tipo_usuario', 1)->first();
        if ($systemUser) {
            return $systemUser->id;
        }
        $user = DB::table('users')->first();

        return $user ? $user->id : null;
    }

    private function onlyExistingColumns($table, array $data)
    {
        $columns = Schema::getColumnListing($table);

        return array_intersect_key($data, array_flip($columns));
    }

    public function down()
    {
        // Los permisos se conservan para evitar retirar accesos al revertir el despliegue.
        if (Schema::hasTable('sidebar_items')) {
            Schema::drop('sidebar_items');
        }
    }
}
