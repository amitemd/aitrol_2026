<?php

namespace Aitrol\Configuracion\Infrastructure\Providers;

use Aitrol\Configuracion\Application\MenuService;
use Aitrol\Configuracion\Domain\MenuRepository;
use Aitrol\Configuracion\Infrastructure\Persistence\EloquentMenuRepository;
use App\Models\User;
use App\Services\Accesos;
use App\Services\ContextoOperativo;
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

        $esAdministrador = fn (User $usuario): bool => $usuario->is_admin === true && $usuario->id_empresa !== null;
        Gate::define('administrar-menu', fn (User $usuario): bool => $esAdministrador($usuario) && $usuario->is_admin_global === true);
        Gate::define('administrar-servidor', fn (User $usuario): bool => $esAdministrador($usuario) && $usuario->is_admin_global === true);
        Gate::define('administrar-configuracion', $esAdministrador);
        Gate::define('permiso', fn (User $usuario, string $opcion, string $accion): bool => app(Accesos::class)->permite($usuario, $opcion, $accion));
        Gate::define('integrar-usuarios', fn (User $usuario): bool =>
            $esAdministrador($usuario) && $usuario->currentAccessToken() instanceof PersonalAccessToken);

        View::composer('partials.adminlte-sidebar', function (Vista $vista): void {
            $usuario = auth()->user();
            $accesos = app(Accesos::class);
            $vista->with('navegacion', $this->app->make(MenuService::class)->navegacion(
                Gate::allows('administrar-configuracion'),
                fn (string $ruta): bool => $usuario !== null && $accesos->muestra($usuario, $ruta),
            ));
        });

        View::composer('partials.adminlte-header', function (Vista $vista): void {
            $usuario = auth()->user();
            if ($usuario?->id_empresa !== null && ($empresa = $usuario->empresa) !== null) {
                $contexto = app(ContextoOperativo::class);
                $sucursal = $contexto->resolverSucursalWeb();
                if ($sucursal === null) {
                    return;
                }
                $anioActual = $contexto->anioActual();
                $vista->with([
                    'empresaOperativa' => $empresa->nombre_comercial ?: $empresa->razon_social,
                    'sucursalOperativa' => $sucursal,
                    'sucursalesOperativas' => $contexto->opciones(),
                    'periodoOperativo' => $contexto->periodo(),
                    'aniosPeriodoOperativo' => range($anioActual, $anioActual - 10),
                ]);
            }
        });
    }
}
