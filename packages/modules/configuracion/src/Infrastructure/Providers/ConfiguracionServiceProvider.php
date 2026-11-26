<?php

namespace Aitrol\Configuracion\Infrastructure\Providers;

use Aitrol\Configuracion\Application\MenuService;
use Aitrol\Configuracion\Domain\MenuRepository;
use Aitrol\Configuracion\Infrastructure\Persistence\EloquentMenuRepository;
use App\Models\User;
use App\Services\Accesos;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as Vista;
use Laravel\Sanctum\PersonalAccessToken;

final class ConfiguracionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MenuRepository::class, EloquentMenuRepository::class);
        $this->app->scoped(Accesos::class);
    }

    public function boot(): void
    {
        $modulo = dirname(__DIR__, 3);

        $this->loadRoutesFrom($modulo.'/routes/web.php');
        $this->loadRoutesFrom($modulo.'/routes/api.php');
        $this->loadMigrationsFrom($modulo.'/database/migrations');
        $this->loadViewsFrom($modulo.'/resources/views', 'configuracion');

        $esAdministrador = fn (User $usuario): bool => $usuario->is_admin === true;
        Gate::define('administrar-menu', $esAdministrador);
        Gate::define('administrar-configuracion', $esAdministrador);
        Gate::define('permiso', fn (User $usuario, string $opcion, string $accion): bool => app(Accesos::class)->permite($usuario, $opcion, $accion));
        Gate::define('integrar-usuarios', fn (User $usuario): bool =>
            $esAdministrador($usuario) && $usuario->currentAccessToken() instanceof PersonalAccessToken);

        View::composer('partials.adminlte-sidebar', function (Vista $vista): void {
            $usuario = auth()->user();
            $accesos = app(Accesos::class);
            $vista->with('navegacion', $this->app->make(MenuService::class)->navegacion(
                Gate::allows('administrar-menu'),
                fn (string $ruta): bool => $usuario !== null && $accesos->muestra($usuario, $ruta),
            ));
        });
    }
}
