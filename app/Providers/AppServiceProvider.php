<?php

namespace App\Providers;

use App\Http\Middleware\RegistrarAuditoria;
use App\Models\TokenAcceso;
use App\Services\TrazaAuditoria;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
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
        Event::listen(MessageLogged::class, RegistrarAuditoria::registrarMensaje(...));
        Event::listen(Logout::class, RegistrarAuditoria::registrarSalida(...));
        Event::listen(QueryExecuted::class, TrazaAuditoria::registrarConsulta(...));
        Sanctum::usePersonalAccessTokenModel(TokenAcceso::class);
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
}
