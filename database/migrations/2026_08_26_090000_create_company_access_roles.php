<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCompanyAccessRoles extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('access_roles')) {
            Schema::create('access_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 20);
                $table->string('nombre', 100);
                $table->string('descripcion', 255)->nullable();
                $table->boolean('estado')->default(true);
                $table->string('origen', 30)->nullable();
                $table->string('referencia_origen', 60)->nullable();
                $table->string('creado_por', 20)->nullable();
                $table->string('modificado_por', 20)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'nombre'], 'access_roles_company_name_uq');
                $table->unique(['id_empresa', 'origen', 'referencia_origen'], 'access_roles_origin_uq');
                $table->index(['id_empresa', 'estado'], 'access_roles_company_status_idx');
            });
        }

        if (! Schema::hasTable('access_role_permissions')) {
            Schema::create('access_role_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('role_id');
                $table->string('permission_key', 120);
                $table->timestamps();
                $table->unique(['role_id', 'permission_key'], 'access_role_permission_uq');
                $table->index('permission_key', 'access_role_permission_key_idx');
            });
        }

        if (! Schema::hasTable('access_user_roles')) {
            Schema::create('access_user_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 20);
                $table->string('id_usuario', 20);
                $table->unsignedInteger('role_id');
                $table->string('asignado_por', 20)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'id_usuario', 'role_id'], 'access_user_role_uq');
                $table->index(['id_empresa', 'id_usuario'], 'access_user_role_scope_idx');
            });
        }

        if (! Schema::hasTable('access_user_permissions')) {
            Schema::create('access_user_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('id_empresa', 20);
                $table->string('id_usuario', 20);
                $table->string('permission_key', 120);
                $table->boolean('permitido')->default(true);
                $table->string('asignado_por', 20)->nullable();
                $table->timestamps();
                $table->unique(['id_empresa', 'id_usuario', 'permission_key'], 'access_user_permission_uq');
                $table->index(['id_empresa', 'id_usuario'], 'access_user_permission_scope_idx');
            });
        }

        $this->publishMenuItem();
        $this->migrateExistingAccess();
        $this->consolidateLegacyMenus();
    }

    private function publishMenuItem()
    {
        if (! Schema::hasTable('sidebar_items')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $parent = DB::table('sidebar_items')->where('clave', 'config.system_admin')->first();
        $optionId = null;

        if (Schema::hasTable('opcion')) {
            $option = Schema::hasColumn('opcion', 'clave')
                ? DB::table('opcion')->where('clave', 'system.access_control')->first()
                : null;
            if ($option) {
                $optionId = $option->id;
            } else {
                $actor = Schema::hasTable('users') ? DB::table('users')->where('id_tipo_usuario', 1)->first() : null;
                if ($actor) {
                    $data = [
                        'clave' => 'system.access_control',
                        'nombre' => 'Roles y permisos',
                        'descripcion' => 'Roles y permisos por empresa y usuario',
                        'ruta' => '/administracion/accesos',
                        'estado' => 1,
                        'id_usuariocrea' => $actor->id,
                        'id_usuariomod' => $actor->id,
                        'ip_creacion' => '127.0.0.1',
                        'ip_modificacion' => '127.0.0.1',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $data = array_intersect_key($data, array_flip(Schema::getColumnListing('opcion')));
                    $optionId = DB::table('opcion')->insertGetId($data);
                }
            }
        }

        $data = [
            'parent_id' => $parent ? $parent->id : null,
            'id_opcion' => $optionId,
            'clave' => 'system.access_control',
            'etiqueta' => 'Roles y permisos',
            'tipo' => 'enlace',
            'nombre_ruta' => 'access-control.index',
            'url' => '/administracion/accesos',
            'icono' => 'fa fa-shield',
            'orden' => 85,
            'patrones_activos' => 'administracion/accesos',
            'fijable' => 1,
            'estado' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $existing = DB::table('sidebar_items')->where('clave', 'system.access_control')->first();
        if ($existing) {
            DB::table('sidebar_items')->where('id', $existing->id)->update($data);
        } else {
            DB::table('sidebar_items')->insert($data);
        }

        if ($optionId && Schema::hasTable('opcion_usuario') && Schema::hasTable('users')) {
            $actor = DB::table('users')->where('id_tipo_usuario', 1)->first();
            $exists = DB::table('opcion_usuario')->where('id_opcion', $optionId)->where('id_tipo_usuario', 1)->exists();
            if ($actor && ! $exists) {
                $grant = [
                    'id_opcion' => $optionId, 'id_tipo_usuario' => 1, 'estado' => 1,
                    'id_usuariocrea' => $actor->id, 'id_usuariomod' => $actor->id,
                    'ip_creacion' => '127.0.0.1', 'ip_modificacion' => '127.0.0.1',
                    'created_at' => $now, 'updated_at' => $now,
                ];
                DB::table('opcion_usuario')->insert(array_intersect_key($grant, array_flip(Schema::getColumnListing('opcion_usuario'))));
            }
        }
    }

    /**
     * Consolida los tres esquemas anteriores sin borrar sus datos. A partir de
     * aquí access_* es la fuente única; las tablas históricas quedan como
     * respaldo para instalaciones que necesiten auditar la transición.
     */
    public function migrateExistingAccess()
    {
        if (! Schema::hasTable('empresa') || ! Schema::hasTable('users')) {
            return;
        }
        $companies = DB::table('empresa')->pluck('id')->toArray();
        $actor = DB::table('users')->where('id_tipo_usuario', 1)->first();
        $actorId = $actor ? $actor->id : null;
        $now = date('Y-m-d H:i:s');

        if (Schema::hasTable('tipousuario')) {
            foreach ($companies as $companyId) {
                foreach (DB::table('tipousuario')->get() as $profile) {
                    $roleId = $this->upsertImportedRole($companyId, $profile->nombre,
                        isset($profile->descripcion) ? $profile->descripcion : null,
                        isset($profile->estado) ? $profile->estado : 1,
                        'tipousuario', $profile->id, $actorId, $now);
                    $this->importProfilePermissions($roleId, $companyId, $profile->id, $now);

                    if (Schema::hasTable('usuario_empresa')) {
                        $users = DB::table('users as user')
                            ->join('usuario_empresa as membership', 'membership.id_usuario', '=', 'user.id')
                            ->where('membership.id_empresa', $companyId)
                            ->where('user.id_tipo_usuario', $profile->id)->pluck('user.id')->toArray();
                        foreach ($users as $userId) {
                            $this->insertUserRole($companyId, $userId, $roleId, $actorId, $now);
                        }
                    }
                }
            }
        }

        if (Schema::hasTable('new_db_roles_usuarios')) {
            foreach ($companies as $companyId) {
                foreach (DB::table('new_db_roles_usuarios')->get() as $legacyRole) {
                    $roleId = $this->upsertImportedRole($companyId, $legacyRole->nombre_rol,
                        'Rol importado desde la administración V3 anterior.', 1,
                        'new_db_roles_usuarios', $legacyRole->id_rol, $actorId, $now);
                    $keys = [];
                    foreach ((array) json_decode($legacyRole->id_submodulos_rol, true) as $value) {
                        if (preg_match('/^(crear|actualizar|leer)_(\d+)$/', (string) $value, $match)) {
                            $menuId = (int) $match[2];
                            $keys[] = 'v3.menu.'.$menuId;
                            if (Schema::hasTable('new_db_menu')) {
                                $menu = DB::table('new_db_menu')->where('id_menu', $menuId)->first();
                                if ($menu && ! empty($menu->controlador_menu)
                                    && ! in_array($menu->controlador_menu, ['legacy-route', 'legacy-url'], true)) {
                                    $actions = ['crear' => 'create', 'actualizar' => 'update', 'leer' => 'view'];
                                    $keys[] = 'v3.action.'.$menu->controlador_menu.'.'.$actions[$match[1]];
                                }
                            }
                        } elseif (preg_match('/_(\d+)$/', (string) $value, $match)) {
                            $keys[] = 'v3.menu.'.(int) $match[1];
                        }
                    }
                    foreach ((array) json_decode($legacyRole->id_permisos_rol, true) as $menuId) {
                        if ((int) $menuId > 0) {
                            $keys[] = 'v3.menu.'.(int) $menuId;
                        }
                    }
                    $this->insertRolePermissions($roleId, $keys, $now);
                }
            }
        }

        $this->importDirectV3MenuPermissions($companies, $actorId, $now);
        $this->importDirectV3ActionPermissions($actorId, $now);
    }

    public function consolidateLegacyMenus()
    {
        $now = date('Y-m-d H:i:s');
        if (Schema::hasTable('sidebar_items')) {
            DB::table('sidebar_items')->where('clave', 'system.user_types')->update([
                'estado' => 0, 'updated_at' => $now,
            ]);
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('controlador_menu', 'configuraciones')
                ->where('funcion_menu', 'permisos')->update(['estado_menu' => 0, 'updated_at_menu' => $now]);
            DB::table('new_db_menu')->where('controlador_menu', 'configuraciones')
                ->where('funcion_menu', 'roles')->update([
                    'nombre_menu' => 'Roles y permisos',
                    'icon_menu' => '<i class="fas fa-user-shield nav-icon"></i>',
                    'updated_at_menu' => $now,
                ]);
        }
    }

    private function upsertImportedRole($companyId, $name, $description, $status, $origin, $sourceId, $actorId, $now)
    {
        $role = DB::table('access_roles')->where('id_empresa', $companyId)
            ->where('origen', $origin)->where('referencia_origen', (string) $sourceId)->first();
        if ($role) {
            return $role->id;
        }

        $sameName = DB::table('access_roles')->where('id_empresa', $companyId)->where('nombre', $name)->first();
        if ($sameName) {
            return $sameName->id;
        }

        return DB::table('access_roles')->insertGetId([
            'id_empresa' => $companyId, 'nombre' => $name, 'descripcion' => $description,
            'estado' => $status ? 1 : 0, 'origen' => $origin, 'referencia_origen' => (string) $sourceId,
            'creado_por' => $actorId, 'modificado_por' => $actorId, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    private function importProfilePermissions($roleId, $companyId, $profileId, $now)
    {
        $keys = [];
        if (Schema::hasTable('opcion_usuario') && Schema::hasTable('sidebar_items')) {
            $keys = DB::table('opcion_usuario as grant')
                ->join('sidebar_items as item', 'item.id_opcion', '=', 'grant.id_opcion')
                ->where('grant.id_tipo_usuario', $profileId)->where('grant.estado', 1)
                ->pluck('item.clave')->toArray();
            if (in_array('system.user_types', $keys, true)) {
                $keys[] = 'system.access_control';
            }
        }
        if (Schema::hasTable('v3_role_menu_permissions')) {
            foreach (DB::table('v3_role_menu_permissions')->where('id_empresa', $companyId)
                ->where('id_tipo_usuario', $profileId)->where('permitido', 1)->pluck('id_menu') as $menuId) {
                $keys[] = 'v3.menu.'.(int) $menuId;
            }
        }
        if (Schema::hasTable('v3_role_action_permissions')) {
            foreach (DB::table('v3_role_action_permissions')->where('id_empresa', $companyId)
                ->where('id_tipo_usuario', $profileId)->where('permitido', 1)->get() as $permission) {
                $keys[] = 'v3.action.'.$permission->modulo.'.'.$permission->accion;
            }
        }
        $this->insertRolePermissions($roleId, $keys, $now);
    }

    private function insertRolePermissions($roleId, array $keys, $now)
    {
        foreach (array_values(array_unique(array_filter($keys))) as $key) {
            if (! DB::table('access_role_permissions')->where('role_id', $roleId)->where('permission_key', $key)->exists()) {
                DB::table('access_role_permissions')->insert(['role_id' => $roleId,
                    'permission_key' => $key, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    private function insertUserRole($companyId, $userId, $roleId, $actorId, $now)
    {
        $scope = ['id_empresa' => $companyId, 'id_usuario' => $userId, 'role_id' => $roleId];
        if (! DB::table('access_user_roles')->where($scope)->exists()) {
            DB::table('access_user_roles')->insert(array_merge($scope, ['asignado_por' => $actorId,
                'created_at' => $now, 'updated_at' => $now]));
        }
    }

    private function importDirectV3MenuPermissions(array $companies, $actorId, $now)
    {
        if (! Schema::hasTable('new_db_permisos_usuario')) {
            return;
        }
        foreach (DB::table('new_db_permisos_usuario')->get() as $legacy) {
            $targetCompanies = $legacy->id_empresa_permiso
                ? [$legacy->id_empresa_permiso]
                : (Schema::hasTable('usuario_empresa')
                    ? DB::table('usuario_empresa')->where('id_usuario', $legacy->id_usuario_permiso)->pluck('id_empresa')->toArray()
                    : $companies);
            foreach ($targetCompanies as $companyId) {
                foreach ((array) json_decode($legacy->id_submenu_permiso, true) as $menuId) {
                    if ((int) $menuId > 0) {
                        $this->upsertUserPermission($companyId, $legacy->id_usuario_permiso,
                            'v3.menu.'.(int) $menuId, 1, $actorId, $now);
                    }
                }
            }
        }
    }

    private function importDirectV3ActionPermissions($actorId, $now)
    {
        if (! Schema::hasTable('v3_action_permissions')) {
            return;
        }
        foreach (DB::table('v3_action_permissions')->get() as $legacy) {
            $this->upsertUserPermission($legacy->id_empresa, $legacy->id_usuario,
                'v3.action.'.$legacy->modulo.'.'.$legacy->accion,
                $legacy->permitido ? 1 : 0, $actorId, $now);
        }
    }

    private function upsertUserPermission($companyId, $userId, $key, $allowed, $actorId, $now)
    {
        if (! $companyId || ! $userId) {
            return;
        }
        $scope = ['id_empresa' => $companyId, 'id_usuario' => $userId, 'permission_key' => $key];
        if (DB::table('access_user_permissions')->where($scope)->exists()) {
            DB::table('access_user_permissions')->where($scope)->update(['permitido' => $allowed,
                'asignado_por' => $actorId, 'updated_at' => $now]);
        } else {
            DB::table('access_user_permissions')->insert(array_merge($scope, ['permitido' => $allowed,
                'asignado_por' => $actorId, 'created_at' => $now, 'updated_at' => $now]));
        }
    }

    public function down()
    {
        if (Schema::hasTable('sidebar_items')) {
            DB::table('sidebar_items')->where('clave', 'system.user_types')->update(['estado' => 1]);
        }
        if (Schema::hasTable('new_db_menu')) {
            DB::table('new_db_menu')->where('controlador_menu', 'configuraciones')
                ->where('funcion_menu', 'permisos')->update(['estado_menu' => 1]);
            DB::table('new_db_menu')->where('controlador_menu', 'configuraciones')
                ->where('funcion_menu', 'roles')->update(['nombre_menu' => 'Roles de usuarios']);
        }
        if (Schema::hasTable('sidebar_items')) {
            DB::table('sidebar_items')->where('clave', 'system.access_control')->delete();
        }
        if (Schema::hasTable('opcion') && Schema::hasColumn('opcion', 'clave')) {
            $option = DB::table('opcion')->where('clave', 'system.access_control')->first();
            if ($option && Schema::hasTable('opcion_usuario')) {
                DB::table('opcion_usuario')->where('id_opcion', $option->id)->delete();
            }
            DB::table('opcion')->where('clave', 'system.access_control')->delete();
        }
        Schema::dropIfExists('access_user_permissions');
        Schema::dropIfExists('access_user_roles');
        Schema::dropIfExists('access_role_permissions');
        Schema::dropIfExists('access_roles');
    }
}
