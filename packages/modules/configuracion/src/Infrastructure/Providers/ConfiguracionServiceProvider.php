<?php

namespace Aitrol\Configuracion\Infrastructure\Providers;

use Aitrol\Configuracion\Application\MenuService;
use Aitrol\Configuracion\Domain\MenuRepository;
use Aitrol\Configuracion\Infrastructure\Persistence\EloquentMenuRepository;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as Vista;

final class ConfiguracionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MenuRepository::class, EloquentMenuRepository::class);
    }

    public function boot(): void
    {
        $modulo = dirname(__DIR__, 3);

        $this->loadRoutesFrom($modulo.'/routes/web.php');
        $this->loadMigrationsFrom($modulo.'/database/migrations');
        $this->loadViewsFrom($modulo.'/resources/views', 'configuracion');

        Gate::define('administrar-menu', fn (User $usuario): bool => $usuario->is_admin === true);

        View::composer('partials.adminlte-sidebar', function (Vista $vista): void {
            $vista->with('navegacion', $this->app->make(MenuService::class)->navegacion(Gate::allows('administrar-menu')));
        });
    }
}
