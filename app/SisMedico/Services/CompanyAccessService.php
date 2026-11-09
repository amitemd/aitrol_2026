<?php

namespace Sis_medico\Services;

use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Support\RequestQueryCache;

class CompanyAccessService
{
    /**
     * Return true/false when the new company policy is configured, or null
     * when the application must keep using its historical permissions.
     */
    public function decision($user, $companyId, $permissionKey)
    {
        if (!$user || !$companyId || !$this->available()) {
            return null;
        }

        $policy = $this->policy($user, $companyId);
        $equivalentKeys = app(PermissionAliasService::class)->equivalentKeys($permissionKey);
        $equivalentDirect = array_intersect_key($policy['direct'], array_fill_keys($equivalentKeys, true));
        // Una denegación individual equivalente conserva prioridad sobre
        // permisos concedidos por cualquiera de los dos catálogos.
        if (in_array(false, $equivalentDirect, true)) return false;
        if (in_array(true, $equivalentDirect, true)) return true;
        if (!$policy['has_roles']) return null;

        // Sin acciones explícitas para el módulo, el autorizador puede revisar
        // la lectura del menú. hasAssignedRoles() le impide recuperar acciones
        // heredadas cuando el usuario ya está administrado por roles de empresa.
        if (strpos($permissionKey, 'v3.action.') === 0) {
            $lastSeparator = strrpos($permissionKey, '.');
            $modulePrefix = substr($permissionKey, 0, $lastSeparator + 1);
            if (!isset($policy['modules'][$modulePrefix])) return null;
        }

        foreach ($equivalentKeys as $key) {
            if (isset($policy['allowed'][$key])) return true;
        }

        // La lectura se gobierna por la opción de menú. Un rol puede tener,
        // por ejemplo, Modificar especialidades además de v3.menu.684; la
        // ausencia de una acción view separada no debe hacer visible una
        // opción que luego responde 403. Las denegaciones individuales ya se
        // resolvieron arriba y conservan prioridad.
        if (preg_match('/^v3\.action\.[^.]+\.view$/', $permissionKey)) {
            return null;
        }
        return false;
    }

    public function hasAssignedRoles($user, $companyId)
    {
        return $user && $companyId && $this->available() && $this->policy($user, $companyId)['has_roles'];
    }

    private function policy($user, $companyId)
    {
        return RequestQueryCache::remember(__METHOD__.json_encode([$companyId, $user->id]), function () use ($companyId, $user) {
            return $this->loadPolicy($companyId, $user->id);
        }, null, false, ['access_roles', 'access_role_permissions', 'access_user_roles', 'access_user_permissions']);
    }

    private function loadPolicy($companyId, $userId)
    {
        $policy = ['direct' => [], 'has_roles' => false, 'modules' => [], 'allowed' => []];
        $direct = DB::table('access_user_permissions')->where('id_empresa', $companyId)
            ->where('id_usuario', $userId)->get(['permission_key', 'permitido']);
        foreach ($direct as $permission) {
            if (!array_key_exists($permission->permission_key, $policy['direct'])) {
                $policy['direct'][$permission->permission_key] = (bool) $permission->permitido;
            }
        }
        $roleIds = DB::table('access_user_roles')->where('id_empresa', $companyId)
            ->where('id_usuario', $userId)->pluck('role_id')->all();
        $policy['has_roles'] = !empty($roleIds);
        if (!$policy['has_roles']) return $policy;

        // Cargar la política completa evita 2–4 consultas por cada opción del menú.
        $permissions = DB::table('access_role_permissions as permission')
            ->leftJoin('access_roles as role', 'role.id', '=', 'permission.role_id')
            ->whereIn('permission.role_id', $roleIds)
            ->get(['permission.permission_key', 'role.id_empresa', 'role.estado']);
        foreach ($permissions as $permission) {
            $key = $permission->permission_key;
            if (strpos($key, 'v3.action.') === 0) {
                $policy['modules'][substr($key, 0, strrpos($key, '.') + 1)] = true;
            }
            if ((string) $permission->id_empresa === (string) $companyId && (int) $permission->estado === 1) {
                $policy['allowed'][$key] = true;
            }
        }
        return $policy;
    }

    public function available()
    {
        return Schema::hasTable('access_roles')
            && Schema::hasTable('access_role_permissions')
            && Schema::hasTable('access_user_roles')
            && Schema::hasTable('access_user_permissions');
    }
}
