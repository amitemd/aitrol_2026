<?php

use App\Console\Commands\CreateAdministrator;
use App\Console\Commands\CustomCommand;
use App\Console\Commands\DeployApplication;
use App\Http\Middleware\AuditHttpRequest;
use App\Http\Middleware\EnforceHttps;
use App\Http\Middleware\SecureAuthenticationResponse;
use App\Http\Middleware\ShareNewSystemContext;
use App\Support\Errors\SupportIncidentRenderer;
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
use Sis_medico\Http\Middleware\BlockCompromisedLegacyEndpoint;
use Sis_medico\Http\Middleware\CheckSidebarPermission;
use Sis_medico\Http\Middleware\Cors;
use Sis_medico\Http\Middleware\LegacyEndpointFirewall;
use Sis_medico\Http\Middleware\RestringirIp;
use Sis_medico\Http\Middleware\SetLang;
use Sis_medico\Http\Middleware\v3\AuditV3Mutation;
use Sis_medico\Http\Middleware\v3\AuthenticateV3ApiClient;
use Sis_medico\Http\Middleware\v3\AuthorizeV3Action;
use Sis_medico\Http\Middleware\v3\CaptureV3AccountingIncidents;
use Sis_medico\Http\Middleware\v3\General;
use Sis_medico\Http\Middleware\v3\GuardV3RequestMethod;
use Sis_medico\Http\Middleware\v3\MinifyHtmlResponse;
use Sis_medico\Http\Middleware\v3\OperacionMonitoreo;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        CustomCommand::class,
        CreateAdministrator::class,
        DeployApplication::class,
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
        $middleware->trustHosts();

        $middleware->appendToGroup('web', SetLang::class);
        $middleware->appendToGroup('web', AuditHttpRequest::class);
        $middleware->redirectGuestsTo(
            fn (Request $request): string => str_starts_with((string) $request->route()?->getName(), 'new-system.')
                ? route('new-system.dashboard')
                : route('login'),
        );
        $middleware->redirectUsersTo(fn (): string => route('new-system.dashboard'));

        $middleware->validateCsrfTokens(except: [
            'maquina/hemograma/prueba',
            'app/interconexion/sistemas/consultas',
        ]);

        $middleware->alias([
            'cors' => Cors::class,
            'force.https' => EnforceHttps::class,
            'general' => General::class,
            'v3.api.client' => AuthenticateV3ApiClient::class,
            'v3.authorize' => AuthorizeV3Action::class,
            'v3.method.guard' => GuardV3RequestMethod::class,
            'v3.audit' => AuditV3Mutation::class,
            'v3.accounting.incidents' => CaptureV3AccountingIncidents::class,
            'v3.html.minify' => MinifyHtmlResponse::class,
            'sidebar.permission' => CheckSidebarPermission::class,
            'blocked.security' => BlockCompromisedLegacyEndpoint::class,
            'legacy.firewall' => LegacyEndpointFirewall::class,
            'ip.restrita' => RestringirIp::class,
            'new.context' => ShareNewSystemContext::class,
            'secure.auth.response' => SecureAuthenticationResponse::class,
            'operacion.v3' => OperacionMonitoreo::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReportDuplicates();

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->context(
            fn (Throwable $exception): array => app(SupportIncidentRenderer::class)->context($exception),
        );

        $exceptions->render(
            fn (Throwable $exception, Request $request) => app(SupportIncidentRenderer::class)->render($exception, $request),
        );
    })->create();
