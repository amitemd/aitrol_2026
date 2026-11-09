<?php

namespace Sis_medico\Http\Middleware\v3;

use Closure;
use Sis_medico\Services\V3AccountingIncidentService;

class CaptureV3AccountingIncidents
{
    private $incidents;

    public function __construct(V3AccountingIncidentService $incidents)
    {
        $this->incidents = $incidents;
    }

    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (\Throwable $exception) {
            if ($request->is('v3/contabilidad*') || $request->is('v3/reportes*') || $request->is('v3/cajabancos*')) {
                try { $this->incidents->captureException($request, $exception); } catch (\Throwable $ignored) {}
            }
            throw $exception;
        }
    }
}
