<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Sis_medico\User;

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
        Gate::define(
            'manage-new-system-users',
            fn (User $user): bool => (int) $user->id_tipo_usuario === 1,
        );

        RateLimiter::for('new-system-password-reset', function (Request $request): Limit {
            $email = Str::lower($request->string('email')->trim()->toString());

            return Limit::perMinutes(5, 3)
                ->by(Str::transliterate($email.'|'.$request->ip()))
                ->response(fn (Request $request, array $headers): RedirectResponse => back()
                    ->withErrors(['email' => 'Demasiadas solicitudes. Inténtalo nuevamente en cinco minutos.'])
                    ->withHeaders($headers));
        });
    }
}
