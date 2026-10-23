<?php

namespace Sis_medico\Http\Middleware\v3;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OperacionMonitoreo
{
    /**
     * Registra una metrica compacta de cada peticion protegida de v3.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $inicio = microtime(true);
        try {
            $response = $next($request);
        } catch (\Exception $e) {
            $this->registrar('error', 'v3.excepcion_no_controlada', [
                'metodo' => $request->method(),
                'ruta' => $this->rutaSegura($request),
                'duracion_ms' => round((microtime(true) - $inicio) * 1000, 2),
                'usuario_id' => Auth::check() ? Auth::user()->id : null,
                'empresa_id' => session('id_empresa'),
                'tipo' => get_class($e),
            ]);
            throw $e;
        }
        $duracion = round((microtime(true) - $inicio) * 1000, 2);
        $estado = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
        $limiteLento = (int) config('monitoreo_v3.slow_request_ms', 2000);

        $contexto = [
            'metodo' => $request->method(),
            'ruta' => $this->rutaSegura($request),
            'estado_http' => $estado,
            'duracion_ms' => $duracion,
            'usuario_id' => Auth::check() ? Auth::user()->id : null,
            'empresa_id' => session('id_empresa'),
            'ip' => $request->ip(),
        ];

        if ($estado >= 500) {
            $this->registrar('error', 'v3.peticion_fallida', $contexto);
        } elseif ($estado >= 400 || $duracion >= $limiteLento) {
            $this->registrar('warning', 'v3.peticion_atencion', $contexto);
        } else {
            $this->registrar('info', 'v3.peticion', $contexto);
        }

        return $response;
    }

    private function rutaSegura($request)
    {
        $route = $request->route();
        return is_object($route) && method_exists($route, 'uri') ? $route->uri() : $request->path();
    }

    private function registrar($nivel, $mensaje, array $contexto)
    {
        try {
            if ($nivel === 'error') {
                Log::error($mensaje, $contexto);
            } elseif ($nivel === 'warning') {
                Log::warning($mensaje, $contexto);
            } else {
                Log::info($mensaje, $contexto);
            }
        } catch (\Exception $e) {
            // El monitoreo nunca debe interrumpir una operacion del negocio.
        }
    }
}
