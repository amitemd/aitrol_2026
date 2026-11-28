<?php

namespace App\Providers;

use App\Http\Middleware\RegistrarAuditoria;
use App\Models\TokenAcceso;
use App\Models\User;
use App\Services\AuditoriaListado;
use App\Services\TrazaAuditoria;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        View::composer([
            'inventarios::index', 'contabilidad::index', 'activos-fijos::index', 'caja-bancos::index',
            'comercial::index', 'tributario::index', 'clientes::index', 'proveedores::index',
            'configuracion::roles', 'contabilidad::detalle',
        ], AuditoriaListado::class);
        TrustProxies::at(config('seguridad.proxies', []));
        $this->limitesDeAcceso();
        Auth::provider('usuarios-activos', fn ($app, $config) => (new EloquentUserProvider($app['hash'], $config['model']))
            ->withQuery(fn ($consulta) => $consulta->where('activo', true)));
        Event::listen(MessageLogged::class, RegistrarAuditoria::registrarMensaje(...));
        Event::listen(Logout::class, RegistrarAuditoria::registrarSalida(...));
        Event::listen(QueryExecuted::class, TrazaAuditoria::registrarConsulta(...));
        Sanctum::usePersonalAccessTokenModel(TokenAcceso::class);
        Sanctum::authenticateAccessTokensUsing(fn ($token, bool $valido): bool =>
            $valido && $token->tokenable instanceof User && $token->tokenable->activo);
        ResetPassword::toMailUsing(function (CanResetPassword $usuario, string $token): MailMessage {
            $ruta = route('password.reset', [
                'token' => $token,
                'email' => $usuario->getEmailForPasswordReset(),
            ], absolute: false);

            return (new MailMessage)
                ->subject('Recupera tu acceso a Aitrol')
                ->greeting('Recuperación de contraseña')
                ->line('Recibimos una solicitud para cambiar la contraseña de tu cuenta.')
                ->action('Cambiar contraseña', rtrim((string) config('app.url'), '/').$ruta)
                ->line('Si no solicitaste el cambio, puedes ignorar este mensaje.');
        });
    }

    private function limitesDeAcceso(): void
    {
        foreach ([
            'inicio-sesion' => [6, 6],
            'solicitar-clave' => [5, 4],
            'restablecer-clave' => [6, 6],
        ] as $nombre => [$porIp, $porCuenta]) {
            RateLimiter::for($nombre, function (Request $request) use ($porIp, $porCuenta): array {
                $limites = [Limit::perMinute($porIp)->by('ip:'.$request->ip())];
                $email = $request->input('email');
                if (is_string($email) && strlen($email) <= 255 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $limites[] = Limit::perMinute($porCuenta)->by('cuenta:'.hash('sha256', Str::lower($email)));
                }

                return $limites;
            });
        }
    }
}
