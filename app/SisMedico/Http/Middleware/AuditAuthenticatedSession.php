<?php

namespace Sis_medico\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Services\SecurityAccessAuditService;

class AuditAuthenticatedSession
{
    private $audit;

    public function __construct(SecurityAccessAuditService $audit)
    {
        $this->audit = $audit;
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (!Auth::check() || !$request->hasSession()) return $response;

        $signature = $this->audit->signature($request);
        $lastSignature = (string) $request->session()->get('security_audit_signature', '');
        $lastAt = (int) $request->session()->get('security_audit_last_at', 0);
        $interval = max(1, (int) config('security_audit.activity_interval_minutes', 15)) * 60;
        if ($signature !== $lastSignature || (time() - $lastAt) >= $interval) {
            $this->audit->record('session_activity', 'success', $request, Auth::user());
            $request->session()->put('security_audit_signature', $signature);
            $request->session()->put('security_audit_last_at', time());
        }
        return $response;
    }
}
