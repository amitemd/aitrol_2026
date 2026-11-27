<?php

namespace Aitrol\Tributario\Infrastructure;

use Aitrol\Tributario\Application\Puertos\RepositorioTributario;
use Aitrol\Tributario\Infrastructure\Persistence\TributarioSql;
use Illuminate\Support\ServiceProvider;

final class TributarioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RepositorioTributario::class, TributarioSql::class);
    }

    public function boot(): void
    {
        $modulo = dirname(__DIR__, 2);
        $this->loadRoutesFrom($modulo.'/routes/tributario.php');
        $this->loadMigrationsFrom($modulo.'/database/migrations');
        $this->loadViewsFrom($modulo.'/resources/views', 'tributario');
    }
}
