<?php

namespace Aitrol\Clientes\Infrastructure\Providers;

use Aitrol\Clientes\Domain\Repositories\ClienteRepository;
use Aitrol\Clientes\Infrastructure\Persistence\EloquentClienteRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;

final class ClientesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClienteRepository::class, EloquentClienteRepository::class);
    }

    public function boot(): void
    {
        $modulePath = dirname(__DIR__, 3);

        $this->loadRoutesFrom($modulePath.'/routes/web.php');
        $this->loadRoutesFrom($modulePath.'/routes/api.php');
        $this->loadMigrationsFrom($modulePath.'/database/migrations');
        $this->loadViewsFrom($modulePath.'/resources/views', 'clientes');

        RateLimiter::for('clientes-api', function (Request $request): Limit {
            $token = $request->user()?->currentAccessToken();

            return Limit::perMinute(60)->by($token instanceof PersonalAccessToken
                ? 'token:'.$token->getKey()
                : 'ip:'.$request->ip());
        });
    }
}
