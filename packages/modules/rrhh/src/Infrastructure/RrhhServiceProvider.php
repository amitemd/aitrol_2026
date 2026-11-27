<?php

namespace Aitrol\Rrhh\Infrastructure;

use Illuminate\Support\ServiceProvider;

final class RrhhServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $modulo = dirname(__DIR__, 2);
        $this->loadRoutesFrom($modulo.'/routes/web.php');
        $this->loadMigrationsFrom($modulo.'/database/migrations');
        $this->loadViewsFrom($modulo.'/resources/views', 'rrhh');
    }
}
