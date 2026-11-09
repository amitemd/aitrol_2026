<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Services\SecurityAccessAuditService;
use Sis_medico\Services\UserNetworkAccessService;

class EnforceUserNetworkAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $decision = app(UserNetworkAccessService::class)->evaluate(Auth::user(), $request);
            if (!$decision['allowed']) {
                app(SecurityAccessAuditService::class)->record('network_access_blocked', 'blocked', $request, Auth::user(),
                    array('reason' => 'Fuera de la politica de acceso asignada'));
                Auth::logout();
                $request->session()->flush();
                $request->session()->regenerate();
                return redirect('/')->withErrors(array('system' => 'Acceso no autorizado por la política de acceso asignada.'));
            }
        }
        return $next($request);
    }
}
