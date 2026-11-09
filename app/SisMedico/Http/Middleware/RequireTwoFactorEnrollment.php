<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Services\SecurityAccessAuditService;
use Sis_medico\Services\TwoFactorEnrollmentGrant;

class RequireTwoFactorEnrollment
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->requiresTwoFactorEnrollment()) {
            return $next($request);
        }

        $user = Auth::user();
        if (!app(TwoFactorEnrollmentGrant::class)->allows($request, $user)) {
            app(SecurityAccessAuditService::class)->record(
                'two_factor_enrollment_session_denied', 'blocked', $request, $user,
                array('reason' => 'Sesión sin autorización de recuperación por correo')
            );
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('password.request')->with(
                'reset_warning',
                'Para activar Authenticator debes verificar primero tu correo y cambiar la contraseña.'
            );
        }

        if ($this->isExempt($request)) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(array(
                'respuesta' => 'two_factor_required',
                'msj' => 'Debes configurar la autenticación en dos pasos antes de continuar.',
                'url' => route('profile'),
            ), 409);
        }

        return redirect()->route('profile')->with('two_factor_required', true);
    }

    private function isExempt($request)
    {
        foreach (array('profile', 'profile/doble-factor/*', 'logout') as $pattern) {
            if ($request->is($pattern)) return true;
        }
        return false;
    }
}
