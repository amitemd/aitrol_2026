<?php

namespace Aitrol\CajaBancos\Infrastructure;

use Illuminate\Support\ServiceProvider;

final class CajaBancosServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $modulo = dirname(__DIR__, 2);
        $this->loadRoutesFrom($modulo.'/routes/caja.php');
        $this->loadMigrationsFrom($modulo.'/database/migrations');
        $this->loadViewsFrom($modulo.'/resources/views', 'caja-bancos');
    }
}
