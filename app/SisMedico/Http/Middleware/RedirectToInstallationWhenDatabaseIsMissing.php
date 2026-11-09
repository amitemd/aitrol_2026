<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Services\InitialSetupService;

class RedirectToInstallationWhenDatabaseIsMissing
{
    public function handle($request, Closure $next)
    {
        // Existing deployments must never be redirected to the one-time
        // installer merely because their legacy users do not match the
        // bootstrap identities. Fresh installation is an explicit mode.
        if (!(bool) config('authenticated_surface.installation_enabled', false)
            || $this->isInstallationRoute($request)) {
            return $next($request);
        }

        try {
            $bootstrapReady = Schema::hasTable('users')
                && Schema::hasTable('migrations')
                && app(InitialSetupService::class)->bootstrapIsReady();
        } catch (\Throwable $exception) {
            $bootstrapReady = false;
        }

        if ($bootstrapReady) {
            return $next($request);
        }

        // El navegador puede conservar una sesión de la base anterior. Se
        // descarta antes de entrar al instalador para no resolver ese usuario.
        $request->session()->flush();
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json(array(
                'respuesta' => 'instalacion_requerida',
                'msj' => 'La base de datos debe instalarse antes de utilizar Aitrol.',
                'url' => route('installation.index'),
            ), 503);
        }

        return redirect()->route('installation.index');
    }

    private function isInstallationRoute($request)
    {
        return $request->is('instalacion') || $request->is('instalacion/*');
    }
}
