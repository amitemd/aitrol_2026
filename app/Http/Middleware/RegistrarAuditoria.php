<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\TrazaAuditoria;
use Closure;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class RegistrarAuditoria
{
    private const EVENTOS = 'auditoria.eventos';
    private const ACTOR = 'auditoria.actor';

    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set(self::EVENTOS, []);
        $request->attributes->set(TrazaAuditoria::class, new TrazaAuditoria);

        return $next($request);
    }

    public static function registrarMensaje(MessageLogged $mensaje): void
    {
        if (! app()->bound('request')) {
            return;
        }
        $request = request();
        $eventos = $request->attributes->get(self::EVENTOS);
        if (! is_array($eventos) || count($eventos) >= 20) {
            return;
        }

        $nivel = match ($mensaje->level) {
            'info', 'notice' => 'info',
            'warning' => 'warning',
            'error', 'critical', 'alert', 'emergency' => 'error',
            default => null,
        };
        if ($nivel === null) {
            return;
        }

        $error = $mensaje->context['exception'] ?? null;
        if ($error instanceof Throwable) {
            TrazaAuditoria::actual()?->registrarError($error);
        }
        $detalle = $error instanceof Throwable ? Str::limit(get_class($error), 180, '') : 'Mensaje del registro de aplicación';
        $eventos[] = ['nivel' => $nivel, 'origen' => 'aplicacion', 'evento' => $detalle];
        $request->attributes->set(self::EVENTOS, $eventos);
    }

    public static function registrarSalida(Logout $salida): void
    {
        if ($salida->user instanceof User && app()->bound('request')) {
            request()->attributes->set(self::ACTOR, $salida->user);
        }
    }

    public function terminate(Request $request, Response $response): void
    {
        $eventos = $request->attributes->get(self::EVENTOS, []);
        $request->attributes->remove(self::EVENTOS);
        $traza = $request->attributes->get(TrazaAuditoria::class);
        $request->attributes->remove(TrazaAuditoria::class);
        $rutaActual = $request->route();
        $ruta = $rutaActual?->getName();
        $pestana = $request->attributes->get('auditoria.pestana');
        $estado = $response->getStatusCode();
        if ($eventos === [] && ($ruta === null || $request->isMethod('OPTIONS') || $request->isMethod('HEAD'))) {
            return;
        }

        try {
            $usuario = $request->attributes->get(self::ACTOR) ?? $request->user();
        } catch (Throwable) {
            $usuario = null;
        }
        $esAcceso = $ruta === 'login.store' || $ruta === 'logout';
        if (($usuario instanceof User || $esAcceso) && $ruta !== null) {
            $validacionWeb = $response->isRedirection() && $request->hasSession() && $request->session()->has('errors');
            $nivel = match (true) {
                $estado >= 500 => 'error',
                $estado >= 400, $validacionWeb => 'warning',
                default => 'info',
            };
            $evento = match (true) {
                $nivel === 'error' => 'Error al procesar la solicitud',
                $nivel === 'warning' => 'Solicitud rechazada',
                $pestana !== null => 'Volvió a una pestaña abierta',
                $ruta === 'logout' => 'Cerró sesión',
                $ruta === 'login.store' => 'Inició sesión',
                $request->isMethod('GET') => $request->is('api/*') ? 'Consultó por API' : 'Abrió o consultó una pantalla',
                default => 'Procesó una operación',
            };
            $eventos[] = ['nivel' => $nivel, 'origen' => 'http', 'evento' => $evento];
        }
        if ($eventos === []) {
            return;
        }

        $datos = [
            'id_empresa' => $usuario instanceof User ? $usuario->id_empresa : null,
            'id_usuario' => $usuario instanceof User ? $usuario->id : null,
            'usuario_nombre' => $usuario instanceof User ? $usuario->name : null,
            'solicitud' => (string) Str::uuid(),
            'ruta' => $pestana?->getName() ?? $ruta,
            'modulo' => TrazaAuditoria::modulo($pestana?->getName() ?? $ruta ?? ''),
            'canal' => $request->is('api/*') ? 'API' : 'Web',
            'destino' => $rutaActual ? Str::limit('/'.ltrim(($pestana ?? $rutaActual)->uri(), '/'), 255, '') : null,
            'controlador' => $pestana === null && $rutaActual?->getControllerClass() ? $rutaActual->getActionName() : null,
            'metodo' => Str::limit($request->method(), 12, ''),
            'estado_http' => $estado,
            'created_at' => now(),
        ];
        if ($traza instanceof TrazaAuditoria && $pestana === null) {
            $datos += $traza->resumen();
        }

        try {
            DB::table('auditoria_eventos')->insert(array_map(
                fn (array $evento): array => $evento + $datos,
                $eventos,
            ));
        } catch (Throwable $error) {
            // El registro operativo conserva el fallo incluso si la base está fuera de servicio.
            error_log('Aitrol: no se pudo escribir la bitácora de auditoría ('.get_class($error).').');
        }
    }
}
