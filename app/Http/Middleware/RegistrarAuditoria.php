<?php

namespace App\Http\Middleware;

use App\Models\User;
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
        $ruta = $request->route()?->getName();
        $estado = $response->getStatusCode();
        $esEscritura = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
        if ($eventos === [] && ($ruta === null || (! $esEscritura && $estado < 400))) {
            return;
        }

        try {
            $usuario = $request->attributes->get(self::ACTOR) ?? $request->user();
        } catch (Throwable) {
            $usuario = null;
        }
        $esAcceso = $ruta === 'login.store' || $ruta === 'logout';
        if (($usuario instanceof User || $esAcceso) && $ruta !== null && ($esEscritura || $estado >= 400)) {
            $validacionWeb = $response->isRedirection() && $request->hasSession() && $request->session()->has('errors');
            $nivel = match (true) {
                $estado >= 500 => 'error',
                $estado >= 400, $validacionWeb => 'warning',
                default => 'info',
            };
            $evento = match ($nivel) {
                'error' => 'Error al procesar la solicitud',
                'warning' => 'Solicitud rechazada',
                'info' => 'Solicitud procesada',
            };
            $eventos[] = ['nivel' => $nivel, 'origen' => 'http', 'evento' => $evento];
        }
        if ($eventos === []) {
            return;
        }

        $datos = [
            'id_empresa' => $usuario instanceof User ? $usuario->id_empresa : null,
            'id_usuario' => $usuario instanceof User ? $usuario->id : null,
            'solicitud' => (string) Str::uuid(),
            'ruta' => $ruta,
            'metodo' => $request->method(),
            'estado_http' => $estado,
            'created_at' => now(),
        ];

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
