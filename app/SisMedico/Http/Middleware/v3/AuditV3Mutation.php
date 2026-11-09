<?php

namespace Sis_medico\Http\Middleware\v3;

use Closure;
use Sis_medico\Services\V3AuditService;

class AuditV3Mutation
{
    private $audit;

    public function __construct(V3AuditService $audit)
    {
        $this->audit = $audit;
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (!$request->isMethod('GET') && !$request->attributes->get('v3.audit.recorded')
            && method_exists($response, 'getStatusCode') && $response->getStatusCode() < 400) {
            $this->audit->record($request, array('reason' => 'Mutacion V3 completada'));
        }
        return $response;
    }
}
