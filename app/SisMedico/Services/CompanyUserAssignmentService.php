<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\User;

class CompanyUserAssignmentService
{
    public function queryForCompanyAdministration($companyId)
    {
        // La administración debe poder abrir también altas antiguas con persona
        // pendiente de sincronizar. El acceso se comprueba con usuario_empresa.
        $query = User::withoutGlobalScope('active_company');
        $companyId = trim((string) $companyId);
        if ($companyId === '' || !Schema::hasTable('usuario_empresa')) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereExists(function ($membership) use ($companyId) {
            $membership->select(DB::raw(1))->from('usuario_empresa as membership')
                ->join('empresa as assigned_company', 'assigned_company.id', '=', 'membership.id_empresa')
                ->whereColumn('membership.id_usuario', 'users.id')
                ->where('membership.id_empresa', $companyId)
                ->where('assigned_company.estado', 1);
        });
    }

    public function usersFor($companyId)
    {
        $companyId = (string) $companyId;
        $users = DB::table('users as user')
            ->leftJoin('tipousuario as type', 'type.id', '=', 'user.id_tipo_usuario')
            ->where('user.estado', 1)
            ->orderBy('user.apellido1')->orderBy('user.nombre1')
            ->get(array(
                'user.id', 'user.nombre1', 'user.nombre2', 'user.apellido1', 'user.apellido2',
                'user.email', 'type.nombre as tipo_usuario',
            ));

        $members = DB::table('usuario_empresa')->where('id_empresa', $companyId)
            ->pluck('id_usuario')->map(function ($id) { return (string) $id; })->flip();
        $rolesByUser = collect();
        if (Schema::hasTable('access_user_roles')) {
            $rolesByUser = DB::table('access_user_roles')->where('id_empresa', $companyId)
                ->orderBy('role_id')->get(array('id_usuario', 'role_id'))->groupBy('id_usuario');
        }

        return $users->map(function ($user) use ($members, $rolesByUser) {
            $user->nombre_completo = trim($user->apellido1.' '.$user->apellido2.' '.$user->nombre1.' '.$user->nombre2);
            $user->asignado = $members->has((string) $user->id);
            $user->role_ids = $rolesByUser->has((string) $user->id)
                ? $rolesByUser->get((string) $user->id)->pluck('role_id')->map(function ($id) { return (string) $id; })->values()->all()
                : array();
            return $user;
        });
    }

    public function save($companyId, $userId, $assigned, array $roleIds, $actorId, $ip)
    {
        $companyId = trim((string) $companyId);
        $userId = trim((string) $userId);
        if ($companyId === '' || !DB::table('empresa')->where('id', $companyId)->where('estado', 1)->exists()) {
            throw new \InvalidArgumentException('La empresa activa no está disponible.');
        }
        if (!DB::table('users')->where('id', $userId)->where('estado', 1)->exists()) {
            throw new \InvalidArgumentException('El usuario seleccionado no está disponible.');
        }

        return DB::transaction(function () use ($companyId, $userId, $assigned, $roleIds, $actorId, $ip) {
            if (!$assigned) {
                if ((string) $userId === (string) $actorId) {
                    throw new \InvalidArgumentException('No puede quitarse a sí mismo de la empresa que está administrando.');
                }
                DB::table('usuario_empresa')->where('id_empresa', $companyId)->where('id_usuario', $userId)->delete();
                $personId = DB::table('users')->where('id', $userId)->value('persona_id');
                app(PersonRoleTenantService::class)->setRole($personId, 'USUARIO', false, $companyId, $actorId, $ip);
                $this->clearCompanyPermissions($companyId, $userId);
                return array('assigned' => false, 'message' => 'El acceso del usuario a esta empresa fue retirado.');
            }

            $this->ensureMembership($companyId, $userId, $actorId, $ip);
            $personId = DB::table('users')->where('id', $userId)->value('persona_id');
            app(PersonRoleTenantService::class)->setRole($personId, 'USUARIO', true, $companyId, $actorId, $ip);
            $this->syncRoles($companyId, $userId, $roleIds, $actorId);
            return array('assigned' => true, 'message' => 'El usuario fue asignado correctamente a la empresa activa.');
        });
    }

    public function ensureMembership($companyId, $userId, $actorId, $ip)
    {
        $companyId = trim((string) $companyId);
        $userId = trim((string) $userId);
        if ($companyId === '' || !DB::table('empresa')->where('id', $companyId)->where('estado', 1)->exists()) {
            throw new \InvalidArgumentException('La empresa activa no está disponible.');
        }
        if (!Schema::hasTable('usuario_empresa')) {
            throw new \RuntimeException('No existe la tabla de asignación de usuarios por empresa.');
        }

        return DB::transaction(function () use ($companyId, $userId, $actorId, $ip) {
            // El usuario recién creado todavía no tiene el vínculo de persona
            // requerido por el scope de empresa. También se admiten inactivos.
            $user = User::withoutGlobalScope('active_company')->findOrFail($userId);
            if (!DB::table('usuario_empresa')->where('id_empresa', $companyId)->where('id_usuario', $userId)->exists()) {
                $now = date('Y-m-d H:i:s');
                $values = array(
                    'id_empresa' => $companyId, 'id_usuario' => $userId,
                    'id_usuariocrea' => $actorId, 'id_usuariomod' => $actorId,
                    'ip_creacion' => $ip, 'ip_modificacion' => $ip,
                    'created_at' => $now, 'updated_at' => $now,
                );
                DB::table('usuario_empresa')->insert(array_intersect_key(
                    $values, array_flip(Schema::getColumnListing('usuario_empresa'))
                ));
            }

            // Los inserts directos de V3 no ejecutan el evento saved de User.
            // Sincronizar incluso si ya existe usuario_empresa repara altas incompletas.
            app(PersonIdentityService::class)->sync($user, 'USUARIO', 'id', 'NATURAL', $companyId);
        });
    }

    private function syncRoles($companyId, $userId, array $roleIds, $actorId)
    {
        if (!Schema::hasTable('access_roles') || !Schema::hasTable('access_user_roles')) return;
        $availableRoles = DB::table('access_roles')->where('id_empresa', $companyId)->where('estado', 1);
        $validRoleIds = DB::table('access_roles')->where('id_empresa', $companyId)->where('estado', 1)
            ->whereIn('id', array_filter($roleIds))->pluck('id')->all();
        if ((clone $availableRoles)->exists() && count($validRoleIds) === 0) {
            throw new \InvalidArgumentException('Seleccione al menos un rol para el usuario dentro de esta empresa.');
        }
        DB::table('access_user_roles')->where('id_empresa', $companyId)->where('id_usuario', $userId)->delete();
        $now = date('Y-m-d H:i:s');
        foreach ($validRoleIds as $roleId) {
            DB::table('access_user_roles')->insert(array(
                'id_empresa' => $companyId, 'id_usuario' => $userId, 'role_id' => $roleId,
                'asignado_por' => $actorId, 'created_at' => $now, 'updated_at' => $now,
            ));
        }
    }

    private function clearCompanyPermissions($companyId, $userId)
    {
        $tables = array(
            array('access_user_roles', 'id_empresa', 'id_usuario'),
            array('access_user_permissions', 'id_empresa', 'id_usuario'),
            array('v3_action_permissions', 'id_empresa', 'id_usuario'),
            array('new_db_permisos_usuario', 'id_empresa_permiso', 'id_usuario_permiso'),
        );
        foreach ($tables as $definition) {
            if (Schema::hasTable($definition[0])) {
                DB::table($definition[0])->where($definition[1], $companyId)->where($definition[2], $userId)->delete();
            }
        }
    }
}
