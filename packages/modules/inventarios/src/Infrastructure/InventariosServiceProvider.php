<?php

namespace Aitrol\Inventarios\Infrastructure;

use Aitrol\Inventarios\Application\RepositorioInventario;
use Aitrol\Inventarios\Infrastructure\Persistence\InventarioSql;
use Illuminate\Support\ServiceProvider;

final class InventariosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RepositorioInventario::class, InventarioSql::class);
    }

    public function boot(): void
    {
        $modulo = dirname(__DIR__, 2);
        $this->loadRoutesFrom($modulo.'/routes/web.php');
        $this->loadRoutesFrom($modulo.'/routes/api.php');
        $this->loadMigrationsFrom($modulo.'/database/migrations');
        $this->loadViewsFrom($modulo.'/resources/views', 'inventarios');
    }
}
