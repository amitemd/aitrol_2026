<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', \Sis_medico\Http\Middleware\SetLang::class);

        $middleware->validateCsrfTokens(except: [
            'maquina/hemograma/prueba',
            'app/interconexion/sistemas/consultas',
        ]);

        $middleware->alias([
            'cors' => \Sis_medico\Http\Middleware\Cors::class,
            'general' => \Sis_medico\Http\Middleware\v3\General::class,
            'operacion.v3' => \Sis_medico\Http\Middleware\v3\OperacionMonitoreo::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
