<?php

namespace Aitrol\Proveedores\Infrastructure\Providers;

use Aitrol\Proveedores\Domain\Repositories\ProveedorRepository;
use Aitrol\Proveedores\Infrastructure\Persistence\EloquentProveedorRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;

final class ProveedoresServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProveedorRepository::class, EloquentProveedorRepository::class);
    }

    public function boot(): void
    {
        $modulePath = dirname(__DIR__, 3);

        $this->loadRoutesFrom($modulePath.'/routes/web.php');
        $this->loadRoutesFrom($modulePath.'/routes/api.php');
        $this->loadMigrationsFrom($modulePath.'/database/migrations');
        $this->loadViewsFrom($modulePath.'/resources/views', 'proveedores');

        RateLimiter::for('proveedores-api', function (Request $request): Limit {
            $token = $request->user()?->currentAccessToken();

            return Limit::perMinute(60)->by($token instanceof PersonalAccessToken
                ? 'token:'.$token->getKey()
                : 'ip:'.$request->ip());
        });
    }
}
