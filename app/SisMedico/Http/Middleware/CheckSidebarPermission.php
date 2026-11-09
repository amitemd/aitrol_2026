<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Sis_medico\Services\SidebarMenuService;

class CheckSidebarPermission
{
    private $menu;

    public function __construct(SidebarMenuService $menu)
    {
        $this->menu = $menu;
    }

    public function handle($request, Closure $next, $permission)
    {
        if (!$this->menu->can($permission)) {
            abort(403, 'No tiene permiso para acceder a esta opción.');
        }
        return $next($request);
    }
}
