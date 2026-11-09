<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Sis_medico\Services\TenantContext;

class ResolveTenant
{
    private $tenants;

    public function __construct(TenantContext $tenants)
    {
        $this->tenants = $tenants;
    }

    public function handle($request, Closure $next)
    {
        // La instalacion debe funcionar aunque users y empresa aun no existan.
        // Evita que una sesion antigua intente consultar tablas no creadas.
        if ($request->is('/')
            || $request->is('login')
            || $request->is('instalacion')
            || $request->is('instalacion/*')) {
            return $next($request);
        }

        if (Auth::check()) {
            $available = $this->tenants->availableFor(Auth::user());
            $this->tenants->requireSelectionWhenNeeded($request, $available);
            $request->attributes->set('tenant.authorized_company_ids', $available
                ->pluck('id')
                ->map(function ($id) { return (string) $id; })
                ->values()
                ->all());

            View::share('tenantsDisponibles', $available);

            if ($this->tenants->selectionIsRequired($request)) {
                View::share('tenantActual', null);
                $routeName = $request->route() ? $request->route()->getName() : null;
                if (!in_array($routeName, array(
                    'tenant.select', 'tenant.switch', 'logout', 'profile',
                    'two-factor.settings.create', 'two-factor.settings.confirm',
                    'two-factor.settings.recovery', 'two-factor.settings.destroy',
                ), true)) {
                    return redirect()->route('tenant.select');
                }

                return $next($request);
            }

            $current = $this->tenants->resolve($request, Auth::user());
            View::share('tenantActual', $current);
        }

        return $next($request);
    }
}
