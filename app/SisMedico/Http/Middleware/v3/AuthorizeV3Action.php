<?php

namespace Sis_medico\Http\Middleware\v3;

use Closure;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Services\V3AuthorizationService;

class AuthorizeV3Action
{
    private $authorization;

    public function __construct(V3AuthorizationService $authorization)
    {
        $this->authorization = $authorization;
    }

    public function handle($request, Closure $next)
    {
        if (!Auth::check() || !$this->authorization->authorize($request, Auth::user())) {
            if ($request->expectsJson()) return response()->json(array('message' => 'Accion no autorizada.'), 403);
            abort(403, 'Accion V3 no autorizada para esta empresa.');
        }
        return $next($request);
    }
}
