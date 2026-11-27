<?php

namespace Aitrol\ActivosFijos\Infrastructure;

use Illuminate\Support\ServiceProvider;

final class ActivosFijosServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $modulo = dirname(__DIR__, 2);
        $this->loadRoutesFrom($modulo.'/routes/activos.php');
        $this->loadMigrationsFrom($modulo.'/database/migrations');
        $this->loadViewsFrom($modulo.'/resources/views', 'activos-fijos');
    }
}
