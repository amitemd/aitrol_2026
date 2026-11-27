<?php

namespace Aitrol\Contabilidad\Infrastructure;

use Aitrol\Contabilidad\Application\Puertos\RepositorioContable;
use Aitrol\Contabilidad\Infrastructure\Persistence\DatosContables;
use App\Events\OperacionContabilizable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class ContabilidadServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RepositorioContable::class, DatosContables::class);
    }

    public function boot(): void
    {
        $modulo = dirname(__DIR__, 2);
        $this->loadRoutesFrom($modulo.'/routes/contabilidad.php');
        $this->loadMigrationsFrom($modulo.'/database/migrations');
        $this->loadViewsFrom($modulo.'/resources/views', 'contabilidad');
        Event::listen(OperacionContabilizable::class, ContabilizacionOperativa::class);
    }
}
