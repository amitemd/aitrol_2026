<?php

namespace App\Compatibility;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LegacyUnavailableController extends Controller
{
    public function __call(string $method, array $parameters): JsonResponse|Response
    {
        $payload = [
            'message' => 'Esta función estaba declarada en las rutas, pero su controlador no existe en el proyecto fuente.',
            'route' => request()->route()?->getName(),
            'action' => request()->route()?->getActionName(),
            'method' => $method,
        ];

        if (request()->expectsJson()) {
            return response()->json($payload, 501);
        }

        return response()->view('errors.legacy-unavailable', $payload, 501);
    }
}
