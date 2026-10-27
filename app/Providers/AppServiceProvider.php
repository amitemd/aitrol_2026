<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        $trustedProxies = trim((string) config('app.trusted_proxies'));

        if ($trustedProxies !== '') {
            TrustProxies::at(
                $trustedProxies === '*'
                    ? '*'
                    : array_values(array_filter(array_map('trim', explode(',', $trustedProxies)))),
            );
        }

        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        Gate::define(
            'manage-new-system-users',
            fn (User $user): bool => (int) $user->id_tipo_usuario === 1,
        );

        Gate::define(
            'view-system-observability',
            fn (User $user): bool => (int) $user->id_tipo_usuario === 1,
        );

        RateLimiter::for('new-system-password-reset', function (Request $request): array {
            $email = Str::lower($request->string('email')->trim()->toString());
            $accountHash = hash('sha256', $email);
            $ipHash = hash('sha256', (string) $request->ip());
            $response = fn (Request $request, array $headers): RedirectResponse => back()
                ->withErrors(['email' => 'Demasiadas solicitudes. Inténtalo nuevamente en cinco minutos.'])
                ->withHeaders($headers);

            return [
                Limit::perMinutes(
                    (int) config('auth.password_reset_security.pair_decay_minutes', 5),
                    (int) config('auth.password_reset_security.pair_max_attempts', 3),
                )->by("password-reset:pair:{$accountHash}:{$ipHash}")->response($response),
                Limit::perMinutes(
                    (int) config('auth.password_reset_security.ip_decay_minutes', 5),
                    (int) config('auth.password_reset_security.ip_max_attempts', 20),
                )->by("password-reset:ip:{$ipHash}")->response($response),
            ];
        });
    }
}
