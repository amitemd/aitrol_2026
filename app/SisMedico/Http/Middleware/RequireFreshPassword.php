<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Support\RequestSchema as Schema;

class RequireFreshPassword
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || $this->isExempt($request) || !$this->policyIsAvailable()) {
            return $next($request);
        }

        if (!Auth::user()->passwordHasExpired()) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(array(
                'respuesta' => 'password_expired',
                'msj' => 'Tu contraseña venció. Debes cambiarla para continuar.',
                'url' => route('password.expired'),
            ), 423);
        }

        return redirect()->route('password.expired');
    }

    private function isExempt($request)
    {
        return $request->is('clave-vencida') || $request->is('logout');
    }

    private function policyIsAvailable()
    {
        try {
            return Schema::hasTable('users') && Schema::hasColumn('users', 'password_changed_at');
        } catch (\Exception $exception) {
            // El despliegue no debe impedir ejecutar la migración.
            return false;
        }
    }
}
