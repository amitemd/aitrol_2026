<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Sis_medico\Services\SecurityAccessAuditService;

/**
 * Emergency containment for legacy endpoints that can alter authentication
 * state or expose credentials without trustworthy authorization.
 */
class BlockCompromisedLegacyEndpoint
{
    private $securityAudit;

    public function __construct(SecurityAccessAuditService $securityAudit)
    {
        $this->securityAudit = $securityAudit;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, Closure $next)
    {
        $this->securityAudit->record('blocked_legacy_endpoint', 'blocked', $request, null,
            array('reason' => 'Ruta protegida por blocked.security'));
        Log::warning('Blocked request to compromised legacy endpoint.', [
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->header('User-Agent'), 0, 255),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(array(
                'error' => 'Esta operación ya no está disponible. Utiliza una opción vigente de Aitrol Hospitalario.',
            ), 404)
                ->header('Cache-Control', 'no-store, private')
                ->header('X-Content-Type-Options', 'nosniff');
        }

        $isLegacyLogin = $request->is('sub_login') || $request->is('sub_login/*');

        return response()->view('errors.blocked-legacy', array(
            'title' => $isLegacyLogin ? 'Enlace de acceso no disponible' : 'Opción no disponible',
            'heading' => $isLegacyLogin ? 'Este enlace de acceso ya no es válido' : 'Esta opción ya no está disponible',
            'message' => $isLegacyLogin
                ? 'Por seguridad, los enlaces directos de acceso fueron desactivados. Ingresa con tu correo y contraseña desde el acceso oficial de Aitrol Hospitalario.'
                : 'La dirección corresponde a una función anterior que fue retirada para proteger la información del sistema.',
        ), 404)
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Content-Type-Options', 'nosniff');
    }
}
