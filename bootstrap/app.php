<?php

use Aitrol\Clientes\Domain\ClienteNoEncontrado;
use Aitrol\Clientes\Domain\IdentificacionDuplicada;
use Aitrol\Proveedores\Domain\IdentificacionDuplicada as IdentificacionProveedorDuplicada;
use Aitrol\Proveedores\Domain\ProveedorNoEncontrado;
use App\Http\Middleware\AplicarIdioma;
use App\Http\Middleware\ExigirHttps;
use App\Http\Middleware\ProtegerRespuesta;
use App\Http\Middleware\RegistrarAuditoria;
use App\Http\Middleware\VerificarEmpresa;
use App\Http\Middleware\VerificarPermiso;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend([RegistrarAuditoria::class, ProtegerRespuesta::class]);
        $middleware->append(ExigirHttps::class);
        $middleware->web(append: [AplicarIdioma::class]);
        $middleware->trimStrings(except: ['clave_firma']);
        $middleware->trustHosts(fn (): array => array_map(
            fn (string $host): string => '^'.preg_quote($host, '/').'$',
            config('seguridad.hosts') ?: array_filter([parse_url(config('app.url'), PHP_URL_HOST)]),
        ), subdomains: false);
        $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_PORT);
        $middleware->alias(['permiso' => VerificarPermiso::class, 'empresa' => VerificarEmpresa::class]);
        $middleware->redirectGuestsTo(fn (): string => route('dashboard'));
        $middleware->redirectUsersTo(fn (): string => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['clave_firma']);
        $exceptions->map(ClienteNoEncontrado::class, fn (ClienteNoEncontrado $error): NotFoundHttpException => new NotFoundHttpException($error->getMessage(), $error));
        $exceptions->map(ProveedorNoEncontrado::class, fn (ProveedorNoEncontrado $error): NotFoundHttpException => new NotFoundHttpException($error->getMessage(), $error));
        $exceptions->dontReport([IdentificacionDuplicada::class, IdentificacionProveedorDuplicada::class]);
        $exceptions->render(function (IdentificacionDuplicada|IdentificacionProveedorDuplicada $error, Request $request): JsonResponse|RedirectResponse {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => $error->getMessage()], 409);
            }

            return back()->withInput()->withErrors(['identificacion' => $error->getMessage()]);
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
