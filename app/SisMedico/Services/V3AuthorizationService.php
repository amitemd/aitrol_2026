<?php

namespace Sis_medico\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\v3\contable\Menu;
use Sis_medico\v3\contable\PermisosUsuario;
use Sis_medico\v3\contable\V3ActionPermission;
use Sis_medico\v3\contable\V3RoleActionPermission;

class V3AuthorizationService
{
    private $classifier;

    public function __construct(V3RequestClassifier $classifier)
    {
        $this->classifier = $classifier;
    }

    public function authorize(Request $request, $user)
    {
        $companyId = (string) $request->session()->get('id_empresa');
        if ($companyId === '' || !$this->belongsToCompany($request, $user, $companyId)) {
            return false;
        }
        if (method_exists($user, 'isSystemUser') && $user->isSystemUser()) {
            return true;
        }

        // Los comprobantes y archivos de una agenda forman parte del flujo
        // operativo compartido. Cualquier usuario autenticado vinculado a la
        // empresa activa puede consultarlos, visualizarlos y cargarlos, aunque
        // su rol no tenga permisos de modificación sobre la agenda. El borrado
        // conserva la autorización normal y el control del controlador.
        $route = $request->route();
        $routeName = $route ? (string) $route->getName() : '';
        if (in_array($routeName, array(
            'v3.doctor-agenda.files.index',
            'v3.doctor-agenda.files.store',
            'v3.doctor-agenda.files.show',
        ), true)) {
            return true;
        }

        // El inicio es el punto de entrada común de V3. Todo usuario activo
        // vinculado a la empresa debe poder verlo; el controlador presenta el
        // tablero correspondiente a su rol. Los módulos y acciones posteriores
        // continúan sometidos a sus permisos específicos.
        if ($request->isMethod('GET') && trim($request->path(), '/') === 'v3/admin') {
            return true;
        }

        $module = $this->classifier->module($request);
        $action = $this->classifier->action($request);
        $companyAccess = app(CompanyAccessService::class);
        $companyDecision = $companyAccess->decision(
            $user,
            $companyId,
            'v3.action.' . $module . '.' . $action
        );
        if ($companyDecision !== null) {
            return $companyDecision;
        }

        // Recepción administra el catálogo que utiliza para agendar y admitir
        // pacientes. No debe depender de una habilitación manual por empresa
        // para consultar, crear o actualizar especialidades. Una denegación
        // explícita continúa teniendo prioridad porque se resolvió arriba.
        if ($module === 'especialidades'
            && in_array($action, array('view', 'create', 'update'), true)
            && app(V3ReceptionDashboardService::class)->isReceptionist($user, $companyId)) {
            return true;
        }

        // Un rol por empresa reemplaza los privilegios históricos del tipo de
        // usuario. La lectura depende del permiso de menú; las demás acciones
        // deben estar concedidas expresamente y no recuperan permisos viejos.
        if ($companyAccess->hasAssignedRoles($user, $companyId)) {
            return $action === 'view' && $this->hasMenuPermission($user, $module, $this->classifier->submodule($request));
        }
        $explicit = V3ActionPermission::where('id_usuario', $user->id)
            ->where('id_empresa', $companyId)->where('modulo', $module)->where('accion', $action)->first();
        if ($explicit) {
            return (bool) $explicit->permitido;
        }

        // La política del rol es el permiso normal para instalaciones nuevas.
        // El permiso individual anterior se conserva como excepción compatible.
        if (Schema::hasTable('v3_role_action_permissions')) {
            $rolePermission = V3RoleActionPermission::where('id_tipo_usuario', $user->id_tipo_usuario)
                ->where('id_empresa', $companyId)->where('modulo', $module)->where('accion', $action)->first();
            if ($rolePermission) {
                return (bool) $rolePermission->permitido;
            }
        }

        // Compatibilidad de usuarios aún no migrados, después de comprobar
        // todas las decisiones explícitas, incluidas las denegaciones.
        if ($action === 'view'
            && in_array($module, array('agenda-clinica', 'pacientes', 'ordenes-laboratorio'), true)
            && app(V3ReceptionDashboardService::class)->isReceptionist($user, $companyId)) {
            return true;
        }

        // La lectura conserva los permisos de menú existentes. Las acciones
        // financieras mutables se niegan por defecto hasta ser otorgadas.
        return $action === 'view' && $this->hasMenuPermission($user, $module, $this->classifier->submodule($request));
    }

    private function belongsToCompany(Request $request, $user, $companyId)
    {
        $authorizedIds = $request->attributes->get('tenant.authorized_company_ids');
        if (is_array($authorizedIds)) {
            return in_array((string) $companyId, $authorizedIds, true);
        }

        // Conserva una verificación segura para usos directos del servicio
        // que no hayan atravesado todavía el middleware ResolveTenant.
        if (method_exists($user, 'isSystemUser') && $user->isSystemUser()) {
            return DB::table('empresa')->where('id', $companyId)->where('estado', 1)->exists();
        }
        return DB::table('usuario_empresa')->where('id_usuario', $user->id)->where('id_empresa', $companyId)->exists();
    }

    private function hasMenuPermission($user, $module, $submodule)
    {
        $permission = PermisosUsuario::getEffectivePermisos($user);
        $ids = $permission ? json_decode($permission->id_submenu_permiso, true) : array();
        if (!is_array($ids) || empty($ids)) return false;

        $query = Menu::where('controlador_menu', $module)->where('estado_menu', 1);
        if ($submodule !== '') {
            $exact = clone $query;
            $menu = $exact->where('funcion_menu', $submodule)->first();
            if ($menu) return in_array((int) $menu->id_menu, array_map('intval', $ids), true);
        }
        return $query->whereIn('id_menu', $ids)->exists();
    }
}
