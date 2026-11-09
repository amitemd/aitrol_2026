<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Sis_medico\Services\SecurityAccessAuditService;

/**
 * Permanent fail-closed containment for retired legacy route collections.
 */
class LegacyEndpointFirewall
{
    private $securityAudit;

    public function __construct(SecurityAccessAuditService $securityAudit)
    {
        $this->securityAudit = $securityAudit;
    }

    public function handle($request, Closure $next, $scope = 'blocked')
    {
        $this->securityAudit->record('blocked_legacy_endpoint', 'blocked', $request, null,
            array('reason' => 'Colección heredada retirada y cerrada permanentemente: ' . $scope));

        Log::warning('Legacy endpoint denied by the central firewall.', array(
            'scope' => $scope,
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->header('User-Agent'), 0, 255),
        ));

        return response()->json(array(
            'error' => 'Recurso no disponible.',
        ), 404)
            ->header('Cache-Control', 'no-store, private')
            ->header('X-Content-Type-Options', 'nosniff');
    }
}
