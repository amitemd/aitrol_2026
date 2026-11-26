<?php

use Aitrol\Clientes\Domain\ClienteNoEncontrado;
use Aitrol\Clientes\Domain\IdentificacionDuplicada;
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
        $middleware->alias(['permiso' => VerificarPermiso::class]);
        $middleware->redirectGuestsTo(fn (): string => route('dashboard'));
        $middleware->redirectUsersTo(fn (): string => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->map(ClienteNoEncontrado::class, fn (ClienteNoEncontrado $error): NotFoundHttpException => new NotFoundHttpException($error->getMessage(), $error));
        $exceptions->dontReport(IdentificacionDuplicada::class);
        $exceptions->render(function (IdentificacionDuplicada $error, Request $request): JsonResponse|RedirectResponse {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => $error->getMessage()], 409);
            }

            return back()->withInput()->withErrors(['identificacion' => $error->getMessage()]);
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
