<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Services\GlobalSessionVersion;

class EnforceGlobalSessionVersion
{
    const SESSION_KEY = 'aitrol_global_session_version';

    private $versions;

    public function __construct(GlobalSessionVersion $versions)
    {
        $this->versions = $versions;
    }

    public function handle($request, Closure $next)
    {
        // Una sesión del navegador puede sobrevivir a la eliminación de la
        // base. No intentes resolver ese usuario hasta que la instalación
        // haya creado nuevamente la tabla principal de autenticación.
        try {
            if (!Schema::hasTable('users')) {
                $request->session()->forget(self::SESSION_KEY);
                return $next($request);
            }
        } catch (\Exception $exception) {
            return $next($request);
        }

        if (!Auth::check()) {
            return $next($request);
        }

        // Authentication cookies are not an authorization to keep using a
        // disabled account. Re-read state through the authenticated provider
        // on every request and terminate the browser session immediately.
        if ((int) Auth::user()->estado !== 1) {
            $userId = Auth::id();
            Auth::logout();
            $request->session()->flush();
            $request->session()->regenerate();
            $request->session()->flash('global_logout_notice', 'Tu cuenta fue deshabilitada. Comunícate con Administración.');

            Log::warning('Sesión invalidada para una cuenta deshabilitada.', array(
                'user_id' => $userId,
                'ip' => $request->ip(),
            ));

            return redirect('/');
        }

        $current = $this->versions->current();
        $sessionVersion = $request->session()->get(self::SESSION_KEY);

        // Generation 1 accepts and stamps sessions created before deployment.
        // Once Systems rotates it, unstamped legacy sessions are also rejected.
        if ($sessionVersion === null && $current === 1) {
            $request->session()->put(self::SESSION_KEY, $current);
            return $next($request);
        }

        if ((int) $sessionVersion === $current) {
            return $next($request);
        }

        $userId = Auth::id();
        Auth::logout();
        $request->session()->flush();
        $request->session()->regenerate();
        $request->session()->flash('global_logout_notice', 'La sesión fue cerrada por seguridad desde Administración del sistema.');

        Log::info('Sesión invalidada por cierre global.', array(
            'user_id' => $userId,
            'ip' => $request->ip(),
        ));

        return redirect('/');
    }
}
