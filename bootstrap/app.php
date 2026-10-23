<?php

use App\Console\Commands\CustomCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Sis_medico\Console\Commands\VerificarApiTributariaV3;
use Sis_medico\Console\Commands\VerificarDespliegueV3;
use Sis_medico\Console\Commands\VerificarSaludV3;
use Sis_medico\Http\Controllers\BdLabsController;
use Sis_medico\Http\Middleware\Cors;
use Sis_medico\Http\Middleware\SetLang;
use Sis_medico\Http\Middleware\v3\General;
use Sis_medico\Http\Middleware\v3\OperacionMonitoreo;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        CustomCommand::class,
        VerificarApiTributariaV3::class,
        VerificarDespliegueV3::class,
        VerificarSaludV3::class,
    ])
    ->withSchedule(function (Schedule $schedule): void {
        $electronicDocumentsUrl = trim((string) config('app.url_variable'));

        if ($electronicDocumentsUrl !== '' && $electronicDocumentsUrl !== 'nulo') {
            $schedule->call(function () use ($electronicDocumentsUrl): void {
                Http::timeout(30)
                    ->get(rtrim($electronicDocumentsUrl, '/').'/api_doc_electronico')
                    ->throw();
            })->everyMinute()->between('04:00', '23:50');
        }

        $schedule->call(function (): void {
            BdLabsController::calcula_porcentaje(date('Y'), date('m'));
        })->weekdays()->dailyAt('20:00');

        $schedule->command('custom:command')->weekdays()->dailyAt('13:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', SetLang::class);

        $middleware->validateCsrfTokens(except: [
            'maquina/hemograma/prueba',
            'app/interconexion/sistemas/consultas',
        ]);

        $middleware->alias([
            'cors' => Cors::class,
            'general' => General::class,
            'operacion.v3' => OperacionMonitoreo::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
