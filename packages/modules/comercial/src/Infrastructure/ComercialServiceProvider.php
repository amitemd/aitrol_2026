<?php

namespace Aitrol\Comercial\Infrastructure;

use Illuminate\Support\ServiceProvider;

final class ComercialServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $modulo = dirname(__DIR__, 2);
        $this->loadRoutesFrom($modulo.'/routes/comercial.php');
        $this->loadMigrationsFrom($modulo.'/database/migrations');
        $this->loadViewsFrom($modulo.'/resources/views', 'comercial');
    }
}
