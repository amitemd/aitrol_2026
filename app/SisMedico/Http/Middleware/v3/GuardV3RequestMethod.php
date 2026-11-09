<?php

namespace Sis_medico\Http\Middleware\v3;

use Closure;
use Sis_medico\Services\V3RequestClassifier;

class GuardV3RequestMethod
{
    private $classifier;

    public function __construct(V3RequestClassifier $classifier)
    {
        $this->classifier = $classifier;
    }

    public function handle($request, Closure $next)
    {
        if (in_array($request->method(), array('GET', 'HEAD'), true) && $this->classifier->isMutationName($this->classifier->operationName($request))) {
            return response()->json(array(
                'message' => 'Esta operacion requiere POST y un token CSRF valido.',
                'code' => 'V3_METHOD_NOT_ALLOWED',
            ), 405, array('Allow' => 'POST'));
        }
        return $next($request);
    }
}
