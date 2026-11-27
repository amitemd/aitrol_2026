<?php

namespace Aitrol\Contabilidad\Infrastructure;

use Illuminate\Support\ServiceProvider;

final class ContabilidadServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $modulo = dirname(__DIR__, 2);
        $this->loadRoutesFrom($modulo.'/routes/contabilidad.php');
        $this->loadMigrationsFrom($modulo.'/database/migrations');
        $this->loadViewsFrom($modulo.'/resources/views', 'contabilidad');
    }
}
