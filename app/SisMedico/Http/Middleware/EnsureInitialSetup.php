<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Services\InitialSetupService;

class EnsureInitialSetup
{
    public function handle($request, Closure $next)
    {
        // These endpoints must work before the users table exists. This also
        // avoids resolving a stale authenticated session against an empty DB.
        if ($this->isInstallationEntry($request)) {
            return $next($request);
        }

        if (Auth::check()
            && (int) Auth::user()->id_tipo_usuario === 1
            && (string) $request->input('configuracion_inicial') === '1') {
            $request->session()->put('initial_setup_return', true);
        }

        if (!Auth::check() || $this->isExempt($request)) {
            return $next($request);
        }

        $setup = app(InitialSetupService::class);
        if ($setup->isComplete()) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(array(
                'respuesta' => 'configuracion_pendiente',
                'msj' => 'Sistemas debe completar la configuración inicial correspondiente al tipo de empresa seleccionado.',
                'url' => route('initial-setup.index'),
            ), 409);
        }

        if ((int) Auth::user()->id_tipo_usuario === 1) {
            return redirect()->route('initial-setup.index');
        }

        return redirect()->route('initial-setup.index');
    }

    private function isExempt($request)
    {
        $patterns = array(
            'instalacion', 'instalacion/*',
            'configuracion-inicial', 'configuracion-inicial/*',
            // El doble factor se configura desde el perfil. Esta superficie
            // debe permanecer disponible antes de terminar la configuracion
            // inicial para evitar un ciclo profile <-> configuracion-inicial.
            'profile', 'profile/doble-factor/*',
            'seleccionar-empresa', 'empresa-activa',
            'empresa', 'empresa/*',
            'contable/establecimiento', 'contable/establecimiento/*',
            'hospital-management', 'hospital-management/*',
            'sala-management', 'sala-management/*',
            'form_enviar_seguro', 'form_enviar_seguro/*',
            'seguro/*', 'seguros/*',
            'sistema/referencias', 'sistema/referencias/*',
            'lang/*', 'logout',
            'avatars/*', 'logo/*',
        );

        foreach ($patterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    private function isInstallationEntry($request)
    {
        return $request->is('/')
            || $request->is('login')
            || $request->is('instalacion')
            || $request->is('instalacion/*');
    }
}
